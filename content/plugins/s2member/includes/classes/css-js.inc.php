<?php
/**
 * CSS/JS loading handlers for s2Member.
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
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_css_js"))
{
	/**
	 * CSS/JS loading handlers for s2Member.
	 *
	 * @package s2Member\CSS_JS
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_css_js
	{
		/**
		 * Outputs CSS for theme integration.
		 *
		 * @package s2Member\CSS_JS
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 */
		public static function css()
		{
			if(!empty($_GET["ws_plugin__s2member_css"]))
				c_ws_plugin__s2member_css_js_in::css();
		}

		/**
		 * Outputs JS for theme integration.
		 *
		 * @package s2Member\CSS_JS
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("init");``
		 */
		public static function js_w_globals()
		{
			if(!empty($_GET["ws_plugin__s2member_js_w_globals"]))
				c_ws_plugin__s2member_css_js_in::js_w_globals();
		}
	}
}