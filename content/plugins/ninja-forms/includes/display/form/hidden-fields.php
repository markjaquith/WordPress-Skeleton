<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_display_hidden_fields');
function ninja_forms_register_display_hidden_fields(){
	add_action('ninja_forms_display_after_open_form_tag', 'ninja_forms_display_hidden_fields');
}

function ninja_forms_display_hidden_fields($form_id){
	global $ninja_forms_processing;
	?>
	<input type="hidden" name="_ninja_forms_display_submit" value="1">
	<input type="hidden" name="_form_id"  id="_form_id" value="<?php echo $form_id;?>">
	<?php
	if( is_object( $ninja_forms_processing) AND $ninja_forms_processing->get_all_errors()){
		?>
		<input type="hidden" id="ninja_forms_processing_error" value="1">
		<?php
	}
}