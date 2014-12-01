<?php

/**
 * Event Dispatcher processing.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package	   AI1EC
 * @subpackage AI1EC.Event
 */
class Ai1ec_Event_Dispatcher extends Ai1ec_Base {

	/**
	 * Register callback object.
	 *
	 * @param string						$hook		   Name of the event hook.
	 * @param Ai1ec_Event_Callback_Abstract $entity		   Event Callback object.
	 * @param integer						$priority	   Priorify of the event hook execution.
	 * @param integer						$accepted_args Number of accepted method parameters.
	 *
	 * @return Ai1ec_Event_Dispatcher Event Dispatcher Object.
	 */
	public function register(
		$hook,
		Ai1ec_Event_Callback_Abstract $entity,
		$priority      = 10,
		$accepted_args = 1
	) {
		$wp_method = 'add_action';
		if ( $entity instanceof Ai1ec_Event_Callback_Filter ) {
			$wp_method = 'add_filter';
		}
		$wp_method(
			$hook,
			array( $entity, 'run' ),
			$priority,
			$accepted_args
		);
		return $this;
	}

	/**
	 * Creates a callback object and register it.
	 *
	 * @param string  $hook          Name of the event hook.
	 * @param array   $method        Method to call.
	 * @param integer $priority      Priorify of the event hook execution.
	 * @param integer $accepted_args Number of accepted method parameters.
	 * @param string  $type          The type to add.
	 *
	 * @return void
	 */
	protected function _register(
		$hook,
		array $method,
		$type,
		$priority      = 10,
		$accepted_args = 1
	) {
		$action = $this->_registry->get(
			'event.callback.' . $type,
			$method[0],
			$method[1]
		);
		$this->register(
			$hook,
			$action,
			$priority,
			$accepted_args
		);
	}

	/**
	 * Register a filter.
	 * 
	 * @param string  $hook          Name of the event hook.
	 * @param array   $method        Method to call.
	 * @param integer $priority      Priorify of the event hook execution.
	 * @param integer $accepted_args Number of accepted method parameters.
	 *
	 * @return void
	 */
	public function register_filter(
		$hook,
		array $method,
		$priority      = 10,
		$accepted_args = 1
	) {
		$this->_register(
			$hook,
			$method,
			'filter',
			$priority,
			$accepted_args
		);
	}

	/**
	 * Register an action.
	 *
	 * @param string  $hook          Name of the event hook.
	 * @param array   $method        Method to call.
	 * @param integer $priority      Priorify of the event hook execution.
	 * @param integer $accepted_args Number of accepted method parameters.
	 *
	 * @return void
	 */
	public function register_action(
		$hook,
		array $method,
		$priority      = 10,
		$accepted_args = 1
	) {
		$this->_register(
			$hook,
			$method,
			'action',
			$priority,
			$accepted_args
		);
	}

	/**
	 * Register a shortcode.
	 *
	 * @param string $shortcode Name of the shortcode tag.
	 * @param array  $method    Method to call.
	 *
	 * @return void
	 */
	public function register_shortcode(
		$shortcode,
		array $method
	) {
		$entity = $this->_registry->get(
			'event.callback.shortcode',
			$method[0],
			$method[1]
		);
		add_shortcode( $shortcode, array( $entity, 'run' ) );
		return $this;
	}

}