<?php
/**
 * s2Member Profile modifications.
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
 * @package s2Member\Profiles
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_profile_mods'))
{
	/**
	 * s2Member Profile modifications.
	 *
	 * @package s2Member\Profiles
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_profile_mods
	{
		/**
		 * Handles Profile modifications.
		 *
		 * @package s2Member\Profiles
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('init');``
		 */
		public static function handle_profile_modifications()
		{
			if(!empty($_POST['ws_plugin__s2member_profile_save']))
				c_ws_plugin__s2member_profile_mods_in::handle_profile_modifications();
		}
	}
}