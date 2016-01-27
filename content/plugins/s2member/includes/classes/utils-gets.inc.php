<?php
/**
 * Get utilities.
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
 * @package s2Member\Utilities
 * @since 3.5
 */
if(!defined('WPINC')) // MUST have WordPress.
	exit ('Do not access this file directly.');

if(!class_exists('c_ws_plugin__s2member_utils_gets'))
{
	/**
	 * Get utilities.
	 *
	 * @package s2Member\Utilities
	 * @since 3.5
	 */
	class c_ws_plugin__s2member_utils_gets
	{
		/**
		 * Retrieves a unique array of all Category IDs in the database.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @uses {@link http://codex.wordpress.org/Function_Reference/get_terms get_terms()}
		 *
		 * @return array Unique array of all Category IDs *(as integers)*.
		 */
		public static function get_all_category_ids()
		{
			if(!($category_ids = wp_cache_get('all_category_ids', 'category')))
			{
				$category_ids = get_terms('category', array('fields' => 'ids', 'get' => 'all'));
				wp_cache_add('all_category_ids', $category_ids, 'category');
			}
			if(is_array($category_ids)) // Use a WP function for this.
				$category_ids = c_ws_plugin__s2member_utils_arrays::force_integers((array)$category_ids);

			return (!empty($category_ids) && is_array($category_ids)) ? array_unique($category_ids) : array();
		}

		/**
		 * Retrieves a unique array of all child Category IDs, within a specific parent Category.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param int|string $parent_category A numeric Category ID.
		 *
		 * @return array Unique array of all Category IDs *(as integers)* in ``$parent_category``.
		 */
		public static function get_all_child_category_ids($parent_category = 0)
		{
			if(is_numeric($parent_category) && $parent_category && is_array($child_categories = get_categories('child_of='.$parent_category.'&hide_empty=0')))
				foreach($child_categories as $child_category) // Go through child Categories.
					$child_category_ids[] = (int)$child_category->term_id;

			return (!empty($child_category_ids) && is_array($child_category_ids)) ? array_unique($child_category_ids) : array();
		}

		/**
		 * Retrieves a unique array of all Tag IDs in the database.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @return array Unique array of all Tag IDs *(as integers)*.
		 */
		public static function get_all_tag_ids()
		{
			foreach((array)get_tags('hide_empty=0') as $tag)
				$tag_ids[] = (int)$tag->term_id; // Collect Tag's ID.

			return (!empty($tag_ids) && is_array($tag_ids)) ? array_unique($tag_ids) : array();
		}

		/**
		 * Converts a comma-delimited list of: Tag slugs/names/ids, into a unique array of all IDs.
		 *
		 * @package s2Member\Utilities
		 * @since 111101
		 *
		 * @param string $tags Tag slugs/names/IDs; comma-delimited.
		 *
		 * @return array Unique array of Tag IDs *(as integers)*. With Tag slugs/names converted to IDs.
		 */
		public static function get_tags_converted_to_ids($tags = '')
		{
			foreach(preg_split('/['."\r\n\t".';,]+/', (string)$tags) as $tag)
			{
				if(($tag = trim($tag)) && is_numeric($tag)) // Force integers.
					$tag_ids[] = ($tag_id = (int)$tag); // Force integer values here.

				else if($tag && is_string($tag)) // A string (i.e., a tag name or a tag slug)?
				{
					if(is_object($term = get_term_by('name', $tag, 'post_tag')))
						$tag_ids[] = (int)$term->term_id;

					else if(is_object($term = get_term_by('slug', $tag, 'post_tag')))
						$tag_ids[] = (int)$term->term_id;
				}
			}
			return (!empty($tag_ids) && is_array($tag_ids)) ? array_unique($tag_ids) : array();
		}

		/**
		 * Retrieves a unique array of all published Post IDs in the database.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param string $post_type Optional. If provided, return all Post IDs of a specific Post Type.
		 *   Otherwise, return all Post IDs that are NOT of these Post Types: `page|attachment|nav_menu_item|revision`.
		 *
		 * @return array Unique array of all Post IDs *(as integers)*, including Custom Post Types; or all Post IDs of a specific Post Type.
		 */
		public static function get_all_post_ids($post_type = '')
		{
			/** @var wpdb $wpdb WordPress DB object instance. */
			global $wpdb; // Global DB object reference.

			if(is_array($post_ids = $wpdb->get_col("SELECT `ID` FROM `".$wpdb->posts."` WHERE `post_status` = 'publish' AND ".(($post_type) ? "`post_type` = '".esc_sql((string)$post_type)."'" : "`post_type` NOT IN('page','attachment','nav_menu_item','revision')"))))
				$post_ids = c_ws_plugin__s2member_utils_arrays::force_integers($post_ids);

			return (!empty($post_ids) && is_array($post_ids)) ? array_unique($post_ids) : array();
		}

		/**
		 * Retrieves a unique array of all published Child Post IDs in the database.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @param int|string|array $parent_ids One or more parent IDs to collect children for.
		 *
		 * @param string           $post_type Optional. If provided, return all Post IDs of a specific Post Type.
		 *   Otherwise, return all Post IDs that are NOT of these Post Types: `page|attachment|nav_menu_item|revision`.
		 *
		 * @return array Unique array of all Child Post IDs *(as integers)*, including Custom Post Types; or all Child Post IDs of a specific Post Type.
		 */
		public static function get_all_child_post_ids($parent_ids = array(), $post_type = '')
		{
			/** @var wpdb $wpdb WordPress DB object instance. */
			global $wpdb; // Global DB object reference.

			if(($parent_ids = (array)$parent_ids)) // Force an array value.
				if(is_array($post_ids = $wpdb->get_col("SELECT `ID` FROM `".$wpdb->posts."` WHERE `post_status` = 'publish'".
				                                       " AND ".($post_type ? "`post_type` = '".esc_sql((string)$post_type)."'" : "`post_type` NOT IN('page','attachment','nav_menu_item','revision')").
				                                       " AND `post_parent` IN('".implode("','", $parent_ids)."')"))
				) $post_ids = c_ws_plugin__s2member_utils_arrays::force_integers($post_ids);

			return (!empty($post_ids) && is_array($post_ids)) ? array_unique($post_ids) : array();
		}

		/**
		 * Retrieves a unique array of all published Page IDs in the database.
		 *
		 * @package s2Member\Utilities
		 * @since 3.5
		 *
		 * @return array Unique array of all Page IDs *(as integers)*.
		 */
		public static function get_all_page_ids()
		{
			/** @var wpdb $wpdb WordPress DB object instance. */
			global $wpdb; // Global DB object reference.

			if(is_array($page_ids = $wpdb->get_col("SELECT `ID` FROM `".$wpdb->posts."` WHERE `post_status` = 'publish' AND `post_type` = 'page'")))
				$page_ids = c_ws_plugin__s2member_utils_arrays::force_integers($page_ids);

			return (!empty($page_ids) && is_array($page_ids)) ? array_unique($page_ids) : array();
		}

		/**
		 * Retrieves a unique array of all Singular IDs in the database that require Custom Capabilities.
		 *
		 * @package s2Member\Utilities
		 * @since 111101
		 *
		 * @return array Unique array of all Singular IDs *(as integers)* that require Custom Capabilities.
		 */
		public static function get_all_singular_ids_with_ccaps()
		{
			/** @var wpdb $wpdb WordPress DB object instance. */
			global $wpdb; // Global DB object reference.

			if(is_array($singular_ids = $wpdb->get_col("SELECT `post_id` FROM `".$wpdb->postmeta."` WHERE `meta_key` = 's2member_ccaps_req' AND `meta_value` != ''")))
				$singular_ids = c_ws_plugin__s2member_utils_arrays::force_integers($singular_ids);

			return (!empty($singular_ids) && is_array($singular_ids)) ? array_unique($singular_ids) : array();
		}

		/**
		 * Retrieves a unique array of unavailable Singular IDs that require Custom Capabilities.
		 *
		 * Only returns Singular IDs that require Custom Capabilities;
		 *   and ONLY those which are NOT satisfied by ``$user``.
		 *
		 * @package s2Member\Utilities
		 * @since 111101
		 *
		 * @param WP_User $user Optional. A `WP_User` object. If this is a valid `WP_User` object, test against this ``$user``, else all are unavailable.
		 *
		 * @return array Unique array of all Singular IDs *(as integers)* NOT available to ``$user``, due to Custom Capability Restrictions.
		 */
		public static function get_unavailable_singular_ids_with_ccaps($user = NULL)
		{
			/** @var wpdb $wpdb WordPress DB object instance. */
			global $wpdb; // Global DB object reference.

			if(is_array($results = $wpdb->get_results("SELECT `".$wpdb->postmeta."`.`post_id`, `".$wpdb->postmeta."`.`meta_value`, `".$wpdb->posts."`.`post_type`".
			                                          " FROM `".$wpdb->posts."`, `".$wpdb->postmeta."` WHERE `".$wpdb->posts."`.`ID` = `".$wpdb->postmeta."`.`post_id`".
			                                          " AND `".$wpdb->postmeta."`.`meta_key` = 's2member_ccaps_req' AND `".$wpdb->postmeta."`.`meta_value` != ''")))
			{
				$bbpress_restrictions_enable = apply_filters('ws_plugin__s2member_bbpress_restrictions_enable', TRUE);
				$bbpress_installed           = c_ws_plugin__s2member_utils_conds::bbp_is_installed(); // bbPress is installed?
				$bbpress_forum_post_type     = $bbpress_installed ? bbp_get_forum_post_type() : ''; // Acquire the current post type for forums.
				$bbpress_topic_post_type     = $bbpress_installed ? bbp_get_topic_post_type() : ''; // Acquire the current post type for topics.

				foreach($results as $r) // Now we need to check Custom Capabilities against ``$user``. If ``$user`` is a valid `WP_User` object, else all are unavailable.
				{
					if(!is_object($user) || empty($user->ID)) // No ``$user`` object? Maybe not logged-in?.
						$singular_ids[] = (int)$r->post_id; // It's NOT available. There is no ``$user``.

					else if(is_array($ccaps = @unserialize($r->meta_value))) // Make sure we unserialize.
					{
						foreach($ccaps as $ccap) // Test for Custom Capability Restrictions now.
							if(strlen($ccap) && !$user->has_cap('access_s2member_ccap_'.$ccap))
							{
								$singular_ids[] = (int)$r->post_id; // It's NOT available.
								break; // Break now, no need to continue in this loop.
							}
					}
					if($bbpress_restrictions_enable && $bbpress_installed && $r->post_type === $bbpress_forum_post_type)
						if(!empty($singular_ids) && in_array((int)$r->post_id, $singular_ids, TRUE))
						{
							if(is_array($child_results = $wpdb->get_results("SELECT `".$wpdb->posts."`.`ID` as `post_id` FROM `".$wpdb->posts."`".
							                                                " WHERE `".$wpdb->posts."`.`post_parent` = '".esc_sql($r->post_id)."'".
							                                                " AND `".$wpdb->posts."`.`post_type` = '".esc_sql($bbpress_topic_post_type)."'")))
								foreach($child_results as $child_r) $singular_ids[] = (int)$child_r->post_id;
						}
				}
			}
			return (!empty($singular_ids) && is_array($singular_ids)) ? array_unique($singular_ids) : array();
		}

		/**
		 * Retrieves a unique array of all Singular IDs that require Specific Post/Page Access.
		 *
		 * @package s2Member\Utilities
		 * @since 111101
		 *
		 * @param bool $exclude_conflicts Optional. Defaults to false. If true, return ONLY those which are NOT in conflict with any other Restriction Types.
		 *   The ``$exclude_conflicts`` argument should be used whenever we introduce a list of option values to a site owner. Helping them avoid mishaps.
		 *   Please note, the ``$exclude_conflicts`` argument implements a resource-intensive processing routine.
		 *
		 * @return array Unique array of all Singular IDs *(as integers)* that require Specific Post/Page Access.
		 */
		public static function get_all_singular_ids_with_sp($exclude_conflicts = FALSE)
		{
			if(is_array(($singular_ids = ($GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'] && is_array($singular_ids = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids']))) ? $singular_ids : array())))
				$singular_ids = c_ws_plugin__s2member_utils_arrays::force_integers($singular_ids);

			if(!empty($singular_ids) && is_array($singular_ids) && $exclude_conflicts /* Return ONLY those which are NOT in conflict with other Restrictions? */)
			{
				$x_ids = array($GLOBALS['WS_PLUGIN__']['s2member']['o']['login_welcome_page'], $GLOBALS['WS_PLUGIN__']['s2member']['o']['membership_options_page'], $GLOBALS['WS_PLUGIN__']['s2member']['o']['file_download_limit_exceeded_page']);

				$x_ids = array_merge($x_ids, c_ws_plugin__s2member_utils_gets::get_all_singular_ids_with_ccaps());

				for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
				{
					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'] === 'all')
					{
						$catgs = c_ws_plugin__s2member_utils_gets::get_all_category_ids();
						$x_ids = array_merge($x_ids, c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($catgs));
						continue; // Continue. The `all` specification is absolute. There's nothing more.
					}
					foreach(($catgs = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_catgs'])) as $catg)
						$catgs = array_merge($catgs, c_ws_plugin__s2member_utils_gets::get_all_child_category_ids($catg));

					$x_ids = array_merge($x_ids, c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($catgs));
					unset  ($catgs, $catg); // Just a little housekeeping here.
				}
				for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
				{
					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags'] === 'all')
					{
						$tags  = c_ws_plugin__s2member_utils_gets::get_all_tag_ids();
						$x_ids = array_merge($x_ids, c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($tags));
						continue; // Continue. The `all` specification is absolute. There's nothing more.
					}
					$tags = c_ws_plugin__s2member_utils_gets::get_tags_converted_to_ids($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_ptags']);

					$x_ids = array_merge($x_ids, c_ws_plugin__s2member_utils_gets::get_singular_ids_in_terms($tags));
					unset ($tags); // Just a little housekeeping here.
				}
				for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
				{
					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'] === 'all')
					{
						$x_ids = array_merge($x_ids, c_ws_plugin__s2member_utils_gets::get_all_post_ids());
						continue; // Continue. The `all` specification is absolute. There's nothing more.
					}
					foreach(($posts = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_posts'])) as $p)
						if(strpos($p, 'all-') === 0 && preg_match('/^all-(.+)$/', $p, $m) /* Protecting `all-` of a specific Post Type? */)
							if((is_array($p_of_type = c_ws_plugin__s2member_utils_gets::get_all_post_ids($m[1])) || (substr($m[1], -1) === 's' && is_array($_p_of_type = c_ws_plugin__s2member_utils_gets::get_all_post_ids(substr($m[1], 0, -1))))) && !empty($p_of_type))
								$x_ids = array_merge($x_ids, $p_of_type); // Merge all Posts of this Post Type.

					$x_ids = array_merge($x_ids, $posts); // Merge together.
					unset ($posts, $p, $m, $p_of_type); // Just a little housekeeping here.
				}
				for($n = 0; $n <= $GLOBALS['WS_PLUGIN__']['s2member']['c']['levels']; $n++)
				{
					if($GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages'] === 'all')
					{
						$x_ids = array_merge($x_ids, c_ws_plugin__s2member_utils_gets::get_all_page_ids());
						continue; // Continue. The `all` specification is absolute. There's nothing more.
					}
					$pages = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['level'.$n.'_pages']);

					$x_ids = array_merge($x_ids, $pages); // Merge.
					unset ($pages); // Just a little housekeeping here.
				}
				$x_ids        = array_unique(c_ws_plugin__s2member_utils_arrays::force_integers($x_ids));
				$singular_ids = array_diff($singular_ids, $x_ids);
			}
			return (!empty($singular_ids) && is_array($singular_ids)) ? array_unique($singular_ids) : array();
		}

		/**
		 * Retrieves a unique array of unavailable Singular IDs that require Specific Post/Page Access.
		 *
		 * Only returns Singular IDs that require Specific Post/Page Access;
		 *   and ONLY those which are NOT satisfied by the current Visitor.
		 *
		 * @package s2Member\Utilities
		 * @since 111101
		 *
		 * @param bool $exclude_conflicts Optional. Defaults to false. If true, return ONLY those which are NOT in conflict with any other Restriction Types.
		 *   The ``$exclude_conflicts`` argument should be used whenever we introduce a list of option values to a site owner. Helping them avoid mishaps.
		 *   Please note, the ``$exclude_conflicts`` argument implements a resource-intensive processing routine.
		 *
		 * @return array Unique array of all Singular IDs *(as integers)* NOT available to current Visitor, due to Specific Post/Page Restrictions.
		 */
		public static function get_unavailable_singular_ids_with_sp($exclude_conflicts = FALSE)
		{
			if($GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'] && is_array($_singular_ids = preg_split('/['."\r\n\t".'\s;,]+/', $GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'])))
				foreach($_singular_ids as $_singular_id) // Now check access to this Singular, against the current Visitor, via read-only ``c_ws_plugin__s2member_sp_access::sp_access()``.
					if(is_numeric($_singular_id) && !c_ws_plugin__s2member_sp_access::sp_access($_singular_id, 'read-only'))
						$singular_ids[] = (int)$_singular_id;

			if(!empty($singular_ids) && is_array($singular_ids) && $exclude_conflicts)
			{
				$all_singular_ids_not_conflicting = c_ws_plugin__s2member_utils_gets::get_all_singular_ids_with_sp('exclude-conflicts');
				foreach($singular_ids as $s => $singular_id) // Weed out anything that's in conflict here.
					if(!in_array($singular_id, $all_singular_ids_not_conflicting))
						unset($singular_ids[$s]); // Housekeeping.
			}
			return (!empty($singular_ids) && is_array($singular_ids)) ? array_unique($singular_ids) : array();
		}

		/**
		 * Retrieves a unique array of all published Singulars, protected with Specific Post/Page Access.
		 *
		 * @package s2Member\Utilities
		 * @since 111101
		 *
		 * @uses {@link http://codex.wordpress.org/Function_Reference/get_posts get_posts()}
		 *
		 * @param bool $exclude_conflicts Optional. Defaults to false. If true, return ONLY those which are NOT in conflict with any other Restriction Types.
		 *   The ``$exclude_conflicts`` argument should be used whenever we introduce a list of option values to a site owner. Helping them avoid mishaps.
		 *   Please note, the ``$exclude_conflicts`` argument implements a resource-intensive processing routine.
		 *
		 * @return array Unique array of all Singulars *(i.e., Posts/Pages )* protected with Specific Post/Page Access.
		 *   Includes Custom Post Types also, as specified by site owner's Specific Post/Page Restrictions.
		 */
		public static function get_all_singulars_with_sp($exclude_conflicts = FALSE)
		{
			$singulars = ($GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids'] && is_array($singulars = get_posts('post_status=publish&post_type=any&include='.$GLOBALS['WS_PLUGIN__']['s2member']['o']['specific_ids']))) ? $singulars : array();

			if(!empty($singulars) && is_array($singulars) && $exclude_conflicts)
			{
				$all_singular_ids_not_conflicting = c_ws_plugin__s2member_utils_gets::get_all_singular_ids_with_sp('exclude-conflicts');
				foreach($singulars as $s => $singular) // Weed out anything that's in conflict here.
					if(!in_array($singular->ID, $all_singular_ids_not_conflicting))
						unset($singulars[$s]); // Housekeeping.
			}
			return (!empty($singulars) && is_array($singulars)) ? c_ws_plugin__s2member_utils_arrays::array_unique($singulars) : array();
		}

		/**
		 * Retrieves a unique array of Singular IDs in the database, within specific term IDs.
		 *
		 * Only returns Singular IDs that are within the ``$terms`` passed through this function.
		 *
		 * @package s2Member\Utilities
		 * @since 110912
		 *
		 * @param array $terms Required. An array of term IDs.
		 *
		 * @return array Unique array of all Singular IDs *(as integers)* within the ``$terms`` passed through this function.
		 */
		public static function get_singular_ids_in_terms($terms = array())
		{
			/** @var wpdb $wpdb WordPress DB object instance. */
			global $wpdb; // Global DB object reference.

			if(!empty($terms) && is_array($terms) && is_array($singular_ids = $wpdb->get_col("SELECT `object_id` FROM `".$wpdb->term_relationships."` WHERE `term_taxonomy_id` IN (SELECT `term_taxonomy_id` FROM `".$wpdb->term_taxonomy."` WHERE `term_id` IN('".implode("','", $terms)."'))")))
				$singular_ids = c_ws_plugin__s2member_utils_arrays::force_integers($singular_ids);

			return (!empty($singular_ids) && is_array($singular_ids)) ? array_unique($singular_ids) : array();
		}
	}
}