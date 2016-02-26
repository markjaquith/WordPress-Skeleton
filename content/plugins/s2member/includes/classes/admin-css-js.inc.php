<?php
/**
 * Administrative CSS/JS for menu pages.
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
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_admin_css_js"))
{
	/**
	 * Administrative CSS/JS for menu pages.
	 *
	 * @package s2Member\Admin_CSS_JS
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_admin_css_js
	{
		/**
		 * Outputs the CSS for administrative menu pages.
		 *
		 * @package s2Member\Admin_CSS_JS
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 */
		public static function menu_pages_css()
		{
			if(!empty($_GET["ws_plugin__s2member_menu_pages_css"]))
				c_ws_plugin__s2member_admin_css_js_in::menu_pages_css();
		}

		/**
		 * Outputs the JS for administrative menu pages.
		 *
		 * @package s2Member\Admin_CSS_JS
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 */
		public static function menu_pages_js()
		{
			if(!empty($_GET["ws_plugin__s2member_menu_pages_js"]))
				c_ws_plugin__s2member_admin_css_js_in::menu_pages_js();
		}
	}
}