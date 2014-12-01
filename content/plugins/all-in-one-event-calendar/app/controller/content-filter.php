<?php

/**
 * Handles strict_compatibility_content_filtering.
 *
 * @author     Time.ly Network Inc.
 * @since      2.1
 *
 * @package    AI1EC
 * @subpackage AI1EC.Controller
 */
class Ai1ec_Controller_Content_Filter extends Ai1ec_Base {

	/**
	 * Content filters lib.
	 * @var Ai1ec_Content_Filters
	 */
	protected $_content_filter;

	/**
	 * Setting _strict_compatibility_content_filtering.
	 * @var bool
	 */
	protected $_strict_compatibility_content_filtering;

	/**
	 * Constructor.
	 *
	 * @param Ai1ec_Registry_Object $registry Registry object.
	 *
	 * @return void Method does not return.
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$this->_content_filter = $registry->get( 'content.filter' );
		$this->_strict_compatibility_content_filtering =
			$registry->get( 'model.settings' )
				->get( 'strict_compatibility_content_filtering' );
	}

	/**
	 * Clears all the_content filters excluding few defaults.
	 *
	 * @return void Method does not return.
	 */
	public function clear_the_content_filters() {
		if ( $this->_strict_compatibility_content_filtering ) {
			$this->_content_filter->clear_the_content_filters();
		}
	}

	/**
	 * Restores the_content filters.
	 *
	 * @return void Method does not return.
	 */
	public function restore_the_content_filters() {
		if ( $this->_strict_compatibility_content_filtering ) {
			$this->_content_filter->restore_the_content_filters();
		}
	}
}