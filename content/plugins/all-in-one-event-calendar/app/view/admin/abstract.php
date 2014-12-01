<?php

/**
 * The abstract class for a admin page.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
abstract class Ai1ec_View_Admin_Abstract extends Ai1ec_Base {

	/**
	 * @var string
	 */
	protected $_page_id;

	/**
	 * @var string
	 */
	protected $_page_suffix;

	/**
	 * Standard constructor
	 * 
	 * @param Ai1ec_Registry_Object $registry
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$exploded_class     = explode( '_', get_class( $this ) );
		$this->_page_suffix = strtolower( end( $exploded_class ) );
	}

	/**
	 * Get the url of the page
	 * 
	 * @return string
	 */
	public function get_url() {
		return add_query_arg(
			array(
				'post_type' => AI1EC_POST_TYPE,
				'page'      => AI1EC_PLUGIN_NAME . '-' . $this->_page_suffix,
			),
			get_admin_url() . 'edit.php'
		);
	}

	/**
	 * Adds the page to the correct menu.
	 */
	abstract public function add_page();

	/**
	 * Adds the page to the correct menu.
	 */
	abstract public function add_meta_box();
	
	/**
	 * Display the page html
	 */
	abstract public function display_page();

	/**
	 * Handle post, likely to be deprecated to use commands.
	 */
	abstract public function handle_post();

}