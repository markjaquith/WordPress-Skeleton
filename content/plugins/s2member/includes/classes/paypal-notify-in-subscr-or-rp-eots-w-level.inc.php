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
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_paypal_notify_in_subscr_or_rp_eots_w_level'))
{
	/**
	 * s2Member's PayPal IPN handler (inner processing routine).
	 *
	 * @package s2Member\PayPal
	 * @since 110720
	 */
	class c_ws_plugin__s2member_paypal_notify_in_subscr_or_rp_eots_w_level
	{
		/**
		 * s2Member's PayPal IPN handler (inner processing routine).
		 *
		 * @package s2Member\PayPal
		 * @since 110720
		 *
		 * @param array $vars Required. An array of defined variables passed by {@link s2Member\PayPal\c_ws_plugin__s2member_paypal_notify_in::paypal_notify()}.
		 *
		 * @return array|bool The original ``$paypal`` array passed in (extracted) from ``$vars``, or false when conditions do NOT apply.
		 */
		public static function cp($vars = array()) // Conditional phase for ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.
		{
			extract($vars, EXTR_OVERWRITE | EXTR_REFS); // Extract all vars passed in from: ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.

			if(((!empty($paypal['txn_type']) && preg_match('/^(subscr_eot|recurring_payment_expired|recurring_payment_suspended_due_to_max_failed_payment)$/i', $paypal['txn_type']) && ($recurring = TRUE))
			    || (!empty($paypal['txn_type']) && preg_match('/^recurring_payment_profile_cancel$/i', $paypal['txn_type']) && !empty($paypal['initial_payment_status']) && preg_match('/^failed$/i', $paypal['initial_payment_status']) && ($recurring = TRUE))
			    || (!empty($paypal['txn_type']) && preg_match('/^new_case$/i', $paypal['txn_type']) && !empty($paypal['case_type']) && preg_match('/^chargeback$/i', $paypal['case_type']) && !($recurring = FALSE)) // Seeking this for future compatibility.
			    || (!empty($paypal['payment_status']) && preg_match('/^(refunded|reversed|reversal)$/i', $paypal['payment_status']) && !($recurring = FALSE))) // The `txn_type` is irrelevant in all of these payment statuses: `refunded|reversed|reversal`.
			   && (!empty($paypal['subscr_id']) || ($paypal['subscr_id'] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_subscr_id($paypal)) || (!empty($paypal['parent_txn_id']) && ($paypal['subscr_id'] = $paypal['parent_txn_id']))) // Other MUST haves.
			   && (!empty($paypal['period1']) || ($paypal['period1'] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_period1($paypal, FALSE)) || empty($recurring) || ($paypal['period1'] = c_ws_plugin__s2member_utils_users::get_user_ipn_signup_var('period1', FALSE, $paypal['subscr_id'])) || ($paypal['period1'] = '0 D'))
			   && (!empty($paypal['period3']) || ($paypal['period3'] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_period3($paypal, FALSE)) || empty($recurring) || ($paypal['period3'] = c_ws_plugin__s2member_utils_users::get_user_ipn_signup_var('period3', FALSE, $paypal['subscr_id'])) || ($paypal['period3'] = '1 D'))
			   && ((!empty($paypal['item_number']) || ($paypal['item_number'] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_item_number($paypal)) || ($paypal['item_number'] = c_ws_plugin__s2member_utils_users::get_user_ipn_signup_var('item_number', FALSE, $paypal['subscr_id'])) || ($paypal['item_number'] = '1')) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['membership_item_number_w_level_regex'], $paypal['item_number']))
			   && (!empty($paypal['item_name']) || ($paypal['item_name'] = c_ws_plugin__s2member_paypal_utilities::paypal_pro_item_name($paypal)) || ($paypal['item_name'] = c_ws_plugin__s2member_utils_users::get_user_ipn_signup_var('item_name', FALSE, $paypal['subscr_id'])) || ($paypal['item_name'] = $_SERVER['HTTP_HOST']))
			   && (!empty($paypal['payer_email']) || ($paypal['payer_email'] = c_ws_plugin__s2member_utils_users::get_user_ipn_signup_var('payer_email', FALSE, $paypal['subscr_id'])) || ($paypal['payer_email'] = c_ws_plugin__s2member_utils_users::get_user_email_with($paypal['subscr_id'])))
			   && (!empty($paypal['subscr_baid']) || ($paypal['subscr_baid'] = $paypal['subscr_id']))
			   && (!empty($paypal['subscr_cid']) || ($paypal['subscr_cid'] = $paypal['subscr_id']))
			)
			{
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_notify_before_subscr_eot', get_defined_vars());
				unset($__refs, $__v); // Housekeeping.

				if(!get_transient($transient_ipn = 's2m_ipn_'.md5('s2member_transient_'.$_paypal_s)) && set_transient($transient_ipn, time(), 31556926 * 10))
				{
					$is_refund             = (preg_match('/^refunded$/i', $paypal['payment_status']) && !empty($paypal['parent_txn_id']));
					$is_reversal           = (preg_match('/^(reversed|reversal)$/i', $paypal['payment_status']) && !empty($paypal['parent_txn_id']));
					$is_reversal           = (!$is_reversal) ? (preg_match('/^new_case$/i', $paypal['txn_type']) && preg_match('/^chargeback$/i', $paypal['case_type'])) : $is_reversal;
					$is_refund_or_reversal = ($is_refund || $is_reversal); // If either of the previous tests above evaluated to true; then it's obviously a Refund and/or a Reversal.
					$is_partial_refund     = // Partial refund detection. All refunds processed against Subscriptions are considered partials. Full refunds occur only against Buy Now transactions.
						(!$is_refund || (!empty($paypal['mc_gross']) && ($original_txn_type = c_ws_plugin__s2member_utils_users::get_user_ipn_signup_var('txn_type', FALSE, $paypal['subscr_id'])) === 'web_accept'
						                 && ($original_mc_gross = c_ws_plugin__s2member_utils_users::get_user_ipn_signup_var('mc_gross', FALSE, $paypal['subscr_id'])) <= abs($paypal['mc_gross']))) ? FALSE : TRUE;
					$is_delayed_eot        = (!$is_refund_or_reversal && preg_match('/^(subscr_eot|recurring_payment_expired)$/i', $paypal['txn_type']) && preg_match('/^I-/i', $paypal['subscr_id']));

					if($is_refund_or_reversal)
						$paypal['s2member_log'][] = 's2Member `txn_type` identified as '.($identified_as = '( `[empty or irrelevant]` ) w/ `payment_status` ( `refunded|reversed|reversal` ) - or - `new_case` w/ `case_type` ( `chargeback` )').'.';
					else $paypal['s2member_log'][] = 's2Member `txn_type` identified as '.($identified_as = '( `subscr_eot|recurring_payment_expired|recurring_payment_suspended_due_to_max_failed_payment` ) - or - `recurring_payment_profile_cancel` w/ `initial_payment_status` ( `failed` )').'.';

					if(empty($_REQUEST['s2member_paypal_proxy'])) // Only on true PayPal IPNs; e.g., we can bypass this on proxied IPNs.
					{
						$paypal['s2member_log'][] = 'Sleeping for 15 seconds. Waiting for a possible ( `subscr_signup|subscr_modify|recurring_payment_profile_created` ).';
						sleep(15); // Sleep here for a moment. PayPal sometimes sends a subscr_eot before the subscr_signup, subscr_modify.
						$paypal['s2member_log'][] = 'Awake. It\'s '.date('D M j, Y g:i:s a T').'. s2Member `txn_type` identified as '.$identified_as.'.';
					}
					$paypal['ip'] = (preg_match('/ip address/i', $paypal['option_name2']) && $paypal['option_selection2']) ? $paypal['option_selection2'] : '';
					$paypal['ip'] = (!$paypal['ip'] && preg_match('/^[a-z0-9]+~[0-9\.]+$/i', $paypal['invoice'])) ? preg_replace('/^[a-z0-9]+~/i', '', $paypal['invoice']) : $paypal['ip'];

					$paypal['currency']        = strtoupper($paypal['mc_currency']); // Normalize input currency.
					$paypal['currency_symbol'] = c_ws_plugin__s2member_utils_cur::symbol($paypal['currency']);

					if(($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['subscr_id'])) && is_object($user = new WP_User($user_id)) && !empty($user->ID))
					{
						$fields      = get_user_option('s2member_custom_fields', $user_id); // These will be needed below.
						$user_reg_ip = get_user_option('s2member_registration_ip', $user_id); // Needed below.
						$user_reg_ip = $paypal['ip'] = ($user_reg_ip) ? $user_reg_ip : $paypal['ip'];

						if((!$is_refund_or_reversal && !$is_delayed_eot && !get_user_option('s2member_auto_eot_time', $user_id))
						   || ($is_refund_or_reversal && $is_partial_refund && $GLOBALS['WS_PLUGIN__']['s2member']['o']['triggers_immediate_eot'] === 'refunds,partial_refunds,reversals')
						   || ($is_refund_or_reversal && !$is_partial_refund && $GLOBALS['WS_PLUGIN__']['s2member']['o']['triggers_immediate_eot'] === 'refunds,partial_refunds,reversals')
						   || ($is_refund_or_reversal && !$is_partial_refund && $GLOBALS['WS_PLUGIN__']['s2member']['o']['triggers_immediate_eot'] === 'refunds,reversals')
						   || ($is_refund && !$is_partial_refund && $GLOBALS['WS_PLUGIN__']['s2member']['o']['triggers_immediate_eot'] === 'refunds')
						   || ($is_reversal && $GLOBALS['WS_PLUGIN__']['s2member']['o']['triggers_immediate_eot'] === 'reversals')
						)
						{
							if(!$user->has_cap('administrator')) // Do NOT process this routine on Administrators.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['auto_eot_system_enabled']) // EOT enabled?
								{
									if($GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_eot_behavior'] === 'demote')
									{
										$processing = $during = TRUE; // Yes, we ARE processing this.

										$eot_del_type = ($is_refund_or_reversal) ? // Set EOT/Del type.
											'ipn-refund-reversal-demotion' : 'ipn-cancellation-expiration-demotion';

										$demotion_role = c_ws_plugin__s2member_option_forces::force_demotion_role('subscriber');
										$existing_role = c_ws_plugin__s2member_user_access::user_access_role($user);

										foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
										do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_eot_before_demote', get_defined_vars());
										do_action('ws_plugin__s2member_during_collective_mods', $user_id, get_defined_vars(), $eot_del_type, 'modification', $demotion_role);
										do_action('ws_plugin__s2member_during_collective_eots', $user_id, get_defined_vars(), $eot_del_type, 'modification');
										unset($__refs, $__v); // Housekeeping.

										if($existing_role !== $demotion_role) // Only if NOT the existing Role.
											$user->set_role($demotion_role); // Give User the demotion Role.

										if(apply_filters('ws_plugin__s2member_remove_ccaps_during_eot_events', (bool)$GLOBALS['WS_PLUGIN__']['s2member']['o']['eots_remove_ccaps'] || $is_refund_or_reversal, get_defined_vars()))
											foreach($user->allcaps as $cap => $cap_enabled)
												if(preg_match('/^access_s2member_ccap_/', $cap))
													$user->remove_cap($ccap = $cap);

										delete_user_option($user_id, 's2member_subscr_gateway');
										delete_user_option($user_id, 's2member_subscr_id');
										delete_user_option($user_id, 's2member_subscr_baid');
										delete_user_option($user_id, 's2member_subscr_cid');

										delete_user_option($user_id, 's2member_ipn_signup_vars');
										if(!apply_filters('ws_plugin__s2member_preserve_paid_registration_times', TRUE))
											delete_user_option($user_id, 's2member_paid_registration_times');

										delete_user_option($user_id, 's2member_last_status_scan');
										delete_user_option($user_id, 's2member_first_payment_txn_id');
										delete_user_option($user_id, 's2member_last_payment_time');
										delete_user_option($user_id, 's2member_last_auto_eot_time');
										delete_user_option($user_id, 's2member_auto_eot_time');

										delete_user_option($user_id, 's2member_file_download_access_log');
										delete_user_option($user_id, 's2member_authnet_payment_failures');

										update_user_option($user_id, 's2member_last_auto_eot_time', time());

										c_ws_plugin__s2member_user_notes::append_user_notes($user_id, 'Demoted by s2Member: '.date('D M j, Y g:i a T'));
										c_ws_plugin__s2member_user_notes::append_user_notes($user_id, 'Paid Subscr. ID @ time of demotion: '.$paypal['subscr_gateway'].' → '.$paypal['subscr_id']);

										$paypal['s2member_log'][] = 'Member Level/Capabilities demoted to: '.ucwords(preg_replace('/_/', ' ', $demotion_role)).'.';

										if($processing && $GLOBALS['WS_PLUGIN__']['s2member']['o']['eot_del_notification_urls'])
										{
											foreach(preg_split('/['."\r\n\t".']+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['eot_del_notification_urls']) as $url) // Handle EOT Notifications.

												if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace('/%%eot_del_type%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($eot_del_type)), $url)) && ($url = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_id'])), $url)))
													if(($url = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_baid'])), $url)) && ($url = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_cid'])), $url)))
														if(($url = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->first_name)), $url)) && ($url = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->last_name)), $url)))
															if(($url = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($user->first_name.' '.$user->last_name))), $url)))
																if(($url = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->user_email)), $url)))
																	if(($url = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->user_login)), $url)))
																		if(($url = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_reg_ip)), $url)))
																			if(($url = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_id)), $url)))
																			{
																				if(is_array($fields) && !empty($fields))
																					foreach($fields as $var => $val) // Custom Registration/Profile Fields.
																						if(!($url = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(maybe_serialize($val))), $url)))
																							break;

																				if(($url = trim(preg_replace('/%%(.+?)%%/i', '', $url))))
																					c_ws_plugin__s2member_utils_urls::remote($url);
																			}
											$paypal['s2member_log'][] = 'EOT/Deletion Notification URLs have been processed.';
										}
										if($processing && $GLOBALS['WS_PLUGIN__']['s2member']['o']['eot_del_notification_recipients'])
										{
											$msg = $sbj = '(s2Member / API Notification Email) - EOT/Deletion';
											$msg .= "\n\n"; // Spacing in the message body.

											$msg .= 'eot_del_type: %%eot_del_type%%'."\n";
											$msg .= 'subscr_id: %%subscr_id%%'."\n";
											$msg .= 'subscr_baid: %%subscr_baid%%'."\n";
											$msg .= 'subscr_cid: %%subscr_cid%%'."\n";
											$msg .= 'user_first_name: %%user_first_name%%'."\n";
											$msg .= 'user_last_name: %%user_last_name%%'."\n";
											$msg .= 'user_full_name: %%user_full_name%%'."\n";
											$msg .= 'user_email: %%user_email%%'."\n";
											$msg .= 'user_login: %%user_login%%'."\n";
											$msg .= 'user_ip: %%user_ip%%'."\n";
											$msg .= 'user_id: %%user_id%%'."\n";

											if(is_array($fields) && !empty($fields))
												foreach($fields as $var => $val)
													$msg .= $var.': %%'.$var.'%%'."\n";

											$msg .= 'cv0: %%cv0%%'."\n";
											$msg .= 'cv1: %%cv1%%'."\n";
											$msg .= 'cv2: %%cv2%%'."\n";
											$msg .= 'cv3: %%cv3%%'."\n";
											$msg .= 'cv4: %%cv4%%'."\n";
											$msg .= 'cv5: %%cv5%%'."\n";
											$msg .= 'cv6: %%cv6%%'."\n";
											$msg .= 'cv7: %%cv7%%'."\n";
											$msg .= 'cv8: %%cv8%%'."\n";
											$msg .= 'cv9: %%cv9%%';

											if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%eot_del_type%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($eot_del_type), $msg)) && ($msg = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $msg)))
												if(($msg = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $msg)) && ($msg = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $msg)))
													if(($msg = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->first_name), $msg)) && ($msg = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->last_name), $msg)))
														if(($msg = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($user->first_name.' '.$user->last_name)), $msg)))
															if(($msg = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_email), $msg)))
																if(($msg = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_login), $msg)))
																	if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $msg)))
																		if(($msg = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $msg)))
																		{
																			if(is_array($fields) && !empty($fields))
																				foreach($fields as $var => $val) // Custom Registration/Profile Fields.
																					if(!($msg = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $msg)))
																						break;

																			if($sbj && ($msg = trim(preg_replace('/%%(.+?)%%/i', '', $msg)))) // Still have a ``$sbj`` and a ``$msg``?

																				foreach(c_ws_plugin__s2member_utils_strings::parse_emails($GLOBALS['WS_PLUGIN__']['s2member']['o']['eot_del_notification_recipients']) as $recipient)
																					wp_mail($recipient, apply_filters('ws_plugin__s2member_eot_del_notification_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_eot_del_notification_email_msg', $msg, get_defined_vars()), 'Content-Type: text/plain; charset=UTF-8');
																		}
											$paypal['s2member_log'][] = 'EOT/Deletion Notification Emails have been processed.';
										}
										foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
										do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_eot_demote', get_defined_vars());
										unset($__refs, $__v); // Housekeeping.
									}
									else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_eot_behavior'] === 'delete')
									{
										$processing = $during = TRUE; // Yes, we ARE processing this.

										$eot_del_type = $GLOBALS['ws_plugin__s2member_eot_del_type'] = // Configure EOT/Del type.
											($is_refund_or_reversal) ? 'ipn-refund-reversal-deletion' : 'ipn-cancellation-expiration-deletion';

										foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
										do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_eot_before_delete', get_defined_vars());
										do_action('ws_plugin__s2member_during_collective_eots', $user_id, get_defined_vars(), $eot_del_type, 'removal-deletion');
										unset($__refs, $__v); // Housekeeping.

										if(is_multisite()) // Multisite does NOT actually delete; ONLY removes.
										{
											remove_user_from_blog($user_id, $current_blog->blog_id);
											// This will automatically trigger `eot_del_notification_urls` as well.
											c_ws_plugin__s2member_user_deletions::handle_ms_user_deletions($user_id, $current_blog->blog_id, 's2says');
										}
										else // Otherwise, we can actually delete them.
											// This will automatically trigger `eot_del_notification_urls` as well.
											wp_delete_user($user_id); // `c_ws_plugin__s2member_user_deletions::handle_user_deletions()`

										$paypal['s2member_log'][] = 'This Member\'s account has been '.((is_multisite()) ? 'removed' : 'deleted').'.';

										$paypal['s2member_log'][] = 'EOT/Deletion Notification URLs have been processed.';

										foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
										do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_eot_delete', get_defined_vars());
										unset($__refs, $__v); // Housekeeping.
									}
									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_eot', get_defined_vars());
									unset($__refs, $__v); // Housekeeping.
								}
								else // Otherwise, treat this as if it were a cancellation. EOTs are currently disabled.
								{
									$processing = $during = TRUE; // Yes, we ARE processing this.

									update_user_option($user_id, 's2member_auto_eot_time', ($auto_eot_time = strtotime('now')));

									$paypal['s2member_log'][] = 'Auto-EOT is currently disabled. Skipping EOT (demote|delete), for now.';
									$paypal['s2member_log'][] = 'Recording the Auto-EOT Time for this Member\'s account: '.date('D M j, Y g:i a T', $auto_eot_time);

									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_eot_disabled', get_defined_vars());
									unset($__refs, $__v); // Housekeeping.
								}
							}
							else $paypal['s2member_log'][] = 'Unable to (demote|delete) Member. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access.';
						}
						else if($is_delayed_eot && !get_user_option('s2member_auto_eot_time', $user_id))
						{
							if(!$user->has_cap('administrator')) // Do NOT process this routine on Administrators.
							{
								$processing = $during = TRUE; // Yes, we ARE processing this.

								$auto_eot_time = c_ws_plugin__s2member_utils_time::auto_eot_time($user_id, $paypal['period1'], $paypal['period3'], '', time());
								/* We assume the last payment was today, because this is how newer PayPal accounts function with respect to EOT handling.
								Newer PayPal accounts ( i.e., Subscription IDs starting with `I-`, will have their EOT triggered upon the last payment. */
								update_user_option($user_id, 's2member_auto_eot_time', $auto_eot_time); // s2Member will follow-up on this later.

								$paypal['s2member_log'][] = 'Auto-EOT Time for this account (delayed), set to: '.date('D M j, Y g:i a T', $auto_eot_time);

								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_eot_delayed', get_defined_vars());
								unset($__refs, $__v); // Housekeeping.
							}
							else $paypal['s2member_log'][] = 'Ignoring Delayed EOT. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access.';
						}
						else if(!$is_refund_or_reversal || $is_delayed_eot)
							$paypal['s2member_log'][] = 'Skipping (demote|delete) Member, for now. An Auto-EOT Time is already set for this account. When an Auto-EOT Time has been recorded, s2Member will handle EOT (demote|delete) events using it\'s own Auto-EOT System - internally.';

						else if($is_refund && $is_partial_refund)
							$paypal['s2member_log'][] = 'Skipping (demote|delete) Member. Your configuration dictates that s2Member should NOT take any immediate action on an EOT associated with a Partial Refund. An s2Member API Notification will still be processed however.';

						else if($is_refund && !$is_partial_refund)
							$paypal['s2member_log'][] = 'Skipping (demote|delete) Member. Your configuration dictates that s2Member should NOT take any immediate action on an EOT associated with a Full Refund. An s2Member API Notification will still be processed however.';

						else if($is_reversal)
							$paypal['s2member_log'][] = 'Skipping (demote|delete) Member. Your configuration dictates that s2Member should NOT take any immediate action on an EOT associated with a Chargeback Reversal. An s2Member API Notification will still be processed however.';
					}
					else if($is_delayed_eot) // Otherwise, we need to re-generate/store this IPN into a Transient Queue. Then re-process it on registration.
					{
						$paypal['s2member_log'][] = 'Skipping this IPN response, for now. The Subscr. ID is not associated with a registered Member.';

						$ipn = array('txn_type' => 'subscr_eot'); // Create a simulated IPN response for txn_type=subscr_eot.

						foreach($paypal as $var => $val)
							if(in_array($var, array('subscr_gateway', 'subscr_id', 'subscr_baid', 'subscr_cid', 'custom', 'invoice', 'payer_email', 'first_name', 'last_name', 'item_name', 'item_number', /* Exclude; might be defaults. 'period1', 'period3', */
							                        'option_name1', 'option_selection1', 'option_name2', 'option_selection2')))
								$ipn[$var] = $val;

						$paypal['s2member_log'][] = 'Re-generating. This IPN will go into a Transient Queue; and be re-processed during registration.';

						set_transient('s2m_'.md5('s2member_transient_ipn_subscr_eot_'.$paypal['subscr_id']), $ipn, 43200);
					}
					else $paypal['s2member_log'][] = 'Unable to (demote|delete) Member. Could not get the existing User ID from the DB. It\'s possible that it was ALREADY processed through another IPN, removed manually by a Site Administrator, or by s2Member\'s Auto-EOT Sys.';
					/*
					Refunds and chargeback reversals. This is excluded from the processing check, because a Member *could* have already been (demoted|deleted).
					In other words, s2Member sends `Refund/Reversal` Notifications ANYTIME a Refund/Reversal occurs; even if s2Member did not process it otherwise.
					Since this routine ignores the processing check, it is *possible* that Refund/Reversal Notification URLs will be contacted more than once.
						If you're writing scripts that depend on Refund/Reversal Notifications, please keep this in mind.
					*/
					if($is_refund_or_reversal) // Previously assigned as a quick method of Refund/Reversal detection.
					{
						$fields      = ($user_id) ? get_user_option('s2member_custom_fields', $user_id) : array(); // These will be needed below.
						$user_reg_ip = ($user_id) ? get_user_option('s2member_registration_ip', $user_id) : ''; // Needed below.
						$user_reg_ip = $paypal['ip'] = ($user_reg_ip) ? $user_reg_ip : $paypal['ip']; // Now merge conditionally.

						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['ref_rev_notification_urls'])
						{
							foreach(preg_split('/['."\r\n\t".']+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['ref_rev_notification_urls']) as $url)

								if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_id'])), $url)) && ($url = preg_replace('/%%parent_txn_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['parent_txn_id'])), $url)))
									if(($url = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_baid'])), $url)) && ($url = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_cid'])), $url)))
										if(($url = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_number'])), $url)) && ($url = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_name'])), $url)))
											if(($url = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency'])), $url)) && ($url = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency_symbol'])), $url)))
												if(($url = preg_replace('/%%-amount%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['mc_gross'])), $url)) && ($url = preg_replace('/%%-fee%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['mc_fee'])), $url)))
													if(($url = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['first_name'])), $url)) && ($url = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['last_name'])), $url)))
														if(($url = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($paypal['first_name'].' '.$paypal['last_name']))), $url)))
															if(($url = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['payer_email'])), $url)))
																if(($url = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_reg_ip)), $url)))
																	if(($url = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_id)), $url)))
																	{
																		if(is_array($fields) && !empty($fields))
																			foreach($fields as $var => $val) // Custom Registration/Profile Fields.
																				if(!($url = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(maybe_serialize($val))), $url)))
																					break;

																		if(($url = trim(preg_replace('/%%(.+?)%%/i', '', $url))))
																			c_ws_plugin__s2member_utils_urls::remote($url);
																	}
							$paypal['s2member_log'][] = 'Refund/Reversal Notification URLs have been processed.';
						}
						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['ref_rev_notification_recipients'])
						{
							$msg = $sbj = '(s2Member / API Notification Email) - Refund/Reversal';
							$msg .= "\n\n"; // Spacing in the message body.

							$msg .= 'subscr_id: %%subscr_id%%'."\n";
							$msg .= 'subscr_baid: %%subscr_baid%%'."\n";
							$msg .= 'subscr_cid: %%subscr_cid%%'."\n";
							$msg .= 'parent_txn_id: %%parent_txn_id%%'."\n";
							$msg .= 'item_number: %%item_number%%'."\n";
							$msg .= 'item_name: %%item_name%%'."\n";
							$msg .= 'currency: %%currency%%'."\n";
							$msg .= 'currency_symbol: %%currency_symbol%%'."\n";
							$msg .= '-amount: %%-amount%%'."\n";
							$msg .= '-fee: %%-fee%%'."\n";
							$msg .= 'first_name: %%first_name%%'."\n";
							$msg .= 'last_name: %%last_name%%'."\n";
							$msg .= 'full_name: %%full_name%%'."\n";
							$msg .= 'payer_email: %%payer_email%%'."\n";
							$msg .= 'user_ip: %%user_ip%%'."\n";
							$msg .= 'user_id: %%user_id%%'."\n";

							if(is_array($fields) && !empty($fields))
								foreach($fields as $var => $val)
									$msg .= $var.': %%'.$var.'%%'."\n";

							$msg .= 'cv0: %%cv0%%'."\n";
							$msg .= 'cv1: %%cv1%%'."\n";
							$msg .= 'cv2: %%cv2%%'."\n";
							$msg .= 'cv3: %%cv3%%'."\n";
							$msg .= 'cv4: %%cv4%%'."\n";
							$msg .= 'cv5: %%cv5%%'."\n";
							$msg .= 'cv6: %%cv6%%'."\n";
							$msg .= 'cv7: %%cv7%%'."\n";
							$msg .= 'cv8: %%cv8%%'."\n";
							$msg .= 'cv9: %%cv9%%';

							if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $msg)) && ($msg = preg_replace('/%%parent_txn_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['parent_txn_id']), $msg)))
								if(($msg = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $msg)) && ($msg = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $msg)))
									if(($msg = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $msg)) && ($msg = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $msg)))
										if(($msg = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $msg)) && ($msg = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $msg)))
											if(($msg = preg_replace('/%%-amount%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['mc_gross']), $msg)) && ($msg = preg_replace('/%%-fee%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['mc_fee']), $msg)))
												if(($msg = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $msg)) && ($msg = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $msg)))
													if(($msg = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $msg)))
														if(($msg = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $msg)))
															if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $msg)))
																if(($msg = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $msg)))
																{
																	if(is_array($fields) && !empty($fields))
																		foreach($fields as $var => $val) // Custom Registration/Profile Fields.
																			if(!($msg = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $msg)))
																				break;

																	if($sbj && ($msg = trim(preg_replace('/%%(.+?)%%/i', '', $msg)))) // Still have a ``$sbj`` and a ``$msg``?

																		foreach(c_ws_plugin__s2member_utils_strings::parse_emails($GLOBALS['WS_PLUGIN__']['s2member']['o']['ref_rev_notification_recipients']) as $recipient)
																			wp_mail($recipient, apply_filters('ws_plugin__s2member_ref_rev_notification_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_ref_rev_notification_email_msg', $msg, get_defined_vars()), 'Content-Type: text/plain; charset=UTF-8');
																}
							$paypal['s2member_log'][] = 'Refund/Reversal Notification Emails have been processed.';
						}
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_eot_refund_reversal', get_defined_vars());
						unset($__refs, $__v); // Housekeeping.
					}
				}
				else // Else, this is a duplicate IPN. Must stop here.
				{
					$paypal['s2member_log'][] = 'Not processing. Duplicate IPN.';
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as a type of EOT.';
					$paypal['s2member_log'][] = 'Duplicate IPN. Already processed. This IPN will be ignored.';
				}
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_notify_after_subscr_eot', get_defined_vars());
				unset($__refs, $__v); // Housekeeping.

				return apply_filters('c_ws_plugin__s2member_paypal_notify_in_subscr_or_rp_eots_w_level', $paypal, get_defined_vars());
			}
			else return apply_filters('c_ws_plugin__s2member_paypal_notify_in_subscr_or_rp_eots_w_level', FALSE, get_defined_vars());
		}
	}
}
