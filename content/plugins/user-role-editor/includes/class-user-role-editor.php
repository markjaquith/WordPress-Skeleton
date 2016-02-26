<?php
/*
 * Main class of User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v2+
 * 
 */

class User_Role_Editor {
    // plugin specific library object: common code stuff, including options data processor
    protected $lib = null;
    
    // work with user multiple roles class
    protected $user_other_roles = null;
    
    // plugin's Settings page reference, we've got it from add_options_pages() call
    protected $setting_page_hook = null;
    // URE's key capability
    public $key_capability = 'not allowed';
	
    // URE pages hook suffixes
    protected $ure_hook_suffixes = null;
    
    /**
     * class constructor
     */
    public function __construct() {

        if (empty($this->lib)) {
            $this->lib = URE_Lib::get_instance('user_role_editor');
        }

        $this->user_other_roles = new URE_User_Other_Roles($this->lib);
        
        if ($this->lib->is_pro()) {
         $this->ure_hook_suffixes = array('settings_page_settings-user-role-editor-pro', 'users_page_users-user-role-editor-pro');         
        } else {
         $this->ure_hook_suffixes = array('settings_page_settings-user-role-editor', 'users_page_users-user-role-editor');
        }
        
        // activation action
        register_activation_hook(URE_PLUGIN_FULL_PATH, array($this, 'setup'));

        // deactivation action
        register_deactivation_hook(URE_PLUGIN_FULL_PATH, array($this, 'cleanup'));
        		
        // Who can use this plugin
        $this->key_capability = $this->lib->get_key_capability();
                
        // Process URE's internal tasks queue
        $task_queue = URE_Task_Queue::get_instance();
        $task_queue->process();
        
        $this->set_hooks();        
        
    }
    // end of __construct()
            
    
    private function set_hooks() {
        if ($this->lib->multisite) {
            // new blog may be registered not at admin back-end only but automatically after new user registration, e.g. 
            // Gravity Forms User Registration Addon does
            add_action( 'wpmu_new_blog', array($this, 'duplicate_roles_for_new_blog'), 10, 2);                        
        }
        
        // setup additional options hooks for the roles
        add_action('init', array($this, 'set_role_additional_options_hooks'), 9);
        
        if (!is_admin()) {
            return;
        }
        
        add_action('admin_init', array($this, 'plugin_init'), 1);

        // Add the translation function after the plugins loaded hook.
        add_action('plugins_loaded', array($this, 'load_translation'));

        // add own submenu 
        add_action('admin_menu', array($this, 'plugin_menu'));
      		
        if ($this->lib->multisite) {
            // add own submenu 
            add_action('network_admin_menu', array($this, 'network_plugin_menu'));
        }


        // add a Settings link in the installed plugins page
        add_filter('plugin_action_links_'. URE_PLUGIN_BASE_NAME, array($this, 'plugin_action_links'), 10, 1);
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);    
    }
    // end of set_hooks()
    
    
    /**
     * True - if it's an instance of Pro version, false - for free version
     * @return boolean
     */    
    public function is_pro() {
        
        return $this->lib->is_pro();
    }
    // end of is_pro()
    
    
    /**
     * Plugin initialization
     * 
     */
    public function plugin_init() {

        global $current_user, $pagenow;

        if (!empty($current_user->ID)) {
            $user_id = $current_user->ID;
        } else {
            $user_id = 0;
        }

        $supress_protection = apply_filters('ure_supress_administrators_protection', false);
        // these filters and actions should prevent editing users with administrator role
        // by other users with 'edit_users' capability
        if (!$supress_protection && !$this->lib->user_is_admin($user_id)) {
            new URE_Protect_Admin($this->lib);
        }

        add_action('admin_enqueue_scripts', array($this, 'admin_load_js'));
        add_action('user_row_actions', array($this, 'user_row'), 10, 2);
        add_filter('all_plugins', array($this, 'exclude_from_plugins_list'));

        if ($this->lib->multisite) {
            $allow_edit_users_to_not_super_admin = $this->lib->get_option('allow_edit_users_to_not_super_admin', 0);
            if ($allow_edit_users_to_not_super_admin) {
                add_filter('map_meta_cap', array($this, 'restore_users_edit_caps'), 1, 4);
                remove_all_filters('enable_edit_any_user_configuration');
                add_filter('enable_edit_any_user_configuration', '__return_true');
                add_filter('admin_head', array($this, 'edit_user_permission_check'), 1, 4);
                if ($pagenow == 'user-new.php') {
                    add_filter('site_option_site_admins', array($this, 'allow_add_user_as_superadmin'));
                }
            }
        } else {
            $count_users_without_role = $this->lib->get_option('count_users_without_role', 0);
            if ($count_users_without_role) {
                add_action('restrict_manage_users', array($this, 'move_users_from_no_role_button'));
                add_action('admin_init', array($this, 'add_css_to_users_page'));
                add_action('admin_footer', array($this, 'add_js_to_users_page'));
            }
        }

        add_action('wp_ajax_ure_ajax', array($this, 'ure_ajax'));
    }
    // end of plugin_init()
    

    /**
   * Allow non-superadmin user to add/create users to the site as superadmin does.
   * Include current user to the list of superadmins - for the user-new.php page only, and 
   * if user really can create_users and promote_users
   * @global string $page
   * @param array $site_admins
   * @return array
   */
  public function allow_add_user_as_superadmin($site_admins) {
  
      global $pagenow, $current_user;
      
      $this->lib->raised_permissions = false;
      
      if ($pagenow!=='user-new.php') {
          return $site_admins;
      }
      
      // Check if current user really can create and promote users
      remove_filter('site_option_site_admins', array($this, 'allow_add_user_as_superadmin'));
      $can_add_user = current_user_can('create_users') && current_user_can('promote_users');
      add_filter('site_option_site_admins', array($this, 'allow_add_user_as_superadmin'));
      
      if (!$can_add_user) {
          return $site_admins; // no help in this case
      }
              
      if (!in_array($current_user->user_login, $site_admins)) {
          $this->lib->raised_permissions = true;
          $site_admins[] = $current_user->user_login;
      }
      
      return $site_admins;
      
  }
  // end of allow_add_user_as_superadmin()
  
  
  public function move_users_from_no_role_button() {
                  
      if ( stripos($_SERVER['REQUEST_URI'], 'wp-admin/users.php')===false ) {
            return;
      }
      
      $assign_role = $this->lib->get_assign_role();
      $users_quant = $assign_role->count_users_without_role();      
      if ($users_quant>0) {
?>          
        &nbsp;&nbsp;<input type="button" name="move_from_no_role" id="move_from_no_role" class="button"
                        value="Without role (<?php echo $users_quant;?>)" onclick="ure_move_users_from_no_role_dialog()">
        <div id="move_from_no_role_dialog" class="ure-dialog">
            <div id="move_from_no_role_content" style="padding: 10px;">
                To: <select name="ure_new_role" id="ure_new_role">
                    <option value="no_rights">No rights</option>
                </select><br>    
            </div>                
        </div>
<?php        
      }
      
  }
  // end of move_users_from_no_role()
  
  
  public function add_css_to_users_page() {
      if ( stripos($_SERVER['REQUEST_URI'], 'wp-admin/users.php')===false ) {
            return;
      }
      if (isset($_GET['page'])) {
          return;
      }

      wp_enqueue_style('wp-jquery-ui-dialog');
      wp_enqueue_style('ure-admin-css', URE_PLUGIN_URL . 'css/ure-admin.css', array(), false, 'screen');
      
  }
  // end of add_css_to_users_page()
  
  
  public function add_js_to_users_page() {
  
      if ( stripos($_SERVER['REQUEST_URI'], 'wp-admin/users.php')===false ) {
            return;
      }      
      if (isset($_GET['page'])) {
          return;
      }
      
      wp_enqueue_script('jquery-ui-dialog', false, array('jquery-ui-core','jquery-ui-button', 'jquery') );
      wp_register_script( 'ure-users-js', plugins_url( '/js/ure-users.js', URE_PLUGIN_FULL_PATH ) );
      wp_enqueue_script ( 'ure-users-js' );      
      wp_localize_script( 'ure-users-js', 'ure_users_data', array(
        'wp_nonce' => wp_create_nonce('user-role-editor'),
        'move_from_no_role_title' => esc_html__('Change role for users without role', 'user-role-editor'),
        'no_rights_caption' => esc_html__('No rights', 'user-role-editor'),  
        'provide_new_role_caption' => esc_html__('Provide new role', 'user-role-editor')
              ));
      
  }
  // end of add_js_to_users_page()
  
  
  /**
   * restore edit_users, delete_users, create_users capabilities for non-superadmin users under multisite
   * (code is provided by http://wordpress.org/support/profile/sjobidoo)
   * 
   * @param type $caps
   * @param type $cap
   * @param type $user_id
   * @param type $args
   * @return type
   */
  public function restore_users_edit_caps($caps, $cap, $user_id, $args) {

        foreach ($caps as $key => $capability) {

            if ($capability != 'do_not_allow')
                continue;

            switch ($cap) {
                case 'edit_user':
                case 'edit_users':
                    $caps[$key] = 'edit_users';
                    break;
                case 'delete_user':
                case 'delete_users':
                    $caps[$key] = 'delete_users';
                    break;
                case 'create_users':
                    $caps[$key] = $cap;
                    break;
            }
        }

        return $caps;
    }
    // end of restore_user_edit_caps()
    
    
    /**
     * Checks that both the editing user and the user being edited are
     * members of the blog and prevents the super admin being edited.
     * (code is provided by http://wordpress.org/support/profile/sjobidoo)
     * 
     */
    function edit_user_permission_check() {
        global $current_user, $profileuser;

        if (is_super_admin()) { // Superadmin may do all
            return;
        }
        
        $screen = get_current_screen();

        get_currentuserinfo();

        if ($screen->base == 'user-edit' || $screen->base == 'user-edit-network') { // editing a user profile
            if (!is_super_admin($current_user->ID) && is_super_admin($profileuser->ID)) { // trying to edit a superadmin while himself is less than a superadmin
                wp_die(esc_html__('You do not have permission to edit this user.'));
            } elseif (!( is_user_member_of_blog($profileuser->ID, get_current_blog_id()) && is_user_member_of_blog($current_user->ID, get_current_blog_id()) )) { // editing user and edited user aren't members of the same blog
                wp_die(esc_html__('You do not have permission to edit this user.'));
            }
        }
    }
    // end of edit_user_permission_check()
    
    
  /**
   * Add/hide edit actions for every user row at the users list
   * 
   * @global type $pagenow
   * @global type $current_user
   * @param string $actions
   * @param type $user
   * @return string
   */
    public function user_row($actions, $user) {

        global $pagenow, $current_user;

        if ($pagenow == 'users.php') {
            if ($current_user->has_cap($this->key_capability)) {
                $actions['capabilities'] = '<a href="' .
                        wp_nonce_url("users.php?page=users-" . URE_PLUGIN_FILE . "&object=user&amp;user_id={$user->ID}", "ure_user_{$user->ID}") .
                        '">' . esc_html__('Capabilities', 'user-role-editor') . '</a>';
            }
        }

        return $actions;
    }

    // end of user_row()

  
    /**
   * every time when new blog created - duplicate to it roles from the main blog (1)  
   * @global wpdb $wpdb
   * @global WP_Roles $wp_roles
   * @param int $blog_id
   * @param int $user_id
   *
   */
  public function duplicate_roles_for_new_blog($blog_id) 
  {
  
    global $wpdb, $wp_roles;
    
    // get Id of 1st (main) blog
    $main_blog_id = $this->lib->get_main_blog_id();
    if ( empty($main_blog_id) ) {
      return;
    }
    $current_blog = $wpdb->blogid;
    switch_to_blog( $main_blog_id );
    $main_roles = new WP_Roles();  // get roles from primary blog
    $default_role = get_option('default_role');  // get default role from primary blog
    switch_to_blog($blog_id);  // switch to the new created blog
    $main_roles->use_db = false;  // do not touch DB
    $main_roles->add_cap('administrator', 'dummy_123456');   // just to save current roles into new blog
    $main_roles->role_key = $wp_roles->role_key;
    $main_roles->use_db = true;  // save roles into new blog DB
    $main_roles->remove_cap('administrator', 'dummy_123456');  // remove unneeded dummy capability
    update_option('default_role', $default_role); // set default role for new blog as it set for primary one
    switch_to_blog($current_blog);  // return to blog where we were at the begin
  }
  // end of duplicate_roles_for_new_blog()

  
  /** 
   * Filter out URE plugin from not admin users to prevent its not authorized deactivation
   * @param type array $plugins plugins list
   * @return type array $plugins updated plugins list
   */
  public function exclude_from_plugins_list($plugins) {        

        // if multi-site, then allow plugin activation for network superadmins and, if that's specially defined, - for single site administrators too    
        if ($this->lib->multisite) { 
            if (is_super_admin() || $this->lib->user_is_admin()) {
                return $plugins;
            }
        } else {    
// is_super_admin() defines superadmin for not multisite as user who can 'delete_users' which I don't like. 
// So let's check if user has 'administrator' role better.
            if (current_user_can('administrator') || $this->lib->user_is_admin()) {
                return $plugins;
            }
        }

        // exclude URE from plugins list
        $key = basename(URE_PLUGIN_DIR) .'/'. URE_PLUGIN_FILE;
        unset($plugins[$key]);        

        return $plugins;
    }
    // end of exclude_from_plugins_list()

  
    /**
     * Load plugin translation files - linked to the 'plugins_loaded' action
     * 
     */
    function load_translation() {

        load_plugin_textdomain('user-role-editor', '', dirname( plugin_basename( URE_PLUGIN_FULL_PATH ) ) .'/lang');
        
    }
    // end of ure_load_translation()

    
    /**
     * Modify plugin action links
     * 
     * @param array $links
     * @return array
     */
    public function plugin_action_links($links) {

        $settings_link = "<a href='options-general.php?page=settings-" . URE_PLUGIN_FILE . "'>" . esc_html__('Settings', 'user-role-editor') . "</a>";
        array_unshift($links, $settings_link);

        return $links;
    }
    // end of plugin_action_links()


    public function plugin_row_meta($links, $file) {

        if ($file == plugin_basename(dirname(URE_PLUGIN_FULL_PATH) .'/'.URE_PLUGIN_FILE)) {
            $links[] = '<a target="_blank" href="http://role-editor.com/changelog">' . esc_html__('Changelog', 'user-role-editor') . '</a>';
        }

        return $links;
    }

    // end of plugin_row_meta
    
    
    public function settings_screen_configure() {
        $settings_page_hook = $this->settings_page_hook;
        if (is_multisite()) {
            $settings_page_hook .= '-network';
        }
        $screen = get_current_screen();
        // Check if current screen is URE's settings page
        if ($screen->id != $settings_page_hook) {
            return;
        }
        $screen_help = new Ure_Screen_Help();
        $screen->add_help_tab( array(
            'id'	=> 'general',
            'title'	=> esc_html__('General', 'user-role-editor'),
            'content'	=> $screen_help->get_settings_help('general')
            ));
        if ($this->lib->is_pro() || !$this->lib->multisite) {
            $screen->add_help_tab( array(
                'id'	=> 'additional_modules',
                'title'	=> esc_html__('Additional Modules', 'user-role-editor'),
                'content'	=> $screen_help->get_settings_help('additional_modules')
                ));
        }
        $screen->add_help_tab( array(
            'id'	=> 'default_roles',
            'title'	=> esc_html__('Default Roles', 'user-role-editor'),
            'content'	=> $screen_help->get_settings_help('default_roles')
            ));
        if ($this->lib->multisite) {
            $screen->add_help_tab( array(
                'id'	=> 'multisite',
                'title'	=> esc_html__('Multisite', 'user-role-editor'),
                'content'	=> $screen_help->get_settings_help('multisite')
                ));
        }
    }
    // end of settings_screen_configure()
    
    
    public function plugin_menu() {

        $translated_title = esc_html__('User Role Editor', 'user-role-editor');
        if (function_exists('add_submenu_page')) {
            $ure_page = add_submenu_page(
                    'users.php', 
                    $translated_title,
                    $translated_title,
                    $this->key_capability, 
                    'users-' . URE_PLUGIN_FILE, 
                    array($this, 'edit_roles'));
            add_action("admin_print_styles-$ure_page", array($this, 'admin_css_action'));
        }

        if ( !$this->lib->multisite || ($this->lib->multisite && !$this->lib->active_for_network) ) {
            $settings_capability = $this->lib->get_settings_capability();
            $this->settings_page_hook = add_options_page(
                    $translated_title,
                    $translated_title,
                    $settings_capability, 
                    'settings-' . URE_PLUGIN_FILE, 
                    array($this, 'settings'));
            add_action( 'load-'.$this->settings_page_hook, array($this,'settings_screen_configure') );
            add_action("admin_print_styles-{$this->settings_page_hook}", array($this, 'admin_css_action'));
        }
    }
    // end of plugin_menu()


    public function network_plugin_menu() {        
        if (is_multisite()) {
            $translated_title = esc_html__('User Role Editor', 'user-role-editor');
            $this->settings_page_hook = add_submenu_page(
                    'settings.php', 
                    $translated_title,
                    $translated_title, 
                    $this->key_capability, 
                    'settings-' . URE_PLUGIN_FILE, 
                    array(&$this, 'settings'));
            add_action( 'load-'.$this->settings_page_hook, array($this,'settings_screen_configure') );
            add_action("admin_print_styles-{$this->settings_page_hook}", array($this, 'admin_css_action'));
        }
        
    }

    // end of network_plugin_menu()

    
    protected function get_settings_action() {

        $action = 'show';
        $update_buttons = array('ure_settings_update', 'ure_addons_settings_update', 'ure_settings_ms_update', 'ure_default_roles_update');
        foreach($update_buttons as $update_button) {
            if (!isset($_POST[$update_button])) {
                continue;
            }
            if (!wp_verify_nonce($_POST['_wpnonce'], 'user-role-editor')) {
                wp_die('Security check failed');
            }
            $action = $update_button;
            break;            
        }

        return $action;

    }
    // end of get_settings_action()

    /**
     * Update General Options tab
     */
    protected function update_general_options() {
        if (defined('URE_SHOW_ADMIN_ROLE') && (URE_SHOW_ADMIN_ROLE == 1)) {
            $show_admin_role = 1;
        } else {
            $show_admin_role = $this->lib->get_request_var('show_admin_role', 'checkbox');
        }
        $this->lib->put_option('show_admin_role', $show_admin_role);

        $caps_readable = $this->lib->get_request_var('caps_readable', 'checkbox');
        $this->lib->put_option('ure_caps_readable', $caps_readable);

        $show_deprecated_caps = $this->lib->get_request_var('show_deprecated_caps', 'checkbox');
        $this->lib->put_option('ure_show_deprecated_caps', $show_deprecated_caps);       
        
        $confirm_role_update = $this->lib->get_request_var('confirm_role_update', 'checkbox');
        $this->lib->put_option('ure_confirm_role_update', $confirm_role_update);
        
        $edit_user_caps = $this->lib->get_request_var('edit_user_caps', 'checkbox');
        $this->lib->put_option('edit_user_caps', $edit_user_caps);       
        
        do_action('ure_settings_update1');

        $this->lib->flush_options();
        $this->lib->show_message(esc_html__('User Role Editor options are updated', 'user-role-editor'));
        
    }
    // end of update_general_options()

    
    /**
     * Update Additional Modules Options tab
     */
    protected function update_addons_options() {
        
        if (!$this->lib->multisite) {
            $count_users_without_role = $this->lib->get_request_var('count_users_without_role', 'checkbox');
            $this->lib->put_option('count_users_without_role', $count_users_without_role);
        }
        do_action('ure_settings_update2');
        
        $this->lib->flush_options();
        $this->lib->show_message(esc_html__('User Role Editor options are updated', 'user-role-editor'));
    }
    // end of update_addons_options()
    
    
    protected function update_default_roles() {
        global $wp_roles;    
        
        // Primary default role
        $primary_default_role = $this->lib->get_request_var('default_user_role', 'post');
        if (!empty($primary_default_role) && isset($wp_roles->role_objects[$primary_default_role]) && $primary_default_role !== 'administrator') {
            update_option('default_role', $primary_default_role);
        }
                
        // Other default roles
        $other_default_roles = array();
        foreach($_POST as $key=>$value) {
            $prefix = substr($key, 0, 8);
            if ($prefix!=='wp_role_') {
                continue;
            }
            $role_id = substr($key, 8);
            if ($role_id!=='administrator' && isset($wp_roles->role_objects[$role_id])) {
                $other_default_roles[] = $role_id;
            }            
        }  // foreach()
        $this->lib->put_option('other_default_roles', $other_default_roles, true);
        
        $this->lib->show_message(esc_html__('Default Roles are updated', 'user-role-editor'));
    }
    // end of update_default_roles()
    
    
    protected function update_multisite_options() {
        if (!$this->lib->multisite) {
            return;
        }

        $allow_edit_users_to_not_super_admin = $this->lib->get_request_var('allow_edit_users_to_not_super_admin', 'checkbox');
        $this->lib->put_option('allow_edit_users_to_not_super_admin', $allow_edit_users_to_not_super_admin);        
        
        do_action('ure_settings_ms_update');

        $this->lib->flush_options();
        $this->lib->show_message(esc_html__('User Role Editor options are updated', 'user-role-editor'));
        
    }
    // end of update_multisite_options()
    

    public function settings() {
        $settings_capability = $this->lib->get_settings_capability();
        if (!current_user_can($settings_capability)) {
            wp_die(esc_html__( 'You do not have sufficient permissions to manage options for User Role Editor.', 'user-role-editor' ));
        }
        $action = $this->get_settings_action();
        switch ($action) {
            case 'ure_settings_update':
                $this->update_general_options();
                break;
            case 'ure_addons_settings_update':
                $this->update_addons_options();
                break;
            case 'ure_settings_ms_update':
                $this->update_multisite_options();
                break;
            case 'ure_default_roles_update':
                $this->update_default_roles();
            case 'show':
            default:                
            ;
        } // switch()
                        
        if (defined('URE_SHOW_ADMIN_ROLE') && (URE_SHOW_ADMIN_ROLE == 1)) {
            $show_admin_role = 1;
        } else {
            $show_admin_role = $this->lib->get_option('show_admin_role', 0);
        }
        $caps_readable = $this->lib->get_option('ure_caps_readable', 0);
        $show_deprecated_caps = $this->lib->get_option('ure_show_deprecated_caps', 0);
        $confirm_role_update = $this->lib->get_option('ure_confirm_role_update', 1);
        $edit_user_caps = $this->lib->get_option('edit_user_caps', 1);
                
        if ($this->lib->multisite) {
            $allow_edit_users_to_not_super_admin = $this->lib->get_option('allow_edit_users_to_not_super_admin', 0);
        } else {
            $count_users_without_role = $this->lib->get_option('count_users_without_role', 0);
        }
        
        $this->lib->get_default_role();
        //$this->lib->editor_init1();
        $this->lib->role_default_prepare_html(0);
        
        $ure_tab_idx = $this->lib->get_request_var('ure_tab_idx', 'int');
                
        do_action('ure_settings_load');        

        if ($this->lib->multisite && is_network_admin()) {
            $link = 'settings.php';
        } else {
            $link = 'options-general.php';
        }
        
        $license_key_only = $this->lib->multisite && is_network_admin() && !$this->lib->active_for_network;

        
        require_once(URE_PLUGIN_DIR . 'includes/settings-template.php');
    }
    // end of settings()


    public function admin_css_action() {

        wp_enqueue_style('wp-jquery-ui-dialog');         
        if (stripos($_SERVER['REQUEST_URI'], 'settings-user-role-editor')!==false) {
            wp_enqueue_style('ure-jquery-ui-tabs', URE_PLUGIN_URL . 'css/jquery-ui-1.10.4.custom.min.css', array(), false, 'screen');
        }
        wp_enqueue_style('ure-admin-css', URE_PLUGIN_URL . 'css/ure-admin.css', array(), false, 'screen');
    }
    // end of admin_css_action()
    
    
    // call roles editor page
    public function edit_roles() {

        if (!current_user_can($this->key_capability)) {
            wp_die(esc_html__('Insufficient permissions to work with User Role Editor', 'user-role-editor'));
        }

        $this->lib->editor();
    }
    // end of edit_roles()
	

    /**
     *  execute on plugin activation
     */
    function setup() {

        $this->lib->make_roles_backup();
        $this->lib->init_ure_caps();
        
        $task_queue = URE_Task_Queue::get_instance();
        $task_queue->add('on_activation');
                
    }
    // end of setup()
                

    /**
     * Load plugin javascript stuff
     * 
     * @param string $hook_suffix
     */
    public function admin_load_js($hook_suffix) {

        URE_Known_JS_CSS_Compatibility_Issues::fix($hook_suffix, $this->ure_hook_suffixes);                
        
        if (!in_array($hook_suffix, $this->ure_hook_suffixes)) {
            return;
        }
        
        $confirm_role_update = $this->lib->get_option('ure_confirm_role_update', 1);
        
        wp_enqueue_script('jquery-ui-dialog', false, array('jquery-ui-core', 'jquery-ui-button', 'jquery'));
        wp_enqueue_script('jquery-ui-tabs', false, array('jquery-ui-core', 'jquery'));
        wp_register_script('ure-js', plugins_url('/js/ure-js.js', URE_PLUGIN_FULL_PATH));
        wp_enqueue_script('ure-js');
        wp_localize_script('ure-js', 'ure_data', array(
            'wp_nonce' => wp_create_nonce('user-role-editor'),
            'page_url' => URE_WP_ADMIN_URL . URE_PARENT . '?page=users-' . URE_PLUGIN_FILE,
            'is_multisite' => is_multisite() ? 1 : 0,
            'confirm_role_update' => $confirm_role_update ? 1 : 0,
            'confirm_title' => esc_html__('Confirm', 'user-role-editor'),
            'yes_label' => esc_html__('Yes', 'user-role-editor'),
            'no_label' => esc_html__('No', 'user-role-editor'),
            'select_all' => esc_html__('Select All', 'user-role-editor'),
            'unselect_all' => esc_html__('Unselect All', 'user-role-editor'),
            'reverse' => esc_html__('Reverse', 'user-role-editor'),
            'update' => esc_html__('Update', 'user-role-editor'),
            'confirm_submit' => esc_html__('Please confirm permissions update', 'user-role-editor'),
            'add_new_role_title' => esc_html__('Add New Role', 'user-role-editor'),
            'rename_role_title' => esc_html__('Rename Role', 'user-role-editor'),
            'role_name_required' => esc_html__(' Role name (ID) can not be empty!', 'user-role-editor'),
            'role_name_valid_chars' => esc_html__(' Role name (ID) must contain latin characters, digits, hyphens or underscore only!', 'user-role-editor'),
            'numeric_role_name_prohibited' => esc_html__(' WordPress does not support numeric Role name (ID). Add latin characters to it.', 'user-role-editor'),
            'add_role' => esc_html__('Add Role', 'user-role-editor'),
            'rename_role' => esc_html__('Rename Role', 'user-role-editor'),
            'delete_role' => esc_html__('Delete Role', 'user-role-editor'),
            'cancel' => esc_html__('Cancel', 'user-role-editor'),
            'add_capability' => esc_html__('Add Capability', 'user-role-editor'),
            'delete_capability' => esc_html__('Delete Capability', 'user-role-editor'),
            'reset' => esc_html__('Reset', 'user-role-editor'),
            'reset_warning' => esc_html__('DANGER! Resetting will restore default settings from WordPress Core.', 'user-role-editor') . "\n\n" .
            esc_html__('If any plugins have changed capabilities in any way upon installation (such as S2Member, WooCommerce, and many more), those capabilities will be DELETED!', 'user-role-editor') . "\n\n" .
            esc_html__('For more information on how to undo changes and restore plugin capabilities go to', 'user-role-editor') . "\n" .
            'http://role-editor.com/how-to-restore-deleted-wordpress-user-roles/' . "\n\n" .
            esc_html__('Continue?', 'user-role-editor'),
            'default_role' => esc_html__('Default Role', 'user-role-editor'),
            'set_new_default_role' => esc_html__('Set New Default Role', 'user-role-editor'),
            'delete_capability' => esc_html__('Delete Capability', 'user-role-editor'),
            'delete_capability_warning' => esc_html__('Warning! Be careful - removing critical capability could crash some plugin or other custom code', 'user-role-editor'),
            'capability_name_required' => esc_html__(' Capability name (ID) can not be empty!', 'user-role-editor'),
            'capability_name_valid_chars' => esc_html__(' Capability name (ID) must contain latin characters, digits, hyphens or underscore only!', 'user-role-editor'),
        ));
        
        // load additional JS stuff for Pro version, if exists
        do_action('ure_load_js');

    }
    // end of admin_load_js()
                       
    
    public function ure_ajax() {
                
        $ajax_processor = new URE_Ajax_Processor($this->lib);
        $ajax_processor->dispatch();
        
    }
    // end of ure_ajax()

    
    public function set_role_additional_options_hooks() {

        $role_additional_options = URE_Role_Additional_Options::get_instance($this->lib);
        $role_additional_options->set_active_items_hooks();
        
    }
    // end of set_role_additional_options_hooks()
    
    
    // execute on plugin deactivation
    function cleanup() {
		
    }
    // end of setup()
        
 
}
// end of User_Role_Editor
