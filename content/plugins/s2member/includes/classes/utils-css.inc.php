<?php
/**
* CSS utilities.
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
* @package s2Member\Utilities
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_utils_css"))
	{
		/**
		* CSS utilities.
		*
		* @package s2Member\Utilities
		* @since 3.5
		*/
		class c_ws_plugin__s2member_utils_css
			{
				/**
				* Handles CSS compression.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param string $css A string of CSS.
				* @return string String of CSS, after compression.
				*/
				public static function compress_css ($css = FALSE)
					{
						$c6 = "/(\:#| #)([A-Z0-9]{6})/i";
						$css = preg_replace ("/\/\*(.*?)\*\//s", "", $css);
						$css = preg_replace ("/[\r\n\t]+/", "", $css);
						$css = preg_replace ("/ {2,}/", " ", $css);
						$css = preg_replace ("/ , | ,|, /", ",", $css);
						$css = preg_replace ("/ \> | \>|\> /", ">", $css);
						$css = preg_replace ("/\[ /", "[", $css);
						$css = preg_replace ("/ \]/", "]", $css);
						$css = preg_replace ("/ \!\= | \!\=|\!\= /", "!=", $css);
						$css = preg_replace ("/ \|\= | \|\=|\|\= /", "|=", $css);
						$css = preg_replace ("/ \^\= | \^\=|\^\= /", "^=", $css);
						$css = preg_replace ("/ \$\= | \$\=|\$\= /", "$=", $css);
						$css = preg_replace ("/ \*\= | \*\=|\*\= /", "*=", $css);
						$css = preg_replace ("/ ~\= | ~\=|~\= /", "~=", $css);
						$css = preg_replace ("/ \= | \=|\= /", "=", $css);
						$css = preg_replace ("/ \+ | \+|\+ /", "+", $css);
						$css = preg_replace ("/ ~ | ~|~ /", "~", $css);
						$css = preg_replace ("/ \{ | \{|\{ /", "{", $css);
						$css = preg_replace ("/ \} | \}|\} /", "}", $css);
						$css = preg_replace ("/ \: | \:|\: /", ":", $css);
						$css = preg_replace ("/ ; | ;|; /", ";", $css);
						$css = preg_replace ("/;\}/", "}", $css);

						return preg_replace_callback ($c6, "c_ws_plugin__s2member_utils_css::_compress_css_c3", $css);
					}
				/**
				* Handles CSS compression of hex colors.
				*
				* Reduces 6 character hex codes to just 3 whenever possible.
				*
				* @package s2Member\Utilities
				* @since 3.5
				*
				* @param array $m Array of matches from ``preg_replace_callback()``.
				* @return string Shortened hex code when possible, full hex code when not possible.
				*/
				public static function _compress_css_c3 ($m = FALSE)
					{
						if ($m[2][0] === $m[2][1] && $m[2][2] === $m[2][3] && $m[2][4] === $m[2][5])
							return $m[1] . $m[2][0] . $m[2][2] . $m[2][4];
						return $m[0];
					}
			}
	}
