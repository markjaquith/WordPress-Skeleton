<?php

class PostFormatCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$php = implode( ' ', $php_files );
		$css = implode( ' ', $css_files );

		checkcount();

		$checks = array(
			'/add_theme_support\(\s?("|\')post-formats("|\')/m'
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $check ) {
				checkcount();
				if ( preg_match( $check, $phpfile, $matches ) ) {
					if ( !strpos( $php, 'get_post_format' ) && !strpos( $php, 'has_post_format' ) && !strpos( $css, '.format' ) ) {
						$filename = tc_filename( $php_key );
						$matches[0] = str_replace(array('"',"'"),'', $matches[0]);
						$error = esc_html( rtrim($matches[0], '(' ) );
						$grep = tc_grep( rtrim($matches[0], '(' ), $php_key);
						$this->error[] = sprintf('<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('<strong>%1$s</strong> was found in the file <strong>%2$s</strong>. However get_post_format and/or has_post_format were not found, and no use of formats in the CSS was detected.', 'theme-check'), $error, $filename);
						$ret = false;
					}
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new PostFormatCheck;