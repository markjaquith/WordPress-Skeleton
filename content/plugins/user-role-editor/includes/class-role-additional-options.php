<?php

class URE_Role_Additional_Options {
    
    private static $instance = null;
    private $lib = null;
    private $items = null;
    private $active_items = null;
    const STORAGE_ID = 'ure_role_additional_options_values';
    
    public function __construct($lib) {
    
        $this->lib = $lib;
        $this->init();
    }
    // end of __construct()
    
    
    public static function get_instance($lib) {
    
        if (self::$instance===null) {
            self::$instance = new URE_Role_Additional_Options($lib);
        }
        
        return self::$instance;
    }
    // end of get_instance()
    
        
    public static function create_item($id, $label, $hook, $routine) {
        $item = new stdClass();
        $item->id = $id;
        $item->label = $label;
        $item->hook = $hook;
        $item->routine = $routine;
        
        return $item;
    }
    // end of create_item()
            

    public static function get_active_items() {
        
        $items = get_option(self::STORAGE_ID, array());
        
        return $items;
    }    
    
    
    private function init() {
        
        $this->items = array();
        $item = self::create_item('hide_admin_bar', esc_html__('Hide admin bar', 'user-role-editor'), 'init', 'ure_hide_admin_bar');
        $this->items[$item->id] = $item;
        
        // Allow other developers to modify the list of role's additonal options 
        $this->items = apply_filters('ure_role_additional_options', $this->items);
    
        $this->active_items = self::get_active_items();
    }
    // end of init()

    
    public function set_active_items_hooks() {
        global $current_user;
        
        if (current_user_can('ure_edit_roles')) {
            return;
        }
                
        foreach($current_user->roles as $role) {
            if (!isset($this->active_items[$role])) {
                continue;
            }
            foreach(array_keys($this->active_items[$role]) as $item_id) {
                add_action($this->items[$item_id]->hook, $this->items[$item_id]->routine);
            }            
        }
        
    }
    // end of set_active_items_hooks()
    
    
    public function save($current_role) {
        $this->active_items = self::get_active_items();
        $this->active_items[$current_role] = array();
        foreach($this->items as $item) {
            if (isset($_POST[$item->id])) {
                $this->active_items[$current_role][$item->id] = 1;
            }
        }
        update_option(self::STORAGE_ID, $this->active_items);
        
    }
    // end of save()
    
        
    public function show($current_role) {
        
?>        
    
    <hr />
    <?php echo esc_html__('Additional Options', 'user-role-editor');?>:
    <table class="form-table" style="clear:none;" cellpadding="0" cellspacing="0">
        <tr>
            <td>

<?php
    $first_time = true;
    foreach($this->items as $item) {
        $checked = (isset($this->active_items[$current_role]) && 
                    isset($this->active_items[$current_role][$item->id])) ? 'checked="checked"' : '';
        if (!$first_time) {
?>
                <br/>
<?php            
        }
?>
                <input type="checkbox" name="<?php echo $item->id;?>" id="<?php echo $item->id;?>" value="<?php echo $item->id;?>" <?php echo $checked;?> >
                <label for="<?php echo $item->id;?>"><?php echo $item->label;?></label>
<?php
        $first_time = false;
    } 
?>
            </td>
            <td></td>
        </tr>                
    </table>    
<?php        
    }   
    // end of show()
    
}
// end of URE_Role_Additional_Options class