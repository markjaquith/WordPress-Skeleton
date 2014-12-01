<?php

/**
 * This class process event content.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View.Event
 */
class Ai1ec_View_Event_Content extends Ai1ec_Base {

	/**
	 * Render event excerpt header.
	 *
	 * @param Ai1ec_Event $event Event to render excerpt for.
	 *
	 * @return void Content is not returned, just rendered.
	 */
	public function excerpt_view( Ai1ec_Event $event ) {
		$location = $this->_registry->get( 'view.event.location' );
		$location = esc_html(
            str_replace(
				"\n",
				', ',
				rtrim( $location->get_location( $event ) )
			)
		);
        $args = array(
            'event'      => $event,
            'location'   => $location,
            'text_when'  => __( 'When:', AI1EC_PLUGIN_NAME ),
            'text_where' => __( 'Where:', AI1EC_PLUGIN_NAME ),
		);
		$loader = $this->_registry->get( 'theme.loader' );
		echo $loader->get_file(
				'event-excerpt.twig',
				$args,
				true
		)->get_content();
	}

	/**
	 * Format events excerpt view.
	 *
	 * @param string $text Content to excerpt.
	 *
	 * @return string Formatted event excerpt.
	 */
	public function event_excerpt( $text ) {
		if ( ! $this->_registry->get( 'acl.aco' )->is_our_post_type() ) {
			return $text;
		}
        $event = $this->_registry->get( 'model.event', get_the_ID() );
        $post  = $this->_registry->get( 'view.event.post' );
		$ob    = $this->_registry->get( 'compatibility.ob' );
        $ob->start();
		$this->excerpt_view( $event );
        // Re-apply any filters to the post content that normally would have
        // been applied if it weren't for our interference (below).
		echo shortcode_unautop( wpautop( $post->trim_excerpt( $event ) ) );
        return $ob->get_clean();
	}

	/**
	 * Avoid re-adding `wpautop` for Ai1EC instances.
	 *
	 * @param string $content Processed content.
	 *
	 * @return string Paragraphs enclosed text.
	 */
	public function event_excerpt_noautop( $content ) {
		if ( ! $this->_registry->get( 'acl.aco' )->is_our_post_type() ) {
			return wpautop( $content );
		}
		return $content;
	}

	public function get_post_excerpt( Ai1ec_Event $event ) {
		$content = strip_tags(
			strip_shortcodes(
				preg_replace(
					'#<\s*script[^>]*>.+<\s*/\s*script\s*>#x',
					'',
					apply_filters(
						'ai1ec_the_content',
						apply_filters(
							'the_content',
							$event->get( 'post' )->post_content
						)
					)
				)
			)
		);
		$content = preg_replace( '/\s+/', ' ', $content );
		$words   = explode( ' ', $content );
		if ( count( $words ) > 25 ) {
			return implode(
				' ',
				array_slice( $words, 0, 25 )
			) . ' [...]';
		}
		return $content;
	}

	/**
	 * Generate the html for the "Back to calendar" button for this event.
	 *
	 * @return string
	 */
	public function get_back_to_calendar_button_html() {
		$class     = '';
		$data_type = '';
		$href      = '';
		if ( isset( $_COOKIE['ai1ec_calendar_url'] ) ) {
			$href = json_decode(
				stripslashes( $_COOKIE['ai1ec_calendar_url'] )
			);
			setcookie( 'ai1ec_calendar_url', '', time() - 3600 );
		} else {
			$href = $this->_registry->get( 'html.element.href', array() );
			$href = $href->generate_href();
		}
		$text    = esc_attr( Ai1ec_I18n::__( 'Back to Calendar' ) );
		$tooltip = esc_attr( Ai1ec_I18n::__( 'View all events' ) );
		$html    = <<<HTML
<a class="ai1ec-calendar-link ai1ec-btn ai1ec-btn-default ai1ec-btn-sm
		ai1ec-tooltip-trigger $class"
	href="$href"
	$data_type
	data-placement="left"
	title="$tooltip">
	<i class="ai1ec-fa ai1ec-fa-calendar ai1ec-fa-fw"></i>
	<span class="ai1ec-hidden-xs">$text</span>
</a>
HTML;
		return apply_filters( 'ai1ec_get_back_to_calendar_html', $html, $href );
	}

	/**
	 * Simple regex-parse of post_content for matches of <img src="foo" />; if
	 * one is found, return its URL.
	 *
	 * @param   null       $size           (width, height) array of returned image
	 *
	 * @return  string|null
	 */
	public function get_content_img_url( Ai1ec_Event $event, &$size = null ) {
		preg_match(
			'/<img([^>]+)src=["\']?([^"\'\ >]+)([^>]*)>/i',
			$event->get( 'post' )->post_content,
			$matches
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

}
