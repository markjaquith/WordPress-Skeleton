<?php
/**
* Shortcode `[s2Member-PayPal-Button]` (inner processing routines).
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
	exit("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_sc_paypal_button_in"))
	{
		/**
		* Shortcode `[s2Member-PayPal-Button]` (inner processing routines).
		*
		* @package s2Member\PayPal
		* @since 3.5
		*/
		class c_ws_plugin__s2member_sc_paypal_button_in
			{
				/**
				* Handles the Shortcode for: `[s2Member-PayPal-Button /]`.
				*
				* @package s2Member\PayPal
				* @since 3.5
				*
				* @attaches-to ``add_shortcode("s2Member-PayPal-Button");``
				*
				* @param array $attr An array of Attributes.
				* @param string $content Content inside the Shortcode.
				* @param string $shortcode The actual Shortcode name itself.
				* @return string The resulting PayPal Button Code.
				*/
				public static function sc_paypal_button ($attr = FALSE, $content = FALSE, $shortcode = FALSE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_sc_paypal_button", get_defined_vars ());
						unset($__refs, $__v);

						c_ws_plugin__s2member_no_cache::no_cache_constants /* No caching on pages that contain this Payment Button. */ (true);

						$attr = /* Force array. Trim quote entities. */ c_ws_plugin__s2member_utils_strings::trim_qts_deep ((array)$attr);

						$attr = shortcode_atts (apply_filters("ws_plugin__s2member_sc_paypal_button_default_attrs", array("ids" => "0", "exp" => "72", "level" => "1", "ccaps" => "", "desc" => "", "ps" => "paypal", "lc" => "", "lang" => "", "cc" => "USD", "dg" => "0", "ns" => "0", "custom" => $_SERVER["HTTP_HOST"], "ta" => "0", "tp" => "0", "tt" => "D", "ra" => "0.01", "rp" => "1", "rt" => "M", "rr" => "1", "rrt" => "", "rra" => "1", "modify" => "0", "cancel" => "0", "sp" => "0", "image" => "default", "output" => "button"), get_defined_vars ()), $attr);

						$attr["modify"] = ($attr["modify"] === "1" && (!is_user_logged_in () || !get_user_option ("s2member_subscr_id")) && $attr["tp"]) ? "0" : $attr["modify"];

						$attr["lc"] = /* Locale code absolutely must be provided in upper-case format. Only after running shortcode_atts(). */ strtoupper ($attr["lc"]);
						$attr["tt"] = /* Term lengths absolutely must be provided in upper-case format. Only after running shortcode_atts(). */ strtoupper ($attr["tt"]);
						$attr["rt"] = /* Term lengths absolutely must be provided in upper-case format. Only after running shortcode_atts(). */ strtoupper ($attr["rt"]);
						$attr["rr"] = /* Must be provided in upper-case format. Numerical, or BN value. Only after running shortcode_atts(). */ strtoupper ($attr["rr"]);
						$attr["ccaps"] = /* Custom Capabilities must be typed in lower-case format. Only after running shortcode_atts(). */ strtolower ($attr["ccaps"]);
						$attr["ccaps"] = /* Custom Capabilities should not have spaces. */ str_replace(" ", "", $attr["ccaps"]);
						$attr["rr"] = /* Lifetime Subscriptions require Buy Now. Only after running shortcode_atts(). */ ($attr["rt"] === "L") ? "BN" : $attr["rr"];
						$attr["rr"] = /* Independent Ccaps require Buy Now. Only after running shortcode_atts(). */ ($attr["level"] === "*") ? "BN" : $attr["rr"];
						$attr["ns"] = /* No shipping directive must be 1 for digital items. After shortcode_atts(). */ ($attr["dg"] === "1") ? "1" : $attr["ns"];

						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_sc_paypal_button_after_shortcode_atts", get_defined_vars ());
						unset($__refs, $__v);

						if /* Cancellation Buttons. */ ($attr["cancel"])
							{
								$default_image = "https://www.paypal.com/" . (($attr["lang"]) ? $attr["lang"] : _x ("en_US", "s2member-front paypal-button-lang-code", "s2member")) . "/i/btn/btn_unsubscribe_LG.gif";

								$code = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-cancellation-button.php")));
								$code = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $code);
								$code = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $code);

								$code = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $code);
								$code = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $code);
								$code = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $code);

								$code = $_code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);

								$code = ($attr["output"] === "anchor") ? /* Already in anchor format; `button` format is not used in Cancellations. */ $code : $code;
								if ($attr["output"] === "url" && preg_match ('/ href\="(.*?)"/', $code, $m) && ($href = $m[1]))
									$code = ($url = c_ws_plugin__s2member_utils_urls::n_amps ($href));

								unset /* Just a little housekeeping */ ($href, $url, $m);

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_sc_paypal_cancellation_button", get_defined_vars ());
								unset($__refs, $__v);
							}
						else if /* Specific Post/Page Buttons. */ ($attr["sp"])
							{
								$default_image = "https://www.paypal.com/" . (($attr["lang"]) ? $attr["lang"] : _x ("en_US", "s2member-front paypal-button-lang-code", "s2member")) . "/i/btn/btn_xpressCheckout.gif";

								$paypal_on0_input_value = ($referencing = c_ws_plugin__s2member_utils_users::get_user_subscr_or_wp_id ()) ? "Referencing Customer ID" : "Originating Domain";
								$paypal_os0_input_value = /* Current User's Paid Subscr. ID, or WP User ID, or domain. */ ($referencing) ? $referencing : $_SERVER["HTTP_HOST"];

								$paypal_on1_input_value = /* Identifies the Customer's IP Address for tracking purposes. */ "Customer IP Address";
								$paypal_os1_input_value = /* Current User's IP Address for tracking purposes. */ $_SERVER["REMOTE_ADDR"];

								$paypal_invoice_input_value = /* s2Member's Unique Code~IP combo. */ uniqid () . "~" . $_SERVER["REMOTE_ADDR"];

								$attr["sp_ids_exp"] = /* Combined "sp:ids:expiration hours". */ "sp:" . $attr["ids"] . ":" . $attr["exp"];

								$success_return_url = /* s2Member handles this all by itself. However, it can be Filtered. */ home_url ("/?s2member_paypal_return=1");
								$success_return_url = apply_filters("ws_plugin__s2member_during_sc_paypal_button_success_return_url", $success_return_url, get_defined_vars ());

								$code = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-sp-checkout-button.php")));
								$code = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $code);
								$code = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $code);

								$code = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $code);
								$code = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $code);
								$code = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $code);
								$code = preg_replace ("/%%cancel_return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/"))), $code);
								$code = preg_replace ("/%%notify_url%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_notify=1"))), $code);
								$code = preg_replace ("/%%return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($success_return_url)), $code);
								$code = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["custom"])), $code);

								$code = preg_replace ('/ name\="lc" value\="(.*?)"/', ' name="lc" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["lc"])) . '"', $code);
								$code = preg_replace ('/ name\="no_shipping" value\="(.*?)"/', ' name="no_shipping" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ns"])) . '"', $code);
								$code = preg_replace ('/ name\="item_name" value\="(.*?)"/', ' name="item_name" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["desc"])) . '"', $code);
								$code = preg_replace ('/ name\="item_number" value\="(.*?)"/', ' name="item_number" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["sp_ids_exp"])) . '"', $code);
								$code = preg_replace ('/ name\="page_style" value\="(.*?)"/', ' name="page_style" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ps"])) . '"', $code);
								$code = preg_replace ('/ name\="currency_code" value\="(.*?)"/', ' name="currency_code" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["cc"])) . '"', $code);
								$code = preg_replace ('/ name\="custom" value\="(.*?)"/', ' name="custom" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["custom"])) . '"', $code);

								$code = preg_replace ('/ name\="invoice" value\="(.*?)"/', ' name="invoice" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_invoice_input_value)) . '"', $code);

								$code = preg_replace ('/ name\="on0" value\="(.*?)"/', ' name="on0" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_on0_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="os0" value\="(.*?)"/', ' name="os0" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_os0_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="on1" value\="(.*?)"/', ' name="on1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_on1_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="os1" value\="(.*?)"/', ' name="os1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_os1_input_value)) . '"', $code);

								$code = preg_replace ('/ name\="amount" value\="(.*?)"/', ' name="amount" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ra"])) . '"', $code);

								$code = $_code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);

								$code = ($attr["output"] === "anchor") ? '<a href="' . esc_attr (c_ws_plugin__s2member_utils_forms::form_whips_2_url ($code)) . '"><img src="' . esc_attr (($attr["image"] && $attr["image"] !== "default") ? $attr["image"] : $default_image) . '" style="width:auto; height:auto; border:0;" alt="PayPal" /></a>' : $code;
								$code = ($attr["output"] === "url") ? c_ws_plugin__s2member_utils_forms::form_whips_2_url ($code) : $code;

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_sc_paypal_sp_button", get_defined_vars ());
								unset($__refs, $__v);
							}
						else if /* Independent Custom Capabilities. */ ($attr["level"] === "*")
							{
								$default_image = "https://www.paypal.com/" . (($attr["lang"]) ? $attr["lang"] : _x ("en_US", "s2member-front paypal-button-lang-code", "s2member")) . "/i/btn/btn_xpressCheckout.gif";

								$paypal_on0_input_value = ($referencing = c_ws_plugin__s2member_utils_users::get_user_subscr_or_wp_id ()) ? "Referencing Customer ID" : "Originating Domain";
								$paypal_os0_input_value = /* Current User's Paid Subscr. ID, or WP User ID, or domain. */ ($referencing) ? $referencing : $_SERVER["HTTP_HOST"];

								$paypal_on1_input_value = /* Identifies the Customer's IP Address for tracking purposes. */ "Customer IP Address";
								$paypal_os1_input_value = /* Current User's IP Address for tracking purposes. */ $_SERVER["REMOTE_ADDR"];

								$paypal_invoice_input_value = /* s2Member's Unique Code~IP combo. */ uniqid () . "~" . $_SERVER["REMOTE_ADDR"];

								$attr["level_ccaps_eotper"] = ($attr["rr"] === "BN" && $attr["rt"] !== "L") ? $attr["level"] . ":" . $attr["ccaps"] . ":" . $attr["rp"] . " " . $attr["rt"] : $attr["level"] . ":" . $attr["ccaps"];
								$attr["level_ccaps_eotper"] = /* Clean any trailing separators from this string. */ rtrim ($attr["level_ccaps_eotper"], ":");

								$success_return_url = /* s2Member handles this all by itself. However, it can be Filtered. */ home_url ("/?s2member_paypal_return=1");
								$success_return_url = apply_filters("ws_plugin__s2member_during_sc_paypal_button_success_return_url", $success_return_url, get_defined_vars ());

								$code = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-ccaps-checkout-button.php")));
								$code = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $code);
								$code = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $code);

								$code = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $code);
								$code = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $code);
								$code = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $code);
								$code = preg_replace ("/%%cancel_return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/"))), $code);
								$code = preg_replace ("/%%notify_url%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_notify=1"))), $code);
								$code = preg_replace ("/%%return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($success_return_url)), $code);
								$code = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["custom"])), $code);

								$code = preg_replace ('/ name\="lc" value\="(.*?)"/', ' name="lc" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["lc"])) . '"', $code);
								$code = preg_replace ('/ name\="no_shipping" value\="(.*?)"/', ' name="no_shipping" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ns"])) . '"', $code);
								$code = preg_replace ('/ name\="item_name" value\="(.*?)"/', ' name="item_name" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["desc"])) . '"', $code);
								$code = preg_replace ('/ name\="item_number" value\="(.*?)"/', ' name="item_number" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["level_ccaps_eotper"])) . '"', $code);
								$code = preg_replace ('/ name\="page_style" value\="(.*?)"/', ' name="page_style" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ps"])) . '"', $code);
								$code = preg_replace ('/ name\="currency_code" value\="(.*?)"/', ' name="currency_code" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["cc"])) . '"', $code);
								$code = preg_replace ('/ name\="custom" value\="(.*?)"/', ' name="custom" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["custom"])) . '"', $code);

								$code = preg_replace ('/ name\="invoice" value\="(.*?)"/', ' name="invoice" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_invoice_input_value)) . '"', $code);

								$code = preg_replace ('/ name\="on0" value\="(.*?)"/', ' name="on0" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_on0_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="os0" value\="(.*?)"/', ' name="os0" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_os0_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="on1" value\="(.*?)"/', ' name="on1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_on1_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="os1" value\="(.*?)"/', ' name="os1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_os1_input_value)) . '"', $code);

								$code = preg_replace ('/ name\="amount" value\="(.*?)"/', ' name="amount" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ra"])) . '"', $code);

								$code = $_code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);

								$code = ($attr["output"] === "anchor") ? '<a href="' . esc_attr (c_ws_plugin__s2member_utils_forms::form_whips_2_url ($code)) . '"><img src="' . esc_attr (($attr["image"] && $attr["image"] !== "default") ? $attr["image"] : $default_image) . '" style="width:auto; height:auto; border:0;" alt="PayPal" /></a>' : $code;
								$code = ($attr["output"] === "url") ? c_ws_plugin__s2member_utils_forms::form_whips_2_url ($code) : $code;

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								do_action("ws_plugin__s2member_during_sc_paypal_ccaps_button", get_defined_vars ());
								unset($__refs, $__v);
							}
						else // Otherwise, we'll process this Button normally, using Membership routines.
							{
								$default_image = "https://www.paypal.com/" . (($attr["lang"]) ? $attr["lang"] : _x ("en_US", "s2member-front paypal-button-lang-code", "s2member")) . "/i/btn/btn_xpressCheckout.gif";

								$paypal_on0_input_value = ($referencing = c_ws_plugin__s2member_utils_users::get_user_subscr_or_wp_id ()) ? "Referencing Customer ID" : "Originating Domain";
								$paypal_os0_input_value = /* Current User's Paid Subscr. ID, or WP User ID, or domain. */ ($referencing) ? $referencing : $_SERVER["HTTP_HOST"];

								$paypal_on1_input_value = /* Identifies the Customer's IP Address for tracking purposes. */ "Customer IP Address";
								$paypal_os1_input_value = /* Current User's IP Address for tracking purposes. */ $_SERVER["REMOTE_ADDR"];

								$paypal_invoice_input_value = /* s2Member's Unique Code~IP combo. */ uniqid () . "~" . $_SERVER["REMOTE_ADDR"];

								$attr["desc"] = (!$attr["desc"]) ? $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $attr["level"] . "_label"] : $attr["desc"];

								$attr["level_ccaps_eotper"] = ($attr["rr"] === "BN" && $attr["rt"] !== "L") ? $attr["level"] . ":" . $attr["ccaps"] . ":" . $attr["rp"] . " " . $attr["rt"] : $attr["level"] . ":" . $attr["ccaps"];
								$attr["level_ccaps_eotper"] = /* Clean any trailing separators from this string. */ rtrim ($attr["level_ccaps_eotper"], ":");

								$success_return_tra = array("ta" => $attr["ta"], "tp" => $attr["tp"], "tt" => $attr["tt"], "ra" => $attr["ra"], "rp" => $attr["rp"], "rt" => $attr["rt"], "rr" => $attr["rr"], "rrt" => $attr["rrt"], "rra" => $attr["rra"], "invoice" => $paypal_invoice_input_value, "checksum" => md5 ($paypal_invoice_input_value . $_SERVER["REMOTE_ADDR"] . $attr["level_ccaps_eotper"]));

								$success_return_url = /* s2Member handles this all by itself. However, it can be Filtered (see below). */ home_url ("/?s2member_paypal_return=1");
								$success_return_url = add_query_arg ("s2member_paypal_return_tra", urlencode (c_ws_plugin__s2member_utils_encryption::encrypt (serialize ($success_return_tra))), $success_return_url);
								$success_return_url = apply_filters("ws_plugin__s2member_during_sc_paypal_button_success_return_url", $success_return_url, get_defined_vars ());

								$code = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-checkout-button.php")));
								$code = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $code);
								$code = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $code);

								$code = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $code);
								$code = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $code);
								$code = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $code);
								$code = preg_replace ("/%%level_label%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $attr["level"] . "_label"])), $code);
								$code = preg_replace ("/%%cancel_return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/"))), $code); // This brings them back to Front Page.
								$code = preg_replace ("/%%notify_url%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_notify=1"))), $code);
								$code = preg_replace ("/%%return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($success_return_url)), $code);
								$code = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["custom"])), $code);
								$code = preg_replace ("/%%level%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["level"])), $code);

								$code = preg_replace ('/ \<\!--(\<input type\="hidden" name\="(amount|src|srt|sra|a1|p1|t1|a3|p3|t3)" value\="(.*?)" \/\>)--\>/', " $1", $code);
								$code = ($attr["rr"] === "BN") ? preg_replace ('/ (\<input type\="hidden" name\="cmd" value\=")(.*?)(" \/\>)/', " $1_xclick$3", $code) : $code;
								$code = ($attr["rr"] === "BN") ? preg_replace ('/ (\<input type\="hidden" name\="(src|srt|sra|a1|p1|t1|a3|p3|t3)" value\="(.*?)" \/\>)/', " <!--$1-->", $code) : $code;
								$code = ($attr["rr"] === "BN" || !$attr["tp"]) ? preg_replace ('/ (\<input type\="hidden" name\="(a1|p1|t1)" value\="(.*?)" \/\>)/', " <!--$1-->", $code) : $code;
								$code = ($attr["rr"] !== "BN") ? preg_replace ('/ (\<input type\="hidden" name\="cmd" value\=")(.*?)(" \/\>)/', " $1_xclick-subscriptions$3", $code) : $code;
								$code = ($attr["rr"] !== "BN") ? preg_replace ('/ (\<input type\="hidden" name\="amount" value\="(.*?)" \/\>)/', " <!--$1-->", $code) : $code;

								$code = preg_replace ('/ name\="lc" value\="(.*?)"/', ' name="lc" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["lc"])) . '"', $code);
								$code = preg_replace ('/ name\="no_shipping" value\="(.*?)"/', ' name="no_shipping" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ns"])) . '"', $code);
								$code = preg_replace ('/ name\="item_name" value\="(.*?)"/', ' name="item_name" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["desc"])) . '"', $code);
								$code = preg_replace ('/ name\="item_number" value\="(.*?)"/', ' name="item_number" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["level_ccaps_eotper"])) . '"', $code);
								$code = preg_replace ('/ name\="page_style" value\="(.*?)"/', ' name="page_style" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ps"])) . '"', $code);
								$code = preg_replace ('/ name\="currency_code" value\="(.*?)"/', ' name="currency_code" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["cc"])) . '"', $code);
								$code = preg_replace ('/ name\="custom" value\="(.*?)"/', ' name="custom" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["custom"])) . '"', $code);

								$code = preg_replace ('/ name\="invoice" value\="(.*?)"/', ' name="invoice" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_invoice_input_value)) . '"', $code);

								$code = preg_replace ('/ name\="on0" value\="(.*?)"/', ' name="on0" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_on0_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="os0" value\="(.*?)"/', ' name="os0" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_os0_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="on1" value\="(.*?)"/', ' name="on1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_on1_input_value)) . '"', $code);
								$code = preg_replace ('/ name\="os1" value\="(.*?)"/', ' name="os1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($paypal_os1_input_value)) . '"', $code);

								$code = preg_replace ('/ name\="modify" value\="(.*?)"/', ' name="modify" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["modify"])) . '"', $code);

								$code = preg_replace ('/ name\="amount" value\="(.*?)"/', ' name="amount" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ra"])) . '"', $code);

								$code = preg_replace ('/ name\="src" value\="(.*?)"/', ' name="src" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["rr"])) . '"', $code);
								$code = preg_replace ('/ name\="srt" value\="(.*?)"/', ' name="srt" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["rrt"])) . '"', $code);
								$code = preg_replace ('/ name\="sra" value\="(.*?)"/', ' name="sra" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["rra"])) . '"', $code);

								$code = preg_replace ('/ name\="a1" value\="(.*?)"/', ' name="a1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ta"])) . '"', $code);
								$code = preg_replace ('/ name\="p1" value\="(.*?)"/', ' name="p1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["tp"])) . '"', $code);
								$code = preg_replace ('/ name\="t1" value\="(.*?)"/', ' name="t1" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["tt"])) . '"', $code);
								$code = preg_replace ('/ name\="a3" value\="(.*?)"/', ' name="a3" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["ra"])) . '"', $code);
								$code = preg_replace ('/ name\="p3" value\="(.*?)"/', ' name="p3" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["rp"])) . '"', $code);
								$code = preg_replace ('/ name\="t3" value\="(.*?)"/', ' name="t3" value="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["rt"])) . '"', $code);

								$code = $_code = ($attr["image"] && $attr["image"] !== "default") ? preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($attr["image"])) . '"', $code) : preg_replace ('/ src\="(.*?)"/', ' src="' . c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($default_image)) . '"', $code);

								$code = ($attr["output"] === "anchor") ? '<a href="' . esc_attr (c_ws_plugin__s2member_utils_forms::form_whips_2_url ($code)) . '"><img src="' . esc_attr (($attr["image"] && $attr["image"] !== "default") ? $attr["image"] : $default_image) . '" style="width:auto; height:auto; border:0;" alt="PayPal" /></a>' : $code;
								$code = ($attr["output"] === "url") ? c_ws_plugin__s2member_utils_forms::form_whips_2_url ($code) : $code;

								foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
								($attr["modify"]) ? do_action("ws_plugin__s2member_during_sc_paypal_modification_button", get_defined_vars ()) : do_action("ws_plugin__s2member_during_sc_paypal_button", get_defined_vars ());
								unset($__refs, $__v);
							}
						$code = c_ws_plugin__s2member_sc_paypal_button_e::sc_paypal_button_encryption ($code, get_defined_vars ());

						return apply_filters("ws_plugin__s2member_sc_paypal_button", $code, get_defined_vars ());
					}
			}
	}
