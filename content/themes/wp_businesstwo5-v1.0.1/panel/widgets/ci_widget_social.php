<?php
/*
	This widget has been deprecated and related options tab has been deprecated in favor of the Socials Ignited plugin.
	You are advised to use that instead. http://wordpress.org/extend/plugins/socials-ignited/
	If you absolutely must use this social widget, edit file /functions/tabs/social_options.php and 
	uncomment the add_filter() line.
*/
?>
<?php 
if( !class_exists('CI_Social') ):
class CI_Social extends WP_Widget {

	function CI_Social(){
		$widget_ops = array('description' => __('Social Icons widget placeholder','ci_theme'));
		$control_ops = array(/*'width' => 300, 'height' => 400*/);
		parent::WP_Widget('ci_social_widget', $name='-= CI Social =-', $widget_ops, $control_ops);
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$color = isset($instance['color']) ? $instance['color'] : 'default';
		$color = ( empty($color) ? 'default' : $color );
		
		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		if(ci_setting('social_rss_show')=='enabled')
		{
			echo '<a href="'.ci_rss_feed().'" class="icn rss '.$color.'" title="'.ci_setting('social_rss_text').'">'.ci_setting('social_rss_text').'</a>';
		}

		$services = ci_social_services();
		foreach($services as $key=>$value)
		{
			$enabled = ci_setting('social_'.$key.'_show');
			$url = ci_setting('social_'.$key.'_url');
			$text = ci_setting('social_'.$key.'_text');
			
			if($enabled=='enabled' and !empty($url))
			{
				echo '<a href="'.$url.'" class="icn '.$key.' '.$color.'" title="'.$text.'">'.$text.'</a>';
			}
		}

		echo $after_widget;
	} // widget

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = stripslashes($new_instance['title']);
		$instance['color'] = stripslashes($new_instance['color']);
		return $instance;
	} // save
	
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'color'=>'default') );
		$title = htmlspecialchars($instance['title']);
		$color = htmlspecialchars($instance['color']);
		echo "<p>".__('This widget is a placeholder for Social Media icons. You may configure those icons from the <a href="themes.php?page=ci_panel.php">CSSIgniter\'s panel</a>, under the <strong>Social Options</strong> tab.', 'ci_theme')."</p>";
		echo '<p><label for="'.$this->get_field_id('title').'">' . __('Title:', 'ci_theme') . '</label><input id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" class="widefat" /></p>';
		echo '<p><label for="'.$this->get_field_id('color').'">'.__('Color scheme to use:', 'ci_theme').'</label></p>';
		echo '<select id="'.$this->get_field_id('color').'" name="' . $this->get_field_name('color') . '">';
			echo '<option value="default" '.selected('default', $color).'>'._x('Default', 'color scheme', 'ci_theme').'</option>';
			echo '<option value="light" '.selected('light', $color).'>'._x('Light', 'color scheme', 'ci_theme').'</option>';
			echo '<option value="dark" '.selected('dark', $color).'>'._x('Dark', 'color scheme', 'ci_theme').'</option>';
		echo '</select>';
	} // form

} // class


if(has_filter('ci_panel_tabs', 'ci_add_tab_social_options')!==false)
{
	register_widget('CI_Social');
}


// Include early on (i.e. 5), so that style.css has a chance of overriding these styles.
add_action('wp_enqueue_scripts', 'ci_enqueue_social_widget_css', 5);
function ci_enqueue_social_widget_css()
{
	if(has_filter('ci_panel_tabs', 'ci_add_tab_social_options')!==false)
	{
		if(is_active_widget('', '', 'ci_social_widget'))
		{
			wp_enqueue_style('ci-widget-social', get_child_or_parent_file_uri('/panel/widgets/styles/ci_widget_social.css'), array(), CI_THEME_VERSION);
		}
	}
}

endif; //class_exists
?>
