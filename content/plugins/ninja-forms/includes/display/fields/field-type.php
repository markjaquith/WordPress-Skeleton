<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Outputs a hidden HTML form element with the type of field this is.
**/
add_action('init', 'ninja_forms_register_display_field_type');
function ninja_forms_register_display_field_type(){
	add_action('ninja_forms_display_after_opening_field_wrap', 'ninja_forms_display_field_type', 10, 2);
}

function ninja_forms_display_field_type( $field_id, $data ){
	global $ninja_forms_loading, $ninja_forms_processing;
	
    $field = ninja_forms_get_field_by_id( $field_id );
    $form_id = $field['form_id'];

	if ( isset ( $ninja_forms_loading ) && $ninja_forms_loading->get_form_ID() == $form_id ) {
		$field_row = $ninja_forms_loading->get_field_settings( $field_id );
	} else if ( isset ( $ninja_forms_processing ) && $ninja_forms_processing->get_form_ID() == $form_id ) {
		$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	}

	$field_type = $field_row['type'];
	if ( strpos( $field_type, '_' ) === 0 ) {
		$field_type = substr( $field_type, 1 );
	}
	$field_type = apply_filters( 'ninja_forms_display_field_type', $field_type, $field_id );
?>
	<input type="hidden" id="ninja_forms_field_<?php echo $field_id;?>_type" value="<?php echo $field_type;?>">
<?php
}
