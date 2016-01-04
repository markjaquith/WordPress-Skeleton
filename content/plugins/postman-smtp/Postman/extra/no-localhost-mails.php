<?php
/**
* Plugin Name: No localhost emails
* Description: Overwrites WP pluggable function wp_mail
*/

if ( !function_exists( 'wp_mail' ) ) :
    function wp_mail() {} // KILL EMAILS IN LOCALHOST
endif;
