<?php

/**
 * Numeric validator.
 *
 * @author       Time.ly Network Inc.
 * @since        2.0
 * @instantiator new
 * @package      AI1EC
 * @subpackage   AI1EC.Validator
 */
class Ai1ec_Validator_Numeric_Or_Default extends Ai1ec_Validator {

	/* (non-PHPdoc)
	 * @see Ai1ec_Validator::validate()
	 */
	public function validate() {
		if ( ! is_numeric( $this->_value ) ) {
			throw new Ai1ec_Value_Not_Valid_Exception();
		}
		return (int)$this->_value;
	}

}