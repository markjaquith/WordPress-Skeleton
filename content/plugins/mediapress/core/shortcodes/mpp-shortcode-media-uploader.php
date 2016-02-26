<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
//[mpp-uloader]
add_shortcode( 'mpp-uploader', 'mpp_shortcode_uploader' );

function mpp_shortcode_uploader( $atts = array(), $content = '' ) {
	
	$default = array(
		'gallery_id'	=> 0,
		'component'		=> mpp_get_current_component(),
		'component_id'	=> mpp_get_current_component_id(),
		'type'			=> '',
		'status'		=> mpp_get_default_status(),
		'view'			=> '',
		'selected'		=> 0,
		'label_empty'	=> __( 'Please select a gallery', 'mediapress' ),
		'show_error'	=> true,
	);
	
	$atts = shortcode_atts( $default, $atts );
	//dropdown list of galleries to sllow userselect one
	$view = 'list';
	
	if ( ! empty( $atts['gallery_id'] ) && is_numeric( $atts['gallery_id'] ) ) {
		$view = 'single';//single gallery uploader
		//override component and $component id
		$gallery = mpp_get_gallery( $atts['gallery_id'] );
		
		if ( ! $gallery ) {
			return __( 'Nonexistent gallery should not be used', 'mediapress' );
		}
		
		//reset
		$atts['component'] = $gallery->component;
		$atts['component_id'] = $gallery->component_id;
		$atts['type'] = $gallery->type;
	} 
	
	//the user must be able to upload to current component or galler
	$can_upload = false;
	
	
	if ( mpp_user_can_upload( $atts['component'], $atts['component_id'], $atts['gallery_id'] ) ) {
		$can_upload = true;
	}
	
	if ( ! $can_upload && $show_error ) {
		return __( 'Sorry, you are not allowed to upload here.', 'mediapress' );
	}
	
	//if we are here, the user can upload
	//we still have one issue, what if the user has not created any gallery and the admin intends to allow the user to upload to their created gallery
	
	$atts['context'] =  'shortcode'; //from where it is being uploaded, 
	
	$atts['view'] = $view;
	
	//passing the 2nd arg makes all these variables available to the loaded file
	mpp_get_template( 'shortcodes/uploader.php', $atts );
	
}