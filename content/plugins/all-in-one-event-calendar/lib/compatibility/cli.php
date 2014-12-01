<?php

/**
 * Command line compatibility options.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.1
 * @package    Ai1EC
 * @subpackage Ai1EC.Compatibility
 */
class Ai1ec_Compatibility_Cli {

	/**
	 * @var bool Whereas current session is command line.
	 */
	protected $_is_cli = false;

	/**
	 * Check current SAPI.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->_is_cli = 'cli' === php_sapi_name();
	}

	/**
	 * Check if running command line session.
	 *
	 * @return bool Yes/No
	 */
	public function is_cli() {
		return $this->_is_cli;
	}

	/**
	 * Disable DB debug when in command line session.
	 *
	 * @param bool $debug Current value.
	 *
	 * @return bool Optionally modified value.
	 */
	public function disable_db_debug( $debug ) {
		if ( $this->_is_cli ) {
			return false;
		}
		return $debug;
	}

}