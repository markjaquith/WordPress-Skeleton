<?php

/**
 * The concrete class for day view.
*
* @author     Time.ly Network Inc.
* @since      2.0
*
* @package    AI1EC
* @subpackage AI1EC.View
*/
class Ai1ec_Calendar_View_Week extends Ai1ec_Calendar_View_Abstract {

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Abstract::get_name()
	*/
	public function get_name() {
		return 'week';
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Calendar_View_Abstract::get_content()
	*/
	public function get_content( array $view_args ) {
		$date_system = $this->_registry->get( 'date.system' );
		$settings    = $this->_registry->get( 'model.settings' );
		$defaults    = array(
			'week_offset'   => 0,
			'cat_ids'       => array(),
			'tag_ids'       => array(),
			'auth_ids'      => array(),
			'post_ids'      => array(),
			'exact_date'    => $date_system->current_time(),
		);
		$args = wp_parse_args( $view_args, $defaults );

		// Localize requested date and get components.
		$local_date = $this->_registry
			->get( 'date.time', $args['exact_date'], 'sys.default' );
		$start_day_offset = $this->get_week_start_day_offset( $local_date->format( 'w' ) );
		// get the first day of week
		$local_date->adjust_day( 0 + $start_day_offset + ( $args['week_offset'] * 7 ) )
			->set_time( 0, 0, 0 );

		$cell_array = $this->get_week_cell_array(
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

		// Create pagination links.	(Translators: '%s' = week's start date.)
		$title = sprintf(
			__( 'Week of %s', AI1EC_PLUGIN_NAME ),
			$local_date->format_i18n( 'F j' )
		);
		$pagination_links = $this->_get_pagination( $args, $title );

		$time_format = $this->_registry->get( 'model.option' )
			->get( 'time_format', Ai1ec_I18n::__( 'g a' ) );

		// Calculate today marker's position.
		$now = $this->_registry->get( 'date.time' );
		$now_text = $now->format_i18n( 'M j' );
		$now = $now->format( 'G' ) * 60 + $now->format( 'i' );
		// Find out if the current week view contains "now" and thus should display
		// the "now" marker.
		$show_now = false;
		foreach ( $cell_array as $day ) {
			if ( $day['today'] ) {
				$show_now = true;
				break;
			}
		}

		$is_ticket_button_enabled = apply_filters( 'ai1ec_week_ticket_button', false );
		$show_reveal_button       = apply_filters( 'ai1ec_week_reveal_button', false );

		$hours = array();
		$today = $this->_registry->get( 'date.time', 'now', 'sys.default' );
		for ( $hour = 0; $hour < 24; $hour++ ) {
			$hours[] = $today
				->set_time( $hour, 0, 0 )
				->format_i18n( $time_format );
		}

		$view_args = array(
			'title'                    => $title,
			'type'                     => 'week',
			'cell_array'               => $cell_array,
			'show_location_in_title'   => $settings->get( 'show_location_in_title' ),
			'now_top'                  => $now,
			'now_text'                 => $now_text,
			'show_now'                 => $show_now,
			'post_ids'                 => join( ',', $args['post_ids'] ),
			'time_format'              => $time_format,
			'done_allday_label'        => false,
			'done_grid'                => false,
			'data_type'                => $args['data_type'],
			'data_type_events'         => '',
			'is_ticket_button_enabled' => $is_ticket_button_enabled,
			'show_reveal_button'       => $show_reveal_button,
			'text_full_day'            => __( 'Reveal full day', AI1EC_PLUGIN_NAME ),
			'text_all_day'             => __( 'All-day', AI1EC_PLUGIN_NAME ),
			'text_now_label'           => __( 'Now:', AI1EC_PLUGIN_NAME ),
			'text_venue_separator'     => __( '@ %s', AI1EC_PLUGIN_NAME ),
			'hours'                    => $hours,
			'indent_multiplier'        => 8,
			'indent_offset'            => 0,
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
	 * Returns a non-associative array of two links for the week view of the
	 * calendar:
	 *    previous week, and next week.
	 * Each element is an associative array containing the link's enabled status
	 * ['enabled'], CSS class ['class'], text ['text'] and value to assign to
	 * link's href ['href'].
	 *
	 * @param array  $args  Current request arguments
	 * @param string $title Title to display in datepicker button
	 *
	 * @return array      Array of links
	 */
	protected function get_week_pagination_links( $args, $title ) {
		$links = array();

		$orig_date = $args['exact_date'];

		$negative_offset = $args['week_offset'] * 7 - 7;
		$positive_offset = $args['week_offset'] * 7 + 7;
		// =================
		// = Previous week =
		// =================
		$local_date = $this->_registry
			->get( 'date.time', $args['exact_date'], 'sys.default' )
			->adjust_day( $negative_offset )
			->set_time( 0, 0, 0 );
		$args['exact_date'] = $local_date->format();
		$href       = $this->_registry->get( 'html.element.href', $args );
		$links[] = array(
			'enabled' => true,
			'class'=> 'ai1ec-prev-week',
			'text' => '<i class="ai1ec-fa ai1ec-fa-chevron-left"></i>',
			'href' => $href->generate_href(),
		);
		// ======================
		// = Minical datepicker =
		// ======================
		$args['exact_date'] = $orig_date;
		$factory = $this->_registry->get( 'factory.html' );
		$links[] = $factory->create_datepicker_link(
			$args,
			$args['exact_date'],
			$title
		);

		// =============
		// = Next week =
		// =============
		$local_date->adjust_day( $positive_offset * 2 ); // above was (-1), (+2) is to counteract
		$args['exact_date'] = $local_date->format();
		$href    = $this->_registry->get( 'html.element.href', $args );
		$links[] = array(
			'enabled' => true,
			'class'=> 'ai1ec-next-week',
			'text' => '<i class="ai1ec-fa ai1ec-fa-chevron-right"></i>',
			'href' => $href->generate_href(),
		);

		return $links;
	}

	/**
	 * get_week_cell_array function
	 *
	 * Return an associative array of weekdays, indexed by the day's date,
	 * starting the day given by $timestamp, each element an associative array
	 * containing three elements:
	 *   ['today']     => whether the day is today
	 *   ['allday']    => non-associative ordered array of events that are all-day
	 *   ['notallday'] => non-associative ordered array of non-all-day events to
	 *                    display for that day, each element another associative
	 *                    array like so:
	 *   ['top']       => how many minutes offset from the start of the day
	 *   ['height']    => how many minutes this event spans
	 *   ['indent']    => how much to indent this event to accommodate multiple
	 *                    events occurring at the same time (0, 1, 2, etc., to
	 *                    be multiplied by whatever desired px/em amount)
	 *   ['event']     => event data object
	 *
	 * @param int $start_of_week    the UNIX timestamp of the first day of the week
	 * @param array $filter     Array of filters for the events returned:
	 *                          ['cat_ids']   => non-associatative array of category IDs
	 *                          ['tag_ids']   => non-associatative array of tag IDs
	 *                          ['post_ids']  => non-associatative array of post IDs
	 *                          ['auth_ids']  => non-associatative array of author IDs
	 *
	 * @return array            array of arrays as per function description
	 */
	protected function get_week_cell_array( Ai1ec_Date_Time $start_of_week, $filter = array() ) {
		$search      = $this->_registry->get( 'model.search' );
		$settings    = $this->_registry->get( 'model.settings' );
		$date_system = $this->_registry->get( 'date.system' );
		$end_of_week = $this->_registry->get( 'date.time', $start_of_week );
		$end_of_week->adjust_day( 7 );
		// Do one SQL query to find all events for the week, including spanning
		$week_events = $search->get_events_between(
			$start_of_week,
			$end_of_week,
			$filter,
			true
		);
		$this->_update_meta( $week_events );
		// Split up events on a per-day basis
		$all_events = array();
		$this->_days_cache = $this->_registry->get( 'cache.memory' );
		$this->_registry->get( 'controller.content-filter' )
			->clear_the_content_filters();
		foreach ( $week_events as $evt ) {
			list( $evt_start, $evt_end ) = $this->
				_get_view_specific_timestamps( $evt );

			// Iterate through each day of the week and generate new event object
			// based on this one for each day that it spans
			for (
				$day = $start_of_week->format( 'j' ),
					$last_week_day_index = $start_of_week->format( 'j' ) + 7;
				$day < $last_week_day_index;
				$day++
			) {
				list( $day_start, $day_end ) = $this->
					_get_wkday_start_end( $day, $start_of_week );

				if ( $evt_end < $day_start ) {
					break; // save cycles
				}

				// If event falls on this day, make a copy.
				if ( $evt_end > $day_start && $evt_start < $day_end ) {
					$_evt = clone $evt;
					if ( $evt_start < $day_start ) {
						// If event starts before this day, adjust copy's start time
						$_evt->set( 'start', $day_start );
						$_evt->set( 'start_truncated', true );
					}
					if ( $evt_end > $day_end ) {
						// If event ends after this day, adjust copy's end time
						$_evt->set( 'end', $day_end );
						$_evt->set( 'end_truncated', true );
					}

					// Store reference to original, unmodified event, required by view.
					$_evt->set( '_orig', $evt );
					$this->_add_runtime_properties( $_evt );

					// Place copy of event in appropriate category
					if ( $_evt->is_allday() ) {
						$all_events[$day_start]['allday'][] = $_evt;
					} else {
						$all_events[$day_start]['notallday'][] = $_evt;
					}
				}
			}
		}
		$this->_registry->get( 'controller.content-filter' )
			->restore_the_content_filters();
		// This will store the returned array
		$days = array();
		$now  = $this->_registry->get(
			'date.time',
			'now',
			$start_of_week->get_timezone()
		);
		// =========================================
		// = Iterate through each date of the week =
		// =========================================
		for (
			$day = $start_of_week->format( 'j' ),
				$last_week_day_index = $start_of_week->format( 'j' ) + 7;
			$day < $last_week_day_index;
			$day++
		) {
			list( $day_date, , $day_date_ob ) = $this->
				_get_wkday_start_end( $day, $start_of_week );

			$exact_date = $date_system->format_datetime_for_url(
				$day_date_ob,
				$settings->get( 'input_date_format' )
			);
			$href_for_date = $this->_create_link_for_day_view( $exact_date );

			// Initialize empty arrays for this day if no events to minimize warnings
			if ( ! isset( $all_events[$day_date]['allday'] ) ) {
				$all_events[$day_date]['allday'] = array();
			}
			if ( ! isset( $all_events[$day_date]['notallday'] ) ) {
				$all_events[$day_date]['notallday'] = array();
			}

			$evt_stack = array( 0 ); // Stack to keep track of indentation

			foreach ( $all_events[$day_date] as $event_type => &$events ) {
				foreach ( $events as &$evt ) {
					$event = array(
						'filtered_title'   => $evt->get_runtime( 'filtered_title' ),
						'post_excerpt'     => $evt->get_runtime( 'post_excerpt' ),
						'color_style'      => $evt->get_runtime( 'color_style' ),
						'category_colors'  => $evt->get_runtime( 'category_colors' ),
						'permalink'        => $evt->get_runtime( 'instance_permalink' ),
						'ticket_url_label' => $evt->get_runtime( 'ticket_url_label' ),
						'edit_post_link'   => $evt->get_runtime( 'edit_post_link' ),
						'faded_color'      => $evt->get_runtime( 'faded_color' ),
						'rgba_color'       => $evt->get_runtime( 'rgba_color' ),
						'short_start_time' => $evt->get_runtime( 'short_start_time' ),
						'instance_id'      => $evt->get( 'instance_id' ),
						'post_id'          => $evt->get( 'post_id' ),
						'is_multiday'      => $evt->get( 'is_multiday' ),
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
					if ( AI1EC_THEME_COMPATIBILITY_FER ) {
						$event = $evt;
					}
					if ( 'notallday' === $event_type) {
						$start = $evt->get( 'start' );
						// Calculate top and bottom edges of current event
						$top = $start->format( 'G' ) * 60 + $start->format( 'i' );
						$bottom = min( $top + $evt->get_duration() / 60, 1440 );
						// While there's more than one event in the stack and this event's top
						// position is beyond the last event's bottom, pop the stack
						while ( count( $evt_stack ) > 1 && $top >= end( $evt_stack ) ) {
							array_pop( $evt_stack );
						}
						// Indentation is number of stacked events minus 1
						$indent = count( $evt_stack ) - 1;
						// Push this event onto the top of the stack
						array_push( $evt_stack, $bottom );
						$evt = array(
							'top'    => $top,
							'height' => $bottom - $top,
							'indent' => $indent,
							'event'  => $event,
						);
					} else {
						$evt = $event;
					}
				}
			}


			$days[$day_date] = array(
				'today'     =>
					$day_date_ob->format( 'Y' ) == $now->format( 'Y' ) &&
					$day_date_ob->format( 'm' ) == $now->format( 'm' ) &&
					$day_date_ob->format( 'j' ) == $now->format( 'j' ),
				'allday'    => $all_events[$day_date]['allday'],
				'notallday' => $all_events[$day_date]['notallday'],
				'href'      => $href_for_date,
				'day'       => $this->_registry->
					get( 'date.time', $day_date )->format_i18n( 'j' ),
				'weekday'   => $this->_registry->
					get( 'date.time', $day_date )->format_i18n( 'D' ),
			);
		}

		return apply_filters( 'ai1ec_get_week_cell_array', $days, $start_of_week, $filter );
	}

	/**
	 * get_week_start_day_offset function
	 *
	 * Returns the day offset of the first day of the week given a weekday in
	 * question.
	 *
	 * @param int $wday      The weekday to get information about
	 * @return int           A value between -6 and 0 indicating the week start
	 *                       day relative to the given weekday.
	 */
	protected function get_week_start_day_offset( $wday ) {
		$settings = $this->_registry->get( 'model.settings' );
		return - ( 7 - ( $settings->get( 'week_start_day' ) - $wday ) ) % 7;
	}

	/**
	 * Get start/end timestamps for a given weekday and week start identifier.
	 *
	 * @param int             $day        Week day number.
	 * @param Ai1ec_Date_Time $week_start Date/Time information for week start.
	 *
	 * @return array List of start and and timestamps, 0-indexed array.
	 */
	protected function _get_wkday_start_end(
		$day,
		Ai1ec_Date_Time $week_start
	) {
		$entry = null;
		$day   = (int)$day;
		if ( null === ( $entry = $this->_days_cache->get( $day ) ) ) {
			$day_start = $this->_registry
				->get( 'date.time', $week_start )
				->set_date(
					$week_start->format( 'Y' ),
					$week_start->format( 'm' ),
					$day
				)
				->set_time( 0, 0, 0 );
			$day_end   = $this->_registry->get( 'date.time', $day_start );
			$day_end->adjust_day( 1 );
			$entry     = array(
				$day_start->format(),
				$day_end->format(),
				$day_start
			);
			unset( $day_end ); // discard and free memory
			$this->_days_cache->set( $day, $entry );
		}
		return $entry;
	}

}
