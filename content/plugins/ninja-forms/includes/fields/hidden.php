<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_hiddenbox(){
	$args = array(
		'name' => __( 'Hidden Field' , 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'edit_function' => 'ninja_forms_field_hidden_edit',
		'display_function' => 'ninja_forms_field_hidden_display',
		'save_function' => '',
		'group' => 'standard_fields',
		'edit_label' => true,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => true,
		'edit_help' => false,
		'edit_meta' => false,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
			'action' => array(
				'change_value' => array(
					'name'        => __( 'Change Value', 'ninja-forms' ),
					'js_function' => 'change_value',
					'output'      => 'text',
				),
			),
		),
		'display_label' => false,
		'sub_edit_function' => 'ninja_forms_field_hidden_edit_sub',
	);

	ninja_forms_register_field('_hidden', $args);
}

add_action('init', 'ninja_forms_register_field_hiddenbox');

function ninja_forms_field_hidden_edit($field_id, $data){
	$custom = '';
	$currency_symbol = isset( $plugin_settings['currency_symbol'] ) ? $plugin_settings['currency_symbol'] : "$";
	$date_format = isset( $plugin_settings['date_format'] ) ? $plugin_settings['date_format'] : "m/d/Y";
	$default_value = isset( $data['default_value'] ) ? $data['default_value'] : '';
	$default_value_type = isset( $data['default_value_type'] ) ? $data['default_value_type'] : '';
	$custom = '';

	if( $default_value == 'none' ){
		$default_value = '';
	}

	?>
	<p class="description description-thin">
		<label for="">
			<?php _e( 'Default Value' , 'ninja-forms'); ?><br />
			<select id="default_value_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>[default_value_type]" class="widefat ninja-forms-_text-default-value">
				<option value="" <?php if( $default_value == ''){ echo 'selected'; $custom = 'no';}?>><?php _e('None', 'ninja-forms'); ?></option>
				<option value="_user_id" <?php if($default_value == '_user_id'){ echo 'selected'; $custom = 'no';}?>><?php _e('User ID (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_firstname" <?php if($default_value == '_user_firstname'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Firstname (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_lastname" <?php if($default_value == '_user_lastname'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Lastname (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_display_name" <?php if($default_value == '_user_display_name'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Display Name (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_email" <?php if($default_value == '_user_email'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Email (If logged in)', 'ninja-forms'); ?></option>
				<option value="post_id" <?php if($default_value == 'post_id'){ echo 'selected'; $custom = 'no';}?>><?php _e('Post / Page ID (If available)', 'ninja-forms'); ?></option>
				<option value="post_title" <?php if($default_value == 'post_title'){ echo 'selected'; $custom = 'no';}?>><?php _e('Post / Page Title (If available)', 'ninja-forms'); ?></option>
				<option value="post_url" <?php if($default_value == 'post_url'){ echo 'selected'; $custom = 'no';}?>><?php _e('Post / Page URL (If available)', 'ninja-forms'); ?></option>
				<option value="today" <?php if($default_value == 'today'){ echo 'selected'; $custom = 'no';}?>><?php _e('Today\'s Date', 'ninja-forms'); ?></option>
				<option value="_custom" <?php if($custom != 'no'){ echo 'selected';}?>><?php _e('Custom', 'ninja-forms'); ?> -></option>
				<option value="querystring" <?php if($default_value_type == 'querystring'){ echo 'selected'; $custom = 'yes';}?>><?php _e('Querystring Variable', 'ninja-forms'); ?> -></option>
			</select>
		</label>
	</p>
	<p class="description description-thin">
		<label for="" id="default_value_label_<?php echo $field_id;?>" style="<?php if($custom == 'no'){ echo 'display:none;';}?>">
			<br />
			<input type="text" class="widefat code nf-default-value-text" name="ninja_forms_field_<?php echo $field_id;?>[default_value]" id="ninja_forms_field_<?php echo $field_id;?>_default_value" value="<?php echo $default_value;?>" data-field-id="<?php echo $field_id; ?>" />
			<span class="querystring-error" style="display:none;"><?php _e( 'This keyword is reserved by WordPress. Please try another.', 'ninja-forms' ); ?></span>
		</label>
	</p>

	<?php
	// Email Input Box ?
	if(isset($data['email'])){
		$email = $data['email'];
	}else{
		$email = '';
	}

	if(isset($data['send_email'])){
		$send_email = $data['send_email'];
	}else{
		$send_email = '';
	}
	?>
	<p class="description description-thin">
			<label for="ninja_forms_field_<?php echo $field_id;?>_email">
			<?php _e( 'Is this an email address?' , 'ninja-forms'); ?>
			<input type="hidden" value="0" name="ninja_forms_field_<?php echo $field_id;?>[email]">
			<input type="checkbox" value="1" name="ninja_forms_field_<?php echo $field_id;?>[email]" id="ninja_forms_field_<?php echo $field_id;?>_email" class="ninja-forms-hidden-email" <?php if($email == 1){ echo "checked";}?>>
		</label>
		<a href="#" class="tooltip">
		    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>images/question-ico.gif" title="">
		    <span>
		        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>images/callout.gif" />
		        <?php _e( 'If this box is checked, Ninja Forms will validate this input as an email address.', 'ninja-forms' );?>
		    </span>
		</a>
	</p>

	<p class="description description-wide">
			<label for="ninja_forms_field_<?php echo $field_id;?>_send_email" id="" style="">
			<?php _e( 'Send a copy of the form to this address?' , 'ninja-forms'); ?>
			<input type="hidden" value="0" name="ninja_forms_field_<?php echo $field_id;?>[send_email]">
			<input type="checkbox" value="1" name="ninja_forms_field_<?php echo $field_id;?>[send_email]" id="ninja_forms_field_<?php echo $field_id;?>_send_email" class="ninja-forms-hidden-send-email" <?php if($send_email == 1){ echo "checked";}?>>
			</label>
			<a href="#" class="tooltip">
			    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>images/question-ico.gif" title="">
			    <span>
			        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
			        <?php _e( 'If this box is checked, Ninja Forms will send a copy of this form (and any messages attached) to this address.', 'ninja-forms' ); ?>
			    </span>
			</a>

	</p>
	<?php
}

function ninja_forms_field_hidden_display( $field_id, $data, $form_id = '' ){
	global $current_user;

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	?>
	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="hidden" class="<?php echo $field_class;?>" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>" />
	<?php

}

function ninja_forms_field_hidden_edit_sub( $field_id, $data ) {
	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	if(isset($data['label'])){
		$label = $data['label'];
	}else{
		$label = '';
	}
	?>
	<label>
		<?php echo $label; ?>
	</label>
	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="text" class="<?php echo $field_class;?>" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>" />
	<?php
}
