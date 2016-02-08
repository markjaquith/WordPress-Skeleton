<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}
/**
 * MediaPress core actions
 * 
 * This section lists/exposes the actions that an addon plugin should use to build any addon for MediaPress
 * 
 * These provide actions for the plugin developers where they can hook without worrying if MediaPress is active or not.
 * These actions are just a layer over WordPress core actions
 * 
 */

/**
 * Actions not declared here but existing
 * 
 */

/***
 * [plugins_loaded] -> [mpp_loaded]
 * mpp_loaded is fired when all the required files of MediaPress is loaded. It is fired at plugins_loaded action.
 * Plugins should use mpp_loaded action to load the core files of their own plugin
 */

/***
 * [init] -> [mpp_init]
 * mpp_init action is equivalent to WordPress 'init' action. Use it to register post type or do nything initialization
 * This is the first action where current user will be available and properly set
 */

/**
 * The following provides an abstraction/interface for the dependent addons
 * It is a very specific sub set of WordPress( or currently BuddyPress ) actions that we feel are important
 * for the deveopment of MediaPress addons.
 * If you need a new hook, please do let us know.
 * 
 * The following actions are modeled after BuddyPress and we believe they have done it in the right way. 
 * 
 * In future, we will unattach the actions from BuddyPress and use WordPress core actions when we move towards non BP Galleries
 * 
 * The best thing is MediaPress addons won't have to worry about that if the hook to various mpp_{action_name}
 */

add_action( 'parse_query',				'mpp_parse_query', 2 ); //
add_action( 'wp',						'mpp_ready', 10 ); //wp
										
add_action( 'after_setup_theme',		'mpp_after_setup_theme', 10 ); // After WP themes
add_action( 'init',						'mpp_setup' , 0 );//first thing on itni
add_action( 'init',						'mpp_init' , 11 );//after buddypress, BP uses 10 priority
add_action( 'wp_enqueue_scripts',		'mpp_enqueue_scripts', 10 );//load front end js
add_action( 'admin_enqueue_scripts',	'mpp_admin_enqueue_scripts', 10 );//load admin js
add_action( 'admin_bar_menu',			'mpp_setup_admin_bar', 10 ); // admin_bar_menu
add_action( 'template_redirect',		'mpp_template_redirect', 9 );
add_action( 'widgets_init',				'mpp_widgets_init', 10 );


add_action( 'mpp_template_redirect',	'mpp_actions', 4 );
add_action( 'mpp_template_redirect',	'mpp_screens', 6 );
/**
 * fires on parse_query
 */
function mpp_parse_query() {
	do_action( 'mpp_parse_query' );
}
/**
 * fires on 'wp' action
 */
function mpp_ready() {
	do_action( 'mpp_ready' );
}

function mpp_after_setup_theme() {
	do_action( 'mpp_after_setup_theme' );
}
/**
 * Register post types, status etc here
 * 
 */
function mpp_setup() {
	do_action( 'mpp_setup' );
}

/**
 * All Initialization code shoud hook to this
 * Register post types, taxonomies or check for users
 * 
 */
function mpp_init() {
	do_action( 'mpp_init' );
}
/**
 * Register/enqueue scripts/styles on this action for front end loading
 * 
 */
function mpp_enqueue_scripts() {
	do_action( 'mpp_enqueue_scripts' );
}
/**
 * Register/enqueue scripts/styles on this action for loading on admin/dashboard
 * 
 */
function mpp_admin_enqueue_scripts() {
	do_action( 'mpp_admin_enqueue_scripts' );
}
/**
 * fires on admin_bar_menu
 * Are you adding a node to adminbar or removing a node from adminbar?
 * This is best suited for that
 */
function mpp_setup_admin_bar() {
	do_action( 'mpp_setup_admin_bar' );
}
/**
 * Do not directly use it
 * Only use it if you can not work with mpp_actions, mpp_screens those are more meaningful actions
 */
function mpp_template_redirect() {
	
	do_action( 'mpp_template_redirect' );
	
}

/**
 * Register your widgets on this action
 * 
 */
function mpp_widgets_init() {
	do_action( 'mpp_widgets_init' );
}

/**
 * Fires on template_redirect 
 * Best suited for doing any type of form manipulation/redirect
 */
function mpp_actions() {
	do_action( 'mpp_actions' );
}
/**
 * Add your screen handlers that loads templates on this action
 */
function mpp_screens() {
	do_action( 'mpp_screens' );
}
