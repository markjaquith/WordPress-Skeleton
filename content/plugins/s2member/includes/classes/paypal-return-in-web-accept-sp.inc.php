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

if(!class_exists('c_ws_plugin__s2member_paypal_return_in_web_accept_sp'))
{
	/**
	 * s2Member's PayPal Auto-Return/PDT handler (inner processing routine).
	 *
	 * @package s2Member\PayPal
	 * @since 110720
	 */
	class c_ws_plugin__s2member_paypal_return_in_web_accept_sp
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
			   && (!empty($paypal['item_number']) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['sp_access_item_number_regex'], $paypal['item_number']))
			   && (empty($paypal['payment_status']) || empty($payment_status_issues) || !preg_match($payment_status_issues, $paypal['payment_status']))
			   && (!empty($paypal['txn_id'])) && (!empty($paypal['txn_baid']) || ($paypal['txn_baid'] = $paypal['txn_id']))
			   && (!empty($paypal['txn_cid']) || ($paypal['txn_cid'] = $paypal['txn_id']))
			)
			{
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_return_before_sp_access', get_defined_vars());
				unset($__refs, $__v);

				if(!get_transient($transient_rtn = 's2m_rtn_'.md5('s2member_transient_'.$_paypal_s)) && set_transient($transient_rtn, time(), 31556926 * 10))
				{
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept` ) for Specific Post/Page Access.';

					list (, $paypal['sp_ids'], $paypal['hours']) = preg_split('/\:/', $paypal['item_number'], 3);

					$paypal['ip'] = (preg_match('/ip address/i', $paypal['option_name2']) && $paypal['option_selection2']) ? $paypal['option_selection2'] : '';
					$paypal['ip'] = (!$paypal['ip'] && preg_match('/^[a-z0-9]+~[0-9\.]+$/i', $paypal['invoice'])) ? preg_replace('/^[a-z0-9]+~/i', '', $paypal['invoice']) : $paypal['ip'];
					$paypal['ip'] = (!$paypal['ip'] && $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $paypal['ip'];

					$paypal['currency']        = strtoupper($paypal['mc_currency']); // Normalize input currency.
					$paypal['currency_symbol'] = c_ws_plugin__s2member_utils_cur::symbol($paypal['currency']);

					if(($sp_access_url = c_ws_plugin__s2member_sp_access::sp_access_link_gen($paypal['sp_ids'], $paypal['hours'], FALSE)))
					{
						$processing = $during = TRUE; // Yes, we ARE processing this.

						setcookie('s2member_sp_tracking', ($s2member_sp_tracking = c_ws_plugin__s2member_utils_encryption::encrypt($paypal['txn_id'])), time() + 31556926, COOKIEPATH, COOKIE_DOMAIN).setcookie('s2member_sp_tracking', $s2member_sp_tracking, time() + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN).($_COOKIE['s2member_sp_tracking'] = $s2member_sp_tracking);

						$paypal['s2member_log'][] = 'Transient Tracking Cookie set on ( `web_accept` ) for Specific Post/Page Access.';

						if($processing && ($code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['sp_tracking_codes']))
						{
							if(($code = c_ws_plugin__s2member_utils_strings::fill_cvs($code, $paypal['custom'])) && ($code = preg_replace('/%%amount%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['mc_gross']), $code)) && ($code = preg_replace('/%%txn_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_id']), $code)))
								if(($code = preg_replace('/%%txn_baid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_baid']), $code)) && ($code = preg_replace('/%%txn_cid%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['txn_cid']), $code)))
									if(($code = preg_replace('/%%currency%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency']), $code)) && ($code = preg_replace('/%%currency_symbol%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['currency_symbol']), $code)))
										if(($code = preg_replace('/%%item_number%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_number']), $code)) && ($code = preg_replace('/%%item_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['item_name']), $code)))
											if(($code = preg_replace('/%%first_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['first_name']), $code)) && ($code = preg_replace('/%%last_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['last_name']), $code)))
												if(($code = preg_replace('/%%full_name%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(trim($paypal['first_name'].' '.$paypal['last_name'])), $code)))
													if(($code = preg_replace('/%%payer_email%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['payer_email']), $code)))
														if(($code = preg_replace('/%%user_ip%%/i', c_ws_plugin__s2member_utils_strings::esc_refs($paypal['ip']), $code)))

															if(($code = trim(preg_replace('/%%(.+?)%%/i', '', $code)))) // This gets stored into a Transient Queue.
															{
																$paypal['s2member_log'][] = 'Storing Specific Post/Page Tracking Codes into a Transient Queue. These will be processed on-site.';
																set_transient('s2m_'.md5('s2member_transient_sp_tracking_codes_'.$paypal['txn_id']), $code, 43200);
															}
						}
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_paypal_return_during_sp_access', get_defined_vars());
						unset($__refs, $__v);

						if(apply_filters('ws_plugin__s2member_immediate_sp_access_redirection', FALSE, get_defined_vars()))
						{
							$paypal['s2member_log'][] = 'Redirecting Customer immediately to the Specific Post/Page.';

							wp_redirect($sp_access_url); // Immediate redirection to Specific Post/Page.
						}
						else if($custom_success_redirection) // Using a custom success redirection URL?
						{
							$paypal['s2member_log'][] = 'Redirecting Customer to a custom URL on success: '.$custom_success_redirection;

							wp_redirect($custom_success_redirection);
						}
						else // Else use the default return URL in this scenario, which is the Specific Post/Page.
						{
							$paypal['s2member_log'][] = 'Redirecting Customer to the Specific Post/Page (after displaying a thank-you message).';

							echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
							                                                             _x('<strong>Thank You! Your transaction has been approved.</strong>', 's2member-front', 's2member'),
							                                                             _x('Continue (Click Here)', 's2member-front', 's2member'), $sp_access_url);
						}
					}
					else // Otherwise, the ID must have been invalid. Or the Post/Page was deleted.
					{
						$paypal['s2member_log'][] = 'Unable to generate Specific Post/Page Access Link. Does your Leading Post/Page still exist?';

						$paypal['s2member_log'][] = 'Redirecting Customer to the Home Page (after displaying an error message).';

						echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
						                                                             _x('<strong>ERROR:</strong> Unable to generate Access Link.<br />Please contact Support for assistance.', 's2member-front', 's2member'),
						                                                             _x('Back To Home Page', 's2member-front', 's2member'), home_url('/'));
					}
				}
				else // Page Expired. Duplicate Return-Data.
				{
					$paypal['s2member_log'][] = 'Page Expired. Duplicate Return-Data.';
					$paypal['s2member_log'][] = 's2Member `txn_type` identified as ( `web_accept` ) for Specific Post/Page Access.';
					$paypal['s2member_log'][] = 'Page Expired. Instructing customer to check their email for further details about how to obtain access to what they purchased.';

					echo c_ws_plugin__s2member_return_templates::return_template($paypal['subscr_gateway'],
					                                                             '<strong>'._x('Thank you! Please check your email for further details regarding your purchase.', 's2member-front', 's2member').'</strong>',
					                                                             _x('Return to Home Page', 's2member-front', 's2member'), home_url('/'));
				}
				foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
				do_action('ws_plugin__s2member_during_paypal_return_after_sp_access', get_defined_vars());
				unset($__refs, $__v);

				return apply_filters('c_ws_plugin__s2member_paypal_return_in_web_accept_sp', $paypal, get_defined_vars());
			}
			else return apply_filters('c_ws_plugin__s2member_paypal_return_in_web_accept_sp', FALSE, get_defined_vars());
		}
	}
}
