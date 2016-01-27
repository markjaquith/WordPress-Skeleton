<?php
/**
 * Administrative CSS/JS for menu pages (inner processing routines).
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
 * @package s2Member\Admin_CSS_JS
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_admin_css_js_in"))
{
	/**
	 * Administrative CSS/JS for menu pages (inner processing routines).
	 *
	 * @package s2Member\Admin_CSS_JS
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_admin_css_js_in
	{
		/**
		 * Outputs the CSS for administrative menu pages.
		 *
		 * @package s2Member\Admin_CSS_JS
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 *
		 * @return null Or exits script execution after loading CSS.
		 */
		public static function menu_pages_css()
		{
			do_action("ws_plugin__s2member_before_menu_pages_css", get_defined_vars());

			if(!empty($_GET["ws_plugin__s2member_menu_pages_css"]) && is_user_logged_in() && current_user_can("create_users"))
			{
				status_header(200); // 200 OK status header.

				header("Content-Type: text/css; charset=UTF-8");
				header("Expires: ".gmdate("D, d M Y H:i:s", strtotime("-1 week"))." GMT");
				header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
				header("Cache-Control: no-cache, must-revalidate, max-age=0");
				header("Pragma: no-cache");

				while(@ob_end_clean()) ; // Clean any existing output buffers.

				$u = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"];
				$i = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]."/images";

				ob_start("c_ws_plugin__s2member_utils_css::compress_css");

				include_once dirname(dirname(__FILE__))."/menu-pages/menu-pages.css";

				echo "\n"; // Add a line break before inclusion of this file.

				@include_once dirname(dirname(__FILE__))."/menu-pages/menu-pages-s.css";

				do_action("ws_plugin__s2member_during_menu_pages_css", get_defined_vars());

				exit(); // Clean exit.
			}
			do_action("ws_plugin__s2member_after_menu_pages_css", get_defined_vars());
		}

		/**
		 * Outputs the JS for administrative menu pages.
		 *
		 * @package s2Member\Admin_CSS_JS
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 *
		 * @return null Or exits script execution after loading JS.
		 */
		public static function menu_pages_js()
		{
			do_action("ws_plugin__s2member_before_menu_pages_js", get_defined_vars());

			if(!empty($_GET["ws_plugin__s2member_menu_pages_js"]) && is_user_logged_in() && current_user_can("create_users"))
			{
				status_header(200); // 200 OK status header.

				header("Content-Type: application/x-javascript; charset=UTF-8");
				header("Expires: ".gmdate("D, d M Y H:i:s", strtotime("-1 week"))." GMT");
				header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
				header("Cache-Control: no-cache, must-revalidate, max-age=0");
				header("Pragma: no-cache");

				while(@ob_end_clean()) ; // Clean any existing output buffers.

				$u = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"];
				$i = $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]."/images";

				for($n = 0, $labels = ""; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
					$labels .= "labels['level".$n."'] = '".((!empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_label"])) ? str_replace('"', "", c_ws_plugin__s2member_utils_strings::esc_js_sq($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_label"], 3)) : "")."';";
				unset($n);

				include_once dirname(dirname(__FILE__))."/menu-pages/menu-pages-min.js";

				echo "\n"; // Add a line break before inclusion of this file.

				@include_once dirname(dirname(__FILE__))."/menu-pages/menu-pages-s-min.js";

				do_action("ws_plugin__s2member_during_menu_pages_js", get_defined_vars());

				exit(); // Clean exit.
			}
			do_action("ws_plugin__s2member_after_menu_pages_js", get_defined_vars());
		}
	}
}