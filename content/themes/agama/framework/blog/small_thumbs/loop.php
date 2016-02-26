<!-- Small Thumbs -->
<div class="small-thumbs">

	 <div class="entry clearfix">
		
		<?php if( has_post_thumbnail() ): ?>
		<!-- Entry Image -->
		<div class="entry-image">
		
			<?php if( get_theme_mod( 'agama_blog_thumbnails_permalink', true ) ): ?>
				<a href="<?php the_permalink(); ?>">
			<?php endif; ?>
			
				<img class="image_fade img-responsive image-grow" src="<?php echo agama_return_image_src('agama-blog-small'); ?>" alt="<?php the_title(); ?>">
			
			<?php if( get_theme_mod( 'agama_blog_thumbnails_permalink', true ) ): ?>
				</a>
			<?php endif; ?>
			
		</div><!--.entry-image-->
		<?php endif; ?>
		
		<div class="entry-c">
			
			<!-- Entry Title -->
			<div class="entry-title">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			</div><!--.entry-title-->
			
			<!-- Entry Meta -->
			<ul class="entry-meta clearfix">
				<li><i class="fa fa-calendar"></i> <?php the_time('m, Y'); ?></li>
				<li><a href="<?php the_author_link(); ?>"><i class="fa fa-user"></i> <?php the_author(); ?></a></li>
				<li><i class="fa fa-folder-open"></i> <?php echo get_the_category_list(', '); ?></li>
				<li><a href="<?php the_permalink(); ?>#comments"><i class="fa fa-comments"></i> <?php echo Agama::comments_count(); ?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo Agama::post_format(); ?></a></li>
			</ul><!--.entry-meta-->
			
			<!-- Entry Content -->
			<div class="entry-content">
				
				<?php the_excerpt(); ?>

			</div><!--.entry-content -->
			
		</div>
	
	</div>

</div><!--.small_thumbs-->