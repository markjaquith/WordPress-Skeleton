<?php
/*
 * Project: User Role Editor WordPress plugin 
 * Class for Assigning to a user multiple roles
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v2+
 * 
*/

class URE_User_Other_Roles {

    protected $lib = null;
    
    
    function __construct(Ure_Lib $lib) {
    
        $this->lib = $lib;
        $this->set_hooks();
    }
    // end of $lib
    
    
    public function set_hooks() {
        
        if (is_admin()) {
            add_filter( 'additional_capabilities_display', array($this, 'additional_capabilities_display'), 10, 1);        
            add_action( 'admin_print_styles-user-edit.php', array($this, 'load_css') );
            add_action( 'admin_print_styles-user-new.php', array($this, 'load_css') );
            add_action( 'admin_enqueue_scripts', array($this, 'load_js' ) );
            add_action( 'edit_user_profile', array($this, 'edit_user_profile_html'), 10, 1 );
            add_action( 'user_new_form', array($this, 'user_new_form'), 10, 1 );
            add_action( 'profile_update', array($this, 'update'), 10 );
        }
        if ($this->lib->multisite) {          
            add_action( 'wpmu_activate_user', array($this, 'add_other_roles'), 10, 1 );
        }
        add_action( 'user_register', array($this, 'add_other_roles'), 10, 1 );
            
    }
    // end of set_hooks()
    
    
    public function additional_capabilities_display($display) {
        
        $display = false;
        
        return $display;
        
    }
    // end of additional_capabilities_display()    
    
    
    /*
     * Load CSS for the user profile edit page
     */
    public function load_css() {
        
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_style('ure-jquery-multiple-select', plugins_url('/css/multiple-select.css', URE_PLUGIN_FULL_PATH), array(), false, 'screen');
        
    }
    // end of load_css()                


    public function load_js($hook_suffix)  {
        
        if (!in_array($hook_suffix, array('user-edit.php', 'user-new.php'))) {
            return;
        }
        
        wp_enqueue_script('jquery-ui-dialog', false, array('jquery-ui-core', 'jquery-ui-button', 'jquery'));
        wp_register_script('ure-jquery-multiple-select', plugins_url('/js/jquery.multiple.select.js', URE_PLUGIN_FULL_PATH));
        wp_enqueue_script('ure-jquery-multiple-select');
        wp_register_script('ure-user-profile-other-roles', plugins_url('/js/ure-user-profile-other-roles.js', URE_PLUGIN_FULL_PATH));
        wp_enqueue_script('ure-user-profile-other-roles');
        wp_localize_script('ure-user-profile-other-roles', 'ure_data_user_profile_other_roles', array(
            'wp_nonce' => wp_create_nonce('user-role-editor'),
            'other_roles' => esc_html__('Other Roles', 'user-role-editor'),
            'select_roles' => esc_html__('Select additional roles for this user', 'user-role-editor')
        ));
    }
    // end of load_js()
    
    
    /**
     * Returns list of user roles, except 1st one, and bbPress assigned as they are shown by WordPress and bbPress theirselves.
     * 
     * @param type $user WP_User from wp-includes/capabilities.php
     * @return array
     */
    public function get_roles_array($user) {

        if (!is_array($user->roles) || count($user->roles) <= 1) {
            return array();
        }

        // get bbPress assigned user role
        if (function_exists('bbp_filter_blog_editable_roles')) {
            $bb_press_role = bbp_get_user_role($user->ID);
        } else {
            $bb_press_role = '';
        }

        $roles = array();
        foreach ($user->roles as $key => $value) {
            if (!empty($bb_press_role) && $bb_press_role === $value) {
                // exclude bbPress assigned role
                continue;
            }
            $roles[] = $value;
        }
        array_shift($roles); // exclude primary role which is shown by WordPress itself

        return $roles;
    }
    // end of get_roles_array()    
    

    private function roles_select_html($user) {
        
        global $wp_roles;
                
        $user_roles = $user->roles;
        $primary_role = array_shift($user_roles);
        $roles = apply_filters('editable_roles', $wp_roles->roles);    // exclude restricted roles if any        
        if (isset($roles[$primary_role])) { // exclude role assigned to the user as a primary role
            unset($roles[$primary_role]);
        }
        $other_roles = $this->get_roles_array($user);
                
        echo '<select multiple="multiple" id="ure_select_other_roles" name="ure_select_other_roles" style="width: 500px;" >'."\n";
        foreach($roles as $key=>$role) {
            echo '<option value="'.$key.'" >'.$role['name'].'</option>'."\n";
        }   // foreach()
        echo '</select><br>'."\n";
                
        if (is_array($other_roles) && count($other_roles) > 0) {
            $other_roles_str = implode(',', $other_roles);
        } else {
            $other_roles_str = '';
        }
            echo '<input type="hidden" name="ure_other_roles" id="ure_other_roles" value="' . $other_roles_str . '" />';
        
        
        $output = $this->lib->roles_text($other_roles);        
        echo '<span id="ure_other_roles_list">'. $output .'</span>';
    }
    // end of roles_select()    
    
    
    private function user_profile_capabilities($user) {
        global $current_user;
        
        $user_caps = $this->lib->get_edited_user_caps($user);
?>
          <tr>
              <th>
                  <?php esc_html_e('Capabilities', 'user-role-editor'); ?>
              </th>    
              <td>
<?php 
                echo $user_caps .'<br/>'; 
      if ($this->lib->user_is_admin($current_user->ID)) {
            echo '<a href="' . wp_nonce_url("users.php?page=users-".URE_PLUGIN_FILE."&object=user&amp;user_id={$user->ID}", "ure_user_{$user->ID}") . '">' . 
                 esc_html__('Edit', 'user-role-editor') . '</a>';
      }                      
?>
              </td>
          </tr>    
<?php                
    }
    // end of user_profile_capabilities()
    
    
    private function display($user, $context) {
?>
        <table class="form-table">
        		<tr>
        			<th scope="row"><?php esc_html_e('Other Roles', 'user-role-editor'); ?></th>
        			<td>
<?php
            $this->roles_select_html($user);            
?>
        			</td>
        		</tr>
<?php
    if ($context=='user-edit') {
        $this->user_profile_capabilities($user);
    }
?>
        </table>		
        <?php
        
    }
    // end of display()
    
    
    /**
     * Add URE stuff to the edit user profile page
     * 
     * @global object $current_user
     * @param object $user
     * @return void
     */
    public function edit_user_profile_html($user) {
                
        if (!$this->lib->is_user_profile_extention_allowed()) {  
            return;
        }
        $show = apply_filters('ure_show_additional_capabilities_section', true);
        if (empty($show)) {
            return;
        }
?>
        <h3><?php esc_html_e('Additional Capabilities', 'user-role-editor'); ?></h3>
<?php
        $this->display($user, 'user-edit');
    }
    // end of edit_user_profile()

    
    public function user_new_form($context) {
        $show = apply_filters('ure_show_additional_capabilities_section', true);
        if (empty($show)) {
            return;
        }
        
        $user = new WP_User();
?>
        <table>
<?php            
        $this->display($user, $context);
?>            
        </table>
<?php        
    }
    // end of edit_user_profile_html()
    
    
    // save additional user roles when user profile is updated, as WordPress itself doesn't know about them
    public function update($user_id) {

        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        $user = get_userdata($user_id);

        if (empty($_POST['ure_other_roles'])) {
            return false;
        }
        
        $ure_other_roles = explode(',', str_replace(' ', '', $_POST['ure_other_roles']));
        $new_roles = array_intersect($user->roles, $ure_other_roles);
        $skip_roles = array();
        foreach ($new_roles as $role) {
            $skip_roles['$role'] = 1;
        }
        unset($new_roles);
        foreach ($ure_other_roles as $role) {
            if (!isset($skip_roles[$role])) {
                $user->add_role($role);
            }
        }
        
        return true;        
    }
    // end of update()

    
    private function add_default_other_roles($user_id) {
        $user = get_user_by('id', $user_id);
        if (empty($user->ID)) {
            return;
        }

        // Get default roles if any
        $other_default_roles = $this->lib->get_option('other_default_roles', array());
        if (count($other_default_roles) == 0) {
            return;
        }
        foreach ($other_default_roles as $role) {
            if (!isset($user->caps[$role])) {
                $user->add_role($role);
            }
        }
    }

    // end of add_default_other_roles()


    public function add_other_roles($user_id) {

        if (empty($user_id)) {
            return;
        }

        $result = $this->update($user_id);
        if ($result) {    // roles were assigned manually
            return;
        }

        $this->add_default_other_roles($user_id);
    }
    // end of add_other_roles()    
    
    
    
}
// end of URE_User_Other_Roles class
