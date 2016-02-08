<?php

/**
 * Get the view associated with current gallery
 * 
 * @param int $gallery_id
 * @param string $default fallback view id
 * @return string view id
 */
function mpp_get_gallery_view_id( $gallery_id, $default = '' ) {

	$view_id = mpp_get_gallery_meta( $gallery_id, '_mpp_view', true );

	if ( ! $view_id ) {
		$view_id = 'default';
	}

	return $view_id;
}

/**
 * Set gallery view
 * 
 * @param type $gallery_id
 * @param type $view_id
 * @return type
 */
function mpp_update_gallery_view_id( $gallery_id, $view_id ) {

	return mpp_update_gallery_meta( $gallery_id, '_mpp_view', $view_id );
}

/**
 * Delete gallery view
 * 
 * @param type $gallery_id
 * @param type $view_id
 * @return type
 */
function mpp_delete_gallery_view_id( $gallery_id ) {

	return mpp_delete_gallery_meta( $gallery_id, '_mpp_view' );
}

/**
 * 
 * @param string $component
 * @return MPP_Gallery_Template_Loader
 */
function mpp_get_component_template_loader( $component ) {
	
	if ( ! class_exists( 'MPP_Gallery_View_Loader' ) ) {
		$path = mediapress()->get_path() . 'core/views/loaders/';

		require_once $path . 'class-mpp-gallery-view-loader.php';
		require_once $path . 'class-mpp-members-gallery-template-loader.php';
		require_once $path . 'class-mpp-groups-gallery-template-loader.php';
		require_once $path . 'class-mpp-sitewide-gallery-template-loader.php';
	}
	
	if ( $component == 'groups' ) {
		$loader = MPP_Groups_Gallery_Template_Loader::get_instance();
	} elseif ( $component == 'members' ) {
		$loader = MPP_Members_Gallery_Template_Loader::get_instance();
	} else {
		$loader = MPP_Sitewide_Gallery_Template_Loader::get_instance();
	}

	return $loader;
}

/**
 * 
 * Get all registered views
 * 
 * @param type $type
 * @return MPP_Gallery_View[] | boolean
 */
function mpp_get_registered_gallery_views( $type ) {

	if ( ! $type ) {
		return false;
	}

	$views = array();

	$mpp = mediapress();

	if ( isset( $mpp->gallery_views[ $type ] ) ) {
		$views = $mpp->gallery_views[ $type ];
	} else {
		//get the default view
		$views = (array) $mpp->gallery_views['default'];
	}

	return $views;
}

/**
 * @todo update
 * @param type $component
 * @return string
 */
function mpp_get_component_gallery_view( $component, $type ) {

	$key = "{$component}_{$type}_gallery_default_view";

	$view_id = mpp_get_option( $key, 'default' );

	return $view_id;
}

/**
 * Get activity view id
 * 
 * @param type $type
 * @return type
 */
function mpp_get_activity_view_id( $type ) {

	$key = "activity_{$type}_default_view";

	$view_id = mpp_get_option( $key, 'default' );

	return $view_id;
}

/**
 * 
 * @param type $type
 * @return boolean | MPP_Gallery_View
 */
function mpp_get_activity_view( $type ) {

	if ( ! $type ) {
		return false;
	}

	$view_id = mpp_get_activity_view_id( $type );

	//if view id is still not found, lets fallback to default
	if ( ! $view_id ) {
		$view_id = 'default';
	}

	// if we are here, we know the view_id and the type
	$mpp = mediapress();

	if ( isset( $mpp->gallery_views[ $type ][ $view_id ] ) ) {
		return $mpp->gallery_views[ $type ][ $view_id ];
	} else {
		$mpp->gallery_views[ $type ]['default'];
	}
}
