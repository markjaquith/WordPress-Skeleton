<?php
//No direct access to the file 
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Add various upload icons to activity post form
 * @return type
 */
function mpp_activity_upload_buttons() {
	
	$component = mpp_get_current_component();
	
	if ( ! mpp_is_activity_upload_enabled( $component ) ) {
		return;
	}

	//if we are here, the gallery activity stream upload is enabled,
	//let us see if we are on user profile and gallery is enabled
	if ( ! mpp_is_enabled( $component, mpp_get_current_component_id() ) ) {
		return;
	}
	//if we are on group page and either the group component is not enabled or gallery is not enabled for current group, do not show the icons
	if ( function_exists( 'bp_is_group' ) && bp_is_group() && ( ! mpp_is_active_component( 'groups' ) || ! ( function_exists( 'mpp_group_is_gallery_enabled' ) && mpp_group_is_gallery_enabled() ) ) ) {
		return;
	}
	//for now, avoid showing it on single gallery/media activity stream
	if ( mpp_is_single_gallery() || mpp_is_single_media() ) {
		return;
	}
	
	?>
	<div id="mpp-activity-upload-buttons" class="mpp-upload-buttons">
	<?php do_action( "mpp_before_activity_upload_buttons" ); //allow to add more type  ?>

		<?php if ( mpp_is_active_type( 'photo' ) && mpp_component_supports_type( $component, 'photo' ) ): ?>
			<a href="#" id="mpp-photo-upload" data-media-type="photo"><img src="<?php echo mediapress()->get_url() . 'assets/images/media-button-image.gif' ?>"/></a>
		<?php endif; ?>

		<?php if ( mpp_is_active_type( 'audio' ) && mpp_component_supports_type( $component, 'audio' ) ): ?>
			<a href="#" id="mpp-audio-upload" data-media-type="audio"><img src="<?php echo mediapress()->get_url() . 'assets/images/media-button-music.gif' ?>"/></a>
		<?php endif; ?>

		<?php if ( mpp_is_active_type( 'video' ) && mpp_component_supports_type( $component, 'video' ) ): ?>
			<a href="#" id="mpp-video-upload"  data-media-type="video"><img src="<?php echo mediapress()->get_url() . 'assets/images/media-button-video.gif' ?>"/></a>
		<?php endif; ?>

		<?php if ( mpp_is_active_type( 'doc' ) && mpp_component_supports_type( $component, 'doc' ) ): ?>
			<a href="#" id="mpp-doc-upload"  data-media-type="doc"><img src="<?php echo mediapress()->get_url() . 'assets/images/media-button-doc.png' ?>" /></a>
		<?php endif; ?>

		<?php //someone please provide me doc icon and some better icons  ?> 

		<?php do_action( 'mpp_after_activity_upload_buttons' ); //allow to add more type  ?>

	</div>
		<?php
	}

//activity filter
	add_action( 'bp_after_activity_post_form', 'mpp_activity_upload_buttons' );

//add dropzone/feedback/uploaded media list for activity

	function mpp_activity_dropzone() {
		?>
	<!-- append uploaded media here -->
	<div id="mpp-uploaded-media-list-activity" class="mpp-uploading-media-list">
		<ul> </ul>
	</div>
	<?php do_action( 'mpp_after_activity_upload_medialist' ); ?>	
	<!-- drop files here for uploading -->
	<?php mpp_upload_dropzone( 'activity' ); ?>
	<?php do_action( 'mpp_after_activity_upload_dropzone' ); ?>
	<!-- show any feedback here -->
	<div id="mpp-upload-feedback-activity" class="mpp-feedback">
		<ul> </ul>
	</div>
	<?php do_action( 'mpp_after_activity_upload_feedback' ); ?>
	<?php
}

add_action( 'bp_after_activity_post_form', 'mpp_activity_dropzone' );

/**
 * Format activity action for 'mpp_media_upload' activity type.
 *
 * 
 * @param string $action  activity action.
 * @param object $activity Activity object.
 * @return string
 */
function mpp_format_activity_action_media_upload( $action, $activity ) {

	$userlink = mpp_get_user_link( $activity->user_id );

	$media_ids = array();
	$media_id = 0;

	$media_id = mpp_activity_get_media_id( $activity->id );

	if ( ! $media_id ) {

		$media_ids = mpp_activity_get_attached_media_ids( $activity->id );

		if ( ! empty( $media_ids ) ) {
			$media_id = $media_ids[0];
		}
	}

	$gallery_id = mpp_activity_get_gallery_id( $activity->id );

	if ( ! $media_id && ! $gallery_id ) {
		return $action; //not a gallery activity, no need to proceed further
	}

	$media = mpp_get_media( $media_id );
	$gallery = mpp_get_gallery( $gallery_id );

	if ( ! $media && ! $gallery ) {
		return $action;
	}

	$activity_type = mpp_activity_get_activity_type( $activity->id ); //is a type specified

	$skip = false;

	if ( $activity_type ) {
		if ( in_array( $activity_type, array( 'edit_gallery', 'add_media' ) ) ) {//'create_gallery',
			$skip = true;
		}
	}

	//there us still a chance for improvement, we should dynamically generate the action instead for the above actions too
	if ( $skip ) {
		return $action;
	}

	if ( $activity_type == 'media_upload' ) {

		$media_count = count( $media_ids );
		$media_id = current( $media_ids );

		$type = $gallery->type;

		//we need the type plural in case of mult
		$type = _n( $type, $type . 's', $media_count ); //photo vs photos etc

		$action = sprintf( __( '%s uploaded %d new %s', 'mediapress' ), $userlink, $media_count, $type );

		//allow modules to filter the action and change the message
		$action = apply_filters( 'mpp_activity_action_media_upload', $action, $activity, $media_id, $media_ids, $gallery );
	} elseif ( $activity_type == 'media_comment' ) {

		if ( mpp_is_single_media() ) {
			$action = sprintf( __( '%s', 'mediapress' ), $userlink );
		} else {
			$action = sprintf( __( "%s commented on %s's %s", 'mediapress' ), $userlink, mpp_get_user_link( $media->user_id ), $media->type ); //brajesh singh commented on @mercime's photo
		}
	} elseif ( $activity_type == 'gallery_comment' ) {

		if ( mpp_is_single_gallery() ) {
			$action = sprintf( '%s', $userlink );
		} else {
			$action = sprintf( __( "%s commented on %s's <a href='%s'>%s gallery</a>", 'mediapress' ), $userlink, mpp_get_user_link( $gallery->user_id ), mpp_get_gallery_permalink( $gallery ), $gallery->type );
		}
	} elseif ( $activity_type == 'create_gallery' ) {

		$action = sprintf( __( '%s created a %s <a href="%s">gallery</a>', 'mediapress' ), $userlink, $gallery->type, mpp_get_gallery_permalink( $gallery ) );
	} else {

		$action = sprintf( __( '%s', 'mediapress' ), $userlink );
	}

	return apply_filters( 'mpp_format_activity_action_media_upload', $action, $activity, $media_id, $media_ids );
}
