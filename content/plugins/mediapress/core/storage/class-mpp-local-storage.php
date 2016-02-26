<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Handles the local storage on the server
 * 
 * This allows to store the files on the same server where WordPress is installed 
 * 
 */
class MPP_Local_Storage extends MPP_Storage_Manager {

	private static $instance;
	
	private $upload_errors = array();

	private function __construct() {


		// $this->setup_upload_errors();
	}

	/**
	 * 
	 * @return MPP_Local_Storage
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/**
	 * Get the source url for the given size
	 * 
	 * @param string $type names of the various image sizes(thumb, mid,etc)
	 * @param int $id ID of the media
	 * 
	 * @return string source( absoulute url) of the media
	 * 
	 */
	public function get_src( $type = null, $id = null ) {
		//ID must be given
		if ( ! $id ) {
			return '';
		}
		
		$url = wp_get_attachment_url( $id );

		if ( ! $type ) {
			return $url; //original media url
		}
		
		$meta = wp_get_attachment_metadata( $id );
		
		//if size info is not available, return original src
		if ( empty( $meta['sizes'][ $type ] ) || empty( $meta['sizes'][ $type ]['file'] ) ) {
			return $url; //return original size
		}
		
		$base_url = str_replace( wp_basename( $url ), '', $url );

		$src = $base_url . $meta['sizes'][ $type ]['file'];

		return $src;
	}

	/**
	 * Get the absolute path to a file ( file system path like /home/xyz/public_html/wp-content/uploads/mediapress/members/1/xyz)
	 * 
	 * @param type $type
	 * @param type $id
	 * @return string
	 * 
	 */
	public function get_path( $type = null, $id = null ) {
		//ID must be given
		if ( ! $id ) {
			return '';
		}

		$upload_info = wp_upload_dir();
		
		$base_dir	 = $upload_info['basedir'];

		$meta = wp_get_attachment_metadata( $id );

		$file = $meta['file'];

		if ( ! $type ) {
			return path_join( $base_dir, $file );
		}
		
		if ( empty( $meta['sizes'][ $type ]['file'] ) )
			return '';
		
		$rel_dir_path = str_replace( wp_basename( $file ), '', $file );

		$dir_path = path_join( $base_dir, $rel_dir_path );

		$abs_path = path_join( $dir_path, $meta['sizes'][ $type ]['file'] );

		return $abs_path;
	}
	
	/**
	 * Uploads a file
	 * 
	 * @param type $file, name of the file field in html .e.g _mpp_file in <input type='file' name='_mpp_file' />
	 * @param array $args{
	 *	
	 *	@type string $component
	 *	@type int $component_id
	 *	@type int $gallery_id
	 * 
	 * }
	 * 
	 * @return boolean
	 */
	public function upload( $file, $args ) {
		
		extract( $args );

		if ( empty( $file_id ) ) {
			return false;
		}

		//setup error
		$this->setup_upload_errors( $component_id );
		
		$ms_flag = false;
		
		if( is_multisite() && has_filter( 'upload_mimes', 'check_upload_mimes' ) ) {
			remove_filter( 'upload_mimes', 'check_upload_mimes' );
			$ms_flag = true;
		}
		
		//$_FILE['_mpp_file']
		$file	 = $file[ $file_id ];

		$unique_filename_callback = null;


		//include from wp-admin dir for media processing
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		if ( ! function_exists( 'mpp_handle_upload_error' ) ) {

			function mpp_handle_upload_error( $file, $message ) {

				return array( 'error' => $message );
			}

		}

		$upload_error_handler = 'mpp_handle_upload_error';
		
		$file = apply_filters( 'mpp_upload_prefilter', $file );
		
		// All tests are on by default. Most can be turned off by $overrides[{test_name}] = false;
		$test_form	 = true;
		$test_size	 = true;
		$test_upload = true;

		// If you override this, you must provide $ext and $type!!!!
		$test_type	 = true;
		$mimes		 = false;

		// Install user overrides. Did we mention that this voids your warranty?
		if ( ! empty( $overrides ) && is_array( $overrides ) ) {
			extract( $overrides, EXTR_OVERWRITE );
		}


		// A successful upload will pass this test. It makes no sense to override this one.
		if ( $file[ 'error' ] > 0 ) {
			return call_user_func( $upload_error_handler, $file, $this->upload_errors[ $file[ 'error' ] ] );
		}
		// A non-empty file will pass this test.
		if ( $test_size && !($file[ 'size' ] > 0 ) ) {
		
			if ( is_multisite() ) {
				$error_msg	 = _x( 'File is empty. Please upload something more substantial.', 'upload error message', 'mediapress' );
			} else {
				$error_msg	 = _x( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'upload error message', 'mediapress' );
			}
			
			return call_user_func( $upload_error_handler, $file, $error_msg );
		}

		// A properly uploaded file will pass this test. There should be no reason to override this one.
		if ( $test_upload && !@ is_uploaded_file( $file[ 'tmp_name' ] ) ) {
			return call_user_func( $upload_error_handler, $file, _x( 'Specified file failed upload test.', 'upload error message', 'mediapress' ) );
		}

		// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
		if ( $test_type ) {
			$wp_filetype = wp_check_filetype_and_ext( $file[ 'tmp_name' ], $file[ 'name' ], $mimes );

			extract( $wp_filetype );

			// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
			if ( $proper_filename ) {
				$file[ 'name' ] = $proper_filename;
			}
			
			if ( ( ! $type || ! $ext ) && ! current_user_can( 'unfiltered_upload' ) ) {
				return call_user_func( $upload_error_handler, $file, _x( 'Sorry, this file type is not permitted for security reasons.', 'upload error message', 'mediapress' ) );
			}
			
			if ( ! $ext ) {
				$ext = ltrim( strrchr( $file['name'], '.' ), '.' );
			}
			
			if ( ! $type ) {
				$type = $file['type'];
			}
			
		} else {
			$type = '';
		}

		// A writable uploads dir will pass this test. Again, there's no point overriding this one.
		if ( ! ( ( $uploads = $this->get_upload_dir( $args ) ) && false === $uploads['error'] ) ) {

			return call_user_func( $upload_error_handler, $file, $uploads['error'] );
		}

		$filename = wp_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

		// Move the file to the uploads dir
		$new_file = $uploads['path'] . "/$filename";

		if ( ! file_exists( $uploads['path'] ) ) {
			wp_mkdir_p( $uploads['path'] );
		}

		if ( false === @ move_uploaded_file( $file['tmp_name'], $new_file ) ) {
			
			if ( 0 === strpos( $uploads['basedir'], ABSPATH ) ) {
				$error_path	 = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
			} else {
				$error_path	 = basename( $uploads['basedir'] ) . $uploads['subdir'];
			}
			
			return $upload_error_handler( $file, sprintf( _x( 'The uploaded file could not be moved to %s.', 'upload error message', 'mediapress' ), $error_path ) );
		}

		// Set correct file permissions
		$stat	 = stat( dirname( $new_file ) );
		$perms	 = $stat['mode'] & 0000666;
		@ chmod( $new_file, $perms );

		// Compute the URL
		$url = $uploads['url'] . "/$filename";

		$this->invalidate_transient( $component, $component_id );
		//if required, fix rotation
		$this->fix_rotation( $new_file );
		
		return apply_filters( 'mpp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ), 'upload' );
	}
	
	/**
	 * save binary data
	 * 
	 * @param type $name
	 * @param type $bits
	 * @param type $args
	 * @return type
	 */
	public function upload_bits( $name, $bits, $upload ) {

		if ( empty( $name ) ) {
			return array( 'error' => _x( 'Empty filename', 'upload error message', 'mediapress' ) );
		}
		
		$wp_filetype = wp_check_filetype( $name );

		if ( ! $wp_filetype['ext'] && ! current_user_can( 'unfiltered_upload' ) ) {
			return array( 'error' => _x( 'Invalid file type', 'upload error message', 'mediapress' ) );
		}
				
		if( ! $upload['path'] ) {
			return false;
		}

		$upload_bits_error = apply_filters( 'mpp_upload_bits', array( 'name' => $name, 'bits' => $bits, 'path' => $upload['path'] ) );

		if ( ! is_array( $upload_bits_error ) ) {
			$upload['error'] = $upload_bits_error;
			return $upload;
		}

		$filename = wp_unique_filename( $upload['path'], $name );

		$new_file = trailingslashit( $upload['path'] ) . "$filename";
		
		if ( ! wp_mkdir_p( dirname( $new_file ) ) ) {
			
			$message = sprintf( _x( 'Unable to create directory %s. Is its parent directory writable by the server?', 'upload error message', 'mediapress' ), dirname( $new_file  ) );
			
			return array( 'error' => $message );
		}

		$ifp = @ fopen( $new_file, 'wb' );
		if ( ! $ifp ) {
			return array( 'error' => sprintf( _x( 'Could not write file %s', 'upload error message', 'mediapress' ), $new_file ) );
		}
		
		@fwrite( $ifp, $bits );
		
		fclose( $ifp );
		clearstatcache();

		// Set correct file permissions
		$stat	 = @ stat( dirname( $new_file ) );
		$perms	 = $stat['mode'] & 0007777;
		$perms	 = $perms & 0000666;
		@ chmod( $new_file, $perms );
		clearstatcache();

		// Compute the URL
		$url = $upload['url'] . "/$filename";
		
		$this->fix_rotation( $new_file );
		
		return array( 'file' => $new_file, 'url' => $url, 'error' => false );
	}	
	/**
	 * Extract meta from uploaded data 
	 * 
	 * @param type $uploaded
	 * @return type
	 */
	public function get_meta( $uploaded ) {

		$meta = array();

		$url	 = $uploaded['url'];
		$type	 = $uploaded['type'];
		$file	 = $uploaded['file'];


		//match mime type
		if ( preg_match( '#^audio#', $type ) ) {
			$meta = wp_read_audio_metadata( $file );
			// use image exif/iptc data for title and caption defaults if possible
		} else {
			$meta = @wp_read_image_metadata( $file );
		}

		return $meta;
	}

	/**
	 * Generate meta data for the media
	 *
	 * @since 1.0.0
	 *	
	 * @access  private
	 * @param int $attachment_id Media ID  to process.
	 * @param string $file Filepath of the Attached image.
	 * 
	 * @return mixed Metadata for attachment.
	 */
	public function generate_metadata( $attachment_id, $file ) {
		
		$attachment = get_post( $attachment_id );
		
		$mime_type = get_post_mime_type( $attachment );
		
		$metadata	 = array();
		
		if ( preg_match( '!^image/!', $mime_type ) && file_is_displayable_image( $file ) ) {
			
			$imagesize			 = getimagesize( $file );
			
			$metadata['width']	 = $imagesize[ 0 ];
			$metadata['height']	 = $imagesize[ 1 ];

			// Make the file path relative to the upload dir
			$metadata['file'] = _wp_relative_upload_path( $file );

			//get the registered media sizes
			$sizes = mpp_get_media_sizes();


			$sizes = apply_filters( 'mpp_intermediate_image_sizes', $sizes, $attachment_id );

			if ( $sizes ) {
			
				$editor = wp_get_image_editor( $file );
				
				if ( ! is_wp_error( $editor ) ) {
					$metadata['sizes'] = $editor->multi_resize( $sizes );
				}
				
			} else {
				
				$metadata['sizes'] = array();
			}

			// fetch additional metadata from exif/iptc
			$image_meta				 = wp_read_image_metadata( $file );
			
			if ( $image_meta ) {
				$metadata['image_meta']	 = $image_meta;
			}
			
		} elseif ( preg_match( '#^video/#', $mime_type ) ) {
			
			$metadata = wp_read_video_metadata( $file );
			
		} elseif ( preg_match( '#^audio/#',  $mime_type) ) {
			
			$metadata = wp_read_audio_metadata( $file );
			
		}
		
		$dir_path = trailingslashit( dirname( $file ) ) .'covers';
		$url = wp_get_attachment_url( $attachment_id );
		$base_url = str_replace( wp_basename( $url ), '', $url );
		
		//processing for audio/video cover
		if ( ! empty( $metadata['image']['data'] ) ) {
			
			$ext = '.jpg';
			switch ( $metadata['image']['mime'] ) {
				case 'image/gif':
					$ext = '.gif';
					break;
				case 'image/png':
					$ext = '.png';
					break;
			}
			$basename	 = str_replace( '.', '-', basename( $file ) ) . '-image' . $ext;
			$uploaded	 = $this->upload_bits( $basename, $metadata['image']['data'] , array( 'path'=> $dir_path, 'url' => $base_url ) );
			
			if ( false === $uploaded[ 'error' ] ) {
				$attachment			 = array(
					'post_mime_type' => $metadata['image']['mime'],
					'post_type'		 => 'attachment',
					'post_content'	 => '',
				);
				$sub_attachment_id	 = wp_insert_attachment( $attachment, $uploaded['file'] );
				$attach_data		 = $this->generate_metadata( $sub_attachment_id, $uploaded['file'] );
				
				wp_update_attachment_metadata( $sub_attachment_id, $attach_data );
				//if the option is set to set post thumbnail
				if( mpp_get_option( 'set_post_thumbnail' ) )  {
					mpp_update_media_meta( $attachment_id, '_thumbnail_id', $sub_attachment_id );
				}
				//set the cover id
				mpp_update_media_cover_id( $attachment_id, $sub_attachment_id );
			}
		}

		// remove the blob of binary data from the array
		if ( isset( $metadata['image']['data'] ) ) {
			unset( $metadata['image']['data'] );
		}

		return apply_filters( 'mpp_generate_metadata', $metadata, $attachment_id );
	}

	/**
	 * Delete all the files associated with a Media
	 * For lovcal storage, WordPress handles deleting, we simply invalidate the transiesnt 
	 * @global type $wpdb
	 * @param type $id
	 * @return boolean
	 */
	public function delete_media( $media_id ) {
		
		$media			 = mpp_get_media( $media_id );
		$this->invalidate_transient( $media->component, $media->component_id );
		return true;
	}
	/**
	 * Called after gallery deletion
	 * 
	 * @param type $gallery_id
	 * @return boolean
	 */
	public function delete_gallery( $gallery_id ) {
		
		$gallery = mpp_get_gallery( $gallery_id );
		
		$dir = $this->get_component_base_dir( $gallery->component, $gallery->component_id );
		
		$dir = untrailingslashit( wp_normalize_path( $dir ) ) . '/'.$gallery->id . '/' ;
		
		if( $dir ) {
			
			mpp_recursive_delete_dir( $dir );
		}
		
		$this->invalidate_transient( $gallery->component, $gallery->component_id );
		
		return true;
	}

	/**
	 * Calculate the Used space by a component
	 * 
	 * @see mpp_get_used_space
	 * 
	 * @access private do not call it directly, use mpp_get_used_space instead
	 * 
	 * @param type $component
	 * @param type $component_id
	 * @return int
	 */
	public function get_used_space( $component, $component_id ) {

		//let us check for the transient as space calculation is bad everytime
		$key = "mpp_space_used_by_{$component}_{$component_id}"; //transient key

		$used_space = get_transient( $key );
		
		if ( ! $used_space ) {

			$dir_name = trailingslashit( $this->get_component_base_dir( $component, $component_id ) ); //base gallery directory for owner

			if ( ! is_dir( $dir_name ) || ! is_readable( $dir_name ) ) {
				return 0; //we don't know the usage or no usage
			}

			$dir = dir( $dir_name );
			$size = 0;

			while ( $file = $dir->read() ) {

				if ( $file != '.' && $file != '..' ) {

					if ( is_dir( $dir_name . $file ) ) {
						$size += get_dirsize( $dir_name . $file );
					} else {
						$size += filesize( $dir_name . $file );
					}
				}
			}
			
			$dir->close();
			set_transient( $key, $size, DAY_IN_SECONDS );

			$used_space = $size;
		}

		$used_space = $used_space / 1024 / 1024;

		return $used_space;
	}


	public function get_errors() {
		
	}

	/**
	 * Server can handle upload?
	 * 
	 * @return boolean
	 */
	public function can_handle() {

		if ( $_FILES['_mpp_file']['size'] < wp_max_upload_size() ) {
			return true;
		}
		
		return false;
	}

	//******************************************************************************
	// Utility methods below
	// 
	//******************************************************************************

	/**
	 * Calculate the upload path for our files
	 * 
	 * It uses wp_upload_dir and appends our path to it, the returned result is similar to what wp_upload_dir provides
	 * 
	 * @since 1.0.0
	 * @see wp_upload_dir
	 * 
	 * @param array $args {
	 * 
	 * 	@type string $component the asssociated component for the media ( groups|members etc)
	 * 	
	 * 	@type int	 $component_id The associated component object id( group id or user id depending on the $component )
	 * 	
	 * 	@type int	 $gallery_id The parent gallery id
	 * 	
	 * 	@type boolean $is_cover Is it cover upload?	 if true, the appends /covers
	 * 							at the end of the path
	 * 
	 * }
	 * @return string
	 */
	public function get_upload_dir( $args ) {

		$default = array(
			'component'		 => '',
			'component_id'	 => 0,
			'gallery_id'	 => 0,
			'is_cover'		 => false,
		);

		$args = wp_parse_args( $args, $default );
		extract( $args );

		$uploads = wp_upload_dir();

		//if a component is not given or the component id is not given, do not alter the upload path
		if ( ! $component || ! $component_id ) {
			return $uploads;
		}

		$uploads['path'] = str_replace( $uploads['subdir'], '', $uploads['path'] );
		$uploads['url']	 = str_replace( $uploads['subdir'], '', $uploads['url'] );

		//now reset upload/sub dir, we have hardcoded mediapress for now, if you want it to be changed, please create a ticket
		$uploads['subdir'] = "/mediapress/{$component}/{$component_id}";

		//make folder like /mediapress/{groups|members}/{user_id or group_id}
		
		if ( $gallery_id ) {
			$uploads['subdir'] = $uploads['subdir'] . "/{$gallery_id}";
		}
			
		if ( $is_cover ) {
			$uploads['subdir'] = $uploads['subdir'] . '/covers';
		}
		
		$uploads['path']	= untrailingslashit( $uploads['path'] ) . $uploads['subdir'];
		$uploads['url']	= untrailingslashit( $uploads['url'] ) . $uploads['subdir'];

		return $uploads;
	}

	/**
	 * Get the path to the base dir of a component
	 * 
	 * @access private
	 * 
	 * @param type $component
	 * @param type $component_id
	 * @return type
	 */
	public function get_component_base_dir( $component, $component_id ){
		
		$uploads = $this->get_upload_dir( array('component' => $component, 'component_id' => $component_id ) );
		
		return $uploads['path'] ;
		
		
	}
	/**
	 * Possible upload errors
	 */
	public function setup_upload_errors( $component ) {

		$allowed_size = mpp_get_allowed_space( $component );

		$this->upload_errors = array(
			UPLOAD_ERR_OK			 => _x( 'Great! the file uploaded successfully!', 'upload error message', 'mediapress' ),
			UPLOAD_ERR_INI_SIZE		 => sprintf( _x( 'Your file size was bigger than the maximum allowed file size of: %s', 'upload error message', 'mediapress' ), $allowed_size ),
			UPLOAD_ERR_FORM_SIZE	 => sprintf( _x( 'Your file was bigger than the maximum allowed file size of: %s', 'upload error message', 'mediapress' ), $allowed_size ),
			UPLOAD_ERR_PARTIAL		 => _x( 'The uploaded file was only partially uploaded', 'upload error message', 'mediapress' ),
			UPLOAD_ERR_NO_FILE		 => _x( 'No file was uploaded', 'upload error message', 'mediapress' ),
			UPLOAD_ERR_NO_TMP_DIR	 => _x( 'Missing a temporary folder.', 'upload error message', 'mediapress' )
		);
	}
	private function invalidate_transient( $component, $component_id = null ){
		
		if ( ! $component || ! $component_id ) {
			return;
		}
		
		$key = "mpp_space_used_by_{$component}_{$component_id}"; //transient key
		
		delete_transient( $key );
		delete_transient( 'dirsize_cache' );
		
	}
	/**
	 * Fix the image rotation issues on mobile devices
	 * 
	 * @param string $file absolute path to the file
	 * @return string 
	 */
	private function fix_rotation( $file ) {
		//exif support not available
		if ( !  function_exists( 'exif_read_data' ) ) {
			return $file;
		}
				
		if ( !  $this->is_valid_image_file( $file ) ) {
			return $file;
		}
		
		$exif = @exif_read_data( $file );
		
		$orientation = isset( $exif['Orientation'] )? $exif['Orientation'] : 0;
		
		
		if ( ! $orientation ) {
			return $file;
		}
		
		$rotate = false;
		$horizontal_flip = false;
		$vertrical_flip = false;
				
		switch( $orientation ) {
			
			case 2:
				$horizontal_flip = true;
				break;
			
			case 3:
				$rotate = 180;
				break;
			
			case 4:
				$vertrical_flip = true;
				break;
			
			case 5:
				//transpose
				$rotate = 90;
				$vertrical_flip = true;
				break;
			
			case 6:
				$rotate = 270;
				break;
			
			case 7:
				$rotate = 90;
				$horizontal_flip = true;
				break;
			
			case 8:
				$rotate = 90;
				break;
		
		}
		
		$image_editor = wp_get_image_editor( $file );
		
		if ( is_wp_error( $image_editor ) ) {
			return $file;
		}
		
		if ( $rotate ) {
			$image_editor->rotate( $rotate );
		}
		
		if ( $horizontal_flip || $vertrical_flip ) {
			$image_editor->flip( $horizontal_flip, $vertrical_flip );
		
		}
		
		$image_editor->save( $file );//save to the file
		
		return $file;
	}
	
	/**
	 * Check if given file is image
	 * 
	 * a copy of file_is_valid_image
	 * @see file_is_valid_image()
	 * @param string $file
	 * @return boolean
	 */
	private function is_valid_image_file( $file ) {
		
		$size = @getimagesize( $file );
		
		return ! empty( $size );
	}
	
}

/**
 * Singleton Instance of Local Strorage 
 * 
 * @return MPP_Local_Storage
 */
function mpp_local_storage() {

	return MPP_Local_Storage::get_instance();
}

///MS compat for calculating space
if ( ! function_exists( 'get_dirsize' ) ):
/**
 * Get the size of a directory.
 *
 * A helper function that is used primarily to check whether
 * a blog has exceeded its allowed upload space.
 *
 * @since MU
 * @uses recurse_dirsize()
 *
 * @param string $directory
 * @return int
 */
function get_dirsize( $directory ) {
	
	$dirsize = get_transient( 'dirsize_cache' );
	
	if ( is_array( $dirsize ) && isset( $dirsize[ $directory ][ 'size' ] ) ) {
		return $dirsize[ $directory ][ 'size' ];
	}

	if ( false == is_array( $dirsize ) ) {
		$dirsize = array();
	}

	$dirsize[ $directory ][ 'size' ] = recurse_dirsize( $directory );

	set_transient( 'dirsize_cache', $dirsize, HOUR_IN_SECONDS );
	return $dirsize[ $directory ][ 'size' ];
}
endif;

if ( ! function_exists( 'recurse_dirsize' ) ):
/** 
 * Get the size of a directory recursively.
 *
 * Used by get_dirsize() to get a directory's size when it contains
 * other directories.
 *
 * @since MU
 *
 * @param string $directory
 * @return int
 */
function recurse_dirsize( $directory ) {
	$size = 0;

	$directory = untrailingslashit( $directory );

	if ( ! file_exists( $directory ) || ! is_dir( $directory ) || ! is_readable( $directory ) ) {
		return false;
	}

	if ( $handle = opendir( $directory ) ) {
		
		while ( ( $file = readdir( $handle ) ) !== false) {
			$path = $directory . '/' . $file;
			if ( $file != '.' && $file != '..' ) {
				
				if ( is_file( $path ) ) {
					$size += filesize( $path );
				} elseif ( is_dir( $path ) ) {
					
					$handlesize = recurse_dirsize( $path );
					
					if ( $handlesize > 0 ) {
						$size += $handlesize;
					}
				}
			}
		}
		closedir( $handle );
	}
	return $size;
}
endif;
