<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_google_options', 100);
	if( !function_exists('ci_add_tab_google_options') ):
		function ci_add_tab_google_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Google Options', 'ci_theme');
			return $tabs; 
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );

	$ci_defaults['google_maps_api_key'] = '';

	load_panel_snippet('google_analytics');
	
?>
<?php else: ?>

	<?php load_panel_snippet('google_analytics'); ?>

	<fieldset class="set">
		<p class="guide"><?php _e('Enter here your Google Maps API Key. While your maps will be displayed at first without an API key, if you get a lot of visits to your site (more than 25.000 per day currently), the maps might stop working. In that case, you need to issue a key from <a href="https://code.google.com/apis/console/">Google Accounts</a>', 'ci_theme'); ?></p>
		<?php ci_panel_input('google_maps_api_key', __('Google Maps API Key', 'ci_theme')); ?>
	</fieldset>

<?php endif; ?>
