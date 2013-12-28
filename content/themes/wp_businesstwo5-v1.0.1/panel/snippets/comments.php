<?php global $ci, $ci_defaults, $load_defaults, $content_width; ?>
<?php if ($load_defaults===TRUE): ?>
<?php

	$ci_defaults['comments_off_message']	= 'enabled';
	$ci_defaults['pages_comments_off']	= 'disabled';

	add_filter('comments_open', 'ci_disable_pages_comments', 10, 2);
	if( !function_exists('ci_disable_pages_comments') ):
	function ci_disable_pages_comments($comments_open, $post_id)
	{
		if(is_page($post_id))
		{
			if(ci_setting('pages_comments_off')=='enabled')
			{
				return false;
			}
			else
			{
				return $comments_open;
			}
		}
		else
		{
			return $comments_open;
		}
	}
	endif;	
	
?>
<?php else: ?>
		
	<fieldset class="set">
		<p class="guide"><?php _e('You can disable comments specifically for <b>Pages</b>. This is usually preferred as pages traditionally hold static, presentational content. When checked, <b>all</b> pages will <b>not</b> have comments, overriding each page\'s setting. <br />When You can also enable and disable the "Comments are closed" message displayed on the bottom of each post/page/etc when the comments are closed. This applies everywhere.' , 'ci_theme'); ?></p>
		<fieldset>
			<?php ci_panel_checkbox('pages_comments_off', 'enabled', __('Disable comments for pages', 'ci_theme')); ?>
			<?php ci_panel_checkbox('comments_off_message', 'enabled', __('Show "Comments are closed" message', 'ci_theme')); ?>
		</fieldset>
	</fieldset>
		
<?php endif; ?>
