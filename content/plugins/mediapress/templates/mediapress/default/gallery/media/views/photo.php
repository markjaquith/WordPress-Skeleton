<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * 
 * Single photo view
 * 
 */
$media = mpp_get_current_media();
?>
<img src="<?php mpp_media_src( '', $media ) ;?>" alt="<?php mpp_media_title( $media ); ?>" class="mpp-large"/>