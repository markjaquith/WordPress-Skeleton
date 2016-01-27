<?php
/**
* New User handlers.
*
* Copyright: © 2009-2011
* {@link http://websharks-inc.com/ WebSharks, Inc.}
* (coded in the USA)
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package s2Member\New_Users
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_user_new"))
	{
		/**
		* New User handlers.
		*
		* @package s2Member\New_Users
		* @since 3.5
		*/
		class c_ws_plugin__s2member_user_new
			{
				/**
				* Adds Custom Fields to `/wp-admin/user-new.php`.
				*
				* We have to buffer because `/user-new.php` has NO Hooks.
				*
				* @package s2Member\New_Users
				* @since 3.5
				*
				* @attaches-to ``add_action("load-user-new.php");``
				*
				* @return null
				*/
				public static function admin_user_new_fields ()
					{
						global $pagenow; // The current admin page file name.

						do_action("ws_plugin__s2member_before_admin_user_new_fields", get_defined_vars ());

						if (is_blog_admin () && $pagenow === "user-new.php" && current_user_can ("create_users"))
							{
								ob_start ("c_ws_plugin__s2member_user_new_in::_admin_user_new_fields");

								do_action("ws_plugin__s2member_during_admin_user_new_fields", get_defined_vars ());
							}

						do_action("ws_plugin__s2member_after_admin_user_new_fields", get_defined_vars ());

						return /* Return for uniformity. */;
					}
			}
	}
