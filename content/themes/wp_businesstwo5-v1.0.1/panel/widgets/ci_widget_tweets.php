<?php 
/*
 * OAuth and wp_TwitterOAuth libraries taken from Rotating Tweets (Twitter widget & shortcode) plugin, v1.3.13, http://wordpress.org/extend/plugins/rotatingtweets/
 */
if(!class_exists('wp_TwitterOAuth')) get_template_part('panel/libraries/wp_twitteroauth');

if( !class_exists('CI_Tweets') ):
class CI_Tweets extends WP_Widget {

	function CI_Tweets(){
		$widget_ops = array('description' => 'Display your latest tweets');
		$control_ops = array('width' => 200, 'height' => 400);
		parent::WP_Widget('ci_twitter_widget', $name='-= CI Tweets =-', $widget_ops, $control_ops);
	}
	
	// display in frontend
	function widget($args, $instance) {
	
		extract($args);
		$ci_title = apply_filters( 'widget_title', empty( $instance['ci_title'] ) ? '' : $instance['ci_title'], $instance, $this->id_base );
		$ci_username = $instance['ci_username'];
		$ci_number   = $instance['ci_number'];
		$callback = str_replace('ci_twitter_widget-', '', $args['widget_id']);
		$widget_class = preg_replace('/[^a-zA-Z0-9]/','', $args['widget_id']);
	
		if(ci_setting('twitter_consumer_key')=='') return;
		if(ci_setting('twitter_consumer_secret')=='') return;
		if(ci_setting('twitter_access_token')=='') return;
		if(ci_setting('twitter_access_token_secret')=='') return;
	
		$connection = new wp_TwitterOAuth(
			ci_setting('twitter_consumer_key'),
			ci_setting('twitter_consumer_secret'),
			ci_setting('twitter_access_token'),
			ci_setting('twitter_access_token_secret')
		);
	
		$trans_name = 'ci_widget_tweets_'.$ci_username.'_'.$ci_number;
		
		if(false === ($result = get_transient($trans_name)))
		{
			$result = $connection->get('statuses/user_timeline', array(
				'screen_name' => $ci_username,
				'count' => $ci_number,
				'include_rts' => 1
			));
	
			$trans_time = ci_setting('twitter_caching_seconds');
			if(intval($trans_time) < 5)
				$trans_time = 5;
			set_transient($trans_name, $result, $trans_time);
		}
		
	
		if(is_wp_error($result)) return;
	
		$data = json_decode($result['body'], true);
	
		if($data===null) return;
		
		echo $before_widget;
		if ($ci_title) echo $before_title . $ci_title . $after_title;
		echo '<div class="' . $widget_class . '  tul"><ul>';
	
		foreach($data as $tweet)
		{
	
			// URL regex taken from http://daringfireball.net/2010/07/improved_regex_for_matching_urls
			// Needed to wrap with # and escape the single quote character near the end, in order to work right.
			$url_regex = '#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#';
	
			$tweet_username = $tweet['user']['screen_name'];
			$tweet_text = $tweet['text'];
			$tweet_text = preg_replace_callback($url_regex, array($this, '_link_urls'), $tweet_text);
			$tweet_text = preg_replace_callback('/\B@([_a-z0-9]+)/i', array($this, '_link_usernames'), $tweet_text);
			$tweet_time = ci_human_time_diff( strtotime($tweet['created_at']) );
			$tweet_id = $tweet['id_str'];
			
			echo '<li><span>'.$tweet_text.'</span> <a class="twitter-time" href="http://twitter.com/'.$tweet_username.'/statuses/'.$tweet_id.'">'.$tweet_time.'</a></li>';
		}
	
		echo '</ul></div>';
		
		echo $after_widget;
	}
	
	function _link_usernames($matches)
	{
		/* E.g.
		 * $matches[0] = '@cssigniter'
		 * $matches[1] = 'cssigniter'
		 */
		return '<a href="http://twitter.com/'. $matches[1] .'">'. $matches[0] .'</a>';
	}
	function _link_urls($matches)
	{
		return '<a href="'. $matches[0] .'">'. $matches[0] .'</a>';
	}
	
	// update widget
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['ci_title'] = stripslashes($new_instance['ci_title']);
		$instance['ci_username'] = stripslashes($new_instance['ci_username']);
		$instance['ci_number'] = stripslashes($new_instance['ci_number']);
		return $instance;
	}
	
	// widget form
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array('ci_title' => '', 'ci_username'=>'', 'ci_number'=>'') );
	
		if( ci_setting('twitter_consumer_key')=='' or
			ci_setting('twitter_consumer_secret')=='' or
			ci_setting('twitter_access_token')=='' or
			ci_setting('twitter_access_token_secret')=='')
		{
			echo '<p>'.__('It looks like you haven\'t provided Twitter access details, in order for this widget to work. Unfortunately, this is needed. Please visit the theme\'s settings and provide the required access details.', 'ci_theme').'</p>';
		}
		else
		{
			$ci_title = htmlspecialchars($instance['ci_title']);
			$ci_username = htmlspecialchars($instance['ci_username']);
			$ci_number = htmlspecialchars($instance['ci_number']);
			echo '<p><label>' . 'Title:' . '</label><input id="' . $this->get_field_id('ci_title') . '" name="' . $this->get_field_name('ci_title') . '" type="text" value="' . $ci_title . '" class="widefat" /></p>';
			echo '<p><label>' . 'Username:' . '</label><input id="' . $this->get_field_id('ci_username') . '" name="' . $this->get_field_name('ci_username') . '" type="text" value="' . $ci_username . '" class="widefat" /></p>';
			echo '<p><label>' . 'Number of tweets:' . '</label><input id="' . $this->get_field_id('ci_number') . '" name="' . $this->get_field_name('ci_number') . '" type="text" value="' . $ci_number . '" class="widefat" /></p>';
		}
	
	} // form

} // class


// Check that the Twitter widget can be loaded.
// Support is added automatically upon the inclusion of the twitter_api panel snippet.
if(get_ci_theme_support('twitter_widget'))
    register_widget('CI_Tweets'); 

endif; // !class_exists
?>
