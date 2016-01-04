<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Function that checks to see if we are processing a submission
 *
 * @since 2.6.2
 * @return void
 */

function nf_check_post() {
	if( isset ( $_POST['_ninja_forms_display_submit'] ) AND absint ( $_POST['_ninja_forms_display_submit'] ) == 1 ){
		// If our nonce isn't set, bail
		if ( !isset ( $_POST['_wpnonce'] ) )
			return false;

		// If our nonce doesn't validate, bail
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'nf_form_' . absint( $_POST['_form_id'] ) ) )
			return false;

		$ajax = Ninja_Forms()->form( absint( $_POST['_form_id'] ) )->get_setting( 'ajax' );

		if( $ajax != 1 ){
			add_action( 'init', 'ninja_forms_setup_processing_class', 5 );
			add_action( 'init', 'ninja_forms_pre_process', 999 );
		}else if( $ajax == 1 AND $_REQUEST['action'] == 'ninja_forms_ajax_submit' ){
			add_action( 'init', 'ninja_forms_setup_processing_class', 5 );
			add_action( 'init', 'ninja_forms_pre_process', 999 );
		}
	}
}

add_action( 'plugins_loaded', 'nf_check_post' );


/*
 *
 * Function that checks to see if session variables have been set that indicate we are on a success page.
 * If we are, intialize the global processing class for access to the form's settings and user values.
 *
 * @since 2.2.45
 * @return void
 */

function ninja_forms_session_class_setup(){
	$transient_id = Ninja_Forms()->session->get( 'nf_transient_id' );
	if ( $transient_id && ! is_admin() ) {
		if ( get_transient( $transient_id ) !== false ) {
			add_action( 'init', 'ninja_forms_setup_processing_class', 5 );
		}
	}
}

add_action( 'init', 'ninja_forms_session_class_setup', 4 );

/*
 *
 * Function that clears any transient values that have been stored in cache for this user.
 *
 * @since 2.2.45
 * @return void
 */

function ninja_forms_clear_transient() {
	//set_transient( 'ninja_forms_test', 'TEST', DAY_IN_SECONDS );
	ninja_forms_delete_transient();
}

add_action( 'wp_head', 'ninja_forms_clear_transient' );

function ninja_forms_page_append_check(){
	global $post, $ninja_forms_append_page_form_id;

	if ( ! empty ( $_REQUEST['preview'] ) && ! empty ( $_REQUEST['preview'] ) ) {
		if(is_array($ninja_forms_append_page_form_id)){
			unset($ninja_forms_append_page_form_id);
		}
	}

	if(!isset($ninja_forms_append_page_form_id)){
		$ninja_forms_append_page_form_id = array();
	}

	if ( $post ) {
		$form_ids = ninja_forms_get_form_ids_by_post_id($post->ID);
		if(is_array($form_ids) AND !empty($form_ids)){
			foreach($form_ids as $form_id){
				$ninja_forms_append_page_form_id[] = $form_id;
				//remove_filter('the_content', 'wpautop');
				add_filter( 'the_content', 'ninja_forms_append_to_page', 9999 );
			}
		}		
	}
}

add_action('wp_head', 'ninja_forms_page_append_check');

function ninja_forms_append_to_page($content){
	global $ninja_forms_append_page_form_id;

	if( !is_admin() && is_main_query() && ( is_page() OR is_single() ) ){
		$form = '';
		if(is_array($ninja_forms_append_page_form_id) && !empty($ninja_forms_append_page_form_id)){
			foreach($ninja_forms_append_page_form_id as $form_id){
				$form .= ninja_forms_return_echo('ninja_forms_display_form', $form_id);
			}
		}else{
			$form = ninja_forms_return_echo('ninja_forms_display_form', $ninja_forms_append_page_form_id);
		}
		$content .= $form;
	}
	return $content;
}

/**
 * Main function used to display a Ninja Form.
 * ninja_forms_display_form() can be called anywhere on in a WordPress template.
 * By default it's called by the ninja_forms_append_to_page() function in the main ninja_forms.php file.
 *
**/

function ninja_forms_display_form( $form_id = '' ){
	//Define our global variables
	global $post, $wpdb, $ninja_forms_fields, $ninja_forms_loading, $ninja_forms_processing;

	//Get the settings telling us whether or not we should clear/hide the completed form.
	//Check to see if the form_id has been sent.
	if($form_id == ''){
		$function = false;
		if(isset($_REQUEST['form_id'])){ //If it hasn't, set it to our requested form_id. Sometimes this function can be called without an expressly passed form_id.
			$form_id = absint( $_REQUEST['form_id'] );
		}
	}else{
		$function = true;
	}
	if($form_id != ''){ //Make sure that we have an active form_id.
		do_action( 'nf_before_display_loading', $form_id );

		// Instantiate our loading global singleton.
		if ( !isset ( $ninja_forms_processing ) or ( isset ( $ninja_forms_processing ) and $ninja_forms_processing->get_form_ID() != $form_id ) ) {
			$ninja_forms_loading = new Ninja_Forms_Loading( $form_id );
		}

		// Run our two loading action hooks.
		do_action( 'ninja_forms_display_pre_init', $form_id );
		do_action( 'ninja_forms_display_init', $form_id );

		if ( isset ( $ninja_forms_loading ) ) {
			$ajax = $ninja_forms_loading->get_form_setting( 'ajax' );
		} else {
			$ajax = $ninja_forms_processing->get_form_setting( 'ajax' );
		}

		if ( !$ajax ) {
			$ajax = 0;
		}

		if ( isset ( $ninja_forms_loading ) ) {
			$logged_in = $ninja_forms_loading->get_form_setting( 'logged_in' );
		} else {
			$logged_in = $ninja_forms_processing->get_form_setting( 'logged_in' );
		}

		if ( !$logged_in ) {
			$logged_in = 0;
		}

		$display = true;

		if( $logged_in == 1 ){
			if( !is_user_logged_in() ){
				$display = false;
			}
		}

		$display = apply_filters( 'ninja_forms_display_show_form', $display, $form_id );

		if($ajax == 1){
			$url = admin_url( 'admin-ajax.php' );
			$url = esc_url_raw( add_query_arg('action', 'ninja_forms_ajax_submit', $url ) );
		}else{
			$url = '';
		}

		if( $display ){
			do_action( 'ninja_forms_before_form_display', $form_id );

			do_action('ninja_forms_display_before_form_wrap', $form_id);
			do_action('ninja_forms_display_open_form_wrap', $form_id);

			do_action('ninja_forms_display_before_form_title', $form_id);
			do_action('ninja_forms_display_form_title', $form_id);
			do_action('ninja_forms_display_after_form_title', $form_id);

			do_action('ninja_forms_display_before_form', $form_id);
			do_action('ninja_forms_display_open_form_tag', $form_id);
			do_action('ninja_forms_display_after_open_form_tag', $form_id);

			do_action('ninja_forms_display_before_fields', $form_id);
			do_action('ninja_forms_display_fields', $form_id);
			do_action('ninja_forms_display_after_fields', $form_id);

			do_action('ninja_forms_display_close_form_tag', $form_id);
			do_action('ninja_forms_display_after_form', $form_id);

			do_action('ninja_forms_display_close_form_wrap', $form_id);
			do_action('ninja_forms_display_after_form_wrap', $form_id);

			do_action( 'ninja_forms_after_form_display', $form_id );

			do_action( 'ninja_forms_display_js', $form_id );
			do_action( 'ninja_forms_display_css', $form_id );
		}else{
			do_action( 'ninja_forms_display_user_not_logged_in', $form_id );
		}
	}
}
