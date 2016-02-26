<div class="block ui-tabs-panel " id="option-ui-id-24" >	
	<?php $current_options = wp_parse_args(  get_option( 'elitepress_lite_options', array() ), theme_data_setup() );
	if(isset($_POST['webriti_settings_save_24']))
	{	
		if($_POST['webriti_settings_save_24'] == 1) 
		{
			if ( empty($_POST) || !wp_verify_nonce($_POST['webriti_gernalsetting_nonce_customization'],'webriti_customization_nonce_gernalsetting') )
			{  print 'Sorry, your nonce did not verify.';	exit; }
			else  
			{
  		$current_options['banner_title_category']=sanitize_text_field($_POST['banner_title_category']);
		$current_options['banner_description_category']=sanitize_text_field($_POST['banner_description_category']);
		
		$current_options['banner_title_archive']=sanitize_text_field($_POST['banner_title_archive']);
		$current_options['banner_description_archive']=sanitize_text_field($_POST['banner_description_archive']);
  		
	
		$current_options['banner_title_author']=sanitize_text_field($_POST['banner_title_author']);
		$current_options['banner_description_author']=sanitize_text_field($_POST['banner_description_author']);
  		
		
		$current_options['banner_title_404']=sanitize_text_field($_POST['banner_title_404']);
		$current_options['banner_description_404']=sanitize_text_field($_POST['banner_description_404']);
  		
		$current_options['banner_title_tag']=sanitize_text_field($_POST['banner_title_tag']);
		$current_options['banner_description_tag']=sanitize_text_field($_POST['banner_description_tag']);
  		
		$current_options['banner_title_search']=sanitize_text_field($_POST['banner_title_search']);
		$current_options['banner_description_search']=sanitize_text_field($_POST['banner_description_search']);
  		
  		update_option('elitepress_lite_options' ,stripslashes_deep($current_options));
  		
  	}
     }
  if($_POST['webriti_settings_save_24'] == 2) 
		{
		do_action('elitepress_restore_data', 2);		
		}
  }
  ?>
	<form method="post" id="webriti_theme_options_24">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Banner Settings','elitepress');?></h2></td>
				<td><div class="webriti_settings_loding" id="webriti_loding_24_image"></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_24_success" ><?php _e('Options data successfully Saved','elitepress');?></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_24_reset" ><?php _e('Options data successfully reset','elitepress');?></div>
				</td>
				<td style="text-align:right;">
					<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('24');">
					<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('24')" >
				</td>
				</tr>
			</table>	
		</div>		
		<?php wp_nonce_field('webriti_customization_nonce_gernalsetting','webriti_gernalsetting_nonce_customization'); ?>
		
		<div class="section">
			 <h3><b><?php _e('Banner Configuration For Category Template','elitepress'); ?></b></h3>
			 <p><h4 class="heading"><?php _e('Category Banner Tagline One','elitepress');?></h4>
			<input type="text" class="inputwidth" name="banner_title_category" id="banner_title_category" value="<?php if($current_options['banner_title_category']!='') { echo esc_attr($current_options['banner_title_category']); } ?>"/>
			</span>

			</p>
			<p><h4 class="heading"><?php _e('Category Banner Description','elitepress');?></h4>
			<textarea class="inputwidth" style="height:100px;" name="banner_description_category" id="banner_description_category" > <?php if($current_options['banner_description_category']!='') { echo esc_attr($current_options['banner_description_category']); } ?></textarea>
			</span>
			</p>
		</div>
		
		<div class="section">
			 <h3><b><?php _e('Banner Configuration For Archive Template','elitepress'); ?></b></h3>
			 <p><h4 class="heading"><?php _e('Archive Banner Tagline One','elitepress');?></h4>
			<input type="text" class="inputwidth" name="banner_title_archive" id="banner_title_archive" value="<?php if($current_options['banner_title_archive']!='') { echo esc_attr($current_options['banner_title_archive']); } ?>"/>
			</span>

			</p>
			<p><h4 class="heading"><?php _e('Archive Banner Description','elitepress');?></h4>
			<textarea class="inputwidth" style="height:100px;" name="banner_description_archive" id="banner_description_archive" > <?php if($current_options['banner_description_archive']!='') { echo esc_attr($current_options['banner_description_archive']); } ?></textarea>
			</span>
			</p>
		</div>
		
		
		<div class="section">
		 <h3><b><?php _e('Banner Configuration For Author Template','elitepress'); ?></b></h3>
			 <p><h4 class="heading"><?php _e('Author Banner Tagline One','elitepress');?></h4>
			<input type="text" class="inputwidth" name="banner_title_author" id="banner_title_author" value="<?php if($current_options['banner_title_author']!='') { echo esc_attr($current_options['banner_title_author']); } ?>"/>
			</span>
			</p>
			<p><h4 class="heading"><?php _e('Author Banner Description','elitepress');?></h4>
			<textarea class="inputwidth" style="height:100px;" name="banner_description_author" id="banner_description_author" > <?php if($current_options['banner_description_author']!='') { echo esc_attr($current_options['banner_description_author']); } ?></textarea>
			</span>
			</p>
		</div>
		
		<div class="section">
			<h3><b><?php _e('Banner Configuration For 404 Template','elitepress'); ?></b></h3>
			<p><h4 class="heading"><?php _e('404 Banner Tagline One','elitepress');?></h4>
			<input type="text" class="inputwidth" name="banner_title_404" id="banner_title_404" value="<?php if($current_options['banner_title_404']!='') { echo esc_attr($current_options['banner_title_404']); } ?>"/>
			</span>
			</p>
	
			<p><h4 class="heading"><?php _e('404 Banner Description','elitepress');?></h4>
			<textarea class="inputwidth" style="height:100px;" name="banner_description_404" id="banner_description_404" > <?php if($current_options['banner_description_404']!='') { echo esc_attr($current_options['banner_description_404']); } ?></textarea>
			</span>
			</p>
		</div>
		
	<div class="section">	
		<h3><b><?php _e('Banner Configuration For Tag Template','elitepress'); ?></b></h3>
		 <p><h4 class="heading"><?php _e('Tag Banner Tagline One','elitepress');?></h4>
		<input type="text" class="inputwidth" name="banner_title_tag" id="banner_title_tag" value="<?php if($current_options['banner_title_tag']!='') { echo esc_attr($current_options['banner_title_tag']); } ?>"/>
		</span>
		</p>
		
		<p><h4 class="heading"><?php _e('Tag Banner Description','elitepress');?></h4>
		<textarea class="inputwidth" style="height:100px;" name="banner_description_tag" id="banner_description_tag" > <?php if($current_options['banner_description_tag']!='') { echo esc_attr($current_options['banner_description_tag']); } ?></textarea>
		</span>
		</p>
	</div>
		
		<div class="section">
			 <h3><b><?php _e('Banner Configuration For Search Template','elitepress'); ?></b></h3>
			<p><h4 class="heading"><?php _e('Search Banner Tagline One','elitepress');?></h4>
			<input type="text" class="inputwidth" name="banner_title_search" id="banner_title_search" value="<?php if($current_options['banner_title_search']!='') { echo esc_attr($current_options['banner_title_search']); } ?>"/>
			</span>
			</p>
			
			<p><h4 class="heading"><?php _e('Search Banner Description','elitepress');?></h4>
			<textarea class="inputwidth" style="height:100px;" name="banner_description_search" id="banner_description_search" > <?php if($current_options['banner_description_search']!='') { echo esc_attr($current_options['banner_description_search']); } ?></textarea>
			</span>
			</p>
		</div>
		
<div id="button_section">
			<input type="hidden" value="1" id="webriti_settings_save_24" name="webriti_settings_save_24" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('24');">
			<input class="btn btn-primary" type="button" value="Save Options" onclick="webriti_option_data_save('24')" >
		</div>
	</form>
</div>