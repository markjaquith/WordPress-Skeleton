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
 * @since 110815
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_paypal_notify_in_wa_ccaps_wo_level'))
{
	/**
	 * s2Member's PayPal IPN handler (inner processing routine).
	 *
	 * @package s2Member\PayPal
	 * @since 110815
	 */
	class c_ws_plugin__s2member_paypal_notify_in_wa_ccaps_wo_level
	{
		/**
		 * s2Member's PayPal IPN handler (inner processing routine).
		 *
		 * @package s2Member\PayPal
		 * @since 110815
		 *
		 * @param array $vars Required. An array of defined variables passed by {@link s2Member\PayPal\c_ws_plugin__s2member_paypal_notify_in::paypal_notify()}.
		 *
		 * @return array|bool The original ``$paypal`` array passed in (extracted) from ``$vars``, or false when conditions do NOT apply.
		 */
		public static function cp($vars = array()) // Conditional phase for ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.
		{
			extract($vars, EXTR_OVERWRITE | EXTR_REFS); // Extract all vars passed in from: ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.

			if((!empty($paypal['txn_type']) && preg_match('/^web_accept$/i', $paypal['txn_type']))
			   && (!empty($paypal['item_number']) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['membership_item_number_wo_level_regex'], $paypal['item_number']))
			   && (empty($paypal['payment_status']) || empty($payment_status_issues) || !preg_match($payment_status_issues, $paypal['payment_status']))
			   && (!empty($paypal['txn_id'])) && (!empty($paypal['payer_email']))
			   && (!empty($paypal['txn_baid']) || ($paypal['txn_baid'] = $paypal['txn_id']))
			   && (!empty($paypal['txn_cid']) || ($paypal['txn_cid'] = $paypal['txn_id']))
			)
			{
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_notify_before_new_ccaps', get_defined_vars());
				unset($__refs, $__v);

				if(!get_transient($transient_ipn = 's2m_ipn_'.md5('s2member_transient_'.$_paypal_s)) && set_transient($transient_ipn, time(), 31556926 * 10))
				{
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept` ) w/ update vars for Capabilities w/o Level.';

					list ($paypal['level'], $paypal['ccaps'], $paypal['eotper']) = preg_split('/\:/', $paypal['item_number'], 3);

					$paypal['ip'] = (preg_match('/ip address/i', $paypal['option_name2']) && $paypal['option_selection2']) ? $paypal['option_selection2'] : '';
					$paypal['ip'] = (!$paypal['ip'] && preg_match('/^[a-z0-9]+~[0-9\.]+$/i', $paypal['invoice'])) ? preg_replace('/^[a-z0-9]+~/i', '', $paypal['invoice']) : $paypal['ip'];

					$paypal['currency']        = strtoupper($paypal['mc_currency']); // Normalize input currency.
					$paypal['currency_symbol'] = c_ws_plugin__s2member_utils_cur::symbol($paypal['currency']);

					if(!empty($coupon['coupon_code']) && c_ws_plugin__s2member_utils_conds::pro_is_installed())
						{
							$coupon_class = new c_ws_plugin__s2member_pro_coupons();
							$coupon_class->update_uses($coupon['coupon_code']);
						}
					if(preg_match('/(referenc|associat|updat|upgrad)/i', $paypal['option_name1']) && $paypal['option_selection1']) // Must have this information for Capability additions.
					{
						if(($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['txn_id'], $paypal['option_selection1'])) && is_object($user = new WP_User ($user_id)) && $user->ID)
						{
							if(!$user->has_cap('administrator')) // Do NOT process this routine on Administrators.
							{
								$processing = $during = TRUE; // Yes, we ARE processing this.

								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_paypal_notify_during_before_new_ccaps', get_defined_vars());
								unset($__refs, $__v);

								$fields      = get_user_option('s2member_custom_fields', $user_id); // These will be needed in the routines below.
								$user_reg_ip = get_user_option('s2member_registration_ip', $user_id); // Original IP during Registration.
								$user_reg_ip = $paypal['ip'] = ($user_reg_ip) ? $user_reg_ip : $paypal['ip']; // Now merge conditionally.

								if(is_multisite() && !is_user_member_of_blog($user_id)) // Must have a Role on this Blog.
								{
									add_existing_user_to_blog(array('user_id' => $user_id, 'role' => get_option('default_role')));
									$user = new WP_User ($user_id);
								}
								if($paypal['ccaps'] && preg_match('/^-all/', str_replace('+', '', $paypal['ccaps'])))
									foreach($user->allcaps as $cap => $cap_enabled)
										if(preg_match('/^access_s2member_ccap_/', $cap))
											$user->remove_cap($ccap = $cap);

								if($paypal['ccaps'] && preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $paypal['ccaps'])))
									foreach(preg_split('/['."\r\n\t".'\s;,]+/', preg_replace('/^-all['."\r\n\t".'\s;,]*/', '', str_replace('+', '', $paypal['ccaps']))) as $ccap)
										if(strlen($ccap = trim(strtolower(preg_replace('/[^a-z_0-9]/i', '', $ccap)))))
											$user->add_cap('access_s2member_ccap_'.$ccap);

								if(!get_user_option('s2member_registration_ip', $user_id))
									update_user_option($user_id, 's2member_registration_ip', $paypal['ip']);

								if(!empty($coupon['full_coupon_code']) && c_ws_plugin__s2member_utils_conds::pro_is_installed())
								{
									$user_coupons = is_array($user_coupons = get_user_option('s2member_coupon_codes', $user_id)) ? $user_coupons : array();
									$user_coupons = array_unique(array_merge($user_coupons, (array)$coupon['full_coupon_code']));
									update_user_option($user_id, 's2member_coupon_codes', $user_coupons);
								}
								$paypal['s2member_log'][] = 's2Member Custom Capabilities updated w/ advanced update routines.';

								$sbj = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ccap_email_subject']; // The same for standard and w/ Pro-Forms.
								$msg = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ccap_email_message']; // The same for standard and w/ Pro-Forms.
								$rec = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ccap_email_recipients']; // The same for standard and w/ Pro-Forms.

								if(($rec = c_ws_plugin__s2member_utils_strings::fill_cvs($rec, $paypal['custom'])) && ($rec = preg_replace('/%%(?:subscr|txn)_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $rec)))
									if(($rec = preg_replace('/%%(?:subscr|txn)_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_baid']), $rec)) && ($rec = preg_replace('/%%(?:subscr|txn)_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_cid']), $rec)))
										if(($rec = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $rec)) && ($rec = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $rec)))
											if(($rec = preg_replace('/%%amount%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['mc_gross']), $rec)) && ($rec = preg_replace('/%%txn_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $rec)))
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

																							if(($sbj = c_ws_plugin__s2member_utils_strings::fill_cvs($sbj, $paypal['custom'])) && ($sbj = preg_replace('/%%(?:subscr|txn)_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $sbj)))
																								if(($sbj = preg_replace('/%%(?:subscr|txn)_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_baid']), $sbj)) && ($sbj = preg_replace('/%%(?:subscr|txn)_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_cid']), $sbj)))
																									if(($sbj = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $sbj)) && ($sbj = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $sbj)))
																										if(($sbj = preg_replace('/%%amount%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['mc_gross']), $sbj)) && ($sbj = preg_replace('/%%txn_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $sbj)))
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

																																						if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%(?:subscr|txn)_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $msg)))
																																							if(($msg = preg_replace('/%%(?:subscr|txn)_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_baid']), $msg)) && ($msg = preg_replace('/%%(?:subscr|txn)_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_cid']), $msg)))
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
																																																						foreach(c_ws_plugin__s2member_utils_strings::parse_emails($rec) as $recipient  /* Go through a possible list of recipients. */)
																																																							c_ws_plugin__s2member_email_configs::email_config().wp_mail($recipient, apply_filters('ws_plugin__s2member_capabilities_email_sbj', $sbj, get_defined_vars()), apply_filters('ws_plugin__s2member_capabilities_email_msg', $msg, get_defined_vars()), 'From: "'.preg_replace('/"/', '"', $GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_name']).'" <'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['reg_email_from_email'].'>'."\r\n".'Content-Type: text/plain; charset=UTF-8').c_ws_plugin__s2member_email_configs::email_config_release();

																																																						$paypal['s2member_log'][] = 'Capability Confirmation Email sent to: '.$rec.'.';
																																																					}
																																																				}
								if($processing && $_REQUEST['s2member_paypal_proxy'] && ($url = $_REQUEST['s2member_paypal_proxy_return_url'])) // A Proxy is requesting a Return URL?
								{
									if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace('/%%(?:subscr|txn)_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['txn_id'])), $url)))
										if(($url = preg_replace('/%%(?:subscr|txn)_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['txn_baid'])), $url)) && ($url = preg_replace('/%%(?:subscr|txn)_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['txn_cid'])), $url)))
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

																								if(($url = trim($url))) // Preserve remaining replacements.
																									// Because the parent routine may perform replacements too.
																									$paypal['s2member_paypal_proxy_return_url'] = $url;
																							}
									$paypal['s2member_log'][] = 'Capability Return, a Proxy Return URL is ready.';
								}
								if($processing && $GLOBALS['WS_PLUGIN__']['s2member']['o']['payment_notification_urls'])
								{
									foreach(preg_split('/['."\r\n\t".']+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['payment_notification_urls']) as $url)

										if(($url = c_ws_plugin__s2member_utils_strings::fill_cvs($url, $paypal['custom'], true)) && ($url = preg_replace('/%%(?:subscr|txn)_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['txn_id'])), $url)))
											if(($url = preg_replace('/%%(?:subscr|txn)_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['txn_baid'])), $url)) && ($url = preg_replace('/%%(?:subscr|txn)_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($paypal['txn_cid'])), $url)))
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
								if($processing && $GLOBALS['WS_PLUGIN__']['s2member']['o']['payment_notification_recipients'])
								{
									$msg = $sbj = '(s2Member / API Notification Email) - Payment';
									$msg .= "\n\n"; // Spacing in the message body.

									$msg .= 'currency: %%currency%%'."\n";
									$msg .= 'currency_symbol: %%currency_symbol%%'."\n";
									$msg .= 'amount: %%amount%%'."\n";
									$msg .= 'txn_id: %%txn_id%%'."\n";
									$msg .= 'txn_baid: %%txn_baid%%'."\n";
									$msg .= 'txn_cid: %%txn_cid%%'."\n";
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

									if(($msg = c_ws_plugin__s2member_utils_strings::fill_cvs($msg, $paypal['custom'])) && ($msg = preg_replace('/%%(?:subscr|txn)_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $msg)))
										if(($msg = preg_replace('/%%(?:subscr|txn)_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_baid']), $msg)) && ($msg = preg_replace('/%%(?:subscr|txn)_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_cid']), $msg)))
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
								if($processing && ($code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ccap_tracking_codes']))
								{
									if(($code = c_ws_plugin__s2member_utils_strings::fill_cvs($code, $paypal['custom'])) && ($code = preg_replace('/%%(?:subscr|txn)_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $code)))
										if(($code = preg_replace('/%%(?:subscr|txn)_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_baid']), $code)) && ($code = preg_replace('/%%(?:subscr|txn)_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_cid']), $code)))
											if(($code = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $code)) && ($code = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $code)))
												if(($code = preg_replace('/%%amount%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['mc_gross']), $code)) && ($code = preg_replace('/%%txn_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $code)))
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

																								if(($code = trim(preg_replace('/%%(.+?)%%/i', '', $code)))) // This gets stored into a Transient Queue.
																								{
																									$paypal['s2member_log'][] = 'Storing Payment Tracking Codes into a Transient Queue. These will be processed on-site.';
																									set_transient('s2m_'.md5('s2member_transient_ccap_tracking_codes_'.$paypal['txn_id']), $code, 43200);
																								}
																							}
								}
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_paypal_notify_during_new_ccaps', get_defined_vars());
								unset($__refs, $__v);
							}
							else $paypal['s2member_log'][] = 'Unable to add new Capabilities. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access.';
						}
						else $paypal['s2member_log'][] = 'Unable to add new Capabilities. Could not get the existing User ID from the DB. Please check the `on0` and `os0` variables in your Button Code.';
					}
					else $paypal['s2member_log'][] = 'Unable to add new Capabilities. Missing User/Member details. Please check the `on0` and `os0` variables in your Button Code.';
				}
				else // Else, this is a duplicate IPN. Must stop here.
				{
					$paypal['s2member_log'][] = 'Not processing. Duplicate IPN.';
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept` ) w/ update vars for Capabilities w/o Level.';
					$paypal['s2member_log'][] = 'Duplicate IPN. Already processed. This IPN will be ignored.';
				}
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_notify_after_new_ccaps', get_defined_vars());
				unset($__refs, $__v);

				return apply_filters('c_ws_plugin__s2member_paypal_notify_in_wa_ccaps_wo_level', $paypal, get_defined_vars());
			}
			else return apply_filters('c_ws_plugin__s2member_paypal_notify_in_wa_ccaps_wo_level', FALSE, get_defined_vars());
		}
	}
}
