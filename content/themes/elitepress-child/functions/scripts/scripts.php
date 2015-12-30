<?php
	
if (!functions_exists('elitepress_scripts')) {
	function elitepress_scripts()
	{	
		$current_options = get_option('elitepress_lite_options');
		$webriti_stylesheet = $current_options['webriti_stylesheet'];
		wp_enqueue_style('elitepress-style', get_stylesheet_uri() );
		wp_enqueue_style('font-awesome-min','//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');	
		wp_enqueue_style('elitepress-bootstrap', WEBRITI_TEMPLATE_DIR_URI . '/css/bootstrap.css');
		wp_enqueue_style('elitepress-default', WEBRITI_TEMPLATE_DIR_URI . '/css/default.css');
		wp_enqueue_style('elitepress-theme-menu', WEBRITI_TEMPLATE_DIR_URI . '/css/theme-menu.css');
		wp_enqueue_style('elitepress-media-responsive', WEBRITI_TEMPLATE_DIR_URI . '/css/media-responsive.css');
		wp_enqueue_style('elitepress-font', WEBRITI_TEMPLATE_DIR_URI . '/css/font/font.css');	
		wp_enqueue_script('elitepress-menu', WEBRITI_TEMPLATE_DIR_URI .'/js/menu/menu.js',array('jquery'));
		wp_enqueue_script('bootstrap', WEBRITI_TEMPLATE_DIR_URI .'/js/bootstrap.min.js');
		wp_enqueue_style('elitepress-flexslider', WEBRITI_TEMPLATE_DIR_URI . '/css/flexslider/flexslider.css');
		wp_enqueue_script('elitepress-jquery-flexslider', WEBRITI_TEMPLATE_DIR_URI .'/js/flexslider/jquery.flexslider.js');	
		wp_enqueue_script('jquery-flex-element', WEBRITI_TEMPLATE_DIR_URI .'/js/flexslider/flexslider-element.js');		
		wp_enqueue_script( 'jquery' );
	}
}	
add_action('wp_enqueue_scripts', 'elitepress_scripts');
if ( is_singular() ){ wp_enqueue_script( "comment-reply" );	}
?>