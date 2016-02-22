=== ezPHP for WordPress ===

Stable tag: 160128
Requires at least: 3.3
Tested up to: 4.5-alpha
Text Domain: ezphp

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Contributors: WebSharks, JasWSInc
Donate link: http://www.websharks-inc.com/r/wp-theme-plugin-donation/
Tags: post, pages, posts, code, php, eval, exec, eval php, exec php, easy php, ez php, variables, conditionals

Evaluates PHP tags in Posts (of any kind, including Pages); and in text widgets. Very lightweight; plus it supports `[php][/php]` shortcodes!

== Description ==

ezPHP brings the power of `<?php ?>` tags into WordPress; or you can use `[php][/php]` shortcode tags (recommended for the WP Visual Editor; this is generally the best approach). PHP tags can be extremely useful when there is logic that needs to be worked out before certain portions of your content are displayed under certain scenarios. It's also helpful when/if there are portions of your content that need to be more dynamic. Developers might use this to pull external files into WordPress (via `include` or `require`) making their work easier.

---

= This Plugin is VERY Simple; only Two Configurable Options =

You can define these PHP constants inside your `/wp-config.php` file (optional).

	<?php
	define('EZPHP_INCLUDED_POST_TYPES', '');
	// Comma-delimited list of Post Types to include (excluding all others).

	define('EZPHP_EXCLUDED_POST_TYPES', '');
	// Comma-delimited list of Post Types to exclude (including all others).

---

For instance, if you want PHP tags evaluated only in Pages; e.g. in the `page` type.

	<?php
	define('EZPHP_INCLUDED_POST_TYPES', 'page');
	// Unless included here; all other Post Types will be excluded now.

---

Or, if you don't want PHP tags evaluated in Posts; e.g. in the `post` type.

	<?php
	define('EZPHP_EXCLUDED_POST_TYPES', 'post');
	// Unless excluded here; all other Post Types will be included now.

---

= Writing PHP Code into a Post/Page or Text Widget =

You can use regular `<?php ?>` tags; OR you can use `[php][/php]` shortcode tags.

= Quick Tip: Writing PHP Code Samples? =

You can use `<!php !>` when writing code samples, to avoid having certain PHP tags evaulated. When you write `<!php !>`, it is translated into `<?php ?>` in the final output; but never executed. Of course, it's ALSO possible to accomplish this with HTML entities; e.g. `&lt;?php ?&gt;`.

== Installation ==

= ezPHP is Very Easy to Install =

1. Upload the `/ezphp` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress®.
3. Use PHP tags in your Posts/Pages/Widgets.

== License ==

Copyright: © 2013 [WebSharks, Inc.](http://www.websharks-inc.com/bizdev/) (coded in the USA)

Released under the terms of the [GNU General Public License](http://www.gnu.org/licenses/gpl-2.0.html).

= Credits / Additional Acknowledgments =

* Software designed for WordPress®.
	- GPL License <http://codex.wordpress.org/GPL>
	- WordPress® <http://wordpress.org>
* Some JavaScript extensions require jQuery.
	- GPL-Compatible License <http://jquery.org/license>
	- jQuery <http://jquery.com/>
* CSS framework and some JavaScript functionality provided by Bootstrap.
	- GPL-Compatible License <http://getbootstrap.com/getting-started/#license-faqs>
	- Bootstrap <http://getbootstrap.com/>
* Icons provided by Font Awesome.
	- GPL-Compatible License <http://fortawesome.github.io/Font-Awesome/license/>
	- Font Awesome <http://fortawesome.github.io/Font-Awesome/>

== Changelog ==

= v160128 =

- Improving support for PHP mixed with Markdown for themes that use Markdown.

= v150214 =

* General maintenance.
* Tested against WordPress v4.1 and v4.2-alpha.

= v131121 =

* General code cleanup and optimizations.
* Adding a new config constant: `EZPHP_INCLUDED_POST_TYPES`. See readme file for details.

= v130924 =

* Adding support for `[php][/php]` shortcode tags as an alternative to regular `<?php ?>` tags.
* Improvements and optimizations that make ezPHP an even more lightweight plugin for PHP evaluation in WordPress®.

= v130922 =

* It is now possible to use `<!php !>` when writing code samples, to avoid having certain PHP tags evaulated. When you write `<!php !>`, it is translated into `<?php ?>` in the final output; but never actually evaluated by the internal PHP parser. Of course, it's ALSO possible to accomplish this with HTML entities; e.g. `&lt;?php ?&gt;`.

= v130123 =

* Initial release.
