<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_profile_pass(){
	$args = array(
		'name' => __( 'Password', 'ninja-forms' ),
		'display_function' => 'ninja_forms_field_profile_pass_display',
		'group' => 'standard_fields',
		'edit_label' => true,
		'edit_label_pos' => false,
		'default_label_pos' => 'left',
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		//'limit' => 1,
		'save_sub' => false,
		'pre_process' => 'ninja_forms_field_profile_pass_pre_process',
		'edit_options' => array(
			array(
				'name' => 'reg_password',
				'type' => 'checkbox',
				'label' => __( 'Use this as a registration password field', 'ninja-forms' ),
				'default' => 1,
				'desc' => '<br>'.__( 'If this box is checked, both password and re-password textboxes will be output.', 'ninja-forms' ),
				'width' => 'wide',
			),
			array(
				'name' => 're_pass',
				'type' => 'text',
				'label' => __( 'Re-enter Password Label', 'ninja-forms' ),
				'class' => 'widefat reg-password',
				'default' => __( 'Re-enter Password', 'ninja-forms' ),
				'width' => 'wide',
			),
			array(
				'name' => 'adv_pass',
				'type' => 'checkbox',
				'label' => __( 'Show Password Strength Indicator', 'ninja-forms' ),
				'default' => 1,
				'class' => 'reg-password',
			),
		),
	);

	if( function_exists( 'ninja_forms_register_field' ) ){
		ninja_forms_register_field('_profile_pass', $args);
	}
}

add_action( 'init', 'ninja_forms_register_field_profile_pass' );

function ninja_forms_field_profile_pass_display( $field_id, $data, $form_id = '' ){
	global $current_user;
	$field_class = ninja_forms_get_field_class( $field_id, $form_id );

	if( isset( $data['default_value'] ) ){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	if( isset( $data['adv_pass'] ) ){
		$adv_pass = $data['adv_pass'];
	}else{
		$adv_pass = 0;
	}

	$default_value_re = '';

	if( isset( $data['label_pos'] ) ){
		$label_pos = $data['label_pos'];
	}else{
		$label_pos = "left";
	}

	if( isset( $data['label'] ) ){
		$label = $data['label'];
	}else{
		$label = '';
	}

	if( isset( $data['re_pass'] ) ){
		$re_pass = $data['re_pass'];
	}else{
		$re_pass = '';
	}

	if( $label_pos == 'inside' ){
		$default_value = $label;
		$default_value_re = $re_pass;
	}

	if( isset( $data['reg_password'] ) ){
		$reg_password = $data['reg_password'];
	}else{
		$reg_password = 1;
	}

	if( $reg_password == 1 ){
		?>
		<input id="pass1_<?php echo $field_id;?>" title="" name="ninja_forms_field_<?php echo $field_id;?>" type="password" class="<?php echo $field_class;?> pass1" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>" />
		</div>
		<div class="ninja-forms-pass2">
		<?php
		if( $label_pos == 'left' OR $label_pos == 'above' ){
			?>
			<label><?php echo $re_pass;?></label>
			<?php
		}
		?>
		<input id="pass2_<?php echo $field_id;?>" title="" name="_pass_<?php echo $field_id;?>" type="password" class="<?php echo $field_class;?> pass2" value="<?php echo $default_value_re;?>" />
		<?php
		if( $label_pos == 'right' OR $label_pos == 'below' ){
			?>
			<label><?php echo $re_pass;?></label>
			<?php
		}
		echo '</div>';
		if( $adv_pass == 1 ){
			$class = apply_filters( 'ninja_forms_display_field_desc_class', 'description indicator-hint', $field_id );
			?>
			<div id="pass-strength-result"><?php _e( 'Strength indicator', 'ninja-forms' ); ?></div>
				<p class="<?php echo $class; ?>"><?php _e( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).', 'ninja-forms' ); ?></p>
			<?php
		}

	}else{
		?>
		<input id="ninja_forms_field_<?php echo $field_id;?>" title="" name="ninja_forms_field_<?php echo $field_id;?>" type="password" class="<?php echo $field_class;?>" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>" /></div>
		<?php
	}
}

function ninja_forms_field_profile_pass_pre_process( $field_id, $user_value ){
	global $ninja_forms_processing;

	$field_row = $ninja_forms_processing->get_field_settings( $field_id );
	$field_data = $field_row['data'];
	if( isset( $field_data['reg_password'] ) AND $field_data['reg_password'] == 1 ){
		if( $user_value != $ninja_forms_processing->get_extra_value( '_pass_'.$field_id ) ){
			$ninja_forms_processing->add_error( 'mismatch-'.$field_id, __( 'Passwords do not match', 'ninja-forms' ), $field_id );
		}else{
			$ninja_forms_processing->update_extra_value( '_password', $user_value );
		}
	}
}

function ninja_forms_field_profile_add_open_wrapper( $field_id, $data ) {
	$field_row = ninja_forms_get_field_by_id( $field_id );
	if ( '_profile_pass' == $field_row['type'] ) {
		echo '<div class="ninja-forms-pass1">';
	}
}
add_action( 'ninja_forms_display_after_opening_field_wrap', 'ninja_forms_field_profile_add_open_wrapper', 10, 2 );

function ninja_forms_field_profile_add_close_wrapper( $field_id, $data ) {
	$field_row = ninja_forms_get_field_by_id( $field_id );
	if ( '_profile_pass' == $field_row['type'] ) {
		//echo '</div>';
	}
}
add_action( 'ninja_forms_display_before_closing_field_wrap', 'ninja_forms_field_profile_add_close_wrapper', 10, 2 );