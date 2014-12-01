<?php

/**
 * The base class which simply sets the registry object.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Bootstrap
 */
abstract class Ai1ec_Base {

	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected $_registry;

	/**
	 * The contructor method.
	 *
	 * Stores in object injected registry object.
	 *
	 * @param Ai1ec_Registry_Object $registry Injected registry object.
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
	}

}