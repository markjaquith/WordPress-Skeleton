<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/******************************** Media storage Management *************************/

/**
 * Register a new storage manager
 * 
 * @param string $method storage method
 * @param MPP_Storage_Manager $object
 */
function mpp_register_storage_manager( $method, MPP_Storage_Manager $object ) {
    
    mediapress()->storage_managers[ $method ] = $object;
    
}
/**
 * Deregister a previously registerd storage method
 * 
 * @param type $method
 */
function mpp_deregister_storage_manager( $method ) {
    
    $mediapress =  mediapress();
    
    unset( $mediapress->storage_managers[ $method ] );
    
}

/**
 * Get all registerd storage managers
 * 
 * @return MPP_Storage_Manager[]
 */
function mpp_get_registered_storage_managers() {
    
    return mediapress()->storage_managers;
    
}
/**
 * Get storage manager for the given media or the default active storage manager
 * 
 * @param int $id Media ID
 * 
 * @return boolean|MPP_Storage_Manager
 */
function mpp_get_storage_manager( $id_or_method = false ) {
	
    if ( ! $id_or_method || $id_or_method && is_numeric( $id_or_method ) ) {
		$method = mpp_get_storage_method( $id_or_method );
	} else {
		$method = trim ( $id_or_method );
	}
	
    $adaptors = mpp_get_registered_storage_managers();
	
    if ( isset( $adaptors[ $method ] ) ) {
        return $adaptors[ $method ];
	}
    
    return false;//adaptor not found for this method, we might have thrown exception as weel
    
}
/**
 * 
 * @param type $id
 * @return string storage method ( local|s3 etc)
 */
function mpp_get_storage_method( $id = false ) {
    
	$type = '';

	if ( $id ) {
		$type = mpp_get_media_meta ( $id, '_mpp_storage_method', true );
	}

	if ( ! $type ) {
		$type = mpp_get_default_storage_method ();
	}

	return apply_filters( 'mpp_get_storage_method', $type, $id );
        
}
/**
 * Return default storage method
 * local|s3|ftp etc
 * @return string default storage method
 */
function mpp_get_default_storage_method() {
    
	return apply_filters( 'mpp_get_default_storage_method', mpp_get_option ( 'default_storage', 'local' ) );
}
/**
 * Get the upload context
 * 
 * @param string $context
 * @return type
 */
function mpp_get_upload_context( $media_id = null, $context = null ){
    
    $current_context = '';
    
	if ( $media_id ) {
        $current_context = mpp_get_media_meta ( $media_id, '_mpp_context', true );
	}
    //if the media upload context is not known, let us see if a default is given
    if ( ! $current_context && $context ) {
        $current_context = $context;
	}
	
	if ( ! $current_context ) {
        $current_context = 'profile';
	}
    
    return apply_filters( 'mpp_get_upload_context', $current_context, $media_id, $context  );
}
