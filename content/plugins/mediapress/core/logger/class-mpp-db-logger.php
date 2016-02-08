<?php

/**
 * Logger Implemetation that uses local database tos tore the logs
 * 
 * Do not use this class directly, see mediapress/core/logger/functions.php for the API
 * 
 */
class MPP_DB_Logger extends MPP_Logger {

	private static $instance = null;
	private $table = '';

	private function __construct() {

		$this->table = mediapress()->get_table_name( 'logs' );
	}

	/**
	 * Get singleton instance
	 * 
	 * @return MPP_DB_Logger
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Logs an entry
	 * 
	 * @param type $args
	 * @return booleaan|int false on failure else log id
	 */
	public function log( $args ) {

		global $wpdb;

		if ( ! empty( $args['id'] ) ) {
			return $this->update( $args );
		}

		$default = array(
			'user_id'	=> 0,
			'item_id'	=> '',
			'action'	=> '',
			//'logged_at'	=> current_time( $type ),
			'value'		=> '',
		);

		$args = wp_parse_args( $args, $default );
		//only pick the fields our table has
		$args = $this->get_fields( $default, $args );

		if ( false === $wpdb->insert( $this->table, $args, array( '%d', '%d', '%s', '%s' ) ) ) {
			return false;
		}

		$log_id = $wpdb->insert_id;

		return $log_id;
	}

	private function update( $args ) {

		if ( ! isset( $args['id'] ) ) {
			return false;
		}

		global $wpdb;

		$logs = $this->get( array( 'id' => $args['id'] ) );

		if ( empty( $logs ) ) {
			return 0; //no raw affected
		}

		$log = array_pop( $logs );

		if ( ! empty( $args['user_id'] ) ) {
			$log->user_id = absint( $args['user_id'] );
		}
		if ( ! empty( $args['item_id'] ) ) {
			$log->item_id = absint( $args['item_id'] );
		}

		if ( ! empty( $args['action'] ) ) {
			$log->action = $args['action'];
		}

		if ( ! empty( $args['value'] ) ) {
			$log->value = $args['value'];
		}

		$fields = get_object_vars( $log );

		//unset( $fields['id'] );

		return $this->save( $fields ); //$wpdb->update( $this->table, $fields, array( 'id' => $args['id'] ), array( '%d', '%d', '%s', '%s' ), array( '%d' ) );
	}

	/**
	 * Increment the value field by given
	 * 
	 * @param type $args
	 * @param type $by
	 */
	public function increment( $args, $by = 1 ) {
		global $wpdb;

		$log = $this->log_exists( $args );

		if ( ! $log ) {
			//does not exist, we need to create an entry
			$args['value'] = $by;

			return $this->log( $args );
		}

		//if we are here, the log exists
		$log->value = $log->value + $by;

		$fields = get_object_vars( $log );

		//unset( $fields['id'] );
		//$wpdb->update uses 2 queries, let us save 1
		return $this->save( $fields ); //$wpdb->update( $this->table, $fields, array( 'id' => $log->id ), array( '%d', '%d', '%s', '%s' ), array( '%d' ) );
	}

	/**
	 * Save/Update fields to database
	 * 
	 * We decided to drop $wpdb->update and use it instead to save a query
	 * 
	 * @global WPDB $wpdb
	 * @param array $args
	 * @return boolean
	 */
	public function save( $args ) {

		global $wpdb;
		if ( $args['id'] ) {

			$query = "UPDATE {$this->table}	SET
						user_id = %d,
						item_id = %d,
						action	= %s,
						value	= %s
					WHERE id	= %d ";
			
			$query = $wpdb->prepare( $query, $args['user_id'], $args['item_id'], $args['action'], $args['value'], $args['id'] );
			
		} else {

			$query = "INSERT INTO {$this->table}	SET
							user_id = %d,
							item_id = %d,
							action	= %s,
							value	= %s";
			
			$query = $wpdb->prepare( $query, $args['user_id'], $args['item_id'], $args['action'], $args['value'] );
		}

		if ( false === $wpdb->query( $query ) ) {
			return false;
		}

		return $wpdb->insert_id;
	}

	/**
	 * Delete one or more log
	 * 
	 * @global type $wpdb
	 * @param type $args
	 * @return boolean
	 */
	public function delete( $args ) {

		global $wpdb;

		$where_conditions = $this->build_conditions( $args );

		if ( empty( $where_conditions ) ) {
			return false;
		}

		$where_sql = join( ' AND ', $where_conditions );

		$query = "DELETE FROM {$this->table} WHERE {$where_sql}";

		return $wpdb->query( $query );
	}

	/**
	 * Check if log exists?
	 * 
	 * @param type $args
	 * @return boolean
	 */
	public function log_exists( $args ) {
		global $wpdb;

		$conditions = $this->build_conditions( $args );

		if ( empty( $conditions ) ) {
			return false;
		}

		$where_sql = join( ' AND ', $conditions );

		$query = "SELECT * FROM {$this->table} WHERE {$where_sql} LIMIT 0, 1";

		$log = $wpdb->get_row( $query );

		return $log;
	}

	public function get( $args ) {

		global $wpdb;

		$conditions = $this->build_conditions( $args );

		$per_page = isset( $args['per_page'] ) ? absint( $args['per_page'] ) : 0;
		$page = isset( $args['page'] ) ? absint( $args['page'] ) : 1;

		$limitby_sql = '';

		if ( $per_page && $page ) {
			$limitby_sql = $wpdb->peraper( "LIMIT %d, %d ", ( $page - 1 ) * $per_page, $per_page );
		}

		$where_sql = join( ' AND ', $conditions );

		if ( $where_sql ) {
			$where_sql = ' WHERE ' . $where_sql;
		}



		$orderby = isset( $args['orderby'] ) ? $args['orderby'] : 'logged_at';
		$order = isset( $args['order'] ) ? $args['order'] : 'DESC';

		$orderby_sql = "ORDER BY {$orderby} {$order}";
		$query = "SELECT * FROM {$this->table} {$where_sql} {$limitby_sql} {$orderby_sql}";

		return $wpdb->get_results( $query );
	}

	public function get_where_sql( $args ) {
		;
	}

	private function build_conditions( $args ) {

		global $wpdb;
		$where_conditions = array();

		if ( ! empty( $args['id'] ) ) {
			$where_conditions[] = $wpdb->prepare( 'id = %d', $args['id'] );
		}

		if ( ! empty( $args['user_id'] ) ) {
			$where_conditions[] = $wpdb->prepare( 'user_id = %d ', $args['user_id'] );
		}

		if ( ! empty( $args['item_id'] ) ) {
			$where_conditions[] = $wpdb->prepare( 'item_id = %d', $args['item_id'] );
		}

		if ( ! empty( $args['action'] ) ) {
			$where_conditions[] = $wpdb->prepare( 'action = %s', $args['action'] );
		}

		if ( ! empty( $args['value'] ) ) {
			if ( ! empty( $args['operator'] ) ) {
				
			}
		}

		return $where_conditions;
	}

	private function get_fields( $fields, $args ) {

		$picked = array();

		foreach ( $fields as $name => $default ) {

			if ( isset( $args[ $name ] ) ) {
				$picked[ $name ] = $args[ $name ];
			} else {
				$picked[ $name ] = $default;
			}
		}

		return $picked;
	}

}
