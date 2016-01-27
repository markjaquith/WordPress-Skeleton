<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter get_permalink for mediapress gallery post type( mpp-gallery)
 * aand make it like site.com/members/username/mediapressslug/gallery-name or site.com/{component-page}/{single-component}/mediapress-slug/gallery-name
 * It allows us to get the permalink to gallery by using the_permalink/get_permalink functions
 */
function mpp_filter_gallery_permalink( $permalink, $post, $leavename, $sample ) {

	//check if BuddyPress is active, if not, we don't filter it yet
	//lightweight check
	if ( ! mediapress()->is_bp_active() ) {
		return $permalink;
	}
	//a little more expensive
	if ( mpp_get_gallery_post_type() != $post->post_type ) {
		return $permalink;
	}

	//this is expensive if the post is not cached
	//If you see too many queries, just make sure to call _prime_post_caches($ids, true, true ); where $ids is collection of post ids
	//that will save a lot of query
	$gallery = mpp_get_gallery( $post );

	// do not modify permalinks for Sitewide gallery
	if ( $gallery->component == 'sitewide' ) {
		return $permalink;
	}

	$slug = $gallery->slug;

	$base_url = mpp_get_gallery_base_url( $gallery->component, $gallery->component_id );

	return apply_filters( 'mpp_get_gallery_permalink', $base_url . '/' . $slug, $gallery );
}

add_filter( 'post_type_link', 'mpp_filter_gallery_permalink', 10, 4 );

//for title
add_filter( 'mpp_get_gallery_title', 'wp_kses_post' );
add_filter( 'mpp_get_gallery_title', 'wptexturize' );
add_filter( 'mpp_get_gallery_title', 'convert_chars' );
add_filter( 'mpp_get_gallery_title', 'trim' );
//for content
add_filter( 'mpp_get_gallery_description', 'wp_kses_post' );
add_filter( 'mpp_get_gallery_description', 'wptexturize' );
add_filter( 'mpp_get_gallery_description', 'convert_smilies' );
add_filter( 'mpp_get_gallery_description', 'convert_chars' );
add_filter( 'mpp_get_gallery_description', 'wpautop' );
add_filter( 'mpp_get_gallery_description', 'make_clickable' );
