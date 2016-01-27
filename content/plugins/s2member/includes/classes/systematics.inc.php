<?php
/**
 * Systematics *(for current page)*.
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
 * @package s2Member\Systematics
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_systematics'))
{
	/**
	 * Systematics *(for current page)*.
	 *
	 * @package s2Member\Systematics
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_systematics
	{
		/**
		 * Determines if the current page is s2Member Systematic.
		 *
		 * @package s2Member\Systematics
		 * @since 111115
		 *
		 * @return bool True if s2Member Systematic, else false.
		 *
		 * @note The results of this function are cached staticially.
		 *   Do NOT call upon this until the `init` Hook is fired.
		 */
		public static function is_s2_systematic_use_page()
		{
			static $is_s2_systematic; // For optimization.

			if(isset($is_s2_systematic)) // Already cached statically? Saves time.
				return $is_s2_systematic; // Filters will have already been applied here.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(!empty($_SERVER['QUERY_STRING']) && preg_match('/[\?&]s2member/'.$ci, $_SERVER['QUERY_STRING']) && c_ws_plugin__s2member_utils_conds::is_site_root($_SERVER['REQUEST_URI']))
				return ($is_s2_systematic = apply_filters('ws_plugin__s2member_is_s2_systematic_use_page', TRUE, get_defined_vars()));

			return ($is_s2_systematic = apply_filters('ws_plugin__s2member_is_s2_systematic_use_page', FALSE, get_defined_vars()));
		}

		/**
		 * Determines if the current page is WordPress Systematic.
		 *
		 * @package s2Member\Systematics
		 * @since 111002
		 *
		 * @return bool True if WordPress Systematic, else false.
		 *
		 * @note The results of this function are cached staticially.
		 *   Do NOT call upon this until the `init` Hook is fired.
		 */
		public static function is_wp_systematic_use_page()
		{
			static $is_wp_systematic; // For optimization.

			if(isset($is_wp_systematic)) // Already cached statically? Saves time.
				return $is_wp_systematic; // Filters will have already been applied here.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(is_admin()) // In the admin area? All administrational pages are considered Systematic.
				return ($is_wp_systematic = apply_filters('ws_plugin__s2member_is_wp_systematic_use_page', TRUE, get_defined_vars()));

			if(defined('WP_INSTALLING') && WP_INSTALLING) // Installing? All WordPress installs are considered Systematic.
				return ($is_wp_systematic = apply_filters('ws_plugin__s2member_is_wp_systematic_use_page', TRUE, get_defined_vars()));

			if(defined('APP_REQUEST') && APP_REQUEST) // App request? All WordPress app requests are considered Systematic.
				return ($is_wp_systematic = apply_filters('ws_plugin__s2member_is_wp_systematic_use_page', TRUE, get_defined_vars()));

			if(defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) // An XML-RPC request? All of these are considered Systematic too.
				return ($is_wp_systematic = apply_filters('ws_plugin__s2member_is_wp_systematic_use_page', TRUE, get_defined_vars()));

			if((defined('DOING_CRON') && DOING_CRON) || strcasecmp(PHP_SAPI, 'CLI') === 0) // CLI or CRON job.
				return ($is_wp_systematic = apply_filters('ws_plugin__s2member_is_wp_systematic_use_page', TRUE, get_defined_vars()));

			if(preg_match('/^\/(?:wp-.+?|xmlrpc)\.php$/'.$ci, c_ws_plugin__s2member_utils_urls::parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)))
				return ($is_wp_systematic = apply_filters('ws_plugin__s2member_is_wp_systematic_use_page', TRUE, get_defined_vars()));

			return ($is_wp_systematic = apply_filters('ws_plugin__s2member_is_wp_systematic_use_page', FALSE, get_defined_vars()));
		}

		/**
		 * Determines if the current page is Systematic in any way.
		 *
		 * @package s2Member\Systematics
		 * @since 3.5
		 *
		 * @return bool True if Systematic, else false.
		 *
		 * @note The results of this function are cached staticially.
		 *   Do NOT call upon this until the `wp` Hook is fired.
		 */
		public static function is_systematic_use_page()
		{
			static $is_systematic; // For optimization.

			if(isset($is_systematic)) // Already cached statically? Saves time.
				return $is_systematic; // Filters will have already been applied here.

			$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

			if(c_ws_plugin__s2member_systematics::is_s2_systematic_use_page()) // An s2Member Systematic Use Page?
				return ($is_systematic = apply_filters('ws_plugin__s2member_is_systematic_use_page', TRUE, get_defined_vars()));

			if(c_ws_plugin__s2member_systematics::is_wp_systematic_use_page()) //* A WordPress Systematic Use Page?
				return ($is_systematic = apply_filters('ws_plugin__s2member_is_systematic_use_page', TRUE, get_defined_vars()));

			if(c_ws_plugin__s2member_utils_conds::bp_is_installed() && (bp_is_register_page() || bp_is_activation_page()))
				return ($is_systematic = apply_filters('ws_plugin__s2member_is_systematic_use_page', TRUE, get_defined_vars()));

			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page'] && is_page($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page']))
				return ($is_systematic = apply_filters('ws_plugin__s2member_is_systematic_use_page', TRUE, get_defined_vars()));

			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'] && is_page($GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page']))
				return ($is_systematic = apply_filters('ws_plugin__s2member_is_systematic_use_page', TRUE, get_defined_vars()));

			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page'] && is_page($GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page']))
				return ($is_systematic = apply_filters('ws_plugin__s2member_is_systematic_use_page', TRUE, get_defined_vars()));

			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_override'] && ($_lro = c_ws_plugin__s2member_login_redirects::login_redirection_uri(NULL, 'root-returns-false')) && preg_match('/^'.preg_quote($_lro, '/').'$/'.$ci, $_SERVER['REQUEST_URI']))
				return ($is_systematic = apply_filters('ws_plugin__s2member_is_systematic_use_page', TRUE, get_defined_vars()));

			return ($is_systematic = apply_filters('ws_plugin__s2member_is_systematic_use_page', FALSE, get_defined_vars()));
		}
	}
}