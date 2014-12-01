<?php

/**
 * Render the request as ical.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http.Response.Render.Strategy
 */
class Ai1ec_Render_Strategy_Redirect extends Ai1ec_Http_Response_Render_Strategy {

	/* (non-PHPdoc)
	 * @see Ai1ec_Http_Response_Render_Strategy::render()
	*/
	public function render( array $params ) {
		Ai1ec_Wp_Uri_Helper::local_redirect( 
			$params['url'], 
			$params['query_args']
		);
	}

}