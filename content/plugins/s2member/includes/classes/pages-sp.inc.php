<?php
/**
 * s2Member's Page protection routines *(for specific Pages)*.
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
 * @package s2Member\Pages
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_pages_sp'))
{
	/**
	 * s2Member's Page protection routines *(for specific Pages)*.
	 *
	 * @package s2Member\Pages
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_pages_sp
	{
		/**
		 * Handles Page Level Access *(for specific Pages)*.
		 *
		 * @package s2Member\Pages
		 * @since 3.5
		 *
		 * @param int|string $page_id Numeric Page ID.
		 * @param bool       $check_user Test permissions against the current User? Defaults to true.
		 *
		 * @return null|array Non-empty array(with details) if access is denied, else null if access is allowed.
		 */
		public static function check_specific_page_level_access($page_id = 0, $check_user = TRUE)
		{
			do_action('ws_plugin__s2member_before_check_specific_page_level_access', get_defined_vars());

			$ci       = $GLOBALS['WS_PLUGIN__']['s2member']['o']['ruris_case_sensitive'] ? '' : 'i';
			$excluded = apply_filters('ws_plugin__s2member_check_specific_page_level_access_excluded', FALSE, get_defined_vars());

			if(!$excluded && is_numeric($page_id) && ($page_id = (int)$page_id) && ($page = get_post($page_id)) && $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
			{
				$page_uri = c_ws_plugin__s2member_utils_urls::parse_uri(get_page_link($page->ID)); // Get a full valid URI for this Page now.

				if(!c_ws_plugin__s2member_systematics_sp::is_wp_systematic_use_specific_page($page->ID, $page_uri)) // Do NOT touch WordPress Systematics.
				{
					$user = (is_user_logged_in() && is_object($user = wp_get_current_user()) && !empty($user->ID)) ? $user : FALSE; // Current User's object.

					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page'] && $page->ID === (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page'] && (!$check_user || !$user || !$user->has_cap('access_s2member_level0')) && $page->ID !== (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
						return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => 0), get_defined_vars());

					else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_redirection_override'] && ($login_redirection_uri = c_ws_plugin__s2member_login_redirects::login_redirection_uri($user, 'root-returns-false')) && preg_match('/^'.preg_quote($login_redirection_uri, '/').'$/'.$ci, $page_uri) && (!$check_user || !$user || !$user->has_cap('access_s2member_level0')) && $page->ID !== (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
						return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => 0), get_defined_vars());

					else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page'] && $page->ID === (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page'] && (!$check_user || !$user || !$user->has_cap('access_s2member_level0')) && $page->ID !== (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'])
						return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => 0), get_defined_vars());

					else if(!c_ws_plugin__s2member_systematics_sp::is_systematic_use_specific_page($page->ID, $page_uri)) // However, there are 3 exceptions above.
					{
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Page Level restrictions. Go through each Level.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'] === 'all' && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if(strpos($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'], 'all-') !== FALSE && (in_array('all-page', preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) || in_array('all-pages', preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts']))) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => $n), get_defined_vars());

							else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'] && in_array($page->ID, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'])) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
								return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}
						if(has_tag('', $page->ID)) // Here we take a look to see if this Page has any Tags. If so, we need to run the full set of routines against Tags also.
						{
							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Tag Level restrictions (possibly through Page Tagger). Go through each Level.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] === 'all' && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
									return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => $n), get_defined_vars());

								else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] && has_tag(preg_split('/['."\r\n\t".';,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags']), $page->ID) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
									return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => $n), get_defined_vars());
							}
						}
						for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // URIs. Go through each Level.
						{
							if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris']) // URIs configured at this Level?

								foreach(preg_split('/['."\r\n\t".']+/', c_ws_plugin__s2member_ruris::fill_ruri_level_access_rc_vars($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ruris'], $user)) as $str)
									if($str && preg_match('/'.preg_quote($str, '/').'/'.$ci, $page_uri) && (!$check_user || !$user || !$user->has_cap('access_s2member_level'.$n)))
										return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_level_req' => $n), get_defined_vars());
						}
						if(is_array($ccaps_req = get_post_meta($page->ID, 's2member_ccaps_req', TRUE)) && !empty($ccaps_req))
						{
							foreach($ccaps_req as $ccap) // The ``$user`` MUST satisfy ALL Custom Capabilities.
								if(strlen($ccap) && (!$check_user || !$user || !$user->has_cap('access_s2member_ccap_'.$ccap)))
									return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_ccap_req' => $ccap), get_defined_vars());
						}
						if($GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'] && in_array($page->ID, preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'])) && (!$check_user || !c_ws_plugin__s2member_sp_access::sp_access($page->ID, 'read-only')))
							return apply_filters('ws_plugin__s2member_check_specific_page_level_access', array('s2member_sp_req' => $page->ID), get_defined_vars());
					}
					do_action('ws_plugin__s2member_during_check_specific_page_level_access', get_defined_vars());
				}
			}
			return apply_filters('ws_plugin__s2member_check_specific_page_level_access', NULL, get_defined_vars());
		}
	}
}