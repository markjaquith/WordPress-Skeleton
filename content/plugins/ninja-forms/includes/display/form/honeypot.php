<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Output our honeypot field
 * @since  2.9.22
 * @param  int  $form_id
 * @return void
 */

function ninja_forms_display_honeypot( $form_id ) {
	$hp_name = ninja_forms_random_string( 5 );
	?>
	<div class="hp-wrap">
		<label><?php _e( 'If you are a human and are seeing this field, please leave it blank.', 'ninja-forms' ); ?>
			<input type="text" value="" name="_<?php echo $hp_name; ?>">
			<input type="hidden" value="_<?php echo $hp_name; ?>" name="_hp_name">
		</label>
	</div>
	<?php
}

add_action( 'ninja_forms_display_before_fields', 'ninja_forms_display_honeypot', 9 );