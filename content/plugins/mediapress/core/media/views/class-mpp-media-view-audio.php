<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The benefit of using a view class is the control that it allows to change the view generation without worrying about template changes
 * 
 */
class MPP_Media_View_Audio extends MPP_Media_View {

	public function display( $media ) {

		mpp_get_template( 'gallery/media/views/audio.php' );
	}

}
