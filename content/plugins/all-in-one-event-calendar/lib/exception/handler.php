<?php

/**
 * Handles exception and errors
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Exception
 */
class Ai1ec_Exception_Handler {

	/**
	 * @var string The option for the messgae in the db
	 */
	const DB_DEACTIVATE_MESSAGE = 'ai1ec_deactivate_message';

	/**
	 * @var string The GET parameter to reactivate the plugin
	 */
	const DB_REACTIVATE_PLUGIN  = 'ai1ec_reactivate_plugin';

	/**
	 * @var callable|null Previously set exception handler if any
	 */
	protected $_prev_ex_handler;

	/**
	 * @var callable|null Previously set error handler if any
	 */
	protected $_prev_er_handler;

	/**
	 * @var string The name of the Exception class to handle
	 */
	protected $_exception_class;

	/**
	 * @var string The name of the ErrorException class to handle
	 */
	protected $_error_exception_class;

	/**
	 * @var string The message to display in the admin notice
	 */
	protected $_message;

	/**
	 * @var array Mapped list of errors that are non-fatal, to be ignored
	 *            in production.
	 */
	protected $_nonfatal_errors = null;

	/**
	 * Store exception handler that was previously set
	 *
	 * @param callable|null $_prev_ex_handler
	 *
	 * @return void Method does not return
	 */
	public function set_prev_ex_handler( $prev_ex_handler ) {
		$this->_prev_ex_handler = $prev_ex_handler;
	}

	/**
	 * Store error handler that was previously set
	 *
	 * @param callable|null $_prev_er_handler
	 *
	 * @return void Method does not return
	 */
	public function set_prev_er_handler( $prev_er_handler ) {
		$this->_prev_er_handler = $prev_er_handler;
	}

	/**
	 * Constructor accepts names of classes to be handled
	 *
	 * @param string $exception_class Name of exceptions base class to handle
	 * @param string $error_class     Name of errors base class to handle
	 *
	 * @return void Constructor newer returns
	 */
	public function __construct( $exception_class, $error_class ) {
		$this->_exception_class       = $exception_class;
		$this->_error_exception_class = $error_class;
		$this->_nonfatal_errors       = array(
			E_USER_WARNING => true,
			E_WARNING      => true,
			E_USER_NOTICE  => true,
			E_NOTICE       => true,
			E_STRICT       => true,
		);
		if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
			// wrapper `constant( 'XXX' )` is used to avoid compile notices
			// on earlier PHP versions.
			$this->_nonfatal_errors[constant( 'E_DEPRECATED' )]      = true;
			$this->_nonfatal_errors[constant( 'E_USER_DEPRECATED') ] = true;
		}
	}

	/**
	 * Return add-on, which caused the exception or null if it was Core.
	 *
	 * Relies on `plugin_to_disable` method which may be implemented by
	 * an exception. If it returns non empty value - it is returned.
	 *
	 * @param Exception $exception Actual exception which was thrown.
	 *
	 * @return string|null Add-on identifier (plugin url), or null.
	 */
	public function is_caused_by_addon( Exception $exception ) {
		$addon = null;
		if ( method_exists( $exception, 'plugin_to_disable' ) ) {
			$addon = $exception->plugin_to_disable();
			if ( empty( $addon ) ) {
				$addon = null;
			}
		}
		if ( null === $addon ) {
			$position   = strlen( dirname( AI1EC_PATH ) ) + 1;
			$length     = strlen( AI1EC_PLUGIN_NAME );
			$trace_list = $exception->getTrace();
			array_unshift(
				$trace_list,
				array( 'file' => $exception->getFile() )
			);
			foreach ( $trace_list as $trace ) {
				if (
					! isset( $trace['file'] ) ||
					! isset( $trace['file'][$position] )
				) {
					continue;
				}
				$file = substr(
					$trace['file'],
					$position,
					strpos( $trace['file'], '/', $position ) - $position
				);
				if ( 0 === strncmp( AI1EC_PLUGIN_NAME, $file, $length ) ) {
					if ( AI1EC_PLUGIN_NAME !== $file ) {
						$addon = $file . '/' . $file . '.php';
					}
				}
			}
		}
		if ( 'core' === strtolower( $addon ) ) {
			return null;
		}
		return $addon;
	}

	/**
	 * Get tag-line for disabling.
	 *
	 * Extracts plugin name from file.
	 *
	 * @param string $addon Name of disabled add-on.
	 *
	 * @return string Message to display before full trace.
	 */
	public function get_disabled_line( $addon ) {
		$file = dirname( AI1EC_PATH ) . DIRECTORY_SEPARATOR . $addon;
		$line = '';
		if (
			is_file( $file ) &&
			preg_match(
				'|Plugin Name:\s*(.+)|',
				file_get_contents( $file ),
				$matches
			)
		) {
			$line = '<h4>' .
				sprintf(
					__( 'Disabled add-on "%s" due to an error' ),
					__( trim( $matches[1] ), dirname( $addon ) )
				) .
				'</h4>';
		}
		return $line;
	}

	/**
	 * Global exceptions handling method
	 *
	 * @param Exception $exception Previously thrown exception to handle
	 *
	 * @return void Exception handler is not expected to return
	 */
	public function handle_exception( Exception $exception ) {
		if ( defined( 'AI1EC_DEBUG' ) && true === AI1EC_DEBUG ) {
			echo '<pre>';
			$this->var_debug( $exception );
			echo '</pre>';
			die();
		}
		// if it's something we handle, handle it
		$backtrace = '<br><br>' . nl2br( $exception );
		if ( $exception instanceof $this->_exception_class ) {
			// check if it's a plugin instead of core
			$disable_addon = $this->is_caused_by_addon( $exception );
			$message       = method_exists( $exception, 'get_html_message' )
				? $exception->get_html_message()
				: $exception->getMessage();
			$message .= $backtrace .
				'<br><br>Request Uri: ' . $_SERVER['REQUEST_URI'];
			if ( null !== $disable_addon ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
				// deactivate the plugin. Fire handlers to hide options.
				deactivate_plugins( $disable_addon );
				global $ai1ec_registry;
				$ai1ec_registry->get( 'notification.admin' )
					->store( 
						$this->get_disabled_line( $disable_addon ) . $message,
						'error',
						2,
						array( Ai1ec_Notification_Admin::RCPT_ADMIN ),
						true
					);
				$this->redirect();
			} else {
				// check if it has a methof for deatiled html
				$this->soft_deactivate_plugin( $message );
			}

		}
		// if it's a PHP error in our plugin files, deactivate and redirect
		else if ( $exception instanceof $this->_error_exception_class ) {
			$this->soft_deactivate_plugin(
				$exception->getMessage() . $backtrace
			);
		}
		// if another handler was set, let it handle the exception
		if ( is_callable( $this->_prev_ex_handler ) ) {
			call_user_func( $this->_prev_ex_handler, $exception );
		}
	}

	/**
	 * Throws an Ai1ec_Error_Exception if the error comes from our plugin
	 *
	 * @param int    $errno      Error level as integer
	 * @param string $errstr     Error message raised
	 * @param string $errfile    File in which error was raised
	 * @param string $errline    Line in which error was raised
	 * @param array  $errcontext Error context symbols table copy
	 *
	 * @throws Ai1ec_Error_Exception If error originates from within Ai1EC
	 *
	 * @return boolean|void Nothing when error is ours, false when no
	 *                      other handler exists
	 */
	public function handle_error(
		$errno,
		$errstr,
		$errfile,
		$errline,
		$errcontext = array()
	) {
		// if the error is not in our plugin, let PHP handle things.
		$position = strpos( $errfile, AI1EC_PLUGIN_NAME );
		if ( false === $position ) {
			if ( is_callable( $this->_prev_er_handler ) ) {
				return call_user_func_array(
					$this->_prev_er_handler,
					func_get_args()
				);
			}
			return false;
		}
		// do not disable plugin in production if the error is rather low
		if (
			isset( $this->_nonfatal_errors[$errno] ) && (
				! defined( 'AI1EC_DEBUG' ) || false === AI1EC_DEBUG
			)
		) {
			$message = sprintf(
				'All-in-One Event Calendar: %s @ %s:%d #%d',
				$errstr,
				$errfile,
				$errline,
				$errno
			);
			return error_log( $message, 0 );
		}
		// let's get the plugin folder
		$tail = substr( $errfile, $position );
		$exploded = explode( DIRECTORY_SEPARATOR, $tail );
		$plugin_dir = $exploded[0];
		// if the error doesn't belong to core, throw the plugin exception to trigger disabling
		// of the plugin in the exception handler
		if ( AI1EC_PLUGIN_NAME !== $plugin_dir ) {
			$exc = implode(
				array_map(
					array( $this, 'return_first_char' ),
					explode( '-', $plugin_dir )
				)
			);
			// all plugins should implement an exception based on this convention
			// which is the same convention we use for constants, only with just first letter uppercase
			$exc = str_replace( 'aioec', 'Ai1ec', $exc ) . '_Exception';
			if ( class_exists( $exc ) ) {
				$message = sprintf(
					'All-in-One Event Calendar: %s @ %s:%d #%d',
					$errstr,
					$errfile,
					$errline,
					$errno
				);
				throw new $exc( $message );
			}
		}
		throw new Ai1ec_Error_Exception(
			$errstr,
			$errno,
			0,
			$errfile,
			$errline
		);
	}

	public function return_first_char( $name ) {
		return $name[0];
	}
	/**
	 * Perform what's needed to deactivate the plugin softly
	 *
	 * @param string $message Error message to be displayed to admin
	 *
	 * @return void Method does not return
	 */
	protected function soft_deactivate_plugin( $message ) {
		add_option( self::DB_DEACTIVATE_MESSAGE, $message );
		$this->redirect();
	}

	/**
	 * Perform what's needed to reactivate the plugin
	 *
	 * @return boolean Success
	 */
	public function reactivate_plugin() {
		return delete_option( self::DB_DEACTIVATE_MESSAGE );
	}

	/**
	 * Get message to be displayed to admin if any
	 *
	 * @return string|boolean Error message or false if plugin is not disabled
	 */
	public function get_disabled_message() {
		global $wpdb;
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1",
				self::DB_DEACTIVATE_MESSAGE
			)
		);
		if ( is_object( $row ) ) {
			return $row->option_value;
		} else { // option does not exist, so we must cache its non-existence
			return false;
		}
	}

	/**
	 * Add an admin notice
	 *
	 * @param string $message Message to be displayed to admin
	 *
	 * @return void Method does not return
	 */
	public function show_notices( $message ) {
		// save the message to use it later
		$this->_message = $message;
		add_action( 'admin_notices', array( $this, 'render_admin_notice' ) );
	}

	/**
	 * Render HTML snipped to be displayd as a notice to admin
	 *
	 * @hook admin_notices When plugin is soft-disabled
	 *
	 * @return void Method does not return
	 */
	public function render_admin_notice() {
		$redirect_url = add_query_arg(
			self::DB_REACTIVATE_PLUGIN,
			'true',
			get_admin_url( $_SERVER['REQUEST_URI'] )
		);
		$label = __(
			'All In One Event Calendar has been disabled due to an error:',
			AI1EC_PLUGIN_NAME
		);
		$message = '<div class="message error">'.
						'<h3>' . $label . '</h3>' .
						'<p>' . $this->_message . '</p>';
		$message .= sprintf(
			__(
				'<p>If you corrected the error and wish to try reactivating the plugin, <a href="%s">click here</a>.</p>',
				AI1EC_PLUGIN_NAME
			),
			$redirect_url
		);
		$message .= '</div>';
		echo $message;
	}

	/**
	 * Redirect the user either to the front page or the dashbord page
	 *
	 * @return void Method does not return
	 */
	protected function redirect() {
		if ( is_admin() ) {
			Ai1ec_Http_Response_Helper::redirect( get_admin_url() );
		} else {
			Ai1ec_Http_Response_Helper::redirect( get_site_url() );
		}
	}
	/**
	 * Had to add it as var_dump was locking my browser.
	 *
	 * Taken from http://www.leaseweblabs.com/2013/10/smart-alternative-phps-var_dump-function/
	 *
	 * @param mixed $variable
	 * @param int $strlen
	 * @param int $width
	 * @param int $depth
	 * @param int $i
	 * @param array $objects
	 * 
	 * @return string
	 */
	public function var_debug( 
		$variable, 
		$strlen = 400, 
		$width = 25, 
		$depth = 10, 
		$i = 0, 
		&$objects = array() 
	) {
		$search  = array( "\0", "\a", "\b", "\f", "\n", "\r", "\t", "\v" );
		$replace = array( '\0', '\a', '\b', '\f', '\n', '\r', '\t', '\v' );
		$string  = '';

		switch ( gettype( $variable ) ) {
			case 'boolean' :
				$string .= $variable ? 'true' : 'false';
				break;
			case 'integer' :
				$string .= $variable;
				break;
			case 'double' :
				$string .= $variable;
				break;
			case 'resource' :
				$string .= '[resource]';
				break;
			case 'NULL' :
				$string .= "null";
				break;
			case 'unknown type' :
				$string .= '???';
				break;
			case 'string' :
				$len = strlen( $variable );
				$variable = str_replace( 
					$search, 
					$replace, 
					substr( $variable, 0, $strlen ), 
					$count );
				$variable = substr( $variable, 0, $strlen );
				if ( $len < $strlen ) {
					$string .= '"' . $variable . '"';
				} else {
					$string .= 'string(' . $len . '): "' . $variable . '"...';
				}
				break;
			case 'array' :
				$len = count( $variable );
				if ( $i == $depth ) {
					$string .= 'array(' . $len . ') {...}';
				} elseif ( ! $len) {
					$string .= 'array(0) {}';
				} else {
					$keys    = array_keys( $variable );
					$spaces  = str_repeat( ' ', $i * 2 );
					$string .= "array($len)\n" . $spaces . '{';
					$count   = 0;
					foreach ( $keys as $key ) {
						if ( $count == $width ) {
							$string .= "\n" . $spaces . "  ...";
							break;
						}
						$string .= "\n" . $spaces . "  [$key] => ";
						$string .= $this->var_debug( 
							$variable[$key],
							$strlen,
							$width,
							$depth,
							$i + 1,
							$objects
						);
						$count ++;
					}
					$string .= "\n" . $spaces . '}';
				}
				break;
			case 'object':
				$id = array_search( $variable, $objects, true );
				if ( $id !== false ) {
					$string .= get_class( $variable ) . '#' . ( $id + 1 ) . ' {...}';
				} else if ( $i == $depth ) {
					$string .= get_class( $variable ) . ' {...}';
				} else {
					$id = array_push( $objects, $variable );
					$array = ( array ) $variable;
					$spaces = str_repeat( ' ', $i * 2 );
					$string .= get_class( $variable ) . "#$id\n" . $spaces . '{';
					$properties = array_keys( $array );
					foreach ( $properties as $property ) {
						$name    = str_replace( "\0", ':', trim( $property ) );
						$string .= "\n" . $spaces . "  [$name] => ";
						$string .= $this->var_debug(
							$array[$property],
							$strlen,
							$width,
							$depth,
							$i + 1,
							$objects
						);
					}
					$string .= "\n" . $spaces . '}';
				}
				break;
		}

		if ( $i > 0 ) {
			return $string;
		}

		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
		do {
			$caller = array_shift( $backtrace );
		} while (
			$caller &&
			! isset( $caller['file'] )
		);
		if ( $caller ) {
			$string = $caller['file'] . ':' . $caller['line'] . "\n" . $string;
		}

		echo nl2br( str_replace( ' ', '&nbsp;', htmlentities( $string ) ) );
	}

}