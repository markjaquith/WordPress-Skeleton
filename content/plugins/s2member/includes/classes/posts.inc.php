<?php
/**
 * s2Member's Post protection routines *(for current Post)*.
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
 * @package s2Member\Posts
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_posts'))
{
	/**
	 * s2Member's Post protection routines *(for current Post)*.
	 *
	 * @package s2Member\Posts
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_posts
	{
		/**
		 * Handles Post Level Access permissions *(for current Post)*.
		 *
		 * @package s2Member\Posts
		 * @since 3.5
		 *
		 * @return null Or exits script execution after redirection.
		 */
		public static function check_post_level_access()
		{
			global $post; // ``get_the_ID()`` unavailable outside The Loop.

			do_action('ws_plugin__s2member_before_check_post_level_access', get_defined_vars());

			$ci       = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';
			$excluded = apply_filters('ws_plugin__s2member_check_post_level_access_excluded', FALSE, get_defined_vars());

			if(!$excluded && is_single() && is_object($post) && !empty($post->ID) && ($post_id = (int)$post->ID) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
			{
				if(!c_ws_plugin__s2member_systematics::is_wp_systematic_use_page()) // Do NOT touch WordPress Systematics. This excludes all WordPress Systematics.
				{
					$user = (is_user_logged_in() && is_object($user = wp_get_current_user()) && !empty($user->ID)) ? $user : FALSE; // Current User's object.

					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_override'] && ($login_redirection_uri = c_ws_plugin__s2member_login_redirects::login_redirection_uri($user, 'root-returns-false')) && preg_match('/^'.preg_quote($login_redirection_uri, '/').'$/'.$ci, $_SERVER['REQUEST_URI']) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level0')))
						c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', 0, $_SERVER['REQUEST_URI'], 'sys').exit ();

					else if(!c_ws_plugin__s2member_systematics::is_systematic_use_page()) // However, there is the one exception above.
					{
						$bbpress_restrictions_enable = apply_filters('ws_plugin__s2member_bbpress_restrictions_enable', TRUE);
						$bbpress_installed           = c_ws_plugin__s2member_utils_conds::bbp_is_installed(); // bbPress is installed?
						$bbpress_forum_post_type     = $bbpress_installed ? bbp_get_forum_post_type() : ''; // Acquire the current post type for forums.
						$bbpress_topic_post_type     = $bbpress_installed ? bbp_get_topic_post_type() : ''; // Acquire the current post type for topics.
						$bbpress_topic_forum_id      = $bbpress_installed && $post->post_type === $bbpress_topic_post_type ? bbp_get_topic_forum_id($post->ID) : 0;

						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Post Level restrictions.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] === 'all' && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
								c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI']).exit ();

							else if(strpos($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'], 'all-') !== FALSE && (in_array('all-'.$post->post_type, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) || in_array('all-'.$post->post_type.'s', preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts']))) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
								c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI']).exit ();

							else if($bbpress_restrictions_enable && $bbpress_installed && $post->post_type === $bbpress_topic_post_type && strpos($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'], 'all-') !== FALSE && (in_array('all-'.$bbpress_forum_post_type, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) || in_array('all-'.$bbpress_forum_post_type.'s', preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts']))) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
								c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI']).exit ();

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] && in_array($post_id, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
								c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI']).exit ();
						}
						if($bbpress_restrictions_enable && $bbpress_installed && $post->post_type === $bbpress_topic_post_type && $bbpress_topic_forum_id)
							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Forum restrictions.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] && in_array($bbpress_topic_forum_id, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
									c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $bbpress_topic_forum_id, 'level', $n, $_SERVER['REQUEST_URI']).exit ();
							}
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Category Level restrictions.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] === 'all' && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
								c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI'], 'catg').exit ();

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] && (in_category(($catgs = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'])), $post_id) || c_ws_plugin__s2member_utils_conds::in_descendant_category($catgs, $post_id)) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
								c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI'], 'catg').exit ();
						}
						if(has_tag()) // Here we take a look to see if this Post has any Tags. If so, we need to run the full set of routines against Tags also.
						{
							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Tag Level restrictions.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] === 'all' && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
									c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI'], 'ptag').exit ();

								else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] && has_tag(preg_split('/['."\r\n\t".';,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'])) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
									c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI'], 'ptag').exit ();
							}
						}
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // URIs.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris']) // URIs configured at this Level?

								foreach(preg_split('/['."\r\n\t".']+/', c_ws_plugin__s2member_ruris::fill_ruri_level_access_rc_vars($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris'], $user)) as $str)
									if($str && preg_match('/'.preg_quote($str, '/').'/'.$ci, $_SERVER['REQUEST_URI']) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && (!$user || !$user->has_cap('access_s2member_level'.$n)))
										c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'level', $n, $_SERVER['REQUEST_URI'], 'ruri').exit ();
						}
						if(is_array($ccaps_req = get_post_meta($post_id, 's2member_ccaps_req', TRUE)) && !empty($ccaps_req) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted'))
						{
							foreach($ccaps_req as $ccap) // The ``$user`` MUST satisfy ALL Custom Capability requirements. Stored as an array of Custom Capabilities.
								if(strlen($ccap) && (!$user || !$user->has_cap('access_s2member_ccap_'.$ccap)) /* Does this ``$user``, have this Custom Capability? */)
									c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'ccap', $ccap, $_SERVER['REQUEST_URI'], 'ccap').exit ();
						}
						if($bbpress_restrictions_enable && $bbpress_installed && $post->post_type === $bbpress_topic_post_type && $bbpress_topic_forum_id)
							if(is_array($ccaps_req = get_post_meta($bbpress_topic_forum_id, 's2member_ccaps_req', TRUE)) && !empty($ccaps_req) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted'))
							{
								foreach($ccaps_req as $ccap) // The ``$user`` MUST satisfy ALL Custom Capability requirements. Stored as an array of Custom Capabilities.
									if(strlen($ccap) && (!$user || !$user->has_cap('access_s2member_ccap_'.$ccap)) /* Does this ``$user``, have this Custom Capability? */)
										c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $bbpress_topic_forum_id, 'ccap', $ccap, $_SERVER['REQUEST_URI'], 'ccap').exit ();
							}
						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'] && in_array($post_id, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'])) && c_ws_plugin__s2member_no_cache::no_cache_constants('restricted') && !c_ws_plugin__s2member_sp_access::sp_access($post_id))
							c_ws_plugin__s2member_mo_page::wp_redirect_w_mop_vars('post', $post_id, 'sp', $post_id, $_SERVER['REQUEST_URI'], 'sp').exit ();
					}
					do_action('ws_plugin__s2member_during_check_post_level_access', get_defined_vars());
				}
			}
			do_action('ws_plugin__s2member_after_check_post_level_access', get_defined_vars());
		}
	}
}