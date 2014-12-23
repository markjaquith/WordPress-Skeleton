=== Theme Check ===
Contributors: Otto42, pross
Author URI: http://ottopress.com/
Plugin URL: http://ottopress.com/wordpress-plugins/theme-check/
Requires at Least: 3.7
Tested Up To: 4.1
Tags: template, theme, check, checker, tool, wordpress, wordpress.org, upload, uploader, test, guideline, review
Stable tag: 20141222.1

A simple and easy way to test your theme for all the latest WordPress standards and practices. A great theme development tool!

== Description ==

The theme check plugin is an easy way to test your theme and make sure it's up to spec with the latest [theme review](https://codex.wordpress.org/Theme_Review) standards. With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.

The tests are run through a simple admin menu and all results are displayed at once. This is very handy for theme developers, or anybody looking to make sure that their theme supports the latest WordPress theme standards and practices.

== Frequently Asked Questions ==

= What's with the version numbers? =

The version number is the date of the revision of the [guidelines](https://codex.wordpress.org/Theme_Review) used to create it.

= Why does it flag something as bad? =

It's not flagging "bad" things, as such. The theme check is designed to be a non-perfect way to test for compliance with the [Theme Review](https://codex.wordpress.org/Theme_Review) guidelines. Not all themes must adhere to these guidelines. The purpose of the checking tool is to ensure that themes uploaded to the central [WordPress.org theme repository](http://wordpress.org/extend/themes/) meet the latest standards of WordPress themes and will work on a wide variety of sites.

Many sites use customized themes, and that's perfectly okay. But themes that are intended for use on many different kinds of sites by the public need to have a certain minimum level of capabilities, in order to ensure proper functioning in many different environments. The Theme Review guidelines are created with that goal in mind.

This theme checker is not perfect, and never will be. It is only a tool to help theme authors, or anybody else who wants to make their theme more capable. All themes submitted to WordPress.org are hand-reviewed by a team of experts. The automated theme checker is meant to be a useful tool only, not an absolute system of measurement.

This plugin does not decide the guidelines used. Any issues with particular theme review guidelines should be discussed on the [Make Themes site](https://make.wordpress.org/themes).

== Other Notes ==

= How to enable trac formatting =

The Theme Review team use this plugin while reviewing themes and copy/paste the output into trac tickets, the trac system has its own markup language.
To enable trac formatting in Theme-Check you need to define a couple of variables in wp-config.php:
*TC_PRE* and *TC_POST* are used as a ticket header and footer.
Examples:
`define( 'TC_PRE', 'Theme Review:[[br]]
- Themes should be reviewed using "define(\'WP_DEBUG\', true);" in wp-config.php[[br]]
- Themes should be reviewed using the test data from the Theme Checklists (TC)
-----
' );`

`define( 'TC_POST', 'Feel free to make use of the contact details below if you have any questions,
comments, or feedback:[[br]]
[[br]]
* Leave a comment on this ticket[[br]]
* Send an email to the Theme Review email list[[br]]
* Use the #wordpress-themes IRC channel on Freenode.' );`
If **either** of these two vars are defined a new trac tickbox will appear next to the *Check it!* button.

== Changelog ==
= 20140929.1 =
* Added new checks and updates from Frank Klein at Automattic. Thanks Frank!
* Updated deprecated function listings
* Customizer check: All add_settings must use sanitization callbacks, for security
* Plugin territory checks: Themes must not register post types or taxonomies or add shortcodes for post content
* Widgets: Calls to register_sidebar must be called from the widgets_init action hook
* Title: <title> tags must exist and not have anything in them other than a call to wp_title()
* CDN: Checks for use of common CDNs (recommended only)
* Note: Changed plugin and author URIs due to old URIs being invalid. These may change again in the future, the URIs to my own site are temporarily only.

= 20131213.1 =
* Corrected errors not being displayed by the plugin and it incorrectly giving a "pass" result to everything.

= 20131212.1 =
* Updated for 3.8
* Most files have changed for better I18N support, so the language files were removed temporarily until translation can be redone.

= 20121211.1 =
* Updated for 3.5
* Remove Paypal button.

= 20110805.1 =
* TimThumb checks removed.
* Proper i18n loading. Fixes http://bit.ly/ouD5Ke.
* Screenshot now previewed in results, with filesize and dimensions.

= 20110602.2 =
* New file list functions hidden folders now detectable.
* Better fopen checks.
* TimThumb version bump

= 20110602.1 =
* DOS/UNIX line ending style checks are now a requirement for proper theme uploading.
* Timthumb version bump
* Several fixes reported by GaryJ
* 3.2 deprecated functions added

= 20110412.1 =
* Fix regex's
* Added check for latest footer injection hack.
* Fix tags check to use new content function correctly
* Sync of all changes made for wporg uploader theme-check.
* Updated checks post 3.1. added screenshot check to svn.
* Fix links check to not return a false failure in some cases
* rm one of the checks that causes problems on wporg uploader (and which is also unnecessary)
* Move unneeded functions out of checkbase into main.php.
* Minor formatting changes only (spacing and such)
* Add check for wp_link_pages() + fix eval() check

= 20110219.2 =
* Merged new UI props Gua Bob [1](http://guabob.com/) 
* Last tested theme is always pre-selected in the themes list.
* Fixed php error in admin_menu.php

= 20110219.1 = 
* See [commit log](https://github.com/Pross/theme-check/commits/) for changes.

= 20110201.2 =
* UI bug fixes [forum post](http://bit.ly/ff7amN) props Mamaduka.
* Textdomain checks for twentyten and no domain.
* Fix div not closing props Mamaduka.

= 20110201.1 =
* i18n working
* sr_RS de_DE ro_RO langs props Daniel Tara and Emil Uzelac.
* Child theme support added, checks made against parent AND child at runtime.
* Trac formatting button added for reviewers.

= 20101228.3 =
* Last revision for 3.1 (hopefully)
* Chips suggestion of checking for inclusion of searchform.php ( not
perfect yet, need more examples to look for ).
* add_theme_page is required, all others flagged and displayed with line
numbers.
* <?= detected properly, short tags outputted with line umbers.
* Mostly internationalized, needs translations now.
* Bug fixes.

= 20101228.2 =
* Added menu checking.
* ThemeURI AuthourURI added to results.
* Lots of small fixes.
* Started translation.

= 20101228.1 =
* Fix embed_defaults filter check and stylesheet file data check.

= 20101226.1 =
* Whole system redesign to allow easier synching with WordPress.org uploader. Many other additions/subtractions/changes as well.
* WordPress 3.1 guidelines added, to help theme authors ensure compatibility for upcoming release.

= 20101110.7 =
* Re-added malware.php checks for fopen and file_get_contents (INFO)
* fixed a couple of undefined index errors.

= 20101110.4_r2 =
* Fixed Warning: Wrong parameter count for stristr()

= 20101110.4_r1 =
* Added `echo` to suggested.php

= 20101110.4 =
* Fixed deprecated function call to get_plugins()

= 20101110.3 =
* Fixed undefined index.

= 20101110.2 =
* Missing `<` in main.php
* Added conditional checks for licence.txt OR Licence tags in style.css
* UI improvements.

= 20101110.1 =
* Date fix!

= 10112010_r1 =
* Fixed hardcoded links check. Added FAQ

= 10112010 =
* First release.
