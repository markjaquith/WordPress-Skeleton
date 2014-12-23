<?php

class SearchFormCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		$checks = array( '/(include\s?\(\s?TEMPLATEPATH\s?\.?\s?["|\']\/searchform.php["|\']\s?\))/' => __( 'Please use <strong>get_search_form()</strong> instead of including searchform.php directly.', 'theme-check' ) );
		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ($checks as $key => $check) {
				checkcount();
				if ( preg_match( $key, $phpfile, $out ) ) {
					$grep = tc_preg( $key, $php_key );
					$filename = tc_filename( $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('<strong>%1$s</strong> %2$s%3$s', 'theme-check'), $filename, $check, $grep);
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new SearchFormCheck;