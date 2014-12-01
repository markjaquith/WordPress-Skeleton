<?php

/**
 * Model used for storing/retrieving taxonomy.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Html
 */
class Ai1ec_Taxonomy extends Ai1ec_Base {

	/**
	 * @var array Map of taxonomy values.
	 */
	protected $_taxonomy_map = array(
		'events_categories' => array(),
		'events_tags' => array(),
	);

	/**
	 * Callback to pre-populate taxonomies before exporting ics.
	 * All taxonomies which are not tags are exported as event_categories
	 *
	 * @param array $post_ids List of Post IDs to inspect.
	 *
	 * @return void
	 */
	public function prepare_meta_for_ics( array $post_ids ) {
		$taxonomies = get_object_taxonomies( AI1EC_POST_TYPE );
		$categories = array();
		$excluded_categories = array(
			'events_tags'  => true,
			'events_feeds' => true
		);
		foreach ( $taxonomies as $taxonomy ) {
			if ( isset( $excluded_categories[$taxonomy] ) ) {
				continue;
			}
			$categories[] = $taxonomy;
		}
		foreach ( $post_ids as $post_id ) {
			$post_id = (int)$post_id;
			$this->_taxonomy_map['events_categories'][$post_id] = array();
			$this->_taxonomy_map['events_tags'][$post_id] = array();
		}
		$tags = wp_get_object_terms(
			$post_ids,
			array( 'events_tags' ),
			array( 'fields' => 'all_with_object_id' )
		);
		foreach ( $tags as $term ) {
			$this->_taxonomy_map[$term->taxonomy][$term->object_id][] = $term;
		}
		$category_terms = wp_get_object_terms(
			$post_ids,
			$categories,
			array( 'fields' => 'all_with_object_id' )
		);
		foreach ( $category_terms as $term ) {
			$this->_taxonomy_map['events_categories'][$term->object_id][] = $term;
		}
	}

	/**
	 * Callback to pre-populate taxonomies before processing.
	 *
	 * @param array $post_ids List of Post IDs to inspect.
	 *
	 * @return void
	 */
	public function update_meta( array $post_ids ) {
		foreach ( $post_ids as $post_id ) {
			$post_id = (int)$post_id;
			$this->_taxonomy_map['events_categories'][$post_id] = array();
			$this->_taxonomy_map['events_tags'][$post_id] = array();
		}
		$terms = wp_get_object_terms(
			$post_ids,
			array( 'events_categories', 'events_tags' ),
			array( 'fields' => 'all_with_object_id' )
		);
		foreach ( $terms as $term ) {
			$this->_taxonomy_map[$term->taxonomy][$term->object_id][] = $term;
		}
	}

	/**
	 * Re-fetch category entries map from database.
	 *
	 * @return array Map of category entries.
	 */
	public function fetch_category_map() {
		$category_map = array();
		$records      = (array)$this->_registry->get( 'dbi.dbi' )->select(
			'ai1ec_event_category_meta',
			array( 'term_id', 'term_image', 'term_color' )
		);
		foreach ( $records as $row ) {
			$image = $color = null;
			if ( $row->term_image ) {
				$image = $row->term_image;
			}
			if ( $row->term_color ) {
				$color = $row->term_color;
			}
			$category_map[(int)$row->term_id] = compact( 'image', 'color' );
		}
		return $category_map;
	}

	/**
	 * Get taxonomy values for specified post.
	 *
	 * @param int    $post_id  Actual Post ID to check.
	 * @param string $taxonomy Name of taxonomy to retrieve values for.
	 *
	 * @return array List of terms (stdClass'es) associated with post.
	 */
	public function get_post_taxonomy( $post_id, $taxonomy ) {
		$post_id = (int)$post_id;
		if ( ! isset( $this->_taxonomy_map[$taxonomy][$post_id] ) ) {
			$definition = wp_get_post_terms( $post_id, $taxonomy );
			if ( empty( $definition ) ) {
				$definition = array();
			}
			$this->_taxonomy_map[$taxonomy][$post_id] = $definition;
		}
		return $this->_taxonomy_map[$taxonomy][$post_id];
	}

	/**
	 * Get post (event) categories taxonomy.
	 *
	 * @param int $post_id Checked post ID.
	 *
	 * @return array List of categories (stdClass'es) associated with event.
	 */
	public function get_post_categories( $post_id ) {
		return $this->get_post_taxonomy( $post_id, 'events_categories' );
	}

	/**
	 * Get post (event) tags taxonomy.
	 *
	 * @param int $post_id Checked post ID.
	 *
	 * @return array List of tags (stdClass'es) associated with event.
	 */
	public function get_post_tags( $post_id ) {
		return $this->get_post_taxonomy( $post_id, 'events_tags' );
	}

	/**
	 * Get cached category description field.
	 *
	 * @param int    $term_id Category ID.
	 * @param string $field   Name of field, one of 'image', 'color'.
	 *
	 * @return string|null Field value or null if entry is not found.
	 */
	public function get_category_field( $term_id, $field ) {
		static $category_meta = null;
		if ( null === $category_meta ) {
			$category_meta = $this->fetch_category_map();
		}
		$term_id = (int)$term_id;
		if ( ! isset( $category_meta[$term_id] ) ) {
			return null;
		}
		return $category_meta[$term_id][$field];
	}
	
	/**
	 * Returns the color of the Event Category having the given term ID.
	 *
	 * @param int $term_id The ID of the Event Category.
	 *
	 * @return string|null Color to use
	 */
	public function get_category_color( $term_id ) {
		return $this->get_category_field( $term_id, 'color' );
	}

	/**
	 * Returns the image of the Event Category having the given term ID.
	 *
	 * @param int $term_id The ID of the Event Category.
	 *
	 * @return string|null Image url to use.
	 */
	public function get_category_image( $term_id ) {
		return $this->get_category_field( $term_id, 'image' );
	}

}