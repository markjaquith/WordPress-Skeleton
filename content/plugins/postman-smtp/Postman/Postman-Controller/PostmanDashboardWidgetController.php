<?php
if (! class_exists ( "PostmanDashboardWidgetController" )) {
	
	//
	class PostmanDashboardWidgetController {
		private $rootPluginFilenameAndPath;
		private $options;
		private $authorizationToken;
		private $wpMailBinder;
		
		/**
		 * Start up
		 */
		public function __construct($rootPluginFilenameAndPath, PostmanOptions $options, PostmanOAuthToken $authorizationToken, PostmanWpMailBinder $binder) {
			assert ( ! empty ( $rootPluginFilenameAndPath ) );
			assert ( ! empty ( $options ) );
			assert ( ! empty ( $authorizationToken ) );
			assert ( ! empty ( $binder ) );
			$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
			$this->options = $options;
			$this->authorizationToken = $authorizationToken;
			$this->wpMailBinder = $binder;
			
			add_action ( 'wp_dashboard_setup', array (
					$this,
					'addDashboardWidget' 
			) );
			
			add_action ( 'wp_network_dashboard_setup', array (
					$this,
					'addNetworkDashboardWidget' 
			) );
			
			// dashboard glance mod
			if ($this->options->isMailLoggingEnabled ()) {
				add_filter ( 'dashboard_glance_items', array (
						$this,
						'customizeAtAGlanceDashboardWidget' 
				), 10, 1 );
			}
			
			// Postman API: register the human-readable plugin state
			add_filter ( 'print_postman_status', array (
					$this,
					'print_postman_status' 
			) );
		}
		
		/**
		 * Add a widget to the dashboard.
		 *
		 * This function is hooked into the 'wp_dashboard_setup' action below.
		 */
		public function addDashboardWidget() {
			// only display to the widget to administrator
			if (PostmanUtils::isAdmin ()) {
				wp_add_dashboard_widget ( 'example_dashboard_widget', __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ), array (
						$this,
						'printDashboardWidget' 
				) ); // Display function.
			}
		}
		
		/**
		 * Add a widget to the network dashboard
		 */
		public function addNetworkDashboardWidget() {
			// only display to the widget to administrator
			if (PostmanUtils::isAdmin ()) {
				wp_add_dashboard_widget ( 'example_dashboard_widget', __ ( 'Postman SMTP', Postman::TEXT_DOMAIN ), array (
						$this,
						'printNetworkDashboardWidget' 
				) ); // Display function.
			}
		}
		
		/**
		 * Create the function to output the contents of our Dashboard Widget.
		 */
		public function printDashboardWidget() {
			$goToSettings = sprintf ( '<a href="%s">%s</a>', PostmanUtils::getSettingsPageUrl (), __ ( 'Settings', Postman::TEXT_DOMAIN ) );
			$goToEmailLog = sprintf ( '%s', _x ( 'Email Log', 'The log of Emails that have been delivered', Postman::TEXT_DOMAIN ) );
			if ($this->options->isMailLoggingEnabled ()) {
				$goToEmailLog = sprintf ( '<a href="%s">%s</a>', PostmanUtils::getEmailLogPageUrl (), $goToEmailLog );
			}
			apply_filters ( 'print_postman_status', null );
			printf ( '<p>%s | %s</p>', $goToEmailLog, $goToSettings );
		}
		
		/**
		 * Print the human-readable plugin state
		 */
		public function print_postman_status() {
			if (! PostmanPreRequisitesCheck::isReady ()) {
				printf ( '<p><span style="color:red">%s</span></p>', __ ( 'Error: Postman is missing a required PHP library.', Postman::TEXT_DOMAIN ) );
			} else if ($this->wpMailBinder->isUnboundDueToException ()) {
				printf ( '<p><span style="color:red">%s</span></p>', __ ( 'Postman: wp_mail has been declared by another plugin or theme, so you won\'t be able to use Postman until the conflict is resolved.', Postman::TEXT_DOMAIN ) );
			} else {
				if ($this->options->getRunMode () != PostmanOptions::RUN_MODE_PRODUCTION) {
					printf ( '<p><span style="background-color:yellow">%s</span></p>', __ ( 'Postman is in <em>non-Production</em> mode and is dumping all emails.', Postman::TEXT_DOMAIN ) );
				} else if (PostmanTransportRegistry::getInstance ()->getSelectedTransport ()->isConfiguredAndReady ()) {
					printf ( '<p class="wp-menu-image dashicons-before dashicons-email"> %s </p>', sprintf ( _n ( '<span style="color:green">Postman is configured</span> and has delivered <span style="color:green">%d</span> email.', '<span style="color:green">Postman is configured</span> and has delivered <span style="color:green">%d</span> emails.', PostmanState::getInstance ()->getSuccessfulDeliveries (), Postman::TEXT_DOMAIN ), PostmanState::getInstance ()->getSuccessfulDeliveries () ) );
				} else {
					printf ( '<p><span style="color:red">%s</span></p>', __ ( 'Postman is <em>not</em> configured and is mimicking out-of-the-box WordPress email delivery.', Postman::TEXT_DOMAIN ) );
				}
				$currentTransport = PostmanTransportRegistry::getInstance ()->getActiveTransport ();
				$deliveryDetails = $currentTransport->getDeliveryDetails ( $this->options );
				printf ( '<p>%s</p>', $deliveryDetails );
			}
		}
		
		/**
		 * Create the function to output the contents of our Dashboard Widget.
		 */
		public function printNetworkDashboardWidget() {
			printf ( '<p class="wp-menu-image dashicons-before dashicons-email"> %s</p>', __ ( 'Postman is operating in per-site mode.', Postman::TEXT_DOMAIN ) );
		}
		
		/**
		 * From http://www.hughlashbrooke.com/2014/02/wordpress-add-items-glance-widget/
		 * http://coffeecupweb.com/how-to-add-custom-post-types-to-at-a-glance-dashboard-widget-in-wordpress/
		 *
		 * @param unknown $items        	
		 * @return string
		 */
		function customizeAtAGlanceDashboardWidget($items = array()) {
			// only modify the At-a-Glance for administrators
			if (PostmanUtils::isAdmin ()) {
				$post_types = array (
						PostmanEmailLogPostType::POSTMAN_CUSTOM_POST_TYPE_SLUG 
				);
				
				foreach ( $post_types as $type ) {
					
					if (! post_type_exists ( $type ))
						continue;
					
					$num_posts = wp_count_posts ( $type );
					
					if ($num_posts) {
						
						$published = intval ( $num_posts->publish );
						$privated = intval ( $num_posts->private );
						$post_type = get_post_type_object ( $type );
						
						$text = _n ( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $privated, Postman::TEXT_DOMAIN );
						$text = sprintf ( $text, number_format_i18n ( $privated ) );
						
						$items [] = sprintf ( '<a class="%1$s-count" href="%3$s">%2$s</a>', $type, $text, PostmanUtils::getEmailLogPageUrl () ) . "\n";
					}
				}
				
				return $items;
			}
		}
	}
}