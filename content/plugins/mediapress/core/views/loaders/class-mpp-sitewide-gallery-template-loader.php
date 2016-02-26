<?php

class MPP_Sitewide_Gallery_Template_Loader extends MPP_Gallery_Template_Loader {

	private static $instance = null;

	public function __construct() {

		parent::__construct();

		$this->id = 'default';
		$this->path = 'sitewide/';
	}

	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function load_template() {


		$template = $this->path . 'home.php';
		$template = apply_filters( 'mpp_get_sitewide_gallery_template', $template );

		mpp_get_template( $template );
	}

	//callbacks
}
