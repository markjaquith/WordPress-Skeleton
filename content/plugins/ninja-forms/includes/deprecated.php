<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Deprecated as of version 2.7.
 */

// Hook into our new save sub filter to add any deprecated filters
function nf_old_save_sub_filter( $user_value, $field_id ) {
	return apply_filters( 'ninja_forms_save_sub', $user_value, $field_id );
}

add_filter( 'nf_save_sub_user_value', 'nf_old_save_sub_filter', 10, 2 );

// Hook into our new nf_save_sub action and add any actions hooked into our old action hooks.
function nf_old_save_sub_action( $sub_id ) {
	do_action( 'ninja_forms_insert_sub', $sub_id );
}

add_action( 'nf_save_sub', 'nf_old_save_sub_action' );

// Hook into our new submissions CSV filename filter.
function nf_old_subs_csv_filename( $filename ) {
	return apply_filters( 'ninja_forms_export_subs_csv_file_name', $filename );
}

add_filter( 'nf_subs_csv_filename', 'nf_old_subs_csv_filename' );

// Hook into our new submissions CSV label filter.
function nf_old_subs_csv_label( $label, $field_id ) {
	return apply_filters( 'ninja_forms_export_sub_label', $label, $field_id );
}

add_filter( 'nf_subs_csv_field_label', 'nf_old_subs_csv_label', 10, 2 );

// Hook into our new submissions CSV label array filter.
function nf_old_subs_csv_label_array( $label_array, $sub_ids ) {
	return apply_filters( 'ninja_forms_export_subs_label_array', $label_array, $sub_ids );
}

add_filter( 'nf_subs_csv_label_array', 'nf_old_subs_csv_label_array', 10, 2 );

// Hook into our new submissions CSV pre_value filter.
function nf_old_subs_csv_pre_value( $user_value, $field_id ) {
	return apply_filters( 'ninja_forms_export_sub_pre_value', $user_value, $field_id );
}

add_filter( 'nf_subs_export_pre_value', 'nf_old_subs_csv_pre_value', 10, 2 );

// Hook into our new submissions CSV value filter.
function nf_old_subs_csv_value( $user_value, $field_id ) {
	return apply_filters( 'ninja_forms_export_sub_value', $user_value, $field_id );
}

add_filter( 'nf_subs_csv_field_value', 'nf_old_subs_csv_value', 10, 2 );

// Hook into our new submissions CSV value array filter.
function nf_old_subs_csv_value_array( $values_array, $sub_ids ) {
	return apply_filters( 'ninja_forms_export_subs_value_array', $values_array, $sub_ids );
}

add_filter( 'nf_subs_csv_value_array', 'nf_old_subs_csv_value_array', 10, 2 );

// Hook into our new CSV BOM filter
function nf_old_subs_csv_bom( $bom ) {
	return apply_filters( 'ninja_forms_csv_bom', $bom );
}

add_filter( 'nf_sub_csv_bom', 'nf_old_subs_csv_bom' );

// Hook into our new CSV delimiter filter
function nf_old_subs_csv_delimiter( $delimiter ) {
	return apply_filters( 'ninja_forms_csv_delimiter', $delimiter );
}

add_filter( 'nf_sub_csv_delimiter', 'nf_old_subs_csv_delimiter' );

// Hook into our new CSV enclosure filter
function nf_old_subs_csv_enclosure( $enclosure ) {
	return apply_filters( 'ninja_forms_csv_enclosure', $enclosure );
}

add_filter( 'nf_sub_csv_enclosure', 'nf_old_subs_csv_enclosure' );

// Hook into our new CSV terminator filter
function nf_old_subs_csv_terminator( $terminator ) {
	return apply_filters( 'ninja_forms_csv_terminator', $terminator );
}

add_filter( 'nf_sub_csv_terminator', 'nf_old_subs_csv_terminator' );

// Hook into our new Submissions table row-actions filter
function nf_old_subs_table_row_actions_filter( $actions, $sub_id, $form_id ) {
	return apply_filters( 'ninja_forms_sub_table_row_actions', array(), false, $sub_id, $form_id );
}

add_filter( 'nf_sub_table_row_actions', 'nf_old_subs_table_row_actions_filter', 10, 3 );

/**
 * ninja_forms_get_subs() has been deprecated in favour of Ninja_Forms()->subs()->get( $args ) or Ninja_Forms()->form( 23 )->get_subs( $args )
 * You can also use WordPress queries ,since this is a custom post type.
 * 
 * @since 2.7
 */

function ninja_forms_get_subs( $args = array() ) {

	$plugin_settings = nf_get_settings();

	if ( isset ( $plugin_settings['date_format'] ) ) {
		$date_format = $plugin_settings['date_format'];
	} else {
		$date_format = 'm/d/Y';
	}

	if( is_array( $args ) AND ! empty( $args ) ) {

		$subs_results = array();
		$meta_query = array();
		$date_query = array();

		if( isset( $args['form_id'] ) ) {
			$meta_query[] = array(
				'key' => '_form_id',
				'value' => $args['form_id'],
			);
		}

		if( isset( $args['action'])){
			$meta_query[] = array(
				'key' => '_action',
				'value' => $args['action'],
			);
		}
		
		$query_args = array(
			'post_type' 	=> 'nf_sub',
			'date_query' 	=> $date_query,
			'meta_query' 	=> $meta_query,
			'posts_per_page'	=> -1,
		);

		if( isset( $args['user_id'] ) ) {
			$query_args['author'] = $args['user_id'];
		}



		if( isset( $args['begin_date'] ) AND $args['begin_date'] != '') {
			$query_args['date_query']['after'] = nf_get_begin_date( $args['begin_date'] )->format("Y-m-d G:i:s");
		}

		if( isset( $args['end_date'] ) AND $args['end_date'] != '' ) {
			$query_args['date_query']['before'] = nf_get_end_date( $args['end_date'] )->format("Y-m-d G:i:s");
		}

		$subs = get_posts( $query_args );

		if ( is_array( $subs ) && ! empty( $subs ) ) {
			$x = 0;
			foreach ( $subs as $sub ) {
				$data = array();
				$subs_results[$x]['id'] = $sub->ID;
				$subs_results[$x]['user_id'] = $sub->post_author;
				$subs_results[$x]['form_id'] = get_post_meta( $sub->ID, '_form_id' );
				$subs_results[$x]['action'] = get_post_meta( $sub->ID, '_action' );

				$meta = get_post_custom( $sub->ID );

				foreach ( $meta as $key => $array ) {
					if ( strpos( $key, '_field_' ) !== false ) {
						$field_id = str_replace( '_field_', '', $key );
						$user_value = $array[0];
						$data[] = array( 'field_id' => $field_id, 'user_value' => $user_value );
					}
				}

				$subs_results[$x]['data'] = $data;
				$subs_results[$x]['date_updated'] = $sub->post_modified;

				$x++;
			}
		}

		return $subs_results;
	}
}

/**
 * ninja_forms_get_sub_count() has been deprecated in favour of Ninja_Forms()->form( 23 )->sub_count or nf_get_sub_count()
 * Function that returns a count of the number of submissions.
 *
 * @since 2.7
 */

function ninja_forms_get_sub_count( $args = array() ) {
	return count( ninja_forms_get_subs( $args ) );
}

/**
 * ninja_forms_get_sub_by_id( $sub_id ) has been deprecated in favour of Ninja_Forms()->sub( 23 );
 * 
 * @since 2.7
 */

function ninja_forms_get_sub_by_id( $sub_id ) {
	$sub = Ninja_Forms()->sub( $sub_id );
	if ( $sub ) {
		$sub_row = array();
		$data = array();
		$sub_row['id'] = $sub_id;
		$sub_row['user_id'] = $sub->user_id;
		$sub_row['form_id'] = $sub->form_id;
		$sub_row['action'] = $sub->action;

		if ( $sub->action == 'submit' ) {
			$sub_row['status'] = 1;
		} else {
			$sub_row['status'] = 0;
		}

		$meta = get_post_custom( $sub_id );

		foreach ( $meta as $key => $array ) {
			if ( strpos( $key, '_field_' ) !== false ) {
				$field_id = str_replace( '_field_', '', $key );
				$user_value = is_serialized( $array[0] ) ? unserialize( $array[0] ) : $array[0];
				$data[] = array( 'field_id' => $field_id, 'user_value' => $user_value );
			}
		}

		$sub_row['data'] = $data;
		$sub_row['date_updated'] = $sub->date_submitted;

		return $sub_row;
	} else {
		return false;
	}
}

/**
 * ninja_forms_get_all_subs() has been deprecated in favour of Ninja_Forms()->subs()->get();
 * 
 * @since 2.7
 */

 function ninja_forms_get_all_subs( $form_id = '' ){
	if ( $form_id == '' )
		return false;

	$args = array( 'form_id' => $form_id );
	return ninja_forms_get_subs( $args );
}

/**
 * ninja_forms_insert_sub() has been deprecated in favour of Ninja_Forms()->subs()->create( $form_id );
 * Because submissions are now a CPT, this function will only return false. 
 * Please replace any instances of this function with the replacement.
 * 
 * @since 2.7
 */

function ninja_forms_insert_sub( $args ) {

	if ( ! isset ( $args['form_id'] ) )
		return false;

	$form_id = $args['form_id'];
	
	$sub_id = Ninja_Forms()->subs()->create( $form_id );
	$args['sub_id'] = $sub_id;

	ninja_forms_update_sub( $args );

	return $sub_id;
}

/**
 * ninja_forms_update_sub() has been deprecated in favour of Ninja_Forms()->sub( 23 )->update_field( id, value );
 * Because submissions are now a CPT, this function will only return false. 
 * Please replace any instances of this function with the replacement.
 * 
 * @since 2.7
 */

function ninja_forms_update_sub( $args ){
	if ( ! isset ( $args['sub_id'] ) )
		return false;

	$sub_id = $args['sub_id'];
	$sub = Ninja_Forms()->sub( $sub_id );

	if ( isset ( $args['data'] ) ) {
		$data = $args['data'];
		unset ( $args['data'] );

		if ( is_serialized( $data ) ) {
			$data = unserialize( $data );

			foreach ( $data as $d ) {
				$field_id = $d['field_id'];
				$user_value = $d['user_value'];
				$sub->add_field( $field_id, $user_value );
			}
		}		
	}

	foreach ( $args as $key => $value ) {
		$sub->update_meta( '_' . $key, $value );
	}

}

/**
 * ninja_forms_export_subs_to_csv() has been deprecated in favour of Ninja_Forms()->subs()->export( sub_ids, return );
 * or Ninja_Forms()->sub( 23 )->export( return );
 * Please replace any instances of this function with the replacement.
 * 
 * @since 2.7
 */

function ninja_forms_export_subs_to_csv( $sub_ids = '', $return = false ){
	Ninja_Forms()->subs()->export( $sub_ids, $return );
}

function ninja_forms_implode_r($glue, $pieces){
	$out = '';
	foreach ( $pieces as $piece ) {
		if ( is_array ( $piece ) ) {
			if ( $out == '' ) {
				$out = ninja_forms_implode_r($glue, $piece);
			} else {
				$out .= ninja_forms_implode_r($glue, $piece); // recurse
			}			
		} else {
			if ( $out == '' ) {
				$out .= $piece;
			} else {
				$out .= $glue.$piece;
			}
		}
	}
	return $out;
}


/**
 * Get the csv delimiter
 * 
 * @return string
 */
function ninja_forms_get_csv_delimiter() {
	return apply_filters( 'ninja_forms_csv_delimiter', ',' );
}

/**
 * Get the csv enclosure
 * 
 * @return string
 */
function ninja_forms_get_csv_enclosure() {
	return apply_filters( 'ninja_forms_csv_enclosure', '"' );
}

/**
 * Get the csv delimiter
 * 
 * @return string
 */
function ninja_forms_get_csv_terminator() {
	return apply_filters( 'ninja_forms_csv_terminator', "\n" );
}

/**
 * Wrapper for nf_save_sub()
 */
function ninja_forms_save_sub() {
	nf_save_sub();
}

function nf_change_all_forms_filter( $cap ) {
	return apply_filters( 'ninja_forms_admin_menu_capabilities', $cap );
}

add_filter( 'ninja_forms_admin_all_forms_capabilities', 'nf_change_all_forms_filter' );

function nf_change_admin_menu_filter( $cap ) {
	return apply_filters( 'ninja_forms_admin_menu_capabilities', $cap );
}

add_filter( 'ninja_forms_admin_parent_menu_capabilities', 'nf_change_admin_menu_filter' );

/** 
 * Deprecated as of version 2.8 
 */

// The admin_mailto setting has been deprecated. Because users may have used this setting to modify who receives the admin email,
// we need to make sure that it is backwards compatible.
function nf_clear_admin_mailto() {
	global $ninja_forms_processing;

	$ninja_forms_processing->update_form_setting( 'admin_mailto', array() );
}

add_action( 'ninja_forms_before_pre_process', 'nf_clear_admin_mailto' );

function nf_modify_admin_mailto( $setting, $setting_name, $id ) {
	global $ninja_forms_processing;

	// Bail if this isn't our admin notification
	if ( ! nf_get_object_meta_value( $id, 'admin_email' ) )
		return $setting;

	// Bail if this isn't the "to" setting.
	if ( $setting_name != 'to' )
		return $setting;
	
	$admin_mailto = $ninja_forms_processing->get_form_setting( 'admin_mailto' );
	$ninja_forms_processing->update_form_setting( 'admin_mailto', '' );

	if ( is_array( $admin_mailto ) && ! empty ( $admin_mailto ) ) {
		$setting = array_merge( $setting, $admin_mailto );
	}

	return $setting;
}

add_filter( 'nf_email_notification_process_setting','nf_modify_admin_mailto', 10, 3 );

add_action('init', 'ninja_forms_register_filter_email_add_fields', 15 );
function ninja_forms_register_filter_email_add_fields(){
	global $ninja_forms_processing;

	if( is_object( $ninja_forms_processing ) ){
		if( $ninja_forms_processing->get_form_setting( 'user_email_fields' ) == 1 ){
			add_filter( 'ninja_forms_user_email', 'ninja_forms_filter_email_add_fields' );
		}
	}

	if( is_object( $ninja_forms_processing ) ){
		if( $ninja_forms_processing->get_form_setting( 'admin_email_fields' ) == 1 ){
			add_filter( 'ninja_forms_admin_email', 'ninja_forms_filter_email_add_fields' );
		}
	}
}

function ninja_forms_filter_email_add_fields( $message ){
	global $ninja_forms_processing, $ninja_forms_fields;

	$form_id = $ninja_forms_processing->get_form_ID();
	$all_fields = ninja_forms_get_fields_by_form_id( $form_id );
	//$all_fields = $ninja_forms_processing->get_all_fields();
	$tmp_array = array();
	if( is_array( $all_fields ) ){
		foreach( $all_fields as $field ){
			if( $ninja_forms_processing->get_field_value( $field['id'] ) ){
				$tmp_array[$field['id']] = $ninja_forms_processing->get_field_value( $field['id'] );
			}
		}
	}
	$all_fields = apply_filters( 'ninja_forms_email_all_fields_array', $tmp_array, $form_id );

	$email_type = $ninja_forms_processing->get_form_setting( 'email_type' );
	if(is_array($all_fields) AND !empty($all_fields)){
		if($email_type == 'html'){
			$message .= "<br><br>";
			$message .= apply_filters( 'nf_email_user_values_title', __( 'User Submitted Values:', 'ninja-forms' ) );
			$message .= "<table>";
		}else{
			$message = str_replace("<p>", "\r\n", $message);
			$message = str_replace("</p>", "", $message);
			$message = str_replace("<br>", "\r\n", $message);
			$message = str_replace("<br />", "\r\n", $message);
			$message = strip_tags($message);
			$message .= "\r\n \r\n";
			$message .= apply_filters( 'nf_email_user_values_title', __( 'User Submitted Values:', 'ninja-forms' ) );
			$message .= "\r\n";
		}
		foreach( $all_fields as $field_id => $user_value ){

			$field_row = $ninja_forms_processing->get_field_settings( $field_id );
			$field_label = $field_row['data']['label'];
			$field_label = apply_filters( 'ninja_forms_email_field_label', $field_label, $field_id );
			$user_value = apply_filters( 'ninja_forms_email_user_value', $user_value, $field_id );
			$field_type = $field_row['type'];

			if( $ninja_forms_fields[$field_type]['process_field'] ){
				if( is_array( $user_value ) AND !empty( $user_value ) ){
					$x = 0;
					foreach($user_value as $val){
						if(!is_array($val)){
							if($x > 0){
								$field_label = '----';
								$field_label = apply_filters( 'ninja_forms_email_field_label', $field_label, $field_id );
							}
							if($email_type == 'html'){
								$message .= "<tr><td width='50%'>".$field_label.":</td><td width='50%'>".$val."</td></tr>";
							}else{
								$message .= $field_label." - ".$val."\r\n";
							}
						}else{
							foreach($val as $v){
								if(!is_array($v)){
									if($x > 0){
										$field_label = '----';
										$field_label = apply_filters( 'ninja_forms_email_field_label', $field_label, $field_id );
									}
									if($email_type == 'html'){
										$message .= "<tr><td width='50%'>".$field_label.":</td><td width='50%'>".$v."</td></tr>";
									}else{
										$message .= $field_label." - ".$v."\r\n";
									}
								}else{
									foreach($v as $a){
										if($x > 0){
											$field_label = '----';
											$field_label = apply_filters( 'ninja_forms_email_field_label', $field_label, $field_id );
										}
										if($email_type == 'html'){
											$message .= "<tr><td width='50%'>".$field_label.":</td><td width='50%'>".$a."</td></tr>";
										}else{
											$message .= $field_label." - ".$a."\r\n";
										}
									}
								}
							}
						}
						$x++;
					}
				}else{
					if($email_type == 'html'){
						$message .= "<tr><td width='50%'>".$field_label.":</td><td width='50%'>".$user_value."</td></tr>";
					}else{
						$message .= $field_label." - ".$user_value."\r\n";
					}
				}

			}
		}
		if($email_type == 'html'){
			$message .= "</table>";
		}
	}
	$message = apply_filters( 'ninja_forms_email_field_list', $message, $form_id );

	return $message;
}

add_action( 'init', 'ninja_forms_register_email_admin' );
function ninja_forms_register_email_admin() {
	add_action( 'ninja_forms_post_process', 'ninja_forms_email_admin', 1000 );
}

function ninja_forms_email_admin() {
	global $ninja_forms_processing;

	do_action( 'ninja_forms_email_admin' );

	$form_ID 			= $ninja_forms_processing->get_form_ID();
	$form_title 		= $ninja_forms_processing->get_form_setting( 'form_title' );
	$admin_mailto 		= $ninja_forms_processing->get_form_setting( 'admin_mailto' );
	$email_from_name 	= $ninja_forms_processing->get_form_setting( 'email_from_name' );
	$email_from 		= $ninja_forms_processing->get_form_setting( 'email_from' );
	$email_type 		= $ninja_forms_processing->get_form_setting( 'email_type' );
	$subject 			= $ninja_forms_processing->get_form_setting( 'admin_subject' );
	$message 			= $ninja_forms_processing->get_form_setting( 'admin_email_msg' );
	$email_reply 		= $ninja_forms_processing->get_form_setting( 'admin_email_replyto' );

	if ( $ninja_forms_processing->get_form_setting( 'admin_email_name' ) ){
		$email_from_name = $ninja_forms_processing->get_form_setting( 'admin_email_name' );
	}

	if ( $email_from_name AND $email_reply ) {
		$email_reply = $email_from_name . ' <' . $email_reply . '>';
	}

	if ( !$subject ){
		$subject = $form_title;
	}
	if ( !$message ){
		$message = '';
	}
	if ( !$email_type ){
		$email_type = '';
	}

	if ( $email_type !== 'plain' ){
		$message = apply_filters( 'ninja_forms_admin_email_message_wpautop', wpautop( $message ) );
	}

	$email_from = $email_from_name.' <'.$email_from.'>';

	$email_from = apply_filters( 'ninja_forms_admin_email_from', $email_from, $email_reply, $form_ID );

	$headers = array();
	$headers[] = 'From: ' . $email_from;
	if( $email_reply ) {
		$headers[] = 'Reply-To: ' . $email_reply;
	}
	$headers[] = 'Content-Type: text/' . $email_type;
	$headers[] = 'charset=utf-8';

	$attachments = false;
	if ($ninja_forms_processing->get_form_setting( 'admin_attachments' ) ) {
		$attachments = $ninja_forms_processing->get_form_setting( 'admin_attachments' );
	}

	if ( is_array( $admin_mailto ) AND !empty( $admin_mailto ) ){
		foreach( $admin_mailto as $to ) {
			if ( $attachments ) {
				wp_mail( $to, $subject, $message, $headers, $attachments );
			} else {
				wp_mail( $to, $subject, $message, $headers );
			}
		}
	}

	// Delete our admin CSV if one is present.
	if ( file_exists( $ninja_forms_processing->get_extra_value( '_attachment_csv_path' ) ) ) {
		unlink ( $ninja_forms_processing->get_extra_value( '_attachment_csv_path' ) );
	}
}

add_action('init', 'ninja_forms_register_email_user');
function ninja_forms_register_email_user(){
	add_action( 'ninja_forms_post_process', 'ninja_forms_email_user', 1000 );
}

function ninja_forms_email_user(){
	global $ninja_forms_processing;

	do_action( 'ninja_forms_email_user' );

	$form_ID = $ninja_forms_processing->get_form_ID();
	$form_title = $ninja_forms_processing->get_form_setting('form_title');
	$user_mailto = array();
	$all_fields = $ninja_forms_processing->get_all_fields();
	if(is_array($all_fields) AND !empty($all_fields)){
		foreach($all_fields as $field_id => $user_value){
			$field_row = $ninja_forms_processing->get_field_settings( $field_id );

			if(isset($field_row['data']['send_email'])){
				$send_email = $field_row['data']['send_email'];
			}else{
				$send_email = 0;
			}

			if($send_email){
				array_push($user_mailto, $user_value);
			}
		}
	}

	$email_from      = $ninja_forms_processing->get_form_setting('email_from');
	$email_from_name = $ninja_forms_processing->get_form_setting( 'email_from_name' );
	$email_type      = $ninja_forms_processing->get_form_setting('email_type');
	$subject         = $ninja_forms_processing->get_form_setting('user_subject');
	$message         = $ninja_forms_processing->get_form_setting('user_email_msg');
	$default_email   = get_option( 'admin_email' );

	if(!$subject){
		$subject = $form_title;
	}
	if(!$message){
		$message = __('Thank you for filling out this form.', 'ninja-forms');
	}
	if(!$email_from){
		$email_from = $default_email;
	}
	if(!$email_type){
		$email_type = '';
	}

	if( $email_type !== 'plain' ){
		$message = apply_filters( 'ninja_forms_user_email_message_wpautop', wpautop( $message ) );
	}

	$email_from = $email_from_name.' <'.$email_from.'>';

	$email_from = htmlspecialchars_decode($email_from);
	$email_from = htmlspecialchars_decode($email_from);

	$headers = array();
	$headers[] = 'From: '.$email_from;
	$headers[] = 'Content-Type: text/'.$email_type;
	$headers[] = 'charset=utf-8';

	$attachments = false;
	if ( $ninja_forms_processing->get_form_setting( 'user_attachments' ) ) {
		$attachments = $ninja_forms_processing->get_form_setting('user_attachments');
	}

	if ( is_array( $user_mailto ) AND ! empty( $user_mailto ) ) {
		// check to make sure there's an attachment before attaching one
		if ( $attachments ) {
			wp_mail( $user_mailto, $subject, $message, $headers, $attachments );
		} else {
			wp_mail( $user_mailto, $subject, $message, $headers );
		}

	}
}

add_action( 'nf_save_sub', 'nf_csv_attachment' );

function nf_csv_attachment( $sub_id ){
	global $ninja_forms_processing;

	// make sure this form is supposed to attach a CSV
	if( 1 == $ninja_forms_processing->get_form_setting( 'admin_attach_csv' ) AND 'submit' == $ninja_forms_processing->get_action() ) {
		
		// create CSV content
		$csv_content = Ninja_Forms()->sub( $sub_id )->export( true );
		
		$upload_dir = wp_upload_dir();
		$path = trailingslashit( $upload_dir['path'] );

		// create temporary file
		$path = tempnam( $path, 'Sub' );
		$temp_file = fopen( $path, 'r+' );
		
		// write to temp file
		fwrite( $temp_file, $csv_content );
		fclose( $temp_file );
		
		// find the directory we will be using for the final file
		$path = pathinfo( $path );
		$dir = $path['dirname'];
		$basename = $path['basename'];
		
		// create name for file
		$new_name = apply_filters( 'ninja_forms_submission_csv_name', 'ninja-forms-submission' );
		
		// remove a file if it already exists
		if( file_exists( $dir.'/'.$new_name.'.csv' ) ) {
			unlink( $dir.'/'.$new_name.'.csv' );
		}
		
		// move file
		rename( $dir.'/'.$basename, $dir.'/'.$new_name.'.csv' );
		$file1 = $dir.'/'.$new_name.'.csv';
		
		// add new file to array of existing files
		$files = $ninja_forms_processing->get_form_setting( 'admin_attachments' );
		array_push( $files, $file1 );
		$ninja_forms_processing->update_form_setting( 'admin_attachments', $files );
		$ninja_forms_processing->update_extra_value( '_attachment_csv_path', $file1 );
	}
}

// Move any attachments that exist for our "admin" and "user" emails.
function nf_modify_attachments( $files, $n_id ) {
	global $ninja_forms_processing;

	if ( Ninja_Forms()->notification( $n_id )->get_setting( 'admin_email' ) ) {
		if ( is_array( $ninja_forms_processing->get_form_setting( 'admin_attachments' ) ) ) {
			$files = array_merge( $files, $ninja_forms_processing->get_form_setting( 'admin_attachments' ) );
		}
	} else if ( Ninja_Forms()->notification( $n_id )->get_setting( 'user_email' ) ) {
		if ( is_array( $ninja_forms_processing->get_form_setting( 'user_attachments' ) ) ) {
			$files = array_merge( $files, $ninja_forms_processing->get_form_setting( 'user_attachments' ) );
		}
	}

	$ninja_forms_processing->update_form_setting( 'admin_attachments', '' );
	
	return $files;
}

add_filter( 'nf_email_notification_attachments', 'nf_modify_attachments', 10, 2 );

// Deprecate old "add all fields" filters
function nf_deprecate_all_fields_email_field_label( $value, $field_id ) {
	return apply_filters( 'ninja_forms_email_field_label', $value, $field_id );
}

add_filter( 'nf_all_fields_field_label', 'nf_deprecate_all_fields_email_field_label', 10, 2 );

function nf_deprecate_all_fields_email_field_value( $value, $field_id ) {
	return apply_filters( 'ninja_forms_email_user_value', $value, $field_id );
}

add_filter( 'nf_all_fields_field_value', 'nf_deprecate_all_fields_email_field_value', 10, 2 );

function nf_deprecate_all_fields_email_table( $value, $form_id ) {
	return apply_filters( 'ninja_forms_email_field_list', $value, $form_id );
}

add_filter( 'nf_all_fields_table', 'nf_deprecate_all_fields_email_table', 10, 2 );

// Deprecate our old success message filter
function nf_deprecate_success_message_filter( $message, $n_id ) {
	return apply_filters( 'ninja_forms_success_msg', $message );
}

add_filter( 'nf_success_msg', 'nf_deprecate_success_message_filter', 10, 2 );

// Remove any references to "admin email" from our imported forms.
function nf_deprecate_form_import( $form ) {
	if ( isset ( $form['data']['admin_mailto'] ) )
		unset( $form['data']['admin_mailto'] );
	
	if ( isset ( $form['data']['admin_email'] ) )
		unset( $form['data']['admin_email'] );
	
	if ( isset ( $form['data']['admin_subject'] ) )
		unset( $form['data']['admin_subject'] );

	if ( isset ( $form['data']['user_mailto'] ) )
		unset( $form['data']['user_mailto'] );

	if ( isset ( $form['data']['user_email'] ) )
		unset( $form['data']['user_email'] );

	if ( isset ( $form['data']['user_subject'] ) )
		unset ( $form['data']['user_subject'] );
	
	if ( isset ( $form['data']['landing_page'] ) )
		unset ( $form['data']['landing_page'] );

	return $form;
}

add_filter( 'ninja_forms_before_import_form', 'nf_deprecate_form_import' );

// Remove any references to "user email" from our imported forms.
function nf_deprecate_field_import( $data ) {
	if ( isset ( $data['send_email'] ) )
		unset ( $data['send_email'] );

	if ( isset ( $data['from_email'] ) )
		unset ( $data['from_email'] );

	if ( isset ( $data['replyto_email'] ) )
		unset ( $data['replyto_email'] );

	return $data;
}

add_filter( 'nf_before_import_field', 'nf_deprecate_field_import' );


/** 
 * Deprecated as of version 2.9
 *
 */


/**
 * Get an array of form settings by form ID
 *
 * @since 2.7
 * @param int $form_id
 * @return array $form['data']
 */
function nf_get_form_settings( $form_id ) {
	return nf_get_object_meta( $form_id );
}

/**
 * Return form data
 * 
 * @since 1.0
 * @param int $form_id
 * @return array $form
 */
function ninja_forms_get_form_by_id( $form_id ) {
	$settings = Ninja_Forms()->form( $form_id )->get_all_settings();
	$date_updated = Ninja_Forms()->form( $form_id )->get_setting( 'date_updated' );
	return array( 'id' => $form_id, 'data' => $settings, 'date_updated' => $date_updated );
}

/**
 * Get a form by field id
 * 
 * @since 1.0
 * @param int $field_id
 * @param array $form
 */
function ninja_forms_get_form_by_field_id( $field_id ){
	global $wpdb;
	$form_id = $wpdb->get_row($wpdb->prepare("SELECT form_id FROM ".NINJA_FORMS_FIELDS_TABLE_NAME." WHERE id = %d", $field_id), ARRAY_A);
	$form_id = $form_id['form_id'];
	$form = ninja_forms_get_form_by_id( $form_id );
	return $form;
}

/**
 * Delete a form
 *
 * @since 1.0
 */
function ninja_forms_delete_form( $form_id = '' ){
	global $wpdb;

	// Bail if we aren't in the admin
	if ( ! is_admin() )
		return false;

	// Bail if we don't have proper permissions
	if ( ! current_user_can( apply_filters( 'nf_delete_form_capabilities', 'manage_options' ) ) )
		return false;

	if( $form_id == '' ){
		$ajax = true;
		$form_id = absint( $_REQUEST['form_id'] );
		check_ajax_referer( 'nf_ajax', 'nf_ajax_nonce' );
	}else{
		$ajax = false;
	}

	Ninja_Forms()->form( $form_id )->delete();

	if( $ajax ){
		die();
	}
}

add_action('wp_ajax_ninja_forms_delete_form', 'ninja_forms_delete_form');

function ninja_forms_get_all_forms( $debug = false ){
	$forms = Ninja_Forms()->forms()->get_all();

	$tmp_array = array();
	$x = 0;
	foreach ( $forms as $form_id ) {
		$tmp_array[ $x ]['id'] = $form_id;
		$tmp_array[ $x ]['data'] = Ninja_Forms()->form( $form_id )->get_all_settings();
		$tmp_array[ $x ]['name'] = Ninja_Forms()->form( $form_id )->get_setting( 'form_title' );
		$x++;
	}

	return $tmp_array;
}

/**
 * Return our form count
 *
 * @since 2.8
 * @return int $count
 */

function nf_get_form_count() {
	global $wpdb;

	$forms = Ninja_Forms()->forms()->get_all();
	return count( $forms );
}

/**
 * Old update form function.
 * 
 * @since 1.0
 * @return void
 */
function ninja_forms_update_form( $args ){
	// Get our form id
	$form_id = $args['where']['id'];
	$update_array = $args['update_array'];
	if ( isset ( $update_array['data'] ) ) {
		$data = maybe_unserialize( $update_array['data'] );
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $val ) {
				Ninja_Forms()->form( $form_id )->update_setting( $key, $val );
			}	
		}
		unset( $update_array['data'] );	
	}

	foreach ( $update_array as $key => $val ) {
		Ninja_Forms()->form( $form_id )->update_setting( $key, $val );
	}

	Ninja_Forms()->form( $form_id )->dump_cache();
	
}

// Add our old form fields
require_once( NINJA_FORMS_DIR . "/includes/fields/honeypot.php" );