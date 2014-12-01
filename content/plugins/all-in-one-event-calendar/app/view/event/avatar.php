<?php

/**
 * This class renders the html for the event avatar.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View.Event
 */
class Ai1ec_View_Event_Avatar extends Ai1ec_Base {

	/**
	 * Get HTML markup for the post's "avatar" image according conditional
	 * fallback model.
	 *
	 * Accepts an ordered array of named avatar $fallbacks. Also accepts a string
	 * of space-separated classes to add to the default classes.
	 * @param   Ai1ec_Event $event          The event to get the avatar for
	 * @param   array|null  $fallback_order Order of fallback in searching for
	 *                                      images, or null to use default
	 * @param   string      $classes        A space-separated list of CSS classes
	 *                                      to apply to the outer <div> element.
	 * @param   boolean     $wrap_permalink Whether to wrap the element in a link
	 *                                      to the event details page.
	 *
	 * @return  string                   String of HTML if image is found
	 */
	public function get_event_avatar(
		Ai1ec_Event $event,
		$fallback_order = null,
		$classes        = '',
		$wrap_permalink = true
	) {
		$source = $size = null;
		$url    = $this->get_event_avatar_url(
			$event,
			$fallback_order,
			$source,
			$size
		);

		if ( empty( $url ) ) {
			return '';
		}

		$url     = esc_attr( $url );
		$classes = esc_attr( $classes );

		// Set the alt tag (helpful for SEO).
		$alt      = $event->get( 'post' )->post_title;
		$location = $this->_registry->get( 'view.event.location' )->get_short_location( $event );
		if ( ! empty( $location ) ) {
			$alt .= ' @ ' . $location;
		}

		$alt       = esc_attr( $alt );
		$size_attr = $size[0] ? "width=\"$size[0]\" height=\"$size[1]\"" : "";
		$html      = '<img src="' . $url . '" alt="' . $alt . '" ' .
			$size_attr . ' />';

		if ( $wrap_permalink ) {
			$permalink = add_query_arg(
				'instance_id',
				$event->get( 'instance_id' ),
				get_permalink( $event->get( 'post_id' ) )
			);
			$html = '<a href="' . $permalink . '">' . $html . '</a>';
		}

		$classes .= ' ai1ec-' . $source;
		$classes .= ( $size[0] > $size[1] )
			? ' ai1ec-landscape'
			: ' ai1ec-portrait';
		$html     = '<div class="ai1ec-event-avatar timely ' . $classes . '">' .
			$html . '</div>';

		return $html;
	}

	/**
	 * Get the post's "avatar" image url according conditional fallback model.
	 *
	 * Accepts an ordered array of named methods for $fallback order. Returns
	 * image URL or null if no image found. Also returns matching fallback in the
	 * $source reference.
	 *
	 * @param   array|null $fallback_order Order of fallbacks in search for images
	 * @param   null       $source         Fallback that returned matching image,
	 *                                     returned format is string
	 * @param   null       $size           (width, height) array of returned image
	 *
	 * @return  string|null
	 */
	public function get_event_avatar_url(
		Ai1ec_Event $event,
		$fallback_order = NULL,
		&$source        = NULL,
		&$size          = NULL
	) {
		if ( empty( $fallback_order ) ) {
			$fallback_order = array(
				'post_thumbnail',
				'content_img',
				'category_avatar',
				'default_avatar',
			);
		}

		$valid_fallbacks = array(
			'post_image'          => 'get_post_image_url',
			'post_thumbnail'      => 'get_post_thumbnail_url',
			'content_img'         => 'get_content_img_url',
			'category_avatar'     => 'get_category_avatar_url',
			'default_avatar'      => 'get_default_avatar_url',
		);

		foreach ( $fallback_order as $fallback ) {
			if ( ! isset( $valid_fallbacks[$fallback] ) ) {
				continue;
			}

			$function = $valid_fallbacks[$fallback];
			$url      = $this->$function( $event, $size );
			if ( NULL !== $url ) {
				$source = $fallback;
				break;
			}
		}

		if ( empty( $url ) ) {
			return NULL;
		}
		return $url;
	}

	/**
	 * Read post meta for post-thumbnail and return its URL as a string.
	 *
	 * @param Ai1ec_Event $event Event object.
	 * @param null        $size  (width, height) array of returned image.
	 *
	 * @return  string|null
	 */
	public function get_post_thumbnail_url( Ai1ec_Event $event, &$size = null ) {
		return $this->_get_post_attachment_url(
			$event,
			array(
				'medium',
				'large',
				'full',
			),
			$size
		);
	}

	/**
	 * Read post meta for post-image and return its URL as a string.
	 *
	 * @param Ai1ec_Event $event Event object.
	 * @param null        $size  (width, height) array of returned image.
	 *
	 * @return  string|null
	 */
	public function get_post_image_url( Ai1ec_Event $event, &$size = null ) {
		return $this->_get_post_attachment_url(
			$event,
			array(
				'full',
				'large',
				'medium'
			),
			$size
		);
	}

	/**
	 * Simple regex-parse of post_content for matches of <img src="foo" />; if
	 * one is found, return its URL.
	 *
	 * @param   Ai1ec_Event $event
	 * @param   null        $size           (width, height) array of returned image
	 *
	 * @return  string|null
	 */
	public function get_content_img_url( Ai1ec_Event $event, &$size = null ) {
		$matches = $this->get_image_from_content(
			$event->get( 'post' )->post_content
		);
		// Check if we have a result, otherwise a notice is issued.
		if ( empty( $matches ) ) {
			return null;
		}

		$url = $matches[2];
		$size = array( 0, 0 );

		// Try to detect width and height.
		$attrs = $matches[1] . $matches[3];
		$matches = null;
		preg_match_all(
			'/(width|height)=["\']?(\d+)/i',
			$attrs,
			$matches,
			PREG_SET_ORDER
		);
		// Check if we have a result, otherwise a notice is issued.
		if ( ! empty( $matches ) ) {
			foreach ( $matches as $match ) {
				$size[ $match[1] === 'width' ? 0 : 1 ] = $match[2];
			}
		}
		return $url;
	}

	/**
	 * Get an image tag from an html string
	 * 
	 * @param string $content
	 * 
	 * @return array
	 */
	public function get_image_from_content( $content ) {
		preg_match(
			'/<img([^>]+)src=["\']?([^"\'\ >]+)([^>]*)>/i',
			$content,
			$matches
		);
		return $matches;
	}
	/**
	 * Returns default avatar image (normally when no other ones are available).
	 *
	 * @param   null       $size           (width, height) array of returned image
	 *
	 * @return  string|null
	 */
	public function get_default_avatar_url( &$size = null ) {
		$loader = $this->_registry->get( 'theme.loader' );
		$file = $loader->get_file( 'default-event-avatar.png', array(), false );
		$size = array( 256, 256 );
		return $file->get_url();
	}

	/**
	 * Returns avatar image for event's deepest category, if any.
	 *
	 * @param Ai1ec_Event $event Avatar requester.
	 * @param void        $size  Unused argument.
	 *
	 * @return string|null Avatar's HTML or null if none.
	 */
	public function get_category_avatar_url( Ai1ec_Event $event, &$size = null ) {
		$db =  $this->_registry->get( 'dbi.dbi' );

		$terms = $this->_registry->get( 'model.taxonomy' )->get_post_categories(
			$event->get( 'post_id' )
		);
		if ( empty( $terms ) ) {
			return null;
		}

		$terms_by_id = array();
		// Key $terms by term_id rather than arbitrary int.
		foreach ( $terms as $term ) {
			$terms_by_id[$term->term_id] = $term;
		}

		// Array to store term depths, sorted later.
		$term_depths = array();
		foreach ( $terms_by_id as $term ) {
			$depth = 0;
			$ancestor = $term;
			while ( ! empty( $ancestor->parent ) ) {
				$depth++;
				if ( ! isset( $terms_by_id[$ancestor->parent] ) ) {
					break;
				}
				$ancestor = $terms_by_id[$ancestor->parent];
			}
			// Store negative depths for asort() to order from deepest to shallowest.
			$term_depths[$term->term_id] = -$depth;
		}
		// Order term IDs by depth.
		asort( $term_depths );

		$url = '';
		$model = $this->_registry->get( 'model.taxonomy' );
		// Starting at deepest depth, find the first category that has an avatar.
		foreach ( $term_depths as $term_id => $depth ) {
			$term_image = $model->get_category_image( $term_id );
			if ( $term_image ) {
				$url = $term_image;
				break;
			}
		}
		return empty( $url ) ? null : $url;
	}

	/**
	 * Read post meta for post-attachment and return its URL as a string.
	 *
	 * @param Ai1ec_Event $event             Event object.
	 * @param array       $ordered_img_sizes Image sizes order.
	 * @param null        $size              (width, height) array of returned
	 *                                       image.
	 *
	 * @return  string|null
	 */
	protected function _get_post_attachment_url(
		Ai1ec_Event $event,
		array $ordered_img_sizes,
		&$size = null
	) {
		// Since WP does will return null if the wrong size is targeted,
		// we iterate over an array of sizes, breaking if a URL is found.
		foreach ( $ordered_img_sizes as $size ) {
			$attributes = wp_get_attachment_image_src(
				get_post_thumbnail_id( $event->get( 'post_id' ) ), $size
			);
			if ( $attributes ) {
				$url = array_shift( $attributes );
				$size = $attributes;
				break;
			}
		}

		return empty( $url ) ? null : $url;
	}

}