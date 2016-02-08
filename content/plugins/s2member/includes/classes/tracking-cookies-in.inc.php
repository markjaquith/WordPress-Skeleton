<?php
/**
 * Tracking Cookies (inner processing routines).
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

if(!class_exists("c_ws_plugin__s2member_tracking_cookies_in"))
{
	/**
	 * Tracking Cookies (inner processing routines).
	 *
	 * @package s2Member\Tracking
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_tracking_cookies_in
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
			do_action("ws_plugin__s2member_before_delete_tracking_cookie", get_defined_vars());

			if(!empty($_GET["s2member_delete_tracking_cookie"])) // Deletes cookie.
			{
				setcookie("s2member_tracking", "", time() + 31556926, COOKIEPATH, COOKIE_DOMAIN);
				setcookie("s2member_tracking", "", time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);

				do_action("ws_plugin__s2member_during_delete_tracking_cookie", get_defined_vars());

				@ini_set("zlib.output_compression", 0);
				if(function_exists("apache_setenv"))
					@apache_setenv("no-gzip", "1");

				status_header(200); // Send a 200 OK status header.

				header("Content-Type: image/png"); // Content-Type image/png for 1px transparency.

				while(@ob_end_clean()) ; // Clean any existing output buffers.

				exit (file_get_contents(dirname(dirname(dirname(__FILE__)))."/images/trans-1px.png"));
			}
			do_action("ws_plugin__s2member_after_delete_tracking_cookie", get_defined_vars());
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
			do_action("ws_plugin__s2member_before_delete_sp_tracking_cookie", get_defined_vars());

			if(!empty($_GET["s2member_delete_sp_tracking_cookie"])) // Deletes cookie.
			{
				setcookie("s2member_sp_tracking", "", time() + 31556926, COOKIEPATH, COOKIE_DOMAIN);
				setcookie("s2member_sp_tracking", "", time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);

				do_action("ws_plugin__s2member_during_delete_sp_tracking_cookie", get_defined_vars());

				@ini_set("zlib.output_compression", 0);
				if(function_exists("apache_setenv"))
					@apache_setenv("no-gzip", "1");

				status_header(200); // Send a 200 OK status header.

				header("Content-Type: image/png"); // Content-Type image/png for 1px transparency.

				while(@ob_end_clean()) ; // Clean any existing output buffers.

				exit (file_get_contents(dirname(dirname(dirname(__FILE__)))."/images/trans-1px.png"));
			}
			do_action("ws_plugin__s2member_after_delete_sp_tracking_cookie", get_defined_vars());
		}
	}
}