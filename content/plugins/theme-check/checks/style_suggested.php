<?php
class Style_Suggested implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		// combine all the css files into one string to make it easier to search
		$css = implode( ' ', $css_files );

		checkcount();
		$ret = true;

		$checks = array(
			'[ \t\/*#]*Theme URI:' => 'Theme URI:',
			'[ \t\/*#]*Author URI:' => 'Author URI:',
			);

		foreach ($checks as $key => $check) {
			if ( !preg_match( '/' . $key . '/i', $css, $matches ) ) {
				$this->error[] = sprintf('<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('<strong>%1$s</strong> is missing from your style.css header.', 'theme-check'), $check);
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Style_Suggested;