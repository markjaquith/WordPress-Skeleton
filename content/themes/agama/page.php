<?php
/**
 * The template for displaying all pages
 *
 * @package Theme-Vision
 * @subpackage Agama
 * @since Agama 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content col-md-9">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>