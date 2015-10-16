<?php 
get_header(); ?>
<?php get_template_part('banner','header'); ?>
<!-- Blog Full Width Section -->
<div class="blog-section">
	<div class="container">
		<div class="row">
			
			<!--Blog Area-->
				<div class="<?php elitepress_post_layout_class(); ?>" >
				<h1 class="blog_detail_head">
				<?php  _e( "Tag Archives:", 'elitepress' ); echo single_tag_title( '', false ); ?>
				</h1>
				<?php
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$tag_id=get_query_var('tag_id');
				$args = array( 'post_type' => 'post','paged'=>$paged,'tag_id' => $tag_id);		
				$post_type_data = new WP_Query( $args );
				while($post_type_data->have_posts()){
				$post_type_data->the_post();
				global $more;
				$more = 0;
				?>
				<?php get_template_part('content',''); ?>
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