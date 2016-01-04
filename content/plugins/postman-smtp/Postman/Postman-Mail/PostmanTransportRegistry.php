<?php
require_once 'PostmanModuleTransport.php';
require_once 'PostmanZendMailTransportConfigurationFactory.php';

/**
 *
 * @author jasonhendriks
 *        
 */
class PostmanTransportRegistry {
	private $transports;
	private $logger;
	
	/**
	 */
	private function __construct() {
		$this->logger = new PostmanLogger ( get_class ( $this ) );
	}
	
	// singleton instance
	public static function getInstance() {
		static $inst = null;
		if ($inst === null) {
			$inst = new PostmanTransportRegistry ();
		}
		return $inst;
	}
	public function registerTransport(PostmanModuleTransport $instance) {
		$this->transports [$instance->getSlug ()] = $instance;
		$instance->init();
	}
	public function getTransports() {
		return $this->transports;
	}
	
	/**
	 * Retrieve a Transport by slug
	 * Look up a specific Transport use:
	 * A) when retrieving the transport saved in the database
	 * B) when querying what a theoretical scenario involving this transport is like
	 * (ie.for ajax in config screen)
	 *
	 * @param unknown $slug        	
	 */
	public function getTransport($slug) {
		$transports = $this->getTransports ();
		if (isset ( $transports [$slug] )) {
			return $transports [$slug];
		}
	}
	
	/**
	 * A short-hand way of showing the complete delivery method
	 *
	 * @param PostmanModuleTransport $transport        	
	 * @return string
	 */
	public function getPublicTransportUri(PostmanModuleTransport $transport) {
		return $transport->getPublicTransportUri ();
	}
	
	/**
	 * Determine if a specific transport is registered in the directory.
	 *
	 * @param unknown $slug        	
	 */
	public function isRegistered($slug) {
		$transports = $this->getTransports ();
		return isset ( $transports [$slug] );
	}
	
	/**
	 * Retrieve the transport Postman is currently configured with.
	 *
	 * @return PostmanDummyTransport|PostmanModuleTransport
	 * @deprecated
	 *
	 */
	public function getCurrentTransport() {
		$selectedTransport = PostmanOptions::getInstance ()->getTransportType ();
		$transports = $this->getTransports ();
		if (! isset ( $transports [$selectedTransport] )) {
			return $transports ['default'];
		} else {
			return $transports [$selectedTransport];
		}
	}
	
	/**
	 *
	 * @param PostmanOptions $options        	
	 * @param PostmanOAuthToken $token        	
	 * @return boolean
	 */
	public function getActiveTransport() {
		$selectedTransport = PostmanOptions::getInstance ()->getTransportType ();
		$transports = $this->getTransports ();
		if (isset ( $transports [$selectedTransport] )) {
			$transport = $transports [$selectedTransport];
			if ($transport->getSlug () == $selectedTransport && $transport->isConfiguredAndReady ()) {
				return $transport;
			}
		}
		return $transports ['default'];
	}
	
	/**
	 * Retrieve the transport Postman is currently configured with.
	 *
	 * @return PostmanDummyTransport|PostmanModuleTransport
	 */
	public function getSelectedTransport() {
		$selectedTransport = PostmanOptions::getInstance ()->getTransportType ();
		$transports = $this->getTransports ();
		if (isset ( $transports [$selectedTransport] )) {
			return $transports [$selectedTransport];
		} else {
			return $transports ['default'];
		}
	}
	
	/**
	 * Determine whether to show the Request Permission link on the main menu
	 *
	 * This link is displayed if
	 * 1. the current transport requires OAuth 2.0
	 * 2. the transport is properly configured
	 * 3. we have a valid Client ID and Client Secret without an Auth Token
	 *
	 * @param PostmanOptions $options        	
	 * @return boolean
	 */
	public function isRequestOAuthPermissionAllowed(PostmanOptions $options, PostmanOAuthToken $authToken) {
		// does the current transport use OAuth 2.0
		$oauthUsed = self::getSelectedTransport ()->isOAuthUsed ( $options->getAuthenticationType () );
		
		// is the transport configured
		if ($oauthUsed) {
			$configured = self::getSelectedTransport ()->isConfiguredAndReady ();
		}
		
		return $oauthUsed && $configured;
	}
	
	/**
	 * Polls all the installed transports to get a complete list of sockets to probe for connectivity
	 *
	 * @param unknown $hostname        	
	 * @param unknown $isGmail        	
	 * @return multitype:
	 */
	public function getSocketsForSetupWizardToProbe($hostname = 'localhost', $smtpServerGuess = null) {
		$hosts = array ();
		if ($this->logger->isDebug ()) {
			$this->logger->debug ( sprintf ( 'Getting sockets for Port Test given hostname %s and smtpServerGuess %s', $hostname, $smtpServerGuess ) );
		}
		foreach ( $this->getTransports () as $transport ) {
			$socketsToTest = $transport->getSocketsForSetupWizardToProbe ( $hostname, $smtpServerGuess );
			if ($this->logger->isTrace ()) {
				$this->logger->trace ( 'sockets to test:' );
				$this->logger->trace ( $socketsToTest );
			}
			$hosts = array_merge ( $hosts, $socketsToTest );
			if ($this->logger->isDebug ()) {
				$this->logger->debug ( sprintf ( 'Transport %s returns %d sockets ', $transport->getName (), sizeof ( $socketsToTest ) ) );
			}
		}
		return $hosts;
	}
	
	/**
	 * If the host port is a possible configuration option, recommend it
	 *
	 * $hostData includes ['host'] and ['port']
	 *
	 * response should include ['success'], ['message'], ['priority']
	 *
	 * @param unknown $hostData        	
	 */
	public function getRecommendation(PostmanWizardSocket $hostData, $userAuthOverride, $originalSmtpServer) {
		$scrubbedUserAuthOverride = $this->scrubUserOverride ( $hostData, $userAuthOverride );
		$transport = $this->getTransport ( $hostData->transport );
		$recommendation = $transport->getConfigurationBid ( $hostData, $scrubbedUserAuthOverride, $originalSmtpServer );
		if ($this->logger->isDebug ()) {
			$this->logger->debug ( sprintf ( 'Transport %s bid %s', $transport->getName (), $recommendation ['priority'] ) );
		}
		return $recommendation;
	}
	
	/**
	 *
	 * @param PostmanWizardSocket $hostData        	
	 * @param unknown $userAuthOverride        	
	 * @return NULL
	 */
	private function scrubUserOverride(PostmanWizardSocket $hostData, $userAuthOverride) {
		$this->logger->trace ( 'before scrubbing userAuthOverride: ' . $userAuthOverride );
		
		// validate userAuthOverride
		if (! ($userAuthOverride == 'oauth2' || $userAuthOverride == 'password' || $userAuthOverride == 'none')) {
			$userAuthOverride = null;
		}
		
		// validate the userAuthOverride
		if (! $hostData->auth_xoauth) {
			if ($userAuthOverride == 'oauth2') {
				$userAuthOverride = null;
			}
		}
		if (! $hostData->auth_crammd5 && ! $hostData->authPlain && ! $hostData->auth_login) {
			if ($userAuthOverride == 'password') {
				$userAuthOverride = null;
			}
		}
		if (! $hostData->auth_none) {
			if ($userAuthOverride == 'none') {
				$userAuthOverride = null;
			}
		}
		$this->logger->trace ( 'after scrubbing userAuthOverride: ' . $userAuthOverride );
		return $userAuthOverride;
	}
	
	/**
	 */
	public function getReadyMessage() {
		if ($this->getCurrentTransport ()->isConfiguredAndReady ()) {
			if (PostmanOptions::getInstance ()->getRunMode () != PostmanOptions::RUN_MODE_PRODUCTION) {
				return __ ( 'Postman is in <em>non-Production</em> mode and is dumping all emails.', Postman::TEXT_DOMAIN );
			} else {
				return __ ( 'Postman is configured.', Postman::TEXT_DOMAIN );
			}
		} else {
			return __ ( 'Postman is <em>not</em> configured and is mimicking out-of-the-box WordPress email delivery.', Postman::TEXT_DOMAIN );
		}
	}
}
