<?php

/**
 * Categories filtering implementation.
 *
 * @instantiator new
 * @author       Time.ly Network Inc.
 * @since        2.0
 * @package      AI1EC
 * @subpackage   AI1EC.Filter
 */
class Ai1ec_Filter_Categories extends Ai1ec_Filter_Taxonomy {

	public function get_taxonomy() {
		return 'events_categories';
	}

}