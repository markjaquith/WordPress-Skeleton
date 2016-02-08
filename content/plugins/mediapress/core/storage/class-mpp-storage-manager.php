<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Storage Manager superclass
 * 
 * All the storage managers must implement this class
 * 
 */
abstract class MPP_Storage_Manager {
    /*
    protected $component;
    protected $component_id;
    protected $gallery_id;
    */
    abstract public function upload( $file, $args );
	/**
	 * Get the media meta
	 */
    abstract public function get_meta( $uploaded_info );
	/**
	 * generate media attachment metadata
	 */
    abstract public function generate_metadata( $id, $file );
    
	/**
	 * Called after the Media Is deleted
	 * 
	 */
    abstract public function delete_media( $media );
    /**
	 * Called when a Gallery is being deleted
	 * Use it to cleanup any remnant of the gallerry
	 * 
	 */
	abstract public function delete_gallery( $gallery );
	
	/**
	 * Get the used space for the vine component
	 */
    abstract public function get_used_space( $component, $component_id );
        
    /**
     * Get the absolute url to a media file
     * e.g http://example.com/wp-content/uploads/mediapress/members/1/xyz.jpg
     */
    public abstract function get_src( $type = '', $id = null );
    /**
     * Get the absolute file system path to the 
     */
    public abstract function get_path( $type = '', $id = null );
	
	/**
	 * An alias for self::get_src()
	 * 
	 * @param string $size name of the registered media size
	 * 
	 * @param int $id media id
	 * 
	 * @return string absolute url of the image
	 */
	public function get_url( $size, $id ){
		
		return $this->get_src( $size, $id );
	}
 
    /**
     * Assume that the server can handle upload
	 * 
     * Mainly used in case of local uploader for checking postmax size etc
     * If you are implementing, return false if the upload data can be handled otherwise return false
	 * 
     * @return boolean
     */
    public function can_handle() {
		
      return true;
		
    }
    
    
    
    
}
