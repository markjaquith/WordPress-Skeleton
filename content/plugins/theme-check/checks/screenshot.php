<?php
class Screenshot_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;
		$filenames = array();

		foreach ( $other_files as $other_key => $otherfile ) {
			array_push( $filenames, strtolower( basename( $other_key ) ) );
		}

		checkcount();

		if ( in_array( 'screenshot.png', $filenames ) || in_array( 'screenshot.jpg', $filenames ) ) {

			foreach ( $other_files as $other_key => $otherfile ) {

				if ( ( basename( $other_key ) === 'screenshot.png' || basename( $other_key ) === 'screenshot.jpg' ) && preg_match( '/.*themes\/[^\/]*\/screenshot\.(png|jpg)/', $other_key ) )  {
					// we have or screenshot!
					$image = getimagesize( $other_key );
					if ( $image[0] > 880 || $image[1] > 660 ) {
						$this->error[] = sprintf('<span class="tc-lead tc-recommended">'. __( 'RECOMMENDED','theme-check' ) . '</span>: ' . __( 'Screenshot is wrong size! Detected: <strong>%1$sx%2$spx</strong>. Maximum allowed size is 880x660px.', 'theme-check' ), $image[0], $image[1]);
					}
					if ( $image[1] / $image[0] != 0.75 ) {
						$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('Screenshot dimensions are wrong! Ratio of width to height should be 4:3.', 'theme-check');
					}
					if ( $image[0] != 880 || $image[1] != 660 ) {
						$this->error[] = '<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('Screenshot size should be 880x660, to account for HiDPI displays. Any 4:3 image size is acceptable, but 880x660 is preferred.', 'theme-check');
					}
				}
			}
		} else {
			$this->error[] = '<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('No screenshot detected! Please include a screenshot.png or screenshot.jpg.', 'theme-check' );
			$ret = false;
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Screenshot_Checks;
