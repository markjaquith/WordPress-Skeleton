<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Helps in excluding MediaPress comments  from the normal UI/Flow of WordPress comments
 * 
 */
class MPP_Comments_Exclusion_Helper {

	private static $instance = null;
	
	private $comment_type;
	
	private function __construct() {
		
		$this->comment_type = mpp_get_comment_type();
		// Hide in comment listing
		add_filter( 'comments_clauses', array( $this, 'exclude_comments' ), 10, 2 );
		add_filter( 'comment_feed_where', array( $this, 'exclude_from_feeds' ), 10, 2 );

		// Update count comments
		add_filter( 'wp_count_comments', array( $this, 'filter_count_comments' ), 10, 2 );
	}

	/**
	 * 
	 * @return MPP_Comments_Exclusion_Helper
	 */
	public static function get_instance() {
		
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
		
	}

	public function exclude_comments( $clauses, $wp_comment_query ) {
		global $wpdb;

		$clauses['where'] .= $wpdb->prepare( ' AND comment_type != %s', $this->comment_type );
		return $clauses;
	}


	
	public function exclude_from_feeds( $where, $wp_comment_query ) {
		global $wpdb;

		$where .= $wpdb->prepare( " AND comment_type != %s", $this->comment_type );
		return $where;
	}

	

	/**
	 * Remove order notes from wp_count_comments()
	 *
	 * @see wp_count_comments()
	 */
	public  function filter_count_comments( $stats, $post_id ) {
		
		//we don't have to worry about it when the post id is given
		if ( ! empty( $post_id ) ) {
			return $stats;
		}
		global $wpdb;
		
		$count = wp_cache_get("comments-{$post_id}", 'counts');

		if ( false !== $count ) {
			return $count;
		}
		
		$where = $wpdb->prepare( "WHERE comment_type != %s", $this->comment_type );
		
		$count = $wpdb->get_results( "SELECT comment_approved, COUNT( * ) AS num_comments FROM {$wpdb->comments} {$where} GROUP BY comment_approved", ARRAY_A );

		$total = 0;
		$approved = array('0' => 'moderated', '1' => 'approved', 'spam' => 'spam', 'trash' => 'trash', 'post-trashed' => 'post-trashed');
		foreach ( (array) $count as $row ) {
			// Don't count post-trashed toward totals
			if ( 'post-trashed' != $row['comment_approved'] && 'trash' != $row['comment_approved'] )
				$total += $row['num_comments'];
			if ( isset( $approved[$row['comment_approved']] ) )
				$stats[$approved[$row['comment_approved']]] = $row['num_comments'];
		}

		$stats['total_comments'] = $total;
		foreach ( $approved as $key ) {
			if ( empty($stats[$key]) )
				$stats[$key] = 0;
		}

		$stats = (object) $stats;
		wp_cache_set("comments-{$post_id}", $stats, 'counts');

		return $stats;
	}
	
	

}

MPP_Comments_Exclusion_Helper::get_instance();
