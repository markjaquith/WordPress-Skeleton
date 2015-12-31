<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Insert a nonce field into our form
 *
 * @since 2.6.1
 * @return void
 */

function nf_form_nonce( $form_id ) {
	wp_nonce_field( 'nf_form_' . $form_id );
}

add_action( 'ninja_forms_display_after_open_form_tag', 'nf_form_nonce' );