<?php
require_once 'PostmanModuleTransport.php';

/**
 * This class integrates Postman with the Gmail API
 * http://ctrlq.org/code/19860-gmail-api-send-emails
 *
 * @author jasonhendriks
 *        
 */
class PostmanGmailApiModuleTransport extends PostmanAbstractZendModuleTransport implements PostmanZendModuleTransport {
	const SLUG = 'gmail_api';
	const PORT = 443;
	const HOST = 'www.googleapis.com';
	const ENCRYPTION_TYPE = 'ssl';
	public function __construct($rootPluginFilenameAndPath) {
		parent::__construct ( $rootPluginFilenameAndPath );
		
		// add a hook on the plugins_loaded event
		add_action ( 'admin_init', array (
				$this,
				'on_admin_init' 
		) );
	}
	public function getProtocol() {
		return 'https';
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanAbstractModuleTransport::isServiceProviderGoogle()
	 */
	public function isServiceProviderGoogle($hostname) {
		return true;
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
		if (PostmanOptions::AUTHENTICATION_TYPE_OAUTH2 == $this->getAuthenticationType ()) {
			$config = PostmanOAuth2ConfigurationFactory::createConfig ( $this );
		} else {
			$config = PostmanBasicAuthConfigurationFactory::createConfig ( $this );
		}
		
		// Google's autoloader will try and load this so we list it first
		require_once 'PostmanGmailApiModuleZendMailTransport.php';
		
		// Gmail Client includes
		require_once 'google-api-php-client-1.1.2/src/Google/Client.php';
		require_once 'google-api-php-client-1.1.2/src/Google/Service/Gmail.php';
		
		// build the Gmail Client
		$authToken = PostmanOAuthToken::getInstance ();
		$client = new Postman_Google_Client ();
		$client->setClientId ( $this->options->getClientId () );
		$client->setClientSecret ( $this->options->getClientSecret () );
		$client->setRedirectUri ( '' );
		// rebuild the google access token
		$token = new stdClass ();
		$token->access_token = $authToken->getAccessToken ();
		$token->refresh_token = $authToken->getRefreshToken ();
		$token->token_type = 'Bearer';
		$token->expires_in = 3600;
		$token->id_token = null;
		$token->created = 0;
		$client->setAccessToken ( json_encode ( $token ) );
		// We only need permissions to compose and send emails
		$client->addScope ( "https://www.googleapis.com/auth/gmail.compose" );
		$service = new Postman_Google_Service_Gmail ( $client );
		$config [PostmanGmailApiModuleZendMailTransport::SERVICE_OPTION] = $service;
		
		return new PostmanGmailApiModuleZendMailTransport ( self::HOST, $config );
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
		return __ ( 'Gmail API', Postman::TEXT_DOMAIN );
	}
	public function isEnvelopeFromValidationSupported() {
		return false;
	}
	public function getHostname() {
		return self::HOST;
	}
	public function getPort() {
		return self::PORT;
	}
	public function getAuthenticationType() {
		return $this->options->getAuthenticationType ();
	}
	public function getSecurityType() {
		return null;
	}
	public function getCredentialsId() {
		$this->options = $this->options;
		if ($this->options->isAuthTypeOAuth2 ()) {
			return $this->options->getClientId ();
		} else {
			return $this->options->getUsername ();
		}
	}
	public function getCredentialsSecret() {
		$this->options = $this->options;
		if ($this->options->isAuthTypeOAuth2 ()) {
			return $this->options->getClientSecret ();
		} else {
			return $this->options->getPassword ();
		}
	}
	public function isServiceProviderMicrosoft($hostname) {
		return false;
	}
	public function isServiceProviderYahoo($hostname) {
		return false;
	}
	public function isOAuthUsed($authType) {
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanAbstractModuleTransport::getDeliveryDetails()
	 */
	public function getDeliveryDetails() {
		/* translators: where (1) is the secure icon and (2) is the transport name */
		return sprintf ( __ ( 'Postman will send mail via the <b>%1$s %2$s</b>.', Postman::TEXT_DOMAIN ), '🔐', $this->getName () );
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanAbstractZendModuleTransport::validateTransportConfiguration()
	 */
	protected function validateTransportConfiguration() {
		$messages = parent::validateTransportConfiguration ();
		if (empty ( $messages )) {
			$this->setReadyForOAuthGrant ();
			if ($this->isPermissionNeeded ()) {
				/* translators: %1$s is the Client ID label, and %2$s is the Client Secret label */
				$message = sprintf ( __ ( 'You have configured OAuth 2.0 authentication, but have not received permission to use it.', Postman::TEXT_DOMAIN ), $this->getScribe ()->getClientIdLabel (), $this->getScribe ()->getClientSecretLabel () );
				$message .= sprintf ( ' <a href="%s">%s</a>.', PostmanUtils::getGrantOAuthPermissionUrl (), $this->getScribe ()->getRequestPermissionLinkText () );
				array_push ( $messages, $message );
				$this->setNotConfiguredAndReady ();
			}
		}
		return $messages;
	}
	
	/**
	 * Given a hostname, what ports should we test?
	 *
	 * May return an array of several combinations.
	 */
	public function getSocketsForSetupWizardToProbe($hostname, $smtpServerGuess) {
		$hosts = array ();
		if ($smtpServerGuess == null || PostmanUtils::isGoogle ( $smtpServerGuess )) {
			array_push ( $hosts, parent::createSocketDefinition ( $this->getHostname (), $this->getPort () ) );
		}
		return $hosts;
	}
	
	/**
	 * Postman Gmail API supports delivering mail with these parameters:
	 *
	 * 70 gmail api on port 465 to www.googleapis.com
	 *
	 * @param unknown $hostData        	
	 */
	public function getConfigurationBid(PostmanWizardSocket $hostData, $userAuthOverride, $originalSmtpServer) {
		$recommendation = array ();
		$recommendation ['priority'] = 0;
		$recommendation ['transport'] = self::SLUG;
		$recommendation ['enc'] = PostmanOptions::SECURITY_TYPE_NONE;
		$recommendation ['auth'] = PostmanOptions::AUTHENTICATION_TYPE_OAUTH2;
		$recommendation ['hostname'] = null; // scribe looks this
		$recommendation ['label'] = $this->getName ();
		$recommendation ['display_auth'] = 'oauth2';
		if ($hostData->hostname == self::HOST && $hostData->port == self::PORT) {
			/* translators: where variables are (1) transport name (2) host and (3) port */
			$recommendation ['message'] = sprintf ( __ ( ('Postman recommends the %1$s to host %2$s on port %3$d.') ), $this->getName (), self::HOST, self::PORT );
			$recommendation ['priority'] = 27000;
		}
		
		return $recommendation;
	}
	
	/**
	 */
	public function createOverrideMenu(PostmanWizardSocket $socket, $winningRecommendation, $userSocketOverride, $userAuthOverride) {
		$overrideItem = parent::createOverrideMenu ( $socket, $winningRecommendation, $userSocketOverride, $userAuthOverride );
		// push the authentication options into the $overrideItem structure
		$overrideItem ['auth_items'] = array (
				array (
						'selected' => true,
						'name' => __ ( 'OAuth 2.0 (requires Client ID and Client Secret)', Postman::TEXT_DOMAIN ),
						'value' => 'oauth2' 
				) 
		);
		return $overrideItem;
	}
	
	/**
	 * Functions to execute on the admin_init event
	 *
	 * "Runs at the beginning of every admin page before the page is rendered."
	 * ref: http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_an_Admin_Page_Request
	 */
	public function on_admin_init() {
		// only administrators should be able to trigger this
		if (PostmanUtils::isAdmin ()) {
			$this->registerStylesAndScripts ();
		}
	}
	
	/**
	 */
	public function registerStylesAndScripts() {
		// register the stylesheet and javascript external resources
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		wp_register_script ( 'postman_gmail_script', plugins_url ( 'Postman/Postman-Mail/postman_gmail.js', $this->rootPluginFilenameAndPath ), array (
				PostmanViewController::JQUERY_SCRIPT,
				'jquery_validation',
				PostmanViewController::POSTMAN_SCRIPT 
		), $pluginData ['version'] );
	}
	
	/**
	 */
	public function enqueueScript() {
		wp_enqueue_script ( 'postman_gmail_script' );
	}
}
