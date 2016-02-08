<?php
/**
 * WordPress footer code.
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
 * @package s2Member\WP_Footer
 * @since 110524RC
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_wp_footer'))
{
	/**
	 * WordPress footer code.
	 *
	 * @package s2Member\WP_Footer
	 * @since 110524RC
	 */
	class c_ws_plugin__s2member_wp_footer
	{
		/**
		 * Generates footer code, when/if configured.
		 *
		 * @package s2Member\WP_Footer
		 * @since 110524RC
		 *
		 * @return null
		 */
		public static function wp_footer_code()
		{
			do_action('ws_plugin__s2member_before_wp_footer_code', get_defined_vars());

			if(($code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['wp_footer_code']))
			{
				if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site())
				{
					echo do_shortcode($code)."\n";
				}
				else // Otherwise, safe to allow PHP code.
				{
					echo do_shortcode(c_ws_plugin__s2member_utilities::evl($code));
				}
			}
			do_action('ws_plugin__s2member_after_wp_footer_code', get_defined_vars());
		}
	}
}