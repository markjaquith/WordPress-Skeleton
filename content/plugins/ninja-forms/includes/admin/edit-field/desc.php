<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'init', 'ninja_forms_register_edit_field_desc' );
function ninja_forms_register_edit_field_desc() {
	add_action( 'ninja_forms_edit_field_after_registered', 'ninja_forms_edit_field_desc', 10 , 2);
}

function ninja_forms_edit_field_desc( $field_id, $field_data ) {
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id( $field_id );
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];
	if ( isset ( $reg_field['edit_desc'] ) ) {
		$edit_desc = $reg_field['edit_desc'];
	} else {
		$edit_desc = true;
	}

	if ( $edit_desc ) {
		if ( isset( $field_data['desc_text'] ) ) {
			$desc_text = $field_data['desc_text'];
		} else {
			$desc_text = '';
		}

		if ( isset( $field_data['show_desc'] ) ) {
			$show_desc = $field_data['show_desc'];
		} else {
			$show_desc = '';
		}

		if ( $show_desc == 1 ) {
			$display_span = '';
		} else {
			$display_span = ' style="display:none;"';
		}

		if ( !isset ( $desc_pos_options ) or $desc_pos_options == '' ) {

			$options = array();
			$options[] = array( 'name' => __( 'None', 'ninja-forms' ), 'value' => 'none' );
			$options[] = array( 'name' => __( 'Before Everything', 'ninja-forms' ), 'value' => 'before_everything' );
			if ( '_submit' != $field_type ) {
				$options[] = array( 'name' => __( 'Before Label', 'ninja-forms' ), 'value' => 'before_label' );
				$options[] = array( 'name' => __( 'After Label', 'ninja-forms' ), 'value' => 'after_label' );
			}
			$options[] = array( 'name' => __( 'After Everything', 'ninja-forms' ), 'value' => 'after_everything' );

		} else {
			$options = $desc_pos_options;
		}

		$desc_desc = sprintf( __( 'If "desc text" is enabled, there will be a question mark %s placed next to the input field. Hovering over this question mark will show the desc text.', 'ninja-forms' ), '<img src="'.NINJA_FORMS_URL.'images/question-ico.gif">' );
		ninja_forms_edit_field_el_output( $field_id, 'checkbox', __( 'Add Description', 'ninja-forms' ), 'show_desc', $show_desc, 'wide', '', 'ninja-forms-show-desc' );
?>
		<span id="ninja_forms_field_<?php echo $field_id;?>_desc_span" <?php echo $display_span;?>>
			<?php
		if ( isset( $field_data['desc_pos'] ) ) {
			$desc_pos = $field_data['desc_pos'];
		} else {
			$desc_pos = '';
		}
		ninja_forms_edit_field_el_output( $field_id, 'select', __( 'Description Position', 'ninja-forms' ), 'desc_pos', $desc_pos, 'wide', $options, 'wide' );
		ninja_forms_edit_field_el_output( $field_id, 'rte', __( 'Description Content', 'ninja-forms' ), 'desc_text', $desc_text, 'wide', '', 'widefat' );

?>
		</span>
		<?php
	}
}
