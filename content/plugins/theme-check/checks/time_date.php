<?php
class Time_Date implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$checks = array(
			'/\sdate_i18n\(\s?["|\'][A-Za-z\s]+\s?["|\']\)/' => 'date_i18n( get_option( \'date_format\' ) )',
			'/[^get_]the_date\(\s?["|\'][A-Za-z\s]+\s?["|\']\)/' => 'the_date( get_option( \'date_format\' ) )',
			'/[^get_]the_time\(\s?["|\'][A-Za-z\s]+\s?["|\']\)/' => 'the_time( get_option( \'date_format\' ) )'
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$matches[0] = str_replace(array('"',"'"),'', $matches[0]);
					$error = trim( esc_html( rtrim( $matches[0], '(' ) ) );
					$this->error[] = sprintf( '<span class="tc-lead tc-info">' . __( 'INFO', 'theme-check' ) . '</span>: ' . __( "At least one hard coded date was found in the file <strong>%s</strong>. Consider get_option( 'date_format' ) instead.", 'theme-check' ), $filename );
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Time_Date;