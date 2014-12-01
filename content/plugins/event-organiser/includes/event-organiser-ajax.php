<?php
/*
* Deals with the plug-in's AJAX requests
*/

add_action( 'wp_ajax_eventorganiser-fullcal', 'eventorganiser_public_fullcalendar' ); 
add_action( 'wp_ajax_nopriv_eventorganiser-fullcal', 'eventorganiser_public_fullcalendar' ); 
add_action( 'wp_ajax_event-admin-cal', 'eventorganiser_admin_calendar' ); 
add_action( 'wp_ajax_eofc-format-time', 'eventorganiser_admin_cal_time_format' ); 
add_action( 'wp_ajax_eo-search-venue', 'eventorganiser_search_venues' ); 
add_action( 'wp_ajax_nopriv_eo_widget_agenda', 'eventorganiser_widget_agenda' );
add_action( 'wp_ajax_eo_widget_agenda', 'eventorganiser_widget_agenda' );
add_action( 'wp_ajax_nopriv_eo_widget_cal', 'eventorganiser_widget_cal' );
add_action( 'wp_ajax_eo_widget_cal', 'eventorganiser_widget_cal' );
add_action( 'wp_ajax_eo_toggle_addon_page', 'eventorganiser_ajax_toggle_addon_page' );


/**
 * Ajax response for the public full calendar. This returns events to be displayed on the front-end full calendar
 *
 *@since 1.3
 *@access private
 *@ignore
*/
function eventorganiser_public_fullcalendar() {
	$request = array(
		'event_start_before'=>$_GET['end'],
		'event_end_after'=>$_GET['start'],
	);

	$time_format = !empty($_GET['timeformat']) ? $_GET['timeformat'] : get_option('time_format');

	//Restrict by category and/or venue
	if( !empty($_GET['category']) ){
		$cats = explode(',',esc_attr($_GET['category']));
		$request['tax_query'][] = array(
				'taxonomy' => 'event-category',
				'field' => 'slug',
				'terms' => $cats,
				'operator' => 'IN'
			);
	}

	if( !empty($_GET['venue']) ){
		$venues = explode(',',esc_attr($_GET['venue']));
		$request['tax_query'][] = array(
				'taxonomy' => 'event-venue',
				'field' => 'slug',
				'terms' => $venues,
				'operator' => 'IN'
			);
	}

	if( !empty( $_GET['users_events'] ) && 'false' != $_GET['users_events'] ){
		$request['bookee_id'] = get_current_user_id();	
	}
	
	if( !empty( $_GET['event_occurrence__in'] ) ){
		$request['event_occurrence__in'] = $_GET['event_occurrence__in'];
	}
	

	$presets = array('numberposts'=>-1, 'group_events_by'=>'','showpastevents'=>true);
	
	if( current_user_can( 'read_private_events' ) ){
		$priv = '_priv';
		$post_status = array( 'publish', 'private' );
	}else{
		$priv = false;
		$post_status = array( 'publish' );
	}

	//Retrieve events		
	$query    = array_merge( $request, $presets );
	
	//In case polylang is enabled with events as translatable. Include locale in cache key.
	$options = get_option( 'polylang' );
	if( defined( 'POLYLANG_VERSION' ) && !empty( $options['post_types']  ) && in_array( 'event', $options['post_types'] ) ){
		$key = "eo_fc_".md5( serialize( $query ). $time_format . get_locale() );
	}else{
		$key = "eo_fc_".md5( serialize( $query ). $time_format );
	}
	
	$calendar = get_transient( "eo_full_calendar_public{$priv}");
	if( $calendar && is_array( $calendar ) && isset( $calendar[$key] ) ){
		$events_array = $calendar[$key];
		/**
	 	* Filters the event before it is sent to the calendar. 
	 	*
	 	* **Note:** This filters the response immediately before sending, and after 
	 	* the cache is saved/retrieved. Changes made on this filter are not cached.
	 	*
	 	* @package fullCalendar
	 	* @param array  $events_array An array of events (array).
	 	* @param array  $query        The query as given to eo_get_events() 
	 	*/
		$events_array = apply_filters( 'eventorganiser_fullcalendar', $events_array, $query );
	
		echo json_encode( $events_array );
		exit;
	}

	$query['post_status'] = $post_status;
	
	$events = eo_get_events( $query );
	$events_array = array();

	//Blog timezone
	$tz = eo_get_blog_timezone();

	//Loop through events
	global $post;
	if ($events) : 
		foreach  ($events as $post) :
			setup_postdata( $post );
			$event=array();

			//Title and url
			$event['title']=html_entity_decode(get_the_title($post->ID),ENT_QUOTES,'UTF-8');
			$link = esc_js(get_permalink( $post->ID));
			
			/**
			 * Filters the link to the event's page on the admin calendar.
			 * 
			 * **Note:** As the calendar is cached, changes made using this filter
			 * will not take effect immediately. You can clear the cache by 
			 * updating an event.
			 * 
			 * ### Example
			 * 
			 *    //Remove link if from calendar if event has no content
			 *    add_filter('eventorganiser_calendar_event_link','myprefix_maybe_no_calendar_link',10,3);
 			 *    function myprefix_maybe_no_calendar_link( $link, $event_id, $occurrence_id ){
 			 *        $the_post = get_post($post_id);
 			 *        if( empty($the_post->post_content) ){
 			 *            return false;
 			 *        }
 			 *        return $link;
 			 *    }
			 *
			 * @package fullCalendar
			 * @param string $link          The url the event points to on the calendar.
			 * @param int    $event_id      The event's post ID.
			 * @param int    $occurrence_id The event's occurrence ID.
			 */
			$link = apply_filters('eventorganiser_calendar_event_link',$link,$post->ID,$post->occurrence_id);
			$event['url'] = $link;
			
			//All day or not?
			$event['allDay'] = eo_is_all_day();
	
			//Get Event Start and End date, set timezone to the blog's timzone
			$event_start = new DateTime($post->StartDate.' '.$post->StartTime, $tz);
			$event_end = new DateTime($post->EndDate.' '.$post->FinishTime, $tz);
			$event['start']= $event_start->format('Y-m-d\TH:i:s\Z');
			$event['end']= $event_end->format('Y-m-d\TH:i:s\Z');	

			//Don't use get_the_excerpt as this adds a link
			$excerpt_length = apply_filters('excerpt_length', 55);

			$description = wp_trim_words( strip_shortcodes(get_the_content()), $excerpt_length, '...' );

			if( $event_start->format('Y-m-d') != $event_end->format('Y-m-d') ){
				//Start & ends on different days

				if( !eo_is_all_day() ){
					//Not all day, include time
					 $date = eo_format_datetime($event_start,'F j '.$time_format).' - '.eo_format_datetime($event_end,'F j '.$time_format);
				}else{
					//All day, don't include date
					if( $event_start->format('Y-m') == $event_end->format('Y-m') ){
						//Same month
						 $date = eo_format_datetime($event_start,'F j').' - '.eo_format_datetime($event_end,'j, Y');
					}else{
						//Different month
						 $date = eo_format_datetime($event_start,'F j').' - '.eo_format_datetime($event_end,'F j');
					}
				}

			}else{
				//Start and end on the same day
							
				if( !eo_is_all_day() ){
					//Not all day, include time
					 $date = eo_format_datetime($event_start,$format="F j, Y $time_format").' - '.eo_format_datetime($event_end,$time_format);
				}else{
					//All day
					 $date = eo_format_datetime($event_start,$format='F j, Y');
				}
			}
			$description = $date.'</br></br>'.$description;
			
			/**
			 * Filters the description of the event as it appears on the calendar tooltip.
			 * 
			 * **Note:** As the calendar is cached, changes made using this filter
			 * will not take effect immediately. You can clear the cache by 
			 * updating an event.
			 *
			 * @link https://gist.github.com/stephenh1988/4040699 Including venue name in tooltip.
			 * 
			 * @package fullCalendar
			 * @param string  $description   The event's tooltip description.
			 * @param int     $event_id      The event's post ID.
			 * @param int     $occurrence_id The event's occurrence ID.
			 * @param WP_Post $post          The event (post) object.
			 */
			$description = apply_filters('eventorganiser_event_tooltip', $description, $post->ID,$post->occurrence_id,$post);
			$event['description'] = $description;
			
			$event['className']   = eo_get_event_classes();
			$event['className'][] = 'eo-event';
			
			//Colour past events
			$now = new DateTime(null,$tz);
			if($event_start <= $now)
				$event['className'][] = 'eo-past-event'; //deprecated. use eo-event-past or eo-event-running
			else
				$event['className'][] = 'eo-future-event'; //deprecated. use eo-event-future
				
			//Include venue if this is set
			$venue = eo_get_venue($post->ID);

			if($venue && !is_wp_error($venue)){
				$event['className'][]= 'venue-'.eo_get_venue_slug($post->ID);//deprecated. use eo-event-venue-{slug}
				$event['venue']=$venue;
			}
				
			//Event categories
			$terms = get_the_terms( $post->ID, 'event-category' );
			$event['category']=array();
			if($terms):
				foreach ($terms as $term):
					$event['category'][]= $term->slug;
					$event['className'][]='category-'.$term->slug;//deprecated. use eo-event-cat-{slug}
				endforeach;
			endif;
			
			//Event tags
			if( eventorganiser_get_option('eventtag') ){
				$terms = get_the_terms( $post->ID, 'event-tag' );
				$event['tags'] = array();
				if( $terms && !is_wp_error( $terms ) ):
					foreach ($terms as $term):
						$event['tags'][]= $term->slug;
						$event['className'][]='tag-'.$term->slug;//deprecated. use eo-event-tag-{slug}
					endforeach;
				endif;
			}

			//Event colour
			$event['textColor'] = '#ffffff'; //default text colour
			if( eo_get_event_color() ) {
				$event['color'] = eo_get_event_color();
				$event['textColor'] = eo_get_event_textcolor( $event['color'] );
			}

			//Add event to array
			/**
			 * Filters the event before it is sent to the calendar. 
			 *
			 * The event is an array with various key/values, which is seralised and sent
			 * to the calendar.
			 *
			 * **Note:** As the calendar is cached, changes made using this filter
			 * will not take effect immediately. You can clear the cache by
			 * updating an event.
			 *
			 * @package fullCalendar
			 * @param array  $event         The event (array)
			 * @param int    $event_id      The event's post ID.
			 * @param int    $occurrence_id The event's occurrence ID.
			 */
			$event = apply_filters('eventorganiser_fullcalendar_event',$event, $post->ID,$post->occurrence_id);
			if( $event ){
				$events_array[] = $event;
			}

		endforeach;
		wp_reset_postdata();
	endif;

	if( !$calendar || !is_array($calendar) )
		$calendar = array();
	
	$calendar[$key] = $events_array;

	set_transient( "eo_full_calendar_public{$priv}",$calendar, 60*60*24);
	
	$events_array = apply_filters( 'eventorganiser_fullcalendar', $events_array, $query );

	//Echo result and exit
	echo json_encode( $events_array );
	exit;
}


/**
 * Ajax response for the admin full calendar. 
 *
 * This gets events and generates summaries for events to be displayed.
 * in the admin 'calendar view'. 
 * Applies 'eventorganiser_admin_cal_summary' to event summary
 * Applies eventorganiser_admin_calendar to the event array
 *
 *@since 1.0
 *@access private
 *@ignore
*/
function eventorganiser_admin_calendar() {
		//request
		$request = array(
			'event_end_after'=>$_GET['start'],
			'event_start_before'=>$_GET['end']
		);

		//Presets
		$presets = array( 
			'posts_per_page'=>-1,
			'post_type'=>'event',
			'group_events_by'=>'',
			'perm' => 'readable');

		$calendar = get_transient('eo_full_calendar_admin');
		$key = $_GET['start'].'--'.$_GET['end'] . 'u='.get_current_user_id();

		if( ( !defined('WP_DEBUG') || !WP_DEBUG ) && $calendar && is_array($calendar) && isset($calendar[$key]) ){
			echo json_encode($calendar[$key]);
			exit;
		}
	
		//Create query
		$query_array = array_merge($presets, $request);	
		$query = new WP_Query($query_array );

		//Retrieve events		
		$query->get_posts();
		$eventsarray = array();

		//Blog timezone
		$tz = eo_get_blog_timezone();

		//Loop through events
		global $post;
		if ( $query->have_posts() ) : 
			while ( $query->have_posts() ) : $query->the_post(); 
				$event=array();
				$colour='';
				//Get title, append status if applicable
				$title = get_the_title();
				if(!empty($post->post_password)){
					$title.=' - '.__('Protected');
				}elseif($post->post_status=='private'){
					$title.=' - '.__('Private');
				}elseif	($post->post_status=='draft'){
					$title.=' - '.__('Draft');
				}
				$event['title']= html_entity_decode ($title,ENT_QUOTES,'UTF-8');

				$schedule = eo_get_event_schedule($post->ID);

				//Check if all day, set format accordingly
				if( $schedule['all_day'] ){
					$event['allDay'] = true;
					$format = get_option('date_format');
				}else{
					$event['allDay'] = false;
					$format = get_option('date_format').'  '.get_option('time_format');
				}

				//Get author (or organiser)
				$organiser = get_userdata( $post->post_author)->display_name;
	
				//Get Event Start and End date, set timezone to the blog's timzone
				$event_start = new DateTime($post->StartDate.' '.$post->StartTime, $tz);
				$event_end = new DateTime($post->EndDate.' '.$post->FinishTime, $tz);
	
				$event['start']= $event_start->format('Y-m-d\TH:i:s\Z');
				$event['end']= $event_end->format('Y-m-d\TH:i:s\Z');
	
				//Produce summary of event
				$summary= "<table class='form-table' >"
							."<tr><th> ".__('Start','eventorganiser').": </th><td> ".eo_format_datetime($event_start,$format)."</td></tr>"
							."<tr><th> ".__('End','eventorganiser').": </th><td> ".eo_format_datetime($event_end, $format)."</td></tr>";
				if( eo_is_multi_event_organiser() ){
					$summary .= "<tr><th> ".__('Organiser','eventorganiser').": </th><td>".$organiser."</td></tr>";
				}
	
				$event['className']=array('event');

				 $now = new DateTime(null,$tz);
				if($event_start <= $now)
					$event['className'][]='past-event';

				//Include venue if this is set
				$venue = eo_get_venue($post->ID);

				if($venue && !is_wp_error($venue)){
					$summary .="<tr><th>".__('Where','eventorganiser').": </th><td>".eo_get_venue_name($venue)."</td></tr>";
					$event['className'][]= 'venue-'.eo_get_venue_slug($post->ID);
					$event['venue']=$venue;
				}

				/**
				 * Filters the summary of the event as it appears in the admin 
				 * calendar's modal.
				 *
				 * **Note:** As the calendar is cached, changes made using this filter
				 * will not take effect immediately. You can clear the cache by
				 * updating an event.
				 *
				 * @package admin-calendar
				 * @param string  $summary       The event (admin) summary,
				 * @param int     $event_id      The event's post ID.
				 * @param int     $occurrence_id The event's occurrence ID.
				 * @param WP_Post $post          The event (post) object.
				 */
				$summary = apply_filters('eventorganiser_admin_cal_summary',$summary,$post->ID,$post->occurrence_id,$post);
	
				$summary .= "</table><p>";
							
				//Include schedule summary if event reoccurrs
			
				if( $schedule['schedule'] != 'once' )
					$summary .='<em>'.__('This event reoccurs','eventorganiser').' '.eo_get_schedule_summary().'</em>';
				$summary .='</p>';

				//Include edit link in summary if user has permission
				if (current_user_can('edit_event', $post->ID)){
					$edit_link = get_edit_post_link( $post->ID,'');
					$summary .= "<span class='edit'><a title='Edit this item' href='".$edit_link."'> ".__('Edit Event','eventorganiser')."</a></span>";
					$event['url']= $edit_link;
				}

				//Include a delete occurrence link in summary if user has permission
				if (current_user_can('delete_event', $post->ID)){
					$admin_url  = admin_url('edit.php');

					$delete_url = add_query_arg(array(
						'post_type'=>'event',
						'page'=>'calendar',
						'series'=>$post->ID,
						'event'=>$post->occurrence_id,
						'action'=>'delete_occurrence'
					),$admin_url);

					$delete_url  = wp_nonce_url( $delete_url , 'eventorganiser_delete_occurrence_'.$post->occurrence_id);

					$summary .= "<span class='delete'>
					<a class='submitdelete' style='color:red;float:right' title='".__('Delete this occurrence','eventorganiser')."' href='".$delete_url."'> ".__('Delete this occurrence','eventorganiser')."</a>
					</span>";

					if( $schedule['schedule'] !='once'){
						$break_url = add_query_arg(array(
							'post_type'=>'event',
							'page'=>'calendar',
							'series'=>$post->ID,
							'event'=>$post->occurrence_id,
							'action'=>'break_series'
						),$admin_url);
						$break_url  = wp_nonce_url( $break_url , 'eventorganiser_break_series_'.$post->occurrence_id);

						$summary .= "<span class='break'>
						<a class='submitbreak' style='color:red;float:right;padding-right: 2em;' title='".__('Break this series','eventorganiser')."' href='".$break_url."'> ".__('Break this series','eventorganiser')."</a>
						</span>";
					}

				}

				//Event categories
				$terms = get_the_terms( $post->ID, 'event-category' );
				$event['category']=array();
				if($terms):
					foreach ($terms as $term):
						$event['category'][]= $term->slug;
						$event['className'][]='category-'.$term->slug;
					endforeach;
				endif;

				//Event colour
				$event['textColor'] = '#ffffff'; //default text colour
				if( eo_get_event_color() ) {
					$event['color'] = eo_get_event_color();
					$event['textColor'] = eo_get_event_textcolor( $event['color'] ) ? eo_get_event_textcolor( $event['color'] ) : '#ffffff';
				}

				//Event summary
				$event['summary'] = '<div id="eo-cal-meta">'.$summary.'</div>';

				//Filter the event array
				/**
				 * @ignore
				 */
				$event = apply_filters('eventorganiser_admin_calendar',$event, $post);
				/**
				 * Filters the event before its sent to the admin calendar.
				 *
				 * **Note:** As the calendar is cached, changes made using this filter
				 * will not take effect immediately. You can clear the cache by
				 * updating an event.
				 * 
				 * @package admin-calendar
				 * @param array $event         The event array.
				 * @param int   $event_id      The event's post ID.
				 * @param int   $occurrence_id The event's occurrence ID.
				 */
				$event = apply_filters('eventorganiser_admin_fullcalendar_event', $event, $post->ID, $post->occurrence_id );

				//Add event to array
				$eventsarray[]=$event;
			endwhile;
		endif;

		if( !$calendar || !is_array($calendar) )
			$calendar = array();
	
		$calendar[$key] = $eventsarray;

		set_transient('eo_full_calendar_admin',$calendar, 60*60*24);

		//Echo result and exit
		echo json_encode($eventsarray);
		exit;
}


/**
 * Ajax response for the widget calendar 
 *
 *  This gets the month being requested and generates the
 * html code to view that month and its events. 
 *
 *@since 1.0
 *@access private
 *@ignore
*/
function eventorganiser_widget_cal() {

		/*Retrieve the month we are after. $month must be a 
		DateTime object of the first of that month*/
		if(isset($_GET['eo_month'])){
			$month  = new DateTime($_GET['eo_month'].'-01'); 
		}else{
			$month = new DateTime('now');
			$month = date_create($month->format('Y-m-1'));
		}		

		$args = array();

		//Restrict by category and/or venue
		foreach( array('event-venue','event-category') as $tax ){
			if( empty($_GET[$tax]) )
				continue;

			$terms = explode(',',trim($_GET[$tax]));

			$args['tax_query'][] = array(
					'taxonomy' => $tax,
					'field' => 'slug',
					'terms' => $terms,
					'operator' => 'IN'
				);
		}

		//Options for the calendar
		$args['showpastevents'] = (empty($_GET['showpastevents']) ? 0 : 1);
		$args['link-to-single'] = (empty($_GET['link-to-single']) ? 0 : 1);
		$args['show-long'] = (empty($_GET['show-long']) ? 0 : 1);

		echo json_encode(EO_Calendar_Widget::generate_output($month,$args));
		exit;
	}


/**
 * Ajax response for the agenda widget
*
 * This gets the month being viewed and generates the
 *
 *@since 1.0
 *@access private
 *@ignore
*/
function eventorganiser_widget_agenda() {
		global $wpdb;

		$number = (int) $_GET['instance_number'];
		$wid = new EO_Events_Agenda_Widget();
		$settings =  $wid->get_settings();
		$instance = $settings[$number];
		$today= new DateTime('now', eo_get_blog_timezone());
		$query=array();
		$return_array = array();

		$query['mode'] = !empty($instance['mode']) ? $instance['mode'] : 'day';
		$query['direction'] = intval($_GET['direction']);
		$query['date'] = ($query['direction'] <1? $_GET['start'] : $_GET['end']);
		$query['order'] = ($query['direction'] <1? 'DESC' : 'ASC');

		$key = 'eo_ag_'.md5(serialize($query)).get_locale();
		$agenda = get_transient('eo_widget_agenda');
		if( $agenda && is_array($agenda) && isset($agenda[$key]) ){
			echo json_encode($agenda[$key]);
			exit;
		}

		if( 'day' == $query['mode'] ){		
			//Day mode
			$selectDates="SELECT DISTINCT StartDate FROM {$wpdb->eo_events}";
			$whereDates = " WHERE {$wpdb->eo_events}.StartDate".( $query['order']=='ASC' ? " >= " : " <= ")."%s ";
			$whereDates .= " AND {$wpdb->eo_events}.StartDate >= %s ";
			$orderlimit = "ORDER BY  {$wpdb->eo_events}.StartDate {$query['order']} LIMIT 4";
			$dates = $wpdb->get_col($wpdb->prepare($selectDates.$whereDates.$orderlimit, $query['date'],$today->format('Y-m-d')));

			if(!$dates)
				return false;

			$query['date1']  = min($dates[0],$dates[count($dates)-1]);
			$query['date2'] = max($dates[0],$dates[count($dates)-1]);

		}elseif( 'week' == $query['mode'] ){		
			//Week mode - find the week of the next/previous event
			$selectDates="SELECT DISTINCT StartDate FROM {$wpdb->eo_events}";
			$whereDates = " WHERE {$wpdb->eo_events}.StartDate".( $query['order']=='ASC' ? " > " : " < ")."%s ";
			$whereDates .= " AND {$wpdb->eo_events}.StartDate >= %s ";
			$orderlimit = "ORDER BY  {$wpdb->eo_events}.StartDate {$query['order']} LIMIT 1";
			$date = $wpdb->get_row($wpdb->prepare($selectDates.$whereDates.$orderlimit, $query['date'],$today->format('Y-m-d')));

			if(!$date)
				return false;

			$datetime = new DateTime($date->StartDate, eo_get_blog_timezone());

			//Get the week day, and the start of the week	
			$week_start_day = (int) get_option('start_of_week');
			$event_day = (int) $datetime->format('w');
			$offset_from_week_start = ($event_day - $week_start_day +7)%7;
			$week_start_date = clone $datetime;
			$week_start_date->modify('- '.$offset_from_week_start.' days');
			$week_end_date = clone $week_start_date;
			$week_end_date->modify('+6 days');//Query is inclusive.

			$query['date1']  = $week_start_date->format('Y-m-d');
			$query['date2'] = $week_end_date->format('Y-m-d'); 

		}else{
			//Month mode - find the month of the next date
			$selectDates="SELECT DISTINCT StartDate FROM {$wpdb->eo_events}";
			$whereDates = " WHERE {$wpdb->eo_events}.StartDate".( $query['order']=='ASC' ? " > " : " < ")."%s ";
			$whereDates .= " AND {$wpdb->eo_events}.StartDate >= %s ";
			$orderlimit = "ORDER BY  {$wpdb->eo_events}.StartDate {$query['order']} LIMIT 1";
			$date = $wpdb->get_row($wpdb->prepare($selectDates.$whereDates.$orderlimit, $query['date'],$today->format('Y-m-d')));

			if(!$date)
				return false;

			$datetime = new DateTime($date->StartDate, eo_get_blog_timezone());
			$query['date1']  = $datetime->format('Y-m-01');
			$query['date2'] = $datetime->format('Y-m-t'); 
		}

		$events = eo_get_events(array(
			'event_start_after'=>$query['date1'],
			'event_start_before'=>$query['date2']
		));

		global $post;
		foreach ($events as $post):
			$return_array[] = array(
				'StartDate'=>$post->StartDate,
				'display'=>eo_get_the_start($instance['group_format']),
				'time'=> ( ($instance['mode']=='day' && eo_is_all_day())  ? __('All Day','eventorganiser') : eo_get_the_start($instance['item_format']) ),
				'post_title'=>get_the_title(),
				'color'=>eo_get_event_color(),
				'event_url'=>get_permalink(),
				'link'=>'<a href="'.get_permalink().'">'.__('View','eventorganiser').'</a>',
				'Glink'=>'<a href="'.eo_get_add_to_google_link().'" target="_blank">'.__('Add To Google Calendar','eventorganiser').'</a>'
			);
		endforeach;

		if( !$agenda || !is_array($agenda) )
			$agenda = array();
	
		$agenda[$key] = $return_array;

		set_transient('eo_widget_agenda',$agenda, 60*60*24);

		echo json_encode($return_array);
		exit;
}


/**
 * Ajax response for searcheing venues. Searches by venue name.
 *
 *@since 1.0
 *@access private
 *@ignore
*/
function eventorganiser_search_venues() {
		
		// Query the venues with the given term
		$value = trim(esc_attr($_GET["term"]));
		$venues = eo_get_venues(array('search'=>$value));

		foreach($venues as $venue){
			$venue_id = (int) $venue->term_id;

			if( !isset($term->venue_address) ){
				/* This is all deprecated - use the API {@link http://codex.wp-event-organiser.com/function-eo_get_venue_address.html} */
				$address = eo_get_venue_address($venue_id);
				$venue->venue_address =  isset($address['address']) ? $address['address'] : '';
				$venue->venue_postal =  isset($address['postcode']) ? $address['postcode'] : ''; 
				$venue->venue_postcode =  isset($address['postcode']) ? $address['postcode'] : '';
				$venue->venue_city =  	isset($address['city']) ? $address['city'] : '';
				$venue->venue_country =  isset($address['country']) ? $address['country'] : '';
				$venue->venue_state =  isset($address['state']) ? $address['state'] : '';
			}

			if( !isset($venue->venue_lat) || !isset($venue->venue_lng) ){
				$venue->venue_lat =  number_format(floatval(eo_get_venue_lat($venue_id)), 6);
				$venue->venue_lng =  number_format(floatval(eo_get_venue_lng($venue_id)), 6);
			}
		}
		
		$tax = get_taxonomy( 'event-venue' );
		$novenue = array( 'term_id' => 0,'name' => $tax->labels->no_item );
		$venues =array_merge (array($novenue),$venues);

		//echo JSON to page  
		$response = $_GET["callback"] . "(" . json_encode($venues) . ")";  
		echo $response;  
		exit;
}

/**
 * Saves the ajax request to update the 24/12 hour setting of admin calendar
 *
 *@since 1.5
 *@access private
 *@ignore
*/
function eventorganiser_admin_cal_time_format(){
	$is24 = (  $_POST['is24'] == 'false' ? 1: 0 );
	$user =wp_get_current_user();
	$is12hour = update_user_meta($user->ID,'eofc_time_format',$is24);
	exit();
}

/**
 * Toggle visibility of extensionpage.
 *
 *@since 2.3
 *@access private
 *@ignore
*/
function eventorganiser_ajax_toggle_addon_page(){
	
	if( !isset( $_POST['hide_addon_page'] ) || !current_user_can( 'manage_options' ) )
		exit();
	
	$hide = (int) ( strtolower( $_POST['hide_addon_page'] ) == 'true' );
	$options = eventorganiser_get_option( false );
	$options['hide_addon_page'] = $hide;

	update_option( 'eventorganiser_options', $options );
	exit(1);
}
?>
