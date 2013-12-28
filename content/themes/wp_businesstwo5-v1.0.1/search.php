<?php get_header(); ?>
<?php
global $wp_query;

$found = $wp_query->post_count > $wp_query->found_posts ? $wp_query->post_count : $wp_query->found_posts;
$none = __('No results found. Please broaden your terms and search again.', 'ci_theme');
$one = __('Just one result found. We either nailed it, or you might want to broaden your terms and search again.', 'ci_theme');
$many = sprintf(__("%d results found.", 'ci_theme'), $found);
?>

<section id="page-content">
	<div class="container">
		<article class="row">
			<div id="single-head" class="sixteen columns">
				<?php get_template_part('part-hero'); ?>
			</div>

			<div class="twelve columns">
				<article <?php post_class('entry row'); ?>>
						<h4><?php ci_e_inflect($found, $none, $one, $many); ?></h4>
						<?php if($found==0) get_search_form(); ?>
				</article>

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('entry group'); ?>>
					<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(sprintf(__('Permanent link to: %s', 'ci_theme'), get_the_title())); ?>"><?php the_title(); ?></a></h1>

					<div class="entry-meta">
						<?php _e('Posted at', 'ci_theme'); ?> <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date(); ?></time>,	<?php _e('in', 'ci_theme'); ?> <?php the_category(', '); ?>
					</div>

					<?php if ( ci_has_image_to_show() ) : ?>
					<?php $alignclass = is_singular() ? ci_setting('featured_single_align') : ''; ?>
					<figure class="entry-thumb <?php echo $alignclass; ?>">
						<?php if ( is_singular() ): ?>
									<a href="<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'large') ); ?>" class="fancybox">
										<?php ci_the_post_thumbnail(array('class'=>'scale-with-grid')); ?>
						<?php else: ?>
									<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(sprintf(__('Permanent link to: %s', 'ci_theme'), get_the_title())); ?>">
										<?php the_post_thumbnail('ci_listing_thumb', array('class'=>'scale-with-grid')); ?>
						<?php endif; ?>
					</a>
					</figure>
					<?php endif; ?>

					<?php ci_e_content(); ?>
					<?php ci_read_more(); ?>
				</article>
				<?php endwhile; endif; ?>

				<?php ci_pagination(); ?>
			</div>

			<?php get_sidebar(); ?>
		</article>
	</div> <!-- .container < #page-content -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
