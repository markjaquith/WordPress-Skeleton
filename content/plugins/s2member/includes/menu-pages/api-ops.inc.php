<?php
/**
 * Menu page for the s2Member plugin (API Notifications page).
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

if(!class_exists("c_ws_plugin__s2member_menu_page_api_ops"))
{
	/**
	 * Menu page for the s2Member plugin (API Notifications page).
	 *
	 * @package s2Member\Menu_Pages
	 * @since 110531
	 */
	class c_ws_plugin__s2member_menu_page_api_ops
	{
		public function __construct()
		{
			echo '<div class="wrap ws-menu-page">'."\n";

			echo '<div class="ws-menu-page-toolbox">'."\n";
			c_ws_plugin__s2member_menu_pages_tb::display();
			echo '</div>'."\n";

			echo '<h2>API / Notifications</h2>'."\n";

			echo '<table class="ws-menu-page-table">'."\n";
			echo '<tbody class="ws-menu-page-table-tbody">'."\n";
			echo '<tr class="ws-menu-page-table-tr">'."\n";
			echo '<td class="ws-menu-page-table-l">'."\n";

			echo '<form method="post" name="ws_plugin__s2member_options_form" id="ws-plugin--s2member-options-form" autocomplete="off">'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_options_save" id="ws-plugin--s2member-options-save" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-options-save")).'" />'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_configured" id="ws-plugin--s2member-configured" value="1" />'."\n";

			do_action("ws_plugin__s2member_during_api_ops_page_before_left_sections", get_defined_vars());

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_signup_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_signup_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Signup Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-signup-notifications-section">'."\n";
				echo '<h3>Signup Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified whenever a new Subscription is created, you\'ll want to read this section. This is marked `Signup`, because the URLs that you list below, will be notified each time a "new", "paying" Member signs up. Depending on your fee structure, this may include a payment that establishes their Subscription, or it may not. This Notification will only be triggered once for each Member. Signup Notifications are sent right after a "new", "paying" Member signs up successfully through your Payment Gateway, regardless of whether any money has actually been transacted. In other words, this Notification is triggered anytime a "new", "paying" Member signs up through your Payment Gateway, even if you provided them with something for free <em>(i.e., even if no money is being transacted)</em>.</p>'."\n";
				echo '<p>This Notification will NOT be processed for Free Subscribers that register without going through your Payment Gateway at all (i.e., they simply register on-site; and there is no checkout whatsoever). This Notification will NOT be processed when an "existing" User/Member pays for a new Subscription <em>(see: Modification Notifications for that scenario)</em>.'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' And, this Notification will NOT be processed on Buy Now transactions for Independent Custom Capabilities <em>(see: Payment Notifications for that scenario)</em>.').'</p>'."\n";
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These Signup Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_signup_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-signup-notification-urls">'."\n";
				echo 'Signup Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_signup_notification_urls" id="ws-plugin--s2member-signup-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["signup_notification_urls"]).'</textarea><br />'."\n";
				echo 'Signup Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%subscr_id%%</code> = The Paid Subscription ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the Transaction ID for the purchase. Payment Gateways do not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there is only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe, which remains constant throughout any &amp; all future payments. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%subscr_cid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Subscription and/or Transaction ID. See %%subscr_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%initial%%</code> = The Initial Fee charged during signup. If you offered a 100% Free Trial, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This will always represent the amount of money the Customer spent, whenever they initially signed up, no matter what. Even if that amount is 0.\\n\\nIf a Customer signs up, under the terms of a 100% Free Trial Period, this will be 0. So be careful using %%initial%% when you offer a 100% Free Trial Period, because a $0.00 sale amount could cause havoc with affiliate programs.\\n\\nIf you\\\'re offering a 100% Free Trial Period, and you need to track sales through affiliate programs, you can either hard-code an amount; or use `Payment Notifications` instead.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular%%</code> = The Regular Amount of the Subscription. If you offer something 100% free, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This is how much the Subscription costs after an Initial Period expires. If you did NOT offer an Initial Period at a different price, %%initial%% and %%regular%% will be equal to the same thing.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%recurring%%</code> = This is the amount that will be charged on a recurring basis, or <code>0</code> if non-recurring. [ <a href="#" onclick="alert(\'If Recurring Payments have not been required, this will be equal to 0. That being said, %%regular%% &amp; %%recurring%% are usually the same value. This variable can be used in two different ways. You can use it to determine what the Regular Recurring Rate is, or to determine whether the Subscription will recur or not. If it is going to recur, %%recurring%% will be > 0.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s IP Address, detected during checkout via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <code><em>level:custom_capabilities:fixed term</em></code>) for the Membership Subscription.</li>'."\n";
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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-signup-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_signup_notification_recipients" id="ws-plugin--s2member-signup-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["signup_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";
			}

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_registration_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_registration_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Registration Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-registration-notifications-section">'."\n";
				echo '<h3>Registration Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified whenever a "new" Member is created, you\'ll want to read this section. This is marked `Registration`, because the URLs that you list below, will be notified each time a "new" Member registers a Username. This is usually triggered right after a `Signup` Notification; at the point in which a "new" Member successfully completes the Registration form, and they are assigned a Username.</p>'."\n";
				echo '<p>This Notification is ALSO triggered whenever you create a "new" User inside your WordPress Dashboard.</p>'."\n";
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These Registration Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_registration_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-registration-notification-urls">'."\n";
				echo 'Registration Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_registration_notification_urls" id="ws-plugin--s2member-registration-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["registration_notification_urls"]).'</textarea><br />'."\n";
				echo 'Registration Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%role%%</code> = The Role ID <code>(subscriber, s2member_level[0-9]+, administrator, editor, author, contributor)</code>.</li>'."\n";
				echo '<li><code>%%level%%</code> = The Level number <code>(0, 1, 2, 3, 4)</code>. (<em>deprecated, no longer recommended; use <code>%%role%%</code></em>)</li>'."\n";
				echo '<li><code>%%ccaps%%</code> = Custom Capabilities. Ex: <code>music,videos,free_gift</code> (<em>in comma-delimited format</em>).</li>'."\n";
				echo '<li><code>%%auto_eot_time%%</code> = Auto-EOT Time (if applicable). Ex: <code>1299925670</code> (<em>unix timestamp</em>).</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name (First &amp; Last) of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_login%%</code> = The Username the Member selected during registration.</li>'."\n";
				echo '<li><code>%%user_pass%%</code> = The Password selected or generated during registration.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The User\'s IP Address, detected via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%user_id%%</code> = A unique WordPress User ID generated during registration.</li>'."\n";
				echo '</ul>'."\n";

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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-registration-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_registration_notification_recipients" id="ws-plugin--s2member-registration-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["registration_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_after_registration_notifications", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_payment_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_payment_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Payment Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-payment-notifications-section">'."\n";
				echo '<h3>Payment Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified whenever payment transactions <em>(including Recurring Payments)</em> take place, you\'ll want to read this section. This is marked `Payment`, because the URLs that you list below, will be notified each time an actual payment occurs. Depending on your fee structure, this may include a first Initial Payment that establishes a Subscription. But more importantly, this will be triggered on all future payments that are received for the lifetime of the Subscription.</p>'."\n";
				echo '<p>So, unlike the `Signup` Notification, `Payment` Notifications take place whenever actual payments are received, instead of just once after signup is completed. If a payment is required during signup <em>(i.e., no Free Trial is being offered)</em>, a Signup Notification will be triggered, and a Payment Notification will ALSO be triggered. In other words, a Payment Notification occurs anytime funds are received, no matter what.</p>'."\n";
				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? '<p>Payment Notifications are also triggered whenever a Buy Now purchase for Independent Custom Capabilities takes place.</p>'."\n" : '';
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These Payment Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_payment_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-payment-notification-urls">'."\n";
				echo 'Payment Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_payment_notification_urls" id="ws-plugin--s2member-payment-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["payment_notification_urls"]).'</textarea><br />'."\n";
				echo 'Payment Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%subscr_id%%</code> = The Paid Subscription ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'There are some exceptions. If you are selling Lifetime or Fixed-Term (non-recurring) access, and/or Independent Custom Capabilities, using Buy Now functionality; the %%subscr_id%% is actually set to the Transaction ID for the payment.\\n\\nPayment Gateways do not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there is only ONE payment), which goes for Independent Custom Capability purchases too; using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe, which remains constant throughout any &amp; all future payments. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%subscr_cid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Subscription and/or Transaction ID. See %%subscr_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%txn_id%%</code> = The Payment Transaction ID, which is always unique for each payment received.</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%amount%%</code> = The Amount of the payment. Most affiliate programs calculate commissions from this.</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased the Membership Subscription'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' or Capabilities.').'</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased the Membership Subscription'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' or Capabilities.').'</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased the Membership Subscription'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' or Capabilities.').'</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased the Membership Subscription'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' or Capabilities.').'</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <code><em>level:custom_capabilities:fixed term</em></code>) that the payment is for.'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' [ <a href="#" onclick="alert(\'If the Payment Notification is for Independent Custom Capabilities, the `level` portion of this string will be an asterisk ( `*` ), since the Membership Level is irrelevant, and remains `as it was` in that scenario. In all other cases, the `level` portion will be a numeric value.\'); return false;">?</a> ]').'</li>'."\n";
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
					echo '<strong>Coupon Replacement Codes (applicable only w/ s2Member Pro-Forms):</strong><br />'."\n";
					echo '<em>These are ONLY included with payments that occur during checkout. They will NOT be provided with any future recurring payments.</em>'."\n";
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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-payment-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_payment_notification_recipients" id="ws-plugin--s2member-payment-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["payment_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_after_payment_notifications", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_modification_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_modification_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Modification Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-modification-notifications-section">'."\n";
				echo '<h3>Modification Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified each time a new Subscription is created by an "existing" User/Member, or an "existing" Member modifies their paid Subscription terms, you\'ll want to read this section. This is marked `Modification`, because the URLs that you list below, will be notified each time an "existing" User/Member <em>(even if they are/were a Free Subscriber)</em> signs up for a paid Subscription <em>(i.e., a Modification takes place against an existing account within WordPress)</em>, or an "existing" Member modifies their paid Subscription terms <em>(again, a Modification takes places against an existing account within WordPress)</em>. Depending on your fee structure, this may include a payment that establishes their Subscription, or it may not.</p>'."\n";
				echo '<p>Modification Notifications are sent right after a Member signs up and/or modifies billing terms successfully through your Payment Gateway, regardless of whether any money has actually been transacted. In other words, this Notification is triggered, even if you provided them with something for free <em>(i.e., even if no money is being transacted)</em>.</p>'."\n";
				echo '<p>This Notification will NOT be processed for "new" Users/Members <em>(see: Signup Notifications for that scenario)</em>.'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' And, this Notification will NOT be processed for Independent Custom Capability purchases <em>(see: Payment Notifications for that scenario)</em>.').'</p>'."\n";
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These Modification Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_modification_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-modification-notification-urls">'."\n";
				echo 'Modification Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_modification_notification_urls" id="ws-plugin--s2member-modification-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["modification_notification_urls"]).'</textarea><br />'."\n";
				echo 'Modification Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%subscr_id%%</code> = The Paid Subscription ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the Transaction ID for the purchase. Payment Gateways do not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there is only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe, which remains constant throughout any &amp; all future payments. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%subscr_cid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Subscription and/or Transaction ID. See %%subscr_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%initial%%</code> = The Initial Fee. If you offered a 100% Free Trial, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This will always represent the amount of money the Customer spent when they completed checkout, no matter what. Even if that amount is 0.\\n\\nIf a Customer upgrades/downgrades under the terms of a 100% Free Trial Period, this will be 0. So, please be careful using %%initial%% when you offer a 100% Free Trial Period, because a $0.00 sale amount could cause havoc with affiliate programs.\\n\\nIf you\\\'re offering a 100% Free Trial Period, and you need to track sales through affiliate programs, you can either hard-code an amount; or use `Payment Notifications` instead.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular%%</code> = The Regular Amount of the Subscription. If you offer something 100% free, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This is how much the Subscription costs after an Initial Period expires. If you did NOT offer an Initial Period at a different price, %%initial%% and %%regular%% will be equal to the same thing.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%recurring%%</code> = This is the amount that will be charged on a recurring basis, or <code>0</code> if non-recurring. [ <a href="#" onclick="alert(\'If Recurring Payments have not been required, this will be equal to 0. That being said, %%regular%% &amp; %%recurring%% are usually the same value. This variable can be used in two different ways. You can use it to determine what the Regular Recurring Rate is, or to determine whether the Subscription will recur or not. If it is going to recur, %%recurring%% will be > 0.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <code><em>level:custom_capabilities:fixed term</em></code>) for the Membership Subscription.</li>'."\n";
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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-modification-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_modification_notification_recipients" id="ws-plugin--s2member-modification-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["modification_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";
			}

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_cancellation_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_cancellation_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Cancellation Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-cancellation-notifications-section">'."\n";
				echo '<h3>Cancellation Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified whenever Subscriptions are cancelled through your Payment Gateway, you\'ll want to read this section. This is marked `Cancellation`, because the URLs that you list below, will be notified each time a Subscription is cancelled. A Cancellation is triggered when you cancel a Customer\'s Subscription through your Payment Gateway, or when a Customer cancels their own Subscription.</p>'."\n";
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These Cancellation Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><em><strong>Understanding Cancellations:</strong> It\'s important to realize that a Cancellation is not an EOT (End Of Term). All that happens during a Cancellation event, is that billing is stopped, and it\'s understood that the Customer is going to lose access, at some point in the future. This does NOT mean, that access will be revoked immediately. A separate EOT event will automatically handle a (demotion or deletion) later, at the appropriate time; which could be several days, or even a year after the Cancellation took place.</em></p>'."\n";
				echo '<p><em><strong>Some Hairy Details:</strong> There might be times whenever you notice that a Member\'s Subscription has been cancelled through your Payment Gateway... but, s2Member continues allowing the User access to your site as a paid Member. Please don\'t be confused by this... in 99.9% of these cases, the reason for this is legitimate. s2Member will only remove the User\'s Membership privileges when an EOT (End Of Term) is processed, a refund occurs, a chargeback occurs, or when a cancellation occurs - which would later result in a delayed Auto-EOT by s2Member.</em></p>'."\n";
				echo '<p><em>s2Member will not process an EOT (End Of Term) until the User has completely used up the time they paid for. In other words, if a User signs up for a monthly Subscription on Jan 1st, and then cancels their Subscription on Jan 15th; technically, they should still be allowed to access the site for another 15 days, and then on Feb 1st, the time they paid for has completely elapsed. At that time, s2Member will remove their Membership privileges; by either demoting them to a Free Subscriber, or deleting their account from the system (based on your configuration). s2Member also calculates one extra day (24 hours) into its equation, just to make sure access is not removed sooner than a Customer might expect.</em></p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_cancellation_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-cancellation-notification-urls">'."\n";
				echo 'Cancellation Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_cancellation_notification_urls" id="ws-plugin--s2member-cancellation-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["cancellation_notification_urls"]).'</textarea><br />'."\n";
				echo 'Cancellation Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%subscr_id%%</code> = The Paid Subscription ID, which remained constant throughout the lifetime of the Membership. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the original Transaction ID for the purchase. Payment Gateways do not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there was only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that required a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remained constant throughout the lifetime of the Membership. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that required a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe, which remained constant throughout the lifetime of the Membership. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%subscr_cid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Subscription and/or Transaction ID. See %%subscr_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <em>level:custom_capabilities:fixed term</em>) that the Subscription was for.</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address associated with their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_login%%</code> = The Username associated with their account. The Customer created this during registration.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s original IP Address, during checkout/registration via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%user_id%%</code> = A unique WordPress User ID that references this account in the WordPress database.</li>'."\n";
				echo '</ul>'."\n";

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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-cancellation-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_cancellation_notification_recipients" id="ws-plugin--s2member-cancellation-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["cancellation_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_after_cancellation_notifications", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_eot_deletion_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_eot_deletion_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="EOT/Deletion Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-eot-deletion-notifications-section">'."\n";
				echo '<h3>EOT/Deletion Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified whenever Subscriptions have ended <em>(and a Member is demoted to a Free Subscriber)</em>, or when an account is deleted from the system, you\'ll want to read this section. This is marked `EOT/Deletion`, because the URLs that you list below, will be notified in both cases.</p>'."\n";
				echo '<p><strong>EOT = End Of Term.</strong> An EOT is triggered <em>immediately</em> when you refund a Customer, when a Customer forces a chargeback to occur, or when a paid Subscription ends naturally <em>(i.e., expires)</em>, and your Payment Gateway sends s2Member an EOT (End Of Term) response. Delayed EOTs occur after a Cancellation, either as a result of you cancelling a Customer\'s Subscription, or a Customer cancelling their own Subscription. A Cancellation usually results in a delayed EOT, because a Cancellation does NOT always warrant an immediate action; there could still be time left on their Subscription.</p>'."\n";
				echo '<p>Manual Deletions will trigger this Notification too. If you delete an account manually from within your WordPress Dashboard, your URLs can be notified automatically through this API Notification, and this scenario can be detected through the Replacement Code <code>%%eot_del_type%%</code>, which is documented below. So the two events are an EOT <em>(of any kind)</em> and/or a Manual Deletion.</p>'."\n";
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These EOT/Deletion Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><em><strong>Some Hairy Details:</strong> There might be times whenever you notice that a Member\'s Subscription has been cancelled through your Payment Gateway... but, s2Member continues allowing the User access to your site as a paid Member. Please don\'t be confused by this... in 99.9% of these cases, the reason for this is legitimate. s2Member will only remove the User\'s Membership privileges when an EOT (End Of Term) is processed, a refund occurs, a chargeback occurs, or when a cancellation occurs - which would later result in a delayed Auto-EOT by s2Member.</em></p>'."\n";
				echo '<p><em>s2Member will not process an EOT (End Of Term) until the User has completely used up the time they paid for. In other words, if a User signs up for a monthly Subscription on Jan 1st, and then cancels their Subscription on Jan 15th; technically, they should still be allowed to access the site for another 15 days, and then on Feb 1st, the time they paid for has completely elapsed. At that time, s2Member will remove their Membership privileges; by either demoting them to a Free Subscriber, or deleting their account from the system (based on your configuration). s2Member also calculates one extra day (24 hours) into its equation, just to make sure access is not removed sooner than a Customer might expect.</em></p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_eot_deletion_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-eot-del-notification-urls">'."\n";
				echo 'EOT/Deletion Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_eot_del_notification_urls" id="ws-plugin--s2member-eot-del-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["eot_del_notification_urls"]).'</textarea><br />'."\n";
				echo 'EOT/Deletion Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%eot_del_type%%</code> = The type of event that triggered this Notification. [ <a href="#" onclick="alert(\'List of possible values:\\n\\nuser-removal-deletion (i.e., manual removal/deletion)\\nauto-eot-cancellation-expiration-demotion\\nauto-eot-cancellation-expiration-deletion\\nipn-cancellation-expiration-demotion\\nipn-cancellation-expiration-deletion\\nipn-refund-reversal-demotion\\nipn-refund-reversal-deletion\'); return false;">list of possible values</a> ]</li>'."\n";
				echo '<li><code>%%subscr_id%%</code> = The Paid Subscription ID, which remained constant throughout the lifetime of the Membership. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the original Transaction ID for the purchase. Payment Gateways do not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there was only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that required a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remained constant throughout the lifetime of the Membership. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that required a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe, which remained constant throughout the lifetime of the Membership. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%subscr_cid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Subscription and/or Transaction ID. See %%subscr_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name listed on their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address associated with their User account. This might be different than what is on file with your Payment Gateway.</li>'."\n";
				echo '<li><code>%%user_login%%</code> = The Username associated with their account. The Customer created this during registration.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s original IP Address, during checkout/registration via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%user_id%%</code> = A unique WordPress User ID that references this account in the WordPress database.</li>'."\n";
				echo '</ul>'."\n";

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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-eot-del-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_eot_del_notification_recipients" id="ws-plugin--s2member-eot-del-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["eot_del_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_after_eot_deletion_notifications", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_refund_reversal_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_refund_reversal_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Refund/Reversal Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-refund-reversal-notifications-section">'."\n";
				echo '<h3>Refund/Reversal Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified whenever Subscriptions have been refunded or reversed <em>(i.e., charged back to you)</em>, you\'ll want to read this section. This is marked `Refund/Reversal`, because the URLs that you list below, will ONLY be notified in those specific cases, as opposed to EOT/Deletion Notifications, which are all-inclusive.</p>'."\n";
				echo '<p>This is very similar to the EOT/Deletion Notification described above. However, there is an important distinction. The all-inclusive EOT/Deletion Notification includes cancellations, expirations, failed payments, refunds, chargebacks, and even manual deletions by the Administrator from within the Dashboard. In other words, EOT/Deletion Notifications are processed ANY time a deletion or End Of Term action takes place. This API Notification, that is, Refund/Reversal Notifications, do NOT include all of those scenarios.</p>'."\n";
				echo '<p>So the distinction is that Refund/Reversal Notifications are ONLY sent under two specific circumstances: 1. You log into your Payment Gateway and refund a payment that is associated with a Subscription. 2. The Customer complains to your Payment Gateway and a chargeback occurs, forcing a Reversal. In both of these cases, an EOT/Deletion Notification will be sent <em>(as described in the previous section)</em>, but since EOT/Deletion is a broader Notification, these Refund/Reversal Notifications are here so you can nail down specific back-office operations in these two specific scenarios.</p>'."\n";
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These Refund/Reversal Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_refund_reversal_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-ref-rev-notification-urls">'."\n";
				echo 'Refund/Reversal Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_ref_rev_notification_urls" id="ws-plugin--s2member-ref-rev-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["ref_rev_notification_urls"]).'</textarea><br />'."\n";
				echo 'Refund/Reversal Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%subscr_id%%</code> = The Paid Subscription ID, which remained constant throughout the lifetime of the Membership. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the original Transaction ID for the purchase. Payment Gateways do not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there was only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that required a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remained constant throughout the lifetime of the Membership. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that required a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe, which remained constant throughout the lifetime of the Membership. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%subscr_cid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Subscription and/or Transaction ID. See %%subscr_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%parent_txn_id%%</code> = The Parent Transaction ID, associated with the original payment being refunded/reversed.</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%-amount%%</code> = The Negative Amount of the payment, that was refunded or reversed back to the Customer.</li>'."\n";
				echo '<li><code>%%-fee%%</code> = The Negative Payment Gateway fee, that was refunded back to you as the Merchant/Seller.</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <em>level:custom_capabilities:fixed term</em>) that the payment was for.</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s original IP Address, during checkout/registration via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%user_id%%</code> = A unique WordPress User ID that references this account in the WordPress database.</li>'."\n";
				echo '</ul>'."\n";

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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-ref-rev-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_ref_rev_notification_recipients" id="ws-plugin--s2member-ref-rev-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["ref_rev_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_after_refund_reversal_notifications", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_sp_sale_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_sp_sale_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Specific Post/Page ~ Sale Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-sp-sale-notifications-section">'."\n";
				echo '<h3>Specific Post/Page ~ Sale Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified whenever Specific Post/Page sales take place, you\'ll want to read this section. This is marked `Specific Post/Page`, because the URLs that you list below, will be notified each time a payment occurs, on a sale providing access to a Specific Post/Page.</p>'."\n";
				echo '<p>This is one of only two API Notifications that are sent for Specific Post/Page Access <em>(i.e., this one, and another below, for Refunds/Reversals)</em>. All of the other API Notifications are designed for Membership Level Access'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' and Independent Custom Capabilities').'. None of the other API Notifications will ever be processed for Specific Post/Page Access. If you intend to respond to events related to Specific Post/Page Access, you MUST use one of the two API Notifications specifically geared to Post/Page Access.</p>'."\n";
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These Sale Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_sp_sale_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-sp-sale-notification-urls">'."\n";
				echo 'Specific Post/Page ~ Sale Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_sp_sale_notification_urls" id="ws-plugin--s2member-sp-sale-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_sale_notification_urls"]).'</textarea><br />'."\n";
				echo 'Specific Post/Page ~ Sale Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%sp_access_url%%</code> = The full URL (generated by s2Member) where the Customer can gain access.</li>'."\n";
				echo '<li><code>%%sp_access_exp%%</code> = Human readable expiration for <code>%%sp_access_url%%</code>. Ex: <em>(link expires in <code>%%sp_access_exp%%</code>)</em>.</li>'."\n";
				echo '<li><code>%%txn_id%%</code> = The Paid Transaction ID. Payment Gateways assign a unique identifier for every purchase.</li>'."\n";
				echo '<li><code>%%txn_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe. Each Stripe Customer has this Customer ID; and also a Transaction ID associated with their purchase of the Specific Post/Page [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%txn_cid%% is simply set to the %%txn_id%% value; i.e., it is a duplicate of %%txn_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Transaction ID. See %%txn_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%amount%%</code> = The full Amount of the sale. If you offer something 100% free, this will be <code>0</code>.</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customers\'s IP Address, during checkout via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-sp-sale-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_sp_sale_notification_recipients" id="ws-plugin--s2member-sp-sale-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_sale_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_after_sp_sale_notifications", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_api_ops_page_during_left_sections_display_sp_refund_reversal_notifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_before_sp_refund_reversal_notifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Specific Post/Page ~ Refund/Reversal Notifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-sp-refund-reversal-notifications-section">'."\n";
				echo '<h3>Specific Post/Page ~ Refund/Reversal Notification URLs (optional)</h3>'."\n";
				echo '<p>If you use affiliate software, or have back-office routines that need to be notified whenever sales have been refunded or reversed <em>(i.e., charged back to you)</em>, you\'ll want to read this section. This is marked `Specific Post/Page`, because the URLs that you list below, will be notified each time a Refund or Reversal occurs, on a sale that provided access to a Specific Post/Page.</p>'."\n";
				echo '<p>This is one of only two Notifications that are sent for Specific Post/Page Access <em>(i.e., this one, and another above, for Sales)</em>. All of the other API Notifications are designed for Membership Level Access'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' and Independent Custom Capabilities').'. None of the other API Notifications will ever be processed for Specific Post/Page Access. If you intend to respond to events related to Specific Post/Page Access, you MUST use one of the two API Notifications specifically geared to Post/Page Access.</p>'."\n";
				echo '<p>Please note, this feature is not to be confused with the PayPal IPN service. PayPal IPN <em>(and other service integrations)</em> are already built into s2Member. They remain active at all times. These Refund/Reversal Notifications are an added layer of functionality, and they are completely optional.</p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/building-an-api-notification-handler/" target="_blank" rel="external">Building An API Notification Handler</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_during_sp_refund_reversal_notifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-sp-ref-rev-notification-urls">'."\n";
				echo 'Specific Post/Page ~ Refund/Reversal Notification URLs:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'You can input multiple Notification URLs by inserting one per line.<br />'."\n";
				echo '<textarea name="ws_plugin__s2member_sp_ref_rev_notification_urls" id="ws-plugin--s2member-sp-ref-rev-notification-urls" rows="3" wrap="off">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_ref_rev_notification_urls"]).'</textarea><br />'."\n";
				echo 'Specific Post/Page ~ Refund/Reversal Notifications take place silently behind-the-scenes, using an HTTP connection.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%parent_txn_id%%</code> = The Parent Transaction ID, associated with the original payment being refunded/reversed.</li>'."\n";
				echo '<li><code>%%parent_txn_cid%%</code> = Applicable only with Stripe integration. This is the Customer\'s ID in Stripe. Each Stripe Customer has this Customer ID; and also a Transaction ID associated with their purchase of the Specific Post/Page [ <a href="#" onclick="alert(\'Applicable only when you integrate s2Member with Stripe. In all other cases, the %%parent_txn_cid%% is simply set to the %%parent_txn_id%% value; i.e., it is a duplicate of %%parent_txn_id%% when running anything other than Stripe.\\n\\nEach Stripe Customer has a Customer ID; and also a Transaction ID. See %%parent_txn_id%% for further details.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%-amount%%</code> = The Negative Amount of the payment, that was refunded or reversed back to the Customer.</li>'."\n";
				echo '<li><code>%%-fee%%</code> = The Negative Payment Gateway fee, that was refunded back to you as the Merchant/Seller.</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased access to a Specific Post/Page.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased access to a Specific Post/Page.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased access to a Specific Post/Page.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased access to a Specific Post/Page.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s original IP Address, during checkout via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number. Ex: <code><em>sp:13,24,36:72</em></code> (translates to: <code><em>sp:comma-delimited IDs:expiration hours</em></code>).</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
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

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-sp-ref-rev-notification-recipients">'."\n";
				echo 'Send An Email Transaction Log Of This Event?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_sp_ref_rev_notification_recipients" id="ws-plugin--s2member-sp-ref-rev-notification-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_ref_rev_notification_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"John" &lt;john@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_api_ops_page_during_left_sections_after_sp_refund_reversal_notifications", get_defined_vars());
			}

			do_action("ws_plugin__s2member_during_api_ops_page_after_left_sections", get_defined_vars());

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

new c_ws_plugin__s2member_menu_page_api_ops ();