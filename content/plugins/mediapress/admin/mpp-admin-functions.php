<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * Check if we are on the Dashboard->Gallery listing page
 * 
 * @return boolean
 */
function mpp_admin_is_gallery_list() {
	
	$screen_id = 'edit-' . mpp_get_gallery_post_type();
	
	//on ajax request, shortcircuit
	if ( defined( 'DOING_AJAX' ) ) {
		return false;
	}
	
	if ( is_admin() && function_exists( 'get_current_screen' ) ) {
		
		$screen = get_current_screen();
		if ( isset( $screen->id ) && $screen->id ==  $screen_id ) {
			return true;
		}
	}
	
	return false;
}
/**
 * Sortable taxonomy columns
 * Credit: http://scribu.net/wordpress/sortable-taxonomy-columns.html
 * Modified to suit our purpose
 * Allows us to sort the gallery listing by 
 * Slightly modified to fit our purpose
 * 
 * @global type $wpdb
 * @param type $clauses
 * @param type $wp_query
 * @return type
 */
function mpp_taxonomy_filter_clauses( $clauses, $wp_query ) {
	
	
	//only apply if we are on the mpp gallery list screen
	if( ! mpp_admin_is_gallery_list() )
		return $clauses;
	
	

	if ( ! isset( $wp_query->query['orderby'] ) )
		return $clauses;
	
	$order_by = $wp_query->query['orderby'];
	
	$order_by_tax = mpp_translate_to_taxonomy( $order_by );
	
	if( ! $order_by_tax || ! in_array( $order_by, array( 'component', 'status', 'type') ) )
		return $clauses;
	
	global $wpdb;
	//if we are here, It is for one of our taxonomy
	
	$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

		$clauses['where'] .= $wpdb->prepare(" AND (taxonomy = %s OR taxonomy IS NULL)", $order_by_tax );
		$clauses['groupby'] = "object_id";
		$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
		$clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
	

	return $clauses;
}
add_filter( 'posts_clauses', 'mpp_taxonomy_filter_clauses', 10, 2 );