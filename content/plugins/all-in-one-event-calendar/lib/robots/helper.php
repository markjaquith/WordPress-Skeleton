<?php

/**
 * File robots.txt helper.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.query
 */
class Ai1ec_Robots_Helper extends Ai1ec_Base {

	/**
	 * Install robotx.txt into current Wordpress instance
	 *
	 * @return void
	 */
	public function install() {
		$option   = $this->_registry->get( 'model.option' );
		$settings = $this->_registry->get( 'model.settings' );
		$robots   = $option->get( 'ai1ec_robots_txt' );
		if ( isset( $robots['page_id'] ) &&
					$robots['page_id'] == $settings->get( 'calendar_page_id' ) ) {
			return;
		}

		$ftp_base_dir  = defined( 'FTP_BASE' ) ? ( FTP_BASE . DIRECTORY_SEPARATOR ) : '';
		// we can't use ABSPATH for ftp, if ftp user is not chrooted they need
		// to define FTP_BASE in wp-config.php
		$robots_file   = $ftp_base_dir . 'robots.txt';
		$robots_txt    = array();
		$is_installed  = false;
		$current_rules = null;
		$custom_rules  = $this->rules( '', false );

		$url = wp_nonce_url(
			'edit.php?post_type=ai1ec_event&page=all-in-one-event-calendar-settings',
			'ai1ec-nonce'
		);

		$redirect_url = admin_url(
			'edit.php?post_type=ai1ec_event&page=all-in-one-event-calendar-settings&noredirect=1'
		);

		if ( ! function_exists( 'request_filesystem_credentials' )  ) {
			return;
		}
		$type = get_filesystem_method();
		if ( 'direct' === $type ) {
			// we have to use ABSPATH for direct
			$robots_file = ABSPATH . 'robots.txt';
		}

		$creds = request_filesystem_credentials( $url, $type, false, false, null );

		if ( ! WP_Filesystem( $creds ) ) {
			$error_v = (
				isset( $_POST['hostname'] ) ||
				isset( $_POST['username'] ) ||
				isset( $_POST['password'] ) ||
				isset( $_POST['connection_type'] )
			);
			if ( $error_v ) {
				// if credentials are given and we don't have access to
				// wp filesystem show notice to user
				// we could use request_filesystem_credentials with true error
				// parameter but in this case second ftp credentials screen
				// would appear
				$notification = $this->_registry->get( 'notification.admin' );
				$err_msg = Ai1ec_I18n::__(
					'<strong>ERROR:</strong> There was an error connecting to the server, Please verify the settings are correct.'
				);
				$notification->store( $err_msg, 'error', 1 );
				// we need to avoid infinity loop if FS_METHOD direct
				// and robots.txt is not writable
				if ( ! isset( $_REQUEST['noredirect'] ) ) {
					Ai1ec_Http_Response_Helper::redirect( $redirect_url );
				}
			}
			return;
		}

		global $wp_filesystem;
		// sometimes $wp_filesystem could be null
		if ( null === $wp_filesystem ) {
			return;
		}
		$redirect = false;
		if ( $wp_filesystem->exists( $robots_file )
				&& $wp_filesystem->is_readable( $robots_file )
					&& $wp_filesystem->is_writable( $robots_file ) ) {
			// Get current robots txt content
			$current_rules = $wp_filesystem->get_contents( $robots_file );

			// Update robots.txt
			$custom_rules = $this->rules( $current_rules, false );
		} 
		$robots_txt['is_installed'] = $wp_filesystem->put_contents(
			$robots_file,
			$custom_rules,
			FS_CHMOD_FILE
		);
		if ( false === $robots_txt['is_installed'] ) {
			$err_msg = Ai1ec_I18n::__(
				'<strong>ERROR:</strong> There was an error storing <strong>robots.txt</strong> to the server, the file could not be written.'
			);
			$this->_registry->get( 'notification.admin' )
				->store( $err_msg, 'error' );
			$redirect = true;
		}
		// Set Page ID
		$robots_txt['page_id'] = $settings->get( 'calendar_page_id' );

		// Update Robots Txt
		$option->set( 'ai1ec_robots_txt', $robots_txt );

		// Update settings textarea
		$settings->set( 'edit_robots_txt', $custom_rules );

		// we need to avoid infinity loop if FS_METHOD direct
		// and robots.txt is not writable
		if ( $redirect && ! isset( $_REQUEST['noredirect'] ) ) {
			Ai1ec_Http_Response_Helper::redirect( $redirect_url );
		}
	}

	/**
	 * Get default robots rules for the calendar
	 *
	 * @param  string $output Current robots rules
	 * @param  string $public Public flag
	 * @return array
	 */
	public function rules( $output = '', $public ) {
		// Current rules
		$current_rules = array_map(
			'trim',
			explode( PHP_EOL, $output )
		);

		// Get calendar page URI
		$calendar_page_id = $this->_registry->get( 'model.settings' )
											->get( 'calendar_page_id' );
		$page_base = get_page_uri( $calendar_page_id );

		// Custom rules
		$custom_rules = array();
		if ( $page_base ) {
			$custom_rules += array(
				"User-agent: *",
				"Disallow: /$page_base/action~posterboard/",
				"Disallow: /$page_base/action~agenda/",
				"Disallow: /$page_base/action~oneday/",
				"Disallow: /$page_base/action~month/",
				"Disallow: /$page_base/action~week/",
				"Disallow: /$page_base/action~stream/",
			);
		}

		$robots = array_merge( $current_rules, $custom_rules );
		$robots = implode(
			PHP_EOL,
			array_filter( array_unique( $robots ) )
		);
		return $robots;
	}
}
