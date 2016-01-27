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
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_paypal_notify_in_subscr_or_wa_w_level'))
{
	/**
	 * s2Member's PayPal IPN handler (inner processing routine).
	 *
	 * @package s2Member\PayPal
	 * @since 110720
	 */
	class c_ws_plugin__s2member_paypal_notify_in_subscr_or_wa_w_level
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
		public static function cp($vars = array() /* Conditional phase for ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``. */)
		{
			extract($vars, EXTR_OVERWRITE | EXTR_REFS); // Extract all vars passed in from: ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.

			if((!empty($paypal['txn_type']) && preg_match('/^(web_accept|subscr_signup)$/i', $paypal['txn_type']))
			   && (!empty($paypal['item_number']) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['membership_item_number_w_level_regex'], $paypal['item_number']))
			   && (!empty($paypal['subscr_id']) || (!empty($paypal['txn_id']) && ($paypal['subscr_id'] = $paypal['txn_id'])))
			   && (empty($paypal['payment_status']) || empty($payment_status_issues) || !preg_match($payment_status_issues, $paypal['payment_status']))
			   && (!empty($paypal['subscr_baid']) || ($paypal['subscr_baid'] = $paypal['subscr_id']))
			   && (!empty($paypal['subscr_cid']) || ($paypal['subscr_cid'] = $paypal['subscr_id']))
			   && (!empty($paypal['payer_email']))
			)
			{
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_notify_before_subscr_signup', get_defined_vars());
				unset($__refs, $__v);

				if(!get_transient($transient_ipn = 's2m_ipn_'.md5('s2member_transient_'.$_paypal_s)) && set_transient($transient_ipn, time(), 31556926 * 10))
				{
					$processing = $modifying = $during = FALSE; // Initialize these flags.

					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept|subscr_signup` ).';

					@list ($paypal['level'], $paypal['ccaps'], $paypal['eotper']) = preg_split('/\:/', $paypal['item_number'], 3);

					$paypal['ip'] = (preg_match('/ip address/i', $paypal['option_name2']) && $paypal['option_selection2']) ? $paypal['option_selection2'] : '';
					$paypal['ip'] = (!$paypal['ip'] && preg_match('/^[a-z0-9]+~[0-9\.]+$/i', $paypal['invoice'])) ? preg_replace('/^[a-z0-9]+~/i', '', $paypal['invoice']) : $paypal['ip'];

					$paypal['period1']    = (isset($paypal['period1']) && preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['period1'] : '0 D';
					$paypal['mc_amount1'] = (isset($paypal['mc_amount1']) && $paypal['mc_amount1'] > 0) ? $paypal['mc_amount1'] : '0.00';

					if(preg_match('/^web_accept$/i', $paypal['txn_type']) /* Conversions for lifetime & fixed-term sales. */)
					{
						$paypal['period3']    = ($paypal['eotper']) ? $paypal['eotper'] : '1 L'; // 1 lifetime.
						$paypal['mc_amount3'] = $paypal['mc_gross']; // The 'Buy Now' amount is the full gross.
					}
					$paypal['initial_term']    = (preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['period1'] : '0 D';
					$paypal['initial']         = isset($paypal['mc_amount1'][0]) && preg_match('/^[1-9]/', $paypal['period1']) ? $paypal['mc_amount1'] : $paypal['mc_amount3'];
					$paypal['regular']         = $paypal['mc_amount3']; // This is the regular payment amount that is charged to the customer. always required by PayPal.
					$paypal['regular_term']    = $paypal['period3']; // This is just set to keep a standard; this way both initial_term & regular_term are available.
					$paypal['recurring']       = !empty($paypal['recurring']) ? $paypal['mc_amount3'] : '0'; // If non-recurring, this should be zero, otherwise regular.
					$paypal['currency']        = strtoupper($paypal['mc_currency']); // Normalize input currency.
					$paypal['currency_symbol'] = c_ws_plugin__s2member_utils_cur::symbol($paypal['currency']);

					if(!empty($coupon['coupon_code']) && c_ws_plugin__s2member_utils_conds::pro_is_installed())
						{
							$coupon_class = new c_ws_plugin__s2member_pro_coupons();
							$coupon_class->update_uses($coupon['coupon_code']);
						}
					$ipn_signup_vars = $paypal; // Create array of IPN signup vars w/o s2member_log.
					unset($ipn_signup_vars['s2member_log']);
					/*
					New Subscription with advanced update vars (option_name1, option_selection1)? These variables are used in Subscr. Modifications.
					*/
					if(preg_match('/(referenc|associat|updat|upgrad)/i', $paypal['option_name1']) && $paypal['option_selection1'])
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_notify_before_subscr_signup_w_update_vars', get_defined_vars());
						unset($__refs, $__v);

						$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept|subscr_signup` ) w/ update vars.';

						if(($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['subscr_id'], $paypal['option_selection1'])) && is_object($user = new WP_User ($user_id)) && $user->ID)
						{
							if(!$user->has_cap('administrator')) // Do NOT process this routine on Administrators.
							{
								$processing = $modifying = $during = TRUE; // Yes, we ARE processing this.

								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_paypal_notify_during_before_subscr_signup_w_update_vars', get_defined_vars());
								do_action('ws_plugin__s2member_during_collective_mods', $user_id, get_defined_vars(), 'ipn-upgrade-downgrade', 'modification', 's2member_level'.$paypal['level']);
								unset($__refs, $__v);

								$fields      = get_user_option('s2member_custom_fields', $user_id);
								$user_reg_ip = get_user_option('s2member_registration_ip', $user_id);
								$user_reg_ip = $paypal['ip'] = ($user_reg_ip) ? $user_reg_ip : $paypal['ip'];

								if(is_multisite() && !is_user_member_of_blog($user_id))
								{
									add_existing_user_to_blog(array('user_id' => $user_id, 'role' => 's2member_level'.$paypal['level']));
									$user = new WP_User ($user_id);
								}
								$current_role = c_ws_plugin__s2member_user_access::user_access_role($user);

								if($current_role !== 's2member_level'.$paypal['level'])
									$user->set_role('s2member_level'.$paypal['level']);

								if($paypal['ccaps'] && preg_match('/^-all/', str_replace('+', '', $paypal['ccaps'])))
									foreach($user->allcaps as $cap => $cap_enabled)
										if(preg_match('/^access_s2member_ccap_/', $cap))
											$user->remove_cap($ccap = $cap);

								if($paypal['ccaps'] && preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $paypal['ccaps'])))
									foreach(preg_split('/['."\r\n\t".'\s;,]+/', preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $paypal['ccaps']))) as $ccap)
										if(strlen($ccap = trim(strtolower(preg_replace('/[^a-z_0-9]/i', '', $ccap)))))
											$user->add_cap('access_s2member_ccap_'.$ccap);

								update_user_option($user_id, 's2member_subscr_gateway', $paypal['subscr_gateway']);
								update_user_option($user_id, 's2member_subscr_id', $paypal['subscr_id']);
								update_user_option($user_id, 's2member_subscr_baid', $paypal['subscr_baid']);
								update_user_option($user_id, 's2member_subscr_cid', $paypal['subscr_cid']);

								update_user_option($user_id, 's2member_custom', $paypal['custom']);

								if(!get_user_option('s2member_registration_ip', $user_id))
									update_user_option($user_id, 's2member_registration_ip', $paypal['ip']);

								update_user_option($user_id, 's2member_ipn_signup_vars', $ipn_signup_vars);

								delete_user_option($user_id, 's2member_file_download_access_log');

								if((preg_match('/^web_accept$/i', $paypal['txn_type']) || ($paypal['initial'] <= 0 && $paypal['regular'] <= 0)) && $paypal['eotper'])
								{
									update_user_option($user_id, 's2member_auto_eot_time', // Set exclusively by the IPN handler; to avoid duplicate extensions.
										($eot_time = c_ws_plugin__s2member_utils_time::auto_eot_time('', '', '', $paypal['eotper'], '', get_user_option('s2member_auto_eot_time', $user_id))));
									$paypal['s2member_log'][] = 'Automatic EOT (End Of Term) Time set to: '.date('D M j, Y g:i:s a T', $eot_time).'.';
								}
								else delete_user_option($user_id, 's2member_auto_eot_time');

								$pr_times                           = get_user_option('s2member_paid_registration_times', $user_id);
								$pr_times['level']                  = (!$pr_times['level']) ? time() : $pr_times['level']; // Preserve existing.
								$pr_times['level'.$paypal['level']] = (!$pr_times['level'.$paypal['level']]) ? time() : $pr_times['level'.$paypal['level']];
								update_user_option($user_id, 's2member_paid_registration_times', $pr_times);

								if(!empty($coupon['full_coupon_code']) && c_ws_plugin__s2member_utils_conds::pro_is_installed())
								{
									$user_coupons = is_array($user_coupons = get_user_option('s2member_coupon_codes', $user_id)) ? $user_coupons : array();
									$user_coupons = array_unique(array_merge($user_coupons, (array)$coupon['full_coupon_code']));
									update_user_option($user_id, 's2member_coupon_codes', $user_coupons);
									$processed_coupons = TRUE; // Flag for routines below.
								}
								c_ws_plugin__s2member_user_notes::clear_user_note_lines($user_id, '/^Demoted by s2Member\:/');
								c_ws_plugin__s2member_user_notes::clear_user_note_lines($user_id, '/^Paid Subscr\. ID @ time of demotion\:/');

								$paypal['s2member_log'][] = 's2Member Level/Capabilities updated w/ advanced update routines.';

								$sbj = $GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_email_subject']; // The same for standard and w/ Pro-Forms.
								$msg = $GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_email_message']; // The same for standard and w/ Pro-Forms.
								$rec = $GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_email_recipients']; // The same for standard and w/ Pro-Forms.

								if(($rec = c_ws_plugin__s2member_utils_strings::fill_cvs($rec, $paypal['custom'])) && ($rec = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $rec)))
									if(($rec = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $rec)) && ($rec = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $rec)))
										if(($rec = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $rec)) && ($rec = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $rec)))
											if(($rec = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $rec)) && ($rec = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $rec)))
												if(($rec = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $rec)) && ($rec = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $rec)))
													if(($rec = preg_replace('/%%initial_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['initial_term'])), $rec)) && ($rec = preg_replace('/%%regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], $paypal['recurring'])), $rec)))
														if(($rec = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $rec)) && ($rec = preg_replace('/%%recurring\/regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs((($paypal['recurring']) ? $paypal['recurring'].' / '.c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], TRUE) : '0 / non-recurring')), $rec)))
															if(($rec = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $rec)) && ($rec = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $rec)))
																if(($rec = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_dq(c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name'])), $rec)) && ($rec = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_dq(c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name'])), $rec)))
																	if(($rec = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_dq(c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name']))), $rec))) // **NOTE** c_ws_plugin__s2member_utils_strings::esc_dq() is applied here. (ex. 'N\'ame' <email>).
																		if(($rec = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $rec)))

																			if(($rec = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $rec)) && ($rec = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $rec)) && ($rec = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $rec)))

																				if(($rec = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->first_name), $rec)) && ($rec = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->last_name), $rec)))
																					if(($rec = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($user->first_name.' '.$user->last_name)), $rec)))
																						if(($rec = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_email), $rec)))
																							if(($rec = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_login), $rec)))
																								if(($rec = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $rec)))
																									if(($rec = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $rec)))

																										if(($sbj = c_ws_plugin__s2member_utils_strings::fill_cvs($sbj, $paypal['custom'])) && ($sbj = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $sbj)))
																											if(($sbj = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $sbj)) && ($sbj = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $sbj)))
																												if(($sbj = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $sbj)) && ($sbj = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $sbj)))
																													if(($sbj = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $sbj)) && ($sbj = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $sbj)))
																														if(($sbj = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $sbj)) && ($sbj = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $sbj)))
																															if(($sbj = preg_replace('/%%initial_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['initial_term'])), $sbj)) && ($sbj = preg_replace('/%%regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], $paypal['recurring'])), $sbj)))
																																if(($sbj = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $sbj)) && ($sbj = preg_replace('/%%recurring\/regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs((($paypal['recurring']) ? $paypal['recurring'].' / '.c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], TRUE) : '0 / non-recurring')), $sbj)))
																																	if(($sbj = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $sbj)) && ($sbj = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $sbj)))
																																		if(($sbj = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $sbj)) && ($sbj = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $sbj)))
																																			if(($sbj = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $sbj)))
																																				if(($sbj = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $sbj)))

																																					if(($sbj = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $sbj)) && ($sbj = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $sbj)) && ($sbj = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $sbj)))

																																						if(($sbj = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->first_name), $sbj)) && ($sbj = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->last_name), $sbj)))
																																							if(($sbj = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($user->first_name.' '.$user->last_name)), $sbj)))
																																								if(($sbj = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_email), $sbj)))
																																									if(($sbj = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_login), $sbj)))
																																										if(($sbj = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $sbj)))
																																											if(($sbj = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $sbj)))

																																												if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $msg)))
																																													if(($msg = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $msg)) && ($msg = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $msg)))
																																														if(($msg = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $msg)) && ($msg = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $msg)))
																																															if(($msg = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $msg)) && ($msg = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $msg)))
																																																if(($msg = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $msg)) && ($msg = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $msg)))
																																																	if(($msg = preg_replace('/%%initial_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['initial_term'])), $msg)) && ($msg = preg_replace('/%%regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], $paypal['recurring'])), $msg)))
																																																		if(($msg = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $msg)) && ($msg = preg_replace('/%%recurring\/regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs((($paypal['recurring']) ? $paypal['recurring'].' / '.c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], TRUE) : '0 / non-recurring')), $msg)))
																																																			if(($msg = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $msg)) && ($msg = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $msg)))
																																																				if(($msg = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $msg)) && ($msg = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $msg)))
																																																					if(($msg = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $msg)))
																																																						if(($msg = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $msg)))

																																																							if(($msg = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $msg)))

																																																								if(($msg = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->first_name), $msg)) && ($msg = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->last_name), $msg)))
																																																									if(($msg = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($user->first_name.' '.$user->last_name)), $msg)))
																																																										if(($msg = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_email), $msg)))
																																																											if(($msg = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_login), $msg)))
																																																												if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $msg)))
																																																													if(($msg = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $msg)))
																																																													{
																																																														if(is_array($fields) && !empty($fields)) foreach($fields as $var => $val /* Custom Registration/Profile Fields. */)
																																																														{
																																																															$rec = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $rec);
																																																															$sbj = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $sbj);
																																																															$msg = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $msg);
																																																														}
																																																														if(($rec = trim(preg_replace('/%%(.+?)%%/i', '', $rec))) && ($sbj = trim(preg_replace('/%%(.+?)%%/i', '', $sbj))) && ($msg = trim(preg_replace('/%%(.+?)%%/i', '', $msg))))
																																																														{
																																																															if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site())
																																																															{
																																																																$sbj = c_ws_plugin__s2member_utilities::evl($sbj, get_defined_vars());
																																																																$msg = c_ws_plugin__s2member_utilities::evl($msg, get_defined_vars());
																																																															}
																																																															foreach(c_ws_plugin__s2member_utils_strings::parse_emails($rec) as $recipient /* Go through a possible list of recipients. */)
																																																																c_ws_plugin__s2member_email_configs::email_config().wp_mail($recipient, apply_filters('ws_plugin__s2member_modification_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_modification_email_msg', $msg, get_defined_vars()), 'From: "'.preg_replace('/"/', "'", $GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_name']).'" <'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_email'].'>'."\r\n".'Content-Type: text/plain; charset=UTF-8').c_ws_plugin__s2member_email_configs::email_config_release();

																																																															$paypal['s2member_log'][] = 'Modification Confirmation Email sent to: '.$rec.'.';
																																																														}
																																																													}
								if($processing && $GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_notification_urls'])
								{
									foreach(preg_split('/['."\r\n\t".']+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_notification_urls']) as $url)

										if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_id'])), $url)))
											if(($url = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_baid'])), $url)) && ($url = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_cid'])), $url)))
												if(($url = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency'])), $url)) && ($url = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency_symbol'])), $url)))
													if(($url = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['initial'])), $url)) && ($url = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['regular'])), $url)) && ($url = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['recurring'])), $url)))
														if(($url = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['initial_term'])), $url)) && ($url = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['regular_term'])), $url)))
															if(($url = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_number'])), $url)) && ($url = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_name'])), $url)))
																if(($url = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['first_name'])), $url)) && ($url = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['last_name'])), $url)))
																	if(($url = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($paypal['first_name'].' '.$paypal['last_name']))), $url)))
																		if(($url = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['payer_email'])), $url)))

																			if(($url = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['full_coupon_code'])), $url)) && ($url = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['coupon_code'])), $url)) && ($url = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['affiliate_id'])), $url)))

																				if(($url = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->first_name)), $url)) && ($url = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->last_name)), $url)))
																					if(($url = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($user->first_name.' '.$user->last_name))), $url)))
																						if(($url = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->user_email)), $url)))
																							if(($url = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->user_login)), $url)))
																								if(($url = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_reg_ip)), $url)))
																									if(($url = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_id)), $url)))
																									{
																										if(is_array($fields) && !empty($fields))
																											foreach($fields as $var => $val /* Custom Registration/Profile Fields. */)
																												if(!($url = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(maybe_serialize($val))), $url)))
																													break;

																										if(($url = trim(preg_replace('/%%(.+?)%%/i', '', $url))))
																											c_ws_plugin__s2member_utils_urls::remote($url);
																									}
									$paypal['s2member_log'][] = 'Modification Notification URLs have been processed.';
								}
								if($processing && $GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_notification_recipients'])
								{
									$msg = $sbj = '(s2Member / API Notification Email) - Modification';
									$msg .= "\n\n"; // Spacing in the message body.

									$msg .= 'subscr_id: %%subscr_id%%'."\n";
									$msg .= 'subscr_baid: %%subscr_baid%%'."\n";
									$msg .= 'subscr_cid: %%subscr_cid%%'."\n";
									$msg .= 'currency: %%currency%%'."\n";
									$msg .= 'currency_symbol: %%currency_symbol%%'."\n";
									$msg .= 'initial: %%initial%%'."\n";
									$msg .= 'regular: %%regular%%'."\n";
									$msg .= 'recurring: %%recurring%%'."\n";
									$msg .= 'initial_term: %%initial_term%%'."\n";
									$msg .= 'regular_term: %%regular_term%%'."\n";
									$msg .= 'item_number: %%item_number%%'."\n";
									$msg .= 'item_name: %%item_name%%'."\n";
									$msg .= 'first_name: %%first_name%%'."\n";
									$msg .= 'last_name: %%last_name%%'."\n";
									$msg .= 'full_name: %%full_name%%'."\n";
									$msg .= 'payer_email: %%payer_email%%'."\n";

									$msg .= 'full_coupon_code: %%full_coupon_code%%'."\n";
									$msg .= 'coupon_code: %%coupon_code%%'."\n";
									$msg .= 'coupon_affiliate_id: %%coupon_affiliate_id%%'."\n";

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

									if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $msg)))
										if(($msg = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $msg)) && ($msg = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $msg)))
											if(($msg = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $msg)) && ($msg = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $msg)))
												if(($msg = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $msg)) && ($msg = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $msg)) && ($msg = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $msg)))
													if(($msg = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $msg)) && ($msg = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $msg)))
														if(($msg = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $msg)) && ($msg = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $msg)))
															if(($msg = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $msg)) && ($msg = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $msg)))
																if(($msg = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $msg)))
																	if(($msg = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $msg)))

																		if(($msg = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $msg)))

																			if(($msg = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->first_name), $msg)) && ($msg = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->last_name), $msg)))
																				if(($msg = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($user->first_name.' '.$user->last_name)), $msg)))
																					if(($msg = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_email), $msg)))
																						if(($msg = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_login), $msg)))
																							if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $msg)))
																								if(($msg = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $msg)))
																								{
																									if(is_array($fields) && !empty($fields))
																										foreach($fields as $var => $val /* Custom Registration/Profile Fields. */)
																											if(!($msg = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $msg)))
																												break;

																									if($sbj && ($msg = trim(preg_replace('/%%(.+?)%%/i', '', $msg)))) // Still have a ``$sbj`` and a ``$msg``?

																										foreach(c_ws_plugin__s2member_utils_strings::parse_emails($GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_notification_recipients']) as $recipient)
																											wp_mail($recipient, apply_filters('ws_plugin__s2member_modification_notification_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_modification_notification_email_msg', $msg, get_defined_vars()), 'Content-Type: text/plain; charset=UTF-8');
																								}
									$paypal['s2member_log'][] = 'Modification Notification Emails have been processed.';
								}
								if($processing && ($code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_tracking_codes']))
								{
									if(($code = c_ws_plugin__s2member_utils_strings::fill_cvs($code, $paypal['custom'])) && ($code = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $code)))
										if(($code = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $code)) && ($code = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $code)))
											if(($code = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $code)) && ($code = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $code)))
												if(($code = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $code)) && ($code = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $code)) && ($code = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $code)))
													if(($code = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $code)) && ($code = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $code)))
														if(($code = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $code)) && ($code = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $code)))
															if(($code = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $code)) && ($code = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $code)))
																if(($code = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $code)))
																	if(($code = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $code)))

																		if(($code = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $code)) && ($code = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $code)) && ($code = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $code)))

																			if(($code = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->first_name), $code)) && ($code = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->last_name), $code)))
																				if(($code = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($user->first_name.' '.$user->last_name)), $code)))
																					if(($code = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_email), $code)))
																						if(($code = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_login), $code)))
																							if(($code = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $code)))
																								if(($code = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $code)))
																								{
																									if(is_array($fields) && !empty($fields))
																										foreach($fields as $var => $val /* Custom Registration/Profile Fields. */)
																											if(!($code = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $code)))
																												break;

																									if(($code = trim(preg_replace('/%%(.+?)%%/i', '', $code))) /* This gets stored into a transient queue. */)
																									{
																										$paypal['s2member_log'][] = 'Storing Modification Tracking Codes into a Transient Queue. These will be processed on-site.';
																										set_transient('s2m_'.md5('s2member_transient_modification_tracking_codes_'.$paypal['subscr_id']), $code, 43200);
																									}
																								}
								}
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_signup_w_update_vars', get_defined_vars());
								unset($__refs, $__v);
							}
							else $paypal['s2member_log'][] = 'Unable to modify Subscription. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access.';
						}
						else $paypal['s2member_log'][] = 'Unable to modify Subscription. Could not get the existing User ID from the DB. Please check the `on0` and `os0` variables in your Button Code.';

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_notify_after_subscr_signup_w_update_vars', get_defined_vars());
						unset($__refs, $__v);
					}
					/*
					New Subscription. Normal Subscription signup, we are not updating anything for a past Subscription.
					*/
					else // Else this is a normal Subscription signup, we are not updating anything.
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_notify_before_subscr_signup_wo_update_vars', get_defined_vars());
						unset($__refs, $__v);

						$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept|subscr_signup` ) w/o update vars.';

						if(($registration_url = c_ws_plugin__s2member_register_access::register_link_gen($paypal['subscr_gateway'], $paypal['subscr_id'], $paypal['custom'], $paypal['item_number'])))
						{
							$processing = $during = TRUE; // Yes, we ARE processing this.

							$sbj = preg_replace('/%%registration_url%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($registration_url), $GLOBALS['WS_PLUGIN__']['s2member']['o'][(($_REQUEST['s2member_paypal_proxy'] && preg_match('/pro-emails/', $_REQUEST['s2member_paypal_proxy_use'])) ? 'pro_' : '').'signup_email_subject']);
							$msg = preg_replace('/%%registration_url%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($registration_url), $GLOBALS['WS_PLUGIN__']['s2member']['o'][(($_REQUEST['s2member_paypal_proxy'] && preg_match('/pro-emails/', $_REQUEST['s2member_paypal_proxy_use'])) ? 'pro_' : '').'signup_email_message']);
							$rec = preg_replace('/%%registration_url%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($registration_url), $GLOBALS['WS_PLUGIN__']['s2member']['o'][(($_REQUEST['s2member_paypal_proxy'] && preg_match('/pro-emails/', $_REQUEST['s2member_paypal_proxy_use'])) ? 'pro_' : '').'signup_email_recipients']);

							if(($rec = c_ws_plugin__s2member_utils_strings::fill_cvs($rec, $paypal['custom'])) && ($rec = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $rec)))
								if(($rec = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $rec)) && ($rec = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $rec)))
									if(($rec = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $rec)) && ($rec = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $rec)))
										if(($rec = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $rec)) && ($rec = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $rec)))
											if(($rec = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $rec)) && ($rec = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $rec)))
												if(($rec = preg_replace('/%%initial_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['initial_term'])), $rec)) && ($rec = preg_replace('/%%regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], $paypal['recurring'])), $rec)))
													if(($rec = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $rec)) && ($rec = preg_replace('/%%recurring\/regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs((($paypal['recurring']) ? $paypal['recurring'].' / '.c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], TRUE) : '0 / non-recurring')), $rec)))
														if(($rec = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $rec)) && ($rec = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $rec)))
															if(($rec = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_dq(c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name'])), $rec)) && ($rec = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_dq(c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name'])), $rec)))
																if(($rec = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_dq(c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name']))), $rec))) // **NOTE** c_ws_plugin__s2member_utils_strings::esc_dq() is applied here. (ex. "N\"ame" <email>).
																	if(($rec = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $rec)))
																		if(($rec = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['ip']), $rec)))
																			if(($rec = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $rec)) && ($rec = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $rec)) && ($rec = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $rec)))

																				if(($sbj = c_ws_plugin__s2member_utils_strings::fill_cvs($sbj, $paypal['custom'])) && ($sbj = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $sbj)))
																					if(($sbj = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $sbj)) && ($sbj = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $sbj)))
																						if(($sbj = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $sbj)) && ($sbj = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $sbj)))
																							if(($sbj = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $sbj)) && ($sbj = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $sbj)))
																								if(($sbj = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $sbj)) && ($sbj = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $sbj)))
																									if(($sbj = preg_replace('/%%initial_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['initial_term'])), $sbj)) && ($sbj = preg_replace('/%%regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], $paypal['recurring'])), $sbj)))
																										if(($sbj = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $sbj)) && ($sbj = preg_replace('/%%recurring\/regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs((($paypal['recurring']) ? $paypal['recurring'].' / '.c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], TRUE) : '0 / non-recurring')), $sbj)))
																											if(($sbj = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $sbj)) && ($sbj = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $sbj)))
																												if(($sbj = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $sbj)) && ($sbj = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $sbj)))
																													if(($sbj = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $sbj)))
																														if(($sbj = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $sbj)))
																															if(($sbj = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['ip']), $sbj)))
																																if(($sbj = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $sbj)) && ($sbj = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $sbj)) && ($sbj = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $sbj)))

																																	if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $msg)))
																																		if(($msg = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $msg)) && ($msg = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $msg)))
																																			if(($msg = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $msg)) && ($msg = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $msg)))
																																				if(($msg = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $msg)) && ($msg = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $msg)))
																																					if(($msg = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $msg)) && ($msg = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $msg)))
																																						if(($msg = preg_replace('/%%initial_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['initial_term'])), $msg)) && ($msg = preg_replace('/%%regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], $paypal['recurring'])), $msg)))
																																							if(($msg = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $msg)) && ($msg = preg_replace('/%%recurring\/regular_cycle%%/i', c_ws_plugin__s2member_utils_strings::esc_refs((($paypal['recurring']) ? $paypal['recurring'].' / '.c_ws_plugin__s2member_utils_time::period_term($paypal['regular_term'], TRUE) : '0 / non-recurring')), $msg)))
																																								if(($msg = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $msg)) && ($msg = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $msg)))
																																									if(($msg = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $msg)) && ($msg = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $msg)))
																																										if(($msg = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $msg)))
																																											if(($msg = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $msg)))
																																												if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['ip']), $msg)))
																																													if(($msg = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $msg)))

																																														if(($rec = trim(preg_replace('/%%(.+?)%%/i', '', $rec))) && ($sbj = trim(preg_replace('/%%(.+?)%%/i', '', $sbj))) && ($msg = trim(preg_replace('/%%(.+?)%%/i', '', $msg))))
																																														{
																																															if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site())
																																															{
																																																$sbj = c_ws_plugin__s2member_utilities::evl($sbj, get_defined_vars());
																																																$msg = c_ws_plugin__s2member_utilities::evl($msg, get_defined_vars());
																																															}
																																															foreach(c_ws_plugin__s2member_utils_strings::parse_emails($rec) as $recipient) // Go through a possible list of recipients.
																																																c_ws_plugin__s2member_email_configs::email_config().wp_mail($recipient, apply_filters('ws_plugin__s2member_signup_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_signup_email_msg', $msg, get_defined_vars()), 'From: "'.preg_replace('/"/', "'", $GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_name']).'" <'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_email'].'>'."\r\n".'Content-Type: text/plain; charset=UTF-8').c_ws_plugin__s2member_email_configs::email_config_release();

																																															$paypal['s2member_log'][] = 'Signup Confirmation Email sent to: '.$rec.'.';
																																														}
							if($processing && $GLOBALS['WS_PLUGIN__']['s2member']['o']['signup_notification_urls'])
							{
								foreach(preg_split('/['."\r\n\t".']+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['signup_notification_urls']) as $url)

									if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_id'])), $url)))
										if(($url = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_baid'])), $url)) && ($url = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_cid'])), $url)))
											if(($url = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency'])), $url)) && ($url = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency_symbol'])), $url)))
												if(($url = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['initial'])), $url)) && ($url = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['regular'])), $url)) && ($url = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['recurring'])), $url)))
													if(($url = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['initial_term'])), $url)) && ($url = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['regular_term'])), $url)))
														if(($url = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_number'])), $url)) && ($url = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_name'])), $url)))
															if(($url = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['first_name'])), $url)) && ($url = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['last_name'])), $url)))
																if(($url = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($paypal['first_name'].' '.$paypal['last_name']))), $url)))
																	if(($url = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['payer_email'])), $url)))
																		if(($url = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['ip'])), $url)))
																			if(($url = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['full_coupon_code'])), $url)) && ($url = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['coupon_code'])), $url)) && ($url = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['affiliate_id'])), $url)))

																				if(($url = trim(preg_replace('/%%(.+?)%%/i', '', $url))))
																					c_ws_plugin__s2member_utils_urls::remote($url);

								$paypal['s2member_log'][] = 'Signup Notification URLs have been processed.';
							}
							if($processing && $GLOBALS['WS_PLUGIN__']['s2member']['o']['signup_notification_recipients'])
							{
								$msg = $sbj = '(s2Member / API Notification Email) - Signup';
								$msg .= "\n\n"; // Spacing in the message body.

								$msg .= 'subscr_id: %%subscr_id%%'."\n";
								$msg .= 'subscr_baid: %%subscr_baid%%'."\n";
								$msg .= 'subscr_cid: %%subscr_cid%%'."\n";
								$msg .= 'currency: %%currency%%'."\n";
								$msg .= 'currency_symbol: %%currency_symbol%%'."\n";
								$msg .= 'initial: %%initial%%'."\n";
								$msg .= 'regular: %%regular%%'."\n";
								$msg .= 'recurring: %%recurring%%'."\n";
								$msg .= 'initial_term: %%initial_term%%'."\n";
								$msg .= 'regular_term: %%regular_term%%'."\n";
								$msg .= 'item_number: %%item_number%%'."\n";
								$msg .= 'item_name: %%item_name%%'."\n";
								$msg .= 'first_name: %%first_name%%'."\n";
								$msg .= 'last_name: %%last_name%%'."\n";
								$msg .= 'full_name: %%full_name%%'."\n";
								$msg .= 'payer_email: %%payer_email%%'."\n";
								$msg .= 'user_ip: %%user_ip%%'."\n";

								$msg .= 'full_coupon_code: %%full_coupon_code%%'."\n";
								$msg .= 'coupon_code: %%coupon_code%%'."\n";
								$msg .= 'coupon_affiliate_id: %%coupon_affiliate_id%%'."\n";

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

								if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $msg)))
									if(($msg = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $msg)) && ($msg = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $msg)))
										if(($msg = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $msg)) && ($msg = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $msg)))
											if(($msg = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $msg)) && ($msg = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $msg)) && ($msg = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $msg)))
												if(($msg = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $msg)) && ($msg = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $msg)))
													if(($msg = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $msg)) && ($msg = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $msg)))
														if(($msg = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $msg)) && ($msg = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $msg)))
															if(($msg = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $msg)))
																if(($msg = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $msg)))
																	if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['ip']), $msg)))
																		if(($msg = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $msg)))

																			if($sbj && ($msg = trim(preg_replace('/%%(.+?)%%/i', '', $msg)))) // Still have a ``$sbj`` and a ``$msg``?

																				foreach(c_ws_plugin__s2member_utils_strings::parse_emails($GLOBALS['WS_PLUGIN__']['s2member']['o']['signup_notification_recipients']) as $recipient)
																					wp_mail($recipient, apply_filters('ws_plugin__s2member_signup_notification_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_signup_notification_email_msg', $msg, get_defined_vars()), 'Content-Type: text/plain; charset=UTF-8');

								$paypal['s2member_log'][] = 'Signup Notification Emails have been processed.';
							}
							if($processing && ($code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['signup_tracking_codes']))
							{
								if(($code = c_ws_plugin__s2member_utils_strings::fill_cvs($code, $paypal['custom'])) && ($code = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $code)))
									if(($code = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $code)) && ($code = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $code)))
										if(($code = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $code)) && ($code = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $code)))
											if(($code = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial']), $code)) && ($code = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular']), $code)) && ($code = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['recurring']), $code)))
												if(($code = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['initial_term']), $code)) && ($code = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['regular_term']), $code)))
													if(($code = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $code)) && ($code = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $code)))
														if(($code = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $code)) && ($code = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $code)))
															if(($code = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $code)))
																if(($code = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $code)))
																	if(($code = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['ip']), $code)))
																		if(($code = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $code)) && ($code = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $code)) && ($code = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $code)))

																			if(($code = trim(preg_replace('/%%(.+?)%%/i', '', $code))) /* This gets stored into a Transient Queue. */)
																			{
																				$paypal['s2member_log'][] = 'Storing Signup Tracking Codes into a Transient Queue. These will be processed on-site.';
																				set_transient('s2m_'.md5('s2member_transient_signup_tracking_codes_'.$paypal['subscr_id']), $code, 43200);
																			}
							}
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_signup_wo_update_vars', get_defined_vars());
							unset($__refs, $__v);
						}
						else $paypal['s2member_log'][] = 'Unable to generate Registration URL for Membership Access. Possible data corruption within the IPN response.';

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_notify_after_subscr_signup_wo_update_vars', get_defined_vars());
						unset($__refs, $__v);
					}
					if($processing && $_REQUEST['s2member_paypal_proxy'] && ($url = $_REQUEST['s2member_paypal_proxy_return_url'])) // A Proxy is requesting a Return URL?
					{
						if((!empty($user_id) && !empty($user) && is_object($user) && $user->ID) || (($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['subscr_id'], $paypal['option_selection1'])) && is_object($user = new WP_User ($user_id)) && $user->ID))
						{
							$fields      = get_user_option('s2member_custom_fields', $user_id); // These will be needed in the routines below.
							$user_reg_ip = get_user_option('s2member_registration_ip', $user_id); // Original IP during Registration.

							if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_id'])), $url)))
								if(($url = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_baid'])), $url)) && ($url = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_cid'])), $url)))
									if(($url = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency'])), $url)) && ($url = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency_symbol'])), $url)))
										if(($url = preg_replace('/%%initial%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['initial'])), $url)) && ($url = preg_replace('/%%regular%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['regular'])), $url)) && ($url = preg_replace('/%%recurring%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['recurring'])), $url)))
											if(($url = preg_replace('/%%initial_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['initial_term'])), $url)) && ($url = preg_replace('/%%regular_term%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['regular_term'])), $url)))
												if(($url = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_number'])), $url)) && ($url = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_name'])), $url)))
													if(($url = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['first_name'])), $url)) && ($url = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['last_name'])), $url)))
														if(($url = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($paypal['first_name'].' '.$paypal['last_name']))), $url)))
															if(($url = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['payer_email'])), $url)))
																if(($url = preg_replace('/%%modification%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode((int)$modifying)), $url)))

																	if(($url = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['full_coupon_code'])), $url)) && ($url = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['coupon_code'])), $url)) && ($url = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['affiliate_id'])), $url)))

																		if(($url = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->first_name)), $url)) && ($url = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->last_name)), $url)))
																			if(($url = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($user->first_name.' '.$user->last_name))), $url)))
																				if(($url = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->user_email)), $url)))
																					if(($url = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->user_login)), $url)))
																						if(($url = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_reg_ip)), $url)))
																							if(($url = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_id)), $url)))
																							{
																								if(is_array($fields) && !empty($fields))
																									foreach($fields as $var => $val /* Custom Registration/Profile Fields. */)
																										if(!($url = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(maybe_serialize($val))), $url)))
																											break;

																								if(($url = trim($url))) // Preserve remaining replacements; parent routine may perform replacements too.
																									$paypal['s2member_paypal_proxy_return_url'] = $url;
																							}
						}
						$paypal['s2member_log'][] = 'Subscr. Return ( `modification='.(int)$modifying.'` ), a Proxy Return URL is ready.';
					}
					if($processing // Process a payment now? Special cases for web_accept and/or Proxy requests with `subscr-signup-as-subscr-payment`.
					   && (preg_match('/^web_accept$/i', $paypal['txn_type']) || ($_REQUEST['s2member_paypal_proxy'] && preg_match('/subscr-signup-as-subscr-payment/', $_REQUEST['s2member_paypal_proxy_use']) && $paypal['txn_id'] && $paypal['mc_gross'] > 0))
					   && ((!empty($user_id) && !empty($user) && is_object($user) && $user->ID) || (($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['subscr_id'], $paypal['option_selection1'])) && is_object($user = new WP_User ($user_id)) && $user->ID))
					)
					{
						$paypal['s2member_log'][] = 'User exists. Handling `payment` for Subscription via ( `'.((preg_match('/^web_accept$/i', $paypal['txn_type'])) ? 'web_accept' : 'subscr-signup-as-subscr-payment').'` ).';

						$pr_times                           = get_user_option('s2member_paid_registration_times', $user_id);
						$pr_times['level']                  = (!$pr_times['level']) ? time() : $pr_times['level']; // Preserve existing.
						$pr_times['level'.$paypal['level']] = (!$pr_times['level'.$paypal['level']]) ? time() : $pr_times['level'.$paypal['level']];
						update_user_option($user_id, 's2member_paid_registration_times', $pr_times);

						if(!get_user_option('s2member_first_payment_txn_id', $user_id) /* 1st payment? */)
							update_user_option($user_id, 's2member_first_payment_txn_id', $paypal['txn_id']);

						update_user_option($user_id, 's2member_last_payment_time', time()); // Update the last payment time.

						$fields      = get_user_option('s2member_custom_fields', $user_id); // These will be needed in the routines below.
						$user_reg_ip = get_user_option('s2member_registration_ip', $user_id); // Original IP during Registration.

						if(empty($processed_coupons) && !empty($coupon['full_coupon_code']) && c_ws_plugin__s2member_utils_conds::pro_is_installed())
							{
								$user_coupons = is_array($user_coupons = get_user_option('s2member_coupon_codes', $user_id)) ? $user_coupons : array();
								$user_coupons = array_unique(array_merge($user_coupons, (array)$coupon['full_coupon_code']));
								update_user_option($user_id, 's2member_coupon_codes', $user_coupons);
								$processed_coupons = TRUE; // Flag for routines below.
							}
						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['payment_notification_urls'])
						{
							foreach(preg_split('/['."\r\n\t".']+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['payment_notification_urls']) as $url)

								if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_id'])), $url)))
									if(($url = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_baid'])), $url)) && ($url = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['subscr_cid'])), $url)))
										if(($url = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency'])), $url)) && ($url = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['currency_symbol'])), $url)))
											if(($url = preg_replace('/%%amount%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['mc_gross'])), $url)) && ($url = preg_replace('/%%txn_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['txn_id'])), $url)))
												if(($url = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_number'])), $url)) && ($url = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['item_name'])), $url)))
													if(($url = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['first_name'])), $url)) && ($url = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['last_name'])), $url)))
														if(($url = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($paypal['first_name'].' '.$paypal['last_name']))), $url)))
															if(($url = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['payer_email'])), $url)))

																if(($url = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['full_coupon_code'])), $url)) && ($url = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['coupon_code'])), $url)) && ($url = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($coupon['affiliate_id'])), $url)))

																	if(($url = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->first_name)), $url)) && ($url = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->last_name)), $url)))
																		if(($url = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(trim($user->first_name.' '.$user->last_name))), $url)))
																			if(($url = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->user_email)), $url)))
																				if(($url = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user->user_login)), $url)))
																					if(($url = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_reg_ip)), $url)))
																						if(($url = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_id)), $url)))
																						{
																							if(is_array($fields) && !empty($fields))
																								foreach($fields as $var => $val /* Custom Registration/Profile Fields. */)
																									if(!($url = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode(maybe_serialize($val))), $url)))
																										break;

																							if(($url = trim(preg_replace('/%%(.+?)%%/i', '', $url))))
																								c_ws_plugin__s2member_utils_urls::remote($url);
																						}
							$paypal['s2member_log'][] = 'Payment Notification URLs have been processed.';
						}
						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['payment_notification_recipients'])
						{
							$msg = $sbj = '(s2Member / API Notification Email) - Payment';
							$msg .= "\n\n"; // Spacing in the message body.

							$msg .= 'subscr_id: %%subscr_id%%'."\n";
							$msg .= 'subscr_baid: %%subscr_baid%%'."\n";
							$msg .= 'subscr_cid: %%subscr_cid%%'."\n";
							$msg .= 'currency: %%currency%%'."\n";
							$msg .= 'currency_symbol: %%currency_symbol%%'."\n";
							$msg .= 'amount: %%amount%%'."\n";
							$msg .= 'txn_id: %%txn_id%%'."\n";
							$msg .= 'item_number: %%item_number%%'."\n";
							$msg .= 'item_name: %%item_name%%'."\n";
							$msg .= 'first_name: %%first_name%%'."\n";
							$msg .= 'last_name: %%last_name%%'."\n";
							$msg .= 'full_name: %%full_name%%'."\n";
							$msg .= 'payer_email: %%payer_email%%'."\n";

							$msg .= 'full_coupon_code: %%full_coupon_code%%'."\n";
							$msg .= 'coupon_code: %%coupon_code%%'."\n";
							$msg .= 'coupon_affiliate_id: %%coupon_affiliate_id%%'."\n";

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

							if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%subscr_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_id']), $msg)))
								if(($msg = preg_replace('/%%subscr_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_baid']), $msg)) && ($msg = preg_replace('/%%subscr_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['subscr_cid']), $msg)))
									if(($msg = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $msg)) && ($msg = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $msg)))
										if(($msg = preg_replace('/%%amount%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['mc_gross']), $msg)) && ($msg = preg_replace('/%%txn_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $msg)))
											if(($msg = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $msg)) && ($msg = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $msg)))
												if(($msg = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $msg)) && ($msg = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $msg)))
													if(($msg = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $msg)))
														if(($msg = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $msg)))

															if(($msg = preg_replace('/%%full_coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['full_coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_code%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['coupon_code']), $msg)) && ($msg = preg_replace('/%%coupon_affiliate_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($coupon['affiliate_id']), $msg)))

																if(($msg = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->first_name), $msg)) && ($msg = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->last_name), $msg)))
																	if(($msg = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($user->first_name.' '.$user->last_name)), $msg)))
																		if(($msg = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_email), $msg)))
																			if(($msg = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_login), $msg)))
																				if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $msg)))
																					if(($msg = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $msg)))
																					{
																						if(is_array($fields) && !empty($fields))
																							foreach($fields as $var => $val /* Custom Registration/Profile Fields. */)
																								if(!($msg = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $msg)))
																									break;

																						if($sbj && ($msg = trim(preg_replace('/%%(.+?)%%/i', '', $msg)))) // Still have a ``$sbj`` and a ``$msg``?

																							foreach(c_ws_plugin__s2member_utils_strings::parse_emails($GLOBALS['WS_PLUGIN__']['s2member']['o']['payment_notification_recipients']) as $recipient)
																								wp_mail($recipient, apply_filters('ws_plugin__s2member_payment_notification_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_payment_notification_email_msg', $msg, get_defined_vars()), 'Content-Type: text/plain; charset=UTF-8');
																					}
							$paypal['s2member_log'][] = 'Payment Notification Emails have been processed.';
						}
					}
					else if($processing // Process a payment now? Special cases for web_accept and/or Proxy requests with `subscr-signup-as-subscr-payment`.
					        && (preg_match('/^web_accept$/i', $paypal['txn_type']) || ($_REQUEST['s2member_paypal_proxy'] && preg_match('/subscr-signup-as-subscr-payment/', $_REQUEST['s2member_paypal_proxy_use']) && $paypal['txn_id'] && $paypal['mc_gross'] > 0))
					)
					{
						$paypal['s2member_log'][] = 'Storing `payment` for Subscription via ( `'.((preg_match('/^web_accept$/i', $paypal['txn_type'])) ? 'web_accept' : 'subscr-signup-as-subscr-payment').'` ).';

						$ipn = array('txn_type' => 'subscr_payment'); // Create a simulated IPN response for txn_type=subscr_payment.

						foreach($paypal as $var => $val)
							if(in_array($var, array('subscr_gateway', 'subscr_id', 'subscr_baid', 'subscr_cid', 'txn_id', 'custom', 'invoice', 'mc_gross', 'mc_currency', 'tax', 'payer_email', 'first_name', 'last_name', 'item_name', 'item_number', 'option_name1', 'option_selection1', 'option_name2', 'option_selection2')))
								$ipn[$var] = $val;

						$paypal['s2member_log'][] = 'Creating an IPN response for `subscr_payment`. This will go into a Transient Queue; and be processed during registration.';

						set_transient('s2m_'.md5('s2member_transient_ipn_subscr_payment_'.$paypal['subscr_id']), $ipn, 43200);
					}
					if($processing // Store signup vars now? If the User already exists in the database, we can go ahead and store these right now.
					   && ((!empty($user_id) && !empty($user) && is_object($user) && $user->ID) || (($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['subscr_id'], $paypal['option_selection1'])) && is_object($user = new WP_User ($user_id)) && $user->ID))
					)
					{
						$paypal['s2member_log'][] = 'Storing IPN signup vars now. These are associated with a User\'s account record; for future reference.';

						update_user_option($user_id, 's2member_ipn_signup_vars', $ipn_signup_vars);
					}
					else if($processing) // Otherwise, we can store these into a Transient Queue for registration processing.
					{
						$paypal['s2member_log'][] = 'Storing IPN signup vars into a Transient Queue. These will be processed on registration.';

						set_transient('s2m_'.md5('s2member_transient_ipn_signup_vars_'.$paypal['subscr_id']), $ipn_signup_vars, 43200);
					}
				}
				else // Else, this is a duplicate IPN. Must stop here.
				{
					$paypal['s2member_log'][] = 'Not processing. Duplicate IPN.';
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept|subscr_signup` ).';
					$paypal['s2member_log'][] = 'Duplicate IPN. Already processed. This IPN will be ignored.';
				}
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_notify_after_subscr_signup', get_defined_vars());
				unset($__refs, $__v);

				return apply_filters('c_ws_plugin__s2member_paypal_notify_in_subscr_or_wa_w_level', $paypal, get_defined_vars());
			}
			return apply_filters('c_ws_plugin__s2member_paypal_notify_in_subscr_or_wa_w_level', FALSE, get_defined_vars());
		}
	}
}
