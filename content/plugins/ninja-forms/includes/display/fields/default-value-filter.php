<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 *
 * Function that filters default values, replacing defined strings with the approparite values.
 *
 * @since 2.4
 * @return $data
 */

function ninja_forms_default_value_filter( $data, $field_id ) {
	global $ninja_forms_fields, $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$default_value = $ninja_forms_loading->get_field_value( $field_id );
		$field_type = $ninja_forms_loading->get_field_setting( $field_id, 'type' );
	} else {
		$default_value = $ninja_forms_processing->get_field_value( $field_id );
		$field_type = $ninja_forms_processing->get_field_setting( $field_id, 'type' );
	}

	if ( $default_value === false and isset ( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
		if ( is_string( $default_value ) )
			$default_value = do_shortcode( $default_value );

	}

	if ( isset ( $ninja_forms_fields[ $field_type ]['process_field'] ) and $ninja_forms_fields[ $field_type ]['process_field'] ) {
		$data['default_value'] = $default_value;
	}

	return $data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_default_value_filter', 7, 2 );