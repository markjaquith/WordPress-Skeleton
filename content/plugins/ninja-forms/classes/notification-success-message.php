<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class for our success message notification type.
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Notifications
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.8
*/

class NF_Notification_Success_Message extends NF_Notification_Base_Type
{

	/**
	 * Get things rolling
	 */
	function __construct() {
		$this->name = __( 'Success Message', 'ninja-forms' );
	}

	/**
	 * Output our edit screen
	 * 
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function edit_screen( $id = '' ) {
		$settings = array(
			'textarea_name' => 'settings[success_msg]',
		);
		$loc_opts = apply_filters( 'nf_success_message_locations',
			array(
				array( 'action' => 'ninja_forms_display_before_fields', 'name' => __( 'Before Form', 'ninja-forms' ) ),
				array( 'action' => 'ninja_forms_display_after_fields', 'name' => __( 'After Form', 'ninja-forms' ) ),
			)
		);
		?>
		<!-- <tr>
			<th scope="row"><label for="success_message_loc"><?php _e( 'Location', 'ninja-forms' ); ?></label></th>
			<td>
				<select name="settings[success_message_loc]">
					<?php
					foreach ( $loc_opts as $opt ) {
						?>
						<option value="<?php echo $opt['action'];?>" <?php selected( nf_get_object_meta_value( $id, 'success_message_loc' ), $opt['action'] ); ?>><?php echo $opt['name'];?></option>
						<?php
					}
					?>
				</select>
			</td>
		</tr> -->
		<tr>
			<th scope="row"><label for="success_msg"><?php _e( 'Message', 'ninja-forms' ); ?></label></th>
			<td>
				<?php wp_editor( nf_get_object_meta_value( $id, 'success_msg' ), 'success_msg', $settings ); ?>
			</td>
		</tr>

		<?php
	}

	/**
	 * Process our Success Message notification
	 * 
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function process( $id ) {
		global $ninja_forms_processing;

		// We need to get our name setting so that we can use it to create a unique success message ID.
		$name = Ninja_Forms()->notification( $id )->get_setting( 'name' );
		// If our name is empty, we need to generate a random string.
		if ( empty ( $name ) ) {
			$name = ninja_forms_random_string( 4 );
		}
		$success_msg = apply_filters( 'nf_success_msg', Ninja_Forms()->notification( $id )->get_setting( 'success_msg' ), $id );
		$success_msg = do_shortcode( wpautop( $success_msg ) );
		$success_msg = nf_parse_fields_shortcode( $success_msg );
		$ninja_forms_processing->add_success_msg( 'success_msg-' . $name, $success_msg );
	}
}

return new NF_Notification_Success_Message();