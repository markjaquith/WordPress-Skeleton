<?php
class Style_Tags implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		checkcount();
		$ret = true;
		$filenames = array();

		foreach( $css_files as $cssfile => $content ) {
			if ( basename( $cssfile ) === 'style.css' ) $data = get_theme_data_from_contents( $content );
		}

		if ( !$data[ 'Tags' ] ) {
			$this->error[] = '<span class="tc-lead tc-recommended">' . __('RECOMMENDED','theme-check') . '</span>: ' . __( '<strong>Tags:</strong> is either empty or missing in style.css header.', 'theme-check' );
			return $ret;
		}

		$allowed_tags = array("black","blue","brown","gray","green","orange","pink","purple","red","silver","tan","white","yellow","dark","light","one-column","two-columns","three-columns","four-columns","left-sidebar","right-sidebar","fixed-layout","fluid-layout","responsive-layout","flexible-header","accessibility-ready","blavatar","buddypress","custom-background","custom-colors","custom-header","custom-menu","editor-style","featured-image-header","featured-images","front-page-post-form","full-width-template","microformats","post-formats","rtl-language-support","sticky-post","theme-options","threaded-comments","translation-ready","holiday","photoblogging","seasonal");

		foreach( $data[ 'Tags' ] as $tag ) {
			if ( !in_array( strtolower( $tag ), $allowed_tags ) ) {
				if ( in_array( strtolower( $tag ), array("flexible-width","fixed-width") ) ) {
					$this->error[] = '<span class="tc-lead tc-warning">'. __('WARNING','theme-check'). '</span>: ' . __( 'The flexible-width and fixed-width tags changed to fluid-layout and fixed-layout tags in WordPress 3.8. Additionally, the responsive-layout tag was added. Please change to using one of the new tags.', 'theme-check' );
				} else {
					$this->error[] = '<span class="tc-lead tc-warning">'. __('WARNING','theme-check'). '</span>: ' . sprintf(__('Found wrong tag, remove <strong>%1$s</strong> from your style.css header.', 'theme-check'), $tag);
					$ret = false;
				}
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new Style_Tags;