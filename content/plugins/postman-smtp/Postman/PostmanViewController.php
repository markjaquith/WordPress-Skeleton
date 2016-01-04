<?php
if (! class_exists ( 'PostmanViewController' )) {
	class PostmanViewController {
		private $logger;
		private $rootPluginFilenameAndPath;
		private $options;
		private $authorizationToken;
		private $oauthScribe;
		private $importableConfiguration;
		private $adminController;
		const POSTMAN_MENU_SLUG = 'postman';
		
		// style sheets and scripts
		const POSTMAN_STYLE = 'postman_style';
		const JQUERY_SCRIPT = 'jquery';
		const POSTMAN_SCRIPT = 'postman_script';
		
		//
		const BACK_ARROW_SYMBOL = '&#11013;';
		
		/**
		 * Constructor
		 *
		 * @param PostmanOptions $options        	
		 * @param PostmanOAuthToken $authorizationToken        	
		 * @param PostmanConfigTextHelper $oauthScribe        	
		 */
		function __construct($rootPluginFilenameAndPath, PostmanOptions $options, PostmanOAuthToken $authorizationToken, PostmanConfigTextHelper $oauthScribe, PostmanAdminController $adminController) {
			$this->options = $options;
			$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
			$this->authorizationToken = $authorizationToken;
			$this->oauthScribe = $oauthScribe;
			$this->adminController = $adminController;
			$this->logger = new PostmanLogger ( get_class ( $this ) );
			PostmanUtils::registerAdminMenu ( $this, 'generateDefaultContent' );
			PostmanUtils::registerAdminMenu ( $this, 'addPurgeDataSubmenu' );
			
			// initialize the scripts, stylesheets and form fields
			add_action ( 'admin_init', array (
					$this,
					'registerStylesAndScripts' 
			), 0 );
		}
		public static function getPageUrl($slug) {
			return PostmanUtils::getPageUrl ( $slug );
		}
		
		/**
		 * Add options page
		 */
		public function generateDefaultContent() {
			// This page will be under "Settings"
			$pageTitle = sprintf ( __ ( '%s Setup', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) );
			$pluginName = __ ( 'Postman SMTP', Postman::TEXT_DOMAIN );
			$uniqueId = self::POSTMAN_MENU_SLUG;
			$pageOptions = array (
					$this,
					'outputDefaultContent' 
			);
			$mainPostmanSettingsPage = add_options_page ( $pageTitle, $pluginName, Postman::MANAGE_POSTMAN_CAPABILITY_NAME, $uniqueId, $pageOptions );
			// When the plugin options page is loaded, also load the stylesheet
			add_action ( 'admin_print_styles-' . $mainPostmanSettingsPage, array (
					$this,
					'enqueueHomeScreenStylesheet' 
			) );
		}
		function enqueueHomeScreenStylesheet() {
			wp_enqueue_style ( PostmanViewController::POSTMAN_STYLE );
			wp_enqueue_script ( 'postman_script' );
		}
		
		/**
		 * Register the Email Test screen
		 */
		public function addPurgeDataSubmenu() {
			$page = add_submenu_page ( null, sprintf ( __ ( '%s Setup', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ), Postman::MANAGE_POSTMAN_CAPABILITY_NAME, PostmanAdminController::MANAGE_OPTIONS_PAGE_SLUG, array (
					$this,
					'outputPurgeDataContent' 
			) );
			// When the plugin options page is loaded, also load the stylesheet
			add_action ( 'admin_print_styles-' . $page, array (
					$this,
					'enqueueHomeScreenStylesheet' 
			) );
		}
		
		/**
		 * Register and add settings
		 */
		public function registerStylesAndScripts() {
			if ($this->logger->isTrace ()) {
				$this->logger->trace ( 'registerStylesAndScripts()' );
			}
			// register the stylesheet and javascript external resources
			$pluginData = apply_filters ( 'postman_get_plugin_metadata', null );
			wp_register_style ( PostmanViewController::POSTMAN_STYLE, plugins_url ( 'style/postman.css', $this->rootPluginFilenameAndPath ), null, $pluginData ['version'] );
			wp_register_style ( 'jquery_ui_style', plugins_url ( 'style/jquery-steps/jquery-ui.css', $this->rootPluginFilenameAndPath ), PostmanViewController::POSTMAN_STYLE, '1.1.0' );
			wp_register_style ( 'jquery_steps_style', plugins_url ( 'style/jquery-steps/jquery.steps.css', $this->rootPluginFilenameAndPath ), PostmanViewController::POSTMAN_STYLE, '1.1.0' );
			
			wp_register_script ( PostmanViewController::POSTMAN_SCRIPT, plugins_url ( 'script/postman.js', $this->rootPluginFilenameAndPath ), array (
					PostmanViewController::JQUERY_SCRIPT 
			), $pluginData ['version'] );
			wp_register_script ( 'sprintf', plugins_url ( 'script/sprintf/sprintf.min.js', $this->rootPluginFilenameAndPath ), null, '1.0.2' );
			wp_register_script ( 'jquery_steps_script', plugins_url ( 'script/jquery-steps/jquery.steps.min.js', $this->rootPluginFilenameAndPath ), array (
					PostmanViewController::JQUERY_SCRIPT 
			), '1.1.0' );
			wp_register_script ( 'jquery_validation', plugins_url ( 'script/jquery-validate/jquery.validate.min.js', $this->rootPluginFilenameAndPath ), array (
					PostmanViewController::JQUERY_SCRIPT 
			), '1.13.1' );

			wp_localize_script ( PostmanViewController::POSTMAN_SCRIPT, 'postman_ajax_msg', array (
					'bad_response' => __ ( 'An unexpected error occurred', Postman::TEXT_DOMAIN ),
					'corrupt_response' => __ ( 'Unexpected PHP messages corrupted the Ajax response', Postman::TEXT_DOMAIN ) 
			) );
			
			wp_localize_script ( 'jquery_steps_script', 'steps_current_step', 'steps_current_step' );
			wp_localize_script ( 'jquery_steps_script', 'steps_pagination', 'steps_pagination' );
			wp_localize_script ( 'jquery_steps_script', 'steps_finish', _x ( 'Finish', 'Press this button to Finish this task', Postman::TEXT_DOMAIN ) );
			wp_localize_script ( 'jquery_steps_script', 'steps_next', _x ( 'Next', 'Press this button to go to the next step', Postman::TEXT_DOMAIN ) );
			wp_localize_script ( 'jquery_steps_script', 'steps_previous', _x ( 'Previous', 'Press this button to go to the previous step', Postman::TEXT_DOMAIN ) );
			wp_localize_script ( 'jquery_steps_script', 'steps_loading', 'steps_loading' );
		}
		
		/**
		 * Options page callback
		 */
		public function outputDefaultContent() {
			// Set class property
			print '<div class="wrap">';
			$this->displayTopNavigation ();
			if (! PostmanPreRequisitesCheck::isReady ()) {
				printf ( '<p><span style="color:red; padding:2px 0; font-size:1.1em">%s</span></p>', __ ( 'Postman is unable to run. Email delivery is being handled by WordPress (or another plugin).', Postman::TEXT_DOMAIN ) );
			} else {
				$statusMessage = PostmanTransportRegistry::getInstance ()->getReadyMessage ();
				if (PostmanTransportRegistry::getInstance ()->getActiveTransport ()->isConfiguredAndReady ()) {
					if ($this->options->getRunMode () != PostmanOptions::RUN_MODE_PRODUCTION) {
						printf ( '<p><span style="background-color:yellow">%s</span></p>', $statusMessage );
					} else {
						printf ( '<p><span style="color:green;padding:2px 0; font-size:1.1em">%s</span></p>', $statusMessage );
					}
				} else {
					printf ( '<p><span style="color:red; padding:2px 0; font-size:1.1em">%s</span></p>', $statusMessage );
				}
				$this->printDeliveryDetails ();
				/* translators: where %d is the number of emails delivered */
				print '<p style="margin:10px 10px"><span>';
				printf ( _n ( 'Postman has delivered <span style="color:green">%d</span> email.', 'Postman has delivered <span style="color:green">%d</span> emails.', PostmanState::getInstance ()->getSuccessfulDeliveries (), Postman::TEXT_DOMAIN ), PostmanState::getInstance ()->getSuccessfulDeliveries () );
				if ($this->options->isMailLoggingEnabled ()) {
					print ' ';
					printf ( __ ( 'The last %d email attempts are recorded <a href="%s">in the log</a>.', Postman::TEXT_DOMAIN ), PostmanOptions::getInstance ()->getMailLoggingMaxEntries (), PostmanUtils::getEmailLogPageUrl () );
				}
				print '</span></p>';
			}
			if ($this->options->isNew ()) {
				printf ( '<h3 style="padding-top:10px">%s</h3>', __ ( 'Thank-you for choosing Postman!', Postman::TEXT_DOMAIN ) );
				/* translators: where %s is the URL of the Setup Wizard */
				printf ( '<p><span>%s</span></p>', sprintf ( __ ( 'Let\'s get started! All users are strongly encouraged to <a href="%s">run the Setup Wizard</a>.', Postman::TEXT_DOMAIN ), $this->getPageUrl ( PostmanConfigurationController::CONFIGURATION_WIZARD_SLUG ) ) );
				printf ( '<p><span>%s</span></p>', sprintf ( __ ( 'Alternately, <a href="%s">manually configure</a> your own settings and/or modify advanced options.', Postman::TEXT_DOMAIN ), $this->getPageUrl ( PostmanConfigurationController::CONFIGURATION_SLUG ) ) );
			} else {
				if (PostmanState::getInstance ()->isTimeToReviewPostman () && ! PostmanOptions::getInstance ()->isNew ()) {
					print '</br><hr width="70%"></br>';
					/* translators: where %s is the URL to the WordPress.org review and ratings page */
					printf ( '%s</span></p>', sprintf ( __ ( 'Please consider <a href="%s">leaving a review</a> to help spread the word! :D', Postman::TEXT_DOMAIN ), 'https://wordpress.org/support/view/plugin-reviews/postman-smtp?filter=5' ) );
				}
				printf ( '<p><span>%s :-)</span></p>', sprintf ( __ ( 'Postman needs translators! Please take a moment to <a href="%s">translate a few sentences on-line</a>', Postman::TEXT_DOMAIN ), 'https://translate.wordpress.org/projects/wp-plugins/postman-smtp/stable' ) );
			}
			printf ( '<p><span>%s</span></p>', __ ( '<b style="background-color:yellow">New for v1.7!</style></b> Send mail with the Mandrill or SendGrid APIs.', Postman::TEXT_DOMAIN ) );
		}
		
		/**
		 */
		private function printDeliveryDetails() {
			$currentTransport = PostmanTransportRegistry::getInstance ()->getActiveTransport ();
			$deliveryDetails = $currentTransport->getDeliveryDetails ( $this->options );
			printf ( '<p style="margin:0 10px"><span>%s</span></p>', $deliveryDetails );
		}
		
		/**
		 *
		 * @param unknown $title        	
		 * @param string $slug        	
		 */
		public static function outputChildPageHeader($title, $slug = '') {
			printf ( '<h2>%s</h2>', sprintf ( __ ( '%s Setup', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) ) );
			printf ( '<div id="postman-main-menu" class="welcome-panel %s">', $slug );
			print '<div class="welcome-panel-content">';
			print '<div class="welcome-panel-column-container">';
			print '<div class="welcome-panel-column welcome-panel-last">';
			printf ( '<h4>%s</h4>', $title );
			print '</div>';
			printf ( '<p id="back_to_main_menu">%s <a id="back_to_menu_link" href="%s">%s</a></p>', self::BACK_ARROW_SYMBOL, PostmanUtils::getSettingsPageUrl (), _x ( 'Back To Main Menu', 'Return to main menu link', Postman::TEXT_DOMAIN ) );
			print '</div></div></div>';
		}
		
		/**
		 */
		public function outputPurgeDataContent() {
			$importTitle = __ ( 'Import', Postman::TEXT_DOMAIN );
			$exportTile = __ ( 'Export', Postman::TEXT_DOMAIN );
			$resetTitle = __ ( 'Reset Plugin', Postman::TEXT_DOMAIN );
			$options = $this->options;
			print '<div class="wrap">';
			PostmanViewController::outputChildPageHeader ( sprintf ( '%s/%s/%s', $importTitle, $exportTile, $resetTitle ) );
			print '<section id="export_settings">';
			printf ( '<h3><span>%s<span></h3>', $exportTile );
			printf ( '<p><span>%s</span></p>', __ ( 'Copy this data into another instance of Postman to duplicate the configuration.', Postman::TEXT_DOMAIN ) );
			$data = '';
			if (! PostmanPreRequisitesCheck::checkZlibEncode ()) {
				$extraDeleteButtonAttributes = sprintf ( 'disabled="true"' );
				$data = '';
			} else {
				$extraDeleteButtonAttributes = '';
				if (! $options->isNew ()) {
					$data = $options->export ();
				}
			}
			printf ( '<textarea cols="80" rows="5" readonly="true" name="settings" %s>%s</textarea>', $extraDeleteButtonAttributes, $data );
			print '</section>';
			print '<section id="import_settings">';
			printf ( '<h3><span>%s<span></h3>', $importTitle );
			print '<form method="POST" action="' . get_admin_url () . 'admin-post.php">';
			wp_nonce_field ( PostmanAdminController::IMPORT_SETTINGS_SLUG );
			printf ( '<input type="hidden" name="action" value="%s" />', PostmanAdminController::IMPORT_SETTINGS_SLUG );
			print '<p>';
			printf ( '<span>%s</span>', __ ( 'Paste data from another instance of Postman here to duplicate the configuration.', Postman::TEXT_DOMAIN ) );
			if (PostmanTransportRegistry::getInstance ()->getSelectedTransport ()->isOAuthUsed ( PostmanOptions::getInstance ()->getAuthenticationType () )) {
				$warning = __ ( 'Warning', Postman::TEXT_DOMAIN );
				$errorMessage = __ ( 'Using the same OAuth 2.0 Client ID and Client Secret from this site at the same time as another site will cause failures.', Postman::TEXT_DOMAIN );
				printf ( ' <span><b>%s</b>: %s</span>', $warning, $errorMessage );
			}
			print '</p>';
			printf ( '<textarea cols="80" rows="5" name="settings" %s></textarea>', $extraDeleteButtonAttributes );
			submit_button ( __ ( 'Import', Postman::TEXT_DOMAIN ), 'primary', 'import', true, $extraDeleteButtonAttributes );
			print '</form>';
			print '</section>';
			print '<section id="delete_settings">';
			printf ( '<h3><span>%s<span></h3>', $resetTitle );
			print '<form method="POST" action="' . get_admin_url () . 'admin-post.php">';
			wp_nonce_field ( PostmanAdminController::PURGE_DATA_SLUG );
			printf ( '<input type="hidden" name="action" value="%s" />', PostmanAdminController::PURGE_DATA_SLUG );
			printf ( '<p><span>%s</span></p><p><span>%s</span></p>', __ ( 'This will purge all of Postman\'s settings, including account credentials and the email log.', Postman::TEXT_DOMAIN ), __ ( 'Are you sure?', Postman::TEXT_DOMAIN ) );
			$extraDeleteButtonAttributes = 'style="background-color:red;color:white"';
			if ($this->options->isNew ()) {
				$extraDeleteButtonAttributes .= ' disabled="true"';
			}
			submit_button ( $resetTitle, 'delete', 'submit', true, $extraDeleteButtonAttributes );
			print '</form>';
			print '</section>';
			print '</div>';
		}
		
		/**
		 */
		private function displayTopNavigation() {
			screen_icon ();
			printf ( '<h2>%s</h2>', sprintf ( __ ( '%s Setup', Postman::TEXT_DOMAIN ), __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ) ) );
			print '<div id="postman-main-menu" class="welcome-panel">';
			print '<div class="welcome-panel-content">';
			print '<div class="welcome-panel-column-container">';
			print '<div class="welcome-panel-column">';
			printf ( '<h4>%s</h4>', __ ( 'Configuration', Postman::TEXT_DOMAIN ) );
			printf ( '<a class="button button-primary button-hero" href="%s">%s</a>', $this->getPageUrl ( PostmanConfigurationController::CONFIGURATION_WIZARD_SLUG ), __ ( 'Start the Wizard', Postman::TEXT_DOMAIN ) );
			printf ( '<p class="">or <a href="%s" class="configure_manually">%s</a></p>', $this->getPageUrl ( PostmanConfigurationController::CONFIGURATION_SLUG ), __ ( 'Show All Settings', Postman::TEXT_DOMAIN ) );
			print '</div>';
			print '<div class="welcome-panel-column">';
			printf ( '<h4>%s</h4>', _x ( 'Actions', 'Main Menu', Postman::TEXT_DOMAIN ) );
			print '<ul>';
			
			// Grant permission with Google
			PostmanTransportRegistry::getInstance ()->getSelectedTransport ()->printActionMenuItem ();
			
			if (PostmanWpMailBinder::getInstance ()->isBound ()) {
				printf ( '<li><a href="%s" class="welcome-icon send_test_email">%s</a></li>', $this->getPageUrl ( PostmanSendTestEmailController::EMAIL_TEST_SLUG ), __ ( 'Send a Test Email', Postman::TEXT_DOMAIN ) );
			} else {
				printf ( '<li><div class="welcome-icon send_test_email">%s</div></li>', __ ( 'Send a Test Email', Postman::TEXT_DOMAIN ) );
			}
			
			// import-export-reset menu item
			if (! $this->options->isNew () || true) {
				$purgeLinkPattern = '<li><a href="%1$s" class="welcome-icon oauth-authorize">%2$s</a></li>';
			} else {
				$purgeLinkPattern = '<li>%2$s</li>';
			}
			$importTitle = __ ( 'Import', Postman::TEXT_DOMAIN );
			$exportTile = __ ( 'Export', Postman::TEXT_DOMAIN );
			$resetTitle = __ ( 'Reset Plugin', Postman::TEXT_DOMAIN );
			$importExportReset = sprintf ( '%s/%s/%s', $importTitle, $exportTile, $resetTitle );
			printf ( $purgeLinkPattern, $this->getPageUrl ( PostmanAdminController::MANAGE_OPTIONS_PAGE_SLUG ), sprintf ( '%s', $importExportReset ) );
			print '</ul>';
			print '</div>';
			print '<div class="welcome-panel-column welcome-panel-last">';
			printf ( '<h4>%s</h4>', _x ( 'Troubleshooting', 'Main Menu', Postman::TEXT_DOMAIN ) );
			print '<ul>';
			printf ( '<li><a href="%s" class="welcome-icon run-port-test">%s</a></li>', $this->getPageUrl ( PostmanConnectivityTestController::PORT_TEST_SLUG ), __ ( 'Connectivity Test', Postman::TEXT_DOMAIN ) );
			printf ( '<li><a href="%s" class="welcome-icon run-port-test">%s</a></li>', $this->getPageUrl ( PostmanDiagnosticTestController::DIAGNOSTICS_SLUG ), __ ( 'Diagnostic Test', Postman::TEXT_DOMAIN ) );
			printf ( '<li><a href="https://wordpress.org/support/plugin/postman-smtp" class="welcome-icon postman_support">%s</a></li>', __ ( 'Online Support', Postman::TEXT_DOMAIN ) );
			print '</ul></div></div></div></div>';
		}
		
	}
}
		