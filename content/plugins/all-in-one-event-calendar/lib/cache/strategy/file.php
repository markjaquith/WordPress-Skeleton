<?php

/**
 * Concrete class for file caching strategy.
 *
 * @instantiator new
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Cache.Strategy
 */
class Ai1ec_Cache_Strategy_File extends Ai1ec_Cache_Strategy {

	/**
	 * @var string
	 */
	private $_cache_dir;
	
	private $_cache_url;

	public function __construct( Ai1ec_Registry_Object $registry, array $cache_dir ) {
		parent::__construct( $registry );
		$this->_cache_dir = $cache_dir['path'];
		$this->_cache_url = $cache_dir['url'];
	}

	/**
	 *
	 * @see Ai1ec_Get_Data_From_Cache::get_data()
	 *
	 */
	public function get_data( $file ) {
		$file = $this->_safe_file_name( $file );
		if ( ! file_exists( $this->_cache_dir . $file ) ) {
			throw new Ai1ec_Cache_Not_Set_Exception(
				'File \'' . $file . '\' does not exist'
			);
		}
		return maybe_unserialize(
			file_get_contents( $this->_cache_dir . $file )
		);
	}

	/**
	 *
	 * @see Ai1ec_Write_Data_To_Cache::write_data()
	 *
	 */
	public function write_data( $filename, $value ) {
		$filename = $this->_safe_file_name( $filename );
		$value    = maybe_serialize( $value );

		$result = $this->_registry->get( 'filesystem.checker' )->put_contents(
			$this->_cache_dir . $filename,
			$value
		);
		if ( false === $result ) {
			$message = 'An error occured while saving data to \'' .
				$this->_cache_dir . $filename . '\'';
			throw new Ai1ec_Cache_Write_Exception( $message );
		}
		return array( 
			'path' => $this->_cache_dir . $filename,
			'url'  => $this->_cache_url . $filename,
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Write_Data_To_Cache::delete_data()
	 */
	public function delete_data( $filename ) {
		// Check if file exists. It might not exists if you switch themes
		// twice without never rendering the CSS
		$filename = $this->_safe_file_name( $filename );
		if (
			file_exists( $this->_cache_dir . $filename ) &&
			false === unlink( $this->_cache_dir . $filename )
		) {
			return false;
		}
		return true;
	}

	/**
	 *
	 * @see Ai1ec_Write_Data_To_Cache::delete_matching()
	 */
	public function delete_matching( $pattern ) {
		$dirhandle = opendir( $this->_cache_dir );
		if ( false === $dirhandle ) {
			return 0;
		}
		$count = 0;
		while ( false !== ( $entry = readdir( $dirhandle ) ) ) {
			if ( '.' !== $entry{0} && false !== strpos( $entry, $pattern ) ) {
				if ( unlink( $this->_cache_dir . $entry ) ) {
					++$count;
				}
			}
		}
		closedir( $dirhandle );
		return $count;
	}

	/**
	 * Get the extension for the file if required
	 * 
	 * @param string $file
	 * 
	 * @return string
	 */
	protected function _get_extension_for_file( $file ) {
		$extensions = array(
			'ai1ec_parsed_css' => '.css'
		);
		if ( isset( $extensions[$file] ) ) {
			return $extensions[$file];
		}
		return '';
	}
	
	/**
	 * _safe_file_name method
	 *
	 * Generate safe file name for any storage case.
	 *
	 * @param string $file File name currently supplied
	 *
	 * @return string Sanitized file name
	 */
	protected function _safe_file_name( $file ) {
		static $prefix = null;
		$extension = $this->_get_extension_for_file( $file );
		if ( null === $prefix ) {
			// always include site_url when there is more than one
			$pref_string = site_url();
			if ( ! AI1EC_DEBUG ) {
				// address multiple re-saves for a single version
				// i.e. when theme settings are being edited
				$pref_string .= mt_rand();
			}
			$prefix = substr( md5( $pref_string ), 0, 8 );
		}
		$length = strlen( $file );
		if ( ! ctype_alnum( $file ) ) {
			$file = preg_replace(
				'|_+|',
				'_',
				preg_replace( '|[^a-z0-9\-,_]|', '_', $file )
			);
		}
		if ( 0 !== strncmp( $file, $prefix, 8 ) ) {
			$file = $prefix . '_' . $file;
		}
		return $file . $extension;
	}

}
