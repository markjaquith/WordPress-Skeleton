<?php

/**
 * Exceptions occuring during bootstrap
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Exception
 */
class Ai1ec_Bootstrap_Exception extends Ai1ec_Exception {

	public function get_html_message() {
		return '<p>Failure in All-in-One Event Calendar core:<br />' .
			$this->getMessage() . '</p>';
	}

}