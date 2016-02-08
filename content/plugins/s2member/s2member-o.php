<?php
/**
 * WordPress with s2Member only.
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
 * @package s2Member
 * @since 110912
 */
define ('_WS_PLUGIN__S2MEMBER_ONLY', TRUE);

include_once dirname(__FILE__).'/includes/classes/utils-s2o.inc.php';

if(($ws_plugin__s2member_o['wp_dir'] = c_ws_plugin__s2member_utils_s2o::wp_dir(dirname(__FILE__), dirname($_SERVER['SCRIPT_FILENAME']))))
{
	if(($ws_plugin__s2member_o['wp_settings_as'] = c_ws_plugin__s2member_utils_s2o::wp_settings_as($ws_plugin__s2member_o['wp_dir'], __FILE__)))
	{
		/**
		 * Short initialization mode for WordPress.
		 *
		 * @package s2Member
		 * @since 110912
		 *
		 * @var bool
		 */
		define ('SHORTINIT', TRUE);

		/**
		 * Flag indicating only s2Member is being loaded.
		 *
		 * @package s2Member
		 * @since 110912
		 *
		 * @var bool
		 */
		define ('WS_PLUGIN__S2MEMBER_ONLY', TRUE);

		/*
		Load WordPress.
		*/
		require($ws_plugin__s2member_o['wp_dir'].'/wp-load.php');
		eval ('?>'.$ws_plugin__s2member_o['wp_settings_as']);
	}
	else // Else fallback on full WordPress.
		require($ws_plugin__s2member_o['wp_dir'].'/wp-load.php');
}
unset($ws_plugin__s2member_o);