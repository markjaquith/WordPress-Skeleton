<?php
/**
 * Uninstall Ninja Forms
 *
 * @package     Ninja Forms
 * @subpackage  Uninstall
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License * 
 * @copyright   Copyright (c) 2014, WP Ninjas
 * @since       2.9
 */

// Bail if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load NF file
include_once( 'ninja-forms.php' );

global $wpdb;

$settings = Ninja_Forms()->get_plugin_settings();

// Bail if we haven't checked the "delete on uninstall" box.
if( isset ( $settings['delete_on_uninstall'] ) && 1 == $settings['delete_on_uninstall'] ) {

	// Remove our options.
	delete_option( 'ninja_forms_settings' );
	delete_option( 'nf_version_upgraded_from' );
    delete_option( 'nf_upgrade_notice' );

    delete_option( 'nf_database_migrations' );

    delete_option( 'nf_convert_notifications_forms' );
	delete_option( 'nf_convert_notifications_complete' );

	delete_option( 'nf_convert_subs_step' );

    delete_option( 'nf_email_fav_updated' );
	delete_option( 'nf_update_email_settings_complete' );

	delete_option( 'nf_converted_subs' );
	delete_option( 'nf_convert_subs_num' );
	delete_option( 'nf_convert_subs_step' );

    delete_option( 'nf_converted_forms' );
	delete_option( 'nf_converted_form_reset' );
	delete_option( 'nf_convert_forms_complete' );


    // Remove upgrade last step options
    $upgrades = NF_UpgradeHandler()->upgrades;

    if( $upgrades AND is_array( $upgrades ) ) {
        foreach ($upgrades as $upgrade) {
            delete_option('nf_upgrade_' . $upgrade->name . '_last_step');
        }
    }
	
	// Remove all of our submissions
	$items = get_posts( array( 'post_type' => 'nf_sub', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	
	if ( $items ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item, true);
		}
	}
	
	// Remove all of our custom tables
	
	$wpdb->query( 'DROP TABLE ' . NF_OBJECTS_TABLE_NAME );
	$wpdb->query( 'DROP TABLE ' . NF_OBJECT_META_TABLE_NAME );
	$wpdb->query( 'DROP TABLE ' . NF_OBJECT_RELATIONSHIPS_TABLE_NAME );
	
	$wpdb->query( 'DROP TABLE ' . NINJA_FORMS_TABLE_NAME );
	$wpdb->query( 'DROP TABLE ' . NINJA_FORMS_FIELDS_TABLE_NAME );
	$wpdb->query( 'DROP TABLE ' . NINJA_FORMS_FAV_FIELDS_TABLE_NAME );
	$wpdb->query( 'DROP TABLE ' . NINJA_FORMS_SUBS_TABLE_NAME );
	
	// Remove our daily cron job
	$timestamp = wp_next_scheduled( 'ninja_forms_daily_action' );
	wp_unschedule_event( $timestamp, 'ninja_forms_daily_action' );
}
