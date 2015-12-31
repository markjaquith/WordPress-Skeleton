<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * Used to restore the progress of a user.
 * If the global processing variable $ninja_forms_processing is set, filter the default_value for each field.
 *
 */

function ninja_forms_filter_restore_progress( $data, $field_id ){
	global $ninja_forms_loading, $ninja_forms_processing, $ninja_forms_fields;

	if ( isset ( $ninja_forms_loading ) ) {
		$field_row = $ninja_forms_loading->get_field_settings( $field_id );
	} else if ( isset ( $ninja_forms_processing ) ) {
		$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	}
	
	if ( isset ( $field_row['type'] ) ) {
		$field_type = $field_row['type'];
	} else {
		$field_type = '';
	}

	if ( isset( $ninja_forms_fields[$field_type]['esc_html'] ) ) {
		$esc_html = $ninja_forms_fields[$field_type]['esc_html'];
	} else {
		$esc_html = true;
	}

	if ( is_object( $ninja_forms_processing ) ) {
		$clear_form = $ninja_forms_processing->get_form_setting( 'clear_complete' );
		$process_complete = $ninja_forms_processing->get_form_setting( 'processing_complete' );
		if ( $process_complete != 1 OR ( $process_complete == 1 AND $clear_form != 1 ) ) {
			if ( $ninja_forms_processing->get_field_value( $field_id ) !== false ) {
				
				if ( $esc_html ) {
					if( is_array( $ninja_forms_processing->get_field_value( $field_id ) ) ){
						$default_value = ninja_forms_esc_html_deep( $ninja_forms_processing->get_field_value( $field_id ) );
					} else {
						$default_value = esc_html( $ninja_forms_processing->get_field_value( $field_id ) );
					}
				} else {
					$default_value = $ninja_forms_processing->get_field_value( $field_id );
				}
				$data['default_value'] = $default_value;
			}
		}
	}

	return $data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_filter_restore_progress', 8, 2 );