<?php
/*
 * User Role Editor WordPress plugin options page
 *
 * @Author: Vladimir Garagulya
 * @URL: http://role-editor.com
 * @package UserRoleEditor
 *
 */


?>
<div class="wrap">
    <a href="http://role-editor.com">
        <div id="ure-icon" class="icon32"><br></div>        
    </a>    
    <h2><?php esc_html_e('User Role Editor - Options', 'user-role-editor'); ?></h2>            

    <div id="ure_tabs" style="clear: left;">
        <ul>
            <li><a href="#ure_tabs-1"><?php esc_html_e('General', 'user-role-editor');?></a></li>
<?php
if (!$license_key_only) {
    if ($this->lib->is_pro() || !$this->lib->multisite) {
?>
            <li><a href="#ure_tabs-2"><?php esc_html_e('Additional Modules', 'user-role-editor'); ?></a></li>
<?php
    }
?>
            <li><a href="#ure_tabs-3"><?php esc_html_e('Default Roles', 'user-role-editor'); ?></a></li>
<?php
    if ( $this->lib->multisite && ($this->lib->is_pro() || is_super_admin()) ) {
?>
            <li><a href="#ure_tabs-4"><?php esc_html_e('Multisite', 'user-role-editor'); ?></a></li>
<?php
    }
}
?>
            <li><a href="#ure_tabs-5"><?php esc_html_e('About', 'user-role-editor');?></a></li>
        </ul>
    <div id="ure_tabs-1">
    <div id="ure-settings-form">
        <form method="post" action="<?php echo $link; ?>?page=settings-<?php echo URE_PLUGIN_FILE; ?>" >   
            <table id="ure_settings">
<?php
if (!$license_key_only) {
?>
                <tr>
                    <td>
                        <input type="checkbox" name="show_admin_role" id="show_admin_role" value="1" 
                        <?php echo ($show_admin_role == 1) ? 'checked="checked"' : ''; ?>
                               <?php echo defined('URE_SHOW_ADMIN_ROLE') ? 'disabled="disabled" title="Predefined by \'URE_SHOW_ADMIN_ROLE\' constant at wp-config.php"' : ''; ?> />
                        <label for="show_admin_role"><?php esc_html_e('Show Administrator role at User Role Editor', 'user-role-editor'); ?></label></td>
                    <td> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="caps_readable" id="caps_readable" value="1" 
                               <?php echo ($caps_readable == 1) ? 'checked="checked"' : ''; ?> />
                        <label for="caps_readable"><?php esc_html_e('Show capabilities in the human readable form', 'user-role-editor'); ?></label></td>
                    <td>                         
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="show_deprecated_caps" id="show_deprecated_caps" value="1" 
                               <?php echo ($show_deprecated_caps == 1) ? 'checked="checked"' : ''; ?> /> 
                        <label for="show_deprecated_caps"><?php esc_html_e('Show deprecated capabilities', 'user-role-editor'); ?></label></td>
                    <td>                        
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="confirm_role_update" id="confirm_role_update" value="1" 
                               <?php echo ($confirm_role_update == 1) ? 'checked="checked"' : ''; ?> /> 
                        <label for="confirm_role_update"><?php esc_html_e('Confirm role update', 'user-role-editor'); ?></label></td>
                    <td>                        
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" name="edit_user_caps" id="edit_user_caps" value="1" 
                               <?php echo ($edit_user_caps == 1) ? 'checked="checked"' : ''; ?> /> 
                        <label for="edit_user_caps"><?php esc_html_e('Edit user capabilities', 'user-role-editor'); ?></label></td>
                    <td>                        
                    </td>
                </tr>
                
<?php
}
    do_action('ure_settings_show1');
?>
            </table>
    <?php wp_nonce_field('user-role-editor'); ?>   
            <input type="hidden" name="ure_tab_idx" value="0" />
            <p class="submit">
                <input type="submit" class="button-primary" name="ure_settings_update" value="<?php _e('Save', 'user-role-editor') ?>" />
            </p>  

        </form>  
    </div>   
    </div> <!-- ure_tabs-1 -->
<?php
if (!$license_key_only) {
    if ($this->lib->is_pro() || !$this->lib->multisite) {
?>
    
    <div id="ure_tabs-2">
        <form name="ure_additional_modules" method="post" action="<?php echo $link; ?>?page=settings-<?php echo URE_PLUGIN_FILE; ?>" >
            <table id="ure_addons">
<?php
if (!$this->lib->multisite) {
?>
                <tr>
                    <td>
                        <input type="checkbox" name="count_users_without_role" id="count_users_without_role" value="1" 
                               <?php echo ($count_users_without_role == 1) ? 'checked="checked"' : ''; ?> /> 
                        <label for="count_users_without_role"><?php esc_html_e('Count users without role', 'user-role-editor'); ?></label></td>
                    <td>                        
                    </td>
                </tr>
<?php      
}
?>
                
<?php                
    do_action('ure_settings_show2');
?>
            </table>    
<?php wp_nonce_field('user-role-editor'); ?>   
            <input type="hidden" name="ure_tab_idx" value="1" />
            <p class="submit">
                <input type="submit" class="button-primary" name="ure_addons_settings_update" value="<?php _e('Save', 'user-role-editor') ?>" />
                
        </form>    
    </div>    
<?php
    }
?>
    
    <div id="ure_tabs-3">
        <form name="ure_default_roles" method="post" action="<?php echo $link; ?>?page=settings-<?php echo URE_PLUGIN_FILE; ?>" >
<?php 
    if (!$this->lib->multisite) {
        esc_html_e('Primary default role: ', 'user-role-editor');
        echo $this->lib->role_default_html;
?>
        <hr>
<?php
    } 
?>
        <?php esc_html_e('Other default roles for new registered user: ', 'user-role-editor'); ?>
        <div id="other_default_roles">
            <?php $this->lib->show_other_default_roles(); ?>
        </div>
<?php 
    if ($this->lib->multisite) {
        echo '<p>'. esc_html__('Note for multisite environment: take into account that other default roles should exist at the site, in order to be assigned to the new registered users.', 'user-role-editor') .'</p>';
    }
?>
        <hr>
        <?php wp_nonce_field('user-role-editor'); ?>   
            <input type="hidden" name="ure_tab_idx" value="2" />
            <p class="submit">
                <input type="submit" class="button-primary" name="ure_default_roles_update" value="<?php _e('Save', 'user-role-editor') ?>" />
            </p>
        </form>      
    </div> <!-- ure_tabs-3 -->   
    
<?php
    if ( $this->lib->multisite && ($this->lib->is_pro() || is_super_admin())) {
?>
    <div id="ure_tabs-4">
        <div id="ure-settings-form-ms">
            <form name="ure_settings_ms" method="post" action="<?php echo $link; ?>?page=settings-<?php echo URE_PLUGIN_FILE; ?>" >
                <table id="ure_settings_ms">
<?php
    if (is_super_admin()) {
?>
                    <tr>
                         <td>
                             <input type="checkbox" name="allow_edit_users_to_not_super_admin" id="allow_edit_users_to_not_super_admin" value="1" 
                                  <?php echo ($allow_edit_users_to_not_super_admin == 1) ? 'checked="checked"' : ''; ?> /> 
                             <label for="allow_edit_users_to_not_super_admin"><?php esc_html_e('Allow non super administrators to create, edit, and delete users', 'user-role-editor'); ?></label>
                         </td>
                         <td>
                         </td>
                    </tr>                          
<?php
    }
                    do_action('ure_settings_ms_show');                    
?>                    
                </table>
<?php wp_nonce_field('user-role-editor'); ?>   
                <input type="hidden" name="ure_tab_idx" value="3" />
            <p class="submit">
                <input type="submit" class="button-primary" name="ure_settings_ms_update" value="<?php _e('Save', 'user-role-editor') ?>" />
            </p>                  
            </form>
        </div>   <!-- ure-settings-form-ms --> 
    </div>  <!-- ure_tabs-4 -->
<?php
    }
}   // if (!$license_key_only) {
?>
        <div id="ure_tabs-5">
            <?php $this->lib->about(); ?>
        </div> <!-- ure_tabs-5 -->
    </div> <!-- ure_tabs -->
</div>
<script>
    jQuery(function() {
        jQuery('#ure_tabs').tabs();
<?php
    if ($ure_tab_idx>0) {
?>
        jQuery("#ure_tabs").tabs("option", "active", <?php echo $ure_tab_idx; ?>);    
<?php
    }
?>
    });    
</script>
