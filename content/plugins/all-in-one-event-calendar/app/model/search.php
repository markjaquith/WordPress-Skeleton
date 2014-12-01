<?php

/**
 * Search Event.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.search
 */
class Ai1ec_Event_Search extends Ai1ec_Base {

	/**
	 * @var Ai1ec_Dbi instance
	 */
	private $_dbi = null;

	/**
	 * Creates local DBI instance.
	 */
	public function __construct( Ai1ec_Registry_Object $registry ){
		parent::__construct( $registry );
		$this->_dbi = $this->_registry->get( 'dbi.dbi' );
	}

	/**
	 * Fetches the event object with the given post ID.
	 *
	 * Uses the WP cache to make this more efficient if possible.
	 *
	 * @param int      $post_id     The ID of the post associated.
	 * @param bool|int $instance_id Instance ID, to fetch post details for.
	 *
	 * @return Ai1ec_Event The associated event object.
	 */
	public function get_event( $post_id, $instance_id = false ) {
		$post_id     = (int)$post_id;
		$instance_id = (int)$instance_id;
		if ( $instance_id < 1 ) {
			$instance_id = false;
		}
		return $this->_registry->get( 'model.event', $post_id, $instance_id );
	}

	/**
	 * Return events falling within some time range.
	 *
	 * Return all events starting after the given start time and before the
	 * given end time that the currently logged in user has permission to view.
	 * If $spanning is true, then also include events that span this
	 * period. All-day events are returned first.
	 *
	 * @param Ai1ec_Date_Time $start Limit to events starting after this.
	 * @param Ai1ec_Date_Time $end   Limit to events starting before this.
	 * @param array $filter          Array of filters for the events returned:
	 *                                   ['cat_ids']  => list of category IDs;
	 *                                   ['tag_ids']  => list of tag IDs;
	 *                                   ['post_ids'] => list of post IDs;
	 *                                   ['auth_ids'] => list of author IDs.
	 * @param bool $spanning         Also include events that span this period.
	 *
	 * @return array List of matching event objects.
	 */
	public function get_events_between(
		Ai1ec_Date_Time $start,
		Ai1ec_Date_Time $end,
		array $filter = array(),
		$spanning     = false
	) {
		// Query arguments
		$args = array(
			$start->format_to_gmt(),
			$end->format_to_gmt(),
		);

		// Get post status Where snippet and associated SQL arguments
		$where_parameters  = $this->_get_post_status_sql();
		$post_status_where = $where_parameters['post_status_where'];
		$args              = array_merge( $args, $where_parameters['args'] );

		// Get the Join (filter_join) and Where (filter_where) statements based
		// on $filter elements specified
		$filter = $this->_get_filter_sql( $filter );

		$ai1ec_localization_helper = $this->_registry->get( 'p28n.wpml' );

		$wpml_join_particle = $ai1ec_localization_helper
			->get_wpml_table_join( 'p.ID' );

		$wpml_where_particle = $ai1ec_localization_helper
			->get_wpml_table_where();

		if ( $spanning ) {
			$spanning_string = 'i.end > %d AND i.start < %d ';
		} else {
			$spanning_string = 'i.start BETWEEN %d AND %d ';
		}

		$sql = '
			SELECT
				`p`.*,
				`e`.`post_id`,
				`i`.`id` AS `instance_id`,
				`i`.`start` AS `start`,
				`i`.`end` AS `end`,
				`e`.`timezone_name` AS `timezone_name`,
				`e`.`allday` AS `event_allday`,
				`e`.`recurrence_rules`,
				`e`.`exception_rules`,
				`e`.`recurrence_dates`,
				`e`.`exception_dates`,
				`e`.`venue`,
				`e`.`country`,
				`e`.`address`,
				`e`.`city`,
				`e`.`province`,
				`e`.`postal_code`,
				`e`.`instant_event`,
				`e`.`show_map`,
				`e`.`contact_name`,
				`e`.`contact_phone`,
				`e`.`contact_email`,
				`e`.`contact_url`,
				`e`.`cost`,
				`e`.`ticket_url`,
				`e`.`ical_feed_url`,
				`e`.`ical_source_url`,
				`e`.`ical_organizer`,
				`e`.`ical_contact`,
				`e`.`ical_uid`,
				`e`.`longitude`,
				`e`.`latitude`
			FROM
				' . $this->_dbi->get_table_name( 'ai1ec_events' ) . ' e
				INNER JOIN
					' . $this->_dbi->get_table_name( 'posts' ) . ' p
						ON ( `p`.`ID` = `e`.`post_id` )
				' . $wpml_join_particle . '
				INNER JOIN
					' . $this->_dbi->get_table_name( 'ai1ec_event_instances' ) . ' i
					ON ( `e`.`post_id` = `i`.`post_id` )
				' . $filter['filter_join'] . '
			WHERE
				post_type = \'' . AI1EC_POST_TYPE . '\'
				' . $wpml_where_particle . '
			AND
				' . $spanning_string . '
				' . $filter['filter_where'] . '
				' . $post_status_where . '
			GROUP BY
				`i`.`id`
			ORDER BY
				`e` . `allday`     DESC,
				`i` . `start`      ASC,
				`p` . `post_title` ASC';

		$query  = $this->_dbi->prepare( $sql, $args );
		$events = $this->_dbi->get_results( $query, ARRAY_A );

		$id_list = array();
		foreach ( $events as $event ) {
			$id_list[] = $event['post_id'];
		}

		if ( ! empty( $id_list ) ) {
			update_meta_cache( 'post', $id_list );
		}

		foreach ( $events as &$event ) {
			$event['allday'] = $this->_is_all_day( $event );
			$event           = $this->_registry->get( 'model.event', $event );
		}

		return $events;
	}

	/**
	 * get_events_relative_to function
	 *
	 * Return all events starting after the given reference time, limiting the
	 * result set to a maximum of $limit items, offset by $page_offset. A
	 * negative $page_offset can be provided, which will return events *before*
	 * the reference time, as expected.
	 *
	 * @param int $time           limit to events starting after this (local) UNIX time
	 * @param int $limit          return a maximum of this number of items
	 * @param int $page_offset    offset the result set by $limit times this number
	 * @param array $filter       Array of filters for the events returned.
	 *                            ['cat_ids']   => non-associatative array of category IDs
	 *                            ['tag_ids']   => non-associatative array of tag IDs
	 *                            ['post_ids']  => non-associatative array of post IDs
	 *                            ['auth_ids']  => non-associatative array of author IDs
	 * @param int $last_day       Last day (time), that was displayed.
	 *                            NOTE FROM NICOLA: be careful, if you want a query with events
	 *                            that have a start date which is greater than today, pass 0 as
	 *                            this parameter. If you pass false ( or pass nothing ) you end up with a query
	 *                            with events that finish before today. I don't know the rationale
	 *                            behind this but that's how it works
	 *
	 * @return array              five-element array:
	 *                              ['events'] an array of matching event objects
	 *                              ['prev'] true if more previous events
	 *                              ['next'] true if more next events
	 *                              ['date_first'] UNIX timestamp (date part) of first event
	 *                              ['date_last'] UNIX timestamp (date part) of last event
	 */
	function get_events_relative_to(
		$time,
		$limit       = 0,
		$page_offset = 0,
		$filter      = array(),
		$last_day    = false
	) {
		$localization_helper = $this->_registry->get( 'p28n.wpml' );
		$settings = $this->_registry->get( 'model.settings' );


		// Even if there ARE more than 5 times the limit results - we shall not
		// try to fetch and display these, as it would crash system
		$upper_boundary = $limit;
		if (
			$settings->get( 'agenda_include_entire_last_day' ) &&
		( false !== $last_day )
		) {
			$upper_boundary *= 5;
		}

		// Convert timestamp to GMT time
		$time = $this->_registry->get(
			'date.system'
		)->current_time() >> 11 << 11;
		// Get post status Where snippet and associated SQL arguments
		$where_parameters  = $this->_get_post_status_sql();
		$post_status_where = $where_parameters['post_status_where'];

		// Get the Join (filter_join) and Where (filter_where) statements based
		// on $filter elements specified
		$filter = $this->_get_filter_sql( $filter );

		// Query arguments
		$args = array( $time );
		$args = array_merge( $args, $where_parameters['args'] );

		if( $page_offset >= 0 ) {
			$first_record = $page_offset * $limit;
		} else {
			$first_record = ( -$page_offset - 1 ) * $limit;
		}


		$wpml_join_particle  = $localization_helper
			->get_wpml_table_join( 'p.ID' );

		$wpml_where_particle = $localization_helper
			->get_wpml_table_where();

		$filter_date_clause = ( $page_offset >= 0 )
			? 'i.end >= %d '
			: 'i.start < %d ';
		$order_direction    = ( $page_offset >= 0 ) ? 'ASC' : 'DESC';
		if ( false !== $last_day ) {
			if ( 0 == $last_day ) {
				$last_day = $time;
			}
			$filter_date_clause = ' i.end ';
			if ( $page_offset < 0 ) {
				$filter_date_clause .= '<';
				$order_direction     = 'DESC';
			} else {
				$filter_date_clause .= '>';
				$order_direction     = 'ASC';
			}
			$filter_date_clause .= ' %d ';
			$args[0]             = $last_day;
			$first_record        = 0;
		}
		$query = $this->_dbi->prepare(
			'SELECT DISTINCT p.*, e.post_id, i.id AS instance_id, ' .
			'i.start AS start, ' .
			'i.end AS end, ' .
			'e.allday AS event_allday, ' .
			'e.recurrence_rules, e.exception_rules, e.ticket_url, e.instant_event, e.recurrence_dates, e.exception_dates, ' .
			'e.venue, e.country, e.address, e.city, e.province, e.postal_code, ' .
			'e.show_map, e.contact_name, e.contact_phone, e.contact_email, e.cost, ' .
			'e.ical_feed_url, e.ical_source_url, e.ical_organizer, e.ical_contact, e.ical_uid, e.timezone_name, e.longitude, e.latitude ' .
			'FROM ' . $this->_dbi->get_table_name( 'ai1ec_events' ) . ' e ' .
			'INNER JOIN ' . $this->_dbi->get_table_name( 'posts' ) . ' p ON e.post_id = p.ID ' .
			$wpml_join_particle .
			'INNER JOIN ' . $this->_dbi->get_table_name( 'ai1ec_event_instances' ) . ' i ON e.post_id = i.post_id ' .
			$filter['filter_join'] .
			"WHERE post_type = '" . AI1EC_POST_TYPE . "' " .
			'AND ' . $filter_date_clause .
			$wpml_where_particle .
			$filter['filter_where'] .
			$post_status_where .
			// Reverse order when viewing negative pages, to get correct set of
			// records. Then reverse results later to order them properly.
			'ORDER BY i.start ' . $order_direction .
			', post_title ' . $order_direction .
			' LIMIT ' . $first_record . ', ' . $upper_boundary,
			$args
		);

		$events = $this->_dbi->get_results( $query, ARRAY_A );

		// Limit the number of records to convert to data-object
		$events = $this->_limit_result_set(
			$events,
			$limit,
			( false !== $last_day )
		);

		// Reorder records if in negative page offset
		if( $page_offset < 0 ) {
			$events = array_reverse( $events );
		}

		$date_first = $date_last = NULL;

		foreach ( $events as &$event ) {
			$event['allday'] = $this->_is_all_day( $event );
			$event           = $this->_registry->get( 'model.event', $event );
			if ( null === $date_first ) {
				$date_first = $event->get( 'start' );
			}
			$date_last = $event->get( 'start' );
		}
		$date_first = $this->_registry->get( 'date.time', $date_first );
		$date_last  = $this->_registry->get( 'date.time', $date_last );
		// jus show next/prev links, in case no event found is shown.
		$next = true;
		$prev = true;

		return array(
			'events'     => $events,
			'prev'       => $prev,
			'next'       => $next,
			'date_first' => $date_first,
			'date_last'  => $date_last,
		);
	}

	/**
	 * Get ID of event in database, matching imported one.
	 *
	 * Return event ID by iCalendar UID, feed url, start time and whether the
	 * event has recurrence rules (to differentiate between an event with a UID
	 * defining the recurrence pattern, and other events with with the same UID,
	 * which are just RECURRENCE-IDs).
	 *
	 * @param int      $uid             iCalendar UID property
	 * @param string   $feed            Feed URL
	 * @param int      $start           Start timestamp (GMT)
	 * @param bool     $has_recurrence  Whether the event has recurrence rules
	 * @param int|null $exclude_post_id Do not match against this post ID
	 *
	 * @return object|null ID of matching event post, or NULL if no match
	 */
	public function get_matching_event_id(
		$uid,
		$feed,
		$start,
		$has_recurrence  = false,
		$exclude_post_id = null
	) {
		$dbi        = $this->_registry->get( 'dbi.dbi' );
		$table_name = $dbi->get_table_name( 'ai1ec_events' );
		$query      = 'SELECT `post_id` FROM ' . $table_name . '
			WHERE
				    ical_feed_url   = %s
				AND ical_uid        = %s
				AND start           = %d ' .
			( $has_recurrence ? 'AND NOT ' : 'AND ' ) .
			' ( recurrence_rules IS NULL OR recurrence_rules = \'\' )';
		$args = array( $feed, $uid );
		if ( $start instanceof Ai1ec_Date_Time ) {
			$args[] = $start->format();
		} else {
			$args[] = (int)$start;
		}
		if ( null !== $exclude_post_id ) {
			$query .= ' AND post_id <> %d';
			$args[] = $exclude_post_id;
		}

		return $dbi->get_var( $dbi->prepare( $query, $args ) );
	}

	/**
	 * Get event by UID. UID must be unique.
	 *
	 * NOTICE: deletes events with that UID if they have different URLs.
	 *
	 * @param string $uid UID from feed.
	 * @param string $uid Feed URL.
	 *
	 * @return int|null Matching Event ID or NULL if none found.
	 */
	public function get_matching_event_by_uid_and_url( $uid, $url ) {
		if ( ! isset( $uid{1} ) ) {
			return null;
		}
		$dbi        = $this->_registry->get( 'dbi.dbi' );
		$table_name = $dbi->get_table_name( 'ai1ec_events' );
		$argv       = array( $uid, $url );
		// fix issue where invalid feed URLs were assigned
		$delete     = 'SELECT `post_id` FROM `'. $table_name .
			'` WHERE `ical_uid` = %s AND `ical_feed_url` != %s';
		$post_ids   = $dbi->get_col( $dbi->prepare( $delete, $argv ) );
		foreach ( $post_ids as $pid ) {
			wp_delete_post( $pid, true );
		}
		// retrieve actual feed ID if any
		$select = 'SELECT `post_id` FROM `' . $table_name .
			'` WHERE `ical_uid` = %s';
		return $dbi->get_var( $dbi->prepare( $select, $argv ) );
	}

	/**
	 * Get event ids for the passed feed url
	 *
	 * @param string $feed_url
	 */
	public function get_event_ids_for_feed( $feed_url ) {
		$dbi        = $this->_registry->get( 'dbi.dbi' );
		$table_name = $dbi->get_table_name( 'ai1ec_events' );
		$query      = 'SELECT `post_id` FROM ' . $table_name .
						' WHERE ical_feed_url = %s';
		return $dbi->get_col( $dbi->prepare( $query, array( $feed_url ) ) );
	}

	/**
	 * Check if given event must be treated as all-day event.
	 *
	 * Event instances that span 24 hours are treated as all-day.
	 * NOTICE: event is passed in before being transformed into
	 * Ai1ec_Event object, with Ai1ec_Date_Time fields.
	 *
	 * @param array $event Event data returned from database.
	 *
	 * @return bool True if event is all-day event.
	 */
	protected function _is_all_day( array $event ) {
		if ( isset( $event['event_allday'] ) && $event['event_allday'] ) {
			return true;
		}

		if ( ! isset( $event['start'] ) || ! isset( $event['end'] ) ) {
			return false;
		}

		return ( 86400 === $event['end'] - $event['start'] );
	}

	/**
	 * _limit_result_set function
	 *
	 * Slice given number of events from list, with exception when all
	 * events from last day shall be included.
	 *
	 * @param array $events   List of events to slice
	 * @param int   $limit    Number of events to slice-off
	 * @param bool  $last_day Set to true to include all events from last day ignoring {$limit}
	 *
	 * @return array Sliced events list
	 */
	protected function _limit_result_set(
		array $events,
		$limit,
		$last_day
	) {
		$limited_events     = array();
		$start_day_previous = 0;
		foreach ( $events as $event ) {
			$start_day = date(
				'Y-m-d',
				$event['start']
			);
			--$limit; // $limit = $limit - 1;
			if ( $limit < 0 ) {
				if ( true === $last_day ) {
					if ( $start_day != $start_day_previous ) {
						break;
					}
				} else {
					break;
				}
			}
			$limited_events[]   = $event;
			$start_day_previous = $start_day;
		}
		return $limited_events;
	}

	/**
	 * _get_post_status_sql function
	 *
	 * Returns SQL snippet for properly matching event posts, as well as array
	 * of arguments to pass to $this_dbi->prepare, in function argument
	 * references.
	 * Nothing is returned by the function.
	 *
	 * @return array An array containing post_status_where: the sql string,
	 * args: the arguments for prepare()
	 */
	protected function _get_post_status_sql() {
		global $current_user;

		$args = array();

		// Query the correct post status
		if (
			current_user_can( 'administrator' ) ||
			current_user_can( 'editor' )
		) {
			// User has privilege of seeing all published and private
			$post_status_where = 'AND post_status IN ( %s, %s ) ';
			$args[]            = 'publish';
			$args[]            = 'private';
		} elseif ( is_user_logged_in() ) {
			// User has privilege of seeing all published and only their own
			// private posts.

			// get user info
			get_currentuserinfo();

			// include post_status = published
			//   OR
			// post_status = private AND post_author = userID
			$post_status_where =
				'AND ( ' .
				'post_status = %s ' .
				'OR ( post_status = %s AND post_author = %d ) ' .
				') ';

			$args[] = 'publish';
			$args[] = 'private';
			$args[] = $current_user->ID;
		} else {
			// User can only see published posts.
			$post_status_where = 'AND post_status = %s ';
			$args[]            = 'publish';
		}

		return array(
			'post_status_where' => $post_status_where,
			'args'              => $args
		);
	}

	/**
	 * Take filter and return SQL options.
	 *
	 * Takes an array of filtering options and turns it into JOIN and WHERE
	 * statements for running an SQL query limited to the specified options.
	 *
	 * @param array $filter Array of filters for the events returned:
	 *                          ['cat_ids']   => list of category IDs
	 *                          ['tag_ids']   => list of tag IDs
	 *                          ['post_ids']  => list of event post IDs
	 *                          ['auth_ids']  => list of event author IDs
	 *
	 * @return array The modified filter array to having:
	 *                   ['filter_join']  the Join statements for the SQL
	 *                   ['filter_where'] the Where statements for the SQL
	 */
	protected function _get_filter_sql( $filter ) {
		$filter_join = $filter_where = array();
		foreach ( $filter as $filter_type => $filter_ids ) {
			$filter_object = null;
			try {
				if ( empty( $filter_ids ) ) {
					$filter_ids = array();
				}
				$filter_object = $this->_registry->get(
					'model.filter.' . $filter_type,
					$filter_ids
				);
				if ( ! ( $filter_object instanceof Ai1ec_Filter_Interface ) ) {
					throw new Ai1ec_Bootstrap_Exception(
						'Filter \'' . get_class( $filter_object ) .
						'\' is not instance of Ai1ec_Filter_Interface'
					);
				}
			} catch ( Ai1ec_Bootstrap_Exception $exception ) {
				continue;
			}
			$filter_join[]  = $filter_object->get_join();
			$filter_where[] = $filter_object->get_where();
		}

		$filter_join  = array_filter( $filter_join );
		$filter_where = array_filter( $filter_where );
		$filter_join  = join( ' ', $filter_join );
		if ( count( $filter_where ) > 0 ) {
			$operator     = $this->get_distinct_types_operator();
			$filter_where = $operator . '( ' .
				implode( ' ) ' . $operator . ' ( ', $filter_where ) .
				' ) ';
		} else {
			$filter_where = '';
		}

		return $filter + compact( 'filter_where', 'filter_join' );
	}

	/**
	 * Get operator for joining distinct filters in WHERE.
	 *
	 * @return string SQL operator.
	 */
	public function get_distinct_types_operator() {
		static $operators = array( 'AND' => 1, 'OR' => 2 );
		$default          = key( $operators );
		$where_operator   = strtoupper( trim( (string)apply_filters(
			'ai1ec_filter_distinct_types_logic',
			$default
		) ) );
		if ( ! isset( $operators[$where_operator] ) ) {
			$where_operator = $default;
		}
		return $where_operator;
	}

}