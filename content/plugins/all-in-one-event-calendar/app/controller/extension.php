<?php

/**
 * Basic extension controller.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Controller
 */
abstract class Ai1ec_Base_Extension_Controller {

	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected $_registry;

	/**
	 * @var array
	 */
	protected $_settings;

	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected static $_registry_static;

	/**
	 * @var array
	 */
	protected static $_settings_static;

	/**
	 * @var array
	 */
	protected static $_schema;

	/**
	 * Get the long name of the extension
	 */
	abstract public function get_name();

	/**
	 * Get the machine name of the extension
	 */
	abstract public function get_machine_name();

	/**
	 * Get the version of the extension
	 */
	abstract public function get_version();

	/**
	 * Get the name of the main plugin file
	 */
	abstract public function get_file();

	/**
	 * Add extension specific settings
	 */
	abstract protected function _get_settings();

	/**
	 * Register action/filters/shortcodes for the extension
	 *
	 * @param Ai1ec_Event_Dispatcher $dispatcher
	 */
	abstract protected function _register_actions(
		Ai1ec_Event_Dispatcher $dispatcher
	);

	/**
	 * Perform the basic compatibility check. 
	 * 
	 * @param string $ai1ec_version
	 * 
	 * @return boolean
	 */
	public function check_compatibility( $ai1ec_version ) {
		return version_compare(
			$ai1ec_version,
			$this->minimum_core_required(),
			'>='
		);
	}

	/**
	 * @return string
	 */
	public function minimum_core_required() {
		return '2.0.7';
	}
	/**
	 * Removes options when uninstalling the plugin.
	 */
	public static function on_uninstall() {
		global $wpdb;
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$settings = self::$_registry_static->get( 'model.settings' );
		foreach ( self::$_settings_static as $name => $params ) {
			$settings->remove_option( $name );
		}
		$schema = self::$_schema;
		foreach ( $schema['tables'] as $table_name ) {
			// Delete table events
			$wpdb->query( 'DROP TABLE IF EXISTS ' . $table_name );
		}
	}

	/**
	 * Public constructor
	 */
	public function __construct() {
		global $wpdb;
		self::$_schema          = $this->_get_schema( $wpdb->prefix );
		$settings               = $this->_get_settings();
		$this->_settings        = $settings;
		self::$_settings_static = $settings;
	}

	/**
	 * initialize the extension. 
	 */
	public function init( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
		// static properties are needed as uninstall hook must be static
		// http://wpseek.com/register_uninstall_hook/
		self::$_registry_static = $registry;
		register_deactivation_hook(
			$this->get_file(),
			array( $this, 'on_deactivation' )
		);

		$this->_install_schema( $registry );
		$this->_register_actions( $registry->get( 'event.dispatcher' ) );
		$this->_add_settings( $registry->get( 'model.settings' ) );
		$this->_perform_upgrade( $registry );
		if ( method_exists( $this, 'initialize_licence_actions' ) ) {
			$this->initialize_licence_actions();
		}
	}

	/**
	 * Hides settings on deactivation.
	 */
	public function on_deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin        = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		$referer       = 'deactivate-plugin_' . $plugin;
		// if we are disabling the plugin in the exception handler, this can't be done.
		// but i want to disable options
		if ( function_exists( '_get_list_table' ) ) {
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			$action        = $wp_list_table->current_action();
			if ( 'deactivate-selected' === $action ) {
				$referer = 'bulk-plugins';
			}
			check_admin_referer( $referer );
		}
		$settings = $this->_registry->get( 'model.settings' );
		foreach ( $this->_settings as $name => $params ) {
			$settings->hide_option( $name );
		}
	}

	/**
	 * Show the settings
	 */
	public function show_settings( Ai1ec_Registry_Object $registry ) {
		$settings = $registry->get( 'model.settings' );
		foreach ( $this->_settings as $name => $params ) {
			if ( isset( $params['renderer'] ) ) {
				$settings->show_option( $name, $params['renderer'] );
			}
		}
		$settings->set( 'allow_statistics', true );
	}

	/**
	 * If extensions need to add tables, they will need to override the function to add a schema.
	 *
	 * @param string $prefix Database prefix to use for table names.
	 *
	 * @return array An array with two keys, schema and tables which are used
	 *               for installing and dropping the table.
	 */
	protected static function _get_schema( $prefix ) {
		return array(
			'tables' => array(),
			'schema' => '',
		);
	}

	/**
	 * Performe upgarde actions based on extension version
	 * 
	 * @param Ai1ec_Registry_Object $registry
	 */
	protected function _perform_upgrade( Ai1ec_Registry_Object $registry ) {
		$version_variable = 'ai1ec_' . $this->get_machine_name() .
			'_version';
		$option  = $registry->get( 'model.option' );
		$version = $option->get( $version_variable );
		if ( $version !== $this->get_version() ) {
			$registry->get( 'model.settings' )->perform_upgrade_actions();
			$this->_perform_upgrade_actions();
			$option->set( $version_variable, $this->get_version(), true );
		}
	}

	/**
	 * Function called on add on upgrade.
	 * Can be overridden by add ons for extra behaviour
	 */
	protected function _perform_upgrade_actions() {
		
	}

	/**
	 * Since the call the to the uninstall hook it's static, if a different behaviour
	 * is needed also this call must be overridden.
	 */
	protected function _register_uninstall_hook() {
		register_uninstall_hook(
			$this->get_file(),
			array( get_class( $this ), 'on_uninstall' )
		);
	}

	/**
	 * Adds extension settings
	 *
	 * @param Ai1ec_Settings $settings
	 */
	protected function _add_settings( Ai1ec_Settings $settings ) {
		foreach ( $this->_settings as $name => $params ) {
			$renderer = null;
			if ( isset( $params['renderer'] ) ) {
				$renderer = $params['renderer'];
			}
			$settings->register(
				$name,
				$params['value'],
				$params['type'],
				$renderer,
				$this->get_version()
			);
		}
	}

	/**
	 * Check if the schema needs to be updated
	 * 
	 * @param Ai1ec_Registry_Object $registry
	 * @throws Ai1ec_Database_Update_Exception
	 */
	protected function _install_schema( Ai1ec_Registry_Object $registry ) {
		$option = $registry->get( 'model.option' );
		$schema = self::$_schema;
		if (
			is_admin() &&
			! empty( $schema['schema'] )
		) {
			$db_version_variable = 'ai1ec_' . $this->get_machine_name() .
				'_db_version';
			$version = sha1( $schema['schema'] );
			if (
				$option->get( $db_version_variable ) !== $version
			) {
				if (
					$registry->get( 'database.helper' )->apply_delta(
						$schema['schema']
				)
				) {
					$option->set( $db_version_variable, $version );
				} else {
					throw new Ai1ec_Database_Update_Exception(
						'Database upgrade for ' . $this->get_name() .
						' failed'
					);
				}
			}
		}
	}

}