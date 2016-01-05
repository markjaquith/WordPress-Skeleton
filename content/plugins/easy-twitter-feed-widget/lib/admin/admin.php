<?php
class Kamn_Easytwitterfeedwidget_Admin {
		
		/** Properties */
		private $kamn_easy_twitter_feed_widget_menu_slug;
		private $kamn_easy_twitter_feed_widget_options_page_hook;
		
		/** Constructor Method */
		function __construct() {
	
			/** Let Set Properties */
			$this->kamn_easy_twitter_feed_widget_menu_slug = 'easy-twitter-feed-widget-options';
			$this->kamn_easy_twitter_feed_widget_options_page_hook = 'settings_page_' . $this->kamn_easy_twitter_feed_widget_menu_slug;
			
			/** Admin Hooks */
			add_action( 'admin_menu', array( $this, 'kamn_easy_twitter_feed_widget_options_page' ) );
			add_action( 'admin_init', array( $this, 'kamn_easy_twitter_feed_widget_options' ) );
			add_action( 'admin_init', array( $this, 'kamn_easy_twitter_feed_widget_options_init' ), 12 );			
			add_action( 'load-'. $this->kamn_easy_twitter_feed_widget_options_page_hook, array( $this, 'kamn_easy_twitter_feed_widget_options_page_contextual_help' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'kamn_easy_twitter_feed_widget_enqueue_scripts' ) );
	
		}
		
		/** Options Page Menu */
		function kamn_easy_twitter_feed_widget_options_page() {
			add_submenu_page( 'options-general.php', esc_html( __( 'Easy Twitter Feed Widget Options', 'kamn-easy-twitter-feed-widget' ) ), esc_html( __( 'Easy Twitter Feed Widget Options', 'kamn-easy-twitter-feed-widget' ) ), 'manage_options', $this->kamn_easy_twitter_feed_widget_menu_slug, array( $this, 'kamn_easy_twitter_feed_widget_options_do_page' ) );
		}
		
		/** Options Page */
		function kamn_easy_twitter_feed_widget_options_do_page() {
			require_once( KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_DIR . 'page.php' );
		}
		
		/** Options Registration */		
		function kamn_easy_twitter_feed_widget_options() {
		
			/** Register theme settings. */
			register_setting( 'kamn_easy_twitter_feed_widget_options_group', 'kamn_easy_twitter_feed_widget_options', array( $this, 'kamn_easy_twitter_feed_widget_options_validate' ) );
			
			/** Fonts Section */
			add_settings_section( 'kamn_easy_twitter_feed_widget_section_script', 'Twitter Script Options', array( $this, 'kamn_easy_twitter_feed_widget_section_script_cb' ), 'kamn_easy_twitter_feed_widget_section_script_page' );			
			add_settings_field( 'kamn_easy_twitter_feed_widget_field_script_control', __( 'Load Twitter Script', 'kamn-easy-twitter-feed-widget' ), array( $this, 'kamn_easy_twitter_feed_widget_field_script_control_cb' ), 'kamn_easy_twitter_feed_widget_section_script_page', 'kamn_easy_twitter_feed_widget_section_script' );
			
			/** General Section */
			add_settings_section( 'kamn_easy_twitter_feed_widget_section_general', 'General Options', array( $this, 'kamn_easy_twitter_feed_widget_section_general_cb' ), 'kamn_easy_twitter_feed_widget_section_general_page' );
			add_settings_field( 'kamn_easy_twitter_feed_widget_field_reset_control', __( 'Reset Easy Twitter Feed Widget Options', 'kamn-easy-twitter-feed-widget' ), array( $this, 'kamn_easy_twitter_feed_widget_field_reset_control_cb' ), 'kamn_easy_twitter_feed_widget_section_general_page', 'kamn_easy_twitter_feed_widget_section_general' );
		
		}
		
		/** Kamn Contextual Help. */
		function kamn_easy_twitter_feed_widget_options_page_contextual_help() {
			
			/** Get the plugin data. */
			$plugin = kamn_easy_twitter_feed_widget_plugin_data();
			$AuthorURI = $plugin['AuthorURI'];
			$PluginURI = $plugin['PluginURI'];
		
			/** Get the current screen */
			$screen = get_current_screen();
			
			/** Add theme reference help screen tab. */
			$screen->add_help_tab( array(
				
				'id' => 'kamn-easy-twitter-feed-widget-plugin',
				'title' => __( 'Plugin Support', 'kamn-easy-twitter-feed-widget' ),
				'content' => implode( '', file( KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_DIR . 'help/support.html' ) ),				
				
				)
			);
			
			/** Add license reference help screen tab. */
			$screen->add_help_tab( array(
				
				'id' => 'kamn-easy-twitter-feed-widget-license',
				'title' => __( 'License', 'kamn-easy-twitter-feed-widget' ),
				'content' => implode( '', file( KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_DIR . 'help/license.html' ) ),				
				
				)
			);
			
			/** Add changelog reference help screen tab. */
			$screen->add_help_tab( array(
				
				'id' => 'kamn-easy-twitter-feed-widget-changelog',
				'title' => __( 'Changelog', 'kamn-easy-twitter-feed-widget' ),
				'content' => implode( '', file( KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_DIR . 'help/changelog.html' ) ),				
				
				)
			);
			
			/** Help Sidebar */
			$sidebar = '<p><strong>' . __( 'For more information:', 'kamn-easy-twitter-feed-widget' ) . '</strong></p>';
			if ( !empty( $AuthorURI ) ) {
				$sidebar .= '<p><a href="' . esc_url( $AuthorURI ) . '" target="_blank">' . __( 'Easy Twitter Feed Widget Plugin', 'kamn-easy-twitter-feed-widget' ) . '</a></p>';
			}
			if ( !empty( $PluginURI ) ) {
				$sidebar .= '<p><a href="' . esc_url( $PluginURI ) . '" target="_blank">' . __( 'Easy Twitter Feed Widget Official Page', 'kamn-easy-twitter-feed-widget' ) . '</a></p>';
			}			
			$screen->set_help_sidebar( $sidebar );
			
		}
		
		/** Kamn Enqueue Scripts */
		function kamn_easy_twitter_feed_widget_enqueue_scripts( $hook ) {
			
			/** Load Scripts For Kamn Options Page */
			if( $hook === $this->kamn_easy_twitter_feed_widget_options_page_hook ) {
				
				/** Load Admin Scripts */
				wp_enqueue_script( 'kamn-easy-twitter-feed-widget-admin-js-theme-options', esc_url( KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_URI . 'plugin-options.js' ), array( 'jquery' ) );
				
				/** Load Admin Stylesheet */
				wp_enqueue_style( 'kamn-easy-twitter-feed-widget-admin-css-theme-options', esc_url( KAMN_EASY_TWITTER_FEED_WIDGET_ADMIN_URI . 'plugin-options.css' ) );
				
			}
				
		}
		
		/** Loads the plugin setting. */
		function kamn_easy_twitter_feed_widget_get_admin_settings() {
	
			/** Global Data */
			global $kamn_easy_twitter_feed_widget;

			/* If the settings array hasn't been set, call get_option() to get an array of plugin settings. */
			if ( !isset( $kamn_easy_twitter_feed_widget->settings_admin ) ) {
				$kamn_easy_twitter_feed_widget->settings_admin = apply_filters( 'kamn_easy_twitter_feed_widget_options_admin_filter', wp_parse_args( get_option( 'kamn_easy_twitter_feed_widget_options', kamn_easy_twitter_feed_widget_options_default() ), kamn_easy_twitter_feed_widget_options_default() ) );
			}
			
			/** return settings. */
			return $kamn_easy_twitter_feed_widget->settings_admin;
		}
		
		/** Kamn Options Init */
		function kamn_easy_twitter_feed_widget_options_init() {
			
			$kamn_easy_twitter_feed_widget_options = get_option( 'kamn_easy_twitter_feed_widget_options' );
			if( !is_array( $kamn_easy_twitter_feed_widget_options ) ) {
				update_option( 'kamn_easy_twitter_feed_widget_options', kamn_easy_twitter_feed_widget_options_default() );
			}
		
		}
		
		/** Kamn Options Range */
		
		/* Boolean Yes | No */		
		function kamn_easy_twitter_feed_widget_boolean_pd() {			
			return array ( 
				1 => __( 'yes', 'kamn-easy-twitter-feed-widget' ), 
				0 => __( 'no', 'kamn-easy-twitter-feed-widget' )
			);		
		}
		
		/** Kamn Options Validation */				
		function kamn_easy_twitter_feed_widget_options_validate( $input ) {
			
			/** Default */
			$default = kamn_easy_twitter_feed_widget_options_default();
			
			/** Kamn Predefined */
			$kamn_easy_twitter_feed_widget_boolean_pd = $this->kamn_easy_twitter_feed_widget_boolean_pd();
			
			/* Validation: kamn_easy_twitter_feed_widget_fontawesome_control */
			if ( ! array_key_exists( $input['kamn_easy_twitter_feed_widget_script_control'], $kamn_easy_twitter_feed_widget_boolean_pd ) ) {
				 $input['kamn_easy_twitter_feed_widget_script_control'] = $default['kamn_easy_twitter_feed_widget_script_control'];
			}
			
			/** Reset Logic */
			if( $input['kamn_easy_twitter_feed_widget_reset_control'] == 1 ) {
				$input = $default;
			}			
			
			return $input;
		
		}
		
		/** Fonts Section Callback */				
		function kamn_easy_twitter_feed_widget_section_script_cb() {
			echo '<div class="kamn-easy-twitter-feed-widget-section-desc">
			  <p class="description">'. __( 'Customize Twitter Embeddable Widget by using the following settings.', 'kamn-easy-twitter-feed-widget' ) .'</p>
			</div>';
		}
		
		/* Script Control Callback */		
		function kamn_easy_twitter_feed_widget_field_script_control_cb() {
			
			$kamn_easy_twitter_feed_widget_options = $this->kamn_easy_twitter_feed_widget_get_admin_settings();
			$items = $this->kamn_easy_twitter_feed_widget_boolean_pd();
			
			echo '<select id="kamn_easy_twitter_feed_widget_script_control" name="kamn_easy_twitter_feed_widget_options[kamn_easy_twitter_feed_widget_script_control]">';
			foreach( $items as $key => $val ) {
			?>
            <option value="<?php echo $key; ?>" <?php selected( $key, $kamn_easy_twitter_feed_widget_options['kamn_easy_twitter_feed_widget_script_control'] ); ?>><?php echo $val; ?></option>
            <?php
			}
			echo '</select>';
			echo '<div><code>'. __( 'Select "no" if your theme supports Twitter Embeddable Widget.', 'kamn-easy-twitter-feed-widget' ) .'</code></div>';
		
		}
		
		/** General Section Callback */				
		function kamn_easy_twitter_feed_widget_section_general_cb() {
			echo '<div class="kamn-easy-twitter-feed-widget-section-desc">
			  <p class="description">'. __( 'Here are the general settings to customize easy twitter feed widget plugin.', 'kamn-easy-twitter-feed-widget' ) .'</p>
			</div>';
		}
		
		/* Reset Control Callback */		
		function kamn_easy_twitter_feed_widget_field_reset_control_cb() {
			echo '<label><input type="checkbox" id="kamn_easy_twitter_feed_widget_reset_control" name="kamn_easy_twitter_feed_widget_options[kamn_easy_twitter_feed_widget_reset_control]" value="1" /> '. __( 'Reset EasyTwitterFeedWidget Options.', 'kamn-easy-twitter-feed-widget' ) .'</label>';
		}
}

/** Initiate Admin */
new Kamn_Easytwitterfeedwidget_Admin();