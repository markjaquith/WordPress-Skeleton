<?php
if (! class_exists ( "PostmanWpMail" )) {
	
	/**
	 * Moved this code into a class so it could be used by both wp_mail() and PostmanSendTestEmailController
	 *
	 * @author jasonhendriks
	 *        
	 */
	class PostmanWpMail {
		private $exception;
		private $transcript;
		private $totalTime;
		private $logger;
		
		/**
		 * Load the dependencies
		 */
		public function init() {
			$this->logger = new PostmanLogger ( get_class ( $this ) );
			require_once 'Postman-Mail/PostmanMessage.php';
			require_once 'Postman-Email-Log/PostmanEmailLogService.php';
			require_once 'Postman-Mail/PostmanMailEngine.php';
			require_once 'Postman-Auth/PostmanAuthenticationManagerFactory.php';
			require_once 'PostmanState.php';
		}
		
		/**
		 * This methods follows the wp_mail function interface, but implements it Postman-style.
		 * Exceptions are held for later inspection.
		 * An instance of PostmanState updates the success/fail tally.
		 *
		 * @param unknown $to        	
		 * @param unknown $subject        	
		 * @param unknown $body        	
		 * @param unknown $headers        	
		 * @param unknown $attachments        	
		 * @return boolean
		 */
		public function send($to, $subject, $message, $headers = '', $attachments = array()) {
			
			// initialize for sending
			$this->init ();
			
			// build the message
			$postmanMessage = $this->processWpMailCall ( $to, $subject, $message, $headers, $attachments );
			
			// build the email log entry
			$log = new PostmanEmailLog ();
			$log->originalTo = $to;
			$log->originalSubject = $subject;
			$log->originalMessage = $message;
			$log->originalHeaders = $headers;
			
			// send the message and return the result
			return $this->sendMessage ( $postmanMessage, $log );
		}
		
		/**
		 * Builds a PostmanMessage based on the WordPress wp_mail parameters
		 *
		 * @param unknown $to        	
		 * @param unknown $subject        	
		 * @param unknown $message        	
		 * @param unknown $headers        	
		 * @param unknown $attachments        	
		 */
		private function processWpMailCall($to, $subject, $message, $headers, $attachments) {
			$this->logger->trace ( 'wp_mail parameters before applying WordPress wp_mail filter:' );
			$this->traceParameters ( $to, $subject, $message, $headers, $attachments );
			
			/**
			 * Filter the wp_mail() arguments.
			 *
			 * @since 1.5.4
			 *       
			 * @param array $args
			 *        	A compacted array of wp_mail() arguments, including the "to" email,
			 *        	subject, message, headers, and attachments values.
			 */
			$atts = apply_filters ( 'wp_mail', compact ( 'to', 'subject', 'message', 'headers', 'attachments' ) );
			if (isset ( $atts ['to'] )) {
				$to = $atts ['to'];
			}
			
			if (isset ( $atts ['subject'] )) {
				$subject = $atts ['subject'];
			}
			
			if (isset ( $atts ['message'] )) {
				$message = $atts ['message'];
			}
			
			if (isset ( $atts ['headers'] )) {
				$headers = $atts ['headers'];
			}
			
			if (isset ( $atts ['attachments'] )) {
				$attachments = $atts ['attachments'];
			}
			
			if (! is_array ( $attachments )) {
				$attachments = explode ( "\n", str_replace ( "\r\n", "\n", $attachments ) );
			}
			
			$this->logger->trace ( 'wp_mail parameters after applying WordPress wp_mail filter:' );
			$this->traceParameters ( $to, $subject, $message, $headers, $attachments );
			
			// Postman API: register the response hook
			add_filter ( 'postman_wp_mail_result', array (
					$this,
					'postman_wp_mail_result' 
			) );
			
			// create the message
			$postmanMessage = $this->createNewMessage ();
			$this->populateMessageFromWpMailParams ( $postmanMessage, $to, $subject, $message, $headers, $attachments );
			
			// return the message
			return $postmanMessage;
		}
		
		/**
		 * Creates a new instance of PostmanMessage with a pre-set From and Reply-To
		 *
		 * @return PostmanMessage
		 */
		public function createNewMessage() {
			$message = new PostmanMessage ();
			$options = PostmanOptions::getInstance ();
			// the From is set now so that it can be overridden
			$transport = PostmanTransportRegistry::getInstance ()->getActiveTransport ();
			$message->setFrom ( $transport->getFromEmailAddress (), $transport->getFromName () );
			// the Reply-To is set now so that it can be overridden
			$message->setReplyTo ( $options->getReplyTo () );
			$message->setCharset ( get_bloginfo ( 'charset' ) );
			return $message;
		}
		
		/**
		 * A convenient place for any code to inject a constructed PostmanMessage
		 * (for example, from MyMail)
		 *
		 * The body parts may be set already at this time.
		 *
		 * @param PostmanMessage $message        	
		 * @return boolean
		 */
		public function sendMessage(PostmanMessage $message, PostmanEmailLog $log) {
			
			// get the Options and AuthToken
			$options = PostmanOptions::getInstance ();
			$authorizationToken = PostmanOAuthToken::getInstance ();
			
			// get the transport and create the transportConfig and engine
			$transport = PostmanTransportRegistry::getInstance ()->getActiveTransport ();
			
			// create the Mail Engine
			$engine = $transport->createMailEngine ();
			
			// add plugin-specific attributes to PostmanMessage
			$message->addHeaders ( $options->getAdditionalHeaders () );
			$message->addTo ( $options->getForcedToRecipients () );
			$message->addCc ( $options->getForcedCcRecipients () );
			$message->addBcc ( $options->getForcedBccRecipients () );
			
			// apply the WordPress filters
			// may impact the from address, from email, charset and content-type
			$message->applyFilters ();
			
			// create the body parts (if they are both missing)
			if ($message->isBodyPartsEmpty ()) {
				$message->createBodyParts ();
			}
			
			// is this a test run?
			$testMode = apply_filters ( 'postman_test_email', false );
			if ($this->logger->isDebug ()) {
				$this->logger->debug ( 'testMode=' . $testMode );
			}
			
			// start the clock
			$startTime = microtime ( true ) * 1000;
			
			try {
				
				// prepare the message
				$message->validate ( $transport );
				
				// send the message
				if ($options->getRunMode () == PostmanOptions::RUN_MODE_PRODUCTION) {
					if ($transport->isLockingRequired ()) {
						PostmanUtils::lock ();
						// may throw an exception attempting to contact the OAuth2 provider
						$this->ensureAuthtokenIsUpdated ( $transport, $options, $authorizationToken );
					}
					
					$this->logger->debug ( 'Sending mail' );
					// may throw an exception attempting to contact the SMTP server
					$engine->send ( $message );
					
					// increment the success counter, unless we are just tesitng
					if (! $testMode) {
						PostmanState::getInstance ()->incrementSuccessfulDelivery ();
					}
				}
				
				// clean up
				$this->postSend ( $engine, $startTime, $options, $transport );
				
				if ($options->getRunMode () == PostmanOptions::RUN_MODE_PRODUCTION || $options->getRunMode () == PostmanOptions::RUN_MODE_LOG_ONLY) {
					// log the successful delivery
					PostmanEmailLogService::getInstance ()->writeSuccessLog ( $log, $message, $engine->getTranscript (), $transport );
				}
				
				// return successful
				return true;
			} catch ( Exception $e ) {
				// save the error for later
				$this->exception = $e;
				
				// write the error to the PHP log
				$this->logger->error ( get_class ( $e ) . ' code=' . $e->getCode () . ' message=' . trim ( $e->getMessage () ) );
				
				// increment the failure counter, unless we are just tesitng
				if (! $testMode && $options->getRunMode () == PostmanOptions::RUN_MODE_PRODUCTION) {
					PostmanState::getInstance ()->incrementFailedDelivery ();
				}
				
				// clean up
				$this->postSend ( $engine, $startTime, $options, $transport );
				
				if ($options->getRunMode () == PostmanOptions::RUN_MODE_PRODUCTION || $options->getRunMode () == PostmanOptions::RUN_MODE_LOG_ONLY) {
					// log the failed delivery
					PostmanEmailLogService::getInstance ()->writeFailureLog ( $log, $message, $engine->getTranscript (), $transport, $e->getMessage () );
				}
				
				// return failure
				return false;
			}
		}
		
		/**
		 * Clean up after sending the mail
		 *
		 * @param PostmanZendMailEngine $engine        	
		 * @param unknown $startTime        	
		 */
		private function postSend(PostmanMailEngine $engine, $startTime, PostmanOptions $options, PostmanModuleTransport $transport) {
			// save the transcript
			$this->transcript = $engine->getTranscript ();
			
			// log the transcript
			if ($this->logger->isTrace ()) {
				$this->logger->trace ( 'Transcript:' );
				$this->logger->trace ( $this->transcript );
			}
			
			// delete the semaphore
			if ($transport->isLockingRequired ()) {
				PostmanUtils::unlock ();
			}
			
			// stop the clock
			$endTime = microtime ( true ) * 1000;
			$this->totalTime = $endTime - $startTime;
		}
		
		/**
		 * Returns the result of the last call to send()
		 *
		 * @return multitype:Exception NULL
		 */
		function postman_wp_mail_result() {
			$result = array (
					'time' => $this->totalTime,
					'exception' => $this->exception,
					'transcript' => $this->transcript 
			);
			return $result;
		}
		
		/**
		 */
		private function ensureAuthtokenIsUpdated(PostmanModuleTransport $transport, PostmanOptions $options, PostmanOAuthToken $authorizationToken) {
			assert ( ! empty ( $transport ) );
			assert ( ! empty ( $options ) );
			assert ( ! empty ( $authorizationToken ) );
			// ensure the token is up-to-date
			$this->logger->debug ( 'Ensuring Access Token is up-to-date' );
			// interact with the Authentication Manager
			$wpMailAuthManager = PostmanAuthenticationManagerFactory::getInstance ()->createAuthenticationManager ();
			if ($wpMailAuthManager->isAccessTokenExpired ()) {
				$this->logger->debug ( 'Access Token has expired, attempting refresh' );
				$wpMailAuthManager->refreshToken ();
				$authorizationToken->save ();
			}
		}
		
		/**
		 * Aggregates all the content into a Message to be sent to the MailEngine
		 *
		 * @param unknown $to        	
		 * @param unknown $subject        	
		 * @param unknown $body        	
		 * @param unknown $headers        	
		 * @param unknown $attachments        	
		 */
		private function populateMessageFromWpMailParams(PostmanMessage $message, $to, $subject, $body, $headers, $attachments) {
			$message->addHeaders ( $headers );
			$message->setBody ( $body );
			$message->setSubject ( $subject );
			$message->addTo ( $to );
			$message->setAttachments ( $attachments );
			return $message;
		}
		
		/**
		 * Trace the parameters to aid in debugging
		 *
		 * @param unknown $to        	
		 * @param unknown $subject        	
		 * @param unknown $body        	
		 * @param unknown $headers        	
		 * @param unknown $attachments        	
		 */
		private function traceParameters($to, $subject, $message, $headers, $attachments) {
			$this->logger->trace ( 'to:' );
			$this->logger->trace ( $to );
			$this->logger->trace ( 'subject:' );
			$this->logger->trace ( $subject );
			$this->logger->trace ( 'headers:' );
			$this->logger->trace ( $headers );
			$this->logger->trace ( 'attachments:' );
			$this->logger->trace ( $attachments );
			$this->logger->trace ( 'message:' );
			$this->logger->trace ( $message );
		}
	}
}