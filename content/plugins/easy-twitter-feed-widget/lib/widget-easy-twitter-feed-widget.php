<?php
/**********************************************
* Twitterwidget Widget
* https://dev.twitter.com/web/embedded-timelines
**********************************************/

class Kamn_Widget_Easytwitterfeedwidget extends WP_Widget {

	/**
	 *  Set up the widget's unique name, ID, class, description, and other options.
	 */
	function __construct() {

		parent::__construct(
			'widget-easy-twitter-feed-widget-kamn',
			apply_filters( 'kamn_easy_twitter_feed_widget_name', __( 'Easy Twitter Feed Widget', 'kamn-easy-twitter-feed-widget') ),
			array(
				'classname'   => 'widget-easy-twitter-feed-widget-kamn',
				'description' => __( 'A widget to display Twitter feed.', 'kamn-easy-twitter-feed-widget' )
			)
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */

	function widget( $args, $instance ) {

		/** Global Data */
		global $post;

		/** Extract Args */
		extract( $args );

		/** Set up the default form values. */
		$defaults = $this->kamn_defaults();

		/** Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		/** Data Chrome */
		$data_chrome = array();
		$data_chrome[] = ( $instance['twitter_widget_chrome_header'] == 0 )? 'noheader': '';
		$data_chrome[] = ( $instance['twitter_widget_chrome_footer'] == 0 )? 'nofooter': '';
		$data_chrome[] = ( $instance['twitter_widget_chrome_border'] == 0 )? 'noborders': '';
		$data_chrome[] = ( $instance['twitter_widget_chrome_scrollbar'] == 0 )? 'noscrollbar': '';
		$data_chrome[] = ( $instance['twitter_widget_chrome_background'] == 0 )? 'transparent': '';

		/** Data Attributes */
		$data_twitter_widget = array(
			'data-widget-id' => $instance['twitter_widget_id'],
			'data-screen-name' => $instance['twitter_widget_screen_name'],
			'data-show-replies' => $instance['twitter_widget_show_replies'],
			'data-theme' => $instance['twitter_widget_theme'],
			'data-link-color' => $instance['twitter_widget_link_color'],
			'data-border-color' => $instance['twitter_widget_border_color'],
			'data-chrome' => trim( join( ' ', $data_chrome ) )
		);

		/** Twitter only manages scrollbar / height at default value. So this is for it :) */
		if( $instance['twitter_widget_tweet_limit'] != 0 ) {
			$data_twitter_widget['data-tweet-limit'] = $instance['twitter_widget_tweet_limit'];
		}

		/** Data Attributes as name=value */
		$data_twitter_widget_nv = '';
		foreach ( $data_twitter_widget as $key => $val ) {
			$data_twitter_widget_nv .= $key . '=' . '"' . esc_attr( $val ) . '"' . ' ';
		}

		/** Open the output of the widget. */
		echo $before_widget;

?>
		<div class="widget-easy-twitter-feed-widget-global-wrapper">
			<div class="widget-easy-twitter-feed-widget-container">

				<?php if ( ! empty( $instance['title'] ) ) : ?>
				<div class="row">
				  <div class="col-lg-12">
					<?php echo $before_title . '<span>' . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . '</span>' . $after_title; ?>
				  </div>
				</div>
				<?php endif; ?>

	          	<div class="widget-easy-twitter-feed-widget-row">
		          	<div class="widget-easy-twitter-feed-widget-col">
			          <div class="twitterwidget <?php echo $widget_id; ?>">
						<a class="twitter-timeline" width="<?php echo $instance['twitter_widget_width']; ?>" height="<?php echo $instance['twitter_widget_height']; ?>" href="https://twitter.com/twitterdev" <?php echo trim( $data_twitter_widget_nv ); ?>><?php _e( 'Tweets by @', 'kamn-easy-twitter-feed-widget' ); ?><?php echo $instance['twitter_widget_screen_name']; ?></a>
			          </div>
	          		</div>
          		</div>

          	</div> <!-- End .widget-global-wrapper -->
        </div>

<?php

		/** Close the output of the widget. */
		echo $after_widget;

	}

	/** Updates the widget control options for the particular instance of the widget.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		/** Default Args */
		$defaults = $this->kamn_defaults();

		/** Update Logic */
		$instance = $old_instance;
		foreach( $defaults as $key => $val ) {
			$instance[$key] = strip_tags( $new_instance[$key] );
		}
		return $instance;

	}

	/**
	 *
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		/** Set up the default form values. */
		$defaults = $this->kamn_defaults();

		/** Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags( $instance['title'] );
		$twitter_widget_tweet_limit = array_merge( array( 0 => 'default' ), array_combine( range( 1, 20 ), range( 1, 20 ) ) );
		$twitter_widget_show_replies = array( 'true' => 'Yes', 'false' => 'No' );
		$twitter_widget_width = range( 180, 520, 20 );
		$twitter_widget_height = range( 200, 600, 50 );
		$twitter_widget_theme = array( 'light' => 'Light', 'dark' => 'Dark' );
		$boolean = array( 1 => 'Yes', 0 => 'No' );
?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'kamn-easy-twitter-feed-widget' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p><strong><?php _e( 'Easy Twitter Feed Widget Settings', 'kamn-easy-twitter-feed-widget' ); ?></strong></p>
		<hr />

        <p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_id' ); ?>"><?php _e( 'Twitter Widget ID:', 'kamn-easy-twitter-feed-widget' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_id' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_id' ); ?>" value="<?php echo esc_attr( $instance['twitter_widget_id'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_screen_name' ); ?>"><?php _e( 'Twitter Screen Name:', 'kamn-easy-twitter-feed-widget' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_screen_name' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_screen_name' ); ?>" value="<?php echo esc_attr( $instance['twitter_widget_screen_name'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_tweet_limit' ); ?>"><?php _e( 'Tweet Limit:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_tweet_limit' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_tweet_limit' ); ?>">
              <?php foreach ( $twitter_widget_tweet_limit as $key => $val ): ?>
			    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['twitter_widget_tweet_limit'], $key ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_show_replies' ); ?>"><?php _e( 'Show Replies:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_show_replies' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_show_replies' ); ?>">
              <?php foreach ( $twitter_widget_show_replies as $key => $val ): ?>
			    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['twitter_widget_show_replies'], $key ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_width' ); ?>"><?php _e( 'Twitter Widget Width:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_width' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_width' ); ?>">
              <?php foreach ( $twitter_widget_width as $val ): ?>
			    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $instance['twitter_widget_width'], $val ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_height' ); ?>"><?php _e( 'Twitter Widget Height:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_height' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_height' ); ?>">
              <?php foreach ( $twitter_widget_height as $val ): ?>
			    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $instance['twitter_widget_height'], $val ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
            <small><?php _e( 'Height setting will work only @ Tweet Limit "default".', 'kamn-easy-twitter-feed-widget' ); ?></small>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_theme' ); ?>"><?php _e( 'Twitter Widget Theme:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_theme' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_theme' ); ?>">
              <?php foreach ( $twitter_widget_theme as $key => $val ): ?>
			    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['twitter_widget_theme'], $key ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_link_color' ); ?>"><?php _e( 'Twitter Widget Link Color:', 'kamn-easy-twitter-feed-widget' ); ?> <small><?php _e( 'e.g #333333', 'kamn-easy-twitter-feed-widget' ); ?></small></label><br />
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_link_color' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_link_color' ); ?>" value="<?php echo esc_attr( $instance['twitter_widget_link_color'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_chrome_header' ); ?>"><?php _e( 'Show Twitter Widget Header:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_chrome_header' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_chrome_header' ); ?>">
              <?php foreach ( $boolean as $key => $val ): ?>
			    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['twitter_widget_chrome_header'], $key ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_chrome_footer' ); ?>"><?php _e( 'Show Twitter Widget Footer:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_chrome_footer' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_chrome_footer' ); ?>">
              <?php foreach ( $boolean as $key => $val ): ?>
			    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['twitter_widget_chrome_footer'], $key ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_chrome_border' ); ?>"><?php _e( 'Show Twitter Widget Border:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_chrome_border' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_chrome_border' ); ?>">
              <?php foreach ( $boolean as $key => $val ): ?>
			    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['twitter_widget_chrome_border'], $key ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_border_color' ); ?>"><?php _e( 'Twitter Widget Border Color:', 'kamn-easy-twitter-feed-widget' ); ?> <small><?php _e( 'e.g #333333', 'kamn-easy-twitter-feed-widget' ); ?></small></label><br />
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_border_color' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_border_color' ); ?>" value="<?php echo esc_attr( $instance['twitter_widget_border_color'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_chrome_scrollbar' ); ?>"><?php _e( 'Show Twitter Widget Scrollbar:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_chrome_scrollbar' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_chrome_scrollbar' ); ?>">
              <?php foreach ( $boolean as $key => $val ): ?>
			    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['twitter_widget_chrome_scrollbar'], $key ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
            <small><?php _e( 'Scrollbar setting will work only @ Tweet Limit "default".', 'kamn-easy-twitter-feed-widget' ); ?></small>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_widget_chrome_background' ); ?>"><?php _e( 'Use Twitter Widget Background Color:', 'kamn-easy-twitter-feed-widget' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'twitter_widget_chrome_background' ); ?>" name="<?php echo $this->get_field_name( 'twitter_widget_chrome_background' ); ?>">
              <?php foreach ( $boolean as $key => $val ): ?>
			    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $instance['twitter_widget_chrome_background'], $key ); ?>><?php echo esc_html( $val ); ?></option>
			  <?php endforeach; ?>
            </select>
		</p>

<?php
	}

	/** Set up the default form values. */
	function kamn_defaults() {

		$defaults = array(
			'title' => esc_attr__( 'Twitter Widget', 'kamn-easy-twitter-feed-widget'),
			'twitter_widget_id' => '344713329262084096',
			'twitter_widget_screen_name' => 'designorbital',
			'twitter_widget_tweet_limit' => 0,
			'twitter_widget_show_replies' => 'false',
			'twitter_widget_width' => 300,
			'twitter_widget_height' => 250,
			'twitter_widget_theme' => 'light',
			'twitter_widget_link_color' => '',
			'twitter_widget_border_color' => '',
			'twitter_widget_chrome_header' => 1,
			'twitter_widget_chrome_footer' => 1,
			'twitter_widget_chrome_border' => 1,
			'twitter_widget_chrome_scrollbar' => 1,
			'twitter_widget_chrome_background' => 1
		);

		return $defaults;

	}

}
