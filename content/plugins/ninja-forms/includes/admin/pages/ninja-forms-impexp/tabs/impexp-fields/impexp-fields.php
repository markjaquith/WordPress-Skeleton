<?php if ( ! defined( 'ABSPATH' ) ) exit;

add_action('init', 'ninja_forms_register_tab_impexp_fields');

function ninja_forms_register_tab_impexp_fields(){
	$args = array(
		'name' => __( 'Favorite Fields', 'ninja-forms' ),
		'page' => 'ninja-forms-impexp',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_impexp_fields',
		'show_save' => false,
	);
	ninja_forms_register_tab('impexp_fields', $args);

}

add_action( 'init', 'ninja_forms_register_imp_fav_fields_metabox' );
function ninja_forms_register_imp_fav_fields_metabox(){
	$args = array(
		'page' => 'ninja-forms-impexp',
		'tab' => 'impexp_fields',
		'slug' => 'imp_fields',
		'title' => __( 'Import Favorite Fields', 'ninja-forms' ),
		'settings' => array(
			array(
				'name' => 'userfile',
				'type' => 'file',
				'label' => __( 'Select a file', 'ninja-forms' ),
				'desc' => '',
				'max_file_size' => 30000,
				'help_text' => '',
			),
			array(
				'name' => 'submit',
				'type' => 'submit',
				'label' => __( 'Import Favorites', 'ninja-forms' ),
				'class' => 'button-secondary',
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

add_action( 'admin_init', 'ninja_forms_register_exp_fav_fields_metabox' );
function ninja_forms_register_exp_fav_fields_metabox(){
	$fav_results = ninja_forms_get_all_favs();
	$fav_options = array();

	if ( is_array( $fav_results ) AND !empty( $fav_results ) ) {
		foreach ( $fav_results as $fav ) {
			$data = $fav['data'];
			$label = $data['label'];
			array_push($fav_options, array('name' => $label, 'value' => $fav['id']));
		}
		$empty = '';
	} else {
		$empty = __( 'No Favorite Fields Found', 'ninja-forms' );
	}
	$args = array(
		'page' => 'ninja-forms-impexp',
		'tab' => 'impexp_fields',
		'slug' => 'exp_fields',
		'title' => __( 'Export Favorite Fields', 'ninja-forms' ),
		'settings' => array(
			array(
				'name' => 'ninja_forms_fav',
				'type' => 'checkbox_list',
				'label' => '',
				'desc' => '',
				'options' => $fav_options,
				'help_text' => '',
			),
			array(
				'name' => '',
				'type' => 'desc',
				'label' => $empty,
			),
			array(
				'name' => 'submit',
				'type' => 'submit',
				'label' => __( 'Export Fields', 'ninja-forms' ),
				'class' => 'button-secondary',
			),
		),
	);
	ninja_forms_register_tab_metabox($args);
}

function ninja_forms_save_impexp_fields( $data ){
	global $wpdb, $ninja_forms_admin_update_message;
	$plugin_settings = nf_get_settings();
	$update_message = '';
	if($_POST['submit'] == __( 'Export Fields', 'ninja-forms' ) ){
		if(isset($_POST['ninja_forms_fav']) AND !empty($_POST['ninja_forms_fav'])){
			$fav_ids = ninja_forms_esc_html_deep( $_POST['ninja_forms_fav'] );

			if(isset($plugin_settings['date_format'])){
				$date_format = $plugin_settings['date_format'];
			}else{
				$date_format = 'm/d/Y';
			}

			//$today = date($date_format);
			$current_time = current_time( 'timestamp' );
			$today = date( $date_format, $current_time );

			$favorites = array();


			if( is_array( $fav_ids ) AND !empty( $fav_ids ) ){
				$x = 0;
				foreach( $fav_ids as $fav_id ){
					$fav_row = ninja_forms_get_fav_by_id( $fav_id );
					$fav_row['id'] = NULL;
					$favorites[$x] = $fav_row;
					$x++;
				}
			}

			$favorites = serialize($favorites);

			header("Content-type: application/csv");
			header("Content-Disposition: attachment; filename=favorites-".$today.".nff");
			header("Pragma: no-cache");
			header("Expires: 0");

			echo $favorites;
			die();
		}else{
			$update_message = __( 'Please select favorite fields to export.', 'ninja-forms' );
		}
	}elseif( $_POST['submit'] == __( 'Import Favorites', 'ninja-forms' ) ){
		
		if( $_FILES['userfile']['error'] == UPLOAD_ERR_OK AND is_uploaded_file( $_FILES['userfile']['tmp_name'] ) ){
			
			$file = file_get_contents($_FILES['userfile']['tmp_name']);
			$favorites = unserialize($file);
			if(is_array($favorites)){
				foreach($favorites as $fav){
					$fav['data'] = serialize( $fav['data'] );
					$wpdb->insert(NINJA_FORMS_FAV_FIELDS_TABLE_NAME, $fav);
				}
			}
			$update_message = __( 'Favorites imported successfully.', 'ninja-forms' );
		}else{
			$update_message = __( 'Please select a valid favorite fields file.', 'ninja-forms' );
		}
	}

	return $update_message;
}