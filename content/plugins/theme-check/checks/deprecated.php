<?php

class Deprecated implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$grep = '';

		$ret = true;

		$checks = array(
			// start wp-includes deprecated
			array( 'get_postdata' => 'get_post()', '1.5.1' ),
			array( 'start_wp' => 'the Loop', '1.5' ),
			array( 'the_category_id' => 'get_the_category()', '0.71' ),
			array( 'the_category_head' => 'get_the_category_by_ID()', '0.71' ),
			array( 'previous_post' => 'previous_post_link()', '2.0' ),
			array( 'next_post' => 'next_post_link()', '2.0' ),
			array( 'user_can_create_post' => 'current_user_can()', '2.0' ),
			array( 'user_can_create_draft' => 'current_user_can()', '2.0' ),
			array( 'user_can_edit_post' => 'current_user_can()', '2.0' ),
			array( 'user_can_delete_post' => 'current_user_can()', '2.0' ),
			array( 'user_can_set_post_date' => 'current_user_can()', '2.0' ),
			array( 'user_can_edit_post_comments' => 'current_user_can()', '2.0' ),
			array( 'user_can_delete_post_comments' => 'current_user_can()', '2.0' ),
			array( 'user_can_edit_user' => 'current_user_can()', '2.0' ),
			array( 'get_linksbyname' => 'get_bookmarks()', '2.1' ),
			array( 'wp_get_linksbyname' => 'wp_list_bookmarks()', '2.1' ),
			array( 'get_linkobjectsbyname' => 'get_bookmarks()', '2.1' ),
			array( 'get_linkobjects' => 'get_bookmarks()', '2.1' ),
			array( 'get_linksbyname_withrating' => 'get_bookmarks()', '2.1' ),
			array( 'get_links_withrating' => 'get_bookmarks()', '2.1' ),
			array( 'get_autotoggle' => '', '2.1' ),
			array( 'list_cats' => 'wp_list_categories', '2.1' ),
			array( 'wp_list_cats' => 'wp_list_categories', '2.1' ),
			array( 'dropdown_cats' => 'wp_dropdown_categories()', '2.1' ),
			array( 'list_authors' => 'wp_list_authors()', '2.1' ),
			array( 'wp_get_post_cats' => 'wp_get_post_categories()', '2.1' ),
			array( 'wp_set_post_cats' => 'wp_set_post_categories()', '2.1' ),
			array( 'get_archives' => 'wp_get_archives', '2.1' ),
			array( 'get_author_link' => 'get_author_posts_url()', '2.1' ),
			array( 'link_pages' => 'wp_link_pages()', '2.1' ),
			array( 'get_settings' => 'get_option()', '2.1' ),
			array( 'permalink_link' => 'the_permalink()', '1.2' ),
			array( 'permalink_single_rss' => 'permalink_rss()', '2.3' ),
			array( 'wp_get_links' => 'wp_list_bookmarks()', '2.1' ),
			array( 'get_links' => 'get_bookmarks()', '2.1' ),
			array( 'get_links_list' => 'wp_list_bookmarks()', '2.1' ),
			array( 'links_popup_script' => '', '2.1' ),
			array( 'get_linkrating' => 'sanitize_bookmark_field()', '2.1' ),
			array( 'get_linkcatname' => 'get_category()', '2.1' ),
			array( 'comments_rss_link' => 'post_comments_feed_link()', '2.5' ),
			array( 'get_category_rss_link' => 'get_category_feed_link()'. '2.5' ),
			array( 'get_author_rss_link' => 'get_author_feed_link()', '2.5' ),
			array( 'comments_rss' => 'get_post_comments_feed_link()', '2.2' ),
			array( 'create_user' => 'wp_create_user()', '2.0' ),
			array( 'gzip_compression' => '', '2.5' ),
			array( 'get_commentdata' => 'get_comment()', '2.7' ),
			array( 'get_catname' => 'get_cat_name()', '2.8' ),
			array( 'get_category_children' => 'get_term_children', '2.8' ),
			array( 'get_the_author_description' => 'get_the_author_meta(\'description\')', '2.8' ),
			array( 'the_author_description' => 'the_author_meta(\'description\')', '2.8' ),
			array( 'get_the_author_login' => 'the_author_meta(\'login\')', '2.8' ),
			array( 'get_the_author_firstname' => 'get_the_author_meta(\'first_name\')', '2.8' ),
			array( 'the_author_firstname' => 'the_author_meta(\'first_name\')', '2.8' ),
			array( 'get_the_author_lastname' => 'get_the_author_meta(\'last_name\')', '2.8' ),
			array( 'the_author_lastname' => 'the_author_meta(\'last_name\')', '2.8' ),
			array( 'get_the_author_nickname' => 'get_the_author_meta(\'nickname\')', '2.8' ),
			array( 'the_author_nickname' => 'the_author_meta(\'nickname\')', '2.8' ),
			array( 'get_the_author_email' => 'get_the_author_meta(\'email\')', '2.8' ),
			array( 'the_author_email' => 'the_author_meta(\'email\')', '2.8' ),
			array( 'get_the_author_icq' => 'get_the_author_meta(\'icq\')', '2.8' ),
			array( 'the_author_icq' => 'the_author_meta(\'icq\')', '2.8' ),
			array( 'get_the_author_yim' => 'get_the_author_meta(\'yim\')', '2.8' ),
			array( 'the_author_yim' => 'the_author_meta(\'yim\')', '2.8' ),
			array( 'get_the_author_msn' => 'get_the_author_meta(\'msn\')', '2.8' ),
			array( 'the_author_msn' => 'the_author_meta(\'msn\')', '2.8' ),
			array( 'get_the_author_aim' => 'get_the_author_meta(\'aim\')', '2.8' ),
			array( 'the_author_aim' => 'the_author_meta(\'aim\')', '2.8' ),
			array( 'get_author_name' => 'get_the_author_meta(\'display_name\')', '2.8' ),
			array( 'get_the_author_url' => 'get_the_author_meta(\'url\')', '2.8' ),
			array( 'the_author_url' => 'the_author_meta(\'url\')', '2.8' ),
			array( 'get_the_author_ID' => 'get_the_author_meta(\'ID\')', '2.8' ),
			array( 'the_author_ID' => 'the_author_meta(\'ID\')', '2.8' ),
			array( 'the_content_rss' => 'the_content_feed()', '2.9' ),
			array( 'make_url_footnote' => '', '2.9' ),
			array( '_c' => '_x()', '2.9' ),
			array( 'translate_with_context' => '_x()', '3.0' ),
			array( 'nc' => 'nx()', '3.0' ),
			array( '__ngettext' => '_n_noop()', '2.8' ),
			array( '__ngettext_noop' => '_n_noop()', '2.8' ),
			array( 'get_alloptions' => 'wp_load_alloptions()', '3.0' ),
			array( 'get_the_attachment_link' => 'wp_get_attachment_link()', '2.5' ),
			array( 'get_attachment_icon_src' => 'wp_get_attachment_image_src()', '2.5' ),
			array( 'get_attachment_icon' => 'wp_get_attachment_image()', '2.5' ),
			array( 'get_attachment_innerhtml' => 'wp_get_attachment_image()', '2.5' ),
			array( 'get_link' => 'get_bookmark()', '2.1' ),
			array( 'sanitize_url' => 'esc_url()', '2.8' ),
			array( 'clean_url' => 'esc_url()', '3.0' ),
			array( 'js_escape' => 'esc_js()', '2.8' ),
			array( 'wp_specialchars' => 'esc_html()', '2.8' ),
			array( 'attribute_escape' => 'esc_attr()', '2.8' ),
			array( 'register_sidebar_widget' => 'wp_register_sidebar_widget()', '2.8' ),
			array( 'unregister_sidebar_widget' => 'wp_unregister_sidebar_widget()', '2.8' ),
			array( 'register_widget_control' => 'wp_register_widget_control()', '2.8' ),
			array( 'unregister_widget_control' => 'wp_unregister_widget_control()', '2.8' ),
			array( 'delete_usermeta' => 'delete_user_meta()', '3.0' ),
			array( 'get_usermeta' => 'get_user_meta()', '3.0' ),
			array( 'update_usermeta' => 'update_user_meta()', '3.0' ),
			array( 'automatic_feed_links' => 'add_theme_support( \'automatic-feed-links\' )', '3.0' ),
			array( 'get_profile' => 'get_the_author_meta()', '3.0' ),
			array( 'get_usernumposts' => 'count_user_posts()', '3.0' ),
			array( 'funky_javascript_callback' => '', '3.0' ),
			array( 'funky_javascript_fix' => '', '3.0' ),
			array( 'is_taxonomy' => 'taxonomy_exists()', '3.0' ),
			array( 'is_term' => 'term_exists()', '3.0' ),
			array( 'is_plugin_page' => '$plugin_page and/or get_plugin_page_hookname() hooks', '3.1' ),
			array( 'update_category_cache' => 'No alternatives', '3.1' ),
			array( 'get_users_of_blog' => 'get_users()', '3.1' ),
			array( 'wp_timezone_supported' => '', '3.2' ),
			array( 'the_editor' => 'wp_editor', '3.3' ),
			array( 'get_user_metavalues' => '', '3.3' ),
			array( 'sanitize_user_object' => '', '3.3' ),
			array( 'get_boundary_post_rel_link' => '', '3.3' ),
			array( 'start_post_rel_link' => 'none available ', '3.3' ),
			array( 'get_index_rel_link' => '', '3.3' ),
			array( 'index_rel_link' => '', '3.3' ),
			array( 'get_parent_post_rel_link' => '', '3.3' ),
			array( 'parent_post_rel_link' => '', '3.3' ),
			array( 'wp_admin_bar_dashboard_view_site_menu' => '', '3.3' ),
			array( 'is_blog_user' => 'is_member_of_blog()', '3.3' ),
			array( 'debug_fopen' => 'error_log()', '3.3' ),
			array( 'debug_fwrite' => 'error_log()', '3.3' ),
			array( 'debug_fclose' => 'error_log()', '3.3' ),
			array( 'get_themes' => 'wp_get_themes()', '3.4' ),
			array( 'get_theme' => 'wp_get_theme()', '3.4' ),
			array( 'get_current_theme' => 'wp_get_theme()', '3.4' ),
			array( 'clean_pre' => '', '3.4' ),
			array( 'add_custom_image_header' => 'add_theme_support( \'custom-header\', $args )', '3.4' ),
			array( 'remove_custom_image_header' => 'remove_theme_support( \'custom-header\' )', '3.4' ),
			array( 'add_custom_background' => 'add_theme_support( \'custom-background\', $args )', '3.4' ),
			array( 'remove_custom_background' => 'remove_theme_support( \'custom-background\' )', '3.4' ),
			array( 'get_theme_data' => 'wp_get_theme()', '3.4' ),
			array( 'update_page_cache' => 'update_post_cache()', '3.4' ),
			array( 'clean_page_cache' => 'clean_post_cache()', '3.4' ),
			array( 'wp_explain_nonce' => 'wp_nonce_ays', '3.4.1' ),
			array( 'sticky_class' => 'post_class()', '3.5' ),
			array( '_get_post_ancestors' => '', '3.5' ),
			array( 'wp_load_image' => 'wp_get_image_editor()', '3.5' ),
			array( 'image_resize' => 'wp_get_image_editor()', '3.5' ),
			array( 'wp_get_single_post' => 'get_post()', '3.5' ),
			array( 'user_pass_ok' => 'wp_authenticate()', '3.5' ),
			array( '_save_post_hook' => '', '3.5' ),
			array( 'gd_edit_image_support' => 'wp_image_editor_supports', '3.5' ),
			array( 'get_user_id_from_string' => 'get_user_by()', '3.6' ),
			array( 'wp_convert_bytes_to_hr' => 'size_format()', '3.6' ),
			array('_search_terms_tidy'  => '', '3.7' ),
			array( 'get_blogaddress_by_domain' => '', '3.7' ),
			// end wp-includes deprecated

			// start wp-admin deprecated
			array( 'tinymce_include' => 'wp_tiny_mce()', '2.1' ),
			array( 'documentation_link' => '', '2.5' ),
			array( 'wp_shrink_dimensions' => 'wp_constrain_dimensions()','3.0' ),
			array( 'dropdown_categories' => 'wp_category_checklist()','2.6' ),
			array( 'dropdown_link_categories' => 'wp_link_category_checklist()','2.6' ),
			array( 'wp_dropdown_cats' => 'wp_dropdown_categories()','3.0' ),
			array( 'add_option_update_handler' => 'register_setting()','3.0' ),
			array( 'remove_option_update_handler' => 'unregister_setting()','3.0' ),
			array( 'codepress_get_lang' => '','3.0' ),
			array( 'codepress_footer_js' => '','3.0' ),
			array( 'use_codepress' => '','3.0' ),
			array( 'get_author_user_ids' => '','3.1' ),
			array( 'get_editable_authors' => '','3.1' ),
			array( 'get_editable_user_ids' => '','3.1' ),
			array( 'get_nonauthor_user_ids' => '','3.1' ),
			array( 'WP_User_Search' => 'WP_User_Query','3.1' ),
			array( 'get_others_unpublished_posts' => '','3.1' ),
			array( 'get_others_drafts' => '','3.1' ),
			array( 'get_others_pending' => '', '3.1' ),
			array( 'wp_dashboard_quick_press()' => '', '3.2' ),
			array( 'wp_tiny_mce' => 'wp_editor', '3.2' ),
			array( 'wp_preload_dialogs' => 'wp_editor()', '3.2' ),
			array( 'wp_print_editor_js' => 'wp_editor()', '3.2' ),
			array( 'wp_quicktags' => 'wp_editor()', '3.2' ),
			array( 'favorite_actions' => 'WP_Admin_Bar', '3.2' ),
			array( 'screen_layout' => '$current_screen->render_screen_layout()', '3.3' ),
			array( 'screen_options' => '$current_screen->render_per_page_options()', '3.3' ),
			array( 'screen_meta' => ' $current_screen->render_screen_meta()', '3.3' ),
			array( 'media_upload_image' => 'wp_media_upload_handler()', '3.3' ),
			array( 'media_upload_audio' => 'wp_media_upload_handler()', '3.3' ),
			array( 'media_upload_video' => 'wp_media_upload_handler()', '3.3' ),
			array( 'media_upload_file' => 'wp_media_upload_handler()', '3.3' ),
			array( 'type_url_form_image' => 'wp_media_insert_url_form( \'image\' )', '3.3' ),
			array( 'type_url_form_audio' => 'wp_media_insert_url_form( \'audio\' )', '3.3' ),
			array( 'type_url_form_video' => 'wp_media_insert_url_form( \'video\' )', '3.3' ),
			array( 'type_url_form_file' => 'wp_media_insert_url_form( \'file\' )', '3.3' ),
			array( 'add_contextual_help' => 'get_current_screen()->add_help_tab()', '3.3' ),
			array( 'get_allowed_themes' => 'wp_get_themes( array( \'allowed\' => true ) )', '3.4' ),
			array( 'get_broken_themes' => 'wp_get_themes( array( \'errors\' => true )', '3.4' ),
			array( 'current_theme_info' => 'wp_get_theme()', '3.4' ),
			array( '_insert_into_post_button' => '', '3.5' ),
			array( '_media_button' => '', '3.5' ),
			array( 'get_post_to_edit' => 'get_post()', '3.5' ),
			array( 'get_default_page_to_edit' => 'get_default_post_to_edit()', '3.5' ),
			array( 'wp_create_thumbnail' => 'image_resize()', '3.5' ),
			array( 'wp_nav_menu_locations_meta_box' => '', '3.6' ),
			array( 'the_attachment_links' => '', '3.7'),
			array( 'wp_update_core' => 'new Core_Upgrader()', '3.7'),
			array( 'wp_update_plugin' => 'new Plugin_Upgrader()', '3.7'),
			array( 'wp_update_theme' => 'new Theme_Upgrader()', '3.7'),
			array( 'get_screen_icon' => '', '3.8',),
			array( 'screen_icon' => '', '3.8' ),
			array( 'wp_dashboard_incoming_links' => '', '3.8',),
			array( 'wp_dashboard_incoming_links_control' => '', '3.8',),
			array( 'wp_dashboard_incoming_links_output' => '', '3.8',),
			array( 'wp_dashboard_plugins' => '', '3.8',),
			array( 'wp_dashboard_primary_control' => '', '3.8',),
			array( 'wp_dashboard_recent_comments_control' => '', '3.8',),
			array( 'wp_dashboard_secondary' => '', '3.8',),
			array( 'wp_dashboard_secondary_control' =>  '', '3.8',),
			array( 'wp_dashboard_secondary_output' => '', '3.8',),
			// end wp-admin
		);
		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $alt => $check ) {
				checkcount();
				$key = key( $check );
				$alt = $check[ $key ];
				if ( preg_match( '/[\s?]' . $key . '\(/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0], '(' ) );
					$version = $check[0];
					$grep = tc_grep( $error, $php_key );

					// Point out the deprecated function.
					$error_msg = sprintf(
						__( '%1$s found in the file %2$s. Deprecated since version %3$s.', 'theme-check' ),
						'<strong>' . $error . '()</strong>',
						'<strong>' . $filename . '</strong>',
						'<strong>' . $version . '</strong>'
					);

					// Add alternative function when available.
					if ( $alt ) {
						$error_msg .= ' ' . sprintf( __( 'Use %s instead.', 'theme-check' ), '<strong>' . $alt . '</strong>' );
					}

					// Add the precise code match that was found.
					$error_msg .= $grep;

					// Add the finalized error message.
					$this->error[] = '<span class="tc-lead tc-required">' . __('REQUIRED','theme-check') . '</span>: ' . $error_msg;

					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Deprecated;