<?php if ( ! defined( 'ABSPATH' ) ) exit;

// For backwards compatibilty, make sure that this function doesn't already exist.
if ( !function_exists ( 'ninja_forms_register_field_post_tags' ) ) {
	function ninja_forms_register_sidebar_post_fields(){
		$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
		if ( !$add_field )
			return false;

		if(isset($_REQUEST['form_id'])){
			$form_id = absint( $_REQUEST['form_id'] );
		}else{
			$form_id = '';
		}
		if( function_exists( 'ninja_forms_get_current_tab' ) ){
			$current_tab = ninja_forms_get_current_tab();
		}
		if($form_id != '' AND $current_tab == 'fields'){
			$form_row = ninja_forms_get_form_by_id($form_id);
			if(is_array($form_row) AND !empty($form_row)){
				$form_data = $form_row['data'];
				if( isset( $form_data['create_post'] ) ){
					$create_post = $form_data['create_post'];
				}else{
					$create_post = '';
				}
				
			}else{
				$create_post = '';
			}
		}else{
			$create_post = '';
		}
		$args = array(
			'name' => 'Post Creation Fields',
			'page' => 'ninja-forms',
			'tab' => 'builder',
			'display_function' => 'ninja_forms_sidebar_display_fields'
		);
		if($create_post == 1){
			if( function_exists( 'ninja_forms_register_sidebar' ) ){
				ninja_forms_register_sidebar('post_fields', $args);
			}
		}
	}

	add_action( 'admin_init', 'ninja_forms_register_sidebar_post_fields' );
}