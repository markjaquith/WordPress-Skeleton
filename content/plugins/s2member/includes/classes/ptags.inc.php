<?php
/**
 * s2Member's Tag protection routines *(for current page)*.
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
 * @package s2Member\Tags
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_ptags'))
{
	/**
	 * s2Member's Tag protection routines *(for current page)*.
	 *
	 * @package s2Member\Tags
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_ptags
	{
		/**
		 * Handles Tag Level Access permissions *(for current page)*.
		 *
		 * @package s2Member\Tags
		 * @since 3.5
		 *
		 * @return null Or exits script execution after redirection.
		 */
		public static function check_ptag_level_access()
		{
			/**
			 * @var $post WP_Post Reference for IDEs.
			 * @var $wp_query WP_Query Reference for IDEs.
			 */
			global $wp_query, $post; // ``get_the_ID()`` NOT available outside The Loop.

			do_action('ws_plugin__s2member_before_check_ptag_level_access', get_defined_vars());

			$ci       = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';
			$excluded = apply_filters('ws_plugin__s2member_check_ptag_level_access_excluded', FALSE, get_defined_vars());

			if(!$excluded && is_tag() && is_object($tag = $wp_query->get_queried_object()) && !empty($tag->term_id) && ($tag_id = (int)$tag->term_id) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
			{
				if(!c_ws_plugin__s2member_systematics::is_wp_systematic_use_page()) // Do NOT touch WordPress Systematics. This excludes all WordPress Systematics.
				{
					$user = (is_user_logged_in() && is_object($user = wp_get_current_user()) && !empty($user->ID)) ? $user : FALSE; // Current User's object.

					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_override'] && ($login_redirection_uri = c_ws_plugin__s2member_login_redirects::login_redirection_uri($user, 'root-returns-false')) && preg_match('/^'.preg_quote($login_redirection_uri, '/').'$/'.$ci, $_SERVER['REQUEST_URI']) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level0')))
						c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('ptag', $tag_id, 'level', 0, $_SERVER['REQUEST_URI'], 'sys').exit ();

					else if(!c_ws_plugin__s2member_systematics::is_systematic_use_page()) // Do NOT protect Systematics. However, there is 1 exception above.
					{
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Tag Level restrictions. Go through each Level.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] === 'all' && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
								c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('ptag', $tag_id, 'level', $n, $_SERVER['REQUEST_URI']).exit ();

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] && (is_tag($tags = preg_split('/['."\r\n\t".';,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'])) || in_array($tag_id, $tags)) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
								c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('ptag', $tag_id, 'level', $n, $_SERVER['REQUEST_URI']).exit ();
						}
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // URIs. Go through each Level.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris']) // URIs configured at this Level?

								foreach(preg_split('/['."\r\n\t".']+/', c_ws_plugin__s2member_ruris::fill_ruri_level_access_rc_vars($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris'], $user)) as $str)
									if($str && preg_match('/'.preg_quote($str, '/').'/'.$ci, $_SERVER['REQUEST_URI']) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
										c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('ptag', $tag_id, 'level', $n, $_SERVER['REQUEST_URI'], 'ruri').exit ();
						}
					}
					do_action('ws_plugin__s2member_during_check_ptag_level_access', get_defined_vars());
				}
			}
			do_action('ws_plugin__s2member_after_check_ptag_level_access', get_defined_vars());
		}
	}
}