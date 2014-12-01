<?php
/*
 * This file incudes functions for parsing iCal data files duringan import.
 /* It endeavours to parse as incluisive;y as much as possible.
 /* It includes functions to cache the file
 /* It is not a validator!
 /* The function will return a nested array
	properties
		vevents
			event1
				parameters
				repeatable parameters
					repeat 1
					repeat 2
			event2
		vtodos etc
 *
 * The iCal specification is available online at:
 *	http://www.ietf.org/rfc/rfc2445.txt
 *
 */
include 'timezones/amr-windows-zones.php';
/* ---------------------------------------------------------------------- */
/* Return the full path to the cache file for the specified URL.*/
function get_cache_file($url) {
		return get_cache_path() .'/'. amr_get_cache_filename($url);
	}
/* ---------------------------------------------------------------------- */
/* Attempt to create the cache directory if it doesn't exist.
	 * Return the path if successful.
*/
function get_cache_path() {
	global $amr_options;
		$cache_path = (ICAL_EVENTS_CACHE_LOCATION. '/ical-events-cache');
		if (!file_exists($cache_path)) { /* if there is no folder */
			if (wp_mkdir_p($cache_path, 0777)) {
				printf('<br />'.__('Your cache directory %s has been created','amr-ical-events-list'),'<code>'.$cache_path.'</code>');
			}
			else {
				die( '<br />'.sprintf(__('Error creating cache directory %s. Please check permissions','amr-ical-events-list'),$cache_path));
			}
		}
		return $cache_path;
	}
/* ---------------------------------------------------------------------- */
/* Return the cache filename for the specified URL.	 */
function amr_get_cache_filename($url) {
		$extension = ICAL_EVENTS_CACHE_DEFAULT_EXTENSION;
		$matches = array();
		if (preg_match('/\.(\w+)$/', $url, $matches)) {
			$extension = $matches[1];
		}
		return md5($url) . ".$extension";
	}
/* ---------------------------------------------------------------------- */
/* Cache the specified URL and return the name of the destination file.	 */
if( !class_exists( 'WP_Http' ) )
          include_once( ABSPATH . WPINC. '/class-http.php' );
/* ---------------------------------------------------------------------- */
function amr_check_start_of_file ($data) {// check if the file looks like a icsfile
	if (empty($data)) return false;
	$checkstart = substr($data,0,15);
	if (!($checkstart == 'BEGIN:VCALENDAR')) {
		If (ICAL_EVENTS_DEBUG) {
			echo '<br /> No VCALENDAR in file. Start has: '.$checkstart.' end';
		}
		echo '<a class="error" href="#" title="'
			.__('Unexpected data contents. Please tell administrator.','amr-ical-events-list' ). ' '
			.__('See comments in source for response received from ics server.','amr-ical-events-list' )
			.'">!</a>';
		echo '<!-- Some of the content returned is: '; var_dump(substr ($data,0,200)); echo ' end of dump -->';
		return false;
	}
	return true;	

}
function amr_set_http_timeout($val) {
global $amr_options;
	if (!empty($amr_options['timeout'])) 
		return ($amr_options['timeout']);
	else 
		return $val;
}
/* ---------------------------------------------------------------------- */
function amr_cache_url($url, $cache=ICAL_EVENTS_CACHE_TTL) {
	global $amr_lastcache;
	global $amr_globaltz;
	global $amr_options;
	
		$text = '';
		// if any args are sent then all must be sent - use wp defaults more or less
		// so better to use filters		
		add_filter( 'http_request_timeout', 'amr_set_http_timeout' );	
		//add_filter( 'http_request_redirection_count', 'amr_' );	
		//'httpversion' => apply_filters( 'http_request_version', '1.0' ),  //or 1.1 
		/*
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);  // just says to return rather than echo
			curl_setopt($c, CURLOPT_USERAGENT, 'PHP/'.PHP_VERSION);
			curl_setopt($c, CURLOPT_ENCODING, '');
			if( strstr( $resource, 'https' ) !== FALSE ) {
				curl_setopt($c, CURLOPT_SSLVERSION, 3);
				curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);
			}
			curl_setopt($c, CURLOPT_COOKIESESSION, true);
			curl_setopt($c, CURLOPT_HEADER, true);
			if( !ini_get('safe_mode') ){
				curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			}
		*/
		
//		If (ICAL_EVENTS_DEBUG) echo '<hr />url before decode: '.$url.'<br />';
		$url = html_entity_decode($url);
//		If (ICAL_EVENTS_DEBUG) echo '<br />url decoded: '.$url.'<hr />';
		$cachedfile = get_cache_file($url);
		if ( file_exists($cachedfile) ) {
			$c = filemtime($cachedfile);
			if ($c) 
				$amr_lastcache = amr_newDateTime(strftime('%c',$c));
			else 
				$amr_lastcache = '';
		}
		else {
			$c = false;
			$amr_lastcache = amr_newDateTime(strftime('%c',0));
			}
		// must we refresh ?
		if ( isset($_REQUEST['nocache']) or isset($_REQUEST['refresh'])
			or (!(file_exists($cachedfile))) or ((time() - ($c)) >= ($cache*60*60))) 	{
			If (ICAL_EVENTS_DEBUG) echo '<br>Get ical file remotely, it is time to refresh or it is not cached: <br />';
			amrical_mem_debug('We are going to refresh next');

			//$url = urlencode($u);  - do NOT encode - that gives an invalid URL response
			$check = wp_remote_get($url);  
			// if use args, must set all - rather use filters and let wp do its thing
			if (( is_wp_error($check) ) or  (isset ($check['response']['code']) and !($check['response']['code'] == 200))
			or (isset ($check[0]) and preg_match ('#404#', $check[0]))) {/* is this bit still meaningful or needed ? */

				If (ICAL_EVENTS_DEBUG) { echo '<hr /><b>Http request failed </b><br /> Dumping response: ';
				var_dump($check);
				}
				if (is_wp_error($check))
					$text = '<br />'.$check->get_error_message().'</br>';
				else $text = '';
				$data = false;
			}	
			elseif  (!stristr($check['headers']['content-type'],'text/calendar')) {
			
				if (amr_check_start_of_file ($data = $check['body'])) { // well wrong content type, but has the content!! - bad calendar provider
					$data = $check['body']; 
					if (current_user_can('manage_options')) { 
					echo '<br />This message is only shown to the administrator, and only when we refresh the file.';
					echo '<br />The ics url given is issuing an incorrect content type of text/html.'
					.' It should be text/calendar. '
					.' Luckily we persevere and check if the content looks like an ics file.'
					.'Please inform the provider of the url. '
					.'Their urls may not be recognised by browsers as ics files. <br />';					
					}
				}
				else {
					if (ICAL_EVENTS_DEBUG) {
						echo '<br />The url given is not returning a calendar file';
						echo '<br />The response was '; var_dump($check['response']);
						echo '<br />The content type is '.$check['headers']['content-type'];
						echo '<br />The content type of an ics file should be text/calendar. <br />';
						
					}
					$data = false;
				}
			
			}
			else $data = $check['body'];  // from the http request		
			
			if (!amr_check_start_of_file ($data)) {			

					$text .= '&nbsp;'.sprintf(__('Error getting calendar file with htpp or curl %s','amr-ical-events-list'), $url);

					if ( file_exists($cachedfile) ) { // Try use cached file if it exists
						if (is_object($amr_lastcache)) 
							$text .= '&nbsp;...'.sprintf(__('Using File last cached at %s','amr-ical-events-list'), $amr_lastcache->format('D c'));
						else 
							$text .= '&nbsp;...'.__('File last cached time not available','amr-ical-events-list');
						echo '<a class="error" href="#" title="'
						.__('Warning: Events may be out of date. ','amr-ical-events-list' )
						. $text.'">!</a>';
						return($cachedfile);  //return file not data
						}
					else {
						echo '<a class="error" href="#" title="'
						.__('No cached ical file for events','amr-ical-events-list' )
						. $text.'">!</a>';
						return (false);
					}
					return (false);
				}
			else If (ICAL_EVENTS_DEBUG) { echo '<br />We have vcalendar in start of file';}
	
			// somebody wanted to pre process ics files that were not well generated?
			// A filter could be added here, but I'm not keen - could add to support load?
	
			if ($data) { /* now save it as a cached file */
				if ($dest = fopen($cachedfile, 'w')) {
					if (!(fwrite($dest, $data))) die ('Error writing cache file'.$dest);
					fclose($dest);
					$amr_lastcache = amr_newDateTime (date('Y-m-d H:i:s'));
				}
				else  {
					echo '<br />Error opening or creating the cached file <br />'.$cachedfile;
					return (false);
				}
			}
			else {	echo '<br>Error opening remote file for refresh '.$url;	return false;}
			if (!isset($amr_lastcache))	$amr_lastcache = amr_newDateTime (date('Y-m-d H:i:s'));
		}
		else {}// no need to refresh, use the cached file

		return ($cachedfile);

}
/* ---------------------------------------------------------------------- */
function amr_parseAttendees	($arraybycolon)    { /* receive full string parsed to array  */
/*
ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;CN=Anna-m

 arie Redpath;X-NUM-GUESTS=0:mailto:annamarieredpath@gmail.com

ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;CN=an

 drew@pahlman.com;X-NUM-GUESTS=0:mailto:andrew@pahlman.com
 
ATTENDEE;X-NUM-GUESTS=0:mailto:1bfb88li8v385q41k6s7fnl9ls@group.calendar.go
 ogle.com 

 ATTENDEE;ROLE=REQ-PARTICIPANT;DELEGATED-FROM="mailto:bob@
        example.com";PARTSTAT=ACCEPTED;CN=Jane Doe:mailto:jdoe@
        example.com
		
ATTENDEE;SENT-BY=mailto:jan_doe@example.com;CN=John Smith:
        mailto:jsmith@example.com		
NOT USING FOR NOW - INTERNAL ATTENDEES ONLY
 */

// the first one should start with ATTENDEE

		$properties = $arraybycolon[0]; 
		unset($arraybycolon[0]);
		foreach ($arraybycolon as $next) {
			if (strstr($next, 'mailto')) {
				$email = amr_parseMailto($next);
			}
		}
		$attendee['mailto'] = $email;
		//
		//ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;CN=Common Name;X-NUM-GUESTS=0
		$parameters = explode (';',$properties);

		foreach ($parameters as $param) {
			if (!($param == 'ATTENDEE')) { // skip the first one
				$parts = explode('=',$param);	
				if (count($parts) == 2) {
					$attendee[$parts[0]] = $parts[1];
				}
			}
		}

	return($attendee); // a single attendee
}
/* ---------------------------------------------------------------------- */
function amr_parseMailto ($text) { //mailto:ovcweb@uoguelph.ca    return email
		return (str_replace('mailto:','',$text));
	} 
/* ---------------------------------------------------------------------- */
function amr_parseOrganiser($arraybysemicolon)    { /* receive full string parsed to array split by the semicolon
	[0]=>ORGANIZER;SENT-BY="mailto
	[1]=>dwood@uoguelph.ca":mailto:ovcweb@uoguelph.ca

	or
	[0]=>ORGANIZER;CN=Webmaster - OVC;SENT-BY="mailto
	[1] => bagunn@uoguelph.ca":mailto:ovcweb@uoguelph.ca

	*/
//	if (ICAL_EVENTS_DEBUG) {echo '<br/>Organiser to parse <br />'; var_dump($arraybysemicolon);}
	$org = array();
	$p0 = explode(';',$arraybysemicolon[0]);
	$m = explode(':',$arraybysemicolon[1]);
//	if (ICAL_EVENTS_DEBUG) {echo '<br/>m : <br />'; var_dump($m); echo '<br/>p0 : <br />'; var_dump($p0);}
	foreach ($m as $i => $m2) {
		if (strtoupper($m2) == 'MAILTO') {
			$mailto = rtrim($m[$i+1],'"');
		}
	}

	foreach ($p0 as $i => $p) {
		$p1 = explode('=',$p);
		if (isset ($p1[0]))  {
			$org['type'] = $p1[0]; /* if (!empty($p1[1])) $org['typevalue'] = $p1[1];   *** Parse this properly if we wantto handle complex attendees */

			if ( ($p1[0] == 'SENT-BY') and (!empty($p1[1]))) {
				$sentby = rtrim($m[0],'"');
				$org['SENT-BY'] = $sentby;
			}
			else {
				if (($p1[0] == 'CN') and (!empty($p1[1]))) {
					$org['CN'] = rtrim( $p1[1], '"');
					}
				}
		}
	}

	if (!empty($mailto)) $org['MAILTO'] = $mailto;
	if (empty($org)) return ($arraybysemicolon);
	return ($org);
    }
/* ---------------------------------------------------------------------- */
    /**
     * Parse a Time Period field.
     */
function amr_parsePeriod($text,$tzobj)    {
        $periodParts = explode('/', $text);
        if (!($start = amr_parseDateTime($periodParts[0], $tzobj))) return (false);
        if ($duration = amr_parseDuration($periodParts[1])) return array('start' => $start, 'duration' => $duration);
		else {
			if (!($end = amr_parseDateTime($periodParts[1], $tzobj))) return (false);
			else {
				return array('start' => $start, 'end' => $end);
			}
		}
    }
	/* ---------------------------------------------------------------------- */
	   /**
     * Parses a DateTime field and returns a datetime object, with either it's own tz if it has one, or the passed one
     */
function amr_parseDateTime($d, $tzobj)    {
		global $amr_globaltz;
		global $utczobj;
		/*  	19970714T133000            ;Local time
			19970714T173000Z           ;UTC time
			tz dealt with already ?*/

		if (empty($d)) {
			echo 'Unexpected error - empty date string to parse ';
			return false;
			}

		if ((substr($d, strlen($d)-1, 1) === 'Z')) {  /*datetime is specifed in UTC */
			$tzobj = $utczobj;
			$d = substr($d, 0, strlen($d)-1);
		}

		$date = substr($d,0, 4).'-'.substr($d,4, 2).'-'.substr($d,6, 2);
		if (strlen ($d) > 8) {
			$time = substr($d,9 ,2 ).':'.substr($d,11 ,2 )  ; /* has to at least have hours and mins */
		}
		else $time = '00:00';
		if (strlen ($d) > 13) {
			$time .= ':'.substr($d,13 ,2 );
		}
		else $time .= ':00';
		/* Now create our date with the timezone in which it was defined , or if local, then in the plugin glovbal timezone */
		
		$dt = amr_create_date_time ($date.' '.$time, $tzobj);
		//try {	$dt = new DateTime($date.' '.$time,	$tzobj); }
		//catch(Exception $e) {
		//	echo '<br />Unable to create DateTime object from '.$date.' <br />'.$e->getMessage();
		//	return (false);
		//}

	return ($dt);
    }
	/* ---------------------------------------------------------------------- */
    /* Parses a Date field. */
function amr_parseRange($range, $daterange, $tzobj)    {  /*
  For RECURRENCE-ID;
  Strings like:
 VALUE=DATE:19960401

 RANGE=THISANDFUTURE:19960120T120000Z
 RANGE=THISANDPRIOR:19960120T120000Z
	*/
		If (isset ($_REQUEST['debugexc'])) {	echo '<br />Got Range '.$range.' with '.$daterange.'<br />';	}
		$r = explode (':', $daterange);
		if (!($thisanddate = amr_parseDateTime($r[1], $tzobj))) return (false);
		If (isset ($_REQUEST['debugexc'])) {	echo '<br />Got range '.$range.' "THISAND" date '.$thisanddate ->format('c').'<br />';	}
		return (array('RANGE'=>$p[0],'DATE'=> $thisanddate));
    }
	/* ---------------------------------------------------------------------- */
    /* Parses a Date field. */
function amr_parseDate($text, $tzobj)    {  /*
		 VALUE=DATE:
		 19970101,19970120,19970217,19970421
		   19970526,19970704,19970901,19971014,19971128,19971129,19971225
		   VALUE=DATE;TZID=/mozilla.org/20070129_1/Europe/Berlin:20061223
	*/
		
		$p = explode (',',$text); 	/* if only a single will still return one array value */
		foreach ($p as $i => $v) {
			$datestring = substr($v, 0, 4).'-'.substr($v,4, 2).'-'.substr($v,6, 2);
			$dates[] = amr_create_date_time ($datestring, $tzobj);
			/*try {
			//	$dates[] =  new DateTime(substr($v,0, 4).'-'.substr($v,4, 2).'-'.substr($v,6, 2), $tzobj);
			//}
			//catch(Exception $e) {
			//	echo '<br />Unable to create DateTime object from '.$text.' <br />'.$e->getMessage();
			//	return (false);
			}*/
		}
		return ($dates);

    }
	/* ------------------------------------------------------------------ */
function amr_parseTZDate ($value, $tzid) {
		$tzobj = amr_parseTZID($text);

		if (!($d = amr_parseDateTime ($value, $tzobj))) return(false);
		else return ($d);
	}
	/* ------------------------------------------------------------------ */
function amr_deduceTZID($icstzid)    { 
// we have something that php didn't like, so we will try work something out
// Not great, really should use the filter function rather
   global $amr_globaltz, $globaltzstring;

//			$strip = array ('(',' ');
//			$icstzid = str_replace($strip,'',$icstzid);
			$gmtend = stripos($icstzid,')'); /* do we have a brackedt GMT ? */
			//if (isset ($_REQUEST['tzdebug'])) {echo '<br/>Check for a bracketed gmt? = '.$gmtend.' in string '.$icstzid ; }
			if (!empty ($gmtend) ) {
				$icstzid = str_replace(')','/',$icstzid);
				$icstzcities = explode ('/',$icstzid); /* could be commas, could be slashes */
				if (isset ($_REQUEST['tzdebug'])) {echo '<br/>strip the gmt out '; print_r($icstzcities);}
				$gmt = stripos(  $icstzid, 'GMT'); /* do we have a brackedt GMT ? */
				if (!empty($gmt)) unset ($icstzcities[0]); /* don't want the GMT - potentially misleading */
			}
			else { /* Maybe we have a list of cities and maybe we do not */
				$icstzcities = array();
				$temp = explode (',',$icstzid); /* could be commas, could be slashes */
				foreach ($temp as $temp2) {
					$temp3 = explode ('/',$temp2);
					$icstzcities = array_merge($icstzcities, $temp3);
					}
				}
			foreach ($icstzcities as $i=>$icscity) {
				$icstzcities[$i] = trim($icscity,' ');
			}	
			//if (isset ($_REQUEST['tzdebug'])) { echo '<br />Do we have a City? <br />';print_r($icstzcities);}
			$globalcontcity = explode ('/',$globaltzstring);
			if (isset ($globalcontcity[1]) ) 
				$globalcity = $globalcontcity[1];
			else 
				$globalcity = $globalcontcity[0];
//			if (isset ($_REQUEST['tzdebug'])) {	echo '<hr> text = '.$text.'<br/>icstzid = '.$icstzid.' and wp tz = '.$globalcity.' <br >'; print_r($icstzcities);		}
			if (in_array($globalcity, $icstzcities)) { /* if one of the cities in the tzid matches ours, again we can use the matched one */
				$tzname = $globaltzstring;
			}
			else {
			
				$alltzcities = amr_get_timezone_cities ();
				if (isset($alltzcities[$icstzid])) { /* then it is a normal php timezone we know about, so we can proceed */
					$tzname = $icstzid;
				}
				else {
					
					foreach ($icstzcities as $i=>$c) {
						if (isset ($alltzcities[$c] )) { /* try each of the cities if we have mutiple */
							$tzname = $alltzcities[$c];
							break;
						}
					}
					if (isset ($_REQUEST['tzdebug'])) {echo '<br/>No match to known cities'; }
				}
			}
			/* */
	
		if (!isset ($tzname)) { /* see if we do it with GMT after all ? */
			if (isset($icstzcities[0])) {
				$tryoffset = str_replace('GMT','',$icstzcities[0]);
				if (empty($tryoffset) or (is_int($tryoffset))) {
					$tzname = amr_getTimeZone($tryoffset);
					if (isset ($_REQUEST['tzdebug'])) 
						{echo '<br/>Try see if offset:'.$tryoffset. ' gave '.$tzname; }
				}
				else {
					$tzname = amr_unknown_timezone($icstzid, $globaltzstring);
				}
			}
			else {
				$tzname = amr_unknown_timezone($icstzid, $globaltzstring);
			}
		}
		if (isset ($_REQUEST['tzdebug'])) echo '<br /><b>Timezone must be: </b> '.$tzname.'<br />';
		$tz = amr_try_timezone ($tzname);
		if (!$tz) $tz = $amr_globaltz;
	return ($tz);
}
/* ------------------------------------------------------------------ */
function amr_tz_error_handler () { //cannot have anonymous function in php < 5.3
}
/* ------------------------------------------------------------------ */
function amr_parseTZID($text)    {
   global $amr_globaltz, // the main timezone object
	$globaltzstring, // the string for the timezone object
	$icsfile_tzname, // the timexone name string in the ics file
	$icsfile_tz;  // the ics timezone object that we last parsed and ended up with (often it's all the same
	
   /* take a string that may have a olson tz object and try to return a tz object */
   /* accept long and short TZ's, --- assume website tz if not valid eg Zimbra's: GMT+01.00/+02.00 */

		$icstzid = trim($text,'"=' ); /* check for enclosing quotes like zimbra issues */
		//-----------------------------------

 //----------------------------------- is it same as wordpress ?  		
		if (empty($globaltzstring)) 
			$globaltzstring = timezone_name_get($amr_globaltz);	
			
		if ($globaltzstring == $icstzid	) {/* if the timezone matches the wordpress or shortcode time zone, then we are cool ! */
			$icsfile_tzname = $icstzid; // set the global
			$icsfile_tz = $amr_globaltz;
			if (isset ($_REQUEST['tzdebug'])) echo '<br />'.$icstzid.' Matches wordpress tz.'; 
			return ($amr_globaltz);
		}
//----------------------------------- is it same as previously parsed ? if yes, go with that

		if (!empty($icsfile_tzname) and ($icsfile_tzname == $icstzid )) {
			if (isset ($_REQUEST['tzdebug'])) echo '<br />'.$icstzid.' Matches previously parsed tz.' ; 
			return ($icsfile_tz);
		}	

//-----------------------------------	is it a valid php timezone ?		
		$timezone_identifiers = (DateTimeZone::listIdentifiers());
		//foreach ($timezone_identifiers as $i=> $z) {echo '<br />'.$i; var_dump($z);}
	    if (in_array($icstzid,$timezone_identifiers, false)) { // we now have a valid php timezone
			if (isset ($_REQUEST['tzdebug'])) {echo '<br/>Php should like:'.$icstzid; }
			$tz = amr_try_timezone ($icstzid); // this shoul work else simething weird going on
			if (!empty($tz)) return($tz);
		}

//-----------------------------------	if not already a valid php timezone, can we make it a valid zone ?	
		$tzid = apply_filters('amr-timezoneid-filter',$icstzid);  //apply filters like for windows zones etc
		// let us see if php likes it		
			
		try {	set_error_handler( 'amr_tz_error_handler'); /* ignore errors , just giveit a go*/ 
					$tz =  timezone_open($tzid);
					restore_error_handler();
					if ($tz) { 
						$tzname = $tzid;
						if (isset ($_REQUEST['tzdebug'])) {echo '<br/>Php liked:'.$tzid; }
					}
					else $tz = amr_deduceTZID($tzid) ;
				}
			catch(Exception $e) {/* we tried the filter but php did't like, so lets try fix it */
			/* else try figure the timezone out */
				$tz = amr_deduceTZID($tzid) ;
			}
			
		if (empty($tz)) {
			if (isset ($_REQUEST['tzdebug'])) {echo '<br/>Giving up on timezone: '.$icstzid; }
			return false; // we couldn't figure it out	
		}	
		if (empty($icsfile_tzname))	{  // if this is the first time we parsed, lets save the hard work
			$icsfile_tzname = $icstzid; // what was actually in the file
			$icsfile_tz = $tz; // this is the php tz we ended up with 
			}
		return ( $tz);
}	
	/* ------------------------------------------------------------------ */
function amr_try_timezone ($tzname) {	
		try {
				$tz =  timezone_open($tzname);
			}
		catch(Exception $e) {
				$text = 'Unable to create Time zone object., Using wp default.<br />'.$e->getMessage();
				amr_tell_admin_the_error ($text);			
				//echo '<br />Unable to create Time zone object., Using wp default.<br />'.$e->getMessage();
				return ($amr_globaltz);
			}
	return ($tz);
}
/* ------------------------------------------------------------------ */
function amr_unknown_timezone ($text, $tzname) {	
	$emessage = 'Unable to deal with timezone like this: '.$text;
//	echo '<!-- '.$emessage.' -->';
//	if (isset ($_REQUEST['tzdebug']) or ICAL_EVENTS_DEBUG) {
	if (is_super_admin()) {
		echo  '<br />Message to logged-in admin only: <b>'.$emessage.'</b>';
		echo '- Making an assumption! Using '.$tzname.'<br />';
	}
	return ($tzname);
}
/* ------------------------------------------------------------------ */
function amr_parseSingleDate($VALUE='DATE-TIME', $text, $tzobj)	{
   /* used for those properties that should only have one value - since many other dates can have multiple date specs, the parsing function returns an array
	Reduce the array to a single value */
		$arr = amr_parseVALUE($VALUE, $text, $tzobj);
		if (is_array($arr)) {
			if (count($arr) > 1) {
				error_log ( '<br>Unexpected multiple date values'.$text);
			}
			else return ($arr[0]);
		}
		return ($arr);
	}
/* ---------------------------------------------------------------------- */
function amr_parseVALUE($VALUE, $text, $tzobj)	{
	/* amr parsing a value like
	VALUE=PERIOD:19960403T020000Z/19960403T040000Z,	19960404T010000Z/PT3H
	VALUE=DATE:19970101,19970120,19970217,19970421,..	19970526,19970704,19970901,19971014,19971128,19971129,19971225
	VALUE=DATE;TZID=/mozilla.org/20070129_1/Europe/Berlin:20061223	*/
	
	
		if (empty($text)) {
			if (ICAL_EVENTS_DEBUG) {echo '<br />For value: '.$VALUE.' text is blank';}
			return (false);
			}

		switch ($VALUE) {
			case 'DATE-TIME': {
				if (!($d = amr_parseDateTime($text, $tzobj))) return (false);
				else return ($d);
				}
			case 'DATE': {if (!($d = amr_parseDate($text, $tzobj))) return (false);
						else return ($d); }
			case 'PERIOD': {if (!($d = amr_parsePeriod($text, $tzobj))) return (false);
						else return ($d); }
			default: { /* something like DATE;TZID=/mozilla.org/20070129_1/Europe/Berlin */
				$p = explode (';',$VALUE);
				if (!($p[0] === 'DATE')) {
					if (ICAL_EVENTS_DEBUG) {echo 'Error: Unexpected data in file '; print_r($p);}
					return (false);
					}
				else {
					if (substr ($p[1], 0, 4) === 'TZID') {/* then we have a weird TZ */
						$tzobj = amr_deal_with_tzpath_in_date (substr($p[1],5)); /* pass the rest of the string over for tz extraction */
						if (!($d = amr_parseDate($text, $tzobj))) 
							return (false);
						else 
							return ($d);
					}
					else {
						if (ICAL_EVENTS_DEBUG) {echo 'Error: Unexpected data in file '; print_r($p[1]);}
						return (false);
					};
				}
			}
			return (false);
		}
	}
/* ---------------------------------------------------------------------- */
/** * Parse a Duration Value field.*/
function amr_parseDuration($text)     {
	/*
	A duration of 15 days, 5 hours and 20 seconds would be:  P15DT5H0M20S
	A duration of 7 weeks would be:  P7W, can be days or weeks, but not both
	we want to convert so can use like this +1 week 2 days 4 hours 2 seconds ether for calc with modify or output.  Could be neg (eg: for trigger)
	*/

	if (isset($_GET['debugdur'])) {echo '<br />Entering Pregmatch.. '.$text;}
	
        if (preg_match('/([+]?|[-])P(([0-9]+W)|([0-9]+D)|)(T(([0-9]+H)|([0-9]+M)|([0-9]+S))+)?/',
			trim($text), $durvalue)) {

			/* 0 is the full string, 1 is the sign, 2 is the , 3 is the week , 6 is th T*/
			if (isset($_GET['debugdur'])) {echo '<br />Pregmatch gives '; var_dump($durvalue);}

			if ($durvalue[1] == "-") {  // Sign.
                $dur['sign'] = '-';
            }
            // Weeks
		    if (!empty($durvalue[3])) $dur['weeks'] = rtrim($durvalue[3],'W');

            if (count($durvalue) > 4) {                // Days.
				if (!empty($durvalue[4])) $dur['days'] = rtrim($durvalue[4],"D");
            }
            if (count($durvalue) > 5) {                // Hours.
				if (!empty($durvalue[7])) $dur['hours'] = rtrim($durvalue[7],"H");

                if (isset($durvalue[8])) {    // Mins.
					$dur['mins'] = rtrim($durvalue[8],"M");
                }
                if (isset($durvalue[9])) { // Secs.
					$dur['secs'] = rtrim($durvalue[9],"S");
                }
            }
			if (!empty ($dur)) return $dur;
			else { // possibly error in input data 
				if (isset($_GET['debugdur'])) {echo '<br />Possibly error in input data that pregmatch did not deal with .. '.$text;}
				return false;
			}
            return $dur;

        } else {
            return false;
        }
    }
/* ---------------------------------------------------------------------- */
function amr_parse_CATEGORIES ($text ) {
	$cats = explode(',',$text);   // will return an array, but since we can have multiple CATEGORIES lines, will get an array of arrays .. confusing
	return($cats);
}
/* ---------------------------------------------------------------------- */
function amr_parseRDATE ($string, $tzobj ) {
/*
		RDATE:19970714T123000Z
		RDATE:19970714T083000
		RDATE;TZID=US-EASTERN:19970714T083000
		RDATE;VALUE=PERIOD:19960403T020000Z/19960403T040000Z,19960404T010000Z/PT3H - not supported yet
		RDATE;VALUE=DATE:19970101,19970120,19970217,19970421,19970526,19970704,19970901,19971014,19971128,19971129,19971225

 could be multiple dates after : */

	if (empty($string)) return false; 
	if (is_object($string)) {/* already parsed */  return($string); }

	if (is_array($string) ) {
		$rdatearray = array();
		foreach ($string as $i => $rdatestring) {
//			$r = $string[0];
			if (is_object($rdatestring)) {/* already parsed  and is an array of dates */  return($string); }
			else {
				//if (isset($_GET['debugexc'])) {echo '<br />Doing next r or exdate '.$i.' '.$rdatestring; }
				$rdate = amr_parseRDATE ($rdatestring, $tzobj );

			}
			if (is_array($rdate)) $rdatearray = array_merge ($rdatearray, $rdate);
		}

		//if (isset($_GET['debugexc'])) {echo '<br />*** Array of r or exdate '; var_dump($rdatearray); }
		return ($rdatearray);
	}

	$rdatestring = explode(':',$string);   /* $VALUE=DATE: or VALUE=DATE-TIME: and a series of dates (no time) */

//	if (isset($_GET['rdebug'])) {echo '<br />Ok now really parse it '; var_dump($rdatestring); echo '<br />'; }

	if (isset($rdatestring[0])) {

		if (($rdatestring[0] == 'VALUE=DATE') and (isset($rdatestring[1])) ) {

			$rdate =  explode(',',$rdatestring[1]); /* that' sall we are doing for now */
//			if (isset($_GET['rdebug'])) {echo '<br />Parsing value=date...<br/> '; var_dump($rdate);}
			foreach ($rdate as $i => $r)  {
					$dates[$i] = array_shift(amr_parseValue ('DATE', $r, $tzobj));
					/*returns array, but there should only be 1 value */
			}
			return($dates);

		}
		else if (($rdatestring[0] == 'VALUE=PERIOD') and (isset($rdatestring[1]))) {
		 echo "<br />HELP cannot yet deal with RDATE with VALUE=PERIOD<br />"; return (false);
			}
		else {
//			if (isset($_GET['debugexc'])) {echo '<br />*** Parsing RDATE date time ';	var_dump($rdatestring);}
			if (($rdatestring[0] == 'VALUE=DATE-TIME') and (isset($rdatestring[1])))  {
				$rdate =  explode(',',$rdatestring[1]);
			}
			else {
				$rdate =  explode(',',$rdatestring[0]);
			}
			foreach ($rdate as $i => $r)  {
					if (empty($r)) { return false; }
					$dates[$i] = amr_parseDateTime ( $r, $tzobj);
					if (isset($_GET['debugexc'])) {echo '<br />*** Parsed as: '.$dates[$i]->format('c');}
			}
			if (empty($dates)) return (false);
			else return ($dates);

		}
	}
	else return (false);
}
/* ---------------------------------------------------------------------- */
function amr_parseAttach ($parts) {
/*
This property can be specified multiple times in a

      "VEVENT", "VTODO", "VJOURNAL", or "VALARM" calendar component with

      the exception of AUDIO alarm that only allows this property to

      occur once.
Default is a URL ATTACH:http://example.com/public/quarterly-report.doc
But could also have :
ATTACH:CID:jsmith.part3.960817T083000.xyzMail@example.com
ATTACH;FMTTYPE=audio/basic:ftp://example.com/pub/

 sounds/bell-01.aud

ATTACH;FMTTYPE=application/msword:http://example.com/

 templates/agenda.doc
  ATTACH;FMTTYPE=text/plain;ENCODING=BASE64;VALUE=BINARY:VGhlIH

      F1aWNrIGJyb3duIGZveCBqdW1wcyBvdmVyIHRoZSBsYXp5IGRvZy4

*/
	if (ICAL_EVENTS_DEBUG) {echo '<hr><br/>Attach to parse <br />'; var_dump($parts); echo '<hr>';}

	if (!empty($parts[0])) {
		if ($parts[0] === 'ATTACH') { // then we have a simple URL or CID
			if (!empty ($parts[1])) {
				if (substr($parts[1],0,3) === 'CID' ) {
					$newattach['type'] = 'CID';
					$newattach['CID'] = esc_attr(substr($parts[1],4));

					}
				else { // must be htpp or ftp
					$newattach['type'] = 'url';
					$newattach['url'] = esc_url_raw($parts[1]);
				}

			}
			else return (null);
		}
		else {  // we have an FMTTYPE
			$newattach['type'] = str_replace('ATTACH;FMTTYPE=','', $parts[0]);  //should be something like application/msword, FMTTYPE=audio/basic
			if (!stristr($newattach['type'], 'VALUE=BINARY')) { // not binary
				$newattach['url'] = esc_url_raw($parts[1]);
				}
			else {
				$newattach['binary'] =  amr_remove_folding ($parts[1]);
				}
			}
	}
	else return (null);
	return($newattach);
}
/* ---------------------------------------------------------------------- */
function amr_parseDefault($prop, $parts) {

			$func = 'amr_parse'.str_replace('-','_',$prop);
			if (function_exists($func))		{	
				$result = call_user_func ($func, $parts);
				if (isset($_REQUEST['debugcustom'])) {echo '<br />Used custom function..'.$func; var_dump($result);}
				return ($result);
			//		return (amr_parseCustomModifiers($p));
			}
			else {
				//if (isset($_REQUEST['debugcustom'])) {echo '<br>No function..'.$func.' for '; var_dump($parts);}
				if (isset ($parts[1])) {  // its a straight value
					return (str_replace ('\,', ',', $parts[1]));
					}
				/* replace any slashes added by ical generator */
				else { //nothing to see here folks... move along
					return '';
				}
				return;
			}
	}

//--------------------------------------------------------------------
function amr_parse_property ($parts) {
/* would receive something like array ('DTSTART; VALUE=DATE', '20060315')) */
/*  NOTE: parts[0]    has the long tag eg: RDATE;TZID=US-EASTERN
or could be DTEND;TZID=America/New_York;VALUE=DATE-TIME:     and the date 20110724T100000 in parts 1  (see forum note from dusty - he is generating the DATETIME
		parts[1]  the bit after the :  19960403T020000Z/19960403T040000Z, 19960404T010000Z/PT3H
		IF 'Z' then must be in UTC
		If no Z
*/
global $amr_globaltz;
	//if (isset($_REQUEST['debugcustom'])) {echo '<br />Enter parse property'; var_dump($parts);}

	if (empty($parts[1])) return false; // we got crap
	$tzobj = $amr_globaltz;  /* Because if there is no timezone specified in any way for the date time then it must a floating value, and so should be created in the global timezone.*/
//	if (ICAL_EVENTS_DEBUG or isset($_REQUEST['tzdebug'])) {echo '<br /> Property : '.$parts[0];}
	$p0 = explode (';', $parts[0]);  /* Looking for ; VALUE = something...;   or TZID=... or both, or more than one anyway ???*/
	// the first bit will be the property like PRODID, or X_WR_TIMEZONE
	// the next will be the modifiers
	$prop = array_shift($p0);
	if (!empty($p0)) {  // if we have some modifiers
		foreach ($p0 as $p) {  // handle special modifiers , could be  VALUE=DATE, TZID=
			
			if (stristr($p, 'TZID')) {		
			    /* Normal TZ, not the one with the path eg:  DTSTART;TZID=US-Eastern:19980119T020000 or  zimbras TZID="GMT+01.00/+02.00 */
//				$TZID = substr($p0[1], 4 );
				$TZID = substr($p, 4 );
				$tzobj = amr_parseTZID($TZID);
			}  /* should create datetime object with it's own TZ, datetime maths works correctly with TZ's */
			else {/* might be just a value=date, in which case we use the global tz?  no may still have TZid */
				$tzobj = $amr_globaltz;
				if (stristr($p, 'VALUE')) {  // we have a possibly redundant modified
						$VALUE = substr($p,6);  // take everything after the '='
				}
				else {// not necessary to deal with modifier here or unknown maybe custom modifiers	
					// only urgent to get tZID's.. or maybe we could even do those later? 
					//check it out on major code revamp, meanwhile if it aint broke, dont fix it
				}				
			}
		}
	}
//	else $tzobj = $amr_globaltz;  /* Because if there is no timezone specified in any way for the date time then it must a floating value, and so should be created in the global timezone.*/
//	switch ($p0[0]) {
	
	switch ($prop) {
		case 'CREATED':
		case 'COMPLETED':
		case 'LAST-MODIFIED':
		case 'DTSTART':
		case 'DTEND':
		case 'DTSTAMP':
		case 'DUE':
			if (isset($VALUE)) {
				$date = amr_parseValue($VALUE, $parts[1], $tzobj);	}
/*				return (amr_parseSingleDate($VALUE, $parts[1], $tzobj));	} */
			else {
				$date = amr_parseSingleDate('DATE-TIME', $parts[1], $tzobj);
			}
			
			if (is_object($date) and 
				(($prop === 'LAST-MODIFIED') or ($prop === 'CREATED')))  {
				amr_track_last_mod($date);
			}
			return ($date);
		case 'ALARM':
		case 'RECURRENCE-ID':  /* could also have range ?*/
			if (isset($VALUE)) {
				return (amr_parseValue($VALUE, $parts[1], $tzobj));	}
			elseif (isset($RANGE)){
				return (amr_parseRange($RANGE, $parts[1], $tzobj));
				}
			else {
				return (amr_parseSingleDate('DATE-TIME', $parts[1], $tzobj));
				}
		case 'EXRULE':
		case 'RRULE': 
			return (amr_parseRRULE($parts[1]));
		case 'BDAY':
			return (amr_parseDate ($parts[1]));

		case 'EXDATE':  {if (isset($_REQUEST['debugexc'])) {echo '<br>  Parsing EXDATE ';}}
		case 'RDATE':
			return (amr_parseRDATE ($parts[1],$tzobj));
		case 'TRIGGER': /* not supported yet, check for datetime and / or duration */
		case 'DURATION':
			$dur = amr_parseDuration ($parts[1]);
			if (isset($_GET['debugdur'])) {echo '<br />Parts1 = '.$parts[1].'<br />Duration = '; var_dump($dur);}
			return ($dur);
		case 'FREEBUSY':
			return ( amr_parsePeriod ($parts[1]));
		case 'TZID': /* ie TZID is a property, not part of a date spec */
			return ($parts[1]);
		case 'ORGANIZER': {
			return(amr_parseOrganiser($parts));
			}
		case 'ATTENDEE': {
			return(amr_parseAttendees($parts));
			}
		case 'ATTACH': {
			$attach = amr_parseAttach($parts);
			If (ICAL_EVENTS_DEBUG) echo '<br />ATTACH returned:<br />'.print_r($attach,true);
			return($attach);
			}
		case 'CATEGORIES': {
			$cats = amr_parse_CATEGORIES($parts[1]);
			If (ICAL_EVENTS_DEBUG) {
				var_dump($parts[1]);
				echo '<br />catreturned  :<br />'.print_r($cats ,true);
			}	
			return($cats );
			}
		default:{
			return (amr_parseDefault($prop, $parts));
		}		
	}
}
/* ---------------------------------------------------------------------- */
function amr_parse_component($type)	{	/* so we know we have a vcalendar at lines[$n] - check for properties or components */
	global $amr_lines;
	global $amr_totallines;
	global $amr_n;
	global $amr_validrepeatablecomponents;
	global $amr_validrepeatableproperties;
	global $amr_globaltz;


	while (($amr_n < $amr_totallines)	)	{
		//if (ICAL_EVENTS_DEBUG) {echo '<br/>*** '.$type.' '.$amr_lines[$amr_n];}
		$amr_n++;
		$parts = explode (':', $amr_lines[$amr_n],2 ); /* explode faster than the preg, just split first : */
		if ((!$parts) or ($parts === $amr_lines[$amr_n])) {
			if (ICAL_EVENTS_DEBUG) echo ( '<br /> Error in line, skipping '.$amr_n.': with value:'.$amr_lines[$amr_n]);
			}
		else {
			if ($parts[0] === 'BEGIN') { /* the we are starting a new sub component - end of the properties, so drop down */
				if (in_array ($parts[1], $amr_validrepeatablecomponents)) {
					$subarray[$parts[1]][] = amr_parse_component($parts[1]);
				}
				else { 
					$subarray[$parts[1]] = amr_parse_component($parts[1]);
				}
			}
			else {
				if ($parts[0] === 'END') {
					if (empty($subarray)) return (false);
					return ($subarray );
				}
				/* now grab the value - just in case there may have been ";" in the value we will take all the rest of the string */
				else {
					if ($parts[0] === 'X-WR-TIMEZONE;VALUE=TEXT') 
						$parts[0] === 'X-WR-TIMEZONE';
						
					$basepart = explode (';', $parts[0], 2);  /* Looking for RRULE; something...*/
					
					//if (isset($_GET['debugcustom'])) {echo '<br />checking basepart '; var_dump($basepart);  echo ' against '; var_dump($amr_validrepeatableproperties); }
					
					if (in_array ($basepart[0], $amr_validrepeatableproperties)) {
						$temp = amr_parse_property ($parts);  
						// now this might return an array (eg categories), which can be multiple lines too
						
						if ($basepart[0] == 'CATEGORIES') {
						//if (is_array($temp)) {
							//if (WP_DEBUG) {echo '<br />Got an array:'.$basepart[0].' '; var_dump($temp);}
							if (!empty($subarray[$basepart[0]]) and is_array($subarray[$basepart[0]]))  {
							// ie we got an array already, must be multiple lines
								$subarray[$basepart[0]] = array_merge($subarray[$basepart[0]], $temp);

							}
							else $subarray[$basepart[0]]= $temp;
						}

						else $subarray[$basepart[0]][] = $temp;					
					}
					else {
						$subarray [$basepart[0]] = amr_parse_property($parts);
						if (($basepart[0] === 'DTSTART') and (isset($basepart[1]))) {
							if (amr_is_untimed($basepart[1])) { /* ie has VALUE=DATE */
								//$subarray ['Untimed'] = true; // removed v4.0.28
								//$subarray ['allday'] = true;  // v4.0.17
								$subarray ['allday'] = 'allday';  // v4.0.28
							}
						}
						else if (($basepart[0] === 'X-MOZ-GENERATION') and (!isset( $subarray ['SEQUENCE']))) {
							$subarray ['SEQUENCE'] = $subarray ['X-MOZ-GENERATION'] ;
						/* If we have an mozilla funny thing, convert it to the sequence if there is no sequence */
						}
						else {
							if (isset($_GET['debugcustom']) and ($basepart[0] === 'X-TRUMBA-CUSTOMFIELD')) { 
								echo '<br />Base Part = '.$basepart[0];
								//var_dump($subarray [$basepart[0]]);
								}
						}
					}
				}
			}
		}
	}
	return ($subarray);	/* return the possibly nested component */
}
/* ---------------------------------------------------------------------- */
// Parse the ical file and return an array ('Properties' => array ( name & params, value), 'Items' => array(( name & params, value), )
function amr_parse_ical ( $cal_file ) {
/* we will try to continue as much as possible, ignore lines that are problems */
	global $icsfile_tzname,
		$amr_lines,
		$amr_totallines,
		$amr_n,
		$amr_validrepeatablecomponents,
		$amr_last_modified;

	$icsfile_tzname = '';
    $line = 0;
    $event = '';
	//If (ICAL_EVENTS_DEBUG) { echo '<br />Calfile = '; var_dump($cal_file);echo '<br />';}
	$data = file_get_contents($cal_file);

		// Now fix folding.  According to RFC, lines can fold by having
		// a CRLF and then a single white space character.
		// We will allow it to be CRLF, CR or LF or any repeated sequence
		// so long as there is a single white space character next.

		/**** we may also need to cope with backslahed backslashes, commas, semicolons as per http://www.kanzaki.com/docs/ical/text.html*/

		$data = amr_remove_folding ($data);
	    $data = str_replace ( "\;", ";", $data );
	    $data = str_replace ( "\,", ",", $data );
		$amr_n = 0;
	    $amr_lines = explode ( "\n", $data );
		$amr_totallines = count ($amr_lines) - 1; /* because we start from 0 */
		If (ICAL_EVENTS_DEBUG) {
			echo '<br><b>Lines in ics file: '.$amr_totallines.'</b>' ;
			//echo '<br />first line: ';	var_dump($amr_lines);
			echo '<br />';
			}

		$parts = explode (':', $amr_lines[$amr_n],2 ); 
		/* explode faster than the preg, just split first : */

		if ($parts[0] === 'BEGIN') {
		
			$ical = amr_parse_component('VCALENDAR');
			
			if (!empty ($amr_last_modified)) 
				$ical['LastModificationTime'] = $amr_last_modified;
			
			//if (isset($_GET['debugcustom'])) {var_dump($ical);}
			
			return($ical);
			}
		else {
			If (ICAL_EVENTS_DEBUG) {
				echo '<br>VCALENDAR not found in file:'.$cal_file;
				echo '<br>Line has: '.$amr_lines[$amr_n] ;
				}
			return false;
			}

}
	
/* ---------------------------------------------------------------------- */
function amr_deal_with_tzpath_in_date ( $tzstring )	{
   /* Receive something like   /mozilla.org/20070129_1/Europe/Berlin
	and return a tz object */
		$tz = explode ('/',$tzstring);
		$l = count ($tz);
		if ($l>1) {
			$tzid= $tz[$l-2].'/'.$tz[$l-1];
		}
		else $tzid = $tz[0] ;
		$tzobj = timezone_open  ( $tzid );
		If (ICAL_EVENTS_DEBUG or isset ($_REQUEST['tzdebug'])) {
			echo '<br />Timezone Reduced to: '.$tzid.' Result of timezone object creation:';
			print_r($tzobj);
		}
		return ($tzobj);
	}
/* ---------------------------------------------------------------------- */	
function amr_get_timezone_cities () { //
$timezone_identifiers = DateTimeZone::listIdentifiers();
	    
/* Africa/Abidjan

Africa/Accra

Africa/Addis_Ababa

Africa/Algiers

Africa/Asmara

*/

	    foreach( $timezone_identifiers as $i=> $value ){
			$c = explode("/",$value);//obtain continent,city
			$tzcities[$value]['continent'] = $c[0];

	        if (isset($c[1])) 
				$tzcities[$c[1]] = $value;
			else 
				$tzcities[$c[0]] = $value;

	     }
		
		return ($tzcities);
	}
/* ---------------------------------------------------------------------- */
function amr_remove_folding ($data) {
		$data = preg_replace ( "/[\r\n]+ /", "", $data );
	    $data = preg_replace ( "/[\r\n]+/", "\n", $data );
	return($data);
}
/* ---------------------------------------------------------------------- */
// Replace RFC 2445 escape characters
function amr_format_ical_text($value) {
  $output = str_replace(
    array('\\\\', '\;', '\,', '\N', '\n'),
    array('\\',   ';',  ',',  "\n", "\n"),
    $value
  );
  return $output;
}
/* ---------------------------------------------------------------------- */
function amr_is_untimed($text) {
/*  checks for VALUE=DATE */
if (stristr ($text, 'VALUE=DATE') and (!stristr($text, 'VALUE=DATE-TIME'))) return (true);
else return (false);
}
/* ---------------------------------------------------------------------- */
function amr_track_last_mod($date) {
global $amr_last_modified; $amr_globaltz;
	if (empty ($amr_last_modified)) 
		$amr_last_modified = amr_newDateTime('0000-00-00 00:00:01');
	if ($date->format('c') > $amr_last_modified->format('c')) {
		$amr_last_modified = clone ($date);
		}
}

