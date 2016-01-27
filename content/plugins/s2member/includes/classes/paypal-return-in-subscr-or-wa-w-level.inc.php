<?php
/**
 * s2Member's PayPal Auto-Return/PDT handler (inner processing routine).
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

if(!class_exists('c_ws_plugin__s2member_paypal_return_in_subscr_or_wa_w_level'))
{
	/**
	 * s2Member's PayPal Auto-Return/PDT handler (inner processing routine).
	 *
	 * @package s2Member\PayPal
	 * @since 110720
	 */
	class c_ws_plugin__s2member_paypal_return_in_subscr_or_wa_w_level
	{
		/**
		 * s2Member's PayPal Auto-Return/PDT handler (inner processing routine).
		 *
		 * @package s2Member\PayPal
		 * @since 110720
		 *
		 * @param array $vars Required. An array of defined variables passed by {@link s2Member\PayPal\c_ws_plugin__s2member_paypal_return_in::paypal_return()}.
		 *
		 * @return array|bool The original ``$paypal`` array passed in (extracted) from ``$vars``, or false when conditions do NOT apply.
		 */
		public static function cp($vars = array()) // Conditional phase for ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.
		{
			extract($vars, EXTR_OVERWRITE | EXTR_REFS); // Extract all vars passed in from: ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.

			if((!empty($paypal['txn_type']) && preg_match('/^(web_accept|subscr_signup|subscr_payment)$/i', $paypal['txn_type']))
			   && (!empty($paypal['item_number']) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['membership_item_number_w_level_regex'], $paypal['item_number']))
			   && (!empty($paypal['subscr_id']) || (!empty($paypal['txn_id']) && ($paypal['subscr_id'] = $paypal['txn_id'])))
			   && (!empty($paypal['subscr_baid']) || ($paypal['subscr_baid'] = $paypal['subscr_id']))
			   && (!empty($paypal['subscr_cid']) || ($paypal['subscr_cid'] = $paypal['subscr_id']))
			   && (empty($paypal['payment_status']) || empty($payment_status_issues) || !preg_match($payment_status_issues, $paypal['payment_status']))
			)
			{
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_return_before_subscr_signup', get_defined_vars());
				unset($__refs, $__v); // Housekeeping.

				if(!get_transient($transient_rtn = 's2m_rtn_'.md5('s2member_transient_'.$_paypal_s)) && set_transient($transient_rtn, time(), 31556926 * 10))
				{
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept|subscr_signup|subscr_payment` ).';

					@list($paypal['level'], $paypal['ccaps'], $paypal['eotper']) = preg_split('/\:/', $paypal['item_number'], 3);

					$paypal['ip'] = (preg_match('/ip address/i', $paypal['option_name2']) && $paypal['option_selection2']) ? $paypal['option_selection2'] : '';
					$paypal['ip'] = (!$paypal['ip'] && preg_match('/^[a-z0-9]+~[0-9\.]+$/i', $paypal['invoice'])) ? preg_replace('/^[a-z0-9]+~/i', '', $paypal['invoice']) : $paypal['ip'];
					$paypal['ip'] = (!$paypal['ip'] && $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $paypal['ip'];

					if((preg_match('/^subscr_payment$/i', $paypal['txn_type']) && !empty($_GET['s2member_paypal_return_tra']))
					   && (($tra = c_ws_plugin__s2member_utils_encryption::decrypt(trim(stripslashes($_GET['s2member_paypal_return_tra'])))) && is_array($tra = maybe_unserialize($tra)))
					   && (count($tra) === 11 && isset($tra['ta'], $tra['tp'], $tra['tt'], $tra['ra'], $tra['rp'], $tra['rt'], $tra['rr'], $tra['rrt'], $tra['rra'], $tra['invoice'], $tra['checksum']))
					   && ($tra['invoice'] === $paypal['invoice']) && ($tra['checksum'] === md5($paypal['invoice'].$paypal['ip'].$paypal['item_number']))
					)
					{
						$tracking_properties = TRUE; // Yes, these tracking properties ARE being set here.

						$paypal['period1']    = ($tra['rr'] !== 'BN' && $tra['tp']) ? $tra['tp'].' '.$tra['tt'] : '0 D';
						$paypal['mc_amount1'] = ($tra['rr'] !== 'BN' && $tra['tp']) ? number_format($tra['ta'], 2, '.', '') : '0.00';

						$paypal['period3']    = $tra['rp'].' '.$tra['rt'];
						$paypal['mc_amount3'] = $tra['ra'];

						$paypal['recurring'] = ($tra['rr'] === '1') ? '1' : '0';

						$paypal['initial_term']    = (preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['period1'] : '0 D'; // Defaults to '0 D' (zero days).
						$paypal['initial']         = (strlen($paypal['mc_amount1']) && preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['mc_amount1'] : $paypal['mc_amount3'];
						$paypal['regular']         = $paypal['mc_amount3']; // This is the Regular Payment Amount that is charged to the Customer. Always required by PayPal.
						$paypal['regular_term']    = $paypal['period3']; // This is just set to keep a standard; this way both initial_term & regular_term are available.
						$paypal['recurring']       = ($paypal['recurring']) ? $paypal['mc_amount3'] : '0'; // If non-recurring, this should be zero, otherwise Regular.
						$paypal['currency']        = strtoupper($paypal['mc_currency']); // Normalize input currency.
						$paypal['currency_symbol'] = c_ws_plugin__s2member_utils_cur::symbol($paypal['currency']);

						$ipn_signup_vars = $paypal; // Copy of PayPal vars; used as IPN signup vars.
						unset($ipn_signup_vars['s2member_log']); // Create array of wouldbe IPN signup vars w/o s2member_log.
					}
					else if(preg_match('/^(web_accept|subscr_signup)$/i', $paypal['txn_type']))
					{
						$tracking_properties = TRUE; // Yes, these tracking properties ARE being set here.

						$paypal['period1']    = (preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['period1'] : '0 D'; // Defaults to '0 D' (zero days).
						$paypal['mc_amount1'] = (strlen($paypal['mc_amount1']) && $paypal['mc_amount1'] > 0) ? $paypal['mc_amount1'] : '0.00';

						if(preg_match('/^web_accept$/i', $paypal['txn_type']) /* Conversions for Lifetime & Fixed-Term sales. */)
						{
							$paypal['period3']    = ($paypal['eotper']) ? $paypal['eotper'] : '1 L'; // 1 Lifetime.
							$paypal['mc_amount3'] = $paypal['mc_gross']; // The 'Buy Now' amount is the full gross.
						}
						$paypal['initial_term']    = (preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['period1'] : '0 D'; // Defaults to '0 D' (zero days).
						$paypal['initial']         = (strlen($paypal['mc_amount1']) && preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['mc_amount1'] : $paypal['mc_amount3'];
						$paypal['regular']         = $paypal['mc_amount3']; // This is the Regular Payment Amount that is charged to the Customer. Always required by PayPal.
						$paypal['regular_term']    = $paypal['period3']; // This is just set to keep a standard; this way both initial_term & regular_term are available.
						$paypal['recurring']       = ($paypal['recurring']) ? $paypal['mc_amount3'] : '0'; // If non-recurring, this should be zero, otherwise Regular.
						$paypal['currency']        = strtoupper($paypal['mc_currency']); // Normalize input currency.
						$paypal['currency_symbol'] = c_ws_plugin__s2member_utils_cur::symbol($paypal['currency']);

						$ipn_signup_vars = $paypal; // Copy of PayPal vars; used as IPN signup vars.
						unset($ipn_signup_vars['s2member_log']); // Create array of wouldbe IPN signup vars w/o s2member_log.
					}
					else $tracking_properties = FALSE; // Not possible.
					/*
					New Subscription with advanced update vars (option_name1, option_selection1)? Used in Subscr. Modifications.
					*/
					if(preg_match('/(referenc|associat|updat|upgrad)/i', $paypal['option_name1']) && $paypal['option_selection1'])
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_return_before_subscr_signup_w_update_vars', get_defined_vars());
						unset($__refs, $__v); // Housekeeping.

						$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept|subscr_signup|subscr_payment` ) w/ update vars.';

						if(($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['subscr_id'], $paypal['option_selection1'])) && is_object($user = new WP_User($user_id)) && $user->ID)
						{
							if(!$user->has_cap('administrator') /* Do NOT process this routine on Administrators. */)
							{
								$processing = $modifying = $during = TRUE; // Yes, we ARE processing this.

								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_paypal_return_during_before_subscr_signup_w_update_vars', get_defined_vars());
								do_action('ws_plugin__s2member_during_collective_mods', $user_id, get_defined_vars(), 'rtn-upgrade-downgrade', 'modification', 's2member_level'.$paypal['level']);
								unset($__refs, $__v); // Housekeeping.

								$fields      = get_user_option('s2member_custom_fields', $user_id); // These will be needed in the routines below.
								$user_reg_ip = get_user_option('s2member_registration_ip', $user_id); // Original IP during Registration.
								$user_reg_ip = $paypal['ip'] = ($user_reg_ip) ? $user_reg_ip : $paypal['ip']; // Now merge conditionally.

								if(is_multisite() && !is_user_member_of_blog($user_id) /* Must have a Role on this Blog. */)
								{
									add_existing_user_to_blog(array('user_id' => $user_id, 'role' => 's2member_level'.$paypal['level']));
									$user = new WP_User($user_id);
								}
								$current_role = c_ws_plugin__s2member_user_access::user_access_role($user);

								if($current_role !== 's2member_level'.$paypal['level']) // Only if we need to.
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

								if(!empty($ipn_signup_vars)) // We should have these from the routines above.
									update_user_option($user_id, 's2member_ipn_signup_vars', $ipn_signup_vars);

								delete_user_option($user_id, 's2member_file_download_access_log');

								if((preg_match('/^web_accept$/i', $paypal['txn_type']) || ($paypal['initial'] <= 0 && $paypal['regular'] <= 0)) && $paypal['eotper'])
								{
									// Don't update this in the return routine. Leave this for the IPN routine.
									// EOT Times might be extended, and we don't want the IPN routine to extend an already-extended EOT Time.
									$eot_time                 = c_ws_plugin__s2member_utils_time::auto_eot_time('', '', '', $paypal['eotper'], '', get_user_option('s2member_auto_eot_time', $user_id));
									$paypal['s2member_log'][] = 'Automatic EOT (End Of Term) Time will be set to: '.date('D M j, Y g:i:s a T', $eot_time).'.';
								}
								else // Otherwise, we need to clear the Auto-EOT Time.
									delete_user_option($user_id, 's2member_auto_eot_time');

								$pr_times                           = get_user_option('s2member_paid_registration_times', $user_id);
								$pr_times['level']                  = (!$pr_times['level']) ? time() : $pr_times['level']; // Preserve existing.
								$pr_times['level'.$paypal['level']] = (!$pr_times['level'.$paypal['level']]) ? time() : $pr_times['level'.$paypal['level']];
								update_user_option($user_id, 's2member_paid_registration_times', $pr_times);

								c_ws_plugin__s2member_user_notes::clear_user_note_lines($user_id, '/^Demoted by s2Member\:/');
								c_ws_plugin__s2member_user_notes::clear_user_note_lines($user_id, '/^Paid Subscr\. ID @ time of demotion\:/');

								$paypal['s2member_log'][] = 's2Member Level/Capabilities updated w/ advanced update routines.';

								setcookie('s2member_tracking', ($s2member_tracking = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['subscr_id'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_tracking', $s2member_tracking, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_tracking'] = $s2member_tracking);

								$paypal['s2member_log'][] = 'Transient Tracking Cookie set on ( `web_accept|subscr_signup|subscr_payment` ) w/ update vars.';

								if($processing && $tracking_properties && ($code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['modification_tracking_codes']))
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
																									foreach($fields as $var => $val) // Custom Registration/Profile Fields.
																										if(!($code = preg_replace('/%%'.preg_quote($var, '/').'%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(maybe_serialize($val)), $code)))
																											break;

																								if(($code = trim(preg_replace('/%%(.+?)%%/i', '', $code))) /* This gets stored into a Transient Queue. */)
																								{
																									$paypal['s2member_log'][] = 'Storing Modification Tracking Codes into a Transient Queue. These will be processed on-site.';
																									set_transient('s2m_'.md5('s2member_transient_modification_tracking_codes_'.$paypal['subscr_id']), $code, 43200);
																								}
																							}
																	}
								}
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_paypal_return_during_subscr_signup_w_update_vars', get_defined_vars());
								unset($__refs, $__v); // Housekeeping.

								if(($redirection_url_after_modification = apply_filters('ws_plugin__s2member_redirection_url_after_modification', FALSE, get_defined_vars())))
								{
									$paypal['s2member_log'][] = 'Redirecting Customer to a custom URL after modification: '.$redirection_url_after_modification;

									wp_redirect($redirection_url_after_modification);
								}
								else // Else, use standard/default handling in this scenario. Have the Customer log in again.
								{
									$paypal['s2member_log'][] = 'Redirecting Customer to the Login Page (after displaying a quick thank-you message). They need to log back in.';

									echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
									                                                             '<strong>'._x('Thank you! You\'ve been updated to:', 's2member-front', 's2member').'<br /><em>'.esc_html($paypal['item_name']).'</em></strong>',
									                                                             _x('Please Log Back In (Click Here)', 's2member-front', 's2member'), wp_login_url());
								}
							}
							else // Unable to modify Subscription. The existing User ID is associated with an Administrator. Stopping here.
							{
								$paypal['s2member_log'][] = 'Unable to modify Subscription. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access. Please make sure that you are NOT logged in as an Administrator while testing.';

								$paypal['s2member_log'][] = 'Redirecting Customer to the Home Page (after displaying an error message).';

								echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
								                                                             _x('<strong>ERROR:</strong> Unable to modify Subscription.<br />Please contact Support for assistance.<br /><br />The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access. Please make sure that you are NOT logged in as an Administrator while testing.', 's2member-front', 's2member'),
								                                                             _x('Back To Home Page', 's2member-front', 's2member'), home_url('/'));
							}
						}
						else // Unable to modify Subscription. Could not get the existing User ID from the DB.
						{
							$paypal['s2member_log'][] = 'Unable to modify Subscription. Could not get the existing User ID from the DB.';

							$paypal['s2member_log'][] = 'Redirecting Customer to the Home Page (after displaying an error message).';

							echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
							                                                             _x('<strong>ERROR:</strong> Unable to modify Subscription.<br />Please contact Support for assistance.<br /><br />Could not get the existing User ID from the DB.', 's2member-front', 's2member'),
							                                                             _x('Back To Home Page', 's2member-front', 's2member'), home_url('/'));
						}
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_return_after_subscr_signup_w_update_vars', get_defined_vars());
						unset($__refs, $__v); // Housekeeping.
					}
					/*
					New Subscription. Normal Subscription signup, we are not updating anything for a past Subscription.
					*/
					else // Else this is a normal Subscription signup, we are not updating an existing Subscription.
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_return_before_subscr_signup_wo_update_vars', get_defined_vars());
						unset($__refs, $__v);

						$processing = $during = TRUE; // Yes, we ARE processing this new Subscription request.

						$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept|subscr_signup|subscr_payment` ) w/o update vars.';

						setcookie('s2member_subscr_gateway', ($s2member_subscr_gateway = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['subscr_gateway'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_subscr_gateway', $s2member_subscr_gateway, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_subscr_gateway'] = $s2member_subscr_gateway);
						setcookie('s2member_subscr_id', ($s2member_subscr_id = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['subscr_id'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_subscr_id', $s2member_subscr_id, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_subscr_id'] = $s2member_subscr_id);
						setcookie('s2member_custom', ($s2member_custom = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['custom'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_custom', $s2member_custom, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_custom'] = $s2member_custom);
						setcookie('s2member_item_number', ($s2member_item_number = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['item_number'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_item_number', $s2member_item_number, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_item_number'] = $s2member_item_number);

						$paypal['s2member_log'][] = 'Registration Cookies set on ( `web_accept|subscr_signup|subscr_payment` ) w/o update vars.';

						setcookie('s2member_tracking', ($s2member_tracking = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['subscr_id'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_tracking', $s2member_tracking, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_tracking'] = $s2member_tracking);

						$paypal['s2member_log'][] = 'Transient Tracking Cookie set on ( `web_accept|subscr_signup|subscr_payment` ) w/o update vars.';

						if($processing && $tracking_properties && ($code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['signup_tracking_codes']))
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

																	if(($code = trim(preg_replace('/%%(.+?)%%/i', '', $code))) /* This gets stored into a Transient Queue. */)
																	{
																		$paypal['s2member_log'][] = 'Storing Signup Tracking Codes into a Transient Queue. These will be processed on-site.';
																		set_transient('s2m_'.md5('s2member_transient_signup_tracking_codes_'.$paypal['subscr_id']), $code, 43200);
																	}
						}
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_return_during_subscr_signup_wo_update_vars', get_defined_vars());
						unset($__refs, $__v); // Housekeeping.

						if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && is_main_site())
						{
							if(($redirection_url_after_mms_farm_signup = apply_filters('ws_plugin__s2member_redirection_url_after_mms_farm_signup', FALSE, get_defined_vars())))
							{
								$paypal['s2member_log'][] = 'Redirecting Customer to a custom URL after signup: '.$redirection_url_after_mms_farm_signup;

								wp_redirect($redirection_url_after_mms_farm_signup);
							}
							else if(!empty($custom_success_redirection)) // Using a custom success redirection URL?
							{
								$paypal['s2member_log'][] = 'Redirecting Customer to a custom URL on success: '.$custom_success_redirection;

								wp_redirect($custom_success_redirection);
							}
							else // Else use the default return URL in this scenario, which is the Signup Page.
							{
								$paypal['s2member_log'][] = 'Redirecting Customer to Signup Page (after displaying a quick thank-you message). They need to Signup/Register now.';

								echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
								                                                             _x('<strong>Thank you! Your account has been approved.<br />The next step is to Register a Username for immediate access.</strong>', 's2member-front', 's2member'),
								                                                             _x('Please Register Now (Click Here)', 's2member-front', 's2member'), c_ws_plugin__s2member_utils_urls::wp_signup_url());
							}
						}
						else // Otherwise, this is NOT a Multisite install. Or it is, but the Super Administrator is NOT selling Blog creation.
						{
							if(($redirection_url_after_signup = apply_filters('ws_plugin__s2member_redirection_url_after_signup', FALSE, get_defined_vars())))
							{
								$paypal['s2member_log'][] = 'Redirecting Customer to a custom URL after signup: '.$redirection_url_after_signup;

								wp_redirect($redirection_url_after_signup);
							}
							else if(!empty($custom_success_redirection)) // Using a custom success redirection URL?
							{
								$paypal['s2member_log'][] = 'Redirecting Customer to a custom URL on success: '.$custom_success_redirection;

								wp_redirect($custom_success_redirection);
							}
							else // Else use the default return URL in this scenario, which is the Registration Page.
							{
								$paypal['s2member_log'][] = 'Redirecting Customer to Registration Page (after displaying a quick thank-you message). They need to Register now.';

								echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
								                                                             _x('<strong>Thank you! Your account has been approved.<br />The next step is to Register a Username for immediate access.</strong>', 's2member-front', 's2member'),
								                                                             _x('Please Register Now (Click Here)', 's2member-front', 's2member'), c_ws_plugin__s2member_utils_urls::wp_register_url());
							}
						}
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_return_after_subscr_signup_wo_update_vars', get_defined_vars());
						unset($__refs, $__v); // Housekeeping.
					}
				}
				else // Page Expired. Duplicate Return-Data.
				{
					$paypal['s2member_log'][] = 'Page Expired. Duplicate Return-Data.';
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept|subscr_signup|subscr_payment` ).';
					$paypal['s2member_log'][] = 'Page Expired. Instructing customer to check their email for further details about how to obtain access to what they purchased.';

					echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
					                                                             '<strong>'._x('Thank you! Please check your email for further details regarding your purchase.', 's2member-front', 's2member').'</strong>',
					                                                             _x('Return to Home Page', 's2member-front', 's2member'), home_url("/"));
				}
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_return_after_subscr_signup', get_defined_vars());
				unset($__refs, $__v); // Housekeeping.

				return apply_filters('c_ws_plugin__s2member_paypal_return_in_subscr_or_wa_w_level', $paypal, get_defined_vars());
			}
			else return apply_filters('c_ws_plugin__s2member_paypal_return_in_subscr_or_wa_w_level', FALSE, get_defined_vars());
		}
	}
}
