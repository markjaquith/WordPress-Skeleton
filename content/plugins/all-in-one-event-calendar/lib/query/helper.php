<?php

/**
 * Query helper.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.query
 */
class Ai1ec_Query_Helper {

	/**
	 * @var WP_Query Instance of WP_Query object
	 */
	protected $_query	= null;

	/**
	 * @var WP_Rewrite Instance of WP_Rewrite object
	 */
	protected $_rewrite = null;

	/**
	 * @var array List of parsed query variables
	 */
	protected $_query_vars = array();

	/**
	 * Initiate object entities
	 *
	 * @param object $query_object	 Instance of query object [optional=WP_Query]
	 * @param object $rewrite_object Instance of query object [optional=WP_Rewrite]
	 *
	 * @return void Constructor does not return
	 */
	public function __construct(
		$query_object   = null,
		$rewrite_object = null
	) {
		if ( null === $query_object ) {
			global $wp_query;
			$query_object = $wp_query;
		}
		$this->_query = $query_object;
		if ( null === $rewrite_object ) {
			global $wp_rewrite;
			$rewrite_object = $wp_rewrite;
		}
		$this->_rewrite = $rewrite_object;
		$this->init_vars();
	}

	/**
	 * Query variable setter/getter
	 *
	 * @param string $name	Name of variable to query
	 * @param mixed	 $value Value to set [optional=null/act as getter]
	 *
	 * @return mixed Variable, null if not present, true in setter mode
	 */
	public function variable( $name, $value = null ) {
		if ( null !== $value ) {
			$this->_query_vars[$name] = $value;
			return true;
		}
		if ( ! isset( $this->_query_vars[$name] ) ) {
			return null;
		}
		return $this->_query_vars[$name];
	}

	/**
	 * Initiate (populate) query variables list. Two different url structures are supported.
	 *
	 *
	 */
	public function init_vars( $query = null ) {
		foreach ( $_REQUEST as $key => $value ) {
			$this->variable( $key, $value );
		}
		if ( null === $query ) {
			$query = $_SERVER['REQUEST_URI'];
		}

		$particles = explode( '/', trim( $query, '/' ) );
		$imported  = 0;
		foreach ( $particles as $element ) {
			if ( $this->_add_serialized_var( $element ) ) {
				++$imported;
			}
		}
		if ( isset( $_REQUEST['ai1ec'] ) ) {
			$particles = explode( '|', trim( $_REQUEST['ai1ec'], '|' ) );
			foreach ( $particles as $element ) {
				if ( $this->_add_serialized_var( $element ) ) {
					++$imported;
				}
			}
		}
		return $imported;
	}

	/**
	 * Check if rewrite module is enabled
	 */
	public function rewrite_enabled() {
		return $this->_rewrite->using_mod_rewrite_permalinks();
	}

	/**
	 * register_rule method
	 *
	 * Register rewrite rule with framework
	 *
	 * @param string $regexp   Expression to register
	 * @param string $landing  URL to be executed on match
	 * @param int    $priority Numeric rule priority - higher means sooner check
	 *
	 * @return string Regexp rule registered with framework
	 */
	public function register_rule( $regexp, $landing, $priority = NULL ) {
		if ( NULL === $priority ) {
			$priority = 1;
		}
		$priority = ( $priority > 0 ) ? 'top' : 'bottom';
		$regexp	  = $this->_inject_route_groups( $regexp );
		$existing = $this->_rewrite->wp_rewrite_rules();
		if ( ! isset( $existing[$regexp] ) ) {
			$this->_rewrite->add_rule(
				$regexp,
				$landing,
				$priority
			);
			$this->_rewrite->flush_rules();
		}
		return $regexp;
	}

	/**
	 * Add serialized (key:value) value to query arguments list
	 */
	protected function _add_serialized_var( $element ) {
		if ( false === strpos( $element, Ai1ec_Uri::DIRECTION_SEPARATOR ) ) {
			return false;
		}
		list( $key, $value ) = explode( Ai1ec_Uri::DIRECTION_SEPARATOR, $element, 2 );
		$this->variable( $key, $value );
		return true;
	}

	/**
	 * Adjust regexp groupping identifiers using WP_Rewrite object
	 */
	protected function _inject_route_groups( $query ) {
		$elements = preg_split(
			'/\$(\d+)/',
			$query,
			null,
			PREG_SPLIT_DELIM_CAPTURE
		);
		$result = '';
		foreach ( $elements as $key => $value ) {
			if ( $key % 2 == 1 ) {
				$value = $this->_rewrite->preg_index($value);
			}
			$result .= $value;
		}
		return $result;
	}

}
