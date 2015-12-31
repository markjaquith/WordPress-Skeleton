<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class for performing actions incrementally. Internally used for converting submissions, exporting submissions, etc.
 * Very useful when interacting with large amounts of data.
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Step Processing
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.7.4
*/

class NF_Step_Processing
{

	/**
	 * @var action
	 */
	var $action = '';

	/**
	 * @var step
	 */
	var $step = '';

	/**
	 * @var total_steps
	 */
	var $total_steps = '';

	/**
	 * @var redirect
	 */
	var $redirect = '';

    /**
     * @var array
     */
    var $errors = array();

	/**
	 * @var args
	 */
	var $args = array();

	/**
	 * Get things rolling
	 * 
	 * @since 2.7.4
	 * @return void
	 */
	function __construct() {

		//Bail if we aren't in the admin.
		if ( ! is_admin() )
			return false;

		ignore_user_abort( true );

		if ( ! nf_is_func_disabled( 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
			//set_time_limit( 0 );
		}

		add_action( 'wp_ajax_nf_' . $this->action, array( $this, 'processing' ) );
	}

	/**
	 * Process our request.
	 * Call the appropriate loading or step functions.
	 * 
	 * @since 2.7.6
	 * @return void
	 */
	public function processing() {

		// Get our passed arguments. These come from the querysting of the processing page.
		if ( isset ( $_REQUEST['args'] ) ) {
			$this->args = $_REQUEST['args'];
			if ( isset ( $this->args['redirect'] ) ) {
				$this->redirect = $this->args['redirect'];
			}
		} else {
			$this->args = array();
		}	

		// Get our current step.
		$this->step = isset ( $_REQUEST['step'] )? esc_html( $_REQUEST['step'] ) : 'loading';

		// Get our total steps
		$this->total_steps = isset ( $_REQUEST['total_steps'] )? esc_html( $_REQUEST['total_steps'] ) : 0;

		// If our step is loading, then we need to return how many total steps there are along with the next step, which is 1.
		if ( 'loading' == $this->step ) {
			$return = $this->loading();
			if ( ! isset ( $return['step'] ) ) {
				$saved_step = get_user_option( 'nf_step_processing_' . $this->action . '_step' );
				if ( ! empty ( $saved_step ) ) {
					$this->step = $saved_step;
				} else {
					$this->step = 1;
				}

				$return['step'] = $this->step;
			}
			if ( ! isset ( $return['complete'] ) ) {
				$return['complete'] = false;
			}
		} else { // We aren't on the loading step, so do our processing.
			$return = $this->step();
			if ( ! isset ( $return['step'] ) ) {
				$this->step++;
				$return['step'] = $this->step;
			}

			if ( ! isset ( $return['complete'] ) ) {
				if ( $this->step > $this->total_steps ) {
					$complete = true;
				} else {
					$complete = false;
				}
				$return['complete'] = $complete;
			}

			$return['total_steps'] = $this->total_steps;
		}

		$user_id = get_current_user_id();

		if ( $return['complete'] ) {
			// Delete our step option
			delete_user_option( $user_id, 'nf_step_processing_' . $this->action . '_step' );
			// Set our redirect variable.
			$return['redirect'] = $this->redirect;
			// Run our complete function
			$this->complete();
		} else {
			// Save our current step so that we can resume if necessary
			update_user_option( $user_id, 'nf_step_processing_' . $this->action . '_step', $this->step );
		}

		if ( isset ( $this->redirect ) && ! empty ( $this->redirect ) ) {
			$this->args['redirect'] = $this->redirect;
		}

        $return['errors'] = ( $this->errors ) ? $this->errors : FALSE;

		$return['args'] = $this->args;

		echo json_encode( $return );
		die();

	}

	/**
	 * Run our loading process.
	 * This function should be overwritten in child classes.
	 * 
	 * @since 2.7.4
	 * @return array $args
	 */
	public function loading() {
		// This space left intentionally blank.
	}

	/**
	 * This function is called for every step.
	 * This function should be overwritten in child classes.
	 * 
	 * @since 2.7.4
	 * @return array $args
	 */
	public function step() {
		// This space left intentionally blank.
	}	

	/**
	 * This function is called for every step.
	 * This function should be overwritten in child classes.
	 * 
	 * @since 2.7.4
	 * @return array $args
	 */
	public function complete() {
		// This space left intentionally blank.
	}



}