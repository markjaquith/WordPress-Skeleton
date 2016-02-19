<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get template part (for templates like the media-loop).
 *
 * @param string $slug
 * @param string $name (default: '')
 * @param string $fallback_path Fallback template directory base
 *
 * 	@since 1.0.1
 *	Used by plugins to supply a default fallback path for the current template file
 *  
 * @return void
 */
function mpp_get_template_part( $slug, $name = '', $fallback_path = '' ) {

	$template = '';
	
	//if fallback path is not given fallback to mediapress plugin
	if ( ! $fallback_path ) {
		$fallback_path = mediapress()->get_path() . 'templates/' . mpp_get_template_dir_name();
	}
	
	$fallback_path = untrailingslashit( $fallback_path );

	// Look in yourtheme/mediapress/slug-name.php 
	if ( $name ) {
		$template = locate_template( array( mpp_get_template_dir_name() . "/{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( $fallback_path . "/{$slug}-{$name}.php" ) ) {
		$template = $fallback_path . "/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/mediapress/slug.php
	if ( ! $template ) {
		$template = locate_template( array( mpp_get_template_dir_name() . "/{$slug}.php" ) );
	}
	
	if ( ! $template ) {
		$template = $fallback_path . "/{$slug}.php";
	}
	
	$template = apply_filters( 'mpp_get_template_part', $template, $slug, $name, $fallback_path );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates 
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array()) Use it to pass variables to the local scope of the included file if you need
 * @param string $default_path (default: '')
 * @return void
 */
function mpp_get_template( $template_name, $args = array(),  $default_path = '' ) {
	
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = mpp_locate_template( array( $template_name ), false, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_name ), '1.0' );
		return;
	}

	do_action( 'mpp_before_template_part', $template_name, $located, $args );

	include( $located );

	do_action( 'mpp_after_template_part', $template_name, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * 		yourtheme		/	mediapress	/	$template_name
 * 		
 * 		$default_path	/	mediapress /
 *
 * @param string $template_name
 * @param string $default_path (default: '')
 * @return string
 */
function mpp_locate_template( $template_names, $load = false,  $default_path = '' ) {

	//mediapress included plugin template path
	if ( ! $default_path ) {
		$default_path = mediapress()->get_path() . 'templates/' . mpp_get_template_dir_name();
	}

	$default_path = untrailingslashit( $default_path );
	
	$located = '';

	$template_names = array_filter( $template_names ); //remove any empty entry
		
	//now the array looks like mediapress/gallery/x.php
	
	$base_dir = mpp_get_template_dir_name();
	
	foreach ( (array) $template_names as $template_name ) {
		
		if ( ! $template_name ) {
			continue;
		}
		
		if ( file_exists( STYLESHEETPATH . '/' . $base_dir . '/' . $template_name ) ) {
			$located = STYLESHEETPATH . '/' . $base_dir . '/' . $template_name;
			break;
		} elseif ( file_exists( TEMPLATEPATH . '/' . $base_dir . '/' . $template_name ) ) {
			$located = TEMPLATEPATH . '/' . $base_dir . '/' . $template_name;
			break;
		} elseif ( file_exists( $default_path . '/' . $template_name ) ) {

			$located = $default_path . '/' . $template_name;
			break;
		}
	}


	if ( $load && '' != $located ) {
		load_template( $located, false );
	}
	
	// Return what we found
	return apply_filters( 'mpp_locate_template', $located, $template_names, $default_path );
}


/**
 * Get the name of directory which will be used by MediaPress to check for the existance of template files
 * It is a relative directory path from the base directory
 * 
 * @return string
 */
function mpp_get_template_dir_name() {

	return apply_filters( 'mmp_get_template_dir_name', 'mediapress/default' );
}

//relative path to the current /mediapress template folre
//e.g images/xyz.png converts to http://pathtotemplateofmediapress/mediapress/images/xyz.png
//another thought says it will be a bad decision to use file system check as they are slow, now not sure if I should proceed with this approach or not
function mpp_get_asset_url( $rel_path, $key ) {


	$url = mediapress()->get_asset( $key );

	//check if it exists in users template folder
	if ( ! $url ) {
		$template_dir = mpp_get_template_dir_name(); //'mediapress'
		$url = '';

		if ( file_exists( STYLESHEETPATH . '/' . $template_dir . '/' . $rel_path ) ) {
			$url = get_stylesheet_directory_uri() . '/' . $template_dir . '/' . $rel_path;
		} elseif ( file_exists( TEMPLATEPATH . '/' . $template_dir . '/' . $rel_path ) ) {
			$url = get_template_directory_uri() . '/' . $template_dir . '/' . $rel_path;
		}

		if ( ! $url ) {
			$url = mediapress()->get_url() . 'templates/' . $template_dir . '/' . $rel_path; //assume that the asset exists in our default template
		}
		
//upadet in mpp asset cache
		mediapress()->add_asset( $key, $url );
	}


	return apply_filters( 'mpp_get_asset_url', $url, $rel_path );
}

function mpp_get_single_media_template( $media = null ) {

	$templates = array();
	
	if ( ! $media ) {
		$media = mpp_get_current_media();
	}

	$loader = mpp_get_component_template_loader( $media->component );
	$path = $loader->get_path();

	$type = $media->type;
	$status = $media->status;

	$slug = 'media/single';

	$templates[] = $path . $slug . '-' . $type . '-' . $status . '.php'; //single-photo-public.php
	//$templates[] =  $path . $slug . '-' . $type . '-' . $status . '.php'; //single-photo-public.php
	$templates[] = $path . $slug . '-' . $type . '-' . $status . '.php'; //single-photo-public.php
	$templates[] = $path . $slug . '-' . $type . '.php'; //single-photo.php
	$templates[] = $path . $slug . '.php'; //single.php

	return $templates;
}

function mpp_get_single_gallery_template( $component = false ) {

	$templates = array();
	$gallery = mpp_get_current_gallery();

	$loader = mpp_get_component_template_loader( $gallery->component );
	$path = $loader->get_path();

	$type = $gallery->type;
	$status = $gallery->status;

	$slug = 'gallery/single';

	$templates[] = $path . $slug . '-' . $type . '-' . $status . '.php'; //single-photo-public.php
	$templates[] = $path . $slug . '-' . $type . '.php'; //single-photo.php
	$templates[] = $path . $slug . '.php'; //single.php

	return $templates;
}

/**
 * Use it to load appropriate view for gallery
 * 
 * @param type $gallery
 */
function mpp_load_gallery_view( $gallery ) {

	$view = mpp_get_gallery_view( $gallery );

	if ( ! $view ) {
		_e( 'Unable to display content. Needs a registered view.', 'mediapress' );

		return;
	}

	$view->display( $gallery );
}
