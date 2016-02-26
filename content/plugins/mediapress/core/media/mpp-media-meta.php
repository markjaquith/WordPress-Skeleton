<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Meta data for gallery/medias/users
 */

function mpp_add_media_meta( $media_id, $meta_key, $meta_value, $unique = false ) {

	return add_post_meta( $media_id, $meta_key, $meta_value, $unique );
	
}

function mpp_get_media_meta( $media_id, $meta_key = '', $single = false ) {
	
	if ( empty( $meta_key ) ) {
		$single = false;
	}
	
	return get_post_meta( $media_id, $meta_key, $single );
}

function mpp_update_media_meta( $media_id, $meta_key, $meta_value, $prev_value = '' ) {
	
	return update_post_meta( $media_id, $meta_key, $meta_value, $prev_value );
	
}

function mpp_delete_media_meta( $media_id, $meta_key = '', $meta_value = '' ) {
	
	return delete_post_meta( $media_id, $meta_key, $meta_value );
	
}
