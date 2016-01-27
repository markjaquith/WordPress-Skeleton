<?php
/**
 * s2Member's self re-activation routines.
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
 * @package s2Member\Installation
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_check_activation"))
{
	/**
	 * s2Member's self re-activation routines.
	 *
	 * @package s2Member\Installation
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_check_activation
	{
		/**
		 * Checks for existing installs that are NOT yet re-activated.
		 *
		 * @package s2Member\Installation
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("admin_init");``
		 */
		public static function check()
		{
			if(!($v = get_option("ws_plugin__s2member_activated_version")) || !version_compare($v, WS_PLUGIN__S2MEMBER_VERSION, ">="))
				c_ws_plugin__s2member_installation::activate("version");

			else if(is_multisite() && is_main_site() && (!($mms_v = get_option("ws_plugin__s2member_activated_mms_version")) || !version_compare($mms_v, WS_PLUGIN__S2MEMBER_VERSION, ">=")))
				c_ws_plugin__s2member_installation::activate("mms_version");

			else if(!($l = (int)get_option("ws_plugin__s2member_activated_levels")) || $l !== $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"])
				c_ws_plugin__s2member_installation::activate("levels");
		}
	}
}