<?php

/**
 * Concrete class for void caching strategy.
 *
 * @instantiator new
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Cache.Strategy
 */
class Ai1ec_Cache_Strategy_Void extends Ai1ec_Cache_Strategy {

	/**
	 * Checks if engine is available
	 *
	 * @return bool Always true
	 */
	static public function is_available() {
		return true;
	}

	/**
	 *
	 * @see Ai1ec_Get_Data_From_Cache::get_data()
	 *
	 */
	public function get_data( $dist_key ) {
		throw new Ai1ec_Cache_Not_Set_Exception( "'$dist_key' not set" );
	}

	/**
	 *
	 * @see Ai1ec_Write_Data_To_Cache::write_data()
	 *
	 */
	public function write_data( $dist_key, $value ) {
		throw new Ai1ec_Cache_Not_Set_Exception(
			'Failed to write \'' . $dist_key . '\' to void cache'
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Write_Data_To_Cache::delete_data()
	 */
	public function delete_data( $key ) {
		return false;
	}

	/**
	 *
	 * @see Ai1ec_Write_Data_To_Cache::delete_matching()
	 */
	public function delete_matching( $pattern ) {
		return 0;
	}

}
