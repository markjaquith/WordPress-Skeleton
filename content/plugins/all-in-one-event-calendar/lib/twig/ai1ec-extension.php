<?php

/**
 * The extension class used by twig..
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Validator
 */
class Ai1ec_Twig_Ai1ec_Extension extends Twig_Extension {

	/**
	 * @var Ai1ec_Registry_Object
	 */
	protected $_registry;

	/**
	 * Inject the registry object.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 */
	public function set_registry( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
	}

	/* (non-PHPdoc)
	 * @see Twig_Extension::getFunctions()
	 */
	public function getFunctions() {
		return array(
			'screen_icon'    => new Twig_Function_Method( $this, 'screen_icon' ),
			'wp_nonce_field' => new Twig_Function_Method( $this, 'wp_nonce_field' ),
			'do_meta_boxes'  => new Twig_Function_Method( $this, 'do_meta_boxes' ),
			'fb'             => new Twig_Function_Method( $this, 'fb' ),
		);
	}

	/**
	 * Twig callback - return a list of filters registered by this extension.
	 *
	 * @return array
	 */
	public function getFilters() {
		return array(
			new Twig_SimpleFilter( 'truncate',          array( $this, 'truncate' ) ),
			new Twig_SimpleFilter( 'timespan',          array( $this, 'timespan' ) ),
			new Twig_SimpleFilter( 'avatar',            array( $this, 'avatar' ) ),
			new Twig_SimpleFilter( 'avatar_url',        array( $this, 'avatar_url' ) ),
			new Twig_SimpleFilter( 'hour_to_datetime',  array( $this, 'hour_to_datetime' ) ),
			new Twig_SimpleFilter( 'weekday',           array( $this, 'weekday' ) ),
			new Twig_SimpleFilter( 'day',               array( $this, 'day' ) ),
			new Twig_SimpleFilter( 'month',             array( $this, 'month' ) ),
			new Twig_SimpleFilter( 'year',              array( $this, 'year' ) ),
			new Twig_SimpleFilter( 'theme_img_url',     array( $this, 'theme_img_url' ) ),
			new Twig_SimpleFilter( 'date_i18n',         array( $this, 'date_i18n' ) ),
			new Twig_SimpleFilter( '__',                'Ai1ec_I18n::__' ),
		);
	}

	/**
	 * Twig callback - return a list of tests registered by this extension.
	 *
	 * @return array
	 */
	public function getTests() {
		return array(
			new Twig_SimpleTest( 'string', array( $this, 'is_string' ) ),
		);
	}

	/**
	 * Get HTML markup for the post's "avatar" image according conditional
	 * fallback model.
	 *
	 * Accepts an ordered array of named avatar $fallbacks. Also accepts a string
	 * of space-separated classes to add to the default classes.
	 * @param   Ai1ec_Event $event          The event to get the avatar for
	 * @param   array|null  $fallback_order Order of fallback in searching for
	 *                                      images, or null to use default
	 * @param   string      $classes        A space-separated list of CSS classes
	 *                                      to apply to the outer <div> element.
	 * @param   boolean     $wrap_permalink Whether to wrap the element in a link
	 *                                      to the event details page.
	 *
	 * @return  string                   String of HTML if image is found
	 */
	public function avatar(
		Ai1ec_Event $event,
		$fallback_order = null,
		$classes = '',
		$wrap_permalink = true
	) {
		return $this->_registry->get( 'view.event.avatar' )
			->get_event_avatar(
				$event,
				$fallback_order,
				$classes,
				$wrap_permalink
			);
	}

	/**
	 * Debug function to be used in twig templates with Firebug/FirePHP
	 * 
	 * @param mixed $object
	 */
	public function fb( $object ) {
		if ( function_exists( 'fb' ) ) {
			fb( $object );
		}
	}
	/**
	 * Get URL for avatar.
	 *
	 * Accepts an ordered array of named avatar $fallbacks.
	 * @param   Ai1ec_Event $event          The event to get the avatar for.
	 * @param   array|null  $fallback_order Order of fallback in searching for
	 *                                      images, or null to use default.
	 *
	 * @return  string                   URL if image is found.
	 */
	public function avatar_url(
		Ai1ec_Event $event,
		$fallback_order = null
	) {
		return $this->_registry->get( 'view.event.avatar' )
			->get_event_avatar_url(
				$event,
				$fallback_order
			);
	}

	/**
	 * Check if provided value is a string.
	 *
	 * @param mixed $var Suspected string
	 *
	 * @return boolean True if it is a string, false otherwise.
	 */
	public function is_string( $var ) {
		return is_string( $var );
	}

	/**
	 * Convert an hour to an Ai1ec_Date_Time object.
	 *
	 * @param int $hour
	 *
	 * @return Ai1ec_Date_Time
	 */
	public function hour_to_datetime( $hour ) {
		return $this->_registry->get( 'date.time', 'now', 'sys.default' )
			->set_time( $hour, 0, 0 );
	}

	/**
	 * Get the name of the weekday.
	 *
	 * @param int $unix_timestamp
	 *
	 * @return string
	 */
	public function weekday( $unix_timestamp ) {
		return $this->_registry->get( 'date.time', $unix_timestamp )
			->format_i18n( 'D' );
	}

	/**
	 * Get the name of the day.
	 *
	 * @param int $unix_timestamp
	 *
	 * @return string
	 */
	public function day( $unix_timestamp ) {
		return $this->_registry->get( 'date.time', $unix_timestamp )
			->format_i18n( 'j' );
	}

	/**
	 * Get the name of the month.
	 *
	 * @param int $unix_timestamp
	 *
	 * @return string
	 */
	public function month( $unix_timestamp ) {
		return $this->_registry->get( 'date.time', $unix_timestamp )
			->format_i18n( 'M' );
	}

	/**
	 * Get the date's year
	 *
	 * @param int $unix_timestamp
	 *
	 * @return string
	 */
	public function year( $unix_timestamp ) {
		return $this->_registry->get( 'date.time', $unix_timestamp )
			->format_i18n( 'Y' );
	}

	/**
	 * Get URL of the given image file in the calendar theme's directory.
	 *
	 * @param int $unix_timestamp
	 *
	 * @return string
	 */
	public function theme_img_url( $image ) {
		$loader = $this->_registry->get( 'theme.loader' );
		return $loader->get_file( $image, array(), false )->get_url();
	}

	/**
	 * Internationalize the given UNIX timestamp with the given format string.
	 *
	 * @param int $unix_timestamp
	 * @param string $format
	 *
	 * @return string
	 */
	public function date_i18n( $unix_timestamp, $format ) {
		return $this->_registry->get( 'date.time', $unix_timestamp )
			->format_i18n( $format );
	}

	/**
	 * Truncate a string after $length characters, appending $read_more string
	 * at end of truncation.
	 *
	 * @param number $length        Length to truncate string to.
	 * @param string $read_more     What string to append if truncated.
	 * @param string $html_entities Whether to treat input string as HTML with
	 *                              possible &asdf; entities
	 * @return string
	 */
	public function truncate(
		$string,
		$length = 35,
		$read_more = '...',
		$html_entities = true
	) {
		// Truncate multibyte encodings differently, if supported.
		if ( function_exists( 'mb_strimwidth' ) ) {
			// First decode entities if requested.
			if ( $html_entities ) {
				$string = html_entity_decode( $string, ENT_QUOTES, 'UTF-8' );
			}
			// Truncate string.
			$string = mb_strimwidth( $string, 0, $length, $read_more, 'UTF-8' );
			// Reencode entities if requested.
			if ( $html_entities ) {
				$string = htmlentities( $string, ENT_QUOTES, 'UTF-8' );
			}
		}
		else {
			// First decode entities if requested.
			if ( $html_entities ) {
				$string = html_entity_decode( $string, ENT_QUOTES );
			}
			// Truncate string.
			$read_more = strlen( $string ) > 35 ? $read_more : '';
			$string = substr( $string, 0, 35 ) . $read_more;
			// Reencode entities if requested.
			if ( $html_entities ) {
				$string = html_entity_decode( $string, ENT_QUOTES );
			}
		}

		return $string;
	}

	/**
	 * Displays a screen icon.
	 *
	 * @uses get_screen_icon()
	 * @since 2.7.0
	 *
	 * @param string|WP_Screen $screen Optional. Accepts a screen object (and defaults to the current screen object)
	 * 	which it uses to determine an icon HTML ID. Or, if a string is provided, it is used to form the icon HTML ID.
	 */
	public function screen_icon( $screen = '' ){
		return screen_icon( $screen );
	}

	/**
	 * Generate a timespan HTML block for an event.
	 *
	 * @param Ai1ec_Event $event              Event to generate timespan for.
	 * @param string      $start_date_display Start date display format.
	 *
	 * @return string Rendered HTML timespan block.
	 */
	public function timespan(
		Ai1ec_Event $event,
		$start_date_display = 'long'
	) {
		return $this->_registry->get( 'view.event.time' )
			->get_timespan_html( $event, $start_date_display );
	}

	/**
	 * Meta-Box template function
	 *
	 * @since 2.5.0
	 *
	 * @param string|object $screen Screen identifier
	 * @param string $context box context
	 * @param mixed $object gets passed to the box callback function as first parameter
	 * @return int number of meta_boxes
	 */
	public function do_meta_boxes( $screen, $context, $object ) {
		do_meta_boxes( $screen, $context, $object );
	}

	/**
	 * Retrieve or display nonce hidden field for forms.
	 *
	 * The nonce field is used to validate that the contents of the form came from
	 * the location on the current site and not somewhere else. The nonce does not
	 * offer absolute protection, but should protect against most cases. It is very
	 * important to use nonce field in forms.
	 *
	 * The $action and $name are optional, but if you want to have better security,
	 * it is strongly suggested to set those two parameters. It is easier to just
	 * call the function without any parameters, because validation of the nonce
	 * doesn't require any parameters, but since crackers know what the default is
	 * it won't be difficult for them to find a way around your nonce and cause
	 * damage.
	 *
	 * The input name will be whatever $name value you gave. The input value will be
	 * the nonce creation value.
	 *
	 * @package WordPress
	 * @subpackage Security
	 * @since 2.0.4
	 *
	 * @param string $action Optional. Action name.
	 * @param string $name Optional. Nonce name.
	 * @param bool $referer Optional, default true. Whether to set the referer field for validation.
	 * @param bool $echo Optional, default true. Whether to display or return hidden form field.
	 * @return string Nonce field.
	 */
	public function wp_nonce_field( $action = -1, $name = "_wpnonce", $referer = true , $echo = true ) {
		wp_nonce_field( $action, $name, $referer, $echo );
	}

	/* (non-PHPdoc)
	 * @see Twig_ExtensionInterface::getName()
	 */
	public function getName() {
		return 'ai1ec';
	}

}