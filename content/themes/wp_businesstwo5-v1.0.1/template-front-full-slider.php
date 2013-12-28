<?php
/*
Template Name: Front Page - Full width slider
*/
?>
<?php get_header(); ?>

<?php 
	global $slider_width;
	if($slider_width=='fixed')
		get_template_part('part-slider_fixed'); 
	else
		get_template_part('part-slider_full'); 
?>

<?php if(ci_setting('disable_home_services')!='enabled'): ?>
	<section id="services">
		<div class="container">
			<?php 
				$services = new WP_Query(array(
					'post_type' => 'service',
					'meta_key'=>'ci_cpt_service_on_homepage',
					'meta_value'=>'enabled',
					'posts_per_page' => -1
				));
				
			?>
			<?php if($services->have_posts()): ?>
				<div class="row service-array">
					<?php while($services->have_posts()): $services->the_post(); ?>
						<article class="service-item <?php ci_e_setting('service_home_cols'); ?> columns">
							<?php 
								$secondary_id = get_post_meta($post->ID, 'ci_cpt_secondary_featured_id', true); 
								if(!empty($secondary_id)){
									$img = wp_get_attachment_image_src($secondary_id, 'ci_portfolio_list_small');
									echo '<a href="'.get_permalink().'">';
									echo '<img src="'.esc_attr($img[0]).'" class="client-thumb scale-with-grid" />';
									echo '</a>';
								}
							?>
							<h3><?php the_title(); ?></h3>
							<?php the_excerpt(); ?>
							<a href="<?php the_permalink(); ?>" class="read-more"><?php _e('Read More', 'ci_theme'); ?></a>
						</article>
					<?php endwhile; ?>
				</div> <!-- .row -->
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
	
			
			<?php get_template_part('part-call_to_action'); ?>
	
	
			<?php if(is_active_sidebar('front-services-sidebar')): ?>
				<div class="business-info row">
					<?php dynamic_sidebar('front-services-sidebar'); ?>
				</div>
			<?php endif; ?>
			
		</div> <!--.container < #services -->
	</section> <!-- #services -->
<?php endif; ?>

<?php if(ci_setting('disable_home_clients')!='enabled'): ?>
	<section id="clients">
		<div class="container">
			<div class="row">
				<h3 class="widget-title clients-title sixteen columns">
					<?php ci_e_setting('home_clients_heading'); ?>
					<?php if(ci_setting('clients_page')!=''): ?>
						<a href="<?php echo get_permalink(ci_setting('clients_page')); ?>"><?php ci_e_setting('home_clients_link_text'); ?></a>
					<?php endif; ?>
				</h3>

				<?php $clients = new WP_Query(array(
					'post_type' => 'client',
					'meta_key' => 'ci_cpt_client_on_homepage',
					'meta_value' => 'enabled',
					'posts_per_page' => -1
				));?>
				<?php if($clients->have_posts()): ?>
					<div id="client-array">
						<?php while($clients->have_posts()): $clients->the_post(); ?>
							<article class="client-item <?php ci_e_setting('client_home_cols'); ?> columns">

								<?php $link = get_post_meta($post->ID, 'ci_cpt_link_url', true); ?>
								<?php if(!empty($link)) echo '<a href="'.$link.'">'; ?>
									<?php the_post_thumbnail('ci_portfolio_slider', array('class'=>'client-thumb scale-with-grid')); ?>
								<?php if(!empty($link)) echo '</a>'; ?>

								<span class="client-name"><?php the_title(); ?></span>
							</article>
						<?php endwhile; ?>
					</div>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div> <!--.container <#clients -->
	</section> <!-- #clients -->
<?php endif; ?>

<?php if(is_active_sidebar('front-before-footer')): ?>
	<section id="home-widget">
		<div class="container">
			<div class="row">
				<?php dynamic_sidebar('front-before-footer'); ?>
			</div> <!-- .row -->
		</div> <!-- .container < #home-widget -->
	</section> <!-- #home-widget -->
<?php endif; ?>

<?php get_footer(); ?>
