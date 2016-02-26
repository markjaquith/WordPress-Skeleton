<?php
/*
 * Stuff specific for User Role Editor WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: vladimir@shinephp.com
 * Author URI: http://shinephp.com
 * 
*/


/**
 * This class contains general stuff for usage at WordPress plugins
 */
class Ure_Lib extends URE_Base_Lib {

	public $roles = null;     
	public $notification = '';   // notification message to show on page
	public $apply_to_all = 0; 

   
	protected $capabilities_to_save = null; 
	protected $current_role = '';
	protected $wp_default_role = '';
	protected $current_role_name = '';  
	protected $user_to_edit = ''; 
	protected $show_deprecated_caps = false; 
	protected $caps_readable = false;
	protected $hide_pro_banner = false;	
	protected $full_capabilities = false;
	public $ure_object = 'role';  // what to process, 'role' or 'user'  
	public    $role_default_html = '';
	protected $role_to_copy_html = '';
	protected $role_select_html = '';
	protected $role_delete_html = '';
	protected $capability_remove_html = '';
	protected $advert = null;
 protected $role_additional_options = null;
 protected $bbpress = null; // reference to the URE_bbPress class instance
 
 // when allow_edit_users_to_not_super_admin option is turned ON, we set this property to true 
 // when we raise single site admin permissions up to the superadmin for the 'Add new user' new-user.php page
 // User_Role_Editor::allow_add_user_as_superadmin()
 public $raised_permissions = false; 
 
 public $debug = false;
 
  
  
    /** class constructor
     * 
     * @param string $options_id
     * 
     */
    public function __construct($options_id) {
                                           
        parent::__construct($options_id); 
        $this->debug = defined('URE_DEBUG') && (URE_DEBUG==1 || URE_DEBUG==true);
 
        $this->bbpress = URE_bbPress::get_instance($this);
        
        $this->upgrade();
    }
    // end of __construct()

    
    public static function get_instance($options_id = '') {
        
        if (self::$instance === null) {
            if (empty($options_id)) {
                throw new Exception('URE_Lib::get_inctance() - Error: plugin options ID string is required');
            }
            // new static() will work too
            self::$instance = new URE_Lib($options_id);
        }

        return self::$instance;
    }
    // end of get_instance()
    
    
    protected function upgrade() {
        
        $ure_version = $this->get_option('ure_version', '0');
        if (version_compare( $ure_version, URE_VERSION, '<' ) ) {
            // for upgrade to 4.18 and higher from older versions
            $this->init_ure_caps();
            $this->put_option('ure_version', URE_VERSION, true);
        }
        
    }
    // end of upgrade()
    
    
    /**
     * Is this the Pro version?
     * @return boolean
     */ 
    public function is_pro() {
        return false;
    }
    // end of is_pro()    
    
    
    public function get_ure_object() {
        
        return $this->ure_object;
    }
    // end of get_ure_object();
    
    
    protected function get_ure_caps() {
        
        $ure_caps = array(
            'ure_edit_roles' => 1,
            'ure_create_roles' => 1,
            'ure_delete_roles' => 1,
            'ure_create_capabilities' => 1,
            'ure_delete_capabilities' => 1,
            'ure_manage_options' => 1,
            'ure_reset_roles' => 1
        );        
        
        return $ure_caps;
    }
    // end of get_ure_caps()
    
                    
    public function init_ure_caps() {
        global $wp_roles;
        
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        
        if (!isset($wp_roles->roles['administrator'])) {
            return;
        }
        
        // Do not turn on URE caps for local administrator by default under multisite, as there is a superadmin.
        $turn_on = !$this->multisite;   
        
        $old_use_db = $wp_roles->use_db;
        $wp_roles->use_db = true;
        $administrator = $wp_roles->role_objects['administrator'];
        $ure_caps = $this->get_ure_caps();
        foreach(array_keys($ure_caps) as $cap) {
            if (!$administrator->has_cap($cap)) {
                $administrator->add_cap($cap, $turn_on);
            }
        }
        $wp_roles->use_db = $old_use_db;        
    }
    // end of init_ure_caps()
    
        
    /**
     * get options for User Role Editor plugin
     * User Role Editor stores its options at the main blog/site only and applies them to the all network
     * 
     */
    protected function init_options($options_id) {
        
        global $wpdb;
        
        if ($this->multisite) { 
            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {    // Be sure the function is defined before trying to use it
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );                
            }
            $this->active_for_network = is_plugin_active_for_network(URE_PLUGIN_BASE_NAME);
        }
        $current_blog = $wpdb->blogid;
        if ($this->multisite && $current_blog!=$this->main_blog_id) {   
            if ($this->active_for_network) {   // plugin is active for whole network, so get URE options from the main blog
                switch_to_blog($this->main_blog_id);  
            }
        }
        
        $this->options_id = $options_id;
        $this->options = get_option($options_id);
        
        if ($this->multisite && $current_blog!=$this->main_blog_id) {
            if ($this->active_for_network) {   // plugin is active for whole network, so return back to the current blog
                restore_current_blog();
            }
        }

    }
    // end of init_options()
    
    
    /**
     * saves options array into WordPress database wp_options table
     */
    public function flush_options() {

        global $wpdb;
        
        $current_blog = $wpdb->blogid;
        if ($this->multisite && $current_blog!==$this->main_blog_id) {
            if ($this->active_for_network) {   // plugin is active for whole network, so get URE options from the main blog
                switch_to_blog($this->main_blog_id);  // in order to save URE options to the main blog
            }
        }
        
        update_option($this->options_id, $this->options);
        
        if ($this->multisite && $current_blog!==$this->main_blog_id) {            
            if ($this->active_for_network) {   // plugin is active for whole network, so return back to the current blog
                restore_current_blog();
            }
        }
        
    }
    // end of flush_options()
    
    
    public function get_main_blog_id() {
        
        return $this->main_blog_id;
        
    }
    

    /**
     * return key capability to have access to User Role Editor Plugin
     * 
     * @return string
     */
    public function get_key_capability() {
        
        if (!$this->multisite) {
            $key_capability = URE_KEY_CAPABILITY;
        } else {
            $enable_simple_admin_for_multisite = $this->get_option('enable_simple_admin_for_multisite', 0);
            if ( (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE == 1) || 
                 $enable_simple_admin_for_multisite) {
                $key_capability = URE_KEY_CAPABILITY;
            } else {
                $key_capability = 'manage_network_users';
            }
        }
                
        return $key_capability;
    }
    // end of get_key_capability()

    
    public function get_settings_capability() {
        
        if (!$this->multisite) {
                $settings_access = 'ure_manage_options';
        } else {
            $enable_simple_admin_for_multisite = $this->get_option('enable_simple_admin_for_multisite', 0);
            if ( (defined('URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE') && URE_ENABLE_SIMPLE_ADMIN_FOR_MULTISITE == 1) || 
                 $enable_simple_admin_for_multisite) {
                $settings_access = 'ure_manage_options';
            } else {
                $settings_access = $this->get_key_capability();
            }
        }
        
        return $settings_access;
    }
    // end of get_settings_capability()
    

    /**
     *  return front-end according to the context - role or user editor
     */
    public function editor() {

        if (!$this->editor_init0()) {
            $this->show_message(esc_html__('Error: wrong request', 'user-role-editor'));
            return false;
        }                
        $this->process_user_request();
        $this->editor_init1();
        $this->show_editor();
        
    }
    // end of editor()

    
    protected function advertisement() {

        if (!$this->is_pro()) {
            $this->advert = new ure_Advertisement();
            $this->advert->display();
        }
    }
    // end of advertisement()

    
    protected function output_role_edit_dialogs() {
?>        
<script language="javascript" type="text/javascript">

  var ure_current_role = '<?php echo $this->current_role; ?>';
  var ure_current_role_name  = '<?php echo $this->current_role_name; ?>';

</script>

<!-- popup dialogs markup -->
<div id="ure_add_role_dialog" class="ure-modal-dialog" style="padding: 10px;">
  <form id="ure_add_role_form" name="ure_add_role_form" method="POST">    
    <div class="ure-label"><?php esc_html_e('Role name (ID): ', 'user-role-editor'); ?></div>
    <div class="ure-input"><input type="text" name="user_role_id" id="user_role_id" size="25"/></div>
    <div class="ure-label"><?php esc_html_e('Display Role Name: ', 'user-role-editor'); ?></div>
    <div class="ure-input"><input type="text" name="user_role_name" id="user_role_name" size="25"/></div>
    <div class="ure-label"><?php esc_html_e('Make copy of: ', 'user-role-editor'); ?></div>
    <div class="ure-input"><?php echo $this->role_to_copy_html; ?></div>        
  </form>
</div>

<div id="ure_rename_role_dialog" class="ure-modal-dialog" style="padding: 10px;">
  <form id="ure_rename_role_form" name="ure_rename_role_form" method="POST">
    <div class="ure-label"><?php esc_html_e('Role name (ID): ', 'user-role-editor'); ?></div>
    <div class="ure-input"><input type="text" name="ren_user_role_id" id="ren_user_role_id" size="25" disabled /></div>
    <div class="ure-label"><?php esc_html_e('Display Role Name: ', 'user-role-editor'); ?></div>
    <div class="ure-input"><input type="text" name="ren_user_role_name" id="ren_user_role_name" size="25"/></div>    
  </form>
</div>

<div id="ure_delete_role_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php esc_html_e('Select Role:', 'user-role-editor');?></div>
    <div class="ure-input"><?php echo $this->role_delete_html; ?></div>
  </div>
</div>

<?php
if ($this->multisite && !is_network_admin()) {
?>
<div id="ure_default_role_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <?php echo $this->role_default_html; ?>
  </div>  
</div>
<?php
}
?>

<div id="ure_delete_capability_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php esc_html_e('Delete:', 'user-role-editor');?></div>
    <div class="ure-input"><?php echo $this->capability_remove_html; ?></div>
  </div>  
</div>

<div id="ure_add_capability_dialog" class="ure-modal-dialog">
  <div style="padding:10px;">
    <div class="ure-label"><?php esc_html_e('Capability name (ID): ', 'user-role-editor'); ?></div>
    <div class="ure-input"><input type="text" name="capability_id" id="capability_id" size="25"/></div>
  </div>  
</div>     

<?php        
        
    }
    // end of output_role_edit_dialogs()
    
    
    protected function output_user_caps_edit_dialogs() {
    

    }
    // end of output_user_caps_edit_dialogs()
    
    
    protected function output_confirmation_dialog() {
?>
<div id="ure_confirmation_dialog" class="ure-modal-dialog">
    <div id="ure_cd_html" style="padding:10px;"></div>
</div>
<?php
    }
    // end of output_confirmation_dialog()
    

    protected function show_editor() {
    $container_width = ($this->ure_object == 'user') ? 1400 : 1200;
    
    $this->show_message($this->notification);
?>
<div class="wrap">
		  <div id="ure-icon" class="icon32"><br/></div>
    <h2><?php _e('User Role Editor', 'user-role-editor'); ?></h2>
    <div id="ure_container" style="min-width: <?php echo $container_width;?>px;">
        <div class="ure-sidebar" >
            <?php
            $this->advertisement();
?>            
        </div>

        <div class="has-sidebar" >
            <form id="ure_form" method="post" action="<?php echo URE_WP_ADMIN_URL . URE_PARENT.'?page=users-'.URE_PLUGIN_FILE;?>" >			
                <div id="ure_form_controls">
<?php
                    wp_nonce_field('user-role-editor', 'ure_nonce');
                    if ($this->ure_object == 'user') {
                        require_once(URE_PLUGIN_DIR . 'includes/ure-user-edit.php');
                    } else {
                        $this->set_current_role();
                        $this->role_edit_prepare_html();
                        require_once(URE_PLUGIN_DIR . 'includes/ure-role-edit.php');
                    }
?>
                    <input type="hidden" name="action" value="update" />
                </div>      
            </form>		      
<?php	
    $this->advertise_pro_version();	
	
    if ($this->ure_object == 'role') {
        $this->output_role_edit_dialogs();
    } else {
        $this->output_user_caps_edit_dialogs();
    }
    do_action('ure_dialogs_html');
    
    $this->output_confirmation_dialog();
?>
        </div>          
    </div>
</div>
<?php
        
    }
    // end of show_editor()
    

	// content of User Role Editor Pro advertisement slot - for direct call
	protected function advertise_pro_version() {
		if ($this->is_pro()) {
			return;
		}
?>		
			<div id="ure_pro_advertisement" style="clear:left;display:block; float: left;">
				<a href="https://www.role-editor.com?utm_source=UserRoleEditor&utm_medium=banner&utm_campaign=Plugins " target="_new" >
<?php 
	if ($this->hide_pro_banner) {
		echo 'User Role Editor Pro: extended functionality, no advertisement - from $29.</a>';
	} else {
?>
					<img src="<?php echo URE_PLUGIN_URL;?>images/user-role-editor-pro-728x90.jpg" alt="User Role Editor Pro" 
						 title="More functionality and premium support with Pro version of User Role Editor."/>
				</a><br />
				<label for="ure_hide_pro_banner">
					<input type="checkbox" name="ure_hide_pro_banner" id="ure_hide_pro_banner" onclick="ure_hide_pro_banner();"/>&nbsp;Thanks, hide this banner.
				</label>
<?php 
	}
?>
			</div>  			
<?php		
		
	}
	// end of advertise_pro_version()
	
	
    // validate information about user we intend to edit
    protected function check_user_to_edit() {

        if ($this->ure_object == 'user') {
            if (!isset($_REQUEST['user_id'])) {
                return false; // user_id value is missed
            }
            $user_id = $_REQUEST['user_id'];
            if (!is_numeric($user_id)) {
                return false;
            }
            if (!$user_id) {
                return false;
            }
            $this->user_to_edit = get_user_to_edit($user_id);
            if (empty($this->user_to_edit)) {
                return false;
            }
        }
        
        return true;
    }
    // end of check_user_to_edit()
    
    
    protected function init_current_role_name() {
        
        if (!isset($this->roles[$_POST['user_role']])) {
            $mess = esc_html__('Error: ', 'user-role-editor') . esc_html__('Role', 'user-role-editor') . ' <em>' . esc_html($_POST['user_role']) . '</em> ' . 
                    esc_html__('does not exist', 'user-role-editor');
            $this->current_role = '';
            $this->current_role_name = '';
        } else {
            $this->current_role = $_POST['user_role'];
            $this->current_role_name = $this->roles[$this->current_role]['name'];
            $mess = '';
        }
        
        return $mess;
        
    }
    // end of init_current_role_name()

    
    /**
     *  prepare capabilities from user input to save at the database
     */
    protected function prepare_capabilities_to_save() {
        $this->capabilities_to_save = array();
        foreach ($this->full_capabilities as $available_capability) {
            $cap_id = str_replace(' ', URE_SPACE_REPLACER, $available_capability['inner']);
            if (isset($_POST[$cap_id])) {
                $this->capabilities_to_save[$available_capability['inner']] = true;
            }
        }
    }
    // end of prepare_capabilities_to_save()
    

    /**
     *  save changes to the roles or user
     *  @param string $mess - notification message to the user
     *  @return string - notification message to the user
     */
    protected function permissions_object_update($mess) {

        if ($this->ure_object == 'role') {  // save role changes to database
            if ($this->update_roles()) {
                if ($mess) {
                    $mess .= '<br/>';
                }
                if (!$this->apply_to_all) {
                    $mess = esc_html__('Role is updated successfully', 'user-role-editor');
                } else {
                    $mess = esc_html__('Roles are updated for all network', 'user-role-editor');
                }
            } else {
                if ($mess) {
                    $mess .= '<br/>';
                }
                $mess = esc_html__('Error occured during role(s) update', 'user-role-editor');
            }
        } else {
            if ($this->update_user($this->user_to_edit)) {
                if ($mess) {
                    $mess .= '<br/>';
                }
                $mess = esc_html__('User capabilities are updated successfully', 'user-role-editor');
            } else {
                if ($mess) {
                    $mess .= '<br/>';
                }
                $mess = esc_html__('Error occured during user update', 'user-role-editor');
            }
        }
        return $mess;
    }
    // end of permissions_object_update()

    
    /**
     * Process user request
     */
    protected function process_user_request() {

        $this->notification = '';
        if (isset($_POST['action'])) {
            if (empty($_POST['ure_nonce']) || !wp_verify_nonce($_POST['ure_nonce'], 'user-role-editor')) {
                echo '<h3>Wrong nonce. Action prohibitied.</h3>';
                exit;
            }

            $action = $_POST['action'];
            
            if ($action == 'reset') {
                $this->reset_user_roles();
                exit;
            } else if ($action == 'add-new-role') {
                // process new role create request
                $this->notification = $this->add_new_role();
            } else if ($action == 'rename-role') {
                // process rename role request
                $this->notification = $this->rename_role();    
            } else if ($action == 'delete-role') {
                $this->notification = $this->delete_role();
            } else if ($action == 'change-default-role') {
                $this->notification = $this->change_default_role();
            } else if ($action == 'caps-readable') {
                if ($this->caps_readable) {
                    $this->caps_readable = 0;					
                } else {
                    $this->caps_readable = 1;
                }
                set_site_transient( 'ure_caps_readable', $this->caps_readable, 600 );
            } else if ($action == 'show-deprecated-caps') {
                if ($this->show_deprecated_caps) {
                    $this->show_deprecated_caps = 0;
                } else {
                    $this->show_deprecated_caps = 1;
                }
                set_site_transient( 'ure_show_deprecated_caps', $this->show_deprecated_caps, 600 );
            } else if ($action == 'hide-pro-banner') {
                $this->hide_pro_banner = 1;
                $this->put_option('ure_hide_pro_banner', 1);	
                $this->flush_options();				
            } else if ($action == 'add-new-capability') {
                $this->notification = $this->add_new_capability();
            } else if ($action == 'delete-user-capability') {
                $this->notification = $this->delete_capability();
            } else if ($action == 'roles_restore_note') {
                $this->notification = esc_html__('User Roles are restored to WordPress default values. ', 'user-role-editor');
            } else if ($action == 'update') {
                $this->roles = $this->get_user_roles();
                $this->init_full_capabilities();
                if (isset($_POST['user_role'])) {
                    $this->notification = $this->init_current_role_name();                    
                }
                $this->prepare_capabilities_to_save();
                $this->notification = $this->permissions_object_update($this->notification);                                  
            } else {
                do_action('ure_process_user_request');
            } // if ($action
        }
        
    }
    // end of process_user_request()

	
	protected function set_apply_to_all() {
    if (isset($_POST['ure_apply_to_all'])) {
        $this->apply_to_all = 1;
    } else {
        $this->apply_to_all = 0;
    }
}
	// end of set_apply_to_all()
	

    public function get_default_role() {
        $this->wp_default_role = get_option('default_role');
    }
    // end of get_default_role()
    

    protected function editor_init0() {
        $this->caps_readable = get_site_transient('ure_caps_readable');
        if (false === $this->caps_readable) {
            $this->caps_readable = $this->get_option('ure_caps_readable');
            set_site_transient('ure_caps_readable', $this->caps_readable, 600);
        }
        $this->show_deprecated_caps = get_site_transient('ure_show_deprecated_caps');
        if (false === $this->show_deprecated_caps) {
            $this->show_deprecated_caps = $this->get_option('ure_show_deprecated_caps');
            set_site_transient('ure_caps_readable', $this->caps_readable, 600);
        }

        $this->hide_pro_banner = $this->get_option('ure_hide_pro_banner', 0);
        $this->get_default_role();

        // could be sent as by POST, as by GET
        if (isset($_REQUEST['object'])) {
            $this->ure_object = $_REQUEST['object'];
            if (!$this->check_user_to_edit()) {
                return false;
            }
        } else {
            $this->ure_object = 'role';
        }

        $this->set_apply_to_all();

        return true;
    }
    // end of editor_init0()


    public function editor_init1() {

        if (!isset($this->roles) || !$this->roles) {
            // get roles data from database
            $this->roles = $this->get_user_roles();
        }

        $this->init_full_capabilities();
        if (empty($this->role_additional_options)) {
            $this->role_additional_options = URE_Role_Additional_Options::get_instance($this);
        }
        
        if (!$this->is_pro()) {
            require_once(URE_PLUGIN_DIR . 'includes/class-advertisement.php');
        }
        
    }
    // end of editor_init1()


    /**
     * return id of role last in the list of sorted roles
     * 
     */
    protected function get_last_role_id() {
        
        // get the key of the last element in roles array
        $keys = array_keys($this->roles);
        $last_role_id = array_pop($keys);
        
        return $last_role_id;
    }
    // end of get_last_role_id()
    
    
    public function get_usermeta_table_name() {
        global $wpdb;
        
        $table_name = (!$this->multisite && defined('CUSTOM_USER_META_TABLE')) ? CUSTOM_USER_META_TABLE : $wpdb->usermeta;
        
        return $table_name;
    }
    // end of get_usermeta_table_name()
    
    
    /**
     * Check if user has "Administrator" role assigned
     * 
     * @global wpdb $wpdb
     * @param int $user_id
     * @return boolean returns true is user has Role "Administrator"
     */
    public function has_administrator_role($user_id) {
        global $wpdb;

        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }

        $table_name = $this->get_usermeta_table_name();
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
        $this->lib->user_to_check[$user_id] = $result;

        return $result;
    }

    // end of has_administrator_role()

  
    /**
     * Checks if user is allowed to use User Role Editor
     * 
     * @global int $current_user
     * @param int $user_id
     * @return boolean true 
     */
    public function user_is_admin($user_id = false) {
        global $current_user;

        $ure_key_capability = $this->get_key_capability();
        if (empty($user_id)) {                    
            $user_id = $current_user->ID;
        }
        $result = user_can($user_id, $ure_key_capability);
        
        return $result;
    }
    // end of user_is_admin()

        
    
  /**
     * return array with WordPress user roles
     * 
     * @global WP_Roles $wp_roles
     * @global type $wp_user_roles
     * @return array
     */
    public function get_user_roles() {

        global $wp_roles;

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }                

        if (!empty($this->bbpress)) {  // bbPress plugin is active
            $this->roles = $this->bbpress->get_roles();
        } else {
            $this->roles = $wp_roles->roles;
        }        
        
        if (is_array($this->roles) && count($this->roles) > 0) {
            asort($this->roles);
        }

        return $this->roles;
    }
    // end of get_user_roles()
     
/*    
    // restores User Roles from the backup record
    protected function restore_user_roles() 
    {
        global $wpdb, $wp_roles;

        $error_message = 'Error! ' . __('Database operation error. Check log file.', 'user-role-editor');
        $option_name = $wpdb->prefix . 'user_roles';
        $backup_option_name = $wpdb->prefix . 'backup_user_roles';
        $query = "select option_value
              from $wpdb->options
              where option_name='$backup_option_name'
              limit 0, 1";
        $option_value = $wpdb->get_var($query);
        if ($wpdb->last_error) {
            $this->log_event($wpdb->last_error, true);
            return $error_message;
        }
        if ($option_value) {
            $query = "update $wpdb->options
                    set option_value='$option_value'
                    where option_name='$option_name'
                    limit 1";
            $record = $wpdb->query($query);
            if ($wpdb->last_error) {
                $this->log_event($wpdb->last_error, true);
                return $error_message;
            }
            $wp_roles = new WP_Roles();
            $reload_link = wp_get_referer();
            $reload_link = remove_query_arg('action', $reload_link);
            $reload_link = esc_url_raw(add_query_arg('action', 'roles_restore_note', $reload_link));
?>    
            <script type="text/javascript" >
              document.location = '<?php echo $reload_link; ?>';
            </script>  
            <?php
            $mess = '';
        } else {
            $mess = __('No backup data. It is created automatically before the first role data update.', 'user-role-editor');
        }
        if (isset($_REQUEST['user_role'])) {
            unset($_REQUEST['user_role']);
        }

        return $mess;
    }
    // end of restore_user_roles()
*/

    protected function convert_caps_to_readable($caps_name) 
    {

        $caps_name = str_replace('_', ' ', $caps_name);
        $caps_name = ucfirst($caps_name);

        return $caps_name;
    }
    // ure_ConvertCapsToReadable
    
            
    public function make_roles_backup() 
    {
        global $wpdb;

        // check if backup user roles record exists already
        $backup_option_name = $wpdb->prefix . 'backup_user_roles';
        $query = "select option_id
              from $wpdb->options
              where option_name='$backup_option_name'
          limit 0, 1";
        $option_id = $wpdb->get_var($query);
        if ($wpdb->last_error) {
            $this->log_event($wpdb->last_error, true);
            return false;
        }
        if (!$option_id) {
            $roles_option_name = $wpdb->prefix.'user_roles';
            $query = "select option_value 
                        from $wpdb->options 
                        where option_name like '$roles_option_name' limit 0,1";
            $serialized_roles = $wpdb->get_var($query);
            // create user roles record backup            
            $query = "insert into $wpdb->options
                (option_name, option_value, autoload)
                values ('$backup_option_name', '$serialized_roles', 'no')";
            $record = $wpdb->query($query);
            if ($wpdb->last_error) {
                $this->log_event($wpdb->last_error, true);
                return false;
            }
        }

        return true;
    }
    // end of ure_make_roles_backup()

    
    protected function role_contains_caps_not_allowed_for_simple_admin($role_id) {
        
        $result = false;
        $role = $this->roles[$role_id];
        if (!is_array($role['capabilities'])) {
            return false;
        }
        foreach (array_keys($role['capabilities']) as $cap) {
            if ($this->block_cap_for_single_admin($cap)) {
                $result = true;
                break;
            }
        }
        
        return $result;
    } 
    // end of role_contains_caps_not_allowed_for_simple_admin()
    
    /**
     * return array with roles which we could delete, e.g self-created and not used with any blog user
     * 
     * @global wpdb $wpdb   - WP database object
     * @return array 
     */
    protected function get_roles_can_delete() {

        $default_role = get_option('default_role');
        $standard_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
        $roles_can_delete = array();
        $users = count_users();
        foreach ($this->roles as $key => $role) {
            $can_delete = true;
            // check if it is default role for new users
            if ($key == $default_role) {
                $can_delete = false;
                continue;
            }
            // check if it is standard role            
            if (in_array($key, $standard_roles)) {
                continue;
            }
            // check if role has capabilities prohibited for the single site administrator
            if ($this->role_contains_caps_not_allowed_for_simple_admin($key)) {
                continue;
            }
                        
            if (!isset($users['avail_roles'][$key])) {
                $roles_can_delete[$key] = $role['name'] . ' (' . $key . ')';
            }
        }

        return $roles_can_delete;
    }
    // end of get_roles_can_delete()
    
    
    /**
     * return array of built-in WP capabilities (WP 3.1 wp-admin/includes/schema.php) 
     * 
     * @return array 
     */
    public function get_built_in_wp_caps() {
        $wp_version = get_bloginfo('version');
        
        $caps = array();
        $caps['switch_themes'] = 1;
        $caps['edit_themes'] = 1;
        $caps['activate_plugins'] = 1;
        $caps['edit_plugins'] = 1;
        $caps['edit_users'] = 1;
        $caps['edit_files'] = 1;
        $caps['manage_options'] = 1;
        $caps['moderate_comments'] = 1;
        $caps['manage_categories'] = 1;
        $caps['manage_links'] = 1;
        $caps['upload_files'] = 1;
        $caps['import'] = 1;
        $caps['unfiltered_html'] = 1;
        $caps['edit_posts'] = 1;
        $caps['edit_others_posts'] = 1;
        $caps['edit_published_posts'] = 1;
        $caps['publish_posts'] = 1;
        $caps['edit_pages'] = 1;
        $caps['read'] = 1;
        $caps['level_10'] = 1;
        $caps['level_9'] = 1;
        $caps['level_8'] = 1;
        $caps['level_7'] = 1;
        $caps['level_6'] = 1;
        $caps['level_5'] = 1;
        $caps['level_4'] = 1;
        $caps['level_3'] = 1;
        $caps['level_2'] = 1;
        $caps['level_1'] = 1;
        $caps['level_0'] = 1;
        $caps['edit_others_pages'] = 1;
        $caps['edit_published_pages'] = 1;
        $caps['publish_pages'] = 1;
        $caps['delete_pages'] = 1;
        $caps['delete_others_pages'] = 1;
        $caps['delete_published_pages'] = 1;
        $caps['delete_posts'] = 1;
        $caps['delete_others_posts'] = 1;
        $caps['delete_published_posts'] = 1;
        $caps['delete_private_posts'] = 1;
        $caps['edit_private_posts'] = 1;
        $caps['read_private_posts'] = 1;
        $caps['delete_private_pages'] = 1;
        $caps['edit_private_pages'] = 1;
        $caps['read_private_pages'] = 1;
        $caps['unfiltered_upload'] = 1;
        $caps['edit_dashboard'] = 1;
        $caps['update_plugins'] = 1;
        $caps['delete_plugins'] = 1;
        $caps['install_plugins'] = 1;
        $caps['update_themes'] = 1;
        $caps['install_themes'] = 1;
        $caps['update_core'] = 1;
        $caps['list_users'] = 1;
        $caps['remove_users'] = 1;
                
        if (version_compare($wp_version, '4.4', '<')) {
            $caps['add_users'] = 1;  // removed from WP v. 4.4.
        }
        
        $caps['promote_users'] = 1;
        $caps['edit_theme_options'] = 1;
        $caps['delete_themes'] = 1;
        $caps['export'] = 1;
        $caps['delete_users'] = 1;
        $caps['create_users'] = 1;
        if ($this->multisite) {
            $caps['manage_network'] = 1;
            $caps['manage_sites'] = 1;
            $caps['create_sites'] = 1;
            $caps['manage_network_users'] = 1;
            $caps['manage_network_themes'] = 1;
            $caps['manage_network_plugins'] = 1;
            $caps['manage_network_options'] = 1;
        }
                
        $caps = apply_filters('ure_built_in_wp_caps', $caps);
        
        return $caps;
    }
    // end of get_built_in_wp_caps()

    
    /**
     * return the array of unused user capabilities
     * 
     * @global WP_Roles $wp_roles
     * @global wpdb $wpdb
     * @return array 
     */
    protected function get_caps_to_remove() 
    {
        global $wp_roles;

        // build full capabilities list from all roles except Administrator 
        $full_caps_list = array();
        foreach ($wp_roles->roles as $role) {
            // validate if capabilities is an array
            if (isset($role['capabilities']) && is_array($role['capabilities'])) {
                foreach ($role['capabilities'] as $capability => $value) {
                    if (!isset($full_caps_list[$capability])) {
                        $full_caps_list[$capability] = 1;
                    }
                }
            }
        }

        $caps_to_exclude = $this->get_built_in_wp_caps();
        $ure_caps = $this->get_ure_caps();
        $caps_to_exclude = array_merge($caps_to_exclude, $ure_caps);

        $caps_to_remove = array();
        foreach ($full_caps_list as $capability => $value) {
            if (!isset($caps_to_exclude[$capability])) {    // do not touch built-in WP caps
                // check roles
                $cap_in_use = false;
                foreach ($wp_roles->role_objects as $wp_role) {
                    if ($wp_role->name != 'administrator') {
                        if ($wp_role->has_cap($capability)) {
                            $cap_in_use = true;
                            break;
                        }
                    }
                }
                if (!$cap_in_use) {
                    $caps_to_remove[$capability] = 1;
                }
            }
        }

        return $caps_to_remove;
    }
    // end of get_caps_to_remove()

    
    /**
     * Build HTML for select drop-down list from capabilities we can remove
     * 
     * @return string
     */
    protected function caps_to_remove_prepare_html() {
        
        $caps_to_remove = $this->get_caps_to_remove();
        if (!empty($caps_to_remove) && is_array($caps_to_remove) && count($caps_to_remove) > 0) {
            $html = '<select id="remove_user_capability" name="remove_user_capability" width="200" style="width: 200px">';
            foreach (array_keys($caps_to_remove) as $key) {
                $html .= '<option value="' . $key . '">' . $key . '</option>';
            }
            $html .= '</select>';
        } else {
            $html = '';
        }

        $this->capability_remove_html = $html;
    }
    // end of caps_to_remove_prepare_html()
    

    /**
     * returns array of deprecated capabilities
     * 
     * @return array 
     */
    protected function get_deprecated_caps() 
    {

        $dep_caps = array(
            'level_0' => 0,
            'level_1' => 0,
            'level_2' => 0,
            'level_3' => 0,
            'level_4' => 0,
            'level_5' => 0,
            'level_6' => 0,
            'level_7' => 0,
            'level_8' => 0,
            'level_9' => 0,
            'level_10' => 0,
            'edit_files' => 0);
        if ($this->multisite) {
            $dep_caps['unfiltered_html'] = 0;
        }

        return $dep_caps;
    }
    // end of get_deprecated_caps()

    
    /**
     * Return true if $capability is included to the list of capabilities allowed for the single site administrator
     * @param string $capability - capability ID
     * @param boolean $ignore_super_admin - if 
     * @return boolean
     */
    protected function block_cap_for_single_admin($capability, $ignore_super_admin=false) {
        
        if (!$this->is_pro()) {    // this functionality is for the Pro version only.
            return false;
        }
        
        if (!$this->multisite) {    // work for multisite only
            return false;
        }
        if (!$ignore_super_admin && is_super_admin()) { // Do not block superadmin
            return false;
        }
        $caps_access_restrict_for_simple_admin = $this->get_option('caps_access_restrict_for_simple_admin', 0);
        if (!$caps_access_restrict_for_simple_admin) {
            return false;
        }
        $allowed_caps = $this->get_option('caps_allowed_for_single_admin', array());
        if (in_array($capability, $allowed_caps)) {
            $block_this_cap = false;
        } else {
            $block_this_cap = true;
        }
        
        return $block_this_cap;
    }
    // end of block_cap_for_single_admin()
    
    
    /**
     * output HTML-code for capabilities list
     * @param boolean $core - if true, then show WordPress core capabilities, else custom (plugins and themes created)
     * @param boolean $for_role - if true, it is role capabilities list, else - user specific capabilities list
     * @param boolean $edit_mode - if false, capabilities checkboxes are shown as disable - readonly mode
     */
    protected function show_capabilities($core = true, $for_role = true, $edit_mode=true) {
                
        if ($this->multisite && !is_super_admin()) {
            $help_links_enabled = $this->get_option('enable_help_links_for_simple_admin_ms', 1);
        } else {
            $help_links_enabled = true;
        }
        
        $onclick_for_admin = '';
        if (!( $this->multisite && is_super_admin() )) {  // do not limit SuperAdmin for multi-site
            if ($core && 'administrator' == $this->current_role) {
                $onclick_for_admin = 'onclick="ure_turn_it_back(this)"';
            }
        }

        if ($core) {
            $quant = count($this->get_built_in_wp_caps());
            $deprecated_caps = $this->get_deprecated_caps();
        } else {
            $quant = count($this->full_capabilities) - count($this->get_built_in_wp_caps());
            $deprecated_caps = array();
        }
        $quant_in_column = (int) $quant / 3;
        $printed_quant = 0;
        $printed_total = 0;
        foreach ($this->full_capabilities as $capability) {            
            if ($core) {
                if (!$capability['wp_core']) { // show WP built-in capabilities 1st
                    continue;
                }
            } else {
                if ($capability['wp_core']) { // show plugins and themes added capabilities
                    continue;
                }
            }
            if (!$this->show_deprecated_caps && isset($deprecated_caps[$capability['inner']])) {
                $hidden_class = 'class="hidden"';
            } else {
                $hidden_class = '';
            }
            if (isset($deprecated_caps[$capability['inner']])) {
                $label_style = 'style="color:#BBBBBB;"';
            } else {
                $label_style = '';
            }
            if ($this->multisite && $this->block_cap_for_single_admin($capability['inner'], true)) {
                if (is_super_admin()) {
                    if (!is_network_admin()) {
                        $label_style = 'style="color: red;"';
                    }
                } else {
                    $hidden_class = 'class="hidden"';
                }
            }
            $checked = '';
            $disabled = '';
            if ($for_role) {
                if (isset($this->roles[$this->current_role]['capabilities'][$capability['inner']]) &&
                        !empty($this->roles[$this->current_role]['capabilities'][$capability['inner']])) {
                    $checked = 'checked="checked"';
                }
            } else {
                if (empty($edit_mode)) {
                    $disabled = 'disabled="disabled"';
                } else {
                    $disabled = '';
                }
                if ($this->user_can($capability['inner'])) {
                    $checked = 'checked="checked"';
                    if (!isset($this->user_to_edit->caps[$capability['inner']])) {
                        $disabled = 'disabled="disabled"';
                    }
                }
            }
            $cap_id = str_replace(' ', URE_SPACE_REPLACER, $capability['inner']);
            echo '<div id="ure_div_cap_'. $cap_id.'" '. $hidden_class .'><input type="checkbox" name="' . $cap_id . '" id="' . 
                    $cap_id . '" value="' . $capability['inner'] .'" '. $checked . ' ' . $disabled . ' ' . $onclick_for_admin . '>';
            if (empty($hidden_class)) {
                if ($this->caps_readable) {
                    $cap_ind = 'human';
                    $cap_ind_alt = 'inner';
                } else {
                    $cap_ind = 'inner';
                    $cap_ind_alt = 'human';
                }
                $help_link = $help_links_enabled ? $this->capability_help_link($capability['inner']) : '';
                echo '<label for="' . $cap_id . '" title="' . $capability[$cap_ind_alt] . '" ' . $label_style . ' > ' . 
                     $capability[$cap_ind] . '</label> ' . $help_link . '</div>';
                $printed_quant++;
                $printed_total++;
                if ($printed_quant>=$quant_in_column) {
                    $printed_quant = 0;
                    echo '</td>';
                    if ($printed_total<$quant) {
                        echo '<td style="vertical-align:top;">';
                    }
                }
            }  else {   // if (empty($hidden_class
                echo '</div>';
            } // if (empty($hidden_class
        }
    }
    // end of show_capabilities()


    /**
     * output HTML code to create URE toolbar
     * 
     * @param string $this->current_role
     * @param boolean $role_delete
     * @param boolean $capability_remove
     */
    protected function toolbar($role_delete = false, $capability_remove = false) {
        $caps_access_restrict_for_simple_admin = $this->get_option('caps_access_restrict_for_simple_admin', 0);
        if ($caps_access_restrict_for_simple_admin) {
            $add_del_role_for_simple_admin = $this->get_option('add_del_role_for_simple_admin', 1);
        } else {
            $add_del_role_for_simple_admin = 1;
        }
        $super_admin = is_super_admin();
        
?>	
        <div id="ure_toolbar" >
           <button id="ure_select_all" class="ure_toolbar_button">Select All</button>
<?php
        if ('administrator' != $this->current_role) {
?>   
               <button id="ure_unselect_all" class="ure_toolbar_button">Unselect All</button> 
               <button id="ure_reverse_selection" class="ure_toolbar_button">Reverse</button> 
<?php
        }
        if ($this->ure_object == 'role') {
?>              
               <hr />
               <div id="ure_update">
                <button id="ure_update_role" class="ure_toolbar_button button-primary" >Update</button> 
<?php
            do_action('ure_role_edit_toolbar_update');
?>                                   
               </div>
<?php
            if (!$this->multisite || $super_admin || $add_del_role_for_simple_admin) { // restrict single site admin
?>
               <hr />               
<?php 
                if (current_user_can('ure_create_roles')) {
?>
               <button id="ure_add_role" class="ure_toolbar_button">Add Role</button>
<?php
                }
?>
               <button id="ure_rename_role" class="ure_toolbar_button">Rename Role</button>   
<?php
            }   // restrict single site admin
            if (!$this->multisite || $super_admin || !$caps_access_restrict_for_simple_admin) { // restrict single site admin
                if (current_user_can('ure_create_capabilities')) {
?>
               <button id="ure_add_capability" class="ure_toolbar_button">Add Capability</button>
<?php
                }
            }   // restrict single site admin
            
            if (!$this->multisite || $super_admin || $add_del_role_for_simple_admin) { // restrict single site admin
                if (!empty($role_delete) && current_user_can('ure_delete_roles')) {
?>  
                   <button id="ure_delete_role" class="ure_toolbar_button">Delete Role</button>
<?php
                }
            } // restrict single site admin
            
            if (!$this->multisite || $super_admin || !$caps_access_restrict_for_simple_admin) { // restrict single site admin            
                if ($capability_remove && current_user_can('ure_delete_capabilities')) {
?>
                   <button id="ure_delete_capability" class="ure_toolbar_button">Delete Capability</button>
<?php
                }
                if ($this->multisite && !is_network_admin()) {  // Show for single site for WP multisite only
?>
               <hr />
               <button id="ure_default_role" class="ure_toolbar_button">Default Role</button>
               <hr />
<?php
                }
?>
               <div id="ure_service_tools">
<?php
                do_action('ure_role_edit_toolbar_service');
                if (!$this->multisite || 
                    (is_main_site( get_current_blog_id()) || (is_network_admin() && is_super_admin()))
                   ) {
                    if (current_user_can('ure_reset_roles')) {
?>                   
                  <button id="ure_reset_roles_button" class="ure_toolbar_button" style="color: red;" title="Reset Roles to its original state">Reset</button> 
<?php
                    }
                }
?>
               </div>
            <?php
            }   // restrict single site admin
        } else {
            ?>
               
               <hr />
            	 <div id="ure_update_user">
                <button id="ure_update_role" class="ure_toolbar_button button-primary">Update</button> 
<?php
    do_action('ure_user_edit_toolbar_update');
?>                   
                
            	 </div>	 
            <?php
        }
            ?>
           
        </div>  
        <?php
    }
    // end of toolbar()
    
    
    /**
     * return link to the capability according its name in $capability parameter
     * 
     * @param string $capability
     * @return string 
     */
    protected function capability_help_link($capability) {

        if (empty($capability)) {
            return '';
        }

        switch ($capability) {
            case 'activate_plugins':
                $url = 'http://www.shinephp.com/activate_plugins-wordpress-capability/';
                break;
            case 'add_users':
                $url = 'http://www.shinephp.com/add_users-wordpress-user-capability/';
                break;
            case 'create_users':
                $url = 'http://www.shinephp.com/create_users-wordpress-user-capability/';
                break;
            case 'delete_others_pages':
            case 'delete_others_posts':
            case 'delete_pages':
            case 'delete_posts':
            case 'delete_protected_pages':
            case 'delete_protected_posts':
            case 'delete_published_pages':
            case 'delete_published_posts':
                $url = 'http://www.shinephp.com/delete-posts-and-pages-wordpress-user-capabilities-set/';
                break;
            case 'delete_plugins':
                $url = 'http://www.shinephp.com/delete_plugins-wordpress-user-capability/';
                break;
            case 'delete_themes':
                $url = 'http://www.shinephp.com/delete_themes-wordpress-user-capability/';
                break;
            case 'delete_users':
                $url = 'http://www.shinephp.com/delete_users-wordpress-user-capability/';
                break;
            case 'edit_dashboard':
                $url = 'http://www.shinephp.com/edit_dashboard-wordpress-capability/';
                break;
            case 'edit_files':
                $url = 'http://www.shinephp.com/edit_files-wordpress-user-capability/';
                break;
            case 'edit_plugins':
                $url = 'http://www.shinephp.com/edit_plugins-wordpress-user-capability';
                break;
            case 'moderate_comments':
                $url = 'http://www.shinephp.com/moderate_comments-wordpress-user-capability/';
                break;
            case 'read':
                $url = 'http://shinephp.com/wordpress-read-capability/';
                break;
            case 'update_core':
                $url = 'http://www.shinephp.com/update_core-capability-for-wordpress-user/';
                break;
            case 'ure_edit_roles':
                $url = 'https://www.role-editor.com/user-role-editor-4-18-new-permissions/';
                break;
            default:
                $url = '';
        }
        // end of switch
        if (!empty($url)) {
            $link = '<a href="' . $url . '" title="' . esc_html__('read about', 'user-role-editor') .' '. $capability .' '. 
                    esc_html__('user capability', 'user-role-editor') .'" target="new"><img src="' . 
                    URE_PLUGIN_URL . 'images/help.png" alt="' . esc_html__('Help', 'user-role-editor') . '" /></a>';
        } else {
            $link = '';
        }

        return $link;
    }
    // end of capability_help_link()
    

    /**
     *  Go through all users and if user has non-existing role lower him to Subscriber role
     * 
     */   
    protected function validate_user_roles() {

        global $wp_roles;

        $default_role = get_option('default_role');
        if (empty($default_role)) {
            $default_role = 'subscriber';
        }
        $users_query = new WP_User_Query(array('fields' => 'ID'));
        $users = $users_query->get_results();
        foreach ($users as $user_id) {
            $user = get_user_by('id', $user_id);
            if (is_array($user->roles) && count($user->roles) > 0) {
                foreach ($user->roles as $role) {
                    $user_role = $role;
                    break;
                }
            } else {
                $user_role = is_array($user->roles) ? '' : $user->roles;
            }
            if (!empty($user_role) && !isset($wp_roles->roles[$user_role])) { // role doesn't exists
                $user->set_role($default_role); // set the lowest level role for this user
                $user_role = '';
            }

            if (empty($user_role)) {
                // Cleanup users level capabilities from non-existed roles
                $cap_removed = true;
                while (count($user->caps) > 0 && $cap_removed) {
                    foreach ($user->caps as $capability => $value) {
                        if (!isset($this->full_capabilities[$capability])) {
                            $user->remove_cap($capability);
                            $cap_removed = true;
                            break;
                        }
                        $cap_removed = false;
                    }
                }  // while ()
            }
        }  // foreach()
    }
    // end of validate_user_roles()

        
    protected function add_capability_to_full_caps_list($cap_id) {
        if (!isset($this->full_capabilities[$cap_id])) {    // if capability was not added yet
            $cap = array();
            $cap['inner'] = $cap_id;
            $cap['human'] = esc_html__($this->convert_caps_to_readable($cap_id), 'user-role-editor');
            if (isset($this->built_in_wp_caps[$cap_id])) {
                $cap['wp_core'] = true;
            } else {
                $cap['wp_core'] = false;
            }

            $this->full_capabilities[$cap_id] = $cap;
        }
    }
    // end of add_capability_to_full_caps_list()

    
    /**
     * Add capabilities from user roles save at WordPress database
     * 
     */
    protected function add_roles_caps() {
        foreach ($this->roles as $role) {
            // validate if capabilities is an array
            if (isset($role['capabilities']) && is_array($role['capabilities'])) {
                foreach (array_keys($role['capabilities']) as $cap) {
                    $this->add_capability_to_full_caps_list($cap);
                }
            }
        }
    }
    // end of add_roles_caps()
    

    /**
     * Add Gravity Forms plugin capabilities, if available
     * 
     */
    protected function add_gravity_forms_caps() {
        
        if (class_exists('GFCommon')) {
            $gf_caps = GFCommon::all_caps();
            foreach ($gf_caps as $gf_cap) {
                $this->add_capability_to_full_caps_list($gf_cap);
            }
        }        
        
    }
    // end of add_gravity_forms_caps()
    
    
    /**
     * Add bbPress plugin user capabilities (if available)
     */
    protected function add_bbpress_caps() {
    
        if (empty($this->bbpress)) {
            return;
        }
        
        $caps = $this->bbpress->get_caps();
        foreach ($caps as $cap) {
            $this->add_capability_to_full_caps_list($cap);
        }
    }
    // end of add_bbpress_caps()
        
    
    /**
     * Provide compatibility with plugins and themes which define their custom user capabilities using 
     * 'members_get_capabilities' filter from Members plugin 
     * 
     */
    protected function add_members_caps() {
        
        $custom_caps = array();
        $custom_caps = apply_filters( 'members_get_capabilities', $custom_caps );
        foreach ($custom_caps as $cap) {
           $this->add_capability_to_full_caps_list($cap);
        }        
        
    }
    // end of add_members_caps()
    

    /**
     * Add capabilities assigned directly to user, and not included into any role
     * 
     */
    protected function add_user_caps() {
        
        if ($this->ure_object=='user') {
            foreach(array_keys($this->user_to_edit->caps) as $cap)  {
                if (!isset($this->roles[$cap])) {   // it is the user capability, not role
                    $this->add_capability_to_full_caps_list($cap);
                }
            }
        }
        
    }
    // end of add_user_caps()
    

    /**
     * Add built-in WordPress caps in case some were not included to the roles for some reason
     * 
     */
    protected function add_wordpress_caps() {
                
        foreach (array_keys($this->built_in_wp_caps) as $cap) {            
            $this->add_capability_to_full_caps_list($cap);
        }        
        
    }
    // end of add_wordpress_caps()
    
    
    protected function add_custom_post_type_caps() {
               
        global $wp_roles;
        
        $capabilities = array(
            'create_posts',
            'edit_posts',
            'edit_published_posts',
            'edit_others_posts',
            'edit_private_posts',
            'publish_posts',
            'read_private_posts',
            'delete_posts',
            'delete_private_posts',
            'delete_published_posts',
            'delete_others_posts'
        );
        
        $post_types = get_post_types(array('_builtin'=>false), 'objects');
        // do not forget attachment post type as it may use the own capabilities set
        $attachment_post_type = get_post_type_object('attachment');
        if ($attachment_post_type->cap->edit_posts!=='edit_posts') {
            $post_types['attachment'] = $attachment_post_type;
        }
        
        foreach($post_types as $post_type) {            
            if (!isset($post_type->cap)) {
                continue;
            }
            foreach($capabilities as $capability) {
                if (isset($post_type->cap->$capability)) {
                    $cap_to_check = $post_type->cap->$capability;
                    $this->add_capability_to_full_caps_list($cap_to_check);
                    if (!$this->multisite &&
                        isset($wp_roles->role_objects['administrator']) && 
                        !isset($wp_roles->role_objects['administrator']->capabilities[$cap_to_check])) {
                        // admin should be capable to edit any posts
                        $wp_roles->role_objects['administrator']->add_cap($cap_to_check, true);
                    }
                }
            }                        
        }
        
        if (!$this->multisite && isset($wp_roles->role_objects['administrator'])) {
            foreach(array('post', 'page') as $post_type_name) {
                $post_type = get_post_type_object($post_type_name);
                if ($post_type->cap->create_posts!=='edit_'. $post_type->name .'s') {   // 'create' capability is active
                    if (!isset($wp_roles->role_objects['administrator']->capabilities[$post_type->cap->create_posts])) {
                        // admin should be capable to create posts and pages
                        $wp_roles->role_objects['administrator']->add_cap($post_type->cap->create_posts, true);
                    }
                }
            }   // foreach()
        }   // if ()
        
    }
    // end of add_custom_post_type_caps()

    
    /**
     * Add capabilities for URE permissions system in case some were excluded from Administrator role
     * 
     */
    protected function add_ure_caps() {        
        
        $ure_caps = $this->get_ure_caps();
        foreach(array_keys($ure_caps) as $cap) {
            $this->add_capability_to_full_caps_list($cap);
        }
        
    }
    // end of add_ure_caps()
    
    
    protected function init_full_capabilities() {
        
        $this->built_in_wp_caps = $this->get_built_in_wp_caps();
        $this->full_capabilities = array();
        $this->add_roles_caps();
        $this->add_gravity_forms_caps();
        $this->add_bbpress_caps();
        $this->add_members_caps();
        $this->add_user_caps();
        $this->add_wordpress_caps();
        $this->add_custom_post_type_caps();
        $this->add_ure_caps();
        
        unset($this->built_in_wp_caps);
        asort($this->full_capabilities);
        
        $this->full_capabilities = apply_filters('ure_full_capabilites', $this->full_capabilities);
        
    }
    // end of init_full_capabilities()


    /**
     * return WordPress user roles to its initial state, just like after installation
     * @global WP_Roles $wp_roles
     */
    protected function wp_roles_reinit() {
        global $wp_roles;
        
        $wp_roles->roles = array();
        $wp_roles->role_objects = array();
        $wp_roles->role_names = array();
        $wp_roles->use_db = true;

        require_once(ABSPATH . '/wp-admin/includes/schema.php');
        populate_roles();
        $wp_roles->reinit();
        
        $this->roles = $this->get_user_roles();
        
    }
    // end of wp_roles_reinit()
    
    /**
     * reset user roles to WordPress default roles
     */
    protected function reset_user_roles() {
        
        if (!current_user_can('ure_reset_roles')) {
            return esc_html__('Insufficient permissions to work with User Role Editor','user-role-editor');
        }
              
        $this->wp_roles_reinit();
        $this->init_ure_caps();
        if ($this->is_full_network_synch() || $this->apply_to_all) {
            $this->current_role = '';
            $this->direct_network_roles_update();
        }
        //$this->validate_user_roles();  // if user has non-existing role lower him to Subscriber role
        
        $reload_link = wp_get_referer();
        $reload_link = esc_url_raw(remove_query_arg('action', $reload_link));
        ?>    
        	<script type="text/javascript" >
             jQuery.ure_postGo('<?php echo $reload_link; ?>', 
                      { action: 'roles_restore_note', 
                        ure_nonce: ure_data.wp_nonce} );
        	</script>  
        <?php
    }
    // end of reset_user_roles()

    
    /**
     * if returns true - make full syncronization of roles for all sites with roles from the main site
     * else - only currently selected role update is replicated
     * 
     * @return boolean
     */
    public function is_full_network_synch() {
        
        $result = defined('URE_MULTISITE_DIRECT_UPDATE') && URE_MULTISITE_DIRECT_UPDATE == 1;
        
        return $result;
    }
    // end of is_full_network_synch()
    
    
    protected function last_check_before_update() {
        if (empty($this->roles) || !is_array($this->roles) || count($this->roles)==0) { // Nothing to save - something goes wrong - stop ...
            return false;
        }
        
        return true;
    }
    // end of last_check_before_update()
    
    
    // Save Roles to database
    protected function save_roles() {
        global $wpdb;

        if (!$this->last_check_before_update()) {
            return false;
        }
        if (!isset($this->roles[$this->current_role])) {
            return false;
        }
        
        $this->capabilities_to_save = $this->remove_caps_not_allowed_for_single_admin($this->capabilities_to_save);
        $this->roles[$this->current_role]['capabilities'] = $this->capabilities_to_save;
        $option_name = $wpdb->prefix . 'user_roles';

        update_option($option_name, $this->roles);

        // save additional options for the current role
        if (empty($this->role_additional_options)) {
            $this->role_additional_options = URE_Role_Additional_Options::get_instance($this);
        }
        $this->role_additional_options->save($this->current_role);
        
        return true;
    }
    // end of save_roles()
    
    
    /**
     * Update roles for all network using direct database access - quicker in several times
     * 
     * @global wpdb $wpdb
     * @return boolean
     */
    public function direct_network_roles_update() {
        global $wpdb;

        if (!$this->last_check_before_update()) {
            return false;
        }
        if (!empty($this->current_role)) {
            if (!isset($this->roles[$this->current_role])) {
                $this->roles[$this->current_role]['name'] = $this->current_role_name;
            }
            $this->roles[$this->current_role]['capabilities'] = $this->capabilities_to_save;
        }        

        $serialized_roles = serialize($this->roles);
        foreach ($this->blog_ids as $blog_id) {
            $prefix = $wpdb->get_blog_prefix($blog_id);
            $options_table_name = $prefix . 'options';
            $option_name = $prefix . 'user_roles';
            $query = "update $options_table_name
                set option_value='$serialized_roles'
                where option_name='$option_name'
                limit 1";
            $wpdb->query($query);
            if ($wpdb->last_error) {
                $this->log_event($wpdb->last_error, true);
                return false;
            }
            // save role additional options
            
        }
        
        return true;
    }
    // end of direct_network_roles_update()

    
    public function restore_after_blog_switching($blog_id = 0) {
        
        if (!empty($blog_id)) {
            switch_to_blog($blog_id);
        }
        // cleanup blog switching data
        $GLOBALS['_wp_switched_stack'] = array();
        $GLOBALS['switched'] = ! empty( $GLOBALS['_wp_switched_stack'] );
    }
    // end of restore_after_blog_switching()
    
    
    protected function wp_api_network_roles_update() {
        global $wpdb;
        
        $result = true;
        $old_blog = $wpdb->blogid;
        foreach ($this->blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            $this->roles = $this->get_user_roles();
            if (!isset($this->roles[$this->current_role])) { // add new role to this blog
                $this->roles[$this->current_role] = array('name' => $this->current_role_name, 'capabilities' => array('read' => true));
            }
            if (!$this->save_roles()) {
                $result = false;
                break;
            }
        }
        $this->restore_after_blog_switching($old_blog);
        $this->roles = $this->get_user_roles();
        
        return $result;
    }
    // end of wp_api_network_roles_update()
    
        
    /**
     * Update role for all network using WordPress API
     * 
     * @return boolean
     */
    protected function multisite_update_roles() {
        
        if ($this->debug) {
            $time_shot = microtime();
        }
        
        if ($this->is_full_network_synch()) {
            $result = $this->direct_network_roles_update();
        } else {
            $result = $this->wp_api_network_roles_update();            
        }

        if ($this->debug) {
            echo '<div class="updated fade below-h2">Roles updated for ' . ( microtime() - $time_shot ) . ' milliseconds</div>';
        }

        return $result;
    }
    // end of multisite_update_roles()

    
    /**
     * Process user request on update roles
     * 
     * @global wpdb $wpdb
     * @return boolean
     */
    protected function update_roles() {

        if ($this->multisite && is_super_admin() && $this->apply_to_all) {  // update Role for the all blogs/sites in the network (permitted to superadmin only)
            if (!$this->multisite_update_roles()) {
                return false;
            }
        } else {
            if (!$this->save_roles()) {
                return false;
            }
        }

        return true;
    }
    // end of update_roles()

    
    /**
     * Write message to the log file
     * 
     * @global type $wp_version
     * @param string $message
     * @param boolean $show_message
     */
    protected function log_event($message, $show_message = false) {
        global $wp_version, $wpdb;

        $file_name = URE_PLUGIN_DIR . 'user-role-editor.log';
        $fh = fopen($file_name, 'a');
        $cr = "\n";
        $s = $cr . date("d-m-Y H:i:s") . $cr .
                'WordPress version: ' . $wp_version . ', PHP version: ' . phpversion() . ', MySQL version: ' . $wpdb->db_version() . $cr;
        fwrite($fh, $s);
        fwrite($fh, $message . $cr);
        fclose($fh);

        if ($show_message) {
            $this->show_message('Error! ' . esc_html__('Error is occur. Please check the log file.', 'user-role-editor'));
        }
    }
    // end of log_event()

    
    /**
     * returns array without capabilities blocked for single site administrators
     * @param array $capabilities
     * @return array
     */
    protected function remove_caps_not_allowed_for_single_admin($capabilities) {
        
        foreach(array_keys($capabilities) as $cap) {
            if ($this->block_cap_for_single_admin($cap)) {
                unset($capabilities[$cap]);
            }
        }
        
        return $capabilities;
    }
    // end of remove_caps_not_allowed_for_single_admin()
    
    
    /**
     * process new role create request
     * 
     * @global WP_Roles $wp_roles
     * 
     * @return string   - message about operation result
     * 
     */
    protected function add_new_role() {

        global $wp_roles;

        if (!current_user_can('ure_create_roles')) {
            return esc_html__('Insufficient permissions to work with User Role Editor','user-role-editor');
        }
        $mess = '';
        $this->current_role = '';
        if (isset($_POST['user_role_id']) && $_POST['user_role_id']) {
            $user_role_id = utf8_decode($_POST['user_role_id']);
            // sanitize user input for security
            $valid_name = preg_match('/[A-Za-z0-9_\-]*/', $user_role_id, $match);
            if (!$valid_name || ($valid_name && ($match[0] != $user_role_id))) { // some non-alphanumeric charactes found!
                return esc_html__('Error: Role ID must contain latin characters, digits, hyphens or underscore only!', 'user-role-editor');
            }
            $numeric_name = preg_match('/[0-9]*/', $user_role_id, $match);
            if ($numeric_name && ($match[0] == $user_role_id)) { // numeric name discovered
                return esc_html__('Error: WordPress does not support numeric Role name (ID). Add latin characters to it.', 'user-role-editor');
            }
            
            if ($user_role_id) {
                $user_role_name = isset($_POST['user_role_name']) ? $_POST['user_role_name'] : false;
                if (!empty($user_role_name)) {
                    $user_role_name = sanitize_text_field($user_role_name);
                } else {
                    $user_role_name = $user_role_id;  // as user role name is empty, use user role ID instead
                }

                if (!isset($wp_roles)) {
                    $wp_roles = new WP_Roles();
                }
                if (isset($wp_roles->roles[$user_role_id])) {
                    return sprintf('Error! ' . esc_html__('Role %s exists already', 'user-role-editor'), $user_role_id);
                }
                $user_role_id = strtolower($user_role_id);
                $this->current_role = $user_role_id;

                $user_role_copy_from = isset($_POST['user_role_copy_from']) ? $_POST['user_role_copy_from'] : false;
                if (!empty($user_role_copy_from) && $user_role_copy_from != 'none' && $wp_roles->is_role($user_role_copy_from)) {
                    $role = $wp_roles->get_role($user_role_copy_from);
                    $capabilities = $this->remove_caps_not_allowed_for_single_admin($role->capabilities);
                } else {
                    $capabilities = array('read' => true, 'level_0' => true);
                }
                // add new role to the roles array      
                $result = add_role($user_role_id, $user_role_name, $capabilities);
                if (!isset($result) || empty($result)) {
                    $mess = 'Error! ' . esc_html__('Error is encountered during new role create operation', 'user-role-editor');
                } else {
                    $mess = sprintf(esc_html__('Role %s is created successfully', 'user-role-editor'), $user_role_name);
                }
            }
        }
        return $mess;
    }
    // end of new_role_create()            
    
    
    /**
     * process rename role request
     * 
     * @global WP_Roles $wp_roles
     * 
     * @return string   - message about operation result
     * 
     */
    protected function rename_role() {

        global $wp_roles;

        $mess = '';
        $user_role_id = filter_input(INPUT_POST, 'user_role_id', FILTER_SANITIZE_STRING);
        if (empty($user_role_id)) {
            return esc_html__('Error: Role ID is empty!', 'user-role-editor');
        }
        $user_role_id = utf8_decode($user_role_id);
        // sanitize user input for security
        $match = array();
        $valid_name = preg_match('/[A-Za-z0-9_\-]*/', $user_role_id, $match);
        if (!$valid_name || ($valid_name && ($match[0] != $user_role_id))) { // some non-alphanumeric charactes found!
            return esc_html__('Error: Role ID must contain latin characters, digits, hyphens or underscore only!', 'user-role-editor');
        }
        $numeric_name = preg_match('/[0-9]*/', $user_role_id, $match);
        if ($numeric_name && ($match[0] == $user_role_id)) { // numeric name discovered
            return esc_html__('Error: WordPress does not support numeric Role name (ID). Add latin characters to it.', 'user-role-editor');
        }

        $new_role_name = filter_input(INPUT_POST, 'user_role_name', FILTER_SANITIZE_STRING);
        if (!empty($new_role_name)) {
            $new_role_name = sanitize_text_field($new_role_name);
        } else {
            return esc_html__('Error: Empty role display name is not allowed.', 'user-role-editor');
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        if (!isset($wp_roles->roles[$user_role_id])) {
            return sprintf('Error! ' . esc_html__('Role %s does not exists', 'user-role-editor'), $user_role_id);
        }                
        $this->current_role = $user_role_id;
        $this->current_role_name = $new_role_name;

        $old_role_name = $wp_roles->roles[$user_role_id]['name'];
        $wp_roles->roles[$user_role_id]['name'] = $new_role_name;
        update_option( $wp_roles->role_key, $wp_roles->roles );
        $mess = sprintf(esc_html__('Role %s is renamed to %s successfully', 'user-role-editor'), $old_role_name, $new_role_name);
        
        return $mess;
    }
    // end of rename_role()

    
    /**
     * Deletes user role from the WP database
     */
    protected function delete_wp_roles($roles_to_del) {
        global $wp_roles;

        if (!current_user_can('ure_delete_roles')) {
            return esc_html__('Insufficient permissions to work with User Role Editor','user-role-editor');
        }
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        $result = false;
        foreach($roles_to_del as $role_id) {
            if (!isset($wp_roles->roles[$role_id])) {
                $result = false;
                break;
            }                                            
            if ($this->role_contains_caps_not_allowed_for_simple_admin($role_id)) { // do not delete
                continue;
            }
            unset($wp_roles->role_objects[$role_id]);
            unset($wp_roles->role_names[$role_id]);
            unset($wp_roles->roles[$role_id]);                
            $result = true;
        }   // foreach()
        if ($result) {
            update_option($wp_roles->role_key, $wp_roles->roles);
        }
        
        return $result;
    }
    // end of delete_wp_roles()
    
    
    protected function delete_all_unused_roles() {        
        
        $this->roles = $this->get_user_roles();
        $roles_to_del = array_keys($this->get_roles_can_delete());  
        $result = $this->delete_wp_roles($roles_to_del);
        $this->roles = null;    // to force roles refresh
        
        return $result;        
    }
    // end of delete_all_unused_roles()
    
    
    /**
     * process user request for user role deletion
     * @global WP_Roles $wp_roles
     * @return type
     */
    protected function delete_role() {        

        if (!current_user_can('ure_delete_roles')) {
            return esc_html__('Insufficient permissions to work with User Role Editor','user-role-editor');
        }
        $mess = '';        
        if (isset($_POST['user_role_id']) && $_POST['user_role_id']) {
            $role = $_POST['user_role_id'];
            if ($role==-1) { // delete all unused roles
                $result = $this->delete_all_unused_roles();
            } else {
                $result = $this->delete_wp_roles(array($role));
            }
            if (empty($result)) {
                $mess = 'Error! ' . esc_html__('Error encountered during role delete operation', 'user-role-editor');
            } elseif ($role==-1) {
                $mess = sprintf(esc_html__('Unused roles are deleted successfully', 'user-role-editor'), $role);
            } else {
                $mess = sprintf(esc_html__('Role %s is deleted successfully', 'user-role-editor'), $role);
            }
            unset($_POST['user_role']);
        }

        return $mess;
    }
    // end of ure_delete_role()

    
    /**
     * Change default WordPress role
     * @global WP_Roles $wp_roles
     * @return string
     */
    protected function change_default_role() {
        global $wp_roles;

        if (!$this->multisite || is_network_admin()) {
            return 'Try to misuse the plugin functionality';
        }
        $mess = '';
        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }
        if (!empty($_POST['user_role_id'])) {
            $user_role_id = $_POST['user_role_id'];
            unset($_POST['user_role_id']);
            if (isset($wp_roles->role_objects[$user_role_id]) && $user_role_id !== 'administrator') {
                $result = update_option('default_role', $user_role_id);
                if (empty($result)) {
                    $mess = 'Error! ' . esc_html__('Error encountered during default role change operation', 'user-role-editor');
                } else {
                    $this->get_default_role();
                    $mess = sprintf(esc_html__('Default role for new users is set to %s successfully', 'user-role-editor'), $wp_roles->role_names[$user_role_id]);
                }
            } elseif ($user_role_id === 'administrator') {
                $mess = 'Error! ' . esc_html__('Can not set Administrator role as a default one', 'user-role-editor');
            } else {
                $mess = 'Error! ' . esc_html__('This role does not exist - ', 'user-role-editor') . esc_html($user_role_id);            
            }
        }

        return $mess;
    }
    // end of change_default_role()
    
    
    /**
     * Not really used in the plugin - just storage for the translation strings
     */
    protected function translation_data() {
// for the translation purpose
        if (false) {
// Standard WordPress roles
            __('Editor', 'user-role-editor');
            __('Author', 'user-role-editor');
            __('Contributor', 'user-role-editor');
            __('Subscriber', 'user-role-editor');
// Standard WordPress capabilities
            __('Switch themes', 'user-role-editor');
            __('Edit themes', 'user-role-editor');
            __('Activate plugins', 'user-role-editor');
            __('Edit plugins', 'user-role-editor');
            __('Edit users', 'user-role-editor');
            __('Edit files', 'user-role-editor');
            __('Manage options', 'user-role-editor');
            __('Moderate comments', 'user-role-editor');
            __('Manage categories', 'user-role-editor');
            __('Manage links', 'user-role-editor');
            __('Upload files', 'user-role-editor');
            __('Import', 'user-role-editor');
            __('Unfiltered html', 'user-role-editor');
            __('Edit posts', 'user-role-editor');
            __('Edit others posts', 'user-role-editor');
            __('Edit published posts', 'user-role-editor');
            __('Publish posts', 'user-role-editor');
            __('Edit pages', 'user-role-editor');
            __('Read', 'user-role-editor');
            __('Level 10', 'user-role-editor');
            __('Level 9', 'user-role-editor');
            __('Level 8', 'user-role-editor');
            __('Level 7', 'user-role-editor');
            __('Level 6', 'user-role-editor');
            __('Level 5', 'user-role-editor');
            __('Level 4', 'user-role-editor');
            __('Level 3', 'user-role-editor');
            __('Level 2', 'user-role-editor');
            __('Level 1', 'user-role-editor');
            __('Level 0', 'user-role-editor');
            __('Edit others pages', 'user-role-editor');
            __('Edit published pages', 'user-role-editor');
            __('Publish pages', 'user-role-editor');
            __('Delete pages', 'user-role-editor');
            __('Delete others pages', 'user-role-editor');
            __('Delete published pages', 'user-role-editor');
            __('Delete posts', 'user-role-editor');
            __('Delete others posts', 'user-role-editor');
            __('Delete published posts', 'user-role-editor');
            __('Delete private posts', 'user-role-editor');
            __('Edit private posts', 'user-role-editor');
            __('Read private posts', 'user-role-editor');
            __('Delete private pages', 'user-role-editor');
            __('Edit private pages', 'user-role-editor');
            __('Read private pages', 'user-role-editor');
            __('Delete users', 'user-role-editor');
            __('Create users', 'user-role-editor');
            __('Unfiltered upload', 'user-role-editor');
            __('Edit dashboard', 'user-role-editor');
            __('Update plugins', 'user-role-editor');
            __('Delete plugins', 'user-role-editor');
            __('Install plugins', 'user-role-editor');
            __('Update themes', 'user-role-editor');
            __('Install themes', 'user-role-editor');
            __('Update core', 'user-role-editor');
            __('List users', 'user-role-editor');
            __('Remove users', 'user-role-editor');
            __('Add users', 'user-role-editor');
            __('Promote users', 'user-role-editor');
            __('Edit theme options', 'user-role-editor');
            __('Delete themes', 'user-role-editor');
            __('Export', 'user-role-editor');
        }
    }
    // end of ure_TranslationData()

    
    /**
     * placeholder - realized at the Pro version
     */
    protected function check_blog_user($user) {
        
        return true;
    }
    // end of check_blog_user()
    
    /**
     * placeholder - realized at the Pro version
     */    
    protected function network_update_user($user) {
        
        return true;
    }
    // end of network_update_user()
    
    
    /**
     * Update user roles and capabilities
     * 
     * @global WP_Roles $wp_roles
     * @param WP_User $user
     * @return boolean
     */
    protected function update_user($user) {
        global $wp_roles;
                
        if ($this->multisite) {
            if (!$this->check_blog_user($user)) {
                return false;
            }
        }
        
        $primary_role = $_POST['primary_role'];  
        if (empty($primary_role) || !isset($wp_roles->roles[$primary_role])) {
            $primary_role = '';
        }
        if (function_exists('bbp_filter_blog_editable_roles')) {  // bbPress plugin is active
            $bbp_user_role = bbp_get_user_role($user->ID);
        } else {
            $bbp_user_role = '';
        }
        
        $edit_user_caps_mode = $this->get_edit_user_caps_mode();
        if (!$edit_user_caps_mode) {    // readonly mode
            $this->capabilities_to_save = $user->caps;
        }
        
        // revoke all roles and capabilities from this user
        $user->roles = array();
        $user->remove_all_caps();

        // restore primary role
        if (!empty($primary_role)) {
            $user->add_role($primary_role);
        }

        // restore bbPress user role if she had one
        if (!empty($bbp_user_role)) {
            $user->add_role($bbp_user_role);
        }

        // add other roles to user
        foreach ($_POST as $key => $value) {
            $result = preg_match('/^wp_role_(.+)/', $key, $match);
            if ($result === 1) {
                $role = $match[1];
                if (isset($wp_roles->roles[$role])) {
                    $user->add_role($role);
                    if (!$edit_user_caps_mode && isset($this->capabilities_to_save[$role])) {
                        unset($this->capabilities_to_save[$role]);
                    }
                }
            }
        }

        
        
        // add individual capabilities to user
        if (count($this->capabilities_to_save) > 0) {
            foreach ($this->capabilities_to_save as $key => $value) {
                $user->add_cap($key);
            }
        }
        $user->update_user_level_from_caps();
        
        if ($this->apply_to_all) { // apply update to the all network
            if (!$this->network_update_user($user)) {
                return false;
            }
        }
        
        return true;
    }
    // end of update_user()

    
    /**
     * Add new capability
     * 
     * @global WP_Roles $wp_roles
     * @return string
     */
    protected function add_new_capability() {
        global $wp_roles;

        if (!current_user_can('ure_create_capabilities')) {
            return esc_html__('Insufficient permissions to work with User Role Editor','user-role-editor');
        }
        $mess = '';
        if (isset($_POST['capability_id']) && $_POST['capability_id']) {
            $user_capability = $_POST['capability_id'];
            // sanitize user input for security
            $valid_name = preg_match('/[A-Za-z0-9_\-]*/', $user_capability, $match);
            if (!$valid_name || ($valid_name && ($match[0] != $user_capability))) { // some non-alphanumeric charactes found!    
                return 'Error! ' . esc_html__('Error: Capability name must contain latin characters and digits only!', 'user-role-editor');
                ;
            }

            if ($user_capability) {
                $user_capability = strtolower($user_capability);
                if (!isset($wp_roles)) {
                    $wp_roles = new WP_Roles();
                }
                $wp_roles->use_db = true;
                $administrator = $wp_roles->get_role('administrator');
                if (!$administrator->has_cap($user_capability)) {
                    $wp_roles->add_cap('administrator', $user_capability);
                    $mess = sprintf(esc_html__('Capability %s is added successfully', 'user-role-editor'), $user_capability);
                } else {
                    $mess = sprintf('Error! ' . esc_html__('Capability %s exists already', 'user-role-editor'), $user_capability);
                }
            }
        }

        return $mess;
    }
    // end of add_new_capability()

    
    /**
     * Delete capability
     * 
     * @global wpdb $wpdb
     * @global WP_Roles $wp_roles
     * @return string - information message
     */
    protected function delete_capability() {
        global $wpdb, $wp_roles;

        
        if (!current_user_can('ure_delete_capabilities')) {
            return esc_html__('Insufficient permissions to work with User Role Editor','user-role-editor');
        }
        $mess = '';
        if (!empty($_POST['user_capability_id'])) {
            $capability_id = $_POST['user_capability_id'];
            $caps_to_remove = $this->get_caps_to_remove();
            if (!is_array($caps_to_remove) || count($caps_to_remove) == 0 || !isset($caps_to_remove[$capability_id])) {
                return sprintf(esc_html__('Error! You do not have permission to delete this capability: %s!', 'user-role-editor'), $capability_id);
            }

            // process users
            $usersId = $wpdb->get_col("SELECT $wpdb->users.ID FROM $wpdb->users");
            foreach ($usersId as $user_id) {
                $user = get_user_to_edit($user_id);
                if ($user->has_cap($capability_id)) {
                    $user->remove_cap($capability_id);
                }
            }

            // process roles
            foreach ($wp_roles->role_objects as $wp_role) {
                if ($wp_role->has_cap($capability_id)) {
                    $wp_role->remove_cap($capability_id);
                }
            }

            $mess = sprintf(esc_html__('Capability %s was removed successfully', 'user-role-editor'), $capability_id);
        }

        return $mess;
    }
    // end of remove_capability()

    
    /**
     * Returns text presentation of user roles
     * 
     * @param type $roles user roles list
     * @return string
     */
    public function roles_text($roles) {
        global $wp_roles;

        if (is_array($roles) && count($roles) > 0) {
            $role_names = array();
            foreach ($roles as $role) {
                $role_names[] = $wp_roles->roles[$role]['name'];
            }
            $output = implode(', ', $role_names);
        } else {
            $output = '';
        }

        return $output;
    }
    // end of roles_text()
    

    /**
     * display opening part of the HTML box with title and CSS style
     * 
     * @param string $title
     * @param string $style 
     */
    protected function display_box_start($title, $style = '') {
        ?>
        			<div class="postbox" style="float: left; <?php echo $style; ?>">
        				<h3 style="cursor:default;"><span><?php echo $title ?></span></h3>
        				<div class="inside">
        <?php
    }
    // 	end of display_box_start()


    /**
     * close HTML box opened by display_box_start() call
     */
    function display_box_end() {
        ?>
        				</div>
        			</div>
        <?php
    }
    // end of display_box_end()
    
    
    public function about() {
        if ($this->is_pro()) {
            return;
        }

?>		  
            <h2>User Role Editor</h2>         
            
            <strong><?php esc_html_e('Version:', 'user-role-editor');?></strong> <?php echo URE_VERSION; ?><br/><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/vladimir.png'; ?>);" target="_blank" href="http://www.shinephp.com/"><?php _e("Author's website", 'user-role-editor'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/user-role-editor-icon.png'; ?>);" target="_blank" href="https://www.role-editor.com"><?php _e('Plugin webpage', 'user-role-editor'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/user-role-editor-icon.png'; ?>);" target="_blank" href="https://www.role-editor.com/download-plugin"><?php _e('Plugin download', 'user-role-editor'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/changelog-icon.png'; ?>);" target="_blank" href="https://www.role-editor.com/changelog"><?php _e('Changelog', 'user-role-editor'); ?></a><br/>
            <a class="ure_rsb_link" style="background-image:url(<?php echo URE_PLUGIN_URL . 'images/faq-icon.png'; ?>);" target="_blank" href="http://www.shinephp.com/user-role-editor-wordpress-plugin/#faq"><?php _e('FAQ', 'user-role-editor'); ?></a><br/>
            <hr />
                <div style="text-align: center;">
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="encrypted" 
                               value="-----BEGIN PKCS7-----MIIHZwYJKoZIhvcNAQcEoIIHWDCCB1QCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYBME5QAQYFDddWBHA4YXI1x3dYmM77clH5s0CgokYnLVk0P8keOxMtYyNQo6xJs6pY1nJfE3tqNg8CZ3btJjmOUa6DsE+K8Nm6OxGHMQF45z8WAs+f/AvQWdSpPXD0eSMu9osNgmC3yv46hOT3B1J3rKkpeZzMThCdUfECqu+lluzELMAkGBSsOAwIaBQAwgeQGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIeMSZk/UuZnuAgcAort75TUUbtDhmdTi1N0tR9W75Ypuw5nBw01HkZFsFHoGezoT95c3ZesHAlVprhztPrizl1UzE9COQs+3p62a0o+BlxUolkqUT3AecE9qs9dNshqreSvmC8SOpirOroK3WE7DStUvViBfgoNAPTTyTIAKKX24uNXjfvx1jFGMQGBcFysbb3OTkc/B6OiU2G951U9R8dvotaE1RQu6JwaRgwA3FEY9d/P8M+XdproiC324nzFel5WlZ8vtDnMyuPxOgggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0xMTEyMTAwODU3MjdaMCMGCSqGSIb3DQEJBDEWBBSFh6YmkoVtYdMaDd5G6EN0dGcPpzANBgkqhkiG9w0BAQEFAASBgAB91K/+gsmpbKxILdCVXCkiOg1zSG+tfq2EZSNzf8z/R1E3HH8qPdm68OToILsgWohKFwE+RCwcQ0iq77wd0alnWoknvhBBoFC/U0yJ3XmA3Hkgrcu6yhVijY/Odmf6WWcz79/uLGkvBSECbjTY0GLxvhRlsh2nAioCfxAr1cFo-----END PKCS7-----">
                        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">                        
                    </form>                        
                </div>
<?php         
    }
    // end of about()

    
    protected function set_current_role() {
        if (!isset($this->current_role) || !$this->current_role) {
            if (isset($_REQUEST['user_role']) && $_REQUEST['user_role'] && isset($this->roles[$_REQUEST['user_role']])) {
                $this->current_role = $_REQUEST['user_role'];
            } else {
                $this->current_role = $this->get_last_role_id();
            }
            $this->current_role_name = $this->roles[$this->current_role]['name'];
        }
    }
    // end of set_current_role()
    
    
    protected function show_admin_role_allowed() {
        $show_admin_role = $this->get_option('show_admin_role', 0);
        $show_admin_role = ((defined('URE_SHOW_ADMIN_ROLE') && URE_SHOW_ADMIN_ROLE==1) || $show_admin_role==1) && $this->user_is_admin();
        
        return $show_admin_role;
    }
    // end of show_admin_role()
    
    
    public function role_default_prepare_html($select_width=200) {
                        
        if (!isset($this->roles) || !$this->roles) {
            // get roles data from database
            $this->roles = $this->get_user_roles();
        }
        
        $caps_access_restrict_for_simple_admin = $this->get_option('caps_access_restrict_for_simple_admin', 0);
        $show_admin_role = $this->show_admin_role_allowed();
        if ($select_width>0) {
            $select_style = 'style="width: '. $select_width .'px"';
        } else {
            $select_style = '';
        }
        $this->role_default_html = '<select id="default_user_role" name="default_user_role" '. $select_style .'>';
        foreach ($this->roles as $key => $value) {
            $selected = $this->option_selected($key, $this->wp_default_role);
            $disabled = ($key==='administrator' && $caps_access_restrict_for_simple_admin && !is_super_admin()) ? 'disabled' : '';
            if ($show_admin_role || $key != 'administrator') {
                $translated_name = esc_html__($value['name'], 'user-role-editor');  // get translation from URE language file, if exists
                if ($translated_name === $value['name']) { // get WordPress internal translation
                    $translated_name = translate_user_role($translated_name);
                }
                $translated_name .= ' (' . $key . ')';                
                $this->role_default_html .= '<option value="' . $key . '" ' . $selected .' '. $disabled .'>' . $translated_name . '</option>';
            }
        }
        $this->role_default_html .= '</select>';
        
    }
    // end of role_default_prepare_html()
    
    
    private function role_delete_prepare_html() {
        $roles_can_delete = $this->get_roles_can_delete();
        if ($roles_can_delete && count($roles_can_delete) > 0) {
            $this->role_delete_html = '<select id="del_user_role" name="del_user_role" width="200" style="width: 200px">';
            foreach ($roles_can_delete as $key => $value) {
                $this->role_delete_html .= '<option value="' . $key . '">' . esc_html__($value, 'user-role-editor') . '</option>';
            }
            $this->role_delete_html .= '<option value="-1" style="color: red;">' . esc_html__('Delete All Unused Roles', 'user-role-editor') . '</option>';
            $this->role_delete_html .= '</select>';
        } else {
            $this->role_delete_html = '';
        }
    }
    // end of role_delete_prepare_html()
    
    
    private function role_select_copy_prepare_html($select_width=200) {
        $caps_access_restrict_for_simple_admin = $this->get_option('caps_access_restrict_for_simple_admin', 0);
        $show_admin_role = $this->show_admin_role_allowed();
        $this->role_to_copy_html = '<select id="user_role_copy_from" name="user_role_copy_from" style="width: '. $select_width .'px">
            <option value="none" selected="selected">' . esc_html__('None', 'user-role-editor') . '</option>';
        $this->role_select_html = '<select id="user_role" name="user_role" onchange="ure_role_change(this.value);">';
        foreach ($this->roles as $key => $value) {
            $selected1 = $this->option_selected($key, $this->current_role);
            $disabled = ($key==='administrator' && $caps_access_restrict_for_simple_admin && !is_super_admin()) ? 'disabled' : '';
            if ($show_admin_role || $key != 'administrator') {
                $translated_name = esc_html__($value['name'], 'user-role-editor');  // get translation from URE language file, if exists
                if ($translated_name === $value['name']) { // get WordPress internal translation
                    $translated_name = translate_user_role($translated_name);
                }
                $translated_name .= ' (' . $key . ')';                
                $this->role_select_html .= '<option value="' . $key . '" ' . $selected1 .' '. $disabled .'>' . $translated_name . '</option>';
                $this->role_to_copy_html .= '<option value="' . $key .'" '. $disabled .'>' . $translated_name . '</option>';
            }
        }
        $this->role_select_html .= '</select>';
        $this->role_to_copy_html .= '</select>';
    }
    // end of role_select_copy_prepare_html()
    
    
    public function role_edit_prepare_html($select_width=200) {
        
        $this->role_select_copy_prepare_html($select_width);
        if ($this->multisite && !is_network_admin()) {
            $this->role_default_prepare_html($select_width);
        }        
        $this->role_delete_prepare_html();                
        $this->caps_to_remove_prepare_html();
    }
    // end of role_edit_prepare_html()
    
    
    public function user_primary_role_dropdown_list($user_roles) {
?>        
        <select name="primary_role" id="primary_role">
<?php
        // Compare user role against currently editable roles
        $user_roles = array_intersect( array_values( $user_roles ), array_keys( get_editable_roles() ) );
        $user_primary_role  = array_shift( $user_roles );

        // print the full list of roles with the primary one selected.
        wp_dropdown_roles($user_primary_role);

        // print the 'no role' option. Make it selected if the user has no role yet.        
        $selected = ( empty($user_primary_role) ) ? 'selected="selected"' : '';
        echo '<option value="" '. $selected.'>' . esc_html__('&mdash; No role for this site &mdash;') . '</option>';
?>
        </select>
<?php        
    }
    // end of user_primary_role_dropdown_list()
    
    
    // returns true if $user has $capability assigned through the roles or directly
    // returns true if user has role with name equal $capability
    protected function user_can($capability) {
        
        if (isset($this->user_to_edit->caps[$capability])) {
            return true;
        }
        foreach ($this->user_to_edit->roles as $role) {
            if ($role===$capability) {
                return true;
            }
            if (!empty($this->roles[$role]['capabilities'][$capability])) {
                return true;
            }
        }
                
        return false;        
    }
    // end of user_can()           
    
    
    // returns true if current user has $capability assigned through the roles or directly
    // returns true if current user has role with name equal $cap
    public function user_has_capability($user, $cap) {

        global $wp_roles;

        if (!is_object($user) || empty($user->ID)) {
            return false;
        }
        if (is_multisite() && is_super_admin($user->ID)) {
            return true;
        }

        if (isset($user->caps[$cap])) {
            return true;
        }
        foreach ($user->roles as $role) {
            if ($role === $cap) {
                return true;
            }
            if (!empty($wp_roles->roles[$role]['capabilities'][$cap])) {
                return true;
            }
        }

        return false;
    }
    // end of user_has_capability()           
        
    
    public function show_other_default_roles() {
        $other_default_roles = $this->get_option('other_default_roles', array());
        foreach ($this->roles as $role_id => $role) {
            if ( $role_id=='administrator' || $role_id==$this->wp_default_role ) {			
                continue;
            }
            if ( in_array($role_id, $other_default_roles) ) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            echo '<label for="wp_role_' . $role_id .'"><input type="checkbox"	id="wp_role_' . $role_id . 
                '" name="wp_role_' . $role_id . '" value="' . $role_id . '"' . $checked .' />&nbsp;' . 
                esc_html__($role['name'], 'user-role-editor') . '</label><br />';
          }		
           
    }
    // end of show_other_default_roles()
                   
    
    public function get_current_role() {
        
        return $this->current_role;
        
    }
    // end of get_current_role()
    
    
    protected function get_edit_user_caps_mode() {
        if ($this->multisite && is_super_admin()) {
            return 1;
        }
        
        $edit_user_caps = $this->get_option('edit_user_caps', 1);
        
        return $edit_user_caps;
    }
    // end of get_edit_user_caps_mode()
    
    
    /**
     * Returns comma separated string of capabilities directly (not through the roles) assigned to the user
     * 
     * @global WP_Roles $wp_roles
     * @param object $user
     * @return string
     */
    public function get_edited_user_caps($user) {
        global $wp_roles;
        
        $output = '';
        foreach ($user->caps as $cap => $value) {
            if (!$wp_roles->is_role($cap)) {
                if ('' != $output) {
                    $output .= ', ';
                }
                $output .= $value ? $cap : sprintf(__('Denied: %s'), $cap);
            }
        }
        
        return $output;
    }
    // end of get_edited_user_caps()
            
    
    public function is_user_profile_extention_allowed() {
        // Check if we are not at the network admin center
        $result = stripos($_SERVER['REQUEST_URI'], 'network/user-edit.php') == false;
        
        return $result;
    }
    // end of is_user_profile_extention_allowed()

    
    // create assign_role object
    public function get_assign_role() {
        $assign_role = new URE_Assign_Role($this);
        
        return $assign_role;
    }
    // end of get_assign_role()
    
}
// end of URE_Lib class