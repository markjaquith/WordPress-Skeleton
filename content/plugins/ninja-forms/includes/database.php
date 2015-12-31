<?php if ( ! defined( 'ABSPATH' ) ) exit;

// Begin Form Interaction Functions

function ninja_forms_insert_field( $form_id, $args = array() ){
	global $wpdb;
	$insert_array = array();

	$insert_array['type'] = $args['type'];
	$insert_array['form_id'] = $form_id;

	if( isset( $args['data'] ) ){
		$insert_array['data'] = $args['data'];
	}else{
		$insert_array['data'] = '';
	}

	if( isset( $args['order'] ) ){
		$insert_array['order'] = $args['order'];
	}else{
		$insert_array['order'] = 999;
	}

	if( isset( $args['fav_id'] ) ){
		$insert_array['fav_id'] = $args['fav_id'];
	}

	if( isset( $args['def_id'] ) ){
		$insert_array['def_id'] = $args['def_id'];
	}

	$new_field = $wpdb->insert( NINJA_FORMS_FIELDS_TABLE_NAME, $insert_array );
	$new_id = $wpdb->insert_id;
	return $new_id;
}

function ninja_forms_get_form_ids_by_post_id( $post_id ){
	global $wpdb;
	$form_ids = array();
	if( is_page( $post_id ) ){
		$form_results = ninja_forms_get_all_forms();
		if(is_array($form_results) AND !empty($form_results)){
			foreach($form_results as $form){
				$form_data = $form['data'];
				if(isset($form_data['append_page']) AND !empty($form_data['append_page'])){
					if($form_data['append_page'] == $post_id){
						$form_ids[] = $form['id'];
					}
				}
			}
		}
		$form_id = get_post_meta( $post_id, 'ninja_forms_form', true );
		if( !empty( $form_id ) ){
			$form_ids[] = $form_id;
		}
	}else if( is_single( $post_id ) ){
		$form_id = get_post_meta( $post_id, 'ninja_forms_form', true );
		if( !empty( $form_id ) ){
			$form_ids[] = $form_id;
		}
	}

	return $form_ids;
}

function ninja_forms_get_form_by_sub_id( $sub_id ){
	global $wpdb;
	$form_id = Ninja_Forms()->sub( $sub_id )->form_id;
	$form_row = ninja_forms_get_form_by_id( $form_id );
	return $form_row;
}

// The ninja_forms_delete_form( $form_id ) function is in includes/deprecated.php

// Begin Field Interaction Functions

function ninja_forms_get_field_by_id($field_id){
	global $wpdb;
	$field_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE id = %d", $field_id), ARRAY_A);
	if( $field_row != null ){
		$field_row['data'] = unserialize($field_row['data']);
		return $field_row;
	}else{
		return false;
	}
}

function ninja_forms_get_fields_by_form_id($form_id, $orderby = 'ORDER BY `order` ASC'){
	global $wpdb;

	$field_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE form_id = %d ".$orderby, $form_id), ARRAY_A);
	if(is_array($field_results) AND !empty($field_results)){
		$x = 0;
		$count = count($field_results) - 1;
		while($x <= $count){
			$field_results[$x]['data'] = unserialize($field_results[$x]['data']);
			$x++;
		}
	}

	return $field_results;
}

function ninja_forms_get_all_fields(){
	global $wpdb;
	$field_results = $wpdb->get_results("SELECT * FROM ".NINJA_FORMS_FIELDS_TABLE_NAME, ARRAY_A);
	if(is_array($field_results) AND !empty($field_results)){
		$x = 0;
		$count = count($field_results) - 1;
		while($x <= $count){
			$field_results[$x]['data'] = unserialize($field_results[$x]['data']);
			$x++;
		}
	}
	return $field_results;
}

function ninja_forms_update_field($args){
	global $wpdb;
	$update_array = $args['update_array'];
	$where = $args['where'];
	$wpdb->update(NINJA_FORMS_FIELDS_TABLE_NAME, $update_array, $where);
}

function ninja_forms_delete_field( $field_id ){
	global $wpdb;
	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE id = %d", $field_id), ARRAY_A);
}

// Begin Favorite Fields Interaction Functions

function ninja_forms_get_fav_by_id($fav_id){
	global $wpdb;
	$fav_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE id = %d", $fav_id), ARRAY_A);
	$fav_row['data'] = unserialize($fav_row['data']);

	return $fav_row;
}

function ninja_forms_delete_fav_by_id($fav_id){
	global $wpdb;
	$wpdb->query($wpdb->prepare("DELETE FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE id = %d", $fav_id), ARRAY_A);
}

function ninja_forms_get_all_favs(){
	global $wpdb;
	$fav_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE row_type = %d ORDER BY name ASC", 1), ARRAY_A);
	if(is_array($fav_results) AND !empty($fav_results)){
		$x = 0;
		$count = count($fav_results) - 1;
		while($x <= $count){
			$fav_results[$x]['data'] = unserialize($fav_results[$x]['data']);
			$x++;
		}
	}
	return $fav_results;
}

// Begin Defined Fields Functions

function ninja_forms_get_def_by_id($def_id){
	global $wpdb;
	$def_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE id = %d", $def_id), ARRAY_A);
	$def_row['data'] = unserialize($def_row['data']);
	return $def_row;
}

function ninja_forms_get_all_defs(){
	global $wpdb;
	$def_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FAV_FIELDS_TABLE_NAME." WHERE row_type = %d", 0), ARRAY_A);
	if(is_array($def_results) AND !empty($def_results)){
		$x = 0;
		$count = count($def_results) - 1;
		while($x <= $count){
			$def_results[$x]['data'] = unserialize($def_results[$x]['data']);
			$x++;
		}
	}
	return $def_results;
}

function ninja_forms_addslashes_deep( $value ){
    $value = is_array($value) ?
        array_map('ninja_forms_addslashes_deep', $value) :
        addslashes($value);
    return $value;
}

function utf8_encode_recursive( $input ){
    if ( is_array( $input ) )    {
        return array_map( __FUNCTION__, $input );
    }else{
        return utf8_encode( $input );
    }
}

function ninja_forms_str_replace_deep($search, $replace, $subject){
    if( is_array( $subject ) ){
        foreach( $subject as &$oneSubject )
            $oneSubject = ninja_forms_str_replace_deep($search, $replace, $oneSubject);
        unset($oneSubject);
        return $subject;
    } else {
        return str_replace($search, $replace, $subject);
    }
}

function ninja_forms_html_entity_decode_deep( $value, $flag = ENT_COMPAT ){
    $value = is_array($value) ?
        array_map('ninja_forms_html_entity_decode_deep', $value) :
        html_entity_decode( $value, $flag );
    return $value;
}

function ninja_forms_htmlspecialchars_deep( $value ){
    $value = is_array($value) ?
        array_map('ninja_forms_htmlspecialchars_deep', $value) :
        htmlspecialchars( $value );
    return $value;
}

function ninja_forms_stripslashes_deep( $value ){
    $value = is_array($value) ?
        array_map('ninja_forms_stripslashes_deep', $value) :
        stripslashes($value);
    return $value;
}

function ninja_forms_esc_html_deep( $value ){
    $value = is_array($value) ?
        array_map('ninja_forms_esc_html_deep', $value) :
        esc_html($value);
    return $value;
}

function nf_wp_kses_post_deep( $value ){
    $value = is_array( $value ) ?
        array_map( 'nf_wp_kses_post_deep', $value ) :
        wp_kses_post($value);

    return $value;
}

function ninja_forms_strip_tags_deep($value ){
 	$value = is_array($value) ?
        array_map('ninja_forms_strip_tags_deep', $value) :
        strip_tags($value);
    return $value;
}

function ninja_forms_json_response(){
	global $ninja_forms_processing;

	$form_id = $ninja_forms_processing->get_form_ID();

	$errors = $ninja_forms_processing->get_all_errors();
	$success = $ninja_forms_processing->get_all_success_msgs();
	$fields = $ninja_forms_processing->get_all_fields();
	$form_settings = $ninja_forms_processing->get_all_form_settings();
	$extras = $ninja_forms_processing->get_all_extras();

	// Success will default to false if there is not success message.
	if ( ! $success && ! $errors ) $success = true;

	if( version_compare( phpversion(), '5.3', '>=' ) ){
		$json = json_encode( array( 'form_id' => $form_id, 'errors' => $errors, 'success' => $success, 'fields' => $fields, 'form_settings' => $form_settings, 'extras' => $extras ), JSON_HEX_QUOT | JSON_HEX_TAG  );
	}else{


		$errors = ninja_forms_html_entity_decode_deep( $errors );
		$success = ninja_forms_html_entity_decode_deep( $success );
		$fields = ninja_forms_html_entity_decode_deep( $fields );
		$form_settings = ninja_forms_html_entity_decode_deep( $form_settings );
		$extras = ninja_forms_html_entity_decode_deep( $extras );

		$errors = utf8_encode_recursive( $errors );
		$success = utf8_encode_recursive( $success );
		$fields = utf8_encode_recursive( $fields );
		$form_settings = utf8_encode_recursive( $form_settings );
		$extras = utf8_encode_recursive( $extras );

		$errors = ninja_forms_str_replace_deep( '"', "\u0022", $errors );
		$errors = ninja_forms_str_replace_deep( "'", "\u0027", $errors );
		$errors = ninja_forms_str_replace_deep( '<', "\u003C", $errors );
		$errors = ninja_forms_str_replace_deep( '>', "\u003E", $errors );

		$success = ninja_forms_str_replace_deep( '"', "\u0022", $success );
		$success = ninja_forms_str_replace_deep( "'", "\u0027", $success );
		$success = ninja_forms_str_replace_deep( '<', "\u003C", $success );
		$success = ninja_forms_str_replace_deep( '>', "\u003E", $success );

		$fields = ninja_forms_str_replace_deep( '"', "\u0022", $fields );
		$fields = ninja_forms_str_replace_deep( "'", "\u0027", $fields );
		$fields = ninja_forms_str_replace_deep( '<', "\u003C", $fields );
		$fields = ninja_forms_str_replace_deep( '>', "\u003E", $fields );

		$form_settings = ninja_forms_str_replace_deep( '"', "\u0022", $form_settings );
		$form_settings = ninja_forms_str_replace_deep( "'", "\u0027", $form_settings );
		$form_settings = ninja_forms_str_replace_deep( '<', "\u003C", $form_settings );
		$form_settings = ninja_forms_str_replace_deep( '>', "\u003E", $form_settings );

		$extras = ninja_forms_str_replace_deep( '"', "\u0022", $extras );
		$extras = ninja_forms_str_replace_deep( "'", "\u0027", $extras );
		$extras = ninja_forms_str_replace_deep( '<', "\u003C", $extras );
		$extras = ninja_forms_str_replace_deep( '>', "\u003E", $extras );

		$json = json_encode( array( 'form_id' => $form_id, 'errors' => $errors, 'success' => $success, 'fields' => $fields, 'form_settings' => $form_settings, 'extras' => $extras ) );
		$json = str_replace( "\\\u0022", "\\u0022", $json );
		$json = str_replace( "\\\u0027", "\\u0027", $json );
		$json = str_replace( "\\\u003C", "\\u003C", $json );
		$json = str_replace( "\\\u003E", "\\u003E", $json );
	}

	return $json;
}

/*
 *
 * Function that sets up our transient variable.
 *
 * @since 2.2.45
 * @return void
 */

function ninja_forms_set_transient(){
	global $ninja_forms_processing;

	$form_id = $ninja_forms_processing->get_form_ID();
	$transient_id = Ninja_Forms()->session->get( 'nf_transient_id' );
	if ( ! $transient_id ) {
		$transient_id = Ninja_Forms()->set_transient_id();
	}
	// Setup our transient variable.
	$transient = array();
	$transient['form_id'] = $form_id;
	$transient['field_values'] = $ninja_forms_processing->get_all_fields();
	$transient['form_settings'] = $ninja_forms_processing->get_all_form_settings();
	$transient['extra_values'] = $ninja_forms_processing->get_all_extras();
	$all_fields_settings = array();
	if ( $ninja_forms_processing->get_all_fields() ) {
		foreach ( $ninja_forms_processing->get_all_fields() as $field_id => $user_value ) {
			$field_settings = $ninja_forms_processing->get_field_settings( $field_id );
			$all_fields_settings[$field_id] = $field_settings;
		}
	}

	$transient['field_settings'] = $all_fields_settings;

	// Set errors and success messages as Ninja_Forms()->session variables.
	$success = $ninja_forms_processing->get_all_success_msgs();
	$errors = $ninja_forms_processing->get_all_errors();

	$transient['success_msgs'] = $success;
	$transient['error_msgs'] = $errors;

	//delete_transient( 'ninja_forms_test' );
	set_transient( $transient_id, $transient, DAY_IN_SECONDS );
}

/*
 *
 * Function that deletes our transient variable
 *
 * @since 2.2.45
 * @return void
 */

function ninja_forms_delete_transient(){
	$transient_id = Ninja_Forms()->session->get( 'nf_transient_id' );
	if( $transient_id ) {
		delete_transient( $transient_id );
	}
}

/**
 * Get a count of submissions for a form
 *
 * @since 2.7
 * @param int $post_id
 * @return int $count
 */
function nf_get_sub_count( $form_id, $post_status = 'publish' ) {
	global $wpdb;

	$meta_key = '_form_id';
	$meta_value = $form_id;

	$sql = "SELECT count(DISTINCT pm.post_id)
	FROM $wpdb->postmeta pm
	JOIN $wpdb->posts p ON (p.ID = pm.post_id)
	WHERE pm.meta_key = '$meta_key'
	AND pm.meta_value = '$meta_value'
	AND p.post_type = 'nf_sub'
	AND p.post_status = '$post_status'";

	$count = $wpdb->get_var($sql);

	return $count;
}

/**
 * Get an array of our fields by form ID.
 * The returned array has the field_ID as the key.
 *
 * @since 2.7
 * @param int $form_id
 * @return array $tmp_array
 */
function nf_get_fields_by_form_id( $form_id, $orderby = 'ORDER BY `order` ASC' ){
	global $wpdb;

	$tmp_array = array();
	$field_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE form_id = %d ".$orderby, $form_id), ARRAY_A);
	if ( is_array( $field_results ) && ! empty( $field_results ) ) {
		foreach ( $field_results as $field ) {
			$field_id = $field['id'];
			$field['data'] = unserialize( $field['data'] );
			$tmp_array[ $field_id ] = $field;
		}
	}

	return $tmp_array;
}
