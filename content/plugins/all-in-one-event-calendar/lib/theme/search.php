<?php

/**
 * Serach calendar themes.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Theme
 */
class Ai1ec_Theme_Search extends Ai1ec_Base {

	/**
	 * @var array Holds global variables which need to be restored.
	 */
	protected $_restore = array();

	/**
	 * Gets the currently available themes.
	 *
	 * @return array The currently available themes
	 */
	public function get_themes() {
		$this->_pre_search( $this->get_theme_dirs() );

		$options    = array(
			'errors'  => null, // null -> all
			'allowed' => null, // null -> all
		);
		$theme_map = null;
		if ( function_exists( 'wp_get_themes' ) ) {
			$theme_map = wp_get_themes( $options );
		} else {
			$theme_map = get_themes() + get_broken_themes();
		}

		add_filter(
			'theme_root_uri',
			array( $this, 'get_root_uri_for_our_themes' ),
			10,
			3
		);
		foreach ( $theme_map as $theme ) {
			$theme->get_theme_root_uri();
		}

		$this->_post_search();
		return $theme_map;
	}

	/**
	 * Sets the correct uri for our core themes.
	 *
	 * @param string $theme_root_uri
	 * @param string $site_url
	 * @param string $stylesheet_or_template
	 *
	 * @return string
	 */
	public function get_root_uri_for_our_themes(
		$theme_root_uri,
		$site_url,
		$stylesheet_or_template
	) {
		$core_themes = explode( ',', AI1EC_CORE_THEMES );
		if ( in_array( $stylesheet_or_template, $core_themes ) ) {
			return AI1EC_URL .'/public/' . AI1EC_THEME_FOLDER;
		}
		return $theme_root_uri;
	}

	/**
	 * Add core folders to scan and allow injection of other.
	 *
	 * @return array The folder to scan for themes
	 */
	public function get_theme_dirs() {
		$theme_dirs = array(
			WP_CONTENT_DIR . DIRECTORY_SEPARATOR . AI1EC_THEME_FOLDER,
			AI1EC_DEFAULT_THEME_ROOT
		);

		$theme_dirs = apply_filters( 'ai1ec_register_theme', $theme_dirs );
		$selected   = array();
		foreach ( $theme_dirs as $directory ) {
			if ( is_dir( $directory ) ) {
				$selected[] = $directory;
			}
		}
		return $selected;
	}

	/**
	 * Replacecs global variables.
	 *
	 * @param array $variables_map
	 *
	 * @return array
	 */
	protected function _replace_search_globals( array $variables_map ) {
		foreach ( $variables_map as $key => $current_value ) {
			global $$key;
			$variables_map[$key] = $$key;
			$$key                = $current_value;
		}
		search_theme_directories( true );
		return $variables_map;
	}

	/**
	 * Set some globals to allow theme searching.
	 *
	 * @param array $directories
	 */
	protected function _pre_search( array $directories ) {
		$this->_restore = $this->_replace_search_globals(
			array(
				'wp_theme_directories' => $directories,
				'wp_broken_themes'     => array(),
			)
		);
		add_filter(
			'wp_cache_themes_persistently',
			'__return_false',
			1
		);
	}

	/**
	 * Reset globals and filters post scan.
	 */
	protected function _post_search() {
		remove_filter(
			'wp_cache_themes_persistently',
			'__return_false',
			1
		);
		$this->_replace_search_globals( $this->_restore );
	}

	/**
	 * Filter the current themes by search.
	 *
	 * @param array $terms
	 * @param array $features
	 * @param bool $broken
	 *
	 * @return array
	 */
	public function filter_themes(
		array $terms    = array(),
		array $features = array(),
		$broken         = false
	) {
		static $theme_list = null;
		if ( null === $theme_list ) {
			$theme_list = $this->get_themes();
		}

		foreach ( $theme_list as $key => $theme ) {
			if (
				( ! $broken && false !== $theme->errors() ) ||
				! $this->theme_matches( $theme, $terms, $features )
			) {
				unset( $theme_list[$key] );
				continue;
			}
		}

		return $theme_list;
	}

	/**
	 * Returns if the $theme is a match for the search.
	 *
	 * @param WP_Theme $theme
	 * @param array $search
	 * @param array $features
	 *
	 * @return boolean
	 */
	public function theme_matches( $theme, array $search, array $features ) {
		static $fields = array(
			'Name',
			'Title',
			'Description',
			'Author',
			'Template',
			'Stylesheet',
		);

		$tags = array_map(
			'sanitize_title_with_dashes',
			$theme['Tags']
		);

		// Match all phrases
		if ( count( $search ) > 0 ) {
			foreach ( $search as $word ) {

				// In a tag?
				if ( ! in_array( $word, $tags ) ) {
					return false;
				}

				// In one of the fields?
				foreach ( $fields as $field ) {
					if ( false === stripos( $theme->get( $field ), $word ) ) {
						return false;
					}
				}

			}
		}

		// Now search the features
		if ( count( $features ) > 0 ) {
			foreach ( $features as $word ) {
				// In a tag?
				if ( ! in_array( $word, $tags ) ) {
					return false;
				}
			}
		}

		// Only get here if each word exists in the tags or one of the fields
		return true;
	}

	/**
	 * Move passed themes to backup folder.
	 *
	 * @param array $themes
	 */
	public function move_themes_to_backup( array $themes ) {
		global $wp_filesystem;
		$root = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . AI1EC_THEME_FOLDER . DIRECTORY_SEPARATOR;
		$backup = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . AI1EC_THEME_FOLDER . '-obsolete' . DIRECTORY_SEPARATOR;
		// this will also set $wp_filesystem global
		$writable = $this->_registry->get( 'filesystem.checker')->is_writable( WP_CONTENT_DIR );
		// this also means the access is 'direct'
		$backup_dir_exists = false;

		$errors = array();
		if ( true === $writable ) {
			if ( ! $wp_filesystem->is_dir( $backup ) ) {
				$backup_dir_exists = $wp_filesystem->mkdir( $backup );
			} else {
				$backup_dir_exists = true;
			}
		} else {
			$message = __(
				'Unable to move your old core themes from <code>wp-content/themes-ai1ec</code> to <code>wp-content/themes-ai1ec-obsolete</code> because your <code>wp-content</code> folder is not writable. Please manually remove your old core themes from <code>wp-content/themes-ai1ec</code>.',
				AI1EC_PLUGIN_NAME
			);
			$errors[] = $message;
		}
		if ( true === $backup_dir_exists ) {
			foreach ( $themes as $theme_dir ) {
				if ( $wp_filesystem->is_dir( $root . $theme_dir ) ) {
					$result = $wp_filesystem->move( $root . $theme_dir, $backup . $theme_dir );
					if ( false === $result ) {
						$message = __(
							'Failed to move your old core themes from <code>wp-content/themes-ai1ec/%s</code> to <code>wp-content/themes-ai1ec-obsolete/%s</code>. Please manually remove your old core themes from <code>wp-content/themes-ai1ec/%s</code>.',
							AI1EC_PLUGIN_NAME
						);
						$errors[] = sprintf( $message, $theme_dir, $theme_dir, $theme_dir );
					}
				}
			}
		}
		if ( ! empty( $errors ) ) {
			$notification = $this->_registry->get( 'notification.admin' );
			$notification->store(
				implode( '<br/>', $errors ),
				'error',
				2,
				array( Ai1ec_Notification_Admin::RCPT_ALL ),
				true
			);
		}
	}
}
