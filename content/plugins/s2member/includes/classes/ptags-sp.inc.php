<?php
/**
 * s2Member's Tag protection routines *(for specific Tags)*.
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

if(!class_exists('c_ws_plugin__s2member_ptags_sp'))
{
	/**
	 * s2Member's Tag protection routines *(for specific Tags)*.
	 *
	 * @package s2Member\Tags
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_ptags_sp
	{
		/**
		 * Handles Tag Level Access *(for specific Tags)*.
		 *
		 * @package s2Member\Tags
		 * @since 3.5
		 *
		 * @param int|string $_tag Numeric Tag ID, Tag Slug, or Tag Name.
		 * @param bool       $check_user Test permissions against the current User? Defaults to true.
		 *
		 * @return null|array Non-empty array(with details) if access is denied, else null if access is allowed.
		 */
		public static function check_specific_ptag_level_access($_tag = '', $check_user = TRUE)
		{
			do_action('ws_plugin__s2member_before_check_specific_ptag_level_access', get_defined_vars());

			if($_tag && is_numeric($_tag) && is_object($term = get_term_by('id', $_tag, 'post_tag')))
			{
				$tag_id   = (int)$_tag; // Need ``$tag_id``, ``$tag_slug``, and also the ``$tag_name``.
				$tag_slug = $term->slug; // Tag slug.
				$tag_name = $term->name; // Tag name.
			}
			else if($_tag && is_string($_tag) && is_object($term = get_term_by('name', $_tag, 'post_tag')))
			{
				$tag_name = $_tag; // Need ``$tag_id``, ``$tag_slug``, and also the ``$tag_name``.
				$tag_id   = (int)$term->term_id; // Tag ID.
				$tag_slug = $term->slug; // Tag slug.
			}
			else if($_tag && is_string($_tag) && is_object($term = get_term_by('slug', $_tag, 'post_tag')))
			{
				$tag_slug = $_tag; // Need ``$tag_id``, ``$tag_slug``, and also the ``$tag_name``.
				$tag_id   = (int)$term->term_id; // Tag ID.
				$tag_name = $term->name; // Tag name.
			}
			$ci       = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';
			$excluded = apply_filters('ws_plugin__s2member_check_specific_ptag_level_access_excluded', FALSE, get_defined_vars());

			if(!$excluded && !empty($tag_id) && !empty($tag_slug) && !empty($tag_name) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
			{
				$tag_uri = c_ws_plugin__s2member_utils_urls::parse_uri(get_tag_link($tag_id)); // Get a full valid URI for this Tag.

				if(!c_ws_plugin__s2member_systematics_sp::is_wp_systematic_use_specific_page(NULL, $tag_uri)) // Do NOT touch WordPress Systematics.
				{
					$user = (is_user_logged_in() && is_object($user = wp_get_current_user()) && !empty($user->ID)) ? $user : FALSE; // Current User's object.

					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_override'] && ($login_redirection_uri = c_ws_plugin__s2member_login_redirects::login_redirection_uri($user, 'root-returns-false')) && preg_match('/^'.preg_quote($login_redirection_uri, '/').'$/'.$ci, $tag_uri) && (!$check_user || !$user || !$user->has_cap('access_s2member_level0')))
						return apply_filters('ws_plugin__s2member_check_specific_ptag_level_access', array('s2member_level_req' => 0), get_defined_vars());

					else if(!c_ws_plugin__s2member_systematics_sp::is_systematic_use_specific_page(NULL, $tag_uri)) // Never restrict Systematics. However, there is 1 exception above.
					{
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Tag Level restrictions. Go through each Level.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] === 'all' && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_ptag_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] && (in_array($tag_name, ($tags = preg_split('/['."\r\n\t".';,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags']))) || in_array($tag_slug, $tags)) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_ptag_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // URIs. Go through each Level.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris']) // URIs configured at this Level?

								foreach(preg_split('/['."\r\n\t".']+/', c_ws_plugin__s2member_ruris::fill_ruri_level_access_rc_vars($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris'], $user)) as $str)
									if($str && preg_match('/'.preg_quote($str, '/').'/'.$ci, $tag_uri) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
										return apply_filters('ws_plugin__s2member_check_specific_ptag_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}
					}
					do_action('ws_plugin__s2member_during_check_specific_ptag_level_access', get_defined_vars());
				}
			}
			return apply_filters('ws_plugin__s2member_check_specific_ptag_level_access', NULL, get_defined_vars());
		}
	}
}