<?php

/**
 * The import/export interface for external services.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Import-export.Interface
 */
interface Ai1ec_Import_Export_Service_Engine
	extends Ai1ec_Import_Export_Engine {

	/**
	 * Register everything the interface needs into core.
	 */
	public function register_settings();

}