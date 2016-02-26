<?php
require_once 'PostmanModuleTransport.php';
/**
 * Postman Mandrill module
 *
 * @author jasonhendriks
 *        
 */
class PostmanMandrillTransport extends PostmanAbstractModuleTransport implements PostmanModuleTransport {
	const SLUG = 'mandrill_api';
	const PORT = 443;
	const HOST = 'mandrillapp.com';
	const PRIORITY = 9000;
	const MANDRILL_AUTH_OPTIONS = 'postman_mandrill_auth_options';
	const MANDRILL_AUTH_SECTION = 'postman_mandrill_auth_section';
	public function __construct($rootPluginFilenameAndPath) {
		parent::__construct ( $rootPluginFilenameAndPath );
		
		// add a hook on the plugins_loaded event
		add_action ( 'admin_init', array (
				$this,
				'on_admin_init' 
		) );
	}
	
	/**
	 *
	 * @param unknown $data        	
	 */
	public function prepareOptionsForExport($data) {
		$data = parent::prepareOptionsForExport ( $data );
		$data [PostmanOptions::MANDRILL_API_KEY] = PostmanOptions::getInstance ()->getMandrillApiKey ();
		return $data;
	}
	public function getProtocol() {
		return 'https';
	}
	
	// this should be standard across all transports
	public function getSlug() {
		return self::SLUG;
	}
	public function getName() {
		return __ ( 'Mandrill API', Postman::TEXT_DOMAIN );
	}
	/**
	 * v0.2.1
	 *
	 * @return string
	 */
	public function getHostname() {
		return self::HOST;
	}
	/**
	 * v0.2.1
	 *
	 * @return string
	 */
	public function getPort() {
		return self::PORT;
	}
	/**
	 * v1.7.0
	 *
	 * @return string
	 */
	public function getTransportType() {
		return 'mandrill_api';
	}
	/**
	 * v0.2.1
	 *
	 * @return string
	 */
	public function getAuthenticationType() {
		return '';
	}
	/**
	 * v0.2.1
	 *
	 * @return string
	 */
	public function getSecurityType() {
		return self::PROTOCOL;
	}
	/**
	 * v0.2.1
	 *
	 * @return string
	 */
	public function getCredentialsId() {
		return $this->options->getClientId ();
	}
	/**
	 * v0.2.1
	 *
	 * @return string
	 */
	public function getCredentialsSecret() {
		return $this->options->getClientSecret ();
	}
	public function isServiceProviderGoogle($hostname) {
		return false;
	}
	public function isServiceProviderMicrosoft($hostname) {
		return false;
	}
	public function isServiceProviderYahoo($hostname) {
		return false;
	}
	public function isOAuthUsed($authType) {
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanModuleTransport::createMailEngine()
	 */
	public function createMailEngine() {
		$apiKey = $this->options->getMandrillApiKey ();
		require_once 'PostmanMandrillMailEngine.php';
		$engine = new PostmanMandrillMailEngine ( $apiKey );
		return $engine;
	}
	
	/**
	 * This short description of the Transport State shows on the Summary screens
	 * (non-PHPdoc)
	 *
	 * @see PostmanModuleTransport::getDeliveryDetails()
	 */
	public function getDeliveryDetails() {
		/* translators: where (1) is the secure icon and (2) is the transport name */
		return sprintf ( __ ( 'Postman will send mail via the <b>%1$s %2$s</b>.', Postman::TEXT_DOMAIN ), '🔐', $this->getName () );
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanAbstractModuleTransport::validateTransportConfiguration()
	 */
	protected function validateTransportConfiguration() {
		$messages = parent::validateTransportConfiguration ();
		$apiKey = $this->options->getMandrillApiKey ();
		if (empty ( $apiKey )) {
			array_push ( $messages, __ ( 'API Key can not be empty', Postman::TEXT_DOMAIN ) . '.' );
			$this->setNotConfiguredAndReady ();
		}
		if (! $this->isSenderConfigured ()) {
			array_push ( $messages, __ ( 'Message From Address can not be empty', Postman::TEXT_DOMAIN ) . '.' );
			$this->setNotConfiguredAndReady ();
		}
		return $messages;
	}
	
	/**
	 * Mandrill API doesn't care what the hostname or guessed SMTP Server is; it runs it's port test no matter what
	 */
	public function getSocketsForSetupWizardToProbe($hostname, $smtpServerGuess) {
		$hosts = array (
				self::createSocketDefinition ( $this->getHostname (), $this->getPort () ) 
		);
		return $hosts;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanModuleTransport::getConfigurationBid()
	 */
	public function getConfigurationBid(PostmanWizardSocket $hostData, $userAuthOverride, $originalSmtpServer) {
		$recommendation = array ();
		$recommendation ['priority'] = 0;
		$recommendation ['transport'] = self::SLUG;
		$recommendation ['hostname'] = null; // scribe looks this
		$recommendation ['label'] = $this->getName ();
		if ($hostData->hostname == self::HOST && $hostData->port == self::PORT) {
			$recommendation ['priority'] = self::PRIORITY;
			/* translators: where variables are (1) transport name (2) host and (3) port */
			$recommendation ['message'] = sprintf ( __ ( ('Postman recommends the %1$s to host %2$s on port %3$d.') ), $this->getName (), self::HOST, self::PORT );
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
						'name' => __ ( 'API Key', Postman::TEXT_DOMAIN ),
						'value' => 'api_key' 
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
			$this->addSettings ();
			$this->registerStylesAndScripts ();
		}
	}
	
	/*
	 * What follows in the code responsible for creating the Admin Settings page
	 */
	
	/**
	 */
	public function addSettings() {
		// the Mandrill Auth section
		add_settings_section ( PostmanMandrillTransport::MANDRILL_AUTH_SECTION, __ ( 'Authentication', Postman::TEXT_DOMAIN ), array (
				$this,
				'printMandrillAuthSectionInfo' 
		), PostmanMandrillTransport::MANDRILL_AUTH_OPTIONS );
		
		add_settings_field ( PostmanOptions::MANDRILL_API_KEY, __ ( 'API Key', Postman::TEXT_DOMAIN ), array (
				$this,
				'mandrill_api_key_callback' 
		), PostmanMandrillTransport::MANDRILL_AUTH_OPTIONS, PostmanMandrillTransport::MANDRILL_AUTH_SECTION );
	}
	
	/**
	 */
	public function printMandrillAuthSectionInfo() {
		/* Translators: Where (1) is the service URL and (2) is the service name and (3) is a api key URL */
		printf ( '<p id="wizard_mandrill_auth_help">%s</p>', sprintf ( __ ( 'Create an account at <a href="%1$s" target="_new">%2$s</a> and enter <a href="%3$s" target="_new">an API key</a> below.', Postman::TEXT_DOMAIN ), 'https://mandrillapp.com', 'Mandrillapp.com', 'https://mandrillapp.com/settings' ) );
	}
	
	/**
	 */
	public function mandrill_api_key_callback() {
		printf ( '<input type="password" autocomplete="off" id="mandrill_api_key" name="postman_options[mandrill_api_key]" value="%s" size="60" class="required" placeholder="%s"/>', null !== $this->options->getMandrillApiKey () ? esc_attr ( PostmanUtils::obfuscatePassword ( $this->options->getMandrillApiKey () ) ) : '', __ ( 'Required', Postman::TEXT_DOMAIN ) );
		print ' <input type="button" id="toggleMandrillApiKey" value="Show Password" class="button button-secondary" style="visibility:hidden" />';
	}
	
	/**
	 */
	public function registerStylesAndScripts() {
		// register the stylesheet and javascript external resources
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		wp_register_script ( 'postman_mandrill_script', plugins_url ( 'Postman/Postman-Mail/postman_mandrill.js', $this->rootPluginFilenameAndPath ), array (
				PostmanViewController::JQUERY_SCRIPT,
				'jquery_validation',
				PostmanViewController::POSTMAN_SCRIPT 
		), $pluginData ['version'] );
	}
	
	/**
	 */
	public function enqueueScript() {
		wp_enqueue_script ( 'postman_mandrill_script' );
	}
	
	/**
	 */
	public function printWizardAuthenticationStep() {
		print '<section class="wizard_mandrill">';
		$this->printMandrillAuthSectionInfo ();
		printf ( '<label for="api_key">%s</label>', __ ( 'API Key', Postman::TEXT_DOMAIN ) );
		print '<br />';
		print $this->mandrill_api_key_callback ();
		print '</section>';
	}
}
