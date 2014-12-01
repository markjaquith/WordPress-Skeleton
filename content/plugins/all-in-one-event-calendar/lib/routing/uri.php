<?php

/**
 * URI management class
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Routing
 */
class Ai1ec_Uri
{

	/**
	 * Set direction separator
	 */
	const DIRECTION_SEPARATOR = '~';

	/**
	 * @var string Arguments (list) separator
	 */
	protected $_arguments_separator = '/';

	/**
	 * Parse page to internal URI representation
	 *
	 * @param string $page Page URI to parse
	 *
	 * @return array|bool Parsed URI or false on failure
	 */
	public function parse( $page ) {
		$result = array(
			'base'  => '',
			'ai1ec' => array(),
			'args'  => null,
			'hash'  => null,
			'_type' => 'args', // enum: args,hash,pretty
		);
		if ( false === ( $parsed = parse_url( $page ) ) ) {
			return false;
		}

		if ( isset( $parsed['scheme'] ) ) {
			if ( !isset( $parsed['host'] ) ) {
				return false;
			}
			$result['base'] = $parsed['scheme'] . '://' . $parsed['host'];
		}

		if ( ! empty( $parsed['path'] ) ) {
			if ( false !== strpos( $parsed['path'], self::DIRECTION_SEPARATOR ) ) {
				$result['_type'] = 'pretty';
				$elements = explode(
					$this->_arguments_separator,
					$parsed['path']
				);
				foreach ( $elements as $particle ) {
					$sep = strpos( $particle, self::DIRECTION_SEPARATOR );
					if ( false !== $sep ) {
						$key = substr( $particle, 0, $sep );
						$result['ai1ec'][$key] = substr( $particle, $sep + 1 );
					} else {
						$result['base'] .= $this->_arguments_separator .
							$particle;
					}
				}
			}
		}

		$query_pos = array(
			'query'    => 'args',
			'fragment' => 'hash',
		);
		foreach ( $query_pos as $source => $destination ) {
			if ( 'hash' === $destination ) {
				$result['_type'] = $destination;
			}
			if ( ! empty( $parsed[$source] ) ) {
				$result = Ai1ec_Utility_Array::deep_merge(
					$result,
					$this->parse_query_str( $parsed[$source], $destination )
				);
			}
		}

		return $result;
	}

	/**
	 * Produce query to use in URI
	 *
	 * @param array $parsed Internal query representation
	 *
	 * @return string Normalized URI
	 */
	public function write( array $parsed ) {
		$uri = $parsed['base'];

		if ( 'pretty' == $parsed['_type'] ) {
			$uri = rtrim( $uri, $this->_arguments_separator );
			foreach ( $parsed['ai1ec'] as $key => $value ) {
				$uri .= $this->_arguments_separator .
					$this->_clean_key( $key ) .
					self::DIRECTION_SEPARATOR .
					$this->_safe_uri_value( $value );
			}
		} else {
			$place  = $parsed['_type'];
			$action = isset( $parsed['ai1ec']['action'] )
			    ? $parsed['ai1ec']['action']
			    : NULL;
			if ( empty( $action ) ) {
			    foreach ( array( 'args', 'hash' ) as $place ) {
				if ( ! empty( $parsed[$place]['action'] ) ) {
				    $action = $parsed[$place]['action'];
				    break;
				}
			    }
			}
			if ( empty( $action ) ) {
			    $action = 'uri_err';
			}
			if ( 0 !== strncmp( $action, 'ai1ec_', 6 ) ) {
			    $action = 'ai1ec_' . $action;
			}
			$combined_ai1ec = array();
			foreach ( $parsed['ai1ec'] as $key => $value ) {
				$combined_ai1ec[] = $this->_clean_key( $key ) .
					self::DIRECTION_SEPARATOR . $this->_safe_uri_value( $value );
			}
			$combined_ai1ec = implode( '|', $combined_ai1ec );
			$parsed[$place]['ai1ec'] = $combined_ai1ec;
			if ( 'hash' === $place ) {
			    $parsed[$place]['action'] = $action;
			}
			unset( $combined_ai1ec, $place );
		}

		$arg_list = array(
		  'args' => '?',
		  'hash' => '#',
		);

		foreach ( $arg_list as $name => $separator ) {
			if ( ! empty( $parsed[$name] ) ) {
				$uri .= $separator . build_query( $parsed[$name] );
			}
		}

		return $uri;
	}

	/**
	 * Convert query string to internal representation
	 *
	 * @param string $query Query to parse
	 * @param string $name	Positional name (i.e. 'hash')
	 *
	 * @return array Parsed query map with 'ai1ec' and {$name} keys
	 */
	public function parse_query_str( $query, $name ) {
		$result = array(
		  'ai1ec' => array(),
		  $name   => array(),
		);
		$parsed = null;
		parse_str( $query, $parsed );
		foreach ( $parsed as $key => $value ) {
			if ( 0 === strncmp( $key, 'ai1ec', 5 ) ) {
				$result['_type'] = $name;
				if ( ! is_array( $value ) ) {
					if ( 'ai1ec' === $key ) {
						$value_list = explode( '|', $value );
						$value		= array();
						foreach ( $value_list as $entry ) {
							list( $sub_key, $sub_value ) = explode(
								self::DIRECTION_SEPARATOR,
								$entry
							);
							$value[$sub_key] = $sub_value;
						}
						unset( $value_list );
					} else {
						$value = array( substr($key, 6) => $value );
					}
				}
				$result['ai1ec'] = array_merge(
					$result['ai1ec'],
					$value
				);
			} else {
				$result[$name][$key] = $value;
			}
		}
		unset( $parsed );
		return $result;
	}

	/**
	 * Create URI-safe value (scalar)
	 *
	 * @param mixed $value Value provided for URL
	 *
	 * @return string Value to use in URI
	 */
	protected function _safe_uri_value( $value ) {
		if ( is_array( $value ) ) {
			$value = implode(',', $value);
		}
		return $value;
	}

	/**
	 * Clean AI1EC sub-element key
	 *
	 * @param string $key Key to clean
	 *
	 * @return string Cleaned key to output
	 */
	protected function _clean_key( $key ) {
		if ( 0 === strncmp( $key, 'ai1ec_', 6 ) ) {
			$key = substr( $key, 6 );
		}
		return $key;
	}

}
