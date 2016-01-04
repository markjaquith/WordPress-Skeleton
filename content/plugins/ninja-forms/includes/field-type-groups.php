<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_register_common_field_type_groups(){
	$args = array(
		'name' => __( 'Standard Fields', 'ninja-forms' ),
	);
	ninja_forms_register_field_type_group( 'standard_fields', $args );

	$args = array(
		'name' => __( 'Layout Elements', 'ninja-forms' ),
	);
	ninja_forms_register_field_type_group( 'layout_elements', $args );

	$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
	if ( !$add_field )
		return false;

	$args = array(
		'name' => __( 'Post Creation', 'ninja-forms' ),
	);
	ninja_forms_register_field_type_group( 'create_post', $args );
}

if ( isset ( $_REQUEST['page'] ) ) {
	if ( strpos( $_REQUEST['page'], 'ninja-forms' ) !== false ) {
		add_action( 'admin_init', 'ninja_forms_register_common_field_type_groups', 8 );
	}
}