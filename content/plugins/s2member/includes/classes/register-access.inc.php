<?php
/**
 * Registration Access Links.
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
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_register_access'))
{
	/**
	 * Registration Access Links.
	 *
	 * @package s2Member\Registrations
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_register_access
	{
		/**
		 * Generates Registration Access Links.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @param string     $subscr_gateway Payment Gateway associated with a Customer.
		 * @param string     $subscr_id Unique Subscr. ID associated with Payment Gateway; associated with a Customer.
		 * @param string     $custom Custom String value *(as supplied in Shortcode)*; must start with installation domain name.
		 * @param int|string $item_number An s2Member-generated `item_number` *( i.e., `1` for Level 1, or `level|ccaps|fixed-term`, or `sp|ids|expiration` )*.
		 * @param bool       $shrink Optional. Defaults to true. If false, the raw registration link will NOT be reduced in size through the tinyURL API.
		 *
		 * @return string|bool A Registration Access Link on success, else false on failure.
		 */
		public static function register_link_gen($subscr_gateway = '', $subscr_id = '', $custom = '', $item_number = '', $shrink = TRUE)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_register_link_gen', get_defined_vars());
			unset($__refs, $__v);

			if($subscr_gateway && is_string($subscr_gateway) && $subscr_id && is_string($subscr_id) && $custom && is_string($custom) && $item_number && (is_string($item_number) || is_numeric($item_number)))
			{
				$register = c_ws_plugin__s2member_utils_encryption::encrypt('subscr_gateway_subscr_id_custom_item_number_time:.:|:.:'.$subscr_gateway.':.:|:.:'.$subscr_id.':.:|:.:'.$custom.':.:|:.:'.$item_number.':.:|:.:'.strtotime('now'));

				$register_link = home_url('/?s2member_register='.urlencode($register)); // Generate long URL/link.

				if($shrink && ($shorter_url = c_ws_plugin__s2member_utils_urls::shorten($register_link)))
					$register_link = $shorter_url.'#'.$_SERVER['HTTP_HOST'];
			}
			return apply_filters('ws_plugin__s2member_register_link_gen', ((!empty($register_link)) ? $register_link : FALSE), get_defined_vars());
		}

		/**
		 * Generates Registration Access Links via AJAX.
		 *
		 * @package s2Member\Registrations
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('wp_ajax_ws_plugin__s2member_reg_access_link_via_ajax');``
		 *
		 * @return null Exits script execution after output is generated for AJAX caller.
		 */
		public static function reg_access_link_via_ajax()
		{
			do_action('ws_plugin__s2member_before_reg_access_link_via_ajax', get_defined_vars());

			status_header(200); // Send a 200 OK status header.
			header('Content-Type: text/plain; charset=UTF-8'); // Content-Type with UTF-8.
			while(@ob_end_clean()) ; // Clean any existing output buffers.

			if(current_user_can('create_users')) // Check privileges as well. Ability to create Users?

				if(!empty($_POST['ws_plugin__s2member_reg_access_link_via_ajax']) && is_string($nonce = $_POST['ws_plugin__s2member_reg_access_link_via_ajax']) && wp_verify_nonce($nonce, 'ws-plugin--s2member-reg-access-link-via-ajax'))

					if(($_p = c_ws_plugin__s2member_utils_strings::trim_deep(stripslashes_deep($_POST))) && isset ($_p['s2member_reg_access_link_subscr_gateway'], $_p['s2member_reg_access_link_subscr_id'], $_p['s2member_reg_access_link_custom'], $_p['s2member_reg_access_link_item_number']))
						$register_link = c_ws_plugin__s2member_register_access::register_link_gen((string)$_p['s2member_reg_access_link_subscr_gateway'], (string)$_p['s2member_reg_access_link_subscr_id'], (string)$_p['s2member_reg_access_link_custom'], (string)$_p['s2member_reg_access_link_item_number']);

			exit(apply_filters('ws_plugin__s2member_reg_access_link_via_ajax', ((!empty($register_link)) ? $register_link : ''), get_defined_vars()));
		}

		/**
		 * Checks registration cookies.
		 *
		 * @package s2Member\Registrations
		 * @since 110707
		 *
		 * @return array|bool An array of cookies if they're OK, else false.
		 */
		public static function reg_cookies_ok()
		{
			global $wpdb;
			/** @var $wpdb \wpdb */

			do_action('ws_plugin__s2member_before_reg_cookies_ok', get_defined_vars());

			if(isset ($_COOKIE['s2member_subscr_gateway'], $_COOKIE['s2member_subscr_id'], $_COOKIE['s2member_custom'], $_COOKIE['s2member_item_number']))
				if(($subscr_gateway = c_ws_plugin__s2member_utils_encryption::decrypt((string)$_COOKIE['s2member_subscr_gateway'])) && ($subscr_id = c_ws_plugin__s2member_utils_encryption::decrypt((string)$_COOKIE['s2member_subscr_id'])) && preg_match('/^'.preg_quote(preg_replace('/\:([0-9]+)$/', '', $_SERVER['HTTP_HOST']), '/').'/i', ($custom = c_ws_plugin__s2member_utils_encryption::decrypt((string)$_COOKIE['s2member_custom']))) && preg_match($GLOBALS['WS_PLUGIN__']['s2member']['c']['membership_item_number_w_level_regex'], ($item_number = c_ws_plugin__s2member_utils_encryption::decrypt((string)$_COOKIE['s2member_item_number']))) && !$wpdb->get_var("SELECT `user_id` FROM `".$wpdb->usermeta."` WHERE `meta_key` = '".$wpdb->prefix."s2member_subscr_id' AND `meta_value` = '".esc_sql($subscr_id)."' LIMIT 1"))
					$reg_cookies_ok = $reg_cookies = array('subscr_gateway' => $subscr_gateway, 'subscr_id' => $subscr_id, 'custom' => $custom, 'item_number' => $item_number);

			return apply_filters('ws_plugin__s2member_reg_cookies_ok', !empty($reg_cookies_ok) && !empty($reg_cookies) ? $reg_cookies : FALSE, get_defined_vars());
		}
	}
}