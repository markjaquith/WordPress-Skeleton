<?php
/**
 * Checks for the title:
 * Are there <title> and </title> tags?
 * Is there a call to wp_title()?
 * There can't be any hardcoded text in the <title> tag.
 *
 * See: http://make.wordpress.org/themes/guidelines/guidelines-theme-check/
 */
class Title_Checks implements themecheck {
    protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php = implode( ' ', $php_files );
		
		// Look for add_theme_support( 'title-tag' ) first
		$titletag = true;
		if ( ! preg_match( '#add_theme_support\s?\(\s?[\'|"]title-tag#', $php ) ) {
			$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('No reference to <strong>add_theme_support( "title-tag" )</strong> was found in the theme. It is recommended that the theme implement this functionality for WordPress 4.1 and above.', 'theme-check' );
			$titletag = false;
		}
		
		/**
		 * Look for <title> and </title> tags.
		 */
		checkcount();
		if ( ( false === strpos( $php, '<title>' ) || false === strpos( $php, '</title>' ) ) && !$titletag  ) {
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check').'</span>: ' . __( 'The theme needs to have <strong>&lt;title&gt;</strong> tags, ideally in the <strong>header.php</strong> file.', 'theme-check' );
			$ret = false;
		}

		/**
		 * Check whether there is a call to wp_title().
		 */
		checkcount();
		if ( false === strpos( $php, 'wp_title(' ) && !$titletag ) {
			$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check').'</span>: ' . __( 'The theme needs to have a call to <strong>wp_title()</strong>, ideally in the <strong>header.php</strong> file.', 'theme-check' );
			$ret = false;
		}

		/**
		 * Check whether the the <title> tag contains something besides a call to wp_title().
		 */
		checkcount();

		foreach ( $php_files as $file_path => $file_content ) {
			/**
			 * First looks ahead to see of there's <title>...</title>
			 * Then performs a negative look ahead for <title><?php wp_title(...); ?></title>
			 */
			if ( preg_match( '/(?=<title>(.*)<\/title>)(?!<title>\s*<\?php\s*wp_title\([^\)]*\);\s*\?>\s*<\/title>)/s', $file_content ) ) {
				$this->error[] = '<span class="tc-lead tc-required">' . __( 'REQUIRED', 'theme-check').'</span>: ' . __( 'The <strong>&lt;title&gt;</strong> tags can only contain a call to <strong>wp_title()</strong>. Use the  <strong>wp_title filter</strong> to modify the output', 'theme-check' );
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Title_Checks;
