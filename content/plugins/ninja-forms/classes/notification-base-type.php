<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class for notification types.
 * This is the parent class. it should be extended by specific notification types
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Notifications
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.8
*/

abstract class NF_Notification_Base_Type
{

	/**
	 * Get things rolling
	 *
	 * @since 2.8
	 */
	function __construct() {

	}

	/**
	 * Processing function
	 *
	 * @access public
	 * @since 2.8
	 * @return false
	 */
	public function process( $id ) {
		// This space left intentionally blank
	}

	/**
	 * Output admin edit screen
	 *
	 * @access public
	 * @since 2.8
	 * @return false
	 */
	public function edit_screen( $id = '' ) {
		// This space left intentionally blank
	}

	/**
	 * Save admin edit screen
	 *
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function save_admin( $id = '', $data ) {
		// This space left intentionally blank
		return $data;
	}

	/**
	 * Explode our settings by ` and extract each value.
	 * Check to see if the setting is a field; if it is, assign the value.
	 * Run shortcodes and return the result.
	 *
	 * @access public
	 * @since 2.8
	 * @return array $setting
	 */
	public function process_setting( $id, $setting, $html = 1 ) {
		global $ninja_forms_processing;

		$setting_name = $setting;

		$setting = explode( '`', Ninja_Forms()->notification( $id )->get_setting( $setting ) );

		for ( $x = 0; $x <= count ( $setting ) - 1; $x++ ) {
			if ( strpos( $setting[ $x ], 'field_' ) !== false ) {
				if ( $ninja_forms_processing->get_field_value( str_replace( 'field_', '', $setting[ $x ] ) ) ) {
					$setting[ $x ] = $ninja_forms_processing->get_field_value( str_replace( 'field_', '', $setting[ $x ] ) );
				} else {
					$setting[ $x ] = '';
				}
			}

			if ( ! is_array ( $setting[ $x] ) ) {
				$setting[ $x ] = str_replace( '[ninja_forms_all_fields]', '[ninja_forms_all_fields html=' . $html . ']', $setting[ $x ] );
				$setting[ $x ] = do_shortcode( $setting[ $x ] );
				$setting[ $x ] = nf_parse_fields_shortcode( $setting[ $x ] );				
			}

		}

		return apply_filters( 'nf_notification_process_setting', $setting, $setting_name, $id );
	}
}
