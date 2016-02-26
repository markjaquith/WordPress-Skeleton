<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 *
 * Function to hook into our field filter and add the selected terms if the field is set to populate_term.
 *
 * @since 2.2.51
 * @return void
 */

// Make sure that this function isn't already defined.
if ( !function_exists ( 'ninja_forms_field_filter_populate_term' ) ) {
    function ninja_forms_field_filter_populate_term( $data, $field_id ){
        global $post, $ninja_forms_loading, $ninja_forms_processing;

        $add_field = apply_filters( 'ninja_forms_use_post_fields', false );
        if ( !$add_field )
            return $data;

        if ( isset ( $ninja_forms_loading ) ) {
            $field_row = $ninja_forms_loading->get_field_settings( $field_id );
        } else if ( isset ( $ninja_forms_processing ) ) {
            $field_row = $ninja_forms_processing->get_field_settings( $field_id );
        }
        
        $field_type = $field_row['type'];
        $field_data = $field_row['data'];

        if ( isset ( $field_data['exclude_terms'] ) ) {
            $exclude_terms = $field_data['exclude_terms'];
        } else {
            $exclude_terms = array();
        }

        if( $field_type == '_list' AND isset( $field_data['populate_term'] ) AND $field_data['populate_term'] != '' ){
            // Set the selected option if we are editing a post.
            if( is_object( $post ) ){
                $selected_terms = get_the_terms( $post->ID, $field_data['populate_term'] );
            }else{
                $selected_term = '';
            }

            if( is_array( $selected_terms ) ){
                foreach( $selected_terms as $term ){
                    $selected_term = $term->term_id;
                    break;
                }
            } else {
            	$selected_term = '';
            }
            if ( $field_data['list_type'] == 'dropdown' ) {
                $tmp_array = array( array( 'label' => __( '- Select One', 'ninja-forms' ), 'value' => '' ) );  
            } else {
                $tmp_array = array();
            }
            
            $populate_term = $field_data['populate_term'];
            $taxonomies = array( $populate_term );
            $args = array(
                'hide_empty' => false,
                'parent' => 0
            );
            $data['default_value'] = $selected_term;
            foreach( get_terms( $taxonomies, $args ) as $parent_term ){
                $tmp_array[] = array( 'label' => $parent_term->name, 'value' => $parent_term->term_id );
                $child_args = array(
                    'hide_empty' => false,
                    'child_of' => $parent_term->term_id
                );
                foreach( get_terms( $taxonomies, $child_args ) as $child_term ) {
                   $tmp_array[] = array( 'label' => '&nbsp;&nbsp;&nbsp;&nbsp;'.$child_term->name, 'value' => $child_term->term_id ); 
                }
                
            }
            $data['list']['options'] = apply_filters( 'ninja_forms_list_terms', $tmp_array, $field_id );
            $data['list_show_value'] = 1;
        }
        return $data;
    }

    add_filter( 'ninja_forms_field', 'ninja_forms_field_filter_populate_term', 11, 2 );
}