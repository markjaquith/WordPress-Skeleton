<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Registration class. Responsible for handling registration of fields, notifications, and sidebars
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Register
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
*/

class NF_Register
{

	/**
	 * Function that registers a notification type
	 * 
	 * @access public
	 * @param string $slug - Notification type slug. Must be unique.
	 * @param string $classname - Name of the class that should be used for the notification type.
	 * @since 3.0
	 * @return void
	 */
	public function notification_type( $slug, $nicename, $classname ) {
		if ( ! empty( $slug ) && ! empty( $classname ) && ! isset ( Ninja_Forms()->registered_field_types[ $slug ] ) ) {
			Ninja_Forms()->registered_notification_types[ $slug ]['nicename'] = $nicename;
			Ninja_Forms()->registered_notification_types[ $slug ]['classname'] = $classname;
		}
	}
	
}