<?php

/**
 * The concrete command that renders the calendar.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
class Ai1ec_Command_Render_Calendar extends Ai1ec_Command {

	/**
	 * @var string
	 */
	protected $_request_type;

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::is_this_to_execute()
	 */
	public function is_this_to_execute() {
		$settings          = $this->_registry->get( 'model.settings' );
		$calendar_page_id  = $settings->get( 'calendar_page_id' );
		if ( empty( $calendar_page_id ) ) {
			return false;
		}
		$localization      = $this->_registry->get( 'p28n.wpml' );
		$aco               = $this->_registry->get( 'acl.aco' );
		$page_ids_to_match = array( $calendar_page_id ) +
		$localization->get_translations_of_page(
				$calendar_page_id
		);
		foreach ( $page_ids_to_match as $page_id ) {

			if ( is_page( $page_id ) ) {
				$this->_request->set_current_page( $page_id );
				if ( ! post_password_required( $page_id ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::set_render_strategy()
	 */
	public function set_render_strategy( Ai1ec_Request_Parser $request ) {
		try {
			$this->_request_type    = $request->get( 'request_type' );
			$this->_render_strategy = $this->_registry->get(
				'http.response.render.strategy.' . $this->_request_type
			);
		} catch ( Ai1ec_Bootstrap_Exception $e ) {
			$this->_request_type    = 'html';
			$this->_render_strategy = $this->_registry->get(
				'http.response.render.strategy.' . $this->_request_type
			);
		}
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Command::do_execute()
	 */
	public function do_execute() {
		// get the calendar html
		$calendar = $this->_registry->get( 'view.calendar.page' );
		$css      = $this->_registry->get( 'css.frontend' )
			->add_link_to_html_for_frontend();
		$js       = $this->_registry->get( 'controller.javascript' )
			->load_frontend_js( true );
		return array(
			'data'     => $calendar->get_content( $this->_request ),
			'callback' => Ai1ec_Request_Parser::get_param(
				'callback',
				null
			),
		);
	}

}