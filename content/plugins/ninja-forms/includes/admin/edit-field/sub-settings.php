<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 *
 * Function used to output the submission sorting option on the backend.
 *
 * @since 2.9
 * @returns void
 */

function nf_edit_field_sort_numeric( $field_id, $field_data ) {
	global $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];

	if ( $ninja_forms_fields[$field_type]['process_field'] && $field_type != '_calc' ) {
		if ( isset ( $field_data['admin_label'] ) ) {
			$admin_label = $field_data['admin_label'];
		} else {
			$admin_label = '';
		}
		if ( isset ( $field_data['num_sort'] ) ) {
			$num_sort = $field_data['num_sort'];
		} else {
			$num_sort = '';
		}
		?>
		<div class="description description-wide">
		<?php
		ninja_forms_edit_field_el_output( $field_id, 'checkbox', __( 'Sort as numeric', 'ninja-forms' ), 'num_sort', $num_sort, 'wide', '', '', __( 'If this box is checked, this column in the submissions table will sort by number.', 'ninja-forms' ) );
		?>
		</div>
		<?php
	}
}

add_action( 'nf_edit_field_advanced', 'nf_edit_field_sort_numeric', 9, 2 );

/*
 *
 * Function used to output our admin label option on the backend.
 *
 * @since 2.9
 * @returns void
 */

function nf_edit_field_admin_label( $field_id, $field_data ) {
	global $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];

	if ( $ninja_forms_fields[$field_type]['process_field'] ) {
		if ( isset ( $field_data['admin_label'] ) ) {
			$admin_label = $field_data['admin_label'];
		} else {
			$admin_label = '';
		}
		if ( isset ( $field_data['num_sort'] ) ) {
			$num_sort = $field_data['num_sort'];
		} else {
			$num_sort = '';
		}
		?>
		<div class="description description-wide">
		<?php
		ninja_forms_edit_field_el_output( $field_id, 'text', __( 'Admin Label', 'ninja-forms' ), 'admin_label', $admin_label, 'wide', '', 'widefat code', __( 'This is the label used when viewing/editing/exporting submissions.', 'ninja-forms' ) );
		?>
		</div>
		<?php
	}
}

add_action( 'nf_edit_field_advanced', 'nf_edit_field_admin_label', 10, 2 );

