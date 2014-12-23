<?php
class Bad_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		$checks = array(
			'/(?<![_|a-z0-9|\.])eval\s?\(/i' => __( 'eval() is not allowed.', 'theme-check' ),
			'/[^a-z0-9](?<!_)(popen|proc_open|[^_]exec|shell_exec|system|passthru)\(/' => __( 'PHP system calls are often disabled by server admins and should not be in themes', 'theme-check' ),
			'/\s?ini_set\(/' => __( 'Themes should not change server PHP settings', 'theme-check' ),
			'/base64_decode/' => __( 'base64_decode() is not allowed', 'theme-check' ),
			'/base64_encode/' => __( 'base64_encode() is not allowed', 'theme-check' ),
			'/uudecode/ims' => __( 'uudecode() is not allowed', 'theme-check' ),
			'/str_rot13/ims' => __( 'str_rot13() is not allowed', 'theme-check' ),
			'/cx=[0-9]{21}:[a-z0-9]{10}/' => __( 'Google search code detected', 'theme-check' ),
			'/pub-[0-9]{16}/i' => __( 'Google advertising code detected', 'theme-check' )
			);

		$grep = '';

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
			checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( trim( $matches[0], '(' ) );
					$grep = tc_grep( $error, $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-warning">'. __( 'WARNING', 'theme-check' ) . '</span>: Found <strong>%1$s</strong> in the file <strong>%2$s</strong>. %3$s. %4$s', $error, $filename, $check, $grep );
					$ret = false;
				}
			}
		}


		$checks = array(
			'/cx=[0-9]{21}:[a-z0-9]{10}/' => __( 'Google search code detected', 'theme-check' ),
			'/pub-[0-9]{16}/' => __( 'Google advertising code detected', 'theme-check' )
			);

		foreach ( $other_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0],'(' ) );
					$grep = tc_grep( $error, $php_key );
					$this->error[] = sprintf(__('<span class="tc-lead tc-warning">WARNING</span>: Found <strong>%1$s</strong> in the file <strong>%2$s</strong>. %3$s.%4$s', 'theme-check'), $error, $filename, $check, $grep);
					$ret = false;
				}
			}
		}
		return $ret;
	}
	function getError() { return $this->error; }
}
$themechecks[] = new Bad_Checks;
