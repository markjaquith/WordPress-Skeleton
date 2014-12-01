<?php

/**
 *@package event-functions
 */
/**
* This functions updates a post of event type, with data given in the $post_data
* and event data given in $event_data. Returns the post_id. 
*
* Triggers {@see `eventorganiser_save_event`} passing event (post) ID
*
* The event data array can contain
*
* * `schedule` => (custom | once | daily | weekly | monthly | yearly)  -- specifies the reoccurrence pattern
* * `schedule_meta` =>
*   * For monthly schedules,
*      * (string) BYMONTHDAY=XX to repeat on XXth day of month, e.g. BYMONTHDAY=01 to repeat on the first of every month.
*      * (string) BYDAY=ND. N= 1|2|3|4|-1 (first, second, third, fourth, last). D is day of week SU|MO|TU|WE|TH|FR|SA. E.g. BYDAY=2TU (repeat on second tuesday)
*   * For weekly schedules,
*      * (array) Days to repeat on: (SU,MO,TU,WE,TH,FR,SA). e.g. set to array('SU','TU') to repeat on Tuesdays & Sundays. 
*      * Can be left blank to repeat weekly from the start date.
* * `frequency` => (int) positive integer, sets frequency of reoccurrence (every 2 days, or every 3 days etc)
* * `all_day` => 1 if its an all day event, 0 if not
* * `start` =>  start date (of first occurrence)  as a datetime object
* * `end` => end date (of first occurrence)  as a datetime object
* * `schedule_last` =>  **START** date of last occurrence (or upper-bound thereof) as a datetime object
* * `number_occurrences` => Instead of specifying `schedule_last` you can specify the number of occurrence a recurring event should have. 
* This is only used if `schedule_last` is not, and for daily, weekly, monthly or yearly recurring events.
* * `include` => array of datetime objects to include in the schedule
* * `exclude` => array of datetime objects to exclude in the schedule
*
* @since 1.5
* @uses wp_insert_post()
*
* @param int $post_id - the event (post) ID for the event you want to update
* @param array $event_data - array of event data
* @param array $post_data - array of data to be used by wp_update_post.
* @return int $post_id - the post ID of the updated event
*/
function eo_update_event( $post_id, $event_data = array(), $post_data = array() ){

	$post_id = (int) $post_id;
	
	$input = array_merge( $post_data, $event_data );
	
	//Backwards compat:
	if( !empty( $input['venue'] ) ){
		$input['tax_input']['event-venue'] = $input['venue'];
	}
	if( !empty( $input['category'] ) ){
		$input['tax_input']['event-category'] = $input['category'];
	}
	
	$event_keys = array_flip( array( 'start', 'end', 'schedule', 'schedule_meta', 'frequency', 
			'all_day', 'schedule_last', 'include', 'exclude', 'occurs_by', 'number_occurrences' ) );
	
	$post_keys = array_flip( array(
			'post_title','post_content','post_status', 'post_type','post_author','ping_status','post_parent','menu_order', 
			'to_ping', 'pinged', 'post_password', 'guid', 'post_content_filtered', 'post_excerpt', 'import_id', 'tax_input'
	) );
	
	$event_data = array_intersect_key( $input, $event_keys );
	$post_data = array_intersect_key( $input, $post_keys ) + $post_data;
	 
	if( empty($post_id) )
		return new WP_Error('eo_error','Empty post ID.');
		
	/**
	 *@ignore
	 */
	$event_data = apply_filters( 'eventorganiser_update_event_event_data', $event_data, $post_id, $post_data, $event_data );
	/**
	 *@ignore
	 */
	$post_data = apply_filters( 'eventorganiser_update_event_post_data', $post_data, $post_id, $post_data, $event_data );

	if( !empty($post_data) ){
		$post_data['ID'] = $post_id;		
		wp_update_post( $post_data );
	}

	//Get previous data, parse with data to be updated
	$prev = eo_get_event_schedule($post_id);
	$event_data = wp_parse_args( $event_data, $prev );

	//If schedule is 'once' and dates are included - set to 'custom':
	if( ( empty($event_data['schedule']) || 'once' == $event_data['schedule'] ) && !empty($event_data['include']) ){
		$event_data['schedule'] = 'custom';
	}
		
	//Do we need to delete existing dates from db?
	$delete_existing = false;
	$diff = array();
	if( $prev ){
		foreach ( $prev as $key => $prev_value ){
			if( $event_data[$key] != $prev_value ){
				if('monthly' == $event_data['schedule'] && $key =='schedule_meta'){
					if( $event_data['occurs_by'] != $prev['occurs_by'] ){
						$diff[]=$key;
						$delete_existing = true;
						break;
					}
				}else{
					
					//If one off event / custom, don't worry about 'schedule_last'
					if( $key == 'schedule_last' && in_array( $event_data['schedule'], array( 'once', 'custom' ) ) )
						continue;
					
					if( $key == 'schedule_last' && empty( $event_data['schedule_last'] ) && !empty( $event_data['number_occurrences'] ) ){
						//Schedule_last is not used. Ignore this if number_occurrences match
						if( $event_data['number_occurrences']  == $prev['number_occurrences'] ){
							continue;
						}
					}
					
					if( $key == 'number_occurrences' && !empty( $event_data['schedule_last'] ) ){
						//schedule_last is being used.  Ignore number_occurrences.
						if( $event_data['schedule_last']  == $prev['schedule_last'] ){
							continue;
						}
					}
					
					$diff[]=$key;
					$delete_existing = true;
					break;
				}
			}
		}
	}
	
	//Need to replace occurrences
	if( $delete_existing || !empty( $event_data['force_regenerate_dates'] ) ){
		//Generate occurrences
		$event_data = _eventorganiser_generate_occurrences($event_data);

		if( is_wp_error($event_data) )
			return $event_data;

		//Insert new dates, remove old dates and update meta
		$re = _eventorganiser_insert_occurrences( $post_id, $event_data );
	}

	/**
	 * Triggered after an event has been updated.
	 *
	 * @param int $post_id The ID of the event
	 */
	do_action( 'eventorganiser_save_event', $post_id );
	return $post_id;
}


/**
* This functions inserts a post of event type, with data given in the $post_data
* and event data given in $event_data. Returns the post ID.
*
* Triggers {@see `eventorganiser_save_event`} passing event (post) ID
*
* The event data array can contain
*
* * `schedule` => (custom | once | daily | weekly | monthly | yearly)  -- specifies the reoccurrence pattern
* * `schedule_meta` =>
*   * For monthly schedules,
*      * (string) BYMONTHDAY=XX to repeat on XXth day of month, e.g. BYMONTHDAY=01 to repeat on the first of every month.
*      * (string) BYDAY=ND. N= 1|2|3|4|-1 (first, second, third, fourth, last). D is day of week SU|MO|TU|WE|TH|FR|SA. E.g. BYDAY=2TU (repeat on second tuesday)
*   * For weekly schedules,
*      * (array) Days to repeat on: (SU,MO,TU,WE,TH,FR,SA). e.g. set to array('SU','TU') to repeat on Tuesdays & Sundays. 
*      * Can be left blank to repeat weekly from the start date.
* * `frequency` => (int) positive integer, sets frequency of reoccurrence (every 2 days, or every 3 days etc)
* * `all_day` => 1 if its an all day event, 0 if not
* * `start` =>  start date (of first occurrence)  as a datetime object
* * `end` => end date (of first occurrence)  as a datetime object
* * `schedule_last` =>  **START** date of last occurrence (or upper-bound thereof) as a datetime object
* * `number_occurrences` => Instead of specifying `schedule_last` you can specify the number of occurrence a recurring event should have. 
* This is only used if `schedule_last` is not, and for daily, weekly, monthly or yearly recurring events.
* * `include` => array of datetime objects to include in the schedule
* * `exclude` => array of datetime objects to exclude in the schedule
*
* ### Example
* The following example creates an event which starts on the 3rd December 2012 15:00 and ends on the 4th December 15:00 and repeats every 4 days until the 25th December (So the last occurrence actually ends on the 23rd).
* <code>
*     $event_data = array(
*	     'start'=> new DateTime('2012-12-03 15:00', eo_get_blog_timezone() ),
*	     'end'=> new DateTime('2012-12-04 15:00', eo_get_blog_timezone() ),
*	     'schedule_last'=> new DateTime('2012-12-25 15:00', eo_get_blog_timezone() ),
*	     'frequency' => 4,
*	     'all_day' => 0,
*	     'schedule'=>'daily',
*    );
*     $post_data = array(
*	     'post_title'=>'The Event Title',
*	     'post_content'=>'My event content',
*    );
*
*    $e = eo_insert_event($post_data,$event_data);
* </code>
* 
* ### Tutorial
* See this <a href="http://www.stephenharris.info/2012/front-end-event-posting/">tutorial</a> or <a href="https://gist.github.com/3867194">this Gist</a> on front-end event posting.
*
* @since 1.5
* @link http://www.stephenharris.info/2012/front-end-event-posting/ Tutorial on front-end event posting
* @uses wp_insert_post() 
*
* @param array $post_data array of data to be used by wp_insert_post.
* @param array $event_data array of event data
* @return int the post ID of the updated event
*/
function eo_insert_event( $post_data = array(), $event_data = array() ){
	global $wpdb;

	$input = array_merge( $post_data, $event_data );
	
	//Backwards compat:
	if( !empty( $input['venue'] ) ){
		$input['tax_input']['event-venue'] = $input['venue'];
	}
	if( !empty( $input['category'] ) ){
		$input['tax_input']['event-category'] = $input['category'];
	}
	
	$event_keys = array_flip( array( 'start', 'end', 'schedule', 'schedule_meta', 'frequency', 
			'all_day', 'schedule_last', 'include', 'exclude', 'occurs_by', 'number_occurrences' ) );
	
	$post_keys = array_flip( array(
			'post_title','post_content','post_status', 'post_type','post_author','ping_status','post_parent','menu_order', 
			'to_ping', 'pinged', 'post_password', 'guid', 'post_content_filtered', 'post_excerpt', 'import_id', 'tax_input'
	) );
	
	$event_data = array_intersect_key( $input, $event_keys ) + $event_data;
	$post_data = array_intersect_key( $input, $post_keys );
		
	//If schedule is 'once' and dates are included - set to 'custom':
	if( ( empty($event_data['schedule']) || 'once' == $event_data['schedule'] ) && !empty($event_data['include']) ){
		$event_data['schedule'] = 'custom';
	}

	$event_data = _eventorganiser_generate_occurrences($event_data);
		
	if( is_wp_error($event_data) )
		return $event_data;

	/**
	 *@ignore
	 */
	$event_data = apply_filters( 'eventorganiser_insert_event_event_data', $event_data, $post_data, $event_data );
	
	/**
	 *@ignore
	 */
	$post_data = apply_filters( 'eventorganiser_insert_event_post_data', $post_data, $post_data, $event_data );
		
	//Finally we create event (first create the post in WP)
	$post_input = array_merge(array('post_title'=>'untitled event'), $post_data, array('post_type'=>'event'));			
	$post_id = wp_insert_post($post_input, true);

	//Did the event insert correctly? 
	if ( is_wp_error( $post_id) ) 
			return $post_id;

	_eventorganiser_insert_occurrences($post_id, $event_data);
			
	//Action used to break cache & trigger Pro actions (& by other plug-ins?)
	/**
	 * Triggered after an event has been updated.
	 * 
	 * @param int $post_id The ID of the event 
	 */
	do_action( 'eventorganiser_save_event', $post_id );
	return $post_id;
}

/**
 * Deletes all occurrences for an event (removes them from the eo_events table).
 * Triggers {@see `eventorganiser_delete_event`} (this action is used to break the caches).
 *
 * This function does not update any of the event schedule details.
 * **Don't call this unless you know what you're doing**.
 * 
 * @since 1.5
 * @access private
 * @param int $post_id the event's (post) ID to be deleted
 * @param int|array $occurrence_ids Occurrence ID (or array of IDs) for specificaly occurrences to delete. If empty/false, deletes all.
 * 
 */
function eo_delete_event_occurrences( $event_id, $occurrence_ids = false ){
	global $wpdb;
	//TODO use this in break/remove occurrence
	
	//Let's just ensure empty is cast as false
	$occurrence_ids = ( empty( $occurrence_ids ) ? false : $occurrence_ids );
	
	if( $occurrence_ids !== false ){
		$occurrence_ids = (array) $occurrence_ids;
		$occurrence_ids = array_map( 'absint', $occurrence_ids );
		$occurrence_ids_in = implode( ', ', $occurrence_ids );
		
		$raw_sql = "DELETE FROM $wpdb->eo_events WHERE post_id=%d AND event_id IN( $occurrence_ids_in )";

	}else{
		$raw_sql = "DELETE FROM $wpdb->eo_events WHERE post_id=%d";
	}
	
	/**
	 * @ignore
	 */
	do_action( 'eventorganiser_delete_event', $event_id, $occurrence_ids ); //Deprecated - do not use!
	
	/**
	 * Triggers just before the specified occurrences for the event are deleted.
	 * 
	 * @param int $event_id The (post) ID of the event of which we're deleting occurrences.
	 * @param array|false $occurrence_ids An array of occurrences to be delete. If `false`, all occurrences are to be removed.
	 */
	do_action( 'eventorganiser_delete_event_occurrences', $event_id, $occurrence_ids );
	
	$del = $wpdb->get_results( $wpdb->prepare(  $raw_sql, $event_id ) );
	
}
add_action( 'delete_post', 'eo_delete_event_occurrences', 10 );

/**
* This is a private function - handles the insertion of dates into the database. Use eo_insert_event or eo_update_event instead.
* @access private
* @ignore
*
* @param int $post_id The post ID of the event
* @param array $event_data Array of event data, including schedule meta (saved as post meta), duration and occurrences
* @return int $post_id
*/
function  _eventorganiser_insert_occurrences( $post_id, $event_data ){
	
	global $wpdb;
	extract( $event_data );
	$tz = eo_get_blog_timezone();

	//Don't use date_diff (requires php 5.3+)
	//Also see https://github.com/stephenharris/Event-Organiser/issues/205
	//And https://github.com/stephenharris/Event-Organiser/issues/224
	$duration_str = eo_date_interval( $start, $end, '+%y year +%m month +%d days +%h hours +%i minutes +%s seconds' );
	
	$event_data['duration_str'] = $duration_str;

	$schedule_last_end = clone $schedule_last;
	$schedule_last_end->modify( $duration_str );

	//Get dates to be deleted / added
	$current_occurrences = eo_get_the_occurrences( $post_id );
	$current_occurrences = $current_occurrences ? $current_occurrences : array();
	
	$delete   = array_udiff( $current_occurrences, $occurrences, '_eventorganiser_compare_dates' );
	$insert   = array_udiff( $occurrences, $current_occurrences, '_eventorganiser_compare_dates' );
	$update   = array_uintersect( $occurrences, $current_occurrences, '_eventorganiser_compare_dates' );
	$update_2 = array_uintersect( $current_occurrences, $update, '_eventorganiser_compare_dates' );
	$keys     = array_keys( $update_2 );
	
	if( $delete ){
		$delete_occurrence_ids = array_keys( $delete );
		eo_delete_event_occurrences( $post_id, $delete_occurrence_ids );
	}
	
	$occurrence_cache = array();
	$occurrence_array = array();
	
	if( $update ){
		$update   = array_combine( $keys, $update );
		
		foreach( $update as $occurrence_id => $occurrence ){

			$occurrence_end = clone $occurrence;
			$occurrence_end->modify($duration_str);
			
			$occurrence_input = array(
				'StartDate'        => $occurrence->format('Y-m-d'),
				'StartTime'        => $occurrence->format('H:i:s'),
				'EndDate'          => $occurrence_end->format('Y-m-d'),
				'FinishTime'       => $end->format('H:i:s'),
			);

			$wpdb->update(
				$wpdb->eo_events, 
				$occurrence_input,				
				array( 'event_id' => $occurrence_id )
			);

			//Add to occurrence cache: TODO use post meta
			$occurrence_array[$occurrence_id] = $occurrence->format('Y-m-d H:i:s');
			$occurrence_cache[$occurrence_id] = array(
				'start' => $occurrence,
				'end'   => new DateTime($occurrence_end->format('Y-m-d').' '.$end->format('H:i:s'), eo_get_blog_timezone())
			);
		}
	}
	
	if( $insert ){
		foreach( $insert as $counter => $occurrence ):
			$occurrence_end = clone $occurrence;
			$occurrence_end->modify($duration_str);

			$occurrence_input =array(
				'post_id'          => $post_id,
				'StartDate'        => $occurrence->format('Y-m-d'),
				'StartTime'        => $occurrence->format('H:i:s'),
				'EndDate'          => $occurrence_end->format('Y-m-d'),
				'FinishTime'       => $end->format('H:i:s'),
				'event_occurrence' => $counter,
			);

			$wpdb->insert( $wpdb->eo_events, $occurrence_input );
			
			$occurrence_array[$wpdb->insert_id] = $occurrence->format('Y-m-d H:i:s');

			//Add to occurrence cache: TODO use post meta
			$occurrence_cache[$wpdb->insert_id] = array(
				'start' => $occurrence,
				'end'   => new DateTime($occurrence_end->format('Y-m-d').' '.$end->format('H:i:s'), $tz ),
			);

		endforeach;
	}
		
	//Set occurrence cache
	wp_cache_set( 'eventorganiser_occurrences_'.$post_id, $occurrence_cache );

	unset( $event_data['occurrences'] );
	$event_data['_occurrences'] = $occurrence_array;
		
	if( !empty($include) ){
		$event_data['include'] = array_map('eo_format_datetime', $include, array_fill(0, count($include), 'Y-m-d H:i:s') );
	}
	
	if( !empty($exclude) ){
		$event_data['exclude'] = array_map('eo_format_datetime', $exclude, array_fill(0, count($exclude), 'Y-m-d H:i:s') );
	}

	unset( $event_data['start'] );
	unset( $event_data['end'] );
	unset( $event_data['schedule_start'] );
	unset( $event_data['schedule_last'] );
		
	update_post_meta( $post_id, '_eventorganiser_event_schedule', $event_data );
	update_post_meta( $post_id, '_eventorganiser_schedule_start_start', $start->format('Y-m-d H:i:s') );
	update_post_meta( $post_id, '_eventorganiser_schedule_start_finish', $end->format('Y-m-d H:i:s') );
	update_post_meta( $post_id, '_eventorganiser_schedule_last_start', $schedule_last->format('Y-m-d H:i:s') );
	update_post_meta( $post_id, '_eventorganiser_schedule_last_finish', $schedule_last_end->format('Y-m-d H:i:s') );
		
	return $post_id;
}


/**
* Gets schedule meta from the database (post meta)
* Datetimes are converted to DateTime objects, in blog's currenty timezone
*
*  Event details include
*
* * `schedule` => (custom | once | daily | weekly | monthly | yearly)  -- specifies the reoccurrence pattern
* * `schedule_meta` =>
*   * For monthly schedules,
*      * (string) BYMONTHDAY=XX to repeat on XXth day of month, e.g. BYMONTHDAY=01 to repeat on the first of every month.
*      * (string) BYDAY=ND. N= 1|2|3|4|-1 (first, second, third, fourth, last). D is day of week SU|MO|TU|WE|TH|FR|SA. E.g. BYDAY=2TU (repeat on second tuesday)
*   * For weekly schedules,
*      * (array) Days to repeat on: (SU,MO,TU,WE,TH,FR,SA). e.g. set to array('SU','TU') to repeat on Tuesdays & Sundays. 
* * `occurs_by` - For use with monthly schedules: how the event reoccurs: BYDAY or BYMONTHDAY
* * `frequency` => (int) positive integer, sets frequency of reoccurrence (every 2 days, or every 3 days etc)
* * `all_day` => 1 if its an all day event, 0 if not
* * `start` =>  start date (of first occurrence)  as a datetime object
* * `end` => end date (of first occurrence)  as a datetime object
* * `schedule_last` =>  **START** date of last occurrence as a datetime object
* * `include` => array of datetime objects to include in the schedule
* * `exclude` => array of datetime objects to exclude in the schedule
*
* @param int $post_id -  The post ID of the event
* @return array event schedule details
*/
function eo_get_event_schedule( $post_id=0 ){

	$post_id = (int) ( empty($post_id) ? get_the_ID() : $post_id);

	if( empty($post_id) ) 
		return false;

	$event_details = get_post_meta( $post_id,'_eventorganiser_event_schedule',true);
	$event_details = wp_parse_args($event_details, array(
		'schedule'=>'once',
		'schedule_meta'=>'',
		'number_occurrences' => 0, //Number occurrences according to recurrence rule. Not necessarily the #occurrences (after includes/excludes)
		'frequency'=>1,
		'all_day'=>0,
		'duration_str'=>'',
		'include'=>array(),
		'exclude'=>array(),
		'_occurrences'=>array(),
	));

	$tz = eo_get_blog_timezone();
	$event_details['start'] = new DateTime(get_post_meta( $post_id,'_eventorganiser_schedule_start_start', true), $tz);
	if( $end_datetime = get_post_meta( $post_id,'_eventorganiser_schedule_start_finish', true ) ){
		$event_details['end'] = new DateTime( $end_datetime, $tz );
	}else{
		$event_details['end'] = new DateTime( '+1 hour', $tz );
	}
	$event_details['schedule_start'] = clone $event_details['start'];
	$event_details['schedule_last'] = new DateTime(get_post_meta( $post_id,'_eventorganiser_schedule_last_start', true), $tz);
	$event_details['schedule_finish'] = new DateTime(get_post_meta( $post_id,'_eventorganiser_schedule_last_finish', true), $tz);

	if( !empty($event_details['_occurrences']) ){
		$event_details['_occurrences'] = array_map('eventorganiser_date_create', $event_details['_occurrences']);
	}

	if( !empty($event_details['include']) ){
		$event_details['include'] = array_map('eventorganiser_date_create', $event_details['include'] );
	}
	if( !empty($event_details['exclude']) ){
		$event_details['exclude'] = array_map('eventorganiser_date_create',$event_details['exclude'] );
	}

	if($event_details['schedule'] == 'weekly'){
		$event_details['occurs_by'] = '';
	}elseif($event_details['schedule'] == 'monthly'){
		$bymonthday = preg_match('/BYMONTHDAY=/',$event_details['schedule_meta']);
		$event_details['occurs_by'] = ($bymonthday ? 'BYMONTHDAY' : 'BYDAY');
	}else{
		$event_details['occurs_by'] ='';
	}

	/**
	 * Filters the schedule metadata for an event (as returned by `eo_get_event_schedule()`.
	 * 
	 * See documentation on `eo_get_event_schedule()` for more details.
	 *
	 * @param array $event_details Details of the event's dates and recurrence pattern
	 * @param int $post_id The ID of the event
	 */
	$event_details = apply_filters( 'eventorganiser_get_event_schedule', $event_details, $post_id );
	return $event_details;
}


/**
* This is a private function - handles the generation of occurrence dates from the schedule data
* @access private
* @ignore
*
* @param array $event_data - Array containing the event's schedule data
* @return array $event_data - Array containing the event's schedule data including 'occurrences', an array of DateTimes
*/
	function _eventorganiser_generate_occurrences( $event_data=array() ){

		$event_defaults = array(
			'start'=>'',
			'end'=>'',
			'all_day'=>0,
			'schedule'=>'once',
			'schedule_meta'=>'',
			'frequency'=>1,
			'schedule_last'=>'',
			'number_occurrences' => 0,
			'exclude'=>array(),
			'include'=>array(),
		);
		extract(wp_parse_args($event_data, $event_defaults));
		
		$occurrences =array(); //occurrences array	

		$exclude = array_filter( (array) $exclude );
		$include = array_filter( (array) $include );
		$exclude = array_udiff($exclude, $include, '_eventorganiser_compare_dates');
		$include = array_udiff($include, $exclude, '_eventorganiser_compare_dates');
		
		//White list schedule
		if( !in_array($schedule, array('once','daily','weekly','monthly','yearly','custom')) )
			return new WP_Error('eo_error',__('Schedule not recognised.','eventorganiser'));
		
		//Ensure event frequency is a positive integer. Else set to 1.
		$frequency = max(absint($frequency),1);
		$all_day = (int) $all_day;
		$number_occurrences = absint( $number_occurrences );
		
		//Check dates are supplied and are valid
		if( !($start instanceof DateTime) )
			return new WP_Error('eo_error',__('Start date not provided.','eventorganiser'));

		if( !($end instanceof DateTime) )
			$end = clone $start;
		
		//If use 'number_occurrences' to limit recurring event, set dummy 'schedule_last' date.
		if( !($schedule_last instanceof DateTime) && $number_occurrences && in_array( $schedule, array( 'daily','weekly','monthly','yearly' ) ) ){
			//Set dummy "last occurrance" date.
			$schedule_last = clone $start;
		}else{
			$number_occurrences = 0;
		}

		if( 'once' == $schedule || !($schedule_last instanceof DateTime) )
			$schedule_last = clone $start;

		//Check dates are in chronological order
		if($end < $start)
			return new WP_Error('eo_error',__('Start date occurs after end date.','eventorganiser'));
		
		if($schedule_last < $start)
			return new WP_Error('eo_error',__('Schedule end date is before is before the start date.','eventorganiser'));

		//Now set timezones
		$timezone = eo_get_blog_timezone();
		$start->setTimezone($timezone);
		$end->setTimezone($timezone);
		$schedule_last->setTimezone($timezone);
		$H = intval($start->format('H'));
		$i = intval($start->format('i'));


		$start_days =array();
		$workaround='';
		$icaldays = array('SU','MO','TU','WE','TH','FR','SA');
		$weekdays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'); 
		$ical2day = array('SU'=>'Sunday','MO'=>'Monday','TU'=>'Tuesday','WE'=>'Wednesday','TH'=>'Thursday','FR'=>'Friday','SA'=>'Saturday');

		//Set up schedule
		switch( $schedule ) :
			case 'once':
			case 'custom':
				$frequency =1;
				$schedule_meta ='';
				$schedule_start = clone $start;
				$schedule_last = clone $start;
				$start_days[] = clone $start;
				$workaround = 'once';//Not strictly a workaround.
				break;

			case 'daily':
				$interval = "+".$frequency."day";
				$start_days[] = clone $start;
				break;	

			case 'weekly':
				$schedule_meta = ( $schedule_meta ? array_filter($schedule_meta) : array() );
				if( !empty($schedule_meta) && is_array($schedule_meta) ):
					foreach ($schedule_meta as $day):
						$start_day = clone $start;
						$start_day->modify($ical2day[$day]);
						$start_days[] = $start_day;
					endforeach;
				else:
					$schedule_meta = array( $icaldays[ $start->format('w') ] );
					$start_days[] = clone $start;
				endif;

				$interval = "+".$frequency."week";
				break;

			case 'monthly':
				$start_days[] = clone $start;
				$rule_value = explode('=',$schedule_meta,2);
				$rule =$rule_value[0];
				$values = !empty( $rule_value[1] ) ? explode(',',$rule_value[1]) : array();//Should only be one value, but may support more in future
				$values =  array_filter( $values );
				
				if( $rule=='BYMONTHDAY' ):
					$date = (int) $start_days[0]->format('d');
					$interval = "+".$frequency."month";
					
					if($date >= 29)
						$workaround = 'short months';	//This case deals with 29/30/31 of month

					$schedule_meta = 'BYMONTHDAY='.$date;

				else:
					if( empty($values) ){
						$date = (int) $start_days[0]->format('d');
						$n = ceil($date/7); // nth weekday of month.
						$day_num = intval($start_days[0]->format('w')); //0 (Sun) - 6(Sat)

					}else{
						//expect e.g. array( 2MO )
						preg_match('/^(-?\d{1,2})([a-zA-Z]{2})/' ,$values[0],$matches);
						$n=(int) $matches[1];
						$day_num = array_search($matches[2],$icaldays);//(Sun) - 6(Sat)
					}

					if($n==5) $n= -1; //If 5th, interpret it as last.
					$ordinal = array('1'=>"first",'2'=>"second",'3'=>"third",'4'=>"fourth",'-1'=>"last");

					if( !isset($ordinal[$n]) )
						return new WP_Error('eo_error',__('Invalid monthly schedule (invalid ordinal)','eventorganiser'));

					$ical_day = $icaldays[$day_num];  //ical day from day_num (SU - SA)
					$day = $weekdays[$day_num];//Full day name from day_num (Sunday -Monday)
					$schedule_meta = 'BYDAY='.$n.$ical_day; //E.g. BYDAY=2MO
					$interval = $ordinal[$n].' '.$day.' of +'.$frequency.' month'; //E.g. second monday of +1 month
					
					//Work around for PHP <5.3
					if(!function_exists('date_diff')){
						$workaround = 'php5.2';
					}
				endif;
				break;
	
			case 'yearly':
				$start_days[] = clone $start;
				if( '29-02' == $start_days[0]->format('d-m') )
					$workaround = 'leap year';
				
				$interval = "+".$frequency."year";
				break;
		endswitch; //End $schedule switch


		//Now we have setup and validated the schedules - loop through and generate occurrences
		foreach($start_days as $index => $start_day):
			$current = clone $start_day;
			$occurrence_n = 0;
			
			switch($workaround):
				//Not really a workaround. Just add the occurrence and finish.
				case 'once':
					$current->setTime($H,$i );
					$occurrences[] = clone $current;
					break;
				
				//Loops for monthly events that require php5.3 functionality
				case 'php5.2':
					while( $current <= $schedule_last || $occurrence_n < $number_occurrences ):
						$current->setTime($H,$i );
						$occurrences[] = clone $current;	
						$current = _eventorganiser_php52_modify($current,$interval);
						$occurrence_n++;
					endwhile; 
					break;

				//Loops for monthly events on the 29th/30th/31st
				case 'short months':
					 $day_int =intval($start_day->format('d'));
	
					//Set the first month
					$current_month= clone $start_day;
					$current_month = date_create($current_month->format('Y-m-1'));
				
					while( $current_month <= $schedule_last || $occurrence_n < $number_occurrences ):
						$month_int = intval($current_month->format('m'));		
						$year_int = intval($current_month->format('Y'));		

						if( checkdate($month_int , $day_int , $year_int) ){
							$current = new DateTime($day_int.'-'.$month_int.'-'.$year_int, $timezone);
							$current->setTime($H,$i );
							$occurrences[] = 	clone $current;
							$occurrence_n++;
						}
						$current_month->modify($interval);
					endwhile;	
					break;

				//To be used for yearly events occuring on Feb 29
				case 'leap year':
					$current_year = clone $current;
					$current_year->modify('-1 day');

					while( $current_year <= $schedule_last || $occurrence_n < $number_occurrences  ):	
						$is_leap_year = (int) $current_year->format('L');

						if( $is_leap_year ){
							$current = clone $current_year;
							$current->modify('+1 day');
							$current->setTime($H,$i );
							$occurrences[] = clone $current;
							$occurrence_n++;
						}

						$current_year->modify( $interval );
					endwhile;
					break;
			
				default:
					while( $current <= $schedule_last || $occurrence_n < $number_occurrences  ):
						$current->setTime($H,$i );
						$occurrences[] = clone $current;	
						$current->modify( $interval );
						$occurrence_n++;
					endwhile;
					break;

				endswitch;//End 'workaround' switch;
		endforeach;

		//Now schedule meta is set up and occurrences are generated.
		if( $number_occurrences > 0 ){
			//If recurrence is limited by #occurrences. Do that here.
			sort( $occurrences );
			$occurrences =  array_slice( $occurrences, 0, $number_occurrences );
		}
		
		//Add inclusions, removes exceptions and duplicates
		if( defined( 'WP_DEBUG' ) && WP_DEBUG ){
			//Make sure 'included' dates doesn't appear in generate date
			$include = array_udiff( $include, $occurrences, '_eventorganiser_compare_dates' );
		}
		$occurrences = array_merge($occurrences, $include); 
		$occurrences = array_udiff($occurrences, $exclude, '_eventorganiser_compare_dates');
		$occurrences = _eventorganiser_remove_duplicates($occurrences);

		//Sort occurrences
		sort($occurrences);
		
		if( empty( $occurrences ) || !$occurrences[0] || !( $occurrences[0] instanceof DateTime ) ){
			return new WP_Error('eo_error',__('Event does not contain any dates.','eventorganiser'));
		}
		$schedule_start = clone $occurrences[0];
		$schedule_last = clone end($occurrences);

		$_event_data = array(
			'start'=>$start,
			'end'=>$end,
			'all_day'=>$all_day,
			'schedule'=>$schedule,
			'schedule_meta'=>$schedule_meta,
			'frequency'=>$frequency,
			'schedule_start'=>$schedule_start,
			'schedule_last'=>$schedule_last,
			'exclude'=>$exclude,
			'include'=>$include,
			'occurrences'=>$occurrences
		);
		
		/**
		 * Filters the event schedule after its dates has been generated by a given schedule.
		 * 
		 * The filtered array is an array of occurrences generated from a 
		 * schedule which may include:
		 * 
		 * * **start** (DateTime) -  when the event starts
		 * * **end** (DateTime) - when the event ends
		 * * **all_day** (Bool) - If the event is all day or no
		 * * **all_day** (Bool) - If the event is all day or not
		 * * **schedule** (String) - One of once|weekl|daily|monthly|yearly|custom
		 * * **schedule_meta** (Array|String) - See documentation for `eo_insert_event()`
		 * * **frequency** (int) - The frequency of which the event repeats
		 * * **schedule_last** (DateTime) - date of last occurrence of event
		 * * **number_occurrences** (int) - number of times the event should repeat (if `schedule_last` is not specified).
		 * * **exclude** (array) - Array of DateTime objects  to exclude from the schedule
		 * * **include** (array) - Array of DateTime objects to include in the schedule
		 * * **occurrences** (array) - Array of DateTime objects generated from the above schedule.
		 * 
		 * @param array The event schedule with generated occurrences.
		 * @param array The original event schedule (without occurrences).  
		 */
		$_event_data = apply_filters( 'eventorganiser_generate_occurrences', $_event_data, $event_data );
		return $_event_data;
	}

/**
 * Generates the ICS RRULE fromthe event schedule data. 
 * @access private
 * @ignore
 * @since 1.0.0
 * @package ical-functions
 *
 * @param int $post_id The event (post) ID. Uses current event if empty.
 * @return string The RRULE to be used in an ICS calendar
 */
function eventorganiser_generate_ics_rrule($post_id=0){

		$post_id = (int) ( empty($post_id) ? get_the_ID() : $post_id);

		$rrule = eo_get_event_schedule($post_id);
		if( !$rrule )
			return false;

		extract($rrule);
		
		$schedule_last->setTimezone( new DateTimeZone('UTC') );
		$schedule_last = $schedule_last->format( 'Ymd\THis\Z' );

		switch($schedule):
			case 'once':
				return false;

			case 'yearly':
				return "FREQ=YEARLY;INTERVAL=".$frequency.";UNTIL=".$schedule_last;

			case 'monthly':
				//TODO Account for possible day shifts with timezone set to UTC
				$reoccurrence_rule = "FREQ=MONTHLY;INTERVAL=".$frequency.";";
				$reoccurrence_rule.=$schedule_meta.";";
				$reoccurrence_rule.= "UNTIL=".$schedule_last;
				return $reoccurrence_rule;
	
			case 'weekly':
				
				if( !eo_is_all_day( $post_id ) ){
					//None all day event, setting event timezone to UTC may cause it to shift days.
					//E.g. a 9pm Monday event in New York will a Tuesday event in UTC.
					//We may need to correct the BYDAY attribute to be valid for UTC.
					
					$days_of_week = array( 'SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA' );
					$UTC = new DateTimeZone('UTC');
					
					//Get day shift upon timezone set to UTC
					$start = eo_get_schedule_start( DATETIMEOBJ, $post_id );
					$local_day = (int) $start->format( 'w' );
					$start->setTimezone( $UTC );
					$utc_day = (int) $start->format( 'w' );
					$diff = $utc_day - $local_day + 7; //ensure difference is positive (should be 0, +1 or +6).
					
					//If there is a shift correct BYDAY
					if( $diff ){
						$utc_days = array();
					
						foreach( $schedule_meta as $day ){
							$utc_day_index = ( array_search( $day, $days_of_week ) + $diff ) %7;
							$utc_days[] = $days_of_week[$utc_day_index];
						}
						$schedule_meta = $utc_days;
					}
					
				}
				
				return "FREQ=WEEKLY;INTERVAL=".$frequency.";BYDAY=".implode(',',$schedule_meta).";UNTIL=".$schedule_last;

			case 'daily':
				return "FREQ=DAILY;INTERVAL=".$frequency.";UNTIL=".$schedule_last;

			default:
		endswitch;
		return false;
}

/**
 * Removes a single occurrence and adds it to the event's 'excluded' dates.
 * @access private
 * @ignore
 * @since 1.5
 *
 * @param int $post_id The event (post) ID
 * @param int $event_id The event occurrence ID
 * @return bool|WP_Error True on success, WP_Error object on failure
 */
	function _eventorganiser_remove_occurrence($post_id=0, $event_id=0){
		global $wpdb;

		$remove = $wpdb->get_row($wpdb->prepare(
			"SELECT {$wpdb->eo_events}.StartDate, {$wpdb->eo_events}.StartTime  
			FROM {$wpdb->eo_events} 
			WHERE post_id=%d AND event_id=%d",$post_id,$event_id));

		if( !$remove )
			return new WP_Error('eo_notice', '<strong>'.__("Occurrence not deleted. Occurrence not found.",'eventorganiser').'</strong>');

		$date = trim($remove->StartDate).' '.trim($remove->StartTime);

		$event_details = get_post_meta( $post_id,'_eventorganiser_event_schedule',true);

		if( ($key = array_search($date,$event_details['include'])) === false){
			//If the date was not manually included, add it to the 'exclude' array
			$event_details['exclude'][] = $date;
		}else{
			//If the date was manually included, just remove it from the included dates
			unset($event_details['include'][$key]);
		}

		//Remove the date from the occurrences
		if( isset($event_details['_occurrences'][$event_id]) ){
			unset($event_details['_occurrences'][$event_id]);
		}

		//Update post meta and delete date from events table
		update_post_meta( $post_id,'_eventorganiser_event_schedule',$event_details);		
		eo_delete_event_occurrences( $post_id, $event_id );

		//Clear cache
		_eventorganiser_delete_calendar_cache();

		return true;
	}
?>
