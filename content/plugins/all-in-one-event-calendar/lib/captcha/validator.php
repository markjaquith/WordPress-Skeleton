<?php

/**
 * Captcha validator class.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Captcha
 */
class Ai1ec_Captcha_Validator extends Ai1ec_Base {

	/**
	 * Performs a captcha check
	 *
	 * @return array
	 */
	public function validate() {
		$settings = $this->_registry->get( 'model.settings' );
		$response = array( 'success' => true );
		if ( empty( $_POST['recaptcha_challenge_field'] ) ||
			empty( $_POST['recaptcha_response_field'] ) ) {
			$response['message'] = __( 'There was an error reading the word verification data. Please try again.', AI1EC_PLUGIN_NAME );
			$response['success'] = false;
		}
	
		require_once( AI1EC_VENDOR_PATH . 'recaptcha/recaptchalib.php' );
		$resp = recaptcha_check_answer(
			$settings->get( 'recaptcha_private_key' ),
			$_SERVER['REMOTE_ADDR'],
			$_POST['recaptcha_challenge_field'],
			$_POST['recaptcha_response_field']
		);
	
		if ( ! $resp->is_valid ) {
			$response['message'] = __( 'Please try answering the word verification again.', AI1EC_PLUGIN_NAME );
			$response['success'] = false;
		}
		return $response;
	}
}