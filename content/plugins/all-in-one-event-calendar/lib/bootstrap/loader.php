<?php

/**
 * Autoloader Class
 *
 * This class is responsible for loading all the requested class of the
 * system
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Loader
 */
class Ai1ec_Loader {

	/**
	 * @var string Used to specify new instances every time.
	 */
	CONST NEWINST    = 'n';

	/**
	 * @var string Used to specify to treat as singleton.
	 */
	CONST GLOBALINST = 'g';

	/**
	 * @var array Map of files to be included
	 */
	protected $_paths          = array();

	/**
	 * @var bool Set to true when internal state is changed
	 */
	protected $_modified       = false;

	/**
	 * @var array Map of files already included
	 */
	protected $_included_files = array();

	/**
	 * @var string The prefix used for the classes
	 */
	protected $_prefix         = null;

	/**
	 * @var string Base path to plugins core directory
	 */
	protected $_base_path      = null;

	/**
	 * @var array Registered folders.
	 */
	protected $_registered     = array();

	/**
	 * load method
	 *
	 * Load given class, via `require`, into memory
	 *
	 * @param string $class Name of class, which needs to be loaded
	 *
	 * @return Ai1ec_Loader Instance of self for chaining
	 */
	public function load( $class ) {
		if ( isset( $this->_paths[$class] ) ) {
			$this->include_file( $this->_paths[$class]['f'] );
		}
		return $this;
	}

	/**
	 * Method which actually includes required file.
	 *
	 * The PHP language construct used is `require` and not a `require_once`,
	 * as this is internal method, which shall guard itself against incidents
	 * that may occur during loading classes more than once.
	 * During include additional callbacks may be fired to include related
	 * files, i.e. speed-up further requires.
	 *
	 * @param string $file Name of file to include
	 *
	 * @return Ai1ec_Loader Instance of self for chaining
	 */
	public function include_file( $file ) {
		if ( ! isset( $this->_included_files[$file] ) ) {
			$this->_included_files[$file] = true;
			require $file;
		}
		return $this->_included_files[$file];
	}

	/**
	 * collect_classes method
	 *
	 * Method to extract classes list from filesystem.
	 * Returned array contains names of class, as keys, and file entites as
	 * value, where *entities* means either a file name
	 * - {@see self::match_file()} for more.
	 *
	 * @return array Map of classes and corresponding file entites
	 */
	public function collect_classes( $path = null, $folder_name = AI1EC_PLUGIN_NAME ) {
		// extension inject theit own base path
		$path  = ( null === $path ) ? $this->_base_path : $path;
		$names = $this->_locate_all_files( $path, $folder_name );
		$names = $this->_process_reflections( $names );
		$this->_cache( $path, $names );
		$this->_paths = array_merge( $this->_paths, $names );
		return $names;
	}

	/**
	 * Read/write cached classes map.
	 *
	 * If no entries are provided - acts as cache reader.
	 *
	 * @param array $entries Entries to write [optional=null]
	 *
	 * @return bool|array False on failure, true on success in writer
	 *		 mode, cached entry in reader mode on success
	 */
	protected function _cache( $path, array $entries = null ) {
		$cache_file = $this->_get_cache_file_path( $path );
		if ( $entries ) {
			if (
				is_file( $cache_file ) &&
				! is_writable( $cache_file ) ||
				! is_writable( dirname( $cache_file ) )
			) {
				return false;
			}
			ksort( $entries, SORT_STRING );
			$content = array(
				'0registered' => $this->_registered,
				'1class_map'  => $entries,
			);
			$content = var_export( $content, true );
			$content = $this->_sanitize_paths( $content, $path );
			$content = '<?php return ' . $content . ';';
			$this->_modified = false;
			if (
				false === file_put_contents( $cache_file, $content, LOCK_EX )
			) { // LOCK_EX is not supported on all hosts (streams)
				return (bool)file_put_contents( $cache_file, $content );
			}
			return true;
		}
		if ( ! is_file( $cache_file ) ) {
			return false;
		}
		$cached = ( require $cache_file );
		$this->_registered[$cache_file] = true;
		return $cached['1class_map'];
	}

	/**
	 * Gets the way classes must be instanciated.
	 *
	 * Retrieves from annotations the way classes must be retrieved.
	 * Possible values are
	 *  - new: a new instance is instantiated every time
	 *  - global: treat as singleton
	 *  - classname.method: a factory is used, specify it in that order
	 * The default if nothing is specified is global.
	 *
	 * @param ReflectionClass $class
	 *
	 * @return string
	 */
	protected function _get_instantiator( ReflectionClass $class ) {
		$doc = $class->getDocComment();
		preg_match_all(
			'#^\s\*\s@instantiator\s+(.*)$#im',
			$doc,
			$annotations
		);
		$instantiator = '';
		if ( isset( $annotations[1][0] ) ) {
			$instantiator = rtrim( $annotations[1][0] );
		}
		return $this->_convert_instantiator_for_map( $instantiator );
	}

	/**
	 * Check if the registry must be injected in the constructor.
	 * By convention the registry will always be the first parameter.
	 *
	 * @param ReflectionClass $class The class to check
	 *
	 * @return boolean true if the registry must be injected, false if not.
	 */
	protected function _inject_registry( ReflectionClass $class ) {
		$contructor = $class->getConstructor();
		if ( null !== $contructor ) {
			foreach ( $contructor->getParameters() as $param ) {
				$param_class = $param->getClass();
				if ( $param_class instanceof ReflectionClass ) {
					$name = $param_class->getName();
					if ( 'Ai1ec_Registry_Object' === $name ) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Update the classmap with Reflection informations.
	 *
	 * @param array $names The class map.
	 *
	 * @return array The classmap with instantiator.
	 */
	protected function _process_reflections( array $names ) {
		$this->_paths = array_merge( $this->_paths, $names );
		spl_autoload_register( array( $this, 'load' ) );
		foreach ( $names as $classname => &$data ) {
			try {
				$class = new ReflectionClass( $data['c'] );
				$data['i'] = $this->_get_instantiator( $class );
				if ( $this->_inject_registry( $class ) ) {
					$data['r'] = 'y';
				}
			} catch ( ReflectionException $excpt ) { // unreachable class
				$data['i'] = self::NEWINST;
			}
		}
		return $names;
	}

	/**
	 * Converts the long form to the short form where applicable.
	 *
	 * @param string $instantiator
	 *
	 * @return string
	 */
	protected function _convert_instantiator_for_map( $instantiator ) {
		if ( empty( $instantiator ) || 'global' === $instantiator ) {
			return self::GLOBALINST;
		}
		if ( 'new' === $instantiator ) {
			return self::NEWINST;
		}
		return $instantiator;
	}

	/**
	 * _locate_all_files method
	 *
	 * Scan file system, given path, recursively, to search for files and
	 * extract `class` names from them.
	 *
	 * @param string $path File system path to scan
	 *
	 * @return array Map of classes and corresponding files
	 */
	protected function _locate_all_files( $path, $folder_name ) {
		$class_list = array();
		$directory	= opendir( $path );
		while ( false !== ( $entry = readdir( $directory ) ) ) {
			if ( '.' === $entry{0} ) {
					continue; // ignore hidden files
			}
			$local_path = $path . DIRECTORY_SEPARATOR . $entry;
			$base_path  = substr( $local_path, strlen( $this->_base_path ) );

			if ( is_dir( $local_path ) ) {
				$class_list += $this->_locate_all_files( $local_path, $folder_name );
			} else {
				$class_list += $this->_extract_classes( $local_path, $folder_name );
			}
		}
		closedir( $directory );
		return $class_list;
	}

	/**
	 * _extract_classes method
	 *
	 * Extract names of classes from given file.
	 * So far only files ending in `.php` are processed and regular expression
	 * is used instead of `token_get_all` to increase parsing speed.
	 *
	 * @param string $file Name of file to scan
	 *
	 * @return array List of classes in file
	 */
	protected function _extract_classes( $file, $folder_name ) {
			$class_list = array();
			if ( '.php' === strrchr( $file, '.' ) ) {
				$tokens = token_get_all( file_get_contents( $file ) );
				for ( $i = 2, $count = count( $tokens ); $i < $count; $i++ ) {
					if (
						T_CLASS      === $tokens[$i - 2][0] ||
						T_INTERFACE  === $tokens[$i - 2][0] &&
						T_WHITESPACE === $tokens[$i - 1][0] &&
						T_STRING     === $tokens[$i][0]
					) {
						$names = $this->_generate_loader_names(
							$tokens[$i][1],
							$file,
							$folder_name
						);
						foreach ( $names as $name ) {
							$class_list[$name] = array(
								'f' => $file,
								'c' => $tokens[$i][1],
							);
						}
					}

				}
			}
			return $class_list;
	}

	/**
	 * Generate path name abbreviation.
	 *
	 * @param string $name Path name particle.
	 *
	 * @return string Abbreviated path name.
	 */
	public function path_name_shortening( $name ) {
		return strtoupper( $name[0] );
	}

	/**
	 * _sanitize_paths method
	 *
	 * Sanitize paths before writing to cache file.
	 * Make sure, that constants and absolute paths are used independently
	 * of system used, thus making file cross-platform generatable.
	 *
	 * @param string $content   Output to be written to cache file.
	 * @param string $base_path Base path to use if not default.
	 *
	 * @return string Modified content, with paths replaced
	 */
	protected function _sanitize_paths(
		$content,
		$base_path  = null
	) {
		$local_ds   = '/';
		$ai1ec_path = $this->_base_path;
		$const_name = 'AI1EC_PATH';
		if ( null !== $base_path ) {
			$ai1ec_path = $base_path;
			$const_name = implode( array_map(
				array( $this, 'path_name_shortening' ),
				explode( '-', basename( $base_path ) )
			) ) . '_PATH';
			$const_name = str_replace( 'AIOEC', 'AI1EC', $const_name );
		}
		if ( '\\' === DIRECTORY_SEPARATOR ) {
			$local_ds   = '\\\\';
			$ai1ec_path = str_replace( '\\', '\\\\', $ai1ec_path );
		}
		$content = str_replace(
			'\'' . $ai1ec_path . $local_ds,
			$const_name . ' . DIRECTORY_SEPARATOR . \'',
			$content
		);
		$content = str_replace(
			$local_ds,
			'\' . DIRECTORY_SEPARATOR . \'',
			$content
		);
		return $content;
	}

    /**
     * Generate all the alternatives name that the loaded recognize.
     *
     * For example:
     * The class Ai1ec_Html_Helper can be loaded as
     * - html.helper ( the path to the file )
     * - Ai1ec_Html_Helper ( needed by Autoload )
     *
     * @param $class string the original name of the class.
     * @param $file string the file
     *
     * @return array An array of strings with the availables names.
     */
	protected function _generate_loader_names( $class, $file, $folder_name ) {
		$names  = array( $class );
		// Remove the extension.
		$file   = substr( $file, 0, strrpos( $file , '.' ) );
		$file   = strtr( $file, array( '//' => '/' ) );
		// Get just the meaningful data.
		$relative_path_position = strrpos( // offset of base directory
			$file,
			DIRECTORY_SEPARATOR . $folder_name . DIRECTORY_SEPARATOR
		);
		$file   = substr(
			$file,
			strpos( // cut to app|lib|vendor|...
				$file,
				DIRECTORY_SEPARATOR,
				$relative_path_position + strlen( $folder_name ) + 2
			)
		);
		$names[] = str_replace(
			DIRECTORY_SEPARATOR,
			'.',
			trim( $file, DIRECTORY_SEPARATOR )
		);
		return $names;
	}

	/**
	 * Translate the key to the actual class name if any
	 *
	 * @param $key string Key requested to initialize
	 *
	 * @return array|null Array of the class, or null if none is found
	 */
	public function resolve_class_name( $key ) {
		if ( ! isset( $this->_paths[$key] ) ) {
			return null;
		}
		return $this->_paths[$key];
	}

	/**
	 * Update cache if object was modified
	 *
	 * @return void Destructor does not return
	 */
	public function __destruct() {
		if ( $this->_modified ) {
			$this->_cache( $this->_paths );
		}
	}

	/**
	 * Convenience wrapper to detect internal extension file path.
	 *
	 * @param string $path Absolute path to extension base directory.
	 *
	 * @return bool Success loading extension classes.
	 */
	public function register_extension_map( $path ) {
		return $this->register_map( $this->_get_cache_file_path( $path ) );
	}

	/**
	 * Register external class map to use in loading sequence
	 *
	 * @param string $file Path to class map
	 *
	 * @return bool Success loading it
	 */
	public function register_map( $file ) {
		if (
			isset( $this->_registered[$file] ) && (
				! defined( 'AI1EC_DEBUG' ) ||
				! AI1EC_DEBUG
			)
		) {
			return true;
		}
		if ( ! is_file( $file ) ) {
			return false;
		}
		$entries = ( require $file );
		foreach ( $entries['1class_map'] as $class_name => $properties ) {
			$this->_paths[$class_name] = $properties;
		}
		$this->_registered[$file] = true;
		return true;
	}

	/**
	 * Constructor
	 *
	 * Initialize the loader creating the map of available classes, if the
	 * AI1EC_DEBUG constants is true the list is regenerated
	 *
	 * @throws Exception if the map is invalid
	 *
	 * @return void Constructor does not return
	 */
	public function __construct( $base_path ) {
		$this->_base_path = $base_path;
		$this->_prefix = explode( '_', __CLASS__ );
		$this->_prefix = $this->_prefix[0];
		$class_map = $this->_cache( $base_path );
		if (
			! is_array( $class_map ) ||
			defined( 'AI1EC_DEBUG' ) && AI1EC_DEBUG
		) {
			if ( ! defined( 'AI1EC_DEBUG' ) || ! AI1EC_DEBUG ) {
				// using generic `Ai1ec_Exception` as others are, potentially,
				// not resolved at this time.
				throw new Ai1ec_Exception(
					'Generated class map is invalid: ' .
					var_export( $class_map, true ) .
					'. Please delete lib/bootstrap/loader-map.php (if it exists), make ' .
					'sure lib/bootstrap/ is writable by the web server, and enable ' .
					'debug mode by setting AI1EC_DEBUG to true (then back to false ' .
					'when done).'
				);
			}
			$class_map = $this->collect_classes();
		}
		$this->_paths = $class_map;
	}

	/**
	 * Method to get cache file path given path to plugin.
	 *
	 * @param string $path Path to plugin directory.
	 *
	 * @return string Absolute path to loader cache file.
	 */
	protected function _get_cache_file_path( $path ) {
		return $path . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR .
			'bootstrap' . DIRECTORY_SEPARATOR . 'loader-map.php';
	}

}
