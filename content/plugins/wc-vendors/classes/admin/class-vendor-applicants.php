<?php

/**
 *
 */
class WCV_Vendor_Applicants
{

	function __construct()
	{
		add_filter( 'user_row_actions', array( $this, 'user_row_actions' ), 10, 2 );
		add_filter( 'load-users.php', array( $this, 'user_row_actions_commit' ) );
	}

	/**
	 *
	 *
	 * @param unknown $actions
	 * @param unknown $user_object
	 *
	 * @return unknown
	 */
	function user_row_actions( $actions, $user_object )
	{
		if ( !empty( $_GET[ 'role' ] ) && $_GET[ 'role' ] == 'pending_vendor' ) {
			$actions[ 'approve_vendor' ] = "<a href='?role=pending_vendor&action=approve_vendor&user_id=" . $user_object->ID . "'>" . __( 'Approve', 'cgc_ub' ) . "</a>";
			$actions[ 'deny_vendor' ]    = "<a href='?role=pending_vendor&action=deny_vendor&user_id=" . $user_object->ID . "'>" . __( 'Deny', 'cgc_ub' ) . "</a>";
		}

		return $actions;
	}


	/**
	 * 
	 */
	public function user_row_actions_commit()
	{
		if ( !empty( $_GET[ 'action' ] ) && !empty( $_GET[ 'user_id' ] ) ) {

			$wp_user_object = new WP_User( (int) $_GET[ 'user_id' ] );

			switch ( $_GET[ 'action' ] ) {
				case 'approve_vendor':
					$role = 'vendor';
					add_action( 'admin_notices', array( $this, 'approved' ) );
					do_action( 'wcvendors_approve_vendor', $wp_user_object ); 
					break;

				case 'deny_vendor':
					$role = 'subscriber';
					add_action( 'admin_notices', array( $this, 'denied' ) );
					do_action( 'wcvendors_deny_vendor', $wp_user_object ); 
					break;

				default:
					// code...
					break;
			}

			$wp_user_object->set_role( $role );

		}
	}


	/**
	 *
	 */
	public function denied()
	{
		echo '<div class="updated">';
		echo '<p>' . __( 'Vendor has been <b>denied</b>.', 'wcvendors' ) . '</p>';
		echo '</div>';
	}


	/**
	 *
	 */
	public function approved()
	{
		echo '<div class="updated">';
		echo '<p>' . __( 'Vendor has been <b>approved</b>.', 'wcvendors' ) . '</p>';
		echo '</div>';
	}


	/**
	 *
	 *
	 * @param unknown $values
	 *
	 * @return unknown
	 */
	public function show_pending_vendors_link( $values )
	{
		$values[ 'pending_vendors' ] = '<a href="?role=asd">' . __( 'Pending Vendors', 'wcvendors' ) . ' <span class="count">(3)</span></a>';

		return $values;
	}

}
