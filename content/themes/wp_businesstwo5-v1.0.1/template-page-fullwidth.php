<?php
/*
Template Name: Fullwidth Page
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

				<div class="sixteen columns">
					<article id="post-<?php the_ID(); ?>" <?php post_class('entry group'); ?>>
						<?php if ( ci_has_image_to_show() ) : ?>
							<?php $alignclass = ci_setting('featured_single_align'); ?>
							<figure class="entry-thumb <?php echo $alignclass; ?>">
								<a href="<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'large') ); ?>" class="fancybox">
									<?php ci_the_post_thumbnail_full(array( "class" => "scale-with-grid" ) ); ?>
								</a>
							</figure>
						<?php endif; ?>
	
						<?php ci_e_content(); ?>
						<?php wp_link_pages(); ?>
						<?php comments_template(); ?>
					</article>
				</div>

			<?php endwhile; endif; ?>
		</article>
	</div> <!-- .container < #page-content -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
