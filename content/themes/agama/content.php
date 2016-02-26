<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package Theme-Vision
 * @subpackage Agama
 * @since Agama 1.0
 */
 $blog_layout = get_theme_mod('agama_blog_layout', 'list');
 ?>

<div class="article-wrapper <?php agama_article_wrapper_class(); ?> clearfix">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/**
	 * Select Blog Layout
	 *
	 * @since 1.1.1
	 */
	switch( $blog_layout ):
	
		case $blog_layout == 'list' && ! is_single():
			get_template_part('framework/blog/list/loop');
		break;
		
		case $blog_layout == 'list' && is_single():
			get_template_part('framework/blog/list/single');
		break;
		
		case $blog_layout == 'grid' && ! is_single():
			get_template_part('framework/blog/grid/loop');
		break;
		
		case $blog_layout == 'grid' && is_single():
			get_template_part('framework/blog/grid/single');
		break;
		
		case $blog_layout == 'small_thumbs' && ! is_single():
			get_template_part('framework/blog/small_thumbs/loop');
		break;
		
		case $blog_layout == 'small_thumbs' && is_single():
			get_template_part('framework/blog/small_thumbs/single');
		break;
	
	endswitch; ?>
	</article><!-- #post -->
</div><!-- .article-wrapper -->