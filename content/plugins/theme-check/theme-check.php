<?php
/*
Plugin Name: Theme Check
Plugin URI: http://ottopress.com/wordpress-plugins/theme-check/
Description: A simple and easy way to test your theme for all the latest WordPress standards and practices. A great theme development tool!
Author: Pross, Otto42
Author URI: http://ottopress.com
Version: 20141222.1
Text Domain: theme-check
*/

class ThemeCheckMain {
	function __construct() {
		add_action( 'admin_init', array( $this, 'tc_i18n' ) );
		add_action( 'admin_menu', array( $this, 'themecheck_add_page' ) );
	}

	function tc_i18n() {
		load_plugin_textdomain( 'theme-check', false, 'theme-check/lang' );
	}

	function load_styles() {
		wp_enqueue_style('style', plugins_url( 'assets/style.css', __FILE__ ), null, null, 'screen');
	}

	function themecheck_add_page() {
		$page = add_theme_page( 'Theme Check', 'Theme Check', 'manage_options', 'themecheck', array( $this, 'themecheck_do_page' ) );
		add_action('admin_print_styles-' . $page, array( $this, 'load_styles' ) );
	}

	function tc_add_headers( $extra_headers ) {
		$extra_headers = array( 'License', 'License URI', 'Template Version' );
		return $extra_headers;
	}

	function themecheck_do_page() {
		if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'theme-check' ) );
		}

		add_filter( 'extra_theme_headers', array( $this, 'tc_add_headers' ) );

		include 'checkbase.php';
		include 'main.php';

		echo '<div id="theme-check" class="wrap">';
		echo '<div id="icon-themes" class="icon32"><br /></div><h2>Theme-Check</h2>';
		echo '<div class="theme-check">';
			tc_form();
		if ( !isset( $_POST[ 'themename' ] ) )  {
			tc_intro();

		}

		if ( isset( $_POST[ 'themename' ] ) ) {
			if ( isset( $_POST[ 'trac' ] ) ) define( 'TC_TRAC', true );
			if ( defined( 'WP_MAX_MEMORY_LIMIT' ) ) { 
				@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );
			}
			check_main( $_POST[ 'themename' ] );
		}
		echo '</div> <!-- .theme-check-->';
		echo '</div>';
	}
}
new ThemeCheckMain;
