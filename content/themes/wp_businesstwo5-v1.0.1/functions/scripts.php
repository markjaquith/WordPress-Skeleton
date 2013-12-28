<?php
//
// Uncomment one of the following two. Their functions are in panel/generic.php
//
add_action('wp_enqueue_scripts', 'ci_enqueue_modernizr', 1);
//add_action('wp_enqueue_scripts', 'ci_print_html5shim', 1);


// This function lives in panel/generic.php
add_action('wp_footer', 'ci_print_selectivizr', 100);



add_action('init', 'ci_register_theme_scripts');
if( !function_exists('ci_register_theme_scripts') ):
function ci_register_theme_scripts()
{
	//
	// Register all scripts here, both front-end and admin. 
	// There is no need to register them conditionally, as the enqueueing can be conditional.
	//

	$key = '';
	if(ci_setting('google_maps_api_key'))
	{
		$key='key=' . ci_setting('google_maps_api_key') . '&';
	}
	$google_url = "http://maps.googleapis.com/maps/api/js?" . $key . "sensor=false";
	wp_register_script('ci-google-maps-api-3', $google_url, array(), null, false);


	wp_register_script('jquery-superfish', get_child_or_parent_file_uri('/panel/scripts/superfish.js'), array('jquery'), false, false);
	wp_register_script('jquery-fitVids', get_child_or_parent_file_uri('/panel/scripts/jquery.fitvid-1.0.js'), array('jquery'), false, false);
	wp_register_script('equalHeights', get_child_or_parent_file_uri('/js/jquery.equalHeights.js'), array('jquery'), false, false);
	wp_register_script('isotope', get_child_or_parent_file_uri('/js/jquery.isotope.min.js'), array('jquery'), false, false);
	wp_register_script('smoothScroll', get_child_or_parent_file_uri('/js/jquery.smooth-scroll.min.js'), array('jquery'), false, false);

	wp_register_script('ci-front-scripts', get_child_or_parent_file_uri('/js/scripts.js'),
		array(
			'jquery',
			'jquery-superfish',
			'jquery-flexslider',
			'jquery-fitVids',
			'jquery-hoverIntent',
			'equalHeights',
			'isotope',
			'smoothScroll',
			'ci-google-maps-api-3'
		),
		CI_THEME_VERSION, true);

		
}
endif;


add_action('wp_enqueue_scripts', 'ci_enqueue_theme_scripts');
if( !function_exists('ci_enqueue_theme_scripts') ):
function ci_enqueue_theme_scripts()
{
	//
	// Enqueue all (or most) front-end scripts here.
	// They can be also enqueued from within template files.
	//	
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );


	//
	// Slider options export for ci-front-scripts
	//
	$params['slider_autoslide'] = ci_setting('slider_autoslide')=='enabled' ? true : false;
	$params['slider_effect'] = ci_setting('slider_effect');
	$params['slider_direction'] = ci_setting('slider_direction');
	$params['slider_duration'] = (string)ci_setting('slider_duration');
	$params['slider_speed'] = (string)ci_setting('slider_speed');

	//
	// Map options export for ci-front-scripts
	//
	$coords = explode(',', ci_setting('map_coords'));
	$params['map_zoom_level'] = ci_setting('map_zoom_level');
	$params['map_coords_lat'] = $coords[0];
	$params['map_coords_long'] = $coords[1];
	$params['map_tooltip'] = ci_setting('map_tooltip');

	wp_enqueue_script('ci-front-scripts');
	wp_localize_script('ci-front-scripts', 'ThemeOption', $params);

}
endif;


add_action('admin_enqueue_scripts','ci_enqueue_admin_theme_scripts');
if( !function_exists('ci_enqueue_admin_theme_scripts') ):
function ci_enqueue_admin_theme_scripts() 
{
	global $pagenow;
	global $post_type;
	//
	// Enqueue here scripts that are to be loaded on all admin pages.
	//

	if(is_admin() and $pagenow=='themes.php' and isset($_GET['page']) and $_GET['page']=='ci_panel.php')
	{
		//
		// Enqueue here scripts that are to be loaded only on CSSIgniter Settings panel.
		//

	}
}
endif;


?>
