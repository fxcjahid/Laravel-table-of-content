<?php

/**
 * Laravel Table Of Content Generator
 * @version 0.1
 * @package: fxcjahid/laravel-table-of-content
 * @author: fxcjahid <fxcjahid3@gmail.com>
 */

namespace fxcjahid\LaravelTableOfContent;


use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\RendererInterface;
use Masterminds\HTML5;
use fxcjahid\LaravelTableOfContent\HtmlHelper;


/**
 * Table Of Contents Generator generates TOCs(table) from HTML Markup
 * 
 */
class Table
{
	use HtmlHelper;

	/**
	 * @var HTML5
	 */
	private $domParser;

	/**
	 * @var MenuFactory
	 */
	private $menuFactory;

	/**
	 * Constructor
	 *
	 * @param MenuFactory $menuFactory
	 * @param HTML5       $htmlParser
	 */
	public function __construct(MenuFactory $menuFactory = null, HTML5 $htmlParser = null)
	{
		$this->domParser   = $htmlParser  ?: new HTML5();
		$this->menuFactory = $menuFactory ?: new MenuFactory();
	}

	/**
	 * Get Menu
	 *
	 * Returns a KNP Menu object, which can be traversed or rendered
	 *
	 * @param string  $markup    Content to get items from $this->getItems($markup, $topLevel, $depth)
	 * @param int     $topLevel  Top Header (1 through 6)
	 * @param int     $depth     Depth (1 through 6)
	 * @return ItemInterface     KNP Menu
	 */
	public function getMenu($markup, $topLevel = 1, $depth = 6)
	{
		// Setup an empty menu object
		$menu = $this->menuFactory->createItem('TOC');

		// Empty?  Return empty menu item
		if (trim($markup) == '') {
			return $menu;
		}

		// Parse HTML
		$tagsToMatch = $this->determineHeaderTags($topLevel, $depth);

		// Initial settings
		$lastElem = $menu;

		// Do it...
		$domDocument = $this->domParser->loadHTML($markup);
		foreach ($this->traverseHeaderTags($domDocument, $topLevel, $depth) as $node) {
			// Skip items without IDs
			if (!$node->hasAttribute('id')) {
				continue;
			}

			// Get the TagName and the level
			$tagName = $node->tagName;
			$level   = array_search(strtolower($tagName), $tagsToMatch) + 1;

			// Determine parent item which to add child
			/** @var MenuItem $parent */
			if ($level == 1) {
				$parent = $menu;
			} elseif ($level == $lastElem->getLevel()) {
				$parent = $lastElem->getParent();
			} elseif ($level > $lastElem->getLevel()) {
				$parent = $lastElem;
				for ($i = $lastElem->getLevel(); $i < ($level - 1); $i++) {
					$parent = $parent->addChild('');
				}
			} else { //if ($level < $lastElem->getLevel())
				$parent = $lastElem->getParent();
				while ($parent->getLevel() > $level - 1) {
					$parent = $parent->getParent();
				}
			}

			$lastElem = $parent->addChild(
				$node->getAttribute('id'),
				[
					'label' => $node->getAttribute('title') ?: $node->textContent,
					'uri' => '#' . $node->getAttribute('id')
				]
			);
		}

		return $menu;
	}

	/**
	 * Get HTML Links in list form
	 *
	 * @param string            $markup   Content to get items from
	 * @param int               $topLevel Top Header (1 through 6)
	 * @param int               $depth    Depth (1 through 6)
	 * @param RendererInterface $renderer
	 * @return string HTML <LI> items
	 */
	public function getTableContent($markup, $topLevel = 1, $depth = 6, RendererInterface $renderer = null)
	{
		if (!$renderer) {
			$renderer = new ListRenderer(new Matcher(), [
				'currentClass'  => 'active',
				'ancestorClass' => 'active_ancestor'
			]);
		}

		return $renderer->render($this->getMenu($markup, $topLevel, $depth));
	}
}
