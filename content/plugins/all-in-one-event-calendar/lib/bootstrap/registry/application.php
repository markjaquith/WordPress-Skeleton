<?php

/**
 * Application Registry: handles application wide variables.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Object
 */
class Ai1ec_Registry_Application implements Ai1ec_Registry {

	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected $_registry;

	/**
	 * @var array
	 */
	protected $_environment = array();

	/**
	 * The contructor method.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 */
	function __construct( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Registry::get()
	 */
	public function get( $key ) {
		if ( ! isset ( $this->_environment[$key] ) ) {
			return false;
		}
		return $this->_environment[$key];
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Registry::set()
	 */
	public function set( $key, $value ) {
		$this->_environment[$key] = $value;
	}
}