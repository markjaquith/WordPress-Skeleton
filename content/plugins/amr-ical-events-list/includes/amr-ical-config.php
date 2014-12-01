<?php
/* This is the amr-ical config section file */


function amr_ical_initialise () {

global $amr_options;
global $amr_general;

global $amr_components;
global $amr_calprop;
global $amr_colheading;
global $amr_compprop;
global $amr_groupings;
global $amr_formats;
global $amr_csize;
global $amr_validrepeatablecomponents;
global $amr_validrepeatableproperties;
global $amr_wkst;
global $amrdf;
global $amrtf;
global $amr_globaltz,$ical_timezone; // amr events uses ical_timezone
global $utczobj;


if (!defined ('ICAL_EVENTS_DEBUG')) { 
	if (isset($_REQUEST["debug"])  
		and ((is_user_logged_in() and current_user_can('administrator') ) or amr_is_trying_to_help()) )
		{ /* for debug and support - calendar data is public anyway, so no danger*/
		
		define('ICAL_EVENTS_DEBUG', true);
		}
	else
		define('ICAL_EVENTS_DEBUG', false);
}

if (ICAL_EVENTS_DEBUG) {
	echo '<h1>Debug mode</h1>';
	echo 'Other debug parameters that can be used together or separately to isolate problems: ';
	echo 'debugexc, rdebug, cdebug, tzdebug, debugall, debugq, memdebug, debugtime <br/>';
	if (!defined('AMR_ICAL_VERSION')) define('AMR_ICAL_VERSION', '0');
	echo '<br />key:'.AMR_ICAL_VERSION.'+'.AMR_ICAL_LIST_VERSION.'#'.PHP_VERSION.'-'
	.get_bloginfo('version').'+'.get_option( 'blog_charset' ).'+'.mb_internal_encoding();
	echo '@'.ini_get("memory_limit");
	echo ' time:'.ini_get('max_execution_time').' seconds';
}



$utczobj = timezone_open('UTC');

/* set to empty string for concise code */
if (!defined('AMR_NL')) define('AMR_NL',"\n" );
if (!defined('AMR_TB')) define('AMR_TB',"\t" );

define('AMR_EVENTS_CACHE_TTL', 60 * 20);  //  20 mins
define('ICAL_EVENTS_CACHE_TTL', 24 * 60 * 60);  // 1 day
define('AMR_MAX_REPEATS', 1000); /* if someone wants to repeat something very frequently from some time way in the past, then may need to increase this */


$amr_ical_image_settings = get_option('amr-ical-images-to-use');
if (empty($amr_ical_image_settings)) {
	$suffix = '_16';
}
else {
	$size = (isset ($amr_ical_image_settings['images_size']) ? $amr_ical_image_settings['images_size'] : '16');
	if (in_array($size, array('16', '32') ))	$suffix = '_'.$size;
	else $suffix = '_16';
}

define('TIMEZONEIMAGE',			'timezone'.$suffix.'.png');
define('MAPIMAGE',				'map'.$suffix.'.png');
define('CALENDARIMAGE',			'calendar'.$suffix.'.png');
define('CALENDARADDTOIMAGE',	'calendar_add'.$suffix.'.png');
define('CALENDARADDSERIESIMAGE','calendar_link'.$suffix.'.png');
define('ADDTOGOOGLEIMAGE',		'addtogoogle'.$suffix.'.png');
define('REFRESHIMAGE',			'arrow_refresh'.$suffix.'.png');

 if ( ! defined( 'WP_PLUGIN_URL' ) )
       define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins/' );

if ( ! defined( 'WP_PLUGIN_DIR' ) )
       define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins/' );

$x = str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

$url = WP_PLUGIN_URL.'/amr-ical-events-list/';
$dir = WP_PLUGIN_DIR.'/amr-ical-events-list/';

define('ICALLISTPLUGINURL', $url);
define('ICALLISTPLUGINDIR', $dir);
define('ICALSTYLEURL', $url.'css/icallist.css');
define('ICALSTYLEFILE', $dir.'css/icallist.css');
define('ICAL_EDITSTYLEFILE', $dir.'css/a-yours.css');
define('ICALSTYLEPRINTURL', $url.'css/icalprint.css');
define('AMRICAL_ABSPATH', $url);
define('IMAGES_LOCATION', AMRICAL_ABSPATH.'images/');
$uploads = wp_upload_dir();
//define('ICAL_EVENTS_CACHE_LOCATION',path_join( ABSPATH, get_option('upload_path')));  /* do what wordpress does otherwise weird behaviour here - some folks already seem to have the abs path there. */
define('ICAL_EVENTS_CACHE_LOCATION',$uploads['basedir']);
define('ICAL_EVENTS_CSS_DIR',ICAL_EVENTS_CACHE_LOCATION.'/css/'); /* where to store custom css so does not get overwritten */
define('ICAL_EVENTS_CSS_URL',$uploads['baseurl'].'/css/'); /* where to store custom css so does not get overwritten */
define('ICAL_EVENTS_CACHE_DEFAULT_EXTENSION','ics');

$amr_wkst = ical_get_weekstart();
$amr_validrepeatablecomponents = array ('VEVENT', 'VTODO', 'VJOURNAL', 'VFREEBUSY', 'VTIMEZONE');
$amr_validrepeatableproperties = array (   // properties that may have multiple entries either in the meta or the icsfile
		'ATTACH', 'ATTENDEE',
		'CATEGORIES','COMMENT','CONTACT','CLASS' ,
		'DESCRIPTION', 'DAYLIGHT',
		'EXDATE','EXRULE',
		'FREEBUSY',
		'RDATE', 'RSTATUS','RELATED','RESOURCES','RRULE',
		//'SEQ',  
		'SUMMARY', 'STATUS', 'STANDARD',
		'TZOFFSETTO','TZOFFSETFROM',
		'URL',
		'XPARAM', 'X-PROP');
$amr_validrepeatableproperties = apply_filters('amr_valid_repeatable_properties',$amr_validrepeatableproperties);

$dateformat = str_replace(' ','\&\n\b\s\p\;', get_option('date_format'));

$amr_formats = array (
		'Time' => str_replace(' ', '\&\n\b\s\p\;',get_option('time_format')),
		'Day' => 'D, '.$dateformat,
//		'Time' => '%I:%M %p',
//		'Day' => '%a, %d %b %Y',
//		'Month' => '%b, %Y',		/* %B is the full month name */
		'Month' => 'F,Y',
		'Year' => 'Y',
		'Week' => '\W\e\e\k W',
//		'Timezone' => 'T',	/* Not accurate enough, leave at default */
		'DateTime' => $dateformat.' '.get_option('time_format')
//		'DateTime' => '%d-%b-%Y %I:%M %p'   /* use if displaying date and time together eg the original fields, */
		);
		
/* used for admin field sizes */
$amr_csize = array('Column' => '2', 'Order' => '2', 'Before' => '40', 'After' => '40', 'ColHeading' => '10');
/* the default setup shows what the default display option is */
$amr_admin_col_head = array (  // Dummy for translation
	'Column' 	=> __('Column','amr-ical-events-list'),
	'Order' 	=> __('Order','amr-ical-events-list'),
	'Before' 	=> __('Before','amr-ical-events-list'),
	'After' 	=> __('After','amr-ical-events-list'),
	);

}	
	
function amr_getTimeZone($offset) {
 $timezones = array(
  '-12'=>'Pacific/Kwajalein',
  '-11'=>'Pacific/Samoa',
  '-10'=>'Pacific/Honolulu',
		'-9.5'=>'Pacific/Marquesas',
  '-9'=>'America/Juneau',
  '-8'=>'America/Los_Angeles',
  '-7'=>'America/Denver',
  '-6'=>'America/Mexico_City',
  '-5'=>'America/New_York',
		'-4.5'=>'America/Caracas',
  '-4'=>'America/Manaus',
  '-3.5'=>'America/St_Johns',
  '-3'=>'America/Argentina/Buenos_Aires',
  '-2'=>'Brazil/DeNoronha',
  '-1'=>'Atlantic/Azores',
  '0'=>'Europe/London',
  '1'=>'Europe/Paris',
  '2'=>'Europe/Helsinki',
  '3'=>'Europe/Moscow',
  '3.5'=>'Asia/Tehran',
  '4'=>'Asia/Baku',
  '4.5'=>'Asia/Kabul',
  '5'=>'Asia/Karachi',
  '5.5'=>'Asia/Calcutta',
		'5.75'=>'Asia/Katmandu',
  '6'=>'Asia/Colombo',
		'6.5'=>'Asia/Rangoon',
  '7'=>'Asia/Bangkok',
  '8'=>'Asia/Singapore',
  '9'=>'Asia/Tokyo',
  '9.5'=>'Australia/Darwin',
  '10'=>'Pacific/Guam',
  '11'=>'Australia/Sydney',
		'11.5'=>'Pacific/Norfolk',
  '12'=>'Asia/Kamchatka',
		'13'=>'Pacific/Enderbury',
		'14'=>'Pacific/Kiritimati'
 );
	$intoffset = intval($offset); /*  to cope with +01.00 */
	$stroffset = strval($intoffset);
	if (isset($timezones[$stroffset])) return ($timezones[$stroffset]);
		else return false;
	}
	/* ---------------------------------------------------------------------------*/
function amr_set_helpful_descriptions () { // used in the admin screen

	$descriptions = array (
	'X-WR-CALNAME'	=> '',
	'X-WR-CALDESC'	=> '',
	'X-WR-TIMEZONE'	=> '',
	'icsurl'		=> '',
	'addtogoogle' 	=> __('A link to allow user to add the whole calendar to their google calendar.','amr-ical-events-list'),
	'icalrefresh' 	=> __('A link to allow user to refetch an ics file','amr-ical-events-list'),
	'LAST-MODIFIED' => __('The time the ics file was last modified','amr-ical-events-list'),

	'SUMMARY'=> 		__('WordPress post title or ICS SUMMARY','amr-ical-events-list'),
	'DESCRIPTION'=> 	__('WordPress content or ICS description','amr-ical-events-list'),
	'excerpt'=> 		__('WordPress excerpt','amr-ical-events-list'),
	'postthumbnail'=> 	__('WordPress post thumbnail','amr-ical-events-list'),
	'LOCATION'=> 		__('Address','amr-ical-events-list'),
	'map'=> 			__('Link to map','amr-ical-events-list'),
	'addevent' => 		__('Link to add event to a google calendar','amr-ical-events-list'),
	'subscribeevent' => __('Link to single event ics file','amr-ical-events-list'),
	'subscribeseries' => __('Link to recurring event series ics file','amr-ical-events-list'),
	'GEO'=> 			__('The latitude and longitude','amr-ical-events-list'),
	'ATTACH'=> 			__('Links to specified ATTACHMENTS (ics)','amr-ical-events-list'),

	'CATEGORIES'=> 		__('WordPress or ics file categories ','amr-ical-events-list'),
	'CLASS'=> 			__('ics class','amr-ical-events-list'),
	'COMMENT'=> 		__('ics comment','amr-ical-events-list'),
//	'PERCENT-COMPLETE'=> __('','amr-ical-events-list'),
	'PRIORITY'=> 		__('ics event priority','amr-ical-events-list'),
//	'RESOURCES'=> 		__('','amr-ical-events-list'),
	'STATUS'=> 			__('ics event status','amr-ical-events-list'),
	'EventDate' => 		__('The date of instance of a repeating date, or the event date','amr-ical-events-list'),
	'StartTime' => 		__('The time of instance of a repeating date, or the event date','amr-ical-events-list'),
	'EndDate' => 		__('The date an event instance ends.  Blank if same as event date ','amr-ical-events-list'),
	'EndTime' => 		__('The time an event instance ends. ','amr-ical-events-list'),
	'DTSTART'=> 		__('The original or first event date of a recurring series','amr-ical-events-list'),
//		'age'=> $dfalse,
	'DTEND'=> 		__('The original or first event\'s end date of a recurring series','amr-ical-events-list'),
	'DUE'=> 		__('The due date of a task.','amr-ical-events-list'),
	'DURATION'=> 	__('The duration of an event.','amr-ical-events-list'),
	'allday' => 	__('Says "all day" (translated) if the event has full days.','amr-ical-events-list'),
	'COMPLETED'=> 	__('If a task is completed.','amr-ical-events-list'),
	'FREEBUSY'=> 	__('Show busy (translated) if the freebusy component is in use.','amr-ical-events-list'),

//	'TRANSP'=> 		'',
//	'declined' => 		__('Users who have declined.','amr-ical-events-list'),
	'rsvp' => 			__('Users who have accepted.','amr-ical-events-list'),
//	'rsvpwithcomment' => __('Form to rsvp with comment.','amr-ical-events-list'),
	'register' => __('Add register button. Registration settings can be set as global defaults or per event.','amr-ical-events-list'),
	'going_ornot_ormaybe' => __('Links to indicate if attending.','amr-ical-events-list'),
	'CONTACT'=> 		__('The contact person if available.','amr-ical-events-list'),
	'ORGANIZER'=> 		__('The author of the event.','amr-ical-events-list'),
	'ATTENDEE'=> 		__('Users who are attending.','amr-ical-events-list'),
	'RECURRENCE-ID'=> 	__('The unique id of a recurrence instance or exception.','amr-ical-events-list'),
	'RELATED-TO'=> 		'',
	'URL'=> 			__('The events url as provided by ics file, or the wordpress event permalink.','amr-ical-events-list'),
	'UID'=> 			__('The unique identifier of the event.','amr-ical-events-list'),
	'EXDATE'=> 	__('Dates excluded from the recurring series','amr-ical-events-list'),
	'EXRULE'=> 	__('Exclusion rule - no longer in spec','amr-ical-events-list'),
	'RDATE'=> 	__('Individual dates on which event is to be repeated','amr-ical-events-list'),
	'RRULE'=> 	__('The rule for the recurrence of the event.','amr-ical-events-list'),
	'ACTION'=> __('Alarm action.','amr-ical-events-list'),
	'REPEAT'=> __('Alarm repeat.','amr-ical-events-list'),
	'TRIGGER'=> __('Alarm trigger.','amr-ical-events-list'),
	'CREATED'=> __('Date event created.','amr-ical-events-list'),
	'DTSTAMP'=> __('Date event published.','amr-ical-events-list'),
	'SEQUENCE'=> __('Modification level of event.','amr-ical-events-list'),
	'LAST-MODIFIED' => __('Date event last modified.','amr-ical-events-list'),
	'VEVENT'=> 	__('Events in an ics file','amr-ical-events-list'),
	'VFREEBUSY'=> __('Items in an ics file that indicate busy or available time slots','amr-ical-events-list'),
	'VTODO'=> __('Todo Task Items in an ics file','amr-ical-events-list'),
	'VJOURNAL'=> __('Journal notes in an ics file - no date or time','amr-ical-events-list'),

);


	return ($descriptions);
}
/* ---------------------------------------------------------------------------*/
function amr_define_possible_groupings () {
	$taxonomies = amr_define_possible_taxonomies ();
	
	foreach ($taxonomies as $i=>$taxo) { 
		$amr_groupings[$taxo] = false;
	}	
	
	$amr_groupings = array_merge ($amr_groupings, array (
			"Year" => false,
			"Quarter" => false,
			"Astronomical Season" => false,
			"Traditional Season" => false,
			"Western Zodiac" => false,
			"Month" => false,
			"Week" => false,
			"Day"=> false
			));
	return ($amr_groupings);		
}
/* ---------------------------------------------------------------------------*/
function amr_define_possible_taxonomies () {
	// check if we have any taxonomies that we may wish to assign an event to
	$taxonomies = get_taxonomies();
	$excluded = array ('nav_menu','link_category', 'post_format') ;
	foreach ($taxonomies as $i=>$tax) {
		  if (!(in_array($tax, $excluded))) $eventtaxonomies[] = $tax;
		}
	return ($eventtaxonomies);	
}
/* ---------------------------------------------------------------------------*/
function amr_set_defaults_for_datetime() {
	global $amr_globaltz;
	global $ical_timezone;

		if (($a_tz = get_option ('timezone_string') ) and (!empty($a_tz))) {
				$amr_globaltz = timezone_open($a_tz);
				//date_default_timezone_set($a_tz);
				If (isset($_REQUEST['tzdebug'])) {	echo '<br />Tz string:'.$a_tz;}
			}
		else {
			
			if (($gmt_offset = get_option ('gmt_offset')) and (!(is_null($gmt_offset))) and (is_numeric($gmt_offset))) {
				$a_tz = amr_getTimeZone($gmt_offset);
				$amr_globaltz = timezone_open($a_tz);
				//date_default_timezone_set($a_tz);
				if (isset($_REQUEST['tzdebug'])) {	echo '<h2>Found gmt offset in wordpress options:'.$gmt_offset.'</h2>';}
			}
			else {
				if (isset($_REQUEST['tzdebug'])) {	echo '<h2>Using php default for timezone</h2>';}
				$amr_globaltz = timezone_open(date_default_timezone_get()); // this will give UTC as wordpres  ALWAYS uses UTC
			}
		}
	if (empty($amr_globaltz)) {
		echo '<br />Error getting and setting global timezone either from wp or the default php  ';
	}
	$ical_timezone = $amr_globaltz;  // usedin amr-events apparently - can we rationalise sometime?
}
/* ---------------------------------------------------------------------------*/
function amr_set_defaults() {
	global $amr_calprop;
	global $amr_colheading;
	global $amr_compprop;
	global $amr_groupings;
	global $amr_components;
	global $amr_limits,$amr_listtype;
	global $amr_formats;
	global $amr_general;
	global $amr_globaltz;
	global $ical_timezone;
	global $eventtaxonomies;
	global $amr_options,$locale;
	
	$amr_listtype = '1'; // global default
	$amr_options = array (
			'ngiyabonga' => false,
			'own_css' => false,
			'feed_css' => true,
			'cssfile' => ICALSTYLEURL,//'icallist.css',
			'date_localise' => 'amr',
			// do NOT translate here, else either saved texts overwrite translations or saved translations prevent default text being translated.
			'noeventsmessage' => 'No events found within criteria',
			'lookmoremessage' => 'Look for more',
			'lookprevmessage' => 'Look for previous','amr-ical-events-list',
			'resetmessage' => 'Reset','amr-ical-events-list',
			'freebusymessage' => '&#10006;'
			);
			
		// if they don't have the gettext function the translation scanners will not pick the strings up for inclusion in the .pot/.po file	
	$fakeforautolangtranslation = array (
			'noeventsmessage' => __('No events found within criteria','amr-ical-events-list'),
			'lookmoremessage' => __('Look for more','amr-ical-events-list'),
			'lookprevmessage' => __('Look for previous','amr-ical-events-list'),
			'resetmessage' => __('Reset','amr-ical-events-list'),	
			'freebusymessage' => __('Busy','amr-ical-events-list'),
			__("Year",'amr-ical-events-list'),
			__("Quarter",'amr-ical-events-list'),
			__("Astronomical Season",'amr-ical-events-list') ,
			__("Traditional Season",'amr-ical-events-list'),
			__("Western Zodiac",'amr-ical-events-list'),
			__("Month",'amr-ical-events-list'),
			__("Week",'amr-ical-events-list') ,
			__("Day",'amr-ical-events-list')
			);		


	if (defined('AMR_ICAL_VERSION'))	
		$amr_options['ngiyabonga']	= true; //do not show credit link
	$alreadyhave = false;
//	if ($locale === 'en_US' )  $amr_options['date_localise'] = 'none';   // v4.0.9 commented out - multi lingual situations may have en as base, but will need localisation
//	else 	
	$amr_options['date_localise'] = 'wp';
	//
	amr_set_defaults_for_datetime();

	$amr_general = array (
			'name' 				=> __('Default','amr-ical-events-list'),
			'Description'		=> __('A default calendar list. This one set to tables with lists in the cells.  Usually needs the css file enabled. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list'),
			"Default Event URL" => '',
			'ListHTMLStyle'		=> 'table',
			'customHTMLstylefile' => ''
			);
	$amr_limits = array (
			"events" 	=> 30,
			"days" 		=> 90,
			"cache" 	=> 24, /* hours */
			"eventscache" => 0.5);  // must not set start here
	$amr_components = array (
			"VEVENT" 	=> true,
			"VTODO" 	=> true,
			"VJOURNAL" 	=> false,
			"VFREEBUSY" => true
	//		"VTIMEZONE" => false /* special handling required if we want to process this - for now we are going to use the php definitions rather */
			);

			
			
	$amr_groupings = amr_define_possible_groupings();

	$amr_colheading = array (
		'1' => __('When','amr-ical-events-list'),
		'2' => __('What', 'amr-ical-events-list'),
		'3' => __('Where', 'amr-ical-events-list')
		);

	$dfalse 	= array('Column' => 0, 'Order' => 999, 'Before' => '', 'After' => '');
	$dtrue 		= array('Column' => 1, 'Order' => 1, 'Before' => '', 'After' => '');
	$dtrue2 	= array('Column' => 2, 'Order' => 1, 'Before' => '', 'After' => '');


	// check if we have any taxonomies that we may wish to assign an event to
	$eventtaxonomies = amr_define_possible_taxonomies ();
	foreach ($eventtaxonomies as $i=>$tax) {
		 $eventtaxonomiesprop[$tax] = array('Column' => 2, 'Order' => 200, 'Before' => '', 'After' => '&nbsp;');
		}
		
	$amr_calprop = array (
			'X-WR-CALNAME'	=> array('Column' => 1, 'Order' => 1, 'Before' => '', 'After' => ''),
			'X-WR-CALDESC'	=> $dfalse,
			'X-WR-TIMEZONE'	=> array('Column' => 0, 'Order' => 40, 'Before' => '', 'After' => ''),
			'icsurl'		=> array('Column' => 2, 'Order' => 20, 'Before' => '', 'After' => ''),
			'addtogoogle' 	=> array('Column' => 2, 'Order' => 10, 'Before' => '', 'After' => ''),
			'icalrefresh' 	=> array('Column' => 0, 'Order' => 30, 'Before' => '', 'After' => ''),
			/* for linking to the ics file, not intended as a display field really unless you want a separate link to it, intended to sit behind name, with desc as title */
			'LAST-MODIFIED' => $dtrue
			//		'CALSCALE'=> $dfalse,
			//		'METHOD'=> $dfalse,
			//		'PRODID'=> $dfalse,
			//		'VERSION'=> $dfalse,
			//		'X-WR-RELCALID'=> $dfalse
			);

	/* NB need to switch some field s on for initial plugin view.  This will be common default for all, then some are customised separately */
	$amr_compprop = array
		(
		'Descriptive' =>
	 	array_merge (
			array (
			'SUMMARY'=> 		array('Column' => 2, 'Order' => 10, 'Before' => '<b>', 'After' => '</b>'),
			'DESCRIPTION'=> 	array('Column' => 2, 'Order' => 20, 'Before' => '', 'After' => ''),
			'excerpt'=> 		array('Column' => 0, 'Order' => 30, 'Before' => '<br />', 'After' => ''),
			'postthumbnail'=> 	array('Column' => 0, 'Order' => 35, 'Before' => '<br />', 'After' => ''),
			'LOCATION'=> 		array('Column' => 2, 'Order' => 41, 'Before' => '', 'After' => ''),
			'map'=> 			array('Column' => 2, 'Order' => 40, 'Before' => '', 'After' => ''),
			'addevent' => 		array('Column' => 2, 'Order' => 1, 'Before' => '', 'After' => ''),
			'subscribeevent' => array('Column' => 2, 'Order' => 2, 'Before' => '', 'After' => ''),
			'subscribeseries' => array('Column' => 2, 'Order' => 3, 'Before' => '', 'After' => ''),
			'GEO'=> 			$dfalse,
			'ATTACH'=> 			array('Column' => 2, 'Order' => 150,
								'Before' => __('More info: ','amr-ical-events-list'),
								'After' => '<br />'),
			'CATEGORIES'=> 		array('Column' => 2, 'Order' => 200, 'Before' => '', 'After' => ''),
			'CLASS'=> 			array('Column' => 0, 'Order' => 210, 'Before' => '', 'After' => ''),
			'COMMENT'=> 		$dfalse,
			'PERCENT-COMPLETE'=> $dfalse,
			'PRIORITY'=> 		array('Column' => 0, 'Order' => 220, 'Before' => '', 'After' => ''),
			'RESOURCES'=> 		$dfalse,
			'STATUS'=> 			array('Column' => 0, 'Order' => 230, 'Before' => '', 'After' => ''),
			),
			$eventtaxonomiesprop),
			'Date and Time' => array (
			'EventDate' => 		array ('Column' => 1, 'Order' => 1, 'Before' => '', 'After' => ''), /* the instance of a repeating date */
			'StartTime' => 		array('Column' => 1, 'Order' => 2, 'Before' => ' ', 'After' => ' '),
			'EndDate' => 		array('Column' => 1, 'Order' => 3, 'Before' => __(' to','amr-ical-events-list').'&nbsp;', 'After' => ''),
			'EndTime' => 		array('Column' => 1, 'Order' => 4, 'Before' => ' ', 'After' => ''),
			'DTSTART'=> 		$dfalse,
	//		'age'=> $dfalse,
			'DTEND'=> 		$dfalse,
			'DUE'=> 		$dfalse,
			'DURATION'=> 	array('Column' => 0, 'Order' => 50, 'Before' => '', 'After' => ''),
			'allday' => 	array('Column' => 1, 'Order' => 4, 'Before' => '', 'After' => ''),
			'COMPLETED'=> 	$dfalse,
			'FREEBUSY'=> 	$dfalse,
			'TRANSP'=> 		$dfalse),

	//	'Time Zone' => array (
	//		'TZID'=> $dtrue,  /* but only show if different from calendar TZ */
	//		'TZNAME'=> $dfalse,
	//		'TZOFFSETFROM'=> $dfalse,
	//		'TZOFFSETTO'=> $dfalse,
	//		'TZURL'=> $dfalse),
		'Relationship' => array (
			'CONTACT'=> 		array('Column' => 0, 'Order' => 350, 'Before' => '', 'After' => ''),
			'ORGANIZER'=> 		array('Column' => 0, 'Order' => 360, 'Before' => '', 'After' => ''),
			'ATTENDEE'=> 		array('Column' => 0, 'Order' => 370, 'Before' => '', 'After' => ''),
			'RECURRENCE-ID'=> 	$dfalse,
			'RELATED-TO'=> 		$dfalse,
			'URL'=> 			array('Column' => 0, 'Order' => 150,
				'Before' => '',
				'After' => ''),
			'UID'=> 			$dfalse
			),
		'Recurrence' => array (  /* in case one wants for someone reason to show the "repeating" data, need to create a format rule for it then*/
			'EXDATE'=> 	$dfalse,
			'EXRULE'=> 	$dfalse,
			'RDATE'=> 	$dfalse,
			'RRULE'=> 	$dfalse
		),
		'Alarm' => array (
			'ACTION'=> $dfalse,
			'REPEAT'=> $dfalse,
			'TRIGGER'=> $dfalse),
		'Change Management'	=> array ( /* optional and/or for debug purposes */
			'CREATED'=> $dfalse,
			'DTSTAMP'=> $dfalse,
			'SEQUENCE'=> $dfalse,
			'LAST-MODIFIED' => $dfalse
			)
		);
//		if (function_exists ('amr_indicate_attendance')) {    //pluggable does not exist yet

//		}
		if (function_exists ('amr_rsvp')) {
		$amr_compprop['Relationship']['declined'] //list
		= array('Column' => 0, 'Order' => 400, 'Before' => '', 'After' => '');
		$amr_compprop['Relationship']['rsvp'] // action form
		= array('Column' => 0, 'Order' => 410, 'Before' => '', 'After' => '');
		$amr_compprop['Relationship']['rsvpwithcomment'] 
		= array('Column' => 0, 'Order' => 420, 'Before' => '', 'After' => '');
		$amr_compprop['Relationship']['going_ornot_ormaybe'] 
			= array('Column' => 0, 'Order' => 430, 'Before' => '', 'After' => '');
		$amr_compprop['Relationship']['total_attending'] 
		= array('Column' => 0, 'Order' => 510, 'Before' => '', 'After' => '');
		$amr_compprop['Relationship']['total_maybe'] 
		= array('Column' => 0, 'Order' => 510, 'Before' => '', 'After' => '');
		$amr_compprop['Relationship']['total_declined'] 
		= array('Column' => 0, 'Order' => 510, 'Before' => '', 'After' => '');
		}
//		

		
		for ($i = 1; $i <= 13; $i++)  { /* setup some list type defaults if we have empty list type arrays */
				$amr_options['listtypes'][$i] = new_listtype(); // set up basic
				$amr_options['listtypes'][$i] = customise_listtype( $i);  /* then tweak */
			}
	
//		add_option('amr-ical-events-list', $amr_options);  // hmm what to do - if we autosave, then they do noyt ickup new defaults automatically
			
}
/* -------------------------------------------------------------------------------------------------------------*/
function amr_ical_showmap ($text) { /* the address text */
	global $amr_options;
		$t1 = __('Show in Google map','amr-ical-events-list');
		if (isset ($amr_options['no_images']) and $amr_options['no_images']) $t3 = $t1;
		else $t3 = '<img src="'.IMAGES_LOCATION.MAPIMAGE.'" alt="'.	$t1	.'" class="amr-bling" />';
	/* this is used to determine what should be done if a map is desired - a link to google behind the text ? or some thing else  */

	return('<a class="hrefmap" href="http://maps.google.com/maps?q='
		.str_replace(' ','%20',($text)).'" target="_BLANK"'   //google wants unencoded
		.' title="'.__('Show location in Google Maps','amr-ical-events-list').'" >'.$t3.'</a>');
	}
/* -------------------------------------------------------------------------------------------------------------*/
/* This is used to tailor the multiple default listing options offered.  A new listtype first gets the common default */
function customise_listtype($i)	{ /* sets up some variations of the default list type*/
	global $amr_options;

	$amr_options['listtypes'][$i]['Id'] = $i;
	switch ($i)	{
		case 1:
			$amr_options['listtypes'][$i]['format']['Day']='D'.'\&\n\b\s\p\;'.'jS'.'\&\n\b\s\p\;'.'M';
			break;
		case 2: {
			$amr_options['listtypes'][$i]['general']['name'] =__('On Tour','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['Description']=__('Default setting uses the original table with lists in the cells. It is grouped by month. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='table';
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['LOCATION']['Column'] = 2;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['DESCRIPTION']['Column'] = 2;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY']['Column'] = 2;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['addevent']['Column'] = 3;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['subscribeevent']['Column'] = 3;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['subscribeseries']['Column'] = 3;
			$amr_options['listtypes'][$i]['heading']['2'] = __('Venue','amr-ical-events-list');
			$amr_options['listtypes'][$i]['heading']['3'] = __('Description','amr-ical-events-list');
			break;
			}
		case 3: {
			$amr_options['listtypes'][$i]['general']['name']=__('Timetable','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['Description']=__('Default setting uses the original table with lists in the cells. It is grouped by day. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='table';
			foreach ($amr_options['listtypes'][$i]['grouping'] as $g=>$v) {$amr_options['listtypes'][$i]['grouping'][$g] = false;}
			$amr_options['listtypes'][$i]['grouping']['Day'] = true;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']['Column'] = 0;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndDate']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']['Before'] = '&#32;';

			$amr_options['listtypes'][$i]['compprop']['Descriptive']['addevent']['Order'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['subscribeevent']['Order'] = 2;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['subscribeseries']['Order'] = 3;
			$amr_options['listtypes'][$i]['heading']['2'] = __('Date','amr-ical-events-list');
			$amr_options['listtypes'][$i]['heading']['2'] = __('Class','amr-ical-events-list');
			$amr_options['listtypes'][$i]['heading']['3'] = __('Room','amr-ical-events-list');
			$amr_options['listtypes'][$i]['format']['Day']='l, jS M';
			break;
			}
		case 4: {
			$amr_options['listtypes'][$i]['general']['name']=__('Widget','amr-ical-events-list'); /* No groupings, minimal */
			$amr_options['listtypes'][$i]['general']['Description']=__('The new default setting for widgets uses lists for the table rows. Good for themes that cannot cope with tables in the sidebar. No grouping. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');

			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='HTML5';
			$amr_options['listtypes'][$i]['format']['Day']='M'.'\&\n\b\s\p\;'.'j';
			$amr_options['listtypes'][$i]['limit'] = array (	"events" => 10,	"days" 	=> 90,"cache" 	=> 24);  /* hours */
			foreach ($amr_options['listtypes'][$i]['grouping'] as $g => $v) {$amr_options['listtypes'][$i]['grouping'][$g] = false;}
			/* No calendar properties for widget - keep it minimal */
			foreach ($amr_options['listtypes'][$i]['calprop'] as $g => $v)
				{$amr_options['listtypes'][$i]['calprop'][$g]['Column'] = 0;}
			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v)
				foreach ($v as $g2 => $v2) {$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] = 0;}
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndDate']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndTime']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY'] = array('Column' => 1, 'Order' => 10, 'Before' => '<br />', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['DESCRIPTION'] = array('Column' => 0, 'Order' => 20, 'Before' => '<br />', 'After' => '');
			$amr_options['listtypes'][$i]['heading']['1'] = $amr_options['listtypes'][$i]['heading']['2'] = $amr_options['listtypes'][$i]['heading']['3'] = '';
			break;
			}
		case 5: {
			$amr_options['listtypes'][$i]['general']['name']=__('HTML5 Exp 1','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['Description']= __('Table style aiming to use html5 tags, but still within a table structure to allow columns. One cannot have two levels of grouping with this option as tbody cannot be nested. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='HTML5table';
			$amr_options['listtypes'][$i]['format']['Day']='j'.'\&\n\b\s\p\;'.'M';
			$amr_options['listtypes'][$i]['grouping']['Day'] = false;
			$amr_options['listtypes'][$i]['grouping']['Month'] = true;
			$amr_options['listtypes'][$i]['heading']['1'] = '';
			$amr_options['listtypes'][$i]['heading']['2'] = '';
			$amr_options['listtypes'][$i]['heading']['3'] = '';
			$amr_options['listtypes'][$i]['calprop']['X-WR-CALNAME']
				= array('Column' => 0, 'Order' => 10, 'Before' => '', 'After' => '&#32;'); //space
			$amr_options['listtypes'][$i]['calprop']['X-WR-CALDESC']
				= array('Column' => 0, 'Order' => 12, 'Before' => ' - ', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']
			= array('Column' => 1, 'Order' => 10, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']
			= array('Column' => 1, 'Order' => 12, 'Before' => '&nbsp;', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndDate']
			= array('Column' => 1, 'Order' => 14, 'Before' => '&nbsp;', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndTime']
			= array('Column' => 1, 'Order' => 16, 'Before' => '&nbsp;', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY']
			= array('Column' => 1, 'Order' => 18, 'Before' => '<h4>', 'After' => '</h4>');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['DESCRIPTION']
				= array('Column' => 1, 'Order' => 30, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['LOCATION']
				= array('Column' => 1, 'Order' => 40, 'Before' => '<address>', 'After' => '</address>');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['addevent']['Column'] = 0;

			break;
			}
		case 6: {
			$amr_options['listtypes'][$i]['general']['name']=__('HTML5 Exp 2','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['Description']=__('An HTML5 test option that tries to be leaner. You can have two level of grouping with this option. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='HTML5';

			$amr_options['listtypes'][$i]['calprop']['X-WR-CALNAME']
				= array('Column' => 0, 'Order' => 10, 'Before' => '<b>', 'After' => '</b>');
			$amr_options['listtypes'][$i]['calprop']['X-WR-CALDESC']
				= array('Column' => 0, 'Order' => 12, 'Before' => ' - ', 'After' => '');
			$amr_options['listtypes'][$i]['grouping']['Day'] = true;
			$amr_options['listtypes'][$i]['grouping']['Month'] = false;
			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v) {
				foreach ($v as $g2 => $v2)	{
					if ($amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] <> 0)
						$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] = 1;
					$amr_options['listtypes'][$i]['compprop'][$g][$g2]['After'] = '&nbsp;';
					$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Before'] = '';
				}
			}
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY']
				= array('Column' => 1, 'Order' => 18, 'Before' => '<h3>', 'After' => '</h3>');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']
				= array('Column' => 0, 'Order' => 10, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']['Order'] = 12;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndDate']
			= array('Column' => 0, 'Order' => 14, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndTime']['Order'] = 16;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['subscribeevent']['Order'] = 20;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['addevent']['Order'] = 22;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['LOCATION']
				= array('Column' => 1, 'Order' => 50, 'Before' => '<address>', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['map']
				= array('Column' => 1, 'Order' => 51,'Before' => '&nbsp;', 'After' => '</address>');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['DESCRIPTION']
				= array('Column' => 1, 'Order' => 100, 'Before' => '', 'After' => '');


			$amr_options['listtypes'][$i]['heading']['1'] = '';
			$amr_options['listtypes'][$i]['heading']['2'] = '';
			$amr_options['listtypes'][$i]['heading']['3'] = '';
			$amr_options['listtypes'][$i]['format']['Day'] = 'j'.'\&\n\b\s\p\;'.'S,'.'\&\n\b\s\p\;'.'l';

			break;
			}
		case 7: {

			$amr_options['listtypes'][$i]['general']['name']=__('EventInfo','amr-ical-events-list'); /* No groupings, minimal */
			$amr_options['listtypes'][$i]['general']['Description']=__('For displaying additional event info on posts created as events. The summary and description are switched off as these are the post title and the post content. Calendar properties and groupings are also not relevant. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['limit'] = array (	"events" => 10,	"days" 	=> 366,"cache" 	=> 24);  /* hours */
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='HTML5table';
			$amr_options['listtypes'][$i]['format']['Day']='l, j F Y';

			$amr_options['listtypes'][$i]['component']['VTODO'] = false;
			$amr_options['listtypes'][$i]['component']['VFREEBUSY'] = false;
			foreach ($amr_options['listtypes'][$i]['grouping'] as $g => $v) {$amr_options['listtypes'][$i]['grouping'][$g] = false;}
			/* No calendar properties for widget - keep it minimal */
			foreach ($amr_options['listtypes'][$i]['calprop'] as $g => $v)
				{$amr_options['listtypes'][$i]['calprop'][$g]['Column'] = 0;}
			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v)
				foreach ($v as $g2 => $v2) {$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] = 0;}
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']['Order'] = 12;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']['Order'] = 10;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']['Before'] = '';
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndDate']['Column'] = 0;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndTime']['Column'] = 0;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['LOCATION']['Order'] = 0;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['map']['Order'] = 0;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY']['Column'] = 0;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['addevent']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['addevent']['Order'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['subscribeevent']['Column'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['subscribeevent']['Order'] = 1;
			$amr_options['listtypes'][$i]['heading']['1'] = $amr_options['listtypes'][$i]['heading']['2'] = $amr_options['listtypes'][$i]['heading']['3'] = '';
			break;
			}
	case 8: {
			$amr_options['listtypes'][$i]['general']['name']=__('Small-Calendar','amr-ical-events-list'); /* No groupings, minimal */
			$amr_options['listtypes'][$i]['general']['Description']=__('The new default setting for calendar widgets. No grouping, No headings. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='smallcalendar';
			$amr_options['listtypes'][$i]['format']['Day']='j';
			$amr_options['listtypes'][$i]['format']['Week']='D'; // 3 letter day of week
			$amr_options['listtypes'][$i]['format']['Month']='M Y';
			$amr_options['listtypes'][$i]['limit'] = array (	"events" => 200,	"days" 	=> 31,"cache" 	=> 24 );  /* hours */

			foreach ($amr_options['listtypes'][$i]['grouping'] as $g => $v) {
				$amr_options['listtypes'][$i]['grouping'][$g] = false;}
			/* No calendar properties for widget - keep it minimal */
			foreach ($amr_options['listtypes'][$i]['calprop'] as $g => $v)
				{$amr_options['listtypes'][$i]['calprop'][$g]['Column'] = 0;}
			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v)
				foreach ($v as $g2 => $v2) {$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] = 0;}
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']
			= array('Column' => 0, 'Order' => 25, 'Before' => '&nbsp;', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']
			= array('Column' => 1, 'Order' => 26, 'Before' => '&nbsp;', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndDate']['Column'] = 0;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndTime']
			= array('Column' => 0, 'Order' => 30, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY']
			= array('Column' => 1, 'Order' => 20, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['DESCRIPTION']['Column'] = 0;
			$amr_options['listtypes'][$i]['heading']['1'] = $amr_options['listtypes'][$i]['heading']['2'] = $amr_options['listtypes'][$i]['heading']['3'] = '';
			break;
			}
		case 9: {
			$amr_options['listtypes'][$i]['general']['name']=__('Large-Calendar','amr-ical-events-list'); /* No groupings, minimal */
			$amr_options['listtypes'][$i]['general']['Description']= __('The new default setting for a large monthly calendar. No grouping, No headings. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is.','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='largecalendar';
			$amr_options['listtypes'][$i]['format']['Day']='M j';
			$amr_options['listtypes'][$i]['format']['Time']='G:i';
			$amr_options['listtypes'][$i]['format']['Month']='F Y';
			$amr_options['listtypes'][$i]['format']['Week']='l'; // lowercase l = full text date
			$amr_options['listtypes'][$i]['limit'] = array (	"events" => 200,	"days" 	=> 31,"cache" 	=> 24 );  /* hours */

			foreach ($amr_options['listtypes'][$i]['grouping'] as $g => $v) {$amr_options['listtypes'][$i]['grouping'][$g] = false;}
			/* No calendar properties for widget - keep it minimal */
//			foreach ($amr_options['listtypes'][$i]['calprop'] as $g => $v)
//				{$amr_options['listtypes'][$i]['calprop'][$g]['Column'] = 0;}
			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v)
				foreach ($v as $g2 => $v2) {$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] = 0;}
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']=
				array('Column' => 2, 'Order' => 10, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']
				= array('Column' => 2, 'Order' => 15, 'Before' => '&nbsp;', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndDate'] = 
				array('Column' => 2, 'Order' => 20, 'Before' => ' to ', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EndTime'] = 
				array('Column' => 2, 'Order' => 25, 'Before' => '&nbsp;', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY'] = array('Column' => 1, 'Order' => 1, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['DESCRIPTION']
			= array('Column' => 2, 'Order' => 1, 'Before' => '<div class="details">', 'After' => '</div>');
			$amr_options['listtypes'][$i]['heading']['1'] = $amr_options['listtypes'][$i]['heading']['2'] = $amr_options['listtypes'][$i]['heading']['3'] = '';
			break;
		}
		case 10: {
			$amr_options['listtypes'][$i]['general']['name']=__('Testing','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['Description']=__('A test option with lots of fields switched on. It has 2 levels of grouping - this is fine so long as the html in use can be nested. If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');

			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='breaks';
			
			foreach ($amr_options['listtypes'][$i]['grouping'] as $g => $v) {
				$amr_options['listtypes'][$i]['grouping'][$g] = false;
			}
			$amr_options['listtypes'][$i]['grouping']['Day'] = true;
			$amr_options['listtypes'][$i]['grouping']['Month'] = true;
			$amr_options['listtypes'][$i]['compprop'][] = apply_filters('amr_ics_component_properties', array()); 
			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v) {
				foreach ($v as $g2 => $v2) {
				 	$amr_options['listtypes'][$i]['compprop'][$g][$g2]['After'] = '<br />';
					$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Before'] = '';
					if ($v2['Column'] == 0) {
						$amr_options['listtypes'][$i]['compprop'][$g][$g2]
						= array('Column' => 1, 'Order' => 99,
						'Before' => '<em>'.$g2.':</em> ',
						'After' => "<br />");
					}
					else {
						$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Before']
					= '<em>'.$g2.':</em> ';
						$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column']
					= 1;
					}

				}
			}


//----------------------------------------------------------------------------------------------------
			foreach ($amr_options['listtypes'][$i]['calprop'] as $g => $v) {
				$amr_options['listtypes'][$i]['calprop'][$g] =
				array('Column' => 3, 'Order' => 1, 'Before' => '', 'After' => '');
			}
			$amr_options['listtypes'][$i]['calprop']['X-WR-CALNAME']['Column'] = 1;
			$amr_options['listtypes'][$i]['calprop']['X-WR-CALDESC']['Column'] = 1;
			$amr_options['listtypes'][$i]['calprop']['X-WR-CALDESC']['Before'] = '&#32;';
			foreach ($amr_options['listtypes'][$i]['component'] as $g=>$v) {
				$amr_options['listtypes'][$i]['component'][$g] = true;
			}
			$amr_options['listtypes'][$i]['heading']['1'] = '';
			$amr_options['listtypes'][$i]['heading']['2'] = '';
			$amr_options['listtypes'][$i]['heading']['3'] = '';
			$amr_options['listtypes'][$i]['format']['Day'] = 'D, F j, Y';

			break;
		}
		case 11: {
			$amr_options['listtypes'][$i]['general']['name']=__('Weekly Horizontal','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['Description']=
			__('Like the large calendar, but different - grouped by week and the weeks continue across months.  Really 2 weeks should be displayed a time.','amr-ical-events-list')
			.__(' If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='weekscalendar';
			$amr_options['listtypes'][$i]['limit'] = array (	"events" => 100,	"days" 	=> 14,"cache" 	=> 24);  /* hours */
			$amr_options['listtypes'][$i]['format']['Time']='H:i';
			$amr_options['listtypes'][$i]['format']['Day']=
			'D'.'\&\n\b\s\p\;'.'j'; //'M';
			foreach ($amr_options['listtypes'][$i]['grouping'] as $g=>$v) {$amr_options['listtypes'][$i]['grouping'][$g] = false;}
			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v) {
				foreach ($v as $g2 => $v2) {
					$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] = 0;
				}
			}
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']
			= array('Column' => 1, 'Order' => 5, 'Before' => '', 'After' => ' ');

			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY']
			= array('Column' => 1, 'Order' => 20, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['DESCRIPTION']
			= array('Column' => 2, 'Order' => 1, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['DURATION']['Column']=2;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['LOCATION']
			= array('Column' => 2, 'Order' => 20, 'Before' => '', 'After' => '');

			$amr_options['listtypes'][$i]['heading']['1'] = $amr_options['listtypes'][$i]['heading']['2'] = $amr_options['listtypes'][$i]['heading']['3'] = '';


			break;
			}
		case 12: {
			$amr_options['listtypes'][$i]['general']['name']=__('Weekly Vertical','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['Description']=
			__('Grouped by day, but only showing 1 week. 3 columns, with excerpt not full description. No icons.','amr-ical-events-list')
			.__(' If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='table';
			$amr_options['listtypes'][$i]['limit'] = array (	"events" => 100,	"days" 	=> 7,"cache" 	=> 24 );  /* hours */
			$amr_options['listtypes'][$i]['format']['Time']='g:i'.'\&\n\b\s\p\;'.'a';
			$amr_options['listtypes'][$i]['format']['Day']=
			'D,j'.'\&\n\b\s\p\;'.'M'; //  to avoid tabel cell wrap
			foreach ($amr_options['listtypes'][$i]['grouping'] as $g=>$v) {$amr_options['listtypes'][$i]['grouping'][$g] = false;}
			$amr_options['listtypes'][$i]['grouping']['Day'] = true;

			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v) {
				foreach ($v as $g2 => $v2) {
					$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] = 0;
				}
			}
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']
			= array('Column' => 0, 'Order' => 4, 'Before' => '', 'After' => '&nbsp;');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']
			= array('Column' => 1, 'Order' => 5, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['allday']
			= array('Column' => 1, 'Order' => 6, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY']
			= array('Column' => 2, 'Order' => 20, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['excerpt']
			= array('Column' => 3, 'Order' => 1, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['DURATION']['Column']=3;
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['LOCATION']
			= array('Column' => 3, 'Order' => 20, 'Before' => '', 'After' => '');

			$amr_options['listtypes'][$i]['heading']['1'] = $amr_options['listtypes'][$i]['heading']['2'] = $amr_options['listtypes'][$i]['heading']['3'] = '';


			break;
			}	
			case 13: {
			foreach ($amr_options['listtypes'][$i]['calprop'] as $g => $v)
				{$amr_options['listtypes'][$i]['calprop'][$g]['Column'] = 0;}
			foreach ($amr_options['listtypes'][$i]['compprop'] as $g => $v) {
				foreach ($v as $g2 => $v2) {
					$amr_options['listtypes'][$i]['compprop'][$g][$g2]['Column'] = 0;
				}
			}
			$amr_options['listtypes'][$i]['general']['name']=__('Event Master','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['Description']=
			__('Grouped by category, intended to be used with no recurrences','amr-ical-events-list')
			.__(' If you configure it, I suggest changing this description to aid your memory of how/why it is configured the way that it is. ','amr-ical-events-list');
			$amr_options['listtypes'][$i]['general']['ListHTMLStyle']='HTML5table';
			$amr_options['listtypes'][$i]['limit'] = array (	"events" => 200,	"days" 	=> 365,"cache" 	=> 24 );  /* hours */
			$amr_options['listtypes'][$i]['format']['Time']='g:i'.'\&\n\b\s\p\;'.'a';
			$amr_options['listtypes'][$i]['format']['Day']=
			'D,j'.'\&\n\b\s\p\;'.'M'; //  to avoid tabel cell wrap
			foreach ($amr_options['listtypes'][$i]['grouping'] as $g=>$v) {$amr_options['listtypes'][$i]['grouping'][$g] = false;}
			$amr_options['listtypes'][$i]['grouping']['category'] = 1;
			$amr_options['listtypes'][$i]['compprop']['Descriptive']['SUMMARY']
			= array('Column' => 1, 'Order' => 10, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['EventDate']
			= array('Column' => 2, 'Order' => 10, 'Before' => 'Next: ', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['StartTime']
			= array('Column' => 3, 'Order' => 20, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['DURATION']
			= array('Column' => 4, 'Order' => 30, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['subscribeseries'] 
			= array('Column' => 5, 'Order' => 20, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['compprop']['Date and Time']['register'] 
			= array('Column' => 5, 'Order' => 10, 'Before' => '', 'After' => '');
			$amr_options['listtypes'][$i]['heading']['1'] 
			= $amr_options['listtypes'][$i]['heading']['2'] 
			= $amr_options['listtypes'][$i]['heading']['3'] 
			= '';


			break;
			}

	}
	
	//--- temp fix before re editing all above, to keep compatibility with older versions.. will eventually drop away the old version.
	
	$amr_options['listtypes'][$i]['compprop'] = amr_remove_array_level ($amr_options['listtypes'][$i]['compprop']);
// so now we have both versions - need to unset the other, but can leave for now.

	return ( $amr_options['listtypes'][$i]);
}
/* ---------------------------------------------------------------------*/
function new_listtype()	{
	global $amr_calprop,
	$amr_colheading,
	$amr_compprop,
	$amr_groupings,
	$amr_components,
	$amr_limits,
	$amr_formats,
	$amr_general;

	$amr_newlisttype = (array
		(
		'general' => $amr_general,
		'format' => $amr_formats,
		'heading' => $amr_colheading,
		'calprop' => $amr_calprop,
		'component' => $amr_components,
		'grouping' => $amr_groupings,
		'compprop' => $amr_compprop,
		'limit' => $amr_limits,
		'Id' => '1'
		)
	);
	return $amr_newlisttype;
	}
/* ---------------------------------------------------------------------*/
function Quarter ($D)
{ 	/* Quarters can be complicated.  There are Tax and fiscal quarters, and many times the tax and fiscal year is different from the calendar year */
	/* We could have used the function commented out for calendar quarters. However to allow for easier variation of the quarter definition. we used the limits concept instead */
	/* $D->format('Y').__(' Q ').(ceil($D->format('n')/3)); */
return date_season('Quarter', $D);
}
function Meteorological ($D)
{return date_season('Meteorological', $D);  }
function Astronomical_Season ($D)
{return date_season('Astronomical', $D);  }
function Traditional_Season ($D)
{return date_season('Traditional', $D);  }
function Western_Zodiac ($D){
return date_season('Zodiac', $D);  }
/* ---------------------------------------------------------------------*/
function date_season ($type='Meteorological',$D) {
	/* Receives ($Dateobject and returns a string with the Meterological season by default*/
	/* Note that the limits must be defined on backwards order with a seemingly repeated entry at the end to catch all */

	if (!(isset($D))) $D =  amr_newDateTime();
	$Y = amr_format_date('Y',$D);
 $limits ['Quarter']=array(

	/* for different quarters ( fiscal, tax, etc,) change the date ranges and the output here  */
		'/12/31'=> $Y.' Q1',
		'/09/31'=> $Y.' Q4',
		'/06/30'=> $Y.' Q3',
		'/03/31'=> $Y.' Q2',
		'/01/00'=> $Y.' Q1',
		);

   $limits ['Meteorological']=array(
		'/12/01'=>'N. Winter, S. Summer',
		'/09/01'=>'N. Fall, S. Spring',
		'/06/01'=>'N. Summer, S. Winter',
		'/03/01'=>'N. Spring, S. Autumn',
		'/01/00'=>'N. Winter, S. Summer'
		);

	$limits ['Astronomical']=array(
		'/12/21'=>'N. Winter, S. Summer',
		'/09/23'=>'N. Fall, S. Spring',
		'/06/21'=>'N. Summer, S. Winter',
		'/03/21'=>'N. Spring, S. Autumn',
		'/01/00'=>'N. Winter, S. Summer'
		);

	$limits ['Traditional']=array(
	/*  actual dates vary , so this is an approximation */
		'/11/08'=>'N. Winter, S. Summer',
		'/08/06'=>'N. Fall, S. Spring',
		'/06/05'=>'N. Summer, S. Winter',
		'/02/05'=>'N. Spring, S. Autumn',
		'/01/00'=>'N. Winter, S. Summer'
		);

	$limits ['Zodiac']=array(
	/*  actual dates vary , so this is an approximation */
		'/12/22'=>'Capricorn',
		'/11/22'=>'Sagittarius',
		'/10/23'=>'Scorpio',
		'/09/23'=>'Libra',
		'/08/23'=>'Virgo',
		'/07/23'=>'Leo',
		'/06/21'=>'Cancer',
		'/05/21'=>'Gemini',
		'/04/20'=>'Taurus',
		'/03/21'=>'Aries',
		'/02/19'=>'Pisces',
		'/01/20'=>'Aquarius',
		'/01/00'=>'Capricon',
		);

	/* get the current year */
   foreach ($limits[$type] AS $key => $value)
   {
	/* add the current year to the limit */
    $limit = $key;
	   $input = amr_format_date ('/m/d', $D);
		/* if date is later than limit, then return the current value, else continue to check the next limit */

    if ($input > $limit) {
			return $value;
	   }
   }
}
/*----------------------------------------------------------------------------------------*/
global	$gnu_freq_conv;
$gnu_freq_conv = array (
/* used to convert from ical FREQ to gnu relative items for date strings useed by php datetime to do maths */
			'DAILY' => 'day',
			'MONTHLY' => 'month',
			'YEARLY' =>  'year',
			'WEEKLY' => 'week',
			'HOURLY' => 'hour',
			'MINUTELY' => 'minute',
			'SECONDLY' => 'second'
			);

/* ------------------------------------------------------------------------------------------------------ */
function amr_getset_options ($reset=false) {
	/* get the options from wordpress if in wordpress
	if no options, then set defaults */
	global $locale, $amr_options;  /* has the initial default configuration */
			/* set up some global config initially */

	amr_set_defaults(); 

	/* we are requested to reset the options, so delete and update with default */
	if ($reset) {
		echo '<div class="updated"><p>';
		_e('Resetting options...', 'amr-ical-events-list');
		if (($d = delete_option('amr-ical-events-list')) or 
			($d = delete_option('AmRiCalEventList')))
			_e('Options Deleted...','amr-ical-events-list');
		else _e('Option was not saved before or error deleting option...','amr-ical-events-list');
		delete_option('amr_ical_images_to_use');
		echo '</p></div>';
		}
	else  {/* *First setup the default config  */
/* general config */
		$amr_options = get_option('amr-ical-events-list');
		if (!empty($amr_options)) { 
			$alreadyhave = true;
			//if had one global option, now split into separate ?
			if (!empty($amr_options['no_types'])) {
			//and !empty($amr_options['1']))  { // then we have old list types, lets convert to new
				for ($i = 1; $i <= $amr_options['no_types']; $i++)  {
					$amr_options['listtypes'][$i] = $amr_options[$i];
					$amr_options['listtypes'][$i]['compprop'] = amr_remove_array_level ($amr_options['listtypes'][$i]['compprop']);
					unset ($amr_options[$i]);
				}

				unset ($amr_options['no_types']); // no longer relevant
				if (is_admin()) { // else might be on a front end page
					amr_notice_listtypes_converted();
					update_option('amr-ical-events-list', $amr_options);
				}

			}
		}
		else {  // really old - can delete one day?
			if ($alreadyhave = get_option('AmRiCalEventList')) {
				delete_option('AmRiCalEventList');
				add_option('amr-ical-events-list', $alreadyhave);
				_e(' Converting option key to lowercase','amr-ical-events-list');
			}
		}

		}
	if (!(isset($alreadyhave)) or (!$alreadyhave) ) 
		amr_set_defaults(); 
	
	if (!empty($amr_options['usehumantime'])) { 
		add_filter ('amr_human_time','amr_human_time');
	}
	return ($amr_options);
	}
//----------------------------------------------	
function amr_ical_apply_version_upgrades ($prev_version) {
global $amr_options;

	// must do oldest updates first
	if (version_compare ($prev_version,'4.0.19','<')) {
		if (!isset($amr_options['lookprevmessage']) ) { // can be empty later
				$amr_options['lookprevmessage'] = __('Look for Previous','amr-ical-events-list'); // for compatibility
				$amr_options['resetmessage'] = __('Reset','amr-ical-events-list'); 
		}
		if (!isset($amr_options['usehumantime'])) // can be false after admin has set the options
			$amr_options['usehumantime'] = true;
	}	
	if (version_compare ($prev_version,'4.0.29','<')) {
		if (!isset($amr_options['freebusymessage']) ) { // can be empty later
			$amr_options['freebusymessage'] = __('Busy','amr-ical-events-list'); // force translation to be recorded 
			$amr_options['freebusymessage'] = '&#10006;';
		}
		
	}
	// later delete the old multiple options and resave as one for reduced db queries 

}	
//----------------------------------------------  temp adjust
function amr_remove_array_level ($compprop) {
	$newcompprop = array();
	foreach ($compprop as $g => $v) { // remove one level

		$newcompprop = array_merge ($newcompprop, $v);
	}
	$newcompprop = amr_sort_by_two_cols_asc('Column','Order',$newcompprop);

	return($newcompprop);
}
//---------------------------------------------- 
	global 	$amr_freq,
		$amr_freq_unit;

	$amr_freq['DAILY'] 			= __('Daily', 'amr-ical-events-list');
	$amr_freq['WEEKLY'] 		= __('Weekly', 'amr-ical-events-list');
	$amr_freq['MONTHLY']		= __('Monthly', 'amr-ical-events-list');
	$amr_freq['YEARLY'] 		= __('Yearly', 'amr-ical-events-list');
	$amr_freq['HOURLY'] 		= __('Hourly', 'amr-ical-events-list');
	$amr_freq['RDATE'] 			= __('on certain dates', 'amr-ical-events-list');
	$amr_freq_unit['DAILY'] 	= __('day', 'amr-ical-events-list');
	$amr_freq_unit['WEEKLY'] 	= __('week', 'amr-ical-events-list');
	$amr_freq_unit['MONTHLY']	= __('month', 'amr-ical-events-list');
	$amr_freq_unit['YEARLY'] 	= __('year', 'amr-ical-events-list');
	$amr_freq_unit['HOURLY'] 	= __('hour', 'amr-ical-events-list');
?>