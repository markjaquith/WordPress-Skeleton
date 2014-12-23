<?php

class Required implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		$checks = array(
			'/\s?get_option\(\s?("|\')home("|\')\s?\)/' => 'home_url()',
			'/\s?get_option\(\s?("|\')site_url("|\')\s?\)/' => 'site_url()',
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$matches[0] = str_replace(array('"',"'"),'', $matches[0]);
					$error = esc_html( rtrim($matches[0], '(' ) );
					$grep = tc_grep( rtrim($matches[0], '(' ), $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('<strong>%1$s</strong> was found in the file <strong>%2$s</strong>. Use <strong>%3$s</strong> instead.%4$s', 'theme-check'), $error, $filename, $check, $grep);
					$ret = false;
				}
			}
		}
		return $ret;
	}
	function getError() { return $this->error; }
}
$themechecks[] = new Required;