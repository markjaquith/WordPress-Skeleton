<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_recaptcha() {

	$settings = get_option( "ninja_forms_settings" );
	$args = array(
		'name' => __( 'reCAPTCHA', 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'edit_function' => '',
		'display_function' => 'ninja_forms_field_recaptcha_display',
		'save_function' => '',
		'group' => 'standard_fields',
		'default_label' => __( 'Confirm that you are not a bot', 'ninja-forms' ),
		'edit_label' => true,
		'req' => true,
		'edit_label_pos' => true,
		'edit_req' => false,
		'edit_custom_class' => false,
		'edit_help' => false,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'action' => array(
				'show' => array(
					'name'        => __( 'Show This', 'ninja-forms' ),
					'js_function' => 'show',
					'output'      => 'hide',
				),
				'hide' => array(
					'name'        => __( 'Hide This', 'ninja-forms' ),
					'js_function' => 'hide',
					'output'      => 'hide',
				),
			),
		),
		'display_label' => true,
		'process_field' => false,
		'pre_process' => 'ninja_forms_field_recaptcha_pre_process',

	);
	// show recaptcha field in admin only if site and secret key exists.
	if ( !empty( $settings['recaptcha_site_key'] ) && !empty( $settings['recaptcha_secret_key'] ) ) {
		ninja_forms_register_field( '_recaptcha', $args );
	}
}

add_action( 'init', 'ninja_forms_register_field_recaptcha' );

function ninja_forms_field_recaptcha_display( $field_id, $data, $form_id = '' ) {
	$settings = get_option( "ninja_forms_settings" );
	$lang = $settings['recaptcha_lang'];
	$siteKey = $settings['recaptcha_site_key'];
	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	if ( !empty( $siteKey ) ) { ?>
		<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="hidden" class="<?php echo $field_class;?>" value="" rel="<?php echo $field_id;?>" />
		<div class="g-recaptcha" data-callback="nf_recaptcha_set_field_value" data-sitekey="<?php echo $siteKey; ?>"></div>
        <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>"> </script>
		<script type="text/javascript">
            function nf_recaptcha_set_field_value(inpval){
            	jQuery("#ninja_forms_field_<?php echo $field_id;?>").val(inpval)
            }
            </script>
		<?php
	}
}

function ninja_forms_field_recaptcha_pre_process( $field_id, $user_value  ) {
	global $ninja_forms_processing;

	// Set our captcha field id for later processing.
	$ninja_forms_processing->update_form_setting( 'recaptcha_field', $field_id );

	// Add our captcha processing.
	add_action( 'ninja_forms_process', 'nf_field_recaptcha_pre_process', -1 );
}

/**
 * Function that actually processes our recaptcha. Runs on a later priority than the field pre_process function
 * @since  2.9.27
 * @param  int  $form_id
 * @return void
 */
function nf_field_recaptcha_pre_process( $form_id ) {
	global $ninja_forms_processing;

	if ( empty( $_POST['g-recaptcha-response'] ) ) {
		$ninja_forms_processing->add_error( 'error_recaptcha', __( 'Please complete the captcha field' , 'ninja-forms' ) );
	}else {
		$settings = get_option( 'ninja_forms_settings' );
		$url = 'https://www.google.com/recaptcha/api/siteverify?secret='.$settings['recaptcha_secret_key'].'&response='.sanitize_text_field( $_POST['g-recaptcha-response'] );
		$resp = wp_remote_get( esc_url_raw( $url ) );

		if ( !is_wp_error( $resp ) ) {
			$body = wp_remote_retrieve_body( $resp );
			$response = json_decode( $body );
			if ( $response->success===false ) {
				if ( !empty( $response->{'error-codes'} ) && $response->{'error-codes'} != 'missing-input-response' ) {
					$error= __( 'Please make sure you have entered your Site & Secret keys correctly', 'ninja-forms' );
				}else {
					$error= __( 'Captcha mismatch. Please enter the correct value in captcha field', 'ninja-forms' );
				}
				$ninja_forms_processing->add_error( 'error_recaptcha', $error );
			}
		}
	}
}
