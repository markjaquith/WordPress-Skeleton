<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_post_process(){
	global $wpdb, $ninja_forms_processing;

	$ajax = $ninja_forms_processing->get_form_setting('ajax');
	$form_id = $ninja_forms_processing->get_form_ID();
	$json = ninja_forms_json_response();
	
	if(!$ninja_forms_processing->get_all_errors()){

		do_action('ninja_forms_post_process');
		$json = ninja_forms_json_response();

		if( !$ninja_forms_processing->get_all_errors() ){

			$ninja_forms_processing->update_form_setting( 'processing_complete', 1 );

			
			if($ajax == 1){
				//header('Content-Type', 'application/json');
				echo $json;
				die();
			}else{

				if( $ninja_forms_processing->get_form_setting( 'landing_page' ) != '' ){
					ninja_forms_set_transient();

                    $url = str_replace( '&amp;', '&', $ninja_forms_processing->get_form_setting( 'landing_page' ) );

					wp_redirect( $url );
					die();
				}
			}
		}else{
			if($ajax == 1){
				//header('Content-Type', 'application/json');
				echo $json;
				die();
			}else{
				//echo 'post-processing';
				//print_r($ninja_forms_processing->get_all_errors());
			}
		}
	}else{
		if($ajax == 1){
			//header('Content-Type', 'application/json');
			echo $json;
			die();
		}else{
			//echo 'post-processing';
			//print_r($ninja_forms_processing->get_all_errors());
		}
	}
}
