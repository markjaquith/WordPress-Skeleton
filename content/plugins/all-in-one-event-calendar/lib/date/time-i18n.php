<?php

/**
 * Time and date internationalization management library
 *
 * @author     Timely Network Inc
 * @since      2012.10.09
 *
 * @package    AllInOneCalendar
 * @subpackage AllInOneCalendar.Lib.Utility
 */
class Ai1ec_Time_I18n_Utility extends Ai1ec_Base {

	/**
	 * @var char Separator to wrap unique keys and avoid collisions
	 *           EOT is used instead of NUL, as NUL is used by `date()`
	 *           functions family as guard and causes memory leaks.
	 */
	protected $_separator = "\004";

	/**
	 * @var array Map of keys, used by date methods
	 */
	protected $_keys      = array();

	/**
	 * @var array Map of keys for substition
	 */
	protected $_skeys     = array();

	/**
	 * @var string Format to use when calling `date_i18n()`
	 */
	protected $_format    = NULL;

	/**
	 * @var Ai1ec_Memory_Utility Parsed time entries
	 */
	protected $_memory    = NULL;

	/**
	 * @var Ai1ec_Memory_Utility Parsed format entries
	 */
	protected $_transf    = NULL;

	/**
	 * Constructor
	 *
	 * Initialize internal memory objects and date keys.
	 *
	 * @param Ai1ec_Memory_Utility $memory Optionally inject memory to use
	 *
	 * @return void Constructor does not return
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		Ai1ec_Cache_Memory $memory = null
	) {
		parent::__construct( $registry );
		if ( NULL === $memory ) {
			$memory = $this->_registry->get( 'cache.memory', 120 ); // 30 * 4
		}
		$this->_memory = $memory;
		$this->_transf = $this->_registry->get( 'cache.memory' );
		$this->_keys   = $this->_initialize_keys();
		$this->_skeys  = $this->_initialize_keys(
			$this->_separator,
			$this->_separator
		);
		$this->_format = implode( $this->_separator, $this->_keys );
	}

	/**
	 * format method
	 *
	 * Convenient wrapper for `date_i18n()`, which caches both faster format
	 * version and response for {$timestamp} and {$is_gmt} combination.
	 *
	 * @param string $format    Format string to output timestamp in
	 * @param int    $timestamp UNIX timestamp to output in given format
	 * @param bool   $is_gmt    Set to true, to treat {$timestamp} as GMT
	 *
	 * @return string Formatted date-time entry
	 */
	public function format( $format, $timestamp = false, $is_gmt = false ) {
		$time_elements = $this->parse( $timestamp, $is_gmt );
		$local_format  = $this->_safe_format( $format );
		return str_replace( $this->_skeys, $time_elements, $local_format );
	}

	/**
	 * parse method
	 *
	 * Parse given timestamp into I18n date/time values map.
	 *
	 * @param int  $timestamp Timestamp to parse
	 * @param bool $is_gmt    Set to true, to treat value as present in GMT
	 *
	 * @return array Map of date format keys and corresponding time values
	 */
	public function parse( $timestamp = false, $is_gmt = false ) {
		$timestamp = (int)$timestamp;
		if ( $timestamp <= 0 ) {
			$timestamp = $this->_registry->get( 'date.system' )->current_time();
		}
		$cache_key = $timestamp . "\0" . $is_gmt;
		if ( NULL === ( $record = $this->_memory->get( $cache_key ) ) ) {
			$record = array_combine(
				$this->_keys,
				explode(
					$this->_separator,
					date_i18n( $this->_format, $timestamp, $is_gmt )
				)
			);
			$this->_memory->set( $cache_key, $record );
		}
		return $record;
	}

	/**
	 * _safe_format method
	 *
	 * Prepare safe format value, to use in substitutions.
	 * In prepared string special values are wrapped by {$_separator} to allow
	 * fast replacement methods, using binary search.
	 *
	 * @param string $format Given format to polish
	 *
	 * @return string Modified format, with special keys wrapped in bin fields
	 */
	protected function _safe_format( $format ) {
		if ( NULL === ( $safe = $this->_transf->get( $format ) ) ) {
			$safe      = '';
			$state     = 0;
			$separator = $this->_separator;
			$length    = strlen( $format );
			for ( $index = 0; $index < $length; $index++ ) {
				if ( $state > 0 ) {
					--$state;
				}
				$current = $format{$index};
				if ( 0 === $state ) {
					if ( '\\' === $current ) {
						$state = 2;
					} elseif ( isset( $this->_keys[$current] ) ) {
						$current = $separator . $current . $separator;
					}
				}
				if ( 2 !== $state ) {
					$safe .= $current;
				}
			}
			$this->_transf->set( $format, $safe );
		}
		return $safe;
	}

	/**
	 * _initialize_keys method
	 *
	 * Prepare list of keys, used by date functions.
	 * Optionally wrap values (keys are the same, always).
	 *
	 * @param string $prepend Prefix to date key
	 * @param string $append  Suffix to date key
	 *
	 * @return array Map of date keys
	 */
	protected function _initialize_keys( $prepend = '', $append = '' ) {
		$keys = array(
			'd',
			'D',
			'j',
			'l',
			'N',
			'S',
			'w',
			'z',
			'W',
			'F',
			'm',
			'M',
			'n',
			't',
			'L',
			'o',
			'Y',
			'y',
			'a',
			'A',
			'B',
			'g',
			'G',
			'h',
			'H',
			'i',
			's',
			'u',
			'e',
			'I',
			'O',
			'P',
			'T',
			'Z',
			'c',
			'r',
			'U',
		);
		$map = array();
		foreach ( $keys as $key ) {
			$map[$key] = $prepend . $key . $append;
		}
		return $map;
	}

}
