<?php
class NonPrintableCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		foreach ( $php_files as $name => $content ) {
		checkcount();
			// 09 = tab
			// 0A = line feed
			// 0D = new line
			if ( preg_match('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x80-\xFF]/', $content, $matches ) ) {
				$filename = tc_filename( $name );
				$non_print = tc_preg( '/[\x00-\x08\x0B-\x0C\x0E-\x1F\x80-\xFF]/', $name );
				$this->error[] = sprintf('<span class="tc-lead tc-info">'.__('INFO','theme-check').'</span>: '.__('Non-printable characters were found in the <strong>%1$s</strong> file. You may want to check this file for errors.%2$s', 'theme-check'), $filename, $non_print);
			}
		}

		// return the pass/fail
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new NonPrintableCheck;