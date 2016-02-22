<?php
/**
 * Support for bbPress user roles and capabilities
 * 
 * Project: User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * 
 **/

class URE_bbPress {

    public static $instance = null;
    
    protected $lib = null;
    
    
    protected function __construct(Ure_Lib $lib) {
        
        $this->lib = $lib;
        
    }
    // end of __construct()
    
    
    static public function get_instance(Ure_Lib $lib) {
        if (!function_exists('bbp_filter_blog_editable_roles')) {  // bbPress plugin is not active
            return null;            
        }
        
        if (self::$instance!==null) {
            return self::$instance;
        }
        
        if ($lib->is_pro()) {
            self::$instance = new URE_bbPress_Pro($lib);
        } else {
            self::$instance = new URE_bbPress($lib);
        }
        
        return self::$instance;
    }
    // end of get_instance()
    

    /**
     * Exclude roles created by bbPress
     * 
     * @global array $wp_roles
     * @return array
     */
    public function get_roles() {
        
        global $wp_roles;
                        
        $roles = bbp_filter_blog_editable_roles($wp_roles->roles);  // exclude bbPress roles	         
        
        return $roles;
    }
    // end of get_roles()
    
    
    /**
     * Get full list user capabilities created by bbPress
     * 
     * @return array
     */   
    public function get_caps() {
        $caps = array_keys(bbp_get_caps_for_role(bbp_get_keymaster_role()));
        
        return $caps;
    }
    // end of get_caps()
    
}
// end of URE_bbPress class