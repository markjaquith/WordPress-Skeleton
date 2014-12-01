<?php

/**
 * Class to aid WP URI management
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Routing
 */
class Ai1ec_Wp_Uri_Helper {

	/**
	 * Redirect to local site page
	 *
	 * Local redirect restricts redirection to parts of the same site. Primary
	 * use for this is in post-submit actions, when form is submitted to point
	 * user back to submission page and clear status from browser, which might
	 * cause issues, such as double submission, with users clicking refresh on
	 * target page.
	 *
	 * @param string $target_uri URI to redirect to (must be local site)
	 * @param array  $extra      Extra arguments to add to query [optional]
	 * @param int    $status     HTTP redirect status [optional=302]
	 *
	 * @return void Method does not return. It perform implicit `exit` to
	 *              protect against further processing
	 */
	static public function local_redirect(
		$target_uri,
		array $extra = array(),
		$status      = 302
	) {
		$target_uri = add_query_arg( $extra, $target_uri );
		wp_safe_redirect( $target_uri, $status );
		exit( 0 );
	}

	/**
	 * Given a URI, extracts pagebase value, as used in `index.php?pagebase={arg}`
	 * when matching rewrites.
	 * It may, optionally, provide arguments from URI to append to query string.
	 * This is indicated via setting {@see $qsa} to non-`false` value.
	 *
	 * @param string $uri URI to parse pagebase value from
	 * @param string $qsa Separator to use to append query arguments [optional]
	 *
	 * @return string Parsed URL
	 */
	static public function get_pagebase( $uri, $qsa = false ) {
		$parsed = parse_url( $uri );
		if ( empty( $parsed ) ) {
			return '';
		}
		$output = '';
		if ( isset( $parsed['path'] ) ) {
			$output = basename( $parsed['path'] );
		}
		if ( isset( $parsed['query'] ) && false !== $qsa ) {
			$output .= $qsa . $parsed['query'];
		}
		return $output;
	}

	/**
	 * Gets the calendar pagebase with the full url but without the language.
	 *
	 * @param string $uri
	 * @param string $lang
	 * @return string
	 */
	static public function get_pagebase_for_links( $uri, $lang ) {
		if( empty( $lang ) ) {
			return $uri;
		}
		if( false !== strpos( $uri, '&amp;lang=' ) ) {
			return str_replace( '&amp;lang=' . $lang, '' , $uri );
		}
		if( false !== strpos( $uri, '?lang=' ) ) {
			return str_replace( '?lang=' . $lang, '' , $uri );
		}
		return $uri;
	}

	/**
	 * Gets the currently requested URL.
	 *
	 * @return string Canonical URL, that is currently requested
	 */
	static public function get_current_url( $skip_port = false ) {
		$page_url = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
			$page_url .= 's';
		}
		$page_url .= '://';
		if ( $_SERVER['SERVER_PORT'] !== '80' && true !== $skip_port ) {
			$page_url .= $_SERVER['SERVER_NAME'] . ':' .
			$_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$page_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		}
		return $page_url;
	}

}
