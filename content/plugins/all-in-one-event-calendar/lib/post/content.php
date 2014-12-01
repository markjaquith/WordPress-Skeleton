<?php

/**
 * Checks if processed page is calendar default page and post has content.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Lib
 */
class Ai1ec_Post_Content_Check extends Ai1ec_Base {

	/**
	 * Checks if post has content for default calendar page and if not sets one.
	 *
	 * @param WP_Post|null $post Post object.
	 *
	 * @return void Method does not return.
	 */
	public function check_content( $post ) {
		if (
			null === $post ||
			! is_object( $post ) ||
			! isset( $post->post_content )
		) {
			return;
		}
		if (
			empty( $post->post_content ) &&
			is_page() &&
			$post->ID === $this->_registry->get( 'model.settings' )
				->get( 'calendar_page_id' )
		) {
			$post->post_content = '<!-- Time.ly Calendar placeholder -->';
		}
	}
}