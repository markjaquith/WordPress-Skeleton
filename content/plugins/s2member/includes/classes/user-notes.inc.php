<?php
/**
* Administrative notes.
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
* @package s2Member\Admin_Notes
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_user_notes"))
	{
		/**
		* Administrative notes.
		*
		* @package s2Member\Admin_Notes
		* @since 3.5
		*/
		class c_ws_plugin__s2member_user_notes
			{
				/**
				* Appends a note onto a specific User/Member's account.
				*
				* @package s2Member\Admin_Notes
				* @since 3.5
				*
				* @param int|string $user_id A numeric WordPress User ID.
				* @param string $notes The string of notes to append. One note, or many.
				* @return string The full set of notes, including appendage.
				*/
				public static function append_user_notes ($user_id = FALSE, $notes = FALSE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_append_user_notes", get_defined_vars ());
						unset($__refs, $__v);

						if ($user_id && $notes && is_string ($notes)) // Must have these.
							{
								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_append_user_notes", get_defined_vars ());
								unset($__refs, $__v);

								$notes = trim (get_user_option ("s2member_notes", $user_id) . "\n" . $notes);

								update_user_option ($user_id, "s2member_notes", $notes);
							}

						return apply_filters("ws_plugin__s2member_append_user_notes", ((!empty($notes)) ? $notes : ""), get_defined_vars ());
					}
				/**
				* Clear specific notes from a User/Member's account; based on line-by-line regex.
				*
				* @package s2Member\Admin_Notes
				* @since 3.5
				*
				* @param int|string $user_id A numeric WordPress User ID.
				* @param string $regex A regular expression to match against each line.
				* @return string The full set of notes, after clearing.
				*/
				public static function clear_user_note_lines ($user_id = FALSE, $regex = FALSE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_clear_user_note_lines", get_defined_vars ());
						unset($__refs, $__v);

						if ($user_id && $regex && is_string ($regex) && ($lines = array()))
							{
								// Careful here to preserve empty lines.
								$notes = trim (get_user_option ("s2member_notes", $user_id));
								foreach (preg_split ("/\n/", $notes) as $line)
									if (!preg_match ($regex, $line))
										$lines[] = $line;

								$notes = trim (implode ("\n", $lines));

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_clear_user_note_lines", get_defined_vars ());
								unset($__refs, $__v);

								update_user_option ($user_id, "s2member_notes", $notes);
							}

						return apply_filters("ws_plugin__s2member_clear_user_note_lines", ((!empty($notes)) ? $notes : ""), get_defined_vars ());
					}
			}
	}
