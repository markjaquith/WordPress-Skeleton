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
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_paypal_notify_in_virtual_terminal"))
	{
		/**
		* s2Member's PayPal IPN handler (inner processing routine).
		*
		* @package s2Member\PayPal
		* @since 110720
		*/
		class c_ws_plugin__s2member_paypal_notify_in_virtual_terminal
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
						extract($vars, EXTR_OVERWRITE | EXTR_REFS); // Extract all vars passed in from: ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``.

						if (!empty($paypal["txn_type"]) && preg_match ("/^virtual_terminal$/i", $paypal["txn_type"]))
							{
								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_paypal_notify_before_virtual_terminal", get_defined_vars ());
								unset($__refs, $__v);

								if (!get_transient ($transient_ipn = "s2m_ipn_" . md5 ("s2member_transient_" . $_paypal_s)) && set_transient ($transient_ipn, time (), 31556926 * 10))
									{
										$paypal["s2member_log"][] = "s2Member `txn_type` identified as ( `virtual_terminal` ).";

										$processing = $during = true; // Yes, we ARE processing this.

										$paypal["s2member_log"][] = "The `txn_type` does not require any action on the part of s2Member.";

										foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
										do_action("ws_plugin__s2member_during_paypal_notify_during_virtual_terminal", get_defined_vars ());
										unset($__refs, $__v);
									}
								else // Else, this is a duplicate IPN. Must stop here.
									{
										$paypal["s2member_log"][] = "Not processing. Duplicate IPN.";
										$paypal["s2member_log"][] = "s2Member `txn_type` identified as ( `virtual_terminal` ).";
										$paypal["s2member_log"][] = "Duplicate IPN. Already processed. This IPN will be ignored.";
									}
								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_paypal_notify_after_virtual_terminal", get_defined_vars ());
								unset($__refs, $__v);

								return apply_filters("c_ws_plugin__s2member_paypal_notify_in_virtual_terminal", $paypal, get_defined_vars ());
							}
						else return apply_filters("c_ws_plugin__s2member_paypal_notify_in_virtual_terminal", false, get_defined_vars ());
					}
			}
	}