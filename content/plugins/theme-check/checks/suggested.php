<?php
class Suggested implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
		'/[\s|]get_bloginfo\(\s?("|\')url("|\')\s?\)/' => 'home_url()',
		'/[\s|]get_bloginfo\(\s?("|\')wpurl("|\')\s?\)/' => 'site_url()',
		'/[\s|]get_bloginfo\(\s?("|\')stylesheet_directory("|\')\s?\)/' => 'get_stylesheet_directory_uri()',
		'/[\s|]get_bloginfo\(\s?("|\')template_directory("|\')\s?\)/' => 'get_template_directory_uri()',
		'/[\s|]get_bloginfo\(\s?("|\')template_url("|\')\s?\)/' => 'get_template_directory_uri()',
		'/[\s|]get_bloginfo\(\s?("|\')text_direction("|\')\s?\)/' => 'is_rtl()',
		'/[\s|]get_bloginfo\(\s?("|\')feed_url("|\')\s?\)/' => 'get_feed_link( \'feed\' ) (feed = rss, rss2, atom)',
		'/[\s|]bloginfo\(\s?("|\')url("|\')\s?\)/' => 'echo home_url()',
		'/[\s|]bloginfo\(\s?("|\')wpurl("|\')\s?\)/' => 'echo site_url()',
		'/[\s|]bloginfo\(\s?("|\')stylesheet_directory("|\')\s?\)/' => 'get_stylesheet_directory_uri()',
		'/[\s|]bloginfo\(\s?("|\')template_directory("|\')\s?\)/' => 'get_template_directory_uri()',
		'/[\s|]bloginfo\(\s?("|\')template_url("|\')\s?\)/' => 'get_template_directory_uri()',
		'/[\s|]bloginfo\(\s?("|\')text_direction("|\')\s?\)/' => 'is_rtl()',
		'/[\s|]bloginfo\(\s?("|\')feed_url("|\')\s?\)/' => 'get_feed_link( \'feed\' ) (feed = rss, rss2, atom)',
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$matches[0] = str_replace(array('"',"'"),'', $matches[0]);
					$error = trim( esc_html( rtrim($matches[0], '(' ) ) );
					$grep = tc_grep( rtrim( $matches[0], '(' ), $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-recommended">' . __( 'RECOMMENDED', 'theme-check' ) . '</span>: '. __( '<strong>%1$s</strong> was found in the file <strong>%2$s</strong>. Use <strong>%3$s</strong> instead.%4$s', 'theme-check'), $error, $filename, $check, $grep);
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Suggested;