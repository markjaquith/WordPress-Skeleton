<?php

class Ai1ec_View_Admin_All_Events extends Ai1ec_Base {
	
	/**
	 * change_columns function
	 *
	 * Adds Event date/time column to our custom post type
	 * and renames Date column to Post Date
	 *
	 * @param array $columns Existing columns
	 *
	 * @return array Updated columns array
	 */
	public function change_columns( array $columns = array() ) {
		$columns['author']           = __( 'Author',          AI1EC_PLUGIN_NAME );
		$columns['date']             = __( 'Post Date',       AI1EC_PLUGIN_NAME );
		$columns['ai1ec_event_date'] = __( 'Event date/time', AI1EC_PLUGIN_NAME );
		return $columns;
	}

	/**
	 * orderby function
	 *
	 * Orders events by event date
	 *
	 * @param string $orderby Orderby sql
	 * @param object $wp_query
	 *
	 * @return void
	 **/
	public function orderby( $orderby, $wp_query ) {
		
		$db = $this->_registry->get( 'dbi.dbi' );
		$aco = $this->_registry->get( 'acl.aco' );
	
		if( true === $aco->is_all_events_page() ) {
			$wp_query->query = wp_parse_args( $wp_query->query );
			$table_name = $db->get_table_name( 'ai1ec_events' );
			$posts = $db->get_table_name( 'posts' );
			if( isset( $wp_query->query['orderby'] ) && 'ai1ec_event_date' === @$wp_query->query['orderby'] ) {
				$orderby = "(SELECT start FROM {$table_name} WHERE post_id = {$posts}.ID) " . $wp_query->get('order');
			} else if( empty( $wp_query->query['orderby'] ) || $wp_query->query['orderby'] === 'menu_order title' ) {
				$orderby = "(SELECT start FROM {$table_name} WHERE post_id = {$posts}.ID) " . 'desc';
			}
		}
		return $orderby;
	}
	
	/**
	 * custom_columns function
	 *
	 * Adds content for custom columns
	 *
	 * @return void
	 **/
	public function custom_columns( $column, $post_id ) {
		if ( 'ai1ec_event_date' === $column ) {
			try {
				$event = $this->_registry->get( 'model.event', $post_id );
				$time  = $this->_registry->get( 'view.event.time' );
				echo $time->get_timespan_html( $event );
			} catch( Exception $e ) {
				// event wasn't found, output empty string
				echo '';
			}
		}
	}
	
	/**
	 * sortable_columns function
	 *
	 * Enable sorting of columns
	 *
	 * @return void
	 **/
	public function sortable_columns( $columns ) {
		$columns["ai1ec_event_date"] = 'ai1ec_event_date';
		return $columns;
	}

	/**
	 * taxonomy_filter_restrict_manage_posts function
	 *
	 * Adds filter dropdowns for event categories and event tags
	 *
	 * @return void
	 **/
	function taxonomy_filter_restrict_manage_posts() {
		global $typenow;
	
		// =============================================
		// = add the dropdowns only on the events page =
		// =============================================
		if( $typenow === AI1EC_POST_TYPE ) {
			$filters = get_object_taxonomies( $typenow );
			foreach( $filters as $tax_slug ) {
				$tax_obj = get_taxonomy( $tax_slug );
				wp_dropdown_categories( array(
				'show_option_all' => __( 'Show All ', AI1EC_PLUGIN_NAME ) . $tax_obj->label,
				'taxonomy'        => $tax_slug,
				'name'            => $tax_obj->name,
				'orderby'         => 'name',
				'selected'        => isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : '',
				'hierarchical'    => $tax_obj->hierarchical,
				'show_count'      => true,
				'hide_if_empty'   => true
				));
			}
		}
	}
	
	/**
	 * taxonomy_filter_post_type_request function
	 *
	 * Adds filtering of events list by event tags and event categories
	 *
	 * @return void
	 **/
	function taxonomy_filter_post_type_request( $query ) {
		global $pagenow, $typenow;
		if( 'edit.php' === $pagenow ) {
			$filters = get_object_taxonomies( $typenow );
			foreach( $filters as $tax_slug ) {
				$var = &$query->query_vars[$tax_slug];
				if( isset( $var ) ) {
					$term = null;
	
					if( is_numeric( $var ) ) {
						$term = get_term_by( 'id', $var, $tax_slug );
					} else {
						$term = get_term_by( 'slug', $var, $tax_slug );
					}
	
					if( isset( $term->slug ) ) {
						$var = $term->slug;
					}
				}
			}
		}
		// ===========================
		// = Order by Event date ASC =
		// ===========================
		if( 'ai1ec_event' === $typenow ) {
			if ( ! array_key_exists( 'orderby', $query->query_vars ) ) {
				$query->query_vars['orderby'] = 'ai1ec_event_date';
				$query->query_vars['order']   = 'desc';
			}
		}
	}
}
