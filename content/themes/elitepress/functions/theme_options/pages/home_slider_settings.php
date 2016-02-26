<div class="block ui-tabs-panel " id="option-ui-id-2" >	
	<?php $current_options = wp_parse_args(  get_option( 'elitepress_lite_options', array() ), theme_data_setup() );
	if(isset($_POST['webriti_settings_save_2']))
	{	
		if($_POST['webriti_settings_save_2'] == 1) 
		{
			if ( empty($_POST) || !wp_verify_nonce($_POST['webriti_gernalsetting_nonce_customization'],'webriti_customization_nonce_gernalsetting') )
			{  printf (__('Sorry, your nonce did not verify.','elitepress'));	exit; }
			else  
			{	
				$current_options['slider_radio']=sanitize_text_field($_POST['slider_radio']);
				$current_options['featured_slider_post']=sanitize_text_field($_POST['featured_slider_post']);
				
				
				
				$all_cats=$_POST['slider_select_category'];
				if($all_cats)
				{
					$arr=' ';
					foreach($all_cats as $val)
					{
						$arr.=$val.',';
					}
					$current_options['slider_select_category']=$arr;
				}
				// slider section enabled yes ya on
				if(isset($_POST['home_banner_enabled']))
				{ echo $current_options['home_banner_enabled']= sanitize_text_field($_POST['home_banner_enabled']); } 
				else { echo $current_options['home_banner_enabled']="off"; } 
				
				// slider section enabled yes ya on
				if(isset($_POST['home_post_enabled']))
				{ echo $current_options['home_post_enabled']= sanitize_text_field($_POST['home_post_enabled']); } 
				else { echo $current_options['home_post_enabled']="off"; } 
				$current_options['animation']=sanitize_text_field($_POST['animation']);
				$current_options['animationSpeed']=sanitize_text_field($_POST['animationSpeed']);
				$current_options['slide_direction']=sanitize_text_field($_POST['slide_direction']);
				$current_options['slideshowSpeed']=sanitize_text_field($_POST['slideshowSpeed']);
				
				echo '<pre>';print_r($current_options);
				
				update_option('elitepress_lite_options', $current_options);
			}
		}	
		 if($_POST['webriti_settings_save_2'] == 2) 
		{
			
			$current_options['home_banner_enabled']='on';
			$current_options['home_post_enabled']='on';
			$current_options['slider_btn_link_target']= 'on';
			$current_options['slider_radio']= 'demo';
			$current_options['slider_select_category']= ' Uncategorized ';
			$current_options['featured_slider_post']= '';

			$current_options['home_slider_enabled']="on";
			$current_options['animation']='slide';
			$current_options['animationSpeed']='1500';
			$current_options['slide_direction']='horizontal';
			$current_options['slideshowSpeed']='2500';
			
			
			
			update_option('elitepress_lite_options',$current_options);
		} 
	}  ?>
	<form method="post" id="webriti_theme_options_2">
		<div id="heading">
			<table style="width:100%;"><tr>
				<td><h2><?php _e('Home Slider Setting','elitepress');?></h2></td>
				<td><div class="webriti_settings_loding" id="webriti_loding_2_image"></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_2_success" ><?php _e('Options data successfully Saved','elitepress');?></div>
					<div class="webriti_settings_massage" id="webriti_settings_save_2_reset" ><?php _e('Options data successfully reset','elitepress');?></div>
				</td>
				<td style="text-align:right;">
					<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('2');">
					<input class="button button-primary button-large" type="button" value="Save Options" onclick="webriti_option_data_save('2')" >
				</td>
				</tr>
			</table>	
		</div>		
		<?php wp_nonce_field('webriti_customization_nonce_gernalsetting','webriti_gernalsetting_nonce_customization'); ?>
		<div class="section">
			<h3><?php _e('Enable Home Slider','elitepress'); ?>  </h3>
			<input type="checkbox" <?php if($current_options['home_banner_enabled']=='on') echo "checked='checked'"; ?> id="home_banner_enabled" name="home_banner_enabled" >
			<span class="explain"><?php _e('Enable Home Slider on front page.','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Select Slider Type','elitepress'); ?>  </h3>
			<input type="radio" name="slider_radio" id="slider_radio_demo" value="demo" <?php if($current_options['slider_radio']=='demo'){echo 'checked';} ?>><?php _e('Demo slider','elitepress'); ?>
			<input type="radio" name="slider_radio" id="slider_radio_post" value="post" <?php if($current_options['slider_radio']=='post'){echo 'checked';} ?>><?php _e('Post slider','elitepress'); ?>
			<input type="radio" name="slider_radio"  id="slider_radio_category" value="category" <?php if($current_options['slider_radio']=='category'){echo 'checked';} ?>> <?php _e('Category slider','elitepress'); ?>
		</div>
		<div id="main_section" class="section" <?php if ($current_options['slider_radio']=='demo'){echo 'style="display:none;"';}?>>
		<div class="section">
			<h3><?php _e('Animation','elitepress'); ?></h3>
			<?php $animation = $current_options['animation']; ?>		
			<select name="animation" class="webriti_inpute" >					
				<option value="fade"  <?php echo selected($animation, 'fade' ); ?>><?php _e('fade','elitepress');?></option>
				<option value="slide" <?php echo selected($animation, 'slide' ); ?>><?php _e('slide','elitepress');?></option> 
			</select>
			<span class="explain"><?php _e('Select the Animation Type.','elitepress'); ?></span>
		</div>
		<div class="section">
			<h3><?php _e('Slide direction','elitepress'); ?></h3>
			<?php $slide_direction = $current_options['slide_direction']; ?>		
				<select name="slide_direction" class="webriti_inpute" >					
					<option value="vertical"  <?php echo selected($slide_direction, 'vertical' ); ?>><?php _e('vertical','elitepress');?></option>
					<option value="horizontal" <?php echo selected($slide_direction, 'horizontal' ); ?>><?php _e('horizontal','elitepress');?></option> 
				</select>
				<span class="explain"><?php _e('Select Slide direction.','elitepress'); ?></span>	
		</div>
		<div class="section">
			<h3><?php _e('Animation speed','elitepress') ?></h3>
			<?php $animationSpeed = $current_options['animationSpeed']; ?>		
				<select name="animationSpeed" class="webriti_inpute" >					
					<option value="500" <?php selected($animationSpeed, '500' ); ?>>0.5</option>
					<option value="1000" <?php selected($animationSpeed, '1000' ); ?>>1.0</option>
					<option value="1500" <?php selected($animationSpeed, '1500' ); ?>>1.5</option>
					<option value="2000" <?php selected($animationSpeed, '2000' ); ?>>2.0</option>
					<option value="2500" <?php selected($animationSpeed, '2500' ); ?>>2.5</option>
					<option value="3000" <?php selected($animationSpeed, '3000' ); ?>>3.0</option>
					<option value="3500" <?php selected($animationSpeed, '3500' ); ?>>3.5</option>
					<option value="4000" <?php selected($animationSpeed, '4000' ); ?>>4.0</option>
					<option value="4500" <?php selected($animationSpeed, '4500' ); ?>>4.5</option>
					<option value="5000" <?php selected($animationSpeed, '5000' ); ?>>5.0</option>
					<option value="5500" <?php selected($animationSpeed, '5500' ); ?>>5.5</option>
				</select>
				<span class="explain"><?php _e('Select Slide Animation speed.','elitepress'); ?></span>	
		</div>
		<div class="section">
			<h3><?php _e('Slideshow speed','elitepress'); ?></h3>
			<?php $slideshowSpeed = $current_options['slideshowSpeed']; ?>		
			<select name="slideshowSpeed" class="webriti_inpute">					
				<option value="500" <?php selected($slideshowSpeed, '500' ); ?>>0.5</option>
				<option value="1000" <?php selected($slideshowSpeed, '1000' ); ?>>1.0</option>
				<option value="1500" <?php selected($slideshowSpeed, '1500' ); ?>>1.5</option>
				<option value="2000" <?php selected($slideshowSpeed, '2000' ); ?>>2.0</option>
				<option value="2500" <?php selected($slideshowSpeed, '2500' ); ?>>2.5</option>
				<option value="3000" <?php selected($slideshowSpeed, '3000' ); ?>>3.0</option>
				<option value="3500" <?php selected($slideshowSpeed, '3500' ); ?>>3.5</option>
				<option value="4000" <?php selected($slideshowSpeed, '4000' ); ?>>4.0</option>
				<option value="4500" <?php selected($slideshowSpeed, '4500' ); ?>>4.5</option>
				<option value="5000" <?php selected($slideshowSpeed, '5000' ); ?>>5.0</option>
				<option value="5500" <?php selected($slideshowSpeed, '5500' ); ?>>5.5</option>
			</select>
			<span class="explain"><?php _e('Select the Slide Show Speed.','elitepress'); ?></span>
		</div>
		</div>
	
		<div id="post_slider" <?php if($current_options['slider_radio']!='post'){echo 'style="display:none;"';}?>>
			<input type="checkbox" <?php if($current_options['home_post_enabled']=='on') echo "checked='checked'"; ?> id="home_post_enabled" name="home_post_enabled" value="on">
			<span class="explain"><?php _e('Enable Feature post slider on Page.','elitepress'); ?></span>
			
			<h3><?php _e('Featured post slider section','elitepress'); ?> </h3>
			<div id="all_slider_content">
				<div class="repeat-content-wrap">
					<div class="row"> 
						<div class="col col-1"> <?php _e('Featured Post Slider','elitepress'); ?>
						</div>
						<div class="col col-2">
							<input type="text" name="featured_slider_post" value="<?php echo $current_options['featured_slider_post'];?>">
							<a href="http://localhost/wordpress_elegent/wp-admin/post.php?post=&amp;action=edit" class="button" title="Click Here To Edit" target="_blank"><?php _e('Click Here To Edit','elitepress'); ?></a>
							<p><span> <h5><?php _e("You can use multiple ID's seprated by Commma[ , ]",'elitepress');?></span></h5></p>						</div>
					</div>
				</div>
			</div>
		</div>	
		<div id="category_slider" <?php if($current_options['slider_radio']!='category'){echo 'style="display:none;"';}?>>
			<h3><?php _e('Featured Category slider section','elitepress'); ?> </h3>
			
			<div>
				<select class="slider_select_cat" name="slider_select_category[]" multiple >
				
				<?php
				$args = array(
				  'orderby' => 'name',
				  'parent' => 0
				  );
				$categories = get_categories( $args );
				foreach ( $categories as $category ) {
				?>
					<option <?php if(!strpos($current_options['slider_select_category'],$category->name)===false){echo 'selected';}?> > <?php echo $category->name;?> </option><?php
				}
				?>
				</select>
			</div>
		</div>
		
		<div id="demo_slider" <?php if ($current_options['slider_radio']!='demo'){echo 'style="display:none;"';}?>>
		</div>	
		<div class="section">
		</div>
		
		<div id="button_section">
			<input type="hidden" value="1" id="webriti_settings_save_2" name="webriti_settings_save_2" />
			<input class="reset-button btn" type="button" name="reset" value="Restore Defaults" onclick="webriti_option_data_reset('2');">
			<input class="button button-primary button-large" type="button" value="Save Options" onclick="webriti_option_data_save('2')" >
		</div>
	</form>
</div>
<script>                         
	
	
  jQuery('input[name=slider_radio]').on('click',function(){
  if(this.value=='category')
  {
	jQuery("#category_slider").attr('style','display:block');
	jQuery("#post_slider").attr('style','display:none');
	jQuery("#demo_slider").attr('style','display:none');
	jQuery("#main_section").attr('style','display:block');
  }
  else if(this.value=='post')
  {
	jQuery("#category_slider").attr('style','display:none');
	jQuery("#post_slider").attr('style','display:block');
	jQuery("#demo_slider").attr('style','display:none');
	jQuery("#main_section").attr('style','display:block');
  }
  else
  {
  jQuery("#category_slider").attr('style','display:none');
  jQuery("#post_slider").attr('style','display:none');
  jQuery("#demo_slider").attr('style','display:block');
  jQuery("#main_section").attr('style','display:none');
  }
  });
  </script>