<?php
/**
 * Menu page for the s2Member plugin (PayPal Options page).
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

if(!class_exists("c_ws_plugin__s2member_menu_page_paypal_ops"))
{
	/**
	 * Menu page for the s2Member plugin (PayPal Options page).
	 *
	 * @package s2Member\Menu_Pages
	 * @since 110531
	 */
	class c_ws_plugin__s2member_menu_page_paypal_ops
	{
		public function __construct()
		{
			echo '<div class="wrap ws-menu-page">'."\n";

			echo '<div class="ws-menu-page-toolbox">'."\n";
			c_ws_plugin__s2member_menu_pages_tb::display();
			echo '</div>'."\n";

			echo '<h2>PayPal Options</h2>'."\n";

			echo '<table class="ws-menu-page-table">'."\n";
			echo '<tbody class="ws-menu-page-table-tbody">'."\n";
			echo '<tr class="ws-menu-page-table-tr">'."\n";
			echo '<td class="ws-menu-page-table-l">'."\n";

			echo '<form method="post" name="ws_plugin__s2member_options_form" id="ws-plugin--s2member-options-form" autocomplete="off">'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_options_save" id="ws-plugin--s2member-options-save" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-options-save")).'" />'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_configured" id="ws-plugin--s2member-configured" value="1" />'."\n";

			do_action("ws_plugin__s2member_during_paypal_ops_page_before_left_sections", get_defined_vars());

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_paypal_account_details", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_paypal_account_details", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="PayPal Account Details">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-paypal-account-details-section">'."\n";
				echo '<a href="http://www.s2member.com/paypal" target="_blank"><img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/paypal-logo.png" class="ws-menu-page-right" style="width:125px; height:125px; border:0;" alt="." /></a>'."\n";
				echo '<h3>PayPal Account Details (required, if using PayPal)</h3>'."\n";
				echo '<p>s2Member integrates with <a href="http://www.s2member.com/paypal" target="_blank" rel="external">PayPal Payments Standard</a>—for businesses. You do not need a PayPal Pro account. You just need to upgrade your Personal PayPal account to a Business status, which is free. A PayPal account can be <a href="http://s2member.com/r/paypal-business-upgrade/" target="_blank" rel="external">upgraded</a> from a Personal account to a Business account by clicking the "Profile" link under your "My Account" tab, selecting "Personal Business Information", and then clicking the "Upgrade Your Account" button. <strong>See also:</strong> This KB article: <a href="http://s2member.com/kb-article/supported-paypal-account-types/" target="_blank" rel="external">PayPal Compatibility (Account Types)</a>.</p>'."\n";
				echo '<p><em><strong>PayPal API Credentials:</strong> Once you have a PayPal Business account, you\'ll need access to your <a href="http://s2member.com/r/paypal-profile-api-access/" target="_blank" rel="external">PayPal API Credentials</a>. Log into your PayPal account, and navigate to <strong>Profile → API Access (or → Request API Credentials)</strong>. From the available options, please choose "Request API Signature".</em></p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_account_details", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-merchant-id">'."\n";
				echo 'Your PayPal Merchant ID:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_paypal_merchant_id" id="ws-plugin--s2member-paypal-merchant-id" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_merchant_id"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → Secure Merchant ID</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-business">'."\n";
				echo 'Your PayPal EMail Address:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_paypal_business" id="ws-plugin--s2member-paypal-business" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_business"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → Email Accounts</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-api-username">'."\n";
				echo 'Your PayPal API Username:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_paypal_api_username" id="ws-plugin--s2member-paypal-api-username" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_api_username"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → API Access (or → Request API Credentials)</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-api-password">'."\n";
				echo 'Your PayPal API Password:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_paypal_api_password" id="ws-plugin--s2member-paypal-api-password" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_api_password"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → API Access (or → Request API Credentials)</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-api-signature">'."\n";
				echo 'Your PayPal API Signature:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_paypal_api_signature" id="ws-plugin--s2member-paypal-api-signature" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_api_signature"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → API Access (or  → "Request API Credentials")</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_account_detail_rows", get_defined_vars());

				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-paypal-sandbox">'."\n";
				echo 'Developer/Sandbox Testing?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="radio" name="ws_plugin__s2member_paypal_sandbox" id="ws-plugin--s2member-paypal-sandbox-0" value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-paypal-sandbox-0">No</label> &nbsp;&nbsp;&nbsp; <input type="radio" name="ws_plugin__s2member_paypal_sandbox" id="ws-plugin--s2member-paypal-sandbox-1" value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_sandbox"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-paypal-sandbox-1">Yes, enable support for Sandbox testing.</label><br />'."\n";
				echo '<em>Only enable this if you\'ve provided Sandbox credentials above.<br />This puts the API, IPN, PDT and Form/Button Generators all into Sandbox mode.<br />See: <a href="http://s2member.com/r/paypal-developers/" target="_blank" rel="external">PayPal Developers</a></em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-btn-encryption">'."\n";
				echo 'Enable Button Encryption?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="radio" name="ws_plugin__s2member_paypal_btn_encryption" id="ws-plugin--s2member-paypal-btn-encryption-0" value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_btn_encryption"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-paypal-btn-encryption-0">No</label> &nbsp;&nbsp;&nbsp; <input type="radio" name="ws_plugin__s2member_paypal_btn_encryption" id="ws-plugin--s2member-paypal-btn-encryption-1" value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_btn_encryption"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-paypal-btn-encryption-1">Yes, enable PayPal Button encryption.</label><br />'."\n";
				echo '<em>If enabled, all of your PayPal Button Shortcodes will produce <em>encrypted</em> PayPal Buttons. This improves security against fraudulent transactions. For extra security, you should update your PayPal account too, under: <strong>My Profile → Website Payment Preferences</strong>. You\'ll want to block all non-encrypted payments. <strong>Note:</strong> this will NOT work until you\'ve supplied s2Member with your PayPal Email Address, and also with your API Username/Password/Signature.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";

				if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site())
				{
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--s2member-gateway-debug-logs">'."\n";
					echo 'Enable Logging Routines?'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="radio" name="ws_plugin__s2member_gateway_debug_logs" id="ws-plugin--s2member-gateway-debug-logs-0" value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["gateway_debug_logs"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-gateway-debug-logs-0">No</label> &nbsp;&nbsp;&nbsp; <input type="radio" name="ws_plugin__s2member_gateway_debug_logs" id="ws-plugin--s2member-gateway-debug-logs-1" value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["gateway_debug_logs"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-gateway-debug-logs-1">Yes, enable debugging, with API, IPN &amp; Return Page logging.</label><br />'."\n";
					echo '<em>This enables API, IPN and Return Page logging. The log files are stored here: <code>'.esc_html(c_ws_plugin__s2member_utils_dirs::doc_root_path($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["logs_dir"])).'</code></em><br />'."\n";
					echo '<em class="ws-menu-page-hilite">If you have any trouble, please review your s2Member log files for problems. See: <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--s2member-logs")).'">Log Viewer</a></em>'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<div class="info" style="margin-bottom:0;">'."\n";
					echo '<p style="margin-top:0;"><span>We highly recommend that you enable logging during your initial testing phase. Logs produce lots of useful details that can help in debugging. Logs can help you find issues in your configuration and/or problems during payment processing. See: <a href="'.esc_attr(admin_url("/admin.php?page=ws-plugin--s2member-logs")).'">Log Files (Debug)</a>.</span></p>'."\n";
					echo '<p style="margin-bottom:0;"><span class="ws-menu-page-error">However, it is very important to disable logging once you go live. Log files may contain personally identifiable information, credit card numbers, secret API credentials, passwords and/or other sensitive information. We strongly suggest that logging be disabled on a live site (for security reasons).</span></p>'."\n";
					echo '</div>'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
				}
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p><em><strong>Sandbox Tip:</strong> If you\'re testing your site through a PayPal Sandbox account, please remember that Email Confirmations from s2Member will not be received after a test purchase. s2Member sends its Confirmation Emails to the PayPal Email Address of the Customer. Since PayPal Sandbox addresses are usually bogus (for testing), you will have to run live transactions before Email Confirmations from s2Member are received. That being said, all other s2Member functionality can be tested through a PayPal Sandbox account. Email Confirmations are the only hang-up.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_account_details_after_sandbox_tip", get_defined_vars());
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_paypal_account_details", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_paypal_payflow_account_details", c_ws_plugin__s2member_utils_conds::pro_is_installed(), get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_paypal_payflow_account_details", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Payflow Account Details">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-paypal-payflow-account-details-section">'."\n";
				echo '<a href="http://www.s2member.com/paypal" target="_blank"><img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/paypal-logo.png" class="ws-menu-page-right" style="width:125px; height:125px; border:0;" alt="." /></a>'."\n";
				echo '<h3>Payflow Account Details (required, if using Payflow)</h3>'."\n";
				echo '<p>Newer PayPal Pro accounts (i.e., PayPal Pro w/ Payflow Edition), come with the Payflow API for Recurring Billing service. If you have a newer PayPal Pro account, and you wish to integrate PayPal\'s Recurring Billing service with s2Member Pro-Forms, you will need to fill in the details here. Providing Payflow API Credentials below, will automatically put s2Member\'s Recurring Billing integration through Pro-Forms into Payflow mode. Just fill in the details below, and you\'re ready to generate Pro-Forms that charge customers on a recurring basis. s2Member will use the Payflow Edition API instead of the older PayPal Pro DPRP service; which is being slowly phased out in favor of Payflow Edition APIs.</p>'."\n";
				echo '<p><em><strong>Payflow API Credentials:</strong> Once you have a PayPal Pro account, you\'ll need access to your <a href="http://s2member.com/r/paypal-profile-api-access/" target="_blank" rel="external">Payflow API Credentials</a>. Log into your PayPal account, and navigate to <strong>Profile → API Access (or → Request API Credentials)</strong>. From the available options, please choose "Payflow / API Access". You will need the following credentials: Username, Password, Partner, and Vendor.</em></p>'."\n";
				echo '<p><em><strong>Important Note:</strong> Supplying Payflow API Credentials here does not mean you can bypass other areas of s2Member\'s configuration; i.e., please supply s2Member with all of your PayPal account details. Your PayPal Pro (Payflow Edition) account is configured here, but up above you will need to configure other Account Details also.</em></p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://s2member.com/kb-article/supported-paypal-account-types/" target="_blank" rel="external">PayPal Account Types</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_payflow_account_details", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-payflow-api-username">'."\n";
				echo 'Your Payflow API Username:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_paypal_payflow_api_username" id="ws-plugin--s2member-paypal-payflow-api-username" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_payflow_api_username"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → API Access (or → Request API Credentials) → Payflow API Access</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-payflow-api-password">'."\n";
				echo 'Your Payflow API Password:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_paypal_payflow_api_password" id="ws-plugin--s2member-paypal-payflow-api-password" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_payflow_api_password"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → API Access (or → Request API Credentials) → Payflow API Access</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-payflow-api-partner">'."\n";
				echo 'Your Payflow API Partner:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" name="ws_plugin__s2member_paypal_payflow_api_partner" id="ws-plugin--s2member-paypal-payflow-api-partner" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_payflow_api_partner"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → API Access (or → Request API Credentials) → Payflow API Access</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-payflow-api-vendor">'."\n";
				echo 'Your Payflow API Vendor:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" name="ws_plugin__s2member_paypal_payflow_api_vendor" id="ws-plugin--s2member-paypal-payflow-api-vendor" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_payflow_api_vendor"]).'" /><br />'."\n";
				echo 'At PayPal, see: <strong>Profile → API Access (or → Request API Credentials) → Payflow API Access</strong>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_payflow_account_detail_rows", get_defined_vars());
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_paypal_payflow_account_details", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_paypal_ipn", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_paypal_ipn", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="PayPal IPN Integration">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-paypal-ipn-section">'."\n";
				echo '<h3>PayPal IPN / Instant Payment Notifications (required, please enable)</h3>'."\n";
				echo '<p>Log into your PayPal account and navigate to this section:<br /><strong>Account Profile → Instant Payment Notification Preferences</strong></p>'."\n";
				echo '<p>Edit your IPN settings &amp; turn IPN Notifications: <strong><code>On</code></strong></p>'."\n";
				echo '<p>You\'ll need your IPN URL, which is:<br /><code>'.esc_html(home_url("/?s2member_paypal_notify=1")).'</code></p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_ipn", get_defined_vars());

				echo '<h3 style="margin:0;">More Information (<a href="#" onclick="jQuery(\'div#ws-plugin--s2member-paypal-ipn-details\').toggle(); return false;" class="ws-dotted-link">click here</a>)</h3>'."\n";
				echo '<div id="ws-plugin--s2member-paypal-ipn-details" style="margin-top:10px; display:none;">'."\n";
				echo '<p><em><strong>Quick Tip:</strong> In addition to the <a href="http://s2member.com/r/paypal-ipn-setup/" target="_blank" rel="external">default IPN settings inside your PayPal account</a>, the IPN URL is also set on a per-transaction basis by the special PayPal Button Code that s2Member provides you with. In other words, if you have multiple sites operating on one PayPal account, that\'s OK. s2Member dynamically sets the IPN URL for each transaction. The result is that the IPN URL configured from within your PayPal account, becomes the default, which is then overwritten on a per-transaction basis. In fact, PayPal recently updated their system to support IPN URL preservation. One PayPal account can handle multiple sites, all using different IPN URLs.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_ipn_after_quick_tip", get_defined_vars());
				echo '<p><em><strong>IPN Communications:</strong> You\'ll be happy to know that s2Member handles cancellations, expirations, failed payments, terminations (e.g., refunds &amp; chargebacks) for you automatically. If you log into your PayPal account and cancel a Member\'s Subscription, or, if the Member logs into their PayPal account and cancels their own Subscription, s2Member will be notified of these important changes and react accordingly through the PayPal IPN service that runs silently behind-the-scene. The PayPal IPN service will notify s2Member whenever a Member\'s payments have been failing, and/or whenever a Member\'s Subscription has expired for any reason. Even refunds &amp; chargeback reversals are supported through the IPN service. If you issue a refund to an unhappy Customer through PayPal, s2Member will be notified, and the account for that Customer will either be demoted to a Free Subscriber, or deleted automatically (based on your configuration). The communication from PayPal → s2Member is seamless.</em></p>'."\n";
				echo '</div>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3 style="margin:0;">IPN w/ Proxy Key (<a href="#" onclick="jQuery(\'div#ws-plugin--s2member-paypal-ipn-proxy-details\').toggle(); return false;" class="ws-dotted-link">optional, for 3rd-party integrations</a>)</h3>'."\n";
				echo '<div id="ws-plugin--s2member-paypal-ipn-proxy-details" style="margin-top:10px; display:none;">'."\n";
				echo '<p>If you\'re using a 3rd-party application that needs to POST simulated IPN transactions to your s2Member installation, you can use this alternate IPN URL, which includes a Proxy Key. This encrypted Proxy Key verifies incoming data being received by s2Member\'s IPN processor. You can change <em>[proxy-gateway]</em> to whatever you like. The <em>[proxy-gateway]</em> value is required. It will be stored by s2Member as the Customer\'s Paid Subscr. Gateway. Your [proxy-gateway] value will also be reflected in s2Member\'s IPN log.</p>'."\n";
				echo '<input type="text" autocomplete="off" value="'.format_to_edit(home_url("/?s2member_paypal_notify=1&s2member_paypal_proxy=[proxy-gateway]&s2member_paypal_proxy_verification=".urlencode(c_ws_plugin__s2member_paypal_utilities::paypal_proxy_key_gen()))).'" style="width:99%;" />'."\n";
				echo '<p><em>Any 3rd-party application that is sending IPN transactions to your s2Member installation must include the <code>custom</code> POST variable, and that variable must always start with your installation domain (i.e., custom=<code>'.esc_html($_SERVER["HTTP_HOST"]).'</code>). In addition, the <code>item_number</code> variable must always match a format that s2Member looks for. Generally speaking, the <code>item_number</code> should be <code>1, 2, 3, or 4</code>, indicating a specific s2Member Level #. However, s2Member also uses some advanced formats in this field. Just to be sure, we suggest creating a PayPal Button with the s2Member Button Generator, and then taking a look at the Full Button Code to see how s2Member expects <code>item_number</code> to be formatted. Other than the aforementioned exceptions, all other POST variables should follow PayPal standards. Please see: <a href="http://www.s2member.com/paypal-ipn-pdt-vars" target="_blank" rel="external">PayPal\'s IPN/PDT reference guide</a> for full documentation.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_ipn_after_proxy", get_defined_vars());
				echo '</div>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_paypal_ipn", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_paypal_pdt", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_paypal_pdt", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="PayPal PDT/Auto-Return Integration">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-paypal-pdt-section">'."\n";
				echo '<h3>PayPal PDT Identity Token (required, please enable)</h3>'."\n";
				echo '<p>Log into your PayPal account and navigate to this section:<br /><strong>Account Profile → Website Payment Preferences</strong></p>'."\n";
				echo '<p>Turn the Auto-Return feature: <strong><code>On</code></strong></p>'."\n";
				echo '<p>You\'ll need your <a href="'.esc_attr(home_url("/?s2member_paypal_return=1&s2member_paypal_proxy=paypal&s2member_paypal_proxy_use=x-preview")).'" target="_blank" rel="external">Auto-Return URL</a>, which is:<br /><code>'.esc_html(home_url("/?s2member_paypal_return=1")).'</code></p>'."\n";
				echo '<p>You must also enable PDT (Payment Data Transfer): <strong><code>On</code></strong><br /><em>You\'ll be issued an Identity Token that you can enter below.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_pdt", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-paypal-identity-token">'."\n";
				echo 'PayPal PDT Identity Token:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_paypal_identity_token" id="ws-plugin--s2member-paypal-identity-token" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["paypal_identity_token"]).'" /><br />'."\n";
				echo 'Your PDT Identity Token will appear under <strong>Profile → Website Payment Preferences</strong> in your PayPal account.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3 style="margin:0;">More Information (<a href="#" onclick="jQuery(\'div#ws-plugin--s2member-paypal-pdt-details\').toggle(); return false;" class="ws-dotted-link">click here</a>)</h3>'."\n";
				echo '<div id="ws-plugin--s2member-paypal-pdt-details" style="margin-top:10px; display:none;">'."\n";
				echo '<p><em><strong>Quick Tip:</strong> In addition to the <a href="http://s2member.com/r/paypal-pdt-setup/" target="_blank" rel="external">default Auto-Return/PDT configuration inside your PayPal account</a>, the Auto-Return URL is also set on a per-transaction basis from within the special PayPal Button Code that s2Member provides you with. In other words, if you have multiple sites operating on one PayPal account, that\'s OK. s2Member sets the Auto-Return URL (dynamically) for each transaction. The result is that the Auto-Return URL configured from within your PayPal account becomes the default, which is then overwritten on a per-transaction basis by the s2Member software.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_pdt_after_quick_tip", get_defined_vars());
				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_paypal_pdt_after_more_info", get_defined_vars());

				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_paypal_pdt", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_signup_confirmation_email", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_signup_confirmation_email", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Signup Confirmation Email (Standard)">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-signup-confirmation-email-section">'."\n";
				echo '<h3>Signup Confirmation Email (required, but the default works fine)</h3>'."\n";
				echo '<p>This email is sent to new Customers after they return from a successful signup at PayPal. The <strong>primary</strong> purpose of this email is to provide the Customer with instructions, along with a link to register a Username for their Membership. You may also customize this further by providing details that are specifically geared to your site.</p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_signup_confirmation_email", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-signup-email-recipients">'."\n";
				echo 'Signup Confirmation Recipients:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_signup_email_recipients" id="ws-plugin--s2member-signup-email-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["signup_email_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"%%full_name%%" &lt;%%payer_email%%&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-signup-email-subject">'."\n";
				echo 'Signup Confirmation Email Subject:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_signup_email_subject" id="ws-plugin--s2member-signup-email-subject" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["signup_email_subject"]).'" /><br />'."\n";
				echo 'Subject Line used in the email sent to a Customer after a successful signup has occurred through PayPal.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-signup-email-message">'."\n";
				echo 'Signup Confirmation Email Message:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_signup_email_message" id="ws-plugin--s2member-signup-email-message" rows="10">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["signup_email_message"]).'</textarea><br />'."\n";
				echo 'Message Body used in the email sent to a Customer after a successful signup has occurred through PayPal.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%registration_url%%</code> = The full URL (generated by s2Member) where the Customer can get registered.</li>'."\n";
				echo '<li><code>%%subscr_id%%</code> = The PayPal Subscription ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the Transaction ID for the purchase. PayPal does not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there is only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%initial%%</code> = The Initial Fee charged during signup. If you offered a 100% Free Trial, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This will always represent the amount of money the Customer spent, whenever they initially signed up, no matter what. If a Customer signs up, under the terms of a 100% Free Trial Period, this will be 0.\'); return false;">?</a> ]</li>'."\n";
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
				echo '<li><code>%%initial_cycle%%</code> = This is the <code>%%initial_term%%</code> from above, converted to a cycle representation of: <code><em>X days/weeks/months/years</em></code>.</li>'."\n";
				echo '<li><code>%%regular_term%%</code> = This is the term length of the Regular Period. This will be a numeric value, followed by a space, then a single letter. [ <a href="#" onclick="alert(\'Here are some examples:\\n\\n%%regular_term%% = 1 D (this means 1 Day)\\n%%regular_term%% = 1 W (this means 1 Week)\\n%%regular_term%% = 1 M (this means 1 Month)\\n%%regular_term%% = 1 Y (this means 1 Year)\\n%%regular_term%% = 1 L (this means 1 Lifetime)\\n\\nThe Regular Term is usually recurring. So the Regular Term value represents the period (or duration) of each recurring period. If %%recurring%% = 0, then the Regular Term only applies once, because it is not recurring. So if it is not recurring, the value of %%regular_term%% simply represents how long their Membership privileges are going to last after the %%initial_term%% has expired, if there was an Initial Term. The value of this variable ( %%regular_term%% ) will never be empty, it will always be at least: 1 D, meaning 1 day. No exceptions.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular_cycle%%</code> = This is the <code>%%regular_term%%</code> from above, converted to a cycle representation of: <code><em>[every] X days/weeks/months/years—OR daily, weekly, bi-weekly, monthly, bi-monthly, quarterly, yearly, or lifetime</em></code>. This is a very useful Replacment Code. Its value is dynamic; depending on term length, recurring status, and period/term lengths configured.</li>'."\n";
				echo '<li><code>%%recurring/regular_cycle%%</code> = Example (<code>14.95 / Monthly</code>), or ... (<code>0 / non-recurring</code>); depending on the value of <code>%%recurring%%</code>.</li>'."\n";
				echo '</ul>'."\n";

				echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
				echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
				echo '</ul>'."\n";
				echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
				echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
				echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ?
					'<div class="ws-menu-page-hr"></div>'."\n".
					'<p style="margin:0;"><strong>PHP Code:</strong> It is also possible to use PHP tags—optional (for developers). If you use PHP tags, please run a test email with <code>&lt;?php print_r(get_defined_vars()); ?&gt;</code>. This will give you a full list of all PHP variables available to you in this email. The <code>$paypal</code> variable is the most important one. It contains all of the <code>$_POST</code> variables received from PayPal\'s IPN service—or from an s2Member Pro-Form integration (e.g., <code>$paypal["item_number"]</code>, <code>$paypal["item_name"]</code>, etc). Please note that all Replacement Codes will be parsed first, and then any PHP tags that you\'ve included. Also, please remember that emails are sent in plain text format.</p>'."\n"
					: '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_signup_confirmation_email", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_modification_confirmation_email", c_ws_plugin__s2member_utils_conds::pro_is_installed(), get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_modification_confirmation_email", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Modification Confirmation Email '.((c_ws_plugin__s2member_utils_conds::pro_is_installed()) ? '(Standard/Pro-Form)' : '(Standard)').'">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-modification-confirmation-email-section">'."\n";
				echo '<h3>Modification Confirmation Email (required, but the default works fine)</h3>'."\n";
				echo '<p>This email is sent to existing Users after they complete an upgrade/downgrade (if and when you make this possible). For instance, if a Free Subscriber upgrades to a paid Membership Level, s2Member considers this a Modification (NOT a Signup; a Signup is associated only with someone completely new). The <strong>primary</strong> purpose of this email is to provide the Customer with a confirmation that their account was updated. You may also customize this further by providing details that are specifically geared to your site.</p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_modification_confirmation_email", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-modification-email-recipients">'."\n";
				echo 'Modification Confirmation Recipients:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_modification_email_recipients" id="ws-plugin--s2member-modification-email-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["modification_email_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"%%full_name%%" &lt;%%payer_email%%&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-modification-email-subject">'."\n";
				echo 'Modification Confirmation Email Subject:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_modification_email_subject" id="ws-plugin--s2member-modification-email-subject" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["modification_email_subject"]).'" /><br />'."\n";
				echo 'Subject Line used in the email sent to a Customer after a successful modification has occurred through PayPal.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-modification-email-message">'."\n";
				echo 'Modification Confirmation Email Message:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_modification_email_message" id="ws-plugin--s2member-modification-email-message" rows="10">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["modification_email_message"]).'</textarea><br />'."\n";
				echo 'Message Body used in the email sent to a Customer after a successful modification has occurred through PayPal.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%subscr_id%%</code> = The PayPal Subscription ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'There is one exception. If you are selling Lifetime or Fixed-Term (non-recurring) access, using Buy Now functionality; the %%subscr_id%% is actually set to the Transaction ID for the purchase. PayPal does not provide a specific Subscription ID for Buy Now purchases. Since Lifetime &amp; Fixed-Term Subscriptions are NOT recurring (i.e., there is only ONE payment), using the Transaction ID as the Subscription ID is a graceful way to deal with this minor conflict.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%subscr_baid%%</code> = Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. This is the Subscription\'s Billing Agreement ID, which remains constant throughout any &amp; all future payments. [ <a href="#" onclick="alert(\'Applicable only with PayPal Pro (Payflow Edition); and only for Express Checkout transactions that require a Billing Agreement. In all other cases, the %%subscr_baid%% is simply set to the %%subscr_id%% value; i.e., it is a duplicate of %%subscr_id%% in most cases.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%initial%%</code> = The Initial Fee. If you offered a 100% Free Trial, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This will always represent the amount of money the Customer spent when they completed checkout, no matter what. Even if that amount is 0. If a Customer upgrades/downgrades under the terms of a 100% Free Trial Period, this will be 0.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular%%</code> = The Regular Amount of the Subscription. If you offer something 100% free, this will be <code>0</code>. [ <a href="#" onclick="alert(\'This is how much the Subscription costs after an Initial Period expires. If you did NOT offer an Initial Period at a different price, %%initial%% and %%regular%% will be equal to the same thing.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%recurring%%</code> = This is the amount that will be charged on a recurring basis, or <code>0</code> if non-recurring. [ <a href="#" onclick="alert(\'If Recurring Payments have not been required, this will be equal to 0. That being said, %%regular%% &amp; %%recurring%% are usually the same value. This variable can be used in two different ways. You can use it to determine what the Regular Recurring Rate is, or to determine whether the Subscription will recur or not. If it is going to recur, %%recurring%% will be > 0.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased the Membership Subscription.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <code><em>level:custom_capabilities:fixed term</em></code>) that the Subscription is for.</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
				echo '<li><code>%%initial_term%%</code> = This is the term length of the Initial Period. This will be a numeric value, followed by a space, then a single letter. [ <a href="#" onclick="alert(\'Here are some examples:\\n\\n%%initial_term%% = 1 D (this means 1 Day)\\n%%initial_term%% = 1 W (this means 1 Week)\\n%%initial_term%% = 1 M (this means 1 Month)\\n%%initial_term%% = 1 Y (this means 1 Year)\\n\\nThe Initial Period never recurs, so this only lasts for the term length specified, then it is over.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%initial_cycle%%</code> = This is the <code>%%initial_term%%</code> from above, converted to a cycle representation of: <code><em>X days/weeks/months/years</em></code>.</li>'."\n";
				echo '<li><code>%%regular_term%%</code> = This is the term length of the Regular Period. This will be a numeric value, followed by a space, then a single letter. [ <a href="#" onclick="alert(\'Here are some examples:\\n\\n%%regular_term%% = 1 D (this means 1 Day)\\n%%regular_term%% = 1 W (this means 1 Week)\\n%%regular_term%% = 1 M (this means 1 Month)\\n%%regular_term%% = 1 Y (this means 1 Year)\\n%%regular_term%% = 1 L (this means 1 Lifetime)\\n\\nThe Regular Term is usually recurring. So the Regular Term value represents the period (or duration) of each recurring period. If %%recurring%% = 0, then the Regular Term only applies once, because it is not recurring. So if it is not recurring, the value of %%regular_term%% simply represents how long their Membership privileges are going to last after the %%initial_term%% has expired, if there was an Initial Term. The value of this variable ( %%regular_term%% ) will never be empty, it will always be at least: 1 D, meaning 1 day. No exceptions.\'); return false;">?</a> ]</li>'."\n";
				echo '<li><code>%%regular_cycle%%</code> = This is the <code>%%regular_term%%</code> from above, converted to a cycle representation of: <code><em>[every] X days/weeks/months/years—OR daily, weekly, bi-weekly, monthly, bi-monthly, quarterly, yearly, or lifetime</em></code>. This is a very useful Replacment Code. Its value is dynamic; depending on term length, recurring status, and period/term lengths configured.</li>'."\n";
				echo '<li><code>%%recurring/regular_cycle%%</code> = Example (<code>14.95 / Monthly</code>), or ... (<code>0 / non-recurring</code>); depending on the value of <code>%%recurring%%</code>.</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name listed on their User account. This might be different than what is on file with PayPal.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name listed on their User account. This might be different than what is on file with PayPal.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name listed on their User account. This might be different than what is on file with PayPal.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address associated with their User account. This might be different than what is on file with PayPal.</li>'."\n";
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
				echo '<strong>Custom Registration/Profile Fields are also supported in this email:</strong>'."\n";
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

				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ?
					'<div class="ws-menu-page-hr"></div>'."\n".
					'<p style="margin:0;"><strong>PHP Code:</strong> It is also possible to use PHP tags—optional (for developers). If you use PHP tags, please run a test email with <code>&lt;?php print_r(get_defined_vars()); ?&gt;</code>. This will give you a full list of all PHP variables available to you in this email. The <code>$paypal</code> variable is the most important one. It contains all of the <code>$_POST</code> variables received from PayPal\'s IPN service—or from an s2Member Pro-Form integration (e.g., <code>$paypal["item_number"]</code>, <code>$paypal["item_name"]</code>, etc). Please note that all Replacement Codes will be parsed first, and then any PHP tags that you\'ve included. Also, please remember that emails are sent in plain text format.</p>'."\n"
					: '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_modification_confirmation_email", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_ccap_confirmation_email", c_ws_plugin__s2member_utils_conds::pro_is_installed(), get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_ccap_confirmation_email", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Capability Confirmation Email '.((c_ws_plugin__s2member_utils_conds::pro_is_installed()) ? '(Standard/Pro-Form)' : '(Standard)').'">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-ccap-confirmation-email-section">'."\n";
				echo '<h3>Capability Confirmation Email (required, but the default works fine)</h3>'."\n";
				echo '<p>This email is sent to existing Users after they complete a Buy Now purchase for one or more Custom Capabilities (if and when you make this possible); see: <strong>Dashboard → s2Member → PayPal Buttons/Forms → Capability (Buy Now)</strong>. The <strong>primary</strong> purpose of this email is to provide the Customer with a confirmation that their account was updated. You may also customize this further by providing details that are specifically geared to your site.</p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_ccap_confirmation_email", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-ccap-email-recipients">'."\n";
				echo 'Capability Confirmation Recipients:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_ccap_email_recipients" id="ws-plugin--s2member-ccap-email-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["ccap_email_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"%%full_name%%" &lt;%%payer_email%%&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-ccap-email-subject">'."\n";
				echo 'Capability Confirmation Email Subject:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_ccap_email_subject" id="ws-plugin--s2member-ccap-email-subject" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["ccap_email_subject"]).'" /><br />'."\n";
				echo 'Subject Line used in the email sent to a Customer after a purchase is completed through PayPal.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-ccap-email-message">'."\n";
				echo 'Capability Confirmation Email Message:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_ccap_email_message" id="ws-plugin--s2member-ccap-email-message" rows="10">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["ccap_email_message"]).'</textarea><br />'."\n";
				echo 'Message Body used in the email sent to a Customer after a purchase is completed through PayPal.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%txn_id%%</code> = The PayPal Transaction ID. PayPal assigns a unique identifier for every purchase.</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%amount%%</code> = The full Amount that you charged for Custom Capability access.</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who completed the purchase.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who completed the purchase.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who completed the purchase.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who completed the purchase.</li>'."\n";
				echo '<li><code>%%item_number%%</code> = The Item Number (colon separated <code><em>*:custom_capabilities</em></code>); where <code>custom_capabilities</code> is a comma-delimited list of the Custom Capabilities they purchased.</li>'."\n";
				echo '<li><code>%%item_name%%</code> = The Item Name (as provided by the <code>desc=""</code> attribute in your Shortcode, which briefly describes the Item Number).</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name listed on their User account. This might be different than what is on file with PayPal.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name listed on their User account. This might be different than what is on file with PayPal.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name listed on their User account. This might be different than what is on file with PayPal.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address associated with their User account. This might be different than what is on file with PayPal.</li>'."\n";
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
				echo '<strong>Custom Registration/Profile Fields are also supported in this email:</strong>'."\n";
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

				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ?
					'<div class="ws-menu-page-hr"></div>'."\n".
					'<p style="margin:0;"><strong>PHP Code:</strong> It is also possible to use PHP tags—optional (for developers). If you use PHP tags, please run a test email with <code>&lt;?php print_r(get_defined_vars()); ?&gt;</code>. This will give you a full list of all PHP variables available to you in this email. The <code>$paypal</code> variable is the most important one. It contains all of the <code>$_POST</code> variables received from PayPal\'s IPN service—or from an s2Member Pro-Form integration (e.g., <code>$paypal["item_number"]</code>, <code>$paypal["item_name"]</code>, etc). Please note that all Replacement Codes will be parsed first, and then any PHP tags that you\'ve included. Also, please remember that emails are sent in plain text format.</p>'."\n"
					: '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_ccap_confirmation_email", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_sp_confirmation_email", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_sp_confirmation_email", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Specific Post/Page Confirmation Email (Standard)">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-sp-confirmation-email-section">'."\n";
				echo '<h3>Specific Post/Page Confirmation Email (required, but the default works fine)</h3>'."\n";
				echo '<p>This email is sent to new Customers after they return from a successful purchase at PayPal, for Specific Post/Page Access. (see: <strong>s2Member → Restriction Options → Specific Post/Page Access</strong>). This is NOT used for Membership sales, only for Specific Post/Page Access. The <strong>primary</strong> purpose of this email is to provide the Customer with instructions, along with a link to access the Specific Post/Page they\'ve purchased access to. If you\'ve created a Specific Post/Page Package (with multiple Posts/Pages bundled together into one transaction), this ONE link (<code>%%sp_access_url%%</code>) will automatically authenticate them for access to ALL of the Posts/Pages included in their transaction. You may customize this email further by providing details that are specifically geared to your site.</p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_sp_confirmation_email", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-sp-email-recipients">'."\n";
				echo 'Specific Post/Page Confirmation Recipients:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_sp_email_recipients" id="ws-plugin--s2member-sp-email-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_email_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"%%full_name%%" &lt;%%payer_email%%&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-sp-email-subject">'."\n";
				echo 'Specific Post/Page Confirmation Email Subject:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_sp_email_subject" id="ws-plugin--s2member-sp-email-subject" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_email_subject"]).'" /><br />'."\n";
				echo 'Subject Line used in the email sent to a Customer after a successful purchase has occurred through PayPal, for Specific Post/Page Access.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-sp-email-message">'."\n";
				echo 'Specific Post/Page Confirmation Email Message:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_sp_email_message" id="ws-plugin--s2member-sp-email-message" rows="10">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_email_message"]).'</textarea><br />'."\n";
				echo 'Message Body used in the email sent to a Customer after a successful purchase has occurred through PayPal, for Specific Post/Page Access.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul class="ws-menu-page-li-margins">'."\n";
				echo '<li><code>%%sp_access_url%%</code> = The full URL (generated by s2Member) where the Customer can gain access.</li>'."\n";
				echo '<li><code>%%sp_access_exp%%</code> = Human readable expiration for <code>%%sp_access_url%%</code>. Ex: <em>(link expires in <code>%%sp_access_exp%%</code>)</em>.</li>'."\n";
				echo '<li><code>%%txn_id%%</code> = The PayPal Transaction ID. PayPal assigns a unique identifier for every purchase.</li>'."\n";
				echo '<li><code>%%currency%%</code> = Three-character currency code (uppercase); e.g., <code>USD</code></li>'."\n";
				echo '<li><code>%%currency_symbol%%</code> = Currency code symbol; e.g., <code>$</code></li>'."\n";
				echo '<li><code>%%amount%%</code> = The full Amount that you charged for Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%first_name%%</code> = The First Name of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%last_name%%</code> = The Last Name of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%full_name%%</code> = The Full Name (First &amp; Last) of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%payer_email%%</code> = The Email Address of the Customer who purchased Specific Post/Page Access.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The Customer\'s IP Address, detected during checkout via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
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

				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ?
					'<div class="ws-menu-page-hr"></div>'."\n".
					'<p style="margin:0;"><strong>PHP Code:</strong> It is also possible to use PHP tags—optional (for developers). If you use PHP tags, please run a test email with <code>&lt;?php print_r(get_defined_vars()); ?&gt;</code>. This will give you a full list of all PHP variables available to you in this email. The <code>$paypal</code> variable is the most important one. It contains all of the <code>$_POST</code> variables received from PayPal\'s IPN service—or from an s2Member Pro-Form integration (e.g., <code>$paypal["item_number"]</code>, <code>$paypal["item_name"]</code>, etc). Please note that all Replacement Codes will be parsed first, and then any PHP tags that you\'ve included. Also, please remember that emails are sent in plain text format.</p>'."\n"
					: '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_sp_confirmation_email", get_defined_vars());
			}

			if(apply_filters("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_display_eot_behavior", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_before_eot_behavior", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Automatic EOT Behavior">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-eot-behavior-section">'."\n";
				echo '<h3>PayPal EOT Behavior (required, please choose)</h3>'."\n";
				echo '<p>EOT = End Of Term. By default, s2Member will demote a paid Member to a Free Subscriber whenever their Subscription term has ended (i.e., expired), been cancelled, refunded, charged back to you, etc. s2Member demotes them to a Free Subscriber, so they will no longer have Member Level Access to your site. However, in some cases, you may prefer to have Customer accounts deleted completely, instead of just being demoted. This is where you choose which method works best for your site. If you don\'t want s2Member to take ANY action at all, you can disable s2Member\'s EOT System temporarily, or even completely. There are also a few other configurable options here, so please read carefully. These options are all very important.</p>'."\n";
				echo '<p><strong>PayPal IPNs:</strong> The PayPal IPN service will notify s2Member whenever a Member\'s payments have been failing, and/or whenever a Member\'s Subscription has expired for any reason. Even refunds &amp; chargeback reversals are supported through the IPN service. For example, if you issue a refund to an unhappy Customer through PayPal, s2Member will eventually be notified, and the account for that Customer will either be demoted to a Free Subscriber, or deleted automatically (based on your configuration). The communication from PayPal → s2Member is seamless.</p>'."\n";
				echo '<p><em><strong>Some Hairy Details:</strong> There might be times whenever you notice that a Member\'s Subscription has been cancelled through PayPal... but, s2Member continues allowing the User  access to your site as a paid Member. Please don\'t be confused by this... in 99.9% of these cases, the reason for this is legitimate. s2Member will only remove the User\'s Membership privileges when an EOT (End Of Term) is processed, a refund occurs, a chargeback occurs, or when a cancellation occurs - which would later result in a delayed Auto-EOT by s2Member.</em></p>'."\n";
				echo '<p><em>s2Member will not process an EOT until the User has completely used up the time they paid for. In other words, if a User signs up for a monthly Subscription on Jan 1st, and then cancels their Subscription on Jan 15th; technically, they should still be allowed to access the site for another 15 days, and then on Feb 1st, the time they paid for has completely elapsed. At that time, s2Member will remove their Membership privileges; by either demoting them to a Free Subscriber, or deleting their account from the system (based on your configuration). s2Member also calculates one extra day (24 hours) into its equation, just to make sure access is not removed sooner than a Customer might expect.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_during_eot_behavior", get_defined_vars());

				echo '<p id="ws-plugin--s2member-auto-eot-system-enabled-via-cron"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["auto_eot_system_enabled"] == 2 && (!function_exists("wp_cron") || !wp_get_schedule("ws_plugin__s2member_auto_eot_system__schedule"))) ? '' : ' style="display:none;"').'>If you\'d like to run s2Member\'s Auto-EOT System through a more traditional Cron Job; instead of through <code>WP-Cron</code>, you will need to configure a Cron Job through your server control panel; provided by your hosting company. Set the Cron Job to run <code>once about every 10 minutes to an hour</code>. You\'ll want to configure an HTTP Cron Job that loads this URL:<br /><code>'.esc_html(home_url("/?s2member_auto_eot_system_via_cron=1")).'</code></p>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-auto-eot-system-enabled">'."\n";
				echo 'Enable s2Member\'s Auto-EOT System?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_auto_eot_system_enabled" id="ws-plugin--s2member-auto-eot-system-enabled">'."\n";
				// Very advanced conditionals here. If the Auto-EOT System is NOT running, or NOT fully configured, this will indicate that no option is set - as sort of a built-in acknowledgment/warning in the UI panel.
				echo (($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["auto_eot_system_enabled"] == 1 && (!function_exists("wp_cron") || !wp_get_schedule("ws_plugin__s2member_auto_eot_system__schedule"))) || ($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["auto_eot_system_enabled"] == 2 && (function_exists("wp_cron") && wp_get_schedule("ws_plugin__s2member_auto_eot_system__schedule"))) || (!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["auto_eot_system_enabled"] && (function_exists("wp_cron") && wp_get_schedule("ws_plugin__s2member_auto_eot_system__schedule")))) ? '<option value=""></option>'."\n" : '';
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["auto_eot_system_enabled"] == 1 && function_exists("wp_cron") && wp_get_schedule("ws_plugin__s2member_auto_eot_system__schedule")) ? ' selected="selected"' : '').'>Yes (enable the Auto-EOT System through WP-Cron)</option>'."\n";
				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? '<option value="2"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["auto_eot_system_enabled"] == 2 && (!function_exists("wp_cron") || !wp_get_schedule("ws_plugin__s2member_auto_eot_system__schedule"))) ? ' selected="selected"' : '').'>Yes (but, I\'ll run it with my own Cron Job)</option>'."\n" : '';
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["auto_eot_system_enabled"] && (!function_exists("wp_cron") || !wp_get_schedule("ws_plugin__s2member_auto_eot_system__schedule"))) ? ' selected="selected"' : '').'>No (disable the Auto-EOT System)</option>'."\n";
				echo '</select><br />'."\n";
				echo 'Recommended setting: (<code>Yes / enable via WP-Cron</code>)'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-membership-eot-behavior">'."\n";
				echo 'Membership EOT Behavior (Demote or Delete)?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_membership_eot_behavior" id="ws-plugin--s2member-membership-eot-behavior">'."\n";
				echo '<option value="demote"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_eot_behavior"] === "demote") ? ' selected="selected"' : '').'>Demote (convert them to a Free Subscriber)</option>'."\n";
				echo '<option value="delete"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_eot_behavior"] === "delete") ? ' selected="selected"' : '').'>Delete (erase their account completely)</option>'."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-eots-remove-ccaps">'."\n";
				echo 'Membership EOTs also Remove all Custom Capabilities?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_eots_remove_ccaps" id="ws-plugin--s2member-eots-remove-ccaps">'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["eots_remove_ccaps"]) ? ' selected="selected"' : '').'>Yes (an EOT also results in the loss of any Custom Capabilities a User/Member may have)</option>'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["eots_remove_ccaps"]) ? ' selected="selected"' : '').'>No (an EOT has no impact on any Custom Capabilities a User/Member may have)</option>'."\n";
				echo '</select><br />'."\n";
				echo '<em>NOTE: If Refunds/Reversals trigger an Immediate EOT (see setting below); Custom Capabilities will always be removed when/if a Refund or Reversal occurs. In other words, this setting is ignored for Refunds/Reversals (IF they trigger an Immediate EOT—based on your configuration below). If you prefer to review all Refunds/Reversals for yourself, please choose that option below.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-eot-grace-time">'."\n";
				echo 'EOT Grace Time (in seconds):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_eot_grace_time" id="ws-plugin--s2member-eot-grace-time" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["eot_grace_time"]).'" /><br />'."\n";
				echo '<em>This is represented in seconds. For example, a value of: <code>86400</code> = 1 day. Your EOT Grace Time; is the amount of time you will offer as a grace period (if any). Most site owners will give customers an additional 24 hours of access; just to help avoid any negativity that may result from a customer losing access sooner than they might expect. You can disable EOT Grace Time by setting this to: <code>0</code>. Note: there is NO Grace Time applied when/if a Refund or Reversal occurs. If Refunds/Reversals trigger an Immediate EOT (see setting below); there is never any Grace Time applied in that scenario.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-triggers-immediate-eot">'."\n";
				echo 'Refunds/Partial Refunds/Reversals (trigger Immediate EOT)?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_triggers_immediate_eot" id="ws-plugin--s2member-triggers-immediate-eot">'."\n";
				echo '<option value="none"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["triggers_immediate_eot"] === "none") ? ' selected="selected"' : '').'>None (I\'ll review these events manually)</option>'."\n";
				echo '<option value="refunds"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["triggers_immediate_eot"] === "refunds") ? ' selected="selected"' : '').'>Full Refunds (full refunds only; ALWAYS trigger an Immediate EOT action)</option>'."\n";
				echo '<option value="reversals"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["triggers_immediate_eot"] === "reversals") ? ' selected="selected"' : '').'>Reversals (chargebacks only; ALWAYS trigger an Immediate EOT action)</option>'."\n";
				echo '<option value="refunds,reversals"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["triggers_immediate_eot"] === "refunds,reversals") ? ' selected="selected"' : '').'>Full Refunds, Reversals (these ALWAYS trigger an Immediate EOT action)</option>'."\n";
				echo '<option value="refunds,partial_refunds,reversals"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["triggers_immediate_eot"] === "refunds,partial_refunds,reversals") ? ' selected="selected"' : '').'>Full Refunds, Partial Refunds, Reversals (these ALWAYS trigger an Immediate EOT action)</option>'."\n";
				echo '</select><br />'."\n";
				echo '<em><strong>Note:</strong> s2Member is not equipped to detect partial refunds against multi-payment Subscriptions reliably. Therefore, all refunds processed against Subscriptions (of any kind) are considered <strong>Partial</strong> Refunds. Full refunds (in the eyes of s2Member) occur only against Buy Now transactions where it is easy for s2Member to see that the refund amount is &gt;= the original Buy Now purchase price (i.e., a Full Refund). <strong>Also Note:</strong> This setting (no matter what you choose) will NOT impact s2Member\'s internal API Notifications for Refund/Reversal events. <a href="#" onclick="alert(\'A Full or Partial Refund; and/or a Reversal Notification will ALWAYS be processed internally by s2Member, even if no action is taken by s2Member in accordance with your configuration here.\\n\\nIn this way, you\\\'ll have the full ability to listen for these events on your own (via API Notifications); if you prefer (optional). For more information, check your Dashboard under: `s2Member → API Notifications → Refunds/Reversals`.\'); return false;">Click here for details</a>.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-eot-time-ext-behavior">'."\n";
				echo 'Fixed-Term Extensions (Auto-Extend)?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_eot_time_ext_behavior" id="ws-plugin--s2member-eot-time-ext-behavior">'."\n";
				echo '<option value="extend"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["eot_time_ext_behavior"] === "extend") ? ' selected="selected"' : '').'>Yes (default, automatically extend any existing EOT Time)</option>'."\n";
				echo '<option value="reset"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["eot_time_ext_behavior"] === "reset") ? ' selected="selected"' : '').'>No (do NOT extend; s2Member should reset EOT Time completely)</option>'."\n";
				echo '</select><br />'."\n";
				echo '<em>This setting will only affect Buy Now transactions for fixed-term lengths. By default, s2Member will automatically extend any existing EOT Time that a Customer may have. For example, if I buy one year of access, and then I buy another year of access (before my first year is totally used up); I end up with everything I paid you for (now over 1 year of access) if this is set to <code>Yes</code>. If this was set to <code>No</code>, the EOT Time would be reset when I make the second purchase; leaving me with only 1 year of access, starting the date of my second purchase.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_paypal_ops_page_during_left_sections_after_eot_behavior", get_defined_vars());
			}

			do_action("ws_plugin__s2member_during_paypal_ops_page_after_left_sections", get_defined_vars());

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

new c_ws_plugin__s2member_menu_page_paypal_ops();