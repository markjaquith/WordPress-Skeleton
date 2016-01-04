<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 *
 * Function used to output calcluation options on each field editing section on the back-end.
 *
 * @since 2.2.28
 * @returns void
 */

function ninja_forms_edit_field_calc( $field_id, $field_data ) {
	global $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	if ( $ninja_forms_fields[$field_type]['process_field'] ) {
		if ( isset ( $field_data['calc_option'] ) ) {
			$calc_option = $field_data['calc_option'];
		} else {
			$calc_option = 0;
		}

		if ( isset ( $field_data['calc_auto_include'] ) ) {
			$calc_auto_include = $field_data['calc_auto_include'];
		} else {
			$calc_auto_include = 0;
		}

		if ( isset ( $field_data['calc_value'] ) ) {
			$calc_value = $field_data['calc_value'];
		} else {
			$calc_value = 0;
		}

		?>
		<div class="description description-wide">
		<?php
		if ( $field_type == '_checkbox' ) {
			if ( !isset ( $calc_value['unchecked'] ) ) {
				$calc_value = array();
				$calc_value['unchecked'] = 0;
				$calc_value['checked'] = 0;
			}
			ninja_forms_edit_field_el_output($field_id, 'text', sprintf( __( '%sChecked%s Calculation Value', 'ninja-forms' ), '<strong>', '</strong>' ), 'calc_value[checked]', $calc_value['checked'], 'wide', '', '', sprintf( __( 'This is the value that will be used if %sChecked%s.', 'ninja-forms' ), '<strong>', '</strong>' ) );
			ninja_forms_edit_field_el_output($field_id, 'text', sprintf( __( '%sUnchecked%s Calculation Value', 'ninja-forms' ), '<strong>', '</strong>' ), 'calc_value[unchecked]', $calc_value['unchecked'], 'wide', '', '', sprintf( __( 'This is the value that will be used if %sUnchecked%s.', 'ninja-forms' ), '<strong>', '</strong>' ) );
		}

		ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Include in the auto-total? (If enabled)', 'ninja-forms' ), 'calc_auto_include', $calc_auto_include, 'wide', '', 'ninja-forms-field-auto-total-include');
		?>
		</div>
		<?php	
	}
}

add_action( 'nf_edit_field_calculations', 'ninja_forms_edit_field_calc', 10, 2 );