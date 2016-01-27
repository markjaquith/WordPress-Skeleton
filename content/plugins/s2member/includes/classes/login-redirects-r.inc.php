<?php
/**
* Login redirect removals.
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
* @package s2Member\Login_Redirects
* @since 3.5
*/
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if (!class_exists ('c_ws_plugin__s2member_login_redirects_r'))
	{
		/**
		* Login redirect removals.
		*
		* @package s2Member\Login_Redirects
		* @since 3.5
		*/
		class c_ws_plugin__s2member_login_redirects_r
			{
				/**
				* Handles completely empty ``login_redirect`` values.
				*
				* @attaches-to ``add_filter('login_redirect');``
				*
				* @package s2Member\Login_Redirects
				* @since 110926
				*
				* @param string $redirect_to Expects the current ``$redirect_to`` passed in by the Filter.
				* @return string A non-empty string value. s2Member will NEVER allow this to be completely empty.
				*/
				public static function _empty_login_redirect_filter($redirect_to)
					{
						return $redirect_to ? $redirect_to : admin_url ();
					}

				/**
				* Handles HTTP/HTTPS ``login_redirect`` values.
				*
				* @attaches-to ``add_filter('login_redirect');``
				*
				* @package s2Member\Login_Redirects
				* @since 130819
				*
				* @param string $redirect_to Expects the current ``$redirect_to`` passed in by the Filter.
				* @return string Updated `redirect_to` value.
				*/
				public static function _http_login_redirect_filter($redirect_to)
					{
						$ci = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';

						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_always_http'])
							if($redirect_to && is_string($redirect_to) && strpos($redirect_to, 'wp-admin') === FALSE)
								{
									$redirect_to = preg_replace('/^https\:\/\//i', 'http://', $redirect_to);
									if(stripos($redirect_to, 'http://') !== 0) // Force full URL.
										{
											$redirect_uri    = $redirect_to; // e.g., `/path/with/?query=args`
											$home_path       = trim((string)@parse_url(home_url('/'), PHP_URL_PATH), '/');
											$http_home_base  = trim(preg_replace('/\/'.preg_quote($home_path, '/').'\/$/'.$ci, '', home_url('/', 'http')), '/');
											$redirect_to     = $http_home_base.'/'.ltrim($redirect_uri, '/');
										}
								}
						return $redirect_to;
					}

				/**
				* Removes all other ``login_redirect`` Filters to prevent conflicts with s2Member.
				*
				* @attaches-to ``add_action('init');``
				*
				* @package s2Member\Login_Redirects
				* @since 3.5
				*/
				public static function remove_login_redirect_filters()
					{
						do_action('ws_plugin__s2member_before_remove_login_redirect_filters', get_defined_vars ());

						if (!apply_filters('ws_plugin__s2member_allow_other_login_redirect_filters', false, get_defined_vars ()))
							{
								remove_all_filters('login_redirect'); // Remove all others.
								do_action('ws_plugin__s2member_during_remove_login_redirect_filters', get_defined_vars ());
							}
						add_filter('login_redirect', 'c_ws_plugin__s2member_login_redirects_r::_empty_login_redirect_filter');
						add_filter('login_redirect', 'c_ws_plugin__s2member_login_redirects_r::_http_login_redirect_filter');

						do_action('ws_plugin__s2member_after_remove_login_redirect_filters', get_defined_vars ());
					}
			}
	}
