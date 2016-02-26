<?php

class URE_Known_JS_CSS_Compatibility_Issues {
    
    public static function fix($hook_suffix, $ure_hook_suffixes) {

        $ure_hook_suffixes[] = 'users.php';
        $ure_hook_suffixes[] = 'profile.php';
        
        if (!in_array($hook_suffix, $ure_hook_suffixes)) {
            return;
        }
        
        self::unload_techgostore($hook_suffix);
        self::unload_musicplay($hook_suffix);
        self::unload_conflict_plugins_css($hook_suffix);
        
    }
    // end of fix()
    
    
    /**
     * Unload WP TechGoStore theme JS and CSS to exclude compatibility issues with URE
     */
    private static function unload_techgostore($hook_suffix) {

        if (!defined('THEME_SLUG') || THEME_SLUG !== 'techgo_') {
            return;
        }

        wp_deregister_script('jqueryform');
        wp_deregister_script('tab');
        wp_deregister_script('shortcode_js');
        wp_deregister_script('fancybox_js');
        wp_deregister_script('bootstrap-colorpicker');
        wp_deregister_script('logo_upload');
        wp_deregister_script('js_wd_menu_backend');

        wp_deregister_style('config_css');
        wp_deregister_style('fancybox_css');
        wp_deregister_style('colorpicker');
        wp_deregister_style('font-awesome');
        wp_deregister_style('css_wd_menu_backend');
    }
    // end of unload_techgostore()
    

    /**
     * Unload MusicPlay theme CSS to exclude compatibility issues with URE
     * 
     */
    private static function unload_musicplay($hook_suffix) {
        if (!in_array($hook_suffix, array('users.php', 'profile.php')) ) {
            return;
        }
        
        if (defined('THEMENAME') && THEMENAME!=='MusicPlay') {
            return;
        }
        
        wp_deregister_style('atpadmin');
        wp_deregister_style('appointment-style');
        wp_deregister_style('atp-chosen');
        wp_deregister_style('atp_plupload');
        wp_deregister_style('atp-jquery-timepicker-addon');
        wp_deregister_style('atp-jquery-ui');
        
    }
    // end of unload_music_play()
    
    
    private static function unload_conflict_plugins_css($hook_suffix) {    
        global $wp_styles;
                
        if (!in_array($hook_suffix, array('users.php', 'profile.php')) ) {
            return;
        }
        
        // remove conflict CSS from responsive-admin-maintenance-pro plugin
        if (isset($wp_styles->registered['admin-page-css'])) {
            wp_deregister_style('admin-page-css');
        }
    }
    // end of unload_conflict_plugins_css()    

    

}
// end of URE_Fix_Known_JS_CSS_Compatibility_Issues