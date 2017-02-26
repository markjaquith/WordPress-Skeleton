<?php

class CommentPaginationCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;

		// combine all the php files into one string to make it easier to search
		$php = implode( ' ', $php_files );
		checkcount();
		if (strpos( $php, 'paginate_comments_links' ) === false &&
		    (strpos( $php, 'next_comments_link' ) === false && strpos( $php, 'previous_comments_link' ) === false ) ) {

			$this->error[] = '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('The theme doesn\'t have comment pagination code in it. Use <strong>paginate_comments_links()</strong> or <strong>next_comments_link()</strong> and <strong>previous_comments_link()</strong> to add comment pagination.', 'theme-check' );
			$ret = false;
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new CommentPaginationCheck;