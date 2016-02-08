<?php
/**
 * CSS/JS loading handlers for s2Member (inner processing routines).
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
 * @package s2Member\CSS_JS
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_css_js_in"))
{
	/**
	 * CSS/JS loading handlers for s2Member (inner processing routines).
	 *
	 * @package s2Member\CSS_JS
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_css_js_in
	{
		/**
		 * Outputs CSS for theme integration.
		 *
		 * @package s2Member\CSS_JS
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 *
		 * @return null Or exits script execution after loading CSS.
		 */
		public static function css()
		{
			do_action("ws_plugin__s2member_before_css", get_defined_vars());

			if(!empty($_GET["ws_plugin__s2member_css"]))
			{
				status_header(200); // 200 OK status header.

				header("Content-Type: text/css; charset=UTF-8");
				header("Expires: ".gmdate("D, d M Y H:i:s", strtotime("+1 week"))." GMT");
				header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
				header("Cache-Control: max-age=604800");
				header("Pragma: public");

				while(@ob_end_clean()) ; // Clean output buffers.

				$u = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"];
				$i = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]."/images";

				ob_start("c_ws_plugin__s2member_utils_css::compress_css");

				include_once dirname(dirname(__FILE__))."/s2member.css";

				do_action("ws_plugin__s2member_during_css", get_defined_vars());

				exit(); // Clean exit.
			}
			do_action("ws_plugin__s2member_after_css", get_defined_vars());
		}

		/**
		 * Outputs JS for theme integration.
		 *
		 * Be sure s2Member's API Constants are already defined before firing this.
		 *
		 * @package s2Member\CSS_JS
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 *
		 * @return null Or exits script execution after loading JS w/Globals.
		 */
		public static function js_w_globals()
		{
			do_action("ws_plugin__s2member_before_js_w_globals", get_defined_vars());

			if(!empty($_GET["ws_plugin__s2member_js_w_globals"]))
			{
				status_header(200); // 200 OK status header.

				header("Content-Type: application/x-javascript; charset=UTF-8");
				header("Expires: ".gmdate("D, d M Y H:i:s", strtotime("+1 week"))." GMT");
				header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
				header("Cache-Control: max-age=604800");
				header("Pragma: public");

				while(@ob_end_clean()) ; // Clean output buffers.

				include_once dirname(dirname(__FILE__))."/jquery/jquery.sprintf/jquery.sprintf-min.js";

				echo "\n"; // Add a line break before writing JavaScript Globals to file.

				echo "var S2MEMBER_VERSION = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_VERSION)."',";

				echo "S2MEMBER_CURRENT_USER_LOGIN_COUNTER = ".S2MEMBER_CURRENT_USER_LOGIN_COUNTER.",";

				echo "S2MEMBER_CURRENT_USER_IS_LOGGED_IN = ".((S2MEMBER_CURRENT_USER_IS_LOGGED_IN) ? "true" : "false").",";
				echo "S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER = ".((S2MEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER) ? "true" : "false").",";

				echo "S2MEMBER_CURRENT_USER_ACCESS_LEVEL = ".S2MEMBER_CURRENT_USER_ACCESS_LEVEL.",";
				echo "S2MEMBER_CURRENT_USER_ACCESS_LABEL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_ACCESS_LABEL)."',";

				echo "S2MEMBER_CURRENT_USER_SUBSCR_ID = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_SUBSCR_ID)."',";
				echo "S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_SUBSCR_OR_WP_ID)."',";
				echo "S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_SUBSCR_GATEWAY)."',";
				echo "S2MEMBER_CURRENT_USER_CUSTOM = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_CUSTOM)."',";

				echo "S2MEMBER_CURRENT_USER_REGISTRATION_TIME = ".S2MEMBER_CURRENT_USER_REGISTRATION_TIME.",";
				echo "S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME = ".S2MEMBER_CURRENT_USER_PAID_REGISTRATION_TIME.",";

				echo "S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS = ".S2MEMBER_CURRENT_USER_PAID_REGISTRATION_DAYS.",";
				echo "S2MEMBER_CURRENT_USER_REGISTRATION_DAYS = ".S2MEMBER_CURRENT_USER_REGISTRATION_DAYS.",";

				echo "S2MEMBER_CURRENT_USER_DISPLAY_NAME = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_DISPLAY_NAME)."',";
				echo "S2MEMBER_CURRENT_USER_FIRST_NAME = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_FIRST_NAME)."',";
				echo "S2MEMBER_CURRENT_USER_LAST_NAME = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_LAST_NAME)."',";

				echo "S2MEMBER_CURRENT_USER_LOGIN = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_LOGIN)."',";
				echo "S2MEMBER_CURRENT_USER_EMAIL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_EMAIL)."',";
				echo "S2MEMBER_CURRENT_USER_IP = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_IP)."',";
				echo "S2MEMBER_CURRENT_USER_REGISTRATION_IP = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_REGISTRATION_IP)."',";

				echo "S2MEMBER_CURRENT_USER_ID = ".S2MEMBER_CURRENT_USER_ID.",";
				echo "S2MEMBER_CURRENT_USER_FIELDS = ".S2MEMBER_CURRENT_USER_FIELDS.",";

				echo "S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED = ".S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED.",";
				echo "S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED = ".((S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_IS_UNLIMITED) ? "true" : "false").",";
				echo "S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY = ".S2MEMBER_CURRENT_USER_DOWNLOADS_CURRENTLY.",";
				echo "S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS = ".S2MEMBER_CURRENT_USER_DOWNLOADS_ALLOWED_DAYS.",";

				echo "S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID = ".S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_ID.",";
				echo "S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID = ".S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_ID.",";
				echo "S2MEMBER_LOGIN_WELCOME_PAGE_ID = ".S2MEMBER_LOGIN_WELCOME_PAGE_ID.",";

				echo "S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL)."',";
				echo "S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_FILE_DOWNLOAD_LIMIT_EXCEEDED_PAGE_URL)."',";
				echo "S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_MEMBERSHIP_OPTIONS_PAGE_URL)."',";
				echo "S2MEMBER_LOGIN_WELCOME_PAGE_URL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_LOGIN_WELCOME_PAGE_URL)."',";
				echo "S2MEMBER_LOGOUT_PAGE_URL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_LOGOUT_PAGE_URL)."',";
				echo "S2MEMBER_LOGIN_PAGE_URL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_LOGIN_PAGE_URL)."',";

				for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
				{
					if(defined(($S2MEMBER_LEVELn_LABEL = "S2MEMBER_LEVEL".$n."_LABEL")))
						echo $S2MEMBER_LEVELn_LABEL." = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(constant($S2MEMBER_LEVELn_LABEL))."',";
				}
				for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
				{
					if(defined(($S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED = "S2MEMBER_LEVEL".$n."_FILE_DOWNLOADS_ALLOWED")))
						echo $S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED." = ".constant($S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED).",";
				}
				for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
				{
					if(defined(($S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS = "S2MEMBER_LEVEL".$n."_FILE_DOWNLOADS_ALLOWED_DAYS")))
						echo $S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS." = ".constant($S2MEMBER_LEVELn_FILE_DOWNLOADS_ALLOWED_DAYS).",";
				}
				echo "S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_FILE_DOWNLOAD_INLINE_EXTENSIONS)."',";

				echo "S2MEMBER_REG_EMAIL_FROM_NAME = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_REG_EMAIL_FROM_NAME)."',";
				echo "S2MEMBER_REG_EMAIL_FROM_EMAIL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_REG_EMAIL_FROM_EMAIL)."',";

				echo "S2MEMBER_PAYPAL_NOTIFY_URL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_PAYPAL_NOTIFY_URL)."',";
				echo "S2MEMBER_PAYPAL_RETURN_URL = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_PAYPAL_RETURN_URL)."',";

				echo "S2MEMBER_PAYPAL_BUSINESS = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_PAYPAL_BUSINESS)."',";
				echo "S2MEMBER_PAYPAL_ENDPOINT = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_PAYPAL_ENDPOINT)."',";
				echo "S2MEMBER_PAYPAL_API_ENDPOINT = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_PAYPAL_API_ENDPOINT)."',";

				echo "S2MEMBER_VALUE_FOR_PP_INV = Math.round (new Date ().getTime ()) + '~".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_IP)."',";
				echo "S2MEMBER_VALUE_FOR_PP_INV_GEN = s2member_value_for_pp_inv_gen = function(){ var invoice = '', formatSeed = function(seed, reqWidth) { seed = parseInt(seed, 10).toString (16); if (reqWidth < seed.length) return seed.slice (seed.length - reqWidth); else if (reqWidth > seed.length) return Array(1 + (reqWidth - seed.length)).join ('0') + seed; return seed; }; if (typeof S2MEMBER_VALUE_FOR_PP_INV_GEN_UNIQUE_SEED === 'undefined') S2MEMBER_VALUE_FOR_PP_INV_GEN_UNIQUE_SEED = Math.floor (Math.random () * 0x75bcd15); S2MEMBER_VALUE_FOR_PP_INV_GEN_UNIQUE_SEED++; invoice = formatSeed(parseInt(new Date ().getTime () / 1000, 10), 8); invoice += formatSeed(S2MEMBER_VALUE_FOR_PP_INV_GEN_UNIQUE_SEED, 5); invoice += '~' + S2MEMBER_CURRENT_USER_IP; return invoice; },";

				echo "S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0 = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON0)."',";
				echo "S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0 = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS0)."',";

				echo "S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1 = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_VALUE_FOR_PP_ON1)."',";
				echo "S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1 = '".c_ws_plugin__s2member_utils_strings::esc_js_sq(S2MEMBER_CURRENT_USER_VALUE_FOR_PP_OS1)."';";

				$u = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"];
				$i = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]."/images";

				echo "\n"; // Add a line break before inclusion.
				include_once dirname(dirname(__FILE__))."/s2member-min.js";

				do_action("ws_plugin__s2member_during_js_w_globals", get_defined_vars());

				exit(); // Clean exit.
			}
			do_action("ws_plugin__s2member_after_js_w_globals", get_defined_vars());
		}
	}
}