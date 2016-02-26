<?php

// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MediaPress Ajax helper
 * Not implementing it as singleton, if you need to add custom handler, attach your own with higher priority
 * 
 */
class MPP_Ajax_Lightbox_Helper {

	private $template_dir;
	
	public function __construct () {
		
		$this->template_dir = mediapress()->get_path() . 'admin/templates/';
		
		$this->setup_hooks();
	}

	
	private function setup_hooks () {
		//activity media
		add_action( 'wp_ajax_mpp_fetch_activity_media', array( $this, 'fetch_activity_media' ) );
		add_action( 'wp_ajax_nopriv_mpp_fetch_activity_media', array( $this, 'fetch_activity_media' ) );
		//for lightbox when clicked on gallery		
		add_action( 'wp_ajax_mpp_fetch_gallery_media', array( $this, 'fetch_gallery_media' ) );
		add_action( 'wp_ajax_nopriv_mpp_fetch_gallery_media', array( $this, 'fetch_gallery_media' ) );
		
	}


	public function fetch_activity_media () {
		
		//do we need nonce validation for this request too? no
		$items = array();
		$activity_id = $_POST['activity_id'];

		if ( ! $activity_id ) {
			exit( 0 );
		}

		$media_ids = mpp_activity_get_attached_media_ids( $activity_id );

		if ( empty( $media_ids ) ) {
			
			array_push( $items, __( 'Sorry, Nothing found!', 'mediapress' ) );
			
			wp_send_json( array( 'items' => $items ) );
			exit( 0 );
		}

		$gallery_id = mpp_activity_get_gallery_id( $activity_id );
		$gallery	= mpp_get_gallery( $gallery_id );
		
		if ( $gallery->component == 'groups' && function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
			//if( empty( buddypress()->groups))
		}
		
		$media_query = new MPP_Media_Query( array( 'in' => $media_ids ) );
		
		if ( $media_query->have_media() ):
			?>


			<?php while ( $media_query->have_media() ): $media_query->the_media(); ?>

				<?php $items[] = array( 'src' => $this->get_media_lightbox_entry() ); ?>

			<?php endwhile; ?>

		<?php endif; ?>
		<?php mpp_reset_media_data(); ?>
		<?php

		wp_send_json( array( 'items' => $items ) );
		exit( 0 );
	}

	public function fetch_gallery_media () {
		
		//do we need nonce validation for this request too? no
		$items = array();
		
		$gallery_id = absint( $_POST['gallery_id'] );
		$gallery = mpp_get_gallery( $gallery_id );
		
		if ( ! $gallery_id || empty( $gallery ) ) {
			exit( 0 );
		}
		
		$statuses = mpp_get_accessible_statuses( $gallery->component, $gallery->component_id, get_current_user_id() );
		
		$media_query = new MPP_Media_Query( array( 'gallery_id' => $gallery_id, 'posts_per_page' => -1, 'status' => $statuses ) );
		
		if ( $media_query->have_media() ):
			?>


			<?php while ( $media_query->have_media() ): $media_query->the_media(); ?>

				<?php $items[] = array( 'src' => $this->get_media_lightbox_entry() ); ?>

			<?php endwhile; ?>

		<?php endif; ?>
		<?php mpp_reset_media_data(); ?>
		<?php

		wp_send_json( array( 'items' => $items ) );
		exit( 0 );
	}
	
	private function get_media_lightbox_entry () {

		ob_start();

		mpp_get_template_part( 'gallery/media/views/lightbox-comment' );
		
		return ob_get_clean();
	}
	
}

//initialize
new MPP_Ajax_Lightbox_Helper();
