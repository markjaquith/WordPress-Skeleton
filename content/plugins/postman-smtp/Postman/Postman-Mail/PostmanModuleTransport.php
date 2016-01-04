<?php
/**
 * Keep the interface_exists check here for Postman Gmail API Extension users!
 * 
 * @author jasonhendriks
 */
if (! interface_exists ( 'PostmanTransport' )) {
	interface PostmanTransport {
		public function isServiceProviderGoogle($hostname);
		public function isServiceProviderMicrosoft($hostname);
		public function isServiceProviderYahoo($hostname);
		// @deprecated
		public function isOAuthUsed($authType);
		public function isTranscriptSupported();
		public function getSlug();
		public function getName();
		// @deprecated
		public function createPostmanMailAuthenticator(PostmanOptions $options, PostmanOAuthToken $authToken);
		public function createZendMailTransport($fakeHostname, $fakeConfig);
		public function isConfigured(PostmanOptionsInterface $options, PostmanOAuthToken $token);
		public function isReady(PostmanOptionsInterface $options, PostmanOAuthToken $token);
		// @deprecated
		public function getMisconfigurationMessage(PostmanConfigTextHelper $scribe, PostmanOptionsInterface $options, PostmanOAuthToken $token);
		// @deprecated
		public function getConfigurationRecommendation($hostData);
		// @deprecated
		public function getHostsToTest($hostname);
	}
}
interface PostmanModuleTransport extends PostmanTransport {
	const RAW_MESSAGE_FOLLOWS = '

--Raw message follows--

';
	public function getDeliveryDetails();
	public function getSocketsForSetupWizardToProbe($hostname, $smtpServerGuess);
	public function getConfigurationBid(PostmanWizardSocket $hostData, $userAuthOverride, $originalSmtpServer);
	public function isLockingRequired();
	public function createMailEngine();
	public function isWizardSupported();
	public function isConfiguredAndReady();
	public function isReadyToSendMail();
	public function getFromEmailAddress();
	public function getFromName();
	public function getProtocol();
	public function isEmailValidationSupported();
	public function getPort();
	public function init();
}
interface PostmanZendModuleTransport extends PostmanModuleTransport {
	public function getAuthenticationType();
	public function getSecurityType();
	public function getCredentialsId();
	public function getCredentialsSecret();
	public function getEnvelopeFromEmailAddress();
}

/**
 *
 * @author jasonhendriks
 *        
 */
abstract class PostmanAbstractModuleTransport implements PostmanModuleTransport {
	private $configurationMessages;
	private $configuredAndReady;
	
	/**
	 * These internal variables are exposed for the subclasses to use
	 *
	 * @var unknown
	 */
	protected $logger;
	protected $options;
	protected $rootPluginFilenameAndPath;
	protected $scribe;
	
	/**
	 */
	public function __construct($rootPluginFilenameAndPath) {
		$this->logger = new PostmanLogger ( get_class ( $this ) );
		$this->options = PostmanOptions::getInstance ();
		$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
	}
	
	/**
	 * Initialize the Module
	 *
	 * Perform validation and create configuration error messages.
	 * The module is not in a configured-and-ready state until initialization
	 */
	public function init() {
		// create the scribe
		$hostname = $this->getHostname ();
		$this->scribe = $this->createScribe ( $hostname );
		
		// validate the transport and generate error messages
		$this->configurationMessages = $this->validateTransportConfiguration ();
	}
	
	/**
	 * SendGrid API doesn't care what the hostname or guessed SMTP Server is; it runs it's port test no matter what
	 */
	public function getSocketsForSetupWizardToProbe($hostname, $smtpServerGuess) {
		$hosts = array (
				self::createSocketDefinition ( $this->getHostname (), $this->getPort () ) 
		);
		return $hosts;
	}
	
	/**
	 * Creates a single socket for the Wizard to test
	 */
	protected function createSocketDefinition($hostname, $port) {
		$socket = array ();
		$socket ['host'] = $hostname;
		$socket ['port'] = $port;
		$socket ['id'] = sprintf ( '%s-%s', $this->getSlug (), $port );
		$socket ['transport_id'] = $this->getSlug ();
		$socket ['transport_name'] = $this->getName ();
		$socket ['smtp'] = false;
		return $socket;
	}
	
	/**
	 *
	 * @param unknown $data        	
	 */
	public function prepareOptionsForExport($data) {
		// no-op
		return $data;
	}
	
	/**
	 */
	public function printActionMenuItem() {
		printf ( '<li><div class="welcome-icon send_test_email">%s</div></li>', $this->getScribe ()->getRequestPermissionLinkText () );
	}
	
	/**
	 *
	 * @param unknown $queryHostname        	
	 */
	protected function createScribe($hostname) {
		$scribe = new PostmanNonOAuthScribe ( $hostname );
		return $scribe;
	}
	
	/**
	 */
	public function enqueueScript() {
		// no-op, this for subclasses
	}
	
	/**
	 * This method is for internal use
	 */
	protected function validateTransportConfiguration() {
		$this->configuredAndReady = true;
		$messages = array ();
		return $messages;
	}
	
	/**
	 */
	protected function setNotConfiguredAndReady() {
		$this->configuredAndReady = false;
	}
	
	/**
	 * A short-hand way of showing the complete delivery method
	 *
	 * @param PostmanModuleTransport $transport        	
	 * @return string
	 */
	public function getPublicTransportUri() {
		$name = $this->getSlug ();
		$host = $this->getHostname ();
		$port = $this->getPort ();
		$protocol = $this->getProtocol ();
		return sprintf ( '%s://%s:%s', $protocol, $host, $port );
	}
	
	/**
	 * The Message From Address
	 */
	public function getFromEmailAddress() {
		return PostmanOptions::getInstance ()->getMessageSenderEmail ();
	}
	
	/**
	 * The Message From Name
	 */
	public function getFromName() {
		return PostmanOptions::getInstance ()->getMessageSenderName ();
	}
	public function getEnvelopeFromEmailAddress() {
		return PostmanOptions::getInstance ()->getEnvelopeSender ();
	}
	public function isEmailValidationSupported() {
		return ! PostmanOptions::getInstance ()->isEmailValidationDisabled ();
	}
	
	/**
	 * Make sure the Senders are configured
	 *
	 * @param PostmanOptions $options        	
	 * @return boolean
	 */
	protected function isSenderConfigured() {
		$options = PostmanOptions::getInstance ();
		$messageFrom = $options->getMessageSenderEmail ();
		return ! empty ( $messageFrom );
	}
	
	/**
	 * Get the configuration error messages
	 */
	public function getConfigurationMessages() {
		return $this->configurationMessages;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanModuleTransport::isConfiguredAndReady()
	 */
	public function isConfiguredAndReady() {
		return $this->configuredAndReady;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanModuleTransport::isReadyToSendMail()
	 */
	public function isReadyToSendMail() {
		return $this->isConfiguredAndReady ();
	}
	
	/**
	 * Determines whether Mail Engine locking is needed
	 *
	 * @see PostmanModuleTransport::requiresLocking()
	 */
	public function isLockingRequired() {
		return false;
	}
	public function isOAuthUsed($authType) {
		return $authType == PostmanOptions::AUTHENTICATION_TYPE_OAUTH2;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanModuleTransport::isWizardSupported()
	 */
	public function isWizardSupported() {
		return false;
	}
	
	/**
	 * This is for step 2 of the Wizard
	 */
	public function printWizardMailServerHostnameStep() {
	}
	
	/**
	 * This is for step 4 of the Wizard
	 */
	public function printWizardAuthenticationStep() {
	}
	
	/**
	 *
	 * @return PostmanNonOAuthScribe
	 */
	public function getScribe() {
		return $this->scribe;
	}
	
	/**
	 *
	 * @param unknown $hostname        	
	 * @param unknown $response        	
	 */
	public function populateConfiguration($hostname) {
		$configuration = array ();
		return $configuration;
	}
	/**
	 *
	 * @param unknown $winningRecommendation        	
	 * @param unknown $response        	
	 */
	public function populateConfigurationFromRecommendation($winningRecommendation) {
		$configuration = array ();
		$configuration ['message'] = $winningRecommendation ['message'];
		$configuration [PostmanOptions::TRANSPORT_TYPE] = $winningRecommendation ['transport'];
		return $configuration;
	}
	
	/**
	 */
	public function createOverrideMenu(PostmanWizardSocket $socket, $winningRecommendation, $userSocketOverride, $userAuthOverride) {
		$overrideItem = array ();
		$overrideItem ['secure'] = $socket->secure;
		$overrideItem ['mitm'] = $socket->mitm;
		$overrideItem ['hostname_domain_only'] = $socket->hostnameDomainOnly;
		$overrideItem ['reported_hostname_domain_only'] = $socket->reportedHostnameDomainOnly;
		$overrideItem ['value'] = $socket->id;
		$overrideItem ['description'] = $socket->label;
		$overrideItem ['selected'] = ($winningRecommendation ['id'] == $overrideItem ['value']);
		return $overrideItem;
	}
	
	/*
	 * ******************************************************************
	 * Not deprecated, but I wish they didn't live here on the superclass
	 */
	public function isServiceProviderGoogle($hostname) {
		return PostmanUtils::endsWith ( $hostname, 'gmail.com' ) || PostmanUtils::endsWith ( $hostname, 'googleapis.com' );
	}
	public function isServiceProviderMicrosoft($hostname) {
		return PostmanUtils::endsWith ( $hostname, 'live.com' );
	}
	public function isServiceProviderYahoo($hostname) {
		return strpos ( $hostname, 'yahoo' );
	}
	
	/*
	 * ********************************
	 * Unused, deprecated methods follow
	 * *********************************
	 */
	
	/**
	 *
	 * @deprecated (non-PHPdoc)
	 * @see PostmanTransport::createZendMailTransport()
	 */
	public function createZendMailTransport($hostname, $config) {
	}
	
	/**
	 *
	 * @deprecated (non-PHPdoc)
	 * @see PostmanTransport::isTranscriptSupported()
	 */
	public function isTranscriptSupported() {
		return false;
	}
	
	/**
	 * Only here because I can't remove it from the Interface
	 */
	public final function getMisconfigurationMessage(PostmanConfigTextHelper $scribe, PostmanOptionsInterface $options, PostmanOAuthToken $token) {
	}
	public final function isReady(PostmanOptionsInterface $options, PostmanOAuthToken $token) {
		return ! ($this->isConfiguredAndReady ());
	}
	public final function isConfigured(PostmanOptionsInterface $options, PostmanOAuthToken $token) {
		return ! ($this->isConfiguredAndReady ());
	}
	/**
	 *
	 * @deprecated (non-PHPdoc)
	 * @see PostmanTransport::getConfigurationRecommendation()
	 */
	public final function getConfigurationRecommendation($hostData) {
	}
	/**
	 *
	 * @deprecated (non-PHPdoc)
	 * @see PostmanTransport::getHostsToTest()
	 */
	public final function getHostsToTest($hostname) {
	}
	protected final function isHostConfigured(PostmanOptions $options) {
		$hostname = $options->getHostname ();
		$port = $options->getPort ();
		return ! (empty ( $hostname ) || empty ( $port ));
	}
	/**
	 *
	 * @deprecated (non-PHPdoc)
	 * @see PostmanTransport::createPostmanMailAuthenticator()
	 */
	public final function createPostmanMailAuthenticator(PostmanOptions $options, PostmanOAuthToken $authToken) {
	}
}

/**
 * For the transports which depend on Zend_Mail
 *
 * @author jasonhendriks
 *        
 */
abstract class PostmanAbstractZendModuleTransport extends PostmanAbstractModuleTransport implements PostmanZendModuleTransport {
	private $oauthToken;
	private $readyForOAuthGrant;
	
	/**
	 */
	public function __construct($rootPluginFilenameAndPath) {
		parent::__construct ( $rootPluginFilenameAndPath );
		$this->oauthToken = PostmanOAuthToken::getInstance ();
	}
	public function getOAuthToken() {
		return $this->oauthToken;
	}
	public function getProtocol() {
		if ($this->getSecurityType () == PostmanOptions::SECURITY_TYPE_SMTPS)
			return 'smtps';
		else
			return 'smtp';
	}
	public function getSecurityType() {
		return PostmanOptions::getInstance ()->getEncryptionType ();
	}
	
	/**
	 *
	 * @param unknown $data        	
	 */
	public function prepareOptionsForExport($data) {
		$data = parent::prepareOptionsForExport ( $data );
		$data [PostmanOptions::BASIC_AUTH_PASSWORD] = PostmanOptions::getInstance ()->getPassword ();
		return $data;
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function isEnvelopeFromValidationSupported() {
		return $this->isEmailValidationSupported ();
	}
	
	/**
	 */
	protected function setReadyForOAuthGrant() {
		$this->readyForOAuthGrant = true;
	}
	
	/**
	 */
	public function printActionMenuItem() {
		if ($this->readyForOAuthGrant && $this->getAuthenticationType () == PostmanOptions::AUTHENTICATION_TYPE_OAUTH2) {
			printf ( '<li><a href="%s" class="welcome-icon send-test-email">%s</a></li>', PostmanUtils::getGrantOAuthPermissionUrl (), $this->getScribe ()->getRequestPermissionLinkText () );
		} else {
			parent::printActionMenuItem ();
		}
	}
	
	/**
	 *
	 * @param unknown $queryHostname        	
	 */
	protected function createScribe($hostname) {
		$scribe = null;
		if ($this->isServiceProviderGoogle ( $hostname )) {
			$scribe = new PostmanGoogleOAuthScribe ();
		} else if ($this->isServiceProviderMicrosoft ( $hostname )) {
			$scribe = new PostmanMicrosoftOAuthScribe ();
		} else if ($this->isServiceProviderYahoo ( $hostname )) {
			$scribe = new PostmanYahooOAuthScribe ();
		} else {
			$scribe = new PostmanNonOAuthScribe ( $hostname );
		}
		return $scribe;
	}
	
	/**
	 * A short-hand way of showing the complete delivery method
	 *
	 * @param PostmanModuleTransport $transport        	
	 * @return string
	 */
	public function getPublicTransportUri() {
		$transportName = $this->getSlug ();
		$options = PostmanOptions::getInstance ();
		$auth = $this->getAuthenticationType ( $options );
		$protocol = $this->getProtocol ();
		$security = $this->getSecurityType ();
		$host = $this->getHostname ( $options );
		$port = $this->getPort ( $options );
		if (! empty ( $security ) && $security != 'ssl') {
			return sprintf ( '%s:%s:%s://%s:%s', $protocol, $security, $auth, $host, $port );
		} else {
			return sprintf ( '%s:%s://%s:%s', $protocol, $auth, $host, $port );
		}
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanModuleTransport::getDeliveryDetails()
	 */
	public function getDeliveryDetails() {
		$this->options = $this->options;
		$deliveryDetails ['transport_name'] = $this->getTransportDescription ( $this->getSecurityType () );
		$deliveryDetails ['host'] = $this->getHostname () . ':' . $this->getPort ();
		$deliveryDetails ['auth_desc'] = $this->getAuthenticationDescription ( $this->getAuthenticationType () );
		/* translators: where (1) is the transport type, (2) is the host, and (3) is the Authentication Type (e.g. Postman will send mail via smtp.gmail.com:465 using OAuth 2.0 authentication.) */
		return sprintf ( __ ( 'Postman will send mail via %1$s to %2$s using %3$s authentication.', Postman::TEXT_DOMAIN ), '<b>' . $deliveryDetails ['transport_name'] . '</b>', '<b>' . $deliveryDetails ['host'] . '</b>', '<b>' . $deliveryDetails ['auth_desc'] . '</b>' );
	}
	
	/**
	 *
	 * @param unknown $encType        	
	 * @return string
	 */
	protected function getTransportDescription($encType) {
		$deliveryDetails = '🔓SMTP';
		if ($encType == PostmanOptions::SECURITY_TYPE_SMTPS) {
			/* translators: where %1$s is the Transport type (e.g. SMTP or SMTPS) and %2$s is the encryption type (e.g. SSL or TLS) */
			$deliveryDetails = '🔐SMTPS';
		} else if ($encType == PostmanOptions::SECURITY_TYPE_STARTTLS) {
			/* translators: where %1$s is the Transport type (e.g. SMTP or SMTPS) and %2$s is the encryption type (e.g. SSL or TLS) */
			$deliveryDetails = '🔐SMTP-STARTTLS';
		}
		return $deliveryDetails;
	}
	
	/**
	 *
	 * @param unknown $authType        	
	 */
	protected function getAuthenticationDescription($authType) {
		if (PostmanOptions::AUTHENTICATION_TYPE_OAUTH2 == $authType) {
			return 'OAuth 2.0';
		} else if (PostmanOptions::AUTHENTICATION_TYPE_NONE == $authType) {
			return _x ( 'no', 'as in "There is no Spoon"', Postman::TEXT_DOMAIN );
		} else {
			switch ($authType) {
				case PostmanOptions::AUTHENTICATION_TYPE_CRAMMD5 :
					$authDescription = 'CRAM-MD5';
					break;
				
				case PostmanOptions::AUTHENTICATION_TYPE_LOGIN :
					$authDescription = 'Login';
					break;
				
				case PostmanOptions::AUTHENTICATION_TYPE_PLAIN :
					$authDescription = 'Plain';
					break;
				
				default :
					$authDescription = $authType;
					break;
			}
			return sprintf ( '%s (%s)', __ ( 'Password', Postman::TEXT_DOMAIN ), $authDescription );
		}
	}
	
	/**
	 * Make sure the Senders are configured
	 *
	 * @param PostmanOptions $options        	
	 * @return boolean
	 */
	protected function isEnvelopeFromConfigured() {
		$options = PostmanOptions::getInstance ();
		$envelopeFrom = $options->getEnvelopeSender ();
		return ! empty ( $envelopeFrom );
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanTransport::getMisconfigurationMessage()
	 */
	protected function validateTransportConfiguration() {
		parent::validateTransportConfiguration ();
		$messages = parent::validateTransportConfiguration ();
		if (! $this->isSenderConfigured ()) {
			array_push ( $messages, __ ( 'Message From Address can not be empty', Postman::TEXT_DOMAIN ) . '.' );
			$this->setNotConfiguredAndReady ();
		}
		if ($this->getAuthenticationType () == PostmanOptions::AUTHENTICATION_TYPE_OAUTH2) {
			if (! $this->isOAuth2ClientIdAndClientSecretConfigured ()) {
				/* translators: %1$s is the Client ID label, and %2$s is the Client Secret label (e.g. Warning: OAuth 2.0 authentication requires an OAuth 2.0-capable Outgoing Mail Server, Sender Email Address, Client ID, and Client Secret.) */
				array_push ( $messages, sprintf ( __ ( 'OAuth 2.0 authentication requires a %1$s and %2$s.', Postman::TEXT_DOMAIN ), $this->getScribe ()->getClientIdLabel (), $this->getScribe ()->getClientSecretLabel () ) );
				$this->setNotConfiguredAndReady ();
			}
		}
		return $messages;
	}
	
	/**
	 *
	 * @return boolean
	 */
	protected function isOAuth2ClientIdAndClientSecretConfigured() {
		$options = PostmanOptions::getInstance ();
		$clientId = $options->getClientId ();
		$clientSecret = $options->getClientSecret ();
		return ! (empty ( $clientId ) || empty ( $clientSecret ));
	}
	
	/**
	 *
	 * @return boolean
	 */
	protected function isPasswordAuthenticationConfigured(PostmanOptions $options) {
		$username = $options->getUsername ();
		$password = $options->getPassword ();
		return $this->options->isAuthTypePassword () && ! (empty ( $username ) || empty ( $password ));
	}
	
	/**
	 *
	 * @return boolean
	 */
	protected function isPermissionNeeded() {
		$accessToken = $this->getOAuthToken ()->getAccessToken ();
		$refreshToken = $this->getOAuthToken ()->getRefreshToken ();
		return $this->isOAuthUsed ( PostmanOptions::getInstance ()->getAuthenticationType () ) && (empty ( $accessToken ) || empty ( $refreshToken ));
	}
	
	/**
	 *
	 * @param unknown $hostname        	
	 * @param unknown $response        	
	 */
	public function populateConfiguration($hostname) {
		$response = parent::populateConfiguration ( $hostname );
		$this->logger->debug ( sprintf ( 'populateConfigurationFromRecommendation for hostname %s', $hostname ) );
		$scribe = $this->createScribe ( $hostname );
		// checks to see if the host is an IP address and sticks the result in the response
		// IP addresses are not allowed in the Redirect URL
		$urlParts = parse_url ( $scribe->getCallbackUrl () );
		$response ['dot_notation_url'] = false;
		if (isset ( $urlParts ['host'] )) {
			if (PostmanUtils::isHostAddressNotADomainName ( $urlParts ['host'] )) {
				$response ['dot_notation_url'] = true;
			}
		}
		$response ['redirect_url'] = $scribe->getCallbackUrl ();
		$response ['callback_domain'] = $scribe->getCallbackDomain ();
		$response ['help_text'] = $scribe->getOAuthHelp ();
		$response ['client_id_label'] = $scribe->getClientIdLabel ();
		$response ['client_secret_label'] = $scribe->getClientSecretLabel ();
		$response ['redirect_url_label'] = $scribe->getCallbackUrlLabel ();
		$response ['callback_domain_label'] = $scribe->getCallbackDomainLabel ();
		return $response;
	}
	
	/**
	 * Populate the Ajax response for the Setup Wizard / Manual Configuration
	 *
	 * @param unknown $hostname        	
	 * @param unknown $response        	
	 */
	public function populateConfigurationFromRecommendation($winningRecommendation) {
		$response = parent::populateConfigurationFromRecommendation ( $winningRecommendation );
		$response [PostmanOptions::AUTHENTICATION_TYPE] = $winningRecommendation ['auth'];
		if (isset ( $winningRecommendation ['enc'] )) {
			$response [PostmanOptions::SECURITY_TYPE] = $winningRecommendation ['enc'];
		}
		if (isset ( $winningRecommendation ['port'] )) {
			$response [PostmanOptions::PORT] = $winningRecommendation ['port'];
		}
		if (isset ( $winningRecommendation ['hostname'] )) {
			$response [PostmanOptions::HOSTNAME] = $winningRecommendation ['hostname'];
		}
		if (isset ( $winningRecommendation ['display_auth'] )) {
			$response ['display_auth'] = $winningRecommendation ['display_auth'];
		}
		return $response;
	}
	
	/**
	 */
	public function createOverrideMenu(PostmanWizardSocket $socket, $winningRecommendation, $userSocketOverride, $userAuthOverride) {
		$overrideItem = parent::createOverrideMenu ( $socket, $winningRecommendation, $userSocketOverride, $userAuthOverride );
		$selected = $overrideItem ['selected'];
		
		// only smtp can have multiple auth options
		$overrideAuthItems = array ();
		$passwordMode = false;
		$oauth2Mode = false;
		$noAuthMode = false;
		if (isset ( $userAuthOverride ) || isset ( $userSocketOverride )) {
			if ($userAuthOverride == 'password') {
				$passwordMode = true;
			} elseif ($userAuthOverride == 'oauth2') {
				$oauth2Mode = true;
			} else {
				$noAuthMode = true;
			}
		} else {
			if ($winningRecommendation ['display_auth'] == 'password') {
				$passwordMode = true;
			} elseif ($winningRecommendation ['display_auth'] == 'oauth2') {
				$oauth2Mode = true;
			} else {
				$noAuthMode = true;
			}
		}
		if ($selected) {
			if ($socket->auth_crammd5 || $socket->auth_login || $socket->authPlain) {
				array_push ( $overrideAuthItems, array (
						'selected' => $passwordMode,
						'name' => __ ( 'Password (requires username and password)', Postman::TEXT_DOMAIN ),
						'value' => 'password' 
				) );
			}
			if ($socket->auth_xoauth || $winningRecommendation ['auth'] == 'oauth2') {
				array_push ( $overrideAuthItems, array (
						'selected' => $oauth2Mode,
						'name' => __ ( 'OAuth 2.0 (requires Client ID and Client Secret)', Postman::TEXT_DOMAIN ),
						'value' => 'oauth2' 
				) );
			}
			if ($socket->auth_none) {
				array_push ( $overrideAuthItems, array (
						'selected' => $noAuthMode,
						'name' => __ ( 'None', Postman::TEXT_DOMAIN ),
						'value' => 'none' 
				) );
			}
			
			// marks at least one item as selected if none are selected
			$atLeastOneSelected = false;
			$firstItem = null;
			// don't use variable reference see http://stackoverflow.com/questions/15024616/php-foreach-change-original-array-values
			foreach ( $overrideAuthItems as $key => $field ) {
				if (! $firstItem) {
					$firstItem = $key;
				}
				if ($field ['selected']) {
					$atLeastOneSelected = true;
				}
			}
			if (! $atLeastOneSelected) {
				$this->logger->debug ( 'nothing selected - forcing a selection on the *first* overrided auth item' );
				$overrideAuthItems [$firstItem] ['selected'] = true;
			}
			
			// push the authentication options into the $overrideItem structure
			$overrideItem ['auth_items'] = $overrideAuthItems;
		}
		return $overrideItem;
	}
}

