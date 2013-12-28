<?php get_header(); ?>

<section id="page-content">
	<div class="container">
		<article class="row">

			<div id="single-head" class="sixteen columns">
				<h1><?php _e("Page Not Found", 'ci_theme'); ?></h1>
				<p><?php _e('The page you are looking for was not found. Perhaps try searching?', 'ci_theme'); ?></p>
			</div>

			<div class="twelve columns">
				<article <?php post_class('entry group'); ?>>
					<?php get_search_form(); ?>
				</article>
			</div>

			<?php get_sidebar(); ?>
		</article>
	</div> <!-- .container < #page-content -->
</section> <!-- #page-content -->

<?php get_footer(); ?>
