<?php

/**
 * The concrete class for month view.
*
* @author     Time.ly Network Inc.
* @since      2.0
*
* @package    AI1EC
* @subpackage AI1EC.View
*/
class Ai1ec_Calendar_View_Month extends Ai1ec_Calendar_View_Abstract {

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Abstract::get_name()
	*/
	public function get_name() {
		return 'month';
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Abstract::get_content()
	*/
	public function get_content( array $view_args ) {
		$date_system = $this->_registry->get( 'date.system' );
		$settings    = $this->_registry->get( 'model.settings' );
		$defaults = array(
			'month_offset'  => 0,
			'cat_ids'       => array(),
			'auth_ids'      => array(),
			'tag_ids'       => array(),
			'post_ids'      => array(),
			'exact_date'    => $date_system->current_time(),
		);
		$args = wp_parse_args( $view_args, $defaults );
		$local_date = $this->_registry
			->get( 'date.time', $args['exact_date'], 'sys.default' );
		$local_date->set_date(
				$local_date->format( 'Y' ),
				$local_date->format( 'm' ) + $args['month_offset'],
				1
			)
			->set_time( 0, 0, 0 );

		$days_events = $this->get_events_for_month(
			$local_date,
			apply_filters(
				'ai1ec_get_events_relative_to_filter',
				array(
					'cat_ids'  => $args['cat_ids'],
					'tag_ids'  => $args['tag_ids'],
					'post_ids' => $args['post_ids'],
					'auth_ids' => $args['auth_ids'],
				),
				$view_args
			)
		);
		$cell_array = $this->get_month_cell_array(
			$local_date,
			$days_events
		);
		// Create pagination links.
		$title = $local_date->format_i18n( 'F Y' );
		$pagination_links = $this->_get_pagination( $args, $title );

		$is_ticket_button_enabled = apply_filters(
			'ai1ec_month_ticket_button',
			false
		);

		$view_args = array(
			'title'                    => $title,
			'type'                     => 'month',
			'weekdays'                 => $this->get_weekdays(),
			'cell_array'               => $cell_array,
			'show_location_in_title'   => $settings->get( 'show_location_in_title' ),
			'month_word_wrap'          => $settings->get( 'month_word_wrap' ),
			'post_ids'                 => join( ',', $args['post_ids'] ),
			'data_type'                => $args['data_type'],
			'data_type_events'         => '',
			'is_ticket_button_enabled' => $is_ticket_button_enabled,
			'text_venue_separator'     => __( '@ %s', AI1EC_PLUGIN_NAME ),
		);
		if ( $settings->get( 'ajaxify_events_in_web_widget' ) ) {
			$view_args['data_type_events'] = $args['data_type'];
		}

		// Add navigation if requested.
		$view_args['navigation'] = $this->_get_navigation(
			array(
				'no_navigation'    => $args['no_navigation'],
				'pagination_links' => $pagination_links,
				'views_dropdown'   => $args['views_dropdown'],
			)
		);

		return
			$this->_registry->get( 'http.request' )->is_json_required(
				$args['request_format']
			)
			? json_encode( $view_args )
			: $this->_get_view( $view_args );
	}

	/**
	 * Returns a non-associative array of four links for the month view of the
	 * calendar:
	 *    previous year, previous month, next month, and next year.
	 * Each element is an associative array containing the link's enabled status
	 * ['enabled'], CSS class ['class'], text ['text'] and value to assign to
	 * link's href ['href'].
	 *
	 * @param array  $args  Current request arguments
	 * @param string $title Title to display in datepicker button
	 *
	 * @return array      Array of links
	 */
	function get_month_pagination_links( $args, $title ) {
		$links = array();

		$local_date = $this->_registry
			->get( 'date.time', $args['exact_date'], 'sys.default' );
		$orig_date = clone $local_date;
		// =================
		// = Previous year =
		// =================
		// Align date to first of month, month offset applied, 1 year behind.
		$local_date
			->set_date(
				$local_date->format( 'Y' ) -1,
				$local_date->format( 'm' ) + $args['month_offset'],
				1
			)
			->set_time( 0, 0, 0 );

		$args['exact_date'] = $local_date->format();
		$href = $this->_registry->get( 'html.element.href', $args );
		$links[] = array(
			'enabled' => true,
			'class'=> 'ai1ec-prev-year',
			'text' =>
			'<i class="ai1ec-fa ai1ec-fa-angle-double-left"></i> ' .
				$local_date->format_i18n( 'Y' ),
			'href' => $href->generate_href(),
		);

		// ==================
		// = Previous month =
		// ==================
		// Align date to first of month, month offset applied, 1 month behind.
		$local_date
			->set_date(
				$local_date->format( 'Y' ) + 1,
				$local_date->format( 'm' ) - 1,
				1
			);
		$args['exact_date'] = $local_date->format();
		$href = $this->_registry->get( 'html.element.href', $args );
		$links[] = array(
			'enabled' => true,
			'class'=> 'ai1ec-prev-month',
			'text' => '<i class="ai1ec-fa ai1ec-fa-angle-left"></i> ' .
			$local_date->format_i18n( 'M' ),
			'href' => $href->generate_href(),
		);

		// ======================
		// = Minical datepicker =
		// ======================
		// Align date to first of month, month offset applied.

		$orig_date
			->set_date(
				$orig_date->format( 'Y' ),
				$orig_date->format( 'm' ) + $args['month_offset'],
				1
			);
		$args['exact_date'] = $orig_date->format();
		$factory = $this->_registry->get( 'factory.html' );
		$links[] = $factory->create_datepicker_link(
			$args,
			$args['exact_date'],
			$title
		);

		// ==============
		// = Next month =
		// ==============
		// Align date to first of month, month offset applied, 1 month ahead.
		$orig_date
			->set_date(
				$orig_date->format( 'Y' ),
				$orig_date->format( 'm' ) + 1,
				1
			);
		$args['exact_date'] = $orig_date->format();
		$href = $this->_registry->get( 'html.element.href', $args );
		$links[] = array(
			'enabled' => true,
			'class'=> 'ai1ec-next-month',
			'text' =>
			$orig_date->format_i18n( 'M' ) .
			' <i class="ai1ec-fa ai1ec-fa-angle-right"></i>',
			'href' => $href->generate_href(),
		);

		// =============
		// = Next year =
		// =============
		// Align date to first of month, month offset applied, 1 year ahead.
		$orig_date
			->set_date(
				$orig_date->format( 'Y' ) + 1,
				$orig_date->format( 'm' ) - 1,
				1
			);
		$args['exact_date'] = $orig_date->format();
		$href = $this->_registry->get( 'html.element.href', $args );
		$links[] = array(
			'enabled' => true,
			'class'=> 'ai1ec-next-year',
			'text' =>
			$orig_date->format_i18n( 'Y' ) .
			' <i class="ai1ec-fa ai1ec-fa-angle-double-right"></i>',
			'href' => $href->generate_href(),
		);

		return $links;
	}

	/**
	 * get_weekdays function
	 *
	 * Returns a list of abbreviated weekday names starting on the configured
	 * week start day setting.
	 *
	 * @return array
	 */
	protected function get_weekdays() {
		$settings    = $this->_registry->get( 'model.settings' );
		static $weekdays;

		if ( ! isset( $weekdays ) ) {
			$time = $this->_registry->get(
				'date.time',
				'next Sunday',
				'sys.default'
			);
			$time->adjust_day( $settings->get( 'week_start_day' ) );

			$weekdays = array();
			for( $i = 0; $i < 7; $i++ ) {
				$weekdays[] = $time->format_i18n( 'D' );
				$time->adjust_day( 1 );// Add a day
			}
		}
		return $weekdays;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Abstract::_add_view_specific_runtime_properties()
	 */
	protected function _add_view_specific_runtime_properties(
		Ai1ec_Event $event
	) {
		$end_day = $this->_registry->get( 'date.time', $event->get( 'end' ) )
			->adjust( -1, 'second' )
			->format_i18n( 'd' );
		$event->set_runtime( 'multiday_end_day', $end_day );
	}

	/**
	 * get_month_cell_array function
	 *
	 * Return an array of weeks, each containing an array of days, each
	 * containing the date for the day ['date'] (if inside the month) and
	 * the events ['events'] (if any) for the day, and a boolean ['today']
	 * indicating whether that day is today.
	 *
	 * @param int $timestamp	    UNIX timestamp of the 1st day of the desired
	 *                            month to display
	 * @param array $days_events  list of events for each day of the month in
	 *                            the format returned by get_events_for_month()
	 *
	 * @return void
	 */
	protected function get_month_cell_array( Ai1ec_Date_Time $timestamp, $days_events ) {
		$settings    = $this->_registry->get( 'model.settings' );
		$date_system = $this->_registry->get( 'date.system' );
		$today = $this->_registry->get( 'date.time' );// Used to flag today's cell

		// Figure out index of first table cell
		$first_cell_index = $timestamp->format( 'w' );
		// Modify weekday based on start of week setting
		$first_cell_index = ( 7 + $first_cell_index - $settings->get( 'week_start_day' ) ) % 7;

		// Get the last day of the month
		$last_day = $timestamp->format( 't' );
		$last_timestamp = clone $timestamp;
		$last_timestamp->set_date(
			$timestamp->format( 'Y' ),
			$timestamp->format( 'm' ),
			$last_day
			)->set_time( 0, 0, 0 );
		// Figure out index of last table cell
		$last_cell_index = $last_timestamp->format( 'w' );
		// Modify weekday based on start of week setting
		$last_cell_index = ( 7 + $last_cell_index - $settings->get( 'week_start_day' ) ) % 7;

		$weeks = array();
		$week = 0;
		$weeks[$week] = array();

		// Insert any needed blank cells into first week
		for( $i = 0; $i < $first_cell_index; $i++ ) {
			$weeks[$week][] = array(
				'date'       => null,
				'events'     => array(),
				'date_link'  => null
			);
		}

		// Insert each month's day and associated events
		for( $i = 1; $i <= $last_day; $i++ ) {
			$day = $this->_registry->get( 'date.time' )
				->set_date(
					$timestamp->format( 'Y' ),
					$timestamp->format( 'm' ),
					$i
				)
				->set_time( 0, 0, 0 )
				->format();
			$exact_date = $date_system->format_date_for_url(
				$day,
				$settings->get( 'input_date_format' )
			);
			$events = array();
			foreach ( $days_events[$i] as $evt ){
				$events[] = array(
					'filtered_title'   => $evt->get_runtime( 'filtered_title' ),
					'post_excerpt'     => $evt->get_runtime( 'post_excerpt' ),
					'color_style'      => $evt->get_runtime( 'color_style' ),
					'category_colors'  => $evt->get_runtime( 'category_colors' ),
					'permalink'        => $evt->get_runtime( 'instance_permalink' ),
					'ticket_url_label' => $evt->get_runtime( 'ticket_url_label' ),
					'edit_post_link'   => $evt->get_runtime( 'edit_post_link' ),
					'short_start_time' => $evt->get_runtime( 'short_start_time' ),
					'multiday_end_day' => $evt->get_runtime( 'multiday_end_day' ),
					'short_start_time' => $evt->get_runtime( 'short_start_time' ),
					'instance_id'      => $evt->get( 'instance_id' ),
					'post_id'          => $evt->get( 'post_id' ),
					'is_allday'        => $evt->is_allday(),
					'is_multiday'      => $evt->is_multiday(),
					'venue'            => $evt->get( 'venue' ),
					'ticket_url'       => $evt->get( 'ticket_url' ),
					'start_truncated'  => $evt->get( 'start_truncated' ),
					'end_truncated'    => $evt->get( 'end_truncated' ),
					'popup_timespan'   => $this->_registry
						->get( 'twig.ai1ec-extension')->timespan( $evt, 'short' ),
					'avatar'           => $this->_registry
						->get( 'twig.ai1ec-extension')->avatar(
							$evt,
							array(
								'post_thumbnail',
								'content_img',
								'location_avatar',
								'category_avatar',
							),
							'',
							false ),
				);
			}
			if ( AI1EC_THEME_COMPATIBILITY_FER ) {
				$events = $days_events[$i];
			}
			$weeks[$week][] = array(
				'date' => $i,
				'date_link' => $this->_create_link_for_day_view( $exact_date ),
				'today' =>
					$timestamp->format( 'Y' ) == $today->format( 'Y' ) &&
					$timestamp->format( 'm' ) == $today->format( 'm' ) &&
					$i                        == $today->format( 'j' ),
				'events' => $events,

			);
			// If reached the end of the week, increment week
			if( count( $weeks[$week] ) == 7 )
				$week++;
		}

		// Insert any needed blank cells into last week
		for( $i = $last_cell_index + 1; $i < 7; $i++ ) {
			$weeks[$week][] = array( 'date' => null, 'events' => array() );
		}

		return $weeks;
	}

	/**
	 * get_events_for_month function
	 *
	 * Return an array of all dates for the given month as an associative
	 * array, with each element's value being another array of event objects
	 * representing the events occuring on that date.
	 *
	 * @param int $time         the UNIX timestamp of a date within the desired month
	 * @param array $filter     Array of filters for the events returned:
	 *                          ['cat_ids']   => non-associatative array of category IDs
	 *                          ['tag_ids']   => non-associatative array of tag IDs
	 *                          ['post_ids']  => non-associatative array of post IDs
	 *                          ['auth_ids']  => non-associatative array of author IDs
	 *
	 * @return array            array of arrays as per function's description
	 */
	protected function get_events_for_month(
		Ai1ec_Date_Time $time,
		$filter = array()
	) {
		$last_day = $time->format( 't' );

		$day_entry = array(
			'multi'  => array(),
			'allday' => array(),
			'other'  => array(),
		);
		$days_events = array_fill(
			1,
			$last_day,
			$day_entry
		);
		unset( $day_entry );
		$start_time = clone $time;
		$start_time->set_date(
			$time->format( 'Y' ),
			$time->format( 'm' ),
			1
		)->set_time( 0, 0, 0 );
		$end_time = clone $start_time;
		$end_time->adjust_month( 1 );
		$search = $this->_registry->get( 'model.search' );
		$month_events = $search->get_events_between(
			$start_time,
			$end_time,
			$filter,
			true
		);
		$start_time = $start_time->format();
		$end_time   = $end_time->format();
		$this->_update_meta( $month_events );
		$this->_registry->get( 'controller.content-filter' )
			->clear_the_content_filters();
		foreach ( $month_events as $event ) {
			$event_start = $event->get( 'start' )->format();
			$event_end   = $event->get( 'end' )->format();

			/**
			 * REASONING: we assume, that event spans multiple periods, one of
			 * which happens to be current (month). Thus we mark, that current
			 * event starts at the very first day of current month and further
			 * we will mark it as having truncated beginning (unless it is not
			 * overlapping period boundaries).
			 * Although, if event starts after the first second of this period
			 * it's start day will be decoded as time 'j' format (`int`-casted
			 * to increase map access time), of it's actual start time.
			*/
			$day = 1;
			if ( $event_start > $start_time ) {
				$day = (int)$event->get( 'start' )->format( 'j' );
			}

			// Set multiday properties. TODO: Should these be made event object
			// properties? They probably shouldn't be saved to the DB, so I'm
			// not sure. Just creating properties dynamically for now.
			if ( $event_start < $start_time ) {
				$event->set( 'start_truncated', true );
			}
			if ( $event_end >= $end_time ) {
				$event->set( 'end_truncated', true );
			}

			// Categorize event.
			$priority = 'other';
			if ( $event->is_allday() ) {
				$priority = 'allday';
			} elseif ( $event->is_multiday() ) {
				$priority = 'multi';
			}
			$this->_add_runtime_properties( $event );
			$days_events[$day][$priority][] = $event;
		}
		$this->_registry->get( 'controller.content-filter' )
			->restore_the_content_filters();
		for ( $day = 1; $day <= $last_day; $day++ ) {
			$days_events[$day] = array_merge(
				$days_events[$day]['multi'],
				$days_events[$day]['allday'],
				$days_events[$day]['other']
			);
		}

		return apply_filters(
			'ai1ec_get_events_for_month',
			$days_events,
			$time,
			$filter
		);
	}

}
