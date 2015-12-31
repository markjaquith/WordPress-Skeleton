<?php

/**
 * Make sure that our honeypot wasn't filled in.
 */
function nf_check_honeypot() {
	global $ninja_forms_processing;

	$hp_name = $ninja_forms_processing->get_extra_value( '_hp_name' );

	if ( $ninja_forms_processing->get_extra_value( $hp_name ) != '' ) {
		$ninja_forms_processing->add_error( 'honeypot', '' );
	}
}

add_action( 'ninja_forms_pre_process', 'nf_check_honeypot', 8 );