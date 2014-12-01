<?php

/**
 * The Acces Control Object class.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.acl
 */
class Ai1ec_Acl_Aco {

	/**
	 * Whether it's All event page or not.
	 * 
	 * @return boolean
	 */
	public function is_all_events_page() {
		global $typenow;
		return $typenow === 'ai1ec_event';
	}

	/**
	 * Whether the current request is for a network or blog admin page
	 *
	 * Does not inform on whether the user is an admin! Use capability checks to
	 * tell if the user should be accessing a section or not.
	 *
	 * @return bool True if inside WordPress administration pages.
	 */
	public function is_admin() {
		return is_admin();
	}

	/**
	 * Check if we are editing our custom post type.
	 * 
	 * @return boolean
	 */
	public function are_we_editing_our_post() {
		global $post;
		return (
			is_object( $post ) &&
			isset( $post->post_type ) &&
			AI1EC_POST_TYPE === $post->post_type
		);
	}

	/**
	 * Check if it's our own custom post type.
	 *
	 * @param int|object $post Optional. Post ID or post object.
	 * Default is the current post from the loop.
	 *
	 * @return boolean
	 */
	public function is_our_post_type( $post_to_check = null ) {
		if ( null === $post_to_check ) {
			global $post;
			$post_to_check = $post;
		}
		return get_post_type( $post_to_check ) === AI1EC_POST_TYPE;
	}

}