<?php
/**
 * Install class on activation.
 *
 * @author  Matt Gates <http://mgates.me>
 * @package ProductVendor
 */


class WCV_Install
{

	/**
	 * Checks if install is requierd
	 *
	 * @return unknown
	 */
	public function init()
	{
		$db_version = WC_Vendors::$pv_options->get_option( 'db_version' );

		// Initial Install 
		if ( version_compare( $db_version, '1.0', '<' ) ) {
			$this->install_wcvendor();
			WC_Vendors::$pv_options->update_option( 'db_version', '1.5.0' );
		}

	}


	/**
	 * Grouped functions for installing the WC Vendor plugin
	 */
	private function install_wcvendor()
	{
		// Clear the cron
		wp_clear_scheduled_hook( 'pv_schedule_mass_payments' );

		// Add the vendors role
		$this->add_new_roles();

		// Create tables
		$this->create_new_tables();

		// Create the Orders page if it doesn't exist
		$orders_page = WC_Vendors::$pv_options->get_option( 'orders_page' );
		if ( empty( $orders_page ) ) $this->create_new_pages();
	}


	/**
	 * Add the new Vendor role
	 *
	 * @return bool
	 */
	private function add_new_roles()
	{
		remove_role( 'pending_vendor' );
		add_role( 'pending_vendor', __( 'Pending Vendor', 'wcvendors' ), array(
																					  'read'         => true,
																					  'edit_posts'   => false,
																					  'delete_posts' => false
																				 ) );

		remove_role( 'vendor' );
		add_role( 'vendor', __('Vendor', 'wcvendors') , array(
										   'assign_product_terms'     => true,
										   'edit_products'            => true,
										   'edit_product'             => true,
										   'edit_published_products'  => false,
										   'manage_product'           => true,
										   'publish_products'         => false,
										   'read'                     => true,
										   'upload_files'             => true,
										   'view_woocommerce_reports' => true,
									  ) );
	}


	/**
	 * Create the pv_commission table
	 */
	private function create_new_tables()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			product_id bigint(20) NOT NULL,
			order_id bigint(20) NOT NULL,
			vendor_id bigint(20) NOT NULL,
			total_due decimal(20,2) NOT NULL,
			qty BIGINT( 20 ) NOT NULL,
			total_shipping decimal(20,2) NOT NULL,
			tax decimal(20,2) NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'due',
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		);";
		dbDelta( $sql );
	}


	/**
	 * Create a page
	 *
	 * @access public
	 * @return void
	 *
	 * @param mixed  $slug         Slug for the new page
	 * @param mixed  $option       Option name to store the page's ID
	 * @param string $page_title   (optional) (default: '') Title for the new page
	 * @param string $page_content (optional) (default: '') Content for the new page
	 * @param int    $post_parent  (optional) (default: 0) Parent for the new page
	 */
	function create_page( $slug, $page_title = '', $page_content = '', $post_parent = 0 )
	{
		global $wpdb;

		$page_id = WC_Vendors::$pv_options->get_option( $slug . '_page' );

		if ( $page_id > 0 && get_post( $page_id ) ) {
			return $page_id;
		}

		$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );
		if ( $page_found ) {
			if ( !$page_id ) {
				WC_Vendors::$pv_options->update_option( $slug . '_page', $page_found );

				return $page_found;
			}

			return $page_id;
		}

		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed'
		);

		$page_id = wp_insert_post( $page_data );
		WC_Vendors::$pv_options->update_option( $slug . '_page', $page_id );

		return $page_id;
	}


	/**
	 * Create the Orders page for the frontend
	 */
	private function create_new_pages()
	{
		global $wpdb;

		$vendor_page_id = $this->create_page( 'vendor_dashboard', __( 'Vendor Dashboard', 'wcvendors' ), '[wcv_vendor_dashboard]' );
		$this->create_page( 'orders', __( 'Orders', 'wcvendors' ), '[wcv_orders]', $vendor_page_id );
		$this->create_page( 'shop_settings', __( 'Shop Settings', 'wcvendors' ), '[wcv_shop_settings]', $vendor_page_id );
	}


	/**
	 * Depreciated 
	 *
	 * @param unknown $version
	 */
	public function update_to( $version )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";

		switch ( $version ) {

			case '1.3.2':

				$sql = "ALTER TABLE  `{$table_name}` ADD  `qty` BIGINT( 20 ) NOT NULL AFTER  `total_due`";
				$wpdb->query( $sql );

				$sql     = "SELECT * FROM `{$table_name}`";
				$results = $wpdb->get_results( $sql );
				foreach ( $results as $key => $value ) {

					$order = new WC_Order( $value->order_id );

					foreach ( $order->get_items() as $o_key => $o_value ) {

						if ( $value->product_id == $o_value[ 'product_id' ] || ( !empty( $o_value[ 'variation_id' ] ) && $value->product_id == $o_value[ 'variation_id' ] ) ) {
							$wpdb->update(
								$table_name,
								array( 'qty' => $o_value[ 'qty' ] ),
								array( 'id' => $value->id ),
								array( '%d' ),
								array( '%d' )
							);
						}
					}

				}

				break;

			case '1.4.0':

				add_role( 'pending_vendor', __( 'Pending Vendor', 'wcvendors' ), array(
																							  'read'         => true,
																							  'edit_posts'   => false,
																							  'delete_posts' => false
																						 ) );

				$this->create_new_pages();

				break;

			case '1.4.2':

				$sql = "ALTER TABLE  `{$table_name}` ADD  `total_shipping` decimal(20,2) NOT NULL AFTER `total_due`";
				$wpdb->query( $sql );

			case '1.4.3':

				$sql = "ALTER TABLE  `{$table_name}` ADD  `tax` decimal(20,2) NOT NULL AFTER `total_shipping`";
				$wpdb->query( $sql );

			case '1.4.5':

				// Flush rules to fix the /page/2/ issue on vendor shop pages
				update_option( WC_Vendors::$id . '_flush_rules', true );

			default:
				// code...
				break;
		}
	}


}
