<?php

/**
 * Abstract class for less variables.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Less.Variable
 */
abstract class Ai1ec_Less_Variable extends Ai1ec_Html_Element {

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * it takes an array of parameters and a renderable.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 * @param array $params
	 * @internal param \Ai1ec_Renderable $renderable
	 */
	public function __construct( Ai1ec_Registry_Object $registry, array $params ) {
		parent::__construct( $registry );
		$this->id          = $params['id'];
		$this->description = $params['description'];
		$this->value       = $params['value'];
	}

}
