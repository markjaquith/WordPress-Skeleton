<?php

/**
 * Loads files for admin and frontend.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Theme
 */
class Ai1ec_Theme_Compiler extends Ai1ec_Base {

	/**
	 * Register filters early on.
	 *
	 * @param Ai1ec_Registry_Object $registry Instance to use.
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		add_filter(
			'ai1ec_twig_environment',
			array( $this, 'ai1ec_twig_environment' )
		);
		add_filter(
			'ai1ec_twig_add_debug',
			'__return_false'
		);
	}

	/**
	 * Perform actual templates (re-)compilation.
	 *
	 * @return void
	 */
	public function generate() {
		$loader = $this->_registry->get( 'theme.loader' );
		header( 'Content-Type: text/plain; charset=utf-8' );
		$start  = microtime( true );
		foreach ( array( true, false ) as $for_admin ) {
			$twig  = $loader->get_twig_instance( $for_admin, true );
			$files = $this->get_files( $twig );
			$this->compile( $twig, $files );
		}
		echo 'Re-compiled in ' . ( microtime( true ) - $start ) . "\n";
		exit( 0 );
	}

	/**
	 * Extract files locatable within provided Twig Environment.
	 *
	 * @param Twig_Environment $twig Instance to check.
	 *
	 * @return array Map of files => Twig templates.
	 */
	public function get_files( Twig_Environment $twig ) {
		$files = array();
		try {
			$paths = $twig->getLoader()->getPaths();
			foreach ( $paths as $path ) {
				$files += $this->read_files( $path, strlen( $path ) + 1 );
			}
		} catch ( Exception $excpt ) { }
		return $files;
	}

	/**
	 * Actually compile templates to cache directory.
	 *
	 * @param Twig_Environment $twig      Instance to use for compilation.
	 * @param array            $file_list Map of files located previously.
	 *
	 * @return void
	 */
	public function compile( Twig_Environment $twig, array $file_list ) {
		foreach ( $file_list as $file => $template ) {
			$twig->loadTemplate( $template );
			echo 'Compiled: ', $template, ' (', $file, ')', "\n";
		}
	}

	/**
	 * Read file system searching for twig files.
	 *
	 * @param string $path        Directory to search in.
	 * @param int    $trim_length Number of characters to omit for templates.
	 *
	 * @return array Map of files => Twig templates.
	 */
	public function read_files( $path, $trim_length ) {
		$handle = opendir( $path );
		$files  = array();
		if ( false === $handle ) {
			return $files;
		}
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( '.' === $file{0} ) {
				continue;
			}
			$new_path = $path . DIRECTORY_SEPARATOR . $file;
			if ( is_dir( $new_path ) ) {
				$files += $this->read_files( $new_path, $trim_length );
			} else if (
				is_file( $new_path ) &&
				'.twig' === strrchr( $new_path, '.' )
			) {
				$files[$new_path] = substr( $new_path, $trim_length );
			}
		}
		closedir( $handle );
		return $files;
	}

	/**
	 * Adjust Twig environment for compilation.
	 *
	 * @param array $environment Initial environment arguments.
	 *
	 * @return 
	 */
	public function ai1ec_twig_environment( array $environment ) {
		$environment['debug']       = false;
		$environment['cache']       = AI1EC_TWIG_CACHE_PATH;
		$environment['auto_reload'] = true;
		if ( ! $this->clean_and_check_dir( $environment['cache'] ) ) {
			throw new Ai1ec_Bootstrap_Exception(
				'Failed to create cache directory: ' . $environment['cache']
			);
		}
		return $environment;
	}

	/**
	 * Ensure cache directory pre-conditions.
	 *
	 * Before compilation starts cache directory must be empty but existing.
	 * NOTE: it attempts to preserve `.gitignore` file in cache/ directory.
	 *
	 * @param string $cache_dir Directory to check.
	 *
	 * @return bool Validity.
	 */
	public function clean_and_check_dir( $cache_dir ) {
		$parent = realpath( $cache_dir );
		if ( ! $this->_prune_dir( $parent ) ) {
			return false;
		}
		if (
			is_dir( $cache_dir ) && chmod( $cache_dir, 0754 )
			|| mkdir( $cache_dir, 0754, true )
		) {
			return true;
		}
		return false;
	}

	/**
	 * Remove directory and all it's contents.
	 *
	 * @param string $cache_dir Absolute path to remove.
	 *
	 * @return bool Success.
	 */
	protected function _prune_dir( $cache_dir ) {
		if ( ! file_exists( $cache_dir ) ) {
			return true;
		}
		$handle = opendir( $cache_dir );
		if ( ! $handle ) {
			return false;
		}
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( '.' === $file{0} ) {
				continue;
			}
			$basename = basename( $file, '.php' );
			// continue deleting only if:
			// - it's 60 characters length (filename w/o '.php')
			// - OR it's 2 characters length (directory)
			// - AND (with two above) it's hex encoded string
			if (
				! (
					( isset( $basename{59} ) && ! isset( $basename{60} ) ) ||
					( isset( $basename{1} )	 && ! isset( $basename{2}  ) ) &&
					ctype_xdigit( $basename )
				)
			) {
				continue;
			}
			$path = $cache_dir . DIRECTORY_SEPARATOR . $file;
			if ( is_file( $path ) ) {
				if ( ! unlink( $path ) ) {
					return false;
				}
			} else {
				if ( ! $this->_prune_dir( $path ) ) {
					return false;
				}
			}
		}
		closedir( $handle );
		if ( is_file( $cache_dir . DIRECTORY_SEPARATOR . 'EMPTY' ) ) {
			return true; // ignore, this directory is intentionally here
		}
		return rmdir( $cache_dir );
	}

}