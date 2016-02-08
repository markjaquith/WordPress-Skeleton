<?php
/**
 * Cron routines handled by s2Member (inner processing routines).
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
 * @package s2Member\Cron_Jobs
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_cron_jobs_in'))
{
	/**
	 * Cron routines handled by s2Member (inner processing routines).
	 *
	 * @package s2Member\Cron_Jobs
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_cron_jobs_in
	{
		/**
		 * Extends WP-Cron schedules to support 10 minute intervals.
		 *
		 * @package s2Member\Cron_Jobs
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('cron_schedules');``
		 *
		 * @param array $schedules Expects an array of WP_Cron schedules passed in by the Filter.
		 *
		 * @return array Array of WP_Cron schedules after having added a 10 minute cycle.
		 */
		public static function extend_cron_schedules($schedules = array())
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_extend_cron_schedules', get_defined_vars());
			unset($__refs, $__v);

			$array = array('every10m' => array('interval' => 600, 'display' => 'Every 10 Minutes'));

			return apply_filters('ws_plugin__s2member_extend_cron_schedules', array_merge($array, $schedules), get_defined_vars());
		}

		/**
		 * Allows the Auto-EOT Sytem to be processed through a server-side Cron Job.
		 *
		 * @package s2Member\Cron_Jobs
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function auto_eot_system_via_cron()
		{
			do_action('ws_plugin__s2member_before_auto_eot_system_via_cron', get_defined_vars());

			if(!empty($_GET['s2member_auto_eot_system_via_cron']))
			{
				if($GLOBALS['WS_PLUGIN__']['s2member']['o']['auto_eot_system_enabled'])
				{
					c_ws_plugin__s2member_auto_eots::auto_eot_system(); // Process.
					do_action('ws_plugin__s2member_during_auto_eot_system_via_cron', get_defined_vars());
				}
				exit(); // Clean exit.
			}
			do_action('ws_plugin__s2member_after_auto_eot_system_via_cron', get_defined_vars());
		}
	}
}
