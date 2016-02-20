<?php
/*
 Plugin Name: BuddyForms Members
 Plugin URI: http://buddyforms.com/downloads/buddyforms-members/
 Description: The BuddyForms Members Component. Let your members write right out of their profiles.
 Version: 1.1.3
 Author: Sven Lehnert
 Author URI: https://profiles.wordpress.org/svenl77
 License: GPLv2 or later
 Network: false

 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ****************************************************************************
 */

/**
 * Loads BuddyForms files only if BuddyPress is present
 *
 * @package BuddyFormsasasdasd
 * @since 0.1 beta
 */


function buddyforms_members_requirements(){

    if( ! defined( 'BP_VERSION' )){
        add_action( 'admin_notices', create_function( '', 'printf(\'<div id="message" class="error"><p><strong>\' . __(\'BuddyForms Members needs BuddyPress to be installed. <a href="%s">Download it now</a>!\', " buddypress" ) . \'</strong></p></div>\', admin_url("plugin-install.php") );' ) );
        return;
    }

    if( ! defined( 'BUDDYFORMS_VERSION' )){
        add_action( 'admin_notices', create_function( '', 'printf(\'<div id="message" class="error"><p><strong>\' . __(\'BuddyForms Members needs BuddyForms to be installed. <a target="_blank" href="%s">--> Get it now</a>!\', " buddyforms" ) . \'</strong></p></div>\', "http://themekraft.com/store/wordpress-front-end-editor-and-form-builder-buddyforms/" );' ) );
        return;
    }

}

add_action('plugins_loaded', 'buddyforms_members_requirements');

function buddyforms_members_init() {
	global $wpdb, $buddyforms_members;

    define('buddyforms_members', '1.1.3');

	if (is_multisite() && BP_ROOT_BLOG != $wpdb->blogid)
		return;

	require (dirname(__FILE__) . '/buddyforms-members.php');
	$buddyforms_members = new BuddyForms_Members();

}

add_action('bp_loaded', 'buddyforms_members_init');
