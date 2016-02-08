<?php

// Exit if the file is accessed directly over web
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MediaPress Ajax helper
 * 
 * We will be moving out stuff to their respective classes in future
 * For now, It is the monolithic implementation for most of the mediapress actions
 */
class MPP_Ajax_Helper {

	private static $instance;
	
	private $template_dir;
	
	private function __construct () {
		
		$this->template_dir = mediapress()->get_path() . 'admin/templates/';
		
		$this->setup_hooks();
	}

	/**
	 * Get singleton instance
	 * 
	 * @return MPP_Ajax_Helper
	 */
	public static function get_instance () {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function setup_hooks () {

		//directory loop
		add_action( 'wp_ajax_mpp_filter', array( $this, 'load_dir_list' ) );
		add_action( 'wp_ajax_nopriv_mpp_filter', array( $this, 'load_dir_list' ) );
		//add/upload a new Media
		add_action( 'wp_ajax_mpp_add_media', array( $this, 'add_media' ) );
		add_action( 'wp_ajax_mpp_upload_cover', array( $this, 'cover_upload' ) );


		//publish to activity
		add_action( 'wp_ajax_mpp_publish_gallery_media', array( $this, 'publish_gallery_media' ) );
		add_action( 'wp_ajax_mpp_hide_unpublished_media', array( $this, 'hide_unpublished_media' ) );

		//media delete
		add_action( 'wp_ajax_mpp_delete_media', array( $this, 'delete_media' ) );
		add_action( 'wp_ajax_mpp_reorder_media', array( $this, 'reorder_media' ) );
		add_action( 'wp_ajax_mpp_bulk_update_media', array( $this, 'bulk_update_media' ) );
		add_action( 'wp_ajax_mpp_delete_gallery_cover', array( $this, 'delete_gallery_cover' ) );
		add_action( 'wp_ajax_mpp_update_gallery_details', array( $this, 'update_gallery_details' ) );
		
		add_action( 'wp_ajax_mpp_reload_bulk_edit', array( $this, 'reload_bulk_edit' ) );
		add_action( 'wp_ajax_mpp_reload_add_media', array( $this, 'reload_add_media' ) );
		
		
	}

	/**
	 * Loads directroy gallery list via ajax
	 * 
	 */
	public function load_dir_list () {

		$type = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
		$page = absint( $_POST['page'] );

		$scope = $_POST['scope'];

		$search_terms = $_POST['search_terms'];

		//make the query and setup 
		mediapress()->is_directory = true;

		//get all public galleries, should we do type filtering
		mediapress()->the_gallery_query = new MPP_Gallery_Query( array(
			'status'		=> 'public',
			'type'			=> $type,
			'page'			=> $page,
			'search_terms'	=> $search_terms,
		) );

		mpp_get_template( 'gallery/loop-gallery.php' );

		exit( 0 );
	}

	/**
	 * Add new media via ajax
	 * This method will be refactored in future to allow adding media from web
	 * 
	 */
	public function add_media () {

		check_ajax_referer( 'mpp_add_media' ); //check for the referrer

		$response = array();

		$file = $_FILES;

		$file_id = '_mpp_file'; //key name in the files array
		//find the components we are trying to add for
		$component = $_POST['component'];
		$component_id = absint( $_POST['component_id'] );
		
		$context = mpp_get_upload_context( false, $_POST['context'] );

		if ( ! $component ) {
			$component = mpp_get_current_component();
		}
		
		if ( ! $component_id ) {
			$component_id = mpp_get_current_component_id();
		}
		
		//get the uploader
		$uploader = mpp_get_storage_manager(); //should we pass the component?
		////should we check for the existence of the default storage method?
		//setup for component
		//$uploader->setup_for( $component, $component_id );

		//check if the server can handle the upload?
		if ( ! $uploader->can_handle() ) {

			wp_send_json_error( array(
				'message' => __( 'Server can not handle this much amount of data. Please upload a smaller file or ask your server administrator to change the settings.', 'mediapress' )
			) );
		}

		if ( ! mpp_has_available_space( $component, $component_id ) ) {
			wp_send_json_error( array(
				'message' => __( 'Unable to upload. You have used the allowed storage quota!', 'mediapress' )
			) );
		}
		//if we are here, the server can handle upload 
		//check should be here
		$gallery_id = 0;

		if ( isset( $_POST['gallery_id'] ) ) {
			$gallery_id = absint( $_POST['gallery_id'] );
		}
		
		if ( $gallery_id ) {
			$gallery = mpp_get_gallery( $gallery_id );
		} else {
			$gallery = false; //not set
		}	
			
		//if there is no gallery id given and the context is activity, we may want to auto create the gallery
		
		$media_type = mpp_get_media_type_from_extension( mpp_get_file_extension( $file[ $file_id ]['name'] ) );

		if ( ! $media_type ) {

			wp_send_json_error( array( 'message' => __( "This file type is not supported.", 'mediapress' ) ) );
		}

		//if there is no gallery type defined( It wil happen in case of new gallery creation from admin page
		//we will set the gallery type as the type of the first media

		if ( $gallery && empty( $gallery->type ) ) {
			//update gallery type
			//set it to media type
			mpp_update_gallery_type( $gallery, $media_type );
		}
		//If the gallery is not given and It is members component, check if the upload context is activity?
		//Check if we have a profile gallery set for the current user for this type of media
		//if yes, then use that gallery to upload the media
		//otherwise we create a gallery of the current media type and set it as the profile gallery for that type

		if ( ! $gallery && $context == 'activity' ) {

			//if gallery is not given and the component supports wall gallery
			//then create
			if ( ! mpp_is_activity_upload_enabled( $component ) ) {

				wp_send_json_error( array( 'message' => __( "The gallery is not selected.", 'mediapress' ) ) );
			}
			
			$gallery_id = mpp_get_wall_gallery_id( array( 
				'component'		=> $component, 
				'component_id'	=> $component_id, 
				'media_type'	=> $media_type 
			) );
			

			if ( ! $gallery_id ) {
				//if gallery does not exist, create it
				// 1.  let us make sure that the wall gallery creation activity is never recorded
				add_filter( 'mpp_do_not_record_create_gallery_activity', '__return_true' ); //do not record gallery activity

				$gallery_id = mpp_create_gallery( array(
					'creator_id'	=> get_current_user_id(),
					'title'			=> sprintf( _x( 'Wall %s Gallery', 'wall gallery name', 'mediapress' ), $media_type ),
					'description'	=> '',
					'status'		=> 'public',
					'component'		=> $component,
					'component_id'	=> $component_id,
					'type'			=> $media_type
				) );
				
				//remove the filter we added
				remove_filter( 'mpp_do_not_record_create_gallery_activity', '__return_true' );
				
				if ( $gallery_id ) {
					//save the profile gallery id
					mpp_update_wall_gallery_id( array(
						'component'		=> $component,
						'component_id'	=> $component_id,
						'media_type'	=> $media_type,
						'gallery_id'	=> $gallery_id
					) );
				}
			}
			//setup gallery object from the profile gallery id
			if ( $gallery_id ) {
				$gallery = mpp_get_gallery( $gallery_id );
			}
			
			//for preexisting activity wall gallery, the original status may not be enabled
			if ( ! mpp_is_active_status( $gallery->status ) ) {
				
				//the current wall gallery status is invalid,
				//update wall gallery status to current default privacy
				mpp_update_gallery_status( $gallery, mpp_get_default_status() );
				
			}
		}
		//we may want to check the upload type and set the gallery to activity gallery etc if it is not set already

		$error = false;

		//detect media type of uploaded file here and then upload it accordingly also check if the media type uploaded and the gallery type matches or not
		//let us build our response for javascript
		//if we are uploading to a gallery, check for type
		//since we will be allowin g upload without gallery too, It is required to make sure $gallery is present or not

		if ( $gallery && ! mpp_is_mixed_gallery( $gallery ) && $media_type !== $gallery->type ) {
			//if we are uploading to a gallery and It is not a mixed gallery, the media type must match the gallery type
			wp_send_json_error( array(
				'message' => sprintf( __( 'This file type is not allowed in current gallery. Only <strong>%s</strong> files are allowed!', 'mediapress' ), mpp_get_allowed_file_extensions_as_string( $gallery->type ) )
			) );
		}


		//if we are here, all is well :)

		if ( ! mpp_user_can_upload( $component, $component_id, $gallery ) ) {

			$error_message = apply_filters( 'mpp_upload_permission_denied_message', __( "You don't have sufficient permissions to upload.", 'mediapress' ) );

			wp_send_json_error( array( 'message' => $error_message ) );
		}

		//if we are here, we have checked for all the basic errors, so let us just upload now


		$uploaded = $uploader->upload( $file, array( 'file_id' => $file_id, 'gallery_id' => $gallery_id, 'component' => $component, 'component_id' => $component_id ) );

		//upload was succesfull?
		if ( ! isset( $uploaded['error'] ) ) {

			//file was uploaded successfully
			if ( apply_filters( 'mpp_use_processed_file_name_as_media_title', false ) ) {

				$title = wp_basename( $uploaded['file'] ); //$_FILES[ $file_id ][ 'name' ];
			} else {

				$title = wp_basename( $_FILES[$file_id]['name'] );
			}

			$title_parts = pathinfo( $title );
			$title = trim( substr( $title, 0, -( 1 + strlen( $title_parts['extension'] ) ) ) );

			$url  = $uploaded['url'];
			$type = $uploaded['type'];
			$file = $uploaded['file'];


			//$title = isset( $_POST['media_title'] ) ? $_POST['media_title'] : '';

			$content = isset( $_POST['media_description'] ) ? $_POST['media_description'] : '';

			$meta = $uploader->get_meta( $uploaded );


			$title_desc = $this->get_title_desc_from_meta( $type, $meta );

			if ( ! empty( $title_desc ) ) {

				if ( empty( $title ) && ! empty( $title_desc['title'] ) ) {
					$title = $title_desc['title'];
				}

				if ( empty( $content ) && ! empty( $title_desc['content'] ) ) {
					$content = $title_desc['content'];
				}
			}



			$status = isset( $_POST['media_status'] ) ? $_POST['media_status'] : '';

			if ( empty( $status ) && $gallery ) {
				$status = $gallery->status; //inherit from parent,gallery must have an status
			}
				
			//we may need some more enhancements here
			if ( ! $status ) {
				$status = mpp_get_default_status();
			}

			//   print_r($upload_info);
			$is_orphan = 0;
			//Any media uploaded via activity is marked as orphan( Not associated with the mediapress unless the activity to which it was attached is actually created, check core/activity/actions.php to see how the orphaned media is adopted by the activity :) )
			if ( $context == 'activity' ) {
				$is_orphan = 1; //by default mark all uploaded media via activity as orphan
			}


			$media_data = array(
				'title'				=> $title,
				'description'		=> $content,
				'gallery_id'		=> $gallery_id,
				'user_id'			=> get_current_user_id(),
				'is_remote'			=> false,
				'type'				=> $media_type,
				'mime_type'			=> $type,
				'src'				=> $file,
				'url'				=> $url,
				'status'			=> $status,
				'comment_status'	=> 'open',
				'storage_method'	=> mpp_get_storage_method(),
				'component_id'		=> $component_id,
				'component'			=> $component,
				'context'			=> $context,
				'is_orphan'			=> $is_orphan,
			);

			$id = mpp_add_media( $media_data );

			//if the media is not uploaded from activity and auto publishing is not enabled, record as unpublished
			if ( $context != 'activity' && ! mpp_is_auto_publish_to_activity_enabled( 'add_media' ) ) {

				mpp_gallery_add_unpublished_media( $gallery_id, $id );
			}

			//should we update and resize images here?
			//
			mpp_gallery_increment_media_count( $gallery_id );

			$attachment = mpp_media_to_json( $id );
			//$attachment['data']['type_id'] = mpp_get_type_term_id( $gallery->type );
			echo json_encode( array(
				'success'	=> true,
				'data'		=> $attachment,
			) );

			//wp_send_json_success( array('name'=>'what') );
			exit( 0 );
		} else {

			wp_send_json_error( array( 'message' => $uploaded['error'] ) );
		}
	}
/**
 * Not used, Oembed support is not yet available in 1.0 branch
 */
	public function add_oembed_media () {

		check_ajax_referer( 'mpp_add_media' ); //check for the referrer


		$media_type = '';
		$gallery_id = '';

		$component = $_POST['component'];
		$component_id = $_POST['component_id'];
		$context = mpp_get_upload_context( false, $_POST['context'] );

		if ( !$component )
			$component = mpp_get_current_component();

		if ( !$component_id )
			$component_id = mpp_get_current_component_id();

		//get the uploader
		$uploader = mpp_get_storage_manager( 'oembed' ); //should we pass the component?
		//setup for component
		//$uploader->setup_for( $component, $component_id );

		//check if the server can handle the upload?
		if ( !$uploader->can_handle() ) {

			wp_send_json_error( array(
				'message' => __( 'Server can not handle this much amount of data. Please upload a smaller file or ask your server administrator to change the settings.', 'mediapress' )
			) );
		}

		if ( !mpp_has_available_space( $component, $component_id ) ) {
			wp_send_json_error( array(
				'message' => __( 'Unable to upload. You have used the allowed storage quota!', 'mediapress' )
			) );
		}
		//if we are here, the server can handle upload 
		//check should be here
		$gallery_id = 0;

		if ( isset( $_POST['gallery_id'] ) )
			$gallery_id = absint( $_POST['gallery_id'] );

		if ( $gallery_id )
			$gallery = mpp_get_gallery( $gallery_id );
		else
			$gallery = false; //not set

			
//if there is no gallery id given and the context is activity, we may want to auto create the gallery

		$media_type = mpp_get_media_type_from_extension( mpp_get_file_extension( $file[$file_id]['name'] ) );

		if ( !$media_type ) {

			wp_send_json_error( array( 'message' => __( "This file type is not supported.", 'mediapress' ) ) );
		}

		//if there is no gallery type defined( It wil happen in case of new gallery creation from admin page
		//we will set the gallery type as the type of the first media

		if ( $gallery && empty( $gallery->type ) ) {
			//update gallery type
			//set it to media type
			mpp_update_gallery_type( $gallery, $media_type );
		}
		//If the gallery is not given and It is members component, check if the upload context is activity?
		//Check if we have a profile gallery set for the current user for this type of media
		//if yes, then use that gallery to upload the media
		//otherwise we create a gallery of the current media type and set it as the profile gallery for that type

		if ( !$gallery && $context == 'activity' ) {

			//if gallery is not given and the component supports wall gallery
			//then create
			if ( !mpp_is_activity_upload_enabled( $component ) ) {

				wp_send_json_error( array( 'message' => __( "The gallery is not selected.", 'mediapress' ) ) );
			}
			$gallery_id = mpp_get_wall_gallery_id( array( 'component' => $component, 'component_id' => $component_id, 'media_type' => $media_type ) );

			if ( !$gallery_id ) {
				//if gallery does not exist, create 1

				$gallery_id = mpp_create_gallery( array(
					'creator_id' => get_current_user_id(),
					'title' => sprintf( _x( 'Wall %s Gallery', 'wall gallery name', 'mediapress' ), $media_type ),
					'description' => '',
					'status' => 'public',
					'component' => $component,
					'component_id' => $component_id,
					'type' => $media_type
						) );

				if ( $gallery_id ) {
					//save the profile gallery id
					mpp_update_wall_gallery_id( array(
						'component' => $component,
						'component_id' => $component_id,
						'media_type' => $media_type,
						'gallery_id' => $gallery_id
					) );
				}
			}
			//setup gallery object from the profile gallery id
			if ( $gallery_id )
				$gallery = mpp_get_gallery( $gallery_id );
		}
		//we may want to check the upload type and set the gallery to activity gallery etc if it is not set already

		$error = false;

		//detect media type of uploaded file here and then upload it accordingly also check if the media type uploaded and the gallery type matches or not
		//let us build our response for javascript
		//if we are uploading to a gallery, check for type
		//since we will be allowin g upload without gallery too, It is required to make sure $gallery is present or not

		if ( $gallery && !mpp_is_mixed_gallery( $gallery ) && $media_type !== $gallery->type ) {
			//if we are uploading to a gallery and It is not a mixed gallery, the media type must match the gallery type
			wp_send_json_error( array(
				'message' => sprintf( __( 'This file type is not allowed in current gallery. Only <strong>%s</strong> files are allowed!', 'mediapress' ), mpp_get_allowed_file_extensions_as_string( $gallery->type ) )
			) );
		}


		//if we are here, all is well :)

		if ( !mpp_user_can_upload( $component, $component_id, $gallery ) ) {

			wp_send_json_error( array( 'message' => __( "You don't have sufficient permissions to upload.", 'mediapress' ) ) );
		}

		//if we are here, we have checked for all the basic errors, so let us just upload now


		$uploaded = $uploader->upload( $file, array( 'file_id' => $file_id, 'gallery_id' => $gallery_id, 'component' => $component, 'component_id' => $component_id ) );

		//upload was succesfull?
		if ( !isset( $uploaded['error'] ) ) {

			//file was uploaded successfully
			$title = $_FILES[$file_id]['name'];

			$title_parts = pathinfo( $title );
			$title = trim( substr( $title, 0, -( 1 + strlen( $title_parts['extension'] ) ) ) );

			$url = $uploaded['url'];
			$type = $uploaded['type'];
			$file = $uploaded['file'];


			//$title = isset( $_POST['media_title'] ) ? $_POST['media_title'] : '';

			$content = isset( $_POST['media_description'] ) ? $_POST['media_description'] : '';

			$meta = $uploader->get_meta( $uploaded );


			$title_desc = $this->get_title_desc_from_meta( $type, $meta );

			if ( !empty( $title_desc ) ) {

				if ( empty( $title ) && !empty( $title_desc['title'] ) )
					$title = $title_desc['title'];

				if ( empty( $content ) && !empty( $title_desc['content'] ) )
					$content = $title_desc['content'];
			}



			$status = isset( $_POST['media_status'] ) ? $_POST['media_status'] : '';

			if ( empty( $status ) && $gallery )
				$status = $gallery->status; //inherit from parent,gallery must have an status

				
//we may need some more enhancements here
			if ( !$status )
				$status = mpp_get_default_status();

			//   print_r($upload_info);
			$is_orphan = 0;
			//Any media uploaded via activity is marked as orphan( Not associated with the mediapress unless the activity to which it was attached is actually created, check core/activity/actions.php to see how the orphaned media is adopted by the activity :) )
			if ( $context == 'activity' )
				$is_orphan = 1; //by default mark all uploaded media via activity as orphan


			$media_data = array(
				'title' => $title,
				'description' => $content,
				'gallery_id' => $gallery_id,
				'user_id' => get_current_user_id(),
				'is_remote' => false,
				'type' => $media_type,
				'mime_type' => $type,
				'src' => $file,
				'url' => $url,
				'status' => $status,
				'comment_status' => 'open',
				'storage_method' => mpp_get_storage_method(),
				'component_id' => $component_id,
				'component' => $component,
				'context' => $context,
				'is_orphan' => $is_orphan,
			);

			$id = mpp_add_media(
					$media_data
			);


			//should we update and resize images here?
			//
			mpp_gallery_increment_media_count( $gallery_id );

			$attachment = mpp_media_to_json( $id );
			//$attachment['data']['type_id'] = mpp_get_type_term_id( $gallery->type );
			echo json_encode( array(
				'success' => true,
				'data' => $attachment,
			) );
			//wp_send_json_success( array('name'=>'what') );
			exit( 0 );
		}else {


			wp_send_json_error( array( 'message' => $uploaded['error'] ) );
		}
	}

	public function cover_upload () {
		
		check_ajax_referer( 'mpp_add_media' ); //check for the referrer

		$response = array();

		$file = $_FILES;

		$file_id = '_mpp_file'; //key name in the files array
		//find the components we are trying to add for
		$component = $component_id = 0;

		$context = 'cover';

		$gallery_id = absint( $_POST['mpp-gallery-id'] );
		$parent_id = absint( $_POST['mpp-parent-id'] );
		$parent_type = isset( $_POST['mpp-parent-type'] ) ? trim( $_POST['mpp-parent-type'] ) : 'gallery'; //default upload to gallery cover 
		
		if ( ! $gallery_id || ! $parent_id ) {
			return;
		}

		$gallery = mpp_get_gallery( $gallery_id );

		$component    = $gallery->component;
		$component_id = $gallery->component_id;

		//get the uploader
		$uploader = mpp_get_storage_manager(); //should we pass the component?
		//setup for component
		//$uploader->setup_for( $component, $component_id );

		//check if the server can handle the upload?
		if ( ! $uploader->can_handle() ) {

			wp_send_json_error( array(
				'message' => __( 'Server can not handle this much amount of data. Please upload a smaller file or ask your server administrator to change the settings.', 'mediapress' )
			) );
		}

		$media_type = mpp_get_media_type_from_extension( mpp_get_file_extension( $file[ $file_id ]['name'] ) );

		//cover is always a photo,
		if ( $media_type != 'photo' ) {

			wp_send_json( array(
				'message' => sprintf( __( 'Please upload a photo. Only <strong>%s</strong> files are allowed!', 'mediapress' ), mpp_get_allowed_file_extensions_as_string( $media_type ) )
			) );
		}

		$error = false;

		//if we are here, all is well :)

		if ( ! mpp_user_can_upload( $component, $component_id, $gallery ) ) {

			wp_send_json_error( array( 'message' => __( "You don't have sufficient permissions to upload.", 'mediapress' ) ) );
		}

		//if we are here, we have checked for all the basic errors, so let us just upload now

		$uploaded = $uploader->upload( $file, array( 'file_id' => $file_id, 'gallery_id' => $gallery_id, 'component' => $component, 'component_id' => $component_id, 'is_cover' => 1 ) );

		//upload was succesfull?
		if ( ! isset( $uploaded['error'] ) ) {

			//file was uploaded successfully
			$title = $_FILES[ $file_id ]['name'];

			$title_parts = pathinfo( $title );
			$title = trim( substr( $title, 0, -( 1 + strlen( $title_parts['extension'] ) ) ) );

			$url  = $uploaded['url'];
			$type = $uploaded['type'];
			$file = $uploaded['file'];


			//$title = isset( $_POST['media_title'] ) ? $_POST['media_title'] : '';

			$content = isset( $_POST['media_description'] ) ? $_POST['media_description'] : '';

			$meta = $uploader->get_meta( $uploaded );

			$title_desc = $this->get_title_desc_from_meta( $type, $meta );

			if ( ! empty( $title_desc ) ) {

				if ( empty( $title ) && ! empty( $title_desc['title'] ) ) {
					$title = $title_desc['title'];
				}

				if ( empty( $content ) && ! empty( $title_desc['content'] ) ) {
					$content = $title_desc['content'];
				}
			}

			$status = isset( $_POST['media_status'] ) ? $_POST['media_status'] : '';

			if ( empty( $status ) && $gallery ) {
				$status = $gallery->status; //inherit from parent,gallery must have an status
			}	
				
			//we may need some more enhancements here
			if ( ! $status ) {
				$status = mpp_get_default_status();
			}	
			//   print_r($upload_info);
			$is_orphan = 0;


			$media_data = array(
				'title'				=> $title,
				'description'		=> $content,
				'gallery_id'		=> $parent_id,
				'user_id'			=> get_current_user_id(),
				'is_remote'			=> false,
				'type'				=> $media_type,
				'mime_type'			=> $type,
				'src'				=> $file,
				'url'				=> $url,
				'status'			=> $status,
				'comment_status'	=> 'open',
				'storage_method'	=> mpp_get_storage_method(),
				'component_id'		=> $component_id,
				'component'			=> $component,
				'context'			=> $context,
				'is_orphan'			=> $is_orphan,
				'is_cover'			=> true
			);
			//cover shuld never be recorded as activity
			add_filter( 'mpp_do_not_record_add_media_activity', '__return_true' );
			
			$id = mpp_add_media( $media_data );
			
			if ( $parent_type =='gallery' ) {
				$old_cover = mpp_get_gallery_cover_id( $parent_id );

			} else {
				$old_cover = mpp_get_media_cover_id( $parent_id );
			}
			
			if ( $gallery->type == 'photo' ) {
				mpp_gallery_increment_media_count( $gallery_id );
			} else {
				//mark it as non gallery media
				mpp_delete_media_meta( $id, '_mpp_is_mpp_media' );

				if ( $old_cover ) {
					mpp_delete_media( $old_cover );
				}
			}

			mpp_update_media_cover_id( $parent_id, $id );

			$attachment = mpp_media_to_json( $id );
			//$attachment['data']['type_id'] = mpp_get_type_term_id( $gallery->type );
			echo json_encode( array(
				'success'	=> true,
				'data'		=> $attachment,
			) );
			//wp_send_json_success( array('name'=>'what') );
			exit( 0 );
			
		} else {


			echo json_encode( array( 'error' => 1, 'message' => $uploaded['error'] ) );
			exit( 0 );
		}
	}

	/**
	 * Utility method to extract title/deesc from meta
	 * 
	 * @param type $type
	 * @param type $meta
	 * @return array( 'title'=> Extracted title, 'content'=>  Extracted content )
	 */
	public function get_title_desc_from_meta ( $type, $meta ) {


		$title = $content = '';
		//match mime type
		if ( preg_match( '#^audio#', $type ) ) {


			if ( ! empty( $meta['title'] ) ) {
				$title = $meta['title'];
			}
			// $content = '';

			if ( ! empty( $title ) ) {

				if ( ! empty( $meta['album'] ) && ! empty( $meta['artist'] ) ) {
					/* translators: 1: audio track title, 2: album title, 3: artist name */
					$content .= sprintf( __( '"%1$s" from %2$s by %3$s.', 'mediapress' ), $title, $meta['album'], $meta['artist'] );
				} elseif ( ! empty( $meta['album'] ) ) {
					/* translators: 1: audio track title, 2: album title */
					$content .= sprintf( __( '"%1$s" from %2$s.', 'mediapress' ), $title, $meta['album'] );
				} elseif ( !empty( $meta['artist'] ) ) {
					/* translators: 1: audio track title, 2: artist name */
					$content .= sprintf( __( '"%1$s" by %2$s.', 'mediapress' ), $title, $meta['artist'] );
				} else {
					$content .= sprintf( __( '"%s".' ), $title );
				}
				
			} elseif ( ! empty( $meta['album'] ) ) {

				if ( ! empty( $meta['artist'] ) ) {
					/* translators: 1: audio album title, 2: artist name */
					$content .= sprintf( __( '%1$s by %2$s.', 'mediapress' ), $meta['album'], $meta['artist'] );
				} else {
					$content .= $meta['album'] . '.';
				}
				
			} elseif ( ! empty( $meta['artist'] ) ) {

				$content .= $meta['artist'] . '.';
			}

			if ( ! empty( $meta['year'] ) ) {
				$content .= ' ' . sprintf( __( 'Released: %d.', 'mediapress' ), $meta['year'] );
			}

			if ( ! empty( $meta['track_number'] ) ) {
				
				$track_number = explode( '/', $meta['track_number'] );
				
				if ( isset( $track_number[1] ) ) {
					$content .= ' ' . sprintf( __( 'Track %1$s of %2$s.', 'mediapress' ), number_format_i18n( $track_number[0] ), number_format_i18n( $track_number[1] ) );
				} else {
					$content .= ' ' . sprintf( __( 'Track %1$s.', 'mediapress' ), number_format_i18n( $track_number[0] ) );
				}
				
			}

			if ( ! empty( $meta['genre'] ) ) {
				$content .= ' ' . sprintf( __( 'Genre: %s.', 'mediapress' ), $meta['genre'] );
			}

			// use image exif/iptc data for title and caption defaults if possible
		} elseif ( $meta ) {
			
			if ( trim( $meta['title'] ) && ! is_numeric( sanitize_title( $meta['title'] ) ) ) {
				$title = $meta['title'];
			}
			
			if ( trim( $meta['caption'] ) ) {
				$content = $meta['caption'];
			}
		}

		return compact( $title, $content );
	}

	public function publish_gallery_media () {

		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'publish' ) ) {
			//should we return or show error?
			return;
		}

		$gallery_id = absint( $_POST['gallery_id'] );


		if ( ! mpp_gallery_has_unpublished_media( $gallery_id ) ) {
			wp_send_json( array( 'message' => __( 'No media to publish.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}

		//check if user has permission
		if ( ! mpp_user_can_publish_gallery_activity( $gallery_id ) ) {
			wp_send_json( array( 'message' => __( "You don't have sufficient permission.", 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		$media_ids = mpp_gallery_get_unpublished_media( $gallery_id );

		$media_count = count( $media_ids );

		$gallery = mpp_get_gallery( $gallery_id );

		$type = $gallery->type;

		$type_name = _n( $type, $type . 's', $media_count );
		$user_link = mpp_get_user_link( get_current_user_id() );

		$gallery_url = mpp_get_gallery_permalink( $gallery );

		$gallery_link = '<a href="' . esc_url( $gallery_url ) . '" title="' . esc_attr( $gallery->title ) . '">'. mpp_get_gallery_title( $gallery ). '</a>';
//has media, has permission, so just publish now
		//
		
		$activity_id = mpp_gallery_record_activity( array(
			'gallery_id'	=> $gallery_id,
			'media_ids'		=> $media_ids,
			'type'			=> 'media_publish',
			'action'		=> sprintf( __( '%s shared %d %s to %s ', 'mediapress' ), $user_link, $media_count, $type_name, $gallery_link ),
			'content'		=> '',
		) );


		if ( $activity_id ) {

			mpp_gallery_delete_unpublished_media( $gallery_id );

			wp_send_json( array( 'message' => __( "Published to activity successfully.", 'mediapress' ), 'success' => 1 ) );
			exit( 0 );
			
		} else {
			
			wp_send_json( array( 'message' => __( "There was a problem. Please try again later.", 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
			
		}

		//we are good, let us check if there are actually unpublished media
		//$unpublished_media = 
		//get unpublished media ids
		//call _mpp_record_activity
		//how about success/failure

		exit( 0 );
	}

	public function hide_unpublished_media () {
		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'delete-unpublished' ) ) {
			//should we return or show error?
			return;
		}

		$gallery_id = absint( $_POST['gallery_id'] );

		if ( ! mpp_gallery_has_unpublished_media( $gallery_id ) ) {
			wp_send_json( array( 'message' => __( 'Nothing to hide.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}

		//check if user has permission
		if ( ! mpp_user_can_publish_gallery_activity( $gallery_id ) ) {
			wp_send_json( array( 'message' => __( "You don't have sufficient permission.", 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}

		mpp_gallery_delete_unpublished_media( $gallery_id );

		wp_send_json( array( 'message' => __( "Successfully hidden!", 'mediapress' ), 'success' => 1 ) );
		exit( 0 );
	}

	public function delete_media() {
		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mpp-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}

		$media_id = absint( $_POST['media_id'] );
		$media = mpp_get_media( $media_id );
		
		if ( ! $media ) {
			wp_send_json( array( 'message' => __( 'Invalid Media.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );	
		}
		
		if ( ! mpp_is_valid_media( $media->id ) ) {
			wp_send_json( array( 'message' => __( 'Invalid Media.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		if ( ! mpp_user_can_delete_media( $media_id ) ) {
			wp_send_json( array( 'message' => __( 'Unauthorized action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		mpp_delete_media( $media_id );
		
		wp_send_json( array( 'message' => __( 'Deleted.', 'mediapress' ), 'success' => 1 ) );
		exit(0);
		
	}
	
	public function reorder_media() {
		
		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mpp-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		//should we check for the permission? not here
	
		$media_ids = $_POST['mpp-media-ids'];//array
	
		$media_ids = wp_parse_id_list( $media_ids );
		$media_ids = array_filter( $media_ids );
		$order = count( $media_ids );
		
		
		foreach( $media_ids as $media_id ) {
		
			if ( ! mpp_user_can_edit_media( $media_id ) ) {
				//unauthorized attemt 

				wp_send_json( array( 'message'=> __( "You don't have permission to update!", 'mediapress' ), 'error' => 1 ) );
				exit( 0 );
			
			}
					//if we are here, let us update the order
			mpp_update_media_order( $media_id, $order );
			$order--;
		
		}
	
		if ( $media_id ) {
			//mark the gallery assorted, we use it in MPP_Media_query to see what should be the default order
			$media = mpp_get_media( $media_id );
			//mark the gallery as sorted
			mpp_mark_gallery_sorted( $media->gallery_id );
		}
	
		wp_send_json( array( 'message'=> __( "Updated", 'mediapress' ), 'success' => 1 ) );
		exit( 0 );
	}
	
	public function bulk_update_media() {
		
		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mpp-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		if ( ! $_POST['mpp-editing-media-ids'] ) {
			return;
		}
		
		$gallery_id = absint( $_POST['gallery_id'] );
		$gallery = mpp_get_gallery( $gallery_id );
		
		if ( ! $gallery_id || ! $gallery ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		$message = '';
	
		$media_ids = $_POST['mpp-editing-media-ids'];
		$media_ids = wp_parse_id_list( $media_ids );
		$media_ids = array_filter( $media_ids );
	
		$bulk_action = false;
	
		if ( ! empty( $_POST['mpp-edit-media-bulk-action'] ) ) {
			$bulk_action = $_POST['mpp-edit-media-bulk-action'];//we are leaving this to allow future enhancements with other bulk action and not restricting to delete only
		}
	
		foreach ( $media_ids as $media_id ) {
			//check what action should we take?
			//1. check if $bulk_action is set? then we may ned to check for deletion

			//otherwise, just update the details :)
			if ( $bulk_action == 'delete' && ! empty( $_POST['mpp-delete-media-check'][ $media_id ] ) ) {

				//delete and continue
				//check if current user can delete?

				if ( ! mpp_user_can_delete_media( $media_id ) ) {
					//if the user is unable to delete media, should we just continue the loop or breakout and redirect back with error?
					//I am in favour of showing error
					$success = 0;
					
					wp_send_json( array( 'message' => __( 'Not allowed to delete!', 'mediapress' ), 'error' => 1 ) );
					exit( 0 );
				}

				//if we are here, let us delete the media
				mpp_delete_media( $media_id );

				$message = __( 'Deleted successfully!', 'mediapress' ); //it will do for each media, that is not  good thing btw
				$success = 1;
				continue;
			}
			//since we already handled delete for the media checked above, 
			//we don't want to do it for the other media hoping that the user was performing bulk delete and not updating the media info
			if ( $bulk_action == 'delete' ) {
				continue;
			}

			//is it media update
			$media_title = $_POST['mpp-media-title'][ $media_id ];

			$media_description = $_POST['mpp-media-description'][ $media_id ];

			$status = $_POST['mpp-media-status'][ $media_id ];
			
			//if we are here, It must not be a bulk action
			$media_info = array(
			   'id'			=> $media_id,	
			   'title'			=> $media_title,
			   'description'	=> $media_description,
			  // 'type'		=> $type,
			   'status'		=> $status,

			);

			mpp_update_media( $media_info );

		}

		if ( ! $bulk_action ) {
			$message = __( 'Updated!', 'mediapress' ) ;
		} elseif ( ! $message ) {
			$message = __( 'Please select media to apply bulk actions.', 'mediapress' ) ;
		}
		
		mediapress()->current_gallery = $gallery;
		
		mediapress()->the_media_query = new MPP_Media_Query( array( 
			'gallery_id'	=> $gallery_id, 
			'per_page'		=> -1, 
			'nopaging'		=> true 
		) );
		
		global $post;
		
		$bkp_post = $post;
		
		ob_start();
		require_once $this->template_dir . 'gallery/edit-media.php';
		
		$contents = ob_get_clean();
		$post = $bkp_post;
		//remember to add content too
		wp_send_json( array( 'message' => $message, 'success' => 1, 'contents' => $contents ) );
		
		exit( 0 );
	}
	
	public function delete_gallery_cover() {
	
		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mpp-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		$gallery = mpp_get_gallery( absint( $_REQUEST['gallery_id'] ) );
		
		if ( ! $gallery ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
	
		//we may want to allow passing of component from the form in future!
		if ( ! mpp_user_can_delete_gallery( $gallery ) ) {
		
			wp_send_json( array( 'message' => __( "You don't have permission to delete this cover!", 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		
		}
		
		//we always need to delete this
		$cover_id = mpp_get_gallery_cover_id( $gallery->id );
		mpp_delete_gallery_cover_id( $gallery->id );

		mpp_delete_media( $cover_id );

		wp_send_json( array( 'message' => __( "Cover deleted", 'mediapress' ), 'success' => 1, 'cover'=> mpp_get_gallery_cover_src( 'thumbnail', $gallery->id )  ) );
		exit( 0 );
	
	}
	
	public function update_gallery_details() {
		
		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mpp-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		$gallery_id = absint( $_POST['mpp-gallery-id'] );
	
		if ( ! $gallery_id ) {
			return;
		}	
	
		//check for permission
		//we may want to allow passing of component from the form in future!
		if ( ! mpp_user_can_edit_gallery( $gallery_id ) ) {

			wp_send_json( array( 'message' => __( "You don't have permission to update.", 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
			
		
		$description = $_POST['mpp-gallery-description'];
	
		$errors = array();
	
		
		//give opportunity to other plugins to add their own validation errors
		$validation_errors = apply_filters( 'mpp-edit-gallery-field-validation', $errors, $_POST );
	
		if ( ! empty( $validation_errors ) ) {
		//let us add the validation error and return back to the earlier page
		
			$message = join( '\r\n', $validation_errors );
			
			wp_send_json( array( 'message' => $message, 'error' => 1 ) );

			exit( 0 );
		
		}
		
	//let us create gallery
	
		$gallery_id = mpp_update_gallery( array(

				'description'	=> $description,
				'id'			=> $gallery_id,

		));
	

		if ( ! $gallery_id ) {

			wp_send_json( array( 'message' => __( 'Unable to update gallery!', 'mediapress' ), 'error' => 1 ) );

			exit( 0 );

		}

		wp_send_json( array( 'message' => __( 'Gallery updated successfully!', 'mediapress' ), 'success' => 1 ) );

		exit( 0 );
	
	
	}
	
	public function reload_bulk_edit() {
		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mpp-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		$gallery_id = absint( $_POST['gallery_id'] );
		$gallery = mpp_get_gallery( $gallery_id );
		
		if ( ! $gallery_id || ! $gallery ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		
		if (  ! mpp_user_can_edit_gallery( $gallery ) ) {
			wp_send_json( array( 'message' => __( "You don't have permission to update gallery.", 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		//show the form
		mediapress()->current_gallery = $gallery;
		
		mediapress()->the_media_query = new MPP_Media_Query( array( 
			'gallery_id'	=> $gallery_id, 
			'per_page'		=> -1, 
			'nopaging'		=> true 
		) );
		
		global $post;
		
		$bkp_post = $post;
		
		ob_start();
		require_once $this->template_dir . 'gallery/edit-media.php';
		
		$contents = ob_get_clean();
		
		$post = $bkp_post;
		
		wp_send_json( array( 'message' => __( 'Updated.', 'mediapress' ), 'success' => 1, 'contents'=> $contents ) );
		
		exit( 0 );
		
	}
	
	public function reload_add_media() {
		//verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mpp-manage-gallery' ) ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		$gallery_id = absint( $_POST['gallery_id'] );
		$gallery = mpp_get_gallery( $gallery_id );
		
		if ( ! $gallery_id || ! $gallery ) {
			wp_send_json( array( 'message' => __( 'Invalid action.', 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		
		if ( ! mpp_user_can_upload( $gallery->component, $gallery->component_id ) ) {
			wp_send_json( array( 'message' => __( "You don't have permission to upload.", 'mediapress' ), 'error' => 1 ) );
			exit( 0 );
		}
		
		//show the form
		mediapress()->current_gallery = $gallery;
		mediapress()->the_media_query = new MPP_Media_Query( array( 'gallery_id' => $gallery_id, 'per_page' => -1, 'nopaging' => true ) );
		
		global $post;
		
		$bkp_post = $post;
		
		ob_start();
		require_once $this->template_dir . 'gallery/add-media.php';
		
		$contents = ob_get_clean();
		
		$post = $bkp_post;
		
		wp_send_json( array( 'message' => __( "Updated.", 'mediapress' ), 'success' => 1, 'contents'=> $contents ) );
		exit( 0 );
		
	}
}

//initialize
MPP_Ajax_Helper::get_instance();
