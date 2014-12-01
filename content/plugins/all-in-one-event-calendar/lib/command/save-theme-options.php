<?php

/**
 * The concrete command that save theme options.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
class Ai1ec_Command_Save_Theme_Options extends Ai1ec_Command_Save_Abstract {

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::is_this_to_execute()
	*/
	public function do_execute() {
		$variables = array();

		// Handle updating of theme options.
		if ( isset( $_POST[Ai1ec_View_Theme_Options::SUBMIT_ID] ) ) {
			$_POST     = stripslashes_deep( $_POST );
			$lessphp   = $this->_registry->get( 'less.lessphp' );
			$variables = $lessphp->get_saved_variables();
			foreach ( $variables as $variable_name => $variable_params ) {
				if ( isset( $_POST[$variable_name] ) ) {
					// Avoid problems for those who are foolish enough to leave php.ini
					// settings at their defaults, which has magic quotes enabled.
					if ( get_magic_quotes_gpc() ) {
						$_POST[$variable_name] = stripslashes( $_POST[$variable_name] );
					}
					if (
						Ai1ec_Less_Variable_Font::CUSTOM_FONT === $_POST[$variable_name]
					) {
						$_POST[$variable_name] = $_POST[$variable_name .
							Ai1ec_Less_Variable_Font::CUSTOM_FONT_ID_SUFFIX];
					}
					// update the original array
					$variables[$variable_name]['value'] = $_POST[$variable_name];
				}
			}
			$_POST = add_magic_quotes( $_POST );
		}
		// Handle reset of theme options.
		elseif ( isset( $_POST[Ai1ec_View_Theme_Options::RESET_ID] ) ) {
			$lessphp = $this->_registry->get( 'less.lessphp' );
			$variables = $lessphp->get_less_variable_data_from_config_file();
			do_action( 'ai1ec_reset_less_variables' );
		}

		$css = $this->_registry->get( 'css.frontend' );

		$css->update_variables_and_compile_css(
			$variables,
			isset(
				$_POST[Ai1ec_View_Theme_Options::RESET_ID]
			)
		);

		return array(
			'url' => admin_url(
				'edit.php?post_type=ai1ec_event&page=all-in-one-event-calendar-edit-css'
			),
			'query_args' => array(),
		);
	}

}
