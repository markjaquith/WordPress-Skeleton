<?php

/**
 * Base html element.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Html
 */
abstract class Ai1ec_Html_Element extends Ai1ec_Base implements Ai1ec_Renderable {

	/**
	 *
	 * @var string
	 */
	protected $id;

	/**
	 *
	 * @var array
	 */
	protected $classes = array();

	/**
	 * @var array
	 */
	protected $attributes = array();

	/**
	 *
	 * @var Ai1ec_Template_Adapter
	 */
	protected $template_adapter;

	/**
	 * Adds the passed attribute name & value to the link's attributes.
	 *
	 * @param string       $name
	 * @param string|array $value
	 */
	public function set_attribute( $name, $value ) {
		$value = ( array ) $value;
		// Let's check if we have a value
		if ( isset( $this->attributes[$name] ) ) {
			// Let's check if it's an array
			$this->attributes[$name] = array_unique(
				array_merge( $this->attributes[$name], $value )
			);
		} else {
			$this->attributes[$name] = $value;
		}
	}

	/**
	 *
	 * @param string $name
	 * @return array|NULL
	 */
	public function get_attribute( $name ) {
		if ( isset( $this->attributes[$name] ) ) {
			return $this->attributes[$name];
		} else {
			return null;
		}
	}
	/**
	 * Adds the given name="value"-formatted attribute expression to the link's
	 * set of attributes.
	 *
	 * @param string $expr Attribute name-value pair in name="value" format
	 */
	public function set_attribute_expr( $expr ) {
		preg_match( '/^([\w\-_]+)=[\'"]([^\'"]*)[\'"]$/', $expr, $matches );
		$name = $matches[1];
		$value = $matches[2];
		$this->set_attribute( $name, $value );
	}


	public function __construct( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
		$this->template_adapter = $registry->get( 'html.helper' );
	}

	/**
	 * Magic method that renders the object as html
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render_as_html();
	}

	/**
	 *
	 * @param $id string
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * Adds an element to the class array
	 *
	 * @param string $class
	 */
	public function add_class( $class ) {
		$this->classes[] = $class;
	}

	/**
	 * Creates the markup to be used to create classes
	 *
	 * @return string
	 */
	protected function create_class_markup() {
		if ( empty( $this->classes ) ) {
			return '';
		}

		$classes = $this->template_adapter->escape_attribute(
			implode( ' ', $this->classes )
		);
		return "class='$classes'";
	}

	/**
	 * Creates the markup for an attribute
	 *
	 * @param string $attribute_name
	 * @param string $attribute_value
	 * @return string
	 */
	protected function create_attribute_markup(
		$attribute_name,
		$attribute_value
	) {
		if (empty( $attribute_value )) {
			return '';
		}
		$attribute_value = $this->template_adapter->escape_attribute( $attribute_value );
		return "$attribute_name='$attribute_value'";
	}

	/**
	 * Renders the markup for the attributes of the tag
	 *
	 * @return string
	 */
	protected function render_attributes_markup() {
		$html = array();
		foreach ( $this->attributes as $name => $values ) {
			$values = $this->template_adapter->escape_attribute(
				implode( ' ', $values )
			);
			$html[] = "$name='$values'";
		}
		return implode( ' ', $html );
	}

	/**
	 * Return the content as html instead of echoing it.
	 *
	 * @return string
	 */
	public function render_as_html() {
		$this->_registry->get( 'compatibility.ob' )->start();
		$this->render();
		return $this->_registry->get( 'compatibility.ob' )->get_clean();
	}
}
