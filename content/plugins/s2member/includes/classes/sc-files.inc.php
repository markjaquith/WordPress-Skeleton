<?php
/**
* Shortcode `[s2File /]`.
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
* @package s2Member\s2File
* @since 110926
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_sc_files"))
	{
		/**
		* Shortcode `[s2File /]`.
		*
		* @package s2Member\s2File
		* @since 110926
		*/
		class c_ws_plugin__s2member_sc_files
			{
				/**
				* Handles the Shortcode for: `[s2File /]`.
				*
				* @package s2Member\s2File
				* @since 110926
				*
				* @attaches-to ``add_shortcode("s2File");``
				*
				* @param array $attr An array of Attributes.
				* @param string $content Content inside the Shortcode.
				* @param string $shortcode The actual Shortcode name itself.
				* @return string Value of the requested File Download URL, or null on failure.
				*/
				public static function sc_get_file($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						return c_ws_plugin__s2member_sc_files_in::sc_get_file($attr, $content, $shortcode);
					}
				/**
				* Handles the Shortcode for: `[s2Stream /]`.
				*
				* @package s2Member\s2File
				* @since 130119
				*
				* @attaches-to ``add_shortcode("s2Stream");``
				*
				* @param array $attr An array of Attributes.
				* @param string $content Content inside the Shortcode.
				* @param string $shortcode The actual Shortcode name itself.
				* @return string HTML markup that produces an audio/video stream for a specific player.
				*/
				public static function sc_get_stream($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						return c_ws_plugin__s2member_sc_files_in::sc_get_stream($attr, $content, $shortcode);
					}
			}
	}
