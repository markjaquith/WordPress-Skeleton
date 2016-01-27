<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Meta data for gallery
 */

/**
 * Add gallery meta data
 *
 * @param int    $gallery_id Gallery id.
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param bool   $unique     Optional. Whether the same key should not be added.
 *                           Default false.
 * @return int|false Meta ID on success, false on failure.
 */
function mpp_add_gallery_meta( $gallery_id, $meta_key, $meta_value, $unique = false ) {

	return add_post_meta( $gallery_id, $meta_key, $meta_value, $unique );
}

/**
 * Retrieve gallery meta 
 *
 * @param int    $gallery_id Gallery id.
 * @param string $meta_key     Optional. The meta key to retrieve. By default, returns
 *                        data for all keys. Default empty.
 * @param bool   $single  Optional. Whether to return a single value. Default false.
 * @return mixed Will be an array if $single is false. Will be value of meta data
 *               field if $single is true.
 */
function mpp_get_gallery_meta( $gallery_id, $meta_key = '', $single = false ) {

	if ( empty( $meta_key ) ) {
		$single = false;
	}

	return get_post_meta( $gallery_id, $meta_key, $single );
}

/**
 * Update gallery meta
 * 
 * @param int    $gallery_id    Gallery id.
 * @param string $meta_key   Metadata key.
 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed  $prev_value Optional. Previous value to check before removing.
 *                           Default empty.
 * @return int|bool Meta ID if the key didn't exist, true on successful update,
 *                  false on failure.
 */
function mpp_update_gallery_meta( $gallery_id, $meta_key, $meta_value, $prev_value = '' ) {

	return update_post_meta( $gallery_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Remove gallery meta
 *
 * @param int    $gallery_id Gallery id
 * @param string $meta_key   Metadata name.
 * @param mixed  $meta_value Optional. Metadata value. Must be serializable if
 *                           non-scalar. Default empty.
 * @return bool True on success, false on failure.
 */
function mpp_delete_gallery_meta( $gallery_id, $meta_key = '', $meta_value = '' ) {

	return delete_post_meta( $gallery_id, $meta_key, $meta_value );
}
