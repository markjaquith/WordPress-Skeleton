<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Submissions.
 * This class handles creating and exporting submissions.
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Submissions
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.7
*/

class NF_Subs {

	/**
	 * Get things started
	 * 
	 * @access public
	 * @since 2.7
	 * @return void/
	 */
	public function __construct() {

	}

	/**
	 * Create a submission.
	 * 
	 * @access public
	 * @since 2.7
	 * @return int $sub_id
	 */
	public function create( $form_id = '' ) {
		// Create Submission
		$post = array(
		  'post_status'    => 'publish',
		  'post_type'      => 'nf_sub'
		);
		$sub_id = wp_insert_post( $post );

		// Add our form ID to the submission
		Ninja_Forms()->sub( $sub_id )->update_form_id( $form_id );

		// Get the current sequential ID
		$last_sub = Ninja_Forms()->form( $form_id )->get_setting( 'last_sub', true );
		$seq_num = ! empty ( $last_sub ) ? $last_sub + 1 : 1;

		$seq_num = apply_filters( 'nf_sub_seq_num', $seq_num, $form_id );

		// Add the sequential ID to the post meta
		Ninja_Forms()->sub( $sub_id )->update_seq_num( $seq_num );

		// Update our form data with the new "last seq id."
		Ninja_Forms()->form( $form_id )->update_setting( 'last_sub', $seq_num );

		// Update our sub count
		Ninja_Forms()->form( $form_id )->sub_count = $seq_num - 1;

		return $sub_id;
	}

	/**
	 * Get submissions based on specific critera.
	 * 
	 * @since 2.7
	 * @param array $args
	 * @return array $sub_ids
	 */
	public function get( $args = array() ) {

		$query_args = array(
			'post_type' 		=> 'nf_sub',
			'posts_per_page'	=> -1,
			'date_query'		=> array(
				'inclusive'		=> true,
			),
		);

		if( isset( $args['form_id'] ) ) {
			$query_args['meta_query'][] = array(
				'key' => '_form_id',
				'value' => $args['form_id'],
			);
		}
		
		if( isset( $args['seq_num'] ) ) {
			$query_args['meta_query'][] = array(
				'key' => '_seq_num',
				'value' => $args['seq_num'],
			);
		}

		if( isset( $args['user_id'] ) ) {
			$query_args['author'] = $args['user_id'];
		}
		
		if( isset( $args['action'])){
			$query_args['meta_query'][] = array(
				'key' => '_action',
				'value' => $args['action'],
			);
		}

		if ( isset ( $args['meta'] ) ) {
			foreach ( $args['meta'] as $key => $value ) {
				$query_args['meta_query'][] = array(
					'key' => $key,
					'value' => $value,
				);
			}
		}

		if ( isset ( $args['fields'] ) ) {
			foreach ( $args['fields'] as $field_id => $value ) {
				$query_args['meta_query'][] = array(
					'key' => '_field_' . $field_id,
					'value' => $value,
				);
			}
		}

		if( isset( $args['begin_date'] ) AND $args['begin_date'] != '') {
			$query_args['date_query']['after'] = nf_get_begin_date( $args['begin_date'] )->format("Y-m-d G:i:s");
		}

		if( isset( $args['end_date'] ) AND $args['end_date'] != '' ) {
			$query_args['date_query']['before'] = nf_get_end_date( $args['end_date'] )->format("Y-m-d G:i:s");
		}

		$subs = new WP_Query( $query_args );;

		$sub_objects = array();

		if ( is_array( $subs->posts ) && ! empty( $subs->posts ) ) {
			foreach ( $subs->posts as $sub ) {
				$sub_objects[] = Ninja_Forms()->sub( $sub->ID );
			}			
		}

		wp_reset_postdata();
		return $sub_objects;
	}

	/**
	 * Export submissions.
	 * 
	 * @access public
	 * @param array $sub_ids
	 * @param bool @return
	 * @since 2.7
	 * @return void
	 */
	public function export( $sub_ids = '', $return = false ){
		global $ninja_forms_fields;

		// Bail if we haven't been sent any IDs.
		if ( empty( $sub_ids ) )
			return false;

		if ( ! is_array( $sub_ids ) )
			$sub_ids = array( $sub_ids );

		$plugin_settings = nf_get_settings();
		$date_format = $plugin_settings['date_format'];
	
		$label_array = array();
		// Get our Form ID.
		$form_id = Ninja_Forms()->sub( $sub_ids[0] )->form_id;

		// Get our list of fields.
		$fields = Ninja_Forms()->form( $form_id )->fields;

		// Add our sequential number.
		$label_array[0]['_seq_num'] = __( '#', 'ninja-forms' );

		// Add our "Date" label.
		$label_array[0]['_date_submitted'] = __( 'Date Submitted', 'ninja-forms' );

		$label_array = apply_filters( 'nf_subs_csv_label_array_before_fields', $label_array, $sub_ids );

		foreach ( $fields as $field_id => $field ) {
			// Get our field type
			$field_type = $field['type'];
			// Check to see if our field type has been set as a "process_field".
			if ( isset ( $ninja_forms_fields[ $field_type ] ) ) {
				$reg_field = $ninja_forms_fields[ $field_type ];
				$process_field = $reg_field['process_field'];
			} else {
				$process_field = false;
			}

			// If this field's "process_field" is set to true, then add its label to the array.
			if ( $process_field ) {
				if ( isset ( $field['data']['admin_label'] ) && $field['data']['admin_label'] != '' ) {
					$label = $field['data']['admin_label'];
				} else if( isset ( $field['data']['label'] ) ) {
					$label = $field['data']['label'];
				}else{
					$label = '';
				}

				$label_array[0][ $field_id ] = apply_filters( 'nf_subs_csv_field_label', $label, $field_id );
			}
		}

		$label_array = ninja_forms_stripslashes_deep( $label_array );
		$label_array = apply_filters( 'nf_subs_csv_label_array', $label_array, $sub_ids );

		$value_array = array();
		$x = 0;
		// Loop through our submissions and create a new row for each one.
		foreach ( $sub_ids as $sub_id ) {
			foreach ( $label_array[0] as $field_id => $label ) {
				// Make sure we aren't working with our date field, which will always have a field id of 0.
				if ( $field_id !== 0 ) {
					// Check to see if our field_id is numeric. If it isn't, then we're working with meta, not a field.
					if ( is_numeric( $field_id ) ) {
						// We're working with a field, grab the value.
						$user_value = Ninja_Forms()->sub( $sub_id )->get_field( $field_id );
					} else if ( '_date_submitted' == $field_id ) {
						// Get the date of our submission.
						$date = strtotime( Ninja_Forms()->sub( $sub_id )->date_submitted );
						// The first item is our date field.
						$user_value = date( $date_format, $date );
					} else if ( '_seq_num' == $field_id ) {
						$user_value = Ninja_Forms()->sub( $sub_id )->get_seq_num();
					} else {
						// We're working with a piece of meta, grabe the value.
						$user_value = Ninja_Forms()->sub( $sub_id )->get_meta( $field_id );
					}

					// Run our value through the appropriate filters before we flatten any arrays.
					$user_value = apply_filters( 'nf_subs_export_pre_value', $user_value, $field_id );
					
					// Implode any arrays we might have.
					if ( is_array( $user_value ) ) {
						$user_value = implode( ',', $user_value );
					}

					// Add an ' to the beginning = sign to prevent any CSV/Excel security issues.
					if ( strpos( $user_value, '=' ) === 0 ) {
						$user_value = "'" . $user_value;
					}
					
					// Run our final value through the appropriate filters and assign it to the array.
					$value_array[ $x ][ $field_id ] = htmlspecialchars_decode( apply_filters( 'nf_subs_csv_field_value', $user_value, $field_id ), ENT_QUOTES );					
				}
			}
			$x++;
		}

		$value_array = ninja_forms_stripslashes_deep( $value_array );
		$value_array = apply_filters( 'nf_subs_csv_value_array', $value_array, $sub_ids );

		$array = array( $label_array, $value_array );
		$today = date( $date_format, current_time( 'timestamp' ) );
		$filename = apply_filters( 'nf_subs_csv_filename', 'nf_subs_' . $today );
		$filename = $filename . ".csv";

		if( $return ){
			return str_putcsv( $array, 
				apply_filters( 'nf_sub_csv_delimiter', ',' ), 
				apply_filters( 'nf_sub_csv_enclosure', '"' ), 
				apply_filters( 'nf_sub_csv_terminator', "\n" )
			);
		}else{
			header( 'Content-type: application/csv');
			header( 'Content-Disposition: attachment; filename="'.$filename .'"' );
			header( 'Pragma: no-cache');
			header( 'Expires: 0' );
			echo apply_filters( 'nf_sub_csv_bom',"\xEF\xBB\xBF" ) ; // Byte Order Mark
			echo str_putcsv( $array, 
				apply_filters( 'nf_sub_csv_delimiter', ',' ), 
				apply_filters( 'nf_sub_csv_enclosure', '"' ), 
				apply_filters( 'nf_sub_csv_terminator', "\n" )
			);

			die();
		}

	}

}
