<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all the valid User Access statuses
 * 
 * @param type $component_type
 * @param type $component_id
 * @return array of status like array( 'public', 'private', 'friends' ) etc
 */
function mpp_get_accessible_statuses( $component_type, $component_id, $user_id = false ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$all_status = mpp_get_active_statuses();

	$allowed_status = array();

	foreach ( $all_status as $status => $status_details ) {

		if ( ! empty( $status_details->callback ) && is_callable( $status_details->callback ) ) {
			//should I use cal_user_func instead? have seenh that as inefficient though!
			$func = $status_details->callback;

			if ( $func( $component_type, $component_id, $user_id ) ) {
				$allowed_status[] = $status;
			}
		}
	}

	//shoudl we check for empty and mark invalid?
	//return the filtered, allowed status for the current context

	return apply_filters( "mpp_get_accessible_" . strtolower( $component_type ) . "_gallery_statuses", $allowed_status, $component_id, $user_id );
}

/**
 * Check if current user can create gallery for the given context
 * 
 * @param type $component
 * @param type $component_id
 * @return boolean
 */
function mpp_user_can_create_gallery( $component, $component_id ) {

	if ( ! is_user_logged_in() ) {
		return false;
	}

	$can_do = false;

	$user_id = get_current_user_id();

	if ( is_super_admin() ) {
		$can_do = true;
	} elseif ( $component == 'members' && $component_id == $user_id && mediapress()->is_bp_active() ) {
		$can_do = true;
	} elseif ( $component == 'groups' && function_exists( 'groups_is_user_member' ) && groups_is_user_member( $user_id, $component_id ) ) {
		$can_do = true;
	} elseif ( $component == 'sitewide' && mpp_is_active_component( 'sitewide' ) ) {
		$can_do = true;
	}

	$can_do = apply_filters( 'mpp_user_can_create_gallery', $can_do, $component, $component_id );

	return $can_do; //apply_filters( "mpp_can_user_upload_to_{$component}", $can_do, $component_id );
}

/**
 * For Single Gallery
 */

/**
 * Check if a User can list/View the gallery media
 * 
 * We are checking for gallery permission and seeing if it is allowed or not?
 * 
 * @param type $gallery_id
 * @param type $user_id
 * @return type
 */
function mpp_user_can_list_media( $gallery_id, $user_id = null ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	//if no user is set, It will be no

	$gallery = mpp_get_gallery( $gallery_id );

	$can_do = false;

	if ( $gallery->user_id == $user_id ) {
		$can_do = true;
	} else {
		$permissions = mpp_get_accessible_statuses( $gallery->component, $gallery->component_id, $user_id );

		if ( in_array( $gallery->status, $permissions ) ) {
			$can_do = true;
		}
	}

	return apply_filters( 'mpp_user_can_list_media', $can_do, $gallery, $user_id );
}

function mpp_user_can_edit_gallery( $gallery_id, $user_id = null ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$gallery = mpp_get_gallery( $gallery_id );

	$can = false;

	if ( is_super_admin( $user_id ) || $gallery->user_id == $user_id ) {
		$can = true;
	}
	//Each module should filter on it to add their own cap check
	return apply_filters( 'mpp_user_can_edit_gallery', $can, $gallery, $user_id );
}

function mpp_user_can_delete_gallery( $gallery_id, $user_id = null ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$gallery = mpp_get_gallery( $gallery_id );

	$can = false;

	//gallery owner & super admin can always delete it

	if ( ( $gallery->user_id == $user_id && $gallery->component != 'groups' ) || is_super_admin() ) {
		$can = true;
	}
	//modules should filter on it to do their own cap check
	return apply_filters( 'mpp_user_can_delete_gallery', $can, $gallery, $user_id );
}

/**
 * Can a user publish the media from a given gallery to activity
 * 
 * @param type $gallery_id
 * @param type $user_id
 * @return type
 */
function mpp_user_can_publish_gallery_activity( $gallery_id, $user_id = null ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$gallery = mpp_get_gallery( $gallery_id );

	$can = false;

	//gallery owner & super admin can always publish it

	if ( mediapress()->is_bp_active() && bp_is_active( 'activity' ) && ( $gallery->user_id == $user_id || is_super_admin() ) ) {
		$can = true;
	}

	return apply_filters( 'mpp_user_can_publish_gallery_activity', $can, $gallery, $user_id );
}

/**
 * 
 * @param type $gallery_id
 * @return type
 */
function mpp_user_can_comment_on_gallery( $gallery_id ) {

	return apply_filters( 'mpp_user_can_comment_on_gallery', is_user_logged_in(), $gallery_id );
}

/**
 * Can the current user upload?
 * 
 * @param type $component
 * @param type $component_id
 * @param type $gallery
 * @return boolean
 */
function mpp_user_can_upload( $component, $component_id, $gallery = null ) {

	if ( ! is_user_logged_in() ) {
		return false;
	}

	$can_do = false;

	$user_id = get_current_user_id();

	if ( is_super_admin() ) {
		$can_do = true;
	} elseif ( mediapress()->is_bp_active() && $component == 'members' && $component_id == $user_id ) {
		$can_do = true;
	} elseif ( mpp_is_active_component( 'groups' ) && $component == 'groups' && function_exists( 'groups_is_user_member' ) && groups_is_user_member( $user_id, $component_id ) ) {
		$can_do = true;
	} elseif ( mpp_is_active_component( 'sitewide' ) && $component == 'sitewide' && $component_id == $user_id ) {
		$can_do = true;
	}

	$can_do = apply_filters( 'mpp_user_can_upload', $can_do, $component, $component_id, $gallery );

	return apply_filters( "mpp_can_user_upload_to_{$component}", $can_do, $gallery );
}

/* * * For single Media */

/**
 * Can the User see this media?
 * 
 * @param type $media_id
 * @param type $user_id
 * @return type
 */
function mpp_user_can_view_media( $media_id, $user_id = null ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$media = mpp_get_media( $media_id );

	$gallery = mpp_get_gallery( $media->gallery_id );

	//a media is only viewable if the parent gallery is viewable

	$allowed = false;

	if ( mpp_user_can_list_media( $gallery->id, $user_id ) ) {

		//nw let us check for media permissions
		$user_permissions = mpp_get_accessible_statuses( $gallery->component, $gallery->component_id, $user_id );

		if ( in_array( $media->status, $user_permissions ) ) {
			$allowed = true;
		}
	}

	return apply_filters( 'mpp_user_can_view_media', $allowed, $media, $gallery, $user_id );
}

/**
 * Check if media can be edited by the given user
 * 
 * @param type $media_id
 * @param type $user_id
 * @return type
 */
function mpp_user_can_edit_media( $media_id, $user_id = null ) {

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$media = mpp_get_media( $media_id );
	
	if ( ! $media ) {
		return false;
	}
	
	$gallery = mpp_get_gallery( $media->gallery_id );

	//this setting should be per gallery based
	$allow = false; //do not alow editing by default
	//if the user is gallery creator, allow him to upload
	if ( $gallery->user_id == $user_id ) {//should we consider context here like members gallery or groups gallery?
		$allow = true;
	} elseif ( $user_id == $media->user_id ) {//check per gallery settings first
		//since current user is uploader/contributor
		//let us check if the gallery allows editing for contributor
		$allow_editing = mpp_get_gallery_meta( $gallery->id, '_mpp_contributors_can_edit', true );

		if ( $allow_editing == 'yes' ) {
			$allow = true;
		} elseif ( $allow_editing != 'no' && mpp_get_option( 'contributors_can_edit' ) ) {
			//check for global settings & make sure it is not overridden in the local settings

			$allow = true;
		}
	}

	return apply_filters( 'mpp_user_can_edit_media', $allow, $media, $gallery, $user_id );
}

/**
 * Check if the media can be deleted by the user
 * 
 * @param type $media_id
 * @param type $user_id
 * @return boolean true if allowed false otherwise
 */
function mpp_user_can_delete_media( $media_id, $user_id = null ) {


	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	$media = mpp_get_media( $media_id );

	if ( ! $media ) {
		return false;
	}

	$gallery = mpp_get_gallery( $media->gallery_id );

	$allow = false; //do not alow editing by default
	//if the user is gallery creator, allow him to delete media
	if ( $gallery->user_id == $user_id ) {//should we consider context here like members gallery or groups gallery?
		$allow = true;
	} elseif ( $user_id == $media->user_id ) {
		//since current user is uploader/contributor
		//let us check if the gallery allows deleting for contributor
		$allow_deleting = mpp_get_gallery_meta( $gallery->id, '_mpp_contributors_can_delete', true );

		if ( $allow_deleting == 'yes' ) {
			$allow = true;
		} elseif ( $allow_deleting != 'no' && mpp_get_option( 'contributors_can_delete' ) ) {
			//check for global settings & make sure it is not overridden in the local settings
			$allow = true;
		}
	}

	return apply_filters( 'mpp_user_can_delete_media', $allow, $media, $gallery, $user_id );
}

/* * * Callback functions for permission checking */

/**
 * Check if user has public gallery access
 * 
 * @param type $component_type
 * @param type $component_id
 * @param type $user_id
 * @return type
 */
function mpp_check_public_access( $component_type, $component_id, $user_id = null ) {

	//always return true in case of public
	return apply_filters( 'mpp_check_public_access', true, $component_type, $component_id, $user_id );
}

/**
 * Check if User has private gallery access
 * @param type $component_type
 * @param type $component_id
 * @param type $user_id
 * @return type
 */
function mpp_check_private_access( $component_type, $component_id, $user_id = null ) {

	$allow = false;
	//needs update, check for bp_displaye_user_id() == $user_id
	if ( function_exists( 'bp_is_my_profile' ) && bp_is_my_profile() || is_super_admin() ) {
		$allow = true;
	}

	return apply_filters( 'mpp_check_private_access', $allow, $component_type, $component_id, $user_id );
}

/**
 * Check if the User Can access Friends only privacy
 * @param type $component_type
 * @param type $component_id
 * @param type $user_id
 * @return type
 */
function mpp_check_friends_access( $component_type, $component_id, $user_id = null ) {

	$allow = false;

	if ( is_super_admin() || $component_id == $user_id || bp_is_active( 'friends' ) && ( 'is_friend' == BP_Friends_Friendship::check_is_friend( $user_id, $component_id ) ) ) {
		$allow = true;
	}

	return apply_filters( 'mpp_check_friends_access', $allow, $component_type, $component_id, $user_id );
}

/**
 * Check if the User Can access Logged in only privacy
 * @param type $component_type
 * @param type $component_id
 * @param type $user_id
 * @return type
 */
function mpp_check_loggedin_access( $component_type, $component_id, $user_id = null ) {

	$allow = false;

	if ( is_user_logged_in() ) {
		$allow = true;
	}

	return apply_filters( 'mpp_check_loggedin_access', $allow, $component_type, $component_id, $user_id );
}

/**
 * Checks if the current user is a follower of the owner component
 * 
 * @param type $component_type
 * @param type $component_id
 * @param type $user_id
 * @return type
 */
function mpp_check_followers_access( $component_type, $component_id, $user_id = null ) {

	$allow = false;

	if ( is_super_admin() || $component_id == $user_id || function_exists( 'bp_follow_is_following' ) && bp_follow_is_following( array( 'leader_id' => $component_id, 'follower_id' => get_current_user_id() ) ) ) {
		$allow = true;
	}

	return apply_filters( 'mpp_check_friends_access', $allow, $component_type, $component_id, $user_id );
}

/**
 * Checks if the owner is a follower of current user
 * Used in status callback
 * 
 * @see mpp_init
 * @see mpp_register_status
 * 
 * @param type $component_type
 * @param type $component_id
 * @param type $user_id
 * @return type
 */
function mpp_check_following_access( $component_type, $component_id, $user_id = null ) {

	$allow = false;

	if ( is_super_admin() || $component_id == $user_id || function_exists( 'bp_follow_is_following' ) && bp_follow_is_following( array( 'leader_id' => get_current_user_id(), 'follower_id' => $component_id ) ) ) {
		$allow = true;
	}

	return apply_filters( 'mpp_check_friends_access', $allow, $component_type, $component_id, $user_id );
}
