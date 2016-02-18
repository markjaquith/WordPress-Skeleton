<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Various Gallery related actions handlers
 * 
 */

/**
 * Handles Gallery creation on the front end in non ajax case
 * 
 * @return type
 */
function mpp_action_create_gallery() {

	//allow gallery to be created from anywhere
	//the form must have mpp-action set and It should be set to 'create-gallery'
	if ( empty( $_POST['mpp-action'] ) || $_POST['mpp-action'] != 'create-gallery' ) {
		return;
	}

	$referer = wp_get_referer();
	//if we are here, It is gallery create action
	if ( ! wp_verify_nonce( $_POST['mpp-nonce'], 'mpp-create-gallery' ) ) {
		//add error message and return back to the old page
		mpp_add_feedback( __( 'Action not authorized!', 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}
	//update it to allow passing component/id from the form
	$component = isset( $_POST['mpp-gallery-component'] ) ? $_POST['mpp-gallery-component'] : mpp_get_current_component();
	$component_id = isset( $_POST['mpp-gallery-component-id'] ) ? $_POST['mpp-gallery-component-id'] : mpp_get_current_component_id();

	//check for permission
	//we may want to allow passing of component from the form in future!
	if ( ! mpp_user_can_create_gallery( $component, $component_id ) ) {
		mpp_add_feedback( __( "You don't have permission to create gallery!", 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}

	//if we are here, validate the data and let us see if we can create

	$title = $_POST['mpp-gallery-title'];
	$description = $_POST['mpp-gallery-description'];

	$type = $_POST['mpp-gallery-type'];
	$status = $_POST['mpp-gallery-status'];
	$errors = array();

	if ( ! mpp_is_active_status( $status ) ) {
		$errors['status'] = __( 'Invalid Gallery status!', 'mediapress' );
	}

	if ( ! mpp_is_active_type( $type ) ) {
		$errors['type'] = __( 'Invalid gallery type!', 'mediapress' );
	}

	//check for current component
	if ( ! mpp_is_enabled( $component, $component_id ) ) {
		$errors['component'] = __( 'Invalid action!', 'mediapress' );
	}

	if ( empty( $title ) ) {
		$errors['title'] = __( 'Title can not be empty', 'mediapress' );
	}

	//give opportunity to other plugins to add their own validation errors
	$validation_errors = apply_filters( 'mpp-create-gallery-field-validation', $errors, $_POST );

	if ( ! empty( $validation_errors ) ) {
		//let us add the validation error and return back to the earlier page
		$message = join( '\r\n', $validation_errors );

		mpp_add_feedback( $message, 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}
		return;
	}

	//let us create gallery

	$gallery_id = mpp_create_gallery( array(
		'title'			=> $title,
		'description'	=> $description,
		'type'			=> $type,
		'status'		=> $status,
		'creator_id'	=> get_current_user_id(),
		'component'		=> $component,
		'component_id'	=> $component_id
	) );


	if ( ! $gallery_id ) {
		mpp_add_feedback( __( 'Unable to create gallery!', 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}
		return;
	}

	//if we are here, the gallery was created successfully,
	//let us redirect to the gallery_slug/manage/upload page

	$redirect_url = mpp_get_gallery_add_media_url( $gallery_id );

	mpp_add_feedback( __( 'Gallery created successfully!', 'mediapress' ) );

	mpp_redirect( $redirect_url );
}

add_action( 'mpp_actions', 'mpp_action_create_gallery', 2 );

/**
 * Handles gallery details updation
 * 
 * @return type
 */
function mpp_action_edit_gallery() {
	//allow gallery to be created from anywhere

	if ( empty( $_POST['mpp-action'] ) || $_POST['mpp-action'] != 'edit-gallery' ) {
		return;
	}

	$referer = wp_get_referer();

	//if we are here, It is gallery create action

	if ( ! wp_verify_nonce( $_POST['mpp-nonce'], 'mpp-edit-gallery' ) ) {
		//add error message and return back to the old page
		mpp_add_feedback( __( 'Action not authorized!', 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}

	$gallery_id = absint( $_POST['mpp-gallery-id'] );

	$gallery = mpp_get_gallery( $gallery_id );
	
	if ( ! $gallery_id || empty( $gallery ) ) {
		return;
	}

	//check for permission
	//we may want to allow passing of component from the form in future!
	if ( ! mpp_user_can_edit_gallery( $gallery_id ) ) {
		mpp_add_feedback( __( "You don't have permission to edit this gallery!", 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}

	//if we are here, validate the data and let us see if we can create

	$title = $_POST['mpp-gallery-title'];
	$description = $_POST['mpp-gallery-description'];


	$status = $_POST['mpp-gallery-status'];
	$errors = array();

	if ( ! mpp_is_active_status( $status ) ) {
		$errors['status'] = __( 'Invalid Gallery status!', 'mediapress' );
	}

	if ( empty( $title ) ) {
		$errors['title'] = __( 'Title can not be empty', 'mediapress' );
	}

	//give opportunity to other plugins to add their own validation errors
	$validation_errors = apply_filters( 'mpp-edit-gallery-field-validation', $errors, $_POST );

	if ( ! empty( $validation_errors ) ) {
		//let us add the validation error and return back to the earlier page
		$message = join( '\r\n', $validation_errors );

		mpp_add_feedback( $message, 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}

	//let us create gallery

	$gallery_id = mpp_update_gallery( array(
		'title'			=> $title,
		'description'	=> $description,
		'status'		=> $status,
		//'creator_id'	=> $gallery->user_id,
		'id'			=> $gallery_id,
	) );


	if ( ! $gallery_id ) {

		mpp_add_feedback( __( 'Unable to update gallery!', 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}

	//if we are here, the gallery was created successfully,
	//let us redirect to the gallery_slug/manage/upload page

	$redirect_url = mpp_get_gallery_settings_url( $gallery_id );

	mpp_add_feedback( __( 'Gallery updated successfully!', 'mediapress' ) );

	mpp_redirect( $redirect_url );
}

add_action( 'mpp_actions', 'mpp_action_edit_gallery', 2 ); //update gallery settings, cover

/**
 * Handles Gallery deletion
 * 
 * @return type
 */
function mpp_action_delete_gallery() {

	if ( empty( $_POST['mpp-action'] ) || $_POST['mpp-action'] != 'delete-gallery' ) {
		return;
	}

	if ( empty( $_POST['gallery_id'] ) ) {
		return;
	}

	$referer = wp_get_referer();

	if ( ! wp_verify_nonce( $_POST['mpp-nonce'], 'mpp-delete-gallery' ) ) {
		//add error message and return back to the old page
		mpp_add_feedback( __( 'Action not authorized!', 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}

	if ( empty( $_POST['mpp-delete-agree'] ) ) {
		return;
		//@todo add feedback that you must agree
	}

	$gallery = '';

	if ( ! empty( $_POST['gallery_id'] ) ) {
		$gallery = mpp_get_gallery( absint( $_POST['gallery_id'] ) );
	}

	//check for permission
	//we may want to allow passing of component from the form in future!
	if ( ! mpp_user_can_delete_gallery( $gallery ) ) {

		mpp_add_feedback( __( "You don't have permission to delete this gallery!", 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}

	//if we are here, delete gallery and redirect to the component base url

	$redirect_url = apply_filters( 'mpp_delete_gallery_redirect_location', mpp_get_gallery_base_url( $gallery->component, $gallery->component_id ), $gallery );

	mpp_delete_gallery( $gallery->id );

	mpp_redirect( $redirect_url );
}

add_action( 'mpp_actions', 'mpp_action_delete_gallery', 2 );

/**
 * Hndles bulk edit action
 * 
 */
function mpp_action_gallery_media_bulkedit() {

	if ( empty( $_POST['mpp-action'] ) || $_POST['mpp-action'] != 'edit-gallery-media' ) {
		return;
	}

	if ( ! $_POST['mpp-editing-media-ids'] ) {
		return;
	}

	$referer = wp_get_referer();

	if ( ! wp_verify_nonce( $_POST['mpp-nonce'], 'mpp-edit-gallery-media' ) ) {

		//add error message and return back to the old page
		mpp_add_feedback( __( 'Action not authorized!', 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}

	$media_ids = $_POST['mpp-editing-media-ids'];
	$media_ids = wp_parse_id_list( $media_ids );


	$bulk_action = false;

	if ( ! empty( $_POST['mpp-edit-media-bulk-action'] ) ) {
		$bulk_action = $_POST['mpp-edit-media-bulk-action']; //we are leaving this to allow future enhancements with other bulk action and not restricting to delete only
	}

	foreach ( $media_ids as $media_id ) {
		//check what action should we take?
		//1. check if $bulk_action is set? then we may ned to check for deletion
		//otherwise, just update the details :)
		if ( $bulk_action == 'delete' && ! empty( $_POST['mpp-delete-media-check'][$media_id] ) ) {

			//delete and continue
			//check if current user can delete?

			if ( ! mpp_user_can_delete_media( $media_id ) ) {
				//if the user is unable to delete media, should we just continue the loop or breakout and redirect back with error?
				//I am in favour of showing error

				mpp_add_feedback( __( 'Not allowed to delete!', 'mediapress' ) );

				if ( $referer ) {
					mpp_redirect( $referer );
				}

				return;
			}

			//if we are here, let us delete the media
			mpp_delete_media( $media_id );

			mpp_add_feedback( __( 'Deleted successfully!', 'mediapress' ), 'error' ); //it will do for each media, that is not  good thing btw

			continue;
		}
		//since we already handled delete for the media checked above, 
		//we don't want to do it for the other media hoping that the user was performing bulk delete and not updating the media info
		if ( $bulk_action == 'delete' ) {
			continue;
		}

		$media_title = $_POST['mpp-media-title'][$media_id];

		$media_description = $_POST['mpp-media-description'][$media_id];

		$status = $_POST['mpp-media-status'][$media_id];
		//type is not editable
		//$type = $_POST['mpp-media-type'][$media_id];
		//if we are here, It must not be a bulk action
		$media_info = array(
			'id'			=> $media_id,
			'title'			=> $media_title,
			'description'	=> $media_description,
			// 'type'		=> $type,
			'status'		=> $status,
		);

		mpp_update_media( $media_info );
	}

	if ( ! $bulk_action ) {
		mpp_add_feedback( __( 'Updated!', 'mediapress' ) );
	}

	if ( $referer ) {
		mpp_redirect( $referer );
	}
}

add_action( 'mpp_actions', 'mpp_action_gallery_media_bulkedit', 2 );

/**
 * Handles Gallery Media Reordering
 * 
 * 
 * @return type
 */
function mpp_action_reorder_gallery_media() {

	if ( empty( $_POST['mpp-action'] ) || $_POST['mpp-action'] != 'reorder-gallery-media' ) {
		return;
	}

	$referer = wp_get_referer();

	if ( ! wp_verify_nonce( $_POST['mpp-nonce'], 'mpp-reorder-gallery-media' ) ) {
		//add error message and return back to the old page
		mpp_add_feedback( __( 'Action not authorized!', 'mediapress' ), 'error' );

		mpp_redirect( $referer );
	}
	//should we check for the permission? not here

	$media_ids = $_POST['mpp-media-ids']; //array

	$media_ids = wp_parse_id_list( $media_ids );

	$order = count( $media_ids );

	foreach ( $media_ids as $media_id ) {

		if ( ! mpp_user_can_edit_media( $media_id ) ) {
			//unauthorized attemt 

			mpp_add_feedback( __( "You don't have permission to update!", 'mediapress' ), 'error' );

			if ( $referer ) {
				mpp_redirect( $referer );
			}

			return;
		}

		//if we are here, let us update the order
		mpp_update_media_order( $media_id, $order );
		$order --;
	}

	if ( $media_id ) {
		//mark the gallery assorted, we use it in MPP_Media_query to see what should be the default order
		$media = mpp_get_media( $media_id );
		//mark the gallery as sorted
		mpp_mark_gallery_sorted( $media->gallery_id );
	}

	mpp_add_feedback( __( 'Updated', 'mediapress' ) );

	if ( $referer ) {
		mpp_redirect( $referer );
	}
}

add_action( 'mpp_actions', 'mpp_action_reorder_gallery_media', 2 );

/**
 * Handles Gallery Cover deletion
 * 
 * @return type
 */
function mpp_action_delete_gallery_cover() {

	if ( ! mpp_is_gallery_cover_delete() ) {
		return;
	}

	if ( ! $_REQUEST['gallery_id'] ) {
		return;
	}

	$gallery = mpp_get_gallery( absint( $_REQUEST['gallery_id'] ) );

	$referer = $redirect_url = mpp_get_gallery_settings_url( $gallery );

	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'delete-cover' ) ) {
		//add error message and return back to the old page
		mpp_add_feedback( __( 'Action not authorized!', 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}


	//we may want to allow passing of component from the form in future!
	if ( ! mpp_user_can_delete_gallery( $gallery ) ) {

		mpp_add_feedback( __( "You don't have permission to delete this cover!", 'mediapress' ), 'error' );

		if ( $referer ) {
			mpp_redirect( $referer );
		}

		return;
	}
	//we always need to delete this
	$cover_id = mpp_get_gallery_cover_id( $gallery->id );
	mpp_delete_gallery_cover_id( $gallery->id );

	//if( $gallery->type != 'photo' ) {
	//delete the uploaded cover too

	mpp_delete_media( $cover_id );

	//}
	mpp_add_feedback( __( 'Cover deleted successfully!', 'mediapress' ) );

	//if we are here, delete gallery and redirect to the component base url

	mpp_redirect( $redirect_url );
}

add_action( 'mpp_actions', 'mpp_action_delete_gallery_cover', 2 );

function mpp_action_publish_gallery_media_to_activity() {


	if ( ! mediapress()->is_bp_active() || ! is_user_logged_in() || ! mpp_is_gallery_management() || ! bp_is_action_variable( 'publish', 1 ) ) {
		return;
	}

	$gallery_id = absint( $_GET['gallery_id'] );

	if ( ! $gallery_id ) {
		return;
	}

	//$referrer = wp_get_referer();
	//if( empty( $referrer ) ) {
	$referrer = mpp_get_gallery_edit_media_url( $gallery_id );
	//}
	//verify nonce

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'publish' ) ) {
		mpp_add_feedback( __( 'Unauthorized action.', 'mediapress' ), 'error' );
		mpp_redirect( $referrer );
	}

	//all is good check for permission

	if ( ! mpp_user_can_publish_gallery_activity( $gallery_id ) ) {

		mpp_add_feedback( __( "You don't have sufficient permission.", 'mediapress' ), 'error' );
		mpp_redirect( $referrer );
	}

	if ( ! mpp_gallery_has_unpublished_media( $gallery_id ) ) {

		mpp_add_feedback( __( 'Nothing to publish.', 'mediapress' ), 'error' );
		mpp_redirect( $referrer );
	}

	//now we can safely publish
	$media_ids = mpp_gallery_get_unpublished_media( $gallery_id );

	$media_count = count( $media_ids );

	$gallery = mpp_get_gallery( $gallery_id );

	$type = $gallery->type;

	$type_name = _n( $type, $type . 's', $media_count );
	$user_link = mpp_get_user_link( get_current_user_id() );

	$gallery_url = mpp_get_gallery_permalink( $gallery );

	$gallery_link = '<a href="' . esc_url( $gallery_url ) . '" title="' . esc_attr( $gallery->title ) . '">' . mpp_get_gallery_title( $gallery ) . '</a>';
//has media, has permission, so just publish now
	//
		
	$activity_id = mpp_gallery_record_activity( array(
		'gallery_id'	=> $gallery_id,
		'media_ids'		=> $media_ids,
		'type'			=> 'media_publish',
		'action'		=> sprintf( __( '%s shared %d %s to %s ', 'mediaprses' ), $user_link, $media_count, $type_name, $gallery_link ),
		'content'		=> '',
	) );


	if ( $activity_id ) {
		mpp_gallery_delete_unpublished_media( $gallery_id );

		mpp_add_feedback( __( "Published to activity successfully.", 'mediapress' ) );
	} else {
		mpp_add_feedback( __( "There was a problem. Please try again later.", 'mediapress' ), 'error' );
	}

	mpp_redirect( $referrer );
}

add_action( 'mpp_actions', 'mpp_action_publish_gallery_media_to_activity', 2 );

function mpp_action_hide_unpublished_media() {


	if ( ! mediapress()->is_bp_active() || ! is_user_logged_in() || ! mpp_is_gallery_management() || ! bp_is_action_variable( 'delete-unpublished', 1 ) ) {
		return;
	}

	$gallery_id = absint( $_GET['gallery_id'] );

	if ( ! $gallery_id ) {
		return;
	}
	//verify nonce
	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'delete-unpublished' ) ) {
		//should we return or show error?
		return;
	}

	$referrer = mpp_get_gallery_edit_media_url( $gallery_id );


	if ( ! mpp_gallery_has_unpublished_media( $gallery_id ) ) {
		mpp_add_feedback( __( 'Nothing to hide.', 'mediapress' ), 'error' );
		mpp_redirect( $referrer );
	}

	//check if user has permission
	if ( ! mpp_user_can_publish_gallery_activity( $gallery_id ) ) {
		mpp_add_feedback( __( "You don't have sufficient permission.", 'mediapress' ), 'error' );
		mpp_redirect( $referrer );
	}


	mpp_gallery_delete_unpublished_media( $gallery_id );

	mpp_add_feedback( __( "Successfully hidden!", 'mediapress' ) );

	mpp_redirect( $referrer );
}

add_action( 'mpp_actions', 'mpp_action_hide_unpublished_media', 2 );
//if we are here, delete gallery and return back
//
//when a gallery is saved, let us do some magic
//it is called for backend as well as front end gallery creation


/**
 * When a gallery post type post is created from the dashboard, we force to make it look like the one created from front end
 * 
 * @param type $post_id
 * @param type $post
 * @param type $update
 * @return type
 */

/** Clanup actions */
function mpp_clean_gallery_cache( $gallery ) {

	if ( is_object( $gallery ) && is_a( $gallery, 'MPP_Gallery' ) ) {
		$gallery = $gallery->id;
	}

	mpp_delete_gallery_cache( $gallery );
}

add_action( 'mpp_gallery_deleted', 'mpp_clean_gallery_cache', 100 );

/**
 * Generate an activity when a new gallery is created
 * We will not generate any activity for profile galleries though.
 * 
 * @param type $gallery_id
 */
function mpp_action_new_gallery_activity( $gallery_id ) {
	//if the admin settings does not ask us to create new activity, 
	//Or it is explicitly restricted, do not proceed
	if ( ! mpp_is_auto_publish_to_activity_enabled( 'create_gallery' ) || apply_filters( 'mpp_do_not_record_create_gallery_activity', false ) ) {
		return;
	}

	$gallery = mpp_get_gallery( $gallery_id );
	$user_link = mpp_get_user_link( $gallery->user_id );

	$link = mpp_get_gallery_permalink( $gallery );

	mpp_gallery_record_activity( array(
		'gallery_id'	=> $gallery_id,
		'type'			=> 'create_gallery',
		'content'		=> '',
		'action'		=> sprintf( __( '%s created a %s <a href="%s">gallery</a>', 'mediapress' ), $user_link, $gallery->type, $link ),
	) );

	//record gallery creation as activity, there will be issue with wall gallery though
}

add_action( 'mpp_gallery_created', 'mpp_action_new_gallery_activity' );
