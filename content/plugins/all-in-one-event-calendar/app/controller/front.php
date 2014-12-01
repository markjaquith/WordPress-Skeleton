<?php

/**
 * The front controller of the plugin.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Controller
 */
class Ai1ec_Front_Controller {

	/**
	 * @var Ai1ec_Registry_Object The Object registry.
	 */
	protected $_registry;

	/**
	 * @var bool Whether the domain has alredy been loaded or not.
	 */
	protected $_domain_loaded = false;

	/**
	 * @var string The pagebase used by Ai1ec_Href_Helper.
	 */
	protected $_pagebase_for_href;

	/**
	 * @var Ai1ec_Request_Parser Instance of the request pa
	 */
	protected $_request;

	/**
	 * @var array
	 */
	protected $_default_theme;

	/**
	 * Initializes the default theme property.
	 */
	public function __construct() {
		// Initialize default theme.
		$this->_default_theme = array(
			'theme_dir'  => AI1EC_DEFAULT_THEME_PATH,
			'theme_root' => AI1EC_DEFAULT_THEME_ROOT,
			'theme_url'  => AI1EC_THEMES_URL . '/' . AI1EC_DEFAULT_THEME_NAME,
			'stylesheet' => AI1EC_DEFAULT_THEME_NAME,
			'legacy'     => false,
		);
	}

	/**
	 * Initialize the controller.
	 *
	 * @param Ai1ec_Loader $ai1ec_loader Instance of Ai1EC classes loader
	 *
	 * @return void
	 */
	public function initialize( $ai1ec_loader ) {
		ai1ec_start();
		$this->_init( $ai1ec_loader );
		$this->_initialize_dispatcher();
		$lessphp = $this->_registry->get( 'less.lessphp' );
		$lessphp->initialize_less_variables_if_not_set();
		$this->_registry->get( 'controller.shutdown' )
			->register( 'ai1ec_stop' );
		add_action( 'plugins_loaded', array( $this, 'register_extensions' ), 1 );
		add_action( 'after_setup_theme', array( $this, 'register_themes' ), 1 );
		add_action( 'init', array( $lessphp, 'invalidate_css_cache_if_requested' ) );
	}

	/**
	 * Let other objects access default theme
	 *
	 * @return array
	 */
	public function get_default_theme() {
		return $this->_default_theme;
	}

	/**
	 * Notify extensions and pass them instance of objects registry.
	 *
	 * @return void
	 */
	public function register_extensions() {
		do_action( 'ai1ec_loaded', $this->_registry );
	}

	/**
	 * Notify themes and pass them instance of objects registry.
	 *
	 * @return void
	 */
	public function register_themes() {
		do_action( 'ai1ec_after_themes_setup', $this->_registry );
	}

	/**
	 * Returns the registry object
	 *
	 * @param mixed $discard not used. Always return the registry.
	 *
	 * @return Ai1ec_Registry_Object
	 */
	public function return_registry( $discard ) {
		return $this->_registry;
	}

	/**
	 * Execute commands if our plugin must handle the request.
	 *
	 * @wp_hook init
	 *
	 * @return void
	 */
	public function route_request() {
		$this->_process_request();
		// get the resolver
		$resolver = $this->_registry->get(
			'command.resolver',
			$this->_request
		);
		// get the command
		$commands = $resolver->get_commands();
		// if we have a command
		if ( ! empty( $commands ) ) {
			foreach( $commands as $command ) {
				$result = $command->execute();
				if ( $command->stop_execution() ) {
					return $result;
				}
			}
		}
	}

	/**
	 * Initializes the URL router used by our plugin.
	 *
	 * @wp_hook init
	 *
	 * @return void
	 */
	public function initialize_router() {
		$settings            = $this->_registry->get( 'model.settings' );

		$cal_page            = $settings->get( 'calendar_page_id' );

		if (
			! $cal_page ||
			$cal_page < 1
		) { // Routing may not be affected in any way if no calendar page exists.
			return null;
		}
		$router              = $this->_registry->get( 'routing.router' );
		$localization_helper = $this->_registry->get( 'p28n.wpml' );
		$page_base          = '';
		$clang              = '';

		if ( $localization_helper->is_wpml_active() ) {
			$trans = $localization_helper
				->get_wpml_translations_of_page(
					$cal_page,
					true
				);
			$clang = $localization_helper->get_language();
			if ( isset( $trans[$clang] ) ) {
				$cal_page = $trans[$clang];
			}
		}
		$template_link_helper = $this->_registry->get( 'template.link.helper' );

		if ( ! get_post( $cal_page ) ) {
			return null;
		}

		$page_base = $template_link_helper->get_page_link(
			$cal_page
		);

		$page_base = Ai1ec_Wp_Uri_Helper::get_pagebase( $page_base );
		$page_link = 'index.php?page_id=' .
			$cal_page;
		$pagebase_for_href = Ai1ec_Wp_Uri_Helper::get_pagebase_for_links(
			get_page_link( $cal_page ),
			$clang
		);

		// save the pagebase to set up the factory later
		$application = $this->_registry->get( 'bootstrap.registry.application' );
		$application->set( 'calendar_base_page', $pagebase_for_href );
		$option = $this->_registry->get( 'model.option' );

		// If the calendar is set as the front page, disable permalinks.
		// They would not be legal under a Windows server. See:
		// https://issues.apache.org/bugzilla/show_bug.cgi?id=41441
		if (
			$option->get( 'permalink_structure' ) &&
			( int ) get_option( 'page_on_front' ) !==
			( int ) $settings->get( 'calendar_page_id' )
		) {
			$application->set( 'permalinks_enabled', true );
		}

		$router->asset_base( $page_base )
			->register_rewrite( $page_link );
	}

	/**
	 * Initialize the system.
	 *
	 * Perform all the inizialization needed for the system.
	 * Throws some uncatched exception for critical failures.
	 * Plugin will be disabled by the exception handler on those failures.
	 *
	 * @param Ai1ec_Loader $ai1ec_loader Instance of Ai1EC classes loader
	 *
	 * @throws Ai1ec_Constants_Not_Set_Exception
	 * @throws Ai1ec_Database_Update_Exception
	 * @throws Ai1ec_Database_Schema_Exception
	 *
	 * @return void Method does not return
	 */
	protected function _init( $ai1ec_loader ) {
		$exception = null;
		// Load the textdomain
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		try {
			// Initialize the registry object
			$this->_initialize_registry( $ai1ec_loader );
			$this->_registry->get( 'event.dispatcher' )->register_filter(
				'ai1ec_perform_scheme_update',
				array( 'database.datetime-migration', 'filter_scheme_update' )
			);
			// Load the css if needed
			$this->_load_css_if_needed();
			// Initialize the crons
			$this->_install_crons();
			// Register the activation hook
			$this->_initialize_schema();
			// set the default theme if not set
			$this->_add_default_theme_if_not_set();
			// check if custom theme is set
			if ( is_admin() ) {
				$this->_check_old_theme();
			}
		} catch ( Ai1ec_Constants_Not_Set_Exception $e ) {
			// This is blocking, throw it and disable the plugin
			$exception = $e;
		} catch ( Ai1ec_Database_Update_Exception $e ) {
			// Blocking throw it so that the plugin is disabled
			$exception = $e;
		} catch ( Ai1ec_Database_Schema_Exception $e ) {
			// Blocking throw it so that the plugin is disabled
			$exception = $e;
		} catch ( Ai1ec_Scheduling_Exception $e ) {
			// not blocking
		}

		if ( null !== $exception ) {
			throw $exception;
		}
	}

	/**
	 * Set the default theme if no theme is set, or populate theme info array if
	 * insufficient information is currently being stored.
	 *
	 * @uses apply_filters() Calls 'ai1ec_pre_save_current_theme' hook to allow
	 *       overwriting of theme information before being stored.
	 */
	protected function _add_default_theme_if_not_set() {
		$option = $this->_registry->get( 'model.option' );
		$theme  = $option->get( 'ai1ec_current_theme', array() );
		$update = false;
		// Theme setting is undefined; default to Vortex.
		if ( empty( $theme ) ) {
			$theme  = $this->_default_theme;
			$update = true;
		}
		// Legacy settings; in 1.x the active theme was stored as a bare string,
		// and they were located in a different folder than they are now.
		else if ( is_string( $theme ) ) {
			$theme_name  = strtolower( $theme );
			$core_themes = explode( ',', AI1EC_CORE_THEMES );
			$legacy      = ! in_array( $theme_name, $core_themes );

			if ( $legacy ) {
				$root = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . AI1EC_THEME_FOLDER;
				$url  = WP_CONTENT_URL . '/' . AI1EC_THEME_FOLDER . '/' . $theme_name;
			} else {
				$root = AI1EC_DEFAULT_THEME_ROOT;
				$url  = AI1EC_THEMES_URL . '/' . $theme_name;
			}
			// if it's from 1.x, move folders to avoid confusion
			if ( apply_filters( 'ai1ec_move_themes_to_backup', true ) ) {
				$this->_registry->get( 'theme.search' )
					->move_themes_to_backup( $core_themes );
			}
			// Ensure existence of theme directory.
			if ( ! is_dir( $root . DIRECTORY_SEPARATOR . $theme_name ) ) {
				// It's missing; something is wrong with this theme. Reset theme to
				// Vortex and warn the user accordingly.
				$option->set( 'ai1ec_current_theme', $this->_default_theme );
				$notification = $this->_registry->get( 'notification.admin' );
				$notification->store(
					sprintf(
						Ai1ec_I18n::__(
							'Your active calendar theme could not be properly initialized. The default theme has been activated instead. Please visit %s and try reactivating your theme manually.'
						),
						'<a href="' . admin_url( AI1EC_THEME_SELECTION_BASE_URL ) . '">' .
						Ai1ec_I18n::__( 'Calendar Themes' ) . '</a>'
					),
					'error',
					1
				);
			}

			$theme = array(
				'theme_dir'  => $root . DIRECTORY_SEPARATOR . $theme_name,
				'theme_root' => $root,
				'theme_url'  => $url,
				'stylesheet' => $theme_name,
				'legacy'     => $legacy,
			);
			$update = true;
		}
		// Ensure 'theme_url' is defined, as this property was added after the first
		// public beta release.
		else if ( ! isset( $theme['theme_url'] ) ) {
			if ( $theme['legacy'] ) {
				$theme['theme_url'] = WP_CONTENT_URL . '/' . AI1EC_THEME_FOLDER . '/' .
					$theme['stylesheet'];
			} else {
				$theme['theme_url'] = AI1EC_THEMES_URL . '/' . $theme['stylesheet'];
			}
			$update = true;
		}

		if ( $update ) {
			$theme = apply_filters( 'ai1ec_pre_save_current_theme', $theme );
			$option->set( 'ai1ec_current_theme', $theme );
		}
	}

	/**
	 * Adds actions handled by the front controller.
	 */
	protected function _add_front_controller_actions() {
		// Initialize router. I use add_action as the dispatcher would just add
		// overhead.
		add_action(
			'init',
			array( $this, 'initialize_router' ),
			PHP_INT_MAX - 1
		);
		add_action(
			'widgets_init',
			array( 'Ai1ec_View_Admin_Widget', 'register_widget' )
		);
		if ( isset( $_GET[Ai1ec_Controller_Javascript_Widget::WIDGET_PARAMETER] ) ) {
			$this->_registry->get( 'event.dispatcher' )->register_action(
				'init',
				array( 'controller.javascript-widget', 'render_js_widget' ),
				PHP_INT_MAX
			);
		}
		// Route the request.
		$action = 'template_redirect';
		if ( is_admin() ) {
			$action = 'init';
		}
		add_action( $action, array( $this, 'route_request' ) );
		add_filter( 'ai1ec_registry', array( $this, 'return_registry' ) );
	}

	/**
	 * Initialize the dispatcher.
	 *
	 * Complete this when writing the dispatcher.
	 *
	 * @return void
	 */
	protected function _initialize_dispatcher() {
		$dispatcher = $this->_registry->get( 'event.dispatcher' );
		$dispatcher->register_action(
			'init',
			array( 'post.custom-type', 'register' )
		);
		$this->_add_front_controller_actions();
		if ( isset( $_GET[Ai1ec_Javascript_Controller::LOAD_JS_PARAMETER] ) ) {
			$dispatcher->register_action(
				'wp_loaded',
				array( 'controller.javascript', 'render_js' )
			);
		}
		$dispatcher->register_action(
			'delete_post',
			array( 'model.event.trashing', 'delete' )
		);
		$dispatcher->register_action(
			'trashed_post',
			array( 'model.event.trashing', 'trash' )
		);
		$dispatcher->register_action(
			'untrashed_post',
			array( 'model.event.trashing', 'untrash' )
		);
		$dispatcher->register_action(
			'pre_http_request',
			array( 'http.request', 'pre_http_request' ),
			10,
			3
		);
		$dispatcher->register_action(
			'http_request_args',
			array( 'http.request', 'init_certificate' ),
			10,
			2
		);
		$dispatcher->register_action(
			'plugins_loaded',
			array( 'theme.loader', 'clean_cache_on_upgrade' ),
			PHP_INT_MAX
		);
		$dispatcher->register_filter(
			'get_the_excerpt',
			array( 'view.event.content', 'event_excerpt' ),
			11
		);
		remove_filter( 'the_excerpt', 'wpautop', 10 );
		$dispatcher->register_filter(
			'the_excerpt',
			array( 'view.event.content', 'event_excerpt_noautop' ),
			11
		);
		$dispatcher->register_filter(
			'robots_txt',
			array( 'robots.helper', 'rules' ),
			10,
			2
		);

		$dispatcher->register_filter(
			'ai1ec_dbi_debug',
			array( 'http.request', 'debug_filter' )
		);
		$dispatcher->register_filter(
			'ai1ec_dbi_debug',
			array( 'compatibility.cli', 'disable_db_debug' )
		);
		// editing a child instance
		if ( basename( $_SERVER['SCRIPT_NAME'] ) === 'post.php' ) {
			$dispatcher->register_action(
				'admin_action_editpost',
				array( 'model.event.parent', 'admin_init_post' )
			);
		}
		// post row action for parent/child
		$dispatcher->register_action(
			'post_row_actions',
			array( 'model.event.parent', 'post_row_actions' ),
			10,
			2
		);
		// Category colors
		$dispatcher->register_action(
			'events_categories_add_form_fields',
			array( 'view.admin.event-category', 'events_categories_add_form_fields' )
		);
		$dispatcher->register_action(
			'events_categories_edit_form_fields',
			array( 'view.admin.event-category', 'events_categories_edit_form_fields' )
		);
		$dispatcher->register_action(
			'created_events_categories',
			array( 'view.admin.event-category', 'created_events_categories' )
		);
		$dispatcher->register_action(
			'edited_events_categories',
			array( 'view.admin.event-category', 'edited_events_categories' )
		);
		$dispatcher->register_action(
			'manage_edit-events_categories_columns',
			array( 'view.admin.event-category', 'manage_event_categories_columns' )
		);
		$dispatcher->register_action(
			'manage_events_categories_custom_column',
			array( 'view.admin.event-category', 'manage_events_categories_custom_column' ),
			10,
			3
		);

		// register ICS cron action
		$dispatcher->register_action(
			Ai1ecIcsConnectorPlugin::HOOK_NAME,
			array( 'calendar-feed.ics', 'cron' )
		);

		if ( is_admin() ) {
			// get the repeat box
			$dispatcher->register_action(
				'wp_ajax_ai1ec_get_repeat_box',
				array( 'view.admin.get-repeat-box', 'get_repeat_box' )
			);
			// add dismissable notice handler
			$dispatcher->register_action(
				'wp_ajax_ai1ec_dismiss_notice',
				array( 'notification.admin', 'dismiss_notice' )
			);
			// save rrurle and convert it to text
			$dispatcher->register_action(
				'wp_ajax_ai1ec_rrule_to_text',
				array( 'view.admin.get-repeat-box', 'convert_rrule_to_text' )
			);
			// tracking opt in ai1ec_tracking
			$dispatcher->register_action(
				'wp_ajax_ai1ec_tracking',
				array( 'controller.export', 'track_optin' )
			);
			// taxonomy filter
			$dispatcher->register_action(
				'restrict_manage_posts',
				array( 'view.admin.all-events', 'taxonomy_filter_restrict_manage_posts' )
			);
			$dispatcher->register_action(
				'parse_query',
				array( 'view.admin.all-events', 'taxonomy_filter_post_type_request' )
			);
			$dispatcher->register_action(
				'admin_menu',
				array( 'view.admin.calendar-feeds', 'add_page' )
			);
			$dispatcher->register_action(
				'current_screen',
				array( 'view.admin.calendar-feeds', 'add_meta_box' )
			);
			$dispatcher->register_action(
				'admin_menu',
				array( 'view.admin.add-ons', 'add_page' )
			);
			$dispatcher->register_action(
				'admin_menu',
				array( 'view.admin.theme-switching', 'add_page' )
			);
			$dispatcher->register_action(
				'admin_menu',
				array( 'view.admin.theme-options', 'add_page' )
			);
			$dispatcher->register_action(
				'current_screen',
				array( 'view.admin.theme-options', 'add_meta_box' )
			);
			$dispatcher->register_action(
				'admin_menu',
				array( 'view.admin.settings', 'add_page' )
			);
			$dispatcher->register_action(
				'current_screen',
				array( 'view.admin.settings', 'add_meta_box' )
			);
			$dispatcher->register_action(
				'init',
				array( 'controller.javascript', 'load_admin_js' )
			);
			$dispatcher->register_action(
				'wp_ajax_ai1ec_add_ics',
				array( 'calendar-feed.ics', 'add_ics_feed' )
			);
			$dispatcher->register_action(
				'wp_ajax_ai1ec_delete_ics',
				array( 'calendar-feed.ics', 'delete_feeds_and_events' )
			);
			$dispatcher->register_action(
				'wp_ajax_ai1ec_update_ics',
				array( 'calendar-feed.ics', 'update_ics_feed' )
			);
			$dispatcher->register_action(
				'network_admin_notices',
				array( 'notification.admin', 'send' )
			);
			$dispatcher->register_action(
				'admin_notices',
				array( 'notification.admin', 'send' )
			);
			$dispatcher->register_action(
				'admin_footer-edit.php',
				array( 'clone.renderer-helper', 'duplicate_custom_bulk_admin_footer' )
			);
			$dispatcher->register_filter(
				'post_row_actions',
				array( 'clone.renderer-helper', 'duplicate_post_make_duplicate_link_row' ),
				10,
				2
			);
			$dispatcher->register_action(
				'add_meta_boxes',
				array( 'view.admin.add-new-event', 'event_meta_box_container' )
			);
			$dispatcher->register_action(
				'add_meta_boxes',
				array( 'view.admin.add-new-event', 'event_banner_meta_box_container' )
			);
			$dispatcher->register_action(
				'edit_form_after_title',
				array( 'view.admin.add-new-event', 'event_inline_alert' )
			);
			$dispatcher->register_action(
				'save_post',
				array( 'model.event.creating', 'save_post' ),
				10,
				2
			);
			$dispatcher->register_action(
				'manage_ai1ec_event_posts_custom_column',
				array( 'view.admin.all-events', 'custom_columns' ),
				10,
				2
			);
			$dispatcher->register_filter(
				'manage_ai1ec_event_posts_columns',
				array( 'view.admin.all-events', 'change_columns' )
			);
			$dispatcher->register_filter(
				'manage_edit-ai1ec_event_sortable_columns',
				array( 'view.admin.all-events', 'sortable_columns' )
			);
			$dispatcher->register_filter(
				'posts_orderby',
				array( 'view.admin.all-events', 'orderby' ),
				10,
				2
			);
			$dispatcher->register_filter(
				'post_updated_messages',
				array( 'view.event.post', 'post_updated_messages' )
			);
			add_action( 'admin_head', array( $this, 'admin_head' ) );
			$dispatcher->register_action(
				'plugin_action_links_' . AI1EC_PLUGIN_BASENAME,
				array( 'view.admin.nav', 'plugin_action_links' )
			);
			$dispatcher->register_action(
				'wp_ajax_ai1ec_rescan_cache',
				array( 'twig.cache', 'rescan' )
			);
			$dispatcher->register_action(
				'admin_init',
				array( 'environment.check', 'run_checks' )
			);
			$dispatcher->register_action(
				'activated_plugin',
				array( 'environment.check', 'check_addons_activation' )
			);
			$dispatcher->register_filter(
				'upgrader_post_install',
				array( 'environment.check', 'check_bulk_addons_activation' )
			);
			// Widget Creator
			$dispatcher->register_action(
				'admin_enqueue_scripts',
				array( 'css.admin', 'admin_enqueue_scripts' )
			);
			$dispatcher->register_action(
				'current_screen',
				array( 'view.admin.widget-creator', 'add_meta_box' )
			);
			$dispatcher->register_action(
				'admin_menu',
				array( 'view.admin.widget-creator', 'add_page' )
			);

		} else { // ! is_admin()
			$dispatcher->register_shortcode(
				'ai1ec',
				array( 'view.calendar.shortcode', 'shortcode' )
			);
			$dispatcher->register_action(
				'after_setup_theme',
				array( 'theme.loader', 'execute_theme_functions' )
			);
			$dispatcher->register_action(
				'the_post',
				array( 'post.content', 'check_content' ),
				PHP_INT_MAX
			);
			$dispatcher->register_action(
				'send_headers',
				array( 'request.redirect', 'handle_categories_and_tags' )
			);
		}
	}
	/**
	 * Outputs menu icon between head tags
	 */
	public function admin_head() {
		global $wp_version;
		$argv = array(
			'before_font_icons'    => version_compare( $wp_version, '3.8', '<' ),
			'admin_theme_img_url'  => AI1EC_ADMIN_THEME_IMG_URL,
			'admin_theme_font_url' => AI1EC_ADMIN_THEME_FONT_URL,
		);
		$this->_registry->get( 'theme.loader' )
			->get_file( 'timely-menu-icon.twig', $argv, true )
			->render();
	}

	/**
	 * _add_defaults method
	 *
	 * Add (merge) default options to given query variable.
	 *
	 * @param string settingsquery variable to ammend
	 *
	 * @return string|NULL Modified variable values or NULL on failure
	 *
	 * @global    Ai1ec_Settings $ai1ec_settings Instance of settings object
	 *                                           to pull data from
	 * @staticvar array          $mapper         Mapping of query names to
	 *                                           default in settings
	 */
	protected function _add_defaults( $name ) {
		$settings = $this->_registry->get( 'model.settings' );
		static $mapper = array(
			'cat' => 'categories',
			'tag' => 'tags',
		);
		$rq_name = 'ai1ec_' . $name . '_ids';
		if (
			! isset( $mapper[$name] ) ||
			! array_key_exists( $rq_name, $this->_request )
		) {
			return NULL;
		}
		$options  = explode( ',', $this->_request[$rq_name] );
		$property = 'default_' . $mapper[$name];
		$options  = array_merge(
			$options,
			$settings->get( $property )
		);
		$filtered = array();
		foreach ( $options as $item ) { // avoid array_filter + is_numeric
			$item = (int)$item;
			if ( $item > 0 ) {
				$filtered[] = $item;
			}
		}
		unset( $options );
		if ( empty( $filtered ) ) {
			return NULL;
		}
		return implode( ',', $filtered );
	}

	/**
	 * Process_request function.
	 *
	 * Initialize/validate custom request array, based on contents of $_REQUEST,
	 * to keep track of this component's request variables.
	 *
	 * @return void
	 **/
	protected function _process_request() {
		$settings       = $this->_registry->get( 'model.settings' );
		$this->_request = $this->_registry->get( 'http.request.parser' );
		$aco            = $this->_registry->get( 'acl.aco' );
		$page_id        = $settings->get( 'calendar_page_id' );
		if (
			! $aco->is_admin() &&
			$page_id &&
			is_page( $page_id )
		) {
			foreach ( array( 'cat', 'tag' ) as $name ) {
				$implosion = $this->_add_defaults( $name );
				if ( $implosion ) {
					$this->request['ai1ec_' . $name . '_ids'] = $implosion;
					$_REQUEST['ai1ec_' . $name . '_ids']      = $implosion;
				}
			}
		}
	}

	/**
	 * Initialize cron functions.
	 *
	 * @throws Ai1ec_Scheduling_Exception
	 *
	 * @return void
	 */
	protected function _install_crons() {
		$scheduling = $this->_registry->get( 'scheduling.utility' );
		$allow      = $this->_registry->get( 'model.settings' )
				->get( 'allow_statistics' );
		$correct    = false;
		// install the cron for stats
		$hook_name = 'ai1ec_n_cron';
		// if stats are disabled, cancel the cron
		if ( false === $allow ) {
			$scheduling->delete( $hook_name );
			$correct = true;
		} else {
			$correct = $scheduling->reschedule(
				$hook_name,
				AI1EC_N_CRON_FREQ,
				AI1EC_N_CRON_VERSION
			);
			$this->_registry->get( 'event.dispatcher' )
				->register_action(
					$hook_name,
					array( 'controller.export', 'n_cron' )
				);
		}
		if ( false === $correct ) {
			throw new Ai1ec_Scheduling_Exception(
				'Some CRON function might not have been installed'
			);
		}
	}

	/**
	 * Initialize the registry object.
	 *
	 * @param Ai1ec_Loader $ai1ec_loader Instance of Ai1EC classes loader
	 *
	 * @return void Method does not return
	 */
	protected function _initialize_registry( $ai1ec_loader ) {
		global $ai1ec_registry;
		$this->_registry = new Ai1ec_Registry_Object( $ai1ec_loader );
		Ai1ec_Time_Utility::set_registry( $this->_registry );
		$ai1ec_registry  = $this->_registry;
	}

	/**
	 * Loads the CSS for the plugin
	 *
	 */
	protected function _load_css_if_needed() {
		// ==================================
		// = Add the hook to render the css =
		// ==================================
		if ( isset( $_GET[Ai1ec_Css_Frontend::QUERY_STRING_PARAM] ) ) {
			// we need to wait for the extension to be registered if the css
			// needs to be compiled. Will find a better way when compiling css.
			$css_controller = $this->_registry->get( 'css.frontend' );
			add_action( 'plugins_loaded', array( $css_controller, 'render_css' ), 2 );
		}
	}

	/**
	 * Load the texdomain for the plugin.
	 *
	 * @wp_hook plugins_loaded
	 *
	 * @return void
	 */
	public function load_textdomain() {
		if ( false === $this->_domain_loaded ) {
			load_plugin_textdomain(
				AI1EC_PLUGIN_NAME, false, AI1EC_LANGUAGE_PATH
			);
			$this->_domain_loaded = true;
		}
	}

	/**
	 * Check if the schema is up to date.
	 *
	 * @throws Ai1ec_Database_Schema_Exception
	 * @throws Ai1ec_Database_Update_Exception
	 *
	 * @return void
	 */
	protected function _initialize_schema() {
		$option     = $this->_registry->get( 'model.option' );
		$schema_sql = $this->get_current_db_schema();
		$version    = sha1( $schema_sql );
		// If existing DB version is not consistent with current plugin's version,
		// or does not exist, then create/update table structure using dbDelta().
		if ( $option->get( 'ai1ec_db_version' ) != $version ) {

			$errors = $this->_registry->get( 'database.applicator' )
				->check_db_consistency_for_date_migration() ;
			if ( ! empty( $errors ) ) {
				$message = Ai1ec_I18n::__(
					'Your database is found to be corrupt. Likely previous update has failed. Please restore All-in-One Event Calendar tables from a backup and retry.<br>Following errors were found:<br>%s'
				);
				$message = sprintf( $message, implode( $errors, '<br>' ) );
				throw new Ai1ec_Database_Update_Exception( $message );
			}
			$this->_registry->get( 'database.applicator' )
				->remove_instance_duplicates();

			if (
				apply_filters( 'ai1ec_perform_scheme_update', true ) &&
				$this->_registry->get( 'database.helper' )->apply_delta(
					$schema_sql
				)
			) {
				$option->set( 'ai1ec_db_version', $version );
			} else {
				throw new Ai1ec_Database_Update_Exception();
			}

			// If the schema structure upgrade is complete move contents
			$categories_key = 'ai1ec_category_meta_ported';
			if ( ! $option->get( $categories_key ) ) {
				$this->_migrate_categories_meta();
				$option->set( $categories_key, true );
			}
		}
	}

	/**
	 * Transform categories meta information.
	 *
	 * Use new `meta` table instead of legacy `colors` table.
	 *
	 * @return void Method does not return.
	 */
	protected  function _migrate_categories_meta() {
		$db         = $this->_registry->get( 'dbi.dbi' );
		$table_name = $db->get_table_name( 'ai1ec_event_category_colors' );
		$db_h       = $this->_registry->get( 'database.helper' );
		if ( $db_h->table_exists( $table_name ) ) { // if old table exists otherwise ignore it
			// Migrate color information
			$dest_table = $db->get_table_name( 'ai1ec_event_category_meta' );
			$colors     = $db->select(
				$table_name,
				array( 'term_id', 'term_color'),
				ARRAY_A
			);
			if ( ! empty( $colors ) ) {
				foreach ( $colors as $color ) {
					$db->insert( $dest_table, $color );
				}
			}
			// Drop the old table
			$db->query( 'DROP TABLE IF EXISTS ' . $table_name );
		}
	}

	/**
	 * Get current database schema as a multi SQL statement.
	 *
	 * @return string Multiline SQL statement.
	 */
	public function get_current_db_schema() {
		$dbi = $this->_registry->get( 'dbi.dbi' );
		// =======================
		// = Create table events =
		// =======================
		$table_name = $dbi->get_table_name( 'ai1ec_events' );
		$sql = "CREATE TABLE $table_name (
				post_id bigint(20) NOT NULL,
				start int(10) UNSIGNED NOT NULL,
				end int(10) UNSIGNED,
				timezone_name varchar(50),
				allday tinyint(1) NOT NULL,
				instant_event tinyint(1) NOT NULL DEFAULT 0,
				recurrence_rules longtext,
				exception_rules longtext,
				recurrence_dates longtext,
				exception_dates longtext,
				venue varchar(255),
				country varchar(255),
				address varchar(255),
				city varchar(255),
				province varchar(255),
				postal_code varchar(32),
				show_map tinyint(1),
				contact_name varchar(255),
				contact_phone varchar(32),
				contact_email varchar(128),
				contact_url varchar(255),
				cost varchar(255),
				ticket_url varchar(255),
				ical_feed_url varchar(255),
				ical_source_url varchar(255),
				ical_organizer varchar(255),
				ical_contact varchar(255),
				ical_uid varchar(255),
				show_coordinates tinyint(1),
				latitude decimal(20,15),
				longitude decimal(20,15),
				force_regenerate tinyint(1) NOT NULL DEFAULT 0,
				PRIMARY KEY  (post_id),
				KEY feed_source (ical_feed_url)
				) CHARACTER SET utf8;";

		// ==========================
		// = Create table instances =
		// ==========================
		$table_name = $dbi->get_table_name( 'ai1ec_event_instances' );
		$sql .= "CREATE TABLE $table_name (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				post_id bigint(20) NOT NULL,
				start int(10) UNSIGNED NOT NULL,
				end int(10) UNSIGNED NOT NULL,
				PRIMARY KEY  (id),
				UNIQUE KEY evt_instance (post_id,start)
				) CHARACTER SET utf8;";

		// ================================
		// = Create table category colors =
		// ================================
		$table_name = $dbi->get_table_name( 'ai1ec_event_category_meta' );
		$sql .= "CREATE TABLE $table_name (
			term_id bigint(20) NOT NULL,
			term_color varchar(255) NOT NULL,
			term_image varchar(254) NULL DEFAULT NULL,
			PRIMARY KEY  (term_id)
			) CHARACTER SET utf8;";

		return $sql;
	}

	/**
	 * Performs run-once check if calendar is using theme outside core directory
	 * what may mean that it is old format theme.
	 *
	 * @return void Method does not return.
	 */
	protected function _check_old_theme() {
		$option = $this->_registry->get( 'model.option' );
		if ( true === (bool)$option->get( 'ai1ec_fer_checked', false ) ) {
			return;
		}
		$cur_theme  = $option->get( 'ai1ec_current_theme', array() );
		$theme_root = dirname( AI1EC_DEFAULT_THEME_ROOT );
		if (
			! isset( $cur_theme['theme_root'] ) ||
			$theme_root === dirname( $cur_theme['theme_root'] )
		) {
			$option->set( 'ai1ec_fer_checked', true );
			return;
		}
		$this->_registry->get( 'notification.admin' )->store(
			Ai1ec_I18n::__(
				'You may be using a legacy custom calendar theme. If you have problems viewing the calendar, please read <a href="https://time.ly/">this article</a>.'
			),
			'error',
			0,
			array( Ai1ec_Notification_Admin::RCPT_ADMIN ),
			true
		);
		$option->set( 'ai1ec_fer_checked', true );
	}

}
