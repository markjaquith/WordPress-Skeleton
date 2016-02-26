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

if(!class_exists('c_ws_plugin__s2member_paypal_return_in_wa_ccaps_wo_level'))
{
	/**
	 * s2Member's PayPal Auto-Return/PDT handler (inner processing routine).
	 *
	 * @package s2Member\PayPal
	 * @since 110720
	 */
	class c_ws_plugin__s2member_paypal_return_in_wa_ccaps_wo_level
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

			if((!empty($paypal['txn_type']) && preg_match('/^web_accept$/i', $paypal['txn_type']))
			   && (!empty($paypal['item_number']) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['membership_item_number_wo_level_regex'], $paypal['item_number']))
			   && (empty($paypal['payment_status']) || empty($payment_status_issues) || !preg_match($payment_status_issues, $paypal['payment_status']))
			   && (!empty($paypal['txn_id'])) && (!empty($paypal['payer_email']))
			   && (!empty($paypal['txn_baid']) || ($paypal['txn_baid'] = $paypal['txn_id']))
			   && (!empty($paypal['txn_cid']) || ($paypal['txn_cid'] = $paypal['txn_id']))
			)
			{
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_return_before_new_ccaps', get_defined_vars());
				unset($__refs, $__v);

				if(!get_transient($transient_rtn = 's2m_rtn_'.md5('s2member_transient_'.$_paypal_s)) && set_transient($transient_rtn, time(), 31556926 * 10))
				{
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept` ) w/ update vars for Capabilities w/o Level.';

					list ($paypal['level'], $paypal['ccaps'], $paypal['eotper']) = preg_split('/\:/', $paypal['item_number'], 3);

					$paypal['ip'] = (preg_match('/ip address/i', $paypal['option_name2']) && $paypal['option_selection2']) ? $paypal['option_selection2'] : '';
					$paypal['ip'] = (!$paypal['ip'] && preg_match('/^[a-z0-9]+~[0-9\.]+$/i', $paypal['invoice'])) ? preg_replace('/^[a-z0-9]+~/i', '', $paypal['invoice']) : $paypal['ip'];
					$paypal['ip'] = (!$paypal['ip'] && $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $paypal['ip'];

					$paypal['currency']        = strtoupper($paypal['mc_currency']); // Normalize input currency.
					$paypal['currency_symbol'] = c_ws_plugin__s2member_utils_cur::symbol($paypal['currency']);

					if(preg_match('/(referenc|associat|updat|upgrad)/i', $paypal['option_name1']) && $paypal['option_selection1'] /* Must have this information for Capability additions. */)
					{
						if(($user_id = c_ws_plugin__s2member_utils_users::get_user_id_with($paypal['txn_id'], $paypal['option_selection1'])) && is_object($user = new WP_User ($user_id)) && $user->ID)
						{
							if(!$user->has_cap('administrator') /* Do NOT process this routine on Administrators. */)
							{
								$processing = $during = TRUE; // Yes, we ARE processing this.

								$fields      = get_user_option('s2member_custom_fields', $user_id); // These will be needed in the routines below.
								$user_reg_ip = get_user_option('s2member_registration_ip', $user_id); // Original IP during Registration.
								$user_reg_ip = $paypal['ip'] = ($user_reg_ip) ? $user_reg_ip : $paypal['ip']; // Now merge conditionally.

								if(is_multisite() && !is_user_member_of_blog($user_id) /* Must have a Role on this Blog. */)
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

								$paypal['s2member_log'][] = 's2Member Custom Capabilities updated w/ advanced update routines.';

								setcookie('s2member_tracking', ($s2member_tracking = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['txn_id'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_tracking', $s2member_tracking, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_tracking'] = $s2member_tracking);

								$paypal['s2member_log'][] = 'Transient Tracking Cookie set on ( `web_accept` ) w/ update vars for Capabilities w/o Level.';

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
																								$paypal['s2member_log'][] = 'Storing Payment Tracking Codes into a Transient Queue. These will be processed on-site.';
																								set_transient('s2m_'.md5('s2member_transient_ccap_tracking_codes_'.$paypal['txn_id']), $code, 43200);
																							}
																						}
																}
								}
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action('ws_plugin__s2member_during_paypal_return_during_new_ccaps', get_defined_vars());
								unset($__refs, $__v);

								if(($redirection_url_after_capabilities = apply_filters('ws_plugin__s2member_redirection_url_after_capabilities', FALSE, get_defined_vars())))
								{
									$paypal['s2member_log'][] = 'Redirecting Customer to a custom URL after Capabilities: '.$redirection_url_after_capabilities;

									wp_redirect($redirection_url_after_capabilities);
								}
								else // Else, use standard/default handling in this scenario. Have the Customer log in again.
								{
									$paypal['s2member_log'][] = 'Redirecting Customer to the Login Page (after displaying a quick thank-you message). They need to log back in.';

									echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
									                                                             '<strong>'._x('Thank you! You now have access to:', 's2member-front', 's2member').'<br /><em>'.esc_html($paypal['item_name']).'</em></strong>',
									                                                             _x('Please Log Back In (Click Here)', 's2member-front', 's2member'), wp_login_url());
								}
							}
							else // Unable to add new Capabilities. The existing User ID is associated with an Administrator. Stopping here.
							{
								$paypal['s2member_log'][] = 'Unable to add new Capabilities. The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access. Please make sure that you are NOT logged in as an Administrator while testing.';

								$paypal['s2member_log'][] = 'Redirecting Customer to the Home Page (after displaying an error message).';

								echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
								                                                             _x('<strong>ERROR:</strong> Unable to add new Capabilities.<br />Please contact Support for assistance.<br /><br />The existing User ID is associated with an Administrator. Stopping here. Otherwise, an Administrator could lose access. Please make sure that you are NOT logged in as an Administrator while testing.', 's2member-front', 's2member'),
								                                                             _x('Back To Home Page', 's2member-front', 's2member'), home_url('/'));
							}
						}
						else // Unable to add new Capabilities. Could not get the existing User ID from the DB.
						{
							$paypal['s2member_log'][] = 'Unable to add new Capabilities. Could not get the existing User ID from the DB.';

							$paypal['s2member_log'][] = 'Redirecting Customer to the Home Page (after displaying an error message).';

							echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
							                                                             _x('<strong>ERROR:</strong> Unable to add new Capabilities.<br />Please contact Support for assistance.<br /><br />Could not get the existing User ID from the DB.', 's2member-front', 's2member'),
							                                                             _x('Back To Home Page', 's2member-front', 's2member'), home_url('/'));
						}
					}
					else // Unable to add new Capabilities. Missing User/Member details.
					{
						$paypal['s2member_log'][] = 'Unable to add new Capabilities. Missing User/Member details. Please check the `on0` and `os0` variables in your Button Code.';

						$paypal['s2member_log'][] = 'Redirecting Customer to the Home Page (after displaying an error message).';

						echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
						                                                             _x('<strong>ERROR:</strong> Unable to add new Capabilities.<br />Please contact Support for assistance.<br /><br />Missing User/Member details.', 's2member-front', 's2member'),
						                                                             _x('Back To Home Page', 's2member-front', 's2member'), home_url('/'));
					}
				}
				else // Page Expired. Duplicate Return-Data.
				{
					$paypal['s2member_log'][] = 'Page Expired. Duplicate Return-Data.';
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept` ) w/ update vars for Capabilities w/o Level.';
					$paypal['s2member_log'][] = 'Page Expired. Instructing customer to check their email for further details about how to obtain access to what they purchased.';

					echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
					                                                             '<strong>'._x('Thank you! Please check your email for further details regarding your purchase.', 's2member-front', 's2member').'</strong>',
					                                                             _x('Return to Home Page', 's2member-front', 's2member'), home_url('/'));
				}
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_return_after_new_ccaps', get_defined_vars());
				unset($__refs, $__v);

				return apply_filters('c_ws_plugin__s2member_paypal_return_in_wa_ccaps_wo_level', $paypal, get_defined_vars());
			}
			else return apply_filters('c_ws_plugin__s2member_paypal_return_in_wa_ccaps_wo_level', FALSE, get_defined_vars());
		}
	}
}
