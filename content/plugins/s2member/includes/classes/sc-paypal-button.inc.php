<?php
/**
* Shortcode `[s2Member-PayPal-Button]`.
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
* @package s2Member\PayPal
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_sc_paypal_button"))
	{
		/**
		* Shortcode `[s2Member-PayPal-Button]`.
		*
		* @package s2Member\PayPal
		* @since 3.5
		*/
		class c_ws_plugin__s2member_sc_paypal_button
			{
				/**
				* Handles the Shortcode for: `[s2Member-PayPal-Button /]`.
				*
				* @package s2Member\PayPal
				* @since 3.5
				*
				* @attaches-to ``add_shortcode("s2Member-PayPal-Button");``
				*
				* @param array $attr An array of Attributes.
				* @param string $content Content inside the Shortcode.
				* @param string $shortcode The actual Shortcode name itself.
				* @return inner Return-value of inner routine.
				*/
				public static function sc_paypal_button ($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						return c_ws_plugin__s2member_sc_paypal_button_in::sc_paypal_button ($attr, $content, $shortcode);
					}
			}
	}
