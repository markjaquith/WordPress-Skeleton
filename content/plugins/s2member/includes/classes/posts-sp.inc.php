<?php
/**
 * s2Member's Post protection routines *(for specific Posts)*.
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

if(!class_exists('c_ws_plugin__s2member_posts_sp'))
{
	/**
	 * s2Member's Post protection routines *(for specific Posts)*.
	 *
	 * @package s2Member\Posts
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_posts_sp
	{
		/**
		 * Handles Post Level Access *(for specific Posts)*.
		 *
		 * @package s2Member\Posts
		 * @since 3.5
		 *
		 * @param int|string $post_id Numeric Post ID.
		 * @param bool       $check_user Test permissions against the current User? Defaults to true.
		 *
		 * @return null|array Non-empty array(with details) if access is denied, else null if access is allowed.
		 */
		public static function check_specific_post_level_access($post_id = 0, $check_user = TRUE)
		{
			do_action('ws_plugin__s2member_before_check_specific_post_level_access', get_defined_vars());

			$ci       = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';
			$excluded = apply_filters('ws_plugin__s2member_check_specific_post_level_access_excluded', FALSE, get_defined_vars());

			if(!$excluded && is_numeric($post_id) && ($post_id = (int)$post_id) && ($post = get_post($post_id)) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
			{
				$post_uri = c_ws_plugin__s2member_utils_urls::parse_uri(get_permalink($post->ID)); // Get a full valid URI for this Post now.

				if(!c_ws_plugin__s2member_systematics_sp::is_wp_systematic_use_specific_page($post->ID, $post_uri)) // Do NOT touch WordPress Systematics.
				{
					$user = (is_user_logged_in() && is_object($user = wp_get_current_user()) && !empty($user->ID)) ? $user : FALSE; // Current User's object.

					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_override'] && ($login_redirection_uri = c_ws_plugin__s2member_login_redirects::login_redirection_uri($user, 'root-returns-false')) && preg_match('/^'.preg_quote($login_redirection_uri, '/').'$/'.$ci, $post_uri) && (!$check_user || !$user || !$user->has_cap('access_s2member_level0')))
						return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => 0), get_defined_vars());

					else if(!c_ws_plugin__s2member_systematics_sp::is_systematic_use_specific_page($post->ID, $post_uri)) // However, there is the one exception above.
					{
						$bbpress_restrictions_enable = apply_filters('ws_plugin__s2member_bbpress_restrictions_enable', TRUE);
						$bbpress_installed           = c_ws_plugin__s2member_utils_conds::bbp_is_installed(); // bbPress is installed?
						$bbpress_forum_post_type     = $bbpress_installed ? bbp_get_forum_post_type() : ''; // Acquire the current post type for forums.
						$bbpress_topic_post_type     = $bbpress_installed ? bbp_get_topic_post_type() : ''; // Acquire the current post type for topics.
						$bbpress_topic_forum_id      = $bbpress_installed && $post->post_type === $bbpress_topic_post_type ? bbp_get_topic_forum_id($post->ID) : 0;

						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Post Level restrictions.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] === 'all' && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if(strpos($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'], 'all-') !== FALSE && $post->post_type && (in_array('all-'.$post->post_type, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) || in_array('all-'.$post->post_type.'s', preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts']))) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if($bbpress_restrictions_enable && $bbpress_installed && $post->post_type === $bbpress_topic_post_type && strpos($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'], 'all-') !== FALSE && (in_array('all-'.$bbpress_forum_post_type, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) || in_array('all-'.$bbpress_forum_post_type.'s', preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts']))) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] && in_array($post->ID, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}
						if($bbpress_restrictions_enable && $bbpress_installed && $post->post_type === $bbpress_topic_post_type && $bbpress_topic_forum_id)
							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Forum restrictions.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] && in_array($bbpress_topic_forum_id, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
									return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());
							}
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Category Level restrictions.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] === 'all' && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] && (in_category(($catgs = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'])), $post->ID) || c_ws_plugin__s2member_utils_conds::in_descendant_category($catgs, $post->ID)) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}
						if(has_tag('', $post->ID)) // Here we take a look to see if this Post has any Tags. If so, we need to run the full set of routines against Tags also.
						{
							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Tag Level restrictions.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] === 'all' && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
									return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());

								else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] && has_tag(preg_split('/['."\r\n\t".';,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags']), $post->ID) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
									return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());
							}
						}
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // URIs.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris']) // URIs configured at this Level?

								foreach(preg_split('/['."\r\n\t".']+/', c_ws_plugin__s2member_ruris::fill_ruri_level_access_rc_vars($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris'], $user)) as $str)
									if($str && preg_match('/'.preg_quote($str, '/').'/'.$ci, $post_uri) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
										return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}
						if(is_array($ccaps_req = get_post_meta($post->ID, 's2member_ccaps_req', TRUE)) && !empty($ccaps_req))
						{
							foreach($ccaps_req as $ccap) // The $user MUST satisfy ALL Custom Capabilities. Serialized array.
								if(strlen($ccap) && (!$check_user || !$user || !$user->has_cap('access_s2member_ccap_'.$ccap)))
									return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_ccap_req' => $ccap), get_defined_vars());
						}
						if($bbpress_restrictions_enable && $bbpress_installed && $post->post_type === $bbpress_topic_post_type && $bbpress_topic_forum_id)
							if(is_array($ccaps_req = get_post_meta($bbpress_topic_forum_id, 's2member_ccaps_req', TRUE)) && !empty($ccaps_req))
							{
								foreach($ccaps_req as $ccap) // The $user MUST satisfy ALL Custom Capabilities. Serialized array.
									if(strlen($ccap) && (!$check_user || !$user || !$user->has_cap('access_s2member_ccap_'.$ccap)))
										return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_ccap_req' => $ccap), get_defined_vars());
							}
						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'] && in_array($post->ID, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'])) && (!$check_user || !c_ws_plugin__s2member_sp_access::sp_access($post->ID, 'read-only')))
							return apply_filters('ws_plugin__s2member_check_specific_post_level_access', array('s2member_sp_req' => $post->ID), get_defined_vars());
					}
					do_action('ws_plugin__s2member_during_check_specific_post_level_access', get_defined_vars());
				}
			}
			return apply_filters('ws_plugin__s2member_check_specific_post_level_access', NULL, get_defined_vars());
		}
	}
}