<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_display_open_cont($form_id){
	//Check to see if the form_id has been sent.
	if($form_id == ''){
		if(isset($_REQUEST['form_id'])){ //If it hasn't, set it to our requested form_id. Sometimes this function can be called without an expressly passed form_id.
			$form_id = absint( $_REQUEST['form_id'] );
		}
	}

	$wrap_class = '';

	$wrap_class = apply_filters( 'ninja_forms_cont_class', $wrap_class, $form_id );

	?>
	<div id="ninja_forms_form_<?php echo $form_id;?>_cont" class="ninja-forms-cont<?php echo $wrap_class; ?>">
	<?php
}

add_action( 'ninja_forms_before_form_display', 'ninja_forms_display_open_cont' );

function ninja_forms_display_close_cont($form_id){
	?>
	</div>
	<?php
}

add_action( 'ninja_forms_after_form_display', 'ninja_forms_display_close_cont' );