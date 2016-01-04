<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Debug/Status page
 *
 * @author 		Patrick Rauland
 * @category 	Admin
 * @since     	2.2.50
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'NF_System_Status' ) ) :

/**
 * NF_System_Status Class
 */
class NF_System_Status {

	/**
	 * Initializes the class
	 */
	public function __construct(){
		// register the system status page
		add_action('admin_init', array($this, 'ninja_forms_register_tab_system_status'));
	}

	/**
	 * Handles output of the reports page in admin.
	 */
	public function ninja_forms_register_tab_system_status(){
		// include the file
		require_once( NINJA_FORMS_DIR . "/includes/admin/pages/system-status.php" );

		// add the arugements
		$args = array(
			'name' => __( 'Ninja Forms System Status', 'ninja-forms' ),
			'page' => 'ninja-forms-system-status',
			'display_function' => 'ninja_forms_tab_system_status',
			'save_function' => '',
			'show_save' => false,
		);

		// register the tab
		ninja_forms_register_tab('system_status', $args);
	}

}

endif;

return new NF_System_Status();