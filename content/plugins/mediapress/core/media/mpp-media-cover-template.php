<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Use it only for non photo media
 * 
 * @param type $type
 * @param type $media
 * @return type
 */
function mpp_get_media_cover_src( $type = 'thumbnail', $media = null ) {

	$media = mpp_get_media( $media );

	$thumbnail_id = mpp_get_media_cover_id( $media->id );

	if ( ! $thumbnail_id ) {
		//if it is a photo, let us say that the photo is itself the cover
		if ( $media->type == 'photo' ) {//&& mpp_gallery_has_media( $gallery->id )
			$thumbnail_id = $media->id;
		}

		if ( ! $thumbnail_id ) {
			$default_image = mpp_get_default_media_cover_image_src( $media, $type );
			return apply_filters( 'mpp_get_media_default_cover_image_src', $default_image, $type, $media );
		}
	}

	$storage = mpp_get_storage_manager( $media->id );
	//get the image src
	$thumb_image_url = $storage->get_src( $type, $thumbnail_id );

	return apply_filters( 'mpp_get_media_cover_src', $thumb_image_url, $type, $media );
	
}

/**
 * Check if media has cover set?
 * 
 * @param type $media
 * @return type
 */
function mpp_media_has_cover_image( $media = null ) {

	$media = mpp_get_media( $media );

	return mpp_get_media_cover_id( $media->id );
	
}

function mpp_get_default_media_cover_image_src( $media, $cover_type ) {

	$media = mpp_get_media( $media );

	//we need to cache the assets to avoid heavy file system read/write etc
	$key = $media->type . '-' . $cover_type;
	//let us assume a naming convention like this
	//media_type-cover_type.png? or whatever e.g video-thumbnail.png, photo-mid.png
	$default_image = $media->type . '-' . $cover_type . '.png';

	$default_image = apply_filters( 'mpp_default_media_cover_file_name', $default_image, $cover_type, $media );

	return mpp_get_asset_url( 'assets/images/' . $default_image, $key );
	
}

/**
 * Get media cover id
 * 
 * @param type $media_id
 * @return type
 */
function mpp_get_media_cover_id( $media_id ) {

	return mpp_get_media_meta( $media_id, '_mpp_cover_id', true );
	
}

/**
 * Update media cover id
 * 
 * @param type $media_id
 * @param type $cover_id
 * @return type
 */
function mpp_update_media_cover_id( $media_id, $cover_id ) {

	return mpp_update_media_meta( $media_id, '_mpp_cover_id', $cover_id );
	
}

/**
 * Delete media cover id
 * 
 * @param type $media_id
 * 
 * @return type
 */
function mpp_delete_media_cover_id( $media_id ) {

	return mpp_delete_media_meta( $media_id, '_mpp_cover_id' );
	
}
