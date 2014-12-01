<?php

/**
 * Library function for massive time conversion operations.
 *
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Date
 */
class Ai1ec_Date_Converter {

	/**
	 * @var Ai1ec_Registry_Object Instance of objects registry.
	 */
	protected $_registry = null;

	/**
	 * Get reference of object registry.
	 *
	 * @param Ai1ec_Registry_Object $registry Injected objects registry.
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
	}

	/**
	 * Change timezone of times provided.
	 *
	 * @param array  $input     List of time entries to convert.
	 * @param string $source_tz Timezone to convert from.
	 * @param string $target_tz Timezone to convert to.
	 * @param string $format    Format of target time entries.
	 *
	 * @return array List of converted times.
	 */
	public function change_timezone(
		array $input,
		$source_tz,
		$target_tz = 'UTC',
		$format    = 'U'
	) {
		$output = array();
		foreach ( $input as $time ) {
			try {
				$time_object = $this->_registry->get(
					'date.time',
					$input,
					$source_tz
				);
				$output[] = $time_object->format( $format, $target_tz );
				unset( $time_object );
			} catch ( Ai1ec_Date_Exception $exception ) {
				// ignore
			}
		}
		return $output;
	}

}