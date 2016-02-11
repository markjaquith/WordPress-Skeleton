=== Multiple Roles ===
Contributors: SeventhSteel
Tags: multiple roles, multiple roles per user, user roles, edit user roles, edit roles, more than one role, more than one role per user, more than one role for each user, many roles per user, unlimited roles
Requires at least: 3.1
Tested up to: 4.2.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow users to have multiple roles on one site.

== Description ==

This plugin allows you to select multiple roles for a user - something that WordPress already supports "under the hood", but doesn't provide a user interface for.

User edit screens will display a checklist of roles instead of the default role dropdown. The main user list screen will also display all roles a user has.

That's it. No extra settings. This plugin is a good complement to other user plugins that don't support multiple roles, such as <a href="https://wordpress.org/plugins/members/">Members</a>.

== Installation ==

= Automatic Install =

1. Log into your WordPress dashboard and go to Plugins &rarr; Add New
2. Search for "Multiple Roles"
3. Click "Install Now" under the Multiple Roles plugin
4. Click "Activate Now"

= Manual Install =

1. Download the plugin from the download button on this page
2. Unzip the file, and upload the resulting `multiple-roles` folder to your `/wp-content/plugins` directory
3. Log into your WordPress dashboard and go to Plugins
4. Click "Activate" under the Multiple Roles plugin

== Frequently Asked Questions ==

= Who can edit users roles? =

Anyone with the `edit_users` capability. By default, that means only administrators and, on multi-site networks, super admins.

= Can you edit your own roles? =

If you're a network admin on a multi-site setup, yes, you can edit your roles in sites on that network. Otherwise, no. This is how WordPress works normally too.

= I'm on the user edit screen - where's the checklist of roles? =

It's underneath the default profile stuff, under the heading "Permissions". If you still can't find it, you might be on your own profile page, or you might not have the `edit_users` capability.

= Can you remove all roles from a user? =

Sure. The user will still be able to log in and out, but won't be able to access any admin screens or see private pages. However, the user will still be able to see the WP Toolbar by default, which displays links to the Dashboard and Profile screens, so clicking on those will result in seeing a permission error.

== Screenshots ==

1. The roles checklist on Edit User screens
2. The Users screen with the enhanced Roles column

== Changelog ==

= 1.0 =
* Initial release.