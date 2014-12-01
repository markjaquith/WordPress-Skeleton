<?php
/**
 * Rewrite helper class.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Rewrite
 */
class Ai1ec_Rewrite_Helper {

	/**
	 * Remove rewrite rules and then recreate rewrite rules.
	 *
	 * @param bool $hard Whether to update .htaccess (hard flush) or just update
	 * 	rewrite_rules transient (soft flush). Default is true (hard).
	 */
	public function flush_rewrite_rules( $hard = true ) {
		flush_rewrite_rules( $hard );
	}
}