<?php

/**
 * Model representing an event or an event instance.
 *
 * @author       Time.ly Network, Inc.
 * @since        2.1
 * @instantiator Ai1ec_Factory_Event.create_event_instance
 * @package      Ai1EC
 * @subpackage   Ai1EC.Model
 */
class Ai1ec_Event_Compatibility extends Ai1ec_Event {

	/**
	 * Getter.
	 *
	 * @param string $name Property name.
	 *
	 * @return mixed Property value.
	 */
	public function __get( $name ) {
		$value = $this->get( $name );
		if ( null !== $value ) {
			return $value;
		}
		return $this->get_runtime( $name );
	}

	/**
	 * Isset magic function.
	 *
	 * @param string $name Property name.
	 *
	 * @return bool True of false.
	 */
	public function __isset( $name ) {
		$method_name = 'get' . $name;
		if ( method_exists( $this, $method_name ) ) {
			return false;
		}
		return ( null !== $this->$name );
	}

	/**
	 * Twig timespan short method.
	 *
	 * @return string Value.
	 */
	public function gettimespan_short() {
		return $this->_registry->get( 'view.event.time' )
			->get_timespan_html( $this, 'short' );
	}

	/**
	 * Twig is_allday method.
	 *
	 * @return bool Value.
	 */
	public function getis_allday() {
		return $this->is_allday();
	}

	/**
	 * Returns Event instance permalink for FER compatibility.
	 *
	 * @return string Event instance permalink.
	 */
	public function getpermalink() {
		return $this->get_runtime( 'instance_permalink' );
	}
}
