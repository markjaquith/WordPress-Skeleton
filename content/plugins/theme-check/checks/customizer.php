<?php

/**
 * Checks for the Customizer.
 */

class CustomizerCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;

		checkcount();

		/**
		 * Check whether every Customizer setting has a sanitization callback set.
		 */
		foreach ( $php_files as $file_path => $file_content ) {
			// Get the arguments passed to the add_setting method
			if ( preg_match_all( '/\$wp_customize->add_setting\(([^;]+)/', $file_content, $matches ) ) {
				// The full match is in [0], the match group in [1]
				foreach ( $matches[1] as $match ) {
					// Check if we have sanitize_callback or sanitize_js_callback
					if ( false === strpos( $match, 'sanitize_callback' ) && false === strpos( $match, 'sanitize_js_callback' ) ) {
						$this->error[] = '<span class="tc-lead tc-required">' . __('REQUIRED','theme-check') . '</span>: ' . __( 'Found a Customizer setting that did not have a sanitization callback function. Every call to the <strong>add_setting()</strong> method needs to have a sanitization callback function passed.', 'theme-check' );
						$ret = false;
					} else {
						// There's a callback, check that no empty parameter is passed.
						if ( preg_match( '/[\'"](?:sanitize_callback|sanitize_js_callback)[\'"]\s*=>\s*[\'"]\s*[\'"]/', $match ) ) {
							$this->error[] = '<span class="tc-lead tc-required">' . __('REQUIRED','theme-check') . '</span>: ' . __( 'Found a Customizer setting that had an empty value passed as sanitization callback. You need to pass a function name as sanitization callback.', 'theme-check' );
							$ret = false;
						}
					}
				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CustomizerCheck;