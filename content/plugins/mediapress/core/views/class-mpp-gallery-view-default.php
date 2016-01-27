<?php

/**
 * Default Grid View
 */
class MPP_Gallery_View_Default extends MPP_Gallery_View {

	private static $instance = null;

	protected function __construct() {

		parent::__construct();
		
		$this->id = 'default';
		$this->name = __( 'Default Grid layout', 'mediapress' );
	}

	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function display( $gallery ) {

		$gallery = mpp_get_gallery( $gallery );

		$type = $gallery->type;

		$templates = array(
			"gallery/views/grid-{$type}.php", //grid-audio.php etc 
			'gallery/views/grid.php',
		);

		mpp_locate_template( $templates, true );
	}

	/**
	 * Default view for the emdia attached to activity
	 * 
	 * @param int[] $media_ids
	 * @return null
	 */
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
			"buddypress/activity/views/grid-{$type}.php", //loop-audio.php etc 
			'buddypress/activity/views/grid.php',
		);

		$located_template = mpp_locate_template( $templates, false );

		include $located_template;
	}

}
