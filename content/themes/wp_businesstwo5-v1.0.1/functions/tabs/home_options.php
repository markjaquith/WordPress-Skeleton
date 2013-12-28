<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_homepage_options', 40);
	if( !function_exists('ci_add_tab_homepage_options') ):
		function ci_add_tab_homepage_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Homepage Options', 'ci_theme'); 
			return $tabs; 
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );

	$ci_defaults['disable_home_services']	= '';
	$ci_defaults['disable_home_clients']	= '';
	$ci_defaults['service_home_cols']	= 'four';
	$ci_defaults['client_home_cols']	= 'four';

	$ci_defaults['clients_page'] = '';
	$ci_defaults['home_clients_heading'] = 'We love our clients';
	$ci_defaults['home_clients_link_text'] = 'See the full list';

	$ci_defaults['call_to_action_enabled'] = 'enabled';
	$ci_defaults['call_to_action_heading'] = 'Contact us now and find out what we can do for you.';
	$ci_defaults['call_to_action_button'] = 'See our Services';
	$ci_defaults['call_to_action_url'] = 'http://www.cssigniter.com';
	
?>
<?php else: ?>

	<fieldset class="set">
		<p class="guide"><?php _e('Select the structure of your homepage.', 'ci_theme'); ?></p>

		<?php ci_panel_checkbox( 'disable_home_services', 'enabled', __('Disable Services Section on Homepage', 'ci_theme') ); ?>
		<?php ci_panel_checkbox( 'disable_home_clients', 'enabled', __('Disable Clients Section on Homepage', 'ci_theme') ); ?>
	</fieldset>

	<fieldset class="set">
		<p class="guide mt20"><?php _e('Select how many columns you want the services area in the frontpage to be. For example, if you have three services, you should set the number to three columns. If you have eight service items, you should set the number to four columns so that you get two rows of four service items.' , 'ci_theme'); ?></p>
		<fieldset>
			<?php 
				$options = array(
					'four' 		=> __('Four Columns', 'ci_theme'),
					'one-third' => __('Three Columns', 'ci_theme'),
					'eight' 	=> __('Two Columns', 'ci_theme')
				);
				ci_panel_dropdown('service_home_cols', $options, __('Service Homepage section columns', 'ci_theme'));
			?>
		</fieldset>
	</fieldset>

	<fieldset class="set">
		<p class="guide mt20"><?php _e('Select how many columns you want the client area in the frontpage to be. For example, if you have three clients, you should set the number to three columns. If you have eight clients you wish to display, you should set the number to four columns so that you get two rows of four client logos.' , 'ci_theme'); ?></p>
		<fieldset>
			<?php 
				$options = array(
					'four' => __('Four Columns', 'ci_theme'),
					'one-third' => __('Three Columns', 'ci_theme'),
					'eight' => __('Two Columns', 'ci_theme')
				);
				ci_panel_dropdown('client_home_cols', $options, __('Client Homepage section columns', 'ci_theme'));				
			?>
		</fieldset>
	</fieldset>

	<fieldset class="set mt20">
		<p class="guide"><?php _e('Select the "Clients" page that you have created and assigned the "Clients Page" template. This will be used automatically wherever a link to the clients listing page is needed. If no page is selected, all related links will be disabled.' , 'ci_theme'); ?></p>
		<fieldset class="mb10">
			<?php $option_name = 'clients_page'; ?>
			<label for="<?php echo $option_name; ?>"><?php _e('Select the "Clients Page"', 'ci_theme'); ?></label>
			<?php wp_dropdown_pages(array(
				'selected'=>$ci[$option_name],
				'name'=>THEME_OPTIONS.'['.$option_name.']',
				'show_option_none' => ' ',
				'show_option_none_value' => ''
			)); ?>
		</fieldset>

		<p class="guide"><?php _e('Set the text for the "Clients" section on the front page. The first is the main Clients heading, while the second is the text that will link to the Clients page that you have declared above.' , 'ci_theme'); ?></p>
		<?php ci_panel_input('home_clients_heading', __('Clients section heading', 'ci_theme')); ?>
		
		<?php ci_panel_input('home_clients_link_text', __('Clients list link text', 'ci_theme')); ?>
			
	</fieldset>

	<fieldset class="set">
		<p class="guide"><?php _e('Set the text for the "Call to action" description and button. This is urges the visitor to visit a specific page, usually a products or services page, and therefore, it is strongly recommended. For flexibility, you may set URL to redirect the user to.' , 'ci_theme'); ?></p>

		<?php ci_panel_checkbox('call_to_action_enabled', 'enabled', __('Enable the "Call to action" button', 'ci_theme')); ?>
		
		<fieldset>
			<?php ci_panel_input('call_to_action_heading', __('Call to action text', 'ci_theme')); ?>
			
			<?php ci_panel_input('call_to_action_button', __('Button text', 'ci_theme')); ?>
	
			<?php ci_panel_input('call_to_action_url', __('URL (include http://)', 'ci_theme')); ?>
		</fieldset>
	</fieldset>

	
<?php endif; ?>
