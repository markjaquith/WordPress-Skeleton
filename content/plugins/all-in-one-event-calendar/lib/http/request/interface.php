<?php

/**
 * Query adapter interface
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http.Request
 */
interface Ai1ec_Adapter_Query_Interface {

	/**
	 * Check if rewrite module is enabled
	 */
	public function rewrite_enabled();

	/**
	 * Register rewrite rule
	 *
	 * @param string $regexp   Matching expression
	 * @param string $landing  Landing point for queries matching regexp
	 * @param int    $priority Rule priority (match list) [optional=NULL]
	 *
	 * @return bool
	 */
	public function register_rule( $regexp, $landing, $priority = NULL );

}
