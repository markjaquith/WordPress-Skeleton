<?php
/**
 * NF Session
 *
 * This is a wrapper class for WP_Session / PHP $_SESSION and handles the storage of cart items, purchase sessions, etc
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Session
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.9.18
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * NF_Session Class
 *
 * @since 1.5
 */
class NF_Session {
	/**
	 * Holds our session data
	 *
	 * @var array
	 * @access private
	 * @since 2.9.18
	 */
	private $session;
	/**
	 * Session index prefix
	 *
	 * @var string
	 * @access private
	 * @since 2.9.18
	 */
	private $prefix = '';
	/**
	 * Get things started
	 *
	 * Defines our WP_Session constants, includes the necessary libraries and
	 * retrieves the WP Session instance
	 *
	 * @since 2.9.18
	 */
	public function __construct() {
		// Use WP_Session (default)
		if ( ! defined( 'WP_SESSION_COOKIE' ) ) {
			define( 'WP_SESSION_COOKIE', 'nf_wp_session' );
		}
		if ( ! class_exists( 'Recursive_ArrayAccess' ) ) {
			require_once NF_PLUGIN_DIR . 'includes/libraries/class-recursive-arrayaccess.php';
		}
		if ( ! class_exists( 'WP_Session' ) ) {
			require_once NF_PLUGIN_DIR . 'includes/libraries/class-wp-session.php';
			require_once NF_PLUGIN_DIR . 'includes/libraries/wp-session.php';
		}

		add_filter( 'wp_session_expiration_variant', array( $this, 'set_expiration_variant_time' ), 99999 );
		add_filter( 'wp_session_expiration', array( $this, 'set_expiration_time' ), 99999 );

		add_action( 'plugins_loaded', array( $this, 'init' ), -1 );

	}
	/**
	 * Setup the WP_Session instance
	 *
	 * @access public
	 * @since 2.9.18
	 * @return void
	 */
	public function init() {
		$this->session = WP_Session::get_instance();
		return $this->session;
	}
	/**
	 * Retrieve session ID
	 *
	 * @access public
	 * @since 2.9.18
	 * @return string Session ID
	 */
	public function get_id() {
		return $this->session->session_id;
	}
	/**
	 * Retrieve a session variable
	 *
	 * @access public
	 * @since 2.9.18
	 * @param string $key Session key
	 * @return string Session variable
	 */
	public function get( $key ) {
		$key = sanitize_key( $key );
		return isset( $this->session[ $key ] ) ? maybe_unserialize( $this->session[ $key ] ) : false;
	}
	/**
	 * Set a session variable
	 *
	 * @since 2.9.18
	 * @param string $key Session key
	 * @param integer $value Session variable
	 * @return string Session variable
	 */
	public function set( $key, $value ) {
		$key = sanitize_key( $key );
		if ( is_array( $value ) ) {
			$this->session[ $key ] = serialize( $value );
		} else {
			$this->session[ $key ] = $value;
		}
		return $this->session[ $key ];
	}

	/**
	 * Force the cookie expiration variant time to 23 minutes
	 *
	 * @access public
	 * @since 2.9.18
	 * @param int $exp Default expiration (1 hour)
	 * @return int
	 */
	public function set_expiration_variant_time( $exp ) {
		return 60 * 23;
	}
	/**
	 * Force the cookie expiration time to 24 minutes
	 *
	 * @access public
	 * @since 2.9.18
	 * @param int $exp Default expiration (1 hour)
	 * @return int
	 */
	public function set_expiration_time( $exp ) {
		return 60 * 24;
	}
}
