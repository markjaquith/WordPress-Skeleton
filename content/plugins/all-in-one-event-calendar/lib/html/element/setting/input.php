<?php

/**
 * Renderer of settings page input option.
 *
 * @author       Time.ly Network, Inc.
 * @instantiator new
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Html
 */
class Ai1ec_Html_Setting_Input extends Ai1ec_Html_Element_Settings {


	/* (non-PHPdoc)
	 * @see Ai1ec_Html_Element_Settings::render()
	 */
	public function render( $output = '' ) {
		$system = $this->_registry->get( 'date.system' );
		$type   = $this->_args['renderer']['type'];
		$date   = $append = false;
		$class  = '';
		$input_type = 'text';
		switch ( $type ) {
			case 'date':
				$date = true;
				break;

			case 'append':
				$append = true;
				break;

			case 'email':
				$input_type = 'email';
				break;
		}

		$input_args = array(
			'class' => $class,
		);
		$settings    = $this->_registry->get( 'model.settings' );
		if ( true === $date ) {
			$input_args += array(
				'data-date-weekstart' => $settings->get( 'week_start_day' ),
				'data-date-format'    => $system->get_date_pattern_by_key(
					$settings->get( 'input_date_format' )
				),
				'size'                => 12,
			);
		}
		$args = array(
			'id'         => $this->_args['id'],
			'label'      => $this->_args['renderer']['label'],
			'input_args' => $input_args,
			'input_type' => $input_type,
			'value'      => $this->_args['value'],
		);
		if ( isset( $this->_args['renderer']['status'] ) ) {
			$args['licence_valid'] =
				$settings->get( $this->_args['renderer']['status'] ) === 'valid' ?
				true :
				false;
		}
		if ( true === $append ) {
			$args['append'] = $this->_args['renderer']['append'];
		}
		if ( isset( $this->_args['renderer']['help'] ) ) {
			$args['help'] = $this->_args['renderer']['help'];
		}
		if ( isset( $this->_args['renderer']['group-class'] ) ) {
			$args['group_class'] = $this->_args['renderer']['group-class'];
		}
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'setting/input.twig', $args, true );
		return parent::render( $file->get_content() );
	}

}