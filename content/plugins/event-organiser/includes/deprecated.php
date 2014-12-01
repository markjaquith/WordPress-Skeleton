<?php
/**
 *@package deprecated
 */

/**
 * Returns a the url which adds a particular occurrence of an event to
 * a google calendar. Must be used inside the loop.
 *
 * @since 1.2.0
 * @deprecated 2.3.0
 * @see eo_get_add_to_google_link()
 *
 * @param int $post_id Optional, the event (post) ID,
 * @return string Url which adds event to a google calendar
 */
function eo_get_the_GoogleLink(){
	_deprecated_function( __FUNCTION__, '2.3', 'eo_get_add_to_google_link()' );
	return eo_get_add_to_google_link();
}

/** 
* Returns an array of DateTime objects for each start date of occurrence
* @since 1.0.0
* @deprecated 1.5
* @see eo_get_the_occurrences_of()
*
* @param int $post_id Optional, the event (post) ID, 
* @return array|false Array of DateTime objects of the start date-times of occurences. False if none exist.
 */
function eo_get_the_occurrences($post_id=0){
	//_deprecated_function( __FUNCTION__, '1.5', 'eo_get_the_occurrences_of()' );
	$occurrences = eo_get_the_occurrences_of($post_id);
	if( $occurrences )
		return wp_list_pluck($occurrences, 'start');
	return false;
}

/**
* Return true is the event is an all day event.
* @since 1.2
* @deprecated 1.5
* @see eo_is_all_day()
*
* @param int $post_id Optional, the event series (post) ID, 
* @return bool True if event runs all day, or false otherwise
 */
function eo_is_allday($post_id=0){
	_deprecated_function( __FUNCTION__, '1.5', 'eo_is_all_day()' );
	return eo_is_all_day($post_id);
}

/**
* Returns the formated date of the last occurrence of an event
* @since 1.0.0
* @deprecated 1.5 use eo_get_schedule_last
* @see eo_get_schedule_last
*
* @param string $format the format to use, using PHP Date format
* @param int $post_id Optional, the event (post) ID, 
* @return string the formatted date 
 */
function eo_get_schedule_end($format='d-m-Y',$post_id=0){
	return eo_get_schedule_last($format,$post_id);
}

/**
* Prints the formated date of the last occurrence of an event
* @since 1.0.0
* @deprecated 1.5 use eo_schedule_last
* @see eo_schedule_last
*
* @param string $format the format to use, using PHP Date format
* @param int $post_id Optional, the event (post) ID, 
 */
function  eo_schedule_end($format='d-m-Y',$post_id=0){
	echo eo_get_schedule_last($format,$post_id);
}


/**
* Returns an array with details of the event's reoccurences
* @since 1.0.0
* @deprecated 1.6
* @see eo_get_event_schedule()
*
* @param int $post_id Optional, the event (post) ID, 
* @return array Schedule information
*/
function eo_get_reoccurrence($post_id=0){
	return eo_get_reoccurence($post_id);
}


/**
* Returns an array with details of the event's reoccurences. 
* Note this is is identical to eo_get_reoccurrence() which corrects a spelling error.
*
* @param int Optional, the event (post) ID, 
 * @since 1.0.0
 * @deprecated 1.6
 * @see eo_get_event_schedule()
*
* @param int $post_id Optional, the event (post) ID, 
* @return array Schedule information
 */
function eo_get_reoccurence($post_id=0){
	_deprecated_function( __FUNCTION__, '1.5', 'eo_get_event_schedule()' );
	$post_id = (int) ( empty($post_id) ? get_the_ID() : $post_id);

	if( empty($post_id) || 'event' != get_post_type($post_id) ) 
		return false;
		
	$return = eo_get_event_schedule( $post_id );	

	if ( !$return )
		return false;

	$return['reoccurrence'] =$return['schedule'];
	$return['meta'] =	$return['schedule_meta'];
	$return['end'] = $return['schedule_last']; 
	return $return; 
}


/**
* Returns the colour of a category associated with the event
* @since 1.3.3
* @deprecated 1.6
* @see eo_get_event_color()
*
* @param int $post_id The event (post) ID
* @return string The colour of the category in HEX format
*/
function eo_event_color($post_id=0){
	_deprecated_function( __FUNCTION__, '1.6', 'eo_get_event_color()' );
	return eo_get_event_color($post_id);
}


/**
 * Retrieve array of venues. Acts as a wrapper for get_terms, except hide_empty defaults to false.
 * @since 1.0.0
 * @deprecated 1.6
 * @see eo_get_venues()
 *
 * @param string|array $args The values of what to search for when returning venues
 * @return array List of Term (venue) Objects
 */
function eo_get_the_venues($args=array()){
	_deprecated_function( __FUNCTION__, '1.6', 'eo_get_venues()' );
	return eo_get_venues($args);
}

/**
 * Deletes the event data associated with post. Should be called when an event is being deleted.
 * This does not delete the post.
 * @since 1.0.0
 * @deprecated 1.6
 * @see eo_delete_event_occurrences()
 *
* @param int $post_id The event (post) ID
 */
function eventorganiser_event_delete($post_id){
	eo_delete_event_occurrences($post_id);
}

?>
