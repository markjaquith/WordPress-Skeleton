<?php 
if( !class_exists('CI_Newsletter') ):
class CI_Newsletter extends WP_Widget {

	function CI_Newsletter(){
		$widget_ops = array('description' => __('Newsletter widget placeholder', 'ci_theme'), 'classname' => 'ci-newsletter');
		$control_ops = array( /*'width' => 300, 'height' => 400*/ );
		parent::WP_Widget('ci_newsletter_widget', $name='-= CI Newsletter =-', $widget_ops, $control_ops);
	}

	function widget($args, $instance) 
	{
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		
		if(ci_setting('newsletter_action')!='')
		{
			echo $before_widget;

			$email_id = ci_setting('newsletter_email_id')!='' ? ' id="'.esc_attr(ci_setting('newsletter_email_id')).'" ' : '';
			$email_name = ci_setting('newsletter_email_name')!='' ? ' name="'.esc_attr(ci_setting('newsletter_email_name')).'" ' : '';
			$name_id = ci_setting('newsletter_name_id')!='' ? ' id="'.esc_attr(ci_setting('newsletter_name_id')).'" ' : '';
			$name_name = ci_setting('newsletter_name_name')!='' ? ' name="'.esc_attr(ci_setting('newsletter_name_name')).'" ' : '';
			
			?>
			<?php if ($title) echo $before_title . $title . $after_title; ?>

			<div class="newsletter-wgt">
	
				<?php if(ci_setting('newsletter_description')!=''): ?>
					<p><?php ci_e_setting('newsletter_description'); ?></p>
				<?php endif; ?>

				<form class="newsletter-form group" action="<?php ci_e_setting('newsletter_action'); ?>">
					<?php if(!empty($name_name)): ?>
						<input 	<?php echo $name_name; ?>
								type="text" 
								placeholder="<?php echo esc_attr(ci_setting('newsletter_name_placeholder')); ?>" 
								value="" 
								<?php echo $name_id; ?>
						/>
					<?php endif; ?>
					<input 	<?php echo $email_name; ?>
							type="text" 
							placeholder="<?php echo esc_attr(ci_setting('newsletter_email_placeholder')); ?>" 
							value="" 
							<?php echo $email_id; ?>
					/>
					<input 
						class="btn" 
						type="submit" 
						id="submit"
						value="<?php ci_e_setting('newsletter_button_text'); ?>"
					/>
					<?php
						$fields = ci_setting('newsletter_hidden_fields');
						if(is_array($fields) and count($fields) > 0)
						{
							for( $i = 0; $i < count($fields); $i+=2 )
							{
								if(empty($fields[$i]))
									continue;
								echo '<input type="hidden" name="'.esc_attr($fields[$i]).'" value="'.esc_attr($fields[$i+1]).'" />';
							}
						}
					?>
				</form>

			</div>
			<?php
		
			echo $after_widget;
		}


	} // widget

	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = stripslashes($new_instance['title']);
		return $instance;
	} // save
 
	function form($instance)
	{
		if(ci_setting('newsletter_action')=='')
		{
			echo '<p><b>'.__('There is no action defined for the newsletter. Please set one from the theme\'s Panel. The form will not be displayed until you do.', 'ci_theme').'</b></p>';
		}
		$instance = wp_parse_args( (array) $instance, array('title'=>'' ) );
		$title = $instance['title'];
		echo "<p>".__('This widget is a placeholder for the Newsletter form. You may configure the newsletter form from the <a href="themes.php?page=ci_panel.php">CSSIgniter\'s panel</a>, under the <strong>Newsletter Options</strong> tab.', 'ci_theme')."</p>";
		echo '<p><label>' . __('Title:', 'ci_theme') . '</label><input id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" class="widefat" /></p>';

	} // form

} // class

register_widget('CI_Newsletter');

endif; //!class_exists
?>
