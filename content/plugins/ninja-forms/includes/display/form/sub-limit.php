<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Function that checks the current sub count and updates the loading class if neccesary.
 * @param  $string $form_id
 * @since 2.5
 * @return void
 */
function nf_check_sub_limit( $form_id ) {
    global $ninja_forms_loading;

    if ( ! isset( $ninja_forms_loading ) )
        return false;

    $sub_limit = $ninja_forms_loading->get_form_setting( 'sub_limit_number' );

    if ( !$sub_limit or empty ( $sub_limit ) )
        return false;

    $args = array(
        'form_id' => $form_id,
        'action'  => 'submit',
    );
    $sub_count = ninja_forms_get_sub_count( $args );

    if ( $sub_count >= $sub_limit ) {
        $ninja_forms_loading->update_form_setting( 'sub_limit_reached', true );
    }

}
add_action( 'ninja_forms_display_init', 'nf_check_sub_limit' );

/**
 * Function that filters the display variable and returns false if sub limit has been reached.
 * @param  bool $display
 * @param  string $form_id
 * @since 2.5
 * @return string $display
 */
function nf_sub_limit_display_filter( $display, $form_id ) {
    global $ninja_forms_loading;

    if ( ! isset( $ninja_forms_loading ) )
        return $display;

    if ( $ninja_forms_loading->get_form_setting( 'sub_limit_reached' ) ) {
        $display = 0;
    }
    return $display;
}
add_filter( 'ninja_forms_display_show_form', 'nf_sub_limit_display_filter', 10, 2 );

/**
 * Function that echoes the sub limit reached message if necessary.
 * @param  string $form_id
 * @since 2.5
 * @return void
 */
function nf_sub_limit_display_msg( $form_id ) {
    global $ninja_forms_loading;

    if ( ! isset( $ninja_forms_loading ) )
        return false;

    if ( $ninja_forms_loading->get_form_setting( 'sub_limit_reached' ) ) {
        $msg = $ninja_forms_loading->get_form_setting( 'sub_limit_msg' );
        $msg = wpautop( $msg );
        $msg = do_shortcode( $msg );
        $msg = '<div class="sub-limit-reached-msg">' . $msg . '</div>';
        $msg = apply_filters( 'nf_sub_limit_reached_msg', $msg, $form_id );
        echo $msg;
    }

}
add_action( 'ninja_forms_display_user_not_logged_in', 'nf_sub_limit_display_msg' );
