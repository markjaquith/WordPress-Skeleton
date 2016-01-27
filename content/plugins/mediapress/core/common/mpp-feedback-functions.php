<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

//Inspired by BuddyPress Message feedback and modified to suit our need
//We don't really need it until we are implementing the BP independent galleries in future but let us keep it in our fold from the begining

/** Messages ******************************************************************/

/**
 * Add a feedback (error/success) message to the WP cookie so it can be displayed after the page reloads.
 *
 * @param string $message Feedback message to be displayed.
 * @param string $type Message type. 'updated', 'success', 'error', 'warning'.
 *        Default: 'success'.
 */
function mpp_add_feedback( $message, $type = '' ) {

	// Success is the default
	if ( empty( $type ) ) {
		$type = 'success';
	}

	// Send the values to the cookie for page reload display
	@setcookie( 'mpp-message',      $message, time() + 60 * 60 * 24, COOKIEPATH );
	@setcookie( 'mpp-message-type', $type,    time() + 60 * 60 * 24, COOKIEPATH );

	// Get MediaPress
	$mp = mediapress();

	/***
	 * Send the values to the $bp global so we can still output messages
	 * without a page reload
	 */
	$mp->template_message      = $message;
	$mp->template_message_type = $type;
}

/**
 * Set up the display of the 'template_notices' feedback message.
 *
 * Checks whether there is a feedback message in the WP cookie and, if so, adds
 * a "template_notices" action so that the message can be parsed into the
 * template and displayed to the user.
 *
 * After the message is displayed, it removes the message vars from the cookie
 * so that the message is not shown to the user multiple times.
 *
 * @uses setcookie() Sets a cookie value for the user.
 */
function mpp_core_setup_feedback_message() {

	// Get MediaPress
	$mp = mediapress();

	if ( empty( $mp->template_message ) && isset( $_COOKIE['mpp-message'] ) ) {
		$mp->template_message = stripslashes( $_COOKIE['mpp-message'] );
	}

	if ( empty( $mp->template_message_type ) && isset( $_COOKIE['mpp-message-type'] ) ) {
		$mp->template_message_type = stripslashes( $_COOKIE['mpp-message-type'] );
	}

	add_action( 'template_notices', 'mpp_core_render_feedback' );

	if ( isset( $_COOKIE['mpp-message'] ) ) {
		@setcookie( 'mpp-message', false, time() - 1000, COOKIEPATH );
	}

	if ( isset( $_COOKIE['mpp-message-type'] ) ) {
		@setcookie( 'mpp-message-type', false, time() - 1000, COOKIEPATH );
	}
}
add_action( 'mpp_actions', 'mpp_core_setup_feedback_message', 5 );

/**
 * Render the 'template_notices' feedback message.
 *
 * The hook action 'template_notices' is used to call this function, it is not
 * called directly.
 */
function mpp_core_render_feedback() {

	// Get MediaPress
	$mp = mediapress();

	if ( ! empty( $mp->template_message ) ) :
		$type    = ( 'success' === $mp->template_message_type ) ? 'updated' : 'error';
		$content = apply_filters( 'mpp_core_render_feedback_content', $mp->template_message, $type ); ?>

		<div id="message" class="bp-template-notice mpp-template-notice <?php echo esc_attr( $type ); ?>">

			<?php echo wpautop( $content ); ?>

		</div>

	<?php

		do_action( 'mpp_core_render_feedback' );

	endif;
}