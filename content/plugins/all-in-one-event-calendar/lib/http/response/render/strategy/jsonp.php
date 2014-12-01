<?php
/**
 * Render the request as jsonp.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http.Response.Render.Strategy
 */
class Ai1ec_Render_Strategy_Jsonp extends Ai1ec_Http_Response_Render_Strategy {

	/* (non-PHPdoc)
	 * @see Ai1ec_Http_Response_Render_Strategy::render()
	 */
	public function render( array $params ) {
		$this->_dump_buffers();
		header( 'HTTP/1.1 200 OK' );
		header( 'Content-Type: application/json; charset=UTF-8' );
		$data   = Ai1ec_Http_Response_Helper::utf8( $params['data'] );
		$output = json_encode( $data );
		if ( ! empty( $params['callback'] ) ) {
			$output = $params['callback'] . '(' . $output . ')';
		} else if ( isset( $_GET['callback'] ) ) {
			$output = $_GET['callback'] . '(' . $output . ')';
		}
		
		echo $output;
		return Ai1ec_Http_Response_Helper::stop( 0 );
	}

}