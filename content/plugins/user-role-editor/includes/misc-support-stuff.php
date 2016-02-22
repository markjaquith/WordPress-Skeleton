<?php

/*
 * Miscellaneous support stuff, which should still be defined beyond of classes
 * 
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * License: GPL v3
 * 
*/

if (class_exists('GFForms') ) {    // if Gravity Forms is installed
// Support for Gravity Forms capabilities
// As Gravity Form has integrated support for the Members plugin - let's imitate its presense,
// so GF code, like
// self::has_members_plugin())
// considers that it is has_members_plugin()   
    if (!function_exists('members_get_capabilities')) { 
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if (!is_plugin_active('members/members.php')) {            
            // define stub function to say "Gravity Forms" plugin: 'Hey! While I'm not the "Members" plugin, but I'm "User Role Editor" and 
            // I'm  capable to manage your roles and capabilities too        
            function members_get_capabilities() {
        
            }
        }
    }
}


if (!function_exists('ure_get_post_view_access_users')) {
    function ure_get_post_view_access_users($post_id) {
        if (!$GLOBALS['user_role_editor']->is_pro()) {
            return false;
        }
        
        $result = $GLOBALS['user_role_editor']->get_post_view_access_users($post_id); 
        
        return $result;
    }   
    // end of ure_get_post_view_users()
    
}   


if (!function_exists('ure_hide_admin_bar')) {
    function ure_hide_admin_bar() {
        
        show_admin_bar(false);
        
    }
    // end of hide_admin_bar()
}