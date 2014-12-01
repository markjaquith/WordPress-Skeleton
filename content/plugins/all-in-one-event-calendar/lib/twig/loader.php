<?php

/**
 * Wrapper for Twig_Loader_Filesystem
 *
 * @author     Time.ly Network Inc.
 * @since      2.1
 *
 * @package    AI1EC
 * @subpackage AI1EC.Twig
 */
class Ai1ec_Twig_Loader_Filesystem extends Twig_Loader_Filesystem {

	/**
	 * Gets the cache key to use for the cache for a given template name.
	 *
	 * @param string $name The name of the template to load
	 *
	 * @return string The cache key
	 *
	 * @throws Twig_Error_Loader When $name is not found.
	 */
	public function getCacheKey( $name ) {
		$cache_key = $this->findTemplate( $name );
		// make path relative
		$cache_key = str_replace(
			WP_PLUGIN_DIR . DIRECTORY_SEPARATOR,
			'',
			$cache_key
		);
		// namespace style separators avoid OS colisions.
		$cache_key = str_replace( '/', '\\', $cache_key );
		return $cache_key;
	}

}