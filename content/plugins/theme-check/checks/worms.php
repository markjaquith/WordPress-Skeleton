<?php
class WormCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$php_files = array_merge( $php_files, $other_files );
		$checks = array(
			'/wshell\.php/'=> __( 'This may be a script used by hackers to get control of your server!', 'theme-check' ),
			'/ShellBOT/' => __( 'This may be a script used by hackers to get control of your server', 'theme-check' ),
			'/uname -a/' => __( 'Tells a hacker what operating system your server is running', 'theme-check' ),
			'/YW55cmVzdWx0cy5uZXQ=/' => __( 'base64 encoded text found in Search Engine Redirect hack <a href="http://blogbuildingu.com/wordpress/wordpress-search-engine-redirect-hack">[1]</a>', 'theme-check' ),
			'/\$_COOKIE\[\'yahg\'\]/' => __( 'YAHG Googlerank.info exploit code <a href="http://creativebriefing.com/wordpress-hacked-googlerankinfo/">[1]</a>', 'theme-check' ),
			'/ekibastos/' => __( 'Possible Ekibastos attack <a href="http://ocaoimh.ie/did-your-wordpress-site-get-hacked/">[1]</a>', 'theme-check' ),
			'/<script>\/\*(GNU GPL|LGPL)\*\/ try\{window.onload.+catch\(e\) \{\}<\/script>/' => __( 'Possible "Gumblar" JavaScript attack <a href="http://threatinfo.trendmicro.com/vinfo/articles/securityarticles.asp?xmlfile=042710-GUMBLAR.xml">[1]</a> <a href="http://justcoded.com/article/gumblar-family-virus-removal-tool/">[2]</a>', 'theme-check' ),
			'/php \$[a-zA-Z]*=\'as\';/' => __( 'Symptom of the "Pharma Hack" <a href="http://blog.sucuri.net/2010/07/understanding-and-cleaning-the-pharma-hack-on-wordpress.html">[1]</a>', 'theme-check' ),
			'/defined?\(\'wp_class_support/' => __( 'Symptom of the "Pharma Hack" <a href="http://blog.sucuri.net/2010/07/understanding-and-cleaning-the-pharma-hack-on-wordpress.html">[1]</a>', 'theme-check' ),
			'/AGiT3NiT3NiT3fUQKxJvI/' => __( 'Malicious footer code injection detected!', 'theme-check' )
			);

		foreach ( $php_files as $php_key => $phpfile ) {
			foreach ( $checks as $key => $check ) {
				checkcount();
				if ( preg_match( $key, $phpfile, $matches ) ) {
					$filename = tc_filename( $php_key );
					$error = $matches[0];
					$grep = tc_grep( $error, $php_key );
					$this->error[] = sprintf('<span class="tc-lead tc-warning">'. __( 'WARNING', 'theme-check') . '</span>: <strong>%1$s</strong> %2$s%3$s', $filename, $check, $grep );
					$ret = false;
				}
			}
		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new WormCheck;
