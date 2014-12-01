<?php

/**
 * Basic interface for the composite.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Html
 */
interface Ai1ec_Renderable {
	/**
	 * This is the main function, it just renders the method for the element,
	 * taking care of childrens ( if any )
	 */
	public function render();
}