<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 *
 * Function to add a dropdown of terms to the list field.
 *
 * @since 2.2.51
 * @returns void
 */

// Make sure that this function isn't already defined.
if ( !function_exists ( 'ninja_forms_edit_field_list_term' ) ) {
    function ninja_forms_edit_field_list_term( $field_id, $field_data ){
        $add_field = apply_filters( 'ninja_forms_use_post_fields', false );
        if ( !$add_field )
            return false;

        $field_row = ninja_forms_get_field_by_id( $field_id );
        $field_type = $field_row['type'];

        if( isset( $field_data['populate_term'] ) ){
            $populate_term = $field_data['populate_term'];
        }else{
            $populate_term = '';
        }

        if ( $populate_term != '' ) {
            $display = '';
        } else {
            $display = 'style="display:none;"';
        }

        $form_row = ninja_forms_get_form_by_field_id( $field_id );

        if( isset( $form_row['data']['post_type'] ) ){
            $post_type = $form_row['data']['post_type'];
        }else{
            $post_type = '';
        }

        if( $field_type == '_list' AND $post_type != '' ){
           ?>
            <div>
                <hr>
                <label>
                    <?php _e( 'Populate this with the taxonomy', 'ninja-forms' );?>: 
                </label>
                <select name="ninja_forms_field_<?php echo $field_id;?>[populate_term]" class="ninja-forms-list-populate-term" rel="<?php echo $field_id;?>">
                    <option value=""><?php _e( '- None', 'ninja-forms' );?></option>
                    <?php
                     // Get a list of terms registered to the post type set above and loop through them.
                    foreach ( get_object_taxonomies( $post_type ) as $tax_name ) {
                        if( $tax_name != 'post_tag' AND $tax_name != 'post_status' AND $tax_name != 'post_format' ){
                            $tax = get_taxonomy( $tax_name );
                            ?>
                            <option value="<?php echo $tax_name;?>" <?php selected( $populate_term, $tax_name );?>><?php echo $tax->labels->name;?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                <div id="ninja_forms_field_<?php echo $field_id;?>_exclude_terms" <?php echo $display;?>>
                    <?php ninja_forms_list_terms_checkboxes( $field_id, $populate_term ); ?>
                </div>
                <br />
                <hr>
            </div>
            <?php
        }
    }

    add_action( 'ninja_forms_edit_field_after_registered', 'ninja_forms_edit_field_list_term', 9, 2 );
}