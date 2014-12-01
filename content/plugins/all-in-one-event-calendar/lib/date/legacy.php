<?php

/**
 * Legacy Time utility.
 *
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Date
 */
class Ai1ec_Time_Utility {

	/**
	 * @var Ai1ec_Registry_Object
	 */
	static protected $_registry;

	/**
	 * @param Ai1ec_Registry_Object $registry
	 */
	static public function set_registry( Ai1ec_Registry_Object $registry ) {
		self::$_registry = $registry;
	}

	/**
	 * Legacy function needed for theme compatibility
	 * 
	 * @param string $format
	 * @param int    $timestamp
	 * @param bool   $is_gmt
	 */
	static public function date_i18n(
		$format,
		$timestamp = false,
		$is_gmt    = true
	) {
		$timezone = ( $is_gmt ) ? 'UTC' : 'sys.default';
		return self::$_registry->get( 'date.time', $timestamp, $timezone )
			->format_i18n( $format );
	}

}