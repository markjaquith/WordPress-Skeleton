<?php

class MPP_Groups_Gallery_Template_Loader extends MPP_Gallery_Template_Loader {

	private static $instance = null;

	protected function __construct() {

		parent::__construct();

		$this->id = 'default';
		$this->path = 'buddypress/groups/';
	}

	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function load_template() {
		
		$template = $this->path . 'home.php';
		$template = apply_filters( 'mpp_get_groups_gallery_template', $template );

		mpp_get_template( $template );
	}

	//callbacks
}
