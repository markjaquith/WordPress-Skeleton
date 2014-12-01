<?php
/**
 * Handle finding and parsing a twig template.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 * @instantiator new
 * @package    AI1EC
 * @subpackage AI1EC.Theme.File
 */
class Ai1ec_File_Twig extends Ai1ec_File_Abstract {

	/**
	 * @var Twig_Environment Twig environment for this helper.
	 */
	protected $_twig;

	/**
	 * @var array
	 */
	protected $_args;

	/**
	 * @param string $name The name of the template.
	 * @param array $args The arguments needed to render the template.
	 * @param Twig_Environment $twig An instance of the Twig environment
	 */
	public function __construct( $name, array $args, $twig ) {
		$this->_args = $args;
		$this->_name = $name;
		$this->_twig = $twig;
	}

	/**
	 * Adds the given search path to the end of the list (low priority).
	 *
	 * @param string $search_path Path to add to end of list
	 */
	public function appendPath( $search_path ) {
		$loader = $this->_twig->getLoader();
		$loader->addPath( $search_path );
	}

	/**
	 * Adds the given search path to the front of the list (high priority).
	 *
	 * @param string $search_path Path to add to front of list
	 */
	public function prepend_path( $search_path ) {
		$loader = $this->_twig->getLoader();
		$loader->prependPath( $search_path );
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_File::locate_file()
	 */
	public function process_file() {
		$loader = $this->_twig->getLoader();
		if ( $loader->exists( $this->_name ) ) {

			$this->_content = $this->_twig->render( $this->_name, $this->_args );

			return true;
		}
		return false;
	}

}
