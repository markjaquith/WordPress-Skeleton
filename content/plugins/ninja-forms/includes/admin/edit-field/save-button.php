<?php if ( ! defined( 'ABSPATH' ) ) exit;
//add_action( 'ninja_forms_edit_field_before_ul', 'ninja_forms_edit_field_save_button' );
function ninja_forms_edit_field_save_button( $form_id ){
	?>
	<input class="button-primary menu-save ninja-forms-save-data" id="ninja_forms_save_data_top" type="submit" value="<?php _e('Save Field Settings', 'ninja-forms'); ?>" />
	<?php
}