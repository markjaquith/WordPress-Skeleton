<?php
/*
 Plugin Name: BuddyForms WooCommerce Form Elements
 Plugin URI: http://buddyforms.com/downloads/buddyforms-woocommerce-form-elements/
 Description: This Plugin adds a new section to the BuddyForms Form Builder with all WooCommerce fields to create Product creation forms for the frontend
 Version: 1.2.1
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

add_action('init', 'bf_wc_fe_loader', 10);

function bf_wc_fe_loader(){
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        add_action('admin_enqueue_scripts', 'bf_wc_admin_enqueue_script');
        bf_wc_fe_includes();
    }

}


add_action('plugins_loaded', 'bf_wc_fe_requirements');
function bf_wc_fe_requirements(){

    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        add_action( 'admin_notices', create_function( '', 'printf(\'<div id="message" class="error"><p><strong>\' . __(\'BuddyForms WooCommerce Form Elements needs WooCommerce to be installed. <a href="%s">Download it now</a>!\', " buddyforms" ) . \'</strong></p></div>\', admin_url("plugin-install.php") );' ) );
        return;
    }

    if ( !BUDDYFORMS_STORE_URL ) {
        add_action( 'admin_notices', create_function( '', 'printf(\'<div id="message" class="error"><p><strong>\' . __(\'BuddyForms WooCommerce Form Elements needs BuddyForms to be installed. <a target="_blank" href="%s">--> Get it now</a>!\', " wc4bp_xprofile" ) . \'</strong></p></div>\', "http://themekraft.com/store/wordpress-front-end-editor-and-form-builder-buddyforms/" );' ) );
        return;
    }

}

function bf_wc_fe_includes(){

    include_once(dirname(__FILE__) . '/includes/form-builder-elements.php');
    include_once(dirname(__FILE__) . '/includes/class-wc-meta-box-product-data.php');
    include_once(dirname(__FILE__) . '/includes/class-wc-meta-box-product-images.php');
    include_once(dirname(__FILE__) . '/includes/form-elements.php');
    include_once(dirname(__FILE__) . '/includes/form-elements-save.php');

    include_once(dirname(__FILE__) . '/includes/form-elements/bf-wc-product-attribute.php');
    //include_once(dirname(__FILE__) . '/includes/form-elements/bf-wc-product-variations.php');
    include_once(dirname(__FILE__) . '/includes/form-elements/bf-wc-product-downloadable.php');
    include_once(dirname(__FILE__) . '/includes/form-elements/bf-wc-product-general.php');
    include_once(dirname(__FILE__) . '/includes/form-elements/bf-wc-product-inventory.php');
    include_once(dirname(__FILE__) . '/includes/form-elements/bf-wc-product-linked.php');
    include_once(dirname(__FILE__) . '/includes/form-elements/bf-wc-product-shipping.php');
    include_once(dirname(__FILE__) . '/includes/form-elements/bf-wc-product-type.php');

    include_once(dirname(__FILE__) . '/includes/wc-admin-assets-frontend/class-wc-admin-assets-frontend.php');

    if(!function_exists('woocommerce_wp_text_input'))
        include_once(WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php');
}

function bf_wc_admin_enqueue_script($hook_suffix){
    global $post;

    if(
        (isset($post) && $post->post_type == 'buddyforms'
            && isset($_GET['action']) &&  $_GET['action'] == 'edit'
            || isset($post) && $post->post_type == 'buddyforms'
            && $hook_suffix == 'post-new.php'
        )
        || $hook_suffix == 'buddyforms_page_create-new-form'
        || $hook_suffix == 'buddyforms_page_bf_add_ons'
    ) {
        wp_enqueue_script( 'buddyforms-woocommerce', plugins_url( '/assets/js/buddyforms-woocommerce.js' , __FILE__ ), array( 'jquery' ) );
    }
 }
