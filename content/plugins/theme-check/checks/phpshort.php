<?php
class PHPShortTagsCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		foreach ( $php_files as $php_key => $phpfile ) {
			checkcount();
			if ( preg_match( '/<\?(\=?)(?!php|xml)/i', $phpfile ) ) {
				$filename = tc_filename( $php_key );
				$grep = tc_preg( '/<\?(\=?)(?!php|xml)/', $php_key );
				$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('Found PHP short tags in file <strong>%1$s</strong>.%2$s', 'theme-check'), $filename, $grep);
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new PHPShortTagsCheck;