<?php

/* 
 * User Role Editor On Screen Help class
 * 
 */

class URE_Screen_Help {
    
    protected function get_general_tab() {
    
        $text = '<h2>'. esc_html__('User Role Editor Options page help', 'user-role-editor') .'</h2>
            <p>
            <ul>
            <li><strong>' . esc_html__('Show Administrator role at User Role Editor', 'user-role-editor').'</strong> - ' .
                esc_html__('turn this option on in order to make the "Administrator" role available at the User Role Editor '
                        . 'roles selection drop-down list. It is hidden by default for security reasons.','user-role-editor') . '</li>
            <li><strong>' . esc_html__('Show capabilities in the human readable form','user-role-editor').'</strong> - ' .
                esc_html__('automatically converts capability names from the technical form for internal use like '
                        . '"edit_others_posts" to more user friendly form, e.g. "Edit others posts".','user-role-editor') . '</li>
            <li><strong>' . esc_html__('Show deprecated capabilities','user-role-editor').'</strong> - '.
                esc_html__('Capabilities like "level_0", "level_1" are deprecated and are not used by WordPress. '
                        . 'They are left at the user roles for the compatibility purpose with the old themes and plugins code. '
                        . 'Turning on this option will show those deprecated capabilities.', 'user-role-editor') . '</li>
            <li><strong>' . esc_html__('Edit user capabilities','user-role-editor').'</strong> - '.
                esc_html__('If turned off - capabilities section of selected user is shown in readonly mode. '
                        . 'Administrator can not assign capabilities to the user directly. '
                        . 'He should make it using roles only.', 'user-role-editor') . '</li>';

        $text = apply_filters('ure_get_settings_general_tab_help', $text);
        $text .='
            </ul>
                </p>';
        
        return $text;
    }
    // end of get_general_tab()


    protected function get_additional_modules_tab() {
        $text = '<h2>'. esc_html__('User Role Editor Options page help', 'user-role-editor') .'</h2>
            <p>
            <ul>';
        if (!is_multisite()) {
            $text .= '<li><strong>' . esc_html__('Count users without role', 'user-role-editor').'</strong> - ' .
                     esc_html__('Show at the "Users" page a quant of users without role. Module allows to assign all of them '.
                     'an empty role "No rights", in order to look on the users list with role "No rights" at the separate tab then.','user-role-editor') . '</li>';        
        }
        $text = apply_filters('ure_get_settings_additional_modules_tab_help', $text);
        $text .='
            </ul>
                </p>';        
        
        return $text;
    }
    // end of get_additional_modules_tab()

    
    protected function get_default_roles_tab() {
        $text = '<h2>'. esc_html__('User Role Editor Options page help', 'user-role-editor') .'</h2>
            <p>
            <ul>
            <li><strong>' . esc_html__('Other default roles for new registered user', 'user-role-editor').'</strong> - ' .
                esc_html__('select roles below to assign them to the new user automatically as an addition to the primary role. '.
                'Note for multisite environment: take into account that other default roles should exist at the site, '. 
                'in order to be assigned to the new registered users.','user-role-editor') . '</li>';        
        
        $text = apply_filters('ure_get_settings_default_roles_tab_help', $text);
        $text .='
            </ul>
                </p>';
        
        return $text;
    }
    // end of get_default_roles_tab()
    
    
    protected function get_multisite_tab() {
        $text = '<h2>'. esc_html__('User Role Editor Options page help', 'user-role-editor') .'</h2>
            <p>
            <ul>
                <li><strong>' . esc_html__('Allow non super-admininstrators to create, edit and delete users', 'user-role-editor').'</strong> - ' .
                esc_html__('Super administrator only may create, edit and delete users under WordPress multi-site by default. ' 
                        . 'Turn this option on in order to remove this limitation.','user-role-editor') . '</li>';
        
        $text = apply_filters('ure_get_settings_multisite_tab_help', $text);
        $text .='
            </ul>
                </p>';
        
        return $text;
    }
    // end of get_multisite_tab()
    
            
    public function get_settings_help($tab_name) {
        switch ($tab_name) {
            case 'general':{
                $text = $this->get_general_tab();
                break;
            }
            case 'additional_modules':{
                $text = $this->get_additional_modules_tab();
                break;
            }
            case 'default_roles':{
                $text = $this->get_default_roles_tab();
                break;
            }
            case 'multisite':{
                $text = $this->get_multisite_tab();
                break;
            }
            default: 
        }
        
        return $text;
    }
    // end of get_settings_help()
    
}
// end of URE_Screen_Help
