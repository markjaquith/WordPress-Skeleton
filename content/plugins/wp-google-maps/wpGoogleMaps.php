<?php
/*
Plugin Name: WP Google Maps
Plugin URI: http://www.wpgmaps.com
Description: The easiest to use Google Maps plugin! Create custom Google Maps with high quality markers containing locations, descriptions, images and links. Add your customized map to your WordPress posts and/or pages quickly and easily with the supplied shortcode. No fuss.
Version: 6.3.05
Author: WP Google Maps
Author URI: http://www.wpgmaps.com
Text Domain: wp-google-maps
Domain Path: /languages
*/

/* 
 * 6.3.05 - 2016-01-14 - Low priority
 * Multiple tab compatibility added
 * 
 * 6.3.04 - 2016-01-04 - Low priority
 * Tested with WP 4.4
 *
 * 6.3.03 - 2015-11-19 - Low Priority
 * Fixed a bug that caused the map to not display when a theme was not selected
 * 
 * 6.3.02 - 2015-11-06 - Low priority
 * A new theme directory has been created - this allows you to use any map theme or style that you want simply by copying and pasting it's data
 * 
 * 6.3.01 - 2015-10-06 - Low priority
 * Added 3 new google map custom themes
 * Corrected internationalization
 * iPhone map marker styling fix
 * Fixed an autocomplete bug
 * All WP Google Maps language files have been updated
 * 
 * 6.3.00 - 2015-09-02 - Low priority
 * Added 5 map themes to the map editor
 * Added a native map widget so you can drag and drop your maps to your widget area
 * Minor bug fixes
 * Language files updated
 * Turkish translation added - thank you Suha Karalar
 * 
 * 6.2.3 - 2015-08-20 - High priority
 * Included the latest version of datatables to fix the bug experienced with the new jQuery being included in WordPress 4.3
 * Updated datatables.responsive to 1.0.7 and included the minified version of the file instead
 * Fixed a few styling bugs in the map editor
 * 
 * 6.2.2 - Security Update - 2015-07-27 - High Priority
 * Security patch
 * Tested with WP 4.2.3
 * 
 * 6.2.1 - Security Update - 2015-07-13 - High Priority
 * Security enhancements to the map editor page, map javascript, marker categories and front end code
 * 
 * 6.2.0 - Liberty Update - 2015-06-24 - Medium Priority
 * Security enhancements (map editor, marker location, map settings)
 * Weather has been removed (deprecated by Google Maps)
 * Major bug fix (Google Map places bug) - caused the map markers not to show if the map store locator was not enabled
 * Fixed a bug that caused the jQuery error message to display briefly before the map loaded
 * Fixed a bug that caused the max map zoom to default back to 3
 * 
 * 6.1.10 - 2015-06-10 - High priority
 * XSS security patch
 * Security enhancements
 * Fixed a bug that didnt allow you to add a map marker if there were no markers to start with
 * 
 * 6.1.9 - 2015-06-01 - Low priority
 * Fixed french translation bug
 * 
 * 6.1.8 - 2015-05-27 - Low priority
 * Greek translation added - Thank you Konstantinos Koukoulakis
 * Added the Google Maps autocomplete functionality to the "add marker" section of the map editor
 * Added the Google Maps autocomplete functionality to the Store Locator
 * 
 * 6.1.7 - 2015-04-22 - Low priority
 * json_encode (extra parameter) issue fixed for hosts using PHP version < 5.3
 * 
 * 6.1.6 - 2015-04-17 - Low priority
 * Rocketscript fix (Cloudfare)
 * Dutch translation added
 * Main translation file updated
 * 
 * 6.1.5 - 2015-03-16 - High priority
 * Timthumb removed
 * New support page added
 * You can now restrict your store locator search by a specific country
 * Bug fix in map editor
 * SSL bug fix
 * Usability Improvements when right clicking to add a marker on the map.
 * 
 * 6.1.4 - 2015-02-13
 * Safari bug fix
 * Fixed issues with map markers containing addresses with single quotes
 * You can now set the max zoom of your google map
 * 
 * 6.1.3 - 2015-01-19
 * IIS 500 server error fix
 * Small map bug fixes
 * Brazilian portuguese language file updated
 * Activation error fixes
 * 
 * 6.1.2 2015-01-19
 * Code improvements (PHP warnings)
 * Tested in WordPress 4.1
 * 
 * 6.1.1 2014-12-19
 * Code improvements
 * 
 * 6.1.0 2014-12-17
 * Added an alternative method to pull the marker data
 * 
 * 6.0.32
 * Comprehensive checks added to the Marker XML Dir field
 * 
 * 6.0.31 2014-11-28
 * Category bug fix
 * 
 * 6.0.30 2014-11-26
 * Added a check for the DOMDocument class
 * Removed the APC Object Cache warning
 * Added new strings to the PO file
 * 
 * 6.0.29
 * New option: You can now show or hide the Store Locator bouncing icon
 * New feature: Add custom CSS in the settings page
 * Code improvements
 * 
 * 6.0.28
 * Enfold / Avia theme conflict resolved (Google Maps API loading twice)
 * Better marker file/directory control
 * Italian translation added (Tommaso Mori)
 * 
 * 6.0.27 - 2014-09-29
 * French translation updated by Arnaud Thomas
 * Security updates (thank you www.htbridge.com)
 * Fixed the bug that wouldnt allow you to select the Google maps API version
 * Code improvements (PHP warnings)
 * Google Map Store Locator bug fix - map zoom levels on 300km, 150km and 75km were incorrect
 * 
 * 6.0.26
 * Attempting to fix the "is_dir" and "open_basedir restriction" errors some users are experiencing.
 * Updated timthumb to version 2.8.14
 * Altered all instances of "is_dir" in timthumb.php (causing fatal errors on some hosts) and replace it with 'file_exists'
 * 
 * 6.0.25
 * Removed the use of "is_dir" which caused fatal errors on some hosts
 * 
 * 6.0.24
 * Added extra support for folder management and error reporting
 * Code improvements (PHP Warnings)
 * Better polygon and polyline handling
 * Hebrew translation added
 * 
 * 6.0.23
 * Added extra support for corrupt polyline and polygon data
 * 
 * 6.0.22
 * Fixed incorrect warning about permissions when permissions where "2755" etc.
 * Add classes to the google map store locator elements
 * 
 * 6.0.21
 * Backend UI improvement
 * You can now right click to add a marker to the map
 * New markers can be dragged
 * Polygons and polylines now have labels
 * Small bug fixes
 * 
 * 
 * 6.0.20
 * You can now set the query string for the store locator
 * 
 * 6.0.19
 * Fixed a bug that caused the marker file to be recreated on every page load in some instances.
 * Fixed a marker listing display bug (iPhone)
 * Now showing default settings for marker path and URL
 * Removed the "map could not load" error
 * Fixed a bug that when threw off gps co-ordinates when adding a lat,lng as an address
 * 
 */


global $wpgmza_version;
global $wpgmza_p_version;
global $wpgmza_t;
global $wpgmza_tblname;
global $wpgmza_tblname_maps;
global $wpgmza_tblname_poly;
global $wpgmza_tblname_polylines;
global $wpgmza_tblname_categories;
global $wpgmza_tblname_category_maps;
global $wpdb;
global $wpgmza_p;
global $wpgmza_g;
global $short_code_active;
global $wpgmza_current_map_id;
global $wpgmza_current_mashup;
global $wpgmza_mashup_ids;
global $debug;
global $debug_step;
global $debug_start;
global $wpgmza_global_array;
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);



$debug = false;
$debug_step = 0;
$wpgmza_p = false;
$wpgmza_g = false;
$wpgmza_tblname = $wpdb->prefix . "wpgmza";
$wpgmza_tblname_maps = $wpdb->prefix . "wpgmza_maps";
$wpgmza_tblname_poly = $wpdb->prefix . "wpgmza_polygon";
$wpgmza_tblname_polylines = $wpdb->prefix . "wpgmza_polylines";
$wpgmza_tblname_categories = $wpdb->prefix. "wpgmza_categories";
$wpgmza_tblname_category_maps = $wpdb->prefix. "wpgmza_category_maps";
$wpgmza_version = "6.3.05";
$wpgmza_p_version = "6.3.05";
$wpgmza_t = "basic";
define("WPGMAPS", $wpgmza_version);
define("WPGMAPS_DIR",plugin_dir_url(__FILE__));

include ("base/includes/wp-google-maps-polygons.php");
include ("base/includes/wp-google-maps-polylines.php");
include ("base/classes/widget_module.class.php");
include ("base/includes/deprecated.php");


if (function_exists('wpgmaps_head_pro')) {
    add_action('admin_head', 'wpgmaps_head_pro');
} else {
    if (function_exists('wpgmaps_pro_activate') && floatval($wpgmza_version) < 5.24) {
        add_action('admin_head', 'wpgmaps_head_old');
    } else {
        add_action('admin_head', 'wpgmaps_head');
    }
    
}
add_action('admin_head','wpgmaps_feedback_head');

add_action('admin_footer', 'wpgmaps_reload_map_on_post');
register_activation_hook( __FILE__, 'wpgmaps_activate' );
register_deactivation_hook( __FILE__, 'wpgmaps_deactivate' );
add_action('init', 'wpgmaps_init');
add_action('admin_menu', 'wpgmaps_admin_menu');
add_filter('widget_text', 'do_shortcode');


$debug_start = (float) array_sum(explode(' ',microtime()));




function wpgmaps_activate() {
    global $wpdb;
    global $wpgmza_version;
    $table_name = $wpdb->prefix . "wpgmza";
    $table_name_maps = $wpdb->prefix . "wpgmza_maps";
    delete_option("WPGMZA");

    
    /* set defaults for the Marker XML Dir and Marker XML URL */
    if (get_option("wpgmza_xml_location") == "") {
        $upload_dir = wp_upload_dir();
        add_option("wpgmza_xml_location",'{uploads_dir}/wp-google-maps/');
    }
    if (get_option("wpgmza_xml_url") == "") {
        $upload_dir = wp_upload_dir();
        add_option("wpgmza_xml_url",'{uploads_url}/wp-google-maps/');
    }
    
    
    wpgmaps_handle_db();
    
    wpgmaps_handle_directory();


    $wpgmza_data = get_option("WPGMZA");
    if (!$wpgmza_data) {
        /* load first map as an example map (i.e. if the user has not installed this plugin before, this must run */
        $res_maps = $wpdb->get_results("SELECT * FROM $table_name_maps");
        if (!$res_maps) { $rows_affected = $wpdb->insert( $table_name_maps, array(
                "map_title" => __("My first map","wp-google-maps"),
                "map_start_lat" => "45.950464398418106",
                "map_start_lng" => "-109.81550500000003",
                "map_width" => "100",
                "map_height" => "400",
                "map_width_type" => "%",
                "map_height_type" => "px",
                "map_start_location" => "45.950464398418106,-109.81550500000003",
                "map_start_zoom" => "2",
                "directions_enabled" => '1',
                "default_marker" => "0",
                "alignment" => "0",
                "styling_enabled" => "0",
                "styling_json" => "",
                "active" => "0",
                "type" => "1",
                "kml" => "",
                "fusion" => "",
                "bicycle" => "2",
                "traffic" => "2",
                "dbox" => "1",
                "dbox_width" => "250",
                "default_to" => "",
                "listmarkers" => "0",
                "listmarkers_advanced" => "0",
                "filterbycat" => "0",
                "order_markers_by" => "1",
                "order_markers_choice" => "2",
                "show_user_location" => "0",
                "ugm_enabled" => "0",
                "ugm_category_enabled" => "0",
                "ugm_access" => "0",
                "mass_marker_support" => "1",
                "other_settings" => ""
            )
        ); }
    } else {
        $rows_affected = $wpdb->insert( $table_name_maps, array(    "map_start_lat" => "".$wpgmza_data['map_start_lat']."",
            "map_start_lng" => "".$wpgmza_data['map_start_lng']."",
            "map_title" => "My Map",
            "map_width" => "".$wpgmza_data['map_width']."",
            "map_height" => "".$wpgmza_data['map_height']."",
            "map_width_type" => "".$wpgmza_data['map_width_type']."",
            "map_height_type" => "".$wpgmza_data['map_height_type']."",
            "map_start_location" => "".$wpgmza_data['map_start_lat'].",".$wpgmza_data['map_start_lng']."",
            "map_start_zoom" => "".$wpgmza_data['map_start_zoom']."",
            "default_marker" => "".$wpgmza_data['map_default_marker']."",
            "type" => "".$wpgmza_data['map_type']."",
            "alignment" => "".$wpgmza_data['map_align']."",
            "styling_enabled" => "0",
            "styling_json" => "",
            "active" => "0",
            "kml" => "",
            "fusion" => "",
            "directions_enabled" => "".$wpgmza_data['directions_enabled']."",
            "bicycle" => "".$wpgmza_data['bicycle']."",
            "traffic" => "".$wpgmza_data['traffic']."",
            "dbox" => "".$wpgmza_data['dbox']."",
            "dbox_width" => "".$wpgmza_data['dbox_width']."",
            "default_to" => "".$wpgmza_data['default_to']."",
            "listmarkers" => "".$wpgmza_data['listmarkers']."",
            "listmarkers_advanced" => "".$wpgmza_data['listmarkers_advanced']."",
            "filterbycat" => "".$wpgmza_data['filterbycat']."",
            "order_markers_by" => "".$wpgmza_data['order_markers_by']."",
            "order_markers_choice" => "".$wpgmza_data['order_markers_choice']."",
            "show_user_location" => "".$wpgmza_data['show_user_location']."",
            "ugm_enabled" => "".$wpgmza_data['ugm_enabled']."",
            "ugm_category_enabled" => "".$wpgmza_data['ugm_category_enabled']."",
            "ugm_access" => "".$wpgmza_data['ugm_access']."",
            "mass_marker_support" => "1",
            "other_settings" => ""

        ) );
        delete_option("WPGMZA");

    }
    
    
    /* load first marker as an example marker */
    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE `map_id` = '1'");
    if (!$results) { $rows_affected = $wpdb->insert( $table_name, array( 'map_id' => '1', 'address' => 'California', 'lat' => '36.778261', 'lng' => '-119.4179323999', 'pic' => '', 'link' => '', 'icon' => '', 'anim' => '', 'title' => '', 'infoopen' => '', 'description' => '', 'category' => 0, 'retina' => 0) ); }

    
    add_option("wpgmaps_current_version",$wpgmza_version);

}
function wpgmaps_deactivate() { /* wpgmza_cURL_response("deactivate"); */ }
function wpgmaps_init() {
    global $wpgmza_pro_version;
    global $wpgmza_version;
    wp_enqueue_script("jquery");
    $plugin_dir = basename(dirname(__FILE__))."/languages/";
    load_plugin_textdomain( 'wp-google-maps', false, $plugin_dir );
    
     
    if (get_option("wpgmza_xml_location") == "") {
        $upload_dir = wp_upload_dir();
        add_option("wpgmza_xml_location",'{uploads_dir}/wp-google-maps/');
    }
    
    if (get_option("wpgmza_xml_url") == "") {
        $upload_dir = wp_upload_dir();
        add_option("wpgmza_xml_url",'{uploads_url}/wp-google-maps/');
    }
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if ($wpgmza_settings['wpgmza_settings_marker_pull'] == "") {
        
        $wpgmza_first_time = get_option("WPGMZA_FIRST_TIME");
                if (!$wpgmza_first_time) { 
                    
                    /* first time, set marker pull to DB */
                    $wpgmza_settings['wpgmza_settings_marker_pull'] = "0";
                    update_option("WPGMZA_OTHER_SETTINGS",$wpgmza_settings);

                } else {
                    /* previous users - set it to XML (what they were using originally) */
                    $wpgmza_settings['wpgmza_settings_marker_pull'] = "1";
                    update_option("WPGMZA_OTHER_SETTINGS",$wpgmza_settings);
                }
    }
   
    if (function_exists("wpgmza_register_pro_version")) {
        global $wpgmza_pro_version;
        if (floatval($wpgmza_pro_version) < 5.41) {
            /* user has pro and is prior to version 5.41 so therefore do not save the shortcode in the URL, rather process it and then save it */
            update_option("wpgmza_xml_url",wpgmza_return_marker_url());
            update_option("wpgmza_xml_location",wpgmza_return_marker_path());
        } else {
            
        }
    } 
    
    
   

    
    
    
    wpgmaps_handle_directory();
    /* handle first time users and updates */
    if (isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu') {
        
        /* check if their using APC object cache, if yes, do nothing with the welcome page as it causes issues when caching the DB options */
        if (class_exists("APC_Object_Cache")) {
            /* do nothing here as this caches the "first time" option and the welcome page just loads over and over again. quite annoying really... */
        }  else { 
            if (isset($_GET['override']) && $_GET['override'] == "1") {
                $wpgmza_first_time = $wpgmza_version;
                update_option("WPGMZA_FIRST_TIME",$wpgmza_first_time);
            } else {
                $wpgmza_first_time = get_option("WPGMZA_FIRST_TIME");
                if (!$wpgmza_first_time) { 
                    /* show welcome screen */
                    $wpgmza_first_time = $wpgmza_version;
                    update_option("WPGMZA_FIRST_TIME",$wpgmza_first_time);
                    wp_redirect(get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu&action=welcome_page");
                    exit();
                }
                
                if ($wpgmza_first_time != $wpgmza_version) {
                    update_option("WPGMZA_FIRST_TIME",$wpgmza_version);
                    
                }
                
            }
        }
    }
    /* check if version is outdated or plugin is being automatically updated */
    $current_version = get_option("wpgmaps_current_version");
    if (!isset($current_version) || $current_version != $wpgmza_version) {
        wpgmaps_handle_db();
        wpgmaps_handle_directory();
        wpgmaps_update_all_xml_file();
        update_option("wpgmaps_current_version",$wpgmza_version);
        
    }
    /*
    if (floatval($wpgmza_pro_version) < 5.52) {
        if (isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu') {
            echo "<div class='updated'><h2>WP Google Maps Notice</h2><p><strong>We have stopped using Timthumb</strong> to generate thumbnails for your markers due to security concerns.</p><p>Please update your pro version of WP Google Maps to at least 5.52 in order for your marker images to function correctly.</p></div>";
        }
    }*/
    
}

function wpgmaps_handle_directory() {
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if (isset($wpgmza_settings['wpgmza_settings_marker_pull']) && $wpgmza_settings['wpgmza_settings_marker_pull'] == '0') {
        /* using db method, do nothing */
        return;
    }
    if (get_option("wpgmza_xml_location") == "") {
        $upload_dir = wp_upload_dir();
        add_option("wpgmza_xml_location",'{uploads_dir}/wp-google-maps/');
    }
    $xml_marker_location = get_option("wpgmza_xml_location");
    if (!file_exists($xml_marker_location)) {
        if (@mkdir($xml_marker_location)) {
            return true;
        } else {
            return false;
        }
        
    }
    
    
}

function wpgmaps_folder_check() {
    $xml_marker_location = wpgmza_return_marker_path();
    if (!file_exists($xml_marker_location) && (isset($_GET['activate']) && $_GET['activate'] == 'true')) {
        add_action('admin_notices', 'wpgmaps_folder_warning');
    }
}
function wpgmaps_folder_warning() {
    $file = get_option("wpgmza_xml_location");
    echo '
    <div class="error"><p>'.__('<strong>WP Google Maps cannot find the directory it uses to save marker data to. Please confirm that <em>', 'wp-google-maps').' '.$file.' '.__('</em>exists. Please also ensure that you assign file permissions of 755 (or 777) to this directory.','wp-google-maps').'</strong></p></div>
    ';

}
function wpgmaps_cache_permission_warning() {
    echo "<div class='error below-h1'><big>";
    _e("Timthumb does not have 'write' permission for the cache directory. Please enable 'write' permissions (755 or 777) for ");
    echo "\"".dirname(__FILE__).DS."cache ";
    _e("in order for images to show up while using Timthumb. Please see ");
    echo "<a href='http://codex.wordpress.org/Changing_File_Permissions#Using_an_FTP_Client'>";
    _e("this page");
    echo "</a> ";
    _e("for help on how to do it. Alternatively, you can disable the use of Timthumb in Maps->Settings");
    echo "</big></div>";
}
function wpgmaps_check_permissions_cache() {
    $filename = dirname( __FILE__ ).DS.'cache'.DS.'wpgmaps.tmp';
    $testcontent = "Permission Check\n";
    $handle = @fopen($filename, 'w');
    if (@fwrite($handle, $testcontent) === FALSE) {
        @fclose($handle);
        add_option("wpgmza_permission","n");
        return false;
    }
    else {
        @fclose($handle);
        add_option("wpgmza_permission","y");
        return true;
    }


}
function wpgmaps_reload_map_on_post() {
    if (isset($_POST['wpgmza_savemap'])){

        $res = wpgmza_get_map_data(sanitize_text_field($_GET['map_id']));
        $wpgmza_lat = $res->map_start_lat;
        $wpgmza_lng = $res->map_start_lng;
        $wpgmza_width = intval($res->map_width);
        $wpgmza_height = intval($res->map_height);
        $wpgmza_width_type = $res->map_width_type;
        $wpgmza_height_type = $res->map_height_type;
        $wpgmza_map_type = $res->type;
        if (!$wpgmza_map_type || $wpgmza_map_type == "" || $wpgmza_map_type == "1") { $wpgmza_map_type = "ROADMAP"; }
        else if ($wpgmza_map_type == "2") { $wpgmza_map_type = "SATELLITE"; }
        else if ($wpgmza_map_type == "3") { $wpgmza_map_type = "HYBRID"; }
        else if ($wpgmza_map_type == "4") { $wpgmza_map_type = "TERRAIN"; }
        else { $wpgmza_map_type = "ROADMAP"; }
        $start_zoom = $res->map_start_zoom;
        if ($start_zoom < 1 || !$start_zoom) { $start_zoom = 5; }
        if (!$wpgmza_lat || !$wpgmza_lng) { $wpgmza_lat = "51.5081290"; $wpgmza_lng = "-0.1280050"; }

        ?>
        <script type="text/javascript">
            jQuery(function() {
                jQuery("#wpgmza_map").css({
                    height:'<?php echo $wpgmza_height; ?><?php echo $wpgmza_height_type; ?>',
                    width:'<?php echo $wpgmza_width; ?><?php echo $wpgmza_width_type; ?>'

                });
                var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                UniqueCode=Math.round(Math.random()*10010);
                MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url(sanitize_text_field($_GET['map_id'])); ?>?u='+UniqueCode,<?php echo sanitize_text_field($_GET['map_id']); ?>);

            });
        </script>
    <?php
    }


}
function wpgmaps_get_marker_url($mapid = false) {
    if (!$mapid) {
        $mapid = sanitize_text_field($_POST['map_id']);
    }
    if (!$mapid) {
        $mapid = sanitize_text_field($_GET['map_id']);
    }
    if (!$mapid) {
        global $wpgmza_current_map_id;
        $mapid = $wpgmza_current_map_id;
    }
    $mapid = intval($mapid);

    global $wpgmza_version;
    if (floatval($wpgmza_version) < 6 || $wpgmza_version == "6.0.4" || $wpgmza_version == "6.0.3" || $wpgmza_version == "6.0.2" || $wpgmza_version == "6.0.1" || $wpgmza_version == "6.0.0") {
        if (is_multisite()) { 
            global $blog_id;
            $wurl = wpgmaps_get_plugin_url()."/".$blog_id."-".$mapid."markers.xml";
        }
        else {
            $wurl = wpgmaps_get_plugin_url()."/".$mapid."markers.xml";
        }
    } else {
        /* later versions store marker files in wp-content/uploads/wp-google-maps director */
        
        if (get_option("wpgmza_xml_url") == "") {
            $upload_dir = wp_upload_dir();
            add_option("wpgmza_xml_url",'{uploads_url}/wp-google-maps/');
        }
        $xml_marker_url = wpgmza_return_marker_url();
        
        if (is_multisite()) { 
            global $blog_id;
            $wurl = $xml_marker_url.$blog_id."-".$mapid."markers.xml";;
        }
        else {
            $wurl = $xml_marker_url.$mapid."markers.xml";
        }
    }
    
    return $wurl;


}


function wpgmaps_admin_edit_marker_javascript() {

    $res = wpgmza_get_marker_data(sanitize_text_field($_GET['id']));
    $wpgmza_lat = $res->lat;
    $wpgmza_lng = $res->lng;
    $wpgmza_map_type = "ROADMAP";

    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    $api_version = $wpgmza_settings['wpgmza_api_version'];
    if (isset($api_version) && $api_version != "") {
        $api_version_string = "v=$api_version&";
    } else {
        $api_version_string = "v=3.14&";
    }
    ?>
    <script type="text/javascript">
        var gmapsJsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
        document.write(unescape("%3Cscript src='" + gmapsJsHost + "maps.google.com/maps/api/js?<?php echo $api_version_string; ?>sensor=false' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <link rel='stylesheet' id='wpgooglemaps-css'  href='<?php echo wpgmaps_get_plugin_url(); ?>/css/wpgmza_style.css' type='text/css' media='all' />
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo wpgmaps_get_plugin_url(); ?>/css/data_table.css" />
    <script type="text/javascript" src="<?php echo wpgmaps_get_plugin_url(); ?>/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" >
        jQuery(document).ready(function(){
            function wpgmza_InitMap() {
                var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                MYMAP.init('#wpgmza_map', myLatLng, 15);
            }
            jQuery("#wpgmza_map").css({
                height:400,
                width:400
            });
            wpgmza_InitMap();
        });

        var MYMAP = {
            map: null,
            bounds: null
        }
        MYMAP.init = function(selector, latLng, zoom) {
            var myOptions = {
                zoom:zoom,
                center: latLng,
                zoomControl: true,
                panControl: true,
                mapTypeControl: true,
                draggable: true,
                disableDoubleClickZoom: false,
                scrollwheel: true,
                streetViewControl: false,
                mapTypeId: google.maps.MapTypeId.<?php echo $wpgmza_map_type; ?>
            }
            this.map = new google.maps.Map(jQuery(selector)[0], myOptions);
            this.bounds = new google.maps.LatLngBounds();

            updateMarkerPosition(latLng);


            var marker = new google.maps.Marker({
                position: latLng,
                map: this.map,
                draggable: true
            });
            google.maps.event.addListener(marker, 'drag', function() {
                updateMarkerPosition(marker.getPosition());
            });
        }
        function updateMarkerPosition(latLng) {
            jQuery("#wpgmaps_marker_lat").val(latLng.lat());
            jQuery("#wpgmaps_marker_lng").val(latLng.lng());
        }


    </script>
<?php


}

function wpgmaps_admin_javascript_basic() {
    global $wpdb;
    global $wpgmza_tblname_maps;
    $ajax_nonce = wp_create_nonce("wpgmza");


    
    if (is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'wp-google-maps-menu' && isset( $_GET['action'] ) && $_GET['action'] == "edit_marker") {
        wpgmaps_admin_edit_marker_javascript();
    }
    else if (is_admin() && isset($_GET['action']) && isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "add_poly") { wpgmaps_b_admin_add_poly_javascript(sanitize_text_field($_GET['map_id'])); }
    else if (is_admin() && isset($_GET['action']) && isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_poly") { wpgmaps_b_admin_edit_poly_javascript(sanitize_text_field($_GET['map_id']),sanitize_text_field($_GET['poly_id'])); }
    else if (is_admin() && isset($_GET['action']) && isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "add_polyline") { wpgmaps_b_admin_add_polyline_javascript(sanitize_text_field($_GET['map_id'])); }
    else if (is_admin() && isset($_GET['action']) && isset($_GET['page']) && $_GET['page'] == 'wp-google-maps-menu' && $_GET['action'] == "edit_polyline") { wpgmaps_b_admin_edit_polyline_javascript(sanitize_text_field($_GET['map_id']),sanitize_text_field($_GET['poly_id'])); }

    else if (is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'wp-google-maps-menu' && isset( $_GET['action'] ) && $_GET['action'] == "edit") {

        if (!$_GET['map_id']) { return; }
        $wpgmza_check = wpgmaps_update_xml_file(sanitize_text_field($_GET['map_id']));
        if ( is_wp_error($wpgmza_check) ) wpgmza_return_error($wpgmza_check);
        
        

        $res = wpgmza_get_map_data(sanitize_text_field($_GET['map_id']));
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

        $map_other_settings = maybe_unserialize($res->other_settings);
        /*
        * deprecated in 6.2.0
        * 
        if (isset($map_other_settings['weather_layer'])) { $weather_layer = $map_other_settings['weather_layer']; } else { $weather_layer = 0; }
        if (isset($map_other_settings['weather_layer_temp_type'])) { $weather_layer_temp_type = $map_other_settings['weather_layer_temp_type']; } else { $weather_layer_temp_type = 0; }
        if (isset($map_other_settings['cloud_layer'])) { $cloud_layer = $map_other_settings['cloud_layer']; } else { $cloud_layer = 0; }
        */
        if (isset($map_other_settings['transport_layer'])) { $transport_layer = $map_other_settings['transport_layer']; } else { $transport_layer = 0; }
        
        
        if (isset($map_other_settings['map_max_zoom'])) { $wpgmza_max_zoom = intval($map_other_settings['map_max_zoom']); } else { $wpgmza_max_zoom = 2; }
        if (isset($map_other_settings['wpgmza_theme_data'])) { $wpgmza_theme_data = $map_other_settings['wpgmza_theme_data']; } else { $wpgmza_theme_data = false; }
        
        $wpgmza_lat = $res->map_start_lat;
        $wpgmza_lng = $res->map_start_lng;
        $wpgmza_width = $res->map_width;
        $wpgmza_height = $res->map_height;
        $wpgmza_width_type = $res->map_width_type;
        $wpgmza_height_type = $res->map_height_type;
        $wpgmza_map_type = $res->type;
        $wpgmza_traffic = $res->traffic;
        $wpgmza_bicycle = $res->bicycle;
        
        
        
        if (isset($wpgmza_settings['wpgmza_settings_map_open_marker_by'])) { $wpgmza_open_infowindow_by = $wpgmza_settings['wpgmza_settings_map_open_marker_by']; } else { $wpgmza_open_infowindow_by = null; }
        if ($wpgmza_open_infowindow_by == null || !isset($wpgmza_open_infowindow_by)) { $wpgmza_open_infowindow_by = '1'; }

        if (!$wpgmza_map_type || $wpgmza_map_type == "" || $wpgmza_map_type == "1") { $wpgmza_map_type = "ROADMAP"; }
        else if ($wpgmza_map_type == "2") { $wpgmza_map_type = "SATELLITE"; }
        else if ($wpgmza_map_type == "3") { $wpgmza_map_type = "HYBRID"; }
        else if ($wpgmza_map_type == "4") { $wpgmza_map_type = "TERRAIN"; }
        else { $wpgmza_map_type = "ROADMAP"; }
        $start_zoom = $res->map_start_zoom;
        if ($start_zoom < 1 || !$start_zoom) {
            $start_zoom = 5;
        }
        if (!$wpgmza_lat || !$wpgmza_lng) {
            $wpgmza_lat = "51.5081290";
            $wpgmza_lng = "-0.1280050";
        }
        
        if (isset($wpgmza_settings['wpgmza_api_version'])) { $api_version = $wpgmza_settings['wpgmza_api_version']; } else { $api_version = ""; }
        if (isset($api_version) && $api_version != "") {
            $api_version_string = "v=$api_version&";
        } else {
            $api_version_string = "v=3.exp&";
        }
        
        if (isset($wpgmza_settings['wpgmza_settings_marker_pull'])) { $marker_pull = $wpgmza_settings['wpgmza_settings_marker_pull']; } else { $marker_pull = "1"; }
        if (isset($marker_pull) && $marker_pull == "0") {
            if (!defined('PHP_VERSION_ID')) {
                $phpversion = explode('.', PHP_VERSION);
                define('PHP_VERSION_ID', ($phpversion[0] * 10000 + $phpversion[1] * 100 + $phpversion[2]));
            }
            if (PHP_VERSION_ID < 50300) {
                $markers = json_encode(wpgmaps_return_markers(sanitize_text_field($_GET['map_id'])));
            } else {
                $markers = json_encode(wpgmaps_return_markers(sanitize_text_field($_GET['map_id'])),JSON_HEX_APOS);    
            }
        }


        ?>
    
        
        <script type="text/javascript">
            var gmapsJsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
            document.write(unescape("%3Cscript src='" + gmapsJsHost + "maps.google.com/maps/api/js?<?php echo $api_version_string; ?>sensor=false&libraries=places' type='text/javascript'%3E%3C/script%3E"));
        </script>

        
        <link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

        <link rel='stylesheet' id='wpgooglemaps-css'  href='<?php echo wpgmaps_get_plugin_url(); ?>/css/wpgmza_style.css' type='text/css' media='all' />
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo wpgmaps_get_plugin_url(); ?>/css/data_table.css" />
        <script type="text/javascript" src="<?php echo wpgmaps_get_plugin_url(); ?>/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" >
            var marker_pull = '<?php echo $marker_pull; ?>';
            var placeSearch, autocomplete;


            <?php if (isset($markers) && strlen($markers) > 0 && $markers != "[]"){ ?>var db_marker_array = JSON.stringify(<?php echo $markers; ?>);<?php } else { echo "var db_marker_array = '';"; } ?>
                   


        if ('undefined' == typeof window.jQuery) {
            alert("jQuery is not installed. WP Google Maps requires jQuery in order to function properly. Please ensure you have jQuery installed.")
        } else {
            // all good.. continue...
        }

        jQuery(function() {
            
            
            function fillInAddress() {
              // Get the place details from the autocomplete object.
              var place = autocomplete.getPlace();
            }



            jQuery(document).ready(function(){

                if (typeof document.getElementById('wpgmza_add_address') !== "undefined") {
                    if (typeof google === 'object' && typeof google.maps === 'object' && typeof google.maps.places === 'object' && typeof google.maps.places.Autocomplete === 'function') {

                        /* initialize the autocomplete form */
                        autocomplete = new google.maps.places.Autocomplete(
                          /** @type {HTMLInputElement} */(document.getElementById('wpgmza_add_address')),
                          { types: ['geocode'] });
                        // When the user selects an address from the dropdown,
                        // populate the address fields in the form.
                        google.maps.event.addListener(autocomplete, 'place_changed', function() {
                            fillInAddress();
                        });
                    }
                }
            wpgmzaTable = jQuery('#wpgmza_table').dataTable({
                "bProcessing": true,
                "aaSorting": [[ 0, "desc" ]]
            });
            
            function wpgmza_reinitialisetbl() {
                wpgmzaTable.fnClearTable( 0 );
                wpgmzaTable = jQuery('#wpgmza_table').dataTable({
                    "bProcessing": true
                });
            }

            function wpgmza_InitMap() {
                var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                UniqueCode=Math.round(Math.random()*10000);
                MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url(intval($_GET['map_id'])); ?>?u='+UniqueCode,<?php echo intval($_GET['map_id']); ?>);
            }

            jQuery("#wpgmza_map").css({
                    height:'<?php echo $wpgmza_height; ?><?php echo $wpgmza_height_type; ?>',
                    width:'<?php echo $wpgmza_width; ?><?php echo $wpgmza_width_type; ?>'

                });
            var geocoder = new google.maps.Geocoder();
            wpgmza_InitMap();




                jQuery("body").on("click", ".wpgmza_del_btn", function() {
                    var cur_id = jQuery(this).attr("id");
                    var wpgm_map_id = "0";
                    if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                    var data = {
                        action: 'delete_marker',
                        security: '<?php echo $ajax_nonce; ?>',
                        map_id: wpgm_map_id,
                        marker_id: cur_id
                    };
                    jQuery.post(ajaxurl, data, function(response) {
                        returned_data = JSON.parse(response);
                        db_marker_array = JSON.stringify(returned_data.marker_data);
                        wpgmza_InitMap();
                        jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                        wpgmza_reinitialisetbl();
                        
                        
                    });


                });
                jQuery("body").on("click", ".wpgmza_poly_del_btn", function() {
                    var cur_id = jQuery(this).attr("id");
                    var wpgm_map_id = "0";
                    if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                    var data = {
                            action: 'delete_poly',
                            security: '<?php echo $ajax_nonce; ?>',
                            map_id: wpgm_map_id,
                            poly_id: cur_id
                    };
                    jQuery.post(ajaxurl, data, function(response) {
                            wpgmza_InitMap();
                            jQuery("#wpgmza_poly_holder").html(response);
                            window.location.reload();

                    });

                });
                jQuery("body").on("click", ".wpgmza_polyline_del_btn", function() {
                    var cur_id = jQuery(this).attr("id");
                    var wpgm_map_id = "0";
                    if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                    var data = {
                            action: 'delete_polyline',
                            security: '<?php echo $ajax_nonce; ?>',
                            map_id: wpgm_map_id,
                            poly_id: cur_id
                    };
                    jQuery.post(ajaxurl, data, function(response) {
                            wpgmza_InitMap();
                            jQuery("#wpgmza_polyline_holder").html(response);
                            window.location.reload();

                    });

                });

                var wpgmza_edit_address = ""; /* set this here so we can use it in the edit marker function below */
                var wpgmza_edit_lat = ""; 
                var wpgmza_edit_lng = ""; 
                jQuery("body").on("click", ".wpgmza_edit_btn", function() {
                    var cur_id = jQuery(this).attr("id");
                    wpgmza_edit_address = jQuery("#wpgmza_hid_marker_address_"+cur_id).val();
                    var wpgmza_edit_title = jQuery("#wpgmza_hid_marker_title_"+cur_id).val();
                    var wpgmza_edit_anim = jQuery("#wpgmza_hid_marker_anim_"+cur_id).val();
                    var wpgmza_edit_infoopen = jQuery("#wpgmza_hid_marker_infoopen_"+cur_id).val();

                    wpgmza_edit_lat = jQuery("#wpgmza_hid_marker_lat_"+cur_id).val();
                    wpgmza_edit_lng = jQuery("#wpgmza_hid_marker_lng_"+cur_id).val();
                    
                    jQuery("#wpgmza_edit_id").val(cur_id);
                    jQuery("#wpgmza_add_address").val(wpgmza_edit_address);
                    jQuery("#wpgmza_add_title").val(wpgmza_edit_title);
                    jQuery("#wpgmza_animation").val(wpgmza_edit_anim);
                    jQuery("#wpgmza_infoopen").val(wpgmza_edit_infoopen);
                    jQuery("#wpgmza_addmarker_div").hide();
                    jQuery("#wpgmza_editmarker_div").show();
                });

                jQuery("#wpgmza_addmarker").click(function(){
                    jQuery("#wpgmza_addmarker").hide();
                    jQuery("#wpgmza_addmarker_loading").show();

                    var wpgm_address = "0";
                    var wpgm_gps = "0";
                    var wpgm_map_id = "0";
                    if (document.getElementsByName("wpgmza_add_address").length > 0) { wpgm_address = jQuery("#wpgmza_add_address").val(); }
                    if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                    var wpgm_anim = "0";
                    var wpgm_infoopen = "0";
                    if (document.getElementsByName("wpgmza_animation").length > 0) { wpgm_anim = jQuery("#wpgmza_animation").val(); }
                    if (document.getElementsByName("wpgmza_infoopen").length > 0) { wpgm_infoopen = jQuery("#wpgmza_infoopen").val(); }

                    /* first check if user has added a GPS co-ordinate */
                    checker = wpgm_address.split(",");
                    var wpgm_lat = "";
                    var wpgm_lng = "";
                    wpgm_lat = checker[0];
                    wpgm_lng = checker[1];
                    checker1 = parseFloat(checker[0]);
                    checker2 = parseFloat(checker[1]);
                    if ((wpgm_lat.match(/[a-zA-Z]/g) === null && wpgm_lng.match(/[a-zA-Z]/g) === null) && checker.length === 2 && (checker1 != NaN && (checker1 <= 90 || checker1 >= -90)) && (checker2 != NaN && (checker2 <= 90 || checker2 >= -90))) {
                        var data = {
                            action: 'add_marker',
                            security: '<?php echo $ajax_nonce; ?>',
                            map_id: wpgm_map_id,
                            address: wpgm_address,
                            lat: wpgm_lat,
                            lng: wpgm_lng,
                            infoopen: wpgm_infoopen,
                            anim: wpgm_anim 
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            returned_data = JSON.parse(response);
                            db_marker_array = JSON.stringify(returned_data.marker_data);
                            wpgmza_InitMap();
                            jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                            jQuery("#wpgmza_addmarker").show();
                            jQuery("#wpgmza_addmarker_loading").hide();
                            jQuery("#wpgmza_add_address").val("");
                            jQuery("#wpgmza_animation").val("0");
                            jQuery("#wpgmza_infoopen").val("0");
                            wpgmza_reinitialisetbl();
                            var myLatLng = new google.maps.LatLng(wpgm_lat,wpgm_lng);
                            MYMAP.map.setCenter(myLatLng);
                            marker_added = false;
                        });
                    } else { 
                        geocoder.geocode ({ 'address': wpgm_address }, function(results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {

                                wpgm_gps = String(results[0].geometry.location);
                                var latlng1 = wpgm_gps.replace("(","");
                                var latlng2 = latlng1.replace(")","");
                                var latlngStr = latlng2.split(",",2);
                                wpgm_lat = parseFloat(latlngStr[0]);
                                wpgm_lng = parseFloat(latlngStr[1]);


                                var data = {
                                    action: 'add_marker',
                                    security: '<?php echo $ajax_nonce; ?>',
                                    map_id: wpgm_map_id,
                                    address: wpgm_address,
                                    lat: wpgm_lat,
                                    lng: wpgm_lng,
                                    infoopen: wpgm_infoopen,
                                    anim: wpgm_anim 
                                };
                                jQuery.post(ajaxurl, data, function(response) {
                                    returned_data = JSON.parse(response);
                                    db_marker_array = JSON.stringify(returned_data.marker_data);
                                    wpgmza_InitMap();
                                    jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                    jQuery("#wpgmza_addmarker").show();
                                    jQuery("#wpgmza_addmarker_loading").hide();
                                    jQuery("#wpgmza_add_address").val("");
                                    jQuery("#wpgmza_animation").val("0");
                                    jQuery("#wpgmza_infoopen").val("0");
                                    wpgmza_reinitialisetbl();
                                    var myLatLng = new google.maps.LatLng(wpgm_lat,wpgm_lng);
                                    MYMAP.map.setCenter(myLatLng);
                                    marker_added = false;
                                });
                                

                            } else {
                                alert("Geocode was not successful for the following reason: " + status);
                                jQuery("#wpgmza_addmarker").show();
                                jQuery("#wpgmza_addmarker_loading").hide();

                            }
                        });
                    }
                    
                    
                    
                    

                    


                });


                jQuery("#wpgmza_editmarker").click(function(){

                    jQuery("#wpgmza_editmarker_div").hide();
                    jQuery("#wpgmza_editmarker_loading").show();


                    var wpgm_edit_id;
                    wpgm_edit_id = parseInt(jQuery("#wpgmza_edit_id").val());
                    var wpgm_address = "0";
                    var wpgm_map_id = "0";
                    var wpgm_gps = "0";
                    var wpgm_anim = "0";
                    var wpgm_infoopen = "0";
                    if (document.getElementsByName("wpgmza_add_address").length > 0) { wpgm_address = jQuery("#wpgmza_add_address").val(); }
                    
                    var do_geocode;
                    if (wpgm_address === wpgmza_edit_address) {
                        do_geocode = false;
                        var wpgm_lat = wpgmza_edit_lat;
                        var wpgm_lng = wpgmza_edit_lng;
                    } else { 
                        do_geocode = true;
                    }
                    
                    if (document.getElementsByName("wpgmza_id").length > 0) { wpgm_map_id = jQuery("#wpgmza_id").val(); }
                    if (document.getElementsByName("wpgmza_animation").length > 0) { wpgm_anim = jQuery("#wpgmza_animation").val(); }
                    if (document.getElementsByName("wpgmza_infoopen").length > 0) { wpgm_infoopen = jQuery("#wpgmza_infoopen").val(); }

                    if (do_geocode === true) {

                    geocoder.geocode( { 'address': wpgm_address}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            wpgm_gps = String(results[0].geometry.location);
                            var latlng1 = wpgm_gps.replace("(","");
                            var latlng2 = latlng1.replace(")","");
                            var latlngStr = latlng2.split(",",2);
                            var wpgm_lat = parseFloat(latlngStr[0]);
                            var wpgm_lng = parseFloat(latlngStr[1]);

                            var data = {
                                action: 'edit_marker',
                                security: '<?php echo $ajax_nonce; ?>',
                                map_id: wpgm_map_id,
                                edit_id: wpgm_edit_id,
                                address: wpgm_address,
                                lat: wpgm_lat,
                                lng: wpgm_lng,
                                anim: wpgm_anim,
                                infoopen: wpgm_infoopen
                            };

                            jQuery.post(ajaxurl, data, function(response) {
                                returned_data = JSON.parse(response);
                                db_marker_array = JSON.stringify(returned_data.marker_data);

                                wpgmza_InitMap();
                                jQuery("#wpgmza_add_address").val("");
                                jQuery("#wpgmza_add_title").val("");
                                jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                jQuery("#wpgmza_addmarker_div").show();
                                jQuery("#wpgmza_editmarker_loading").hide();
                                jQuery("#wpgmza_edit_id").val("");
                                wpgmza_reinitialisetbl();
                                marker_added = false;
                            });

                        } else {
                            alert("Geocode was not successful for the following reason: " + status);
                        }
                    });
                    } else {
                        /* address was the same, no need for geocoding */
                        var data = {
                                action: 'edit_marker',
                                security: '<?php echo $ajax_nonce; ?>',
                                map_id: wpgm_map_id,
                                edit_id: wpgm_edit_id,
                                address: wpgm_address,
                                lat: wpgm_lat,
                                lng: wpgm_lng,
                                anim: wpgm_anim,
                                infoopen: wpgm_infoopen
                            };

                            jQuery.post(ajaxurl, data, function(response) {
                                returned_data = JSON.parse(response);
                                db_marker_array = JSON.stringify(returned_data.marker_data);
                                wpgmza_InitMap();
                                jQuery("#wpgmza_add_address").val("");
                                jQuery("#wpgmza_add_title").val("");
                                jQuery("#wpgmza_marker_holder").html(JSON.parse(response).table_html);
                                jQuery("#wpgmza_addmarker_div").show();
                                jQuery("#wpgmza_editmarker_loading").hide();
                                jQuery("#wpgmza_edit_id").val("");
                                wpgmza_reinitialisetbl();
                                marker_added = false;
                            });
                    }



                });
            });

        });


        var marker_added = false;
        var MYMAP = {
            map: null,
            bounds: null
        }
        MYMAP.init = function(selector, latLng, zoom) {
            var myOptions = {
                minZoom: <?php echo $wpgmza_max_zoom; ?>,
                maxZoom: 21,
                zoom:zoom,
                center: latLng,
                zoomControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_zoom']) && $wpgmza_settings['wpgmza_settings_map_zoom'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                panControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_pan']) && $wpgmza_settings['wpgmza_settings_map_pan'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                mapTypeControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_type']) && $wpgmza_settings['wpgmza_settings_map_type'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                streetViewControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_streetview']) && $wpgmza_settings['wpgmza_settings_map_streetview'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                draggable: <?php if (isset($wpgmza_settings['wpgmza_settings_map_draggable']) && $wpgmza_settings['wpgmza_settings_map_draggable'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                disableDoubleClickZoom: <?php if (isset($wpgmza_settings['wpgmza_settings_map_clickzoom']) && $wpgmza_settings['wpgmza_settings_map_clickzoom'] == "yes") { echo "true"; } else { echo "false"; } ?>,
                scrollwheel: <?php if (isset($wpgmza_settings['wpgmza_settings_map_scroll']) && $wpgmza_settings['wpgmza_settings_map_scroll'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                mapTypeId: google.maps.MapTypeId.<?php echo $wpgmza_map_type; ?>
            }
            this.map = new google.maps.Map(jQuery(selector)[0], myOptions);
            this.bounds = new google.maps.LatLngBounds();


            <?php if ($wpgmza_theme_data !== false && isset($wpgmza_theme_data) && $wpgmza_theme_data != '') { ?>
            this.map.setOptions({styles: <?php echo stripslashes($wpgmza_theme_data); ?>});
            <?php } ?>

            google.maps.event.addListener(MYMAP.map, 'rightclick', function(event) {
                if (marker_added === false) {
                    var marker = new google.maps.Marker({
                        position: event.latLng, 
                        map: MYMAP.map
                    });
                    marker.setDraggable(true);
                    google.maps.event.addListener(marker, 'dragend', function(event) { 
                        jQuery("#wpgmza_add_address").val(event.latLng.lat()+', '+event.latLng.lng());
                    } );
                    jQuery("#wpgmza_add_address").val(event.latLng.lat()+', '+event.latLng.lng());
                    jQuery("#wpgm_notice_message_save_marker").show();
                    marker_added = true;
                    setTimeout(function() {
                        jQuery("#wpgm_notice_message_save_marker").fadeOut('slow')
                    }, 3000);
                } else {
                    jQuery("#wpgm_notice_message_addfirst_marker").fadeIn('fast')
                    setTimeout(function() {
                        jQuery("#wpgm_notice_message_addfirst_marker").fadeOut('slow')
                    }, 3000);
                }
               
            });
            

            google.maps.event.addListener(MYMAP.map, 'zoom_changed', function() {
                zoomLevel = MYMAP.map.getZoom();

                jQuery("#wpgmza_start_zoom").val(zoomLevel);
                if (zoomLevel == 0) {
                    MYMAP.map.setZoom(10);
                }
            });
            
            
<?php
                $total_poly_array = wpgmza_b_return_polygon_id_array(sanitize_text_field($_GET['map_id']));
                if ($total_poly_array > 0) {
                foreach ($total_poly_array as $poly_id) {
                    $polyoptions = wpgmza_b_return_poly_options($poly_id);
                    $linecolor = $polyoptions->linecolor;
                    $fillcolor = $polyoptions->fillcolor;
                    $fillopacity = $polyoptions->opacity;
                    $lineopacity = $polyoptions->lineopacity;
                    $title = $polyoptions->title;
                    $link = $polyoptions->link;
                    $ohlinecolor = $polyoptions->ohlinecolor;
                    $ohfillcolor = $polyoptions->ohfillcolor;
                    $ohopacity = $polyoptions->ohopacity;
                    if (!$linecolor) { $linecolor = "000000"; }
                    if (!$fillcolor) { $fillcolor = "66FF00"; }
                    if ($fillopacity == "") { $fillopacity = "0.5"; }
                    if ($lineopacity == "") { $lineopacity = "1.0"; }
                    if ($ohlinecolor == "") { $ohlinecolor = $linecolor; }
                    if ($ohfillcolor == "") { $ohfillcolor = $fillcolor; }
                    if ($ohopacity == "") { $ohopacity = $fillopacity; }
                    $linecolor = "#".$linecolor;
                    $fillcolor = "#".$fillcolor;
                    $ohlinecolor = "#".$ohlinecolor;
                    $ohfillcolor = "#".$ohfillcolor;
                    
                    $poly_array = wpgmza_b_return_polygon_array($poly_id);
                    
                        
            ?> 

            <?php if (sizeof($poly_array) > 1) { ?>

            var WPGM_PathData_<?php echo $poly_id; ?> = [
                <?php
                        foreach ($poly_array as $single_poly) {
                            $poly_data_raw = str_replace(" ","",$single_poly);
                            $poly_data_raw = explode(",",$poly_data_raw);
                            $lat = $poly_data_raw[0];
                            $lng = $poly_data_raw[1];
                            ?>
                            new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),            
                            <?php
                        }
                ?>
                
               
            ];
            var WPGM_Path_<?php echo $poly_id; ?> = new google.maps.Polygon({
              path: WPGM_PathData_<?php echo $poly_id; ?>,
              strokeColor: "<?php echo $linecolor; ?>",
              fillOpacity: "<?php echo $fillopacity; ?>",
              strokeOpacity: "<?php echo $lineopacity; ?>",
              fillColor: "<?php echo $fillcolor; ?>",
              strokeWeight: 2
            });

            WPGM_Path_<?php echo $poly_id; ?>.setMap(this.map);
            <?php } } ?>

            <?php } ?>


           
<?php
                /* polylines */
                    $total_polyline_array = wpgmza_b_return_polyline_id_array(sanitize_text_field($_GET['map_id']));
                    if ($total_polyline_array > 0) {
                    foreach ($total_polyline_array as $poly_id) {
                        $polyoptions = wpgmza_b_return_polyline_options($poly_id);
                        $linecolor = $polyoptions->linecolor;
                        $fillopacity = $polyoptions->opacity;
                        $linethickness = $polyoptions->linethickness;
                        if (!$linecolor) { $linecolor = "000000"; }
                        if (!$linethickness) { $linethickness = "4"; }
                        if (!$fillopacity) { $fillopacity = "0.5"; }
                        $linecolor = "#".$linecolor;
                        $poly_array = wpgmza_b_return_polyline_array($poly_id);
                        ?>
                    
                <?php if (sizeof($poly_array) > 1) { ?>
                    var WPGM_PathLineData_<?php echo $poly_id; ?> = [
                    <?php
                    $poly_array = wpgmza_b_return_polyline_array($poly_id);

                    foreach ($poly_array as $single_poly) {
                        $poly_data_raw = str_replace(" ","",$single_poly);
                        $poly_data_raw = explode(",",$poly_data_raw);
                        $lat = $poly_data_raw[0];
                        $lng = $poly_data_raw[1];
                        ?>
                        new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),            
                        <?php
                    }
                    ?>
                ];
                var WPGM_PathLine_<?php echo $poly_id; ?> = new google.maps.Polyline({
                  path: WPGM_PathLineData_<?php echo $poly_id; ?>,
                  strokeColor: "<?php echo $linecolor; ?>",
                  strokeOpacity: "<?php echo $fillopacity; ?>",
                  strokeWeight: "<?php echo $linethickness; ?>"
                  
                });

                WPGM_PathLine_<?php echo $poly_id; ?>.setMap(this.map);
                    <?php } } } ?>    
            
            
            
            
            
            google.maps.event.addListener(MYMAP.map, 'center_changed', function() {
                var location = MYMAP.map.getCenter();
                jQuery("#wpgmza_start_location").val(location.lat()+","+location.lng());
                jQuery("#wpgmaps_save_reminder").show();
            });

            <?php if ($wpgmza_bicycle == "1") { ?>
            var bikeLayer = new google.maps.BicyclingLayer();
            bikeLayer.setMap(MYMAP.map);
            <?php } ?>
            <?php if ($wpgmza_traffic == "1") { ?>
            var trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(MYMAP.map);
            <?php } ?>
           
            <?php if ($transport_layer == 1) { ?>
            var transitLayer = new google.maps.TransitLayer();
            transitLayer.setMap(MYMAP.map);
            <?php } ?>



            google.maps.event.addListener(MYMAP.map, 'click', function() {
                infoWindow.close();
            });


        }

        var infoWindow = new google.maps.InfoWindow();
        <?php
            $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
            $wpgmza_settings_infowindow_width = "250";
            if (isset($wpgmza_settings['wpgmza_settings_infowindow_width'])) { $wpgmza_settings_infowindow_width = $wpgmza_settings['wpgmza_settings_infowindow_width']; }
            if (!isset($wpgmza_settings_infowindow_width) || !$wpgmza_settings_infowindow_width) { $wpgmza_settings_infowindow_width = "250"; }
        ?>
        infoWindow.setOptions({maxWidth:<?php echo $wpgmza_settings_infowindow_width; ?>});


        

        MYMAP.placeMarkers = function(filename,map_id) {
            marker_array = [];
            
            if (marker_pull === '1') {
            
            
                jQuery.get(filename, function(xml){
                    jQuery(xml).find("marker").each(function(){
                        var wpmgza_map_id = jQuery(this).find('map_id').text();
                        if (wpmgza_map_id == map_id) {
                            var wpmgza_address = jQuery(this).find('address').text();
                            var wpmgza_anim = jQuery(this).find('anim').text();
                            var wpmgza_infoopen = jQuery(this).find('infoopen').text();
                            var lat = jQuery(this).find('lat').text();
                            var lng = jQuery(this).find('lng').text();
                            var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                            MYMAP.bounds.extend(point);

                            if (wpmgza_anim === "1") {
                                var marker = new google.maps.Marker({
                                        position: point,
                                        map: MYMAP.map,
                                        animation: google.maps.Animation.BOUNCE
                                });
                            }
                            else if (wpmgza_anim === "2") {
                                var marker = new google.maps.Marker({
                                        position: point,
                                        map: MYMAP.map,
                                        animation: google.maps.Animation.DROP
                                });
                            }
                            else {
                                var marker = new google.maps.Marker({
                                        position: point,
                                        map: MYMAP.map
                                });
                            }


                            var html='<p class="wpgmza_infowinfow_address" style="margin-top:0; padding-top:0; margin-bottom:2px; padding-bottom:2px; font-weight:bold;">'+wpmgza_address+'</p>';

                            if (wpmgza_infoopen === "1") {
                                
                                infoWindow.setContent(html);
                                infoWindow.open(MYMAP.map, marker);
                            }

                            <?php if ($wpgmza_open_infowindow_by == '2') { ?>
                            google.maps.event.addListener(marker, 'mouseover', function() {
                                infoWindow.close();
                                infoWindow.setContent(html);
                                infoWindow.open(MYMAP.map, marker);

                            });
                            <?php } else { ?>
                            google.maps.event.addListener(marker, 'click', function() {
                                infoWindow.close();
                                infoWindow.setContent(html);
                                infoWindow.open(MYMAP.map, marker);

                            });
                            <?php } ?>

                        }

                    });
                });
            } else {
                if (db_marker_array.length > 0) {
                var dec_marker_array = jQuery.parseJSON(db_marker_array);
                jQuery.each(dec_marker_array, function(i, val) {
                
                
                    var wpmgza_address = val.address;
                    var wpmgza_anim = val.anim;
                    var wpmgza_infoopen = val.infoopen;
                    var lat = val.lat;
                    var lng = val.lng;
                    var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                    MYMAP.bounds.extend(point);

                    if (wpmgza_anim === "1") {
                        var marker = new google.maps.Marker({
                                position: point,
                                map: MYMAP.map,
                                animation: google.maps.Animation.BOUNCE
                        });
                    }
                    else if (wpmgza_anim === "2") {
                        var marker = new google.maps.Marker({
                                position: point,
                                map: MYMAP.map,
                                animation: google.maps.Animation.DROP
                        });
                    }
                    else {
                        var marker = new google.maps.Marker({
                                position: point,
                                map: MYMAP.map
                        });
                    }


                    var html='<p class="wpgmza_infowinfow_address" style="margin-top:0; padding-top:0; margin-bottom:2px; padding-bottom:2px; font-weight:bold;">'+wpmgza_address+'</p>';

                    if (wpmgza_infoopen === "1") {
                        
                        infoWindow.setContent(html);
                        infoWindow.open(MYMAP.map, marker);
                    }

                    <?php if ($wpgmza_open_infowindow_by == '2') { ?>
                    google.maps.event.addListener(marker, 'mouseover', function() {
                        infoWindow.close();
                        infoWindow.setContent(html);
                        infoWindow.open(MYMAP.map, marker);

                    });
                    <?php } else { ?>
                    google.maps.event.addListener(marker, 'click', function() {
                        infoWindow.close();
                        infoWindow.setContent(html);
                        infoWindow.open(MYMAP.map, marker);

                    });
                    <?php } ?>
                
                
                
                
                
                
              });
            
            }
            }
        }






        </script>
    <?php
    }

}


function wpgmaps_user_javascript_basic() {
    global $short_code_active;
    global $wpgmza_current_map_id;

    if ($short_code_active) {

        $ajax_nonce = wp_create_nonce("wpgmza");


        $res = wpgmza_get_map_data($wpgmza_current_map_id);
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
        
        if (isset($wpgmza_settings['wpgmza_api_version'])) { 
            $api_version = $wpgmza_settings['wpgmza_api_version'];
            if (isset($api_version) && $api_version != "") {
                $api_version_string = "v=$api_version&";
            } else {
                $api_version_string = "v=3.14&";
            }
        } else {
            $api_version_string = "v=3.exp&";
        }
        
        $map_other_settings = maybe_unserialize($res->other_settings);
        /*
         * deprecated in 6.2.0
         
        if (isset($map_other_settings['weather_layer'])) { $weather_layer = $map_other_settings['weather_layer']; }  else { $weather_layer = false; }
        if (isset($map_other_settings['weather_layer_temp_type'])) { $weather_layer_temp_type = $map_other_settings['weather_layer_temp_type']; } else { $weather_layer_temp_type = false; }
        if (isset($map_other_settings['cloud_layer'])) { $cloud_layer = $map_other_settings['cloud_layer']; } else { $cloud_layer = false; }
        */
       
        if (isset($map_other_settings['transport_layer'])) { $transport_layer = $map_other_settings['transport_layer']; } else { $transport_layer = false; }
        if (isset($map_other_settings['store_locator_bounce'])) { $store_locator_bounce = $map_other_settings['store_locator_bounce']; } else { $store_locator_bounce = 1; }
        
        $wpgmza_lat = $res->map_start_lat;
        $wpgmza_lng = $res->map_start_lng;
        $wpgmza_width = $res->map_width;
        $wpgmza_height = $res->map_height;
        $wpgmza_width_type = $res->map_width_type;
        $wpgmza_height_type = $res->map_height_type;
        $wpgmza_map_type = $res->type;
        $wpgmza_traffic = $res->traffic;
        $wpgmza_bicycle = $res->bicycle;

        if (isset($map_other_settings['map_max_zoom'])) { $wpgmza_max_zoom = intval($map_other_settings['map_max_zoom']); } else { $wpgmza_max_zoom = 2; }
        if (isset($map_other_settings['wpgmza_theme_data'])) { $wpgmza_theme_data = $map_other_settings['wpgmza_theme_data']; } else { $wpgmza_theme_data = false; }

        
        if (isset($wpgmza_settings['wpgmza_settings_map_open_marker_by'])) { $wpgmza_open_infowindow_by = $wpgmza_settings['wpgmza_settings_map_open_marker_by']; } else { $wpgmza_open_infowindow_by = '1'; }
        if ($wpgmza_open_infowindow_by == null || !isset($wpgmza_open_infowindow_by)) { $wpgmza_open_infowindow_by = '1'; }

        if (!$wpgmza_map_type || $wpgmza_map_type == "" || $wpgmza_map_type == "1") { $wpgmza_map_type = "ROADMAP"; }
        else if ($wpgmza_map_type == "2") { $wpgmza_map_type = "SATELLITE"; }
        else if ($wpgmza_map_type == "3") { $wpgmza_map_type = "HYBRID"; }
        else if ($wpgmza_map_type == "4") { $wpgmza_map_type = "TERRAIN"; }
        else { $wpgmza_map_type = "ROADMAP"; }
        $start_zoom = $res->map_start_zoom;
        if ($start_zoom < 1 || !$start_zoom) { $start_zoom = 5; }
        if (!$wpgmza_lat || !$wpgmza_lng) { $wpgmza_lat = "51.5081290"; $wpgmza_lng = "-0.1280050"; }

        
        
        if (isset($wpgmza_settings['wpgmza_settings_marker_pull'])) { $marker_pull = $wpgmza_settings['wpgmza_settings_marker_pull']; } else { $marker_pull = "1"; }
        $restrict_search = false;
        if (isset($map_other_settings['wpgmza_store_locator_restrict'])) { $restrict_search = $map_other_settings['wpgmza_store_locator_restrict']; } else { $restrict_search = false; }
        if (isset($marker_pull) && $marker_pull == "0") {
            $markers = json_encode(wpgmaps_return_markers($wpgmza_current_map_id));
        }
        
        
        ?>
        
        
        <script type="text/javascript">
            var gmapsJsHost = (("https:" == document.location.protocol) ? "https://" : "http://");
            document.write(unescape("%3Cscript src='" + gmapsJsHost + "maps.google.com/maps/api/js?<?php echo $api_version_string; ?>sensor=false&libraries=places' type='text/javascript'%3E%3C/script%3E"));
        </script>
       
        <script type="text/javascript" >
            var marker_pull = '<?php echo $marker_pull; ?>';
            <?php if (isset($markers) && strlen($markers) > 0 && $markers != "[]"){ ?>var db_marker_array = JSON.stringify(<?php echo $markers; ?>);<?php } else { echo "var db_marker_array = '';"; } ?>
                   
            if ('undefined' === typeof window.jQuery) {
                setTimeout(function(){ 
                    document.getElementById('wpgmza_map').innerHTML = 'Error: In order for WP Google Maps to work, jQuery must be installed. A check was done and jQuery was not present. Please see the <a href="http://www.wpgmaps.com/documentation/troubleshooting/jquery-troubleshooting/" title="WP Google Maps - jQuery Troubleshooting">jQuery troubleshooting section of our site</a> for more information.';
                }, 3000);
            } else {
                /* all good.. continue... */
            }

            jQuery(function() {


                jQuery(document).ready(function(){
                    if (/1\.(0|1|2|3|4|5|6|7)\.(0|1|2|3|4|5|6|7|8|9)/.test(jQuery.fn.jquery)) {
                        setTimeout(function(){ 
                            document.getElementById('wpgmza_map').innerHTML = 'Error: Your version of jQuery is outdated. WP Google Maps requires jQuery version 1.7+ to function correctly. Go to Maps->Settings and check the box that allows you to over-ride your current jQuery to try eliminate this problem.';
                        }, 3000);
                    } else {

                        jQuery("#wpgmza_map").css({
                            height:'<?php echo $wpgmza_height; ?><?php echo $wpgmza_height_type; ?>',
                            width:'<?php echo $wpgmza_width; ?><?php echo $wpgmza_width_type; ?>'
                        });
                        var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                        MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                        UniqueCode=Math.round(Math.random()*10000);
                        MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);

                        jQuery('body').on('tabsactivate', function(event, ui) {
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });

 
                        jQuery('body').on('click', '.wpb_tabs_nav li', function(event, ui) {
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });           
                        /* tab compatibility */

                        jQuery('body').on('click', '.ui-tabs-nav li', function(event, ui) {
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });
                        jQuery('body').on('click', '.tp-tabs li a', function(event, ui) {
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });
                        jQuery('body').on('click', '.nav-tabs li a', function(event, ui) {
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });

                        jQuery('body').on('click', '.x-accordion-heading', function(){ 
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });
                        jQuery('body').on('click', '.x-nav-tabs li', function (event, ui) { 
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });  
                        jQuery('body').on('click', '.tab-title', function (event, ui) { 
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });  
                        jQuery('body').on('click', '.tab-link', function (event, ui) { 
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });  
                        jQuery('body').on('click', '.et_pb_tabs_controls li', function (event, ui) { 
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });  
                        jQuery('body').on('click', '.fusion-tab-heading', function (event, ui) { 
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });  
                        jQuery('body').on('click', '.et_pb_tab', function (event, ui) { 
                            MYMAP.init('#wpgmza_map', myLatLng, <?php echo $start_zoom; ?>);
                            UniqueCode=Math.round(Math.random()*10000);
                            MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,<?php echo $wpgmza_current_map_id; ?>,null,null,null);
                        });  
                        
                        
                        /* compatibility for GDL tabs */
                        jQuery('body').on('click', '.gdl-tabs li', function(event, ui) {
                            for(var entry in wpgmaps_localize) {
                                InitMap(wpgmaps_localize[entry]['id'],'all',false);
                            }
                        });      

                        jQuery('body').on('click', '#tabnav  li', function(event, ui) {
                            for(var entry in wpgmaps_localize) {
                                InitMap(wpgmaps_localize[entry]['id'],'all',false);
                            }
                        });



                    }

                });

            });


            var MYMAP = {
                map: null,
                bounds: null
            }

            MYMAP.init = function(selector, latLng, zoom) {
                var myOptions = {
                    zoom:zoom,
                    minZoom: <?php echo $wpgmza_max_zoom; ?>,
                    maxZoom: 21,
                    center: latLng,
                    zoomControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_zoom']) && $wpgmza_settings['wpgmza_settings_map_zoom'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                    panControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_pan']) && $wpgmza_settings['wpgmza_settings_map_pan'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                    mapTypeControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_type']) && $wpgmza_settings['wpgmza_settings_map_type'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                    streetViewControl: <?php if (isset($wpgmza_settings['wpgmza_settings_map_streetview']) && $wpgmza_settings['wpgmza_settings_map_streetview'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                    draggable: <?php if (isset($wpgmza_settings['wpgmza_settings_map_draggable']) && $wpgmza_settings['wpgmza_settings_map_draggable'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                    disableDoubleClickZoom: <?php if (isset($wpgmza_settings['wpgmza_settings_map_clickzoom']) && $wpgmza_settings['wpgmza_settings_map_clickzoom'] == "yes") { echo "true"; } else { echo "false"; } ?>,
                    scrollwheel: <?php if (isset($wpgmza_settings['wpgmza_settings_map_scroll']) && $wpgmza_settings['wpgmza_settings_map_scroll'] == "yes") { echo "false"; } else { echo "true"; } ?>,
                    mapTypeId: google.maps.MapTypeId.<?php echo $wpgmza_map_type; ?>
                }

                this.map = new google.maps.Map(jQuery(selector)[0], myOptions);
                this.bounds = new google.maps.LatLngBounds();

                <?php if ($wpgmza_theme_data !== false && isset($wpgmza_theme_data) && $wpgmza_theme_data != '') { ?>
                    this.map.setOptions({styles: <?php echo stripslashes($wpgmza_theme_data); ?>});
                <?php } ?>
                
                
                
<?php
                $total_poly_array = wpgmza_b_return_polygon_id_array($wpgmza_current_map_id);
                if ($total_poly_array > 0) {
                foreach ($total_poly_array as $poly_id) {
                    $polyoptions = wpgmza_b_return_poly_options($poly_id);
                    $linecolor = $polyoptions->linecolor;
                    $lineopacity = $polyoptions->lineopacity;
                    $fillcolor = $polyoptions->fillcolor;
                    $fillopacity = $polyoptions->opacity;
                    if (!$linecolor) { $linecolor = "000000"; }
                    if (!$fillcolor) { $fillcolor = "66FF00"; }
                    if ($lineopacity == "") { $lineopacity = "1.0"; }
                    if ($fillopacity == "") { $fillopacity = "0.5"; }
                    $linecolor = "#".$linecolor;
                    $fillcolor = "#".$fillcolor;
                    
                    $poly_array = wpgmza_b_return_polygon_array($poly_id);
                    
                        
            ?> 

            <?php if (sizeof($poly_array) > 1) { ?>

            var WPGM_PathData_<?php echo $poly_id; ?> = [
                <?php
                        foreach ($poly_array as $single_poly) {
                            $poly_data_raw = str_replace(" ","",$single_poly);
                            $poly_data_raw = explode(",",$poly_data_raw);
                            $lat = $poly_data_raw[0];
                            $lng = $poly_data_raw[1];
                            ?>
                            new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),            
                            <?php
                        }
                ?>
                
               
            ];
            var WPGM_Path_<?php echo $poly_id; ?> = new google.maps.Polygon({
              path: WPGM_PathData_<?php echo $poly_id; ?>,
              strokeColor: "<?php echo $linecolor; ?>",
              strokeOpacity: "<?php echo $lineopacity; ?>",
              fillOpacity: "<?php echo $fillopacity; ?>",
              fillColor: "<?php echo $fillcolor; ?>",
              strokeWeight: 2
            });

            WPGM_Path_<?php echo $poly_id; ?>.setMap(this.map);
            <?php } } ?>

            <?php } ?>


           
<?php
                /* polylines */
                    $total_polyline_array = wpgmza_b_return_polyline_id_array($wpgmza_current_map_id);
                    if ($total_polyline_array > 0) {
                    foreach ($total_polyline_array as $poly_id) {
                        $polyoptions = wpgmza_b_return_polyline_options($poly_id);
                        $linecolor = $polyoptions->linecolor;
                        $fillopacity = $polyoptions->opacity;
                        $linethickness = $polyoptions->linethickness;
                        if (!$linecolor) { $linecolor = "000000"; }
                        if (!$linethickness) { $linethickness = "4"; }
                        if (!$fillopacity) { $fillopacity = "0.5"; }
                        $linecolor = "#".$linecolor;
                        $poly_array = wpgmza_b_return_polyline_array($poly_id);
                        ?>
                    
                <?php if (sizeof($poly_array) > 1) { ?>
                    var WPGM_PathLineData_<?php echo $poly_id; ?> = [
                    <?php
                    $poly_array = wpgmza_b_return_polyline_array($poly_id);

                    foreach ($poly_array as $single_poly) {
                        $poly_data_raw = str_replace(" ","",$single_poly);
                        $poly_data_raw = explode(",",$poly_data_raw);
                        $lat = $poly_data_raw[0];
                        $lng = $poly_data_raw[1];
                        ?>
                        new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),            
                        <?php
                    }
                    ?>
                ];
                var WPGM_PathLine_<?php echo $poly_id; ?> = new google.maps.Polyline({
                  path: WPGM_PathLineData_<?php echo $poly_id; ?>,
                  strokeColor: "<?php echo $linecolor; ?>",
                  strokeOpacity: "<?php echo $fillopacity; ?>",
                  strokeWeight: "<?php echo $linethickness; ?>"
                  
                });

                WPGM_PathLine_<?php echo $poly_id; ?>.setMap(this.map);
                    <?php } } } ?>                 
                
                
                
                <?php if (isset($wpgmza_bicycle) && $wpgmza_bicycle == "1") { ?>
                var bikeLayer = new google.maps.BicyclingLayer();
                bikeLayer.setMap(this.map);
                <?php } ?>
                <?php if (isset($wpgmza_traffic) && $wpgmza_traffic == "1") { ?>
                var trafficLayer = new google.maps.TrafficLayer();
                trafficLayer.setMap(this.map);
                <?php } ?>
                <?php if (isset($transport_layer) && $transport_layer == 1) { ?>
                var transitLayer = new google.maps.TransitLayer();
                transitLayer.setMap(MYMAP.map);
                <?php } ?>
                
                google.maps.event.addListener(MYMAP.map, 'click', function() {
                    infoWindow.close();
                });
                
                
                
            }

            var infoWindow = new google.maps.InfoWindow();
            <?php
                $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
                $wpgmza_settings_infowindow_width = "250";
                if (isset($wpgmza_settings['wpgmza_settings_infowindow_width'])) { $wpgmza_settings_infowindow_width = $wpgmza_settings['wpgmza_settings_infowindow_width']; }
                if (!isset($wpgmza_settings_infowindow_width) || !$wpgmza_settings_infowindow_width) { $wpgmza_settings_infowindow_width = "250"; }
            ?>
            infoWindow.setOptions({maxWidth:<?php echo $wpgmza_settings_infowindow_width; ?>});

            google.maps.event.addDomListener(window, 'resize', function() {
                var myLatLng = new google.maps.LatLng(<?php echo $wpgmza_lat; ?>,<?php echo $wpgmza_lng; ?>);
                MYMAP.map.setCenter(myLatLng);
            });
            MYMAP.placeMarkers = function(filename,map_id,radius,searched_center,distance_type) {
                var check1 = 0;
                if (marker_pull === '1') {
                
                    jQuery.get(filename, function(xml){
                        jQuery(xml).find("marker").each(function(){
                            var wpmgza_map_id = jQuery(this).find('map_id').text();

                            if (wpmgza_map_id == map_id) {
                                var wpmgza_address = jQuery(this).find('address').text();
                                var lat = jQuery(this).find('lat').text();
                                var lng = jQuery(this).find('lng').text();
                                var wpmgza_anim = jQuery(this).find('anim').text();
                                var wpmgza_infoopen = jQuery(this).find('infoopen').text();
                                var current_lat = jQuery(this).find('lat').text();
                                var current_lng = jQuery(this).find('lng').text();
                                var show_marker_radius = true;

                                if (radius !== null) {
                                    if (check1 > 0 ) { } else { 


                                        var point = new google.maps.LatLng(parseFloat(searched_center.lat()),parseFloat(searched_center.lng()));
                                        MYMAP.bounds.extend(point);
                                        <?php if ($store_locator_bounce == 1) { ?>
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP.map,
                                                animation: google.maps.Animation.BOUNCE
                                        });
                                        <?php } else { /* dont show icon */ ?>


                                        <?php } ?>
                                        if (distance_type === "1") {
                                            var populationOptions = {
                                                  strokeColor: '#FF0000',
                                                  strokeOpacity: 0.25,
                                                  strokeWeight: 2,
                                                  fillColor: '#FF0000',
                                                  fillOpacity: 0.15,
                                                  map: MYMAP.map,
                                                  center: point,
                                                  radius: parseInt(radius / 0.000621371)
                                                };
                                        } else {
                                            var populationOptions = {
                                                  strokeColor: '#FF0000',
                                                  strokeOpacity: 0.25,
                                                  strokeWeight: 2,
                                                  fillColor: '#FF0000',
                                                  fillOpacity: 0.15,
                                                  map: MYMAP.map,
                                                  center: point,
                                                  radius: parseInt(radius / 0.001)
                                                };
                                        }
                                        
                                        cityCircle = new google.maps.Circle(populationOptions);
                                        check1 = check1 + 1;
                                    }
                                    var R = 0;
                                    if (distance_type === "1") {
                                        R = 3958.7558657440545; 
                                    } else {
                                        R = 6378.16; 
                                    }
                                    var dLat = toRad(searched_center.lat()-current_lat);
                                    var dLon = toRad(searched_center.lng()-current_lng); 
                                    var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(toRad(current_lat)) * Math.cos(toRad(searched_center.lat())) * Math.sin(dLon/2) * Math.sin(dLon/2); 
                                    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                                    var d = R * c;
                                    
                                    if (d < radius) { show_marker_radius = true; } else { show_marker_radius = false; }
                                }



                                var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                                MYMAP.bounds.extend(point);
                                if (show_marker_radius === true) {
                                    if (wpmgza_anim === "1") {
                                    var marker = new google.maps.Marker({
                                            position: point,
                                            map: MYMAP.map,
                                            animation: google.maps.Animation.BOUNCE
                                        });
                                    }
                                    else if (wpmgza_anim === "2") {
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP.map,
                                                animation: google.maps.Animation.DROP
                                        });
                                    }
                                    else {
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP.map
                                        });
                                    }
                                    var d_string = "";
                                    if (radius !== null) {                                 
                                        if (distance_type === "1") {
                                            d_string = "<p style='min-width:100px; display:block;'>"+Math.round(d,2)+"<?php _e("miles away","wp-google-maps") ?> </p>"; 
                                        } else {
                                            d_string = "<p style='min-width:100px; display:block;'>"+Math.round(d,2)+"<?php _e("km away","wp-google-maps") ?> </p>"; 
                                        }
                                    } else { d_string = ''; }


                                    var html='<p style=\'min-width:100px; display:block;\'>'+wpmgza_address+'</p>'+d_string;
                                    if (wpmgza_infoopen === "1") {
                                        infoWindow.setContent(html);
                                        infoWindow.open(MYMAP.map, marker);
                                    }
                                    <?php if ($wpgmza_open_infowindow_by == '2') { ?>
                                    google.maps.event.addListener(marker, 'mouseover', function() {
                                        infoWindow.close();
                                        infoWindow.setContent(html);
                                        infoWindow.open(MYMAP.map, marker);

                                    });
                                    <?php } else { ?>
                                    google.maps.event.addListener(marker, 'click', function() {
                                        infoWindow.close();
                                        infoWindow.setContent(html);
                                        infoWindow.open(MYMAP.map, marker);

                                    });
                                    <?php } ?>
                                }
                            }
                        });

                    });
                } else { 
                
                    if (db_marker_array.length > 0) {
                        var dec_marker_array = jQuery.parseJSON(db_marker_array);
                        jQuery.each(dec_marker_array, function(i, val) {
                            
                            
                            var wpmgza_map_id = val.map_id;

                                if (wpmgza_map_id == map_id) {
                                    
                                    var wpmgza_address = val.address;
                                    var wpmgza_anim = val.anim;
                                    var wpmgza_infoopen = val.infoopen;
                                    var lat = val.lat;
                                    var lng = val.lng;
                                    var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                                    
                                   
                                    var current_lat = val.lat;
                                    var current_lng = val.lng;
                                    var show_marker_radius = true;

                                    if (radius !== null) {
                                        if (check1 > 0 ) { } else { 


                                            var point = new google.maps.LatLng(parseFloat(searched_center.lat()),parseFloat(searched_center.lng()));
                                            MYMAP.bounds.extend(point);
                                            <?php if ($store_locator_bounce == 1) { ?>
                                            var marker = new google.maps.Marker({
                                                    position: point,
                                                    map: MYMAP.map,
                                                    animation: google.maps.Animation.BOUNCE
                                            });
                                            <?php } else { /* dont show icon */ ?>


                                            <?php } ?>
                                            if (distance_type === "1") {
                                                var populationOptions = {
                                                      strokeColor: '#FF0000',
                                                      strokeOpacity: 0.25,
                                                      strokeWeight: 2,
                                                      fillColor: '#FF0000',
                                                      fillOpacity: 0.15,
                                                      map: MYMAP.map,
                                                      center: point,
                                                      radius: parseInt(radius / 0.000621371)
                                                    };
                                            } else {
                                                var populationOptions = {
                                                      strokeColor: '#FF0000',
                                                      strokeOpacity: 0.25,
                                                      strokeWeight: 2,
                                                      fillColor: '#FF0000',
                                                      fillOpacity: 0.15,
                                                      map: MYMAP.map,
                                                      center: point,
                                                      radius: parseInt(radius / 0.001)
                                                    };
                                            }
                                            
                                            cityCircle = new google.maps.Circle(populationOptions);
                                            check1 = check1 + 1;
                                        }
                                        var R = 0;
                                        if (distance_type === "1") {
                                            R = 3958.7558657440545; 
                                        } else {
                                            R = 6378.16; 
                                        }
                                        var dLat = toRad(searched_center.lat()-current_lat);
                                        var dLon = toRad(searched_center.lng()-current_lng); 
                                        var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(toRad(current_lat)) * Math.cos(toRad(searched_center.lat())) * Math.sin(dLon/2) * Math.sin(dLon/2); 
                                        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
                                        var d = R * c;
                                        
                                        if (d < radius) { show_marker_radius = true; } else { show_marker_radius = false; }
                                    }



                                    var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
                                    MYMAP.bounds.extend(point);
                                    if (show_marker_radius === true) {
                                        if (wpmgza_anim === "1") {
                                        var marker = new google.maps.Marker({
                                                position: point,
                                                map: MYMAP.map,
                                                animation: google.maps.Animation.BOUNCE
                                            });
                                        }
                                        else if (wpmgza_anim === "2") {
                                            var marker = new google.maps.Marker({
                                                    position: point,
                                                    map: MYMAP.map,
                                                    animation: google.maps.Animation.DROP
                                            });
                                        }
                                        else {
                                            var marker = new google.maps.Marker({
                                                    position: point,
                                                    map: MYMAP.map
                                            });
                                        }
                                        var d_string = "";
                                        if (radius !== null) {                                 
                                            if (distance_type === "1") {
                                                d_string = "<p style='min-width:100px; display:block;'>"+Math.round(d,2)+"<?php _e("miles away","wp-google-maps") ?> </p>"; 
                                            } else {
                                                d_string = "<p style='min-width:100px; display:block;'>"+Math.round(d,2)+"<?php _e("km away","wp-google-maps") ?> </p>"; 
                                            }
                                        } else { d_string = ''; }


                                        var html='<p style=\'min-width:100px; display:block;\'>'+wpmgza_address+'</p>'+d_string;
                                        if (wpmgza_infoopen === "1") {
                                            infoWindow.setContent(html);
                                            infoWindow.open(MYMAP.map, marker);
                                        }
                                        <?php if ($wpgmza_open_infowindow_by == '2') { ?>
                                        google.maps.event.addListener(marker, 'mouseover', function() {
                                            infoWindow.close();
                                            infoWindow.setContent(html);
                                            infoWindow.open(MYMAP.map, marker);

                                        });
                                        <?php } else { ?>
                                        google.maps.event.addListener(marker, 'click', function() {
                                            infoWindow.close();
                                            infoWindow.setContent(html);
                                            infoWindow.open(MYMAP.map, marker);

                                        });
                                        <?php } ?>
                                    }
                                }
                            
                            
                            
                            
                            
                        });
                    }
            
            
                            
                
                }
            }
            jQuery("body").on("keypress","#addressInput", function(event) {
              if ( event.which == 13 ) {
                 jQuery('.wpgmza_sl_search_button').trigger('click');
              }
            });
            var autocomplete;
            function fillInAddress() {
              // Get the place details from the autocomplete object.
              var place = autocomplete.getPlace();
            }
            var elementExists = document.getElementById("addressInput");
            if (typeof google === 'object' && typeof google.maps === 'object' && typeof google.maps.places === 'object' && typeof google.maps.places.Autocomplete === 'function') {
                if (elementExists !== null) {
                    /* initialize the autocomplete form */
                    autocomplete = new google.maps.places.Autocomplete(
                      /** @type {HTMLInputElement} */(document.getElementById('addressInput')),
                      { types: ['geocode'] });
                    // When the user selects an address from the dropdown,
                    // populate the address fields in the form.
                    google.maps.event.addListener(autocomplete, 'place_changed', function() {
                    fillInAddress();
                    });
                } 
            }


            function searchLocations(map_id) {
                var address = document.getElementById("addressInput").value;
                var geocoder = new google.maps.Geocoder();


            <?php if (!$restrict_search) { ?>
                geocoder.geocode({address: address}, function(results, status) {
                  if (status === google.maps.GeocoderStatus.OK) {
                       searchLocationsNear(map_id,results[0].geometry.location);
                  } else {
                       alert(address + ' not found');
                  }
                });
              }
            <?php } else { ?>
                geocoder.geocode({address: address,componentRestrictions: {country: '<?php echo $restrict_search; ?>'}}, function(results, status) {
                  if (status === google.maps.GeocoderStatus.OK) {
                       searchLocationsNear(map_id,results[0].geometry.location);
                  } else {
                       alert(address + ' not found');
                  }
                });
              }
            <?php } ?>

            function clearLocations() {
                infoWindow.close();
            }




            function searchLocationsNear(mapid,center_searched) {
                clearLocations();
                var distance_type = document.getElementById("wpgmza_distance_type").value;
                var radius = document.getElementById('radiusSelect').value;
                if (distance_type === "1") {
                    if (radius === "1") { zoomie = 14; }
                    else if (radius === "5") { zoomie = 12; }
                    else if (radius === "10") { zoomie = 11; }
                    else if (radius === "25") { zoomie = 9; }
                    else if (radius === "50") { zoomie = 8; }
                    else if (radius === "75") { zoomie = 8; }
                    else if (radius === "100") { zoomie = 7; }
                    else if (radius === "150") { zoomie = 7; }
                    else if (radius === "200") { zoomie = 6; }
                    else if (radius === "300") { zoomie = 6; }
                    else { zoomie = 14; }
                } else {
                    if (radius === "1") { zoomie = 14; }
                    else if (radius === "5") { zoomie = 12; }
                    else if (radius === "10") { zoomie = 11; }
                    else if (radius === "25") { zoomie = 10; }
                    else if (radius === "50") { zoomie = 9; }
                    else if (radius === "75") { zoomie = 8; }
                    else if (radius === "100") { zoomie = 8; }
                    else if (radius === "150") { zoomie = 7; }
                    else if (radius === "200") { zoomie = 7; }
                    else if (radius === "300") { zoomie = 6; }
                    else { zoomie = 14; }
                }
                MYMAP.init("#wpgmza_map", center_searched, zoomie, 3);
                MYMAP.placeMarkers('<?php echo wpgmaps_get_marker_url($wpgmza_current_map_id); ?>?u='+UniqueCode,mapid,radius,center_searched,distance_type);
            }

            function toRad(Value) {
                /** Converts numeric degrees to radians */
                return Value * Math.PI / 180;
            }



        </script>
    <?php
    }
}




function wpgmaps_update_xml_file($mapid = false) {
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if (isset($wpgmza_settings['wpgmza_settings_marker_pull']) && $wpgmza_settings['wpgmza_settings_marker_pull'] == '0') {
        /* using db method, do nothing */
        return;
    }
    
    

    if (!$mapid) {
        $mapid = sanitize_text_field($_POST['map_id']);
    }
    if (!$mapid) {
        $mapid = sanitize_text_field($_GET['map_id']);
    }
    global $wpdb;
    
    
    /* added in 6.0.30 */
    if (!class_exists("DOMDocument")) {
        return new WP_Error( 'db_query_error', __( 'DOMDocument is not enabled' ), "Please contact your host and ask them to enable the DOMDocument class. WP Google Maps uses this class to create the marker data files." );
    }
    
    
    
    $dom = new DOMDocument('1.0');
    $dom->formatOutput = true;
    $channel_main = $dom->createElement('markers');
    $channel = $dom->appendChild($channel_main);
    $table_name = $wpdb->prefix . "wpgmza";

    /* PREVIOUS VERSION HANDLING */
    
    if (function_exists('wpgmza_register_pro_version')) {
        $prov = get_option("WPGMZA_PRO");
        $wpgmza_pro_version = $prov['version'];
        $sql = "SELECT * FROM $table_name WHERE `map_id` = '$mapid' AND `approved` = 1";
        $results = $wpdb->get_results($sql);
    } else {

        $results = $wpdb->get_results(
            "
                SELECT *
                FROM $table_name
                WHERE `map_id` = '$mapid'
                AND `approved` = 1

                "
        );
    }
    


    

    foreach ( $results as $result ) {   
        
        $id = $result->id;
        $address = stripslashes($result->address);
        $description = stripslashes($result->description);
        $pic = $result->pic;
        if (!$pic) { $pic = ""; }
        $icon = $result->icon;
        if (!$icon) { $icon = ""; }
        $link_url = $result->link;
        if ($link_url) {  } else { $link_url = ""; }
        $lat = $result->lat;
        $lng = $result->lng;
        $anim = $result->anim;
        $retina = $result->retina;
        $category = $result->category;
        
        if ($icon == "") {
            if (function_exists('wpgmza_get_category_data')) {
                $category_data = wpgmza_get_category_data($category);
                if (isset($category_data->category_icon) && isset($category_data->category_icon) != "") {
                    $icon = $category_data->category_icon;
                } else {
                   $icon = "";
                }
                if (isset($category_data->retina)) {
                    $retina = $category_data->retina;
                }
            }
        }
        $infoopen = $result->infoopen;
        
        $mtitle = stripslashes($result->title);
        $map_id = $result->map_id;

        $channel = $channel_main->appendChild($dom->createElement('marker'));
        $title = $channel->appendChild($dom->createElement('marker_id'));
        $title->appendChild($dom->CreateTextNode($id));
        $title = $channel->appendChild($dom->createElement('map_id'));
        $title->appendChild($dom->CreateTextNode($map_id));
        $title = $channel->appendChild($dom->createElement('title'));
        $title->appendChild($dom->CreateTextNode($mtitle));
        $title = $channel->appendChild($dom->createElement('address'));
        $title->appendChild($dom->CreateTextNode($address));
        $desc = $channel->appendChild($dom->createElement('desc'));
        $desc->appendChild($dom->CreateTextNode($description));
        $desc = $channel->appendChild($dom->createElement('pic'));
        $desc->appendChild($dom->CreateTextNode($pic));
        $desc = $channel->appendChild($dom->createElement('icon'));
        $desc->appendChild($dom->CreateTextNode($icon));
        $desc = $channel->appendChild($dom->createElement('linkd'));
        $desc->appendChild($dom->CreateTextNode($link_url));
        $bd = $channel->appendChild($dom->createElement('lat'));
        $bd->appendChild($dom->CreateTextNode($lat));
        $bd = $channel->appendChild($dom->createElement('lng'));
        $bd->appendChild($dom->CreateTextNode($lng));
        $bd = $channel->appendChild($dom->createElement('anim'));
        $bd->appendChild($dom->CreateTextNode($anim));
        $bd = $channel->appendChild($dom->createElement('retina'));
        $bd->appendChild($dom->CreateTextNode($retina));
        $bd = $channel->appendChild($dom->createElement('category'));
        $bd->appendChild($dom->CreateTextNode($category));
        $bd = $channel->appendChild($dom->createElement('infoopen'));
        $bd->appendChild($dom->CreateTextNode($infoopen));

        
    }
    $upload_dir = wp_upload_dir();
    
    wpgmaps_handle_directory();

    
    $xml_marker_location = wpgmza_return_marker_path(); 
    
    if (is_multisite()) {
        global $blog_id;
        if ($dom->save($xml_marker_location.$blog_id.'-'.$mapid.'markers.xml') == FALSE) {
          return new WP_Error( 'db_query_error', __( 'Could not save XML file' ), "Could not save marker XML file (".$xml_marker_location.$blog_id."-".$mapid."markers.xml) for Map ID $mapid" );
        }
    } else {
        

        /* PREVIOUS VERSION HANDLING */
        if (function_exists('wpgmza_register_pro_version')) {
            $prov = get_option("WPGMZA_PRO");
            $wpgmza_pro_version = $prov['version'];
            if ($dom->save($xml_marker_location.$mapid.'markers.xml') == FALSE) {
                return new WP_Error( 'db_query_error', __( 'Could not save XML file' ), "Could not save marker XML file (".$xml_marker_location.$mapid."markers.xml) for Map ID $mapid" );
            }
        }
        else {
            if ($dom->save($xml_marker_location.$mapid.'markers.xml') == FALSE) {
                return new WP_Error( 'db_query_error', __( 'Could not save XML file' ), "Could not save marker XML file (".$xml_marker_location.$mapid."markers.xml) for Map ID $mapid" );
            }
        }
    }
    return true;
   
}


function wpgmaps_return_markers($mapid = false) {

    if (!$mapid) {
        return;
    }
    global $wpdb;
    
    $table_name = $wpdb->prefix . "wpgmza";
    $sql = "SELECT * FROM $table_name WHERE `map_id` = '$mapid' AND `approved` = 1";
    $results = $wpdb->get_results($sql);
    $m_array = array();
    $cnt = 0;
    foreach ( $results as $result ) {   
        
        $id = $result->id;
        $address = stripslashes($result->address);
        $description = stripslashes($result->description);
        $pic = $result->pic;
        if (!$pic) { $pic = ""; }
        $icon = $result->icon;
        if (!$icon) { $icon = ""; }
        $link_url = $result->link;
        if ($link_url) {  } else { $link_url = ""; }
        $lat = $result->lat;
        $lng = $result->lng;
        $anim = $result->anim;
        $retina = $result->retina;
        $category = $result->category;
        
        if ($icon == "") {
            if (function_exists('wpgmza_get_category_data')) {
                $category_data = wpgmza_get_category_data($category);
                if (isset($category_data->category_icon) && isset($category_data->category_icon) != "") {
                    $icon = $category_data->category_icon;
                } else {
                   $icon = "";
                }
                if (isset($category_data->retina)) {
                    $retina = $category_data->retina;
                }
            }
        }
        $infoopen = $result->infoopen;
        
        $mtitle = stripslashes($result->title);
        $map_id = $result->map_id;
        
        
        $m_array[$cnt] = array(
            'map_id' => $map_id,
            'marker_id' => $id,
            'title' => $mtitle,
            'address' => $address,
            'desc' => $description,
            'pic' => $pic,
            'icon' => $icon,
            'linkd' => $link_url,
            'lat' => $lat,
            'lng' => $lng,
            'anim' => $anim,
            'retina' => $retina,
            'category' => $category,
            'infoopen' => $infoopen
        );
        $cnt++;
        
    }

    return $m_array;
   
}


function wpgmza_return_marker_url() {
    $url = get_option("wpgmza_xml_url");
    
    
    $content_url = content_url();
    $content_url = trim($content_url, '/');
     
    $plugins_url = plugins_url();
    $plugins_url = trim($plugins_url, '/');
     
    $upload_url = wp_upload_dir();
    $upload_url = $upload_url['baseurl'];
    $upload_url = trim($upload_url, '/');

    $url = str_replace('{wp_content_url}', $content_url, $url);
    $url = str_replace('{plugins_url}', $plugins_url, $url);
    $url = str_replace('{uploads_url}', $upload_url, $url);
    
    /* just incase people use the "dir" instead of "url" */
    $url = str_replace('{wp_content_dir}', $content_url, $url);
    $url = str_replace('{plugins_dir}', $plugins_url, $url);
    $url = str_replace('{uploads_dir}', $upload_url, $url);

    if (empty($url)) {
        $url = $upload_url."/wp-google-maps/";
    }
    
    if (substr($url, -1) != "/") { $url = $url."/"; }


    return $url;
    
    
    
    
    
}


function wpgmza_return_marker_path() { 
        $file = get_option("wpgmza_xml_location");
        $content_dir = WP_CONTENT_DIR;
        $content_dir = trim($content_dir, '/');
        if (defined('WP_PLUGIN_DIR')) {
            $plugin_dir = str_replace(wpgmza_get_document_root(), '', WP_PLUGIN_DIR);
            $plugin_dir = trim($plugin_dir, '/');
        } else {
            $plugin_dir = str_replace(wpgmza_get_document_root(), '', WP_CONTENT_DIR . '/plugins');
            $plugin_dir = trim($plugin_dir, '/');
        }
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'];
        $upload_dir = rtrim($upload_dir, '/');
        
        $file = str_replace('{wp_content_dir}', $content_dir, $file);
        $file = str_replace('{plugins_dir}', $plugin_dir, $file);
        $file = str_replace('{uploads_dir}', $upload_dir, $file);
        $file = trim($file);
        
        if (empty($file)) {
            $file = $upload_dir."/wp-google-maps/";
        }
        

        
        
        
        
        /* 6.0.32 - checked for beginning slash, but not on local host */
        if (
                (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == "127.0.0.1") || 
                (isset($_SERVER['LOCAL_ADDR']) && $_SERVER['LOCAL_ADDR'] == "127.0.0.1") || 
                substr($file, 0, 2) == "C:" ||
                substr($file, 0, 2) == "D:" ||
                substr($file, 0, 2) == "E:" ||
                substr($file, 0, 2) == "F:" ||
                substr($file, 0, 2) == "G:"
                
            ) { } else {
            if (substr($file, 0, 1) != "/") { $file = "/".$file; }
        }
        
        /* 6.0.32 - check if its just returning 'wp-content/...' */
        if (substr($file, 0, 10) == "wp-content") { 
            $file = wpgmza_get_site_root()."/".$file;
        }
        
        if (substr($file, -1) != "/") { $file = $file."/"; }
        
        return $file;

    
}

function wpgmza_get_document_root() {
    $document_root = null;

    if ($document_root === null) {
        if (!empty($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == $_SERVER['PHP_SELF']) {
            $document_root = wpgmza_get_site_root();
        } elseif (!empty($_SERVER['SCRIPT_FILENAME'])) {
            $document_root = substr(wpgmza_path($_SERVER['SCRIPT_FILENAME']), 0, -strlen(wpgmza_path($_SERVER['PHP_SELF'])));
        } elseif (!empty($_SERVER['PATH_TRANSLATED'])) {
            $document_root = substr(wpgmza_path($_SERVER['PATH_TRANSLATED']), 0, -strlen(wpgmza_path($_SERVER['PHP_SELF'])));
        } elseif (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $document_root = wpgmza_path($_SERVER['DOCUMENT_ROOT']);
        } else {
            $document_root = wpgmza_get_site_root();
        }

        $document_root = realpath($document_root);
        $document_root = wpgmza_path($document_root);
    }

    return $document_root;
}
function wpgmza_get_site_root() {
    $site_root = ABSPATH;
    $site_root = realpath($site_root);
    $site_root = wpgmza_path($site_root);

    return $site_root;
}
function wpgmza_path($path) {
    $path = preg_replace('~[/\\\]+~', '/', $path);
    $path = rtrim($path, '/');

    return $path;
}
function wpgmza_get_site_path() {
    $site_url = wpgmza_get_site_url();
    $parse_url = @parse_url($site_url);

    if ($parse_url && isset($parse_url['path'])) {
        $site_path = '/' . ltrim($parse_url['path'], '/');
    } else {
        $site_path = '/';
    }

    if (substr($site_path, -1) != '/') {
        $site_path .= '/';
    }

    return $site_path;
}
function wpgmza_get_site_url() {
    static $site_url = null;

    if ($site_url === null) {
        $site_url = get_option('siteurl');
        $site_url = rtrim($site_url, '/');
    }

    return $site_url;
}

function wpgmaps_update_all_xml_file() {
    global $wpdb;
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if (isset($wpgmza_settings['wpgmza_settings_marker_pull']) && $wpgmza_settings['wpgmza_settings_marker_pull'] == '0') {
        /* using db method, do nothing */
        return;
    }
    
    $table_name = $wpdb->prefix . "wpgmza_maps";
    $results = $wpdb->get_results("SELECT `id` FROM $table_name WHERE `active` = 0");

    foreach ( $results as $result ) {
        $map_id = $result->id;
        $wpgmza_check = wpgmaps_update_xml_file($map_id);
        if ( is_wp_error($wpgmza_check) ) wpgmza_return_error($wpgmza_check);
    }
}



function wpgmaps_action_callback_basic() {
    global $wpdb;
    global $wpgmza_tblname;
    global $wpgmza_p;
    global $wpgmza_tblname_poly;
    global $wpgmza_tblname_polylines;
    $check = check_ajax_referer( 'wpgmza', 'security' );
    $table_name = $wpdb->prefix . "wpgmza";

    if ($check == 1) {

        if ($_POST['action'] == "add_marker") {
            $rows_affected = $wpdb->insert( $table_name, array( 'map_id' => sanitize_text_field($_POST['map_id']), 'address' => sanitize_text_field($_POST['address']), 'lat' => sanitize_text_field($_POST['lat']), 'lng' => sanitize_text_field($_POST['lng']), 'infoopen' => $_POST['infoopen'], 'description' => '', 'title' => '', 'anim' => sanitize_text_field($_POST['anim']), 'link' => '', 'icon' => '', 'pic' => '', 'infoopen' => sanitize_text_field($_POST['infoopen']), 'retina' => '0' ) );
            $wpgmza_check = wpgmaps_update_xml_file(sanitize_text_field($_POST['map_id']));
            if ( is_wp_error($wpgmza_check) ) wpgmza_return_error($wpgmza_check);
            $return_a = array(
                "marker_id" => $wpdb->insert_id,
                "marker_data" => wpgmaps_return_markers(sanitize_text_field($_POST['map_id'])),
                "table_html" => wpgmza_return_marker_list(sanitize_text_field($_POST['map_id']))
            );
            echo json_encode($return_a);
        }
        if ($_POST['action'] == "edit_marker") {
            $cur_id = sanitize_text_field($_POST['edit_id']);
            $rows_affected = $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET address = %s, lat = %f, lng = %f, anim = %d, infoopen = %d WHERE id = %d", sanitize_text_field($_POST['address']), sanitize_text_field($_POST['lat']), sanitize_text_field($_POST['lng']), sanitize_text_field($_POST['anim']), sanitize_text_field($_POST['infoopen']), $cur_id) );
            $wpgmza_check = wpgmaps_update_xml_file(sanitize_text_field($_POST['map_id']));
            if ( is_wp_error($wpgmza_check) ) wpgmza_return_error($wpgmza_check);
            $return_a = array(
                "marker_id" => $cur_id,
                "marker_data" => wpgmaps_return_markers(sanitize_text_field($_POST['map_id'])),
                "table_html" => wpgmza_return_marker_list(sanitize_text_field($_POST['map_id']))
            );
            echo json_encode($return_a);

        }
        if ($_POST['action'] == "delete_marker") {
            $marker_id = sanitize_text_field($_POST['marker_id']);
            $wpdb->query(
                "
                        DELETE FROM $wpgmza_tblname
                        WHERE `id` = '$marker_id'
                        LIMIT 1
                        "
            );
            $wpgmza_check = wpgmaps_update_xml_file(sanitize_text_field($_POST['map_id']));
            if ( is_wp_error($wpgmza_check) ) wpgmza_return_error($wpgmza_check);
            $return_a = array(
                "marker_id" => $marker_id,
                "marker_data" => wpgmaps_return_markers(sanitize_text_field($_POST['map_id'])),
                "table_html" => wpgmza_return_marker_list(sanitize_text_field($_POST['map_id']))
            );
            echo json_encode($return_a);


        }
        if ($_POST['action'] == "delete_poly") {
            $poly_id = sanitize_text_field($_POST['poly_id']);

            $wpdb->query(
                    "
                    DELETE FROM $wpgmza_tblname_poly
                    WHERE `id` = '$poly_id'
                    LIMIT 1
                    "
            );

            echo wpgmza_b_return_polygon_list(sanitize_text_field($_POST['map_id']));

        }
        if ($_POST['action'] == "delete_polyline") {
            $poly_id = sanitize_text_field($_POST['poly_id']);

            $wpdb->query(
                    "
                    DELETE FROM $wpgmza_tblname_polylines
                    WHERE `id` = '$poly_id'
                    LIMIT 1
                    "
            );

            echo wpgmza_b_return_polyline_list(sanitize_text_field($_POST['map_id']));

        }
        

    }

    die(); 

}

function wpgmaps_load_maps_api() {
    wp_enqueue_script('google-maps' , 'http://maps.google.com/maps/api/js?sensor=true' , false , '3');
}

function wpgmaps_tag_basic( $atts ) {
    global $wpgmza_current_map_id;
    extract( shortcode_atts( array(
        'id' => '1'
    ), $atts ) );
    $ret_msg = "";
    global $short_code_active;
    $wpgmza_current_map_id = $atts['id'];

    $res = wpgmza_get_map_data($atts['id']);
    if (!isset($res)) { echo __("Error: The map ID","wp-google-maps")." (".$wpgmza_current_map_id.") ".__("does not exist","wp-google-maps"); return; }
    $short_code_active = true;

    if (!function_exists('wpgmaps_admin_styles_pro')) {
        global $wpgmza_version;
        wp_register_style( 'wpgmaps-style', plugins_url('css/wpgmza_style.css', __FILE__),array(),$wpgmza_version);
        wp_enqueue_style( 'wpgmaps-style' );
    }
    

    $map_align = $res->alignment;
    
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if (isset($wpgmza_settings['wpgmza_settings_marker_pull']) && $wpgmza_settings['wpgmza_settings_marker_pull'] == '0') {
    } else {
        /* only check if marker file exists if they are using the XML method */
        wpgmza_check_if_marker_file_exists($wpgmza_current_map_id);
    }
    
    
    

    $map_width_type = stripslashes($res->map_width_type);
    $map_height_type = stripslashes($res->map_height_type);
    if (!isset($map_width_type)) { $map_width_type == "px"; }
    if (!isset($map_height_type)) { $map_height_type == "px"; }
    if ($map_width_type == "%" && intval($res->map_width) > 100) { $res->map_width = 100; }
    if ($map_height_type == "%" && intval($res->map_height) > 100) { $res->map_height = 100; }

    if (!$map_align || $map_align == "" || $map_align == "1") { $map_align = "float:left;"; }
    else if ($map_align == "2") { $map_align = "margin-left:auto !important; margin-right:auto; !important; align:center;"; }
    else if ($map_align == "3") { $map_align = "float:right;"; }
    else if ($map_align == "4") { $map_align = ""; }
    $map_style = "style=\"display:block; overflow:auto; width:".$res->map_width."".$map_width_type."; height:".$res->map_height."".$map_height_type."; $map_align\"";

    $map_other_settings = maybe_unserialize($res->other_settings);
    $sl_data = "";
    if (isset($map_other_settings['store_locator_enabled']) && $map_other_settings['store_locator_enabled'] == 1) {
        $sl_data = wpgmaps_sl_user_output_basic($wpgmza_current_map_id);
    } else { $sl_data = ""; }
    
    
    $ret_msg .= "
            <style>
            #wpgmza_map img { max-width:none !important; }
            .wpgmza_widget { overflow: auto; }
            </style>
            
            $sl_data    
            <div id=\"wpgmza_map\" $map_style>
            </div>
        ";
    
    
    if (isset($wpgmza_main_settings['wpgmza_custom_css']) && $wpgmza_main_settings['wpgmza_custom_css'] != "") { 
        $ret_msg = "
            <!-- WP Google Maps Custom CSS -->
            <style type=\"text/css\">".
                
                $wpgmza_main_settings['wpgmza_custom_css']
                
            ."</style>
            ".$ret_msg;
    }
    
    return $ret_msg;
}
function wpgmza_check_if_marker_file_exists($mapid) {
    wpgmaps_handle_directory();
    $upload_dir = wp_upload_dir(); 
    
    $xml_marker_location = get_option("wpgmza_xml_location");
    if (is_multisite()) {
        global $blog_id;
        if (file_exists($xml_marker_location.$blog_id.'-'.$mapid.'markers.xml')) {
            /* all OK */  
        } else {
            $wpgmza_check = wpgmaps_update_xml_file($mapid);
            if ( is_wp_error($wpgmza_check) ) wpgmza_return_error($wpgmza_check);
        }
    }
    else {
            if (file_exists($xml_marker_location.$mapid.'markers.xml')) {
            } else {
                $wpgmza_check = wpgmaps_update_xml_file($mapid);
                if ( is_wp_error($wpgmza_check) ) wpgmza_return_error($wpgmza_check);
            }
    }
}
function wpgmaps_sl_user_output_basic($map_id) {
    $map_settings = wpgmza_get_map_data($map_id);
    
    $map_width = $map_settings->map_width;
    $map_width_type = stripslashes($map_settings->map_width_type);
    $map_other_settings = maybe_unserialize($map_settings->other_settings);
    
    if (isset($map_other_settings['store_locator_query_string'])) { $sl_query_string = stripslashes($map_other_settings['store_locator_query_string']); } else { $sl_query_string = __("ZIP / Address:","wp-google-maps"); }
    
    if ($map_width_type == "px" && $map_width < 300) { $map_width = "300"; }
    
    $ret_msg = "";
    
    $ret_msg .= "<div class=\"wpgmza_sl_main_div\">";
    $ret_msg .= "       <div class=\"wpgmza_sl_query_div\">";
    $ret_msg .= "           <div class=\"wpgmza_sl_query_innerdiv1\"><label for='addressInput'>".$sl_query_string."</label></div>";
    $ret_msg .= "           <div class=\"wpgmza_sl_query_innerdiv2\"><input type=\"text\" id=\"addressInput\" size=\"20\"/></div>";
    $ret_msg .= "       </div>";

    $ret_msg .= "       <div class=\"wpgmza_sl_radius_div\">";
    $ret_msg .= "           <div class=\"wpgmza_sl_radius_innerdiv1\"><label for='radiusSelect'>".__("Radius","wp-google-maps").":</label></div>";
    $ret_msg .= "           <div class=\"wpgmza_sl_radius_innerdiv2\">";
    $ret_msg .= "           <select class=\"wpgmza_sl_radius_select\" id=\"radiusSelect\">";
    $ret_msg .= "               ";

    if ($map_other_settings['store_locator_distance'] == 1) {
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"1\">".__("1mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"5\">".__("5mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"10\" selected>".__("10mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"25\">".__("25mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"50\">".__("50mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"75\">".__("75mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"100\">".__("100mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"150\">".__("150mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"200\">".__("200mi","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"300\">".__("300mi","wp-google-maps")."</option>";
    } else {
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"1\">".__("1km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"5\">".__("5km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"10\" selected>".__("10km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"25\">".__("25km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"50\">".__("50km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"75\">".__("75km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"100\">".__("100km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"150\">".__("150km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"200\">".__("200km","wp-google-maps")."</option>";
        $ret_msg .= "                   <option class=\"wpgmza_sl_select_option\" value=\"300\">".__("300km","wp-google-maps")."</option>";
    }
    
    $ret_msg .= "               </select><input type='hidden' value='".$map_other_settings['store_locator_distance']."' name='wpgmza_distance_type' id='wpgmza_distance_type'  style='display:none;' />";
    $ret_msg .= "           </div>";
    $ret_msg .= "       </div>";
    
    if (function_exists("wpgmza_register_pro_version") && isset($map_other_settings['store_locator_category']) && $map_other_settings['store_locator_category'] == "1") {
        $ret_msg .= "       <div class=\"wpgmza_sl_category_div\">";
        $ret_msg .= "           <div class=\"wpgmza_sl_category_innerdiv1\">".__("Category","wp-google-maps").":</div>";
        $ret_msg .= "           <div class=\"wpgmza_sl_category_innerdiv2\">";
        $ret_msg .= "              ".wpgmza_pro_return_category_checkbox_list($map_id)."";
        $ret_msg .= "           </div>";
        $ret_msg .= "       </div>";
    }

    $ret_msg .= "       <input class=\"wpgmza_sl_search_button\" type=\"button\" onclick=\"searchLocations($map_id)\" value=\"".__("Search","wp-google-maps")."\"/>";
    $ret_msg .= "    </div>";
    $ret_msg .= "    <div><select id=\"locationSelect\" style=\"width:100%;visibility:hidden\"></select></div>";
    
    return $ret_msg;
    
}

function wpgmaps_get_plugin_url() {
    if ( !function_exists('plugins_url') )
        return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));
    return plugins_url(plugin_basename(dirname(__FILE__)));
}

function wpgmaps_head() {
    
    global $wpgmza_tblname_maps;
    global $wpgmza_version;

    /* deprecated in version 6.0.30 as GoDaddy have added a "flush cache" feature */
    /*
    $checker = get_dropins();
    if (isset($checker['object-cache.php'])) {
	echo "<div id=\"message\" class=\"error\"><p>".__("Please note: <strong>WP Google Maps will not function correctly while using APC Object Cache.</strong> We have found that GoDaddy hosting packages automatically include this with their WordPress hosting packages. Please email GoDaddy and ask them to remove the object-cache.php from your wp-content/ directory.","wp-google-maps")."</p></div>";
    }
     * 
     */

    if ((isset($_GET['page']) && $_GET['page'] == "wp-google-maps-menu") || (isset($_GET['page']) && $_GET['page'] == "wp-google-maps-menu-settings")) {
        wpgmaps_folder_check();
    }
    
    
    if (isset($_POST['wpgmza_savemap'])){
        global $wpdb;

        

        $map_id = intval(sanitize_text_field($_POST['wpgmza_id']));
        $map_title = sanitize_text_field(esc_attr($_POST['wpgmza_title']));
        $map_height = sanitize_text_field($_POST['wpgmza_height']);
        $map_width = sanitize_text_field($_POST['wpgmza_width']);
        $map_width_type = sanitize_text_field($_POST['wpgmza_map_width_type']);
        if ($map_width_type == "%") { $map_width_type = "\%"; }
        $map_height_type = sanitize_text_field($_POST['wpgmza_map_height_type']);
        if ($map_height_type == "%") { $map_height_type = "\%"; }
        $map_start_location = sanitize_text_field($_POST['wpgmza_start_location']);
        $map_start_zoom = intval(sanitize_text_field($_POST['wpgmza_start_zoom']));
        $type = intval(sanitize_text_field($_POST['wpgmza_map_type']));
        $alignment = intval(sanitize_text_field($_POST['wpgmza_map_align']));
        $bicycle_enabled = intval(sanitize_text_field($_POST['wpgmza_bicycle']));
        $traffic_enabled = intval(sanitize_text_field($_POST['wpgmza_traffic']));

        $map_max_zoom = intval(sanitize_text_field($_POST['wpgmza_max_zoom']));
        

        $gps = explode(",",$map_start_location);
        $map_start_lat = $gps[0];
        $map_start_lng = $gps[1];
        
        $other_settings = array();
        $other_settings['store_locator_enabled'] = intval(sanitize_text_field($_POST['wpgmza_store_locator']));
        $other_settings['store_locator_distance'] = intval(sanitize_text_field($_POST['wpgmza_store_locator_distance']));
        $other_settings['store_locator_bounce'] = intval(sanitize_text_field($_POST['wpgmza_store_locator_bounce']));
        $other_settings['store_locator_query_string'] = sanitize_text_field($_POST['wpgmza_store_locator_query_string']);
        if (isset($_POST['wpgmza_store_locator_restrict'])) { $other_settings['wpgmza_store_locator_restrict'] = sanitize_text_field($_POST['wpgmza_store_locator_restrict']); }
        


        $other_settings['map_max_zoom'] = sanitize_text_field($map_max_zoom);
        /* deprecated in 6.2.0

        $other_settings['weather_layer'] = intval($_POST['wpgmza_weather']);
        $other_settings['weather_layer_temp_type'] = intval($_POST['wpgmza_weather_temp_type']);
        $other_settings['cloud_layer'] = intval($_POST['wpgmza_cloud']);
        */
        $other_settings['transport_layer'] = intval(sanitize_text_field($_POST['wpgmza_transport']));
        



        if (isset($_POST['wpgmza_theme'])) { 
            $theme = intval(sanitize_text_field($_POST['wpgmza_theme']));
            $theme_data = sanitize_text_field($_POST['wpgmza_theme_data_'.$theme]);
            $other_settings['wpgmza_theme_data'] = $theme_data;
            $other_settings['wpgmza_theme_selection'] = $theme;
        }

        /* overwrite theme data if a custom theme is selected */
        if (isset($_POST['wpgmza_styling_json'])) { $other_settings['wpgmza_theme_data'] = sanitize_text_field($_POST['wpgmza_styling_json']); }


        $other_settings_data = maybe_serialize($other_settings);

        $data['map_default_starting_lat'] = $map_start_lat;
        $data['map_default_starting_lng'] = $map_start_lng;
        $data['map_default_height'] = $map_height;
        $data['map_default_width'] = $map_width;
        $data['map_default_zoom'] = $map_start_zoom;
        $data['map_default_max_zoom'] = $map_max_zoom;
        $data['map_default_type'] = $type;
        $data['map_default_alignment'] = $alignment;
        $data['map_default_width_type'] = $map_width_type;
        $data['map_default_height_type'] = $map_height_type;



        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_maps SET
                map_title = %s,
                map_width = %s,
                map_height = %s,
                map_start_lat = %f,
                map_start_lng = %f,
                map_start_location = %s,
                map_start_zoom = %d,
                type = %d,
                bicycle = %d,
                traffic = %d,
                alignment = %d,
                map_width_type = %s,
                map_height_type = %s,
                other_settings = %s
                WHERE id = %d",

                $map_title,
                $map_width,
                $map_height,
                $map_start_lat,
                $map_start_lng,
                $map_start_location,
                $map_start_zoom,
                $type,
                $bicycle_enabled,
                $traffic_enabled,
                $alignment,
                $map_width_type,
                $map_height_type,
                $other_settings_data,
                $map_id)
        );
        update_option('WPGMZA_SETTINGS', $data);
        echo "<div class='updated'>";
        _e("Your settings have been saved.","wp-google-maps");
        echo "</div>";

    }

    else if (isset($_POST['wpgmza_save_maker_location'])){
        global $wpdb;
        global $wpgmza_tblname;
        $mid = sanitize_text_field($_POST['wpgmaps_marker_id']);
        $wpgmaps_marker_lat = sanitize_text_field($_POST['wpgmaps_marker_lat']);
        $wpgmaps_marker_lng = sanitize_text_field($_POST['wpgmaps_marker_lng']);

        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname SET
                lat = %s,
                lng = %s
                WHERE id = %d",

                $wpgmaps_marker_lat,
                $wpgmaps_marker_lng,
                $mid)
        );

        echo "<div class='updated'>";
        _e("Your marker location has been saved.","wp-google-maps");
        echo "</div>";


    }
    else if (isset($_POST['wpgmza_save_poly'])){
        global $wpdb;
        global $wpgmza_tblname_poly;
        $mid = sanitize_text_field($_POST['wpgmaps_map_id']);
        if (!isset($_POST['wpgmza_polygon']) || $_POST['wpgmza_polygon'] == "") {
            echo "<div class='error'>";
            _e("You cannot save a blank polygon","wp-google-maps");
            echo "</div>";
            
        } else {
            $wpgmaps_polydata = sanitize_text_field($_POST['wpgmza_polygon']);
        
            if (isset($_POST['poly_name'])) { $polyname = sanitize_text_field($_POST['poly_name']); } else { $polyname = "Polyline"; }
            if (isset($_POST['poly_line'])) { $linecolor = sanitize_text_field($_POST['poly_line']); } else { $linecolor = "000000"; }
            if (isset($_POST['poly_fill'])) { $fillcolor = sanitize_text_field($_POST['poly_fill']); } else { $fillcolor = "66FF00"; }
            if (isset($_POST['poly_opacity'])) { $opacity = sanitize_text_field($_POST['poly_opacity']); } else { $opacity = "0.5"; }
            if (isset($_POST['poly_line_opacity'])) { $line_opacity = sanitize_text_field($_POST['poly_line_opacity']); } else { $line_opacity = "0.5"; }
            if (isset($_POST['poly_line_hover_line_color'])) { $ohlinecolor = sanitize_text_field($_POST['poly_line_hover_line_color']); } else { $ohlinecolor = ""; }
            if (isset($_POST['poly_hover_fill_color'])) { $ohfillcolor = sanitize_text_field($_POST['poly_hover_fill_color']); } else { $ohfillcolor = ""; }
            if (isset($_POST['poly_hover_opacity'])) { $ohopacity = sanitize_text_field($_POST['poly_hover_opacity']); } else { $ohopacity = ""; }

            $rows_affected = $wpdb->query( $wpdb->prepare(
                    "INSERT INTO $wpgmza_tblname_poly SET
                    map_id = %d,
                    polydata = %s,
                    polyname = %s,
                    linecolor = %s,
                    lineopacity = %s,
                    fillcolor = %s,
                    opacity = %s,
                    ohlinecolor = %s,
                    ohfillcolor = %s,
                    ohopacity = %s
                    ",

                    $mid,
                    $wpgmaps_polydata,
                    $polyname,
                    $linecolor,
                    $line_opacity,
                    $fillcolor,
                    $opacity,
                    $ohlinecolor,
                    $ohfillcolor,
                    $ohopacity
                )
            );
            echo "<div class='updated'>";
            _e("Your polygon has been created.","wp-google-maps");
            echo "</div>";
        }


    }
    else if (isset($_POST['wpgmza_edit_poly'])){
        global $wpdb;
        global $wpgmza_tblname_poly;
        $mid = sanitize_text_field($_POST['wpgmaps_map_id']);
        $pid = sanitize_text_field($_POST['wpgmaps_poly_id']);
        if (!isset($_POST['wpgmza_polygon']) || $_POST['wpgmza_polygon'] == "") {
            echo "<div class='error'>";
            _e("You cannot save a blank polygon","wp-google-maps");
            echo "</div>";
    
        } else {
            $wpgmaps_polydata = sanitize_text_field($_POST['wpgmza_polygon']);
        
            if (isset($_POST['poly_name'])) { $polyname = sanitize_text_field($_POST['poly_name']); } else { $polyname = "Polyline"; }
            if (isset($_POST['poly_line'])) { $linecolor = sanitize_text_field($_POST['poly_line']); } else { $linecolor = "000000"; }
            if (isset($_POST['poly_fill'])) { $fillcolor = sanitize_text_field($_POST['poly_fill']); } else { $fillcolor = "66FF00"; }
            if (isset($_POST['poly_opacity'])) { $opacity = sanitize_text_field($_POST['poly_opacity']); } else { $opacity = "0.5"; }
            if (isset($_POST['poly_line_opacity'])) { $line_opacity = sanitize_text_field($_POST['poly_line_opacity']); } else { $line_opacity = "0.5"; }
            if (isset($_POST['poly_line_hover_line_color'])) { $ohlinecolor = sanitize_text_field($_POST['poly_line_hover_line_color']); } else { $ohlinecolor = ""; }
            if (isset($_POST['poly_hover_fill_color'])) { $ohfillcolor = sanitize_text_field($_POST['poly_hover_fill_color']); } else { $ohfillcolor = ""; }
            if (isset($_POST['poly_hover_opacity'])) { $ohopacity = sanitize_text_field($_POST['poly_hover_opacity']); } else { $ohopacity = ""; }


            $rows_affected = $wpdb->query( $wpdb->prepare(
                    "UPDATE $wpgmza_tblname_poly SET
                    polydata = %s,
                    polyname = %s,
                    linecolor = %s,
                    lineopacity = %s,
                    fillcolor = %s,
                    opacity = %s,
                    ohlinecolor = %s,
                    ohfillcolor = %s,
                    ohopacity = %s
                    WHERE `id` = %d"
                    ,

                    $wpgmaps_polydata,
                    $polyname,
                    $linecolor,
                    $line_opacity,
                    $fillcolor,
                    $opacity,
                    $ohlinecolor,
                    $ohfillcolor,
                    $ohopacity,
                    $pid
                )
            );
            echo "<div class='updated'>";
            _e("Your polygon has been saved.","wp-google-maps");
            echo "</div>";
        }


    }
    else if (isset($_POST['wpgmza_save_polyline'])){
        global $wpdb;
        global $wpgmza_tblname_polylines;
        $mid = sanitize_text_field($_POST['wpgmaps_map_id']);
        if (!isset($_POST['wpgmza_polyline']) || $_POST['wpgmza_polyline'] == "") {
            echo "<div class='error'>";
            _e("You cannot save a blank polyline","wp-google-maps");
            echo "</div>";
    
        } else {
            $wpgmaps_polydata = sanitize_text_field($_POST['wpgmza_polyline']);
        
        
            if (isset($_POST['poly_name'])) { $polyname = sanitize_text_field($_POST['poly_name']); } else { $polyname = ""; }
            if (isset($_POST['poly_line'])) { $linecolor = sanitize_text_field($_POST['poly_line']); } else { $linecolor = "000000"; }
            if (isset($_POST['poly_thickness'])) { $linethickness = sanitize_text_field($_POST['poly_thickness']); } else { $linethickness = "0"; }
            if (isset($_POST['poly_opacity'])) { $opacity = sanitize_text_field($_POST['poly_opacity']); } else { $opacity = "1"; }

            $rows_affected = $wpdb->query( $wpdb->prepare(
                    "INSERT INTO $wpgmza_tblname_polylines SET
                    map_id = %d,
                    polydata = %s,
                    polyname = %s,
                    linecolor = %s,
                    linethickness = %s,
                    opacity = %s
                    ",

                    $mid,
                    $wpgmaps_polydata,
                    $polyname,
                    $linecolor,
                    $linethickness,
                    $opacity
                )
            );
            echo "<div class='updated'>";
            _e("Your polyline has been created.","wp-google-maps");
            echo "</div>";
        }


    }
    else if (isset($_POST['wpgmza_edit_polyline'])){
        global $wpdb;
        global $wpgmza_tblname_polylines;
        $mid = sanitize_text_field($_POST['wpgmaps_map_id']);
        $pid = sanitize_text_field($_POST['wpgmaps_poly_id']);
        if (!isset($_POST['wpgmza_polyline']) || $_POST['wpgmza_polyline'] == "") {
            echo "<div class='error'>";
            _e("You cannot save a blank polyline","wp-google-maps");
            echo "</div>";
    
        } else {
            $wpgmaps_polydata = sanitize_text_field($_POST['wpgmza_polyline']);
            if (isset($_POST['poly_name'])) { $polyname = sanitize_text_field($_POST['poly_name']); } else { $polyname = ""; }
            if (isset($_POST['poly_line'])) { $linecolor = sanitize_text_field($_POST['poly_line']); } else { $linecolor = "000000"; }
            if (isset($_POST['poly_thickness'])) { $linethickness = sanitize_text_field($_POST['poly_thickness']); } else { $linethickness = "0"; }
            if (isset($_POST['poly_opacity'])) { $opacity = sanitize_text_field($_POST['poly_opacity']); } else { $opacity = "1"; }

            $rows_affected = $wpdb->query( $wpdb->prepare(
                    "UPDATE $wpgmza_tblname_polylines SET
                    polydata = %s,
                    polyname = %s,
                    linecolor = %s,
                    linethickness = %s,
                    opacity = %s
                    WHERE `id` = %d"
                    ,

                    $wpgmaps_polydata,
                    $polyname,
                    $linecolor,
                    $linethickness,
                    $opacity,
                    $pid
                )
            );
            echo "<div class='updated'>";
            _e("Your polyline has been saved.","wp-google-maps");
            echo "</div>";
        }


    }    
    else if (isset($_POST['wpgmza_save_settings'])){
        global $wpdb;
        $wpgmza_data = array();
        if (isset($_POST['wpgmza_settings_map_streetview'])) { $wpgmza_data['wpgmza_settings_map_streetview'] = sanitize_text_field($_POST['wpgmza_settings_map_streetview']); }
        if (isset($_POST['wpgmza_settings_map_zoom'])) { $wpgmza_data['wpgmza_settings_map_zoom'] = sanitize_text_field($_POST['wpgmza_settings_map_zoom']); }
        if (isset($_POST['wpgmza_settings_map_pan'])) { $wpgmza_data['wpgmza_settings_map_pan'] = sanitize_text_field($_POST['wpgmza_settings_map_pan']); }
        if (isset($_POST['wpgmza_settings_map_type'])) { $wpgmza_data['wpgmza_settings_map_type'] = sanitize_text_field($_POST['wpgmza_settings_map_type']); }
        if (isset($_POST['wpgmza_settings_force_jquery'])) { $wpgmza_data['wpgmza_settings_force_jquery'] = sanitize_text_field($_POST['wpgmza_settings_force_jquery']); }
        if (isset($_POST['wpgmza_settings_map_scroll'])) { $wpgmza_data['wpgmza_settings_map_scroll'] = sanitize_text_field($_POST['wpgmza_settings_map_scroll']); }
        if (isset($_POST['wpgmza_settings_map_draggable'])) { $wpgmza_data['wpgmza_settings_map_draggable'] = sanitize_text_field($_POST['wpgmza_settings_map_draggable']); }
        if (isset($_POST['wpgmza_settings_map_clickzoom'])) { $wpgmza_data['wpgmza_settings_map_clickzoom'] = sanitize_text_field($_POST['wpgmza_settings_map_clickzoom']); }
        if (isset($_POST['wpgmza_settings_map_open_marker_by'])) { $wpgmza_data['wpgmza_settings_map_open_marker_by'] = sanitize_text_field($_POST['wpgmza_settings_map_open_marker_by']); }
        if (isset($_POST['wpgmza_api_version'])) { $wpgmza_data['wpgmza_api_version'] = sanitize_text_field($_POST['wpgmza_api_version']); }
        if (isset($_POST['wpgmza_custom_css'])) { $wpgmza_data['wpgmza_custom_css'] = sanitize_text_field($_POST['wpgmza_custom_css']); }
        if (isset($_POST['wpgmza_marker_xml_location'])) { update_option("wpgmza_xml_location",sanitize_text_field($_POST['wpgmza_marker_xml_location'])); }
        if (isset($_POST['wpgmza_marker_xml_url'])) { update_option("wpgmza_xml_url",sanitize_text_field($_POST['wpgmza_marker_xml_url'])); }
        if (isset($_POST['wpgmza_access_level'])) { $wpgmza_data['wpgmza_settings_access_level'] = sanitize_text_field($_POST['wpgmza_access_level']); }
        if (isset($_POST['wpgmza_settings_marker_pull'])) { $wpgmza_data['wpgmza_settings_marker_pull'] = sanitize_text_field($_POST['wpgmza_settings_marker_pull']); }
        update_option('WPGMZA_OTHER_SETTINGS', $wpgmza_data);
        echo "<div class='updated'>";
        _e("Your settings have been saved.","wp-google-maps");
        echo "</div>";


    }
    

    



}
function wpgmaps_feedback_head() {
        
    

    
    if (isset($_POST['wpgmza_save_feedback'])) {
        
        global $wpgmza_pro_version;
        global $wpgmza_global_array;
        if (function_exists('curl_version')) {
            
            $request_url = "http://www.wpgmaps.com/apif/rec.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            
            curl_close($ch);
            $wpgmza_global_array['message'] = __('Thank you for your feedback!','wp-google-maps');
            $wpgmza_global_array['code'] = '100';
        } else {
            
            $wpgmza_global_array['message'] = __('Thank you for your feedback!','wp-google-maps');
            $wpgmza_global_array['code'] = '100';
        }
        
    }
    
    
}
function wpgmaps_head_old() {
    global $wpgmza_tblname_maps;
    if (isset($_POST['wpgmza_savemap'])){
        global $wpdb;


        $map_id = sanitize_text_field($_POST['wpgmza_id']);
        $map_title = sanitize_text_field($_POST['wpgmza_title']);
        $map_height = sanitize_text_field($_POST['wpgmza_height']);
        $map_width = sanitize_text_field($_POST['wpgmza_width']);


        $map_width_type = sanitize_text_field($_POST['wpgmza_map_width_type']);
        if ($map_width_type == "%") { $map_width_type = "\%"; }
        $map_height_type = sanitize_text_field($_POST['wpgmza_map_height_type']);
        if ($map_height_type == "%") { $map_height_type = "\%"; }
        $map_start_location = sanitize_text_field($_POST['wpgmza_start_location']);
        $map_start_zoom = intval($_POST['wpgmza_start_zoom']);
        $type = intval($_POST['wpgmza_map_type']);
        $alignment = intval($_POST['wpgmza_map_align']);
        $order_markers_by = intval($_POST['wpgmza_order_markers_by']);
        $order_markers_choice = intval($_POST['wpgmza_order_markers_choice']);
        $show_user_location = intval($_POST['wpgmza_show_user_location']);
        $directions_enabled = intval($_POST['wpgmza_directions']);
        $bicycle_enabled = intval($_POST['wpgmza_bicycle']);
        $traffic_enabled = intval($_POST['wpgmza_traffic']);
        $dbox = intval($_POST['wpgmza_dbox']);
        $dbox_width = sanitize_text_field($_POST['wpgmza_dbox_width']);
        $default_to = sanitize_text_field($_POST['wpgmza_default_to']);
        $listmarkers = intval($_POST['wpgmza_listmarkers']);
        $listmarkers_advanced = intval($_POST['wpgmza_listmarkers_advanced']);
        $filterbycat = intval($_POST['wpgmza_filterbycat']);


        $gps = explode(",",$map_start_location);
        $map_start_lat = $gps[0];
        $map_start_lng = $gps[1];
        $map_default_marker = sanitize_text_field($_POST['upload_default_marker']);
        $kml = sanitize_text_field($_POST['wpgmza_kml']);
        $fusion = sanitize_text_field($_POST['wpgmza_fusion']);

        $data['map_default_starting_lat'] = $map_start_lat;
        $data['map_default_starting_lng'] = $map_start_lng;
        $data['map_default_height'] = $map_height;
        $data['map_default_width'] = $map_width;
        $data['map_default_zoom'] = $map_start_zoom;
        $data['map_default_type'] = $type;
        $data['map_default_alignment'] = $alignment;
        $data['map_default_order_markers_by'] = $order_markers_by;
        $data['map_default_order_markers_choice'] = $order_markers_choice;
        $data['map_default_show_user_location'] = $show_user_location;
        $data['map_default_directions'] = $directions_enabled;
        $data['map_default_bicycle'] = $bicycle_enabled;
        $data['map_default_traffic'] = $traffic_enabled;
        $data['map_default_dbox'] = $dbox;
        $data['map_default_dbox_width'] = $dbox_width;
        $data['map_default_default_to'] = $default_to;
        $data['map_default_listmarkers'] = $listmarkers;
        $data['map_default_listmarkers_advanced'] = $listmarkers_advanced;
        $data['map_default_filterbycat'] = $filterbycat;
        $data['map_default_marker'] = $map_default_marker;
        $data['map_default_width_type'] = $map_width_type;
        $data['map_default_height_type'] = $map_height_type;





        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_maps SET
                map_title = %s,
                map_width = %s,
                map_height = %s,
                map_start_lat = %f,
                map_start_lng = %f,
                map_start_location = %s,
                map_start_zoom = %d,
                default_marker = %s,
                type = %d,
                alignment = %d,
                order_markers_by = %d,
                order_markers_choice = %d,
                show_user_location = %d,
                directions_enabled = %d,
                kml = %s,
                bicycle = %d,
                traffic = %d,
                dbox = %d,
                dbox_width = %s,
                default_to = %s,
                listmarkers = %d,
                listmarkers_advanced = %d,
                filterbycat = %d,
                fusion = %s,
                map_width_type = %s,
                map_height_type = %s
                WHERE id = %d",

                $map_title,
                $map_width,
                $map_height,
                $map_start_lat,
                $map_start_lng,
                $map_start_location,
                $map_start_zoom,
                $map_default_marker,
                $type,
                $alignment,
                $order_markers_by,
                $order_markers_choice,
                $show_user_location,
                $directions_enabled,
                $kml,
                $bicycle_enabled,
                $traffic_enabled,
                $dbox,
                $dbox_width,
                $default_to,
                $listmarkers,
                $listmarkers_advanced,
                $filterbycat,
                $fusion,
                $map_width_type,
                $map_height_type,
                $map_id)
        );



        update_option('WPGMZA_SETTINGS', $data);


        echo "<div class='updated'>";
        _e("Your settings have been saved.","wp-google-maps");
        echo "</div>";

    }

    else if (isset($_POST['wpgmza_save_maker_location'])){
        global $wpdb;
        global $wpgmza_tblname;
        $mid = sanitize_text_field($_POST['wpgmaps_marker_id']);
        $wpgmaps_marker_lat = sanitize_text_field($_POST['wpgmaps_marker_lat']);
        $wpgmaps_marker_lng = sanitize_text_field($_POST['wpgmaps_marker_lng']);

        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname SET
                lat = %s,
                lng = %s
                WHERE id = %d",

                $wpgmaps_marker_lat,
                $wpgmaps_marker_lng,
                $mid)
        );





        echo "<div class='updated'>";
        _e("Your marker location has been saved.","wp-google-maps");
        echo "</div>";


    }
    else if (isset($_POST['wpgmza_save_poly'])){
        global $wpdb;
        global $wpgmza_tblname_poly;
        $mid = sanitize_text_field($_POST['wpgmaps_map_id']);
        $wpgmaps_polydata = sanitize_text_field($_POST['wpgmza_polygon']);
        $linecolor = sanitize_text_field($_POST['poly_line']);
        $fillcolor = sanitize_text_field($_POST['poly_fill']);
        $opacity = sanitize_text_field($_POST['poly_opacity']);

        $rows_affected = $wpdb->query( $wpdb->prepare(
                "INSERT INTO $wpgmza_tblname_poly SET
                map_id = %d,
                polydata = %s,
                linecolor = %s,
                fillcolor = %s,
                opacity = %s
                ",

                $mid,
                $wpgmaps_polydata,
                $linecolor,
                $fillcolor,
                $opacity
            )
        );
        echo "<div class='updated'>";
        _e("Your polygon has been created.","wp-google-maps");
        echo "</div>";


    }
    else if (isset($_POST['wpgmza_edit_poly'])){
        global $wpdb;
        global $wpgmza_tblname_poly;
        $mid = sanitize_text_field($_POST['wpgmaps_map_id']);
        $pid = sanitize_text_field($_POST['wpgmaps_poly_id']);
        $wpgmaps_polydata = sanitize_text_field($_POST['wpgmza_polygon']);
        $linecolor = sanitize_text_field($_POST['poly_line']);
        $fillcolor = sanitize_text_field($_POST['poly_fill']);
        $opacity = sanitize_text_field($_POST['poly_opacity']);

        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_poly SET
                polydata = %s,
                linecolor = %s,
                fillcolor = %s,
                opacity = %s
                WHERE `id` = %d"
                ,

                $wpgmaps_polydata,
                $linecolor,
                $fillcolor,
                $opacity,
                $pid
            )
        );
        echo "<div class='updated'>";
        _e("Your polygon has been saved.","wp-google-maps");
        echo "</div>";


    }
    else if (isset($_POST['wpgmza_save_polyline'])){
        global $wpdb;
        global $wpgmza_tblname_polylines;
        $mid = sanitize_text_field($_POST['wpgmaps_map_id']);
        $wpgmaps_polydata = sanitize_text_field($_POST['wpgmza_polyline']);
        $linecolor = sanitize_text_field($_POST['poly_line']);
        $linethickness = sanitize_text_field($_POST['poly_thickness']);
        $opacity = sanitize_text_field($_POST['poly_opacity']);

        $rows_affected = $wpdb->query( $wpdb->prepare(
                "INSERT INTO $wpgmza_tblname_polylines SET
                map_id = %d,
                polydata = %s,
                linecolor = %s,
                linethickness = %s,
                opacity = %s
                ",

                $mid,
                $wpgmaps_polydata,
                $linecolor,
                $linethickness,
                $opacity
            )
        );
        echo "<div class='updated'>";
        _e("Your polyline has been created.","wp-google-maps");
        echo "</div>";


    }
    else if (isset($_POST['wpgmza_edit_polyline'])){
        global $wpdb;
        global $wpgmza_tblname_polylines;
        $mid = sanitize_text_field($_POST['wpgmaps_map_id']);
        $pid = sanitize_text_field($_POST['wpgmaps_poly_id']);
        $wpgmaps_polydata = sanitize_text_field($_POST['wpgmza_polyline']);
        $linecolor = sanitize_text_field($_POST['poly_line']);
        $linethickness = sanitize_text_field($_POST['poly_thickness']);
        $opacity = sanitize_text_field($_POST['poly_opacity']);

        $rows_affected = $wpdb->query( $wpdb->prepare(
                "UPDATE $wpgmza_tblname_polylines SET
                polydata = %s,
                linecolor = %s,
                linethickness = %s,
                opacity = %s
                WHERE `id` = %d"
                ,

                $wpgmaps_polydata,
                $linecolor,
                $linethickness,
                $opacity,
                $pid
            )
        );
        echo "<div class='updated'>";
        _e("Your polyline has been saved.","wp-google-maps");
        echo "</div>";


    }
    else if (isset($_POST['wpgmza_save_settings'])){
        global $wpdb;
        $wpgmza_data['wpgmza_settings_image_width'] = sanitize_text_field($_POST['wpgmza_settings_image_width']);
        $wpgmza_data['wpgmza_settings_image_height'] = sanitize_text_field($_POST['wpgmza_settings_image_height']);
        $wpgmza_data['wpgmza_settings_use_timthumb'] = sanitize_text_field($_POST['wpgmza_settings_use_timthumb']);
        $wpgmza_data['wpgmza_settings_infowindow_width'] = sanitize_text_field($_POST['wpgmza_settings_infowindow_width']);
        $wpgmza_data['wpgmza_settings_infowindow_links'] = sanitize_text_field($_POST['wpgmza_settings_infowindow_links']);
        $wpgmza_data['wpgmza_settings_infowindow_address'] = sanitize_text_field($_POST['wpgmza_settings_infowindow_address']);
        $wpgmza_data['wpgmza_settings_infowindow_link_text'] = sanitize_text_field($_POST['wpgmza_settings_infowindow_link_text']);
        $wpgmza_data['wpgmza_settings_map_streetview'] = sanitize_text_field($_POST['wpgmza_settings_map_streetview']);
        $wpgmza_data['wpgmza_settings_map_zoom'] = sanitize_text_field($_POST['wpgmza_settings_map_zoom']);
        $wpgmza_data['wpgmza_settings_map_pan'] = sanitize_text_field($_POST['wpgmza_settings_map_pan']);
        $wpgmza_data['wpgmza_settings_map_type'] = sanitize_text_field($_POST['wpgmza_settings_map_type']);
        $wpgmza_data['wpgmza_settings_map_scroll'] = sanitize_text_field($_POST['wpgmza_settings_map_scroll']);
        $wpgmza_data['wpgmza_settings_map_draggable'] = sanitize_text_field($_POST['wpgmza_settings_map_draggable']);
        $wpgmza_data['wpgmza_settings_map_clickzoom'] = sanitize_text_field($_POST['wpgmza_settings_map_clickzoom']);
        $wpgmza_data['wpgmza_settings_ugm_striptags'] = sanitize_text_field($_POST['wpgmza_settings_map_striptags']);
        $wpgmza_data['wpgmza_settings_force_jquery'] = sanitize_text_field($_POST['wpgmza_settings_force_jquery']);
        $wpgmza_data['wpgmza_settings_markerlist_category'] = sanitize_text_field($_POST['wpgmza_settings_markerlist_category']);
        $wpgmza_data['wpgmza_settings_markerlist_icon'] = sanitize_text_field($_POST['wpgmza_settings_markerlist_icon']);
        $wpgmza_data['wpgmza_settings_markerlist_title'] = sanitize_text_field($_POST['wpgmza_settings_markerlist_title']);
        $wpgmza_data['wpgmza_settings_markerlist_address'] = sanitize_text_field($_POST['wpgmza_settings_markerlist_address']);
        $wpgmza_data['wpgmza_settings_markerlist_description'] = sanitize_text_field($_POST['wpgmza_settings_markerlist_description']);
        update_option('WPGMZA_OTHER_SETTINGS', $wpgmza_data);
        echo "<div class='updated'>";
        _e("Your settings have been saved.","wp-google-maps");
        echo "</div>";


    }



}






function wpgmaps_admin_menu() {
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    
    if (isset($wpgmza_settings['wpgmza_settings_access_level'])) { $access_level = $wpgmza_settings['wpgmza_settings_access_level']; } else { $access_level = "manage_options"; }
    add_menu_page('WPGoogle Maps', __('Maps','wp-google-maps'), $access_level, 'wp-google-maps-menu', 'wpgmaps_menu_layout', wpgmaps_get_plugin_url()."/images/map_app_small.png");
    
    if (function_exists('wpgmaps_menu_category_layout')) { add_submenu_page('wp-google-maps-menu', 'WP Google Maps - Categories', __('Categories','wp-google-maps'), $access_level , 'wp-google-maps-menu-categories', 'wpgmaps_menu_category_layout'); }
    if (function_exists('wpgmza_register_pro_version')) { add_submenu_page('wp-google-maps-menu', 'WP Google Maps - Advanced Options', __('Advanced','wp-google-maps'), $access_level , 'wp-google-maps-menu-advanced', 'wpgmaps_menu_advanced_layout'); }
    add_submenu_page('wp-google-maps-menu', 'WP Google Maps - Settings', __('Settings','wp-google-maps'), $access_level , 'wp-google-maps-menu-settings', 'wpgmaps_menu_settings_layout');
    add_submenu_page('wp-google-maps-menu', 'WP Google Maps - Support', __('Support','wp-google-maps'), $access_level , 'wp-google-maps-menu-support', 'wpgmaps_menu_support_layout');

}


function wpgmaps_menu_layout() {
    
    
    
    $handle = 'avia-google-maps-api';
    $list = 'enqueued';
    if (wp_script_is( $handle, $list )) {
        wp_deregister_script('avia-google-maps-api');
    }
    
    /*check to see if we have write permissions to the plugin folder*/
    if (!isset($_GET['action'])) {
        wpgmza_map_page();
    } else {
        echo"<br /><div style='float:right; display:block; width:250px; height:65px; padding:6px; text-align:center; background-color: #EEE; border: 1px solid #E6DB55; margin-right:17px;'><strong>".__("Experiencing problems with the plugin?","wp-google-maps")."</strong><br /><a href='http://www.wpgmaps.com/documentation/troubleshooting/' title='WP Google Maps Troubleshooting Section' target='_BLANK'>".__("See the troubleshooting manual.","wp-google-maps")."</a> <br />".__("Or ask a question on our ","wp-google-maps")." <a href='http://www.wpgmaps.com/forums/forum/support-forum/' title='WP Google Maps Support Forum' target='_BLANK'>".__("Support forum.","wp-google-maps")."</a></div>";

        if ($_GET['action'] == "trash" && isset($_GET['map_id'])) {
            if ($_GET['s'] == "1") {
                if (wpgmaps_trash_map(sanitize_text_field($_GET['map_id']))) {
                    echo "<script>window.location = \"".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu\"</script>";
                } else {
                    _e("There was a problem deleting the map.","wp-google-maps");
                }
            } else {
                $res = wpgmza_get_map_data(sanitize_text_field($_GET['map_id']));
                echo "<h2>".__("Delete your map","wp-google-maps")."</h2><p>".__("Are you sure you want to delete the map","wp-google-maps")." <strong>\"".$res->map_title."?\"</strong> <br /><a href='?page=wp-google-maps-menu&action=trash&map_id=".sanitize_text_field($_GET['map_id'])."&s=1'>".__("Yes","wp-google-maps")."</a> | <a href='?page=wp-google-maps-menu'>".__("No","wp-google-maps")."</a></p>";
            }
        }
        if ($_GET['action'] == "duplicate" && isset($_GET['map_id'])) {
            if (function_exists('wpgmaps_duplicate_map')) {    
                $new_id = wpgmaps_duplicate_map(sanitize_text_field($_GET['map_id']));
                if ($new_id > 0) {
                    wpgmza_map_page();
                } else {
                    _e("There was a problem duplicating the map.","wp-google-maps");
                    wpgmza_map_page();
                }
            }
        }
         
        else if ($_GET['action'] == "edit_marker" && isset($_GET['id'])) {

            wpgmza_edit_marker(sanitize_text_field($_GET['id']));

        }
        else if ($_GET['action'] == "add_poly" && isset($_GET['map_id'])) {

            if (function_exists("wpgmza_b_real_pro_add_poly")) {
                wpgmza_b_real_pro_add_poly(sanitize_text_field($_GET['map_id']));
            } else {
                wpgmza_b_pro_add_poly(sanitize_text_field($_GET['map_id']));
            }

        }
        else if ($_GET['action'] == "edit_poly" && isset($_GET['map_id'])) {

            if (function_exists("wpgmza_b_real_pro_edit_poly")) {
                wpgmza_b_real_pro_edit_poly(sanitize_text_field($_GET['map_id']));
            } else {
                wpgmza_b_pro_edit_poly(sanitize_text_field($_GET['map_id']));
            }
            

        }
        else if ($_GET['action'] == "add_polyline" && isset($_GET['map_id'])) {

            wpgmza_b_pro_add_polyline(sanitize_text_field($_GET['map_id']));

        }
        else if ($_GET['action'] == "edit_polyline" && isset($_GET['map_id'])) {

            wpgmza_b_pro_edit_polyline(sanitize_text_field($_GET['map_id']));

        }
        else if ($_GET['action'] == 'welcome_page') {
            $file = dirname(__FILE__).'/base/classes/WPGM_templates.php';
            include ($file);
            $wpgmc = new WPGMAPS_templates();
            $wpgmc->welcome_page();
        
        }
        else {

            if (function_exists('wpgmza_register_pro_version')) {

                $prov = get_option("WPGMZA_PRO");
                $wpgmza_pro_version = $prov['version'];
                if (floatval($wpgmza_pro_version) < 5.41) {
                    wpgmaps_upgrade_notice();
                    wpgmza_pro_menu();
                } else {
                    wpgmza_pro_menu();
                }


            } else {
                wpgmza_basic_menu();

            }

        }
    }

}



function wpgmaps_menu_marker_layout() {

    if (!$_GET['action']) {

        wpgmza_marker_page();

    } else {
        echo"<br /><div style='float:right; display:block; width:250px; height:36px; padding:6px; text-align:center; background-color: #EEE; border: 1px solid #E6DB55; margin-right:17px;'><strong>".__("Experiencing problems with the plugin?","wp-google-maps")."</strong><br /><a href='http://www.wpgmaps.com/documentation/troubleshooting/' title='WP Google Maps Troubleshooting Section' target='_BLANK'>".__("See the troubleshooting manual.","wp-google-maps")."</a></div>";


        if ($_GET['action'] == "trash" && isset($_GET['marker_id'])) {

            if ($_GET['s'] == "1") {
                if (wpgmaps_trash_marker(sanitize_text_field($_GET['marker_id']))) {
                    echo "<script>window.location = \"".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-marker-menu\"</script>";
                } else {
                    _e("There was a problem deleting the marker.");;
                }
            } else {
                $res = wpgmza_get_marker_data(sanitize_text_field($_GET['map_id']));
                echo "<h2>".__("Delete Marker","wp-google-maps")."</h2><p>".__("Are you sure you want to delete this marker:","wp-google-maps")." <strong>\"".$res->address."?\"</strong> <br /><a href='?page=wp-google-maps-marker-menu&action=trash&marker_id=".sanitize_text_field($_GET['marker_id'])."&s=1'>".__("Yes","wp-google-maps")."</a> | <a href='?page=wp-google-maps-marker-menu'>".__("No","wp-google-maps")."</a></p>";
            }



        }
    }

}

function wpgmaps_menu_settings_layout() {
    if (function_exists('wpgmza_register_pro_version')) {
        if (function_exists('wpgmaps_settings_page_pro')) {
            wpgmaps_settings_page_pro();
        }
    } else {
        wpgmaps_settings_page_basic();
    }
}


function wpgmaps_settings_page_basic() {
    
    wpgmza_stats("settings_basic");
    
    echo"<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>".__("WP Google Map Settings","wp-google-maps")."</h2>";

    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if (isset($wpgmza_settings['wpgmza_settings_map_streetview'])) { $wpgmza_settings_map_streetview = $wpgmza_settings['wpgmza_settings_map_streetview']; }
    if (isset($wpgmza_settings['wpgmza_settings_map_zoom'])) { $wpgmza_settings_map_zoom = $wpgmza_settings['wpgmza_settings_map_zoom']; }
    if (isset($wpgmza_settings['wpgmza_settings_map_pan'])) { $wpgmza_settings_map_pan = $wpgmza_settings['wpgmza_settings_map_pan']; }
    if (isset($wpgmza_settings['wpgmza_settings_map_type'])) { $wpgmza_settings_map_type = $wpgmza_settings['wpgmza_settings_map_type']; }
    if (isset($wpgmza_settings['wpgmza_settings_force_jquery'])) { $wpgmza_force_jquery = $wpgmza_settings['wpgmza_settings_force_jquery']; }
    if (isset($wpgmza_settings['wpgmza_settings_map_scroll'])) { $wpgmza_settings_map_scroll = $wpgmza_settings['wpgmza_settings_map_scroll']; }
    if (isset($wpgmza_settings['wpgmza_settings_map_draggable'])) { $wpgmza_settings_map_draggable = $wpgmza_settings['wpgmza_settings_map_draggable']; }
    if (isset($wpgmza_settings['wpgmza_settings_map_clickzoom'])) { $wpgmza_settings_map_clickzoom = $wpgmza_settings['wpgmza_settings_map_clickzoom']; }
    if (isset($wpgmza_settings['wpgmza_api_version'])) { $wpgmza_api_version = $wpgmza_settings['wpgmza_api_version']; }
    if (isset($wpgmza_settings['wpgmza_custom_css'])) { $wpgmza_custom_css = $wpgmza_settings['wpgmza_custom_css']; } else { $wpgmza_custom_css  = ""; }

    $wpgmza_api_version_selected = array();
    $wpgmza_api_version_selected[0] = "";
    $wpgmza_api_version_selected[1] = "";
    $wpgmza_api_version_selected[2] = "";
    
    if (isset($wpgmza_api_version) && $wpgmza_api_version == "3.14") { $wpgmza_api_version_selected[0] = "selected"; }
    else if (isset($wpgmza_api_version) && $wpgmza_api_version == "3.15") { $wpgmza_api_version_selected[1] = "selected"; }
    else if (isset($wpgmza_api_version) && $wpgmza_api_version == "3.exp") { $wpgmza_api_version_selected[2] = "selected"; }
    else { $wpgmza_api_version_selected[0] = "selected"; }
    
    $wpgmza_settings_map_open_marker_by_checked[0] = "";
    $wpgmza_settings_map_open_marker_by_checked[1] = "";
    if (isset($wpgmza_settings['wpgmza_settings_map_open_marker_by'])) { $wpgmza_settings_map_open_marker_by = $wpgmza_settings['wpgmza_settings_map_open_marker_by']; } else { $wpgmza_settings_map_open_marker_by = false; }
    if ($wpgmza_settings_map_open_marker_by == '1') { $wpgmza_settings_map_open_marker_by_checked[0] = "checked='checked'"; }
    else if ($wpgmza_settings_map_open_marker_by == '2') { $wpgmza_settings_map_open_marker_by_checked[1] = "checked='checked'"; }
    else { $wpgmza_settings_map_open_marker_by_checked[0] = "checked='checked'"; }
    
    
    $show_advanced_marker_tr = 'style="visibility:hidden; display:none;"';
    $wpgmza_settings_marker_pull_checked[0] = "";
    $wpgmza_settings_marker_pull_checked[1] = "";
    if (isset($wpgmza_settings['wpgmza_settings_marker_pull'])) { $wpgmza_settings_marker_pull = $wpgmza_settings['wpgmza_settings_marker_pull']; } else { $wpgmza_settings_marker_pull = false; }
    if ($wpgmza_settings_marker_pull == '0' || $wpgmza_settings_marker_pull == 0) { $wpgmza_settings_marker_pull_checked[0] = "checked='checked'"; $show_advanced_marker_tr = 'style="visibility:hidden; display:none;"'; }
    else if ($wpgmza_settings_marker_pull == '1' || $wpgmza_settings_marker_pull == 1) { $wpgmza_settings_marker_pull_checked[1] = "checked='checked'";  $show_advanced_marker_tr = 'style="visibility:visible; display:table-row;"'; }
    else { $wpgmza_settings_marker_pull_checked[0] = "checked='checked'"; $show_advanced_marker_tr = 'style="visibility:hidden; display:none;"'; }   
    
    
    
    

    $wpgmza_access_level_checked[0] = "";
    $wpgmza_access_level_checked[1] = "";
    $wpgmza_access_level_checked[2] = "";
    $wpgmza_access_level_checked[3] = "";
    $wpgmza_access_level_checked[4] = "";
    if (isset($wpgmza_settings['wpgmza_settings_access_level'])) { $wpgmza_access_level = $wpgmza_settings['wpgmza_settings_access_level']; } else { $wpgmza_access_level = ""; }
    if ($wpgmza_access_level == "manage_options") { $wpgmza_access_level_checked[0] = "selected"; }
    else if ($wpgmza_access_level == "edit_pages") { $wpgmza_access_level_checked[1] = "selected"; }
    else if ($wpgmza_access_level == "publish_posts") { $wpgmza_access_level_checked[2] = "selected"; }
    else if ($wpgmza_access_level == "edit_posts") { $wpgmza_access_level_checked[3] = "selected"; }
    else if ($wpgmza_access_level == "read") { $wpgmza_access_level_checked[4] = "selected"; }
    else { $wpgmza_access_level_checked[0] = "selected"; }
    
    if (isset($wpgmza_settings_map_scroll)) { if ($wpgmza_settings_map_scroll == "yes") { $wpgmza_scroll_checked = "checked='checked'"; } else { $wpgmza_scroll_checked = ""; } } else { $wpgmza_scroll_checked = ""; }
    if (isset($wpgmza_settings_map_draggable)) { if ($wpgmza_settings_map_draggable == "yes") { $wpgmza_draggable_checked = "checked='checked'"; } else { $wpgmza_draggable_checked = ""; } } else { $wpgmza_draggable_checked = ""; }
    if (isset($wpgmza_settings_map_clickzoom)) { if ($wpgmza_settings_map_clickzoom == "yes") { $wpgmza_clickzoom_checked = "checked='checked'"; } else { $wpgmza_clickzoom_checked = ""; } } else { $wpgmza_clickzoom_checked = ""; }

    
    if (isset($wpgmza_settings_map_streetview)) { if ($wpgmza_settings_map_streetview == "yes") { $wpgmza_streetview_checked = "checked='checked'"; }  else { $wpgmza_streetview_checked = ""; } }  else { $wpgmza_streetview_checked = ""; }
    if (isset($wpgmza_settings_map_zoom)) { if ($wpgmza_settings_map_zoom == "yes") { $wpgmza_zoom_checked = "checked='checked'"; } else { $wpgmza_zoom_checked = ""; } } else { $wpgmza_zoom_checked = ""; }
    if (isset($wpgmza_settings_map_pan)) { if ($wpgmza_settings_map_pan == "yes") { $wpgmza_pan_checked = "checked='checked'"; } else { $wpgmza_pan_checked = ""; } } else { $wpgmza_pan_checked = ""; }
    if (isset($wpgmza_settings_map_type)) { if ($wpgmza_settings_map_type == "yes") { $wpgmza_type_checked = "checked='checked'"; } else { $wpgmza_type_checked = ""; } } else { $wpgmza_type_checked = ""; }
    if (isset($wpgmza_force_jquery)) { if ($wpgmza_force_jquery == "yes") { $wpgmza_force_jquery_checked = "checked='checked'"; } else { $wpgmza_force_jquery_checked = ""; } } else { $wpgmza_force_jquery_checked = ""; }

    if (function_exists('wpgmza_register_pro_version')) {
        $pro_settings1 = wpgmaps_settings_page_sub('infowindow');
        $prov = get_option("WPGMZA_PRO");
        $wpgmza_pro_version = $prov['version'];
        if (floatval($wpgmza_pro_version) < 3.9) {
            $prov_msg = "<div class='error below-h1'><p>Please note that these settings will only work with the Pro Addon version 3.9 and above. Your current version is $wpgmza_pro_version. To download the latest version, please email <a href='mailto:nick@wpgmaps.com'>nick@wpgmaps.com</a></p></div>";
        }
    } else {
        $pro_settings1 = "";
        $prov_msg = "";
    }
    $marker_location = wpgmza_return_marker_path();
    $marker_url = wpgmza_return_marker_url();
    
    
    $wpgmza_file_perms = substr(sprintf('%o', @fileperms($marker_location)), -4);
    $fpe = false;
    $fpe_error = "";
    if ($wpgmza_file_perms == "0777" || $wpgmza_file_perms == "0755" || $wpgmza_file_perms == "0775" || $wpgmza_file_perms == "0705" || $wpgmza_file_perms == "2777" || $wpgmza_file_perms == "2755" || $wpgmza_file_perms == "2775" || $wpgmza_file_perms == "2705") { 
        $fpe = true;
        $fpe_error = "";
    }
    else if ($wpgmza_file_perms == "0") {
        $fpe = false;
        $fpe_error = __("This folder does not exist. Please create it.","wp-google-maps"). " ($marker_location)";
    } else { 
        $fpe = false;
        $fpe_error = __("File Permissions:","wp-google-maps").$wpgmza_file_perms." ".__(" - The plugin does not have write access to this folder. Please CHMOD this folder to 755 or 777, or change the location","wp-google-maps");
    }
    
    if (!$fpe) {
        $wpgmza_file_perms_check = "<span style='color:red;'>$fpe_error</span>";
    } else {
        $wpgmza_file_perms_check = "<span style='color:green;'>$fpe_error</span>";
        
    }
    $upload_dir = wp_upload_dir();
    
        $map_settings_action = '';
            
            $ret = "<form action='' method='post' id='wpgmaps_options'>";
            $ret .= "    <p>$prov_msg</p>";


            $ret .= "    <div id=\"wpgmaps_tabs\">";
            $ret .= "        <ul>";
            $ret .= "                <li><a href=\"#tabs-1\">".__("Maps","wp-google-maps")."</a></li>";
            $ret .= "                <li><a href=\"#tabs-2\">".__("InfoWindows","wp-google-maps")."</a></li>";
            $ret .= "                <li><a href=\"#tabs-3\">".__("Marker Listing","wp-google-maps")."</a></li>";
            $ret .= "                <li><a href=\"#tabs-4\">".__("Advanced","wp-google-maps")."</a></li>";
            $ret .= "                <li><a href=\"#tabs-5\">".__("Error Log","wp-google-maps")."</a></li>";
            $ret .= "        </ul>";
            $ret .= "        <div id=\"tabs-1\">";
            $ret .= "                <h3>".__("Map Settings")."</h3>";
            $ret .= "                <table class='form-table'>";
            $ret .= "                <tr>";
            $ret .= "                     <td width='200' valign='top' style='vertical-align:top;'>".__("General Map Settings","wp-google-maps").":</td>";
            $ret .= "                     <td>";
            $ret .= "                            <input name='wpgmza_settings_map_streetview' type='checkbox' id='wpgmza_settings_map_streetview' value='yes' $wpgmza_streetview_checked /> ".__("Disable StreetView")."<br />";
            $ret .= "                            <input name='wpgmza_settings_map_zoom' type='checkbox' id='wpgmza_settings_map_zoom' value='yes' $wpgmza_zoom_checked /> ".__("Disable Zoom Controls")."<br />";
            $ret .= "                            <input name='wpgmza_settings_map_pan' type='checkbox' id='wpgmza_settings_map_pan' value='yes' $wpgmza_pan_checked /> ".__("Disable Pan Controls")."<br />";
            $ret .= "                            <input name='wpgmza_settings_map_type' type='checkbox' id='wpgmza_settings_map_type' value='yes' $wpgmza_type_checked /> ".__("Disable Map Type Controls")."<br />";
            $ret .= "                            <input name='wpgmza_settings_map_scroll' type='checkbox' id='wpgmza_settings_map_scroll' value='yes' $wpgmza_scroll_checked /> ".__("Disable Mouse Wheel Zoom","wp-google-maps")."<br />";
            $ret .= "                            <input name='wpgmza_settings_map_draggable' type='checkbox' id='wpgmza_settings_map_draggable' value='yes' $wpgmza_draggable_checked /> ".__("Disable Mouse Dragging","wp-google-maps")."<br />";
            $ret .= "                            <input name='wpgmza_settings_map_clickzoom' type='checkbox' id='wpgmza_settings_map_clickzoom' value='yes' $wpgmza_clickzoom_checked /> ".__("Disable Mouse Double Click Zooming","wp-google-maps")."<br />";

            $ret .= "                    </td>";
            $ret .= "                 </tr>";
            $ret .= "               <tr>";
            $ret .= "                        <td width='200' valign='top'>".__("Troubleshooting Options","wp-google-maps").":</td>";
            $ret .= "                     <td>";
            $ret .= "                            <input name='wpgmza_settings_force_jquery' type='checkbox' id='wpgmza_settings_force_jquery' value='yes' $wpgmza_force_jquery_checked /> ".__("Over-ride current jQuery with version 1.8.3 (Tick this box if you are receiving jQuery related errors)")."<br />";
            $ret .= "                    </td>";
            $ret .= "                </tr>";
            $ret .= "                <tr>";
            $ret .= "                        <td width='200' valign='top'>".__("Use Google Maps API","wp-google-maps").":</td>";
            $ret .= "                     <td>";
            $ret .= "                        <select id='wpgmza_api_version' name='wpgmza_api_version'  >";
            $ret .= "                                    <option value=\"3.14\" ".$wpgmza_api_version_selected[0].">3.14</option>";
            $ret .= "                                    <option value=\"3.15\" ".$wpgmza_api_version_selected[1].">3.15</option>";
            $ret .= "                                    <option value=\"3.exp\" ".$wpgmza_api_version_selected[2].">3.exp</option>";

            $ret .= "                                </select>    ";
            $ret .= "                    </td>";
            $ret .= "                </tr>";
            $ret .= "                <tr>";
            $ret .= "                        <td width='200' valign='top'>".__("Lowest level of access to the map editor","wp-google-maps").":</td>";
            $ret .= "                     <td>";
            $ret .= "                        <select id='wpgmza_access_level' name='wpgmza_access_level'  >";
            $ret .= "                                    <option value=\"manage_options\" ".$wpgmza_access_level_checked[0].">Admin</option>";
            $ret .= "                                    <option value=\"edit_pages\" ".$wpgmza_access_level_checked[1].">Editor</option>";
            $ret .= "                                    <option value=\"publish_posts\" ".$wpgmza_access_level_checked[2].">Author</option>";
            $ret .= "                                    <option value=\"edit_posts\" ".$wpgmza_access_level_checked[3].">Contributor</option>";
            $ret .= "                                    <option value=\"read\" ".$wpgmza_access_level_checked[4].">Subscriber</option>";
            $ret .= "                        </select>    ";
            $ret .= "                    </td>";
            $ret .= "                </tr>";


            $ret .= "            </table>";

            $ret .= "        </div>";
            $ret .= "        <div id=\"tabs-2\">";
            $ret .= "            <h3>".__("Marker InfoWindow Settings")."</h3>";
            $ret .= "            <table class='form-table'>";
            $ret .= "                <tr>";
            $ret .= "                    <td valign='top' width='200' style='vertical-align:top;'>".__("Open Marker InfoWindows by","wp-google-maps")." </td>";
            $ret .= "                        <td><input name='wpgmza_settings_map_open_marker_by' type='radio' id='wpgmza_settings_map_open_marker_by' value='1' ".$wpgmza_settings_map_open_marker_by_checked[0]." />Click<br /><input name='wpgmza_settings_map_open_marker_by' type='radio' id='wpgmza_settings_map_open_marker_by' value='2' ".$wpgmza_settings_map_open_marker_by_checked[1]." />Hover </td>";
            $ret .= "                </tr>";
  
            $ret .= "            </table>";
            $ret .= "        </div>";
            $ret .= "        <div id=\"tabs-3\">";

            $ret .= "            <table class='form-table'>";
            $ret .= "        <h3>".__("Marker Listing Settings","wp-google-maps")."</h3>";
            $ret .= "       <p>".__("Changing these settings will alter the way the marker list appears on your website.","wp-google-maps")."</p>";
            $ret .= "                 <div class=\"wpgm_notice_message\">";
            $ret .= "                     <ul>";
            $ret .= "                         <li>";
            $ret .= "                             <i class=\"fa fa-hand-o-right\"> </i> <a target='_blank' href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=mlisting_settings\">Add Beautiful Marker Listings</a> to your maps with the Pro version for only $39.99 once off. Support and updates included forever.";
            $ret .= "                         </li>";
            $ret .= "                     </ul>";
            $ret .= "                 </div>";
            $ret .= "       <hr />";
            
            $ret .= "       <h4>".__("Advanced Marker Listing","wp-google-maps")."</h4>";
            $ret .= "       <table class='form-table'>";
            $ret .= "       <tr>";
            $ret .= "           <td width='200' valign='top' style='vertical-align:top;'>".__("Column settings","wp-google-maps")."</td>";
            $ret .= "           <td>";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Icon column","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Title column","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Address column","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Category column","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Description column","wp-google-maps")."<br />";
            $ret .= "           </td>";
            $ret .= "       </tr>";
            $ret .= "   </table>";
            $ret .= "   <hr/>";
             
            $ret .= "   <h4>".__("Carousel Marker Listing","wp-google-maps")."</h4>";
            $ret .= "   <table class='form-table'>";
            $ret .= "       <tr>";
            $ret .= "           <td width='200' valign='top' style='vertical-align:top;'>".__("Theme selection","wp-google-maps")."</td>";
            $ret .= "           <td>";
            $ret .= "               <select disabled >";
            $ret .= "                   <option >".__("Sky","wp-google-maps")."</option>";
            $ret .= "                   <option >".__("Sun","wp-google-maps")."</option>";
            $ret .= "                   <option >".__("Earth","wp-google-maps")."</option>";
            $ret .= "                   <option >".__("Monotone","wp-google-maps")."</option>";
            $ret .= "                   <option >".__("PinkPurple","wp-google-maps")."</option>";
            $ret .= "                   <option >".__("White","wp-google-maps")."</option>";
            $ret .= "                   <option >".__("Black","wp-google-maps")."</option>";
            $ret .= "               </select>";
            $ret .= "           </td>";
            $ret .= "       </tr>";
            $ret .= "       <tr>";
            $ret .= "           <td width='200' valign='top' style='vertical-align:top;'>".__("Carousel settings","wp-google-maps")."</td>";
            $ret .= "           <td>";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Image","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Title","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Marker Icon","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Address","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Description","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Marker Link","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Hide the Directions Link","wp-google-maps")."<br />";
            $ret .= "               <br /><input type='checkbox' disabled /> ".__("Resize Images with Timthumb","wp-google-maps")."<br />";
            $ret .= "               <br /><input type='checkbox' disabled /> ".__("Enable lazyload of images","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Enable autoheight","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox'  disabled /> ".__("Enable pagination","wp-google-maps")."<br />";
            $ret .= "               <input type='checkbox' disabled /> ".__("Enable navigation","wp-google-maps")."<br />";
            $ret .= "               <input type='text' disabled /> ".__("Items","wp-google-maps")."<br />";
            $ret .= "               <input type='text' disabled /> ".__("Autoplay after x milliseconds (1000 = 1 second)","wp-google-maps")."<br />";
            $ret .= "           </td>";
            $ret .= "    </tr>";
            $ret .= "   </table>";
            $ret .= "</div>";

            $ret .= "<div id=\"tabs-4\">";
            $ret .= "               <h3>".__("Advanced Settings","wp-google-maps")."</h3>";
            $ret .= "               <h4>".__("Marker Data Location","wp-google-maps")."</h4>";
            $ret .= "                   <table class='form-table'>";
            $ret .= "                <tr>";
            $ret .= "                    <td valign='top' width='200' style='vertical-align:top;'>".__("Pull marker data from","wp-google-maps")." </td>";
            $ret .= "                        <td>"
                    . "                         <input name='wpgmza_settings_marker_pull' type='radio' id='wpgmza_settings_marker_pull' class='wpgmza_settings_marker_pull' value='0' ".$wpgmza_settings_marker_pull_checked[0]." />".__("Database (Great for small amounts of markers)","wp-google-maps")."<br />"
                    . "                         <input name='wpgmza_settings_marker_pull' type='radio' id='wpgmza_settings_marker_pull' class='wpgmza_settings_marker_pull' value='1' ".$wpgmza_settings_marker_pull_checked[1]." />".__("XML File  (Great for large amounts of markers)","wp-google-maps")
                    . "                      </td>";
            $ret .= "                </tr>";
            $ret .= "                <p>".__("We suggest that you change the two fields below ONLY if you are experiencing issues when trying to save the marker XML files.","wp-google-maps")."</p>";
            $ret .= "                    <tr class='wpgmza_marker_dir_tr' $show_advanced_marker_tr>";
            $ret .= "                           <td width='200' valign='top' style='vertical-align:top;'>".__("Marker data XML directory","wp-google-maps").":</td>";
            $ret .= "                           <td>";
            $ret .= "                               <input id='wpgmza_marker_xml_location' name='wpgmza_marker_xml_location' value='".get_option("wpgmza_xml_location")."' class='regular-text code' /> $wpgmza_file_perms_check";
            $ret .= "                               <br />";
            $ret .= "                               <small>".__("You can use the following","wp-google-maps").": {wp_content_dir},{plugins_dir},{uploads_dir}<br /><br />";
            $ret .= "                               ".__("Currently using","wp-google-maps").": <strong><em>$marker_location</em></strong></small>";
            $ret .= "                       </td>";
            $ret .= "                   </tr>";
            $ret .= "                    <tr class='wpgmza_marker_url_tr' $show_advanced_marker_tr>";
            $ret .= "                           <td width='200' valign='top' style='vertical-align:top;'>".__("Marker data XML URL","wp-google-maps").":</td>";
            $ret .= "                        <td>";
            $ret .= "                           <input id='wpgmza_marker_xml_url' name='wpgmza_marker_xml_url' value='".get_option("wpgmza_xml_url")."' class='regular-text code' />";
            $ret .= "                              <br />";
            $ret .= "                               <br />";
            $ret .= "                               <small>".__("You can use the following","wp-google-maps").": {wp_content_url},{plugins_url},{uploads_url}<br /><br />";
            $ret .= "                               ".__("Currently using","wp-google-maps").": <strong><em>$marker_url</em></strong></small>";
            $ret .= "                       </td>";
            $ret .= "                   </tr>";
            $ret .= "                   </table>";
            $ret .= "               <h4>".__("Custom CSS","wp-google-maps")."</h4>";
            $ret .= "                   <table class='form-table'>";
            $ret .= "                    <tr>";
            $ret .= "                           <td width='200' valign='top' style='vertical-align:top;'>".__("Custom CSS","wp-google-maps").":</td>";
            $ret .= "                           <td>";
            $ret .= "                               <textarea name=\"wpgmza_custom_css\" id=\"wpgmza_marker_xml_url\" cols=\"70\" rows=\"10\">$wpgmza_custom_css</textarea>";
            $ret .= "                       </td>";
            $ret .= "                   </tr>";
            $ret .= "                   </table>";
            
            
            
            
            $ret .= "           </div>";
            $ret .= "           <div id=\"tabs-5\">";
            $ret .= "               <table class='form-table'>";
            $ret .= "                   <h3>".__("WP Google Maps Error log","wp-google-maps")."</h3>";
            $ret .= "                   <p>".__("Having issues? Perhaps something below can give you a clue as to what's wrong. Alternatively, email this through to nick@wpgmaps.com for help!","wp-google-maps")."</p>";
            $ret .= "                   <textarea style='width:100%; height:600px;' readonly>";
            $ret .= "                   ".wpgmza_return_error_log()."";
            $ret .= "                   </textarea>";
            $ret .= "               </table>";
            $ret .= "           </div>";
            $ret .= "       </div>";
            $ret .= "       <p class='submit'><input type='submit' name='wpgmza_save_settings' class='button-primary' value='".__("Save Settings","wp-google-maps")." &raquo;' /></p>";
            $ret .= "   </form>";
            $ret .=  "</div>";
            
            echo $ret;
            

}

function wpgmaps_menu_advanced_layout() {
    if (function_exists('wpgmza_register_pro_version')) {
        wpgmza_pro_advanced_menu();
    }

}

function wpgmaps_menu_support_layout() {
    if (function_exists('wpgmza_pro_support_menu')) {
        wpgmza_pro_support_menu();
    } else {
        wpgmza_basic_support_menu();
    }

}
function wpgmza_review_nag() {
    if (!function_exists('wpgmza_register_pro_version')) {
        $wpgmza_review_nag = get_option("wpgmza_review_nag");
        if ($wpgmza_review_nag) { 
            return;
        }
        $wpgmza_stats = get_option("wpgmza_stats");

        
        if ($wpgmza_stats) {
            if (isset($wpgmza_stats['dashboard']['first_accessed'])) {
                $first_acc = $wpgmza_stats['dashboard']['first_accessed'];
                $datedif = time() - strtotime($first_acc);
                $days_diff = floor($datedif/(60*60*24));

                if ($days_diff >= 10) {
                    $rate_text = sprintf( __( '<h3>We need your love!</h3><p>If you are enjoying our plugin, please consider <a href="%1$s" target="_blank" class="button button-primary">reviewing WP Google Maps</a>. It would mean the world to us! If you are experiencing issues with the plugin, please <a href="%2$s" target="_blank"  class="button button-secondary">contact us</a> and we will help you as soon as humanly possible!</p>', 'wp-google-maps' ),
                        'https://wordpress.org/support/view/plugin-reviews/wp-google-maps?filter=5',
                        'http://www.wpgmaps.com/contact-us/'
                    );
                    echo "<style>.wpgmza_upgrade_nag { display:none; }</style>";
                    echo "<div class='updated wpgmza_nag_review_div'>".$rate_text."<p><a href='admin.php?page=wp-google-maps-menu&action2=close_review' class='wpgmza_close_review_nag button button-secondary' title='".__("We will not nag you again, promise!","wp-google-maps")."'>".__("Close","wp-google-maps")."</a></p></div>";
                }
            }
        }
    }

}
function wpgmza_map_page() {
    

    if (isset($_GET['action2']) && $_GET['action2'] == "close_review") {
        update_option("wpgmza_review_nag",time());
    }

    wpgmza_review_nag();
    
    if (function_exists('wpgmza_register_pro_version')) {
        wpgmza_stats("list_maps_pro");
        echo"<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>".__("My Maps","wp-google-maps")." <a href=\"admin.php?page=wp-google-maps-menu&action=new\" class=\"add-new-h2\">".__("Add New","wp-google-maps")."</a></h2>";
        wpgmaps_check_versions();
        wpgmaps_list_maps();
    } 
    else {
        wpgmza_stats("list_maps_basic");
        echo"<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>".__("My Maps","wp-google-maps")."</h2>";
        echo"<p class='wpgmza_upgrade_nag'><i><a href='http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=mappage_1' target=\"_BLANK\" title='".__("Pro Version","wp-google-maps")."'>".__("Create unlimited maps","wp-google-maps")."</a> ".__("with the","wp-google-maps")." <a href='http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=mappage_2' title='Pro Version'  target=\"_BLANK\">".__("Pro Version","wp-google-maps")."</a> ".__("of WP Google Maps for only","wp-google-maps")." <strong>$39.99!</strong></i></p>";
        wpgmaps_list_maps();


    }
    echo "</div>";
    echo"<br /><div style='float:right;'><a href='http://www.wpgmaps.com/documentation/troubleshooting/'  target='_BLANK' title='WP Google Maps Troubleshooting Section'>".__("Problems with the plugin? See the troubleshooting manual.","wp-google-maps")."</a></div>";
}


function wpgmaps_list_maps() {
    global $wpdb;
    global $wpgmza_tblname_maps;
    
    if (function_exists('wpgmaps_list_maps_pro')) { wpgmaps_list_maps_pro(); return; }

    if ($wpgmza_tblname_maps) { $table_name = $wpgmza_tblname_maps; } else { $table_name = $wpdb->prefix . "wpgmza_maps"; }


    $results = $wpdb->get_results(
        "
	SELECT *
	FROM $table_name
        WHERE `active` = 0
        ORDER BY `id` DESC
	"
    );
    echo "

      <table class=\"wp-list-table widefat fixed \" cellspacing=\"0\">
	<thead>
	<tr>
		<th scope='col' id='id' class='manage-column column-id sortable desc'  style=''><span>".__("ID","wp-google-maps")."</span></th>
                <th scope='col' id='map_title' class='manage-column column-map_title sortable desc'  style=''><span>".__("Title","wp-google-maps")."</span></th>
                <th scope='col' id='map_width' class='manage-column column-map_width' style=\"\">".__("Width","wp-google-maps")."</th>
                <th scope='col' id='map_height' class='manage-column column-map_height'  style=\"\">".__("Height","wp-google-maps")."</th>
                <th scope='col' id='type' class='manage-column column-type sortable desc'  style=\"\"><span>".__("Type","wp-google-maps")."</span></th>
        </tr>
	</thead>
        <tbody id=\"the-list\" class='list:wp_list_text_link'>
";
    foreach ( $results as $result ) {
        if ($result->type == "1") { $map_type = __("Roadmap","wp-google-maps"); }
        else if ($result->type == "2") { $map_type = __("Satellite","wp-google-maps"); }
        else if ($result->type == "3") { $map_type = __("Hybrid","wp-google-maps"); }
        else if ($result->type == "4") { $map_type = __("Terrain","wp-google-maps"); }
        if (function_exists('wpgmza_register_pro_version')) {
            $trashlink = "| <a href=\"?page=wp-google-maps-menu&action=trash&map_id=".$result->id."\" title=\"Trash\">".__("Trash","wp-google-maps")."</a>";
        } else {
            $trashlink = "";
        }
        echo "<tr id=\"record_".$result->id."\">";
        echo "<td class='id column-id'>".$result->id."</td>";
        echo "<td class='map_title column-map_title'><strong><big><a href=\"?page=wp-google-maps-menu&action=edit&map_id=".$result->id."\" title=\"".__("Edit","wp-google-maps")."\">".stripslashes($result->map_title)."</a></big></strong><br /><a href=\"?page=wp-google-maps-menu&action=edit&map_id=".$result->id."\" title=\"".__("Edit","wp-google-maps")."\">".__("Edit","wp-google-maps")."</a> $trashlink</td>";
        echo "<td class='map_width column-map_width'>".$result->map_width."".stripslashes($result->map_width_type)."</td>";
        echo "<td class='map_width column-map_height'>".$result->map_height."".stripslashes($result->map_height_type)."</td>";
        echo "<td class='type column-type'>".$map_type."</td>";
        echo "</tr>";


    }
    echo "</table>";
}




function wpgmza_marker_page() {
    echo"<div class=\"wrap\"><div id=\"icon-edit\" class=\"icon32 icon32-posts-post\"><br></div><h2>".__("My Markers","wp-google-maps")." <a href=\"admin.php?page=wp-google-maps-marker-menu&action=new\" class=\"add-new-h2\">".__("Add New","wp-google-maps")."</a></h2>";
    wpgmaps_list_markers();
    echo "</div>";
    echo"<br /><div style='float:right;'><a href='http://www.wpgmaps.com/documentation/troubleshooting/' title='WP Google Maps Troubleshooting Section'>".__("Problems with the plugin? See the troubleshooting manual.","wp-google-maps")."</a></div>";

}

function wpgmaps_list_markers() {
    global $wpdb;
    global $wpgmza_tblname;

    $results = $wpdb->get_results(
        "
	SELECT *
	FROM $wpgmza_tblname
        ORDER BY `address` DESC
	"
    );
    echo "

      <table class=\"wp-list-table widefat fixed \" cellspacing=\"0\">
	<thead>
	<tr>
		<th scope='col' id='marker_id' class='manage-column column-id sortable desc'  style=''><span>".__("ID","wp-google-maps")."</span></th>
                <th scope='col' id='marker_icon' class='manage-column column-map_title sortable desc'  style=''><span>".__("Icon","wp-google-maps")."</span></th>
                <th scope='col' id='marker_linked_to' class='manage-column column-map_title sortable desc'  style=''><span>".__("Linked to","wp-google-maps")."</span></th>
                <th scope='col' id='marker_title' class='manage-column column-map_width' style=\"\">".__("Title","wp-google-maps")."</th>
                <th scope='col' id='marker_address' class='manage-column column-map_width' style=\"\">".__("Address","wp-google-maps")."</th>
                <th scope='col' id='marker_gps' class='manage-column column-map_height'  style=\"\">".__("GPS","wp-google-maps")."</th>
                <th scope='col' id='marker_pic' class='manage-column column-type sortable desc'  style=\"\"><span>".__("Pic","wp-google-maps")."</span></th>
                <th scope='col' id='marker_link' class='manage-column column-type sortable desc'  style=\"\"><span>".__("Link","wp-google-maps")."</span></th>
        </tr>
	</thead>
        <tbody id=\"the-list\" class='list:wp_list_text_link'>
";
    foreach ( $results as $result ) {
        echo "<tr id=\"record_".$result->id."\">";
        echo "<td class='id column-id'>".$result->id."</td>";
        echo "<td class='id column-id'>".$result->icon."</td>";
        echo "<td class='id column-id'>".$result->map_id."</td>";
        echo "<td class='id column-id'>".$result->title."</td>";
        echo "<td class='id column-id'>".$result->address."</td>";
        echo "<td class='id column-id'>".$result->lat.",".$result->lng."</td>";
        echo "<td class='id column-id'>".$result->pic."</td>";
        echo "<td class='id column-id'>".$result->link."</td>";
        echo "</tr>";


    }
    echo "</table>";

}



function wpgmaps_check_versions() {
    $prov = get_option("WPGMZA_PRO");
    $wpgmza_pro_version = $prov['version'];
    if (floatval($wpgmza_pro_version) < 4.51 || $wpgmza_pro_version == null) {
        wpgmaps_upgrade_notice();
    }
}

function wpgmza_basic_menu() {
    
    
    global $wpgmza_tblname_maps;
    global $wpdb;
    /* deprecated
    *  if (!wpgmaps_check_permissions()) { wpgmaps_permission_warning(); }
    */
    if ($_GET['action'] == "edit" && isset($_GET['map_id'])) {
        $res = wpgmza_get_map_data(sanitize_text_field($_GET['map_id']));
        if (function_exists("wpgmaps_marker_permission_check")) { wpgmaps_marker_permission_check(); }

        
        $other_settings_data = maybe_unserialize($res->other_settings);
        if (isset($other_settings_data['store_locator_enabled'])) { $wpgmza_store_locator_enabled = $other_settings_data['store_locator_enabled']; } else { $wpgmza_store_locator_enabled = 0; }
        if (isset($other_settings_data['store_locator_distance'])) { $wpgmza_store_locator_distance = $other_settings_data['store_locator_distance']; } else { $wpgmza_store_locator_distance = 0; }
        if (isset($other_settings_data['store_locator_bounce'])) { $wpgmza_store_locator_bounce = $other_settings_data['store_locator_bounce']; } else { $wpgmza_store_locator_bounce = 1; }
        if (isset($other_settings_data['store_locator_query_string'])) { $wpgmza_store_locator_query_string = stripslashes($other_settings_data['store_locator_query_string']); } else { $wpgmza_store_locator_query_string = __("ZIP / Address:","wp-google-maps"); }
        if (isset($other_settings_data['wpgmza_store_locator_restrict'])) { $wpgmza_store_locator_restrict = $other_settings_data['wpgmza_store_locator_restrict']; } else { $wpgmza_store_locator_restrict = ""; }

        /* deprecated in 6.2.0
        if (isset($other_settings_data['weather_layer'])) { $wpgmza_weather_option = $other_settings_data['weather_layer']; } else { $wpgmza_weather_option = 2; } 
        if (isset($other_settings_data['weather_layer_temp_type'])) { $wpgmza_weather_option_temp_type = $other_settings_data['weather_layer_temp_type']; } else { $wpgmza_weather_option_temp_type = 1; } 
        if (isset($other_settings_data['cloud_layer'])) { $wpgmza_cloud_option = $other_settings_data['cloud_layer']; } else { $wpgmza_cloud_option = 2; } 
        */
        if (isset($other_settings_data['transport_layer'])) { $wpgmza_transport_option = $other_settings_data['transport_layer']; } else { $wpgmza_transport_option = 2; } 
        
        if (isset($other_settings_data['map_max_zoom'])) { $wpgmza_max_zoom[intval($other_settings_data['map_max_zoom'])] = "SELECTED"; } else { $wpgmza_max_zoom[1] = "SELECTED";  }
        
        
        if (isset($res->map_start_zoom)) { $wpgmza_zoom[intval($res->map_start_zoom)] = "SELECTED"; } else { $wpgmza_zoom[8] = "SELECTED";  }
        if (isset($res->type)) { $wpgmza_map_type[intval($res->type)] = "SELECTED"; } else { $wpgmza_map_type[1] = "SELECTED"; }
        if (isset($res->alignment)) { $wpgmza_map_align[intval($res->alignment)] = "SELECTED"; } else { $wpgmza_map_align[1] = "SELECTED"; }
        if (isset($res->bicycle)) { $wpgmza_bicycle[intval($res->bicycle)] = "SELECTED"; } else { $wpgmza_bicycle[2] = "SELECTED"; }
        if (isset($res->traffic)) { $wpgmza_traffic[intval($res->traffic)] = "SELECTED"; } else { $wpgmza_traffic[2] = "SELECTED"; }

        if (stripslashes($res->map_width_type) == "%") { $wpgmza_map_width_type_percentage = "SELECTED"; $wpgmza_map_width_type_px = ""; } else { $wpgmza_map_width_type_px = "SELECTED"; $wpgmza_map_width_type_percentage = ""; }
        if (stripslashes($res->map_height_type) == "%") { $wpgmza_map_height_type_percentage = "SELECTED"; $wpgmza_map_height_type_px = ""; } else { $wpgmza_map_height_type_px = "SELECTED"; $wpgmza_map_height_type_percentage = ""; }

        for ($i=0;$i<22;$i++) {
            if (!isset($wpgmza_zoom[$i])) { $wpgmza_zoom[$i] = ""; }
        }
        for ($i=0;$i<22;$i++) {
            if (!isset($wpgmza_max_zoom[$i])) { $wpgmza_max_zoom[$i] = ""; }
        }
        for ($i=0;$i<5;$i++) {
            if (!isset($wpgmza_map_type[$i])) { $wpgmza_map_type[$i] = ""; }
        }
        for ($i=0;$i<5;$i++) {
            if (!isset($wpgmza_map_align[$i])) { $wpgmza_map_align[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_bicycle[$i])) { $wpgmza_bicycle[$i] = ""; }
        }
        for ($i=0;$i<3;$i++) {
            if (!isset($wpgmza_traffic[$i])) { $wpgmza_traffic[$i] = ""; }
        }
        
        
        
        $wpgmza_store_locator_enabled_checked[0] = '';
        $wpgmza_store_locator_enabled_checked[1] = '';
        $wpgmza_store_locator_distance_checked[0] = '';
        $wpgmza_store_locator_distance_checked[1] = '';
        $wpgmza_store_locator_bounce_checked[0] = '';
        $wpgmza_store_locator_bounce_checked[1] = '';
        
        if ($wpgmza_store_locator_enabled == 1) {
            $wpgmza_store_locator_enabled_checked[0] = 'selected';
        } else {
            $wpgmza_store_locator_enabled_checked[1] = 'selected';
        }
        if ($wpgmza_store_locator_distance == 1) {
            $wpgmza_store_locator_distance_checked[0] = 'selected';
        } else {
            $wpgmza_store_locator_distance_checked[1] = 'selected';
        }
        if ($wpgmza_store_locator_bounce == 1) {
            $wpgmza_store_locator_bounce_checked[0] = 'selected';
        } else {
            $wpgmza_store_locator_bounce_checked[1] = 'selected';
        }

        /*
        $wpgmza_weather_layer_checked[0] = '';
        $wpgmza_weather_layer_checked[1] = '';
        $wpgmza_weather_layer_temp_type_checked[0] = '';
        $wpgmza_weather_layer_temp_type_checked[1] = '';
        $wpgmza_cloud_layer_checked[0] = '';
        $wpgmza_cloud_layer_checked[1] = '';
        */
        $wpgmza_transport_layer_checked[0] = '';
        $wpgmza_transport_layer_checked[1] = '';
        

        /*        
        if ($wpgmza_weather_option == 1) {
            $wpgmza_weather_layer_checked[0] = 'selected';
        } else {
            $wpgmza_weather_layer_checked[1] = 'selected';
        }
        if ($wpgmza_weather_option_temp_type == 1) {
            $wpgmza_weather_layer_temp_type_checked[0] = 'selected';
        } else {
            $wpgmza_weather_layer_temp_type_checked[1] = 'selected';
        }
        if ($wpgmza_cloud_option == 1) {
            $wpgmza_cloud_layer_checked[0] = 'selected';
        } else {
            $wpgmza_cloud_layer_checked[1] = 'selected';
        }
        */
        if ($wpgmza_transport_option == 1) {
            $wpgmza_transport_layer_checked[0] = 'selected';
        } else {
            $wpgmza_transport_layer_checked[1] = 'selected';
        }

        $wpgmza_act = "disabled readonly";
        $wpgmza_act_msg = "<div class=\"update-nag\" style=\"padding:5px; \">".__("Add custom icons, titles, descriptions, pictures and links to your markers with the","wp-google-maps")." \"<a href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=below_marker\" title=\"".__("Pro Edition","wp-google-maps")."\" target=\"_BLANK\">".__("Pro Edition","wp-google-maps")."</a>\" ".__("of this plugin for just","wp-google-maps")." <strong>$39.99</strong></div>";
        $wpgmza_csv = "<p><a href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=csv_link\" target=\"_BLANK\" title=\"".__("Pro Edition","wp-google-maps")."\">".__("Purchase the Pro Edition","wp-google-maps")."</a> ".__("of WP Google Maps and save your markers to a CSV file!","wp-google-maps")."</p>";
    }
    

    if (isset($other_settings_data['wpgmza_theme_selection'])) { $theme_sel_checked[$other_settings_data['wpgmza_theme_selection']] = "checked"; $wpgmza_theme_class[$other_settings_data['wpgmza_theme_selection']] = "wpgmza_theme_selection_activate"; } else {  $wpgmza_theme = false; $wpgmza_theme_class[0] = "wpgmza_theme_selection_activate"; }
    for ($i=0;$i<9;$i++) {
        if (!isset($wpgmza_theme_class[$i])) { $wpgmza_theme_class[$i] = ""; }
    }
    for ($i=0;$i<9;$i++) {
        if (!isset($theme_sel_checked[$i])) { $theme_sel_checked[$i] = ""; }
    }   
        
    /* check if they are using W3 Total Cache and that wp-google-maps appears in the rejected files list */
    if (class_exists("W3_Plugin_TotalCache")) {
        $wpgmza_w3_check = new W3_Plugin_TotalCache;
        if (function_exists("w3_instance")) {
            $modules = w3_instance('W3_ModuleStatus');
            $cdn_check = $modules->is_enabled('cdn');
            if (strpos(esc_textarea(implode("\r\n", $wpgmza_w3_check->_config->get_array('cdn.reject.files'))),'wp-google-maps') !== false) {
                $does_cdn_contain_our_plugin = true;
            } else { $does_cdn_contain_our_plugin = false; }



            if ($cdn_check == 1 && !$does_cdn_contain_our_plugin) {
                echo "<div class=\"update-nag\" style=\"padding:5px; \"><h1>".__("Please note","wp-google-maps").":</h1>".__("We've noticed that you are using W3 Total Cache and that you have CDN enabled.<br /><br />In order for the markers to show up on your map, you need to add '<strong><em>{uploads_dir}/wp-google-maps*</strong></em>' to the '<strong>rejected files</strong>' list in the <a href='admin.php?page=w3tc_cdn#advanced'>CDN settings page</a> of W3 Total Cache","wp-google-maps")."</div>";
            }
        }
        
        
    }


    wpgmza_stats("dashboard");

    if( isset( $other_settings_data['wpgmza_theme_data'] ) ){
        $wpgmza_theme_data_custom = $other_settings_data['wpgmza_theme_data'];
    } else {
        $wpgmza_theme_data_custom  = '';
    }
    

    
    echo "

           <div class='wrap'>
                <h1>WP Google Maps</h1>
                <div class='wide'>



                    <h2>".__("Map Settings","wp-google-maps")."</h2>
                    <form action='' method='post' id='wpgmaps_options'>
                    <p></p>
                    <div id=\"wpgmaps_tabs\">
                        <ul>
                            <li><a href=\"#tabs-1\">".__("General Settings","wp-google-maps")."</a></li>
                            <li><a href=\"#tabs-7\">".__("Themes","wp-google-maps")."</a></li>
                            <li><a href=\"#tabs-2\">".__("Directions","wp-google-maps")."</a></li>
                            <li><a href=\"#tabs-3\">".__("Store Locator","wp-google-maps")."</a></li>
                            <li><a href=\"#tabs-4\">".__("Advanced Settings","wp-google-maps")."</a></li>
                            <li><a href=\"#tabs-5\">".__("Marker Listing Options","wp-google-maps")."</a></li>
                            <li style=\"background-color: #d7e6f2; font-weight: bold;\"><a href=\"#tabs-6\">".__("Pro Upgrade","wp-google-maps")."</a></li>
                        </ul>
                        <div id=\"tabs-1\">
                            <p></p>
                            <input type='hidden' name='http_referer' value='".$_SERVER['PHP_SELF']."' />
                            <input type='hidden' name='wpgmza_id' id='wpgmza_id' value='".$res->id."' />
                            <input id='wpgmza_start_location' name='wpgmza_start_location' type='hidden' size='40' maxlength='100' value='".$res->map_start_location."' />
                            <select id='wpgmza_start_zoom' name='wpgmza_start_zoom' style='display:none;' >
                                        <option value=\"1\" ".$wpgmza_zoom[1].">1</option>
                                        <option value=\"2\" ".$wpgmza_zoom[2].">2</option>
                                        <option value=\"3\" ".$wpgmza_zoom[3].">3</option>
                                        <option value=\"4\" ".$wpgmza_zoom[4].">4</option>
                                        <option value=\"5\" ".$wpgmza_zoom[5].">5</option>
                                        <option value=\"6\" ".$wpgmza_zoom[6].">6</option>
                                        <option value=\"7\" ".$wpgmza_zoom[7].">7</option>
                                        <option value=\"8\" ".$wpgmza_zoom[8].">8</option>
                                        <option value=\"9\" ".$wpgmza_zoom[9].">9</option>
                                        <option value=\"10\" ".$wpgmza_zoom[10].">10</option>
                                        <option value=\"11\" ".$wpgmza_zoom[11].">11</option>
                                        <option value=\"12\" ".$wpgmza_zoom[12].">12</option>
                                        <option value=\"13\" ".$wpgmza_zoom[13].">13</option>
                                        <option value=\"14\" ".$wpgmza_zoom[14].">14</option>
                                        <option value=\"15\" ".$wpgmza_zoom[15].">15</option>
                                        <option value=\"16\" ".$wpgmza_zoom[16].">16</option>
                                        <option value=\"17\" ".$wpgmza_zoom[17].">17</option>
                                        <option value=\"18\" ".$wpgmza_zoom[18].">18</option>
                                        <option value=\"19\" ".$wpgmza_zoom[19].">19</option>
                                        <option value=\"20\" ".$wpgmza_zoom[20].">20</option>
                                        <option value=\"21\" ".$wpgmza_zoom[21].">21</option>
                                    </select>
                            <table>
                                <tr>
                                    <td>".__("Short code","wp-google-maps").":</td>
                                    <td><input type='text' readonly name='shortcode' style='font-size:18px; text-align:center;' onclick=\"this.select()\" value='[wpgmza id=\"".$res->id."\"]' /> <small><i>".__("copy this into your post or page to display the map","wp-google-maps")."</i></td>
                                </tr>
                                <tr>
                                    <td>".__("Map Name","wp-google-maps").":</td>
                                    <td><input id='wpgmza_title' name='wpgmza_title' type='text' size='20' maxlength='50' value='".stripslashes($res->map_title)."' /></td>
                                </tr>
                                <tr>
                                     <td>".__("Width","wp-google-maps").":</td>
                                     <td>
                                     <input id='wpgmza_width' name='wpgmza_width' type='text' size='4' maxlength='4' value='".$res->map_width."' />
                                     <select id='wpgmza_map_width_type' name='wpgmza_map_width_type'>
                                        <option value=\"px\" $wpgmza_map_width_type_px>px</option>
                                        <option value=\"%\" $wpgmza_map_width_type_percentage>%</option>
                                     </select>
                                     <small><em>".__("Set to 100% for a responsive map","wp-google-maps")."</em></small>

                                    </td>
                                </tr>
                                <tr>
                                    <td>".__("Height","wp-google-maps").":</td>
                                    <td><input id='wpgmza_height' name='wpgmza_height' type='text' size='4' maxlength='4' value='".$res->map_height."' />
                                     <select id='wpgmza_map_height_type' name='wpgmza_map_height_type'>
                                        <option value=\"px\" $wpgmza_map_height_type_px>px</option>
                                        <option value=\"%\" $wpgmza_map_height_type_percentage>%</option>
                                     </select><span style='display:none; width:200px; font-size:10px;' id='wpgmza_height_warning'>".__("We recommend that you leave your height in PX. Depending on your theme, using % for the height may break your map.","wp-google-maps")."</span>

                                    </td>
                                </tr>
                                <tr>
                                    <td>".__("Zoom Level","wp-google-maps").":</td>
                                    <td>
                                    <input type=\"text\" id=\"amount\" style=\"display:none;\"  value=\"$res->map_start_zoom\"><div id=\"slider-range-max\"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>".__("Map Alignment","wp-google-maps").":</td>
                                    <td><select id='wpgmza_map_align' name='wpgmza_map_align'>
                                        <option value=\"1\" ".$wpgmza_map_align[1].">".__("Left","wp-google-maps")."</option>
                                        <option value=\"2\" ".$wpgmza_map_align[2].">".__("Center","wp-google-maps")."</option>
                                        <option value=\"3\" ".$wpgmza_map_align[3].">".__("Right","wp-google-maps")."</option>
                                        <option value=\"4\" ".$wpgmza_map_align[4].">".__("None","wp-google-maps")."</option>
                                    </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>".__("Map type","wp-google-maps").":</td>
                                    <td><select id='wpgmza_map_type' name='wpgmza_map_type'>
                                        <option value=\"1\" ".$wpgmza_map_type[1].">".__("Roadmap","wp-google-maps")."</option>
                                        <option value=\"2\" ".$wpgmza_map_type[2].">".__("Satellite","wp-google-maps")."</option>
                                        <option value=\"3\" ".$wpgmza_map_type[3].">".__("Hybrid","wp-google-maps")."</option>
                                        <option value=\"4\" ".$wpgmza_map_type[4].">".__("Terrain","wp-google-maps")."</option>
                                    </select>
                                    </td>
                                </tr>

                                </table>
                        </div>
                        <div id=\"tabs-7\">
                            <h4>".__("Select a theme for your map","wp-google-maps")."</h4>
                            <table class='' id='wpgmaps_theme_table'>

                                <tr>
                                    <td width='50%'>        
                                        <img src=\"".WPGMAPS_DIR."/images/theme_0.jpg\" title=\"Default\" id=\"wpgmza_theme_selection_0\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[0]."\" tid=\"0\">     
                                        <img src=\"".WPGMAPS_DIR."/images/theme_1.jpg\" title=\"Blue\" id=\"wpgmza_theme_selection_1\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[1]."\"  tid=\"1\">     
                                        <img src=\"".WPGMAPS_DIR."/images/theme_2.jpg\" title=\"Apple Maps\" id=\"wpgmza_theme_selection_2\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[2]."\"  tid=\"2\">     
                                        <img src=\"".WPGMAPS_DIR."/images/theme_3.jpg\" title=\"Grayscale\" id=\"wpgmza_theme_selection_3\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[3]."\"  tid=\"3\">     
                                        <img src=\"".WPGMAPS_DIR."/images/theme_4.jpg\" title=\"Pale\" id=\"wpgmza_theme_selection_4\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[4]."\"  tid=\"4\">     
                                        <img src=\"".WPGMAPS_DIR."/images/theme_5.jpg\" title=\"Red\" id=\"wpgmza_theme_selection_5\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[5]."\"  tid=\"5\">     
                                        <img src=\"".WPGMAPS_DIR."/images/theme_6.jpg\" title=\"Dark Grey\" id=\"wpgmza_theme_selection_6\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[6]."\"  tid=\"6\">     
                                        <img src=\"".WPGMAPS_DIR."/images/theme_7.jpg\" title=\"Monochrome\" id=\"wpgmza_theme_selection_7\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[7]."\"  tid=\"7\">     
                                        <img src=\"".WPGMAPS_DIR."/images/theme_8.jpg\" title=\"Old Fashioned\" id=\"wpgmza_theme_selection_8\" width=\"200\" class=\"wpgmza_theme_selection ".$wpgmza_theme_class[8]."\"  tid=\"8\">     
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_0\" value=\"0\" ".$theme_sel_checked[0]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_1\" value=\"1\" ".$theme_sel_checked[1]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_2\" value=\"2\" ".$theme_sel_checked[2]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_3\" value=\"3\" ".$theme_sel_checked[3]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_4\" value=\"4\" ".$theme_sel_checked[4]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_5\" value=\"5\" ".$theme_sel_checked[5]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_6\" value=\"6\" ".$theme_sel_checked[6]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_7\" value=\"7\" ".$theme_sel_checked[7]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <input type=\"radio\" name=\"wpgmza_theme\" id=\"rb_wpgmza_theme_8\" value=\"8\" ".$theme_sel_checked[8]." class=\"wpgmza_theme_radio wpgmza_hide_input\">
                                        <textarea name=\"wpgmza_theme_data_0\" id=\"rb_wpgmza_theme_data_0\" class=\"wpgmza_hide_input\">"."[ \"visibility\", \"invert_lightness\", \"color\", \"weight\", \"hue\", \"saturation\", \"lightness\", \"gamma\"]"."</textarea>
                                        <textarea name=\"wpgmza_theme_data_1\" id=\"rb_wpgmza_theme_data_1\" class=\"wpgmza_hide_input\">"."[{\"featureType\": \"administrative\",\"elementType\": \"labels.text.fill\",\"stylers\": [{\"color\": \"#444444\"}]},{\"featureType\": \"landscape\",\"elementType\": \"all\",\"stylers\": [{\"color\": \"#f2f2f2\"}]},{\"featureType\": \"poi\",\"elementType\": \"all\",\"stylers\": [{\"visibility\": \"off\"}]},{\"featureType\": \"road\",\"elementType\": \"all\",\"stylers\": [{\"saturation\": -100},{\"lightness\": 45}]},{\"featureType\": \"road.highway\",\"elementType\": \"all\",\"stylers\": [{\"visibility\": \"simplified\"}]},{\"featureType\": \"road.arterial\",\"elementType\": \"labels.icon\",\"stylers\": [{\"visibility\": \"off\"}]},{\"featureType\": \"transit\",\"elementType\": \"all\",\"stylers\": [{\"visibility\": \"off\"}]},{\"featureType\": \"water\",\"elementType\": \"all\",\"stylers\": [{\"color\": \"#46bcec\"},{\"visibility\": \"on\"}]}]"."</textarea>
                                        <textarea name=\"wpgmza_theme_data_2\" id=\"rb_wpgmza_theme_data_2\" class=\"wpgmza_hide_input\">"."[{\"featureType\":\"landscape.man_made\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#f7f1df\"}]},{\"featureType\":\"landscape.natural\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#d0e3b4\"}]},{\"featureType\":\"landscape.natural.terrain\",\"elementType\":\"geometry\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi.business\",\"elementType\":\"all\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi.medical\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#fbd3da\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#bde6ab\"}]},{\"featureType\":\"road\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#ffe15f\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#efd151\"}]},{\"featureType\":\"road.arterial\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#ffffff\"}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"black\"}]},{\"featureType\":\"transit.station.airport\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#cfb2db\"}]},{\"featureType\":\"water\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#a2daf2\"}]}]"."</textarea>
                                        <textarea name=\"wpgmza_theme_data_3\" id=\"rb_wpgmza_theme_data_3\" class=\"wpgmza_hide_input\">"."[{\"featureType\":\"landscape\",\"stylers\":[{\"saturation\":-100},{\"lightness\":65},{\"visibility\":\"on\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"saturation\":-100},{\"lightness\":51},{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.highway\",\"stylers\":[{\"saturation\":-100},{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.arterial\",\"stylers\":[{\"saturation\":-100},{\"lightness\":30},{\"visibility\":\"on\"}]},{\"featureType\":\"road.local\",\"stylers\":[{\"saturation\":-100},{\"lightness\":40},{\"visibility\":\"on\"}]},{\"featureType\":\"transit\",\"stylers\":[{\"saturation\":-100},{\"visibility\":\"simplified\"}]},{\"featureType\":\"administrative.province\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"water\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"on\"},{\"lightness\":-25},{\"saturation\":-100}]},{\"featureType\":\"water\",\"elementType\":\"geometry\",\"stylers\":[{\"hue\":\"#ffff00\"},{\"lightness\":-25},{\"saturation\":-97}]}]"."</textarea>
                                        <textarea name=\"wpgmza_theme_data_4\" id=\"rb_wpgmza_theme_data_4\" class=\"wpgmza_hide_input\">"."[{\"featureType\":\"administrative\",\"elementType\":\"all\",\"stylers\":[{\"visibility\":\"on\"},{\"lightness\":33}]},{\"featureType\":\"landscape\",\"elementType\":\"all\",\"stylers\":[{\"color\":\"#f2e5d4\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#c5dac6\"}]},{\"featureType\":\"poi.park\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"on\"},{\"lightness\":20}]},{\"featureType\":\"road\",\"elementType\":\"all\",\"stylers\":[{\"lightness\":20}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#c5c6c6\"}]},{\"featureType\":\"road.arterial\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#e4d7c6\"}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#fbfaf7\"}]},{\"featureType\":\"water\",\"elementType\":\"all\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#acbcc9\"}]}]"."</textarea>
                                        <textarea name=\"wpgmza_theme_data_5\" id=\"rb_wpgmza_theme_data_5\" class=\"wpgmza_hide_input\">[{\"stylers\": [ {\"hue\": \"#890000\"}, {\"visibility\": \"simplified\"}, {\"gamma\": 0.5}, {\"weight\": 0.5} ] }, { \"elementType\": \"labels\", \"stylers\": [{\"visibility\": \"off\"}] }, { \"featureType\": \"water\", \"stylers\": [{\"color\": \"#890000\"}] } ]</textarea>
                                        <textarea name=\"wpgmza_theme_data_6\" id=\"rb_wpgmza_theme_data_6\" class=\"wpgmza_hide_input\">[{\"featureType\":\"all\",\"elementType\":\"labels.text.fill\",\"stylers\":[{\"saturation\":36},{\"color\":\"#000000\"},{\"lightness\":40}]},{\"featureType\":\"all\",\"elementType\":\"labels.text.stroke\",\"stylers\":[{\"visibility\":\"on\"},{\"color\":\"#000000\"},{\"lightness\":16}]},{\"featureType\":\"all\",\"elementType\":\"labels.icon\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"administrative\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":20}]},{\"featureType\":\"administrative\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":17},{\"weight\":1.2}]},{\"featureType\":\"landscape\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":20}]},{\"featureType\":\"poi\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":21}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.fill\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":17}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry.stroke\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":29},{\"weight\":0.2}]},{\"featureType\":\"road.arterial\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":18}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":16}]},{\"featureType\":\"transit\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":19}]},{\"featureType\":\"water\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#000000\"},{\"lightness\":17}]}]</textarea>
                                        <textarea name=\"wpgmza_theme_data_7\" id=\"rb_wpgmza_theme_data_7\" class=\"wpgmza_hide_input\">[{\"featureType\":\"administrative.locality\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#2c2e33\"},{\"saturation\":7},{\"lightness\":19},{\"visibility\":\"on\"}]},{\"featureType\":\"landscape\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#ffffff\"},{\"saturation\":-100},{\"lightness\":100},{\"visibility\":\"simplified\"}]},{\"featureType\":\"poi\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#ffffff\"},{\"saturation\":-100},{\"lightness\":100},{\"visibility\":\"off\"}]},{\"featureType\":\"road\",\"elementType\":\"geometry\",\"stylers\":[{\"hue\":\"#bbc0c4\"},{\"saturation\":-93},{\"lightness\":31},{\"visibility\":\"simplified\"}]},{\"featureType\":\"road\",\"elementType\":\"labels\",\"stylers\":[{\"hue\":\"#bbc0c4\"},{\"saturation\":-93},{\"lightness\":31},{\"visibility\":\"on\"}]},{\"featureType\":\"road.arterial\",\"elementType\":\"labels\",\"stylers\":[{\"hue\":\"#bbc0c4\"},{\"saturation\":-93},{\"lightness\":-2},{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.local\",\"elementType\":\"geometry\",\"stylers\":[{\"hue\":\"#e9ebed\"},{\"saturation\":-90},{\"lightness\":-8},{\"visibility\":\"simplified\"}]},{\"featureType\":\"transit\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#e9ebed\"},{\"saturation\":10},{\"lightness\":69},{\"visibility\":\"on\"}]},{\"featureType\":\"water\",\"elementType\":\"all\",\"stylers\":[{\"hue\":\"#e9ebed\"},{\"saturation\":-78},{\"lightness\":67},{\"visibility\":\"simplified\"}]}]</textarea>
                                        <textarea name=\"wpgmza_theme_data_8\" id=\"rb_wpgmza_theme_data_8\" class=\"wpgmza_hide_input\">[{\"featureType\":\"administrative\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"poi\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"road\",\"elementType\":\"labels\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"water\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"transit\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"landscape\",\"stylers\":[{\"visibility\":\"simplified\"}]},{\"featureType\":\"road.highway\",\"stylers\":[{\"visibility\":\"off\"}]},{\"featureType\":\"road.local\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"road.highway\",\"elementType\":\"geometry\",\"stylers\":[{\"visibility\":\"on\"}]},{\"featureType\":\"water\",\"stylers\":[{\"color\":\"#84afa3\"},{\"lightness\":52}]},{\"stylers\":[{\"saturation\":-17},{\"gamma\":0.36}]},{\"featureType\":\"transit.line\",\"elementType\":\"geometry\",\"stylers\":[{\"color\":\"#3f518c\"}]}]</textarea>
                                    </td>
                                    <td width='50%'>
                                    <h3>".__("Or use a custom theme","wp-google-maps")."</h3>
                                    <p><a href='http://www.wpgmaps.com/map-themes/?utm_source=plugin&utm_medium=link&utm_campaign=browse_themes' title='' target='_BLANK' class='button button-primary'>".__("Browse the theme directory","wp-google-maps")."</a></p>
                                    <p>".__("Paste your custom theme data here:","wp-google-maps")."</p>
                                        <textarea name=\"wpgmza_styling_json\" id=\"wpgmza_styling_json\" rows=\"8\" cols=\"40\">".stripslashes($wpgmza_theme_data_custom)."</textarea>
                                    <p><a href='javascript:void(0);' title='".__("Preview","wp-google-maps")."' class='button button-seconday' id='wpgmza_preview_theme'>".__("Preview","wp-google-maps")."</a></p>
                                    </td>

                                </tr>
                                <tr>
                                <td>
                                    <p>

                                    </p>
                                </td>
                                </tr>
                            </table>

                        </div>

                        <div id=\"tabs-2\">
                            <div class=\"wpgm_notice_message\">
                                <ul>
                                    <li>
                                        <i class=\"fa fa-hand-o-right\"> </i> <a target='_BLANK' href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=directions\">Enable directions</a> with the Pro version for only $39.99 once off. Support and updates included forever.
                                    </li>
                                </ul>
                            </div>
                                        
                                        

                            <table class='form-table' id='wpgmaps_directions_options'>
                                <tr>
                                    <td width='200px'>".__("Enable Directions?","wp-google-maps").":</td>
                                    <td><select class='postform' readonly disabled>
                                        <option>".__("No","wp-google-maps")."</option>
                                        <option>".__("Yes","wp-google-maps")."</option>
                                    </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    ".__("Directions Box Open by Default?","wp-google-maps").":
                                    </td>
                                    <td>
                                    <select class='postform' readonly disabled>
                                        <option>".__("No","wp-google-maps")."</option>
                                        <option>".__("Yes, on the left","wp-google-maps")."</option>
                                        <option>".__("Yes, on the right","wp-google-maps")."</option>
                                        <option>".__("Yes, above","wp-google-maps")."</option>
                                        <option>".__("Yes, below","wp-google-maps")."</option>
                                    </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    ".__("Directions Box Width","wp-google-maps").":
                                    </td>
                                    <td>
                                    <input type='text' size='4' maxlength='4' class='small-text' readonly disabled /> px
                                    </td>
                                </tr>

                            </table>
                        </div><!-- end of tab2 -->
                        
                        <div id=\"tabs-3\">
                            
                            <table class='' id='wpgmaps_directions_options'>
                                <tr>
                                    <td width='200'>".__("Enable Store Locator","wp-google-maps").":</td>
                                    <td><select id='wpgmza_store_locator' name='wpgmza_store_locator' class='postform'>
                                            <option value=\"1\" ".$wpgmza_store_locator_enabled_checked[0].">".__("Yes","wp-google-maps")."</option>
                                            <option value=\"2\" ".$wpgmza_store_locator_enabled_checked[1].">".__("No","wp-google-maps")."</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td width='200'>".__("Restrict to country","wp-google-maps").":</td>
                                    <td><input type=\"text\" name=\"wpgmza_store_locator_restrict\" id=\"wpgmza_store_locator_restrict\" value=\"$wpgmza_store_locator_restrict\" style='width:110px;' placeholder='Country TLD'> <em>".__("Insert country TLD. For example, use DE for Germany.","wp-google-maps")." ".__("Leave blank for no restrictions.","wp-google-maps")."</em></td>
                                </tr>

                                <tr>
                                    <td>".__("Show distance in","wp-google-maps").":</td>
                                    <td><select id='wpgmza_store_locator_distance' name='wpgmza_store_locator_distance' class='postform'>
                                        <option value=\"1\" ".$wpgmza_store_locator_distance_checked[0].">".__("Miles","wp-google-maps")."</option>
                                        <option value=\"2\" ".$wpgmza_store_locator_distance_checked[1].">".__("Kilometers","wp-google-maps")."</option>
                                    </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>".__("Query string","wp-google-maps").":</td>
                                    <td><input type=\"text\" name=\"wpgmza_store_locator_query_string\" id=\"wpgmza_store_locator_query_string\" value=\"$wpgmza_store_locator_query_string\">
                                    </td>
                                </tr>
                                <tr>
                                    <td width='200'>".__("Show bouncing icon","wp-google-maps").":</td>
                                    <td><select id='wpgmza_store_locator_bounce' name='wpgmza_store_locator_bounce' class='postform'>
                                            <option value=\"1\" ".$wpgmza_store_locator_bounce_checked[0].">".__("Yes","wp-google-maps")."</option>
                                            <option value=\"2\" ".$wpgmza_store_locator_bounce_checked[1].">".__("No","wp-google-maps")."</option>
                                        </select>
                                    </td>
                                </tr>

                            </table>
                            <p><em>".__('View','wp-google-maps')." <a href='http://wpgmaps.com/documentation/store-locator' target='_BLANK'>".__('Store Locator Documentation','wp-google-maps')."</a></em></p>
                        </div><!-- end of tab3 -->

                        <div id=\"tabs-4\">

                        <table class='' id='wpgmaps_advanced_options'>
                        <tr>
                            <td width='320'>".__("Enable Bicycle Layer?","wp-google-maps").":</td>
                            <td><select id='wpgmza_bicycle' name='wpgmza_bicycle' class='postform'>
                                <option value=\"1\" ".$wpgmza_bicycle[1].">".__("Yes","wp-google-maps")."</option>
                                <option value=\"2\" ".$wpgmza_bicycle[2].">".__("No","wp-google-maps")."</option>
                            </select>
                            </td>
                        </tr>
                        <tr>
                        <td>".__("Enable Traffic Layer?","wp-google-maps").":</td>
                            <td><select id='wpgmza_traffic' name='wpgmza_traffic' class='postform'>
                                <option value=\"1\" ".$wpgmza_traffic[1].">".__("Yes","wp-google-maps")."</option>
                                <option value=\"2\" ".$wpgmza_traffic[2].">".__("No","wp-google-maps")."</option>
                            </select></td>
                        </tr>
                        
                        <tr>
                            <td width='320'>".__("Enable Public Transport Layer?","wp-google-maps").":</td>
                            <td><select id='wpgmza_transport' name='wpgmza_transport' class='postform'>
                                <option value=\"1\" ".$wpgmza_transport_layer_checked[0].">".__("Yes","wp-google-maps")."</option>
                                <option value=\"2\" ".$wpgmza_transport_layer_checked[1].">".__("No","wp-google-maps")."</option>
                            </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <td width='320'>".__("Maximum Zoom Level","wp-google-maps").":</td>
                            <td>
                                <select id='wpgmza_max_zoom' name='wpgmza_max_zoom' >
                                    <option value=\"1\" ".$wpgmza_max_zoom[1].">1</option>
                                    <option value=\"2\" ".$wpgmza_max_zoom[2].">2</option>
                                    <option value=\"3\" ".$wpgmza_max_zoom[3].">3</option>
                                    <option value=\"4\" ".$wpgmza_max_zoom[4].">4</option>
                                    <option value=\"5\" ".$wpgmza_max_zoom[5].">5</option>
                                    <option value=\"6\" ".$wpgmza_max_zoom[6].">6</option>
                                    <option value=\"7\" ".$wpgmza_max_zoom[7].">7</option>
                                    <option value=\"8\" ".$wpgmza_max_zoom[8].">8</option>
                                    <option value=\"9\" ".$wpgmza_max_zoom[9].">9</option>
                                    <option value=\"10\" ".$wpgmza_max_zoom[10].">10</option>
                                    <option value=\"11\" ".$wpgmza_max_zoom[11].">11</option>
                                    <option value=\"12\" ".$wpgmza_max_zoom[12].">12</option>
                                    <option value=\"13\" ".$wpgmza_max_zoom[13].">13</option>
                                    <option value=\"14\" ".$wpgmza_max_zoom[14].">14</option>
                                    <option value=\"15\" ".$wpgmza_max_zoom[15].">15</option>
                                    <option value=\"16\" ".$wpgmza_max_zoom[16].">16</option>
                                    <option value=\"17\" ".$wpgmza_max_zoom[17].">17</option>
                                    <option value=\"18\" ".$wpgmza_max_zoom[18].">18</option>
                                    <option value=\"19\" ".$wpgmza_max_zoom[19].">19</option>
                                    <option value=\"20\" ".$wpgmza_max_zoom[20].">20</option>
                                    <option value=\"21\" ".$wpgmza_max_zoom[21].">21</option>
                                </select>
                            </td>
                        </tr>                        
                        
                    </table>

                            <div class=\"wpgm_notice_message\">
                                <ul>
                                    <li>
                                        ".__("Get the rest of these advanced features with the Pro version for only <a href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=advanced\">$39.99 once off</a>. Support and updates included forever.","wp-google-maps")."
                                    </li>
                                </ul>
                            </div>

                            <table class='form-table' id='wpgmaps_advanced_options'>
                                <tr>
                                    <td>".__("Default Marker Image","wp-google-maps").":</td>
                                    <td><input id=\"\" name=\"\" type='hidden' size='35' class='regular-text' maxlength='700' value='".$res->default_marker."' ".$wpgmza_act."/> <input id=\"upload_default_marker_btn\" type=\"button\" value=\"".__("Upload Image","wp-google-maps")."\" $wpgmza_act /> <a href=\"javascript:void(0);\" onClick=\"document.forms['wpgmza_map_form'].upload_default_marker.value = ''; var span = document.getElementById('wpgmza_mm'); while( span.firstChild ) { span.removeChild( span.firstChild ); } span.appendChild( document.createTextNode('')); return false;\" title=\"Reset to default\">-reset-</a></td>
                                </tr>

                                <tr>
                                    <td>".__("Show User's Location?","wp-google-maps").":</td>
                                    <td><select class='postform' readonly disabled>
                                        <option >".__("No","wp-google-maps")."</option>
                                        <option >".__("Yes","wp-google-maps")."</option>
                                    </select>
                                    </td>
                                </tr>
                                <tr>

                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>".__("KML/GeoRSS URL","wp-google-maps").":</td>
                                    <td>
                                     <input type='text' size='100' maxlength='700' class='regular-text' readonly disabled /> <em><small>".__("The KML/GeoRSS layer will over-ride most of your map settings","wp-google-maps")."</small></em></td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>".__("Fusion table ID","wp-google-maps").":</td>
                                    <td>
                                     <input type='text' size='20' maxlength='200' class='small-text' readonly disabled /> <em><small>".__("Read data directly from your Fusion Table.","wp-google-maps")."</small></em></td>
                                    </td>
                                </tr>
                            </table>
                        </div><!-- end of tab4 -->
                        <div id=\"tabs-5\" style=\"font-family:sans-serif;\">
                            <div class=\"wpgm_notice_message\">
                                <ul>
                                    <li>
                                        ".__("Enable Marker Listing with the <a href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=marker_listing\">Pro version for only $39.99 once off</a>. Support and updates included forever.","wp-google-maps")."
                                    </li>
                                </ul>
                            </div>

                            <table class='' id='wpgmaps_marker_listing_options'>
                                <tr>
                                     <td valign=\"top\">".__("List Markers","wp-google-maps").":</td>
                                     <td>

                                        <input type=\"radio\" disabled >".__("None","wp-google-maps")."<br />
                                        <input type=\"radio\" disabled >".__("Basic table","wp-google-maps")."<br />
                                        <input type=\"radio\" disabled >".__("Advanced table with real time search and filtering","wp-google-maps")."<br />
                                        <input type=\"radio\" disabled >".__("Carousel","wp-google-maps")." (".__("beta","wp-google-maps").")<br />


                                    </td>
                                </tr>
                                <tr>
                                     <td>".__("Filter by Category","wp-google-maps").":</td>
                                     <td>
                                        <input id='wpgmza_filterbycat' type='checkbox' disabled /> ".__("Allow users to filter by category?","wp-google-maps")."

                                    </td>
                                </tr>
                                <tr>
                                     <td>".__("Order markers by","wp-google-maps").":</td>
                                     <td>
                                        <select disabled class='postform'>
                                            <option >".__("ID","wp-google-maps")."</option>
                                            <option >".__("Title","wp-google-maps")."</option>
                                            <option >".__("Address","wp-google-maps")."</option>
                                            <option >".__("Description","wp-google-maps")."</option>
                                            <option >".__("Category","wp-google-maps")."</option>
                                        </select>
                                        <select disabled class='postform'>
                                            <option >".__("Ascending","wp-google-maps")."</option>
                                            <option >".__("Descending","wp-google-maps")."</option>
                                        </select>

                                    </td>
                                </tr>

                                <tr style='height:20px;'>
                                     <td></td>
                                     <td></td>
                                </tr>

                                <tr>
                                     <td valign='top'>".__("Move list inside map","wp-google-maps").":</td>
                                     <td>
                                        <input disabled type='checkbox' value='1' /> ".__("Move your marker list inside the map area","wp-google-maps")."<br />

                                        ".__("Placement: ","wp-google-maps")."
                                        <select readonly disabled id='wpgmza_push_in_map_placement' name='wpgmza_push_in_map_placement' class='postform'>
                                            <option>".__("Top Center","wp-google-maps")."</option>
                                            <option>".__("Top Left","wp-google-maps")."</option>
                                            <option>".__("Top Right","wp-google-maps")."</option>
                                            <option>".__("Left Top ","wp-google-maps")."</option>
                                            <option>".__("Right Top","wp-google-maps")."</option>
                                            <option>".__("Left Center","wp-google-maps")."</option>
                                            <option>".__("Right Center","wp-google-maps")."</option>
                                            <option>".__("Left Bottom","wp-google-maps")."</option>
                                            <option>".__("Right Bottom","wp-google-maps")."</option>
                                            <option>".__("Bottom Center","wp-google-maps")."</option>
                                            <option>".__("Bottom Left","wp-google-maps")."</option>
                                            <option>".__("Bottom Right","wp-google-maps")."</option>
                                        </select> <br />
                                    </td>
                                </tr>
                                <tr style='height:20px;'>
                                     <td></td>
                                     <td></td>
                                </tr>                                

                            </table>
                            <div class=\"about-wrap\">
                            <div class=\"feature-section three-col\">
                                <div class=\"col\">
                                <h4>".__("Basic","wp-google-maps")."</h4>
                                <p style='display:block; height:40px;'>".__("Show a basic list of your markers","wp-google-maps")."</p>
                                 <img src='".WPGMAPS_DIR."base/assets/marker-listing-basic.jpg' style=\"border:1px solid #ccc;\" />              
                                </div>
                                <div class=\"col\">
                                <h4>".__("Carousel","wp-google-maps")."</h4>
                                <p style='display:block; height:40px;'>".__("Beautiful, responsive, mobile-friendly carousel marker listing","wp-google-maps")."</p>
                                 <img src='".WPGMAPS_DIR."base/assets/marker-listing-carousel.jpg' style=\"border:1px solid #ccc;\" />              
                                </div>
                                <div class=\"col\">
                                <h4>".__("Tabular","wp-google-maps")."</h4>
                                <p style='display:block; height:40px;'>".__("Advanced, tabular marker listing functionality with real time filtering","wp-google-maps")."</p>
                                 <img src='".WPGMAPS_DIR."base/assets/marker-listing-advanced.jpg' style=\"border:1px solid #ccc;\" />              
                                </div>
                            </div>
                            </div>

                        </div>
                        <div id=\"tabs-6\" style=\"font-family:sans-serif;\">
                            <h1 style=\"font-weight:200;\">12 Amazing Reasons to Upgrade to our Pro Version</h1>
                            <p style=\"font-size:16px; line-height:28px;\">We've spent over two years upgrading our plugin to ensure that it is the most user-friendly and comprehensive map plugin in the WordPress directory. Enjoy the peace of mind knowing that you are getting a truly premium product for all your mapping requirements. Did we also mention that we have fantastic support?</p>
                            <div id=\"wpgm_premium\">
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Create custom markers with detailed info windows</h2>
                                        <p>Add titles, descriptions, HTML, images, animations and custom icons to your markers.</p>
                                    </div>
                                </div>
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Enable directions</h2>
                                        <p>Allow your visitors to get directions to your markers. Either use their location as the starting point or allow them to type in an address.</p>
                                    </div>
                                </div>
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Unlimited maps</h2>
                                        <p>Create as many maps as you like.</p>
                                    </div>
                                </div>
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>List your markers</h2>
                                        <p>Choose between three methods of listing your markers.</p>
                                    </div>
                                </div>                                
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Add categories to your markers</h2>
                                        <p>Create and assign categories to your markers which can then be filtered on your map.</p>
                                    </div>
                                </div>                                
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Advanced options</h2>
                                        <p>Enable advanced options such as showing your visitor's location, marker sorting, bicycle layers, traffic layers and more!</p>
                                    </div>
                                </div>  
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Import / Export</h2>
                                        <p>Export your markers to a CSV file for quick and easy editing. Import large quantities of markers at once.</p>
                                    </div>
                                </div>                                
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Add KML & Fusion Tables</h2>
                                        <p>Add your own KML layers or Fusion Table data to your map</p>
                                    </div>
                                </div>                                   
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Polygons and Polylines</h2>
                                        <p>Add custom polygons and polylines to your map by simply clicking on the map. Perfect for displaying routes and serviced areas.</p>
                                    </div>
                                </div>
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Amazing Support</h2>
                                        <p>We pride ourselves on providing quick and amazing support. <a target=\"_BLANK\" href=\"http://wordpress.org/support/view/plugin-reviews/wp-google-maps?filter=5\">Read what some of our users think of our support</a>.</p>
                                    </div>
                                </div>
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Easy Upgrade</h2>
                                        <p>You'll receive a download link immediately. Simply upload and activate the Pro plugin to your WordPress admin area and you're done!</p>
                                    </div>
                                </div>                                  
                                <div class=\"wpgm_premium_row\">
                                    <div class=\"wpgm_icon\"></div>
                                    <div class=\"wpgm_details\">
                                        <h2>Free updates and support forever</h2>
                                        <p>Once you're a pro user, you'll receive free updates and support forever! You'll also receive amazing specials on any future plugins we release.</p>
                                    </div>
                                </div>              
                                
                                <br /><p>Get all of this and more for only $39.99 once off</p>                                
                                <br /><a href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=upgradenow\" target=\"_BLANK\" title=\"Upgrade now for only $39.99 once off\" class=\"button-primary\" style=\"font-size:20px; display:block; width:150px; text-align:center; height:30px; line-height:26px;\">Upgrade Now</a>
                                <br /><br />
                                <a href=\"http://www.wpgmaps.com/demo/\" target=\"_BLANK\">View the demos</a>.<br /><br />
                                Have a sales question? Contact either Nick or Jarryd on <a href=\"mailto:nick@wpgmaps.com\">nick@wpgmaps.com</a> or use our <a href=\"http://www.wpgmaps.com/contact-us/\" target=\"_BLANK\">contact form</a>. <br /><br />
                                Need help? <a href=\"http://www.wpgmaps.com/forums/forum/support-forum/\" target=\"_BLANK\">Ask a question on our support forum</a>.       
                                


                        </div><!-- end of tab5 -->   
                        
                        </div>
                        </div>
                    
                    <!-- end of tabs -->


                            
                            <p class='submit'><input type='submit' name='wpgmza_savemap' class='button-primary' value='".__("Save Map","wp-google-maps")." &raquo;' /></p>
                                
                            <p style=\"width:100%; color:#808080;\">
                                ".__("Tip: Use your mouse to change the layout of your map. When you have positioned the map to your desired location, press \"Save Map\" to keep your settings.","wp-google-maps")."</p>

                            <div style='display:block; overflow:auto; width:100%;'>
                            
                            <div style='display:block; width:49%; margin-right:1%; overflow:auto; float:left;'>
                                <div id=\"wpgmaps_tabs_markers\">
                                    <ul>
                                            <li><a href=\"#tabs-m-1\" class=\"tabs-m-1\">".__("Markers","wp-google-maps")."</a></li>
                                            <li><a href=\"#tabs-m-2\" class=\"tabs-m-1\">".__("Advanced markers","wp-google-maps")."</a></li>
                                            <li><a href=\"#tabs-m-3\" class=\"tabs-m-2\">".__("Polygon","wp-google-maps")."</a></li>
                                            <li><a href=\"#tabs-m-4\" class=\"tabs-m-3\">".__("Polylines","wp-google-maps")."</a></li>
                                    </ul>
                                    <div id=\"tabs-m-1\">


                                        <h2 style=\"padding-top:0; margin-top:0;\"><i class=\"fa fa-map-marker\"> </i> ".__("Markers","wp-google-maps")."</h2>
                                        <table>
                                            <input type=\"hidden\" name=\"wpgmza_edit_id\" id=\"wpgmza_edit_id\" value=\"\" />
                                            <tr>
                                                <td valign='top'>".__("Address/GPS","wp-google-maps").": </td>
                                                <td><input id='wpgmza_add_address' name='wpgmza_add_address' type='text' size='35' maxlength='200' value=''  /> <br /><small><em>".__("Or right click on the map","wp-google-maps")."</small></em><br /><br /></td>

                                            </tr>

                                            <tr>
                                                <td>".__("Animation","wp-google-maps").": </td>
                                                <td>
                                                    <select name=\"wpgmza_animation\" id=\"wpgmza_animation\">
                                                        <option value=\"0\">".__("None","wp-google-maps")."</option>
                                                        <option value=\"1\">".__("Bounce","wp-google-maps")."</option>
                                                        <option value=\"2\">".__("Drop","wp-google-maps")."</option>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td>".__("InfoWindow open by default","wp-google-maps").": </td>
                                                <td>
                                                    <select name=\"wpgmza_infoopen\" id=\"wpgmza_infoopen\">
                                                        <option value=\"0\">".__("No","wp-google-maps")."</option>
                                                        <option value=\"1\">".__("Yes","wp-google-maps")."</option>
                                                </td>
                                            </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <span id=\"wpgmza_addmarker_div\"><input type=\"button\" class='button-primary' id='wpgmza_addmarker' value='".__("Add Marker","wp-google-maps")."' /></span> <span id=\"wpgmza_addmarker_loading\" style=\"display:none;\">".__("Adding","wp-google-maps")."...</span>
                                                <span id=\"wpgmza_editmarker_div\" style=\"display:none;\"><input type=\"button\" id='wpgmza_editmarker'  class='button-primary' value='".__("Save Marker","wp-google-maps")."' /></span><span id=\"wpgmza_editmarker_loading\" style=\"display:none;\">".__("Saving","wp-google-maps")."...</span>
                                                    <div id=\"wpgm_notice_message_save_marker\" style=\"display:none;\">
                                                        <div class=\"wpgm_notice_message\" style='text-align:left; padding:1px; margin:1px;'>
                                                                 <h4 style='padding:1px; margin:1px;'>".__("Remember to save your marker","wp-google-maps")."</h4>
                                                        </div>

                                                    </div>
                                                    <div id=\"wpgm_notice_message_addfirst_marker\" style=\"display:none;\">
                                                        <div class=\"wpgm_notice_message\" style='text-align:left; padding:1px; margin:1px;'>
                                                                 <h4 style='padding:1px; margin:1px;'>".__("Please add the current marker before trying to add another marker","wp-google-maps")."</h4>
                                                        </div>

                                                    </div>
                                            </td>

                                        </tr>

                                        </table>
                                    </div>

                                    <div id=\"tabs-m-2\">
                                        <h2 style=\"padding-top:0; margin-top:0;\"><i class=\"fa fa-map-marker\"> </i> ".__("Advanced markers","wp-google-maps")."</h2>
                                        <div class=\"wpgm_notice_message\">
                                            <ul>
                                                <li>
                                                    <i class=\"fa fa-hand-o-right\"> </i> <a target=\"_BLANK\" href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=advanced_markers\">".__("Add advanced markers","wp-google-maps")."</a> ".__("with the Pro version","wp-google-maps")."
                                                </li>
                                            </ul>
                                        </div>
                                        <table>
                                        <tr>
                                            <td>".__("Address/GPS","wp-google-maps").": </td>
                                            <td><input id='' name='' type='text' size='35' maxlength='200' value=''  $wpgmza_act /> &nbsp;<br /></td>

                                        </tr>

                                        <tr>
                                            <td>".__("Animation","wp-google-maps").": </td>
                                            <td>
                                                <select name=\"\" id=\"\">
                                                    <option value=\"0\">".__("None","wp-google-maps")."</option>
                                                    <option value=\"1\">".__("Bounce","wp-google-maps")."</option>
                                                    <option value=\"2\">".__("Drop","wp-google-maps")."</option>
                                            </td>
                                        </tr>


                                        <tr>
                                            <td>".__("InfoWindow open by default","wp-google-maps").": </td>
                                            <td>
                                                <select name=\"\" id=\"\">
                                                    <option value=\"0\">".__("No","wp-google-maps")."</option>
                                                    <option value=\"1\">".__("Yes","wp-google-maps")."</option>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>".__("Title","wp-google-maps").": </td>
                                            <td><input id='' name='' type='text' size='35' maxlength='200' value='' $wpgmza_act /></td>

                                        </tr>

                                        <tr><td>".__("Description","wp-google-maps").": </td>
                                            <td><textarea id='' name='' ".$wpgmza_act."  style='background-color:#EEE; width:272px;'></textarea>  &nbsp;<br /></td></tr>
                                        <tr><td>".__("Pic URL","wp-google-maps").": </td>
                                            <td><input id='' name=\"\" type='text' size='35' maxlength='700' value='' ".$wpgmza_act."/> <input id=\"\" type=\"button\" value=\"".__("Upload Image","wp-google-maps")."\" $wpgmza_act /><br /></td></tr>
                                        <tr><td>".__("Link URL","wp-google-maps").": </td>
                                            <td><input id='' name='' type='text' size='35' maxlength='700' value='' ".$wpgmza_act." /></td></tr>
                                        <tr><td>".__("Custom Marker","wp-google-maps").": </td>
                                            <td><input id='' name=\"\" type='hidden' size='35' maxlength='700' value='' ".$wpgmza_act."/> <input id=\"\" type=\"button\" value=\"".__("Upload Image","wp-google-maps")."\" $wpgmza_act /> &nbsp;</td></tr>
                                        <tr>
                                            <td>".__("Category","wp-google-maps").": </td>
                                            <td>
                                                <select readonly disabled>
                                                    <option value=\"0\">".__("Select","wp-google-maps")."</option>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input type=\"button\" class='button-primary' disabled id='' value='".__("Add Marker","wp-google-maps")."' />
                                            </td>

                                            </tr>

                                        </table>
                                        <p>$wpgmza_act_msg</p>
                                        <br /><br />$wpgmza_csv
                                    </div>

                                    <div id=\"tabs-m-3\">
                                        <h2 style=\"padding-top:0; margin-top:0;\"><i class=\"fa fa-star\"> </i> ".__("Polygons","wp-google-maps")."</h2>
                                        <span id=\"wpgmza_addpolygon_div\"><a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu&action=add_poly&map_id=".sanitize_text_field($_GET['map_id'])."' id='wpgmza_addpoly' class='button-primary' value='".__("Add a New Polygon","wp-google-maps")."' />".__("Add a New Polygon","wp-google-maps")."</a></span>
                                        <div id=\"wpgmza_poly_holder\">".wpgmza_b_return_polygon_list(sanitize_text_field($_GET['map_id']))."</div>
                                    </div>
                                    <div id=\"tabs-m-4\">
                                        <h2 style=\"padding-top:0; margin-top:0;\"><i class=\"fa fa-bars\"> </i> ".__("Polylines","wp-google-maps")."</h2>
                                        <span id=\"wpgmza_addpolyline_div\"><a href='".get_option('siteurl')."/wp-admin/admin.php?page=wp-google-maps-menu&action=add_polyline&map_id=".sanitize_text_field($_GET['map_id'])."' id='wpgmza_addpolyline' class='button-primary' value='".__("Add a New Polyline","wp-google-maps")."' />".__("Add a New Polyline","wp-google-maps")."</a></span>
                                        <div id=\"wpgmza_polyline_holder\">".wpgmza_b_return_polyline_list(sanitize_text_field($_GET['map_id']))."</div>
                                    </div>

                                </div>
                            </div>
                            <div style='display:block; width:50%; overflow:auto; float:left;'>
                            

                                <div id=\"wpgmza_map\">
                                    <div class=\"wpgm_notice_message\" style='text-align:center;'>
                                        <ul>
                                            <li><small><strong>".__("The map could not load.","wp-google-maps")."</strong><br />".__("This is normally caused by a conflict with another plugin or a JavaScript error that is preventing our plugin's Javascript from executing. Please try disable all plugins one by one and see if this problem persists.","wp-google-maps")."</small>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div id=\"wpgmaps_save_reminder\" style=\"display:none;\">
                                    <div class=\"wpgm_notice_message\" style='text-align:center;'>
                                        <ul>
                                            <li>
                                             <h4>".__("Remember to save your map!","wp-google-maps")."</h4>
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                            


                            
                        </form>
                            
                            <h2 style=\"padding-top:0; margin-top:20px;\">".__("Your Markers","wp-google-maps")."</h2>
                            <div id=\"wpgmza_marker_holder\">
                            ".wpgmza_return_marker_list(sanitize_text_field($_GET['map_id']))."
                            </div>
                        
                            <table style='clear:both;'>
                                <tr>
                                    <td><img src=\"".wpgmaps_get_plugin_url()."/images/custom_markers.jpg\" width=\"260\" style=\"border:3px solid #808080;\" title=\"".__("Add detailed information to your markers!")."\" alt=\"".__("Add custom markers to your map!","wp-google-maps")."\" /><br /><br /></td>
                                    <td valign=\"middle\"><span style=\"font-size:18px; color:#666;\">".__("Add detailed information to your markers for only","wp-google-maps")." <strong>$39.99</strong>. ".__("Click","wp-google-maps")." <a href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=image1\" title=\"Pro Edition\" target=\"_BLANK\">".__("here","wp-google-maps")."</a></span></td>
                                </tr>
                                <tr>
                                    <td><img src=\"".wpgmaps_get_plugin_url()."/images/custom_marker_icons.jpg\" width=\"260\" style=\"border:3px solid #808080;\" title=\"".__("Add custom markers to your map!","wp-google-maps")."\" alt=\"".__("Add custom markers to your map!","wp-google-maps")."\" /><br /><br /></td>
                                    <td valign=\"middle\"><span style=\"font-size:18px; color:#666;\">".__("Add different marker icons, or your own icons to make your map really stand out!","wp-google-maps")." ".__("Click","wp-google-maps")." <a href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=image3\" title=\"".__("Pro Edition","wp-google-maps")."\" target=\"_BLANK\">".__("here","wp-google-maps")."</a></span></td>
                                </tr>
                                <tr>
                                    <td><img src=\"".wpgmaps_get_plugin_url()."/images/get_directions.jpg\" width=\"260\" style=\"border:3px solid #808080;\" title=\"".__("Add custom markers to your map!","wp-google-maps")."\" alt=\"".__("Add custom markers to your map!","wp-google-maps")."\" /><br /><br /></td>
                                    <td valign=\"middle\"><span style=\"font-size:18px; color:#666;\">".__("Allow your visitors to get directions to your markers!","wp-google-maps")." ".__("Click","wp-google-maps")." <a href=\"http://www.wpgmaps.com/purchase-professional-version/?utm_source=plugin&utm_medium=link&utm_campaign=image2\" title=\"".__("Pro Edition","wp-google-maps")."\" target=\"_BLANK\">".__("here","wp-google-maps")."</a></span></td>
                                </tr>
                            </table>

                   

                    <p><br /><br />".__("WP Google Maps encourages you to make use of the amazing icons created by Nicolas Mollet's Maps Icons Collection","wp-google-maps")." <a href='http://mapicons.nicolasmollet.com'>http://mapicons.nicolasmollet.com/</a> ".__("and to credit him when doing so.","wp-google-maps")."</p>
                </div>


            </div>



        ";



}



function wpgmza_edit_marker($mid) {
    global $wpgmza_tblname_maps;

    global $wpdb;
    if ($_GET['action'] == "edit_marker" && isset($mid)) {
        $mid = sanitize_text_field($mid);
        $res = wpgmza_get_marker_data($mid);
        echo "
           <div class='wrap'>
                <h1>WP Google Maps</h1>
                <div class='wide'>

                    <h2>".__("Edit Marker Location","wp-google-maps")." ".__("ID","wp-google-maps")."#$mid</h2>
                    <form action='?page=wp-google-maps-menu&action=edit&map_id=".$res->map_id."' method='post' id='wpgmaps_edit_marker'>
                    <p></p>

                    <input type='hidden' name='wpgmaps_marker_id' id='wpgmaps_marker_id' value='".$mid."' />
                    <div id=\"wpgmaps_status\"></div>
                    <table>

                        <tr>
                            <td>".__("Marker Latitude","wp-google-maps").":</td>
                            <td><input id='wpgmaps_marker_lat' name='wpgmaps_marker_lat' type='text' size='15' maxlength='100' value='".$res->lat."' /></td>
                        </tr>
                        <tr>
                            <td>".__("Marker Longitude","wp-google-maps").":</td>
                            <td><input id='wpgmaps_marker_lng' name='wpgmaps_marker_lng' type='text' size='15' maxlength='100' value='".$res->lng."' /></td>
                        </tr>

                    </table>
                    <p class='submit'><input type='submit' name='wpgmza_save_maker_location' class='button-primary' value='".__("Save Marker Location","wp-google-maps")." &raquo;' /></p>
                    <p style=\"width:600px; color:#808080;\">".__("Tip: Use your mouse to change the location of the marker. Simply click and drag it to your desired location.","wp-google-maps")."</p>


                    <div id=\"wpgmza_map\">
                        <div class=\"wpgm_notice_message\" style='text-align:center;'>
                            <ul>
                                <li><small><strong>".__("The map could not load.","wp-google-maps")."</strong><br />".__("This is normally caused by a conflict with another plugin or a JavaScript error that is preventing our plugin's Javascript from executing. Please try disable all plugins one by one and see if this problem persists. If it persists, please contact nick@wpgmaps.com for support.","wp-google-maps")."</small>
                                </li>
                            </ul>
                        </div>
                    </div>




                    </form>
                </div>


            </div>



        ";

    }



}





function wpgmaps_admin_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-slider');

    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
        wp_register_script('my-wpgmaps-upload', plugins_url('js/media.js', __FILE__), array('jquery'), '1.0', true);
        wp_enqueue_script('my-wpgmaps-upload');
    } else {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_register_script('my-wpgmaps-upload', WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/upload.js', array('jquery','media-upload','thickbox'));
        wp_enqueue_script('my-wpgmaps-upload');
    }

    if (isset($_GET['action'])) {
        if ($_GET['action'] == "add_poly" || $_GET['action'] == "edit_poly" || $_GET['action'] == "add_polyline" || $_GET['action'] == "edit_polyline") {
            wp_register_script('my-wpgmaps-color', plugins_url('js/jscolor.js',__FILE__), false, '1.4.1', false);
            wp_enqueue_script('my-wpgmaps-color');
        }
        if ($_GET['page'] == "wp-google-maps-menu" && $_GET['action'] == "edit") {
            wp_enqueue_script( 'jquery-ui-tabs');
            wp_register_script('my-wpgmaps-tabs', plugins_url('js/wpgmaps_tabs.js',__FILE__), array('jquery-ui-core'), '1.0.1', true);
            wp_enqueue_script('my-wpgmaps-tabs');
            wp_register_script('my-wpgmaps-color', plugins_url('js/jscolor.js',__FILE__), false, '1.4.1', false);
            wp_enqueue_script('my-wpgmaps-color');
        }
    }
    if (isset($_GET['page'])) {
        
        if ($_GET['page'] == "wp-google-maps-menu-settings") {
            wp_enqueue_script( 'jquery-ui-tabs');
            if (wp_script_is('my-wpgmaps-tabs','registered')) {  } else {
                wp_register_style('jquery-ui-smoothness', '//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
                wp_enqueue_style('jquery-ui-smoothness');
                wp_register_script('my-wpgmaps-tabs', WPGMAPS_DIR.'js/wpgmaps_tabs.js', array('jquery-ui-core'), '1.0.1', true);
                wp_enqueue_script('my-wpgmaps-tabs');
                
            }
        }

        if ($_GET['page'] == "wp-google-maps-menu-support" && !function_exists('wpgmaps_admin_styles_pro')) {
            wp_register_style('fontawesome', plugins_url('css/font-awesome.min.css', __FILE__));
            wp_enqueue_style('fontawesome');
            wp_register_style('wpgmaps-admin-style', plugins_url('css/wpgmaps-admin.css', __FILE__));
            wp_enqueue_style('wpgmaps-admin-style');
            
        }
    
    }
}
function wpgmaps_user_styles() {

    if (!function_exists('wpgmaps_admin_styles_pro')) {
        global $wpgmza_version;
        wp_register_style( 'wpgmaps-style', plugins_url('css/wpgmza_style.css', __FILE__),array(),$wpgmza_version);
        wp_enqueue_style( 'wpgmaps-style' );
    }


}

function wpgmaps_admin_styles() {
    wp_enqueue_style('thickbox');
     global $wpgmza_version;
        wp_register_style( 'wpgmaps-style', plugins_url('css/wpgmza_style.css', __FILE__),array(),$wpgmza_version );
        wp_enqueue_style( 'wpgmaps-style' );
    wp_register_style( 'fontawesome', plugins_url('css/font-awesome.min.css', __FILE__) );
    wp_enqueue_style( 'fontawesome' );

}

if (isset($_GET['page'])) {

    if ($_GET['page'] == 'wp-google-maps-menu' || $_GET['page'] == 'wp-google-maps-menu-settings' || $_GET['page'] == "wp-google-maps-menu-support") {
        add_action('admin_print_scripts', 'wpgmaps_admin_scripts');
        add_action('admin_print_styles', 'wpgmaps_admin_styles');
    }
}



add_action('wp_print_styles', 'wpgmaps_user_styles');



function wpgmza_return_marker_list($map_id,$admin = true,$width = "100%",$mashup = false,$mashup_ids = false) {
    global $wpdb;
    global $wpgmza_tblname;
    
    
    if ($mashup) {
        $map_ids = $mashup_ids;
        $wpgmza_cnt = 0;

        if ($mashup_ids[0] == "ALL") {

            $wpgmza_sql1 = "
            SELECT *
            FROM $wpgmza_tblname
            ORDER BY `id` DESC
            ";
        }
        else {
            $wpgmza_id_cnt = count($map_ids);
            $sql_string1 = "";
            foreach ($map_ids as $wpgmza_map_id) {
                $wpgmza_cnt++;
                if ($wpgmza_cnt == 1) { $sql_string1 .= "`map_id` = '$wpgmza_map_id' "; }
                elseif ($wpgmza_cnt > 1 && $wpgmza_cnt < $wpgmza_id_cnt) { $sql_string1 .= "OR `map_id` = '$wpgmza_map_id' "; }
                else { $sql_string1 .= "OR `map_id` = '$wpgmza_map_id' "; }

            }
            $wpgmza_sql1 = "
            SELECT *
            FROM $wpgmza_tblname
            WHERE $sql_string1 ORDER BY `id` DESC
            ";
        }

    } else {
        $wpgmza_sql1 = "
            SELECT *
            FROM $wpgmza_tblname
            WHERE `map_id` = '$map_id' ORDER BY `id` DESC
            ";
    }
    $marker_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpgmza_tblname WHERE map_id = %d",$map_id ) );
    if ($marker_count > 5000) {
        return __("There are too many markers to make use of the live edit function. The maximum amount for this functionality is 5000 markers. Anything more than that could crash your browser. In order to edit your markers, you would need to download the table in CSV format, edit it and re-upload it.","wp-google-maps");
    } else {
 
        $results = $wpdb->get_results($wpgmza_sql1);
        $wpgmza_tmp_body = "";
        $wpgmza_tmp_head = "";
        $wpgmza_tmp_footer = "";

        $res = wpgmza_get_map_data($map_id);
        if (!$res->default_marker) {
            $default_marker = "<img src='".wpgmaps_get_plugin_url()."/images/marker.png' />";
        } else {
            $default_marker = "<img src='".$res->default_marker."' />";
        }
        
        
        foreach ( $results as $result ) {
            $img = $result->pic;
            $link = $result->link;
            $icon = $result->icon;
            
            
            if (isset($result->approved)) {
                $approved = $result->approved;
                if ($approved == 0) {
                    $show_approval_button = true;
                } else {
                    $show_approval_button = false;
                }
            } else {
                $show_approval_button = false;
            }
            
            $category_icon = wpgmza_get_category_icon($result->category);

            if (!$img) { $pic = ""; } else { $pic = "<img src=\"".$result->pic."\" width=\"40\" />"; }
            
            if (!$category_icon) {
                if (!$icon) { 
                    $icon = $default_marker; 
                } else { 
                    $icon = "<img src='".$result->icon."' />";
                }
            } else {
                if (!$icon) { 
                    $icon = "<img src='".$category_icon."' />";
                } else { 
                    $icon = "<img src='".$result->icon."' />";
                }
                
            }

            if (!$link) { $linktd = ""; } else { $linktd = "<a href=\"".$result->link."\" target=\"_BLANK\" title=\"".__("View this link","wp-google-maps")."\">&gt;&gt;</a>"; }

            if ($admin) {
                
                $wpgmza_tmp_body .= "<tr id=\"wpgmza_tr_".$result->id."\" class=\"gradeU\">";
                $wpgmza_tmp_body .= "<td height=\"40\">".$result->id."</td>";
                $wpgmza_tmp_body .= "<td height=\"40\">".$icon."<input type=\"hidden\" id=\"wpgmza_hid_marker_icon_".$result->id."\" value=\"".$result->icon."\" /><input type=\"hidden\" id=\"wpgmza_hid_marker_anim_".$result->id."\" value=\"".$result->anim."\" /><input type=\"hidden\" id=\"wpgmza_hid_marker_category_".$result->id."\" value=\"".$result->category."\" /><input type=\"hidden\" id=\"wpgmza_hid_marker_infoopen_".$result->id."\" value=\"".$result->infoopen."\" /><input type=\"hidden\" id=\"wpgmza_hid_marker_retina_".$result->id."\" value=\"".$result->retina."\" /></td>";
                $wpgmza_tmp_body .= "<td>".stripslashes($result->title)."<input type=\"hidden\" id=\"wpgmza_hid_marker_title_".$result->id."\" value=\"".stripslashes($result->title)."\" /></td>";
                $wpgmza_tmp_body .= "<td>".wpgmza_return_category_name($result->category)."<input type=\"hidden\" id=\"wpgmza_hid_marker_category_".$result->id."\" value=\"".$result->category."\" /></td>";
                $wpgmza_tmp_body .= "<td>".stripslashes($result->address)."<input type=\"hidden\" id=\"wpgmza_hid_marker_address_".$result->id."\" value=\"".stripslashes($result->address)."\" /><input type=\"hidden\" id=\"wpgmza_hid_marker_lat_".$result->id."\" value=\"".$result->lat."\" /><input type=\"hidden\" id=\"wpgmza_hid_marker_lng_".$result->id."\" value=\"".$result->lng."\" /></td>";
                $wpgmza_tmp_body .= "<td>".stripslashes($result->description)."<input type=\"hidden\" id=\"wpgmza_hid_marker_desc_".$result->id."\" value=\"".  htmlspecialchars(stripslashes($result->description))."\" /></td>";
                $wpgmza_tmp_body .= "<td>$pic<input type=\"hidden\" id=\"wpgmza_hid_marker_pic_".$result->id."\" value=\"".$result->pic."\" /></td>";
                $wpgmza_tmp_body .= "<td>$linktd<input type=\"hidden\" id=\"wpgmza_hid_marker_link_".$result->id."\" value=\"".$result->link."\" /></td>";
                $wpgmza_tmp_body .= "<td width='170' align='center'>";
                $wpgmza_tmp_body .= "    <a href=\"#wpgmaps_marker\" title=\"".__("Edit this marker","wp-google-maps")."\" class=\"wpgmza_edit_btn button\" id=\"".$result->id."\"><i class=\"fa fa-edit\"> </i> </a> ";
                $wpgmza_tmp_body .= "    <a href=\"?page=wp-google-maps-menu&action=edit_marker&id=".$result->id."\" title=\"".__("Edit this marker","wp-google-maps")."\" class=\"wpgmza_edit_btn button\" id=\"".$result->id."\"><i class=\"fa fa-map-marker\"> </i></a> ";
                if ($show_approval_button) {
                    $wpgmza_tmp_body .= "    <a href=\"javascript:void(0);\" title=\"".__("Approve this marker","wp-google-maps")."\" class=\"wpgmza_approve_btn button\" id=\"".$result->id."\"><i class=\"fa fa-check\"> </i> </a> ";
                }
                $wpgmza_tmp_body .= "    <a href=\"javascript:void(0);\" title=\"".__("Delete this marker","wp-google-maps")."\" class=\"wpgmza_del_btn button\" id=\"".$result->id."\"><i class=\"fa fa-times\"> </i></a>";
                $wpgmza_tmp_body .= "</td>";
                $wpgmza_tmp_body .= "</tr>";
            } else {
                $wpgmza_tmp_body .= "<tr id=\"wpgmza_marker_".$result->id."\" mid=\"".$result->id."\" mapid=\"".$result->map_id."\" class=\"wpgmaps_mlist_row\">";
                $wpgmza_tmp_body .= "   <td width='1px;' style='display:none; width:1px !important;'><span style='display:none;'>".sprintf('%02d', $result->id)."</span></td>";
                $wpgmza_tmp_body .= "   <td class='wpgmza_table_marker' height=\"40\">".str_replace("'","\"",$icon)."</td>";
                $wpgmza_tmp_body .= "   <td class='wpgmza_table_title'>".stripslashes($result->title)."</td>";
                $wpgmza_tmp_body .= "   <td class='wpgmza_table_category'>".wpgmza_return_category_name($result->category)."</td>";
                $wpgmza_tmp_body .= "   <td class='wpgmza_table_address'>".stripslashes($result->address)."</td>";
                $wpgmza_tmp_body .= "   <td class='wpgmza_table_description'>".stripslashes($result->description)."</td>";
                $wpgmza_tmp_body .= "</tr>";
            }
        }
        if ($admin) {
            
            $wpgmza_tmp_head .= "<table id=\"wpgmza_table\" class=\"display\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:$width;\">";
            $wpgmza_tmp_head .= "<thead>";
            $wpgmza_tmp_head .= "<tr>";
            $wpgmza_tmp_head .= "   <th><strong>".__("ID","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th><strong>".__("Icon","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th><strong>".__("Title","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th><strong>".__("Category","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th><strong>".__("Address","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th><strong>".__("Description","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th><strong>".__("Image","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th><strong>".__("Link","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th style='width:182px;'><strong>".__("Action","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "</tr>";
            $wpgmza_tmp_head .= "</thead>";
            $wpgmza_tmp_head .= "<tbody>";
    
        } else {
            
            $wpgmza_tmp_head .= "<div id=\"wpgmza_marker_holder_".$map_id."\" style=\"width:$width;\">";
            $wpgmza_tmp_head .= "<table id=\"wpgmza_table_".$map_id."\" class=\"wpgmza_table\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:$width;\">";
            $wpgmza_tmp_head .= "<thead>";
            $wpgmza_tmp_head .= "<tr>";
            $wpgmza_tmp_head .= "   <th width='1' style='display:none; width:1px !important;'></th>";
            $wpgmza_tmp_head .= "   <th class='wpgmza_table_marker'><strong></strong></th>";
            $wpgmza_tmp_head .= "   <th class='wpgmza_table_title'><strong>".__("Title","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th class='wpgmza_table_category'><strong>".__("Category","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th class='wpgmza_table_address'><strong>".__("Address","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "   <th class='wpgmza_table_description'><strong>".__("Description","wp-google-maps")."</strong></th>";
            $wpgmza_tmp_head .= "</tr>";
            $wpgmza_tmp_head .= "</thead>";
            $wpgmza_tmp_head .= "<tbody>";
        }
        if ($admin) {
            $wpgmza_tmp_footer .= "</tbody></table>";
        } else {
            $wpgmza_tmp_footer .= "</tbody></table></div>";
        }
        return $wpgmza_tmp_head.$wpgmza_tmp_body.$wpgmza_tmp_footer;
    }
}

function wpgmza_return_category_name($cid) {

    global $wpdb;
    global $wpgmza_tblname_categories;
    $pos = strpos($cid, ",");
    if ($pos === false) {
        $results = $wpdb->get_results("SELECT * FROM `$wpgmza_tblname_categories` WHERE `id` = '$cid' LIMIT 1");
        foreach ( $results as $result ) {
            return $result->category_name;
        }
    } else {
        $categories = explode(",",$cid);
        $ret_cat = "";
        $tot_cnt = count($categories);
        $countr = 0;
        foreach ($categories as $cid) {
            $countr++;
            $results = $wpdb->get_results("SELECT * FROM `$wpgmza_tblname_categories` WHERE `id` = '$cid' LIMIT 1");
            foreach ( $results as $result ) {
                if ($countr >= $tot_cnt) {
                    $ret_cat .= $result->category_name;
                } else { $ret_cat .= $result->category_name.","; }
            }
            
        }
        return $ret_cat;
    }
    


}


function wpgmaps_chmodr($path, $filemode) {
    /* removed in 6.0.25. is_dir caused fatal errors on some hosts */
}









if (function_exists('wpgmza_register_pro_version')) {
    add_action('wp_ajax_add_marker', 'wpgmaps_action_callback_pro');
    add_action('wp_ajax_delete_marker', 'wpgmaps_action_callback_pro');
    add_action('wp_ajax_edit_marker', 'wpgmaps_action_callback_pro');
    add_action('wp_ajax_approve_marker', 'wpgmaps_action_callback_pro');
    add_action('wp_ajax_delete_poly', 'wpgmaps_action_callback_pro');
    add_action('wp_ajax_delete_polyline', 'wpgmaps_action_callback_pro');
    add_action('template_redirect','wpgmaps_check_shortcode');

    if (function_exists('wpgmza_register_gold_version')) {
        add_action('admin_head', 'wpgmaps_admin_javascript_gold');
    } else {
        add_action('admin_head', 'wpgmaps_admin_javascript_pro');
    }
        add_action('wp_footer', 'wpgmaps_user_javascript_pro');

    if (function_exists('wpgmza_register_ugm_version')) {
    }

    add_shortcode( 'wpgmza', 'wpgmaps_tag_pro' );
} else {
    add_action('admin_head', 'wpgmaps_admin_javascript_basic');
    add_action('wp_ajax_add_marker', 'wpgmaps_action_callback_basic');
    add_action('wp_ajax_delete_marker', 'wpgmaps_action_callback_basic');
    add_action('wp_ajax_edit_marker', 'wpgmaps_action_callback_basic');+
    add_action('wp_ajax_delete_poly', 'wpgmaps_action_callback_basic');
    add_action('wp_ajax_delete_polyline', 'wpgmaps_action_callback_basic');
    
    add_action('template_redirect','wpgmaps_check_shortcode');
    add_action('wp_footer', 'wpgmaps_user_javascript_basic');
    add_shortcode( 'wpgmza', 'wpgmaps_tag_basic' );
}



function wpgmaps_check_shortcode() {
    global $posts;
    global $short_code_active;
    $short_code_active = false;
    $pattern = get_shortcode_regex();

    foreach ($posts as $wpgmpost) {
        preg_match_all('/'.$pattern.'/s', $wpgmpost->post_content, $matches);
        foreach ($matches as $match) {
            if (is_array($match)) {
                foreach($match as $key => $val) {
                    $pos = strpos($val, "wpgmza");
                    if ($pos === false) { } else { $short_code_active = true; }
                }
            }
        }
    }
}

function wpgmaps_check_permissions() {
    $filename = dirname( __FILE__ ).'/wpgmaps.tmp';
    $testcontent = "Permission Check\n";
    $handle = @fopen($filename, 'w');
    if (@fwrite($handle, $testcontent) === FALSE) {
        @fclose($handle);
        add_option("wpgmza_permission","n");
        return false;
    }
    else {
        @fclose($handle);
        add_option("wpgmza_permission","y");
        return true;
    }


}
function wpgmaps_permission_warning() {
    echo "<div class='error below-h1'><big>";
    _e("The plugin directory does not have 'write' permissions. Please enable 'write' permissions (755) for ");
    echo "\"".c."\" ";
    _e("in order for this plugin to work! Please see ");
    echo "<a href='http://codex.wordpress.org/Changing_File_Permissions#Using_an_FTP_Client'>";
    _e("this page");
    echo "</a> ";
    _e("for help on how to do it.");
    echo "</big></div>";
}


/* handle database check upon upgrade */
function wpgmaps_update_db_check() {
    global $wpgmza_version;
    if (get_option('wpgmza_db_version') != $wpgmza_version) {
        wpgmaps_handle_db();
    }

    
}


add_action('plugins_loaded', 'wpgmaps_update_db_check');





function wpgmaps_handle_db() {
    global $wpdb;
    global $wpgmza_version;
    global $wpgmza_tblname_poly;
    global $wpgmza_tblname_polylines;
    global $wpgmza_tblname_categories;
    global $wpgmza_tblname_category_maps;
    global $wpgmza_tblname;

    $table_name = $wpdb->prefix . "wpgmza";




    $sql = "
        CREATE TABLE `".$table_name."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          map_id int(11) NOT NULL,
          address varchar(700) NOT NULL,
          description mediumtext NOT NULL,
          pic varchar(700) NOT NULL,
          link varchar(700) NOT NULL,
          icon varchar(700) NOT NULL,
          lat varchar(100) NOT NULL,
          lng varchar(100) NOT NULL,
          anim varchar(3) NOT NULL,
          title varchar(700) NOT NULL,
          infoopen varchar(3) NOT NULL,
          category varchar(500) NOT NULL,
          approved tinyint(1) DEFAULT '1',
          retina tinyint(1) DEFAULT '0',
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);



    $sql = "
        CREATE TABLE `".$wpgmza_tblname_poly."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          map_id int(11) NOT NULL,
          polydata LONGTEXT NOT NULL,
          linecolor VARCHAR(7) NOT NULL,
          lineopacity VARCHAR(7) NOT NULL,
          fillcolor VARCHAR(7) NOT NULL,
          opacity VARCHAR(3) NOT NULL,
          title VARCHAR(250) NOT NULL,
          link VARCHAR(700) NOT NULL,
          ohfillcolor VARCHAR(7) NOT NULL,
          ohlinecolor VARCHAR(7) NOT NULL,
          ohopacity VARCHAR(3) NOT NULL,
          polyname VARCHAR(100) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    dbDelta($sql);


    $sql = "
        CREATE TABLE `".$wpgmza_tblname_polylines."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          map_id int(11) NOT NULL,
          polydata LONGTEXT NOT NULL,
          linecolor VARCHAR(7) NOT NULL,
          linethickness VARCHAR(3) NOT NULL,
          opacity VARCHAR(3) NOT NULL,
          polyname VARCHAR(100) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    dbDelta($sql);


    $sql = "
        CREATE TABLE `".$wpgmza_tblname_categories."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          active TINYINT(1) NOT NULL,
          category_name VARCHAR(50) NOT NULL,
          category_icon VARCHAR(700) NOT NULL,
          retina TINYINT(1) DEFAULT '0',
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    dbDelta($sql);

    $sql = "
        CREATE TABLE `".$wpgmza_tblname_category_maps."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          cat_id INT(11) NOT NULL,
          map_id INT(11) NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
    ";

    dbDelta($sql);


    $table_name = $wpdb->prefix . "wpgmza_maps";
    $sql = "
        CREATE TABLE `".$table_name."` (
          id int(11) NOT NULL AUTO_INCREMENT,
          map_title varchar(50) NOT NULL,
          map_width varchar(6) NOT NULL,
          map_height varchar(6) NOT NULL,
          map_start_lat varchar(700) NOT NULL,
          map_start_lng varchar(700) NOT NULL,
          map_start_location varchar(700) NOT NULL,
          map_start_zoom INT(10) NOT NULL,
          default_marker varchar(700) NOT NULL,
          type INT(10) NOT NULL,
          alignment INT(10) NOT NULL,
          directions_enabled INT(10) NOT NULL,
          styling_enabled INT(10) NOT NULL,
          styling_json mediumtext NOT NULL,
          active INT(1) NOT NULL,
          kml VARCHAR(700) NOT NULL,
          bicycle INT(10) NOT NULL,
          traffic INT(10) NOT NULL,
          dbox INT(10) NOT NULL,
          dbox_width varchar(10) NOT NULL,
          listmarkers INT(10) NOT NULL,
          listmarkers_advanced INT(10) NOT NULL,
          filterbycat TINYINT(1) NOT NULL,
          ugm_enabled INT(10) NOT NULL,
          ugm_category_enabled TINYINT(1) NOT NULL,
          fusion VARCHAR(100) NOT NULL,
          map_width_type VARCHAR(3) NOT NULL,
          map_height_type VARCHAR(3) NOT NULL,
          mass_marker_support INT(10) NOT NULL,
          ugm_access INT(10) NOT NULL,
          order_markers_by INT(10) NOT NULL,
          order_markers_choice INT(10) NOT NULL,
          show_user_location INT(3) NOT NULL,
          default_to VARCHAR(700) NOT NULL,
          other_settings longtext NOT NULL,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
    ";

    dbDelta($sql);


    /* check for previous versions containing 'desc' instead of 'description' */
    $results = $wpdb->get_results("DESC $wpgmza_tblname");
    $founded = 0;
    foreach ($results as $row ) {
        if ($row->Field == "desc") {
            $founded++;
        }
    }
    if ($founded>0) { $wpdb->query("ALTER TABLE $wpgmza_tblname CHANGE `desc` `description` MEDIUMTEXT"); }
    

    
    /* check for older version of "category" and change to varchar instead of int */
    $results = $wpdb->get_results("DESC $wpgmza_tblname");
    $founded = 0;
    foreach ($results as $row ) {
        
        if ($row->Field == "category") {
            if ($row->Type == "int(11)") {
                $founded++;
            }
        }
    }
    if ($founded>0) { $wpdb->query("ALTER TABLE $wpgmza_tblname CHANGE `category` `category` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0'"); }
    

    add_option("wpgmza_db_version", $wpgmza_version);
    update_option("wpgmza_db_version",$wpgmza_version);
}

function wpgmza_get_map_data($map_id) {
    global $wpdb;
    global $wpgmza_tblname_maps;

    $result = $wpdb->get_results("
        SELECT *
        FROM $wpgmza_tblname_maps
        WHERE `id` = '".$map_id."' LIMIT 1
    ");

    if (isset($result[0])) { return $result[0]; }

    

}
function wpgmza_get_marker_data($mid) {
    global $wpdb;
    global $wpgmza_tblname;

    $result = $wpdb->get_results("
        SELECT *
        FROM $wpgmza_tblname
        WHERE `id` = '".$mid."' LIMIT 1
    ");

    $res = $result[0];
    return $res;

}
function wpgmaps_upgrade_notice() {
    global $wpgmza_pro_version;
    echo "<div class='error below-h1'>

            <p>Dear Pro User<br /></p>

            <p>We have recently added new functionality to the Pro version of this plugin. You are currently using the latest
            Basic version which needs the latest Pro version for all functionality to work correctly. Your current Pro version is
            $wpgmza_pro_version - The latest Pro version is <strong>5.41</strong><br /></p>

            <p>You should be able to update your Pro version the same way you <a href='update-core.php'>update all other WordPress plugins</a>. </p>
            <p>If you run into any problems updating your pro version, please view <a href='http://www.wpgmaps.com/documentation/how-do-i-update-my-wp-google-maps-progolddev-plugin/'>this page</a>. </p>

            <p>Kind regards,<br /><a href='http://www.wpgmaps.com/'>WP Google Maps</a></p>

    </div>";
}
function wpgmaps_trash_map($map_id) {
    global $wpdb;
    global $wpgmza_tblname_maps;
    if (isset($map_id)) {
        $rows_affected = $wpdb->query( $wpdb->prepare( "UPDATE $wpgmza_tblname_maps SET active = %d WHERE id = %d", 1, $map_id) );
        return true;
    } else {
        return false;
    }
}

function wpgmza_stats($sec) {
    $wpgmza_stats = get_option("wpgmza_stats");
    if ($wpgmza_stats) {
        if (isset($wpgmza_stats[$sec]["views"])) {
            $wpgmza_stats[$sec]["views"] = $wpgmza_stats[$sec]["views"] + 1;
            $wpgmza_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
        } else {
            $wpgmza_stats[$sec]["views"] = 1;
            $wpgmza_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
            $wpgmza_stats[$sec]["first_accessed"] = date("Y-m-d H:i:s");
        }


    } else {

        $wpgmza_stats[$sec]["views"] = 1;
        $wpgmza_stats[$sec]["last_accessed"] = date("Y-m-d H:i:s");
        $wpgmza_stats[$sec]["first_accessed"] = date("Y-m-d H:i:s");


    }
    update_option("wpgmza_stats",$wpgmza_stats);

}

function wpgmaps_filter(&$array) {
    $clean = array();
    foreach($array as $key => &$value ) {
        if( is_array($value) ) {
            wpgmaps_filter($value);
        } else {
            if (get_magic_quotes_gpc()) {
                $data = stripslashes($value);
            }
            $data = esc_sql($value);
        }
    }
}
function wpgmaps_debugger($section) {

    global $debug;
    global $debug_start;
    global $debug_step;
    if ($debug) {
        $end = (float) array_sum(explode(' ',microtime()));
        echo "<!-- $section processing time: ". sprintf("%.4f", ($end-$debug_start))." seconds\n -->";
    }

}

function wpgmaps_load_jquery() {
    if (!is_admin()) {
        $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
        if (isset($wpgmza_settings['wpgmza_settings_force_jquery'])) { 
            if ($wpgmza_settings['wpgmza_settings_force_jquery'] == "yes") {
                wp_deregister_script('jquery');
                wp_register_script('jquery', plugins_url("js/jquery.min.js",__FILE__), false, "1.8.3");
        }
        }
        wp_enqueue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'wpgmaps_load_jquery', 9999);

function wpgmza_get_category_data($cat_id) {
    global $wpgmza_tblname_categories;
    global $wpdb;
    
    $result = $wpdb->get_row("
	SELECT `category_icon`,`retina`
	FROM `$wpgmza_tblname_categories`
        WHERE `id` = '$cat_id'
        AND `active` = 0
        LIMIT 1
	");
    return $result;
}
function wpgmza_get_category_icon($cat_id) {
    global $wpgmza_tblname_categories;
    global $wpdb;
    
    $result = $wpdb->get_var("
	SELECT `category_icon`
	FROM `$wpgmza_tblname_categories`
        WHERE `id` = '$cat_id'
        AND `active` = 0
        LIMIT 1
	");
    return $result;
}


function wpgmza_return_error($data) {
    echo "<div id=\"message\" class=\"error\"><p><strong>".$data->get_error_message()."</strong><blockquote>".$data->get_error_data()."</blockquote></p></div>";
    wpgmza_write_to_error_log($data);
}
function wpgmza_write_to_error_log($data) {
    if (wpgmza_error_directory()) {
        if (is_multisite()) {
            $upload_dir = wp_upload_dir();
            $content = "\r\n".date("Y-m-d H:i:s").": ".$data->get_error_message() . " -> ". $data->get_error_data();
            $fp = @fopen($upload_dir['basedir'].'/wp-google-maps'."/error_log.txt","a+");
            fwrite($fp,$content);
        } else {
            $content = "\r\n".date("Y-m-d H:i:s").": ".$data->get_error_message() . " -> ". $data->get_error_data();
            $fp = @fopen(ABSPATH.'wp-content/uploads/wp-google-maps'."/error_log.txt","a+");
            fwrite($fp,$content);
        }
    }
    
    error_log(date("Y-m-d H:i:s"). ": WP Google Maps : " . $data->get_error_message() . "->" . $data->get_error_data());
    
}
function wpgmza_error_directory() {
    $upload_dir = wp_upload_dir();
    
    if (is_multisite()) {
        if (!file_exists($upload_dir['basedir'].'/wp-google-maps')) {
            wp_mkdir_p($upload_dir['basedir'].'/wp-google-maps');
            $content = "Error log created";
            $fp = @fopen($upload_dir['basedir'].'/wp-google-maps'."/error_log.txt","w+");
            fwrite($fp,$content);
        }
    } else {
        if (!file_exists(ABSPATH.'wp-content/uploads/wp-google-maps')) {
            wp_mkdir_p(ABSPATH.'wp-content/uploads/wp-google-maps');
            $content = "Error log created";
            $fp = @fopen(ABSPATH.'wp-content/uploads/wp-google-maps'."/error_log.txt","w+");
            fwrite($fp,$content);
        }
        
    }
    return true;
    
}

function wpgmza_return_error_log() {
    $fh = @fopen(ABSPATH.'wp-content/uploads/wp-google-maps'."/error_log.txt","r");
    $ret = "";
    if ($fh) {
        for ($i=0;$i<10;$i++) {
            $visits = fread($fh,4096);
            $ret .= $visits;
        }
    } else {
        $ret .= "No errors to report on";
    }
    return $ret;
    
}
function wpgmaps_marker_permission_check() { 
    
    
    $wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
    if (isset($wpgmza_settings['wpgmza_settings_marker_pull']) && $wpgmza_settings['wpgmza_settings_marker_pull'] == '0') {
        /* using db method, do nothing */
        return;
    }
    
    
    if (function_exists("wpgmza_register_pro_version")) {
        global $wpgmza_pro_version;
        if (floatval($wpgmza_pro_version) < 5.41) {
            $marker_location = get_option("wpgmza_xml_location");
        } else {
            $marker_location = wpgmza_return_marker_path();
        }
    } else {
        $marker_location = wpgmza_return_marker_path();
    }
    
    
    $wpgmza_file_perms = substr(sprintf('%o', fileperms($marker_location)), -4);
    $fpe = false;
    $fpe_error = "";
    if ($wpgmza_file_perms == "0777" || $wpgmza_file_perms == "0755" || $wpgmza_file_perms == "0775" || $wpgmza_file_perms == "0705" || $wpgmza_file_perms == "2705" || $wpgmza_file_perms == "2775" || $wpgmza_file_perms == "2777" ) { 
        $fpe = true;
        $fpe_error = "";
    }
    else if ($wpgmza_file_perms == "0") {
        $fpe = false;
        $fpe_error = __("This folder does not exist. Please create it.","wp-google-maps"). ": ". $marker_location;
    } else { 
        $fpe = false;
        $fpe_error = __("WP Google Maps does not have write permission to the marker location directory. This is required to store marker data. Please CHMOD the folder ","wp-google-maps").$marker_location.__(" to 755 or 777, or change the directory in the Maps->Settings page. (Current file permissions are ","wp-google-maps").$wpgmza_file_perms.")";
    }
    
    if (!$fpe) {
	echo "<div id=\"message\" class=\"error\"><p>".$fpe_error."</p></div>";
    } 
}

function wpgmza_basic_support_menu() {
    wpgmza_stats("support_basic");
?>   
        <h1><?php _e("WP Google Maps Support","wp-google-maps"); ?></h1>
        <div class="wpgmza_row">
            <div class='wpgmza_row_col' style='background-color:#FFF;'>
                <h2><i class="fa fa-book fa-2x"></i> <?php _e("Documentation","wp-google-maps"); ?></h2>
                <hr />
                <p><?php _e("Getting started? Read through some of these articles to help you along your way.","wp-google-maps"); ?></p>
                <p><strong><?php _e("Documentation:","wp-google-maps"); ?></strong></p>
                <ul>
                    <li><a href='http://www.wpgmaps.com/documentation/creating-your-first-map/' target='_BLANK' title='<?php _e("Creating your first map","wp-google-maps"); ?>'><?php _e("Creating your first map","wp-google-maps"); ?></a></li>
                    <li><a href='http://www.wpgmaps.com/documentation/using-your-map-in-a-widget/' target='_BLANK' title='<?php _e("Using your map as a Widget","wp-google-maps"); ?>'><?php _e("Using your map as a Widget","wp-google-maps"); ?></a></li>
                    <li><a href='http://www.wpgmaps.com/documentation/changing-the-google-maps-language/' target='_BLANK' title='<?php _e("Changing the Google Maps language","wp-google-maps"); ?>'><?php _e("Changing the Google Maps language","wp-google-maps"); ?></a></li>
                    <li><a href='http://www.wpgmaps.com/documentation/' target='_BLANK' title='<?php _e("WP Google Maps Documentation","wp-google-maps"); ?>'><?php _e("View all documentation.","wp-google-maps"); ?></a></li>
                </ul>
            </div>
            <div class='wpgmza_row_col' style='background-color:#FFF;'>
                <h2><i class="fa fa-exclamation-circle fa-2x"></i> <?php _e("Troubleshooting","wp-google-maps"); ?></h2>
                <hr />
                <p><?php _e("WP Google Maps has a diverse and wide range of features which may, from time to time, run into conflicts with the thousands of themes and other plugins on the market.","wp-google-maps"); ?></p>
                <p><strong><?php _e("Common issues:","wp-google-maps"); ?></strong></p>
                <ul>
                    <li><a href='http://www.wpgmaps.com/documentation/troubleshooting/my-map-is-not-showing-on-my-website/' target='_BLANK' title='<?php _e("My map is not showing on my website","wp-google-maps"); ?>'><?php _e("My map is not showing on my website","wp-google-maps"); ?></a></li>
                    <li><a href='http://www.wpgmaps.com/documentation/troubleshooting/my-markers-are-not-showing-on-my-map/' target='_BLANK' title='<?php _e("My markers are not showing on my map in the front-end","wp-google-maps"); ?>'><?php _e("My markers are not showing on my map in the front-end","wp-google-maps"); ?></a></li>
                    <li><a href='http://www.wpgmaps.com/documentation/troubleshooting/im-getting-jquery-errors-showing-on-my-website/' target='_BLANK' title='<?php _e("I'm getting jQuery errors showing on my website","wp-google-maps"); ?>'><?php _e("I'm getting jQuery errors showing on my website","wp-google-maps"); ?></a></li>
                </ul>
            </div>
            <div class='wpgmza_row_col' style='background-color:#FFF;'>
                <h2><i class="fa fa-bullhorn fa-2x"></i> <?php _e("Support","wp-google-maps"); ?></h2>
                <hr />
                <p><?php _e("Still need help? Use one of these links below.","wp-google-maps"); ?></p>
                <ul>
                    <li><a href='http://www.wpgmaps.com/forums/forum/support-forum/' target='_BLANK' title='<?php _e("Support forum","wp-google-maps"); ?>'><?php _e("Support forum","wp-google-maps"); ?></a></li>
                    <li><a href='http://www.wpgmaps.com/contact-us/' target='_BLANK' title='<?php _e("Contact us","wp-google-maps"); ?>'><?php _e("Contact us","wp-google-maps"); ?></a></li>
                </ul>
            </div>
            
            
        </div>
        
<?php
}
