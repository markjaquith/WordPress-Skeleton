<?php
/**********************************************
* Default Options
**********************************************/

function kamn_easy_twitter_feed_widget_options_default()  {
	
	$default = array(
			
		'kamn_easy_twitter_feed_widget_script_control' => 1,
		'kamn_easy_twitter_feed_widget_reset_control' => 0
		
	);
	
	return $default;
	
}

/**********************************************
* Plugin Settings
**********************************************/

/** Loads the plugin setting. */
function kamn_easy_twitter_feed_widget_get_settings() {
	
	/** Global Data */
	global $kamn_easy_twitter_feed_widget;

	/* If the settings array hasn't been set, call get_option() to get an array of plugin settings. */
	if ( !isset( $kamn_easy_twitter_feed_widget->settings ) ) {
		$kamn_easy_twitter_feed_widget->settings = apply_filters( 'kamn_easy_twitter_feed_widget_options_filter', wp_parse_args( get_option( 'kamn_easy_twitter_feed_widget_options', kamn_easy_twitter_feed_widget_options_default() ), kamn_easy_twitter_feed_widget_options_default() ) );
	}
	
	/** return settings. */
	return $kamn_easy_twitter_feed_widget->settings;
}

/**********************************************
* Plugin Data
**********************************************/

/** Function for getting the plugin data */
function kamn_easy_twitter_feed_widget_plugin_data() {
	
	/** Global Data */
	global $kamn_easy_twitter_feed_widget;
	
	/** If the parent theme data isn't set, let grab it. */
	if ( !isset( $kamn_easy_twitter_feed_widget->plugin_data ) ) {
		$kamn_easy_twitter_feed_widget->plugin_data = get_plugin_data( KAMN_EASY_TWITTER_FEED_WIDGET_DIR . 'easy-twitter-feed-widget.php' );
	}

	/** Return the plugin data. */
	return $kamn_easy_twitter_feed_widget->plugin_data;
}

/**********************************************
* External Link
**********************************************/

function kamn_easy_twitter_feed_widget_external_link( $key = '' ) {

	$kamn_easy_twitter_feed_widget_external_link = array(
		'fa-icons' => 'http://fontawesome.io/icons/',
	);

	return $kamn_easy_twitter_feed_widget_external_link[$key];

}