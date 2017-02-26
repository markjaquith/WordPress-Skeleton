<?php

class ArtisteerCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );

		checkcount();

		$ret = true;
		if (
			strpos( $php, 'art_normalize_widget_style_tokens' ) !== false
			|| strpos( $php, 'art_include_lib' ) !== false
			|| strpos( $php, '_remove_last_slash($url) {' ) !== false
			|| strpos( $php, 'adi_normalize_widget_style_tokens' ) !== false
			|| strpos( $php, 'm_normalize_widget_style_tokens' ) !== false
			|| strpos ( $php, "bw = '<!--- BEGIN Widget --->';" ) !== false
			|| strpos ( $php, "ew = '<!-- end_widget -->';" ) !== false
			|| strpos ( $php, "end_widget' => '<!-- end_widget -->'") !== false
		) {
			$this->error[] = "<span class='tc-lead tc-warning'>" . __('WARNING', 'theme-check' ). "</span>: " . __( 'This theme appears to have been auto-generated. Generated themes are not allowed in the themes directory.', 'theme-check' );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new ArtisteerCheck;