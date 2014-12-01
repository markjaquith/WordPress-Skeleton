<?php

/**
 * Helps Rendering clone html.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Clone
 */
class Ai1ec_Clone_Renderer_Helper extends Ai1ec_Base {

	/**
	 * add clone bluk action in the dropdown
	 *
	 * @wp_hook admin_footer-edit.php
	 */
	public function duplicate_custom_bulk_admin_footer() {
		$aco = $this->_registry->get( 'acl.aco' );
		if ( true === $aco->are_we_editing_our_post() ) {
			?>
				<script type="text/javascript">
					jQuery(document).ready(function() {

						jQuery('<option>').val('clone').text('<?php _e( 'Clone', AI1EC_PLUGIN_NAME )?>').appendTo("select[name='action']");
						jQuery('<option>').val('clone').text('<?php _e( 'Clone', AI1EC_PLUGIN_NAME )?>').appendTo("select[name='action2']");

					});
				</script>
			<?php
		}
	}

	/**
	 * Add the link to action list for post_row_actions
	 *
	 * @wp_hook post_row_action
	 *
	 */
	function duplicate_post_make_duplicate_link_row( $actions, $post ) {
		if ( $post->post_type == "ai1ec_event" ) {
			$actions['clone'] = '<a href="'.$this->duplicate_post_get_clone_post_link( $post->ID , 'display', false).'" title="'
			. esc_attr(__("Make new copy of event", AI1EC_PLUGIN_NAME))
			. '">' .  __( 'Clone', AI1EC_PLUGIN_NAME ) . '</a>';
			$actions['edit_as_new_draft'] = '<a href="' . $this->duplicate_post_get_clone_post_link( $post->ID ) . '" title="'
			. esc_attr(__( 'Copy to a new draft' , AI1EC_PLUGIN_NAME ))
			. '">' .  __( 'Clone to Draft' , AI1EC_PLUGIN_NAME ) . '</a>';
		}
		return $actions;
	}

	/**
	 * Retrieve duplicate post link for post.
	 *
	 *
	 * @param int $id Optional. Post ID.
	 * @param string $context Optional, default to display. How to write the '&', defaults to '&amp;'.
	 * @param string $draft Optional, default to true
	 * @return string
	 */
	function duplicate_post_get_clone_post_link( $id = 0, $context = 'display', $draft = true ) {

		if ( ! $post = get_post( $id ) ) {
			return;
		}


		if ( $draft ) {
			$action_name = "duplicate_post_save_as_new_post_draft";
		} else {
			$action_name = "duplicate_post_save_as_new_post";
		}

		if ( 'display' == $context ) {
			$action = '?action=' . $action_name . '&amp;post=' . $post->ID;
		} else {
			$action = '?action=' . $action_name . '&post=' . $post->ID;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return;
		}

		return apply_filters(
			'duplicate_post_get_clone_post_link',
			wp_nonce_url( admin_url( "admin.php". $action ), 'ai1ec_clone_' . $post->ID ),
			$post->ID,
			$context
		);
	}
}