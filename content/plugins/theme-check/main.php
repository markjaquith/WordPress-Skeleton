<?php
function check_main( $theme ) {
	global $themechecks, $data, $themename;
	$themename = $theme;
	$theme = get_theme_root( $theme ) . "/$theme";
	$files = listdir( $theme );
	$data = tc_get_theme_data( $theme . '/style.css' );
	if ( $data[ 'Template' ] ) {
		// This is a child theme, so we need to pull files from the parent, which HAS to be installed.
		$parent = get_theme_root( $data[ 'Template' ] ) . '/' . $data['Template'];
		if ( ! tc_get_theme_data( $parent . '/style.css' ) ) { // This should never happen but we will check while were here!
			echo '<h2>' . sprintf(__('Parent theme <strong>%1$s</strong> not found! You have to have parent AND child-theme installed!', 'theme-check'), $data[ 'Template' ] ) . '</h2>';
			return;
		}
		$parent_data = tc_get_theme_data( $parent . '/style.css' );
		$themename = basename( $parent );
		$files = array_merge( listdir( $parent ), $files );
	}

	if ( $files ) {
		foreach( $files as $key => $filename ) {
			if ( substr( $filename, -4 ) == '.php' ) {
				$php[$filename] = php_strip_whitespace( $filename );
			}
			else if ( substr( $filename, -4 ) == '.css' ) {
				$css[$filename] = file_get_contents( $filename );
			}
			else {
				$other[$filename] = ( ! is_dir($filename) ) ? file_get_contents( $filename ) : '';
			}
		}

		// run the checks
		$success = run_themechecks($php, $css, $other);

		global $checkcount;

		// second loop, to display the errors
		echo '<h2>' . __( 'Theme Info', 'theme-check' ) . ': </h2>';
		echo '<div class="theme-info">';
		if (file_exists( trailingslashit( WP_CONTENT_DIR . '/themes' ) . trailingslashit( basename( $theme ) ) . 'screenshot.png' ) ) {
			$image = getimagesize( $theme . '/screenshot.png' );
			echo '<div style="float:right" class="theme-info"><img style="max-height:180px;" src="' . trailingslashit( WP_CONTENT_URL . '/themes' ) . trailingslashit( basename( $theme ) ) . 'screenshot.png" />';
			echo '<br /><div style="text-align:center">' . $image[0] . 'x' . $image[1] . ' ' . round( filesize( $theme . '/screenshot.png' )/1024 ) . 'k</div></div>';
		}

		echo ( !empty( $data[ 'Title' ] ) ) ? '<p><label>' . __( 'Title', 'theme-check' ) . '</label><span class="info">' . $data[ 'Title' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'Version' ] ) ) ? '<p><label>' . __( 'Version', 'theme-check' ) . '</label><span class="info">' . $data[ 'Version' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'AuthorName' ] ) ) ? '<p><label>' . __( 'Author', 'theme-check' ) . '</label><span class="info">' . $data[ 'AuthorName' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'AuthorURI' ] ) ) ? '<p><label>' . __( 'Author URI', 'theme-check' ) . '</label><span class="info"><a href="' . $data[ 'AuthorURI' ] . '">' . $data[ 'AuthorURI' ] . '</a>' . '</span></p>' : '';
		echo ( !empty( $data[ 'URI' ] ) ) ? '<p><label>' . __( 'Theme URI', 'theme-check' ) . '</label><span class="info"><a href="' . $data[ 'URI' ] . '">' . $data[ 'URI' ] . '</a>' . '</span></p>' : '';
		echo ( !empty( $data[ 'License' ] ) ) ? '<p><label>' . __( 'License', 'theme-check' ) . '</label><span class="info">' . $data[ 'License' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'License URI' ] ) ) ? '<p><label>' . __( 'License URI', 'theme-check' ) . '</label><span class="info">' . $data[ 'License URI' ] . '</span></p>' : '';
		echo ( !empty( $data[ 'Tags' ] ) ) ? '<p><label>' . __( 'Tags', 'theme-check' ) . '</label><span class="info">' . implode( $data[ 'Tags' ], ', ') . '</span></p>' : '';
		echo ( !empty( $data[ 'Description' ] ) ) ? '<p><label>' . __( 'Description', 'theme-check' ) . '</label><span class="info">' . $data[ 'Description' ] . '</span></p>' : '';

		if ( $data[ 'Template' ] ) {
		if ( $data['Template Version'] > $parent_data['Version'] ) {
			echo '<p>' . sprintf(__('This child theme requires at least version <strong>%1$s</strong> of theme <strong>%2$s</strong> to be installed. You only have <strong>%3$s</strong> please update the parent theme.', 'theme-check'), $data['Template Version'], $parent_data['Title'], $parent_data['Version'] ) . '</p>';
		}
			echo '<p>' . sprintf(__( 'This is a child theme. The parent theme is: <strong>%1$s</strong>. These files have been included automatically!', 'theme-check'), $data[ 'Template' ] ) . '</p>';
			if ( empty( $data['Template Version'] ) ) {
				echo '<p>' . __('Child theme does not have the <strong>Template Version</strong> tag in style.css.', 'theme-check') . '</p>';
			} else {
				echo ( $data['Template Version'] < $parent_data['Version'] ) ? '<p>' . sprintf(__('Child theme is only tested up to version %1$s of %2$s breakage may occur! %3$s installed version is %4$s', 'theme-check'), $data['Template Version'], $parent_data['Title'], $parent_data['Title'], $parent_data['Version'] ) . '</p>' : '';
			}
		 }
		echo '</div><!-- .theme-info-->';

		$plugins = get_plugins( '/theme-check' );
		$version = explode( '.', $plugins['theme-check.php']['Version'] );
		echo '<p>' . sprintf(__(' Running <strong>%1$s</strong> tests against <strong>%2$s</strong> using Guidelines Version: <strong>%3$s</strong> Plugin revision: <strong>%4$s</strong>', 'theme-check'), $checkcount, $data[ 'Title' ], $version[0], $version[1] ) . '</p>';
		$results = display_themechecks();
		if ( !$success ) {
			echo '<h2>' . sprintf(__('One or more errors were found for %1$s.', 'theme-check'), $data[ 'Title' ] ) . '</h2>';
		} else {
			echo '<h2>' . sprintf(__('%1$s passed the tests', 'theme-check'), $data[ 'Title' ] ) . '</h2>';
			tc_success();
		}
		if ( !defined( 'WP_DEBUG' ) || WP_DEBUG == false ) echo '<div class="updated"><span class="tc-fail">' . __('WARNING','theme-check') . '</span> ' . __( '<strong>WP_DEBUG is not enabled!</strong> Please test your theme with <a href="http://codex.wordpress.org/Editing_wp-config.php">debug enabled</a> before you upload!', 'theme-check' ) . '</div>';
		echo '<div class="tc-box">';
		echo '<ul class="tc-result">';
		echo $results;
		echo '</ul></div>';
	}
}


function tc_intro() {
?>
	<h2><?php _e( 'About', 'theme-check' ); ?></h2>
	<p><?php _e( "The theme check plugin is an easy way to test your theme and make sure it's up to spec with the latest theme review standards. With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.", 'theme-check' ); ?></p>
	<h2><?php _e( 'Contact', 'theme-check' ); ?></h2>
	<p><?php printf( __( 'Theme-Check is maintained by %1s and %2s.', 'theme-check' ),
		'<a href="http://profiles.wordpress.org/users/pross/">Pross</a>',
		'<a href="http://profiles.wordpress.org/users/otto42/">Otto42</a>'
		); ?></p>
	<p><?php _e( 'If you have found a bug or would like to make a suggestion or contribution why not join the <a href="http://wordpress.org/extend/themes/contact/">theme-reviewers mailing list</a> or leave a post on the <a href="http://wordpress.org/tags/theme-check?forum_id=10">WordPress forums</a>.', 'theme-check' ); ?></p>
	<h2><?php _e( 'Contributors', 'theme-check' ); ?></h2>
	<h3><?php _e( 'Localization', 'theme-check' ); ?></h3>
	<ul>
	<li><a href="http://www.onedesigns.com/">Daniel Tara</a></li>
	<li><a href="http://index56.com/">Emil Uzelac</a></li>
	</ul>
	<h3><?php _e( 'Testers', 'theme-check' ); ?></h3>
	<p><a href="http://make.wordpress.org/themes/"><?php _e( 'The WordPress Theme Review Team', 'theme-check' ); ?></a></p>
	<?php
}

function tc_success() {
	?>
	<div class="tc-success"><p><?php _e( 'Now your theme has passed the basic tests you need to check it properly using the test data before you upload to the WordPress Themes Directory.', 'theme-check' ); ?></p>
	<p><?php _e( 'Make sure to review the guidelines at <a href="http://codex.wordpress.org/Theme_Review">Theme Review</a> before uploading a Theme.', 'theme-check' ); ?></p>
	<h3><?php _e( 'Codex Links', 'theme-check' ); ?></h3>
	<ul>
	<li><a href="http://codex.wordpress.org/Theme_Development"><?php _e('Theme Development', 'theme-check' ); ?></a></li>
	<li><a href="http://wordpress.org/support/forum/5"><?php _e('Themes and Templates forum', 'theme-check' ); ?></a></li>
	<li><a href="http://codex.wordpress.org/Theme_Unit_Test"><?php _e('Theme Unit Tests', 'theme-check' ); ?></a></li>
	</ul></div>
	<?php
}

function tc_form() {
	$themes = tc_get_themes();
	echo '<form action="themes.php?page=themecheck" method="post">';
	echo '<select name="themename">';
	foreach( $themes as $name => $location ) {
		echo '<option ';
		if ( isset( $_POST['themename'] ) ) {
			echo ( $location['Stylesheet'] === $_POST['themename'] ) ? 'selected="selected" ' : '';
		} else {
			echo ( basename( STYLESHEETPATH ) === $location['Stylesheet'] ) ? 'selected="selected" ' : '';
		}
		echo ( basename( STYLESHEETPATH ) === $location['Stylesheet'] ) ? 'value="' . $location['Stylesheet'] . '" style="font-weight:bold;">' . $name . '</option>' : 'value="' . $location['Stylesheet'] . '">' . $name . '</option>';
	}
	echo '</select>';
	echo '<input class="button" type="submit" value="' . __( 'Check it!', 'theme-check' ) . '" />';
	if ( defined( 'TC_PRE' ) || defined( 'TC_POST' ) ) echo ' <input name="trac" type="checkbox" /> ' . __( 'Output in Trac format.', 'theme-check' );
	echo ' <input name="s_info" type="checkbox" /> ' . __( 'Suppress INFO.', 'theme-check' );
	echo '</form>';
}
