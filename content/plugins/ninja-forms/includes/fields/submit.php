<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_submit(){
	$args = array(
		'name' => __( 'Submit', 'ninja-forms' ),
		'display_function' => 'ninja_forms_field_submit_display',
		'group' => 'standard_fields',
		'edit_label' => true,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'display_label' => false,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'process_field' => false,
		'limit' => 1,
	);

	ninja_forms_register_field('_submit', $args);
}

add_action('init', 'ninja_forms_register_field_submit');

function ninja_forms_field_submit_display( $field_id, $data, $form_id = '' ){
	global $ninja_forms_loading, $ninja_forms_processing;

	if ( isset ( $ninja_forms_loading ) ) {
		$form_id = $ninja_forms_loading->get_form_ID();
	} else {
		$form_id = $ninja_forms_processing->get_form_ID();
	}

	if(isset($data['show_field'])){
		$show_field = $data['show_field'];
	}else{
		$show_field = true;
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	if(isset($data['label']) AND $data['label'] != ''){
		$label = $data['label'];
	}else{
		$label = 'Submit';
	}
	$plugin_settings = nf_get_settings();
	if ( isset ( $plugin_settings['process_label'] ) ) {
		$processing_msg = $plugin_settings['process_label'];
	}
	?>
	<div id="nf_submit_<?php echo $form_id; ?>">
		<input type="submit" name="_ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $field_class;?>" id="ninja_forms_field_<?php echo $field_id;?>" value="<?php echo $label;?>" rel="<?php echo $field_id;?>" >
	</div>
	<div id="nf_processing_<?php echo $form_id; ?>" style="display:none;">
		<input type="submit" name="_ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $field_class; ?>" id="ninja_forms_field_<?php echo $field_id;?>" value="<?php echo $processing_msg; ?>" rel="<?php echo $field_id;?>" disabled>
	</div>
	<?php

}
