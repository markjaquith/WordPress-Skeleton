<?php

/**
 * The concrete command that clone events.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Command
 */
class Ai1ec_Command_Clone extends Ai1ec_Command {

	/**
	 * @var array The posts that must be cloned
	 */
	protected $_posts = array();

	/**
	 * @var bool Whether to redirect or not
	 */
	protected $_redirect = false;

	/**
	 * The abstract method concrete command must implement.
	 *
	 * Retrieve whats needed and returns it
	 *
	 * @return array
	 */
	public function do_execute() {
		$id = 0;
		foreach ( $this->_posts as $post ) {
			$id = $this->duplicate_post_create_duplicate(
				$post['post'],
				$post['status']
			);
		}
		if ( true === $this->_redirect ) {
			if ( '' === $post['status'] ) {
				return array(
					'url' => admin_url( 'edit.php?post_type=' . AI1EC_POST_TYPE ),
					'query_args' => array()
				);
			} else {
				return array(
					'url' => admin_url( 'post.php?action=edit&post=' . $id ),
					'query_args' => array()
				);
			}
		}
		// no redirect, just go on with the page
		return array();
	}

	/**
	 * Returns whether this is the command to be executed.
	 *
	 * I handle the logi of execution at this levele, which is not usual for
	 * The front controller pattern, because othe extensions need to inject
	 * logic into the resolver ( oAuth or ics export for instance )
	 * and this seems to me to be the most logical way to do this.
	 *
	 * @return boolean
	 */
	public function is_this_to_execute() {
		$current_action = $this->_registry->get(
			'http.request'
		)->get_current_action();

		if (
			'clone' === $current_action &&
			! empty( $_REQUEST['post'] )
		) {
			foreach ( $_REQUEST['post'] as $post_id ) {
				$this->_posts[] = array(
					'status' => '',
					'post'   => get_post( $post_id )
				);
			}
			return true;
		}

		// other actions need the nonce to be verified

		// duplicate single post
		if (
			$current_action === 'duplicate_post_save_as_new_post' &&
			! empty( $_REQUEST['post'] )
		) {
			check_admin_referer( 'ai1ec_clone_'. $_REQUEST['post'] );

			$this->_posts[] = array(
				'status' => '',
				'post'   => get_post( $_REQUEST['post'] )
			);
			$this->_redirect = true;
			return true;
		}
		// duplicate single post as draft
		if (
			$current_action === 'duplicate_post_save_as_new_post_draft' &&
			! empty( $_REQUEST['post'] )
		) {
			check_admin_referer( 'ai1ec_clone_'. $_REQUEST['post'] );
			$this->_posts[] = array(
				'status' => 'draft',
				'post'   => get_post( $_REQUEST['post'] )
			);
			$this->_redirect = true;
			return true;
		}
		return false;
	}

	/**
	 * Sets the render strategy.
	 *
	 * @param Ai1ec_Request_Parser $request
	 */
	public function set_render_strategy( Ai1ec_Request_Parser $request ) {
		if ( true === $this->_redirect ) {
			$this->_render_strategy = $this->_registry
				->get( 'http.response.render.strategy.redirect' );
		} else {
			$this->_render_strategy = $this->_registry
				->get( 'http.response.render.strategy.void' );
		}
	}

	/**
	 * Create a duplicate from a posts' instance
	 */
	public function duplicate_post_create_duplicate( $post, $status = '' ) {
		$post            = get_post( $post );
		$new_post_author = $this->_duplicate_post_get_current_user();
		$new_post_status = $status;
		if ( empty( $new_post_status ) ) {
			$new_post_status = $post->post_status;
		}
		$new_post_status = $this->_get_new_post_status( $new_post_status );

		$new_post = array(
			'menu_order'     => $post->menu_order,
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'pinged'         => $post->pinged,
			'post_author'    => $new_post_author->ID,
			'post_content'   => $post->post_content,
			'post_date'      => $post->post_date,
			'post_date_gmt'  => get_gmt_from_date( $post->post_date  ),
			'post_excerpt'   => $post->post_excerpt,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => $new_post_status,
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
		);

		$new_post_id    = wp_insert_post( $new_post );
		$edit_event_url = esc_attr(
			admin_url( "post.php?post={$new_post_id}&action=edit" )
		);
		$message = sprintf(
			__( '<p>The event <strong>%s</strong> was cloned succesfully. <a href="%s">Edit cloned event</a></p>', AI1EC_PLUGIN_NAME ),
			$post->post_title,
			$edit_event_url
		);
		$notification   = $this->_registry->get( 'notification.admin' );
		$notification->store( $message );
		$this->_duplicate_post_copy_post_taxonomies( $new_post_id, $post );
		$this->_duplicate_post_copy_attachments(     $new_post_id, $post );
		$this->_duplicate_post_copy_post_meta_info(  $new_post_id, $post );

		if ( $this->_registry->get( 'acl.aco' )->is_our_post_type( $post ) ) {
			try {
				$old_event = $this->_registry->get( 'model.event', $post->ID );
				$old_event->set( 'post_id',         $new_post_id );
				$old_event->set( 'post',            null );
				$old_event->set( 'ical_feed_url',   null );
				$old_event->set( 'ical_source_url', null );
				$old_event->set( 'ical_organizer',  null );
				$old_event->set( 'ical_contact',    null );
				$old_event->set( 'ical_uid',        null );
				$old_event->save();
			} catch ( Ai1ec_Event_Not_Found_Exception $exception ) {
				/* ignore */
			}
		}

		$meta_post = $this->_registry->get( 'model.meta-post' );
		$meta_post->delete( $new_post_id, '_dp_original' );
		$meta_post->add(    $new_post_id, '_dp_original', $post->ID );

		// If the copy gets immediately published, we have to set a proper slug.
		if (
			$new_post_status == 'publish' ||
			$new_post_status == 'future'
		) {
			$post_name = wp_unique_post_slug(
				$post->post_name,
				$new_post_id,
				$new_post_status,
				$post->post_type,
				$post->post_parent
			);

			$new_post = array();
			$new_post['ID']        = $new_post_id;
			$new_post['post_name'] = $post_name;

			// Update the post into the database
			wp_update_post( $new_post );
		}

		return $new_post_id;
	}

	/**
	 * Copy the meta information of a post to another post
	 */
	protected function _duplicate_post_copy_post_meta_info( $new_id, $post ) {
		$post_meta_keys = get_post_custom_keys( $post->ID );
		if ( empty( $post_meta_keys ) ) return;
		//$meta_blacklist = explode(",",get_option('duplicate_post_blacklist'));
		//if ( $meta_blacklist == "" )
		$meta_blacklist = array();
		$meta_keys = array_diff( $post_meta_keys, $meta_blacklist );

		foreach ( $meta_keys as $meta_key ) {
			$meta_values = get_post_custom_values( $meta_key, $post->ID );
			foreach ( $meta_values as $meta_value ) {
				$meta_value = maybe_unserialize( $meta_value );
				add_post_meta( $new_id , $meta_key , $meta_value );
			}
		}
	}

	/**
	 * Copy the attachments
	 * It simply copies the table entries, actual file won't be duplicated
	 */
	protected function _duplicate_post_copy_attachments( $new_id, $post ) {
		//if (get_option('duplicate_post_copyattachments') == 0) return;

		// get old attachments
		$attachments = get_posts(
			array(
				'post_type'   => 'attachment',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => $post->ID,
			)
		);
		// clone old attachments
		foreach ( $attachments as $att ) {
			$new_att_author = $this->_duplicate_post_get_current_user();

			$new_att = array(
				'menu_order'     => $att->menu_order,
				'comment_status' => $att->comment_status,
				'guid'           => $att->guid,
				'ping_status'    => $att->ping_status,
				'pinged'         => $att->pinged,
				'post_author'    => $new_att_author->ID,
				'post_content'   => $att->post_content,
				'post_date'      => $att->post_date,
				'post_date_gmt'  => get_gmt_from_date( $att->post_date ),
				'post_excerpt'   => $att->post_excerpt,
				'post_mime_type' => $att->post_mime_type,
				'post_parent'    => $new_id,
				'post_password'  => $att->post_password,
				'post_status'    => $this->_get_new_post_status(
					$att->post_status
				),
				'post_title'     => $att->post_title,
				'post_type'      => $att->post_type,
				'to_ping'        => $att->to_ping,
			);

			$new_att_id = wp_insert_post( $new_att );

			// get and apply a unique slug
			$att_name = wp_unique_post_slug(
				$att->post_name,
				$new_att_id,
				$att->post_status,
				$att->post_type,
				$new_id
			);
			$new_att = array();
			$new_att['ID']        = $new_att_id;
			$new_att['post_name'] = $att_name;

			wp_update_post( $new_att );


		}
	}

	/**
	 * Copy the taxonomies of a post to another post
	 */
	protected function _duplicate_post_copy_post_taxonomies( $new_id, $post ) {
		$db = $this->_registry->get( 'dbi.dbi' );
		if ( $db->are_terms_set() ) {
			// Clear default category (added by wp_insert_post)
			wp_set_object_terms( $new_id , NULL, 'category' );

			$post_taxonomies = get_object_taxonomies( $post->post_type );

			$taxonomies_blacklist = array();
			$taxonomies = array_diff( $post_taxonomies , $taxonomies_blacklist );
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms(
					$post->ID ,
					$taxonomy ,
					array( 'orderby' => 'term_order' )
				);
				$terms = array();
				for ( $i = 0; $i < count( $post_terms ); $i++ ) {
					$terms[] = $post_terms[ $i ]->slug;
				}
				wp_set_object_terms( $new_id , $terms , $taxonomy );
			}
		}
	}

	/**
	 * Get the currently registered user
	 */
	protected function _duplicate_post_get_current_user() {
		if ( function_exists( 'wp_get_current_user' ) ) {
			return wp_get_current_user();
		} else {
			$db = $this->_registry->get( 'dbi.dbi' );
			$query = $db->prepare(
				'SELECT * FROM ' . $wpdb->users . ' WHERE user_login = %s',
				$_COOKIE[ USER_COOKIE ]
			);
			$current_user = $db->get_results( $query );
			return $current_user;
		}
	}

	/**
	 * Get the status for `duplicate' post
	 *
	 * If user cannot publish post (event), and original post status is
	 * *publish*, then it will be duplicated with *pending* status.
	 * In other cases original status will remain.
	 *
	 * @param string $old_status Status of old post
	 *
	 * @return string Status for new post
	 */
	protected  function _get_new_post_status( $old_status ) {
		if (
			'publish' === $old_status &&
			! current_user_can( 'publish_ai1ec_events' )
		) {
			return 'pending';
		}
		return $old_status;
	}
}
