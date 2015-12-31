<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 *
 * Function that adds the address group dropdown to the user information field items.
 *
 * @since 2.2.37
 * @returns void
 */

function ninja_forms_user_info_fields_groups( $field_id, $field_data ){
	global $ninja_forms_fields;
	$field = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field['type'];
	$default_user_info = 0;
	if ( isset ( $ninja_forms_fields[$field_type]['edit_options'] ) and is_array( $ninja_forms_fields[$field_type]['edit_options'] ) ) {
		foreach ( $ninja_forms_fields[$field_type]['edit_options'] as $option ) {
			if ( isset ( $option['name'] ) and $option['name'] == 'user_info_field_group' and isset ( $option['default'] ) ) {
				$default_user_info = $option['default'];
				break;
			}
		}
	}

	if ( ( isset ( $field_data['user_info_field_group'] ) AND $field_data['user_info_field_group'] == 1 ) or ( ( !isset ( $field_data['user_info_field_group'] ) or $field_data['user_info_field_group'] !== 0 ) and $default_user_info == 1 ) ) {
		$options = array(
			array( 'name' => '- '.__( 'None', 'ninja-forms' ), 'value' => '' ),
			array( 'name' => __( 'Billing', 'ninja-forms' ), 'value' => 'billing' ),
			array( 'name' => __( 'Shipping', 'ninja-forms' ), 'value' => 'shipping' ),
			array( 'name' => __( 'Custom', 'ninja-forms' ).' ->', 'value' => 'custom' ),
		);

		if ( isset ( $field_data['user_info_field_group_name'] ) ) {
			$group_name = $field_data['user_info_field_group_name'];
		} else { 
			$group_name = '';
		}

		if ( isset ( $field_data['user_info_field_group_custom'] ) ) {
			$group_custom = $field_data['user_info_field_group_custom'];
		} else {
			$group_custom = '';
		}

		if ( $group_name == 'custom' ) {
			$custom_class = '';
		} else {
			$custom_class = 'hidden';
		}
		
		ninja_forms_edit_field_el_output( $field_id, 'select', __( 'User Info Field Group', 'ninja-forms' ), 'user_info_field_group_name', $group_name, 'thin', $options, 'user-info-group-name widefat' );
		ninja_forms_edit_field_el_output( $field_id, 'text', __( 'Custom Field Group', 'ninja-forms' ), 'user_info_field_group_custom', $group_custom, 'thin', '', 'user-info-custom-group widefat '.$custom_class, '', $custom_class );
	}
}

add_action( 'ninja_forms_edit_field_after_registered', 'ninja_forms_user_info_fields_groups', 10, 2 );