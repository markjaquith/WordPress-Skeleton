<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Postman_Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 *
 * @see Postman_Zend_Mime
 */
// require_once 'Zend/Mime.php';

/**
 *
 * @see Postman_Zend_Mail_Protocol_Smtp
 */
// require_once 'Zend/Mail/Protocol/Smtp.php';

/**
 *
 * @see Postman_Zend_Mail_Transport_Abstract
 */
// require_once 'Zend/Mail/Transport/Abstract.php';

/**
 * SMTP connection object
 *
 * Loads an instance of Postman_Zend_Mail_Protocol_Smtp and forwards smtp transactions
 *
 * @category Zend
 * @package Postman_Zend_Mail
 * @subpackage Transport
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
if (! class_exists ( 'PostmanGmailApiModuleZendMailTransport' )) {
	class PostmanGmailApiModuleZendMailTransport extends Postman_Zend_Mail_Transport_Abstract {
		const SERVICE_OPTION = 'service';
		const MESSAGE_SENDER_EMAIL_OPTION = 'sender_email';
		private $logger;
		private $message;
		private $transcript;
		
		/**
		 * EOL character string used by transport
		 *
		 * @var string
		 * @access public
		 */
		public $EOL = "\n";
		
		/**
		 * Remote smtp hostname or i.p.
		 *
		 * @var string
		 */
		protected $_host;
		
		/**
		 * Port number
		 *
		 * @var integer|null
		 */
		protected $_port;
		
		/**
		 * Local client hostname or i.p.
		 *
		 * @var string
		 */
		protected $_name = 'localhost';
		
		/**
		 * Authentication type OPTIONAL
		 *
		 * @var string
		 */
		protected $_auth;
		
		/**
		 * Config options for authentication
		 *
		 * @var array
		 */
		protected $_config;
		
		/**
		 * Instance of Postman_Zend_Mail_Protocol_Smtp
		 *
		 * @var Postman_Zend_Mail_Protocol_Smtp
		 */
		protected $_connection;
		
		/**
		 * Constructor.
		 *
		 * @param string $host
		 *        	OPTIONAL (Default: 127.0.0.1)
		 * @param array|null $config
		 *        	OPTIONAL (Default: null)
		 * @return void
		 *
		 * @todo Someone please make this compatible
		 *       with the SendMail transport class.
		 */
		public function __construct($host = '127.0.0.1', Array $config = array()) {
			if (isset ( $config ['name'] )) {
				$this->_name = $config ['name'];
			}
			if (isset ( $config ['port'] )) {
				$this->_port = $config ['port'];
			}
			if (isset ( $config ['auth'] )) {
				$this->_auth = $config ['auth'];
			}
			
			$this->_host = $host;
			$this->_config = $config;
			$this->logger = new PostmanLogger ( get_class ( $this ) );
		}
		
		/**
		 * Class destructor to ensure all open connections are closed
		 *
		 * @return void
		 */
		public function __destruct() {
			if ($this->_connection instanceof Postman_Zend_Mail_Protocol_Smtp) {
				try {
					$this->_connection->quit ();
				} catch ( Postman_Zend_Mail_Protocol_Exception $e ) {
					// ignore
				}
				$this->_connection->disconnect ();
			}
		}
		
		/**
		 * Sets the connection protocol instance
		 *
		 * @param Postman_Zend_Mail_Protocol_Abstract $client        	
		 *
		 * @return void
		 */
		public function setConnection(Postman_Zend_Mail_Protocol_Abstract $connection) {
			$this->_connection = $connection;
		}
		
		/**
		 * Gets the connection protocol instance
		 *
		 * @return Postman_Zend_Mail_Protocol|null
		 */
		public function getConnection() {
			return $this->_connection;
		}
		
		/**
		 * Send an email via the Gmail API
		 *
		 * Uses URI https://www.googleapis.com
		 *
		 *
		 * @return void
		 * @todo Rename this to sendMail, it's a public method...
		 */
		public function _sendMail() {
			
			// Prepare the message in message/rfc822
			$message = $this->header . Postman_Zend_Mime::LINEEND . $this->body;
			$this->message = $message;
			
			// The message needs to be encoded in Base64URL
			$encodedMessage = rtrim ( strtr ( base64_encode ( $message ), '+/', '-_' ), '=' );
			$googleApiMessage = new Postman_Google_Service_Gmail_Message ();
			$googleApiMessage->setRaw ( $encodedMessage );
			$googleService = $this->_config [self::SERVICE_OPTION];
			$result = array ();
			try {
				$result = $googleService->users_messages->send ( 'me', $googleApiMessage );
				if ($this->logger->isInfo ()) {
					$this->logger->info ( sprintf ( 'Message %d accepted for delivery', PostmanState::getInstance ()->getSuccessfulDeliveries () + 1 ) );
				}
				$this->transcript = print_r ( $result, true );
				$this->transcript .= PostmanModuleTransport::RAW_MESSAGE_FOLLOWS;
				$this->transcript .= $message;
			} catch ( Exception $e ) {
				$this->transcript = $e->getMessage ();
				$this->transcript .= PostmanModuleTransport::RAW_MESSAGE_FOLLOWS;
				$this->transcript .= $message;
				throw $e;
			}
		}
		public function getMessage() {
			return $this->message;
		}
		public function getTranscript() {
			return $this->transcript;
		}
		
		/**
		 * Format and fix headers
		 *
		 * Some SMTP servers do not strip BCC headers. Most clients do it themselves as do we.
		 *
		 * @access protected
		 * @param array $headers        	
		 * @return void
		 * @throws Postman_Zend_Transport_Exception
		 */
		protected function _prepareHeaders($headers) {
			if (! $this->_mail) {
				/**
				 *
				 * @see Postman_Zend_Mail_Transport_Exception
				 */
				// require_once 'Zend/Mail/Transport/Exception.php';
				throw new Postman_Zend_Mail_Transport_Exception ( '_prepareHeaders requires a registered Postman_Zend_Mail object' );
			}
			
			// google will unset the Bcc header for us.
			// unset ( $headers ['Bcc'] );
			
			// Prepare headers
			parent::_prepareHeaders ( $headers );
		}
	}
}