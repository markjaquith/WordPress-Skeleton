<?php

/**
 * Get the term_slug from term id without going through extra database query
 * We fetch it from our stored srray of MPP_Terms in mediapress object
 * 
 * @see core/class-mpp-taxonomy.php for implemetation details
 * 
 * For Internal use only
 * 
 * @access private
 * @param type $term_slug
 * @param type $mpp_terms_list
 * @return int
 * 
 */
function mpp_get_term_id_by_slug( $term_slug, $mpp_terms_list ) {
	
	//if the status id is given we scan into mediapress->statuses array for it
	$term_id = 0;//non existant
	
	if ( ! $term_slug || ! is_string( $term_slug ) ) {
		return $term_id;
	}
	
	$mpp = mediapress();
	
	if ( ! isset( $mpp->{$mpp_terms_list} ) ) {
		return $term_id;
	}
	
	
	$mpp_terms = $mpp->{$mpp_terms_list};//
	
	foreach ( $mpp_terms as $mpp_term ) {
		
		if ( $mpp_term->get_slug() == $term_slug ) {
			$term_id = $mpp_term->get_id ();
			break;
			
		}
	}
	
	return absint( $term_id );
}


/**
 * Get the term_slug from term id without going through extra database query
 * We fetch it from our stored srray of MPP_Terms in mediapress object
 * 

 * 
 * For Internal use only
 * 
 * @access private
 * @param type $term_id
 * @param type $mpp_terms_list
 * @return string
 * 
 */
function mpp_get_term_slug( $term_id, $mpp_terms_list ){
	
	//if the status id is given we scan into mediapress->statuses array for it
	$slug = '';//non existant
	if ( ! $term_id || ! is_numeric( $term_id ) ) {
		return $slug;
	}
	
	
	$mpp = mediapress();
	
	if ( ! isset( $mpp->{$mpp_terms_list} ) ) {
		return $slug;
	}
	
	
	$mpp_terms = $mpp->{$mpp_terms_list};//
	
	foreach ( $mpp_terms as $mpp_term ) {
		
		if ( $mpp_term->get_id() == $term_id ) {
			$slug = $mpp_term->get_slug ();
			break;
			
		}
	}
	
	return $slug;
}


function mpp_get_status_term_slug( $status_id ) {
	
	return mpp_get_term_slug( $status_id, 'statuses' );
}
function mpp_get_type_term_slug( $type_id ) {
	
	return mpp_get_term_slug( $type_id, 'types' );
}
function mpp_get_component_term_slug( $component_id ) {
	
	return mpp_get_term_slug( $component_id, 'components' );
}
/**
 * 
 * @param string $key private|public etc
 * 
 * @return MPP_Status|Boolean
 */
function mpp_get_status_object( $key ) {
	
	if ( ! $key ) {
		return '';
	}
	
	if ( is_numeric( $key ) ) {
		$key = mpp_get_status_term_slug( $key );
	}
	
	$mpp = mediapress();
	
	if ( $key && isset( $mpp->statuses[ $key ] ) && is_a( $mpp->statuses[ $key ], 'MPP_Status' ) ) {
		return $mpp->statuses[ $key ];
	}
	
	return false;
}

/**
 * 
 * @param string $key members|groups etc
 * 
 * @return MPP_Component|boolean
 */
function mpp_get_component_object( $key ) {

	if ( ! $key ) {
		return '';
	}
	
	if ( is_numeric( $key ) ) {
		$key = mpp_get_component_term_slug( $key );
	}
	
	$mpp = mediapress();
	
	if ( isset( $mpp->components[ $key ] ) && is_a( $mpp->components[ $key ], 'MPP_Component' ) ) {
		return $mpp->components[ $key ];
	}
	
	return false;
}

/**
 * 
 * @param string|int $key component term_name keys members|groups  etc
 * 
 * @return MPP_Type|boolean
 */
function mpp_get_type_object( $key ) {
	
	if ( ! $key ) {
		return '';
	}
	
	if ( is_numeric( $key ) ) {
		$key = mpp_get_type_term_slug( $key );
	}
	
	$mpp = mediapress();
	
	if ( isset( $mpp->types[ $key ] ) && is_a( $mpp->types[ $key ], 'MPP_Type' ) ) {
		return $mpp->types[$key];
	}
	
	return false;
}


/**
 * Get allowed file extensions for this type as array
 * 
 * @param type $type audio|photo|video etc
 * @return array( 'jpg', 'gif', ..)//allowed extensions for a given type
 */
function mpp_get_allowed_file_extensions( $type ) {
	
	if ( ! mpp_is_registered_type( $type ) ) {  //should we only do it for active types?
		return array();
	}
		
	$type_object = mpp_get_type_object( $type );
	
	return  $type_object->get_allowed_extensions() ;
}

/**
 * Get the list of allowed file extensions
 * 
 * @param type $type
 * @param type $separator
 * @return string
 */
function mpp_get_allowed_file_extensions_as_string( $type, $separator = ',' ) {
	
	$extensions = mpp_get_allowed_file_extensions( $type );
	
	if ( empty( $extensions ) ) {
		return '';
	}
	
	return join( $separator, $extensions );
}

/** Let us improve the performance*/
/**
 * Cache all terms used by MediaPress to avoid the query overhead
 * 
 * we know that we won't have more than 10-15 terms, so It is perfectly ok to store them in cache
 * in future, we may only want to includde few fields
 * 
 */
function _mpp_cache_all_terms(){
	
	$taxonomies = _mpp_get_all_taxonomies();
	
	$args = array( 'hide_empty' => false, );
	
	$terms = get_terms( $taxonomies, $args );
	
	$new_terms = _mpp_build_terms_array( $terms );
	
	foreach ( $taxonomies as $tax ) {
		
		if ( empty( $new_terms[ $tax ] ) ) {
			$new_terms[$tax] = array();//avoid cache miss causing recursion in _mpp_get_all_terms
		}
	}
	
	foreach ( $new_terms as $taxonomy => $tax_terms ) {
		wp_cache_set( 'mpp_taxonomy_'. $taxonomy, $tax_terms, 'mpp' );
	}
}

function _mpp_cache_term( $term ) {
	
	$taxonomy = $term->taxonomy;
	
	$terms = _mpp_get_terms( $taxonomy );
	
	$terms[ mpp_strip_underscore( $term->slug ) ] = $term;
	
	wp_cache_set( 'mpp_taxonomy_'. $taxonomy, $terms, 'mpp' );
}

function _mpp_get_term( $slug_or_id, $taxonomy ) {
	
	$term = '';
	
	if ( ! $slug_or_id ) {
		return false;
	}
	
	$terms = _mpp_get_terms( $taxonomy );
	
	if ( is_numeric( $slug_or_id ) ) {
		foreach ( $terms as $term_item ) {
			
			if ( $slug_or_id == $term_item->term_id ) {
				
				$term = $term_item;
				break;
			}
		}
		//search and return
		
	} else {
		$term = isset( $terms[ $slug_or_id ] ) ? $terms[ $slug_or_id ] : '';	
	}

	return $term;
}

function _mpp_get_terms( $taxonomy ) {
	
	if ( ! $taxonomy ||  ! in_array( $taxonomy, _mpp_get_all_taxonomies() ) ) {
		return false;
	}
	
	$terms = wp_cache_get( 'mpp_taxonomy_'. $taxonomy, 'mpp' );
	
	if ( $terms !== false ) {
		return $terms;
	}
	//if we are here, It is a cache miss
	_mpp_cache_all_terms();
	
	return _mpp_get_terms( $taxonomy );//
	
}
/**
 * Rebuilds the default terms array keyed by taxonomy/slug
 * @param type $terms
 * @return type
 */
function _mpp_build_terms_array( &$terms ) {
	
	//builds like
	//$array('mpp-taxonomy'=> array( 'term_slug' => $term_object ));
	
	$new_terms = array();
	
	foreach ( $terms as $term ) {
		
		$new_terms[ $term->taxonomy ][ mpp_strip_underscore( $term->slug ) ] = $term;
	}
	
	return $new_terms;
}
/**
 * Get an array of the names of mpp core taxonomies ( literally mpp-status, mpp-component, mpp-type )
 * @return type
 */
function _mpp_get_all_taxonomies() {
	
	return apply_filters( 'mpp_get_all_taxonomies', array( mpp_get_status_taxname(), mpp_get_type_taxname(), mpp_get_component_taxname() ) );
}

/**
 * Translates our terminology to internal taxonomy( for e.f component translates to mpp-component and so on )
 * @param string $name
 * @return type
 */
function mpp_translate_to_taxonomy( $name ) {
	
	$tax_name = '';
	/**
	 * @todo Think about the possiblity to name the functions dynamicallly like mpp_get_{$name}_taxname() for flexibility
	 */
	if ( $name == 'component' ) {
		$tax_name = mpp_get_component_taxname ();
	} elseif ( $name == 'type' ) {
		$tax_name = mpp_get_type_taxname ();
	} elseif ( $name == 'status' ) {
		$tax_name = mpp_get_status_taxname ();
	}
	
	return $tax_name;
}
