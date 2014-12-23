<?php

class WidgetsCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();

		// no widgets registered or used...
		if ( strpos( $php, 'register_sidebar' ) === false && strpos( $php, 'dynamic_sidebar' ) === false ) {
			$this->error[] = "<span class='tc-lead tc-recommended'>" . __( "RECOMMENDED", 'theme-check') . '</span>: '. __( "This theme contains no sidebars/widget areas. See <a href='https://codex.wordpress.org/Widgets_API'>Widgets API</a>", "theme-check" );
			$ret = true;
		}

		if ( strpos( $php, 'register_sidebar' ) !== false && strpos( $php, 'dynamic_sidebar' ) === false ) {
			$this->error[] = "<span class='tc-lead tc-required'>" . __( "REQUIRED", 'theme-check') . '</span>: '. __( "The theme appears to use <strong>register_sidebar()</strong> but no <strong>dynamic_sidebar()</strong> was found. See: <a href='https://codex.wordpress.org/Function_Reference/dynamic_sidebar'>dynamic_sidebar</a><pre> &lt;?php dynamic_sidebar( \$index ); ?&gt;</pre>", "theme-check" );
			$ret = false;
		}

		if ( strpos( $php, 'register_sidebar' ) === false && strpos( $php, 'dynamic_sidebar' ) !== false ) {
			$this->error[] = "<span class='tc-lead tc-required'>" . __( "REQUIRED", 'theme-check') . '</span>: '. __( "The theme appears to use <strong>dynamic_sidebars()</strong> but no <strong>register_sidebar()</strong> was found. See: <a href='https://codex.wordpress.org/Function_Reference/register_sidebar'>register_sidebar</a><pre> &lt;?php register_sidebar( \$args ); ?&gt;</pre>", "theme-check" );
			$ret = false;
		}

		/**
		 * There are widgets registered, is the widgets_init action present?
		 */
		if ( strpos( $php, 'register_sidebar' ) !== false && preg_match( '/add_action\(\s*("|\')widgets_init("|\')\s*,/', $php ) == false ) {
			$this->error[] = "<span class='tc-lead tc-required'>" . __( "REQUIRED", 'theme-check') . '</span>: '. sprintf( __( "Sidebars need to be registered in a custom function hooked to the <strong>widgets_init</strong> action. See: %s.", "theme-check" ), '<a href="https://codex.wordpress.org/Function_Reference/register_sidebar">register_sidebar()</a>' );
			$ret = false;
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new WidgetsCheck;
