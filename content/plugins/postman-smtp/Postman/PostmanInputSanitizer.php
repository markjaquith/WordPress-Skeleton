<?php
if (! class_exists ( 'PostmanInputSanitizer' )) {
	class PostmanInputSanitizer {
		private $logger;
		private $options;
		const VALIDATION_SUCCESS = 'validation_success';
		const VALIDATION_FAILED = 'validation_failed';
		public function __construct() {
			$this->logger = new PostmanLogger ( get_class ( $this ) );
			$this->options = PostmanOptions::getInstance();
		}
		
		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input
		 *        	Contains all settings fields as array keys
		 */
		public function sanitize($input) {
			$this->logger->debug ( "Sanitizing data before storage" );
			
			$new_input = array ();
			$success = true;
			
			$this->sanitizeString ( 'Encryption Type', PostmanOptions::SECURITY_TYPE, $input, $new_input );
			$this->sanitizeString ( 'Hostname', PostmanOptions::HOSTNAME, $input, $new_input );
			if (! empty ( $input [PostmanOptions::PORT] )) {
				$port = absint ( $input [PostmanOptions::PORT] );
				if ($port > 0) {
					$this->sanitizeInt ( 'Port', PostmanOptions::PORT, $input, $new_input );
				} else {
					$new_input [PostmanOptions::PORT] = $this->options->getPort ();
					add_settings_error ( PostmanOptions::PORT, PostmanOptions::PORT, 'Invalid TCP Port', 'error' );
					$success = false;
				}
			}
			// check the auth type AFTER the hostname because we reset the hostname if auth is bad
			$this->sanitizeString ( 'From Email', PostmanOptions::MESSAGE_SENDER_EMAIL, $input, $new_input );
			// the wizard doesn't set an envelope sender, so we'll default it to From Email
			$new_input [PostmanOptions::ENVELOPE_SENDER] = $new_input [PostmanOptions::MESSAGE_SENDER_EMAIL];
			$this->sanitizeString ( 'Sender Email', PostmanOptions::ENVELOPE_SENDER, $input, $new_input );
			$this->sanitizeString ( 'Transport Type', PostmanOptions::TRANSPORT_TYPE, $input, $new_input );
			$this->sanitizeString ( 'Authorization Type', PostmanOptions::AUTHENTICATION_TYPE, $input, $new_input );
			$this->sanitizeString ( 'From Name', PostmanOptions::MESSAGE_SENDER_NAME, $input, $new_input );
			$this->sanitizeString ( 'Client ID', PostmanOptions::CLIENT_ID, $input, $new_input );
			$this->sanitizeString ( 'Client Secret', PostmanOptions::CLIENT_SECRET, $input, $new_input );
			$this->sanitizeString ( 'Username', PostmanOptions::BASIC_AUTH_USERNAME, $input, $new_input );
			$this->sanitizePassword ( 'Password', PostmanOptions::BASIC_AUTH_PASSWORD, $input, $new_input, $this->options->getPassword () );
			$this->sanitizePassword ( 'Mandrill API Key', PostmanOptions::MANDRILL_API_KEY, $input, $new_input, $this->options->getMandrillApiKey () );
			$this->sanitizePassword ( 'SendGrid API Key', PostmanOptions::SENDGRID_API_KEY, $input, $new_input, $this->options->getSendGridApiKey () );
			$this->sanitizeString ( 'Reply-To', PostmanOptions::REPLY_TO, $input, $new_input );
			$this->sanitizeString ( 'From Name Override', PostmanOptions::PREVENT_MESSAGE_SENDER_NAME_OVERRIDE, $input, $new_input );
			$this->sanitizeString ( 'From Email Override', PostmanOptions::PREVENT_MESSAGE_SENDER_EMAIL_OVERRIDE, $input, $new_input );
			$this->sanitizeString ( 'Disable Email Validation', PostmanOptions::DISABLE_EMAIL_VALIDAITON, $input, $new_input );
			$this->sanitizeString ( 'Forced To Recipients', PostmanOptions::FORCED_TO_RECIPIENTS, $input, $new_input );
			$this->sanitizeString ( 'Forced CC Recipients', PostmanOptions::FORCED_CC_RECIPIENTS, $input, $new_input );
			$this->sanitizeString ( 'Forced BCC Recipients', PostmanOptions::FORCED_BCC_RECIPIENTS, $input, $new_input );
			$this->sanitizeString ( 'Additional Headers', PostmanOptions::ADDITIONAL_HEADERS, $input, $new_input );
			$this->sanitizeInt ( 'Read Timeout', PostmanOptions::READ_TIMEOUT, $input, $new_input );
			$this->sanitizeInt ( 'Conenction Timeout', PostmanOptions::CONNECTION_TIMEOUT, $input, $new_input );
			$this->sanitizeInt ( 'Log Level', PostmanOptions::LOG_LEVEL, $input, $new_input );
			$this->sanitizeString ( 'Email Log Enabled', PostmanOptions::MAIL_LOG_ENABLED_OPTION, $input, $new_input );
			$this->sanitizeLogMax ( 'Email Log Max Entries', PostmanOptions::MAIL_LOG_MAX_ENTRIES, $input, $new_input );
			$this->sanitizeString ( 'Run Mode', PostmanOptions::RUN_MODE, $input, $new_input );
			$this->sanitizeString ( 'Stealth Mode', PostmanOptions::STEALTH_MODE, $input, $new_input );
			$this->sanitizeInt ( 'Transcript Size', PostmanOptions::TRANSCRIPT_SIZE, $input, $new_input );
			$this->sanitizeString ( 'Temporary Directory', PostmanOptions::TEMPORARY_DIRECTORY, $input, $new_input );
			
			if ($new_input [PostmanOptions::CLIENT_ID] != $this->options->getClientId () || $new_input [PostmanOptions::CLIENT_SECRET] != $this->options->getClientSecret () || $new_input [PostmanOptions::HOSTNAME] != $this->options->getHostname ()) {
				$this->logger->debug ( "Recognized new Client ID" );
				// the user entered a new client id and we should destroy the stored auth token
				delete_option ( PostmanOAuthToken::OPTIONS_NAME );
			}
			
			// can we create a tmp file? - this code is duplicated in ActivationHandler
			PostmanUtils::deleteLockFile ( $new_input [PostmanOptions::TEMPORARY_DIRECTORY] );
			$lockSuccess = PostmanUtils::createLockFile ( $new_input [PostmanOptions::TEMPORARY_DIRECTORY] );
			// &= does not work as expected in my PHP
			$lockSuccess = $lockSuccess && PostmanUtils::deleteLockFile ( $new_input [PostmanOptions::TEMPORARY_DIRECTORY] );
			$this->logger->debug ( 'FileLocking=' . $lockSuccess );
			PostmanState::getInstance ()->setFileLockingEnabled ( $lockSuccess );
			
			if ($success) {
				PostmanSession::getInstance ()->setAction ( self::VALIDATION_SUCCESS );
			} else {
				PostmanSession::getInstance ()->setAction ( self::VALIDATION_FAILED );
			}
			
			return $new_input;
		}
		private function sanitizeString($desc, $key, $input, &$new_input) {
			if (isset ( $input [$key] )) {
				$this->logSanitize ( $desc, $input [$key] );
				$new_input [$key] = trim ( $input [$key] );
			}
		}
		
		/**
		 * Sanitize a Basic Auth password, and base64-encode it
		 *
		 * @param unknown $desc        	
		 * @param unknown $key        	
		 * @param unknown $input        	
		 * @param unknown $new_input        	
		 */
		private function sanitizePassword($desc, $key, $input, &$new_input, $existingPassword) {
			// WordPress calling Sanitize twice is a known issue
			// https://core.trac.wordpress.org/ticket/21989
			$action = PostmanSession::getInstance ()->getAction ();
			// if $action is not empty, then sanitize has already run
			if (! empty ( $action )) {
				// use the already encoded password in the $input
				$new_input [$key] = $input [$key];
				// log it
				$this->logger->debug ( 'Warning, second sanitizePassword attempt detected' );
			} else if (isset ( $input [$key] )) {
				if (strlen ( $input [$key] ) > 0 && preg_match ( '/^\**$/', $input [$key] )) {
					// if the password is all stars, then keep the existing password
					$new_input [$key] = $existingPassword;
				} else {
					// otherwise the password is new, so trim it
					$new_input [$key] = trim ( $input [$key] );
				}
				// log it
				$this->logSanitize ( $desc, $new_input [$key] );
				// base-64 scramble password
				$new_input [$key] = base64_encode ( $new_input [$key] );
			}
			$this->logger->debug ( sprintf ( 'Encoding %s as %s', $desc, $new_input [$key] ) );
		}
		private function sanitizeLogMax($desc, $key, $input, &$new_input) {
			if (isset ( $input [$key] )) {
				$value = absint ( $input [$key] );
				if ($value <= 0) {
					$new_input [$key] = PostmanOptions::getInstance ()->getMailLoggingMaxEntries ();
					$h = new PostmanMessageHandler ();
					$h->addError ( sprintf ( '%s %s', __ ( 'Maximum Log Entries', 'Configuration Input Field', Postman::TEXT_DOMAIN ), __ ( 'must be greater than 0', Postman::TEXT_DOMAIN ) ) );
				} else {
					$this->logSanitize ( $desc, $input [$key] );
					$new_input [$key] = $value;
				}
			}
		}
		private function sanitizeInt($desc, $key, $input, &$new_input) {
			if (isset ( $input [$key] )) {
				$this->logSanitize ( $desc, $input [$key] );
				$new_input [$key] = absint ( $input [$key] );
			}
		}
		private function logSanitize($desc, $value) {
			$this->logger->trace ( 'Sanitize ' . $desc . ' ' . $value );
		}
	}
}
