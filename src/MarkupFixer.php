<?php

/**
 * Laravel Table Of Content Generator
 * @version 0.1
 * @package: fxcjahid/laravel-table-of-content
 * @author: fxcjahid <fxcjahid3@gmail.com>
 */

namespace Fxcjahid\LaravelTableOfContent;


use Masterminds\HTML5;
use RuntimeException;

/**
 * 
 * TOC Markup Fixer will add `id` attributes to all H1...H6 tags where they haven't attributes
 *  
 */

class MarkupFixer
{
	use HtmlHelper;

	/**
	 * @var HTML5
	 */
	private $htmlParser;

	/**
	 * Constructor
	 *
	 * @param HTML5 $htmlParser
	 */
	public function __construct(HTML5 $htmlParser = null)
	{
		$this->htmlParser = $htmlParser ?: new HTML5();
	}

	/**
	 * Fix markup
	 *
	 * @param string $markup
	 * @param int    $topLevel
	 * @param int    $depth
	 * @return string Markup with added IDs
	 * @throws RuntimeException
	 */
	public function fix($markup, $topLevel = 1, $depth = 6)
	{
		if (!$this->isFullHtmlDocument($markup)) {
			$partialID = uniqid('toc_generator_');
			$markup = sprintf("<body id='%s'>%s</body>", $partialID, $markup);
		}

		$domDocument = $this->htmlParser->loadHTML($markup);
		$domDocument->preserveWhiteSpace = true; // do not clobber whitespace

		$sluggifier = new UniqueSluggifier();

		/** @var \DOMElement $node */
		foreach ($this->traverseHeaderTags($domDocument, $topLevel, $depth) as $node) {
			if ($node->getAttribute('id')) {
				continue;
			}

			$node->setAttribute('id', $sluggifier->slugify($node->getAttribute('title') ?: $node->textContent));
		}

		return $this->htmlParser->saveHTML(
			(isset($partialID)) ? $domDocument->getElementById($partialID)->childNodes : $domDocument
		);
	}
}
