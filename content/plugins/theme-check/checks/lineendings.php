<?php
class LineEndingsCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		foreach ( $php_files as $php_key => $phpfile ) {
			if (preg_match("/\r\n/",$phpfile)) {
				if (preg_match("/[^\r]\n/",$phpfile)) {
					$filename = tc_filename( $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('Both DOS and UNIX style line endings were found in the file <strong>%1$s</strong>. This causes a problem with SVN repositories and must be corrected before the theme can be accepted. Please change the file to use only one style of line endings.', 'theme-check'), $filename);
					$ret = false;
				}
			}
		}
		foreach ( $css_files as $css_key => $cssfile ) {
			if (preg_match("/\r\n/",$cssfile)) {
				if (preg_match("/[^\r]\n/",$cssfile)) {
					$filename = tc_filename( $css_key );
					$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('Both DOS and UNIX style line endings were found in the file <strong>%1$s</strong>. This causes a problem with SVN repositories and must be corrected before the theme can be accepted. Please change the file to use only one style of line endings.', 'theme-check'), $filename);
					$ret = false;
				}
			}
		}
		foreach ( $other_files as $oth_key => $othfile ) {
			$e = pathinfo($oth_key);
			if ( isset( $e['extension'] ) && in_array( $e['extension'], array( 'txt','js' ) ) ) {
				if (preg_match("/\r\n/",$othfile)) {
					if (preg_match("/[^\r]\n/",$othfile)) {
						$filename = tc_filename( $oth_key );
					$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('Both DOS and UNIX style line endings were found in the file <strong>%1$s</strong>. This causes a problem with SVN repositories and must be corrected before the theme can be accepted. Please change the file to use only one style of line endings.', 'theme-check'), $filename);
						$ret = false;
					}
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new LineEndingsCheck;
