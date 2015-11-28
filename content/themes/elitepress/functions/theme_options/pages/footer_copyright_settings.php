<div class="block ui-tabs-panel " id="option-ui-id-23" >	
	<?php $current_options = wp_parse_args(  get_option( 'elitepress_lite_options', array() ), theme_data_setup() );
	if(isset($_POST['webriti_settings_save_23']))
	{	
		if($_POST['webriti_settings_save_23'] == 1) 
		{
			if ( empty($_POST) || !wp_verify_nonce($_POST['webriti_gernalsetting_nonce_customization'],'webriti_customization_nonce_gernalsetting') )
			{  printf (__('Sorry, your nonce did not verify.','elitepress'));	exit; }
			else  
			{		
				$current_options['footer_copyright_text']=wp_kses_post(balanceTags($_POST['footer_copyright_text'],true));
				// Footer menu bar Enabled or Disabled
				if(isset($_POST['footer_menu_bar_enabled']))
				{ echo $current_options['footer_menu_bar_enabled']= 'on'; } 
				else { echo $current_options['footer_menu_bar_enabled']='off'; }
				
				
				
				update_option('elitepress_lite_options', stripslashes_deep($current_options));
			}
		}	
		if($_POST['webriti_settings_save_23'] == 2) 
		{
			$current_options['footer_menu_bar_enabled'] = 'on';
			$current_options['footer_copyright_text']= __('<p>Copyright 2014 elitepress <a href="#">WordPress Theme</a>. All rights reserved</p>','elitepress');
			update_option('elitepress_lite_options',$current_options);
		}
	}  ?>
	<form method="post" id="webriti_theme_options_23">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Footer Customizations','elitepress');?></h2></td>
				<td><div class="webriti_settings_loding" id="webriti_loding_23_image"></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_23_success" ><?php _e('Options data successfully Saved','elitepress');?></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_23_reset" ><?php _e('Options data successfully reset','elitepress');?></div>
				</td>
				<td style="text-align:right;">
					<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('23');">
					<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('23')" >
				</td>
				</tr>
			</table>	
		</div>		
		<?php wp_nonce_field('webriti_customization_nonce_gernalsetting','webriti_gernalsetting_nonce_customization'); ?>
				
		<div class="section">		
			<h3><?php _e('Footer Customization text','elitepress'); ?></h3>
			<textarea rows="10" cols="50" class="webriti_inpute" name="footer_copyright_text" id="footer_copyright_text" ><?php if(isset($current_options['footer_copyright_text'])) 
			{ echo esc_attr_e($current_options['footer_copyright_text']); } ?> </textarea>
			<span class="explain"><?php  _e('Enter the Footer Customization text','elitepress');?></span>
		</div>	
		
		
		<div class="section">
			<h3><?php _e('Enable Footer Menu Bar:','elitepress'); ?>  </h3>
			<input type="checkbox" <?php if($current_options['footer_menu_bar_enabled']=='on') echo "checked='checked'"; ?> id="footer_menu_bar_enabled" name="footer_menu_bar_enabled" > <span class="explain"><?php _e('Enable Footer Menu Bar.','elitepress'); ?></span>
		</div>
		
		<div id="button_section">
			<input type="hidden" value="1" id="webriti_settings_save_23" name="webriti_settings_save_23" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('23');">
			<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('23')" >
		</div>
	</form>
</div>