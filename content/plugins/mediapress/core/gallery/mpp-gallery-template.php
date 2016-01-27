<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template specific hooks
 * Used to attach functionality to template
 */
//show the publish to activity on mediapress edit gallery page

function mpp_gallery_show_publish_gallery_activity_button() {
	
	if ( ! mediapress()->is_bp_active() ) {
		return;
	}
	
	$gallery_id = mpp_get_current_gallery_id();
	//if not a valid gallery id or no unpublished media exists, just don't show it
	if ( ! $gallery_id || ! mpp_gallery_has_unpublished_media( $gallery_id ) ) {
		return;
	}

	$gallery = mpp_get_gallery( $gallery_id );

	$unpublished_media = mpp_gallery_get_unpublished_media( $gallery_id );
	//unpublished media count
	$unpublished_media_count = count( $unpublished_media );

	$type = $gallery->type;

	$type_name = _n( $type, $type . 's', $unpublished_media_count );

	//if we are here, there are unpublished media
	?>
	<div id="mpp-unpublished-media-info">
		<p> <?php printf( __( 'You have %d %s not published to actvity.', 'mediapress' ), $unpublished_media_count, $type_name ); ?>
			<span class="mpp-gallery-publish-activity"><?php mpp_gallery_publish_activity_link( $gallery_id ); ?></span>
			<span class="mpp-gallery-unpublish-activity"><?php mpp_gallery_unpublished_media_delete_link( $gallery_id ); ?></span>
		</p>
	</div>

	<?php
}

add_action( 'mpp_before_bulkedit_media_form', 'mpp_gallery_show_publish_gallery_activity_button' );

//generate the dropzone

function mpp_upload_dropzone( $context ) {
	?>
	<div id="mpp-upload-dropzone-<?php echo $context; ?>" class="mpp-dropzone">
		<div class="mpp-drag-drop-inside">
			<p class="mpp-drag-drop-info"><?php _e( 'Drop files here', 'mediapress' ); ?></p>
			<p><?php _e( 'or', 'mediapress' ); ?></p>
			<p class="mpp-drag-drop-buttons"><input id="mpp-upload-media-button-<?php echo $context; ?>" type="button" class="button mpp-button-select-files" value="<?php _e( 'Select files', 'mediapress' ); ?>" />
			<p class="mpp-uploader-allowed-file-type-info"></p>	
		</div>
	</div>
	<?php wp_nonce_field( 'mpp-manage-gallery', '_mpp_manage_gallery_nonce' ); ?>
	<?php
}
