<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'mpp-create-gallery', 'mpp_shortcode_create_gallery' );

function mpp_shortcode_create_gallery( $atts = array(), $content = null ) {

	$defaults = array();
	//do not show it to the non logged user
	if ( ! is_user_logged_in() ) {
		return $content;
	}
	
	ob_start();

	mpp_get_template( 'shortcodes/create-gallery.php' );

	$content = ob_get_clean();

	return $content;
}
