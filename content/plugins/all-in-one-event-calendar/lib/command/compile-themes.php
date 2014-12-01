<?php

/**
 * (Re)compile themes for shipping.
 *
 * @author     Time.ly Network Inc.
 * @since      2.1
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
class Ai1ec_Command_Compile_Themes extends Ai1ec_Command {
	
	/*
	 * (non-PHPdoc) @see Ai1ec_Command::is_this_to_execute()
	 */
	public function is_this_to_execute() {
		return (
			AI1EC_DEBUG &&
			isset( $_GET['ai1ec_recompile_templates'] ) &&
			$_SERVER['REMOTE_ADDR'] === $_SERVER['SERVER_ADDR']
		);
	}
	
	/* (non-PHPdoc)
	 * @see Ai1ec_Command::set_render_strategy()
	*/
	public function set_render_strategy( Ai1ec_Request_Parser $request ) {
		$this->_render_strategy = $this->_registry->get(
			'http.response.render.strategy.void'
		);
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::do_execute()
	*/
	public function do_execute() {
		$this->_registry->get( 'theme.compiler' )->generate();
        return Ai1ec_Http_Response_Helper::stop( 0 );
	}

}