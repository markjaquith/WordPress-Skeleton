<?php global $ci, $ci_defaults, $load_defaults, $content_width; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_display_options', 20);
	if( !function_exists('ci_add_tab_display_options') ):
		function ci_add_tab_display_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Display Options', 'ci_theme');
			return $tabs; 
		}
	endif;
	
	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );
	load_panel_snippet('excerpt');
	load_panel_snippet('seo');
	load_panel_snippet('comments');

	load_panel_snippet('slider_flexslider');

	load_panel_snippet('featured_image_single');

	// Set our full width template width and options.
	add_filter('ci_full_template_width', 'ci_fullwidth_width');
	if( !function_exists('ci_fullwidth_width') ):
	function ci_fullwidth_width()
	{ 
		return 940;
	}
	endif;
	load_panel_snippet('featured_image_fullwidth');

?>
<?php else: ?>

	<?php load_panel_snippet('slider_flexslider'); ?>

	<?php load_panel_snippet('excerpt'); ?>	

	<?php load_panel_snippet('seo'); ?>	

	<?php load_panel_snippet('comments'); ?>	

	<?php load_panel_snippet('featured_image_single'); ?>

	<?php load_panel_snippet('featured_image_fullwidth'); ?>

	<?php endif; ?>
