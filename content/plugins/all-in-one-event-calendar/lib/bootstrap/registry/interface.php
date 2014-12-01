<?php

/**
 * The basic registry interface.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Object
 */
interface Ai1ec_Registry {

	/**
	 * Retrieves the key from the registry
	 *
	 * @param string $key
	 *
	 * @return mixed the value associated to the key.
	 */
	public function get( $key );

	/**
	 * Set the key into the registry.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $key, $value );
}