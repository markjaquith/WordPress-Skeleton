<?php
/**
* Pluggable functions within WordPress.
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
* @package s2Member
* @since 110707
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if (!function_exists ('wp_new_user_notification'))
	{
		/**
		* New User notifications.
		*
		* The arguments to this function are passed through the class method.
		*
		* @package s2Member
		* @since 110707
		*
		* @return class Return-value of class method.
		*/
		if ($GLOBALS['WS_PLUGIN__']['s2member']['o']['new_user_emails_enabled'])
			{
				function wp_new_user_notification()
					{
						$args = func_get_args(); // Function arguments.

						if (version_compare(get_bloginfo('version'), '4.3.1', '>='))
						{
							$_43_args = array(); // Initialize WP v4.3.1 args.

							$_43_args[0] = isset($args[0]) ? $args[0] : 0;
							// This is always a user ID. Still the same.

							$_43_args[1] = isset($args[3]) ? $args[3] : '';
							// Our `wp_new_user_notification()` implementation supports a 4th arg: `$user_pass`.
							// Default; no passwords via email in WordPress v4.3+.

							if (!empty($args[2]) && $args[2] === 'both')
								$_43_args[2] = array('user', 'admin');

							else if (!empty($args[2]) && is_string($args[2])) // Something else?
								$_43_args[2] = array($args[2]); // e.g., `user`, `admin`.

							else $_43_args[2] = array('admin'); // Default behavior.

							$args = $_43_args; // Use restructured arguments.
						}
						// Sucky WP v4.3 workaround. I was forced into doing this!!
						else if (version_compare(get_bloginfo('version'), '4.3', '>='))
						{
							$_43_args = array(); // Initialize WP v4.3 args.

							$_43_args[0] = isset($args[0]) ? $args[0] : 0;
							// This is always a user ID. Still the same.

							$_43_args[1] = isset($args[2]) ? $args[2] : '';
							// Our `wp_new_user_notification()` implementation supports a 3rd arg: `$user_pass`.
							// Default; no passwords via email in WordPress v4.3+.

							if (!empty($args[1]) && $args[1] === 'both')
								$_43_args[2] = array('user', 'admin');

							else if (!empty($args[1]) && is_string($args[1])) // Something else?
								$_43_args[2] = array($args[1]); // e.g., `user`, `admin`.

							else $_43_args[2] = array('admin'); // Default behavior.

							$args = $_43_args; // Use restructured arguments.
						}
						return call_user_func_array('c_ws_plugin__s2member_email_configs::new_user_notification', $args);
					}
				add_filter('wpmu_welcome_user_notification', 'c_ws_plugin__s2member_email_configs::new_user_notification', 10, 2);
			}
		$GLOBALS['WS_PLUGIN__']['s2member']['c']['pluggables']['wp_new_user_notification'] = true;
	}
