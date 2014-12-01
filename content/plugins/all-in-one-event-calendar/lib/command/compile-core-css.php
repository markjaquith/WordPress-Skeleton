<?php
/**
 * The concrete command that compiles CSS.
*
* @author     Time.ly Network Inc.
* @since      2.1
*
* @package    AI1EC
* @subpackage AI1EC.Command
*/
class Ai1ec_Command_Compile_Core_Css extends Ai1ec_Command {
	
	/*
	 * (non-PHPdoc) @see Ai1ec_Command::is_this_to_execute()
	 */
	public function is_this_to_execute() {
		if ( isset( $_GET['ai1ec_compile_css'] ) &&
			$_SERVER['SERVER_ADDR'] === $_SERVER['REMOTE_ADDR'] &&
			AI1EC_DEBUG
		) {
			return true;
		}
		return false;
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
		$less = $frontend = $this->_registry->get( 'less.lessphp' );
		$css = $less->parse_less_files( null, true );

		$filename = AI1EC_DEFAULT_THEME_PATH . DIRECTORY_SEPARATOR .
			'css' . DIRECTORY_SEPARATOR . 'ai1ec_parsed_css.css';
		if ( false === @file_put_contents( $filename, $css ) ) {
			echo 'There has been an error writing core CSS';
		} else {
			echo 'Core CSS compiled succesfully and written in ' . $filename;
		}
		return array(
		);
	}
}