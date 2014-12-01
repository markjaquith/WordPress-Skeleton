<?php

/**
 * Event create/update form backend view layer.
 *
 * Manage creation of boxes (containers) for our control elements
 * and instantiating, as well as updating them.
 *
 * @author       Time.ly Network, Inc.
 * @since        2.0
 * @package      Ai1EC
 * @subpackage   Ai1EC.View
 */
class Ai1ec_View_Add_New_Event extends Ai1ec_Base {

	/**
	 * Create hook to display event meta box when creating or editing an event.
	 *
	 * @wp_hook add_meta_boxes
	 *
	 * @return void
	 */
	public function event_meta_box_container() {
		add_meta_box(
			AI1EC_POST_TYPE,
			Ai1ec_I18n::__( 'Event Details' ),
			array( $this, 'meta_box_view' ),
			AI1EC_POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Create hook to display Banner image meta box.
	 *
	 * @wp_hook add_meta_boxes
	 *
	 * @return void
	 */
	public function event_banner_meta_box_container() {
		if ( ! apply_filters( 'ai1ec_use_banner_image', false ) ) {
			return;
		}
		add_meta_box(
			AI1EC_POST_TYPE. '_banner',
			Ai1ec_I18n::__( 'Banner Image' ),
			array( $this, 'banner_meta_box_view' ),
			AI1EC_POST_TYPE,
			'side',
			'low'
		);
	}

	/**
	 * Add Event Details meta box to the Add/Edit Event screen in the dashboard.
	 *
	 * @return void
	 */
	public function meta_box_view( $post ) {

		$theme_loader         = $this->_registry->get( 'theme.loader' );
		$empty_event          = $this->_registry->get( 'model.event' );

		// ==================
		// = Default values =
		// ==================
		// ATTENTION - When adding new fields to the event remember that you must
		// also set up the duplicate-controller.
		// TODO: Fix this duplication.
		$all_day_event    = '';
		$instant_event    = '';
		$start            = $this->_registry->get( 'date.time' );
		$end              = $this->_registry->get( 'date.time', '+1 hour' );
		$timezone_name    = null;
		$timezones_list   = $this->_registry->get( 'date.timezone' )->get_timezones( true );
		$show_map         = false;
		$google_map       = '';
		$venue            = '';
		$country          = '';
		$address          = '';
		$city             = '';
		$province         = '';
		$postal_code      = '';
		$contact_name     = '';
		$contact_phone    = '';
		$contact_email    = '';
		$contact_url      = '';
		$cost             = '';
		$is_free          = '';
		$rrule            = '';
		$rrule_text       = '';
		$repeating_event  = false;
		$exrule           = '';
		$exrule_text      = '';
		$exclude_event    = false;
		$exdate           = '';
		$show_coordinates = false;
		$longitude        = '';
		$latitude         = '';
		$coordinates      = '';
		$ticket_url       = '';

		$instance_id = false;
		if ( isset( $_REQUEST['instance'] ) ) {
			$instance_id = absint( $_REQUEST['instance'] );
		}
		if ( $instance_id ) {
			add_filter(
				'print_scripts_array',
				array( $this, 'disable_autosave' )
			);
		}

		try {
			// on some php version, nested try catch blocks fail and the exception would never be caught.
			// this is why we use this approach.
			$excpt = null;
			$event = null;
			try {
				$event = $this->_registry->get(
					'model.event',
					get_the_ID(),
					$instance_id
				);
			} catch ( Ai1ec_Event_Not_Found_Exception $excpt ) {
				$ai1ec_localization_helper = $this->_registry
					->get( 'p28n.wpml' );
				$translatable_id = $ai1ec_localization_helper
					->get_translatable_id();
				if ( false !== $translatable_id ) {
					$event = $this->_registry->get(
						'model.event',
						$translatable_id,
						$instance_id
					);
				}
			}
			if ( null !== $excpt ) {
				throw $excpt;
			}

			// Existing event was found. Initialize form values with values from
			// event object.
			$all_day_event    = $event->is_allday()  ? 'checked' : '';
			$instant_event    = $event->is_instant() ? 'checked' : '';

			$start            = $event->get( 'start' );
			$end              = $event->get( 'end' );
			$timezone_name    = $event->get( 'timezone_name' );

			$multi_day        = $event->is_multiday();

			$show_map         = $event->get( 'show_map' );
			$google_map       = $show_map ? 'checked="checked"' : '';

			$show_coordinates = $event->get( 'show_coordinates' );
			$coordinates      = $show_coordinates ? 'checked="checked"' : '';
			$longitude        = (float)$event->get( 'longitude', 0 );
			$latitude         = (float)$event->get( 'latitude',  0 );
			// There is a known bug in Wordpress (https://core.trac.wordpress.org/ticket/15158) that saves 0 to the DB instead of null.
			// We handle a special case here to avoid having the fields with a value of 0 when the user never inputted any coordinates
			if ( ! $show_coordinates ) {
				$longitude = '';
				$latitude  = '';
			}

			$venue            = $event->get( 'venue' );
			$country          = $event->get( 'country' );
			$address          = $event->get( 'address' );
			$city             = $event->get( 'city' );
			$province         = $event->get( 'province' );
			$postal_code      = $event->get( 'postal_code' );
			$contact_name     = $event->get( 'contact_name' );
			$contact_phone    = $event->get( 'contact_phone' );
			$contact_email    = $event->get( 'contact_email' );
			$contact_url      = $event->get( 'contact_url' );
			$cost             = $event->get( 'cost' );
			$ticket_url       = $event->get( 'ticket_url' );
			$rrule            = $event->get( 'recurrence_rules' );
			$exrule           = $event->get( 'exception_rules' );
			$exdate           = $event->get( 'exception_dates' );
			$repeating_event  = ! empty( $rrule );
			$exclude_event    = ! empty( $exrule );

			$is_free = '';
			$free = $event->is_free();
			if ( ! empty( $free ) ) {
				$is_free = 'checked="checked" ';
				$cost    = '';
			}

			if ( $repeating_event ) {
				$rrule_text = ucfirst(
					$this->_registry->get( 'recurrence.rule' )
					->rrule_to_text( $rrule )
				);
			}

			if ( $exclude_event ) {
				$exrule_text = ucfirst(
					$this->_registry->get( 'recurrence.rule' )
					->rrule_to_text( $exrule )
				);
			}
		} catch ( Ai1ec_Event_Not_Found_Exception $excpt ) {
			// Event does not exist.
			// Leave form fields undefined (= zero-length strings)
			$event = null;
		}

		// Time zone; display if set.
		$timezone        = '';
		$timezone_string = null;
		$date_timezone   = $this->_registry->get( 'date.timezone' );

		if (
			! empty( $timezone_name ) &&
			$local_name = $date_timezone->get_name( $timezone_name )
		) {
			$timezone_string = $local_name;
		}
		if ( null === $timezone_string ) {
			$timezone_string = $date_timezone->get_default_timezone();
		}

		if ( $timezone_string ) {
			$timezone = $this->_registry->get( 'date.system' )
				->get_gmt_offset_expr( $timezone_string );
		}

		if ( empty( $timezone_name ) ) {
			/**
			 * Actual Olsen timezone name is used when value is to be directly
			 * exposed to user in some mean. It's possible to use named const.
			 * `'sys.default'` only when passing value to date.time library.
			 */
			$timezone_name = $date_timezone->get_default_timezone();
		}

		// This will store each of the accordion tabs' markup, and passed as an
		// argument to the final view.
		$boxes = array();
		$parent_event_id = null;
		if ( $event ) {
			$parent_event_id = $this->_registry->get( 'model.event.parent' )
				->event_parent( $event->get( 'post_id' ) );
		}
		// ===============================
		// = Display event time and date =
		// ===============================
		$args = array(
			'all_day_event'      => $all_day_event,
			'instant_event'      => $instant_event,
			'start'              => $start,
			'end'                => $end,
			'repeating_event'    => $repeating_event,
			'rrule'              => $rrule,
			'rrule_text'         => $rrule_text,
			'exclude_event'      => $exclude_event,
			'exrule'             => $exrule,
			'exrule_text'        => $exrule_text,
			'timezone'           => $timezone,
			'timezone_string'    => $timezone_string,
			'timezone_name'      => $timezone_name,
			'exdate'             => $exdate,
			'parent_event_id'    => $parent_event_id,
			'instance_id'        => $instance_id,
			'timezones_list'     => $timezones_list,
		);

		$boxes[] = $theme_loader
			->get_file( 'box_time_and_date.php', $args, true )
			->get_content();

		// =================================================
		// = Display event location details and Google map =
		// =================================================
		$args = array(
			'select_venue'     => apply_filters( 'ai1ec_admin_pre_venue_html', '' ),
			'save_venue'       => apply_filters( 'ai1ec_admin_post_venue_html', '' ),
			'venue'            => $venue,
			'country'          => $country,
			'address'          => $address,
			'city'             => $city,
			'province'         => $province,
			'postal_code'      => $postal_code,
			'google_map'       => $google_map,
			'show_map'         => $show_map,
			'show_coordinates' => $show_coordinates,
			'longitude'        => $longitude,
			'latitude'         => $latitude,
			'coordinates'      => $coordinates,
		);
		$boxes[] = $theme_loader
			->get_file( 'box_event_location.php', $args, true )
			->get_content();

		// ======================
		// = Display event cost =
		// ======================
		$args = array(
			'cost'       => $cost,
			'is_free'    => $is_free,
			'ticket_url' => $ticket_url,
			'event'      => $empty_event,
		);
		$boxes[] = $theme_loader
			->get_file( 'box_event_cost.php', $args, true )
			->get_content();



		// =========================================
		// = Display organizer contact information =
		// =========================================
		$args = array(
			'contact_name'    => $contact_name,
			'contact_phone'   => $contact_phone,
			'contact_email'   => $contact_email,
			'contact_url'     => $contact_url,
			'event'           => $empty_event,
		);
		$boxes[] = $theme_loader
			->get_file( 'box_event_contact.php', $args, true )
			->get_content();

		// ==========================
		// = Parent/Child relations =
		// ==========================
		if ( $event ) {
			$parent   = $this->_registry->get( 'model.event.parent' )
				->get_parent_event( $event->get( 'post_id' ) );
			if ( $parent ) {
				try {
					$parent = $this->_registry->get( 'model.event', $parent );
				} catch ( Ai1ec_Event_Not_Found_Exception $exception ) { // ignore
					$parent = null;
				}
			}
			if ( $parent ) {
				$children = $this->_registry->get( 'model.event.parent' )
					->get_child_event_objects( $event->get( 'post_id' ) );
				$args = compact( 'parent', 'children' );
				$args['registry'] = $this->_registry;

				$boxes[] = $theme_loader->get_file(
					'box_event_children.php',
					$args,
					true
				)->get_content();
			}

		}

		$boxes = apply_filters( 'ai1ec_add_new_event_boxes', $boxes, $event );
		// Display the final view of the meta box.
		$args = array(
			'boxes'          => $boxes,
		);
		echo $theme_loader
			->get_file( 'add_new_event_meta_box.php', $args, true )
			->get_content();
	}

	/**
	 * Add Banner Image meta box to the Add/Edit Event.
	 *
	 * @return void
	 */
	public function banner_meta_box_view( $post ) {
		$banner_image_meta = get_post_meta( $post->ID, 'ai1ec_banner_image' );
		$theme_loader      = $this->_registry->get( 'theme.loader' );
		$args = array(
			'src'         => $banner_image_meta && $banner_image_meta[0]
				? $banner_image_meta[0] : false,
			'set_text'    => Ai1ec_I18n::__( 'Set banner image' ),
			'remove_text' => Ai1ec_I18n::__( 'Remove banner image' ),

		);
		echo $theme_loader
			->get_file( 'banner-image.twig', $args, true )
			->get_content();
	}

	/**
	 * disable_autosave method
	 *
	 * Callback to disable autosave script
	 *
	 * @param array $input List of scripts registered
	 *
	 * @return array Modified scripts list
	 */
	public function disable_autosave( array $input ) {
		wp_deregister_script( 'autosave' );
		$autosave_key = array_search( 'autosave', $input );
		if ( false === $autosave_key || ! is_scalar( $autosave_key ) ) {
			unset( $input[$autosave_key] );
		}
		return $input;
	}

	/**
	 * Renders Bootstrap inline alert.
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void Method does not return.
	 */
	public function event_inline_alert( $post ) {
		if (
			! isset( $post->post_type ) ||
			AI1EC_POST_TYPE != $post->post_type
		) {
			return;
		}
		$theme_loader = $this->_registry->get( 'theme.loader' );
		echo $theme_loader->get_file( 'box_inline_warning.php', null, true )
			->get_content();
	}

}
