<?php
require_once 'PostmanModuleTransport.php';
/**
 * Postman SendGrid module
 *
 * @author jasonhendriks
 *        
 */
class PostmanSendGridTransport extends PostmanAbstractModuleTransport implements PostmanModuleTransport {
	const SLUG = 'sendgrid_api';
	const PORT = 443;
	const HOST = 'api.sendgrid.com';
	const PRIORITY = 8000;
	const SENDGRID_AUTH_OPTIONS = 'postman_sendgrid_auth_options';
	const SENDGRID_AUTH_SECTION = 'postman_sendgrid_auth_section';
	
	/**
	 *
	 * @param unknown $rootPluginFilenameAndPath        	
	 */
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
	
	// this should be standard across all transports
	public function getSlug() {
		return self::SLUG;
	}
	public function getName() {
		return __ ( 'SendGrid API', Postman::TEXT_DOMAIN );
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
		return 'SendGrid_api';
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanModuleTransport::createMailEngine()
	 */
	public function createMailEngine() {
		$apiKey = $this->options->getSendGridApiKey ();
		require_once 'PostmanSendGridMailEngine.php';
		$engine = new PostmanSendGridMailEngine ( $apiKey );
		return $engine;
	}
	public function getDeliveryDetails() {
		/* translators: where (1) is the secure icon and (2) is the transport name */
		return sprintf ( __ ( 'Postman will send mail via the <b>%1$s %2$s</b>.', Postman::TEXT_DOMAIN ), '🔐', $this->getName () );
	}
	
	/**
	 *
	 * @param unknown $data        	
	 */
	public function prepareOptionsForExport($data) {
		$data = parent::prepareOptionsForExport ( $data );
		$data [PostmanOptions::SENDGRID_API_KEY] = PostmanOptions::getInstance ()->getSendGridApiKey ();
		return $data;
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see PostmanTransport::getMisconfigurationMessage()
	 */
	protected function validateTransportConfiguration() {
		$messages = parent::validateTransportConfiguration ();
		$apiKey = $this->options->getSendGridApiKey ();
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
	 *
	 * @param unknown $hostname        	
	 * @param unknown $response        	
	 */
	public function populateConfiguration($hostname) {
		$response = parent::populateConfiguration ( $hostname );
		return $response;
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
		// the SendGrid Auth section
		add_settings_section ( PostmanSendGridTransport::SENDGRID_AUTH_SECTION, __ ( 'Authentication', Postman::TEXT_DOMAIN ), array (
				$this,
				'printSendGridAuthSectionInfo' 
		), PostmanSendGridTransport::SENDGRID_AUTH_OPTIONS );
		
		add_settings_field ( PostmanOptions::SENDGRID_API_KEY, __ ( 'API Key', Postman::TEXT_DOMAIN ), array (
				$this,
				'sendgrid_api_key_callback' 
		), PostmanSendGridTransport::SENDGRID_AUTH_OPTIONS, PostmanSendGridTransport::SENDGRID_AUTH_SECTION );
	}
	public function printSendGridAuthSectionInfo() {
		/* Translators: Where (1) is the service URL and (2) is the service name and (3) is a api key URL */
		printf ( '<p id="wizard_sendgrid_auth_help">%s</p>', sprintf ( __ ( 'Create an account at <a href="%1$s" target="_new">%2$s</a> and enter <a href="%3$s" target="_new">an API key</a> below.', Postman::TEXT_DOMAIN ), 'https://sendgrid.com', 'SendGrid.com', 'https://app.sendgrid.com/settings/api_keys' ) );
	}
	
	/**
	 */
	public function sendgrid_api_key_callback() {
		printf ( '<input type="password" autocomplete="off" id="sendgrid_api_key" name="postman_options[sendgrid_api_key]" value="%s" size="60" class="required" placeholder="%s"/>', null !== $this->options->getSendGridApiKey () ? esc_attr ( PostmanUtils::obfuscatePassword ( $this->options->getSendGridApiKey () ) ) : '', __ ( 'Required', Postman::TEXT_DOMAIN ) );
		print ' <input type="button" id="toggleSendGridApiKey" value="Show Password" class="button button-secondary" style="visibility:hidden" />';
	}
	
	/**
	 */
	public function registerStylesAndScripts() {
		// register the stylesheet and javascript external resources
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		wp_register_script ( 'postman_sendgrid_script', plugins_url ( 'Postman/Postman-Mail/postman_sendgrid.js', $this->rootPluginFilenameAndPath ), array (
				PostmanViewController::JQUERY_SCRIPT,
				'jquery_validation',
				PostmanViewController::POSTMAN_SCRIPT 
		), $pluginData ['version'] );
	}
	
	/**
	 */
	public function enqueueScript() {
		wp_enqueue_script ( 'postman_sendgrid_script' );
	}
	
	/**
	 */
	public function printWizardAuthenticationStep() {
		print '<section class="wizard_sendgrid">';
		$this->printSendGridAuthSectionInfo ();
		printf ( '<label for="api_key">%s</label>', __ ( 'API Key', Postman::TEXT_DOMAIN ) );
		print '<br />';
		print $this->sendgrid_api_key_callback ();
		print '</section>';
	}
}
