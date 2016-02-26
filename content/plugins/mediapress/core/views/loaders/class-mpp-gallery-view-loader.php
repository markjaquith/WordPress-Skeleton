<?php
/**
 * Template Loader for Components
 */
abstract class MPP_Gallery_Template_Loader {
	
	protected $id = '';
	protected $path = '';
	
	protected function __construct( $args = null ) {
	
	}
	
	//we could implement singleton here for child using static keyword but that won't work for < php 5.4 and self is not good idea here, so are the traits
	//moving singleton out to individual loader
	
	/**
	 * Get unique view id
	 * 
	 * @return string unique view ID
	 */
	public function get_id() {
		return $this->id;
	}
	/**
	 * Get relative path for component template directory
	 * 
	 * @return type
	 */
	public function get_path() {
		return $this->path;
	}
	
	abstract public function load_template();

}