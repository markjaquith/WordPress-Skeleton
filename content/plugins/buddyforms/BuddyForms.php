<?php

/*
 Plugin Name: BuddyForms
 Plugin URI:  http://buddyforms.com
 Description: Form Magic and Collaborative Publishing for WordPress. With Frontend Editing and Drag-and-Drop Form Builder.
 Version: 1.5.1
 Author: Sven Lehnert
 Author URI: https://profiles.wordpress.org/svenl77
 Licence: GPLv3
 Network: false

 *****************************************************************************
 *
 * This script is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA
 *
 ****************************************************************************
 */

class BuddyForms {

	/**
	 * @var string
	 */
	public $version = '1.5.1';

	/**
	 * Initiate the class
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function __construct() {

		$this->load_constants();

		add_action( 'init'					, array($this, 'init_hook')						, 1, 1);
		add_action( 'init'					, array($this, 'includes')						, 4, 1);
		add_action( 'init'					, array($this, 'buddyforms_update_db_check')	, 10  );

		add_action( 'plugins_loaded'		, array($this, 'load_plugin_textdomain'));

		add_action( 'admin_enqueue_scripts'	, array($this, 'buddyforms_admin_style')		, 1, 1);
		add_action( 'admin_enqueue_scripts'	, array($this, 'buddyforms_admin_js')			, 2, 1);
		add_action( 'admin_footer'			, array($this, 'buddyforms_admin_js_footer')	, 2, 1);
		add_action( 'template_redirect'		, array($this, 'buddyform_front_js_loader')		, 2, 1);

	}

	/**
	 * Defines buddyforms_init action
	 *
	 * This action fires on WP's init action and provides a way for the rest of WP,
	 * as well as other dependent plugins, to hook into the loading process in an
	 * orderly fashion.
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function init_hook() {
		global $buddyforms;
		$this->set_globals();
		do_action('buddyforms_init');
	}

	/**
	 * Defines constants needed throughout the plugin.
	 *
	 * These constants can be overridden in bp-custom.php or wp-config.php.
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function load_constants() {

		define('BUDDYFORMS_VERSION', $this->version);

		// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
		define( 'BUDDYFORMS_STORE_URL', 'https://buddyforms.com/' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

		// the name of your product. This should match the download name in EDD exactly
		define( 'BUDDYFORMS_EDD_ITEM_NAME', 'BuddyForms' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file


		if (!defined('BUDDYFORMS_PLUGIN_URL'))
            define('BUDDYFORMS_PLUGIN_URL', plugins_url('/',__FILE__));

		if (!defined('BUDDYFORMS_INSTALL_PATH'))
			define('BUDDYFORMS_INSTALL_PATH', dirname(__FILE__) . '/');

		if (!defined('BUDDYFORMS_INCLUDES_PATH'))
			define('BUDDYFORMS_INCLUDES_PATH', BUDDYFORMS_INSTALL_PATH . 'includes/');

		if (!defined('BUDDYFORMS_TEMPLATE_PATH'))
			define('BUDDYFORMS_TEMPLATE_PATH', BUDDYFORMS_INSTALL_PATH . 'templates/');

	}

	/**
	 * Setup all globals
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	static function set_globals() {
		global $buddyforms;

		$buddyforms = get_option('buddyforms_forms');
		$buddyforms = apply_filters('buddyforms_set_globals', $buddyforms);

		return $buddyforms;
	}

	/**
	 * Include files needed by BuddyForms
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function includes() {

		if(!function_exists('PFBC_Load'))
			require_once( BUDDYFORMS_INCLUDES_PATH . '/resources/pfbc/Form.php' );

        require_once( BUDDYFORMS_INCLUDES_PATH . 'functions.php' );
        require_once( BUDDYFORMS_INCLUDES_PATH . 'the-content.php' );
        require_once( BUDDYFORMS_INCLUDES_PATH . 'rewrite-roles.php' );

        require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form.php');
        require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-render.php');
        require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-ajax.php');
        require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-elements.php');
		require_once( BUDDYFORMS_INCLUDES_PATH . 'form/form-control.php');
		require_once( BUDDYFORMS_INCLUDES_PATH . 'revisions.php' );

		require_once( BUDDYFORMS_INCLUDES_PATH . 'shortcodes.php' );
		require_once( BUDDYFORMS_INCLUDES_PATH . 'wp-mail.php' );

		if (is_admin()){

			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/admin-ajax.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/admin-post-type.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/admin-settings.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/add-ons.php');

			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/form-builder-elements.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/mail-notification.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/roles-and-capabilities.php');

			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-select-form.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-form-elements.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-form-setup.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-form-header.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-form-footer.php');
			require_once( BUDDYFORMS_INCLUDES_PATH . '/admin/form-builder/meta-boxes/metabox-default-sidebar.php');


			if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
				// load our custom updater
				include( BUDDYFORMS_INCLUDES_PATH . '/resources/edd/EDD_SL_Plugin_Updater.php' );
			}

		}


	}

	/**
	 * Load the textdomain for the plugin
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain('buddyforms', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	/**
	 * Enqueue the needed CSS for the admin screen
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	function buddyforms_admin_style($hook_suffix) {
		global $post;

		if(
			(isset($post) && $post->post_type == 'buddyforms' && isset($_GET['action']) && $_GET['action'] == 'edit'
			|| isset($post) && $post->post_type == 'buddyforms' && $hook_suffix == 'post-new.php')
			//|| isset($_GET['post_type']) && $_GET['post_type'] == 'buddyforms'
			|| $hook_suffix == 'buddyforms_page_bf_add_ons'
		) {

			if ( is_rtl() ) {
				wp_enqueue_style(	'style-rtl',	plugins_url('assets/admin/css/admin-rtl.css', __FILE__) );
			}

			wp_enqueue_style('bootstrapcss', plugins_url('assets/admin/css/bootstrap.css', __FILE__) );
			wp_enqueue_style('buddyforms_admin_css', plugins_url('assets/admin/css/admin.css', __FILE__) );

			// load the tk_icons
			wp_enqueue_style( 'tk_icons', plugins_url('/includes/resources/tk_icons/style.css', __FILE__) );

		}
		// load the tk_icons
		wp_enqueue_style( 'tk_icons', plugins_url('/includes/resources/tk_icons/style.css', __FILE__) );
	}

	/**
	 * Enqueue the needed JS for the admin screen
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	function buddyforms_admin_js($hook_suffix) {
		global $post;

		if(
			(isset($post) && $post->post_type == 'buddyforms' && isset($_GET['action']) && $_GET['action'] == 'edit'
				|| isset($post) && $post->post_type == 'buddyforms' && $hook_suffix == 'post-new.php')
			//|| isset($_GET['post_type']) && $_GET['post_type'] == 'buddyforms'
			|| $hook_suffix == 'buddyforms_page_bf_add_ons'
		) {
				wp_register_script('buddyforms_admin_js', plugins_url('assets/admin/js/admin.js', __FILE__));
				$admin_text_array = array(
					'check' => __( 'Check all', 'buddyforms' ),
					'uncheck' => __( 'Uncheck all', 'buddyforms' )
				);
				wp_localize_script( 'buddyforms_admin_js', 'admin_text', $admin_text_array );
				wp_enqueue_script( 'buddyforms_admin_js' );

				wp_enqueue_script('bootstrapjs', plugins_url('assets/admin/js/bootstrap.js', __FILE__), array('jquery') );
				wp_enqueue_script('jQuery');
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('jquery-ui-accordion');

				wp_enqueue_script( 'buddyforms-select2-js', plugins_url('includes/resources/select2/select2.min.js', __FILE__) , array( 'jquery' ), '3.5.2' );
				wp_enqueue_style( 'buddyforms-select2-css',plugins_url('includes/resources/select2/select2.css', __FILE__));

			}
	}
	/**
	 * Enqueue the needed JS for the admin screen
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	function buddyforms_admin_js_footer() {
		global $post, $hook_suffix;

		if(
			(isset($post)
				&& $post->post_type == 'buddyforms'
				&& isset($_GET['action']) &&  $_GET['action'] == 'edit'
				|| isset($post) && $post->post_type == 'buddyforms'
			)
		) {
		?>
		<script>!function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!0,baseUrl:"http://buddyforms.helpscoutdocs.com/"},contact:{enabled:!0,formId:"44c14297-6391-11e5-8846-0e599dc12a51"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});</script>
		<?php
	}

	}
	/**
	 * Enqueue the needed JS for the form in the frontend
	 *
	 * @package buddyforms
	 * @since 0.1-beta
	 */
	function buddyform_front_js_loader(){
		global $post, $wp_query, $buddyforms;

		$found = false;

		// check the post content for the short code
		if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'buddyforms_form') )
			$found = true;

		if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'buddyforms_list_all') )
			$found = true;

		if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'bf') )
			$found = true;

		if(isset($wp_query->query['bf_action']))
			$found = true;

		$found = apply_filters('buddyforms_front_js_css_loader', $found);

		if($found)
			BuddyForms::buddyform_front_js();

	}
	function buddyform_front_js() {

		do_action('buddyforms_front_js_css_enqueue');

		wp_enqueue_script( 'jquery' );
        //wp_enqueue_script( 'jquery-form' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widgets' );
		wp_enqueue_script( 'jquery-ui-datepicker' );

        wp_enqueue_script( 'jquery-validation', plugins_url('assets/resources/jquery.validate.min.js', __FILE__) , array( 'jquery' ) );

        wp_enqueue_script( 'buddyforms-select2-js', plugins_url('includes/resources/select2/select2.min.js', __FILE__) , array( 'jquery' ), '3.5.2' );
        wp_enqueue_style( 'buddyforms-select2-css',plugins_url('includes/resources/select2/select2.css', __FILE__));

		wp_enqueue_script( 'buddyforms-jquery-ui-timepicker-addon-js',	plugins_url('includes/resources/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.js', __FILE__), array('jquery-ui-core' ,'jquery-ui-datepicker', 'jquery-ui-slider') );
		wp_enqueue_style( 'buddyforms-jquery-ui-timepicker-addon-css',	plugins_url('includes/resources/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.css', __FILE__));

		wp_enqueue_script( 'buddyforms-js', plugins_url('assets/js/buddyforms.js', __FILE__),	array('jquery-ui-core' ,'jquery-ui-datepicker', 'jquery-ui-slider') );

		wp_enqueue_media();
		wp_enqueue_script( 'media-uploader-js', plugins_url('assets/js/media-uploader.js', __FILE__),	array('jquery') );

		wp_enqueue_style(	'buddyforms-the-loop-css', plugins_url('assets/css/the-loop.css', __FILE__));
		wp_enqueue_style(	'buddyforms-the-form-css', plugins_url('assets/css/the-form.css', __FILE__));

	}

	function buddyforms_update_db_check() {
		$buddyforms_old = get_option('buddyforms_options');

		if(!$buddyforms_old)
			return;

		update_option('buddyforms_options_old', $buddyforms_old);

		foreach($buddyforms_old['buddyforms'] as $key => $form ){
			$bf_forms_args = array(
				'post_title' 		=> $form['name'],
				'post_type' 		=> 'buddyforms',
				'post_status' 		=> 'publish',
			);

			// Insert the new form
			$post_id = wp_insert_post( $bf_forms_args, true );
			$form['id'] = $post_id;

			update_post_meta($post_id, '_buddyforms_options', $form);

			// Update the option _buddyforms_forms used to reduce queries
			$buddyforms_forms = get_option('buddyforms_forms');

			$buddyforms_forms[$form['slug']] = $form;
			update_option('buddyforms_forms', $buddyforms_forms);

		}

		update_option('buddyforms_version', BUDDYFORMS_VERSION);

		delete_option('buddyforms_options');

		buddyforms_attached_page_rewrite_rules(TRUE);
	}

}

$GLOBALS['buddyforms_new'] = new BuddyForms();

function buddyforms_edd_plugin_updater() {

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'buddyforms_edd_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( BUDDYFORMS_STORE_URL, __FILE__, array(
			'version' 	=> BUDDYFORMS_VERSION, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => BUDDYFORMS_EDD_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'Sven Lehnert',  // author of this plugin
			'url'       => home_url()
		)
	);

	//print_r($edd_updater);

}
add_action( 'admin_init', 'buddyforms_edd_plugin_updater', 0 );
