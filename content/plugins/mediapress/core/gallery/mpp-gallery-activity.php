<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A wrapper for bp_has_activity
 * checks if the gallery has associated activity
 * 
 * @param type $args
 * @return type
 */
function mpp_gallery_has_activity( $args = null ) {

	$default = array(
		'gallery_id'	=> mpp_get_current_gallery_id()
	);

	$args = wp_parse_args( $args, $default );

	extract( $args );

	$args = array(
		'meta_query' => array(
			array(
				'key'		=> '_mpp_gallery_id',
				'value'		=> $gallery_id
			),
			array(
				'key'		=> '_mpp_context',
				'value'		=> 'gallery',
				'compare'	=> '='
			)
		),
		'type' => 'mpp_media_upload'
	);
	return bp_has_activities( $args );
}

/**
 * Delete all activity meta where this gallery is attached
 * 
 * @param type $gallery_id
 * @return type
 */
function mpp_gallery_delete_activity_meta( $gallery_id ) {

	return mpp_delete_activity_meta_by_key_value( '_mpp_gallery_id', $gallery_id );
}

/**
 * Deleets all activity for the gllery
 * @param type $gallery_id
 */
function mpp_gallery_delete_activity( $gallery_id ) {

	//all activity where meta_key = _mpp_gallery_id

	return mpp_delete_activity_by_meta_key_value( '_mpp_gallery_id', $gallery_id );
}

/**
 * Get an array of unpublished media ids
 * 
 * @param int $gallery_id
 * @return array of media ids
 */
function mpp_gallery_get_unpublished_media( $gallery_id ) {

	return mpp_get_gallery_meta( $gallery_id, '_mpp_unpublished_media_id', false ); //get an array
}

/**
 * Add media to the list of unpublished media
 * 
 * @param int $gallery_id
 * @param int|array $media_ids single media id or an array of media ids
 */
function mpp_gallery_add_unpublished_media( $gallery_id, $media_ids ) {

	if ( ! mediapress()->is_bp_active() ) {
		return;
	}
	
	$media_ids = (array) $media_ids; // one or more media is given

	$unpublished = mpp_gallery_get_unpublished_media( $gallery_id );

	$media_ids = array_diff( $media_ids, $unpublished );

	//add all new media ids to the unpublished list

	foreach ( $media_ids as $new_media_id ) {

		mpp_add_gallery_meta( $gallery_id, '_mpp_unpublished_media_id', $new_media_id );
	}
}

/**
 * Update the list of unpublished media
 * 
 * @param int $gallery_id
 * @param int|array $media_ids single media id or an array of media ids
 */
function mpp_gallery_update_unpublished_media( $gallery_id, $media_ids ) {

	$media_ids = (array) $media_ids; // one or more media is given

	if ( empty( $media_ids ) ) {
		return;
	}
	//delete all existing media in the list
	mpp_gallery_delete_unpublished_media( $gallery_id );
	//add the new list
	mpp_gallery_add_unpublished_media( $gallery_id, $media_ids );
}

/**
 *  Delete the unpublished media
 * 
 * @param int $gallery_id
 * @param int|array $media_id either a single media id or an array of media ids
 */
function mpp_gallery_delete_unpublished_media( $gallery_id, $media_id = array() ) {

	if ( empty( $media_id ) ) {
		//delete all
		mpp_delete_gallery_meta( $gallery_id, '_mpp_unpublished_media_id' );
	} else {
		//media is given? or media ids are give?
		$media_ids = (array) $media_id;

		foreach ( $media_ids as $mid ) {

			mpp_delete_gallery_meta( $gallery_id, '_mpp_unpublished_media_id', $mid );
		}
	}
}

/**
 * Check if current Gallery has unpublished media
 * 
 * @param type $gallery_id
 * @return boolean
 */
function mpp_gallery_has_unpublished_media( $gallery_id ) {

	$media_ids = mpp_gallery_get_unpublished_media( $gallery_id );

	if ( ! empty( $media_ids ) ) {
		return true;
	}

	return false;
}

function mpp_gallery_record_activity( $args ) {

	$default = array(
		'id'			=> false,
		'gallery_id'	=> null,
		'media_ids'		=> null, //single id or an array of ids
		'action'		=> '',
		'content'		=> '',
		'type'			=> '', //type of activity  'create_gallery, update_gallery, media_upload etc'
			//'component'		=> '',// mpp_get_current_component(),
			//'component_id'	=> '',//mpp_get_current_component_id(),
			//'user_id'		=> '',//get_current_user_id(),
	);

	$args = wp_parse_args( $args, $default );

	if ( ! $args['gallery_id'] ) {
		return false;
	}

	$gallery_id = absint( $args['gallery_id'] );

	$gallery = mpp_get_gallery( $gallery_id );

	if ( ! $gallery ) {
		return false;
	}

	$args['status'] = $gallery->status;

	return mpp_record_activity( $args );
}
