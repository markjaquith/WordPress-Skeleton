<?php
/**
 * iCalcnv ver 3.0
 * copyright (c) 2011 Kjell-Inge Gustafsson kigkonsult
 * kigkonsult.se/index.php
 * ical@kigkonsult.se
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
**/
define( 'ICALCNVVERSION', 'iCalcnv 3.0' );
/**
 * This class implements the iCalcnv class
 * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
 * @since  3.0 - 2011-12-05
**/
class iCalcnv {
  /**
   * @access   private
   * @var      object
   */
  private $log;
  /**
   * @access   private
   * @var      array
   */
  private $config;
  /**
   * __construct
   *
   * @access public
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-07
   * @param  object $log
   * @return void
   */
  public function __construct( & $log = false ) {
    $this->log = $log;
    if( $this->log )
      $this->log->log( '************ '.get_class( $this ).' initiate ************', PEAR_LOG_DEBUG );
            /** set config defaults */
    $this->setConfig();
  }
  /**
   * function csv2iCal
   *
   * Convert csv file to iCal format and send file to browser (default) or save Ical file to disk
   * Definition iCal  : rcf2445, http://kigkonsult.se/downloads/index.php#rfc2445
   * Definition csv   : http://en.wikipedia.org/wiki/Comma-separated_values
   * Using iCalcreator: http://kigkonsult.se/downloads/index.php#iCalcreator
   * csv directory/file read/write
   *
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-21
   * @return bool return FALSE when error
   */
  public function csv2iCal() {
    $timeexec = array( 'start' => microtime( TRUE ));
    if( $this->log )
      $this->log->log( ' ********** START **********', PEAR_LOG_NOTICE );
    $conf = array();
    foreach( $this->config as $key => $value ) {
      if( in_array( strtolower( $key ), array( 'inputdirectory', 'outputdirectory'
                                              ,'inputfilename',  'outputfilename'
                                              ,'inputurl'
                                              ,'backup',         'save', 'skip' )))
         continue;
      if( in_array( $key, array( 'del', 'sep', 'nl' )))
        $conf[$key] = "$value";
      else {
        $conf[strtoupper( $value )] = strtoupper( $key ); // flip map names
        if( $this->log )
          $this->log->log( "$value mapped to $key", PEAR_LOG_DEBUG );
      }
    }
    $fp = false;
    $string_to_parse = $this->getConfig( 'string_to_parse' );
    if( $string_to_parse ) {
    	$fp = fopen( 'php://temp/maxmemory:' . 1024*1024, 'rw' );
    	fputs( $fp, $string_to_parse );
    	fseek( $fp, 0 );
    } else {
    	/** check input/output directory and filename */
    	$inputdirFile   = $outputdirFile   =  '';
    	$inputFileParts = $outputFileParts = array();
    	$remoteInput    = $remoteOutput    = FALSE;
    	if( FALSE === $this->_fixIO( 'input', 'csv', $inputdirFile, $inputFileParts, $remoteInput )) {
    		if( $this->log ) {
    			$this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ), 5 ).' sec', PEAR_LOG_ERR );
    			$this->log->log( "ERROR 2, invalid input ($inputdirFile)", PEAR_LOG_ERR );
    			$this->log->flush();
    		}
    		return FALSE;
    	}
    	if( FALSE === $this->_fixIO( 'output', FALSE, $outputdirFile, $outputFileParts, $remoteOutput )) {
    		if( FALSE === $this->setConfig( 'outputfilename', $inputFileParts['filename'].'.ics' )) {
    			if( $this->log ) {
    				$this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ),  5 ).' sec', PEAR_LOG_ERR );
    				$this->log->log( 'ERROR 3,invalid output ('.$inputFileParts['filename'].'.csv)', PEAR_LOG_ERR );
    				$this->log->flush();
    			}
    			return FALSE;
    		}
    		$outputdirFile   = $this->getConfig ('outputdirectory' ).DIRECTORY_SEPARATOR.$inputFileParts['filename'].'.ics';
    		$outputFileParts = pathinfo( $outputdirFile );
    		if( $this->log )
    			$this->log->log( "output set to '$outputdirFile'", PEAR_LOG_NOTICE );
    	}
    	if( $this->log ) {
    		$this->log->log( "INPUT..FILE:$inputdirFile", PEAR_LOG_NOTICE );
    		$this->log->log( "OUTPUT.FILE:$outputdirFile", PEAR_LOG_NOTICE );
    	}
    	/** read csv file into input array */
    	$fp = fopen( $inputdirFile, "r" );
    	if( FALSE === $fp ) {
    		if( $this->log ) {
    			$this->log->log( "ERROR 4, unable to read file: '$inputdirFile'", PEAR_LOG_ERR );
    			$this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ),  5 ).' sec', PEAR_LOG_DEBUG );
    			$this->log->flush();
    		}
    		return FALSE;
    	}
    }
    $rows = array();
    while ( FALSE !== ( $row = fgetcsv( $fp, FALSE, $conf['sep'], $conf['del'] )))
      $rows[] = $row;
    fclose( $fp );
    $cntrows = count( $rows );
            /** iCalcreator checks when setting directory and filename */
    $calendar = new vcalendar();
    if( FALSE !== ( $unique_id = $this->getConfig( 'unique_id' )))
      $calendar->setConfig( 'unique_id', $unique_id );
    if( ! $this->getConfig( 'outputobj' ) ) {
    	if( $remoteOutput ) {
    		if( FALSE === $calendar->setConfig( 'url', $outputdirFile )) {
    			if( $this->log ) {
    				$this->log->log( "ERROR 5, iCalcreator: invalid url: '$outputdirFile'", PEAR_LOG_ERR );
    				$this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ),  5 ).' sec', PEAR_LOG_DEBUG );
    				$this->log->flush();
    			}
    			return FALSE;
    		}
    	}
    	else {
    		if( FALSE === $calendar->setConfig( 'directory', $outputFileParts['dirname'] )) {
    			if( $this->log ) {
    				$this->log->log( "ERROR 6, INPUT FILE:'$inputdirFile'  iCalcreator: invalid directory: '".$outputFileParts['dirname']."'", PEAR_LOG_ERR );
    				$this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ),  5 ).' sec', PEAR_LOG_DEBUG );
    				$this->log->flush();
    			}
    			return FALSE;
    		}
    		if( FALSE === $calendar->setConfig( 'filename', $outputFileParts['basename'] )) {
    			if( $this->log ) {
    				$this->log->log( "ERROR 7, INPUT FILE:'$inputdirFile' iCalcreator: invalid filename: '".$outputFileParts['basename']."'", PEAR_LOG_ERR );
    				$this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ),  5 ).' sec',PEAR_LOG_DEBUG );
    				$this->log->flush();
    			}
    			return FALSE;
    		}
    	}
    }
    $timeexec['fileOk'] = microtime( TRUE );
            /** info rows */
    $actrow = 0;
    for( $row = $actrow; $row < $cntrows; $row++ ) {
      if( empty( $rows[$row] ) ||
         ( 1 >= count( $rows[$row] )) ||
         ( '' >= $rows[$row][1] ) ||
         ( 'iCal' == substr( $rows[$row][0], 0, 4 )) ||
         ( 'kigkonsult.se' == $rows[$row][0] ))
        continue;
      elseif( 'TYPE' == strtoupper( $rows[$row][0] )) {
        $actrow = $row;
        break;
      }
      elseif( 'CALSCALE' == strtoupper( $rows[$row][0] ))
        $calendar->setProperty( 'CALSCALE', $rows[$row][1] );
      elseif( 'METHOD' == strtoupper( $rows[$row][0] ))
        $calendar->setProperty( 'METHOD', $rows[$row][1] );
      elseif( 'X-' == substr( $rows[$row][0], 0, 2 ))
        $calendar->setProperty( $rows[$row][0], $rows[$row][1] );
      elseif( 2 >= count( $rows[$row] ))
        continue;
      else {
        $actrow = $row;
        break;
      }
    }
    $timeexec['infoOk'] = microtime( TRUE );
    $cntprops  = 0;
    $proporder = array();
            /** fix opt. vtimezone */
    if(( $actrow < $cntrows) && ( in_array( 'tzid', $rows[$actrow] ) || in_array( 'TZID', $rows[$actrow] ))) {
      foreach( $rows[$actrow] as $key => $header ) {
        $header = strtoupper( $header );
        if( isset( $conf[$header] )) {
          $proporder[$conf[$header]] = $key; // check map of userfriendly name to iCal property name
          if( $this->log )
            $this->log->log( "header row ix:$key => $header, replaced by ".$conf[$header], PEAR_LOG_DEBUG );
        }
        else
          $proporder[$header] = $key;
      }
      if( $this->log )
        $this->log->log( "comp proporder=".implode(',',array_flip( $proporder )), PEAR_LOG_DEBUG );
      $allowedProps = array( 'VTIMEZONE' => array( 'TZID', 'LAST-MODIFIED', 'TZURL' )
                           , 'STANDARD'  => array( 'DTSTART', 'TZOFFSETTO', 'TZOFFSETFROM', 'COMMENT', 'RDATE', 'RRULE', 'TZNAME' )
                           , 'DAYLIGHT'  => array( 'DTSTART', 'TZOFFSETTO', 'TZOFFSETFROM', 'COMMENT', 'RDATE', 'RRULE', 'TZNAME' ));
      $actrow++;
      $comp = $subcomp = $actcomp = FALSE;
      for( $row = $actrow; $row < $cntrows; $row++ ) {
        if( empty( $rows[$row] ) || ( 1 >= count( $rows[$row] )))
          continue;
        $compname = strtoupper( $rows[$row][0] );
        if( 'TYPE' == $compname ) { // next header
          $actrow = $row;
          break;
        }
        if( $comp && $subcomp ) {
          $comp->setComponent( $subcomp );
          $subcomp = FALSE;
        }
        if( 'VTIMEZONE' == $compname ) {
          if( $comp )
            $calendar->setComponent( $comp );
          $comp = new vtimezone();
          $actcomp = & $comp;
          $cntprops += 1;
        }
        elseif( 'STANDARD' == $compname ) {
          $subcomp = new vtimezone( 'STANDARD' );
          $actcomp = & $subcomp;
        }
        elseif( 'DAYLIGHT' == $compname ) {
          $subcomp = new vtimezone( 'DAYLIGHT' );
          $actcomp = & $subcomp;
        }
        else {
          if( $this->log )
            $this->log->log( "skipped $compname", PEAR_LOG_WARNING );
          continue;
        }
        foreach( $proporder as $propName => $col ) { // insert all properties into component
          if(( 2 > $col ) || ( 'ORDER' == strtoupper( $propName )))
            continue;
          $propName = strtoupper( $propName );
          if(( 'X-' != substr( $propName, 0, 2 )) &&
             ( !in_array( $propName, $allowedProps[$compname] ))) { // check if allowed property for the component
            if( $this->log )
              $this->log->log( "skipped $compname: $propName", PEAR_LOG_DEBUG );
            continue;
          }
          if( isset( $rows[$row][$col] ) && !empty( $rows[$row][$col] )) {
            $rows[$row][$col] = str_replace( array( "\r\n", "\n\r", "\n", "\r" ), $conf['nl'], $rows[$row][$col] );
            $value = ( FALSE !== strpos( $rows[$row][$col], $conf['nl'] )) ? explode( $conf['nl'], $rows[$row][$col] ) : array( $rows[$row][$col] );
            foreach( $value as $val ) {
              if( empty( $val ) && ( '0' != $val ))
                 continue;
              $del = ( FALSE !== strpos( $val, ':' )) ? ';' : ':';
              if( FALSE !== $actcomp->parse( "$propName$del$val" )) {
                if( $this->log )
                  $this->log->log( "iCalcreator->parse( '$propName $val' )", PEAR_LOG_DEBUG );
              }
              elseif( $this->log )
                $this->log->log( "ERROR 8, INPUT FILE:'$inputdirFile' iCalcreator: parse error: '$propName$del$val'", PEAR_LOG_ERR );
            } // end foreach( $value
          } // end if( isset
        } // end foreach( $proporder
      } // end for( $row = $actrow
      if( $comp && $subcomp )
        $comp->setComponent( $subcomp );
      if( $comp )
        $calendar->setComponent( $comp );
      $comp = $subcomp = $actcomp = FALSE;
    }
    $timeexec['zoneOk'] = microtime( TRUE );
            /** fix data */
    $proporder = array();
    if(( $actrow < $cntrows) && isset( $rows[$actrow][0] ) && ( 'TYPE' == strtoupper( $rows[$actrow][0] ))) {
      foreach( $rows[$actrow] as $key => $header ) {
        $header = strtoupper( $header );
      if( isset( $conf[$header] )) {
        $proporder[$conf[$header]] = $key; // check map of user friendly name to iCal property name
        if( $this->log )
          $this->log->log( "header row ix:'$key => $header', mapped to '".$conf[$header]."'", PEAR_LOG_DEBUG );
      }
      else
        $proporder[$header] = $key;
      }
      if( $this->log )
        $this->log->log( "comp proporder=".implode(',',array_flip( $proporder )), PEAR_LOG_DEBUG );
      $allowedProps = array( 'VEVENT'    => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'CONTACT', 'CREATED', 'DESCRIPTION', 'DTEND'
                                                 , 'DTSTAMP', 'DTSTART', 'DURATION', 'EXDATE', 'RXRULE', 'GEO', 'LAST-MODIFIED', 'LOCATION', 'ORGANIZER'
                                                 , 'PRIORITY', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO', 'RESOURCES', 'RRULE', 'REQUEST-STATUS', 'SEQUENCE'
                                                 , 'STATUS', 'SUMMARY', 'TRANSP', 'UID', 'URL', )
                           , 'VTODO'     => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'COMPLETED', 'CONTACT', 'CREATED', 'DESCRIPTION'
                                                 , 'DTSTAMP', 'DTSTART', 'DUE', 'DURATION', 'EXDATE', 'EXRULE', 'GEO', 'LAST-MODIFIED', 'LOCATION', 'ORGANIZER'
                                                 , 'PERCENT', 'PRIORITY', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO', 'RESOURCES', 'RRULE', 'REQUEST-STATUS'
                                                 , 'SEQUENCE', 'STATUS', 'SUMMARY', 'UID', 'URL' )
                           , 'VJOURNAL'  => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'CONTACT', 'CREATED', 'DESCRIPTION', 'DTSTAMP'
                                                 , 'DTSTART', 'EXDATE', 'EXRULE', 'LAST-MODIFIED', 'ORGANIZER', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO'
                                                 , 'RRULE', 'REQUEST-STATUS', 'SEQUENCE', 'STATUS', 'SUMMARY', 'UID', 'URL' )
                           , 'VFREEBUSY' => array( 'ATTENDEE', 'COMMENT', 'CONTACT', 'DTEND', 'DTSTAMP', 'DTSTART', 'DURATION', 'FREEBUSY', 'ORGANIZER', 'UID', 'URL' )
                           , 'VALARM'    => array( 'ACTION', 'ATTACH', 'ATTENDEE', 'DESCRIPTION', 'DURATION', 'REPEAT', 'SUMMARY', 'TRIGGER' ));
      $actrow++;
      $comp = $subcomp = $actcomp = FALSE;
      $allowedComps = array( 'VEVENT', 'VTODO', 'VJOURNAL', 'VFREEBUSY' );
      for( $row = $actrow; $row < $cntrows; $row++ ) {
        if( empty( $rows[$row] ) || ( 1 >= count( $rows[$row] )))
          continue;
        if( $comp && $subcomp ) {
          $comp->setComponent( $subcomp );
          $subcomp = FALSE;
        }
        $compname = strtoupper( $rows[$row][0] );
        if( $this->log )
          $this->log->log( "'$compname' START", PEAR_LOG_NOTICE );
        if( in_array( $compname, $allowedComps )) {
          if( $comp )
            $calendar->setComponent( $comp );
          $comp = new $rows[$row][0];
          $actcomp = & $comp;
          $cntprops += 1;
        }
        elseif( 'VALARM' == $compname ) {
          $subcomp = new valarm();
          $actcomp = & $subcomp;
        }
        else {
          if( $this->log )
            $this->log->log( "skipped $compname", PEAR_LOG_WARNING );
          continue;
        }
        foreach( $proporder as $propName => $col ) { // insert all properties into component
          if(( 2 > $col ) || ( 'ORDER' == strtoupper( $propName )))
            continue;
          $propName = strtoupper( $propName );
          if( $this->log )
            $this->log->log( "$compname $propName START (col=$col)", PEAR_LOG_DEBUG );
          if(( 'X-' != substr( $propName, 0, 2 )) &&
             ( !in_array( $propName, $allowedProps[$compname] ))) { // check if allowed property for the component
            if( $this->log )
              $this->log->log( "skipped $compname $propName", PEAR_LOG_NOTICE  );
            continue;
          }
          if(( isset( $rows[$row][$col] ) && !empty( $rows[$row][$col] )) ||
             (( 'SEQUENCE' == $propName ) && ('0' == $rows[$row][$col] ))) {
            $rows[$row][$col] = str_replace( array( "\r\n", "\n\r", "\n", "\r" ), $conf['nl'], $rows[$row][$col] );
            $value = ( FALSE !== strpos( $rows[$row][$col], $conf['nl'] )) ? explode( $conf['nl'], $rows[$row][$col] ) : array( $rows[$row][$col] );
            $ctests = array ( '://', 'fax:', 'cid:', 'sms:', 'tel:', 'urn:', 'crid:', 'news:', 'pres:', 'mailto:', 'MAILTO:' );
            foreach( $value as $val ) {
              if( empty( $val ) && ( '0' != $val ) && ( 0 != $val ))
                 continue;
              if( 'GEO' == $propName ) {
                $parseval = ( FALSE !== strpos( $val, ':' )) ? "GEO$val" : "GEO:$val";
                if( FALSE === $actcomp->parse( $parseval )) {
                  if( $this->log )
                    $this->log->log( "ERROR 11, INPUT FILE:'$inputdirFile' iCalcreator: parse error: '$parseval'", PEAR_LOG_ERR );
                }
              }
              elseif( 'REQUEST-STATUS' == $propName ) { // 'REQUEST-STATUS' without any parameters.. .
                if( FALSE === $actcomp->parse( "$propName:$val" )) {
                  if( $this->log )
                    $this->log->log( "ERROR 12, INPUT FILE:'$inputdirFile' iCalcreator: parse error: '$propName:$val'", PEAR_LOG_ERR );
                }
              }
              $cntm = $pos = 0;
              foreach( $ctests as $tst )
                $cntm += substr_count( $val, $tst );
              $cntc = substr_count( $val, ':' );
              $cntq = substr_count( $val, '=' );
              $cnts = substr_count( $val, ';' );
              if(( 0 == $cntq ) && ( 0 == $cnts )) // no parameters
                $del = ':';
              elseif(( 1 == $cntc ) && (( $cntq + 1 ) == $cnts )) // parameters and colon
                $del = ';';
              elseif( $cntc == ( $cntm + 1))
                $del = ';';
              else
                $del = (( 1 >  $cntm ) && ( 0 < $cntc )) ? ';' : ':';
              if(( 'X-' == substr( $propName, 0, 2 )) ||
                 ( in_array( $propName, array( 'CATEGORIES', 'COMMENT', 'CONTACT', 'DESCRIPTION', 'LOCATION', 'RESOURCES', 'SUMMARY' )))) {
                $val = str_replace( ',', '\,', $val );
                if( FALSE !== ( $pos = strpos( $del.$val, ':' ))) {
                  while( FALSE !== ( $pos2 = strpos( $val, ';', $pos+1 ))) {
                    $val = substr( $val, 0, $pos2).'\;'.substr( $val, ( $pos2 + 1 ));
                    if( $this->log ) $this->log->log( "pos=$pos pos2=$pos2 val='$val'", PEAR_LOG_DEBUG );
                    $pos = $pos2+1;
                  }
                }
              }
              if( FALSE === $actcomp->parse( "$propName$del$val" )) {
                if( $this->log )
                  $this->log->log( "ERROR 13, INPUT FILE:'$inputdirFile' iCalcreator: parse error: '$propName$del$val'", PEAR_LOG_ERR );
              }
              elseif( $this->log )
                $this->log->log( "iCalcreator->parse( '$propName$del$val' )", PEAR_LOG_DEBUG );
            } // end foreach( $value as $val
          } // end if( isset( $rows[$row][$col]
        } // end foreach( $proporder
      } // end for( $row = $actrow;
      if( $comp && $subcomp )
        $comp->setComponent( $subcomp );
      if( $comp )
        $calendar->setComponent( $comp );
    }
    $save = $this->getConfig( 'save' );
    if( $this->log ) {
      $timeexec['exit'] = microtime( TRUE );
      $msg  = "INPUT '$inputdirFile'";
      $msg .= ' fileOk:' .number_format(( $timeexec['fileOk']  - $timeexec['start'] ),  5 );
      $msg .= ' infoOk:' .number_format(( $timeexec['infoOk']  - $timeexec['fileOk'] ), 5 );
      $msg .= ' zoneOk:' .number_format(( $timeexec['zoneOk']  - $timeexec['infoOk'] ), 5 );
      $msg .= ' compOk:' .number_format(( $timeexec['exit']    - $timeexec['zoneOk'] ), 5 );
      $msg .= ' total:'  .number_format(( $timeexec['exit']    - $timeexec['start'] ),  5 ).' sec';
      $this->log->log( $msg, PEAR_LOG_DEBUG );
      $msg  = "'$inputdirFile' (".$cntprops.' components) start:'.date( 'H:i:s', $timeexec['start'] );
      $msg .= ' total:'  .number_format(( $timeexec['exit']    - $timeexec['start'] ),  5 ).' sec';
      if( $save )
        $msg .= " -> '$outputdirFile'";
      $this->log->log( $msg, PEAR_LOG_NOTICE );
    }
            /** return calendar, save or send the file */
    if( $this->getConfig( 'outputobj' ) ) {
      if( $this->log ) {
        $this->log->log( "INPUT FILE:'$inputdirFile' returning iCalcreator vcalendar instance", PEAR_LOG_NOTICE );
        $this->log->flush();
      }
      return $calendar;
      exit();
    }
    $d  = $calendar->getConfig( 'directory' );
    $f  = $calendar->getConfig( 'filename' );
    $df = $d.DIRECTORY_SEPARATOR.$f;
    if( $save ) {
      if( FALSE !== $calendar->saveCalendar()) {
        if( $this->log ) {
           $this->log->log( "INPUT FILE:'$inputdirFile' saved '$df'", PEAR_LOG_NOTICE );
           $this->log->flush();
        }
        return TRUE;
      }
      else { // ??
        if( $this->log ) {
          $this->log->log( "ERROR 16, INPUT FILE:'$inputdirFile' can't write to output file : '$df'", PEAR_LOG_ERR );
          $this->log->flush();
        }
        return FALSE;
      }
    }
    else {
      if( $this->log ) {
        $this->log->log( "INPUT FILE:'$inputdirFile' returning : '$f'", PEAR_LOG_NOTICE );
        $this->log->flush();
      }
      $output   = $calendar->createCalendar();
      $filesize = strlen( $output );
      if( substr_count( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' )) {
        $output   = gzencode( $output, 9 );
        $filesize = strlen( $output );
        header( 'Content-Encoding: gzip' );
        header( 'Vary: *' );
      }
      header( 'Content-Type: text/calendar; charset=utf-8' );
      header( "Content-Disposition: attachment; filename='$f'" );
      header( 'Cache-Control: max-age=10' );
      header( 'Content-Length: '.$filesize );
      echo $output;
    }
    return TRUE;
  }
  /**
   * getConfig
   *
   * @access public
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-15
   * @param  string $key
   * @param  string $subkey, opt.
   * @return mixed
   */
  public function getConfig( $key, $subkey=FALSE ) {
    if(( '2' <= $key) && ( '99' > $key )) // iCal2csv column
      $continue = TRUE;
    elseif( in_array( strtolower( $key ), array( 'inputdirectory', 'outputdirectory'
                                                ,'inputfilename',  'outputfilename'
                                                ,'inputurl',       'outputobj'
                                                ,'backup',         'save'
                                                ,'del', 'sep', 'nl', 'skip' )))
      $key = strtolower( $key );
    else
      $key = strtoupper( $key );
    if( FALSE !== $subkey ) {
      if( isset( $this->config[$key][$subkey] ))
        return $this->config[$key][$subkey];
      if( $this->log )
        $this->log->log( "config keys '$key' '$subkey' not found", PEAR_LOG_WARNING );
      return FALSE;
    }
    if( isset( $this->config[$key] ))
      return $this->config[$key];
    if( $this->log && !ctype_digit((string) $key ) && !in_array( $key, array( 'backup', 'outputobj', 'save' )))
      $this->log->log( "config key '$key' not found", PEAR_LOG_WARNING );
    return FALSE;
  }
  /**
   * setConfig
   *
   * @access public
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-15
   * @param  array $config
   * @return bool return FALSE when error
   */
  public function setConfig( $config=FALSE, $value=FALSE ) {
    if( !$config ) {
      $this->config = array();
      $this->setConfig( 'inputdirectory',  '.' );
      $this->setConfig( 'outputdirectory', '.' );
      $this->setConfig( 'del',             '"' );
      $this->setConfig( 'sep',             ',' );
      $this->setConfig( 'nl',              PHP_EOL );
      $this->setConfig( 'unique_id', ( isset( $_SERVER['SERVER_NAME'] )) ? gethostbyname( $_SERVER['SERVER_NAME'] ) : 'localhost' );
      $this->setConfig( 'extension_check', true );// Check for extension by default
      $this->setConfig( 'string_to_parse', false );// By default you have a file
      if( $this->log )
        $this->log->log( 'All config default values are set', PEAR_LOG_INFO );
      return TRUE;
    }
    if( is_array( $config )) { // ensure right order when setting config's
      $confKeys = array_keys( $config );
      $confOrder = array( 'inputdirectory', 'inputfilename', 'save', 'outputdirectory', 'outputfilename', 'backup', );
      foreach( $confKeys as $key )
        $confKeys[strtolower( $key )] = $key;
      foreach( $confOrder as $key ) {
        if( isset( $confKeys[$key] )) {
          if( FALSE === $this->setConfig( $key, $config[$confKeys[$key]] ))
            return FALSE;
          unset( $config[$confKeys[$key]] );
        }
      }
      foreach( $config as $key => $value ) {
        if( FALSE === $this->setConfig( $key, $value ))
          return FALSE;
      }
      return TRUE;
    }
    if( $this->log )
      $this->log->log( "setConfig: $config => ".var_export( $value, TRUE ), PEAR_LOG_NOTICE );
    if(( '2' <= $config) && ( '99' > $config )) {
      $this->config[$config] = strtoupper( $value );
      if( $this->log )
        $this->log->log( "column $config contains ".strtoupper( $value ), PEAR_LOG_DEBUG );
    }
    $key = strtolower( $config );
    switch( $key ) {
      case 'inputdirectory':
      case 'outputdirectory':
        $directory = realpath( $value );
        $msg = FALSE;
        if(( 'ouputdirectory' == $key ) && !is_dir( $directory ) && ( FALSE === @mkdir( $directory )))
          $msg = "Can't create directory ($dirFile)";
        if( !$msg && !file_exists( $directory ))
          $msg = "No directory exists ($directory)";
        if( !$msg && !is_dir( $directory ))
          $msg = "Invalid directory: ($directory)";
        if( !$msg && ( 'inputdirectory' == $key ) && !is_readable( $directory ))
          $msg = "Directory not readable ($directory)";
        if( !$msg && ( 'ouputdirectory' == $key ) && !is_writable( $directory ))
          $msg = "Directory not writeable ($directory)";
        if( $msg ) {
          if( $this->log )
            $this->log->log( $msg, PEAR_LOG_ERR );
          return FALSE;
        }
        if( $this->log )
          $this->log->log( "$key set to '$directory'", PEAR_LOG_DEBUG );
        $this->config[$key] = $directory;
        break;
      case 'inputfilename':
        $this->config[$key] = $value;
        if( $value && ( FALSE === $this->_fileCheckRead())) {
          unset( $this->config[$key] );
          return FALSE;
        }
        break;
      case 'outputfilename':
        $this->config[$key] = $value;
        if( $value && ( FALSE !== $this->getConfig( 'save' ))
                   && ( FALSE === $this->_fileCheckWrite( $key ))) {
          unset( $this->config[$key] );
          return FALSE;
        }
        break;
      case 'backup':
        if( $value && ( FALSE !== $this->getConfig( 'outputfilename' ))
                   && ( FALSE !== $this->getConfig( 'save' ))
                   && ( FALSE === $this->_fileCheckWrite( $key )))
          return FALSE; // it's ok, no break here.. .
      case 'save':
      case 'inputurl':
      case 'outputobj':
      case 'del':      // iCal2csv field delimiter
      case 'sep':      // iCal2csv field separator
      case 'nl':       // iCal2csv new line character(-s)
        $this->config[$key] = $value;
        break;
      case 'skip': // iCal2csv column
        if( !is_array( $value ))
          $this->config['skip'][] = strtoupper( $value );
        foreach( $value as $six => $skipp )
          $this->config['skip'][$six] = strtoupper( $skipp );
        break;
      default:
        $this->config[strtoupper( $key )] = $value;
        if( $this->log )
          $this->log->log( strtoupper( $key )." mapped to $value", PEAR_LOG_DEBUG );
        break;
    } // end switch
    return TRUE;
  }
  /**
   * function _fileCheckRead
   *
   * Check if input file is a file and readable
   *
   * @access private
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-05
   * @return bool return FALSE when error
   */
  private function _fileCheckRead() {
    $msg     = FALSE;
    $dirFile = $this->config['inputdirectory'].DIRECTORY_SEPARATOR.$this->config['inputfilename'];
    if( $this->log )
      $this->log->log( "START file='$dirFile'", PEAR_LOG_DEBUG );
    clearstatcache();
    if( !$msg && !file_exists( $dirFile ))              $msg = "No file exists ($dirFile)";
    if( !$msg && !is_file( $dirFile ))                  $msg = "File no file ($dirFile)";
    if( !$msg && !is_readable( $dirFile ))              $msg = "File not readable ($dirFile)";
    if( !$msg && ( 0 >= filesize( $dirFile )))          $msg = "File empty ($dirFile)";
    clearstatcache();
    if( $msg ) {
      if( $this->log )
        $this->log->log( $msg, PEAR_LOG_ERR );
      return FALSE;
    }
    if( $this->log )
      $this->log->log( " ok ($dirFile)", PEAR_LOG_INFO );
    return TRUE;
  }
  /**
   * function _fileCheckWrite
   *
   * Check if a filename is a writeable file
   * file is created if missing
   * if file exists,it may be backuped with ext .YmdHis.old
   *
   * @access private
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-05
   * @param  string $operation
   * @return bool return FALSE when error
   */
  private function _fileCheckWrite( $operation='check' ) {
    $msg     = FALSE;
    $dirFile = $this->config['outputdirectory'].DIRECTORY_SEPARATOR.$this->config['outputfilename'];
    if( $this->log )
      $this->log->log( "($operation), file='$dirFile'", PEAR_LOG_DEBUG );
    if( FALSE !== $this->getConfig( 'save' )) {
      if( !$msg && !file_exists( $dirFile ) && ( FALSE === touch( $dirFile )))
                                                        $msg = "Can't create file ($dirFile)";
      if( !$msg && !is_file( $dirFile ))                $msg = "File no file ($dirFile)";
      if( !$msg && !is_writable( $dirFile ))            $msg = "File not writeable ($dirFile)";
    }
    if( $msg ) {
      if( $this->log )
        $this->log->log( "($operation) $msg", PEAR_LOG_ERR );
      clearstatcache();
      return FALSE;
    }
    if( 0 < filesize( $dirFile ) && ( 'backup' == $operation ) && ( FALSE !== $this->getConfig( 'save' ))) { // file exists, make unique backup
      $dirFileOld = $dirFile.'.'.date( 'YmdHis', filemtime( $dirFile )).'.old';
      if( @copy( $dirFile, $dirFileOld ))
        if ( $this->log )
         $this->log->log( "Existing file ($dirFile) saved as $dirFileOld", PEAR_LOG_NOTICE );
      else { // ??
        if( $this->log )
          $this->log->log( "($operation), unable to backup file ($dirFile) as '$dirFileOld'", PEAR_LOG_ERR );
        clearstatcache();
        return FALSE;
      }
    }
    clearstatcache();
    if( $this->log )
      $this->log->log( "($operation) ok ($dirFile)", PEAR_LOG_INFO );
    return TRUE;
  }
  /**
   * function _fixio
   *
   * Check if a filename is a writeable file
   * file is created if missing
   * if file exists,it may be backuped with ext .YmdHis.old
   *
   * @access private
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-12
   * @param  string $operation
   * @param  mixed  $ext
   * @param  string $dirFile
   * @param  array  $fileParts
   * @param  bool   $remote
   * @return bool   return FALSE when error
   */
  private function _fixIO( $operation, $ext, & $dirFile, & $fileParts, & $remote ) {
    if( FALSE !== ( $dirFile = $this->getConfig( $operation.'filename' ))) {
      if( $this->log )
        $this->log->log( "found (1): '$dirFile'", PEAR_LOG_DEBUG );
      if( $this->getConfig( 'extension_check' ) ) {
      	if( $ext && ( strtolower( $ext ) !== strtolower( substr( $dirFile, -3 )))) {
      		if( $this->log )
      			$this->log->log( "ERROR 1, '$ext' wanted, invalid file extension found ($dirFile)", PEAR_LOG_ERR );
      		return FALSE;
      	}
      }
      $dirFile   = $this->getConfig( $operation.'directory' ).DIRECTORY_SEPARATOR.$dirFile;
      $fileParts = pathinfo( $dirFile );
      if( $this->log )
        $this->log->log( 'fileParts (1):'.var_export( $fileParts, TRUE ), PEAR_LOG_DEBUG );
      return TRUE;
    }
    elseif(( 'input' == $operation ) && FALSE !== ( $dirFile = $this->getConfig( 'inputurl' ))) {
      if( $this->log )
        $this->log->log( "found (2): $dirFile", PEAR_LOG_DEBUG );
      $fileParts = parse_url( $dirFile );
      $fileParts = array_merge( $fileParts, pathinfo( $fileParts['path'] ));
      $remote    = (( 'http://' == strtolower( substr( $dirFile, 0, 7 ))) || ( 'webcal://' == strtolower( substr( $dirFile, 0, 9 )))) ? TRUE : FALSE;
      if( $this->log )
        $this->log->log( 'fileParts (2):'.var_export( $fileParts, TRUE ), PEAR_LOG_DEBUG );
      return TRUE;
    }
    if( $this->log )
      $this->log->log( "No $operation found!!", PEAR_LOG_WARNING );
    return FALSE;
  }
  /**
   * function iCal2csv
   *
   * Convert iCal file to csv format and send file to browser (default) or save csv file to disk
   * Definition iCal  : rcf2445, http://kigkonsult.se/downloads/index.php#rfc2445
   * Definition csv   : http://en.wikipedia.org/wiki/Comma-separated_values
   * Using iCalcreator: http://kigkonsult.se/downloads/index.php#iCalcreator
   * ical directory/file read/write error OR iCalcreator parse error will be directed to log
   *
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-21
   * @param  object $calendar opt. iCalcreator calendar instance
   * @return bool   returns FALSE when error
   */
  public function iCal2csv( $calendar=FALSE ) {
    $timeexec = array( 'start' => microtime( TRUE ));
    if( $this->log )
      $this->log->log( ' ********** START **********', PEAR_LOG_NOTICE );
            /** check input/output directory and filename */
    $inputdirFile   = $outputdirFile   =  '';
    $inputFileParts = $outputFileParts = array();
    $remoteInput    = $remoteOutput    = FALSE;
    if( $calendar ) {
      $inputdirFile   = $calendar->getConfig( 'DIRFILE' );
      $inputFileParts = pathinfo( $inputdirFile );
      $inputFileParts['dirname'] = realpath( $inputFileParts['dirname'] );
      if( $this->log )
        $this->log->log( 'fileParts:'.var_export( $inputFileParts, TRUE ), PEAR_LOG_DEBUG );
    }
    elseif( FALSE === $this->_fixIO( 'input', 'ics', $inputdirFile, $inputFileParts, $remoteInput )) {
      if( $this->log ) {
        $this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ), 5 ).' sec', PEAR_LOG_ERR );
        $this->log->log( "ERROR 2, invalid input ($inputdirFile)", PEAR_LOG_ERR );
        $this->log->flush();
      }
      return FALSE;
    }
    if( FALSE === $this->_fixIO( 'output', FALSE, $outputdirFile, $outputFileParts, $remoteOutput )) {
      if( FALSE === $this->setConfig( 'outputfilename', $inputFileParts['filename'].'.csv' )) {
        if( $this->log ) {
          $this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ),  5 ).' sec', PEAR_LOG_ERR );
          $this->log->log( 'ERROR 3, invalid output ('.$inputFileParts['filename'].'.csv)', PEAR_LOG_ERR );
          $this->log->flush();
        }
        return FALSE;
      }
      $outputdirFile   = $this->getConfig( 'outputdirectory' ).DIRECTORY_SEPARATOR.$inputFileParts['filename'].'.csv';
      $outputFileParts = pathinfo( $outputdirFile );
      if( $this->log )
        $this->log->log( "output set to '$outputdirFile'", PEAR_LOG_INFO );
    }
    if( $this->log ) {
      $this->log->log( "INPUT..FILE:$inputdirFile", PEAR_LOG_NOTICE );
      $this->log->log( "OUTPUT.FILE:$outputdirFile", PEAR_LOG_NOTICE );
    }
    if( $calendar )
      $calnl = $calendar->getConfig( 'nl' );
    else {  /** iCalcreator set config, read and parse input iCal file */
      $calendar = new vcalendar();
      if( FALSE !== ( $unique_id = $this->getConfig( 'unique_id' )))
        $calendar->setConfig( 'unique_id', $unique_id );
      $calnl = $calendar->getConfig( 'nl' );
      if( $remoteInput ) {
        if( FALSE === $calendar->setConfig( 'url', $inputdirFile )) {
          if( $this->log ) {
            $this->log->log( "ERROR 4, INPUT FILE:'$inputdirFile' iCalcreator: invalid url", PEAR_LOG_ERR );
            $this->log->flush();
          }
          return FALSE;
        }
      }
      else {
        if( FALSE === $calendar->setConfig( 'directory', $inputFileParts['dirname'] )) {
          if( $this->log ) {
            $this->log->log( "ERROR 5, INPUT FILE:'$inputdirFile' iCalcreator: invalid directory: '".$inputFileParts['dirname']."'", PEAR_LOG_ERR );
            $this->log->flush();
          }
          return FALSE;
        }
        if( FALSE === $calendar->setConfig( 'filename',  $inputFileParts['basename'] )) {
          if( $this->log ) {
            $this->log->log( "ERROR 6, INPUT FILE:'$inputdirFile' iCalcreator: invalid filename: '".$inputFileParts['basename']."'", PEAR_LOG_ERR );
            $this->log->flush();
          }
          return FALSE;
        }
      }
      if( FALSE === $calendar->parse()) {
        if( $this->log ) {
          $this->log->log( "ERROR 7, INPUT FILE:'$inputdirFile' iCalcreator parse error", PEAR_LOG_ERR );
          $this->log->flush();
        }
        return FALSE;
      }
    } // end if( !$calendar )
    $timeexec['fileOk'] = microtime( TRUE );
    if( !function_exists( 'iCaldate2timestamp' )) {
      function iCaldate2timestamp( $d ) {
        if( 6 > count( $d ))
          return mktime( 0, 0, 0, $d['month'], $d['day'], $d['year'] );
        else
          return mktime( $d['hour'], $d['min'], $d['sec'], $d['month'], $d['day'], $d['year'] );
      }
    }
    if( !function_exists( 'fixiCalString' )) {
      function fixiCalString( $s ) {
        $s = str_replace( '\,',   ',',     $s );
        $s = str_replace( '\;',   ';',     $s );
        $s = str_replace( '\n ',  chr(10), $s );
        $s = str_replace( '\\\\', '\\',    $s );
        return $s;
      }
    }
            /** create output array */
    $rows = array();
            /** info rows */
    $rows[] = array( 'kigkonsult.se', ICALCREATOR_VERSION, ICALCNVVERSION, date( 'Y-m-d H:i:s' ));
    $inputdirFile = ( $remoteInput ) ? $inputdirFile : $inputFileParts['basename'];
    $rows[] = array( 'iCal input', $inputdirFile, 'csv output', $outputFileParts['basename'] );
    if( FALSE !== ($prop = $calendar->getProperty( 'CALSCALE' )))
      $rows[] = array( 'CALSCALE', $prop );
    if( FALSE !== ( $prop = $calendar->getProperty( 'METHOD' )))
      $rows[] = array( 'METHOD', $prop );
    while( FALSE !== ( $xprop = $calendar->getProperty()))
      $rows[] = array( $xprop[0], $xprop[1] );
    $timeexec['infoOk'] = microtime( TRUE );
    if( FALSE === ( $propsToSkip = $this->getConfig( 'skip')))
      $propsToSkip = array();
            /** fix property order list */
    $proporderOrg = array();
    for( $key = 2; $key < 99; $key++ ) {
      if( FALSE !== ( $value = $this->getConfig( $key ))) {
        $proporderOrg[$value] = $key;
        if( $this->log )
          $this->log->log( "$value in column $key", 7 );
      }
    }
            /** fix vtimezone property order list */
    $proporder          = $proporderOrg;
    $proporder['TYPE']  =  0;
    $proporder['ORDER'] =  1;
    $props = array( 'TZID', 'LAST-MODIFIED', 'TZURL', 'DTSTART', 'TZOFFSETTO', 'TZOFFSETFROM'
                  , 'COMMENT', 'RRULE', 'RDATE', 'TZNAME' );
    $pix = 2;
    foreach( $props as $prop ) {
      if( isset( $proporder[$prop] )) continue;
      if( in_array( $prop, $propsToSkip )) {
        if( $this->log )
          $this->log->log( "$prop removed from output", PEAR_LOG_DEBUG );
        continue;
      }
      while( in_array( $pix, $proporder )) $pix++;
      $proporder[$prop] = $pix++;
    }
            /** remove unused properties from and add x-props to property order list */
    $maxpropix = 11;
    if( $maxpropix != ( count( $proporder ) - 1 ))
      $maxpropix = count( $proporder ) - 1;
    $compsinfo = $calendar->getConfig( 'compsinfo');
    $potmp = array();
    $potmp[0]                   =  'TYPE';
    $potmp[1]                   =  'ORDER';
    foreach( $compsinfo as $cix => $compinfo) {
      if( 'vtimezone' != $compinfo['type'] )
        continue;
      $comp = $calendar->getComponent( $compinfo['ordno'] );
      foreach( $compinfo['props'] as $propName => $propcnt ) {
        if( !in_array( $propName, $potmp ) && isset( $proporder[$propName] ))
          $potmp[$proporder[$propName]] = $propName;
        elseif( 'X-PROP' == $propName ) {
          while( $xprop = $comp->getProperty()) {
            if( !in_array( $xprop[0], $potmp )) {
              $maxpropix += 1;
              $potmp[$maxpropix] = $xprop[0];
            } // end if
          } // end while xprop
        } // end X-PROP
      } // end $compinfo['props']
      if( isset( $compinfo['sub'] )) {
        foreach( $compinfo['sub'] as $compinfo2 ) {
          foreach( $compinfo2['props'] as $propName => $propcnt ) {
            if( !in_array( $propName, $potmp ) && isset( $proporder[$propName] ))
              $potmp[$proporder[$propName]] = $propName;
            elseif( 'X-PROP' == $propName ) {
              $scomp = $comp->getComponent( $compinfo2['ordno'] );
              while( $xprop = $scomp->getProperty()) {
                if( !in_array( $xprop[0], $potmp )) {
                  $maxpropix += 1;
                  $potmp[$maxpropix] = $xprop[0];
                } // end if
              } // end while xprop
            } // end X-PROP
          } // end $compinfo['sub']['props']
        } // end foreach( $compinfo['sub']
      } // end if( isset( $compinfo['sub']
    } // end foreach compinfo - vtimezone
    ksort( $potmp, SORT_NUMERIC );
    $proporder = array_flip( array_values( $potmp ));
    if( $this->log )
      $this->log->log( "timezone proporder=".implode(',',array_flip($proporder)), PEAR_LOG_DEBUG );
            /** create vtimezone info */
    $row = count( $rows ) - 1;
    if( 2 < count( $proporder )) {
      $row += 1;
            /** create vtimezone header row */
      foreach( $proporder as $propName => $col ) {
        if( isset( $this->config[$propName] )) {
          $rows[$row][$col] = $this->config[$propName]; // check map of userfriendly name to iCal property name
          if( $this->log )
            $this->log->log( "header row, col=$col: $propName, replaced by ".$this->config[$propName], PEAR_LOG_DEBUG );
        }
        else
          $rows[$row][$col] = $propName;
      }
      $allowedProps = array( 'VTIMEZONE' => array( 'TZID', 'LAST-MODIFIED', 'TZURL' )
                           , 'STANDARD'  => array( 'DTSTART', 'TZOFFSETTO', 'TZOFFSETFROM', 'COMMENT', 'RDATE', 'RRULE', 'TZNAME' )
                           , 'DAYLIGHT'  => array( 'DTSTART', 'TZOFFSETTO', 'TZOFFSETFROM', 'COMMENT', 'RDATE', 'RRULE', 'TZNAME' ));
            /** create vtimezone data rows */
      foreach( $compsinfo as $cix => $compinfo) {
        if( 'vtimezone' != $compinfo['type'] )
          continue;
        $row += 1;
        foreach( $proporder as $propName => $col )
          $rows[$row][] = ''; // set all cells empty
        $rows[$row][$proporder['TYPE']]  = $compinfo['type'];
        $rows[$row][$proporder['ORDER']] = $compinfo['ordno'];
        $comp = $calendar->getComponent( $compinfo['ordno'] );
        foreach( $proporder as $propName => $col ) {
          if(( 'TYPE' == $propName ) || ( 'ORDER' == $propName ))
            continue;
          if( 'X-' == substr( $propName, 0, 2 ))
            continue;
          if( !in_array( $propName, $allowedProps['VTIMEZONE'] )) { // check if component allows property
            if( $this->log )
              $this->log->log( "ERROR 8, INPUT FILE:'$inputdirFile' iCalcreator: unvalid property for component '".$compinfo['type']."': '$propName'", PEAR_LOG_INFO );
            continue;
          }
          if( isset( $compinfo['props'][$propName] )) {
            if( 'LAST-MODIFIED' == $propName )
              $fcn = 'createLastModified';
            else
              $fcn = 'create'.strtoupper( substr( $propName, 0, 1 )).strtolower( substr( $propName, 1 ));
            if( !method_exists ( $comp, $fcn )) {
              if( $this->log )
                $this->log->log( "ERROR 9, INPUT FILE:'$inputdirFile' iCalcreator: unknown property: '$propName' ($fcn)", PEAR_LOG_INFO );
              continue;
            }
            $output = str_replace( "$calnl ",     '',      rtrim( $comp->$fcn()));
            $output = str_replace( $propName.';', '',      $output );
            $output = str_replace( $propName.':', '',      $output );
            $rows[$row][$proporder[$propName]] = fixiCalString( $output );
          }
        } // end foreach( $proporder
        if( isset( $compinfo['props']['X-PROP'] ))  {
          while( $xprop = $comp->getProperty()) {
            $output = str_replace( "$calnl ", '',      rtrim( $xprop[1] ));
            $rows[$row][$proporder[$xprop[0]]] = fixiCalString( $output );
          }
        }
        if( isset( $compinfo['sub'] )) {
          foreach( $compinfo['sub'] as $compinfo2 ) {
            $row += 1;
            foreach( $proporder as $propName => $col )
              $rows[$row][] = ''; // set all cells empty
            $rows[$row][$proporder['TYPE']]  = $compinfo2['type'];
            $rows[$row][$proporder['ORDER']] = $compinfo['ordno'].':'.$compinfo2['ordno'];
            $scomp = $comp->getComponent( $compinfo2['ordno'] );
            foreach( $proporder as $propName => $col ) {
              if(( 'TYPE' == $propName ) || ( 'ORDER' == $propName ))
                continue;
              if( 'X-' == substr( $propName, 0, 2 ))
                continue;
              if( !in_array( $propName, $allowedProps[strtoupper( $compinfo2['type'] )] )) { // check if component allows property
                if( $this->log )
                  $this->log->log( "ERROR 10, INPUT FILE:'$inputdirFile' iCalcreator: unvalid property for component '".$compinfo2['type']."': '$propName'", PEAR_LOG_INFO );
                continue;
              }
              if( isset( $compinfo2['props'][$propName] )) {
                $fcn = 'create'.strtoupper( substr( $propName, 0, 1 )).strtolower( substr( $propName, 1 ));
                if( !method_exists ( $scomp, $fcn )) {
                  if( $this->log )
                    $this->log->log( "ERROR 11, INPUT FILE:'$inputdirFile' iCalcreator: unknown property: '$propName' ($fcn)", PEAR_LOG_INFO );
                  continue;
                }
                $output = str_replace( "$calnl ",     '',      rtrim( $scomp->$fcn()));
                $output = str_replace( $propName.';', '',      $output );
                $output = str_replace( $propName.':', '',      $output );
                $rows[$row][$proporder[$propName]] = fixiCalString( $output );
              }
            } // end foreach( $proporder
            if( isset( $compinfo2['props']['X-PROP'] ))  {
              while( $xprop = $scomp->getProperty()) {
                $output = str_replace( "$calnl ", '',      rtrim( $xprop[1] ));
                $rows[$row][$proporder[$xprop[0]]] = fixiCalString( $output );
              }
            }
          } // end foreach( $compinfo['sub']
        } // end if( isset( $compinfo['sub']['props'] ))
      } // end foreach
    } // end vtimezone
    $timeexec['zoneOk'] = microtime( TRUE );
    $maxColCount = count( $proporder );
            /** fix property order list */
    $proporder          = $proporderOrg;
    $proporder['TYPE']  =  0;
    $proporder['ORDER'] =  1;
    $props = array( 'UID', 'DTSTAMP', 'SUMMARY', 'DTSTART', 'DURATION', 'DTEND', 'DUE', 'RRULE', 'RDATE', 'EXRULE', 'EXDATE'
                  , 'DESCRIPTION', 'CATEGORIES', 'ORGANIZER', 'LOCATION', 'RESOURCES', 'CONTACT', 'URL', 'COMMENT', 'PRIORITY'
                  , 'ATTENDEE', 'CLASS', 'TRANSP', 'SEQUENCE', 'STATUS', 'COMPLETED', 'CREATED', 'LAST-MODIFIED', 'ACTION'
                  , 'TRIGGER', 'REPEAT', 'ATTACH', 'FREEBUSY', 'RELATED-TO', 'REQUEST-STATUS', 'GEO', 'PERCENT-COMPLETE', 'RECURRENCE-ID' );
    $pix = 2;
    foreach( $props as $prop ) {
      if( isset( $proporder[$prop] )) continue;
      if( in_array( $prop, $propsToSkip )) {
        if( $this->log )
          $this->log->log( "$prop removed from output", PEAR_LOG_DEBUG );
        continue;
      }
      while( in_array( $pix, $proporder )) $pix++;
      $proporder[$prop] = $pix++;
    }
    if( $this->log )
      $this->log->log( "comp proporder (0)=".implode(',',array_flip($proporder)), PEAR_LOG_DEBUG );
            /** remove unused properties from and add x-props to property order list */
    if( $maxpropix < (count( $proporder ) - 1))
      $maxpropix = count( $proporder ) - 1;
    $potmp = array();
    $potmp[0]                   =  'TYPE';
    $potmp[1]                   =  'ORDER';
  //  $potmp[2]                   =  'UID';
    foreach( $compsinfo as $cix => $compinfo) {
      if( 'vtimezone' == $compinfo['type'] )
        continue;
      foreach( $compinfo['props'] as $propName => $propcnt ) {
        if( !in_array( $propName, $potmp ) && isset( $proporder[$propName] ))
          $potmp[$proporder[$propName]] = $propName;
        elseif( 'X-PROP' == $propName ) {
          $comp = $calendar->getComponent( $compinfo['ordno'] );
          while( $xprop = $comp->getProperty()) {
            if( !in_array( $xprop[0], $potmp )) {
              $maxpropix += 1;
              $potmp[$maxpropix] = $xprop[0];
            } // end if
          } // while( $xprop
        } // end elseif( 'X-PROP'
      } // end foreach( $compinfo['props']
      if( isset( $compinfo['sub'] )) {
        foreach( $compinfo['sub'] as $compinfo2 ) {
          foreach( $compinfo2['props'] as $propName => $propcnt ) {
            if( !in_array( $propName, $potmp ) && isset( $proporder[$propName] ))
              $potmp[$proporder[$propName]] = $propName;
            elseif( 'X-PROP' == $propName ) {
              $scomp = $comp->getComponent( $compinfo2['ordno'] );
              while( $xprop = $scomp->getProperty()) {
                if( !in_array( $xprop[0], $potmp )) {
                  $maxpropix += 1;
                  $potmp[$maxpropix] = $xprop[0];
                } // end if
              } // end while xprop
            } // end X-PROP
          } // end $compinfo['sub']['props']
        } // end foreach( $compinfo['sub']
      } // end if( isset( $compinfo['sub']
    }
    ksort( $potmp, SORT_NUMERIC );
    $proporder = array_flip( array_values( $potmp ));
    if( $this->log )
      $this->log->log( "comp proporder=".implode(',',array_flip($proporder)), PEAR_LOG_DEBUG );
    if( $maxColCount < count( $proporder ))
      $maxColCount = count( $proporder );
            /** create header row */
    $row += 1;
    foreach( $proporder as $propName => $col ) {
      if( isset( $this->config[$propName] )) {
        $rows[$row][$col] = $this->config[$propName]; // check map of userfriendly name to iCal property name
        if( $this->log )
          $this->log->log( "header row, col=$col: $propName, replaced by ".$this->config[$propName], PEAR_LOG_DEBUG );
      }
      else
        $rows[$row][$col] = $propName;
    }
    $allowedProps = array( 'VEVENT'    => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'CONTACT', 'CREATED', 'DESCRIPTION', 'DTEND'
                                               , 'DTSTAMP', 'DTSTART', 'DURATION', 'EXDATE', 'RXRULE', 'GEO', 'LAST-MODIFIED', 'LOCATION', 'ORGANIZER'
                                               , 'PRIORITY', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO', 'RESOURCES', 'RRULE', 'REQUEST-STATUS', 'SEQUENCE'
                                               , 'STATUS', 'SUMMARY', 'TRANSP', 'UID', 'URL', )
                         , 'VTODO'     => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'COMPLETED', 'CONTACT', 'CREATED', 'DESCRIPTION'
                                               , 'DTSTAMP', 'DTSTART', 'DUE', 'DURATION', 'EXDATE', 'EXRULE', 'GEO', 'LAST-MODIFIED', 'LOCATION', 'ORGANIZER'
                                               , 'PERCENT', 'PRIORITY', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO', 'RESOURCES', 'RRULE', 'REQUEST-STATUS'
                                               , 'SEQUENCE', 'STATUS', 'SUMMARY', 'UID', 'URL' )
                         , 'VJOURNAL'  => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'CONTACT', 'CREATED', 'DESCRIPTION', 'DTSTAMP'
                                               , 'DTSTART', 'EXDATE', 'EXRULE', 'LAST-MODIFIED', 'ORGANIZER', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO'
                                               , 'RRULE', 'REQUEST-STATUS', 'SEQUENCE', 'STATUS', 'SUMMARY', 'UID', 'URL' )
                         , 'VFREEBUSY' => array( 'ATTENDEE', 'COMMENT', 'CONTACT', 'DTEND', 'DTSTAMP', 'DTSTART', 'DURATION', 'FREEBUSY', 'ORGANIZER', 'UID', 'URL' )
                         , 'VALARM'    => array( 'ACTION', 'ATTACH', 'ATTENDEE', 'DESCRIPTION', 'DURATION', 'REPEAT', 'SUMMARY', 'TRIGGER' ));
            /** create data rows */
    foreach( $compsinfo as $cix => $compinfo) {
      if( 'vtimezone' == $compinfo['type'] )
        continue;
      $row += 1;
      foreach( $proporder as $propName => $col )
        $rows[$row][] = ''; // set all cells empty
      $rows[$row][$proporder['TYPE']]  = $compinfo['type'];
      $rows[$row][$proporder['ORDER']] = $compinfo['ordno'];
  //    $rows[$row][$proporder['UID']]   = $compinfo['uid'];
      $comp = $calendar->getComponent( $compinfo['ordno'] );
      foreach( $proporder as $propName => $col ) {
        if(( 'TYPE' == $propName ) || ( 'ORDER' == $propName ))
          continue;
        if( 'X-' == substr( $propName, 0, 2 ))
          continue;
        if( !in_array( $propName, $allowedProps[strtoupper( $compinfo['type'] )] )) { // check if component allows property
          if( $this->log )
            $this->log->log( "ERROR 12, INPUT FILE:'$inputdirFile' iCalcreator: unvalid property for component '".$compinfo['type']."': '$propName'", PEAR_LOG_INFO );
          continue;
        }
        if( isset( $compinfo['props'][$propName] )) {
          switch( $propName ) {
            case 'LAST-MODIFIED' ;
              $fcn = 'createLastModified';
              break;
            case 'RECURRENCE-ID':
              $fcn = 'createRecurrenceid';
              break;
            case 'RELATED-TO':
              $fcn = 'createRelatedTo';
              break;
            case 'REQUEST-STATUS':
              $fcn = 'createRequestStatus';
              break;
            case 'PERCENT-COMPLETE':
              $fcn = 'createPercentComplete';
              break;
           default:
            $fcn = 'create'.strtoupper( substr( $propName, 0, 1 )).strtolower( substr( $propName, 1 ));
          }
          if( !method_exists ( $comp, $fcn )) {
            if( $this->log )
              $this->log->log( 'ERROR 12, INPUT FILE:"'.$inputdirFile.'" iCalcreator: unknown property: "'.$propName.'" ('.$fcn.')', PEAR_LOG_INFO );
            continue;
          }
          $output = str_replace( "$calnl ",     '',      rtrim( $comp->$fcn()));
          if( 'SEQUENCE:0' == $output ) {
            $rows[$row][$proporder[$propName]] = '0';
            continue;
          }
          $output = str_replace( $propName.';', '',      $output );
          $output = str_replace( $propName.':', '',      $output );
          $rows[$row][$proporder[$propName]] = fixiCalString( $output );
        }
      } // end foreach( $proporder
      if( isset( $compinfo['props']['X-PROP'] ))  {
        while( $xprop = $comp->getProperty()) {
          $output = str_replace( "$calnl ", '', rtrim( $xprop[1] ));
          $rows[$row][$proporder[$xprop[0]]] = fixiCalString( $output );
        }
      }
      if( isset( $compinfo['sub'] )) {
        foreach( $compinfo['sub'] as $compinfo2 ) {
          $row += 1;
          foreach( $proporder as $propName => $col )
            $rows[$row][] = ''; // set all cells empty
          $rows[$row][$proporder['TYPE']]  = $compinfo2['type'];
          $rows[$row][$proporder['ORDER']] = $compinfo['ordno'].':'.$compinfo2['ordno'];
          $scomp = $comp->getComponent( $compinfo2['ordno'] );
          foreach( $proporder as $propName => $col ) {
            if(( 'TYPE' == $propName ) || ( 'ORDER' == $propName ))
              continue;
            if( 'X-' == substr( $propName, 0, 2 ))
              continue;
            if( !in_array( $propName, $allowedProps[strtoupper( $compinfo2['type'] )] )) { // check if component allows property
              if( $this->log )
                $this->log->log( "ERROR 13, INPUT FILE:'$inputdirFile' iCalcreator: unvalid property for component '".$compinfo2['type']."': '$propName'", PEAR_LOG_INFO );
              continue;
            }
            if( isset( $compinfo2['props'][$propName] )) {
              $fcn = 'create'.strtoupper( substr( $propName, 0, 1 )).strtolower( substr( $propName, 1 ));
              if( !method_exists ( $scomp, $fcn )) {
                if( $this->log )
                  $this->log->log( 'ERROR 14, INPUT FILE:"'.$inputdirFile.'" iCalcreator: unknown property: "'.$propName.'" ('.$fcn.')', PEAR_LOG_INFO );
                continue;
              }
              $output = str_replace( "$calnl ", '', rtrim( $scomp->$fcn()));
              $output = str_replace( $propName.';', '',   $output );
              $output = str_replace( $propName.':', '',   $output );
              $rows[$row][$proporder[$propName]] = fixiCalString( $output );
            }
          } // end foreach( $proporder
          if( isset( $compinfo2['props']['X-PROP'] ))  {
            while( $xprop = $scomp->getProperty()) {
              $output = str_replace( "$calnl ", '', rtrim( $xprop[1] ));
              $rows[$row][$proporder[$xprop[0]]] = fixiCalString( $output );
            }
          }
        } // if( isset( $compinfo2['props']['X-PROP']
      } // end if( isset( $compinfo['sub']
    } // foreach( $compsinfo as
    $timeexec['compOk'] = microtime( TRUE );
            /** fix csv format */
    // fields that contain commas, double-quotes, or line-breaks must be quoted,
    // a quote within a field must be escaped with an additional quote immediately preceding the literal quote,
    // space before and after delimiter commas may be trimmed (which is prohibited by RFC 4180)
    // a line break within an element must be preserved.
    // Fields may ALWAYS be enclosed within double-quote characters, whether necessary or not.
    foreach( $rows as $row => $line ) {
      for( $col = 0; $col < $maxColCount; $col++ ) {
        if( !isset( $line[$col] ) || ( empty( $line[$col] ) && ( '0' != $line[$col] ))) {
          $rows[$row][$col] = $this->config['del'].$this->config['del'];
          continue;
        }
        if( ctype_digit( $line[$col] ))
          continue;
        $cell = str_replace( $this->config['del'], $this->config['del'].$this->config['del'], $line[$col] );
        $rows[$row][$col] = $this->config['del'].$cell.$this->config['del'];
      }
      $rows[$row] = implode( $this->config['sep'], $rows[$row] );
    }
    $output = implode( $this->config['nl'], $rows ).$this->config['nl'];
    $save = $this->getConfig( 'save' );
    if( $this->log ) {
      $timeexec['exit'] = microtime( TRUE );
      $msg  = "'$inputdirFile'";
      $msg .= ' fileOk:' .number_format(( $timeexec['fileOk']  - $timeexec['start'] ),  PEAR_LOG_NOTICE );
      $msg .= ' infoOk:' .number_format(( $timeexec['infoOk']  - $timeexec['fileOk'] ), PEAR_LOG_NOTICE );
      $msg .= ' zoneOk:' .number_format(( $timeexec['zoneOk']  - $timeexec['infoOk'] ), PEAR_LOG_NOTICE );
      $msg .= ' compOk:' .number_format(( $timeexec['compOk']  - $timeexec['zoneOk'] ), PEAR_LOG_NOTICE );
      $msg .= ' csvOk:'  .number_format(( $timeexec['exit']    - $timeexec['compOk'] ), PEAR_LOG_NOTICE );
      $msg .= ' total:'  .number_format(( $timeexec['exit']    - $timeexec['start'] ),  PEAR_LOG_NOTICE ).'sec';
      $this->log->log( $msg, PEAR_LOG_DEBUG );
      $msg  = "'$inputdirFile' (".count($compsinfo).' components) start:'.date( 'H:i:s', $timeexec['start'] );
      $msg .= ' total:'  .number_format(( $timeexec['exit']    - $timeexec['start'] ),  PEAR_LOG_NOTICE ).'sec';
      if( $save )
        $msg .= " -> '$outputdirFile'";
      $msg .= ', size='.strlen( $output );
      $msg .= ', '.count( $rows )." rows, $maxColCount cols";
      $this->log->log( $msg, PEAR_LOG_NOTICE );
    }
            /** save or send the file */
    if( $save ) {
      if( FALSE !== file_put_contents( $outputdirFile, $output )) {
        if( $this->log ) {
          $this->log->log( "INPUT FILE:'$inputdirFile' saved as '$outputdirFile'", PEAR_LOG_NOTICE );
          $this->log->flush();
        }
        return TRUE;
      }
      else {
        if( $this->log ) {
          $this->log->log( "ERROR 15, INPUT FILE:'$inputdirFile' Invalid write to output file : '.$outputdirFile'", PEAR_LOG_ERR );
          $this->log->flush();
        }
        return FALSE;
      }
    }
    if( $this->log ) {
      $this->log->log( "INPUT FILE:'$inputdirFile' redirected as '".$outputFileParts['basename']."'", PEAR_LOG_NOTICE );
      $this->log->flush();
    }
            /** return data, auto gzip */
    $filesize   = strlen( $output );
    if( substr_count( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' )) {
      $output   = gzencode( $output, 9 );
      $filesize = strlen( $output );
      header( 'Content-Encoding: gzip');
      header( 'Vary: *');
    }
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="'.$outputFileParts['basename'].'"' );
    header( 'Cache-Control: max-age=10' );
    header( 'Content-Length: '.$filesize );
    echo $output;
  }
  /**
   * function iCal2xls
   *
   * Convert iCal file to xls format and send file to browser (default) or save xls file to disk
   * Definition iCal  : rcf2445, http://kigkonsult.se/downloads/index.php#rfc
   * Using iCalcreator: http://kigkonsult.se/downloads/index.php#iCalcreator
   * Based on PEAR Spreadsheet_Excel_Writer-0.9.1 (and OLE-1.0.0RC1)
   * to be installed as
   * pear install channel://pear.php.net/OLE-1.0.0RC1
   * pear install channel://pear.php.net/Spreadsheet_Excel_Writer-0.9.1
   *
   * @author Kjell-Inge Gustafsson <ical@kigkonsult.se>
   * @since  3.0 - 2011-12-21
   * @param  object $calendar opt. iCalcreator calendar instance
   * @return bool   returns FALSE when error
   */
  public function iCal2xls( $calendar =FALSE ) {
    $timeexec = array( 'start' => microtime( TRUE ));
    if( $this->log )
      $this->log->log( ' ********** START **********', PEAR_LOG_NOTICE );
            /** check input/output directory and filename */
    $inputdirFile   = $outputdirFile   =  '';
    $inputFileParts = $outputFileParts = array();
    $remoteInput    = $remoteOutput    = FALSE;
    if( $calendar ) {
      $inputdirFile   = $calendar->getConfig( 'DIRFILE' );
      $inputFileParts = pathinfo( $inputdirFile );
      $inputFileParts['dirname'] = realpath( $inputFileParts['dirname'] );
      if( $this->log )
        $this->log->log( 'fileParts:'.var_export( $inputFileParts, TRUE ), PEAR_LOG_DEBUG );
    }
    elseif( FALSE === $this->_fixIO( 'input', 'ics', $inputdirFile, $inputFileParts, $remoteInput )) {
      if( $this->log ) {
        $this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ), 5 ).' sec', PEAR_LOG_ERR );
        $this->log->log( "ERROR 2, invalid input ($inputdirFile)", PEAR_LOG_ERR );
        $this->log->flush();
      }
      return FALSE;
    }
    if( FALSE === $this->_fixIO( 'output', FALSE, $outputdirFile, $outputFileParts, $remoteOutput )) {
      if( FALSE === $this->setConfig( 'outputfilename', $inputFileParts['filename'].'.xls' )) {
        if( $this->log ) {
          $this->log->log( number_format(( microtime( TRUE ) - $timeexec['start'] ), 5 ).' sec', PEAR_LOG_ERR );
          $this->log->log( 'ERROR 3, invalid output ('.$inputFileParts['filename'].'.csv)', PEAR_LOG_ERR );
          $this->log->flush();
        }
        return FALSE;
      }
      $outputdirFile   = $this->getConfig( 'outputdirectory' ).DIRECTORY_SEPARATOR.$inputFileParts['filename'].'.xls';
      $outputFileParts = pathinfo( $outputdirFile );
      if( $this->log )
        $this->log->log( "output set to '$outputdirFile'", PEAR_LOG_INFO );
    }
    if( $this->log ) {
      $this->log->log( "INPUT..FILE:$inputdirFile", PEAR_LOG_NOTICE );
      $this->log->log( "OUTPUT.FILE:$outputdirFile", PEAR_LOG_NOTICE );
    }
    $save = $this->getConfig( 'save' );
    if( $calendar )
      $calnl = $calendar->getConfig( 'nl' );
    else {    /** iCalcreator set config, read and parse input iCal file */
      $calendar = new vcalendar();
      if( FALSE !== ( $unique_id = $this->getConfig( 'unique_id' )))
        $calendar->setConfig( 'unique_id', $unique_id );
      $calnl = $calendar->getConfig( 'nl' );
      if( $remoteInput ) {
        if( FALSE === $calendar->setConfig( 'url', $inputdirFile )) {
          if( $this->log )
            $this->log->log( "ERROR 3 INPUT FILE:'$inputdirFile' iCalcreator: invalid url", 3 );
          return FALSE;
        }
      }
      else {
        if( FALSE === $calendar->setConfig( 'directory', $inputFileParts['dirname'] )) {
          if( $this->log ) {
            $this->log->log( "ERROR 4 INPUT FILE:'$inputdirFile' iCalcreator: invalid directory: '".$inputFileParts['dirname']."'", 3 );
            $this->log->flush();
          }
          return FALSE;
        }
        if( FALSE === $calendar->setConfig( 'filename',  $inputFileParts['basename'] )) {
          if( $this->log ) {
            $this->log->log( "ERROR 5 INPUT FILE:'$inputdirFile' iCalcreator: invalid filename: '".$inputFileParts['basename']."'", 3 );
            $this->log->flush();
          }
          return FALSE;
        }
      }
      if( FALSE === $calendar->parse()) {
        if( $this->log ) {
          $this->log->log( "ERROR 6 INPUT FILE:'$inputdirFile' iCalcreator parse error", 3 );
          $this->log->flush();
        }
        return FALSE;
      }
    } // end if( !$calendar )
    $timeexec['fileOk'] = microtime( TRUE );
    if( !function_exists( 'iCaldate2timestamp' )) {
      function iCaldate2timestamp( $d ) {
        if( 6 > count( $d ))
          return mktime( 0, 0, 0, $d['month'], $d['day'], $d['year'] );
        else
          return mktime( $d['hour'], $d['min'], $d['sec'], $d['month'], $d['day'], $d['year'] );
      }
    }
    if( !function_exists( 'fixiCalString' )) {
      function fixiCalString( $s ) {
        global $calnl;
        $s = str_replace( '\,',          ',',     $s );
        $s = str_replace( '\;',          ';',     $s );
        $s = str_replace( '\n ',         chr(10), $s );
        $s = str_replace( '\\\\',        '\\',    $s );
        $s = str_replace( "$calnl",      chr(10), $s );
        return utf8_decode( $s );
      }
    }
            /** Creating a workbook */
    require_once 'Spreadsheet/Excel/Writer.php';
    if( $save )
      $workbook = new Spreadsheet_Excel_Writer( $outputdirFile );
    else
      $workbook = new Spreadsheet_Excel_Writer();
    $workbook->setVersion(8); // Use Excel97/2000 Format
            /** opt. sending HTTP headers */
    if( !$save )
      $workbook->send( $outputFileParts['basename'] );
            /** Creating a worksheet */
    $worksheet = & $workbook->addWorksheet( $inputFileParts['filename'] );
            /** fix formats */
    $format_bold = & $workbook->addFormat();
    $format_bold->setBold();
    $timeexec['wrkbkOk'] = microtime( TRUE );
            /** info rows */
    $row = -1;
    $worksheet->writeString(   ++$row,  0, 'kigkonsult.se', $format_bold );
    $worksheet->writeString(     $row,  1, ICALCREATOR_VERSION, $format_bold );
    $worksheet->writeString(     $row,  2, ICALCNVVERSION.' iCal2xls', $format_bold );
    $worksheet->writeString(     $row,  3, date( 'Y-m-d H:i:s' ));
    $filename = ( $remoteInput ) ? $inputdirFile : $inputFileParts['basename'];
    $worksheet->writeString(   ++$row,  0, 'iCal input', $format_bold );
    $worksheet->writeString(     $row,  1, $filename );
    $worksheet->writeString(     $row,  2, 'xls output', $format_bold );
    $worksheet->writeString(     $row,  3, $outputFileParts['basename'] );
    if( FALSE !== ( $prop = $calendar->getProperty( 'CALSCALE' ))) {
      $worksheet->writeString( ++$row,  0, 'CALSCALE', $format_bold );
      $worksheet->writeString(   $row,  1, $prop );
    }
    if( FALSE !== ( $prop = $calendar->getProperty( 'METHOD' ))) {
      $worksheet->writeString( ++$row,  0, 'METHOD', $format_bold );
      $worksheet->writeString(   $row,  1, $prop );
    }
    while( FALSE !== ( $xprop = $calendar->getProperty())) {
      $worksheet->writeString( ++$row,  0, $xprop[0], $format_bold );
      $worksheet->writeString(   $row,  1, $xprop[1] );
    }
    $timeexec['infoOk'] = microtime( TRUE );
    if( FALSE === ( $propsToSkip = $this->getConfig( 'skip')))
      $propsToSkip = array();
            /** fix property order list */
    $proporderOrg = array();
    for( $key = 2; $key < 99; $key++ ) {
      if( FALSE !== ( $value = $this->getConfig( $key ))) {
        $proporderOrg[$value] = $key;
        if( $this->log )
          $this->log->log( "$value in column $key", 7 );
      }
    }
            /** fix vtimezone property order list */
    $proporder          = $proporderOrg;
    $proporder['TYPE']  =  0;
    $proporder['ORDER'] =  1;
    $props = array( 'TZID', 'LAST-MODIFIED', 'TZURL', 'DTSTART', 'TZOFFSETTO', 'TZOFFSETFROM'
                  , 'COMMENT', 'RRULE', 'RDATE', 'TZNAME' );
    $pix = 2;
    foreach( $props as $prop ) {
      if( isset( $proporder[$prop] )) continue;
      if( in_array( $prop, $propsToSkip )) {
        if( $this->log )
          $this->log->log( "'$prop' removed from output", 7 );
        continue;
      }
      while( in_array( $pix, $proporder )) $pix++;
      $proporder[$prop] = $pix++;
    }
            /** remove unused properties from and add x-props to property order list */
    $maxpropix = 11;
    if( $maxpropix != ( count( $proporder ) - 1 ))
      $maxpropix = count( $proporder ) - 1;
    $compsinfo = $calendar->getConfig( 'compsinfo');
    $potmp = array();
    $potmp[0]                   =  'TYPE';
    $potmp[1]                   =  'ORDER';
    foreach( $compsinfo as $cix => $compinfo) {
      if( 'vtimezone' != $compinfo['type'] )
        continue;
      $comp = $calendar->getComponent( $compinfo['ordno'] );
      foreach( $compinfo['props'] as $propName => $propcnt ) {
        if( !in_array( $propName, $potmp ) && isset( $proporder[$propName] ))
          $potmp[$proporder[$propName]] = $propName;
        elseif( 'X-PROP' == $propName ) {
          while( $xprop = $comp->getProperty()) {
            if( !in_array( $xprop[0], $potmp )) {
              $maxpropix += 1;
              $potmp[$maxpropix] = $xprop[0];
            } // end if
          } // end while xprop
        } // end X-PROP
      } // end $compinfo['props']
      if( isset( $compinfo['sub'] )) {
        foreach( $compinfo['sub'] as $compinfo2 ) {
          foreach( $compinfo2['props'] as $propName => $propcnt ) {
            if( !in_array( $propName, $potmp ) && isset( $proporder[$propName] ))
              $potmp[$proporder[$propName]] = $propName;
            elseif( 'X-PROP' == $propName ) {
              $scomp = $comp->getComponent( $compinfo2['ordno'] );
              while( $xprop = $scomp->getProperty()) {
                if( !in_array( $xprop[0], $potmp )) {
                  $maxpropix += 1;
                  $potmp[$maxpropix] = $xprop[0];
                } // end if
              } // end while xprop
            } // end X-PROP
          } // end $compinfo['sub']['props']
        } // end foreach( $compinfo['sub']
      } // end if( isset( $compinfo['sub']
    } // end foreach compinfo - vtimezone
    ksort( $potmp, SORT_NUMERIC );
    $proporder = array_flip( array_values( $potmp ));
    if( $this->log )
      $this->log->log( "timezone proporder=".implode(',',array_flip($proporder)), 7 );
            /** create vtimezone info */
    if( 2 < count( $proporder )) {
      $row += 1;
            /** create vtimezone header row */
      foreach( $proporder as $propName => $col ) {
        if( isset( $this->config[$propName] )) {
          $worksheet->writeString( $row,  $col, $this->config[$propName], $format_bold ); // check map of userfriendly name to iCal property name
          if( $this->log )
            $this->log->log( "header row, col=$col: $propName, replaced by ".$this->config[$propName], 7 );
        }
        else
          $worksheet->writeString( $row,  $col, $propName, $format_bold );
      }
      $allowedProps = array( 'VTIMEZONE' => array( 'TZID', 'LAST-MODIFIED', 'TZURL' )
                           , 'STANDARD'  => array( 'DTSTART', 'TZOFFSETTO', 'TZOFFSETFROM', 'COMMENT', 'RDATE', 'RRULE', 'TZNAME' )
                           , 'DAYLIGHT'  => array( 'DTSTART', 'TZOFFSETTO', 'TZOFFSETFROM', 'COMMENT', 'RDATE', 'RRULE', 'TZNAME' ));
            /** create vtimezone data rows */
      foreach( $compsinfo as $cix => $compinfo) {
        if( 'vtimezone' != $compinfo['type'] )
          continue;
        $row += 1;
        $worksheet->writeString(   $row, $proporder['TYPE'],  $compinfo['type'] );
        $worksheet->writeString(   $row, $proporder['ORDER'], $compinfo['ordno'] );
        $comp = $calendar->getComponent( $compinfo['ordno'] );
        foreach( $proporder as $propName => $col ) {
          if(( 'TYPE' == $propName ) || ( 'ORDER' == $propName ))
            continue;
          if( 'X-' == substr( $propName, 0, 2 ))
            continue;
          if( !in_array( $propName, $allowedProps['VTIMEZONE'] )) { // check if component allows property
            if( $this->log )
              $this->log->log( "ERROR 7, INPUT FILE:'$inputdirFile' iCalcreator: unvalid property for component '".$compinfo['type']."': '$propName'", PEAR_LOG_INFO );
            continue;
          }
          if( isset( $compinfo['props'][$propName] )) {
            if( 'LAST-MODIFIED' == $propName )
              $fcn = 'createLastModified';
            else
              $fcn = 'create'.strtoupper( substr( $propName, 0, 1 )).strtolower( substr( $propName, 1 ));
            if( !method_exists ( $comp, $fcn )) {
              if( $this->log )
                $this->log->log( 'ERROR 8 INPUT FILE:"'.$filename.'" iCalcreator: unknown property: "'.$propName.'" ('.$fcn.')', PEAR_LOG_INFO );
              continue;
            }
            $output = str_replace( "$calnl ",     '',      rtrim( $comp->$fcn()));
            $output = str_replace( $propName.';', '',      $output );
            $output = str_replace( $propName.':', '',      $output );
            $worksheet->writeString( $row, $proporder[$propName], fixiCalString( $output ));
          }
        } // end foreach( $proporder
        if( isset( $compinfo['props']['X-PROP'] )) {
          while( $xprop = $comp->getProperty()) {
            $output = str_replace( "$calnl ", '',      rtrim( $xprop[1] ));
            $worksheet->writeString( $row, $proporder[$xprop[0]], fixiCalString( $output ));
          }
        }
        if( isset( $compinfo['sub'] )) {
          foreach( $compinfo['sub'] as $compinfo2 ) {
            $row += 1;
            $worksheet->writeString(   $row, $proporder['TYPE'],  $compinfo2['type'] );
            $worksheet->writeString(   $row, $proporder['ORDER'], $compinfo['ordno'].':'.$compinfo2['ordno'] );
            $scomp = $comp->getComponent( $compinfo2['ordno'] );
            foreach( $proporder as $propName => $col ) {
              if(( 'TYPE' == $propName ) || ( 'ORDER' == $propName ))
                continue;
              if( 'X-' == substr( $propName, 0, 2 ))
                continue;
              if( !in_array( $propName, $allowedProps[strtoupper( $compinfo2['type'] )] )) { // check if component allows property
                if( $this->log )
                  $this->log->log( "ERROR 9, INPUT FILE:'$inputdirFile' iCalcreator: unvalid property for component '".$compinfo2['type']."': '$propName'", PEAR_LOG_INFO );
                continue;
              }
              if( isset( $compinfo2['props'][$propName] )) {
                $fcn = 'create'.strtoupper( substr( $propName, 0, 1 )).strtolower( substr( $propName, 1 ));
                if( !method_exists ( $scomp, $fcn )) {
                  if( $this->log )
                    $this->log->log( 'ERROR 10 INPUT FILE:"'.$filename.'" iCalcreator: unknown property: "'.$propName.'" ('.$fcn.')', PEAR_LOG_INFO );
                  continue;
                }
                $output = str_replace( "$calnl ",     '',      rtrim( $scomp->$fcn()));
                $output = str_replace( $propName.';', '',      $output );
                $output = str_replace( $propName.':', '',      $output );
                $worksheet->writeString( $row, $proporder[$propName], fixiCalString( $output ));
              }
            } // end foreach( $proporder
            if( isset( $compinfo2['props']['X-PROP'] )) {
              while( $xprop = $scomp->getProperty()) {
                $output = str_replace( "$calnl ", '',      rtrim( $xprop[1] ));
                $worksheet->writeString( $row, $proporder[$xprop[0]], fixiCalString( $output ));
              }
            }
          } // end foreach( $compinfo['sub']
        } // end if( isset( $compinfo['sub']['props'] ))
      } // end foreach
    } // end vtimezone
    $timeexec['zoneOk'] = microtime( TRUE );
    $maxColCount = count( $proporder );
            /** fix property order list */
    $proporder          = $proporderOrg;
    $proporder['TYPE']  =  0;
    $proporder['ORDER'] =  1;
    $props = array( 'UID', 'DTSTAMP', 'SUMMARY', 'DTSTART', 'DURATION', 'DTEND', 'DUE', 'RRULE', 'RDATE', 'EXRULE', 'EXDATE'
                  , 'DESCRIPTION', 'CATEGORIES', 'ORGANIZER', 'LOCATION', 'RESOURCES', 'CONTACT', 'URL', 'COMMENT', 'PRIORITY'
                  , 'ATTENDEE', 'CLASS', 'TRANSP', 'SEQUENCE', 'STATUS', 'COMPLETED', 'CREATED', 'LAST-MODIFIED', 'ACTION'
                  , 'TRIGGER', 'REPEAT', 'ATTACH', 'FREEBUSY', 'RELATED-TO', 'REQUEST-STATUS', 'GEO', 'PERCENT-COMPLETE', 'RECURRENCE-ID' );
    $pix = 2;
    foreach( $props as $prop ) {
      if( isset( $proporder[$prop] )) continue;
      if( in_array( $prop, $propsToSkip )) {
        if( $this->log )
          $this->log->log( "'$prop' removed from output", 7 );
        continue;
      }
      while( in_array( $pix, $proporder )) $pix++;
      $proporder[$prop] = $pix++;
    }
            /** remove unused properties from and add x-props to property order list */
    if( $maxpropix < (count( $proporder ) - 1))
      $maxpropix = count( $proporder ) - 1;
    $potmp = array();
    $potmp[0]                   =  'TYPE';
    $potmp[1]                   =  'ORDER';
//  $potmp[2]                   =  'UID';
    foreach( $compsinfo as $cix => $compinfo) {
      if( 'vtimezone' == $compinfo['type'] )
        continue;
      foreach( $compinfo['props'] as $propName => $propcnt ) {
        if( !in_array( $propName, $potmp ) && isset( $proporder[$propName] ))
          $potmp[$proporder[$propName]] = $propName;
        elseif( 'X-PROP' == $propName ) {
          $comp = $calendar->getComponent( $compinfo['ordno'] );
          while( $xprop = $comp->getProperty()) {
            if( !in_array( $xprop[0], $potmp )) {
              $maxpropix += 1;
              $potmp[$maxpropix] = $xprop[0];
            } // end if
          } // while( $xprop
        } // end elseif( 'X-PROP'
      } // end foreach( $compinfo['props']
      if( isset( $compinfo['sub'] )) {
        foreach( $compinfo['sub'] as $compinfo2 ) {
          foreach( $compinfo2['props'] as $propName => $propcnt ) {
            if( !in_array( $propName, $potmp ) && isset( $proporder[$propName] ))
              $potmp[$proporder[$propName]] = $propName;
            elseif( 'X-PROP' == $propName ) {
              $scomp = $comp->getComponent( $compinfo2['ordno'] );
              while( $xprop = $scomp->getProperty()) {
                if( !in_array( $xprop[0], $potmp )) {
                  $maxpropix += 1;
                  $potmp[$maxpropix] = $xprop[0];
                } // end if
              } // end while xprop
            } // end X-PROP
          } // end $compinfo['sub']['props']
        } // end foreach( $compinfo['sub']
      } // end if( isset( $compinfo['sub']
    }
  ksort( $potmp, SORT_NUMERIC );
  $proporder = array_flip( array_values( $potmp ));
  if( $this->log )
    $this->log->log( "comp proporder=".implode(',',array_flip($proporder)), 7 );
  if( $maxColCount < count( $proporder ))
    $maxColCount = count( $proporder );
            /** create header row */
    $row += 1;
    foreach( $proporder as $propName => $col ) {
      if( isset( $this->config[$propName] )) {
        $worksheet->writeString(   $row,  $col, $this->config[$propName], $format_bold ); // check map of userfriendly name to iCal property name
        if( $this->log )
          $this->log->log( "header row, col=$col: $propName, replaced by ".$this->config[$propName], 7 );
      }
      else
        $worksheet->writeString(   $row,  $col, $propName, $format_bold );
    }
    $allowedProps = array( 'VEVENT'    => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'CONTACT', 'CREATED', 'DESCRIPTION', 'DTEND'
                                               , 'DTSTAMP', 'DTSTART', 'DURATION', 'EXDATE', 'RXRULE', 'GEO', 'LAST-MODIFIED', 'LOCATION', 'ORGANIZER'
                                               , 'PRIORITY', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO', 'RESOURCES', 'RRULE', 'REQUEST-STATUS', 'SEQUENCE'
                                               , 'STATUS', 'SUMMARY', 'TRANSP', 'UID', 'URL', )
                         , 'VTODO'     => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'COMPLETED', 'CONTACT', 'CREATED', 'DESCRIPTION'
                                               , 'DTSTAMP', 'DTSTART', 'DUE', 'DURATION', 'EXDATE', 'EXRULE', 'GEO', 'LAST-MODIFIED', 'LOCATION', 'ORGANIZER'
                                               , 'PERCENT', 'PRIORITY', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO', 'RESOURCES', 'RRULE', 'REQUEST-STATUS'
                                               , 'SEQUENCE', 'STATUS', 'SUMMARY', 'UID', 'URL' )
                         , 'VJOURNAL'  => array( 'ATTACH', 'ATTENDEE', 'CATEGORIES', 'CLASS', 'COMMENT', 'CONTACT', 'CREATED', 'DESCRIPTION', 'DTSTAMP'
                                               , 'DTSTART', 'EXDATE', 'EXRULE', 'LAST-MODIFIED', 'ORGANIZER', 'RDATE', 'RECURRENCE-ID', 'RELATED-TO'
                                               , 'RRULE', 'REQUEST-STATUS', 'SEQUENCE', 'STATUS', 'SUMMARY', 'UID', 'URL' )
                         , 'VFREEBUSY' => array( 'ATTENDEE', 'COMMENT', 'CONTACT', 'DTEND', 'DTSTAMP', 'DTSTART', 'DURATION', 'FREEBUSY', 'ORGANIZER', 'UID', 'URL' )
                         , 'VALARM'    => array( 'ACTION', 'ATTACH', 'ATTENDEE', 'DESCRIPTION', 'DURATION', 'REPEAT', 'SUMMARY', 'TRIGGER' ));
            /** create data rows */
    foreach( $compsinfo as $cix => $compinfo) {
      if( 'vtimezone' == $compinfo['type'] )
        continue;
      $row += 1;
      $worksheet->writeString(   $row, $proporder['TYPE'],  $compinfo['type'] );
      $worksheet->writeString(   $row, $proporder['ORDER'], $compinfo['ordno'] );
//    $worksheet->write(         $row, $proporder['UID'],   $compinfo['uid'] );
      $comp = $calendar->getComponent( $compinfo['ordno'] );
      foreach( $proporder as $propName => $col ) {
        if(( 'TYPE' == $propName ) || ( 'ORDER' == $propName ))
          continue;
        if( 'X-' == substr( $propName, 0, 2 ))
          continue;
        if( !in_array( $propName, $allowedProps[strtoupper( $compinfo['type'] )] )) { // check if component allows property
          if( $this->log )
            $this->log->log( "ERROR 11, INPUT FILE:'$inputdirFile' iCalcreator: unvalid property for component '".$compinfo['type']."': '$propName'", PEAR_LOG_INFO );
          continue;
        }
        if( isset( $compinfo['props'][$propName] )) {
          switch( $propName ) {
            case 'LAST-MODIFIED' ;
              $fcn = 'createLastModified';
              break;
            case 'RECURRENCE-ID':
              $fcn = 'createRecurrenceid';
              break;
            case 'RELATED-TO':
              $fcn = 'createRelatedTo';
              break;
            case 'REQUEST-STATUS':
              $fcn = 'createRequestStatus';
              break;
            case 'PERCENT-COMPLETE':
              $fcn = 'createPercentComplete';
              break;
           default:
            $fcn = 'create'.strtoupper( substr( $propName, 0, 1 )).strtolower( substr( $propName, 1 ));
          }
          if( !method_exists ( $comp, $fcn )) {
            if( $this->log )
              $this->log->log( "ERROR 12 INPUT FILE:'$filename' iCalcreator: unknown property: '$propName' ($fcn)", PEAR_LOG_INFO );
            continue;
          }
          $output = str_replace( "$calnl ",     '',      rtrim( $comp->$fcn()));
          $output = str_replace( $propName.';', '',      $output );
          $output = str_replace( $propName.':', '',      $output );
          $worksheet->writeString( $row, $proporder[$propName], fixiCalString( $output ));
        }
      } // end foreach( $proporder
      if( isset( $compinfo['props']['X-PROP'] )) {
        while( $xprop = $comp->getProperty()) {
          $output = str_replace( "$calnl ", '',      rtrim( $xprop[1] ));
          $worksheet->writeString( $row, $proporder[$xprop[0]], fixiCalString( $output ));
        }
      }
      if( isset( $compinfo['sub'] )) {
        foreach( $compinfo['sub'] as $compinfo2 ) {
          $row += 1;
          $worksheet->writeString(   $row, $proporder['TYPE'],  $compinfo2['type'] );
          $worksheet->writeString(   $row, $proporder['ORDER'], $compinfo['ordno'].':'.$compinfo2['ordno'] );
          $scomp = $comp->getComponent( $compinfo2['ordno'] );
          foreach( $proporder as $propName => $col ) {
            if(( 'TYPE' == $propName ) || ( 'ORDER' == $propName ))
              continue;
            if( 'X-' == substr( $propName, 0, 2 ))
              continue;
            if( !in_array( $propName, $allowedProps[strtoupper( $compinfo2['type'] )] )) { // check if component allows property
              if( $this->log )
                $this->log->log( "ERROR 13, INPUT FILE:'$inputdirFile' iCalcreator: unvalid property for component '".$compinfo2['type']."': '$propName'", PEAR_LOG_INFO );
              continue;
            }
            if( isset( $compinfo2['props'][$propName] )) {
              $fcn = 'create'.strtoupper( substr( $propName, 0, 1 )).strtolower( substr( $propName, 1 ));
              if( !method_exists ( $scomp, $fcn )) {
                if( $this->log )
                  $this->log->log( "ERROR 14 INPUT FILE:'$filename' iCalcreator: unknown property: '$propName' ($fcn)", PEAR_LOG_INFO );
                continue;
              }
              $output = str_replace( "$calnl ",     '',      rtrim( $scomp->$fcn()));
              $output = str_replace( $propName.';', '',      $output );
              $output = str_replace( $propName.':', '',      $output );
              $worksheet->writeString( $row, $proporder[$propName], fixiCalString( $output ));
            } // end if( isset( $compinfo2['props'][$propName]
          } // end foreach( $proporder
          if( isset( $compinfo2['props']['X-PROP'] )) {
            while( $xprop = $scomp->getProperty()) {
              $output = str_replace( "$calnl ", '',      rtrim( $xprop[1] ));
              $output = str_replace( '\\n ',    chr(10), $output );
              $worksheet->writeString( $row, $proporder[$xprop[0]], fixiCalString( $output ));
            }
          } // end if( isset( $compinfo2['props']['X-PROP']
        } // end foreach( $compinfo['sub']
      } // end if( isset( $compinfo['sub']
    } // foreach( $compsinfo as
    if( $this->log ) {
      $timeexec['exit'] = microtime( TRUE );
      $msg  = "'$filename'";
      $msg .= ' fileOk:' .number_format(( $timeexec['fileOk']  - $timeexec['start'] ),   5 );
      $msg .= ' wrkbkOk:'.number_format(( $timeexec['wrkbkOk'] - $timeexec['fileOk'] ),  5 );
      $msg .= ' infoOk:' .number_format(( $timeexec['infoOk']  - $timeexec['wrkbkOk'] ), 5 );
      $msg .= ' zoneOk:' .number_format(( $timeexec['zoneOk']  - $timeexec['infoOk'] ),  5 );
      $msg .= ' compOk:' .number_format(( $timeexec['exit']    - $timeexec['zoneOk'] ),  5 );
      $msg .= ' total:'  .number_format(( $timeexec['exit']    - $timeexec['start'] ),   5 ).'sec';
      $msg .= ', '.($row+1)." rows, $maxColCount cols";
      $this->log->log( $msg, PEAR_LOG_DEBUG );
      $msg  = "'$filename' (".count($compsinfo).' components) start:'.date( 'H:i:s', $timeexec['start'] );
      $msg .= ' total:'  .number_format(( $timeexec['exit']    - $timeexec['start'] ),   5 ).'sec';
      if( $save )
        $msg .= " saved as '$outputdirFile'";
      else
        $msg .= " redirected as '".$outputFileParts['basename']."'";
      $this->log->log( $msg, PEAR_LOG_NOTICE );
    }
            /** Close and, opt., send the file */
    if( $this->log )
      $this->log->flush();
    $workbook->close();
    return TRUE;
  }
}
?>