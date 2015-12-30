<?php
/**Theme Name	: elitepress
 * Theme Core Functions and Codes
*/	
	/**Includes reqired resources here**/
	define('WEBRITI_TEMPLATE_DIR_URI',get_template_directory_uri());	
	define('WEBRITI_TEMPLATE_DIR',get_template_directory());
	define('WEBRITI_THEME_FUNCTIONS_PATH',WEBRITI_TEMPLATE_DIR.'/functions');	
	define('WEBRITI_THEME_OPTIONS_PATH',WEBRITI_TEMPLATE_DIR_URI.'/functions/theme_options');
	require( WEBRITI_THEME_FUNCTIONS_PATH . '/menu/default_menu_walker.php'); 
	require( WEBRITI_THEME_FUNCTIONS_PATH . '/menu/webriti_nav_walker.php'); 
	require( WEBRITI_THEME_FUNCTIONS_PATH . '/widget/custom-sidebar.php');
	require_once( WEBRITI_THEME_FUNCTIONS_PATH . '/scripts/scripts.php');
	require( WEBRITI_THEME_FUNCTIONS_PATH . '/meta-box/post-meta.php');
	require( WEBRITI_THEME_FUNCTIONS_PATH . '/template-tag.php');
	require( WEBRITI_THEME_FUNCTIONS_PATH . '/font/font.php');
	
	//wp title tag starts here
	function elitepress_head( $title, $sep ) {
	        global $paged, $page;
	
	        if ( is_feed() )
	                return $title;
	
     // Add the site name.
        $title .= get_bloginfo( 'name', 'display' );
		        // Add the site description for the home/front page.
      $site_description = get_bloginfo( 'description', 'display' );
	        if ( $site_description && ( is_home() || is_front_page() ) )
                $title = "$title $sep $site_description";
	 // Add a page number if necessary.
        if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
	                $title = "$title $sep " . sprintf( __( 'Page %s', 'elitepress' ), max( $paged, $page ) );	
        return $title;
	}	
	add_filter( 'wp_title', 'elitepress_head', 10, 2);
	
	add_action( 'after_setup_theme', 'elitepress_setup' ); 	
	function elitepress_setup()
	{
		global $content_width;
		if ( ! isset( $content_width ) ) $content_width = 600;//In PX */
		
		// Load text domain for translation-ready
		load_theme_textdomain( 'elitepress', WEBRITI_THEME_FUNCTIONS_PATH . '/lang' );
		
		add_theme_support( 'post-thumbnails' ); //supports featured image
		// This theme uses wp_nav_menu() in one location.
		register_nav_menu( 'primary', __( 'Primary Menu', 'elitepress' ) ); //Navigation
		register_nav_menu( 'footer_menu', __( 'Footer Menu', 'elitepress' ) );
		// theme support 	
		$args = array('default-color' => '000000',);
		add_theme_support( 'custom-background', $args  ); 
		add_theme_support( 'automatic-feed-links');
		
		require_once('theme_setup_data.php');
		require( WEBRITI_THEME_FUNCTIONS_PATH . '/theme_options/option_pannel.php' ); // for Option Panel Settings		
	}
	
	function elitepress_add_gravatar_class($class) {
		$class = str_replace("class='avatar", "class='img-responsive comment-img", $class);
		return $class;
	}
	add_filter('get_avatar','elitepress_add_gravatar_class');

	function elitepress_excerpt_length($length ) {
	return 20;
	}
	add_filter( 'excerpt_length', 'elitepress_excerpt_length', 999 );
	
	add_filter('get_the_excerpt','elitepress_post_slider_excerpt');
	add_filter('excerpt_more','__return_false');
	function elitepress_post_slider_excerpt($output){
	
		return '<div class="slide-text-bg2">' .'<h3>'.$output.'</h3>'.'</div>'.
		       '<div class="flex-btn-div"><a href="' . get_permalink() . '" class="btn1 flex-btn">Read More</a></div>';
			}
		

function elitepress_get_custom_link($url,$target,$title)
{
	if($title)
	{
		if(($url!='') && $url!='#')
		{?>
			<a href="<?php echo $url; ?>" <?php if($target=='on' || $target==true){ echo 'target="_blank"'; } ?> >
			<?php echo $title; ?>
			</a>
		<?php
		}else {
		echo '<p>'.$title.'</p>';
		}
	}
}
?>