<?php

/**
 * Wrapper to HTML convenience methods.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Html
 */
class Ai1ec_Html_Helper {

	/**
	 * Escape HTML for use within any tag.
	 *
	 * @param string $input Arbitrary UTF-8 input.
	 *
	 * @return string Escaped HTML value.
	 */
	public function esc_html( $input ) {
		return esc_html( $input );
	}

	/**
	 * Escape string for use within HTML attribute.
	 *
	 * @param string $input Characters to be used as HTML attribute.
	 *
	 * @return string Escaped characters sequence.
	 */
	public function esc_attr( $input ) {
		return esc_attr( $input );
	}

}