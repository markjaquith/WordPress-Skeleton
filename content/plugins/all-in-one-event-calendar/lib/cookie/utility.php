<?php

/**
 * An utility class for cookies.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Cookie
 */
class Ai1ec_Cookie_Utility extends Ai1ec_Base {

	static public $types = array(
		'cat_ids',
		'tag_ids',
		'auth_ids',
	);

	/**
	 *  Check if a cookie is set for the current page
	 *
	 * @return Ai1ec_Cookie_Present_Dto
	 */
	public function is_cookie_set_for_current_page() {
		$cookie_dto = $this->_registry->get( 'cookie.dto' );
		$settings = $this->_registry->get( 'model.settings' );
		$calendar_url       = get_page_link( $settings->get( 'calendar_page_id' ) );
		$requested_page_url = Ai1ec_Wp_Uri_Helper::get_current_url( true );
		$cookie_set = isset( $_COOKIE['ai1ec_saved_filter'] );
		if( false !== $cookie_set ) {
			$cookie = json_decode( stripslashes( $_COOKIE['ai1ec_saved_filter'] ), true );
			if (
				$calendar_url === $requested_page_url &&
				isset( $cookie['calendar_page'] ) &&
				$cookie['calendar_page'] !== $calendar_url
			) {
				$cookie_dto->set_calendar_cookie( $cookie['calendar_page'] );
				$cookie_dto->set_is_cookie_set_for_calendar_page( true );
				$cookie_dto->set_is_a_cookie_set_for_this_page( true );
			} else if ( isset( $cookie[$requested_page_url] ) ) {
				$cookie_dto->set_shortcode_cookie( $cookie[$requested_page_url] );
				$cookie_dto->set_is_cookie_set_for_shortcode( true );
				$cookie_dto->set_is_a_cookie_set_for_this_page( true );
			} else if (
				// we must make the is_page( $ai1ec_settings->calendar_page_id ) for a really edge case
				// when for example the calendar page is http://localhost/wordpress_pro/?page_id=1
				// and the requested page is http://localhost/wordpress_pro/?page_id=1234
				strpos( $requested_page_url, $calendar_url ) === 0 &&
				isset( $cookie['calendar_page'] ) &&
				is_page( $settings->get( 'calendar_page_id' ) )
				 ) {
				// This is the case after a redirect from the calendar page
				$cookie_dto->set_is_a_cookie_set_for_this_page( true );
				$cookie_dto->set_calendar_cookie( $cookie['calendar_page'] );
			}
		}
		return $cookie_dto;
	}

	/**
	 * Returns path for cookies.
	 *
	 * @return string
	 */
	public function get_path_for_cookie() {
		$parsed = parse_url( site_url() );
		return isset( $parsed['path'] ) ? $parsed['path'] : '/';
	}
}