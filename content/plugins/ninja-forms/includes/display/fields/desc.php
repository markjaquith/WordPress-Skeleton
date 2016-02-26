<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Outputs the HTML of the description content if it is set to display.
 *
**/

// add_action( 'init', 'ninja_forms_init_field_desc' );
// function ninja_forms_init_field_desc() {
// 	add_action( 'ninja_forms_display_before_field', 'ninja_forms_add_field_desc', 10, 2 );
// }

function ninja_forms_add_field_desc( $field_id, $data ){
	$plugin_settings = nf_get_settings();

	if ( isset( $data['desc_pos'] ) ) {
		$desc_pos = $data['desc_pos'];
	} else {
		$desc_pos = 'none';
	}

	if ( $desc_pos == 'none' ) {
		remove_action( 'ninja_forms_display_after_opening_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_before_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_after_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_before_closing_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
	} elseif ( $desc_pos == 'before_everything' ) {
		add_action( 'ninja_forms_display_after_opening_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_before_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_after_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_before_closing_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
	} elseif ( $desc_pos == 'before_label' ) {
		add_action( 'ninja_forms_display_before_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_after_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_before_closing_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_after_opening_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
	} elseif ( $desc_pos == 'after_label' ) {
		add_action( 'ninja_forms_display_after_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_before_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_before_closing_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_after_opening_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
	} elseif ( $desc_pos == 'after_everything' ) {
		add_action( 'ninja_forms_display_before_closing_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_before_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_after_field_label', 'ninja_forms_display_field_desc', 10, 2 );
		remove_action( 'ninja_forms_display_after_opening_field_wrap', 'ninja_forms_display_field_desc', 10, 2 );
	}

}
add_action( 'ninja_forms_display_before_field', 'ninja_forms_add_field_desc', 10, 2 );

function ninja_forms_display_field_desc( $field_id, $data ){
	$plugin_settings = nf_get_settings();

	$class = apply_filters( 'ninja_forms_display_field_desc_class', 'ninja-forms-field-description', $field_id );
	
	if ( ( isset( $data['show_desc'] ) and $data['show_desc'] == 1 ) and isset( $data['desc_text'] ) ) {
		echo '<div class="' . $class . '">';
			echo do_shortcode( wpautop( $data['desc_text'] ) );
		echo '</div>';
	}
}