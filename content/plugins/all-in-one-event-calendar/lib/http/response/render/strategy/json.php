<?php
/**
 * Render the request as json.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http.Response.Render.Strategy
 */
class Ai1ec_Render_Strategy_Json extends Ai1ec_Render_Strategy_Jsonp {

	/* (non-PHPdoc)
	 * @see Ai1ec_Http_Response_Render_Strategy::render()
	 */
	public function render( array $params ) {
		$params['callback'] = '';
		return parent::render( $params );
	}
}