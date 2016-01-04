<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
 *
 * Function to filter the term IDS and return the term names.
 *
 * @since 2.2.51
 * @returns void
 */

// Make sure that this function isn't already defined.
if ( !function_exists ( 'ninja_forms_filter_term_ids_for_name' ) ) {
	function ninja_forms_filter_term_ids_for_name( $val, $field_id ){
		global $ninja_forms_loading, $ninja_forms_processing;

		
		$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
		if ( !$add_field )
			return $val;

		if ( isset ( $ninja_forms_loading ) ) {
			$field_row = $ninja_forms_loading->get_field_settings( $field_id );
		} else {
			$field_row = $ninja_forms_processing->get_field_settings( $field_id );
		}
		
		if ( $field_row['type'] == '_list' ) {
			if ( isset( $field_row['data']['populate_term'] ) and !empty ( $field_row['data']['populate_term'] ) ) {
				$tax = $field_row['data']['populate_term'];
				if ( !is_array( $val ) ) {
					if ( strpos( $val, "," ) !== false ) {
						$val = explode( ",", $val );
					}				
				}

				if ( is_array( $val ) ) {
					$tmp = '';
					$x = 0;
					foreach ( $val as $v ) {
						$term_obj = get_term( $v, $tax );
						if ( $term_obj AND !is_wp_error( $term_obj ) ) {
							if ( $x == 0 ) {
								$tmp .= $term_obj->name;
							} else {
								$tmp .= ', '.$term_obj->name;
							}
							$x++;			
						}
					}
					$val = $tmp;
				} else {
					$term_obj = get_term( $val, $tax );
					if ( $term_obj AND !is_wp_error( $term_obj ) ) {
						$val = $term_obj->name;					
					}
				}
			}	
		}

		return $val;
	}

	add_filter( 'ninja_forms_email_user_value', 'ninja_forms_filter_term_ids_for_name', 10, 2 );
	add_filter( 'ninja_forms_export_sub_value', 'ninja_forms_filter_term_ids_for_name', 10, 2 );
}

/*
 *
 * Function to filter the term IDS and return the term names for the backend submission editor.
 *
 * @since 2.2.51
 * @returns void
 */

// Make sure that this function isn't already defined.
if ( !function_exists ( 'ninja_forms_filter_term_ids_for_name_sub_td' ) ) {
	function ninja_forms_filter_term_ids_for_name_sub_td( $val, $field_id, $sub_id ){
		$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
		if ( !$add_field )
			return $val;

		return ninja_forms_filter_term_ids_for_name( $val, $field_id );
	}
	
	add_filter( 'ninja_forms_view_sub_td', 'ninja_forms_filter_term_ids_for_name_sub_td', 10, 3 );
}