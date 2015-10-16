<?php 
get_header();
get_template_part('banner','header'); ?>

<!-- Blog Full Width Section -->
<div class="blog-section">
	<div class="container">
		<div class="row">
			<!--Blog Area-->
			<div class="<?php elitepress_post_layout_class(); ?>" >
				<?php if ( have_posts() ) { ?>
				<h1 class="search_heading">
				<?php printf( __( "Search Results For: %s", 'elitepress' ), '<span>' . get_search_query() . '</span>' ); ?>
				</h1>
				<?php while ( have_posts() ) { the_post();
				global $more;
				$more = 0;
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class('blog-area-full'); ?>>
				<?php if(has_post_thumbnail()){
				$defalt_arg =array('class' => "img-responsive"); ?>
					<div class="blog-post-img">
						<?php the_post_thumbnail('webriti_blogfull_img', $defalt_arg); ?>
						<div class="post-date"><h3><?php echo get_the_date('j'); ?></h3><span><?php echo get_the_date('M'); ?></span></div>
					</div>
					<?php } ?>
					<div class="blog-info">
						<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
						<div class="blog-seprator"></div>
						<div class="blog-post-info-detail">
							<?php _e('By', 'elitepress'); ?><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a>
							<?php 	$tag_list = get_the_tag_list();
							if(!empty($tag_list)) { ?>
							<div class="blog-tags"><?php _e('IN', 'elitepress')?> <a href="<?php the_permalink(); ?>"><?php the_tags('', ', ', ''); ?></a>
							</div>
							<?php } ?>
						</div>
						<div class="blog-description"><?php the_content( __( 'Read More' , 'elitepress' ) ); ?></div>
						<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'elitepress' ), 'after' => '</div>' ) ); ?>
					</div>
				</div>
				<?php }	?>
				<div class="blog-pagination">
				<?php next_posts_link( __('Previous','elitepress') ); ?>
				<?php previous_posts_link( __('Next','elitepress') ); ?>
				</div>
				<?php } else { ?>
				<div class="search_error">
					<div class="search_err_heading"><h2><?php _e( "Nothing Found", 'elitepress' ); ?></h2> </div>
					<div class="elitepress_searching">
						<p><?php _e( "Sorry, but nothing matched your search criteria. Please try again with some different keywords.", 'elitepress' ); ?></p>
					</div>
				</div>
			<?php get_search_form();
			} ?>
			</div>
			<!--/Blog Area-->
			<?php get_sidebar(); ?>
		</div>	
	</div>
</div>
<?php get_footer(); ?>
<!-- /Blog Full Width Section -->
<div class="clearfix"></div>