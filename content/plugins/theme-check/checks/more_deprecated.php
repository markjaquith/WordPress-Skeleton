<?php

/**
 * Checks for the use of deprecated function parameters.
 */

class More_Deprecated implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
			'get_bloginfo' => array(
				'home'                 => 'home_url()',
				'url'                  => 'home_url()',
				'wpurl'                => 'site_url()',
				'stylesheet_directory' => 'get_stylesheet_directory_uri()',
				'template_directory'   => 'get_template_directory_uri()',
				'template_url'         => 'get_template_directory_uri()',
				'text_direction'       => 'is_rtl()',
				'feed_url'             => "get_feed_link( 'feed' ), where feed is rss, rss2 or atom",
			),
			'bloginfo' => array(
				'home'                 => 'echo esc_url( home_url() )',
				'url'                  => 'echo esc_url( home_url() )',
				'wpurl'                => 'echo esc_url( site_url() )',
				'stylesheet_directory' => 'echo esc_url( get_stylesheet_directory_uri() )',
				'template_directory'   => 'echo esc_url( get_template_directory_uri() )',
				'template_url'         => 'echo esc_url( get_template_directory_uri() )',
				'text_direction'       => 'is_rtl()',
				'feed_url'             => "echo esc_url( get_feed_link( 'feed' ) ), where feed is rss, rss2 or atom",
			),
			'get_option' => array(
				'home'     => 'home_url()',
				'site_url' => 'site_url()',
			)
		);

		foreach ( $php_files as $php_key => $php_file ) {
			// Loop through all functions.
			foreach ( $checks as $function => $data ) {
				checkcount();

				// Loop through the parameters and look for all function/parameter combinations.
				foreach ( $data as $parameter => $replacement ) {
					if ( preg_match( '/' . $function . '\(\s*("|\')' . $parameter . '("|\')\s*\)/', $php_file, $matches ) ) {
						$filename      = tc_filename( $php_key );
						$error         = ltrim( rtrim( $matches[0], '(' ) );
						$grep          = tc_grep( $error, $php_key );
						$this->error[] = sprintf( '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check' ) . '</span>: ' . __( '<strong>%1$s</strong> was found in the file <strong>%2$s</strong>. Use <strong>%3$s</strong> instead.%4$s', 'theme-check' ), $error, $filename, $replacement, $grep );
						$ret           = false;
					}
				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new More_Deprecated;