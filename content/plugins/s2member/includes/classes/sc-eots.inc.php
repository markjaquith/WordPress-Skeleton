<?php
/**
 * Shortcode `[s2Eot /]`.
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
 * @package s2Member\s2Eot
 * @since 150713
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_sc_eots'))
{
	/**
	 * Shortcode `[s2Eot /]`.
	 *
	 * @package s2Member\s2Eot
	 * @since 150713
	 */
	class c_ws_plugin__s2member_sc_eots
	{
		/**
		 * Handles the Shortcode for: `[s2Eot /]`.
		 *
		 * @package s2Member\s2Eot
		 * @since 150713
		 *
		 * @attaches-to ``add_shortcode('s2Eot');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Return-value of inner routine.
		 */
		public static function sc_eot_details($attr = array(), $content = '', $shortcode = '')
		{
			return c_ws_plugin__s2member_sc_eots_in::sc_eot_details($attr, $content, $shortcode);
		}
	}
}
