<?php
/**
 * Shortcode `[s2If /]`.
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
 * @package s2Member\s2If
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_sc_if_conds'))
{
	/**
	 * Shortcode `[s2If /]`.
	 *
	 * @package s2Member\s2If
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_sc_if_conds
	{
		/**
		 * Handles the Shortcode for: `[s2If /]`.
		 *
		 * These Shortcodes are also safe to use on a Multisite Blog Farm.
		 *
		 * Is Multisite Networking enabled? Please keep the following in mind.
		 * ``current_user_can()``, will ALWAYS return true for a Super Admin!
		 *   *(this can be confusing when testing conditionals)*.
		 *
		 * If you're running a Multisite Blog Farm, you can Filter this array:
		 *   `ws_plugin__s2member_sc_if_conditionals_blog_farm_safe`
		 *   ``$blog_farm_safe``
		 *
		 * @package s2Member\s2If
		 * @since 3.5
		 *
		 * @attaches-to ``add_shortcode('s2If')`` + _s2If, __s2If, ___s2If for nesting.
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Return-value of inner routine.
		 */
		public static function sc_if_conditionals($attr = array(), $content = '', $shortcode = '')
		{
			return c_ws_plugin__s2member_sc_if_conds_in::sc_if_conditionals($attr, $content, $shortcode);
		}
	}
}