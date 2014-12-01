<?php
/**
* Venue related functions
*@package venue-functions
*/

/**
* Returns the ID of the venue of an event.
*
* Can be used inside the loop to output the venue id of the current event by not passing an ID.
* Otherwise it returns the venue ID of the passed event ID.
*
* ### Examples
* This function can be used inside the Loop to return the venue ID of the current event
* <code>
*    $current_events_venue_id = eo_get_venue();
* </code>  
* To obtain the venue ID of event 23:
* <code>
*    $venue_id = eo_get_venue(23);
* </code>
* @since 1.0.0
* @param int $post_id The event (post) ID. Uses current event if empty.
* @return int The corresponding venue (event-venue term) ID
 */
function eo_get_venue($event_id=''){
	global $post;
	$event = $post;

	if( !empty($event_id) ){
		$post_id = $event_id;
	}else{
		$post_id = (isset($post->ID) ? $post->ID : 0);
	}

	if( empty($post_id) )
		return false;

	$venue = get_the_terms($post_id,'event-venue');

	if ( empty($venue) || is_wp_error( $venue ) )
		return false;

	$venue = array_pop($venue);

	return (int) $venue->term_id;	
}


/**
* Returns the slug of the venue of an event.
*
* When used without an argument it uses the event specified in the global $post (e.g. current event in the loop).
* Can be used inside the loop to output the venue id of the current event.
* 
* ### Examples
* Inside the loop, you can output the current event's venue
* <code>
*   <?php echo eo_get_venue_slug(); ?> 
* </code>    
* Get the last start date of event with id 7
* <code>
*   <?php $venue_slug = eo_get_venue_slug(7); ?>
* </code>
* 
* @since 1.0.0
* @param int $post_id The event (post) ID. Uses current event if empty.
* @return int The corresponding venue (event-venue term) slug
 */
function eo_get_venue_slug($event_id=''){
	global $post;
	$event = $post;

	if( !empty($event_id) ){
		$post_id = $event_id;
	}else{
		$post_id = (isset($post->ID) ? $post->ID : 0);
	}

	$venue = get_the_terms($post_id,'event-venue');

	if ( empty($venue) || is_wp_error( $venue ) )
		return false;

	$venue = array_pop($venue);

	return $venue->slug;
}



/**
* A utility function for getting the venue ID from a venue ID or slug.
* Useful for when we don't know which is being passed to us, but we want the ID.
* IDs **must** be cast as integers
* @since 1.6
*
* @param mixed $venue_slug_or_id The venue ID as an integer. Or Slug as string. Uses venue of current event if empty.
* @return int The corresponding venue (event-venue term) ID or false if not found.
 */
function eo_get_venue_id_by_slugorid($venue_slug_or_id=''){

	$venue = $venue_slug_or_id;

	if( empty($venue) )
		return eo_get_venue();

	if( is_int($venue) )
		return (int) $venue;

	$venue = eo_get_venue_by('slug', $venue);

	if( $venue )
		return (int) $venue->term_id;
	
	return false;
}


/**
 * Get all venue data from database by venue field and data. This acts as a simple wrapper for  {@see `get_term_by()`}
 *
 * Warning: `$value` is not escaped for 'name' `$field`. You must do it yourself, if required.
 * 
 * If `$value` does not exist for that `$field`, the return value will be false other the term will be returned.
 *
 * ###Example
 * Get the venue ID by slug (A better way is to use {@see `eo_get_venue_id_by_slugorid()`}
 * <code>
 *     $venue = eo_get_venue_by('slug','my-venue-slug'); 
 *     if( $venue )
 *          $venue_id = (int) $venue->term_id;
 *</code>
 *
 * @uses get_term_by()
 * @since 1.6
 *
 * @param string $field Either 'slug', 'name', or 'id'
 * @param string|int $value Search for this term value
 * @param string $output Constant OBJECT, ARRAY_A, or ARRAY_N
 * @param string $filter Optional, default is raw or no WordPress defined filter will applied.
 * @return mixed Term Row from database. Will return false if $taxonomy does not exist or $term was not found.
 */
function eo_get_venue_by($field,$value,$output = OBJECT, $filter = 'raw' ){
	$venue = get_term_by($field, $value, 'event-venue',$output, $filter);
	return $venue;
}


/**
* Returns the name of the venue of an event.
* If used without any arguments uses the venue of the current event.
*
* Returns the name of a venue specified by it's slug or ID. If used inside the loop, it can return the name of the current post's venue. If specifying the venue by ID, **the ID must be an integer**.
* This function behaves differently to {@see `eo_get_venue_slug()`} which takes the event ID, rather than venue ID or slug, as an optional argument.
*
* ### Examples
* Inside the loop, you can output the current event's venue
* <code>
*      <?php echo eo_get_venue_name(); ?>
* </code>   
* To get the name of event with id 7, you can use `eo_get_venue` to obtain the venue ID of the event.
* <code>
*      <?php 
*         $venue_id = eo_get_venue(7); 
*         $venue_name = eo_get_venue_name(%venue_id); 
*       ?>
* </code>
* @since 1.0.0
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
* @return string The name of the corresponding venue
 */
function eo_get_venue_name($venue_slug_or_id=''){
	$venue_id =  eo_get_venue_id_by_slugorid($venue_slug_or_id);
	$venue = get_term($venue_id,'event-venue');
	
	if ( empty($venue) || is_wp_error( $venue ) )
		return false;

	return $venue->name;
}

/**
* Echos the venue of the event
*
* @uses eo_get_venue_name()
* @param (int) venue id or (string) venue slug
*
 * @since 1.0.0
 */
function eo_venue_name($venue_slug_or_id=''){
	echo  eo_get_venue_name($venue_slug_or_id);
}


/**
* Returns the description of the description of an event.
* If used with any arguments uses the venue of the current event.
*
* Returns the description of a venue specified by it's slug or ID. When used without an argument it uses the event specified in the `global $post` (i.e. the current event in the Loop). If specifying the 
* venue by ID, **the ID must be an integer**.
*
* ###Example
* <code>
*     <?php 
*     $event_id = 7;
*     $venue_id = eo_get_venue( $event_id );
*     echo eo_get_venue_description( $venue_id );
*     
*     //The following displays the description for the venue with **ID** '12'
*     echo eo_get_venue_description( 12 );
*     
*     //The following displays the description for the venue with **slug** '12'
*     echo eo_get_venue_description( '12' );
*     ?>
* </code>
* @since 1.0.0
* @see `eo_venue_description()`
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
* @return string The description. of the corresponding venue
 */
function eo_get_venue_description($venue_slug_or_id=''){
	$venue_id =  eo_get_venue_id_by_slugorid($venue_slug_or_id);
	$description = eo_get_venue_meta($venue_id,'_description');
	$description = wptexturize($description);
	$description = convert_chars($description);
	$description = wpautop($description);
	$description = shortcode_unautop($description);
	$description = do_shortcode($description);
	return $description;
}

/**
* Prints the name of the description of an event.
* Can be used inside the loop to output the 
* venue id of the current event.
* @since 1.0.0
* @uses eo_get_venue_description()
*
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
 */
function eo_venue_description($venue_slug_or_id=''){
	echo  eo_get_venue_description($venue_slug_or_id);
}


function eo_get_venue_excerpt( $venue_slug_or_id='', $excerpt_length = 55 ){

	$venue_id =  eo_get_venue_id_by_slugorid( $venue_slug_or_id );

	$text  = eo_get_venue_meta($venue_id,'_description');
	$text = strip_shortcodes( $text );
	$text = str_replace(']]>', ']]&gt;', $text);

	$excerpt_length = apply_filters( 'excerpt_length', $excerpt_length );
	$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		
	$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

	return apply_filters( 'eventorganiser_venue_excerpt', $text, $venue_id );
}




/**
* Returns an latitude-longtitude array (keys 'lat', 'lng')
* If used with any arguments uses the venue of the current event.
*
* Returns a latitude-longitude array of a venue specified by it's slug or ID. When used without an argument it uses the event specified in the `global $post` (i.e. the current event in the Loop). If 
* specifying the venue by ID, **the ID must be an integer**.
* @since 1.0.0
*
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
* @return array Array with keys 'lat' and 'lng' with corresponding float values.
 */
function eo_get_venue_latlng($venue_slug_or_id=''){	
	$lat = eo_get_venue_lat($venue_slug_or_id);
	$lng = eo_get_venue_lng($venue_slug_or_id);	
	return array('lat'=>$lat,'lng'=>$lng);
}

/**
* Returns the latitude co-ordinate of a venue.
* If used with any arguments uses the venue of the current event.
*
* Returns the latitude of a venue specified by it's slug or ID. When used without an argument it uses the event specified in the `global $post` (i.e. the current event in the Loop). If specifying the 
* specifying the venue by ID, **the ID must be an integer**.
* @since 1.0.0
*
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
* @return float The latitude of the venue as a float. 0 If it doesn't exist.
 */
function eo_get_venue_lat($venue_slug_or_id=''){
	$venue_id =  eo_get_venue_id_by_slugorid($venue_slug_or_id);
	$lat = eo_get_venue_meta($venue_id,'_lat');
	$lat =  ! empty($lat) ? $lat : 0;
	return $lat;
}

/**
* Returns the longtitude co-ordinate of a venue.
* If used with any arguments uses the venue of the current event.
*
* Returns the longtitude of a venue specified by it's slug or ID. When used without an argument it uses the event specified in the `global $post` (i.e. the current event in the Loop). If specifying the 
* specifying the venue by ID, **the ID must be an integer**.
* @since 1.0.0
*
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
* @return float The longtitude of the venue as a float. 0 If it doesn't exist.
 */
function eo_get_venue_lng($venue_slug_or_id=''){
	$venue_id =  eo_get_venue_id_by_slugorid($venue_slug_or_id);
	$lng = eo_get_venue_meta($venue_id,'_lng');
	$lng =  ! empty($lng) ? $lng : 0;
	return $lng;
}


/**
* Prints the latitude co-ordinate of a venue.
* If used with any arguments uses the venue of the current event.
* @uses eo_get_venue_lat()
* @since 1.0.0
*
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
 */
function eo_venue_lat($venue_slug_or_id=''){
	echo eo_get_venue_lat($venue_slug_or_id);
}


/**
* Prints the longtitude co-ordinate of a venue.
* If used with any arguments uses the venue of the current event.
* @uses eo_get_venue_lng()
* @since 1.0.0
*
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
 */
function eo_venue_lng($venue_slug_or_id=''){
	echo eo_get_venue_lng($venue_slug_or_id);
}


/**
* Returns the permalink of a venue
* If used with any arguments uses the venue of the current event.
* @uses get_term_link()
* @since 1.0.0
*
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
* @return string Link of the venue page
 */
function eo_get_venue_link($venue_slug_or_id=''){
	$venue_id =  eo_get_venue_id_by_slugorid($venue_slug_or_id);
	return get_term_link( $venue_id, 'event-venue' );
}


/**
* Prints the permalink of a venue
* If used with any arguments uses the venue of the current event.
* @uses eo_get_venue_link()
* @since 1.0.0
*
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
 */
function eo_venue_link($venue_slug_or_id=''){
	$venue_id =  eo_get_venue_id_by_slugorid($venue_slug_or_id);
	echo  eo_get_venue_link($venue_slug_or_id);
}


/**
* Returns an array with address details of the event's venue.
* The keys consist of
*
* * 'address'
* * 'city'
* * 'state' - the state/province/county of the venue
* * 'postcode'
* * 'country'
*
* If used without any arguments uses the venue of the current event.
* 
* ### Examples
* Return the details of venue 16. **(Please note when using the ID it must be an integer - that is 16 not '16').**
* <code>
*     $address_details = eo_get_venue_address(16); 
*     //$address_details = eo_get_venue_address('16'); This method is incorrect.
* </code>   
* Print the post-code of venue 'my-venue-slug'
* <code>
*     $address_details = eo_get_venue_address('my-venue-slug'); 
*     echo "The post code of 'my-venue-slug' is: ".$address_details['postcode']; 
* </code>   
* Return the details of the venue of event 23 we can use `{@see eo_get_venue()}` to obtain the venue ID.
* <code>
*   $venue_id = eo_get_venue(23); 
*    $address_details = eo_get_venue_address($venue_id); 
* </code>   
* 
* @since 1.0.0
* @param int|string $venue_slug_or_id The venue ID (as an integer) or slug (as a string). Uses venue of current event if empty.
* @return array Array of venue address details
 */
function eo_get_venue_address($venue_slug_or_id=''){
	$address=array();	
	$venue_id =  eo_get_venue_id_by_slugorid($venue_slug_or_id);
	$address_keys = array_keys(_eventorganiser_get_venue_address_fields());
	foreach( $address_keys as $meta_key ){
		$key = trim($meta_key,'_');
		$address[$key] = eo_get_venue_meta($venue_id,$meta_key);
	}
	return $address;
}


/**
 * Retrieve array of venues. Acts as a wrapper for {@link https://codex.wordpress.org/Function_Reference/get_terms get_terms()}, except hide_empty defaults to false.
 *
 * The list of arguments that `$args` can contain, which will overwrite the defaults:
 *
 * * **orderby** - Default is 'name'. Can be name, count, slug, city, state, country, postcode, address 
 * or distance (when used with a {@link http://wp-event-organiser.com/pro-features/event-venue-queries/ proximity-query})
 * * **order** - ASC|DESC Default is ASC.
 * * **hide_empty** - Default is 0 (false)
 * * **exclude** - Default is an empty array. An array, comma- or space-delimited string
 * of term ids to exclude from the return array. If 'include' is non-empty,
 * 'exclude' is ignored.
 * * **include** - Default is an empty array. An array, comma- or space-delimited string
 * of term ids to include in the return array.
 * * **number** - The maximum number of terms to return. Default is to return them all.
 * * **offset** - The number by which to offset the terms query.
 * * **fields** - Default is 'all', which returns an array of term objects.
 * If 'fields' is 'ids' or 'names', returns an array of integers or strings, respectively.
 * *  **slug** - Returns terms whose "slug" matches this value. Default is empty string.
 * * **search** - Returned terms' names will contain the value of 'search',
 * * **case-insensitive**. Default is an empty string.
 *
 * ###Example
 * <code>
 *     $venues = eo_get_venues(); 
 *     
 *     if( $venues ){
 *          echo '<ul>'; 
 *          foreach($venues as $venue): 
 *		  $venue_id = (int) $venue->term_id;
 *               printf('<li> <a href="%s">%s</a>', eo_get_venue_link($venue_id), esc_html($venue->name));
 *          endforeach; 
 *          echo '</ul>';
 *     }
 * </code>        
 * The retreive all venues within 10 miles of Windsor Castle
 * <code>
 *      $meta_query = array(
 *			'proximity' => array(
 *					'center' => eo_remote_geocode( "Windsor [castle]" ),
 *					'distance' => 10,
 *					'unit' => 'miles',
 *					'compare' => '<='
 *			),	
 *      );
 *
 *      $venues = eo_get_venues( array( 'meta_query' => $meta_query ) );
 * </code>
 * See {@link http://wp-event-organiser.com/pro-features/event-venue-queries/ documentation on venue meta queries}.    
 *
 * @uses get_terms()
 * @see eo_remote_geocode()
 * @link http://wp-event-organiser.com/pro-features/event-venue-queries/ Documentation on event-venue meta queries
 * @link https://gist.github.com/3902494 Gist for creating an archive page of all the venues
 * @link https://codex.wordpress.org/Function_Reference/get_terms get_terms()
 * @since 1.0.0
 * @param string|array $args The values of what to search for when returning venues
 * @return array List of Term (venue) Objects
 */
function eo_get_venues($args=array()){
	$args = wp_parse_args( $args, array('hide_empty'=>0, 'fields'=>'all') );
	$venues = get_terms('event-venue',$args);
	if( $venues && !is_wp_error( $venues ) ){
		//Ensure IDs are cast as integers {@link https://github.com/stephenh1988/Event-Organiser/issues/21}
		if( $args['fields'] == 'ids' ){
			$venues = array_map('intval', $venues);
		}elseif( $args['fields'] == 'all' ){
			foreach( $venues as $venue)
				$venue->term_id = (int)$venue->term_id;
		}
	}
	return $venues;
}


/**
 * Updates new venue in the database. 
 *
 * Calls {@see `wp_insert_term()`} to update the taxonomy term
 * Updates venue meta data to database (for 'core' meta keys)
 * The $args is an array - the same as that accepted by {@link https://codex.wordpress.org/Function_Reference/wp_update_term wp_update_term()}
 * The $args array can also accept the following keys: 
 *
 * * description
 * * address
 * * city
 * * state
 * * postcode
 * * country
 * * latitude
 * * longtitude
 *
 * @since 1.4.0
 *
 * @uses wp_update_term() to update venue (taxonomy) term
 * @uses do_action() Calls 'eventorganiser_save_venue' hook with the venue id
 *
 * @param int $venue_id The Term ID of the venue to update
 * @param array $args Array as accepted by wp_update_term and including the 'core' metadata
 * @return array|WP_Error Array of term ID and term-taxonomy ID or a WP_Error on error
 */
	function eo_update_venue($venue_id, $args=array()){

		$term_args = array_intersect_key($args, array('name'=>'','term_id'=>'','term_group'=>'','term_taxonomy_id'=>'','alias_of'=>'','parent'=>0,'slug'=>'','count'=>''));
		$meta_args = array_intersect_key($args, array('description'=>'','address'=>'','postcode'=>'','city'=>'','state'=>'','country'=>'','latitude'=>'','longtitude'=>''));
		$venue_id = (int) $venue_id;


		//Update taxonomy table
		$resp = wp_update_term($venue_id,'event-venue', $term_args);

		if( is_wp_error($resp) ){
			return $resp;
		}

		$venue_id = (int) $resp['term_id'];

		foreach( $meta_args as $key => $value ){
			switch($key):
				case 'latitude':
					$meta_key = '_lat';
					break;
				case 'longtitude':
					$meta_key = '_lng';
					break;
				default:
					$meta_key = '_'.$key;
					break;
			endswitch;

			$validated_value = eventorganiser_sanitize_meta($meta_key, $value);

			update_metadata('eo_venue', $venue_id, $meta_key, $validated_value);		
		}
		/**
		 * Triggered when a venue is created / updated.
		 * 
		 * @param int $venue_id The (term) ID of the venue.
		 */
		do_action('eventorganiser_save_venue',$venue_id);

		return array('term_id' => $venue_id, 'term_taxonomy_id' => $resp['term_taxonomy_id']);
	}


/**
 * Adds a new venue to the database. 
 *
 * Calls {@see `wp_insert_term()`} to create the taxonomy term
 * Adds venue meta data to database (for 'core' meta keys)
 *
 * The $args is an array - the same as that accepted by {@link https://codex.wordpresss.org/Function_Reference/wp_update_term wp_update_term()}
 * The $args array can also accept the following keys: 
 *
 * * description
 * * address
 * * city
 * * state
 * * postcode
 * * country
 * * latitude
 * * longtitude
 *
 * @since 1.4.0
 *
 * @uses `wp_insert_term()` to create venue (taxonomy) term
 * @uses do_action() Calls 'eventorganiser_insert_venue' hook with the venue id
 * @uses do_action() Calls 'eventorganiser_save_venue' hook with the venue id
 * @link https://codex.wordpress.org/Function_Reference/wp_insert_term wp_insert_term()
 *
 * @param string $name the venue to insert
 * @param array $args Array as accepted by wp_update_term and including the 'core' metadata
 * @return array|WP_Error Array of term ID and term-taxonomy ID or a WP_Error on error
 */
	function eo_insert_venue($name, $args=array()){
		$term_args = array_intersect_key($args, array('name'=>'','term_id'=>'','term_group'=>'','term_taxonomy_id'=>'','alias_of'=>'','parent'=>0,'slug'=>'','count'=>''));
		$meta_args = array_intersect_key($args, array('description'=>'','address'=>'','postcode'=>'','city'=>'','state'=>'','country'=>'','latitude'=>'','longtitude'=>''));
	
		$resp = wp_insert_term($name,'event-venue',$term_args);

		if(is_wp_error($resp)){
			return $resp;
		}

		$venue_id = (int) $resp['term_id'];

		foreach( $meta_args as $key => $value ){
			switch($key):
				case 'latitude':
					$meta_key = '_lat';
					break;
				case 'longtitude':
					$meta_key = '_lng';
					break;
				default:
					$meta_key = '_'.$key;
					break;
			endswitch;

			$validated_value = eventorganiser_sanitize_meta($meta_key, $value);

			if( !empty($validated_value) )
				add_metadata('eo_venue', $venue_id, $meta_key, $validated_value, true);		
		}
	
		/**
		 * Triggered when a venue is created.
		 *
		 * @param int $venue_id The (term) ID of the venue.
		 */
		do_action('eventorganiser_insert_venue',$venue_id);
		
		/**
		 * Triggered when a venue is created / updated.
		 *
		 * @param int $venue_id The (term) ID of the venue.
		 */
		do_action('eventorganiser_save_venue',$venue_id);

		return array('term_id' => $venue_id, 'term_taxonomy_id' => $resp['term_taxonomy_id']);
	}

/**
 * Deletes a venue in the database. 
 *
 * Calls {@see `wp_delete_term()`} to delete the taxonomy term
 * Deletes all the venue's meta 
 * 
 * @since 1.4.0
 *
 * @uses wp_delete_term to delete venue (taxonomy) term
 * @uses do_action() Calls 'eventorganiser_delete_venue' hook with the venue id
 *
 * @param int $venue_id the Term ID of the venue to update
 * @return bool|WP_Error false or error on failure. True after sucessfully deleting the venue and its meta data.
 */
	function eo_delete_venue($venue_id){
		global $wpdb;
		$resp =wp_delete_term( $venue_id, 'event-venue');
		if( is_wp_error($resp) || false === $resp ){
			return $resp;
		}
		$venue_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->eo_venuemeta WHERE eo_venue_id = %d ", $venue_id ));

		if ( !empty($venue_meta_ids) ) {
			$in_venue_meta_ids = "'" . implode("', '", $venue_meta_ids) . "'";
			$wpdb->query( "DELETE FROM $wpdb->eo_venuemeta WHERE meta_id IN($in_venue_meta_ids)" );
		}
		
		/**
		 * @ignore 
		 * This should probably be triggered *before* venue is deleted.
		 */
		do_action('eventorganiser_delete_venue',$venue_id);
		
		/**
		 * Triggered when a venue is deleted
		 *
		 * @param int $venue_id The (term) ID of the venue.
		 */
		do_action('eventorganiser_venue_deleted',$venue_id);

		return true;
	}

/**
 * Returns the mark-up for a Google map of the venue (and enqueues scripts).
 * Accepts an arguments array corresponding to the attributes supported by the shortcode.
 * 
 * ### Examples
 * <code>
 *   // Display map of two venues 
 *   <?php echo eo_get_venue_map(array('london-eye','edinburgh-castle')); ?>
 * </code>
 * @since 1.6
 * @link http://wp-event-organiser.com/blog/tutorial/changing-the-venue-map-icon/ Changing the venue map icon
 * @link http://www.stephenharris.info/2012/event-organiser-1-6-whats-new/ Examples of using eo_get_venue_map()
 * @param mixed $venue_slug_or_id The venue ID as an integer. Or Slug as string. Uses venue of current event if empty.
 * @return string The markup of the map. False is no venue found.
 */
function eo_get_venue_map($venue_slug_or_id='', $args=array()){

		//Cast as array to allow multi venue support
		if( $venue_slug_or_id == '%all%' || is_array($venue_slug_or_id) && in_array('%all%',$venue_slug_or_id) ){
			$all_venues = eo_get_venues();
			if( $all_venues )
				$venue_slug_or_id = array_map('intval',wp_list_pluck($all_venues,'term_id'));

		}
		if( !is_array($venue_slug_or_id) )
			$venue_slug_or_id = array( $venue_slug_or_id );

		$venue_ids = array_map('eo_get_venue_id_by_slugorid',$venue_slug_or_id);

		//Map properties
		$args = shortcode_atts( array(
			'zoom' => 15, 'scrollwheel'=>true, 'zoomcontrol'=>true, 'rotatecontrol'=>true,
			'pancontrol'=>true, 'overviewmapcontrol'=>true, 'streetviewcontrol'=>true,
			'maptypecontrol'=>true, 'draggable'=>true,'maptypeid' => 'ROADMAP',
			'width' => '100%','height' => '200px','class' => '',
			'tooltip' => true
			), $args );

		//Cast zoom as integer
		$args['zoom'] = (int) $args['zoom']; 
		
		//Escape attributes
		$width = esc_attr($args['width']);
		$height = esc_attr($args['height']);
		$class = esc_attr($args['class']);

		$args['maptypeid'] = strtoupper($args['maptypeid']);

		 //If class is selected use that style, otherwise use specified height and width
		if( !empty($class) ){
			$class .= " eo-venue-map googlemap";
			$style = "";
		}else{
			$class = "eo-venue-map googlemap";
			$style = "style='height:".$height.";width:".$width.";' ";
		}

		$venue_ids = array_filter($venue_ids);

		if( empty($venue_ids) )
			return false;
		
		//Set up venue locations for map
		foreach( $venue_ids as $venue_id ){

			//Venue lat/lng array
			$latlng = eo_get_venue_latlng($venue_id);

			//Venue tooltip description
			$tooltip_content = '<strong>'.eo_get_venue_name($venue_id).'</strong>';
			$address = array_filter(eo_get_venue_address($venue_id));
			if( !empty($address) )
				$tooltip_content .='<br />'.implode(', ',$address);
			
			/**
			 * Filters the tooltip content for a venue.
			 * 
			 * ### Example
			 * 
			 *    //Adds a link to the venue page to the tooltip
			 *    add_filter( 'eventorganiser_venue_tooltip', 'my_venue_tooltip_content_link_to_venue', 10, 2 );
			 *    function my_venue_tooltip_content_link_to_venue( $description, $venue_id ){
    		 *        $description .= sprintf('<p><a href="%s"> Visit the venue page! </a> </p>', eo_get_venue_link($venue_id));
			 *        return $description;
			 *    }
			 * 
			 * @link https://gist.github.com/stephenharris/4988307 Add upcoming events to the the tooltip 
			 * @param string $tooltip_content The HTML content for the venue tooltip.
			 * @param int $venue_id The ID of the venue.
			 * @param array $args An array of map options. See documentation for `eo_get_venue_map()`.
			 */
			$tooltip_content = apply_filters( 'eventorganiser_venue_tooltip', $tooltip_content, $venue_id, $args );
			
			/**
			 * Filters the url of the venue map marker. Set to `null` for default.
			 *
			 * @link http://wp-event-organiser.com/extensions/event-organiser-venue-markers Custom venue markers
			 * @param string|null $icon Url to the icon image. Null to use default.
			 * @param int $venue_id The ID of the venue.
			 * @param array $args An array of map options. See documentation for `eo_get_venue_map()`.
			 */
			$icon = apply_filters( 'eventorganiser_venue_marker', null, $venue_id, $args );
	
			$locations[] =array( 
					'venue_id' => $venue_id,
					'lat'=>$latlng['lat'], 
					'lng'=>$latlng['lng'], 
					'tooltipContent'=>$tooltip_content, 
					'icon' => $icon );
		}

		//This could be improved
		EventOrganiser_Shortcodes::$map[] = array_merge($args, array('locations'=>$locations) );
		EventOrganiser_Shortcodes::$add_script = true;
		$id = count(EventOrganiser_Shortcodes::$map);

		return  "<div class='".$class."' id='eo_venue_map-{$id}' ".$style."></div>";
}


/**
 * Retrieve post meta field for a venue.
 *
 * This function returns the values of the venue meta with the specified key from the specified venue. (Specified by the venue ID - the taxonomy term ID).
 *
 * ### Examples
 * <code>
 *    <?php $key_1_values = eo_get_venue_meta(76, 'key_1'); ?>
 * </code>
 * To retrieve only the first value of a given key:
 * <code>
 *   <?php $key_1_value = eo_get_venue_meta(76, 'key_1', true); ?>
 * </code>
 * @since 1.5.0
 * @link http://wp-event-organiser.com/documentation/developers/venue-meta-data-and-metaboxes/ How to create custom fields for venues
 * @param int $venue_id Venue (term) ID.
 * @param string $key Optional. The meta key to retrieve. By default, returns data for all keys.
 * @param bool $single Whether to return a single value.
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single
 *  is true.
 */
function eo_get_venue_meta($venue_id, $key ='', $single=true){	
	return get_metadata('eo_venue', $venue_id, $key, $single); 
}


/**
 * Add meta data field to a venue
 *
 * You should avoid the following 'core' keys:
 *
 * * _description
 * * _address
 * * _city
 * * _state
 * * _postcode
 * * _country
 * * _latitude
 * * _longtitude
 *
 * It is **strongly** recommended that you prefix your keys with and underscore.
 *
 * @since 1.5.0
 * @link http://wp-event-organiser.com/documentation/developers/venue-meta-data-and-metaboxes/ How to create custom fields for venues
 *
 * @param int $venue_id Venue (term) ID.
 * @param string $key Metadata name.
 * @param mixed $value Metadata value.
 * @param bool $unique Optional, default is false. Whether the same key should not be added.
 * @return bool False for failure. True for success.
 */
function eo_add_venue_meta($venue_id, $key, $value, $unique = false ){
	return add_metadata('eo_venue',$venue_id, $key, $value, $unique);
}

/**
 * Update venue meta field based on venue (term) ID.
 *
 * Use the $prev_value parameter to differentiate between meta fields with the
 * same key and venue ID. This may be used in place of {@see `eo_add_venue_meta()`} function. The first thing this function will do is make sure that `$meta_key` already exists on `$venue_id`. If it does not, `add_post_meta($venue_id, $meta_key, $meta_value)` is called instead and its result is returned. Returns meta_id if the meta doesn't exist, otherwise returns true on success and false on failure.
 *
 * If the meta field for the venue does not exist, it will be added.
 *
 * You should avoid the following 'core' keys:
 *
 * * _description
 * * _address
 * * _city
 * * _state
 * * _postcode
 * * _country
 * * _latitude
 * * _longtitude
 *
 * It is **strongly** recommended that you prefix your keys with and underscore.
 *
 * @since 1.5.0
 * @link http://wp-event-organiser.com/documentation/developers/venue-meta-data-and-metaboxes/ How to create custom fields for venues
 *
 * @param int $venue_id Venue (term) ID.
 * @param string $key Metadata key.
 * @param mixed $value Metadata value.
 * @param mixed $prev_value Optional. Previous value to check before removing.
 * @return bool False on failure, true if success.
 */
function eo_update_venue_meta($venue_id, $key, $value, $prev_value=''){
	return update_metadata('eo_venue', $venue_id, $key, $value, $prev_value);
}

/**
 * Remove metadata matching criteria from a venue.
 *
 * You can match based on the key, or key and value. Removing based on key and
 * value, will keep from removing duplicate metadata with the same key. It also
 * allows removing all metadata matching key, if needed.
 *
 * @since 1.5.0
 * @link http://wp-event-organiser.com/documentation/developers/venue-meta-data-and-metaboxes/ How to create custom fields for venues
 *
 * @param int $venue_id Venue (term) ID.
 * @param string $key Metadata name.
 * @param mixed $value Optional. Metadata value.
 * @return bool False for failure. True for success.
 */
function eo_delete_venue_meta($venue_id, $key, $value = '', $delete_all = false ){
	return delete_metadata('eo_venue',$venue_id, $key, $value, $delete_all);
}

/**
 * Sanitizes (or validates) the metadata (expects raw) before being inserted into the databse.
 * 
 * @since 1.4.0
 * @access private
 * @ignore
 * @param string The key of the meta data
 * @param mixed The meta data being validated.
 * @return mixed The validated value. False if the key is not recognised.
 */
function eventorganiser_sanitize_meta($key,$value){

	switch($key):
		case '_description':
			$value = wp_filter_post_kses($value);
			break;
		case '_lat':
		case '_lng':
			//Cast as float and then string: make sure string uses . not , for decimal point
			$value = floatval($value);
			$value = number_format($value, 6);
			break;
		default:
			$address_keys = _eventorganiser_get_venue_address_fields();
			if( isset($address_keys[$key]) )
				$value = sanitize_text_field($value);
			else
				$value = false;
	endswitch;

	return $value;
}

/**
 *@ignore
 *@access private
 */
function _eventorganiser_get_venue_address_fields(){
	//Keys *must* be prefixed by a '_'.
	$address_fields = array(
		'_address'=>  __('Address','eventorganiser'),
		'_city'=>  __('City','eventorganiser'),
		'_state'=>  __('State / Province','eventorganiser'),
		'_postcode'=>  __('Post Code','eventorganiser'),
		'_country'=>  __('Country','eventorganiser'),
	);

	/**
	 * Filters fields used for the address of a venue.
	 * 
	 * This filter allows you to remove address components you don't need or add-ones
	 * you do. The array is indexed by meta-key which **must** be prefixed by an
	 * underscore (`_`), The value is the label of the address component.
	 * 
	 * Added fields will appear in the address metabox on the admin venue screen.
	 *
	 * @param array $address_fields An array of address components
	 */
	$address_fields = apply_filters('eventorganiser_venue_address_fields', $address_fields);
	return $address_fields;
}


/**
 *@ignore
 *@access private
 */
function eventorganiser_venue_dropdown($post_id=0,$args){
	$venues = get_terms('event-venue', array('hide_empty'=>false));
	$current = (int) eo_get_venue($post_id); 

	$id = (!empty($args['id']) ? 'id="'.esc_attr($args['id']).'"' : '');
	$name = (!empty($args['name']) ? 'name="'.esc_attr($args['name']).'"' : '');
	?>
	<select <?php echo $id.' '.$name; ?>>
		<option><?php _e("Select a venue",'eventorganiser');?></option>
		<?php foreach ($venues as $venue):?>
			<option <?php  selected($venue->term_id,$current);?> value="<?php echo $venue->term_id;?>"><?php echo $venue->name; ?></option>
		<?php endforeach;?>
	</select><?php
}
/**
 *@ignore
 *@access private
 */
function eo_event_venue_dropdown( $args = '' ) {
	$defaults = array(
		'show_option_all' =>'', 
		'echo' => 1,
		'selected' => 0, 
		'name' => 'event-venue', 
		'id' => '',
		'class' => 'postform event-organiser event-venue-dropdown event-dropdown', 
		'tab_index' => 0, 
	);

	$defaults['selected'] =  (is_tax('event-venue') ? get_query_var('event-venue') : 0);
	$r = wp_parse_args( $args, $defaults );
	$r['taxonomy']='event-venue';
	extract( $r );

	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 )
		$tab_index_attribute = " tabindex=\"$tab_index\"";

	$categories = get_terms($taxonomy, $r ); 
	$name = esc_attr( $name );
	$class = esc_attr( $class );
	$id = $id ? esc_attr( $id ) : $name;

	$output = "<select style='width:150px' name='$name' id='$id' class='$class' $tab_index_attribute>\n";
	
	if ( $show_option_all ) {
		$output .= '<option '.selected($selected,0,false).' value="0">'.$show_option_all.'</option>';
	}

	if ( ! empty( $categories ) ) {
		foreach ($categories as $term):
			$output .= '<option value="'.$term->slug.'"'.selected($selected,$term->slug,false).'>'.$term->name.'</option>';
		endforeach; 
	}
	$output .= "</select>\n";

	if ( $echo )
		echo $output;

	return $output;
}

?>
