<?php

/**
 * Concrete implementation for email notifications.
 *
 * @author       Time.ly Network Inc.
 * @since        2.0
 * @instantiator new
 * @package      AI1EC
 * @subpackage   AI1EC.Notification
 */
class Ai1ec_Email_Notification extends Ai1ec_Notification {

	/**
	 * @var string
	 */
	private $_subject;

	/**
	 * @var array
	 */
	private $_translations = array();

	/**
	 * @param array: $translations
	 */
	public function set_translations( array $translations ) {
		$this->_translations = $translations;
	}

	public function __construct(
		Ai1ec_Registry_Object $registry,
		$message,
		array $recipients,
		$subject
	) {
		parent::__construct( $registry );
		$this->_message    = $message;
		$this->_recipients = $recipients;
		$this->_subject    = $subject;
	}

	public function send() {
		$this->_parse_text();
		return wp_mail( $this->_recipients, $this->_subject, $this->_message );
	}

	private function _parse_text() {
		$this->_message = strtr( $this->_message, $this->_translations );
		$this->_subject = strtr( $this->_subject, $this->_translations );
	}

}