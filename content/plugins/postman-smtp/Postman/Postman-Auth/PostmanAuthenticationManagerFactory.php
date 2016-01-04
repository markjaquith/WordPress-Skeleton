<?php
if (! class_exists ( "PostmanAuthenticationManagerFactory" )) {
	
	require_once 'PostmanGoogleAuthenticationManager.php';
	require_once 'PostmanMicrosoftAuthenticationManager.php';
	require_once 'PostmanNonOAuthAuthenticationManager.php';
	require_once 'PostmanYahooAuthenticationManager.php';
	
	//
	class PostmanAuthenticationManagerFactory {
		private $logger;
		
		// singleton instance
		public static function getInstance() {
			static $inst = null;
			if ($inst === null) {
				$inst = new PostmanAuthenticationManagerFactory ();
			}
			return $inst;
		}
		private function __construct() {
			$this->logger = new PostmanLogger ( get_class ( $this ) );
		}
		public function createAuthenticationManager() {
			$transport = PostmanTransportRegistry::getInstance ()->getSelectedTransport ();
			return $this->createManager ( $transport );
		}
		private function createManager(PostmanZendModuleTransport $transport) {
			$options = PostmanOptions::getInstance ();
			$authorizationToken = PostmanOAuthToken::getInstance ();
			$authenticationType = $options->getAuthenticationType ();
			$hostname = $options->getHostname ();
			$clientId = $options->getClientId ();
			$clientSecret = $options->getClientSecret ();
			$senderEmail = $options->getMessageSenderEmail ();
			$scribe = $transport->getScribe ();
			$redirectUrl = $scribe->getCallbackUrl ();
			if ($transport->isOAuthUsed ( $options->getAuthenticationType () )) {
				if ($transport->isServiceProviderGoogle ( $hostname )) {
					$authenticationManager = new PostmanGoogleAuthenticationManager ( $clientId, $clientSecret, $authorizationToken, $redirectUrl, $senderEmail );
				} else if ($transport->isServiceProviderMicrosoft ( $hostname )) {
					$authenticationManager = new PostmanMicrosoftAuthenticationManager ( $clientId, $clientSecret, $authorizationToken, $redirectUrl );
				} else if ($transport->isServiceProviderYahoo ( $hostname )) {
					$authenticationManager = new PostmanYahooAuthenticationManager ( $clientId, $clientSecret, $authorizationToken, $redirectUrl );
				} else {
					assert ( false );
				}
			} else {
				$authenticationManager = new PostmanNonOAuthAuthenticationManager ();
			}
			$this->logger->debug ( 'Created ' . get_class ( $authenticationManager ) );
			return $authenticationManager;
		}
	}
}