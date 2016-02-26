<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//should we go with view or viewer?
abstract class MPP_Media_View {

	public abstract function display( $media );
}
