<?php
//global $event_list;
?>
<div class="portfolio-section">
	<div class="container">
		<!-- Section for latest events title -->
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<h3 class="section-title">Latest Events</h3>
			</div>
		</div>
	
	<!--/section title-->
	
	<!--Section for displating latest events -->
	<div class="row">
	<?php 
		$args = array('post_type' => 'epl_event');
		$query = new WP_QUERY($args);
		
		if ($query->have_posts()){ //if statement to check if there are event type posts
				while ($query->have_posts()){   //start of while loop
					
					$query->the_post();
					
					
	?>  		
		
		
		<div class="col-md-4 col-sm-6 portfolio-area">
			<div class="portfolio_image">
				<?php the_post_thumbnail(array(360, 370)); ?>
			</div>
			<div class="home_portfolio_caption">
				<h4><a href ="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
				<p>Piece of text</p>
			</div>			
		</div>
	
	
	<?php 
				}//end of while loop
		}?>	
	</div>	
	</div>
</div>