<?php
/**
 * Query protection routines.
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
 * @package s2Member\Queries
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_querys'))
{
	/**
	 * Query protection routines.
	 *
	 * @package s2Member\Queries
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_querys
	{
		/**
		 * The current WordPress query object reference.
		 *
		 * @package s2Member\Queries
		 * @since 110912
		 *
		 * @var null|object
		 */
		public static $current_wp_query;

		/**
		 * Forces query Filters *(on-demand)*.
		 *
		 * s2Member respects the query var: `suppress_filters`.
		 * If you need to make a query without it being Filtered, use  ``$wp_query->set ('suppress_filters', true);``.
		 *
		 * @package s2Member\Queries
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('pre_get_posts');``
		 *
		 * @param WP_Query $wp_query Expects ``$wp_query`` by reference.
		 */
		public static function force_query_level_access(&$wp_query = NULL)
		{
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_force_query_level_access', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			c_ws_plugin__s2member_querys::query_level_access($wp_query, TRUE);

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_after_force_query_level_access', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.
		}

		/**
		 * Filter all WordPress queries.
		 *
		 * s2Member respects the query var: `suppress_filters`.
		 * If you need to make a query without it being Filtered, use  ``$wp_query->set ('suppress_filters', true);``.
		 *
		 * @package s2Member\Queries
		 * @since 3.5
		 *
		 * @attaches-to ``add_action('pre_get_posts');``
		 *
		 * @param WP_Query $wp_query Expects ``$wp_query`` by reference, from the Filter.
		 * @param bool     $force Optional. Defaults to false. If true, we bypass all standard conditions.
		 *   However, s2Member will NEVER bypass `supress_filters`.
		 *
		 * @todo For improved reliability, modify other query vars associated with exclusions/inclusions. Like `tag_slug__in`?
		 *   See: {@link http://codex.wordpress.org/Class_Reference/WP_Query#Parameters WP_Query#Parameters}
		 *
		 * @todo Make it possible to force filtering, even when used in combination with Query Conditionals and ``get_posts()``, which auto-supresses.
		 *   Or, perhaps strengthen the existing ``$force`` parameter in this regard.
		 *
		 * @see Workaround for bbPress and the `s` key. See: <http://bit.ly/1obLpv4>
		 */
		public static function query_level_access(&$wp_query = NULL, $force = FALSE)
		{
			global $wpdb; // Global DB object reference.

			c_ws_plugin__s2member_querys::$current_wp_query = &$wp_query;

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_before_query_level_access', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			if(is_object($wp_query) && !$wp_query->get('___s2_is_bbp_has_replies'))
				// Workaround for bbPress and the `s` key. See: <http://bit.ly/1obLpv4>
				c_ws_plugin__s2member_querys::_query_level_access_sys($wp_query); // Systematics.

			remove_filter('comment_feed_where', 'c_ws_plugin__s2member_querys::_query_level_access_coms', 100, 2);
			remove_filter('wp_get_nav_menu_items', 'c_ws_plugin__s2member_querys::_query_level_access_navs', 100);

			if(is_object($wpdb) && is_object($wp_query) && (($o = $GLOBALS['WS_PLUGIN__']['s2member']['o']['filter_wp_query']) || $force))
			{
				if((!is_admin() || c_ws_plugin__s2member_querys::_is_admin_ajax_search($wp_query)) && !$wp_query->get('___s2_is_bbp_has_replies') /* See: <http://bit.ly/1obLpv4> */)
				{
					$suppressing_filters = $wp_query->get('suppress_filters'); // Filter suppression on?

					if((!$suppressing_filters && $force) // Forcing this routine bypasses all of these other conditionals. Works with API function ``attach_s2member_query_filters()``.
					   || (!$suppressing_filters && in_array('all', $o) && !($wp_query->is_main_query() && $wp_query->is_singular())) // Don't create 404 errors. Allow Security Gate to handle these.
					   || (!$suppressing_filters && (in_array('all', $o) || in_array('searches', $o)) && $wp_query->is_search()) // Or, is this a search results query, possibly via AJAX: `admin-ajax.php`?
					   || (!$suppressing_filters && (in_array('all', $o) || in_array('feeds', $o)) && $wp_query->is_feed() && !$wp_query->is_comment_feed()) // Or, is this a feed; and it's NOT for comments?
					   || (!$suppressing_filters && (in_array('all', $o) || in_array('comment-feeds', $o)) && $wp_query->is_feed() && $wp_query->is_comment_feed()) // Or, is this a feed; and it IS indeed for comments?
					   || (($suppressing_filters !== 'n/a') && (in_array('all', $o) || in_array('nav-menus', $o)) && in_array('wp_get_nav_menu_items', ($callers = (isset($callers) ? $callers : c_ws_plugin__s2member_utilities::callers()))))
					)
					{
						if(!$suppressing_filters && (in_array('all', $o) || in_array('comment-feeds', $o)) && $wp_query->is_feed() && $wp_query->is_comment_feed())
							add_filter('comment_feed_where', 'c_ws_plugin__s2member_querys::_query_level_access_coms', 100, 2);

						if($suppressing_filters !== 'n/a' && (in_array('all', $o) || in_array('nav-menus', $o))) // Suppression irrelevant here.
							if(in_array('wp_get_nav_menu_items', ($callers = (isset($callers) ? $callers : c_ws_plugin__s2member_utilities::callers()))))
								add_filter('wp_get_nav_menu_items', 'c_ws_plugin__s2member_querys::_query_level_access_navs', 100);

						if((is_user_logged_in() && is_object($user = wp_get_current_user()) && !empty($user->ID) && ($user_id = $user->ID)) || !($user = FALSE))
						{
							$bbpress_restrictions_enable = apply_filters('ws_plugin__s2member_bbpress_restrictions_enable', TRUE);
							$bbpress_installed           = c_ws_plugin__s2member_utils_conds::bbp_is_installed(); // bbPress is installed?
							$bbpress_forum_post_type     = $bbpress_installed ? bbp_get_forum_post_type() : ''; // Acquire the current post type for forums.
							$bbpress_topic_post_type     = $bbpress_installed ? bbp_get_topic_post_type() : ''; // Acquire the current post type for topics.

							if(!$user && ($_lwp = (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page']))
							{
								$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), array($_lwp))));
								$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), array($_lwp))));
							}
							if(!$user && ($_dep = (int)$GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page']))
							{
								$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), array($_dep))));
								$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), array($_dep))));
							}
							if(is_array($_ccaps = c_ws_plugin__s2member_utils_gets::get_unavailable_singular_ids_with_ccaps($user)) && !empty($_ccaps))
							{
								$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $_ccaps)));
								$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), $_ccaps)));
							}
							if(is_array($_sps = c_ws_plugin__s2member_utils_gets::get_unavailable_singular_ids_with_sp()) && !empty($_sps))
							{
								$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $_sps)));
								$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), $_sps)));
							}
							unset($_lwp, $_dep, $_ccaps, $_sps); // A little housekeeping here. Ditch these temporary variables.

							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Category Level Restrictions.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] === 'all' && (!$user || !current_user_can('access_s2member_level'.$n)))
								{
									$wp_query->set('category__in', array()); // Include no other Categories.
									$wp_query->set('category__not_in', ($_catgs = c_ws_plugin__s2member_utils_gets::get_all_category_ids()));
									$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), ($_singulars = c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($_catgs)))));
									$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $_singulars)));
									break; // All Categories will be locked down.
								}
								else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] && (!$user || !current_user_can('access_s2member_level'.$n)))
								{
									foreach(($_catgs = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'])) as $_catg)
										$_catgs = array_merge($_catgs, c_ws_plugin__s2member_utils_gets::get_all_child_category_ids($_catg));

									$wp_query->set('category__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('category__in')), $_catgs)));
									$wp_query->set('category__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('category__not_in')), $_catgs)));
									$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), ($_singulars = c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($_catgs)))));
									$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $_singulars)));
								}
							}
							unset($_catgs, $_catg, $_singulars); // A little housekeeping here. Ditch these temporary variables.

							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Tag Level Restrictions.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] === 'all' && (!$user || !current_user_can('access_s2member_level'.$n)))
								{
									$wp_query->set('tag__in', array()); // Include no other Tags.
									$wp_query->set('tag__not_in', ($_tags = c_ws_plugin__s2member_utils_gets::get_all_tag_ids()));
									$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), ($_singulars = c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($_tags)))));
									$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $_singulars)));
									break; // ALL Tags will be locked down.
								}
								else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] && (!$user || !current_user_can('access_s2member_level'.$n)))
								{
									$_tags = c_ws_plugin__s2member_utils_gets::get_tags_converted_to_ids($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags']);

									$wp_query->set('tag__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('tag__in')), $_tags)));
									$wp_query->set('tag__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('tag__not_in')), $_tags)));
									$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), ($_singulars = c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($_tags)))));
									$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $_singulars)));
								}
							}
							unset($_tags, $_tag, $_singulars); // A little housekeeping here. Ditch these temporary variables.

							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Post Level Restrictions.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] === 'all' && (!$user || !current_user_can('access_s2member_level'.$n)))
								{
									$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), ($_posts = c_ws_plugin__s2member_utils_gets::get_all_post_ids()))));
									$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), $_posts)));
									break; // ALL Posts will be locked down.
								}
								else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] && (!$user || !current_user_can('access_s2member_level'.$n)))
								{
									foreach(($_posts = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) as $_p)
									{
										if(strpos($_p, 'all-') === 0 && preg_match('/^all-(.+?)$/', $_p, $_m)) // Protecting `all-` of a specific Post Type?
											if((is_array($_p_of_type = c_ws_plugin__s2member_utils_gets::get_all_post_ids($_m[1])) || (substr($_m[1], -1) === 's' && is_array($_p_of_type = c_ws_plugin__s2member_utils_gets::get_all_post_ids(substr($_m[1], 0, -1))))) && !empty($_p_of_type))
												$_posts = array_merge($_posts, $_p_of_type); // Merge all Posts of this Post Type.
									}
									if($bbpress_restrictions_enable && $bbpress_installed)
										$_posts = array_merge($_posts, c_ws_plugin__s2member_utils_gets::get_all_child_post_ids($_posts, $bbpress_topic_post_type));
									$_posts = array_unique(c_ws_plugin__s2member_utils_arrays::force_integers($_posts)); // Force integers.

									$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $_posts)));
									$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), $_posts)));
								}
							}
							unset($_posts, $_p, $_m, $_p_of_type); // A little housekeeping here. Ditch these temporary variables.

							for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Page Level Restrictions.
							{
								if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'] === 'all' && (!$user || !current_user_can('access_s2member_level'.$n)))
								{
									$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), ($_pages = c_ws_plugin__s2member_utils_gets::get_all_page_ids()))));
									$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), $_pages)));
									break; // ALL Pages will be locked down.
								}
								else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'] && (!$user || !current_user_can('access_s2member_level'.$n)))
								{
									$_pages = c_ws_plugin__s2member_utils_arrays::force_integers(preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages']));

									$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $_pages)));
									$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), $_pages)));
								}
							}
							unset($_pages); // A little housekeeping here. Ditch these temporary variables.
						}
						foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
						do_action('ws_plugin__s2member_during_query_level_access', get_defined_vars());
						unset($__refs, $__v); // Housekeeping.
					}
				}
			}
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('ws_plugin__s2member_after_query_level_access', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.
		}

		/**
		 * Always filters Systematics in search results & feeds.
		 *
		 * s2Member respects the query var: `suppress_filters`.
		 * If you need to make a query without it being Filtered, use  ``$wp_query->set ('suppress_filters', true);``.
		 *
		 * @package s2Member\Queries
		 * @since 3.5
		 *
		 * @param WP_Query $wp_query Expects ``$wp_query`` by reference.
		 */
		public static function _query_level_access_sys(&$wp_query = NULL)
		{
			global $wpdb; // Global DB object reference.

			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('_ws_plugin__s2member_before_query_level_access_sys', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.

			if(is_object($wpdb) && is_object($wp_query) && !($suppressing_filters = $wp_query->get('suppress_filters')))
				if((!is_admin() && ($wp_query->is_search() || $wp_query->is_feed())) || c_ws_plugin__s2member_querys::_is_admin_ajax_search($wp_query))
				{
					$s = $systematics = array($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page'], $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'], $GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page']);
					$s = $systematics = c_ws_plugin__s2member_utils_arrays::force_integers($s); // Force integer values here.

					$wp_query->set('post__not_in', array_unique(array_merge(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__not_in')), $s)));
					$wp_query->set('post__in', array_unique(array_diff(c_ws_plugin__s2member_utils_arrays::force_integers((array)$wp_query->get('post__in')), $s)));

					foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
					do_action('_ws_plugin__s2member_during_query_level_access_sys', get_defined_vars());
					unset($__refs, $__v); // Housekeeping.
				}
			foreach(array_keys(get_defined_vars()) as $__v) $__refs[$__v] =& $$__v;
			do_action('_ws_plugin__s2member_after_query_level_access_sys', get_defined_vars());
			unset($__refs, $__v); // Housekeeping.
		}

		/**
		 * Filters WordPress navigation menu items.
		 *
		 * @package s2Member\Queries
		 * @since 110912
		 *
		 * @attaches-to ``add_filter('wp_get_nav_menu_items');``
		 *
		 * @param array $items Expects an array of items to be passed through by the Filter.
		 *
		 * @return array The revised array of ``$items``.
		 */
		public static function _query_level_access_navs($items = array())
		{
			global $wpdb; // Global DB object reference.
			$wp_query = &c_ws_plugin__s2member_querys::$current_wp_query;

			if(is_array($items) && !empty($items) && is_object($wpdb) && is_object($wp_query) && $wp_query->get('suppress_filters') !== 'n/a')
			{
				$x_post_ids = (array)$wp_query->get('post__not_in');
				$x_post_ids = c_ws_plugin__s2member_utils_arrays::force_integers($x_post_ids);

				$x_taxonomy_ids = array_merge((array)$wp_query->get('category__not_in'), (array)$wp_query->get('tag__not_in'));
				$x_taxonomy_ids = c_ws_plugin__s2member_utils_arrays::force_integers($x_taxonomy_ids);

				foreach($items as $key => $item) // Filter through all navigational menu ``$items``.
					if(isset($item->ID, $item->object_id, $item->type) && (int)$item->ID !== (int)$item->object_id && in_array($item->type, array('post_type', 'category'), TRUE))
						if(($item->type === 'post_type' && in_array($item->object_id, $x_post_ids)) || ($item->type === 'category' && in_array($item->object_id, $x_taxonomy_ids)))
						{
							foreach($items as $child_key => $child_item) // Loop back through all ``$items``, looking for children.
								if(!empty($child_item->menu_item_parent) && (int)$child_item->menu_item_parent === (int)$item->ID)
									unset($items[$child_key]); // Remove this ``$child_item``, belonging to an excluded parent.
							unset($items[$key]); // Exclude the parent ``$item`` now.
						}
			}
			remove_filter('wp_get_nav_menu_items', 'c_ws_plugin__s2member_querys::_query_level_access_navs', 100);

			return apply_filters('_ws_plugin__s2member_query_level_access_navs', $items, get_defined_vars());
		}

		/**
		 * Filters ``$cwhere`` query portion.
		 *
		 * @package s2Member\Queries
		 * @since 110912
		 *
		 * @attaches-to ``add_filter('comment_feed_where');``
		 *
		 * @param string   $cwhere Expects the SQL `WHERE` portion to be passed through by the Filter.
		 * @param WP_Query $wp_query Expects ``$wp_query`` by reference, from the Filter.
		 *
		 * @return string The revised ``$cwhere`` string.
		 */
		public static function _query_level_access_coms($cwhere = '', &$wp_query = NULL)
		{
			global $wpdb; // Global DB object reference.

			if(is_string($cwhere) && is_object($wpdb) && is_object($wp_query) && !$wp_query->get('suppress_filters'))
			{
				$x_terms     = array_merge((array)$wp_query->get('category__not_in'), (array)$wp_query->get('tag__not_in'));
				$x_terms     = array_unique(c_ws_plugin__s2member_utils_arrays::force_integers($x_terms)); // Force integer values.
				$x_singulars = c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($x_terms); // Singulars.

				$cwhere .= " AND `".$wpdb->comments."`.`comment_post_ID` NOT IN('".implode("','", (array)$wp_query->get('post__not_in'))."')";
				$cwhere .= " AND `".$wpdb->comments."`.`comment_post_ID` NOT IN('".implode("','", $x_singulars)."')";
			}
			remove_filter('comment_feed_where', 'c_ws_plugin__s2member_querys::_query_level_access_coms', 100, 2);

			return apply_filters('_ws_plugin__s2member_query_level_access_coms', $cwhere, get_defined_vars());
		}

		/**
		 * AJAX search via `admin-ajax.php`?
		 *
		 * @package s2Member\Queries
		 * @since 110912
		 *
		 * @param WP_Query $wp_query Expects ``$wp_query`` by reference.
		 *
		 * @return bool True if it's an AJAX search via `admin-ajax.php`, else false.
		 */
		public static function _is_admin_ajax_search(&$wp_query = NULL)
		{
			global $wpdb; // Global DB object reference.

			if(is_object($wpdb) && is_object($wp_query) && is_admin() && $wp_query->is_search())
				if(defined('DOING_AJAX') && DOING_AJAX && !empty($_REQUEST['action']) && (did_action('wp_ajax_nopriv_'.$_REQUEST['action']) || did_action('wp_ajax_'.$_REQUEST['action'])))
					return apply_filters('_ws_plugin__s2member_is_admin_ajax_search', TRUE, get_defined_vars());

			return apply_filters('_ws_plugin__s2member_is_admin_ajax_search', FALSE, get_defined_vars());
		}

		/**
		 * Filters WordPress Page queries that use wp_list_pages()
		 *
		 * @package s2Member\Queries
		 * @since 130617
		 *
		 * @attaches-to ``add_filter('wp_list_pages_excludes');``
		 *
		 * @param array $excludes An array of any existing excludes.
		 *
		 * @return array The array of ``$excludes``.
		 */
		public static function _query_level_access_list_pages($excludes = array())
		{
			if(!$GLOBALS['WS_PLUGIN__']['s2member']['o']['filter_wp_query']
			   || (!in_array('all', $GLOBALS['WS_PLUGIN__']['s2member']['o']['filter_wp_query'])
			       && !in_array('pages', $GLOBALS['WS_PLUGIN__']['s2member']['o']['filter_wp_query']))
			) return $excludes; // Not applicable.

			$systematics   = array(); // Initialize.
			$systematics[] = $GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page'];
			if(!is_user_logged_in()) $systematics[] = $GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page'];
			$systematics = c_ws_plugin__s2member_utils_arrays::force_integers($systematics); // Force integer values here.
			$excludes    = array_merge($excludes, $systematics);

			for($n = $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n >= 0; $n--) // Here we need to exclude any Page not available to the current user.
			{
				if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'] === 'all' && !current_user_can('access_s2member_level'.$n))
					$excludes = array_merge($excludes, c_ws_plugin__s2member_utils_arrays::force_integers(c_ws_plugin__s2member_utils_gets::get_all_page_ids()));
				else if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'] && !current_user_can('access_s2member_level'.$n))
					$excludes = array_merge($excludes, c_ws_plugin__s2member_utils_arrays::force_integers(preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'])));
			}
			return apply_filters('_ws_plugin__s2member_query_level_access_list_pages', $excludes, get_defined_vars());
		}

		/**
		 * Flags a WP Query has being a `bbp_has_replies()` query.
		 *
		 * @package s2Member\Queries
		 * @since 140906
		 *
		 * @attaches-to ``add_filter('bbp_has_replies_query');``
		 *
		 * @param array $args Query arguments passed by the filter.
		 *
		 * @return array The array of ``$args``.
		 *
		 * @see Workaround for bbPress and the `s` key. See: <http://bit.ly/1obLpv4>
		 */
		public static function _bbp_flag_has_replies($args)
		{
			return array_merge($args, array('___s2_is_bbp_has_replies' => TRUE));
		}
	}
}