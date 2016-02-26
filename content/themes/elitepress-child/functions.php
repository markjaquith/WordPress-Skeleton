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

function my_bp_core_activated_user(  $user_id, $key, $user ) {
	$user = get_user_by('id', $user_id);
	$user->add_role('vendor');
};
add_action( 'bp_core_activated_user', 'my_bp_core_activated_user', 10, 3 );

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
//remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10); //Unhooks sidebar

//Removes woocommerce's start and end content wrapper and uses my start and end custom wrapper instead
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);//Removes start wrapper
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);//Removes end wrapper

add_action('woocommerce_before_main_content', 'my_theme_wrapper_start', 10);//Adds my start wrapper
add_action('woocommerce_after_main_content', 'my_theme_wrapper_end', 10);//Adds my end wrapper

function my_theme_wrapper_start() {//my start custom wrapper
  echo '<div class="container"><div class="row">';
}

function my_theme_wrapper_end() {//my end wrapper
  echo '</div></div>';
}

//code for declaring that elite-press child theme supports WooCommerce
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

remove_filter('woocommerce_breadcrumb_defaults');//Removes filter that styles and adds a breadcrumb

add_filter('woocommerce_breadcrumb_defaults', 'my_breadcrumb_default', 10); //Add my own
function my_breadcrumb_default(){
	return array(
		'delimiter'   => '&nbsp;&#47;&nbsp;',
    'wrap_before' => '<nav class="woocommerce-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
    'wrap_after'  => '</nav><hr class="breadcrumb-hr">',
    'before'      => '',
    'after'       => '',
    'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' )
	);
}

/* Adds View Store button to BuddyPress profiles */
add_action('bp_member_header_actions', 'wcvendors_pro_bp_member_header_actions');
function wcvendors_pro_bp_member_header_actions(){

				$wcv_profile_id  = bp_displayed_user_id(); //gets member id of the current displayed member's profile page
				$shop_name =  sanitize_title(get_user_meta( $wcv_profile_id, 'pv_shop_name', true )); //gets shop name and makes it url friendly
				$home_url = get_home_url(); //gets website's home url
				//generates a button with link url pointing to the users shop
				$sold_by = "<div class=\"generic-button\"><a class=\"send-message\"href=\"$home_url/vendors/" . $shop_name . "/\">Visit My Store</a></div>";


        $wcv_profile_info = get_userdata( bp_displayed_user_id() ); //get userdata profile data by current member's profile id
				$wcv_store_name =  get_user_meta( $wcv_profile_id, 'pv_shop_name', true);//gets shop name of user based on user id
				$user_roles = $wcv_profile_info->roles; //get a list of the user's roles
				$vendor_role = ''; //initialises to varable string to empty string
				//sets assigns "vendor" string to $vendor_role if vendor role is part of the user's role
				if (in_array("vendor", $user_roles)) {//returns true if "vendor" key is in array
    				$vendor_role = "vendor"; //if true $vendor variable is assigned a value of vendor"
				}

        if ( $vendor_role == "vendor" && $wcv_store_name) { //checks if user is a vendor and user has set a store name
            echo $sold_by;
        }
				else {
						echo '';
				}
}

/* Adds a View Profile link on the vendors store header */
add_action('wcv_after_main_header', 'custom_wcv_after_vendor_store_title'); //action for adding content to artist's shop page
function custom_wcv_after_vendor_store_title() {
        $wcv_profile_id = get_query_var('author'); //gets id of current author archive e.g.artists product list/archive
        $profile_url = bp_core_get_user_domain ( $wcv_profile_id ); //gets url of artist's buddypress profile page
        echo '<div class="col-md-12 seller-link"><a href="'. $profile_url .'" class=""><strong>View Seller Profile</strong></a></div>';
}

/* Adds a link to Profile on Single Product Pages */
add_action('woocommerce_product_meta_start', 'custom_woocommerce_product_meta_start');
function custom_woocommerce_product_meta_start() {
        $wcv_profile_id = get_the_author_meta('ID');
        $profile_url = bp_core_get_user_domain ( $wcv_profile_id );
        echo 'Artist Seller Profile: <a href="'. $profile_url .'">View My Profile</a>';
}

//add_action('woocommerce_product_meta_start', 'wcv_bppm_woocommerce_product_meta_start');
//function wcv_bppm_woocommerce_product_meta_start() {
//        if ( is_user_logged_in() ) {
//	        $wcv_store_id =        get_the_author_meta('ID');
//	        $wcv_store_name =      get_user_meta( $wcv_store_id, 'pv_shop_name', true);
//	        echo '<br>Contact Vendor: <a href="' . bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . get_the_author_meta('user_login') .'">Contact ' . $wcv_store_name . '</a>';
//        } else {
        //$wcv_my_account_url = get_permalink( get_option('woocommerce_myaccount_page_id'));
        //echo '<br>Contact Vendor: <a href="' . $wcv_my_account_url . '">Login or Register to Contact Vendor</a>';
//					echo '';
//				}
//}

?>
