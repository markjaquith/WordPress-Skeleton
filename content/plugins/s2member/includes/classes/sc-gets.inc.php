<?php
/**
 * Shortcode `[s2Get /]`.
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
 * @package s2Member\s2Get
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_sc_gets'))
{
	/**
	 * Shortcode `[s2Get /]`.
	 *
	 * @package s2Member\s2Get
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_sc_gets
	{
		/**
		 * Handles the Shortcode for: `[s2Get /]`.
		 *
		 * @package s2Member\s2Get
		 * @since 3.5
		 *
		 * @attaches-to ``add_shortcode('s2Get');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Return-value of inner routine.
		 */
		public static function sc_get_details($attr = array(), $content = '', $shortcode = '')
		{
			return c_ws_plugin__s2member_sc_gets_in::sc_get_details($attr, $content, $shortcode);
		}
	}
}
