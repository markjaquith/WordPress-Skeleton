 <div class="block ui-tabs-panel " id="option-ui-id-22" >	
	<?php $current_options = wp_parse_args(  get_option( 'elitepress_lite_options', array() ), theme_data_setup() );
	if(isset($_POST['webriti_settings_save_22']))
	{	
		if($_POST['webriti_settings_save_22'] == 1) 
		{
			if ( empty($_POST) || !wp_verify_nonce($_POST['webriti_gernalsetting_nonce_customization'],'webriti_customization_nonce_gernalsetting') )
			{  print 'Sorry, your nonce did not verify.';	exit; }
			else  
			{
				$current_options['social_media_facebook_link']=esc_url_raw($_POST['social_media_facebook_link']);
				$current_options['social_media_twitter_link']=esc_url_raw($_POST['social_media_twitter_link']);
				$current_options['social_media_googleplus_link']=esc_url_raw($_POST['social_media_googleplus_link']);
				$current_options['social_media_linkedin_link']=esc_url_raw($_POST['social_media_linkedin_link']);
				$current_options['social_media_skype_link']=esc_url_raw($_POST['social_media_skype_link']);
				$current_options['social_media_dribbble_link']=esc_url_raw($_POST['social_media_dribbble_link']);
				$current_options['social_media_youtube_link']=esc_url_raw($_POST['social_media_youtube_link']);
				$current_options['social_media_vimeo_link']=esc_url_raw($_POST['social_media_vimeo_link']);
				$current_options['social_media_pagelines_link']=esc_url_raw($_POST['social_media_pagelines_link']);
				
				
				// social media on header section in front page
				if(isset($_POST['header_social_media_enabled']))
				{ echo $current_options['header_social_media_enabled']="on"; } 
				else { echo $current_options['header_social_media_enabled']="off"; }
				
				// facebook media open in new tab
				if(isset($_POST['facebook_media_enabled']))
				{ echo $current_options['facebook_media_enabled']="on"; } 
				else { echo $current_options['facebook_media_enabled']="off"; }
				// twitter media open in new tab
				if(isset($_POST['twitter_media_enabled']))
				{ echo $current_options['twitter_media_enabled']= "on"; } 
				else { echo $current_options['twitter_media_enabled']="off"; }
				// googleplus media open in new tab
				if(isset($_POST['googleplus_media_enabled']))
				{ echo $current_options['googleplus_media_enabled']= "on"; } 
				else { echo $current_options['googleplus_media_enabled']="off"; }
				// linkedin media open in new tab
				if(isset($_POST['linkedin_media_enabled']))
				{ echo $current_options['linkedin_media_enabled']= "on"; } 
				else { echo $current_options['linkedin_media_enabled']="off"; }
				// skype media open in new tab
				if(isset($_POST['skype_media_enabled']))
				{ echo $current_options['skype_media_enabled']= "on"; } 
				else { echo $current_options['skype_media_enabled']="off"; }
				// dribbble media open in new tab
				if(isset($_POST['dribbble_media_enabled']))
				{ echo $current_options['dribbble_media_enabled']= "on"; } 
				else { echo $current_options['dribbble_media_enabled']="off"; }
				// youtube media open in new tab
				if(isset($_POST['youtube_media_enabled']))
				{ echo $current_options['youtube_media_enabled']= "on"; } 
				else { echo $current_options['youtube_media_enabled']="off"; }
				// vimeo media open in new tab
				if(isset($_POST['vimeo_media_enabled']))
				{ echo $current_options['vimeo_media_enabled']= "on"; } 
				else { echo $current_options['vimeo_media_enabled']="off"; }
				// pagelines media open in new tab
				if(isset($_POST['pagelines_media_enabled']))
				{ echo $current_options['pagelines_media_enabled']= "on"; } 
				else { echo $current_options['pagelines_media_enabled']="off"; }
				
				
				// Contact Section Enable in Header page
				if(isset($_POST['contact_address_settings']))
				{ echo $current_options['contact_address_settings']= "on"; } 
				else { echo $current_options['contact_address_settings']="off"; }
				
				//Contact section 
				$current_options['contact_email']=sanitize_text_field($_POST['contact_email']);
				$current_options['contact_phone_number']=sanitize_text_field($_POST['contact_phone_number']);
				
				
				// Logo Section Enable in header page
				if(isset($_POST['logo_section_settings']))
				{ echo $current_options['logo_section_settings']= "on"; } 
				else { echo $current_options['logo_section_settings']="off"; }
				
				
				//Logo section 
				$current_options['upload_image_logo']=sanitize_text_field($_POST['upload_image_logo']);	$current_options['height']=sanitize_text_field($_POST['height']);
				$current_options['width']=sanitize_text_field($_POST['width']);
				if(isset($_POST['text_title']))
				{ echo $current_options['text_title']="on"; } 
				else
				{ echo $current_options['text_title']="off"; }
				
				
				//Search bar Enable in header page
				if(isset($_POST['header_search_bar_enabled']))
				{ echo $current_options['header_search_bar_enabled']="on"; } 
				else { echo $current_options['header_search_bar_enabled']="off"; }
				
				
				
				
				update_option('elitepress_lite_options', stripslashes_deep($current_options));
			}
		}	
		if($_POST['webriti_settings_save_22'] == 2) 
		{	
			
			// social media on header section
			$current_options['header_social_media_enabled']="on";
			$current_options['facebook_media_enabled']="on";
			$current_options['twitter_media_enabled']="on";
			$current_options['googleplus_media_enabled']="on";
			$current_options['linkedin_media_enabled']="on";
			$current_options['skype_media_enabled']="on";
			$current_options['dribbble_media_enabled']="on";
			$current_options['youtube_media_enabled']="on";
			$current_options['vimeo_media_enabled']="on";
			$current_options['pagelines_media_enabled']="on";
			
			$current_options['social_media_facebook_link']="#";
			$current_options['social_media_twitter_link']="#";
			$current_options['social_media_googleplus_link']="#";
			$current_options['social_media_linkedin_link']="#";
			$current_options['social_media_skype_link']="#";
			$current_options['social_media_dribbble_link']="#";
			$current_options['social_media_youtube_link']="#";
			$current_options['social_media_vimeo_link']="#";
			$current_options['social_media_pagelines_link']="#";
			
			//Contact Us Address Settings
			$current_options['contact_address_settings']= 'on';
			
			$current_options['contact_email']= __('info@elitepresstheme.com','elitepress');
			$current_options['contact_phone_number']= __('+48-0987-654-321','elitepress');
			
			
			//header logo setting 
			$current_options['logo_section_settings']= 'on';
			
			$current_options['upload_image_logo']="";
			$current_options['height']=50;
			$current_options['width']=250;
			$current_options['text_title']="on";
			
			//Header Search bar setting
			$current_options['header_search_bar_enabled']= 'on';
			
			
			update_option('elitepress_lite_options',$current_options);
		}
	}  ?>
	<form method="post" id="webriti_theme_options_22">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Social Media Links Settings','elitepress');?></h2></td>
				<td><div class="webriti_settings_loding" id="webriti_loding_22_image"></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_22_success" ><?php _e('Options data successfully Saved','elitepress');?></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_22_reset" ><?php _e('Options data successfully reset','elitepress');?></div>
				</td>
				<td style="text-align:right;">
					<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('22');">
					<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('22')" >
				</td>
				</tr>
			</table>	
		</div>		
		<?php wp_nonce_field('webriti_customization_nonce_gernalsetting','webriti_gernalsetting_nonce_customization'); ?>
		<div class="section">
			<h3><?php _e('Enable Social Media Links on Header:','elitepress'); ?>  </h3>
			<input type="checkbox" <?php if($current_options['header_social_media_enabled']=='on') echo "checked='checked'"; ?> id="header_social_media_enabled" name="header_social_media_enabled" > <span class="explain"><?php _e('Enable social media links on header section.','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Facebook Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute"  placeholder="Enter http://facebook.com"  type="text" name="social_media_facebook_link" id="social_media_facebook_link" value="<?php if($current_options['social_media_facebook_link']!='') { echo  esc_url($current_options['social_media_facebook_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['facebook_media_enabled']=='on') echo "checked='checked'"; ?> id="facebook_media_enabled" name="facebook_media_enabled" > <span class="explain"><?php _e('Open facebook media link in new tab/window','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Twitter Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute" placeholder="Enter http://twitter.com"  type="text" name="social_media_twitter_link" id="social_media_twitter_link" value="<?php if($current_options['social_media_twitter_link']!='') { echo  esc_url($current_options['social_media_twitter_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['twitter_media_enabled']=='on') echo "checked='checked'"; ?> id="twitter_media_enabled" name="twitter_media_enabled" > <span class="explain"><?php _e('Open twitter media link in new tab/window','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Google Plus Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute" placeholder="Enter http://google.com"  type="text" name="social_media_googleplus_link" id="social_media_googleplus_link" value="<?php if($current_options['social_media_googleplus_link']!='') { echo  esc_url($current_options['social_media_googleplus_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['googleplus_media_enabled']=='on') echo "checked='checked'"; ?> id="googleplus_media_enabled" name="googleplus_media_enabled" > <span class="explain"><?php _e('Open googleplus media link in new tab/window','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Linkedin Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute" placeholder="Enter http://linkedin.com"  type="text" name="social_media_linkedin_link" id="social_media_linkedin_link" value="<?php if($current_options['social_media_linkedin_link']!='') { echo  esc_url($current_options['social_media_linkedin_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['linkedin_media_enabled']=='on') echo "checked='checked'"; ?> id="linkedin_media_enabled" name="linkedin_media_enabled" > <span class="explain"><?php _e('Open linkedin media link in new tab/window','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Skype Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute" placeholder="Enter http://skype.com"  type="text" name="social_media_skype_link" id="social_media_skype_link" value="<?php if($current_options['social_media_skype_link']!='') { echo  esc_url($current_options['social_media_skype_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['skype_media_enabled']=='on') echo "checked='checked'"; ?> id="skype_media_enabled" name="skype_media_enabled" > <span class="explain"><?php _e('Open skype media link in new tab/window','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Dribbble Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute" placeholder="Enter http://dribbble.com"  type="text" name="social_media_dribbble_link" id="social_media_dribbble_link" value="<?php if($current_options['social_media_dribbble_link']!='') { echo  esc_url($current_options['social_media_dribbble_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['dribbble_media_enabled']=='on') echo "checked='checked'"; ?> id="dribbble_media_enabled" name="dribbble_media_enabled" > <span class="explain"><?php _e('Open dribbble media link in new tab/window','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Youtube Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute" placeholder="Enter http://youtube.com"  type="text" name="social_media_youtube_link" id="social_media_youtube_link" value="<?php if($current_options['social_media_youtube_link']!='') { echo  esc_url($current_options['social_media_youtube_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['youtube_media_enabled']=='on') echo "checked='checked'"; ?> id="youtube_media_enabled" name="youtube_media_enabled" > <span class="explain"><?php _e('Open youtube media link in new tab/window','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Vimeo Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute" placeholder="Enter http://vimeo.com"  type="text" name="social_media_vimeo_link" id="social_media_vimeo_link" value="<?php if($current_options['social_media_vimeo_link']!='') { echo  esc_url($current_options['social_media_vimeo_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['vimeo_media_enabled']=='on') echo "checked='checked'"; ?> id="vimeo_media_enabled" name="vimeo_media_enabled" > <span class="explain"><?php _e('Open vimeo media link in new tab/window','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Pagelines Profile Link:','elitepress');?></h3>
			<input class="webriti_inpute" placeholder="Enter http://pagelines.com"  type="text" name="social_media_pagelines_link" id="social_media_pagelines_link" value="<?php if($current_options['social_media_pagelines_link']!='') { echo  esc_url($current_options['social_media_pagelines_link']); } ?>" >
			<input type="checkbox" <?php if($current_options['pagelines_media_enabled']=='on') echo "checked='checked'"; ?> id="pagelines_media_enabled" name="pagelines_media_enabled" > <span class="explain"><?php _e('Open pagelines media link in new tab/window','elitepress'); ?></span>
		</div>
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Contact setting','elitepress');?></h2></td></tr>
			</table>	
	  </div>
	  <div class="section">
			<h3><?php _e('Enable Email id and Phone number on Header:','elitepress'); ?>  </h3>
			<input type="checkbox" <?php if($current_options['contact_address_settings']=='on') echo "checked='checked'"; ?> id="contact_address_settings" name="contact_address_settings" > <span class="explain"><?php _e('Enable email and phone number on header section.','elitepress'); ?></span>
	 </div>
	<div class="section">
			<h3><?php _e('Input Email id:','elitepress');?></h3>
			<input class="webriti_inpute"  placeholder="email id"  type="text" name="contact_email" id="contact_email" value="<?php if($current_options['contact_email']!='') { echo esc_attr($current_options['contact_email']); } ?>" >
	</div>
	
	<div class="section">
			<h3><?php _e('Input Contact Phone Number:','elitepress');?></h3>
			<input class="webriti_inpute"  placeholder="Phone number"  type="text" name="contact_phone_number" id="contact_phone_number" value="<?php if($current_options['contact_phone_number']!='') { echo esc_attr($current_options['contact_phone_number']); } ?>" >
	</div>
	<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Logo setting','elitepress');?></h2></td></tr>
			</table>	
	</div>
	<div class="section">
			<h3><?php _e('Enable Logo or text on Header:','elitepress'); ?>  </h3>
			<input type="checkbox" <?php if($current_options['logo_section_settings']=='on') echo "checked='checked'"; ?> id="logo_section_settings" name="logo_section_settings" > <span class="explain"><?php _e('Enable Logo or text on header section.','elitepress'); ?></span>
	</div>
	
	
	<div class="section">
			<h3><?php _e('Custom Logo','elitepress'); ?>
				<span class="icons help"><span class="tooltip"><?php  _e('Add custom logo from here suggested size is 250X50 px','elitepress');?></span></span>
			</h3>
			<input class="webriti_inpute" type="text" value="<?php if($current_options['upload_image_logo']!='') { echo esc_url($current_options['upload_image_logo']); } ?>" id="upload_image_logo" name="upload_image_logo" size="36" class="upload has-file"/>
			<input type="button" id="upload_button" value="Custom Logo" class="upload_image_button" />
			
			<?php if($current_options['upload_image_logo']!='') { ?>
			<p><img style="height:60px;width:100px;" src="<?php if($current_options['upload_image_logo']!='') { echo esc_url($current_options['upload_image_logo']); } ?>" /></p>
			<?php } ?>
		</div>
		
		<div class="section">
			<h3><?php _e('Logo Height','elitepress'); ?>
				<span class="icons help"><span class="tooltip"><?php  _e('Default Logo Height : 50px, if you want to increase than specify your value','elitepress'); ?></span></span>
			</h3>
			<input class="webriti_inpute"  type="text" name="height" id="height" value="<?php echo $current_options['height']; ?>" >						
		</div>
		<div class="section">
			<h3><?php _e('Logo Width','elitepress'); ?>
				<span class="icons help"><span class="tooltip"><?php  _e('Default Logo Width : 250px, if you want to increase than specify your value','elitepress');?></span></span>
			</h3>
			<input  class="webriti_inpute" type="text" name="width" id="width"  value="<?php echo $current_options['width']; ?>" >			
		</div>
		<div class="section">
			<h3><?php _e('Text Title','elitepress'); ?></h3>
			<input type="checkbox" <?php if($current_options['text_title']=='on') echo "checked='checked'"; ?> id="text_title" name="text_title" > <span class="explain"><?php _e('Enable text-based Site Title.   Setup title','elitepress');?> <a href="<?php echo esc_url(home_url('/') ); ?>wp-admin/options-general.php"><?php _e('Click Here','elitepress');?></a>.</span>
		</div>
		
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Header Menu & Search Bar setting','elitepress');?></h2></td></tr>
			</table>	
	  </div>
	<div class="section">
			<h3><?php _e('Enable Search bar on Header section:','elitepress'); ?>  </h3>
			<input type="checkbox" <?php if($current_options['header_search_bar_enabled']=='on') echo "checked='checked'"; ?> id="header_search_bar_enabled" name="header_search_bar_enabled" > <span class="explain"><?php _e('Enable Search bar on header section.','elitepress'); ?></span>
	 </div>
	
		<div id="button_section">
			<input type="hidden" value="1" id="webriti_settings_save_22" name="webriti_settings_save_22" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('22');">
			<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('22')" >
		</div>
		
	</form>
</div>