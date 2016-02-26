<?php
/**
 * Login customizations.
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
 * @package s2Member\Login_Customizations
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_login_customizations'))
{
	/**
	 * Login customizations.
	 *
	 * @package s2Member\Login_Customizations
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_login_customizations
	{
		/**
		 * Filters the login/registration logo URL.
		 *
		 * @package s2Member\Login_Customizations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('login_headerurl');``
		 *
		 * @param string $url Expects a login header URL passed in by the Filter.
		 *
		 * @return string A URL based on s2Member's UI configuration.
		 */
		public static function login_header_url($url = '')
		{
			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_design_enabled'])
				return $url; // Login/registration design disabled in this case.

			do_action('ws_plugin__s2member_before_login_header_url', get_defined_vars());

			$url = $GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_logo_url'];

			return apply_filters('ws_plugin__s2member_login_header_url', $url, get_defined_vars());
		}

		/**
		 * Filters the login/registration logo title.
		 *
		 * @package s2Member\Login_Customizations
		 * @since 3.5
		 *
		 * @attaches-to ``add_filter('login_headertitle');``
		 *
		 * @param string $title Expects a title passed in by the Filter.
		 *
		 * @return string A title based on s2Member's UI configuration.
		 */
		public static function login_header_title($title = '')
		{
			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_design_enabled'])
				return $title; // Login/registration design disabled in this case.

			do_action('ws_plugin__s2member_before_login_header_title', get_defined_vars());

			$title = $GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_logo_title'];

			return apply_filters('ws_plugin__s2member_login_header_title', $title, get_defined_vars());
		}

		/**
		 * Styles login/registration *( i.e., `/wp-login.php` )*.
		 *
		 * @package s2Member\Login_Customizations
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('login_head');``
		 */
		public static function login_header_styles()
		{
			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_design_enabled'])
				return; // Login/registration design disabled in this case.

			$s = ''; // Initialize styles string here to give hooks a chance.
			$a = array(); // Initialize here to give filters a chance.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_login_header_styles', get_defined_vars());
			unset($__refs, $__v);

			$a[] = '<style type="text/css">'; // Open style tag, then give filters a chance below.
			$i   = apply_filters('ws_plugin__s2member_login_header_styles_important', ' !important', get_defined_vars());
			$a   = apply_filters('ws_plugin__s2member_login_header_styles_array_after_open', $a, get_defined_vars());

			$a[] = 'html, body { border:0'.$i.'; background:none'.$i.'; }';
			$a[] = 'html { background-color:#'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_color'].$i.'; }';
			$a[] = 'html { background-image:url('.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_image'].')'.$i.'; }';
			$a[] = 'html { background-repeat:'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_image_repeat'].$i.'; }';
			$a[] = '@media (max-width: 767px) { html, body { background-size: contain '.$i.'; } }';

			$a[] = 'body, body * { font-size:'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_font_size'].$i.'; }';
			$a[] = 'body, body * { font-family:'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_font_family'].$i.'; }';

			$a[] = 'div#login { width: 100% '.$i.'; max-width:'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_logo_src_width'].'px'.$i.'; }';
			$a[] = 'div#login h1 a { background:url('.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_logo_src'].') no-repeat top center'.$i.'; background-size:contain'.$i.'; }';
			$a[] = 'div#login h1 a { display:block'.$i.'; width:100%'.$i.'; height:'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_logo_src_height'].'px'.$i.'; }';

			$a[] = 'div#login form { box-shadow:1px 1px 2px #'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_box_shadow_color'].', -1px -1px 2px #'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_box_shadow_color'].$i.'; border-radius:5px'.$i.'; padding-bottom:16px'.$i.'; }';

			$a[] = 'div#login p#nav, div#login p#nav a, div#login p#nav a:hover, div#login p#nav a:active, div#login p#nav a:focus { color:#'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_text_color'].$i.'; text-shadow:1px 1px 3px #'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_text_shadow_color'].$i.'; }';
			$a[] = 'div#login p#backtoblog, div#login p#backtoblog a, div#login p#backtoblog a:hover, div#login p#backtoblog a:active, div#login p#backtoblog a:focus { color:#'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_text_color'].$i.'; text-shadow:1px 1px 3px #'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_background_text_shadow_color'].$i.'; }';

			$a[] = 'div#login form p { margin:2px 0 16px 0'.$i.'; }'; // Handles paragraph margins inside the form.
			$a[] = 'div#login form input[type="text"], div#login form input[type="email"], div#login form input[type="password"], div#login form textarea, div#login form select { margin:0'.$i.'; padding:3px'.$i.'; border-radius:3px'.$i.'; box-sizing:border-box'.$i.'; width:100%'.$i.'; background:#FBFBFB repeat scroll 0 0'.$i.'; border:1px solid #E5E5E5'.$i.'; font-size:'.$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_font_field_size'].$i.'; font-weight:normal'.$i.'; color:#333333'.$i.'; }';
			$a[] = '@supports (-moz-appearance: none){ div#login form select { font-size:'.min((integer)$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_font_field_size'], 15).'px'.$i.'; } }'; // Mozilla doesn't like the larger font size. This corrects that issue in Firefox.
			$a[] = 'div#login form label { cursor:pointer'.$i.'; } div#login form label.ws-plugin--s2member-custom-reg-field-op-l { opacity:0.7'.$i.'; font-size:90%'.$i.'; vertical-align:middle'.$i.'; }';
			$a[] = 'div#login form input[type="checkbox"], div#login form input[type="radio"] { margin:0 3px 0 0'.$i.'; vertical-align:middle'.$i.'; }';
			$a[] = 'div#login form input#ws-plugin--s2member-custom-reg-field-user-pass2[type="password"] { margin-top:5px'.$i.'; }';

			$a[] = 'div#login form div.ws-plugin--s2member-custom-reg-field-divider-section { margin:2px 0 16px 0'.$i.'; border:0'.$i.'; height:1px'.$i.'; line-height:1px'.$i.'; background:#CCCCCC'.$i.'; }';
			$a[] = 'div#login form div.ws-plugin--s2member-custom-reg-field-divider-section-title { margin:2px 0 16px 0'.$i.'; border:0 solid #CCCCCC'.$i.'; border-width:0 0 1px 0'.$i.'; padding:0 0 10px 0'.$i.'; font-size:110%'.$i.'; }';

			$a[] = 'div#login form input[type="submit"], div#login form input[type="submit"]:hover, div#login form input[type="submit"]:active, div#login form input[type="submit"]:focus { color:#666666'.$i.'; text-shadow:2px 2px 5px #EEEEEE'.$i.'; border:1px solid #999999'.$i.'; border-radius:3px'.$i.'; background:#FBFBFB'.$i.'; box-shadow:0 -1px 2px 0 rgba(0,0,0,0.2) inset'.$i.'; }';
			$a[] = 'div#login form input[type="submit"]:hover, div#login form input[type="submit"]:active, div#login form input[type="submit"]:focus { color:#000000'.$i.'; text-shadow:2px 2px 5px #CCCCCC'.$i.'; border-color:#000000'.$i.'; }';
			$a[] = 'div#login form#registerform p.submit { float:none'.$i.'; margin-top:-10px'.$i.'; } div#login form#registerform input[type="submit"] { float:none'.$i.'; width:100%'.$i.'; box-sizing:border-box'.$i.'; }';
			$a[] = 'div#login form#lostpasswordform p.submit { float:none'.$i.'; } div#login form#lostpasswordform input[type="submit"] { float:none'.$i.'; width:100%'.$i.'; box-sizing:border-box'.$i.'; }';
			$a[] = 'div#login form#resetpassform #pass-strength-result { float:none'.$i.'; width:100%'.$i.'; box-sizing:border-box'.$i.'; } div#login form#resetpassform p.submit { float:none'.$i.'; } div#login form#resetpassform input[type="submit"] { float:none'.$i.'; width:100%'.$i.'; box-sizing:border-box'.$i.'; }';

			$a[] = 'div.ws-plugin--s2member-password-strength { margin-top:3px'.$i.'; font-color:#000000'.$i.'; background-color:#EEEEEE'.$i.'; padding:3px'.$i.'; border-radius:3px'.$i.'; } div.ws-plugin--s2member-password-strength-short { background-color:#FFA0A0'.$i.'; } div.ws-plugin--s2member-password-strength-weak { background-color:#FFB78C'.$i.'; } div.ws-plugin--s2member-password-strength-good { background-color:#FFEC8B'.$i.'; } div.ws-plugin--s2member-password-strength-strong { background-color:#C3FF88'.$i.'; } div.ws-plugin--s2member-password-strength-mismatch { background-color:#D6C1AB'.$i.'; }';

			$a[] = 'div#login form#registerform p#reg_passmail { font-style:italic'.$i.'; }';

			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['custom_reg_password'])
				$a[] = 'div#login form#registerform p#reg_passmail { display:none'.$i.'; }';

			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_footer_backtoblog'])
				$a[] = 'div#login p#backtoblog { display:none'.$i.'; }';

			$a   = apply_filters('ws_plugin__s2member_login_header_styles_array_before_close', $a, get_defined_vars());
			$a[] = '</style>'; // Now close style tag. There are other filters below.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_during_login_header_styles', get_defined_vars());
			unset($__refs, $__v);

			$a = apply_filters('ws_plugin__s2member_login_header_styles_array', $a, get_defined_vars());
			$s .= "\n".implode("\n", $a)."\n\n"; // Now put all array elements together.

			echo apply_filters('ws_plugin__s2member_login_header_styles', $s, get_defined_vars());

			do_action('ws_plugin__s2member_after_login_header_styles', get_defined_vars());
		}

		/**
		 * Displays login footer design.
		 *
		 * @package s2Member\Login_Customizations
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('login_footer');``
		 *
		 * @return void
		 */
		public static function login_footer_design()
		{
			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_design_enabled'])
				return; // Login/registration design disabled in this case.

			do_action('ws_plugin__s2member_before_login_footer_design', get_defined_vars());

			if(($code = $GLOBALS['WS_PLUGIN__']['s2member']['o']['login_reg_footer_design']))
				if(is_multisite() && c_ws_plugin__s2member_utils_conds::is_multisite_farm() && !is_main_site())
				{
					echo $code."\n"; // No PHP here.
				}
				else // Otherwise, safe to allow PHP code.
				{
					eval('?>'.$code);
				}
			do_action('ws_plugin__s2member_after_login_footer_design', get_defined_vars());
		}

		/**
		 * Filters the Lost Password URL and changes the default behavior of wp_lostpassword_url()
		 * so that it uses site_url() instead of network_site_url(), but only if is_multisite()
		 * In a non-multisite environment, the default WordPress behavior (as of v4.3.1) is used.
		 *
		 * @package s2Member\Login_Customizations
		 * @since 140603
		 *
		 * @attaches-to ``add_filter('lostpassword_url');``
		 *
		 * @param string $lostpassword_url The lost password page URL.
		 * @param string $redirect The path to redirect to on login.
		 *
		 * @return string Lost password URL.
		 */
		public static function lost_password_url($lostpassword_url, $redirect)
		{
			if(apply_filters('ws_plugin__s2member_tweak_lost_password_url', is_multisite(), get_defined_vars()))
			{
				$args = array('action' => 'lostpassword');
				if(!empty($redirect)) $args['redirect_to'] = $redirect;

				$lostpassword_url = add_query_arg(urlencode_deep($args), site_url('wp-login.php', 'login'));
			}
			return apply_filters('ws_plugin__s2member_lost_password_url', $lostpassword_url, $redirect, get_defined_vars());
		}
	}
}
