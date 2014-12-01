<?php

/**
 * Model representing an event or an event instance.
 *
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @instantiator Ai1ec_Factory_Event.create_event_instance
 * @package      Ai1EC
 * @subpackage   Ai1EC.Model
 */
class Ai1ec_Event extends Ai1ec_Base {

	/**
	 * @var Ai1ec_Event_Entity Data store object reference.
	 */
	protected $_entity    = null;

	/**
	 * @var array Map of fields that require special care during set/get
	 *            operations. Values have following meanings:
	 *            [0]  - both way care required;
	 *            [1]  - only `set` operations require care;
	 *            [-1] - only `get` (for storage) operations require care.
	 */
	protected $_swizzable = array(
		'contact_url'   => -1, // strip on save/import
		'cost'          => 0,
		'ticket_url'    => -1, // strip on save/import
		'start'         => -1,
		'end'           => -1,
		'timezone_name' => -1,
	);

	/**
	 * @var array Runtime properties
	 */
	protected $_runtime_props = array();

	/**
	 * @var bool|null Boolean cache-definition indicating if event is multiday.
	 */
	protected $_is_multiday = null;

	/**
	 * Wrapper to get property value.
	 *
	 * @param string $property Name of property to get.
	 * @param mixed  $default  Default value to return.
	 *
	 * @return mixed Actual property.
	 */
	public function get( $property, $default = null ) {
		return $this->_entity->get( $property, $default );
	}

	/**
	 * Get properties generated at runtime
	 *
	 * @param string $property
	 *
	 * @return string
	 */
	public function get_runtime( $property, $default = '' ) {
		return isset( $this->_runtime_props[$property] ) ?
			$this->_runtime_props[$property] :
			$default;
	}

	/**
	 * Set properties generated at runtime
	 *
	 * @param string $property
	 * @param string $value
	 */
	public function set_runtime( $property, $value ) {
		$this->_runtime_props[$property] = $value;
	}

	/**
	 * Handle property initiation.
	 *
	 * Decides, how to extract value stored in permanent storage.
	 *
	 * @param string $property Name of property to handle
	 * @param mixed  $value    Value, read from permanent storage
	 *
	 * @return bool Success
	 */
	public function set( $property, $value ) {
		if (
			isset( $this->_swizzable[$property] ) &&
			$this->_swizzable[$property] >= 0
		) {
			$method = '_handle_property_construct_' . $property;
			$value  = $this->{$method}( $value );
		}
		$this->_entity->set( $property, $value );
		return $this;
	}

	/**
	 * Set the event is all day, during the specified number of days
	 *
	 * @param number $length
	 */
	public function set_all_day( $length = 1 ) {
		// set allday as true
		$this->set( 'allday', true );
		$start = $this->get( 'start' );
		// reset time component
		$start->set_time( 0, 0, 0 );
		$end = $this->_registry->get( 'date.time', $start );
		// set the correct length
		$end->adjust_day( $length );
		$this->set( 'end', $end );
	}

	/**
	 * Set the event as if it has no end time
	 */
	public function set_no_end_time() {
		$this->set( 'instant_event', true );
		$start = $this->get( 'start' );
		$end   = $this->_registry->get( 'date.time', $start );
		$end->set_time(
			$start->format( 'H' ),
			$start->format( 'i' ) + 30,
			$start->format( 's' )
		);
		$this->set( 'end', $end );
	}

	/**
	 * Set object fields from arbitrary array.
	 *
	 * @param array $data Supposedly map of fields to initiate.
	 *
	 * @return Ai1ec_Event Instance of self for chaining.
	 */
	public function initialize_from_array( array $data ) {

		// =======================================================
		// = Assign each event field the value from the database =
		// =======================================================
		foreach ( $this->_entity->list_properties() as $property ) {
			if ( 'post' !== $property && isset( $data[$property] ) ) {
				$this->set( $property, $data[$property] );
				unset( $data[$property] );
			}
		}
		if ( isset( $data['post'] ) ) {
			$this->set( 'post', (object)$data['post'] );
		} else {
			// ========================================
			// = Remaining fields are the post fields =
			// ========================================
			$this->set( 'post', (object)$data );
		}
		return $this;
	}

	/**
	 * Initialize object from ID.
	 *
	 * Attempts to retrieve entity from database and if succeeds - uses
	 * {@see self::initialize_from_array} to initiate actual values.
	 *
	 * @param int      $post_id  ID of post (event) to initiate.
	 * @param int|bool $instance ID of event instance, false for base event.
	 *
	 * @return Ai1ec_Event Instance of self for chaining.
	 *
	 * @throws Ai1ec_Event_Not_Found_Exception If entity is not locatable.
	 */
	public function initialize_from_id( $post_id, $instance = false ) {
		$post = get_post( $post_id );
		if ( ! $post || $post->post_status == 'auto-draft' ) {
			throw new Ai1ec_Event_Not_Found_Exception(
				'Post with ID \'' . $post_id .
				'\' could not be retrieved from the database.'
			);
		}
		$post_id = (int)$post_id;
		$dbi     = $this->_registry->get( 'dbi.dbi' );

		$left_join  = '';
		$select_sql = '
			e.post_id,
			e.timezone_name,
			e.recurrence_rules,
			e.exception_rules,
			e.allday,
			e.instant_event,
			e.recurrence_dates,
			e.exception_dates,
			e.venue,
			e.country,
			e.address,
			e.city,
			e.province,
			e.postal_code,
			e.show_map,
			e.contact_name,
			e.contact_phone,
			e.contact_email,
			e.contact_url,
			e.cost,
			e.ticket_url,
			e.ical_feed_url,
			e.ical_source_url,
			e.ical_organizer,
			e.ical_contact,
			e.ical_uid,
			e.longitude,
			e.latitude,
			e.show_coordinates,
			GROUP_CONCAT( ttc.term_id ) AS categories,
			GROUP_CONCAT( ttt.term_id ) AS tags
		';

		if ( false !== $instance && is_numeric( $instance ) ) {
			$select_sql .= ', IF( aei.start IS NOT NULL, aei.start, e.start ) as start,' .
						   '  IF( aei.start IS NOT NULL, aei.end,   e.end )   as end ';

			$instance = (int)$instance;
			$this->set( 'instance_id', $instance );
			$left_join = 'LEFT JOIN ' . $dbi->get_table_name( 'ai1ec_event_instances' ) .
				' aei ON aei.id = ' . $instance . ' AND e.post_id = aei.post_id ';
		} else {
			$select_sql .= ', e.start as start, e.end as end, e.allday ';
		}

		// =============================
		// = Fetch event from database =
		// =============================
		$query = 'SELECT ' . $select_sql . '
			FROM ' . $dbi->get_table_name( 'ai1ec_events' ) . ' e
				LEFT JOIN ' .
					$dbi->get_table_name( 'term_relationships' ) . ' tr
					ON ( e.post_id = tr.object_id )
				LEFT JOIN ' . $dbi->get_table_name( 'term_taxonomy' ) . ' ttc
					ON (
						tr.term_taxonomy_id = ttc.term_taxonomy_id AND
						ttc.taxonomy = \'events_categories\'
					)
				LEFT JOIN ' . $dbi->get_table_name( 'term_taxonomy' ) . ' ttt
					ON (
						tr.term_taxonomy_id = ttt.term_taxonomy_id AND
						ttt.taxonomy = \'events_tags\'
					)
				' . $left_join . '
			WHERE e.post_id = ' . $post_id . '
			GROUP BY e.post_id';

		$event = $dbi->get_row( $query, ARRAY_A );
		if ( null === $event || null === $event['post_id'] ) {
			throw new Ai1ec_Event_Not_Found_Exception(
				'Event with ID \'' . $post_id .
				'\' could not be retrieved from the database.'
			);
		}

		$event['post'] = $post;
		return $this->initialize_from_array( $event );
	}

	/**
	 * Create new event object, using provided data for initialization.
	 *
	 * @param Ai1ec_Registry_Object $registry  Injected object registry.
	 * @param int|array|null        $data      Look up post with id $data, or
	 *                                         initialize fields with associative
	 *                                         array $data containing both post
	 *                                         and event fields.
	 * @param bool                  $instance  Optionally instance ID.
	 *
	 * @throws Ai1ec_Invalid_Argument_Exception When $data is not one
	 *                                          of int|array|null.
	 * @throws Ai1ec_Event_Not_Found_Exception  When $data relates to
	 *                                          non-existent ID.
	 *
	 * @return void
	 */
	function __construct(
		Ai1ec_Registry_Object $registry,
		$data     = null,
		$instance = false
	) {
		parent::__construct( $registry );
		$this->_entity = $this->_registry->get( 'model.event.entity' );
		if ( null === $data ) {
			return; // empty object
		} else if ( is_numeric( $data ) ) {
			$this->initialize_from_id( $data, $instance );
		} else if ( is_array( $data ) ) {
			$this->initialize_from_array( $data );
		} else {
			throw new Ai1ec_Invalid_Argument_Exception(
				'Argument to constructor must be integer, array or null' .
				', not ' . var_export( $data, true )
			);
		}

		if ( $this->is_allday() ) {
			try {
				$timezone = $this->_registry->get( 'date.timezone' )
					->get( $this->get( 'timezone_name' ) );
				$this->_entity->set_preferred_timezone( $timezone );
			} catch ( Exception $excpt ) {
				//  ignore
			}
		}
	}

	/**
	 * Restore original URL from loggable event URL
	 *
	 * @param string $value URL as seen by visitor
	 *
	 * @return string Original URL
	 */
	public function get_nonloggable_url( $value ) {
		if (
			empty( $value ) ||
			false === strpos( $value, AI1EC_REDIRECTION_SERVICE )
		) {
			return $value;
		}
		$decoded = json_decode(
			base64_decode(
				trim(
					substr( $value, strlen( AI1EC_REDIRECTION_SERVICE ) ),
					'/'
				)
			),
			true
		);
		if ( ! isset( $decoded['l'] ) ) {
			return '';
		}
		return $decoded['l'];
	}

	/**
	 * Twig method for retrieving avatar.
	 *
	 * @param  bool   $wrap_permalink Whether to wrap avatar in <a> element or not
	 *
	 * @return string Avatar markup
	 */
	public function getavatar( $wrap_permalink = true ) {
		return $this->_registry->
			get( 'view.event.avatar' )->get_event_avatar(
				$this,
				$this->_registry->get( 'view.calendar.fallbacks' )->get_all(),
				'',
				$wrap_permalink
			);
	}

	/**
	 * Returns whether Event has geo information.
	 *
	 * @return bool True or false.
	 */
	public function has_geoinformation() {
		$latitude  = floatval( $this->get( 'latitude') );
		$longitude = floatval( $this->get( 'longitude' ) );
		return (
			(
				$latitude >= 0.000000000000001 ||
				$latitude <= -0.000000000000001
			) &&
			(
				$longitude >= 0.000000000000001 ||
				$longitude <= -0.000000000000001
			)
		);
	}

	/**
	 * Handle `cost` value reading from permanent storage.
	 *
	 * @param string $value Value stored in permanent storage
	 *
	 * @return bool Success: true, always
	 */
	protected function _handle_property_construct_cost( $value ) {
		$test_value = false;
		if (
			isset( $value{1} ) && (
				':' === $value{1} || ';' === $value{1}
			)
		) {
			$test_value = unserialize( $value );
		}
		$cost = $is_free = NULL;
		if ( false === $test_value ) {
			$cost    = trim( $value );
			$is_free = false;
		} else {
			extract( $test_value, EXTR_IF_EXISTS );
		}
		$this->_entity->set( 'is_free', (bool)$is_free );
		return (string)$cost;
	}

	/**
	 * Get UID to be used for current event.
	 *
	 * The generated format is cached in static variable within this function
	 * to re-use when generating UIDs for different entries.
	 *
	 * @return string Generated UID.
	 *
	 * @staticvar string $format Cached format.
	 */
	public function get_uid() {
		static $format = null;
		if ( null === $format ) {
			$site_url = parse_url( get_site_url() );
			$format   = 'ai1ec-%d@' . $site_url['host'];
			if ( isset( $site_url['path'] ) ) {
				$format .= $site_url['path'];
			}
		}
		return sprintf( $format, $this->get( 'post_id' ) );
	}

	/**
	 * Check if event is free.
	 *
	 * @return bool Free status.
	 */
	public function is_free() {
		return (bool)$this->get( 'is_free' );
	}

	/**
	 * Check if event is taking all day.
	 *
	 * @return bool True for all-day long events.
	 */
	public function is_allday() {
		return (bool)$this->get( 'allday' );
	}

	/**
	 * Check if event has virtually no time.
	 *
	 * @return bool True for instant events.
	 */
	public function is_instant() {
		return (bool)$this->get( 'instant_event' );
	}

	/**
	 * Check if event is taking multiple days.
	 *
	 * Uses object-wide variable {@see self::$_is_multiday} to store
	 * calculated value after first call.
	 *
	 * @return bool True for multiday events.
	 */
	public function is_multiday() {
		if ( null === $this->_is_multiday ) {
			$start = $this->get( 'start' );
			$end   = $this->get( 'end' );
			$diff  = $end->diff_sec( $start );
			$this->_is_multiday = $diff > 86400 &&
				$start->format( 'Y-m-d' ) !== $end->format( 'Y-m-d' );
		}
		return $this->_is_multiday;
	}

	/**
	 * Get the duration of the event
	 *
	 * @return number
	 */
	public function get_duration() {
		$duration = $this->get_runtime( 'duration', null );
		if ( null === $duration ) {
			$duration = $this->get( 'end' )->format() -
				$this->get( 'start' )->format();
			$this->set_runtime( 'duration', $duration );
		}
		return $duration;
	}

	/**
	 * Create/update entity representation.
	 *
	 * Saves the current event data to the database. If $this->post_id exists,
	 * but $update is false, creates a new record in the ai1ec_events table of
	 * this event data, but does not try to create a new post. Else if $update
	 * is true, updates existing event record. If $this->post_id is empty,
	 * creates a new post AND record in the ai1ec_events table for this event.
	 *
	 * @param  bool  $update  Whether to update an existing event or create a
	 *                        new one
	 * @return int            The post_id of the new or existing event.
	 */
	function save( $update = false ) {
		do_action( 'ai1ec_pre_save_event', $this, $update );
		if ( ! $update ) {
			$response = apply_filters( 'ai1ec_event_save_new', $this );
			if ( is_wp_error( $response ) ) {
				throw new Ai1ec_Event_Create_Exception(
					'Failed to create event: ' . $response->get_error_message()
				);
			}
		}

		$dbi        = $this->_registry->get( 'dbi.dbi' );
		$columns    = $this->prepare_store_entity();
		$format     = $this->prepare_store_format( $columns );
		$table_name = $dbi->get_table_name( 'ai1ec_events' );
		$post_id    = $columns['post_id'];

		if ( $this->get( 'end' )->is_empty() ) {
			$this->set_no_end_time();
		}
		if ( $post_id ) {
			$success = false;
			if ( ! $update ) {
				$success = $dbi->insert(
					$table_name,
					$columns,
					$format
				);
			} else {
				$success = $dbi->update(
					$table_name,
					$columns,
					array( 'post_id' => $columns['post_id'] ),
					$format,
					array( '%d' )
				);
			}
			if ( false === $success ) {
				return false;
			}

		} else {
			// ===================
			// = Insert new post =
			// ===================
			$post_id = wp_insert_post( $this->get( 'post' ), false );
			if ( 0 === $post_id ) {
				return false;
			}
			$this->set( 'post_id', $post_id );
			$columns['post_id'] = $post_id;

			$taxonomy = $this->_registry->get(
				'model.event.taxonomy',
				$post_id
			);
			$taxonomy->set_categories( $this->get( 'categories' ) );
			$taxonomy->set_tags(       $this->get( 'tags' ) );

			if (
				$feed = $this->get( 'feed' ) &&
				isset( $feed->feed_id )
			) {
				$taxonomy->set_feed( $feed );
			}

			// =========================
			// = Insert new event data =
			// =========================
			if ( false === $dbi->insert( $table_name, $columns, $format ) ) {
				return false;
			}
		}

		// give other plugins / extensions the ability to do things
		// when saving, like fetching authors which i removed as it's not core.
		do_action( 'ai1ec_save_event' );

		$instance_model = $this->_registry->get( 'model.event.instance' );
		$instance_model->recreate( $this );

		do_action( 'ai1ec_event_saved', $post_id, $this, $update );
		return $post_id;
	}

	/**
	 * Prepare fields format flags to use in database operations.
	 *
	 * NOTICE: parameter $entity is ignored as of now.
	 *
	 * @param array $entity Serialized entity to prepare flags for.
	 *
	 * @return array List of format flags to use in integrations with DBI.
	 */
	public function prepare_store_format( array $entity ) {
		// ===============================================================
		// ====== Sample implementation to follow method signature: ======
		// ===============================================================
		// static $format = array(
		// 	'post_id'       => '%d',
		// 	'start'         => '%d',
		// 	'end'           => '%d',
		// 	'timezone_name' => '%s',
		// 	// other keys to follow...
		// );
		// return array_values( array_intersect_key( $format, $entity ) );
		// ===============================================================
		$format = array(
			'%d',  // post_id
			'%d',  // start
			'%d',  // end
			'%s',  // timezone_name
			'%d',  // allday
			'%d',  // instant_event
			'%s',  // recurrence_rules
			'%s',  // exception_rules
			'%s',  // recurrence_dates
			'%s',  // exception_dates
			'%s',  // venue
			'%s',  // country
			'%s',  // address
			'%s',  // city
			'%s',  // province
			'%s',  // postal_code
			'%d',  // show_map
			'%s',  // contact_name
			'%s',  // contact_phone
			'%s',  // contact_email
			'%s',  // contact_url
			'%s',  // cost
			'%s',  // ticket_url
			'%s',  // ical_feed_url
			'%s',  // ical_source_url
			'%s',  // ical_uid
			'%d',  // show_coordinates
			'%f',  // latitude
			'%f',  // longitude
		);
		return $format;
	}

	/**
	 * Prepare event entity {@see self::$_entity} for persistent storage.
	 *
	 * Creates an array of database fields and corresponding values.
	 *
	 * @return array Map of fields to store.
	 */
	public function prepare_store_entity() {
		$entity = array(
			'post_id'          => $this->storage_format( 'post_id' ),
			'start'            => $this->storage_format( 'start' ),
			'end'              => $this->storage_format( 'end' ),
			'timezone_name'    => $this->storage_format( 'timezone_name' ),
			'allday'           => $this->storage_format( 'allday' ),
			'instant_event'    => $this->storage_format( 'instant_event' ),
			'recurrence_rules' => $this->storage_format( 'recurrence_rules' ),
			'exception_rules'  => $this->storage_format( 'exception_rules' ),
			'recurrence_dates' => $this->storage_format( 'recurrence_dates' ),
			'exception_dates'  => $this->storage_format( 'exception_dates' ),
			'venue'            => $this->storage_format( 'venue' ),
			'country'          => $this->storage_format( 'country' ),
			'address'          => $this->storage_format( 'address' ),
			'city'             => $this->storage_format( 'city' ),
			'province'         => $this->storage_format( 'province' ),
			'postal_code'      => $this->storage_format( 'postal_code' ),
			'show_map'         => $this->storage_format( 'show_map' ),
			'contact_name'     => $this->storage_format( 'contact_name' ),
			'contact_phone'    => $this->storage_format( 'contact_phone' ),
			'contact_email'    => $this->storage_format( 'contact_email' ),
			'contact_url'      => $this->storage_format( 'contact_url' ),
			'cost'             => $this->storage_format( 'cost' ),
			'ticket_url'       => $this->storage_format( 'ticket_url' ),
			'ical_feed_url'    => $this->storage_format( 'ical_feed_url' ),
			'ical_source_url'  => $this->storage_format( 'ical_source_url' ),
			'ical_uid'         => $this->storage_format( 'ical_uid' ),
			'show_coordinates' => $this->storage_format( 'show_coordinates' ),
			'latitude'         => $this->storage_format( 'latitude',  '' ),
			'longitude'        => $this->storage_format( 'longitude', '' ),
		);
		return $entity;
	}

	/**
	 * Compact field for writing to persistent storage.
	 *
	 * @param string $field   Name of field to compact.
	 * @param mixed  $default Default value to use for undescribed fields.
	 *
	 * @return mixed Value or $default.
	 */
	public function storage_format( $field, $default = null ) {
		$value = $this->_entity->get( $field, $default );
		if (
			isset( $this->_swizzable[$field] ) &&
			$this->_swizzable[$field] <= 0
		) {
			$value = $this->{ '_handle_property_destruct_' . $field }( $value );
		}
		return $value;
	}

	/**
	 * Allow properties to be modified after cloning.
	 *
	 * @return void
	 */
	public function __clone() {
		$this->_entity = clone $this->_entity;
	}

	/**
	 * Decode timezone to use for event.
	 *
	 * Following algorythm is used to detect a value:
	 *     - take value provided in input;
	 *     - if empty - take value associated with start time;
	 *     - if empty - take current environment timezone.
	 *
	 * @param string $timezone_name Timezone provided in input.
	 *
	 * @return string Timezone name to use for event in future.
	 */
	protected function _handle_property_destruct_timezone_name(
		$timezone_name
	) {
		if ( empty( $timezone_name ) ) {
			$timezone_name = $this->get( 'start' )->get_timezone();
			if ( empty( $timezone_name ) ) {
				$timezone_name = $this->_registry->get( 'date.timezone' )
					->get_default_timezone();
			}
		}
		return $timezone_name;
	}

	/**
	 * Store `Ticket URL` in non-loggable form
	 *
	 * @param string $ticket_url URL for buying tickets.
	 *
	 * @return string Non loggable URL
	 */
	protected function _handle_property_destruct_ticket_url( $ticket_url ) {
		return $this->get_nonloggable_url( $ticket_url );
	}

	/**
	 * Format datetime to UNIX timestamp for storage.
	 *
	 * @param Ai1ec_Date_Time $start Datetime object to compact.
	 *
	 * @return int UNIX timestamp.
	 */
	protected function _handle_property_destruct_start( Ai1ec_Date_Time $start ) {
		return $start->format_to_gmt();
	}

	/**
	 * Format datetime to UNIX timestamp for storage.
	 *
	 * @param Ai1ec_Date_Time $end Datetime object to compact.
	 *
	 * @return int UNIX timestamp.
	 */
	protected function _handle_property_destruct_end( Ai1ec_Date_Time $end ) {
		return $end->format_to_gmt();
	}

	/**
	 * Store `Contact URL` in non-loggable form.
	 *
	 * @param string $contact_url URL for contact details.
	 *
	 * @return string Non loggable URL.
	 */
	protected function _handle_property_destruct_contact_url( $contact_url ) {
		return $this->get_nonloggable_url( $contact_url );
	}

	/**
	 * Handle `cost` writing to permanent storage.
	 *
	 * @param string $cost Value of cost.
	 *
	 * @return string Serialized value to store.
	 */
	protected function _handle_property_destruct_cost( $cost ) {
		$cost = array(
			'cost'    => $cost,
			'is_free' => false,
		);
		if ( $this->get( 'is_free' ) ) {
			$cost['is_free'] = true;
		}
		return serialize( $cost );
	}

}
