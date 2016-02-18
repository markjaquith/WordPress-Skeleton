<?php
/**
 * Role logic: retrieving, updating, and checking permission.
 */
class MDMR_Model {

	/**
	 * Grab all WordPress roles.
	 *
	 * @return array Roles in name => label pairs.
	 */
	public function get_roles() {
		global $wp_roles;
		return $wp_roles->role_names;
	}

	/**
	 * Grab a particular user's roles.
	 *
	 * @param object|int $user The user object or ID.
	 * @return array Roles in name => label pairs.
	 */
	public function get_user_roles( $user = 0 ) {

		if ( $user && is_int( $user ) )
			$user = get_user_by( 'id', $user );

		if ( !$user )
			return array();

		global $wp_roles;
		$roles = array();

		foreach( $user->roles as $role ) {
			$roles[$role] = $wp_roles->role_names[$role];
		}

		return $roles;

	}

	/**
	 * Erase the user's existing roles and replace them with the new array.
	 *
	 * @param integer $user_id The WordPress user ID.
	 * @param array $roles The new array of roles for the user.
	 */
	public function update_roles( $user_id = 0, $roles = array() ) {

		$roles = array_map( 'sanitize_key', (array) $roles );
		$user = get_user_by( 'id', $user_id );

		// remove all roles
		$user->set_role( '' );

		foreach( $roles as $role ) {
			$user->add_role( $role );
		}

	}

	/**
	 * Check whether or not a user can edit roles. User must have the edit_roles cap and
	 * must be on a specific site (and not in the network admin area). Users also can't
	 * edit their own roles unless they're a network admin.
	 *
	 * @return bool True if current user can update roles, false if not.
	 */
	public function can_update_roles() {

		if ( is_network_admin()
	      || !current_user_can( 'edit_users' )
		  || ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE && !current_user_can( 'manage_sites' ) ) )
			return false;

		return true;

	}

}