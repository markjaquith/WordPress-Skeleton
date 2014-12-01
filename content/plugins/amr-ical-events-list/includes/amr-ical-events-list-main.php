<?php

global $amr_options;
global $amrW;  /* set to W if running as widget, so that css id's will be different */
$amrW = '';

if (version_compare(AMR_PHPVERSION_REQUIRED, PHP_VERSION, '>')) {
	echo( '<h2>'.__('Minimum Php version '.AMR_PHPVERSION_REQUIRED.' required for Amr Ical Events.  Your version is '.PHP_VERSION,'amr-ical-events-list').	'</h2>');
	}
if (!(class_exists('DateTime'))) {
	echo '<h1>'.
	__ ('The <a href="http://au.php.net/manual/en/class.datetime.php"> DateTime Class </a> must be enabled on your system for this plugin to work. They may need to be enabled at compile time.  The class should exist by default in PHP version 5.2.','amr-ical-events-list')
	.'</h1>';}
/* see http://acko.net/blog/php-clone */
  if (version_compare(phpversion(), '5.0', '<')) {

    eval('function clone($object) {

      return $object;

    }

    ');

  }
  
amr_set_defaults_for_datetime();  // needed all over the place
/*----------------------------------------------------------------------------------------*/
//add_action ('after_setup_theme','amr_load_pluggables');
//add_action ('wp','amr_load_pluggables', 10);	//move it later, No not good , plugins that apply the filters will nothave it then, so will fail
add_action ('plugins_loaded','amr_load_pluggables', 99);	//move it later, No not good , plugins that apply the filters will nothave it then

function amr_load_pluggables() {
	require_once('amr-pluggable.php');
}
/*----------------------------------------------------------------------------------------*/
function amr_ical_events_list_record_version() {
	global $amr_options;
	if (empty($amr_options['amr-ical-events-list-version'])) 
		$amr_options['amr-ical-events-list-version'] = 1.0;
	if ((version_compare(AMR_ICAL_LIST_VERSION , $amr_options['amr-ical-events-list-version'], '>'))) {
		amr_ical_apply_version_upgrades ($amr_options['amr-ical-events-list-version']);
		$amr_options['amr-ical-events-list-version'] = AMR_ICAL_LIST_VERSION;
		$result = update_option( 'amr-ical-events-list', $amr_options);
		return($result);
	}
}
/* --------------------------------------------------  */
function ical_ordinalize( $num ){

	if (in_array($num,array(11, 12, 13)))
		return sprintf(_x('%s th',' the 11, 12 or 13th ' ,'amr-ical-events-list'), $num);

	switch( substr($num,-1) ){

		case 1 	: 	if ($num<10)	return $num; /* every 1 not every first */

				   	else 			return
		sprintf(_x('%s st',' the twenty first etc ' ,'amr-ical-events-list'), $num);

		case 2 	: 	return
		sprintf(_x('%s nd',' the second ' ,'amr-ical-events-list'), $num);

		case 3 	: 	return
		sprintf(_x('%s rd',' the third ' ,'amr-ical-events-list'), $num);

		default : 	return
		sprintf(_x('%s th',' the nth ' ,'amr-ical-events-list'), $num);

	}

}
/*----------------------------------------------------------------------------------------*/
function ical_ordinalize_words( $i ){

	if 		($i == 1) 	return( __('the first','amr-ical-events-list'));

	else if ($i == 0) 	return (__('every','amr-ical-events-list'));

	else if ($i == -1) 	return ( __('the last','amr-ical-events-list'));

	else if ($i < 0) 	return ( sprintf (__('the %s last', 'amr-ical-events-list'), ical_ordinalize( -$i )));

	else 				return ( sprintf (__('the %s', 'amr-ical-events-list'), ical_ordinalize( $i )));

}
/*----------------------------------------------------------------------------------------*/
function amr_fulldaytext () {
global  $wp_locale;
	$fulldayofweek = array (

		'MO'=> $wp_locale->get_weekday('1'),

		'TU'=>$wp_locale->get_weekday('2'),

		'WE'=>$wp_locale->get_weekday('3'),

		'TH'=>$wp_locale->get_weekday('4'),

		'FR'=>$wp_locale->get_weekday('5'),

		'SA'=>$wp_locale->get_weekday('6'),

		'SU'=>$wp_locale->get_weekday('0')

		);
		foreach ($fulldayofweek as $i => $d) { /* cater for lack of translations - somehow came back blank for non english */

			if (empty($d ))  $fulldayofweek[$i] = ucfirst(strtolower($i));

		}
	return ($fulldayofweek);
}
/*----------------------------------------------------------------------------------------*/
function amr_weekdayabbr () {
global  $wp_locale;

	$daysofweek = array ( /* use the wordpress localisation functions so we do not have to retranslate ourselevs */

		'MO'=> $wp_locale->get_weekday_abbrev(__('Monday')),

		'TU'=> $wp_locale->get_weekday_abbrev(__('Tuesday')),

		'WE'=> $wp_locale->get_weekday_abbrev(__('Wednesday')),

		'TH'=> $wp_locale->get_weekday_abbrev(__('Thursday')),

		'FR'=> $wp_locale->get_weekday_abbrev(__('Friday')),

		'SA'=> $wp_locale->get_weekday_abbrev(__('Saturday')),

		'SU'=> $wp_locale->get_weekday_abbrev(__('Sunday')),

	);

	foreach ($daysofweek as $i => $d) {

		if (empty($d ))  $daysofweek[$i] = $i;

	}
	return ($daysofweek);
}
/* --------------------------------------------------  */
function amr_dayofyear2date( $year, $DayInYear ) { /* Year if format YYYY, Day in year 1 to 366 */

	$d = amr_newDateTime($year.'-01-01');
	date_modify($d, '+'.($DayInYear-1).' days');

return ($d);

}
/* --------------------------------------------------  */
function amr_get_week_no_with_wkst ($date, $wkst) { /* only copes with WKST MO, SU, SA. returns 0 if in l ast year  */

	$yearday 	= $date->format('z')+1;
	$jan1c		= amr_newDateTime($date->format('Y').'-01-01');
	/* GET week 1 day 1 for our week start ============================================== */
	$dw = $jan1c->format('w') + 1; /* 0 (for Sunday) through 6 (for Saturday)  change to => 1 to 7 */
	if ($wkst === 'MO')  		$dw = $dw-1;
	else if ($wkst === 'SA') 	$dw = (($dw + 1) % 7); /* remainder */
	if ($dw == 0) 	$dw=7;
	if ($dw <=4) 	$adj = -$dw + 1;
	else 			$adj = -$dw + 8;
	$w1d1 = new Datetime(); //if cloning dont need tz
	$w1d1 = clone $jan1c;
	if (!($adj == 0)) date_modify($w1d1,$adj.' days' );
	/* So now we have w1d1 ?============================================== */
	$w1d1yearday = $w1d1->format('z')+1;
	if ($yearday >= 4 ) {
		if ($w1d1 < $jan1c) {/* the start is in the prev year */
			$w2d1 = $w1d1; date_modify ($w2d1, '+7 days'); /* to bring it to this year */
			$w2d1yearday = $w2d1->format('z')+1;
			$weekno = floor((($yearday - $w2d1yearday)/7)+2);
		}
		else
		$weekno = floor((($yearday - $w1d1yearday)/7)+1);
	}
	else {
		if ($yearday < $w1d1yearday) $weekno = 0 ;/* ie last year */
		else $weekno = 1;
	}
	return($weekno);
}
/* --------------------------------------------------  */
function amr_output_icalduration ($duarray) {
	$d = '';
	$t = '';
	if (!empty($duarray['weeks'])) 	$d  = (int)$duarray['weeks'].'W';
	if (!empty($duarray['days'])) 	$d .=     (int)$duarray['days'].'D';
	if (!empty($duarray['hours'])) 	$t .= (int)$duarray['hours'].'H';
	if (!empty($duarray['minutes'])) $t .=    (int)$duarray['minutes'].'M';
	if (!empty($duarray['seconds'])) $t .=    (int)$duarray['seconds'].'S';
	if (!empty($t)) $t = 'T'.$t;
	return ('P'.$d.$t);
}
/* ----------------------------------------------------------------------------------- */
function amr_parseRepeats (&$event) {

global $amr_globaltz;

	// if (isset($_GET['rdebug'])) {echo '<hr>in parse repeats'; var_dump($event['RRULE'] );}

	if (!empty($event['RRULE'])) /* and is_string($event['RRULE'])) */
		$event['RRULE'] 	= amr_parseRRULE($event['RRULE']);

	if (!empty($event['EXRULE'])) /* and is_string($event['EXRULE']))	*/
		$event['EXRULE'] 	= amr_parseRRULE($event['EXRULE']);
	if (!empty($event['RDATE'])) {/*and is_string($event['RDATE'])) */
		$event['RDATE'] 	= amr_parseRDATE($event['RDATE'],$amr_globaltz );
		$event['RDATE'][] = $event['DTSTART'];  // 201406 must include DTSTART in RDATE
		
	}
	if (!empty($event['EXDATE'])) /*and is_string($event['EXDATE']))*/
		$event['EXDATE'] 	= amr_parseRDATE($event['EXDATE'],$amr_globaltz );

    // if (isset($_GET['rdebug'])) {echo '<hr>in parse repeats'; var_dump($event['RRULE'] );}
	return ($event);

}
/* -------------------------------------------------------------------------*/
function amr_get_googletime($time)   {
	if (!(is_object($time))) return($time);
	$t = clone ($time);  /* if you get a parse error, then you are not on PHP 5! */
    $t->setTimezone(new DateTimeZone("UTC")); // google wants UTC
	$text = $t->format("Ymd\THis\Z");

    return ($text);

   }
/*--------------------------------------------------------------------------------*/
function amr_get_googledate($time)   {
	if (!(is_object($time))) return($time);
	$t = clone ($time);
    $t->setTimezone(new DateTimeZone("UTC")); // google wants UTC
    return ($t->format("Ymd"));

   }
/*--------------------------------------------------------------------------------*/
function amr_get_googleeventdate($e) { 
/* google has updated functioning to use ics end date for all day events (note instructions at
http://support.google.com/calendar/bin/answer.py?hl=en&answer=2476685 are wrong - they show same date for end date for 1 day event.
Testing shows one can also leave enddate blank for single day events. No more - now need an enddate
NOTE: must all be in UTC!!
 */

	if (!isset ($e['EventDate'] )) 
		return (''); /* we have no start date or start time */
	else { 
		// we have a start date
		if (isset ($e['allday']) and (!($e['allday'] === 'allday') ))  {  
			// not an all day
			$start = amr_get_googletime ($e['EventDate']);
			if (isset ($e['EndDate'])) {
				$end = '/'.amr_get_googletime ($e['EndDate']);
				
			}
			else $end = '/'.$start; // no end date
		
			
		
		}
		else { // it is an all day
			
			$start = amr_get_googledate ($e['EventDate']);
			if (isset ($e['EndDate'])) {
				if ($e['EndDate'] == $e['EventDate']) 
					$end = '/'.$start;
				else {	
					$ics_enddate = clone($e['EndDate']);
					date_modify($ics_enddate,'+1 day');
					$end = '/'.amr_get_googledate ($ics_enddate);
				}
			}
			else $end = '/'.$start; // no end date
		}
	}
	
	return ($start.$end);


   }
/* ---------------------------------------------------------------------- */
function amr_get_css_url_choices () {
	$dir = amr_get_css_path();
	$css_url = ICAL_EVENTS_CSS_URL;

	$files = amr_get_files($dir, '.css');

	if (!empty($files)) foreach ($files as $i => $f) $files[$i] = ICAL_EVENTS_CSS_URL.$f;
	$files[] = 	ICALLISTPLUGINURL.'css/icallist.css';
	return($files);
}
/* ---------------------------------------------------------------------- */
function amr_get_css_path() { /** Attempt to create  a css directory if it doesn't exist.
	 * Return the path if successful.*/
		$css_path = ICAL_EVENTS_CSS_DIR;
		$css_file = ($css_path.'custom_icallist.css');
		if (!file_exists($css_path)) { /* if there is no folder  or safe mode enabled */
			if (ini_get('safe_mode')) {  // safe mode must be enabled , give up
				return false;
			}
			if (wp_mkdir_p($css_path, 0777)) {
				// printf('<br />'.__('A css directory %s has been created','amr-ical-events-list'),'<code>'.$css_path.'</code>');
			}
			else {
				echo( '<br />'.sprintf(__('Error creating custom css directory %s. Please check permissions','amr-ical-events-list'),$css_path));
				return false;
			}
		}
		if (!file_exists($css_file)) { /* if there is no starting css file, then copy the plugin version over  */
			if (ini_get('safe_mode')) {  // safe mode must be enabled , give up
				return false;
			}
			else amr_copy_file(ICALLISTPLUGINDIR.'css/icallist.css',$css_file );
		}
		return $css_path;
	}
/* ---------------------------------------------------------------------- */
function amr_copy_file( $from, $to) {
   if ( !copy($from ,$to ) ) {

			  // If copy failed, chmod file to 0644 and try again.

			 chmod($to, 0644);

			 if ( !copy($from , $to) )

				 return (new WP_Error('copy_failed', __('Could not copy file.', 'amr-ical-events-list'), $to ));

		 }

	chmod($to, 0644);
}
/*--------------------------------------------------------------------------------*/
function amr_ical_events_style()  /* check if there is a style spec, and file exists */{
global $amr_options;
global $amr_listtype;
	$icalstyledir = amr_get_css_path(); // create the custom css path if it exists ICALSTYLEURL;
	$icalstyleurl = ICALSTYLEURL;

	if ($amr_options = get_option ('amr-ical-events-list')) {  //if we have stored options

		if ((isset ($amr_options['own_css'])) and !($amr_options['own_css'])) {
			if (empty($amr_options['cssfile'])) 
				$icalstyleurl = ICALSTYLEURL;
			else {/* check if old saved option */
				if (stristr($amr_options['cssfile'],'http://')
					or stristr($amr_options['cssfile'],'https://')) //what if https
					$icalstyleurl = $amr_options['cssfile'];
				else 
					$icalstyleurl = ICALLISTPLUGINURL.'css/'.$amr_options['cssfile'];
			}
			wp_register_style('amr-ical-events-list', $icalstyleurl, array( ), 1.0 , 'all' );
			wp_enqueue_style('amr-ical-events-list' );
			wp_register_style('amr-ical-events-list_print', ICALSTYLEPRINTURL, array(), 1.0 , 'print');
		    wp_enqueue_style('amr-ical-events-list_print');
		}
		// else do NOT load css
	}
	else {
		wp_register_style('amr-ical-events-list', $icalstyleurl, array( ), 1.0 , 'all' );
		wp_enqueue_style('amr-ical-events-list' );
		wp_register_style('amr-ical-events-list_print', ICALSTYLEPRINTURL, array(), 1.0 , 'print');
		wp_enqueue_style('amr-ical-events-list_print');
	}

}
// ----------------------------------------------------------------------------------------
function amr_sort_by_two_cols_asc ($col1, $col2, &$data) {  // sorts by two columns ascending
	// Obtain a list of columns

	foreach ($data as $key => $row) {
		// if col1 is an object ?

	    if (!empty($row[$col1]))
			$column1[$key]  = $row[$col1];
		else
			$column1[$key]  = '99999';//will never happen
		if (!empty($row[$col2]))
			$column2[$key]  = $row[$col2];
		else
			$column2[$key]  = '99999';


	}

	array_multisort($column1, SORT_ASC, $column2, SORT_ASC,   $data);

	return $data;
}
/* --------------------------------------------------  sort through the options that define what to display in what column, in what sequence, delete the non display and sort column and sequenc  */
function prepare_order_and_sequence ($orderspec){
//	foreach ($amr_options[$format]['compprop'] as $key => $row)
	foreach ($orderspec as $key => $row) {
		if (( isset ($row['Column'])) && (!($row['Column']== "0") ))
		$order[$key] = $row;
	}
	if (!isset($order)) return;  /* Nothing is to be displayed */
	// Prepare to sort order for printing

	foreach ($order as $key => $row) {
		if ( isset ($row['Column'])) {
			$col[$key]  = $row['Column'];
			$seq[$key] = $row['Order'];
		}
	}

	array_multisort($col, SORT_ASC, $seq, SORT_ASC, $order);

	foreach ($order as $k => $v)  {/* for each key,  prepare once*/
		$order[$k]['Before'] = stripslashes ($v['Before']);
		$order[$k]['After'] = stripslashes ($v['After']);
	}
// now re structure into columns rather than what we had before
	$columns = array();
	foreach ($order as $k => $spec) {
		$columns[$spec['Column']][$k] = $spec;
	}
	return ($columns);
//	return ($order);
}
/* --------------------------------------------------  */
function ical_get_weekstart() {

	$wkst = get_option('start_of_week');  /* Somewhat annoyingly, wp has sunday as 0 and sat as 6 !! */

	if (!$wkst) $wkst = 0; /* only becuase this is wp default */

	$ical_day_of_week = array ( /* translate from wp day of week to  ical day of week  */

		'1' => 'MO',

		'2' => 'TU',

		'3' => 'WE',

		'4' => 'TH',

		'5' => 'FR',

		'6' => 'SA',

		'0' => 'SU');

	$wkst = $ical_day_of_week[$wkst];

	return ($wkst);

}
/* -----------------------------------------------------------------------------------------------------*/
function get_oldweekdays ($d) { /* Looks like it works compared to http://www.searchforancestors.com/utility/dayofweek.html and if not, an aproximation is better than nothing !*/

		$dummy = new Datetime(); //if cloning dont need tz
		$dummy = clone ($d);
		date_modify($dummy,'+91500 weeks'); /* a guess from when the date started breaking, plus some extra*/
		$w = $dummy->format('w');
		return($w);
}
/* --------------------------------------------------  */
function amr_same_time ($d1, $d2) {
	if ($d1->format('YmdHis') === $d2->format('YmdHis')) return (true);  //v 4.0.9  maybe should check whole day ?
	else return (false);
}

/* --------------------------------------------------  */
function amr_amp ($content) { //trying to avoid double ?
	return (str_replace('&','&amp;',str_replace('&amp;','&',$content) ));
}
/* --------------------------------------------------  */
function amr_daysDifference( $beginDate, $endDate){ /* *** what if dates are in different timezones, and somehow cross over the days - will that ever happen??  */

   //explode the date by "-" and storing to array
   if (!(is_object($beginDate) and (is_object($endDate)))) return(false);
   $date_parts1=explode("-", $beginDate->format('n-j-Y'));

   $date_parts2=explode("-", $endDate->format('n-j-Y'));

   //gregoriantojd() Converts a Gregorian date to Julian Day Count  - month/day/year

   $start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);

   $end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);

   return ($end_date - $start_date);

}
/* --------------------------------------------------  */
function amr_secondsDifference( $beginDate, $endDate){ /* *** what if dates are in different timezones, and somehow cross over the days - will that ever happen??  */

   //explode the date by "-" and storing to array
   if (!(is_object($beginDate) and (is_object($endDate)))) return(false);
   $sec1 = (int) $beginDate->format('U');

   $sec2 = (int) $endDate->format('U');

   return ($sec2-$sec1);

}
/* --------------------------------------------------  */
function amr_calc_duration ( $start, $end) { /* assume in same timezone , ics dtend? */
	/* In php 5.3 there is a date diff calculation */
	/* calculate weeks, days etc and return in array */
	/* don't want to use unix date stamp */
	if (!(is_object($start) and (is_object($end)))) return(false);

//	if (function_exists('date_diff')) { /* for php 5.3 only - untested locally  - need to check for 0 and unset */
//		$interval = date_diff($start, $end);
//		$d = (int) $interval->format('%d');
//		$d = $duarray['days'] = (int) $interval->format('%d');
//		$duarray['hours'] = (int) $interval->format('%h');
//		$duarray['minutes'] = (int) $interval->format('%m');
//		$duarray['seconds'] = (int) $interval->format('%s');
//		$duarray['weeks'] = $d / 7;  if ($duarray['weeks'] < 1) $duarray['weeks'] = 0;
//		$duarray['days'] = $d % 7;
//		return ($duarray);
//	}
	/* else ....  do our own non unix calc */
	$d = amr_daysDifference( $start, $end);
	/* weeks */
	$w = $d / 7;  if ($w < 1) $w = 0;
	$d = $d % 7;   /* the remainder of days after complete weeks taken out */
	/* Note we do not need to add an extra day  or prior period if the hours go over a day, as the previous calculation will already have worked that out ????*/

	/* hours */
	$b = $start->format('G');
	$e = $end->format('G');
	$h = $e - $b;
	if ($h < 0) {
		$d = $d - 1;
		$h = 24 + $h;
	}
	/* minutes */
	$b = $start->format('i');
	$e = $end->format('i');
	$m = $e - $b;
	if ($m < 0) {
		$h = $h - 1;
		$m = 60 + $m;
	}

	/* seconds */
	$b = $start->format('s');
	$e = $end->format('s');
	$s = $e - $b;
	if ($s < 0) {
		$m = $m - 1;
		$s = 60 + $s;
	}

	$duarray = array ();
	if ($w > 0) {$duarray['weeks'] = (int)$w;}
	if ($d > 0) {$duarray['days'] = (int)$d;}
	if ($h > 0) {$duarray['hours'] = (int)$h;}
	if ($m > 0) {$duarray['minutes'] = (int)$m;}
	if ($s > 0) {$duarray['seconds'] = (int)$s;}

	return ($duarray);
}
/* ---------------------------------------------------------------------- */
/*  Return true iff the two times span exactly a multiple of 24 hours, from midnight one day to midnight the next.   But what if multi day over daylight saving ?? 
 */
function amr_is_all_day($d1, $d2) {
	if (!(is_object($d1) and (is_object($d2)))) return(false);

	if ($d1==$d2) return false; // if exact match then assume strict RC 5545 adherence, so cannot be all day, must be zero duration

	$dur = amr_calc_duration ( $d1, $d2);  // duration could be zero if no end time or DTEND exact same as DTSTART (not all day)
	if (empty ($dur['hours']) and empty ($dur['minutes']) and empty ($dur['seconds'] )) {
		return true;
		}
	else {
		return (false);
	}
}
/* ---------------------------------------------------------------------- */
/* return true if the event is untimed and the end is one day after the start */
function amr_is_an_ical_single_day($d1, $d2) {
//	If (ICAL_EVENTS_DEBUG) echo '<br>check if ical single day<br>'.$d1->format('c').'<br>'.$d2->format('c');

	$d1a = clone ($d1);
	date_modify ($d1a,'next day');
//	If (ICAL_EVENTS_DEBUG) echo '<br>check if ical single day<br>'.$d1a->format('c').'<br>'.$d2->format('c');
	if ($d1a === $d2) {
		return (true);
		}
	return (false);
	}
/* --------------------------------------------------------------------------------------*/
function amr_is_same_day($d1, $d2) { /** Return true iff the two specified times fall on the same day. */
		return (	$d1->format('Ymd') === 	$d2->format('Ymd'));
		}
/* --------------------------------------------------------------------------------------*/
function amr_is_before($d1, $d2) {	/* Return true if the first date is earlier than the second date */
		if ($d1 < $d2 ) return (true);
		else return (false);
	}
/* --------------------------------------------------------- */
function amr_add_duration_to_date (&$e, $d) {
/* NOTE: some date modify installations cannot cope with 0 durations, or just a +, so do not pass that ! */
/* adjust the signs  of the duration array as necessary to that date modify can handle it */

/*   dur-value  = (["+"] / "-") "P" (dur-date / dur-time / dur-week)

  dur-date   = dur-day [dur-time]

  dur-time   = "T" (dur-hour / dur-minute / dur-second)

  dur-week   = 1*DIGIT "W"

  dur-hour   = 1*DIGIT "H" [dur-minute]

  dur-minute = 1*DIGIT "M" [dur-second]

  dur-second = 1*DIGIT "S"

  dur-day    = 1*DIGIT "D"
  */
	if (empty($d)) return ($e);
	if (!is_array($d)) $d = maybe_unserialize($d);

	if ((isset($d['sign'] )) and ($d['sign'] === '-')) $dmod = '-';  /* then apply it to get our current end time */
	else $dmod = '+';

	foreach ($d as $i => $v)  {  /* the duration array must be in the right order */
		if (!($i === 'sign')) {
			if ( (!(empty($v))) and (!($v == '0')))	$dmod .= $v.' '.$i ;
			}
	}
	if ((!empty($dmod)) and (strlen($dmod) > 1)) date_modify ($e, $dmod );
	return ($e);
  }
 /* ------------------------------------------------------------------------------------*/
function amr_derive_dates (&$e) {
/* Derive basic date dependent data  - called early on before repeating */
	if (!isset($e['DTSTART']) ) return (false);
	if (is_array($e['DTSTART'])) $e['DTSTART'] = $e['DTSTART'][0];
	if (isset($e['DTEND']) and is_array($e['DTEND'])) $e['DTEND'] = $e['DTEND'][0];
	if ((isset ($e['DURATION'])) and (empty ($e['DTEND'])))  {  /*** an array of the duration values, calc the end date or time */
		$e['DTEND'] = new Datetime(); //if cloning dont need tz
		$e['DTEND'] = clone ($e['DTSTART']);
		$e['DTEND'] = amr_add_duration_to_date ($e['DTEND'], $e['DURATION']);
	}
	else {
		if ((isset ($e['DTEND'])) and (empty ($e['DURATION']))) { /* we don't have a duration */
			$e['DURATION'] = amr_calc_duration ( $e['DTSTART'], $e['DTEND']);		/* calc the duration from the original values*/
		}
	}
	if (isset ($e['DTEND']) and is_object($e['DTEND']) ) {
		if (!isset($e['allday']) and amr_is_all_day($e['DTSTART'], $e['DTEND'])) // only chcek if not done
			$e['allday'] = 'allday';
	/* set EndDate as human version instead of technical DTEND */
		$e['EndDate'] = new Datetime(); //if cloning dont need tz
		$e['EndDate'] = clone ($e['DTEND']);
		if ((isset($e['allday']) and ($e['allday'] == 'allday')))
			date_modify($e['EndDate'],'-1 day'); // for human view
	}

	return($e);
}
/* ------------------------------------------------------------------------------------*/
function amr_derive_eventdates_further (&$e) {
global $amr_globaltz;
/* Derive any date dependent data - requires EventDate at least to have been set */
	$now = date_create('now',$amr_globaltz );

	if (isset($e['EventDate'])) { 
		if (isset($_REQUEST['tzdebug'])) {echo '<br /> date='; var_dump($e['EventDate']);}
		// 20140209 somehow events have eventdate stored in db
		if (!is_object($e['EventDate'])) {
			$e['EventDate'] = amr_convert_date_string_to_object($e['EventDate']);
		}
		date_timezone_set($e['EventDate'],$amr_globaltz );
	}
	if (!isset($e['Classes'])) $e['Classes'] = '';
	if (isset($e['EndDate'])) {
		if (!is_object($e['EndDate'])) {
			$e['EndDate'] = amr_convert_date_string_to_object($e['EndDate']);
		}
		date_timezone_set($e['EndDate'],$amr_globaltz );
		if (amr_is_before($e['EndDate'], $now)) $e['Classes'] .= ' history';
		}
	else /* ie there is no end date, just an event date */ if (amr_is_before($e['EventDate'], $now)) $e['Classes'] .= ' history';
	if (amr_is_before( $now, $e['EventDate'])) $e['Classes'] .= ' future';
	else $e['Classes'] .= ' inprogress';
	
	if (amr_is_same_day ($e['EventDate'],  $now)) $e['Classes'] .= ' today';

		if (isset ($e['Untimed'])) { /* if it is untimed, the ical spec says that the end date is the "next day" */
		$e['Classes'] .= ' untimed';
		if (isset ($e['EndDate']) ) {
			if (	(amr_is_an_ical_single_day ($e['EventDate'], $e['EndDate'])) OR
					(amr_is_same_day($e['EndDate'],  $e['EventDate'])) ) /* an ical generator error, but deal with it */ {
				unset ($e['EndDate']); /* so we don't display them unecessarily */
				unset ($e['EndTime']);
				}
			else { /* it must be a multi day, all day - due to spec, we need to chop a day off for presentation purposes */
				//$e['EndDate']->modify("-1 day");  // NO! Endate wil be created correct;y, DT END will remain as original
				if ($e['EventDate'] == $e['EndDate']) { /* */
				}
				}
			}
	}
	else $e['StartTime'] = $e['EventDate']; /* will format to time, later keep date  for max flex */

	if ((isset($e['allday']) and ($e['allday'] == 'allday')) OR
	    ((isset ($e['EndDate']) ) and (amr_is_all_day($e['EventDate'], $e['EndDate'])))) {

			unset ($e['StartTime']);
			unset ($e['EndTime']);
			$e['Classes'] .= ' allday';
//			$e['EndDate']->modify("-1 day"); //due to spec, we need to chop a day off for presentation purposes // NO! Endate wil be created correct;y, DT END will remain as original
		}
	else {
		if (isset ($e['EndDate'])) {
			if (amr_same_time($e['EventDate'], $e['EndDate']))
				unset ($e['EndTime']);
			else $e['EndTime'] = $e['EndDate'];
		}
	}

	return($e);
}
/* ------------------------------------------------------------------------------------*/
function amr_derive_component_further (&$e) {
$e = amr_derive_for_list_or_eventinfo ($e); // order nb - otherwise summary have link when we do not want it to
$e = amr_derive_info_for_list_only ($e);

return ($e);

}
/* ------------------------------------------------------------------------------------*/
function amr_derive_for_list_or_eventinfo (&$e) {  // should only derive the ones we need
	if (isset ($e['GEO'])) {
		$e['map'] = amr_ical_showmap($e['GEO']);
		}
	else if ((isset ($e['LOCATION'])) and (!empty($e['LOCATION']))) {
			$e['map'] = amr_ical_showmap($e['LOCATION']);
	}
	if ($g = add_event_to_google($e))
		$e['addevent'] = $g;

	if (function_exists ('amr_subscribe_to_event')) {
		$g = amr_subscribe_to_event($e);
		if (!empty($g) ) $e['subscribeevent'] = $g;
	}
	if (function_exists ('amr_subscribe_to_event_series')) {
		$g = amr_subscribe_to_event_series($e);
		if (!empty($g) ) $e['subscribeseries'] = $g;
	}
	if (function_exists ('amr_indicate_attendance') and !empty($e['id'])) {
		$e['attending_event'] = amr_indicate_attendance($e['id']);
	}
	if (function_exists ('amr_register_for_event') and !empty($e['id'])) {
		$e['register'] = amr_register_for_event($e['id']);
	}
	$e = apply_filters('amr-derive-custom-fields',$e);
	return ($e);

}
/* ------------------------------------------------------------------------------------*/
function amr_derive_info_for_list_only (&$e) {

	/* Do not call this from eventinfo shortcode - it will cause an infinite loop! */
	if (!isset($e['Classes'])) $e['Classes'] = ''; //initialise
	if (isset ($e['EventDate'])) {
		amr_derive_eventdates_further($e);
//		$bookm = $e['EventDate']->format('U');
	}
//	else $bookm = '';
	/* Noew get some styling possibilities */
	if (isset ($e['RRULE']) or (isset ($e['RDATE']))) $e['Classes'] .= ' recur';
	if (isset ($e['STATUS']) ){
		$e['Classes'] .= ' '.amr_just_flatten_array($e['STATUS']);  /* would have values like 'CONFIRMED'*/
	}
	if (isset($e['name']))  $e['Classes'] .= ' '.$e['name'];
	if (isset($e['type']))  $e['Classes'] .= ' '.$e['type'];  /* so we can style events, todo's differently */
	if (!empty($e['CATEGORIES'])) {
		if (is_array($e['CATEGORIES'])) { // v4.1.16 need category_name for wp queries, but classes do not like spaces
			foreach ($e['CATEGORIES'] as $i=>$c) {$sluggish[$i] = sanitize_title($c);}
			$e['Classes'] .= ' '.implode(' ',$sluggish);
		}
		else
			$e['Classes'] .= ' '.sanitize_title($e['CATEGORIES']);
	}
	if (isset($e['terms']) and is_array($e['terms']))  // so we have all the associated terms
		$e['Classes'] .= ' '.implode(' ',$e['terms']);
	if (isset($e['tag_ids']) and is_array($e['tag_ids']))  // so we have all the associated terms
		$e['Classes'] .= ' t'.implode(' t',$e['tag_ids']);
//	if (isset($e['UID'])) {
//		$e['Bookmark'] = str_replace('@','',$e['UID']);  /* must be before summary as it is used there .  Must be a char to start not a number and get rid of odd chars for validation*/
//		$e['Bookmark'] = 'a'.htmlentities(str_replace('http://','',$e['Bookmark'] ).$bookm);
//	}
	$e['SUMMARY'] = amr_derive_summary ($e);  // do not hover the description
	$e['excerpt'] = amr_make_excerpt_from_description($e);
	return ($e);
}
/* --------------------------------------------------  */
function amr_make_excerpt_from_description ($e) {
	if (empty($e['excerpt'])) {
		if (!empty($e['DESCRIPTION'])) {  // must be abn ics file
			$desc = amr_just_flatten_array ($e['DESCRIPTION']);
		}
		else if (!empty($e['CONTENT'])) {  // must be abn ics file
			$desc = strip_shortcodes(amr_just_flatten_array ($e['CONTENT']));
		}
		else $desc = '';

		$length = apply_filters('excerpt_length', 55); // in case a filter returns garbage;
		if (!is_int($length))  $length = 55;
		$excerpt = substr( 
				$desc,
				0, // start
				$length) //length
				.'...'; 

		return ($excerpt);
	}
	else return($e['excerpt']);
}
/* --------------------------------------------------  */
function amr_just_flatten_array ($arr) {
/* expecting array of text strings - convert to one txt string */
	$txt = '';
	if (is_array($arr)) {
		if (empty($arr)) return (null);
		else {
			foreach ($arr as $i => $v) {	$txt .= $v;	}
			return ($txt);
		}
	}
	else return($arr);
}
/* --------------------------------------------------  */
function amr_check_flatten_array ($arr) {
	if (is_array($arr)) {
		if (empty($arr)) return (null);
		else {
			foreach ($arr as $i => $v) {if (empty($v)) unset ($arr[$i]);}
			if (empty($arr) or (count($arr)< 1)) return (null);
			else return ($arr);
		}
	}
	else return ($arr);
}
/* --------------------------------------------------  */
function add_querystring_var($url, $key, $value) {
   /* replaces the first instance with the key and value passed */

	   $url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');

	   $url = substr($url, 0, -1);

	   if (strpos($url, '?') === false) {

			return ($url . '?' . $key . '=' . $value);

	   } else {

			return ($url . '&' . $key . '=' . $value);

	   }

}
/* --------------------------------------------------  */
function amr_get_htmlstylefile() {
	global $amr_options;
	global $amr_listtype;

	$custom_htmlstyle_file = $amr_options['listtypes'][$amr_listtype]['general']['customHTMLstylefile'];
	if (empty ($custom_htmlstyle_file ) )
		return false;
	$uploads = wp_upload_dir();
	if (!file_exists($uploads['basedir'].$custom_htmlstyle_file))  // just in case
		return false;
	else
		$custom_htmlstyle_file = $uploads['basedir'].$custom_htmlstyle_file ;
	return($custom_htmlstyle_file);
}
/* --------------------------------------------------  */
function amr_doing_box_calendar () {
global $amr_options,
	$amr_liststyle,
	$amr_listtype;

	if (in_array ($amr_liststyle, array('smallcalendar','largecalendar','weekscalendar')))  return true;
	else return false;
}
/* --------------------------------------------------  */
function amr_do_a_headercell_html($htm, $i, $colhead) {
	$html =	((!empty($htm['hcell'])) ? $htm['hcell'].' class="amrcol'.$i.'">': '')
			.$colhead
			.((!empty($htm['hcellc'])) ? $htm['hcellc'] : '');
return $html;
}
/* -------------------------------------------------------------------------------------------*/
function amr_do_column_header_html($htm, $i, $headhtml) {
	$html =	$htm['head']
			.(!empty($htm['row']) ? ($htm['row'].'>'): '')
			.$headhtml
			.(!empty($htm['rowc']) ? $htm['rowc'] : '')
			.$htm['headc'];
return $html;
}
/* -------------------------------------------------------------------------------------------*/
		/** Sort the specified associative array by the specified key.
		 * Originally from
		 * http://us2.php.net/manual/en/function.usort.php.
		 */
function amr_sort_by_key($data, $key) {
	if (!(is_array($data)) ) return ($data);
			// Reverse sort
			$compare = create_function('$a, $b', 'if (!isset($a["' . $key . '"])) return false;'
			.'	if (!isset($b["' . $key . '"])) return (false) ;'
			.'	if  ($a["' . $key . '"] == $b["' . $key . '"]) { return 0; } else { return ($a["' . $key . '"] < $b["' . $key . '"]) ? -1 : 1; }');
			usort($data, $compare);
			return $data;
		}
/* ------------------------------------------------------------------------------------*/
function amr_reverse_sort_by_key($data, $key) {
	if (!(is_array($data)) ) return ($data);
			// Reverse sort
			$compare = create_function('$a, $b', 'if (!isset($a["' . $key . '"])) return false;'
			.'	if (!isset($b["' . $key . '"])) return (false) ;'
			.'	if  ($a["' . $key . '"] == $b["' . $key . '"]) { return 0; } else { return ($a["' . $key . '"] > $b["' . $key . '"]) ? -1 : 1; }');
			usort($data, $compare);
			return $data;
		}

function amr_falls_between($eventdate, $astart, $aend) {
		/*
		 * Return true iff the specified event falls between the given
		 * start and end times.
		 */
		if (($eventdate <= $aend) and
			($eventdate >= $astart)) return ( true);
		else return (false);
		}
/* ------------------------------------------------------------------------------------*/
function amr_event_should_be_shown($event, $astart, $aend) {
		/*
		 Return true if the specified event should be shown.  This could be due to a number of situations:
		 * there is no dtstart (it is a note?)
		 * event starts and ends between the start and end date
		 * event starts after the start date, before the end date - it may finish after the end date/time.
		 * event started before the start date and ends after the finish date
		 * event is an untimed event (eg: all day) and starts on the same day, but before the start datetime.  (Does it still have it's end date - we zap that at some point?)
		 */
		if (!isset ($event['DTSTART'] )) return (true);
		//if (ICAL_EVENTS_DEBUG) echo '<hr>dtstart='.$event['DTSTART']->format('c');
		if ($event['DTSTART'] >= $astart) {
			//if (ICAL_EVENTS_DEBUG) echo '<br />date >= start' ;
			if ($event['DTSTART'] <= $aend) {
				//if (ICAL_EVENTS_DEBUG) echo '<br />date <= end' ;
				return (true);
				}
			else {
				//if (ICAL_EVENTS_DEBUG) echo '<br />date > start' ;
				if (isset($event['Untimed']) and amr_is_same_day($event['DTSTART'],$aend )) {
					//if (ICAL_EVENTS_DEBUG) echo '<br />date untimed and same day' ;
					return (true);
				}
				else {
					//if (ICAL_EVENTS_DEBUG) { echo '<br />date not untimed or not same day' ;  echo '<hr>';}
					return (false);
					}
			}
		}
		else { /* ie DTSTART is < $astart, but may still be on same day, or not finished, ie continuing past */
			//if (ICAL_EVENTS_DEBUG) echo '<br />date < start' ;
			if (isset($event['DTEND'])	and ($event['DTEND'] >= $astart)) {
				//if (ICAL_EVENTS_DEBUG) echo '<br />date <= end' ;
				return (true);
			}
			else {
				//if (ICAL_EVENTS_DEBUG) echo '<br /> No end....' ;
				if (isset($event['Untimed']) and amr_is_same_day($event['DTSTART'],$astart )) {
					//if (ICAL_EVENTS_DEBUG) echo '..Is Untimed and same day' ;
					return (true);
				}
				else if (isset($_REQUEST['debugall'])) echo '.. Is NOT Untimed or not same day' ;
			}

		}
}
/* ------------------------------------------------------------------------------------*/
function amr_arrayobj_unique2 (&$arr) { 	/* Process an array of datetime objects and remove duplicates 		 */
		/* 	Duplicates can arise from edit's of ical events - these will have different sequence numbers and possibly differnet details - delete one with lower sequence,
			Also there maybe? the possobility of complex recurring rules generating duplicates - these will have same sequence no's and should have same details - delete one
			Note: Mozilla does not seem to generate a SEQUENCE ID, but does do a X-MOZ-GENERATION.
		*/
		$limit = count ($arr);
		if (isset($_REQUEST['debugexc'])) {echo '<br><br>Check for modifications or exceptions for array of '.$limit.' records<br>'; }
		krsort ($arr);  /***  sort numerically  We can then walk through and "toss" the lower sequence numbers for a given uid and date instance */
		if (isset($_REQUEST['debugexc'])) {
			echo '<br />Check sorted correctly :';
			foreach ($arr as $i=> $a) {echo '<br>..'.$i;}  /* Check the sorting */
			echo '<br />End Check sorted correctly.<br />';
		}
		$seqprev = '';
		$uiddateprev = '';
		foreach ($arr as $i => $e) {
				if ($seqstart = strrpos($i, " ")) {
					$seq = (int) substr($i, $seqstart, 100);
					$uiddate = substr ($i, 0, $seqstart);
				}
				else {
					if (isset($_REQUEST['debugexc'])) echo '<hr/>Must be a unique event? as key does not match pattern'; break;
				} /* not an ical event, must be a unique post event */
				if (isset($_REQUEST['debugexc'])) {
					echo '<br />seq='.$seq.' '.substr($i,0,6).'...'.substr($i,$seqstart-26,200);
					echo '<br />uidate: '.$uiddate;
					echo '<br />uidprv: '.$uiddateprev;
				}
				if ($uiddate == $uiddateprev) {
					if ($seq < $seqprev ) {
						unset ($arr[$i]);
						if (isset($_REQUEST['debugexc'])) echo '<br /><b>Delete seq: Seq was ='.$seq.' and $seqprev was: '.$seqprev	.'</b>';
						}
					else if ($seqprev < $seq) {
						if (isset ($iprev)) unset ($arr[$iprev]);
						if (isset($_REQUEST['debugexc']))
							echo '<br /><b>Delete seqprev Seq was ='.$seq.' and seqprev was: '.$seqprev.'</b>';
					}
					else echo '<br ><b>Unexpected inability to sort modification from original.  Please inform administrator. ? seqprev = '.$seqprev.' and seq = '.$seq.'</b>';
					/* Note that while sequence numbers can be the same for a recurrencid, the plugn will add 999 for a recurrence id */
				}
				else {
					if (isset($_REQUEST['debugexc'])) {echo '<br />No uid and date match.<br />';}
				}
				$uiddateprev = $uiddate;
				$seqprev = $seq;
				$iprev = $i;
//				if (!(empty($seq)) and (!($seq===" "))) {
	//				if (isset($_REQUEST['debugexc'])) echo '<br />** Possible exception with seq: '.$seq.' - check for outdated instances for <br />'.substr($i,0,6).'...'.substr($i,$seqstart-26,200);
//				}
			}
	}
	/* ========================================================================= */
function amr_repeat_anevent(&$event, $astart, $aend, $limit) {
global $amr_globaltz;
	/* for a single event, handle the repeats as much as is possible */
	$repeats = array();
	$exclusions = array();
	$repeatstart = new Datetime(); //if cloning dont need tz
	$repeatstart = clone $event['DTSTART'];
	$event = amr_parseRepeats($event);

	if (!empty($event['RRULE']))	{

		if (isset($_GET['debugexc'])) {
			echo '<hr />After Parsing Repeats:<br />';
			if (isset ($event['RRULE'])) { echo ' RRULE:<br />';var_dump($event['RRULE']); }
			echo '<br />* RDATE:<br />';
			if (isset ($event['RDATE'])) var_dump($event['RDATE']);
			echo '<br />* EXDATE:<br />';
			if (isset ($event['EXDATE'])) var_dump($event['EXDATE']);
			echo '<br />* EXRULE:<br />';
			if (isset ($event['EXRULE'])) var_dump($event['EXRULE']);

			}

		if (!isset($event['RRULE'][0])) $event['RRULE'] = array($event['RRULE']); /* depending where we got our rrule from, we may or may not have multiple */
		if (isset($_GET['rdebug'])) { echo '<br><b>We have '.count($event['RRULE']).' rrules</b>';}
		foreach ($event['RRULE'] as $i => $rrule) {

			$reps = amr_process_RRULE($rrule, $repeatstart, $astart, $aend, $limit);
			if (is_array($reps) and count($reps) > 0) {
				$repeats = array_merge ($repeats, $reps);
			}
		}
		if (isset($_GET['rdebug'])) { echo '<br>Got '.count($repeats). ' after RRULE';}
	}
	if (!empty($event['RDATE']))	{
		if (ICAL_EVENTS_DEBUG) { echo '<br>Processing RDATES...';}
		foreach ($event['RDATE'] as $i => $rdate) {
					$reps = amr_process_RDATE  ($rdate, $repeatstart, $aend, $limit);
					if (is_array($reps) and count($reps) > 0) {
						$repeats  = array_merge($repeats , $reps);
					}
				}
				if (ICAL_EVENTS_DEBUG) { echo '<br>Got '.count($repeats). ' after RDATE';}
		}

	if (!empty($event['EXRULE']))	{
			if (isset($_REQUEST['debugexc'])) { echo '<br><h3>Have EXRULE </h3>';var_dump($event['EXRULE']);}
			foreach ($event['EXRULE'] as $i => $exrule) {
				$reps = amr_process_RRULE($exrule, $repeatstart, $astart, $aend, $limit);
				if (is_array($reps) and count($reps) > 0) {
					$exclusions  = $reps;
				}
			}
			if (isset($_REQUEST['debugexc'])) { echo '<br><h3>Got '.count($exclusions). ' after EXRULE</h3>';}
		}

	if (!empty($event['EXDATE']))	{
			if (isset($_GET['debugexc'])) {
				echo '<br><h4>Have EXDATE to exclude </h4>';
				foreach ($event['EXDATE'] as $exdate) {echo '<br />EXC:'.$exdate->format('c');};
			}
			$exclusions  = array();
			foreach ($event['EXDATE'] as $i => $exdate) {
				$reps = amr_process_RDATE($exdate, $repeatstart, $aend, $limit);
				if (is_array($reps) and count($reps) > 0) {
						$exclusions  = array_merge($exclusions , $reps);
				}

			}
			if (isset($_GET['debugexc'])) { echo '<br />Got  '.count($exclusions). ' exclusions after checking EXDATE';}
		}

	if (isset($_GET['rdebug'])){ echo '<br />Still have '.count($repeats). ' before exclusions<br />';}
			/* Now remove the exclusions from the repeat instances */
		if (is_array($exclusions) and count ($exclusions) > 0) {

			foreach ($exclusions as $i => $excl) {
				date_timezone_set($excl, $amr_globaltz );
			    foreach ($repeats as $j => $rep) {
					date_timezone_set($rep, $amr_globaltz );
					if ((isset($_REQUEST['debugexc']) )) {
						echo '<br />Exc:'.$excl->format('c'). ' =? Rep:'.$rep->format('c');
					}
				    if ($excl->format('c') == $rep->format('c')) {
//					if ($excl === $rep) {
//					if (!($excl < $rep) and !($excl > $rep)) {
						if (isset($_REQUEST['debugexc'])) {
							echo '<br> Exclusion matches repeat date, so exclude this date '.$j.' '. $rep->format('c');
						}
						unset($repeats[$j]); /* will create a gap in the index, but that's okay, not reliant on it  */
					}
				}
			}
		}
		if (isset($_REQUEST['debugexc'])) { echo '<br>Now have '.count($repeats). ' after  exclusions '; }

		return ($repeats);

	}
	/* ========================================================================= */
function debug_print_event ($e, $nest=0) {
		$tab = '';
		echo '<ul>';
		foreach ($e as $i => $f) {
			if (is_object($f)) echo '<li>'.$tab.$i.' = '.$f->format('c').'</li>';
			else if (is_array($f)) {echo '<li>'.$tab.$i; debug_print_event ($f, ($nest+1)); echo '</li>';}
				else {echo '<li>';
//				.$tab.$i.' = '
				if (!(is_int($i))) echo $i.' = ';
				var_dump($f).'</li>';
				}
		}
		echo '</ul>';
	}
	/* ========================================================================= */
function amr_create_enddate (&$e) {
	/* if the necessary data exist, then create the end date for a possibly repeated event.  - note this is NOT the DTEND */
	if (isset ($e['DURATION'])) {/* if not just an alarm */
		if (isset ($e['EventDate'])) {
			$e['EndDate'] = new Datetime(); //if cloning dont need tz
			$e['EndDate'] = clone ($e['EventDate']);
			$e['EndDate'] = amr_add_duration_to_date ($e['EndDate'], $e['DURATION']);
			//check here whether all day or not  - human expects 1 day less, DTEND expects next day - keep it so
			if (!empty ($e['allday']) and ($e['allday'] == 'allday')) {
				date_modify($e['EndDate'],'-1 day');
			}
			return (true);
		}
	}
	else return (false);
	}
	/* ========================================================================= */
function amr_generate_repeats(&$event, $astart, $aend, $limit) { /* takes an event and some parameters and generates the repeat events */
		$repeats = array(); // and array of dates
		$newevents = array();  // an array of events
		
		if (isset($event['DTSTART'])) {
			if (is_object($event['DTSTART'])) {
				$dtstart = $event['DTSTART'];
				$dt = empty($event['DTSTART']) ? '' : $event['DTSTART']->format('c'); /* begin setting up the event key that will help us check for modifocations - semingly duplicates! - overwrite for repeats */
			}
			else {
				if (ICAL_EVENTS_DEBUG) echo('<r><b>DTSTART is not an object</b>'.$event['DTSTART']);
				return (false); /* it is set, but it is not an aobject ? */	}
			}
		else { /* possibly an undated, non repeating VTODO or Vjournal- no repeating to be done if no DTSTART, and no RDATE */
			$dt = '-nodtstart';
			if (!isset($event['RDATE']))	{
				if (isset ($event['UID'])) 	
					$newevents[$event['UID']] = $event;
				else 
					amr_tell_admin_the_error('There is an invalid event.  It has no UID:'.print_r($event,true));
				return ($newevents); /* possibly an undated, non repeating VTODO or Vjournal- no repeating to be done if no DTSTART, and no RDATE */
			}
			else {
				echo ('This event is invalid.  It has no DTSTART, but does have RDATE.  Not allowed according to ical spec.');
				return(false);
			/***check for repeating RDATEs if no start date */
			}
		}
		/* To handle modifications, use a key to the events, so can match any later mods with a repeating event we may have generated */
		$seq = empty($event['SEQUENCE']) ? '0' : $event['SEQUENCE']; /* begin setting up the event key that will help us check for modifications - semingly duplicates!*/
		if (!isset($event['UID'])) $event['UID'] = 'NoUID';
		 /* there is no repeating rule, so just copy over */
		if (isset ($event['RECURRENCE-ID'])) {   /* a modification or exceptions to a repeating instance ? */
//					$recdate = $dt;
			if (isset ($_GET['debugexc'])) {echo '<br />RECURRENCE-ID:'; var_dump($event['RECURRENCE-ID']);}
			if (is_array($event['RECURRENCE-ID'])) { /* just take first, should only be one */
				$recdateobj = $event['RECURRENCE-ID'][0];
				$recdate = $recdateobj->format('YmdHis');   // purely identifies specifc instances of a repeating rule are affected by the exception/modification
				//20140721 - lost the month in ymd somehow - added back
				if (isset ($_GET['debugexc'])) {echo '<br /> Flag recurrence modification for '.$recdate; }
			}
			else if (is_object($event['RECURRENCE-ID'])) {  /* Then it is a date instance which has been modified .  We need to overwrite the appropriate repeating dates.  This is done later? *** */
				$recdateobj = $event['RECURRENCE-ID'];
				$recdate = $recdateobj->format('YmdHis');
				if (isset ($_GET['debugexc'])) {echo '<br />RecurrenceID date to check against event rrule.' ;echo($recdate);}
			}
			else { /****  should deal with THISANDFUTURE or THISANDPRIOR  EG:
				 RECURRENCE-ID;RANGE=THISANDPRIOR:19980401T133000Z */
				echo '<br>THISAND.... modification to repeating event encountered.  This cannot be dealt with yet'; var_dump($event['RECURRENCE-ID']);
			}
			if ( amr_event_should_be_shown($event,$astart, $aend) or
				amr_falls_between($recdateobj, $astart, $aend) or  /* If the modification relates to an event  instance (ie date) that is in range */
				amr_is_same_day ($recdateobj, $astart) or /* if on same day, may be an all day event - will constrain finally later */
				amr_is_same_day ($recdateobj, $aend)
				) /* OR the new date is in our display range, */{
				$key = $event['UID'].' '.$recdate.' '.$seq.'999';  /* By virtue of being a recurrence id it should override a non recurrence (ie normal) even if they have the same sequence */
				$newevents[$key] = $event;  /* so we drop the old events used to generate the repeats */
				$newevents[$key]['EventDate'] = new Datetime(); //if cloning dont need tz
				$newevents[$key]['EventDate'] = clone ($dtstart);
				if (!(amr_create_enddate($newevents[$key]))) {if (ICAL_EVENTS_DEBUG) echo ' ** No end date - is it an alarm? ';};
			}
			else {
				if (isset ($_GET['debugexc'])) {
					echo '<br /> '.$recdate.' not in range and '.$dtstart->format('c').' not in range' ;
//					debug_print_event ($event);
					}
				return(false); /* the modification and the instance that it relates to are not in our date range */
			}
		}
		else { /* It is not a recurrence id, may be a repating, or solo */
			if (isset($dtstart) ) {
				if ( amr_is_before ($dtstart, $aend) ) {  /* If the start is after our end limit, then skip this event */
				if (isset($event['RRULE']) or (isset($event['RDATE']))) {
					/* if have, must use dtstart in case we are dependent on it's characteristics,. We can exclude too early dates later on */
					$repeats = amr_repeat_anevent($event, $astart, $aend, $limit );  /**** try for a more efficient start? */
					if (isset($_REQUEST['rdebug']) or ICAL_EVENTS_DEBUG) {
						if (count($repeats) > 0)	echo '<br>Create repeats: '.count($repeats);
					}
					/* now need to convert back to a full event by copying the event data for each repeat */
					if (is_array($repeats) and (count($repeats) > 0)) {
						foreach ($repeats as $i => $r) {
							$repkey = $event['UID'].' '.$r->format('YmdHis').' '.$seq;	/* Don't use timezone - some recurrence id's maybe created with universal dates */
							if (isset ($newevents[$repkey])) {
								// error_log('Unexpected Duplication of Repeating Event '.$repkey.' - error in ical file or error in plugin?'); // only happened on weird rrule - may be valid anyway so as not to miss anything
							}
							$newevents[$repkey] = $event;  // copy the event data over - note objects will point to same object unless we clone   Use duration or new /clone Enddate
							$newevents[$repkey]['EventDate'] = new Datetime(); //if cloning dont need tz
							$newevents[$repkey]['EventDate'] = clone ($r);
//							if (ICAL_EVENTS_DEBUG) {echo '<br>Created '.$newevents[$repkey]['EventDate']->format('YmdHis l');	}
							if (!amr_create_enddate($newevents[$repkey])) {
								if (ICAL_EVENTS_DEBUG) echo ' ** No end date created, maybe just a start? ';
								};
						}
					}
				}
				else {
					$key = $event['UID'].' '.$dt.' '.$seq;  /* No Recurrence id and no RRULE or RDATE */
					$newevents[$key] = $event;  // copy the event data over - note objects will point to same object - is this an issue?   Use duration or new /clone Enddate
					$newevents[$key]['EventDate'] = new Datetime(); //if cloning dont need tz
					$newevents[$key]['EventDate'] = clone ($dtstart);
					if (isset ($newevents[$key]['DURATION'] )) {
						amr_create_enddate ($newevents[$key]);  //changed because EndDate not same as DTEND if all day
						}
					}
				}
			}
			else { // no starting date
				$key = $event['UID'].' '.$dt.' '.$seq;
				$newevents[$key] = $event;
				$newevents[$key]['EventDate'] = '';
				}
		}
		if (ICAL_EVENTS_DEBUG) {echo '<br />number of newevents = '.count($newevents);}
		return ($newevents);
	}
/* ------------------------------------------------------------------------------------*/
	/*
	 * Constrain the list of COMPONENTS to those which fall between the
	 * specified start and end time, up to the specified number of
	 * events.
	 * Note; MUST take  RECURRENCE -ID if the recurrence date is in range (even though the DTSTART may not be), as they may be modifying a date that is within the range
	 */
function amr_constrain_components($events, $start, $end, $limit) {
global $amr_limits;

		$newevents = $events;
		/* should we be moving to destination date  */
		if ((count($newevents) < 1) or (!is_array($newevents)))	
			return (false);
			
		$newevents = amr_sort_by_key($newevents , 'EventDate');

		$newevents = apply_filters('amr_events_after_sort', $newevents);

		if (ICAL_EVENTS_DEBUG) {
			echo '<br>Sorted  '.count($newevents).' events';
			echo '<br>first '.$newevents[0]['EventDate']->format('c');
			echo '<br>last '.$newevents[count($newevents)-1]['EventDate']->format('c');
			echo '<br>Now constrain them...<br> ';
		}
		$constrained = array();
		$count = 0;
		foreach ($newevents as $k => $event) {
//
			if (isset ($event['EventDate']) and (is_object ($event['EventDate']))) {
				if (amr_falls_between($event['EventDate'], $start, $end) OR
					(isset($event['EndDate']) and
						(amr_falls_between($event['EndDate'], $start, $end) OR  /* end date will catch those all day ones that are untimed or those that h ave not yet finished !! */
						(amr_falls_between($start, $event['EventDate'], $event['EndDate'])))) ) /* catch those that start before our start and end after our start */
					{
					$constrained[] = $event;
					if (isset($_REQUEST['debugall'])) {
						echo '<br>Choosing no: '.$k.' '. $event['EventDate']->format('c').' ending '. (isset($event['EndDate'])? $event['EndDate']->format('c'): ' no end');		}
					++$count;
				}
			}
			else $constrained[] = $event;
			if ($count >= $limit) break;
		}

		if (isset($amr_limits['eventsoffset']) and (!empty($amr_limits['eventsoffset']))) {
			if (function_exists('amr_do_events_offset')) {
				$constrained = amr_do_events_offset ($constrained);
			}
		}
		$constrained = apply_filters('amr_events_after_sort_and_constrain', $constrained);
		return $constrained;
	}
/* ========================================================================= */
/*
		 * generate repeating events down to nonrepeating events at the  corresponding repeat time.
		 For ease of processing the repeat arrays will initially be ISO 8601 date (added in PHP 5)  eg:	2004-02-12T15:19:21+00:00
		 we will then convert them to date time objects
		 */
function amr_process_icalevents($events, $astart, $aend, $limit) {
		$dates = array();
		foreach ($events as $i=> $event) {
			amr_derive_dates ($event); /* basic clean up only - removing unnecessary arrays etc */
			$more = amr_generate_repeats($event, $astart, $aend, $limit);
			if (ICAL_EVENTS_DEBUG) {echo '<br />'.$i.' number of more events = '.count($more);}
			if (is_array($more)) 
				$dates = array_merge ($dates,$more) ;
			if (ICAL_EVENTS_DEBUG) {echo '<br />number of dates = '.count($dates);}	
			//amrical_mem_debug('before unset '.$i);
			//unset($more);
			//amrical_mem_debug('After event '.$i);
		}
		if (ICAL_EVENTS_DEBUG) {echo '<hr>No.Dates= '.count($dates);  } //
		if ((is_array($dates)) and (count($dates) > 1)) { /* must be > 1 for tere to be a duplicate! */
			amr_arrayobj_unique2($dates); /* remove any duplicate in the values , check UID and Seq.  This may arise if we have many VEVENTS relating to one event */
			//if (ICAL_EVENTS_DEBUG) { echo '<br>Now have '.count($dates). ' after duplicates check.';}
		}
		return ($dates) ;
	}
/* -------------------------------------------------------------------------*/
function process_icalurl($url) {
global $amr_limits;
/* cache the url if necessary, and then parse it into basic nested structure */
	amrical_mem_debug('Before Process ical url');
	$file = amr_cache_url(str_ireplace('webcal://', 'http://',$url),$amr_limits['cache']);
	if (!($file)) {
			echo '<br>'.sprintf(__('Unable to load or cache ical calendar %s','amr-ical-events-list'),$url);
			return false;
		}
		
	amrical_mem_debug('Before parse ical url');	
	$ical = amr_parse_ical($file);

	if (! (is_array($ical) )) {
			echo '<a class="error" href="#" title="'.sprintf(__('Error finding or parsing ical calendar %s','amr-ical-events-list' ),$url).'">!</a>';
			return($ical);
		}
	$ical['icsurl'] = $url;

	return ($ical);
}
/* -------------------------------------------------------------------------*/
function amr_echo_parameters() {
global $amr_limits;
	foreach ($amr_limits as $i=> $v) {
		if (!(in_array($i, array('cache',
		'eventscache',
		'pagination',
		'headings',
		'show_views',
		'calendar_properties',
		'show_month_nav',
		'day_links',
		'eventpoststoo') ))) {
			$label = __($i,'amr-ical-events-list').' = ';
			if (is_object($v)) $t[] = $label.$v->format('j M i:s');
			else if (!empty($v)) $t[] = $label.$v;
		}
	}
	$text = implode (', ',$t);
	return ($text);
}
/* -------------------------------------------------------------------------*/
function amr_string($s) {
/* Maybe check for a calendar name and if it exists, then use it for styling? - NOt NOW  */
	return(str_replace(array (' ','.','-',',','"',"'"), '', $s));
}
/* -------------------------------------------------------------------------*/
function suggest_other_icalplugin($featuretext) {
?>
	<br/><?php echo $featuretext.' - '.__('This feature requires the plugin amr-events','amr-ical-events-list');
	?>&nbsp;<a href="http://icalevents.com"><?php _e('Get it here','amr-ical-events-list' ); ?></a>
	<br/><?php

}

/* -------------------------------------------------------------------------*/
function amr_get_events_cache_key ($criteria) {
	global $amr_limits;
	$string = '';
	$keys = array_merge($amr_limits,$criteria);
	if (isset($keys['headings'])) unset($keys['headings']);
	if (isset($keys['pagination'])) unset($keys['pagination']);
	if (isset($keys['cache'])) unset($keys['cache']);
	if (isset($keys['eventscache'])) unset($keys['eventscache']);
	foreach ($keys as $i => $v) {
		if (!empty($v)) {
			if (is_object($v))
				$string .= ' '.$i.'='.$v->format('Ymd:H');
			else {
				if (is_array($v)) $string .= ' '.$i.'='.implode($v);
				else $string .= ' '.$i.'='.$v;
			}
		}
	}

	if (isset($_REQUEST['debugcache'])) echo '<br /><b>string for key of cache = <br/>'.$string.'</b>';
	$key = md5( $string );
	return ($key);
}
/* -------------------------------------------------------------------------*/
function amr_get_cached_events_from_db($criteria) {
	global $amr_limits;

	if (version_compare(5.3, PHP_VERSION, '>')) {
		if (isset($_REQUEST['debugcache']))echo '<b>NB: No event transient caching possible yet  because objects do not serialise properly in php < 5.3</b>';
		return(false);
	}
	else return (false) ; /* until we upgrade ourselves and can test properly  - icdsoft do not have yet */

	if (isset($_REQUEST['debugcache'])) echo '<h3>Trying cache = </h3>';
//---- build the key
	$key = amr_get_events_cache_key($criteria);
// ----now see if we have a cache

	$cache = get_transient('amr_events');

	if ( is_array($cache) && isset( $cache[ $key ] ) ) {
		 if (isset($_REQUEST['debugcache'])) echo ('<hr>we got an events cache with key '.$key);
//		 var_dump($cache[ $key ]); die('let see');

         return ( $cache[$key] );
	}
	else {
		if (isset($_REQUEST['debugcache'])) echo ('<h3>No events cached - requery </h3>');
		return false;
	}
}
/* -------------------------------------------------------------------------*/
function amr_set_cached_events_from_db($criteria, $events) {
	global $amr_options;  /***  nb when we get back to this -when hosts are on 5.3 or for some other reason, fetch in hours eventscache */
	global $amr_limits;
//---- build the key
	$key = amr_get_events_cache_key($criteria);
	if (isset($_REQUEST['debugcache'])) echo '<h3>Setting cache with key = '.$key.'</h3>';
// ----now see if we have a cache

	$cache = get_transient('amr_events');
	$cache[$key] = $events;
	set_transient('amr_events', $cache, 60*20); /* save transient for 20 mins */

	if (isset($_REQUEST['debugcache']))  echo ('<h3>cache set  '.$key.'</h3>');
	return true;
}
/* -------------------------------------------------------------------------*/
function amr_process_icalspec($criteria, $start, $end, $no_events, $icalno=0) {
/*  parameters - an array of urls, an array of limits (actually in amr_limits)  */
	global $amr_options,
		$amr_limits,
		$amr_listtype,
		$amrW,
		$amrtotalevents,
		$change_view_allowed,
		$amr_doing_icallist, /* use to prevent the eventinfo shortcode echoing data to the screen when in ical event list mode */
		$amr_one_page_events_cache;  /* if widget and calendar doing same thing */

	$amr_doing_icallist = true;
	if (!empty($amrW)) 
		$w = 'w'; /* so we know if we are in the widget or not */
	else 
		$w = '';

	$key = amr_get_events_cache_key ($criteria);
	if (isset ($amr_one_page_events_cache[$key]))  {
		$icals 		= $amr_one_page_events_cache[$key]['icals'];
		$components = $amr_one_page_events_cache[$key]['components'];
		if (ICAL_EVENTS_DEBUG)   echo ('<h3>Grabbing the one page events cache  '.$key.'</h3>');
		}
	else { 
		if (!empty($criteria['exclude_in_progress']))
			add_filter ('amr_events_after_sort_and_constrain', 'amr_events_exclude_in_progress');
		if (!empty($criteria['sort_later_events_first']))
			add_filter ('amr_events_after_sort_and_constrain', 'amr_events_sort_later_events_first');	
		If (isset($_GET['debugcache']))   echo ('<h3>No one page events cache  '.$key.', so start afresh</h3>');
		if (isset($_GET['debugcache'])) { echo ('<hr>Criteria '); var_dump($criteria);}
		if ((isset($amr_limits['eventpoststoo'])) and ($amr_limits['eventpoststoo'])) {
			if (function_exists('amr_ical_from_posts')) {
//				$events = amr_get_cached_events_from_db($criteria);
//				if ($events) {	if (ICAL_EVENTS_DEBUG) echo '<h3>We got some events cached </h3>';}
//				else {

					$events = amr_ical_from_posts($criteria);
					//amr_set_cached_events_from_db($criteria, $events);  /*** cannot use till php 5.3 */

//				}
				if (!empty($events)) {
					foreach ($events as $i=>$event) {
						$events[$i] = amr_parse_unparsed($event);
						$event 		= amr_parseRepeats($event);
					}
					if (ICAL_EVENTS_DEBUG) { echo '<br>Events from posts now parsed. Try make calendar';}
					
					$icals = amr_make_ical_from_posts($events, $criteria);

					if (ICAL_EVENTS_DEBUG) { echo '<br>Got calendars from posts :'.count($icals).' events='.count($events).'<br>'; }
				}

			}
			else {
				if (empty($criteria['urls'])) {
					suggest_other_icalplugin (__('No url entered - did you want events from posts ?','amr-ical-events-list' ));
				}
				//else _e("huh?");
				}

		}
		else if (ICAL_EVENTS_DEBUG) echo '<br />Ignore eventsposts ';
	}
	/* ------------------------------  check for urls and do those too, or only */
		$icals2 = array();
		if (!empty($criteria['urls'])) {
			foreach ($criteria['urls'] as $i => $url) {
				$icals2[$i] = process_icalurl($url);
				if (!(is_array($icals2[$i]))) unset ($icals2[$i]);
				else if (function_exists('amr_mimic_taxonomies'))
					$icals2[$i] = amr_mimic_taxonomies ($icals2[$i]) ;	// filter by category_name
			}
		}

		if (!empty($icals) ) { 
			if (isset($icals2) )
				$icals = array_merge($icals, $icals2 );
		}
		else
			if (isset($icals2) ) $icals = $icals2;

	/* ------------------------------now we have potentially  a bunch of calendars in the ical array, each with properties and items */

		/* Merge then constrain  by options */
		$components = array();  /* all components actually, not just events */
		if (isset ($icals) and is_array($icals)) {	/* if we have some parse data */
			foreach ($icals as $j => $ical) { /* for each  Ics file within an ICal spec*/
				if ((!isset($amr_options['listtypes'][$amr_listtype]['component'])) or (count($amr_options['listtypes'][$amr_listtype]['component']) < 1))
					_e('No ical components requested for display','amr-ical-events-list');
				else {
					foreach ($amr_options['listtypes'][$amr_listtype]['component'] as $i => $c) {  /* for each component type requested */
						if ($c) {		/* If this component was requested, merge the items from Ical items into events */
							if (isset($ical[$i])) {  /* Eg: if we have an array $ical['VEVENT'] etc*/
								foreach ($ical[$i] as $k => $a) { /*  save the compenent type so we can style accordingly */
									$ical[$i][$k]['type'] = $i;
									$ical[$i][$k]['name'] = 'cal'.$j; /* save the name for styling */
								}
								if (!empty($components) ) {
									$components = array_merge ($components, $ical[$i]);	
								}
								else 
									$components = $ical[$i];
							}
						}
					}
				}
			}
			If (isset($_REQUEST['debug'])) echo '<br />Got x events '.count($components);
			
			amrical_mem_debug('Before process');
			$components = amr_process_icalevents($components, $start, $end, $no_events);
			amrical_mem_debug('Before constrain');
			$amrtotalevents = count($components);
			$components = amr_constrain_components($components, $start, $end, $no_events);
			$amr_last_date_time = amr_save_last_event_date($components);
			If (ICAL_EVENTS_DEBUG) {
				echo '<br />After constrain No dates:'.count($components).' and last event date time is: ';
				if (is_object($amr_last_date_time)) echo $amr_last_date_time->format('c');
				var_dump($amr_last_date_time);
			}
		}
		if (!isset ($amr_one_page_events_cache[$key])) {
				$amr_one_page_events_cache[$key]['components'] = $components;
				$amr_one_page_events_cache[$key]['icals'] = $icals;
		}

		
		if (isset($_REQUEST['debug'])) {echo '<hr/>Is end set Before list - 1? '; var_dump($amr_limits['end']);}		
		amrical_mem_debug('Before listing');
		if (isset ($icals) and is_array($icals)) {
/* amr here is the main html  code  *** */
			if (isset($amr_limits['calendar_properties']) )
				$do_prop = $amr_limits['calendar_properties'];
			else
				$do_prop = true;
			if ($do_prop) {
				$tid 	= $w.'calprop'.$icalno;
				$class 	= 'vcalendar '.$w.'icalprop '; //20110222
				$thecal =  amr_list_properties ($icals, $tid, $class);		/* list the calendar properties if requested */
			}
			else $thecal = '';
			
if (isset($_REQUEST['debug'])) {echo '<hr/>Is end set Before list - 0? '; var_dump($amr_limits['end']);}
			
			if ((!amr_doing_box_calendar())
			and empty($components) ) {
				$thecal .= amr_handle_no_events ();
			}
			else {
				$tid 	= '';
				$class 	= ' ical ';
				if (isset($_REQUEST['debug'])) {echo '<hr/>Is end set Before list? '; var_dump($amr_limits['end']);}
				$thecal .= amr_list_events($components, $tid, $class, $show_views=true);
			}

		}
		else $thecal = '';	/* the urls were not valid or some other error ocurred, for this spec, we have nothing to print */
/* amr  end of core calling code --- */

	if (!empty($amr_options['do_grouping_js']) ) {
		$thecal .=	amr_ical_load_frontend_scripts();
	}
	amrical_mem_debug('After generating listing');
	return ($thecal);
	
	}
/* -------------------------------------------------------------------------*/
function amr_save_last_event_date($events) { // must have been sorted
global $amr_last_date_time;
	if (is_array($events)) {
		$lastevent = end($events);
		if (!empty($lastevent['EventDate'])) {
			$amr_last_date_time =   $lastevent['EventDate'];
			return $amr_last_date_time;
			}
		else {	
			if (ICAL_EVENTS_DEBUG) { echo ' No last date time in:'; var_dump($lastevent);}
		}	
	}
	else {
		if (ICAL_EVENTS_DEBUG) { echo ' Events not an array'; var_dump($events);}
		}
	
// else will not save a last date.
}
/* -------------------------------------------------------------------------*/
function amr_get_params ($attributes=array()) {
/*  We are passed the widget or shortcode attributes,
      check them, get what we can there, then check for passed parameters (form or query string )
     Anything unset we will get from the default settings for that listtype.
     The defaults list type is 1.
     then set the amr_limits (note the calendar options may overwrite these)
*/
	global $amr_limits, /* has days, events, start ? end ?*/
	$amr_listtype,
	$amr_liststyle,
	$amr_options,
	$amr_groupings,
	$amr_formats,
	$amr_globaltz,
	$change_view_allowed,
	$amr_calendar_url,
	$amrW; // indicates if widget, 
	



//	if (!empty ($amrW) ) //must be empty not isset  ??nlr - default listtype should be set in attributes
//		$amr_listtype='4';
//	else
//		$amr_listtype='1';

	//If (ICAL_EVENTS_DEBUG) {echo '<hr>Attributes passed by calling function<br />'; var_dump($attributes); }
//
	$amr_options = amr_getset_options();
//
	$defaults = array( /* defaults array for shortcode , - these override limits if they exist in limit, */
	'listtype' => $amr_listtype,
  	'startoffset' => '',
	'hoursoffset' => '',
	'monthsoffset' => '',
	'daysoffset'=> '',
	'start' => '', /* date('Ymd'), */
	'days' => '',
	'events' => '',
	'months' => '',      /* if we have months, start at begin of month and ignore days? */
	'hours' => '',
	'weeks' => '',
	'tz' => '',
	'eventpoststoo' => '1',
	'agenda' => '',
	'calendar' => '',
	'eventmap' => '0',
	'show_views' => '1',
	'show_month_nav' => '0',
	'show_look_more' => '0',
	'day_links' => '1',
	'month_year_dropdown' => '0',
	'month_prev_next' => '0',
	'calendar_properties' => '1',
	'pagination' => '1',
	'headings' => '1',
	'ignore_query' => '',
	'grouping' => '',
	'more_url' => '',
	'no_filters' => '0',
	'sort_later_events_first' => '0',
	'exclude_in_progress' => '0'
	);

	$shortcode_params = shortcode_atts( $defaults, $attributes ) ;

	//
	if (!empty ($attributes) ) {  // if there is stuff left - must be urls
		foreach ($attributes as $i => $v) {
			if (is_numeric ($i) or ($i == 'ics')) {  // ie if it is a value passed with no name, we expect only urls like that
				if (substr($v, 0 ,1) == ':') { /* attempt to maintain old filter compatibity */
					$shortcode_params['urls'][$i] = substr ($v, 1);
				}
				$v = (str_ireplace('webcal://', 'http://',$v));
				if ((function_exists('filter_var')) and (!filter_var($v, FILTER_VALIDATE_URL))) { /* rejecting a valid URL on php 5.2.14  */
					echo '<h2>'.sprintf(__('Invalid Ical URL %s','amr-ical-events-list'), $v).'</h2>';
					if (current_user_can('administrator')) {
						echo '<p>Notes to admin only (does not show if not logged in as admin): 
						<ul>
						<li>If you have put [ical in the widget - take it out! </li>
						<li>the php validation functon does not cope with internationalised domain, please contact the developer if this affects you.</li>
						</ul></p>';
					}
				}
				else
					$shortcode_params['urls'][$i] = esc_url_raw($v);
				unset ($attributes[$i]);
				}
		} // end foreach
	} //end if

// -------------------------------------------------------------------------------------------------- 
// handle taxonomies we do not even know about yet
	/*  get the parameters we want out of the attributes, supplying defaults for anything missing  */
	/* but now we may have missed taxonomies etc as the defaults do not know about them, so get the diff  */
	if (!empty($attributes)) {		
		//var_dump($attributes);
		//var_dump($shortcode_params); // - this could be multi-d array due to urls so array diff will throw a notice in php 5.4
		// maybe extract somehow ourselves?  or use array_diff_key instead /****
		$taxo_selection = array_diff_key($attributes,$shortcode_params);
	}	
	// if we had some taxos, marge them into shortcode selection
	if (!empty($taxo_selection)) 	
		$shortcode_params = array_merge ($shortcode_params,$taxo_selection );

	$allow_query = (!isset($shortcode_params['ignore_query']) or !($shortcode_params['ignore_query']) );
	$ignore_query_all = (isset($shortcode_params['ignore_query']) and ($shortcode_params['ignore_query'] == 'all') );
	unset ($shortcode_params['ignore_query']);

	if ($allow_query) {
	//
		parse_str($_SERVER['QUERY_STRING'], $queryargs); /* Get anything passed in the query string that will override shortcodes */
		// check for posted values to get around that pesky problem

		if (isset($_REQUEST['start'])) $queryargs['start'] = $_REQUEST['start'];

		foreach ($queryargs as $i=>$arg) {
			$queryargs[$i] = filter_var($arg, FILTER_SANITIZE_STRING); //Strip tags,
			}
		if ($change_view_allowed) {	// for upcoming events widget, may want to allow query of days etc, but not the listtype change
			if (isset($queryargs['calendar'])) 	
				$queryargs['listtype'] 	= $queryargs['calendar'];
			if (isset($queryargs['agenda']) )	
				$queryargs['listtype'] 	= $queryargs['agenda'];
		}
		

		unset($queryargs['page_id']);
		unset($queryargs['debug']);
		If (isset($_REQUEST['debugq'])) {echo '<hr> Query allowed, attributes/args to consider adding:<br />'; var_dump($queryargs); }

		$shortcode_params = array_merge ($shortcode_params, $queryargs);
	/* If the input arrays have the same string keys, then the later value for that key will overwrite the previous one.
	If, however, the arrays contain numeric keys, the later value will not overwrite the original value, but will be appended.
	*/
//		if (ICAL_EVENTS_DEBUG) {echo '<hr>After merge with query args<br />'; var_dump($shortcode_params); }
		//unset($queryargs['listtype']);
	}
	else if (isset($_REQUEST['debugq'])) {echo '<hr>Ignoring most query parameters ';}
//
	// save the global list type first
	if (!empty($shortcode_params['listtype'])) {
		$amr_listtype = $shortcode_params['listtype'];
		if (ICAL_EVENTS_DEBUG) {echo '<br />Listtype from shortcode: '.$amr_listtype; echo '<br />shortcode params'; var_dump($shortcode_params);}
		unset($shortcode_params['listtype']);
		
	}
	

	//	unset($attributes['listtype']);
	if ($change_view_allowed and $allow_query) { // then we will NOT ignore these query parameters as that  will break our navigation
		//if (ICAL_EVENTS_DEBUG) {echo '<br />Allowed to change views';}
		if (isset($_GET['listtype'])) 
			$amr_listtype = intval ( $_GET['listtype']);
		if (isset($_GET['eventmap'])) 
			$amr_listtype = 'eventmap'; // havent figured this out yet
	}

	// check the listtype here ?
	if (empty($amr_options['listtypes'][$amr_listtype])) {
		if (current_user_can('administrator') ) { 
			echo (sprintf(__('System error - event list type %s missing - please inform administrator.', 'amr-ical-events-list'),$amr_listtype));
			
			_e('Now using an available list type to list events','amr-ical-events-list');
		}
		$amr_listtype = amr_first_available_listtype ();
	}

	$amr_liststyle = (isset ($amr_options['listtypes'][$amr_listtype]['general']['ListHTMLStyle']))
			? $amr_options['listtypes'][$amr_listtype]['general']['ListHTMLStyle']
			: $amr_liststyle = 'table';//; 'list'

	if (!empty ($shortcode_params['more_url']) ) {
		$amr_calendar_url =  esc_url($shortcode_params['more_url']);
		unset($shortcode_params['more_url']);
	}

// always respond to start
//	if (in_array ($amr_liststyle, array('smallcalendar','largecalendar', 'weekscalendar'))) {
		// then query args overeride even if we have ignore query
	if (!$ignore_query_all) {
		if (isset($_REQUEST['start'])) 
			$queryargs['start'] = $_REQUEST['start'];	//need here too
		if (!empty($queryargs['start']))  {
				$shortcode_params['start'] = abs ((int) $queryargs['start']);
				if (ICAL_EVENTS_DEBUG) {echo '<br />START = '.$shortcode_params['start'];}

//			$shortcode_params['start'] = (substr((string) $shortcode_params['start'],0,6).'01'); //set to month start - done in month shortcdoe?
			}
		if (!empty($queryargs['months'])) 
			$shortcode_params['months'] = abs( (int) $queryargs['months']);
		if (!empty($queryargs['tz']))
			$shortcode_params['tz'] = filter_var($queryargs['tz'],FILTER_SANITIZE_STRING);
	}
	else if (isset($_REQUEST['debugall'])) echo '<br/>** Ignoring all query parameters';

//----------------------------------------------------------------------------------------------------------------- now do thelimits array
	// then get the limits for that list type

	$amr_limits = $amr_options['listtypes'][$amr_listtype]['limit']; /* get the default limits */
	If (isset($_REQUEST['debug'])) {echo '<hr>Defaults limits before we change them<br />'; var_dump($amr_limits ); }
	
	
	if ($amr_liststyle === 'weekscalendar') {
		$amr_limits['weeks'] = 2; // default for horizontal calendar
	}  
	if (($amr_liststyle === 'largecalendar') or $amr_liststyle === 'smallcalendar')  {
		if (empty($amr_limits['months'])) $amr_limits['months'] = 1; // default for horizontal calendar
	}  

	If (isset($_REQUEST['debugall'])) {echo '<hr>limits before we change them<br />'; var_dump($amr_limits ); }
// now check groupings
	$gg = '';
	if (!empty($shortcode_params['grouping']))
		$gg = ucwords($shortcode_params['grouping']);
	unset($shortcode_params['grouping']);
	if (!empty($_REQUEST['grouping']))
		$gg = ucwords($_REQUEST['grouping']);
	if (!($gg == '')) {
		unset($amr_options['listtypes'][$amr_listtype]['grouping']);
		if (array_key_exists($gg,$amr_groupings)) { // if it is a valid grouping
			$amr_options['listtypes'][$amr_listtype]['grouping'][$gg] = true;
			unset ($shortcode_params['grouping']);
		}
	}

	if (!empty ($shortcode_params ) ) {
		foreach ($shortcode_params as $i => $v) { // must be atts not limits as atts may hold more then limits - limits just has initial limits
			if (in_array($i, array(
				'headings',
				'show_views',
				'agenda',
				'eventmap',
				'calendar',
				'eventpoststoo',
				'pagination',
				'calendar_properties',
				'no_filters',
				'show_month_nav',
				'show_look_more',
				'day_links',
				'more_url',
				'month_year_dropdown',
				'month_prev_next'
				)	)) {
				$amr_limits[$i] = abs (intval ( $v));
				unset($shortcode_params[$i]);  // only unset if empty  what??? - else will lose others
			}
			else {
				if  (in_array($i, array('days','events','months','listtype','hours', 'weeks'
					,'daysoffset', 'startoffset', 'hoursoffset','monthsoffset'))) {
					if (!empty($v)) 
						$amr_limits[$i] = (int)$v ;  // can be negative, so no abs
					unset($shortcode_params[$i]);
					}
				}
		}
		if (!empty($amr_limits['listtype'])) {
			$amr_listtype = $amr_limits['listtype'];
			if (ICAL_EVENTS_DEBUG) echo '<br />listtype set to: ';
		}
	}

 // else might be a taxonomy or categeory etc  ... pass through - not if we have filtered string
/* ----check if we want to overwrite the wordpress timezone */
	if (!(empty($shortcode_params['tz'])))  // allows one to overwrite the timezone
		$amr_globaltz = timezone_open ($shortcode_params['tz']);

	unset ($shortcode_params['preview']);	//clear it out so we do not pass to wp query
	unset ($shortcode_params['tz']);	//clear it out so we do not pass to wp query

	If (isset($_REQUEST['tzdebug'])) {

		echo '<p>Plugin/Wordpress Timezone:'.timezone_name_get($amr_globaltz);
		echo ', current offset is '.$amr_globaltz->getOffset(date_create('now',$amr_globaltz))/(60*60).'</p>';
		}
//-------------------------------
	$pos_int_options = array("options"=> array("min_range"=>1, "max_range"=>1000));
	$neg_int_options = array("options"=> array("min_range"=>-1000, "max_range"=>1000));
	/* check non url parameters  */

	if (empty ($shortcode_params['start'])) {
		$amr_limits['start'] = date_create('now',$amr_globaltz);		
	}
	else {
		$amr_limits['start'] = abs((int) $shortcode_params['start']);
	}
	unset ($shortcode_params['start']); // clear it out so we do not pass it to wp query

// work out whether we are doing a calendar or not.  calendars must still respond to at least the month navigation

	foreach ($amr_limits as $i => $a) {
		if (($i === 'start') or ($i === 'end')) {
			if (!(is_object($a))) {
				if (checkdate(substr($a,4,2), /* month */
						substr($a,6,2), /* day*/
						substr($a,0,4)) /* year */ )
						$amr_limits[$i] = date_create($a,$amr_globaltz);
				else {
					_e('Invalid Start Date', 'amr-ical-events-list');
					$amr_limits[$i] = date_create('now',$amr_globaltz);
					}
			}
		 /*  else all is okay - we have default date of now */
		}
	}

	If (ICAL_EVENTS_DEBUG) {echo '<hr> Check limits for listtype:'.$amr_listtype.' <br />'; var_dump($amr_limits); }

	if (!empty( $shortcode_params['daysoffset'])) { /* keeping startoffset for old version compatibility, but allowing for daysoffset for compatibility with other offsets */

		$amr_limits['startoffset'] = (int) $shortcode_params['daysoffset']; // can be negative
	}
/* ---- check for urls that are either passed by query or form, or are in the shortcode with a number or not ie: not ics =  */

	//set up global for easier access
	$amr_formats = $amr_options['listtypes'][$amr_listtype]['format'];
// now adjust our start dta if necessary
	if (!empty($amr_limits['startoffset'])) {
		$daysoffset = (int)($amr_limits['startoffset']);
		if ($daysoffset > 0) $daysoffset = '+'.(string)$daysoffset.' days';
		else $daysoffset = (string)$daysoffset.' days';
		date_modify($amr_limits['start'],$daysoffset) ;
	}
//
	if (!empty($amr_limits['hoursoffset'])) {
		$hrsoffset = (int)($amr_limits['hoursoffset']);
		if ($hrsoffset > 0) $hrsoffset = '+'.(string)$hrsoffset.' hours';
		else $hrsoffset = (string)$hrsoffset.' hours';
		date_modify($amr_limits['start'],$hrsoffset) ; /*** as per request from jd  */
	}
//
	if (!empty($amr_limits['monthsoffset'])) {
		$mthsoffset = (int)($amr_limits['monthsoffset']);
		if ($mthsoffset >= 0) $mthsoffset = '+'.(string)$mthsoffset.' months';
		else $mthsoffset = (string)$mthsoffset.' months';
		date_modify($amr_limits['start'],$mthsoffset) ;
	}
//
	if (!empty($amr_limits['hours'])) { /* then set the time to the beginning of the day , and get rid of months and days */
		date_time_set ($amr_limits['start'],$amr_limits['start']->format('G'),0,0);	/* set the time to beginning of the hour */
		unset ($amr_limits['days']);
		unset ($amr_limits['months']);
	}
	if (!empty($amr_limits['months'])) {/* then set the date to the beginning of the month and get rid of days */
			$days = (int) $amr_limits['start']->format('d');
			if ($days > 1) 	$amr_limits['start']->modify('-'.($days-1).' days');
			date_time_set ($amr_limits['start'],0,0,0);
			unset ($amr_limits['days']);
		}
//
	if (empty($amr_limits['hours'])) {/* else use the days and  then set the time to the beginning of the day */
		date_time_set ($amr_limits['start'],0,0,0);
	}
	
	$wkst = ical_get_weekstart(); // get the wp start of week
	if (isset($_GET['debugwks'])) echo '<br/>wkst = '.$wkst;
	if (!empty($amr_limits['weeks'])) { // weeks overrides all else
				
		$amr_limits['start'] = amr_get_human_start_of_week ($amr_limits['start'], $wkst); /* set to start of week */
		$amr_limits['days'] = $amr_limits['weeks'] * 7;
		unset ($amr_limits['hours']);

		if (isset($_GET['debugwks'])) { echo '<br />Start set to = '.$amr_limits['start']->format('c');}

	}
//	now find our end date  - may get overridden if calendar
	$amr_limits['end'] = new Datetime(); //if cloning dont need tz
	$amr_limits['end'] = clone ($amr_limits['start']);

	if (!empty($amr_limits['months']))
		date_modify($amr_limits['end'],'+'.($amr_limits['months']).' months') ;
	if (!empty($amr_limits['days']))
		date_modify($amr_limits['end'],'+'.($amr_limits['days']).' days') ;
	if (!empty($amr_limits['hours']))
		date_modify($amr_limits['end'],'+'.($amr_limits['hours']).' hours') ;
	else  date_modify($amr_limits['end'],'-1 second') ; /* so that we do not include events starting in the next time period */

	if (isset($_GET['debug'])) echo '<br/>end = '.$amr_limits['end']->format('c');


//	date_time_set ($amr_limits['end'],23,59,59);  // if we have this, do we need the -1 second below?
	If (isset($_GET['debugq'])) {
		echo '<hr> Before passing others <br />';
		var_dump($shortcode_params);
		echo amr_echo_parameters();
	}

	If (isset($_GET['debugq']))  {echo '<hr> Left in params to pass to wp query?: <br />'; var_dump($shortcode_params); }
	return ($shortcode_params);  // only return params needed for wp query ?
}
/* -------------------------------------------------------------------------*/
function amr_get_id_for_shortcode () {  // allows for the possibility of multiple shortcodes in one page.
global $amr_icalno;

	if (!(isset($amr_icalno))) $amr_icalno = 0;
	else $amr_icalno= $amr_icalno + 1;

	if ($amr_icalno	== 0) $idnum= '';
	else $idnum = $amr_icalno;
	$id = ' id="events_wrap'.$idnum.'" ';

	return ($id);
}
/* -------------------------------------------------------------------------*/
function amr_do_ical_shortcode ($attributes, $content = null) {
global $amr_limits,
	$change_view_allowed,
	$amr_ical_am_doing;/* used to give each ical  table a unique id on a page or post */
// This is the main function.  It replaces [iCal URL]'s with events. Each as a separate list
/* Allow multiple urls and only one listtype */
/*  merge atts with this array, so we will have a default list */

global $amr_been_here;
/*  Test because if some numbskull puts the ical or events shortcodes IN an event.
*/	
	if (amr_a_nested_event_shortcode ()) return (false); //someone entered an event shortcode into event content - causing a loop of events lists inside event lists
	
	$change_view_allowed = true;
	$amr_ical_am_doing = 'list';
	$defaults['listtype'] = 1;
	$defaults['eventpoststoo'] = '0';
	$defaults['show_views'] = 0;
	$defaults['show_month_nav'] = 0;
	$defaults['headings'] = 0;

	if (empty($attributes)) $atts = $defaults;

	else $atts = array_merge( $defaults, $attributes ) ;
	$criteria =	amr_get_params ($atts);  /* strip out and set any other attstributes  - they will set the limits table */
	/* separate out the other possible variables like list type, then just have the urls */


	$content = amr_process_icalspec(
		$criteria,
		$amr_limits['start'],
		$amr_limits['end'],
		$amr_limits['events']);

	$id = amr_get_id_for_shortcode();
	$html = '<div '.$id.' >'.$content.'</div>';
	
	/* we made it out the other end with out looping ?*/
	$amr_been_here = false;
	
  return ($html);

}
/* -------------------------------------------------------------------------*/
function amr_do_smallcal_shortcode ($attributes) {
global $amr_limits,
	$amr_listtype,
	$change_view_allowed,
	$amr_icalno,
	$amr_ical_am_doing,
	$amr_been_here;
// treat as widget anyway to avoid view changes etc
// This is the main function.  It replaces [iCal URL]'s with events. Each as a separate list   if no listtype set it to 8
/* Allow multiple urls and only one listtype */
/*  merge atts with this array, so we will have a default list */
	$change_view_allowed = true;
	$amr_ical_am_doing = 'smallcalendar';
	// set defaults, can be overridden by shortcode parameters
	$defaults['listtype'] = 8;
	$defaults['months'] = 1;
	$defaults['show_views'] = 0;
	$defaults['ignore_query'] = 0;
	
	if (amr_a_nested_event_shortcode ()) return (false); //someone entered an event shortcode into event content - causing a loop of events lists inside event lists

	if (empty($attributes)) $attributes = array();

	$atts = array_merge( $defaults, $attributes ) ;
	$criteria =	amr_get_params ($atts);  /* strip out and set any other attstributes  - they will set the limits table */
		// remove the custom post type parameter as it will cause us to lose all others and just show 1 event

	/* separate out the other possible variables like list type, then just have the urls */
	$id = amr_get_id_for_shortcode(); // need this to increment icalno

	$content = amr_process_icalspec(
		$criteria,
		$amr_limits['start'],
		$amr_limits['end'],
		$amr_limits['events'],
		$amr_icalno);
	$change_view_allowed = true; // reset the global


	if (isset ($amr_limits['months']) and ($amr_limits['months'] > 1))
		$id = ' id="multismallcalendar" ';

	$html = '<div '.$id.' >'.$content.'</div>';

	/* we made it out the other end with out looping ?*/
	$amr_been_here = false;
  return ($html);

}
/* -------------------------------------------------------------------------*/
function amr_do_largecal_shortcode ($attributes, $content = null) {
global $amr_limits,
 $amr_listtype,
 $change_view_allowed,
 $amr_ical_am_doing,
 $amr_icalno,
 $amr_been_here;
 ;/* used to give each ical  table a unique id on a page or post */
// This is the main function.  It replaces [iCal URL]'s with events. Each as a separate list   if no listtype set it to 8
/* Allow multiple urls and only one listtype */
/*  merge atts with this array, so we will have a default list */

	if (amr_a_nested_event_shortcode ()) return (false); //someone entered an event shortcode into event content - causing a loop of events lists inside event lists

	$change_view_allowed = true;
	$amr_ical_am_doing = 'largecalendar';

	$defaults['listtype'] = 9;
	$defaults['months'] = 1;
	$defaults['show_month_nav'] = 1; // default is on in calendar

	if (empty($attributes)) $atts = $defaults;
	else $atts = array_merge( $defaults, $attributes ) ;

	$criteria =	amr_get_params ($atts);  /* strip out and set any other attstributes  - they will set the limits table */

	/* criteria will be any selection */
	/* separate out the other possible variables like list type, then just have the urls */

	$id = amr_get_id_for_shortcode(); // need this to increment icalno

	$content = amr_process_icalspec($criteria,
		$amr_limits['start'],
		$amr_limits['end'],
		$amr_limits['events'],
		$amr_icalno);


	$html = '<div '.$id.' >'.$content.'</div>';
	/* we made it out the other end with out looping ?*/
	$amr_been_here = false;
  return ($html);

}
/* -------------------------------------------------------------------------*/
function amr_do_weekscal_shortcode ($attributes, $content = null) {
global $amr_limits,
 $amr_listtype,
 $change_view_allowed,
 $amr_ical_am_doing,
 $amr_icalno,
 $amr_been_here;/* used to give each ical  table a unique id on a page or post */
// This is the main function.  It replaces [iCal URL]'s with events. Each as a separate list   if no listtype set it to 8
/* Allow multiple urls and only one listtype */
/*  merge atts with this array, so we will have a default list */
	if (amr_a_nested_event_shortcode ()) return (false); //someone entered an event shortcode into event content - causing a loop of events lists inside event lists

	$change_view_allowed = true;
	$amr_ical_am_doing = 'weekscalendar';

	$defaults['listtype'] = 11;
	$defaults['weeks'] = 2;
	$defaults['months'] = 0;
	$defaults['days'] = 14;
	$defaults['show_month_nav'] = 1;  // will actually do weeks
	$defaults['show_views'] = '';

	if (empty($attributes)) 
		$atts = $defaults;
	else 
		$atts = array_merge( $defaults, $attributes ) ;

	$criteria =	amr_get_params ($atts);  /* strip out and set any other attstributes  - they will set the limits table */

	/* criteria will be any selection */
	/* separate out the other possible variables like list type, then just have the urls */

	$id = amr_get_id_for_shortcode(); // need this to increment icalno

	$content = amr_process_icalspec($criteria,
		$amr_limits['start'],
		$amr_limits['end'],
		$amr_limits['events'],
		$amr_icalno);


	$html = '<div '.$id.' >'.$content.'</div>';

	/* we made it out the other end with out looping ?*/
	$amr_been_here = false;
  return ($html);

}
// ----------------------------------------------------------------------------------------
function amr_first_available_listtype() {
	global $amr_options;
	if (isset ($amr_options['listtypes']['4'])) 
		return('4');  // use the widget list type if still there as least offensive general purpose list type
	$keys = array_keys($amr_options['listtypes']);
	return (array_shift($keys));
}
// ----------------------------------------------------------------------------------------
function amr_get_set_start_for_nav () {  // gets or sets a date object to the begiinging of the curremt month, or the passed date

	if (isset($_REQUEST['start'])) {
		$starttxt = intval ($_REQUEST['start']);
		$starttxt = substr($starttxt,0,4).'-'.substr($starttxt,4,2).'-'.substr($starttxt,6,2);
		$start = amr_newDateTime($starttxt);
		}
	else {
		$start = amr_newDateTime('');	// must always set to first of month for dropdown to work
		$y = $start->format('Y');
		$m = $start->format('m');
		$start->setDate($y,$m,'01');
	}
	return ($start);
}
/* 
/* -----------------------------------------------------------------------------------------------*/
function amr_ical_widget_init() {
	register_widget('amr_ical_widget');
	register_widget('amr_icalendar_widget');
}
/* ------------------------------------------------------------------------------------------------ */
function amrical_add_scripts() {
 	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
}
/* ------------------------------------------------------------------------------------------------ */
function amrical_add_adminstyle() {
	if ((stristr ($_SERVER['QUERY_STRING'],'manage_amr_ical'))
	or (stristr ($_SERVER['QUERY_STRING'],'manage_event_listing')))   {
		$myStyleUrl = ICALLISTPLUGINURL.'css/icaladmin.css';
		$myStyleFile = ICALLISTPLUGINDIR.'css/icaladmin.css';
		if ( file_exists($myStyleFile) ) {
            wp_register_style('amricaladmin', $myStyleUrl);
            wp_enqueue_style( 'amricaladmin');
        }
	}
}
/* ------------------------------------------------------------------------------------------------------ */
function amr_ical_exception_handler($exception) {
	
  echo __("Uncaught exception: ", 'amr-ical-events-list') , $exception->getMessage(), "\n";
  _e('<br /><br />An error in the input data may prevent correct display of this page.  Please advise the administrator as soon as possible.', 'amr-ical-events-list');

}
/* ------------------------------------------------------------------------------------------------------ */
function amr_notice_listtypes_converted () {  // from version < 4.0 to version 4.0

		echo ('<div class="updated fade">'
		.__('Your existing saved list types have been converted.  If you wish to return to the earlier version of the plugin, you will have to reset the list types and reapply your changes. Or use your DB backup to set the amr-ical-events-list option back to previous version. You do have regular backups right? ','amr-ical-events-list').'<br />');
		echo '</div>';
 }
/* ------------------------------------------------------------------------------------------------ */
function amr_ical_include_scripts() {  // only load js if requested
 global $amr_listtype,$amr_options,$amr_doing_icallist;
 // csss required for widgets etc, so allow general load
	
	if (empty($amr_doing_icallist)) return;
	if (empty($amr_options['do_grouping_js'])) return;
	$url =  plugins_url().'/amr-ical-events-list/js/amr-ical.js';
 //	wp_register_script( 'amr-ical', $url.'amr-ical.js', array( 'jquery'), false, false);
	wp_enqueue_script( 'amr-ical', $url, array( 'jquery'), false, true);  // true for in footer

 }
/* ------------------------------------------------------------------------------------------------ */
 function amr_ical_load_frontend_scripts() {  // only load js if requested
 // csss required for widgets etc, so allow general load
	wp_enqueue_script('jquery');

 }
/* ------------------------------------------------------------------------------------------------------ */
 /**
	Adds a links directly to the settings page from the plugin page
	*/
function amr_plugin_links($links, $file) {
		if ( $file == 'amr-ical-events-list/amr-ical-events-list.php' ) {
			$links[] = '<a title="'
			. __('General listing Settings', 'amr-ical-events-list')
			.' href="admin.php?page=manage_amr_ical" >'
			. __('Settings', 'amr-ical-events-list') . "</a>";
			$links[] = "<a href='admin.php?page=manage_event_listing'>" . __('List Type Settings', 'amr-ical-events-list') . "</a>";
			$links[] = "<a href='http://icalevents.com/documentation/'><b>" . __('Documentation', 'amr-ical-events-list') . "</b></a>";
		}
		return $links;
	} // end plugin_links()

/* ------------------------------------------------------------------------------------------------------ */
	set_exception_handler('amr_ical_exception_handler');
	//amr_ical_initialise ();   // setup all basic settings, like globals and constants etc
	add_action('plugins_loaded'         , 'amr_ical_initialise' ); // so can check debug
	if (is_admin() )	{
		add_action('admin_init'         , 'amrical_add_adminstyle');
		add_action('admin_menu'         , 'amrical_add_options_panel');
		add_action('admin_print_scripts', 'amrical_add_scripts');
	}
	else { // add_action('wp_head'        ,  'amr_ical_events_style');
		add_action('wp_print_styles'    , 'amr_ical_events_style');
		add_action('wp_enqueue_scripts' , 'amr_ical_load_frontend_scripts' ); //enque jquery
		add_action('wp_footer' 			, 'amr_ical_include_scripts' ); // add js to footer
	}

	add_action('widgets_init'           , 'amr_ical_widget_init');


	// as per ottos post for language packs
	//add_action( 'init'             		, 'amr_ical_load_text' );
	add_shortcode('iCal'                , 'amr_do_ical_shortcode');
	add_shortcode('ical'                , 'amr_do_ical_shortcode'); // in case people make mistake
	add_shortcode('smallcalendar'       , 'amr_do_smallcal_shortcode');
	add_shortcode('largecalendar'       , 'amr_do_largecal_shortcode');
	add_shortcode('weekscalendar'       , 'amr_do_weekscal_shortcode');
	add_shortcode('expandall'       	, 'amr_expand_all');

	register_activation_hook(__FILE__, 'amr_ical_events_list_record_version');
	add_filter('plugin_row_meta', 'amr_plugin_links', 10, 2);
	//add_action('plugins_loaded', 'amr_debug_time');  // removed for now - 'only' a debug function wrt to load times and somehow functionnotfound in mutisite. 

?>