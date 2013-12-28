<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_site_options', 10);
	if( !function_exists('ci_add_tab_site_options') ):
		function ci_add_tab_site_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Site Options', 'ci_theme');
			return $tabs; 
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );

	$ci_defaults['cpt_work_enabled'] = 'enabled';
	$ci_defaults['cpt_product_enabled'] = 'enabled';

	load_panel_snippet('logo');
	load_panel_snippet('favicon');
	load_panel_snippet('touch_favicon');
	load_panel_snippet('footer_text');

	//
	// Functions for NOT loading specific custom post types.
	//
	if(ci_setting('cpt_work_enabled')!='enabled')
	{
		add_filter('load_custom_post_type_files', 'ci_unload_work_cpt');
		if( !function_exists('ci_unload_work_cpt') ):
		function ci_unload_work_cpt($post_types)
		{
			$found = array_search('functions/post_types/work', $post_types);
			if($found!==false)
			{
				unset($post_types[$found]);
			}
			return $post_types;
		}
		endif;
	}

	if(ci_setting('cpt_product_enabled')!='enabled')
	{
		add_filter('load_custom_post_type_files', 'ci_unload_product_cpt');
		if( !function_exists('ci_unload_product_cpt') ):
		function ci_unload_product_cpt($post_types)
		{
			$found = array_search('functions/post_types/product', $post_types);
			if($found!==false)
			{
				unset($post_types[$found]);
			}
			return $post_types;
		}
		endif;
	}

?>
<?php else: ?>
	
	<?php load_panel_snippet('logo'); ?>
	
	<?php load_panel_snippet('favicon'); ?>

	<?php load_panel_snippet('touch_favicon'); ?>

	<?php load_panel_snippet('footer_text'); ?>

	<?php load_panel_snippet('sample_content'); ?>

	<fieldset class="set">

		<p class="guide"><?php _e('You may enable or disable the <b>Work</b> and <b>Product</b> post types, as they are quite similar, but they may or may not be both needed. Unchecking a post type will prevent the theme loading it completely, so you might experience some issues if there are dependencies on it. For example, if you import the sample content (which includes sample content for both post types) you might get an error importing posts for the specific post type. Make sure you enable both of them while importing the sample content.', 'ci_theme'); ?></p>
		
		<?php ci_panel_checkbox('cpt_work_enabled', 'enabled', __('Enable "Work" post type', 'ci_theme')); ?>
	
		<?php ci_panel_checkbox('cpt_product_enabled', 'enabled', __('Enable "Product" post type', 'ci_theme')); ?>

	</fieldset>
<?php endif; ?>
