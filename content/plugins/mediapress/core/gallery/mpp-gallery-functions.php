<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Gallery Specific functions
 */


/**
 * Privacy related
 * 
 */

/**
 * Get registered gallery statuses
 * 
 * @return array associative array of status objects where key is the status slug(e.g private|public etc)
 * 
 */
function mpp_get_registered_statuses() {

	$mediapress = mediapress();

	return $mediapress->gallery_statuses;
}

/**
 * Check if given status is valid Gallery Status(was it registered?)?
 * 
 * @param type $status key for status e.g public|privact|friendsonly
 * @return boolean
 * 
 */
function mpp_is_registered_status( $status ) {

	if ( empty( $status ) ) {
		return false; //empty can not be valid status
	}

	$statuses = mpp_get_registered_statuses();

	if ( isset( $statuses[ $status ] ) ) {
		return true;
	}

	return false;
}

/**
 *  Check if the list of provided statuses are registered for gallery
 * 
 * The provided list could be comma separated like 'private,public' or array like array('private', public')
 * @param type $statuses
 * @return boolean
 * 
 */
function mpp_are_registered_statuses( $statuses ) {

	if ( empty( $statuses ) ) {
		return false; //empty can not be valid statuses
	}

	$statuses = mpp_string_to_array( $statuses );

	$valid_statuses = mpp_get_registered_statuses();

	$valid_statuses = array_keys( $valid_statuses ); //get the valid status keys as array

	$diff = array_diff( $statuses, $valid_statuses );

	if ( ! empty( $diff ) ) {//if there exists atleast one status which is not registered as valid
		return false;
	}

	return true; //yup valid
}

/**
 * Get all valid components which can be associated to the gallery
 * 
 * @return MPP_Component[] keys are $component_name(groups|members etc)
 * //we need to change it to registered componenets
 */
function mpp_get_registered_components() {

	$mediapress = mediapress();

	return $mediapress->components;
}

/**
 *  Is valid gallery associated component
 * 
 * @param type $component ( members|groups)
 * @return boolean
 * @deprecated use mpp_is_registered_component
 * 
 */
function mpp_is_registered_gallery_component( $component ) {
	
	return mpp_is_registerd_component( $component );
	
}

/**
 * Is a valid & registered component
 * 
 * @param string $component
 * @return boolean
 */
function mpp_is_registered_component( $component ) {

	if ( empty( $component ) ) {
		return false;
	}

	$valid_components = mpp_get_registered_components();

	if ( isset( $valid_components[$component] ) ) {
		return true;
	}

	return false;
}

/**
 * Are valid & registered gallery associated components
 * The component list can be comma separated list like user,groups or array like array('user', 'groups')
 * @param type $components
 * @return boolean
 */
function mpp_are_registered_components( $components ) {

	if ( empty( $components ) ) {
		return false;
	}

	$components = mpp_string_to_array( $components );

	$valid_components = mpp_get_registered_components();

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
function mpp_get_registered_types() {

	$mediapress = mediapress();

	return $mediapress->types;
}

/**
 * Is valid gallery type?
 * @param type $type Gallery type key (photo|audio|video)
 * @return boolean
 */
function mpp_is_registered_type( $type ) {

	if ( empty( $type ) ) {
		return false;
	}

	$valid_types = mpp_get_registered_types();

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
function mpp_are_registered_types( $types ) {

	if ( empty( $types ) ) {
		return false;
	}

	$types = mpp_string_to_array( $types );

	$valid_types = mpp_get_registered_types();

	$valid_types = array_keys( $valid_types ); //get keys as array

	$diff = array_diff( $types, $valid_types );

	if ( ! empty( $diff ) ) {//there exists atleast one unregistered type
		return false;
	}

	return true;
}

/**
 * Check if given post a valid gallery type?
 * 
 * @param int $id
 */
function mpp_is_valid_gallery( $id ) {

	return get_post_type( $id ) == mpp_get_gallery_post_type();
}

/**
 * Check if given gallery is sitewide gallery
 * 
 * @param int | WP_Post $id
 * @return boolean
 */
function mpp_is_sitewide_gallery( $id ) {

	if ( ! mpp_is_valid_gallery( $id ) ) {
		return false;
	}
	//assume it is a valid gallery
	$gallery = mpp_get_gallery( $id );

	if ( ! empty( $gallery ) && $gallery->component == 'sitewide' ) {
		return true;
	}
	return false;
}

/**
 * Get the gallery id for a wall gallery type
 * 
 * 
 * @param type $user_id
 * @param type $type(media type)
 * @return type
 */
function mpp_get_wall_gallery_id( $args ) {

	if ( empty( $args ) || ! is_array( $args ) ) {
		return false;
	}

	extract( $args );

	if ( ! $component || ! $component_id || ! $media_type ) {
		return false;
	}

	$id = 0;
	$func_name = "mpp_get_{$component}_wall_{$media_type}_gallery_id";

	if ( function_exists( $func_name ) ) {
		$id = call_user_func( $func_name, $component_id );
	}

	return apply_filters( 'mpp_get_wall_gallery_id', $id, $component_id, $component, $media_type );
}

/**
 * 
 * Update the wall gallery id for a group
 * 
 * @param array $args{
 * 	
 * 	@type int $component_id
 *  @type string $component
 *  @type string $media_type
 * 	@type int $gallery_id
 * 
 *  }
 * @return boolean
 */
function mpp_update_wall_gallery_id( $args ) {

	if ( empty( $args ) || ! is_array( $args ) ) {
		return false;
	}

	extract( $args );

	if ( ! $component || ! $component_id || ! $media_type || ! $gallery_id ) {
		return;
	}

	$id = 0;

	$func_name = "mpp_update_{$component}_wall_{$media_type}_gallery_id";

	if ( function_exists( $func_name ) ) {
		$id = call_user_func( $func_name, $component_id, $gallery_id );
	}

	return apply_filters( 'mpp_update_wall_gallery_id', $id, $component_id, $component, $media_type );
}

/**
 * Delete a wall gallery id
 * 
 * @param array $args{
 * 	
 * 	@type int $component_id
 *  @type string $component
 *  @type string $media_type
 * 	@type int $gallery_id
 * 
 *  }
 * @return boolean
 */
function mpp_delete_wall_gallery_id( $args ) {

	if ( empty( $args ) || ! is_array( $args ) ) {
		return false;
	}

	$default = array(
		'component'		=> '',
		'component_id'	=> '',
		'gallery_id'	=> 0,
		'media_type'	=> ''
	);

	$args = wp_parse_args( $args, $default );

	extract( $args );

	if ( ! $component || ! $component_id || ! $media_type ) {
		return;
	}


	$func_name = "mpp_delete_{$component}_wall_gallery_id";

	if ( function_exists( $func_name ) ) {
		$id = call_user_func( $func_name, $component_id, $media_type, $gallery_id );
	}

	do_action( 'mpp_delete_wall_gallery_id', $id, $component_id, $component, $media_type );
}

/**
 * Check if the given gallery is wall gallery
 * 
 * @param int|MPP_Gallery $gallery_id numeric gallery id or gallery object
 * @return boolean true if wall gallery, false if not wall gallery
 */
function mpp_is_wall_gallery( $gallery_id ) {

	$gallery = mpp_get_gallery( $gallery_id );

	if ( ! $gallery ) {
		return false;
	}

	$wall_gallery_id = mpp_get_wall_gallery_id( array(
		'component'		=> $gallery->component,
		'component_id'	=> $gallery->component_id,
		'media_type'	=> $gallery->type
	) );

	if ( $gallery->id === $wall_gallery_id ) {
		return true;
	}

	return false;
}

/**
 * Get profile photo gallery id
 * 
 * @param type $user_id
 * @return type
 */
function mpp_get_members_wall_photo_gallery_id( $user_id ) {

	return (int) mpp_get_user_meta( $user_id, '_mpp_wall_photo_gallery_id', true );
}

/**
 * Get profile Video gallery id
 * 
 * @param type $user_id
 * @return type
 */
function mpp_get_members_wall_video_gallery_id( $user_id ) {

	return (int) mpp_get_user_meta( $user_id, '_mpp_wall_video_gallery_id', true );
}

/**
 * Get profile audio gallery id
 * @param type $user_id
 * @return type
 */
function mpp_get_members_wall_audio_gallery_id( $user_id ) {

	return (int) mpp_get_user_meta( $user_id, '_mpp_wall_audio_gallery_id', true );
}

/**
 * Get profile photo gallery id
 * 
 * @param type $user_id
 * @return type
 */
function mpp_update_members_wall_photo_gallery_id( $user_id, $gallery_id ) {

	return mpp_update_user_meta( $user_id, '_mpp_wall_photo_gallery_id', $gallery_id );
}

/**
 * Get profile Video gallery id
 * 
 * @param type $user_id
 * @return type
 */
function mpp_update_members_wall_video_gallery_id( $user_id, $gallery_id ) {

	return mpp_update_user_meta( $user_id, '_mpp_wall_video_gallery_id', $gallery_id );
}

/**
 * Get profile audio gallery id
 * @param type $user_id
 * @return type
 */
function mpp_update_members_wall_audio_gallery_id( $user_id, $gallery_id ) {

	return mpp_update_user_meta( $user_id, '_mpp_wall_audio_gallery_id', $gallery_id );
}

/* * *
 * Delete
 */

function mpp_delete_members_wall_gallery_id( $user_id, $type, $gallery_id ) {

	$key = "_mpp_wall_{$type}_gallery_id";

	$wall_gallery_id = (int) mpp_get_user_meta( $user_id, $key, true );

	if ( mpp_delete_user_meta( $user_id, $key, $gallery_id ) ) {
		return $wall_gallery_id;
	}

	return 0; //invalid gallery id if unable to delete
}

/*
 * ---------------------------- Gallery Creation, Gallery Editing, Gallery deletion--------------------------
 */

/**
 * Create new Gallery
 * @param type $args
 * @return boolean
 */
function mpp_create_gallery( $args = '' ) {


	$default = array(
		'id'			=> false,
		'creator_id'	=> get_current_user_id(),
		'title'			=> '',
		'description'	=> '',
		'slug'			=> '',
		'status'		=> '',
		'order'			=> false,
		'component'		=> false,
		'component_id'	=> false,
		'type'			=> ''
	);
	//if the gallery id is set 
	if ( isset( $args['id'] ) && ! empty( $args['id'] ) ) {
		return mpp_update_gallery( $args );
	}

	$args = wp_parse_args( $args, $default );

	extract( $args );

	$post_data = array(
		'post_type'		=> mpp_get_gallery_post_type(),
		'post_status'	=> 'publish',
		'post_author'	=> $creator_id,
		'post_title'	=> $title,
		'post_content'	=> $description,
		'post_name'		=> $slug,
	);

	//if no component id is given, assume to be user gallery    
	if ( ! $component_id ) {
		$component_id = get_current_user_id();
	}

	//if a component is not given, assume it to be user gallery
	if ( ! $component ) {
		$component = 'members';
	}

	if ( ! $type ) {
		$type = 'photo';
	}

	$gallery_status = mpp_get_default_status();

	if ( ! empty( $status ) ) {

		if ( mpp_is_registered_status( $status ) ) {
			$gallery_status = $status;
		}
	}

	//hierarchical tax should always pass ids
	$tax = array(
		mpp_get_component_taxname() => mpp_underscore_it( $component ), //(array)mpp_get_component_term_id(
		mpp_get_type_taxname()		=> mpp_underscore_it( $type ), //(array)mpp_get_type_term_id
		mpp_get_status_taxname()	=> mpp_underscore_it( $gallery_status )//(array)mpp_get_status_term_id
	);


	$post_data['tax_input'] = $tax;

	$gallery_id = wp_insert_post( $post_data );

	if ( is_wp_error( $gallery_id ) ) {
		return false; //unable to add gallery
	}

	//otherwise update the meta with component id
	mpp_update_gallery_meta( $gallery_id, '_mpp_component_id', $component_id );
	//fire the gallery created action
	do_action( 'mpp_gallery_created', $gallery_id );

	return $gallery_id;
}

/**
 * @desc Update Existing Gallery
 */
//improve
function mpp_update_gallery( $args ) {

	if ( ! isset( $args['id'] ) ) {
		return new WP_Error( 0, __( 'Must provide ID to update a gallery.', 'mediapress' ) );
	}

	$gallery = get_post( $args['id'] );

	//we need meaning full error handling in future
	if ( ! $gallery ) {
		return new WP_Error( 0, __( 'Gallery Does not exist!', 'mediapress' ) );
	}
	// $gdata =  get_object_vars( $gallery );

	$gallery_data = array(
		'id'			=> $gallery->ID,
		'title'			=> $gallery->post_title,
		'description'	=> $gallery->post_content,
		'slug'			=> $gallery->post_name,
		'creator_id'	=> $gallery->post_author,
		'order'			=> $gallery->menu_order,
		'component_id'	=> mpp_get_gallery_meta( $gallery->ID, '_mpp_component_id' ),
		'component'		=> '',
		'status'		=> '',
		'type'			=> '',
			/// 'type'          => mpp_get_object_type( $gallery ),
			// 'status'        => mpp_get_object_status( $gallery ),
			//'component'     => mpp_get_object_component(  $gallery )
	);

	$args = wp_parse_args( $args, $gallery_data );
	extract( $args );


	if ( empty( $id ) || empty( $title ) ) {
		return false;
	}
	//now let us check which fields need to be updated

	if ( $creator_id ) {
		$gallery->post_author = $creator_id;
	}

	if ( $title ) {
		$gallery->post_title = $title;
	}

	if ( $description ) {
		$gallery->post_content = $description;
	}

	if ( $slug ) {
		$gallery->post_name = $slug;
	}

	if ( $order ) {
		$gallery->menu_order = $order;
	}

	$post = get_object_vars( $gallery );
	$tax = array();

	if ( $component && mpp_is_active_component( $component ) ) {
		$tax[mpp_get_component_taxname()] = mpp_underscore_it( $component );
	}

	if ( $type && mpp_is_active_type( $type ) ) {
		$tax[mpp_get_type_taxname()] = mpp_underscore_it( $type );
	}

	if ( $status && mpp_is_active_status( $status ) ) {
		$tax[ mpp_get_status_taxname() ] = mpp_underscore_it( $status );
	}
	
	if ( ! empty( $tax ) ) {
		$post['tax_input'] = $tax;
	}
	//$post['ID'] = $gallery->id;

	$gallery_id = wp_update_post( $post );

	if ( is_wp_error( $gallery_id ) ) {
		return $gallery_id; //error
	}

	do_action( 'mpp_gallery_updated', $gallery_id );

	return $gallery_id;
}

/**
 * Delete a Gallery
 * For an outline of actions, please see MPP_Delection_Actions_mapper class
 *  
 * this function calls actions 'mpp_before_gallery_delete' -> deletes everything -> 'mpp_gallery_deleted'
 */
function mpp_delete_gallery( $gallery_id, $force_delete = true ) {
	/**
	 * @see MPP_Deletion_Actions_Mapper::map_before_delete_post_action()
	 * @see MPP_Deletion_Actions_Mapper::map_deleted_post() for the approprivte function
	 */
	/**
	 * Action flow
	 *  wp_delete_post() 
	 * 		-> do_action('before_delete_post')
	 * 		-> MPP_Deletion_Actions_Mapper::map_before_delete_post_action()
	 * 		-> do_action ( 'mpp_before_gallery_delete, $gallery_id )
	 * 		-> cleanup gallery
	 * 		.........
	 * 		.........
	 * 
	 *  wp_delete_post()
	 * 		-> do_action( 'deleted_post', $post_id )
	 * 		-> do_action( 'mpp_gallery_deleted', $gallery_id )		
	 */
	return wp_delete_post( $gallery_id, $force_delete );
}

/**
 * Set/Update gallery type
 * 
 * @param int|MPP_Gallery $gallery
 * @param string $type ( audio|video|photo etc)
 */
function mpp_update_gallery_type( $gallery, $type ) {

	$gallery = mpp_get_gallery( $gallery );

	wp_set_object_terms( $gallery->id, mpp_get_type_term_id( $type ), mpp_get_type_taxname() );
}

/**
 * Set/Update gallery status
 * 
 * @param int|MPP_Gallery $gallery
 * @param string $type ( public|private|friendsonly etc)
 */
function mpp_update_gallery_status( $gallery, $status ) {

	$gallery = mpp_get_gallery( $gallery );

	wp_set_object_terms( $gallery->id, mpp_get_status_term_id( $status ), mpp_get_status_taxname() );
}

/**
 * Set/Update gallery component
 * 
 * @param int|MPP_Gallery $gallery
 * @param string $type ( groups|groups|events etc)
 */
function mpp_update_gallery_component( $gallery, $component ) {

	$gallery = mpp_get_gallery( $gallery );

	wp_set_object_terms( $gallery->id, mpp_get_component_term_id( $component ), mpp_get_component_taxname() );
}

/**
 * Check if a Gallery exists when the gallery slug, the associated component type and component id is given
 * 
 * @param type $gallery_slug
 * @param type $component
 * @param type $component_id
 * @return boolean|object gallery post row or false
 */
function mpp_gallery_exists( $gallery_slug_or_id, $component, $component_id ) {

	$args = array(
		'component'		=> $component,
		'component_id'	=> $component_id,
		// 'slug'       => $gallery_slug,
		'post_status'	=> 'publish',
		'post_type'		=> mpp_get_gallery_post_type()
	);
	
	//won't work with form input/request uri if int
	if ( is_int( $gallery_slug_or_id ) ) {
		$args['id'] = absint( $gallery_slug_or_id );
	} else {
		$args['slug'] = $gallery_slug_or_id;
	}

	return mpp_post_exists( $args );
}

/**
 * Stats Funtions
 */

/**
 *  
 * Get total gallery which is owned by this user
 * 
 * @param type $user_id
 * @return type
 */
function mpp_get_total_gallery_for_user( $user_id = false ) {
	//find all galleries where component is is
	//check if BuddyPress profile, then get the dsiplayed user id
	if ( ! $user_id && function_exists( 'bp_displayed_user_id' ) && bp_is_user() ) {
		$user_id = bp_displayed_user_id();
	}

	//if author archive, get the author id
	if ( ! $user_id && is_author() ) {
		$user_id = get_queried_object_id(); //get the author id
	}
	//if still not given, get the current user id
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	return mpp_get_gallery_count( array(
		'component'		=> 'members',
		'component_id'	=> $user_id
	));
}

/**
 *  Get gallery count by gallery type
 * 
 * @param type $type
 * @param string $owner_type component( members|groups)
 * @param type $owner_id user_id|grup_id etc
 * @param type $status public|private etc
 * @return int
 */
function mpp_get_gallery_count_by_type( $type, $owner_type, $owner_id, $status = null ) {
	//check for the vailidity of the gallery type
	if ( ! mpp_is_registered_type( $type ) ) {
		return 0; //ahh , that's not right
	}

	return mpp_get_gallery_count( array(
		'component'		=> $owner_type,
		'component_id'	=> $owner_id,
		'type'			=> $type,
		'status'		=> $status
	));
}

/**
 * Get count of all galleries which have public status
 * we use it on directory
 * 
 */
function mpp_get_public_gallery_count() {

	return mpp_get_gallery_count( array(
		'status' => 'public'
	));
}

/**
 * Get all gallery Ids for members|groups etc
 * 
 * @param type $args
 * @see mpp_get_object_ids for parameters 
 * @return array of gallery IDS
 */
function mpp_get_gallery_ids( $args ) {

	return mpp_get_object_ids( $args, mpp_get_gallery_post_type() );
}

/**
 * Get total gallery count for user|group etc
 * 
 * @param type $args
 * @see mpp_get_object_count for parameter details
 * @return type
 * 
 * @todo to save queries, in future, I believe It will be a better option to save the count in user meta, i have left it for now to avoid any syn issue
 * We will do it when we introduce mpp-tools
 * 
 * 
 */
function mpp_get_gallery_count( $args ) {

	return mpp_get_object_count( $args, mpp_get_gallery_post_type() );
}

/**
 * Get the total no. of media in this gallery
 * 
 * @param int $gallery_id
 * @return type
 */
function mpp_gallery_get_media_count( $gallery_id ) {

	return mpp_get_gallery_meta( $gallery_id, '_mpp_media_count', true );
}

/**
 * Update and set the media count for this gallery
 * 
 * @param type $gallery_id
 * @param type $count
 * @return type
 */
function mpp_gallery_update_media_count( $gallery_id, $count ) {

	return mpp_update_gallery_meta( $gallery_id, '_mpp_media_count', $count );
}

/**
 * Increment the media count in gallery by the given numbre
 * 
 * @param type $gallery_id
 * @param type $count
 * @return type
 */
function mpp_gallery_increment_media_count( $gallery_id, $count = 1 ) {

	return mpp_gallery_change_media_count( $gallery_id, $count );
}

/**
 * Decrement the media count in gallery by the given numbre
 * 
 * @param type $gallery_id
 * @param type $count
 * @return type
 */
function mpp_gallery_decrement_media_count( $gallery_id, $count = -1 ) {

	return mpp_gallery_change_media_count( $gallery_id, $count );
}

/**
 * Change the media count in gallery by the given number
 * 
 * @param type $gallery_id
 * @param type $count
 * @return type
 */
function mpp_gallery_change_media_count( $gallery_id, $count = 1 ) {

	if ( ! $gallery_id ) {
		return;
	}

	$current_count = absint( mpp_gallery_get_media_count( $gallery_id ) );

	$updated_count = $current_count + $count;

	if ( $updated_count < 0 ) {
		$updated_count = 0; //no media
	}

	return mpp_update_gallery_meta( $gallery_id, '_mpp_media_count', $updated_count );
}

/**
 * Mark a Gallery as sorted
 * 
 */
function mpp_mark_gallery_sorted( $gallery_id ) {

	return mpp_update_gallery_meta( $gallery_id, '_mpp_is_sorted', 1 );
}

/**
 * Mark a Gallery as sorted
 * 
 */
function mpp_is_gallery_sorted( $gallery_id ) {

	return mpp_get_gallery_meta( $gallery_id, '_mpp_is_sorted', true );
}

/**
 * Get the id of the latest media in the given gallery
 * 
 * @param type $gallery_id
 * 
 * @return boolean|int false or the media id
 */
function mpp_gallery_get_latest_media_id( $gallery_id ) {

	if ( ! $gallery_id ) {
		return false;
	}

	$media = get_posts( array(
		'post_parent'		=> $gallery_id,
		'meta_key'			=> '_mpp_is_mpp_media',
		'post_type'			=> mpp_get_media_post_type(),
		'post_status'		=> 'any',
		'meta_value'		=> 1,
		'posts_per_page'	=> 1,
		'fields'			=> 'ids'
	) );

	if ( ! empty( $media ) ) {
		return array_pop( $media ); //
	}

	return false;
}

/**
 * Generate/Display breadcrumb 
 * @param array $args
 * @return string|null
 */
function mpp_gallery_breadcrumb( $args = null ) {

	$default = array(
		'separator'		=> '/',
		'before'		=> '',
		'after'			=> '',
		'show_home'		=> false
	);

	$args = wp_parse_args( $args, $default );
	extract( $args );

	$crumbs = array();

	$component = mpp_get_current_component();
	$component_id = mpp_get_current_component_id();

	if ( mediapress()->is_bp_active() && bp_is_active( 'groups' ) && bp_is_group() ) {
		$name = bp_get_group_name( groups_get_current_group() );
	} elseif ( mediapress()->is_bp_active() && bp_is_user() ) {
		$name = bp_get_displayed_user_fullname();
	} elseif ( $component == 'sitewide' ) {
		$name = '';
	}

	$my_or_his_gallery = '';

	if ( $name ) {
		$my_or_his_gallery = sprintf( __( "%s's gallery", 'mediapress' ), $name );
	}

	if ( function_exists( 'bp_is_my_profile' ) && bp_is_my_profile() ) {
		$my_or_his_gallery = __( 'Your Galleries', 'mediapress' );
	}

	if ( mpp_is_media_management() ) {
		$crumbs[] = ucwords( mediapress()->get_edit_action() );
	}

	if ( mpp_is_single_media() ) {
		$media = mpp_get_current_media();

		if ( mpp_is_media_management() ) {
			$crumbs[] = sprintf( '<a href="%s">%s</a>', mpp_get_media_permalink( $media ), mpp_get_media_title( $media ) );
		} else {
			$crumbs[] = sprintf( '<span>%s</span>', mpp_get_media_title( $media ) );
		}
	}

	if ( mpp_is_gallery_management() ) {
		$crumbs[] = ucwords( mediapress()->get_edit_action() );
	}

	if ( mpp_is_single_gallery() ) {
		$gallery = mpp_get_current_gallery();

		if ( mpp_is_gallery_management() || mpp_is_single_media() ) {
			$crumbs[] = sprintf( '<a href="%s">%s</a>', mpp_get_gallery_permalink( $gallery ), mpp_get_gallery_title( $gallery ) );
		} else {
			$crumbs[] = sprintf( '<span>%s</span>', mpp_get_gallery_title( $gallery ) );
		}
	}

	if ( $my_or_his_gallery ) {
		$crumbs [] = sprintf( '<a href="%s">%s</a>', mpp_get_gallery_base_url( $component, $component_id ), $my_or_his_gallery );
	}

	if ( count( $crumbs ) <= 1 && ! $show_home ) {
		return;
	}

	$crumbs = array_reverse( $crumbs );

	echo join( $separator, $crumbs );
}

/**
 * Should we show gallery description on single gallery pages?
 * 
 * @param type $gallery
 * @return boolean
 */
function mpp_show_gallery_description( $gallery = false ) {

	$gallery = mpp_get_gallery( $gallery );

	$show = mpp_get_option( 'show_gallery_description' ); //under theme tab in admin panel

	return apply_filters( 'mpp_show_gallery_description', $show, $gallery );
}
