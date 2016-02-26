<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds our not logged-in message if the user is not logged-in and the form requires the user to be logged-in.
 *
 * @since 2.9
 * @return void
 */
function nf_not_logged_in_msg( $form_id ) {
	$not_logged_in = Ninja_Forms()->form( $form_id )->get_setting( 'logged_in' );
	if ( ! is_user_logged_in() && 1 == $not_logged_in ) {
		echo Ninja_Forms()->form( $form_id )->get_setting( 'not_logged_in_msg' );
	}
}

add_action( 'ninja_forms_display_user_not_logged_in', 'nf_not_logged_in_msg' );