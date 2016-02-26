<?php

/**
 * Vendor functions
 *
 * @author  Matt Gates <http://mgates.me>, WC Vendors <http://wcvendors.com>
 * @package WCVendors
 */


class WCV_Vendors
{

	/**
	 * Retrieve all products for a vendor
	 *
	 * @param int $vendor_id
	 *
	 * @return object
	 */
	public static function get_vendor_products( $vendor_id )
	{
		$args = array(
			'numberposts' => -1,
			'post_type'   => 'product',
			'author'      => $vendor_id,
			'post_status' => 'publish',
		);

		$args = apply_filters( 'pv_get_vendor_products_args', $args );

		return get_posts( $args );
	}

	public static function get_default_commission( $vendor_id )
	{
		return get_user_meta( $vendor_id, 'pv_custom_commission_rate', true );
	}


	/**
	 * Vendor IDs and PayPal addresses from an order
	 *
	 * @param object  $order
	 * @param unknown $items (optional)
	 *
	 * @return array
	 */
	public static function get_vendors_from_order( $order, $items = false )
	{
		if ( !$order ) return;
		if ( !$items ) $items = $order->get_items();

		$vendors = array();
		foreach ( $items as $key => $product ) {

			$author = WCV_Vendors::get_vendor_from_product( $product[ 'product_id' ] );

			// Only store the vendor authors
			if ( !WCV_Vendors::is_vendor( $author ) ) continue;

			$vendors[ $author ] = the_author_meta( 'author_paypal', $author );
		}

		return apply_filters( 'pv_vendors_from_order', $vendors, $order );
	}


	/**
	 *
	 *
	 * @param unknown $order
	 * @param unknown $group (optional)
	 *
	 * @return unknown
	 */
	public static function get_vendor_dues_from_order( $order, $group = true )
	{
		global $woocommerce;

		$give_tax       	= WC_Vendors::$pv_options->get_option( 'give_tax' );
		$give_shipping 		= WC_Vendors::$pv_options->get_option( 'give_shipping' );
		$receiver      		= array();
		$shipping_given 	= 0;
		$tax_given      	= 0;

		WCV_Shipping::$pps_shipping_costs = array();

		foreach ( $order->get_items() as $key => $product ) {

			$product_id 				= !empty( $product[ 'variation_id' ] ) ? $product[ 'variation_id' ] : $product[ 'product_id' ];
			$author     				= WCV_Vendors::get_vendor_from_product( $product_id );
			$give_tax_override 			= get_user_meta( $author, 'wcv_give_vendor_tax', true ); 
			$give_shipping_override 	= get_user_meta( $author, 'wcv_give_vendor_shipping', true ); 
			$is_vendor  				= WCV_Vendors::is_vendor( $author );
			$commission 				= $is_vendor ? WCV_Commission::calculate_commission( $product[ 'line_subtotal' ], $product_id, $order ) : 0;
			$tax        				= !empty( $product[ 'line_tax' ] ) ? (float) $product[ 'line_tax' ] : 0;
			
			// Check if shipping is enabled
			if ( get_option('woocommerce_calc_shipping') === 'no' ) { 
				$shipping = 0; $shipping_tax = 0; 
			} else {
					$shipping_costs = WCV_Shipping::get_shipping_due( $order->id, $product, $author );
					$shipping = $shipping_costs['amount']; 
					$shipping_tax = $shipping_costs['tax']; 
			}

			// Add line item tax and shipping taxes together 
			$total_tax = (float) $tax + (float) $shipping_tax; 

			// Tax override on a per vendor basis
			if ( $give_tax_override ) $give_tax = true; 
			// Shipping override 
			if ( $give_shipping_override ) $give_shipping = true; 

			if ( $is_vendor ) {

				$shipping_given += $give_shipping ? $shipping : 0;
				$tax_given += $give_tax ? $total_tax : 0;

				$give = 0;
				$give += !empty( $receiver[ $author ][ 'total' ] ) ? $receiver[ $author ][ 'total' ] : 0;
				$give += $give_shipping ? $shipping : 0;
				$give += $commission;
				$give += $give_tax ? $total_tax : 0;

				if ( $group ) {

					$receiver[ $author ] = array(
						'vendor_id'  => (int) $author,
						'commission' => !empty( $receiver[ $author ][ 'commission' ] ) ? $receiver[ $author ][ 'commission' ] + $commission : $commission,
						'shipping'   => $give_shipping ? ( !empty( $receiver[ $author ][ 'shipping' ] ) ? $receiver[ $author ][ 'shipping' ] + $shipping : $shipping) : 0,
						'tax'        => $give_tax ? ( !empty( $receiver[ $author ][ 'tax' ] ) ? $receiver[ $author ][ 'tax' ] + $total_tax : $total_tax ) : 0,
						'qty'        => !empty( $receiver[ $author ][ 'qty' ] ) ? $receiver[ $author ][ 'qty' ] + $product[ 'qty' ] : $product[ 'qty' ],
						'total'      => $give,
					);

				} else {

					$receiver[ $author ][ $key ] = array(
						'vendor_id'  => (int) $author,
						'product_id' => $product_id,
						'commission' => $commission,
						'shipping'   => $give_shipping ? $shipping : 0,
						'tax'        => $give_tax ? $total_tax : 0,
						'qty'        => $product[ 'qty' ],
						'total'      => ($give_shipping ? $shipping : 0) + $commission + ( $give_tax ? $total_tax : 0 ),
					);

				}

			}

			$admin_comm = $product[ 'line_subtotal' ] - $commission;

			if ( $group ) {
				$receiver[ 1 ] = array(
					'vendor_id'  => 1,
					'qty'        => !empty( $receiver[ 1 ][ 'qty' ] ) ? $receiver[ 1 ][ 'qty' ] + $product[ 'qty' ] : $product[ 'qty' ],
					'commission' => !empty( $receiver[ 1 ][ 'commission' ] ) ? $receiver[ 1 ][ 'commission' ] + $admin_comm : $admin_comm,
					'total'      => !empty( $receiver[ 1 ] ) ? $receiver[ 1 ][ 'total' ] + $admin_comm : $admin_comm,
				);
			} else {
				$receiver[ 1 ][ $key ] = array(
					'vendor_id'  => 1,
					'product_id' => $product_id,
					'commission' => $admin_comm,
					'shipping'   => 0,
					'tax'        => 0,
					'qty'        => $product[ 'qty' ],
					'total'      => $admin_comm,
				);
			}

		}
		
		// Add remainders on end to admin
		$discount = $order->get_total_discount();
		$shipping = ( $order->order_shipping - $shipping_given );
		$tax = round(( $order->order_tax + $order->order_shipping_tax ) - $tax_given, 2); 
		$total    = ( $tax + $shipping ) - $discount;

		if ( $group ) {
			$receiver[ 1 ][ 'commission' ] = $receiver[ 1 ][ 'commission' ] - $discount;
			$receiver[ 1 ][ 'shipping' ]   = $shipping;
			$receiver[ 1 ][ 'tax' ]        = $tax;
			$receiver[ 1 ][ 'total' ] += $total;
		} else {
			$receiver[ 1 ][ $key ][ 'commission' ] = $receiver[ 1 ][ $key ][ 'commission' ] - $discount;
			$receiver[ 1 ][ $key ][ 'shipping' ]   = ( $order->order_shipping - $shipping_given );
			$receiver[ 1 ][ $key ][ 'tax' ]        = $tax;
			$receiver[ 1 ][ $key ][ 'total' ] += $total;
		}

		// Reset the array keys
		// $receivers = array_values( $receiver );

		return $receiver;
	}


	/**
	 * Return the PayPal address for a vendor
	 *
	 * If no PayPal is set, it returns the vendor's email
	 *
	 * @param int $vendor_id
	 *
	 * @return string
	 */
	public static function get_vendor_paypal( $vendor_id )
	{
		$paypal = get_user_meta( $vendor_id, $meta_key = 'pv_paypal', true );
		$paypal = !empty( $paypal ) ? $paypal : get_the_author_meta( 'user_email', $vendor_id, false );

		return $paypal;
	}


	/**
	 * Check if a vendor has an amount due for an order already
	 *
	 * @param int $vendor_id
	 * @param int $order_id
	 *
	 * @return int
	 */
	public static function count_due_by_vendor( $vendor_id, $order_id )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";

		$query = "SELECT COUNT(*)
					FROM {$table_name}
					WHERE vendor_id = %s
					AND order_id = %s
					AND status = %s";
		$count = $wpdb->get_var( $wpdb->prepare( $query, $vendor_id, $order_id, 'due' ) );

		return $count;
	}


	/**
	 * All commission due for a specific vendor
	 *
	 * @param int $vendor_id
	 *
	 * @return int
	 */
	public static function get_due_orders_by_vendor( $vendor_id )
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "pv_commission";

		$query   = "SELECT *
					FROM {$table_name}
					WHERE vendor_id = %s
					AND status = %s";
		$results = $wpdb->get_results( $wpdb->prepare( $query, $vendor_id, 'due' ) );

		return $results;
	}


	/**
	 *
	 *
	 * @param unknown $product_id
	 *
	 * @return unknown
	 */
	public static function get_vendor_from_product( $product_id )
	{
		// Make sure we are returning an author for products or product variations only 
		if ( 'product' === get_post_type( $product_id ) || 'product_variation' === get_post_type( $product_id ) ) { 
			$parent = get_post_ancestors( $product_id );
			if ( $parent ) $product_id = $parent[ 0 ];

			$post = get_post( $product_id );
			$author = $post ? $post->post_author : 1;
			$author = apply_filters( 'pv_product_author', $author, $product_id );
		} else { 
			$author = -1; 
		}
		return $author;
	}


	/**
	 * Checks whether the ID provided is vendor capable or not
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public static function is_vendor( $user_id )
	{
		$user = get_userdata( $user_id ); 
		
		if (is_object($user)) { 
			$is_vendor = is_array( $user->roles ) ? in_array( 'vendor', $user->roles ) : false;
		} else { 
			$is_vendor = false; 
		}

		return apply_filters( 'pv_is_vendor', $is_vendor, $user_id );
	}


	/**
	 * Grabs the vendor ID whether a username or an int is provided
	 * and returns the vendor_id if it's actually a vendor
	 *
	 * @param unknown $input
	 *
	 * @return unknown
	 */
	public static function get_vendor_id( $input )
	{
		if ( empty( $input ) ) {
			return false;
		}

		$users = get_users( array( 'meta_key' => 'pv_shop_slug', 'meta_value' => sanitize_title( $input ) ) );

		if ( !empty( $users ) && count( $users ) == 1 ) {
			$vendor = $users[ 0 ];
		} else {
			$int_vendor = is_numeric( $input );
			$vendor     = !empty( $int_vendor ) ? get_userdata( $input ) : get_user_by( 'login', $input );
		}

		if ( $vendor ) {
			$vendor_id = $vendor->ID;
			if ( self::is_vendor( $vendor_id ) ) {
				return $vendor_id;
			}
		}

		return false;
	}


	/**
	 * Retrieve the shop page for a specific vendor
	 *
	 * @param unknown $vendor_id
	 *
	 * @return string
	 */
	public static function get_vendor_shop_page( $vendor_id )
	{
		$vendor_id = self::get_vendor_id( $vendor_id );
		if ( !$vendor_id ) return;

		$slug   = get_user_meta( $vendor_id, 'pv_shop_slug', true );
		$vendor = !$slug ? get_userdata( $vendor_id )->user_login : $slug;

		if ( get_option( 'permalink_structure' ) ) {
			$permalink = trailingslashit( WC_Vendors::$pv_options->get_option( 'vendor_shop_permalink' ) );

			return trailingslashit( home_url( sprintf( '/%s%s', $permalink, $vendor ) ) );
		} else {
			return esc_url( add_query_arg( array( 'vendor_shop' => $vendor ), get_post_type_archive_link( 'product' ) ) );
		}
	}


	/**
	 * Retrieve the shop name for a specific vendor
	 *
	 * @param unknown $vendor_id
	 *
	 * @return string
	 */
	public static function get_vendor_shop_name( $vendor_id )
	{
		$vendor_id = self::get_vendor_id( $vendor_id );
		$name      = $vendor_id ? get_user_meta( $vendor_id, 'pv_shop_name', true ) : false;
		$shop_name = !$name ? get_userdata( $vendor_id )->user_login : $name;

		return $shop_name;
	}


	/**
	 *
	 *
	 * @param unknown $user_id
	 *
	 * @return unknown
	 */
	public static function is_pending( $user_id )
	{
		$user = get_userdata( $user_id );

		$role       = !empty( $user->roles ) ? array_shift( $user->roles ) : false;
		$is_pending = ( $role == 'pending_vendor' );

		return $is_pending;
	}

	/* 
	* 	Is this a vendor product ? 
	* 	@param uknown $role 
	*/ 
	public static function is_vendor_product($role) { 
		return ($role === 'Vendor') ? true : false; 
	}

	/* 
	*	Is this the vendors shop archive page ? 
	*/
	public static function is_vendor_page() { 

		$vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
		$vendor_id   = WCV_Vendors::get_vendor_id( $vendor_shop );

		return $vendor_id ? true : false; 

	}

	/* 
	*	Is this a vendor single product page ? 
	*/
	public static function is_vendor_product_page($vendor_id) { 

		$vendor_product = WCV_Vendors::is_vendor_product( wcv_get_user_role($vendor_id) ); 
		return $vendor_product ? true : false; 

	}

	public static function get_vendor_sold_by( $vendor_id ){ 

		$vendor_display_name = WC_Vendors::$pv_options->get_option( 'vendor_display_name' ); 
		$vendor =  get_userdata( $vendor_id ); 

		switch ($vendor_display_name) {
			case 'display_name':
				$display_name = $vendor->display_name;
				break;
			case 'user_login': 
				$display_name = $vendor->user_login;
				break;
			case 'user_email': 
				$display_name = $vendor->user_email;
				break;

			default:
				$display_name = WCV_Vendors::get_vendor_shop_name( $vendor_id ); 
				break;
		}

		return $display_name; 
	}

}