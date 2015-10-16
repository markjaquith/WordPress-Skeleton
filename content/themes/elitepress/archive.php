<?php get_header();?>
<?php get_template_part('banner','header'); ?>
<!-- Page Title Section -->
<div class="clearfix"></div>
<!-- /Page Title Section -->
	
<!-- Blog Full Width Section -->
<div class="blog-section">
	<div class="container">
		<div class="row">
			
			<!--Blog Area-->
			<div class="<?php elitepress_post_layout_class(); ?>" >
			
					<?php if ( have_posts() ) : ?>
					<h1 class="blog_detail_head">
					<?php if ( is_day() ) : ?>
					<?php  _e( "Daily Archives: ", 'elitepress' ); echo (get_the_date()); ?>
					<?php elseif ( is_month() ) : ?>
					<?php  _e( "Monthly Archives: ", 'elitepress' ); echo (get_the_date( 'F Y' )); ?>
					<?php elseif ( is_year() ) : ?>
					<?php  _e( "Yearly Archives: ", 'elitepress' );  echo (get_the_date( 'Y' )); ?>
					<?php else : ?>
					<?php _e( "Blog Archives: ", 'elitepress' ); ?>
					<?php endif; ?>
					</h1>
				<?php
				while ( have_posts() ) : the_post();
				global $more;
				$more = 0;
				?>
				<?php get_template_part('content',''); ?>
				<?php endwhile;	?>			
				<div class="blog-pagination">
					<?php previous_posts_link( __('Previous','elitepress') ); ?>
					<?php next_posts_link( __('Next','elitepress') ); ?>
				</div>
			</div>
			<?php endif; ?>
			<!--/Blog Area-->
		<?php get_sidebar(); ?>
		</div>	
	</div>
</div>
<?php get_footer(); ?>
<!-- /Blog Full Width Section -->
<div class="clearfix"></div>