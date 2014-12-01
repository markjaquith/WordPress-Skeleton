<?php
/**
 * Abstract strategy class to render the Request.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http.Response.Render
 */
abstract class Ai1ec_Http_Response_Render_Strategy extends Ai1ec_Base {

	/**
	 * Dump output buffers before starting output
	 *
	 * @return bool True unless an error occurs
	 */
	protected function _dump_buffers() {
		$this->_registry->get( 'dbi.dbi' )->disable_debug();
	

		return $this
			->_registry
			->get( 'compatibility.ob' )
			->end_clean_all();

	}

	/**
	 * Render the output.
	 *
	 * @param array $params
	 */
	abstract public function render( array $params );

}