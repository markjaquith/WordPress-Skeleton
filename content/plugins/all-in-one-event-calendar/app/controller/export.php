<?php

class Ai1ec_Export_Controller extends Ai1ec_Base {

	/**
	 * export_location function
	 *
	 * @param array $data
	 * @param bool $update
	 *
	 * @return void
	 **/
	public function export_location( $data, $update = false ) {
		// if there is no data to send, return
		if (
			empty( $data['venue'] ) &&
			empty( $data['country'] ) &&
			empty( $data['address'] ) &&
			empty( $data['city'] ) &&
			empty( $data['province'] ) &&
			empty( $data['postal_code'] ) &&
			empty( $data['latitude'] ) &&
			empty( $data['longitude'] )
		) {
			return;
		}

		// For this remote call we need to remove cUrl, because the minimum timeout
		// of cUrl is 1 second. This causes Facebook import to fail when importing
		// many events (even from just a few friends). A timeout greater than 0.05s
		// will be a great hindrance to performance.
		add_filter( 'use_curl_transport', array( $this, 'remove_curl' ) );

		// Send data using post to locations API.
		wp_remote_post( AI1EC_LOCATIONS_API,
			array(
				'body' => array(
					'venue'       => $data['venue'],
					'country'     => $data['country'],
					'address'     => $data['address'],
					'city'        => $data['city'],
					'province'    => $data['province'],
					'postal_code' => $data['postal_code'],
					'latitude'    => $data['latitude'],
					'longitude'   => $data['longitude'],
					'update'      => $update,
				),
				'timeout'  => 0.01,
				'blocking' => false,
			)
		);

		// Revert cUrl setting to what it was.
		remove_filter( 'use_curl_transport', array( $this, 'remove_curl' ) );
	}

	/**
	 * Simple function that returns false, intended for the use_curl_transport
	 * filter to disable the use of cUrl.
	 *
	 * @return boolean
	 */
	public function remove_curl() {
		return false;
	}

	/**
	 * Statistics collection cron.
	 *
	 * @return void
	 */
	public function n_cron() {
		$xguard = $this->_registry->get( 'compatibility.xguard' );
		$guard_name = 'n_cron';

		// Acquire Cron
		if ( ! $xguard->acquire( $guard_name ) ) {
			return null;
		}
		$dbi            = $this->_registry->get( 'dbi.dbi' );
		$ai1ec_settings = $this->_registry->get( 'model.settings' );

		$query = 'SELECT COUNT( ID ) as num_events
			FROM ' . $dbi->get_table_name( 'posts' ) . '
			WHERE post_type   = \'' . AI1EC_POST_TYPE . '\'
			  AND post_status = \'publish\'';
		$n_events = $dbi->get_var( $query );

		$query   = 'SELECT COUNT( ID ) FROM ' . $dbi->get_table_name( 'users' );
		$n_users = $dbi->get_var( $query );

		$categories = $tags = array();
		$term_list  = get_terms(
			'events_categories',
			array( 'hide_empty' => false )
		);
		foreach ( $term_list as $term ) {
			if ( isset( $term->name ) ) {
				$categories[] = $term->name;
			}
		}
		$term_list  = get_terms(
			'events_tags',
			array( 'hide_empty' => false )
		);
		foreach ( $term_list as $term ) {
			if ( isset( $term->name ) ) {
				$tags[] = $term->name;
			}
		}
		$fs_method  = 'UNKNOWN';
		global $wp_filesystem;
		if ( is_object( $wp_filesystem ) && isset( $wp_filesystem->method ) ) {
			$fs_method = $wp_filesystem->method;
		}
		$options    = $this->_registry->get( 'model.option' );
		$data       = array(
			'n_users'        => $n_users,
			'n_events'       => $n_events,
			'categories'     => $categories,
			'tags'           => $tags,
			'blog_name'      => get_bloginfo( 'name' ),
			'cal_url'        => get_permalink(
				$ai1ec_settings->get( 'calendar_page_id' )
			),
			'ics_url'        => AI1EC_EXPORT_URL,
			'php_version'    => phpversion(),
			'mysql_version'  => $this->_registry->get( 'dbi.dbi' )
				->db_version(),
			'wp_version'     => get_bloginfo( 'version' ),
			'wp_lang'        => get_bloginfo( 'language' ),
			'wp_url'         => home_url(),
			'timezone'       => $this->_registry->get( 'date.timezone' )
				->get_default_timezone(),
			'privacy'        => $options->get( 'blog_public' ),
			'plugin_version' => AI1EC_VERSION,
			'addons'         => $this->_get_addons(),
			'wp_filesystem'  => $fs_method,
			'wp_debug'       => WP_DEBUG,
			'ai1ec_debug'    => AI1EC_DEBUG,
			'active_theme'   => $options->get(
				'ai1ec_template',
				AI1EC_DEFAULT_THEME_NAME
			),
		);
		// send request
		wp_remote_post(
			AI1EC_STATS_API,
			array(
				'body' => $data,
			)
		);

		// Release lock
		$xguard->release( $guard_name );
	}

	/**
	 * Return a map of add-ons installed on site.
	 *
	 * Map contains following keys for each entry:
	 *     - v - version of add-on;
	 *     - u - plugin URI (if non-official add-on is used);
	 *     - a - bool flag to indicate if plugin is active.
	 *
	 * @return array Map of add-ons.
	 */
	protected function _get_addons() {
		$addons = array();
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();
		foreach ( $all_plugins as $plugin => $options ) {
			$addons[$plugin] = array(
				'v' => $options['Version'],
				'u' => $options['PluginURI'],
				'a' => is_plugin_active( $plugin ),
			);
		}
		return $addons;
	}

	/**
	 * Send stats if user agrees by clicking on the optin pointer
	 */
	public function track_optin() {
		$send_stats = $_POST['tracking'] === 'true';
		$settings = $this->_registry->get( 'model.settings' );
		$settings->set( 'allow_statistics', $send_stats );
		$settings->set( 'show_tracking_popup', false );
	}
}