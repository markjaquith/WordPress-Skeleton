<?php
/**
 * The command resolver class that handles command.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
class Ai1ec_Command_Resolver {

	/**
	 * @var array The available commands.
	 */
	private $_commands = array();

	/**
	 * @var Ai1ec_Registry_Object The Object registry.
	 */
	private $_registry;

	/**
	 * @var Ai1ec_Request_Parser The Request parser.
	 */
	private $_request;

	/**
	 * Public constructor
	 *
	 * @param Ai1ec_Registry_Object $registry
	 * @param Ai1ec_Request_Parser $request
	 *
	 * @return void
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		Ai1ec_Request_Parser $request
	) {
		$this->add_command(
			$registry->get(
				'command.compile-themes', $request
			)
		);
		$this->add_command(
			$registry->get(
				'command.disable-gzip', $request
			)
		);
		$this->add_command(
			$registry->get(
				'command.export-events', $request
			)
		);
		$this->add_command(
			$registry->get(
				'command.render-event', $request
			)
		);
		$this->add_command(
			$registry->get(
				'command.render-calendar', $request
			)
		);
		$this->add_command(
			$registry->get(
				'command.change-theme', $request
			)
		);
		$this->add_command(
			$registry->get(
				'command.save-settings',
				$request,
				array(
					'action' => 'ai1ec_save_settings',
					'nonce_action' => Ai1ec_View_Admin_Settings::NONCE_ACTION,
					'nonce_name' => Ai1ec_View_Admin_Settings::NONCE_NAME,
				)
			)
		);
		$this->add_command(
			$registry->get(
				'command.save-theme-options',
				$request,
				array(
					'action' => 'ai1ec_save_theme_options',
					'nonce_action' => Ai1ec_View_Theme_Options::NONCE_ACTION,
					'nonce_name' => Ai1ec_View_Theme_Options::NONCE_NAME,
				)
			)
		);
		$this->add_command(
			$registry->get(
				'command.clone', $request
			)
		);
		$this->add_command(
			$registry->get(
				'command.compile-core-css', $request
			)
		);
		$request->parse();
		$this->_registry = $registry;
		$this->_request  = $request;
	}

	/**
	 * Add a command.
	 *
	 * @param Ai1ec_Command $command
	 *
	 * @return Ai1ec_Comment_Resolver Self for calls chaining
	 */
	public function add_command( Ai1ec_Command $command ) {
		$this->_commands[] = $command;
		return $this;
	}

	/**
	 * Return the command to execute or false.
	 *
	 * @return Ai1ec_Command|null
	 */
	public function get_commands() {
		$commands = array();
		foreach ( $this->_commands as $command ) {
			if ( $command->is_this_to_execute() ) {
				$commands[] = $command;
			}
		}
		return $commands;
	}
}