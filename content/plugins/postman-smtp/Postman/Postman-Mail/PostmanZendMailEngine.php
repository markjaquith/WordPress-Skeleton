<?php
if (! class_exists ( "PostmanZendMailEngine" )) {
	
	require_once 'Zend-1.12.10/Loader.php';
	require_once 'Zend-1.12.10/Registry.php';
	require_once 'Zend-1.12.10/Mime/Message.php';
	require_once 'Zend-1.12.10/Mime/Part.php';
	require_once 'Zend-1.12.10/Mime.php';
	require_once 'Zend-1.12.10/Validate/Interface.php';
	require_once 'Zend-1.12.10/Validate/Abstract.php';
	require_once 'Zend-1.12.10/Validate.php';
	require_once 'Zend-1.12.10/Validate/Ip.php';
	require_once 'Zend-1.12.10/Validate/Hostname.php';
	require_once 'Zend-1.12.10/Mail.php';
	require_once 'Zend-1.12.10/Exception.php';
	require_once 'Zend-1.12.10/Mail/Exception.php';
	require_once 'Zend-1.12.10/Mail/Transport/Exception.php';
	require_once 'Zend-1.12.10/Mail/Transport/Abstract.php';
	require_once 'Zend-1.12.10/Mail/Transport/Smtp.php';
	require_once 'Zend-1.12.10/Mail/Transport/Sendmail.php';
	require_once 'Zend-1.12.10/Mail/Protocol/Abstract.php';
	require_once 'Zend-1.12.10/Mail/Protocol/Exception.php';
	require_once 'Zend-1.12.10/Mail/Protocol/Smtp.php';
	require_once 'Zend-1.12.10/Mail/Protocol/Smtp/Auth/Oauth2.php';
	require_once 'Zend-1.12.10/Mail/Protocol/Smtp/Auth/Login.php';
	require_once 'Zend-1.12.10/Mail/Protocol/Smtp/Auth/Crammd5.php';
	require_once 'Zend-1.12.10/Mail/Protocol/Smtp/Auth/Plain.php';
	
	/**
	 * This class knows how to interface with Wordpress
	 * including loading/saving to the database.
	 *
	 * The various Transports available:
	 * http://framework.zend.com/manual/current/en/modules/zend.mail.smtp.options.html
	 *
	 * @author jasonhendriks
	 *        
	 */
	class PostmanZendMailEngine implements PostmanMailEngine {
		
		// logger for all concrete classes - populate with setLogger($logger)
		protected $logger;
		
		// the result
		private $transcript;
		
		//
		private $transport;
		
		/**
		 *
		 * @param unknown $senderEmail        	
		 * @param unknown $accessToken        	
		 */
		function __construct(PostmanZendModuleTransport $transport) {
			assert ( isset ( $transport ) );
			$this->transport = $transport;
			
			// create the logger
			$this->logger = new PostmanLogger ( get_class ( $this ) );
		}
		
		/**
		 * (non-PHPdoc)
		 *
		 * @see PostmanSmtpEngine::send()
		 */
		public function send(PostmanMessage $message) {
			$this->logger->debug ( "Prepping Zend" );
			$envelopeFrom = new PostmanEmailAddress ( $this->transport->getEnvelopeFromEmailAddress () );
			if ($this->transport->isEnvelopeFromValidationSupported ()) {
				// validate the envelope from since we didn't do it in the Message
				$envelopeFrom->validate ( 'Envelope From' );
			}
			
			// create the Message
			$charset = $message->getCharset ();
			$this->logger->debug ( 'Building Postman_Zend_Mail with charset=' . $charset );
			$mail = new Postman_Zend_Mail ( $charset );
			
			// add the Postman signature - append it to whatever the user may have set
			if (! PostmanOptions::getInstance ()->isStealthModeEnabled ()) {
				$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
				$mail->addHeader ( 'X-Mailer', sprintf ( 'Postman SMTP %s for WordPress (%s)', $pluginData ['version'], 'https://wordpress.org/plugins/postman-smtp/' ) );
			}
			
			// add the headers - see http://framework.zend.com/manual/1.12/en/zend.mail.additional-headers.html
			foreach ( ( array ) $message->getHeaders () as $header ) {
				$this->logger->debug ( sprintf ( 'Adding user header %s=%s', $header ['name'], $header ['content'] ) );
				$mail->addHeader ( $header ['name'], $header ['content'], true );
			}
			
			// if the caller set a Content-Type header, use it
			$contentType = $message->getContentType ();
			if (! empty ( $contentType )) {
				$mail->addHeader ( 'Content-Type', $contentType, false );
				$this->logger->debug ( 'Adding content-type ' . $contentType );
			}
			
			// add the From Header
			$fromHeader = $this->addFrom ( $message, $mail );
			$fromHeader->log ( $this->logger, 'From' );
			
			// add the Sender Header, overriding what the user may have set
			$mail->addHeader ( 'Sender', $this->transport->getFromEmailAddress (), false );
			// from RFC 5321: http://tools.ietf.org/html/rfc5321#section-4.4
			// A message-originating SMTP system SHOULD NOT send a message that
			// already contains a Return-path header field.
			// I changed Zend/Mail/Mail.php to fix this
			$mail->setReturnPath ( $this->transport->getEnvelopeFromEmailAddress () );
			
			// add the to recipients
			foreach ( ( array ) $message->getToRecipients () as $recipient ) {
				$recipient->log ( $this->logger, 'To' );
				$mail->addTo ( $recipient->getEmail (), $recipient->getName () );
			}
			
			// add the cc recipients
			foreach ( ( array ) $message->getCcRecipients () as $recipient ) {
				$recipient->log ( $this->logger, 'Cc' );
				$mail->addCc ( $recipient->getEmail (), $recipient->getName () );
			}
			
			// add the to recipients
			foreach ( ( array ) $message->getBccRecipients () as $recipient ) {
				$recipient->log ( $this->logger, 'Bcc' );
				$mail->addBcc ( $recipient->getEmail (), $recipient->getName () );
			}
			
			// add the reply-to
			$replyTo = $message->getReplyTo ();
			// $replyTo is null or a PostmanEmailAddress object
			if (isset ( $replyTo )) {
				$mail->setReplyTo ( $replyTo->getEmail (), $replyTo->getName () );
			}
			
			// add the date
			$date = $message->getDate ();
			if (! empty ( $date )) {
				$mail->setDate ( $date );
			}
			
			// add the messageId
			$messageId = $message->getMessageId ();
			if (! empty ( $messageId )) {
				$mail->setMessageId ( $messageId );
			}
			
			// add the subject
			if (null !== $message->getSubject ()) {
				$mail->setSubject ( $message->getSubject () );
			}
			
			// add the message content
			{
				$textPart = $message->getBodyTextPart ();
				if (! empty ( $textPart )) {
					$this->logger->debug ( 'Adding body as text' );
					$mail->setBodyText ( $textPart );
				}
				$htmlPart = $message->getBodyHtmlPart ();
				if (! empty ( $htmlPart )) {
					$this->logger->debug ( 'Adding body as html' );
					$mail->setBodyHtml ( $htmlPart );
				}
			}
			
			// add attachments
			$this->logger->debug ( "Adding attachments" );
			$message->addAttachmentsToMail ( $mail );
			
			// create the SMTP transport
			$this->logger->debug ( "Create the Zend_Mail transport" );
			$zendTransport = $this->transport->createZendMailTransport ( $this->transport->getHostname (), array () );
			
			try {
				// send the message
				$this->logger->debug ( "Sending mail" );
				$mail->send ( $zendTransport );
				if ($this->logger->isInfo ()) {
					$this->logger->info ( sprintf ( 'Message %d accepted for delivery', PostmanState::getInstance ()->getSuccessfulDeliveries () + 1 ) );
				}
				// finally not supported??
				if ($zendTransport->getConnection () && ! PostmanUtils::isEmpty ( $zendTransport->getConnection ()->getLog () )) {
					$this->transcript = $zendTransport->getConnection ()->getLog ();
				} else if (method_exists ( $zendTransport, 'getTranscript' ) && ! PostmanUtils::isEmpty ( $zendTransport->getTranscript () )) {
					// then use the API response
					$this->transcript = $zendTransport->getTranscript ();
				} else if (method_exists ( $zendTransport, 'getMessage' ) && ! PostmanUtils::isEmpty ( $zendTransport->getMessage () )) {
					// then use the Raw Message as the Transcript
					$this->transcript = $zendTransport->getMessage ();
				}
			} catch ( Exception $e ) {
				// finally not supported??
				if ($zendTransport->getConnection () && ! PostmanUtils::isEmpty ( $zendTransport->getConnection ()->getLog () )) {
					$this->transcript = $zendTransport->getConnection ()->getLog ();
				} else if (method_exists ( $zendTransport, 'getTranscript' ) && ! PostmanUtils::isEmpty ( $zendTransport->getTranscript () )) {
					// then use the API response
					$this->transcript = $zendTransport->getTranscript ();
				} else if (method_exists ( $zendTransport, 'getMessage' ) && ! PostmanUtils::isEmpty ( $zendTransport->getMessage () )) {
					// then use the Raw Message as the Transcript
					$this->transcript = $zendTransport->getMessage ();
				}
				
				// get the current exception message
				$message = $e->getMessage ();
				if ($e->getCode () == 334) {
					// replace the unusable Google message with a better one in the case of code 334
					$message = sprintf ( __ ( 'Communication Error [334] - make sure the Envelope From email is the same account used to create the Client ID.', Postman::TEXT_DOMAIN ) );
				}
				// create a new exception
				$newException = new Exception ( $message, $e->getCode () );
				// throw the new exception after handling
				throw $newException;
			}
		}
		
		/**
		 * Get the sender from PostmanMessage and add it to the Postman_Zend_Mail object
		 *
		 * @param PostmanMessage $message        	
		 * @param Postman_Zend_Mail $mail        	
		 * @return PostmanEmailAddress
		 */
		public function addFrom(PostmanMessage $message, Postman_Zend_Mail $mail) {
			$sender = $message->getFromAddress ();
			// now log it and push it into the message
			$senderEmail = $sender->getEmail ();
			$senderName = $sender->getName ();
			assert ( ! empty ( $senderEmail ) );
			if (! empty ( $senderName )) {
				$mail->setFrom ( $senderEmail, $senderName );
			} else {
				$mail->setFrom ( $senderEmail );
			}
			return $sender;
		}
		
		// return the SMTP session transcript
		public function getTranscript() {
			return $this->transcript;
		}
	}
}

