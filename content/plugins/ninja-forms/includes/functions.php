<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_return_echo($function_name){
	$arguments = func_get_args();
    array_shift($arguments); // We need to remove the first arg ($function_name)
    ob_start();
    call_user_func_array($function_name, $arguments);
	$return = ob_get_clean();
	return $return;
}

function ninja_forms_random_string($length = 10){
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $random_string;
}

function ninja_forms_remove_from_array($arr, $key, $val, $within = FALSE) {
    foreach ($arr as $i => $array)
            if ($within && stripos($array[$key], $val) !== FALSE && (gettype($val) === gettype($array[$key])))
                unset($arr[$i]);
            elseif ($array[$key] === $val)
                unset($arr[$i]);

    return array_values($arr);
}

function ninja_forms_letters_to_numbers( $size ) {
	$l		= substr( $size, -1 );
	$ret	= substr( $size, 0, -1 );
	switch( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
		case 'T':
			$ret *= 1024;
		case 'G':
			$ret *= 1024;
		case 'M':
			$ret *= 1024;
		case 'K':
			$ret *= 1024;
	}
	return $ret;
}

function ninja_forms_subval_sort( $a, $subkey ) {
	$b = '';
	foreach($a as $k=>$v) {
		$b[$k] = strtolower($v[$subkey]);
	}
	if ( is_array ( $b ) ) {
		asort($b);
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		return $c;
	} else {
		return $a;
	}

}

/**
 * Takes a field ID and returns the admin label if it exists and the label if it does not.
 *
 * @since 2.8
 * @param int $field_id
 * @return string $label
 */
function nf_get_field_admin_label( $field_id, $form_id = '' ) {

	if ( empty ( $form_id ) ) {
		$form = ninja_forms_get_form_by_field_id( $field_id );
		$form_id = $form['id'];
	}

	$admin_label = isset( Ninja_Forms()->form( $form_id )->fields[ $field_id ]['data']['admin_label'] ) ? Ninja_Forms()->form( $form_id )->fields[ $field_id ]['data']['admin_label'] : '';
	$field_label = isset( Ninja_Forms()->form( $form_id )->fields[ $field_id ]['data']['label'] ) ? Ninja_Forms()->form( $form_id )->fields[ $field_id ]['data']['label'] : '';

	if ( ! empty( $admin_label ) ) {
		$label = $admin_label;
	} else {
		$label = $field_label;
	}

	return $label;
}

/**
 * Return the begin date with an added 00:00:00.
 * Checks for the current date format setting and tries to respect it.
 *
 * @since 2.7
 * @param string $begin_date
 * @return string $begin_date
 */
function nf_get_begin_date( $begin_date ) {
	$plugin_settings = nf_get_settings();

	if ( isset ( $plugin_settings['date_format'] ) ) {
		$date_format = $plugin_settings['date_format'];
	} else {
		$date_format = 'm/d/Y';
	}

	if ( $date_format == 'd/m/Y' ) {
		$begin_date = str_replace( '/', '-', $begin_date );
	} else if ( $date_format == 'm-d-Y' ) {
		$begin_date = str_replace( '-', '/', $begin_date );
	}
	$begin_date .= '00:00:00';
	$begin_date = new DateTime( $begin_date );

	return $begin_date;
}

/**
 * Return the end date with an added 23:59:59.
 * Checks for the current date format setting and tries to respect it.
 *
 * @since 2.7
 * @param string $end_date
 * @return string $end_date
 */
function nf_get_end_date( $end_date ) {
	$plugin_settings = nf_get_settings();

	if ( isset ( $plugin_settings['date_format'] ) ) {
		$date_format = $plugin_settings['date_format'];
	} else {
		$date_format = 'm/d/Y';
	}

	if ( $date_format == 'd/m/Y' ) {
		$end_date = str_replace( '/', '-', $end_date );
	} else if ( $date_format == 'm-d-Y' ) {
		$end_date = str_replace( '-', '/', $end_date );
	}
	$end_date .= '23:59:59';
	$end_date = new DateTime( $end_date );

	return $end_date;
}

/**
 * Checks whether function is disabled.
 *
 * @since 2.7
 *
 * @param string  $function Name of the function.
 * @return bool Whether or not function is disabled.
 */
function nf_is_func_disabled( $function ) {
	$disabled = explode( ',',  ini_get( 'disable_functions' ) );

	return in_array( $function, $disabled );
}

/**
 * Acts as a wrapper/alias for nf_get_objects_by_type that is specific to notifications.
 *
 * @since 2.8
 * @return array $notifications
 */
function nf_get_all_notifications() {
	return nf_get_objects_by_type( 'notification' );
}

/**
 * Acts as a wrapper/alias for nf_get_object_children that is specific to notifications.
 *
 * @since 2.8
 * @param string $form_id
 * @return array $notifications
 */

function nf_get_notifications_by_form_id( $form_id, $full_data = true ) {
	return nf_get_object_children( $form_id, 'notification', $full_data );
}

/**
 * Acts as a wrapper/alias for nf_get_object_meta
 *
 * @since 2.8
 * @param string $id
 * @return array $notification
 */

function nf_get_notification_by_id( $notification_id ) {
	return nf_get_object_meta( $notification_id );
}

/**
 * Insert a notification into the database.
 *
 * Calls nf_insert_object()
 * Calls nf_add_relationship()
 * Calls nf_update_object_meta()
 *
 * @since 2.8
 * @param int $form_id
 * @return int $n_id
 */

function nf_insert_notification( $form_id = '' ) {
	if ( empty ( $form_id ) )
		return false;

	$n_id = nf_insert_object( 'notification' );
	nf_add_relationship( $n_id, 'notification', $form_id, 'form' );
	$date_updated = date( 'Y-m-d', current_time( 'timestamp' ) );
	nf_update_object_meta( $n_id, 'date_updated', $date_updated );
	return $n_id;
}

/**
 * Delete a notification.
 *
 * Acts as a wrapper/alias for nf_delete_object
 *
 * @since 2.8
 * @param int $n_id
 * @return void
 */

function nf_delete_notification( $n_id ) {
	nf_delete_object( $n_id );
}


/**
 * Function that gets a piece of object meta
 *
 * @since 2.8
 * @param string $object_id
 * @param string $meta_key
 * @return var $meta_value
 */

function nf_get_object_meta_value( $object_id, $meta_key ) {
	global $wpdb;

	$meta_value = $wpdb->get_row( $wpdb->prepare( 'SELECT meta_value FROM ' . NF_OBJECT_META_TABLE_NAME . ' WHERE object_id = %d AND meta_key = %s', $object_id, $meta_key ), ARRAY_A );
	if ( is_array ( $meta_value['meta_value'] ) ) {
		$meta_value['meta_value'] = unserialize(  $meta_value['meta_value'] );
	}

	return $meta_value['meta_value'];
}


/**
 * Function that gets children objects by type and parent id
 *
 * @since 2.8
 * @param string $parent_id
 * @param string $type
 * @return array $children
 */

function nf_get_object_children( $object_id, $child_type = '', $full_data = true, $include_forms = true ) {
	global $wpdb;


	if ( $include_forms ) {
		if ( $child_type != '' ) {
			$children = $wpdb->get_results( $wpdb->prepare( "SELECT child_id FROM " . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . " WHERE child_type = %s AND parent_id = %d", $child_type, $object_id ), ARRAY_A);
		} else {
			$children = $wpdb->get_results( $wpdb->prepare( "SELECT child_id FROM " . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . " WHERE parent_id = %d", $object_id ), ARRAY_A);
		}
	} else {
		if ( $child_type != '' ) {
			$children = $wpdb->get_results( $wpdb->prepare( "SELECT child_id FROM " . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . " WHERE child_type = %s AND parent_id = %d AND parent_type <> 'form'", $child_type, $object_id ), ARRAY_A);
		} else {
			$children = $wpdb->get_results( $wpdb->prepare( "SELECT child_id FROM " . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . " WHERE parent_id = %d AND parent_type <> 'form'", $object_id ), ARRAY_A);
		}
	}

	$tmp_array = array();
	if ( $full_data ) {
		foreach( $children as $id ) {
			$child_id = $id['child_id'];
			$settings = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM " . NF_OBJECT_META_TABLE_NAME . " WHERE object_id = %d", $child_id ), ARRAY_A);
			if ( ! empty( $settings ) ) {
				foreach ( $settings as $s ) {
					if ( is_array ( $s['meta_value'] ) ) {
						$s['meta_value'] =  unserialize( $s['meta_value'] );
					}
					$tmp_array[ $child_id ][ $s['meta_key'] ] = $s['meta_value'];
				}
			} else {
				$tmp_array[ $child_id ] = array();
			}
		}


	} else {
		if ( is_array( $children ) ) {
			foreach ( $children as $child ) {
				$tmp_array[] = $child['child_id'];
			}
		}
	}

	return $tmp_array;
}

/**
 * Function that updates a piece of object meta
 *
 * @since 3.0
 * @param string $object_id
 * @param string $meta_key
 * @param string $meta_value
 * @return string $meta_id
 */

function nf_update_object_meta( $object_id, $meta_key, $meta_value ) {
	global $wpdb;

	if ( is_array( $meta_value ) ) {
		$meta_value = serialize( $meta_value );
	}

	// Check to see if this meta_key/meta_value pair exist for this object_id.
	$found = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM ".NF_OBJECT_META_TABLE_NAME." WHERE object_id = %d AND meta_key = %s", $object_id, $meta_key ), ARRAY_A );

	if ( $found ) {
		$wpdb->update( NF_OBJECT_META_TABLE_NAME, array( 'meta_value' => $meta_value ), array( 'meta_key' => $meta_key, 'object_id' => $object_id ) );
		$meta_id = $found['id'];
	} else {
		$wpdb->insert( NF_OBJECT_META_TABLE_NAME, array( 'object_id' => $object_id, 'meta_key' => $meta_key, 'meta_value' => $meta_value ) );
		$meta_id = $wpdb->insert_id;
	}

	return $meta_id;
}

/**
 * Function that gets all the meta values attached to a given object.
 *
 * @since 2.8
 * @param string $object
 * @return array $settings
 */
function nf_get_object_meta( $object_id ) {
	global $wpdb;

	$tmp_array = array();
	$settings = $wpdb->get_results( $wpdb->prepare( 'SELECT meta_key, meta_value FROM ' . NF_OBJECT_META_TABLE_NAME . ' WHERE object_id = %d', $object_id ), ARRAY_A);

	if ( is_array( $settings ) ) {
		foreach( $settings as $setting ) {
			$tmp_array[ $setting['meta_key'] ] = $setting['meta_value'] = maybe_unserialize( $setting['meta_value'] );
		}
	}

	return $tmp_array;
}

/**
 * Insert an object.
 *
 * @since 3.0
 * @param string $type
 * @return int $object_id
 */

function nf_insert_object( $type, $id = NULL ) {
	global $wpdb;
	$wpdb->insert( NF_OBJECTS_TABLE_NAME, array( 'id' => $id, 'type' => $type ) );
	return $wpdb->insert_id;
}

/**
 * Delete an object. Also removes all of the objectmeta attached to the object and any references to it in the relationship table.
 *
 * @since 2.8
 * @param int $object_id
 * @return bool
 */

function nf_delete_object( $object_id ) {
	global $wpdb;

	// Check to see if we have any object children.
	$children = nf_get_object_children( $object_id, '', false, false );

	foreach ( $children as $child_id ) {
		nf_delete_object( $child_id );
	}

	// Delete this object.
	$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . NF_OBJECTS_TABLE_NAME .' WHERE id = %d', $object_id ) );

	// Delete any objectmeta attached to this object.
	$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . NF_OBJECT_META_TABLE_NAME .' WHERE object_id = %d', $object_id ) );

	// Delete any references to this object in the relationship table
	$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . NF_OBJECT_RELATIONSHIPS_TABLE_NAME .' WHERE child_id = %d OR parent_id = %d', $object_id, $object_id ) );

	return true;
}


/**
 * Create a relationship between two objects
 *
 * @since 2.8
 * @param int $child_id
 * @param string child_type
 * @param int $parent_id
 * @param string $parent_type
 * @return void
 */

function nf_add_relationship( $child_id, $child_type, $parent_id, $parent_type ) {
	global $wpdb;
	// Make sure that our relationship doesn't already exist.
	$count = $wpdb->query( $wpdb->prepare( 'SELECT id FROM ' . NF_OBJECT_RELATIONSHIPS_TABLE_NAME .' WHERE child_id = %d AND parent_id = %d', $child_id, $parent_id ), ARRAY_A );
	if ( empty( $count ) ) {
		$wpdb->insert( NF_OBJECT_RELATIONSHIPS_TABLE_NAME, array( 'child_id' => $child_id, 'child_type' => $child_type, 'parent_id' => $parent_id, 'parent_type' => $parent_type ) );
	}
}

/**
 * Get an object's parent
 *
 * @since 2.8
 * @param int $child_id
 * @return int $parent_id
 */

function nf_get_object_parent( $child_id ) {
	global $wpdb;
	// Check our relationship table for where this ID appears as a child.
	$parent = $wpdb->get_row( $wpdb->prepare( 'SELECT parent_id FROM ' . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . ' WHERE child_id = %d', $child_id ), ARRAY_A );
	return $parent['parent_id'];
}

/**
 * Get an object's type
 *
 * @since 2.8.6
 * @param $object_id
 * @return string $return
 */

function nf_get_object_type( $object_id ) {
	global $wpdb;
	// Get our object type
	$type = $wpdb->get_row( $wpdb->prepare( 'SELECT type FROM ' . NF_OBJECTS_TABLE_NAME . ' WHERE id = %d', $object_id ), ARRAY_A );
	$return = ( isset ( $type['type'] ) ) ? $type['type'] : false;
	return $return;
}

/*
 * Get User IP
 *
 * Returns the IP address of the current visitor
 *
 * @since 2.8
 * @return string $ip User's IP address
 */
function nf_get_ip() {

    $ip = '127.0.0.1';

    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return apply_filters( 'nf_get_ip', $ip );
}


/**
 * Function that gets all objects of a given type.
 *
 * @since 2.8
 * @return array $results
 */

function nf_get_objects_by_type( $object_type ) {
	global $wpdb;

	// Bail if we don't have an object type.
	if ( $object_type == '' )
		return false;

	$results = $wpdb->get_results( $wpdb->prepare( 'SELECT id FROM ' . NF_OBJECTS_TABLE_NAME . ' WHERE type = %s', $object_type ), ARRAY_A );

	return $results;
}

/**
 * Add filters so that users given the ability to see the "All Forms" table and the add new form page
 * can add new fields and delete forms.
 *
 * @since 2.8.6
 * @return void
 */
function nf_add_permissions_filters( $cap ) {
	return apply_filters( 'ninja_forms_admin_all_forms_capabilities', $cap );
}

add_filter( 'nf_new_field_capabilities', 'nf_add_permissions_filters' );
add_filter( 'nf_delete_field_capabilities', 'nf_add_permissions_filters' );
add_filter( 'nf_delete_form_capabilities', 'nf_add_permissions_filters' );

function nf_admin_footer_text( $footer_text ) {
	global $current_screen, $pagenow, $typenow;

	$current_tab = ninja_forms_get_current_tab();

	// only display custom text on Ninja Admin Pages
	if ( isset( $current_screen->id ) && strpos( $current_screen->id, 'ninja' ) !== false || ( ( $pagenow == 'edit.php' || $pagenow == 'post.php' ) && $typenow == 'nf_sub' ) ) {
		$footer_text = sprintf( __( 'Please rate %sNinja Forms%s %s on %sWordPress.org%s to help us keep this plugin free.  Thank you from the WP Ninjas team!', 'ninja-forms' ), '<strong>', '</strong>', '<a href="http://wordpress.org/support/view/plugin-reviews/ninja-forms?filter=5" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>', '<a href="http://wordpress.org/support/view/plugin-reviews/ninja-forms?filter=5" target="_blank">', '</a>' );
	}

	if ( 'builder' == $current_tab ) {
		$footer_text = '';
	}

	return $footer_text;
}

add_filter( 'admin_footer_text', 'nf_admin_footer_text' , 1, 2 );
