<?php
/**
 * Handle finding and parsing a PHP file.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 * @instantiator new
 * @package    AI1EC
 * @subpackage AI1EC.Theme.File
 */
class Ai1ec_File_Php extends Ai1ec_File_Abstract {

	/**
	 * @var string filename with the variables
	 */
	const USER_VARIABLES_FILE = 'user_variables';

	/**
	 * @var array the arguments used by the PHP template.
	 */
	private $_args;

	/**
	 * Initialize class specific variables.
	 *
	 * @parma Ai1ec_Registry_Object $registry
	 * @param string $name
	 * @param array $paths
	 * @param array $args
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		$name,
		array $paths,
		array $args
	) {
		parent::__construct( $registry, $name, $paths );
		$this->_args  = $args;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_File_Abstract::locate_file()
	 */
	public function process_file() {
		// if the file was already processed just return.
		if ( isset( $this->_content ) ) {
			return true;
		}
		$files_to_check = array();
		foreach ( array_values( $this->_paths ) as $path ) {
			$files_to_check[] = $path . $this->_name;
		}
		foreach ( $files_to_check as $file ) {
			if ( is_file( $file ) ) {
				// Check if file is custom LESS variable definitions.
				$user_variables_pattern = Ai1ec_File_Less::THEME_LESS_FOLDER .
					'/' . self::USER_VARIABLES_FILE;

				if ( strpos( $this->_name, $user_variables_pattern ) === 0 ) {
					// It's a user variables file. We must handle the fact that it might
					// be legacy.
					if ( true === $this->_args['is_legacy_theme'] ) {
						$content = ( require $file );
						if ( isset( $less_user_variables ) ) {
							$content = $less_user_variables;
						}
						$this->_content = $content;
					} else {
						$this->_content = require $file;
					}
				} else {
					$this->_registry->get( 'compatibility.ob' )->start();
					extract( $this->_args );
					require $file;
					$this->_content = $this->_registry
						->get( 'compatibility.ob' )->get_clean();
				}

				return true;
			}
		}
		return false;
	}

	/**
	 * Legacy function to keep conpatibility with 1.x themes.
	 *
	 * @param string $file
	 */
	public function get_theme_img_url( $file ) {
		return $this->_registry->get( 'theme.loader' )
			->get_file( $file, array(), false )->get_url();
	}
}
