<?php
$current_options = get_option('elitepress_lite_options',theme_data_setup()); 
get_header();
get_template_part('index', 'banner');
?>
<!-- Blog Full Width Section -->
<div class="blog-section">
	<div class="container">
		<div class="row">
		
			<!--Blog Area-->
					<div class="<?php elitepress_post_layout_class(); ?>" >
					<?php
					if($current_options['home_post_enabled']=='on')
					{
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					$args = array( 'post_type' => 'post','paged'=>$paged);		
					$post_type_data = new WP_Query( $args );
					}
					else
					{  
					$arr=explode(",",$current_options['featured_slider_post']);
					$args = array('post__not_in' => $arr);
					query_posts( $args );
					}
					$i=1;
					while( have_posts() ) : the_post();	
					?>
					<?php get_template_part('content',''); ?>
					<?php 
					$i++; wp_reset_postdata();endwhile; ?>
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