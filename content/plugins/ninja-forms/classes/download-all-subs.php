<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Download_All_Subs extends NF_Step_Processing {

	function __construct() {
		$this->action = 'download_all_subs';

		parent::__construct();
	}

	public function loading() {

		$form_id  = isset( $this->args['form_id'] ) ? absint( $this->args['form_id'] ) : 0;

		if ( empty( $form_id ) ) {
			return array( 'complete' => true );
		}
			
	 	$sub_count = nf_get_sub_count( $form_id );

		if( empty( $this->total_steps ) || $this->total_steps <= 1 ) {
			$this->total_steps = round( ( $sub_count / 250 ), 0 ) + 2;
		}

		$args = array(
			'total_steps' => $this->total_steps,
		);

		$this->args['filename'] = $this->random_filename( 'all-subs' );
		update_user_option( get_current_user_id(), 'nf_download_all_subs_filename', $this->args['filename'] );
		$this->redirect = esc_url_raw( add_query_arg( array( 'download_all' => $this->args['filename'] ), $this->args['redirect'] ) );

		return $args;
	}

	public function step() {
		
		$exported_subs = get_user_option( get_current_user_id(), 'nf_download_all_subs_ids' );
		if ( ! is_array( $exported_subs ) ) {
			$exported_subs = array();
		}

		$previous_name = get_user_option( get_current_user_id(), 'nf_download_all_subs_filename' );
		if ( $previous_name ) {
			$this->args['filename'] = $previous_name;
		}

		$args = array(
			'posts_per_page' => 250,
			'paged' => $this->step,
			'post_type' => 'nf_sub',
			'meta_query' => array(
				array( 
					'key' => '_form_id',
					'value' => $this->args['form_id'],
				),
			),
		);

		$subs_results = get_posts( $args );

		if ( is_array( $subs_results ) && ! empty( $subs_results ) ) {
			$upload_dir = wp_upload_dir();
			$file_path = trailingslashit( $upload_dir['path'] ) . $this->args['filename'] . '.csv';
			$myfile = fopen( $file_path, 'a' ) or die( 'Unable to open file!' );
			$x = 0;
			$export = '';
			foreach ( $subs_results as $sub ) {
				$sub_export = Ninja_Forms()->sub( $sub->ID )->export( true );
				if ( $x > 0 || $this->step > 1 ) {
					$sub_export = substr( $sub_export, strpos( $sub_export, "\n" ) + 1 );
				}
				if ( ! in_array( $sub->ID, $exported_subs ) ) {
					$export .= $sub_export;
					$exported_subs[] = $sub->ID;					
				}
				$x++;
			}
			fwrite( $myfile, $export );
			fclose( $myfile );
		}

		update_user_option( get_current_user_id(), 'nf_download_all_subs_ids', $exported_subs );
	}

	public function complete() {
		delete_user_option( get_current_user_id(), 'nf_download_all_subs_ids' );
		delete_user_option( get_current_user_id(), 'nf_download_all_subs_filename' );
	}

	/**
	 * Add an integar to the end of our filename to make sure it is unique
	 * 
	 * @access public
	 * @since 2.7.6
	 * @return $filename
	 */
	public function random_filename( $filename ) {
		$upload_dir = wp_upload_dir();
		$file_path = trailingslashit( $upload_dir['path'] ) . $filename . '.csv';
		if ( file_exists ( $file_path ) ) {
			for ($x = 0; $x < 999 ; $x++) { 
				$tmp_name = $filename . '-' . $x;
				$tmp_path = trailingslashit( $upload_dir['path'] );
				if ( file_exists( $tmp_path . $tmp_name . '.csv' ) ) {
					$this->random_filename( $tmp_name );
					break;
				} else {
					$this->filename = $tmp_name;
					break;
				}
			}
		}

		return $filename;
	}

}