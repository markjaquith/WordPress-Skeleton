<?php

/**
 * Plugin Name:         WC Vendors
 * Plugin URI:          https://www.wcvendors.com
 * Description:         Allow vendors to sell their own products and receive a commission for each sale. 
 * Author:              WC Vendors
 * Author URI:          https://www.wcvendors.com
 *
 * Version:             1.8.6
 * Requires at least:   4.0.0
 * Tested up to:        4.4.1
 *
 * Text Domain:         wcvendors
 * Domain Path:         /languages/
 *
 * @category            Plugin
 * @copyright           Copyright © 2012 Matt Gates
 * @copyright           Copyright © 2016 WC Vendors
 * @author              Matt Gates, WC Vendors
 * @package             WCVendors
 */


/**
 *   Plugin activation hook 
 */
function wcvendors_activate() {

	/**
	 *  Requires woocommerce to be installed and active 
	 */
	if ( !class_exists( 'WooCommerce' ) ) { 
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( __( 'WC Vendors requires WooCommerce to run. Please install WooCommerce and activate before attempting to activate again.', 'wcvendors' ) );
	}
} // wcvendors_activate()

register_activation_hook( __FILE__, 'wcvendors_activate' );


/**
 * Required functions
 */
require_once trailingslashit( dirname( __FILE__ ) ) . 'classes/includes/class-functions.php';

/**
 * Check if WooCommerce is active
 */
if ( wcv_is_woocommerce_activated() ) {

	/* Define an absolute path to our plugin directory. */
	if ( !defined( 'wcv_plugin_dir' ) ) 		define( 'wcv_plugin_dir', trailingslashit( dirname( __FILE__ ) ) . '/' );
	if ( !defined( 'wcv_assets_url' ) ) 		define( 'wcv_assets_url', trailingslashit( plugins_url( 'assets', __FILE__ ) ) );
	if ( !defined( 'wcv_plugin_base' ) ) 		define( 'wcv_plugin_base', plugin_basename( __FILE__ ) );
	if ( !defined( 'wcv_plugin_dir_path' ) )	define( 'wcv_plugin_dir_path', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

	/**
	 * Main Product Vendor class
	 *
	 * @package WCVendors
	 */
	class WC_Vendors
	{

		/**
		 * @var
		 */
		public static $pv_options;
		public static $id = 'wc_prd_vendor';

		/**
		 * Constructor.
		 */
		public function __construct()
		{
			
			// Load text domain 
			add_action( 'plugins_loaded', array( $this, 'load_il8n' ) );

			$this->title = __( 'WC Vendors', 'wcvendors' );

			// Install & upgrade
			add_action( 'admin_init', array( $this, 'check_install' ) );
			add_action( 'admin_init', array( $this, 'maybe_flush_permalinks' ), 99 );
			add_action( 'admin_init', array( $this, 'wcv_required_ignore_notices' ) );
			add_action( 'admin_notices', array( $this, 'wcv_required_admin_notice') );


			add_action( 'plugins_loaded', array( $this, 'load_settings' ) );
			add_action( 'plugins_loaded', array( $this, 'include_gateways' ) );
			add_action( 'plugins_loaded', array( $this, 'include_core' ) ); 
			add_action( 'current_screen', array( $this, 'include_assets' ) ); 

			add_filter( 'plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2 );
			add_action( self::$id . '_options_updated', array( $this, 'option_updates' ), 10, 2 );

			// Start a PHP session, if not yet started
			/* this line will initialize session to early and for each page
			if ( !session_id() ) session_start();*/
		}


		/**
		 *
		 */
		public function invalid_wc_version()
		{
			echo '<div class="error"><p>' . __( '<b>WC Vendors is disabled</b>. WC Vendors requires a minimum of WooCommerce v2.4.0.', 'wcvendors' ) . '</p></div>';
		}


		/**
		 * Check whether install has ran before or not
		 *
		 * Run install if it hasn't.
		 *
		 * @return unknown
		 */
		public function check_install()
		{
			global $woocommerce;

			// WC 2.0.1 is required
			if ( version_compare( $woocommerce->version, '2.4', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'invalid_wc_version' ) );
				deactivate_plugins( plugin_basename( __FILE__ ) );
				return false;
			}

			require_once wcv_plugin_dir . 'classes/class-install.php';

			$this->load_settings();
			$install = new WCV_Install;
			$install->init();
		}


		/**
		 * Set static $pv_options to hold options class
		 */
		public function load_settings()
		{
			if ( empty( self::$pv_options ) ) {
				require_once wcv_plugin_dir . 'classes/admin/settings/classes/sf-class-settings.php';
				self::$pv_options = new SF_Settings_API( self::$id, $this->title, 'woocommerce', __FILE__ );
				self::$pv_options->load_options( wcv_plugin_dir . 'classes/admin/settings/sf-options.php' );
			}
		}

		public function load_il8n() { 

			$domain = 'wcvendors';
		    
		    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		    //Place your custom translations into wp-content/languages/wc-vendors to be upgrade safe 
		    load_textdomain($domain, WP_LANG_DIR.'/wc-vendors/'.$domain.'-'.$locale.'.mo');
			
			load_plugin_textdomain( 'wcvendors', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		}


		/**
		 * Include core files
		 */
		public function include_core()
		{
			require_once wcv_plugin_dir . 'classes/class-queries.php';
			require_once wcv_plugin_dir . 'classes/class-vendors.php';
			require_once wcv_plugin_dir . 'classes/class-cron.php';
			require_once wcv_plugin_dir . 'classes/class-commission.php';
			require_once wcv_plugin_dir . 'classes/class-shipping.php';
			require_once wcv_plugin_dir . 'classes/front/class-vendor-cart.php';
			require_once wcv_plugin_dir . 'classes/front/dashboard/class-vendor-dashboard.php';
			require_once wcv_plugin_dir . 'classes/front/class-vendor-shop.php';
			require_once wcv_plugin_dir . 'classes/front/signup/class-vendor-signup.php';
			require_once wcv_plugin_dir . 'classes/front/orders/class-orders.php';
			require_once wcv_plugin_dir . 'classes/admin/emails/class-emails.php';
			require_once wcv_plugin_dir . 'classes/admin/class-product-meta.php';
			require_once wcv_plugin_dir . 'classes/admin/class-vendor-applicants.php';
			require_once wcv_plugin_dir . 'classes/admin/class-vendor-reports.php';
			require_once wcv_plugin_dir . 'classes/admin/class-admin-reports.php';
			require_once wcv_plugin_dir . 'classes/admin/class-admin-users.php';
			require_once wcv_plugin_dir . 'classes/admin/class-admin-page.php';
			require_once wcv_plugin_dir . 'classes/admin/class-vendor-admin-dashboard.php'; 
			require_once wcv_plugin_dir . 'classes/includes/class-wcv-shortcodes.php';


			if ( !function_exists( 'woocommerce_wp_text_input' ) && !is_admin() ) {
				include_once(WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php');
			}

			new WCV_Vendor_Shop;
			new WCV_Vendor_Cart;
			new WCV_Commission;
			new WCV_Shipping;
			new WCV_Cron;
			new WCV_Orders;
			new WCV_Vendor_Dashboard;
			new WCV_Product_Meta;
			new WCV_Vendor_Reports;
			new WCV_Admin_Setup;
			new WCV_Vendor_Admin_Dashboard; 
			new WCV_Admin_Reports;
			new WCV_Vendor_Applicants;
			new WCV_Admin_Users;
			new WCV_Emails;
			new WCV_Vendor_Signup;
			new WCV_Shortcodes; 
		}

		/** 
		*	Load plugin assets 
		*/ 
		public function include_assets(){

			$screen = get_current_screen(); 

			if ( in_array( $screen->id, array( 'edit-product' ) ) ) {
				wp_enqueue_script( 'wcv_quick-edit', wcv_assets_url. 'js/wcv-admin-quick-edit.js', array('jquery') );
			}

		}


		/**
		 * Include payment gateways
		 */
		public function include_gateways()
		{
			require_once wcv_plugin_dir . 'classes/gateways/PayPal_AdvPayments/paypal_ap.php';
			require_once wcv_plugin_dir . 'classes/gateways/PayPal_Masspay/class-paypal-masspay.php';
			require_once wcv_plugin_dir . 'classes/gateways/WCV_Gateway_Test/class-wcv-gateway-test.php';
		}


		/**
		 * Do an action when options are updated
		 *
		 * @param array   $options
		 * @param unknown $tabname
		 */
		public function option_updates( $options, $tabname )
		{
			// Change the vendor role capabilities
			if ( $tabname == sanitize_title(__( 'Capabilities', 'wcvendors' )) ) {
				$can_add          = $options[ 'can_submit_products' ];
				$can_edit         = $options[ 'can_edit_published_products' ];
				$can_submit_live  = $options[ 'can_submit_live_products' ];
				$can_view_reports = $options[ 'can_view_backend_reports' ];

				$args = array(
					'assign_product_terms'      => $can_add,
					'edit_products'             => $can_add || $can_edit,
					'edit_published_products'   => $can_edit,
					'delete_published_products' => $can_edit,
					'delete_products'           => $can_edit,
					'manage_product'            => $can_add,
					'publish_products'          => $can_submit_live,
					'read'                      => true,
					'read_products'             => $can_edit || $can_add,
					'upload_files'              => true,
					'import'                    => true,
					'view_woocommerce_reports'  => false,
				);

				remove_role( 'vendor' );
				
				add_role( 'vendor', __('Vendor', 'wcvendors'), $args );
			} // Update permalinks
			else if ( $tabname == sanitize_title(__( 'General', 'wcvendors' ) )) {
				$old_permalink = WC_Vendors::$pv_options->get_option( 'vendor_shop_permalink' );
				$new_permalink = $options[ 'vendor_shop_permalink' ];

				if ( $old_permalink != $new_permalink ) {
					update_option( WC_Vendors::$id . '_flush_rules', true );
				}
			}

			do_action( 'wcvendors_option_updates', $options, $tabname ); 

		}


		/**
		 *  If the settings are updated and the vendor page link has changed update permalinks 
		 *	@access public
		 *
		*/
		public function maybe_flush_permalinks()
		{
			if ( get_option( WC_Vendors::$id . '_flush_rules' ) ) {
				flush_rewrite_rules();
				update_option( WC_Vendors::$id . '_flush_rules', false );
			}
		}

		/**
		 *  Add links to plugin page to our external help site. 
		 *	@param $links - links array from action 
		 *	@param $file - file reference for this plugin 
		 *	@access public 
		 * 
		 */
		public static function plugin_row_meta( $links, $file ) {
			if ( $file == wcv_plugin_base ) {

				$row_meta = array(
	                            'docs' 		=> '<a href="http://www.wcvendors.com/kb/" target="_blank">'.__( 'Documentation/KB', 'wcvendors' ).'</a>',
	                            'help' 		=> '<a href="http://www.wcvendors.com/help/" target="_blank">'.__( 'Help Forums', 'wcvendors').'</a>',
	                            'support' 	=> '<a href="http://www.wcvendors.com/contact-us/" target="_blank">'.__( 'Paid Support', 'wcvendors' ).'</a>'
	                        );

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		/**
		 *  Add admin notices to ensure users are saving the settings correctly 
		 * 	@access public 
		 * 
		*/
		public function wcv_required_admin_notice(){
				global $current_user;

			if ( current_user_can( 'manage_options' ) ) {
	        		$current_user_id = $current_user->ID;

					if ( WC_Vendors::$pv_options->get_option( 'vendor_shop_permalink' ) == null  && ! get_user_meta( $current_user_id, 'wcv_shop_ignore_notice' ) ) {
						echo '<div class="updated">
					   	<p>'.sprintf (__('WC Vendors requires the Vendor shop page value be set <a href="%s">click here to set it.</a> | <a href="%s">Hide Notice</a>','wcvendors'), 'admin.php?page=wc_prd_vendor', esc_url( add_query_arg( 'wcv_shop_ignore_notice', '0' ) ) ).'</p>
						</div>';
					}

					$general_tab = ( isset( $_GET['tab'] ) && 'general' == $_GET['tab'] ) || !isset( $_GET['tab'] ) ? true : false; 

					if ( isset( $_GET['page'] ) && 'wc_prd_vendor' == $_GET['page'] && isset( $_GET[ 'settings-updated' ] ) && $general_tab == true && ! get_user_meta( $current_user_id, 'wcv_pl_ignore_notice' ) ) {
						echo '<div class="updated">
					   	<p>'.sprintf (__('You must save your permalinks once you have modified your vendor page. <a href="%s">click here to save</a>.  | <a href="%s">Hide Notice</a>','wcvendors'), 'options-permalink.php', esc_url( add_query_arg( 'wcv_pl_ignore_notice', '0' ) ) ).'</p>
						</div>';
					}
			}	
		}			
            
		/**
		 * Add user meta to remember ignore notices 
		 * @access public
         * 
		 */
		public function wcv_required_ignore_notices(){
			global $current_user;
    		$current_user_id = $current_user->ID;
    		
	        /* If user clicks to ignore the notice, add that to their user meta */
	        if ( isset( $_GET[ 'wcv_shop_ignore_notice' ] ) && '0' == $_GET[ 'wcv_shop_ignore_notice' ] ) {
	            add_user_meta( $current_user_id, 'wcv_shop_ignore_notice', 'true', true);
	    	}				
			if ( isset($_GET['wcv_pl_ignore_notice']) && '0' == $_GET['wcv_pl_ignore_notice'] ) {
			 	add_user_meta( $current_user_id, 'wcv_pl_ignore_notice', 'true' , true);
			}
		}


	}


	new WC_Vendors;

}