<?php

/**
 * The abstract class for validators.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Validator
 */
abstract class Ai1ec_Validator extends Ai1ec_Base {

	/**
	 * @var mixed The value to validate.
	 */
	protected $_value;

	/**
	 * @var mixed Additional info needed for complex validation
	 */
	protected $_context;

	/**
	 * Constructor function
	 * 
	 * @param Ai1ec_Registry_Object $registry
	 * @param string $value
	 * @param mixed $context
	 */
	public function __construct( Ai1ec_Registry_Object $registry, $value, $context = array() ) {
		parent::__construct( $registry );
		$this->_value   = $value;
		$this->_context = $context;
	}

	/**
	 * Validates the value.
	 * 
	 * @throws Ai1ec_Value_Not_Valid_Exception if the velue is not valid.
	 * 
	 * @return mixed the validated value (allows to set it to default)
	 */
	abstract public function validate();

}