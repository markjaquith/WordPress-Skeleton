<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Notification
 * 
 * Single notification object.
 * This object lets us call it like: Ninja_Forms()->notification( 33 )->methods()
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Notifications
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.8
*/

class NF_Notification
{

	/**
	 * @var notification id
	 */
	var $id = '';

	/**
	 * @var type
	 */
	var $type = '';

	/**
	 * @var active
	 * Holds a boolean value.
	 */
	var $active = '';

	/**
	 * @var form_id
	 * Holds the id of our form.
	 */
	var $form_id = '';


	/**
	 * Get things rolling
	 * 
	 * @since 2.8
	 * @return void
	 */
	function __construct( $id ) {
		$this->id = $id;
		$this->type = nf_get_object_meta_value( $id, 'type' );
		$this->active = ( nf_get_object_meta_value( $id, 'active' ) == 1 ) ? true : false;
		$this->form_id = nf_get_object_parent( $id );
	}

	/**
	 * Ouptut our admin screen
	 * 
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function edit_screen() {
		$type = $this->type;
		// Call our type edit screen.
		Ninja_Forms()->notification_types[ $type ]->edit_screen( $this->id );
	}

	/**
	 * Delete our notification
	 * 
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function delete() {
		nf_delete_notification( $this->id );
	}

	/**
	 * Activate our notification
	 * 
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function activate() {
		nf_update_object_meta( $this->id, 'active', 1 );
		$this->active = true;
	}

	/**
	 * Deactivate our notification
	 * 
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function deactivate() {
		nf_update_object_meta( $this->id, 'active', 0 );
		$this->active = false;
	}

	/**
	 * Duplicate our notification
	 *
	 * @access public
	 * @since 2.8
	 * @return int $n_id
	 */
	public function duplicate() {
		$n_id = Ninja_Forms()->notifications->create( $this->form_id );
		$meta = nf_get_notification_by_id( $this->id );
		foreach ( $meta as $meta_key => $meta_value ) {
			nf_update_object_meta( $n_id, $meta_key, $meta_value );
		}

		$name = nf_get_object_meta_value( $n_id, 'name' ) . ' - ' . __( 'duplicate', 'ninja-forms' );
		nf_update_object_meta( $n_id, 'name', $name );
	}

	/**
	 * Run our notification processing function
	 * 
	 * @access public
	 * @since 2.8
	 * @return void
	 */
	public function process() {
		$type = $this->type;
		if ( isset ( Ninja_Forms()->notification_types[ $type ] ) && is_object( Ninja_Forms()->notification_types[ $type ] ) ) {
			Ninja_Forms()->notification_types[ $type ]->process( $this->id );			
		}
	}

	/**
	 * Get a notification setting
	 * 
	 * @access public
	 * @since 2.8
	 * @return string $meta_value
	 */
	public function get_setting( $meta_key ) {
		return nf_get_object_meta_value( $this->id, $meta_key );
	}

	/**
	 * Update a notification setting
	 * 
	 * @access public
	 * @since 2.8
	 * @return bool
	 */
	public function update_setting( $meta_key, $meta_value ) {
		nf_update_object_meta( $this->id, $meta_key, $meta_value );
		return true;
	}

	/**
	 * Get our notification type name
	 * 
	 * @access public
	 * @since 2.9
	 * @return string $name
	 */
	public function type_name() {
		$type = $this->type;
		// Call our type edit screen.
		return Ninja_Forms()->notification_types[ $type ]->name;
	}

}