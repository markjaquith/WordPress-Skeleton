<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * like have_posts() alternative for gallery loop
 * 
 * check if there are galleries available
 * 
 * 
 * @return boolean true if there are galleries available, else false
 */
function mpp_have_galleries() {

	$the_gallery_query = mediapress()->the_gallery_query;

	if ( $the_gallery_query ) {
		return $the_gallery_query->have_galleries();
	}

	return false;
}

/**
 * Fetch the current gallery
 * 
 * @return type
 */
function mpp_the_gallery() {

	return mediapress()->the_gallery_query->the_gallery();
}

/**
 * print gallery id
 * 
 * @param type $gallery
 */
function mpp_gallery_id( $gallery = false ) {

	echo mpp_get_gallery_id( $gallery );
}

/**
 * Get gallery id
 * 
 * @param int|object $gallery
 * @return int gallery id
 */
function mpp_get_gallery_id( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );
	return apply_filters( 'mpp_get_gallery_id', $gallery->id );
}

/**
 * Print gallery title
 * 
 * @param type $gallery
 */
function mpp_gallery_title( $gallery = false ) {

	echo mpp_get_gallery_title( $gallery );
}

/**
 * Get gallery title
 * 
 * @param type $gallery
 * @return string
 */
function mpp_get_gallery_title( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );

	return apply_filters( 'mpp_get_gallery_title', $gallery->title, $gallery->id );
}

/**
 * Print gallery slug
 * 
 * @param int|MPP_Gallery $gallery
 * 
 * @return string gallery slug(post slug)
 */
function mpp_gallery_slug( $gallery = false ) {

	echo mpp_get_gallery_slug( $gallery );
}

/**
 * Get gallery slug
 * 
 * @param type $gallery int|object
 * @return string
 */
function mpp_get_gallery_slug( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );

	return apply_filters( 'mpp_get_gallery_slug', $gallery->slug, $gallery->id );
}

/**
 * Print gallery description
 * 
 * @param type $gallery
 */
function mpp_gallery_description( $gallery = false ) {

	echo mpp_get_gallery_description( $gallery );
}

/**
 * Get gallery description
 * 
 * @param type $gallery
 * @return type
 */
function mpp_get_gallery_description( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );

	return apply_filters( 'mpp_get_gallery_description', stripslashes( $gallery->description ), $gallery->id );
}

/**
 * print the type of gallery
 * 
 * @param type $gallery
 */
function mpp_gallery_type( $gallery = false ) {

	echo mpp_get_gallery_type( $gallery );
}

/**
 * 
 * @param type $gallery
 * @return string gallery type (audio|video|photo etc)
 */
function mpp_get_gallery_type( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );

	return apply_filters( 'mpp_get_gallery_type', $gallery->type, $gallery->id );
}

/**
 * Print Gallery status (private|public etc)
 * 
 * @param type $gallery
 */
function mpp_gallery_status( $gallery = false ) {

	echo mpp_get_gallery_status( $gallery );
}

/**
 * Get gallery status
 * 
 * @param type $gallery
 * @return string Gallery status(public|private|friends only)
 */
function mpp_get_gallery_status( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );

	return apply_filters( 'mpp_get_gallery_status', $gallery->status, $gallery->id );
}

/**
 * Print the date of creation for the gallery
 * 
 * @param type $gallery
 */
function mpp_gallery_date_created( $gallery = false ) {

	echo mpp_get_gallery_date_created( $gallery );
}

/**
 * Get the date this gallery was created
 * @param type $gallery
 * @return type 
 */
function mpp_get_gallery_date_created( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );

	return apply_filters( 'mpp_get_gallery_date_created', date_i18n( get_option( 'date_format' ), $gallery->date_created ), $gallery->id );
}

/**
 * Print When was the last time gallery was updated
 * 
 * @param type $gallery
 */
function mpp_gallery_last_updated( $gallery = false ) {

	echo mpp_get_gallery_last_updated( $gallery );
}

/**
 * Get the date this gallery was last updated
 * 
 * @param type $gallery
 * @return type 
 */
function mpp_get_gallery_last_updated( $gallery = false ) {

	return apply_filters( 'mpp_get_gallery_date_updated', date_i18n( get_option( 'date_format' ), $gallery->date_updated ), $gallery->id );
}

/**
 * Print the user id of the person who created this gallery
 * 
 * @param type $gallery
 */
function mpp_gallery_creator_id( $gallery = false ) {

	echo mpp_get_gallery_creator_id( $gallery );
}

/**
 * Get the ID of the person who created this Gallery
 * @param type $gallery
 * @return type 
 */
function mpp_get_gallery_creator_id( $gallery = null ) {

	$gallery = mpp_get_gallery( $gallery );

	return apply_filters( 'mpp_get_gallery_creator_id', $gallery->user_id, $gallery->id );
}

/**
 * Print the css class for the gallery
 * 
 * @param type $class
 * @param type $media
 */
function mpp_gallery_class( $class = '', $gallery = null ) {

	echo mpp_get_gallery_class( $class, $gallery );
}

/**
 * Get css class list for the gallery
 *  
 * @param string $class
 * @param int|MPP_Gallery $gallery
 * @return type
 */
function mpp_get_gallery_class( $class = '', $gallery = null ) {

	$gallery = mpp_get_gallery( $gallery );

	return apply_filters( 'mpp_get_gallery_class', "mpp-item mpp-gallery mpp-gallery-{$gallery->type} $class" );
}

/**
 * Print the gallery anchor html attributes
 * 
 * @param array $args
 */
function mpp_gallery_html_attributes( $args = null ) {

	echo mpp_get_gallery_html_attributes( $args );
}

/**
 * Build the attributes(prop=val) for the gallery anchor elemnt
 * It may be useful in adding some extra attributes to the anchor
 * @param type $args
 * @return string
 */
function mpp_get_gallery_html_attributes( $args = null ) {

	$default = array(
		'class'				=> '',
		'id'				=> '',
		'title'				=> '',
		'data-mpp-context'	=> 'galery',
		'gallery'			=> 0 //pass gallery id or media, not required inside a loop
	);

	$args = wp_parse_args( $args, $default );

	$gallery = mpp_get_gallery( $args['gallery'] );

	if ( ! $gallery ) {
		return '';
	}

	//if(! $args['id'] )
	//	$args['id'] = 'mpp-gallery-thumbnail-' . $gallery->id;

	$args['gallery'] = $gallery; //we will pass teh gallery object to the filter too

	$args = (array) apply_filters( 'mpp_gallery_html_attributes_pre', $args );

	unset( $args['gallery'] );

	if ( empty( $args['title'] ) ) {
		$args['title'] = mpp_get_gallery_title( $gallery );
	}

	if ( ! isset( $args['data-mpp-gallery-id'] ) ) {
		$args['data-mpp-gallery-id'] = mpp_get_gallery_id( $gallery );
	}

	return mpp_get_html_attributes( $args ); //may be a filter in future here
}

/**
 * Print the current gallery loop pagination links
 * 
 */
function mpp_gallery_pagination() {

	echo mpp_get_gallery_pagination();
}

/**
 * Get the pagination links for the current loop
 * 
 * @return type
 */
function mpp_get_gallery_pagination() {

	if ( mediapress()->the_gallery_query ) {
		return mediapress()->the_gallery_query->paginate();
	}

	return '';
}

function mpp_get_next_gallery_id( $gallery_id ) {

	$gallery = mpp_get_gallery( $gallery_id );

	$args = array(
		'component'		=> $gallery->component,
		'component_id'	=> $gallery->component_id,
		'object_id'		=> $gallery->id,
		'next'			=> true,
	);

	$next_gallery_id = mpp_get_adjacent_object_id( $args, mpp_get_gallery_post_type() );

	return $next_gallery_id;
}

function mpp_get_previous_gallery_id( $gallery_id ) {

	if ( ! $gallery_id ) {
		return false;
	}

	$gallery = mpp_get_gallery( $gallery_id );

	$args = array(
		'component'		=> $gallery->component,
		'component_id'	=> $gallery->component_id,
		'object_id'		=> $gallery->id,
		'next'			=> false,
	);

	$prev_gallery_id = mpp_get_adjacent_object_id( $args, mpp_get_gallery_post_type() );

	return $prev_gallery_id;
}

function mpp_get_adjacent_gallery_link( $format, $link, $gallery_id = null, $previous = false ) {

	if ( ! $gallery_id ) {
		$gallery_id = mpp_get_current_gallery_id();
	}
	
	if ( ! $previous ) {
		$next_gallery_id = mpp_get_next_gallery_id( $gallery_id );
	} else {
		$next_gallery_id = mpp_get_previous_gallery_id( $gallery_id );
	}
	
	if ( ! $next_gallery_id ) {
		return;
	}
	
	$gallery = mpp_get_gallery( $next_gallery_id );

	if ( empty( $gallery ) ) {
		return;
	}

	$title = mpp_get_gallery_title( $gallery );

	if ( empty( $title ) ) {
		$title = $previous ? __( 'Previous', 'mediapress' ) : __( 'Next', 'mediapress' );
	}
	
	$date = mysql2date( get_option( 'date_format' ), $gallery->date_created );
	$rel = $previous ? 'prev' : 'next';

	$string = '<a href="' . mpp_get_gallery_permalink( $gallery ) . '" rel="' . $rel . '">';
	$inlink = str_replace( '%title', $title, $link );
	$inlink = str_replace( '%date', $date, $inlink );
	$inlink = $string . $inlink . '</a>';

	$output = str_replace( '%link', $inlink, $format );

	return $output;
}

/**
 * Print Next gallery link
 * 
 * @param type $format
 * @param type $link
 * @param type $gallery_id
 */
function mpp_next_gallery_link( $format = '%link &raquo;', $link = '%title', $gallery_id = null ) {

	echo mpp_get_adjacent_gallery_link( $format, $link, $gallery_id, false );
}

/**
 * Print Previous gallery link
 * 
 * @param type $format
 * @param type $link
 * @param type $gallery_id
 */
function mpp_previous_gallery_link( $format = '&laquo; %link ', $link = '%title', $gallery_id = null ) {

	echo mpp_get_adjacent_gallery_link( $format, $link, $gallery_id, true );
}

/**
 * Prints the pagination count text e.g. Viewing gallery 3 of 5 etc
 * 
 */
function mpp_gallery_pagination_count() {

	if ( ! mediapress()->the_gallery_query ) {
		return;
	}

	mediapress()->the_gallery_query->pagination_count();
}

/**
 * Get the total gallery count for the current query
 * 
 * Use inside the loop only
 */
function mpp_total_gallery_count() {

	echo mpp_get_total_gallery_count();
}

/**
 * Get total gallery count for the current query
 * 
 * Use inside the loop only
 * 
 * @return type
 */
function mpp_get_total_gallery_count() {

	$found = 0;
	
	if ( mediapress()->the_gallery_query ) {
		$found = mediapress()->the_gallery_query->found_posts;
	}

	return apply_filters( 'mpp_get_total_gallery_count', $found );
}

/**
 * Total Gallery count for user
 */
function mpp_total_gallery_count_for_member() {

	echo mpp_get_total_gallery_count_for_member();
}

//fix
/**
 * @todo update for actual count
 * 
 * @return type
 */
function mpp_get_total_gallery_count_for_member() {

	return apply_filters( 'mpp_get_total_gallery_count_for_member', mpp_get_total_gallery_for_user() );
}

/**
 * Is Single Gallery
 * 
 * @return type 
 */
function mpp_is_single_gallery() {

	if ( mediapress()->the_gallery_query && mediapress()->the_gallery_query->is_single() ) {
		return true;
	}

	return false;
}

/**
 * Get The Single gallery ID
 *
 * @return Int 
 */
function mpp_get_current_gallery_id() {

	return mediapress()->current_gallery->id;
}

/**
 * Get current Gallery
 * @return MPP_Gallery|null 
 */
function mpp_get_current_gallery() {

	return mediapress()->current_gallery;
}

function mpp_gallery_action_links( $gallery = null ) {

	echo mpp_get_gallery_action_links( $gallery );
}

function mpp_get_gallery_action_links( $gallery = null ) {

	$links = array();

	$gallery = mpp_get_gallery( $gallery );

	$links ['view'] = sprintf( '<a href="%1$s" title="view %2$s" class="mpp-view-gallery">%3$s</a>', mpp_get_gallery_permalink( $gallery ), esc_attr( $gallery->title ), __( 'view', 'mediapress' ) );

	//upload?

	if ( mpp_user_can_upload( $gallery->component, $gallery->component_id, $gallery ) ) {
		$links['upload'] = sprintf( '<a href="%s" alt="' . __( 'upload files to %s', 'mediapress' ) . '">%s</a>', mpp_get_gallery_add_media_url( $gallery ), mpp_get_gallery_title( $gallery ), __( 'upload', 'mediapress' ) );
	}
	//delete
	if ( mpp_user_can_delete_gallery( $gallery ) ) {
		$links['delete'] = sprintf( '<a href="%s" alt="' . __( 'delete %s', 'mediapress' ) . '" class="confirm mpp-confirm mpp-delete mpp-delete-gallery">%s</a>', mpp_get_gallery_delete_url( $gallery ), mpp_get_gallery_title( $gallery ), __( 'delete', 'mediapress' ) );
	}

	return apply_filters( 'mpp_gallery_actions_links', join( ' ', $links ), $links, $gallery );
}

/**
 * List galleries drop down
 * 
 * @param type $args
 * @return string
 */
function mpp_list_galleries_dropdown( $args = null ) {

	$default = array(
		'name'				=> 'mpp-gallery-list',
		'id'				=> 'mpp-gallery-list',
		'selected'			=> 0,
		'type'				=> '',
		'status'			=> '',
		'component'			=> '',
		'component_id'		=> '',
		'posts_per_page'	=> -1,
	);

	$args = wp_parse_args( $args, $default );

	extract( $args );

	if ( ! $component || ! $component_id ) {
		return;
	}

	$mppq = new MPP_Gallery_Query( $args );

	$html = '';
	$selected_attr = '';

	while ( $mppq->have_galleries() ) {

		$selected_attr = selected( $selected, mpp_get_gallery_id(), false );

		$html .= "<option value='" . mpp_get_gallery_id() . " '" . $selected_attr . ">" . mpp_get_gallery_title() . "</option>";
	}
	//reset current gallery
	mpp_reset_gallery_data();

	if ( ! empty( $html ) ) {
		$html = "<select name='{$name}' id='{$id}'>" . $html . "</select>";
	}
	
	if ( ! $echo ) {
		return $html;
	} else {
		echo $html;
	}
	
}

function mpp_get_editable_statuses( $type = null, $component = null ) {

	if ( ! $type || $type == 'active' ) {
		$statuses = mpp_get_active_statuses();
	} else {
		$statuses = mpp_get_registered_statuses();
	}
	
	//if a component is given, filter the status

	if ( $component ) {

		$all_statuses = (array) $statuses;
		$statuses = array();

		foreach ( $all_statuses as $status => $status_object ) {

			if ( mpp_component_supports_status( $component, $status ) )
				$statuses[$status] = $status_object;
		}
	}

	return apply_filters( 'mpp_get_editable_statuses', $statuses );
}

function mpp_get_editable_types( $type = null, $component = null ) {

	if ( ! $type || $type == 'active' ) {
		$types = mpp_get_active_types();
	} else {
		$types = mpp_get_registered_types();
	}
	
	//if a component is given, filter the status

	if ( $component ) {

		$all_types = (array) $types;
		$types = array();

		foreach ( $all_types as $type_slug => $type_object ) {

			if ( mpp_component_supports_type( $component, $type_slug ) )
				$types[$type_slug] = $type_object;
		}
	}

	return apply_filters( 'mpp_get_editable_types', $types );
}

function mpp_get_editable_components( $type = null ) {

	if ( ! $type || $type == 'active' ) {
		$components = mpp_get_active_components();
	} else {
		$components = mpp_get_registered_components();
	}
	
	//if a component is given, filter the status

	return apply_filters( 'mpp_get_editable_components', $components );
}

function mpp_status_dd( $args ) {

	$default = array(
		'name'		=> 'mpp-gallery-status',
		'id'		=> 'mpp-gallery-status',
		'echo'		=> true,
		'selected'	=> '',
		'component' => '',
		'type'		=> 'active', //'active||registered
	);

	$args = wp_parse_args( $args, $default );
	extract( $args );

	$statuses = mpp_get_editable_statuses( $type, $component );

	$html = "<select name='{$name}' id='{$id}'>";

	foreach ( $statuses as $key => $status ) {
		$html .= "<option value='{$key}'" . selected( $selected, $key, false ) . " >{$status->label}</option>";
	}

	$html .= "</select>";

	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
	
}

/**
 * Gallery Type drop down for use in themes
 * 
 * 
 */
function mpp_type_dd( $args = null ) {

	$default = array(
		'name'		=> 'mpp-gallery-type',
		'id'		=> 'mpp-gallery-type',
		'echo'		=> true,
		'component' => '',
		'selected'	=> '',
		'type'		=> 'active',
	);

	$args = wp_parse_args( $args, $default );
	extract( $args );


	$allowed_types = mpp_get_editable_types( $type, $component );

	$html = "";

	$html = "<select name='{$name}' id='{$id}'>";

	foreach ( $allowed_types as $key => $type ) {
		$html .="<option value='{$key}'" . selected( $key, $selected, false ) . " >{$type->label} </option>";
	}
	
	$html.="</select>";

	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
	
}

function mpp_component_dd( $args = null ) {

	$default = array(
		'name'		=> 'mpp-gallery-component',
		'id'		=> 'mpp-gallery-component',
		'echo'		=> true,
		'selected'	=> '',
		'type'		=> 'active',
	);

	$args = wp_parse_args( $args, $default );
	extract( $args );

	$allowed = mpp_get_editable_components( $type );

	$html = "";

	$html = "<select name='{$name}' id='{$id}'>";

	foreach ( $allowed as $key => $component ) {
		$html .="<option value='{$key}'" . selected( $key, $selected, false ) . " >{$component->label} </option>";
	}
	
	$html .= "</select>";

	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
	
}

function mpp_gallery_view_dd( $args ) {

	$default = array(
		'name'		=> 'mpp-gallery-view',
		'id'		=> 'mpp-gallery-view',
		'echo'		=> true,
		'selected'	=> 'default',
		'component' => '',
		'type'		=> '', // photo|audio|video
		'view_type' => 'gallery', //view type 'widget', 'shortcode' 'gallery' etc
		'class'		=> 'mpp-view-type'
	);

	$args = wp_parse_args( $args, $default );
	extract( $args );

	if ( ! $type ) {

		if ( $view_type == 'widget' ) {
			echo _ex( 'To reveal view options, please save the widget', 'widget view message', 'mediapress' );
		}

		return;
	}
	
	$views = mpp_get_registered_gallery_views( $type );

	$html = "<select name='{$name}' id='{$id}'>";

	foreach ( $views as $key => $view ) {

		if ( $component && ! $view->supports_component( $component ) ) {
			continue;
		}

		if ( ! $view->supports( $view_type ) ) {
			continue;
		}

		$html .= "<option value='{$key}'" . selected( $selected, $key, false ) . " >{$view->get_name()}</option>";
	}

	$html .= "</select>";

	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
}

/**
 * Output the Gallery directory search form.
 */
function mpp_directory_gallery_search_form() {

	$default_search_value = bp_get_search_default_text( 'mediapress' );
	$search_value = ! empty( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : $default_search_value;

	$search_form_html = '<form action="" method="get" id="search-mpp-form">
		<label><input type="text" name="s" id="mpp_search" placeholder="' . esc_attr( $search_value ) . '" /></label>
		<input type="submit" id="mpp_search_submit" name="mpp_search_submit" value="' . __( 'Search', 'mediapress' ) . '" />
	</form>';

	echo apply_filters( 'mpp_directory_gallery_search_form', $search_form_html );
}

function mpp_get_gallery_grid_column_class( $gallery = null ) {

	//we are using 1-24 col grid, where 3-24 repsesents 1/8th and so on
	$col = mpp_get_option( 'gallery_columns' );

	return mpp_get_grid_column_class( $col );
}

/**
 * Get the name of template slug for the given gallery media loop
 * 
 * 
 * @param type $gallery
 * @return string  {$type} name or {$type}-playlist e.g video or vide-playlis depending on the given gallery supports playlist of not?
 */
function mpp_get_media_loop_template_slug( $gallery ) {

	$slug = '';
	$type = mpp_get_gallery_type( $gallery );

	if ( mpp_gallery_supports_playlist( false, $type ) ) {

		$slug = "{$type}-playlist";
	} else {

		$slug = $type;
	}

	return $slug;
}
