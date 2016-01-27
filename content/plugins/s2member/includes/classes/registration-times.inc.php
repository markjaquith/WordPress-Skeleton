<?php
/**
 * Registration Times.
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
 * @package s2Member\Registrations
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_registration_times"))
{
	/**
	 * Registration Times.
	 *
	 * @package s2Member\Registrations
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_registration_times
	{
		/**
		 * Synchronizes Paid Registration Times with Role assignments.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("set_user_role");``
		 *
		 * @param integer|string $user_id A numeric WordPress User ID should be passed in by the Action Hook.
		 * @param string         $role A WordPress Role ID/Name should be passed in by the Action Hook.
		 *
		 * @return null
		 */
		public static function synchronize_paid_reg_times($user_id = FALSE, $role = FALSE)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_before_synchronize_paid_reg_times", get_defined_vars());
			unset($__refs, $__v);

			if($user_id && is_object($user = new WP_User ($user_id)) && !empty($user->ID) && ($level = c_ws_plugin__s2member_user_access::user_access_level($user)) > 0)
			{
				$pr_times                 = get_user_option("s2member_paid_registration_times", $user_id);
				$pr_times["level"]        = (empty($pr_times["level"])) ? time() : $pr_times["level"];
				$pr_times["level".$level] = (empty($pr_times["level".$level])) ? time() : $pr_times["level".$level];
				update_user_option($user_id, "s2member_paid_registration_times", $pr_times); // Update now.
			}
		}

		/**
		 * Retrieves a Registration Time.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @param integer|string $user_id Optional. A numeric WordPress User ID. Defaults to the current User, if logged-in.
		 *
		 * @return int A Unix timestamp, indicating Registration Time, else `0` on failure.
		 */
		public static function registration_time($user_id = 0)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_before_registration_time", get_defined_vars());
			unset($__refs, $__v);

			$user = ($user_id) ? new WP_User ($user_id) : ((is_user_logged_in()) ? wp_get_current_user() : FALSE);

			if(is_object($user) && !empty($user->ID) && ($user_id = $user->ID) && $user->user_registered)
			{
				return apply_filters("ws_plugin__s2member_registration_time", strtotime($user->user_registered), get_defined_vars());
			}
			else // Else we return a default value of 0, because there is insufficient data.
				return apply_filters("ws_plugin__s2member_registration_time", 0, get_defined_vars());
		}

		/**
		 * Retrieves a Paid Registration Time.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @param int|string $level Optional. Defaults to the first/initial Paid Registration Time, regardless of Level#.
		 * @param int|string $user_id Optional. A numeric WordPress User ID. Defaults to the current User, if logged-in.
		 *
		 * @return int A Unix timestamp, indicating Paid Registration Time, else `0` on failure.
		 */
		public static function paid_registration_time($level = FALSE, $user_id = FALSE)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_before_paid_registration_time", get_defined_vars());
			unset($__refs, $__v);

			$level = (!is_numeric($level)) ? "level" : "level".preg_replace("/[^0-9]/", "", (string)$level);
			$user  = ($user_id) ? new WP_User ($user_id) : ((is_user_logged_in()) ? wp_get_current_user() : FALSE);

			if($level && is_object($user) && !empty($user->ID) && ($user_id = $user->ID) && is_array($pr_times = get_user_option("s2member_paid_registration_times", $user_id)))
			{
				return apply_filters("ws_plugin__s2member_paid_registration_time", ((isset ($pr_times[$level])) ? (int)$pr_times[$level] : 0), get_defined_vars());
			}
			else // Else we return a default value of `0`, because there is insufficient data.
				return apply_filters("ws_plugin__s2member_paid_registration_time", 0, get_defined_vars());
		}
	}
}