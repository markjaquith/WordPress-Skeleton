<?php

/**
 * A helper class for Filesystem checks.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Filesystem
 */
class Ai1ec_Filesystem_Checker {


	public function __construct() {
		include_once ABSPATH . 'wp-admin/includes/file.php';
	}
	/**
	 * check if the path is writable. To make the check .
	 *
	 * @param string $path
	 * @return boolean
	 */
	public function is_writable( $path ) {
		global $wp_filesystem;

		// try without credentials 
		$writable = WP_Filesystem( false, $path );
		// We consider the directory as writable if it uses the direct transport,
		// otherwise credentials would be needed
		if ( true === $writable ) {
			return true;
		}
		// if the user has FTP and sockets defined 
		if ( 
				$this->is_ftp_or_sockets( $wp_filesystem->method ) &&
				$this->are_ftp_constants_defined()
		) {
			$creds = request_filesystem_credentials( '', $wp_filesystem->method, false, $path );
			$writable = WP_Filesystem( $creds, $path );
			if ( true === $writable ) {
				return true;
			}
		}
		if (
			$this->is_ssh( $wp_filesystem->method ) &&
			$this->are_ssh_constants_defined()
		) {
			$creds = request_filesystem_credentials( '', $wp_filesystem->method, false, $path );
			$writable = WP_Filesystem( $creds, $path );
			if ( true === $writable ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if method is ssh
	 * 
	 * @param strin $method
	 * @return boolean
	 */
	public function is_ssh( $method ) {
		return 'ssh2' === $method;
	}

	/**
	 * Check if method is ftp or sockets
	 * 
	 * @param string $method
	 * @return boolean
	 */
	public function is_ftp_or_sockets( $method ) {
		return 'ftpext' === $method ||
				'ftpsockets' === $method;
	}

	/**
	 * Check if credentials for ssh are defined
	 * 
	 * @return boolean
	 */
	public function are_ssh_constants_defined() {
		return defined('FTP_HOST') &&
			defined('FTP_PUBKEY') &&
			defined('FTP_PRIKEY');
	}

	/**
	 * Check if credentials for ftp are defined
	 *
	 * @return boolean
	 */
	public function are_ftp_constants_defined() {
		return defined('FTP_HOST') &&
				defined('FTP_USER') &&
				defined('FTP_PASS');
	}

	/**
	 * Creates a file using $wp_filesystem.
	 * 
	 * @param string $file
	 * @param string $content
	 */
	public function put_contents( $file, $content ) {
		global $wp_filesystem;
		return $wp_filesystem->put_contents(
			$file,
			$content
		);
	}
	
	/**
	 * Get the content folder from Wordpress if available
	 * 
	 * @return string the folder to use or ''
	 */
	public function get_ai1ec_static_dir_if_available() {
		global $wp_filesystem;
		// reset the filesystem to it's standard.
		WP_Filesystem();
		$content_dir = $wp_filesystem->wp_content_dir();
		$static_dir = trailingslashit( $content_dir . 'ai1ec_static' );
		if ( 
			! $wp_filesystem->is_dir( $static_dir ) && 
			! $wp_filesystem->mkdir( $static_dir ) 
		) {
			return '';
		}
		return $static_dir;
	}

}