<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Comment Related functions
 */
/**
 * Abstraction for meta functions
 * Uses WordPress native functions to handle it
 * 
 * 
 */
/**
 * Get comment meta value 
 * 
 * @param int $comment_id
 * @param string $key
 * @param bool $single
 * @return mixed array if $single is false else the meta value
 */
function mpp_get_comment_meta( $comment_id, $key = '', $single = false ) {
    
    return get_comment_meta( $comment_id, $key, $single  ); 
}

/**
 * Add a comment meta
 * 
 * @param int $comment_id
 * @param string $meta_key
 * @param mixed $meta_value
 * @param boolean $unique
 * @return int|bool
 */
function mpp_add_comment_meta( $comment_id, $meta_key, $meta_value, $unique = false ) {
    
    return add_comment_meta( $comment_id, $meta_key, $meta_value, $unique );
}
/**
 * Update comment meta
 * 
 * @param int $comment_id
 * @param string $meta_key
 * @param mixed $meta_value
 * @param mixed $prev_value
 * @return bool true on success, false on failure
 */
function mpp_update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value = '' ) {
    
    return update_comment_meta( $comment_id, $meta_key, $meta_value, $prev_value ) ;
}
/**
 * Delete a comment meta
 * 
 * @param int $comment_id
 * @param string $meta_key
 * @param mixed $meta_value
 * @return boolean true on success false on failure
 */
function mpp_delete_comment_meta( $comment_id, $meta_key, $meta_value = '' ) {
    
    return delete_comment_meta( $comment_id, $meta_key, $meta_value );
}



/**
 * Get the associated activity ID for a WordPress Comment
 * 
 * @param type $comment_id
 * @return type
 */

function mpp_comment_get_associated_activity_id( $comment_id ) {
    
    return mpp_get_comment_meta( $comment_id, '_mpp_activity_id',  true );
    
}
/**
 * Update the associated Activity Id for a WordPress Comment
 * 
 * @param type $comment_id
 * @param type $activity_id
 * @return type
 */
function mpp_comment_update_associated_activity_id( $comment_id, $activity_id ) {
    
    return mpp_update_comment_meta( $comment_id, '_mpp_activity_id',  $activity_id );
    
}

/**
 * Delete Associated Activity Id for a WordPress Comment
 * 
 * @param type $comment_id
 * @return type
 */
function mpp_comment_delete_associated_activity_id( $comment_id ) {
    
    return mpp_delete_comment_meta( $comment_id, '_mpp_activity_id' );
    
}


/**
 * Get attached media ids for the comment
 *  
 * @param type $comment_id
 * @return array of media ids
 */
function mpp_comment_get_attached_media_ids( $comment_id ) {
    
    return mpp_get_comment_meta( $comment_id, '_mpp_attached_media_id', false );
    
}
/**
 * Update Attached list of media ids for a comment
 * 
 * @param int $comment_id
 * @param array $media_ids
 * @return array
 */
function mpp_comment_update_attached_media_ids( $comment_id, $media_ids ) {
	
   foreach( $media_ids as $media_id ) {
	   mpp_add_comment_meta( $comment_id, '_mpp_attached_media_id', $media_id );
   }
   return $media_ids;

}
/**
 * Delete Attached list of media ids for the comment
 * 
 */
function mpp_comment_delete_attached_media_ids( $comment_id ) {
   
    return mpp_delete_comment_meta( $comment_id, '_mpp_attached_media_id' );

}

/**
 * Adds a new comment to the database.
 * A copy of wp_new_comment()
 * @see wp_new_comment()
 */
function mpp_add_comment( $commentdata ) {
    //basic fields
    //set to default
	$user_id = get_current_user_id();
	
    if ( empty( $commentdata['comment_author'] ) ) {
        $commentdata['comment_author'] = mpp_get_user_display_name( $user_id );
	}
	
    if ( empty( $commentdata['comment_author_email'] ) ) {
       $commentdata['comment_author_email'] = mpp_get_user_email ( $user_id );
	}
	
    if ( empty( $commentdata['comment_author_url'] ) ) {
        $commentdata['comment_author_url'] = mpp_get_user_url( $user_id );
	}
	/**
	 * Filter a comment's data before it is sanitized and inserted into the database.
	 *
	 * @since 1.5.0
	 *
	 * @param array $commentdata Comment data.
	 */
	$commentdata = apply_filters( 'preprocess_comment', $commentdata );

	$commentdata['comment_post_ID'] = (int) $commentdata['post_id'];//media Id or Gallery ID
	
    if ( isset($commentdata['user_ID']) ) {
		$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
	} elseif ( isset($commentdata['user_id']) ) {
		$commentdata['user_id'] = (int) $commentdata['user_id'];
	}

	$commentdata['comment_parent'] = isset($commentdata['comment_parent']) ? absint($commentdata['comment_parent']) : 0;
	//$parent_status = ( 0 < $commentdata['comment_parent'] ) ? wp_get_comment_status($commentdata['comment_parent']) : '';
	$commentdata['comment_parent'] = $commentdata['comment_parent']>0 ? $commentdata['comment_parent'] : 0;

	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
	$commentdata['comment_agent']     = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ) : '';

	$commentdata['comment_date']     = current_time('mysql');
	$commentdata['comment_date_gmt'] = current_time('mysql', 1);

	$commentdata = wp_filter_comment( $commentdata );

	$commentdata['comment_approved'] = 1;//make approved by default wp_allow_comment($commentdata);

	$comment_ID = wp_insert_comment( $commentdata );

	do_action( 'mpp_comment_added', $comment_ID, $commentdata['comment_approved'] );

	

	return $comment_ID;
}
/**
 * 
 * @param type $comment
 * @return MPP_Comment
 */
function mpp_get_comment( $comment = null ) {
    
    $comment = get_comment( $comment );
    return mpp_comment_migrate( $comment );
}
/**
 * Get custom comment type
 * 
 * @return string
 */
function mpp_get_comment_type() {
	
	return 'mpp-comment';
}