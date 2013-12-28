<?php
/*
 * Template Name: Post Archives
 */
?>

<?php get_header(); ?>

<?php
	global $paged;
	$arrParams = array(
		'paged' => $paged,
		'ignore_sticky_posts'=>1,
		'showposts' => ci_setting('archive_no'));
	query_posts($arrParams);
?>

<section id="page-content">
	<div class="container">
		<article class="row">

			<div id="single-head" class="sixteen columns">
				<h1><?php the_title(); ?></h1>
				<?php if (has_excerpt()) { the_excerpt(); } ?>
			</div>

			<div class="twelve columns">
				<article id="post-<?php the_ID(); ?>" <?php post_class('entry group'); ?>>
					<h3 class="hdr"><?php _e('Latest Posts', 'ci_theme'); ?></h3>
					<ul class="lst archive">
						<?php while (have_posts() ) : the_post(); ?>
						<li><a href="<?php the_permalink(); ?>" title="<?php _e('Permalink to:', 'ci_theme'); ?> <?php the_title(); ?>"><?php the_title(); ?></a> - <?php echo get_the_date(); ?><?php the_excerpt(); ?></li>
						<?php endwhile; ?>
					</ul>

					<?php if (ci_setting('archive_week')=='enabled'): ?>
					<h3 class="hdr"><?php _e('Weekly Archive', 'ci_theme'); ?></h3>
					<ul class="lst archive"><?php wp_get_archives('type=weekly&show_post_count=1') ?></ul>
					<?php endif; ?>

					<?php if (ci_setting('archive_month')=='enabled'): ?>
					<h3 class="hdr"><?php _e('Monthly Archive', 'ci_theme'); ?></h3>
					<ul class="lst archive"><?php wp_get_archives('type=monthly&show_post_count=1') ?></ul>
					<?php endif; ?>

					<?php if (ci_setting('archive_year')=='enabled'): ?>
					<h3 class="hdr"><?php _e('Yearly Archive', 'ci_theme'); ?></h3>
					<ul class="lst archive"><?php wp_get_archives('type=yearly&show_post_count=1') ?></ul>
					<?php endif; ?>
				</article>
			</div>

			<?php get_sidebar(); ?>
		</article>
	</div> <!-- .container < #page-content -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
