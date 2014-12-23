<?php
// recommended deprecations checks... After some time, these will move into deprecated.php and become required.
class Deprecated_Recommended implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$grep = '';

		$ret = true;

		$checks = array(
			array( 'rich_edit_exists' => '', '3.9'),
			array( 'default_topic_count_text' => '', '3.9'),
			array( 'format_to_post' => '', '3.9'),
			array( 'get_current_site_name' => 'get_current_site()', '3.9'),
			array( 'wpmu_current_site' => '', '3.9'),
			array( 'get_all_category_ids' => 'get_terms()', '4.0' ),
			array( 'like_escape' => 'wpdb::esc_like()', '4.0' ),
			array( 'url_is_accessable_via_ssl' => '', '4.0' ),
		);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $alt => $check ) {
				checkcount();
				$version = $check;
				$key = key( $check );
				$alt = $check[ $key ];
				if ( preg_match( '/[\s?]' . $key . '\(/', $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = ltrim( rtrim( $matches[0], '(' ) );
					$version = $check[0];
					$grep = tc_grep( $error, $php_key );

					// Point out the deprecated function.
					$error_msg = sprintf(
						__( '%1$s found in the file %2$s. Deprecated since version %3$s.', 'theme-check' ),
						'<strong>' . $error . '()</strong>',
						'<strong>' . $filename . '</strong>',
						'<strong>' . $version . '</strong>'
					);

					// Add alternative function when available.
					if ( $alt ) {
						$error_msg .= ' ' . sprintf( __( 'Use %s instead.', 'theme-check' ), '<strong>' . $alt . '</strong>' );
					}

					// Add the precise code match that was found.
					$error_msg .= $grep;

					// Add the finalized error message.
					$this->error[] = '<span class="tc-lead tc-recommended">' . __('RECOMMENDED','theme-check') . '</span>: ' . $error_msg;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Deprecated_Recommended;