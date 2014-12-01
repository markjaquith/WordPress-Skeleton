<?php

/**
 * Handle finding CSS/LESS files.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 * @instantiator new
 * @package    AI1EC
 * @subpackage AI1EC.Theme.File
 */
class Ai1ec_File_Less extends Ai1ec_File_Abstract {

	/**
	 * @var string The default CSS folder.
	 */
	const THEME_CSS_FOLDER = 'css';

	/**
	 * @var string The default less folder.
	 */
	const THEME_LESS_FOLDER = 'less';

	/**
	 * Returns the name of the file.
	 * @return string
	 */
	public function get_name() {
		return $this->_name;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_File_Abstract::process_file()
	 */
	public function process_file() {
		// 1. Look for a CSS file in the directory of the current theme.
		// 2. Look for a LESS version in the directory of the current theme.
		// 3. Look for a LESS file into the default theme folder.
		$name = $this->_name;
		$css_file  = $name . '.css';
		$less_file = $name . '.less';
		$files_to_check = array();
		foreach ( $this->_paths as $path ) {
			$files_to_check[] =
				$path . self::THEME_LESS_FOLDER . DIRECTORY_SEPARATOR . $less_file;
			$files_to_check[] =
				$path . self::THEME_CSS_FOLDER . DIRECTORY_SEPARATOR . $css_file;
			if ( '..' . DIRECTORY_SEPARATOR . 'style' === $name ) {
				$files_to_check[] =
					$path . self::THEME_LESS_FOLDER . DIRECTORY_SEPARATOR . $css_file;
			}
		}

		foreach ( $files_to_check as $file_to_check ) {
			if ( file_exists( $file_to_check ) ) {
				$this->_content = file_get_contents( $file_to_check );
				$this->_name    = $file_to_check;
				return true;
			}
		}
		return false;
	}
}
