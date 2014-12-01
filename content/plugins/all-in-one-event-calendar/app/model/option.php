<?php

/**
 * Options management class.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Model
 */
class Ai1ec_Option extends Ai1ec_App {

	/**
	 * @var Ai1ec_Cache_Memory In-memory cache storage engine for fast access.
	 */
	protected $_cache = null;

	/**
	 * @var Ai1ec_Registry_Object instance of the registry object.
	 */
	protected $_registry;

	/**
	 * Add cache instance to object scope.
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
		$this->_cache    = $registry->get( 'cache.memory' );
	}

	/**
	 * Create an option if it does not exist.
	 *
	 * @param string $name     Key to put value under.
	 * @param mixed  $value    Value to put to storage.
	 * @param bool   $autoload Set to true to load on start.
	 *
	 * @return bool Success.
	 */
	public function add( $name, $value, $autoload = false ) {
		$autoload = $this->_parse_autoload( $autoload );
		if ( ! add_option( $name, $value, '', $autoload ) ) {
			return false;
		}
		$this->_cache->set( $name, $value );
		return true;
	}

	/**
	 * Create an option if it does not exist, or update existing.
	 *
	 * @param string $name     Key to put value under.
	 * @param mixed  $value    Value to put to storage.
	 * @param bool   $autoload Set to true to load on start.
	 *
	 * @return bool Success.
	 */
	public function set( $name, $value, $autoload = false ) {
		$comparator = "\0t\0";
		if ( $this->get( $name, $comparator ) === $comparator ) {
			return $this->add( $name, $value, $autoload );
		}
		if ( ! update_option( $name, $value ) ) {
			return false;
		}
		$this->_cache->set( $name, $value );
		return true;
	}

	/**
	 * Get a value from storage.
	 *
	 * @param string $name    Key to retrieve.
	 * @param mixed  $default Value to return if key was not set previously.
	 *
	 * @return mixed Value from storage or {$default}.
	 */
	public function get( $name, $default = null ) {
		$value = $this->_cache->get( $name, $default );
		if ( $default === $value ) {
			$value = get_option( $name, $default );
			$this->_cache->set( $name, $value );
		}
		return $value;
	}

	/**
	 * Delete value from storage.
	 *
	 * @param string $name Key to delete.
	 *
	 * @wp_hook deleted_option Fire after deletion.
	 *
	 * @return bool Success.
	 */
	public function delete( $name ) {
		$this->_cache->delete( $name );
		if ( 'deleted_option' === current_filter() ) {
			return true; // avoid loops
		}
		return delete_option( $name );
	}

	/**
	 * Convert autoload flag input to value recognized by WordPress.
	 *
	 * @param bool $input Autoload flag value.
	 *
	 * @return string Autoload identifier.
	 */
	protected function _parse_autoload( $input ) {
		return $input ? 'yes' : 'no';
	}

}
