<?php
/**
 * The concrete command that disabel gzip.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
class Ai1ec_Command_Disable_Gzip extends Ai1ec_Command {

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::is_this_to_execute()
	 */
	public function is_this_to_execute() {
		if ( isset( $_GET['ai1ec_disable_gzip_compression'] ) ) {
			check_admin_referer( 'ai1ec_disable_gzip_compression' );
			return true;
		}
		return false;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::do_execute()
	 */
	public function do_execute() {
		$this->_registry->get( 'model.settings' )
			->set( 'disable_gzip_compression', true );
		return array(
			'url'        => admin_url( 'edit.php' ),
			'query_args' => array(
				'post_type' => 'ai1ec_event',
				'page'      => 'all-in-one-event-calendar-settings',
			),
		);
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