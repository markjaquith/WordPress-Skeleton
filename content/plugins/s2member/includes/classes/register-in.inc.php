<?php
/**
* Registration Links (inner processing routines).
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
* @package s2Member\Registrations
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_register_in"))
	{
		/**
		* Registration Links (inner processing routines).
		*
		* @package s2Member\Registrations
		* @since 3.5
		*/
		class c_ws_plugin__s2member_register_in
			{
				/**
				* Handles Registration Links.
				*
				* @package s2Member\Registrations
				* @since 3.5
				*
				* @attaches-to ``add_action("init");``
				*
				* @return null Or exits script execution after redirection.
				*/
				public static function register ()
					{
						do_action("ws_plugin__s2member_before_register", get_defined_vars ());

						if (!empty($_GET["s2member_register"])) // If they're attempting to access the registration system.
							{
								while (@ob_end_clean ()); // Clean any existing output buffers.

								$msg_503 = _x ('<strong>Your Link Expired:</strong><br />Please contact Support if you need assistance.', "s2member-front", "s2member");

								if (is_array($register = preg_split ("/\:\.\:\|\:\.\:/", c_ws_plugin__s2member_utils_encryption::decrypt (trim (stripslashes ((string)$_GET["s2member_register"]))))))
									{
										if (count ($register) === 6 && $register[0] === "subscr_gateway_subscr_id_custom_item_number_time" /* Does the checksum value match up here? */)
											{
												if (is_numeric ($register[5]) && $register[5] <= strtotime ("now") && $register[5] >= strtotime ("-" . apply_filters("ws_plugin__s2member_register_link_exp_time", "2 days", get_defined_vars ())))
													{
														$_COOKIE["s2member_subscr_gateway"] = /* For ``reg_cookies_ok ()``. */ c_ws_plugin__s2member_utils_encryption::encrypt ($register[1]);
														$_COOKIE["s2member_subscr_id"] = /* For ``reg_cookies_ok ()``. */ c_ws_plugin__s2member_utils_encryption::encrypt ($register[2]);
														$_COOKIE["s2member_custom"] = /* For ``reg_cookies_ok ()``. */ c_ws_plugin__s2member_utils_encryption::encrypt ($register[3]);
														$_COOKIE["s2member_item_number"] = /* For ``reg_cookies_ok ()``. */ c_ws_plugin__s2member_utils_encryption::encrypt ($register[4]);

														if (($reg_cookies = c_ws_plugin__s2member_register_access::reg_cookies_ok ()) && extract ($reg_cookies) /* Needed? */)
															{
																status_header(200); // Send a 200 OK status header.
																header("Content-Type: text/html; charset=UTF-8"); // Content-Type with UTF-8.

																setcookie ("s2member_subscr_gateway", $_COOKIE["s2member_subscr_gateway"], time () + 31556926, COOKIEPATH, COOKIE_DOMAIN) . setcookie ("s2member_subscr_gateway", $_COOKIE["s2member_subscr_gateway"], time () + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
																setcookie ("s2member_subscr_id", $_COOKIE["s2member_subscr_id"], time () + 31556926, COOKIEPATH, COOKIE_DOMAIN) . setcookie ("s2member_subscr_id", $_COOKIE["s2member_subscr_id"], time () + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
																setcookie ("s2member_custom", $_COOKIE["s2member_custom"], time () + 31556926, COOKIEPATH, COOKIE_DOMAIN) . setcookie ("s2member_custom", $_COOKIE["s2member_custom"], time () + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);
																setcookie ("s2member_item_number", $_COOKIE["s2member_item_number"], time () + 31556926, COOKIEPATH, COOKIE_DOMAIN) . setcookie ("s2member_item_number", $_COOKIE["s2member_item_number"], time () + 31556926, SITECOOKIEPATH, COOKIE_DOMAIN);

																do_action("ws_plugin__s2member_during_register", get_defined_vars ());

																if (is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && is_main_site () && ($location = c_ws_plugin__s2member_utils_urls::wp_signup_url ()))
																	{
																		echo '<script type="text/javascript">' . "\n";
																		echo "window.location = '" . c_ws_plugin__s2member_utils_strings::esc_js_sq ($location) . "';";
																		echo '</script>' . "\n";
																	}
																else if (($location = c_ws_plugin__s2member_utils_urls::wp_register_url ()))
																	{
																		echo '<script type="text/javascript">' . "\n";
																		echo "window.location = '" . c_ws_plugin__s2member_utils_strings::esc_js_sq ($location) . "';";
																		echo '</script>' . "\n";
																	}
																exit (); // Clean exit. The browser will now be redirected to ``$location``.
															}
														else
															status_header(503) . header ("Content-Type: text/html; charset=UTF-8") . exit ($msg_503);
													}
												else
													status_header(503) . header ("Content-Type: text/html; charset=UTF-8") . exit ($msg_503);
											}
										else
											status_header(503) . header ("Content-Type: text/html; charset=UTF-8") . exit ($msg_503);
									}
								else
									status_header(503) . header ("Content-Type: text/html; charset=UTF-8") . exit ($msg_503);
							}

						do_action("ws_plugin__s2member_after_register", get_defined_vars ());
					}
			}
	}
