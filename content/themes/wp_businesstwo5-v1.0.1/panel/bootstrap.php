<?php
	do_action('ci_theme_before_bootstrap');

	//
	// Holds theme features by functions add_ci_theme_support() etc, similar to native add_theme_support()
	//
	$_ci_theme_features = array();
	
	// Get vital theme files.
	get_template_part('functions/overrides');
	get_template_part('panel/development');
	get_template_part('panel/generic');
	get_template_part('panel/widgets');
	get_template_part('functions/nav_menus');
	get_template_part('functions/comments');
	get_template_part('functions/sidebars');
	get_template_part('functions/scripts');
	get_template_part('functions/styles');
	get_template_part('functions/post_types');
	get_template_part('functions/template_hooks');
	get_template_part('panel/ci_panel');
	get_template_part('panel/scripts/fancybox');
	get_template_part('panel/post_color_scheme');
	get_template_part('panel/localization');


	do_action('ci_theme_bootstrap');
	

	// Handle Feeds.
	ci_register_custom_feed();



	// Set pingback URL
	add_action('wp_head', 'ci_print_pingback_url');
	if( !function_exists('ci_print_pingback_url') ):
	function ci_print_pingback_url()
	{
		?><link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" /><?php
	}
	endif;
	
	//
	// Don't move this function.
	// Writes defaults or initialises the theme options' array.
	//
	if( !function_exists('ci_default_options') ):
	function ci_default_options($assign_defaults=false)
	{
		global $ci, $ci_defaults;
		
		if ($assign_defaults==true)
		{
			$ci = wp_parse_args($ci, $ci_defaults);
			update_option(THEME_OPTIONS, $ci);
		}
		else
		{
			// If there are unset options, just set them to empty string to avoid warnings, such as, index not found.
			foreach ($ci_defaults as $name=>$value)
			{
				if(!isset($ci[$name]))
					$ci[$name]='';
			}
		}
		
	}
	endif;


	//
	// If just activated, save default theme options and go to our options page.
	//
	global $pagenow;
	if ( is_admin() and isset($_GET['activated'] ) and $pagenow == "themes.php" )
	{
		ci_default_options(true);
		flush_rewrite_rules();

		add_action('init', 'ci_theme_activated');

		wp_redirect( 'themes.php?page=ci_panel.php' );
	}
	
	if( !function_exists('ci_theme_activated') ):
	function ci_theme_activated()
	{
		// For some reason, the do_action doesn't work properly if placed directly
		// above wp_redirect( 'themes.php?page=ci_panel.php' );
		// So this function hooked on init is necessary.
		do_action('ci_theme_activated');
	}
	endif;

	//
	// Make sure the theme options array has no undefined options.
	//
	add_action('after_setup_theme', 'ci_default_fields_set');
	if( !function_exists('ci_default_fields_set') ):
	function ci_default_fields_set() 
	{ 
		ci_default_options(false); 
	}
	endif;

	do_action('ci_theme_after_bootstrap');
?>
