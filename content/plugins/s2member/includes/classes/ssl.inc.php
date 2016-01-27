<?php
/**
 * SSL routines.
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
 * @package s2Member\SSL
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_ssl'))
{
	/**
	 * SSL routines.
	 *
	 * @package s2Member\SSL
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_ssl
	{
		/**
		 * Forces SSL on specific Posts/Pages, or any page for that matter.
		 *
		 * Triggered by Custom Field: `s2member_force_ssl = yes|port#`
		 *
		 * Triggered by: `?s2-ssl` or `?s2-ssl=yes|port#`.
		 *
		 * @package s2Member\SSL
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('init');``
		 * @also-attaches-to ``add_action('wp');``
		 *
		 * @return null Possibly exiting script execution after redirection to SSL variation.
		 */
		public static function check_force_ssl()
		{
			static $forced = FALSE; // Only force SSL once.
			global $post; // We need this global reference to ``$post``.

			do_action('ws_plugin__s2member_before_check_force_ssl', get_defined_vars());

			if(!$forced && !c_ws_plugin__s2member_systematics::is_wp_systematic_use_page())
			{
				$s2_ssl_gv = apply_filters('ws_plugin__s2member_check_force_ssl_get_var_name', 's2-ssl', get_defined_vars());
				$_g_s2_ssl = isset($_GET[$s2_ssl_gv]) && (!strlen($_GET[$s2_ssl_gv]) || !preg_match('/^(0|no|off|false)$/i', $_GET[$s2_ssl_gv]))
					? (!strlen($_GET[$s2_ssl_gv]) ? TRUE : $_GET[$s2_ssl_gv]) : FALSE;
				$force_ssl = apply_filters('ws_plugin__s2member_check_force_ssl', $_g_s2_ssl, get_defined_vars());

				if($force_ssl || (did_action('wp') && is_singular() && is_object($post) && ($force_ssl = get_post_meta($post->ID, 's2member_force_ssl', TRUE))))
					if(!preg_match('/^(0|no|off|false)$/i', (string)$force_ssl) && ($forced = TRUE)) // Make sure it's NOT a negative variation.
						c_ws_plugin__s2member_ssl_in::force_ssl(get_defined_vars()); // Call inner routine now.
			}
			do_action('ws_plugin__s2member_after_check_force_ssl', get_defined_vars());
		}
	}
}