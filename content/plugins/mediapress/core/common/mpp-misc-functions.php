<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* * *
 * Common DB Queries and helpers for queries
 */

/**
 * Get an array of Gallery ids or Media ids based on other params
 * 
 * @global type $wpdb
 * 
 * @param type $args {
 *  @type component string|array comma separated sting or array of components eg 'groups,members' or array('groups', 'members' )
 *  @type component_id int numeric component id (user id or group id)
 *  @type status string|array comma separated list or array of statuses e.g. 'public,private,friends' or array ( 'public', 'private', 'friends' )
 *  @type type   string|array comma separated list or array of media types e.g 'audio,video,photo' or array ( 'audio', 'video', 'photo' )
 * 
 * @param string $post_type
 * 
 * @return mixed array of gallery or media post ids
 */
function mpp_get_object_ids ( $args, $post_type ) {

	global $wpdb;

	$post_type_sql = '';

	$sql = array();

	$default = array(
		'component'		=> '',
		'component_id'	=> false,
		'status'		=> '',
		'type'			=> '',
		'post_status'	=> 'publish'
	);
	
	//if component is set to user, we can simply avoid component query 
	//may be next iteration someday

	$args = wp_parse_args( $args, $default );

	extract( $args );

	if ( ! $status ) {
		if ( $component && $component_id ) {
			$status = mpp_get_accessible_statuses( $component, $component_id, get_current_user_id() );
		} else {
			$status = array_keys( mpp_get_active_statuses() );
		}
	}

	if ( ! $component ) {
		$component = array_keys( mpp_get_active_components() );
	}

	if ( ! $type ) {
		$type = array_keys( mpp_get_active_types() );
	}

	//do we have a component set
	if ( $component ) {
		$sql [] = mpp_get_tax_sql( $component, mpp_get_component_taxname() );
	}


	//do we have a component set
	if ( $status ) {
		$sql [] = mpp_get_tax_sql( $status, mpp_get_status_taxname() );
	}

	//for type, repeat it
	if ( $type ) {
		$sql [] = mpp_get_tax_sql( $type, mpp_get_type_taxname() );
	}


	$post_type_sql = $wpdb->prepare( "SELECT DISTINCT ID as object_id FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s", $post_type, $post_status );

	//if a user or group id is given
	if ( $component_id ) {
		$post_type_sql = $wpdb->prepare( "SELECT DISTINCT p.ID  as object_id FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE p.post_type= %s AND p.post_status = %s AND pm.meta_key=%s and pm.meta_value=%d", $post_type, $post_status, '_mpp_component_id', $component_id );
	}
	//$sql[] = $post_type_sql;
	$new_sql = $join_sql = ''; //array();
	//let us generate inner sub queries
	if ( $sql ) {
		$join_sql = ' (' . join( ' AND object_id IN (', $sql );
	}

	//we need to append the ) for closing the sub queries
	for ( $i = 0; $i < count( $sql ); $i++ ) {
		$join_sql .=')';
	}


	$new_sql = $post_type_sql;

	//if the join sql is present, let us append it

	if ( $join_sql ) {
		$new_sql .= ' AND ID IN ' . $join_sql;
	}

	return $wpdb->get_col( $new_sql );
}

/**
 *  Get total galleries|media based on other parameters
 * 
 * @param type $args {
 *  @type component string|array comma separated sting or array of components eg 'groups,members' or array('groups', 'members' )
 *  @type component_id int numeric component id (user id or group id)
 *  @type status string|array comma separated list or array of statuses e.g. 'public,private,friends' or array ( 'public', 'private', 'friends' )
 *  @type type   string|array comma separated list or array of media types e.g 'audio,video,photo' or array ( 'audio', 'video', 'photo' )
 * @param string $post_type
 * 
 * @return int total no of posts
 */
function mpp_get_object_count ( $args, $post_type ) {

	global $wpdb;

	$post_type_sql = '';

	$sql = array();

	$default = array(
		'component'		=> '',
		'component_id'	=> false,
		'status'		=> '',
		'type'			=> '',
		'post_status'	=> 'publish'
	);
	
	//if component is set to user, we can simply avoid component query 
	//may be next iteration someday

	$args = wp_parse_args( $args, $default );

	extract( $args );

	if ( ! $status ) {
		if ( $component && $component_id ) {
			$status = mpp_get_accessible_statuses( $component, $component_id, get_current_user_id() );
		} else {
			$status = array_keys( mpp_get_active_statuses() );
		}
	}

	if ( ! $component ) {
		$component = array_keys( mpp_get_active_components() );
	}
	
	if ( ! $type ) {
		$type = array_keys( mpp_get_active_types() );
	}

	//do we have a component set
	if ( $component ) {
		$sql [] = mpp_get_tax_sql( $component, mpp_get_component_taxname() );
	}

	//do we have a component set
	if ( $status ) {
		$sql [] = mpp_get_tax_sql( $status, mpp_get_status_taxname() );
	}

	//for type, repeat it
	if ( $type ) {
		$sql [] = mpp_get_tax_sql( $type, mpp_get_type_taxname() );
	}


	//we need to find all the object ids which are present in these terms
	//since mysql does not have intersect clause and inner join will be causing too large data set
	//let us use another apprioach for now
	//in our case
	//theere are 3 taxonomies
	//so we will be looking for the objects appearing thrice

	$tax_object_sql = " (SELECT DISTINCT t.object_id FROM (" . join( " UNION ALL ", $sql ) . ") AS t GROUP BY object_id HAVING count(*) >=3 )";

	$post_type_sql = $wpdb->prepare( "SELECT COUNT( DISTINCT ID ) FROM {$wpdb->posts} WHERE post_type = %s AND post_status =%s", $post_type, $post_status );

	//if a user or group id is given
	if ( $component_id ) {
		$post_type_sql = $wpdb->prepare( "SELECT COUNT( DISTINCT p.ID ) AS total FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE p.post_type= %s AND p.post_status = %s AND pm.meta_key=%s and pm.meta_value=%d", $post_type, $post_status, '_mpp_component_id', $component_id );
	}

	//$sql[] = $post_type_sql;
	$new_sql = $join_sql = ''; //array();
	//let us generate inner sub queries
	/* if ( $sql )
	  $join_sql	 = ' (' . join( ' AND object_id IN (', $sql );

	  //we need to append the ) for closing the sub queries
	  for ( $i = 0; $i < count( $sql ); $i++ )
	  $join_sql .=')';

	 */
	$join_sql = $tax_object_sql;
	$new_sql = $post_type_sql;

	//if the join sql is present, let us append it
	if ( $join_sql ) {
		$new_sql .= ' AND ID IN ' . $join_sql;
	}

//echo $new_sql;
	return $wpdb->get_var( $new_sql );
}

function mpp_get_adjacent_object_id ( $args, $post_type ) {

	global $wpdb;

	$post_type_sql = '';

	$sql = array();

	$default = array(
		'component'		=> '',
		'component_id'	=> false,
		'status'		=> mpp_get_accessible_statuses( mpp_get_current_component(), mpp_get_current_component_id() ),
		'type'			=> '',
		'post_status'	=> 'any',
		'next'			=> true,
		'object_id'		=> '', //given post id
		'object_parent'	=> 0,
	);

	if ( $post_type == mpp_get_gallery_post_type() ) {
		$default['post_status'] = 'publish'; //for gallery, the default post type should be published status
	}
	
	//if component is set to user, we can simply avoid component query 
	//may be next iteration someday

	$args = wp_parse_args( $args, $default );

	extract( $args );
	//whether we are looking for next post or previous post
	if ( $next ) {
		$op = '>';
	} else {
		$op = '<';
	}
	
	//do we have a component set
	if ( $component ) {
		$sql [] = mpp_get_tax_sql( $component, mpp_get_component_taxname() );
	}

	//do we have a component set
	if ( $status ) {
		$sql [] = mpp_get_tax_sql( $status, mpp_get_status_taxname() );
	}

	//for type, repeat it
	if ( $type ) {
		$sql [] = mpp_get_tax_sql( $type, mpp_get_type_taxname() );
	}


//so let us build one
	/* $term_object_sql = "SELECT object_id FROM (
	  (SELECT DISTINCT value FROM table_a)
	  UNION ALL
	  (SELECT DISTINCT value FROM table_b)
	  ) AS t1 GROUP BY value HAVING count(*) >= 2; */
	$post_type_sql = $wpdb->prepare( "SELECT DISTINCT ID as object_id FROM {$wpdb->posts} WHERE post_type = %s ", $post_type );

	//if a user or group id is given
	if ( $component_id ) {
		$post_type_sql = $wpdb->prepare( "SELECT DISTINCT p.ID  as object_id FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE p.post_type= %s  AND pm.meta_key=%s and pm.meta_value=%d", $post_type, '_mpp_component_id', $component_id );
	}

	$post_status_sql = '';

	if ( $post_status && $post_status != 'any' ) {
		$post_status_sql = $wpdb->prepare( " AND post_status =%s", $post_status );
	}

	//$sql[] = $post_type_sql;
	$new_sql = $join_sql = ''; //array();
	//let us generate inner sub queries
	if ( $sql ) {
		$join_sql = ' (' . join( ' AND object_id IN (', $sql );
	}

	//we need to append the ) for closing the sub queries
	for ( $i = 0; $i < count( $sql ); $i++ ) {
		$join_sql .=')';
	}


	$new_sql = $post_type_sql . $post_status_sql;

	//if the join sql is present, let us append it

	if ( $join_sql ) {
		$new_sql .= ' AND ID IN ' . $join_sql;
	}

	//for next/prev
	//sorted gallery
	//or by date
	//
	$post = get_post( $object_id );
	$sorted = false;

	if ( $object_parent && mpp_is_gallery_sorted( $object_parent ) ) {
		$new_sql .= $wpdb->prepare( " AND p.menu_order $op %d ", $post->menu_order );

		$sorted = true;
	} else {
		$new_sql .= $wpdb->prepare( " AND p.ID $op %d ", $object_id );
		$sorted = false;
	}
	
	if ( $object_parent ) {
		$new_sql .= $wpdb->prepare( " AND post_parent = %d ", $object_parent );
	}
	
	$oreder_by_clause = '';

	if ( $sorted ) {
		$oreder_by_clause = " ORDER BY p.menu_order ";
	} else {
		$oreder_by_clause = "ORDER BY p.ID";
	}
	
	if ( ! $next ) {
		//for previous
		//find the last element les than give
		$oreder_by_clause .= " DESC ";
	} else {
		$oreder_by_clause .=" ASC";
	}

	if ( ! empty( $new_sql ) ) {
		$new_sql .= $oreder_by_clause . ' LIMIT 0, 1';
	}


	return $wpdb->get_var( $new_sql );
}

/**
 * generate sql for tax queries where terms slugs and taxonomy is given
 * @param type $terms
 * @param type $taxonomy
 * @return string|boolean
 */
function mpp_get_tax_sql ( $terms, $taxonomy ) {

	//for type, repeat it
	if ( $terms ) {
		//if the comma separated terms are given, convert it to array
		$terms = mpp_string_to_array( $terms );

		//prepend underscore to each of the term
		//$terms		 = array_map( 'mpp_underscore_it', $terms );
		//get the term_taxonomy ids array for component
		$term_tax_ids = mpp_get_tt_ids( $terms, $taxonomy );

		$objects_in_terms_sql = mpp_get_objects_in_terms_sql( $term_tax_ids );

		return $objects_in_terms_sql;

		//find all posts in associated with this tt
	}

	return false;
}

/**
 * Return an array of Terms Ids for given term slugs (an array of slugs can be passed)
 * @global type $wpdb
 * @param type $terms
 * @return type
 */
function mpp_get_term_ids ( $terms, $taxonomy ) {

	$terms_data = mpp_get_terms_data( $taxonomy );

	if ( ! is_array( $terms ) ) {
		$terms = explode( ',', $terms );
	}

	$ids = array();

	foreach ( $terms as $term ) {

		if ( ! empty( $terms_data[ $term ] ) ) {
			$ids[] = $terms_data[ $term ]->get_id();
		}
	}

	return $ids;
}

/**
 * Returns an array of term_taxonomy_ids for the terms under given taxonomy
 * @param type $terms
 * @param type $taxonomy
 * @todo need to remove IN and use ORed Condition, just leaving as I don't need it now
 */
function mpp_get_tt_ids ( $terms, $taxonomy ) {

	$terms_data = mpp_get_terms_data( $taxonomy );

	if ( ! is_array( $terms ) ) {
		$terms = explode( ',', $terms );
	}

	$ids = array();

	foreach ( $terms as $term ) {

		if ( ! empty( $terms_data[ $term ] ) ) {
			$ids[] = $terms_data[ $term ]->get_tt_id();
		}
	}

	return $ids;
}

/**
 * Returns sql for finding objects in given term taxonomy ids
 * We Use it when finding media/galleries for perticular term
 * @global type $wpdb
 * @param type $term_taxonomy_ids
 * @param type $taxonomies
 * @param type $args
 * @return WP_Error| sql string
 */
function mpp_get_objects_in_terms_sql ( $term_taxonomy_ids ) {

	global $wpdb;

	if ( ! is_array( $term_taxonomy_ids ) ) {
		$term_taxonomy_ids = (array) $term_taxonomy_ids;
	}

	/*
	  foreach ( (array) $taxonomies as $taxonomy ) {
	  if ( !taxonomy_exists( $taxonomy ) )
	  return new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy', 'mediapress' ) );
	  } */

	$term_taxonomy_ids = array_map( 'intval', $term_taxonomy_ids );


	$term_taxonomy_ids = "'" . implode( "', '", $term_taxonomy_ids ) . "'";

	$sql = "SELECT DISTINCT tr.object_id AS object_id  FROM $wpdb->term_relationships as tr WHERE tr.term_taxonomy_id IN ($term_taxonomy_ids) ";

	return $sql;
}


/**
 * 
 * @param type $taxonomy the name of actual WordPress taxonomy we use for various mpp related things
 * 
 * @return MPP_Taxonomy[]
 */
function mpp_get_terms_data ( $taxonomy ) {
	
	$data = array();
	
	if ( empty( $taxonomy ) ) {
		return $data;
	}

	switch ( $taxonomy ) {

		case mpp_get_status_taxname():

			$data = mediapress()->statuses;
			break;
		
		case mpp_get_type_taxname():
			$data = mediapress()->types;
			break;
		
		case mpp_get_component_taxname():
			$data = mediapress()->components;
			break;
	}

	return $data;
}
