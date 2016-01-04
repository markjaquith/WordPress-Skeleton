<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Outputs the HTML of the form wrap.
 *
**/
add_action('init', 'ninja_forms_register_display_open_form_wrap');
function ninja_forms_register_display_open_form_wrap(){
	add_action('ninja_forms_display_open_form_wrap', 'ninja_forms_display_open_form_wrap');
}

function ninja_forms_display_open_form_wrap($form_id){
	global $ninja_forms_processing;
	//Check to see if the form_id has been sent.
	if($form_id == ''){
		if(isset($_REQUEST['form_id'])){ //If it hasn't, set it to our requested form_id. Sometimes this function can be called without an expressly passed form_id.
			$form_id = absint( $_REQUEST['form_id'] );
		}
	}

	$wrap_class = '';

	$wrap_class = apply_filters( 'ninja_forms_form_wrap_class', $wrap_class, $form_id );

	?>
	<div id="ninja_forms_form_<?php echo $form_id;?>_wrap" class="ninja-forms-form-wrap<?php echo $wrap_class; ?>">
	<?php
}

add_action('ninja_forms_display_close_form_wrap', 'ninja_forms_display_close_form_wrap');
function ninja_forms_display_close_form_wrap($form_id){
	?>
	</div>
	<?php
}