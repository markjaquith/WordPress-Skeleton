<?php
/**
 * Helper for scripts.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Script
 */
class Ai1ec_Script_Helper {

	/**
	 *
	 * @param $name string
	 *        Unique identifer for the script
	 *
	 * @param $file string
	 *        Filename of the script
	 *
	 * @param $deps array
	 *        Dependencies of the script
	 *
	 * @param $in_footer bool
	 *        Whether to add the script to the footer of the page
	 *
	 *
	 * @return void
	 *
	 * @see Ai1ec_Scripts::enqueue_admin_script()
	 *
	 */
	public function enqueue_script( $name, $file, $deps = array(), $in_footer = false ) {
		wp_enqueue_script( $name, $file, $deps, AI1EC_VERSION, $in_footer );
	}

}