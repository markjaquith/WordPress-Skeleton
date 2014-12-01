<?php
//TODO How does UNTIL=[DATE] as opposed to UNTIL=[DATE-TIME] affect "foreign" recurring events
//TODO Resolve issue (1) below
//TODO Detect issue (2) and issue error notices

/**
 * Parses a local or remote ICAL file
 * 
 * Example usage
 * <code>
 *      $ical = new EO_ICAL_Parser();
 *      $ical->parse( 'http://www.dol.govt.nz/er/holidaysandleave/publicholidays/publicholidaydates/ical/auckland.ics' );
 *      
 *      $ical->events; //Array of events
 *      $ical->venues; //Array of venue names
 *      $ical->categories; //Array of category names
 *      $ical->errors; //Array of WP_Error errors
 *      $ical->warnings; //Array of WP_Error 'warnings'. This are "non-fatal" errors (e.g. warnings about timezone 'guessing').
 * </code>
 * 
 * You can configire default settings by passing an array to the class constructor.
 * <code>
 *      $ical = new EO_ICAL_Parser( array( ..., 'default_status' => 'published', ... ) );
 * </code>
 * Available settings include:
 * 
 *  *  **status_map** - How to interpret the ICAL STATUS property.
 *  *  **default_status** - Default status of posts (unless otherwise specified by STATUS). Default is 'draft'
 * 
 * @link http://www.ietf.org/rfc/rfc2445.txt ICAL Specification 
 * @link http://www.kanzaki.com/docs/ical/ ICAL Specification excerpts
 * @author stephen
 * @package ical-functions
 *
 */
class EO_ICAL_Parser{

	/**
	 * Array of events present in the feed
	 * @var array
	 */
	var $events = array();
	
	/**
	 * Array of venues present in the feed
	 * @var array
	*/
	var $venues = array();
	
	/**
	 * Array of venue metadata present in the feed
	 * @var array
	*/
	var $venue_meta = array();
	
	/**
	 * Array of categories present in the feed
	 * @var array
	*/
	var $categories = array();
	
	/**
	 * Number of events parsed.
	 * @var int
	 */
	var $events_parsed = 0;
	
	/**
	 * Number of venues parsed.
	 * @var int
	 */
	var $venue_parsed = 0;
	
	/**
	 * Number of categories parsed.
	 * @var int
	 */
	var $categories_parsed = 0;
	
	/**
	 * Timeout for remote fetching (in seconds)
	 * @var int 
	 */
	var $remote_timeout = 10;
	

	/**
	 * Array of WP_Error objects. These are errors which abort the parsing.
	 * @var array
	 */
	var $errors = array();
	
	/**
	 * Array of WP_Error objects. These are soft-errors which the parser tries to deal with
	 * @var array
	 */
	var $warnings = array();

	
	/**
	 * The current event being parsed. Stores data retrieved so far in the parsing.
	 * @var array
	 */
	var $current_event = array();
		
	/**
	 * Indicates which line in the feed we are at
	 * @var int
	 */
	var $line = 0; //Current line being parsed
	
	/**
	 * Keeps track of where we are in the feed.
	 * @var string
	 */
	var $state = "NONE";
	
	
	/**
	 * Option to toggle whether a HTML description should be used (if present).
	 * @var bool
	 */
	var $parse_html = true; //If description is given in HTML, try to use that.

	
	/**
	 * Constructor with settings passed as arguments
	 * Available options include 'status_map' and 'default_status'.
	 * 
	 * @param array $args
	 */
	function __construct( $args = array() ){

		$args = array_merge( array(
					'status_map' => array(
						'CONFIRMED' => 'publish',
						'CANCELLED' => 'trash',
						'TENTATIVE' => 'draft',
					),
					'default_status' => 'draft',
					'parse_html' => true,
				), $args );
		
		/**
		 * Filters the options for the iCal parser class
		 * 
		 * `$args` is an array with keys:
		 * 
		 *  - `status_map` - mapping iCal status to WordPress status. By default
		 *     <pre><code>
		 *     array(
		 *			'CONFIRMED' => 'publish',
		 *			'CANCELLED' => 'trash',
		 *			'TENTATIVE' => 'draft',
				);
		 *     </code></pre>
		 *  - `default_status` - the status to use for the event if the iCal feed does not provide a status#
		 *  - `parse_html` - whether to parse a HTML version of event descriptions if provided 
		 *
		 * @param array $args Options for the iCal Parser
		 * @param EO_ICAL_Parser $ical_parser The iCal parser object
		 */
		$args = apply_filters_ref_array( 'eventorganiser_ical_parser_args', array( $args, &$this ) );
		
		$this->calendar_timezone = eo_get_blog_timezone();
		
		$this->default_status = $args['default_status'];
		$this->status_map = $args['status_map'];
		$this->parse_html = $args['parse_html'];
		
	}


	/**
	 * Parses the given $file. Returns WP_Error on error.
	 * 
	 * @param string $file Path to iCal file or an url to an ical file
	 * @return bool|WP_Error. True if parsed. Returns WP_Error on error;
	 */
	function parse( $file ){
		
		//Local file
		if( is_file($file) && file_exists($file)  ){
			$this->ical_array = $this->file_to_array( $file );

		//Remote file
		}elseif( preg_match('!^(http|https|ftp|webcal)://!i', $file) ){
			$this->ical_array = $this->url_to_array( $file );

		}else{
			$this->ical_array =  new WP_Error( 
				'invalid-ical-source', 
				__( 'There was an error detecting iCal source.', 'eventorganiser' )
			);
		}

		if( is_wp_error( $this->ical_array ) )
			return $this->ical_array;
		
		if( empty( $this->ical_array ) ){
			return new WP_Error( 'unable-to-read', __( 'Unable to read iCal file', 'eventorganiser' ) );
		}

		//Go through array and parse events
		$result = $this->parse_ical_array();
		
		if( "NONE" == $this->state ){
			return new WP_Error( 'unable-to-fetch', __( 'Feed not found', 'eventorganiser' ) );
		}
		
		$this->events_parsed = count( $this->events );
		$this->venue_parsed = count( $this->venues );
		$this->categories_parsed = count( $this->categories );
		
		/**
		 * Filter the feed class by reference.
		 * 
		 * This filter allows you to view and modify all events, venues and categories from
		 * a parsed iCal feed. The example below adds all events to the category 'imported'
		 * 
		 * <pre><code>
		 * add_action( 'eventorganiser_ical_feed_parsed', 'my_auto_assign_event_cat_to_feed' );
		 * function my_auto_assign_event_cat_to_feed( $ical_parser ){
		 *      if( $ical_parser->events_parsed ){
		 *          foreach( $ical_parser->events_parsed  as $index => $event ){
		 *      		$this->events_parsed[$index]['event-category'][] = 'imported';
		 *      	}
		 *      }
		 * }
		 * </code></pre>
		 * 
		 * @since 2.7
		 * @param EO_ICAL_Parser $EO_ICAL_Parser The feed parser object containg parsed events/venues/categories.
		 */
		do_action_ref_array( 'eventorganiser_ical_feed_parsed', array( &$this ) );
		
		return true;
	}
	
	/**
	 * Fetches ICAL calendar from a feed url and returns its contents as an array.
	 * 
	 * @ignore
	 * @param sring $url The url of the ICAL feed 
	 * @return array|bool Array of line in ICAL feed, false on error 
	 */
	protected function url_to_array( $url ){
		
		//Handle webcal:// protocol: change to http://
		$url = preg_replace('#^(webcal://)#', 'http://', $url );
		
		$response =  wp_remote_get( $url, array( 'timeout' => $this->remote_timeout ) );
		$contents = wp_remote_retrieve_body( $response );
		$response_code = wp_remote_retrieve_response_code( $response );
		
		if( is_wp_error( $response ) )
			return $response;
		
		if( $response_code != 200 ){
			return new WP_Error( 'unable-to-fetch',
				sprintf(
					'%s. Response code: %s.',
					wp_remote_retrieve_response_message( $response ),
					$response_code
			));
		}
		
		if( $contents )
			return explode( "\n", $contents );
		
		
		return new WP_Error( 'unable-to-fetch', 
			sprintf( 
				__( 'There was an error fetching the feed. Response code: %s.', 'eventorganiser' ),
				$response_code
			));
	}

	/**
	 * Fetches ICAL calendar from a file and returns its contents as an array.
	 *
	 * @ignore
	 * @param sring $url The ICAL file
	 * @return array|bool Array of line in ICAL feed, false on error
	 */
	protected function file_to_array( $file ){

		$file_handle = @fopen( $file, "rb");
		$lines = array();

		if( !$file_handle )
			return new WP_Error( 
						'unable-to-open', 
					__( 'There was an error opening the ICAL file.', 'eventorganiser' )
					);

		//Feed lines into array
		while (!feof( $file_handle ) ):
			$line_of_text = fgets( $file_handle, 4096 );
			$lines[]= $line_of_text;
		endwhile;

		fclose($file_handle);

		return $lines;
	}

	/**
	 * Modifies the ical_array to unfold multi-line entries into a single line. 
	 * Preserves the original line numbering so that line numbers in error messages
	 * match up with the line numbers when viewing the (unfolded) iCal file in a 
	 * text editor.  
	 */
	function unfold_lines( $lines ) {
		
		$unfolded_lines = array();

		$i = 0;
		
		while( $i < count ( $lines ) ) {
			
			$unfolded_lines[$i] = rtrim( $lines[$i], "\n\r" );
			
			$j = $i+1;
			
			while( isset( $lines[$j] ) && strlen( $lines[$j] ) > 0 && ( $lines[$j]{0} == ' ' || $lines[$j]{0} == "\t" )) {
				$unfolded_lines[$i] .= rtrim( substr( $lines[$j], 1 ), "\n\r" );
				$j++;
			}
			
			$i = ($j-1) + 1;
		}
		
		return $unfolded_lines;
	}
	

	/**
	 * Parses through an array of lines (of an ICAL file)
	 * @ignore
	 */
	protected function parse_ical_array(){

		$this->ical_array = $this->unfold_lines( $this->ical_array );
		
		$this->state = "NONE";//Initial state
		$this->line = 1;

		//Read through each line
		foreach ( $this->ical_array as $index => $line_content ):
		
			if( !empty( $this->errors ) )
				break;
		
			$this->line = $index + 1;
			$buff = trim( $line_content );

			if( !empty( $buff ) ):
				$line = $this->_split_line( $buff );

				//On the right side of the line we may have DTSTART;TZID= or DTSTART;VALUE=
				$modifiers = explode( ';', $line[0] );
				$property = array_shift( $modifiers );
				$value = ( isset( $line[1] ) ? trim( $line[1] ) : '' );

				//If we are in EVENT state
				if ( $this->state == "VEVENT" ) {
					
					if( $property == "BEGIN" && $value == 'VALARM' ){
						//In state VEVENT > VALARM
						$this->state = "VEVENT:VALARM";
						
						
					//If END:VEVENT, add event to parsed events and clear $event
					}elseif( $property == 'END' && $value =='VEVENT' ){
						$this->state = "VCALENDAR";
						
						$this->current_event['_lines']['end'] = $this->line;
						
						//Now we've finished passing the event, move venue data to $this->venue_meta
						if( isset( $this->current_event['geo'] ) && !empty( $this->current_event['event-venue'] ) ){
							$venue = $this->current_event['event-venue'];
							$this->venue_meta[$venue]['latitude'] = $this->current_event['geo']['lat'];
							$this->venue_meta[$venue]['longtitude'] = $this->current_event['geo']['lng'];
							unset( $this->current_event['geo'] );
						}
						
						if( empty( $this->current_event['uid'] ) ){
							$this->report_warning( 
									$this->current_event['_lines'], 
									'event-no-uid',
									"Event does not have a unique identifier (UID) property."
							);
						}
						
						if( empty( $this->current_event['sequence'] ) ){
							$this->current_event['sequence'] = 0;
						}
						
						//Check to see if an event has already been parsed with this UID 
						$index = isset( $this->current_event['uid'] ) ? 'uid:'.$this->current_event['uid'] : count( $this->events );
						if( isset( $this->events[$index] ) ){
			
							if( $this->current_event['sequence'] > $this->events[$index]['sequence'] ){
								$this->events[$index] = $this->current_event;
								
							}elseif( isset( $this->events[$index]['recurrence-id'] ) ){
								//This event has recurrence ID - replace it.
								$this->events[$index] = $this->current_event;
								
							}elseif( isset( $this->current_event['recurrence-id'] ) ){
								//Ignore this event - keep existing
								
							}elseif( $this->current_event['sequence'] == $this->events[$index]['sequence'] ){
								$this->report_warning( 
									$this->current_event['_lines'], 
									'duplicate-id',
									sprintf( 
										"Duplicate UID (%s) found in feed. UIDs must be unique.",
										$this->current_event['uid']
									)
								);
							}
				
						}else{
							$this->events[$index] = $this->current_event;
						}
						
						$this->current_event = array();

					//Otherwise, parse event property
					}else{
						try{
							while( isset( $this->ical_array[$this->line] ) && $this->ical_array[$this->line-1][0] == ' ' ){
								//Remove initial white space {@link http://www.ietf.org/rfc/rfc2445.txt Section 4.1}
								$value .= substr( $this->ical_array[$this->line-1], 1 );
								$this->line++;
							}
						
							$this->parse_event_property( $property, $value, $modifiers );

						}catch( Exception $e ){
							$this->report_error( $this->line, 'event-property-error', $e->getMessage() );
							$this->state = "VCALENDAR";//Abort parsing event
						}
					}

					
				//We are in a VEVENT > VALARM stte
				}elseif( $this->state == "VEVENT:VALARM" ){
					
					//We ignore VALARMs...
					if ( $property=='END' && $value=='VALARM')
						$this->state = "VEVENT";
					
					
				// If we are in CALENDAR state
				}elseif ($this->state == "VCALENDAR") {

					//Begin event
					if( $property=='BEGIN' && $value=='VEVENT'){
						$this->state = "VEVENT";
						$this->current_event = array( '_lines' => array( 'start' => $this->line ) );

					}elseif ( $property=='END' && $value=='VCALENDAR'){
						$this->state = "ENDCALENDAR";
		
					}elseif($property=='X-WR-TIMEZONE'){
						$this->calendar_timezone = $this->parse_timezone($value);
					}

				//Other
				}elseif($this->state == "NONE" && $property=='BEGIN' && $value=='VCALENDAR') {
					$this->state = "VCALENDAR";
				}
			endif; //If line is not empty
		endforeach; //For each line
		
		$this->events = array_values( $this->events );
	}


	/**
	 * Report an error with an iCal file
	 * @ignore
	 * @param int $line The line on which the error occurs.
	 * @param string $type The type of error
	 * @param string $message Verbose error message
	 */
	protected function report_error( $line, $type, $message ){

		if( is_array( $line ) ){
			$this->errors[] = new WP_Error(
				$type,
				sprintf( __( '[Lines %1$d-%2$d]', 'eventorganiser' ), $line['start'], $line['end'] ).' '.$message,
				array( 'line' => $line )
			);
			
		}else{
			$this->errors[] = new WP_Error(
					$type,
					sprintf( __( '[Line %1$d]', 'eventorganiser' ), $line ).' '.$message,
					array( 'line' => $line )
			);
		}
	}
	
	/**
	 * Report an warnings with an iCal file
	 * @ignore
	 * @param int $line The line on which the error occurs.
	 * @param string $type The type of error
	 * @param string $message Verbose error message
	 */
	protected function report_warning( $line, $type, $message ){
	
		if( is_array( $line ) ){
			$this->warnings[] = new WP_Error(
					$type,
					sprintf( __( '[Lines %1$d-%2$d]', 'eventorganiser' ), $line['start'], $line['end'] ).' '.$message,
					array( 'line' => $line )
			);
				
		}else{
			$this->warnings[] = new WP_Error(
					$type,
					sprintf( __( '[Line %1$d]', 'eventorganiser' ), $line ).' '.$message,
					array( 'line' => $line )
			);
		}
	}


	/**
	 * @ignore
	 */
	protected function parse_event_property( $property, $value, $modifiers ){

		if( !empty( $modifiers ) ):
			foreach( $modifiers as $modifier ):
				if ( stristr( $modifier, 'TZID' ) ){
			
					$date_tz = $this->parse_timezone( substr( $modifier, 5 ) );

				}elseif( stristr( $modifier, 'VALUE' ) ){
					$meta = substr( $modifier, 6 );
				}
			endforeach;
		endif;

		//For dates - if there is not an associated timezone, use calendar default.
		if( empty( $date_tz ) )
			$date_tz = $this->calendar_timezone;

		switch( $property ):
		case 'UID':
			$this->current_event['uid'] = $value;
		break;
		
		case 'SEQUENCE':
			$this->current_event['sequence'] = $value; 
			break;
		case 'RECURRENCE-ID':
			//This is not properly implemented yet but is used to detect
			//when feed entries may share a UID.
			$this->current_event['recurrence-id'] = $value;
			break;
		case 'CREATED':
		case 'DTSTART':
		case 'DTEND':
			if( isset( $meta ) && $meta == 'DATE' ):
				$date = $this->parse_ical_date( $value );
				$allday = 1;
			else:
				$date = $this->parse_ical_datetime( $value, $date_tz );
				$allday = 0;
			endif;

			if( empty( $date ) )
				break;

			switch( $property ):
				case'DTSTART':
					$this->current_event['start'] = $date;
					$this->current_event['all_day'] = $allday;
				break;

				case 'DTEND':
					if( $allday == 1 )
						$date->modify('-1 second');
					$this->current_event['end'] = $date;
				break;

				case 'CREATED':
					$date->setTimezone( new DateTimeZone('utc') );
					$this->current_event['post_date_gmt'] = $date->format('Y-m-d H:i:s');
				break;

			endswitch;
		break;

		case 'EXDATE':
		case 'RDATE':
			//The modifiers have been dealt with above. We do similiar to above, except for an array of dates...
			$value_array = explode( ',', $value );

			//Note, we only consider the Date part and ignore the time
			foreach( $value_array as $val ):
				$date = $this->parse_ical_date( $val );
				
				if( $property == 'EXDATE' ){
					$this->current_event['exclude'][] = $date;
				}else{
					$this->current_event['include'][] = $date;
				}
			endforeach;
		break;

			//Reoccurrence rule properties
		case 'RRULE':
			$this->current_event += $this->parse_RRule($value);
		break;

			//The event's summary (AKA post title)
		case 'SUMMARY':
			$this->current_event['post_title'] = $this->parse_ical_text( $value );
		break;

			//The event's description (AKA post content)
		case 'DESCRIPTION':
			if( !isset( $this->current_event['post_content'] ) ){
				$this->current_event['post_content'] = $this->parse_ical_text( $value );
			}
		break;
		
			//Description, in alternative format
		case 'X-ALT-DESC':
			if( $this->parse_html && !empty( $modifiers[0] ) && in_array( $modifiers[0], array( "FMTTYPE=text/html", "ALTREP=text/html" ) ) ){
				$this->current_event['post_content'] = $this->parse_ical_html( $value );	
			}
		break;
		
			//Event venues, assign to existing venue - or if set, create new one
		case 'LOCATION':
			if( !empty( $value ) ):

			$venue_name = trim($value);
				
			if( !isset( $this->venues[$venue_name] ) )
				$this->venues[$venue_name] = $venue_name;
				
			$this->current_event['event-venue'] = $venue_name;
			endif;
		break;

		case 'CATEGORIES':
			$cats = explode( ',', $value );
			
			if( !empty( $cats ) ):

			foreach ($cats as $cat_name):
				$cat_name = trim($cat_name);

				if( !isset( $this->categories[$cat_name] ) )
					$this->categories[$cat_name] = $cat_name;
				
				if( !isset($this->current_event['event-category']) || !in_array( $cat_name, $this->current_event['event-category']) )
					$this->current_event['event-category'][] = $cat_name;
				
			endforeach;

			endif;
		break;

			//The event's status
		case 'STATUS':
			$map = $this->status_map;

			$this->current_event['post_status'] = isset( $map[$value] ) ? $map[$value] : $this->default_status;
		break;

		case 'GEO':
			$lat_lng = array_map( 'floatval', explode( ';', $value ) );
			if( count( $lat_lng ) === 2 ){
				$keys = array( 'lat', 'lng' );
				$this->current_event['geo'] = array_combine( $keys, $lat_lng );
			}
		break;
			
		//An url associated with the event
		case 'URL':
			$this->current_event['url'] = $value;
		break;

		
			endswitch;

	}

	protected function parse_ical_html( $text ){
		
		$text = $this->parse_ical_text( $text );
		
		if( preg_match( "/<body>(.+)<\/body>/i", $text, $matches ) ){
			$text = $matches[1];
		}
		
		return $text;
	}

	/**
	 * Takes escaped text and returns the text unescaped.
	 * 
	 * @see https://github.com/fruux/sabre-vobject/blob/219935b414c24ce89acd32d509966d44f04f4012/lib/Parser/MimeDir.php#L469:L513
	 * @ignore
	 * @param string $text - the escaped test
	 * @return string $text - the text, unescaped.
	 */
	protected function parse_ical_text($text){

		//Unfold
		$text = str_replace( "\n ","", $text );
		$text = str_replace( "\r\n ", "", $text );

		//Repalce any intended new lines with PHP_EOL
		//$text = str_replace( '\n', "<br>", $text );
		$text = nl2br( $text );
				
		$regex = '#  (?: (\\\\ (?: \\\\ | N | n | ; | , ) ) ) #x';
        $matches = preg_split( $regex, $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

        $result = '';
        
        foreach( $matches as $match ) {

            switch ( $match ) {
                case '\\\\' :
                    $result .='\\';
                    break;
                case '\N' :
                case '\n' :
                    $result .="\n";
                    break;
                case '\;' :
                    $result .=';';
                    break;
                case '\,' :
                    $result .=',';
                    break;
                default :
                    $result .= $match;
                    break;

            }

        }

		return addslashes( $result );
	}

	/**
	 * Takes a date-time in ICAL and returns a datetime object
	 * @ignore
	 * @param string $tzid - the value of the ICAL TZID property
	 * @return DateTimeZone - the timezone with the given identifier or false if it isn't recognised
	 */
	public function parse_timezone( $tzid ){
		
		$tzid = str_replace( '-', '/', $tzid );
		$tzid = trim( $tzid, '\'"' );

		//Try just using the passed timezone ID
		try{
			$tz = new DateTimeZone( $tzid );
		}catch( exception $e ){
			$tz = null;
		}

		//If we have something like (GMT+01.00) Amsterdam / Berlin / Bern / Rome / Stockholm / Vienna lets try the cities
		if( is_null( $tz ) && preg_match( '/GMT(?P<offset>.+)\)(?P<cities>.+)?/', $tzid, $matches ) ){
			
			if( !empty( $matches['cities'] ) ){
				$parts = explode( '/', $matches['cities'] );
				$tz_cities = array_map( 'trim', $parts );
				$identifiers = timezone_identifiers_list();
			
				foreach( $tz_cities as $tz_city ){
			
					$tz_city = ucfirst( strtolower( $tz_city ) );
			
					foreach( $identifiers as $identifier ){
			
						$parts = explode('/', $identifier );
						$city = array_pop( $parts );
							
						if( $city != $tz_city )
							continue;
			
						try{
							$tz = new DateTimeZone( $identifier );
							break 2;
						}catch( exception $e ){
							$tz = null;
						}
					}
				}
			}
			
			if( $tz == null && $matches['offset'] ){
				
				$offset = (int) str_replace( '/', '-',  trim( $matches['offset'] ) );
				
				if( $offset == 0 ){
					$tz = new DateTimeZone( 'UTC' );
				}else{
					$offset *= 3600; // convert hour offset to seconds
					$allowed_zones = timezone_abbreviations_list();

					foreach ( $allowed_zones as $abbr ):
						foreach ( $abbr as $city ):
							if ( $city['offset'] == $offset ){
								try{
									$tz = new DateTimeZone( $city['timezone_id'] );
									break 2;
								}catch( exception $e ){
									$tz = null;
								}
							}
						endforeach;
					endforeach;
				}
			}
		}	
		
		//If we have something like /mozilla.org/20070129_1/Europe/Berlin
		if( is_null( $tz ) && preg_match( '#(/?)mozilla.org/([\d_]+)/(?P<tzid>.+)#', $tzid, $matches ) ){
			try{
				$tz = new DateTimeZone( $matches['tzid'] );
			}catch( exception $e ){
				$tz = null;
			}
		}

		//Let plugins over-ride this
		/**
		 * Filters the DateTimeZone object parsed from a timezone ID in an iCal feed.
		 * 
		 * @param DateTimeZone $tz The timezone interpreted from a given string ID
		 * @param string $tzid The give timezone ID
		 */
		$tz = apply_filters( 'eventorganiser_ical_timezone', $tz, $tzid );
		
		if ( ! ($tz instanceof DateTimeZone ) ) {
			$tz = eo_get_blog_timezone();
		}
		
		if( $tz->getName() != $tzid ){
			$this->report_warning( 
				$this->line, 
				'timezone-parser-warning', 
				sprintf( 'Unknown timezone "%s" interpreted as "%s".', $tzid, $tz->getName() )
			);
		}
		
		return $tz;
	}


	
	/**
	 * Takes a date in ICAL and returns a datetime object
	 * 
	 * Expects date in yyyymmdd format
	 * @ignore
	 * @param string $ical_date - date in ICAL format
	 * @return DateTime - the $ical_date as DateTime object
	 */
	protected function parse_ical_date( $ical_date ){

		preg_match('/^(\d{8})*/', $ical_date, $matches);

		if( count( $matches ) !=2 ){
			throw new Exception(
				sprintf(
					__( 'Invalid date "%s". Date expected in YYYYMMDD format.', 'eventorganiser' ),
					$ical_date
				));
		}

		//No time is given, so ignore timezone. (So use blog timezone).
		$datetime = new DateTime( $matches[1], eo_get_blog_timezone() );

		return $datetime;
	}

	/**
	 * Takes a date-time in ICAL and returns a datetime object
	 * 
	 * It returns the datetime in the specified 
	 * 
	 * Expects
	 *  * utc:  YYYYMMDDTHHiissZ
	 *  * local:  YYYYMMDDTHHiiss
	 *  
	 * @ignores
	 * @param string $ical_date - date-time in ICAL format
	 * @param DateTimeZone $tz - Timezone 'local' is interpreted as
	 * @return DateTime - the $ical_date as DateTime object
	 */
	protected function parse_ical_datetime( $ical_date, $tz ){
		
		preg_match('/^((\d{8}T\d{6})(Z)?)/', $ical_date, $matches);

		if( count( $matches ) == 3 ){
			//floating / local date

		}elseif( count($matches) == 4 ){
			$tz = new DateTimeZone('UTC');

		}else{
			throw new Exception(
					sprintf(
						__( 'Invalid datetime "%s". Date expected in YYYYMMDDTHHiissZ or YYYYMMDDTHHiiss format.', 'eventorganiser' ),
						$ical_date
					));
			return false;
		}

		$datetime = new DateTime( $matches[2], $tz );

		return $datetime;
	}

	/**
	 * Takes a date-time in ICAL and returns a datetime object

	 * @since 1.1.0
	 * @ignore
	 * @param string $RRule - the value of the ICAL RRule property
	 * @return array - a reoccurrence rule array as understood by Event Organiser
	 */
	protected function parse_RRule($RRule){
		//RRule is a sequence of rule parts seperated by ';'
		$rule_parts = explode(';',$RRule);

		foreach ($rule_parts as $rule_part):
		
			if( empty( $rule_part ) ){
				continue;
			}

			//Each rule part is of the form PROPERTY=VALUE
			$prop_value =  explode('=',$rule_part, 2);
			$property = $prop_value[0];
			$value = $prop_value[1];

			switch( $property ):
				case 'FREQ':
					$rule_array['schedule'] =strtolower($value);
				break;

				case 'INTERVAL':
					$rule_array['frequency'] =intval($value);
				break;

				case 'UNTIL':
					//Is the scheduled end a date-time or just a date?
					if( preg_match( '/^((\d{8}T\d{6})(Z)?)/', $value ) )
						$date = $this->parse_ical_datetime( $value, new DateTimeZone('UTC') );
					else
						$date = $this->parse_ical_date( $value );
			
					$rule_array['schedule_last'] = $date;
				break;
			
				case 'COUNT':
					$rule_array['number_occurrences'] = absint( $value );
				break;

				case 'BYDAY':
					$byday = $value;
				break;

				case 'BYMONTHDAY':
					$bymonthday = $value;
				break;
			
				//Not supported with warning
				case 'BYSECOND':
				case 'BYMINUTE':
				case 'BYHOUR':
				case 'BYYEARDAY':
				case 'BYWEEKNO':
				case 'BYSETPOS':
					$this->report_warning(
							$this->line,
							'unsupported-recurrence-rule',
							sprintf(
								'Feed contains unrecognised recurrence rule: "%s" and may have not been imported correctly.',
								 $property 
							)
					);
				break;
			
				//Not supported without warning
				case 'WKST':
				break;
				
			endswitch;

		endforeach;

		//Meta-data for Weekly and Monthly schedules
		if( $rule_array['schedule']=='monthly' ):
			
			if( isset( $byday ) ){
				preg_match_all('/(-?\d+)([a-zA-Z]+)/', $byday, $matches);
				
				if ( count( $matches[0] ) > 1 ){
					$this->report_warning(
						$this->line,
						'unsupported-recurrence-rule',
						sprintf(
							'Feed contains unsupported value for "%s" and may have not been imported correctly.',
							 $property 
						)
					);
				}
				
				$rule_array['schedule_meta'] ='BYDAY='.$matches[0][0];

			}elseif( isset( $bymonthday ) ){
				
				$days = explode( ',', $bymonthday );
				
				if ( count( $days ) > 1 ){
					$this->report_warning(
							$this->line,
							'unsupported-recurrence-rule',
							sprintf(
									'Feed contains unsupported value for "%s" and may have not been imported correctly.',
									$property
							)
					);
				}
				
				$rule_array['schedule_meta'] ='BYMONTHDAY='.$days[0];

			}else{
				throw new Exception('Incomplete scheduling information');
			}

		elseif( $rule_array['schedule'] == 'weekly' ):
			preg_match( '/([a-zA-Z,]+)/', $byday, $matches );
			$rule_array['schedule_meta'] = explode(',',$matches[1]);

		endif;

		//If importing indefinately recurring, recurr up to some large point in time.
		//TODO make a log of this somewhere.
		if( empty( $rule_array['schedule_last'] ) && empty( $rule_array['number_occurrences'] ) ){
			$rule_array['schedule_last'] = new DateTime( '2038-01-19 00:00:00' );
			
			$this->report_warning(
				$this->line,
				'indefinitely-recurring-event',
				"Feed contained an indefinitely recurring event. This event will recurr until 2038-01-19."
			);
		}
		
		return $rule_array;
	}

	/**
	 * Responsible for splitting an iCal line into Property and Value
	 * 
	 * E.g. `BEGIN:VEVENT` to `BEGIN` and `VEVENT`. Special care needs to be taken
	 * when dealing with values such as `DTSTART;TZID="(GMT +01:00)":20140712T100000`
	 * 
	 * @see http://wp-event-organiser.com/forums/topic/error-while-sync-ical-feed/#post-11087
	 * @param string $line A line in an iCal feed
	 * @return array Array containing the property part and value part of $line
	 */
	function _split_line( $line ){
		
    	//"Escape" colons in quotation marks
    	$escaped_line = preg_replace( "/\"([^\"]+)(:)([^\"]+)\"/", "\"$1{{colon}}$3\"", $line );
    	$line_parts = explode( ":", $escaped_line );
    	
    	//Property (potentially with modifiers)
    	$property = str_replace( "{{colon}}", ":", $line_parts[0] );
    	
    	//value
    	array_shift( $line_parts );
    	$value = ( $line_parts ? implode( ":", $line_parts ) : false );
    	$value = str_replace( "{{colon}}", ":", $value );

		return array( $property, $value );
	}
}

/*
 *  * Known issue (1): recurrence is sometimes not translated properly across timezones.
 *  - ICAL has event recurring every month on the 2nd at 02:00 (2am) UTC time.
 *  - Importing blog has New York Time Zone (UTC -4/5).
 *  - Then event recurs every month on the **1st** at 22:00 (10pm) New York Time
 *  - The **2nd** is not corrected to **1st**.
 *  
 *  * Known issue (2): cannot import events with a recurrence schedule EO doesn't understand.
 */