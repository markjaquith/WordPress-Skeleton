<?php
class BuddyForms_Members {

	/**
	 * Initiate the class
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	 */
	 function __construct() {

		// Let plugins know that BuddyForms has started loading
		add_action( 'plugins_loaded',   array( $this, 'load_hook' ), 20 );

		// Load predefined constants first thing
		add_action( 'buddyforms_members_load', 	array( $this, 'load_constants' ), 2 );
		
		// Includes necessary files
		add_action( 'buddyforms_members_load', 	array( $this, 'includes' ), 4 );

		// Load the BP Component extension
		add_action( 'buddyforms_members_load', 	array( $this, 'buddyforms_setup_component' ), 6 );
		
		// Load textdomain
		add_action( 'buddyforms_members_load',     array( $this, 'load_plugin_textdomain' ) );
		
		// Let other plugins know that BuddyForms has finished initializing
		add_action( 'bp_init',  array( $this, 'init_hook' ) );

	}

	/**
	 * Defines buddyforms_load action
	 *
	 * This action fires on WP's plugins_loaded action and provides a way for the rest of
	 * BuddyForms, as well as other dependent plugins, to hook into the loading process in
	 * an orderly fashion.
	 *
	 * @package BuddyPress Docs
	 * @since 1.2
	 */
	function load_hook() {
		do_action( 'buddyforms_members_load' );
	}

	/**
	 * Defines buddyforms_init action
	 *
	 * This action fires on WP's init action and provides a way for the rest of BuddyForms
	 * as well as other dependent plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package BuddyPress Docs
	 * @since 1.0-beta
	 */
	function init_hook() {
		do_action('buddyforms_members_init');
	}

	/**
	 * Defines buddyforms_loaded action
	 *
	 * This action tells BP Docs and other plugins that the main initialization process has
	 * finished.
	 *
	 * @package BuddyPress Docs
	 * @since 1.0-beta
	 */
	function loaded() {
		do_action( 'buddyforms_members_loaded' );
	}

	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * These constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	 */
	public function load_constants() {
			
		if (!defined('BUDDYFORMS_MEMBERS_INSTALL_PATH'))
			define('BUDDYFORMS_MEMBERS_INSTALL_PATH', dirname(__FILE__) . '/');

		if (!defined('BUDDYFORMS_MEMBERS_INCLUDES_PATH'))
			define('BUDDYFORMS_MEMBERS_INCLUDES_PATH', BUDDYFORMS_MEMBERS_INSTALL_PATH . 'includes/');

		if (!defined('BUDDYFORMS_MEMBERS_TEMPLATE_PATH'))
			define('BUDDYFORMS_MEMBERS_TEMPLATE_PATH', BUDDYFORMS_MEMBERS_INCLUDES_PATH . 'templates/');
		
	}

	/**
	 * Includes files needed by BuddyForms
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	 */
	public function includes() {

		require_once (BUDDYFORMS_MEMBERS_INCLUDES_PATH . 'functions.php');
		require_once (BUDDYFORMS_MEMBERS_INCLUDES_PATH . 'form-elements.php');
		require_once (BUDDYFORMS_MEMBERS_INCLUDES_PATH . 'member-extention.php');
		require_once (BUDDYFORMS_MEMBERS_INCLUDES_PATH . 'redirect.php');
		
		if (!class_exists('BP_Theme_Compat'))
			require_once (BUDDYFORMS_MEMBERS_INCLUDES_PATH . 'bp-backwards-compatibililty-functions.php');
	}

	/**
	 * Loads the textdomain for the plugin
	 *
	 * @package BuddyForms
	 * @since 0.1 beta
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain('buddyforms', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	/**
	 * Sets up the component
	 *
	 * @since   Marketplace 0.9.1
	 */
	function buddyforms_setup_component() {
		global $bp, $wp_query;
	
		$bp->buddyforms = new BuddyForms_Members_Extention();
	
	}

}
