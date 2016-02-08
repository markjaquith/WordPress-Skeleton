<?php

/**
 * Maps various deletion actions of WordPress to MediaPress in order to provide clear and consisten api for MediaPress plugins
 * 
 * It has become a little bit messy but we don't want to rewrite the whole thing. So using this calss allows us to
 *  latch on to various WordPress actions for deleting posts/attachments
 */
class MPP_Deletion_Actions_Mapper {
	
	private static $instance = null;
	/**
	 * array of items currently being worked on
	 *  
	 * @var array 
	 */
	private $items = array();


	private function __construct() {

		//before_delete_post is never called for attachment, so we are sure that we only need to worry about gallery
		add_action( 'before_delete_post', array( $this, 'map_before_delete_post_action' ) );

		add_action( 'delete_attachment', array( $this, 'map_before_delete_attachment_action' ) );

		//Every thing has been deleted but the post object is still available in cache
		//this action fires for both the post/attachment
		//be warned that in case of attachment, there is no action whcih comes after deleting media files
		//this action runs before WordPress deletes media

		add_action( 'deleted_post', array( $this, 'map_deleted_post_action' ) );

		add_filter( 'mpp_cleanup_single_media_on_delete', array( $this, 'check_for_single_delete_optimization' ), 10, 3 );
	}

	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Maps the before_delete_post action to mpp_before_gallery_delete action
	 * It also does the cleanup of attachments as wp_delete_post() does not delete attachments
	 * 
	 * @param int $gallery_id post id
	 * @return unknown
	 */
	public function map_before_delete_post_action( $gallery_id ) {

		//is this called or a valid gallery?
		if ( ! $gallery_id || ! mpp_is_valid_gallery( $gallery_id ) ) {
			return;
		}

		$gallery = mpp_get_gallery( $gallery_id );

		//we are certain that it is called for gallery
		//fire the MediaPress gallery delete action

		if ( $this->is_queued( $gallery_id ) ) {
			//this action is already being executed for the current gallery, no need to do that again
			return;
		}

		$this->add_item( $gallery_id, 'gallery' );

		do_action( 'mpp_before_gallery_delete', $gallery_id );

		//after that action, we delete all attachment

		global $wpdb;
		//1// delete all media
		$storage_manager = null;

		$media_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d", $gallery_id ) );

		//we need the storage manager to notify it that it do do any final cleanup after the gallery delete
		//should we keep a reference to the storage manager for each gallery? what will happen for a gallery that contains local/remote media?
		//for the time being we are not keeping a reference but this method is doing exactly the same and I am not sure which approach will be fbetter for futuer

		if ( ! empty( $media_ids ) ) {
			$mid = $media_ids[0];

			$storage_manager = mpp_get_storage_manager( $mid );
		}

		//delete all media
		foreach ( $media_ids as $media_id ) {

			wp_delete_attachment( $media_id ); //delete all media
		}

		if ( mediapress()->is_bp_active() ) {

			//delete all gallery activity
			mpp_gallery_delete_activity( $gallery_id );

			//delete all associated activity meta
			//mpp_gallery_delete_activity_meta( $gallery_id );
		}

		//Delete wall gallery meta
		mpp_delete_wall_gallery_id( array(
			'component'		=> $gallery->component,
			'component_id'	=> $gallery->component_id,
			'gallery_id'	=> $gallery->id,
			'media_type'	=> $gallery->type
		) );

		//do any final cleanup, deletegate to the storage manager 
		if ( $storage_manager ) {
			$storage_manager->delete_gallery( $gallery );
		}
		
		return;
	}

	/**
	 * Maps 'delete_attachment' to mpp_before_media_delete action
	 * Also, cleans up cover, decrements media count and deletes activities associated
	 * 
	 * @param type $media_id
	 * @return type
	 */
	public function map_before_delete_attachment_action( $media_id ) {

		if ( ! mpp_is_valid_media( $media_id ) ) {
			//the attachment is not media
			return;
		}
		//this is just a precaution in case some faulty plugin calls it again
		//if everything is normal, we don't need to check for this
		if ( $this->is_queued( $media_id ) ) {
			return; //
		}

		//push this media to teh queue
		$this->add_item( $media_id, 'media' );

		/**
		 * mpp_before_media_delete action fires before WordPress starts deleting an attachment which is a valid media( in MediaPress). 
		 * Any plugin utilizing this action can access the fully functional media object by using mpp_get_media() on the passed media id
		 *  
		 */
		do_action( 'mpp_before_media_delete', $media_id );

		$storage_manager = mpp_get_storage_manager( $media_id );
		$storage_manager->delete_media( $media_id );

		//delete if it is set as cover
		delete_metadata( 'post', null, '_mpp_cover_id', $media_id, true ); // delete all for any posts.
		delete_metadata( 'post', null, '_mpp_unpublished_media_id', $media_id, true ); // delete all for any posts.
		//
		//if media has cover, delete the cover
		$media = mpp_get_media( $media_id );

		$gallery_id = $media->gallery_id;

		if ( mpp_media_has_cover_image( $media_id ) ) {
			mpp_delete_media( mpp_get_media_cover_id( $media_id ) );
		}

		if ( apply_filters( 'mpp_cleanup_single_media_on_delete', true, $media_id, $gallery_id ) ) {
			mpp_gallery_decrement_media_count( $gallery_id );

			if ( mediapress()->is_bp_active() ) {
				//delete all activities related to this media
				//mpp_media_delete_activities( $media_id );
				mpp_delete_activity_for_single_published_media( $media_id );
				//delete all activity meta key where this media is associated

				mpp_media_delete_activity_meta( $media_id );
			}
		}

		return;
	}

	/**
	 * Maps the 'deleted_post' action to 'mpp_gallert_deleted' or 'mpp_media_deleted' action depending on
	 *  whether the post type is gallery or attachment
	 * 
	 * @param type $post_id
	 * @return type
	 */
	public function map_deleted_post_action( $post_id ) {

		if ( ! $this->is_queued( $post_id ) ) {
			//if it is a gallery or media delete the post id must be in our queue
			//if we are here, It is neither a gallery nor a media delete action
			return;
		}

		if ( $this->is_gallery( $post_id ) ) {
			$this->do_gallery_delete( $post_id );
		} elseif ( $this->is_media( $post_id ) ) {
			$this->do_media_deleted( $post_id );
		}

		//remove from our local queue
		$this->remove_item( $post_id );
	}

	/**
	 * This action is called when a Gallery is completely delete( all meta, taxonomy association and its chid moved )
	 * Please do know that WodPress does not delet attachment by default, we need to
	 *  
	 * @param type $gallery_id
	 */
	private function do_gallery_delete( $gallery_id ) {

		do_action( 'mpp_gallery_deleted', $gallery_id );

		//clear gallery cache
	}

	/**
	 * Fired when a Media is completely removed from database and all its associations lost
	 * Only thing remaining at this time is WordPress starts deleting the files after this action
	 * CAUTION: do not try to call mpp_get_media on this action, may give bad results
	 * 
	 * @param type $media_id
	 */
	private function do_media_deleted( $media_id ) {

		do_action( 'mpp_media_deleted', $media_id );


		//clear media cache
	}

	public function check_for_single_delete_optimization( $yes_or_no, $media_id, $gallery_id ) {
		//if we are already deleting the gallery, there is no need to worry about media cleanup
		if ( $this->is_queued( $gallery_id ) ) {
			return false;
		}

		return $yes_or_no;
	}

	/**
	 * Check if current item is gallery
	 * @param type $item_id
	 * @return boolean
	 */
	private function is_gallery( $item_id ) {

		if ( $this->get_item_type( $item_id ) == 'gallery' ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if given type is media
	 * 
	 * @param type $item_id
	 * @return boolean
	 */
	private function is_media( $item_id ) {

		if ( $this->get_item_type( $item_id ) == 'media' ) {
			return true;
		}

		return false;
	}

	/**
	 * Is this item in our queue?
	 * 
	 * @param type $item_id
	 * @return boolean
	 */
	private function is_queued( $item_id ) {

		if ( isset( $this->items['item_' . $item_id] ) ) {
			//have we already fired it? no need to do that again
			return true;
		}

		return false;
	}

	/**
	 * Get the type( media|gallery) for the given post id
	 * It checks on our current queue and if the item is found, reurns its type
	 * 
	 * @param type $item_id
	 * @return string ( media|gallery )
	 */
	private function get_item_type( $item_id ) {

		$key = 'item_' . $item_id;

		if ( isset( $this->items[ $key ] ) ) {
			return $this->items[ $key ];
		}

		return ''; //invalid item
	}

	/**
	 * Adds an item to the queue
	 * 
	 * @param type $item_id
	 * @param type $type
	 */
	private function add_item( $item_id, $type ) {
		
		$this->items[ 'item_' . $item_id ] = $type;
		
	}

	/**
	 * Removes an item from the queue
	 * 
	 * @param int $item_id (post id for gallery or media )
	 */
	private function remove_item( $item_id ) {

		unset( $this->items[ 'item_' . $item_id ] );
	}

}

MPP_Deletion_Actions_Mapper::get_instance();
