<?php 

if( !class_exists('EO_Extension') ){

	/**
	 * Useful abstract class which can be utilised by extensions
	 */
	abstract class EO_Extension{
	
		public $slug;
	
		public $label;
	
		public $public_url;
	
		public $api_url = 'http://wp-event-organiser.com';
	
		public $id;
		
		public $dependencies = false;
	
		public function __construct(){
			$this->hooks();
		}
	
		/**
		 * Returns true if event organsier version is $v or higher
		 */
		static function eo_is_after( $v ){
			$installed_plugins = get_plugins();
			$eo_version = isset( $installed_plugins['event-organiser/event-organiser.php'] )  ? $installed_plugins['event-organiser/event-organiser.php']['Version'] : false;
			return ( $eo_version && ( version_compare( $eo_version, $v  ) >= 0 )  );
		}
	
		/**
		 * Get's current version of installed plug-in.
		 */
		public function get_current_version(){
			$plugins = get_plugins();
	
			if( !isset( $plugins[$this->slug] ) )
				return false;
	
			$plugin_data = $plugins[$this->slug];
			return $plugin_data['Version'];
		}
	
	
		/* Check that the minimum required dependency is loaded */
		public function check_dependencies() {
	
			$installed_plugins = get_plugins();
	
			if( empty( $this->dependencies ) ){
				return;
			}
			
			foreach ( $this->dependencies as $dep_slug => $dep ) {
	
				if ( !isset( $installed_plugins[$dep_slug] ) ) {
					$this->not_installed[] = $dep_slug;
	
				}elseif ( -1 == version_compare( $installed_plugins[$dep_slug]['Version'], $dep['version'] )  ) {
					$this->outdated[] = $dep_slug;
	
				}elseif ( !is_plugin_active( $dep_slug ) ) {
					$this->not_activated[] = $dep_slug;
				}
			}
	
			/* If dependency does not exist - uninstall. If the version is incorrect, we'll try to cope */
			if ( !empty( $this->not_installed ) ) {
				deactivate_plugins( $this->slug );
			}
	
			if ( !empty( $this->not_installed )  || !empty( $this->outdated )  || !empty( $this->not_activated ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			}
		}
	
	
		public function admin_notices() {
	
			$installed_plugins = get_plugins();
	
			echo '<div class="updated">';
	
			//Display warnings for uninstalled dependencies
			if ( !empty( $this->not_installed )  ) {
				foreach (  $this->not_installed as $dep_slug ) {
					printf(
						'<p> <strong>%1$s</strong> has been deactivated as it requires %2$s (version %3$s or higher). Please <a href="%4$s"> install %2$s</a>.</p>',
						$this->label,
						$this->dependencies[$dep_slug]['name'],
						$this->dependencies[$dep_slug]['version'],
						$this->dependencies[$dep_slug]['url']
					);
				}
			}
	
			//Display warnings for outdated dependencides.
			if ( !empty( $this->outdated ) && 'update-core' != get_current_screen()->id ) {
				foreach (  $this->outdated as $dep_slug ) {
					printf(
						'<p><strong>%1$s</strong> requires version %2$s <strong>%3$s</strong> or higher to function correctly. Please update <strong>%2$s</strong>.</p>',
						$this->label,
						$this->dependencies[$dep_slug]['name'],
						$this->dependencies[$dep_slug]['version']
					);
				}
			}
	
			//Display notice for activated dependencides
			if ( !empty(  $this->not_activated )  ) {
				foreach (  $this->not_activated as $dep_slug ) {
					printf(
						'<p><strong>%1$s</strong> requires %2$s to function correctly. Click to <a href="%3$s" >activate <strong>%2$s</strong></a>.</p>',
						$this->label,
						$this->dependencies[$dep_slug]['name'],
						wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $dep_slug, 'activate-plugin_' . $dep_slug )
					);
				}
			}

			echo '</div>';
		}


		public function hooks(){

			add_action( 'admin_init', array( $this, 'check_dependencies' ) );

			add_action( 'in_plugin_update_message-' . $this->slug, array( $this, 'plugin_update_message' ), 10, 2 );

				
			if( is_multisite() ){
				//add_action( 'network_admin_menu', array( 'EO_Extension', 'setup_ntw_settings' ) );
				add_action( 'network_admin_menu', array( $this, 'add_multisite_field' ) );
				add_action( 'wpmu_options', array( 'EO_Extension', 'do_ntw_settings' ) );
				add_action( 'update_wpmu_options', array( 'EO_Extension', 'save_ntw_settings' ) );
			}else{
				add_action( 'eventorganiser_register_tab_general', array( $this, 'add_field' ) );
			}

			add_filter( 'pre_set_site_transient_update_plugins', array($this,'check_update'));

			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 9999, 3 );
		}


		public function is_valid( $key ){

			$key = strtoupper( str_replace( '-', '', $key ) );

			$local_key = get_site_option($this->id.'_plm_local_key');

			//Token depends on key being checked to instantly invalidate the local period when key is changed.
			$token = wp_hash($key.'|'.$_SERVER['SERVER_NAME'].'|'.$_SERVER['SERVER_ADDR'].'|'.$this->slug);

			if( $local_key ){
				$response = maybe_unserialize( $local_key['response'] );
				$this->key_data = $response;

				if( $token == $response['token'] ){

					$last_checked = isset($response['date_checked']) ?  intval($response['date_checked'] ) : 0;
					$expires = $last_checked + 24 * 24 * 60 * 60;

					if( $response['valid'] == 'TRUE' &&  ( time() < $expires ) ){
						//Local key is still valid
						return true;
					}
				}
			}
	
			//Check license format
			if( empty( $key ) )
				return new WP_Error( 'no-key-given' );

			if( preg_match('/[^A-Z234567]/i', $key) )
				return new WP_Error( 'invalid-license-format' );

			if( $is_valid = get_transient( $this->id . '_check' ) && false !== get_transient( $this->id . '_check_lock' ) ){
				if( $token === $is_valid )
					return true;
			}

			//Check license remotely
			$resp = wp_remote_post($this->api_url, array(
					'method' => 'POST',
					'timeout' => 45,
					'body' => array(
							'plm-action' => 'check_license',
							'license' => $key,
							'product' => $this->slug,
							'domain' => $_SERVER['SERVER_NAME'],
							'token' => $token,
					),
			));
	
			$body = (array) json_decode( wp_remote_retrieve_body( $resp ) );
	
			if( !$body || !isset($body['response']) ){
				//No response or error
				$grace =  $last_checked + 1 * 24 * 60 * 60;
	
				if(  time() < $grace )
					return true;
	
				return new WP_Error( 'invalid-response' );
			}
	
			$response =  maybe_unserialize( $body['response'] );
	
			update_option( $this->id . '_plm_local_key', $body );
	
			if( $token != $response['token'] )
				return new WP_Error( 'invalid-token' );
	
			if( $response['valid'] == 'TRUE' )
				$is_valid = true;
			else
				$is_valid = new WP_Error( $response['reason'] );

			set_transient( $this->id . '_check_lock', $key, 15*20 );
			set_transient( $this->id . '_check', $token, 15*20 );

			return $is_valid;
		}


		public function plugin_update_message( $plugin_data, $r  ){

			if( is_wp_error( $this->is_valid( get_site_option( $this->id.'_license' ) ) ) ){
				printf(
				'<br> The license key you have entered is invalid.
				<a href="%s"> Purchase a license key </a> or enter a valid license key <a href="%s">here</a>',
					$this->public_url,
					admin_url('options-general.php?page=event-settings')
				);
			}
		}

		public function add_multisite_field(){
				
			register_setting( 'settings-network', $this->id.'_license' );

			add_settings_section( 'eo-ntw-settings', "Event Organiser Extension Licenses", '__return_false', 'settings-network' );
				
			add_settings_field(
				$this->id.'_license',
				$this->label,
				array( $this, 'field_callback'),
				'settings-network',
				'eo-ntw-settings'
			);
		}

		static function do_ntw_settings(){
			wp_nonce_field("eo-ntw-settings-options", '_eontwnonce');			
			do_settings_sections( 'settings-network' );
		}

		static function save_ntw_settings(){
			
			if( !current_user_can( 'manage_network_options' ) ){
				return false;
			}
	
			if( !isset( $_POST['_eontwnonce'] ) || !wp_verify_nonce( $_POST['_eontwnonce'], 'eo-ntw-settings-options' ) ){
				return false;
			}

			$whitelist_options = apply_filters( 'whitelist_options', array());
			if( isset( $whitelist_options['settings-network'] ) ){
				foreach( $whitelist_options['settings-network'] as $option_name ){
					if ( ! isset($_POST[$option_name]) )
						continue;
					$value = wp_unslash( $_POST[$option_name] );
					update_site_option( $option_name, $value );
				}
			}

		}

		public function add_field(){

			register_setting( 'eventorganiser_general', $this->id.'_license' );

			if( self::eo_is_after( '2.3' ) ){
				$section_id = 'general_licence';
			}else{
				$section_id = 'general';
			}
	
			add_settings_field(
				$this->id.'_license',
				$this->label,
				array( $this, 'field_callback'),
				'eventorganiser_general',
				$section_id
			);
		}


		public function field_callback(){

			$key = get_site_option( $this->id.'_license'  );
			$check = $this->is_valid( $key );
			$valid = !is_wp_error( $check );
	
			$message = false;

			if( !$valid ){
				$message =  sprintf(
					'The license key you have entered is invalid. <a href="%s">Purchase a license key</a>.',
					$this->public_url
				);
						
				$message .= eventorganiser_inline_help(
						sprintf( 'Invalid license key (%s)', $check->get_error_code() ),
						sprintf( 
								'<p>%s</p><p> Without a valid license key you will not be eligable for updates or support. You can purchase a
					license key <a href="%s">here</a>.</p> <p> If you have entered a valid license which does not seem to work, please
					<a href="%s">contact suppport</a>.',
								$this->_get_verbose_reason( $check->get_error_code() ),
								$this->public_url,
								'http://wp-event-organiser.com/contact/'
						)
				);
			}elseif( isset( $this->key_data) && !empty( $this->key_data['expires'] ) ){
				
				$now     = new DateTime( 'now' );
				$expires = new DateTime( $this->key_data['expires'] );
				 
				$time_diff = abs( $expires->format('U') - $now->format('U') );
				$days     = floor( $time_diff/86400 );

				if( $days <= 21 ){
				
					$message =  sprintf( 
						'This key expires on %s. <a href="%s">Renew within the next %d days</a> for a 50%% discount',
						$expires->format( get_option( 'date_format' ) ),
						'http://wp-event-organiser.com/my-account',
						$days
					);
					
				}
			}
	
			eventorganiser_text_field( array(
				'label_for' => $this->id.'_license',
				'value' => $key,
				'name' => $this->id.'_license',
				'style' => $valid ? 'background:#D7FFD7' : 'background:#FFEBE8',
				'class' => 'regular-text',
				'help' => $message
			) );
	
		}
	
	
		private function _get_verbose_reason( $code ){
			
			$reasons = array(
				'no-key-given'           => 'No key has been provided.',
				'invalid-license-format' => 'The entered key is incorrect.',
				'invalid-response'       => 'There was an error in authenticating the license key status.',
				'invalid-token'          => 'There was an error in authenticating the license key status.',
				'key-not-found'          => 'No key has been provided.',
				'license-not-found'      => 'The provided license could not be found.',
				'license-suspended'      => 'The license key is no longer valid.',
				'incorrect-product'      => 'The key is not valid for this product.',
				'license-expired'        => 'Your license key has expired.',
				'site-limit-reached'     => 'The key has met the site limit.',
				'unknown'                => 'An unknown error has occurred'
			);
			
			if( isset( $reasons[$code] ) ){
				return $reasons[$code];
			}else{
				return $code;	
			}
		}
		
		public function plugin_info( $check, $action, $args ){
	
			if ( $args->slug == $this->slug ) {
				$obj = $this->get_remote_plugin_info('plugin_info');
				return $obj;
			}
			return $check;
		}
	
		/**
		 * Fired just before setting the update_plugins site transient. Remotely checks if a new version is available
		 */
		public function check_update($transient){
	
			/**
			 * wp_update_plugin() triggers this callback twice by saving the transient twice
			 * The repsonse is kept in a transient - so there isn't much of it a hit.
			 */
	
			//Get remote information
			$plugin_info = $this->get_remote_plugin_info('plugin_info');
	
			// If a newer version is available, add the update
			if ( $plugin_info && version_compare($this->get_current_version(), $plugin_info->new_version, '<' ) ){
	
				$obj = new stdClass();
				$obj->slug = $this->slug;
				$obj->new_version = $plugin_info->new_version;
				$obj->package =$plugin_info->download_link;
	
				if( isset( $plugin_info->sections['upgrade_notice'] ) ){
					$obj->upgrade_notice = $plugin_info->sections['upgrade_notice'];
				}
	
				//Add plugin to transient.
				$transient->response[$this->slug] = $obj;
			}
	
			return $transient;
		}
	
	
		/**
		 * Return remote data
		 * Store in transient for 12 hours for performance
		 *
		 * @param (string) $action -'info', 'version' or 'license'
		 * @return mixed $remote_version
		 */
		public function get_remote_plugin_info($action='plugin_info'){
	
			$key = wp_hash( 'plm_'.$this->id . '_' . $action . '_' . $this->slug );
			if( false !== ( $plugin_obj = get_site_transient( $key ) ) && !$this->force_request() ){
				return $plugin_obj;
			}
	
			$request = wp_remote_post( $this->api_url, array(
					'method' => 'POST',
					'timeout' => 45,
					'body' => array(
							'plm-action' => $action,
							'license'    => get_site_option( $this->id.'_license' ),
							'product'    => $this->slug,
							'domain'     => $_SERVER['SERVER_NAME'],
					)
			));
	
			if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				//If its the plug-in object, unserialize and store for 12 hours.
				$plugin_obj = ( 'plugin_info' == $action ? unserialize( $request['body'] ) : $request['body'] );
				set_site_transient( $key, $plugin_obj, 12*60*60 );
				return $plugin_obj;
			}
			//Don't try again for 5 minutes
			set_site_transient( $key, '', 5*60 );
			return false;
		}
	
	
		public function force_request(){
	
			//We don't use get_current_screen() because of conclict with InfiniteWP
			global $current_screen;
	
			if ( ! isset( $current_screen ) )
				return false;
	
			return isset( $current_screen->id ) && ( 'plugins' == $current_screen->id || 'update-core' == $current_screen->id );
		}
	
	}
}