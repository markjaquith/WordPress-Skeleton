<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Outputs the HTML for the ajax processing div. Only used when submitting via ajax.
 *
**/

function ninja_forms_display_process_message($form_id){
	global $ninja_forms_processing_error, $ninja_forms_processing_response;
	$plugin_settings = nf_get_settings();
	$form_row = ninja_forms_get_form_by_id($form_id);
	$process_label = __( $plugin_settings['process_label'], 'ninja-forms' );

	if( isset( $form_row['data']['ajax'] ) ){
		$ajax = $form_row['data']['ajax'];
	}else{
		$ajax = 0;
	}

	if($ajax == 1){
	?>
	<div id="ninja_forms_form_<?php echo $form_id;?>_process_msg" style="display:none;" class="ninja-forms-processing">
		<?php echo $process_label;?> <img src="<?php echo NINJA_FORMS_URL;?>/images/loading.gif" alt="loading">
	</div>
	<?php
	}
}

add_action( 'ninja_forms_display_before_form_wrap', 'ninja_forms_display_process_message' );