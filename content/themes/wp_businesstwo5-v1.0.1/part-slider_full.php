<?php $q = new WP_Query( array(
	'post_type' => array('slider', 'work', 'product'),
	'meta_key' => 'ci_cpt_on_homepage_slider',
	'meta_value' => 'slider',
	'posts_per_page' => -1
)); 
?>
<?php if($q->have_posts()): ?>
	<section id="home-slider" class="row">
		<div class="flexslider">
			<ul class="slides">
				<?php while($q->have_posts()): $q->the_post();?>
					<li>
						<?php 
							$link = get_post_meta($post->ID, 'ci_cpt_slider_url', true); 
							$link = !empty($link) ? $link : get_permalink();
						?>
						<a class="slide-link" href="<?php echo $link; ?>">
							<div class="slide-thumb">
								<div class="slide-title-wrap">
									<div class="slide-title container">
										<h3><?php the_title(); ?></h3>
									</div>
								</div>
								<?php the_post_thumbnail('ci_slider_thumb'); ?>
							</div>
						</a>
					</li>
				<?php endwhile; ?>
			</ul> <!--.slides -->
		</div> <!-- .flexslider -->
	</section> <!-- #home-slider -->
<?php endif; ?>
<?php wp_reset_postdata(); ?>
