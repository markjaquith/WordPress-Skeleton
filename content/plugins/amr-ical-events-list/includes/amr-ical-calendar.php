<?php
/**
 * Display calendar with days that have posts as links.
 *
 * The calendar is cached, which will be retrieved, if it exists. If there are
 * no posts for the month, then it will not be displayed.
 *
 * @since 1.0.0
 *
 * @param bool $initial Optional, default is true. Use initial calendar names.
 * @param bool $echo Optional, default is true. Set to false for return.
 */
// ----------------------------------------------------------------------------------------
function amr_get_events_in_months_format ($events, $months, $start) {

	$bunchesofevents= array();
// prepare the months array so we show a calendar even if no events 
	$dummydate = new Datetime(); //if cloning dont need tz
	$dummydate = clone $start ;
// prepare the containers one per day 
	for ($i = 1; $i <= $months; $i++) {
		$yearmonth = $dummydate->format('Ym'); //numerical so do not need amr_date_format
		$bunchesofevents[$yearmonth] = array();
		date_modify($dummydate, '+1 month');
	}

// assign events to the box of their year and month	
	if (!empty ($events)) {
		foreach ($events as $event) {
			
			//$yearmonth = $event['EventDate']->format('Ym');   //numerical so do not need amr_date_format
			if (empty ($event['dummyYMD'])) {
				if (ICAL_EVENTS_DEBUG) {
					echo '<br />Error in dummy YMD for multi day event'; var_dump($event); 
				}				
			}
			else $yearmonth = substr($event['dummyYMD'],0,6); // quicker?
			if (isset($bunchesofevents[$yearmonth])) // then we have generated dummy events past our end date, so stop
				$bunchesofevents[$yearmonth][] = $event;
		}
	}
	return ($bunchesofevents);
}
// ----------------------------------------------------------------------------------------
function amr_prepare_day_titles ($titles, $liststyle) {
	
		// if it is a largecalendar, then show info only
		$daylinktext = __('Go to events for this day only','amr-ical-events-list');	

		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false 
		|| stripos($_SERVER['HTTP_USER_AGENT'], 'camino') !== false 
		|| stripos($_SERVER['HTTP_USER_AGENT'], 'safari') !== false)
			$ak_title_separator = "\n";
		else
			$ak_title_separator = ', ';
// if small calendar
		if ( $titles ) {
			foreach ( $titles as $day => $daywithtitles_array ) {
				if ($liststyle == 'largecalendar') 
					$daytitles[$day] = $daylinktext;
				else {
					if (is_array($daywithtitles_array) ) 
						//$string = implode(',',$daywithtitles_array);
						$string = implode($ak_title_separator,$daywithtitles_array);
					else
					$string = $daywithtitles_array;

					$daytitles[$day] = ($string );
				}
			}
		}
		else return;
		
	return ($daytitles);
}
// ----------------------------------------------------------------------------------------
function amr_get_events_in_weeks_format ($events, $weeks, $start) {  // should be using dummmyYMD?
	global $amr_globaltz;
	$wkst = ical_get_weekstart(); // get the wp start of week 
	
	if (isset($_GET['debugwks'])) { echo '<br />Separate '.count($events).' events into weeks for '.$weeks.' weeks using wkst: '.$wkst;}
		
	$weeksofevents= array();
// prepare the months array so we show a calendar even if no events 
	$dummydate = new Datetime(); //if cloning dont need tz
	$dummydate = clone $start ;
	$dummydate = amr_get_human_start_of_week($dummydate,$wkst);
	for ($i = 0; $i < $weeks; $i++) {
		$weekbeginning = $dummydate->format('Ymj'); //numerical so do not need amr_date_format
		if (empty ($firstweekbeginning) ) 
			$firstweekbeginning = $weekbeginning;
		if (isset($_GET['debugwks'])) {echo '<br />weekbeginning'.$weekbeginning; }
		$weeksofevents[$weekbeginning] = array();
		date_modify($dummydate, '+7 days');
	}

// assign events to the box of their year and month	
	if (!empty ($events)) {
		foreach ($events as $event) {
			if (!empty($event['dummyYMD']) ) {  // ahh need the dummy date, not the Event Date
				 date_date_set( $dummydate,
					substr($event['dummyYMD'],0,4),
					substr($event['dummyYMD'],4,2),
					substr($event['dummyYMD'],6,2)
					);
				if (isset($_GET['debugwks'])) {echo '<br />date:'.$event['dummyYMD'];}
				$dummydate = amr_get_human_start_of_week ($dummydate , $wkst);
				$weekbeginning = $dummydate->format('Ymj');
				if (isset($_GET['debugwks'])) {echo '<br />start of week:'.$weekbeginning;}
				if (isset($weeksofevents[$weekbeginning])) {
					$weeksofevents[$weekbeginning][] = $event;
				}
				else 	{  // the week beginning is not in our current set - might be a multi day that started the previous week or even earlier 
					if (isset($_GET['debugwks'])) {
						echo '<br />No week begin of '.$weekbeginning.' for ? '.$dummydate->format('c').' '.$event['SUMMARY'];
					//$weeksofevents[$weekbeginning][] = $event;  // assign our multi day to first week
						//var_dump($event);
					}
				}

			}
			else if (isset($_GET['debugwks'])) {echo '<br />event with no dummy date'; var_dump($event);}
		}
	}
	if (isset($_GET['debugwks'])) {
		echo '<br />Have dates for:'.count($weeksofevents).' weeks';
		foreach ($weeksofevents as $i => $bunchevents) {
			echo '<br />'.$i.' '.count($bunchevents);
			foreach($bunchevents as $i => $e) {
				echo '<br />&nbsp;&nbsp;'.$e['EventDate']->format('Ymd');   //numerical so do not need amr_date_format
			}
		}
	}

	return ($weeksofevents);
}
// ----------------------------------------------------------------------------------------
function amr_events_as_calendar($liststyle, $events, $id, $class='', $initial = true) { /* startingpoint was wp calendar */

	global $amr_options, $amr_listtype, $amr_limits, $amrW;
	global $amr_globaltz;
	global $change_view_allowed;
	global $wpdb, $wp_locale;
	global $amr_calendar_url;

	$empty = '&nbsp;';

	$link = amr_get_day_link_stem(); // get the daylink stem

		
// ---  Note that if months set, then events will have started from beg of month */

	$months = 1;
	$weeks = 2;  // as default
	if (isset ($amr_limits['months'])) {
		$months = $amr_limits['months'];  //may need later on if we are going to show multiple boxes on one page
		$weeks = 0;
		}
	else if (isset ($amr_limits['weeks'])) 	{
		$weeks = $amr_limits['weeks'];
		$months=0;
		}

	// testing 
	//$weeks = 2;		// need weeks =2 else miss early ones
	// Let's figure out when we are

	$start    		= amr_newDateTime();
	$today_day 		= $start->format('j');
	$today_month 	= $start->format('m');
	$today_year 	= $start->format('Y');

	$start    	= clone $amr_limits['start'];	
	$thismonth	= $start->format('m');
	$thisyear 	= $start->format('Y');
	
	if (!($liststyle === 'weekscalendar') )
		$start->setDate($thisyear, $thismonth, 1);
	else 	
		$start->setDate($thisyear, $thismonth, $start->format('j'));

	// what was asked for  in url (in case using small calendar as a selector )

	if (!empty($_REQUEST['start']) and is_numeric($_REQUEST['start'] )) {	
		$selected_day 	= substr($_REQUEST['start'],6,2);
		$selected_month = substr($_REQUEST['start'],4,2);
		$selected_year 	= substr($_REQUEST['start'],0,4);
	}
	else {
		$selected_day 	= $today_day;
		$selected_month = $today_month ;
		$selected_year 	= $today_year;
	}	

	$events = amr_check_for_multiday_events ($events); // now have dummy multi day events added and field dummyYMD to use 
	

	if (!($liststyle === 'weekscalendar')) 
		$bunchesofevents = amr_get_events_in_months_format ($events, $months, $start);
	else
		$bunchesofevents = amr_get_events_in_weeks_format ($events, $weeks, $start);

	if ($liststyle === 'weekscalendar') {		
		if (!empty($amr_options['listtypes'][$amr_listtype]['format']['Day']))
			$caption_format = $amr_options['listtypes'][$amr_listtype]['format']['Day'];
		else 	
			$caption_format = 'j M';	
	}
	else {
		if (!empty($amr_options['listtypes'][$amr_listtype]['format']['Month']))
				$caption_format = $amr_options['listtypes'][$amr_listtype]['format']['Month'];
		else 	$caption_format =	'F,Y';
	}


//	if ( isset($_GET['w']) ) $w = ''.intval($_GET['w']); /* what sthis for ?*/
	// week_begins = 0 stands for Sunday
	$week_begins= intval(get_option('start_of_week'));

	if (($liststyle == 'smallcalendar') )   // for compatibility with wordpress default 
		$class = ' widget_calendar ';
	if (empty($class)) 
		$class = $liststyle;
	else 
		$class = $class.' '.$liststyle.' ';
			
	if (!empty($amr_limits['show_views']) and $change_view_allowed) 
		$views = amrical_calendar_views();
	else $views = '';
	$html = $views;
	$calendar_output = '';
	$multi_output = '';

	if (empty($amr_limits['show_month_nav'])) {
		$navigation ='';
		$tfoot = '';
	}
	else {	
			$navigation = amr_calendar_navigation($start, $months, $weeks, $liststyle); // include month year dropdown	with links	

			if (($liststyle == 'smallcalendar' ) and ($months < 2))	{
				$tfoot = '<tfoot><tr><td class="calendar_navigation" colspan="7">'.$navigation.'</td></tr></tfoot>';
				}
			else {
				$tfoot = '';
				$html .= '<div class="calendar_navigation">'.$navigation.'</div>';
			}		
	}

	$columns = prepare_order_and_sequence ($amr_options['listtypes'][$amr_listtype]['compprop']);
	if (empty($columns)) return;

	// now do for each month or week-------------------------------------------------------------------------------------------------------

	if (isset($_GET['debugwks'])) echo '<br />Bunches of events = '.count($bunchesofevents).'<br />';
	foreach ($bunchesofevents as $ym => $bunchevents) {  //also for weeks
		$thismonth= substr($ym,4,2);
		$thisyear = substr($ym,0,4);
		if (!($liststyle === 'weekscalendar'))
			$start->setDate($thisyear, $thismonth, 1);
		else 	
			$start->setDate($thisyear, $thismonth, $start->format('j'));
	
		if (ICAL_EVENTS_DEBUG) echo '<br />'.$ym;
		if (isset($_GET['debugwks'])) echo '<br />weeks = '.$weeks.' '.$start->format('c');
		
		$dayheaders = 	'<tr class="dayheaders">'.amr_calendar_colheaders($liststyle, $start).'</tr>';
	
		if ($liststyle === 'weekscalendar') {  // then cannot use thead as can only have one thead per table- else is data
				$calendar_caption = apply_filters('amr_events_table_caption',amr_weeks_caption($start));
				if (!empty($calendar_caption) ) 
					$calendar_caption = '<tr class="caption"><th colspan="7">'.$calendar_caption.'</th></tr>';
				$calendar_output .= '<tbody>';
				$calendar_output .= $dayheaders;
		}
		else 	{
			$calendar_caption = apply_filters('amr_events_table_caption',amr_date_i18n ($caption_format, $start));
			if (!empty($calendar_caption) ) 
				$calendar_caption = '<caption>' .$calendar_caption.'</caption>';			
			$calendar_output .= '<table '.$id.' class="'.$class.'" >' . $calendar_caption;
			$calendar_output .= '<thead>'.$dayheaders.'</thead>'.$tfoot.'<tbody>';
		}	



		// Get days with events

		$titles = array();
		$eventsfortheday = array();
		$dayswithevents = array();
		
		
		if (ICAL_EVENTS_DEBUG) echo '<br />Bunch events count='.count($bunchevents);
		if (!empty ($bunchevents)) { // NOTE SINGULAR month
		// get the titles and events for each day		
			$bunchevents = amr_sort_by_two_cols ('dummytime','MultiDay', $bunchevents); //20140805
					
			foreach ($bunchevents as $event) {
			// convert eventdate to display timezone now for day of month assignment, other dates will be
			// converted to display timezone at display time.
				if (empty($event['EventDate'])) continue; // if no date, we cannot display anywhere 
				if (isset($event['dummyYMD']) ) {

					//$month = $event['EventDate']->format('m');
					//$month = substr($event['dummyYMD'],4,2); // quicker?
					//if (isset($_GET['debugwks'])) {echo '<br />Do we need monts=thismonth check?'.$month.' '.$thismonth;}
										
					//if ($month == $thismonth) {  
					// this allows to have agenda with more months and events cached
						//$day = $event['dummyYMD']->format('j');	
						$day = ltrim(substr($event['dummyYMD'],6,2),'0'); // quicker?							
						$dayswithevents[] = $day;	
						// replace with listtype format
						$title = '';
						if (isset ($event['SUMMARY']) ) 
							$title = $event['SUMMARY'];
						if (is_array($title)) 
							$title = implode($title);
						$titles[$day][] = $title;
						//
						$eventsfortheday[$day][] = $event;
					//	}
				}
			}
		}

		if (isset($dayswithevents)) 
			$dayswithevents = array_unique ($dayswithevents);
				
			
		if (!($liststyle === 'smallcalendar') or !function_exists('amr_events_customisable_small_calendar_daytitles') ) 
			$daytitles = amr_prepare_day_titles ($titles, $liststyle);   // for large hover?
		
		unset ($titles);

		//-----------------------------------------------------------------------------------
		
		if (!empty($eventsfortheday)) { 
			if (ICAL_EVENTS_DEBUG) echo ' we have '.count($eventsfortheday).' days of events';
			foreach ( $eventsfortheday as $day => $devents ) {
				if (ICAL_EVENTS_DEBUG) echo '<br />Day ='.$day. ' with '.count($devents).' events ';
				$dayhtml[$day] = amr_list_one_days_events($devents, $columns);
				if (function_exists('amr_events_customisable_small_calendar_daytitles') and ($liststyle === 'smallcalendar') )
					$daytitles[$day] = amr_events_customisable_small_calendar_daytitles($devents, $columns);
				//if (isset($_GET['debugwks']))  echo '<br />Day: '.$day.' '.$dayhtml[$day];
			}
		}
		unset($eventsfortheday);
//		else echo 'EMPTY events forday';

/* ------See how much we should pad in the beginning */
			$week = 1;
			$calendar_output .= "\n\t".'<tr class="week week1">';
					
//-----------------------------------------------------------------------------------		
			if ($liststyle === 'weekscalendar') {
			//if (isset ($weeks)) {
				$day1 = $start->format('j'); // set to start of week //The day of the month without leading zeros (1 to 31)
				//$daysinbunch = $day1+6;
				$daysinbunch = 7;
			}
			else {
				$pad = calendar_week_mod($start->format('w')-$week_begins);
				if ( 0 != $pad ) {
					$calendar_output .=
					"\n\t\t".'<td colspan="'. esc_attr($pad) .'" class="pad">'
					//.'&nbsp;'
					.$empty
					.'</td>';			
				}
				$day1 = 1;
				$daysinbunch = $start->format('t');	//The number of days in the given month
			}
			if (isset($_GET['debugwks'])) echo '<br />Day 1= '.$day1;
		
			$newrow = false;
			$nextdate = new Datetime(); //if cloning dont need tz
			$nextdate = clone($start);
		
//			for ( $day = $day1; $day <= $daysinbunch; ++$day ) {	
			for ( $i = 1; $i <= $daysinbunch; $i+=1 ) {	
				if (isset($_GET['debugwks'])) echo '<br />i = '.$i;		
				
//				$calendar_output .= amr_handle_each_day ($thisyear, $thismonth, $day, $daytitles, $dayswithevents,$dayhtml);
				if ( isset($newrow) && $newrow ) {
					if ($week > 1) { // then we need to end the previous row
						$calendar_output .= AMR_NL.'</tr>';						
						$calendar_output .= AMR_NL.'<tr class="week week'.$week.'">'.AMR_NL;	
					}
					//else echo 'new row but $week = '.$week;					
				}	
				
				$newrow = false;
				$lastinrow = '';
				// check if after this we need a new row eg if month calendar//
//				if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) ) {
				if ( 6 == calendar_week_mod($nextdate->format('w')-$week_begins) ) {
					$newrow = true;
					$lastinrow = ' endweek';
					$week = $week+1; // helps to balance out the multi month view	
				}
							
				/* wp code - can't we do better ? */
				//$datestring = $day.'-'.$thismonth.'-'.$thisyear; // must use hyphens for uk english dates, else it goes US
				//$dow = date('N',strtotime($datestring)); // does not like dates earlier than 1902
				$dow = $nextdate->format('N');
				$thisyear = $nextdate->format('Y');
				$thismonth = $nextdate->format('m');
				$day = $nextdate->format('j');
				
				$hasevents = '';
				if ((!empty ($amr_limits['day_links'])) and ($amr_limits['day_links']) and
					 (!empty($daytitles[$day]) )) { // then we have events for that day, so can link to it
						$hasevents = ' hasevents ';
						$daylink = '<a class="daylink" href="'.
							htmlentities(amr_get_day_link($thisyear, $thismonth, $day, $link))
							. '" title="' . ($daytitles[$day]) . '">'.$day.'</a>';
					}
				else {
						$daylink = $day;
					}			
					
				if ( ($day == $today_day) && 
					($thismonth == $today_month) && 
					($thisyear == $today_year) )
					$today = ' today ';
				else $today = '';
				
				if ( ($day == $selected_day) && 
					($thismonth == $selected_month) && 
					($thisyear == $selected_year) )
					$selected = ' selected ';
				else $selected = '';
				
				$calendar_output .= '<td class="day'.$dow.$today.$selected.$hasevents.$lastinrow.'">';							
				if (!($liststyle === 'weekscalendar') )
					$calendar_output .= '<div class="day">'.$daylink.'</div>';
				if ((!empty($dayswithevents) ) and ( in_array($day, $dayswithevents) )) {// any posts today?				
					if (isset($_GET['debugwks'])) {echo '<br />Day='.$day;}
	//				if (($liststyle == 'largecalendar') 
					if (in_array ($liststyle, array('largecalendar','weekscalendar')) 
					and (!empty($dayhtml[$day])))
						$calendar_output .= AMR_NL.$dayhtml[$day];
				}
				else {
					$calendar_output .= $empty; //'&nbsp;';
				}
				$calendar_output .= '</td>';
				date_modify($nextdate, '+1 day');
			}	
				
			// now check if we need to pad to the end of the week
//			$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
			$pad = 6 - calendar_week_mod($dow - $week_begins) ;  
			if ( $pad != 0 && $pad != 7 ) {
				$calendar_output .= "\n\t\t".'<td class="pad" colspan="'. esc_attr($pad) .'">&nbsp;'
				//.$dow.' '.$week_begins
				.'</td>';
				}
			else 
				$week=$week-1;

			if (($months > 1) and ($liststyle == 'smallcalendar')) { // pad so that they will all line up nicely in the multiview
				for ($w=$week; $week <=5; ++$week) {
				$calendar_output .=
					"\n\t".'</tr><tr><td class="pad" colspan="7" >&nbsp;</td>'
					."\n\t";
				}
			}	
				
			
			if ($liststyle === 'weekscalendar') 
				$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t";
			else 
				$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";
			$multi_output .= $calendar_output;
			$calendar_output = '';
			if (isset ($weeks)) date_modify($start, '+7 days');
			else				date_modify($start, '+1 month');
			
	} // for each bunch (eg month?)
	
	if ($liststyle === 'weekscalendar') // if we are doing weekly, we want only one table so all will line up
		$multi_output = '<table '.$id.' class="'.$class.'" >'.$multi_output.'</table><!-- end weekly table -->';
	
	$html .= $multi_output;
	return($html);
}
// ----------------------------------------------------------------------------------------
function amr_list_one_days_events($events, $columns) { /* for the large calendar */
	global $amr_options,
		$amr_limits,
		$amr_listtype,
		$amrW,
		$amrtotalevents;

		if (empty($events)) return;
		$html = '';

		$no_cols = 1;
/* --- setup the html tags ------need to have divs if want to allow html else must strip---------------------------------------- */
		$ev = AMR_NL.'<div class="event'; 
		$evc = '</div> ';

/* -- body code ------------------------------------------*/
		$groupedhtml = '';
		
		// need to resort in case we have messed the timing up, keep the multi days to the top, hopefully in the order that they were in before.
		// done earlier // $events = amr_sort_by_two_cols ('dummytime','MultiDay', $events); //20140805
		
		foreach ($events as $i => $e) { /* for each event, loop through the properties and see if we should display */
	
			amr_derive_component_further ($e); 
			if (!empty($e['Classes']))
				$classes = strtolower($e['Classes']);
			else $classes = '';
			$eventhtml = ''; /*  each event on a new list */
			$colhtml = array();
			

			foreach ($columns as $col => $order) {
				$colhtml[$col] = '';
				foreach ($order as $field => $fieldconfig) { /* ie for one event, check how to order the bits */
									/* Now check if we should print the component or not, we may have an array of empty string - check our event has that value */
					if (isset($e[$field])) 
						$v = amr_check_flatten_array ($e[$field]);
					else 
						$v =null;
					if (!empty($v))	{
//						$col = $fieldconfig['Column'];
						$colhtml[$col] .= 
							amr_format_value($v, $field, $e, 
							$fieldconfig['Before'],
							$fieldconfig['After'] );  /* amr any special formating here */
					}
				}
			}
			
			foreach ($colhtml as $col => $chtml) {
				$eventhtml .= (empty($chtml)) ?  AMR_NL : AMR_NL.'<div class="details'.$col.'">'.$chtml.'</div>';
			}
			if (!($eventhtml === '')) { /* ------------------------------- if we have some event data to list  */
				$eventhtml = $ev.$classes.'">'.$eventhtml.$evc;
				}
			$html .= AMR_NL.$eventhtml;
		}
		//if (!empty($html)) $html = $html.$ulc;
return ($html);
}
// ----------------------------------------------------------------------------------------
function amr_get_day_link_stem() {
	global $amr_calendar_url, $amr_limits;
	
	if (!empty($amr_calendar_url))  
	// if they have defined a url to use for these sorts of links, then use it
		$link = $amr_calendar_url;
	else 
	// else get a clean version of the current url
		$link = amr_clean_link();	
			
	// how do we know whether to force a listtype or not ?
	// we must if in large calendar or small calendar
	// and it should be the agenda one
	// If they have specified a url, then that page should either already be
	// in a suitable listtype, or the url should have a listtype passed to it.
	
	if (!empty ($amr_limits['agenda'])) {  
	// do not want listtype unless requested?
		$agenda = $amr_limits['agenda'];	
	}	
	else 	
		$agenda = 1;
	
	$link = add_query_arg( 'listtype', $agenda ,$link); 
	
	return ($link);
}
// ----------------------------------------------------------------------------------------
function amr_get_day_link($thisyear, $thismonth, $thisday, &$link) { /* the start date object  and the months to show */

	// old comment as to why the linktype add in was commented out : what does it mean?:
	//no ? - they must just do something sensible when they set up the  page
	
	$link = add_query_arg( 'days', '1' ,$link);
	$link = add_query_arg( 'months', '0' ,$link);  // else existing months will override days 
	$link = add_query_arg( 'start', 
		$thisyear.str_pad($thismonth,2,'0',STR_PAD_LEFT).str_pad($thisday,2,'0',STR_PAD_LEFT), 
		$link );
	$link = amr_check_for_wpml_lang_parameter ($link);
return ($link);

}
// ----------------------------------------------------------------------------------------
function amrical_get_month_link($start, $months, $link) { /* the start date object  and the months to show */
	$link = (add_query_arg( 'start', $start, $link ));
	$link = (add_query_arg( 'months', $months, $link ));
	$link = amr_check_for_wpml_lang_parameter ($link);
return ($link);

}
// ----------------------------------------------------------------------------------------
function amr_calendar_colheaders ($liststyle, $start) {
global $wp_locale,
	$amr_options,
	$amr_globaltz,
	$amr_listtype;
	// week_begins = 0 stands for Sunday
	$week_begins= intval(get_option('start_of_week'));
	$format = 	$amr_options['listtypes'][$amr_listtype]['format']['Day'];
	$dummydate = new Datetime(); //if cloning dont need tz
	$dummydate = clone $start ;// so as not to overwrite start 
	
	$myweek = array();
	$calendar_output = '';
	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$dayofweek = ($wdcount+$week_begins)%7;

		$myweek[] = $wp_locale->get_weekday($dayofweek);
		// make a note of which number is saturday and sunday so we can add css classes for the weekend
		if ($dayofweek == 0) $sunday = $wdcount;
		if ($dayofweek == 6) $satday = $wdcount;
	}
	
	foreach ( $myweek as $dayofweek => $wd ) {

		switch ($liststyle) {
			case "smallcalendar" : {
				$day_name = $wp_locale->get_weekday_initial($wd);
				break;
			}
			case "weekscalendar" : {//else weekscalendar
				$day_name = amr_format_date($format,$dummydate); // v 4.0.9
				
				break;
			}
			default: { //else large calendar
				$day_name = $wp_locale->get_weekday_abbrev($wd);
			}
		
		}
		date_modify($dummydate, '+1 day'); //must increment the day 
		
//		$day_name = ($liststyle=="smallcalendar") ? 
//			$wp_locale->get_weekday_initial($wd) : 
//			$wp_locale->get_weekday_abbrev($wd);
		$wd = esc_attr($wd);
		if ($dayofweek === $sunday) 
			$class= ' class="sunday" ';
		elseif ($dayofweek === $satday) 
			$class= ' class="saturday" ';
		else $class='';

		$calendar_output .= "\n\t\t<th ".$class." scope=\"col\" title=\"$wd\">$day_name</th>";
	}

		
			
	return 	$calendar_output;	
}
// ----------------------------------------------------------------------------------------
function amr_event_is_multiday($event) { //determine if event is a multi day event

	if (empty($event['DURATION'])) {
		if (empty($event['EndDate'])) return false; // no duration at all, just a date 
		else $duration = amr_calc_duration ( $event['EventDate'], $event['EndDate']);
	}
	else $duration = $event['DURATION'];
	if (isset($_GET['debugmulti'])) {echo '<br /> duration = '; var_dump($duration);}
	$days = 0;
	if (!empty($duration['days']) and ($duration['days'] >= 1 )) {
		$days=$duration['days']; 
		}
	if ( (!empty($duration['hours']) and ($duration['hours'] >= 1 )) or
		(!empty($duration['minutes']) and ($duration['minutes'] >= 1 ))  or
		(!empty($duration['seconds']) and ($duration['seconds'] >= 1 ))  ) {
		// then we go over 1 day into the next
			$days = $days + 1;
		}
	
	if (!empty($duration['weeks']) and ($duration['weeks'] >= 1 )) 
		$days = $days + (7*$duration['weeks']); 
	if (isset($_GET['debugmulti'])) echo '<br /> The number of days over which to show event '.$event['SUMMARY'].' is = '.$days;
	return $days;
}
// ----------------------------------------------------------------------------------------
function amr_sort_by_two_cols ($col1, $col2, &$data) {  // sorts by two columns ascending
	// Obtain a list of columns
	foreach ($data as $key => $row) {
		// if col1 is an object ? 
	    if (!empty($row[$col1])) 
			$column1[$key]  = $row[$col1];
		else 
			$column1[$key]  = '-1';//will never happen
		if (!empty($row[$col2])) 
			$column2[$key]  = $row[$col2];
		else 
			$column2[$key]  = '-999';

	}
	
	if (empty ($column1) or empty ($column2)) return $data;  
	
	array_multisort($column1, SORT_ASC, $column2, SORT_DESC,   $data);

	return $data;
}
// ----------------------------------------------------------------------------------------
function amr_sort_by_three_cols ($col1, $col2, $col3, &$data) {  // sorts by two columns ascending
global $amr_globaltz;

	// Obtain a list of columns
	foreach ($data as $key => $row) {
		// if col1 is an object ? 
	    if (!empty($row[$col1])) 
			$column1[$key]  = $row[$col1];
		else 
			$column1[$key]  = '-1';//will never happen
		if (!empty($row[$col2])) 
			$column2[$key]  = $row[$col2];
		else 
			$column2[$key]  = '-999';
		if (!empty($row[$col3])) 
			$column3[$key]  = $row[$col3];
		else 
			$column3[$key]  = '-999';	

	}
	array_multisort($column1, SORT_ASC, $column2, SORT_DESC, $column2, SORT_ASC,   $data);

	return $data;
}
// ----------------------------------------------------------------------------------------
function amr_check_for_multiday_events (&$events) {	//for each event passed, chcek whiether it is a multi day, and if so add dummy days	
global $amr_globaltz;
	
	if (!empty ($events)) {
		foreach ($events as $m => $event) {	
			if (empty($event['EventDate'])) {
				if (ICAL_EVENTS_DEBUG) {
					echo 'Unexpected empty event date for event '; 
					print_r($event, true);
				}
			}
			else {
				date_timezone_set($event['EventDate'], $amr_globaltz);  // may do this earlier (after recurrence though), no harm in twice
				$days = amr_event_is_multiday($event);
				if (isset($_GET['debugmulti'])) echo '<br />Doing '.$event['id'].' <b>multiday = '.$days.' </b>';
				if ($days > 1 ) {
					$day = 1;				
					if (empty ($events[$m]['Classes'])) $events[$m]['Classes'] = '';
					$events[$m]['Classes'] .= ' firstday ';	// already have first day
					$events[$m]['dummyYMD'] = $event['EventDate']->format('Ymd'); //numerical so do not need amr_date_format
					$events[$m]['MultiDay'] = $day; 
					//$tempdate = amr_newDateTime();  // create new obect so we do not update the same object
					while ($day < ($days)) {   // must not be <= because we are plus one anyway - and original is first anyway
						if (isset($_GET['debugmulti'])) echo '<br /> Do day = '.$day;
						$tempdate = new Datetime(); //if cloning dont need tz
						$tempdate = clone $event['EventDate']; // copy the current event date		
						date_modify ($tempdate,'+'.$day.' days');  // adjust days to currenmt midddle date if necessary
						// must do like above in case we go over a month 
						$day = $day+1;
						$dummy[$m][$day] = $events[$m]; // copy event data over , but use dumy so we do not reprocess the additions
						$dummy[$m][$day]['dummyYMD'] = $tempdate->format('Ymd');;  // now set the date for this dummy event //numerical so do not need amr_date_format
						$dummy[$m][$day]['MultiDay'] = $day;  // flag it as a multi day
						// set the classes so we can style multi days
						if (isset($_GET['debugmulti'])) echo ' dummyymd= '.$dummy[$m][$day]['dummyYMD'];	
						if ($day >= $days) {
							$dummy[$m][$day]['Classes'] .= ' lastday ';
							$dummy[$m][$day]['Classes'] = str_replace ('firstday', '', $dummy[$m][$day]['Classes']);
						}
						else {
							$dummy[$m][$day]['Classes'] = str_replace ('firstday', '', $dummy[$m][$day]['Classes']);
							$dummy[$m][$day]['Classes'] .= ' middleday ';
						}
					}
					
				}
				else {
					$events[$m]['dummyYMD'] = $event['EventDate']->format('Ymd');  //numerical so do not need amr_date_format
					$events[$m]['MultiDay'] = '0'; // to force non multidays to bottom
				}
				$events[$m]['dummytime'] = $event['EventDate']->format('His'); //for sorting according to time
			}	
		}
		//once we have processed all the events, THEN we can add the dummies in, so we do not reprocess them!
		if (!empty($dummy )) {
			foreach ($dummy as $m => $dummydays) { 
				foreach ($dummydays as $k => $event) {
					$events[] = $event;				
				}
			}
		}
		//$events = amr_sort_by_three_cols ('dummyYMD', 'MultiDay', 'dummytime', $events);
		$events = amr_sort_by_two_cols ('dummyYMD', 'MultiDay', $events);
		if (isset($_GET['debugmulti'])) {
			foreach ($events as $i => $e) {
				echo '<br />'.$e['id'].' '.$e['EventDate']->format('Ymd').' '.$e['dummyYMD'];
			}
		}
	}
	return ($events);
}	