<?php

/**
 * Renderer of settings page checkbox option.
 *
 * @author       Time.ly Network, Inc.
 * @instantiator new
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Html
 */
class Ai1ec_Html_Settings_Checkbox extends Ai1ec_Html_Element_Settings {

	
	/* (non-PHPdoc)
	 * @see Ai1ec_Html_Element_Settings::render()
	 */
	public function render( $output = '' ) {
		$attributes = array(
			'class' => 'checkbox',
		);
		if ( true === $this->_args['value'] ) {
			$attributes['checked'] = 'checked';
		}
		$args               = $this->_args;
		$args['attributes'] = $attributes;
		$loader             = $this->_registry->get( 'theme.loader' );
		$file               = $loader->get_file(
			'setting/checkbox.twig',
			$args,
			true
		);
		return parent::render( $file->get_content() );
	}

}