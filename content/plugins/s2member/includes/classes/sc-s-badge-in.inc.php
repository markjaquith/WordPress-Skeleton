<?php
/**
 * Shortcode for `[s2Member-Security-Badge /]` (inner processing routines).
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
 * @package s2Member\Security_Badges
 * @since 110524RC
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_sc_s_badge_in'))
{
	/**
	 * Shortcode for `[s2Member-Security-Badge /]` (inner processing routines).
	 *
	 * @package s2Member\Security_Badges
	 * @since 110524RC
	 */
	class c_ws_plugin__s2member_sc_s_badge_in
	{
		/**
		 * Handles the Shortcode for: `[s2Member-Security-Badge /]`.
		 *
		 * @package s2Member\Security_Badges
		 * @since 110524RC
		 *
		 * @attaches-to ``add_shortcode('s2Member-Security-Badge');``
		 *
		 * @param array  $attr An array of Attributes.
		 * @param string $content Content inside the Shortcode.
		 * @param string $shortcode The actual Shortcode name itself.
		 *
		 * @return string Resulting Security Badge code; HTML markup.
		 */
		public static function sc_s_badge($attr = array(), $content = '', $shortcode = '')
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_sc_s_badge', get_defined_vars());
			unset($__refs, $__v);

			$attr = c_ws_plugin__s2member_utils_strings::trim_qts_deep((array)$attr);
			$attr = shortcode_atts(array('v' => '1'), $attr); // One attribute.
			$code = c_ws_plugin__s2member_utilities::s_badge_gen($attr['v'], FALSE, FALSE);

			return apply_filters('ws_plugin__s2member_sc_s_badge', $code, get_defined_vars());
		}
	}
}