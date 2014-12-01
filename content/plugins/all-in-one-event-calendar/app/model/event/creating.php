<?php

/**
 * Handles create/update operations.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Model
 */
class Ai1ec_Event_Creating extends Ai1ec_Base {

	/**
	 * Saves meta post data.
	 *
	 * @wp_hook save_post
	 *
	 * @param  int    $post_id Post ID.
	 * @param  object $post    Post object.
	 *
	 * @return object|null Saved Ai1ec_Event object if successful or null.
	 */
	public function save_post( $post_id, $post ) {
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if (
			! isset( $_POST[AI1EC_POST_TYPE] ) ||
			! wp_verify_nonce( $_POST[AI1EC_POST_TYPE], 'ai1ec' )
		) {
			return null;
		}

		if (
			isset( $post->post_status ) &&
			'auto-draft' === $post->post_status
		) {
			return null;
		}

		// verify if this is not inline-editing
		if (
			isset( $_REQUEST['action'] ) &&
			'inline-save' === $_REQUEST['action']
		) {
			return null;
		}

		// verify that the post_type is that of an event
		if ( ! $this->_registry->get( 'acl.aco' )->is_our_post_type( $post ) ) {
			return null;
		}

		/**
		 * =====================================================================
		 *
		 * CHANGE CODE BELLOW TO HAVE FOLLOWING PROPERTIES:
		 * - be initializiable from model;
		 * - have sane defaults;
		 * - avoid that cluster of isset and ternary operator.
		 *
		 * =====================================================================
		 */

		// LABEL:magicquotes
		// remove WordPress `magical` slashes - we work around it ourselves
		$_POST = stripslashes_deep( $_POST );

		$all_day          = isset( $_POST['ai1ec_all_day_event'] )    ? 1                                             : 0;
		$instant_event    = isset( $_POST['ai1ec_instant_event'] )    ? 1                                             : 0;
		$timezone_name    = isset( $_POST['ai1ec_timezone_name'] )    ? $_POST['ai1ec_timezone_name']                 : 'sys.default';
		$start_time       = isset( $_POST['ai1ec_start_time'] )       ? $_POST['ai1ec_start_time']                    : '';
		$end_time         = isset( $_POST['ai1ec_end_time'] )         ? $_POST['ai1ec_end_time']                      : '';
		$venue            = isset( $_POST['ai1ec_venue'] )            ? $_POST['ai1ec_venue']                         : '';
		$address          = isset( $_POST['ai1ec_address'] )          ? $_POST['ai1ec_address']                       : '';
		$city             = isset( $_POST['ai1ec_city'] )             ? $_POST['ai1ec_city']                          : '';
		$province         = isset( $_POST['ai1ec_province'] )         ? $_POST['ai1ec_province']                      : '';
		$postal_code      = isset( $_POST['ai1ec_postal_code'] )      ? $_POST['ai1ec_postal_code']                   : '';
		$country          = isset( $_POST['ai1ec_country'] )          ? $_POST['ai1ec_country']                       : '';
		$google_map       = isset( $_POST['ai1ec_google_map'] )       ? 1                                             : 0;
		$cost             = isset( $_POST['ai1ec_cost'] )             ? $_POST['ai1ec_cost']                          : '';
		$is_free          = isset( $_POST['ai1ec_is_free'] )          ? (bool)$_POST['ai1ec_is_free']                 : false;
		$ticket_url       = isset( $_POST['ai1ec_ticket_url'] )       ? $_POST['ai1ec_ticket_url']                    : '';
		$contact_name     = isset( $_POST['ai1ec_contact_name'] )     ? $_POST['ai1ec_contact_name']                  : '';
		$contact_phone    = isset( $_POST['ai1ec_contact_phone'] )    ? $_POST['ai1ec_contact_phone']                 : '';
		$contact_email    = isset( $_POST['ai1ec_contact_email'] )    ? $_POST['ai1ec_contact_email']                 : '';
		$contact_url      = isset( $_POST['ai1ec_contact_url'] )      ? $_POST['ai1ec_contact_url']                   : '';
		$show_coordinates = isset( $_POST['ai1ec_input_coordinates'] )? 1                                             : 0;
		$longitude        = isset( $_POST['ai1ec_longitude'] )        ? $_POST['ai1ec_longitude']                     : '';
		$latitude         = isset( $_POST['ai1ec_latitude'] )         ? $_POST['ai1ec_latitude']                      : '';
		$banner_image     = isset( $_POST['ai1ec_banner_image'] )     ? $_POST['ai1ec_banner_image']                  : '';

		$rrule  = NULL;
		$exrule = NULL;
		$exdate = NULL;

		// if rrule is set, convert it from local to UTC time
		if (
			isset( $_POST['ai1ec_repeat'] ) &&
			! empty( $_POST['ai1ec_repeat'] )
		) {
			$rrule = $_POST['ai1ec_rrule'];
		}

		// if exrule is set, convert it from local to UTC time
		if (
			isset( $_POST['ai1ec_exclude'] ) &&
			! empty( $_POST['ai1ec_exclude'] ) &&
			NULL !== $rrule // no point for exclusion, if repetition is not set
		) {
			$exrule = $this->_registry->get( 'recurrence.rule' )->merge_exrule(
				$_POST['ai1ec_exrule'],
				$_POST['ai1ec_rrule']
			);
		}
		// if exdate is set, convert it from local to UTC time
		if (
			isset( $_POST['ai1ec_exdate'] ) &&
			! empty( $_POST['ai1ec_exdate'] )
		) {
			$exdate = $_POST['ai1ec_exdate'];
		}

		$is_new = false;
		$event  = null;
		try {
			$event =  $this->_registry->get(
				'model.event',
				$post_id ? $post_id : null
			);
		} catch ( Ai1ec_Event_Not_Found_Exception $excpt ) {
			// Post exists, but event data hasn't been saved yet. Create new event
			// object.
			$is_new = true;
			$event  =  $this->_registry->get( 'model.event' );
		}
		$formatted_timezone = $this->_registry->get( 'date.timezone' )
				->get_name( $timezone_name );
		if ( empty( $timezone_name ) || ! $formatted_timezone ) {
			$timezone_name = 'sys.default';
		}

		unset( $formatted_timezone );
		$start_time_entry = $this->_registry
			->get( 'date.time', $start_time, $timezone_name );
		$end_time_entry   = $this->_registry
			->get( 'date.time', $end_time,   $timezone_name );

		$timezone_name = $start_time_entry->get_timezone();
		if ( null === $timezone_name ) {
			$timezone_name = $start_time_entry->get_default_format_timezone();
		}

		$event->set( 'post_id',          $post_id );
		$event->set( 'start',            $start_time_entry );
		if ( $instant_event ) {
			$event->set_no_end_time();
		} else {
			$event->set( 'end',          $end_time_entry );
		}
		$event->set( 'timezone_name',    $timezone_name );
		$event->set( 'allday',           $all_day );
		$event->set( 'venue',            $venue );
		$event->set( 'address',          $address );
		$event->set( 'city',             $city );
		$event->set( 'province',         $province );
		$event->set( 'postal_code',      $postal_code );
		$event->set( 'country',          $country );
		$event->set( 'show_map',         $google_map );
		$event->set( 'cost',             $cost );
		$event->set( 'is_free',          $is_free );
		$event->set( 'ticket_url',       $ticket_url );
		$event->set( 'contact_name',     $contact_name );
		$event->set( 'contact_phone',    $contact_phone );
		$event->set( 'contact_email',    $contact_email );
		$event->set( 'contact_url',      $contact_url );
		$event->set( 'recurrence_rules', $rrule );
		$event->set( 'exception_rules',  $exrule );
		$event->set( 'exception_dates',  $exdate );
		$event->set( 'show_coordinates', $show_coordinates );
		$event->set( 'longitude',        trim( $longitude ) );
		$event->set( 'latitude',         trim( $latitude ) );
		$event->set( 'ical_uid',         $event->get_uid() );

		update_post_meta( $post_id, 'ai1ec_banner_image', $banner_image );

		// let other extensions save their fields.
		do_action( 'ai1ec_save_post', $event );

		$event->save( ! $is_new );


		// LABEL:magicquotes
		// restore `magic` WordPress quotes to maintain compatibility
		$_POST = add_magic_quotes( $_POST );
		return $event;
	}

	/**
	 * _create_duplicate_post method
	 *
	 * Create copy of event by calling {@uses wp_insert_post} function.
	 * Using 'post_parent' to add hierarchy.
	 *
	 * @param array $data Event instance data to copy
	 *
	 * @return int|bool New post ID or false on failure
	 **/
	public function create_duplicate_post() {
		if ( ! isset( $_POST['post_ID'] ) ) {
			return false;
		}
		$clean_fields = array(
			'ai1ec_repeat'      => NULL,
			'ai1ec_rrule'       => '',
			'ai1ec_exrule'      => '',
			'ai1ec_exdate'      => '',
			'post_ID'           => NULL,
			'post_name'         => NULL,
			'ai1ec_instance_id' => NULL,
		);
		$old_post_id = $_POST['post_ID'];
		$instance_id = $_POST['ai1ec_instance_id'];
		foreach ( $clean_fields as $field => $to_value ) {
			if ( NULL === $to_value ) {
				unset( $_POST[$field] );
			} else {
				$_POST[$field] = $to_value;
			}
		}
		$_POST   = _wp_translate_postdata( false, $_POST );
		$_POST['post_parent'] = $old_post_id;
		$post_id = wp_insert_post( $_POST );
		$this->_registry->get( 'model.event.parent' )->event_parent(
			$post_id,
			$old_post_id,
			$instance_id
		);
		return $post_id;
	}
}
