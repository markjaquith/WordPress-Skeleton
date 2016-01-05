<?php
/**********************************************
* Widgets
**********************************************/

/** Widgets Skeleton */
function kamn_easy_twitter_feed_widget_widgets_skeleton() {
	
	/** Theme Settings */
	$kamn_easy_twitter_feed_widget_options = kamn_easy_twitter_feed_widget_get_settings();

	/** Skeleton */
	$kamn_easy_twitter_feed_widget_widgets_skeleton = array(

		'Kamn_Widget_Easytwitterfeedwidget' => array(
			'enable' => 1,
			'class' => KAMN_EASY_TWITTER_FEED_WIDGET_LIB_DIR . 'widget-easy-twitter-feed-widget.php',
			'enqueue_script' => $kamn_easy_twitter_feed_widget_options['kamn_easy_twitter_feed_widget_script_control'],
			'enqueue_style' => 0,
			'scripts' => array(
				1 => array(
					'handle' => 'kamn-js-widget-easy-twitter-feed-widget',
					'src' => KAMN_EASY_TWITTER_FEED_WIDGET_JS_URI . 'widget-easy-twitter-feed-widget.js',
					'deps' => array( 'jquery' )
				)
			),
			'styles' => array()
		)

	);

	return $kamn_easy_twitter_feed_widget_widgets_skeleton;

}

/** Widgets */
add_action( 'widgets_init', 'kamn_easy_twitter_feed_widget_widgets' );

/** Register Widgets */
function kamn_easy_twitter_feed_widget_widgets() {

	/** Avaiable Widgets */
	$kamn_easy_twitter_feed_widget_widgets_skeleton = kamn_easy_twitter_feed_widget_widgets_skeleton();

	/** Register Widgets */
	foreach( $kamn_easy_twitter_feed_widget_widgets_skeleton as $key => $widget ) {
		if( $widget['enable'] == 1 ) {
			require_once( $widget['class'] );
			register_widget( $key );
		}
	}

	/** Enqueue Widget Scripts */
	add_action( 'wp_enqueue_scripts', 'kamn_easy_twitter_feed_widget_media_widget', 20 );
	function kamn_easy_twitter_feed_widget_media_widget() {
		
		/** Avaiable Widgets */
		$kamn_easy_twitter_feed_widget_widgets_skeleton = kamn_easy_twitter_feed_widget_widgets_skeleton();
		
		/** Iterate */
		foreach( $kamn_easy_twitter_feed_widget_widgets_skeleton as $key => $widget ) {			
			
			/** Enqueue Scripts */
			if( $widget['enable'] == 1 && $widget['enqueue_script'] == 1 ) {				
				foreach( $widget['scripts'] as $script ) {
					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], '1.0', true );
				}
			}

			/** Enqueue Styles */
			if( $widget['enable'] == 1 && $widget['enqueue_style'] == 1 ) {				
				foreach( $widget['styles'] as $style ) {
					wp_enqueue_style( $style['handle'], $style['src'] );
				}
			}		
		
		}

	}	

}