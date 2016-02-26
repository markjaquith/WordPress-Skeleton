<?php
/**
 * s2Member's Security Gate.
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
 * @package s2Member\Security
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_security'))
{
	/**
	 * s2Member's Security Gate.
	 *
	 * @package s2Member\Security
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_security
	{
		/**
		 * s2Member's Security Gate (protects WordPress content).
		 *
		 * @package s2Member\Security
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('wp');``
		 *
		 * @return null May redirect a browser *(exiting script execution)*, when/if content is NOT available to the current User/Member.
		 */
		public static function security_gate() // s2Member's Security Gate.
		{
			do_action('ws_plugin__s2member_before_security_gate', get_defined_vars());

			if(is_category()) // Categories & other inclusives.
				c_ws_plugin__s2member_catgs::check_catg_level_access();

			else if(is_tag()) // Post/Page Tags & other inclusives.
				c_ws_plugin__s2member_ptags::check_ptag_level_access();

			else if(is_single()) // All Posts & other inclusives.
				c_ws_plugin__s2member_posts::check_post_level_access();

			else if(is_page()) // All Pages & other inclusives.
				c_ws_plugin__s2member_pages::check_page_level_access();

			else // Else, we simply look at URIs & other inclusives.
				c_ws_plugin__s2member_ruris::check_ruri_level_access();

			do_action('ws_plugin__s2member_after_security_gate', get_defined_vars());
		}

		/**
		 * s2Member's Security Gate (protects WordPress queries).
		 *
		 * @package s2Member\Security
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('pre_get_posts');``
		 *
		 * @param WP_Query $wp_query Global ``$wp_query``, by reference.
		 *
		 * @return null May filter WordPress queries, by hiding protected content which is NOT available to the current User/Member.
		 */
		public static function security_gate_query(&$wp_query = NULL) // s2Member's Security Gate.
		{
			do_action('ws_plugin__s2member_before_security_gate_query', get_defined_vars());

			c_ws_plugin__s2member_querys::query_level_access($wp_query); // By reference.

			do_action('ws_plugin__s2member_after_security_gate_query', get_defined_vars());
		}
	}
}