<?php
/**
 * s2Member's URI protection routines *(for current URI)*.
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
 * @package s2Member\URIs
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_ruris'))
{
	/**
	 * s2Member's URI protection routines *(for current URI)*.
	 *
	 * @package s2Member\URIs
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_ruris
	{
		/**
		 * Handles URI Level Access permissions *(for current URI)*.
		 *
		 * @package s2Member\URIs
		 * @since 3.5
		 *
		 * @return null Or exits script execution after redirection.
		 */
		public static function check_ruri_level_access()
		{
			do_action('ws_plugin__s2member_before_check_ruri_level_access', get_defined_vars());

			$ci       = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';
			$excluded = apply_filters('ws_plugin__s2member_check_ruri_level_access_excluded', FALSE, get_defined_vars());

			if(!$excluded && $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page']) // Has it been excluded?
			{
				if(!c_ws_plugin__s2member_systematics::is_wp_systematic_use_page()) // Do NOT touch WordPress Systematics. This excludes all WordPress Systematics.
				{
					$user = (is_user_logged_in() && is_object($user = wp_get_current_user()) && !empty($user->ID)) ? $user : FALSE; // Current User's object.

					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_override'] && ($login_redirection_uri = c_ws_plugin__s2member_login_redirects::login_redirection_uri($user, 'root-returns-false')) && preg_match('/^'.preg_quote($login_redirection_uri, '/').'$/'.$ci, $_SERVER['REQUEST_URI']) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level0')))
						c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('ruri', $_SERVER['REQUEST_URI'], 'level', 0, $_SERVER['REQUEST_URI'], 'sys').exit ();

					else if(!c_ws_plugin__s2member_systematics::is_systematic_use_page()) // Do NOT protect Systematics. However, there is 1 exception above ^.
					{
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // URIs. Go through each Level.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris']) // URIs configured at this Level?
								foreach(preg_split('/['."\r\n\t".']+/', c_ws_plugin__s2member_ruris::fill_ruri_level_access_rc_vars($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris'], $user)) as $str)
									if($str && preg_match('/'.preg_quote($str, '/').'/'.$ci, $_SERVER['REQUEST_URI']) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
										c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('ruri', $_SERVER['REQUEST_URI'], 'level', $n, $_SERVER['REQUEST_URI']).exit ();
						}
					}
					do_action('ws_plugin__s2member_during_check_ruri_level_access', get_defined_vars());
				}
			}
			do_action('ws_plugin__s2member_after_check_ruri_level_access', get_defined_vars());
		}

		/**
		 * Fills Replacement Code variables in URIs; collectively.
		 *
		 * @package s2Member\URIs
		 * @since 3.5
		 *
		 * @param string $uris A URI string, or a string of multiple URIs is also fine.
		 * @param object $user Optional. A `WP_User` object. Defaults to the current User, if logged-in.
		 *
		 * @return string Collective string of input URIs, with Replacement Codes having been filled.
		 */
		public static function fill_ruri_level_access_rc_vars($uris = '', $user = NULL)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_fill_ruri_level_access_rc_vars', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			$uris      = (string)$uris; // Force ``$uris`` to a string value.
			$orig_uris = $uris; // Record the original URIs that were passed in; collectively.

			$user = (is_object($user) || is_object($user = wp_get_current_user()))
			        && !empty($user->ID) ? $user : NULL;

			$user_id       = ($user) ? (string)$user->ID : '';
			$user_login    = ($user) ? (string)strtolower($user->user_login) : '';
			$user_nicename = ($user) ? (string)strtolower($user->user_nicename) : '';

			$user_level  = (string)c_ws_plugin__s2member_user_access::user_access_level($user);
			$user_role   = (string)c_ws_plugin__s2member_user_access::user_access_role($user);
			$user_ccaps  = (string)implode('-', c_ws_plugin__s2member_user_access::user_access_ccaps($user));
			$user_logins = ($user) ? (string)(int)get_user_option('s2member_login_counter', $user_id) : '-1';

			$uris = (strlen($user_login)) ? preg_replace('/%%current_user_login%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_login)), $uris) : $uris;
			$uris = (strlen($user_nicename)) ? preg_replace('/%%current_user_nicename%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_nicename)), $uris) : $uris;
			$uris = (strlen($user_id)) ? preg_replace('/%%current_user_id%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_id)), $uris) : $uris;
			$uris = (strlen($user_level)) ? preg_replace('/%%current_user_level%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_level)), $uris) : $uris;
			$uris = (strlen($user_role)) ? preg_replace('/%%current_user_role%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_role)), $uris) : $uris;
			$uris = (strlen($user_ccaps)) ? preg_replace('/%%current_user_ccaps%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_ccaps)), $uris) : $uris;
			$uris = (strlen($user_logins)) ? preg_replace('/%%current_user_logins%%/i', c_ws_plugin__s2member_utils_strings::esc_refs(urlencode($user_logins)), $uris) : $uris;

			return apply_filters('ws_plugin__s2member_fill_ruri_level_access_rc_vars', $uris, get_defined_vars());
		}
	}
}