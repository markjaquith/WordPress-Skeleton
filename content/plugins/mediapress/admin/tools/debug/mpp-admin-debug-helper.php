<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * Create the Debug Menu( currently MediaPress|Tools) and display the debug info
 * 
 */
class MPP_Admin_Debug_Helper {
    
    private static $instance;
    
	private function __construct() {
		
       // add_action( 'admin_init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        
    }
    /**
     * 
     * @return MPP_Admin_Debug_Helper
     */
    public static function get_instance() {
        
        if( ! isset( self::$instance ) ) {
            self::$instance = new self();
		}
		
        return self::$instance;
    }
	
	/**
	 * Add Tools Menu
	 */
	public function add_menu() {
		
		add_submenu_page( mpp_admin()->get_menu_slug(), __( 'Tools', 'mediapress' ), __( 'Tools', 'mediapress' ), 'manage_options', 'mpp-tools', array( $this, 'render' ) );
		
	}
	/**
	 * Show/render the setting page
	 * 
	 */
	public function render() {
		
		$path = mediapress()->get_path();
		//load viewer
		require_once $path . 'admin/tools/debug/mpp-debug-view.php';
	}
	
	public function display() {
		
		$path = mediapress()->get_path();
		
		if( ! class_exists( 'Browser' ) ) {
			require_once $path . 'admin/tools/lib/browser.php';
		}
		
		require_once $path . 'admin/tools/debug/mpp-debug-output.php';
		
	}
}

MPP_Admin_Debug_Helper::get_instance();