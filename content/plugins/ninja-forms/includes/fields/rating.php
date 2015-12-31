<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_rating(){
	$args = array(
		'name' => __( 'Star Rating', 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'display_function' => 'ninja_forms_field_rating_display',
		'pre_process' => 'ninja_forms_field_rating_pre_process',
		'group' => 'standard_fields',
		'edit_options' => array(
			array(
				'name' => 'rating_stars',
				'type' => 'text',
				'label' => __( 'Number of stars', 'ninja-forms' ),
				'width' => 'thin',
				'default' => 5,
			),
		),
		'edit_label' => true,
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => true,
		'edit_conditional' => true,
	);

	ninja_forms_register_field('_rating', $args);
}

add_action('init', 'ninja_forms_register_field_rating');

function ninja_forms_field_rating_display( $field_id, $data, $form_id = '' ){
	if( isset( $data['default_value'] ) ){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	if( isset( $data['rating_stars'] ) ){
		$rating_stars = $data['rating_stars'];
	}else{
		$rating_stars = 5;
	}

	$x = 1;
	while( $x <= $rating_stars ){
		?>
		<input name="ninja_forms_field_<?php echo $field_id;?>" type="radio" class="ninja-forms-star" value="<?php echo $x;?>" <?php checked( $default_value, $x );?>/>
		<?php
		$x++;
	}
}

function ninja_forms_field_rating_pre_process( $field_id, $user_value ) {
	global $ninja_forms_processing;
	
	if ( $user_value == false ) {
		$ninja_forms_processing->update_field_value( $field_id, '' );
	}
}