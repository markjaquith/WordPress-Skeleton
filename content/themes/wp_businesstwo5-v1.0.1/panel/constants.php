<?php

//
//
// The following constants are generated and should not be touched.
// Edit file /functions/constants.php instead.
//
//

get_template_part('functions/constants');

if(!defined('WP_THEME_URL'))		define('WP_THEME_URL', get_stylesheet_directory_uri());
if(!defined('CI_DOMAIN'))			define('CI_DOMAIN', 'ci_'.CI_THEME_NAME);
if(!defined('CI_SAMPLE_CONTENT'))	define('CI_SAMPLE_CONTENT', CI_DOMAIN.'_sample_content');
if(!defined('THEME_OPTIONS')) 		define('THEME_OPTIONS', CI_DOMAIN.'_theme_options');

// Get the version from the stylesheet. 
if( function_exists( 'wp_get_theme' ) ) {
	$theme_data = wp_get_theme( basename( get_template_directory()) );
	$version = $theme_data->Version;
} else {
	$theme_data = get_theme_data( get_template_directory() . '/style.css' );
	$version = $theme_data['Version'];
}
if(!defined('CI_THEME_VERSION')) 	define('CI_THEME_VERSION', $version);


?>
