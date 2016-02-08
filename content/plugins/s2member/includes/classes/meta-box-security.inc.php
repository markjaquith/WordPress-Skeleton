<?php
/**
* Security meta box.
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

if(!class_exists("c_ws_plugin__s2member_meta_box_security"))
	{
		/**
		* Security meta box.
		*
		* @package s2Member\Meta_Boxes
		* @since 3.5
		*/
		class c_ws_plugin__s2member_meta_box_security
			{
				/**
				* Adds security meta box to Post/Page editing stations.
				*
				* @package s2Member\Meta_Boxes
				* @since 3.5
				*
				* @param object $post Post/Page object.
				* @return null
				*/
				public static function security_meta_box($post = FALSE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_security_meta_box", get_defined_vars());
						unset($__refs, $__v);

						if(is_object($post) && ($post_id = $post->ID) && (($post->post_type === "page" && current_user_can("edit_page", $post_id)) || current_user_can("edit_post", $post_id)))
							{
								if /* OK. So we're dealing with a Page classification. */($post->post_type === "page" && ($page_id = $post_id))
									{
										if(!in_array($page_id, array_merge(array($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"], $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_welcome_page"], $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["file_download_limit_exceeded_page"]), preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["specific_ids"]))))
											{
												echo '<input type="hidden" name="ws_plugin__s2member_security_meta_box_save" id="ws-plugin--s2member-security-meta-box-save" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-security-meta-box-save")).'" />'."\n";
												echo '<input type="hidden" name="ws_plugin__s2member_security_meta_box_save_id" id="ws-plugin--s2member-security-meta-box-save-id" value="'.esc_attr($page_id).'" />'."\n";

												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
													$pages[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_pages"]));

												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
													$posts[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_posts"]));

												echo '<p style="margin-left:2px;"><strong>Page Level Restriction?</strong></p>'."\n";
												echo '<label class="screen-reader-text" for="ws-plugin--s2member-security-meta-box-level">Add Level Restriction?</label>'."\n";
												echo '<select name="ws_plugin__s2member_security_meta_box_level" id="ws-plugin--s2member-security-meta-box-level" style="width:99%;">'."\n";
												echo  /* By default, we allow public access to any Post/Page. */'<option value=""></option>'."\n";

												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
													echo ($pages[$n] !== array("all")) ? // Protecting `all` Pages, of any kind?
													((!in_array("all-page", $posts[$n]) && !in_array("all-pages", $posts[$n])) // Protecting Posts of type: `page`?
													? '<option value="'.$n.'"'.((in_array($page_id, $pages[$n])) ? ' selected="selected"' : '').'>'.(($n === $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]) ? 'Require Highest Level #'.$n : 'Require Level #'.$n.' (or higher)').'</option>'."\n"
													: '<option value="" disabled="disabled">Level #'.$n.' (already protects "all" Posts of this type)</option>'."\n")
													: '<option value="" disabled="disabled">Level #'.$n.' (already protects "all" Pages)</option>'."\n";

												echo '</select><br /><small>* see: <strong>Restriction Options → Pages</strong></small>'."\n";

												if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site())
													// ^ Will change once Custom Capabilities are compatible with a Blog Farm.
													{
														echo '<p style="margin-top:15px; margin-left:2px;"><strong>Require Custom Capabilities?</strong></p>'."\n";
														echo '<label class="screen-reader-text" for="ws-plugin--s2member-security-meta-box-ccaps">Custom Capabilities?</label>'."\n";
														echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_security_meta_box_ccaps" id="ws-plugin--s2member-security-meta-box-ccaps" value="'.format_to_edit(trim(implode(",", (array)get_post_meta($page_id, "s2member_ccaps_req", true)))).'" onkeyup="if(this.value.match(/[^a-z_0-9,]/)) this.value = jQuery.trim (jQuery.trim (this.value).replace (/[ \-]/g, \'_\').replace (/[^a-z_0-9,]/gi, \'\').toLowerCase ());" style="width:99%;" />'."\n";
														echo '<br /><small>* see: <strong>API Scripting → Custom Capabilities</strong></small>'."\n";
													}
											}

										else if($page_id == $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"])
											echo 'This Page is your:<br /><strong>Membership Options Page</strong><br />(always publicly available)';

										else if($page_id == $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_welcome_page"])
											echo 'This Page is your:<br /><strong>Login Welcome Page</strong><br />(automatically guarded by s2Member)';

										else if($page_id == $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["file_download_limit_exceeded_page"])
											echo 'This Page is your:<br /><strong>Download Limit Exceeded Page</strong><br />(automatically guarded by s2Member)';

										else if(in_array($page_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["specific_ids"])))
											echo 'This Page is a:<br /><strong>Specific Post/Page for sale</strong><br />(already guarded by s2Member)';
									}
								else // Otherwise, we assume this is a Post, or possibly a Custom Post Type. It's NOT a Page.
									{
										if(!in_array($post_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["specific_ids"])))
											{
												echo '<input type="hidden" name="ws_plugin__s2member_security_meta_box_save" id="ws-plugin--s2member-security-meta-box-save" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-security-meta-box-save")).'" />'."\n";
												echo '<input type="hidden" name="ws_plugin__s2member_security_meta_box_save_id" id="ws-plugin--s2member-security-meta-box-save-id" value="'.esc_attr($post_id).'" />'."\n";

												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
													$posts[$n] = array_unique(preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_posts"]));

												echo '<p style="margin-left:2px;"><strong>Post Level Restriction?</strong></p>'."\n";
												echo '<label class="screen-reader-text" for="ws-plugin--s2member-security-meta-box-level">Add Level Restriction?</label>'."\n";
												echo '<select name="ws_plugin__s2member_security_meta_box_level" id="ws-plugin--s2member-security-meta-box-level" style="width:99%;">'."\n";
												echo '<option value=""></option>'."\n"; // By default, we allow public access to any Post/Page.

												for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
													echo ($posts[$n] !== array("all")) ? // Protecting `all` Posts, of any kind?
													((!in_array("all-".$post->post_type, $posts[$n]) && !in_array("all-".$post->post_type."s", $posts[$n])) // Protecting Posts `all-[of-this-type]`?
													? '<option value="'.$n.'"'.((in_array($post_id, $posts[$n])) ? ' selected="selected"' : '').'>'.(($n === $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]) ? 'Require Highest Level #'.$n : 'Require Level #'.$n.' (or higher)').'</option>'."\n"
													: '<option value="" disabled="disabled">Level #'.$n.' (already protects "all" Posts of this type)</option>'."\n")
													: '<option value="" disabled="disabled">Level #'.$n.' (already protects "all" Posts)</option>'."\n";

												echo '</select><br /><small>* see: <strong>Restriction Options → Posts</strong></small>'."\n";

												if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site())
													// ^ Will change once Custom Capabilities are compatible with a Blog Farm.
													{
														echo '<p style="margin-top:15px; margin-left:2px;"><strong>Require Custom Capabilities?</strong></p>'."\n";
														echo '<label class="screen-reader-text" for="ws-plugin--s2member-security-meta-box-ccaps">Custom Capabilities?</label>'."\n";
														echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_security_meta_box_ccaps" id="ws-plugin--s2member-security-meta-box-ccaps" value="'.format_to_edit(trim(implode(",", (array)get_post_meta($post_id, "s2member_ccaps_req", true)))).'" onkeyup="if(this.value.match(/[^a-z_0-9,]/)) this.value = jQuery.trim (jQuery.trim (this.value).replace (/[ \-]/g, \'_\').replace (/[^a-z_0-9,]/gi, \'\').toLowerCase ());" style="width:99%;" />'."\n";
														echo '<br /><small>* see: <strong>API Scripting → Custom Capabilities</strong></small>'."\n";
													}
											}

										else if(in_array($post_id, preg_split("/[\r\n\t\s;,]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["specific_ids"])))
											echo 'This Post is a:<br /><strong>Specific Post/Page for sale</strong><br />(already guarded by s2Member)';
									}
							}

						do_action("ws_plugin__s2member_after_security_meta_box", get_defined_vars());

						return /* Return for uniformity. */;
					}
			}
	}
