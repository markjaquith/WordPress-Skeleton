<?php
/**
 * Access CAP Times.
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
 * @package s2Member\CCAPS
 * @since 140514
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_access_cap_times'))
{
	/**
	 * Access CAP Times.
	 *
	 * @package s2Member\CCAPS
	 * @since 140514
	 */
	class c_ws_plugin__s2member_access_cap_times
	{
		/**
		 * @var string Current log time increment.
		 */
		protected static $log_time = NULL;

		/**
		 * @var array Previous array of user CAPS.
		 *    For internal use only.
		 */
		protected static $prev_caps_by_user = array();

		/**
		 * Get user caps before udpate.
		 *
		 * @package s2Member\CCAPS
		 * @since 140514
		 *
		 * @attaches-to ``add_action('add_user_meta')`` (indirectly)
		 * @attaches-to ``add_action('update_user_meta')``
		 *
		 * @param integer $meta_id Meta row ID in database.
		 * @param integer $object_id User ID.
		 * @param string  $meta_key Meta key.
		 * @param mixed   $meta_value Meta value.
		 */
		public static function get_user_caps_before_update($meta_id, $object_id, $meta_key, $meta_value)
		{
			$wpdb = $GLOBALS['wpdb'];
			/** @var $wpdb \wpdb For IDEs. */

			if(strpos($meta_key, 'capabilities') === FALSE || $meta_key !== $wpdb->get_blog_prefix().'capabilities')
				return; // Not updating caps.

			$user_id = (integer)$object_id;
			$user    = new WP_User($user_id);
			if(!$user->ID || !$user->exists())
				return; // Not a valid user.

			self::$prev_caps_by_user[$user_id] = $user->caps;
		}

		/**
		 * Get user caps before udpate.
		 *
		 * @package s2Member\CCAPS
		 * @since 140514
		 *
		 * @attaches-to ``add_action('add_user_meta')``
		 *
		 * @param integer $object_id User ID.
		 * @param string  $meta_key Meta key.
		 * @param mixed   $meta_value Meta value.
		 */
		public static function get_user_caps_before_update_on_add($object_id, $meta_key, $meta_value)
		{
			self::get_user_caps_before_update(0, $object_id, $meta_key, $meta_value);
		}

		/**
		 * Logs access capability times.
		 *
		 * @package s2Member\CCAPS
		 * @since 140514
		 *
		 * @attaches-to ``add_action('added_user_meta')``
		 * @attaches-to ``add_action('updated_user_meta')``
		 * @attaches-to ``add_action('deleted_user_meta')`` (indirectly)
		 *
		 * @param integer $meta_id Meta row ID in database.
		 * @param integer $object_id User ID.
		 * @param string  $meta_key Meta key.
		 * @param mixed   $meta_value Meta value.
		 */
		public static function log_access_cap_times($meta_id, $object_id, $meta_key, $meta_value)
		{
			$wpdb = $GLOBALS['wpdb'];
			/** @var $wpdb \wpdb For IDEs. */

			if(strpos($meta_key, 'capabilities') === FALSE || $meta_key !== $wpdb->get_blog_prefix().'capabilities')
				return; // Not updating caps.

			$user_id = (integer)$object_id;
			$user    = new WP_User($user_id);
			if(!$user->ID || !$user->exists())
				return; // Not a valid user.

			$caps['prev']            = !empty(self::$prev_caps_by_user[$user_id]) ? self::$prev_caps_by_user[$user_id] : array();
			self::$prev_caps_by_user = array(); // Reset this in case `get_user_caps_before_update()` doesn't run somehow.
			$caps['now']             = is_array($meta_value) ? $meta_value : array();
			$role_objects            = $GLOBALS['wp_roles']->role_objects;

			foreach($caps as &$_caps_prev_now)
			{
				foreach(array_intersect(array_keys($_caps_prev_now), array_keys($role_objects)) as $_role)
					if($_caps_prev_now[$_role]) // If the cap (i.e., the role) is enabled; merge its caps.
						$_caps_prev_now = array_merge($_caps_prev_now, $role_objects[$_role]->capabilities);

				$_s2_caps_prev_now = array();
				foreach($_caps_prev_now as $_cap => $_enabled)
					if(strpos($_cap, 'access_s2member_') === 0)
						$_s2_caps_prev_now[substr($_cap, 16)] = $_enabled;
				$_caps_prev_now = $_s2_caps_prev_now;
			}
			unset($_s2_caps_prev_now, $_caps_prev_now, $_role, $_cap, $_enabled);

			$ac_times = get_user_option('s2member_access_cap_times', $user_id);
			if(!is_array($ac_times)) $ac_times = array();

			if(!isset(self::$log_time))
				self::$log_time = (float)time();

			foreach($caps['prev'] as $_cap => $_was_enabled)
				if($_was_enabled && empty($caps['now'][$_cap]))
					$ac_times[number_format((self::$log_time += .0001), 4, '.', '')] = '-'.$_cap;
			unset($_cap, $_was_enabled);

			foreach($caps['now'] as $_cap => $_now_enabled)
				if($_now_enabled && empty($caps['prev'][$_cap]))
					$ac_times[number_format((self::$log_time += .0001), 4, '.', '')] = $_cap;
			unset($_cap, $_now_enabled);

			update_user_option($user_id, 's2member_access_cap_times', $ac_times);
		}

		/**
		 * Logs access capability times.
		 *
		 * @package s2Member\CCAPS
		 * @since 140514
		 *
		 * @attaches-to ``add_action('deleted_user_meta')``
		 *
		 * @param array   $meta_ids Meta row ID in database.
		 * @param integer $object_id User ID.
		 * @param string  $meta_key Meta key.
		 */
		public static function log_access_cap_times_on_delete($meta_ids, $object_id, $meta_key)
		{
			$wpdb = $GLOBALS['wpdb'];
			/** @var $wpdb \wpdb For IDEs. */

			if(strpos($meta_key, 'capabilities') === FALSE || $meta_key !== $wpdb->get_blog_prefix().'capabilities')
				return; // Not updating caps.

			if(!is_array($meta_ids) || !$meta_ids)
				return; // Nothing to do.

			if(count($meta_ids) > 50)
				if(function_exists('set_time_limit'))
					@set_time_limit(900);

			$user_ids = $wpdb->get_col("SELECT DISTINCT `user_id` FROM `".$wpdb->usermeta."` WHERE `umeta_id` IN('".implode("','", $meta_ids)."')");

			if(count($user_ids) > 50)
				if(function_exists('set_time_limit'))
					@set_time_limit(900);

			foreach($user_ids as $_user_id)
				self::log_access_cap_times(0, $_user_id, $meta_key, array());
			unset($_user_id);
		}

		/**
		 * Gets access capability times.
		 *
		 * @package s2Member\CCAPS
		 * @since 140514
		 *
		 * @param integer $user_id WP User ID.
		 * @param array   $access_caps Optional. If not passed, this returns all times for all caps.
		 *    If passed, please pass an array of specific access capabilities to get the times for.
		 *    If removal times are desired, you should add a `-` prefix.
		 *    e.g., `array('ccap_music','level2','-ccap_video')`
		 *
		 * @return array An array of all access capability times.
		 *    Keys are UTC timestamps (w/ microtime precision), values are the capabilities (including `-` prefixed removals).
		 *    e.g., `array('1234567890.0001' => 'ccap_music', '1234567890.0002' => 'level2', '1234567890.0003' => '-ccap_video')`
		 */
		public static function get_access_cap_times($user_id, $access_caps = array())
		{
			$ac_times = array();
			if(($user_id = (integer)$user_id))
			{
				$ac_times = get_user_option('s2member_access_cap_times', $user_id);
				if(!is_array($ac_times)) $ac_times = array();

				/* ------- Begin back compat. with `s2member_paid_registration_times`. */

				// $update_ac_times = empty($ac_times) ? FALSE : TRUE;
				$ac_times_min = !empty($ac_times) ? min(array_keys($ac_times)) : 0;
				if(($r_time = c_ws_plugin__s2member_registration_times::registration_time($user_id)) && (empty($ac_times_min) || $r_time < $ac_times_min))
					$ac_times[number_format(($r_time += .0001), 4, '.', '')] = 'level0';

				if(is_array($pr_times = get_user_option('s2member_paid_registration_times', $user_id)))
				{
					$role_objects = $GLOBALS['wp_roles']->role_objects;
					foreach($pr_times as $_level => $_time)
						if(isset($role_objects['s2member_'.$_level]) && (empty($ac_times_min) || $_time < $ac_times_min))
							foreach(array_keys($role_objects['s2member_'.$_level]->capabilities) as $_cap)
								if(strpos($_cap, 'access_s2member_') === 0)
									$ac_times[number_format(($_time += .0001), 4, '.', '')] = substr($_cap, 16);
					unset($_level, $_time, $_cap);
				}
				/* ------- End back compat. with `s2member_paid_registration_times`. */

				if($access_caps)
					$ac_times = array_intersect($ac_times, (array)$access_caps);

				ksort($ac_times, SORT_NUMERIC);

				//if($update_ac_times)
				//	update_user_option($user_id, 's2member_access_cap_times', $ac_times);
			}
			return apply_filters('ws_plugin__s2member_get_access_cap_times', $ac_times, get_defined_vars());
		}
	}
}