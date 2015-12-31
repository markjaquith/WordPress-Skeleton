<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Function that resets the field values to default if the form has been submitted.
 *
 * @since 2.5
 * @return void
 */

function nf_clear_complete( $form_id ) {
	global $ninja_forms_processing, $current_user, $post;

	if ( ! isset ( $ninja_forms_processing ) or $ninja_forms_processing->get_form_setting( 'clear_complete' ) == 0 or $ninja_forms_processing->get_form_setting( 'processing_complete' ) != 1 )
		return false;

	$all_fields = $ninja_forms_processing->get_all_fields();
	foreach ( $all_fields as $field_id => $user_value ) {
		$default_value = $ninja_forms_processing->get_field_setting( $field_id, 'default_value' );

		get_currentuserinfo();
		$user_ID 			= $current_user->ID;
		if ( $user_ID and !empty( $user_ID ) ) {
			$user_firstname 	= $current_user->user_firstname;
		    $user_lastname 		= $current_user->user_lastname;
		    $user_display_name 	= $current_user->display_name;
		    $user_email 		= $current_user->user_email;
		} else {
			$user_ID 			= '';
			$user_firstname 	= '';
		    $user_lastname 		= '';
		    $user_display_name 	= '';
		    $user_email 		= '';
		}


	    if ( is_object ( $post ) ) {
		    $post_ID 			= $post->ID;
		    $post_title 		= $post->post_title;
		    $post_url			= get_permalink( $post_ID );
	    } else {
	    	$post_ID      		= '';
	    	$post_title 		= '';
	    	$post_url 			= '';
	    }

	    switch( $default_value ){
			case '_user_id':
				$default_value = $user_ID;
				break;
			case '_user_firstname':
				$default_value = $user_firstname;
				break;
			case '_user_lastname':
				$default_value = $user_lastname;
				break;
			case '_user_display_name':
				$default_value = $user_display_name;
				break;
			case '_user_email':
				$default_value = $user_email;
				break;
			case 'post_id':
				$default_value = $post_ID;
				break;
			case 'post_title':
				$default_value = $post_title;
				break;
			case 'post_url':
				$default_value = $post_url;
				break;
			case 'today':
				$plugin_settings = nf_get_settings();
				if ( isset ( $plugin_settings['date_format'] ) ) {
					$date_format = $plugin_settings['date_format'];
				} else {
					$date_format = 'm/d/Y';
				}
				$default_value = date( $date_format, current_time( 'timestamp' ) );
				break;
		}



		$ninja_forms_processing->update_field_value( $field_id, $default_value );
	}
}

add_action( 'ninja_forms_display_init', 'nf_clear_complete', 999 );