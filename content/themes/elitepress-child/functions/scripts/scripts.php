<?php
	function my_scripts()
	{	
		$current_options = get_option('elitepress_lite_options');
		$webriti_stylesheet = $current_options['webriti_stylesheet'];
		wp_enqueue_style('font-awesome-min','//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');	
		wp_enqueue_style('elitepress-bootstrap', MY_TEMPLATE_DIR_URI . '/css/bootstrap.css');
		wp_enqueue_style('elitepress-default', MY_TEMPLATE_DIR_URI . '/css/default.css');
		wp_enqueue_style('elitepress-theme-menu', MY_TEMPLATE_DIR_URI . '/css/theme-menu.css');
		wp_enqueue_style('elitepress-media-responsive', MY_TEMPLATE_DIR_URI . '/css/media-responsive.css');
		wp_enqueue_style('elitepress-font', MY_TEMPLATE_DIR_URI . '/css/font/font.css');	
		wp_enqueue_script('elitepress-menu', MY_TEMPLATE_DIR_URI .'/js/menu/menu.js',array('jquery'));
		wp_enqueue_script('bootstrap', MY_TEMPLATE_DIR_URI .'/js/bootstrap.min.js');
		wp_enqueue_style('elitepress-flexslider', MY_TEMPLATE_DIR_URI . '/css/flexslider/flexslider.css');
		wp_enqueue_script('elitepress-jquery-flexslider', MY_TEMPLATE_DIR_URI .'/js/flexslider/jquery.flexslider.js');	
		wp_enqueue_script('jquery-flex-element', MY_TEMPLATE_DIR_URI .'/js/flexslider/flexslider-element.js');	
		wp_enqueue_script( 'jquery' );
	}
	
add_action('wp_enqueue_scripts', 'my_scripts');
if ( is_singular() ){ wp_enqueue_script( "comment-reply" );	}
?>