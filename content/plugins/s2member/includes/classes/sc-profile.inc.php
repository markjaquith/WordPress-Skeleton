<?php
/**
* Shortcode for `[s2Member-Profile /]`.
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
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_sc_profile"))
	{
		/**
		* Shortcode for `[s2Member-Profile /]`.
		*
		* @package s2Member\Profiles
		* @since 3.5
		*/
		class c_ws_plugin__s2member_sc_profile
			{
				/**
				* Handles the Shortcode for: `[s2Member-Profile /]`.
				*
				* @package s2Member\Profiles
				* @since 3.5
				*
				* @attaches-to ``add_shortcode("s2Member-Profile");``
				*
				* @param array $attr An array of Attributes.
				* @param string $content Content inside the Shortcode.
				* @param string $shortcode The actual Shortcode name itself.
				* @return inner Return-value of inner routine.
				*/
				public static function sc_profile ($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						return c_ws_plugin__s2member_sc_profile_in::sc_profile ($attr, $content, $shortcode);
					}
			}
	}
