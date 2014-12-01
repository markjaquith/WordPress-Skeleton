<?php

/**
 * Base class for integers-based filters.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Filter
 */
abstract class Ai1ec_Filter_Int implements Ai1ec_Filter_Interface {

	/**
	 * @var Ai1ec_Registry_Object Injected object registry.
	 */
	protected $_registry = null;

	/**
	 * @var array Sanitized input values with only positive integers kept.
	 */
	protected $_values   = array();

	/**
	 * Sanitize input values upon construction.
	 *
	 * @param Ai1ec_Registry_Object $registry      Injected registry.
	 * @param array                 $filter_values Values to sanitize.
	 *
	 * @return void
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		array $filter_values = array()
	) {
		$this->_registry = $registry;
		$this->_values   = array_filter(
			array_map(
				array( $this->_registry->get( 'primitive.int' ), 'positive' ),
				$filter_values
			)
		);
	}

	/**
	 * These simple filters does not require new joins.
	 *
	 * @return string Empty string is returned.
	 */
	public function get_join() {
		return '';
	}

	/**
	 * Get condition part of query for single field.
	 *
	 * @param string $inner_operator Inner logics to use. It is ignored.
	 *
	 * @return string Conditional snippet for query.
	 */
	public function get_where( $inner_operator = null ) {
		if ( empty( $this->_values ) ) {
			return '';
		}
		return $this->get_field() . ' IN ( ' . join( ',', $this->_values ) . ' )';
	}

	/**
	 * Require ancestors to override this to build correct conditional snippet.
	 *
	 * @return string Column alias to use in condition.
	 */
	abstract public function get_field();

}