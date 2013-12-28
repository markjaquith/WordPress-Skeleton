<?php
add_action( 'widgets_init', 'ci_widgets_init' );
if( !function_exists('ci_widgets_init') ):
function ci_widgets_init() {

	register_sidebar(array(
		'name' => __( 'Blog Sidebar', 'ci_theme'),
		'id' => 'blog-sidebar',
		'description' => __( 'The widgets of this sidebar will appear on your post and blog pages.', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s group">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'Pages Sidebar', 'ci_theme'),
		'id' => 'page-sidebar',
		'description' => __( 'The widgets of this sidebar will appear on your static pages.', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s group">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'Header Social Widget', 'ci_theme'),
		'id' => 'header-wgt',
		'description' => __( 'Place here the Socials Ignited Widget and it will be displayed in your header. You need to download the Socials Ignited plugin from http://wordpress.org/extend/plugins/socials-ignited/', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="widget seven columns %2$s group">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'Services Sidebar', 'ci_theme'),
		'id' => 'services-sidebar',
		'description' => __( 'The widgets of this sidebar will appear on your services page.', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s group">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'Footer Widgets', 'ci_theme'),
		'id' => 'footer-wgt-1',
		'description' => __( 'The widgets of this sidebar will appear on your footer.', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="four columns widget %2$s group">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'Front Page (Services)', 'ci_theme'),
		'id' => 'front-services-sidebar',
		'description' => __( 'The widgets of this sidebar will appear on your front page template, last thing within the Services section.', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s group info-item one-third columns">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'Front Page (Before Footer)', 'ci_theme'),
		'id' => 'front-before-footer',
		'description' => __( 'The widgets of this sidebar will appear on your front page template, last thing before the footer widget.', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s group eight columns">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));


}
endif;
?>
