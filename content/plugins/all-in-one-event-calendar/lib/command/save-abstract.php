<?php

/**
 * The abstract command that save something in the admin.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
abstract class Ai1ec_Command_Save_Abstract extends Ai1ec_Command {
	
	protected $_controller = 'front';
	
	protected $_action;
	
	protected $_nonce_name;
	
	protected $_nonce_action;
	
	/**
	 * Public constructor, set the strategy according to the type.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 * @param Ai1ec_Request_Parser $request
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		Ai1ec_Request_Parser $request,
		array $args
	) {
		parent::__construct( $registry, $request );
		$this->_action = $args['action'];
		$this->_nonce_action = $args['nonce_action'];
		$this->_nonce_name = $args['nonce_name'];
	}
	
	/* (non-PHPdoc)
	 * @see Ai1ec_Command::is_this_to_execute()
	*/
	public function is_this_to_execute() {
		$params = $this->get_parameters();
		if ( false === $params ) {
			return false;
		}
		if ( $params['controller'] === $this->_controller &&
			$params['action'] === $this->_action ) {
			$pass = wp_verify_nonce(
				$_POST[$this->_nonce_name],
				$this->_nonce_action
			);
			if ( ! $pass ) {
				wp_die( "Failed security check" );
			}
			return true;
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see Ai1ec_Command::set_render_strategy()
	*/
	public function set_render_strategy( Ai1ec_Request_Parser $request ) {
		$this->_render_strategy = $this->_registry->get(
			'http.response.render.strategy.redirect'
		);
	}
}