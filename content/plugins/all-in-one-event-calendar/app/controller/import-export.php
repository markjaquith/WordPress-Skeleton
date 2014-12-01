<?php

/**
 * The controller which handles import/export.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Controller
 */
class Ai1ec_Import_Export_Controller {

	/**
	 * @var array The registered engines.
	 */
	protected $_engines = array();


	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected $_registry;

	/**
	 * @var array Import / export params.
	 */
	protected $_params;

	/**
	 * This controller is instanciated only if we need to import/export something.
	 *
	 * When it is instanciated it allows other engines to be injected through a
	 * filter. If we do not plan to ship core engines, let's skip the
	 * $core_engines param.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 * @param array $core_engines
	 * @param array $params
	 */
	public function __construct(
			Ai1ec_Registry_Object $registry,
			array $core_engines = array( 'ics' ),
			array $params = array()
	) {
		$this->_registry = $registry;
		$known_engines   = apply_filters(
			'ai1ec_register_import_export_engines',
			$core_engines
		);
		$this->_params   = $params;
		foreach ( $known_engines as $engine ) {
			$this->register( $engine );
		}
	}

	/**
	 * Register an import-export engine.
	 *
	 * @param string $engine
	 */
	public function register( $engine ) {
		$this->_engines[$engine] = true;
	}

	/**
	 * Import events into the calendar.
	 *
	 * @param string $engine
	 * @param array $args
	 *
	 * @throws Ai1ec_Engine_Not_Set_Exception If the engine is not set.
	 * @throws Ai1ec_Parse_Exception          If an error happens during parse.
	 *
	 * @return int The number of imported events
	 */
	public function import_events( $engine, array $args ) {
		if ( ! isset( $this->_engines[$engine] ) ) {
			throw new Ai1ec_Engine_Not_Set_Exception(
				'The engine ' . $engine . 'is not registered.'
			);
		}
		// external engines must register themselves into the registry.
		$engine    = $this->_registry->get( 'import-export.' . $engine );
		$exception = null;
		try {
			return $engine->import( $args );
		} catch ( Ai1ec_Parse_Exception $parse_exception ) {
			$exception = $parse_exception;
		}
		throw $exception;
	}

	/**
	 * Export the events using the specified engine.
	 *
	 * @param string $engine
	 * @param array $args
	 *
	 * @throws Ai1ec_Engine_Not_Set_Exception
	 */
	public function export_events( $engine, array $args ) {
		if ( ! isset( $this->_engines[$engine] ) ) {
			throw new Ai1ec_Engine_Not_Set_Exception(
				'The engine ' . $engine . 'is not registered.'
			);
		}
		// external engines must register themselves into the registry.
		$engine = $this->_registry->get( 'import-export.' . $engine );
		return $engine->export( $args, $this->_params );
	}
}