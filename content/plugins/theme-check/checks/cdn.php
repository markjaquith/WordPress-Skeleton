<?php

/**
 * Checks for resources being loaded from CDNs.
 */

class CDNCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$ret = true;
		$php_code = implode( ' ', $php_files );

		checkcount();

		$cdn_list = array(
			'bootstrap-maxcdn'      => 'maxcdn.bootstrapcdn.com/bootstrap',
			'bootstrap-netdna'      => 'netdna.bootstrapcdn.com/bootstrap',
			'bootswatch-maxcdn'     => 'maxcdn.bootstrapcdn.com/bootswatch',
			'bootswatch-netdna'     => 'netdna.bootstrapcdn.com/bootswatch',
			'font-awesome-maxcdn'   => 'maxcdn.bootstrapcdn.com/font-awesome',
			'font-awesome-netdna'   => 'netdna.bootstrapcdn.com/font-awesome',
			'html5shiv-google'      => 'html5shiv.googlecode.com/svn/trunk/html5.js',
			'html5shiv-maxcdn'      => 'oss.maxcdn.com/libs/html5shiv',
			'jquery'                => 'code.jquery.com/jquery-',
			'respond-js'            => 'oss.maxcdn.com/libs/respond.js',
		);

		foreach( $cdn_list as $cdn_slug => $cdn_url ) {
			if ( false !== strpos( $php_code, $cdn_url ) ) {
				$this->error[] = '<span class="tc-lead tc-recommended">' . __('RECOMMENDED','theme-check') . '</span>: ' . sprintf( __( 'Found the URL of a CDN in the code: %s. You should not load CSS or Javascript resources from a CDN, please bundle them with the theme.', 'theme-check' ), '<code>' . esc_html( $cdn_url ) . '</code>' );
				//$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CDNCheck;