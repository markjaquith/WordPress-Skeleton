<?php

/*
 * Plugin Name: Postman SMTP
 * Plugin URI: https://wordpress.org/plugins/postman-smtp/
 * Description: Email not reliable? Postman is the first and only WordPress SMTP plugin to implement OAuth 2.0 for Gmail, Hotmail and Yahoo Mail. Setup is a breeze with the Configuration Wizard and integrated Port Tester. Enjoy worry-free delivery even if your password changes!
 * Version: 1.7.2
 * Author: Jason Hendriks
 * Text Domain: postman-smtp
 * Author URI: http://www.codingmonkey.ca
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// The Postman Mail API
//
// filter postman_test_email: before calling wp_mail, implement this filter and return true to disable the success/fail counters
// filter postman_wp_mail_result: apply this filter after calling wp_mail for an array containing the SMTP error, transcript and time
// filter postman_get_plugin_metadata: apply this filter to get plugin metadata
// filter postman_wp_mail_bind_status: apply this filter to get wp_mail bind status
// filter print_postman_status: apply this filter to print the human-readable plugin state
// filter postman_module: implement this filter and return the instance of the module
// filter postman_register_modules: apply this filter to register the module

// TODO v1.7
// -- Postmark API http://plugins.svn.wordpress.org/postmark-approved-wordpress-plugin/trunk/postmark.php
// -- Amazon SES API http://docs.aws.amazon.com/ses/latest/DeveloperGuide/send-email-api.html
// TODO v2.0
// -- PHP7 compatibility
// -- class autoloading
// -- Add dismiss option for "unconfigured message" .. for multisites
// -- customize sent-mail icon WordPress dashboard
// -- multisite support for site-wide email configuration. allow network admin to choose whether subdomains may override with their own settings. subdomains may override with their own settings.
// -- multiple mailbox support

/**
 * Create the main Postman class to start Postman
 *
 * @param unknown $startingMemory        	
 */
function postman_start($startingMemory) {
	postman_setupPostman ();
	PostmanUtils::logMemoryUse ( $startingMemory, 'Postman' );
}

/**
 * Instantiate the mail Postman class
 */
function postman_setupPostman() {
	require_once 'Postman/Postman.php';
	$kevinCostner = new Postman ( __FILE__, '1.7.2' );
}

/**
 * Start Postman
 */
postman_start ( memory_get_usage () );

