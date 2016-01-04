<?php

/**
 * Postman execution begins here:
 * - the default Postman transports are loaded
 * - the wp_mail function is overloaded, whether or not Postman has been properly configured
 * - the custom post types are created, in case they are needed for the WordPress importer
 * - the database upgrade is run, if there is a version mismatch
 * - the shortcode is created
 * - the admin screens are loaded, the Message Handler created, if the current user can manage Postman
 * - on activation/deactivation, the custom capability is added to/removed from the administrator role
 * - a custom str_getcsv function is added to the global namespace, if it is missing
 *
 * @author jasonhendriks
 * @copyright Jan 16, 2015
 */
class Postman {
	
	//
	const ADMINISTRATOR_ROLE_NAME = 'administrator';
	const MANAGE_POSTMAN_CAPABILITY_NAME = 'manage_postman_smtp';
	const TEXT_DOMAIN = 'postman-smtp';
	
	//
	private $logger;
	private $messageHandler;
	private $wpMailBinder;
	private $pluginData;
	private $rootPluginFilenameAndPath;
	
	/**
	 * The constructor
	 *
	 * @param unknown $rootPluginFilenameAndPath
	 *        	- the __FILE__ of the caller
	 */
	public function __construct($rootPluginFilenameAndPath, $version) {
		assert ( ! empty ( $rootPluginFilenameAndPath ) );
		assert ( ! empty ( $version ) );
		$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
		
		// load the dependencies
		require_once 'PostmanOptions.php';
		require_once 'PostmanState.php';
		require_once 'PostmanLogger.php';
		require_once 'PostmanUtils.php';
		require_once 'Postman-Mail/PostmanTransportRegistry.php';
		require_once 'Postman-Mail/PostmanDefaultModuleTransport.php';
		require_once 'Postman-Mail/PostmanSmtpModuleTransport.php';
		require_once 'Postman-Mail/PostmanGmailApiModuleTransport.php';
		require_once 'Postman-Mail/PostmanMandrillTransport.php';
		require_once 'Postman-Mail/PostmanSendGridTransport.php';
		require_once 'PostmanOAuthToken.php';
		require_once 'PostmanWpMailBinder.php';
		require_once 'PostmanConfigTextHelper.php';
		require_once 'Postman-Email-Log/PostmanEmailLogPostType.php';
		require_once 'Postman-Mail/PostmanMyMailConnector.php';
		
		// get plugin metadata - alternative to get_plugin_data
		$this->pluginData = array (
				'name' => __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ),
				'version' => $version 
		);
		
		// register the plugin metadata filter (part of the Postman API)
		add_filter ( 'postman_get_plugin_metadata', array (
				$this,
				'getPluginMetaData' 
		) );
		
		// create an instance of the logger
		$this->logger = new PostmanLogger ( get_class ( $this ) );
		if ($this->logger->isDebug ()) {
			$this->logger->debug ( sprintf ( '%1$s v%2$s starting', $this->pluginData ['name'], $this->pluginData ['version'] ) );
		}
		
		if (isset ( $_REQUEST ['page'] ) && $this->logger->isTrace ()) {
			$this->logger->trace ( 'Current page: ' . $_REQUEST ['page'] );
		}
		
		// register the email transports
		$this->registerTransports ( $rootPluginFilenameAndPath );
		
		// store an instance of the WpMailBinder
		$this->wpMailBinder = PostmanWpMailBinder::getInstance ();
		
		// bind to wp_mail - this has to happen before the "init" action
		// this design allows other plugins to register a Postman transport and call bind()
		// bind may be called more than once
		$this->wpMailBinder->bind ();
		
		// registers the custom post type for all callers
		PostmanEmailLogPostType::automaticallyCreatePostType ();
		
		// run the DatastoreUpgrader any time there is a version mismatch
		if (PostmanState::getInstance ()->getVersion () != $this->pluginData ['version']) {
			// manually trigger the activation hook
			if ($this->logger->isInfo ()) {
				$this->logger->info ( sprintf ( "Upgrading datastore from version %s to %s", PostmanState::getInstance ()->getVersion (), $this->pluginData ['version'] ) );
			}
			require_once 'PostmanInstaller.php';
			$upgrader = new PostmanInstaller ();
			$upgrader->activatePostman ();
		}
		
		// MyMail integration
		new PostmanMyMailConnector ( $rootPluginFilenameAndPath );
		
		// register the shortcode handler on the add_shortcode event
		add_shortcode ( 'postman-version', array (
				$this,
				'version_shortcode' 
		) );
		
		// hook on the plugins_loaded event
		add_action ( 'plugins_loaded', array (
				$this,
				'on_plugins_loaded' 
		) );
		
		// hook on the wp_loaded event
		add_action ( 'wp_loaded', array (
				$this,
				'on_wp_loaded' 
		) );
		
		// hook on the acivation event
		register_activation_hook ( $rootPluginFilenameAndPath, array (
				$this,
				'on_activation' 
		) );
		
		// hook on the deactivation event
		register_deactivation_hook ( $rootPluginFilenameAndPath, array (
				$this,
				'on_deactivation' 
		) );
	}
	
	/**
	 * Functions to execute on the plugins_loaded event
	 *
	 * "After active plugins and pluggable functions are loaded"
	 * ref: http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_a_Typical_Request
	 */
	public function on_plugins_loaded() {
		// load the text domain
		$this->loadTextDomain ();
		
		// register the setup_admin function on plugins_loaded because we need to call
		// current_user_can to verify the capability of the current user
		if (PostmanUtils::isAdmin () && is_admin ()) {
			$this->setup_admin ();
		}
	}
	
	/**
	 * Functions to execute on the wp_loaded event
	 *
	 * "After WordPress is fully loaded"
	 * ref: http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_a_Typical_Request
	 */
	public function on_wp_loaded() {
		// register the check for configuration errors on the wp_loaded hook,
		// because we want it to run after the OAuth Grant Code check on the init hook
		$this->check_for_configuration_errors ();
	}
	
	/**
	 * Functions to execute on the register_activation_hook
	 * ref: https://codex.wordpress.org/Function_Reference/register_activation_hook
	 */
	public function on_activation() {
		if ($this->logger->isInfo ()) {
			$this->logger->info ( 'Activating plugin' );
		}
		require_once 'PostmanInstaller.php';
		$upgrader = new PostmanInstaller ();
		$upgrader->activatePostman ();
	}
	
	/**
	 * Functions to execute on the register_deactivation_hook
	 * ref: https://codex.wordpress.org/Function_Reference/register_deactivation_hook
	 */
	public function on_deactivation() {
		if ($this->logger->isInfo ()) {
			$this->logger->info ( 'Deactivating plugin' );
		}
		require_once 'PostmanInstaller.php';
		$upgrader = new PostmanInstaller ();
		$upgrader->deactivatePostman ();
	}
	
	/**
	 * If the user is on the WordPress Admin page, creates the Admin screens
	 */
	public function setup_admin() {
		$this->logger->debug ( 'Admin start-up sequence' );
		
		$options = PostmanOptions::getInstance ();
		$authToken = PostmanOAuthToken::getInstance ();
		$rootPluginFilenameAndPath = $this->rootPluginFilenameAndPath;
		
		// load the dependencies
		require_once 'PostmanMessageHandler.php';
		require_once 'PostmanAdminController.php';
		require_once 'Postman-Controller/PostmanDashboardWidgetController.php';
		require_once 'Postman-Controller/PostmanAdminPointer.php';
		require_once 'Postman-Email-Log/PostmanEmailLogController.php';
		require_once 'Postman-Connectivity-Test/PostmanConnectivityTestController.php';
		require_once 'Postman-Configuration/PostmanConfigurationController.php';
		require_once 'Postman-Send-Test-Email/PostmanSendTestEmailController.php';
		require_once 'Postman-Diagnostic-Test/PostmanDiagnosticTestController.php';
		
		// create and store an instance of the MessageHandler
		$this->messageHandler = new PostmanMessageHandler ();
		
		// create the Admin Controllers
		new PostmanDashboardWidgetController ( $rootPluginFilenameAndPath, $options, $authToken, $this->wpMailBinder );
		new PostmanAdminController ( $rootPluginFilenameAndPath, $options, $authToken, $this->messageHandler, $this->wpMailBinder );
		new PostmanEmailLogController ( $rootPluginFilenameAndPath );
		new PostmanConnectivityTestController ( $rootPluginFilenameAndPath );
		new PostmanConfigurationController ( $rootPluginFilenameAndPath );
		new PostmanSendTestEmailController ( $rootPluginFilenameAndPath );
		new PostmanDiagnosticTestController ( $rootPluginFilenameAndPath );
		
		// register the Postman signature (only if we're on a postman admin screen) on the in_admin_footer event
		if (PostmanUtils::isCurrentPagePostmanAdmin ()) {
			add_action ( 'in_admin_footer', array (
					$this,
					'print_signature' 
			) );
		}
	}
	
	/**
	 * Check for configuration errors and displays messages to the user
	 */
	public function check_for_configuration_errors() {
		$options = PostmanOptions::getInstance ();
		$authToken = PostmanOAuthToken::getInstance ();
		
		// did Postman fail binding to wp_mail()?
		if ($this->wpMailBinder->isUnboundDueToException ()) {
			// this message gets printed on ANY WordPress admin page, as it's a fatal error that
			// may occur just by activating a new plugin
			
			// log the fatal message
			$this->logger->fatal ( 'Postman: wp_mail has been declared by another plugin or theme, so you won\'t be able to use Postman until the conflict is resolved.' );
			
			if (PostmanUtils::isAdmin () && is_admin ()) {
				// on any admin pages, show this error message
				
				// I noticed the wpMandrill and SendGrid plugins have the exact same error message here
				// I've adopted their error message as well, for shits and giggles .... :D
				$message = __ ( 'Postman: wp_mail has been declared by another plugin or theme, so you won\'t be able to use Postman until the conflict is resolved.', Postman::TEXT_DOMAIN );
				$this->messageHandler->addError ( $message );
			}
		} else {
			$transport = PostmanTransportRegistry::getInstance ()->getCurrentTransport ();
			$scribe = $transport->getScribe ();
			
			$virgin = $options->isNew ();
			if (! $transport->isConfiguredAndReady ()) {
				// if the configuration is broken, and the user has started to configure the plugin
				// show this error message
				$messages = $transport->getConfigurationMessages ();
				foreach ( $messages as $message ) {
					if ($message) {
						// log the warning message
						$this->logger->warn ( sprintf ( '%s Transport has a configuration problem: %s', $transport->getName (), $message ) );
						
						if (PostmanUtils::isAdmin () && PostmanUtils::isCurrentPagePostmanAdmin ()) {
							// on pages that are Postman admin pages only, show this error message
							$this->messageHandler->addError ( $message );
						}
					}
				}
			}
			
			// on pages that are NOT Postman admin pages only, show this error message
			if (PostmanUtils::isAdmin () && ! PostmanUtils::isCurrentPagePostmanAdmin () && ! $transport->isConfiguredAndReady ()) {
				// on pages that are *NOT* Postman admin pages only....
				// if the configuration is broken show this error message
				add_action ( 'admin_notices', Array (
						$this,
						'display_configuration_required_warning' 
				) );
			}
		}
	}
	
	/**
	 * Returns the plugin version number and name
	 * Part of the Postman API
	 *
	 * @return multitype:unknown NULL
	 */
	public function getPluginMetaData() {
		// get plugin metadata
		return $this->pluginData;
	}
	
	/**
	 * This is the general message that Postman requires configuration, to warn users who think
	 * the plugin is ready-to-go as soon as it is activated.
	 * This message only goes away once the plugin is configured.
	 */
	public function display_configuration_required_warning() {
		if (PostmanUtils::isAdmin ()) {
			if ($this->logger->isDebug ()) {
				$this->logger->debug ( 'Displaying configuration required warning' );
			}
			$message = sprintf ( PostmanTransportRegistry::getInstance ()->getReadyMessage () );
			$goToSettings = sprintf ( '<a href="%s">%s</a>', PostmanUtils::getSettingsPageUrl (), __ ( 'Settings', Postman::TEXT_DOMAIN ) );
			$goToEmailLog = sprintf ( '%s', _x ( 'Email Log', 'The log of Emails that have been delivered', Postman::TEXT_DOMAIN ) );
			if (PostmanOptions::getInstance ()->isMailLoggingEnabled ()) {
				$goToEmailLog = sprintf ( '<a href="%s">%s</a>', PostmanUtils::getEmailLogPageUrl (), $goToEmailLog );
			}
			$message .= (sprintf ( ' %s | %s', $goToEmailLog, $goToSettings ));
			;
			$this->messageHandler->printMessage ( $message, PostmanMessageHandler::WARNING_CLASS );
		}
	}
	
	/**
	 * Register the email transports.
	 *
	 * The Gmail API used to be a separate plugin which was registered when that plugin
	 * was loaded. But now both the SMTP, Gmail API and other transports are registered here.
	 *
	 * @param unknown $pluginData        	
	 */
	private function registerTransports($rootPluginFilenameAndPath) {
		PostmanTransportRegistry::getInstance ()->registerTransport ( new PostmanDefaultModuleTransport ( $rootPluginFilenameAndPath ) );
		PostmanTransportRegistry::getInstance ()->registerTransport ( new PostmanSmtpModuleTransport ( $rootPluginFilenameAndPath ) );
		PostmanTransportRegistry::getInstance ()->registerTransport ( new PostmanGmailApiModuleTransport ( $rootPluginFilenameAndPath ) );
		PostmanTransportRegistry::getInstance ()->registerTransport ( new PostmanMandrillTransport ( $rootPluginFilenameAndPath ) );
		PostmanTransportRegistry::getInstance ()->registerTransport ( new PostmanSendGridTransport ( $rootPluginFilenameAndPath ) );
	}
	
	/**
	 * Print the Postman signature on the bottom of the page
	 *
	 * http://striderweb.com/nerdaphernalia/2008/06/give-your-wordpress-plugin-credit/
	 */
	function print_signature() {
		printf ( '<a href="https://wordpress.org/plugins/postman-smtp/">%s</a> %s<br/>', $this->pluginData ['name'], $this->pluginData ['version'] );
	}
	
	/**
	 * Loads the appropriate language file
	 */
	private function loadTextDomain() {
		// had to hardcode the third parameter, Relative path to WP_PLUGIN_DIR,
		// because __FILE__ returns the wrong path if the plugin is installed as a symlink
		$shortLocale = substr ( get_locale (), 0, 2 );
		if ($shortLocale != 'en') {
			$langDir = 'postman-smtp/Postman/languages';
			$success = load_plugin_textdomain ( Postman::TEXT_DOMAIN, false, $langDir );
			if ($this->logger->isDebug ()) {
				if ($success) {
					$this->logger->debug ( sprintf ( 'local translation file loaded for locale=%s', get_locale () ) );
				} else {
					$this->logger->debug ( sprintf ( 'failed to load local translation file: locale=%s file=%s/%s-%s.mo', get_locale (), $langDir, Postman::TEXT_DOMAIN, get_locale () ) );
				}
			}
		}
	}
	
	/**
	 * Shortcode to return the current plugin version.
	 *
	 * From http://code.garyjones.co.uk/get-wordpress-plugin-version/
	 *
	 * @return string Plugin version
	 */
	function version_shortcode() {
		return $this->pluginData ['version'];
	}
}

if (! function_exists ( 'str_getcsv' )) {
	/**
	 * PHP version less than 5.3 don't have str_getcsv natively.
	 *
	 * @param unknown $string        	
	 * @return multitype:
	 */
	function str_getcsv($string) {
		$logger = new PostmanLogger ( 'postman-common-functions' );
		if ($logger->isDebug ()) {
			$logger->debug ( 'Using custom str_getcsv' );
		}
		return PostmanUtils::postman_strgetcsv_impl ( $string );
	}
}

