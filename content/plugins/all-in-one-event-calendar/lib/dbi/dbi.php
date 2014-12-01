<?php

/**
 * Wrapper for WPDB (WordPress DB Class)
 *
 * Thic class wrap the access to WordPress DB class ($wpdb) and
 * allows us to abstract from the WordPress code and to expand it
 * with convenience method specific for ai1ec
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Dbi
 */
class Ai1ec_Dbi {

	/**
	 * @var Ai1ec_Registry_Object Instance of object registry.
	 */
	protected $_registry = null;

	/**
	 * @var wpdb Instance of database interface object
	 */
	protected $_dbi = null;

	/**
	 * @var array Queries executed for log.
	 */
	protected $_queries = array();

	/**
	 * @var bool Set to true when logging is enabled.
	 */
	protected $_log_enabled = false;

	/**
	 * Constructor assigns injected database access object to class variable.
	 *
	 * @param Ai1ec_Registry_Object $registry Injected registry.
	 * @param wpdb                  $dbi      Injected database access object.
	 *
	 * @return void Constructor does not return.
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		$dbi = null
	) {
		if ( null === $dbi ) {
			global $wpdb;
			$dbi = $wpdb;
		}
		$this->_dbi      = $dbi;
		$this->_registry = $registry;
		$this->_registry->get( 'controller.shutdown' )->register(
			array( $this, 'shutdown' )
		);
		add_action(
			'ai1ec_loaded',
			array( $this, 'check_debug' ),
			PHP_INT_MAX
		);
		$this->set_timezone();
	}

	/**
	 * Set timezone to UTC to avoid conversion errors.
	 *
	 * @return void
	 */
	public function set_timezone() {
		$this->_dbi->query( "SET time_zone = '+0:00'" );
	}

	/**
	 * Call explicitly when debug output must be disabled.
	 *
	 * @return void Method is not meant to return.
	 */
	public function disable_debug() {
		$this->_log_enabled = false;
	}

	/**
	 * Only attempt to enable debug after all add-ons are loaded.
	 *
	 * @wp_hook ai1ec_loaded
	 *
	 * @uses apply_filters ai1ec_dbi_debug
	 *
	 * @return void
	 */
	public function check_debug() {
		$this->_log_enabled = apply_filters(
			'ai1ec_dbi_debug',
			( false !== AI1EC_DEBUG )
		);
	}

	/**
	 * Perform a MySQL database query, using current database connection.
	 *
	 * @param string $sql_query Database query
	 *
	 * @return int|false Number of rows affected/selected or false on error
	 */
	public function query( $sql_query ) {
		$this->_query_profile( $sql_query );
		$result = $this->_dbi->query( $sql_query );
		$this->_query_profile( $result );
		return $result;
	}

	/**
	 * Retrieve one column from the database.
	 *
	 * Executes a SQL query and returns the column from the SQL result.
	 * If the SQL result contains more than one column, this function returns the column specified.
	 * If $query is null, this function returns the specified column from the previous SQL result.
	 *
	 * @param string|null $query Optional. SQL query. Defaults to previous query.
	 * @param int         $col   Optional. Column to return. Indexed from 0.
	 *
	 * @return array Database query result. Array indexed from 0 by SQL result row number.
	 */
	public function get_col( $query = null , $col = 0 ) {
		$this->_query_profile( $query );
		$result = $this->_dbi->get_col( $query, $col );
		$this->_query_profile( count( $result ) );
		return $result;
	}

	/**
	 * Check if the terms variable is set in the Wpdb object
	 */
	public function are_terms_set() {
		return isset( $this->_dbi->terms );
	}

	/**
	 * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
	 *
	 * The following directives can be used in the query format string:
	 *   %d (integer)
	 *   %f (float)
	 *   %s (string)
	 *   %% (literal percentage sign - no argument needed)
	 *
	 * All of %d, %f, and %s are to be left unquoted in the query string and they need an argument passed for them.
	 * Literals (%) as parts of the query must be properly written as %%.
	 *
	 * This function only supports a small subset of the sprintf syntax; it only supports %d (integer), %f (float), and %s (string).
	 * Does not support sign, padding, alignment, width or precision specifiers.
	 * Does not support argument numbering/swapping.
	 *
	 * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
	 *
	 * Both %d and %s should be left unquoted in the query string.
	 *
	 * @param string $query Query statement with sprintf()-like placeholders
	 * @param array|mixed $args The array of variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the query's placeholders if
	 * 	being called like {@link http://php.net/sprintf sprintf()}.
	 * @param mixed $args,... further variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/sprintf sprintf()}.
	 *
	 * @return null|false|string Sanitized query string, null if there is no query, false if there is an error and string
	 * 	if there was something to prepare
	 */
	public function prepare( $query, $args ) {

		if ( null === $query ) {
			return null;
		}

		$args = func_get_args();
		array_shift( $args );
		// If args were passed as an array (as in vsprintf), move them up
		if ( isset( $args[0] ) && is_array( $args[0] ) ) {
			$args = $args[0];
		}
		$query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
		$query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
		$query = preg_replace( '|(?<!%)%f|', '%F', $query ); // Force floats to be locale unaware
		$query = preg_replace( '|(?<!%)%s|', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
		array_walk( $args, array( $this->_dbi, 'escape_by_ref' ) );
		return @vsprintf( $query, $args );
	}

	/**
	 * Retrieve an entire SQL result set from the database (i.e., many rows)
	 *
	 * Executes a SQL query and returns the entire SQL result.
	 *
	 * @param string $query SQL query.
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants. With one of the first three, return an array of rows indexed from 0 by SQL result row number.
	 * 	Each row is an associative array (column => value, ...), a numerically indexed array (0 => value, ...), or an object. ( ->column = value ), respectively.
	 * 	With OBJECT_K, return an associative array of row objects keyed by the value of each row's first column's value. Duplicate keys are discarded.
	 *
	 * @return mixed Database query results
	 */
	public function get_results( $query, $output = OBJECT ){
		$this->_query_profile( $query );
		$result = $this->_dbi->get_results( $query, $output );
		$this->_query_profile( count( $result ) );
		return $result;
	}

	/**
	 * Retrieve one variable from the database.
	 *
	 * Executes a SQL query and returns the value from the SQL result.
	 * If the SQL result contains more than one column and/or more than one row, this function returns the value in the column and row specified.
	 * If $query is null, this function returns the value in the specified column and row from the previous SQL result.
	 *
	 * @param string|null $query SQL query. Defaults to null, use the result from the previous query.
	 * @param int         $col   Column of value to return. Indexed from 0.
	 * @param int         $row   Row of value to return. Indexed from 0.
	 *
	 * @return string|null Database query result (as string), or null on failure
	 */
	public function get_var( $query = null, $col = 0, $row = 0 ) {
		$this->_query_profile( $query );
		$result = $this->_dbi->get_var( $query, $col, $row );
		$this->_query_profile( null !== $result );
		return $result;
	}

	/**
	 * Retrieve one row from the database.
	 *
	 * Executes a SQL query and returns the row from the SQL result
	 *
	 * @param string|null $query SQL query.
	 * @param string $output Optional. one of ARRAY_A | ARRAY_N | OBJECT constants. Return an associative array (column => value, ...),
	 * 	a numerically indexed array (0 => value, ...) or an object ( ->column = value ), respectively.
	 * @param int $row Optional. Row to return. Indexed from 0.
	 *
	 * @return mixed Database query result in format specified by $output or null on failure
	 */
	public function get_row( $query = null, $output = OBJECT, $row = 0 ) {
		$this->_query_profile( $query );
		$result = $this->_dbi->get_row( $query, $output, $row );
		$this->_query_profile( null !== $result );
		return $result;
	}

	/**
	 * Insert a row into a table.
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 *
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert( $table, $data, $format = null ) {
		$this->_query_profile(
			'INSERT INTO ' . $table . '; data: ' . json_encode( $data )
		);
		$result = $this->_dbi->insert(
			$this->get_table_name( $table ),
			$data,
			$format
		);
		$this->_query_profile( $result );
		return $result;
	}

	/**
	 * Perform removal from table.
	 *
	 * @param string $table  Table to remove from.
	 * @param array  $where  Where conditions
	 * @param array  $format Format entities for where.
	 *
	 * @return int|false Number of rows deleted or false.
	 */
	public function delete( $table, $where, $format = null ) {
		$this->_query_profile(
			'DELETE FROM ' . $table . '; conditions: ' . json_encode( $where )
		);
		$result = $this->_dbi->delete(
			$this->get_table_name( $table ),
			$where,
			$format
		);
		$this->_query_profile( $result );
		return $result;
	}

	/**
	 * Update a row in the table
	 *
	 * @param string $table table name
	 * @param array $data Data to update (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be joined with ANDs. Both $where columns and $where values should be "raw".
	 * @param array|string $format Optional. An array of formats to be mapped to each of the values in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where. If string, that format will be used for all of the items in $where. A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $where will be treated as strings.
	 *
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function update( $table, $data, $where, $format = null, $where_format = null ) {
		$this->_query_profile( 'UPDATE ' . $table . ': ' . implode( '//', $data ) );
		$result = $this->_dbi->update( $table, $data, $where, $format, $where_format );
		$this->_query_profile( $result );
		return $result;
	}

	/**
	 * Retrieve all results from given table.
	 *
	 * @param string $table   Name of table.
	 * @param array  $columns List of columns to retrieve.
	 * @param string $output  See {@see self::get_results()} $output for more.
	 *
	 * @return array Collection.
	 */
	public function select( $table, array $columns, $output = OBJECT ) {
		$sql_query = 'SELECT `' . implode( '`, `', $columns ) . '` FROM `' .
			$this->get_table_name( $table ) . '`';
		return $this->get_results( $sql_query, $output );
	}

	/**
	 * The database version number.
	 *
	 * @return false|string false on failure, version number on success
	 */
	public function db_version() {
		return $this->_dbi->db_version();
	}

	/**
	 * Return the id of last `insert` operation.
	 *
	 * @return int Returns integer optionally zero when no insert was performed.
	 */
	public function get_insert_id() {
		return $this->_dbi->insert_id;
	}

	/**
	 * Return the full name for the table.
	 *
	 * @param string $table Table name.
	 *
	 * @return string Full table name for the table requested.
	 */
	public function get_table_name( $table = '' ) {
		static $prefix_len = null;
		if ( ! isset( $this->_dbi->{$table} ) ) {
			if ( null === $prefix_len ) {
				$prefix_len = strlen( $this->_dbi->prefix );
			}
			if ( 0 === strncmp( $this->_dbi->prefix, $table, $prefix_len ) ) {
				return $table;
			}
			return $this->_dbi->prefix . $table;
		}
		return $this->_dbi->{$table};
	}

	/**
	 * Return escaped value.
	 *
	 * @param string $input Value to be escaped.
	 *
	 * @return string Escaped value.
	 */
	public function escape( $input ) {
		$this->_dbi->escape_by_ref( $input );
		return $input;
	}

	/**
	 * In debug mode prints DB queries table.
	 *
	 * @return void
	 */
	public function shutdown() {
		if ( ! $this->_log_enabled ) {
			return false;
		}
		echo '<div class="timely timely-debug">
		  <table class="table table-striped">
		    <thead>
		      <tr>
		        <th>N.</th>
		        <th>Query</th>
		        <th>Duration, ms</th>
		        <th>Row Count</th>
		      </tr>
		    </thead>
		    <tbody>';
		$i    = 0;
		$time = 0;
		foreach ( $this->_queries as $query ) {
			$time += $query['d'];
			echo '<tr>
			        <td>', ++$i, '</td>
			        <td>', $query['q'], '</td>
			        <td>', round( $query['d'] * 1000, 2 ), '</td>
			        <td>', (int)$query['r'], '</td>
			      </tr>';
		}
		echo '
		    </tbody>
            <tfoot>
              <tr>
                <th colspan="4">Total time, ms: ',
				round( $time * 1000, 2 ), '</th>
              </tr>
            </tfoot>
		  </table>
		</div>';
		return true;
	}

	/**
	 * Method aiding query profiling.
	 *
	 * How to use:
	 * - on method resulting in query start call _query_profiler( 'SQL query' )
	 * - on it's end call _query_profiler( (int)number_of_rows|(bool)false )
	 *
	 * @param mixed $query_or_result Query on first call, result on second.
	 *
	 * @return void
	 */
	protected function _query_profile( $query_or_result ) {
		static $last = null;
		if ( null === $last ) {
			$last = array(
				'd' => microtime( true ),
				'q' => $query_or_result,
			);
		} else {
			if ( count( $this->_queries ) > 200 ) {
				array_shift( $this->_queries );
			}
			$this->_queries[] = array(
				'd' => microtime( true ) - $last['d'],
				'q' => $last['q'],
				'r' => $query_or_result,
			);
			$last = null;
		}
	}

}