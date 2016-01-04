<?php
if (! class_exists ( "PostmanSendGridMailEngine" )) {
	
	require_once 'sendgrid-php-3.2.0/sendgrid-php.php';
	
	/**
	 * Sends mail with the SendGrid API
	 * https://sendgrid.com/docs/API_Reference/Web_API/mail.html
	 *
	 * @author jasonhendriks
	 *        
	 */
	class PostmanSendGridMailEngine implements PostmanMailEngine {
		
		// logger for all concrete classes - populate with setLogger($logger)
		protected $logger;
		
		// the result
		private $transcript;
		
		//
		private $email;
		private $apiKey;
		
		/**
		 *
		 * @param unknown $senderEmail        	
		 * @param unknown $accessToken        	
		 */
		function __construct($apiKey) {
			assert ( ! empty ( $apiKey ) );
			$this->apiKey = $apiKey;
			
			// create the logger
			$this->logger = new PostmanLogger ( get_class ( $this ) );
			
			// create the Message
			$this->email = new SendGrid\Email ();
		}
		
		/**
		 * (non-PHPdoc)
		 *
		 * @see PostmanSmtpEngine::send()
		 */
		public function send(PostmanMessage $message) {
			$options = PostmanOptions::getInstance ();
			
			// add the Postman signature - append it to whatever the user may have set
			if (! $options->isStealthModeEnabled ()) {
				$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
				$this->email->addHeader ( 'X-Mailer', sprintf ( 'Postman SMTP %s for WordPress (%s)', $pluginData ['version'], 'https://wordpress.org/plugins/postman-smtp/' ) );
			}
			
			// add the headers - see http://framework.zend.com/manual/1.12/en/zend.mail.additional-headers.html
			foreach ( ( array ) $message->getHeaders () as $header ) {
				$this->logger->debug ( sprintf ( 'Adding user header %s=%s', $header ['name'], $header ['content'] ) );
				$this->email->addHeader ( $header ['name'], $header ['content'] );
			}
			
			// if the caller set a Content-Type header, use it
			$contentType = $message->getContentType ();
			if (! empty ( $contentType )) {
				$this->logger->debug ( 'Adding content-type ' . $contentType );
				$this->email->addHeader ( 'Content-Type', $contentType );
			}
			
			// add the From Header
			$sender = $message->getFromAddress ();
			{
				$senderEmail = $sender->getEmail ();
				$senderName = $sender->getName ();
				assert ( ! empty ( $senderEmail ) );
				$this->email->setFrom ( $senderEmail );
				if (! empty ( $senderName )) {
					$this->email->setFromName ( $senderName );
				}
				// now log it
				$sender->log ( $this->logger, 'From' );
			}
			
			// add the Sender Header, overriding what the user may have set
			$this->email->addHeader ( 'Sender', $options->getEnvelopeSender () );
			
			// add the to recipients
			foreach ( ( array ) $message->getToRecipients () as $recipient ) {
				$recipient->log ( $this->logger, 'To' );
				$this->email->addTo ( $recipient->getEmail (), $recipient->getName () );
			}
			
			// add the cc recipients
			foreach ( ( array ) $message->getCcRecipients () as $recipient ) {
				$recipient->log ( $this->logger, 'Cc' );
				$this->email->addCc ( $recipient->getEmail (), $recipient->getName () );
			}
			
			// add the bcc recipients
			foreach ( ( array ) $message->getBccRecipients () as $recipient ) {
				$recipient->log ( $this->logger, 'Bcc' );
				$this->email->addBcc ( $recipient->getEmail (), $recipient->getName () );
			}
			
			// add the reply-to
			$replyTo = $message->getReplyTo ();
			// $replyTo is null or a PostmanEmailAddress object
			if (isset ( $replyTo )) {
				$this->email->setReplyTo ( $replyTo->format () );
			}
			
			// add the date
			$date = $message->getDate ();
			if (! empty ( $date )) {
				$this->email->setDate ( $message->getDate () );
			}
			
			// add the messageId
			$messageId = $message->getMessageId ();
			if (! empty ( $messageId )) {
				$this->email->addHeader ( 'message-id', $messageId );
			}
			
			// add the subject
			if (null !== $message->getSubject ()) {
				$this->email->setSubject ( $message->getSubject () );
			}
			
			// add the message content
			{
				$textPart = $message->getBodyTextPart ();
				if (! empty ( $textPart )) {
					$this->logger->debug ( 'Adding body as text' );
					$this->email->setText ( $textPart );
				}
				$htmlPart = $message->getBodyHtmlPart ();
				if (! empty ( $htmlPart )) {
					$this->logger->debug ( 'Adding body as html' );
					$this->email->setHtml ( $htmlPart );
				}
			}
			
			// add attachments
			$this->logger->debug ( "Adding attachments" );
			$this->addAttachmentsToMail ( $message );
			
			$result = array ();
			try {
				
				if ($this->logger->isDebug ()) {
					$this->logger->debug ( "Creating SendGrid service with apiKey=" . $this->apiKey );
				}
				$sendgrid = new SendGrid ( $this->apiKey );
				
				// send the message
				if ($this->logger->isDebug ()) {
					$this->logger->debug ( "Sending mail" );
				}
				$result = $sendgrid->send ( $this->email );
				if ($this->logger->isInfo ()) {
					$this->logger->info (  );
				}
				$this->transcript = print_r ( $result, true );
				$this->transcript .= PostmanModuleTransport::RAW_MESSAGE_FOLLOWS;
				$this->transcript .= print_r ( $this->email, true );
			} catch ( SendGrid\Exception $e ) {
				$this->transcript = $e->getMessage ();
				$this->transcript .= PostmanModuleTransport::RAW_MESSAGE_FOLLOWS;
				$this->transcript .= print_r ( $this->email, true );
				throw $e;
			}
		}
		
		/**
		 * Add attachments to the message
		 *
		 * @param Postman_Zend_Mail $mail        	
		 */
		private function addAttachmentsToMail(PostmanMessage $message) {
			$attachments = $message->getAttachments ();
			if (! is_array ( $attachments )) {
				// WordPress may a single filename or a newline-delimited string list of multiple filenames
				$attArray = explode ( PHP_EOL, $attachments );
			} else {
				$attArray = $attachments;
			}
			// otherwise WordPress sends an array
			foreach ( $attArray as $file ) {
				if (! empty ( $file )) {
					$this->logger->debug ( "Adding attachment: " . $file );
					$this->email->addAttachment ( basename ( $file ) );
				}
			}
		}
		
		// return the SMTP session transcript
		public function getTranscript() {
			return $this->transcript;
		}
	}
}

