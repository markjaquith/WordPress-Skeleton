<?php
require_once 'PostmanModuleTransport.php';
if (! class_exists ( 'PostmanSmtpModuleTransport' )) {
	class PostmanDefaultModuleTransport extends PostmanAbstractZendModuleTransport implements PostmanZendModuleTransport {
		const SLUG = 'default';
		private $fromName;
		private $fromEmail;
		
		/**
		 *
		 * @param unknown $rootPluginFilenameAndPath        	
		 */
		public function __construct($rootPluginFilenameAndPath) {
			parent::__construct ( $rootPluginFilenameAndPath );
			$this->init ();
		}
		
		/**
		 * Copied from WordPress core
		 * Set the from name and email
		 */
		public function init() {
			parent::init();
			// From email and name
			// If we don't have a name from the input headers
			$this->fromName = 'WordPress';
			
			/*
			 * If we don't have an email from the input headers default to wordpress@$sitename
			 * Some hosts will block outgoing mail from this address if it doesn't exist but
			 * there's no easy alternative. Defaulting to admin_email might appear to be another
			 * option but some hosts may refuse to relay mail from an unknown domain. See
			 * https://core.trac.wordpress.org/ticket/5007.
			 */
			
			// Get the site domain and get rid of www.
			$sitename = strtolower ( $_SERVER ['SERVER_NAME'] );
			if (substr ( $sitename, 0, 4 ) == 'www.') {
				$sitename = substr ( $sitename, 4 );
			}
			
			$this->fromEmail = 'wordpress@' . $sitename;
		}
		public function isConfiguredAndReady() {
			return false;
		}
		public function isReadyToSendMail() {
			return true;
		}
		public function getFromEmailAddress() {
			return $this->fromEmail;
		}
		public function getFromName() {
			return $this->fromName;
		}
		public function getEnvelopeFromEmailAddress() {
			return $this->getFromEmailAddress ();
		}
		public function isEmailValidationSupported() {
			return false;
		}
		
		/**
		 * (non-PHPdoc)
		 *
		 * @see PostmanAbstractZendModuleTransport::validateTransportConfiguration()
		 */
		protected function validateTransportConfiguration() {
			return array ();
			// no-op, always valid
		}
		
		/**
		 * (non-PHPdoc)
		 *
		 * @see PostmanModuleTransport::createMailEngine()
		 */
		public function createMailEngine() {
			require_once 'PostmanZendMailEngine.php';
			return new PostmanZendMailEngine ( $this );
		}
		
		/**
		 * (non-PHPdoc)
		 *
		 * @see PostmanZendModuleTransport::createZendMailTransport()
		 */
		public function createZendMailTransport($fakeHostname, $fakeConfig) {
			$config = array (
					'port' => $this->getPort () 
			);
			return new Postman_Zend_Mail_Transport_Smtp ( $this->getHostname (), $config );
		}
		
		/**
		 * Determines whether Mail Engine locking is needed
		 *
		 * @see PostmanModuleTransport::requiresLocking()
		 */
		public function isLockingRequired() {
			return PostmanOptions::AUTHENTICATION_TYPE_OAUTH2 == $this->getAuthenticationType ();
		}
		public function getSlug() {
			return self::SLUG;
		}
		public function getName() {
			return __ ( 'Default', Postman::TEXT_DOMAIN );
		}
		public function getHostname() {
			return 'localhost';
		}
		public function getPort() {
			return 25;
		}
		public function getSecurityType() {
			return PostmanOptions::SECURITY_TYPE_NONE;
		}
		public function getAuthenticationType() {
			return PostmanOptions::AUTHENTICATION_TYPE_NONE;
		}
		public function getCredentialsId() {
			$options = PostmanOptions::getInstance ();
			if ($options->isAuthTypeOAuth2 ()) {
				return $options->getClientId ();
			} else {
				return $options->getUsername ();
			}
		}
		public function getCredentialsSecret() {
			$options = PostmanOptions::getInstance ();
			if ($options->isAuthTypeOAuth2 ()) {
				return $options->getClientSecret ();
			} else {
				return $options->getPassword ();
			}
		}
		public function isServiceProviderGoogle($hostname) {
			return PostmanUtils::endsWith ( $hostname, 'gmail.com' );
		}
		public function isServiceProviderMicrosoft($hostname) {
			return PostmanUtils::endsWith ( $hostname, 'live.com' );
		}
		public function isServiceProviderYahoo($hostname) {
			return strpos ( $hostname, 'yahoo' );
		}
		public function isOAuthUsed($authType) {
			return false;
		}
		public final function getConfigurationBid(PostmanWizardSocket $hostData, $userAuthOverride, $originalSmtpServer) {
			return null;
		}
		
		/**
		 * Does not participate in the Wizard process;
		 *
		 * (non-PHPdoc)
		 *
		 * @see PostmanModuleTransport::getSocketsForSetupWizardToProbe()
		 */
		public function getSocketsForSetupWizardToProbe($hostname, $smtpServerGuess) {
			return array ();
		}
	}
}
