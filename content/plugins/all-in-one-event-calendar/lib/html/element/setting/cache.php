<?php

/**
 * Renderer of settings page html.
 *
 * @author       Time.ly Network, Inc.
 * @instantiator new
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Html
 */
class Ai1ec_Html_Setting_Cache extends Ai1ec_Html_Element_Settings {

	/* (non-PHPdoc)
	 * @see Ai1ec_Html_Element_Settings::render()
	 */
	public function render( $output = '' ) {
		$args   = $this->get_twig_cache_args();
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'setting/twig_cache.twig', $args, true );
		return parent::render( $file->get_content() );
	}

	/**
	 * Returns data for Twig template.
	 *
	 * @return array Data for template
	 */
	public function get_twig_cache_args() {
		$args = array(
			'cache_available' => (
				AI1EC_CACHE_UNAVAILABLE !== $this->_args['value'] &&
				! empty( $this->_args['value'] )
			),
			'id'              => $this->_args['id'],
			'label'           => $this->_args['renderer']['label'],
			'text'            => array(
				'refresh' => Ai1ec_I18n::__( 'Check again' ),
				'nocache' => Ai1ec_I18n::__( 'Templates cache is not writable' ),
				'okcache' => Ai1ec_I18n::__( 'Templates cache is writable' ),
				'rescan'  => Ai1ec_I18n::__( 'Checking...' ),
				'title'   => Ai1ec_I18n::__( 'Performance Report' ),
			),
		);

		return $args;
	}
}