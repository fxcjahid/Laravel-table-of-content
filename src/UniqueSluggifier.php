<?php

/**
 * Laravel Table Of Content Generator
 * @version 0.1
 * @package: fxcjahid/laravel-table-of-content
 * @author: fxcjahid <fxcjahid3@gmail.com>
 */

namespace fxcjahid\LaravelTableOfContent;


use Cocur\Slugify\Slugify;

/**
 * 
 * UniqueSluggifier creates slugs from text without repeating the same slug twice per instance
 * 
 */
class UniqueSluggifier
{
	/**
	 * @var Slugify
	 */
	private $slugify;

	/**
	 * @var array
	 */
	private $used;

	/**
	 * Constructor
	 *
	 * @param Slugify $slugify
	 */
	public function __construct(Slugify $slugify = null)
	{
		$this->used = array();
		$this->slugify = $slugify ?: new Slugify();
	}

	/**
	 * Slugify
	 *
	 * @param string $text
	 * @return string
	 */
	public function slugify($text)
	{
		$slugged = $this->slugify->slugify($text);

		$count = 1;
		$orig = $slugged;
		while (in_array($slugged, $this->used)) {
			$slugged = $orig . '-' . $count;
			$count++;
		}

		$this->used[] = $slugged;
		return $slugged;
	}
}
