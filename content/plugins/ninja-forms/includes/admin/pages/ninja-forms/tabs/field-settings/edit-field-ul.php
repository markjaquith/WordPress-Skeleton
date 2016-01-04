<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'ninja_forms_edit_field_ul', 'ninja_forms_edit_field_output_ul' );
function ninja_forms_edit_field_output_ul( $form_id ){
	$fields = ninja_forms_get_fields_by_form_id( $form_id );
	?>
	<div id="ninja-forms-viewport">
		<input class="button-primary menu-save nf-save-admin-fields" id="ninja_forms_save_data_top" type="button" value="<?php _e('Save', 'ninja-forms'); ?>" />
		<a href="#" class="button-secondary nf-save-spinner" style="display:none;" disabled><span class="spinner nf-save-spinner" style="float:left;"></span></a>
		<ul class="menu ninja-forms-field-list" id="ninja_forms_field_list">
	  		<?php
				if( is_array( $fields ) AND !empty( $fields ) ){
					foreach( $fields as $field ){
						ninja_forms_edit_field( $field['id'] );
					}
				}
			?>
		</ul>

		<input class="button-primary menu-save nf-save-admin-fields" id="ninja_forms_save_data_bot" type="button" value="<?php _e('Save', 'ninja-forms'); ?>" />
		<a href="#" class="button-secondary nf-save-spinner" style="display:none;" disabled><span class="spinner nf-save-spinner" style="float:left;"></span></a>
	</div>
		<?php

}