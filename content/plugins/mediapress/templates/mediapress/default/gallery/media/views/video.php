<?php
/**
 * Single video view
 * 
 */
$media = mpp_get_current_media();
	if( ! $media )
		return ;

	$args = array(
		'src'		=> mpp_get_media_src( '',  $media ),
		'poster'	=> mpp_get_media_src( 'thumbnail', $media ),

	);
	
	echo wp_video_shortcode( $args );