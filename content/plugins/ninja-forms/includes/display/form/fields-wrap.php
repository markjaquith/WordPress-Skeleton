<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 *
 * Function that outputs an opening <div> tag for wrapping fields.
 *
 * @since 2.2.17
 * @return void
 */

function ninja_forms_display_open_fields_wrap( $form_id ){
	$display = 1;

	$display = apply_filters( 'ninja_forms_display_fields_wrap_visibility', $display, $form_id );

	if ( $display != 1 )
		$hide_class = " ninja-forms-no-display";
	else
		$hide_class = "";

	$wrap_class = '';

	$wrap_class = apply_filters( 'ninja_forms_fields_wrap_class', $wrap_class, $form_id );

	?>
	<div id="ninja_forms_form_<?php echo $form_id;?>_all_fields_wrap" class="ninja-forms-all-fields-wrap<?php echo $wrap_class;?><?php echo $hide_class;?>">
	<?php
}

add_action( 'ninja_forms_display_before_fields', 'ninja_forms_display_open_fields_wrap' );

 /*
  *
  * Function that outputs a closing </div> tag for wrapping fields.
  *
  * @since 2.2.17
  * @returns void
  */

function ninja_forms_display_close_fields_wrap( $form_id ){
	?>
	</div>
	<?php
}

add_action( 'ninja_forms_display_after_fields', 'ninja_forms_display_close_fields_wrap' );