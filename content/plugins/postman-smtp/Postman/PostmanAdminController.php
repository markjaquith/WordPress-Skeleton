<?php
if (! class_exists ( "PostmanAdminController" )) {
	
	require_once 'PostmanOptions.php';
	require_once 'PostmanState.php';
	require_once 'PostmanState.php';
	require_once 'PostmanOAuthToken.php';
	require_once 'Postman-Connectivity-Test/Postman-PortTest.php';
	require_once 'Postman-Configuration/PostmanSmtpDiscovery.php';
	require_once 'PostmanInputSanitizer.php';
	require_once 'Postman-Configuration/PostmanImportableConfiguration.php';
	require_once 'PostmanConfigTextHelper.php';
	require_once 'PostmanAjaxController.php';
	require_once 'PostmanViewController.php';
	require_once 'PostmanPreRequisitesCheck.php';
	require_once 'Postman-Auth/PostmanAuthenticationManagerFactory.php';
	
	//
	class PostmanAdminController {
		
		// this is the slug used in the URL
		const MANAGE_OPTIONS_PAGE_SLUG = 'postman/manage-options';
		
		// NONCE NAMES
		const PURGE_DATA_SLUG = 'postman_purge_data';
		const IMPORT_SETTINGS_SLUG = 'postman_import_settings';
		
		// The Postman Group is used for saving data, make sure it is globally unique
		const SETTINGS_GROUP_NAME = 'postman_group';
		
		// a database entry specifically for the form that sends test e-mail
		const TEST_OPTIONS = 'postman_test_options';
		const SMTP_OPTIONS = 'postman_smtp_options';
		const SMTP_SECTION = 'postman_smtp_section';
		const BASIC_AUTH_OPTIONS = 'postman_basic_auth_options';
		const BASIC_AUTH_SECTION = 'postman_basic_auth_section';
		const OAUTH_AUTH_OPTIONS = 'postman_oauth_options';
		const OAUTH_SECTION = 'postman_oauth_section';
		const MESSAGE_SENDER_OPTIONS = 'postman_message_sender_options';
		const MESSAGE_SENDER_SECTION = 'postman_message_sender_section';
		const MESSAGE_FROM_OPTIONS = 'postman_message_from_options';
		const MESSAGE_FROM_SECTION = 'postman_message_from_section';
		const MESSAGE_OPTIONS = 'postman_message_options';
		const MESSAGE_SECTION = 'postman_message_section';
		const MESSAGE_HEADERS_OPTIONS = 'postman_message_headers_options';
		const MESSAGE_HEADERS_SECTION = 'postman_message_headers_section';
		const NETWORK_OPTIONS = 'postman_network_options';
		const NETWORK_SECTION = 'postman_network_section';
		const LOGGING_OPTIONS = 'postman_logging_options';
		const LOGGING_SECTION = 'postman_logging_section';
		const MULTISITE_OPTIONS = 'postman_multisite_options';
		const MULTISITE_SECTION = 'postman_multisite_section';
		const ADVANCED_OPTIONS = 'postman_advanced_options';
		const ADVANCED_SECTION = 'postman_advanced_section';
		const EMAIL_VALIDATION_SECTION = 'postman_email_validation_section';
		const EMAIL_VALIDATION_OPTIONS = 'postman_email_validation_options';
		
		// slugs
		const POSTMAN_TEST_SLUG = 'postman-test';
		
		// logging
		private $logger;
		
		// Holds the values to be used in the fields callbacks
		private $rootPluginFilenameAndPath;
		private $options;
		private $authorizationToken;
		private $importableConfiguration;
		
		// helpers
		private $messageHandler;
		private $oauthScribe;
		private $wpMailBinder;
		
		/**
		 * Constructor
		 *
		 * @param unknown $rootPluginFilenameAndPath        	
		 * @param PostmanOptions $options        	
		 * @param PostmanOAuthToken $authorizationToken        	
		 * @param PostmanMessageHandler $messageHandler        	
		 * @param PostmanWpMailBinder $binder        	
		 */
		public function __construct($rootPluginFilenameAndPath, PostmanOptions $options, PostmanOAuthToken $authorizationToken, PostmanMessageHandler $messageHandler, PostmanWpMailBinder $binder) {
			assert ( ! empty ( $rootPluginFilenameAndPath ) );
			assert ( ! empty ( $options ) );
			assert ( ! empty ( $authorizationToken ) );
			assert ( ! empty ( $messageHandler ) );
			assert ( ! empty ( $binder ) );
			assert ( PostmanUtils::isAdmin () );
			assert ( is_admin () );
			
			$this->logger = new PostmanLogger ( get_class ( $this ) );
			$this->options = $options;
			$this->authorizationToken = $authorizationToken;
			$this->messageHandler = $messageHandler;
			$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
			$this->wpMailBinder = $binder;
			
			// check if the user saved data, and if validation was successful
			$session = PostmanSession::getInstance ();
			if ($session->isSetAction ()) {
				$this->logger->debug ( sprintf ( 'session action: %s', $session->getAction () ) );
			}
			if ($session->getAction () == PostmanInputSanitizer::VALIDATION_SUCCESS) {
				// unset the action
				$session->unsetAction ();
				// do a redirect on the init hook
				$this->registerInitFunction ( 'handleSuccessfulSave' );
				// add a saved message to be shown after the redirect
				$this->messageHandler->addMessage ( _x ( 'Settings saved.', 'The plugin successfully saved new settings.', Postman::TEXT_DOMAIN ) );
				return;
			} else {
				// unset the action in the failed case as well
				$session->unsetAction ();
			}
			
			// test to see if an OAuth authentication is in progress
			if ($session->isSetOauthInProgress ()) {
				// there is only a three minute window that Postman will expect a Grant Code, once Grant is clicked by the user
				$this->logger->debug ( 'Looking for grant code' );
				if (isset ( $_GET ['code'] )) {
					$this->logger->debug ( 'Found authorization grant code' );
					// queue the function that processes the incoming grant code
					$this->registerInitFunction ( 'handleAuthorizationGrant' );
					return;
				}
			}
			
			// continue to initialize the AdminController
			add_action ( 'init', array (
					$this,
					'on_init' 
			) );
			
			// Adds "Settings" link to the plugin action page
			add_filter ( 'plugin_action_links_' . plugin_basename ( $this->rootPluginFilenameAndPath ), array (
					$this,
					'postmanModifyLinksOnPluginsListPage' 
			) );
		}
		
		/**
		 * Functions to execute on the init event
		 *
		 * "Typically used by plugins to initialize. The current user is already authenticated by this time."
		 * ref: http://codex.wordpress.org/Plugin_API/Action_Reference#Actions_Run_During_a_Typical_Request
		 */
		public function on_init() {
			// only administrators should be able to trigger this
			if (PostmanUtils::isAdmin ()) {
				//
				$transport = PostmanTransportRegistry::getInstance ()->getCurrentTransport ();
				$this->oauthScribe = $transport->getScribe ();
				
				// register content handlers
				$viewController = new PostmanViewController ( $this->rootPluginFilenameAndPath, $this->options, $this->authorizationToken, $this->oauthScribe, $this );
				
				// register action handlers
				$this->registerAdminPostAction ( self::PURGE_DATA_SLUG, 'handlePurgeDataAction' );
				$this->registerAdminPostAction ( self::IMPORT_SETTINGS_SLUG, 'importSettingsAction' );
				$this->registerAdminPostAction ( PostmanUtils::REQUEST_OAUTH2_GRANT_SLUG, 'handleOAuthPermissionRequestAction' );
				
				if (PostmanUtils::isCurrentPagePostmanAdmin ()) {
					$this->checkPreRequisites ();
				}
			}
		}
		
		/**
		 * 
		 */
		private function checkPreRequisites() {
			$states = PostmanPreRequisitesCheck::getState ();
			foreach ( $states as $state ) {
				if (! $state ['ready']) {
					/* Translators: where %1$s is the name of the library */
					$message = sprintf ( __ ( 'This PHP installation requires the <b>%1$s</b> library.', Postman::TEXT_DOMAIN ), $state ['name'] );
					if ($state ['required']) {
						$this->messageHandler->addError ( $message );
					} else {
						// $this->messageHandler->addWarning ( $message );
					}
				}
			}
		}
		
		/**
		 *
		 * @param unknown $actionName        	
		 * @param unknown $callbackName        	
		 */
		private function registerInitFunction($callbackName) {
			$this->logger->debug ( 'Registering init function ' . $callbackName );
			add_action ( 'init', array (
					$this,
					$callbackName 
			) );
		}
		
		/**
		 * Registers actions posted by am HTML FORM with the WordPress 'action' parameter
		 *
		 * @param unknown $actionName        	
		 * @param unknown $callbankName        	
		 */
		private function registerAdminPostAction($actionName, $callbankName) {
			// $this->logger->debug ( 'Registering ' . $actionName . ' Action Post handler' );
			add_action ( 'admin_post_' . $actionName, array (
					$this,
					$callbankName 
			) );
		}
		
		/**
		 * Add "Settings" link to the plugin action page
		 *
		 * @param unknown $links        	
		 * @return multitype:
		 */
		public function postmanModifyLinksOnPluginsListPage($links) {
			// only administrators should be able to trigger this
			if (PostmanUtils::isAdmin ()) {
				$mylinks = array (
						sprintf ( '<a href="%s" class="postman_settings">%s</a>', PostmanUtils::getSettingsPageUrl (), __ ( 'Settings', Postman::TEXT_DOMAIN ) ) 
				);
				return array_merge ( $mylinks, $links );
			}
		}
		
		/**
		 * This function runs after a successful, error-free save
		 */
		public function handleSuccessfulSave() {
			// WordPress likes to keep GET parameters around for a long time
			// (something in the call to settings_fields() does this)
			// here we redirect after a successful save to clear those parameters
			PostmanUtils::redirect ( PostmanUtils::POSTMAN_HOME_PAGE_RELATIVE_URL );
		}
		
		/**
		 * This function handle the request to import plugin data
		 */
		public function importSettingsAction() {
			$this->logger->debug ( 'is wpnonce import-settings?' );
			$success = true;
			if (wp_verify_nonce ( $_REQUEST ['_wpnonce'], PostmanAdminController::IMPORT_SETTINGS_SLUG )) {
				$success = PostmanOptions::getInstance ()->import ( $_POST ['settings'] );
			} else {
				$success = false;
			}
			if (! $success) {
				$this->messageHandler->addError ( __ ( 'There was an error importing the data.', Postman::TEXT_DOMAIN ) );
				$this->logger->error ( 'There was an error importing the data' );
			}
			PostmanUtils::redirect ( PostmanUtils::POSTMAN_HOME_PAGE_RELATIVE_URL );
		}
		/**
		 * This function handle the request to purge plugin data
		 */
		public function handlePurgeDataAction() {
			$this->logger->debug ( 'is wpnonce purge-data?' );
			if (wp_verify_nonce ( $_REQUEST ['_wpnonce'], PostmanAdminController::PURGE_DATA_SLUG )) {
				$this->logger->debug ( 'Purging stored data' );
				delete_option ( PostmanOptions::POSTMAN_OPTIONS );
				delete_option ( PostmanOAuthToken::OPTIONS_NAME );
				delete_option ( PostmanAdminController::TEST_OPTIONS );
				$logPurger = new PostmanEmailLogPurger ();
				$logPurger->removeAll ();
				$this->messageHandler->addMessage ( __ ( 'Plugin data was removed.', Postman::TEXT_DOMAIN ) );
				PostmanUtils::redirect ( PostmanUtils::POSTMAN_HOME_PAGE_RELATIVE_URL );
			}
		}
		
		/**
		 * Handles the authorization grant
		 */
		function handleAuthorizationGrant() {
			$logger = $this->logger;
			$options = $this->options;
			$authorizationToken = $this->authorizationToken;
			$logger->debug ( 'Authorization in progress' );
			$transactionId = PostmanSession::getInstance ()->getOauthInProgress ();
			
			// begin transaction
			PostmanUtils::lock ();
			
			$authenticationManager = PostmanAuthenticationManagerFactory::getInstance ()->createAuthenticationManager ();
			try {
				if ($authenticationManager->processAuthorizationGrantCode ( $transactionId )) {
					$logger->debug ( 'Authorization successful' );
					// save to database
					$authorizationToken->save ();
					$this->messageHandler->addMessage ( __ ( 'The OAuth 2.0 authorization was successful. Ready to send e-mail.', Postman::TEXT_DOMAIN ) );
				} else {
					$this->messageHandler->addError ( __ ( 'Your email provider did not grant Postman permission. Try again.', Postman::TEXT_DOMAIN ) );
				}
			} catch ( PostmanStateIdMissingException $e ) {
				$this->messageHandler->addError ( __ ( 'The grant code from Google had no accompanying state and may be a forgery', Postman::TEXT_DOMAIN ) );
			} catch ( Exception $e ) {
				$logger->error ( 'Error: ' . get_class ( $e ) . ' code=' . $e->getCode () . ' message=' . $e->getMessage () );
				/* translators: %s is the error message */
				$this->messageHandler->addError ( sprintf ( __ ( 'Error authenticating with this Client ID. [%s]', Postman::TEXT_DOMAIN ), '<em>' . $e->getMessage () . '</em>' ) );
			}
			
			// clean-up
			PostmanUtils::unlock ();
			PostmanSession::getInstance ()->unsetOauthInProgress ();
			
			// redirect home
			PostmanUtils::redirect ( PostmanUtils::POSTMAN_HOME_PAGE_RELATIVE_URL );
		}
		
		/**
		 * This method is called when a user clicks on a "Request Permission from Google" link.
		 * This link will create a remote API call for Google and redirect the user from WordPress to Google.
		 * Google will redirect back to WordPress after the user responds.
		 */
		public function handleOAuthPermissionRequestAction() {
			$this->logger->debug ( 'handling OAuth Permission request' );
			$authenticationManager = PostmanAuthenticationManagerFactory::getInstance ()->createAuthenticationManager ();
			$transactionId = $authenticationManager->generateRequestTransactionId ();
			PostmanSession::getInstance ()->setOauthInProgress ( $transactionId );
			$authenticationManager->requestVerificationCode ( $transactionId );
		}
	}
}