<?php

/**
 * Redirect for categories and tags.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Request
 */
class Ai1ec_Request_Redirect extends Ai1ec_Base {

	/**
	 * Checks if current request is direct for Events cats/tags and redirects
	 * to filtered calendar.
	 *
	 * @param WP $wpobj WP object.
	 *
	 * @return void Method does not return.
	 */
	public function handle_categories_and_tags( WP $wpobj ) {
		$cats = Ai1ec_Event_Taxonomy::CATEGORIES;
		$tags = Ai1ec_Event_Taxonomy::TAGS;
		if (
			! isset( $wpobj->query_vars ) || (
				! isset( $wpobj->query_vars[$cats] ) &&
				! isset( $wpobj->query_vars[$tags] )
			)
		) {
			return;
		}
		$is_cat = isset( $wpobj->query_vars[$cats] );
		$is_tag = isset( $wpobj->query_vars[$tags] );
		if ( $is_cat ) {
			$query_ident = $cats;
			$url_ident   = 'cat_ids';
		}
		if ( $is_tag ) {
			$query_ident = $tags;
			$url_ident   = 'tag_ids';
		}
		$term = get_term_by(
			'slug',
			$wpobj->query_vars[$query_ident],
			$query_ident
		);
		if ( ! $term ) {
			return;
		}
		$href = $this->_registry->get(
			'html.element.href',
			array( $url_ident => $term->term_id )
		);
		return Ai1ec_Http_Response_Helper::redirect(
			$href->generate_href(),
			301
		);
	}
}

