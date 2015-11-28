<?php 
get_header(); ?>
<!-- Page Title Section -->
<?php get_template_part('banner','header'); ?>
<div class="clearfix"></div>
<!-- /Page Title Section -->
	
<!-- Blog Full Width Section -->
<div class="blog-section">
	<div class="container">
		<div class="row">
			
			<!--Blog Area-->
			<div class="<?php elitepress_post_layout_class(); ?>" >
			<h1 class="blog_detail_head">
			<?php  _e( "Category  Archives:", 'elitepress' ); echo single_cat_title( '', false ); ?>
			</h1>	
				<?php
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$category_id=get_query_var('cat');
				$args = array( 'post_type' => 'post','paged'=>$paged,'cat' => $category_id);
				$post_type_data = new WP_Query( $args );
				while($post_type_data->have_posts()){
				$post_type_data->the_post();
				global $more;
				$more = 0;
				get_template_part('content',''); ?>
				<?php }	?>			
				<div class="blog-pagination">
					<?php previous_posts_link( __('Previous','elitepress') ); ?>
					<?php next_posts_link( __('Next','elitepress') ); ?>
				</div>
				</div>
			<!--/Blog Area-->
			<?php get_sidebar(); ?>
		</div>	
	</div>
</div>
<?php get_footer(); ?>
<!-- /Blog Full Width Section -->
<div class="clearfix"></div>	