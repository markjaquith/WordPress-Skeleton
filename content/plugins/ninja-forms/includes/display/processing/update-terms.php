<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 *
 * Function to hook into the post creation/update that will change the term based upon the selected term(s)
 *
 * @since 2.2.51
 * @return void
 */

// Make sure that this function isn't already defined.
if ( !function_exists ( 'ninja_forms_pre_process_populate_term' ) ) {
    function ninja_forms_pre_process_populate_term( $form_id ){
        global $ninja_forms_processing;

        $add_field = apply_filters( 'ninja_forms_use_post_fields', false );
        if ( !$add_field )
            return false;

        // Loop through our fields and see if we have a list field. If we do, check for the 'populate_term' setting.
        $field_values = $ninja_forms_processing->get_all_fields();
        if( is_array( $field_values ) ){
            foreach( $field_values as $field_id => $user_value ){
                $field_row = $ninja_forms_processing->get_field_settings( $field_id );
                $field_type = $field_row['type'];
                $field_data = $field_row['data'];
                if( $field_type == '_list' AND isset( $field_data['populate_term'] ) AND $field_data['populate_term'] != '' ){
                    if( !is_array( $user_value ) ){
                        $user_value = array( $user_value );
                    }
                    $ninja_forms_processing->update_form_setting( $field_data['populate_term'].'_terms', $user_value );
                }
            }
        }
    }

    add_action( 'ninja_forms_pre_process', 'ninja_forms_pre_process_populate_term' );
}