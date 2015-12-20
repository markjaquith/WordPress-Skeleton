<!-- Header social & Contact Info -->
	<div class="container">
		<div class="row">
		<?php $current_options = get_option('elitepress_lite_options',theme_data_setup());
			if($current_options['header_social_media_enabled']=='on') { ?>
			<div class="col-md-6">
				<ul class="head-contact-social">
					<?php if($current_options['social_media_facebook_link']!='') { ?>
					<li class="facebook"><a href="<?php echo esc_url($current_options['social_media_facebook_link']); ?>" <?php if($current_options['facebook_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-facebook"></i></a></li>
					<?php }
					if($current_options['social_media_twitter_link']!='') { ?>
					<li class="twitter"><a href="<?php echo esc_url($current_options['social_media_twitter_link']); ?>" <?php if($current_options['twitter_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-twitter"></i></a></li>
					<?php }
					if($current_options['social_media_googleplus_link']!='') { ?>
					<li class="googleplus"><a href="<?php echo esc_url($current_options['social_media_googleplus_link']); ?>" <?php if($current_options['googleplus_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-google-plus"> </i></a></li>
					<?php }
					if($current_options['social_media_linkedin_link']!='') { ?>
					<li class="linkedin"><a href="<?php echo esc_url($current_options['social_media_linkedin_link']); ?>" <?php if($current_options['linkedin_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-linkedin"></i></a></li>
					<?php }
					if($current_options['social_media_skype_link']!='') { ?>
					<li class="skype"><a href="<?php echo esc_url($current_options['social_media_skype_link']); ?>" <?php if($current_options['skype_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-skype"></i></a></li>
					<?php }
					if($current_options['social_media_dribbble_link']!='') { ?>
					<li class="dribbble"><a href="<?php echo esc_url($current_options['social_media_dribbble_link']); ?>" <?php if($current_options['dribbble_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-dribbble"></i></a></li>
					<?php }
					if($current_options['social_media_youtube_link']!='') { ?>
					<li class="youtube"><a href="<?php echo esc_url($current_options['social_media_youtube_link']); ?>" <?php if($current_options['youtube_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-youtube"></i></a></li>
					<?php }
					if($current_options['social_media_vimeo_link']!='') { ?>
					<li class="vimeo"><a href="<?php echo esc_url($current_options['social_media_vimeo_link']); ?>" <?php if($current_options['vimeo_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-vimeo-square"></i></a></li>
					<?php }
					if($current_options['social_media_pagelines_link']!='') { ?>
					<li class="pagelines"><a href="<?php echo esc_url($current_options['social_media_pagelines_link']); ?>" <?php if($current_options['pagelines_media_enabled']=='on'){ echo "target='_blank'"; } ?> ><i class="fa fa-pagelines"></i></a></li>
					<?php } ?>
				</ul>				
			</div>
			<?php } ?>
			<div class="col-md-6">
				<div class="clear"></div>
			<?php if($current_options['contact_address_settings']=='on') { ?>
				<ul class="head-contact-info">
					<?php if($current_options['contact_email']){ ?>
					<li><a href="#"><?php echo $current_options['contact_email'];?><i class="fa fa-envelope"></i></a></li>
					<?php } ?>
					<?php if(($current_options['contact_email']) && ($current_options['contact_phone_number'])){ ?>
					<li><span class="line"><?php _e('&#124;', 'elitepress'); ?></span></li>
					<?php } ?>
					<?php if($current_options['contact_phone_number']){ ?>
					<li><i class="fa fa-phone"></i><?php echo $current_options['contact_phone_number']; ?></li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>	
		</div>		
	</div>
	<!-- /Header social & Contact Info -->
<div class="head-topbar"></div>	