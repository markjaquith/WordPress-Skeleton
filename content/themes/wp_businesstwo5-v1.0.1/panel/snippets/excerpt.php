<?php global $ci, $ci_defaults, $load_defaults, $content_width; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	
	$ci_defaults['preview_content'] 		= 'disabled'; //enabled means content, disabled means excerpt
	$ci_defaults['excerpt_length'] 			= 50;
	$ci_defaults['excerpt_text'] 			= '[...]';	
	$ci_defaults['read_more_text'] 			= 'Read More &raquo;';


	if( !function_exists('ci_read_more') ):
		function ci_read_more($post_id=false, $return=false)
		{
			global $post;
	
			if($post_id===false)
				$post_id = $post->ID;
	
			$link = apply_filters('ci-read-more-link', 
								'<a class="ci-more-link" href="'. get_permalink($post_id) . '"><span>' . ci_setting('read_more_text') . '</span></a>',
								ci_setting('read_more_text'),
								get_permalink($post_id) );
			
			//					
			// This is how to set the read more markup of ci_read_more() per theme.
			// You need to do it in one of the theme's file (e.g. in /functions/tabs/), not in here.
			//
			/*
			add_filter('ci-read-more-link', 'ci_theme_readmore', 10, 3);
			function ci_theme_readmore($html, $text, $link)
			{
				return '<a class="button" href="'.$link.'">'.$text.'</a>';
			}
			*/


			if($return===true)
				return $link;
			else
				echo $link;
				
		}
	endif;
	
	// Handle the excerpt.
	add_filter('excerpt_length', 'ci_excerpt_length');
	if( !function_exists('ci_excerpt_length') ):
		function ci_excerpt_length($length) {
			return ci_setting('excerpt_length');
		}
	endif;
	
	add_filter('excerpt_more', 'ci_excerpt_more');
	if( !function_exists('ci_excerpt_more') ):
		function ci_excerpt_more($more) {
			return ci_setting('excerpt_text');
		}
	endif;

	add_filter('the_content_more_link', 'ci_change_read_more');
	if( !function_exists('ci_change_read_more') ):
		function ci_change_read_more($morelink) {
			return str_replace('(more...)', ci_setting('read_more_text'), $morelink);
		}
	endif;

?>
<?php else: ?>
		
	<fieldset class="set">
		<p class="guide"><?php _e('You can select whether you want the Content or the Excerpt to be displayed on listing pages.', 'ci_theme'); ?></p>
		<label><?php _e('Use the following on listing pages', 'ci_theme'); ?></label>
		<?php ci_panel_radio('preview_content', 'use_content', 'enabled', __('Use the Content', 'ci_theme')); ?>
		<?php ci_panel_radio('preview_content', 'use_excerpt', 'disabled', __('Use the Excerpt', 'ci_theme')); ?>

		<p class="guide mt10"><?php _e('You can set what the Read More text will be. This applies to both the Content and the Excerpt.', 'ci_theme'); ?></p>
		<?php ci_panel_input('read_more_text', __('Read More text', 'ci_theme')); ?>

		<p class="guide mt10"><?php _e('You can define how long the Excerpt will be (in words). You can also set the text that appears when the excerpt is auto-generated and is automatically cut-off. These options only apply to the Excerpt.', 'ci_theme'); ?></p>
		<?php ci_panel_input('excerpt_length', __('Excerpt length (in words)', 'ci_theme')); ?>
		<?php ci_panel_input('excerpt_text', __('Excerpt auto cut-off text', 'ci_theme')); ?>
	</fieldset>
		
<?php endif; ?>
