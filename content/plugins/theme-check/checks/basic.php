<?php

// do some basic checks for strings
class Basic_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files) {

		$php = implode( ' ', $php_files );
		$grep = '';
		$ret = true;

		$checks = array(
			'DOCTYPE' => __( 'See: <a href="https://codex.wordpress.org/HTML_to_XHTML">https://codex.wordpress.org/HTML_to_XHTML</a><pre>&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"<br />"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"?&gt;</pre>', 'theme-check' ),
			'wp_footer\(' => __( 'See: <a href="https://codex.wordpress.org/Function_Reference/wp_footer">wp_footer</a><pre> &lt;?php wp_footer(); ?&gt;</pre>', 'theme-check' ),
			'wp_head\(' => __( 'See: <a href="https://codex.wordpress.org/Function_Reference/wp_head">wp_head</a><pre> &lt;?php wp_head(); ?&gt;</pre>', 'theme-check' ),
			'language_attributes' => __( 'See: <a href="https://codex.wordpress.org/Function_Reference/language_attributes">language_attributes</a><pre>&lt;html &lt;?php language_attributes(); ?&gt;</pre>', 'theme-check' ),
			'charset' => __( 'There must be a charset defined in the Content-Type or the meta charset tag in the head.', 'theme-check' ),
			'add_theme_support\(\s?("|\')automatic-feed-links("|\')\s?\)' => __( 'See: <a href="https://codex.wordpress.org/Function_Reference/add_theme_support">add_theme_support</a><pre> &lt;?php add_theme_support( $feature ); ?&gt;</pre>', 'theme-check' ),
			'comments_template\(' => __( 'See: <a href="https://codex.wordpress.org/Template_Tags/comments_template">comments_template</a><pre> &lt;?php comments_template( $file, $separate_comments ); ?&gt;</pre>', 'theme-check' ),
			'wp_list_comments\(' => __( 'See: <a href="https://codex.wordpress.org/Template_Tags/wp_list_comments">wp_list_comments</a><pre> &lt;?php wp_list_comments( $args ); ?&gt;</pre>', 'theme-check' ),
			'comment_form\(' => __( 'See: <a href="https://codex.wordpress.org/Template_Tags/comment_form">comment_form</a><pre> &lt;?php comment_form(); ?&gt;</pre>', 'theme-check' ),
			'body_class' => __( 'See: <a href="https://codex.wordpress.org/Template_Tags/body_class">body_class</a><pre> &lt;?php body_class( $class ); ?&gt;</pre>', 'theme-check' ),
			'wp_link_pages\(' => __( 'See: <a href="https://codex.wordpress.org/Function_Reference/wp_link_pages">wp_link_pages</a><pre> &lt;?php wp_link_pages( $args ); ?&gt;</pre>', 'theme-check' ),
			'post_class\(' => __( 'See: <a href="https://codex.wordpress.org/Template_Tags/post_class">post_class</a><pre> &lt;div id="post-&lt;?php the_ID(); ?&gt;" &lt;?php post_class(); ?&gt;&gt;</pre>', 'theme-check' )
			);

		foreach ($checks as $key => $check) {
			checkcount();
			if ( !preg_match( '/' . $key . '/i', $php ) ) {
				if ( $key === 'add_theme_support\(\s?("|\')automatic-feed-links("|\')\s?\)' ) $key = __( 'add_theme_support( \'automatic-feed-links\' )', 'theme-check');
				if ( $key === 'wp_enqueue_script\(\s?("|\')comment-reply("|\')' ) $key = __( 'wp_enqueue_script( \'comment-reply\' )', 'theme-check');
				if ( $key === 'body_class' ) $key = __( 'body_class call in body tag', 'theme-check');
				if ( $key === 'register_sidebar[s]?\(' ) $key = __( 'register_sidebar() or register_sidebars()', 'theme-check');
				$key = ltrim( trim ( trim( $key, '(' ), '\\' ) );
				$this->error[] = sprintf( '<span class="tc-lead tc-required">'.__('REQUIRED','theme-check').'</span>: '.__('Could not find <strong>%1$s</strong>. %2$s', 'theme-check' ), $key, $check );
				$ret = false;
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}

$themechecks[] = new Basic_Checks;
