<?php
/*
 * Template Name: Services Listing
 */
?>

<?php get_header(); ?>

<section id="page-content">
	<div class="container">
		<article class="row">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div id="single-head" class="sixteen columns">
					<h1><?php the_title(); ?></h1>
					<?php if (has_excerpt()) { the_excerpt(); } ?>
				</div>
	
				<div class="twelve columns">
					<?php the_content(); ?>
					<section id="services">
							<?php
								$services = new WP_Query(array(
									'post_type' => 'service',
									'posts_per_page' => -1
								));
							?>
							<?php if($services->have_posts()): ?>
							<div class="row service-array">
								<?php ci_column_classes(2, 10, true); ?>
								<?php while($services->have_posts()): $services->the_post(); ?>
									<article class="service-item columns <?php echo ci_column_classes(2, 12); ?>">
										<h3><?php the_title(); ?></h3>
										<?php 
											$secondary_id = get_post_meta($post->ID, 'ci_cpt_secondary_featured_id', true); 
											if(!empty($secondary_id)){
												$img = wp_get_attachment_image_src($secondary_id, 'ci_portfolio_list_small');
												echo '<figure class="service-thumb">';
													echo '<a href="'.get_permalink().'">';
													echo '<img src="'.esc_attr($img[0]).'" class="scale-with-grid" />';
													echo '</a>';
												echo '</figure>';
											}
										?>
										<?php the_excerpt(); ?>
										<a href="<?php the_permalink(); ?>" class="read-more"><?php _e('Read More', 'ci_theme'); ?></a>
									</article>
								<?php endwhile; ?>
							</div> <!-- .row -->
							<?php endif; ?>
							<?php wp_reset_postdata(); ?>
	
					</section> <!-- #services -->
	
				</div>
			<?php endwhile; endif; ?>

			<?php get_sidebar(); ?>
			<?php get_template_part('part-call_to_action'); ?>

		</article>

	</div> <!-- .container < #page-content -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
