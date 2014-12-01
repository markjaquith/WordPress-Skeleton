<?php

/**
 * The class which handles Twig cache rescan process.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Twig
 */
class Ai1ec_Twig_Cache extends Ai1ec_Base {

	/**
	 * Rescan cache for writable directory.
	 *
	 * @return void
	 */
	public function rescan() {
		$cache_dir = $this->_registry->get( 'theme.loader' )
				->get_cache_dir( true );
		$render_json    = $this->_registry->get(
				'http.response.render.strategy.json'
		);
		$output['data'] = array(
			'state' => (int)(false !== $cache_dir),
		);
		$render_json->render( $output );
	}

}
