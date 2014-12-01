<?php

/**
 * Filter provider interface.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Filter
 */
interface Ai1ec_Filter_Interface {

	/**
	 * Store user-input locally.
	 *
	 * @param Ai1ec_Registry_Object $registry      Injected registry.
	 * @param array                 $filter_values User provided input.
	 *
	 * @return void
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		array $filter_values = array()
	);

	/**
	 * Return SQL snippet for `FROM` part.
	 *
	 * @return string Valid SQL snippet for `FROM` part.
	 */
	public function get_join();

	/**
	 * Return SQL snippet for `WHERE` part.
	 *
	 * Snippet should not be put in brackets - this will be performed
	 * in upper level.
	 *
	 * @return string Valid SQL snippet.
	 */
	public function get_where();

}