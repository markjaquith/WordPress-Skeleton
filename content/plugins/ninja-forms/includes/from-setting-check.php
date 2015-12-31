<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 *
 * Function that checks to see if we've properly removed the old "From Email" setting and replaced it with "From Name" and "From Email."
 *
 * @since 2.3
 * @return void
 */

function ninja_forms_check_email_from_name() {

	$plugin_settings = nf_get_settings();
	// Check to see if we've already fixed the setting.
	if ( !isset ( $plugin_settings['fix_form_email_from'] ) or $plugin_settings['fix_form_email_from'] != 1 ) {
		// Get our forms.
		$forms = ninja_forms_get_all_forms();
		if ( is_array ( $forms ) ) {
			foreach( $forms as $form ) {
				// Check to see if we've already added the "from_email_name."
				if ( !isset ( $form['data']['email_from_name'] ) and isset ( $form['data']['email_from'] ) ) {
					// This field doesn't have an "email_from_name" saved, so we'll run it through the adjustment function.
					$email_from = ninja_forms_split_email_from( $form['data']['email_from'] );
					$form['data']['email_from'] = $email_from['email_from'];
					$form['data']['email_from_name'] = $email_from['email_from_name'];
					$args = array(
						'update_array' => array(
							'data' => serialize( $form['data'] ),
							),
						'where' => array(
							'id' => $form['id'],
							),
					);
					ninja_forms_update_form($args);
				}
			}
		}
		$plugin_settings['fix_form_email_from'] = 1;
		update_option( 'ninja_forms_settings', $plugin_settings );
	}
}

add_action( 'init', 'ninja_forms_check_email_from_name' );

/*
 *
 * Function that looks at our "Email From" setting and breaks it up into "Name" and "Email."
 *
 * @since 2.3
 * @return $tmp_array array
 */

function ninja_forms_split_email_from( $email_from ) {
	$pat = '/\<([^\"]*?)\>/'; // text between quotes excluding quotes
	$value = $email_from;
	$tmp_array = array();
	if( preg_match( $pat, $value, $matches ) ) {
		$arr = explode("<", $email_from, 2);
		$tmp_array['email_from_name'] = $arr[0];
		$tmp_array['email_from'] = $matches[1];
	} else {
		$tmp_array['email_from_name'] = '';
		$tmp_array['email_from'] = $email_from;
	}
	return $tmp_array;
}