<?php 
/**
Template Name: Fullwidth
*/
get_header();
get_template_part('index', 'banner');
?>
<!-- Blog Full Width Section -->
<div class="blog-section">
	<div class="container">
		<div class="row">
			<!--Blog Area-->
			<div class="col-md-12">
			<?php get_template_part('content',''); ?>
			</div>
			<!--/Blog Area-->
			
		</div>
		<?php comments_template('',true); ?>
	</div>
</div>
<?php get_footer(); ?>
<!-- /Blog Full Width Section -->	