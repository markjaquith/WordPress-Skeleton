<?php

/**
 * This class represent a LESS variable of type font.
 *
 * @author       Time.ly Network Inc.
 * @since        2.0
 * @instantiator new
 * @package      AI1EC
 * @subpackage   AI1EC.Less.Variable
 */
class Ai1ec_Less_Variable_Font extends Ai1ec_Less_Variable {

	/**
	 * @var string Value saved when a custom font is used
	 */
	const CUSTOM_FONT = 'custom';

	/**
	 * @var string suffix added to custom font fields
	 */
	const CUSTOM_FONT_ID_SUFFIX = '_custom';


	/**
	 * @var string True if using a custom value
	 */
	private $use_custom_value = false;

	/**
	 * @var string The custom value.
	 */
	private $custom_value;

	/**
	 *
	 * @var array
	 */
	private $fonts = array(
		'Arial'               => 'Arial, Helvetica, sans-serif',
		'Arial Black'         => '"Arial Black", Gadget, sans-serif',
		'Comic Sans MS'       => '"Comic Sans MS", cursive',
		'Courier New'         => '"Courier New", monospace',
		'Georgia'             => 'Georgia, Georgia, serif',
		'Helvetica Neue'      => '"Helvetica Neue", Helvetica, Arial, sans-serif',
		'League Gothic'       => '"League Gothic", Impact, "Arial Black", Arial, sans-serif',
		'Impact'              => 'Impact, Charcoal, sans-serif',
		'Lucida Console'      => '"Lucida Console", Monaco, monospace',
		'Lucida Sans Unicode' => '"Lucida Sans Unicode", Lucida Grande, sans-serif',
		'MS Sans Serif'       => '"MS Sans Serif", Geneva, sans-serif',
		'MS Serif'            => '"MS Serif", "New York", serif',
		'Palatino'            => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
		'Tahoma'              => 'Tahoma, Geneva, sans-serif',
		'Times New Roman'     => '"Times New Roman", Times, serif',
		'Trebuchet Ms'        => '"Trebuchet MS", "Lucida Grande", sans-serif',
		'Verdana'             => 'Verdana, Geneva, sans-serif',
	);

	/**
	 * @param Ai1ec_Registry_Object $registry
	 * @param array $params
	 */
	public function __construct( Ai1ec_Registry_Object $registry, array $params ) {
		$this->fonts[__( "Custom...", AI1EC_PLUGIN_NAME )] = self::CUSTOM_FONT;

		// Allow extensions to add options to the font list.
		$this->fonts = apply_filters( 'ai1ec_font_options', $this->fonts );
		if ( ! in_array( $params['value'], $this->fonts ) ) {
			$this->use_custom_value = true;
			$this->custom_value = $params['value'];
			$this->value = self::CUSTOM_FONT;
		}
		parent::__construct( $registry, $params );
	}

	/**
	 * (non-PHPdoc)
	 * add the fonts
	 * @see Ai1ec_Less_Variable::set_up_renderable()
	 */
	public function _get_options() {
		$options = array();
		foreach ( $this->fonts as $text => $key ) {
			$option = array(
				'text' => $text,
				'value' => $key,
			);
			if ( $key === $this->value
				 || ( self::CUSTOM_FONT === $key && $this->use_custom_value )
			   ) {
				$option['args'] = array(
					'selected' => 'selected',
				);
			}
			$options[] = $option;
		}
		return $options;
	}

	/**
	 * (non-PHPdoc)
	 * @see Ai1ec_Less_Variable::render()
	 */
	public function render() {
		$args = array(
			'label' => $this->description,
			'id'    => $this->id,
			'input' => array(
				'id' => $this->id . self::CUSTOM_FONT_ID_SUFFIX,
				'value' => '',
				'args'  => array(
					'placeholder' => __( "Enter custom font(s)", AI1EC_PLUGIN_NAME ),
					'class'       => 'ai1ec-custom-font',
				),
			),
			'select' => array(
				'id' => $this->id,
				'args' => array(
					'class' => 'ai1ec_font'
				),
				'options' => $this->_get_options(),
			)

		);

		if ( ! $this->use_custom_value ) {
			$args['input']['args']['class'] = 'ai1ec-custom-font ai1ec-hide';
		} else {
			$args['input']['value'] = $this->custom_value;
		}
		$loader = $this->_registry->get( 'theme.loader' );
		$file   = $loader->get_file( 'theme-options/font.twig', $args, true );
		return $file->get_content();
	}
}
