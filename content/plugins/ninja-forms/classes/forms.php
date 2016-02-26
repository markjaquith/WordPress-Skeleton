<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Handles adding and removing forms.
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Form
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.9
*/

class NF_Forms {

	/**
	 * Store our array of form ids
	 * 
	 * @since 2.9
	 */
	var $forms = array();
	
	/**
	 * Get things started
	 * 
	 * @access public
	 * @since 2.9
	 * @return void
	 */
	public function __construct() {
		add_action( 'nf_maybe_delete_form', array( $this, 'maybe_delete' ) );
	}

	/**
	 * Get all forms
	 * 
	 * @access public
	 * @since 2.9
	 * @return array $forms
	 */
	public function get_all( $show_new = false ) {
		global $wpdb;

		$debug = ! empty ( $_REQUEST['debug'] ) ? true : false;

		if ( empty ( $this->forms ) ) {
			$forms = nf_get_objects_by_type( 'form' );

			$tmp_array = array();
			foreach ( $forms as $form ) {
				$form_id = $form['id'];

				$status = Ninja_Forms()->form( $form_id )->get_setting( 'status' );
				if ( ( $status == 'new' && $show_new ) || $status != 'new' ) {
					$title = Ninja_Forms()->form( $form_id )->get_setting( 'form_title' );
					if ( strpos( $title, '_' ) === 0 ) {
						if ( $debug )
							$tmp_array[] = $form_id;
					} else {
						$tmp_array[] = $form_id;
					}
				}
			}
			$this->forms = $tmp_array;
		}

		return $this->forms;
	}

	/**
	 * Delete a form if it is created and not saved within 24 hrs.
	 * 
	 * @access public
	 * @since 2.9
	 * @return void
	 */
	public function maybe_delete( $form_id ) {
		$status = Ninja_Forms()->form( $form_id )->get_setting( 'status' );
		if ( 'new' == $status ) {
			Ninja_Forms()->form( $form_id )->delete();
		}
	}

	/**
	 * Update cached forms
	 * 
	 * @access public
	 * @since 2.9
	 * @return void
	 */
	public function update_cache( $debug = false, $show_new = false ) {
		$this->forms = array();
		$this->get_all( $debug, $show_new );
	}
}