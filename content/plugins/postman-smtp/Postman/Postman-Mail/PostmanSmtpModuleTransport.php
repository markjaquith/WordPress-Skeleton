<?php
require_once 'PostmanModuleTransport.php';

/**
 *
 * @author jasonhendriks
 *        
 */
class PostmanSmtpModuleTransport extends PostmanAbstractZendModuleTransport implements PostmanZendModuleTransport {
	const SLUG = 'smtp';
	public function __construct($rootPluginFilenameAndPath) {
		parent::__construct ( $rootPluginFilenameAndPath );
		
		// add a hook on the plugins_loaded event
		add_action ( 'admin_init', array (
				$this,
				'on_admin_init' 
		) );
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
		return 'SMTP';
	}
	public function getHostname() {
		$this->options = $this->options;
		return $this->options->getHostname ();
	}
	public function getPort() {
		$this->options = $this->options;
		return $this->options->getPort ();
	}
	public function getAuthenticationType() {
		return $this->options->getAuthenticationType ();
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
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanTransport::getMisconfigurationMessage()
	 */
	protected function validateTransportConfiguration() {
		$messages = parent::validateTransportConfiguration ();
		if (! $this->isHostConfigured ( $this->options )) {
			array_push ( $messages, __ ( 'Outgoing Mail Server Hostname and Port can not be empty.', Postman::TEXT_DOMAIN ) );
			$this->setNotConfiguredAndReady ();
		}
		if (! $this->isEnvelopeFromConfigured ()) {
			array_push ( $messages, __ ( 'Envelope-From Email Address can not be empty', Postman::TEXT_DOMAIN ) . '.' );
			$this->setNotConfiguredAndReady ();
		}
		if ($this->options->isAuthTypePassword () && ! $this->isPasswordAuthenticationConfigured ( $this->options )) {
			array_push ( $messages, __ ( 'Username and password can not be empty.', Postman::TEXT_DOMAIN ) );
			$this->setNotConfiguredAndReady ();
		}
		if ($this->getAuthenticationType () == PostmanOptions::AUTHENTICATION_TYPE_OAUTH2) {
			if (! $this->isOAuth2SupportedHostConfigured ()) {
				/* translators: %1$s is the Client ID label, and %2$s is the Client Secret label (e.g. Warning: OAuth 2.0 authentication requires an OAuth 2.0-capable Outgoing Mail Server, Sender Email Address, Client ID, and Client Secret.) */
				array_push ( $messages, sprintf ( __ ( 'OAuth 2.0 authentication requires a supported OAuth 2.0-capable Outgoing Mail Server.', Postman::TEXT_DOMAIN ) ) );
				$this->setNotConfiguredAndReady ();
			}
		}
		if (empty ( $messages )) {
			$this->setReadyForOAuthGrant ();
			if ($this->isPermissionNeeded ( $this->options, $this->getOAuthToken () )) {
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
	 *
	 * @return boolean
	 */
	private function isOAuth2SupportedHostConfigured() {
		$options = PostmanOptions::getInstance ();
		$hostname = $options->getHostname ();
		$supportedOAuthProvider = $this->isServiceProviderGoogle ( $hostname ) || $this->isServiceProviderMicrosoft ( $hostname ) || $this->isServiceProviderYahoo ( $hostname );
		return $supportedOAuthProvider;
	}
	
	/**
	 * Given a hostname, what ports should we test?
	 *
	 * May return an array of several combinations.
	 */
	public function getSocketsForSetupWizardToProbe($hostname, $smtpServerGuess) {
		$hosts = array (
				$this->createSocketDefinition ( $hostname, 25 ),
				$this->createSocketDefinition ( $hostname, 465 ),
				$this->createSocketDefinition ( $hostname, 587 ) 
		);
		
		return $hosts;
	}
	
	/**
	 * Creates a single socket for the Wizard to test
	 */
	protected function createSocketDefinition($hostname, $port) {
		$socket = parent::createSocketDefinition ( $hostname, $port );
		$socket ['smtp'] = true;
		return $socket;
	}
	
	/**
	 * SendGrid will never recommend it's configuration
	 *
	 * @param unknown $hostData        	
	 */
	public function getConfigurationBid(PostmanWizardSocket $hostData, $userAuthOverride, $originalSmtpServer) {
		$port = $hostData->port;
		$hostname = $hostData->hostname;
		// because some servers, like smtp.broadband.rogers.com, report XOAUTH2 but have no OAuth2 front-end
		$supportedOAuth2Provider = $this->isServiceProviderGoogle ( $hostname ) || $this->isServiceProviderMicrosoft ( $hostname ) || $this->isServiceProviderYahoo ( $hostname );
		$score = 1;
		$recommendation = array ();
		// increment score for auth type
		if ($hostData->mitm) {
			$this->logger->debug ( 'Losing points for MITM' );
			$score -= 10000;
			$recommendation ['mitm'] = true;
		}
		if (! empty ( $originalSmtpServer ) && $hostname != $originalSmtpServer) {
			$this->logger->debug ( 'Losing points for Not The Original SMTP server' );
			$score -= 10000;
		}
		$secure = true;
		if ($hostData->startTls) {
			// STARTTLS was formalized in 2002
			// http://www.rfc-editor.org/rfc/rfc3207.txt
			$recommendation ['enc'] = PostmanOptions::SECURITY_TYPE_STARTTLS;
			$score += 30000;
		} elseif ($hostData->protocol == 'SMTPS') {
			// "The hopelessly confusing and imprecise term, SSL,
			// has often been used to indicate the SMTPS wrapper and
			// TLS to indicate the STARTTLS protocol extension."
			// http://stackoverflow.com/a/19942206/4368109
			$recommendation ['enc'] = PostmanOptions::SECURITY_TYPE_SMTPS;
			$score += 28000;
		} elseif ($hostData->protocol == 'SMTP') {
			$recommendation ['enc'] = PostmanOptions::SECURITY_TYPE_NONE;
			$score += 26000;
			$secure = false;
		}
		
		// if there is a way to send mail....
		if ($score > 10) {
			
			// determine the authentication type
			if ($hostData->auth_xoauth && $supportedOAuth2Provider && (empty ( $userAuthOverride ) || $userAuthOverride == 'oauth2')) {
				$recommendation ['auth'] = PostmanOptions::AUTHENTICATION_TYPE_OAUTH2;
				$recommendation ['display_auth'] = 'oauth2';
				$score += 500;
				if (! $secure) {
					$this->logger->debug ( 'Losing points for sending credentials in the clear' );
					$score -= 10000;
				}
			} elseif ($hostData->auth_crammd5 && (empty ( $userAuthOverride ) || $userAuthOverride == 'password')) {
				$recommendation ['auth'] = PostmanOptions::AUTHENTICATION_TYPE_CRAMMD5;
				$recommendation ['display_auth'] = 'password';
				$score += 400;
				if (! $secure) {
					$this->logger->debug ( 'Losing points for sending credentials in the clear' );
					$score -= 10000;
				}
			} elseif ($hostData->authPlain && (empty ( $userAuthOverride ) || $userAuthOverride == 'password')) {
				$recommendation ['auth'] = PostmanOptions::AUTHENTICATION_TYPE_PLAIN;
				$recommendation ['display_auth'] = 'password';
				$score += 300;
				if (! $secure) {
					$this->logger->debug ( 'Losing points for sending credentials in the clear' );
					$score -= 10000;
				}
			} elseif ($hostData->auth_login && (empty ( $userAuthOverride ) || $userAuthOverride == 'password')) {
				$recommendation ['auth'] = PostmanOptions::AUTHENTICATION_TYPE_LOGIN;
				$recommendation ['display_auth'] = 'password';
				$score += 200;
				if (! $secure) {
					$this->logger->debug ( 'Losing points for sending credentials in the clear' );
					$score -= 10000;
				}
			} else if (empty ( $userAuthOverride ) || $userAuthOverride == 'none') {
				$recommendation ['auth'] = PostmanOptions::AUTHENTICATION_TYPE_NONE;
				$recommendation ['display_auth'] = 'none';
				$score += 100;
			}
			
			// tiny weighting to prejudice the port selection, all things being equal
			if ($port == 587) {
				$score += 4;
			} elseif ($port == 25) {
				// "due to the prevalence of machines that have worms,
				// viruses, or other malicious software that generate large amounts of
				// spam, many sites now prohibit outbound traffic on the standard SMTP
				// port (port 25), funneling all mail submissions through submission
				// servers."
				// http://www.rfc-editor.org/rfc/rfc6409.txt
				$score += 3;
			} elseif ($port == 465) {
				// use of port 465 for SMTP was deprecated in 1998
				// http://www.imc.org/ietf-apps-tls/mail-archive/msg00204.html
				$score += 2;
			} else {
				$score += 1;
			}
			
			// create the recommendation message for the user
			// this can only be set if there is a valid ['auth'] and ['enc']
			$transportDescription = $this->getTransportDescription ( $recommendation ['enc'] );
			$authDesc = $this->getAuthenticationDescription ( $recommendation ['auth'] );
			$recommendation ['label'] = sprintf ( 'SMTP - %2$s:%3$d', $transportDescription, $hostData->hostnameDomainOnly, $port );
			/* translators: where %1$s is a description of the transport (eg. SMTPS-SSL), %2$s is a description of the authentication (eg. Password-CRAMMD5), %3$d is the TCP port (eg. 465), %4$d is the hostname */
			$recommendation ['message'] = sprintf ( __ ( 'Postman recommends %1$s with %2$s authentication to host %4$s on port %3$d.', Postman::TEXT_DOMAIN ), $transportDescription, $authDesc, $port, $hostname );
		}
		
		// fill-in the rest of the recommendation
		$recommendation ['transport'] = PostmanSmtpModuleTransport::SLUG;
		$recommendation ['priority'] = $score;
		$recommendation ['port'] = $port;
		$recommendation ['hostname'] = $hostname;
		$recommendation ['transport'] = self::SLUG;
		
		return $recommendation;
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
			$this->addSettings ();
			$this->registerStylesAndScripts ();
		}
	}
	
	/**
	 */
	public function registerStylesAndScripts() {
		// register the stylesheet and javascript external resources
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		wp_register_script ( 'postman_smtp_script', plugins_url ( 'Postman/Postman-Mail/postman_smtp.js', $this->rootPluginFilenameAndPath ), array (
				PostmanViewController::JQUERY_SCRIPT,
				'jquery_validation',
				PostmanViewController::POSTMAN_SCRIPT 
		), $pluginData ['version'] );
	}
	
	/*
	 * What follows in the code responsible for creating the Admin Settings page
	 */
	
	/**
	 */
	public function enqueueScript() {
		wp_enqueue_script ( 'postman_smtp_script' );
	}
	
	/**
	 */
	public function addSettings() {
		$transport = $this;
		$this->options = $this->options;
		$oauthScribe = $transport->getScribe ();
		
		// Sanitize
		add_settings_section ( PostmanAdminController::SMTP_SECTION, __ ( 'Transport Settings', Postman::TEXT_DOMAIN ), array (
				$this,
				'printSmtpSectionInfo' 
		), PostmanAdminController::SMTP_OPTIONS );
		
		add_settings_field ( PostmanOptions::HOSTNAME, __ ( 'Outgoing Mail Server Hostname', Postman::TEXT_DOMAIN ), array (
				$this,
				'hostname_callback' 
		), PostmanAdminController::SMTP_OPTIONS, PostmanAdminController::SMTP_SECTION );
		
		add_settings_field ( PostmanOptions::PORT, __ ( 'Outgoing Mail Server Port', Postman::TEXT_DOMAIN ), array (
				$this,
				'port_callback' 
		), PostmanAdminController::SMTP_OPTIONS, PostmanAdminController::SMTP_SECTION );
		
		add_settings_field ( PostmanOptions::ENVELOPE_SENDER, __ ( 'Envelope-From Email Address', Postman::TEXT_DOMAIN ), array (
				$this,
				'sender_email_callback' 
		), PostmanAdminController::SMTP_OPTIONS, PostmanAdminController::SMTP_SECTION );
		
		add_settings_field ( PostmanOptions::SECURITY_TYPE, _x ( 'Security', 'Configuration Input Field', Postman::TEXT_DOMAIN ), array (
				$this,
				'encryption_type_callback' 
		), PostmanAdminController::SMTP_OPTIONS, PostmanAdminController::SMTP_SECTION );
		
		add_settings_field ( PostmanOptions::AUTHENTICATION_TYPE, __ ( 'Authentication', Postman::TEXT_DOMAIN ), array (
				$this,
				'authentication_type_callback' 
		), PostmanAdminController::SMTP_OPTIONS, PostmanAdminController::SMTP_SECTION );
		
		add_settings_section ( PostmanAdminController::BASIC_AUTH_SECTION, __ ( 'Authentication', Postman::TEXT_DOMAIN ), array (
				$this,
				'printBasicAuthSectionInfo' 
		), PostmanAdminController::BASIC_AUTH_OPTIONS );
		
		add_settings_field ( PostmanOptions::BASIC_AUTH_USERNAME, __ ( 'Username', Postman::TEXT_DOMAIN ), array (
				$this,
				'basic_auth_username_callback' 
		), PostmanAdminController::BASIC_AUTH_OPTIONS, PostmanAdminController::BASIC_AUTH_SECTION );
		
		add_settings_field ( PostmanOptions::BASIC_AUTH_PASSWORD, __ ( 'Password', Postman::TEXT_DOMAIN ), array (
				$this,
				'basic_auth_password_callback' 
		), PostmanAdminController::BASIC_AUTH_OPTIONS, PostmanAdminController::BASIC_AUTH_SECTION );
		
		// the OAuth section
		add_settings_section ( PostmanAdminController::OAUTH_SECTION, __ ( 'Authentication', Postman::TEXT_DOMAIN ), array (
				$this,
				'printOAuthSectionInfo' 
		), PostmanAdminController::OAUTH_AUTH_OPTIONS );
		
		add_settings_field ( 'callback_domain', sprintf ( '<span id="callback_domain">%s</span>', $oauthScribe->getCallbackDomainLabel () ), array (
				$this,
				'callback_domain_callback' 
		), PostmanAdminController::OAUTH_AUTH_OPTIONS, PostmanAdminController::OAUTH_SECTION );
		
		add_settings_field ( 'redirect_url', sprintf ( '<span id="redirect_url">%s</span>', $oauthScribe->getCallbackUrlLabel () ), array (
				$this,
				'redirect_url_callback' 
		), PostmanAdminController::OAUTH_AUTH_OPTIONS, PostmanAdminController::OAUTH_SECTION );
		
		add_settings_field ( PostmanOptions::CLIENT_ID, $oauthScribe->getClientIdLabel (), array (
				$this,
				'oauth_client_id_callback' 
		), PostmanAdminController::OAUTH_AUTH_OPTIONS, PostmanAdminController::OAUTH_SECTION );
		
		add_settings_field ( PostmanOptions::CLIENT_SECRET, $oauthScribe->getClientSecretLabel (), array (
				$this,
				'oauth_client_secret_callback' 
		), PostmanAdminController::OAUTH_AUTH_OPTIONS, PostmanAdminController::OAUTH_SECTION );
	}
	
	/**
	 * Print the Section text
	 */
	public function printSmtpSectionInfo() {
		print __ ( 'Configure the communication with the mail server.', Postman::TEXT_DOMAIN );
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function hostname_callback() {
		printf ( '<input type="text" id="input_hostname" name="postman_options[hostname]" value="%s" size="40" class="required" placeholder="%s"/>', null !== $this->options->getHostname () ? esc_attr ( $this->options->getHostname () ) : '', __ ( 'Required', Postman::TEXT_DOMAIN ) );
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function port_callback($args) {
		printf ( '<input type="text" id="input_port" name="postman_options[port]" value="%s" %s placeholder="%s"/>', null !== $this->options->getPort () ? esc_attr ( $this->options->getPort () ) : '', isset ( $args ['style'] ) ? $args ['style'] : '', __ ( 'Required', Postman::TEXT_DOMAIN ) );
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function encryption_type_callback() {
		$encType = $this->options->getEncryptionType ();
		print '<select id="input_enc_type" class="input_encryption_type" name="postman_options[enc_type]">';
		printf ( '<option class="input_enc_type_none" value="%s" %s>%s</option>', PostmanOptions::SECURITY_TYPE_NONE, $encType == PostmanOptions::SECURITY_TYPE_NONE ? 'selected="selected"' : '', __ ( 'None', Postman::TEXT_DOMAIN ) );
		printf ( '<option class="input_enc_type_ssl" value="%s" %s>%s</option>', PostmanOptions::SECURITY_TYPE_SMTPS, $encType == PostmanOptions::SECURITY_TYPE_SMTPS ? 'selected="selected"' : '', 'SMTPS' );
		printf ( '<option class="input_enc_type_tls" value="%s" %s>%s</option>', PostmanOptions::SECURITY_TYPE_STARTTLS, $encType == PostmanOptions::SECURITY_TYPE_STARTTLS ? 'selected="selected"' : '', 'STARTTLS' );
		print '</select>';
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function authentication_type_callback() {
		$authType = $this->options->getAuthenticationType ();
		printf ( '<select id="input_%2$s" class="input_%2$s" name="%1$s[%2$s]">', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::AUTHENTICATION_TYPE );
		printf ( '<option class="input_auth_type_none" value="%s" %s>%s</option>', PostmanOptions::AUTHENTICATION_TYPE_NONE, $authType == PostmanOptions::AUTHENTICATION_TYPE_NONE ? 'selected="selected"' : '', 'None' );
		printf ( '<option class="input_auth_type_plain" value="%s" %s>%s</option>', PostmanOptions::AUTHENTICATION_TYPE_PLAIN, $authType == PostmanOptions::AUTHENTICATION_TYPE_PLAIN ? 'selected="selected"' : '', 'Plain' );
		printf ( '<option class="input_auth_type_login" value="%s" %s>%s</option>', PostmanOptions::AUTHENTICATION_TYPE_LOGIN, $authType == PostmanOptions::AUTHENTICATION_TYPE_LOGIN ? 'selected="selected"' : '', 'Login' );
		printf ( '<option class="input_auth_type_crammd5" value="%s" %s>%s</option>', PostmanOptions::AUTHENTICATION_TYPE_CRAMMD5, $authType == PostmanOptions::AUTHENTICATION_TYPE_CRAMMD5 ? 'selected="selected"' : '', 'CRAM-MD5' );
		printf ( '<option class="input_auth_type_oauth2" value="%s" %s>%s</option>', PostmanOptions::AUTHENTICATION_TYPE_OAUTH2, $authType == PostmanOptions::AUTHENTICATION_TYPE_OAUTH2 ? 'selected="selected"' : '', 'OAuth 2.0' );
		print '</select>';
	}
	
	/**
	 * Print the Section text
	 */
	public function printBasicAuthSectionInfo() {
		print __ ( 'Enter the account credentials.', Postman::TEXT_DOMAIN );
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function basic_auth_username_callback() {
		$inputValue = (null !== $this->options->getUsername () ? esc_attr ( $this->options->getUsername () ) : '');
		$inputDescription = __ ( 'The Username is usually the same as the Envelope-From Email Address.', Postman::TEXT_DOMAIN );
		print ('<input tabindex="99" id="fake_user_name" name="fake_user[name]" style="position:absolute; top:-500px;" type="text" value="Safari Autofill Me">') ;
		printf ( '<input type="text" id="input_basic_auth_username" name="postman_options[basic_auth_username]" value="%s" size="40" class="required" placeholder="%s"/><br/><span class="postman_input_description">%s</span>', $inputValue, __ ( 'Required', Postman::TEXT_DOMAIN ), $inputDescription );
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function basic_auth_password_callback() {
		print ('<input tabindex="99" id="fake_password" name="fake[password]" style="position:absolute; top:-500px;" type="password" value="Safari Autofill Me">') ;
		printf ( '<input type="password" id="input_basic_auth_password" name="postman_options[basic_auth_password]" value="%s" size="40" class="required" placeholder="%s"/>', null !== $this->options->getPassword () ? esc_attr ( PostmanUtils::obfuscatePassword ( $this->options->getPassword () ) ) : '', __ ( 'Required', Postman::TEXT_DOMAIN ) );
		print ' <input type="button" id="togglePasswordField" value="Show Password" class="button button-secondary" style="visibility:hidden" />';
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function oauth_client_id_callback() {
		printf ( '<input type="text" onClick="this.setSelectionRange(0, this.value.length)" id="oauth_client_id" name="postman_options[oauth_client_id]" value="%s" size="60" class="required" placeholder="%s"/>', null !== $this->options->getClientId () ? esc_attr ( $this->options->getClientId () ) : '', __ ( 'Required', Postman::TEXT_DOMAIN ) );
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function oauth_client_secret_callback() {
		printf ( '<input type="text" onClick="this.setSelectionRange(0, this.value.length)" autocomplete="off" id="oauth_client_secret" name="postman_options[oauth_client_secret]" value="%s" size="60" class="required" placeholder="%s"/>', null !== $this->options->getClientSecret () ? esc_attr ( $this->options->getClientSecret () ) : '', __ ( 'Required', Postman::TEXT_DOMAIN ) );
	}
	
	/**
	 * Print the Section text
	 */
	public function printOAuthSectionInfo() {
		$this->options = $this->options;
		$transport = $this;
		$oauthScribe = $transport->getScribe ();
		printf ( '<p id="wizard_oauth2_help">%s</p>', $oauthScribe->getOAuthHelp () );
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function callback_domain_callback() {
		printf ( '<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly="readonly" id="input_oauth_callback_domain" value="%s" size="60"/>', $this->getCallbackDomain () );
	}
	
	/**
	 */
	private function getCallbackDomain() {
		try {
			$this->options = $this->options;
			$transport = $this;
			$oauthScribe = $transport->getScribe ();
			return $oauthScribe->getCallbackDomain ();
		} catch ( Exception $e ) {
			return __ ( 'Error computing your domain root - please enter it manually', Postman::TEXT_DOMAIN );
		}
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function redirect_url_callback() {
		$this->options = $this->options;
		$transport = $this;
		$oauthScribe = $transport->getScribe ();
		printf ( '<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly="readonly" id="input_oauth_redirect_url" value="%s" size="60"/>', $oauthScribe->getCallbackUrl () );
	}
	
	/**
	 * Get the settings option array and print one of its values
	 */
	public function sender_email_callback() {
		$inputValue = (null !== $this->options->getEnvelopeSender () ? esc_attr ( $this->options->getEnvelopeSender () ) : '');
		$requiredLabel = __ ( 'Required', Postman::TEXT_DOMAIN );
		$envelopeFromMessage = __ ( 'This address, like the <b>return address</b> printed on an envelope, identifies the account owner to the SMTP server.', Postman::TEXT_DOMAIN );
		$spfMessage = sprintf ( __ ( 'For reliable delivery, this domain must specify an <a target="_new" href="%s">SPF record</a> permitting the use of the SMTP server named above.', Postman::TEXT_DOMAIN ), 'https://www.mail-tester.com/spf/' );
		printf ( '<input type="email" id="input_envelope_sender_email" name="postman_options[envelope_sender]" value="%s" size="40" class="required" placeholder="%s"/> <br/><span class="postman_input_description">%s %s</span>', $inputValue, $requiredLabel, $envelopeFromMessage, $spfMessage );
	}
	
	/**
	 */
	public function printWizardMailServerHostnameStep() {
		printf ( '<legend>%s</legend>', _x ( 'Which host will relay the mail?', 'Wizard Step Title', Postman::TEXT_DOMAIN ) );
		printf ( '<p>%s</p>', __ ( 'This is the Outgoing (SMTP) Mail Server, or Mail Submission Agent (MSA), which Postman delegates mail delivery to. This server is specific to your email account, and if you don\'t know what to use, ask your email service provider.', Postman::TEXT_DOMAIN ) );
		printf ( '<p>%s</p>', __ ( 'Note that many WordPress hosts, such as GoDaddy, Bluehost and Dreamhost, require that you use their mail accounts with their mail servers, and prevent you from using others.', Postman::TEXT_DOMAIN ) );
		printf ( '<label for="hostname">%s</label>', __ ( 'Outgoing Mail Server Hostname', Postman::TEXT_DOMAIN ) );
		print $this->hostname_callback ();
		printf ( '<p class="ajax-loader" style="display:none"><img src="%s"/></p>', plugins_url ( 'postman-smtp/style/ajax-loader.gif' ) );
		$warning = __ ( 'Warning', Postman::TEXT_DOMAIN );
		/* Translators: Where (%s) is the name of the web host */
		$nonGodaddyDomainMessage = sprintf ( __ ( 'Your email address <b>requires</b> access to a remote SMTP server blocked by %s.', Postman::TEXT_DOMAIN ), 'GoDaddy' );
		$nonGodaddyDomainMessage .= sprintf ( ' %s', __ ( 'If you have access to cPanel, enable the Remote Mail Exchanger.', Postman::TEXT_DOMAIN ) );
		printf ( '<p id="godaddy_block"><span style="background-color:yellow"><b>%s</b>: %s</span></p>', $warning, $nonGodaddyDomainMessage );
		/* Translators: Where (%1$s) is the SPF-info URL and (%2$s) is the name of the web host */
		$godaddyCustomDomainMessage = sprintf ( __ ( 'If you own this domain, make sure it has an <a href="%1$s">SPF record authorizing %2$s</a> as a relay, or you will have delivery problems.', Postman::TEXT_DOMAIN ), 'http://www.mail-tester.com/spf/godaddy', 'GoDaddy' );
		printf ( '<p id="godaddy_spf_required"><span style="background-color:yellow"><b>%s</b>: %s</span></p>', $warning, $godaddyCustomDomainMessage );
	}
	
	/**
	 */
	public function printWizardAuthenticationStep() {
		print '<section class="wizard-auth-oauth2">';
		print '<p id="wizard_oauth2_help"></p>';
		printf ( '<label id="callback_domain" for="callback_domain">%s</label>', $this->getScribe ()->getCallbackDomainLabel () );
		print '<br />';
		print $this->callback_domain_callback ();
		print '<br />';
		printf ( '<label id="redirect_url" for="redirect_uri">%s</label>', $this->getScribe ()->getCallbackUrlLabel () );
		print '<br />';
		print $this->redirect_url_callback ();
		print '<br />';
		printf ( '<label id="client_id" for="client_id">%s</label>', $this->getScribe ()->getClientIdLabel () );
		print '<br />';
		print $this->oauth_client_id_callback ();
		print '<br />';
		printf ( '<label id="client_secret" for="client_secret">%s</label>', $this->getScribe ()->getClientSecretLabel () );
		print '<br />';
		print $this->oauth_client_secret_callback ();
		print '<br />';
		print '</section>';
		
		print '<section class="wizard-auth-basic">';
		printf ( '<p class="port-explanation-ssl">%s</p>', __ ( 'Enter the account credentials.', Postman::TEXT_DOMAIN ) );
		printf ( '<label for="username">%s</label>', __ ( 'Username', Postman::TEXT_DOMAIN ) );
		print '<br />';
		print $this->basic_auth_username_callback ();
		print '<br />';
		printf ( '<label for="password">%s</label>', __ ( 'Password', Postman::TEXT_DOMAIN ) );
		print '<br />';
		print $this->basic_auth_password_callback ();
		print '</section>';
	}
}
