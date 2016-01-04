<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_number() {
	$args = array(
		'name' => __( 'Number', 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'edit_function' => 'ninja_forms_field_number_edit',
		'edit_options' => array(
			array(
				'type' => 'text',
				'class' => 'medium-text',
				'name' => 'number_min',
				'label' => __( 'Minimum Value', 'ninja-forms' ),
			),
			array(
				'type' => 'text',
				'class' => 'medium-text',
				'name' => 'number_max',
				'label' => __( 'Maximum Value', 'ninja-forms' ),
			),
			array(
				'type' => 'text',
				'class' => 'medium-text',
				'name' => 'number_step',
				'label' => __( 'Step (amount to increment by)', 'ninja-forms' ),
			),
		),
		'display_function' => 'ninja_forms_field_number_display',
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
	);

	ninja_forms_register_field( '_number', $args );
}

add_action( 'init', 'ninja_forms_register_field_number' );

function ninja_forms_field_number_edit( $field_id, $data ) {
	
	$plugin_settings = nf_get_settings();

	$custom = '';
	// Default Value
	if( isset( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
	} else {
		$default_value = '';
	}
	if( $default_value == 'none' ) {
		$default_value = '';
	}

	?>
	<div class="description description-thin">
		<span class="field-option">
		<label for="">
			<?php _e( 'Default Value' , 'ninja-forms' ); ?>
		</label><br />
			<select id="default_value_<?php echo $field_id;?>" name="" class="widefat ninja-forms-_text-default-value">
				<option value="" <?php if( $default_value == '' ) { echo 'selected'; $custom = 'no'; } ?>><?php _e( 'None', 'ninja-forms' ); ?></option>
				<option value="_user_id" <?php if( $default_value == '_user_id') { echo 'selected'; $custom = 'no'; } ?>><?php _e( 'User ID (If logged in)', 'ninja-forms' ); ?></option>
				<option value="post_id" <?php if( $default_value == 'post_id') { echo 'selected'; $custom = 'no'; } ?>><?php _e( 'Post / Page ID (If available)', 'ninja-forms' ); ?></option>
				<option value="_custom" <?php if( $custom != 'no') { echo 'selected'; } ?>><?php _e( 'Custom', 'ninja-forms' ); ?> -></option>
			</select>
		</span>
	</div>
	<div class="description description-thin">

		<label for="" id="default_value_label_<?php echo $field_id;?>" style="<?php if($custom == 'no') { echo 'display:none;'; } ?>">
			<span class="field-option">
			<?php _e( 'Default Value' , 'ninja-forms' ); ?><br />
			<input type="number" class="widefat code" name="ninja_forms_field_<?php echo $field_id;?>[default_value]" id="ninja_forms_field_<?php echo $field_id;?>_default_value" value="<?php echo $default_value;?>" />
			</span>
		</label>

	</div>
	<?php
}

function ninja_forms_field_number_display( $field_id, $data, $form_id = '' ) {
	if ( isset( $data['default_value'] ) ) {
		$default_value = $data['default_value'];
	} elseif( isset( $data['number_min'] ) ) {
		$default_value = $data['number_min'];
	}

	if ( isset( $data['number_min'] ) ) {
		$min = ' min="' . esc_attr( $data['number_min'] ) . '"';
	} else {
		$min = '';
	}

	if ( isset( $data['number_max'] ) ) {
		$max = ' max="' . esc_attr( $data['number_max'] ) . '"';
	} else {
		$max = '';
	}

	if ( isset( $data['number_step'] ) ) {
		$step = ' step="' . esc_attr( $data['number_step'] ) . '"';
	} else {
		$step = '';
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );

?>
		<input type="number"<?php echo $min . $max . $step; ?> name="ninja_forms_field_<?php echo esc_attr( $field_id ); ?>" id="ninja_forms_field_<?php echo esc_attr( $field_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>" rel="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $default_value ); ?>"/>
<?php
}
