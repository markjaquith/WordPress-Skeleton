<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php

	// Defaults must be set per theme, by using the 'ci_footer_credits' and 'ci_footer_credits_secondary' filters.
	// For the footer options to appear, 'ci_footer_credits' MUST have a filter. 
	// One would expect the filters to live in /functions/template_hooks.php

	/*
		// Example default footer filter.
		add_filter('ci_footer_credits', 'ci_theme_footer_credits');
		if( !function_exists('ci_theme_footer_credits') ):
		function ci_theme_footer_credits($string){
			return 'Default footer text';
		}
		endif;
	*/
	
	$ci_defaults['ci_footer_credits'] = apply_filters('ci_footer_credits', '');
	$ci_defaults['ci_footer_credits_secondary'] = apply_filters('ci_footer_credits_secondary', '');

?>
<?php else: ?>

	<?php if( has_filter('ci_footer_credits') ): ?>

		<fieldset class="set">
			<?php $allowed_tags = apply_filters('ci_footer_allowed_tags', array('<a>','<b>','<strong>','<i>','<em>','<span>')); ?>
			<p class="guide"><?php echo apply_filters('ci_panel_footer_credits_description', sprintf(__('You can change the footer text by entering your custom text here. You may use <strong>:year:</strong> to display the current year. The following HTML tags are allowed: %s', 'ci_theme'), htmlspecialchars(implode(' ', $allowed_tags)) )); ?></p>
	
			<?php if(has_filter('ci_footer_credits')): ?>
				<?php ci_panel_textarea('ci_footer_credits', __('Footer text', 'ci_theme') ); ?>
			<?php endif; ?>
	
			<?php if(has_filter('ci_footer_credits_secondary')): ?>
				<?php ci_panel_textarea('ci_footer_credits_secondary', __('Secondary footer text', 'ci_theme') ); ?>
			<?php endif; ?>
	
		</fieldset>

	<?php endif; ?>

<?php endif; ?>
