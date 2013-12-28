<?php
/*
Template Name: Works Listing Page
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

			<div class="row">
				<nav id="portfolio-filters" class="sixteen columns">
					<a href="#filter" class="active-item" data-filter="*"><?php _e('Show All', 'ci_theme'); ?></a>
					<?php
						$args = array(
							'hide_empty' => 0
						);
	
						$skills = get_terms('skill', $args);
					?>
					<?php foreach ( $skills as $skill ) : ?>
						<a href="#filter" data-filter=".<?php echo $skill->slug; ?>"><?php echo $skill->name; ?></a>
					<?php endforeach; ?>
				</nav><!-- /portfolio-filters -->
			</div>

			<div id="portfolio-items" class="row">
				<?php $ci_work_query = new WP_Query('post_type=work&posts_per_page=-1'); ?>
				<?php if ( $ci_work_query-> have_posts() ) : while ( $ci_work_query->have_posts() ) : $ci_work_query->the_post(); ?>
				<?php $item_skills = wp_get_object_terms($post->ID, 'skill');	?>

				<article class="<?php ci_e_setting('work_columns'); ?> columns <?php foreach ( $item_skills as $item_skill ) : echo $item_skill->slug.' '; endforeach; ?> columns portfolio-item">

					<a href="<?php echo get_permalink(); ?>" title="<?php echo esc_attr(get_the_title()); ?>" class="fb">
						<?php the_post_thumbnail('ci_portfolio_slider', array('class'=>'scale-with-grid')); ?>
					</a>
					<div class="portfolio-desc">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="desc"><?php echo mb_substr(get_the_excerpt(), 0, 70); ?>...</p>
					</div>
				</article><!-- /portfolio-item -->

				<?php endwhile; endif; ?>
				<?php wp_reset_postdata(); ?>

			</div><!-- /portfolio-items -->

			<?php get_template_part('part', 'call_to_action'); ?>

			<?php endwhile; endif; ?>
		</article>
	</div> <!-- .container < #page-content -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
