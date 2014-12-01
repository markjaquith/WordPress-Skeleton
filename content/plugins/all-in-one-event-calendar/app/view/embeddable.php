<?php
abstract class Ai1ec_Embeddable extends WP_Widget {

	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected $_registry;

	/**
	 * @var string
	 */
	protected $_id;

	/**
	 * @var boolean
	 */
	protected $_css_loaded = false;

	/**
	 * Get default values for shortcode or widget.
	 *
	 * @return array
	 */
	abstract public function get_defaults();

	/**
	 * Get values which are configurable in the Javascript widget.
	 * Some things might not be configurable.
	 *
	 * @return array
	 */
	abstract public function get_js_widget_configurable_defaults();

	/**
	 * Create the html for the widget. Shared by all versions.
	 *
	 * @param array $args_for_widget
	 * @param bool  $remote_request whether the request is for a remote site or not (useful to inline CSS)
	 */
	abstract public function get_content( array $args_for_widget, $remote_request = false );

	/**
	 * Add the required javascript for the widget. Needed for shortcode and Wordpress widget
	 */
	abstract public function add_js();

	/**
	 * Register the widget to the controller.
	 *
	 * @param string $id_base
	 */
	abstract public function register_javascript_widget( $id_base );


	/**
	 * Return options needed for thw "Widget creator page
	 *
	 * @return array
	 */
	abstract public function get_configurable_for_widget_creation();

	/**
	 * The human-readable name of the widget.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Checks and returns widget requirements.
	 *
	 * @return string
	 */
	abstract public function check_requirements();

	/**
	 * Register widget class with current WP instance.
	 * This must be static as otherwise the class would be instantiated twice,
	 * one to register it and the other from Wordpress.
	 *
	 * @return string
	 */
	public static function register_widget() {
		throw new Ai1ec_Exception( 'This should be implemented in child class' );
	}

	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		$this->_id = $id_base;
		parent::__construct( $id_base, $name, $widget_options, $control_options );
		add_shortcode( $id_base, array( $this, 'shortcode' ) );
		$this->_registry = apply_filters( 'ai1ec_registry', false );
		$this->register_javascript_widget( $id_base );
		add_filter( 'ai1ec_js_translations', array( $this, 'add_js_translations' ) );
		$this->_registry->get( 'css.frontend' )->add_link_to_html_for_frontend();
	}

	/**
	 * @param array $translations
	 * @return array
	 */
	public function add_js_translations( array $translations ) {
		$translations['javascript_widgets'][$this->_id] = $this->get_js_widget_configurable_defaults();
		return $translations;
	}
	/**
	 * Widget function.
	 *
	 * Outputs the given instance of the widget to the front-end.
	 *
	 * @param  array $args     Display arguments passed to the widget
	 * @param  array $instance The settings for this widget instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$defaults = $this->get_defaults();
		$instance = wp_parse_args( $instance, $defaults );
		$this->add_js();
		$args['widget_html'] = $this->get_content( $instance );
		if ( ! empty( $args['widget_html'] ) ) {
			$args['title'] = $instance['title'];
			$args          = $this->_filter_widget_args( $args );
			// Display theme
			$this->_registry->get( 'theme.loader' )->get_file(
				'widget.twig',
				$args
			)->render();
		}

	}

	/**
	 * Renders shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 */
	public function shortcode( $atts, $content = null ) {
		$defaults = $this->get_defaults();
		$atts = shortcode_atts( $defaults, $atts );
		$this->add_js();
		return $this->get_content( $atts );
	}

	/**
	 * Renders js widget
	 *
	 * @param array $args
	 */
	public function javascript_widget( $args ) {
		$defaults = $this->get_defaults();
		$args = wp_parse_args( $args, $defaults );
		return $this->get_content( $args, true );
	}

	/**
	 * Filters default widget parameters like classes, html elements before and
	 * after title or widget. Useful for Feature Events widget which has
	 * different title styling.
	 *
	 * @param array $args Widget arguments.
	 *
	 * @return array Filtered arguments.
	 */
	protected function _filter_widget_args( $args ) {
		return $args;
	}
}