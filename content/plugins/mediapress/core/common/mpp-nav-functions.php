<?php

/**
 * Default menu item visibility check callback
 * 
 * @param type $item
 * @param type $gallery
 * @return type
 */
function mpp_is_menu_item_visible( $item, $gallery ) {

	$can_see = false;

	//if the current user is super admin or owner of the gallery, they can see everything
	if ( is_super_admin() || get_current_user_id() == $gallery->user_id ) {

		$can_see = true;
	}

	if ( ! $can_see ) {

		//check if action is protected, If it is not protected, anyone can see
		if ( !in_array( $item[ 'action' ], array( 'view', 'manage', 'edit', 'reorder', 'upload' ) ) ) {
			$can_see = true;
		}
	}
	//should we provide a filter here, I am sure people will misuse it

	return apply_filters( 'mpp_is_menu_item_visible', $can_see, $item, $gallery );
}

/**
 * Add a new menu item to the current gallery menu
 * 
 * @param type $args
 * @return type
 */
function mpp_add_gallery_nav_item( $args ) {

	return mediapress()->get_menu( 'gallery' )->add_item( $args );
}

/**
 * Remove a nav item from the current gallery nav
 * 
 * @param type $args
 * @return type
 */
function mpp_remove_gallery_nav_item( $args ) {

	return mediapress()->get_menu( 'gallery' )->remove_item( $args );
}

/**
 * Render gallery menu
 * 
 * @param type $gallery
 */
function mpp_gallery_admin_menu( $gallery, $selected =  '' ) {
	
	$gallery = mpp_get_gallery( $gallery );
	
	mediapress()->get_menu( 'gallery' )->render( $gallery, $selected );
}

/**
 * Add a new nav item in the media nav
 * 
 * @param type $args
 * @return type
 */
function mpp_add_media_nav_item( $args ) {

	return mediapress()->get_menu( 'media' )->add_item( $args );
}
/**
 * Remove a nav item from the media nav
 * 
 * @param type $args
 * @return type
 */
function mpp_remove_media_nav_item( $args ) {

	return mediapress()->get_menu( 'media' )->remove_item( $args );
}

/**
 * Render media admin tabs
 * 
 * @param type $media
 */
function mpp_media_menu( $media ) {

	$media = mpp_get_media( $media );
	mediapress()->get_menu( 'media' )->render( $media );
}
