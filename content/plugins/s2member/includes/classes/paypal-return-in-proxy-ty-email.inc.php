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
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_paypal_return_in_proxy_ty_email"))
	{
		/**
		* s2Member's PayPal Auto-Return/PDT handler (inner processing routine).
		*
		* @package s2Member\PayPal
		* @since 110720
		*/
		class c_ws_plugin__s2member_paypal_return_in_proxy_ty_email
			{
				/**
				* s2Member's PayPal Auto-Return/PDT handler (inner processing routine).
				*
				* @package s2Member\PayPal
				* @since 110720
				*
				* @param array $vars Required. An array of defined variables passed by {@link s2Member\PayPal\c_ws_plugin__s2member_paypal_return_in::paypal_return()}.
				* @return array|bool The original ``$paypal`` array passed in (extracted) from ``$vars``, or false when conditions do NOT apply.
				*/
				public static function /* Conditional phase for ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``. */ cp ($vars = array())
					{
						extract /* Extract all vars passed in from: ``c_ws_plugin__s2member_paypal_notify_in::paypal_notify()``. */($vars, EXTR_OVERWRITE | EXTR_REFS);

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_during_paypal_return_before_explicit_ty_email", get_defined_vars ());
						unset($__refs, $__v);

						$paypal["s2member_log"][] = "Customer MUST wait for Email Confirmation—`proxy_use`: ( `ty-email` ).";

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_during_paypal_return_during_explicit_ty_email", get_defined_vars ());
						unset($__refs, $__v);

						if /* Using a custom success redirection URL? */ ($custom_success_redirection)
						{
							$paypal["s2member_log"][] = "Redirecting Customer to a custom URL on success: " . $custom_success_redirection . ".";

							wp_redirect($custom_success_redirection);
						}
						else // Else we use the default redirection URL for this scenario, which is the Home Page.
						{
							$paypal["s2member_log"][] = "Redirecting Customer to the Home Page (after asking Customer to check their email).";

							echo c_ws_plugin__s2member_return_templates::return_template ($paypal["subscr_gateway"],
							   _x ('<strong>Thank you! (you MUST check your email before proceeding).</strong><br /><br />* Note: It can take <em>(up to 15 minutes)</em> for Email Confirmation with important details. If you don\'t receive email confirmation in the next 15 minutes, please contact Support.', "s2member-front", "s2member") . (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"] || (c_ws_plugin__s2member_utils_conds::pro_is_installed () && !empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["pro_" . $paypal["subscr_gateway"] . "_sandbox"]))) ? '<br /><br />' . _x ('<strong>* Sandbox Mode *:</strong> You may NOT receive this Email in Sandbox Mode. Sandbox addresses are usually bogus (for testing).', "s2member-front", "s2member") : ''),
							   _x ("Back To Home Page", "s2member-front", "s2member"), home_url ("/"));
						}
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_during_paypal_return_after_explicit_ty_email", get_defined_vars ());
						unset($__refs, $__v);

						return apply_filters("c_ws_plugin__s2member_paypal_return_in_proxy_ty_email", $paypal, get_defined_vars ());
					}
			}
	}