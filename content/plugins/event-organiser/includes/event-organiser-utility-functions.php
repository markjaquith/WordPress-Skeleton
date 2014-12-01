<?php
/**
 * Utility functions
 *
 * @package utility-functions
*/

/**
 * Formats a datetime object into a specified format and handles translations.
 * Used by 
 *
 * * {@see `eo_get_the_start()`} 
 * * {@see `eo_get_the_end()`}
 * * {@see `eo_get_schedule_start()`}
 * * {@see `eo_get_schedule_last()`}
 *
 * The constant DATETIMEOBJ can be passed to them to get datetime objects 
 * Applies {@see `eventorganiser_format_datetime`} filter
 *
 * @since 1.2.0
 * @link https://php.net/manual/en/function.date.php PHP Date
 *
 * @param dateTime $datetime The datetime to format
 * @param string|constant $format How to format the date, see https://php.net/manual/en/function.date.php  or DATETIMEOBJ constant to return the datetime object.
 * @return string|dateTime The formatted date
*/

function eo_format_datetime($datetime,$format='d-m-Y'){
	global  $wp_locale;

	if( DATETIMEOBJ == $format )
		return $datetime;

	if ( ( !empty( $wp_locale->month ) ) && ( !empty( $wp_locale->weekday ) ) ) :
			//Translate
			$datemonth = $wp_locale->get_month($datetime->format('m'));
			$datemonth_abbrev = $wp_locale->get_month_abbrev($datemonth);
			$dateweekday = $wp_locale->get_weekday($datetime->format('w'));
			$dateweekday_abbrev = $wp_locale->get_weekday_abbrev( $dateweekday );
			$datemeridiem =  trim($wp_locale->get_meridiem($datetime->format('a')));
			$datemeridiem_capital =$wp_locale->get_meridiem($datetime->format('A'));

			$datemeridiem = (empty($datemeridiem) ? $datetime->format('a')  : $datemeridiem);
	
			$dateformatstring = ' '.$format;
			$dateformatstring = preg_replace( "/([^\\\])D/", "\\1" . backslashit( $dateweekday_abbrev ), $dateformatstring );
			$dateformatstring = preg_replace( "/([^\\\])F/", "\\1" . backslashit( $datemonth ), $dateformatstring );
			$dateformatstring = preg_replace( "/([^\\\])l/", "\\1" . backslashit( $dateweekday ), $dateformatstring );
			$dateformatstring = preg_replace( "/([^\\\])M/", "\\1" . backslashit( $datemonth_abbrev ), $dateformatstring );
			$dateformatstring = preg_replace( "/([^\\\])a/", "\\1" . backslashit( $datemeridiem ), $dateformatstring );
			$dateformatstring = preg_replace( "/([^\\\])A/", "\\1" . backslashit( $datemeridiem_capital ), $dateformatstring );
			$dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) -1 );
	 endif;	
	$formatted_datetime = $datetime->format($dateformatstring);
	
	/**
	 * Filters the formatted date (DateTime object).
	 * 
	 * Formats should be specified using [php date format standards](https://php.net/manual/en/function.date.php).
	 *
	 * @link https://php.net/manual/en/function.date.php PHP date formatting standard
	 * @param string $formatted_datetime The formatted date.
	 * @param string $format             The format in which the date should be returned.
	 * @param string $datetime           The provided DateTime object
	 */
	$formatted_datetime = apply_filters('eventorganiser_format_datetime', $formatted_datetime , $format, $datetime);
	return $formatted_datetime;
}

/**
* Formats a date string in the format 'YYYY-MM-DD H:i:s' format or even
 * relative strings like 'today' into a specified format.
 *
 * @uses eo_format_datetime()
 * @since 1.2.0
 * @link https://php.net/manual/en/function.date.php PHP Date
 *
 * @param string $dateString The date as a string
 * @param string $format How to format the date. DATETIMEOBJ for datetime object.
 * @return string|dateTime The formatted date
*/
function eo_format_date($dateString='',$format='d-m-Y'){

	if($format!=''&& $dateString!=''){
		$datetime = new DateTime($dateString, eo_get_blog_timezone());
		$formated =  eo_format_datetime($datetime,$format);
		return $formated;
	}
	return false;
}

/**
 *  Returns the blog timezone
 *
 * Gets timezone settings from the db. If a timezone identifier is used just turns
 * it into a DateTimeZone. If an offset is usd, it tries to find a suitable timezone.
 * If all else fails it uses UTC.
 *
 * @since 1.3.0
 *
 * @return DateTimeZone The blog timezone
*/
function eo_get_blog_timezone(){
	$tzstring = wp_cache_get( 'eventorganiser_timezone' );

	if ( false === $tzstring ) {

		$tzstring =get_option('timezone_string');
		$offset = get_option('gmt_offset');

		// Remove old Etc mappings.  Fallback to gmt_offset.
		if ( !empty($tz_string) && false !== strpos($tzstring,'Etc/GMT') )
			$tzstring = '';

		if( empty($tzstring) && $offset!=0 ):
			//use offset		
			$offset *= 3600; // convert hour offset to seconds
			$allowed_zones = timezone_abbreviations_list();

			foreach ($allowed_zones as $abbr):
				foreach ($abbr as $city):
					if ($city['offset'] == $offset){
						$tzstring=$city['timezone_id'];
						break 2;
					}
				endforeach;
			endforeach;
		endif;

		//Issue with the timezone selected, set to 'UTC'
		if( empty($tzstring) ):
			$tzstring = 'UTC';
		endif;

		//Cache timezone string not timezone object
		//Thanks to Ben Huson https://wordpress.org/support/topic/plugin-event-organiser-getting-500-is-error-when-w3-total-cache-is-on
		wp_cache_set( 'eventorganiser_timezone', $tzstring );
	} 

	if( $tzstring instanceof DateTimeZone)
		return $tzstring;

	$timezone = new DateTimeZone($tzstring);
	return $timezone; 
}


/**
 * Calculates and formats an interval between two days, passed in any order.
 * It's a PHP 5.2 workaround for {@link https://www.php.net/manual/en/dateinterval.format.php date interval format}
 * 
 * This does not correctly handle DST but instead mimics the same buggy behaviour exphibted by PHP's date interval. 
 * See https://bugs.php.net/bug.php?id=63953
 * 
 * @see https://bugs.php.net/bug.php?id=63953
 * 
 * @since 1.5
 *
 * @param dateTime $_date1 One date to compare
 * @param dateTime $_date2 Second date to compare
 * @param string $format Used to format the interval. See https://www.php.net/manual/en/dateinterval.format.php
 * @return string Formatted interval.
*/
function eo_date_interval($_date1,$_date2, $format){

	//Calculate R values (signs)
	$R = ($_date1 <= $_date2 ? '+' : '-');
	$r = ($_date1 <= $_date2 ? '' : '-');

	//Make sure $date1 is ealier
	$date1 = clone ($_date1 <= $_date2 ? $_date1 : $_date2);
	$date2 = clone ($_date1 <= $_date2 ? $_date2 : $_date1);

	//Calculate total days difference
	$total_days = floor(abs($date1->format('U') - $date2->format('U'))/86400);

	$periods = array( 'years'=>-1,'months'=>-1,'days'=>-1,'hours'=>-1);

	foreach ($periods as $period => &$i ){

		$temp_pointer = clone $date1;
		
		while( $temp_pointer <= $date2 ){
			$temp_pointer->modify( "+1 $period" );
			$i++;
		}
		
		if ( $i > -1 ){
			$date1->modify( "+$i $period" );
		}else{
			$date1->modify( "$i $period" );
		}
		
	}
	extract($periods);

	//Minutes, seconds
	$seconds = round(abs($date1->format('U') - $date2->format('U')));
	$minutes = floor($seconds/60);
	$seconds = $seconds - $minutes*60;

	$chars = str_split( $format );
	$length = count( $chars );
	
	if( $length == 1 ){
		return $format;
	}
	
	$values = array(
			'%' => "%",
			'y' => $years,
			'Y' => zeroise( $years, 2 ),
			'm' => $months,
			'M' => zeroise( $months, 2 ),
			'd' => $days,
			'D' => zeroise( $days, 2 ),
			'a' => $total_days,
			'h' => $hours,
			'H' => zeroise( $hours, 2 ),
			'i' => $minutes,
			'I' => zeroise( $minutes, 2 ),
			's' => $seconds,
			'S' => zeroise( $seconds, 2 ),
			'r' => $r,
			'R' => $R,
	);
	
	$result = "";
	$previous_char_processed =  false;
	
	for( $i = 0; $i < $length; $i++ ){
	
		if( ( $i > 0 && $chars[$i-1] === '%' ) && !$previous_char_processed ){
				
			if( isset( $values[$chars[$i]] ) ){
				$result .= $values[$chars[$i]];
				$previous_char_processed = true;
								
			}else{
				$result .= $chars[$i-1].$chars[$i];
				$previous_char_processed = true;
			}
			
					
		}elseif( $chars[$i] == '%' ){
			$previous_char_processed = false;
			
		}else{
			$result .= $chars[$i];
			$previous_char_processed = true;
		}
		//var_dump($result);
	}
	
	return $result;
}	 

/**
* Very basic class to convert php date format into xdate date format used for javascript.
*
* Takes a php date format and converts it to {@link http://arshaw.com/xdate/#Formatting xdate format} so
* that it can b used in javascript (notably the fullCalendar).
*
* Doesn't support
* 
* * L Whether it's a leap year
* * N ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0)
* * w Numeric representation of the day of the week (0=sun,...)
* * z The day of the year (starting from 0)
* * t Number of days in the given month
* * B Swatch Internet time
* * u microseconds
* * e 	Timezone identifier (added in PHP 5.1.0) 	Examples: UTC, GMT, Atlantic/Azores
* * I (capital i) 	Whether or not the date is in daylight saving time 	1 if Daylight Saving Time, 0 otherwise.
* * O  Difference to Greenwich time (GMT) in hours 	Example: +0200
* * T  Timezone abbreviation 	Examples: EST, MDT ...
* * Z  Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive.
* * c  ISO 8601 date (added in PHP 5) 	2004-02-12T15:19:21+00:00
* * r  RFC 2822 formatted date 	Example: Thu, 21 Dec 2000 16:01:07 +0200
* * U Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT) 	See also time()
*
* @since 2.1.3
*
*@param string $phpformat Format according to https://php.net/manual/en/function.date.php
*@return string The format translated to xdate format: http://arshaw.com/xdate/#Formatting
*/
function eo_php2xdate($phpformat=""){
	$php2xdate = array(
		'Y'=>'yyyy','y'=>'yy','L'=>''/*Not Supported*/,'o'=>'I',
		'j'=>'d','d'=>'dd','D'=>'ddd','l'=>'dddd','N'=>'', /*NS*/ 'S'=>'S',
		'w'=>'', /*NS*/ 'z'=>'',/*NS*/ 'W'=>'w',
		'F'=>'MMMM','m'=>'MM','M'=>'MMM','n'=>'M','t'=>'',/*NS*/
		'a'=>'tt','A'=>'TT',
		'B'=>'',/*NS*/'g'=>'h','G'=>'H','h'=>'hh','H'=>'HH','u'=>'fff',
		'i'=>'mm','s'=>'ss',
		'O'=>'zz ', 'P'=>'zzz',
		'c'=>'u',
	);
		
	$xdateformat="";

	for($i=0;  $i< strlen($phpformat); $i++){

		//Handle backslash excape
		if($phpformat[$i]=="\\"){
			$xdateformat .= "\\".$phpformat[$i+1];
			$i++;
			continue;
		}

		if(isset($php2xdate[$phpformat[$i]])){
			$xdateformat .= $php2xdate[$phpformat[$i]];
		}else{
			$xdateformat .= $phpformat[$i];
		}
	}
	return $xdateformat;
}

/**
 * Very basic class to convert php date format into xdate date format used for javascript.
 * @deprecated 2.1.3
 * @ignore
 * @since 1.4
 */
function eventorganiser_php2xdate( $phpformat="" ){
	return eo_php2xdate( $phpformat );
}



/**
 * Very basic class to convert php date format into jQuery UI date format used for javascript.
 * @ignore
 * @deprecated 2.1.3 Use 
 * @since 1.7
 */
function eventorganiser_php2jquerydate( $phpformat="" ){
	return eo_php2jquerydate( $phpformat );
}


/**
 * Very basic class to convert php date format into jQuery UI date format used for javascript.
 *
 * Similar to `{@see eventorganiser_php2xdate()}` - but the format is slightly different for jQuery UI  
 * Takes a php date format and converts it to {@link http://docs.jquery.com/UI/Datepicker/formatDate} so
 * that it can b used in javascript (notably by the datepicker).
 * 
 * **Please note that this function does not convert time formats**
 *
 * @since 2.1.3
 *
 *@param string $phpformat Format according to https://php.net/manual/en/function.date.php
 *@return string The format translated to xdate format: http://docs.jquery.com/UI/Datepicker/formatDate
 */
function eo_php2jquerydate($phpformat=""){
	$php2jquerydate = array(
			'Y'=>'yy','y'=>'y','L'=>''/*Not Supported*/,'o'=>'',/*Not Supported*/
			'j'=>'d','d'=>'dd','D'=>'D','DD'=>'dddd','N'=>'',/*NS*/ 'S' => ''/*NS*/,
			'w'=>'', /*NS*/ 'z'=>'o',/*NS*/ 'W'=>'w',
			'F'=>'MM','m'=>'mm','M'=>'M','n'=>'m','t'=>'',/*NS*/
			'a'=>''/*NS*/,'A'=>''/*NS*/,
			'B'=>'',/*NS*/'g'=>''/*NS*/,'G'=>''/*NS*/,'h'=>''/*NS*/,'H'=>''/*NS*/,'u'=>'fff',
			'i'=>''/*NS*/,'s'=>''/*NS*/,
			'O'=>''/*NS*/, 'P'=>''/*NS*/,
	);

	$jqueryformat="";

	for($i=0;  $i< strlen($phpformat); $i++){

		//Handle backslash excape
		if($phpformat[$i]=="\\"){
			$jqueryformat .= "\\".$phpformat[$i+1];
			$i++;
			continue;
		}

		if(isset($php2jquerydate[$phpformat[$i]])){
			$jqueryformat .= $php2jquerydate[$phpformat[$i]];
		}else{
			$jqueryformat .= $phpformat[$i];
		}
	}
	return $jqueryformat;
}


/**
* A utilty function intended for removing duplicate DateTime objects from array
* @since 1.5
 *@access private
* @ignore
*
* @param array $array Array of DateTime objects (though can work for other objects)
* @return array A sub-array of the passed array, containing only unique elements.
*/
function _eventorganiser_remove_duplicates( $array=array() ){
	//Do we need to worry about the times of the date-time objects?

	if( empty($array) )
		return $array;

	$unique = array();
	foreach ($array as $key=>$object){
		if( !in_array($object, $unique) )
			$unique[$key] = $object;
        }

        return $unique;
} 


/**
 * Utility function Compares two DateTime object, 
 * Returns +1 if the first date is after, -1 if its before or 0 if they're the same
 * Note: does not compare *times*. 
 * Used to filter occurrances with udiff
 *
 * @access private
*  @ignore
 * @since 1.0.0
 *
 * @param dateTime $date1 The first date to compare
 * @param dateTime $date2 The second date to compare
 * @return int 1 | 0 | -1
 */
function _eventorganiser_compare_dates($date1,$date2){
	//Don't wish to compare times
	if($date1->format('Ymd') == $date2->format('Ymd'))
		return 0;

	 return ($date1 > $date2)? 1:-1;
}



/**
 * A workaround which handles "[ordinal] [day] of +(n) month" statements when
 * using php 5.2.
 *
 * @access private
 * @ignore
 * @since 1.3
 *
 * @param dateTime $date dateTime object to modify
 * @param string $modify How to modify the date: e.g. 'last Sunday of +2 month'
 * @return dateTime the modified dateTime object. DateTime is set to blog tz.
 */
function _eventorganiser_php52_modify($date='',$modify=''){
	
	//Expect e.g. 'Second Monday of +2 month
	$pattern = '/([a-zA-Z]+)\s([a-zA-Z]+) of ([\+|-]?\d+) month/i';
	preg_match($pattern, $modify, $matches);

	$ordinal = array_search(strtolower($matches[1]), array('last','first','second','third','fourth') ); //0-4
	$day = array_search(strtolower($matches[2]), array('sunday','monday','tuesday','wednesday','thursday','friday','saturday') ); //0-6
	$freq = intval($matches[3]);

	//set to first day of month
	$date = date_create($date->format('Y-m-1'));

	//first day of +$freq months
	$date->modify('+'.$freq.' month');

	//Calculate offset to day of week	
	if($ordinal >0):
		$offset = ($day-intval($date->format('w')) +7)%7;//Offset to first weekday of that month (e.g. first Monday)
		$d =($offset) +7*($ordinal-1) +1; //If wanting to get second, third or fourth monday add multiples of 7.

	else:
		//Ordinal is 'Last'
		$date = date_create($date->format('Y-m-t'));//Last day
		$offset = intval(($date->format('w')-$day+7)%7);//Find offset from last day to the last weekday of month (e.g. last Sunday)
		$d = intval($date->format('t'))-$offset;
	endif;

	$date = date_create($date->format('Y-m-'.$d), eo_get_blog_timezone());

	return $date;
}

/**
* Generates excerpt from contant if needed, and trims to a given length
*
* Is very simliar to wp_trim_excerpt. Doesn't apply excerpt_more filter
 * Applies eventorganiser_trim_excerpt filter
 * Must be used inside the loop
 * @ignore
 * @access private
 * @since 1.5
 * @param string $text Optional. The excerpt. If set to empty, an excerpt is generated.
 * @param int $excerpt_length The excerpt length if generated from content.
 * @return string The excerpt.
*/
function eventorganiser_trim_excerpt($text = '', $excerpt_length=55) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');
		$text = strip_shortcodes( $text );
		/**
		 * @ignore
		 */
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = wp_trim_words( $text, $excerpt_length, '...' );
	}
	/**
	 * Filter the event's trimmed excerpt string.
	 *
	 * @param string $text        The trimmed text.
	 * @param string $raw_excerpt The text prior to trimming.
	 */
	$text = apply_filters('eventorganiser_trim_excerpt', $text, $raw_excerpt);
	return $text;
}


/**
 * A helper function, creates a DateTime object from a date string and sets the timezone to the blog's current timezone
 *
 *@since 1.3
 *@uses date_create()
 *@param string $datetime_string A date-time in string format
 *@return datetime The corresponding DateTime object.
*/
function eventorganiser_date_create($datetime_string){
	return date_create( $datetime_string, eo_get_blog_timezone() );
}


function eo_check_datetime( $format, $datetime_string, $timezone = false ){
	
	$timezone = ( $timezone instanceof DateTimeZone ) ? $timezone : eo_get_blog_timezone();
	
	global $wp_locale;
	
	//Handle localised am/pm
	$am = $wp_locale->get_meridiem('am');
	$AM = $wp_locale->get_meridiem('AM');
	$pm = $wp_locale->get_meridiem('pm');
	$PM = $wp_locale->get_meridiem('PM');
	
	$meridan = array_filter( compact( 'am', 'AM', 'pm', 'PM' ), 'trim' );
	$meridan = array_filter( $meridan );
	if( $meridan ){
		$datetime_string = str_replace( array_values( $meridan ), array_keys( $meridan ), $datetime_string );
	}

	if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ){
    	return date_create_from_format( $format, $datetime_string, $timezone );
	}elseif( function_exists( 'strptime' ) ){
		
		//Workaround for outdated php versions. Limited support, see conversion array below.
		
		//Format conversion
		$format_conversion = array(
			'Y' => '%Y', 
			'm'	=> '%m', 'F' => '%B', 
			'd'	=> '%d', 'j' => '%e',
			'S' => '%0',
			'H' => '%H', 'G' => '%H', 'h'=> '%I', 'g' => '%I', 
			'i' => '%M', 
			's' => '%S', 
			'a' => '%p', 'A' => '%P'
		);
		
		$strptime_format = str_replace(
			array_keys( $format_conversion ),
			array_values( $format_conversion ),
			$format
		);

		$strptime = strptime( $datetime_string, $strptime_format );

		if( false == $strptime || !array_filter( $strptime ) ){
			return false;
		}
		
		$ymdhis = sprintf(
				'%04d-%02d-%02d %02d:%02d:%02d',
				$strptime['tm_year'] + 1900, 
				$strptime['tm_mon'] + 1,
				$strptime['tm_mday'],
				$strptime['tm_hour'],
				$strptime['tm_min'],
				$strptime['tm_sec']
		);

		try {
			$date = new DateTime( $ymdhis, $timezone );
			return $date;
		} catch (Exception $e) {			
			return false;
		}
		
	}else{

		//Workaround for outdated php versions without strptime. Limited support, see conversion array below.
		
		//Format conversion
		$format_conversion = array(
				'Y' => '(?P<year>\d{4})',
				'm'	=> '(?P<month>\d{1,})', 
				'd'	=> '(?P<day>\d{1,})', 
				'H' => '(?P<hour>\d{2})', 
				'G' => '(?P<hour>\d{1,2})',
				'h' => '(?P<hour>\d{2})', 
				'g' => '(?P<hour>\d{1,2})',
				'i' => '(?P<minute>\d{1,2})',
				's' => '(?P<second>\d{1,2})',
				'a' => '(?P<meridan>(am|pm))',
				'A' => '(?P<meridan>(AM|PM))'
		);
		
		$reg_exp = "/^" . strtr( $format, $format_conversion ) . "$/";
		
		if( !preg_match( $reg_exp, $datetime_string, $matches ) ){
			return false;
		}
		
		$meridan    = isset( $matches['meridan'] ) ? strtolower( $matches['meridan'] ) : false;
		$components = eo_array_key_whitelist( $matches, array( 'year', 'month', 'day', 'hour', 'minute', 'second' ) );
		extract( array_map( 'intval', $components ) );
		
		if ( !checkdate( $month, $day, $year ) ){
			return false;
		}
		
		$datetime = new DateTime( null, $timezone );
		$datetime->setDate( $year, $month, $day );
		
		if( isset( $hour ) && isset( $minute ) ){
			if( $hour < 0 || $hour > 23 || $minute < 0 || $minute > 59 ){
				return false;
			}
			$hour = ( $meridan === 'pm' && $hour < 12 ) ? $hour + 12 : $hour; //acount for 12 hour time
			$datetime->setTime( $hour, $minute );
		}
		
		return $datetime;
	
	}
		
}
/**
 * (Private) Utility function checks a date-time string is formatted correctly (according to the options) 
 * @access private
 * @ignore
 * @since 1.0.0

 * @param datetime_string - a datetime string 
 * @param string $format - Format of the datetime string. One of 'd-m-Y H:i', 'm-d-Y H:i' and 'Y-m-d H:i'.
 * @return int DateTime| false - the parsed datetime string as a DateTime object or false on error (incorrectly formatted for example)
 */
function _eventorganiser_check_datetime( $datetime_string = '', $format = null ){

	if( is_null( $format ) )
		$format = eventorganiser_get_option('dateformat');
	
	//Backwards compatible - can probably remove 2.1+
	if( is_bool( $format ) ){
		_deprecated_argument('_eventorganiser_check_datetime', '1.8', 'This function no longer accepts a boolean, pass the format of the passed date-time string.');
		if( true === $format )
			$format = 'Y-m-d';
		else
			$format = eventorganiser_get_option('dateformat');
	}

	//Get regular expression.
	if( $format == 'Y-m-d' ){
		$reg_exp = "/(?P<year>\d{4})[-.\/](?P<month>\d{1,})[-.\/](?P<day>\d{1,}) (?P<hour>\d{2}):(?P<minute>\d{2})/";

	}elseif( $format == 'd-m-Y' ){
		$reg_exp = "/(?P<day>\d{1,})[-.\/](?P<month>\d{1,})[-.\/](?P<year>\d{4}) (?P<hour>\d{2}):(?P<minute>\d{2})/";

	}else{
		$reg_exp = "/(?P<month>\d{1,})[-.\/](?P<day>\d{1,})[-.\/](?P<year>\d{4}) (?P<hour>\d{2}):(?P<minute>\d{2})/";
	}

	if( !preg_match($reg_exp, $datetime_string,$matches) ) 
		return false;

	extract(array_map('intval',$matches));

	if ( !checkdate($month, $day, $year) || $hour < 0 || $hour > 23 || $minute < 0 || $minute > 59 )
		return false;

	$datetime = new DateTime(null, eo_get_blog_timezone());
	$datetime->setDate($year, $month, $day);
	$datetime->setTime($hour, $minute);
	return $datetime;
}

/**
 * Utility function for printing/returning radio boxes
 *
 * The $args array - excepts the following keys
 *
 * * **id** - The id of the radiobox (alias: label_for)
 * * **name** - The name of the radiobox
 * * **checked** - The the value to have checked
 * * **options** - Array of options in 'value'=>'Label' format
 * * **label** - The label for the radiobox field set
 * * **class** - Class to be added to the radiobox field set
 * * **echo** - Whether to print the mark-up or just return it
 * * **help** - Optional, help text to accompany the field.
 *
 * @access private
 * @param $args array The array of arguments
 */
function eventorganiser_radio_field( $args ){

	$args = wp_parse_args($args,array(
			'checked'=>'', 'help' => '', 'options'=>'', 'name'=>'', 'echo'=>1,
			'class'=>'', 'label' => '','label_for'=>''
			));	

	$id = ( !empty($args['id']) ? $args['id'] : $args['label_for']);
	$name = isset($args['name']) ?  $args['name'] : '';
	$checked = $args['checked'];
	$label = !empty($args['label']) ? '<legend><label>'.esc_html($args['label']).'</label></legend>' : '';
	$class =  !empty($args['class']) ? 'class="'.sanitize_html_class($args['class']).'"'  : '';

	$html = sprintf('<fieldset %s> %s', $class, $label);
	if( !empty($args['options']) ){
		foreach ($args['options'] as $value => $opt_label ){
			$html .= sprintf('<label for="%1$s"><input type="radio" id="%1$s" name="%3$s" value="%4$s" %2$s> <span> %5$s </span></label><br>',
				esc_attr($id.'_'.$value),
				checked($value, $checked, false),
				esc_attr($name),
				esc_attr($value),
				esc_html($opt_label));
		}
	}
	if(!empty($args['help'])){
		$html .= '<p class="description">'.esc_html($args['help']).'</p>';
	}
	$html .= '</fieldset>';

	if( $args['echo'] )
		echo $html;

	return $html;
}


/**
 * Utility function for printing/returning select field
 *
 * The $args array - excepts the following keys
 *
 * * **id** - The id of the select box (alias: label_for)
 * * **name** - The name of the select box
 * * **selected** - The the value to have selected
 * * **options** - Array of options in 'value'=>'Label' format
* * **multiselect** True or False for multi-select
 * * **label** - The label for the radiobox field set
 * * **class** - Class to be added to the radiobox field set
 * * **echo** - Whether to print the mark-up or just return it
 * * **help** - Optional, help text to accompany the field.
 *
 * @access private
 * @param $args array The array of arguments
 */
function eventorganiser_select_field($args){

	$args = wp_parse_args($args,array(
		'selected'=>'', 'help' => null, 'options'=>'', 'name'=>'', 'echo'=>1,
		'label_for'=>'','class'=>'','disabled'=>false,'multiselect'=>false,
		'inline_help' => false, 'style' => false, 'data' => false,
	));	
	

	$id = ( !empty($args['id']) ? $args['id'] : $args['label_for']);
	$name = isset($args['name']) ?  $args['name'] : '';
	$selected = $args['selected'];
	$classes = array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) );
	$class = implode( ' ', $classes );
	$multiselect = ($args['multiselect'] ? 'multiple' : '' );
	$disabled = ($args['disabled'] ? 'disabled="disabled"' : '' );
	$style = (  !empty($args['style']) ?  sprintf('style="%s"', $args['style']) : '' );
	
	//Custom data-* attributes
	$data = '';
	if( !empty( $args['data'] ) && is_array( $args['data'] ) ){
		foreach( $args['data'] as $key => $attr_value ){
			$data .= sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( $attr_value ) );
		}
	}
	
	$html = sprintf('<select %s name="%s" id="%s" %s>',
		!empty( $class ) ? 'class="'.$class.'"'  : '',
			esc_attr($name),
			esc_attr($id),
			$multiselect.' '.$disabled.' '.$style. ' '.$data
		);
		if( !empty( $args['show_option_all'] ) ){
			$html .= sprintf('<option value="" %s> %s </option>',selected( empty($selected), true, false ), esc_html( $args['show_option_all'] ) );
		}

		if( !empty($args['options']) ){
			foreach ($args['options'] as $value => $label ){
				if( $args['multiselect'] && is_array($selected) )
					$_selected = selected( in_array($value, $selected), true, false);
				else
					$_selected =  selected($selected, $value, false);

				$html .= sprintf('<option value="%s" %s> %s </option>',esc_attr($value),$_selected, esc_html($label));
			}
		}
	$html .= '</select>'. $args['inline_help'];

	if( isset( $args['help'] ) ){
		$html .= '<p class="description">'.esc_html($args['help']).'</p>';
	}

	if( $args['echo'] )
		echo $html;

	return $html;
}


/**
 * Utility function for printing/returning text field
 *
 * The $args array - excepts the following keys
 *
 * * **id** - The id of the select box (alias: label_for)
 * * **name** - The name of the select box
 * * **value** - The value of the text field
 * * **type** - The type  of the text field (e.g. 'text','hidden','password')
 * * **options** - Array of options in 'value'=>'Label' format
 * * **label** - The label for the radiobox field set
 * * **class** - Class to be added to the radiobox field set
 * * **echo** - Whether to print the mark-up or just return it
 * * **help** - Optional, help text to accompany the field.
 *
 * @access private
 * @param $args array The array of arguments
 */
function eventorganiser_text_field($args){

	$args = wp_parse_args( $args,
		array(
		 	'type' => 'text', 'value'=>'', 'placeholder' => '','label_for'=>'', 'inline_help' => false,
			 'size'=>false, 'min' => false, 'max' => false, 'style'=>false, 'echo'=>true, 'data'=>false,
			'class' => false, 'required' => false,
			)
		);		

	$id = ( !empty($args['id']) ? $args['id'] : $args['label_for']);
	$name = isset($args['name']) ?  $args['name'] : '';
	$value = $args['value'];
	$type = $args['type'];
	$classes = array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) );
	$class = implode( ' ', $classes );

	$min = (  $args['min'] !== false ?  sprintf('min="%d"', $args['min']) : '' );
	$max = (  $args['max'] !== false ?  sprintf('max="%d"', $args['max']) : '' );
	$size = (  !empty($args['size']) ?  sprintf('size="%d"', $args['size']) : '' );
	$style = (  !empty($args['style']) ?  sprintf('style="%s"', $args['style']) : '' );
	$placeholder = ( !empty( $args['placeholder'] ) || is_numeric( $args['placeholder'] ) ) ? sprintf('placeholder="%s"', $args['placeholder']) : '';
	$disabled = ( !empty($args['disabled']) ? 'disabled="disabled"' : '' );
	$required = ( !empty($args['required']) ? 'required="required"' : '' );
	

	//Custom data-* attributes
	$data = '';
	if( !empty( $args['data'] ) && is_array( $args['data'] ) ){
		foreach( $args['data'] as $key => $attr_value ){
			$data .= sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( $attr_value ) );
		}
	}
	
	

	$attributes = array_filter( array($min,$max,$size,$placeholder,$disabled, $disabled, $style, $data ) );

	$html = sprintf('<input type="%s" name="%s" class="%s" id="%s" value="%s" autocomplete="off" %s /> %s',
		esc_attr( $type ), 
		esc_attr( $name ),
		$class,
		esc_attr( $id ),
		esc_attr( $value ),
		implode(' ', $attributes),
		 $args['inline_help']
	);

	if( isset($args['help']) ){
		$html .= '<p class="description">'.$args['help'].'</p>';
	}

	if( $args['echo'] )
		echo $html;

	return $html;
}
	

/**
 * Utility function for printing/returning text field
 *
 * The $args array - excepts the following keys
 *
 * * **id** - The id of the checkbox (alias: label_for)
 * * **name** - The name of the select box
 * * **options** - Single or Array of options in 'value'=>'Label' format
 * * **values** - The values of the text field
 * * **type** - The type  of the text field (e.g. 'text','hidden','password')

 * * **label** - The label for the radiobox field set
 * * **class** - Class to be added to the radiobox field set
 * * **echo** - Whether to print the mark-up or just return it
 * * **help** - Optional, help text to accompany the field.
 *
 * @access private
 * @param $args array The array of arguments
 */
function eventorganiser_checkbox_field($args=array()){

	$args = wp_parse_args($args,array(
		 	 'help' => '','name'=>'', 'class'=>'', 'label_for' => '',
			'checked'=>'', 'echo'=>true,'multiselect'=>false
		));

	$id = ( !empty($args['id']) ? $args['id'] : $args['label_for']);
	$name = isset($args['name']) ?  $args['name'] : '';
	$class = ( $args['class'] ? "class='".sanitize_html_class($args['class'])."'"  :"" );

	/* $options and $checked are either both arrays or they are both strings. */
	$options =  isset($args['options']) ? $args['options'] : false;
	$checked =  isset($args['checked']) ? $args['checked'] : 1;
	
	//Custom data-* attributes
	$data = '';
	if( !empty( $args['data'] ) && is_array( $args['data'] ) ){
		foreach( $args['data'] as $key => $attr_value ){
			$data .= sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( $attr_value ) );
		}
	}

	$html ='';
	if( is_array($options) ){
		foreach( $options as $value => $opt_label ){
			$html .= sprintf(
				'<label for="%1$s">
					<input type="checkbox" name="%2$s" id="%1$s" value="%3$s" %4$s %5$s> 
					%6$s </br>
				</label>',
				esc_attr( $id.'_'.$value ),
				esc_attr( trim( $name ) . '[]' ),
				esc_attr( $value ),
				checked( in_array( $value, $checked ), true, false ),
				$class,
				esc_attr( $opt_label )
			);
		}
	}else{
		$html .= sprintf('<input type="checkbox" id="%1$s" name="%2$s" %3$s %4$s value="%5$s">',
							esc_attr($id),
							esc_attr($name),
							checked( $checked, $options, false ),
							$class,
							esc_attr($options)
							);
	}
	
	if(!empty($args['help'])){
		$html .= '<p class="description">'.$args['help'].'</p>';
	}

	if( $args['echo'] )
		echo $html;

	return $html;
}



/**
 * Utility function for printing/returning text area
 *
 * The $args array - excepts the following keys
 *
 * * **id** - The id of the checkbox (alias: label_for)
 * * **name** - The name of the select box
 * * **options** - Single or Array of options in 'value'=>'Label' format
 * * **tinymce** Whether to use the TinyMCE editor. The TinyMCE prints directly.
 * * **value** - The value of the text area
 * * **rows** - The number of rows. Default 5.
 * * **cols** - The number of columns. Default 50.
 * * **class** - Class to be added to the textarea
 * * **echo** - Whether to print the mark-up or just return it
 * * **help** - Optional, help text to accompany the field.
 *
 * @access private
 * @param $args array The array of arguments
 */
function eventorganiser_textarea_field($args){

	$args = wp_parse_args($args,array(
	 	'type' => 'text', 'value'=>'', 'tinymce' => '', 'help' => '',
		'class'=>'large-text', 'echo'=>true,'rows'=>5, 'cols'=>50,
		'readonly'=> false,
	));

	$id    = ( !empty($args['id']) ? $args['id'] : $args['label_for']);
	$class = implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $args['class'] ) ) );
	$name  = isset( $args['name'] ) ?  $args['name'] : '';
	$value = isset( $args['value'] ) ?  $args['value'] : '';

	//Custom data-* attributes
	$data = '';
	if( !empty( $args['data'] ) && is_array( $args['data'] ) ){
		foreach( $args['data'] as $key => $attr_value ){
			$data .= sprintf( 'data-%s="%s"', esc_attr( $key ), esc_attr( $attr_value ) );
		}
	}
	$rows       = !empty( $args['rows'] ) ? sprintf( 'rows="%d"', $args['rows'] ) : '';
	$cols       = !empty( $args['cols'] ) ? sprintf( 'cols="%d"', $args['cols'] ) : '';
	$readonly   = $args['readonly'] ? 'readonly' : '';
	$attributes = array_filter( array( $rows, $cols, $readonly, $data ) );

	$html = '';
	
	if( $args['tinymce'] ){
		ob_start();

		$tinymce_args = is_array( $args['tinymce'] ) ? $args['tinymce'] : array();
		$tinymce_args = array_merge( array(
				'textarea_name' => $name,
				'media_buttons' => false,
				'textarea_rows' => intval( $args['rows'] ),
		), $tinymce_args );
		
		wp_editor( $value, esc_attr( $id ) ,$tinymce_args );
		
		$html .= ob_get_contents();
		
		if( $data ){
			$html = str_replace( 
				'<div id="wp-' . $id . '-editor-container"',
				'<div id="wp-' . $id . '-editor-container" '.$data.' ',
				$html
			);
		}
		
		ob_end_clean();
	}else{
		$html .= sprintf('<textarea %s name="%s" class="%s" id="%s">%s</textarea>',
				implode( ' ', $attributes ),
				esc_attr( $name ),
				$class,
				esc_attr( $id ),
				esc_textarea( $value )
		);
	}

	if(!empty($args['help'])){
		$html .= '<p class="description">'.$args['help'].'</p>';
	}

	if( $args['echo'] )
		echo $html;

	return $html;
}

/**
 * @ignore
 * @private
 * @param unknown_type $text
 * @return mixed
 */
function eventorganiser_esc_printf($text){
	return str_replace('%','%%',$text);
}

/**
 * @ignore
 * @private
 * @param unknown_type $key
 * @param unknown_type $group
 * @return Ambigous <boolean, mixed>
 */
function eventorganiser_cache_get( $key, $group ){

	$ns_key = wp_cache_get( 'eventorganiser_'.$group.'_key' );

	// if not set, initialize it
	if ( $ns_key === false )
		wp_cache_set( 'eventorganiser_'.$group.'_key', 1 );

	return wp_cache_get( "eo_".$ns_key."_".$key, $group );
}

/**
 * @ignore
 * @private
 * @param unknown_type $group
 * @param unknown_type $key
 * @return Ambigous <false, number>|boolean
 */
function eventorganiser_clear_cache( $group, $key = false ){

	if( $key == false ){
		//If a key is not specified clear entire group by incrementing name space
		return wp_cache_incr( 'eventorganiser_'.$group.'_key' );
		
	}elseif( $ns_key = wp_cache_get( 'eventorganiser_'.$group.'_key' ) ){
		//If key is specified - clear particular key from the group
		return wp_cache_delete( "eo_".$ns_key."_".$key, $group );
	}
}
	
/**
 * @ignore
 * @private
 * @param unknown_type $key
 * @param unknown_type $value
 * @param unknown_type $group
 * @param unknown_type $expire
 * @return unknown
 */
function eventorganiser_cache_set( $key, $value, $group, $expire = 0 ){
	$ns_key = wp_cache_get( 'eventorganiser_'.$group.'_key' );

	// if not set, initialize it
	if ( $ns_key === false )
		wp_cache_set( 'eventorganiser_'.$group.'_key', 1 );

	return wp_cache_add( "eo_".$ns_key."_".$key, $value, $group, $expire );
}

/**
 * Display inline help via a qTip2 tooltip.
 * 
 * The function handles the javascript/css loading and generates the link HTML which will trigger the tooltip. 
 * The HTML can be returned or printed using the fourth argument. 
 * 
 * @private
 * @ignore
 * @param string $title The title of the tooltip that will appear
 * @param string $content The content of the tooltip
 * @param bool $echo Whether the link HTML should be printed as well as returned.
 * @return string
 */
function eventorganiser_inline_help( $title, $content, $echo = false, $type = 'help' ){
	static $help = array();
	
	$help[] = array(
		'title' => $title,
		'content' => $content,
	);
	
	wp_localize_script( 'eo-inline-help', 'eoHelp', $help );

	//Ensure style is called after  WP styles
	add_action( 'admin_footer', '_eventorganiser_enqueue_inline_help_scripts', 100 );

	$id = count($help)-1;
	$src = EVENT_ORGANISER_URL."css/images/{$type}-14.png";
	
	$link = sprintf( '<a href="#" id="%s" class="eo-inline-help eo-%s-inline"><img src="%s" width="16" height="16"></a>', 
				'eo-inline-help-' . $id, 
				$type, 
				$src 
			);
	
	if( $echo )
		echo $link;
	
	return $link;
}

/**
 * @ignore
 */
function _eventorganiser_enqueue_inline_help_scripts(){
	wp_enqueue_script( 'eo-inline-help' );
	wp_enqueue_style( 'eventorganiser-style' );
}

/**
 * Darken or lighten a colour (hex) by a given percent (as a decimal)
 * 
 * @param string $hex
 * @param float $percent Percent as a decimal. E.g. 75% = 0.75
 * @return string The generated colour as a hexedecimal
 */
function eo_color_luminance( $hex, $percent ) {

	// validate hex string
	$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
	$new_hex = '#';

	if( !$hex )
		return false;
	
	if ( strlen( $hex ) < 6 ) {
		$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
	}

	// convert to decimal and change luminosity
	for ($i = 0; $i < 3; $i++) {
		$dec = hexdec( substr( $hex, $i*2, 2 ) );
		$dec = min( max( 0, $dec + $dec * $percent ), 255 );
		$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
	}

	return $new_hex;
}


/**
 * Whether the blog's time settings indicates it uses 12 or 24 hour time
 * @deprecated 2.1.3 Use {@see `eo_blog_is_24()`} instead.
 * @see eo_blog_is_24()
 */
function eventorganiser_blog_is_24(){
	return eo_blog_is_24();
}

/**
 * Whether the blog's time settings indicates it uses 12 or 24 hour time
 *
 * If uses meridian (am/pm) it is 12 hour. Otherwise if it uses 'H' as the 
 * time format it is 24 hour. Otherwise assumed to be 12 hour.
 * 
 */
function eo_blog_is_24(){

	$time = get_option( 'time_format');

	//Check for meridian
	if( preg_match( '~\b(A.)\b|([^\\\\]A)~i', $time, $matches ) ){
		$is_24 = false;
		
	//Check for 24 hour format
	}elseif( strpos( $time, 'H' ) > -1 || strpos( $time, 'G' ) > -1  ){
		$is_24 = true;

	//Assume it isn't
	}else{
		$is_24 = false;
	}

	/**
	 * Filters whether your site's time format is 12 hour or 24 hour.
	 * 
	 * This does not affect anything on the front-end, but it is used to determine 
	 * the format in which times are entered admin-side. If `true` then 24 hour time 
	 * is used, otherwise 12 hour time is used.
	 * 
	 * By default this value is a 'best guess' based on your site's time format
	 * option in *Settings > General*.
	 * 
	 * ### Example
	 * 
	 *     //If you want input time to be forced to 12 hour format
	 *     add_filter( 'eventorganiser_blog_is_24', '__return_false' );
	 *     //If you want input time to be forced to 24 hour format
	 *     add_filter( 'eventorganiser_blog_is_24', '__return_true' );
	 * 
	 * @param bool $is_24 Is the site's time format option using 12 hour or 24 hour time. 
	 */
	$is_24 = apply_filters( 'eventorganiser_blog_is_24', $is_24 );
	return $is_24;
}

/**
 * Wrapper for `wp_localize_script`. 
 * 
 * Allows additional arguments to added to a js variable before its printed. By contrast
 * wp_localize_script() over-rides prevous calls to the same handle-object pair.
 * 
 * This allows (most) Event Organiser js-variables to live under the namespace 'eventorganiser'
 *
 * @since 2.1
 * @ignore
 * @uses eventorganiser_array_merge_recursive_distinct()
 * @access private
 * @param string $handle
 * @param array $obj
 */
function eo_localize_script( $handle, $obj ){
	static $eventorganiser_localise_obj = array();
	
	$eventorganiser_localise_obj = eo_array_merge_recursive_distinct( $eventorganiser_localise_obj, $obj );

	wp_localize_script( $handle, 'eventorganiser', $eventorganiser_localise_obj );	
}

/**
 * Recursively merge two or more arrays. 
 * 
 * Unlike `array_merge_recursive()` the datatype is not altered. Values override existing values. If its an array,
 * Matching keys' values in a latter array overwrite those in the earlier arrays.
 * 
 * @param array1 array Initial array to merge.
 * @param array2 array Second array to merge  
 *  
 * @since 2.1
 * @ignore
 * @see https://www.php.net/manual/en/function.array-merge-recursive.php#91049
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 * @author Michiel <michiel (at) synetic (dot) nl
 * @return array the resulting array.
 */
function &eo_array_merge_recursive_distinct ( array $array1, array $array2 /* array 3, array 4 */  ){
	
	$aArrays = func_get_args();
	$aMerged = $aArrays[0];
	
	for($i = 1; $i < count($aArrays); $i++){
		if ( is_array( $aArrays[$i] ) ){
			foreach ($aArrays[$i] as $key => $val){
				if ( is_array( $aArrays[$i][$key] ) ){
					if( isset( $aMerged[$key] ) && is_array( $aMerged[$key] ) ){
						$aMerged[$key] =  eo_array_merge_recursive_distinct( $aMerged[$key], $aArrays[$i][$key] );
					}else{
						$aMerged[$key] = $aArrays[$i][$key];
					}
				}else{
					$aMerged[$key] = $val;
				}
			}
		}
	}
	
	return $aMerged;
}


/**
 * Add $dep (script handle) to the array of dependencies for $handle
 * 
 * @since 2.1
 * @ignore
 * @access private
 * @see https://wordpress.stackexchange.com/questions/100709/add-a-script-as-a-dependency-to-a-registered-script
 * @param string $handle Script handle for which you want to add a dependency
 * @param string $dep Script handle - the dependency you wish to add
 */
function eventorganiser_append_dependency( $handle, $dep ){
	global $wp_scripts;
	
	$script = $wp_scripts->query( $handle, 'registered');
	if( !$script )
		return false;
	
	if( !in_array( $dep, $script->deps ) ){
		$script->deps[] = $dep;
	}
	
	return true;
}


/**
 * Escapes a string so it safe for use in ICAL template. 
 * 
 * Commas, semicolons, newlines and backslashes are escaped.
 * 
 * @ignore
 * @see http://www.ietf.org/rfc/rfc2445.txt
 * @since 2.1
 * @param string $text The string to be escaped
 * @return string The escaped string.
 */
function eventorganiser_escape_ical_text( $text ){
	
	$text = str_replace( "\\", "\\\\", $text );
	$text = str_replace( ",", "\,", $text );
	$text = str_replace( ";", "\;", $text );
	/*
	 * An intentional formatted text line break MUST only be included in a
   	 * "TEXT" property value by representing the line break with the
   	 * character sequence of BACKSLASH (US-ASCII decimal 92), followed by a
   	 * LATIN SMALL LETTER N (US-ASCII decimal 110) or a LATIN CAPITAL LETTER
   	 * N (US-ASCII decimal 78), that is "\n" or "\N".
	 */
	$text = str_replace( "\r\n", "\n", $text );
	$text = str_replace( "\n", "\\n", $text );
	
	return $text;
}

/**
 * Fold text as per [iCal specifications](http://www.ietf.org/rfc/rfc2445.txt)
 * 
 * Lines of text SHOULD NOT be longer than 75 octets, excluding the line
 * break. Long content lines SHOULD be split into a multiple line
 * representations using a line "folding" technique. That is, a long
 * line can be split between any two characters by inserting a CRLF 
 * immediately followed by a single linear white space character (i.e.,
 * SPACE, US-ASCII decimal 32 or HTAB, US-ASCII decimal 9). Any sequence
 * of CRLF followed immediately by a single linear white space character
 * is ignored (i.e., removed) when processing the content type.
 *
 * @ignore
 * @since 2.7
 * @param string $text The string to be escaped
 * @return string The escaped string.
 */
function eventorganiser_fold_ical_text( $text ){

	$text_arr = array();

	$lines = ceil( mb_strlen( $text ) / 75 );
	
	for( $i = 0; $i < $lines; $i++ ){
		$text_arr[$i] = mb_substr( $text, $i * 75, 75 );
	}

	return join( $text_arr, "\r\n " );
}

/**
 * Like wp_list_pluck() but plucks out key and value from each object in the list
 * 
 * @since 2.2
 * @param array $list A list of objects or arrays
 * @param int|string $field A field from the object to used as the key of the entire object
 * @param int|string $field A field from the object to place instead of the entire object
 * @return multitype:unknown NULL
 */
function eo_list_pluck_key_value( $list, $key_field, $value_field ){
	
	$new_list = array();
	
	foreach ( $list as $key => $value ) {
		if ( is_object( $value ) )
			$new_list[ $value->$key_field ] = $value->$value_field;
		else
			$new_list[ $value[ $key_field ] ] = $value[ $value_field ];
	} 
	
	return $new_list;
}

/**
 * Given an array, it returns only the values whose key exists in 
 * a given key whitelist.
 * 
 * @param array $array The array for which we wish to weed out non whitelisted values.
 * @param array $whitelist An array of permissable keys.
 * @return array The original array with non-permissable keys removed
 */
function eo_array_key_whitelist( $array, $whitelist = array() ){
	return array_intersect_key( $array, array_flip( $whitelist ) );
}



/**
 * Does this site have more than one event organiser.
 *
 * Checks to see if more than one user has published an event.
 *
 * @since 2.7.5
 * @return bool Whether or not we have more than one author (of an event)
 */
function eo_is_multi_event_organiser() {
	global $wpdb;

	if ( false === ( $is_multi_event_organiser = get_transient( 'eo_is_multi_event_organiser' ) ) ) {
		$rows = (array) $wpdb->get_col("SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_type = 'event' AND post_status = 'publish' LIMIT 2");
		$is_multi_event_organiser = 1 < count( $rows ) ? 1 : 0;
		set_transient( 'eo_is_multi_event_organiser', $is_multi_event_organiser );
	}

	/**
	 * Filter whether the site has more than one user with published events.
	 *
	 * @since 2.7.5
	 * @param bool $is_multi_event_organiser Whether $is_multi_event_organiser should evaluate as true.
	 */
	return apply_filters( 'eventorganiser_is_multi_event_organiser', (bool) $is_multi_event_organiser );
}

/**
 * Combines two arrays by joining them by their key. 
 * 
 * This is similar to array_combine, but rather than combining
 * by index, the array is combined by key. Any keys not found 
 * in both are ignored.
 * 
 * @param array $key_array   Array whose values form the keys of the returned array 
 * @param array $value_array Array whose values form the values of the returned array
 * @return array The combined array
 */
function eo_array_combine_assoc( $key_array, $value_array ) {
	
	$output = array();
	$keys = array_keys( array_intersect_key( $key_array, $value_array ) );
	
	if( $keys ){
		foreach( $keys as $key ){
			$output[$key_array[$key]] = $value_array[$key]; 
		}
	}
	
	return $output;
}