<?php

/**
 * The exception thrown when value doesn't pass validation.
 *
 * @author     Time.ly Network Inc.
 * @since      2.2
 *
 * @package    AI1EC
 * @subpackage AI1EC.Lib
 */
class Ai1ec_Outdated_Addon_Exception extends Ai1ec_Exception {

	protected $_addon;

	/**
	 * Constructor.
	 *
	 * @param string $message Exception message.
	 * @param string $addon   Addon to disable.
	 *
	 * @return void Method does not return.
	 */
	public function __construct( $message, $addon ) {
		parent::__construct( $message );
		$this->_addon = $addon;
	}

	/**
	 * Returns addon name.
	 *
	 * @return string Addon name.
	 */
	public function plugin_to_disable() {
		return $this->_addon;
	}

	/**
	 * Overrides __toString() to avoid stack trace.
	 *
	 * @return string Empty string.
	 */
	public function __toString() {
		return '';
	}
}