<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_edit_field_remove_button( $field_id ){
	global $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];
	$show_remove = $reg_field['show_remove'];

	if ( $show_remove ) {
		?>
		<div class="menu-item-actions description-wide submitbox">
			<a class="submitdelete deletion nf-remove-field" id="ninja_forms_field_<?php echo $field_id;?>_remove" data-field="<?php echo $field_id; ?>" href="#"><?php _e('Remove', 'ninja-forms'); ?></a>
		</div>
		<?php
	}
	
}

// add_action( 'ninja_forms_edit_field_after_registered', 'ninja_forms_edit_field_remove_button', 99999 );