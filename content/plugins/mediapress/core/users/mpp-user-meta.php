<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get User meta
 * 
 * 
 * @param type $user_id
 * @param type $meta_key
 * @param type $single
 * @return type
 */
function mpp_get_user_meta( $user_id, $meta_key, $single = false ) {

	if ( function_exists( 'bp_get_user_meta' ) ) {
		$callback = 'bp_get_user_meta';
	} else {
		$callback = 'get_user_meta';
	}
	
	return $callback( $user_id, $meta_key, $single );
}

/**
 * Update User meta
 * 
 * @param type $user_id
 * @param type $meta_key
 * @param type $meta_value
 * @return type
 */
function mpp_update_user_meta( $user_id, $meta_key, $meta_value = '' ) {

	if ( function_exists( 'bp_update_user_meta' ) ) {
		$callback = 'bp_update_user_meta';
	} else {
		$callback = 'update_user_meta';
	}
	
	return $callback( $user_id, $meta_key, $meta_value );
}

/**
 * Deletes a usermeta
 * An abstraction layer for deleting user meta
 * 
 * @param type $user_id
 * @param type $meta_key
 * @param type $meta_value
 * @return type
 */
function mpp_delete_user_meta( $user_id, $meta_key, $meta_value = '' ) {

	if ( function_exists( 'bp_delete_user_meta' ) ) {
		$callback = 'bp_delete_user_meta';
	} else {
		$callback = 'delete_user_meta';
	}
	
	return $callback( $user_id, $meta_key, $meta_value );
}
