<div class="block ui-tabs-panel " id="option-ui-id-3" >	
	<?php $current_options = wp_parse_args(  get_option( 'elitepress_lite_options', array() ), theme_data_setup() );
	if(isset($_POST['webriti_settings_save_3']))
	{	
		if($_POST['webriti_settings_save_3'] == 1) 
		{
			if ( empty($_POST) || !wp_verify_nonce($_POST['webriti_gernalsetting_nonce_customization'],'webriti_customization_nonce_gernalsetting') )
			{  print 'Sorry, your nonce did not verify.';	exit; }
			else  
			{
			$current_options['service_title']=sanitize_text_field($_POST['service_title']);
			$current_options['service_description']=sanitize_text_field($_POST['service_description']);
			
			$current_options['service_one_title'] = sanitize_text_field($_POST['service_one_title']);
			$current_options['service_one_description'] = sanitize_text_field($_POST['service_one_description']);
			$current_options['service_one_icon'] = sanitize_text_field($_POST['service_one_icon']);
			
			$current_options['service_two_title'] = sanitize_text_field($_POST['service_two_title']);
			$current_options['service_two_description'] = sanitize_text_field($_POST['service_two_description']);
			$current_options['service_two_icon'] = sanitize_text_field($_POST['service_two_icon']);
			
			$current_options['service_three_title'] = sanitize_text_field($_POST['service_three_title']);
			$current_options['service_three_description'] = sanitize_text_field($_POST['service_three_description']);
			$current_options['service_three_icon'] = sanitize_text_field($_POST['service_three_icon']);
			
			$current_options['service_four_title'] = sanitize_text_field($_POST['service_four_title']);
			$current_options['service_four_description'] = sanitize_text_field($_POST['service_four_description']);
			$current_options['service_four_icon'] = sanitize_text_field($_POST['service_four_icon']);
			
			// service section enabled yes ya on
			if(isset($_POST['service_section_enabled']))
			{ echo $current_options['service_section_enabled']="on"; } 
			else { echo $current_options['service_section_enabled']="off"; } 
				
			update_option('elitepress_lite_options', stripslashes_deep($current_options));
			}
		}	
		if($_POST['webriti_settings_save_3'] == 2) 
		{
			// Other Service Section in Service Template
			$current_options['service_section_enabled']="on";
			
			$current_options['service_title']=__('Our Services', 'elitepress');	
			$current_options['service_description']=__('Duis aute irure dolor in reprehenderit in voluptate velit cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupid non proident, sunt in culpa qui officia deserunt mollit anim id est laborum..', 'elitepress');
			
			$current_options['service_one_title'] = __('Responsive Design', 'elitepress');
			$current_options['service_one_description'] = __('Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet ferment etiam porta sem malesuada magna mollis.', 'elitepress');
			$current_options['service_one_icon'] = 'fa fa-shield';
			
			$current_options['service_two_title'] = __('Twitter Bootstrap 3.2.0', 'elitepress');
			$current_options['service_two_description'] = __('Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet ferment etiam porta sem malesuada magna mollis.', 'elitepress');
			$current_options['service_two_icon'] = 'fa fa-tablet';
			
			$current_options['service_three_title'] = __('Exclusive Support', 'elitepress');
			$current_options['service_three_description'] = __('Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet ferment etiam porta sem malesuada magna mollis.', 'elitepress');
			$current_options['service_three_icon'] = 'fa fa-edit';
			
			$current_options['service_four_title'] = __('Incredibly Flexible', 'elitepress');
			$current_options['service_four_description'] = __('Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet ferment etiam porta sem malesuada magna mollis.', 'elitepress');
			$current_options['service_four_icon'] = 'fa fa-star-half-o';
			
			update_option('elitepress_lite_options',$current_options);
		}
	}  ?>
	<form method="post" id="webriti_theme_options_3">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Service Settings','elitepress');?></h2></td>
				<td><div class="webriti_settings_loding" id="webriti_loding_3_image"></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_3_success" ><?php _e('Options data successfully Saved','elitepress');?></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_3_reset" ><?php _e('Options data successfully reset','elitepress');?></div>
				</td>
				<td style="text-align:right;">
					<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('3');">
					<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('3')" >
				</td>
				</tr>
			</table>	
		</div>		
		<?php wp_nonce_field('webriti_customization_nonce_gernalsetting','webriti_gernalsetting_nonce_customization'); ?>
		
		<div class="section">
			<h3><?php _e('Enable Home Service Section','elitepress'); ?>  </h3>
			<input type="checkbox" <?php if($current_options['service_section_enabled']=='on') echo "checked='checked'"; ?> id="service_section_enabled" name="service_section_enabled">
			<span class="explain"><?php _e('Enable service on front page.','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Service section Heading on Home page','elitepress');?></h3>
			<input class="webriti_inpute"  type="text" name="service_title" id="service_title" value="<?php if($current_options['service_title']!='') { echo esc_attr($current_options['service_title']); } ?>" >		
			<span class="explain"><?php  _e('Enter Service Heading for Service Section.','elitepress');?></span>
		</div>
		<div class="section">
			<h3><?php _e('Service section Description on Home page','elitepress');?></h3>
			<textarea rows="3" cols="8" id="service_description" name="service_description"><?php if($current_options['service_description']!='') { echo esc_attr($current_options['service_description']); } ?></textarea>		
			<span class="explain"><?php  _e('Enter Service Description for Service Section.','elitepress');?></span>
		</div>
		
		<div class="section">		
			<h3><?php _e('Service One Settings','elitepress'); ?></h3>
		</div>
		<div class="section">	
		<h3><?php _e('Service Icon One ','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="service_one_icon" id="service_one_icon" value="<?php echo $current_options['service_one_icon']; ?>" >
			<span class="explain"><?php _e('Enter the service icon.','elitepress'); ?></span>
		</div>
		
		<div class="section">
			<h3><?php _e('Service One Title','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="service_one_title" id="service_one_title" value="<?php if($current_options['service_one_title']!='') { echo esc_attr($current_options['service_one_title']); } ?>" >
			<span class="explain"><?php _e('Enter the service one title.','elitepress'); ?></span>
		</div>
		<div class="section">	
		<h3><?php _e('Service one Description','elitepress'); ?></h3>
			<textarea rows="3" cols="8" id="service_one_description" name="service_one_description"><?php if($current_options['service_one_description']!='') { echo esc_attr($current_options['service_one_description']); } ?></textarea>
			<span class="explain"><?php _e('Enter the service one description.','elitepress'); ?></span>
		</div>
		<div class="section">		
			<h3><?php _e('Service Two Settings','elitepress'); ?></h3>
		</div>
		<div class="section">	
		<h3><?php _e('Service Icon Two ','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="service_two_icon" id="service_two_icon" value="<?php echo $current_options['service_two_icon']; ?>" >
			<span class="explain"><?php _e('Enter the service icon.','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Service Two Title','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="service_two_title" id="service_two_title" value="<?php if($current_options['service_two_title']!='') { echo esc_attr($current_options['service_two_title']); } ?>" >
			<span class="explain"><?php _e('Enter the service two title.','elitepress'); ?></span>
		</div>
		<div class="section">	
		<h3><?php _e('Service Two Description','elitepress'); ?></h3>
			<textarea rows="3" cols="8" id="service_two_description" name="service_two_description"><?php if($current_options['service_two_description']!='') { echo esc_attr($current_options['service_two_description']); } ?></textarea>
			<span class="explain"><?php _e('Enter the service two description.','elitepress'); ?></span>
		</div>
		<div class="section">		
			<h3><?php _e('Service Three Settings','elitepress'); ?></h3>
		</div>
		<div class="section">	
		<h3><?php _e('Service Icon Three ','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="service_three_icon" id="service_three_icon" value="<?php echo $current_options['service_three_icon']; ?>" >
			<span class="explain"><?php _e('Enter the service icon.','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Service Three Title','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="service_three_title" id="service_three_title" value="<?php if($current_options['service_three_title']!='') { echo esc_attr($current_options['service_three_title']); } ?>" >
			<span class="explain"><?php _e('Enter the service three title.','elitepress'); ?></span>
		</div>
		<div class="section">	
		<h3><?php _e('Service Three Description','elitepress'); ?></h3>
			<textarea rows="3" cols="8" id="service_three_description" name="service_three_description"><?php if($current_options['service_three_description']!='') { echo esc_attr($current_options['service_three_description']); } ?></textarea>
			<span class="explain"><?php _e('Enter the service three description.','elitepress'); ?></span>
		</div>
		<div class="section">		
			<h3><?php _e('Service Four Settings','elitepress'); ?></h3>
		</div>
		<div class="section">	
		<h3><?php _e('Service Icon Four ','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="service_four_icon" id="service_four_icon" value="<?php echo $current_options['service_four_icon']; ?>" >
			<span class="explain"><?php _e('Enter the service icon.','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Service Four Title','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="service_four_title" id="service_four_title" value="<?php if($current_options['service_four_title']!='') { echo esc_attr($current_options['service_four_title']); } ?>" >
			<span class="explain"><?php _e('Enter the service four title.','elitepress'); ?></span>
		</div>
		<div class="section">	
		<h3><?php _e('Service Four Description','elitepress'); ?></h3>
			<textarea rows="3" cols="8" id="service_four_description" name="service_four_description"><?php if($current_options['service_four_description']!='') { echo esc_attr($current_options['service_four_description']); } ?></textarea>
			<span class="explain"><?php _e('Enter the service four description.','elitepress'); ?></span>
		</div>

		<div id="button_section">
			<input type="hidden" value="1" id="webriti_settings_save_3" name="webriti_settings_save_3" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('3');">
			<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('3')" >
		</div>
	</form>
</div>