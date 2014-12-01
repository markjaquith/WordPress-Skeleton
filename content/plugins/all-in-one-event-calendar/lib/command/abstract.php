<?php
/**
 * The abstract command class.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
abstract class Ai1ec_Command {

	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected $_registry;

	/**
	 * @var Ai1ec_Request_Parser
	 */
	protected $_request;

	/**
	 * @var Ai1ec_Http_Response_Render_Strategy
	 */
	protected $_render_strategy;

	/**
	 * Public constructor.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 * @param Ai1ec_Request_Parser $request
	 */
	public function __construct(
			Ai1ec_Registry_Object $registry,
			Ai1ec_Request_Parser $request
	) {
		$this->_registry = $registry;
		$this->_request  = $request;
	}

	/**
	 * Gets parameters from the request object.
	 *
	 * @return array|boolean
	 */
	public function get_parameters() {
		$plugin = $controller = $action = null;
		$plugin     = Ai1ec_Request_Parser::get_param( 'plugin', $plugin );
		$controller = Ai1ec_Request_Parser::get_param( 'controller', $controller );
		$action     = Ai1ec_Request_Parser::get_param( 'action', $action );
		if ( (string)AI1EC_PLUGIN_NAME === (string)$plugin &&
			null !== $controller &&
			null !== $action
		) {
			return array(
				'controller' => $controller,
				'action'     => $action
			);
		}
		return false;

	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function execute() {
		// Set the render strategy
		$this->set_render_strategy( $this->_request );
		// get the data from the concrete implementation
		$data = $this->do_execute();
		// render it.
		$this->_render_strategy->render( $data );
	}

	/**
	 * Defines whether to stop execution of command loop or not.
	 *
	 * @return bool True or false.
	 */
	public function stop_execution() {
		return false;
	}

	/**
	 * The abstract method concrete command must implement.
	 *
	 * Retrieve whats needed and returns it
	 *
	 * @return array
	 */
	abstract public function do_execute();

	/**
	 * Returns whether this is the command to be executed.
	 *
	 * I handle the logi of execution at this levele, which is not usual for
	 * The front controller pattern, because othe extensions need to inject
	 * logic into the resolver ( oAuth or ics export for instance )
	 * and this seems to me to be the most logical way to do this.
	 *
	 * @return boolean
	 */
	abstract public function is_this_to_execute();

	/**
	 * Sets the render strategy.
	 *
	 * @param Ai1ec_Request_Parser $request
	 */
	abstract public function set_render_strategy( Ai1ec_Request_Parser $request );
}