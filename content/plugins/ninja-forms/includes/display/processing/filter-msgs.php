<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_filter_msgs');
function ninja_forms_register_filter_msgs(){
	add_action( 'ninja_forms_post_process', 'ninja_forms_filter_msgs' );
}

function ninja_forms_filter_msgs(){
	global $ninja_forms_processing;

	//Get the form settings for the form currently being processed.
	$admin_subject = $ninja_forms_processing->get_form_setting( 'admin_subject' );
	$user_subject = $ninja_forms_processing->get_form_setting( 'user_subject' );
	$success_msg = $ninja_forms_processing->get_form_setting( 'success_msg' );
	$admin_email_msg = $ninja_forms_processing->get_form_setting( 'admin_email_msg' );
	$user_email_msg = $ninja_forms_processing->get_form_setting( 'user_email_msg' );
	$save_msg = $ninja_forms_processing->get_form_setting( 'save_msg' );

	//Apply the wpautop to our fields if the email type is set to HTML
	//$success_msg = wpautop( $success_msg );
	$save_msg = wpautop( $save_msg );
	if( $ninja_forms_processing->get_form_setting( 'email_type' ) == 'html' ){
		$admin_email_msg = wpautop( $admin_email_msg );
		$user_email_msg = wpautop( $user_email_msg );
	}

	//Apply shortcodes to each of our message fields.
	$admin_subject = do_shortcode( $admin_subject );
	$user_subject = do_shortcode( $user_subject );
	//$success_msg = do_shortcode( $success_msg );
	$admin_email_msg = do_shortcode( $admin_email_msg );
	$user_email_msg = do_shortcode( $user_email_msg );
	$save_msg = do_shortcode( $save_msg );

	//Call any functions which may be attached to the filter for our message fields
	$ninja_forms_processing->update_form_setting('admin_subject', apply_filters('ninja_forms_admin_subject', $admin_subject));
	$ninja_forms_processing->update_form_setting('user_subject', apply_filters('ninja_forms_user_subject', $user_subject));
	//$ninja_forms_processing->update_form_setting('success_msg', apply_filters('ninja_forms_success_msg', $success_msg));
	$ninja_forms_processing->update_form_setting('admin_email_msg', apply_filters('ninja_forms_admin_email', $admin_email_msg));
	$ninja_forms_processing->update_form_setting('user_email_msg', apply_filters('ninja_forms_user_email', $user_email_msg));
	$ninja_forms_processing->update_form_setting('save_msg', apply_filters('ninja_forms_save_msg', $save_msg));
}