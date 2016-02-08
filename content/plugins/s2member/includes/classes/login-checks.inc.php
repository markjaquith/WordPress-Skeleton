<?php
/**
 * Login checks.
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
 * @package s2Member\Login_Checks
 * @since 131025
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_login_checks'))
{
	/**
	 * Login checks.
	 *
	 * @package s2Member\Login_Checks
	 * @since 131025
	 */
	class c_ws_plugin__s2member_login_checks
	{
		/**
		 * Assists in multisite User authentication.
		 *
		 * @package s2Member\Login_Checks
		 * @since 131025
		 *
		 * @attaches-to ``add_filter('wp_authenticate_user');``
		 *
		 * @param WP_User|WP_Error Expects either a WP_User or WP_Error object passed in by the Filter.
		 *
		 * @return WP_User|WP_Error WP_User or WP_Error object (if there was a problem).
		 */
		public static function ms_wp_authenticate_user($user_or_wp_error)
		{
			if(!is_multisite()) return $user_or_wp_error;

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_ms_wp_authenticate_user', get_defined_vars());
			unset($__refs, $__v);

			if(is_a($user_or_wp_error, 'WP_User') && ($user = $user_or_wp_error) && $user->ID && !is_super_admin($user->ID) && !in_array(get_current_blog_id(), array_keys(get_blogs_of_user($user->ID)), TRUE))
				$user_or_wp_error = new WP_Error('invalid_username', _x('<strong>ERROR</strong>: Invalid username for this site.', 's2member-front', 's2member'));

			return apply_filters('ws_plugin__s2member_ms_wp_authenticate_user', $user_or_wp_error, get_defined_vars());
		}

		/**
		 * Assists in User authentication (stops max simultaneous logins).
		 *
		 * @package s2Member\Login_Checks
		 * @since 131025
		 *
		 * @attaches-to ``add_filter('wp_authenticate_user');``
		 *
		 * @param WP_User|WP_Error Expects either a WP_User or WP_Error object passed in by the Filter.
		 *
		 * @return WP_User|WP_Error WP_User or WP_Error object (if there was a problem).
		 */
		public static function stop_simultaneous_logins($user_or_wp_error)
		{
			if(!($max = $GLOBALS['WS_PLUGIN__']['s2member']['o']['max_simultaneous_logins']))
				return $user_or_wp_error; // Simultaneous login monitoring not enabled here.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_stop_simultaneous_logins', get_defined_vars());
			unset($__refs, $__v);

			if(is_a($user_or_wp_error, 'WP_User') && ($user = $user_or_wp_error) && $user->ID && !is_super_admin($user->ID) && c_ws_plugin__s2member_login_checks::get_simultaneous_logins($user->user_login) + 1 > $max)
				$user_or_wp_error = new WP_Error('max_simultaneous_logins', sprintf(_x('<strong>ERROR</strong>: Max simultaneous logins for username: %1$s. Please wait %2$s and try again.', 's2member-front', 's2member'), $user->user_login, $GLOBALS['WS_PLUGIN__']['s2member']['o']['max_simultaneous_logins_timeout']));

			return apply_filters('ws_plugin__s2member_stop_simultaneous_logins', $user_or_wp_error, get_defined_vars());
		}

		/**
		 * Monitors simultaneous logins (updates timer on each page view).
		 *
		 * @package s2Member\Login_Checks
		 * @since 131025
		 *
		 * @attaches-to ``add_action('init');``
		 *
		 * @param WP_User|WP_Error Expects either a WP_User or WP_Error object passed in by the Filter.
		 *
		 * @return WP_User|WP_Error WP_User or WP_Error object (if there was a problem).
		 */
		public static function monitor_simultaneous_logins()
		{
			if(!is_user_logged_in() || is_super_admin())
				return; // Nothing to do here.

			if(!($max = $GLOBALS['WS_PLUGIN__']['s2member']['o']['max_simultaneous_logins']))
				return; // Simultaneous login monitoring not enabled here.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_monitor_simultaneous_logins', get_defined_vars());
			unset($__refs, $__v);

			$user     = wp_get_current_user();
			$username = $user->user_login; // The username.
			c_ws_plugin__s2member_login_checks::update_simultaneous_logins($username, $user, 'timer');
		}

		/**
		 * Handles simultaneous logouts.
		 *
		 * @package s2Member\Login_Checks
		 * @since 131025
		 *
		 * @attaches-to ``add_action('clear_auth_cookie');``
		 */
		public static function simultaneous_logout()
		{
			if(!is_user_logged_in() || is_super_admin())
				return; // Nothing to do here.

			if(!($max = $GLOBALS['WS_PLUGIN__']['s2member']['o']['max_simultaneous_logins']))
				return; // Simultaneous login monitoring not enabled here.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_simultaneous_logout', get_defined_vars());
			unset($__refs, $__v);

			$user     = wp_get_current_user();
			$username = $user->user_login; // The username.
			c_ws_plugin__s2member_login_checks::update_simultaneous_logins($username, $user, 'decrement');
		}

		/**
		 * Get simultaneous logins for a particular username.
		 *
		 * @package s2Member\Login_Checks
		 * @since 131025
		 *
		 * @param string $username Expects a username (e.g., a `user_login` value).
		 *
		 * @return integer Current number of simultaneous logins.
		 */
		public static function get_simultaneous_logins($username)
		{
			if(!$username) return 0; // Nothing to get.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_get_simultaneous_logins', get_defined_vars());
			unset($__refs, $__v);

			$prefix            = 's2m_slm_'; // s2Member Transient prefix for all simultaneous login monitoring.
			$transient_entries = $prefix.md5('s2member_simultaneous_login_entries_for_'.strtolower((string)$username));

			$timeout     = $GLOBALS['WS_PLUGIN__']['s2member']['o']['max_simultaneous_logins_timeout'];
			$timeout_ago = strtotime('-'.$timeout); // e.g., 30 minutes ago.

			$entries = (is_array($entries = get_transient($transient_entries))) ? $entries : array();
			foreach($entries as $_entry => $_time /* Auto-expire entries, based on time. */)
				if($_time < $timeout_ago) unset($entries[$_entry]);

			return apply_filters('ws_plugin__s2member_get_simultaneous_logins', count($entries), get_defined_vars());
		}

		/**
		 * Update simultaneous logins for a particular username.
		 *
		 * @package s2Member\Login_Checks
		 * @since 131025
		 *
		 * @attaches-to ``add_action('wp_login');``
		 *
		 * @param string       $username Expects a username (e.g., a `user_login` value).
		 * @param WP_User|null $user When fired against `wp_login` this receives a WP_User object also.
		 * @param string       $action Default action is to increment the counter. This can be set to `decrement` or NULL to do nothing.
		 */
		public static function update_simultaneous_logins($username, $user = NULL, $action = 'increment')
		{
			if(!$username) return; // Nothing to do.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_update_simultaneous_logins', get_defined_vars());
			unset($__refs, $__v);

			$prefix            = 's2m_slm_'; // s2Member Transient prefix for all simultaneous login monitoring.
			$transient_entries = $prefix.md5('s2member_simultaneous_login_entries_for_'.strtolower((string)$username));

			$timeout     = $GLOBALS['WS_PLUGIN__']['s2member']['o']['max_simultaneous_logins_timeout'];
			$timeout_ago = strtotime('-'.$timeout); // e.g., 30 minutes ago.

			$entries = (is_array($entries = get_transient($transient_entries))) ? $entries : array();
			foreach($entries as $_entry => $_time /* Auto-expire entries, based on time. */)
				if($_time < $timeout_ago) unset($entries[$_entry]);

			$total_entries = count($entries); // May need this below.

			if($action === 'increment') $entries[] = time(); // New entry.
			else if($action === 'decrement') array_pop($entries); // Remove last entry.
			else if($action === 'timer') // Update time on last entry; or add a new entry.
				$entries[(($total_entries) ? $total_entries - 1 : 0)] = time();

			set_transient($transient_entries, $entries, strtotime('+'.$timeout) - time());

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_after_update_simultaneous_logins', get_defined_vars());
			unset($__refs, $__v);
		}
	}
}