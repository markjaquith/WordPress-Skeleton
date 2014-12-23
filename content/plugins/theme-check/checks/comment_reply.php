<?php
class Comment_Reply implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$php = implode( ' ', $php_files );
		$ret = true;

		checkcount();

		if ( ! preg_match( '/wp_enqueue_script\(\s?("|\')comment-reply("|\')/i', $php ) ) {
			if ( ! preg_match( '/comment-reply/', $php ) ) {
				$check = __( 'See: <a href="https://codex.wordpress.org/Migrating_Plugins_and_Themes_to_2.7/Enhanced_Comment_Display">Migrating Plugins and Themes to 2.7/Enhanced Comment Display</a><pre> &lt;?php if ( is_singular() ) wp_enqueue_script( "comment-reply" ); ?&gt;</pre>', 'theme-check' );
				$this->error[] = sprintf('<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('Could not find the <strong>comment-reply</strong> script enqueued. %1$s', 'theme-check'), $check);
				$ret = false;
			} else {
				$this->error[] = '<span class="tc-lead tc-info">'.__('INFO','theme-check').'</span>: '.__('Could not find the <strong>comment-reply</strong> script enqueued, however a reference to \'comment-reply\' was found. Make sure that the comment-reply script is being enqueued properly on singular pages.', 'theme-check');
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Comment_Reply;
