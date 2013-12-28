<?php 
if( !class_exists('CI_Ads125') ):
class CI_Ads125 extends WP_Widget {

	function CI_Ads125(){
		$widget_ops = array('description' => __('Display 125x125 Banners','ci_theme'));
		$control_ops = array('width' => 300, 'height' => 400);
		parent::WP_Widget('ci_ads125_widget', $name='-= CI 125x125 Ads =-', $widget_ops, $control_ops);
	}


	function widget($args, $instance) {
		extract($args);
		$ci_title = apply_filters( 'widget_title', empty( $instance['ci_title'] ) ? '' : $instance['ci_title'], $instance, $this->id_base );
		$ci_random = $instance['ci_random'];
		$ci_new_win = $instance['ci_new_win'];

		$b = array();
		for($i=1; $i<=8; $i++)
		{
			$b[$i]['url'] = $instance['ci_b'.$i.'url'];
			$b[$i]['lin'] = $instance['ci_b'.$i.'lin'];
			$b[$i]['tit'] = $instance['ci_b'.$i.'tit'];
		}
		
		echo $before_widget;
	
		if ($ci_title) 
			echo $before_title . $ci_title . $after_title;

		echo '<ul id="ads125" class="group">';

		if($ci_random == "random")
			shuffle($b);

		$target = '';
		if($ci_new_win == 'enabled')
			$target = ' target="_blank" ';

		$i=1;
		foreach($b as $key=>$value)
		{
			if (!empty($value['url']))
			{
				if ($i % 2==0)
					echo '<li class="last"><a href="'. $value['lin'] .'" ' . $target . ' ><img src="' . $value['url'] . '" alt="' . $value['tit'] . '" /></a></li>';
				else			
					echo '<li><a href="'. $value['lin'] .'" ' . $target . ' ><img src="' . $value['url'] . '" alt="' . $value['tit'] . '" /></a></li>';
			$i++;
			}
		}

		echo "</ul>";
	
		echo $after_widget;
	} // widget

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['ci_title'] = stripslashes($new_instance['ci_title']);
		$instance['ci_random'] = $new_instance['ci_random'];
		$instance['ci_new_win'] = $new_instance['ci_new_win'];
		
		for($i=1; $i<=8; $i++)
		{
			$instance['ci_b'.$i.'url'] = stripslashes($new_instance['ci_b'.$i.'url']);
			$instance['ci_b'.$i.'lin'] = stripslashes($new_instance['ci_b'.$i.'lin']);
			$instance['ci_b'.$i.'tit'] = stripslashes($new_instance['ci_b'.$i.'tit']);
		}
		
		return $instance;
	} // save

	function form($instance){
		$instance = wp_parse_args( (array) $instance, array('ci_title'=>'', 'ci_random' => '', 'ci_new_win' => '', 'ci_b1url'=>'', 'ci_b1lin'=>'', 'ci_b1tit'=>'', 'ci_b2url'=>'', 'ci_b2lin'=>'', 'ci_b2tit'=>'' , 'ci_b3url'=>'', 'ci_b3lin'=>'', 'ci_b3tit'=>'' , 'ci_b4url'=>'', 'ci_b4lin'=>'', 'ci_b4tit'=>'' , 'ci_b5url'=>'', 'ci_b5lin'=>'', 'ci_b5tit'=>'' , 'ci_b6url'=>'', 'ci_b6lin'=>'', 'ci_b6tit'=>'' , 'ci_b7url'=>'', 'ci_b7lin'=>'', 'ci_b7tit'=>'' , 'ci_b8url'=>'', 'ci_b8lin'=>'', 'ci_b8tit'=>''));
		
		$ci_title = htmlspecialchars($instance['ci_title']);
		$ci_random = $instance['ci_random'];
		$ci_new_win = $instance['ci_new_win'];

		$b = array();
		for($i=1; $i<=8; $i++)
		{
			$b[$i]['url'] = htmlspecialchars($instance['ci_b'.$i.'url']);
			$b[$i]['lin'] = htmlspecialchars($instance['ci_b'.$i.'lin']);
			$b[$i]['tit'] = htmlspecialchars($instance['ci_b'.$i.'tit']);
		}
		
		
		echo '<p><label>' . __('Title', 'ci_theme') . '</label><input id="' . $this->get_field_id('ci_title') . '" name="' . $this->get_field_name('ci_title') . '" type="text" value="' . $ci_title . '" class="widefat" /></p>';
		echo '<p><input id="' . $this->get_field_id('ci_random') . '" name="' . $this->get_field_name('ci_random') . '" type="checkbox"'. checked($instance['ci_random'], "random") .' value="random"  /> <label for="'.$this->get_field_id('ci_random').'"><strong>' . __('Display ads in random order?', 'ci_theme') . '</strong></label></p>';
		echo '<p><input id="' . $this->get_field_id('ci_new_win') . '" name="' . $this->get_field_name('ci_new_win') . '" type="checkbox"'. checked($instance['ci_new_win'], "enabled") .' value="enabled"  /> <label for="'.$this->get_field_id('ci_new_win').'"><strong>' . __('Open ads in new window?', 'ci_theme') . '</strong></label></p>';

		for($i=1; $i<=8; $i++)
		{
			$b[$i]['url'] = htmlspecialchars($instance['ci_b'.$i.'url']);
			$b[$i]['lin'] = htmlspecialchars($instance['ci_b'.$i.'lin']);
			$b[$i]['tit'] = htmlspecialchars($instance['ci_b'.$i.'tit']);
			echo '<p><label>' . 'Banner #'.$i.' URL'	. '</label><input id="' . $this->get_field_id('ci_b'.$i.'url') . '" name="' . $this->get_field_name('ci_b'.$i.'url') . '" type="text" value="' . $b[$i]['url'] . '" class="widefat" /></p>';
			echo '<p><label>' . 'Banner #'.$i.' Link' 	. '</label><input id="' . $this->get_field_id('ci_b'.$i.'lin') . '" name="' . $this->get_field_name('ci_b'.$i.'lin') . '" type="text" value="' . $b[$i]['lin'] . '" class="widefat" /></p>';
			echo '<p><label>' . 'Banner #'.$i.' Title' 	. '</label><input id="' . $this->get_field_id('ci_b'.$i.'tit') . '" name="' . $this->get_field_name('ci_b'.$i.'tit') . '" type="text" value="' . $b[$i]['tit'] . '" class="widefat" /></p>';
		}


	} // form

} // class

register_widget('CI_Ads125');

endif; //class_exists
?>
