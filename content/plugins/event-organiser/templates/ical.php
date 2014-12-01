<?php

echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:-//" . get_bloginfo('name') . "//NONSGML Events//EN\r\n";
echo "CALSCALE:GREGORIAN\r\n";
echo "X-WR-CALNAME:" . get_bloginfo('name') . " - Events\r\n";
echo "X-ORIGINAL-URL:" . get_post_type_archive_link('event') . "\r\n";
echo "X-WR-CALDESC:" . get_bloginfo('name') . " - Events\r\n";

// Loop through events
if ( have_posts() ) :

	$now = new DateTime();
	$dtstamp = $now->format('Ymd\THis\Z');
	$UTC_tz = new DateTimeZone('UTC');

	while( have_posts() ): the_post();
	
		global $post;

		// If event has no corresponding row in events table then skip it
		if ( !isset($post->event_id) || $post->event_id == -1 )
			continue;

		$start = eo_get_the_start(DATETIMEOBJ);
		$end = eo_get_the_end(DATETIMEOBJ);
		$created_date = get_post_time('Ymd\THis\Z',true);
		$modified_date = get_post_modified_time('Ymd\THis\Z',true);
		$schedule_data = eo_get_event_schedule();

		// Set up start and end date times
		if ( eo_is_all_day() ) {
			$format =	'Ymd';
			$start_date = $start->format($format);
			$end->modify('+1 minute');
			$end_date = $end->format($format);				
		} else {
			$format =	'Ymd\THis\Z';
			$start->setTimezone($UTC_tz);
			$start_date =$start->format($format);
			$end->setTimezone($UTC_tz);
			$end_date = $end->format($format);
		}

		// Generate Event status
		if ( get_post_status(get_the_ID()) == 'publish' )
			$status = 'CONFIRMED';
		else
			$status = 'TENTATIVE';

		// Output event
		echo "BEGIN:VEVENT\r\n";
		echo "UID:" . eo_get_event_uid() . "\r\n";
		echo "STATUS:" . $status . "\r\n";
		echo "DTSTAMP:" . $dtstamp . "\r\n";
		echo "CREATED:" . $created_date . "\r\n";
		echo "LAST-MODIFIED:" . $modified_date . "\r\n";
		
		if ( eo_is_all_day() ) :
			echo "DTSTART;VALUE=DATE:" . $start_date . "\r\n";
			echo "DTEND;VALUE=DATE:" . $end_date . "\r\n";
		else :
			echo "DTSTART:" . $start_date . "\r\n";
			echo "DTEND:" . $end_date . "\r\n";
		endif;
		
		if ( $reoccurrence_rule = eventorganiser_generate_ics_rrule() ) :
			echo "RRULE:" . $reoccurrence_rule . "\r\n";
		endif;
		
		if ( !empty($schedule_data['exclude']) ) :
			$exclude_strings = array();
			foreach ( $schedule_data['exclude'] as $exclude ){
				if ( !eo_is_all_day() ){
					$vdate = '';
					$exclude->setTimezone($UTC_tz);
					$exclude_strings[] = $exclude->format('Ymd\THis\Z');
				}else{
					$vdate = ';VALUE=DATE';
					$exclude_strings[] = $exclude->format('Ymd');
				}
			}
			echo "EXDATE" . $vdate . ":" . implode(',',$exclude_strings) . "\r\n";
		endif;
		
		if ( !empty($schedule_data['include']) ) :
			$include_strings = array();
			foreach ( $schedule_data['include'] as $include ){
				if ( !eo_is_all_day() ){
					$vdate = '';
					$include->setTimezone($UTC_tz);
					$include_strings[] = $include->format('Ymd\THis\Z');
				}else{
					$vdate = ';VALUE=DATE';
					$include_strings[] = $include->format('Ymd');
				}
			}
			echo "RDATE" . $vdate . ":" . implode(',',$include_strings) . "\r\n";
		endif;
		
		echo eventorganiser_fold_ical_text( html_entity_decode( 
			"SUMMARY: " . eventorganiser_escape_ical_text( get_the_title_rss() ) ) 
		) . "\r\n";
		
		$description = wp_strip_all_tags( get_the_excerpt() );
		$description = ent2ncr( convert_chars( $description ) );
		/**
	 	* Filters the description of the event as it appears in the iCal feed.
	 	*
	 	* @param string $description The event description
	 	*/
		$description = apply_filters( 'eventorganiser_ical_description', $description );
		$description = eventorganiser_escape_ical_text( $description );
		
		if ( !empty( $description ) ) :
			echo eventorganiser_fold_ical_text( "DESCRIPTION: $description" ) . "\r\n";
		endif;
		
		$description = wpautop( get_the_content() );	
		$description = str_replace( "\r\n", "", $description ); //Remove new lines
		$description = str_replace( "\n", "", $description );
		$description = eventorganiser_escape_ical_text( $description );
		echo eventorganiser_fold_ical_text( html_entity_decode( "X-ALT-DESC;FMTTYPE=text/html: $description" ) ) . "\r\n";
		
		$cats = get_the_terms( get_the_ID(), 'event-category' );
		if ( $cats && !is_wp_error($cats) ) :
			$cat_names = wp_list_pluck($cats, 'name');
			$cat_names = array_map( 'eventorganiser_escape_ical_text', $cat_names );
			echo "CATEGORIES:" . implode(',',$cat_names) . "\r\n";
		endif;
		
		if ( eo_get_venue() ) :
			$venue = eo_get_venue_name( eo_get_venue() );
			echo "LOCATION:" . eventorganiser_fold_ical_text( eventorganiser_escape_ical_text( $venue ) ) . "\r\n";
			echo "GEO:" . implode( ';', eo_get_venue_latlng( $venue ) ) . "\r\n";
		endif;
		
		if( get_the_author_meta( 'ID' ) ){
			$author_name = eventorganiser_escape_ical_text( get_the_author() );
			$author_email = eventorganiser_escape_ical_text( get_the_author_meta( 'user_email' ) );
			echo eventorganiser_fold_ical_text( 'ORGANIZER;CN="' . $author_name . '":MAILTO:' . $author_email ) . "\r\n";
		}
		
		echo eventorganiser_fold_ical_text( 'URL;VALUE=URI:' . get_the_permalink() ) . "\r\n";
		
		echo "END:VEVENT\r\n";

	endwhile;

endif;

echo "END:VCALENDAR\r\n";