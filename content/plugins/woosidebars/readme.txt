=== WooSidebars ===
Contributors: woothemes, mattyza
Tags: widgets, sidebars, widget-areas
Requires at least: 4.1
Tested up to: 4.3
Stable tag: 1.4.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WooSidebars adds functionality to display different widgets in a sidebar, according to a context (for example, a specific page or a category).

== Description ==

With WooSidebars, it's possible to change the widgets that display in a sidebar (widgetized area) according to a context (for example, a specific page, a specific blog post, certain categories or the search results screen). Setting up a custom widget area to display across multiple conditions is as easy as a few clicks.

[vimeo http://vimeo.com/42980663]

Looking for a helping hand? [View plugin documentation](http://docs.woothemes.com/documentation/plugins/woosidebars/).

Looking to contribute code to this plugin? [Fork the repository over at GitHub](http://github.com/woothemes/woosidebars/). Please also read the CONTRIBUTING.md file, bundled within this plugin.

== Installation ==

= Minimum Requirements =

* WordPress 3.3 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t even need to leave your web browser. To do an automatic install of WooSidebars, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type "WooSidebars" and click Search Plugins. Once you’ve found our widget areas plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking Install Now. After clicking that link you will be asked if you’re sure you want to install the plugin. Click yes and WordPress will automatically complete the installation.

= Manual installation =

The manual installation method involves downloading WooSidebars and uploading it to your webserver via your favourite FTP application.

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

= Where to go after installation =

Once WooSidebars has been installed and activated, please visit the "Appearance -> Widget Areas" screen to begin adding custom widget areas.

= Upgrading =

Automatic updates should work a charm; as always though, ensure you backup your site just in case.

== Frequently Asked Questions ==

= Where can I find WooSidebars documentation and user guides? =

For further documentation on using WooSidebars, please view the [WooSidebars Documentation](http://docs.woothemes.com/documentation/plugins/woosidebars/).

= Will WooSidebars work with my theme? =

Yes; WooSidebars will work with any theme that supports dynamic widget areas.

= How can I contribute to WooSidebars development? =

Looking to contribute code to this plugin? [Fork the repository over at GitHub](http://github.com/woothemes/woosidebars/).
(submit pull requests to the "develop" branch)

== Screenshots ==

1. The list of custom widget areas.
2. Adding a custom widget area.

== Upgrade Notice ==

= 1.4.3 =
Bug fix and maintenance release.

= 1.4.2 =
Security Fix for XSS vulnerability

= 1.4.1 =
Fixes an error notice on the homepage, caused by the tag check logic.

= 1.4.0 =
WordPress 3.8 compatibility.
Adds "posts tagged with" condition.

= 1.3.1 =
Bug fix to ensure multiple conditions save correctly.

= 1.3.0 =
Optimisation update.

= 1.2.2 =
"Widget Areas" menu is now only visible to users who can add/modify widgets.

= 1.2.1 =
Updated for WordPress 3.5+ compatibility. Adjusted "Advanced" tab logic. Fixed bug where "Template Hierarchy -> Pages" condition wasn't being applied correctly. Dequeue WordPress SEO admin stylesheets from the "Widget Areas" "Add" and "Edit" screens.

= 1.2.0 =
Moved to WordPress.org. Woo! Added scope to methods and properties where missing.

== Changelog ==

= 1.4.3 =
* 2015-09-22
* Ensures condition headings are present before attempting to output in the conditions meta box.

= 1.4.2 =
* 2015-04-22
* Security Fix for remove_query_arg vulnerability

= 1.4.1 =
* 2015-02-17
* Fixes an error notice on the homepage, caused by the tag check logic.

= 1.4.0 =
* 2015-02-17
* WordPress 3.8 compatibility.
* Adds "posts tagged with" condition.


= 1.3.1 =
* 2013-08-13
* Bug fix to ensure multiple conditions save correctly.

= 1.3.0 =
* 2013-08-12
* Introduces woosidebars_upper_limit filter, used on all database queries, to control scaling.
* Fixes several PHP notices for "undefined index".
* Fixes "single" condition, where a small section of logic was missing for determining if the condition applied to the current screen being loaded.
* Optimises admin-side JavaScript and CSS.

= 1.2.2 =
* 2013-03-08
* Changes capability for displaying the menu to "edit_theme_options" in line with the "Widgets" menu capability.

= 1.2.1 =
* 2013-01-09
* Updated admin JavaScript for WordPress 3.5+ compatibility. Moved "Advanced" tab outside of the tabs list.
* Fixed bug with the "Template Hierarchy -> Pages" condition that wasn't applying.
* When WordPress SEO is active, dequeue unused stylesheets on the "Widget Areas" "add" and "edit" screens.

= 1.2.0 =
* Renamed files according to standards naming convention.
* Added scope to methods and properties where missing.

= 1.1.2 =
* Routine hardening and maintenance update. Fixed notice message in WooCommerce integration.

= 1.1.1 =
* Fix notice displayed on WooCommerce product pages from the WooSidebars integration.

= 1.1.0 =
* Initial WooCommerce integration.

= 1.0.0 =
* First release. Woo!