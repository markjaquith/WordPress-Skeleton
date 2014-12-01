<?php

/**
 * Event instance management model.
 *
 *
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.Model
 */
class Ai1ec_Event_Instance extends Ai1ec_Base {

	/**
	 * @var Ai1ec_Dbi Instance of database abstraction.
	 */
	protected $_dbi = null;

	/**
	 * Store locally instance of Ai1ec_Dbi.
	 *
	 * @param Ai1ec_Registry_Object $registry Injected object registry.
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$this->_dbi = $this->_registry->get( 'dbi.dbi' );
	}

	/**
	 * Remove entries for given post. Optionally delete particular instance.
	 *
	 * @param int      $post_id     Event ID to remove instances for.
	 * @param int|null $instance_id Instance ID, or null for all.
	 *
	 * @return int|bool Number of entries removed, or false on failure.
	 */
	public function clean( $post_id, $instance_id = null ) {
		$where  = array( 'post_id' => $post_id );
		$format = array( '%d' );
		if ( null !== $instance_id ) {
			$where['id'] = $instance_id;
			$format[]    = '%d';
		}
		return $this->_dbi->delete( 'ai1ec_event_instances', $where, $format );
	}

	/**
	 * Remove and then create instance entries for given event.
	 *
	 * @param Ai1ec_Event $event Instance of event to recreate entries for.
	 *
	 * @return bool Success.
	 */
	public function recreate( Ai1ec_Event $event ) {
		$this->clean( $event->get( 'post_id' ) );
		return ( false !== $this->create( $event ) );
	}

	/**
	 * Create list of recurrent instances.
	 *
	 * @param Ai1ec_Event $event          Event to generate instances for.
	 * @param array       $event_instance First instance contents.
	 * @param int         $_start         Timestamp of first occurence.
	 * @param int         $duration       Event duration in seconds.
	 * @param string      $timezone       Target timezone.
	 *
	 * @return array List of event instances.
	 */
	public function create_instances_by_recurrence(
		Ai1ec_Event $event,
		array $event_instance,
		$_start,
		$duration,
		$timezone
	) {
		$recurrence_parser = $this->_registry->get( 'recurrence.rule' );
		$evs               = array();

		$startdate = array(
			'timestamp' => $_start,
			'tz'        => $timezone,
		);
		$start			   = $event_instance['start'];
		$wdate             = $startdate = $enddate
		                   = iCalUtilityFunctions::_timestamp2date( $startdate, 6 );
		$enddate['year']   = $enddate['year'] + 3;
		$exclude_dates	   = array();
		$recurrence_dates  = array();
		if ( $event->get( 'exception_rules' ) ) {
			// creat an array for the rules
			$exception_rules = $recurrence_parser
				->build_recurrence_rules_array(
					$event->get( 'exception_rules' )
				);
			$exception_rules = iCalUtilityFunctions::_setRexrule(
				$exception_rules
			);
			$result = array();
			// The first array is the result and it is passed by reference
			iCalUtilityFunctions::_recur2date(
				$exclude_dates,
				$exception_rules,
				$wdate,
				$startdate,
				$enddate
			);
		}
		$recurrence_rules = $recurrence_parser
			->build_recurrence_rules_array(
				$event->get( 'recurrence_rules' )
			);

		$recurrence_rules = iCalUtilityFunctions::_setRexrule( $recurrence_rules );
		iCalUtilityFunctions::_recur2date(
			$recurrence_dates,
			$recurrence_rules,
			$wdate,
			$startdate,
			$enddate
		);

		
		$recurrence_dates = array_keys( $recurrence_dates );
		// Add the instances
		foreach ( $recurrence_dates as $date ) {

			// The arrays are in the form timestamp => true so an isset call is what we need
			if ( isset( $exclude_dates[$date] ) ) {
				continue;
			}
			$event_instance['start'] = $date;
			$event_instance['end']	 = $date + $duration;
			$excluded	= false;

			// Check if exception dates match this occurence
			if ( $exception_dates = $event->get( 'exception_dates' ) ) {
				$match_exdates = $this->date_match_exdates(
					$date,
					$exception_dates,
					$timezone
				);
				if ( $match_exdates ) {
					$excluded = true;
				}
			}

			// Add event only if it is not excluded
			if ( false === $excluded ) {
				$evs[] = $event_instance;
			}
		}

		return $evs;
	}

	/**
	 * Generate and store instance entries in database for given event.
	 *
	 * @param Ai1ec_Event $event Instance of event to create entries for.
	 *
	 * @return bool Success.
	 */
	public function create( Ai1ec_Event $event ) {
        $evs = array();
        $e	 = array(
            'post_id' => $event->get( 'post_id' ),
            'start'   => $event->get( 'start'   )->format_to_gmt(),
            'end'     => $event->get( 'end'     )->format_to_gmt(),
        );
        $duration = $event->get( 'end' )->diff_sec( $event->get( 'start' ) );

        // Always cache initial instance
        $evs[] = $e;

        $_start = $event->get( 'start' )->format_to_gmt();
        $_end   = $event->get( 'end'   )->format_to_gmt();

        if ( $event->get( 'recurrence_rules' ) ) {
			$_restore_default_tz = date_default_timezone_get();
			$start_timezone      = $event->get( 'start' )->get_timezone();
			date_default_timezone_set( $start_timezone );
			$evs = array_merge(
				$evs,
				$this->create_instances_by_recurrence(
					$event,
					$e,
					$_start,
					$duration,
					$start_timezone
				)
			);
			date_default_timezone_set( $_restore_default_tz );
			unset( $_restore_default_tz );
        }

        // Make entries unique (sometimes recurrence generator creates duplicates?)
        $evs_unique = array();
        foreach ( $evs as $ev ) {
            $evs_unique[md5( serialize( $ev ) )] = $ev;
        }

		$search_helper = $this->_registry->get( 'model.search' );
        foreach ( $evs_unique as $e ) {
            // Find out if this event instance is already accounted for by an
            // overriding 'RECURRENCE-ID' of the same iCalendar feed (by comparing the
            // UID, start date, recurrence). If so, then do not create duplicate
            // instance of event.
            $start             = $e['start'];
			$matching_event_id = null;
			if ( $event->get( 'ical_uid' ) ) {
				$matching_event_id = $search_helper->get_matching_event_id(
					$event->get( 'ical_uid' ),
					$event->get( 'ical_feed_url' ),
					$event->get( 'start' ),
					false,
					$event->get( 'post_id' )
				);
			}

            // If no other instance was found
            if ( null === $matching_event_id ) {
				$this->_dbi->insert(
					'ai1ec_event_instances',
					$e,
					array( '%d', '%d', '%d' )
				);
            }
        }

        return true;
	}

	/**
	 * Check if given date match dates in EXDATES rule.
	 *
	 * @param string $date     Date to check.
	 * @param string $ics_rule ICS EXDATES rule.
	 * @param string $timezone Timezone to evaluate value in.
	 *
	 * @return bool True if given date is in rule.
	 */
    public function date_match_exdates( $date, $ics_rule, $timezone ) {
		$ranges = $this->_get_date_ranges( $ics_rule, $timezone );
        foreach ( $ranges as $interval ) {
			if ( $date >= $interval[0] && $date <= $interval[1] ) {
				return true;
			}
			if ( $date <= $interval[0] ) {
				break;
			}
        }
        return false;
    }

	/**
	 * Prepare date range list for fast exdate search.
	 *
	 * NOTICE: timezone is relevant in only first run.
	 *
	 * @param string $date_list ICS list provided from data model.
	 * @param string $timezone  Timezone in which to evaluate.
	 *
	 * @return array List of date ranges, sorted in increasing order.
	 */
	protected function _get_date_ranges( $date_list, $timezone ) {
		static $ranges = array();
		if ( ! isset( $ranges[$date_list] ) ) {
			$ranges[$date_list] = array();
			$exploded = explode( ',', $date_list );
			sort( $exploded );
			foreach ( $exploded as $date ) {
				// COMMENT on `rtrim( $date, 'Z' )`:
				// user selects exclusion date in event timezone thus it
				// must be parsed as such as opposed to UTC which happen
				// when 'Z' is preserved.
				$date = $this->_registry
					->get( 'date.time', rtrim( $date, 'Z' ), $timezone )
					->format_to_gmt();
				$ranges[$date_list][] = array(
					$date,
					$date + (24 * 60 * 60) - 1
				);
			}
		}
		return $ranges[$date_list];
	}

}