<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Just a clone, not being used, we will restructure and use in 1.1
 * Handles the Oembed storage on the server
 * 
 * This allows to store the files on the same server where WordPress is installed 
 * 
 */
class MPP_Oembed_Storage extends MPP_Storage_Manager {

	private static $instance;
	/**
	 *
	 * @var WP_oEmbed 
	 */
	private $oembed; 
	
	private $upload_errors = array();

	private function __construct() {

		$this->oembed = new MPP_oEmbed();
		
		// $this->setup_upload_errors();
	}

	/**
	 * 
	 * @return MPP_Oembed_Storage_Manager
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) )
			self::$instance = new self();

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
		if ( ! $id )
			return '';
		
		
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

		
	}
	
	
	/**
	 * Extract meta from uploaded data 
	 * 
	 * @param type $uploaded
	 * @return type
	 */
	public function get_meta( $uploaded ) {

		$meta = array();

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
		
		
	}

	

	/**
	 * Delete all the files associated with a Media
	 * 
	 * @global type $wpdb
	 * @param type $id
	 * @return boolean
	 */
	public function delete( $media_id ) {
		
	
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

		return 0;//we are not storing anything on our server
		
	}

	
	public function get_errors() {
		
	}

	/**
	 * Server can handle upload?
	 * 
	 * @return boolean
	 */
	public function can_handle() {

		return true;//in future we may check the url for provider
	}

	
	/**
	 * Possible upload errors
	 */
	public function setup_upload_errors( $component ) {

		

		$this->upload_errors = array(
		
		);
	}

}

/**
 * Singleton Instance of MPP_Oembed_Storage_Manager
 * 
 * @return MPP_Oembed_Storage_Manager
 */
function mpp_oembed_storage() {

	return MPP_Oembed_Storage_Manager::get_instance();
}

