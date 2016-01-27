<?php
/**
* Meta box saves.
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
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_meta_box_saves"))
	{
		/**
		* Meta box saves.
		*
		* @package s2Member\Meta_Boxes
		* @since 3.5
		*/
		class c_ws_plugin__s2member_meta_box_saves
			{
				/**
				* Saves data entered into meta boxes on Post/Page editing stations.
				*
				* @package s2Member\Meta_Boxes
				* @since 3.5
				*
				* @attaches-to ``add_action("save_post");``
				*
				* @param int|string $post_id Numeric Post/Page ID.
				* @return null
				*/
				public static function save_meta_boxes ($post_id = FALSE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_save_meta_boxes", get_defined_vars ());
						unset($__refs, $__v);

						if ($post_id && !empty($_POST["ws_plugin__s2member_security_meta_box_save"]) && ($nonce = $_POST["ws_plugin__s2member_security_meta_box_save"]) && wp_verify_nonce ($nonce, "ws-plugin--s2member-security-meta-box-save"))
							if (!empty($_POST["ws_plugin__s2member_security_meta_box_save_id"]) && $post_id == $_POST["ws_plugin__s2member_security_meta_box_save_id"] && !empty($_POST["post_type"]))
								// We do NOT process historical revisions here; because it causes confusion in the General Options panel for s2Member.
								{
									$_p = /* Clean and create a local copy. */ c_ws_plugin__s2member_utils_strings::trim_deep (stripslashes_deep ($_POST));

									if (($_p["post_type"] === "page" && current_user_can ("edit_page", $post_id)) || current_user_can ("edit_post", $post_id))
										{
											if /* OK. So we're dealing with a Page classification? */ ($_p["post_type"] === "page" && ($page_id = $post_id))
												{
													if /* CAN be empty. */ (isset ($_p["ws_plugin__s2member_security_meta_box_level"]))
														{
															for ($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
																$pages[$n] = array_unique (preg_split ("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $n . "_pages"]));

															for ($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
																$posts[$n] = array_unique (preg_split ("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $n . "_posts"]));

															for ($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
																if (($i = array_search ($page_id, $pages[$n])) !== false)
																	unset($pages[$n][$i]);

															if (isset ($pages[$_p["ws_plugin__s2member_security_meta_box_level"]]) && is_array($pages[$_p["ws_plugin__s2member_security_meta_box_level"]]))
																if ($pages[$_p["ws_plugin__s2member_security_meta_box_level"]] !== array("all") && !in_array("all-page", $posts[$_p["ws_plugin__s2member_security_meta_box_level"]]) && !in_array("all-pages", $posts[$_p["ws_plugin__s2member_security_meta_box_level"]]))
																	array_push ($pages[$_p["ws_plugin__s2member_security_meta_box_level"]], (string)$page_id);

															for ($n = 0, $new_options = array(); $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
																$new_options = array_merge ($new_options, array("ws_plugin__s2member_level" . $n . "_pages" => trim (implode (",", $pages[$n]))));

															foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
															do_action("ws_plugin__s2member_during_save_meta_boxes", get_defined_vars ());
															unset($__refs, $__v);

															c_ws_plugin__s2member_menu_pages::update_all_options ($new_options, true, false, array("page-conflict-warnings"), true);
														}
												}

											else // Otherwise, we assume this is a Post, or possibly a Custom Post Type. It's NOT a Page.
												{
													if /* CAN be empty. */ (isset ($_p["ws_plugin__s2member_security_meta_box_level"]))
														{
															for ($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
																$posts[$n] = array_unique (preg_split ("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $n . "_posts"]));

															for ($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
																if (($i = array_search ($post_id, $posts[$n])) !== false)
																	unset($posts[$n][$i]);

															if (isset ($posts[$_p["ws_plugin__s2member_security_meta_box_level"]]) && is_array($posts[$_p["ws_plugin__s2member_security_meta_box_level"]]))
																if ($posts[$_p["ws_plugin__s2member_security_meta_box_level"]] !== array("all") && !in_array("all-" . $_p["post_type"], $posts[$_p["ws_plugin__s2member_security_meta_box_level"]]) && !in_array("all-" . $_p["post_type"] . "s", $posts[$_p["ws_plugin__s2member_security_meta_box_level"]]))
																	array_push ($posts[$_p["ws_plugin__s2member_security_meta_box_level"]], (string)$post_id);

															for ($n = 0, $new_options = array(); $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
																$new_options = array_merge ($new_options, array("ws_plugin__s2member_level" . $n . "_posts" => trim (implode (",", $posts[$n]))));

															foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
															do_action("ws_plugin__s2member_during_save_meta_boxes", get_defined_vars ());
															unset($__refs, $__v);

															c_ws_plugin__s2member_menu_pages::update_all_options ($new_options, true, false, array("page-conflict-warnings"), true);
														}
												}

											if /* OK. So we're dealing with a Page classification? */ ($_p["post_type"] === "page" && ($page_id = $post_id))
												{
													if /* CAN be empty. */ (isset ($_p["ws_plugin__s2member_security_meta_box_ccaps"]))
														{
															$ccaps_req = trim (strtolower ($_p["ws_plugin__s2member_security_meta_box_ccaps"]), ",");
															$ccaps_req = trim (preg_replace ("/[^a-z_0-9,]/", "", $ccaps_req), ",");

															if (strlen ($ccaps_req) && ($s2member_ccaps_req = preg_split ("/[\r\n\t\s;,]+/", $ccaps_req)))
																update_post_meta ($page_id, "s2member_ccaps_req", $s2member_ccaps_req);

															else // Otherwise, the array is empty. Safe to delete.
																delete_post_meta ($page_id, "s2member_ccaps_req");
														}
												}

											else // Otherwise, we assume this is a Post, or possibly a Custom Post Type. It's NOT a Page.
												{
													if (isset ($_p["ws_plugin__s2member_security_meta_box_ccaps"])) // CAN be empty.
														{
															$ccaps_req = trim (strtolower ($_p["ws_plugin__s2member_security_meta_box_ccaps"]), ",");
															$ccaps_req = trim (preg_replace ("/[^a-z_0-9,]/", "", $ccaps_req), ",");

															if (strlen ($ccaps_req) && ($s2member_ccaps_req = preg_split ("/[\r\n\t\s;,]+/", $ccaps_req)))
																update_post_meta ($post_id, "s2member_ccaps_req", $s2member_ccaps_req);

															else // Otherwise, the array is empty. Safe to delete.
																delete_post_meta ($post_id, "s2member_ccaps_req");
														}
												}
										}
								}

						do_action("ws_plugin__s2member_after_save_meta_boxes", get_defined_vars ());

						return /* Return for uniformity. */;
					}
			}
	}
