<?php

/**
 * Handle finding and parsing an image file.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 * @instantiator new
 * @package    AI1EC
 * @subpackage AI1EC.Theme.File
 */
class Ai1ec_File_Image extends Ai1ec_File_Abstract {

	/**
	 * @var string The url of the image file.
	 */
	protected $_url;

	/**
	 * Get the URL to the image file.
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->_url;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_File_Abstract::process_file()
	 */
	public function process_file() {
		$files_to_check = array();
		foreach ( array_keys( $this->_paths ) as $path ) {
			$files_to_check[$path] =
				$path . 'img' . DIRECTORY_SEPARATOR . $this->_name;
		}
		foreach ( $files_to_check as $path => $file ) {
			if ( file_exists( $file ) ) {
				// Construct URL based on base URL available in $this->_paths array.
				$this->_url     = $this->_paths[$path] . '/img/' . $this->_name;
				$this->_content = $file;
				return true;
			}
		}
		return false;
	}
}
