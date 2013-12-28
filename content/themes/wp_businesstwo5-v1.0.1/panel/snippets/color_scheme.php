<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php

	$ci_defaults['stylesheet'] ='default.css';

?>
<?php else: ?>

	<?php
		$schemes = array();
	
		$path = '';
		if( is_child_theme() and file_exists(get_stylesheet_directory().'/colors') ) {
			$path = get_stylesheet_directory().'/colors';
		}
		elseif( file_exists(get_template_directory().'/colors') ) {
			$path = get_template_directory().'/colors';
		}
	
		$path = apply_filters('ci_color_schemes_directory', $path);
		
		if(!empty($path) and is_readable($path))
		{
			if ($handle = opendir($path)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						$file_info = pathinfo($path.'/'.$file);
						if(!empty($file_info['extension']) and $file_info['extension']=='css')
						{
							$schemes[$file] = $file;
						}
					}
				}
				closedir($handle);
			}
		}
	?>

	<fieldset class="set">
		<p class="guide"><?php _e('Select your color scheme. This affects the overall look and feel of your website.', 'ci_theme'); ?></p>
		<?php 

			// Try to retain old settings where the stylesheet didn't include the extension .css
			if(!empty($ci['stylesheet']))
			{
				$color = $ci['stylesheet'];
				if(substr_right($ci['stylesheet'], 4)!='.css')
				{
					$ci['stylesheet'] = $ci['stylesheet'] . '.css';
				}
			}

			ci_panel_dropdown('stylesheet', $schemes, __('Color scheme', 'ci_theme')); 
		?>
	</fieldset>

<?php endif; ?>
