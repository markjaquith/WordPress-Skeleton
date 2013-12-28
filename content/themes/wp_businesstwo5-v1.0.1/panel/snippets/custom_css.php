<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php

	$ci_defaults['custom_css'] = '';


	// 110 is the priority. It's important to be a big number, i.e. low priority.
	// Low priority means it will execute AFTER the other hooks, hence this will override other styles previously set.
	// Custom Background has a priority of 100, so this custom css can override the background.
	add_action('wp_head', 'ci_custom_css', 110);
	if( !function_exists('ci_custom_css') ):
		function ci_custom_css() 
		{
			global $ci;
			$css = $ci['custom_css'];	
			
			if (!empty($css)) 
			{
				$css = "<style type=\"text/css\">\n" . $css . "</style>\n";
				echo html_entity_decode($css);
			}	
		}
	endif;
?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('Paste here any custom CSS code you might have.', 'ci_theme'); ?></p>
		<?php ci_panel_textarea('custom_css', __('CSS Code', 'ci_theme')); ?>
	</fieldset>
<?php endif; ?>
