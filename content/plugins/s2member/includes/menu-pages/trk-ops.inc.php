<?php
/**
 * Menu page for the s2Member plugin (API Tracking page).
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
	exit("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_menu_page_trk_ops"))
{
	/**
	 * Menu page for the s2Member plugin (API Tracking page).
	 *
	 * @package s2Member\Menu_Pages
	 * @since 110531
	 */
	class c_ws_plugin__s2member_menu_page_trk_ops
	{
		public function __construct()
		{
			echo '<div class="wrap ws-menu-page">'."\n";

			echo '<div class="ws-menu-page-toolbox">'."\n";
			c_ws_plugin__s2member_menu_pages_tb::display();
			echo '</div>'."\n";

			echo '<h2>API / Tracking</h2>'."\n";

			echo '<table class="ws-menu-page-table">'."\n";
			echo '<tbody class="ws-menu-page-table-tbody">'."\n";
			echo '<tr class="ws-menu-page-table-tr">'."\n";
			echo '<td class="ws-menu-page-table-l">'."\n";

			echo '<form method="post" name="ws_plugin__s2member_options_form" id="ws-plugin--s2member-options-form" autocomplete="off">'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_options_save" id="ws-plugin--s2member-options-save" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-options-save")).'" />'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_configured" id="ws-plugin--s2member-configured" value="1" />'."\n";

			do_action("ws_plugin__s2member_during_trk_ops_page_before_left_sections", get_defined_vars());

			if(apply_filters("ws_plugin__s2member_during_trk_ops_page_during_left_sections_display_signup_tracking", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_before_signup_tracking", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Signup Tracking Codes">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-signup-tracking-section">'."\n";
				echo '<h3>Signup Tracking Codes (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, a list server, tracking codes from advertising networks, or the like; you\'ll want to read this section. The HTML'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' and/or PHP').' code that you enter below, will be loaded up in a web browser, after a "new", "paying" Member completes Signup through your Payment Gateway. This is marked `Signup`, because Signup Tracking Codes will be displayed each time a "new", "paying" Member signs up. Depending on your fee structure, this may include a payment that establishes their Subscription, or it may not.</p>'."\n";
				echo '<p>Signup Tracking Codes will only be displayed once for each Member. Signup Tracking Codes are displayed right after a "new", "paying" Member signs up successfully through your Payment Gateway, regardless of whether any money has actually been transacted. In other words, Signup Tracking Codes are displayed anytime a "new", "paying" Member signs up; even if you provided them with something 100% free <em>(i.e., even if no money is being transacted)</em>.</p>'."\n";
				echo '<p>s2Member will display your Signup Tracking Codes in one of four possible locations... <strong>1.</strong> If possible, on the Thank-You Return Page, after returning from your Payment Gateway. <strong>2.</strong> Otherwise, if possible, on the Registration Form; after returning from your Payment Gateway. <em>Note. If you offer a 100% free Trial Period, Tracking Codes will be displayed in location #2 when using PayPal Standard Button integration.</em> <strong>3.</strong> Otherwise, if possible, on the Login Form after Registration is completed. <strong>4.</strong> Otherwise, in the footer of your WordPress theme, as soon as possible <em>(immediately with s2Member Pro-Form integration)</em>; or after the Customer\'s very first login.</p>'."\n";
				echo '<p>Signup Tracking Codes are displayed for all types of Membership Level Access. Including Recurring Subscriptions <em>(with or without a Free Trial Period)</em>, Non-Recurring Subscriptions <em>(with or without a Free Trial Period)</em>, Lifetime Subscriptions, and even Fixed-Term Subscriptions. All of these are supported by s2Member\'s Button/Form Generators.</p>'."\n";
				echo '<p>Signup Tracking Codes will NOT be processed for Free Subscribers that register without going through your Payment Gateway at all (i.e., they simply register on-site; and there is no checkout whatsoever). Signup Tracking Codes will NOT be processed when an "existing" User/Member pays for a new Subscription <em>(see: Modification Tracking Codes for that scenario)</em>.'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' And, Signup Tracking Codes will NOT be processed on Buy Now transactions for Independent Custom Capabilities <em>(see: Capability Tracking Codes for that scenario)</em>.').'</p>'."\n";
				echo '<p><em><strong>AD BLOCKERS:</strong> If a web browser has ad blockers enabled (i.e., the web browser has an ad blocking extension or add-on), Tracking Codes from popular online advertising companies (including many affiliate networks) may NOT be shown. Ad blockers can prevent your Tracking Codes from being loaded in a customer\'s browser. If you\'d like to avoid this problem, consider integrating with s2Member\'s API Notifications instead of with Tracking Codes. API Notifications occur silently behind-the-scenes (more reliably), whereas Tracking Codes are loaded in a customer\'s browser. For more information, please see: <strong>s2Member → API / Notifications</strong>.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_during_signup_tracking", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-signup-tracking-codes">'."\n";
				echo 'Integrate Signup Tracking Codes:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_signup_tracking_codes" id="ws-plugin--s2member-signup-tracking-codes" rows="8" wrap="off" spellcheck="false">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["signup_tracking_codes"]).'</textarea><br />'."\n";
				echo 'Any valid XHTML / JavaScript'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' (or even PHP)').' code will work just fine here. Just try not to put anything here that would actually be visible to the Customer. Things like 1x1 pixel images that load up silently and/or JavaScript tracking routines will be fine. Google Analytics code works just fine, AdSense performance tracking, as well as Yahoo tracking and other affiliate network codes are all OK here.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%subscr_id%%</code> = The Paid Subscription ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the Transaction ID for the purchase. Payment Gateways do not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there is only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe, which remains constant throughout any &amp; all future payments. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%subscr_cid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Subscription and/or Transaction ID. See %%subscr_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%initial%%</code> = The Initial Fee charged during signup. If you offered a 100% Free Trial Period, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This will always represent the amount of money the Customer spent, whenever they initially signed up, no matter what. If a Customer signs up, under the terms of a 100% Free Trial Period, this will be 0.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular%%</code> = The Regular Amount of the Subscription. If you offer something 100% free, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This is how much the Subscription costs after an Initial Period expires. If you did NOT offer an Initial Period at a different price, %%initial%% and %%regular%% will be equal to the same thing.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%recurring%%</code> = This is the amount that will be charged on a recurring basis, or <code>0</code> if non-recurring. [ <a href="#" onclick="alert(\'If Recurring Payments have not been required, this will be equal to 0. That being said, %%regular%% &amp; %%recurring%% are usually the same value. This variable can be used in two different ways. You can use it to determine what the Regular Recurring Rate is, or to determine whether the Subscription will recur or not. If it is going to recur, %%recurring%% will be > 0.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s IP Address, detected during checkout via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <code><em>level:custom_capabilities:fixed term</em></code>) that the Subscription is for.</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
				echo '<li><code>%%initial_term%%</code> = This is the term length of the Initial Period. This will be a numeric value, followed by a space, then a single letter. [ <a href="#" onclick="alert(\'Here are some examples:\\n\\n%%initial_term%% = 1 D (this means 1 Day)\\n%%initial_term%% = 1 W (this means 1 Week)\\n%%initial_term%% = 1 M (this means 1 Month)\\n%%initial_term%% = 1 Y (this means 1 Year)\\n\\nThe Initial Period never recurs, so this only lasts for the term length specified, then it is over.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular_term%%</code> = This is the term length of the Regular Period. This will be a numeric value, followed by a space, then a single letter. [ <a href="#" onclick="alert(\'Here are some examples:\\n\\n%%regular_term%% = 1 D (this means 1 Day)\\n%%regular_term%% = 1 W (this means 1 Week)\\n%%regular_term%% = 1 M (this means 1 Month)\\n%%regular_term%% = 1 Y (this means 1 Year)\\n%%regular_term%% = 1 L (this means 1 Lifetime)\\n\\nThe Regular Term is usually recurring. So the Regular Term value represents the period (or duration) of each recurring period. If %%recurring%% = 0, then the Regular Term only applies once, because it is not recurring. So if it is not recurring, the value of %%regular_term%% simply represents how long their Membership privileges are going to last after the %%initial_term%% has expired, if there was an Initial Term. The value of this variable ( %%regular_term%% ) will never be empty, it will always be at least: 1 D, meaning 1 day. No exceptions.\'); return false;">?</a> ]</li>'."\n";
				echo '</ul>'."\n";

				if(c_ws_plugin__s2member_utils_conds::pro_is_installed())
				{
					echo '<strong>Coupon Replacement Codes (applicable only w/ s2Member Pro-Forms):</strong>'."\n";
					echo '<ul class="ws-menu-page-li-margins">'."\n";
					echo '<li><code>%%full_coupon_code%%</code> = A full Coupon Code—if one is accepted by your configuration of s2Member. This may indicate an Affiliate Coupon Code, which will include your Affiliate Suffix Chars too (i.e., the full Coupon Code).</li>'."\n";
					echo '<li><code>%%coupon_code%%</code> = A Coupon Code—if one is accepted by your configuration of s2Member. This will NOT include any Affiliate Suffix Chars. This indicates the actual Coupon Code accepted by your configuration of s2Member (excluding any Affiliate ID).</li>'."\n";
					echo '<li><code>%%coupon_affiliate_id%%</code> = This is the end of an Affiliate Coupon Code <em>(i.e., the referring affiliate\'s ID)</em>. This is only applicable if an Affiliate Coupon Code is accepted by your configuration of s2Member.</li>'."\n";
					echo '</ul>'."\n";
				}

				echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
				echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
				echo '</ul>'."\n";
				echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
				echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
				echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><em><strong>Tip:</strong> With <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Pro Add-on / Prices")).'" target="_blank" rel="external">s2Member Pro-Forms</a>, it\'s possible to integrate Affiliate Coupon Codes. Each of your affiliates can add their affiliate ID onto the end of any valid Coupon Code that you\'ve configured with s2Member Pro. Please check your Dashboard here: <strong>s2Member → Pro Coupon Codes → Affiliate Coupon Codes</strong>. This is a VERY powerful feature.</em></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_after_signup_tracking", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_trk_ops_page_during_left_sections_display_modification_tracking", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_before_modification_tracking", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Modification Tracking Codes">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-modification-tracking-section">'."\n";
				echo '<h3>Modification Tracking Codes (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, a list server, tracking codes from advertising networks, or the like; you\'ll want to read this section. The HTML'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' and/or PHP').' code that you enter below, will be loaded up in a web browser, each time a Subscription Modification occurs. This is marked `Modification`, because Modification Tracking Codes are displayed each time an "existing" User/Member <em>(even if they are/were a Free Subscriber)</em> signs up for a paid Subscription <em>(i.e., a Modification takes place against an existing account within WordPress)</em>, or an "existing" Member modifies their paid Subscription terms <em>(again, a Modification takes places against an existing account within WordPress)</em>. Depending on your fee structure, this may include a payment that establishes their Subscription, or it may not.</p>'."\n";
				echo '<p>Modification Tracking Codes are displayed right after a Member signs up and/or modifies billing terms successfully through your Payment Gateway, regardless of whether any money has actually been transacted. In other words, Modification Tracking Codes are displayed even if you provided them with something for free <em>(i.e., even if no money is being transacted)</em>.</p>'."\n";
				echo '<p>s2Member will display your Modification Tracking Codes in one of three possible locations... <strong>1.</strong> If possible, on the Thank-You Return Page, after returning from your Payment Gateway. <strong>2.</strong> Otherwise, if possible, on the Login Form after returning from your Payment Gateway <em>(i.e., when the Customer is asked to log back in)</em>. <strong>3.</strong> Otherwise, in the footer of your WordPress theme, as soon as possible <em>(immediately with s2Member Pro-Form integration)</em>; or after the Customer\'s next login.</p>'."\n";
				echo '<p>Modification Tracking Codes are displayed for all types of Membership Level Access. Including Recurring Subscriptions <em>(with or without a Free Trial Period)</em>, Non-Recurring Subscriptions <em>(with or without a Free Trial Period)</em>, Lifetime Subscriptions, and even Fixed-Term Subscriptions. All of these are supported by s2Member\'s Button/Form Generators.</p>'."\n";
				echo '<p>Modification Tracking Codes will NOT be processed when a "new" User/Member signs up <em>(see: Signup Tracking Codes for that scenario)</em>.'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' And, Modification Tracking Codes will NOT be processed on Buy Now transactions for Independent Custom Capabilities <em>(see: Capability Tracking Codes for that scenario)</em>.').'</p>'."\n";
				echo '<p><em><strong>AD BLOCKERS:</strong> If a web browser has ad blockers enabled (i.e., the web browser has an ad blocking extension or add-on), Tracking Codes from popular online advertising companies (including many affiliate networks) may NOT be shown. Ad blockers can prevent your Tracking Codes from being loaded in a customer\'s browser. If you\'d like to avoid this problem, consider integrating with s2Member\'s API Notifications instead of with Tracking Codes. API Notifications occur silently behind-the-scenes (more reliably), whereas Tracking Codes are loaded in a customer\'s browser. For more information, please see: <strong>s2Member → API / Notifications</strong>.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_during_modification_tracking", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-modification-tracking-codes">'."\n";
				echo 'Integrate Modification Tracking Codes:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_modification_tracking_codes" id="ws-plugin--s2member-modification-tracking-codes" rows="8" wrap="off" spellcheck="false">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["modification_tracking_codes"]).'</textarea><br />'."\n";
				echo 'Any valid XHTML / JavaScript'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' (or even PHP)').' code will work just fine here. Just try not to put anything here that would actually be visible to the Customer. Things like 1x1 pixel images that load up silently and/or JavaScript tracking routines will be fine. Google Analytics code works just fine, AdSense performance tracking, as well as Yahoo tracking and other affiliate network codes are all OK here.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%subscr_id%%</code> = The Paid Subscription ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the Transaction ID for the purchase. Payment Gateways do not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there is only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe, which remains constant throughout any &amp; all future payments. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%subscr_cid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Subscription and/or Transaction ID. See %%subscr_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%initial%%</code> = The Initial Fee charged during signup. If you offered a 100% Free Trial Period, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This will always represent the amount of money the Customer spent, whenever they initially signed up, no matter what. If a Customer signs up, under the terms of a 100% Free Trial Period, this will be 0.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular%%</code> = The Regular Amount of the Subscription. If you offer something 100% free, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This is how much the Subscription costs after an Initial Period expires. If you did NOT offer an Initial Period at a different price, %%initial%% and %%regular%% will be equal to the same thing.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%recurring%%</code> = This is the amount that will be charged on a recurring basis, or <code>0</code> if non-recurring. [ <a href="#" onclick="alert(\'If Recurring Payments have not been required, this will be equal to 0. That being said, %%regular%% &amp; %%recurring%% are usually the same value. This variable can be used in two different ways. You can use it to determine what the Regular Recurring Rate is, or to determine whether the Subscription will recur or not. If it is going to recur, %%recurring%% will be > 0.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <code><em>level:custom_capabilities:fixed term</em></code>) that the Subscription is for.</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
				echo '<li><code>%%initial_term%%</code> = This is the term length of the Initial Period. This will be a numeric value, followed by a space, then a single letter. [ <a href="#" onclick="alert(\'Here are some examples:\\n\\n%%initial_term%% = 1 D (this means 1 Day)\\n%%initial_term%% = 1 W (this means 1 Week)\\n%%initial_term%% = 1 M (this means 1 Month)\\n%%initial_term%% = 1 Y (this means 1 Year)\\n\\nThe Initial Period never recurs, so this only lasts for the term length specified, then it is over.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular_term%%</code> = This is the term length of the Regular Period. This will be a numeric value, followed by a space, then a single letter. [ <a href="#" onclick="alert(\'Here are some examples:\\n\\n%%regular_term%% = 1 D (this means 1 Day)\\n%%regular_term%% = 1 W (this means 1 Week)\\n%%regular_term%% = 1 M (this means 1 Month)\\n%%regular_term%% = 1 Y (this means 1 Year)\\n%%regular_term%% = 1 L (this means 1 Lifetime)\\n\\nThe Regular Term is usually recurring. So the Regular Term value represents the period (or duration) of each recurring period. If %%recurring%% = 0, then the Regular Term only applies once, because it is not recurring. So if it is not recurring, the value of %%regular_term%% simply represents how long their Membership privileges are going to last after the %%initial_term%% has expired, if there was an Initial Term. The value of this variable ( %%regular_term%% ) will never be empty, it will always be at least: 1 D, meaning 1 day. No exceptions.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address associated with their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_login%%</code> = The Username associated with their account. The Customer created this during registration.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s original IP Address, during checkout/registration via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%user_id%%</code> = A unique WordPress User ID that references this account in the WordPress database.</li>'."\n";
				echo '</ul>'."\n";

				if(c_ws_plugin__s2member_utils_conds::pro_is_installed())
				{
					echo '<strong>Coupon Replacement Codes (applicable only w/ s2Member Pro-Forms):</strong>'."\n";
					echo '<ul class="ws-menu-page-li-margins">'."\n";
					echo '<li><code>%%full_coupon_code%%</code> = A full Coupon Code—if one is accepted by your configuration of s2Member. This may indicate an Affiliate Coupon Code, which will include your Affiliate Suffix Chars too (i.e., the full Coupon Code).</li>'."\n";
					echo '<li><code>%%coupon_code%%</code> = A Coupon Code—if one is accepted by your configuration of s2Member. This will NOT include any Affiliate Suffix Chars. This indicates the actual Coupon Code accepted by your configuration of s2Member (excluding any Affiliate ID).</li>'."\n";
					echo '<li><code>%%coupon_affiliate_id%%</code> = This is the end of an Affiliate Coupon Code <em>(i.e., the referring affiliate\'s ID)</em>. This is only applicable if an Affiliate Coupon Code is accepted by your configuration of s2Member.</li>'."\n";
					echo '</ul>'."\n";
				}

				echo '<strong>Custom Registration/Profile Fields are also supported in this Notification:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%date_of_birth%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>date_of_birth</code>.</li>'."\n";
				echo '<li><code>%%street_address%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>street_address</code>.</li>'."\n";
				echo '<li><code>%%country%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>country</code>.</li>'."\n";
				echo '<li><em><code>%%etc, etc...%%</code> <strong>see:</strong> s2Member → General Options → Registration/Profile Fields</em>.</li>'."\n";
				echo '</ul>'."\n";

				echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
				echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
				echo '</ul>'."\n";
				echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
				echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
				echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><em><strong>Tip:</strong> With <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Pro Add-on / Prices")).'" target="_blank" rel="external">s2Member Pro-Forms</a>, it\'s possible to integrate Affiliate Coupon Codes. Each of your affiliates can add their affiliate ID onto the end of any valid Coupon Code that you\'ve configured with s2Member Pro. Please check your Dashboard here: <strong>s2Member → Pro Coupon Codes → Affiliate Coupon Codes</strong>. This is a VERY powerful feature.</em></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_after_modification_tracking", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_trk_ops_page_during_left_sections_display_ccap_tracking", (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()), get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_before_ccap_tracking", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Capability Tracking Codes">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-ccap-tracking-section">'."\n";
				echo '<h3>Capability Tracking Codes (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, a list server, tracking codes from advertising networks, or the like; you\'ll want to read this section. The HTML'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' and/or PHP').' code that you enter below, will be loaded up in a web browser, each time Independent Custom Capabilities are purchased. This is marked `Capability`, because Capability Tracking Codes are displayed each time an "existing" User/Member <em>(even if they are/were a Free Subscriber)</em> pays you for Independent Custom Capabilities through a Buy Now transaction. This is the only circumstance in which your Capability Tracking Codes will be displayed.</p>'."\n";
				echo '<p>s2Member will display your Capability Tracking Codes in one of three possible locations... <strong>1.</strong> If possible, on the Thank-You Return Page, after returning from your Payment Gateway. <strong>2.</strong> Otherwise, if possible, on the Login Form after returning from your Payment Gateway <em>(i.e., when the Customer is asked to log back in)</em>. <strong>3.</strong> Otherwise, in the footer of your WordPress theme, as soon as possible <em>(immediately with s2Member Pro-Form integration)</em>; or after the Customer\'s next login.</p>'."\n";
				echo '<p><em><strong>AD BLOCKERS:</strong> If a web browser has ad blockers enabled (i.e., the web browser has an ad blocking extension or add-on), Tracking Codes from popular online advertising companies (including many affiliate networks) may NOT be shown. Ad blockers can prevent your Tracking Codes from being loaded in a customer\'s browser. If you\'d like to avoid this problem, consider integrating with s2Member\'s API Notifications instead of with Tracking Codes. API Notifications occur silently behind-the-scenes (more reliably), whereas Tracking Codes are loaded in a customer\'s browser. For more information, please see: <strong>s2Member → API / Notifications</strong>.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_during_ccap_tracking", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-ccap-tracking-codes">'."\n";
				echo 'Integrate Capability Tracking Codes:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_ccap_tracking_codes" id="ws-plugin--s2member-ccap-tracking-codes" rows="8" wrap="off" spellcheck="false">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["ccap_tracking_codes"]).'</textarea><br />'."\n";
				echo 'Any valid XHTML / JavaScript'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' (or even PHP)').' code will work just fine here. Just try not to put anything here that would actually be visible to the Customer. Things like 1x1 pixel images that load up silently and/or JavaScript tracking routines will be fine. Google Analytics code works just fine, AdSense performance tracking, as well as Yahoo tracking and other affiliate network codes are all OK here.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%txn_id%%</code> = The Payment Transaction ID, which is always unique for each payment received.</li>'."\n";
				echo '<li><code>%%txn_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe. Each Stripe Customer has this Customer ID; and also a Transaction ID associated with their purchase [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%txn_cid%% is simply set to the %%txn_id%% value; i.e., it is a duplicate of %%txn_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Transaction ID. See %%txn_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%amount%%</code> = The Amount of the payment. If you offer something 100% free, this will be <code>0</code>.</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased the Independent Custom Capabilities.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased the Independent Custom Capabilities.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased the Independent Custom Capabilities.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased the Independent Custom Capabilities.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <code><em>*level:custom_capabilities:fixed term</em></code>) that the payment is for. [ <a href="#" onclick="alert(\'With Independent Custom Capabilities, the `level` portion of this string will be an asterisk ( `*` ), since the Membership Level is irrelevant, and remains `as it was`.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address associated with their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_login%%</code> = The Username associated with their account. The Customer created this during registration.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s original IP Address, during checkout/registration via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%user_id%%</code> = A unique WordPress User ID that references this account in the WordPress database.</li>'."\n";
				echo '</ul>'."\n";

				if(c_ws_plugin__s2member_utils_conds::pro_is_installed())
				{
					echo '<strong>Coupon Replacement Codes (applicable only w/ s2Member Pro-Forms):</strong>'."\n";
					echo '<ul class="ws-menu-page-li-margins">'."\n";
					echo '<li><code>%%full_coupon_code%%</code> = A full Coupon Code—if one is accepted by your configuration of s2Member. This may indicate an Affiliate Coupon Code, which will include your Affiliate Suffix Chars too (i.e., the full Coupon Code).</li>'."\n";
					echo '<li><code>%%coupon_code%%</code> = A Coupon Code—if one is accepted by your configuration of s2Member. This will NOT include any Affiliate Suffix Chars. This indicates the actual Coupon Code accepted by your configuration of s2Member (excluding any Affiliate ID).</li>'."\n";
					echo '<li><code>%%coupon_affiliate_id%%</code> = This is the end of an Affiliate Coupon Code <em>(i.e., the referring affiliate\'s ID)</em>. This is only applicable if an Affiliate Coupon Code is accepted by your configuration of s2Member.</li>'."\n";
					echo '</ul>'."\n";
				}

				echo '<strong>Custom Registration/Profile Fields are also supported in this Notification:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%date_of_birth%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>date_of_birth</code>.</li>'."\n";
				echo '<li><code>%%street_address%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>street_address</code>.</li>'."\n";
				echo '<li><code>%%country%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>country</code>.</li>'."\n";
				echo '<li><em><code>%%etc, etc...%%</code> <strong>see:</strong> s2Member → General Options → Registration/Profile Fields</em>.</li>'."\n";
				echo '</ul>'."\n";

				echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
				echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
				echo '</ul>'."\n";
				echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
				echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
				echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><em><strong>Tip:</strong> With <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Pro Add-on / Prices")).'" target="_blank" rel="external">s2Member Pro-Forms</a>, it\'s possible to integrate Affiliate Coupon Codes. Each of your affiliates can add their affiliate ID onto the end of any valid Coupon Code that you\'ve configured with s2Member Pro. Please check your Dashboard here: <strong>s2Member → Pro Coupon Codes → Affiliate Coupon Codes</strong>. This is a VERY powerful feature.</em></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_after_ccap_tracking", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_trk_ops_page_during_left_sections_display_sp_tracking", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_before_sp_tracking", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Specific Post/Page Tracking Codes">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-sp-tracking-section">'."\n";
				echo '<h3>Tracking Codes For Specific Post/Page Access (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, a list server, tracking codes from advertising networks, or the like; you\'ll want to read this section. The HTML'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' and/or PHP').' code that you enter below, will be loaded up in a web browser, after a Customer completes a successful transaction through your Payment Gateway; specifically for Post/Page Access. These Codes are NOT injected for any type of Membership Level Access'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' or Independent Custom Capabilities').'. These are only for Specific Post/Page transactions. The Tracking Codes that you enter below, will be displayed in one of two possible locations... <strong>1.</strong> If possible, on the Thank-You Return Page, after returning from your Payment Gateway. <strong>2.</strong> Otherwise, in the footer of your WordPress theme, as soon as possible <em>(immediately with s2Member Pro-Form integration)</em>.</p>'."\n";
				echo '<p><em><strong>AD BLOCKERS:</strong> If a web browser has ad blockers enabled (i.e., the web browser has an ad blocking extension or add-on), Tracking Codes from popular online advertising companies (including many affiliate networks) may NOT be shown. Ad blockers can prevent your Tracking Codes from being loaded in a customer\'s browser. If you\'d like to avoid this problem, consider integrating with s2Member\'s API Notifications instead of with Tracking Codes. API Notifications occur silently behind-the-scenes (more reliably), whereas Tracking Codes are loaded in a customer\'s browser. For more information, please see: <strong>s2Member → API / Notifications</strong>.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_during_sp_tracking", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-sp-tracking-codes">'."\n";
				echo 'Specific Post/Page Tracking Codes:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_sp_tracking_codes" id="ws-plugin--s2member-sp-tracking-codes" rows="8" wrap="off" spellcheck="false">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_tracking_codes"]).'</textarea><br />'."\n";
				echo 'Any valid XHTML / JavaScript'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' (or even PHP)').' code will work just fine here. Just try not to put anything here that would actually be visible to the Customer. Things like 1x1 pixel images that load up silently and/or JavaScript tracking routines will be fine. Google Analytics code works just fine, AdSense performance tracking, as well as Yahoo tracking and other affiliate network codes are all OK here.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%txn_id%%</code> = The Paid Transaction ID. Payment Gateways assign a unique identifier for every purchase.</li>'."\n";
				echo '<li><code>%%txn_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe. Each Stripe Customer has this Customer ID; and also a Transaction ID associated with their purchase [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%txn_cid%% is simply set to the %%txn_id%% value; i.e., it is a duplicate of %%txn_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Transaction ID. See %%txn_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%amount%%</code> = The full Amount that you charged for Specific Post/Page Access. If you offer something 100% free, this will be <code>0</code>.</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s IP Address, detected during checkout via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number. Ex: <code><em>sp:13,24,36:72</em></code> (translates to: <code><em>sp:comma-delimited IDs:expiration hours</em></code>).</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
				echo '</ul>'."\n";

				if(c_ws_plugin__s2member_utils_conds::pro_is_installed())
				{
					echo '<strong>Coupon Replacement Codes (applicable only w/ s2Member Pro-Forms):</strong>'."\n";
					echo '<ul class="ws-menu-page-li-margins">'."\n";
					echo '<li><code>%%full_coupon_code%%</code> = A full Coupon Code—if one is accepted by your configuration of s2Member. This may indicate an Affiliate Coupon Code, which will include your Affiliate Suffix Chars too (i.e., the full Coupon Code).</li>'."\n";
					echo '<li><code>%%coupon_code%%</code> = A Coupon Code—if one is accepted by your configuration of s2Member. This will NOT include any Affiliate Suffix Chars. This indicates the actual Coupon Code accepted by your configuration of s2Member (excluding any Affiliate ID).</li>'."\n";
					echo '<li><code>%%coupon_affiliate_id%%</code> = This is the end of an Affiliate Coupon Code <em>(i.e., the referring affiliate\'s ID)</em>. This is only applicable if an Affiliate Coupon Code is accepted by your configuration of s2Member.</li>'."\n";
					echo '</ul>'."\n";
				}

				echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
				echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
				echo '</ul>'."\n";
				echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
				echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
				echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><em><strong>Tip:</strong> With <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Pro Add-on / Prices")).'" target="_blank" rel="external">s2Member Pro-Forms</a>, it\'s possible to integrate Affiliate Coupon Codes. Each of your affiliates can add their affiliate ID onto the end of any valid Coupon Code that you\'ve configured with s2Member Pro. Please check your Dashboard here: <strong>s2Member → Pro Coupon Codes → Affiliate Coupon Codes</strong>. This is a VERY powerful feature.</em></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_after_sp_tracking", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_trk_ops_page_during_left_sections_display_integrations_divider", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_before_integrations_divider", get_defined_vars());
				echo '<div style="border-bottom:1px solid #DFDFDF; margin:-20px 0 20px 0; padding:0;">&nbsp;</div>'."\n";
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_after_integrations_divider", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_trk_ops_page_during_left_sections_display_idev", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_before_idev", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Integrating iDevAffiliate">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-idev-section">'."\n";
				echo '<h3>Integrating iDevAffiliate (affiliate program management)</h3>'."\n";
				echo '<a href="http://www.s2member.com/r/idevaffiliate/" target="_blank"><img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/idev-logo.gif" class="ws-menu-page-right ws-menu-page-bordered" style="width:125px; height:125px; border:0;" alt="." /></a>'."\n";
				echo '<p>Adding affiliate tracking software to your site is one of the most effective ways to achieve more sales, more traffic, and more search engine ranking. <a href="http://www.s2member.com/r/idevaffiliate/" target="_blank" rel="external">iDevAffiliate</a> (an affiliate management portal), installs in just minutes, and can be integrated seamlessly with s2Member. We recommend <a href="http://www.s2member.com/r/idevaffiliate/" target="_blank" rel="external">iDevAffiliate Standard</a> ( $99 ) because of its proven track record, and its ability to integrate with s2Member using a variety of techniques. The most popular being a Hidden Image Tag.</p>'."\n";
				echo '<p>If you choose to <a href="http://www.s2member.com/r/idevaffiliate/" target="_blank" rel="external">install iDevAffiliate</a>, you will need to configure your <strong>iDevAffiliate → Shopping Cart Integration</strong>. Please choose <code>Generic Tracking Pixel</code>. Then, grab your Hidden Image Tag, and pop the code provided by iDevAffiliate into one of the fields for Tracking Codes <em>(at the top of this page)</em>. You MUST also add Replacement Codes to your Hidden Image Tag. To save you some trouble, we\'ve provided some examples below, one for each of s2Member\'s Tracking Code integrations.</p>'."\n";
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_during_idev", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><strong>Signup Tracking Code, for iDevAffiliate integration:</strong></p>'."\n";
				echo '<p>idev_saleamt=<strong>%%initial%%</strong><br />idev_ordernum=<strong>%%subscr_id%%</strong></p>'."\n";
				echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/idev-signup-tracking-code.x-php")).'</p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><strong>Modification Tracking Code, for iDevAffiliate integration:</strong></p>'."\n";
				echo '<p>idev_saleamt=<strong>%%initial%%</strong><br />idev_ordernum=<strong>%%subscr_id%%</strong></p>'."\n";
				echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/idev-modification-tracking-code.x-php")).'</p>'."\n";

				if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site())
				{
					echo '<div class="ws-menu-page-hr"></div>'."\n";
					echo '<p><strong>Capability Tracking Code, for iDevAffiliate integration:</strong></p>'."\n";
					echo '<p>idev_saleamt=<strong>%%amount%%</strong><br />idev_ordernum=<strong>%%txn_id%%</strong></p>'."\n";
					echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/idev-ccap-tracking-code.x-php")).'</p>'."\n";
				}

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><strong>Specific Post/Page Tracking Code, for iDevAffiliate integration:</strong></p>'."\n";
				echo '<p>idev_saleamt=<strong>%%amount%%</strong><br />idev_ordernum=<strong>%%txn_id%%</strong></p>'."\n";
				echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/idev-sp-tracking-code.x-php")).'</p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p>Your <code>profile</code> ID will be assigned by iDevAffiliate. Be sure to replace <code>profile=123</code> with your own profile ID.</p>'."\n";
				echo '<p><em><strong>Tip:</strong> iDevAffiliate also provides an alternative method, using a 3rd-party call. The alternative 3rd-party call, could be used with <strong>s2Member → API Notifications.</strong> A 3rd-party call, is essentially an HTTP connection that runs silently behind-the-scenes, as opposed to being loaded in a browser. It\'s a bit more powerful (and reliable), but also more advanced.</em></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><em><strong>Tip:</strong> With <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Pro Add-on / Prices")).'" target="_blank" rel="external">s2Member Pro-Forms</a>, it\'s possible to integrate Affiliate Coupon Codes with iDevAffiliate. Each of your affiliates can add their affiliate ID onto the end of any valid Coupon Code that you\'ve configured with s2Member Pro. Please check your Dashboard here: <strong>s2Member → Pro Coupon Codes → Affiliate Coupon Codes</strong>. This is a VERY powerful feature.</em></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_after_idev", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_trk_ops_page_during_left_sections_display_shareasale", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_before_shareasale", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Integrating ShareASale">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-shareasale-section">'."\n";
				echo '<h3>Integrating ShareASale (affiliate program management)</h3>'."\n";
				echo '<a href="http://www.s2member.com/r/shareasale/" target="_blank"><img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/sas-logo.png" class="ws-menu-page-right ws-menu-page-bordered" style="width:125px; height:125px; border:0;" alt="." /></a>'."\n";
				echo '<p>Established in 2000, <a href="http://www.s2member.com/r/shareasale/" target="_blank" rel="external">ShareASale</a> provides award winning technology and service; which will enable you to connect with a network of established affiliates, as well as recruit new ones. Joining ShareASale, maximizes your ability to reach the greatest number of affiliates, with the least amount of work. At ShareASale, you\'ll have access to an existing affiliate-base. You place your site on the market, and let their existing affiliates promote your products/services.</p>'."\n";
				echo '<p>If you <a href="http://www.s2member.com/r/shareasale/" target="_blank" rel="external">become a Merchant at ShareASale</a>, you will need to configure your <strong>ShareASale → Sale Tracking</strong>. Grab your Hidden Image Tag, and pop the code provided by ShareASale into one of the fields for Tracking Codes <em>(at the top of this page)</em>. You MUST also add Replacement Codes to your Hidden Image Tag. To save you some trouble, we\'ve provided some examples below, one for each of s2Member\'s Tracking Code integrations.</p>'."\n";
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_during_shareasale", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><strong>Signup Tracking Code, for ShareASale integration:</strong></p>'."\n";
				echo '<p>amount=<strong>%%initial%%</strong><br />tracking=<strong>%%subscr_id%%</strong></p>'."\n";
				echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/sas-signup-tracking-code.x-php")).'</p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><strong>Modification Tracking Code, for ShareASale integration:</strong></p>'."\n";
				echo '<p>amount=<strong>%%initial%%</strong><br />tracking=<strong>%%subscr_id%%</strong></p>'."\n";
				echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/sas-modification-tracking-code.x-php")).'</p>'."\n";

				if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site())
				{
					echo '<div class="ws-menu-page-hr"></div>'."\n";
					echo '<p><strong>Capability Tracking Code, for ShareASale integration:</strong></p>'."\n";
					echo '<p>amount=<strong>%%amount%%</strong><br />tracking=<strong>%%txn_id%%</strong></p>'."\n";
					echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/sas-ccap-tracking-code.x-php")).'</p>'."\n";
				}

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><strong>Specific Post/Page Tracking Code, for ShareASale integration:</strong></p>'."\n";
				echo '<p>amount=<strong>%%amount%%</strong><br />tracking=<strong>%%txn_id%%</strong></p>'."\n";
				echo '<p>'.c_ws_plugin__s2member_utils_strings::highlight_php(file_get_contents(dirname(__FILE__)."/code-samples/sas-sp-tracking-code.x-php")).'</p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p>Your <code>merchantID</code> will be assigned by ShareASale. Be sure to replace <code>merchantID=123</code> with the one they assign you.</p>'."\n";
				echo '<p><em><strong>Tip:</strong> ShareASale also provides an alternative method, using a 3rd-party call. The alternative 3rd-party call, could be used with <strong>s2Member → API Notifications.</strong> A 3rd-party call, is essentially an HTTP connection that runs silently behind-the-scenes, as opposed to being loaded in a browser. It\'s a bit more powerful (and reliable), but also more advanced.</em></p>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><em><strong>Tip:</strong> With <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Pro Add-on / Prices")).'" target="_blank" rel="external">s2Member Pro-Forms</a>, it\'s possible to integrate Affiliate Coupon Codes with ShareASale. Each of your affiliates can add their affiliate ID onto the end of any valid Coupon Code that you\'ve configured with s2Member Pro. Please check your Dashboard here: <strong>s2Member → Pro Coupon Codes → Affiliate Coupon Codes</strong>. This is a VERY powerful feature.</em></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_after_shareasale", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_trk_ops_page_during_left_sections_display_other_methods", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_before_other_methods", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Other Tracking Methods Available">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-other-methods-section">'."\n";
				echo '<h3>Other Tracking Methods Are Available (there\'s always a way)</h3>'."\n";
				echo '<p>Check the s2Member API Notifications panel. You\'ll find additional layers of automation available through the use of the `Signup`, `Registration`, `Payment`, `Modification`, `EOT/Deletion`, `Refund/Reversal`, and `Specific Post/Page` Notifications that are available to you through the s2Member API. The s2Member API Notifications make it possible to integrate with 3rd party applications; like list servers, affiliate programs, and other back-office routines; in more advanced ways.</p>'."\n";
				echo '<p>Since s2Member API Notifications operate silently on the back-end, they tend to be more reliable and also more versatile. That being said, nothing replaces the simplicity of Tracking Codes. The more advanced API Notifications are NOT always the best tool for the job. For instance, API Notifications will NOT work with Google Analytics, or 1 pixel &lt;img&gt; tags. They operate silently behind-the-scenes, using HTTP connections, as opposed to being loaded in a browser.</p>'."\n";
				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_during_other_methods", get_defined_vars());

				echo '<div class="ws-menu-page-hr"></div>'."\n";
				echo '<p><em><strong>Tip:</strong> With <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Pro Add-on / Prices")).'" target="_blank" rel="external">s2Member Pro-Forms</a>, it\'s possible to integrate Affiliate Coupon Codes with iDevAffiliate. Each of your affiliates can add their affiliate ID onto the end of any valid Coupon Code that you\'ve configured with s2Member Pro. Please check your Dashboard here: <strong>s2Member → Pro Coupon Codes → Affiliate Coupon Codes</strong>. This is a VERY powerful feature.</em></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_trk_ops_page_during_left_sections_after_other_methods", get_defined_vars());
			}

			do_action("ws_plugin__s2member_during_trk_ops_page_after_left_sections", get_defined_vars());

			echo '<p class="submit"><input type="submit" value="Save All Changes" /></p>'."\n";

			echo '</form>'."\n";

			echo '</td>'."\n";

			echo '<td class="ws-menu-page-table-r">'."\n";
			c_ws_plugin__s2member_menu_pages_rs::display();
			echo '</td>'."\n";

			echo '</tr>'."\n";
			echo '</tbody>'."\n";
			echo '</table>'."\n";

			echo '</div>'."\n";
		}
	}
}

new c_ws_plugin__s2member_menu_page_trk_ops ();