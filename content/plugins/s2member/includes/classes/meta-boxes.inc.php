<?php
/**
* Administrative Meta Boxes.
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
* @package s2Member\Meta_Boxes
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_meta_boxes"))
	{
		/**
		* Administrative Meta Boxes.
		*
		* @package s2Member\Meta_Boxes
		* @since 3.5
		*/
		class c_ws_plugin__s2member_meta_boxes
			{
				/**
				* Adds meta boxes to Post/Page editing stations.
				*
				* Note: WordPress also calls this Hook with ``$type`` set to: `link` and `comment`. Possibly others.
				* 	Thus, the need for: ``in_array($type, array_keys (get_post_types ()))``.
				*
				* @package s2Member\Meta_Boxes
				* @since 3.5
				*
				* @attaches-to ``add_action("add_meta_boxes");``
				*
				* @param string $type String indicating type of Post, or another classification *( i.e., `nav_menu_item` )*.
				* @return null
				*/
				public static function add_meta_boxes ($type = FALSE)
					{
						do_action("ws_plugin__s2member_before_add_meta_boxes", get_defined_vars ());

						$excluded_types = array("link", "comment", "revision", "attachment", "nav_menu_item", "snippet", "redirect");
						$excluded_types = apply_filters("ws_plugin__s2member_add_meta_boxes_excluded_types", $excluded_types, get_defined_vars ());

						if (in_array($type, array_keys (get_post_types ())) && !in_array($type, $excluded_types))
							add_meta_box ("ws-plugin--s2member-security", "s2Member™", "c_ws_plugin__s2member_meta_box_security::security_meta_box", $type, "side", "high");

						do_action("ws_plugin__s2member_after_add_meta_boxes", get_defined_vars ());

						return /* Return for uniformity. */;
					}
			}
	}
