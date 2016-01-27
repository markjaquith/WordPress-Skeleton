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

if(!class_exists('c_ws_plugin__s2member_paypal_return_in_subscr_modify_w_level'))
{
	/**
	 * s2Member's PayPal Auto-Return/PDT handler (inner processing routine).
	 *
	 * @package s2Member\PayPal
	 * @since 110720
	 */
	class c_ws_plugin__s2member_paypal_return_in_subscr_modify_w_level
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
		public static function cp($vars = array() /* Conditional phase for ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``. */)
		{
			extract($vars, EXTR_OVERWRITE | EXTR_REFS /* Extract all vars passed in from: ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``. */);

			if((!empty($paypal['txn_type']) && preg_match('/^subscr_modify$/i', $paypal['txn_type']))
			   && (!empty($paypal['item_number']) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['membership_item_number_w_level_regex'], $paypal['item_number']))
			   && (!empty($paypal['subscr_id'])) && (!empty($paypal['subscr_baid']) || ($paypal['subscr_baid'] = $paypal['subscr_id']))
			   && (!empty($paypal['subscr_cid']) || ($paypal['subscr_cid'] = $paypal['subscr_id']))
			)
			{
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_return_before_subscr_modify', get_defined_vars());
				unset($__refs, $__v);

				if(!get_transient($transient_rtn = 's2m_rtn_'.md5('s2member_transient_'.$_paypal_s)) && set_transient($transient_rtn, time(), 31556926 * 10))
				{
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `subscr_modify` ), a Subscription Modification.';

					list($paypal['level'], $paypal['ccaps']/*, $paypal['eotper'] */) = preg_split('/\:/', $paypal['item_number'], 2);

					$paypal['ip'] = (preg_match('/ip address/i', $paypal['option_name2']) && $paypal['option_selection2']) ? $paypal['option_selection2'] : '';
					$paypal['ip'] = (!$paypal['ip'] && preg_match('/^[a-z0-9]+~[0-9\.]+$/i', $paypal['invoice'])) ? preg_replace('/^[a-z0-9]+~/i', '', $paypal['invoice']) : $paypal['ip'];
					$paypal['ip'] = (!$paypal['ip'] && $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $paypal['ip'];

					$paypal['period1']    = (preg_match('/^[1-9]/', $paypal['period1'])) ? $paypal['period1'] : '0 D'; // Defaults to '0 D' (zero days).
					$paypal['mc_amount1'] = (strlen($paypal['mc_amount1']) && $paypal['mc_amount1'] > 0) ? $paypal['mc_amount1'] : '0.00';

					if(preg_match('/^web_accept$/i', $paypal['txn_type']) /* Conversions for Lifetime & Fixed-Term sales. */)
					{
						$paypal['period3']    = !empty($paypal['eotper']) ? $paypal['eotper'] : '1 L'; // 1 Lifetime.
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

					if(($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['subscr_id'])) && is_object($user = new WP_User($user_id)) && $user->ID)
					{
						if(!$user->has_cap('administrator') /* Do NOT process this routine on Administrators. */)
						{
							$processing = $modifying = $during = TRUE; // Yes, we ARE processing this.

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_paypal_return_during_before_subscr_modify', get_defined_vars());
							do_action('ws_plugin__s2member_during_collective_mods', $user_id, get_defined_vars(), 'rtn-upgrade-downgrade', 'modification', 's2member_level'.$paypal['level']);
							unset($__refs, $__v);

							$fields      = get_user_option('s2member_custom_fields', $user_id); // These will be needed in the routines below.
							$user_reg_ip = get_user_option('s2member_registration_ip', $user_id); // Original IP during Registration.
							$user_reg_ip = $paypal['ip'] = ($user_reg_ip) ? $user_reg_ip : $paypal['ip']; // Now merge conditionally.

							if(is_multisite() && !is_user_member_of_blog($user_id) /* Must have a Role on this Blog. */)
							{
								add_existing_user_to_blog(array('user_id' => $user_id, 'role' => 's2member_level'.$paypal['level']));
								$user = new WP_User($user_id); // Now update the $user object we're using.
							}
							$current_role = c_ws_plugin__s2member_user_access::user_access_role($user);

							if($current_role !== 's2member_level'.$paypal['level'] /* Only if we need to. */)
								$user->set_role('s2member_level'.$paypal['level']); // Upgrade/downgrade.

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
							$pr_times['level']                  = (!$pr_times['level']) ? time() : $pr_times['level']; // Preserve existing.
							$pr_times['level'.$paypal['level']] = (!$pr_times['level'.$paypal['level']]) ? time() : $pr_times['level'.$paypal['level']];
							update_user_option($user_id, 's2member_paid_registration_times', $pr_times);

							c_ws_plugin__s2member_user_notes::clear_user_note_lines($user_id, '/^Demoted by s2Member\:/');
							c_ws_plugin__s2member_user_notes::clear_user_note_lines($user_id, '/^Paid Subscr\. ID @ time of demotion\:/');

							$paypal['s2member_log'][] = 's2Member Level/Capabilities updated on ( `subscr_modify` ), a Subscription Modification.';

							setcookie('s2member_tracking', ($s2member_tracking = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['subscr_id'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_tracking', $s2member_tracking, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_tracking'] = $s2member_tracking);

							$paypal['s2member_log'][] = 'Transient Tracking Cookie set on ( `subscr_modify` ), a Subscription Modification.';

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

																							if(($code = trim(preg_replace('/%%(.+?)%%/i', '', $code))) /* This gets stored into a Transient Queue. */)
																							{
																								$paypal['s2member_log'][] = 'Storing Modification Tracking Codes into a Transient Queue. These will be processed on-site.';
																								set_transient('s2m_'.md5('s2member_transient_modification_tracking_codes_'.$paypal['subscr_id']), $code, 43200);
																							}
																						}
																}
							}
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action('ws_plugin__s2member_during_paypal_return_during_subscr_modify', get_defined_vars());
							unset($__refs, $__v);

							if(($redirection_url_after_modification = apply_filters('ws_plugin__s2member_redirection_url_after_modification', FALSE, get_defined_vars())))
							{
								$paypal['s2member_log'][] = 'Redirecting this Member to a custom URL after modification: '.$redirection_url_after_modification;

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
						else // Else, unable to modify Subscription. The existing User ID is associated with an Administrator. Stopping here.
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
				}
				else // Page Expired. Duplicate Return-Data.
				{
					$paypal['s2member_log'][] = 'Page Expired. Duplicate Return-Data.';
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as `subscr_modify`.';
					$paypal['s2member_log'][] = 'Page Expired. Instructing customer to check their email for further details about how to obtain access to what they purchased.';

					echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
					                                                             '<strong>'._x('Thank you! Please check your email for further details regarding your purchase.', 's2member-front', 's2member').'</strong>',
					                                                             _x('Return to Home Page', 's2member-front', 's2member'), home_url('/'));
				}
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_return_after_subscr_modify', get_defined_vars());
				unset($__refs, $__v);

				return apply_filters('c_ws_plugin__s2member_paypal_return_in_subscr_modify_w_level', $paypal, get_defined_vars());
			}
			else return apply_filters('c_ws_plugin__s2member_paypal_return_in_subscr_modify_w_level', FALSE, get_defined_vars());
		}
	}
}
