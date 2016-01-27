<?php
/**
 * s2Member's Brute Force protection routines.
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
 * @package s2Member\Brute_Force
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_brute_force'))
{
	/**
	 * s2Member's Brute Force protection routines.
	 *
	 * @package s2Member\Brute_Force
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_brute_force
	{
		/**
		 * Tracks failed login attempts.
		 *
		 * Prevents an attacker from guessing Usernames/Passwords.
		 * Allows only 5 failed login attempts every 30 minutes.
		 *
		 * @package s2Member\Brute_Force
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('wp_login_failed');``
		 *
		 * @param string $username Expects the $username to be passed in through the Hook.
		 */
		public static function track_failed_logins($username = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_track_failed_logins', get_defined_vars());
			unset($__refs, $__v);

			if(($max = $GLOBALS['WS_PLUGIN__']['s2member']['o']['max_failed_login_attempts']))
			{
				$exp_secs = strtotime('+'.apply_filters('ws_plugin__s2member_track_failed_logins__exp_time', '30 minutes', get_defined_vars())) - time();
				// If you add Filters to this value, you should use a string that is compatible with PHP's strtotime() function.

				$ip = $_SERVER['REMOTE_ADDR']; // Default value.
				if(!empty($GLOBALS['s2member_pro_remote_op_auth_check_user_ip'])
				   && c_ws_plugin__s2member_utils_conds::pro_is_installed()
				   && c_ws_plugin__s2member_pro_remote_ops::is_remote_op('auth_check_user')
				) $ip = $GLOBALS['s2member_pro_remote_op_auth_check_user_ip'];

				$transient = 's2m_ipr_'.md5('s2member_transient_failed_login_attempts_'.$ip);
				set_transient($transient, (int)get_transient($transient) + 1, $exp_secs);
			}
			do_action('ws_plugin__s2member_after_track_failed_logins', get_defined_vars());
		}

		/**
		 * Stops anyone attempting a Brute Force attack.
		 *
		 * Prevents an attacker from guessing Usernames/Passwords.
		 * Allows only 5 failed login attempts every 30 minutes.
		 *
		 * @package s2Member\Brute_Force
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('authenticate');``
		 *
		 * @param WP_User $user Expects a WP_User object, or possibly a null value.
		 *   This parameter value is simply passed through this routine.
		 *
		 * @return WP_User|null Either null, the ``$user`` obj, or a `WP_Error` obj.
		 */
		public static function stop_brute_force_logins($user = NULL)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_stop_brute_force_logins', get_defined_vars());
			unset($__refs, $__v);

			if(($max = $GLOBALS['WS_PLUGIN__']['s2member']['o']['max_failed_login_attempts']))
			{
				$ip = $_SERVER['REMOTE_ADDR']; // Default value.
				if(!empty($GLOBALS['s2member_pro_remote_op_auth_check_user_ip'])
				   && c_ws_plugin__s2member_utils_conds::pro_is_installed()
				   && c_ws_plugin__s2member_pro_remote_ops::is_remote_op('auth_check_user')
				) $ip = $GLOBALS['s2member_pro_remote_op_auth_check_user_ip'];

				if((int)get_transient('s2m_ipr_'.md5('s2member_transient_failed_login_attempts_'.$ip)) > $max)
				{
					$exp_secs = strtotime('+'.apply_filters('ws_plugin__s2member_track_failed_logins__exp_time', '30 minutes', get_defined_vars())) - time();
					// If you add Filters to this value, you should use a string that is compatible with PHP's strtotime() function.

					$about  = c_ws_plugin__s2member_utils_time::approx_time_difference(time(), time() + $exp_secs);
					$errors = new WP_Error('incorrect_password', sprintf(_x('Max failed logins. Please wait %s and try again.', 's2member-front', 's2member'), $about));

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action('ws_plugin__s2member_during_stop_brute_force_logins', get_defined_vars());
					unset($__refs, $__v);
				}
			}
			return apply_filters('ws_plugin__s2member_stop_brute_force_logins', !empty($errors) ? $errors : $user, get_defined_vars());
		}
	}
}