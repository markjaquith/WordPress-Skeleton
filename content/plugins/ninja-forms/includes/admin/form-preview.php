<?php if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'ninja_forms_preview_form' );
function ninja_forms_preview_form() {
	global $ninja_forms_append_page_form_id;
	if( ! empty ( $_REQUEST['form_id'] ) AND ! empty ( $_REQUEST['preview'] ) ) { //I
		$form_id = absint( $_REQUEST['form_id'] );
		$ninja_forms_append_page_form_id = array($form_id);
		add_filter( 'the_content', 'ninja_forms_append_to_page', 9999 );		
	}
}

function ninja_forms_preview_link( $form_id = '', $echo = true ) {
	if( $form_id == '' ){
		if( isset( $_REQUEST['form_id'] ) ){
			$form_id = absint( $_REQUEST['form_id'] );
		}else{
			$form_id = '';
		}
	}
	$base = home_url();

	$form_data = ninja_forms_get_form_by_id( $form_id );

	$append_page = Ninja_Forms()->form( $form_id )->get_setting( 'append_page' );

	if ( empty( $append_page ) ) {
		$opt =  nf_get_settings();
		if ( isset ( $opt['preview_id'] ) ) {
			$page_id = $opt['preview_id'];
		} else {
			$page_id = '';
		}
	} else {
		$page_id = $append_page;
	}

	if( $echo ){
		$preview_link = '<a target="_blank" href="' . $base . '/?page_id=' . $page_id . '&preview=true&form_id=' . $form_id . '">' . __( 'Preview Form', 'ninja-forms' ) . '</a>';
	}else{
		$preview_link = $base . '/?page_id=' . $page_id . '&preview=true&form_id=' . $form_id;
	}

	return $preview_link;

}