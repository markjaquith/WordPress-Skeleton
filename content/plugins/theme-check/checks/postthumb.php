<?php

class PostThumbnailCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();

		if ( strpos( $php, 'the_post_thumbnail' ) === false ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('No reference to <strong>the_post_thumbnail()</strong> was found in the theme. It is recommended that the theme implement this functionality instead of using custom fields for thumbnails.', 'theme-check' );
		}

		if ( strpos( $php, 'post-thumbnails' ) === false ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('No reference to post-thumbnails was found in the theme. If the theme has a thumbnail like functionality, it should be implemented with <strong>add_theme_support( "post-thumbnails" )</strong>in the functions.php file.', 'theme-check' );
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new PostThumbnailCheck;