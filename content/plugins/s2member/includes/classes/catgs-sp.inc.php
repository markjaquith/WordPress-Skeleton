<?php
/**
 * s2Member's Category protection routines *(for specific Categories)*.
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
 * @package s2Member\Categories
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_catgs_sp'))
{
	/**
	 * s2Member's Category protection routines *(for specific Categories)*.
	 *
	 * @package s2Member\Categories
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_catgs_sp
	{
		/**
		 * Handles Category Level Access *(for specific Categories)*.
		 *
		 * @package s2Member\Categories
		 * @since 3.5
		 *
		 * @param int|string $cat_id Numeric Category ID.
		 * @param bool       $check_user Test permissions against the current User? Defaults to true.
		 *
		 * @return null|array Non-empty array(with details) if access is denied, else null if access is allowed.
		 */
		public static function check_specific_catg_level_access($cat_id = '', $check_user = TRUE)
		{
			do_action('ws_plugin__s2member_before_check_specific_catg_level_access', get_defined_vars());

			$ci       = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';
			$excluded = apply_filters('ws_plugin__s2member_check_specific_catg_level_access_excluded', FALSE, get_defined_vars());

			if(!$excluded && is_numeric($cat_id) && ($cat_id = (int)$cat_id) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
			{
				$cat_uri = c_ws_plugin__s2member_utils_urls::parse_uri(get_category_link($cat_id));

				if(!c_ws_plugin__s2member_systematics_sp::is_wp_systematic_use_specific_page(NULL, $cat_uri))
				{
					$user = (is_user_logged_in() && is_object($user = wp_get_current_user()) && !empty($user->ID)) ? $user : FALSE;

					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_override'] && ($login_redirection_uri = c_ws_plugin__s2member_login_redirects::login_redirection_uri($user, 'root-returns-false')) && preg_match('/^'.preg_quote($login_redirection_uri, '/').'$/'.$ci, $cat_uri) && (!$check_user || !$user || !$user->has_cap('access_s2member_level0')))
						return apply_filters('ws_plugin__s2member_check_specific_catg_level_access', array('s2member_level_req' => 0), get_defined_vars());

					else if(!c_ws_plugin__s2member_systematics_sp::is_systematic_use_specific_page(NULL, $cat_uri))
					{
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--)
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] === 'all' && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_catg_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] && in_array($cat_id, ($catgs = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs']))) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_catg_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'])
								foreach(preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs']) as $catg)
									if($catg && cat_is_ancestor_of($catg, $cat_id) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
										return apply_filters('ws_plugin__s2member_check_specific_catg_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}

						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--)
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris'])
								foreach(preg_split('/['."\r\n\t".']+/', c_ws_plugin__s2member_ruris::fill_ruri_level_access_rc_vars($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris'], $user)) as $str)
									if($str && preg_match('/'.preg_quote($str, '/').'/'.$ci, $cat_uri) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
										return apply_filters('ws_plugin__s2member_check_specific_catg_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}
					}
					do_action('ws_plugin__s2member_during_check_specific_catg_level_access', get_defined_vars());
				}
			}
			return apply_filters('ws_plugin__s2member_check_specific_catg_level_access', NULL, get_defined_vars());
		}
	}
}