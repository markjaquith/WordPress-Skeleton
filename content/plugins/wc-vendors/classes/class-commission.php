<?php

/**
 * Commission functions
 *
 * @author  Matt Gates <http://mgates.me>
 * @package ProductVendor
 */


class WCV_Commission
{


	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->completed_statuses = apply_filters( 'wcvendors_completed_statuses', array(
																								'completed',
																								'processing',
																						   ) );

		$this->reverse_statuses = apply_filters( 'wcvendors_reversed_statuses', array(
																							 'pending',
																							 'refunded',
																							 'cancelled',
																							 'failed',
																						) );

		$this->check_order_reverse();
		$this->check_order_complete();
	}


	/**
	 * Run actions when an order is reversed
	 */
	public function check_order_reverse()
	{
		foreach ( $this->completed_statuses as $completed ) {
			foreach ( $this->reverse_statuses as $reversed ) {
				add_action( "woocommerce_order_status_{$completed}_to_{$reversed}", array( 'WCV_Commission', 'reverse_due_commission' ) );
			}
		}
	}


	/**
	 * Runs only on a manual order update by a human
	 */
	public function check_order_complete()
	{
		foreach ( $this->completed_statuses as $completed ) {
			add_action( 'woocommerce_order_status_' . $completed, array( 'WCV_Commission', 'log_commission_due' ) );
		}
	}


	/**
	 * Reverse commission for an entire order
	 *
	 * Only runs if the order has been logged in pv_commission table
	 *
	 * @param int $order_id
	 *
	 * @return unknown
	 */
	public function reverse_due_commission( $order_id )
	{
		global $wpdb;

		// Check if this order exists
		$count = WCV_Commission::count_commission_by_order( $order_id );
		if ( !$count ) return false;

		// Deduct this amount from the vendor's total due
		$results = WCV_Commission::sum_total_due_for_order( $order_id );
		$ids        = implode( ',', $results[ 'ids' ] );
		$table_name = $wpdb->prefix . "pv_commission";

		$query   = "UPDATE `{$table_name}` SET `status` = '%s' WHERE id IN ({$ids})";
		$results = $wpdb->query( $wpdb->prepare( $query, 'reversed' ) );

		return $results;
	}


	/**
	 * Store all commission due for an order
	 *
	 * @return bool
	 *
	 * @param int $order_id
	 */
	public static function log_commission_due( $order_id )
	{
		global $woocommerce;

		$order = new WC_Order( $order_id );
		$dues  = WCV_Vendors::get_vendor_dues_from_order( $order, false );

		foreach ( $dues as $vendor_id => $details ) {

			// Only process vendor commission
			if ( !WCV_Vendors::is_vendor( $vendor_id ) ) continue;

			// See if they currently have an amount due
			$due = WCV_Vendors::count_due_by_vendor( $vendor_id, $order_id );
			if ( $due > 0 ) continue;

			// Get the dues in an easy format for inserting to our table
			$insert_due = array();

			foreach ( $details as $key => $detail ) {
				$product_id = $detail['product_id'];

				$insert_due[ $product_id ] = array(
					'order_id'       => $order_id,
					'vendor_id'      => $vendor_id,
					'product_id'     => $product_id,
					'total_due'      => !empty( $insert_due[ $product_id ][ 'total_due' ] ) ? ( $detail[ 'commission' ] + $insert_due[ $product_id ][ 'total_due' ] ) : $detail[ 'commission' ],
					'total_shipping' => !empty( $insert_due[ $product_id ][ 'total_shipping' ] ) ? ( $detail[ 'shipping' ] + $insert_due[ $product_id ][ 'total_shipping' ] ) : $detail[ 'shipping' ],
					'tax'            => !empty( $insert_due[ $product_id ][ 'tax' ] ) ? ( $detail[ 'tax' ] + $insert_due[ $product_id ][ 'tax' ] ) : $detail[ 'tax' ],
					'qty'            => !empty( $insert_due[ $product_id ][ 'qty' ] ) ? ( $detail[ 'qty' ] + $insert_due[ $product_id ][ 'qty' ] ) : $detail[ 'qty' ],
					'time'           => $order->order_date,
				);
			}

			if ( !empty( $insert_due ) ) {
				WCV_Commission::insert_new_commission( array_values( $insert_due ) );
			}
		}

	}


	/**
	 * Add up the totals for an order for each vendor
	 *
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function sum_total_due_for_order( $order_id )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";
		$query      = "SELECT `id`, `total_due`, `total_shipping`, `tax`, `vendor_id`
					     FROM `{$table_name}`
					     WHERE `order_id` = %d
					     AND `status` = %s";

		$results = $wpdb->get_results( $wpdb->prepare( $query, $order_id, 'due' ) );

		foreach ( $results as $commission ) {
			$commission_ids[ ] = $commission->id;

			$pay[ $commission->vendor_id ] = !empty( $pay[ $commission->vendor_id ] )
				? ( $pay[ $commission->vendor_id ] + ( $commission->total_due + $commission->total_shipping + $commission->tax ) )
				: ( $commission->total_due + $commission->total_shipping + $commission->tax );
		}

		$return = array(
			'vendors' => $pay,
			'ids'     => $commission_ids,
		);

		return $return;
	}


	/**
	 * Return all commission outstanding with a 'due' status
	 *
	 * @return object
	 */
	public static function get_all_due()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";
		$query      = "SELECT id, vendor_id, total_due
					FROM `{$table_name}`
					WHERE status = %s";
		$results    = $wpdb->get_results( $wpdb->prepare( $query, 'due' ) );

		return $results;
	}


	/**
	 * Check if this order has commission logged already
	 *
	 * @param int $order_id
	 *
	 * @return int
	 */
	public static function count_commission_by_order( $order_id )
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "pv_commission";

		if ( is_array( $order_id ) )
			$order_id = implode( ',', $order_id );

		$query = "SELECT COUNT(order_id) AS order_count
				     FROM {$table_name}
				     WHERE order_id IN ($order_id)
				     AND status <> %s";
		$count = $wpdb->get_var( $wpdb->prepare( $query, 'reversed' ) );

		return $count;
	}

	/**
	 * Check the commission status for the order 
	 *
	 * @param array 	$order
	 * @param string 	$status
	 *
	 * @return int
	 */
	public static function check_commission_status( $order, $status ) { 

		global $wpdb; 

		$table_name 	= $wpdb->prefix . "pv_commission";

		$order_id 		= $order[ 'order_id' ]; 
		$vendor_id 		= $order[ 'vendor_id' ]; 
    	$product_id		= $order[ 'product_id' ]; 

		$query = "SELECT count(order_id) AS order_count 
				 	FROM {$table_name}
				 	WHERE order_id = {$order_id} 
				 	AND vendor_id = {$vendor_id} 
				 	AND product_id = {$product_id}
				 	AND status = %s
		"; 

		return $wpdb->get_var( $wpdb->prepare( $query , $status ) ); 

	}


	/**
	 * Product's commission rate in percentage form
	 *
	 * Eg: 50 for 50%
	 *
	 * @param int $product_id
	 *
	 * @return float
	 */
	public static function get_commission_rate( $product_id )
	{

		$commission = 0; 

		$parent = get_post_ancestors( $product_id );
		if ( $parent ) $product_id = $parent[ 0 ];

		$vendor_id = WCV_Vendors::get_vendor_from_product( $product_id );

		$product_commission = get_post_meta( $product_id, 'pv_commission_rate', true );
		$vendor_commission  = WCV_Vendors::get_default_commission( $vendor_id );
		$default_commission = WC_Vendors::$pv_options->get_option( 'default_commission' );

		if ( $product_commission != '' && $product_commission !== false ) {
			$commission = $product_commission;
		}

		else if ( $vendor_commission != '' && $vendor_commission !== false ) {
			$commission = $vendor_commission;
		}

		else if ( $default_commission != '' && $default_commission !== false ) {
			$commission = $default_commission;
		}

		return apply_filters( 'wcv_commission_rate_percent', $commission, $product_id );
	}


	/**
	 * Commission due for a product based on a rate and price
	 *
	 * @param float   $product_price
	 * @param unknown $product_id
	 *
	 * @return float
	 */
	public static function calculate_commission( $product_price, $product_id, $order )
	{
		$commission_rate = WCV_Commission::get_commission_rate( $product_id );
		$commission      = $product_price * ( $commission_rate / 100 );
		$commission      = round( $commission, 2 );

		return apply_filters( 'wcv_commission_rate', $commission, $product_id, $product_price, $order );
	}


	/**
	 * Log commission to the pv_commission table
	 *
	 * Will either update or insert to the database
	 *
	 * @param array $orders
	 *
	 * @return unknown
	 */
	public static function insert_new_commission( $orders = array() )
	{
		global $wpdb;

		if ( empty( $orders ) ) return false;

		$table = $wpdb->prefix . "pv_commission";

		// Insert the time and default status 'due'
		foreach ( $orders as $key => $order ) {
			$orders[ $key ][ 'time' ]   = $order['time'];
			$orders[ $key ][ 'status' ] = ( $order['total_due'] == 0 ) ? 'paid' : 'due';
		}

		foreach ( $orders as $key => $order ) {
			$where  = array(
				'order_id'   => $order[ 'order_id' ],
				'product_id' => $order[ 'product_id' ],
				'vendor_id'  => $order[ 'vendor_id' ],
				'qty'        => $order[ 'qty' ],
			);
			// Is the commission already paid? 
			$count = WCV_Commission::check_commission_status( $order, 'paid' ); 

			if ( $count == 0 ) { 
				$update = $wpdb->update( $table, $order, $where );
				if ( !$update ) $insert = $wpdb->insert( $table, $order );
			}

		}

		do_action( 'wcv_commissions_inserted', $orders );
	}


	/**
	 * Set commission to 'paid' for an entire order
	 *
	 *
	 * @access public
	 *
	 * @param mixed   $order_id   An array of Order IDs or an int.
	 * @param unknown $column_ids (optional)
	 *
	 * @return bool.
	 */
	public static function set_order_commission_paid( $order_id, $column_ids = false )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";

		if ( is_array( $order_id ) )
			$order_id = implode( ',', $order_id );

		$query  = "UPDATE `{$table_name}` SET `status` = 'paid' WHERE order_id IN ($order_id)";
		$result = $wpdb->query( $query );

		return $result;
	}


	/**
	 * Set commission to 'paid' for an entire order
	 *
	 *
	 * @access public
	 *
	 * @param mixed   $order_id   An array of Order IDs or an int.
	 *
	 * @return bool.
	 */
	public static function set_vendor_commission_paid( $vendors )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";

		if ( is_array( $vendors ) )
			$vendors = implode( ',', $vendors );

		$query  = "UPDATE `{$table_name}` SET `status` = 'paid' WHERE vendor_id IN ($vendors)";
		$result = $wpdb->query( $query );

		return $result;
	}


}
