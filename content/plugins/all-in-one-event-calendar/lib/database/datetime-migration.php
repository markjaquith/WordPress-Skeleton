<?php

/**
 * The date-time migration utility layer.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Database
 */
class Ai1ecdm_Datetime_Migration {

	/**
	 * @var wpdb Instance of wpdb or it's extension.
	 */
	protected $_dbi           = null;

	/**
	 * @var array List of tables to be processed.
	 */
	protected $_tables        = array();

	/**
	 * @var array Map of indices on selected tables.
	 */
	protected $_indices       = array();

	/**
	 * @var string Table suffix used in migration process.
	 */
	protected $_table_suffix  = '_dt_ui_mig';

	/**
	 * @var string Column suffix used in data transformation.
	 */
	protected $_column_suffix = '_transformation';

	/**
	 * Output debug statements.
	 *
	 * @var mixed $arg1 Number of arguments to output.
	 *
	 * @return bool True when debug is in action.
	 */
	static public function debug( /** polymorphic arg list **/ ) {
		if ( ! defined( 'AI1EC_DEBUG' ) || ! AI1EC_DEBUG ) {
			return false;
		}
		$argv = func_get_args();
		foreach ( $argv as $value ) {
			echo '<pre class="timely-debug">',
				'<small>', microtime( true ), '</small>', "\n";
			var_export( $value );
			echo '</pre>';
		}
		return true;
	}

	/**
	 * Acquire references of global variables and define non-scalar values.
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		$this->_dbi = $registry->get( 'dbi.dbi' );
		$this->_tables = array(
			$this->_dbi->get_table_name( 'ai1ec_events' )                => array(
				'start',
				'end',
			),
			$this->_dbi->get_table_name( 'ai1ec_event_instances' )       => array(
				'start',
				'end',
			),
			$this->_dbi->get_table_name( 'ai1ec_facebook_users_events' ) => array(
				'start',
			),
		);
		$this->_indices = array(
			$this->_dbi->get_table_name( 'ai1ec_event_instances' ) => array(
				'evt_instance' => array(
					'unique'  => true,
					'columns' => array( 'post_id', 'start' ),
					'name'	  => 'evt_instance',
				),
			),
		);
	}

	/**
	 * Interface to underlying methods to use as a filter callback.
	 *
	 * @wp_hook ai1ec_perform_scheme_update
	 *
	 * @return bool True when database is up to date.
	 */
	public function filter_scheme_update() {
		return ( ! $this->is_change_required() || $this->execute() );
	}

	/**
	 * Retrieve columns for a given table.
	 *
	 * Checks if table exists before attempting to retrieve it.
	 *
	 * @param string $table Name of table to retrieve columns for.
	 *
	 * @return array Map of column names and their types.
	 */
	public function get_columns( $table ) {
		if ( ! $this->_is_table( $table ) ) {
			return array();
		}
		$list = $this->_dbi->get_results(
			'SHOW COLUMNS FROM `' . $table . '`'
		);
		$columns = array();
		foreach ( $list as $column ) {
			$columns[$column->Field] = strtolower( $column->Type );
		}
		return $columns;
	}

	/**
	 * Retrieve list of indices for a given table.
	 *
	 * Checks if table exists before attempting to retrieve it.
	 *
	 * @param string $table Name of table to retrieve indices for.
	 *
	 * @return array Map of index names.
	 */
	public function get_indices( $table ) {
		if ( ! $this->_is_table( $table ) ) {
			return array();
		}
		$list = $this->_dbi->get_results(
			'SHOW INDEX FROM `' . $table . '`'
		);
		$columns = array();
		foreach ( $list as $column ) {
			$columns[ strtolower( $column->Key_name ) ] = $column->Key_name;
		}
		return $columns;
	}

	/**
	 * Check if database change is required.
	 *
	 * @return bool True if any changes are required.
	 */
	public function is_change_required() {
		foreach ( $this->_tables as $table => $columns ) {
			$existing = $this->get_columns( $table );
			foreach ( $existing as $column => $type ) {
				if (
					false === array_search( $column, $columns ) ||
					0 !== stripos( $type, 'datetime' )
				) {
					unset( $existing[$column] );
				}
			}
			if ( empty( $existing ) ) {
				unset( $this->_tables[$table] );
			}
		}
		if ( ! empty( $this->_tables ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Single stop for executing database changes.
	 *
	 * @return bool Success.
	 */
	public function execute() {
		return $this->create_copies()
			&& $this->apply_changes_to_copies()
			&& $this->swap_tables();
	}

	/**
	 * Create copies of tables to be transformed.
	 *
	 * @return bool Success.
	 */
	public function create_copies() {
		$tables = array_keys( $this->_tables );
		foreach ( $tables as $table ) {
			$suffixed = $table . $this->_table_suffix;
			if (
				! $this->drop( $suffixed ) ||
				! $this->copy( $table, $suffixed )
			) {
				return false;
			}
		}
		self::debug(
			'Copies of following tables created successfully:',
			$tables
		);
		return true;
	}

	/**
	 * Transform columns on copied tables.
	 *
	 * @return bool Success.
	 */
	public function apply_changes_to_copies() {
		foreach ( $this->_tables as $table => $columns ) {
			$name = $table . $this->_table_suffix;
			if (
				! (
					$this->drop_indices( $table, $name )
					&& $this->out_of_bounds_fix( $table, $name )
					&& $this->add_columns( $name, $columns )
					&& $this->transform_dates( $name, $columns )
					&& $this->replace_columns( $name, $columns )
					&& $this->restore_indices( $table, $name )
				)
			) {
				return false;
			}
		}
		self::debug(
			'Table copies successfully modified:',
			$this->_tables
		);
		return true;
	}

	/**
	 * Keep old table under unique name and move modified into it's place.
	 *
	 * @return bool Success.
	 */
	public function swap_tables() {
		$tables  = array_keys( $this->_tables );
		$renames = array();
		foreach ( $tables as $table ) {
			$modified  = $table . $this->_table_suffix;
			$backup    = $table . '_' . date( 'Y_m_d' ) . '_' . getmypid();
			$renames[] = '`' . $table    . '` TO `' . $backup . '`';
			$renames[] = '`' . $modified . '` TO `' . $table  . '`';
		}
		$sql_query = 'RENAME TABLE ' . implode( ', ', $renames );
		if ( false === $this->_dbi->query( $sql_query ) ) {
			return false;
		}
		self::debug(
			'Tables successfully swaped:',
			$this->_tables
		);
		return true;
	}

	/**
	 * Drop given table indices.
	 *
	 * @param string $name  Original table name.
	 * @param string $table Table to actually perform changes upon.
	 *
	 * @return bool Success.
	 */
	public function drop_indices( $name, $table ) {
		self::debug( __METHOD__ );
		if ( ! isset( $this->_indices[$name] ) ) {
			return true;
		}
		$existing = $this->get_indices( $table );
		foreach ( $this->_indices[$name] as $index => $options ) {
			if ( isset( $existing[$index] ) ) {
				$sql_query = 'ALTER TABLE `' . $table . '` DROP INDEX `' .
					$index . '`';
				if ( false === $this->_dbi->query( $sql_query ) ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Add intermediate columns to a table.
	 *
	 * @param string $table   Name of table to modify.
	 * @param array  $columns List of column names to be added.
	 *
	 * @return bool Success.
	 */
	public function add_columns( $table, $columns ) {
		self::debug( __METHOD__ );
		$column_particles = array();
		foreach ( $columns as $column ) {
			$name = $column . $this->_column_suffix;
			$column_particles[] = 'ADD COLUMN ' . $name .
				' INT(10) UNSIGNED NOT NULL';
		}
		$sql_query = 'ALTER TABLE `' . $table . '` ' .
			implode( ', ', $column_particles );
		return ( false !== $this->_dbi->query( $sql_query ) );
	}

	/**
	 * Copy date values from `DATETIME` to `INT(10)` columns.
	 *
	 * @param string $table   Name of table to modify.
	 * @param array  $columns List of column names to be copied.
	 *
	 * @return bool Success.
	 */
	public function transform_dates( $table, $columns ) {
		self::debug( __METHOD__ );
		$update_particles = array();
		foreach ( $columns as $column ) {
			$name      = $column . $this->_column_suffix;
			$new_value = '\'1970-01-01 00:00:00\'';
			if ( 'end' === $column && in_array( 'start', $columns ) ) {
				$new_value = 'IFNULL(`start`, ' . $new_value . ')';
			}
			$update_particles[] = '`' . $name .
				'` = UNIX_TIMESTAMP( IFNULL(`' . $column . '`, ' . $new_value . ' ))';
		}
		$sql_query = 'UPDATE `' . $table . '` SET ' .
			implode( ', ', $update_particles );
		return ( false !== $this->_dbi->query( $sql_query ) );
	}

	/**
	 * Drop old columns and move intermediate columns into their place.
	 *
	 * @param string $table   Name of table to modify.
	 * @param array  $columns List of column names to be replaced.
	 *
	 * @return bool Success.
	 */
	public function replace_columns( $table, $columns ) {
		self::debug( __METHOD__ );
		$snippets = array();
		foreach ( $columns as $column ) {
			$snippets[] = 'DROP COLUMN `' . $column . '`';
			$snippets[] = 'CHANGE COLUMN `' . $column . $this->_column_suffix .
				'` `' . $column . '` INT(10) UNSIGNED NOT NULL';
		}
		$sql_query = 'ALTER TABLE `' . $table . '` ' .
			implode( ', ', $snippets );
		return ( false !== $this->_dbi->query( $sql_query ) );
	}

	/**
	 * Restore indices for table processed.
	 *
	 * @param string $name  Original table name.
	 * @param string $table Table to actually perform changes upon.
	 *
	 * @return bool Success.
	 */
	public function restore_indices( $name, $table ) {
		self::debug( __METHOD__ );
		if ( ! isset( $this->_indices[$name] ) ) {
			return true;
		}
		foreach ( $this->_indices[$name] as $index => $options ) {
			$sql_query = 'ALTER TABLE `' . $table . '` ADD';
			if ( $options['unique'] ) {
				$sql_query .= ' UNIQUE';
			}
			$sql_query .= ' INDEX `' .
				$index . '` (`' .
				implode( '`, `', $options['columns'] ) .
				'`)';
			if ( false === $this->_dbi->query( $sql_query ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Drop table.
	 *
	 * @param string $table Name of table to drop.
	 *
	 * @return bool Success.
	 */
	public function drop( $table ) {
		$sql_query = 'DROP TABLE IF EXISTS ' . $table;
		return ( false !== $this->_dbi->query( $sql_query ) );
	}

	/**
	 * Create table copy with full data set.
	 *
	 * @param string $existing  Name of table to copy.
	 * @param string $new_table Name of table to create.
	 *
	 * @return bool Success.
	 */
	public function copy( $existing, $new_table ) {
		$queries = array(
			'CREATE TABLE ' . $new_table . ' LIKE '          . $existing,
			'INSERT INTO '  . $new_table . ' SELECT * FROM ' . $existing,
		);
		foreach ( $queries as $query ) {
			self::debug( $query );
			if ( false === $this->_dbi->query( $query ) ) {
				return false;
			}
		}
		$count_new = $this->_dbi->get_var(
			'SELECT COUNT(*) FROM ' . $new_table
		);
		$count_old = $this->_dbi->get_var(
			'SELECT COUNT(*) FROM ' . $existing
		);
		// check if difference between tables records doesn't exceed
		// several least significant bits of old table entries count
		if ( absint( $count_new - $count_old ) > ( $count_old >> 4 ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Return list of tables to be processed
	 *
	 * @return array List of tables to be processed
	 */
	public function get_tables() {
		return $this->_tables;
	}

	/**
	 * Delete events dated before or at `1970-01-01 00:00:00`.
	 *
	 * @param string $table Original table.
	 * @param string $name  Temporary table to replay changes onto.
	 *
	 * @return bool Success.
	 */
	public function out_of_bounds_fix( $table, $name ) {
		static $instances = null;
		if ( null === $instances ) {
			$instances = $this->_dbi->get_table_name( 'ai1ec_event_instances' );
		}
		if ( $instances !== $table ) {
			return true;
		}
		$query = 'DELETE FROM `' .
			$this->_dbi->get_table_name( $name ) .
			'` WHERE `start` <= \'1970-01-01 00:00:00\'';
		return ( false !== $this->_dbi->query( $query ) );
	}

	/**
	 * Check if given table exists.
	 *
	 * @param string $table Name of table to check.
	 *
	 * @return bool Existence.
	 */
	protected function _is_table( $table ) {
		$name = $this->_dbi->get_var(
			$this->_dbi->prepare( 'SHOW TABLES LIKE %s', $table )
		);
		return ( (string)$table === (string)$name );
	}

}