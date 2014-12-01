<?php

/**
 * Renderer of settings page textarea option.
 *
 * @author       Time.ly Network, Inc.
 * @instantiator new
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Html
 */
class Ai1ec_Html_Setting_Textarea extends Ai1ec_Html_Element_Settings {

	const DEFAULT_ROWS = 6;

	/* (non-PHPdoc)
	 * @see Ai1ec_Html_Element_Settings::render()
	 */
	public function render( $output = '' ) {
		$type  = $this->_args['renderer']['type'];
		$date  = $append = false;

		// Set attributes
		$input_args = array();

		// Set textarea rows
		if ( ! empty( $this->_args['renderer']['rows'] ) ) {
			$input_args['rows'] = $this->_args['renderer']['rows'];
		}

		// Set textarea disabled
		if ( ! empty( $this->_args['renderer']['disabled'] ) ) {
			$input_args['disabled'] = $this->_args['renderer']['disabled'];
		}

		// Set textarea readonly
		if ( ! empty( $this->_args['renderer']['readonly'] ) ) {
			$input_args['readonly'] = $this->_args['renderer']['readonly'];
		}

		$args = array(
			'id'         => $this->_args['id'],
			'label'      => $this->_args['renderer']['label'],
			'input_args' => $input_args,
			'value'      => $this->_args['value'],
		);
		if ( true === $append ) {
			$args['append'] = $this->_args['renderer']['append'];
		}
		if ( isset( $this->_args['renderer']['help'] ) ) {
			$args['help'] = $this->_args['renderer']['help'];
		}
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'setting/textarea.twig', $args, true );
		return parent::render( $file->get_content() );
	}

}
