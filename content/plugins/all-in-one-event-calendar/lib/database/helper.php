<?php

/**
 * Ai1ec_Database class
 *
 * Class responsible for generic database operations
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Database
 */
class Ai1ec_Database_Helper {

	/**
	 * @var array Map of tables and their parsed definitions
	 */
	protected $_schema_delta = array();

	/**
	 * @var array List of valid table prefixes
	 */
	protected $_prefixes     = array();

	/**
	 * @var wpdb Localized instance of wpdb object
	 */
	protected $_db = NULL;

	/**
	 * @var bool If set to true - no operations will be performed
	 */
	protected $_dry_run  = false;

	/**
	 * Constructor
	 *
	 * @param Ai1ec_Registry_Object $registry
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		$this->_db       = $registry->get( 'dbi.dbi' );
		$this->_prefixes = array(
			$this->_db->get_table_name( 'ai1ec_' ),
			$this->_db->get_table_name(),
			'',
		);
	}

	/**
	 * Check if dry run is enabled
	 *
	 * @param bool $dry Change dryness [optional=NULL]
	 *
	 * @return bool Dryness of run or previous value
	 */
	public function is_dry( $dry = NULL ) {
		if ( NULL !== $dry ) {
			$previous = $this->_dry_run;
			$this->_dry_run = (bool)$dry;
			return $previous;
		}
		return $this->_dry_run;
	}

	/**
	 * Get fully-qualified table name given it's abbreviated form
	 *
	 * @param string $name         Name (abbreviation) of table to check
	 * @param bool   $ignore_check Return longest name if no table exist [false]
	 *
	 * @return string Fully-qualified table name
	 *
	 * @throws Ai1ec_Database_Schema_Exception If no table matches
	 */
	public function table( $name, $ignore_check = false ) {
		$existing  = $this->get_all_tables();
		$table     = NULL;
		$candidate = NULL;
		$name      = $name;
		foreach ( $this->_prefixes as $prefix ) {
			$candidate = $prefix . $name;
			$index     = strtolower( $candidate );
			if ( isset( $existing[$index] ) ) {
				$table = $existing[$index];
				break;
			}
		}
		if ( NULL === $table ) {
			if ( true === $ignore_check ) {
				return $candidate;
			}
			throw new Ai1ec_Database_Schema_Exception(
				'Table \'' . $name . '\' does not exist'
			);
		}
		return $table;
	}

	/**
	 * Drop given indices from table
	 *
	 * @param string       $table   Name of table to modify
	 * @param string|array $indices List, or single, of indices to remove
	 *
	 * @return bool Success
	 *
	 * @throws Ai1ec_Database_Schema_Exception If table is not found
	 */
	public function drop_indices( $table, $indices ) {
		if ( ! is_array( $indices ) ) {
			$indices = array( (string)$indices );
		}
		$table    = $this->table( $table );
		$existing = $this->get_indices( $table );
		$removed  = 0;
		foreach ( $indices as $index ) {
			if (
				! isset( $existing[$index] ) ||
				$this->_dry_query(
					'ALTER TABLE ' . $table . ' DROP INDEX ' . $index
				)
			) {
				++$removed;
			}
		}
		return ( count( $indices ) === $removed );
	}

	/**
	 * Create indices for given table
	 *
	 * Input ({@see $indices}) must be the same, as output of
	 * method {@see self::get_indices()}.
	 *
	 * @param string $table   Name of table to create indices for
	 * @param array  $indices Indices representation to be created
	 *
	 * @return bool Success
	 *
	 * @throws Ai1ec_Database_Schema_Exception If table is not found
	 */
	public function create_indices( $table, array $indices ) {
		$table = $this->table( $table );
		foreach ( $indices as $name => $definition ) {
			$query = 'ALTER TABLE ' . $table . ' ADD ';
			if ( $definition['unique'] ) {
				$query .= 'UNIQUE ';
			}
			$query .= 'KEY ' . $name . ' (' .
				implode( ', ', $definition['columns'] ) .
				')';
			if ( ! $this->_dry_query( $query ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * get_indices method
	 *
	 * Get map of indices defined for table.
	 *
	 * @NOTICE: no optimization will be performed here, and response will not
	 * be cached, to allow checking result of DDL statements.
	 *
	 * Returned array structure (example):
	 * array(
	 *     'index_name' => array(
	 *         'name'    => 'index_name',
	 *         'columns' => array(
	 *             'column1',
	 *             'column2',
	 *             'column3',
	 *         ),
	 *         'unique'  => true,
	 *     ),
	 * )
	 *
	 * @param string $table Name of table to retrieve index names for
	 *
	 * @return array Map of index names and their representation
	 *
	 * @throws Ai1ec_Database_Schema_Exception If table is not found
	 */
	public function get_indices( $table ) {
		$sql_query = 'SHOW INDEXES FROM ' . $this->table( $table );
		$result    = $this->_db->get_results( $sql_query );
		$indices   = array();
		foreach ( $result as $index ) {
			$name = $index->Key_name;
			if ( ! isset( $indices[$name] ) ) {
				$indices[$name] = array(
					'name'    => $name,
					'columns' => array(),
					'unique'  => ! (bool)intval( $index->Non_unique ),
				);
			}
			$indices[$name]['columns'][$index->Column_name] = $index->Sub_part;
		}
		return $indices;
	}

	/**
	 * Perform query, unless `dry_run` is selected. In later case just output
	 * the final query and return true.
	 *
	 * @param string $query SQL Query to execute
	 *
	 * @return mixed Query state, or true in dry run mode
	 */
	public function _dry_query( $query ) {
		if ( $this->is_dry() ) {
			pr( $query );
			return true;
		}
		$result = $this->_db->query( $query );
		if ( AI1EC_DEBUG ) {
			echo '<h4>', $query, '</h4><pre>', var_export( $result, true ), '</pre>';
		}
		return $result;
	}

	/**
	 * Check if given table exists
	 *
	 * @param string $table Name of table to check
	 *
	 * @return bool Existance
	 */
	public function table_exists( $table ) {
		$map = $this->get_all_tables();
		return isset( $map[strtolower( $table )] );
	}

	/**
	 * Return a list of all tables currently present
	 *
	 * @return array Map of tables present
	 */
	public function get_all_tables() {
		/**
		 * @TODO: refactor using dbi.dbi::get_tables
		 */
		$sql_query = 'SHOW TABLES LIKE \'' .
			$this->_db->get_table_name() .
			'%\'';
		$result    = $this->_db->get_col( $sql_query );
		$tables    = array();
		foreach ( $result as $table ) {
			$tables[strtolower( $table )] = $table;
		}
		return $tables;
	}

	/**
	 * apply_delta method
	 *
	 * Attempt to parse and apply given database tables definition, as a delta.
	 * Some validation is made prior to calling DB, and fields/indexes are also
	 * checked for consistency after sending queries to DB.
	 *
	 * NOTICE: only "CREATE TABLE" statements are handled. Others will, likely,
	 * be ignored, if passed through this method.
	 *
	 * @param string|array $query Single or multiple queries to perform on DB
	 *
	 * @return bool Success
	 *
	 * @throws Ai1ec_Database_Error In case of any error
	 */
	public function apply_delta( $query ) {
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR .
				'includes' . DIRECTORY_SEPARATOR . 'upgrade.php';
		}
		$success = false;
		$this->_schema_delta = array();
		$queries = $this->_prepare_delta( $query );
		$result  = dbDelta( $queries );
		$success = $this->_check_delta();
		return $success;
	}

	/**
	 * get_notices_helper method
	 *
	 * DIP implementing method, to give access to Ai1ec_Deferred_Rendering_Helper.
	 *
	 * @param Ai1ec_Deferred_Rendering_Helper $replacement Notices implementor
	 *
	 * @return Ai1ec_Deferred_Rendering_Helper Instance of notices implementor
	 */
	public function get_notices_helper(
		Ai1ec_Deferred_Rendering_Helper $replacement = NULL
	) {
		static $helper = NULL;
		if ( NULL !== $replacement ) {
			$helper = $replacement;
		}
		if ( NULL === $helper ) {
			$helper = Ai1ec_Deferred_Rendering_Helper::get_instance();
		}
		return $helper;
	}

	/**
	 * _prepare_delta method
	 *
	 * Prepare statements for execution.
	 * Attempt to parse various SQL definitions and compose the one, that is
	 * most likely to be accepted by delta engine.
	 *
	 * @param string|array $queries Single or multiple queries to perform on DB
	 *
	 * @return bool Success
	 *
	 * @throws Ai1ec_Database_Error In case of any error
	 */
	protected function _prepare_delta( $queries ) {
		if ( ! is_array( $queries ) ) {
			$queries = explode( ';', $queries );
			$queries = array_filter( $queries );
		}
		$current_table = NULL;
		$ctable_regexp = '#
			\s*CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([^ ]+)`?\s*
			\((.+)\)
			([^()]*)
			#six';
		foreach ( $queries as $query ) {
			if ( preg_match( $ctable_regexp, $query, $matches ) ) {
				$this->_schema_delta[$matches[1]] = array(
					'tblname' => $matches[1],
					'cryptic'  => NULL,
					'creator'  => '',
					'columns' => array(),
					'indexes' => array(),
					'content' => preg_replace( '#`#', '', $matches[2] ),
					'clauses' => $matches[3],
				);
			}
		}
		$this->_parse_delta();
		$sane_queries = array();
		foreach ( $this->_schema_delta as $table => $definition ) {
			$create = 'CREATE TABLE ' . $table . " (\n";
			foreach ( $definition['columns'] as $column ) {
				$create .= '    ' . $column['create'] . ",\n";
			}
			foreach ( $definition['indexes'] as $index ) {
				$create .= '    ' . $index['create'] . ",\n";
			}
			$create = substr( $create, 0, -2 ) . "\n";
			$create .= ')' . $definition['clauses'];
			$this->_schema_delta[$table]['creator'] = $create;
			$this->_schema_delta[$table]['cryptic'] = md5( $create );
			$sane_queries[] = $create;
		}
		return $sane_queries;
	}

	/**
	 * _parse_delta method
	 *
	 * Parse table application (creation) statements into atomical particles.
	 * Here "atomical particles" stands for either columns, or indexes.
	 *
	 * @return void Method does not return
	 *
	 * @throws Ai1ec_Database_Error In case of any error
	 */
	protected function _parse_delta() {
		foreach ( $this->_schema_delta as $table => $definitions ) {
			$listing = explode( "\n", $definitions['content'] );
			$listing = array_filter( $listing, array( $this, '_is_not_empty_line' ) );
			$lines   = count( $listing );
			$lineno  = 0;
			foreach ( $listing as $line ) {
				++$lineno;
				$line = trim( preg_replace( '#\s+#', ' ', $line ) );
				$line_new = rtrim( $line, ',' );
				if (
					$lineno < $lines && $line === $line_new ||
					$lineno == $lines && $line !== $line_new
				) {
					throw new Ai1ec_Database_Error(
						'Missing comma in line \'' . $line . '\''
					);
				}
				$line = $line_new;
				unset( $line_new );
				$type = 'indexes';
				if ( false === ( $record = $this->_parse_index( $line ) ) ) {
					$type   = 'columns';
					$record = $this->_parse_column( $line );
				}
				if ( isset(
						$this->_schema_delta[$table][$type][$record['name']]
				) ) {
					throw new Ai1ec_Database_Error(
						'For table `' . $table . '` entry ' . $type .
						' named `' . $record['name'] . '` was declared twice' .
						' in ' . $definitions
					);
				}
				$this->_schema_delta[$table][$type][$record['name']] = $record;
			}
		}
	}

	/**
	 * _parse_index method
	 *
	 * Given string attempts to detect, if it is an index, and if yes - parse
	 * it to more navigable index definition for future validations.
	 * Creates modified index create line, for delta application.
	 *
	 * @param string $description Single "line" of CREATE TABLE statement body
	 *
	 * @return array|bool Index definition, or false if input does not look like index
	 *
	 * @throws Ai1ec_Database_Error In case of any error
	 */
	protected function _parse_index( $description ) {
		$description = preg_replace(
			'#^CONSTRAINT(\s+`?[^ ]+`?)?\s+#six',
			'',
			$description
		);
		$details     = explode( ' ', $description );
		$index       = array(
			'name'    => NULL,
			'content' => array(),
			'create'  => '',
		);
		$details[0]  = strtoupper( $details[0] );
		switch ( $details[0] ) {
			case 'PRIMARY':
				$index['name']   = 'PRIMARY';
				$index['create'] = 'PRIMARY KEY ';
				break;

			case 'UNIQUE':
				$name = $details[1];
				if (
					0 === strcasecmp( 'KEY',   $name ) ||
					0 === strcasecmp( 'INDEX', $name )
				) {
					$name = $details[2];
				}
				$index['name']   = $name;
				$index['create'] = 'UNIQUE KEY ' . $name;
				break;

			case 'KEY':
			case 'INDEX':
				$index['name']   = $details[1];
				$index['create'] = 'KEY ' . $index['name'];
				break;

			default:
				return false;
		}
		$index['content'] = $this->_parse_index_content( $description );
		$index['create'] .= ' (';
		foreach ( $index['content'] as $column => $length ) {
			$index['create'] .= $column;
			if ( NULL !== $length ) {
				$index['create'] .= '(' . $length . ')';
			}
			$index['create'] .= ',';
		}
		$index['create'] = substr( $index['create'], 0, -1 );
		$index['create'] .= ')';
		return $index;
	}

	/**
	 * _parse_column method
	 *
	 * Parse column to parseable definition.
	 * Some valid definitions may still be not recognizes (namely SET and ENUM)
	 * thus one shall beware, when attempting to create such.
	 * Create alternative create table entry line for delta application.
	 *
	 * @param string $description Single "line" of CREATE TABLE statement body
	 *
	 * @return array Column definition
	 *
	 * @throws Ai1ec_Database_Error In case of any error
	 */
	protected function _parse_column( $description ) {
		$column_regexp = '#^
			([a-z][a-z_]+)\s+
			(
				[A-Z]+
				(?:\s*\(\s*\d+(?:\s*,\s*\d+\s*)?\s*\))?
				(?:\s+UNSIGNED)?
				(?:\s+ZEROFILL)?
				(?:\s+BINARY)?
				(?:
					\s+CHARACTER\s+SET\s+[a-z][a-z_]+
					(?:\s+COLLATE\s+[a-z][a-z0-9_]+)?
				)?
			)
			(
				\s+(?:NOT\s+)?NULL
			)?
			(
				\s+DEFAULT\s+[^\s]+
			)?
			(\s+ON\s+UPDATE\s+CURRENT_(?:TIMESTAMP|DATE))?
			(\s+AUTO_INCREMENT)?
			\s*,?\s*
		$#six';
		if ( ! preg_match( $column_regexp, $description, $matches ) ) {
			throw new Ai1ec_Database_Error(
				'Invalid column description ' . $description
			);
		}
		$column = array(
			'name'    => $matches[1],
			'content' => array(),
			'create'  => '',
		);
		if ( 0 === strcasecmp( 'boolean', $matches[2] ) ) {
			$matches[2] = 'tinyint(1)';
		}
		$column['content']['type'] = $matches[2];
		$column['content']['null'] = (
			! isset( $matches[3] ) ||
			0 !== strcasecmp( 'NOT NULL', trim( $matches[3] ) )
		);
		$column['create'] = $column['name'] . ' ' . $column['content']['type'];
		if ( isset( $matches[3] ) ) {
			$column['create'] .= ' ' .
				implode(
					' ',
					array_map(
						'trim',
						array_slice( $matches, 3 )
					)
				);
		}
		return $column;
	}

	/**
	 * _parse_index_content method
	 *
	 * Parse index content, to a map of columns and their length.
	 * All index (content) cases shall be covered, although it is only tested.
	 *
	 * @param string Single line of CREATE TABLE statement, containing index definition
	 *
	 * @return array Map of columns and their length, as per index definition
	 *
	 * @throws Ai1ec_Database_Error In case of any error
	 */
	protected function _parse_index_content( $description ) {
		if ( ! preg_match( '#^[^(]+\((.+)\)$#', $description, $matches ) ) {
			throw new Ai1ec_Database_Error(
				'Invalid index description ' . $description
			);
		}
		$columns = array();
		$textual = explode( ',', $matches[1] );
		$column_regexp = '#\s*([^(]+)(?:\s*\(\s*(\d+)\s*\))?\s*#sx';
		foreach ( $textual as $column ) {
			if (
				! preg_match( $column_regexp, $column, $matches ) || (
					  isset( $matches[2] ) &&
					  (string)$matches[2] !== (string)intval( $matches[2] )
				)
			) {
				throw new Ai1ec_Database_Error(
					'Invalid index (columns) description ' . $description .
					' as per \'' . $column . '\''
				);
			}
			$matches[1] = trim( $matches[1] );
			$columns[$matches[1]] = NULL;
			if ( isset( $matches[2] ) ) {
				$columns[$matches[1]] = (int)$matches[2];
			}
		}
		return $columns;
	}

	/**
	 * _check_delta method
	 *
	 * Given parsed schema definitions (in {@see self::$_schema_delta} map) this
	 * method performs checks, to ensure that table exists, columns are of
	 * expected type, and indexes match their definition in original query.
	 *
	 * @return bool Success
	 *
	 * @throws Ai1ec_Database_Error In case of any error
	 */
	protected function _check_delta() {
		if ( empty( $this->_schema_delta ) ) {
			return true;
		}
		foreach ( $this->_schema_delta as $table => $description ) {

			$columns = $this->_db->get_results( 'SHOW FULL COLUMNS FROM ' . $table );
			if ( empty( $columns ) ) {
				throw new Ai1ec_Database_Error(
					'Required table `' . $table . '` was not created'
				);
			}
			$db_column_names = array();
			foreach ( $columns as $column ) {
				if ( ! isset( $description['columns'][$column->Field] ) ) {
					if ( $this->_db->query(
						'ALTER TABLE `' . $table .
						'` DROP COLUMN `' . $column->Field . '`'
					 ) ) {
						continue;
					}
					continue; // ignore so far
					//throw new Ai1ec_Database_Error(
					//	'Unknown column `' . $column->Field .
					//	'` is present in table `' . $table . '`'
					//);
				}
				$db_column_names[$column->Field] = $column->Field;
				$type_db = $column->Type;
				$collation = '';
				if ( $column->Collation ) {
					$collation = ' CHARACTER SET ' .
						substr(
							$column->Collation,
							0,
							strpos( $column->Collation, '_' )
						) . ' COLLATE ' . $column->Collation;
				}
				$type_req = $description['columns'][$column->Field]
					['content']['type'];
				if (
					false !== stripos(
						$type_req,
						' COLLATE '
					)
				) {
					// suspend collation checking
					//$type_db .= $collation;
					$type_req = preg_replace(
						'#^
							(.+)
							\s+CHARACTER\s+SET\s+[a-z0-9_]+
							\s+COLLATE\s+[a-z0-9_]+
							(.+)?\s*
						$#six',
						'$1$2',
						$type_req
					);
				}
				$type_db  = strtolower(
					preg_replace( '#\s+#', '', $type_db )
				);
				$type_req = strtolower(
					preg_replace( '#\s+#', '', $type_req )
				);
				if ( 0 !== strcmp( $type_db, $type_req ) ) {
					throw new Ai1ec_Database_Error(
						'Field `' . $table . '`.`' . $column->Field .
						'` is of incompatible type'
					);
				}
				if (
					'YES' === $column->Null &&
					false === $description['columns'][$column->Field]
						['content']['null'] ||
					'NO' === $column->Null &&
					true === $description['columns'][$column->Field]
						['content']['null']
				) {
					throw new Ai1ec_Database_Error(
						'Field `' . $table . '`.`' . $column->Field .
						'` NULLability is flipped'
					);
				}
			}
			if (
				$missing = array_diff(
					array_keys( $description['columns'] ),
					$db_column_names
				)
			) {
					throw new Ai1ec_Database_Error(
						'In table `' . $table . '` fields are missing: ' .
						implode( ', ', $missing )
					);
			}

			$indexes = $this->get_indices( $table );

			foreach ( $indexes as $name => $definition ) {
				if ( ! isset( $description['indexes'][$name] ) ) {
					continue; // ignore so far
					//throw new Ai1ec_Database_Error(
					//	'Unknown index `' . $name .
					//	'` is defined for table `' . $table . '`'
					//);
				}
				if (
					$missed = array_diff_assoc(
						$description['indexes'][$name]['content'],
						$definition['columns']
					)
				) {
					throw new Ai1ec_Database_Error(
						'Index `' . $name .
						'` definition for table `' . $table . '` has invalid ' .
						' fields: ' . implode( ', ', array_keys( $missed ) )
					);
				}
			}

			if (
				$missing = array_diff(
					array_keys( $description['indexes'] ),
					array_keys( $indexes )
				)
			) {
					throw new Ai1ec_Database_Error(
						'In table `' . $table . '` indexes are missing: ' .
						implode( ', ', $missing )
					);
			}

		}
		return true;
	}

	/**
	 * _is_not_empty_line method
	 *
	 * Helper method, to check that any given line is not empty.
	 * Aids array_filter in detecting empty SQL query lines.
	 *
	 * @param string $line Single line of DB query statement
	 *
	 * @return bool True if line is not empty, false otherwise
	 */
	protected function _is_not_empty_line( $line ) {
		$line = trim( $line );
		return ! empty( $line );
	}

}
