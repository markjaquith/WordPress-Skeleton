<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

//only load if not already loaded
if( ! class_exists( 'MPP_Admin_Settings_Page' ) ):
	
	$ob_path = dirname( __FILE__ ). DIRECTORY_SEPARATOR ;
	
	require_once $ob_path . 'core/class-mpp-admin-settings-field.php';
	require_once $ob_path . 'core/class-mpp-admin-settings-section.php';
	require_once $ob_path . 'core/class-mpp-admin-settings-panel.php';
	require_once $ob_path . 'core/class-mpp-admin-settings-page.php';
	require_once $ob_path . 'core/class-mpp-admin-settings-helper.php';
	
endif;


/**
 * Register a loader to load Field Class dynamically if they exist in fields/ directory
 * 
 * @param string $class name of the class
 */
function mpp_admin_settings_field_class_loader( $class ) {
  
    //let us just get the part after MPP_Admin_Settings_Field_ string e.g for MPP_Admin_Settings_Field_Text class it loads fields/text.php
    $file_name = strtolower( str_replace( 'MPP_Admin_Settings_Field_', '', $class ) );
    
    //let us reach to the file
    $file = dirname( __FILE__ ). DIRECTORY_SEPARATOR . 'fields'. DIRECTORY_SEPARATOR . $file_name. '.php';
     
    if( is_readable( $file ) ) {
     
		require_once $file; 
	}	
       
}
spl_autoload_register( 'mpp_admin_settings_field_class_loader' );
