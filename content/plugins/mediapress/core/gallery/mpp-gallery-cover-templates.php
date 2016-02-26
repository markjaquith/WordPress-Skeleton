<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print the absolute url of the cover image
 * 
 * @param type $type (thumbnail|mid|large or any registerd size)
 * @param mixed $gallery id or object
 */
function mpp_gallery_cover_src( $type = 'thumbnail', $gallery = null ) {

	echo mpp_get_gallery_cover_src( $type, $gallery );
}

/**
 * Get the absolute url of the cover image
 * 
 * @param type $type ( thumbnail|mind|large or any register image size)
 * @param type $gallery
 * @return string
 */
function mpp_get_gallery_cover_src( $type = 'thumbnail', $gallery = null ) {

	$gallery = mpp_get_gallery( $gallery );

	$thumbnail_id = mpp_get_gallery_cover_id( $gallery->id );

	if ( ! $thumbnail_id ) {

		//if gallery type is photo, then set the first photo as the cover
		//todo, firs update media count
		if ( $gallery->type == 'photo' ) {//&& mpp_gallery_has_media( $gallery->id )
			$thumbnail_id = mpp_gallery_get_latest_media_id( $gallery->id );

			//update gallery cover id
			if ( $thumbnail_id ) {
				mpp_update_gallery_cover_id( $gallery->id, $thumbnail_id );
			}
			
		}//
		//
		if ( ! $thumbnail_id ) {

			$default_image = mpp_get_default_gallery_cover_image_src( $gallery, $type );

			return apply_filters( 'mpp_get_gallery_default_cover_image_src', $default_image, $type, $gallery );
		}
	}

	//get the image src
	$thumb_image_url = _mpp_get_cover_photo_src( $type, $thumbnail_id );

	return apply_filters( 'mpp_get_gallery_cover_src', $thumb_image_url, $type, $gallery );
}

/**
 * Check if Gallery has a cover set
 * 
 * @param type $gallery
 * @return boolean|int false if no cover else cover image id
 */
function mpp_gallery_has_cover_image( $gallery = null ) {

	$gallery = mpp_get_gallery( $gallery );

	return mpp_get_gallery_cover_id( $gallery->id );
}

/**
 * If there is no cover set for a gallery, use the default cover image
 * 
 * @param type $gallery
 * @param type $cover_type
 * @return type
 */
function mpp_get_default_gallery_cover_image_src( $gallery, $cover_type ) {

	$gallery = mpp_get_gallery( $gallery );

	//we need to cache the assets to avoid heavy file system read/write etc

	$key = $gallery->type . '-' . $cover_type;
	//let us assume a naming convention like this
	//gallery_type-cover_type.png? or whatever e.g video-thumbnail.png, photo-mid.png
	$default_image = $gallery->type . '-' . $cover_type . '.png';

	$default_image = apply_filters( 'mpp_default_cover_file_name', $default_image, $cover_type, $gallery );

	return mpp_get_asset_url( 'assets/images/' . $default_image, $key );
}

/**
 * Get the attachment Id which is used for gallery cover
 * 
 * @param type $gallery_id
 * @return int|boolean attachment id or false
 */
function mpp_get_gallery_cover_id( $gallery_id ) {

	return mpp_get_gallery_meta( $gallery_id, '_mpp_cover_id', true );
}

/**
 * Update Gallery cover attachment id
 * 
 * @param type $gallery_id
 * @param type $cover_id
 * @return type
 */
function mpp_update_gallery_cover_id( $gallery_id, $cover_id ) {

	return mpp_update_gallery_meta( $gallery_id, '_mpp_cover_id', $cover_id );
}

/**
 * Delete gallery cover Id
 * 
 * @param type $gallery_id
 * 
 * @return type
 */
function mpp_delete_gallery_cover_id( $gallery_id ) {

	return mpp_delete_gallery_meta( $gallery_id, '_mpp_cover_id' );
}

function _mpp_get_cover_photo_src( $type = '', $media = null ) {
	
	if ( is_object( $media ) ) {
		$media = $media->id;
	}

	$storage_manager = mpp_get_storage_manager( $media );

	return $storage_manager->get_src( $type, $media );
}
