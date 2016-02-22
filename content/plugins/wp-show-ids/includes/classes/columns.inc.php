<?php
/*
Copyright: © 2009 WebSharks, Inc. ( coded in the USA )
<mailto:support@websharks-inc.com> <http://www.websharks-inc.com/>

Released under the terms of the GNU General Public License.
You should have received a copy of the GNU General Public License,
along with this software. In the main directory, see: /licensing/
If not, see: <http://www.gnu.org/licenses/>.
*/
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
/**/
if (!class_exists ("c_ws_plugin__wp_show_ids_columns"))
	{
		class c_ws_plugin__wp_show_ids_columns
			{
				/*
				Configure all of the required Hooks/Filters for columns & css.
				Attach to: add_action("admin_init");
				*/
				public static function configure ()
					{
						global $wp_post_types, $wp_taxonomies; /* Global references. */
						/**/
						do_action ("ws_plugin__wp_show_ids_before_configure", get_defined_vars ());
						/**/
						add_action ("admin_head", "c_ws_plugin__wp_show_ids_handlers::echo_css");
						/**/
						add_filter ("manage_users_columns", "c_ws_plugin__wp_show_ids_handlers::return_column", 100, 1);
						add_filter ("manage_users_custom_column", "c_ws_plugin__wp_show_ids_handlers::return_value", 100, 3);
						/**/
						add_filter ("manage_edit-comments_columns", "c_ws_plugin__wp_show_ids_handlers::return_column", 100, 1);
						add_action ("manage_comments_custom_column", "c_ws_plugin__wp_show_ids_handlers::echo_value", 100, 2);
						/**/
						add_filter ("manage_link-manager_columns", "c_ws_plugin__wp_show_ids_handlers::return_column", 100, 1);
						add_action ("manage_link_custom_column", "c_ws_plugin__wp_show_ids_handlers::echo_value", 100, 2);
						/**/
						add_filter ("manage_upload_columns", "c_ws_plugin__wp_show_ids_handlers::return_column", 100, 1);
						add_action ("manage_media_custom_column", "c_ws_plugin__wp_show_ids_handlers::echo_value", 100, 2);
						/**/
						if (is_array ($wp_taxonomies) && !empty ($wp_taxonomies)) /* All; including Custom Taxonomies. */
							foreach (array_keys ($wp_taxonomies) as $taxonomy)
								{
									add_filter ("manage_edit-" . $taxonomy . "_columns", "c_ws_plugin__wp_show_ids_handlers::return_column", 100, 1);
									add_filter ("manage_" . $taxonomy . "_custom_column", "c_ws_plugin__wp_show_ids_handlers::return_value", 100, 3);
								}
						/**/
						if (is_array ($wp_post_types) && !empty ($wp_post_types)) /* All; including Custom Post Types. */
							foreach (array_keys ($wp_post_types) as $type)
								{
									add_filter ("manage_" . $type . "_posts_columns", "c_ws_plugin__wp_show_ids_handlers::return_column", 100, 1);
									add_action ("manage_" . $type . "_posts_custom_column", "c_ws_plugin__wp_show_ids_handlers::echo_value", 100, 2);
								}
						/**/
						do_action ("ws_plugin__wp_show_ids_after_configure", get_defined_vars ());
						/**/
						return; /* Return for uniformity. */
					}
			}
	}
?>