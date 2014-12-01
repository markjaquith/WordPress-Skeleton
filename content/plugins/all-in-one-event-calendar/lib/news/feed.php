<?php

/**
 * RSS feed importer.
 *
 * @author     Time.ly Network Inc.
 * @since      2.1
 *
 * @package    AI1EC
 * @subpackage AI1EC.News
 */
class Ai1ec_News_Feed {

	/**
	 * Import RSS feed.
	 *
	 * @param int    $limit Number of entries to fetch.
	 * @param string $feed  URI of RSS feed to import.
	 *
	 * @return array RSS feed entries.
	 */
	public function import_feed( $limit = 5, $feed = AI1EC_RSS_FEED ) {
		include_once(
			ABSPATH . WPINC . DIRECTORY_SEPARATOR . 'class-simplepie.php'
		);
		$cache  = $this->get_transient_name( $feed );
		$result = get_transient( $cache );
		if ( false === $result || AI1EC_DEBUG ) {
			$result     = $this->fetch_feed( $limit, $feed );
			$expiration = $this->get_expiration( $feed );
			set_transient( $cache, $result, $expiration );
		}
		return $result;
	}

	/**
	 * Fetch feed w/o caching.
	 *
	 * @param int    $limit Number of entries to fetch.
	 * @param string $feed  URI of RSS feed to import.
	 *
	 * @return array List of feed entries.
	 */
	public function fetch_feed( $limit, $feed ) {
		if ( ! class_exists( 'SimplePie_File' ) ) {
			include_once(
				ABSPATH . WPINC . DIRECTORY_SEPARATOR .'SimplePie'
				. DIRECTORY_SEPARATOR .'File.php'
			);
		}
		$result = array();
		try {
			$file = new SimplePie_File( $feed );
			$feed = new SimplePie();
			$feed->set_raw_data( $file->body );
			$feed->init();
			if ( ! is_wp_error( $feed ) ) {
				$result = $feed->get_items( 0, $limit );
			}
		} catch ( Exception $exception ) {} // discard
		return $result;
	}

	/**
	 * Get name to be used for transient.
	 *
	 * @param string $feed URI of feed to get transien name for.
	 *
	 * @return string Transient name to use.
	 */
	public function get_transient_name( $feed ) {
		return ( AI1EC_RSS_FEED === $feed )
			? 'ai1ec_rss_feed' :
			'ai1ec_rss_' . substr( md5( $feed ), 5, 8 );
	}

	/**
	 * Get expiration time (in seconds) for feed.
	 *
	 * @param string $feed URI of feed to get transien name for.
	 *
	 * @return int Number of seconds to keep cached feed output.
	 */
	public function get_expiration( $feed ) {
		$expiration = apply_filters(
			'wp_feed_cache_transient_lifetime',
			12 * HOUR_IN_SECONDS,
			$feed
		);
		return $expiration;
	}

}