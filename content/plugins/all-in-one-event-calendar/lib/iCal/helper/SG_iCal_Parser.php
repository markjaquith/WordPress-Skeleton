<?php // BUILD: Remove line

class SG_iCal_Parser {
	/**
	 * Fetches $url and passes it on to be parsed
	 * @param string $url
	 * @param SG_iCal $ical
	 */
	public static function Parse( $url, SG_iCal $ical ) {
		$content = self::Fetch( $url );
		$content = self::UnfoldLines($content);
		self::_Parse( $content, $ical );
	}

	/**
	 * Passes a text string on to be parsed
	 * @param string $content
	 * @param SG_iCal $ical
	 */
	public static function ParseString($content, SG_iCal $ical ) {
		$content = self::UnfoldLines($content);
		self::_Parse( $content, $ical );
	}

	/**
	 * Fetches a resource and tries to make sure it's UTF8
	 * encoded
	 * @return string
	 */
	protected static function Fetch( $resource ) {
		$is_utf8 = true;

		if( is_file( $resource ) ) {
			// The resource is a local file
			$content = file_get_contents($resource);

			if( ! self::_ValidUtf8( $content ) ) {
				// The file doesn't appear to be UTF8
				$is_utf8 = false;
			}
		} else {
			// The resource isn't local, so it's assumed to
			// be a URL
			$c = curl_init();
			curl_setopt($c, CURLOPT_URL, $resource);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
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
			$content = curl_exec($c);
			$ct = curl_getinfo($c, CURLINFO_CONTENT_TYPE);
			$enc = preg_replace('/^.*charset=([-a-zA-Z0-9]+).*$/', '$1', $ct);
			if( ! self::_ValidUtf8( $content ) ) {
				// The data isn't utf-8
				$is_utf8 = false;
			}
		}

		if( !$is_utf8 ) {
			$content = utf8_encode($content);
		}

		return $content;
	}

	/**
	 * Takes the string $content, and creates a array of iCal lines.
	 * This includes unfolding multi-line entries into a single line.
	 * @param $content string
	 */
	protected static function UnfoldLines($content) {
		$data = array();
		$content = explode("\n", $content);
		for( $i=0; $i < count($content); $i++) {
			$line = rtrim($content[$i]);
			while( isset($content[$i+1]) && strlen($content[$i+1]) > 0 && ($content[$i+1]{0} == ' ' || $content[$i+1]{0} == "\t" )) {
				$line .= rtrim(substr($content[++$i],1));
			}
			$data[] = $line;
		}
		return $data;
	}

	/**
	 * Parses the feed found in content and calls storeSection to store
	 * parsed data
	 * @param string $content
	 * @param SG_iCal $ical
	 */
	private static function _Parse( $content, SG_iCal $ical ) {
		$main_sections = array('vevent', 'vjournal', 'vtodo', 'vtimezone', 'vcalendar');
		$array_idents = array('exdate','rdate');
		$sections = array();
		$section = '';
		$current_data = array();
		foreach( $content AS $line ) {
			$line = new SG_iCal_Line($line);
			if( $line->isBegin() ) {
				// New block of data, $section = new block
				$section = strtolower($line->getData());
				$sections[] = strtolower($line->getData());
			} elseif( $line->isEnd() ) {
				// End of block of data ($removed = just ended block, $section = new top-block)
				$removed = array_pop($sections);
				$section = end($sections);

				if( array_search($removed, $main_sections) !== false ) {
					self::StoreSection( $removed, $current_data[$removed], $ical);
					$current_data[$removed] = array();
				}
			} else {
				// Data line
				foreach( $main_sections AS $s ) {
					// Loops though the main sections
					if( array_search($s, $sections) !== false ) {
						// This section is in the main section
						if( $section == $s ) {
							// It _is_ the main section else
							if (in_array($line->getIdent(), $array_idents))
								//exdate could appears more that once
								$current_data[$s][$line->getIdent()][] = $line;
							else {
								$current_data[$s][$line->getIdent()] = $line;
							}
						} else {
							// Sub section
							$current_data[$s][$section][$line->getIdent()] = $line;
						}
						break;
					}
				}
			}
		}
		$current_data = array();
	}

	/**
	 * Stores the data in provided SG_iCal object
	 * @param string $section eg 'vcalender', 'vevent' etc
	 * @param string $data
	 * @param SG_iCal $ical
	 */
	protected static function storeSection( $section, $data, SG_iCal $ical ) {
		$data = SG_iCal_Factory::Factory($ical, $section, $data);
		switch( $section ) {
			case 'vcalendar':
				return $ical->setCalendarInfo( $data );
			case 'vevent':
				return $ical->addEvent( $data );
			case 'vjournal':
			case 'vtodo':
				return true; // TODO: Implement
			case 'vtimezone':
				return $ical->addTimeZone( $data );
		}
	}

	/**
	 * This functions does some regexp checking to see if the value is
	 * valid UTF-8.
	 *
	 * The function is from the book "Building Scalable Web Sites" by
	 * Cal Henderson.
	 *
	 * @param string $data
	 * @return bool
	 */
	public static function _ValidUtf8( $data ) {
		$rx  = '[\xC0-\xDF]([^\x80-\xBF]|$)';
		$rx .= '|[\xE0-\xEF].{0,1}([^\x80-\xBF]|$)';
		$rx .= '|[\xF0-\xF7].{0,2}([^\x80-\xBF]|$)';
		$rx .= '|[\xF8-\xFB].{0,3}([^\x80-\xBF]|$)';
		$rx .= '|[\xFC-\xFD].{0,4}([^\x80-\xBF]|$)';
		$rx .= '|[\xFE-\xFE].{0,5}([^\x80-\xBF]|$)';
		$rx .= '|[\x00-\x7F][\x80-\xBF]';
		$rx .= '|[\xC0-\xDF].[\x80-\xBF]';
		$rx .= '|[\xE0-\xEF]..[\x80-\xBF]';
		$rx .= '|[\xF0-\xF7]...[\x80-\xBF]';
		$rx .= '|[\xF8-\xFB]....[\x80-\xBF]';
		$rx .= '|[\xFC-\xFD].....[\x80-\xBF]';
		$rx .= '|[\xFE-\xFE]......[\x80-\xBF]';
		$rx .= '|^[\x80-\xBF]';

		return ( ! (bool) preg_match('!'.$rx.'!', $data) );
	}
}


