<?php
/**
* New User handlers (inner processing routines).
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
* @package s2Member\New_Users
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_user_new_in"))
	{
		/**
		* New User handlers (inner processing routines).
		*
		* @package s2Member\New_Users
		* @since 3.5
		*/
		class c_ws_plugin__s2member_user_new_in
			{
				/**
				* Callback adds Custom Fields to `/wp-admin/user-new.php`.
				*
				* We have to buffer because `/user-new.php` has NO Hooks.
				*
				* @package s2Member\New_Users
				* @since 3.5
				*
				* @attaches-to ``ob_start("c_ws_plugin__s2member_user_new_in::_admin_user_new_fields");``
				*
				* @return string Output buffer.
				*/
				public static function _admin_user_new_fields ($buffer = FALSE)
					{
						global $pagenow; // The current admin page file name.

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("_ws_plugin__s2member_before_admin_user_new_fields", get_defined_vars ());
						unset($__refs, $__v);

						if (is_blog_admin () && $pagenow === "user-new.php" && current_user_can ("create_users"))
							{
								$_p = c_ws_plugin__s2member_utils_strings::trim_deep (stripslashes_deep ($_POST));

								$unfs = '<div style="margin:25px 0 25px 0; height:1px; line-height:1px; background:#CCCCCC;"></div>' . "\n";

								$unfs .= '<h3 style="position:relative;"><img src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]) . '/images/large-icon.png" title="s2Member (a Membership management system for WordPress)" alt="" style="position:absolute; top:-15px; right:0; border:0;" />s2Member Configuration &amp; Profile Fields' . ((is_multisite ()) ? ' (for this Blog)' : '') . '</h3>' . "\n";

								$unfs .= '<table class="form-table">' . "\n";

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_before", get_defined_vars ());
								unset($__refs, $__v);

								if (is_multisite ()) // Multisite Networking is currently lacking these fields; we pop them in.
									{
										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_first_name", get_defined_vars ());
										unset($__refs, $__v);

										$unfs .= '<tr>' . "\n";
										$unfs .= '<th><label for="ws-plugin--s2member-user-new-first-name">First Name:</label></th>' . "\n";
										$unfs .= '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_user_new_first_name" id="ws-plugin--s2member-user-new-first-name" value="' . esc_attr ($_p["ws_plugin__s2member_user_new_first_name"]) . '" class="regular-text" /></td>' . "\n";
										$unfs .= '</tr>' . "\n";

										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_first_name", get_defined_vars ());
										unset($__refs, $__v);

										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_last_name", get_defined_vars ());
										unset($__refs, $__v);

										$unfs .= '<tr>' . "\n";
										$unfs .= '<th><label for="ws-plugin--s2member-user-new-last-name">Last Name:</label></th>' . "\n";
										$unfs .= '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_user_new_last_name" id="ws-plugin--s2member-user-new-last-name" value="' . esc_attr ($_p["ws_plugin__s2member_user_new_last_name"]) . '" class="regular-text" /></td>' . "\n";
										$unfs .= '</tr>' . "\n";

										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_last_name", get_defined_vars ());
										unset($__refs, $__v);
									}

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_subscr_gateway", get_defined_vars ());
								unset($__refs, $__v);

								$unfs .= '<tr>' . "\n";
								$unfs .= '<th><label for="ws-plugin--s2member-user-new-s2member-subscr-gateway">Paid Subscr. Gateway:</label> <a href="#" onclick="alert(\'A Payment Gateway code is associated with the Paid Subscr. ID below. A Paid Subscription ID (or a Buy Now Transaction ID) is only valid for paid Members. Also known as (a Recurring Profile ID, a ClickBank Receipt #, a Google Order ID, an AliPay Trade No.). Under normal circumstances, this is filled automatically by s2Member. This field is ONLY here for Customer Service purposes; just in case you ever need to enter a Paid Subscr. Gateway/ID manually. This field will be empty for Free Subscribers, and/or anyone who is NOT paying you.\\n\\nThe value of Paid Subscr. ID, can be a PayPal Standard `Subscription ID`, or a PayPal Pro `Recurring Profile ID`, or a PayPal `Transaction ID`; depending on the type of sale. Your PayPal account will supply this information. If you\\\'re using Google Wallet, use the Google Order ID. ClickBank provides a Receipt #, ccBill provides a Subscription ID, Authorize.Net provides a Subscription ID, and AliPay provides a Transaction ID. The general rule is... IF there\\\'s a Subscription ID, use that! If there\\\'s NOT, use the Transaction ID.\'); return false;" tabindex="-1">[?]</a></th>' . "\n";
								$unfs .= '<td><select name="ws_plugin__s2member_user_new_s2member_subscr_gateway" id="ws-plugin--s2member-user-new-s2member-subscr-gateway" style="width:25em;"><option value=""></option>' . "\n";
								foreach (apply_filters("ws_plugin__s2member_profile_s2member_subscr_gateways", array("paypal" => "PayPal (code: paypal)"), get_defined_vars ()) as $gateway => $gateway_name)
									$unfs .= '<option value="' . esc_attr ($gateway) . '"' . (($gateway === $_p["ws_plugin__s2member_user_new_s2member_subscr_gateway"]) ? ' selected="selected"' : '') . '>' . esc_html ($gateway_name) . '</option>' . "\n";
								$unfs .= '</select>' . "\n";
								$unfs .= '</td>' . "\n";
								$unfs .= '</tr>' . "\n";

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_subscr_gateway", get_defined_vars ());
								unset($__refs, $__v);

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_subscr_id", get_defined_vars ());
								unset($__refs, $__v);

								$unfs .= '<tr>' . "\n";
								$unfs .= '<th><label for="ws-plugin--s2member-user-new-s2member-subscr-id">Paid Subscr. ID:</label> <a href="#" onclick="alert(\'A Paid Subscription ID (or a Buy Now Transaction ID) is only valid for paid Members. Also known as (a Recurring Profile ID, a ClickBank Receipt #, a Google Order ID, an AliPay Trade No.). Under normal circumstances, this is filled automatically by s2Member. This field is ONLY here for Customer Service purposes; just in case you ever need to enter a Paid Subscr. Gateway/ID manually. This field will be empty for Free Subscribers, and/or anyone who is NOT paying you.\\n\\nThe value of Paid Subscr. ID, can be a PayPal Standard `Subscription ID`, or a PayPal Pro `Recurring Profile ID`, or a PayPal `Transaction ID`; depending on the type of sale. Your PayPal account will supply this information. If you\\\'re using Google Wallet, use the Google Order ID. ClickBank provides a Receipt #, ccBill provides a Subscription ID, Authorize.Net provides a Subscription ID, and AliPay provides a Transaction ID. The general rule is... if there\\\'s a Subscription ID, use that! If there\\\'s NOT, use the Transaction ID.\'); return false;" tabindex="-1">[?]</a></th>' . "\n";
								$unfs .= '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_user_new_s2member_subscr_id" id="ws-plugin--s2member-user-new-s2member-subscr-id" value="' . format_to_edit ($_p["ws_plugin__s2member_user_new_s2member_subscr_id"]) . '" class="regular-text" /></td>' . "\n";
								$unfs .= '</tr>' . "\n";

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_subscr_id", get_defined_vars ());
								unset($__refs, $__v);

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_custom", get_defined_vars ());
								unset($__refs, $__v);

								$unfs .= '<tr>' . "\n";
								$unfs .= '<th><label for="ws-plugin--s2member-user-new-s2member-custom">Custom Value:</label> <a href="#" onclick="alert(\'A Paid Subscription is always associated with a Custom String that is passed through the custom=\\\'\\\'' . c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"]), 3) . '\\\'\\\' attribute of your Shortcode. This Custom Value, MUST always start with your domain name. However, you can also pipe delimit additional values after your domain, if you need to.\\n\\nFor example:\n' . c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"]), 3) . '|cv1|cv2|cv3\'); return false;" tabindex="-1">[?]</a></th>' . "\n";
								$unfs .= '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_user_new_s2member_custom" id="ws-plugin--s2member-user-new-s2member-custom" value="' . format_to_edit ($_p["ws_plugin__s2member_user_new_s2member_custom"]) . '" class="regular-text" /></td>' . "\n";
								$unfs .= '</tr>' . "\n";

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_custom", get_defined_vars ());
								unset($__refs, $__v);

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_registration_ip", get_defined_vars ());
								unset($__refs, $__v);

								$unfs .= '<tr>' . "\n";
								$unfs .= '<th><label for="ws-plugin--s2member-user-new-s2member-registration-ip">Registration IP:</label> <a href="#" onclick="alert(\'This is the IP Address the User had at the time of registration. If you don\\\'t know the User\\\'s IP Address, just leave this blank. If this is left empty, s2Member will make attempts in the future to grab the User\\\'s IP Address.\'); return false;" tabindex="-1">[?]</a></th>' . "\n";
								$unfs .= '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_user_new_s2member_registration_ip" id="ws-plugin--s2member-user-new-s2member-registration-ip" value="' . format_to_edit ($_p["ws_plugin__s2member_user_new_s2member_registration_ip"]) . '" class="regular-text" /></td>' . "\n";
								$unfs .= '</tr>' . "\n";

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_registration_ip", get_defined_vars ());
								unset($__refs, $__v);

								if (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ())
									// ^ Will change once Custom Capabilities are compatible with a Blog Farm.
									{
										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_ccaps", get_defined_vars ());
										unset($__refs, $__v);

										$unfs .= '<tr>' . "\n";
										$unfs .= '<th><label for="ws-plugin--s2member-user-new-s2member-ccaps">Custom Capabilities:</label> <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: s2Member → API Scripting → Custom Capabilities.' . ((is_multisite ()) ? '\\n\\nCustom Capabilities are assigned on a per-Blog basis. So having a set of Custom Capabilities for one Blog, and having NO Custom Capabilities on another Blog - is very common. This is how permissions are designed to work.' : '') . '\'); return false;" tabindex="-1">[?]</a></th>' . "\n";
										$unfs .= '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_user_new_s2member_ccaps" id="ws-plugin--s2member-user-new-s2member-ccaps" value="' . format_to_edit ($_p["ws_plugin__s2member_user_new_s2member_ccaps"]) . '" class="regular-text" onkeyup="if(this.value.match(/[^a-z_0-9,]/)) this.value = jQuery.trim (jQuery.trim (this.value).replace (/[ \-]/g, \'_\').replace (/[^a-z_0-9,]/gi, \'\').toLowerCase ());" /></td>' . "\n";
										$unfs .= '</tr>' . "\n";

										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_ccaps", get_defined_vars ());
										unset($__refs, $__v);
									}

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_auto_eot_time", get_defined_vars ());
								unset($__refs, $__v);

								$unfs .= '<tr>' . "\n";
								$unfs .= '<th><label for="ws-plugin--s2member-user-new-auto-eot-time">Automatic EOT Time:</label> <a href="#" onclick="alert(\'EOT = End Of Term. ( i.e., Account Expiration / Termination. ).\\n\\nIf you leave this empty, s2Member will configure an EOT Time automatically, based on the paid Subscription associated with this account. In other words, if a paid Subscription expires, is cancelled, terminated, refunded, reversed, or charged back to you; s2Member will deal with the EOT automatically.\\n\\nThat being said, if you would rather take control over this, you can. If you type in a date manually, s2Member will obey the Auto-EOT Time that you\\\'ve given, no matter what. In other words, you can force certain Members to expire automatically, at a time that you specify. s2Member will obey.\\n\\nValid formats for Automatic EOT Time:\\n\\nmm/dd/yyyy\\nyyyy-mm-dd\\n+1 year\\n+2 weeks\\n+2 months\\n+10 minutes\\nnext thursday\\ntomorrow\\ntoday\\n\\n* anything compatible with PHP\\\'s strtotime() function.\'); return false;" tabindex="-1">[?]</a>' . (($auto_eot_time) ? '<br /><small>(<a href="http://www.world-time-zones.org/zones/greenwich-mean-time.htm" target="_blank" rel="external">Universal Time / GMT</a>)</small>' : '') . '</th>' . "\n";
								$unfs .= '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_user_new_s2member_auto_eot_time" id="ws-plugin--s2member-user-new-auto-eot-time" value="' . format_to_edit ($_p["ws_plugin__s2member_user_new_s2member_auto_eot_time"]) . '" class="regular-text" /></td>' . "\n";
								$unfs .= '</tr>' . "\n";

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_auto_eot_time", get_defined_vars ());
								unset($__refs, $__v);

								if (c_ws_plugin__s2member_list_servers::list_servers_integrated ()) // Only if integrated with s2Member.
									{
										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_opt_in", get_defined_vars ());
										unset($__refs, $__v);

										$unfs .= '<tr>' . "\n";
										$unfs .= '<th><label for="ws-plugin--s2member-user-new-opt-in">Process List Servers:</label> <a href="#" onclick="alert(\'You have at least one List Server integrated with s2Member. Would you like to process a confirmation request for this new User? If not, just leave the box unchecked.\'); return false;" tabindex="-1">[?]</a></th>' . "\n";
										$unfs .= '<td><label><input type="checkbox" name="ws_plugin__s2member_user_new_opt_in" id="ws-plugin--s2member-user-new-opt-in" value="1"' . (($_p["ws_plugin__s2member_user_new_opt_in"]) ? ' checked="checked"' : '') . ' /> Yes, send a mailing list confirmation email to this new User.</label></td>' . "\n";
										$unfs .= '</tr>' . "\n";

										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_opt_in", get_defined_vars ());
										unset($__refs, $__v);
									}

								if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"]) // Now, do we have Custom Fields?
									if ($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level ("any", "administrative"))
										{
											$unfs .= '<tr>' . "\n";
											$unfs .= '<td colspan="2">' . "\n";
											$unfs .= '<div style="height:1px; line-height:1px; background:#CCCCCC;"></div>' . "\n";
											$unfs .= '</td>' . "\n";
											$unfs .= '</tr>' . "\n";

											foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
											do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_custom_fields", get_defined_vars ());
											unset($__refs, $__v);

											foreach (json_decode ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"], true) as $field)
												{
													foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
													do_action("_ws_plugin__s2member_during_admin_user_new_fields_during_custom_fields_before", get_defined_vars ());
													unset($__refs, $__v);

													if (in_array($field["id"], $fields_applicable)) // Field applicable?
														{
															$field_var = preg_replace ("/[^a-z0-9]/i", "_", strtolower ($field["id"]));
															$field_id_class = preg_replace ("/_/", "-", $field_var);

															foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
															if (apply_filters("_ws_plugin__s2member_during_admin_user_new_fields_during_custom_fields_display", true, get_defined_vars ()))
																{
																	if (!empty($field["section"]) && $field["section"] === "yes") // Starts a new section?
																		$unfs .= '<tr><td colspan="2"><div class="ws-plugin--s2member-user-new-divider-section' . ((!empty($field["sectitle"])) ? '-title' : '') . '">' . ((!empty($field["sectitle"])) ? $field["sectitle"] : '') . '</div></td></tr>';

																	$unfs .= '<tr>' . "\n";
																	$unfs .= '<th><label for="ws-plugin--s2member-user-new-' . esc_attr ($field_id_class) . '">' . ((preg_match ("/^(checkbox|pre_checkbox)$/", $field["type"])) ? ucwords (preg_replace ("/_/", " ", $field_var)) : $field["label"]) . ':</label></th>' . "\n";
																	$unfs .= '<td>' . c_ws_plugin__s2member_custom_reg_fields::custom_field_gen (__FUNCTION__, $field, "ws_plugin__s2member_user_new_", "ws-plugin--s2member-user-new-", "", ((preg_match ("/^(text|textarea|select|selects)$/", $field["type"])) ? "width:99%;" : ""), "", "", $_p, $_p["ws_plugin__s2member_user_new_" . $field_var], "administrative") . '</td>' . "\n";
																	$unfs .= '</tr>' . "\n";
																}
															unset($__refs, $__v);
														}

													foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
													do_action("_ws_plugin__s2member_during_admin_user_new_fields_during_custom_fields_after", get_defined_vars ());
													unset($__refs, $__v);
												}

											foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
											do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_custom_fields", get_defined_vars ());
											unset($__refs, $__v);

											$unfs .= '<tr>' . "\n";
											$unfs .= '<td colspan="2">' . "\n";
											$unfs .= '<div style="height:1px; line-height:1px; background:#CCCCCC;"></div>' . "\n";
											$unfs .= '</td>' . "\n";
											$unfs .= '</tr>' . "\n";
										}

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_before_notes", get_defined_vars ());
								unset($__refs, $__v);

								$unfs .= '<tr>' . "\n";
								$unfs .= '<th><label for="ws-plugin--s2member-user-new-s2member-notes">Administrative Notes:</label> <a href="#" onclick="alert(\'This is for Administrative purposes. You can keep a list of Notations about this account. These Notations are private; Users/Members will never see these.\\n\\n*Note* The s2Member software may `append` Notes to this field occasionally, under special circumstances. For example, when/if s2Member demotes a paid Member to a Free Subscriber, s2Member will leave a Note in this field.\'); return false;" tabindex="-1">[?]</a><br /><br /><small>These Notations are private; Users/Members will never see any of these notes.</small></th>' . "\n";
								$unfs .= '<td><textarea name="ws_plugin__s2member_user_new_s2member_notes" id="ws-plugin--s2member-user-new-s2member-notes" rows="5" wrap="off" spellcheck="false" style="width:99%;">' . format_to_edit ($_p["ws_plugin__s2member_user_new_s2member_notes"]) . '</textarea></td>' . "\n";
								$unfs .= '</tr>' . "\n";

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_after_notes", get_defined_vars ());
								unset($__refs, $__v);

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("_ws_plugin__s2member_during_admin_user_new_fields_after", get_defined_vars ());
								unset($__refs, $__v);

								$unfs .= '</table>' . "\n";

								$unfs .= '<div style="margin:25px 0 25px 0; height:1px; line-height:1px; background:#CCCCCC;"></div>' . "\n";

								$buffer = preg_replace ("/(\<\/table\>)(\s*)(\<p\s+class\s*\=\s*['\"]submit['\"]\s*\>)(\s*)(\<input\s+type\s*\=\s*['\"]submit['\"]\s+name\s*\=\s*['\"]createuser['\"])/", "$1$2\n" . $unfs . "$3$4$5", $buffer);
							}

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("_ws_plugin__s2member_after_admin_user_new_fields", get_defined_vars ());
						unset($__refs, $__v);

						return apply_filters("_ws_plugin__s2member_admin_user_new_fields", $buffer, get_defined_vars ());
					}
			}
	}
