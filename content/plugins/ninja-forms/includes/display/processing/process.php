<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_process(){
	global $wpdb, $ninja_forms_fields, $ninja_forms_processing;

	$ajax = $ninja_forms_processing->get_form_setting('ajax');
	$form_id = $ninja_forms_processing->get_form_ID();

	if(!$ninja_forms_processing->get_all_errors()){
		do_action('ninja_forms_process');
		ninja_forms_post_process();
	}else{
		if($ajax == 1){
			$json = ninja_forms_json_response();
			//header('Content-Type', 'application/json');
			echo $json;
			die();
		}else{
			//echo 'processing';
			//print_r($ninja_forms_processing->get_all_errors());
		}
	}
}