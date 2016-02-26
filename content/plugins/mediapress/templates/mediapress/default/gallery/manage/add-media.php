<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * Load Add Media form/New Upload form
 */
mpp_get_template( 'gallery/manage/upload-form.php' );