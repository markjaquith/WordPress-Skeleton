<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Import a serialized ninja form
 *
 * @since unknown
 * @returns int
 */
function ninja_forms_import_form( $file ){
	global $wpdb;
	$form = unserialize( trim( $file ) );
	$form_fields = isset( $form['field'] ) ? $form['field'] : null;
	$notifications = isset ( $form['notifications'] ) ? $form['notifications'] : null;

	unset ( $form['field'] );
	unset ( $form['notifications'] );

	$form = apply_filters( 'ninja_forms_before_import_form', $form );
	// Remove our last_sub setting. This is our starting seq_num.
	if ( isset ( $form['data']['last_sub'] ) )
		unset( $form['data']['last_sub'] );

	// Create our form
	$form_id = Ninja_Forms()->form()->create( $form['data'] );

	$form['id'] = $form_id;

	if(is_array($form_fields)){
		for ($x=0; $x < count( $form_fields ); $x++) {
			$form_fields[$x]['form_id'] = $form_id;
			$form_fields[$x]['data'] = apply_filters( 'nf_before_import_field', $form_fields[$x]['data'], $form_fields[$x]['id'] );
			$form_fields[$x]['data'] = serialize( $form_fields[$x]['data'] );
			$old_field_id = $form_fields[$x]['id'];
			$form_fields[$x]['id'] = NULL;
			$wpdb->insert( NINJA_FORMS_FIELDS_TABLE_NAME, $form_fields[$x] );
			$form_fields[$x]['id'] = $wpdb->insert_id;
			$form_fields[$x]['old_id'] = $old_field_id;
			$form_fields[$x]['data'] = unserialize( $form_fields[$x]['data'] );
		}
	}

	$form['field'] = $form_fields;
	$form['notifications'] = $notifications;	

	// Insert any notifications we might have.
	if ( is_array( $notifications ) ) {
		foreach ( $notifications as $n ) {
			$n_id = nf_insert_notification( $form_id );
			$n = apply_filters( 'nf_import_notification_meta', $n, $n_id, $form );
			unset( $n['conditions'] );
			foreach ( $n as $meta_key => $meta_value ) {
				foreach ( $form_fields as $field ) {
					// We need to replace any references to old fields in our notification
					if ( 'email_message' == $meta_key ) {
						$meta_value = str_replace( '[ninja_forms_field id=' . $field['old_id'].']', '[ninja_forms_field id='.$field['id'].']', $meta_value );
						$meta_value = str_replace( 'ninja_forms_field_' . $field['old_id'], 'ninja_forms_field_' . $field['id'], $meta_value );
					} else {
						$meta_value = preg_replace( '/\bfield_' . $field['old_id'] . '\b/u', 'field_' . $field['id'], $meta_value );
					}
				}
				nf_update_object_meta( $n_id, $meta_key, $meta_value );
			}
		}
	}

	
	do_action( 'ninja_forms_after_import_form', $form );
	return $form['id'];
}