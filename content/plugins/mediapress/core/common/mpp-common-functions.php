<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Get Gallery Post Type
 * 
 * @return string
 */
function mpp_get_gallery_post_type() {
	/**
	 * If you are a plugin author, please avoid the temptation to change the slug unless it is very important
	 * 
	 * I have left it but it should be used with utmost caution
	 * 
	 */
	return 'mpp-gallery';//apply_filters( 'mpp_gallery_post_type',  );
}

/**
 * Get Gallery Post Type Rewrite slug
 * 
 * @return string
 */
function mpp_get_gallery_post_type_rewrite_slug() {

	$slug = mpp_get_option( 'gallery_permalink_slug' );
	
	if ( ! $slug ) {
		$slug = 'gallery';
	}
		
	return apply_filters( 'mpp_gallery_post_type_rewrite_slug',  $slug );
}

/**
 * Get media post type
 * 
 * It is attachment
 * 
 * @return string
 */
function mpp_get_media_post_type() {

	return 'attachment';
}

/**
 * Get taxonomy for Media Type
 * 
 * @return string
 */
function mpp_get_type_taxname() {

	return 'mpp-type';
}

/**
 *  Get Taxonomy for components (members|groups)
 * 
 * @return string
 */
function mpp_get_component_taxname() {

	return 'mpp-component';
}

function mpp_get_status_taxname() {

	return 'mpp-status';
}
/**
 * Check if MediaPress is enabled for the given component
 * 
 * @param string $component current component, possible values 
 * @param type $component_id
 * @return type
 */
function mpp_is_enabled( $component, $component_id ) {

	return apply_filters( 'mpp_is_enabled', mpp_is_active_component( $component ), $component, $component_id );
	
}

function mpp_get_all_taxonomies_info() {

	return mediapress()->taxonomies;
}
/**
 * Is MediaPress network activated on the multisite install?
 * 
 * @return boolean
 */
function mpp_is_network_activated() {

	if ( ! is_multisite() ) {
		return false;
	}

	$plugins = get_site_option( 'active_sitewide_plugins');
	
	if ( isset( $plugins[ mediapress()->get_basename() ] ) ) {
		return true;
	}

	return false;
}

/**
 * Check if given post exists
 * 
 * @param type $args array(
 *     'id'=> //optional,
 *     '
 * )
 * 
 * @return boolean|object post data row on success else false
 */
function mpp_post_exists( $args ) {

	extract( $args );

	if ( ! empty( $id ) ) {
		//if ID is give, just shortcircuit the check

		$post = get_post( $post_id );
		if ( ! $post ) {
			return false;
		} else {
			return $post->ID;
		}
	}


	if ( ! $component_id || ! $slug || ! $post_type ) {
		return false;
	}

	$posts = get_posts(
	array(
		'post_type'					 => $post_type,
		'post_status'				 => $post_status,
		'name'						 => $slug,
		mpp_get_component_taxname()	 => mpp_underscore_it( $component ),
		'meta_query'				 => array(
			array(
				'key'		 => '_mpp_component_id',
				'value'		 => $component_id,
				'compare'	 => '=',
				'type'		 => 'UNSIGNED'
			)
		),
		'fields'					 => 'all'
	)
	);

	if ( ! empty( $posts ) ) {
		return array_pop( $posts );
	}
	
	return false;
}

/**
 * Check if a term already exists for gallery
 * 
 * @global type $wpdb
 * @param type $term
 * @param type $taxonomy
 * @param type $parent
 * @return int existing term id
 */
function mpp_term_exists( $term, $taxonomy = '', $parent = 0 ) {
	
	$term = mpp_strip_underscore( $term );
	return  _mpp_get_term( $term, $taxonomy ) ;
	/*
	global $wpdb;

	$select = "SELECT term_id FROM $wpdb->terms as t WHERE ";

	$tax_select = "SELECT tt.term_id, tt.term_taxonomy_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_id = t.term_id WHERE ";

	if ( is_numeric( $term ) ) {
		if ( 0 == $term )
			return 0;
		$where = 't.term_id = %d';
		if ( !empty( $taxonomy ) )
			return $wpdb->get_row( $wpdb->prepare( $tax_select . $where . " AND tt.taxonomy = %s", $term, $taxonomy ), ARRAY_A );
		else
			return $wpdb->get_var( $wpdb->prepare( $select . $where, $term ) );
	}

	$term = trim( wp_unslash( $term ) );

	$slug = $term;

	$where			 = 't.slug = %s';
	$where_fields	 = array( $slug );

	if ( !empty( $taxonomy ) ) {

		$where_fields[] = $taxonomy;


		if ( $result = $wpdb->get_row( $wpdb->prepare( "SELECT tt.term_id, tt.term_taxonomy_id FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy as tt ON tt.term_id = t.term_id WHERE $where AND tt.taxonomy = %s", $where_fields ), ARRAY_A ) )
			return $result;
	}


	return $wpdb->get_var( $wpdb->prepare( "SELECT term_id FROM $wpdb->terms as t WHERE $where", $slug ) );
	  
	 */
}

/**
 * Utility Functions
 */

/**
 * Prepend _ to everything passed
 */
function mpp_underscore_it( $str ) {

	if ( ! $str ) {
		return false;
	}

	//if already underscored, do not do it again
	if ( strpos( $str, '_' ) === 0 ) {
		return $str;
	}

	return '_' . $str;
}

/**
 * Remove underscore (_) from the prefix
 */
function mpp_strip_underscore( $str ) {
	
	if ( ! $str ) {
		return false;
	}

	if ( strpos( $str, '_' ) === 0 ) {
		return substr( $str, 1, strlen( $str ) );
	} else {
		return $str;
	}
}

/**
 * Converts string delimited by some separator to array
 * 
 * Useful when not sure if a single string or arry or multiple comma separated strings are passed
 * 
 * @param type $string
 * @param type $delim
 * @return type
 */
function mpp_string_to_array( $string, $delim = ',' ) {
	//if empty or already array
	if ( empty( $string ) || is_array( $string ) || is_numeric( $string ) ) {
		return $string;
	}

	return explode( $delim, $string );
}
/**
 * Convert array to string 
 * It is used to join array data as string
 * 
 * Example mpp_array_to_string( array( 'a', 'b', 'c' ) return "a,b,c"
 * 
 * @param array $array
 * @param string $delim
 * @return string 
 */
function mpp_array_to_string( $array, $delim = ',' ) {
	//if empty or already string
	if ( empty( $array ) || is_string( $array ) || is_numeric( $array ) ) {
		return $array;
	}

	return join( $delim, $array );
}

/**
 * Get the current page URI
 * 
 * @return string
 */
function mpp_get_current_page_uri() {

	if ( defined( 'DOING_AJAX' ) ) {
		return wp_get_referer();
	}

	$uri = 'http';
	
	if ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] == 'on' ) {
		$uri .= 's';
	}
	

	$uri .= '://';

	if ( $_SERVER[ 'SERVER_PORT' ] != '80' ) {
		$uri .= $_SERVER[ 'SERVER_NAME' ] . ':' . $_SERVER[ 'SERVER_PORT' ] . $_SERVER[ 'REQUEST_URI' ];
	} else {
		$uri .= $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
	}
	return $uri;
}
/**
 * Get the associated term id for media/gallery 
 * 
 * @see get_the_terms
 * 
 * It is a modified clone of get_the_terms
 * @param type $object media|gallery id or object
 * @param type $taxonomy
 * @return boolean|int false or term id
 */
function _mpp_get_object_term_id( $object, $taxonomy ) {
	
	if ( is_object( $object ) ) {
		$object = $object->id;
	}
	
	if ( ! $object ) {
		return false;
	}
	
	$terms = get_object_term_cache( $object, $taxonomy );
	
	if ( false === $terms ) {
		$terms = wp_get_object_terms( $object, $taxonomy );
		wp_cache_add($object, $terms, $taxonomy . '_relationships');
	}

	/**
	 * Filter the list of terms attached to the given post.
	 *
	 * @since 3.1.0
	 *
	 * @param array|WP_Error $terms    List of attached terms, or WP_Error on failure.
	 * @param int            $post_id  Post ID.
	 * @param string         $taxonomy Name of the taxonomy.
	 */
	$terms = apply_filters( 'get_the_terms', $terms, $object, $taxonomy );

	if ( empty( $terms ) ) {
		return false;
	}

	$term = array_pop( $terms );
	return $term->term_id;
}
/**
 * Get the term id that identifies the current status for a post object(could be gallery|attachment)
 * 
 * @param mixed|MPP_Gallery|MPP_Media object or ID
 * 
 * @return int status ID(term id for the status associated with this post)
 * 
 */
function mpp_get_object_status_term_id( $object ) {

	return _mpp_get_object_term_id( $object, mpp_get_status_taxname() );
}

/**
 * Get the slug of the status term( e.g private|public|friendsonly etc)
 * 
 * @param type $object
 * 
 * @return string status slug(used to uniquely identify this status) e.g (private|public|friends)
 */
function mpp_get_object_status( $object ) {

	$status_id = mpp_get_object_status_term_id( $object );

	//now get the slug for this status
	$status = mpp_get_status_object( $status_id );

	$slug = '';
	if ( isset( $status->slug ) ) {
		$slug = $status->slug;
	}

	return mpp_strip_underscore( $slug );
}

/**
 * 
 * @param mixed|int $object gallery or media object or ID
 * 
 * @return string label for the status e.g (Private| Public| Friends Only)
 */
function mpp_get_object_status_label( $object ) {

	$status_id = mpp_get_object_status_term_id( $object );
	//now get the slug for this status
	$status = mpp_get_status_object( $status_id );

	if ( $status ) {
		return $status->label;
	}
	
	return '';
}

/**
 * Get term_id that represents current type for a post object(could be gallery|attachment)
 * 
 * @param mixed|int Gallery|Media object or ID
 * 
 * @return int type ID(term id for the type associated with this post)
 */
function mpp_get_object_type_term_id( $object ) {

	return _mpp_get_object_term_id( $object, mpp_get_type_taxname() );
}

/**
 * 
 * @param type $object
 * @return string type slug(used to uniquely identify this status) e.g (audio|video|photo)
 */
function mpp_get_object_type( $object ) {

	$type_id = mpp_get_object_type_term_id( $object );

	//now get the slug for this status
	$type = mpp_get_type_object( $type_id );
	$slug = '';
	
	if ( isset( $type->slug ) ) {
		$slug = $type->slug;
	}
	
	return mpp_strip_underscore( $slug );
}

/**
 * 
 * @param mixed|int $object gallery or media object or ID
 * @return string label for the gallery, media type e.g (Audio| Video| Photo)
 */
function mpp_get_object_type_label( $object ) {

	$type_id = mpp_get_object_type_term_id( $object );

	//now get the slug for this status
	$type = mpp_get_type_object( $type_id );

	return $type->label;
}

/**
 * Get singular name for the given type
 * 
 * @param mixed|int $object gallery or media object or ID
 * @return string label for the gallery, media type e.g (Audio| Video| Photo)
 */
function mpp_get_object_type_singular_name( $object ) {

	$type_id = mpp_get_object_type_term_id( $object );

	//now get the slug for this status
	$type = mpp_get_type_object( $type_id );

	return $type->singular_name;
}

/**
 * Get term_id  for the term that represents the current component associated with a post object(could be gallery|attachment)
 * 
 * @param mixed|int| MPP_Gallery|MPP_Media object 
 * @return int component type ID(term id for the type associated with this post)
 */
function mpp_get_object_component_term_id( $object ) {

	return _mpp_get_object_term_id( $object, mpp_get_component_taxname() );
}

/**
 * 
 * @param type $object
 * @return string type slug(used to uniquely identify this component) e.g (members|groups|events etc)
 */
function mpp_get_object_component( $object ) {

	$term_id = mpp_get_object_component_term_id( $object );
	
	$slug = '';
	//now get the slug for this status
	$component = mpp_get_component_object( $term_id );
	
	if ( isset( $component->slug ) ) {
		$slug = $component->slug;
	}
	
	return mpp_strip_underscore( $slug );
}

/**
 * Get the label 
 * @param mixed|int $object gallery or media object or ID
 * @return string label for the gallery, media component type e.g (Group| Member| Event)
 */
function mpp_get_object_component_label( $object ) {

	$component_term_id = mpp_get_object_component_term_id( $object );

	//now get the slug for this status
	$component = mpp_get_component_object( $component_term_id );
	
	return $component->label;
}

/**
 * Find the term_id for a give type( or given term_slug)
 * 
 * Types are stored as custom taxonomy terms, we need to find the term_id corrosponding to a given type( e.g photo|audio etc)
 * 
 * 
 * @param type $type
 * @return type 
 */
function mpp_get_type_term_id( $type ) {

	return mpp_get_term_id_by_slug( $type, 'types' );
}

/**
 * Get the internal term_id for the given status (e.g find the term_id for private|public etc terms )
 * 
 * @param type $status
 * @return type 
 */
function mpp_get_status_term_id( $status ) {
	
	return mpp_get_term_id_by_slug( $status, 'statuses' );
}

/**
 * We are storing association of gallery/media to components as taxonomy
 * 
 * This function provides that term_id for the component e.g (groups|members) 
 * @param type $component
 * @return type 
 */
function mpp_get_component_term_id( $component ) {

	return (int) mpp_get_term_id_by_slug( $component, 'components' );
}

/**
 * Get current component id
 * 
 * @global type $bp
 * @param type $component_id
 * @return type 
 */
function mpp_get_current_component_id( $component_id = null ) {/** component Id: $bp->groups->id="groups"/"user"/etc etc */
	
	$owner_id = 0;
	
	if ( mpp_is_sitewide_gallery_component() ) {
		$post = get_queried_object();
		$owner_id = $post->post_author;
		
	}
	
	if ( ! $owner_id ) {
		$owner_id = get_current_user_id();
	}
	
	/* let the hook do the magic*, other components use this hook for providing ids */
	return apply_filters( 'mpp_get_current_component_id', $owner_id ); //context sensitive dd
}

/**
 * Get current component type
 * @return type 
 */
function mpp_get_current_component() {
	
	if ( isset( $_POST['_mpp_current_component'] ) ) {
		$component = trim( $_POST['_mpp_current_component'] );
		
		if ( ! mpp_is_active_component( $component ) ) {
			$component = '';
		}
		
	} elseif ( ! mediapress()->is_bp_active() || mpp_is_sitewide_gallery_component() ) {
		//if BuddyPress is not active, or BuddyPress is active and we are on the sitewide gallery page
		$component = 'sitewide';
	} else {
		$component = 'members';//may not be the best idea
	}
	
	return strtolower( apply_filters( 'mpp_get_current_component',  $component ) ); //context sensitive
}

function mpp_admin_is_edit_gallery() {
	
	if ( !  is_admin() ) {
		return false;
	}
	
	$post_id = isset( $_GET['post'] ) ? $_GET['post'] : 0;

	if ( empty( $post_id ) ) {
		return false;
	}

	$post = get_post( $post_id );

	if ( mpp_get_gallery_post_type() == $post->post_type ) {
		return true;
	}

	return false;
}

function mpp_admin_is_add_gallery() {
	

	if ( is_admin() && isset( $_GET['post_type'] ) && $_GET['post_type'] == mpp_get_gallery_post_type() && isset( $_GET['mpp-gallery-type'] ) ) {
		return true;
	} 

	return false;
}

function mpp_get_reserved_slugs() {

	$reserved =  array( 'edit', 'info', 'cover', 'members', 'manage', 'image', 'media', 'reorder', 'delete-cover', 'my-gallery' );
	
	$reserved = array_merge( $reserved, array_keys( mpp_get_registered_components() ), array_keys( mpp_get_registered_statuses() ), array_keys( mpp_get_registered_types() ) );
	
	return apply_filters( 'mpp_reserved_slugs', $reserved );
}
/**
 * Check if given key is reserved
 * @param type $slug
 * @return boolean
 */
function mpp_is_reserved_slug( $slug ) {
	
	$reserved = mpp_get_reserved_slugs();//array( 'edit', 'info', 'cover', 'members', 'manage', 'image', 'media', 'reorder', 'delete-cover' );
	
	if ( in_array( $slug, $reserved ) ) {
		return true;
	}
	
	return false;
}

/**
 * Update Media extensions, save it to mpp-settings
 * 
 * @param type $type
 * @param type $extensions
 */
function mpp_update_media_extensions( $type, $extensions ) {

	$all_extensions			 = mpp_get_all_media_extensions();
	
	$all_extensions = array_map( 'mpp_array_to_string', $all_extensions ) ;
	
	if ( ! empty( $extensions ) ) {
		$all_extensions[$type]   = join( ',', $extensions );
	}
	
	mpp_update_option( 'extensions', $all_extensions );
}

/**
 * Get the array of allowed file extensions for a given type
 * 
 * @param string $type media type could be photo, audio, video doc etc
 * @return mixed array of extensions e.g array( 'gif', 'jpg', 'png') etc
 */
function mpp_get_media_extensions( $type ) {

	$extensions = mpp_get_all_media_extensions();
	
	if ( isset( $extensions[ $type ] ) ) {
		return $extensions[ $type ];
	}
	
	return array();
}

/**
 * get all extensions as multidimensional array
 * Key is media type and values are array of allowed extensions e.g 
 *  array(
 *      'photo'=> array('jpg', 'png', 'gif'),
 *      'audio'=> array( 'mp3', 'wav'),
 * )
 * 
 * @return array of extensions as multi dimensional array
 */
function mpp_get_all_media_extensions() {

	$all_extensions = (array) mpp_get_option( 'extensions', array() );

	$extensions = array();
	
	//traverse and convert to array
	foreach (  $all_extensions as $type => $extension ) {
		$extensions[$type] = mpp_string_to_array( strtolower ( $extension ) ); //lowercase extensions and convert to array
	}
	
	return $extensions;
}

/**
 * @todo make independent of BP
 * 
 * @return type
 */
function mpp_get_all_options() {

	$default = array(
		'galleries_per_page'				=> 20, //how many galleries to show per page in the gallery loop
		'galleries_per_rss'					=> 15, //how many galleries to show in the rss feed
		'media_per_page'					=> 20, //how many media per page for the media loop
		'media_per_gallery_page'			=> 20, //how many media per gallery page(single gallery)
		'media_per_rss'						=> 20, //how many media in the rss feed
		'comments_per_page'					=> 20, //how many comments per page
		'show_upload_quota'					=> 0, //should we show the upload quota to the user?
		'activity_upload'					=> true, //is activity upload enabled?
		'has_gallery_directory'				=> true, //have we enabled the gallery directory?
		'default_storage'					=> 'local', //type of storage engine used
		'default_media_status'				=> 'public', //if the status is not given and gallery does not exist, what should be the default status?
		'mpp_upload_space'					=> 10, //how many Mbs?
		'show_orphaned_media'				=> 0,
		'delete_orphaned_media'				=> 0,
		
		'enable_audio_playlist'				=> true,
		'enable_video_playlist'				=> true,
		
		'gallery_columns'					=> 4,
		'media_columns'						=> 4,
		
		'enable_gallery_comment'			=> 1,
		'enable_media_comment'				=> 1,
		'active_components'					=> array( 'members' => 'members', 'sitewide' => 'sitewide' ),
		'active_types'						=> array( 'photo' => 'photo', 'audio' => 'audio', 'video'=> 'video' ),
		'active_statuses'					=> array( 'public' => 'public', 'private' => 'private' ),
		'default_status'					=> 'public',
		'extensions'						=> array(),
		'load_lightbox'						=> 1,
		'enable_activity_lightbox'			=> 1,
		'enable_gallery_lightbox'			=> 1,
		'autopublish_activities'			=> array(),
		//sitewide
		'enable_gallery_archive'			=> 0,
		'gallery_archive_slug'				=> 'galleries',
		'gallery_permalink_slug'			=> 'gallery',
		'sitewide_active_types'				=> array( 'photo' => 'photo', 'audio' => 'audio', 'video'=> 'video' ),
		'members_active_types'				=> array( 'photo' => 'photo', 'audio' => 'audio', 'video'=> 'video' ),
		'members_enable_type_filters'		=> 1,//enable type filters on member page
		'groups_active_types'				=> array( 'photo' => 'photo', 'audio' => 'audio', 'video'=> 'video' ),
		'enable_group_galleries_default'	=> 'yes',
		'groups_enable_my_galleries'		=> 1,
		
		
	);


	//if ( function_exists( 'bp_get_option' ) )
	//	$options = bp_get_option( 'mpp-settings', $default );
	//else
	$options = get_option( 'mpp-settings', $default );

	return apply_filters( 'mpp_settings', $options );
}

function mpp_save_options( $options ) {

	//if ( function_exists( 'bp_update_option' ) )
	//	$callback	 = 'bp_update_option';
	//else
		$callback	 = 'update_option';
	//both function have same signature, so no need to worry

	return $callback( 'mpp-settings', $options );
}

/**
 * Get the gallery settings for a perticular option
 * 
 * @param string $option the name of gallery specific option 
 * @return mixed (array|int|string) depending on the option  
 */
function mpp_get_option( $option, $default = '' ) {


	$options = mpp_get_all_options();

	return isset( $options[ $option ] ) ? $options[ $option ] : $default; //may be a bad idea but we are going to keep it unless we implement the admin panel
}

/**
 * Update individual MediaPress option and save that to database( in options table )
 * 
 * @param type $option_name
 * @param type $value
 */
function mpp_update_option( $option_name, $value ) {

	$options				 = mpp_get_all_options();
	$options[$option_name]	 = $value;
	
	mpp_save_options( $options );
}

/**
 * Perform a wp_redirect
 *
 * @uses wp_safe_redirect()
 *
 * @param string $location The redirect URL.
 * @param int $status Optional. The numeric code to give in the redirect
 *        headers. Default: 302.
 */
function mpp_redirect( $location, $status = 302 ) {

	if ( function_exists( 'buddypress' ) ) {
		return bp_core_redirect( $location );
	}
	// On some setups, passing the value of wp_get_referer() may result in an
	// empty value for $location, which results in an error. Ensure that we
	// have a valid URL.
	if ( empty( $location ) ) {
		$location = site_url( '/' );
	}

	wp_safe_redirect( $location, $status );
	die;
}

/**
 * Get the default status to be applied to media/gallery
 * @return string
 */
function mpp_get_default_status() {

	return mpp_get_option( 'default_status', 'public' );
}



//In current release, media & gallery have same statuses
//in future, we will separate them

/**
 * array of allowed types
 * 
 * @return MPP_Status[]
 */
function mpp_get_active_statuses() {
	//$mediapress = mediapress();

	$active_status_keys = (array) mpp_get_option( 'active_statuses' );
	$registered_statuses = mpp_get_registered_statuses();
	
	$types = array();
	foreach( $active_status_keys as $type ) {
		
		if ( isset( $registered_statuses[ $type ] ) ) {
			$types[ $type ] = $registered_statuses[ $type ];
		}
	}
	
	return $types;
	
}

/**
 * Check if given status is enabled?( allowed by admin)
 * 
 * @param type $status key for status e.g public|privact|friendsonly
 * @return boolean
 * 
 */
function mpp_is_active_status( $status ) {

	if ( empty( $status ) ) {
		return false; //empty can not be valid status
	}
	
	$statuses = mpp_get_active_statuses();
	
	if ( isset( $statuses[ $status ] ) ) {
		return true;
	}

	return false;
}

/**
 *  Check if the list of provided statuses are enabled for gallery
 * 
 * The provided list could be comma separated like 'private,public' or array like array('private', public')
 * @param type $statuses
 * @return boolean
 * 
 */
function mpp_are_active_statuses( $statuses ) {

	if ( empty( $statuses ) ) {
		return false; //empty can not be valid statuses
	}
	
	$statuses = mpp_string_to_array( $statuses );

	$valid_statuses = mpp_get_active_statuses();

	$valid_statuses = array_keys( $valid_statuses ); //get the valid status keys as array

	$diff = array_diff( $statuses, $valid_statuses );

	if ( ! empty( $diff ) ) {//if there exists atleast one status which is not registered as valid
		return false;
	}

	return true; //yup valid
}

/**
 * Get all enabled components which can be associated to the gallery
 * 
 * @return MPP_Component[] keys are $component_name(groups|members etc)
 * //we need to change it to registered componenets
 */
function mpp_get_active_components() {

	$mediapress = mediapress();
	
	$registered_components = mpp_get_registered_components();
	
	$active_components_keys = (array) mpp_get_option( 'active_components' );
	
	$active_components = array();
	
	foreach ( $active_components_keys as $key  ) {
		
		if ( isset( $registered_components[ $key ] ) ) {
			$active_components[ $key ] = $registered_components[ $key ] ;
		}
		
	}
	
	return $active_components;
	
	//return $mediapress->active_components;
}

/**
 *  Is enabled gallery associated component
 * 
 * @param type $component ( members|groups)
 * @return boolean
 */
function mpp_is_active_component( $component ) {

	if ( empty( $component ) ) {
		return false;
	}

	$components = mpp_get_active_components();

	if ( isset( $components[ $component ] ) ) {
		return true;
	}

	return false;
}

/**
 * Are valid gallery associated components
 * The component list can be comma separated list like user,groups or array like array('user', 'groups')
 * @param type $components
 * @return boolean
 */
function mpp_are_active_components( $components ) {

	if ( empty( $components ) ) {
		return false;
	}

	$components = mpp_string_to_array( $components );

	$valid_components = mpp_get_active_components();

	$valid_components = array_keys( $valid_components );

	$diff = array_diff( $components, $valid_components );

	if ( ! empty( $diff ) ) {
		return false;
	}

	return true;
}

/**
 * Gallery Types related
 * 
 */

/**
 * Get all valid registered gallery types as key=>Type Object array
 * 
 * @return MPP_Type[]
 */
function mpp_get_active_types() {

	//$mediapress = mediapress();

	$active_type_keys = (array) mpp_get_option( 'active_types' );
	$registered_types = mpp_get_registered_types();
	
	$types = array();
	
	foreach ( $active_type_keys as $type ) {
		
		if ( isset( $registered_types[ $type ] ) ) {
			$types[ $type ] = $registered_types[ $type ];
		}
	}
	
	return $types;
	//return $mediapress->active_types;
}

/**
 * Is valid gallery type?
 * @param type $type Gallery type key (photo|audio|video)
 * @return boolean
 */
function mpp_is_active_type( $type ) {

	if ( empty( $type ) ) {
		return false;
	}

	$valid_types = mpp_get_active_types();

	if ( isset( $valid_types[ $type ] ) ) {
		return true;
	}

	return false;
}

/**
 * Are these types valid
 * 
 * Used to validated agains a list of types
 * 
 * @param type $types
 * @return boolean
 */
function mpp_are_active_types( $types ) {

	if ( empty( $types ) ) {
		return false;
	}

	$types = mpp_string_to_array( $types );

	$valid_types = mpp_get_active_types();

	$valid_types = array_keys( $valid_types ); //get keys as array

	$diff = array_diff( $types, $valid_types );

	if ( ! empty( $diff ) ) {//there exists atleast one unregistered type
		return false;
	}

	return true;
}

function mpp_component_init_type_support( $component ) {
	
	$supported_types = mpp_component_get_supported_types( $component );
	
	foreach ( $supported_types as $type ) {
		mpp_component_add_type_support( $component, $type );
	}
}

function mpp_component_has_type_filters_enabled( $component, $component_id ) {
	
	return mpp_get_option( $component .'_enable_type_filters', 0 );
}
/**
 * Is autopublishing enable for the given gallery action
 * 
 * @param string $action create_gallery|upload_media
 * @return boolean
 */
function mpp_is_auto_publish_to_activity_enabled( $action ) {
	
	$enabled_types = mpp_get_option( 'autopublish_activities' );
	
	if ( empty( $enabled_types ) ) {
		return false;
	}	
	
	if ( in_array( $action, $enabled_types ) ) {
		return true;
	}
	
	return false;
}
/**
 * 
 * @param array $args prop=>val array for html attributes
 * @return string html attributes
 */

function mpp_get_html_attributes( $args = array () ) {
	
	$atts = '';
	
	foreach ( $args as $key => $val ) {
		
		if ( empty( $val ) ) {
			continue;
		}
		
		$key = sanitize_key( $key );//my not be proper here
		$val = esc_attr( $val );
		
		$atts .= "{$key} = '{$val}' ";
	}
	
	return $atts;
}
/**
 * Calculates the class to be applied for our media/gallery grid based on purecss
 * 
 * @param type $col
 */
function mpp_get_grid_column_class( $col  ) {
	
	$col = absint( $col );
	
	if ( empty( $col ) ) {
		return ;
	}
	
	$supported = array( 1, 2, 3, 4, 5, 6, 8, 12 );
	//supported grids are col-1, col-2, col-3, col-4, col-5, col-6, col-8, col-12
	if ( ! in_array( $col, $supported ) ) {
		return 'mpp-col-'. $col . ' mpp-col-not-supported';
	}
	
	if ( $col == 5 ) {//special case
		return 'mpp-u-1-5';
	}
	//in all other cases
	$col = (int) (24/$col);
	
	return "mpp-u-{$col}-24";
}

function mpp_recursive_delete_dir( $dir ) {
	
	if ( ! is_dir( $dir ) || ! is_readable( $dir ) ) {
		return false;
	}
	   
    $items = scandir( $dir ); 
     
	foreach ( $items as $item ) {
		 
		if ( $item == '.' || $item != '..' ) {
			continue;
		}
		
		$file = trailingslashit( wp_normalize_path( $dir ) ) .  $item ;
		
		if ( is_dir( $item ) ) {
			mpp_recursive_delete_dir ( $file );
		} else {
			@ unlink( $file );
		}
        
    } 
     
	return @ rmdir( $dir ); 
   
}
