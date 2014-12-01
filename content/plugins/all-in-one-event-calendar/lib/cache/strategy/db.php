<?php

/**
 * Concrete class for DB caching strategy.
 *
 * @instantiator new
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Cache.Strategy
 */
class Ai1ec_Cache_Strategy_Db extends Ai1ec_Cache_Strategy {

	/**
	 * @var Ai1ec_Option Instance of database adapter
	 */
	private $model_option;

	public function __construct( Ai1ec_Registry_Object $registry, Ai1ec_Option $option ) {
		parent::__construct( $registry );
		$this->model_option = $option;
	}

	/**
	 *
	 * @see Ai1ec_Get_Data_From_Cache::get_data()
	 *
	 */
	public function get_data( $key ) {
		$key  = $this->_key( $key );
		$data = $this->model_option->get( $key );
		if ( false === $data ) {
			throw new Ai1ec_Cache_Not_Set_Exception(
				'No data under \'' . $key . '\' present'
			);
		}
		return maybe_unserialize( $data );
	}

	/**
	 *
	 * @see Ai1ec_Write_Data_To_Cache::write_data()
	 *
	 */
	public function write_data( $key, $value ) {
		$result = $this->model_option->set(
			$this->_key( $key ),
			maybe_serialize( $value )
		);
		if ( false === $result ) {
			throw new Ai1ec_Cache_Write_Exception(
				'An error occured while saving data to \'' . $key . '\''
			);
		}
	}

	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Write_Data_To_Cache::delete_data()
	 */
	public function delete_data( $key ) {
		return $this->model_option->delete(
			$this->_key( $key )
		);
	}

	/**
	 *
	 * @see Ai1ec_Write_Data_To_Cache::delete_matching()
	 */
	public function delete_matching( $pattern ) {
		$db = $this->_registry->get( 'dbi.dbi' );
		$sql_query = $db->prepare(
			'SELECT option_name FROM ' . $db->get_table_name( 'options' ) .
			' WHERE option_name LIKE %s',
			'%%' . $pattern . '%%'
		);
		$keys = $db->get_col( $sql_query );
		foreach ( $keys as $key ) {
			$this->model_option->delete( $key );
		}
		return count( $keys );
	}

	/**
	 * _key method
	 *
	 * Get safe key name to use within options API
	 *
	 * @param string $key Key to sanitize
	 *
	 * @return string Safe to use key
	 */
	protected function _key( $key ) {
		if ( strlen( $key ) > 53 ) {
			$hash = md5( $key );
			$key  = substr( $key, 0, 16 ) . '_' . $hash;
		}
		return $key;
	}

}
