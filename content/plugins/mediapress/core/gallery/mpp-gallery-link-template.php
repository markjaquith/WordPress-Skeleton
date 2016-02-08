<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Link template tags
 */

/**
 * Get the base url for the component gallery home page
 * e.g http://site.com/members/user-name/gallery //without any trailing slash
 * 
 * @param type $component
 * @return type
 * 
 * @todo In future, avoid dependecy on BuddyPress
 * 
 */
function mpp_get_gallery_base_url( $component, $component_id ) {

	$base_url = '';

	if ( $component == 'members' ) {
		$base_url = mpp_get_user_url( $component_id ) . MPP_GALLERY_SLUG;
	} elseif ( $component == 'groups' && function_exists( 'bp_get_group_permalink' ) ) {
		$base_url = bp_get_group_permalink( new BP_Groups_Group( $component_id ) ) . MPP_GALLERY_SLUG;
	}
	//for admin new/edit gallery, specially new gallery
	if ( ! $base_url && ( empty( $component ) || empty( $component_id ) ) ) {
		$base_url = mpp_get_user_url( get_current_user_id() ) . MPP_GALLERY_SLUG;
	}

	return apply_filters( 'mpp_get_gallery_base_url', untrailingslashit( $base_url ), $component, $component_id );
}

/**
 * Display the permalink for the current gallery
 */
function mpp_gallery_permalink() {
	echo mpp_get_gallery_permalink();
}

/**
 *  Get the url/permalink of the current ga;llery in the loop or the given gallery
 * 
 * @param type $gallery
 * @return type
 */
function mpp_get_gallery_permalink( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );
	$permalink = get_permalink( $gallery->id );

	return apply_filters( 'mpp_get_gallery_permalink', $permalink, $gallery );
}

/**
 * Action URLs
 */

/**
 * print the  url of Create Gallery page for the given component, defaults to user
 * @param type $component
 */
function mpp_gallery_create_url( $component, $component_id ) {

	echo mpp_get_gallery_create_url( $component, $component_id );
}

/**
 * Get the url of the gallery creation page for the given component
 * 
 * @param type $component
 * @return type
 */
function mpp_get_gallery_create_url( $component, $component_id ) {

	$link = mpp_get_gallery_base_url( $component, $component_id ) . '/create?_wpnonce=' . wp_create_nonce( 'create-gallery' );

	return apply_filters( 'mpp_get_gallery_create_url', $link, $component );
}

/**
 * Default action for gallery management page
 * 
 * It is used to decide what should be shown on the main page of management
 * 
 * @param type $gallery
 * @return type
 */
function mpp_get_gallery_management_default_action( $gallery ) {

	return apply_filters( 'mpp_get_gallery_management_default_action', 'edit', $gallery );
}

/**
 * Print the url of the single gallery management page
 * 
 * @param type $gallery
 */
function mpp_gallery_management_base_url( $gallery = null ) {

	echo mpp_get_gallery_management_base_url( $gallery );
}

/**
 * Get the url for gallery management page
 * 
 * It is like http://site.com/xyz/single-gallery-permalink/manage/ [ single-gallary-permalink/manage/
 * @param type $gallery
 * @return type
 */
function mpp_get_gallery_management_base_url( $gallery = null ) {

	$gallery = mpp_get_gallery( $gallery );
	$link = untrailingslashit( mpp_get_gallery_permalink( $gallery ) ) . '/manage/';

	$link = apply_filters( 'mpp_get_gallery_management_base_url', $link, $gallery );

	return $link;
}

/**
 * Print the url for gallery management page
 * 
 * @param type $gallery
 */
function mpp_gallery_management_url( $gallery = null ) {

	echo mpp_get_gallery_management_url( $gallery );
}

/**
 * Get the url for gallery management page
 * 
 * @param type $gallery
 * @return type
 */
function mpp_get_gallery_management_url( $gallery = null, $action = null ) {

	$gallery = mpp_get_gallery( $gallery );

	if ( ! $action ) {
		$action = mpp_get_gallery_management_default_action( $gallery );
	}

	$link = mpp_get_gallery_management_base_url( $gallery ) . $action . '/?_wpnonce=' . wp_create_nonce( $action ) . '&gallery_id=' . $gallery->id;

	$link = apply_filters( 'mpp_get_gallery_management_url', $link, $action, $gallery );

	return $link;
}

/**
 * Print the url of the add media  sub page for the gallery management screen
 * 
 * @param type $gallery
 */
function mpp_gallery_add_media_url( $gallery = null ) {

	echo mpp_get_gallery_add_media_url( $gallery );
}

/**
 * Get the url of the add media  sub page for the gallery management page
 * 
 * @param type $gallery
 * @return type
 */
function mpp_get_gallery_add_media_url( $gallery = null ) {

	$link = mpp_get_gallery_management_url( $gallery, 'add' ); //add media

	$link = apply_filters( 'mpp_get_gallery_add_media_url', $link, $gallery );

	return $link;
}

/**
 * Print the url of the media reorder sub page for the gallery management screen
 * 
 * @param type $gallery
 */
function mpp_gallery_edit_media_url( $gallery = null ) {

	echo mpp_get_gallery_edit_media_url( $gallery );
}

/**
 * Get the url of the media reorder sub page for the gallery management page
 * 
 * @param type $gallery
 * @return type
 */
function mpp_get_gallery_edit_media_url( $gallery = null ) {

	$link = mpp_get_gallery_management_url( $gallery, 'edit' ); //edit mediaa

	$link = apply_filters( 'mpp_get_gallery_edit_media_url', $link, $gallery );

	return $link;
}

/**
 * Print the url of the media reorder sub page for the gallery management screen
 * 
 * @param type $gallery
 */
function mpp_gallery_reorder_media_url( $gallery = null ) {

	echo mpp_get_gallery_reorder_media_url( $gallery );
}

/**
 * Get the url of the media reorder sub page for the gallery management page
 * 
 * @param type $gallery
 * @return type
 */
function mpp_get_gallery_reorder_media_url( $gallery = null ) {

	$link = mpp_get_gallery_management_url( $gallery, 'reorder' );

	$link = apply_filters( 'mpp_get_gallery_reorder_media_url', $link, $gallery );

	return $link;
}

function mpp_gallery_settings_url( $gallery = null ) {

	echo mpp_get_gallery_settings_url( $gallery );
}

function mpp_get_gallery_settings_url( $gallery = null ) {

	//should we have some option to ask for confirmation or not
	//let us implement 2 step delete for now

	$link = mpp_get_gallery_management_url( $gallery, 'settings' );

	$link = apply_filters( 'mpp_get_gallery_settings_url', $link, $gallery );

	return $link;
}

function mpp_gallery_delete_url( $gallery = null ) {

	echo mpp_get_gallery_delete_url( $gallery );
}

function mpp_get_gallery_delete_url( $gallery = null ) {

	//should we have some option to ask for confirmation or not
	//let us implement 2 step delete for now
	$link = mpp_get_gallery_management_url( $gallery, 'delete' );

	$link = apply_filters( 'mpp_get_gallery_delete_url', $link, $gallery );

	return $link;
}

//cover tags

function mpp_gallery_cover_delete_url( $gallery = null ) {

	echo mpp_get_gallery_cover_delete_url( $gallery );
}

function mpp_get_gallery_cover_delete_url( $gallery = null ) {

	$link = mpp_get_gallery_management_url( $gallery, 'delete-cover' );

	$link = apply_filters( 'mpp_get_gallery_cover_delete_url', $link, $gallery );

	return $link;
}

function mpp_gallery_publish_activity_url( $gallery = null ) {

	echo mpp_gallery_get_publish_activity_url( $gallery );
}

function mpp_gallery_get_publish_activity_url( $gallery = null ) {

	$link = mpp_get_gallery_management_url( $gallery, 'publish' );

	$link = apply_filters( 'mpp_gallery_publish_activity_url', $link, $gallery );

	return $link;
}

function mpp_gallery_unpublished_media_delete_url( $gallery = null ) {

	echo mpp_gallery_get_unpublished_media_delete_url( $gallery );
}

function mpp_gallery_get_unpublished_media_delete_url( $gallery = null ) {

	$link = mpp_get_gallery_management_url( $gallery, 'delete-unpublished' );

	$link = apply_filters( 'mpp_gallery_unpublish_media_delete_url', $link, $gallery );

	return $link;
}

/**
 * Print publish to activity url for the given gallery
 *  
 * @param int $gallery_id
 * @param string $label
 */
function mpp_gallery_publish_activity_link( $gallery_id, $label = '' ) {
	echo mpp_gallery_get_publish_activity_link( $gallery_id, $label );
}

/**
 * Get the publish activity link for given gallery
 * 
 * @param type $gallery_id
 * @param type $label
 * @return string
 */
function mpp_gallery_get_publish_activity_link( $gallery_id, $label = '' ) {

	if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) || ! mpp_gallery_has_unpublished_media( $gallery_id ) || ! mpp_user_can_publish_gallery_activity( $gallery_id ) ) {
		return '';
	}
	//this gallery has unpublished media and the user can publish the media to activity
	if ( empty( $label ) ) {
		$label = _x( 'Publish', ' Publish to activity button label', 'mediapress' );
	}

	$title = __( 'Publish the to activity', 'mediapress' );

	$url = mpp_gallery_get_publish_activity_url( $gallery_id );

	return sprintf( "<a href='%s' title ='%s' class='button mpp-button mpp-action-button mpp-publish-to-activity-button'>%s</a>", $url, $title, $label );
}

function mpp_gallery_unpublished_media_delete_link( $gallery_id, $label = '' ) {

	echo mpp_gallery_get_unpublished_media_delete_link( $gallery_id, $label );
}

function mpp_gallery_get_unpublished_media_delete_link( $gallery_id, $label = '' ) {

	if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) || ! mpp_gallery_has_unpublished_media( $gallery_id ) || ! mpp_user_can_publish_gallery_activity( $gallery_id ) ) {
		return '';
	}
	//this gallery has unpublished media and the user can publish the media to activity
	if ( empty( $label ) ) {
		$label = _x( 'Hide', 'Clear unpublished media notification', 'mediapress' );
	}

	$title = __( 'Clear unpublished media notification', 'mediapress' );

	$url = mpp_gallery_get_unpublished_media_delete_url( $gallery_id );

	return sprintf( "<a href='%s' title ='%s' class='button mpp-button mpp-action-button mpp-delete-unpublished-media-button'>%s</a>", $url, $title, $label );
}

function mpp_gallery_create_form_action() {

	echo mpp_get_gallery_base_url( mpp_get_current_component(), mpp_get_current_component_id() ) . '/create';
}

/**
 * Display a create Gallery Button
 * 
 * @return string
 */
function mpp_gallery_create_button() {

	//check whether to display the link or not
	$component = mpp_get_current_component();
	$component_id = mpp_get_current_component_id();

	if ( ! mpp_user_can_create_gallery( $component, $component_id ) ) {
		return false;
	}
	
	?>
	<a id="add_new_gallery_link" href="<?php mpp_gallery_create_url( $component, $component_id ); ?>">Add Gallery</a>
	<?php
}
