<?php
/*
Plugin Name: Event Organiser
Plugin URI: http://www.wp-event-organiser.com
Version: 2.9.2
Description: Creates a custom post type 'events' with features such as reoccurring events, venues, Google Maps, calendar views and events and venue pages
Author: Stephen Harris
Author URI: http://www.stephenharris.info
Network: false
Text Domain: eventorganiser
Domain Path: /languages
*/
/*  Copyright 2011 Stephen Harris (contact@stephenharris.info)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
/*  Cookies used:
         eo_admin_cal_last_viewed_date   -   stores the last viewed date on the admin calendar. 
         eo_admin_cal_last_view   -   stores the last used admin calendar view (month, week, day).
         Expires: 10 minutes. Used for persitant admin calendar
*/
/**
 * The main plug-in loader
 */

/**
 * Set the plug-in database version
 */ 
define( 'EVENT_ORGANISER_VER', '2.9.2' );


add_action( 'after_setup_theme', '_eventorganiser_set_constants' );
function _eventorganiser_set_constants(){
	/*
 	* Defines the plug-in directory url
 	* <code>url:http://mysite.com/wp-content/plugins/event-organiser</code>
	*/
	define( 'EVENT_ORGANISER_URL', plugin_dir_url( __FILE__ ) );
	
	require_once(EVENT_ORGANISER_DIR.'event-organiser-add-ons.php');
}

/*
 * Defines the plug-in directory path
 * <code>/home/mysite/public_html/wp-content/plugins/event-organiser</code>
*/
define( 'EVENT_ORGANISER_DIR', plugin_dir_path( __FILE__ ) );

/**
 * For use in datetime formats. To return a datetime object rather than formatted string
 */
define( 'DATETIMEOBJ', 'DATETIMEOBJ', true );


/**
 * Load the translation file for current language. Checks in wp-content/languages first
 * and then the event-organiser/languages.
 *
 * Edits to translation files inside event-organiser/languages will be lost with an update
 * **If you're creating custom translation files, please use the global language folder.**
 *
 * @since 1.3
 * @ignore
 * @uses apply_filters() Calls 'plugin_locale' with the get_locale() value
 * @uses load_textdomain() To load the textdomain from global language folder
 * @uses load_plugin_textdomain() To load the textdomain from plugin folder
 */
function eventorganiser_load_textdomain() {
	$domain = 'eventorganiser';

	/**
	 *@ignore
	 */
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		
	$mofile = $domain . '-' . $locale . '.mo';

	/* Check the global language folder */
	$files = array( WP_LANG_DIR . '/event-organiser/' . $mofile, WP_LANG_DIR . '/' . $mofile );
	foreach ( $files as $file ){
		if ( file_exists( $file ) )
			return load_textdomain( $domain, $file );
	}

	//If we got this far, fallback to the plug-in language folder.
	//We could use load_textdomain - but this avoids touching any more constants.
	load_plugin_textdomain( 'eventorganiser', false, basename( dirname( __FILE__ ) ).'/languages' );
}
add_action( 'plugins_loaded', 'eventorganiser_load_textdomain' );

global $eventorganiser_roles;
$eventorganiser_roles = array(
	'edit_events' => __( 'Edit Events', 'eventorganiser' ),
	'publish_events' => __( 'Publish Events', 'eventorganiser' ),
	'delete_events' => __( 'Delete Events', 'eventorganiser' ),
	'edit_others_events' => __( 'Edit Others\' Events', 'eventorganiser' ),
	'delete_others_events' => __( 'Delete Other\'s Events', 'eventorganiser' ),
	'read_private_events' => __( 'Read Private Events', 'eventorganiser' ),
	'manage_venues' => __( 'Manage Venues', 'eventorganiser' ),
	'manage_event_categories' => __( 'Manage Event Categories & Tags', 'eventorganiser' ),
);
			
/****** Install, activation & deactivation******/
require_once( EVENT_ORGANISER_DIR . 'includes/event-organiser-install.php' );

register_activation_hook( __FILE__, 'eventorganiser_install' ); 
register_deactivation_hook(  __FILE__, 'eventorganiser_deactivate' );
register_uninstall_hook( __FILE__, 'eventorganiser_uninstall' );


function eventorganiser_get_option( $option = false, $default = false ){

	$defaults = array(
		'url_event' => 'events/event',
		'url_events' => 'events/event',
		'url_venue' => 'events/venue',
		'url_cat' => 'events/category',
		'url_tag' => 'events/tag',
		'url_on' => 'on',
		'navtitle' => __( 'Events', 'eventorganiser' ),
		'group_events' => '',
		'feed' => 1,
		'deleteexpired' => 0,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'eventtag', 'event-venue' ),
		'event_redirect' => 'events',
		'dateformat' => 'd-m-Y',
		'prettyurl' => 1,
		'templates' => 1,
		'addtomenu' => 0,
		'menu_item_db_id' => 0,
		'excludefromsearch' => 0,
		'showpast' => 0,
		'runningisnotpast' => 0,
		'hide_addon_page' => 0,
		'disable_css' => 0,
	);
	$options = get_option( 'eventorganiser_options', $defaults );
	$options = wp_parse_args( $options, $defaults );
	
	$options['supports'][] = 'event-venue';
	
	$options = apply_filters( 'eventorganiser_options', $options );
	
	if ( false === $option )
		return $options;

	/* Backwards compatibility for 'eventag' option */
	if ( $option === 'eventtag' )
		return in_array( 'eventtag', $options['supports'] );
	
	if ( $option === 'dateformat' ){
		//Backwards compatibility (migration from mm-dd/dd-mm to php format):
		if ( $options[$option] == 'mm-dd' ){
			$options[$option] = 'm-d-Y';
		} elseif ( $options[$option] == 'dd-mm' ){
			$options[$option] = 'd-m-Y';
		}
	}

	if ( ! isset( $options[$option] ) )
		return $default;

	return $options[$option];
}


/****** Register event post type and event taxonomy******/
require_once(EVENT_ORGANISER_DIR.'includes/event-organiser-cpt.php');

/****** Register scripts, styles and actions******/
require_once(EVENT_ORGANISER_DIR.'includes/event-organiser-register.php');

/****** Deals with the queries******/
require_once(EVENT_ORGANISER_DIR.'includes/event-organiser-archives.php');

/****** Deals with importing/exporting & subscriptions******/
require_once(EVENT_ORGANISER_DIR.'includes/class-event-organiser-im-export.php');
require_once(EVENT_ORGANISER_DIR.'includes/class-eo-ical-parser.php');

if ( is_admin() ):
	require_once(EVENT_ORGANISER_DIR.'classes/class-eventorganiser-admin-page.php');

	/****** event editing pages******/
	require_once(EVENT_ORGANISER_DIR.'event-organiser-edit.php');
	require_once(EVENT_ORGANISER_DIR.'event-organiser-manage.php');
	
	/****** settings, venue and calendar pages******/
	require_once(EVENT_ORGANISER_DIR.'event-organiser-settings.php');
	require_once(EVENT_ORGANISER_DIR.'event-organiser-venues.php');
	require_once(EVENT_ORGANISER_DIR.'event-organiser-calendar.php');
	
	require_once(EVENT_ORGANISER_DIR.'event-organiser-debug.php');
	
	require_once(EVENT_ORGANISER_DIR.'event-organiser-go-pro.php');

endif;

if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
	/****** Ajax actions ******/
	require_once(EVENT_ORGANISER_DIR.'includes/event-organiser-ajax.php');
}

/****** Functions ******/
require_once(EVENT_ORGANISER_DIR.'includes/event-organiser-event-functions.php');
require_once(EVENT_ORGANISER_DIR.'includes/event-organiser-venue-functions.php');
require_once(EVENT_ORGANISER_DIR.'includes/event-organiser-utility-functions.php');
require_once(EVENT_ORGANISER_DIR.'includes/deprecated.php');
require_once(EVENT_ORGANISER_DIR.'includes/event.php');
require_once(EVENT_ORGANISER_DIR.'includes/class-eo-extension.php');

/****** Templates - note some plug-ins will require this to included admin-side too ******/
require_once('includes/event-organiser-templates.php');

/****** Widgets and Shortcodes ******/
require_once(EVENT_ORGANISER_DIR.'classes/class-eo-agenda-widget.php');
require_once(EVENT_ORGANISER_DIR.'classes/class-eo-event-list-widget.php');
require_once(EVENT_ORGANISER_DIR.'classes/class-eo-calendar-widget.php');
require_once(EVENT_ORGANISER_DIR.'classes/class-eo-widget-categories.php');
require_once(EVENT_ORGANISER_DIR.'classes/class-eo-widget-venues.php');
require_once(EVENT_ORGANISER_DIR.'classes/class-eventorganiser-shortcodes.php');