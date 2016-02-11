<?php
/**
 * A set of action functions which handle the behavior of the roles checklist
 * on user edit screens.
 */
class MDMR_Checklist_Controller {

	/**
	 * The model object.
	 *
	 * @var object
	 */
	var $model;

	/**
	 * Constructor. Define properties.
	 *
	 * @param object $model The model object.
	 */
	public function __construct( $model ) {
		$this->model = $model;
	}

	/**
	 * Remove the default WordPress role dropdown from the DOM.
	 *
	 * @param string $hook The current admin screen.
	 */
	public function remove_dropdown( $hook ) {
		if ( $hook != 'user-edit.php' )
			return;
		wp_enqueue_script( 'md-multiple-roles', MDMR_URL . 'views/js/scripts.js', array( 'jquery' ) );
	}

	/**
	 * Output the checklist view. If the user is not allowed to edit roles,
	 * nothing will appear.
	 *
	 * @param object $user The current user object.
	 */
	public function output_checklist( $user ) {

		if ( !$this->model->can_update_roles() )
			return;

		wp_nonce_field( 'update-md-multiple-roles', 'md_multiple_roles_nonce' );

		$roles        = $this->model->get_roles();
		$user_roles   = $user->roles;

		include( MDMR_PATH . 'views/checklist.html.php' );

	}

	/**
	 * Update the given user's roles as long as we've passed the nonce
	 * and permissions checks.
	 *
	 * @param int $user_id The user ID whose roles might get updated.
	 */
	public function process_checklist( $user_id ) {

		if ( isset( $_POST['md_multiple_roles_nonce'] ) && !wp_verify_nonce( $_POST['md_multiple_roles_nonce'], 'update-md-multiple-roles' ) )
			return;

		if ( !$this->model->can_update_roles() )
			return;

		$new_roles = isset( $_POST['md_multiple_roles'] ) ? $_POST['md_multiple_roles'] : array();

		$this->model->update_roles( $user_id, $new_roles );

	}

}