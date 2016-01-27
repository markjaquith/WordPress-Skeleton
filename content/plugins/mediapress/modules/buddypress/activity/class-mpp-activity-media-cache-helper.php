<?php

/**
 * Cache the Media/Galleries when activity is being fetched
 */
class MPP_Activity_Media_Cache_Helper {

	public function __construct() {

		$this->setup_hooks();
	}

	public function setup_hooks() {

		add_filter( 'bp_activity_prefetch_object_data', array( $this, 'cache' ) );
	}

	/**
	 * Since we are filtering on 'bp_activity_prefetch_object_data', the activity meta is already cached,
	 * So, we won't query for media ids instead loop and build the list
	 * 
	 * @param type $activities
	 */
	public function cache( $activities ) {

		if ( empty( $activities ) ) {
			return;
		}

		$media_ids = array();
		$gallery_ids = array();

		foreach ( $activities as $activity ) {
			//check if the activity has attached gallery
			$gallery_id = mpp_activity_get_gallery_id( $activity->id );

			if ( $gallery_id ) {
				$gallery_ids[] = $gallery_id;
			}

			//check for media ids

			$attached_media_ids = mpp_activity_get_attached_media_ids( $activity->id );

			if ( ! empty( $attached_media_ids ) ) {

				$media_ids = array_merge( $media_ids, $attached_media_ids );
			}

			$associated_media_id = mpp_activity_get_media_id( $activity->id );

			if ( ! empty( $associated_media_id ) ) {
				$media_ids[] = $associated_media_id;
			}
		}

		$merged_ids = array_merge( $media_ids, $gallery_ids );

		$merged_ids = array_unique( $merged_ids );

		if ( ! empty( $merged_ids ) ) {
			_prime_post_caches( $merged_ids, true, true );
		}

		return $activities;
	}

}

//prefetch activity associated gallery/media data
new MPP_Activity_Media_Cache_Helper();
