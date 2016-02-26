=== User Role Editor ===
Contributors: shinephp
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=vladimir%40shinephp%2ecom&lc=RU&item_name=ShinePHP%2ecom&item_number=User%20Role%20Editor%20WordPress%20plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: user, role, editor, security, access, permission, capability
Requires at least: 4.0
Tested up to: 4.4.2
Stable tag: 4.23.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

User Role Editor WordPress plugin makes user roles and capabilities changing easy. Edit/add/delete WordPress user roles and capabilities.

== Description ==

With User Role Editor WordPress plugin you can change user role (except Administrator) capabilities easy, with a few clicks.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

To read more about 'User Role Editor' visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/) at [shinephp.com](http://shinephp.com)

Short demo about 1st steps with User Role Editor:
https://www.youtube.com/watch?v=UmMtOmWGGxY

Do you need more functionality with quality support in the real time? Do you wish to remove advertisements from User Role Editor pages? 
[Buy Pro version](https://www.role-editor.com). 
[User Role Editor Pro](https://www.role-editor.com) includes extra modules:
<ul>
<li>Block selected admin menu items for role.</li>
<li>Block selected widgets under "Appearance" menu for role.</li>
<li>Block selected meta boxes (dashboard, posts, pages, custom post types) for role.</li>
<li>"Export/Import" module. You can export user roles to the local file and import them then to any WordPress site or other sites of the multi-site WordPress network.</li> 
<li>Roles and Users permissions management via Network Admin  for multisite configuration. One click Synchronization to the whole network.</li>
<li>"Other roles access" module allows to define which other roles user with current role may see at WordPress: dropdown menus, e.g assign role to user editing user profile, etc.</li>
<li>Per posts/pages users access management to post/page editing functionality.</li>
<li>Per plugin users access management for plugins activate/deactivate operations.</li>
<li>Per form users access management for Gravity Forms plugin.</li>
<li>Shortcode to show enclosed content to the users with selected roles only.</li>
<li>Posts and pages view restrictions for selected roles.</li>
</ul>
Pro version is advertisement free. Premium support is included.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor.zip" archive content to the "/wp-content/plugins/user-role-editor" directory.
3. Activate "User Role Editor" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Users"-"User Role Editor" menu item and change your WordPress standard roles capabilities according to your needs.

== Frequently Asked Questions ==
- Does it work with WordPress in multi-site environment?
Yes, it works with WordPress multi-site. By default plugin works for every blog from your multi-site network as for locally installed blog.
To update selected role globally for the Network you should turn on the "Apply to All Sites" checkbox. You should have superadmin privileges to use User Role Editor under WordPress multi-site.
Pro version allows to manage roles of the whole network from the Netwok Admin.

To read full FAQ section visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/#faq) at [shinephp.com](shinephp.com).

== Screenshots ==
1. screenshot-1.png User Role Editor main form
2. screenshot-2.png Add/Remove roles or capabilities
3. screenshot-3.png User Capabilities link
4. screenshot-4.png User Capabilities Editor
5. screenshot-5.png Bulk change role for users without roles

To read more about 'User Role Editor' visit [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/) at [shinephp.com](shinephp.com).

= Translations =

If you wish to check available translations or help with plugin translation to your language visit this link
https://translate.wordpress.org/projects/wp-plugins/user-role-editor/

== Changelog ==
= [4.23.2] 03.02.2016 =
* Fix: PHP warning "Strict Standards: Static function URE_Base_Lib::get_instance() should not be abstract" was generated

= [4.23.1] 01.02.2016 =
* Fix: 'get_called_class()' function call was excluded for the compatibility with PHP 5.2.*
* Fix: ure-users.js was loaded not only to the 'Users' page.

= [4.23] 31.01.2016 =
* Fix: "Users - Without Role" button showed empty roles drop down list on the 1st call. 
* Update: Own task queue was added, so code which should executed once after plugin activation is executed by the next request to WP and may use a selected WordPress action to fire with a needed priority.
* Update: Call of deprecated mysql_server_info() is replaced with $wpdb->db_version().
* Update: Singleton patern is applied to the URE_Lib class.
* Minor code enhancements

= [4.22] 15.01.2016 =
* Unused 'add_users' capability was removed from the list of core capabilities as it was removed from WordPress starting from version 4.4
* bbPress user capabilities are supported for use in the non-bbPress roles. You can not edit roles created by bbPress, as bbPress re-creates them dynamically for every request to the server. Full support for bbPress roles editing will be included into User Role Editor Pro version 4.22.
* Self-added "Other Roles" column removed from "Users" list, as WordPress started to show all roles assigned to the user in its own "Role" column.
* 'ure_show_additional_capabilities_section' filter allows to  hide 'Other Roles' section at the 'Add new user', 'Edit user' pages.

= [4.21.1] 16.12.2015 =
* Fix: 'Update' button did not work at User's Capabilities page due to confirmation dialog call error.


= [4.21] 11.12.2015 =
* It's possible to switch off the update role confirmation (Settings - User Role Editor - General tab).
* Standard JavaScript confirm box before role update was replaced with custom one to exclude 'Prevent this page from creating additional dialogs' option in the Google Chrome browser.
* Fix: Removed hard coded folder name (user-role-editor) from the used paths.


= [4.20.1] 15.11.2015 =
* Fix: "Primary default role" drop-down menu was not shown at "Settings - User Role Editor - Default Roles" tab for WordPress single site installation.

= [4.20] 15.11.2015 =
* "Additional options" section was added to the user role editor page. Currently it contains the only "Hide admin bar". The list of options may be customized/extended by developers via "ure_role_additonal_options" filter.
* "Default Role" button is hidden to not duplicate functionality. Use "Settings - User Role Editor - Default Roles" tab instead. This button is available only for the single sites of WP multisite now.
* Code restructure, optimization: administrator protection parts extracted to the separate class.

= [4.19.3] 14.10.2015 =
* Fix: minor CSS change.
* Automatically add all available custom post types capabilities to the administrator role under the single site environment. Custom posts types selection query updated to include all custom post types except 'built-in' when adding custom capabilities for them
* Special flag was set to indicate that single site administrator gets raised (superadmin) permissions temporary especially for the 'user-new.php' page, but current user is not the superadmin really. 
  (This temporary permissions raising is done to allow single site administrator to add new users under multisite if related option is active.)

= [4.19.2] 01.10.2015 =
* Fix: multiple default roles assignment did not work under the multisite environment, when user was created from front-end by WooCommerce, etc.
* Update: the translation text domain was changed to the plugin slug (user-role-editor) for the compatibility with translations.wordpress.org
* Update: CSS enhanced to exclude column wrapping for the capabilities with the long names.

= [4.19.1] 20.08.2015 =
* Default role value has not been refreshed automatically after change at the "Default Role" dialog - fixed.
* More detailed notice messages are shown after default role change - to reflect a possible error or problem.
* Other default roles (in addition to the primary role) has been assigned to a new registered user for requests from the admin back-end only. Now this feature works for the requests from the front-end user registration forms too.

= 4.19 =
* 28.07.2015
* It is possible to assign to the user multiple roles directly through a user profile edit page. 
* Custom SQL-query (checked if the role is in use and slow on the huge data) was excluded and replaced with WordPress built-in function call. [Thanks to Aaron](https://wordpress.org/support/topic/poorly-scaling-queries).
* Bulk role assignment to the users without role was rewritten for cases with a huge quant of users. It processes just 50 users without role for the one request to return the answer from the server in the short time. The related code was extracted to the separate class.
* Code to fix JavaScript and CSS compatibility issues introduced by other plugins and themes, which load its stuff globally, was extracted into the separate class.
* Custom filters were added: 'ure_full_capabilites' - takes 1 input parameter, array with a full list of user capabilities visible at URE, 'ure_built_in_wp_caps' - takes 1 input parameter, array with a list of WordPress core user capabilities. These filters may be useful if you give access to the URE for some not administrator user, and wish to change the list of capabilities which are available to him at URE.
* Dutch translation was updated. Thanks to Gerhard Hoogterp.

= 4.18.4 =
* 30.04.2015
* Calls to the function add_query_arg() is properly escaped with esc_url_raw() to exclude potential XSS vulnerabilities. Nothing critical: both calls of add_query_arg() are placed at the unused sections of the code.
* Italian translation was updated. Thanks to Leo.

= 4.18.3 =
* 24.02.2015
* Fixed PHP fatal error for roles reset operation.
* Fixed current user capability checking before URE Options page open.
* 3 missed phrases were added to the translations files. Thanks to [Morteza](https://wordpress.org/support/profile/mo0orteza)
* Hebrew translation updated. Thanks to [atar4u](http://atar4u.com)
* Persian translation updated. Thanks to [Morteza](https://wordpress.org/support/profile/mo0orteza)

= 4.18.2 =
* 06.02.2015
* New option "Edit user capabilities" was added. If it is unchecked - capabilities section of selected user will be shown in the readonly mode. Administrator (except superadmin for multisite) can not assign capabilities to the user directly. He should make it using roles only.
* More universal checking applied to the custom post type capabilities creation to exclude not existing property notices.
* Multisite: URE's options page is prohibited by 'manage_network_users' capability instead of 'ure_manage_options' in case single site administrators does not have permission to use URE.
* URE protects administrator user from editing by other users by default. If you wish to turn off such protection, you may add filter 'ure_supress_administrators_protection' and return 'true' from it.
* Plugin installation to the WordPress multisite with large (thousands) subsites had a problem with script execution time. Fixed. URE does not try to update all subsites at once now. It does it for every subsite separately, only when you visit that subsite.
* Fixed JavaScript bug with 'Reset Roles' for FireFox v.34.


Click [here](https://www.role-editor.com/changelog)</a> to look at [the full list of changes](https://www.role-editor.com/changelog) of User Role Editor plugin.


== Additional Documentation ==

You can find more information about "User Role Editor" plugin at [this page](http://www.shinephp.com/user-role-editor-wordpress-plugin/)

I am ready to answer on your questions about plugin usage. Use [plugin page comments](http://www.shinephp.com/user-role-editor-wordpress-plugin/) for that.
