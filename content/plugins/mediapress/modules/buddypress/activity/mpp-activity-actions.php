<?php

//No direct access to the file 
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}
/* * *
 * MediaPress Activity Related actions
 */

/**
 * Do not allow BuddyPress to set the MediaPress update as activity
 * 
 */
function mpp_activity_disable_set_latest_activity_id( $val, $object_id, $meta_key, $meta_value, $prev_value ) {

	if ( $meta_key != 'bp_latest_update' ) {
		return $val; // we will only stop for bp_latest_update
	}

	if ( empty( $_POST['mpp-attached-media'] ) ) {
		return $val;
	}
	//return anything non null value to stop the new id being saved in db
	return 1; //It will stop wp from storing the bp_latest_update meta when the activity update is MediaPress related.
}

add_filter( 'update_user_metadata', 'mpp_activity_disable_set_latest_activity_id', 101, 5 );

/**
 * When a user activity is posted, we mark all the media that was uploaded and is set as orphaned(by default) to be attached to this activity
 * @param string $content
 * @param int $user_id
 * @param int $activity_id
 */
function mpp_activity_mark_attached_media_for_user_wall( $content, $user_id, $activity_id ) {

	mpp_activity_mark_attached_media( $activity_id );
}

add_action( 'bp_activity_posted_update', 'mpp_activity_mark_attached_media_for_user_wall', 1, 3 );

/**
 * When a group activity is posted, we mark all the media that was uploaded and is set as orphaned(by default) to be attached to this activity
 * @param string $content
 * @param int $user_id
 * @param int $group_id
 * @param int $activity_id
 */
function mpp_activity_mark_attached_media_for_groups_wall( $content, $user_id, $group_id, $activity_id ) {

	mpp_activity_mark_attached_media( $activity_id );
}

add_action( 'bp_groups_posted_update', 'mpp_activity_mark_attached_media_for_groups_wall', 1, 4 );

/**
 * Register Activity actions for the enabled components
 */
function mpp_activity_register_actions() {

	$components = mpp_get_active_components();
	//get the component ids as key
	$components = array_keys( $components );
	//add activity to the list of components
	array_push( $components, 'activity' );

	$contexts = $components;
	//the sad thing is BuddyPress deviates here, It should have used component as the context name, that makes much sense
	foreach ( $contexts as $key => $context_component ) {

		if ( $context_component == 'members' ) {
			$contexts[$key] = 'member';
		} elseif ( $context_component == 'groups' ) {
			$contexts[$key] = 'group';
		}
	}
	// Register the activity stream actions for all enabled gallery component
	foreach ( $components as $component ) {
		bp_activity_set_action(
				$component, 'mpp_media_upload', __( 'User Uploaded a media', 'mediapress' ), 'mpp_format_activity_action_media_upload', __( 'Gallery Updates', 'mediapress' ), $contexts
		);
	}

	do_action( 'mpp_register_activity_actions' );
}

add_action( 'bp_register_activity_actions', 'mpp_activity_register_actions' );

/**
 * On New Activity Comment, Create a new shadow WordPress comment too
 * 
 * @param type $comment_id
 * @param type $param
 * @param type $activity
 * @return type
 */
function mpp_activity_synchronize_reply_to_comment( $comment_id, $param, $activity ) {
	//it must be upload from activity
	//so lt us crea
	//check that the media was posted in activity
	$gallery_id = mpp_activity_get_gallery_id( $activity->id );

	if ( ! $gallery_id ) {
		return;
	}
	
	$bp_comment = new BP_Activity_Activity( $comment_id );

	//now we need to add a comment
	//
    //my logic to find the parent may be flawed here, Needs a confirmation from other people
	if ( $bp_comment->secondary_item_id != $activity->id ) {
		$parent_id = $bp_comment->secondary_item_id;
		//this is a multilevel comment
		//we will add a child comment in wp too as the 
	} else {
		$parent_id = $activity->id;
	}

	$wp_comment_parent_id = (int) mpp_activity_get_associated_comment_id( $parent_id );
	//if we are here, It must be an activity where we have uploaded media
	//we will create a comment and add
	if ( $wp_comment_parent_id > 0 ) {
		//we have a parent comment associated, so we will be adding a child comment
		$wp_comment = get_comment( $wp_comment_parent_id );
	}

	$commetn_data = array(
		'post_id'			=> $gallery_id,
		'user_id'			=> get_current_user_id(),
		'comment_parent'	=> $wp_comment_parent_id,
		'comment_content'	=> $bp_comment->content,
		'comment_type'		=> mpp_get_comment_type(),
	);

	$new_comment_id = mpp_add_comment( $commetn_data );

	//update comment meta
	if ( $new_comment_id ) {

		mpp_update_comment_meta( $new_comment_id, '_mpp_activity_id', $comment_id );

		mpp_activity_update_associated_comment_id( $comment_id, $new_comment_id );
	}
}

//we no longer synchronize
//add_action( 'bp_activity_comment_posted', 'mpp_activity_synchronize_reply_to_comment', 10, 3 );
/**
 * Create a WordPress comment when an update with media is posted
 * 
 * @param string $content
 * @param int $user_id
 * @param int $activity_id
 */
function mpp_activity_synchronize_user_activity_to_comment( $content, $user_id, $activity_id ) {

	mpp_activity_create_comment_for_activity( $activity_id );
}

//add_action( 'bp_activity_posted_update', 'mpp_activity_synchronize_user_activity_to_comment', 10, 3 );
/**
 * This section deals with the activity to comment synchronization
 */

/**
 * Create a shadow WordPress comment when a group update with media is posted
 *  
 * @param string $content
 * @param int $user_id
 * @param int $group_id
 * @param int $activity_id
 */
function mpp_activity_synchronize_group_activity_to_comment( $content, $user_id, $group_id, $activity_id ) {

	mpp_activity_create_comment_for_activity( $activity_id );
}

//add_action( 'bp_groups_posted_update', 'mpp_activity_synchronize_group_activity_to_comment', 10, 4 );