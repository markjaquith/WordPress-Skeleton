<?php

/**
 * The concrete command that change active theme.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
class Ai1ec_Command_Change_Theme extends Ai1ec_Command {

	/**
	 * Executes the command to change the active theme.
	 *
	 * NOTE: {@see self::is_this_to_execute} must return true for this command
	 * to execute; we can trust that input has been checked for injections.
	 */
	public function do_execute() {
		// Update the active theme in the options table.
		$stylesheet = preg_replace(
			'|[^a-z_\-]+|i',
			'',
			$_GET['ai1ec_stylesheet']
		);
		$this->_registry->get( 'theme.loader' )->switch_theme( array(
			'theme_root' => realpath( $_GET['ai1ec_theme_root'] ),
			'theme_dir'  => realpath( $_GET['ai1ec_theme_dir'] ),
			'theme_url'  => $_GET['ai1ec_theme_url'],
			'stylesheet' => $stylesheet,
			'legacy'     => (bool)intval( $_GET['ai1ec_legacy'] )
		) );

		// Return user to themes list page with success message.
		return array(
			'url' => admin_url(
				'edit.php?post_type=ai1ec_event&page=all-in-one-event-calendar-themes'
			),
			'query_args' => array(
				'activated' => 1
			)
		);
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Command_Save_Abstract::set_render_strategy()
	 */
	public function set_render_strategy( Ai1ec_Request_Parser $request ) {
		$this->_render_strategy = $this->_registry->get(
			'http.response.render.strategy.redirect'
		);
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::is_this_to_execute()
	*/
	public function is_this_to_execute() {
		if (
			isset( $_GET['ai1ec_action'] ) &&
			$_GET['ai1ec_action'] === 'activate_theme' &&
			current_user_can( 'switch_ai1ec_themes' ) &&
			is_dir( $_GET['ai1ec_theme_dir'] ) &&
			is_dir( $_GET['ai1ec_theme_root'] )
		) {
			check_admin_referer(
				'switch-ai1ec_theme_' . $_GET['ai1ec_stylesheet']
			);
			return true;
		}
		return false;
	}
}
