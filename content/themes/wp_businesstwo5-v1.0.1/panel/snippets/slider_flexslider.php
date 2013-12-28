<?php global $ci, $ci_defaults, $load_defaults, $content_width; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	
	$ci_defaults['slider_autoslide'] 	= 'enabled';
	$ci_defaults['slider_effect'] 		= 'fade';
	$ci_defaults['slider_direction'] 	= 'horizontal';
	$ci_defaults['slider_speed'] 		= 3000;
	$ci_defaults['slider_duration']		= 600;

?>
<?php else: ?>
		
	<fieldset class="set">
		<p class="guide"><?php _e('The following options control the main slider. You may enable or disable auto-sliding by checking the appropriate option and further control its behavior.' , 'ci_theme'); ?></p>
		<fieldset>
			<?php ci_panel_checkbox('slider_autoslide', 'enabled', __('Enable auto-slide', 'ci_theme')); ?>
		</fieldset>
		<fieldset>
			<?php 
				$slider_effects = array(
					'fade' => _x('Fade', 'slider effect', 'ci_theme'),
					'slide' => _x('Slide','slider effect', 'ci_theme')
				);
				ci_panel_dropdown('slider_effect', $slider_effects, __('Slider Effect', 'ci_theme'));
			?>
		</fieldset>
		<fieldset>
			<?php 
				$slider_direction = array(
					'horizontal' => _x('Horizontal', 'slider direction', 'ci_theme'),
					'vertical' => _x('Vertical','slider direction', 'ci_theme')
				);
				ci_panel_dropdown('slider_direction', $slider_direction, __('Slide Direction (only for <b>Slide</b> effect)', 'ci_theme'));
			?>
		</fieldset>
		<fieldset>
			<?php ci_panel_input('slider_speed', __('Slideshow speed in milliseconds (smaller number means faster)', 'ci_theme')); ?>
		</fieldset>
		<fieldset>
			<?php ci_panel_input('slider_duration', __('Animation duration in milliseconds (smaller number means faster)', 'ci_theme')); ?>
		</fieldset>
	</fieldset>
		
<?php endif; ?>
