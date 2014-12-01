<?php

/**
 * Wrap library calls to date subsystem.
 *
 * Meant to increase performance and work around known bugs in environment.
 *
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Date
 */
class Ai1ec_Date_System extends Ai1ec_Base {

	/**
	 * @var array List of local time (key '0') and GMT time (key '1').
	 */
	protected $_current_time = array();

	/**
	 * @var Ai1ec_Cache_Memory
	 */
	protected $_gmtdates;

	/**
	 * Initiate current time list.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$gmt_time = ( version_compare( PHP_VERSION, '5.1.0' ) >= 0 )
			? time()
			: gmmktime();
		$this->_current_time     = array(
			(int)$_SERVER['REQUEST_TIME'],
			$gmt_time,
		);
		$this->_gmtdates = $registry->get( 'cache.memory' );
	}

	/**
	 * Get current time UNIX timestamp.
	 *
	 * Uses in-memory value, instead of re-calling `time()` / `gmmktime()`.
	 *
	 * @param bool $is_gmt Set to true to get GMT timestamp.
	 *
	 * @return int Current time UNIX timestamp
	 */
	public function current_time( $is_gmt = false ) {
		return $this->_current_time[(int)( (bool)$is_gmt )];
	}

	/**
	 * Returns the associative array of date patterns supported by the plugin.
	 *
	 * Currently the formats are:
	 *   array(
	 *     'def' => 'd/m/yyyy',
	 *     'us'  => 'm/d/yyyy',
	 *     'iso' => 'yyyy-m-d',
	 *     'dot' => 'm.d.yyyy',
	 *   );
	 *
	 * 'd' or 'dd' represent the day, 'm' or 'mm' represent the month, and 'yy'
	 * or 'yyyy' represent the year.
	 *
	 * @return array List of supported date patterns.
	 */
	public function get_date_patterns() {
		return array(
			'def' => 'd/m/yyyy',
			'us'  => 'm/d/yyyy',
			'iso' => 'yyyy-m-d',
			'dot' => 'm.d.yyyy',
		);
	}

	/**
	 * Get acceptable date format.
	 *
	 * Returns the date pattern (in the form 'd-m-yyyy', for example) associated
	 * with the provided key, used by plugin settings. Simply a static map as
	 * follows:
	 *
	 * @param string $key Key for the date format.
	 *
	 * @return string Associated date format pattern.
	 */
	public function get_date_pattern_by_key( $key = 'def' ) {
		$patterns = $this->get_date_patterns();
		if ( ! isset( $patterns[$key] ) ) {
			return (string)current( $patterns );
		}
		return $patterns[$key];
	}

	/**
	 * Format timestamp into URL safe, user selected representation.
	 *
	 * Returns a formatted date given a timestamp, based on the given date
	 * format, with any '/' characters replaced with URL-friendly '-'
	 * characters.
	 *
	 * @see Ai1ec_Date_System::get_date_patterns() for supported date formats.
	 *
	 * @param int    $timestamp UNIX timestamp representing a date.
	 * @param string $pattern   Key of date pattern (@see
	 *                          self::get_date_format_patter()) to
	 *                          format date with
	 *
	 * @return string Formatted date string.
	 */
	public function format_date_for_url( $timestamp, $pattern = 'def' ) {
		$date = $this->format_date( $timestamp, $pattern );
		$date = str_replace( '/', '-', $date );
		return $date;
	}

	/**
	 * Similar to {@see format_date_for_url} just using new DateTime interface.
	 *
	 * @param Ai1ec_Date_Time $datetime Instance of datetime to format.
	 * @param string          $pattern  Target format to use.
	 *
	 * @return string Formatted datetime string.
	 */
	public function format_datetime_for_url(
		Ai1ec_Date_Time $datetime,
		$pattern = 'def'
	) {
		$date = $datetime->format( $this->get_date_format_patter( $pattern ) );
		return str_replace( '/', '-', $date );
		return $date;
	}

	/**
	 * Returns a formatted date given a timestamp, based on the given date format.
	 *
	 * @see  self::get_date_patterns() for supported date formats.
	 *
	 * @param  int $timestamp    UNIX timestamp representing a date (in GMT)
	 * @param  string $pattern   Key of date pattern (@see
	 *                           self::get_date_format_patter()) to
	 *                           format date with
	 * @return string            Formatted date string
	 */
	public function format_date( $timestamp, $pattern = 'def' ) {
		return gmdate( $this->get_date_format_patter( $pattern ), $timestamp );
	}

	public function get_date_format_patter( $requested ) {
		$pattern = $this->get_date_pattern_by_key( $requested );
		$pattern = str_replace(
			array( 'dd', 'd', 'mm', 'm', 'yyyy', 'yy' ),
			array( 'd',  'j', 'm',  'n', 'Y',    'y' ),
			$pattern
		);
		return $pattern;
	}

	/**
	 * Returns human-readable version of the GMT offset.
	 *
	 * @param string $timezone_name Olsen Timezone name [optional=null]
	 *
	 * @return string GMT offset expression
	 */
	public function get_gmt_offset_expr( $timezone_name = null ) {
		$timezone = $this->get_gmt_offset( $timezone_name );
		$offset_h = (int)( $timezone / 60 );
		$offset_m = absint( $timezone - $offset_h * 60 );
		$timezone = sprintf(
			Ai1ec_I18n::__( 'GMT%+d:%02d' ),
			$offset_h,
			$offset_m
		);

		return $timezone;
	}

	/**
	 * Get current GMT offset in seconds.
	 *
	 * @param string $timezone_name Olsen Timezone name [optional=null]
	 *
	 * @return int Offset from GMT in seconds.
	 */
	public function get_gmt_offset( $timezone_name = null ) {
		if ( null === $timezone_name ) {
			$timezone_name = 'sys.default';
		}
		$current = $this->_registry->get(
			'date.time',
			'now',
			$timezone_name
		);
		return $current->get_gmt_offset();
	}

	/**
	 * gmgetdate method
	 *
	 * Get date/time information in GMT
	 *
	 * @param int $timestamp Timestamp at which information shall be evaluated
	 *
	 * @return array Associative array of information related to the timestamp
	 */
	public function gmgetdate( $timestamp = NULL ) {
		if ( NULL === $timestamp ) {
			$timestamp = (int)$_SERVER['REQUEST_TIME'];
		}
		if ( NULL === ( $date = $this->_gmtdates->get( $timestamp ) ) ) {
			$particles = explode(
				',',
				gmdate( 's,i,G,j,w,n,Y,z,l,F,U', $timestamp )
			);
			$date      = array_combine(
				array(
					'seconds',
					'minutes',
					'hours',
					'mday',
					'wday',
					'mon',
					'year',
					'yday',
					'weekday',
					'month',
					0
				),
				$particles
			);
			$this->_gmtdates->set( $timestamp, $date );
		}
		return $date;
	}
}