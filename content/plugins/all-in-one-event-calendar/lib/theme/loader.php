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
class Ai1ec_Theme_Loader {

	/**
	 * @const string Name of option which forces theme clean-up if set to true.
	 */
    const OPTION_FORCE_CLEAN = 'ai1ec_clean_twig_cache';

	/**
	 * @const string Prefix for theme arguments filter name.
	 */
	const ARGS_FILTER_PREFIX = 'ai1ec_theme_args_';

	/**
	 * @var array contains the admin and theme paths.
	 */
	protected $_paths = array(
		'admin' => array( AI1EC_ADMIN_PATH => AI1EC_ADMIN_URL ),
		'theme' => array(),
	);

	/**
	 * @var Ai1ec_Registry_Object The registry Object.
	 */
	protected $_registry;

	/**
	 * @var array Array of Twig environments.
	 */
	protected $_twig = array();

	/**
	 * @var bool Whether this theme uses .php templates instead of .twig
	 */
	protected $_legacy_theme = false;

	/**
	 * @var bool Whether this theme is a child of the default theme
	 */
	protected $_child_theme = false;

	/**
	 * @var bool Whether this theme is a core theme
	 */
	protected $_core_theme = false;

	/**
	 * @return boolean
	 */
	public function is_legacy_theme() {
		return $this->_legacy_theme;
	}

	/**
	 *
	 * @param $registry Ai1ec_Registry_Object
	 *       	 The registry Object.
	 */
	public function __construct(
			Ai1ec_Registry_Object $registry
		) {
		$this->_registry         = $registry;
		$option                  = $this->_registry->get( 'model.option' );
		$theme                   = $option->get( 'ai1ec_current_theme' );
		$this->_legacy_theme     = (bool)$theme['legacy'];

		// Find out if this is a core theme.
		$core_themes             = explode( ',', AI1EC_CORE_THEMES );
		$this->_core_theme       = in_array( $theme['stylesheet'], $core_themes );

		// Default theme's path is always the last in the list of paths to check,
		// so add it first (path list is a stack).
		$this->add_path_theme(
			AI1EC_DEFAULT_THEME_PATH . DIRECTORY_SEPARATOR,
			AI1EC_THEMES_URL . '/' . AI1EC_DEFAULT_THEME_NAME . '/'
		);

		// If using a child theme, set flag and push its path to top of stack.
		if ( AI1EC_DEFAULT_THEME_NAME !== $theme['stylesheet'] ) {
			$this->_child_theme = true;
			$this->add_path_theme(
				$theme['theme_dir'] . DIRECTORY_SEPARATOR,
				$theme['theme_url'] . '/'
			);
		}
	}

	/**
	 * Adds file search path to list. If an extension is adding this path, and
	 * this is a custom child theme, inserts its path at the second index of the
	 * list. Else pushes it onto the top of the stack.
	 *
	 * @param string $target       Name of path purpose, i.e. 'admin' or 'theme'.
	 * @param string $path         Absolute path to the directory to search.
	 * @param string $url          URL to the directory represented by $path.
	 * @param string $is_extension Whether an extension is adding this page.
	 *
	 * @return bool Success.
	 */
	public function add_path( $target, $path, $url, $is_extension = false ) {
		if ( ! isset( $this->_paths[$target] ) ) {
			// Invalid target.
			return false;
		}

		// New element to insert into associative array.
		$new = array( $path => $url );

		if (
			true  === $is_extension &&
			true  === $this->_child_theme &&
			false === $this->_core_theme
		) {
			// Special case: extract first element into $head and insert $new after.
			$head = array_splice( $this->_paths[$target], 0, 1 );
		} else {
			// Normal case: $new gets pushed to the top of the array.
			$head = array();
		}

		$this->_paths[$target] = $head + $new + $this->_paths[$target];
		return true;
	}

	/**
	 * Add admin files search path.
	 *
	 * @param string $path Path to admin template files.
	 * @param string $url  URL to the directory represented by $path.
	 *
	 * @return bool Success.
	 */
	public function add_path_admin( $path, $url ) {
		return $this->add_path( 'admin', $path, $url );
	}

	/**
	 * Add theme files search path.
	 *
	 * @param string $path         Path to theme template files.
	 * @param string $url          URL to the directory represented by $path.
	 * @param string $is_extension Whether an extension is adding this path.
	 *
	 * @return bool Success.
	 */
	public function add_path_theme( $path, $url, $is_extension = false ) {
		return $this->add_path( 'theme', $path, $url, $is_extension );
	}

	/**
	 * Extension registration hook to automatically add file paths.
	 *
	 * NOTICE: extensions are expected to exactly replicate Core directories
	 * structure. If different extension is to be developed at some point in
	 * time - this will have to be changed.
	 *
	 * @param string $path Absolute path to extension's directory.
	 * @param string $url  URL to directory represented by $path.
	 *
	 * @return Ai1ec_Theme_Loader Instance of self for chaining.
	 */
	public function register_extension( $path, $url ) {
		$D = DIRECTORY_SEPARATOR; // For readability.

		// Add extension's admin path.
		$this->add_path_admin(
			$path . $D .'public' . $D . 'admin' . $D,
			$url . '/public/admin/'
		);

		// Add extension's theme path(s).
		$option = $this->_registry->get( 'model.option' );
		$theme  = $option->get( 'ai1ec_current_theme' );

		// Default theme's path is always later in the list of paths to check,
		// so add it first (path list is a stack).
		$this->add_path_theme(
			$path . $D . 'public' . $D . AI1EC_THEME_FOLDER . $D .
				AI1EC_DEFAULT_THEME_NAME . $D,
			$url . '/public/' . AI1EC_THEME_FOLDER . '/' . AI1EC_DEFAULT_THEME_NAME .
				'/',
			true
		);

		// If using a core child theme, set flag and push its path to top of stack.
		if ( true === $this->_child_theme && true === $this->_core_theme ) {
			$this->add_path_theme(
				$path . $D . 'public' . $D . AI1EC_THEME_FOLDER . $D .
					$theme['stylesheet'] . $D,
				$url . '/public/' . AI1EC_THEME_FOLDER . '/' . $theme['stylesheet'] .
					'/',
				true
			);
		}
		return $this;
	}

	/**
	 * Get the requested file from the filesystem.
	 *
	 * Get the requested file from the filesystem. The file is already parsed.
	 *
	 * @param string $filename        Name of file to load.
	 * @param array  $args            Map of variables to use in file.
	 * @param bool   $is_admin        Set to true for admin-side views.
	 * @param bool   $throw_exception Set to true to throw exceptions on error.
	 * @param array  $paths           For PHP & Twig files only: list of paths to use instead of default.
	 *
	 * @throws Ai1ec_Exception If File is not found or not possible to handle.
	 *
	 * @return Ai1ec_File_Abstract An instance of a file object with content parsed.
	 */
	public function get_file(
		$filename,
		$args            = array(),
		$is_admin        = false,
		$throw_exception = true,
		array $paths     = null
	) {
		$dot_position = strrpos( $filename, '.' ) + 1;
		$ext          = substr( $filename, $dot_position );
		$file         = false;

		switch ( $ext ) {
			case 'less':
			case 'css':
				$filename_base = substr( $filename, 0, $dot_position - 1);
				$file          = $this->_registry->get(
					'theme.file.less',
					$filename_base,
					array_keys( $this->_paths['theme'] ) // Values (URLs) not used for CSS
				);
				break;

			case 'png':
			case 'gif':
			case 'jpg':
				$paths = $is_admin ? $this->_paths['admin'] : $this->_paths['theme'];
				$file  = $this->_registry->get(
					'theme.file.image',
					$filename,
					$paths // Paths => URLs needed for images
				);
				break;

			case 'php':
				$args = apply_filters(
					self::ARGS_FILTER_PREFIX . $filename,
					$args,
					$is_admin
				);
				if ( null === $paths ) {
					$paths = $is_admin ? $this->_paths['admin'] : $this->_paths['theme'];
					$paths = array_keys( $paths ); // Values (URLs) not used for PHP
				}
				$args['is_legacy_theme'] = $this->_legacy_theme;
				$file                    = $this->_registry->get(
					'theme.file.php',
					$filename,
					$paths,
					$args
				);
				break;

			case 'twig':
				$args = apply_filters(
					self::ARGS_FILTER_PREFIX . $filename,
					$args,
					$is_admin
				);
				if ( null === $paths ) {
					$paths = $is_admin ? $this->_paths['admin'] : $this->_paths['theme'];
					$paths = array_keys( $paths ); // Values (URLs) not used for Twig
				}
				if ( true === $this->_legacy_theme && ! $is_admin ) {
					$filename = substr( $filename, 0, $dot_position - 1);
					$file     = $this->_get_legacy_file(
						$filename,
						$args,
						$paths
					);
				} else {
					$file = $this->_registry->get(
						'theme.file.twig',
						$filename,
						$args,
						$this->_get_twig_instance( $paths, $is_admin )
					);
				}
				break;

			default:
				throw new Ai1ec_Exception(
					sprintf(
						Ai1ec_I18n::__( "We couldn't find a suitable loader for filename with extension '%s'" ),
						$ext
					)
				);
				break;
		}

		// here file is a concrete class otherwise the exception is thrown
		if ( ! $file->process_file() && true === $throw_exception ) {
			throw new Ai1ec_Exception(
				'The specified file "' . $filename . '" doesn\'t exist.'
			);
		}
		return $file;
	}

	/**
	 * Tries to load a PHP file from the theme. If not present, it falls back to
	 * Twig.
	 *
	 * @param string $filename Filename to locate
	 * @param array  $args     Args to pass to template
	 * @param array  $paths    Array of paths to search
	 *
	 * @return Ai1ec_File_Abstract
	 */
	protected function _get_legacy_file( $filename, array $args, array $paths ) {
		$php_file = $filename . '.php';
		$php_file = $this->get_file( $php_file, $args, false, false, $paths );

		if ( false === $php_file->process_file() ) {
			$twig_file = $this->_registry->get(
				'theme.file.twig',
				$filename . '.twig',
				$args,
				$this->_get_twig_instance( $paths, false )
			);

			// here file is a concrete class otherwise the exception is thrown
			if ( ! $twig_file->process_file() ) {
				throw new Ai1ec_Exception(
					'The specified file "' . $filename . '" doesn\'t exist.'
				);
			}
			return $twig_file;
		}
		return $php_file;
	}

	/**
	 * Get Twig instance.
	 *
	 * @param bool $is_admin Set to true for admin views.
	 * @param bool $refresh  Set to true to get fresh instance.
	 *
	 * @return Twig_Environment Configured Twig instance.
	 */
	public function get_twig_instance( $is_admin = false, $refresh = false ) {
		if ( $refresh ) {
			unset( $this->_twig );
		}
		$paths = $is_admin ? $this->_paths['admin'] : $this->_paths['theme'];
		$paths = array_keys( $paths ); // Values (URLs) not used for Twig
		return $this->_get_twig_instance( $paths, $is_admin );
	}

	/**
	 * Get cache dir for Twig.
	 *
	 * @param bool $rescan Set to true to force rescan
	 *
	 * @return string|bool Cache directory or false
	 */
	public function get_cache_dir( $rescan = false ) {
		$settings         = $this->_registry->get( 'model.settings' );
		$ai1ec_twig_cache = $settings->get( 'twig_cache' );
		if (
			! empty( $ai1ec_twig_cache ) &&
			false === $rescan
		) {
			return ( AI1EC_CACHE_UNAVAILABLE === $ai1ec_twig_cache )
				? false
				: $ai1ec_twig_cache;
		}
		$path          = false;
		$scan_dirs     = array( AI1EC_TWIG_CACHE_PATH );
		if ( apply_filters( 'ai1ec_check_static_dir', true ) ) {
			$filesystem    = $this->_registry->get( 'filesystem.checker' );
			$upload_folder = $filesystem->get_ai1ec_static_dir_if_available();
			if ( '' !== $upload_folder ) {
				$scan_dirs[] = $upload_folder;
			}
		}
		foreach ( $scan_dirs as $dir ) {
			if ( $this->_is_dir_writable( $dir ) ) {
				$path = $dir;
				break;
			}
		}

		$settings->set(
			'twig_cache',
			false === $path ? AI1EC_CACHE_UNAVAILABLE : $path
		);
		if ( false === $path ) {
			/* @TODO: move this to Settings -> Advanced -> Cache and provide a nice message */
		}
		return $path;
	}

	/**
	 * After upgrade clean cache if it's not default.
	 *
	 * @return void Method doesn't return
	 */
	public function clean_cache_on_upgrade() {
		if ( ! apply_filters( 'ai1ec_clean_cache_on_upgrade', true ) ) {
			return;
		}
		$model_option = $this->_registry->get( 'model.option' );
		if ( $model_option->get( self::OPTION_FORCE_CLEAN, false ) ) {
			$model_option->set( self::OPTION_FORCE_CLEAN, false );
			$cache = realpath( $this->get_cache_dir() );
			if ( 0 !== strcmp( $cache, realpath( AI1EC_TWIG_CACHE_PATH ) ) ) {
				$this->_registry->get( 'theme.compiler' )
					->clean_and_check_dir( $cache );
			}
		}
	}

	/**
	 * This method whould be in a factory called by the object registry.
	 * I leave it here for reference.
	 *
	 * @param array $paths Array of paths to search
	 * @param bool  $is_admin whether to use the admin or not admin Twig instance
	 *
	 * @return Twig_Environment
	 */
	protected function _get_twig_instance( array $paths, $is_admin ) {
		$instance = $is_admin ? 'admin' : 'front';
		if ( ! isset( $this->_twig[$instance] ) ) {

			// Set up Twig environment.
			$loader_path = array();

			foreach ( $paths as $path ) {
				if ( is_dir( $path . 'twig' . DIRECTORY_SEPARATOR ) ) {
					$loader_path[] = $path . 'twig' . DIRECTORY_SEPARATOR;
				}
			}

			$loader = new Ai1ec_Twig_Loader_Filesystem( $loader_path );
			unset( $loader_path );
			// TODO: Add cache support.
			$environment = array(
				'cache'            => $this->get_cache_dir(),
				'optimizations'    => -1,   // all
				'auto_reload'      => false,
			);
			if ( AI1EC_DEBUG ) {
				$environment += array(
					'debug' => true, // produce node structure
				);
				// auto_reload never worked well
				$environment['cache'] = false;
			}
			$environment = apply_filters(
				'ai1ec_twig_environment',
				$environment
			);

			$ai1ec_twig_environment = new Ai1ec_Twig_Environment(
					$loader,
					$environment
				);
			$ai1ec_twig_environment->set_registry( $this->_registry );

			$this->_twig[$instance] = $ai1ec_twig_environment;
			if ( apply_filters( 'ai1ec_twig_add_debug', AI1EC_DEBUG ) ) {
				$this->_twig[$instance]->addExtension( new Twig_Extension_Debug() );
			}

			$extension = $this->_registry->get( 'twig.ai1ec-extension' );
			$extension->set_registry( $this->_registry );
			$this->_twig[$instance]->addExtension( $extension );
		}
		return $this->_twig[$instance];
	}

	/**
	 * Called during 'after_setup_theme' action. Runs theme's special
	 * functions.php file, if present.
	 */
	public function execute_theme_functions() {
		$option    = $this->_registry->get( 'model.option' );
		$theme     = $option->get( 'ai1ec_current_theme' );
		$functions = $theme['theme_dir'] . DIRECTORY_SEPARATOR . 'functions.php';

		if ( file_exists( $functions ) ) {
			include( $functions );
		}
	}

	/**
	 * Safe checking for directory writeability.
	 *
	 * @param string $dir Path of likely directory.
	 *
	 * @return bool Writeability.
	 */
	private function _is_dir_writable( $dir ) {
		$stack = array(
			dirname( dirname( $dir ) ),
			dirname( $dir ),
			$dir,
		);
		foreach ( $stack as $element ) {
			if ( is_dir( $element )  ) {
				continue;
			}
			if ( ! is_writable( dirname( $element ) ) ) {
				return false;
			}
			if ( ! mkdir( $dir, 0755, true ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Switch to the given calendar theme.
	 *
	 * @param  array $theme            The theme's settings array
	 * @param  bool  $delete_variables If true, deletes user variables from DB.
	 *                                 Else replaces them with config file.
	 */
	public function switch_theme( array $theme, $delete_variables = true ) {
		$option = $this->_registry->get( 'model.option' );
		$option->set(
			'ai1ec_current_theme',
			$theme
		);
		$lessphp = $this->_registry->get( 'less.lessphp' );
		// If requested, delete theme variables from DB.
		if ( $delete_variables ) {
			$option->delete( Ai1ec_Less_Lessphp::DB_KEY_FOR_LESS_VARIABLES );
		}
		// Else replace them with those loaded from config file.
		else {
			$option->set(
				Ai1ec_Less_Lessphp::DB_KEY_FOR_LESS_VARIABLES,
				$lessphp->get_less_variable_data_from_config_file()
			);
		}
		// Recompile CSS for new theme.
		$css_controller = $this->_registry->get( 'css.frontend' );
		$css_controller->invalidate_cache( null, false );
	}

}
