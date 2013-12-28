<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_contact_options', 70);
	if( !function_exists('ci_add_tab_contact_options') ):
		function ci_add_tab_contact_options($tabs)
		{
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Contact Options', 'ci_theme');
			return $tabs;
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );

	$ci_defaults['header_contact_text'] = 'Talk to us';
	$ci_defaults['header_contact_text_emph'] = '(0123) 456-7890';

	$ci_defaults['contact_addr'] = 'Pointblank Str. 14, 54242, California';
	$ci_defaults['contact_tel'] = '(0123) 456-7890';
	$ci_defaults['contact_email'] = 'your@email.com';

	$ci_defaults['disable_map'] = '';
	$ci_defaults['map_tooltip'] = 'Pointblank Str. 14, 54242, California';
	$ci_defaults['map_coords'] = '33.59,-80';
	$ci_defaults['map_zoom_level'] = '6';

?>
<?php else: ?>
	<fieldset class="set">
		<p class="guide"><?php _e('You may enable or disable the header contact details. Leaving a field empty, will disable that field from the header.', 'ci_theme');?> </p>
		<?php ci_panel_input('header_contact_text', __('Header contact text:', 'ci_theme')); ?>
		<?php ci_panel_input('header_contact_text_emph', __('Emphasized header contact text:', 'ci_theme')); ?>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('Contact Info: Here you can place the contact information of your business. They will be displayed in your contact page.', 'ci_theme');?> </p>
		<?php ci_panel_input('contact_addr', __('Address:', 'ci_theme')); ?>
		<?php ci_panel_input('contact_email', __('Email:', 'ci_theme')); ?>
		<?php ci_panel_input('contact_tel', __('Telephone No:', 'ci_theme')); ?>
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('Map Settings: Here you can customize your map settings.', 'ci_theme');?> </p>
		<?php ci_panel_input('map_coords', __('Enter the exact coordinates of your address (you can find your coordinates based on address using <a href="http://itouchmap.com/latlong.html">this tool</a>):', 'ci_theme')); ?>
		<?php ci_panel_input('map_zoom_level', __('Enter a single number from 1 to 20 that represents the default zoom level you want on your map. Higher number means closer.', 'ci_theme')); ?>
		<?php ci_panel_input('map_tooltip', __('Enter the text you wish to display when a user clicks on the map pin that points to your address (e.g. Your actual address):', 'ci_theme')); ?>
		<?php ci_panel_checkbox('disable_map', 'enabled', __('Disable the map.', 'ci_theme')); ?>
	</fieldset>

<?php endif; ?>
