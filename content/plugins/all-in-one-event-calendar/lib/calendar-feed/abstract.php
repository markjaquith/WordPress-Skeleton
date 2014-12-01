<?php

/**
 * The abstract class for the Calendar feeds tab.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Calendar-feed
 */
abstract class Ai1ec_Connector_Plugin extends Ai1ec_Base {

	/**
	 * An associative array where the keys are the name of the variables stored in the Settings object
	 * while the values are the description in the admin Panel
	 *
	 * @var array;
	 *
	 */
	protected $settings =  array();

	/**
	 * An array of variables used by the plugin. Some of this variables are required:
	 *   title => The name of the tab in the calendar feeds settings
	 *   id    => The id used in the href of the tab. Must be unique
	 *
	 * @var array
	 */
	protected $variables = array();

	/**
	 * Handles any action the plugin requires when the users makes a POST in the calendar feeds page.
	 */
	abstract public function handle_feeds_page_post();

	/**
	 * Get title to be used for tab human-identification.
	 *
	 * @return string Localized string.
	 */
	abstract public function get_tab_title();

	/**
	 * Renders the content of the tab, where all the action takes place.
	 *
	 */
	abstract public function render_tab_content();

	/**
	 * Let the plugin display an admin notice if neede.
	 *
	 */
	abstract public function display_admin_notices();

	/**
	 * Run the code that cleans up the DB and CRON functions the plugin has installed.
	 *
	 */
	abstract public function run_uninstall_procedures();

	/**
	 * Renders the HTML for the tabbed navigation
	 *
	 * @return void
	 *   Echoes the HTML string that act as tab header for the plugin
	 */
	public function render_tab_header() {
		// Use the standard view helper
		$args = array(
			'title'  => $this->get_tab_title(),
			'id'     => $this->variables['id'],
		);
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'plugins/tab_header.php', $args, true );
		$file->render();
	}

	/**
	 * Gets the settings for the Plugin from the settings object.
	 *
	 * @param string $class_name The name of the Plugin for which we are
	 *                           retrieving the settings.
	 *
	 * @return array An associative array with the settings stored in settings
	 *               object or an empty array if settings are not set.
	 */
	protected function get_plugin_settings( $class_name ) {
		$plugins_options = $this->_registry->get( 'model.settings' )
			->get( 'plugins_options' );
		return isset( $plugins_options[$class_name] )
			? $plugins_options[$class_name]
			: array();
	}

	/**
	 * Generate an arry which contains all settings data.
	 *
	 * Only data that will be processed by the admin view is considered.
	 *
	 * @return array An array of Associative arrays that hold everything that's
	 *               needed to render the settings field in the admin section.
	 */
	protected function generate_settings_array_for_admin_view() {
		// Get the plugin settings
		$plugin_settings = $this->get_plugin_settings( get_class( $this ) );
		// This is the array that will be returned
		$result = array();
		// Iterate over the settings
		foreach ( $this->settings as $setting ) {
			if ( $setting['admin-page'] === TRUE ) {
				// For each setting get it's value, description and id
				$result[] = array (
					"setting-description" => __( $setting['description'], AI1EC_PLUGIN_NAME ),
					"setting-value"       => $plugin_settings[$setting['id']],
					"setting-id"          => $setting['id'],
				);
			}
		}
		return $result;
	}
	/**
	 * Check that at least one of the settings has ha value.
	 *
	 * @param array $settings
	 *
	 * @return boolean
	 */
	protected function at_least_one_config_field_is_set( array $settings ) {
		foreach ( $settings as $setting ) {
			if( ! empty( $setting['setting-value'] ) ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * If the plugin settings are not set they will be initialized to ''
	 *
	 */
	public function initialize_settings_if_not_set() {
		// Get the class name.
		$class_name = get_class( $this );
		$settings = $this->_registry->get( 'model.settings' );
		$plugins_options = $settings->get( 'plugins_options' );
		// Check if the options have been set
		if ( ! isset( $plugins_options[$class_name] ) ) {
			// If not set them. The key is the class name, the value is an associative array
			$plugins_options[$class_name] = array();
			foreach ( $this->settings as $setting ) {
				$plugins_options[$class_name][$setting['id']] = '';
			}
		}
		$settings->set( 'plugins_options', $plugins_options );
	}
	/**
	 * Retrieves the specified plugin setting
	 *
	 * @param string $variable_name The name of the variable to be retrieved
	 *
	 * @return mixed The variable value or FALSE if it's not set
	 */
	protected function get_plugin_variable( $variable_name ) {
		$plugin_settings = $this->get_plugin_settings( get_class( $this ) );
		return isset( $plugin_settings[$variable_name] ) ? $plugin_settings[$variable_name] : FALSE;
	}
	/**
	 * Saves the variable int he plugin settings.
	 *
	 * @param string $variable_name The name of the variable to save.
	 *
	 * @param mixed $value The value of the variable to save.
	 */
	protected function save_plugin_variable( $variable_name, $value ) {
		$this->save_plugin_settings( array( $variable_name => $value ), TRUE );
	}
	/**
	 * Saves the plugin settings in the settings object
	 *
	 * @param array $data
	 *   An associative array of data to be saved
	 *
	 * @param boolean $not_from_setting_page
	 *   True if the function is not called from the setting page and must trigger the saving, false otherwise
	 */
	public function save_plugin_settings( array $data, $not_from_setting_page = FALSE ) {
		$settings = $this->_registry->get( 'model.settings' );
		$plugins_options = $settings->get( 'plugins_options' );
		// Get the class name.
		$class_name = get_class( $this );
		// We need to save the old settings so that we can then let the Facebook plugin check if the user changed app-id / secret
		$old_settings = $this->get_plugin_settings( get_class( $this ) );


		// Check if the options have been set
		if ( isset( $plugins_options[$class_name] ) ) {
			// If the options for the plugin are set, iterate over the settings
			foreach ( $this->settings as $setting ) {
				// Always check that the key is set, data can come from $_POST or from an internal call
				if( isset( $data[$setting['id']] ) ) {
					$plugins_options[$class_name][$setting['id']] = $data[$setting['id']];
				}
			}
		}
		$settings->set( 'plugins_options', $plugins_options );
		if ( $not_from_setting_page === TRUE ) {
			$settings->persist( );
		} else {
			$old_settings['page'] = $data['page'];
			do_action( "ai1ec-$class_name-postsave-setting", $old_settings );
		}
	}
	/**
	 * Prints an error message with standard formatting
	 *
	 * @param string $message The error message to be echoed to the screen
	 *
	 * @param boolean $close_tab_div TRUE if after the error message we should close the tab div
	 */
	protected function render_error_page( $message, $close_tab_div = FALSE ) {
		$args = array();
		$args['message'] = $message;
		$loader = $this->_registry->get( 'theme.loader' );
		$file = $loader->get_file( 'plugins/display_error_message.php', $args, true );
		$file->render();
		if( $close_tab_div === TRUE ) {
			$this->render_closing_div_of_tab();
		}
	}

	/**
	 * Renders the opening div of the tab and set the active status if this tab is the active one
	 *
	 * @param string $active_feed the tab that should be active.
	 */
	protected function render_opening_div_of_tab() {
		$args = array(
			'id' => $this->variables['id'],
		);
		$loader = $this->_registry->get( 'theme.loader' );
		$file = $loader->get_file( 'plugins/render_opening_div.php', $args, true );
		$file->render();
	}

	/**
	 * This renders the closing div of the tab.
	 */
	protected function render_closing_div_of_tab(  ) {
		echo '</div>';
	}

}