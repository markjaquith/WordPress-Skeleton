<?php

/**
 * Base application model.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Model
 */
class Ai1ec_App extends Ai1ec_Base {

	/**
	 * Initiate base objects.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 * @internal param \Ai1ec_Registry_Object $system Injectable system object.
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$this->_initialize();
	}

	/**
	 * Post construction routine.
	 *
	 * Override this method to perform post-construction tasks.
	 *
	 * @return void Return from this method is ignored.
	 */
	protected function _initialize() {}


}