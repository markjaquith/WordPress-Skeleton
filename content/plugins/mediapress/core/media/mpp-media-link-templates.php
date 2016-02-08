<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Permanent link to a media
 * 
 * @param type $media
 */
function mpp_media_permalink( $media = null ) {
	
	echo mpp_get_media_permalink( $media );
	
}

function mpp_get_media_permalink( $media = null ) {

	$media = mpp_get_media( $media );

	$gallery_permalink = untrailingslashit( mpp_get_gallery_permalink( $media->gallery_id ) );

	if ( $media->component == 'sitewide' ) {
		$gallery_permalink .='/media';
	}

	return apply_filters( 'mpp_get_media_permalink', $gallery_permalink . '/' . mpp_get_media_slug( $media ) );
	
}

/**
 * For the sake of consistency, we use permalink/url as same
 * 
 * @param type $media
 */
function mpp_media_url( $media = null ) {
	
	echo mpp_get_media_url( $media );
	
}

function mpp_get_media_url( $media = null ) {
	
	return mpp_get_media_permalink( $media );
	
}

/**
 * Print Edit Media URL
 * 
 * @param type $media
 */
function mpp_media_edit_url( $media = null ) {
	
	echo mpp_get_media_edit_url( $media );
	
}

/**
 * Get the Edit media URL
 * 
 * @param type $media
 */
function mpp_get_media_edit_url( $media = null ) {//change
	
	$permalink = mpp_get_media_permalink( $media );

	return $permalink . '/edit/';
	
}

/**
 * Print delete media url
 * 
 * @param type $media
 */
function mpp_media_delete_url( $media = null ) {
	
	echo mpp_get_media_delete_url( $media );
	
}

/**
 * Get Media delete url
 * 
 * @param type $media
 * @return string
 */
function mpp_get_media_delete_url( $media = null ) {

	$media = mpp_get_media( $media );

	$link = mpp_get_media_edit_url( $media ) . 'delete/?mpp-action=delete-media&mpp-nonce=' . wp_create_nonce( 'mpp-delete-media' ) . '&mpp-media-id=' . $media->id;

	return $link; //needs improvenemt
	
}

function mpp_media_cover_delete_url( $media = null ) {

	echo mpp_get_media_cover_delete_url( $media );
	
}

function mpp_get_media_cover_delete_url( $media = null ) {

	$link = mpp_get_media_edit_url( $media ) . '?_wpnonce=' . wp_create_nonce( 'cover-delete' ) . '&mpp-action=cover-delete&media_id=' . $media->id;

	$link = apply_filters( 'mpp_get_media_cover_delete_url', $link, $media );

	return $link;
}
