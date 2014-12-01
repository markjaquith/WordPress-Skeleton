<?php

/**
 * Authors filtering implementation.
 *
 * @instantiator new
 * @author       Time.ly Network Inc.
 * @since        2.0
 * @package      AI1EC
 * @subpackage   AI1EC.Filter
 */
class Ai1ec_Filter_Authors extends Ai1ec_Filter_Int {

	public function get_field() {
		return 'p.post_author';
	}

}