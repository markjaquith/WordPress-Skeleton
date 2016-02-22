<?php

/*
 * Main class of User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v2+
 * 
 */

class URE_Protect_Admin {
    
    private $lib = null;
    private $user_to_check = null;  // cached list of user IDs, who has Administrator role     	 
    
    public function __construct($lib) {
        $this->lib = $lib;
        $this->user_to_check = array();
        
        // Exclude administrator role from edit list.
        add_filter('editable_roles', array($this, 'exclude_admin_role'));
        // prohibit any actions with user who has Administrator role
        add_filter('user_has_cap', array($this, 'not_edit_admin'), 10, 3);
        // exclude users with 'Administrator' role from users list
        add_action('pre_user_query', array($this, 'exclude_administrators'));
        // do not show 'Administrator (s)' view above users list
        add_filter('views_users', array($this, 'exclude_admins_view'));       
    }
    // end of __construct()
    

    /**
     * exclude administrator role from the roles list
     * 
     * @param string $roles
     * @return array
     */
    public function exclude_admin_role($roles) {

        if (isset($roles['administrator'])) {
            unset($roles['administrator']);
        }

        return $roles;
    }
    // end of exclude_admin_role()
    
    
        /**
     * Check if user has "Administrator" role assigned
     * 
     * @global wpdb $wpdb
     * @param int $user_id
     * @return boolean returns true is user has Role "Administrator"
     */
    private function has_administrator_role($user_id) {
        global $wpdb;

        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $table_name = $this->lib->get_usermeta_table_name();
        $meta_key = $wpdb->prefix . 'capabilities';
        $query = "SELECT count(*)
                FROM $table_name
                WHERE user_id=$user_id AND meta_key='$meta_key' AND meta_value like '%administrator%'";
        $has_admin_role = $wpdb->get_var($query);
        if ($has_admin_role > 0) {
            $result = true;
        } else {
            $result = false;
        }
        // cache checking result for the future use
        $this->user_to_check[$user_id] = $result;

        return $result;
    }

    // end of has_administrator_role()
    
    
    /**
     * We have two vulnerable queries with user id at admin interface, which should be processed
     * 1st: http://blogdomain.com/wp-admin/user-edit.php?user_id=ID&wp_http_referer=%2Fwp-admin%2Fusers.php
     * 2nd: http://blogdomain.com/wp-admin/users.php?action=delete&user=ID&_wpnonce=ab34225a78
     * If put Administrator user ID into such request, user with lower capabilities (if he has 'edit_users')
     * can edit, delete admin record
     * This function removes 'edit_users' capability from current user capabilities
     * if request has admin user ID in it
     *
     * @param array $allcaps
     * @param type $caps
     * @param string $name
     * @return array
     */
    public function not_edit_admin($allcaps, $caps, $name) {
        
        $user_keys = array('user_id', 'user');
        foreach ($user_keys as $user_key) {
            $access_deny = false;
            $user_id = $this->lib->get_request_var($user_key, 'get');
            if (empty($user_id)) {
                break;
            }
            if ($user_id == 1) {  // built-in WordPress Admin
                $access_deny = true;
            } else {
                if (!isset($this->user_to_check[$user_id])) {
                    // check if user_id has Administrator role
                    $access_deny = $this->has_administrator_role($user_id);
                } else {
                    // user_id was checked already, get result from cash
                    $access_deny = $this->user_to_check[$user_id];
                }
            }
            if ($access_deny) {
                unset($allcaps['edit_users']);
            }
            break;            
        }

        return $allcaps;
    }
    // end of not_edit_admin()
    
    
    /**
     * add where criteria to exclude users with 'Administrator' role from users list
     * 
     * @global wpdb $wpdb
     * @param  type $user_query
     */
    public function exclude_administrators($user_query) {

        global $wpdb;

        $result = false;
        $links_to_block = array('profile.php', 'users.php');
        foreach ($links_to_block as $key => $value) {
            $result = stripos($_SERVER['REQUEST_URI'], $value);
            if ($result !== false) {
                break;
            }
        }

        if ($result === false) { // block the user edit stuff only
            return;
        }

        // get user_id of users with 'Administrator' role  
        $tableName = $this->lib->get_usermeta_table_name();
        $meta_key = $wpdb->prefix . 'capabilities';
        $admin_role_key = '%"administrator"%';
        $query = "select user_id
              from $tableName
              where meta_key='$meta_key' and meta_value like '$admin_role_key'";
        $ids_arr = $wpdb->get_col($query);
        if (is_array($ids_arr) && count($ids_arr) > 0) {
            $ids = implode(',', $ids_arr);
            $user_query->query_where .= " AND ( $wpdb->users.ID NOT IN ( $ids ) )";
        }
    }
    // end of exclude_administrators()

    
    /*
     * Exclude view of users with Administrator role
     * 
     */
    public function exclude_admins_view($views) {

        unset($views['administrator']);

        return $views;
    }
    // end of exclude_admins_view()
        
}
// end of URE_Protect_Admin class
