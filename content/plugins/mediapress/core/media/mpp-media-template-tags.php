<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * like have_posts() alternative for media loop
 * check if there are galleries
 * 
 * 
 * @return boolean
 */
function mpp_have_media() {

	$the_media_query = mediapress()->the_media_query;
	
	if ( $the_media_query ) {
		return $the_media_query->have_media();
	}
	
	return false;
}

/**
 * Fetch the current media
 * 
 * @return type
 */
function mpp_the_media() {

	return mediapress()->the_media_query->the_media();
	
}

/**
 * print media id
 * 
 * @param type $media
 */
function mpp_media_id( $media = false ) {
	
	echo mpp_get_media_id( $media );
	
}

/**
 * Get media id
 * @param int|object $media
 * @return int media id
 */
function mpp_get_media_id( $media = false ) {

	$media = mpp_get_media( $media );
	return apply_filters( 'mpp_get_media_id', $media->id );
	
}

function mpp_media_title( $media = false ) {

	echo mpp_get_media_title( $media );
	
}

/**
 * Get media title
 * @param type $media
 * @return string
 */
function mpp_get_media_title( $media = false ) {

	$media = mpp_get_media( $media );

	return apply_filters( 'mpp_get_media_title', $media->title, $media->id );
	
}

function mpp_media_src( $type = '', $media = null ) {

	echo mpp_get_media_src( $type, $media );
	
}

function mpp_get_media_src( $type = '', $media = null ) {

	$media = mpp_get_media( $media );
	//if media is not photo and the type specified is empty or not 'originial'., get cover
	if ( $media->type != 'photo' ) {

		if ( ! empty( $type ) && $type != 'original' ) {
			return mpp_get_media_cover_src( $type, $media->id );
		}
		
	}
	
	$storage_manager = mpp_get_storage_manager( $media->id );

	return $storage_manager->get_src( $type, $media->id );
	
}

function mpp_media_path( $type = '', $media = null ) {

	echo mpp_get_media_path( $type, $media );
	
}

function mpp_get_media_path( $type = '', $media = null ) {

	$media = mpp_get_media( $media );

	$storage_manager = mpp_get_storage_manager( $media->id );

	return $storage_manager->get_path( $type, $media->id );
	
}

/**
 *  Print media slug
 * @param type $media
 */
function mpp_media_slug( $media = false ) {
	
	echo mpp_get_media_slug( $media );
	
}

/**
 * Get media slug
 * @param type $media int|object
 * @return type
 */
function mpp_get_media_slug( $media = false ) {

	$media = mpp_get_media( $media );

	return apply_filters( 'mpp_get_media_slug', $media->slug, $media->id );
	
}

/**
 * To Generate the actual code for showing media
 * We will rewrite it with better api in future, currently, It acts as fallback
 * 
 * The goal of this function is to generate appropriate output for listing media based on media type
 * 
 * @param type $media
 */
function mpp_load_media_view( $media = null ) {

	$view = mpp_get_media_view( $media );

	if ( ! $view ) {
		printf( __( 'There are no view object registered to handle the display of the content of <strong> %s </strong> type', 'mediapress' ), $media->type );
	} else {
		$view->display( $media );
	}
}

function mpp_media_content( $media = null ) {

	mpp_load_media_view( $media );
	
}

/**
 * Print media description
 * 
 * @param type $media
 */
function mpp_media_description( $media = false ) {
	
	echo mpp_get_media_description( $media );
	
}

/**
 * Get media description
 * 
 * @param type $media
 * @return type
 */
function mpp_get_media_description( $media = false ) {

	$media = mpp_get_media( $media );

	return apply_filters( 'bp_get_media_description', stripslashes( $media->description ), $media->id );
	
}

/**
 * print the type of media
 * @param type $media
 */
function mpp_media_type( $media = false ) {

	echo mpp_get_media_type( $media );
	
}

/**
 * 
 * @param type $media
 * @return string media type (audio|video|photo etc)
 */
function mpp_get_media_type( $media = false ) {

	$media = mpp_get_media( $media );

	return apply_filters( 'mpp_get_media_type', $media->type, $media->id );
	
}

/**
 * Print Gallery status (private|public)
 * @param type $media
 */
function mpp_media_status( $media = false ) {

	echo mpp_get_media_status( $media );
	
}

/**
 * 
 * @param type $media
 * @return string Gallery status(public|private|friends only)
 */
function mpp_get_media_status( $media = false ) {

	$media = mpp_get_media( $media );

	return apply_filters( 'mpp_get_media_status', $media->status, $media->id );
	
}

/**
 * Print the date of creation for the media
 * 
 * @param type $media
 */
function mpp_media_date_created( $media = false ) {
	
	echo mpp_get_media_date_created( $media );
	
}

/**
 * Get the date this media was created
 * @param type $media
 * @return type 
 */
function mpp_get_media_date_created( $media = false ) {

	$media = mpp_get_media( $media );
	return apply_filters( 'mpp_get_media_date_created', date_i18n( get_option( 'date_format' ), $media->date_created ), $media->id );
	
}

/**
 * Print When was the last time media was updated
 * 
 * @param type $media
 */
function mpp_media_last_updated( $media = false ) {
	
	echo mpp_get_media_last_updated( $media );
	
}

/**
 * Get the date this media was last updated
 * 
 * @param type $media
 * @return type 
 */
function mpp_get_media_last_updated( $media = false ) {

	return apply_filters( 'mpp_get_media_date_updated', date_i18n( get_option( 'date_format' ), $media->date_updated ), $media->id );
	
}

/**
 * Print the user id of the person who created this media
 * 
 * @param type $media
 */
function mpp_media_creator_id( $media = false ) {
	
	echo mpp_get_media_creator_id( $media );
	
}

/**
 * Get the ID of the person who created this Gallery
 * @param type $media
 * @return type 
 */
function mpp_get_media_creator_id( $media = null ) {

	$media = mpp_get_media( $media );

	return apply_filters( 'mpp_get_media_creator_id', $media->user_id, $media->id );
	
}

/**
 * Print the css class list 
 * @param type $class
 * @param type $media
 */
function mpp_media_class( $class = '', $media = null ) {
	
	echo mpp_get_media_class( $class, $media );
	
}

/**
 * Get css class list fo the media 
 * @param type $class
 * @param type $media
 * @return type
 */
function mpp_get_media_class( $class = '', $media = null ) {

	$media = mpp_get_media( $media );
	
	$class_list = "mpp-item mpp-media mpp-media-{$media->type}";
	
	if ( mpp_is_single_media() ) {
		$class_list .=" mpp-item-single mpp-media-single mpp-media-single-{$media->type}";
	}
	
	return apply_filters( 'mpp_get_media_class', "{$class_list} {$class}" );
}

/**
 * Print the media anchor html attributes
 * 
 * @param array $args
 */
function mpp_media_html_attributes( $args = null ) {

	echo mpp_get_media_html_attributes( $args );
	
}

/**
 * Build the attributes(prop=val) for the media anchor elemnt
 * It may be useful in adding some extra attributes to the anchor
 * @param type $args
 * @return string
 */
function mpp_get_media_html_attributes( $args = null ) {

	$default = array(
		'class'				=> '',
		'id'				=> '',
		'title'				=> '',
		'data-mpp-context'	=> 'gallery',
		'media'				=> 0 //pass gallery id or media, not required inside a loop
	);

	$args = wp_parse_args( $args, $default );

	$media = mpp_get_media( $args['media'] );

	if ( ! $media ) {
		return '';
	}

	//if(! $args['id'] )
	//	$args['id'] = 'mpp-media-thumbnail-' . $gallery->id;

	$args['media'] = $media; //we will pass the media object to the filter too

	$args = (array) apply_filters( 'mpp_media_html_attributes_pre', $args );

	unset( $args['media'] );

	if ( empty( $args['title'] ) ) {
		$args['title'] = mpp_get_media_title( $media );
	}

	return mpp_get_html_attributes( $args ); //may be a filter in future here
}

/**
 * Print pagination
 */
function mpp_media_pagination() {
	
	echo mpp_get_media_pagination();
	
}

/**
 * Get the pagination text
 * @return string
 */
function mpp_get_media_pagination() {

	//check if the current gallery supports playlist. then do not show pagination
	if ( ! mediapress()->the_media_query || mpp_gallery_supports_playlist( mpp_get_gallery() ) ) {
		return;
	}

	return "<div class='mpp-paginator'>" . mediapress()->the_media_query->paginate() . "</div>";
}

/**
 * Show the pagination count like showing 1-10 of 20
 * 
 */
function mpp_media_pagination_count() {

	if ( ! mediapress()->the_media_query ) {
		return;
	}

	mediapress()->the_media_query->pagination_count();
}

function mpp_get_next_media_id( $media_id ) {

	if ( ! $media_id ) {
		return false;
	}

	$media = mpp_get_media( $media_id );

	$args = array(
		'component'		=> $media->component,
		'component_id'	=> $media->component_id,
		'object_id'		=> $media->id,
		'object_parent' => $media->gallery_id,
		'next'			=> true,
	);

	$prev_gallery_id = mpp_get_adjacent_object_id( $args, mpp_get_media_post_type() );

	return $prev_gallery_id;
	
}

function mpp_get_previous_media_id( $media_id ) {

	if ( ! $media_id ) {
		return false;
	}

	$media = mpp_get_media( $media_id );

	$args = array(
		'component'		=> $media->component,
		'component_id'	=> $media->component_id,
		'object_id'		=> $media->id,
		'object_parent' => $media->gallery_id,
		'next'			=> false,
	);

	$prev_gallery_id = mpp_get_adjacent_object_id( $args, mpp_get_media_post_type() );

	return $prev_gallery_id;
	
}

function mpp_get_adjacent_media_link( $format, $link, $media_id = null, $previous = false ) {

	if ( ! $media_id ) {
		$media_id = mpp_get_current_media_id();
	}
	
	if ( ! $previous ) {
		$next_media_id = mpp_get_next_media_id( $media_id );
	} else {
		$next_media_id = mpp_get_previous_media_id( $media_id );
	}
	
	if ( ! $next_media_id ) {
		return;
	}

	$media = mpp_get_media( $next_media_id );

	if ( empty( $media ) ) {
		return;
	}

	$title = mpp_get_media_title( $media );

	$css_class = $previous ? 'mpp-previous' : 'mpp-next'; //css class 

	if ( empty( $title ) ) {
		$title = $previous ? __( 'Previous', 'mediapress' ) : __( 'Next', 'mediapress' );
	}
	
	$date = mysql2date( get_option( 'date_format' ), $media->date_created );
	$rel = $previous ? 'prev' : 'next';

	$string = '<a href="' . mpp_get_media_permalink( $media ) . '" rel="' . $rel . '" class="' . $css_class . '">';
	$inlink = str_replace( '%title', $title, $link );
	$inlink = str_replace( '%date', $date, $inlink );
	$inlink = $string . $inlink . '</a>';

	$output = str_replace( '%link', $inlink, $format );

	return $output;
	
}

function mpp_next_media_link( $format = '%link &raquo;', $link = '%title', $media_id = null ) {

	echo mpp_get_adjacent_media_link( $format, $link, $media_id, false );
	
}

function mpp_previous_media_link( $format = '&laquo; %link ', $link = '%title', $media_id = null ) {

	echo mpp_get_adjacent_media_link( $format, $link, $media_id, true );
	
}

/**
 * Stats Related
 * 
 * must be used inside the media loop
 * 
 */

/**
 * print the total media count for the current query
 */
function mpp_total_media_count() {

	echo mpp_get_total_media_count();
	
}

/**
 * get the total no. of media in current query
 * 
 * @return int
 */
function mpp_get_total_media_count() {

	$found = 0;
	
	if ( mediapress()->the_media_query ) {
		$found = mediapress()->the_media_query->found_posts;
	}

	return apply_filters( 'mpp_get_total_media_count', $found );
	
}

/**
 * Total media count for user
 */
function mpp_total_media_count_for_member() {

	echo mpp_get_total_media_count_for_member();
	
}

/**
 * @todo update for actual count
 * @return type
 * @todo
 */
function mpp_get_total_media_count_for_member() {

	return apply_filters( 'mpp_get_total_media_count_for_member', mpp_get_total_media_for_user() );
	
}

/**
 * Other functions
 */

/**
 * Get The Single media ID
 * @global  $bp
 * @return Int 
 */
function mpp_get_current_media_id() {

	return mediapress()->current_media->id;
	
}

/**
 * Get current Media
 * @return MPP_Media|null 
 */
function mpp_get_current_media() {

	return mediapress()->current_media;
	
}

/**
 * Is it media directory?
 * @return type 
 * @todo handle the single media case for root media
 */
function mpp_is_media_directory() {

	$action = bp_current_action();
	
	if ( mpp_is_gallery_directory() && ! empty( $action ) ) {
		return true;
	}

	return false;
	
}

/**
 * Is Single Media
 * @global  $bp
 * @return type 
 */
function mpp_is_single_media() {

	if ( mediapress()->the_media_query && mediapress()->the_media_query->is_single() ) {
		return true;
	}

	return false;
}

function mpp_is_remote_media( $media = null ) {

	$media = mpp_get_media( $media );

	return apply_filters( 'mpp_get_media_is_remote', $media->is_remote );
	
}

/**
 * @todo update
 */
function mpp_no_media_message() {
//detect the type here

	$type_name = bp_action_variable( 0 );

	//$type_name = media_get_type_name_plural( $type );

	if ( ! empty( $type_name ) ) {
		$message = sprintf( __( 'There are no %s yet.', 'mediapress' ), strtolower( $type_name ) );
	} else {
		$message = __( 'There are no galleries yet.', 'mediapress' );
	}
	
	echo $message;
}

function mpp_media_action_links( $media = null ) {

	echo mpp_get_media_action_links( $media );
	
}

/**
 * Action links like view/edit/dele/upload to show on indiavidula media
 * 
 * @param type $media
 * @return type
 */
function mpp_get_media_action_links( $media = null ) {

	$links = array();

	$media = mpp_get_media( $media );
	//$links ['view'] = sprintf( '<a href="%1$s" title="view %2$s" class="mpp-view-media">%3$s</a>', mpp_get_media_permalink( $media ), esc_attr( $media->title ), __( 'view', 'mediapress' ) );
	//upload?

	if ( mpp_user_can_edit_media( $media->id ) ) {
		$links['edit'] = sprintf( '<a href="%s" alt="' . __( 'Edit %s', 'mediapress' ) . '">%s</a>', mpp_get_media_edit_url( $media ), mpp_get_media_title( $media ), __( 'edit', 'mediapress' ) );
	}
	//delete
	if ( mpp_user_can_delete_media( $media ) ) {
		$links['delete'] = sprintf( '<a href="%s" alt="' . __( 'delete %s', 'mediapress' ) . '" class="confirm mpp-confirm mpp-delete mpp-delete-media">%s</a>', mpp_get_media_delete_url( $media ), mpp_get_media_title( $media ), __( 'delete', 'mediapress' ) );
	}

	return apply_filters( 'mpp_media_actions_links', join( ' ', $links ), $links, $media );
	
}

/**
 * Get the column class to be assigned to the media grid
 * 
 * @param type $media
 * @return type
 */
function mpp_get_media_grid_column_class( $media = null ) {
	//we are using 1-24 col grid, where 3-24 repsesents 1/8th and so on
	$col = mpp_get_option( 'media_columns' );

	return mpp_get_grid_column_class( $col );
}
