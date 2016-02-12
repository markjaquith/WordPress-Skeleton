<?php
/**Theme Name	: elitepress-child
 * Theme Core Functions and Codes
*/
/**Includes reqired resources here**/
define('MY_TEMPLATE_DIR_URI', get_stylesheet_directory_uri());
define('MY_TEMPLATE_DIR',get_stylesheet_directory());
define('MY_THEME_FUNCTIONS_PATH',MY_TEMPLATE_DIR.'/functions');

require( MY_THEME_FUNCTIONS_PATH . '/widget/custom-sidebar.php');
require_once( MY_THEME_FUNCTIONS_PATH . '/scripts/scripts.php');

add_action ('wp_enqueue_scripts','theme_enqueue_style');
function theme_enqueue_style() {
	wp_enqueue_style ('parent-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style( 'child-style', get_stylesheet_uri(), array('parent-style')  );
	wp_enqueue_script('utility', get_stylesheet_directory_uri() .'/js/utility.js');
}

//Filters
//Functions dealing with manipulating the excerpt of posts
add_filter('get_the_excerpt','my_post_slider_excerpt');
function my_post_slider_excerpt($output){

		return '<p>'.$output.'</p>';
}

add_filter( 'excerpt_length', 'my_excerpt_length', 1000 );	//returns the length of the excerpt in words. In this case it is 15 words extracted from the excerpt
function my_excerpt_length($length) {
	return 15;
}

function my_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'my_excerpt_more', 1000 );

/**
 * Buddypress
 */
//Functions and methods for removing and stopping wordpress from loading parent functions
function remove_parent_post_slider_excerpt() {	//This function is created to remove and stop the 'parent_post_slider_excerpt' function from executing.
    remove_filter('get_the_excerpt','elitepress_post_slider_excerpt'); //This WordPress API hook/function removes the function, elitepress_post_slider_excerpt from the parent.
}
add_action( 'wp_loaded', 'remove_parent_post_slider_excerpt' ); //This WordPress API hook/function loads and executes the 'remove_parent_post_slider_excerpt'

function remove_parent_enqueue_scripts() {	//This function is used to remove and stop the 'parent_post_slider_excerpt' function from executing
    remove_action('wp_enqueue_scripts','elitepress_scripts');
}
add_action( 'wp_loaded', 'remove_parent_enqueue_scripts' );

function mpp_custom_restrict_group_upload( $can_do, $component, $component_id, $gallery  ) {

	if ( $component != 'groups' ) {
		return $can_do;
	}
	//we only care about group upload
	$gallery = mpp_get_gallery( $gallery );

	if ( ! $gallery || $gallery->user_id != get_current_user_id() ) {
		return false;//do not allow if gallery is not given
	}

	return true;//the user had created this gallery

}
add_filter( 'mpp_user_can_upload', 'mpp_custom_restrict_group_upload', 11, 4 );
//shortcodes
 //start of shortcode
function bpProfile( $atts=null, $content=null ) { //shortcode for returning the url of an artist members profile
global $user_ID;

if ( is_user_logged_in() ) {
return '<a href='.bp_core_get_user_domain( $user_ID ).'profile/>Back to my profile</a>';
} else {
return "";
}
}
add_shortcode('bpProfile','bpProfile');//end of shortcode

/**
 * WooCommerce
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10); //Unhooks sidebar

//Removes woocommerce's start and end content wrapper and uses my start and end custom wrapper instead
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);//Removes start wrapper
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);//Removes end wrapper

add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);//Adds my start wrapper
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);//Adds my end wrapper

function my_theme_wrapper_start() {//my start custom wrapper
  echo '<div class="container"><div class="row"><div class="col-md-9">';
}

function my_theme_wrapper_end() {//my end wrapper
  echo '</div></div></div>';
}

//code for declaring that elite-press child theme supports WooCommerce
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

?>
