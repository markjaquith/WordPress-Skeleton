<?php
//all deprecated functions, is not loaded anymore
/**
 * Get an array of all the permissions available to the current user for the gallery
 * 
 * 
 * @param type $component_type
 * @param type $component_id
 * @param type $gallery_id
 * @return type
 * @deprecated 1.0beta1
 * @see mpp_get_accessible_statuses()
 * 
 */
function mpp_get_current_user_access_permissions ( $component_type = false, $component_id = false, $gallery_id = false ) {

	if ( ! $component_type ) {
		$component_type = mpp_get_current_component();
	}
	
	if ( ! $component_id ) {
		$component_id = mpp_get_current_component_id();
	}
	
	return apply_filters( "mpp_get_current_user_" . strtolower( $component_type ) . "_gallery_access", array( 'public' ), $component_id, $gallery_id );
}