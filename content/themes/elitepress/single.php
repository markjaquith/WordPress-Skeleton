<?php 
get_header(); 	
get_template_part('index','banner');
 ?>
<!-- Blog Detail Section -->
<div class="blog-section">
	<div class="container">
		<div class="row">
		
			<!--Blog Detail Area-->
			<div class="<?php elitepress_post_layout_class(); ?>" >
			
				<?php 
					while(have_posts()) { the_post();
				?>
				
				<?php get_template_part('content',''); ?>
				
			<!--Blog Author-->
				<div class="blog-author">
					<div class="media">
						<div class="pull-left">
							<?php echo get_avatar( get_the_author_meta('ID'), 60); ?>
						</div>
						<div class="media-body">
							<h6><?php the_author_link(); ?></h6>
							<p><?php the_author_meta( 'description' ); ?></p>
						</div>
					</div>	
				</div>
				<!--/Blog Author-->
				
			<!--Comment Section-->
			<?php comments_template('',true); ?>
			<!--/Comment Section-->
			<?php } ?>
			</div>
			
			<!--Sidebar Area-->
			<?php get_sidebar(); ?>
			<!--Sidebar Area-->
		</div>	
	</div>
</div>
<!-- Footer Section -->
<?php get_footer(); ?>
<!-- /Close of wrapper -->