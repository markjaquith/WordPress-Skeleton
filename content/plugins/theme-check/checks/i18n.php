<?php

// check for various I18N errors

class I18NCheck implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {
		$ret = true;
		$error = '';
		checkcount();

		// make sure the tokenizer is available
		if ( !function_exists( 'token_get_all' ) ) return true;

		foreach ( $php_files as $php_key => $phpfile ) {
			$error='';

			$stmts = array();
			foreach ( array('_e(', '__(', '_e (', '__ (') as $finder) {
				$search = $phpfile;
				while ( ( $pos = strpos($search, $finder) ) !== false ) {
					$search = substr($search,$pos);
					$open=1;
					$i=strpos($search,'(')+1;
					while( $open>0 ) {
						switch($search[$i]) {
						case '(':
							$open++; break;
						case ')':
							$open--; break;
						}
						$i++;
					}
					$stmts[] = substr($search,0,$i);
					$search = substr($search,$i);
				}
			}

			foreach ( $stmts as $match ) {
				$tokens = @token_get_all('<?php '.$match.';');
				if (!empty($tokens)) {
					foreach ($tokens as $token) {
						if (is_array($token) && in_array( $token[0], array( T_VARIABLE ) ) ) {
							$filename = tc_filename( $php_key );
							$grep = tc_grep( ltrim( $match ), $php_key );
							preg_match( '/[^\s]*\s[0-9]+/', $grep, $line);
							$error = ( !strpos( $error, $line[0] ) ) ? $grep : '';
							$this->error[] = sprintf('<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('Possible variable <strong>%1$s</strong> found in translation function in <strong>%2$s</strong>. Translation function calls must NOT contain PHP variables. %3$s','theme-check'),
								$token[1], $filename, $error);
							break; // stop looking at the tokens on this line once a variable is found
						}
					}
				}
			}


		}
		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new I18NCheck;