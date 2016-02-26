<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_textarea(){
	$args = array(
		'name' => __( 'Textarea', 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'edit_function' => '',
		'edit_options' => array(
			array(
				'type' => 'textarea',
				'name' => 'default_value',
				'label' => __( 'Default Value', 'ninja-forms' ),
				'width' => 'wide',
				'class' => 'widefat',
			),
			array(
				'type' => 'checkbox',
				'name' => 'textarea_rte',
				'label' => __( 'Show Rich Text Editor', 'ninja-forms' ),
			),
			array(
				'type' => 'checkbox',
				'name' => 'textarea_media',
				'label' => __( 'Show Media Upload Button', 'ninja-forms' ),
			),
			array(
				'type' => 'checkbox',
				'name' => 'disable_rte_mobile',
				'label' => __( 'Disable Rich Text Editor on Mobile', 'ninja-forms' ),
			),
		),
		'display_function' => 'ninja_forms_field_textarea_display',
		'save_function' => '',
		'group' => 'standard_fields',
		'edit_label' => true,
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_desc' => true,
		'edit_meta' => false,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'textarea',
			),
		),
		'edit_sub_value' => 'nf_field_textarea_edit_sub_value',
		'pre_process'	=> 'nf_field_textarea_pre_process',
	);

	ninja_forms_register_field('_textarea', $args);
}

add_action('init', 'ninja_forms_register_field_textarea');

function ninja_forms_field_textarea_display( $field_id, $data, $form_id = '' ){
	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	$default_value = htmlspecialchars_decode( $default_value );

	if(isset($data['textarea_rte'])){
		$textarea_rte = $data['textarea_rte'];
	}else{
		$textarea_rte = 0;
	}

	if( isset ( $data['textarea_media'] ) AND $data['textarea_media'] == 1 ){
		$textarea_media = true;
	}else{
		$textarea_media = false;
	}

	if ( isset( $data['disable_rte_mobile'] ) AND 1 == $data['disable_rte_mobile'] AND wp_is_mobile() ) {
		$textarea_rte = 0;
	}

	if( isset( $data['input_limit'] ) ){
		$input_limit = $data['input_limit'];
	}else{
		$input_limit = '';
	}

	if( isset( $data['input_limit_type'] ) ){
		$input_limit_type = $data['input_limit_type'];
	}else{
		$input_limit_type = '';
	}

	if( isset( $data['input_limit_msg'] ) ){
		$input_limit_msg = $data['input_limit_msg'];
	}else{
		$input_limit_msg = '';
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );

	$default_value = filter_var( $default_value, FILTER_SANITIZE_STRING );
	$default_value = filter_var( $default_value, FILTER_SANITIZE_SPECIAL_CHARS );

	if($textarea_rte == 1){
		$settings = array( 'media_buttons' => $textarea_media );
		$args = apply_filters( 'ninja_forms_textarea_rte', $settings );
		wp_editor( $default_value, 'ninja_forms_field_'.$field_id, $args );
	}else{
		?>
		<textarea name="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $field_class;?>" rel="<?php echo $field_id;?>" data-input-limit="<?php echo $input_limit;?>" data-input-limit-type="<?php echo $input_limit_type;?>" data-input-limit-msg="<?php echo $input_limit_msg;?>"><?php echo $default_value;?></textarea>
		<?php
	}
}

/**
 * Edit submission value output function
 *
 * @since 2.7
 * @return void
 */
function nf_field_textarea_edit_sub_value( $field_id, $user_value ) {
	?>
	<textarea name="fields[<?php echo $field_id; ?>]"><?php echo $user_value; ?></textarea>
	<?php
}

/**
 * Make sure we strip nested script tags from our values
 *
 * @since  2.9.19
 * @return  void
 */
function nf_field_textarea_pre_process( $field_id, $user_value ) {
	global $ninja_forms_processing;

	while ( false !== strpos ( $user_value, '&lt;script' )
		|| false !== strpos ( $user_value, '<script' ) 
		|| false !== strpos ( $user_value, '&lt;/script' ) 
		|| false !== strpos ( $user_value, '</script' ) 
		|| false !== strpos ( $user_value, '<textarea' )
		|| false !== strpos ( $user_value, '&lt;textarea' )
		|| false !== strpos ( $user_value, '</textarea' )
		|| false !== strpos ( $user_value, '&lt;/textarea' )
		) {
		
		$user_value = str_replace( '&lt;script', '', $user_value );
		$user_value = str_replace( '<script', '', $user_value );
		$user_value = str_replace( '&lt;/script', '', $user_value );
		$user_value = str_replace( '</script', '', $user_value );

		$user_value = str_replace( '&lt;textarea', '', $user_value );
		$user_value = str_replace( '<textarea', '', $user_value );
		$user_value = str_replace( '&lt;/textarea', '', $user_value );
		$user_value = str_replace( '</textarea', '', $user_value );
	}

	$ninja_forms_processing->update_field_value( $field_id, $user_value );
}