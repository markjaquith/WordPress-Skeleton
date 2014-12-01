<?php

/**
 * Base class for caching strategy.
 *
 *
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Cache.Strategy
 */
abstract class Ai1ec_Cache_Strategy extends Ai1ec_Base {

	/**
	 * Retrieves the data store for the passed key
	 *
	 * @param string $key
	 * @throws Ai1ec_Cache_Not_Set_Exception if the key was not set
	 */
	abstract public function get_data( $key );

	/**
	 * Write the data to the persistence Layer
	 *
	 * @throws Ai1ec_Cache_Write_Exception
	 * @param string $key
	 * @param string $value
	 */
	abstract public function write_data( $key, $value );

	/**
	 * Deletes the data associated with the key from the persistence layer.
	 *
	 * @param string $key
	 */
	abstract public function delete_data( $key );

	/**
	 * Delete multiple cache entries matching given pattern
	 *
	 * @param string $pattern Scalar pattern, which shall match key
	 *
	 * @return int Count of entries deleted
	 */
	abstract public function delete_matching( $pattern );

}
