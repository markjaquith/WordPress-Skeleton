<div class="block ui-tabs-panel " id="option-ui-id-9" >	
	<?php $current_options = wp_parse_args(  get_option( 'elitepress_lite_options', array() ), theme_data_setup() );
	if(isset($_POST['webriti_settings_save_9']))
	{	
		if($_POST['webriti_settings_save_9'] == 1) 
		{
			if ( empty($_POST) || !wp_verify_nonce($_POST['webriti_gernalsetting_nonce_customization'],'webriti_customization_nonce_gernalsetting') )
			{  print 'Sorry, your nonce did not verify.';	exit; }
			else
			{
				$current_options['header_call_out_title'] = sanitize_text_field($_POST['header_call_out_title']);
				$current_options['header_call_out_description'] = sanitize_text_field($_POST['header_call_out_description']);
				$current_options['header_call_out_btn_text'] = sanitize_text_field($_POST['header_call_out_btn_text']);	
				$current_options['header_call_out_btn_link']=esc_url_raw($_POST['header_call_out_btn_link']);
				// Call Out Area Button Link Target
				if(isset($_POST['header_call_out_btn_link_target']))
				{ echo $current_options['header_call_out_btn_link_target']="on"; } 
				else
				{ echo $current_options['header_call_out_btn_link_target']="off"; } 
				// Call Out Area Enabled on About us or Service Section or Front Page
				if(isset($_POST['header_call_out_area_enabled']))
				{ echo $current_options['header_call_out_area_enabled']= "on"; } 
				else { echo $current_options['header_call_out_area_enabled']="off"; }
				
				update_option('elitepress_lite_options', stripslashes_deep($current_options));
			}
		}	
		if($_POST['webriti_settings_save_9'] == 2) 
		{	
			$current_options['header_call_out_area_enabled'] = 'on';
			$current_options['header_call_out_title']=__('Want to say Hey or find out more?','elitepress');
			$current_options['header_call_out_description']= __('Reprehen derit in voluptate velit cillum dolore eu fugiat nulla pariaturs sint occaecat cupid non proidentse.','elitepress');
			$current_options['header_call_out_btn_text']= __('Buy It Now!','elitepress');
			$current_options['header_call_out_btn_link']="";	
			$current_options['header_call_out_btn_link_target']= 'on';					
			update_option('elitepress_lite_options',$current_options);
		}
	}  ?>
	<form method="post" id="webriti_theme_options_9">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Header Top Call Out Area','elitepress');?></h2></td>
				<td><div class="webriti_settings_loding" id="webriti_loding_9_image"></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_9_success" ><?php _e('Options data successfully Saved','elitepress');?></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_9_reset" ><?php _e('Options data successfully reset','elitepress');?></div>
				</td>
				<td style="text-align:right;">
					<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('9');">
					<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('9')" >
				</td>
				</tr>
			</table>	
		</div>		
		<?php wp_nonce_field('webriti_customization_nonce_gernalsetting','webriti_gernalsetting_nonce_customization'); ?>
		<div class="section">
			<h3><?php _e('Enable Header Top Call Out Area Section :','elitepress'); ?>  </h3>
			<input type="checkbox" <?php if($current_options['header_call_out_area_enabled']=='on') echo "checked='checked'"; ?> id="header_call_out_area_enabled" name="header_call_out_area_enabled" > <span class="explain"><?php _e('Enable header top call out area on front page.','elitepress'); ?></span>
		</div>
		
		<div class="section">		
			<h3><?php _e('Header Top Call Out Title','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="header_call_out_title" id="header_call_out_title" placeholder="Enter http://example.com"  value="<?php if(isset($current_options['header_call_out_title'])) { echo $current_options['header_call_out_title']; } ?>">
			<span class="explain"><?php _e('Enter the header top call out title.','elitepress'); ?></span>
		</div>
		
		<div class="section">		
			<h3><?php _e('Header Top Call Out Description','elitepress'); ?></h3>
			<textarea rows="4" cols="8" id="header_call_out_description" name="header_call_out_description"><?php if($current_options['header_call_out_description']!='') { echo esc_attr($current_options['header_call_out_description']); } ?></textarea>
			<span class="explain"><?php _e('Enter the header top call out description.','elitepress'); ?></span>
		</div>
		
		<div class="section">		
			<h3><?php _e('Header Top Call Out Button Text','elitepress'); ?></h3>
			<input class="webriti_inpute"  type="text" name="header_call_out_btn_text" id="header_call_out_btn_text" placeholder="Enter http://example.com"  value="<?php if(isset($current_options['header_call_out_btn_text'])) { echo $current_options['header_call_out_btn_text']; } ?>">
			<span class="explain"><?php _e('Enter the header call out button text.','elitepress'); ?></span>
		</div>

		<div class="section">	
		<h3><?php _e('Header Top Call Out Button Link','elitepress'); ?></h3>			
			<input class="webriti_inpute"  type="text" name="header_call_out_btn_link" id="header_call_out_btn_link" placeholder="Enter http://example.com"  value="<?php if(isset($current_options['header_call_out_btn_link'])) { echo $current_options['header_call_out_btn_link']; } ?>" >
			<span class="explain"><?php _e('Enter the header top call out button link.','elitepress'); ?></span>
			<p><input type="checkbox" id="header_call_out_btn_link_target" name="header_call_out_btn_link_target" <?php if($current_options['header_call_out_btn_link_target']=='on') echo "checked='checked'"; ?> > <?php _e('Open link in a new window/tab','elitepress'); ?></p>
		</div>		
		<div id="button_section">
			<input type="hidden" value="1" id="webriti_settings_save_9" name="webriti_settings_save_9" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('9');">
			<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('9')" >
		</div>
	</form>
</div>