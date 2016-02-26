<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Theme compat for Directory pages
 * 
 */
/**
 * Handle the display of the mediapress directory index.
 */
function mpp_gallery_screen_directory() {
	
	if ( mpp_is_gallery_directory() ) {
		
		bp_update_is_directory( true, 'mediapress' );

		do_action( 'mpp_gallery_screen_directory' );

		bp_core_load_template( apply_filters( 'mpp_gallery_screen_directory', 'mediapress/directory/index-full' ) );
	}
}
add_action( 'mpp_screens', 'mpp_gallery_screen_directory', 1 );
/**
 * 
 * This class sets up the necessary theme compatability actions to safely output
 * registration template parts to the_title and the_content areas of a theme.
 *
 *
 */
class MPP_Directory_Theme_Compat {

	
	public function __construct() {
		
		add_action( 'bp_setup_theme_compat', array( $this, 'is_directory' ) );
	}

	/**
	 * Are we looking at Gallery or Media Directories?
	 *
	 */
	public function is_directory() {
		
		// Bail if not looking at the registration or activation page
		if ( ! mpp_is_gallery_directory() ) {
			return;
		}
		
		bp_set_theme_compat_active( true );
		
		buddypress()->theme_compat->use_with_current_theme = true;
		// Not a directory
		bp_update_is_directory( true, 'mediapress' );

		// Setup actions
		add_filter( 'bp_get_buddypress_template',                array( $this, 'template_hierarchy' ) );
		add_action( 'bp_template_include_reset_dummy_post_data', array( $this, 'dummy_post'    ) );
		add_filter( 'bp_replace_the_content',                    array( $this, 'directory_content' ) );
		
	}

	
	public function template_hierarchy( $templates = array() ) {
		

		// Setup our templates based on priority
		$new_templates = apply_filters( "mpp_template_hierarchy_directory", array(
			"mediapress/directory/index-full.php"
		) );

		// Merge new templates with existing stack
		// @see bp_get_theme_compat_templates()
		$templates = array_merge( (array) $new_templates, $templates );

		return $templates;
	}

	/**
	 * Update the global $post with dummy data
	 *
	 * @since BuddyPress (1.7)
	 */
	public function dummy_post() {
		// Registration page
		if ( mpp_is_gallery_directory() ) {
			$title = __( 'Gallery Directory', 'mediapress' );
		}
		
		bp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => bp_get_directory_title( 'mediapress' ),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => mpp_get_gallery_post_type(),
			'post_status'    => 'publish',
			'is_page'        => true,
			'comment_status' => 'closed'
		) );
	}

	/**
	 * Filter the_content with either the register or activate templates.
	 *
	 * @since BuddyPress (1.7)
	 */
	public function directory_content() {
		
		if ( mpp_is_gallery_component() ) {
			
			return bp_buffer_template_part( 'mediapress/default/buddypress/directory/index', null, false );
		}
		
	}
}

new MPP_Directory_Theme_Compat();

function mpp_add_bp_template_stack( $templates ) {
    // if we're on a page of our plugin and the theme is not BP Default, then we
    // add our path to the template path array
    if ( mpp_is_gallery_component() ) {
 
        $templates[] = mediapress()->get_path() . 'templates/';
    }

    return $templates;
}
 
add_filter( 'bp_get_template_stack', 'mpp_add_bp_template_stack', 10, 1 );


/**
 * Load the Page template for MediaPress Single Sitewide Gallery
 * Looks for mediapress/default/single-gallery-$type-$status.php
 *			 mediapress/default/single-gallery-$type.php
 *			 mediapress/default/single-gallery.php
 *			 single-mpp-gallery.php
 *			 singular.php
 *			 index.php
 * in the child theme, then parent theme and finally falls back to check in wp-content/mediapress/template/mediapress/default
 * We don't provide any default copy for this as we are not going to mess with the page layout. Still, a theme developer has the choice to do it their own way
 * 	 
 * Look at template_include hook and 
 * 
 * @see get_single_template()
 * @see get_query_template()
 * 
 * @param string $template absolute path to the template file
 * @return string absolute path to the template file
 */
function mpp_filter_single_template_for_sitewide_gallery( $template ) {
	//our sitewide gallery is not enabled or we are not on single sitewide gallery no need to bother
	if ( ! mpp_is_active_component( 'sitewide' ) || ! mpp_is_sitewide_gallery_component() ) {
		return $template;
	}
	
	$default_template = 'mediapress/default/sitewide/home.php';//modify it to use the current default template
	//load our template
	//should we load separate template for edit actions?
	$gallery = mpp_get_current_gallery();
	$media = mpp_get_current_media();
	
	$templates = array( $default_template );
	/*if( $media ) {
		
		$type = $media->type;
		$status = $media->status;
		$slug =  'single-media';
		//this is single media page
		
	} elseif( $gallery ) {
		//single gallery page
		$slug = 'single-gallery';
		$type = $gallery->type;
		$status = $gallery->status;
		
	}
	//look inside theme's mediapress/ directory
	$templates = $default_template . $slug . '-' . $type . '-' . $status . '.php';//single-gallery-photo-public.php/single-media-photo-public.php 
	$templates = $default_template . $slug . '-' . $type . '.php'; //single-gallery-photo.php/single-media-photo.php 
	$templates = $default_template . $slug . '.php'; //single-gallery.php/single-media.php 
	*/
	//we need to locate the template and if the template is not present in the themes, we need to setup theme compat
	
	$located = locate_template( $templates );
	
	if ( $located ) {
	//mediapress()->set_theme_compat( false );
		$template = $located;
	} else {
		//if not found, setup theme compat
		mpp_setup_sitewide_gallery_theme_compat();
	}
	
	return $template;
}
add_filter( 'single_template', 'mpp_filter_single_template_for_sitewide_gallery' );


function mpp_setup_sitewide_gallery_theme_compat() {
	
	add_action( 'loop_start', 'mpp_check_sitewide_gallery_main_loop' );
	//filter 'the_content' to show the galery thing
	add_filter( 'the_content', 'mpp_replace_the_content' );
	
}
/**
 * Replace the content of post with gallery
 * 
 * @staticvar boolean $_mpp_filter_applied
 * @param type $content
 * @return type
 */
function mpp_replace_the_content( $content = '' ) {

	static $_mpp_filter_applied;
	// Bail if not the main loop where theme compat is happening
	if ( ! mediapress()->is_using_theme_compat() || isset( $_mpp_filter_applied ) ) {
		return $content;
	}
	$_mpp_filter_applied = true;
	
	$new_content = apply_filters( 'mpp_replace_the_content', $content );

	// Juggle the content around and try to prevent unsightly comments
	if ( ! empty( $new_content ) && ( $new_content !== $content ) ) {

		// Set the content to be the new content
		$content = $new_content;

		// Clean up after ourselves
		unset( $new_content );

		// Reset the $post global
		//wp_reset_postdata();
	}

	mediapress()->set_theme_compat( false );
	
	return $content;
}

function mpp_check_sitewide_gallery_main_loop( $query ) {
	
	if ( $query->is_main_query() ) {
		mediapress()->set_theme_compat( true );
	} else {
		mediapress()->set_theme_compat( false );
	}
}

add_filter( 'mpp_replace_the_content', 'mpp_sitewide_gallery_theme_compat_content' );

function mpp_sitewide_gallery_theme_compat_content() {
 	
	ob_start();
		
	mpp_get_component_template_loader( 'sitewide' )->load_template();
	
	$content = ob_get_clean();
	
	return $content;
}