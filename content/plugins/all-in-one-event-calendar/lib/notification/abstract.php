<?php

/**
 * Abstract class for notifications.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Notification
 */
abstract class Ai1ec_Notification extends Ai1ec_Base {

	/**
	 * @var string The message to send.
	 */
	protected $_message;

	/**
	 * @var array A list of recipients.
	 */
	protected $_recipients = array();

	/**
	 * This function performs the actual sending of the message.
	 *
	 * Must be implemented in child classes.
	 *
	 * @return bool Success.
	 */
	abstract public function send();

}