<?php
get_header();
$current_options = get_option('elitepress_lite_options',theme_data_setup()); 

 if(is_category()){
  $h1=$current_options['banner_title_category'];
  $bd=$current_options['banner_description_category'];
  }else if(is_author())
  {
  $h1=$current_options['banner_title_author'];
  $bd=$current_options['banner_description_author'];
  }else if(is_404())
  {
  $h1=$current_options['banner_title_404'];
  $bd=$current_options['banner_description_404'];
  }
  else if(is_tag())
  {
  $h1=$current_options['banner_title_tag'];
  $bd=$current_options['banner_description_tag'];
  }else if(is_archive() )
  {
  $h1=$current_options['banner_title_archive'];
  $bd=$current_options['banner_description_archive'];
  }
  else if(is_search())
  {
  $h1=$current_options['banner_title_search'];
  $bd=$current_options['banner_description_search'];
  }
  else
  {
  $h1=get_post_meta( $post->ID, 'banner_title', true );
  $bd=get_post_meta( $post->ID, 'banner_description', true );
  }
  ?>
<div class="page-title-section">		
	<div class="overlay">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="page-title">
					<h1><?php if($h1!=''){ echo esc_attr($h1); } else{ 
					_e("Title",'elitepress');} ?></h1>
					<div class="page-title-seprator"></div>
					 <p><?php if($bd!=''){ echo esc_attr($bd);}  else { 
					_e('Autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et dolore feugait','elitepress');}?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>