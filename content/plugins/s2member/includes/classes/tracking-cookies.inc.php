<?php
/**
 * Tracking Cookies.
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
 * @package s2Member\Tracking
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_tracking_cookies"))
{
	/**
	 * Tracking Cookies.
	 *
	 * @package s2Member\Tracking
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_tracking_cookies
	{
		/**
		 * Deletes s2Member's temporary tracking cookie.
		 *
		 * @package s2Member\Tracking
		 * @since 110815
		 *
		 * @attaches-to ``add_action("init");``
		 */
		public static function delete_tracking_cookie()
		{
			if(!empty($_GET["s2member_delete_tracking_cookie"]))
				c_ws_plugin__s2member_tracking_cookies_in::delete_tracking_cookie();
		}

		/**
		 * Deletes s2Member's temporary tracking cookie.
		 *
		 * @package s2Member\Tracking
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 */
		public static function delete_sp_tracking_cookie()
		{
			if(!empty($_GET["s2member_delete_sp_tracking_cookie"]))
				c_ws_plugin__s2member_tracking_cookies_in::delete_sp_tracking_cookie();
		}
	}
}