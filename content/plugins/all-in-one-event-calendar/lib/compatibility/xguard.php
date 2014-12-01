<?php

/**
 * Execution guard.
 *
 * Guards process execution for multiple runs at the same moment of time.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Compatibility
 */
class Ai1ec_Compatibility_Xguard extends Ai1ec_Base {

	/**
	 * Return time of last acquisition.
	 *
	 * If execution guard with that name was never acquired it returns 0 (zero).
	 * If acquisition fails it returns false.
	 *
	 * @param string $name    Name of guard to be acquired.
	 * @param int    $timeout Timeout, how long lock is held after acquisition.
	 *
	 * @return bool Success to acquire lock for given period.
	 */
	public function acquire( $name, $timeout = 86400 ) {
		$name  = $this->safe_name( $name );
		$dbi   = $this->_registry->get( 'dbi.dbi' );
		$entry = array(
			'time' => time(),
			'pid'  => getmypid(),
		);
		$table = $dbi->get_table_name( 'options' );
		$dbi->query( 'START TRANSACTION' );
		$query = $dbi->prepare(
			'SELECT option_value FROM ' . $table .
			' WHERE option_name = %s',
			$name
		);
		$prev = $dbi->get_var( $query );
		if ( ! empty( $prev ) ) {
			$prev = json_decode( $prev, true );
		}
		if (
			! empty( $prev ) &&
			( (int)$prev['time'] + (int)$timeout ) >= $entry['time']
		) {
			$dbi->query( 'ROLLBACK' );
			return false;
		}
		$query = '';
		if ( empty( $prev ) ) {
			$query = 'INSERT INTO';
		} else {
			$query = 'UPDATE';
		}
		$query .= ' `' . $table . '` SET `option_name` = %s, `option_value` = %s, `autoload` = 0';
		if ( ! empty( $prev ) ) {
			$query .= ' WHERE `option_name` = %s';
		}
		$query   = $dbi->prepare( $query, $name, json_encode( $entry ), $name );
		$success = $dbi->query( $query );
		if ( ! $success ) {
			$dbi->query( 'ROLLBACK' );
			return false;
		}
		$dbi->query( 'COMMIT' );
		return true;
	}

	/**
	 * Method release logs execution guard release phase.
	 *
	 * @param string $name Name of acquisition.
	 *
	 * @return bool Not expected to fail.
	 */
	public function release( $name ) {
		return false !== $this->_registry->get( 'dbi.dbi' )->delete(
			'options',
			array( 'option_name' => $this->safe_name( $name ) ),
			array( '%s' )
		);
	}

	/**
	 * Prepare safe file names.
	 *
	 * @param string $name Name of acquisition
	 *
	 * @return string Actual safeguard name to use.
	 */
	protected function safe_name( $name ) {
		$name = preg_replace( '/[^A-Za-z_0-9\-]/', '_', $name );
		$name = trim( preg_replace( '/_+/', '_', $name ), '_' );
		$name = 'ai1ec_xlock_' . $name;
		return substr( $name, 0, 50 );
	}

}