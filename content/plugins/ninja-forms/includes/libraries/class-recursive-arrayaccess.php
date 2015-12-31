<?php
/**
 * Multidimensional ArrayAccess
 *
 * Allows ArrayAccess-like functionality with multidimensional arrays.  Fully supports
 * both sets and unsets.
 *
 * @package WordPress
 * @subpackage Session
 * @since 3.6.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Recursive array class to allow multidimensional array access.
 *
 * @package WordPress
 * @since 3.6.0
 */
class Recursive_ArrayAccess implements ArrayAccess {
	/**
	 * Internal data collection.
	 *
	 * @var array
	 */
	protected $container = array();

	/**
	 * Flag whether or not the internal collection has been changed.
	 *
	 * @var bool
	 */
	protected $dirty = false;

	/**
	 * Default object constructor.
	 *
	 * @param array $data
	 */
	protected function __construct( $data = array() ) {
		foreach ( $data as $key => $value ) {
			$this[ $key ] = $value;
		}
	}

	/**
	 * Allow deep copies of objects
	 */
	public function __clone() {
		foreach ( $this->container as $key => $value ) {
			if ( $value instanceof self ) {
				$this[ $key ] = clone $value;
			}
		}
	}

	/**
	 * Output the data container as a multidimensional array.
	 *
	 * @return array
	 */
	public function toArray() {
		$data = $this->container;
		foreach ( $data as $key => $value ) {
			if ( $value instanceof self ) {
				$data[ $key ] = $value->toArray();
			}
		}
		return $data;
	}

	/**
	* ArrayAccess Implementation
	**/

	/**
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset An offset to check for.
	 *
	 * @return boolean true on success or false on failure.
	 */
	public function offsetExists( $offset ) {
		return isset( $this->container[ $offset ]) ;
	}

	/**
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet( $offset ) {
		return isset( $this->container[ $offset ] ) ? $this->container[ $offset ] : null;
	}

	/**
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value  The value to set.
	 *
	 * @return void
	 */
	public function offsetSet( $offset, $data ) {
		if ( is_array( $data ) ) {
			$data = new self( $data );
		}
		if ( $offset === null ) { // don't forget this!
			$this->container[] = $data;
		} else {
			$this->container[ $offset ] = $data;
		}

		$this->dirty = true;
	}

	/**
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset The offset to unset.
	 *
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		unset( $this->container[ $offset ] );
	}
}
