<?php

/**
 * Custom Post type class.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Post
 */
class Ai1ec_Post_Custom_Type extends Ai1ec_Base {

	/**
	 * Registers the custom post type.
	 *
	 * @wp-hook init
	 */
	public function register() {
		$settings = $this->_registry->get( 'model.settings' );

		// ===============================
		// = labels for custom post type =
		// ===============================
		$labels = array(
			'name'               => Ai1ec_I18n::_x( 'Events', 'Custom post type name' ),
			'singular_name'      => Ai1ec_I18n::_x( 'Event', 'Custom post type name (singular)' ),
			'add_new'            => Ai1ec_I18n::__( 'Add New' ),
			'add_new_item'       => Ai1ec_I18n::__( 'Add New Event' ),
			'edit_item'          => Ai1ec_I18n::__( 'Edit Event' ),
			'new_item'           => Ai1ec_I18n::__( 'New Event' ),
			'view_item'          => Ai1ec_I18n::__( 'View Event' ),
			'search_items'       => Ai1ec_I18n::__( 'Search Events' ),
			'not_found'          => Ai1ec_I18n::__( 'No Events found' ),
			'not_found_in_trash' => Ai1ec_I18n::__( 'No Events found in Trash' ),
			'parent_item_colon'  => Ai1ec_I18n::__( 'Parent Event' ),
			'menu_name'          => Ai1ec_I18n::__( 'Events' ),
			'all_items'          => $this->get_all_items_name(),
		);


		// ================================
		// = support for custom post type =
		// ================================
		$supports = array( 'title', 'editor', 'comments', 'custom-fields', 'thumbnail', 'author' );

		// =============================
		// = args for custom post type =
		// =============================
		$page_base = false;
		if ( $settings->get( 'calendar_page_id' ) ) {
			$page_base = get_page_uri( $settings->get( 'calendar_page_id' ) );
		}

		$rewrite     = array( 'slug' => Ai1ec_I18n::__( 'event' ) );
		$has_archive = true;
		if (
			$settings->get( 'calendar_base_url_for_permalinks' ) &&
			$page_base
		) {
			$rewrite     =  array( 'slug' => $page_base );
			$has_archive = AI1EC_ALTERNATIVE_ARCHIVE_URL;
		}
		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => $rewrite,
			'map_meta_cap'        => true,
			'capability_type'     => 'ai1ec_event',
			'has_archive'         => $has_archive,
			'hierarchical'        => false,
			'menu_position'       => 5,
			'supports'            => $supports,
			'exclude_from_search' => $settings->get( 'exclude_from_search' ),
		);

		// ========================================
		// = labels for event categories taxonomy =
		// ========================================
		$events_categories_labels = array(
			'name'          => Ai1ec_I18n::_x( 'Event Categories', 'Event categories taxonomy' ),
			'singular_name' => Ai1ec_I18n::_x( 'Event Category', 'Event categories taxonomy (singular)' )
		);

		// ==================================
		// = labels for event tags taxonomy =
		// ==================================
		$events_tags_labels = array(
			'name'          => Ai1ec_I18n::_x( 'Event Tags', 'Event tags taxonomy' ),
			'singular_name' => Ai1ec_I18n::_x( 'Event Tag', 'Event tags taxonomy (singular)' )
		);

		// ==================================
		// = labels for event feeds taxonomy =
		// ==================================
		$events_feeds_labels = array(
			'name'          => Ai1ec_I18n::_x( 'Event Feeds', 'Event feeds taxonomy' ),
			'singular_name' => Ai1ec_I18n::_x( 'Event Feed', 'Event feed taxonomy (singular)' )
		);

		// ======================================
		// = args for event categories taxonomy =
		// ======================================
		$events_categories_args = array(
			'labels'       => $events_categories_labels,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => 'events_categories' ),
			'capabilities' => array(
				'manage_terms' => 'manage_events_categories',
				'edit_terms'   => 'manage_events_categories',
				'delete_terms' => 'manage_events_categories',
				'assign_terms' => 'edit_ai1ec_events'
			)
		);

		// ================================
		// = args for event tags taxonomy =
		// ================================
		$events_tags_args = array(
			'labels'       => $events_tags_labels,
			'hierarchical' => false,
			'rewrite'      => array( 'slug' => 'events_tags' ),
			'capabilities' => array(
				'manage_terms' => 'manage_events_categories',
				'edit_terms'   => 'manage_events_categories',
				'delete_terms' => 'manage_events_categories',
				'assign_terms' => 'edit_ai1ec_events'
			)
		);

		// ================================
		// = args for event feeds taxonomy =
		// ================================
		$events_feeds_args = array(
			'labels'       => $events_feeds_labels,
			'hierarchical' => false,
			'rewrite'      => array( 'slug' => 'events_feeds' ),
			'capabilities' => array(
				'manage_terms' => 'manage_events_categories',
				'edit_terms'   => 'manage_events_categories',
				'delete_terms' => 'manage_events_categories',
				'assign_terms' => 'edit_ai1ec_events'
			),
			'public'        => false // don't show taxonomy in admin UI
		);

		// ======================================
		// = register event categories taxonomy =
		// ======================================
		register_taxonomy(
			'events_categories',
			array( AI1EC_POST_TYPE ),
			$events_categories_args
		);

		// ================================
		// = register event tags taxonomy =
		// ================================
		register_taxonomy(
			'events_tags',
			array( AI1EC_POST_TYPE ),
			$events_tags_args
		);

		// ================================
		// = register event tags taxonomy =
		// ================================
		register_taxonomy(
			'events_feeds',
			array( AI1EC_POST_TYPE ),
			$events_feeds_args
		);

		// ========================================
		// = register custom post type for events =
		// ========================================
		register_post_type( AI1EC_POST_TYPE, $args );

		// get event contributor if saved in the db
		$contributor = get_role( 'ai1ec_event_assistant' );
		// if it's present and has the wrong capability delete it.
		if (
			$contributor instanceOf WP_Role && 
			$contributor->has_cap( 'publish_ai1ec_events' )
		) {
			remove_role( 'ai1ec_event_assistant' );
			$contributor = false;
		}
		// Create event contributor role with the same capabilities
		// as subscriber role, plus event managing capabilities
		// if we have not created it yet.
		if ( ! $contributor ) {
			$caps = get_role( 'subscriber' )->capabilities;
			$role = add_role(
				'ai1ec_event_assistant',
				'Event Contributor',
				$caps
			);
			$role->add_cap( 'edit_ai1ec_events' );
			$role->add_cap( 'read_ai1ec_events' );
			$role->add_cap( 'delete_ai1ec_events' );
			$role->add_cap( 'read' );
			unset( $caps, $role );
		}

		// Add event managing capabilities to administrator, editor, author.
		// The last created capability is "manage_ai1ec_feeds", so check for
		// that one.
		$role = get_role( 'administrator' );
		if ( is_object( $role ) && ! $role->has_cap( 'manage_ai1ec_feeds' ) ) {
			$role_list = array( 'administrator', 'editor', 'author' );
			foreach ( $role_list as $role_name ) {
				$role = get_role( $role_name );
				if ( null === $role || ! ( $role instanceof WP_Role ) ) {
					continue;
				}
				// Read events.
				$role->add_cap( 'read_ai1ec_event' );
				// Edit events.
				$role->add_cap( 'edit_ai1ec_event' );
				$role->add_cap( 'edit_ai1ec_events' );
				$role->add_cap( 'edit_others_ai1ec_events' );
				$role->add_cap( 'edit_private_ai1ec_events' );
				$role->add_cap( 'edit_published_ai1ec_events' );
				// Delete events.
				$role->add_cap( 'delete_ai1ec_event' );
				$role->add_cap( 'delete_ai1ec_events' );
				$role->add_cap( 'delete_others_ai1ec_events' );
				$role->add_cap( 'delete_published_ai1ec_events' );
				$role->add_cap( 'delete_private_ai1ec_events' );
				// Publish events.
				$role->add_cap( 'publish_ai1ec_events' );
				// Read private events.
				$role->add_cap( 'read_private_ai1ec_events' );
				// Manage categories & tags.
				$role->add_cap( 'manage_events_categories' );
				// Manage calendar feeds.
				$role->add_cap( 'manage_ai1ec_feeds' );
				if ( 'administrator' === $role_name ) {
					// Change calendar themes & manage calendar options.
					$role->add_cap( 'switch_ai1ec_themes' );
					$role->add_cap( 'manage_ai1ec_options' );
				}
			}
		}

	}

	/**
	 * Appending pending items number to the menu name.
	 *
	 * If current user can publish events and there
	 * is at least 1 event pending, append the pending
	 * events number to the menu
	 *
	 * @return string
	 */
	public function get_all_items_name() {
		// if current user can publish events
		if ( current_user_can( 'publish_ai1ec_events' ) ) {
			// get all pending events
			$query = array (
				'post_type'      => AI1EC_POST_TYPE,
				'post_status'    => 'pending',
				'posts_per_page' => -1,
			);
			$query = new WP_Query( $query );

			// at least 1 pending event?
			if ( $query->post_count > 0 ) {
				// append the pending events number to the menu
				return sprintf(
					Ai1ec_I18n::__(
						'All Events <span class="update-plugins count-%d" title="%d Pending Events"><span class="update-count">%d</span></span>'
					),
					$query->post_count,
					$query->post_count,
					$query->post_count
				);
			}
		}

		// no pending events, or the user doesn't have sufficient capabilities
		return Ai1ec_I18n::__( 'All Events' );
	}

}