<?php

/*
  Plugin Name: Events Planner
  Plugin URI: http://wpEventsPlanner.com
  Description: A comprehensive event management plugin that contains support for multiple event types, payments, custom forms, and etc.

  Version: 1.3.8

  Author: Abel Sekepyan
  Author URI: http://wpEventsPlanner.com

  Copyright (c) 2015 Abel Sekepyan  All Rights Reserved.

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
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

if ( !function_exists( 'add_action' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

add_action( 'plugins_loaded', 'start_events_planner' );

require_once(dirname( __FILE__ ) . '/application/config/config.php');

epl_log( 'clear' );
epl_log( 'init', "Plugin file loaded." );


function start_events_planner() {

    //Starting the output buffer here for paypal redirect to work
    //Will see if conflict with other plugins.  If so, will use init hook
    ob_start();


    if ( !session_id() ) {
        session_start();

        if ( isset( $_GET['destroy_epl_sess'] ) )
            session_destroy();

        if ( !isset( $_SESSION['__epl'] ) ) {
            $_SESSION['__epl'] = array( );
        }
    }

    //error_reporting(E_ALL ^ E_NOTICE);
    //ini_set( 'display_errors', 1 );

    require_once EPL_SYSTEM_FOLDER . 'epl-base.php'; //load super object
    require_once EPL_SYSTEM_FOLDER . 'epl-init.php'; //load ini class
    require_once EPL_SYSTEM_FOLDER . 'epl-controller.php'; //load parent controller
    require_once EPL_SYSTEM_FOLDER . 'epl-model.php'; //load parent model
    //initialize the plugin (menus, load js, css....), the base super object

    $init = new EPL_Init;

    add_action( 'init', array( $init, 'route' ) );
    /**
     * Shortcode
     */
    add_shortcode( 'events_planner', array( $init, 'shortcode_route' ) );

    /*
     * ajax
     */
    add_action( 'wp_ajax_events_planner_form', array( $init, 'route' ) );
    add_action( 'wp_ajax_nopriv_events_planner_form', array( $init, 'route' ) );

}

/*
 * activation hooks, need to be called early
 */

register_activation_hook( __FILE__, 'epl_activate' );
register_deactivation_hook( __FILE__, 'epl_deactivate' );


function epl_activate() {

    require_once(dirname( __FILE__ ) . '/application/config/install_defaults.php');


    update_option( 'events_planner_version', EPL_PLUGIN_VERSION );
    update_option( 'events_planner_active', 1 );

    global $default_vals;

    foreach ( $default_vals as $key => $data ) {
        /* check for option then update if necessary */
        if ( !get_option( $key ) ) {
            add_option( $key, $data );
        }
    }
}


function epl_deactivate() {

    update_option( 'events_planner_active', 0 );
}


function epl_log( $log= '', $message = '' ) {
    return;
    $process = array(
        'init',
        'debug',
        'clear'
    );

    if ( !in_array( $log, $process ) )
        return;

    $log_file = WP_PLUGIN_DIR . "/log.txt";
    if ( !file_exists( $log_file ) )
        return;

    if ( $log == 'clear' )
        file_put_contents( $log_file, '' );

    static $flags = FILE_APPEND; //first call clears the file
    $new_call = "\r\n";


    $_bt = '';
    /* $trace = debug_backtrace();
      if ( isset( $trace[1] ) ) {

      $_bt = " \t\t called by {$trace[1]['class']} :: {$trace[1]['function']}, line {$trace[1]['line']}, file {$trace[1]['file']}";

      }
     */
    //$message = $new_call . date( "m/d/Y H:i:s" ) . ' > ' . $message . $_bt . "\r\n";
    $message = $new_call . $message . $_bt . "\r\n";

    file_put_contents( $log_file, $new_call . $message, $flags );
}
