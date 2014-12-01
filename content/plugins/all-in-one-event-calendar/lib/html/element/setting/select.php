<?php

/**
 * Renderer of settings page select option.
 *
 * @author       Time.ly Network, Inc.
 * @instantiator new
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Html
 */
class Ai1ec_Html_Setting_Select extends Ai1ec_Html_Element_Settings {

	/* (non-PHPdoc)
	 * @see Ai1ec_Html_Element_Settings::render()
	 */
	public function render( $output = '' ) {
		if ( isset( $this->_args['renderer']['condition'] ) ) {
			$callback = explode( ':', $this->_args['renderer']['condition'] );
			$render   = $this->_registry->dispatch(
				$callback[0],
				$callback[1]
			);
			if ( ! $render ) {
				return '';
			}
		}
		$options = $this->_args['renderer']['options'];
		if ( ! is_array( $options ) ) {
			$callback = explode( ':', $options );
			if ( ! isset( $callback[1] ) ) {
				$options = $this->{$options}();
			} else {
				$value = $this->_args['value'];
				if( false === is_array( $this->_args['value'] ) ){
					$value = array( $this->_args['value'] );
				}
				$options = $this->_registry->dispatch(
					$callback[0],
					$callback[1]
				);
			}
		}
		$options = apply_filters( 'ai1ec_settings_select_options' , $options, $this->_args['id'] );
		foreach ( $options as $key => &$option ) {
			// if the key is a string, it's an optgroup
			if ( is_string( $key ) ) {
				foreach ( $option as &$opt ) {
					$opt = $this->_set_selected_value( $opt );
				}
			} else {
				$option = $this->_set_selected_value( $option );
			}
		}
		$select_args = array();
		$args = array(
			'id'         => $this->_args['id'],
			'label'      => $this->_args['renderer']['label'],
			'attributes' => $select_args,
			'options'    => $options,
		);
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'setting/select.twig', $args, true );
		return parent::render( $file->get_content() );
	}

	/**
	 * Toggle `selected` attribute according to current selection.
	 *
	 * @param array $option Option being checked.
	 *
	 * @return array Optionally modified option entry.
	 */
	protected function _set_selected_value( array $option ) {
		if ( $option['value'] === $this->_args['value'] ) {
			$option['args'] = array(
				'selected' => 'selected',
			);
		}
		return $option;
	}

	/**
	 * Gets the options for the "Starting day of week" select.
	 *
	 * @return array
	 */
	protected function get_weekdays() {
		$locale  = $this->_registry->get( 'p28n.wpml' );
		$options = array();
		for ( $day_index = 0; $day_index <= 6; $day_index++ ) {
			$option = array(
				'text'  => $locale->get_weekday( $day_index ),
				'value' => $day_index,
			);
			$options[] = $option;
		}
		return $options;
	}

}
