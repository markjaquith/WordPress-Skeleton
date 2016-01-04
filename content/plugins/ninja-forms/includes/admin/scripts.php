<?php if ( ! defined( 'ABSPATH' ) ) exit;
//Load up our WP Ninja Custom Form JS files.
function ninja_forms_admin_css(){
	$plugin_settings = nf_get_settings();

	wp_enqueue_style( 'jquery-smoothness', NINJA_FORMS_URL .'css/smoothness/jquery-smoothness.css' );
	wp_enqueue_style( 'ninja-forms-admin', NINJA_FORMS_URL .'css/ninja-forms-admin.css?nf_ver=' . NF_PLUGIN_VERSION );
	wp_enqueue_style( 'nf-admin-modal', NINJA_FORMS_URL .'assets/css/admin-modal.css?nf_ver=' . NF_PLUGIN_VERSION );

	add_filter('admin_body_class', 'ninja_forms_add_class');

}

function ninja_forms_add_class($classes) {
	// add 'class-name' to the $classes array
	$classes .= ' nav-menus-php';
	// return the $classes array
	return $classes;
}

function ninja_forms_admin_js(){
	global $version_compare, $public_query_vars;

	$form_id = isset ( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : '';

	if ( defined( 'NINJA_FORMS_JS_DEBUG' ) && NINJA_FORMS_JS_DEBUG ) {
		$suffix = '';
		$src = 'dev';
	} else {
		$suffix = '.min';
		$src = 'min';
	}

	$plugin_settings = nf_get_settings();
	if(isset($plugin_settings['date_format'])){
		$date_format = $plugin_settings['date_format'];
	}else{
		$date_format = 'm/d/Y';
	}

	$date_format = ninja_forms_date_to_datepicker($date_format);

	$datepicker_args = array();
	if ( !empty( $date_format ) ) {
		$datepicker_args['dateFormat'] = $date_format;
	}

	wp_enqueue_script('ninja-forms-admin',
	NINJA_FORMS_URL . 'js/' . $src .'/ninja-forms-admin' . $suffix . '.js?nf_ver=' . NF_PLUGIN_VERSION,
	array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'jquery-ui-draggable', 'jquery-ui-droppable', 'nf-admin-modal' ) );

	wp_localize_script( 'ninja-forms-admin', 'ninja_forms_settings', array( 'nf_ajax_nonce' => wp_create_nonce( 'nf_ajax'), 'form_id' => $form_id, 'datepicker_args' => apply_filters( 'ninja_forms_admin_forms_datepicker_args', $datepicker_args ), 'add_fav_prompt' => __( 'What would you like to name this favorite?', 'ninja-forms' ), 'add_fav_error' => __( 'You must supply a name for this favorite.', 'ninja-forms' ), 'deactivate_all_licenses_confirm' => __( 'Really deactivate all licenses?', 'ninja-forms' ) ) );
	wp_localize_script( 'ninja-forms-admin', 'nf_conversion_title', __( 'Reset the form conversion process for v2.9+', 'ninja-forms' ) );
	wp_localize_script( 'ninja-forms-admin', 'nf_nuke_title', __( 'Remove ALL Ninja Forms data upon uninstall?', 'ninja-forms' ) );


	if ( isset ( $_REQUEST['page'] ) && $_REQUEST['page'] == 'ninja-forms' && isset ( $_REQUEST['tab'] ) ) {
		wp_enqueue_script( 'nf-builder',
			NINJA_FORMS_URL . 'assets/js/' . $src .'/builder' . $suffix . '.js?nf_ver=' . NF_PLUGIN_VERSION,
			array( 'backbone' ) );

		if ( '' != $form_id ) {
			$fields = Ninja_Forms()->form( $form_id )->fields;

			$current_tab = ninja_forms_get_current_tab();
			$current_page = isset ( $_REQUEST['page'] ) ? esc_html( $_REQUEST['page'] ) : '';

			foreach ( $fields as $field_id => $field ) {
				$fields[ $field_id ]['metabox_state'] = 0;
			}

			$form_status = Ninja_Forms()->form( $form_id )->get_setting( 'status' );
			$form_title = Ninja_Forms()->form( $form_id )->get_setting( 'form_title' );

			wp_localize_script( 'nf-builder', 'nf_admin', array( 'edit_form_text' => __( 'Edit Form', 'ninja-forms' ), 'form_title' => $form_title, 'form_status' => $form_status, 'fields' => $fields, 'saved_text' => __( 'Saved', 'ninja-forms' ), 'save_text' => __( 'Save', 'ninja-forms' ), 'saving_text' => __( 'Saving...', 'ninja-forms' ), 'remove_field' => __( 'Remove this field? It will be removed even if you do not save.', 'ninja-forms' ) ) );
		
			$reserved_terms = array( 
				'attachment',
				'attachment_id',
				'author',
				'author_name',
				'calendar',
				'cat',
				'category',
				'category__and',
				'category__in',
				'category__not_in',
				'category_name',
				'comments_per_page',
				'comments_popup',
				'customize_messenger_channel',
				'customized',
				'cpage',
				'day',
				'debug',
				'error',
				'exact',
				'feed',
				'hour',
				'link_category',
				'm',
				'minute',
				'monthnum',
				'more',
				'name',
				'nav_menu',
				'nonce',
				'nopaging',
				'offset',
				'order',
				'orderby',
				'p',
				'page',
				'page_id',
				'paged',
				'pagename',
				'pb',
				'perm',
				'post',
				'post__in',
				'post__not_in',
				'post_format',
				'post_mime_type',
				'post_status',
				'post_tag',
				'post_type',
				'posts',
				'posts_per_archive_page',
				'posts_per_page',
				'preview',
				'robots',
				's',
				'search',
				'second',
				'sentence',
				'showposts',
				'static',
				'subpost',
				'subpost_id',
				'tag',
				'tag__and',
				'tag__in',
				'tag__not_in',
				'tag_id',
				'tag_slug__and',
				'tag_slug__in',
				'taxonomy',
				'tb',
				'term',
				'theme',
				'type',
				'w',
				'withcomments',
				'withoutcomments',
				'year',
			);

			wp_localize_script( 'nf-builder', 'wp_reserved_terms', $reserved_terms );
		}
	}
}
