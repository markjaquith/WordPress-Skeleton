<?php

/**
 * Abstract class for meta entries management.
 *
 * Via use of cache allows object-based access to meta entries.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Model
 */
abstract class Ai1ec_Meta extends Ai1ec_App {

	/**
	 * @var string Name of base object for storage.
	 */
	protected $_object = '';

	/**
	 * @var Ai1ec_Cache_Memory In-memory cache operator.
	 */
	protected $_cache = null;

	/**
	 * Initialize instance-specific in-memory cache storage.
	 *
	 * @return void Method does not return.
	 */
	protected function _initialize() {
		$class = get_class( $this );
		$this->_object = strtolower(
			substr( $class, strlen( __CLASS__ ) + 1  )
		);
		$this->_cache = $this->_registry->get( 'cache.memory' );
	}

	/**
	 * Create new entry if it does not exist and cache provided value.
	 *
	 * @param string $object_id ID of object to store.
	 * @param string $key       Key particle for ID to store.
	 * @param mixed  $value     Serializable value to store.
	 *
	 * @return bool Success.
	 */
	final public function add( $object_id, $key, $value ) {
		if ( ! $this->_add( $object_id, $key, $value ) ) {
			return false;
		}
		$this->_cache->set( $this->_cache_key( $object_id, $key ), $value );
		return true;
	}

	/**
	 * Update existing entry and cache it's value.
	 *
	 * @param string $object_id ID of object to store.
	 * @param string $key       Key particle for ID to store.
	 * @param mixed  $value     Serializable value to store.
	 *
	 * @return bool Success.
	 */
	final public function update( $object_id, $key, $value ) {
		if ( ! $this->_update( $object_id, $key, $value ) ) {
			return false;
		}
		$this->_cache->set( $this->_cache_key( $object_id, $key ), $value );
		return true;
	}

	/**
	 * Get object value - from cache or actual store.
	 *
	 * @param string $object_id ID of object to get.
	 * @param string $key       Key particle for ID to get.
	 * @param mixed  $default   Value to return if nothing found.
	 *
	 * @return mixed Value stored or {$default}.
	 */
	final public function get( $object_id, $key, $default = null ) {
		$cache_key = $this->_cache_key( $object_id, $key );
		$value     = $this->_cache->get( $cache_key, $default );
		if ( $default === $value ) {
			$value = $this->_get( $object_id, $key );
			$this->_cache->set( $cache_key, $value );
		}
		return $value;
	}

	/**
	 * Create or update an entry cache new value.
	 *
	 * @param string $object_id ID of object to store.
	 * @param string $key       Key particle for ID to store.
	 * @param mixed  $value     Serializable value to store.
	 *
	 * @return bool Success.
	 */
	final public function set( $object_id, $key, $value ) {
		if ( ! $this->get( $object_id, $key ) ) {
			if ( ! $this->_add( $object_id, $key, $value ) ) {
				return false;
			}
		} else {
			if ( ! $this->_update( $object_id, $key, $value ) ) {
				return false;
			}
		}
		$this->_cache->set( $this->_cache_key( $object_id, $key ), $value );
		return true;
	}

	/**
	 * Remove object entry based on ID and key.
	 *
	 * @param string $object_id ID of object to remove.
	 * @param string $key       Key particle for ID to remove.
	 *
	 * @return bool Success.
	 */
	final public function delete( $object_id, $key ) {
		$this->_cache->delete( $this->_cache_key( $object_id, $key ) );
		return $this->_delete( $object_id, $key );
	}

	/**
	 * Get object value from actual store.
	 *
	 * @param string $object_id ID of object to get.
	 * @param string $key       Key particle for ID to get.
	 *
	 * @return mixed Value as found.
	 */
	protected function _get( $object_id, $key ) {
		$function = 'get_' . $this->_object . '_meta';
		return $function( $object_id, $key, true );
	}

	/**
	 * Create new entry if it does not exist.
	 *
	 * @param string $object_id ID of object to store.
	 * @param string $key       Key particle for ID to store.
	 * @param mixed  $value     Serializable value to store.
	 *
	 * @return bool Success.
	 */
	protected function _add( $object_id, $key, $value ) {
		$function = 'add_' . $this->_object . '_meta';
		return $function( $object_id, $key, $value, true );
	}

	/**
	 * Update existing entry.
	 *
	 * @param string $object_id ID of object to store.
	 * @param string $key       Key particle for ID to store.
	 * @param mixed  $value     Serializable value to store.
	 *
	 * @return bool Success.
	 */
	protected function _update( $object_id, $key, $value ) {
		$function = 'update_' . $this->_object . '_meta';
		return $function( $object_id, $key, $value );
	}

	/**
	 * Remove object entry based on ID and key.
	 *
	 * @param string $object_id ID of object to remove.
	 * @param string $key       Key particle for ID to remove.
	 *
	 * @return bool Success.
	 */
	protected function _delete( $object_id, $key ) {
		$function = 'delete_' . $this->_object . '_meta';
		return $function( $object_id, $key );
	}

	/**
	 * Generate key for use with cache engine.
	 *
	 * @param string $object_id ID of object.
	 * @param string $key       Key particle for ID.
	 *
	 * @return string Single identifier for given keys.
	 */
	protected function _cache_key( $object_id, $key ) {
		static $separator = "\0";
		return $object_id . $separator . $key;
	}

}