<?php

/**
 * Interface for HTML elements.
 *
 * In this context element is a complex collection of HTML tags
 * rendered to suit specific needs.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Html
 */
interface Ai1ec_Html_Element_Interface {

	/**
	 * Set attribute for renderable element.
	 *
	 * Attributes are object specific.
	 *
	 * @param string $attribute Name of attribute to set.
	 * @param mixed  $value     Value to set for attribute.
	 *
	 * @return Ai1ec_Html_Element_Interface Instance of self for chaining.
	 */
	public function set( $attribute, $value );

	/**
	 * Generate HTML snippet for inclusion in page.
	 *
	 * @param string $snippet Particle to append to result.
	 *
	 * @return string HTML snippet.
	 *
	 * @throws Ai1ec_Html_Exception If rendering may not be completed.
	 */
	public function render( $snippet = '' );

}