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

if(!class_exists('c_ws_plugin__s2member_paypal_notify_in_subscr_modify_w_level'))
{
	/**
	 * s2Member's PayPal IPN handler (inner processing routine).
	 *
	 * @package s2Member\PayPal
	 * @since 110720
	 */
	class c_ws_plugin__s2member_paypal_notify_in_subscr_modify_w_level
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

			if((!empty($paypal['txn_type']) && preg_match('/^subscr_modify$/i', $paypal['txn_type']))
			   && (!empty($paypal['item_number']) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['membership_item_number_w_level_regex'], $paypal['item_number']))
			   && (!empty($paypal['subscr_id'])) && (!empty($paypal['payer_email']))
			   && (!empty($paypal['subscr_baid']) || ($paypal['subscr_baid'] = $paypal['subscr_id']))
			   && (!empty($paypal['subscr_cid']) || ($paypal['subscr_cid'] = $paypal['subscr_id']))
			)
			{
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_notify_before_subscr_modify', get_defined_vars());
				unset($__refs, $__v);

				if(!get_transient($transient_ipn = 's2m_ipn_'.md5('s2member_transient_'.$_paypal_s)) && set_transient($transient_ipn, time(), 31556926 * 10))
				{
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `subscr_modify` ).';

					list ($paypal['level'], $paypal['ccaps']/*, $paypal['eotper'] */) = preg_split('/\:/', $paypal['item_number'], 2);

					$paypal['ip'] = (preg_match('/ip address/i', $paypal['option_name2']) && $paypal['option_selection2']) ? $paypal['option_selection2'] : '';
					$paypal['ip'] = (!$paypal['ip'] && preg_match('/^[a-z0-9]+~[0-9\.]+$/i', $paypal['invoice'])) ? preg_replace('/^[a-z0-9]+~/i', '', $paypal['invoice']) : $paypal['ip'];

					$paypal['period1']    = (preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['period1'] : '0 D'; // Defaults to '0 D' (zero days).
					$paypal['mc_amount1'] = (strlen($paypal['mc_amount1']) && $paypal['mc_amount1'] > 0) ? $paypal['mc_amount1'] : '0.00'; // '0.00'.

					$paypal['initial_term']    = (preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['period1'] : '0 D'; // Defaults to '0 D' (zero days).
					$paypal['initial']         = (strlen($paypal['mc_amount1']) && preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['mc_amount1'] : $paypal['mc_amount3'];
					$paypal['regular']         = $paypal['mc_amount3']; // This is the Regular Payment Amount that is charged to the Customer. Always required by PayPal.
					$paypal['regular_term']    = $paypal['period3']; // This is just set to keep a standard; this way both initial_term & regular_term are available.
					$paypal['recurring']       = ($paypal['recurring']) ? $paypal['mc_amount3'] : '0'; // If non-recurring, this should be zero, otherwise Regular.
					$paypal['currency']        = strtoupper($paypal['mc_currency']); // Normalize input currency.
					$paypal['currency_symbol'] = c_ws_plugin__s2member_utils_cur::symbol($paypal['currency']);

					$ipn_signup_vars = $paypal;
					unset($ipn_signup_vars['s2member_log']); // Create array of IPN signup vars w/o s2member_log.

					if(($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['subscr_id'])) && is_object($user = new WP_User ($user_id)) && $user->ID)
					{
						if(!$user->has_cap('administrator')) // Do NOT process this routine on Administrators.
						{
							$processing = $modifying = $during = TRUE; // Yes, we ARE processing this.

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_paypal_notify_during_before_subscr_modify', get_defined_vars());
							do_action('ws_plugin__s2member_during_collective_mods', $user_id, get_defined_vars(), 'ipn-upgrade-downgrade', 'modification', 's2member_level'.$paypal['level']);
							unset($__refs, $__v);

							$fields      = get_user_option('s2member_custom_fields', $user_id); // These will be needed in the routines below.
							$user_reg_ip = get_user_option('s2member_registration_ip', $user_id); // Original IP during Registration.
							$user_reg_ip = $paypal['ip'] = ($user_reg_ip) ? $user_reg_ip : $paypal['ip']; // Now merge conditionally.

							if(is_multisite() && !is_user_member_of_blog($user_id)) // Must have a Role on this Blog.
							{
								add_existing_user_to_blog(array('user_id' => $user_id, 'role' => 's2member_level'.$paypal['level']));
								$user = new WP_User ($user_id);
							}
							$current_role = c_ws_plugin__s2member_user_access::user_access_role($user);

							if($current_role !== 's2member_level'.$paypal['level']) // Only if we need to.
								$user->set_role('s2member_level'.$paypal['level']); // (upgrade/downgrade)

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

							delete_user_option($user_id, 's2member_auto_eot_time');

							$pr_times                           = get_user_option('s2member_paid_registration_times', $user_id);
							$pr_times['level']                  = (!$pr_times['level']) ? time() : $pr_times['level']; // Preserves existing.
							$pr_times['level'.$paypal['level']] = (!$pr_times['level'.$paypal['level']]) ? time() : $pr_times['level'.$paypal['level']];
							update_user_option($user_id, 's2member_paid_registration_times', $pr_times); // Update now.

							c_ws_plugin__s2member_user_notes::clear_user_note_lines($user_id, '/^Demoted by s2Member\:/');
							c_ws_plugin__s2member_user_notes::clear_user_note_lines($user_id, '/^Paid Subscr\. ID @ time of demotion\:/');

							$paypal['s2member_log'][] = 's2Member Level/Capabilities updated on Subscription modification.';

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

																																																				if(($msg = preg_replace('/%%user_first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->first_name), $msg)) && ($msg = preg_replace('/%%user_last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->last_name), $msg)))
																																																					if(($msg = preg_replace('/%%user_full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($user->first_name.' '.$user->last_name)), $msg)))
																																																						if(($msg = preg_replace('/%%user_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_email), $msg)))
																																																							if(($msg = preg_replace('/%%user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user->user_login), $msg)))
																																																								if(($msg = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_reg_ip), $msg)))
																																																									if(($msg = preg_replace('/%%user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($user_id), $msg)))
																																																									{
																																																										if(is_array($fields) && !empty($fields)) foreach($fields as $var => $val/* Custom Registration/Profile Fields. */)
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
																{
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
																								$paypal['s2member_log'][] = 'Storing Modification Tracking Codes into a Transient Queue. These will be processed on-site.';
																								set_transient('s2m_'.md5('s2member_transient_modification_tracking_codes_'.$paypal['subscr_id']), $code, 43200);
																							}
																						}
																}
							}
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_paypal_notify_during_subscr_modify', get_defined_vars());
							unset($__refs, $__v);
						}
						else $paypal['s2member_log'][] = 'Unable to modify Subscription. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access.';
					}
					else $paypal['s2member_log'][] = 'Unable to modify Subscription. Could not get the existing User ID from the DB.';
				}
				else // Else, this is a duplicate IPN. Must stop here.
				{
					$paypal['s2member_log'][] = 'Not processing. Duplicate IPN.';
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `subscr_modify` ).';
					$paypal['s2member_log'][] = 'Duplicate IPN. Already processed. This IPN will be ignored.';
				}
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_notify_after_subscr_modify', get_defined_vars());
				unset($__refs, $__v);

				return apply_filters('c_ws_plugin__s2member_paypal_notify_in_subscr_modify_w_level', $paypal, get_defined_vars());
			}
			else return apply_filters('c_ws_plugin__s2member_paypal_notify_in_subscr_modify_w_level', FALSE, get_defined_vars());
		}
	}
}
