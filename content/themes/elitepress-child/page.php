<?php 
get_header();
get_template_part('index', 'banner');
?>
<!-- Blog Full Width Section -->
<div class="blog-section">
	<div class="container">
		<div class="row">
		
			<!--Blog Area-->
			<div class="<?php elitepress_post_layout_class(); ?>" >
			<?php get_template_part('content',''); ?>
			<?php comments_template('',true); ?>
			</div>
			<!--/Blog Area-->
			<?php get_sidebar(); ?>
		</div>
		
		
	</div>
	
</div>
<?php get_footer(); ?>
<!-- /Blog Full Width Section -->	