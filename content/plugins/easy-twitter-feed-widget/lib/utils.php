<?php
/**********************************************
* Translation
**********************************************/

load_plugin_textdomain( 'kamn-easy-twitter-feed-widget', false, KAMN_EASY_TWITTER_FEED_WIDGET_DIR_BASENAME . 'languages/' );

/**********************************************
* Media
**********************************************/

/** Enqueue Scripts */
add_action( 'wp_enqueue_scripts', 'kamn_easy_twitter_feed_widget_media' );

/** Enqueue Scripts */
function kamn_easy_twitter_feed_widget_media() {

	/** Enqueue CSS Files */
	
	/** Plugin Stylesheet */
	wp_enqueue_style( 'kamn-css-easy-twitter-feed-widget', esc_url( KAMN_EASY_TWITTER_FEED_WIDGET_URI . 'easy-twitter-feed-widget.css' ) );

}