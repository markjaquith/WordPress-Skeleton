<header class="entry-header">

	<?php if ( ! post_password_required() && ! is_attachment() && get_the_post_thumbnail() && ! is_search() ) { // Attachments ?>
	
		<figure class="effect-bubba" data-src="<?php echo agama_return_image_src('agama-blog-large'); ?>">
			<img src="<?php echo agama_return_image_src('agama-blog-large'); ?>" class="img-responsive">
			<figcaption></figcaption>
		</figure>
	
	<?php } ?>
	
	<h1 class="entry-title"><?php the_title(); ?></h1>
	
	<?php
	/**
	 * agama_blog_post_meta hook
	 *
	 * @hooked agama_render_blog_post_meta - 10  (output HTML post meta details)
	 */
	echo '<p class="single-line-meta">';
	do_action( 'agama_blog_post_meta' );
	echo '</p>';
	?>

</header>

<?php if( ! is_sticky() ): ?>
	<div class="entry-sep"></div>
<?php endif; ?>

<div class="article-entry-wrapper">

	<?php if ( is_sticky() && is_home() && ! is_paged() ) { // Sticky post ?>
		<div class="featured-post">
			<?php _e( 'Featured post', 'agama' ); ?>
		</div>
	<?php } ?>
	
	<div class="entry-content">
		
		<?php the_content(); ?>
		
		<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'agama' ), 'after' => '</div>' ) ); ?>
	</div>
	
	<!-- Content Footer -->
	<footer class="entry-meta">
		
		<?php edit_post_link( __( 'Edit', 'agama' ), '<span class="edit-link">', '</span>' ); ?>
		
		<?php Agama::about_author(); ?>
		
	</footer><!-- .entry-meta -->

</div>