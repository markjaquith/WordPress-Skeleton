<?php

/**
 * The Theme options page.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
class Ai1ec_View_Theme_Options extends Ai1ec_View_Admin_Abstract {

	/**
	 * @var string The nonce action
	 */
	const NONCE_ACTION = 'ai1ec_theme_options_save';

	/**
	 * @var string The nonce name
	 */
	const NONCE_NAME  = 'ai1ec_theme_options_nonce';

	/**
	 * @var string The id/name of the submit button.
	 */
	const SUBMIT_ID = 'ai1ec_save_themes_options';

	/**
	 * @var string The id/name of the Reset button.
	 */
	const RESET_ID = 'ai1ec_reset_themes_options';

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $meta_box_id;

	/**
	 * Adds the page to the correct menu.
	 */
	public function add_page() {
		$theme_options_page = add_submenu_page(
			AI1EC_ADMIN_BASE_URL,
			Ai1ec_I18n::__( 'Theme Options' ),
			Ai1ec_I18n::__( 'Theme Options' ),
			'manage_ai1ec_options',
			AI1EC_PLUGIN_NAME . '-edit-css',
			array( $this, 'display_page' )
		);
		$settings = $this->_registry->get( 'model.settings' );
		if ( false !== $settings->get( 'less_variables_page' ) ) {
			// Make copy of Theme Options page at its old location.
			$submenu['themes.php'][] = array(
				Ai1ec_I18n::__( 'Calendar Theme Options' ),
				'manage_ai1ec_options',
				AI1EC_THEME_OPTIONS_BASE_URL,
			);
		};
		$settings->set( 'less_variables_page', $theme_options_page );
	}

	/**
	 * Add meta box for page.
	 *
	 * @wp_hook admin_init
	 *
	 * @return void
	 */
	public function add_meta_box() {
		// Add the 'General Settings' meta box.
		add_meta_box(
			'ai1ec-less-variables-tabs',
			Ai1ec_I18n::_x( 'Calendar Theme Options', 'meta box' ),
			array( $this, 'display_meta_box' ),
			$this->_registry->get( 'model.settings' )
				->get( 'less_variables_page' ),
			'left',
			'default'
		);
	}

	/**
	 * Display the page html
	 */
	public function display_page() {

		$settings = $this->_registry->get( 'model.settings' );

		$args = array(
			'title' => Ai1ec_I18n::__(
				'Calendar Theme Options'
			),
			'nonce' => array(
				'action'   => self::NONCE_ACTION,
				'name'     => self::NONCE_NAME,
				'referrer' => false,
			),
			'metabox' => array(
				'screen' => $settings->get( 'themes_option_page' ),
				'action' => 'left',
				'object' => null
			),
			'action' =>
				'?controller=front&action=ai1ec_save_theme_options&plugin=' . AI1EC_PLUGIN_NAME
		);

		$frontend = $this->_registry->get( 'css.frontend' );

		$loader = $this->_registry->get( 'theme.loader' );

		$file   = $loader->get_file( 'theme-options/page.twig', $args, true );

		return $file->render();

	}

	/**
	 * Displays the meta box for the settings page.
	 *
	 * @param mixed $object
	 * @param mixed $box
	 */
	public function display_meta_box( $object, $box )  {

		$tabs = array(
			'general' => array(
				'name' => Ai1ec_I18n::__( 'General' ),
			),
			'table' => array(
				'name' => Ai1ec_I18n::__( 'Tables' ),
			),
			'buttons' => array(
				'name' => Ai1ec_I18n::__( 'Buttons' ),
			),
			'forms' => array(
				'name' => Ai1ec_I18n::__( 'Forms' ),
			),
			'calendar' => array(
				'name' => Ai1ec_I18n::__( 'Calendar general' ),
			),
			'month' => array(
				'name' => Ai1ec_I18n::__( 'Month/week/day view' ),
			),
			'agenda' => array(
				'name' => Ai1ec_I18n::__( 'Agenda view' ),
			),
		);

		$tabs = apply_filters( 'ai1ec_less_variables_tabs', $tabs );

		$less_variables  = $this->_registry
			->get( 'less.lessphp' )->get_saved_variables();
		$tabs            = $this->_get_tabs_to_show( $less_variables, $tabs );

		$loader          = $this->_registry->get( 'theme.loader' );
		$args            = array(
			'stacked'       => true,
			'content_class' => 'ai1ec-form-horizontal',
			'tabs'          => $tabs,
			'submit'        => array(
				'id'          => self::SUBMIT_ID,
				'value'       => '<i class="ai1ec-fa ai1ec-fa-save ai1ec-fa-fw"></i> ' .
					Ai1ec_I18n::__( 'Save Options' ),
				'args'        => array(
					'class'     => 'ai1ec-btn ai1ec-btn-primary ai1ec-btn-lg',
				),
			),
			'reset'         => array(
				'id'          => self::RESET_ID,
				'value'       => '<i class="ai1ec-fa ai1ec-fa-undo ai1ec-fa-fw"></i> ' .
					Ai1ec_I18n::__( 'Reset to Defaults' ),
				'args'        => array(
					'class'     => 'ai1ec-btn ai1ec-btn-danger ai1ec-btn-lg',
				),
			),
		);
		$file = $loader->get_file( 'theme-options/bootstrap_tabs.twig', $args, true );
		$file->render();

	}

	/**
	 * Return the theme options tabs
	 *
	 * @param array $less_variables
	 * @param array $tabs list of tabs
	 *
	 * @return array the array of tabs to display
	 */
	protected function _get_tabs_to_show( array $less_variables, array $tabs) {

		// Inizialize the array of tabs that will be added to the layout
		$bootstrap_tabs_to_add = array();

		foreach( $tabs as $id => $tab ){
			$tab['elements'] = array();
			$bootstrap_tabs_to_add[$id] = $tab;
		}
		foreach ( $less_variables as $variable_id => $variable_attributes ) {
			$variable_attributes['id'] = $variable_id;
			$renderable = $this->_registry->get(
				'less.variable.' . $variable_attributes['type'],
				$variable_attributes
			);
			$bootstrap_tabs_to_add[$variable_attributes['tab']]['elements'][] = array(
				'html' => $renderable->render()
			);
		}
		return $bootstrap_tabs_to_add;
	}

	/**
	 * Handle post, likely to be deprecated to use commands.
	 */
	public function handle_post()  {

	}
}
