<?php
/*
 * User Role Editor Pro WordPress plugin
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://www.role-editor.com
 * License: GPL v3
 * 
 */

/* 
 * User Role Editor's internal tasks queue
 * Usage: on URE plugin activation  URE adds 'on_activation' task to this queue, which fires 'ure_on_activation' action 
 * on the next WordPress call. It's useful when some action is needed unavailable at standard plugin activation point, 
 * like 'admin_menu', which is used for the admin menu access data conversion - class URE_Admin_Menu_Hashes.
 * Class User_Role_Editor_Pro adds execute_once method for the 'ure_on_activation' action, where 
 * URE_Admin_Menu_Hashes::require_data_conversion(); method is called which registers tasks for data coversion, including 
 * individual tasks for every site of the multisite network
 * 
 */
class URE_Task_Queue {
    
    private static $instance = null; // object exemplar reference  according to singleton patern
    const OPTION_NAME = 'ure_tasks_queue';        
    private $queue = null;
    
    
    public static function get_instance() {
                
        if (self::$instance===null) {        
            self::$instance = new URE_Task_Queue();
        }
        
        return self::$instance;
        
    }
    // end of get_instance()
    
    
    protected function __construct() {
        
        $this->init();
        
    }
    // end of __construct()
    
    
    private function init() {
        
        $this->queue = get_option(self::OPTION_NAME, array());
        
    }
    // end of init()
            
    
    public function reinit() {
        
        $this->init();
        
    }
    // end of reinit()


    /**
     * 
     * @param string $task_id
     * @param array $args=array('action'=>'action_name', 'routine'=>'routine_name', 'priority'=>99)
     */
    public function add($task_id, $args=array()) {
        
        $this->queue[$task_id] = $args;
        update_option(self::OPTION_NAME, $this->queue);
        
    }
    // end of add_task()
    
        
    public function remove($task_id) {
        
        if (isset($this->queue[$task_id])) {
            unset($this->queue[$task_id]);
            update_option(self::OPTION_NAME, $this->queue);
        }
    }
    // end of remove_task()
    
    
    /**
     * Returns true in case a queue is empty
     * 
     * @return boolean
     */
    public function is_empty() {
        
        return count($this->queue)==0;
    }
    // end of is_empty()
    
    
    /** 
     * Consumers should add there tasks with add_method and add 'ure_fulfil_task' action routine to work on it.
     * Do not forget remove task after it was fulfilled.
     * 
     * @return void
     */
    
    public function process() {
        
        if ($this->is_empty()) {
            return;
        }
        
        foreach($this->queue as $task_id=>$task) {
            if ($task_id=='on_activation') {
                do_action('ure_on_activation');        
                $this->remove('on_activation'); // remove this task after execution if it was defined
            } elseif (!empty($task['action'])) {
                    $priority =  empty($task['priority']) ? 10: $task['priority'];
                    add_action($task['action'], $task['routine'], $priority);
            } else {
                add_action('init', $task['routine']);
            }            
        }
    }
    // end of process();
    
    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone() {
        
    }
    // end of __clone()
    
    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup() {
        
    }
    // end of __wakeup()

}
// end of class URE_On_Activation