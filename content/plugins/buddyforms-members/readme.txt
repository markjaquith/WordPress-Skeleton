=== BuddyForms Members ===
Contributors: svenl77, themekraft, buddyforms
Tags: buddypress, user, members, profiles, custom post types, taxonomy, frontend posting, frontend editing,
Requires at least: WordPress 3.9, BuddyPress 1.7.x
Tested up to: WordPress 4.4.1, BuddyPress 2.4.3
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WordPress Front End Editor And Form Builder For Your User Generated Content

== Description ==

This is the BuddyForms Members Extension. Integrate your BuddyForms Forms into your BuddyPress Members Profile. You need the BuddyForms plugin installed for the plugin to work. <a href="http://buddyforms.com" target="_blank">Get BuddyForms now!</a>

Do you really have the time to be posting and updating your blog everyday? Didn’t think so… <b>BuddyForms Members is a premium plugin for BuddyPress</b> that allows your users to write, edit and upload posts, images, video, & just about any other content to your site, right from their BuddyPress Member Profile!

Once you download the plugin, it’s super simple to setup:
All you have to do is drag-and-drop to build the form your users will be submitting on the front-end.

<h4>...with full moderation and revision control feature.</h4>
It doesn’t matter how complex or big your site is, BuddyForms can handle ALL of your user-generated content.

Build forms with an easy Form Builder. The easy way to bring your existing plugins into the BuddyPress ecosystem and make it accessible for your users right from their profile.

<h4>For Any Post Type </h4>
Choose which post type should be created when users submit your form.
Turn any custom-post-type based WordPress plugin into a collaborative publishing tool and let your users do the dirty work of adding content for you! Perfect for online magazines, blogs, directories, stores, FAQ’s… you name it.
Easy Form Builder
Create and customize forms on the fly with easy drag and drop editing. No coding necessary.

You get all the necessary elements like Text Fields, Email Input, Checkboxes, Dropdowns and more.
<h4>Form Elements</h4>
The custom form builder provides the following elements to create your custom post forms:

<b>Classic Fields = Custom Fields</b><br>
Text Field<br>
Email Field<br>
Link Field<br>
Text Area<br>
Radio Button<br>
Checkbox<br>
Dropdown<br>
Multi Dropdown<br>
<br>
<b>Post Fields</b><br>
Taxonomy Dropdown<br>
Hidden<br>
Turn comments on/off<br>
Moderation and Revisions<br>
<br>
You can choose how your members create, manage and edit their posts. Full control of the publishing process with reviews and revisions.

== Documentation & Support ==

<h4>Extensive Documentation and Support</h4>

All code is neat, clean and well documented (inline as well as in the documentation).

The BuddyForms Documentation with many how-to’s is following now!

If you still get stuck somewhere, our support gets you back on the right track.
You can find all help buttons in your BuddyForms Settings Panel in your WP Dashboard!

== Installation ==

You can download and install BuddyForms Members using the built in WordPress plugin installer. If you download BuddyForms manually,
make sure it is uploaded to "/wp-content/plugins/buddyforms/".

Activate BuddyPress in the "Plugins" admin panel using the "Activate" link. If you're using WordPress Multisite, you can optionally activate BuddyForms Network Wide.

== Frequently Asked Questions ==

You need the BuddyForms plugin installed for the plugin to work.
<a href="http://buddyforms.com" target="_blank">Get BuddyForms now!</a>

When is it the right choice for you?

As soon as you plan a WordPress and BuddyPress powered site where users should be able to submit content from the front-end.
BuddyForms gives you these possibilities for a wide variety of uses.

== Screenshots ==

1. **Overview in Member Profile** - The overview of each author's posts to be seen in the related member profile.

2. **Create/Edit Post in Member Profile** - When creating a new post or editing an existing one, right from the member profile.

3. **Add New Form** - This is how it looks when you add a new form with BuddyForms.

4. **Form Builder** - Enjoy the easy drag-and-drop form builder of BuddyForms.

5. **Backend Overview** - The backend overview of your existing forms.

== Changelog ==

= 1.1.3 =
Add a new function to rewrite the edit link for grouped forms. There have been some rewrite issues left from the 1.1 update.

= 1.1.2 =
There was an issue cursed by the last update.I have added [$bp->current_component][$bp->current_action] as array to the new global to support many sub pages

= 1.1.1 =
Create a new global buddyforms_member_tabs to find the needed form slug
Fixed a redirect issue some users expected wired redirects in the profile. This was happen because of the missing form slug in some setups. Should be fixed now with the now global.

= 1.1 =
Make it work with the latest version of BuddyForms. The BuddyForms array has changed so I adjust the code too the new structure
Changed default BuddyForms to BUDDYFORMS_VERSION
Fixe no post got displayed in the profile tab...
Added post meta for selecting parent tab
Added child tab
Added new option to select the parent
Add child parent form relationship. I use the attached page to group forms.
Clean up code after rewrite
Fix the pagination. The parent my posts pagination was broken. I have fixed this with a redirect to have always the same url structure in the profile.
Add css for hide the home tab. Its not used and gets redirected.

= 1.0.11 =
Fixed a small bug with BP_MEMBERS_SLUG. The constant does not work if the slug got changed

= 1.0.10 =
only display posts created by the form
remove the old delete post structure
fixed the dependencies message
rename session

= 1.0.9 =
Fixed a conflict with the BP Group Hierarchy Plugin. Props to Mitch for reporting and helping me fix this issue.

= 1.0.8 =
add a isset check to prevent a array_key_exists error if no form is created.

= 1.0.7 =
new language files for hebrew thanks to Milena
add support for the shortcodes button
changed the query to only show post parents
changed plugin uri

= 1.0.5 =
 *display the form tab only if the user has the needed role
 *check if the buddy press component exists
 *load the js css if BuddyForms is displayed
 *add new admin notice

= 1.0.4 =
rewrite the integration and data object
its now translation ready
Small bug fixes

= 1.0.3 =
Small bug fixes
Spelling correction

= 1.0.2 =
add wp 3.9 support and added a more detailed readme description

= 1.0.1 =
add buddyforms_members_requirements check

= 1.0 =
final 1.0 version
