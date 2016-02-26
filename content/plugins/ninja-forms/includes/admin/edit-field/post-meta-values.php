<?php if ( ! defined( 'ABSPATH' ) ) exit;
/*
function ninja_forms_post_meta_value_box( $field_id ){
	global $wpdb, $ninja_forms_fields;

	$add_field = apply_filters( 'ninja_forms_use_post_fields', false );
	if ( !$add_field )
		return false;

		
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	$reg_field = $ninja_forms_fields[$field_type];
	$field_process = $reg_field['process_field'];

	$form_row = ninja_forms_get_form_by_field_id( $field_id );
	if ( isset ( $form_row['data']['create_post'] ) AND $form_row['data']['create_post'] == 1 ) {
		if( isset( $field_data['post_meta_value'] ) ){
			$post_meta_value = $field_data['post_meta_value'];
		}else{
			$post_meta_value = '';
		}

		if( $field_process ){
			$meta_keys = $wpdb->get_results( "SELECT meta_key FROM $wpdb->postmeta", ARRAY_A );
			$meta_array = array();
			foreach( $meta_keys as $key ){
				$first_char = substr( $key['meta_key'], 0, 1 );
				if( $first_char != '_' ){
					array_push( $meta_array, $key['meta_key'] );
				}
			}
			$meta_array = array_unique( $meta_array );

			?>
			<div class=" description description-wide" id="ninja_forms_post_meta_values">
			<label class="label">
				<?php _e( 'Attach this value to custom post meta', 'ninja-forms' );?>:
			</label><br />
			<?php
			if( is_array( $meta_array ) AND !empty( $meta_array ) ){
				$custom = true;
				if( $post_meta_value != '' ){
					foreach( $meta_array  as $meta ){
						if( $post_meta_value == $meta ){
							$custom = false;
						}
					}
				}
				if( $post_meta_value == '' ){
					$custom = false;
				}
			}
			?>
			<select name="" id="ninja_forms_field_<?php echo $field_id;?>_post_meta_value" class="ninja-forms-post-meta-value">
				<option value="">- None</option>
				<option value="custom" <?php selected($custom, true);?>>- Custom -></option>
				<?php
				if( is_array( $meta_array ) AND !empty( $meta_array ) ){
					$custom = true;
					if( $post_meta_value != '' ){
						foreach( $meta_array  as $meta ){
							if( $post_meta_value == $meta ){
								$custom = false;
							}
						}
					}
					if( $post_meta_value == '' ){
						$custom = false;
					}
					
					foreach( $meta_array  as $meta ){
						?>
						<option value="<?php echo $meta;?>" <?php selected( $post_meta_value, $meta );?>><?php echo $meta;?></option>
						<?php
					}
				}
				?>
			</select>
			<?php
			if( $custom ){
				$display_input = '';
			}else{
				$display_input = 'display:none;';
			}
			?>
			<input type="text" name="ninja_forms_field_<?php echo $field_id;?>[post_meta_value]" id="ninja_forms_field_<?php echo $field_id;?>_custom_post_meta_value" value="<?php echo $post_meta_value;?>" style="<?php echo $display_input;?> width:350px">
			</div>
			<?php
		}	
	}
}

add_action( 'ninja_forms_edit_field_after_registered', 'ninja_forms_post_meta_value_box' );
*/