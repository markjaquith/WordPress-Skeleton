<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_hr(){
	$args = array(
		'name' => __( 'hr', 'ninja-forms' ),
		'sidebar' => 'layout_fields',
		'edit_function' => '',
		'display_function' => 'ninja_forms_field_hr_display',
		'group' => 'layout_elements',
		'display_label' => false,
		'display_wrap' => false,
		'edit_label' => false,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => true,
		'edit_help' => false,
		'edit_meta' => false,
		'edit_conditional' => true,
		'process_field' => false,
	);

	ninja_forms_register_field('_hr', $args);
}

add_action('init', 'ninja_forms_register_field_hr');

function ninja_forms_field_hr_display( $field_id, $data, $form_id = '' ){
	if( isset( $data['display_style'] ) ){
		$display_style = $data['display_style'];
	}else{
		$display_style = '';
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	?>
	<hr class="<?php echo $field_class;?>" style="<?php echo $display_style;?>" id="ninja_forms_field_<?php echo $field_id;?>_div_wrap" rel="<?php echo $field_id;?>" />
	<?php

}