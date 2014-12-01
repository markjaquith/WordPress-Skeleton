<?php

/**
 * Time entity.
 *
 * @instantiator new
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Date
 */
class Ai1ec_Date_Time {

	/**
	 * @var Ai1ec_Registry_Object Instance of objects registry.
	 */
	protected $_registry  = null;

	/**
	 * @var DateTime Instance of date time object used to perform manipulations.
	 */
	protected $_date_time = null;

	/**
	 * @var string Olsen name of preferred timezone to use if none is requested.
	 */
	protected $_preferred_timezone = null;

	/**
	 * @var bool Set to true when `no value` is set.
	 */
	protected $_is_empty = false;

	/**
	 * Initialize local date entity.
	 *
	 * @param Ai1ec_Registry_Object $registry Objects registry instance.
	 * @param string                $time     For details {@see self::format}.
	 * @param string                $timezone For details {@see self::format}.
	 *
	 * @return void
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		$time     = 'now',
		$timezone = 'UTC'
	) {
		$this->_registry = $registry;
		$this->set_date_time( $time, $timezone );
	}

	/**
	 * Since clone is shallow, we need to clone the DateTime object
	 */
	public function __clone() {
		$this->_date_time = clone $this->_date_time;
	}

	/**
	 * Return formatted date in desired timezone.
	 *
	 * NOTICE: consider optimizing by storing multiple copies of `DateTime` for
	 * each requested timezone, or some of them, as of now timezone is changed
	 * back and forth every time when formatting is called for.
	 *
	 * @param string $format   Desired format as accepted by {@see date}.
	 * @param string $timezone Valid timezone identifier. Defaults to current.
	 *
	 * @return string Formatted date time.
	 *
	 * @throws Ai1ec_Date_Timezone_Exception If timezone is not recognized.
	 */
	public function format( $format = 'U', $timezone = null ) {
		if ( $this->_is_empty ) {
			return null;
		}
		if ( 'U' === $format ) { // performance cut
			return $this->_date_time->format( 'U' );
		}
		$timezone  = $this->get_default_format_timezone( $timezone );
		$last_tz   = $this->get_timezone();
		$this->set_timezone( $timezone );
		$formatted = $this->_date_time->format( $format );
		$this->set_timezone( $last_tz );
		return $formatted;
	}

	/**
	 * Format date time to i18n representation.
	 *
	 * @param string $format   Target I18n format.
	 * @param string $timezone Valid timezone identifier. Defaults to current.
	 *
	 * @return string Formatted time.
	 */
	public function format_i18n( $format, $timezone = null ) {
		$parser    = $this->_registry->get( 'parser.date' );
		$parsed    = $parser->get_format( $format );
		$inflected = $this->format( $parsed, $timezone );
		$formatted = $parser->squeeze( $inflected );
		return $formatted;
	}

	/**
	 * Commodity method to format to UTC.
	 *
	 * @param string $format Target format, defaults to UNIX timestamp.
	 *
	 * @return string Formatted datetime string.
	 */
	public function format_to_gmt( $format = 'U' ) {
		return $this->format( $format, 'UTC' );
	}

	/**
	 * Create JavaScript ready date/time information string.
	 *
	 * @param bool $event_timezone Set to true to format in event timezone.
	 *
	 * @return string JavaScript date/time string.
	 */
	public function format_to_javascript( $event_timezone = false ) {
		$event_timezone = ( $event_timezone )
			? $this->get_timezone()
			: null;
		return $this->format( 'Y-m-d\TH:i:s', $event_timezone );
	}

	/**
	 * Get timezone to use when format doesn't have one.
	 *
	 * Precedence:
	 *     1. Timezone supplied for formatting;
	 *     2. Objects preferred timezone;
	 *     3. Default systems timezone.
	 *
	 * @var string $timezone Requested formatting timezone.
	 *
	 * @return string Olsen timezone name to use.
	 */
	public function get_default_format_timezone( $timezone = null ) {
		if ( null !== $timezone ) {
			return $timezone;
		}
		if ( null !== $this->_preferred_timezone ) {
			return $this->_preferred_timezone;
		}
		return $this->_registry->get( 'date.timezone' )
			->get_default_timezone();
	}

	/**
	 * Offset from GMT in minutes.
	 *
	 * @return int Signed integer - offset.
	 */
	public function get_gmt_offset() {
		return $this->_date_time->getOffset() / 60;
	}

	/**
	 * Returns timezone offset as human readable GMT string.
	 *
	 * @return string
	 */
	public function get_gmt_offset_as_text() {
		$offset = $this->_date_time->getOffset() / 3600;
		return 'GMT' . ( $offset > 0 ? '+' : '' ) . $offset;
	}

	/**
	 * Set preferred timezone to use when format is called without any.
	 *
	 * @param DateTimeZone $timezone Preferred timezone instance.
	 *
	 * @return Ai1ec_Date_Time Instance of self for chaining.
	 */
	public function set_preferred_timezone( $timezone ) {
		if ( $timezone instanceof DateTimeZone ) {
			$timezone = $timezone->getName();
		}
		$this->_preferred_timezone = (string)$timezone;
		return $this;
	}

	/**
	 * Change timezone of stored entity.
	 *
	 * @param string $timezone Valid timezone identifier.
	 *
	 * @return Ai1ec_Date Instance of self for chaining.
	 *
	 * @throws Ai1ec_Date_Timezone_Exception If timezone is not recognized.
	 */
	public function set_timezone( $timezone = 'UTC' ) {
		$date_time_tz = ( $timezone instanceof DateTimeZone )
			? $timezone
			: $this->_registry->get( 'date.timezone' )->get( $timezone );
		$this->_date_time->setTimezone( $date_time_tz );
		return $this;
	}

	/**
	 * Get timezone associated with current object.
	 *
	 * @return string|null Valid PHP timezone string or null on error.
	 */
	public function get_timezone() {
		$timezone = $this->_date_time->getTimezone();
		if ( false === $timezone ) {
			return null;
		}
		return $timezone->getName();
	}

	/**
	 * Get difference in seconds between to dates.
	 *
	 * In PHP versions post 5.3.0 the {@see DateTimeImmutable::diff()} is
	 * used. In earlier versions the difference between two timestamps is
	 * being checked.
	 *
	 * @param Ai1ec_Date_Time $comparable Other date time entity.
	 *
	 * @return int Number of seconds between two dates.
	 */
	public function diff_sec( Ai1ec_Date_Time $comparable, $timezone = null ) {
		// NOTICE: `$this->_is_empty` is not touched here intentionally
		// because there is no meaningful difference to `empty` value.
		// It is left to be handled at upper level - you are not likely to
		// reach situation where you compare something against empty value.
		if ( version_compare( PHP_VERSION, '5.3.0' ) < 0 ) {
			$difference = $this->_date_time->format( 'U' ) -
				$comparable->_date_time->format( 'U' );
			if ( $difference < 0 ) {
				$difference *= -1;
			}
			return $difference;
		}
		$difference = $this->_date_time->diff( $comparable->_date_time, true );
		return (
			$difference->days * 86400 +
			$difference->h    * 3600  +
			$difference->i    * 60    +
			$difference->s
		);
	}

	/**
	 * Adjust only date fragment of entity.
	 *
	 * @param int $year  Year of the date.
	 * @param int $month Month of the date.
	 * @param int $day   Day of the date.
	 *
	 * @return Ai1ec_Date_Time Instance of self for chaining.
	 */
	public function set_date( $year, $month, $day ) {
		$this->_date_time->setDate( $year, $month, $day );
		$this->_is_empty = false;
		return $this;
	}

	/**
	 * Adjust only time fragment of entity.
	 *
	 * @param int $hour   Hour of the time.
	 * @param int $minute Minute of the time.
	 * @param int $second Second of the time.
	 *
	 * @return Ai1ec_Date_Time Instance of self for chaining.
	 */
	public function set_time( $hour, $minute = 0, $second = 0 ) {
		$this->_date_time->setTime( $hour, $minute, $second );
		$this->_is_empty = false;
		return $this;
	}

	/**
	 * Adjust day part of date time entity.
	 *
	 * @param int $quantifier Day adjustment quantifier.
	 *
	 * @return Ai1ec_Date_Time Instance of self for chaining.
	 */
	public function adjust_day( $quantifier ) {
		// NOTICE: `$this->_is_empty` is not touched here, because if you
		// start adjusting value it's likely not empty by then.
		$this->adjust( $quantifier, 'day' );
		return $this;
	}

	/**
	 * Adjust day part of date time entity.
	 *
	 * @param int $quantifier Day adjustment quantifier.
	 *
	 * @return Ai1ec_Date_Time Instance of self for chaining.
	 */
	public function adjust_month( $quantifier ) {
		$this->adjust( $quantifier, 'month' );
		return $this;
	}

	/**
	 * Change/initiate stored date time entity.
	 *
	 * NOTICE: time specifiers falling in range 0..2048 will be treated
	 * as a UNIX timestamp, to full format specification, thus ignoring
	 * any value passed for timezone.
	 *
	 * @param string $time     Valid (PHP-parseable) date/time identifier.
	 * @param string $timezone Valid timezone identifier.
	 *
	 * @return Ai1ec_Date Instance of self for chaining.
	 */
	public function set_date_time( $time = 'now', $timezone = 'UTC' ) {
		if ( $time instanceof self ) {
			$this->_is_empty           = $time->_is_empty;
			$this->_date_time          = clone $time->_date_time;
			$this->_preferred_timezone = $time->_preferred_timezone;
			if ( 'UTC' !== $timezone && $timezone ) {
				$this->set_timezone( $timezone );
			}
			return $this;
		}
		$this->assert_utc_timezone();
		$date_time_tz = $this->_registry->get( 'date.timezone' )
				->get( $timezone );
		$reset_tz     = false;
		$this->_is_empty = false;
		if ( null === $time ) {
			$this->_is_empty = true;
			$time            = '@' . ~PHP_INT_MAX;
			$reset_tz        = true;
		} else if ( $this->is_timestamp( $time ) ) {
			$time     = '@' . $time; // treat as UNIX timestamp
			$reset_tz = true; // store intended TZ
		}
		// PHP <= 5.3.5 compatible
		$this->_date_time = new DateTime( $time, $date_time_tz );
		if ( $reset_tz ) {
			$this->set_timezone( $date_time_tz );
		}
		return $this;
	}

	/**
	 * Check if value should be treated as a UNIX timestamp.
	 *
	 * @param string $time Provided time value.
	 *
	 * @return bool True if seems like UNIX timestamp.
	 */
	public function is_timestamp( $time ) {
		// '20001231T001559Z'
		if ( isset( $time{8} ) && 'T' === $time{8} ) {
			return false;
		}
		if ( (string)(int)$time !== (string)$time ) {
			return false;
		}
		// 1000..2459 are treated as hours, 2460..9999 - as years
		if ( $time > 999 && $time < 2460 ) {
			return false;
		}
		return true;

	}

	/**
	 * Assert that current timezone is UTC.
	 *
	 * @return bool Success.
	 */
	public function assert_utc_timezone() {
		$default = (string)date_default_timezone_get();
		$success = true;
		if ( 'UTC' !== $default ) {
			// issue admin notice
			$success = date_default_timezone_set( 'UTC' );
		}
		return $success;
	}

	/**
	 * Magic method for compatibility.
	 *
	 * @return string ISO-8601 formatted date-time.
	 */
	public function __toString() {
		return $this->format( 'c' );
	}

	/**
	 * Modifies the DateTime object
	 *
	 * @param int $quantifieruantifier
	 * @param string $longname
	 */
	public function adjust( $quantifier, $longname ) {
		$quantifier = (int)$quantifier;
		if ( $quantifier > 0 && '+' !== $quantifier{0} ) {
			$quantifier = '+' . $quantifier;
		}
		$modifier = $quantifier . ' ' . $longname;
		$this->_date_time->modify( $modifier );
		return $this;
	}

	/**
	 * Explicitly check if value (date) is empty.
	 *
	 * @return bool Emptiness
	 */
	public function is_empty() {
		return $this->_is_empty;
	}

}