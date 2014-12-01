<?php

/**
 * This class renders the html for the event location.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.View.Event
 */
class Ai1ec_View_Event_Location extends Ai1ec_Base {

	/**
	 * Return location details in brief format, separated by | characters.
	 *
	 * @return $string Short location string
	 */
	public function get_short_location( Ai1ec_Event $event ) {
		$location_items = array();
		foreach ( array( 'venue', 'city', 'province', 'country' ) as $field ) {
			if ( $event->get( $field ) !== '' ) {
				$location_items[] = $event->get( $field );
			}
		}
		return implode( ' | ', $location_items );
	}

	/*
	 * Return any available location details separated by newlines
	*/
	public function get_location( Ai1ec_Event $event ) {
		$location = '';
		$venue    = $event->get( 'venue' );
		if ( $venue ) {
			$location .= $venue . "\n";
		}
		$address = $event->get( 'address' );
		if ( $address ) {
			$bits = explode( ',', $address );
			$bits = array_map( 'trim', $bits );

			// If more than three comma-separated values, treat first value as
			// the street address, last value as the country, and everything
			// in the middle as the city, state, etc.
			if ( count( $bits ) >= 3 ) {
				// Append the street address
				$street_address = array_shift( $bits ) . "\n";
				if ( $street_address ) {
					$location .= $street_address;
				}
				// Save the country for the last line
				$country = array_pop( $bits );
				// Append the middle bit(s) (filtering out any zero-length strings)
				$bits = array_filter( $bits, 'strval' );
				if ( $bits ) {
					$location .= join( ', ', $bits ) . "\n";
				}
				if ( $country ) {
					$location .= $country . "\n";
				}
			} else {
				// There are two or less comma-separated values, so just append
				// them each on their own line (filtering out any zero-length strings)
				$bits      = array_filter( $bits, 'strval' );
				$location .= join( "\n", $bits );
			}
		}
		return $location;
	}

	/**
	 * get_map_view function
	 *
	 * Returns HTML markup displaying a Google map of the given event, if the event
	 * has show_map set to true. Returns a zero-length string otherwise.
	 *
	 * @return void
	 **/
	function get_map_view( Ai1ec_Event $event ) {
		$settings = $this->_registry->get( 'model.settings' );
		$loader = $this->_registry->get( 'theme.loader' );
		if( ! $event->get( 'show_map' ) ) {
			return '';
		}

		$location = $this->get_latlng( $event );
		if ( ! $location ) {
			$location = $event->get( 'address' );
		}

		$args = array(
			'address'                 => $location,
			'gmap_url_link'           => $this->get_gmap_url( $event, false ),
			'hide_maps_until_clicked' => $settings->get( 'hide_maps_until_clicked' ),
			'text_view_map'           => __( 'Click to view map', AI1EC_PLUGIN_NAME ),
			'text_full_map'           => __( 'View Full-Size Map', AI1EC_PLUGIN_NAME ),
		);
		return $loader->get_file( 'event-map.twig', $args, false )->get_content();
	}

	/**
	 * Returns the latitude/longitude coordinates as a textual string
	 * parsable by the Geocoder API.
	 *
	 * @param  Ai1ec_Event &$event The event to return data from
	 *
	 * @return string              The latitude & longitude string, or null
	 */
	public function get_latlng( Ai1ec_Event $event ) {
		// If the coordinates are set, use those, otherwise use the address.
		$location = NULL;
		// If the coordinates are set by hand use them.
		if ( $event->get( 'show_coordinates' ) ) {
			$longitude = floatval( $event->get( 'longitude' ) );
			$latitude  = floatval( $event->get( 'latitude' ) );
			$location  = $latitude . ',' . $longitude;
		}
		return $location;
	}

	/**
	 * Returns the URL to the Google Map for the given event object.
	 *
	 * @param Ai1ec_Event $event  The event object to display a map for
	 *
	 * @return string
	 */
	public function get_gmap_url( Ai1ec_Event $event ) {
		$lang     = $this->_registry->get( 'p28n.wpml' )->get_language();
		$location = $this->get_latlng( $event );
		if ( ! $location ) {
			$location = $event->get( 'address' );
		}
		return 'https://www.google.com/maps?f=q&hl=' . urlencode( $lang ) .
			'&source=embed&q=' . urlencode( $location );
	}

}