<?php
class File_Checks implements themecheck {
	protected $error = array();

	function check( $php_files, $css_files, $other_files ) {

		$ret = true;

		$filenames = array();

		foreach ( $php_files as $php_key => $phpfile ) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		foreach ( $other_files as $php_key => $phpfile ) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		foreach ( $css_files as $php_key => $phpfile ) {
			array_push( $filenames, strtolower( basename( $php_key ) ) );
		}
		$blacklist = array(
				'thumbs.db'				=> __( 'Windows thumbnail store', 'theme-check' ),
				'desktop.ini'			=> __( 'windows system file', 'theme-check' ),
				'project.properties'	=> __( 'NetBeans Project File', 'theme-check' ),
				'project.xml'			=> __( 'NetBeans Project File', 'theme-check' ),
				'\.kpf'					=> __( 'Komodo Project File', 'theme-check' ),
				'^\.+[a-zA-Z0-9]'		=> __( 'Hidden Files or Folders', 'theme-check' ),
				'php.ini'				=> __( 'PHP server settings file', 'theme-check' ),
				'dwsync.xml'			=> __( 'Dreamweaver project file', 'theme-check' ),
				'error_log'				=> __( 'PHP error log', 'theme-check' ),
				'web.config'			=> __( 'Server settings file', 'theme-check' ),
				'\.sql'					=> __( 'SQL dump file', 'theme-check' ),
				'__MACOSX'				=> __( 'OSX system file', 'theme-check' )
				);

		$musthave = array( 'index.php', 'comments.php', 'style.css' );
		$rechave = array( 'readme.txt' => __( 'Please see <a href="https://codex.wordpress.org/Theme_Review#Theme_Documentation">Theme_Documentation</a> for more information.', 'theme-check' ) );

		checkcount();

		foreach( $blacklist as $file => $reason ) {
			if ( $filename = preg_grep( '/' . $file . '/', $filenames ) ) {
				$error = implode( array_unique( $filename ), ' ' );
				$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('<strong>%1$s</strong> %2$s found.', 'theme-check'), $error, $reason) ;
				$ret = false;
			}
		}

		foreach( $musthave as $file ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = sprintf('<span class="tc-lead tc-warning">'.__('WARNING','theme-check').'</span>: '.__('could not find the file <strong>%1$s</strong> in the theme.', 'theme-check'), $file);
				$ret = false;
			}
		}

		foreach( $rechave as $file => $reason ) {
			if ( !in_array( $file, $filenames ) ) {
				$this->error[] = sprintf('<span class="tc-lead tc-recommended">'.__('RECOMMENDED','theme-check').'</span>: '.__('could not find the file <strong>%1$s</strong> in the theme. %2$s', 'theme-check'), $file, $reason );
			}
		}

		return $ret;
	}

	function getError() { return $this->error; }
}
$themechecks[] = new File_Checks;
