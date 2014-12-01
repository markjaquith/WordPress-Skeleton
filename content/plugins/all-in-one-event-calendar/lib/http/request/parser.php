<?php

/**
 * Concrete request parsing class.
 *
 * @author       Time.ly Network Inc.
 * @since        2.0
 * @instantiator new
 * @package      AI1EC
 * @subpackage   AI1EC.Http.Request
 */
class Ai1ec_Request_Parser extends Ai1ec_Abstract_Query {

	/**
	 * @var int ID of page currently open
	 */
	static $current_page = NULL;

	/**
	 * set_current_page method
	 *
	 * Set ID of currently open page
	 *
	 * @param int $page_id ID of page currently open
	 *
	 * @return void Method does not return
	 */
	static public function set_current_page( $page_id ) {
		self::$current_page = $page_id;
	}

	/**
	 * get_param function
	 *
	 * Tries to return the parameter from POST and GET
	 * incase it is missing, default value is returned
	 *
	 * @param string $param Parameter to return
	 * @param mixed $default Default value
	 *
	 * @return mixed
	 **/
	static public function get_param( $param, $default='' ) {
		if ( isset( $_POST[$param] ) ) {
			return $_POST[$param];
		}
		if ( isset( $_GET[$param] ) ) {
			return $_GET[$param];
		}
		return $default;
	}

	/**
	 * get_current_page method
	 *
	 * Get ID of currently open page
	 *
	 * @return int|NULL ID of currently open page, or NULL if none set
	 */
	static public function get_current_page() {
		return self::$current_page;
	}

	/**
	 * Initiate default filters for arguments parser
	 */
	public function __construct(
		Ai1ec_Registry_Object $registry,
		array $argv     = null,
		$default_action = null
	) {
		parent::__construct( $registry, $argv );
		$settings_view = $this->_registry->get( 'model.settings-view' );
		$action_list   = array_keys( $settings_view->get_all() );
		foreach ( $action_list as $action ) {
			$action_list[] = 'ai1ec_' . $action;
		}

		if ( null === $default_action ) {
			$default_action = $settings_view->get_default();
		}

		$this->add_rule(
			'action',
			false,
			'string',
			$default_action,
			$action_list
		);
		$this->add_rule( 'page_offset',   false, 'int', 0,    false );
		$this->add_rule( 'month_offset',  false, 'int', 0,    false );
		$this->add_rule( 'oneday_offset', false, 'int', 0,    false );
		$this->add_rule( 'week_offset',   false, 'int', 0,    false );
		$this->add_rule( 'time_limit',    false, 'int', 0,    false );
		$this->add_rule( 'cat_ids',       false, 'int', null, ',' );
		$this->add_rule( 'tag_ids',       false, 'int', null, ',' );
		$this->add_rule( 'post_ids',      false, 'int', null, ',' );
		$this->add_rule( 'auth_ids',      false, 'int', null, ',' );
		$this->add_rule( 'term_ids',      false, 'int', null, ',' );
		$this->add_rule( 'exact_date',    false, 'string', null, false );
		// This is the type of the request: Standard, json or jsonp
		$this->add_rule( 'request_type',  false, 'string', 'html', false );
		// This is the format of the request.
		$this->add_rule( 'request_format',false, 'string', 'html', false );
		// The callback function for jsonp calls
		$this->add_rule( 'callback',      false, 'string', null, false );
		// Whether to include navigation controls
		$this->add_rule( 'no_navigation' ,false, 'string', false, false );
		// whether to display the filter bar in the super widget
		$this->add_rule( 'display_filters' ,false, 'string', false, false );
		$this->add_rule( 'applying_filters' ,false, 'string', false, false );
		$this->add_rule( 'shortcode' ,false, 'string', false, false );
		$this->add_rule( 'events_limit', false, 'int', null, false );
		do_action( 'ai1ec_request_parser_rules_added', $this );
	}

	/**
	 * Get query argument name prefix.
	 *
	 * Inherited from parent class. Method is used to detect query name
	 * prefix, that is used to "namespace" own (private) query variables.
	 *
	 * @return string Query prefix 'ai1ec_'
	 */
	protected function _get_prefix() {
		return 'ai1ec_';
	}

}
