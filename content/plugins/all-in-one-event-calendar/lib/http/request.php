<?php

/**
 * Utility handling HTTP(s) automation issues
 *
 * @author     Timely Network Inc
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Http
 */
class Ai1ec_Http_Request {

	/**
	 * Public constructor
	 *
	 * @param Ai1ec_Registry_Object $registry
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		$this->_registry = $registry;
	}

	/**
	 * Callback for debug-checking filters. Changes debug to false for AJAX req.
	 *
	 * @wp_hook ai1ec_dbi_debug
	 *
	 * @param bool $do_debug Current debug value.
	 *
	 * @return bool Optionally modified `$do_debug`.
	 */
	public function debug_filter( $do_debug ) {
		if ( $this->is_ajax() ) {
			$do_debug = false;
		}
		return $do_debug;
	}

	/**
	 * Check if we are processing AJAX request.
	 *
	 * @return bool True if it's an AJAX request.
	 */
	public function is_ajax() {
		if ( defined( 'DOING_AJAX' ) ) {
			return true;
		}
		if (
			isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
			'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH']
		) {
			return true;
		}
		if (
			isset( $_GET['ai1ec_doing_ajax'] ) &&
			'true' === $_GET['ai1ec_doing_ajax']
		) {
			return true;
		}
		if ( isset( $_GET['ai1ec_js_widget'] ) ) {
			return true;
		}
		if (
			isset( $_GET['ai1ec_render_js'] ) ||
			isset( $_GET['ai1ec_render_css'] )
		) {
			return true;
		}
		return apply_filters( 'ai1ec_is_ajax', false );
	}

	/**
	 * Check if client accepts gzip and we should compress content
	 *
	 * Plugin settings, client preferences and server capabilities are
	 * checked to make sure we should use gzip for output compression.
	 *
	 * @return bool True when gzip should be used
	 */
	public function client_use_gzip() {
		$settings = $this->_registry->get( 'model.settings' );

		if (
			$settings->get( 'disable_gzip_compression' ) ||
			(
				isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) &&
				'identity' === $_SERVER['HTTP_ACCEPT_ENCODING'] ||
				! extension_loaded( 'zlib' )
			)
		) {
			return false;
		}
		$zlib_output_handler = ini_get( 'zlib.output_handler' );
		if (
			in_array( 'ob_gzhandler', ob_list_handlers() ) ||
			in_array(
				strtolower( ini_get( 'zlib.output_compression' ) ),
				array( '1', 'on' )
			) ||
			! empty( $zlib_output_handler )
		) {
			return false;
		}
		return true;
	}

	/**
	 * Disable `streams` transport support as necessary
	 *
	 * Following (`streams`) transport is disabled only when request to cron
	 * dispatcher are made to make sure that requests does have no impact on
	 * browsing experience - site is not slowed down, when crons are spawned
	 * from within current screen session.
	 *
	 * @param mixed  $output  HTTP output
	 * @param string $url     Original request URL
	 *
	 * @return mixed Original or modified $output
	 */
	public function pre_http_request( $status, $output, $url ) {
		$cron_url = site_url( 'wp-cron.php' );
		remove_filter( 'use_streams_transport', 'ai1ec_return_false' );
		if (
			0 === strncmp( $url, $cron_url, strlen( $cron_url ) ) &&
			! function_exists( 'curl_init' )
		) {
			add_filter( 'use_streams_transport', 'ai1ec_return_false' );
		}
		return $status;
	}

	/**
	 * Inject time.ly certificate to cURL resource handle
	 *
	 * @param resource $curl Instance of cURL resource
	 *
	 * @return void Method does not return value
	 */
	public function curl_inject_certificate( $curl ) {
		// verify that the passed argument
		// is resource of type 'curl'
		if (
			is_resource( $curl ) &&
			'curl' === get_resource_type( $curl )
		) {
			// set CURLOPT_CAINFO to AI1EC_CA_ROOT_PEM
			curl_setopt( $curl, CURLOPT_CAINFO, AI1EC_CA_ROOT_PEM );
		}
	}

	/**
	 * Initialize time.ly certificate only for time.ly domain
	 *
	 * @param array  $args Http arguments.
	 * @param string $url Current URL address.
	 *
	 * @return void Method does not return value
	 */
	public function init_certificate( $args, $url ) {
		remove_action( 'http_api_curl', array( $this, 'curl_inject_certificate' ) );
		if ( false !== stripos( $url, '//time.ly' ) ) {
			add_action( 'http_api_curl', array( $this, 'curl_inject_certificate' ) );
		}
		return $args;
	}

	/**
	 * Checks if is json required for frontend rendering.
	 *
	 * @param string $request_format Format.
	 *
	 * @return bool True or false.
	 */
	public function is_json_required( $request_format ) {
		return
			'json' === $request_format
			&& AI1EC_USE_FRONTEND_RENDERING
			&& $this->is_ajax();
	}

	/**
	 * Returns current action for bulk operations.
	 *
	 * @return string|null Action or null when empty.
	 */
	public function get_current_action() {
		if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {
			return $_REQUEST['action'];
		}
		if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
			return $_REQUEST['action2'];
		}
		return null;
	}
}
