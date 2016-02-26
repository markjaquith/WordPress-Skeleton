<?php if ( ! defined( 'ABSPATH' ) ) exit;

function nf_save_sub(){
	global $ninja_forms_processing, $ninja_forms_fields;

	// save forms by default
	$save = true;

	// check if there's some legacy save settings saved in the database
	if ( 0 === $ninja_forms_processing->get_form_setting('save_subs') ) {
		$save = false;
	}
	$save = apply_filters ( 'ninja_forms_save_submission', $save, $ninja_forms_processing->get_form_ID() );

	if( $save ){

		$action = $ninja_forms_processing->get_action();
		$user_id = $ninja_forms_processing->get_user_ID();
		$sub_id = $ninja_forms_processing->get_form_setting( 'sub_id' );
		$form_id = $ninja_forms_processing->get_form_ID();
		$field_data = $ninja_forms_processing->get_all_fields();

		// If we don't have a submission ID already, create a submission post.
		if ( empty( $sub_id ) ) {
			$sub_id = Ninja_Forms()->subs()->create( $form_id );
			Ninja_Forms()->sub( $sub_id )->update_user_id( $user_id );
			do_action( 'nf_create_sub', $sub_id );
			// Update our legacy $ninja_forms_processing with the new sub_id
			$ninja_forms_processing->update_form_setting( 'sub_id', $sub_id );
		}

		do_action( 'nf_before_save_sub', $sub_id );
		
		Ninja_Forms()->sub( $sub_id )->update_action( $action );
		
		if ( is_array ( $field_data ) && ! empty ( $field_data ) ) {
			// Loop through our submitted data and add the values found there.

			// Maintain backwards compatibility with older extensions that use the ninja_forms_save_sub_args filter.
			$data = array();
			//

			foreach ( $field_data as $field_id => $user_value ) {
				$field_row = $ninja_forms_processing->get_field_settings( $field_id );
				$field_type = $field_row['type'];
				if ( isset ( $ninja_forms_fields[$field_type]['save_sub'] ) ) {
					$save_sub = $ninja_forms_fields[$field_type]['save_sub'];
					if( $save_sub ){
						$user_value = apply_filters( 'nf_save_sub_user_value', $user_value, $field_id );
						if( is_array( $user_value ) ){
							$user_value = ninja_forms_esc_html_deep( $user_value );
						}else{
							$user_value = esc_html( $user_value );
						}
						// Add our submitted field value.
						Ninja_Forms()->sub( $sub_id )->add_field( $field_id, $user_value );

						// Maintain backwards compatibility with older extensions that use the ninja_forms_save_sub_args filter.
						$data[] = array( 'field_id' => $field_id, 'user_value' => $user_value );
						//
					}
				}
			}
		}

		// Maintain backwards compatibility with older extensions that still use the ninja_forms_save_sub_args filter.
		$args = apply_filters( 'ninja_forms_save_sub_args', array(
			'sub_id' 	=> $sub_id,
			'form_id' 	=> $form_id,
			'data' 		=> serialize( $data ),
		) );

		ninja_forms_update_sub( $args );
		//

		do_action( 'nf_save_sub', $sub_id );
	}
}

add_action('ninja_forms_post_process', 'nf_save_sub');