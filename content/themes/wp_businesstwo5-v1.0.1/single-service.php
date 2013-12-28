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
				<article id="post-<?php the_ID(); ?>" <?php post_class('entry group'); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-thumb">
							<?php the_post_thumbnail('ci_listing_thumb', array('class'=>'scale-with-grid')); ?>
					</figure>
					<?php endif; ?>

					<?php ci_e_content(); ?>
				</article>
			</div>
			<?php endwhile; endif; ?>

			<?php get_sidebar(); ?>
		</article>
	</div> <!-- .container < #page-content -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
