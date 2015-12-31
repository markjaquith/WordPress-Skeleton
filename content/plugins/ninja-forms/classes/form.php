<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Handles the output of our form, as well as interacting with its settings.
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Form
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.7
*/

class NF_Form {

	/**
	 * @var form_id
	 * @since 2.7
	 */
	var $form_id;

	/**
	 * @var settings - Form Settings
	 * @since 2.7
	 */
	var $settings = array();

	/**
	 * @var fields - Form Fields
	 * @since 2.7
	 */
	var $fields = array();

	/**
	 * @var fields - Fields List
	 * @since 2.7
	 */
	var $field_keys = array();

	/**
	 * @var errors - Form errors
	 * @since 2.7
	 */
	var $errors = array();

	/**
	 * Get things started
	 * 
	 * @access public
	 * @since 2.7
	 * @return void
	 */
	public function __construct( $form_id = '' ) {
		if ( ! empty ( $form_id ) ) { // We've been passed a form id.
			// Set our current form id.
			$this->form_id = $form_id;
			$this->update_fields();
			$this->settings = nf_get_form_settings( $form_id );
		}
	}

	/**
	 * Add a form
	 * 
	 * @access public
	 * @since 2.9
	 * @return int $form_id
	 */
	public function create( $defaults = array() ) {
		$form_id = nf_insert_object( 'form' );
		$date_updated = date( 'Y-m-d', current_time( 'timestamp' ) );
		nf_update_object_meta( $form_id, 'date_updated', $date_updated );

		foreach( $defaults as $meta_key => $meta_value ) {
			nf_update_object_meta( $form_id, $meta_key, $meta_value );
		}

		// Add a single event hook that will check to see if this is an orphaned function.
		$timestamp = strtotime( '+24 hours', time() );
		$args = array(
			'form_id' => $form_id
		);
		wp_schedule_single_event( $timestamp, 'nf_maybe_delete_form', $args );
		return $form_id;
	}

	/**
	 * Insert a field into our form
	 * 
	 * @access public
	 * @since 2.9
	 * @return bool()
	 */
	public function insert_field( $field_id ) {
		return nf_add_relationship( $field_id, 'field', $this->form_id, 'form' );
	}

	/**
	 * Update our fields
	 * 
	 * @access public
	 * @since 2.9
	 * @return void
	 */
	public function update_fields() {
		$this->fields = nf_get_fields_by_form_id( $this->form_id );
	}

	/**
	 * Get one of our form settings.
	 * 
	 * @access public
	 * @since 2.7
	 * @return string $setting
	 */
	public function get_setting( $setting, $bypass_cache = false ) {
		if ( $bypass_cache ) {
			return nf_get_object_meta_value( $this->form_id, 'last_sub' );
		}
		if ( isset ( $this->settings[ $setting ] ) ) {
			return $this->settings[ $setting ];
		} else {
			return false;
		}
	}

	/**
	 * Update a form setting (this doesn't update anything in the database)
	 * Changes are only applied to this object.
	 * 
	 * @access public
	 * @since 2.8
	 * @param string $setting
	 * @param mixed $value
	 * @return bool
	 */
	public function update_setting( $setting, $value ) {
		$this->settings[ $setting ] = $value;
		nf_update_object_meta( $this->form_id, $setting, $value );
		$this->dump_cache();
		return true;
	}

	/**
	 * Get all of our settings
	 * 
	 * @access public
	 * @since 2.9
	 * @return array $settings
	 */
	public function get_all_settings() {
		return $this->settings;
	}

	/**
	 * Get all the submissions for this form
	 * 
	 * @access public
	 * @since 2.7
	 * @return array $sub_ids
	 */
	public function get_subs( $args = array() ) {
		$args['form_id'] = $this->form_id;
		return Ninja_Forms()->subs()->get( $args );
	}

	/**
	 * Return a count of the submissions this form has had
	 * 
	 * @access public
	 * @param array $args
	 * @since 2.7
	 * @return int $count
	 */
	public function sub_count( $args = array() ) {
		return count( $this->get_subs( $args ) );
	}

	/**
	 * Delete this form
	 * 
	 * @access public
	 * @since 2.9
	 */
	public function delete() {
		global $wpdb;
		// Delete this object.
		nf_delete_object( $this->form_id );
		// Delete any fields on this form.
		$wpdb->query($wpdb->prepare( "DELETE FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE form_id = %d", $this->form_id ) );
	}

    /**
     * Delete the cached form object (transient)
     *
     * @access public
     * @since 2.9.17
     */
    public function dump_cache()
    {
        delete_transient( 'nf_form_' . $this->form_id );
    }

    /**
     * Deprecated wrapper for dump_cache()
     *
     * @access public
     * @since 2.9.12
     */
    public function dumpCache()
    {
        $this->dump_cache();
    }

}
