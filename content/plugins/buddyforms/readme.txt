=== BuddyForms  ===

Contributors: svenl77
Tags: collaborative, publishing, buddypress, groups, custom post types, taxonomy, frontend, posting, editing, forms, form builder
Requires at least: WordPress 3.9, BuddyPress 2.x
Tested up to: WordPress 4.4
Stable tag: 1.5.1
Author: Sven Lehnert
Author URI: https://profiles.wordpress.org/svenl77
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress Front End Editor And Form Builder For Your User Generated Content

== Description ==

BuddyForms enables Your users to share their own content in a way that You choose to.
Create versatile and creative content forms that Your users can use to share content with, and become a part of the content sharing community

== Frequently Asked Questions ==

You can find all help buttons in your BuddyForms Settings Panel in your WP Dashboard!

<b>Search the Documentation</b>
http://docs.buddyforms.com

<b>Create a new Support Ticket</b>
Create new Support Tickets or check your existing once in your BuddyForms Account.
https://buddyforms.com/checkout/support-tickets/

or write us a email: support@buddyforms.com

== Upgrade Notice ==

If you updated from version 1.3 please test your Form Elements "Featured Image" and "File". They have changed rapidly.
If you encounter any issues just delete the form elements and add them to the form again. This should fix all issues.

== Changelog ==

1.5.1
<ul>
<li>Fixed spelling and help text huge thanks to holden for the review and making all more easy to understand!<li>
<li>add new action buddyforms_settings_page_tab to allow extension to add new settings tabs to the buddyforms settings page.<li>
<li>add new filter bf_admin_tabs to register the settings tab and label<li>
<li>fixed a conflict with userpro using the same function name sortArrayByArray changed to buddyforms_sort_array_by_array<li>
<ul>

1.5
<ul>
<li>Add new Filter buddyforms_the_loop_edit_permalink to change the loop edit permalink</li></li>
<li>Add new filter buddyforms_after_save_post_redirect to change the redirect url</li>
<li>Add a new do action buddyforms_process_post_start and buddyforms_process_post_end to add Multi Site Support.</li>
<li>Add a new column shortcake to the form list</li>
<li>Fixed a css issue. The css for the loops was not loaded if used in shortcode</li>
<li>Add new shortcake bf</li>
<li>change the form action logic</li>
<li>add class button btn btn-primary to the featured image form element</li>
<li>add new hook buddyforms_post_edit_meta_box_select_form </li>
<li>Add error message text to the settings.</li>
<li>fixed a small issue in the settings if no form is created</li>
<li>restructure the-loop.php</li>
<li>remove the form if ajax redirect</li>
<li>adjust the error handling and make it a global</li>
<li>add editpost_title = none as default to make save a post without a title possible</li>
<li>remove the field type as class to avoid conflicts with other plugins and themes</li>
<li>regenerate the global on transition_post_status</li>
<li>simplifier the js</li>
<li>rename function edd_sl_sample_plugin_updater to buddyforms_edd_plugin_updater</li>
<li>Handle field types now like slugs and sanitize_title before save option</li>
<li>fixed the taxonomy issue if single select and create new together creates two tax also if only one was allowed</li>
<li>Create a settings page and move the license page in there</li>
<li>Add new option to select a default form for every post type</li>
<li>add new option to filter the list posts</li>
<li>fixed radiobuton and checkbox and featured image... was broken from ui rewrite</li>
<li>fixed a issue with title required</li>
<li>fixed a issue with the validation if a - in the form slug</li>
<li>return the merged args arrays after buddyforms_after_save_post</li>
<li>handle form element types as slugs use sanitize_title</li>
<li>recreate the forms global after every form edit</li>
<li>add some checks from fresh install testing issues</li>
<li>fixed a bug with the capabilities js select all</li>
<li>remove old unneeded js</li>
<li>prefix all js css classes to avoid conflicts</li>
<li>rebuild static function set_globals() automatically generate form elements slug if empty</li>
<li>rebuild admin credits. hook into BuddyForms edit screen</li>
<li>Rebuild the why how the $buddyforms global is created to reduce query's</li>
<li>Add a new option After Submission redirect to user posts list</li>
<li>Add a new hook to wp_login_form -> buddyforms_wp_login_form</li>
<li>restructure code to reduce query's deleted unneeded functions</li>
<li>Add .bf_hidden_checkbox to show hide options
rebuild the select, checkbox, radio button form builder form elements. Add new option for label. Update should work.</li>
<li>The Title is now a normal Form Element and can be removed. If no title is se none is added as title.
Same for the content

Use
bf_update_editpost_title
or
bf_update_editpost_content

to set the title or content dynamically.</li>
<li>Switched from horizontal to vertical tabs</li>
<li>Update to the latest select2</li>
<li>Add a new filter buddyforms_after_save_post_redirect to manipulate the redirect url</li>
<li>taxonomy form element default term gets loaded dynamically now via ajax add select2 to the form builder</li>
<li>Create update script for the update form 1.4x to 1.5</li>
<li>Rewrite the complete admin ui</li>
<li>fixing issues from the rewrite</li>
<li>Switched from options based to post based system<li>
<li>removed bootstrap and switched to WordPress standard elements</li>
<li>Createv the new meta boxes for the Form Builder</li>
<li>New readme file to work with edd</li>
<li>clean up the code</li>
<li>Inline documentation</li>
<li>many small fixes and improvements</li>
</ul>


1.4.2
<ul>
<li>removed overflow: auto; from the list css to avoid conflicts</li>
<li>add missing required option to form elements</li>
<li>fix the hidden title required issue</li>
<li>clean up the code</li>
</ul>

1.4.1
<ul>
<li>fixed small merging issues.</li>
</ul>

1.4
<ul>
	<li>add a new filter buddyforms_wp_login_form to the form</li>
	<li>add check if submitter is author</li>
	<li>fix a bug with the form element title. in some cases with other js conflicts it was possible to delete the title tag.</li>
	<li>Add new options to the form builder to select the action happened after form submission</li>
	<li>rewrite the form.php and add ajax form submit functionality to the form</li>
	<li>add nonce check to the ajax form</li>
	<li>build a work around for the wp_editor shortcodes issue. Need to investigate deeper why the shortcodes are executed. For now I load the wp_editor content with jQuery</li>
	<li>fixed smaller bugs reported by users</li>
	<li>add help text</li>
	<li>fixed a types conflict in the form builder</li>
	<li>add post_parent support</li>
	<li>add ajax for post delete</li>
	<li>removed the old delete function</li>
	<li>removed the old delete rewrite roles</li>
	<li>only display posts created by the form</li>
	<li>small css changes and clean up the css</li>
	<li>changed the-loop.php</li>
	<li>Add options to change the Form Element Title</li>
	<li>rewrite the addons section and load all BuddyForms Extension with a buddyforms search from wordpress.org</li>
	<li>changed the featured image function to work with ajax</li>
	<li>add file and featured image ajax upload</li>
	<li>add wp_handle_upload_prefilter to check for allowed file types</li>
	<li>fixed a checkbox issue if attribute id was empty</li>
	<li>start adding deep validation</li>
	<li>add new option hidden to the taxonomy field</li>
	<li>add an ajax form option to the form builder</li>
	<li>start adding validation to the form</li>
	<li>fixed a bug with the featured image upload</li>
	<li>add required option to the content form element</li>
	<li>fixed a bug in the taxonomy hidden form element</li>
	<li>fixed a bug with the allowed post type in the media uploader</li>
	<li>fixed the after submit options. It was not working correctly</li>
	<li>add new option to the title to make it a hidden field</li>
	<li>fixed an issue with the edit link options</li>
	<li>changed the url to the new buddyforms.com site</li>
	<li>rewrite the jQuery to make the button handling work</li>
	<li>add stripslashes to the form elements</li>
	<li>fixed delete featured image issue</li>
	<li>add beautiful validation to the form ;)</li>
	<li>Super nice validation options for every form element max min and message ;)</li>
	<li>Admin UI improvements</li>
	<li>removed the old zendesk support and link to the new support system</li>
	<li>add new option to the file element to select supported file types</li>
	<li>adjust the form messages</li>
	<li>clean up js</li>
	<li>clean up code</li>
	<li>finalise the new ui</li>
</ul>

1.3.2
<ul>
	<li>check if the form is broken and if some fields are missing do not add it to the adminbar</li>
	<li>add new filter bf_form_before_render</li>
	<li>cleanup the code</li>
	<li>small clean up of readme.txt Props @rugwarrior</li>
	<li>fixes small issues in the error handling</li>
	<li>Added German translations. Props @rugwarrior</li>
	<li>Revised PO/MO files for changed source files. Props @rugwarrior</li>
	<li>Fixed typo revison -&gt; revision. Props @rugwarrior</li>
	<li>Started making admin.js translatable. Props @rugwarrior</li>
	<li>Fixed some typos.</li>
	<li>Made more strings translatable.</li>
	<li>add missing translations. Thanks to Milena to point me on this !</li>
</ul>

1.3.1
<ul>
	<li>Fixed a bug in the taxonomy default form element</li>
</ul>

1.3
<ul>
	<li>Add new check if the user has the needed rights before adding the form to the admin bar</li>
	<li>Create new function bf_edit_post_link to support new capabilities in the front end</li>
	<li>Switch from chosen to select2</li>
	<li>Add new error message to logged off users</li>
	<li>Clean up debugger notice</li>
	<li>Optimised the link rewrite function</li>
	<li>Fixed form submit not working on mobile</li>
	<li>Add new filter for then shortcodes button</li>
	<li>Add new shortcodes to tynymce</li>
	<li>Rewrite the Shortcodes</li>
	<li>Changed plugin uri</li>
	<li>Add new filters to manipulate the edit form id</li>
	<li>Add a jQuery to make different submit buttons possible</li>
	<li>Add post_parent as parameter</li>
	<li>Fixed a bug in the error handling</li>
	<li>Small css changes</li>
	<li>Clean up the code</li>
</ul>

1.2
<ul>
	<li>create new form elements for title and content</li>
	<li>3 new form elements: date, number and html</li>
	<li>add wp editor options to the form builder in the content element</li>
	<li>fixed editing BuddyPress js issues</li>
	<li>fixed shortcode over content issues</li>
	<li>update chosen js to the latest version</li>
	<li>add media uploader js</li>
	<li>change split to explode</li>
	<li>load the js css only if a buddyforms view is displayed</li>
	<li>css fixes</li>
	<li>restructure code</li>
	<li>create an update script for the new version</li>
	<li>make it possible to enter tags comma separated</li>
	<li>spelling correction</li>
	<li>add german language files</li>
</ul>

1.1
<ul>
	<li>add language support</li>
	<li>add featured image as form element</li>
	<li>add file form element</li>
	<li>add ajax to delete a file</li>
	<li>fixxed a pagination bug</li>
	<li>only display the post type related taxonomies in the form element options</li>
	<li>add translation textdomain "buddyforms"</li>
	<li>rebuild the add new form screen</li>
	<li>remove unneeded form elements from add form screen</li>
	<li>add mail notification settings</li>
	<li>add mail notification system to buddy forms</li>
	<li>add date time form element for post status future</li>
	<li>spelling session</li>
	<li>ui design</li>
	<li>Settings page Add Ons rewrite</li>
	<li>add new settings page for roles and capabilities</li>
	<li>cleanup the code</li>
	<li>fixed bugs</li>
	<li>add new default option to taxonomy form element</li>
	<li>add Italien language</li>
</ul>

1.0.5
<ul>
	<li>rename hook buddyforms_add_form_element_in_sidebar to buddyforms_add_form_element_to_sidebar</li>
	<li>spelling correction</li>
</ul>

1.0.4
<ul>
	<li>remove unneeded html</li>
</ul>

1.0.3
<ul>
	<li>editing your pending/draft posts from the frontend.</li>
	<li>fixed some css issues</li>
</ul>

1.0.2
<ul>
	<li>remove old button for community forum</li>
	<li>add some new filter</li>
</ul>

1.0.1
<ul>
	<li>catch if create a new post_tag is empty</li>
	<li>metabox rework</li>
</ul>

1.0
<ul>
	<li>first release</li>
</ul>
