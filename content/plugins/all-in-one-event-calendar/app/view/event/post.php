<?php

/**
 * This class renders the html for the event colors.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View.Event
 */
class Ai1ec_View_Event_Post extends Ai1ec_Base {

	/**
	 * Add event-specific messages to be used when one is modified in dashboard.
	 *
	 * @wp_hook post_updated_messages
	 *
	 * @param  array $messages List of messages.
	 *
	 * @return array Modified list of messages.
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages[AI1EC_POST_TYPE] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf(
				Ai1ec_I18n::__( 'Event updated. <a href="%s">View event</a>' ),
				esc_url( get_permalink( $post_ID ) )
			),
			2 => Ai1ec_I18n::__( 'Custom field updated.' ),
			3 => Ai1ec_I18n::__( 'Custom field deleted.' ),
			4 => Ai1ec_I18n::__( 'Event updated.' ),
			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision'] )
				? sprintf(
					Ai1ec_I18n::__( 'Event restored to revision from %s' ),
					wp_post_revision_title( (int) $_GET['revision'], false )
				)
				: false,
			6 => sprintf(
				Ai1ec_I18n::__( 'Event published. <a href="%s">View event</a>' ),
				esc_url( get_permalink($post_ID) )
			),
			7 => Ai1ec_I18n::__( 'Event saved.' ),
			8 => sprintf(
				Ai1ec_I18n::__( 'Event submitted. <a target="_blank" href="%s">Preview event</a>' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),
			9 => sprintf(
				Ai1ec_I18n::__( 'Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>' ),
				// translators: Publish box date format, see http://php.net/date
				$this->_registry->get( 'date.time', $post->post_date )->format_i18n( Ai1ec_I18n::__( 'M j, Y @ G:i' ) ),
				esc_url( get_permalink($post_ID) )
			),
			10 => sprintf(
				Ai1ec_I18n::__( 'Event draft updated. <a target="_blank" href="%s">Preview event</a>' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),
		);

		return $messages;
	}

	/**
	 * Generates an excerpt from the given content string.
	 *
	 * Adapted from WordPress's `wp_trim_excerpt' function that is not useful
	 * for applying to custom content.
	 *
	 * @param string $text The content to trim.
	 *
	 * @return string The excerpt.
	 */
	public function trim_excerpt( Ai1ec_Event $event, $length = 35, $more = '[...]' ) {
		$raw_excerpt    = $event->get( 'post' )->post_content;
		if ( ! isset( $raw_excerpt{0} ) ) {
			$raw_excerpt = '&nbsp;';
		}

		$text           = preg_replace(
			'#<\s*script[^>]*>.+<\s*/\s*script\s*>#x',
			'',
			apply_filters(
				'the_excerpt',
				$raw_excerpt
			)
		);
		$text           = strip_shortcodes( $text );
		$text           = str_replace( ']]>', ']]&gt;', $text );
		$text           = strip_tags( $text );

		$excerpt_length = apply_filters( 'excerpt_length', $length );
		$excerpt_more   = apply_filters( 'excerpt_more', $more );
		$words          = preg_split(
			'/\s+/',
			$text,
			$excerpt_length + 1,
			PREG_SPLIT_NO_EMPTY
		);
		if ( count( $words ) > $excerpt_length ) {
			array_pop( $words );
			$text = implode( ' ', $words );
			$text = $text . $excerpt_more;
		} else {
			$text = implode( ' ', $words );
		}
		return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
	}

}