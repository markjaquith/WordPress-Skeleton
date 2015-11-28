<?php
// function for post meta
if ( ! function_exists( 'elitepress_post_meta_content' ) ) :

function elitepress_post_meta_content()
{ ?>
   
	        <!--show date of post-->
			
			<div class="blog-post-info-detail">
				<span class="blog_tags">
						<?php _e('By','elitepress');?><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) );?>"><?php the_author_link();?></a>
								
						<?php _e('On','elitepress');?><a href="<?php the_permalink();?>"><?php echo get_the_date(); ?></a>
								
						<?php 	$tag_list = get_the_tag_list();
						if(!empty($tag_list)) { ?>
						<div class="blog-tags"><?php _e('In','elitepress');?><?php the_tags('', ', ', ''); ?>,<?php 	$cat_list = get_the_category_list();
							if(!empty($cat_list)) { ?><?php the_category(', '); ?><?php } ?>
						</div><?php } ?>
				</span>
			</div>
			
			
			<?php } endif;  
			// this functions accepts two parameters first is the preset size of the image and second  is for additional classes, you can also add yours 
			if(!function_exists( 'elitepress_post_thumbnail')) : 

			function elitepress_post_thumbnail($preset,$class){
			if(has_post_thumbnail()){ 
			$defalt_arg =array('class' => $class);
						if(!is_single()){?>
			
			<div class="blog-post-img">
					<?php the_post_thumbnail($preset, $defalt_arg); ?>
					<div class="post-date"><h3><?php echo get_the_date('j'); ?></h3><span><?php echo get_the_date('M'); ?></span>
					</div>
			</div>
			
			<?php }
			else { ?>
			<div class="blog-post-img">
				<?php the_post_thumbnail($preset, $defalt_arg);?>
				<div class="post-date"><h3><?php echo get_the_date('j'); ?></h3><span><?php echo get_the_date('M'); ?></span></div>
			</div>
			<?php } } } endif;
			// this functions accepts one parameters for image class
			if(!function_exists( 'elitepress_full_thumbnail')) : 					
			function elitepress_image_thumbnail($preset,$class){
			if(has_post_thumbnail()){ 
			$defalt_arg =array('class' => $class);
						the_post_thumbnail($preset, $defalt_arg);} } endif;
			// This Function Check whether Sidebar active or Not
			if(!function_exists( 'elitepress_post_layout_class' )) :

			function elitepress_post_layout_class(){
				if(is_active_sidebar('sidebar_primary'))
					{ echo 'col-md-8'; } 
				else 
					{ echo 'col-md-12'; }  
			 
			} endif; 
			?>