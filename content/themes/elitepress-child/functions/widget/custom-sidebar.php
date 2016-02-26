<?php
add_action( 'widgets_init', 'my_widgets_init',15);
function my_widgets_init() {
/*sidebar*/
register_sidebar( array(
		'name' => __( 'Sidebar Widget Area', 'elitepress' ),
		'id' => 'sidebar_primary',
		'description' => __( 'The right sidebar widget area', 'elitepress' ),
		'before_widget' => '<div class="sidebar-widget">',
		'after_widget' => '</div>',
		'before_title' => '<div class="sidebar-widget-title"><h4>',
		'after_title' => '</h4></div>',
) );

register_sidebar( array(
		'name' => __( 'Vendor Sidebar Widget Area', 'elitepress' ),
		'id' => 'sidebar_primary_two',
		'description' => __( 'The right sidebar widget area for non-users', 'elitepress' ),
		'before_widget' => '<div class="sidebar-widget">',
		'after_widget' => '</div>',
		'before_title' => '<div class="sidebar-widget-title"><h3>',
		'after_title' => '</h3></div>',
) );

register_sidebar( array(
		'name' => __( 'Footer Widget Area', 'elitepress' ),
		'id' => 'footer_widget_area',
		'description' => __( 'footer widget area', 'elitepress' ),
		'before_widget' => '<div class="col-md-3 col-sm-6 footer-widget-column">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="footer-widget-title">',
		'after_title' => '</h3>',
) );
register_sidebar( array(
		'name' => __( 'Shop Widget Area', 'elitepress' ),
		'id' => 'shop',
		'description' => __( 'Sidebar for Woocommerce', 'elitepress' ),
		'before_widget' => '<div class="sidebar-widget">',
		'after_widget' => '</div>',
		'before_title' => '<div class="sidebar-widget-title"><h3>',
		'after_title' => '</h3></div>',
) );
}
?>
