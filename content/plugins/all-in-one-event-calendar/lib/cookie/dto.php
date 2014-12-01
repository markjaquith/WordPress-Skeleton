<?php

/**
 * The cookie Data Transfer Object.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Cookie
 */
class Ai1ec_Cookie_Present_Dto {

	/**
	 * @var boolean
	 */
	private $is_cookie_set_for_calendar_page = false;

	/**
	 * @var boolean
	 */
	private $is_cookie_set_for_shortcode = false;

	/**
	 * @var array
	 */
	private $shortcode_cookie;

	/**
	 * @var string
	 */
	private $calendar_cookie;

	/**
	 * @var boolean
	 */
	private $is_a_cookie_set_for_this_page = false;

	/**
	 * @return the $is_a_cookie_set_for_this_page
	 */
	public function get_is_a_cookie_set_for_this_page() {
		return $this->is_a_cookie_set_for_this_page;
	}

	/**
	 * @param boolean $is_a_cookie_set_for_this_page
	 */
	public function set_is_a_cookie_set_for_this_page(
		$is_a_cookie_set_for_this_page
	) {
		$this->is_a_cookie_set_for_this_page = $is_a_cookie_set_for_this_page;
	}

	/**
	 * @return boolean the $is_calendar_page
	 */
	public function get_is_cookie_set_for_calendar_page() {
		return $this->is_cookie_set_for_calendar_page;
	}

	/**
	 * @return boolean the $is_cookie_set
	 */
	public function get_is_cookie_set_for_shortcode() {
		return $this->is_cookie_set_for_shortcode;
	}

	/**
	 * @return array the $shortcode_cookie
	 */
	public function get_shortcode_cookie() {
		return $this->shortcode_cookie;
	}

	/**
	 * @return string the $calendar_cookie
	 */
	public function get_calendar_cookie() {
		return $this->calendar_cookie;
	}

	/**
	 * @param boolean $is_calendar_page
	 */
	public function set_is_cookie_set_for_calendar_page( $is_cookie_set_for_calendar_page ) {
		$this->is_cookie_set_for_calendar_page = $is_cookie_set_for_calendar_page;
	}

	/**
	 * @param boolean $is_cookie_set
	 */
	public function set_is_cookie_set_for_shortcode( $is_cookie_set ) {
		$this->is_cookie_set_for_shortcode = $is_cookie_set;
	}

	/**
	 * @param multitype: $shortcode_cookie
	 */
	public function set_shortcode_cookie( $shortcode_cookie ) {
		$this->shortcode_cookie = $shortcode_cookie;
	}

	/**
	 * @param string $calendar_cookie
	 */
	public function set_calendar_cookie( $calendar_cookie ) {
		$this->calendar_cookie = $calendar_cookie;
	}

}