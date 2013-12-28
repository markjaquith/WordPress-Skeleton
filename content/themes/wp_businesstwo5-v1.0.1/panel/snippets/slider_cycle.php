<?php global $ci, $ci_defaults, $load_defaults, $content_width; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	
	$ci_defaults['slider_autoslide'] 	= 'enabled';
	$ci_defaults['slider_timeout'] 		= 3000;
	$ci_defaults['slider_speed'] 		= 500;
	$ci_defaults['slider_effect'] 		= 'scrollRight';
	$ci_defaults['slider_sync'] 		= 'enabled';

?>
<?php else: ?>

	<fieldset class="set">
		<p class="guide"><?php _e('The following options control the main slider. You may enable auto-sliding by checking the appropriate option, or by setting the auto-slide timeout to a value grater than 0. A demo of the transition effects can be seen <a href="http://jquery.malsup.com/cycle/browser.html">here</a>.' , 'ci_theme'); ?></p>
		<fieldset>
			<?php ci_panel_checkbox('slider_autoslide', 'enabled', __('Enable auto-slide', 'ci_theme')); ?>
		</fieldset>
		<fieldset>
			<?php ci_panel_input('slider_timeout', __('Auto-slide timeout (milliseconds)', 'ci_theme')); ?>
		</fieldset>
		<fieldset>
			<?php 
				$slider_effects = array(
					'none' => _x('None', 'slider effect', 'ci_theme'),
					'cover' => _x('Cover', 'slider effect', 'ci_theme'),
					'uncover' => _x('Uncover', 'slider effect', 'ci_theme'),
					'fade' => _x('Fade', 'slider effect', 'ci_theme'),
					'fadeZoom' => _x('Fade Zoom', 'slider effect', 'ci_theme'),
					'shuffle' => _x('Shuffle', 'slider effect', 'ci_theme'),
					'toss' => _x('Toss', 'slider effect', 'ci_theme'),
					'wipe' => _x('Wipe', 'slider effect', 'ci_theme'),
					'zoom' => _x('Zoom', 'slider effect', 'ci_theme'),
					'scrollVert' => _x('Scroll Vertically', 'slider effect', 'ci_theme'),
					'scrollHorz' => _x('Scroll Horizontally', 'slider effect', 'ci_theme'),
					'scrollLeft' => _x('Scroll Left', 'slider effect', 'ci_theme'),
					'scrollRight' => _x('Scroll Right', 'slider effect', 'ci_theme'),
					'scrollUp' => _x('Scroll Up', 'slider effect', 'ci_theme'),
					'scrollDown' => _x('Scroll Down', 'slider effect', 'ci_theme'),
					'blindX' => _x('Blind X', 'slider effect', 'ci_theme'),
					'blindY' => _x('Blind Y', 'slider effect', 'ci_theme'),
					'blindZ' => _x('Blind Z', 'slider effect', 'ci_theme'),
					'curtainX' => _x('Curtain X', 'slider effect', 'ci_theme'),
					'curtainY' => _x('Curtain Y', 'slider effect', 'ci_theme'),
					'growX' => _x('Grow X', 'slider effect', 'ci_theme'),
					'growY' => _x('Grow Y', 'slider effect', 'ci_theme'),
					'slideX' => _x('Slide X', 'slider effect', 'ci_theme'),
					'slideY' => _x('Slide Y', 'slider effect', 'ci_theme'),
					'turnUp' => _x('Turn Up', 'slider effect', 'ci_theme'),
					'turnDown' => _x('Turn Down', 'slider effect', 'ci_theme'),
					'turnLeft' => _x('Turn Left', 'slider effect', 'ci_theme'),
					'turnRight' => _x('Turn Right', 'slider effect', 'ci_theme')
				);
				ci_panel_dropdown('slider_effect', $slider_effects, __('Slider Effect', 'ci_theme'));
			?>
		</fieldset>
		<fieldset>
			<?php ci_panel_input('slider_speed', __('Slideshow speed in milliseconds (smaller number means faster)', 'ci_theme')); ?>
		</fieldset>
		<fieldset>
			<?php ci_panel_checkbox('slider_sync', 'enabled', __('Enable synchronized sliding', 'ci_theme')); ?>
		</fieldset>
	</fieldset>
		
<?php endif; ?>
