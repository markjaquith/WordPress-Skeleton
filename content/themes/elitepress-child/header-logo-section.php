<!-- Logo goes here -->
	<div class="container">
		<div class="row">
	<?php $current_options = get_option('elitepress_lite_options',theme_data_setup()); 
			
	 if($current_options['logo_section_settings']=='on') { ?>
			<div class="col-md-8">	
				<div class="site-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="The Arches Project">
					<?php
						if($current_options['text_title'] =="on") //If the value of 'text_title' in the 'current_options' array equals 'on' then the following line of code within the if statement block will be output to the DOM 
						{ echo "<div class=elitepress_title_head>" . get_bloginfo( ). "</div>"; }//Since there is no text_title selected in the options panel then nothing will be displayed
						else if($current_options['upload_image_logo']!='') 
						{ ?>
						<img src="<?php echo esc_url($current_options['upload_image_logo']); ?>" style="height:<?php if($current_options['height']!='') { echo esc_html($current_options['height']); } ?>px; width:<?php if($current_options['width']!='') { echo esc_html($current_options['width']); } ?>px;" alt="logo" />
						<?php } ?>
					</a>
				</div>
			</div>
			<div class="col-md-4">	
				<div id="header-btn-container" class="pull-right">
					<a class="btn btn-default btn-lg" href="#" role="button">Apply</a>
					<a class="btn btn-default btn-lg" href="#" role="button">Donate</a>
				</div>
			</div>	
		</div>
	<?php } ?>	
	</div>
<!-- /Logo goes here -->