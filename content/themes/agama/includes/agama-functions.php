<?php

// Include framework init file
get_template_part('framework/framework-init');

// Include Agama class
get_template_part('includes/agama-class');

// Include WooCommerce class file
get_template_part('includes/woocommerce');

// Custom header support
get_template_part('includes/custom-header');

if ( ! isset( $content_width ) ) 
	$content_width = 1200;

/**
 * Agama setup
 *
 * @since Agama 1.0
 */
function agama_setup() {
	/*
	 * Makes Agama available for translation.
	 */
	load_theme_textdomain( 'agama', AGAMA_DIR.'languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
	
	// Adds support for title tag
	add_theme_support( 'title-tag' );

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

	register_nav_menu( 'top', __( 'Top Menu', 'agama' ) );
	register_nav_menu( 'primary', __( 'Primary Menu', 'agama' ) );

	/*
	 * This theme supports custom background color and image,
	 * and here we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'e6e6e6',
	) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 800, 9999 ); // Unlimited height, soft crop
	
	// Register custom image sizes
	add_image_size( 'agama-blog-large', 776, 310, true );
    add_image_size( 'agama-blog-medium', 320, 202, true );
	add_image_size( 'agama-blog-small', 400, 300, true );
    add_image_size( 'agama-related-img', 180, 138, true );
    add_image_size( 'agama-recent-posts', 700, 441, true );
	
	/*
	 * Declare WooCommerce Support
	 */
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'agama_setup' );

/**
 * Backwards Compatibility for Title Tag
 *
 * @since Agama 1.0
 */
if ( ! function_exists( '_wp_render_title_tag' ) ) {
function agama_slug_render_title() {
?>
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php
	}
	add_action( 'wp_head', 'agama_slug_render_title' );
}

/**
 * Agama Thumb Title
 * Get post-page article title and separates it on two halfs
 *
 * @since Agama v1.0.1
 * @return string
 */
function agama_thumb_title() {
	$title = get_the_title();
	$findme   = ' ';
	$pos = strpos($title, $findme);
	if( $pos === false ) {
			$output = '<h2>'.$title.'</h2>';
		} else {
			// isolate part 1 and part 2.
			$title_part_one = strstr($title, ' ', true); // As of PHP 5.3.0
			$title_part_two = strstr($title, ' ');
			$output = '<h2>'.$title_part_one.'<span>'.$title_part_two.'</span></h2>';
		}
	echo $output;
}

/**
 * Get Attachment Image Src
 *
 * @since Agama v1.0.1
 * @return string
 */
function agama_return_image_src( $thumb_size ) {
	$att_id	 = get_post_thumbnail_id();
	$att_src = wp_get_attachment_image_src( $att_id, $thumb_size );
	return esc_url($att_src[0]);
}

/**
 * Check if $page is template page
 *
 * @since Agama v1.0.1
 * @return string
 */
function agama_is_page_template( $page ) {
	if( is_page_template( 'page-templates/'.$page ) ) {
		return true;
	}
	return false;
}

/**
 * Filter the page menu arguments.
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since Agama 1.0
 */
function agama_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'agama_page_menu_args' );

if ( ! function_exists( 'agama_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since Agama 1.0
 */
function agama_content_nav( $html_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo esc_attr( $html_id ); ?>" class="navigation clearfix" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'agama' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'agama' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'agama' ) ); ?></div>
		</nav><!-- .navigation -->
	<?php endif;
}
endif;

if ( ! function_exists( 'agama_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own agama_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Agama 1.0
 */
function agama_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'agama' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'agama' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-wrap clearfix">
			
			<div class="comment-meta">
				<div class="comment-author vcard">
					<span class="comment-avatar clearfix">
						<?php echo get_avatar( $comment, 44 ); ?>
					</span>
				</div>
			</div><!-- .comment-meta -->

			<div class="comment-content comment">
				<div class="comment-author">
				<?php
				printf( '<a href="%1$s">%2$s %3$s</a>', get_permalink(), get_comment_author_link(),
							// If current post author is also comment author, make it known visually.
							( $comment->user_id === $post->post_author ) ? '<cite>' . __( 'author', 'agama' ) . '</cite>' : ''
				);
				printf( '<span><a href="%1$s"><time datetime="%2$s">%3$s</time></a></span>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'agama' ), get_comment_date(), get_comment_time() )
				);
				?>
				</div>
				<?php comment_text(); ?>
				<?php //edit_comment_link( __( '<i class="fa fa-edit"></i> Edit', 'agama' ), '<p class="edit-link">', '</p>' ); ?>
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .comment-content -->
			
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'agama' ); ?></p>
			<?php endif; ?>
			
		</div><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

if ( ! function_exists( 'agama_entry_meta' ) ) :
/**
 * Set up post entry meta.
 *
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own agama_entry_meta() to override in a child theme.
 *
 * @since Agama 1.0
 */
function agama_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'agama' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'agama' ) );

	$date = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date('c') ),
		esc_html( get_the_date() )
	);

	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'agama' ), get_the_author() ) ),
		get_the_author()
	);

	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	if ( $tag_list ) {
		$utility_text = __( 'This entry was posted in %1$s and tagged %2$s on %3$s<span class="by-author"> by %4$s</span>.', 'agama' );
	} elseif ( $categories_list ) {
		$utility_text = __( 'This entry was posted in %1$s on %3$s<span class="by-author"> by %4$s</span>.', 'agama' );
	} else {
		$utility_text = __( 'This entry was posted on %3$s<span class="by-author"> by %4$s</span>.', 'agama' );
	}

	printf(
		$utility_text,
		$categories_list,
		$tag_list,
		$date,
		$author
	);
}
endif;

/**
 * .article-wrapper Grid, List - Style
 *
 * @since Agama v1.0.1
 */
function agama_article_wrapper_class() {
	$blog_layout = get_theme_mod('agama_blog_layout', 'list');
	switch( $blog_layout ):
		
		case 'list':
			echo 'list-style';
		break;
		
		case 'grid':
			echo 'grid-style';
		break;
		
		case 'small_thumbs':
			echo 'small_thumbs';
		break;
	
	endswitch;
}

/**
 * Render HTML for blog post date / post format
 *
 * @since Agama v1.0.1
 */
if( ! function_exists( 'agama_render_blog_post_date' ) ) {
	function agama_render_blog_post_date() {
		global $post;
		
		// If not single post
		if( !is_single() ) {
			echo '<div class="entry-date">';
			echo '<div class="date-box updated">';
			echo sprintf( '<span class="date">%s</span>', get_the_time('d') ); // Get day
			echo sprintf( '<span class="month-year">%s</span>', get_the_time('m, Y') ); // Get month, year
			echo '</div>';
			echo '<div class="format-box">';
			echo sprintf( '%s', Agama::post_format() );
			echo '</div>';
			echo '</div><!-- .entry-date -->';
		}
	}
}
add_action( 'agama_blog_post_date_and_format', 'agama_render_blog_post_date', 10 );

/**
 * Render HTML blog post meta details
 *
 * @since Agama v1.0.1
 */
if( ! function_exists( 'agama_render_blog_post_meta' ) ) {
	function agama_render_blog_post_meta() {
		
		echo sprintf( 
			'%s <span class="vcard"><span class="fn">%s</span></span>', 
			'<i class="fa fa-user"></i>', get_the_author_link() 
		);
		
		echo '<span class="inline-sep">/</span>';
		
		echo sprintf( '%s <span>%s</span>', '<i class="fa fa-calendar"></i>', get_the_time('F j, Y') );
		
		// Output next details only on list blog layout or on single post page
		if( get_theme_mod('agama_blog_layout', 'list') == 'list' || is_single() ) {
			echo '<span class="inline-sep">/</span>';
			echo sprintf( '%s %s', '<i class="fa fa-folder-open"></i>', get_the_category_list(', ') );
			echo '<span class="inline-sep">/</span>';
			
			// Comments number
			if( comments_open() ) {
				echo sprintf( '%s <a href="%s">%s</a>', '<i class="fa fa-comments"></i>', get_comments_link(), get_comments_number().__( ' comments', 'agama' ) );
			}
		}
	}
}
add_action( 'agama_blog_post_meta', 'agama_render_blog_post_meta', 10 );

/**
 * Infinite Scroll Init
 * 
 * @since 1.0.3
 */
function agama_infinite_scroll_init() { ?>
<script>
jQuery(function($){
	<?php if( get_theme_mod('agama_blog_layout', 'list') == 'grid' && ! is_singular() ): ?>
	var $container = $('#content').imagesLoaded(function(){
	
		$container.isotope({
			itemSelector : '.article-wrapper'
		});
	  
		$container.infinitescroll({
			navSelector  : '.navigation',
			nextSelector : '.nav-previous a',
			itemSelector : '.article-wrapper',
			loading: {
				finishedMsg: '<?php _e( 'No more posts to load.', 'agama' ); ?>',
				img: '<?php echo AGAMA_IMG .'loader.gif'; ?>'
			  },
			errorCallback: function(){
				$('#infscr-loading').replaceWith(
					"<div id='infscr-loading'><?php _e( 'No more posts to load.', 'agama' ); ?></div>"
				);
			  },
			},
			// call Isotope as a callback
			function( newElements ) {
				$container.isotope( 'appended', $( newElements ) ); 
			}
		);
		
		<?php if( get_theme_mod('agama_blog_infinite_trigger', 'button') == 'button'): ?>
		jQuery(window).unbind('.infscr');
		jQuery('.navigation').css('display', 'none');
		
		jQuery('#infinite-loadmore').click(function() {
			jQuery('#content').infinitescroll('retrieve');
				return false;
		});
		
		jQuery(document).ajaxError(function(e,xhr,opt) {
			if(xhr.status==404)
				jQuery('.navigation .nav-previous a').remove();
		});
		<?php endif; ?>
	});
	<?php else: ?>
	jQuery('#content').infinitescroll({
		navSelector  : '.navigation',
		nextSelector : '.navigation .nav-previous a',
		itemSelector : '.article-wrapper',
		loading: {
			finishedMsg: '<?php _e( 'No more posts to load.', 'agama' ); ?>',
			img: '<?php echo AGAMA_IMG .'loader.gif'; ?>'
		},
	});
	
	<?php if( get_theme_mod('agama_blog_infinite_trigger', 'button') == 'button'): ?>
	jQuery(window).unbind('.infscr');
	jQuery('.navigation').css('display', 'none');
	
	jQuery('#infinite-loadmore').click(function() {
		jQuery('#content').infinitescroll('retrieve');
			return false;
	});
	
	jQuery(document).ajaxError(function(e,xhr,opt) {
		if(xhr.status==404)
			jQuery('.navigation .nav-previous a').remove();
	});
	<?php endif; ?>
	
	<?php endif; ?>
});
</script>
<?php }

/**
 * Agama Credits
 *
 * @since Agama v1.0.1
 */
if( ! function_exists( 'agama_render_credits' ) ) {
	function agama_render_credits() {
		echo html_entity_decode( get_theme_mod( 'agama_footer_copyright', sprintf( __( '2015 &copy; Powered by %s.', 'agama' ), '<a href="http://www.theme-vision.com" target="_blank">Theme-Vision</a>' ) ) );
	}
}
add_action( 'agama_credits', 'agama_render_credits' );