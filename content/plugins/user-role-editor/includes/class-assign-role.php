<?php
/**
 * Project: User Role Editor plugin
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * Greetings: some ideas and code samples for long runing cron job was taken from the "Broken Link Checker" plugin (Janis Elst).
 * License: GPL v2+
 * 
 * Assign role to the users without role stuff
 */
class URE_Assign_Role {
    
    const MAX_USERS_TO_PROCESS = 50;
    
    protected $lib = null;
    
    
    function __construct($lib) {
        
        $this->lib = $lib;
    }
    // end of __construct()


    public function create_no_rights_role() {
        global $wp_roles;
        
        $role_id = 'no_rights';
        $role_name = 'No rights';
        
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        if (isset($wp_roles->roles[$role_id])) {
            return;
        }
        add_role($role_id, $role_name, array());
        
    }
    // end of create_no_rights_role()
        
    
    private function get_where_condition() {
        global $wpdb;

        $usermeta = $this->lib->get_usermeta_table_name();
        $id = get_current_blog_id();
        $blog_prefix = $wpdb->get_blog_prefix($id);
        $where = "where not exists (select user_id from {$usermeta}
                                          where user_id=users.ID and meta_key='{$blog_prefix}capabilities') or
                          exists (select user_id from {$usermeta}
                                    where user_id=users.ID and meta_key='{$blog_prefix}capabilities' and meta_value='a:0:{}')";
                                    
        return $where;                            
    }
    // end of get_where_condition()
    
    
    public function count_users_without_role() {
        
        global $wpdb;
    
        $where = $this->get_where_condition();
        $query = "select count(ID) from {$wpdb->users} users {$where}";
        $users_quant = $wpdb->get_var($query);
        
        return $users_quant;
    }
    // end of count_users_without_role()
    
    
    public function get_users_without_role($new_role='') {
        
        global $wpdb;
        
        $top_limit = self::MAX_USERS_TO_PROCESS;
        $id = get_current_blog_id();
        $blog_prefix = $wpdb->get_blog_prefix($id);
        $where = $this->get_where_condition();
        $query = "select ID from {$wpdb->users} users
                    {$where}
                    limit 0, {$top_limit}";
        $users0 = $wpdb->get_col($query);        
        
        return $users0;        
    }
    // end of get_users_without_role()
       
}
// end of URE_Assign_Role class