<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Front-end Actions
 *
 * @package     Ninja Forms
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, WP Ninjas
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.7
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Hooks NF actions, when present in the $_GET superglobal. Every nf_action
 * present in $_GET is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 2.7
 * @return void
*/
function nf_get_actions() {
	if ( isset( $_GET['nf_action'] ) ) {
		do_action( 'nf_' . $_GET['nf_action'], $_GET );
	}
}
add_action( 'init', 'nf_get_actions', 999 );

/**
 * Hooks NF actions, when present in the $_POST superglobal. Every nf_action
 * present in $_POST is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 2.7
 * @return void
*/
function nf_post_actions() {
	if ( isset( $_POST['nf_action'] ) ) {
		do_action( 'nf_' . $_POST['nf_action'], $_POST );
	}
}
add_action( 'init', 'nf_post_actions', 999 );