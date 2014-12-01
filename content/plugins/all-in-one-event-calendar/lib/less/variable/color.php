<?php

/**
 * This class represents a LESS variable of type color. It supports hex, rgb
 * and rgba formats.
 *
 * @author       Time.ly Network Inc.
 * @since        2.0
 * @instantiator new
 * @package      AI1EC
 * @subpackage   AI1EC.Less.Variable
 */
class Ai1ec_Less_Variable_Color extends Ai1ec_Less_Variable {

	/**
	 * @var boolean
	 */
	protected  $readonly = false;

	public function render() {
		$readonly = $this->readonly === true ? 'readonly' : '';
		
		$args = array(
			'label' =>    $this->description,
			'readonly' => $readonly,
			'id' => $this->id,
			'value' => $this->value,
			'format' => $this->_get_format(),
		);
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'theme-options/color-picker.twig', $args, true );
		return $file->get_content();
	}

	/**
	 * (non-PHPdoc)
	 * Set up the color picker
	 * @see Ai1ec_Less_Variable::set_up_renderable()
	 */
	protected function _get_format() {
		$format = 'hex';
		if( substr( $this->value, 0, 3 ) === 'rgb' ) {
			if( substr( $this->value, 0, 4 ) === 'rgba' ) {
				$format = 'rgba';
			} else {
				$format = 'rgb';
			}
		}
		return $format;
	}

}
