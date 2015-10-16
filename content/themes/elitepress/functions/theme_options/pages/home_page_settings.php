<div class="block ui-tabs-panel active" id="option-ui-id-1" >
<?php $current_options = wp_parse_args(  get_option( 'elitepress_lite_options', array() ), theme_data_setup() );
	if(isset($_POST['webriti_settings_save_1']))
	{	
		if($_POST['webriti_settings_save_1'] == 1) 
		{
			if ( empty($_POST) || !wp_verify_nonce($_POST['webriti_gernalsetting_nonce_customization'],'webriti_customization_nonce_gernalsetting') )
			{  print 'Sorry, your nonce did not verify.';	exit; }
			else  
			{	
				$current_options['upload_image_favicon']=esc_url($_POST['upload_image_favicon']);
				$current_options['webrit_custom_css'] = wp_strip_all_tags($_POST['webrit_custom_css']);			
				if(isset($_POST['text_title']))
				{ echo $current_options['text_title']=sanitize_text_field($_POST['text_title']); } 
				else
				{ echo $current_options['text_title']="off"; } 
				
				update_option('elitepress_lite_options', stripslashes_deep($current_options));
			}
		}	
		if($_POST['webriti_settings_save_1'] == 2) 
		{	
			$current_options['upload_image_favicon']="";
			$current_options['text_title']="on";
			$current_options['webrit_custom_css']="";		
			update_option('elitepress_lite_options',$current_options);
		}
	}  ?>	
	<form method="post" id="webriti_theme_options_1">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Quick Start Settings','elitepress');?></h2></td>
				<td style="width:30%;">
					<div class="webriti_settings_loding" id="webriti_loding_1_image"></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_1_success" ><?php _e('Options data successfully Saved','elitepress');?></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_1_reset" ><?php _e('Options data successfully reset','elitepress');?></div>
				</td>
				<td style="text-align:right;">					
					<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('1');">
					<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('1')" >
				</td>
				</tr>
			</table>			
		</div>	
		<?php wp_nonce_field('webriti_customization_nonce_gernalsetting','webriti_gernalsetting_nonce_customization'); ?>
		<div class="section">
			<h3><?php _e('Custom Favicon','elitepress'); ?>
				<span class="icons help"><span class="tooltip"><?php  _e('Make sure you upload .icon image type which is not more then 25X25 px.','elitepress');?></span></span>
			</h3>
			<input class="webriti_inpute" type="text" value="<?php if($current_options['upload_image_favicon']!='') { echo esc_url($current_options['upload_image_favicon']); } ?>" id="upload_image_favicon" name="upload_image_favicon" size="36" class="upload has-file"/>
			<input type="button" id="upload_button" value="Favicon Icon" class="upload_image_button"  />			
			<?php if($current_options['upload_image_favicon']!='') { ?>
			<p><img style="height:60px;width:100px;" src="<?php  echo esc_url($current_options['upload_image_favicon']);  ?>" /></p>
			<?php } ?>
		</div>
		<div class="section">
			<h3><?php _e('Custom css','elitepress'); ?></h3>
			<textarea rows="8" cols="8" id="webrit_custom_css" name="webrit_custom_css"><?php if($current_options['webrit_custom_css']!='') { echo esc_attr($current_options['webrit_custom_css']); } ?></textarea>
			<div class="explain"><?php _e('This is a powerful feature provided here. No need to use custom css plugin, just paste your css code and see the magic.','elitepress'); ?><br></div>
		</div>		
		<div id="button_section">
			<input type="hidden" value="1" id="webriti_settings_save_1" name="webriti_settings_save_1" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('1');">
			<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('1')" >
			<!--  alert massage when data saved and reset -->
		</div>
	</form>	
</div>