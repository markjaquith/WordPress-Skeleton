<?php

/**
 * Abstract class for extensions which are sold.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Controller
 */
abstract class Ai1ec_Base_License_Controller extends Ai1ec_Base_Extension_Controller {

	/**
	 * @var string Settings entry name for license key.
	 */
	protected $_licence;

	/**
	 * @var string Settings entry name for license status output.
	 */
	protected $_licence_status;

	/**
	 * @var string Licensing API endpoint URI.
	 */
	protected $_store = 'http://time.ly/';

	/**
	 * Get label to be used for license input field.
	 *
	 * @return string Localized label field.
	 */
	abstract public function get_license_label();

	/**
	 * @param Ai1ec_Registry_Object $registry
	 */
	public function initialize_licence_actions() {
		$this->_register_licence_actions();
		$this->_register_licence_fields();
		$this->_register_updating();
	}

	/**
	 * Add the extension tab if not present
	 *
	 * @param array $tabs
	 * @return array
	 */
	public function add_tabs( array $tabs ) {
		if ( ! isset( $tabs['extensions'] ) ) {
			$tabs['extensions'] = array(
				'name'  => Ai1ec_I18n::__( 'Add-ons' ),
				'items' => array(
					'licenses' => Ai1ec_I18n::__( 'Licenses' ),
				),
			);
		} else if ( ! isset( $tabs['extensions']['items']['licenses'] ) ) {
			$tabs['extensions']['items']['licenses'] = Ai1ec_I18n::__( 'Licences' );
		}
		return $tabs;
	}

	/**
	 * Check the licence if it has changed and adds the status
	 *
	 * @param array $old_options
	 * @param array $new_options
	 *
	 */
	public function check_licence( array $old_options, array $new_options ) {
		$old_licence = $old_options[$this->_licence]['value'];
		$new_licence = $new_options[$this->_licence]['value'];
		$status      = $old_options[$this->_licence_status]['value'];
		if ( $new_licence !== $old_licence ) {
			$license = trim( $new_licence );
			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->get_name() ),// the name of our product in EDD,
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, $this->_store ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			// $license_data->license will be either "active" or "inactive"

			$this->_registry->get( 'model.settings' )
				->set( $this->_licence_status, $license_data->license );
		}

	}

	/**
	 * Register action for licences.
	 */
	protected function _register_licence_actions() {
		$dispatcher = $this->_registry->get( 'event.dispatcher' );
		// we need the super class so we use get_class()
		$class      = explode( '_', get_class( $this ) );
		$controller = strtolower( end( $class ) );
		$dispatcher->register_filter(
			'ai1ec_add_setting_tabs',
			array( 'controller.' . $controller, 'add_tabs' )
		);
		$dispatcher->register_action(
			'ai1ec_settings_updated',
			array( 'controller.' . $controller, 'check_licence' ),
			10,
			2
		);
	}

	/**
	 * Register EDD updater class
	 */
	protected function _register_updating() {
		$license_key = $this->_registry->get( 'model.settings' )
			->get( $this->_licence );
		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater(
			$this->_store,
			$this->get_file(),
			array(
				'version'   => $this->get_version(),   // current version number
				'license'   => $license_key,           // license key (used get_option above to retrieve from DB)
				'item_name' => $this->get_name(),      // name of this plugin
				'author'    => 'Time.ly Network Inc.', // author of this plugin
				'url'       => home_url(),
			)
		);
	}

	/**
	 * Register fields for licence
	 */
	protected function _register_licence_fields() {
		$plugin_id             = $this->get_machine_name();
		$this->_licence        = 'ai1ec_licence_' . $plugin_id;
		$this->_licence_status = 'ai1ec_licence_status_' . $plugin_id;
		$options               = array(
			$this->_licence => array(
				'type' => 'string',
				'version'  => $this->get_version(),
				'renderer' => array(
					'class'       => 'input',
					'group-class' => 'ai1ec-col-sm-7',
					'tab'         => 'extensions',
					'item'        => 'licenses',
					'type'        => 'normal',
					'label'       => $this->get_license_label(),
					'status'      => $this->_licence_status,
				),
				'default'  => '',
			),
			$this->_licence_status => array(
				'type'     => 'string',
				'version'  => $this->get_version(),
				'default'  => 'invalid',
			),
		);
		$settings = $this->_registry->get( 'model.settings' );
		foreach ( $options as $key => $option ) {
			$renderer = null;
			if ( isset( $option['renderer'] ) ) {
				$renderer = $option['renderer'];
			}
			$settings->register(
				$key,
				$option['default'],
				$option['type'],
				$renderer,
				$option['version']
			);
		}
	}

}