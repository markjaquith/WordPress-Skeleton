<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Update form settings to the new storage system when the form is viewed for the first time.
 *
 * @since 2.9
 * @return void
 */
function nf_29_update_form_settings( $form_id ) {
	global $wpdb;

    // Check to see if the conversion has been reset.
    $is_reset = get_option( 'nf_converted_form_reset', 0 );

    // Check to see if an object exists with our form id.
    $type = nf_get_object_type($form_id);

    if ( $type ) {
        // We have an object with our form id.

        if ( $is_reset AND 'form' == $type ) {
            // Give precedence to the most recent form.

            // Set a new ID for the form being converted.
            $f_id = nf_insert_object('form');

            $fields = $wpdb->get_results("SELECT * FROM " . NINJA_FORMS_FIELDS_TABLE_NAME . " WHERE form_id = " . $form_id, ARRAY_A);

            foreach ($fields as $field) {

                unset($field['id']);

                $field['form_id'] = $f_id;

                // Copy the Fields to the new ID.
                $wpdb->insert(NINJA_FORMS_FIELDS_TABLE_NAME, $field);

            }

            $relationships = $wpdb->get_results("SELECT * FROM " . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . " WHERE parent_id = " . $form_id, ARRAY_A);

            foreach ($relationships as $relationship) {

                unset($relationship['id']);

                // Copy the object related to the form.
                $object = $wpdb->get_results("SELECT * FROM " . NF_OBJECTS_TABLE_NAME . " WHERE id = " . $relationship['child_id'], ARRAY_A);

                unset($object['id']);

                $wpdb->insert(NF_OBJECTS_TABLE_NAME, $object);

                $relationship['child_id'] = $wpdb->insert_id;

                $relationship['parent_id'] = $f_id;

                // Copy the Relationships to the new ID.
                $wpdb->insert(NF_OBJECT_RELATIONSHIPS_TABLE_NAME, $relationship);

            }

        } else {
            // Give precedence to the converting form.

            // Insert a new object.
            $next_id = nf_insert_object($type);

            // Replace all instances of the conflicting object ID with our new one.
            $wpdb->update(NF_OBJECT_META_TABLE_NAME, array('object_id' => $next_id), array('object_id' => $form_id));
            $wpdb->update(NF_OBJECT_RELATIONSHIPS_TABLE_NAME, array('parent_id' => $next_id), array('parent_type' => $type, 'parent_id' => $form_id));
            $wpdb->update(NF_OBJECT_RELATIONSHIPS_TABLE_NAME, array('child_id' => $next_id), array('child_type' => $type, 'child_id' => $form_id));

            // Delete the original (conflicting) object
            $wpdb->query('DELETE FROM ' . NF_OBJECTS_TABLE_NAME . ' WHERE id = ' . $form_id);

        }

    }

    // Get the form from the old table.
    $form = $wpdb->get_row( 'SELECT * FROM ' . NINJA_FORMS_TABLE_NAME . ' WHERE id = ' . $form_id, ARRAY_A );

    // Set the insert form ID, if not already set.
    $f_id = isset ( $f_id ) ? $f_id : nf_insert_object( 'form', $form['id'] );

    // Unpack the converted form's settings
    $settings = maybe_unserialize( $form['data'] );
    $settings['date_updated'] = $form['date_updated'];

	foreach ( $settings as $meta_key => $value ) {
		nf_update_object_meta( $f_id, $meta_key, $value );
	}
	nf_update_object_meta( $f_id, 'status', '' );
}

/**
 * Check our option to see if we've updated all of our form settings.
 * If we haven't, then update the form currently being viewed.
 * 
 * @since 2.9
 * @return void
 */
function nf_29_update_form_settings_check( $form_id ) {
	// Bail if we are in the admin
	if ( is_admin() )
		return false;

	// Bail if this form was created in 2.9 or higher.
	if ( 'form' == nf_get_object_type( $form_id ) )
		return false;

	// Get a list of forms that we've already converted.
	$completed_forms = get_option( 'nf_converted_forms', array() );

	if ( ! is_array( $completed_forms ) )
		$completed_forms = array();

	// Bail if we've already converted the db for this form.
	if ( in_array( $form_id, $completed_forms ) )
		return false;

	nf_29_update_form_settings( $form_id );

	$completed_forms[] = $form_id;
	update_option( 'nf_converted_forms', $completed_forms );
}

add_action( 'nf_before_display_loading', 'nf_29_update_form_settings_check' );