<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helpers for shortcodes
 */
function mpp_shortcode_get_data( $type, $key ) {
	
	$data = mediapress()->get_data( 'shortcode' ); //,  = $shortcode_column;

	if ( isset( $data[$type][$key] ) ) {
		return $data[$type][$key];
	}

	return false;
}

function mpp_shortcode_save_data( $type, $key, $value ) {

	$data = mediapress()->get_data( 'shortcode' ); //,  = $shortcode_column;

	if ( ! $data ) {
		$data = array();
	}

	$data[$type][$key] = $value;

	mediapress()->add_data( 'shortcode', $data );
}

function mpp_shortcode_reset_data( $type, $key = null ) {

	$data = mediapress()->get_data( 'shortcode' ); //,  = $shortcode_column;

	if ( ! $key ) {
		unset( $data[$type] );
	} else {
		unset( $data[$type][$key] );
	}

	mediapress()->add_data( 'shortcode', $data ); //save the updated data	
}

/** Gallery */
function mpp_shortcode_get_gallery_data( $key ) {

	return mpp_shortcode_get_data( 'gallery', $key );
}

function mpp_shortcode_save_gallery_data( $key, $value ) {

	return mpp_shortcode_save_data( 'gallery', $key, $value );
}

function mpp_shortcode_reset_gallery_data( $key = null ) {

	return mpp_shortcode_reset_data( 'gallery', $key );
}

/** Media * */
function mpp_shortcode_get_media_data( $key ) {

	return mpp_shortcode_get_data( 'media', $key );
}

function mpp_shortcode_save_media_data( $key, $value ) {

	return mpp_shortcode_save_data( 'media', $key, $value );
}

function mpp_shortcode_reset_media_data( $key = null ) {

	return mpp_shortcode_reset_data( 'media', $key );
}
