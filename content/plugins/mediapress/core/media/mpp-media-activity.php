<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * a wrapper for bp_has_activity
 * Chck if the activities for a media exist
 * 
 * @param type $args
 * @return type
 */
function mpp_media_has_activity( $args = null ) {

	$default = array(
		'media_id' => mpp_get_current_media_id(),
	);

	$args = wp_parse_args( $args, $default );
	extract( $args );

	$args = array(
		'meta_query' => array(
			array(
				'key'	=> '_mpp_media_id',
				'value' => $media_id,
			)
		),
		'type' => 'mpp_media_upload'
	);
	
	return bp_has_activities( $args );
}

/**
 * Delete all the metas where the key and value matches given pair
 * @param type $media_id
 * @return type
 */
function mpp_media_delete_attached_activity_media_id( $media_id ) {

	return mpp_delete_activity_meta_by_key_value( '_mpp_attached_media_id', $media_id );
	
}

/**
 * Get associated activity Id for Media
 * 
 * @param type $media_id
 * @return type
 */
function mpp_media_get_activity_id( $media_id ) {
	
	return mpp_get_media_meta( $media_id, '_mpp_activity_id', true );
	
}

/**
 * 
 * @param type $media_id
 * @param type $activity_id
 * @return type
 */
function mpp_media_update_activity_id( $media_id, $activity_id ) {
	
	return mpp_update_media_meta( $media_id, '_mpp_activity_id', $activity_id );
	
}

/**
 * Check if Media has an activity associated
 * 
 * @param type $media_id
 * @return type
 */
function mpp_media_has_activity_entries( $media_id ) {
	
	return mpp_media_get_activity_id( $media_id );
	
}

/**
 * Delete all activity comments for this media
 * 
 * @param type $media_id
 */
function mpp_media_delete_activities( $media_id ) {
	
	return mpp_delete_activity_by_meta_key_value( '_mpp_media_id', $media_id );
	
}

//delete all activity meta entry for this media
//always call after deleting the media
function mpp_media_delete_activity_meta( $media_id ) {
	//delete _mpp_media_id
	//mpp_delete_activity_meta_by_key_value( '_mpp_media_id', $media_id );
	//delete _mpp_attached_media_ids

	mpp_delete_activity_meta_by_key_value( '_mpp_attached_media_id', $media_id );
}
