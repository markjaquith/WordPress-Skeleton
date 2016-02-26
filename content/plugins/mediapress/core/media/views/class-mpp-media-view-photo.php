<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPP_Media_View_Photo extends MPP_Media_View {

	public function display( $media ) {

		mpp_get_template( 'gallery/media/views/photo.php' );
		
	}

}
