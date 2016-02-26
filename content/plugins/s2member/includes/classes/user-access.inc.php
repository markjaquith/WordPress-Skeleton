<?php
/**
 * User access routines.
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
 * @package s2Member\User_Access
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_user_access'))
{
	/**
	 * User access routines.
	 *
	 * @package s2Member\User_Access
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_user_access
	{
		/**
		 * Determines the Access Role of a User/Member.
		 *
		 * If ``$user`` is NOT passed in, check the current User/Member.
		 * If ``$user`` IS passed in, this function will check a specific ``$user``.
		 *   Returns their Role ID/Name value.
		 *
		 * @package s2Member\User_Access
		 * @since 3.5
		 *
		 * @param \WP_User $user Optional. A `WP_User` object. Defaults to the current User.
		 *   In order to check the current User, you must call this function with no arguments/parameters.
		 *
		 * @return string Role ID/Name, or an empty string if they have no Role, or if ``$user`` does not exist, or if no User is currently logged-in.
		 */
		public static function user_access_role($user = NULL)
		{
			if((func_num_args() && (!is_object($user) || empty($user->ID))) || (!func_num_args() && !$user && (!is_object($user = (is_user_logged_in()) ? wp_get_current_user() : FALSE) || empty($user->ID))))
				return apply_filters('ws_plugin__s2member_user_access_role', '', get_defined_vars());

			else // Else we return the first Role in their array of assigned WordPress Roles.
				return apply_filters('ws_plugin__s2member_user_access_role', reset($user->roles), get_defined_vars());
		}

		/**
		 * Determines Custom Capabilities of a User/Member.
		 *
		 * If ``$user`` is NOT passed in, check the current User/Member.
		 * If ``$user`` IS passed in, this function will check a specific ``$user``.
		 *   Returns an array of Custom Capabilities.
		 *
		 * @package s2Member\User_Access
		 * @since 3.5
		 *
		 * @param \WP_User $user Optional. A `WP_User` object. Defaults to the current User.
		 *   In order to check the current User, you must call this function with no arguments/parameters.
		 *
		 * @return array Array of Custom Capabilities, or an empty array if they have no Custom Capabilities, or if ``$user`` does not exist, or if no User is currently logged-in.
		 */
		public static function user_access_ccaps($user = NULL)
		{
			if((func_num_args() && (!is_object($user) || empty($user->ID))) || (!func_num_args() && !$user && (!is_object($user = (is_user_logged_in()) ? wp_get_current_user() : FALSE) || empty($user->ID))))
				return apply_filters('ws_plugin__s2member_user_access_ccaps', array(), get_defined_vars());

			else // Otherwise, we DO have the $user object available.
			{
				$ccaps = array(); // Initializes $ccaps array.

				foreach($user->allcaps as $cap => $cap_enabled)
					if(preg_match('/^access_s2member_ccap_/', $cap) && $cap_enabled)
						$ccaps[] = preg_replace('/^access_s2member_ccap_/', '', $cap);

				return apply_filters('ws_plugin__s2member_user_access_ccaps', $ccaps, get_defined_vars());
			}
		}

		/**
		 * Determines Access Level of a User/Member.
		 *
		 * If ``$user`` is NOT passed in, check the current User/Member.
		 * If ``$user`` IS passed in, this function will check a specific ``$user``.
		 *   Returns `-1` thru number of configured Levels, according to the Access Level#.
		 *
		 * @package s2Member\User_Access
		 * @since 3.5
		 *
		 * @param object $user Optional. A `WP_User` object. Defaults to the current User.
		 *   In order to check the current User, you must call this function with no arguments/parameters.
		 *
		 * @return int Access Level#, `-1` if ``$user`` does not exist, or if no User is currently logged-in.
		 */
		public static function user_access_level($user = NULL)
		{
			if((func_num_args() && (!is_object($user) || empty($user->ID))) || (!func_num_args() && !$user && (!is_object($user = (is_user_logged_in()) ? wp_get_current_user() : FALSE) || empty($user->ID))))
				return apply_filters('ws_plugin__s2member_user_access_level', -1, get_defined_vars()); // No $user, or NOT logged in.

			for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--)
				if($user->has_cap('access_s2member_level'.$n)) // Membership Level access.
				{
					return apply_filters('ws_plugin__s2member_user_access_level', $n, get_defined_vars());
				}
			return apply_filters('ws_plugin__s2member_user_access_level', 0, get_defined_vars());
		}

		/**
		 * Determines Access Level of a specific Role.
		 *
		 * @package s2Member\User_Access
		 * @since 3.5
		 *
		 * @param string $role A WordPress Role ID/Name.
		 *
		 * @return int Access Level#, `-1` if ``$role`` is empty.
		 */
		public static function user_access_role_to_level($role = '')
		{
			if(!($role = strtolower($role))) // No ``$role`` provided. Default value of -1.
				return apply_filters('ws_plugin__s2member_user_access_role_to_level', -1, get_defined_vars());

			else if(in_array($role, array('administrator', 'editor', 'author', 'contributor', 'bbp_moderator')))
				return apply_filters('ws_plugin__s2member_user_access_role_to_level', $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels'], get_defined_vars());

			else if(preg_match('/^s2member_level([0-9]+)$/i', $role, $m) && $m[1] >= 1) // Test for s2Member Roles.
				return apply_filters('ws_plugin__s2member_user_access_role_to_level', (int)$m[1], get_defined_vars());

			else if($role === 'subscriber') // Testing for Free Subscriber Role.
				return apply_filters('ws_plugin__s2member_user_access_role_to_level', 0, get_defined_vars());

			else // Else we assume this is a 'User' ( a Free Subscriber with an Access Level of 0. ).
				return apply_filters('ws_plugin__s2member_user_access_role_to_level', 0, get_defined_vars());
		}

		/**
		 * Determines Access Label for a User/Member.
		 *
		 * If ``$user`` is NOT passed in, check the current User/Member.
		 * If ``$user`` IS passed in, this function will check a specific ``$user``.
		 *
		 * @package s2Member\User_Access
		 * @since 3.5
		 *
		 * @param object $user Optional. A `WP_User` object. Defaults to the current User.
		 *   In order to check the current User, you must call this function with no arguments/parameters.
		 *
		 * @return string Access Level Label, empty string if ``$user`` does not exist, or if no User is currently logged-in.
		 */
		public static function user_access_label($user = NULL)
		{
			if((func_num_args() && (!is_object($user) || empty($user->ID))) || (!func_num_args() && !$user && (!is_object($user = (is_user_logged_in()) ? wp_get_current_user() : FALSE) || empty($user->ID))))
			{
				return apply_filters('ws_plugin__s2member_user_access_label', '', get_defined_vars()); // No $user, or NOT logged in.
			}
			else if(($level = c_ws_plugin__s2member_user_access::user_access_level($user)) >= 0 && !empty($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$level.'_label']))
			{
				return apply_filters('ws_plugin__s2member_user_access_label', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$level.'_label'], get_defined_vars());
			}
			else // Else there is no Label configured for this User/Member. Return empty string.
				return apply_filters('ws_plugin__s2member_user_access_label', '', get_defined_vars());
		}
	}
}