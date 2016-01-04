<?php
if (! class_exists ( 'PostmanAdminPointer' )) {
	
	/**
	 * From http://code.tutsplus.com/articles/integrating-with-wordpress-ui-admin-pointers--wp-26853
	 *
	 * @author jasonhendriks
	 *        
	 */
	class PostmanAdminPointer {
		private $logger;
		private $rootPluginFilenameAndPath;
		
		/**
		 *
		 * @param unknown $rootPluginFilenameAndPath        	
		 */
		function __construct($rootPluginFilenameAndPath) {
			$this->logger = new PostmanLogger ( get_class ( $this ) );
			$this->rootPluginFilenameAndPath = $rootPluginFilenameAndPath;
			
			// Don't run on WP < 3.3
			if (get_bloginfo ( 'version' ) < '3.3' || true)
				return;
			
			add_action ( 'admin_enqueue_scripts', array (
					$this,
					'wptuts_pointer_load' 
			), 1000 );
			add_filter ( 'postman_admin_pointers-settings_page_postman', array (
					$this,
					'wptuts_register_pointer_testing' 
			) );
		}
		
		/**
		 *
		 * @param unknown $hook_suffix        	
		 */
		function wptuts_pointer_load($hook_suffix) {
			// only do this for administrators
			if (PostmanUtils::isAdmin ()) {
				$this->logger->trace ( 'wptuts' );
				
				$screen = get_current_screen ();
				$screen_id = $screen->id;
				
				// Get pointers for this screen
				$pointers = apply_filters ( 'postman_admin_pointers-' . $screen_id, array () );
				
				if (! $pointers || ! is_array ( $pointers ))
					return;
					
					// Get dismissed pointers
				$dismissed = explode ( ',', ( string ) get_user_meta ( get_current_user_id (), 'dismissed_wp_pointers', true ) );
				$this->logger->trace ( $dismissed );
				$valid_pointers = array ();
				
				// Check pointers and remove dismissed ones.
				foreach ( $pointers as $pointer_id => $pointer ) {
					
					// Sanity check
					if (in_array ( $pointer_id, $dismissed ) || empty ( $pointer ) || empty ( $pointer_id ) || empty ( $pointer ['target'] ) || empty ( $pointer ['options'] ))
						continue;
					
					$pointer ['pointer_id'] = $pointer_id;
					
					// Add the pointer to $valid_pointers array
					$valid_pointers ['pointers'] [] = $pointer;
				}
				
				// No valid pointers? Stop here.
				if (empty ( $valid_pointers )) {
					return;
				}
				
				// Add pointers style to queue.
				wp_enqueue_style ( 'wp-pointer' );
				
				// Add pointers script to queue. Add custom script.
				wp_enqueue_script ( 'postman_admin_pointer', plugins_url ( 'script/postman-admin-pointer.js', $this->rootPluginFilenameAndPath ), array (
						'wp-pointer' 
				) );
				
				// Add pointer options to script.
				wp_localize_script ( 'postman_admin_pointer', 'postman_admin_pointer', $valid_pointers );
				$this->logger->trace ( 'out wptuts' );
			}
		}
		function wptuts_register_pointer_testing($p) {
			// only do this for administrators
			if (PostmanUtils::isAdmin () && false) {
				$p ['postman16_log'] = array (
						'target' => '.configure_manually',
						'options' => array (
								'content' => '',
								'position' => array (
										'edge' => 'top',
										'align' => 'left' 
								) 
						) 
				);
				return $p;
			}
		}
	}
}
