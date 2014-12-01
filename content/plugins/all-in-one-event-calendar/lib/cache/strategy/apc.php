<?php

/**
 * Concrete class for APC caching strategy.
 *
 * @instantiator new
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Cache.Strategy
 */
class Ai1ec_Cache_Strategy_Apc extends Ai1ec_Cache_Strategy {

	/**
	 * is_available method
	 *
	 * Checks if APC is available for use.
	 * Following pre-requisites are checked: APC functions availability,
	 * APC is enabled via configuration and PHP is not running in CGI.
	 *
	 * @return bool Availability
	 */
	static public function is_available() {
		return function_exists( 'apc_store' ) &&
		       function_exists( 'apc_fetch' ) &&
		       ini_get( 'apc.enabled' ) &&
			   ( false === strpos( php_sapi_name(), 'cgi' ) );
	}

	/**
	 *
	 * @see Ai1ec_Get_Data_From_Cache::get_data()
	 *
	 */
	public function get_data( $dist_key ) {
		$key  = $this->_key( $dist_key );
		$data = apc_fetch( $key );
		if ( false === $data ) {
			throw new Ai1ec_Cache_Not_Set_Exception( "$dist_key not set" );
		}
		return $data;
	}

	/**
	 *
	 * @see Ai1ec_Write_Data_To_Cache::write_data()
	 *
	 */
	public function write_data( $dist_key, $value ) {
		$key          = $this->_key( $dist_key );
		$store_method = 'apc_add';
		if ( false !== ( $existing = apc_fetch( $key ) ) ) {
			if ( $value === $existing ) {

				return true;
			}
			$store_method = 'apc_store';
		} elseif ( false === function_exists( $store_method ) ) {

			$store_method = 'apc_store';
		}
		if ( false === $store_method( $key, $value ) ) {
			try {
				if ( $value !== $this->get_data( $key ) ) {
					throw new Ai1ec_Cache_Not_Set_Exception( 'Data mis-match' );
				}
			} catch ( Ai1ec_Cache_Not_Set_Exception $excpt ) {

				throw new Ai1ec_Cache_Not_Set_Exception(
					'Failed to write ' . $dist_key . ' to APC cache'
				);
			}
		}
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Write_Data_To_Cache::delete_data()
	 */
	public function delete_data( $key ) {
		if ( false === apc_delete( $this->_key( $key ) ) ) {
			return false;
		}
		return true;
	}

	/**
	 *
	 * @see Ai1ec_Write_Data_To_Cache::delete_matching()
	 */
	public function delete_matching( $pattern ) {
		// not implemented - concider flushing APC cache
		return 0;
	}

	/**
	 * _key method
	 *
	 * Make sure we are on the safe side - in case of multi-instances
	 * environment some prefix is required.
	 *
	 * @param string $key Key to be used against APC cache
	 *
	 * @return string Key with prefix prepended
	 */
	protected function _key( $key ) {
		static $prefix = NULL;
		if ( NULL === $prefix ) {
			$prefix = substr( md5( site_url() ), 0, 8 );
		}
		if ( 0 !== strncmp( $key, $prefix, 8 ) ) {
			$key = $prefix . $key;
		}
		return $key;
	}

}
