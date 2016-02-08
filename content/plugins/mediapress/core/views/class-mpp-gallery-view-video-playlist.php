<?php

/**
 * Default Grid View
 */
class MPP_Gallery_View_Video_Playlist extends MPP_Gallery_View {

	private static $instance = null;

	protected function __construct() {

		parent::__construct();

		$this->id = 'playlist';
		$this->name = __( 'Video Playlist', 'mediapress' );
	}

	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function display( $gallery ) {

		mpp_get_template( 'gallery/views/playlist-video.php' );
	}

	public function display_settings( $gallery ) {
		
	}

	public function activity_display( $media_ids = array() ) {

		if ( ! $media_ids ) {
			return;
		}
		//we will use include to load found template file, the file will have $media_ids available 
		$templates = array(
			'buddypress/activity/views/playlist-video.php'
		);

		$located_template = mpp_locate_template( $templates, false );
		
		if ( $located_template ) {
			include $located_template;
		}
	}

}
