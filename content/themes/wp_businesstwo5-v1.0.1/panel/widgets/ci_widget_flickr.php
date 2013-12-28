<?php
if( !class_exists('CI_Flickr') ):
class CI_Flickr extends WP_Widget {

	function CI_flickr() {
		$widget_ops = array('description' => __('FlickR Widget','ci_theme'));
		$control_ops = array('width' => 200);
		parent::WP_Widget('ci_flickr_widget', '-= CI Flickr Widget =-',$widget_ops,$control_ops); 
	}
	
	function widget($args,$instance) {  
		extract($args);
		$ci_title   = apply_filters( 'widget_title', empty( $instance['ci_title'] ) ? '' : $instance['ci_title'], $instance, $this->id_base );
		$ci_id      = $instance['ci_id'];
		$ci_number  = $instance['ci_number'];
		$ci_type    = $instance['ci_type'];
		$ci_sorting = $instance['ci_sorting'];

		echo $before_widget;
		if ($ci_title) echo $before_title . $ci_title . $after_title;
		?>
			<div class="f group"><script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $ci_number; ?>&amp;display=<?php echo $ci_sorting; ?>&amp;&amp;layout=x&amp;source=<?php echo $ci_type; ?>&amp;<?php echo $ci_type; ?>=<?php echo $ci_id; ?>&amp;size=s"></script></div>        
		<?php	
		echo $after_widget;
	}

	function update($new_instance, $old_instance) { 
		return $new_instance;
	}

	function form($instance) { 
		$instance = wp_parse_args( (array) $instance, array('ci_title' => '', 'ci_id' => '', 'ci_number' => '', 'ci_type' => '', 'ci_sorting' => '', 'ci_size' => ''));       
		$ci_title   = esc_attr($instance['ci_title']);
		$ci_id      = esc_attr($instance['ci_id']);
		$ci_number  = esc_attr($instance['ci_number']);
		$ci_type    = esc_attr($instance['ci_type']);
		$ci_sorting = esc_attr($instance['ci_sorting']);
		?>
		<p>
			<label><?php _e('Title:','ci_theme'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('ci_title'); ?>" value="<?php echo $ci_title; ?>" class="widefat" id="<?php echo $this->get_field_id('ci_title'); ?>" />
		</p>
		<p>
			<label><span style="color:#0063DC; font-weight:bold;">Flick<i style="font-style:normal;color:#FF0084">r</i></span> ID:</label>
			<input type="text" name="<?php echo $this->get_field_name('ci_id'); ?>" value="<?php echo $ci_id; ?>" class="widefat" id="<?php echo $this->get_field_id('ci_id'); ?>" />
		</p>
		<p>
			<label><?php _e('Type:','ci_theme'); ?></label>
			<select name="<?php echo $this->get_field_name('ci_type'); ?>" class="widefat" id="<?php echo $this->get_field_id('ci_type'); ?>">
				<option value="user" <?php if($ci_type == "user"){ echo "selected='selected'";} ?>>User</option>
				<option value="group" <?php if($ci_type == "group"){ echo "selected='selected'";} ?>>Group</option>            
			</select>
		</p>
		<p>
			<label><?php _e('Number:','ci_theme'); ?></label>
			<select name="<?php echo $this->get_field_name('ci_number'); ?>" class="widefat" id="<?php echo $this->get_field_id('ci_number'); ?>">
				<?php for ( $ci = 1; $ci <= 9; $ci += 1) { ?>
					<option value="<?php echo $ci; ?>" <?php if($ci_number == $ci){ echo "selected='selected'";} ?>><?php echo $ci; ?></option>
				<?php } ?>
			</select>
		</p>
		
		<p>
			<label><?php _e('Sorting:','ci_theme'); ?></label>
			<select name="<?php echo $this->get_field_name('ci_sorting'); ?>" class="widefat" id="<?php echo $this->get_field_id('ci_sorting'); ?>">
				<option value="latest" <?php if($ci_sorting == "latest"){ echo "selected='selected'";} ?>>Latest</option>
				<option value="random" <?php if($ci_sorting == "random"){ echo "selected='selected'";} ?>>Random</option>            
			</select>
		</p>
		<?php
	}
} 

register_widget('CI_Flickr');

endif; //class_exists
?>
