<?php
require_once ('PostmanRegisterConfigurationSettings.php');
class PostmanConfigurationController {
	const CONFIGURATION_SLUG = 'postman/configuration';
	const CONFIGURATION_WIZARD_SLUG = 'postman/configuration_wizard';
	
	// logging
	private $logger;
	private $options;
	private $settingsRegistry;
	
	// Holds the values to be used in the fields callbacks
	private $rootPluginFilenameAndPath;
	
	/**
	 * Constructor
	 *
	 * @param unknown $rootPluginFilenameAndPath        	
	 */
	public function __construct($rootPluginFilenameAndPath) {
		assert ( ! empty ( $rootPluginFilenameAndPath ) );
		assert ( PostmanUtils::isAdmin () );
		assert ( is_admin () );
		
		$this->logger = new PostmanLogger ( get_class ( $this ) );
		$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
		$this->options = PostmanOptions::getInstance ();
		$this->settingsRegistry = new PostmanSettingsRegistry ();
		
		PostmanUtils::registerAdminMenu ( $this, 'addConfigurationSubmenu' );
		PostmanUtils::registerAdminMenu ( $this, 'addSetupWizardSubmenu' );
		
		// hook on the init event
		add_action ( 'init', array (
				$this,
				'on_init' 
		) );
		
		// initialize the scripts, stylesheets and form fields
		add_action ( 'admin_init', array (
				$this,
				'on_admin_init' 
		) );
	}
	
	/**
	 * Functions to execute on the init event
	 *
	 * "Typically used by plugins to initialize. The current user is already authenticated by this time."
	 * ref: http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_a_Typical_Request
	 */
	public function on_init() {
		// register Ajax handlers
		new PostmanGetHostnameByEmailAjaxController ();
		new PostmanManageConfigurationAjaxHandler ();
		new PostmanImportConfigurationAjaxController ( $this->options );
	}
	
	/**
	 * Fires on the admin_init method
	 */
	public function on_admin_init() {
		//
		$this->registerStylesAndScripts ();
		$this->settingsRegistry->on_admin_init ();
	}
	
	/**
	 * Register and add settings
	 */
	private function registerStylesAndScripts() {
		if ($this->logger->isTrace ()) {
			$this->logger->trace ( 'registerStylesAndScripts()' );
		}
		// register the stylesheet and javascript external resources
		$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
		wp_register_script ( 'postman_manual_config_script', plugins_url ( 'Postman/Postman-Configuration/postman_manual_config.js', $this->rootPluginFilenameAndPath ), array (
				PostmanViewController::JQUERY_SCRIPT,
				'jquery_validation',
				PostmanViewController::POSTMAN_SCRIPT 
		), $pluginData ['version'] );
		wp_register_script ( 'postman_wizard_script', plugins_url ( 'Postman/Postman-Configuration/postman_wizard.js', $this->rootPluginFilenameAndPath ), array (
				PostmanViewController::JQUERY_SCRIPT,
				'jquery_validation',
				'jquery_steps_script',
				PostmanViewController::POSTMAN_SCRIPT,
				'sprintf' 
		), $pluginData ['version'] );
	}
	
	/**
	 */
	private function addLocalizeScriptsToPage() {
		$warning = __ ( 'Warning', Postman::TEXT_DOMAIN );
		/* translators: where %s is the name of the SMTP server */
		wp_localize_script ( 'postman_wizard_script', 'postman_smtp_mitm', sprintf ( '%s: %s', $warning, __ ( 'connected to %1$s instead of %2$s.', Postman::TEXT_DOMAIN ) ) );
		/* translators: where %d is a port number */
		wp_localize_script ( 'postman_wizard_script', 'postman_wizard_bad_redirect_url', __ ( 'You are about to configure OAuth 2.0 with an IP address instead of a domain name. This is not permitted. Either assign a real domain name to your site or add a fake one in your local host file.', Postman::TEXT_DOMAIN ) );
		
		// user input
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_input_sender_email', '#input_' . PostmanOptions::MESSAGE_SENDER_EMAIL );
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_input_sender_name', '#input_' . PostmanOptions::MESSAGE_SENDER_NAME );
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_port_element_name', '#input_' . PostmanOptions::PORT );
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_hostname_element_name', '#input_' . PostmanOptions::HOSTNAME );
		
		// the enc input
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_enc_for_password_el', '#input_enc_type_password' );
		// these are the ids for the <option>s in the encryption <select>
		
		// the password inputs
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_input_basic_username', '#input_' . PostmanOptions::BASIC_AUTH_USERNAME );
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_input_basic_password', '#input_' . PostmanOptions::BASIC_AUTH_PASSWORD );
		
		// the auth input
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_redirect_url_el', '#input_oauth_redirect_url' );
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_input_auth_type', '#input_' . PostmanOptions::AUTHENTICATION_TYPE );
		
		// the transport modules scripts
		foreach ( PostmanTransportRegistry::getInstance ()->getTransports () as $transport ) {
			$transport->enqueueScript ();
		}

		// we need data from port test
		PostmanConnectivityTestController::addLocalizeScriptForPortTest ();
		
	}
	
	/**
	 * Register the Configuration screen
	 */
	public function addConfigurationSubmenu() {
		$page = add_submenu_page ( null, sprintf ( __ ( '%s Setup', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ), Postman::MANAGE_POSTMAN_CAPABILITY_NAME, PostmanConfigurationController::CONFIGURATION_SLUG, array (
				$this,
				'outputManualConfigurationContent' 
		) );
		// When the plugin options page is loaded, also load the stylesheet
		add_action ( 'admin_print_styles-' . $page, array (
				$this,
				'enqueueConfigurationResources' 
		) );
	}
	
	/**
	 */
	function enqueueConfigurationResources() {
		$this->addLocalizeScriptsToPage ();
		wp_enqueue_style ( PostmanViewController::POSTMAN_STYLE );
		wp_enqueue_style ( 'jquery_ui_style' );
		wp_enqueue_script ( 'postman_manual_config_script' );
		wp_enqueue_script ( 'jquery-ui-tabs' );
	}
	
	/**
	 * Register the Setup Wizard screen
	 */
	public function addSetupWizardSubmenu() {
		$page = add_submenu_page ( null, sprintf ( __ ( '%s Setup', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ), Postman::MANAGE_POSTMAN_CAPABILITY_NAME, PostmanConfigurationController::CONFIGURATION_WIZARD_SLUG, array (
				$this,
				'outputWizardContent' 
		) );
		// When the plugin options page is loaded, also load the stylesheet
		add_action ( 'admin_print_styles-' . $page, array (
				$this,
				'enqueueWizardResources' 
		) );
	}
	
	/**
	 */
	function enqueueWizardResources() {
		$this->addLocalizeScriptsToPage ();
		$this->importableConfiguration = new PostmanImportableConfiguration ();
		$startPage = 1;
		if ($this->importableConfiguration->isImportAvailable ()) {
			$startPage = 0;
		}
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_setup_wizard', array (
				'start_page' => $startPage 
		) );
		wp_enqueue_style ( 'jquery_steps_style' );
		wp_enqueue_style ( PostmanViewController::POSTMAN_STYLE );
		wp_enqueue_script ( 'postman_wizard_script' );
		wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, '$jq', 'jQuery.noConflict(true)' );
		$shortLocale = substr ( get_locale (), 0, 2 );
		if ($shortLocale != 'en') {
			$url = plugins_url ( sprintf ( 'script/jquery-validate/localization/messages_%s.js', $shortLocale ), $this->rootPluginFilenameAndPath );
			wp_enqueue_script ( sprintf ( 'jquery-validation-locale-%s', $shortLocale ), $url );
		}
	}
	
	/**
	 */
	public function outputManualConfigurationContent() {
		print '<div class="wrap">';
		
		PostmanViewController::outputChildPageHeader ( __ ( 'Settings', Postman::TEXT_DOMAIN ), 'advanced_config' );
		print '<div id="config_tabs"><ul>';
		print sprintf ( '<li><a href="#account_config">%s</a></li>', __ ( 'Account', Postman::TEXT_DOMAIN ) );
		print sprintf ( '<li><a href="#message_config">%s</a></li>', __ ( 'Message', Postman::TEXT_DOMAIN ) );
		print sprintf ( '<li><a href="#logging_config">%s</a></li>', __ ( 'Logging', Postman::TEXT_DOMAIN ) );
		print sprintf ( '<li><a href="#advanced_options_config">%s</a></li>', __ ( 'Advanced', Postman::TEXT_DOMAIN ) );
		print '</ul>';
		print '<form method="post" action="options.php">';
		// This prints out all hidden setting fields
		settings_fields ( PostmanAdminController::SETTINGS_GROUP_NAME );
		print '<section id="account_config">';
		if (sizeof ( PostmanTransportRegistry::getInstance ()->getTransports () ) > 1) {
			do_settings_sections ( 'transport_options' );
		} else {
			printf ( '<input id="input_%2$s" type="hidden" name="%1$s[%2$s]" value="%3$s"/>', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::TRANSPORT_TYPE, PostmanSmtpModuleTransport::SLUG );
		}
		print '<div id="smtp_config" class="transport_setting">';
		do_settings_sections ( PostmanAdminController::SMTP_OPTIONS );
		print '</div>';
		print '<div id="password_settings" class="authentication_setting non-oauth2">';
		do_settings_sections ( PostmanAdminController::BASIC_AUTH_OPTIONS );
		print '</div>';
		print '<div id="oauth_settings" class="authentication_setting non-basic">';
		do_settings_sections ( PostmanAdminController::OAUTH_AUTH_OPTIONS );
		print '</div>';
		print '<div id="mandrill_settings" class="authentication_setting non-basic non-oauth2">';
		do_settings_sections ( PostmanMandrillTransport::MANDRILL_AUTH_OPTIONS );
		print '</div>';
		print '<div id="sendgrid_settings" class="authentication_setting non-basic non-oauth2">';
		do_settings_sections ( PostmanSendGridTransport::SENDGRID_AUTH_OPTIONS );
		print '</div>';
		print '</section>';
		print '<section id="message_config">';
		do_settings_sections ( PostmanAdminController::MESSAGE_SENDER_OPTIONS );
		do_settings_sections ( PostmanAdminController::MESSAGE_FROM_OPTIONS );
		do_settings_sections ( PostmanAdminController::EMAIL_VALIDATION_OPTIONS );
		do_settings_sections ( PostmanAdminController::MESSAGE_OPTIONS );
		do_settings_sections ( PostmanAdminController::MESSAGE_HEADERS_OPTIONS );
		print '</section>';
		print '<section id="logging_config">';
		do_settings_sections ( PostmanAdminController::LOGGING_OPTIONS );
		print '</section>';
		/*
		 * print '<section id="logging_config">';
		 * do_settings_sections ( PostmanAdminController::MULTISITE_OPTIONS );
		 * print '</section>';
		 */
		print '<section id="advanced_options_config">';
		do_settings_sections ( PostmanAdminController::NETWORK_OPTIONS );
		do_settings_sections ( PostmanAdminController::ADVANCED_OPTIONS );
		print '</section>';
		submit_button ();
		print '</form>';
		print '</div>';
		print '</div>';
	}
	
	/**
	 */
	public function outputWizardContent() {
		// Set default values for input fields
		$this->options->setMessageSenderEmailIfEmpty ( wp_get_current_user ()->user_email );
		$this->options->setMessageSenderNameIfEmpty ( wp_get_current_user ()->display_name );
		
		// construct Wizard
		print '<div class="wrap">';
		
		PostmanViewController::outputChildPageHeader ( __ ( 'Setup Wizard', Postman::TEXT_DOMAIN ) );
		
		print '<form id="postman_wizard" method="post" action="options.php">';
		
		// account tab
		
		// message tab
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::PREVENT_MESSAGE_SENDER_EMAIL_OVERRIDE, $this->options->isPluginSenderEmailEnforced () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::PREVENT_MESSAGE_SENDER_NAME_OVERRIDE, $this->options->isPluginSenderNameEnforced () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::REPLY_TO, $this->options->getReplyTo () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::FORCED_TO_RECIPIENTS, $this->options->getForcedToRecipients () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::FORCED_CC_RECIPIENTS, $this->options->getForcedCcRecipients () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::FORCED_BCC_RECIPIENTS, $this->options->getForcedBccRecipients () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::ADDITIONAL_HEADERS, $this->options->getAdditionalHeaders () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::DISABLE_EMAIL_VALIDAITON, $this->options->isEmailValidationDisabled () );
		
		// logging tab
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::MAIL_LOG_ENABLED_OPTION, $this->options->getMailLoggingEnabled () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::MAIL_LOG_MAX_ENTRIES, $this->options->getMailLoggingMaxEntries () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::TRANSCRIPT_SIZE, $this->options->getTranscriptSize () );
		
		// advanced tab
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::CONNECTION_TIMEOUT, $this->options->getConnectionTimeout () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::READ_TIMEOUT, $this->options->getReadTimeout () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::LOG_LEVEL, $this->options->getLogLevel () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::RUN_MODE, $this->options->getRunMode () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::STEALTH_MODE, $this->options->isStealthModeEnabled () );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]" value="%3$s" />', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::TEMPORARY_DIRECTORY, $this->options->getTempDirectory () );
		
		// display the setting text
		settings_fields ( PostmanAdminController::SETTINGS_GROUP_NAME );
		
		// Wizard Step 0
		printf ( '<h5>%s</h5>', _x ( 'Import Configuration', 'Wizard Step Title', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		printf ( '<legend>%s</legend>', _x ( 'Import configuration from another plugin?', 'Wizard Step Title', Postman::TEXT_DOMAIN ) );
		printf ( '<p>%s</p>', __ ( 'If you had a working configuration with another Plugin, the Setup Wizard can begin with those settings.', Postman::TEXT_DOMAIN ) );
		print '<table class="input_auth_type">';
		printf ( '<tr><td><input type="radio" id="import_none" name="input_plugin" value="%s" checked="checked"></input></td><td><label> %s</label></td></tr>', 'none', __ ( 'None', Postman::TEXT_DOMAIN ) );
		
		if ($this->importableConfiguration->isImportAvailable ()) {
			foreach ( $this->importableConfiguration->getAvailableOptions () as $options ) {
				printf ( '<tr><td><input type="radio" name="input_plugin" value="%s"/></td><td><label> %s</label></td></tr>', $options->getPluginSlug (), $options->getPluginName () );
			}
		}
		print '</table>';
		print '</fieldset>';
		
		// Wizard Step 1
		printf ( '<h5>%s</h5>', _x ( 'Sender Details', 'Wizard Step Title', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		printf ( '<legend>%s</legend>', _x ( 'Who is the mail coming from?', 'Wizard Step Title', Postman::TEXT_DOMAIN ) );
		printf ( '<p>%s</p>', __ ( 'Enter the email address and name you\'d like to send mail as.', Postman::TEXT_DOMAIN ) );
		printf ( '<p>%s</p>', __ ( 'Please note that to prevent abuse, many email services will <em>not</em> let you send from an email address other than the one you authenticate with.', Postman::TEXT_DOMAIN ) );
		printf ( '<label for="postman_options[sender_email]">%s</label>', __ ( 'Email Address', Postman::TEXT_DOMAIN ) );
		print $this->settingsRegistry->from_email_callback ();
		print '<br/>';
		printf ( '<label for="postman_options[sender_name]">%s</label>', __ ( 'Name', Postman::TEXT_DOMAIN ) );
		print $this->settingsRegistry->sender_name_callback ();
		print '</fieldset>';
		
		// Wizard Step 2
		printf ( '<h5>%s</h5>', __ ( 'Outgoing Mail Server Hostname', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		foreach ( PostmanTransportRegistry::getInstance ()->getTransports () as $transport ) {
			$transport->printWizardMailServerHostnameStep ();
		}
		print '</fieldset>';
		
		// Wizard Step 3
		printf ( '<h5>%s</h5>', __ ( 'Connectivity Test', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		printf ( '<legend>%s</legend>', __ ( 'How will the connection to the mail server be established?', Postman::TEXT_DOMAIN ) );
		printf ( '<p>%s</p>', __ ( 'Your connection settings depend on what your email service provider offers, and what your WordPress host allows.', Postman::TEXT_DOMAIN ) );
		printf ( '<p id="connectivity_test_status">%s: <span id="port_test_status">%s</span></p>', __ ( 'Connectivity Test', Postman::TEXT_DOMAIN ), _x ( 'Ready', 'TCP Port Test Status', Postman::TEXT_DOMAIN ) );
		printf ( '<p class="ajax-loader" style="display:none"><img src="%s"/></p>', plugins_url ( 'postman-smtp/style/ajax-loader.gif' ) );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]">', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::TRANSPORT_TYPE );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]">', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::PORT );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]">', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::SECURITY_TYPE );
		printf ( '<input type="hidden" id="input_%2$s" name="%1$s[%2$s]">', PostmanOptions::POSTMAN_OPTIONS, PostmanOptions::AUTHENTICATION_TYPE );
		print '<p id="wizard_recommendation"></p>';
		/* Translators: Where %1$s is the socket identifier and %2$s is the authentication type */
		printf ( '<p class="user_override" style="display:none"><label><span>%s:</span></label> <table id="user_socket_override" class="user_override"></table></p>', _x ( 'Socket', 'A socket is the network term for host and port together', Postman::TEXT_DOMAIN ) );
		printf ( '<p class="user_override" style="display:none"><label><span>%s:</span></label> <table id="user_auth_override" class="user_override"></table></p>', __ ( 'Authentication', Postman::TEXT_DOMAIN ) );
		print ('<p><span id="smtp_mitm" style="display:none; background-color:yellow"></span></p>') ;
		$warning = __ ( 'Warning', Postman::TEXT_DOMAIN );
		$clearCredentialsWarning = __ ( 'This configuration option will send your authorization credentials in the clear.', Postman::TEXT_DOMAIN );
		printf ( '<p id="smtp_not_secure" style="display:none"><span style="background-color:yellow">%s: %s</span></p>', $warning, $clearCredentialsWarning );
		print '</fieldset>';
		
		// Wizard Step 4
		printf ( '<h5>%s</h5>', __ ( 'Authentication', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		printf ( '<legend>%s</legend>', __ ( 'How will you prove your identity to the mail server?', Postman::TEXT_DOMAIN ) );
		foreach ( PostmanTransportRegistry::getInstance ()->getTransports () as $transport ) {
			$transport->printWizardAuthenticationStep ();
		}
		print '</fieldset>';
		
		// Wizard Step 5
		printf ( '<h5>%s</h5>', _x ( 'Finish', 'The final step of the Wizard', Postman::TEXT_DOMAIN ) );
		print '<fieldset>';
		printf ( '<legend>%s</legend>', _x ( 'You\'re Done!', 'Wizard Step Title', Postman::TEXT_DOMAIN ) );
		print '<section>';
		printf ( '<p>%s</p>', __ ( 'Click Finish to save these settings, then:', Postman::TEXT_DOMAIN ) );
		print '<ul style="margin-left: 20px">';
		printf ( '<li class="wizard-auth-oauth2">%s</li>', __ ( 'Grant permission with the Email Provider for Postman to send email and', Postman::TEXT_DOMAIN ) );
		printf ( '<li>%s</li>', __ ( 'Send yourself a Test Email to make sure everything is working!', Postman::TEXT_DOMAIN ) );
		print '</ul>';
		print '</section>';
		print '</fieldset>';
		print '</form>';
		print '</div>';
	}
}

/**
 *
 * @author jasonhendriks
 *        
 */
class PostmanGetHostnameByEmailAjaxController extends PostmanAbstractAjaxHandler {
	const IS_GOOGLE_PARAMETER = 'is_google';
	function __construct() {
		parent::__construct ();
		PostmanUtils::registerAjaxHandler ( 'postman_check_email', $this, 'getAjaxHostnameByEmail' );
	}
	/**
	 * This Ajax function retrieves the smtp hostname for a give e-mail address
	 */
	function getAjaxHostnameByEmail() {
		$goDaddyHostDetected = $this->getBooleanRequestParameter ( 'go_daddy' );
		$email = $this->getRequestParameter ( 'email' );
		$d = new PostmanSmtpDiscovery ( $email );
		$smtp = $d->getSmtpServer ();
		$this->logger->debug ( 'given email ' . $email . ', smtp server is ' . $smtp );
		$this->logger->trace ( $d );
		if ($goDaddyHostDetected && ! $d->isGoogle) {
			// override with the GoDaddy SMTP server
			$smtp = 'relay-hosting.secureserver.net';
			$this->logger->debug ( 'detected GoDaddy SMTP server, smtp server is ' . $smtp );
		}
		$response = array (
				'hostname' => $smtp,
				self::IS_GOOGLE_PARAMETER => $d->isGoogle,
				'is_go_daddy' => $d->isGoDaddy,
				'is_well_known' => $d->isWellKnownDomain 
		);
		$this->logger->trace ( $response );
		wp_send_json_success ( $response );
	}
}
class PostmanManageConfigurationAjaxHandler extends PostmanAbstractAjaxHandler {
	function __construct() {
		parent::__construct ();
		PostmanUtils::registerAjaxHandler ( 'manual_config', $this, 'getManualConfigurationViaAjax' );
		PostmanUtils::registerAjaxHandler ( 'get_wizard_configuration_options', $this, 'getWizardConfigurationViaAjax' );
	}
	
	/**
	 * Handle a Advanced Configuration request with Ajax
	 *
	 * @throws Exception
	 */
	function getManualConfigurationViaAjax() {
		$queryTransportType = $this->getTransportTypeFromRequest ();
		$queryAuthType = $this->getAuthenticationTypeFromRequest ();
		$queryHostname = $this->getHostnameFromRequest ();
		
		// the outgoing server hostname is only required for the SMTP Transport
		// the Gmail API transport doesn't use an SMTP server
		$transport = PostmanTransportRegistry::getInstance ()->getTransport ( $queryTransportType );
		if (! $transport) {
			throw new Exception ( 'Unable to find transport ' . $queryTransportType );
		}
		
		// create the response
		$response = $transport->populateConfiguration ( $queryHostname );
		$response ['referer'] = 'manual_config';
		
		// set the display_auth to oauth2 if the transport needs it
		if ($transport->isOAuthUsed ( $queryAuthType )) {
			$response ['display_auth'] = 'oauth2';
			$this->logger->debug ( 'ajaxRedirectUrl answer display_auth:' . $response ['display_auth'] );
		}
		$this->logger->trace ( $response );
		wp_send_json_success ( $response );
	}
	
	/**
	 * Once the Port Tests have run, the results are analyzed.
	 * The Transport place bids on the sockets and highest bid becomes the recommended
	 * The UI response is built so the user may choose a different socket with different options.
	 */
	function getWizardConfigurationViaAjax() {
		$this->logger->debug ( 'in getWizardConfiguration' );
		$originalSmtpServer = $this->getRequestParameter ( 'original_smtp_server' );
		$queryHostData = $this->getHostDataFromRequest ();
		$sockets = array ();
		foreach ( $queryHostData as $id => $datum ) {
			array_push ( $sockets, new PostmanWizardSocket ( $datum ) );
		}
		$this->logger->error ( $sockets );
		$userPortOverride = $this->getUserPortOverride ();
		$userAuthOverride = $this->getUserAuthOverride ();
		
		// determine a configuration recommendation
		$winningRecommendation = $this->getWinningRecommendation ( $sockets, $userPortOverride, $userAuthOverride, $originalSmtpServer );
		if ($this->logger->isTrace ()) {
			$this->logger->trace ( 'winning recommendation:' );
			$this->logger->trace ( $winningRecommendation );
		}
		
		// create the reponse
		$response = array ();
		$configuration = array ();
		$response ['referer'] = 'wizard';
		if (isset ( $userPortOverride ) || isset ( $userAuthOverride )) {
			$configuration ['user_override'] = true;
		}
		
		if (isset ( $winningRecommendation )) {
			
			// create an appropriate (theoretical) transport
			$transport = PostmanTransportRegistry::getInstance ()->getTransport ( $winningRecommendation ['transport'] );
			
			// create user override menu
			$overrideMenu = $this->createOverrideMenus ( $sockets, $winningRecommendation, $userPortOverride, $userAuthOverride );
			if ($this->logger->isTrace ()) {
				$this->logger->trace ( 'override menu:' );
				$this->logger->trace ( $overrideMenu );
			}
			
			$queryHostName = $winningRecommendation ['hostname'];
			if ($this->logger->isDebug ()) {
				$this->logger->debug ( 'Getting scribe for ' . $queryHostName );
			}
			$generalConfig1 = $transport->populateConfiguration ( $queryHostName );
			$generalConfig2 = $transport->populateConfigurationFromRecommendation ( $winningRecommendation );
			$configuration = array_merge ( $configuration, $generalConfig1, $generalConfig2 );
			$response ['override_menu'] = $overrideMenu;
			$response ['configuration'] = $configuration;
			if ($this->logger->isTrace ()) {
				$this->logger->trace ( 'configuration:' );
				$this->logger->trace ( $configuration );
				$this->logger->trace ( 'response:' );
				$this->logger->trace ( $response );
			}
			wp_send_json_success ( $response );
		} else {
			/* translators: where %s is the URL to the Connectivity Test page */
			$configuration ['message'] = sprintf ( __ ( 'Postman can\'t find any way to send mail on your system. Run a <a href="%s">connectivity test</a>.', Postman::TEXT_DOMAIN ), PostmanViewController::getPageUrl ( PostmanViewController::PORT_TEST_SLUG ) );
			$response ['configuration'] = $configuration;
			if ($this->logger->isTrace ()) {
				$this->logger->trace ( 'configuration:' );
				$this->logger->trace ( $configuration );
			}
			wp_send_json_error ( $response );
		}
	}
	
	/**
	 * // for each successful host/port combination
	 * // ask a transport if they support it, and if they do at what priority is it
	 * // configure for the highest priority you find
	 *
	 * @param unknown $queryHostData        	
	 * @return unknown
	 */
	private function getWinningRecommendation($sockets, $userSocketOverride, $userAuthOverride, $originalSmtpServer) {
		foreach ( $sockets as $socket ) {
			$winningRecommendation = $this->getWin ( $socket, $userSocketOverride, $userAuthOverride, $originalSmtpServer );
			$this->logger->error ( $socket->label );
		}
		return $winningRecommendation;
	}
	
	/**
	 *
	 * @param PostmanSocket $socket        	
	 * @param unknown $userSocketOverride        	
	 * @param unknown $userAuthOverride        	
	 * @param unknown $originalSmtpServer        	
	 * @return Ambigous <NULL, unknown, string>
	 */
	private function getWin(PostmanWizardSocket $socket, $userSocketOverride, $userAuthOverride, $originalSmtpServer) {
		static $recommendationPriority = - 1;
		static $winningRecommendation = null;
		$available = $socket->success;
		if ($available) {
			$this->logger->debug ( sprintf ( 'Asking for judgement on %s:%s', $socket->hostname, $socket->port ) );
			$recommendation = PostmanTransportRegistry::getInstance ()->getRecommendation ( $socket, $userAuthOverride, $originalSmtpServer );
			$recommendationId = sprintf ( '%s_%s', $socket->hostname, $socket->port );
			$recommendation ['id'] = $recommendationId;
			$this->logger->debug ( sprintf ( 'Got a recommendation: [%d] %s', $recommendation ['priority'], $recommendationId ) );
			if (isset ( $userSocketOverride )) {
				if ($recommendationId == $userSocketOverride) {
					$winningRecommendation = $recommendation;
					$this->logger->debug ( sprintf ( 'User chosen socket %s is the winner', $recommendationId ) );
				}
			} elseif ($recommendation && $recommendation ['priority'] > $recommendationPriority) {
				$recommendationPriority = $recommendation ['priority'];
				$winningRecommendation = $recommendation;
			}
			$socket->label = $recommendation ['label'];
		}
		return $winningRecommendation;
	}
	
	/**
	 *
	 * @param unknown $queryHostData        	
	 * @return multitype:
	 */
	private function createOverrideMenus($sockets, $winningRecommendation, $userSocketOverride, $userAuthOverride) {
		$overrideMenu = array ();
		foreach ( $sockets as $socket ) {
			$overrideItem = $this->createOverrideMenu ( $socket, $winningRecommendation, $userSocketOverride, $userAuthOverride );
			if ($overrideItem != null) {
				$overrideMenu [$socket->id] = $overrideItem;
			}
		}
		
		// sort
		krsort ( $overrideMenu );
		$sortedMenu = array ();
		foreach ( $overrideMenu as $menu ) {
			array_push ( $sortedMenu, $menu );
		}
		
		return $sortedMenu;
	}
	
	/**
	 *
	 * @param PostmanWizardSocket $socket        	
	 * @param unknown $winningRecommendation        	
	 * @param unknown $userSocketOverride        	
	 * @param unknown $userAuthOverride        	
	 */
	private function createOverrideMenu(PostmanWizardSocket $socket, $winningRecommendation, $userSocketOverride, $userAuthOverride) {
		if ($socket->success) {
			$transport = PostmanTransportRegistry::getInstance ()->getTransport ( $socket->transport );
			$this->logger->debug ( sprintf ( 'Transport %s is building the override menu for socket', $transport->getSlug () ) );
			$overrideItem = $transport->createOverrideMenu ( $socket, $winningRecommendation, $userSocketOverride, $userAuthOverride );
			return $overrideItem;
		}
		return null;
	}
	
	/**
	 */
	private function getTransportTypeFromRequest() {
		return $this->getRequestParameter ( 'transport' );
	}
	
	/**
	 */
	private function getHostnameFromRequest() {
		return $this->getRequestParameter ( 'hostname' );
	}
	
	/**
	 */
	private function getAuthenticationTypeFromRequest() {
		return $this->getRequestParameter ( 'auth_type' );
	}
	
	/**
	 */
	private function getHostDataFromRequest() {
		return $this->getRequestParameter ( 'host_data' );
	}
	
	/**
	 */
	private function getUserPortOverride() {
		return $this->getRequestParameter ( 'user_port_override' );
	}
	
	/**
	 */
	private function getUserAuthOverride() {
		return $this->getRequestParameter ( 'user_auth_override' );
	}
}
class PostmanImportConfigurationAjaxController extends PostmanAbstractAjaxHandler {
	private $options;
	/**
	 * Constructor
	 *
	 * @param PostmanOptions $options        	
	 */
	function __construct(PostmanOptions $options) {
		parent::__construct ();
		$this->options = $options;
		PostmanUtils::registerAjaxHandler ( 'import_configuration', $this, 'getConfigurationFromExternalPluginViaAjax' );
	}
	
	/**
	 * This function extracts configuration details form a competing SMTP plugin
	 * and pushes them into the Postman configuration screen.
	 */
	function getConfigurationFromExternalPluginViaAjax() {
		$importableConfiguration = new PostmanImportableConfiguration ();
		$plugin = $this->getRequestParameter ( 'plugin' );
		$this->logger->debug ( 'Looking for config=' . $plugin );
		foreach ( $importableConfiguration->getAvailableOptions () as $this->options ) {
			if ($this->options->getPluginSlug () == $plugin) {
				$this->logger->debug ( 'Sending configuration response' );
				$response = array (
						PostmanOptions::MESSAGE_SENDER_EMAIL => $this->options->getMessageSenderEmail (),
						PostmanOptions::MESSAGE_SENDER_NAME => $this->options->getMessageSenderName (),
						PostmanOptions::HOSTNAME => $this->options->getHostname (),
						PostmanOptions::PORT => $this->options->getPort (),
						PostmanOptions::AUTHENTICATION_TYPE => $this->options->getAuthenticationType (),
						PostmanOptions::SECURITY_TYPE => $this->options->getEncryptionType (),
						PostmanOptions::BASIC_AUTH_USERNAME => $this->options->getUsername (),
						PostmanOptions::BASIC_AUTH_PASSWORD => $this->options->getPassword (),
						'success' => true 
				);
				break;
			}
		}
		if (! isset ( $response )) {
			$response = array (
					'success' => false 
			);
		}
		wp_send_json ( $response );
	}
}
