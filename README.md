Laravel Table of Content Generator
==================================

Generates a Table of Contents from ***H1...H6***  Tags in HTML Content

This package provides a simple library to build a Table-of-Contents from HTML markup.  It does so by evaluating your *H1...H6* tags.
It can also automatically add appropriate *id* anchor attributes to header tags so that in-page links work.

Features:

* Generates HTML menus and [KnpMenu Item](https://github.com/KnpLabs/KnpMenu) Menus
* Add anchor ID attributes to *H1*...*H6*  tags in your content where they haven't attributes
* You can specify tag which *H1*...*H6*  heading tag levels to include in the TOC
* Uses the flexible [KnpMenu Library](https://github.com/KnpLabs/KnpMenu) to generate menus
* [PSR-12](https://www.php-fig.org/psr/psr-12/) compliant
* Composer-compatible
* Unit-tested (95% coverage)

Installation Options
--------------------

Install with composer :

`composer require fxcjahid/laravel-table-of-content`

Usage
-----

This package contains two main classes:

1. ` fxcjahid\LaravelTableOfContent\MarkupFixer`: Adds `id` anchor attributes to any *H1*...*H6* tags that do not already have any (you can specify which header tag levels to use at runtime)
2. `fxcjahid\LaravelTableOfContent\Table ` : Generates a Table of Contents from HTML markup

Basic Example:

```php
use Fxcjahid\LaravelTableOfContent\Table;
use Fxcjahid\LaravelTableOfContent\MarkupFixer;

$content = <<<END
	<h2>This is heading H2</h2>
	<h3>This is heading H3</h3>
	<h4>This is heading H4</h4>
	<h2>This is heading H1</h2>
END;


/**
  * Get Markup tag fixies
  */

$getFixContent = $markup->fix($content);

/**
  * Get Table Of content 
  * Levels 1-6 (It's will skip heading tag)
  */

$getTableOfContent = $toc->getTableContent($getFixContent, 2);


$htmlOut  = "<div class='content'>" . $getFixContent . "</div>";
$htmlOut .= "<div class='toc'>" . $getTableOfContent . "</div>";

return $htmlOut;


```

This produces the following output:

```html
<div class='content'>
    <h1 id="this-is-a-header-tag-with-no-anchor-id">This is a header tag with no anchor id</h1>
    <p>Lorum ipsum doler sit amet</p>
    <h2 id="foo">This is a header tag with an anchor id</h2>
    <p>Stuff here</p>
    <h3 id="bar">This is a header tag with an anchor id</h3>
</div>
<div class='toc'>
    <ul>
        <li class="first last">
        <span></span>
            <ul class="menu_level_1">
                <li class="first last">
                    <a href="#foo">This is a header tag with an anchor id</a>
                    <ul class="menu_level_2">
                        <li class="first last">
                            <a href="#bar">This is a header tag with an anchor id</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>
</div>
```

Integration in controller
-------------------------

```php
use Fxcjahid\LaravelTableOfContent\Table;
use Fxcjahid\LaravelTableOfContent\MarkupFixer;

/**
 * Show the articles page.
 */
 public function show(Table $toc, MarkupFixer $markup){
	$content = <<<END
		<h2>This is heading H2</h2>
		<h3>This is heading H3</h3>
		<h4>This is heading H4</h4>
		<h2>This is heading H1</h2>
	END;

	/**
	 * Get Markup tag fixies 
	 */
	$getFixContent = $markup->fix($content);

	/**
	 * Get Table Of content 
	 * Note: without ID arttibutes Table can't generate
	 */
	$getTableOfContent = $toc->getTableContent($getFixContent);

       $htmlOut  = "<div class='content'>" . $getFixContent . "</div>"; 
       $htmlOut .= "<div class='toc'>" . $getTableOfContent . "</div>";

       echo $htmlOut;

 } 
```

* This package inspired by https://github.com/caseyamcl/toc
