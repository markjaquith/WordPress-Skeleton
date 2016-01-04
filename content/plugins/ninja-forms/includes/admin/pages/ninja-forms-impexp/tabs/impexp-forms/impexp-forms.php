<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_tab_impexp_forms');

function ninja_forms_register_tab_impexp_forms(){
	$args = array(
		'name' => __( 'Forms', 'ninja-forms' ),
		'page' => 'ninja-forms-impexp',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_impexp_forms',
		'show_save' => false,
	);
	ninja_forms_register_tab('impexp_forms', $args);

}

add_action('init', 'ninja_forms_register_imp_forms_metabox');
function ninja_forms_register_imp_forms_metabox(){
	$args = array(
		'page' => 'ninja-forms-impexp',
		'tab' => 'impexp_forms',
		'slug' => 'imp_form',
		'title' => __( 'Import a form', 'ninja-forms' ),
		'settings' => array(
			array(
				'name' => 'userfile',
				'type' => 'file',
				'label' => __( 'Select a file', 'ninja-forms' ),
				'desc' => '',
				'max_file_size' => 5000000,
				'help_text' => '',
			),
			array(
				'name' => 'submit',
				'type' => 'submit',
				'label' => __( 'Import Form', 'ninja-forms' ),
				'class' => 'button-secondary',
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

function ninja_forms_register_exp_forms_metabox(){
	if ( ! isset ( $_REQUEST['page'] ) || 'ninja-forms-impexp' != $_REQUEST['page'] )
		return false;
	
	$form_results = ninja_forms_get_all_forms();
	$form_select = array();
	if(is_array($form_results) AND !empty($form_results)){
		foreach($form_results as $form){
			if( isset( $form['data'] ) ){
				$data = $form['data'];
				$form_title = $data['form_title'];
				array_push($form_select, array('name' => $form_title, 'value' => $form['id']));
			}
		}
	}
	$args = array(
		'page' => 'ninja-forms-impexp',
		'tab' => 'impexp_forms',
		'slug' => 'exp_form',
		'title' => __('Export a form', 'ninja-forms'),
		'settings' => array(
			array(
				'name' => 'form_id',
				'type' => 'select',
				'label' => __('Select a form', 'ninja-forms'),
				'desc' => '',
				'options' => $form_select,
				'help_text' => '',
			),
			array(
				'name' => 'submit',
				'type' => 'submit',
				'label' => __('Export Form', 'ninja-forms'),
				'class' => 'button-secondary',
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

add_action('admin_init', 'ninja_forms_register_exp_forms_metabox');

/*
 *
 * Function that returns a serialized string containing the form for export.
 *
 * @since 2.2.42
 * @returns $form_row string
 */

function ninja_forms_serialize_form( $form_id ){
	if ( $form_id == '' )
		return;

	$plugin_settings = nf_get_settings();
	$form_row = array();
	$form_row['data'] = Ninja_Forms()->form( $form_id )->get_all_settings();
	$field_results = ninja_forms_get_fields_by_form_id( $form_id );
	$form_row['id'] = NULL;
	if ( is_array ( $form_row ) AND ! empty ( $form_row ) ) {
		if ( is_array( $field_results ) AND ! empty( $field_results ) ) {
			$x = 0;
			foreach( $field_results as $field ) {
				$form_row['field'][$x] = $field;
				$x++;
			}
		}
	}

	// Get all of our notifications for this form
	$notifications = nf_get_notifications_by_form_id( $form_id );
	$form_row['notifications'] = $notifications;

	$form_row = apply_filters( 'nf_export_form_row', $form_row );

	$form_row = serialize($form_row);

	return $form_row;
}


function ninja_forms_export_form( $form_id ){
	if($form_id == '')
		return;
	$plugin_settings = nf_get_settings();
	$form_title = Ninja_Forms()->form( $form_id )->get_setting( 'form_title' );
	$form_row = ninja_forms_serialize_form( $form_id );
	$form_title = preg_replace('/[^a-zA-Z0-9-]/', '', $form_title);
	$form_title = str_replace (" ", "-", $form_title);

	if(isset($plugin_settings['date_format'])){
		$date_format = $plugin_settings['date_format'];
	}else{
		$date_format = 'm/d/Y';
	}

	//$today = date($date_format);
	$current_time = current_time('timestamp');
	$today = date($date_format, $current_time);

	header("Content-type: application/csv");
	header('Content-Disposition: attachment; filename="'.$form_title.'"-"'.$today.'".nff"');
	header("Pragma: no-cache");
	header("Expires: 0");
	echo $form_row;
	die();
}

function ninja_forms_save_impexp_forms($data){
	global $wpdb, $ninja_forms_admin_update_message;
	$plugin_settings = nf_get_settings();
	$form_id = isset( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : '';
	$update_msg = '';
	if( $_REQUEST['submit'] == __('Export Form', 'ninja-forms') OR ( isset( $_REQUEST['export_form'] ) AND absint( $_REQUEST['export_form'] ) == 1 ) ){
		if($form_id != ''){
			ninja_forms_export_form( $form_id );
		}else{
			$ninja_forms_admin_update_message = __( 'Please select a form.', 'ninja-forms' );
		}
	}elseif($_REQUEST['submit'] == __('Import Form', 'ninja-forms')){
		if ($_FILES['userfile']['error'] == UPLOAD_ERR_OK AND is_uploaded_file($_FILES['userfile']['tmp_name'])){
			$file = file_get_contents($_FILES['userfile']['tmp_name']);
			ninja_forms_import_form( $file );
			$update_msg = __( 'Form Imported Successfully.', 'ninja-forms' );
		}else{
			//echo $_FILES['userfile']['error'];
			$update_msg = __( 'Please select a valid exported form file.', 'ninja-forms' );
		}
	}
	return $update_msg;
}


/*
 *
 * Function that fixes calculation fields and their references to newly created fields.
 *
 * @since 2.2.40
 * @returns void
 */

add_action( 'ninja_forms_after_import_form', 'ninja_forms_calc_after_import_form' );

function ninja_forms_calc_after_import_form( $form ){
	global $wpdb;

	if( is_array( $form['field'] ) AND !empty( $form['field'] ) ){
		$field_rows = ninja_forms_get_fields_by_form_id( $form['id'] );
		if( is_array( $field_rows ) AND !empty( $field_rows ) ){
			for ($y=0; $y < count( $field_rows ); $y++) {
				if ( isset ( $field_rows[$y]['data']['calc'] ) AND is_array( $field_rows[$y]['data']['calc'] ) ) {
					for ( $i=0; $i < count( $field_rows[$y]['data']['calc']); $i++ ) {
						foreach( $form['field'] as $inserted_field ){
							if ( isset ( $field_rows[$y]['data']['calc'][$i]['field'] ) AND $inserted_field['old_id'] == $field_rows[$y]['data']['calc'][$i]['field'] ) {
								$field_rows[$y]['data']['calc'][$i]['field'] = $inserted_field['id'];
							}
						}
					}
				}

				if ( isset ( $field_rows[$y]['data']['calc_eq'] ) AND $field_rows[$y]['data']['calc_eq'] != '' ) {
					$calc_eq = $field_rows[$y]['data']['calc_eq'];
					foreach( $form['field'] as $inserted_field ){
						$calc_eq = str_replace( 'field_'.$inserted_field['old_id'], 'field_'.$inserted_field['id'], $calc_eq );
					}
					$field_rows[$y]['data']['calc_eq'] = $calc_eq;
				}				

				$field_rows[$y]['data'] = serialize( $field_rows[$y]['data'] );
				$args = array(
					'update_array' => array(
						'data' => $field_rows[$y]['data'],
						),
					'where' => array(
						'id' => $field_rows[$y]['id'],
						),
				);
				ninja_forms_update_field($args);
			}

			if ( isset ( $form['data']['success_msg'] ) AND $form['data']['success_msg'] != '' ) {
				$success_msg = $form['data']['success_msg'];
				foreach( $form['field'] as $inserted_field ){
					$success_msg = str_replace( '[ninja_forms_field id='.$inserted_field['old_id'].']', '[ninja_forms_field id='.$inserted_field['id'].']', $success_msg );
				}
				$form['data']['success_msg'] = $success_msg;
			}

			if ( isset ( $form['data']['user_subject'] ) AND $form['data']['user_subject'] != '' ) {
				$user_subject = $form['data']['user_subject'];
				foreach( $form['field'] as $inserted_field ){
					$user_subject = str_replace( '[ninja_forms_field id='.$inserted_field['old_id'].']', '[ninja_forms_field id='.$inserted_field['id'].']', $user_subject );
				}
				$form['data']['user_subject'] = $user_subject;
			}			

			if ( isset ( $form['data']['user_email_msg'] ) AND $form['data']['user_email_msg'] != '' ) {
				$user_email_msg = $form['data']['user_email_msg'];
				foreach( $form['field'] as $inserted_field ){
					$user_email_msg = str_replace( '[ninja_forms_field id='.$inserted_field['old_id'].']', '[ninja_forms_field id='.$inserted_field['id'].']', $user_email_msg );
				}
				$form['data']['user_email_msg'] = $user_email_msg;
			}

			if ( isset ( $form['data']['admin_subject'] ) AND $form['data']['admin_subject'] != '' ) {
				$admin_subject = $form['data']['admin_subject'];
				foreach( $form['field'] as $inserted_field ){
					$admin_subject = str_replace( '[ninja_forms_field id='.$inserted_field['old_id'].']', '[ninja_forms_field id='.$inserted_field['id'].']', $admin_subject );
				}
				$form['data']['admin_subject'] = $admin_subject;
			}			

			if ( isset ( $form['data']['admin_email_msg'] ) AND $form['data']['admin_email_msg'] != '' ) {
				$admin_email_msg = $form['data']['admin_email_msg'];
				foreach( $form['field'] as $inserted_field ){
					$admin_email_msg = str_replace( '[ninja_forms_field id='.$inserted_field['old_id'].']', '[ninja_forms_field id='.$inserted_field['id'].']', $admin_email_msg );
				}
				$form['data']['admin_email_msg'] = $admin_email_msg;
			}

			if ( isset ( $form['data']['mp_confirm_msg'] ) AND $form['data']['mp_confirm_msg'] != '' ) {
				$mp_confirm_msg = $form['data']['mp_confirm_msg'];
				foreach( $form['field'] as $inserted_field ){
					$mp_confirm_msg = str_replace( '[ninja_forms_field id='.$inserted_field['old_id'].']', '[ninja_forms_field id='.$inserted_field['id'].']', $mp_confirm_msg );
				}
				$form['data']['mp_confirm_msg'] = $mp_confirm_msg;
			}

			if ( isset ( $form['data']['save_msg'] ) AND $form['data']['save_msg'] != '' ) {
				$save_msg = $form['data']['save_msg'];
				foreach( $form['field'] as $inserted_field ){
					$save_msg = str_replace( '[ninja_forms_field id='.$inserted_field['old_id'].']', '[ninja_forms_field id='.$inserted_field['id'].']', $save_msg );
				}
				$form['data']['save_msg'] = $save_msg;
			}

			if ( isset ( $form['data']['save_email_msg'] ) AND $form['data']['save_email_msg'] != '' ) {
				$save_email_msg = $form['data']['save_email_msg'];
				foreach( $form['field'] as $inserted_field ){
					$save_email_msg = str_replace( '[ninja_forms_field id='.$inserted_field['old_id'].']', '[ninja_forms_field id='.$inserted_field['id'].']', $save_email_msg );
				}
				$form['data']['save_email_msg'] = $save_email_msg;
			}

			$args = array(
				'update_array' => array(
					'data' => serialize( $form['data'] ),
					),
				'where' => array(
					'id' => $form['id'],
					),
			);
			ninja_forms_update_form($args);
		}
	}
}