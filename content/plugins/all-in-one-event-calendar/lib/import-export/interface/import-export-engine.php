<?php

/**
 * The basic import/export interface.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Import-export.Interface
 */
interface Ai1ec_Import_Export_Engine {

	/**
	 * This methods allow for importing of events.
	 *
	 * @param array $arguments An array of arguments needed for parsing.
	 *
	 * @throws Ai1ec_Parse_Exception When the data passed is not parsable
	 *
	 * @return int The number of imported events.
	 */
	public function import( array $arguments );

	/**
	 * This methods allow exporting events.
	 *
	 * @param array $arguments An array of arguments needed for exporting.
	 * @param array @params    An array of export parameters.
	 *
	 * @return void It doesn't return anything.
	 */
	public function export( array $arguments, array $params = array() );
}