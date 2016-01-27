<?php
/**
* s2Member's PayPal IPN handler (inner processing routine).
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
* @since 110720
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_paypal_notify_in_subscr_or_rp_cancellation_w_level"))
	{
		/**
		* s2Member's PayPal IPN handler (inner processing routine).
		*
		* @package s2Member\PayPal
		* @since 110720
		*/
		class c_ws_plugin__s2member_paypal_notify_in_subscr_or_rp_cancellation_w_level
			{
				/**
				* s2Member's PayPal IPN handler (inner processing routine).
				*
				* @package s2Member\PayPal
				* @since 110720
				*
				* @param array $vars Required. An array of defined variables passed by {@link s2Member\PayPal\c_ws_plugin__s2member_paypal_notify_in::paypal_notify()}.
				* @return array|bool The original ``$paypal`` array passed in (extracted) from ``$vars``, or false when conditions do NOT apply.
				*/
				public static function cp ($vars = array()) // Conditional phase for ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.
					{
						extract ($vars, EXTR_OVERWRITE | EXTR_REFS); // Extract all vars passed in from: ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.

						if ((!empty($paypal["txn_type"]) && preg_match ("/^(subscr_cancel|recurring_payment_profile_cancel|mp_cancel)$/i", $paypal["txn_type"]))
						&& !(preg_match ("/^recurring_payment_profile_cancel$/i", $paypal["txn_type"]) && !empty($paypal["initial_payment_status"]) && preg_match ("/^failed$/i", $paypal["initial_payment_status"]))
						&& ((!empty($paypal["item_number"]) || ($paypal["item_number"] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_item_number ($paypal))) && preg_match ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["membership_item_number_w_level_regex"], $paypal["item_number"]))
						&& (!empty($paypal["period1"]) || ($paypal["period1"] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_period1 ($paypal)) || ($paypal["period1"] = "0 D"))
						&& (!empty($paypal["period3"]) || ($paypal["period3"] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_period3 ($paypal)))
						&& (!empty($paypal["subscr_id"]) || ($paypal["subscr_id"] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_subscr_id ($paypal)))
						&& (!empty($paypal["item_name"]) || ($paypal["item_name"] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_item_name ($paypal)))
						&& (!empty($paypal["payer_email"]) || ($paypal["payer_email"] = c_ws_plugin__s2member_utils_users::get_user_email_with ($paypal["subscr_id"])))
						&& (!empty($paypal["subscr_baid"]) || ($paypal["subscr_baid"] = $paypal["subscr_id"]))
						&& (!empty($paypal["subscr_cid"]) || ($paypal["subscr_cid"] = $paypal["subscr_id"])))
							{
								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_paypal_notify_before_subscr_cancel", get_defined_vars ());
								unset($__refs, $__v);

								if (!get_transient ($transient_ipn = "s2m_ipn_" . md5 ("s2member_transient_" . $_paypal_s)) && set_transient ($transient_ipn, time (), 31556926 * 10))
									{
										$paypal["s2member_log"][] = "s2Member `txn_type` identified as ( `subscr_cancel|recurring_payment_profile_cancel|mp_cancel` ).";

										list ($paypal["level"], $paypal["ccaps"]) = preg_split ("/\:/", $paypal["item_number"], 3);

										$paypal["ip"] = (preg_match ("/ip address/i", $paypal["option_name2"]) && $paypal["option_selection2"]) ? $paypal["option_selection2"] : "";
										$paypal["ip"] = (!$paypal["ip"] && preg_match ("/^[a-z0-9]+~[0-9\.]+$/i", $paypal["invoice"])) ? preg_replace ("/^[a-z0-9]+~/i", "", $paypal["invoice"]) : $paypal["ip"];

										if (($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with ($paypal["subscr_id"])) && is_object ($user = new WP_User ($user_id)) && $user->ID)
											{
												if (!$user->has_cap ("administrator")) // Do NOT process this routine on Administrators.
													{
														$fields = get_user_option ("s2member_custom_fields", $user_id); // These will be needed in the routines below.
														$user_reg_ip = get_user_option ("s2member_registration_ip", $user_id); // Original IP during Registration.
														$user_reg_ip = $paypal["ip"] = ($user_reg_ip) ? $user_reg_ip : $paypal["ip"]; // Now merge conditionally.

														if (!get_user_option ("s2member_auto_eot_time", $user_id)) // Respect existing.
															{
																$processing = $during = true; // Yes, we ARE processing this.

																$auto_eot_time = c_ws_plugin__s2member_utils_time::auto_eot_time ($user_id, $paypal["period1"], $paypal["period3"]);

																update_user_option ($user_id, "s2member_auto_eot_time", $auto_eot_time); // s2Member follows-up later.

																$paypal["s2member_log"][] = "Auto-EOT Time for this account: " . date ("D M j, Y g:i a T", $auto_eot_time);

																foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
																do_action("ws_plugin__s2member_during_paypal_notify_during_subscr_cancel", get_defined_vars ());
																unset($__refs, $__v);
															}
														else $paypal["s2member_log"][] = "Ignoring Cancellation. An Auto-EOT Time is already set for this Member. An s2Member API Notification will still be processed however.";

														if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["cancellation_notification_urls"])
															{
																foreach (preg_split ("/[\r\n\t]+/", $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["cancellation_notification_urls"]) as $url) // Handle Cancellation Notifications.

																	if (($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($paypal["subscr_id"])), $url)))
																		if (($url = preg_replace ("/%%subscr_baid%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($paypal["subscr_baid"])), $url)) && ($url = preg_replace ("/%%subscr_cid%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($paypal["subscr_cid"])), $url)))
																			if (($url = preg_replace ("/%%item_number%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($paypal["item_number"])), $url)) && ($url = preg_replace ("/%%item_name%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($paypal["item_name"])), $url)))
																				if (($url = preg_replace ("/%%user_first_name%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($user->first_name)), $url)) && ($url = preg_replace ("/%%user_last_name%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($user->last_name)), $url)))
																					if (($url = preg_replace ("/%%user_full_name%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode (trim ($user->first_name . " " . $user->last_name))), $url)))
																						if (($url = preg_replace ("/%%user_email%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($user->user_email)), $url)))
																							if (($url = preg_replace ("/%%user_login%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($user->user_login)), $url)))
																								if (($url = preg_replace ("/%%user_ip%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($user_reg_ip)), $url)))
																									if (($url = preg_replace ("/%%user_id%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode ($user_id)), $url)))
																										{
																											if (is_array($fields) && !empty($fields))
																												foreach /* Custom Registration/Profile Fields. */ ($fields as $var => $val)
																													if (!($url = preg_replace ("/%%" . preg_quote ($var, "/") . "%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (urlencode (maybe_serialize ($val))), $url)))
																														break;

																											if (($url = trim (preg_replace ("/%%(.+?)%%/i", "", $url))))
																												c_ws_plugin__s2member_utils_urls::remote ($url);
																										}
																$paypal["s2member_log"][] = "Cancellation Notification URLs have been processed.";
															}
														if ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["cancellation_notification_recipients"])
															{
																$msg = $sbj = "(s2Member / API Notification Email) - Cancellation";
																$msg .= "\n\n"; // Spacing in the message body.

																$msg .= "subscr_id: %%subscr_id%%\n";
																$msg .= "subscr_baid: %%subscr_baid%%\n";
																$msg .= "subscr_cid: %%subscr_cid%%\n";
																$msg .= "item_number: %%item_number%%\n";
																$msg .= "item_name: %%item_name%%\n";
																$msg .= "user_first_name: %%user_first_name%%\n";
																$msg .= "user_last_name: %%user_last_name%%\n";
																$msg .= "user_full_name: %%user_full_name%%\n";
																$msg .= "user_email: %%user_email%%\n";
																$msg .= "user_login: %%user_login%%\n";
																$msg .= "user_ip: %%user_ip%%\n";
																$msg .= "user_id: %%user_id%%\n";

																if (is_array($fields) && !empty($fields))
																	foreach ($fields as $var => $val)
																		$msg .= $var . ": %%" . $var . "%%\n";

																$msg .= "cv0: %%cv0%%\n";
																$msg .= "cv1: %%cv1%%\n";
																$msg .= "cv2: %%cv2%%\n";
																$msg .= "cv3: %%cv3%%\n";
																$msg .= "cv4: %%cv4%%\n";
																$msg .= "cv5: %%cv5%%\n";
																$msg .= "cv6: %%cv6%%\n";
																$msg .= "cv7: %%cv7%%\n";
																$msg .= "cv8: %%cv8%%\n";
																$msg .= "cv9: %%cv9%%";

																if (($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace ("/%%subscr_id%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($paypal["subscr_id"]), $msg)))
																	if (($msg = preg_replace ("/%%subscr_baid%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($paypal["subscr_baid"]), $msg)) && ($msg = preg_replace ("/%%subscr_cid%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($paypal["subscr_cid"]), $msg)))
																		if (($msg = preg_replace ("/%%item_number%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($paypal["item_number"]), $msg)) && ($msg = preg_replace ("/%%item_name%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($paypal["item_name"]), $msg)))
																			if (($msg = preg_replace ("/%%user_first_name%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($user->first_name), $msg)) && ($msg = preg_replace ("/%%user_last_name%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($user->last_name), $msg)))
																				if (($msg = preg_replace ("/%%user_full_name%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (trim ($user->first_name . " " . $user->last_name)), $msg)))
																					if (($msg = preg_replace ("/%%user_email%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($user->user_email), $msg)))
																						if (($msg = preg_replace ("/%%user_login%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($user->user_login), $msg)))
																							if (($msg = preg_replace ("/%%user_ip%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($user_reg_ip), $msg)))
																								if (($msg = preg_replace ("/%%user_id%%/i", c_ws_plugin__s2member_utils_strings::esc_refs ($user_id), $msg)))
																									{
																										if (is_array($fields) && !empty($fields))
																											foreach /* Custom Registration/Profile Fields. */ ($fields as $var => $val)
																												if (!($msg = preg_replace ("/%%" . preg_quote ($var, "/") . "%%/i", c_ws_plugin__s2member_utils_strings::esc_refs (maybe_serialize ($val)), $msg)))
																													break;

																										if ($sbj && ($msg = trim (preg_replace ("/%%(.+?)%%/i", "", $msg)))) // Still have a ``$sbj`` and a ``$msg``?

																											foreach (c_ws_plugin__s2member_utils_strings::parse_emails ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["cancellation_notification_recipients"]) as $recipient)
																												wp_mail ($recipient, apply_filters("ws_plugin__s2member_cancellation_notification_email_sbj", $sbj, get_defined_vars ()), apply_filters("ws_plugin__s2member_cancellation_notification_email_msg", $msg, get_defined_vars ()), "Content-Type: text/plain; charset=UTF-8");
																									}
																$paypal["s2member_log"][] = "Cancellation Notification Emails have been processed.";
															}
													}
												else $paypal["s2member_log"][] = "Ignoring Cancellation. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access.";
											}
										else $paypal["s2member_log"][] = "Unable to handle Cancellation. Could not get the existing User ID from the DB.";
									}
								else // Else, this is a duplicate IPN. Must stop here.
									{
										$paypal["s2member_log"][] = "Not processing. Duplicate IPN.";
										$paypal["s2member_log"][] = "s2Member `txn_type` identified as ( `subscr_cancel|recurring_payment_profile_cancel` ).";
										$paypal["s2member_log"][] = "Duplicate IPN. Already processed. This IPN will be ignored.";
									}
								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_paypal_notify_after_subscr_cancel", get_defined_vars ());
								unset($__refs, $__v);

								return apply_filters("c_ws_plugin__s2member_paypal_notify_in_subscr_or_rp_cancellation_w_level", $paypal, get_defined_vars ());
							}
						else return apply_filters("c_ws_plugin__s2member_paypal_notify_in_subscr_or_rp_cancellation_w_level", false, get_defined_vars ());
					}
			}
	}
