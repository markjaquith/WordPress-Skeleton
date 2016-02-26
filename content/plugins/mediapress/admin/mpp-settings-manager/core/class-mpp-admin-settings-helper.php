<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * Settings Page store
 * Use it to add/retrieve settings pages
 * Not required to make the script function but just a convenienence
 * 
 */
class MPP_Admin_Settings_Upload_Helper {
	
    /**
     *
     * @var string url of the current directory 
     */
    private $url;
    
    /**
     *
     * @var type 
     */
    private static $instance;
    /**
     *
     * @var MPP_Admin_Settings_Upload_Helper
     */
    
    private $pages = array();
    
    private function __construct() {
        
        
        if( ! isset( $this->url ) ) {
            //we need to find the directory of the options-buddy
            //it could be inside a theme or a plugin we  don't know
            $ob_path = dirname( __FILE__ );

            //for windows
            $ob_path= str_replace( '\\', '/', $ob_path );

            $abspath = str_replace( '\\', '/', ABSPATH );
        
            //find relative path
            $rel_path = str_replace( $abspath, '', $ob_path );

            $this->url = trailingslashit( site_url('/') . $rel_path );
        }
		
        add_action( 'admin_enqueue_scripts', array( $this, 'load_js' ) );
    }
    /**
     * 
     * @return MPP_Admin_Settings_Upload_Helper
     */
    public static function get_instance() {
    
        if( ! isset( self::$instance ) )
            self::$instance = new self();
        
        return self::$instance;
        
    }
    /**
     * 
     * @param type $page_name the slug for page
     * @param MPP_Admin_Settings_Page $page
     * @return MPP_Admin_Settings_Page
     */
    public function add_page( $page_name, $page = false ) {
        
        if( ! $page )
            $page = new MPP_Admin_Settings_Page( $page_name );
        
		$this->pages[$page_name] = $page;
        
        return $page;
        
    }
    /**
     * 
     * @param string $page_name
     * @return MPP_Admin_Settings_Page
     */
    public function get_page( $page_name ) {
        //if the page exists in the store, let us return it
        if( isset( $this->pages[$page_name] ) )
            return $this->pages[$page_name];
        
       //otherwise return a new page
       
        return $this->add_page($page_name);
    }
    
       
    
         /**
     * Enqueue scripts and styles
     */
    public function load_js() {
		
       wp_enqueue_media();
       wp_enqueue_script( 'mpp-admin-options-media-uploader', $this->url . '_inc/uploader.js', array( 'jquery' ) );
              
    }
}

//MPP_Admin_Settings_Upload_Helper::get_instance();
