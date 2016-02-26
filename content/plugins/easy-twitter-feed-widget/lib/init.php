<?php
class Kamn_Easytwitterfeedwidget {
	
	/** Constructor */
	function __construct() {

		/** Standard Class */
		global $kamn_easy_twitter_feed_widget;
		$kamn_easy_twitter_feed_widget = new stdClass;
		
		/** Loader */
		add_action( 'plugins_loaded', array( $this, 'kamn_easy_twitter_feed_widget_loader' ), 10 );
		
		/** Setup */
		add_action( 'plugins_loaded', array( $this, 'kamn_easy_twitter_feed_widget_setup' ), 12 );		

	}
	
	/** Loader */
	function kamn_easy_twitter_feed_widget_loader() {

		/** Directory Location Constants */

		if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR' ) ) {
			define( 'KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR', trailingslashit( KAMN_EASY_TWITTER_FEED_WIDGET_DIR . 'lib' ) );
		}		

		if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_DIR' ) ) {
			define( 'KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_DIR', trailingslashit( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR . 'admin' ) );
		}

		if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_JS_DIR' ) ) {
			define( 'KAMN_EASY_TWITTER_FEED_WIDGET_JS_DIR', trailingslashit( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR . 'js' ) );
		}

		if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_CSS_DIR' ) ) {
			define( 'KAMN_EASY_TWITTER_FEED_WIDGET_CSS_DIR', trailingslashit( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR . 'css' ) );
		}
		
		/** URI Location Constants */

		if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_LIB_URI' ) ) {
			define( 'KAMN_EASY_TWITTER_FEED_WIDGET_LIB_URI', trailingslashit( KAMN_EASY_TWITTER_FEED_WIDGET_URI . 'lib' ) );
		}

		if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_URI' ) ) {
			define( 'KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_URI', trailingslashit( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_URI . 'admin' ) );
		}

		if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_JS_URI' ) ) {
			define( 'KAMN_EASY_TWITTER_FEED_WIDGET_JS_URI', trailingslashit( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_URI . 'js' ) );
		}

		if ( !defined( 'KAMN_EASY_TWITTER_FEED_WIDGET_CSS_URI' ) ) {
			define( 'KAMN_EASY_TWITTER_FEED_WIDGET_CSS_URI', trailingslashit( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_URI . 'css' ) );
		}

		/** Core Classes / Functions */
		require_once( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR . 'core.php' );

		/** Register Modules */
		require_once( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR . 'modules.php' );
		
		/** Load Admin */
		if ( is_admin() ) {
			
			/** Admin Options */
			require_once( KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_DIR . 'admin.php' );

		}
		
	}	
	
	/** Plugin Setup */
	function kamn_easy_twitter_feed_widget_setup() {
		
		/** Utility */
		require_once( KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR . 'utils.php' );
		
	}
	
}

/** Initiate Class */
new Kamn_Easytwitterfeedwidget();