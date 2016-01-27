<?php
/**
* Menu page for the s2Member plugin (PayPal Button Generator page).
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
* @package s2Member\Menu_Pages
* @since 3.0
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit ("Do not access this file directly.");

if (!class_exists ("c_ws_plugin__s2member_menu_page_paypal_buttons"))
	{
		/**
		* Menu page for the s2Member plugin (PayPal Button Generator page).
		*
		* @package s2Member\Menu_Pages
		* @since 110531
		*/
		class c_ws_plugin__s2member_menu_page_paypal_buttons
			{
				public function __construct ()
					{
						echo '<div class="wrap ws-menu-page">' . "\n";

						echo '<div class="ws-menu-page-toolbox">'."\n";
						c_ws_plugin__s2member_menu_pages_tb::display ();
						echo '</div>'."\n";

						echo '<h2>PayPal Buttons</h2>' . "\n";

						echo '<table class="ws-menu-page-table">' . "\n";
						echo '<tbody class="ws-menu-page-table-tbody">' . "\n";
						echo '<tr class="ws-menu-page-table-tr">' . "\n";
						echo '<td class="ws-menu-page-table-l">' . "\n";

						do_action("ws_plugin__s2member_during_paypal_buttons_page_before_left_sections", get_defined_vars ());

						for ($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++) // Starting with Level #1 here.
							{
								if (($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_levelN_buttons = "ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_level" . $n . "_buttons") && apply_filters($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_levelN_buttons, true, get_defined_vars ()))
									{
										if (($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_levelN_buttons = "ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_level" . $n . "_buttons"))
											do_action($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_levelN_buttons, get_defined_vars ());

										echo '<div class="ws-menu-page-group" title="Buttons For Level #' . $n . ' Access">' . "\n";

										echo '<div class="ws-menu-page-section ws-plugin--s2member-level' . $n . '-buttons-section">' . "\n";
										echo '<h3>Button Code Generator For Level #' . $n . ' Access</h3>' . "\n";
										echo '<p>Very simple. All you do is customize the form fields provided, for each Membership Level that you plan to offer. Then press (Generate Button Code). These special PayPal Buttons are customized to work with s2Member seamlessly. Member accounts will be activated instantly, in an automated fashion. When you, or a Member, cancels their Membership, or fails to make payments on time, s2Member will automatically terminate their Membership privileges. s2Member makes extensive use of the PayPal IPN service. s2Member receives updates from PayPal behind-the-scene.</p>' . "\n";
										echo '<p><em><strong>Please note:</strong> buttons are NOT saved here. This is only a Button Generator. Once you\'ve generated your Button, copy/paste it into your Membership Options Page. If you lose your Button Code, you\'ll need to come back &amp; re-generate a new one. If you\'re in Sandbox Test-Mode, and you\'re NOT using the Shortcode Format, please remember to come back and re-generate your Buttons before you go live.</em></p>' . "\n";

										if (($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_levelN_buttons = "ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_level" . $n . "_buttons"))
											do_action($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_levelN_buttons, get_defined_vars ());

										echo '<table class="form-table">' . "\n";
										echo '<tbody>' . "\n";
										echo '<tr>' . "\n";

										echo '<th class="ws-menu-page-th-side">' . "\n";
										echo '<label for="ws-plugin--s2member-level' . $n . '-shortcode">' . "\n";
										echo 'Button Code<br />For Level #' . $n . ':<br /><br />' . "\n";
										echo '<div id="ws-plugin--s2member-level' . $n . '-button-prev"></div>' . "\n";
										echo '</label>' . "\n";
										echo '</th>' . "\n";

										echo '<td>' . "\n";
										echo '<form onsubmit="return false;" autocomplete="off">' . "\n";
										echo '<p id="ws-plugin--s2member-level' . $n . '-trial-line">I\'ll offer the first <input type="text" autocomplete="off" id="ws-plugin--s2member-level' . $n . '-trial-period" value="0" size="6" /> <select id="ws-plugin--s2member-level' . $n . '-trial-term">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-membership-trial-terms.php"))) . '</select> @ $<input type="text" autocomplete="off" id="ws-plugin--s2member-level' . $n . '-trial-amount" value="0.00" size="4" /></p>' . "\n";
										echo '<p><span id="ws-plugin--s2member-level' . $n . '-trial-then">Then, </span>I want to charge: $<input type="text" autocomplete="off" id="ws-plugin--s2member-level' . $n . '-amount" value="0.01" size="4" /> / <select id="ws-plugin--s2member-level' . $n . '-term">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-membership-regular-terms.php"))) . '</select></p>' . "\n";
										echo '<p>Checkout Page Style <a href="#" onclick="alert(\'Optional. This can be configured inside your PayPal account. PayPal allows you to create Custom Page Styles, and assign a unique name to them. You can add your own header image and color selection to the checkout form. Once you\\\'ve created a Custom Page Style at PayPal, you can enter that Page Style here.\\n\\nIn addition. The Shortcode below, provided by s2Member; supports an image attribute: image=\\\'\\\'default\\\'\\\'. This can be changed to a full URL, pointing to a custom image of your own; instead of the default PayPal Button image.\'); return false;" tabindex="-1">[?]</a>: <input type="text" autocomplete="off" id="ws-plugin--s2member-level' . $n . '-page-style" value="paypal" size="18" /> <select id="ws-plugin--s2member-level' . $n . '-currency">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-currencies.php"))) . '</select> <input type="button" value="Generate Button Code" onclick="ws_plugin__s2member_paypalButtonGenerate(\'level' . $n . '\');" /></p>' . "\n";
										echo '<p>Description: <input type="text" autocomplete="off" id="ws-plugin--s2member-level' . $n . '-desc" value="' . format_to_edit ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $n . "_label"]) . ' / description and pricing details here." size="73" /></p>' . "\n";
										echo '<p' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '>Custom Capabilities (comma-delimited) <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: s2Member → API Scripting → Custom Capabilities.\\n\\n*ADVANCED TIP: You can specifiy a list of Custom Capabilities that will be (Added) with this purchase. Or, you could tell s2Member to (Remove All) Custom Capabilities that may or may not already exist for a particular Member, and (Add) only the new ones that you specify. To do this, just start your list of Custom Capabilities with `-all`.\\n\\nSo instead of just (Adding) Custom Capabilities:\\nmusic,videos,archives,gifts\\n\\nYou could (Remove All) that may already exist, and then (Add) new ones:\\n-all,calendar,forums,tools\\n\\nOr to just (Remove All) and (Add) nothing:\\n-all\'); return false;" tabindex="-1">[?]</a> <input type="text" maxlength="125" autocomplete="off" id="ws-plugin--s2member-level' . $n . '-ccaps" size="40" /></p>' . "\n";
										echo '</form>' . "\n";
										echo '</td>' . "\n";

										echo '</tr>' . "\n";
										echo '<tr>' . "\n";

										echo '<td colspan="2">' . "\n";
										echo '<form onsubmit="return false;" autocomplete="off">' . "\n";

										if (($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_levelN_buttons_before_shortcode = "ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_level" . $n . "_buttons_before_shortcode"))
											do_action($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_levelN_buttons_before_shortcode, get_defined_vars ());

										echo '<strong>WordPress Shortcode:</strong> (recommended for both the WordPress Visual &amp; HTML Editors)<br />' . "\n";
										$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/paypal-checkout-button-shortcode.php")));
										$ws_plugin__s2member_temp_s = preg_replace ("/%%level%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($n)), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%level_label%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $n . "_label"])), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__s2member_temp_s);
										echo '<input type="text" autocomplete="off" id="ws-plugin--s2member-level' . $n . '-shortcode" value="' . format_to_edit ($ws_plugin__s2member_temp_s) . '" onclick="this.select ();" />' . "\n";

										echo '<div' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '><br />' . "\n";
										echo '<strong>Resulting PayPal Button Code:</strong> (ultimately, your Shortcode will produce this snippet)<br />' . "\n";
										echo '<textarea id="ws-plugin--s2member-level' . $n . '-button" rows="8" wrap="off" onclick="this.select ();">';
										$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-checkout-button.php")));
										$ws_plugin__s2member_temp_s = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%level%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($n)), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%level_label%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level" . $n . "_label"])), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%cancel_return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/"))), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%notify_url%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_notify=1"))), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_return=1"))), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $ws_plugin__s2member_temp_s);
										$ws_plugin__s2member_temp_s = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $ws_plugin__s2member_temp_s);
										echo format_to_edit ($ws_plugin__s2member_temp_s);
										echo '</textarea><br />' . "\n";
										echo '&uarr; <em>This <span class="ws-menu-page-hilite">may contain PHP code too</span>; so be careful if you use this.</em>' . "\n";
										echo '</div>' . "\n";

										echo '</form>' . "\n";
										echo '</td>' . "\n";

										echo '</tr>' . "\n";
										echo '</tbody>' . "\n";
										echo '</table>' . "\n";
										echo '</div>' . "\n";

										echo '</div>' . "\n";

										if (($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_levelN_buttons = "ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_level" . $n . "_buttons"))
											do_action($ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_levelN_buttons, get_defined_vars ());
									}
							}

						if (apply_filters("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_modification_buttons", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_modification_buttons", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Subscr. Modification Buttons">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-modification-buttons-section">' . "\n";
								echo '<h3>Button Code Generator For Subscription Modifications</h3>' . "\n";
								echo '<p>If you\'d like to give your Members <em>(and/or your Free Subscribers)</em> the ability to modify their billing plan, by switching to a more expensive option, or a less expensive option; generate a new PayPal Modification Button here. Configure the updated Level, pricing, terms, etc. Then, make that new Modification Button available to Members who are logged into their existing account with you. For example, you might want to insert a "Level #2" Upgrade Button into your Login Welcome Page, which would up-sell existing Level #1 Members to a more expensive plan that you offer.</p>' . "\n";
								echo '<p><em><strong>Important Note:</strong> Modification Buttons should be displayed to existing Users/Members, and they should be logged-in, BEFORE clicking this Button. Otherwise, post-processing of their transaction will fail to recognize the Customer\'s existing account within WordPress. Please display this Button only to Users/Members that are already logged into their account (perhaps in your Login Welcome Page for s2Member), or in another location where you can be absolutely sure that a User/Member is logged in. s2Member\'s Simple Conditionals could also be used to ensure a User/Member is logged in, by wrapping your Shortcode within a Conditional test. For further details, please see: <strong>s2Member → API Scripting → Simple Conditionals</strong>.</em></p>' . "\n";
								echo '<p><em><strong>Modification Process:</strong> When you send a Member to PayPal using a Subscription Modification Button, PayPal will ask them to login. Once they\'re logged in, instead of being able to signup for a new Membership, PayPal will provide them with the ability to upgrade and/or downgrade their existing Membership with you, by allowing them to switch to the Membership Plan that was specified in the Subscription Modification Button. PayPal handles this nicely, and you\'ll be happy to know that s2Member has been pre-configured to deal with this scenario as well, so that everything remains automated. Their Membership Access Level will either be promoted, or demoted, based on the actions they took at PayPal during the modification process. Once an existing Member completes their Subscription Modification at PayPal, they\'ll be brought back to their Login Welcome Page, instead of to the registration screen.</em></p>' . "\n";
								echo '<p><em><strong>Also Works For Free Subscribers:</strong> Although a Free Subscriber does not have an existing PayPal Subscription, s2Member is capable of adapting to this scenario gracefully. Just make sure that your existing Free Subscribers <em>(the ones who wish to upgrade)</em> pay for their Membership through a Modification Button generated by s2Member. That will allow them to continue using their existing account with you. In other words, they can keep their existing Username <em>(and anything already associated with that Username)</em>, rather than being forced to re-register after checkout.</em></p>' . "\n";
								echo '<p><em><strong>Make It More User-Friendly:</strong> You can make the Subscription Modification Process, more user-friendly, by setting up a <a href="#" onclick="alert(\'Optional. This can be configured inside your PayPal account. PayPal allows you to create Custom Page Styles, and assign a unique name to them. You can add your own header image and color selection to the checkout form. Once you\\\'ve created a Custom Page Style at PayPal, you can tell s2Member to use that Page Style whenever you generate your Button Code.\'); return false;">Custom Page Style at PayPal</a>, specifically for Subscription Modification Buttons. Use a custom header image, with a brief explanation to the Customer. Something like, "Log into PayPal", "You can Modify your Subscription!".</em></p>' . "\n";
								echo '<p><em><strong>Integrating Conditionals:</strong> Since each Modification Button is configured for a specific Level, you may want to create multiple Modification Buttons, one for each combination you intend to make available. s2Member\'s API Conditionals can help you display the proper Button to each Customer, based on the status of their existing account. For further details, see: <strong>s2Member → API Scripting</strong>.</em></p>' . "\n";
								echo (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()) ? '<p><em><strong>Independent Custom Capabilities:</strong> If you just want to sell an existing Member new Custom Capabilities, without affecting their paid Subscription in any way, please see the next Button Generator: <code>Capability (Buy Now) Buttons</code>. Independent Capability Buttons facilitate Buy Now functionality, specifically for Custom Capabilities, without affecting the Customer\'s primary Subscription and Membership Level Access.</em></p>' . "\n" : '';
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_modification_buttons", get_defined_vars ());

								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";

								echo '<th class="ws-menu-page-th-side">' . "\n";
								echo '<label for="ws-plugin--s2member-modification-shortcode">' . "\n";
								echo 'Button Code<br />For Modifications:<br /><br />' . "\n";
								echo '<div id="ws-plugin--s2member-modification-button-prev"></div>' . "\n";
								echo '</label>' . "\n";
								echo '</th>' . "\n";

								echo '<td>' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";

								echo '<p>Modification: <select id="ws-plugin--s2member-modification-level">' . "\n";

								for ($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
									{
										echo '<optgroup label="Level #' . $n . '">' . "\n";
										echo '<option value="upgrade:' . $n . '">&uarr; Upgrade To Level #' . $n . '</option>' . "\n";
										echo ($n < $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]) ? '<option value="downgrade:' . $n . '">&darr; Downgrade To Level #' . $n . '</option>' . "\n" : '';
										echo '</optgroup>' . "\n";

										echo ($n < $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]) ? '<option disabled="disabled"></option>' . "\n" : '';
									}

								echo '</select></p>' . "\n";

								echo '<p id="ws-plugin--s2member-modification-trial-line">I\'ll offer the first <input type="text" autocomplete="off" id="ws-plugin--s2member-modification-trial-period" value="0" size="6" /> <select id="ws-plugin--s2member-modification-trial-term">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-membership-trial-terms.php"))) . '</select> @ $<input type="text" autocomplete="off" id="ws-plugin--s2member-modification-trial-amount" value="0.00" size="4" /></p>' . "\n";
								echo '<p><span id="ws-plugin--s2member-modification-trial-then">Then, </span>I want to charge: $<input type="text" autocomplete="off" id="ws-plugin--s2member-modification-amount" value="0.01" size="4" /> / <select id="ws-plugin--s2member-modification-term">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-membership-regular-terms.php"))) . '</select></p>' . "\n";
								echo '<p>Checkout Page Style <a href="#" onclick="alert(\'Optional. This can be configured inside your PayPal account. PayPal allows you to create Custom Page Styles, and assign a unique name to them. You can add your own header image and color selection to the checkout form. Once you\\\'ve created a Custom Page Style at PayPal, you can enter that Page Style here.\\n\\nIn addition. The Shortcode below, provided by s2Member; supports an image attribute: image=\\\'\\\'default\\\'\\\'. This can be changed to a full URL, pointing to a custom image of your own; instead of the default PayPal Button image.\'); return false;" tabindex="-1">[?]</a>: <input type="text" autocomplete="off" id="ws-plugin--s2member-modification-page-style" value="paypal" size="18" /> <select id="ws-plugin--s2member-modification-currency">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-currencies.php"))) . '</select> <input type="button" value="Generate Button Code" onclick="ws_plugin__s2member_paypalButtonGenerate(\'modification\');" /></p>' . "\n";
								echo '<p>Description: <input type="text" autocomplete="off" id="ws-plugin--s2member-modification-desc" value="Description and pricing details here." size="73" /></p>' . "\n";
								echo '<p' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '>Custom Capabilities (comma-delimited) <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: s2Member → API Scripting → Custom Capabilities.\\n\\n*ADVANCED TIP: You can specifiy a list of Custom Capabilities that will be (Added) with this purchase. Or, you could tell s2Member to (Remove All) Custom Capabilities that may or may not already exist for a particular Member, and (Add) only the new ones that you specify. To do this, just start your list of Custom Capabilities with `-all`.\\n\\nSo instead of just (Adding) Custom Capabilities:\\nmusic,videos,archives,gifts\\n\\nYou could (Remove All) that may already exist, and then (Add) new ones:\\n-all,calendar,forums,tools\\n\\nOr to just (Remove All) and (Add) nothing:\\n-all\'); return false;" tabindex="-1">[?]</a> <input type="text" maxlength="125" autocomplete="off" id="ws-plugin--s2member-modification-ccaps" size="40" /></p>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '<tr>' . "\n";

								echo '<td colspan="2">' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_modification_buttons_before_shortcode", get_defined_vars ());
								echo '<strong>WordPress Shortcode:</strong> (recommended for both the WordPress Visual &amp; HTML Editors)<br />' . "\n";
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/paypal-checkout-button-shortcode.php")));
								$ws_plugin__s2member_temp_s = preg_replace ("/%%level%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ("1")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%level_label%% /", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level1_label"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/\/]$/", 'modify="1" /]', $ws_plugin__s2member_temp_s); // Adds modify="1" to the end of the Shortcode.
								echo '<input type="text" autocomplete="off" id="ws-plugin--s2member-modification-shortcode" value="' . format_to_edit ($ws_plugin__s2member_temp_s) . '" onclick="this.select ();" />' . "\n";

								echo '<div' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '><br />' . "\n";
								echo '<strong>Resulting PayPal Button Code:</strong> (ultimately, your Shortcode will produce this snippet)<br />' . "\n";
								echo '<textarea id="ws-plugin--s2member-modification-button" rows="8" wrap="off" onclick="this.select ();">';
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-checkout-button.php")));
								$ws_plugin__s2member_temp_s = preg_replace ('/name\="modify" value\="(.*?)"/', 'name="modify" value="1"', $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%level%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ("1")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%level_label%% /", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level1_label"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%cancel_return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%notify_url%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_notify=1"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_return=1"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $ws_plugin__s2member_temp_s);
								echo format_to_edit ($ws_plugin__s2member_temp_s);
								echo '</textarea><br />' . "\n";
								echo '&uarr; <em>This <span class="ws-menu-page-hilite">may contain PHP code too</span>; so be careful if you use this.</em>' . "\n";
								echo '</div>' . "\n";

								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_modification_buttons", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_ccap_buttons", (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()), get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_ccap_buttons", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Capability (Buy Now) Buttons">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-ccap-buttons-section">' . "\n";
								echo '<h3>Button Code Generator For Independent Custom Capabilities</h3>' . "\n";
								echo '<p>This is VERY advanced. For further details, please check your Dashboard: <strong>s2Member → API Scripting → Custom Capabiities</strong>.</p>' . "\n";
								echo '<p>With s2Member, you can sell one or more Custom Capabilities using Buy Now functionality, to "existing" Users/Members, regardless of which Membership Level they have on your site <em>(i.e., you could even sell Independent Custom Capabilities to Users at Membership Level #0, normally referred to as Free Subscribers, if you like)</em>. So this is quite flexible. Independent Custom Capabilities do NOT rely on any specific Membership Level. That\'s why s2Member refers to these as `Independent` Custom Capabilities, because you can sell Capabilities this way, through Buy Now functionality, and the Customer\'s Membership Level Access, along with any existing paid Subscription they may already have with you, will remain completely unaffected. That being said, if you intend to charge a recurring fee for Custom Capabilities, please use a <code>Subscr. Modification Button</code> instead; because Independent Custom Capabilities can only be sold through Buy Now functionality.</p>' . "\n";
								echo '<p>Independent Custom Capabilities are added to a Customer\'s account immediately after checkout, and the Customer will have the Custom Capabilities for as long as their Membership lasts, based on their primary Subscription with your site, and/or forever, if they have a Lifetime account with you. In other words, Independent Custom Capabilities will exist on the Customer\'s account forever, or until an EOT <em>(End Of Term)</em> occurs on their primary Subscription with you; in which case s2Member would demote or delete the Customer\'s account <em>(based on your EOT configuration)</em>, and all Custom Capabilities are removed as well.</p>' . "\n";
								echo '<p>Very simple. All you do is customize the form fields provided, for each set of Custom Capabilities that you plan to sell. Then press (Generate Button Code). These special PayPal Buttons are customized to work with s2Member seamlessly. The Customer will be granted additional access to one or more Custom Capabilities that you specify; while the Customer\'s Membership Level Access and any existing paid Subscription they may already have with you, will remain completely unaffected.</p>' . "\n";
								echo '<p><em><strong>Important Note:</strong> Independent Custom Capability Buttons should ONLY be displayed to existing Users/Members, and they MUST be logged-in, BEFORE clicking this Button. Otherwise, post-processing of their transaction will fail to recognize the Customer\'s existing account within WordPress. Please display this Button only to Users/Members that are already logged into their account (perhaps in your Login Welcome Page for s2Member), or in another location where you can be absolutely sure that a User/Member is logged in. s2Member\'s Simple Conditionals could also be used to ensure a User/Member is logged in, by wrapping your Shortcode within a Conditional test. For further details, please see: <strong>s2Member → API Scripting → Simple Conditionals</strong>.</em></p>' . "\n";
								echo '<p><em><strong>Please note:</strong> buttons are NOT saved here. This is only a Button Generator. Once you\'ve generated your Button, copy/paste it into your WordPress Editor. If you lose your Button Code, you\'ll need to come back &amp; re-generate a new one. If you\'re in Sandbox Test-Mode, and you\'re NOT using the Shortcode Format, please remember to come back and re-generate your Buttons before you go live.</em></p>' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_ccap_buttons", get_defined_vars ());

								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";

								echo '<th class="ws-menu-page-th-side">' . "\n";
								echo '<label for="ws-plugin--s2member-ccap-shortcode">' . "\n";
								echo 'Button Code<br />For Capabilities:<br /><br />' . "\n";
								echo '<div id="ws-plugin--s2member-ccap-button-prev"></div>' . "\n";
								echo '</label>' . "\n";
								echo '</th>' . "\n";

								echo '<td>' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";
								echo '<p>I want to charge: $<input type="text" autocomplete="off" id="ws-plugin--s2member-ccap-amount" value="0.01" size="4" /> / <select id="ws-plugin--s2member-ccap-term">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-membership-ccap-terms.php"))) . '</select></p>' . "\n";
								echo '<p>Checkout Page Style <a href="#" onclick="alert(\'Optional. This can be configured inside your PayPal account. PayPal allows you to create Custom Page Styles, and assign a unique name to them. You can add your own header image and color selection to the checkout form. Once you\\\'ve created a Custom Page Style at PayPal, you can enter that Page Style here.\\n\\nIn addition. The Shortcode below, provided by s2Member; supports an image attribute: image=\\\'\\\'default\\\'\\\'. This can be changed to a full URL, pointing to a custom image of your own; instead of the default PayPal Button image.\'); return false;" tabindex="-1">[?]</a>: <input type="text" autocomplete="off" id="ws-plugin--s2member-ccap-page-style" value="paypal" size="18" /> <select id="ws-plugin--s2member-ccap-currency">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-currencies.php"))) . '</select> <input type="button" value="Generate Button Code" onclick="ws_plugin__s2member_paypalCcapButtonGenerate();" /></p>' . "\n";
								echo '<p>Description: <input type="text" autocomplete="off" id="ws-plugin--s2member-ccap-desc" value="Description and pricing details here." size="73" /></p>' . "\n";
								echo '<p>Custom Capabilities (comma-delimited) <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: s2Member → API Scripting → Custom Capabilities.\\n\\n*ADVANCED TIP: You can specifiy a list of Custom Capabilities that will be (Added) with this purchase. Or, you could tell s2Member to (Remove All) Custom Capabilities that may or may not already exist for a particular Member, and (Add) only the new ones that you specify. To do this, just start your list of Custom Capabilities with `-all`.\\n\\nSo instead of just (Adding) Custom Capabilities:\\nmusic,videos,archives,gifts\\n\\nYou could (Remove All) that may already exist, and then (Add) new ones:\\n-all,calendar,forums,tools\'); return false;" tabindex="-1">[?]</a> <input type="text" maxlength="125" autocomplete="off" id="ws-plugin--s2member-ccap-ccaps" size="40" /></p>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '<tr>' . "\n";

								echo '<td colspan="2">' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_ccap_buttons_before_shortcode", get_defined_vars ());
								echo '<strong>WordPress Shortcode:</strong> (recommended for both the WordPress Visual &amp; HTML Editors)<br />' . "\n";
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/paypal-ccaps-checkout-button-shortcode.php")));
								$ws_plugin__s2member_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__s2member_temp_s);
								echo '<input type="text" autocomplete="off" id="ws-plugin--s2member-ccap-shortcode" value="' . format_to_edit ($ws_plugin__s2member_temp_s) . '" onclick="this.select ();" />' . "\n";

								echo '<div' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '><br />' . "\n";
								echo '<strong>Resulting PayPal Button Code:</strong> (ultimately, your Shortcode will produce this snippet)<br />' . "\n";
								echo '<textarea id="ws-plugin--s2member-ccap-button" rows="8" wrap="off" onclick="this.select ();">';
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-ccaps-checkout-button.php")));
								$ws_plugin__s2member_temp_s = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%cancel_return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%notify_url%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_notify=1"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_return=1"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $ws_plugin__s2member_temp_s);
								echo format_to_edit ($ws_plugin__s2member_temp_s);
								echo '</textarea><br />' . "\n";
								echo '&uarr; <em>This <span class="ws-menu-page-hilite">may contain PHP code too</span>; so be careful if you use this.</em>' . "\n";
								echo '</div>' . "\n";

								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_ccap_buttons", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_cancellation_buttons", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_cancellation_buttons", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Subscr. Cancellation Buttons">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-cancellation-buttons-section">' . "\n";
								echo '<h3>One Button Does It All For Cancellations (copy/paste)</h3>' . "\n";
								echo '<p>Since all recurring charges are associated with a PayPal Subscription; and every PayPal Subscription is associated with a PayPal Account; your Members will always have a PayPal Account of their own, which is tied to their Membership with you. So... a Member can simply log into their own PayPal account and cancel their Subscription(s) with you at anytime, all on their own. However, some Customers do not realize this. So, if you would like to make it clearer (easier) for Members to cancel their own Subscription(s), you can provide this Cancellation Button for them on your Login Welcome Page, or somewhere in the support section of your website. Note... you don\'t have to use this Cancellation Button at all, if you don\'t want to. It\'s completely optional.</p>' . "\n";
								echo '<p><em><strong>Cancellation Process:</strong> Very simple. A Member clicks the Cancellation Button. PayPal asks them to log into their PayPal account. Once they\'re logged in, PayPal will display a list of all active Subscriptions they have with you. They choose which ones they want to cancel, and s2Member is notified silently behind-the-scenes, through the PayPal IPN service.</em></p>' . "\n";
								echo '<p><em><strong>Understanding Cancellations:</strong> It\'s important to realize that a Cancellation is not an EOT (End Of Term). All that happens during a Cancellation event, is that billing is stopped, and it\'s understood that the Customer is going to lose access, at some point in the future. This does NOT mean, that access will be revoked immediately. A separate EOT event will automatically handle a (demotion or deletion) later, at the appropriate time; which could be several days, or even a year after the Cancellation took place.</em></p>' . "\n";
								echo '<p><em><strong>Some Hairy Details:</strong> There might be times whenever you notice that a Member\'s Subscription has been cancelled through PayPal... but, s2Member continues allowing the User access to your site as a paid Member. Please don\'t be confused by this... in 99.9% of these cases, the reason for this is legitimate. s2Member will only remove the User\'s Membership privileges when an EOT (End Of Term) is processed, a refund occurs, a chargeback occurs, or when a cancellation occurs - which would later result in a delayed Auto-EOT by s2Member.</em></p>' . "\n";
								echo '<p><em>s2Member will not process an EOT (End Of Term) until the User has completely used up the time they paid for. In other words, if a User signs up for a monthly Subscription on Jan 1st, and then cancels their Subscription on Jan 15th; technically, they should still be allowed to access the site for another 15 days, and then on Feb 1st, the time they paid for has completely elapsed. At that time, s2Member will remove their Membership privileges; by either demoting them to a Free Subscriber, or deleting their account from the system (based on your configuration). s2Member also calculates one extra day (24 hours) into its equation, just to make sure access is not removed sooner than a Customer might expect.</em></p>' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_cancellation_buttons", get_defined_vars ());

								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";

								echo '<th class="ws-menu-page-th-side">' . "\n";
								echo '<label for="ws-plugin--s2member-cancellation-shortcode">' . "\n";
								echo 'Button Code<br />For Cancellations:<br /><br />' . "\n";
								echo '<div id="ws-plugin--s2member-cancellation-button-prev">' . "\n";
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-cancellation-button.php")));
								$ws_plugin__s2member_temp_s = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $ws_plugin__s2member_temp_s);
								echo preg_replace ("/\<a/", '<a target="_blank"', $ws_plugin__s2member_temp_s);
								echo '</div>' . "\n";
								echo '</label>' . "\n";
								echo '</th>' . "\n";

								echo '<td>' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";
								echo '<p>No configuration necessary.</p>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '<tr>' . "\n";

								echo '<td colspan="2">' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_cancellation_buttons_before_shortcode", get_defined_vars ());
								echo '<strong>WordPress Shortcode:</strong> (recommended for both the WordPress Visual &amp; HTML Editors)<br />' . "\n";
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/paypal-cancellation-button-shortcode.php")));
								echo '<input type="text" autocomplete="off" id="ws-plugin--s2member-cancellation-shortcode" value="' . format_to_edit ($ws_plugin__s2member_temp_s) . '" onclick="this.select ();" />' . "\n";

								echo '<div' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '><br />' . "\n";
								echo '<strong>Resulting PayPal Button Code:</strong> (ultimately, your Shortcode will produce this snippet)<br />' . "\n";
								echo '<textarea id="ws-plugin--s2member-cancellation-button" rows="8" wrap="off" onclick="this.select ();">';
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-cancellation-button.php")));
								$ws_plugin__s2member_temp_s = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $ws_plugin__s2member_temp_s);
								echo format_to_edit ($ws_plugin__s2member_temp_s);
								echo '</textarea><br />' . "\n";
								echo '&uarr; <em>This <span class="ws-menu-page-hilite">may contain PHP code too</span>; so be careful if you use this.</em>' . "\n";
								echo '</div>' . "\n";

								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_cancellation_buttons", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_reg_links", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_reg_links", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Member Registration Access Links">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-reg-links-section">' . "\n";
								echo '<h3>Registration Access Link Generator (for Customer Service)</h3>' . "\n";
								echo '<p>s2Member automatically generates Registration Access Links for your Customers after checkout, and also sends them a link in a Confirmation Email. However, if you ever need to deal with a Customer Service issue that requires a new Registration Access Link to be created manually, you can use this tool for that. Alternatively, you can create their account yourself/manually by going to <strong>Users → Add New</strong>. Either of these methods will work fine.</p>' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_reg_links", get_defined_vars ());

								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";

								echo '<td>' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";
								echo '<p>Paid Membership Level#: <select id="ws-plugin--s2member-reg-link-level" style="min-width:200px;">' . "\n";
								for ($n = 1; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
									echo '<option value="' . $n . '">s2Member Level #' . $n . '</option>' . "\n";
								echo '</select></p>' . "\n";
								echo '<p>Paid Subscr. ID: <input type="text" autocomplete="off" id="ws-plugin--s2member-reg-link-subscr-id" value="" size="50" /> <a href="#" onclick="alert(\'The Customer\\\'s Paid Subscr. ID (aka: Recurring Profile ID, Transaction ID) must be unique. This value can be obtained from inside your PayPal account under the History tab. Each paying Customer MUST be associated with a unique Paid Subscr. ID. If the Customer is NOT associated with a Paid Subscr. ID, you will need to generate a unique value for this field on your own. But keep in mind, s2Member will be unable to maintain future communication with the PayPal IPN (i.e., Notification) service if this value does not reflect a real Paid Subscr. ID that exists in your PayPal History log.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";
								echo '<p>Custom String Value: <input type="text" autocomplete="off" id="ws-plugin--s2member-reg-link-custom" value="' . esc_attr ($_SERVER["HTTP_HOST"]) . '" size="30" /> <a href="#" onclick="alert(\'A Paid Subscription is always associated with a Custom String that is passed through the custom=\\\'\\\'' . c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"]), 3) . '\\\'\\\' attribute of your Shortcode. This Custom Value, MUST always start with your domain name. However, you can also pipe delimit additional values after your domain, if you need to.\\n\\nFor example:\n' . c_ws_plugin__s2member_utils_strings::esc_js_sq (esc_attr ($_SERVER["HTTP_HOST"]), 3) . '|cv1|cv2|cv3\'); return false;" tabindex="-1">[?]</a> <input type="button" value="Generate Access Link" onclick="ws_plugin__s2member_paypalRegLinkGenerate();" /> <img id="ws-plugin--s2member-reg-link-loading" src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]) . '/images/ajax-loader.gif" alt="" style="display:none;" /></p>' . "\n";
								echo '<p' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '>Custom Capabilities (comma-delimited) <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: s2Member → API Scripting → Custom Capabilities.\'); return false;" tabindex="-1">[?]</a> <input type="text" maxlength="125" autocomplete="off" id="ws-plugin--s2member-reg-link-ccaps" size="40" onkeyup="if(this.value.match(/[^a-z_0-9,]/)) this.value = jQuery.trim (jQuery.trim (this.value).replace (/[ \-]/g, \'_\').replace (/[^a-z_0-9,]/gi, \'\').toLowerCase ());" /></p>' . "\n";
								echo '<p>Fixed Term Length (for Buy Now transactions): <input type="text" autocomplete="off" id="ws-plugin--s2member-reg-link-fixed-term" value="" size="10" /> <a href="#" onclick="alert(\'If the Customer purchased Membership through a Buy Now transaction (i.e., there is no Initial/Trial Period and no recurring charges for ongoing access), you may configure a Fixed Term Length in this field. This way the Customer\\\'s Membership Access is automatically revoked by s2Member at the appropriate time. This will be a numeric value, followed by a space, then a single letter.\\n\\nHere are some examples:\\n\\n1 D (this means 1 Day)\\n1 W (this means 1 Week)\\n1 M (this means 1 Month)\\n1 Y (this means 1 Year)\\n1 L (this means 1 Lifetime)\'); return false;">[?]</a></p>' . "\n";
								echo '<p id="ws-plugin--s2member-reg-link" style="display:none;"></p>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_reg_links", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_sp_buttons", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_sp_buttons", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Specific Post/Page (Buy Now) Buttons">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-sp-buttons-section">' . "\n";
								echo '<h3>Button Code Generator For Specific Post/Page Buttons</h3>' . "\n";
								echo '<p>s2Member now supports an additional layer of functionality (very powerful), which allows you to sell access to specific Posts/Pages that you\'ve created in WordPress. Specific Post/Page Access works independently from Member Level Access. That is, you can sell an unlimited number of Posts/Pages using "Buy Now" Buttons, and your Customers will NOT be required to have a Membership Account with your site in order to receive access. If they are already a Member, that\'s fine, but they won\'t need to be.</p>' . "\n";
								echo '<p>In other words, Customers will NOT need to login, just to receive access to the Specific Post/Page they purchased access to. s2Member will immediately redirect the Customer to the Specific Post/Page after checkout is completed successfully. An email is also sent to the Customer with a link (see: <strong>s2Member → PayPal Options → Specific Post/Page Email</strong>). Authentication is handled automatically through self-expiring links, good for 72 hours by default.</p>' . "\n";
								echo '<p>Specific Post/Page Access, is sort of like selling a product. Only, instead of shipping anything to the Customer, you just give them access to a specific Post/Page on your site; one that you created in WordPress. A Specific Post/Page that is protected by s2Member, might contain a download link for your eBook, access to file &amp; music downloads, access to additional support services, and the list goes on and on. The possibilities with this are endless; as long as your digital product can be delivered through access to a WordPress Post/Page that you\'ve created. To protect Specific Posts/Pages, please see: <strong>s2Member → Restriction Options → Specific Post/Page Access</strong>. Once you\'ve configured your Specific Post/Page Restrictions, those Posts/Pages will be available in the menus below.</p>' . "\n";
								echo '<p>Very simple. All you do is customize the form fields provided, for each Post/Page that you plan to sell. Then press (Generate Button Code). These special PayPal Buttons are customized to work with s2Member seamlessly. You can even Package Additional Posts/Pages together into one transaction.</p>' . "\n";
								echo '<p><em><strong>Please note:</strong> buttons are NOT saved here. This is only a Button Generator. Once you\'ve generated your Button, copy/paste it into your WordPress Editor. If you lose your Button Code, you\'ll need to come back &amp; re-generate a new one. If you\'re in Sandbox Test-Mode, and you\'re NOT using the Shortcode Format, please remember to come back and re-generate your Buttons before you go live.</em></p>' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_sp_buttons", get_defined_vars ());

								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";

								echo '<th class="ws-menu-page-th-side">' . "\n";
								echo '<label for="ws-plugin--s2member-sp-shortcode">' . "\n";
								echo 'Button Code<br />Specific Posts/Pages:<br /><br />' . "\n";
								echo '<div id="ws-plugin--s2member-sp-button-prev"></div>' . "\n";
								echo '</label>' . "\n";
								echo '</th>' . "\n";

								echo '<td>' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";

								echo '<p><select id="ws-plugin--s2member-sp-leading-id">' . "\n";
								echo '<option value="">&mdash; Select a Leading Post/Page that you\'ve protected &mdash;</option>' . "\n";

								$ws_plugin__s2member_temp_a_singulars = c_ws_plugin__s2member_utils_gets::get_all_singulars_with_sp ("exclude-conflicts");

								foreach ($ws_plugin__s2member_temp_a_singulars as $ws_plugin__s2member_temp_o)
									echo '<option value="' . esc_attr ($ws_plugin__s2member_temp_o->ID) . '">' . esc_html ($ws_plugin__s2member_temp_o->post_title) . '</option>' . "\n";

								echo '</select> <a href="#" onclick="alert(\'Required. The Leading Post/Page, is what your Customers will land on after checkout.\n\n*Tip* If there are no Posts/Pages in the menu, it\\\'s because you\\\'ve not configured s2Member for Specific Post/Page Access yet. See: s2Member → Restriction Options → Specific Post/Page Access.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";

								echo '<p><select id="ws-plugin--s2member-sp-additional-ids" multiple="multiple" style="height:100px;">' . "\n";
								echo '<optgroup label="&mdash; Package Additional Posts/Pages that you\'ve protected &mdash;">' . "\n";

								foreach ($ws_plugin__s2member_temp_a_singulars as $ws_plugin__s2member_temp_o)
									echo '<option value="' . esc_attr ($ws_plugin__s2member_temp_o->ID) . '">' . esc_html ($ws_plugin__s2member_temp_o->post_title) . '</option>' . "\n";

								echo '</optgroup></select> <a href="#" onclick="alert(\'Hold down your `Ctrl` key to select multiples.\\n\\nOptional. If you include Additional Posts/Pages, Customers will still land on your Leading Post/Page; BUT, they\\\'ll ALSO have access to some Additional Posts/Pages that you\\\'ve protected. This gives you the ability to create Post/Page Packages.\\n\\nIn other words, a Customer is sold a Specific Post/Page (they\\\'ll land on your Leading Post/Page after checkout), which might contain links to some other Posts/Pages that you\\\'ve packaged together under one transaction.\\n\\nBundling Additional Posts/Pages into one Package, authenticates the Customer for access to the Additional Posts/Pages automatically (i.e., only one Access Link is needed, and s2Member generates this automatically). However, you will STILL need to design your Leading Post/Page (which is what a Customer will actually land on), with links pointing to the other Posts/Pages. This way your Customers will have clickable links to everything they\\\'ve paid for.\\n\\n*Quick Summary* s2Member sends Customers to your Leading Post/Page, and also authenticates them for access to any Additional Posts/Pages automatically. You handle it from there.\\n\\n*Tip* If there are no Posts/Pages in this menu, it\\\'s because you\\\'ve not configured s2Member for Specific Post/Page Access yet. See: s2Member → Restriction Options → Specific Post/Page Access.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";

								echo '<p>I want to charge: $<input type="text" autocomplete="off" id="ws-plugin--s2member-sp-amount" value="0.01" size="4" /> / <select id="ws-plugin--s2member-sp-hours">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-sp-hours.php"))) . '</select></p>' . "\n";
								echo '<p>Description: <input type="text" autocomplete="off" id="ws-plugin--s2member-sp-desc" value="Description and pricing details here." size="68" /></p>' . "\n";
								echo '<p>Checkout Page Style <a href="#" onclick="alert(\'Optional. This can be configured inside your PayPal account. PayPal allows you to create Custom Page Styles, and assign a unique name to them. You can add your own header image and color selection to the checkout form. Once you\\\'ve created a Custom Page Style at PayPal, you can enter that Page Style here.\\n\\nIn addition. The Shortcode below, provided by s2Member; supports an image attribute: image=\\\'\\\'default\\\'\\\'. This can be changed to a full URL, pointing to a custom image of your own; instead of the default PayPal Button image.\'); return false;" tabindex="-1">[?]</a>: <input type="text" autocomplete="off" id="ws-plugin--s2member-sp-page-style" value="paypal" size="18" /> <select id="ws-plugin--s2member-sp-currency">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-currencies.php"))) . '</select> <input type="button" value="Generate Button Code" onclick="ws_plugin__s2member_paypalSpButtonGenerate();" /></p>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '<tr>' . "\n";

								echo '<td colspan="2">' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_sp_buttons_before_shortcode", get_defined_vars ());
								echo '<strong>WordPress Shortcode:</strong> (recommended for both the WordPress Visual &amp; HTML Editors)<br />' . "\n";
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/shortcodes/paypal-sp-checkout-button-shortcode.php")));
								$ws_plugin__s2member_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__s2member_temp_s);
								echo '<input type="text" autocomplete="off" id="ws-plugin--s2member-sp-shortcode" value="' . format_to_edit ($ws_plugin__s2member_temp_s) . '" onclick="this.select ();" />' . "\n";

								echo '<div' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? ' style="display:none;"' : '') . '><br />' . "\n";
								echo '<strong>Resulting PayPal Button Code:</strong> (ultimately, your Shortcode will produce this snippet)<br />' . "\n";
								echo '<textarea id="ws-plugin--s2member-sp-button" rows="8" wrap="off" onclick="this.select ();">';
								$ws_plugin__s2member_temp_s = trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/buttons/paypal-sp-checkout-button.php")));
								$ws_plugin__s2member_temp_s = preg_replace ("/%%endpoint%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? "www.sandbox.paypal.com" : "www.paypal.com")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_business%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%paypal_merchant_id%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%cancel_return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%notify_url%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_notify=1"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%return%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ("/?s2member_paypal_return=1"))), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%custom%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($_SERVER["HTTP_HOST"])), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%images%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"] . "/images")), $ws_plugin__s2member_temp_s);
								$ws_plugin__s2member_temp_s = preg_replace ("/%%wpurl%%/", c_ws_plugin__s2member_utils_strings::esc_refs (esc_attr (home_url ())), $ws_plugin__s2member_temp_s);
								echo format_to_edit ($ws_plugin__s2member_temp_s);
								echo '</textarea><br />' . "\n";
								echo '&uarr; <em>This <span class="ws-menu-page-hilite">may contain PHP code too</span>; so be careful if you use this.</em>' . "\n";
								echo '</div>' . "\n";

								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_sp_buttons", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_sp_links", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_sp_links", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Specific Post/Page Access Links">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-sp-links-section">' . "\n";
								echo '<h3>Specific Post/Page Access Link Generator (for Customer Service)</h3>' . "\n";
								echo '<p>s2Member automatically generates Specific Post/Page Access Links for your Customers after checkout, and also sends them a link in a Confirmation Email. However, if you ever need to deal with a Customer Service issue that requires a new Specific Post/Page Access Link to be created manually, you can use this tool for that.</p>' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_sp_links", get_defined_vars ());

								echo '<table class="form-table">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr>' . "\n";

								echo '<td>' . "\n";
								echo '<form onsubmit="return false;" autocomplete="off">' . "\n";

								echo '<p><select id="ws-plugin--s2member-sp-link-leading-id">' . "\n";
								echo '<option value="">&mdash; Select a Leading Post/Page that you\'ve protected &mdash;</option>' . "\n";

								$ws_plugin__s2member_temp_a_singulars = c_ws_plugin__s2member_utils_gets::get_all_singulars_with_sp ("exclude-conflicts");

								foreach ($ws_plugin__s2member_temp_a_singulars as $ws_plugin__s2member_temp_o)
									echo '<option value="' . esc_attr ($ws_plugin__s2member_temp_o->ID) . '">' . esc_html ($ws_plugin__s2member_temp_o->post_title) . '</option>' . "\n";

								echo '</select> <a href="#" onclick="alert(\'Required. The Leading Post/Page, is what your Customers will land on after checkout.\n\n*Tip* If there are no Posts/Pages in the menu, it\\\'s because you\\\'ve not configured s2Member for Specific Post/Page Access yet. See: s2Member → Restriction Options → Specific Post/Page Access.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";

								echo '<p><select id="ws-plugin--s2member-sp-link-additional-ids" multiple="multiple" style="height:100px; min-width:450px;">' . "\n";
								echo '<optgroup label="&mdash; Package Additional Posts/Pages that you\'ve protected &mdash;">' . "\n";

								foreach ($ws_plugin__s2member_temp_a_singulars as $ws_plugin__s2member_temp_o)
									echo '<option value="' . esc_attr ($ws_plugin__s2member_temp_o->ID) . '">' . esc_html ($ws_plugin__s2member_temp_o->post_title) . '</option>' . "\n";

								echo '</optgroup></select> <a href="#" onclick="alert(\'Hold down your `Ctrl` key to select multiples.\\n\\nOptional. If you include Additional Posts/Pages, Customers will still land on your Leading Post/Page; BUT, they\\\'ll ALSO have access to some Additional Posts/Pages that you\\\'ve protected. This gives you the ability to create Post/Page Packages.\\n\\nIn other words, a Customer is sold a Specific Post/Page (they\\\'ll land on your Leading Post/Page after checkout), which might contain links to some other Posts/Pages that you\\\'ve packaged together under one transaction.\\n\\nBundling Additional Posts/Pages into one Package, authenticates the Customer for access to the Additional Posts/Pages automatically (i.e., only one Access Link is needed, and s2Member generates this automatically). However, you will STILL need to design your Leading Post/Page (which is what a Customer will actually land on), with links pointing to the other Posts/Pages. This way your Customers will have clickable links to everything they\\\'ve paid for.\\n\\n*Quick Summary* s2Member sends Customers to your Leading Post/Page, and also authenticates them for access to any Additional Posts/Pages automatically. You handle it from there.\\n\\n*Tip* If there are no Posts/Pages in this menu, it\\\'s because you\\\'ve not configured s2Member for Specific Post/Page Access yet. See: s2Member → Restriction Options → Specific Post/Page Access.\'); return false;" tabindex="-1">[?]</a></p>' . "\n";

								echo '<p><select id="ws-plugin--s2member-sp-link-hours">' . trim (c_ws_plugin__s2member_utilities::evl (file_get_contents (dirname (dirname (__FILE__)) . "/templates/options/paypal-sp-hours.php"))) . '</select> <input type="button" value="Generate Access Link" onclick="ws_plugin__s2member_paypalSpLinkGenerate();" /> <img id="ws-plugin--s2member-sp-link-loading" src="' . esc_attr ($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]) . '/images/ajax-loader.gif" alt="" style="display:none;" /></p>' . "\n";
								echo '<p id="ws-plugin--s2member-sp-link" style="display:none;"></p>' . "\n";
								echo '</form>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_sp_links", get_defined_vars ());
							}

						if (apply_filters("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_display_shortcode_attrs", true, get_defined_vars ()))
							{
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_before_shortcode_attrs", get_defined_vars ());

								echo '<div class="ws-menu-page-group" title="Shortcode Attributes (Explained)">' . "\n";

								echo '<div class="ws-menu-page-section ws-plugin--s2member-shortcode-attrs-section">' . "\n";
								echo '<h3>Shortcode Attributes (Explained In Full Detail)</h3>' . "\n";
								echo '<p>When you generate a Button Code, s2Member will make a <a href="http://s2member.com/r/shortcode-reference/" target="_blank" rel="external">Shortcode</a> available to you. Like most Shortcodes for WordPress, s2Member reads Attributes in your Shortcode. These Attributes will be pre-configured by one of s2Member\'s Button Generators automatically; so there really is nothing more you need to do. However, many site owners like to know exactly how these Shortcode Attributes work. Below, is a brief overview of each possible Shortcode Attribute.</p>' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_shortcode_attrs", get_defined_vars ());

								echo '<table class="form-table" style="margin-top:0;">' . "\n";
								echo '<tbody>' . "\n";
								echo '<tr style="padding-top:0;">' . "\n";

								echo '<td style="padding-top:0;">' . "\n";
								echo '<ul class="ws-menu-page-li-margins">' . "\n";
								echo '<li><code>cancel="0"</code> Cancellation Button. Only valid w/ Membership Level Access. Possible values: <code>0</code> = this is NOT a Cancellation Button, <code>1</code> = this IS a Cancellation Button.</li>' . "\n";
								echo '<li><code>cc="USD"</code> 3 character Currency Code. Not valid when <code>cancel="1"</code>.</li>' . "\n";
								echo (!is_multisite () || !c_ws_plugin__s2member_utils_conds::is_multisite_farm () || is_main_site ()) ? '<li><code>ccaps="music,videos"</code> A comma-delimited list of Custom Capabilities. Only valid w/ Membership Level Access and/or Independent Custom Capabilities.</li>' . "\n" : '';
								echo '<li><code>custom="' . esc_html ($_SERVER["HTTP_HOST"]) . '"</code> must start with your domain. Additional values can be piped in (ex: <code>custom="' . esc_html ($_SERVER["HTTP_HOST"]) . '|cv1|cv2|cv3|etc"</code>). Not valid when <code>cancel="1"</code>.</li>' . "\n";
								echo '<li><code>desc="Gold Membership"</code> A brief purchase Description. Not valid when <code>cancel="1"</code>.</li>' . "\n";
								echo '<li><code>dg="0"</code> The Digital Goods directive. s2Member will eventually be integrated with <a href="http://s2member.com/r/paypal-express-checkout-digitals/" target="_blank" rel="external">Digital Goods</a> for inline Express Checkout. But for now, this should always be <code>0</code>.</li>' . "\n";
								echo '<li><code>exp="72"</code> Access Expires (in hours). Only valid when <code>sp="1"</code> for Specific Post/Page Access.</li>' . "\n";
								echo '<li><code>ids="14"</code> A Post/Page ID#, or a comma-delimited list of IDs. Only valid when <code>sp="1"</code> for Specific Post/Page Access.</li>' . "\n";
								echo '<li><code>image="default"</code> Button Image Location. Possible values: <code>default</code> = use the default PayPal Button, <code>http://...</code> = location of your custom Image.</li>' . "\n";
								echo '<li><code>lang=""</code> Optional 5 character Button Language Code <em>(ake: Locale Code—ex: <code>en_US</code>)</em>. This controls the interface language of the PayPal Button itself. If unspecified, the language defaults to English (i.e., <code>en_US</code>; or to the value set by an optional MO translation file; which translates s2Member overall). See <a href="http://s2member.com/r/paypal-locale-codes/" target="_blank" rel="external">this list of possible Locale Codes</a>.</li>' . "\n";
								echo '<li><code>lc=""</code> Optional 2 character Country/Locale Code <em>(i.e., Country Code—ex: <code>US</code>)</em>. This controls the interface language used at PayPal during checkout. If unspecified, the language is determined by PayPal when possible, defaulting to <code>US</code> <em>english</em> when not possible. See <a href="http://s2member.com/r/paypal-locale-codes/" target="_blank" rel="external">this list of possible Country Codes</a>. Not valid when <code>cancel="1"</code>.</li>' . "\n";
								echo '<li><code>level="1"</code> Membership Level [1-4] <em>(or, up to the number of configured Levels)</em>. Only valid for Buttons providing paid Membership Level Access.' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' Or, with Independent Custom Capabilities this MUST be set to <code>level="*"</code>, and <code>ccaps=""</code> must NOT be empty <em>(i.e., <code>level="*" ccaps="music,videos"</code>)</em>.') . '</li>' . "\n";
								echo '<li><code>modify="0"</code> Modification directive. Only valid w/ Membership Level Access. Possible values: <code>0</code> = allows Customers to only create a new Subscription, <code>1</code> = allows Customers to modify their current Subscription or sign up for a new one, <code>2</code> = allows Customers to only modify their current Subscription.</li>' . "\n";
								echo '<li><code>ns="0"</code> The <em>no_shipping</em> directive. Possible values: <code>0</code> = prompt for an address, but do not require one, <code>1</code> = do not prompt for a shipping address, <code>2</code> = prompt for an address, and require one. Not valid when <code>cancel="1"</code>.</li>' . "\n";
								echo '<li><code>output="button"</code> Output Type. Possible values: <code>button</code> = PayPal Button w/hidden inputs, <code>anchor</code> = PayPal Button (  &lt;a&gt; anchor tag ) URL w/ ?query string, <code>url</code> = raw URL w/ ?query string.</li>' . "\n";
								echo '<li><code>ps="paypal"</code> PayPal checkout Page Style. Not valid when <code>cancel="1"</code>.</li>' . "\n";
								echo '<li><code>ra="0.01"</code> Regular, Buy Now, and/or Recurring Amount. Must be &gt;= <code>0.01</code>. Not valid when <code>cancel="1"</code>.</li>' . "\n";
								echo '<li><code>rp="1"</code> Regular Period. Only valid w/ Membership Level Access' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' and/or Independent Custom Capabilities') . '. Must be &gt;= <code>1</code> (ex: <code>1</code> Week, <code>2</code> Months, <code>1</code> Month, <code>3</code> Days).</li>' . "\n";
								echo '<li><code>rt="M"</code> Regular Term. Only valid w/ Membership Level Access' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' and/or Independent Custom Capabilities') . '. Possible values: <code>D</code> = Days, <code>W</code> = Weeks, <code>M</code> = Months, <code>Y</code> = Years, <code>L</code> = Lifetime.</li>' . "\n";
								echo '<li><code>rr="1"</code> Recurring directive. Only valid w/ Membership Level Access' . ((is_multisite () && c_ws_plugin__s2member_utils_conds::is_multisite_farm () && !is_main_site ()) ? '' : ' and/or Independent Custom Capabilities') . '. Possible values: <code>0</code> = non-recurring "Subscription" with possible Trial Period for free, or at a different Trial Amount; <code>1</code> = recurring "Subscription" with possible Trial Period for free, or at a different Trial Amount; <code>BN</code> = non-recurring "Buy Now" functionality, no Trial Period possible.</li>' . "\n";
								echo '<li><code>rrt=""</code> Recurring Times <em>(i.e., a fixed number of installments)</em>. Only valid w/ Membership Level Access. When unspecified, any recurring charges will remain ongoing until cancelled, or until payments start failing. If this is set to <code>1 or higher</code> the regular recurring charges will only continue for X billing cycles, depending on what you specify. This is only valid when <code>rr="1"</code> for recurring "Subscriptions". Please note that a fixed number of installments, also means a fixed period of access. If a Customer\'s billing is monthly, and you set <code>rrt="3"</code>, billing will continue for only 3 monthly installments. After that, billing would stop, and their access to the site would be revoked as well <em>(based on your EOT Behavior setting under: s2Member → PayPal Options)</em>.</li>' . "\n";
								echo '<li><code>rra="1"</code> Reattempt failed payments? Possible values: <code>0</code> = do NOT reattempt billing when/if a recurring payment fails; <code>1</code> = yes, DO reattempt billing when/if a recurring payment fails. With PayPal Standard integration, PayPal will retry a maximum of 2 times when you set <code>rra="1"</code>; after that, a Subscription would be terminated due to Max Failed Payments having been reached. PayPal Standard integration does NOT make it possible to configure Max Failed Payments, it simply defaults to a value of <code>2</code> whenever <code>rra="1"</code>, indicating that you DO want to retry failed payments.</li>' . "\n";
								echo '<li><code>sp="0"</code> Specific Post/Page Button. Possible values: <code>0</code> = this is NOT a Specific Post/Page Access Button, <code>1</code> = this IS a Specific Post/Page Access Button.</li>' . "\n";
								echo '<li><code>ta="0.00"</code> Trial Amount. Only valid w/ Membership Level Access. Must be <code>0</code> when <code>rt="L"</code> or when <code>rr="BN"</code>.</li>' . "\n";
								echo '<li><code>tp="0"</code> Trial Period. Only valid w/ Membership Level Access. Must be <code>0</code> when <code>rt="L"</code> or when <code>rr="BN"</code>.</li>' . "\n";
								echo '<li><code>tt="D"</code> Trial Term. Only valid w/ Membership Level Access. Possible values: <code>D</code> = Days, <code>W</code> = Weeks, <code>M</code> = Months, <code>Y</code> = Years.</li>' . "\n";
								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_during_shortcode_attrs_lis", get_defined_vars ());
								echo '</ul>' . "\n";
								echo '</td>' . "\n";

								echo '</tr>' . "\n";
								echo '</tbody>' . "\n";
								echo '</table>' . "\n";
								echo '</div>' . "\n";

								echo '</div>' . "\n";

								do_action("ws_plugin__s2member_during_paypal_buttons_page_during_left_sections_after_shortcode_attrs", get_defined_vars ());
							}

						do_action("ws_plugin__s2member_during_paypal_buttons_page_after_left_sections", get_defined_vars ());

						echo '</td>' . "\n";

						echo '<td class="ws-menu-page-table-r">' . "\n";
						c_ws_plugin__s2member_menu_pages_rs::display ();
						echo '</td>' . "\n";

						echo '</tr>' . "\n";
						echo '</tbody>' . "\n";
						echo '</table>' . "\n";

						echo '</div>' . "\n";
					}
			}
	}

new c_ws_plugin__s2member_menu_page_paypal_buttons ();
