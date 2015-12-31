<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 *
 * Function used to store the attachment IDs being uploaded into a $_SESSION variable.
 *
 * This should affect files being uploaded by the media manager popup on the front-end.
 * Becuase the post hasn't technically been created yet, when you insert an image or media, it doesn't automatically attach it to the post id.
 *
 * @since 0.8
 * @returns $data
 */

function ninja_forms_set_attachment_to_change( $data, $attachment_id ){
	if ( !isset ( $data['ninja_forms_upload_field'] ) OR !$data['ninja_forms_upload_field'] ) {
		if ( !isset( $_SESSION['ninja_forms_change_attachment'] ) OR !is_array( $_SESSION['ninja_forms_change_attachment'] ) ) {
			$_SESSION['ninja_forms_change_attachment'] = array();
		}
		$_SESSION['ninja_forms_change_attachment'][] = $attachment_id;
	}
	
	return $data;
}

add_filter( 'wp_update_attachment_metadata', 'ninja_forms_set_attachment_to_change', 10, 2 );

/*
 *
 * Function used to attach media uploads to the newly created post when a post is updated or created.
 *
 * @since 0.8
 * @returns void
 */

function ninja_forms_attach_media_uploads( $post_id ){
	if ( isset( $_SESSION['ninja_forms_change_attachment'] ) AND is_array( $_SESSION['ninja_forms_change_attachment'] ) ) {
		foreach ( $_SESSION['ninja_forms_change_attachment'] as $attachment_id ) {
			$post = get_post( $attachment_id, ARRAY_A );
			if ( is_array( $post ) ) {
				wp_update_post( array( 'ID' => $attachment_id, 'post_type' => 'attachment', 'post_parent' => $post_id ) );	
			}
		} 
		$_SESSION['ninja_forms_change_attachment'] = '';
	}
}

add_action( 'ninja_forms_create_post', 'ninja_forms_attach_media_uploads' );
add_action( 'ninja_forms_update_post', 'ninja_forms_attach_media_uploads' );