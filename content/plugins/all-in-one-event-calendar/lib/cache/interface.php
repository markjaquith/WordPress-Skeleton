<?php

/**
 * Interface for cache engines.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Cache
 */
interface Ai1ec_Cache_Interface {

	/**
	 * Set entry to cache.
	 *
	 * @param string $key   Key for which value must be stored.
	 * @param mixed  $value Actual value to store.
	 *
	 * @return bool Success.
	 */
	public function set( $key, $value );

	/**
	 * Add entry to cache if one does not exist.
	 *
	 * @param string $key   Key for which value must be stored.
	 * @param mixed  $value Actual value to store.
	 *
	 * @return bool Success.
	 */
	public function add( $key, $value );

	/**
	 * Retrieve value from cache.
	 *
	 * @param string $key     Key for which to retrieve value.
	 * @param mixed  $default Value to return if none found.
	 *
	 * @return mixed Previously stored or $default value.
	 */
	public function get( $key, $default = NULL );

	/**
	 * Delete value from cache.
	 *
	 * @param string $key Key for value to remove.
	 *
	 * @return bool Success.
	 */
	public function delete( $key );

}