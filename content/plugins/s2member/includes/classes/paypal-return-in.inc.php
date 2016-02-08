<?php
/**
* s2Member's PayPal Auto-Return/PDT handler (inner processing routines).
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
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_paypal_return_in"))
	{
		/**
		* s2Member's PayPal Auto-Return/PDT handler (inner processing routines).
		*
		* @package s2Member\PayPal
		* @since 3.5
		*/
		class c_ws_plugin__s2member_paypal_return_in
			{
				/**
				* Handles PayPal Return URLs.
				*
				* @package s2Member\PayPal
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution after redirection.
				*/
				public static function paypal_return ()
					{
						global /* For Multisite support. */ $current_site, $current_blog;

						do_action("ws_plugin__s2member_before_paypal_return", get_defined_vars ());

						if (!empty($_GET["s2member_paypal_return"]) && ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"] || !empty($_GET["s2member_paypal_proxy"])))
							{
								$paypal = array(); // Initialize PayPal array; we also reference this with a variable for a possible proxy handler.
								if(!empty($_GET["s2member_paypal_proxy"]) && in_array($_GET["s2member_paypal_proxy"], array("alipay", "stripe", "authnet", "clickbank", "ccbill", "google"), TRUE))
									${esc_html(trim(stripslashes($_GET["s2member_paypal_proxy"])))} = &$paypal; // Internal alias by reference.

								$custom_success_redirection = (!empty($_GET["s2member_paypal_return_success"])) ? esc_html (trim (stripslashes ($_GET["s2member_paypal_return_success"]))) : false;
								$custom_success_redirection = ($custom_success_redirection) ? str_ireplace (array("&#038;", "&amp;"), "&", $custom_success_redirection) : $custom_success_redirection;

								if (is_array($paypal = c_ws_plugin__s2member_paypal_utilities::paypal_postvars ()) && ($_paypal = $paypal) && ($_paypal_s = serialize ($_paypal)))
									{
										$paypal["s2member_log"][] = "Return-Data received on: " . date ("D M j, Y g:i:s a T");
										$paypal["s2member_log"][] = "s2Member POST vars verified " . ((!empty($paypal["proxy_verified"])) ? "with a Proxy Key" : "through a POST back to PayPal.");

										$paypal["subscr_gateway"] = (!empty($_GET["s2member_paypal_proxy"])) ? esc_html (trim (stripslashes ($_GET["s2member_paypal_proxy"]))) : "paypal";

										if (empty($_GET["s2member_paypal_proxy"]) || empty($_GET["s2member_paypal_proxy_use"]) || !preg_match ("/ty-email/", $_GET["s2member_paypal_proxy_use"]))
											{
												$payment_status_issues = "/^(failed|denied|expired|refunded|partially_refunded|reversed|reversal|canceled_reversal|voided)$/i";

												if (!empty($paypal["custom"]) && preg_match ("/^" . preg_quote (preg_replace ("/\:([0-9]+)$/", "", $_SERVER["HTTP_HOST"]), "/") . "/i", $paypal["custom"]))
													{
														$paypal["s2member_log"][] = "s2Member originating domain ( `\$_SERVER[\"HTTP_HOST\"]` ) validated.";

														foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
														if (!apply_filters("ws_plugin__s2member_during_paypal_return_conditionals", false, get_defined_vars ()))
															{
																unset($__refs, $__v);

																if (($_paypal_cp = c_ws_plugin__s2member_paypal_return_in_web_accept_sp::cp (get_defined_vars ())))
																	$paypal = $_paypal_cp;

																else if (($_paypal_cp = c_ws_plugin__s2member_paypal_return_in_wa_ccaps_wo_level::cp (get_defined_vars ())))
																	$paypal = $_paypal_cp;

																else if (($_paypal_cp = c_ws_plugin__s2member_paypal_return_in_subscr_or_wa_w_level::cp (get_defined_vars ())))
																	$paypal = $_paypal_cp;

																else if (($_paypal_cp = c_ws_plugin__s2member_paypal_return_in_subscr_modify_w_level::cp (get_defined_vars ())))
																	$paypal = $_paypal_cp;

																else // Else we have an unexpected scenario ( i.e., an unexpected `txn_type/status` ).
																	{
																		$paypal["s2member_log"][] = "Unexpected `txn_type/status`. The `txn_type/status` did not match a required action.";

																		$paypal["s2member_log"][] = "Redirecting Customer to the Home Page (after displaying an error message).";

																		echo c_ws_plugin__s2member_return_templates::return_template ($paypal["subscr_gateway"],
																			_x ('<strong>ERROR:</strong> Unexpected <code>txn_type/status</code>.<br />The <code>txn_type/status</code> did not meet requirements.<br />Please contact Support for assistance.', "s2member-front", "s2member"),
																			_x ("Back To Home Page", "s2member-front", "s2member"), home_url ("/"));
																	}
															}
														else unset($__refs, $__v); // Else a custom conditional has been applied by filters.
													}
												else // Else, use the default ``$_SERVER["HTTP_HOST"]`` error.
													{
														if /* Enqueue an admin notice if the site owner is using the wrong domain variation. */ ($paypal["custom"] && ($paypal["custom"] === "www.".$_SERVER["HTTP_HOST"] || "www.".$paypal["custom"] === $_SERVER["HTTP_HOST"]))
															c_ws_plugin__s2member_admin_notices::enqueue_admin_notice("<strong>s2Member:</strong> Post-processing failed on at least one transaction. It appears that you have a PayPal Button configured with a <code>custom=\"\"</code> Shortcode Attribute that does NOT match up with your installation domain name. If your site uses the <code>www.</code> prefix, please include that. If it does not, please exclude the <code>www.</code> prefix. You should have <code>custom=\"".preg_replace ("/\:([0-9]+)$/", "", $_SERVER["HTTP_HOST"])."\"</code>", "*:*", true);

														$paypal["s2member_log"][] = 'Unable to verify `$_SERVER["HTTP_HOST"]`. Please check the `custom` value in your Button Code. It MUST start with your domain name.';

														$paypal["s2member_log"][] = "Redirecting Customer to the Home Page (after displaying an error message).";

														echo c_ws_plugin__s2member_return_templates::return_template ($paypal["subscr_gateway"],
															_x ('<strong>ERROR:</strong> Unable to verify <code>$_SERVER["HTTP_HOST"]</code>.<br />Please contact Support for assistance.<br /><br />If you are the site owner, please check the <code>custom</code> value in your Button Code. It MUST start with your domain name.', "s2member-front", "s2member"),
															_x ("Back To Home Page", "s2member-front", "s2member"), home_url ("/"));
													}
											}
										else // In this case ... a Proxy has explicitly requested `ty-email` processing.
											$paypal = $_paypal_cp = c_ws_plugin__s2member_paypal_return_in_proxy_ty_email::cp (get_defined_vars ());
									}
								else if (!empty($_GET["s2member_paypal_proxy"]) && !empty($_GET["s2member_paypal_proxy_use"]) && preg_match ("/x-preview/", $_GET["s2member_paypal_proxy_use"]) && ($paypal["subscr_gateway"] = esc_html (trim (stripslashes ($_GET["s2member_paypal_proxy"])))))
									$paypal = $_paypal_cp = c_ws_plugin__s2member_paypal_return_in_proxy_x_preview::cp (get_defined_vars ());

								else if (empty($_GET["tx"]) && empty($_GET["s2member_paypal_proxy"]) && ($paypal["subscr_gateway"] = "paypal"))
									$paypal = $_paypal_cp = c_ws_plugin__s2member_paypal_return_in_no_tx_data::cp (get_defined_vars ());

								else // Extensive log reporting here. This is an area where many site owners find trouble. Depending on server configuration; remote HTTPS connections may fail.
									{
										if /* Enqueue an admin notice if the site owner is missing the PDT Identity Key. */ (!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_identity_token"])
											c_ws_plugin__s2member_admin_notices::enqueue_admin_notice("<strong>s2Member:</strong> You have no PayPal PDT Identity Token configured. PayPal Auto-Return handling failed. Please update your PayPal PDT Identity Key. See: <strong>s2Member → PayPal Options → PayPal PDT/Auto-Return Integration</strong>. Thank you!", "*:*", true);

										$paypal["s2member_log"][] = "Unable to verify \$_POST vars. This is most likely related to an invalid configuration of s2Member, or a problem with server compatibility.";
										$paypal["s2member_log"][] = "Please make sure that you configure a PayPal PDT Identity Token for your installation of s2Member. See: `s2Member → PayPal Options → PayPal PDT/Auto-Return Integration`.";
										$paypal["s2member_log"][] = "See also, this KB article: `http://www.s2member.com/kb/server-scanner/`. We suggest that you run the s2Member Server Scanner.";
										$paypal["s2member_log"][] = /* Recording _POST + _GET vars for analysis and debugging. */ var_export ($_REQUEST, true);

										$paypal["s2member_log"][] = "Redirecting Customer to the Home Page (after displaying an error message).";

										echo c_ws_plugin__s2member_return_templates::return_template ("default",
											_x ('<strong>ERROR:</strong> Unable to verify <code>$_POST</code> vars.<br />Please contact Support for assistance.<br /><br />This is most likely related to an invalid configuration of s2Member, or a problem with server compatibility. If you are the site owner, and you\'re absolutely SURE that your configuration is valid, you may want to run some tests on your server, just to be sure <code>$_POST</code> variables are populated, and that your server is able to connect/communicate with your Payment Gateway over an HTTPS connection.<br /><br />s2Member uses the <code>WP_Http</code> class for remote connections; which will try to use <code>cURL</code> first, and then fall back on the <code>FOPEN</code> method when <code>cURL</code> is not available. On a Windows server, you may have to disable your <code>cURL</code> extension; and instead, set <code>allow_url_fopen = yes</code> in your php.ini file. The <code>cURL</code> extension (usually) does NOT support SSL connections on a Windows server.<br /><br />Please see <a href="http://www.s2member.com/forums/topic/ideal-server-configuration-for-s2member/" target="_blank">this thread</a> for details regarding the ideal server configuration for s2Member.', "s2member-front", "s2member"),
											_x ("Back To Home Page", "s2member-front", "s2member"), home_url ("/"));
									}
								/*
								Add RTN proxy (when available) to the ``$paypal`` array.
								*/
								if (!empty($_GET["s2member_paypal_proxy"]))
									$paypal["s2member_paypal_proxy"] = esc_html(trim(stripslashes((string)$_GET["s2member_paypal_proxy"])));
								/*
								Add IPN proxy use vars (when available) to the ``$paypal`` array.
								*/
								if (!empty($_GET["s2member_paypal_proxy_use"]))
									$paypal["s2member_paypal_proxy_use"] = esc_html(trim(stripslashes((string)$_GET["s2member_paypal_proxy_use"])));
								/*
								Also add RTN proxy self-verification (when available) to the ``$paypal`` array.
								*/
								if (!empty($_GET["s2member_paypal_proxy_verification"]))
									$paypal["s2member_paypal_proxy_verification"] = esc_html(trim(stripslashes((string)$_GET["s2member_paypal_proxy_verification"])));
								/*
								Also add RTN success redirection URL (when available) to the ``$paypal`` array.
								*/
								if (!empty($_GET["s2member_paypal_return_success"]))
									$paypal["s2member_paypal_return_success"] = esc_html(trim(stripslashes((string)$_GET["s2member_paypal_return_success"])));
								/*
								Also add RTN t and r Attributes (when available) to the ``$paypal`` array.
								*/
								if (!empty($_GET["s2member_paypal_return_tra"]))
									$paypal["s2member_paypal_return_tra"] = esc_html(trim(stripslashes((string)$_GET["s2member_paypal_return_tra"])));
								/*
								Log this IPN post-processing event now.
								*/
								c_ws_plugin__s2member_utils_logs::log_entry('gateway-core-rtn', $paypal);
								/*
								Hook during core RTN post-processing might be useful for developers.
								*/
								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_paypal_return", get_defined_vars ());
								unset($__refs, $__v);

								exit(); // Clean exit now.
							}
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_after_paypal_return", get_defined_vars ());
						unset($__refs, $__v);
					}
			}
	}
