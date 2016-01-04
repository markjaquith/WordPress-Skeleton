<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_shortcode( $atts ){
	if ( is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {
		$return = '[ninja_forms_display_form';
		if ( is_array ( $atts ) ) {
			foreach ( $atts as $key => $value ) {
				$return .= ' ' . $key . '=' . $value;
			}
		}
		$return .= ']';
		return $return;
	} else {
		$form = ninja_forms_return_echo( 'ninja_forms_display_form', $atts['id'] );
		return $form;
	}
}

add_shortcode( 'ninja_forms', 'ninja_forms_shortcode' );
add_shortcode( 'ninja_form', 'ninja_forms_shortcode' );
/**
 * Old Ninja Forms shortcode
 */
add_shortcode( 'ninja_forms_display_form', 'ninja_forms_shortcode' );

function ninja_forms_field_shortcode( $atts ){
	global $ninja_forms_processing;
	$field_id = $atts['id'];
	if ( is_object ( $ninja_forms_processing ) ) {
		$value = $ninja_forms_processing->get_field_value( $field_id );
		$value = apply_filters( 'ninja_forms_field_shortcode', $value, $atts );
		if( is_array( $value ) ){
			$value = implode( ',', $value );
		}
	} else {
		$value = '';
	}
	return nf_wp_kses_post_deep( $value );
}
add_shortcode( 'ninja_forms_field', 'ninja_forms_field_shortcode' );

function ninja_forms_sub_date_shortcode( $atts ){
	global $ninja_forms_processing;
	if( isset( $atts['format'] ) ){
		$date_format = $atts['format'];
	}else{
		$date_format = 'm/d/Y';
	}
	$date = date( $date_format );
	return $date;
}
add_shortcode( 'ninja_forms_sub_date', 'ninja_forms_sub_date_shortcode' );


function ninja_forms_pre_process_shortcode($content) {
   global $shortcode_tags;
   // Remove our previously registered shortcode.
   //remove_shortcode( 'ninja_forms_display_form' );
   // Backup current registered shortcodes and clear them all out
   $current_shortcodes = $shortcode_tags;
   $shortcode_tags = array();
   add_shortcode( 'ninja_forms_display_form', 'ninja_forms_shortcode' );
   // Do the shortcode (only the one above is registered)
   $content = do_shortcode($content);
   // Put the original shortcodes back
   $shortcode_tags = $current_shortcodes;
   return $content;
}
add_filter('the_content', 'ninja_forms_pre_process_shortcode', 9999);

/**
 * Parse the [nf_sub_seq_num] shortcode
 * 
 * @since 2.8.4
 * @return string $setting
 */
function nf_parse_sub_seq_num_shortcode( $setting, $setting_name = '', $id = '' ) {
	global $ninja_forms_processing;

	if ( ! is_object( $ninja_forms_processing ) )
		return $setting;

	$sub_id = $ninja_forms_processing->get_form_setting( 'sub_id' );
	if ( empty ( $sub_id ) )
		return $setting;

	$seq_num = Ninja_Forms()->sub( $sub_id )->get_seq_num();
	
	$setting = str_replace( '[nf_sub_seq_num]', $seq_num, $setting );
	return $setting;
}

add_filter( 'nf_email_notification_process_setting', 'nf_parse_sub_seq_num_shortcode', 10, 3 );
add_filter( 'nf_success_msg', 'nf_parse_sub_seq_num_shortcode', 10, 2 );

/**
 * Shortcode for ninja_forms_all_fields
 *
 * @since 2.8
 * @return string $content
 */
function nf_all_fields_shortcode( $atts, $content = '' ) {
	global $ninja_forms_fields, $ninja_forms_processing;

	if ( ! isset ( $ninja_forms_processing ) )
		return false;

	$html = isset ( $atts['html'] ) ? $atts['html'] : 1;

	if ( 1 == $html ) {
		// Generate our "all fields" table for use as a JS var.
		$field_list = '<table><tbody>';
	} else {
		$field_list = '';
	}
	foreach ( $ninja_forms_processing->get_all_fields() as $field_id => $user_value ) {
		if ( ! $user_value )
			continue;

		$field = $ninja_forms_processing->get_field_settings( $field_id );
		$type = $field['type'];
		if ( ! isset ( $ninja_forms_fields[ $type ] ) || ! $ninja_forms_fields[ $type ]['process_field'] )
			continue;

		$value = apply_filters( 'nf_all_fields_field_value', ninja_forms_field_shortcode( array( 'id' => $field_id ) ), $field_id );
		$label = strip_tags( apply_filters( 'nf_all_fields_field_label', $field['data']['label'], $field_id ) );

		if ( 1 == $html ) {
			$field_list .= '<tr id="ninja_forms_field_' . $field_id . '"><td>' . $label .':</td><td>' . $value . '</td></tr>';
		} else {
			$field_list .= $label . ' - ' . $value . "\r\n";
		}
	}

	if ( 1 == $html )
		$field_list .= '</tbody></table>';

	return apply_filters( 'nf_all_fields_table', $field_list, $ninja_forms_processing->get_form_ID() );

}

add_shortcode( 'ninja_forms_all_fields', 'nf_all_fields_shortcode' );

/**
 * Parse our [ninja_forms_field] shortcode, just incase the shortcode parser screwed up.
 *
 * @since 2.8.4
 * @return content
 */
function nf_parse_fields_shortcode( $content ) {
	global $ninja_forms_processing;

	if ( ! isset ( $ninja_forms_processing ) )
		return $content;

	if ( is_array ( $content ) )
		return $content;

	$matches = array();
	$pattern = '\[(\[?)(ninja_forms_field|ninja_forms_all_fields)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';

	preg_match_all('/'.$pattern.'/s', $content, $matches);

	if ( is_array( $matches ) && ! empty( $matches[2] ) ) {
		foreach ( $matches[2] as $key => $shortcode ) {

			if ( 'ninja_forms_field' == $shortcode  ) {
				if ( isset ( $matches[3][ $key ] ) ) {
					$atts = shortcode_parse_atts( $matches[3][ $key ] );
					$id = $atts['id'];
					$value = $ninja_forms_processing->get_field_value( $id );
					if( is_array( $value ) ){
						$value = implode( ',', $value );
					}
					$content = str_replace( $matches[0][ $key ], $value, $content );
				}
			} else if ( 'ninja_forms_all_fields' == $shortcode ) {
				if ( isset ( $matches[3][ $key ] ) ) {
					$atts = shortcode_parse_atts( $matches[3][ $key ] );
					$content = str_replace( $matches[0][ $key ], nf_all_fields_shortcode( $atts, $content ), $content );
				}
			}
		}
	}
	return $content;
}

/**
 * Shortcode for ninja_forms_all_fields
 *
 * @since 2.8
 * @return string sub_limit_number
 */
function ninja_forms_display_sub_limit_number_shortcode( $atts ){
  $form = Ninja_Forms()->form( $atts[ 'id' ] );
  
  if ( isset( $form->settings[ 'sub_limit_number' ] ) ) {
    return $form->settings[ 'sub_limit_number' ];
  }
  else {
    return null;
  }
}

add_shortcode( 'ninja_forms_display_sub_limit_number', 'ninja_forms_display_sub_limit_number_shortcode' );

/**
 * Shortcode for ninja_forms_display_sub_number
 *
 * @since 2.8
 * @return int nf_get_sub_count()
 * @see nf_get_sub_count()
 */
function ninja_forms_display_sub_number_shortcode( $atts ){
  $sub_count = nf_get_sub_count( $atts[ 'id' ] );
  return $sub_count;
}

add_shortcode( 'ninja_forms_display_sub_number', 'ninja_forms_display_sub_number_shortcode' );

/**
 * Shortcode for ninja_forms_display_sub_number_remaining
 *
 * @since 2.8
 * @return int
 * @see nf_get_sub_count()
 */
function ninja_forms_display_sub_number_remaining_shortcode( $atts ){
  $form = Ninja_Forms()->form($atts[ 'id' ]);
  
  if( isset( $form->settings[ 'sub_limit_number' ] ) ) {
    $sub_count = nf_get_sub_count( $atts[ 'id' ] );
    $sub_limit = (int) $form->settings[ 'sub_limit_number' ];
    
    if( $sub_count > $sub_limit )
      return 0;
    
    return $sub_limit - $sub_count;
  }
  else {
    return null;
  }
}
add_shortcode( 'ninja_forms_display_sub_number_remaining', 'ninja_forms_display_sub_number_remaining_shortcode' );
