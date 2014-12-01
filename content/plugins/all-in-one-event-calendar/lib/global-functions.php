<?php

/**
 * Define global functions
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Lib
 */

/**
 * Always return false for action/filter hooks
 *
 * @return boolean
 */
function ai1ec_return_false() {
	return false;
}

/**
 * Executed after initialization of Front Controller.
 *
 * @return void
 */
function ai1ec_start() {
	ob_start();
}

/**
 * Executed before script shutdown, when WP core objects are present.
 *
 * @return void
 */
function ai1ec_stop() {
	if ( ob_get_level() ) {
		echo ob_get_clean();
	}
}

/**
 * Create `<pre>` wrapped variable dump.
 *
 * @param mixed $var Arbitrary value to dump.
 *
 * @return void
 */
function ai1ec_dump( $var ) {
	if ( ! defined( 'AI1EC_DEBUG' ) || ! AI1EC_DEBUG ) {
		return null;
	}
	echo '<pre>';
	var_dump( $var );
	echo '</pre>';
	exit( 0 );
}

/**
 * Indicate deprecated function.
 *
 * @param string $function Name of called function.
 *
 * @return void
 */
function ai1ec_deprecated( $function ) {
	trigger_error(
		'Function \'' . $function . '\' is deprecated.',
		E_USER_WARNING
	);
}