<?php

/**
 * Controller that handles shutdown functions.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Controller
 */
class Ai1ec_Shutdown_Controller {

	/**
	 * @var array Map of object names and class names they represent to preserve
	 */
	protected $_preserve        = array(
		'wpdb'            => 'wpdb',
		'wp_object_cache' => 'WP_Object_Cache',
	);

	/**
	 * @var array Map of object names and their representation from global scope
	 */
	protected $_restorables     = array();

	/**
	 * @var array List of callbacks to be executed during shutdown sequence
	 */
	protected $_callbacks       = array();


	/**
	 * Destructor
	 *
	 * Here processing of globals is made - values are replaced, callbacks
	 * are executed and globals are restored to the previous state.
	 *
	 * @return void Destructor does not return
	 */
	public function __destruct() {
		// replace globals from our internal store
		$restore = array();
		foreach ( $this->_preserve as $name => $class ) {
			if (
				! isset( $GLOBALS[$name] ) ||
				! ( $GLOBALS[$name] instanceof $class )
			) {
				$restore[$name] = NULL;
				if ( isset( $GLOBALS[$name] ) ) {
					$restore[$name] = $GLOBALS[$name];
				}
				$GLOBALS[$name] = $this->_restorables[$name];
			}
		}
		// execute callbacks
		foreach ( $this->_callbacks as $callback ) {
			call_user_func( $callback );
		}
		// restore globals to previous state
		foreach ( $restore as $name => $object ) {
			if ( NULL === $object ) {
				unset( $GLOBALS[$name] );
			} else {
				$GLOBALS[$name] = $object;
			}
		}
		// destroy local references
		foreach ( $this->_restorables as $name => $object ) {
			unset( $object, $this->_restorables[$name] );
		}
		if ( AI1EC_DEBUG ) {
			// __destruct is called twice if facebook extension is installed
			// still can't find the reason, this fixes it but prevent other plugins
			// __destruct() so let's just use it in dev until we fix this.
			exit();
		}
	}

	/**
	 * Register a callback to be executed during shutdown sequence
	 *
	 * @param callback $callback Valid PHP callback
	 *
	 * @return Ai1ec_Shutdown_Utility Self instance for chaining
	 */
	public function register( $callback ) {
		$this->_callbacks[] = $callback;
		return $this;
	}

	/**
	 * Constructor
	 *
	 * Here global variables are referenced locally to ensure their preservation
	 *
	 * @return void Constructor does not return
	 */
	public function __construct() {
		foreach ( $this->_preserve as $name => $class ) {
			$this->_restorables[$name] = $GLOBALS[$name];
		}
	}

}