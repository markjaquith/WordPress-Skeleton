<?php 
/* Template Name: buddypress */
get_header();
 ?>
<div class="content-section">
	<div class="title-section">
	</div>
	<div class="container">
	   <div class="row">
		<div class="col-md-8">
			<?php get_template_part('content',''); ?>  
		
		<div class="col-md-4">
			
			<?php 
				//Displays sidebar for members with level one access and above(artists to admin level can see the sidebar)
			  if(current_user_can(access_s2member_level1)){
				if ( is_active_sidebar( 'sidebar_primary' ) ) //checks if sidebar primary is active then dispolays the sidebar
				{ dynamic_sidebar( 'sidebar_primary' );	}
			  }
			?>
		</div>
	   </div>	
	</div>
</div>	

<?php get_footer(); ?>
