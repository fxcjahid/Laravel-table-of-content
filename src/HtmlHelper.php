<?php

/**
 * Laravel Table Of Content Generator
 * @version 0.1
 * @package: fxcjahid/laravel-table-of-content
 * @author: fxcjahid <fxcjahid3@gmail.com>
 */


namespace fxcjahid\LaravelTableOfContent;

use ArrayIterator;
use DOMDocument;
use DomElement;
use DOMXPath;

/**
 * Trait that helps with HTML-related operations
 *
 * @package TOC
 */
trait HtmlHelper
{
	/**
	 * Convert a topLevel and depth to H1..H6 tags array
	 *
	 * @param int $topLevel
	 * @param int $depth
	 * @return array|string[]  Array of header tags; ex: ['h1', 'h2', 'h3']
	 */
	protected function determineHeaderTags($topLevel, $depth)
	{
		$desired = range((int) $topLevel, (int) $topLevel + ((int) $depth - 1));
		$allowed = [1, 2, 3, 4, 5, 6];

		return array_map(function ($val) {
			return 'h' . $val;
		}, array_intersect($desired, $allowed));
	}



	/**
	 * Traverse Header Tags in DOM Document
	 *
	 * @param DOMDocument $domDocument
	 * @param int          $topLevel
	 * @param int          $depth
	 * @return ArrayIterator|DomElement[]
	 */
	protected function traverseHeaderTags(DOMDocument $domDocument, $topLevel, $depth)
	{
		$xpath = new DOMXPath($domDocument);

		$xpathQuery = sprintf(
			"//*[%s]",
			implode(' or ', array_map(function ($v) {
				return sprintf('local-name() = "%s"', $v);
			}, $this->determineHeaderTags($topLevel, $depth)))
		);

		$nodes = [];
		foreach ($xpath->query($xpathQuery) as $node) {
			$nodes[] = $node;
		}

		return new ArrayIterator($nodes);
	}

	/**
	 * Is this a full HTML document
	 *
	 * Guesses, based on presence of <body>...</body> tags
	 *
	 * @param string $markup
	 * @return bool
	 */
	protected function isFullHtmlDocument($markup)
	{
		return (strpos($markup, "<body") !== false && strpos($markup, "</body>") !== false);
	}
}