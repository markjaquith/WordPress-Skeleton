<?php

/**
 * Internationalization layer.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.P28n
 */
class Ai1ec_I18n {

	/**
	 * Translates string. Wrapper for WordPress `__()` function.
	 *
	 * @param string $term Message to translate.
	 *
	 * @return string Translated string representation.
	 */
	static public function __( $term ) {
		return __( $term, AI1EC_PLUGIN_NAME );
	}

	/**
	 * Translates string in context. Wrapper for WordPress `_x()` function.
	 *
	 * @param string $term Message to translate.
	 * @param string $ctxt Translation context for message.
	 *
	 * @return string Translated string representation.
	 */
	static public function _x( $term, $ctxt ) {
		return _x( $term, $ctxt, AI1EC_PLUGIN_NAME );
	}

}