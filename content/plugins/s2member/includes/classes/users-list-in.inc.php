<?php
/**
 * Users list (inner processing routines).
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
 * @package s2Member\Users_List
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_users_list_in"))
{
	/**
	 * Users list (inner processing routines).
	 *
	 * @package s2Member\Users_List
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_users_list_in
	{
		/**
		 * Adds Custom Fields to the admin Profile editing page.
		 *
		 * @package s2Member\Users_List
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("edit_user_profile");``
		 * @attaches-to ``add_action("show_user_profile");``
		 *
		 * @param $user \WP_User Expects a `WP_User` object passed in by the Action Hook.
		 */
		public static function users_list_edit_cols($user = NULL)
		{
			global $current_site, $current_blog; // Multisite networks.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_before_users_list_edit_cols", get_defined_vars());
			unset($__refs, $__v);

			$current_user = is_user_logged_in() ? wp_get_current_user() : FALSE; // Current User.

			if(is_object($user) && !empty($user->ID) && ($user_id = $user->ID) && is_object($current_user) && !empty($current_user->ID))
			{
				$role  = c_ws_plugin__s2member_user_access::user_access_role($user); // This User's current WordPress Role.
				$level = c_ws_plugin__s2member_user_access::user_access_level($user); // User's Access Level for s2Member.

				if(current_user_can("edit_users") && (!is_multisite() || is_super_admin() || is_user_member_of_blog($user_id)))
				{
					echo '<div style="margin:25px 0 25px 0; height:1px; line-height:1px; background:#CCCCCC;"></div>'."\n";

					echo '<h3 style="position:relative;"><img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/large-icon.png" title="s2Member (a Membership management system for WordPress)" alt="" style="position:absolute; top:-15px; right:0; border:0;" />s2Member Configuration &amp; Profile Fields'.((is_multisite()) ? ' (for this Blog)' : '').'</h3>'."\n";

					echo '<table class="form-table">'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_before", get_defined_vars());
					unset($__refs, $__v);

					if(is_multisite() && is_super_admin()) // Super Admins can edit this.
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_before_originating_blog", get_defined_vars());
						unset($__refs, $__v);

						echo '<tr>'."\n";
						echo '<th><label for="ws-plugin--s2member-profile-s2member-originating-blog">Originating Blog ID#:</label> <a href="#" onclick="alert(\'On a Multisite Network, this is how s2Member keeps track of which Blog each User/Member originated from. So this ID#, is automatically associated with a Blog in your Network, matching the User\\\'s point of origin. ~ ONLY a Super Admin can modify this.\\n\\nOn a Multisite Blog Farm, the Originating Blog ID# for your own Customers, will ALWAYS be associated with your (Main Site). It is NOT likely that you\\\'ll need to modify this manually, but s2Member makes it available; just in case.\\n\\n*Tip* - If you add Users (and/or Blogs) with the `Super Admin` Network Administration panel inside WordPress, then you WILL need to set everything manually. s2Member does NOT tamper with automation routines whenever YOU (as a Super Administrator) are working in that area.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
						echo '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_profile_s2member_originating_blog" id="ws-plugin--s2member-profile-s2member-originating-blog" value="'.format_to_edit(get_user_meta($user_id, "s2member_originating_blog", TRUE)).'" class="regular-text" /></td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_after_originating_blog", get_defined_vars());
						unset($__refs, $__v);
					}
					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_before_subscr_gateway", get_defined_vars());
					unset($__refs, $__v);

					echo '<tr>'."\n";
					echo '<th><label for="ws-plugin--s2member-profile-s2member-subscr-gateway">Paid Subscr. Gateway:</label> <a href="#" onclick="alert(\'A Payment Gateway Code is associated with the Paid Subscr. ID below. A Paid Subscription ID (or a Buy Now Transaction ID) is only valid for paid Members. Also known as a Recurring Profile ID, a ClickBank Receipt #, a Google Order ID, an AliPay Trade No.\\n\\nThis will be filled automatically by s2Member. This field will be empty for Free Subscribers, and/or anyone who is NOT paying you. This field is only editable for Customer Service purposes; just in case you ever need to update the Paid Subscr. Gateway/ID manually.\\n\\nThe value of Paid Subscr. ID can be a PayPal Standard Subscription ID, or a PayPal Pro Recurring Profile ID, or a PayPal Transaction ID; depending on the type of sale. Your PayPal account will supply this information.\\n\\nIf you are using Stripe, please use the customer\\\'s Subscription ID (if applicable); else a Charge ID or an Invoice ID.\\n\\nIf you\\\'re using Google Wallet, use the Google Order ID. ClickBank provides a Receipt #, ccBill provides a Subscription ID, Authorize.Net provides a Subscription ID, and AliPay provides a Transaction ID.\\n\\nThe general rule is... IF there\\\'s a Subscription ID, use that! If there\\\'s NOT, use the Transaction ID.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
					echo '<td><select name="ws_plugin__s2member_profile_s2member_subscr_gateway" id="ws-plugin--s2member-profile-s2member-subscr-gateway" style="width:25em;"><option value=""></option>'."\n";
					foreach(apply_filters("ws_plugin__s2member_profile_s2member_subscr_gateways", array("paypal" => "PayPal (code: paypal)"), get_defined_vars()) as $gateway => $gateway_name)
						echo '<option value="'.esc_attr($gateway).'"'.(($gateway === get_user_option("s2member_subscr_gateway", $user_id)) ? ' selected="selected"' : '').'>'.esc_html($gateway_name).'</option>'."\n";
					echo '</select>'."\n";
					echo '</td>'."\n";
					echo '</tr>'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_after_subscr_gateway", get_defined_vars());
					unset($__refs, $__v);

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_before_subscr_id", get_defined_vars());
					unset($__refs, $__v);

					echo '<tr>'."\n";
					echo '<th><label for="ws-plugin--s2member-profile-s2member-subscr-id">Paid Subscr. ID:</label> <a href="#" onclick="alert(\'A Paid Subscription ID (or a Buy Now Transaction ID) is only valid for paid Members. Also known as a Recurring Profile ID, a ClickBank Receipt #, a Google Order ID, an AliPay Trade No.\\n\\nThis will be filled automatically by s2Member. This field will be empty for Free Subscribers, and/or anyone who is NOT paying you. This field is only editable for Customer Service purposes; just in case you ever need to update the Paid Subscr. Gateway/ID manually.\\n\\nThe value of Paid Subscr. ID can be a PayPal Standard Subscription ID, or a PayPal Pro Recurring Profile ID, or a PayPal Transaction ID; depending on the type of sale. Your PayPal account will supply this information.\\n\\nIf you are using Stripe, please use the customer\\\'s Subscription ID (if applicable); else a Charge ID or an Invoice ID.\\n\\nIf you\\\'re using Google Wallet, use the Google Order ID. ClickBank provides a Receipt #, ccBill provides a Subscription ID, Authorize.Net provides a Subscription ID, and AliPay provides a Transaction ID.\\n\\nThe general rule is... IF there\\\'s a Subscription ID, use that! If there\\\'s NOT, use the Transaction ID.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
					echo '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_profile_s2member_subscr_id" id="ws-plugin--s2member-profile-s2member-subscr-id" value="'.format_to_edit(get_user_option("s2member_subscr_id", $user_id)).'" class="regular-text" /></td>'."\n";
					echo '</tr>'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_after_subscr_id", get_defined_vars());
					unset($__refs, $__v);

					if(c_ws_plugin__s2member_utils_conds::pro_is_installed())
						if(in_array('stripe', $GLOBALS['WS_PLUGIN__']['s2member']['o']['pro_gateways_enabled']))
						{
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_before_subscr_cid", get_defined_vars());
							unset($__refs, $__v);

							echo '<tr>'."\n";
							echo '<th><label for="ws-plugin--s2member-profile-s2member-subscr-cid">Paid Subscr. CID:</label> <a href="#" onclick="alert(\'A Paid Subscription CID; i.e., a Customer\\\'s ID. Applicable only with Stripe integration. s2Member fills this in automatically. This is the Customer\\\'s ID in Stripe, which remains constant throughout any & all future payments. Each Stripe Customer has this Customer ID; and also a Subscription and/or Transaction ID.\\n\\nIn all other cases, the Paid Subscr. CID is simply set to the Paid Subscr. ID value; i.e., it is a duplicate of Paid Subscr. ID when running anything other than Stripe.\\n\\nThis field will be empty for Free Subscribers, and/or anyone who is NOT paying you. This field is only editable for Customer Service purposes; just in case you ever need to update the Paid Subscr. Gateway/ID/CID manually.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
							echo '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_profile_s2member_subscr_cid" id="ws-plugin--s2member-profile-s2member-subscr-cid" value="'.format_to_edit(get_user_option("s2member_subscr_cid", $user_id)).'" class="regular-text" /></td>'."\n";
							echo '</tr>'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_after_subscr_cid", get_defined_vars());
							unset($__refs, $__v);
						}
					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_before_custom", get_defined_vars());
					unset($__refs, $__v);

					echo '<tr>'."\n";
					echo '<th><label for="ws-plugin--s2member-profile-s2member-custom">Custom Value:</label> <a href="#" onclick="alert(\'A Paid Subscription is always associated with a Custom String that is passed through the custom=\\\'\\\''.c_ws_plugin__s2member_utils_strings::esc_js_sq(esc_attr($_SERVER["HTTP_HOST"]), 3).'\\\'\\\' attribute of your Shortcode. This Custom Value, MUST always start with your domain name. However, you can also pipe delimit additional values after your domain, if you need to.\\n\\nFor example:\n'.c_ws_plugin__s2member_utils_strings::esc_js_sq(esc_attr($_SERVER["HTTP_HOST"]), 3).'|cv1|cv2|cv3\'); return false;" tabindex="-1">[?]</a></th>'."\n";
					echo '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_profile_s2member_custom" id="ws-plugin--s2member-profile-s2member-custom" value="'.format_to_edit(get_user_option("s2member_custom", $user_id)).'" class="regular-text" /></td>'."\n";
					echo '</tr>'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_after_custom", get_defined_vars());
					unset($__refs, $__v);

					if(c_ws_plugin__s2member_utils_conds::pro_is_installed())
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_before_coupon_codes", get_defined_vars());
						unset($__refs, $__v);

						echo '<tr>'."\n";
						echo '<th><label for="ws-plugin--s2member-profile-s2member-coupon-codes">Coupon Code(s):</label> <a href="#" onclick="alert(\'This is a comma-delimited list of the Coupon Codes associated with this user; i.e., the Coupon Code(s) that have been used to complete checkout. s2Member updates this list automatically. This field is only editable for Customer Service purposes; i.e., just in case you ever need to update the list manually.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
						echo '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_profile_s2member_coupon_codes" id="ws-plugin--s2member-profile-s2member-coupon-codes" value="'.format_to_edit(implode(',', is_array($_user_coupon_codes = get_user_option("s2member_coupon_codes", $user_id)) ? $_user_coupon_codes : array())).'" class="regular-text" /></td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_after_coupon_codes", get_defined_vars());
						unset($__refs, $__v);
					}
					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_before_registration_ip", get_defined_vars());
					unset($__refs, $__v);

					echo '<tr>'."\n";
					echo '<th><label for="ws-plugin--s2member-profile-s2member-registration-ip">Registration IP:</label> <a href="#" onclick="alert(\'This is the IP Address the User had at the time of registration. If you don\\\'t know the User\\\'s IP Address, just leave this blank. If this is left empty, s2Member will make attempts in the future to grab the User\\\'s IP Address.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
					echo '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_profile_s2member_registration_ip" id="ws-plugin--s2member-profile-s2member-registration-ip" value="'.format_to_edit(get_user_option("s2member_registration_ip", $user_id)).'" class="regular-text" /></td>'."\n";
					echo '</tr>'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_after_registration_ip", get_defined_vars());
					unset($__refs, $__v);

					if(!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) // Will change once Custom Capabilities are compatible with a Blog Farm.
					{
						foreach($user->allcaps as $cap => $cap_enabled)
							if(preg_match("/^access_s2member_ccap_/", $cap))
								$ccaps[] = preg_replace("/^access_s2member_ccap_/", "", $cap);

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_before_ccaps", get_defined_vars());
						unset($__refs, $__v);

						echo '<tr>'."\n";
						echo '<th><label for="ws-plugin--s2member-profile-s2member-ccaps">Custom Capabilities:</label> <a href="#" onclick="alert(\'Optional. This is VERY advanced.\\nSee: s2Member → API Scripting → Custom Capabilities.'.((is_multisite()) ? '\\n\\nCustom Capabilities are assigned on a per-Blog basis. So having a set of Custom Capabilities for one Blog, and having NO Custom Capabilities on another Blog - is very common. This is how permissions are designed to work.' : '').'\'); return false;" tabindex="-1">[?]</a>'.((is_multisite()) ? '<br /><small>(for this Blog)</small>' : '').'</th>'."\n";
						echo '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_profile_s2member_ccaps" id="ws-plugin--s2member-profile-s2member-ccaps" value="'.format_to_edit(((!empty($ccaps)) ? implode(",", $ccaps) : "")).'" class="regular-text" onkeyup="if(this.value.match(/[^a-z_0-9,]/)) this.value = jQuery.trim (jQuery.trim (this.value).replace (/[ \-]/g, \'_\').replace (/[^a-z_0-9,]/gi, \'\').toLowerCase ());" /></td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_after_ccaps", get_defined_vars());
						unset($__refs, $__v);
					}
					if(!$user->has_cap("administrator")) // Do NOT present these details for Administrator accounts.
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_before_auto_eot_time", get_defined_vars());
						unset($__refs, $__v);

						echo '<tr>'."\n";
						$auto_eot_time = get_user_option("s2member_auto_eot_time", $user_id);
						$auto_eot_time = ($auto_eot_time) ? date("D M j, Y g:i a T", $auto_eot_time) : "";
						echo '<th><label for="ws-plugin--s2member-profile-s2member-auto-eot-time">Automatic EOT Time:</label> <a href="#" onclick="alert(\'EOT = End Of Term. ( i.e., Account Expiration / Termination. ).\\n\\nIf you leave this empty, s2Member will configure an EOT Time automatically, based on the paid Subscription associated with this account. In other words, if a paid Subscription expires, is cancelled, terminated, refunded, reversed, or charged back to you; s2Member will deal with the EOT automatically.\\n\\nThat being said, if you would rather take control over this, you can. If you type in a date manually, s2Member will obey the Auto-EOT Time that you\\\'ve given, no matter what. In other words, you can force certain Members to expire automatically, at a time that you specify. s2Member will obey.\\n\\nValid formats for Automatic EOT Time:\\n\\nmm/dd/yyyy\\nyyyy-mm-dd\\n+1 year\\n+2 weeks\\n+2 months\\n+10 minutes\\nnext thursday\\ntomorrow\\ntoday\\n\\n* anything compatible with PHP\\\'s strtotime() function.\'); return false;" tabindex="-1">[?]</a>'.(($auto_eot_time) ? '<br /><small>(<a href="https://en.wikipedia.org/wiki/Coordinated_Universal_Time" target="_blank" rel="external">Universal Time / GMT</a>)</small>' : '').'</th>'."\n";
						echo '<td><input type="text" autocomplete="off" name="ws_plugin__s2member_profile_s2member_auto_eot_time" id="ws-plugin--s2member-profile-s2member-auto-eot-time" value="'.format_to_edit($auto_eot_time).'" class="regular-text" /></td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_after_auto_eot_time", get_defined_vars());
						unset($__refs, $__v);

						if(c_ws_plugin__s2member_utils_conds::pro_is_installed())
						{
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_before_reminders_enable", get_defined_vars());
							unset($__refs, $__v);

							echo '<tr>'."\n";
							echo '<th><label for="ws-plugin--s2member-profile-s2member-reminders-enable-yes">Enable Reminder Emails?</label> <a href="#" onclick="alert(\'This setting applies only if you have configured reminder email notifications in s2Member; e.g., EOT Renewal/Reminder Emails or NPT Renewal/Reminder Emails.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
							echo '<td><label><input type="radio" name="ws_plugin__s2member_profile_s2member_reminders_enable" id="ws-plugin--s2member-profile-s2member-reminders-enable-yes" value="1"'.((string)get_user_option('s2member_reminders_enable', $user_id) !== '0' ? ' checked' : '').' /> Yes</label> &nbsp;&nbsp;&nbsp; <label><input type="radio" name="ws_plugin__s2member_profile_s2member_reminders_enable" id="ws-plugin--s2member-profile-s2member-reminders-enable-no" value="0"'.((string)get_user_option('s2member_reminders_enable', $user_id) === '0' ? ' checked' : '').' /> No (exclude)</label></td>'."\n";
							echo '</tr>'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_after_reminders_enable", get_defined_vars());
							unset($__refs, $__v);

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_before_reset_pass_resend", get_defined_vars());
							unset($__refs, $__v);

							echo '<tr>'."\n";
							echo '<th><label for="ws-plugin--s2member-profile-reset-pass-resend">Reset Password &amp; Resend Welcome Email Message:</label> <a href="#" onclick="alert(\'Checking this box will tell s2Member to reset this User\\\'s password and then reprocess the New User Email Notification message against this User\\\'s account. The user will get an email message with their Username and a Password reset link.\\n\\nThis can be helpful in cases where a User/Member missed the original email message for some reason. The User\\\'s password is reset to a new auto-generated password, nullifying the old one. Then, the user receives a link via email that they can use to set a new password of their choosing.\\n\\nTIP: It is possible to customize the New User Email Notification message with s2Member. Please see: `Dashboard → s2Member → General Options → Email Configuration → New User Notifications`.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
							echo '<td><label><input type="checkbox" name="ws_plugin__s2member_profile_reset_pass_resend" id="ws-plugin--s2member-profile-reset-pass-resend" value="1" /> Yes, reset password &amp; resend welcome email message to this User.</label></td>'."\n";
							echo '</tr>'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_after_reset_pass_resend", get_defined_vars());
							unset($__refs, $__v);
						}
					}
					if(c_ws_plugin__s2member_list_servers::list_servers_integrated()) // Only if integrated with s2Member.
					{
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_before_opt_in", get_defined_vars());
						unset($__refs, $__v);

						echo '<tr>'."\n";
						echo '<th><label for="ws-plugin--s2member-profile-opt-in">Re-process List Servers:</label> <a href="#" onclick="alert(\'You have at least one List Server integrated with s2Member. Would you like to re-process a confirmation request for this User? If not, just leave the box un-checked.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
						echo '<td><label><input type="checkbox" name="ws_plugin__s2member_profile_opt_in" id="ws-plugin--s2member-profile-opt-in" value="1" /> Yes, send a mailing list confirmation email to this User.</label></td>'."\n";
						echo '</tr>'."\n";

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_edit_cols_after_opt_in", get_defined_vars());
						unset($__refs, $__v);

						if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_auto_opt_out_transitions"])
							if(($custom_reg_auto_op_outs = c_ws_plugin__s2member_utils_strings::wrap_deep($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_auto_opt_outs"], "/^", "$/i")))
								if(c_ws_plugin__s2member_utils_arrays::in_regex_array("user-role-change", $custom_reg_auto_op_outs) || c_ws_plugin__s2member_utils_arrays::in_regex_array("modification", $custom_reg_auto_op_outs))
								{
									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action("ws_plugin__s2member_during_users_list_edit_cols_before_auto_opt_out_transitions", get_defined_vars());
									unset($__refs, $__v);

									echo '<tr>'."\n";
									echo '<th><label for="ws-plugin--s2member-custom-reg-auto-opt-out-transitions">Allow List Transitioning:</label> <a href="#" onclick="alert(\'You\\\'ve configured s2Member with List Transitions enabled. By leaving this box checked, s2Member will Transition the User\\\'s mailing list subscription(s) automatically. For example, if a Member is demoted from Level #2, down to Level #1; s2Member will add them to the Level #1 List(s) after it removes them from the Level #2 List(s).\\n\\nDepending on your configuration of s2Member, a transition may ONLY occur if s2Member IS able to successfully remove them from an existing List. In other words, if they are currently NOT subscribed to any List(s), s2Member may NOT transition them to any new Lists (depending on your configuration).\'); return false;" tabindex="-1">[?]</a></th>'."\n";
									echo '<td><label><input type="checkbox" name="ws_plugin__s2member_custom_reg_auto_opt_out_transitions" id="ws-plugin--s2member-custom-reg-auto-opt-out-transitions" value="1" checked="checked" /> Yes, automatically transition this User\'s mailing list subscription(s) when/if I change their Role.</label></td>'."\n";
									echo '</tr>'."\n";

									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									do_action("ws_plugin__s2member_during_users_list_edit_cols_after_auto_opt_out_transitions", get_defined_vars());
									unset($__refs, $__v);
								}
					}
					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_before_ip_restrictions", get_defined_vars());
					unset($__refs, $__v);

					echo '<tr>'."\n";
					echo '<th><label for="ws-plugin--s2member-profile-ip-restrictions">Reset IP Restrictions:</label> <a href="#" onclick="alert(\'A single Username is only valid for a certain number of unique IP addresses (as configured in your s2Member → General Options). Once that limit is reached, s2Member assumes there has been a security breach. At that time, s2Member will place a temporary ban (preventing access).\\n\\nIf you have spoken to a legitimate Customer that is receiving an error upon logging in (ex: 503 / too many IP addresses), you can remove this temporary ban by checking the box below. If the abusive behavior continues, s2Member will automatically re-instate IP Restrictions in the future. If you would like to gain further control over IP Restrictions, please check your General Options panel for s2Member.\'); return false;" tabindex="-1">[?]</a></th>'."\n";
					echo '<td><label><input type="checkbox" name="ws_plugin__s2member_profile_ip_restrictions" id="ws-plugin--s2member-profile-ip-restrictions" value="1" /> Yes, delete/reset IP Restrictions associated with this Username.</label>'.((c_ws_plugin__s2member_ip_restrictions::specific_ip_restriction_at_or_above_max(strtolower($user->user_login)) || c_ws_plugin__s2member_ip_restrictions::specific_ip_restriction_breached_security(strtolower($user->user_login))) ? '<br /><em>*WARNING* this User is at (or above) max allowable IP addresses (based on your IP Restrictions).</em>' : '<br /><em>*Note* this User is NOT currently banned by any of your IP Restrictions.</em>').'</td>'."\n";
					echo '</tr>'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_after_ip_restrictions", get_defined_vars());
					unset($__refs, $__v);

					if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"]) // Only if configured.
						if($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level($level, "administrative"))
						{
							echo '<tr>'."\n";
							echo '<td colspan="2">'."\n";
							echo '<div style="height:1px; line-height:1px; background:#CCCCCC;"></div>'."\n";
							echo '</td>'."\n";
							echo '</tr>'."\n";

							$fields = get_user_option("s2member_custom_fields", $user_id); // Existing fields.

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_before_custom_fields", get_defined_vars());
							unset($__refs, $__v);

							foreach(json_decode($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"], TRUE) as $field)
							{
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action("ws_plugin__s2member_during_users_list_edit_cols_during_custom_fields_before", get_defined_vars());
								unset($__refs, $__v);

								if(in_array($field["id"], $fields_applicable)) // Field applicable?
								{
									$field_var      = preg_replace("/[^a-z0-9]/i", "_", strtolower($field["id"]));
									$field_id_class = preg_replace("/_/", "-", $field_var);

									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									if(apply_filters("ws_plugin__s2member_during_users_list_edit_cols_during_custom_fields_display", TRUE, get_defined_vars()))
									{
										if(!empty($field["section"]) && $field["section"] === "yes") // Starts a new section?
											echo '<tr><td colspan="2"><div class="ws-plugin--s2member-profile-divider-section'.((!empty($field["sectitle"])) ? '-title' : '').'">'.((!empty($field["sectitle"])) ? $field["sectitle"] : '').'</div></td></tr>';

										echo '<tr>'."\n";
										echo '<th><label for="ws-plugin--s2member-profile-'.esc_attr($field_id_class).'">'.((preg_match("/^(checkbox|pre_checkbox)$/", $field["type"])) ? ucwords(preg_replace("/_/", " ", $field_var)) : $field["label"]).':</label></th>'."\n";
										echo '<td>'.c_ws_plugin__s2member_custom_reg_fields::custom_field_gen(__FUNCTION__, $field, "ws_plugin__s2member_profile_", "ws-plugin--s2member-profile-", "", ((preg_match("/^(text|textarea|select|selects)$/", $field["type"])) ? "width:99%;" : ""), "", "", $fields, @$fields[$field_var], "administrative").'</td>'."\n";
										echo '</tr>'."\n";
									}
									unset($__refs, $__v);
								}
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action("ws_plugin__s2member_during_users_list_edit_cols_during_custom_fields_after", get_defined_vars());
								unset($__refs, $__v);
							}
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_after_custom_fields", get_defined_vars());
							unset($__refs, $__v);

							echo '<tr>'."\n";
							echo '<td colspan="2">'."\n";
							echo '<div style="height:1px; line-height:1px; background:#CCCCCC;"></div>'."\n";
							echo '</td>'."\n";
							echo '</tr>'."\n";
						}
					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_before_notes", get_defined_vars());
					unset($__refs, $__v);

					echo '<tr>'."\n";
					echo '<th><label for="ws-plugin--s2member-profile-s2member-notes">Administrative Notes:</label> <a href="#" onclick="alert(\'This is for Administrative purposes. You can keep a list of Notations about this account. These Notations are private; Users/Members will never see these.\\n\\n*Note* The s2Member software may `append` Notes to this field occasionally, under special circumstances. For example, when/if s2Member demotes a paid Member to a Free Subscriber, s2Member will leave a Note in this field.\'); return false;" tabindex="-1">[?]</a><br /><br /><small>These Notations are private; Users/Members will never see any of these notes.</small></th>'."\n";
					echo '<td><textarea name="ws_plugin__s2member_profile_s2member_notes" id="ws-plugin--s2member-profile-s2member-notes" rows="5" wrap="off" spellcheck="false" style="width:99%;">'.format_to_edit(get_user_option("s2member_notes", $user_id)).'</textarea></td>'."\n";
					echo '</tr>'."\n";

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_after_notes", get_defined_vars());
					unset($__refs, $__v);

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action("ws_plugin__s2member_during_users_list_edit_cols_after", get_defined_vars());
					unset($__refs, $__v);

					echo '</table>'."\n";

					echo '<div style="margin:25px 0 25px 0; height:1px; line-height:1px; background:#CCCCCC;"></div>'."\n";
				}
				else if($current_user->ID === $user->ID) // Otherwise, a User can always edit their own Profile.
				{
					if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"] /* Only if configured. */)
						if($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level($level, "profile"))
						{
							echo '<div style="margin:25px 0 25px 0; height:1px; line-height:1px; background:#CCCCCC;"></div>'."\n";

							echo '<h3>'._x("Additional Profile Fields", "s2member-front", "s2member").((is_multisite()) ? ' '._x("(for this site)", "s2member-front", "s2member") : "").'</h3>'."\n";

							echo '<table class="form-table">'."\n";

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_before", get_defined_vars());
							unset($__refs, $__v);

							$fields = get_user_option("s2member_custom_fields", $user_id); // Existing fields.

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_before_custom_fields", get_defined_vars());
							unset($__refs, $__v);

							foreach(json_decode($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"], TRUE) as $field)
							{
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action("ws_plugin__s2member_during_users_list_edit_cols_during_custom_fields_before", get_defined_vars());
								unset($__refs, $__v);

								if(in_array($field["id"], $fields_applicable)) // Field applicable?
								{
									$field_var      = preg_replace("/[^a-z0-9]/i", "_", strtolower($field["id"]));
									$field_id_class = preg_replace("/_/", "-", $field_var);

									foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
									if(apply_filters("ws_plugin__s2member_during_users_list_edit_cols_during_custom_fields_display", TRUE, get_defined_vars()))
									{
										if(!empty($field["section"]) && $field["section"] === "yes") // Starts a new section?
											echo '<tr><td colspan="2"><div class="ws-plugin--s2member-profile-divider-section'.((!empty($field["sectitle"])) ? '-title' : '').'">'.((!empty($field["sectitle"])) ? $field["sectitle"] : '').'</div></td></tr>';

										echo '<tr>'."\n";
										echo '<th><label for="ws-plugin--s2member-profile-'.esc_attr($field_id_class).'">'.((preg_match("/^(checkbox|pre_checkbox)$/", $field["type"])) ? ucwords(preg_replace("/_/", " ", $field_var)) : $field["label"]).':</label></th>'."\n";
										echo '<td>'.c_ws_plugin__s2member_custom_reg_fields::custom_field_gen(__FUNCTION__, $field, "ws_plugin__s2member_profile_", "ws-plugin--s2member-profile-", "", ((preg_match("/^(text|textarea|select|selects)$/", $field["type"])) ? "width:99%;" : ""), "", "", $fields, $fields[$field_var], "profile").'</td>'."\n";
										echo '</tr>'."\n";
									}
									unset($__refs, $__v);
								}
								foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
								do_action("ws_plugin__s2member_during_users_list_edit_cols_during_custom_fields_after", get_defined_vars());
								unset($__refs, $__v);
							}
							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_after_custom_fields", get_defined_vars());
							unset($__refs, $__v);

							foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
							do_action("ws_plugin__s2member_during_users_list_edit_cols_after", get_defined_vars());
							unset($__refs, $__v);

							echo '</table>'."\n";

							echo '<div style="margin:25px 0 25px 0; height:1px; line-height:1px; background:#CCCCCC;"></div>'."\n";
						}
				}
			}
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_after_users_list_edit_cols", get_defined_vars());
			unset($__refs, $__v);
		}

		/**
		 * Saves Custom Fields after an admin updates Profile.
		 *
		 * @package s2Member\Users_List
		 * @since 3.5
		 *
		 * @attaches-to ``add_action("edit_user_profile_update");``
		 * @attaches-to ``add_action("personal_options_update");``
		 *
		 * @param int|string $user_id Expects a numeric WordPress User ID passed in by the Action Hook.
		 */
		public static function users_list_update_cols($user_id = '')
		{
			global $current_site, $current_blog; // Multisite networks.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_before_users_list_update_cols", get_defined_vars());
			unset($__refs, $__v);

			$user         = new WP_User($user_id); // We need both of these objects. `$user` and `$current_user`.
			$current_user = (is_user_logged_in()) ? wp_get_current_user() : FALSE; // Current user.

			if(is_object($user) && !empty($user->ID) && ($user_id = $user->ID) && is_object($current_user) && !empty($current_user->ID))
			{
				if(current_user_can("edit_users") && (!is_multisite() || is_super_admin() || is_user_member_of_blog($user_id)))
				{
					if(!empty($_POST) && is_array($_p = c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_POST))))
					{
						$old_user = unserialize(serialize($user)); // Copy existing User obj.
						$old_role = c_ws_plugin__s2member_user_access::user_access_role($old_user);

						$role  = isset($_p["role"]) && $_p["role"] !== $old_role ? $_p["role"] : $old_role;
						$level = c_ws_plugin__s2member_user_access::user_access_role_to_level($role);

						$user->roles      = isset($_p["role"]) && $_p["role"] !== $old_role ? array($_p["role"]) : $old_user->roles;
						$user->user_email = isset($_p["email"]) && is_email($_p["email"]) && $_p["email"] !== $old_user->user_email && !email_exists($_p["email"]) ? $_p["email"] : $old_user->user_email;
						$user->first_name = isset($_p["first_name"]) && $_p["first_name"] !== $old_user->first_name ? $_p["first_name"] : $old_user->first_name;
						$user->last_name  = isset($_p["last_name"]) && $_p["last_name"] !== $old_user->last_name ? $_p["last_name"] : $old_user->last_name;

						$auto_eot_time = !empty($_p["ws_plugin__s2member_profile_s2member_auto_eot_time"]) ? strtotime($_p["ws_plugin__s2member_profile_s2member_auto_eot_time"]) : "";

						if($role !== $old_role) // In this case, we need to fire Hook: `ws_plugin__s2member_during_collective_mods`.
							do_action("ws_plugin__s2member_during_collective_mods", $user_id, get_defined_vars(), "user-role-change", "modification", $role, $user, $old_user);

						if(isset($_p["ws_plugin__s2member_profile_s2member_originating_blog"]) && is_multisite() && is_super_admin())
							update_user_meta($user_id, "s2member_originating_blog", $_p["ws_plugin__s2member_profile_s2member_originating_blog"]);

						if(isset($_p["ws_plugin__s2member_profile_s2member_subscr_gateway"]))
							update_user_option($user_id, "s2member_subscr_gateway", $_p["ws_plugin__s2member_profile_s2member_subscr_gateway"]);

						if(isset($_p["ws_plugin__s2member_profile_s2member_subscr_id"]))
							update_user_option($user_id, "s2member_subscr_id", $_p["ws_plugin__s2member_profile_s2member_subscr_id"]);

						if(isset($_p["ws_plugin__s2member_profile_s2member_subscr_cid"]))
							update_user_option($user_id, "s2member_subscr_cid", $_p["ws_plugin__s2member_profile_s2member_subscr_cid"]);

						if(isset($_p["ws_plugin__s2member_profile_s2member_custom"]))
							update_user_option($user_id, "s2member_custom", $_p["ws_plugin__s2member_profile_s2member_custom"]);

						if(isset($_p["ws_plugin__s2member_profile_s2member_coupon_codes"]))
							update_user_option($user_id, "s2member_coupon_codes", array_map('trim', preg_split('/,+/', $_p["ws_plugin__s2member_profile_s2member_coupon_codes"], NULL, PREG_SPLIT_NO_EMPTY)));

						if(isset($_p["ws_plugin__s2member_profile_s2member_registration_ip"]))
							update_user_option($user_id, "s2member_registration_ip", $_p["ws_plugin__s2member_profile_s2member_registration_ip"]);

						if(isset($_p["ws_plugin__s2member_profile_s2member_notes"]))
							update_user_option($user_id, "s2member_notes", $_p["ws_plugin__s2member_profile_s2member_notes"]);

						if(isset($_p["ws_plugin__s2member_profile_s2member_auto_eot_time"]) && isset($auto_eot_time))
							update_user_option($user_id, "s2member_auto_eot_time", $auto_eot_time);

						if(isset($_p["ws_plugin__s2member_profile_s2member_reminders_enable"]))
							update_user_option($user_id, "s2member_reminders_enable", (string)(int)$_p["ws_plugin__s2member_profile_s2member_reminders_enable"]);

						if(isset($_p["ws_plugin__s2member_profile_s2member_ccaps"]))
						{
							foreach($user->allcaps as $cap => $cap_enabled)
								if(preg_match("/^access_s2member_ccap_/", $cap))
									$user->remove_cap($ccap = $cap);

							if(!empty($_p["ws_plugin__s2member_profile_s2member_ccaps"]))
								foreach(preg_split("/[\r\n\t\s;,]+/", $_p["ws_plugin__s2member_profile_s2member_ccaps"]) as $ccap)
									if(strlen($ccap = trim(strtolower(preg_replace("/[^a-z_0-9]/i", "", $ccap)))))
										$user->add_cap("access_s2member_ccap_".$ccap);
						}
						if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"])
						{
							foreach(json_decode($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"], TRUE) as $field)
							{
								$field_var      = preg_replace("/[^a-z0-9]/i", "_", strtolower($field["id"]));
								$field_id_class = preg_replace("/_/", "-", $field_var);

								if(isset($_p["ws_plugin__s2member_profile_".$field_var]) /* Field being set? */)
								{
									if((is_array($_p["ws_plugin__s2member_profile_".$field_var]) && !empty($_p["ws_plugin__s2member_profile_".$field_var])) || (is_string($_p["ws_plugin__s2member_profile_".$field_var]) && strlen($_p["ws_plugin__s2member_profile_".$field_var])))
										$fields[$field_var] = $_p["ws_plugin__s2member_profile_".$field_var];
									else if(isset($fields)) unset($fields[$field_var]);
								}
								else if(isset($fields)) unset($fields[$field_var]);
							}
						}
						if(!empty($fields))
							update_user_option($user_id, "s2member_custom_fields", $fields);
						else delete_user_option($user_id, "s2member_custom_fields");

						if($level > 0 /* We only process this if they are higher than Level #0. */)
						{
							$pr_times                 = get_user_option("s2member_paid_registration_times", $user_id);
							$pr_times["level"]        = (empty($pr_times["level"])) ? time() : $pr_times["level"];
							$pr_times["level".$level] = (empty($pr_times["level".$level])) ? time() : $pr_times["level".$level];
							update_user_option($user_id, "s2member_paid_registration_times", $pr_times); // Update now.
						}
						if(!empty($_p["ws_plugin__s2member_profile_opt_in"]) && !empty($role) && $level >= 0 /* Should we process List Servers? */)
							c_ws_plugin__s2member_list_servers::process_list_servers($role, $level, $user->user_login, ((!empty($_p["pass1"])) ? $_p["pass1"] : ""), $user->user_email, $user->first_name, $user->last_name, FALSE, TRUE, TRUE, $user_id);

						if(!empty($_p["ws_plugin__s2member_profile_ip_restrictions"]) /* Delete/reset IP Restrictions? */)
							c_ws_plugin__s2member_ip_restrictions::delete_reset_specific_ip_restrictions(strtolower($user->user_login));

						if(!empty($_p["ws_plugin__s2member_profile_reset_pass_resend"]) && c_ws_plugin__s2member_utils_conds::pro_is_installed() /* Reset password & resend email notification? */)
							c_ws_plugin__s2member_email_configs::reset_pass_resend_new_user_notification($user_id, ((!empty($_p["pass1"])) ? $_p["pass1"] : ""), array("user"), $user->user_email);

						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_update_cols", get_defined_vars());
						unset($__refs, $__v);
					}
				}
				else if($current_user->ID === $user->ID /* Otherwise, a User can always edit their own Profile. */)
				{
					if(!empty($_POST) && is_array($_p = c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_POST))))
					{
						$role  = c_ws_plugin__s2member_user_access::user_access_role($user /* Role is not changing here. */);
						$level = c_ws_plugin__s2member_user_access::user_access_role_to_level($role);

						if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"])
							if($fields_applicable = c_ws_plugin__s2member_custom_reg_fields::custom_fields_configured_at_level($level, "profile"))
							{
								$fields           = array(); // Initialize.
								$_existing_fields = get_user_option("s2member_custom_fields", $user_id);

								foreach(json_decode($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"], TRUE) as $field)
								{
									$field_var      = preg_replace("/[^a-z0-9]/i", "_", strtolower($field["id"]));
									$field_id_class = preg_replace("/_/", "-", $field_var);

									if(!in_array($field["id"], $fields_applicable) || strpos($field["editable"], "no") === 0)
									{
										if(isset($_existing_fields[$field_var]) && ((is_array($_existing_fields[$field_var]) && !empty($_existing_fields[$field_var])) || (is_string($_existing_fields[$field_var]) && strlen($_existing_fields[$field_var]))))
											$fields[$field_var] = $_existing_fields[$field_var];
										else unset($fields[$field_var]);
									}
									else if($field["required"] === "yes" && (!isset($_p["ws_plugin__s2member_profile_".$field_var]) || (!is_array($_p["ws_plugin__s2member_profile_".$field_var]) && !is_string($_p["ws_plugin__s2member_profile_".$field_var])) || (is_array($_p["ws_plugin__s2member_profile_".$field_var]) && empty($_p["ws_plugin__s2member_profile_".$field_var])) || (is_string($_p["ws_plugin__s2member_profile_".$field_var]) && !strlen($_p["ws_plugin__s2member_profile_".$field_var]))))
									{
										if(isset($_existing_fields[$field_var]) && ((is_array($_existing_fields[$field_var]) && !empty($_existing_fields[$field_var])) || (is_string($_existing_fields[$field_var]) && strlen($_existing_fields[$field_var]))))
											$fields[$field_var] = $_existing_fields[$field_var];
										else unset($fields[$field_var]);
									}
									else if(isset($_p["ws_plugin__s2member_profile_".$field_var]))
									{
										if((is_array($_p["ws_plugin__s2member_profile_".$field_var]) && !empty($_p["ws_plugin__s2member_profile_".$field_var])) || (is_string($_p["ws_plugin__s2member_profile_".$field_var]) && strlen($_p["ws_plugin__s2member_profile_".$field_var])))
											$fields[$field_var] = $_p["ws_plugin__s2member_profile_".$field_var];
										else unset($fields[$field_var]);
									}
									else unset($fields[$field_var]);
								}
								if(!empty($fields))
									update_user_option($user_id, "s2member_custom_fields", $fields);
								else delete_user_option($user_id, "s2member_custom_fields");
							}
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action("ws_plugin__s2member_during_users_list_update_cols", get_defined_vars());
						unset($__refs, $__v);
					}
				}
			}
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action("ws_plugin__s2member_after_users_list_update_cols", get_defined_vars());
			unset($__refs, $__v);
		}
	}
}
