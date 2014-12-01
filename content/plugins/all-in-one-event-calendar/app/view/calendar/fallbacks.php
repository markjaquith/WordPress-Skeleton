<?php

/**
 * The concrete class for the calendar page.
 *
 * @author     Time.ly Network Inc.
 * @since      2.1
 *
 * @package    AI1EC
 * @subpackage AI1EC.View
 */
class Ai1ec_Calendar_Avatar_Fallbacks extends Ai1ec_Base {

	/**
	 * Default avatar fallbacks.
	 *
	 * @var array
	 */
	protected $_fallbacks = array(
		'post_thumbnail',
		'content_img',
		'category_avatar',
	);

	/**
	 * Get registered fallbacks.
	 *
	 * @return array
	 */
	public function get_all() {
		return $this->_fallbacks;
	}

	/**
	 * Register new avatar fallbacks.
	 *
	 * @param array $fallbacks Fallbacks.
	 *
	 * @return void Method does not return.
	 */
	public function set( array $fallbacks ) {
		$this->_fallbacks = $fallbacks;
	}

}
