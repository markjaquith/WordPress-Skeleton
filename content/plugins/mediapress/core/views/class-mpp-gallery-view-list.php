<?php

/**
 * Shows all the items as a list
 * 
 */
class MPP_Gallery_View_List extends MPP_Gallery_View {

	private static $instance = null;

	protected function __construct() {
		
		parent::__construct();
		
		$this->id = 'list';
		$this->name = __( 'List View', 'mediapress' );
	}

	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function display( $gallery ) {

		mpp_get_template( 'gallery/views/list.php' );
	}

	public function activity_display( $media_ids = array() ) {

		if ( ! $media_ids ) {
			return;
		}

		$media = $media_ids[0];

		$media = mpp_get_media( $media );

		if ( ! $media ) {
			return;
		}

		$type = $media->type;

		//we will use include to load found template file, the file will have $media_ids available 
		$templates = array(
			"buddypress/activity/views/list-{$type}.php", //list-audio.php etc 
			'buddypress/activity/views/list.php',
		);
		
		$located_template = mpp_locate_template( $templates, false );

		include $located_template;
	}

}
