<?php
/**
 * Abstract request parsing class.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http.Request
 */
abstract class Ai1ec_Abstract_Query extends Ai1ec_Base implements arrayaccess {
	/**
	 * @var array Request array to parse
	 */
	protected $_request = null;

	/**
	 * @var array Parsing rules map
	 */
	protected $_rules	= null;

	/**
	 * @var array Parsed values
	 */
	protected $_parsed	= null;

	/**
	 * @var array Indicator - whereas parsing was finished
	 */
	protected $_ready	= false;

	/**
	 * Return prefix that shall be used to access values
	 */
	abstract protected function _get_prefix( );

	/**
	 * Constructor
	 *
	 * Store locally copy of arguments array
	 *
	 * @param array $argv Arguments to be parsed [optional=null]
	 *
	 * @return void Constructor does not return
	 */
	public function __construct( Ai1ec_Registry_Object $registry, array $argv = NULL ) {
		parent::__construct( $registry );
		if ( NULL === $argv ) {
			$request_uri = explode( '?', $_SERVER['REQUEST_URI'] );
			$request_uri = urldecode( $request_uri[0] );
			$argv = trim( $request_uri, '/' );
			if ( ( $arg_start = strpos( $argv, '/' ) ) > 0 ) {
				$argv = substr( $argv, $arg_start + 1 );
			}
			$arg_list = explode( '/', $argv );
			$argv     = array();
			foreach ( $arg_list as $arg ) {
				if ( ( $colon = strpos( $arg, Ai1ec_Uri::DIRECTION_SEPARATOR ) ) > 0 ) {
					$argv[substr( $arg, 0, $colon )] = substr( $arg, $colon + 1 );
				}
			}
		}
		$this->_rules   = array( );
		$this->_request = $argv;
	}

	/**
	 * parse method
	 *
	 * Parse request values given rules array
	 *
	 * @return bool Success
	 */
	public function parse() {
		if ( ! isset( $this->_request['ai1ec'] ) ) {
			$this->_request['ai1ec'] = array();
		}
		foreach ( $this->_rules as $field => $options ) {
			$value = $options['default'];
			if ( ( $ext_var = $this->_get_var( $field ) ) ) {
				$value = $this->_sane_value(
					$ext_var,
					$options
				);
			} elseif ( $options['mandatory'] ) {
				$this->_parsed = array( );
				return false;
			}
			if ( $options['is_list'] ) {
				$value = (array)$value;
			}
			$this->_parsed[$field] = $value;
			if ( ! isset( $this->_request['ai1ec'][$field] ) ) {
				$this->_request['ai1ec'][$field] = $value;
			}
		}
		$this->_ready = true;
		return true;
	}

	/**
	 * Get parsed values map.
	 *
	 * @param array $name_list List of values to pull
	 * If associative value is encountered - *key* is used to pull
	 * request entity, and *value* to store it in returned map.
	 *
	 * @return array Parsed values map
	 */
	public function get_dict( array $name_list ) {
		$dictionary = array( );
		foreach ( $name_list as $alias => $name ) {
			if ( is_int( $alias ) ) {
				$alias = $name;
			}
			$value = $this->get( $name );
			if ( empty( $value ) ) {
			    $value = $this->get( $alias );
			}
			$dictionary[$alias] = $value;
		}
		return $dictionary;
	}

	/**
	 * Get parsed value
	 *
	 * @param array $name Name of value to pull
	 *
	 * @return array Parsed value
	 */
	public function get( $name ) {
		if ( ! $this->_ready ) {
			return false;
		}
		if ( ! isset( $this->_parsed[$name] ) ) {
			return false;
		}
		return $this->_parsed[$name];
	}

	/**
	 * Check if the request is empry ( that means we are accessing the calendare page without parameters )
	 *
	 * @return boolean
	 */
	public function is_empty_request() {
		return empty( $this->_request );
	}

	protected function _get_var( $name, $prefix = '' ) {
		$name     = $this->_name_without_prefix( $name );
		$use_name = $prefix . $name;
		if ( isset( $this->_request[$use_name] ) ) {
			return $this->_request[$use_name];
		}
		$result = $this->_registry->get( 'http.request.wordpress-adapter' )
			->variable( $use_name );
		if ( null === $result || false === $result ) {
			$defined_prefix = $this->_get_prefix( );
			if ( '' === $prefix && $defined_prefix !== $prefix ) {
				return $this->_get_var( $name, $defined_prefix );
			}
		}
		return $result;
	}

	protected function _name_without_prefix( $name ) {
		$prefix = $this->_get_prefix( );
		$length = strlen( $prefix );
		if ( 0 === strncmp( $name, $prefix, $length ) ) {
			return substr( $name, $length );
		}
		return $name;
	}

	/**
	 * Get scalar value representation
	 *
	 * @param array $name Name of value to pull
	 *
	 * @return array Parsed value converted to scalar
	 */
	public function get_scalar( $name ) {
		$value = $this->get( $name );
		if ( ! is_scalar( $value ) ) {
			$value = implode( $this->_rules[$name]['list_sep'], $value );
		}
		return $value;
	}

	/**
	 * @overload ArrayAccess::offsetExists()
	 */
	public function offsetExists( $offset ) {
		if ( false === $this->get( $offset ) ) {
			return false;
		}
		return true;
	}

	/**
	 * @overload ArrayAccess::offsetGet()
	 */
	public function offsetGet( $offset ) {
		return $this->get_scalar( $offset );
	}

	/**
	 * @overload ArrayAccess::offsetSet()
	 */
	public function offsetSet( $offset, $value ) {
		// not implemented and will not be
	}

	/**
	 * @overload ArrayAccess::offsetUnset()
	 */
	public function offsetUnset( $offset ) {
		// not implemented and will not be
	}

	/**
	 * Add argument parsing rule
	 *
	 * @param string      $field     Name of field to parse
	 * @param bool        $mandatory Set to true for mandatory fields
	 * @param string      $type      Type of field
	 * @param mixed       $default   Default value to use if one is not present
	 * @param string|bool $list_sep  Set to list separator (i.e. ',') if it is a
	 *                               list or false if value is not a list value.
	 *                               For 'enum' set to array of values.
	 *
	 * @return bool Success
	 */
	public function add_rule(
		$field,
		$mandatory = true,
		$type      = 'int',
		$default   = null,
		$list_sep  = false
	) {
		if ( ! is_scalar( $field ) || is_bool( $field ) ) {
			return false;
		}
		if ( false === $this->_valid_type( $type ) ) {
			return false;
		}
		$mandatory = (bool)$mandatory;
		$is_list   = false !== $list_sep && is_scalar( $list_sep );
		$field     = $this->_name_without_prefix( $field );
		$prefix    = $this->_get_prefix( );
		$record    = compact(
			'field',
			'mandatory',
			'type',
			'default',
			'is_list',
			'list_sep'
		);
		// ? => emit notice, if field is already defined
		$this->_rules[$field]           = $record;
		$this->_rules[$prefix . $field] = $record;
		$this->_ready                   = false;
		return true;
	}

	/**
	 * _sane_value method
	 *
	 * Check if given type definition is valid.
	 * Return sanitizer function name (if applicable) for valid type.
	 *
	 * @param string $name Type name to use
	 *
	 * @return string|bool Name of sanitization function or false
	 */
	protected function _valid_type( $name ) {
		static $map = array(
			'int'     => 'intval',
			'integer' => 'intval',
			'float'   => 'floatval',
			'double'  => 'floatval',
			'real'    => 'floatval',
			'string'  => 'strval',
			'enum'    => null,
		);
		if ( !isset( $map[$name] ) ) {
			return false;
		}
		return $map[$name];
	}

	/**
	 * _sane_value method
	 *
	 * Parse single input value according to processing rules.
	 * Relies on {@see self::_type_cast()} for value conversion.
	 *
	 * @param mixed $input   Original request value
	 * @param array $options Type definition options
	 *
	 * @return mixed Sanitized value
	 */
	protected function _sane_value( $input, array $options ) {
		$sane_value = null;
		if ( $options['is_list'] ) {
			$value      = explode( $options['list_sep'], $input );
			$sane_value = array( );
			foreach ( $value as $element ) {
				$cast_element = $this->_type_cast( $element, $options );
				if ( ! empty( $cast_element ) ) {
					$sane_value[] = $cast_element;
				}
			}
		} else {
			$sane_value = $this->_type_cast( $input, $options );
		}
		return $sane_value;
	}

	/**
	 * _type_cast method
	 *
	 * Cast value to given type.
	 * Non-PHP type 'enum' is accepted
	 *
	 * @param mixed $value   Value to cast
	 * @param array $options Type definition options
	 *
	 * @return mixed Casted value
	 */
	protected function _type_cast( $value, array $options ) {
		if ( 'enum' === $options['type'] ) {
			if ( in_array( $value, $options['list_sep'] ) ) {
				return $value;
			}
			return null;
		}
		$cast  = $this->_valid_type( $options['type'] );
		$value = $cast( $value );
		return $value;
	}
}
