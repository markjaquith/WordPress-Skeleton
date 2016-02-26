<?php
/**
 * Menu page for the s2Member plugin (General Options page).
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

if(!class_exists("c_ws_plugin__s2member_menu_page_gen_ops"))
{
	/**
	 * Menu page for the s2Member plugin (General Options page).
	 *
	 * @package s2Member\Menu_Pages
	 * @since 110531
	 */
	class c_ws_plugin__s2member_menu_page_gen_ops
	{
		public function __construct()
		{
			echo '<div class="wrap ws-menu-page">'."\n";

			echo '<div class="ws-menu-page-toolbox">'."\n";
			c_ws_plugin__s2member_menu_pages_tb::display();
			echo '</div>'."\n";

			echo '<h2>General Options</h2>'."\n";

			echo '<table class="ws-menu-page-table">'."\n";
			echo '<tbody class="ws-menu-page-table-tbody">'."\n";
			echo '<tr class="ws-menu-page-table-tr">'."\n";
			echo '<td class="ws-menu-page-table-l">'."\n";

			echo '<form method="post" name="ws_plugin__s2member_options_form" id="ws-plugin--s2member-options-form" autocomplete="off">'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_options_save" id="ws-plugin--s2member-options-save" value="'.esc_attr(wp_create_nonce("ws-plugin--s2member-options-save")).'" />'."\n";
			echo '<input type="hidden" name="ws_plugin__s2member_configured" id="ws-plugin--s2member-configured" value="1" />'."\n";

			do_action("ws_plugin__s2member_during_gen_ops_page_before_left_sections", get_defined_vars());

			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_uninstall", (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site() || is_super_admin()), get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_uninstall", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Plugin Deletion Safeguards"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["run_uninstall_routines"]) ? ' default-state="open"' : '').'>'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-uninstall-section">'."\n";
				echo '<h3>Plugin Deletion Safeguards (highly recommended)</h3>'."\n";
				echo '<p>By default, s2Member will retain all of it\'s Roles, Capabilities, and your Configuration Options when/if you delete s2Member from the Plugins Menu in WordPress. However, if you would like for s2Member to erase itself completely, please choose: <code>No (upon deletion, erase all data/options)</code>. See also: <a href="http://s2member.com/kb-article/how-do-i-uninstall-s2member/" target="_blank" rel="external">s2Member Uninstall Instructions</a></p>';
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_uninstall", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-run-uninstall-routines">'."\n";
				echo 'Safeguard s2Member Data/Options?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_run_uninstall_routines" id="ws-plugin--s2member-run-uninstall-routines">'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["run_uninstall_routines"]) ? ' selected="selected"' : '').'>Yes (safeguard all data/options)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["run_uninstall_routines"]) ? ' selected="selected"' : '').'>No (upon deletion, erase all data/options)</option>'."\n";
				echo '</select><br />'."\n";
				echo 'Recommended setting: (<code>Yes, safeguard all data/options</code>)'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_uninstall", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_security", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_security", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Security Encryption Key">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-security-section">'."\n";
				echo '<img src="'.esc_attr($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["dir_url"]).'/images/large-icon.png" title="s2Member (a Membership management system for WordPress)" alt="" style="float:right; margin:0 0 0 25px; border:0;" />'."\n";
				echo '<h3>Security Encryption Key (optional, for tighter security)</h3>'."\n";
				echo '<p>Just like WordPress, s2Member is open-source software. Which is wonderful. However, this also makes it possible for anyone to grab a copy of the software, and try to learn their way around its security measures. In order to keep your installation of s2Member unique/secure, you should configure a Security Encryption Key. s2Member will use your Security Encryption Key to protect itself against hackers. It does this by encrypting all sensitive information with your Key. A Security Encryption Key is unique to your installation.</p>'."\n";
				echo '<p>Once you configure this, you <em>do not</em> want to change it—ever! In fact, it is a <em>very</em> good idea to keep this backed up in a safe place, just in case you need to move your site, or re-install s2Member in the future. Some of the sensitive data that s2Member stores will be encrypted with this Key. If you change it, that data can no longer be read, even by s2Member itself. In other words, don\'t use s2Member for six months, then decide to change your Key. That would break your installation.</p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_security", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-sec-encryption-key">'."\n";
				echo 'Security Encryption Key (at least 60 chars)'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sec_encryption_key"]) ? ' <a href="#" onclick="ws_plugin__s2member_enableSecurityKey(); return false;" title="(not recommended)">edit key</a>' : ' <a href="#" onclick="ws_plugin__s2member_generateSecurityKey(); return false;" title="Insert an auto-generated Key. (recommended)">auto-generate</a>')."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" maxlength="256" autocomplete="off" name="ws_plugin__s2member_sec_encryption_key" id="ws-plugin--s2member-sec-encryption-key" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sec_encryption_key"]).'"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sec_encryption_key"]) ? ' disabled="disabled"' : '').' />'."\n";
				echo (!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sec_encryption_key"]) ? '<br />This may contain letters, numbers, spaces; even punctuation. Up to 256 characters.<br /><em>Ex: <code>'.esc_html(strtoupper(c_ws_plugin__s2member_utils_strings::random_str_gen(64))).'</code></em>'."\n" : '';
				echo (count($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sec_encryption_key_history"]) > 1) ? '<br /><a href="#" onclick="ws_plugin__s2member_securityKeyHistory(); return false;">Click here</a> for a history of your last 10 Encryption Keys.<div id="ws-plugin--s2member-sec-encryption-key-history" style="display:none;"><code>'.implode('</code><br /><code>', $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sec_encryption_key_history"]).'</code></div>'."\n" : '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3>Additional Details Regarding this Key:</h3>'."\n";
				echo '<p>Your Security Encryption Key is used throughout s2Member\'s source code for many different things. However, most (not all, but most) uses of this Key are related to transactional processing within a particular session; so changing the Key won\'t really impact these scenarios in any significant way. Your Security Encryption Key is simply there to enhance security of data that is being transmitted in these cases.</p>'."\n";
				echo '<p>That said, there are a few scenarios where use of your Security Encryption Key is more long-term. These include: Specific Post/Page Access Links, Registration Access Links, and it can also have a long-term impact on IPN communication because some data analyzed by s2Member includes a checksum that depends on your Key. If the Key changes, it could cause future IPN data (i.e., data from your payment gateway) to fail validation.</p>'."\n";

				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_security", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_localhost_info", (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site() || is_super_admin()), get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_localhost_info", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Localhost WAMP/MAMP Developers">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-localhost-info-section">'."\n";
				echo '<h3>Localhost WAMP/MAMP Installs (are you a developer?)</h3>'."\n";
				echo '<p>If you\'re developing your site in a <code>localhost</code> environment, running something like WAMP/MAMP, or <a href="http://www.easyphp.org/" target="_blank" rel="external">EasyPHP</a>, please add this line to your <code>/wp-config.php</code> file: <code><span style="color:#0000BB;">define</span><span style="color:#007700;">(</span><span style="color:#DD0000;">"LOCALHOST"</span>, <span style="color:#0000BB;">true</span><span style="color:#007700;">);</span></code>.</p>'."\n";
				echo '<p>This lets s2Member know definitively that your site is in a <code>localhost</code> environment. s2Member will adjust itself accordingly, maximizing functionality during your developement. s2Member can usually auto-detect this, but in cases where your <code>localhost</code> installation runs on something other than <code>127.0.0.1/localhost</code>, you need to tell s2Member definitively, by adding that line to your <code>/wp-config.php</code> file. For instance, s2Member needs to know when your server IP is the same as all User IPs.</p>'."\n";
				echo '<p><em>Once your site goes live, please remove the line. If you\'re already on a live server connected to the web, please ignore this section.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_localhost_info", get_defined_vars());
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_localhost_info", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_lazy_load", (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site() || is_super_admin()), get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_lazy_load", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="CSS/JS Lazy Loading">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-lazy-load-section">'."\n";
				echo '<h3>CSS/JS Lazy Loading (Client-Side Libraries)</h3>'."\n";
				echo '<p>By default, s2Member will load it\'s CSS/JS libraries on every page of your site. However, you may wish to enable lazy-loading here (i.e., only load when absolutely necessary).</p>'."\n";
				echo '<p><em><strong>Tip:</strong> Do you need s2Member\'s CSS/JS on every page? If not, you can turn lazy-loading on. If you need s2Member\'s CSS/JS on a given Post/Page, you can insert an HTML comment into the Post/Page content like this: <code>&lt;!--s2member--&gt;</code>. If a Post/Page contains the word <code>s2member</code> or an <code>[s2*</code> Shortcode, this will automatically trigger s2Member\'s lazy-load routine (no matter what you configure here). Thus, it\'s an easy way to force s2Member to load it\'s CSS/JS on specific Posts/Pages where you deem this necessary. There is also a WordPress filter available: <code>add_filter("ws_plugin__s2member_lazy_load_css_js", "__return_true");</code> for developers. This could be incorporated into more dynamic scenarios.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_lazy_load", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-lazy-load-css-js">'."\n";
				echo 'Lazy-Load s2Member\'s CSS/JS Libraries?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_lazy_load_css_js" id="ws-plugin--s2member-lazy-load-css-js">'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["lazy_load_css_js"]) ? ' selected="selected"' : '').'>No (always load the CSS/JS libraries; i.e., on every page of the site)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["lazy_load_css_js"]) ? ' selected="selected"' : '').'>Yes (lazy-load CSS/JS libraries; i.e., load only when absolutely necessary)</option>'."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_lazy_load", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_s_badge_wp_footer_code", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_s_badge_wp_footer_code", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="s2Member Security Badge">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-s-badge-wp-footer-code-section">'."\n";
				echo '<h3>Security Badge &amp; Footer Configuration (optional)</h3>'."\n";
				echo '<div class="ws-menu-page-right">'.c_ws_plugin__s2member_utilities::s_badge_gen("1", TRUE, TRUE).'</div>'."\n";
				echo '<p>An s2Member Security Badge (optional), can be used to express your site\'s concern for security; demonstrating to all Users/Members that your site (and the s2Member software) takes security seriously. However, in order to qualify your site, you <em>must</em> generate a Security Encryption Key (previous section), and then click "Save All Changes". Only then, will s2Member officially verify your installation</em>.</p>'."\n";
				echo '<p>Once you\'ve <a href="http://www.s2member.com/kb/security-badges/" target="_blank" rel="external">properly configured all security aspects of s2Member</a>, your s2Member Security Badge will be verified. To see the "verified" version of your Security Badge, you might need to refresh your browser after saving all changes (i.e., after you create a Security Encryption Key). Also, s2Member will <em>not</em> "verify" your site if you turn off Unique IP Restrictions, Brute Force Login Protection, or if your <code>/wp-config.php</code> file lacks <a href="http://codex.wordpress.org/Editing_wp-config.php#Security_Keys" target="_blank" rel="external">Security Keys</a> <em>(each at least 60 chars in length)</em>. In addition, it\'s not possible for s2Member to verify your Security Badge if your site is in a <code>localhost</code> environment; i.e., not connected to the web.</p>'."\n";
				echo '<p><strong>How does s2Member know when my site is secure?</strong><br />If enabled below, an API call for "Security Badge Status" will allow web service connections to determine your status. Clicking <a href="'.esc_attr(home_url("/?s2member_s_badge_status=1")).'" target="_blank" rel="external">this link</a> will report <code>1</code> <em>(secure)</em>, <code>0</code> <em>(at risk)</em>, or <code>-</code> <em>(API disabled)</em>. Once all security considerations are satisfied, s2Member will report <code>1</code> <em>(secure)</em> for your installation. <strong>Note:</strong> this simple API will not, and should not, report any other information. It will only report the current status of your Security Badge, as determined by your installation of s2Member. When/if you install the s2Member Security Badge, s2Member will make a connection to your site <strong>once per day</strong>, to test your status.</p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_s_badge_wp_footer_code", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-s-badge-status-enabled">'."\n";
				echo 'Enable Security Badge Status API?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_s_badge_status_enabled" id="ws-plugin--s2member-s-badge-status-enabled">'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["s_badge_status_enabled"]) ? ' selected="selected"' : '').'>No (default, Badge Status API is disabled)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["s_badge_status_enabled"]) ? ' selected="selected"' : '').'>Yes (enable Badge Status API for verification)</option>'."\n";
				echo '</select><br />'."\n";
				echo 'This must be enabled if you want s2Member to verify your Security Badge.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-wp-footer-code">'."\n";
				echo 'Customize WordPress Footer:<br />'."\n";
				echo '<small>[ <a href="#" onclick="this.$code = jQuery(\'textarea#ws-plugin--s2member-wp-footer-code\'); this.$code.val(jQuery.trim(unescape(\''.rawurlencode('[s2Member-Security-Badge v="1" /]').'\')+\'\n\'+this.$code.val())); return false;">Click HERE to insert your Security Badge</a> ],<br />or use Shortcode <code>[s2Member-Security-Badge v="1" /]</code> in a Post/Page/Widget.<br />The <code>v="1"</code> attribute is a Security Badge style/variation. Try variations <code>1|2|3</code>.</small>'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_wp_footer_code" id="ws-plugin--s2member-wp-footer-code" rows="8" wrap="off" spellcheck="false">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["wp_footer_code"]).'</textarea><br />'."\n";
				echo 'Any valid XHTML / JavaScript'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site()) ? '' : ' (or even PHP)').' code will work just fine here.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_s_badge_wp_footer_code", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_email_config", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_email_config", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Email Configuration">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-email-section">'."\n";
				echo '<h3 style="margin:0;">Email From: '.esc_html('"Name" <address>').'</h3>'."\n";
				echo '<p style="margin:0;">This is the name/address that will appear in outgoing email notifications sent by the s2Member plugin.</p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_email_from_name_config", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-reg-email-from-name">'."\n";
				echo 'Email From Name:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_reg_email_from_name" id="ws-plugin--s2member-reg-email-from-name" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["reg_email_from_name"]).'" /><br />'."\n";
				echo 'We recommend that you use the name of your site here.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-reg-email-from-email">'."\n";
				echo 'Email From Address:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_reg_email_from_email" id="ws-plugin--s2member-reg-email-from-email" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["reg_email_from_email"]).'" /><br />'."\n";
				echo 'Example: support@your-domain.com. <em>Please read <a href="#" onclick="alert(\'Running WordPress with an SMTP mail plugin?\\n\\nPlease be advised. If you run an SMTP mail plugin with WordPress, be sure to configure s2Member with a valid `From:` address (i.e., one matching your SMTP configuration perhaps). Most free SMTP servers, such as Gmail and Yahoo, require that your `From:` header match the email address associated with your account. Please check with your SMTP service provider before attempting to configure plugins like s2Member to use a different `From:` address when sending email messages.\'); return false;">this important note</a></em>.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-reg-email-support-link">'."\n";
				echo 'Email Support/Contact Link:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_reg_email_support_link" id="ws-plugin--s2member-reg-email-support-link" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["reg_email_support_link"]).'" /><br />'."\n";
				echo 'Ex: <code>mailto:support@your-domain.com</code> (<em>mailto link</em>)<br />'."\n";
				echo 'Or: <code>'.esc_html(home_url("/contact-us/")).'</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3 style="margin:0;">New User Email Configuration</h3>'."\n";
				echo '<input type="hidden" id="ws-plugin--s2member-pluggables-wp-new-user-notification" value="'.esc_attr((empty($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["pluggables"]["wp_new_user_notification"])) ? '0' : '1').'" />'."\n";
				echo (empty($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["pluggables"]["wp_new_user_notification"])) ? '<p class="ws-menu-page-error" style="margin:0;"><em><strong>Conflict warning:</strong> You have another theme or plugin installed that is preventing s2Member from controlling this aspect of your installation. When the pluggable function <code><a href="http://codex.wordpress.org/Function_Reference/wp_new_user_notification" target="_blank" rel="external">wp_new_user_notification()</a></code> is handled by another plugin, it\'s not possible for s2Member to allow customization of New User Emails. This is NOT a major issue. In fact, in some cases, it might be desirable. That being said, if you DO want to use s2Member\'s customization of New User Emails, you will need to deactivate one plugin at a time until this conflict warning goes away.</em></p>'."\n" : '';
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_new_user_emails", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_new_user_emails_enabled" id="ws-plugin--s2member-new-user-emails-enabled">'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["new_user_emails_enabled"]) ? ' selected="selected"' : '').'>No (default, use WordPress defaults)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["new_user_emails_enabled"]) ? ' selected="selected"' : '').'>Yes (customize New User Emails with s2Member)</option>'."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div id="ws-plugin--s2member-new-user-emails">'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3 style="margin:0;">New User Email Message (<a href="#" onclick="jQuery(\'div#ws-plugin--s2member-new-user-email-details\').toggle(); return false;" class="ws-dotted-link">click to customize</a>)</h3>'."\n";
				echo '<p style="margin:0;">This email is sent to new Users/Members who did <em>not</em> set a Custom Password during registration.</p>'."\n";
				if($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password"])
					echo '<p class="info" style="font-size:80%; margin:.5em 0 0 0;"><strong>↑ NOTE:</strong> You currently have Custom Passwords enabled in your s2Member Registration/Profile Field options. Therefore, this email is not going to be sent; i.e., it is only sent to users who need it for the purpose of obtaining their password.</p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_new_user_email", get_defined_vars());

				echo '<div id="ws-plugin--s2member-new-user-email-details" style="display:none;">'."\n";
				echo c_ws_plugin__s2member_utils_conds::bp_is_installed() ? '<p><em><strong>BuddyPress:</strong> please note that BuddyPress does NOT send this email to Users that register through the BuddyPress registration system. This is because BuddyPress sends each User an activation link; eliminating the need for this email altogether. However, you CAN still customize s2Member\'s separate email to paying Members. See: <strong>s2Member → PayPal Options → Signup Confirmation Email</strong>.</em></p>'."\n" : '';
				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-new-user-email-subject">'."\n";
				echo 'New User Email Subject:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_new_user_email_subject" id="ws-plugin--s2member-new-user-email-subject" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["new_user_email_subject"]).'" /><br />'."\n";
				echo 'Subject Line used in the email sent to new Users/Members.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-new-user-email-message">'."\n";
				echo 'New User Email Message:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<p><em>(The purpose of this email is to send the user a password-setup link; i.e., <code>%%wp_set_pass_url%%</code>)</em></small><p>'."\n";
				echo '<textarea name="ws_plugin__s2member_new_user_email_message" id="ws-plugin--s2member-new-user-email-message" rows="10">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["new_user_email_message"]).'</textarea><br />'."\n";
				echo 'Message Body used in the email sent to new Users/Members.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul>'."\n";
				echo '<li style="margin-bottom:1em;"><code>%%wp_set_pass_url%%</code> = The full URL where Users can set their password for the first time. Note that <code>%%wp_set_pass_url%%</code> should be used instead of the older/deprecated <code style="text-decoration:line-through;">%%user_pass%%</code> Replacement Code. Starting w/ WordPress v4.3+, a user can visit this link (i.e., <code>%%wp_set_pass_url%%</code>) to set a password of their own, and this is accomplished without sending a plain-text password via email; which improves security. It is suggested that you prefix this as follows: <em>To set your password, visit: <code>%%wp_set_pass_url%%</code></em></li>'."\n";
				echo '<li><code>%%role%%</code> = The Role ID <code>(subscriber, s2member_level[0-9]+, administrator, editor, author, contributor)</code>.</li>'."\n";
				echo '<li><code>%%label%%</code> = The Role ID Label <code>(Subscriber, s2Member Level 1, s2Member Level 2; or your own custom Labels—if configured)</code>.</li>'."\n";
				echo '<li><code>%%level%%</code> = The Level number <code>(0, 1, 2, 3, 4)</code>. (<em>deprecated, no longer recommended; use <code>%%role%%</code></em>)</li>'."\n";
				echo '<li><code>%%ccaps%%</code> = Custom Capabilities. Ex: <code>music,videos,free_gift</code> (<em>in comma-delimited format</em>).</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name (First &amp; Last) of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_login%%</code> = The Username the Member selected during registration.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The User\'s IP Address, detected via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%user_id%%</code> = A unique WordPress User ID generated during registration.</li>'."\n";
				echo '<li><code>%%wp_login_url%%</code> = The full URL where Users can get logged into your site.</li>'."\n";
				echo '<li style="margin-top:1em;"><code style="text-decoration:line-through;">%%user_pass%%</code> = <em>Deprecated, please stop using this. Starting w/ WordPress v4.3, this email is only sent to users who did NOT set a Custom Password during registration; i.e., the New User Notification email is only sent to the user whenever they need it to set their password. In that scenario, you should be sending the user the <code>%%wp_set_pass_url%%</code> instead of sending a plain-text password via email; which is a security no-no.</em></li>'."\n";
				echo '</ul>'."\n";

				echo '<strong>Custom Registration/Profile Fields are also supported in this email:</strong>'."\n";
				echo '<ul>'."\n";
				echo '<li><code>%%date_of_birth%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>date_of_birth</code>.</li>'."\n";
				echo '<li><code>%%street_address%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>street_address</code>.</li>'."\n";
				echo '<li><code>%%country%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>country</code>.</li>'."\n";
				echo '<li><em><code>%%etc, etc...%%</code> <strong>see:</strong> s2Member → General Options → Registration/Profile Fields</em>.</li>'."\n";
				echo '</ul>'."\n";

				echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
				echo '<ul>'."\n";
				echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
				echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
				echo '</ul>'."\n";
				echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
				echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
				echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ?
					'<div class="ws-menu-page-hr"></div>'."\n".
					'<p style="margin:0;"><strong>PHP Code:</strong> It is also possible to use PHP tags—optional (for developers). If you use PHP tags, please run a test email with <code>&lt;?php print_r(get_defined_vars()); ?&gt;</code>. This will give you a full list of all PHP variables available to you in this email. The <code>$user</code> variable is the most important one. It\'s an instance of the <a href="http://codex.wordpress.org/Class_Reference/WP_User" target="_blank" rel="external"><code>WP_User</code></a> class (e.g., <code>$user->ID</code>, <code>$user->has_cap()</code>, etc). Please note that all Replacement Codes will be parsed first, and then any PHP tags that you\'ve included. Also, please remember that emails are sent in plain text format.</p>'."\n"
					: '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<h3 style="margin:0;">Administrative: New User Notification (<a href="#" onclick="jQuery(\'div#ws-plugin--s2member-new-user-admin-email-details\').toggle(); return false;" class="ws-dotted-link">click to customize</a>)</h3>'."\n";
				echo '<p style="margin:0;">This email notification is sent to you, each time a new User/Member registers.</p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_new_user_admin_email", get_defined_vars());

				echo '<div id="ws-plugin--s2member-new-user-admin-email-details" style="display:none;">'."\n";
				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-new-user-admin-email-recipients">'."\n";
				echo 'New User Notification Recipients:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_new_user_admin_email_recipients" id="ws-plugin--s2member-new-user-admin-email-recipients" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["new_user_admin_email_recipients"]).'" /><br />'."\n";
				echo 'This is a semicolon ( ; ) delimited list of Recipients. Here is an example:<br />'."\n";
				echo '<code>"Name" &lt;user@example.com&gt;; admin@example.com; "Webmaster" &lt;webmaster@example.com&gt;</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-new-user-admin-email-subject">'."\n";
				echo 'New User Notification Subject:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_new_user_admin_email_subject" id="ws-plugin--s2member-new-user-admin-email-subject" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["new_user_admin_email_subject"]).'" /><br />'."\n";
				echo 'Subject Line used in the email notification sent to Administrator.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-new-user-admin-email-message">'."\n";
				echo 'New User Notification Message:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_new_user_admin_email_message" id="ws-plugin--s2member-new-user-admin-email-message" rows="10">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["new_user_admin_email_message"]).'</textarea><br />'."\n";
				echo 'Message Body used in the email notification sent to Administrator.<br /><br />'."\n";
				echo '<strong>You can also use these special Replacement Codes if you need them:</strong>'."\n";
				echo '<ul>'."\n";
				echo '<li><code>%%role%%</code> = The Role ID <code>(subscriber, s2member_level[0-9]+, administrator, editor, author, contributor)</code>.</li>'."\n";
				echo '<li><code>%%label%%</code> = The Role ID Label <code>(Subscriber, s2Member Level 1, s2Member Level 2; or your own custom Labels—if configured)</code>.</li>'."\n";
				echo '<li><code>%%level%%</code> = The Level number <code>(0, 1, 2, 3, 4)</code>. (<em>deprecated, no longer recommended; use <code>%%role%%</code></em>)</li>'."\n";
				echo '<li><code>%%ccaps%%</code> = Custom Capabilities. Ex: <code>music,videos,free_gift</code> (<em>in comma-delimited format</em>).</li>'."\n";
				echo '<li><code>%%user_first_name%%</code> = The First Name of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_last_name%%</code> = The Last Name of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_full_name%%</code> = The Full Name (First &amp; Last) of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_email%%</code> = The Email Address of the Member who registered their Username.</li>'."\n";
				echo '<li><code>%%user_login%%</code> = The Username the Member selected during registration.</li>'."\n";
				echo '<li><code>%%user_ip%%</code> = The User\'s IP Address, detected via <code>$_SERVER["REMOTE_ADDR"]</code>.</li>'."\n";
				echo '<li><code>%%user_id%%</code> = A unique WordPress User ID generated during registration.</li>'."\n";
				echo '<li><code>%%wp_login_url%%</code> = The full URL where Users can get logged into your site.</li>'."\n";
				echo '<li style="margin-top:1em;"><code style="text-decoration:line-through;">%%user_pass%%</code> = <em>Deprecated. Starting w/ WordPress v4.3, this is no longer applicable (or advised).</em></li>'."\n";
				echo '</ul>'."\n";

				echo '<strong>Custom Registration/Profile Fields are also supported in this email:</strong>'."\n";
				echo '<ul>'."\n";
				echo '<li><code>%%date_of_birth%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>date_of_birth</code>.</li>'."\n";
				echo '<li><code>%%street_address%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>street_address</code>.</li>'."\n";
				echo '<li><code>%%country%%</code> would be valid; if you have a Custom Registration/Profile Field with the ID <code>country</code>.</li>'."\n";
				echo '<li><em><code>%%etc, etc...%%</code> <strong>see:</strong> s2Member → General Options → Registration/Profile Fields</em>.</li>'."\n";
				echo '</ul>'."\n";

				echo '<strong>Custom Replacement Codes can also be inserted using these instructions:</strong>'."\n";
				echo '<ul>'."\n";
				echo '<li><code>%%cv0%%</code> = The domain of your site, which is passed through the `custom` attribute in your Shortcode.</li>'."\n";
				echo '<li><code>%%cv1%%</code> = If you need to track additional custom variables, you can pipe delimit them into the `custom` attribute; inside your Shortcode, like this: <code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|cv1|cv2|cv3"</code>. You can have an unlimited number of custom variables. Obviously, this is for advanced webmasters; but the functionality has been made available for those who need it.</li>'."\n";
				echo '</ul>'."\n";
				echo '<strong>This example uses cv1 to record a special marketing campaign:</strong><br />'."\n";
				echo '<em>(The campaign (i.e., christmas-promo) could be referenced using <code>%%cv1%%</code>)</em><br />'."\n";
				echo '<code>custom="'.esc_html($_SERVER["HTTP_HOST"]).'|christmas-promo"</code>'."\n";

				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ?
					'<div class="ws-menu-page-hr"></div>'."\n".
					'<p style="margin:0;"><strong>PHP Code:</strong> It is also possible to use PHP tags—optional (for developers). If you use PHP tags, please run a test email with <code>&lt;?php print_r(get_defined_vars()); ?&gt;</code>. This will give you a full list of all PHP variables available to you in this email. The <code>$user</code> variable is the most important one. It\'s an instance of the <a href="http://codex.wordpress.org/Class_Reference/WP_User" target="_blank" rel="external"><code>WP_User</code></a> class (e.g., <code>$user->ID</code>, <code>$user->has_cap()</code>, etc). Please note that all Replacement Codes will be parsed first, and then any PHP tags that you\'ve included. Also, please remember that emails are sent in plain text format.</p>'."\n"
					: '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_email_config", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_open_registration", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_open_registration", get_defined_vars());

				if(is_multisite() && is_main_site()) // A Multisite Network, and we're on the Main Site?
				{
					echo '<div class="ws-menu-page-group" title="Open Registration">'."\n";

					echo '<div class="ws-menu-page-section ws-plugin--s2member-open-registration-section">'."\n";
					echo '<h3>Open Registration / Free Subscribers (optional)</h3>'."\n";
					echo '<p>On the Main Site of a Multisite Network, the settings for Open Registration are consolidated into the <strong>s2Member → Multisite (Config)</strong> panel.</p>'."\n";
					do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_open_registration", get_defined_vars());
					echo '</div>'."\n";

					echo '</div>'."\n";
				}
				else // Else we display this section normally. No special considerations are required in this case.
				{
					echo '<div class="ws-menu-page-group" title="Open Registration">'."\n";

					echo '<div class="ws-menu-page-section ws-plugin--s2member-open-registration-section">'."\n";
					echo '<h3>Open Registration / Free Subscribers (optional)</h3>'."\n";
					echo '<p>s2Member supports Free Subscribers (at Level #0), along with four Primary Levels [1-4] of paid Membership. If you want your visitors to be capable of registering absolutely free, you will want to "allow" Open Registration. Whenever a visitor registers without paying, they\'ll automatically become a Free Subscriber at Level #0.</p>'."\n";
					do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_open_registration", get_defined_vars());

					echo '<table class="form-table">'."\n";
					echo '<tbody>'."\n";
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--s2member-allow-subscribers-in">'."\n";
					echo 'Allow Open Registration? (Free Subscribers)'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<select name="ws_plugin__s2member_allow_subscribers_in" id="ws-plugin--s2member-allow-subscribers-in">'."\n";
					echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["allow_subscribers_in"]) ? ' selected="selected"' : '').'>No (do NOT allow Open Registration)</option>'."\n";
					echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["allow_subscribers_in"]) ? ' selected="selected"' : '').'>Yes (allow Open Registration; Free Subscribers at Level #0)</option>'."\n";
					echo '</select><br />'."\n";
					echo 'If you set this to <code>Yes</code>, you\'re unlocking <a href="'.esc_attr(c_ws_plugin__s2member_utils_urls::wp_register_url()).'" target="_blank" rel="external" onclick="alert(\'s2Member will now open your Standard Registration Form.\\n* s2Member makes this form available to logged-in Administrators, at all times (for testing purposes), regardless of configuration.'.((c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '\\n\\nBuddyPress: BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( '.c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_urls::bp_register_url()).' ), because you\\\'re already logged-in. Please log out before testing BuddyPress registration.' : '').'\');">/wp-login.php?action=register</a>. When a visitor registers without paying, they\'ll automatically become a Free Subscriber at Level #0. The s2Member software reserves Level #0; to be used only for Free Subscribers. All other Membership Levels [1-4] require payment.'."\n";
					echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<br /><br /><em><strong>BuddyPress:</strong> BuddyPress will use its own Registration Form <a href="'.esc_attr(c_ws_plugin__s2member_utils_urls::bp_register_url()).'" target="_blank" rel="external" onclick="alert(\'s2Member will now open your BuddyPress Registration Form.\\n* However, you will probably be redirected away from this BuddyPress Registration Form ( '.c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_urls::bp_register_url()).' ), because you\\\'re already logged-in. Please log out before testing BuddyPress registration.\');">here</a>.<br />s2Member integrates with BuddyPress, and the above setting will control Open Regisration for BuddyPress too.</em>'."\n" : '';
					echo '</td>'."\n";

					echo '</tr>'."\n";
					echo '</tbody>'."\n";
					echo '</table>'."\n";
					echo '</div>'."\n";

					echo '</div>'."\n";
				}
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_open_registration", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_membership_levels", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_membership_levels", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Membership Levels/Labels">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-membership-levels-section">'."\n";
				echo '<h3>Membership Levels (required, please customize these)</h3>'."\n";
				echo '<p>The default Membership Levels are labeled generically; feel free to modify them as needed. s2Member supports Free Subscribers <em>(at Level #0)</em>, along with several Primary Roles for paid Membership <em>(i.e., Levels 1-4)</em>, created by the s2Member plugin.'.((!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? ' s2Member also supports unlimited Custom Capability Packages <em>(see <strong>s2Member → API Scripting → Custom Capabilities</strong>)</em>' : '').'. That being said, you don\'t have to use all of the Membership Levels if you don\'t want to. To use only 1 or 2 of these Levels, just design your Membership Options Page, so it only includes Payment Buttons for the Levels being used.</p>'."\n";
				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? '<p><em><strong>TIP:</strong> <strong>Unlimited Membership Levels</strong> are only possible with <a href="'.esc_attr(c_ws_plugin__s2member_readmes::parse_readme_value("Pro Add-on / Prices")).'" target="_blank" rel="external">s2Member Pro</a>. However, Custom Capabilities are possible in all versions of s2Member, including the free version. Custom Capabilities are a great way to extend s2Member in creative ways. If you\'re an advanced site owner, a theme designer, or a web developer integrating s2Member for a client, please check your Dashboard, under: <strong>s2Member → API Scripting → Custom Capabilities</strong>. We also recommend <a href="http://www.s2member.com/videos/A2C07377CF60025E/" target="_blank" rel="external">this video tutorial</a>.</em></p>'."\n" : '';
				echo (!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? '<p><strong>See also:</strong> These KB articles: <a href="http://www.s2member.com/kb/roles-caps/" target="_blank" rel="external">s2Member Roles/Capabilities</a> and <a href="http://www.s2member.com/kb/simple-shortcode-conditionals/" target="_blank" rel="external">Simple Shortcode Conditionals</a>.</p>'."\n" : '';
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_membership_levels", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";

				for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
				{
					echo '<tr>'."\n";

					echo '<th>'."\n";
					echo '<label for="ws-plugin--s2member-level'.$n.'-label">'."\n";
					echo ($n === 0) ? 'Level #'.$n.' <em>(Free Subscribers)</em>:'."\n" : 'Level #'.$n.' Members:'."\n";
					echo '</label>'."\n";
					echo '</th>'."\n";

					echo '</tr>'."\n";
					echo '<tr>'."\n";

					echo '<td>'."\n";
					echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_level'.$n.'_label" id="ws-plugin--s2member-level'.$n.'-label" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_label"]).'" /><br />'."\n";
					echo 'This is the Label for Level #'.$n.(($n === 0) ? ' (Free Subscribers)' : ' Members').'.<br />'."\n";
					echo '</td>'."\n";

					echo '</tr>'."\n";
				}
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th style="padding-top:0;">'."\n";
				echo '<label for="ws-plugin--s2member-apply-label-translations">'."\n";
				echo 'Force WordPress to use your Labels?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="radio" name="ws_plugin__s2member_apply_label_translations" id="ws-plugin--s2member-apply-label-translations-0" value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["apply_label_translations"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-apply-label-translations-0">No</label> &nbsp;&nbsp;&nbsp; <input type="radio" name="ws_plugin__s2member_apply_label_translations" id="ws-plugin--s2member-apply-label-translations-1" value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["apply_label_translations"]) ? ' checked="checked"' : '').' /> <label for="ws-plugin--s2member-apply-label-translations-1">Yes, force WordPress to use my Labels.</label><br />'."\n";
				echo 'This affects your administrative Dashboard only <em>(i.e., your list of Users)</em>.<br />s2Member can force WordPress to use your Labels instead of referencing Roles by `s2Member Level #`. If this is your first installation of s2Member, we suggest leaving this set to <code>no</code> until you\'ve had a chance to get acclimated with s2Member\'s functionality. In fact, many site owners choose to leave this off, because they find it less confusing when Roles are referred to by their s2Member Level #.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<input type="button" value="Reset Roles/Capabilities" class="ws-menu-page-right ws-plugin--s2member-reset-roles-button" style="min-width:175px;" />'."\n";
				echo '<p>The button to the right, is a nifty tool, which allows you to reset s2Member\'s internal Roles and Capabilities that integrate with WordPress. If you, or a developer working with you, has made attempts to alter the default <em>internal</em> Role/Capability sets that come with s2Member, and you need to reset them back to the way s2Member expects them to be, please use this tool. <em>Attn Developers: it is also possible lock-in your modified Roles/Capabilities with an s2Member Filter. Please see <a href="http://s2member.com/kb-article/locking-s2member-rolescapabilities/" target="_blank" rel="external">this KB article for details</a>.</em></p>'."\n";

				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_membership_levels", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_login_registration", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_login_registration", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Login/Registration Design">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-login-registration-section">'."\n";
				echo '<h3>Login/Registration Page Customization (optional)</h3>'."\n";
				echo '<p>These settings customize your Standard Login/Registration Pages:<br />(<a href="'.esc_attr(c_ws_plugin__s2member_utils_urls::wp_register_url()).'" target="_blank" rel="external" onclick="alert(\'s2Member will now open your Standard Registration Form.\\n* s2Member makes this form available to logged-in Administrators, at all times (for testing purposes), regardless of configuration.'.((c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( '.c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_urls::bp_register_url()).' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '').'\');">'.esc_html(c_ws_plugin__s2member_utils_urls::wp_register_url()).'</a>)</p>'."\n";
				echo (is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && is_main_site()) ? '<p><em>The Main Site of a Multisite Blog Farm uses this Form instead, powered by your theme.<br />(<a href="'.esc_attr(c_ws_plugin__s2member_utils_urls::wp_signup_url()).'" target="_blank" rel="external" onclick="alert(\'s2Member will now open your Multisite Registration Form.\\n* s2Member makes this form available to logged-in Super Administrators, at all times (for testing purposes), regardless of configuration.'.((c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( '.c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_urls::bp_register_url()).' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '').'\');">'.esc_html(c_ws_plugin__s2member_utils_urls::wp_signup_url()).'</a>)</em></p>'."\n" : '';
				echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<p><em><strong>BuddyPress:</strong> BuddyPress will use its own Registration Form, powered by your theme.<br />(<a href="'.esc_attr(c_ws_plugin__s2member_utils_urls::bp_register_url()).'" target="_blank" rel="external" onclick="alert(\'s2Member will now open your BuddyPress Registration Form.\\n* However, you will probably be redirected away from this BuddyPress Registration Form ( '.c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_urls::bp_register_url()).' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.\');">'.esc_html(c_ws_plugin__s2member_utils_urls::bp_register_url()).'</a>)</em></p>'."\n" : '';
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_login_registration", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<h3 style="margin:0;">Enable This Functionality?</h3>'."\n";
				echo '<select name="ws_plugin__s2member_login_reg_design_enabled" id="ws-plugin--s2member-login-reg-design-enabled">'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_design_enabled"]) ? ' selected="selected"' : '').'>No (default, use WordPress defaults)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_design_enabled"]) ? ' selected="selected"' : '').'>Yes (customize Login/Registration with s2Member)</option>'."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div id="ws-plugin--s2member-login-reg-design"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_design_enabled"]) ? ' style="display:none;"' : '').'>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<h3 style="margin:0;">Overall Font/Size Configuration</h3>'."\n";
				echo '<p style="margin:0;">These settings are all focused on your Login/Registration Fonts.</p>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-font-size">'."\n";
				echo 'Overall Font Size:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_font_size" id="ws-plugin--s2member-login-reg-font-size" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_font_size"]).'" /><br />'."\n";
				echo 'Set this to a numeric value, calculated in pixels.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-font-family">'."\n";
				echo 'Overall Font Family:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_font_family" id="ws-plugin--s2member-login-reg-font-family" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_font_family"]).'" /><br />'."\n";
				echo 'Set this to a web-safe font family.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-font-field-size">'."\n";
				echo 'Form Field Font Size:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_font_field_size" id="ws-plugin--s2member-login-reg-font-field-size" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_font_field_size"]).'" /><br />'."\n";
				echo 'Set this to a numeric value, calculated in pixels.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<h3 style="margin:0;">Background Configuration</h3>'."\n";
				echo '<p style="margin:0;">These settings are all focused on your Login/Registration Background.</p>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-background-color">'."\n";
				echo 'Background Color:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_background_color" id="ws-plugin--s2member-login-reg-background-color" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_color"]).'" /><br />'."\n";
				echo 'Set this to a 6-digit hex color code.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-background-image">'."\n";
				echo 'Background Image:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_background_image" id="ws-plugin--s2member-login-reg-background-image" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_image"]).'" /><br />'."\n";
				echo '<input type="button" id="ws-plugin--s2member-login-reg-background-image-media-btn" value="Open Media Library" class="ws-menu-page-media-btn" rel="ws-plugin--s2member-login-reg-background-image" />'."\n";
				echo 'Set this to the URL of your Background Image. (this is optional)<br />';
				echo 'If supplied, your Background Image will be tiled.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-background-image-repeat">'."\n";
				echo 'Background Image Tile:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_login_reg_background_image_repeat" id="ws-plugin--s2member-login-reg-background-image-repeat">'."\n";
				echo '<option value="repeat"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_image_repeat"] === "repeat") ? ' selected="selected"' : '').'>Seamless Tile ( background-repeat: repeat; )</option>'."\n";
				echo '<option value="repeat-x"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_image_repeat"] === "repeat-x") ? ' selected="selected"' : '').'>Tile Horizontally ( background-repeat: repeat-x; )</option>'."\n";
				echo '<option value="repeat-y"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_image_repeat"] === "repeat-y") ? ' selected="selected"' : '').'>Tile Vertically ( background-repeat: repeat-y; )</option>'."\n";
				echo '<option value="no-repeat"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_image_repeat"] === "no-repeat") ? ' selected="selected"' : '').'>No Tiles ( background-repeat: no-repeat; )</option>'."\n";
				echo '</select><br />'."\n";
				echo 'This controls the way your Background Image is styled with CSS. [ <a href="http://s2member.com/r/css-background-repeat/" target="_blank" rel="external">learn more</a> ]'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-background-text-color">'."\n";
				echo 'Color of Text on top of your Background:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_background_text_color" id="ws-plugin--s2member-login-reg-background-text-color" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_text_color"]).'" /><br />'."\n";
				echo 'Set this to a 6-digit hex color code.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-background-text-shadow-color">'."\n";
				echo 'Shadow Color for Text on top of your Background:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_background_text_shadow_color" id="ws-plugin--s2member-login-reg-background-text-shadow-color" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_text_shadow_color"]).'" /><br />'."\n";
				echo 'Set this to a 6-digit hex color code.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-background-box-shadow-color">'."\n";
				echo 'Shadow Color for Boxes on top of your Background:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_background_box_shadow_color" id="ws-plugin--s2member-login-reg-background-box-shadow-color" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_background_box_shadow_color"]).'" /><br />'."\n";
				echo 'Set this to a 6-digit hex color code.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<h3 style="margin:0;">Logo Image Configuration</h3>'."\n";
				echo '<p style="margin:0;">These settings are all focused on your Login/Registration Logo.</p>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-logo-src">'."\n";
				echo 'Logo Image Location:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_logo_src" id="ws-plugin--s2member-login-reg-logo-src" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_logo_src"]).'" /><br />'."\n";
				echo '<input type="button" id="ws-plugin--s2member-login-reg-logo-src-media-btn" value="Open Media Library" class="ws-menu-page-media-btn" rel="ws-plugin--s2member-login-reg-logo-src" />'."\n";
				echo 'Set this to the URL of your Logo Image.<br />'."\n";
				echo 'Suggested size is around 500 x 100.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-logo-src-width">'."\n";
				echo 'Logo Image Width:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_logo_src_width" id="ws-plugin--s2member-login-reg-logo-src-width" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_logo_src_width"]).'" /><br />'."\n";
				echo 'The pixel Width of your Logo Image. <em>* This ALSO affects the overall width of your Login/Registration forms. If you want wider form fields, use a wider Logo.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-logo-src-height">'."\n";
				echo 'Logo Image Height:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_logo_src_height" id="ws-plugin--s2member-login-reg-logo-src-height" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_logo_src_height"]).'" /><br />'."\n";
				echo 'The pixel Height of your Logo Image.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-logo-url">'."\n";
				echo 'Logo Image Click URL:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_logo_url" id="ws-plugin--s2member-login-reg-logo-url" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_logo_url"]).'" /><br />'."\n";
				echo 'Set this to the Click URL for your Logo Image.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-logo-title">'."\n";
				echo 'Logo Image Title Attribute:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_reg_logo_title" id="ws-plugin--s2member-login-reg-logo-title" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_logo_title"]).'" /><br />'."\n";
				echo 'Used as the <code>title=""</code> attribute for your Logo Image.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-footer-backtoblog">'."\n";
				echo 'Display [Back to Home Page] Link At Bottom?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_login_reg_footer_backtoblog" id="ws-plugin--s2member-login-reg-footer-backtoblog">'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_footer_backtoblog"]) ? ' selected="selected"' : '').'>Yes, display link at bottom pointing visitors back to the home page</option>'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_footer_backtoblog"]) ? ' selected="selected"' : '').'>No, hide this link (I\'ll create my own custom footer w/ the details I prefer)</option>'."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<h3 style="margin:0;">Footer Design (i.e., Bottom)</h3>'."\n";
				echo '<p style="margin:0;">This field accepts raw HTML'.((!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? ' (and/or PHP)' : '').' code.</p>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-reg-footer-design">'."\n";
				echo 'Login/Registration Footer Design (optional):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<textarea name="ws_plugin__s2member_login_reg_footer_design" id="ws-plugin--s2member-login-reg-footer-design" rows="3" wrap="off" spellcheck="false">'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_reg_footer_design"]).'</textarea><br />'."\n";
				echo 'This optional HTML'.((!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? ' (and/or PHP)' : '').' code will appear at the very bottom of your Login/Registration Forms.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_login_registration", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_custom_reg_fields", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_custom_reg_fields", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Registration/Profile Fields &amp; Options">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-custom-reg-fields-section">'."\n";
				echo '<h3>Custom Registration/Profile Fields (optional, for further customization)</h3>'."\n";
				echo '<p>Some fields are already built-in by default. The defaults are: <code>*Username*, *Email*, *First Name*, *Last Name*</code>.</p>'."\n";

				echo '<p>Custom Fields will appear in your Standard Registration Form, and in User/Member Profiles:<br />(<a href="'.esc_attr(c_ws_plugin__s2member_utils_urls::wp_register_url()).'" target="_blank" rel="external" onclick="alert(\'s2Member will now open your Standard Registration Form.\\n* s2Member makes this form available to logged-in Administrators, at all times (for testing purposes), regardless of configuration.'.((c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( '.c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_urls::bp_register_url()).' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '').'\');">'.esc_html(c_ws_plugin__s2member_utils_urls::wp_register_url()).'</a>)</p>'."\n";
				echo (is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && is_main_site()) ? '<p><em>The Main Site of a Multisite Blog Farm uses this Form. s2Member supports Custom Fields here too.<br />(<a href="'.esc_attr(c_ws_plugin__s2member_utils_urls::wp_signup_url()).'" target="_blank" rel="external" onclick="alert(\'s2Member will now open your Multisite Registration Form.\\n* s2Member makes this form available to logged-in Super Administrators, at all times (for testing purposes), regardless of configuration.'.((c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '\\n\\nBuddyPress: * BuddyPress will use its own Registration Form. Please note, you will probably be redirected away from the BuddyPress Registration Form ( '.c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_urls::bp_register_url()).' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.' : '').'\');">'.esc_html(c_ws_plugin__s2member_utils_urls::wp_signup_url()).'</a>)</em></p>'."\n" : '';
				echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<p><em><strong>BuddyPress:</strong> BuddyPress will use its own Registration Form <a href="'.esc_attr(c_ws_plugin__s2member_utils_urls::bp_register_url()).'" target="_blank" rel="external" onclick="alert(\'s2Member will now open your BuddyPress Registration Form.\\n* However, you will probably be redirected away from this BuddyPress Registration Form ( '.c_ws_plugin__s2member_utils_strings::esc_js_sq(c_ws_plugin__s2member_utils_urls::bp_register_url()).' ), because you\\\'re ALREADY logged-in. Please log out before testing BuddyPress registration.\');">here</a>.<br />s2Member can integrate your Custom Fields with BuddyPress too, please see options below.</em></p>'."\n" : '';
				echo '<p><strong>Regarding Registration:</strong> Custom Fields do not appear during repeat registration and/or checkout attempts (i.e., they do not appear for any user that is currently logged into the site). Please make sure that you test registration and/or checkout forms while not logged in (i.e., please test as a first-time customer). Existing users, members, customers may update Custom Fields by editing their Profile.</p>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_custom_reg_fields", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label>'."\n";
				echo 'Custom Registration/Profile Fields:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="hidden" name="ws_plugin__s2member_custom_reg_fields" id="ws-plugin--s2member-custom-reg-fields" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields"]).'" />'."\n";
				echo '<div id="ws-plugin--s2member-custom-reg-field-configuration"></div>'."\n"; // This is filled by JavaScript routines.
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-custom-reg-names">'."\n";
				echo 'Collect First/Last Names During Registration?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_custom_reg_names" id="ws-plugin--s2member-custom-reg-names">'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password"]) ? ' selected="selected"' : '').'>Yes (always collect First/Last Names during registration)</option>'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_names"]) ? ' selected="selected"' : '').'>No (do NOT collect First/Last Names during registration)</option>'."\n";
				echo '</select><br />'."\n";
				echo 'Recommended setting (<code>Yes</code>). It\'s usually a good idea to leave this on.'."\n";
				echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<br /><em>* Has no affect on BuddyPress registration form (BuddyPress always collects a full <code>Name</code> field).</em>'."\n" : '';
				echo (c_ws_plugin__s2member_utils_conds::pro_is_installed()) ? '<br /><em>* s2Member Pro (Checkout) Forms always require a First/Last Name for billing.</em>'."\n" : '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-custom-reg-display-name">'."\n";
				echo 'Set "Display Name" During Registration?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_custom_reg_display_name" id="ws-plugin--s2member-custom-reg-display-name">'."\n";
				echo '<option value="full"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_display_name"] === "full") ? ' selected="selected"' : '').'>Yes (set Display Name to User\'s Full Name)</option>'."\n";
				echo '<option value="first"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_display_name"] === "first") ? ' selected="selected"' : '').'>Yes (set Display Name to User\'s First Name)</option>'."\n";
				echo '<option value="last"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_display_name"] === "last") ? ' selected="selected"' : '').'>Yes (set Display Name to User\'s Last Name)</option>'."\n";
				echo '<option value="login"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_display_name"] === "login") ? ' selected="selected"' : '').'>Yes (set Display Name to User\'s Username)</option>'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_display_name"]) ? ' selected="selected"' : '').'>No (leave Display Name at default WordPress value)</option>'."\n";
				echo '</select>'."\n";
				echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<br /><em>* Has no affect on BuddyPress registration form (BuddyPress always uses its full <code>Name</code> field).</em>'."\n" : '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-custom-reg-password">'."\n";
				echo 'Allow Custom Passwords During Registration?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<p style="margin-bottom:4px;"><em><strong>Note:</strong> Custom Passwords are easier for users. However, this has an impact on site security. Enabling Custom Passwords disables the New User Notification email that contains a password-setup link for each user. In other words, enabling Custom Passwords effectively disables any sort of email verification procedure, because the user is allowed to set their password during registration, instead of doing that via email confirmation—which is the default WordPress behavior.</em></p>'."\n";
				echo (is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && is_main_site()) ? '<p style="margin-bottom:4px;"><em>* For security purposes, Custom Passwords are not possible on the Main Site of a Blog Farm. <a href="#" onclick="alert(\'For security purposes, Custom Passwords are not possible on the Main Site of a Blog Farm. A User must wait for the activation/confirmation email; where a randomly generated Password will be assigned. Please note, this limitation only affects your Main Site, via `/wp-signup.php`. In other words, your Customers (i.e., other Blog Owners) will still have the ability to allow Custom Passwords with s2Member. YOU are affected by this limitation, NOT them.\\n\\n* NOTE: s2Member (Pro) removes this limitation.\\nIf you install the s2Member Pro Add-on, you WILL be able to allow Custom Passwords through s2Member Pro-Forms; even on a Multisite Blog Farm.\'); return false;" tabindex="-1">[?]</a></em></p>'."\n" : '';
				echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<p style="margin-bottom:4px;"><em>* Does not affect BuddyPress registration form (always <code>yes</code> with BuddyPress registration).</em></p>'."\n" : '';
				echo '<select name="ws_plugin__s2member_custom_reg_password" id="ws-plugin--s2member-custom-reg-password"'.((is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && is_main_site() && !c_ws_plugin__s2member_utils_conds::pro_is_installed()) ? ' disabled="disabled"' : '').'>'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password"]) ? ' selected="selected"' : '').'>No (send each user a password-setup link after registration; recommended for best security)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password"]) ? ' selected="selected"' : '').'>Yes (allow members to create their own password during registration)</option>'."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-custom-reg-password-min-length">'."\n";
				echo 'Minimum Length/Strength for Custom Passwords:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo 'Minimum length: <input type="text" autocomplete="off" name="ws_plugin__s2member_custom_reg_password_min_length" id="ws-plugin--s2member-custom-reg-password-min-length" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password_min_length"]).'" maxlength="2" size="2" style="width:auto;" /> characters.'."\n";
				echo '&nbsp;&nbsp;&nbsp; Minimum strength: <select name="ws_plugin__s2member_custom_reg_password_min_strength" id="ws-plugin--s2member-custom-reg-password-min-strength" style="width:auto;">'."\n";
				echo '<option value="n/a"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password_min_strength"] === 'n/a') ? ' selected="selected"' : '').'>N/A (do not enforce a password strength requirement)</option>'."\n";
				echo '<option value="weak"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password_min_strength"] === 'weak') ? ' selected="selected"' : '').'>Weak (only needs to meet minimum length requirement)</option>'."\n";
				echo '<option value="good"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password_min_strength"] === 'good') ? ' selected="selected"' : '').'>Good (must have numbers, letters, and mixed caSe)</option>'."\n";
				echo '<option value="strong"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_password_min_strength"] === 'strong') ? ' selected="selected"' : '').'>Strong (must have numbers, letters, mixed caSe, and punctuation)</option>'."\n";
				echo '</select>'."\n";
				echo '<p><em><strong>Tip:</strong> Minimum length and password strength also impact profile updates, so it\'s a good idea to configure these even if you\'re not using Custom Passwords during registration.</em></p>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-custom-reg-force-personal-emails">'."\n";
				echo 'Force Personal Emails During Registration?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_custom_reg_force_personal_emails" id="ws-plugin--s2member-custom-reg-force-personal-emails" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_force_personal_emails"]).'" /><br />'."\n";
				echo 'To force personal email addresses, provide a comma-delimited list of email users to reject. <a href="#" onclick="alert(\'s2Member will reject [user]@ (based on your configuration here). A JavaScript alert message will be issued, asking the User to, `please use a personal email address`.\'); return false;" tabindex="-1">[?]</a><br />'."\n";
				echo 'Ex: <code>info,help,admin,webmaster,hostmaster,sales,support,spam</code><br />'."\n";
				echo 'See: <a href="http://s2member.com/r/mailchimp-role-based-emails/" target="_blank" rel="external">this article</a> for a more complete list.'."\n";
				echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<br /><em>* Affects BuddyPress registration form too.</em>'."\n" : '';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-custom-reg-fields-4bp">'."\n";
				echo 'Integrate Custom Registration/Profile Fields with BuddyPress?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<div class="ws-menu-page-scrollbox" style="height:65px;">'."\n";
				echo '<input type="hidden" name="ws_plugin__s2member_custom_reg_fields_4bp[]" value="update-signal"'.((!c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? ' disabled="disabled"' : '').' />'."\n";
				foreach(array("profile-view" => "Yes, integrate with BuddyPress Public Profiles.", "registration" => "Yes, integrate with BuddyPress Registration Form.", "profile" => "Yes, integrate with BuddyPress Profile Editing Panel.") as $ws_plugin__s2member_temp_s_value => $ws_plugin__s2member_temp_s_label)
					echo '<input type="checkbox" name="ws_plugin__s2member_custom_reg_fields_4bp[]" id="ws-plugin--s2member-custom-reg-fields-4bp-'.esc_attr(preg_replace("/[^a-z0-9_\-]/", "-", $ws_plugin__s2member_temp_s_value)).'" value="'.esc_attr($ws_plugin__s2member_temp_s_value).'"'.((in_array($ws_plugin__s2member_temp_s_value, $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["custom_reg_fields_4bp"])) ? ' checked="checked"' : '').((!c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? ' disabled="disabled"' : '').' /> <label for="ws-plugin--s2member-custom-reg-fields-4bp-'.esc_attr(preg_replace("/[^a-z0-9_\-]/", "-", $ws_plugin__s2member_temp_s_value)).'">'.$ws_plugin__s2member_temp_s_label.'</label><br />'."\n";
				echo '</div>'."\n";
				echo (!c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<em>* BuddyPress is not installed; which is perfectly OK. BuddyPress is not a requirement.</em>'."\n" : '<em>* The options above, make it possible to integrate Custom Registration/Profile Fields (i.e., those configured with s2Member) into BuddyPress as well. However, if you configure Profile Fields with BuddyPress, those will NOT be integrated with s2Member. Therefore, if you need Custom Registration/Profile Fields to work with both s2Member and with BuddyPress, please configure them with s2Member.</em>';
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_custom_reg_fields", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_login_welcome_page", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_login_welcome_page", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Login Welcome Page">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-login-welcome-page-section">'."\n";
				echo '<h3>Login Welcome Page (required, please customize this)</h3>'."\n";
				echo '<p>Please create and/or choose an existing Page to use as the first page Members will see after logging in.</p>'."\n";
				echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<p><em><strong>BuddyPress:</strong> s2Member integrates with BuddyPress. Your Login Welcome Page affects BuddyPress too.</em></p>'."\n" : '';
				echo '<p><em><strong>Always Private:</strong> This Page will always require a logged-in User/Member. In fact, this Page will be protected from public access by s2Member automatically. <strong>Note:</strong> for technical reasons, your Login Welcome Page <strong>cannot</strong> be set to your Front Page (i.e., your Home Page); or your Posts Page (i.e., your main Blog page). Please create a separate (dedicated) Page in WordPress, and then designate it as your Login Welcome Page below.</em></p>'."\n";
				echo '<p><strong>See also:</strong> This KB article: <a href="http://www.s2member.com/kb/customizing-your-lwp/" target="_blank" rel="external">Customizing Your Login Welcome Page</a>.</p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_login_welcome_page", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-welcome-page">'."\n";
				echo 'Login Welcome Page:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_login_welcome_page" id="ws-plugin--s2member-login-welcome-page">'."\n";
				echo '<option value="">&mdash; Select &mdash;</option>'."\n";
				foreach(($ws_plugin__s2member_temp_a = array_merge((array)get_pages())) as $ws_plugin__s2member_temp_o)
					echo '<option value="'.esc_attr($ws_plugin__s2member_temp_o->ID).'"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_redirection_override"] && $ws_plugin__s2member_temp_o->ID == $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_welcome_page"]) ? ' selected="selected"' : '').'>'.esc_html($ws_plugin__s2member_temp_o->post_title).'</option>'."\n";
				echo '</select><br />'."\n";
				echo 'Please choose a Page to be used as the first page Members will see after logging in. This Page can contain anything you like. We recommend the following title: <code>Welcome To Our Members Area</code>.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-redirection-override">'."\n";
				echo 'Or, a Special Redirection URL?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="text" autocomplete="off" name="ws_plugin__s2member_login_redirection_override" id="ws-plugin--s2member-login-redirection-override" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_redirection_override"]).'" /><br />'."\n";
				echo 'Or, you may configure a Special Redirection URL, if you prefer. You\'ll need to type in the full URL, starting with: <code>http://</code>. <em>A few <a href="#" onclick="alert(\'Replacement Codes:\\n\\n%%current_user_login%% = The current User\\\'s Username, lowercase (deprecated, please use %%current_user_nicename%%).\\n\\n%%current_user_nicename%% = The current User\\\'s Nicename in lowercase format (i.e., a cleaner version of the username for URLs; recommended for best compatibility).\\n\\n%%current_user_id%% = The current User\\\'s ID.\\n\\n%%current_user_level%% = The current User\\\'s s2Member Level.\\n\\n%%current_user_role%% = The current User\\\'s WordPress Role.'.((!is_multisite() || !c_ws_plugin__s2member_utils_conds::is_multisite_farm() || is_main_site()) ? '\\n\\n%%current_user_ccaps%% = The current User\\\'s Custom Capabilities.' : '').'\\n\\n%%current_user_logins%% = Number of times the current User has logged in.\\n\\nFor example, if you\\\'re using BuddyPress, and you want to redirect Members to their BuddyPress Profile page after logging in, you would setup a Special Redirection URL, like this: '.home_url("/members/%%current_user_nicename%%/profile/").'\\n\\nOr ... using %%current_user_level%%, you could have a separate Login Welcome Page for each Membership Level that you plan to offer. BuddyPress not required.\'); return false;">Replacement Codes</a> are also supported here.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table" style="margin-top:0;">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-login-redirection-always-http">'."\n";
				echo 'Always Redirect non-Administrative Users (after login) using HTTP?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_login_redirection_always_http" id="ws-plugin--s2member-login-redirection-always-http">'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_redirection_always_http"]) ? ' selected="selected"' : '').'>No, do NOT modify (use WordPress default behavior; i.e., detect URL scheme automatically)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["login_redirection_always_http"]) ? ' selected="selected"' : '').'>Yes, always redirect non-administrative users to non-SSL version (i.e., always use http://)</option>'."\n";
				echo '</select><br />'."\n";
				echo 'Recommended setting: <code>Yes</code>. This is compatible w/ <a href="http://codex.wordpress.org/Administration_Over_SSL" target="_blank" rel="external"><code>FORCE_SSL_LOGIN</code></a> and/or <a href="http://codex.wordpress.org/Administration_Over_SSL" target="_blank" rel="external"><code>FORCE_SSL_ADMIN</code></a>.'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_login_welcome_page", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_membership_options_page", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_membership_options_page", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Membership Options Page">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-membership-options-page-section">'."\n";
				echo '<h3>Membership Options Page (required, please customize this)</h3>'."\n";
				echo '<p>Please create and/or choose an existing Page that showcases your Membership Options. This special Page is where you\'ll insert the Payment Button(s) generated for you by s2Member (or Pro-Forms; if you\'re running s2Member Pro). This Page serves as your lead-in signup page <em>(i.e., you\'ll give visitors one or more registration options here, and they\'ll pay for the option they choose)</em>.</p>'."\n";
				echo '<p>Your Membership Options Page should detail all of the features that come with Membership to your site, and provide a Payment Button (or Pro-Form; if you\'re running s2Member Pro) for each Level of access you plan to offer. This is also the Page that anyone could be redirected to <em>(by s2Member)</em>, should they attempt to access an area of your site that requires access to something they\'re currenty not allowed to view.</p>'."\n";
				echo '<p><em><strong>Tip:</strong> If you allow Open Registration (i.e., Free Subscribers), you might want to place a link on your Membership Options Page, which points directly to your free Registration Form, instead of routing a Customer through your Payment Gateway first. It\'s a matter of preference though. For further details, please check the section above: <strong>s2Member → General Options → Open Registration</strong>.</em></p>'."\n";
				echo c_ws_plugin__s2member_utils_conds::bp_is_installed() ? '<p><em><strong>BuddyPress:</strong> Even with BuddyPress, s2Member still needs a Membership Options Page. This is where your Payment Buttons (or Pro-Forms; if you\'re running s2Member Pro) will go, giving people the ability to pay you. And again, this is also the Page that anyone could be redirected to <em>(by s2Member)</em>, should they attempt to access an area of your site that requires access to something they\'re currenty not allowed to view.</em></p>'."\n" : '';
				echo '<p><em><strong>Always Public:</strong> This Page must be public at all times. In fact, s2Member will not allow this Page to be restricted in any way. <strong>Note:</strong> for technical reasons, your Membership Options Page <strong>cannot</strong> be set to your Front Page (i.e., your Home Page); or your Posts Page (i.e., your main Blog page). Please create a separate (dedicated) Page in WordPress, and then designate it as your Membership Options Page below.</em></p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_membership_options_page", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-membership-options-page">'."\n";
				echo 'Membership Options Page:'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_membership_options_page" id="ws-plugin--s2member-membership-options-page">'."\n";
				echo '<option value="">&mdash; Select &mdash;</option>'."\n";
				foreach(($ws_plugin__s2member_temp_a = array_merge((array)get_pages())) as $ws_plugin__s2member_temp_o)
					echo '<option value="'.esc_attr($ws_plugin__s2member_temp_o->ID).'"'.(($ws_plugin__s2member_temp_o->ID == $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page"]) ? ' selected="selected"' : '').'>'.esc_html($ws_plugin__s2member_temp_o->post_title).'</option>'."\n";
				echo '</select><br />'."\n";
				echo 'Please choose a Page that provides Users with a way to sign up. We recommend the following title: <code>Membership Signup</code>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-membership-options-page-vars-enable">'."\n";
				echo 'Enable MOP Vars (i.e., Membership Options Page Variables)?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_membership_options_page_vars_enable" id="ws-plugin--s2member-membership-options-page-vars-enable">'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page_vars_enable"]) ? ' selected="selected"' : '').'>Yes (enable MOP Vars in all redirections; recommended behavior)</option>'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["membership_options_page_vars_enable"]) ? ' selected="selected"' : '').'>No (don\'t include the additional details provided by MOP Vars)</option>'."\n";
				echo '</select><br />'."\n";
				echo 'See also: <strong>Dashboard → s2Member → API / Scripting → Membership Options Page / Variables</strong><br />'."\n";
				echo 'Recommended setting: (<code>Yes, enable MOP Vars</code>)'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_membership_options_page", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_profile_modifications", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_profile_modifications", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="Member Profile Modifications">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-profile-modifications-section">'."\n";
				echo '<h3>Giving Members The Ability To Modify Their Profile</h3>'."\n";
				echo '<p>s2Member can be configured to redirect Members away from the <a href="'.esc_attr(admin_url("/profile.php")).'" target="_blank" rel="external">default Profile Editing Panel</a> that is built into WordPress. When/if a Member attempts to access the default Profile Editing Panel, they\'ll instead be redirected to the Login Welcome Page that you\'ve configured with s2Member. <strong>Why would I redirect away from the default Profile Editing Panel?</strong> Unless you\'ve made some drastic modifications to your WordPress installation, the default Profile Editing Panel that comes with WordPress is <em>not</em> suited for any sort of public access.</p>'."\n";
				echo '<p>So instead of using this default Profile Editing Panel, s2Member provides you <em>(the site owner)</em> with a special Shortcode: <code>[s2Member-Profile /]</code>. You can insert this into your Login Welcome Page, or any Post/Page for that matter <em>(even into a Text Widget)</em>. This Shortcode produces an Inline Profile Editing Form that supports all aspects of s2Member, including Password changes; and any Custom Registration/Profile Fields that you\'ve configured with s2Member. Alternatively, you can send your Members to a <a href="'.esc_attr(home_url("/?s2member_profile=1")).'" target="_blank" rel="external">special Stand-Alone version</a>. The stand-alone version makes it possible for you to <a href="#" onclick="if(!window.open(\''.home_url("/?s2member_profile=1").'\', \'_popup\', \'width=600,height=400,left=100,screenX=100,top=100,screenY=100,location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1\')) alert(\'Please disable popup blockers and try again!\'); return false;" rel="external">open it up in a popup window</a>, or embed it into your Login Welcome Page using an IFRAME. Code samples below.</p>'."\n";
				echo (c_ws_plugin__s2member_utils_conds::bp_is_installed()) ? '<p><em><strong>BuddyPress:</strong> BuddyPress already provides Users/Members with a Profile Editing Panel, powered by your theme. If you\'ve configured Custom Registration/Profile Fields with s2Member, you can also enable s2Member\'s Profile Field integration with BuddyPress (recommended). For further details, see: <strong>s2Member → General Options → Registration/Profile Fields</strong>.</em></p>'."\n" : '';
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_profile_modifications", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-force-admin-lockouts">'."\n";
				echo 'Redirect Members away from the Default Profile Panel?'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_force_admin_lockouts" id="ws-plugin--s2member-force-admin-lockouts">'."\n";
				echo '<option value="0"'.((!$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["force_admin_lockouts"]) ? ' selected="selected"' : '').'>No (I want to use the WordPress default methodologies)</option>'."\n";
				echo '<option value="1"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["force_admin_lockouts"]) ? ' selected="selected"' : '').'>Yes (redirect to Login Welcome Page; locking all /wp-admin/ areas)</option>'."\n";
				echo '</select><br />'."\n";
				echo 'Recommended setting (<code>Yes</code>). <em><strong>Note:</strong> When this is set to (<code>Yes</code>), s2Member will take an initiative to further safeguard ALL <code>/wp-admin/</code> areas of your installation; not just the Default Profile Panel.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";

				echo '<div class="ws-menu-page-hr"></div>'."\n";

				echo '<p style="margin-bottom:0;"><strong>Shortcode (copy/paste)</strong>, for an Inline Profile Modification Form:<br />'."\n";
				echo '<p style="margin-top:0;"><input type="text" autocomplete="off" value="'.format_to_edit('[s2Member-Profile /]').'" onclick="this.select ();" /></p>'."\n";

				echo '<p style="margin-top:25px; margin-bottom:0;"><strong>Stand-Alone (copy/paste)</strong>, for popup window:</p>'."\n";
				echo '<p style="margin-top:0;"><input type="text" autocomplete="off" value="'.format_to_edit(preg_replace("/\<\?php echo S2MEMBER_CURRENT_USER_PROFILE_MODIFICATION_PAGE_URL; \?\>/", c_ws_plugin__s2member_utils_strings::esc_refs(home_url("/?s2member_profile=1")), file_get_contents(dirname(__FILE__)."/code-samples/current-user-profile-modification-page-url-2-ops.x-php"))).'" onclick="this.select ();" /></p>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_profile_modifications", get_defined_vars());
			}
			if(apply_filters("ws_plugin__s2member_during_gen_ops_page_during_left_sections_display_url_shortening", TRUE, get_defined_vars()))
			{
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_before_url_shortening", get_defined_vars());

				echo '<div class="ws-menu-page-group" title="URL Shortening Service Preference">'."\n";

				echo '<div class="ws-menu-page-section ws-plugin--s2member-url-shortening-section">'."\n";
				echo '<h3>URL Shortening Service API (Preference)</h3>'."\n";
				echo '<p>In a few special cases, long URLs generated by s2Member (i.e., those containing encrypted authentication details) will be shortened. URLs are shortened automatically, using one of the URL Shortening APIs <em>(listed below)</em>. A shortened URL prevents issues with very long links becoming corrupted by a Customer\'s email application. For instance, the Signup Confirmation Email that s2Member sends out to a new paying Customer may contain a link which is shortened to prevent corruption by email applications. By default, s2Member uses the tinyURL API. tinyURL has proven itself to be the most reliable. However, in cases where an API service call fails, s2Member will automatically use one or more of its other APIs as a backup. The option below allows you to configure which URL Shortening API s2Member should try first <em>(i.e., the one you prefer)</em>.</p>'."\n";
				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_during_url_shortening", get_defined_vars());

				echo '<table class="form-table">'."\n";
				echo '<tbody>'."\n";
				echo '<tr>'."\n";

				echo '<th>'."\n";
				echo '<label for="ws-plugin--s2member-default-url-shortener">'."\n";
				echo 'URL Shortening Service API (Preference):'."\n";
				echo '</label>'."\n";
				echo '</th>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<select name="ws_plugin__s2member_default_url_shortener" id="ws-plugin--s2member-default-url-shortener">'."\n";
				echo '<option value="tiny_url"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["default_url_shortener"] === "tiny_url") ? ' selected="selected"' : '').'>tinyurl.com (free; no API key)</option>'."\n";
				echo '<option value="bitly"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["default_url_shortener"] === "bitly") ? ' selected="selected"' : '').'>Bitly (free; API key required)</option>'."\n";
				echo '<option value="goo_gl"'.(($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["default_url_shortener"] === "goo_gl") ? ' selected="selected"' : '').'>goo.gl (almost-free; API key required)</option>'."\n";
				echo '</select>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '<tr>'."\n";

				echo '<td>'."\n";
				echo '<input type="password" autocomplete="off" name="ws_plugin__s2member_default_url_shortener_key" id="ws-plugin--s2member-default-url-shortener-key" value="'.format_to_edit($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["default_url_shortener_key"]).'" /><br />'."\n";
				echo '<em>If you choose Bitly, enter your <a href="http://s2member.com/r/bitly-oauth-access-token/" target="_blank" rel="external">Bitly oAuth Access Token</a>. If you choose Google, <a href="http://s2member.com/r/google-console-create-project/" target="_blank" rel="external">create a new Google Project</a>, enable the URL Shortening API in your Project, generate a Public API Key and enter it here.</em>'."\n";
				echo '</td>'."\n";

				echo '</tr>'."\n";
				echo '</tbody>'."\n";
				echo '</table>'."\n";
				echo '</div>'."\n";

				echo '</div>'."\n";

				do_action("ws_plugin__s2member_during_gen_ops_page_during_left_sections_after_url_shortening", get_defined_vars());
			}
			do_action("ws_plugin__s2member_during_gen_ops_page_after_left_sections", get_defined_vars());

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

new c_ws_plugin__s2member_menu_page_gen_ops ();
