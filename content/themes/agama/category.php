<?php
/**
 * The template for displaying Category pages
 *
 * Used to display archive-type pages for posts in a category.
 *
 * @package Theme-Vision
 * @subpackage Agama
 * @since Agama 1.0
 */

get_header(); ?>

	<section id="primary" class="site-content col-md-9">
		
		<?php if( get_theme_mod('agama_blog_layout', 'list') == 'grid' ): ?>
			<header class="archive-header">
				<h1 class="archive-title"><?php printf( __( 'Category Archives: %s', 'agama' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></h1>

			<?php if ( category_description() ) : // Show an optional category description ?>
				<div class="archive-meta"><?php echo category_description(); ?></div>
			<?php endif; ?>
			</header><!-- .archive-header -->
		<?php endif; ?>
		
		<div id="content" role="main" <?php if( get_theme_mod('agama_blog_layout', 'list') == 'grid' && ! is_singular() ): ?>class="js-isotope"  data-isotope-options='{ "itemSelector": ".article-wrapper" }'<?php endif; ?>>

		<?php if ( have_posts() ) : ?>
		
			<?php if( get_theme_mod('agama_blog_layout', 'list') != 'grid' ): ?>
			<header class="archive-header">
				<h1 class="archive-title"><?php printf( __( 'Category Archives: %s', 'agama' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?></h1>

			<?php if ( category_description() ) : // Show an optional category description ?>
				<div class="archive-meta"><?php echo category_description(); ?></div>
			<?php endif; ?>
			</header><!-- .archive-header -->
			<?php endif; ?>

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/* Include the post format-specific template for the content. If you want to
				 * this in a child theme then include a file called called content-___.php
				 * (where ___ is the post format) and that will be used instead.
				 */
				get_template_part( 'content', get_post_format() );

			endwhile;
			
			if( get_theme_mod('agama_blog_layout', 'list') != 'grid' ) {
				agama_content_nav( 'nav-below' );
			}
			?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>

		</div><!-- #content -->
		<?php if( get_theme_mod('agama_blog_layout', 'list') == 'grid' ): ?>
			<?php agama_content_nav( 'nav-below' ); ?>
		<?php endif; ?>
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
