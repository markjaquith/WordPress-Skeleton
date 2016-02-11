<?php

/**
 * Report views
 *
 * @author  Matt Gates <http://mgates.me>
 * @package ProductVendor
 */


class WCV_Vendor_Reports
{


	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->vendor_id = !current_user_can( 'manage_woocommerce' ) ? wp_get_current_user()->ID : '';
		if ( !empty ( $this->vendor_id ) ) {
			add_filter( 'woocommerce_reports_charts', array( $this, 'filter_tabs' ), 99 );
			add_filter( 'woocommerce_json_search_found_products', array( $this, 'filter_products_json' ) );
			add_filter( 'woocommerce_reports_product_sales_order_items', array( $this, 'filter_products' ) );
			add_filter( 'woocommerce_reports_top_sellers_order_items', array( $this, 'filter_products' ) );
			add_filter( 'woocommerce_reports_top_earners_order_items', array( $this, 'filter_products' ) );
		}
		
	}

	/**
	 * Show only reports that are useful to a vendor
	 *
	 * @param array $tabs
	 *
	 * @return array
	 */
	public function filter_tabs( $tabs )
	{
		global $woocommerce;

		$remove = array(
			'woocommerce_sales_overview',
			'woocommerce_daily_sales',
			'woocommerce_monthly_sales',
			'woocommerce_monthly_taxes',
			'woocommerce_category_sales',
			'woocommerce_coupon_sales',
		);		

		$reports = $tabs[ 'orders' ][ 'reports' ];

		foreach ( $reports as $key => $chart ) {
			if ( $key == 'coupon_usage' ) {
				unset( $tabs[ 'orders' ][ 'reports' ][ $key ] );
			}
		}

		// These are admin tabs
		$return = array(
			'orders' => $tabs[ 'orders' ]
		);

		return $return;
	}


	/**
	 * Filter products based on current vendor
	 *
	 * @param unknown $orders
	 *
	 * @return unknown
	 */
	public function filter_products( $orders )
	{
		$products = WCV_Vendors::get_vendor_products( $this->vendor_id );

		$ids = array();
		foreach ( $products as $product ) {
			$ids[ ] = ( $product->ID );
		}

		foreach ( $orders as $key => $order ) {

			if ( !in_array( $order->product_id, $ids ) ) {
				unset( $orders[ $key ] );
				continue;
			} else {
				if ( !empty( $order->line_total ) ) {
					$orders[ $key ]->line_total = WCV_Commission::calculate_commission( $order->line_total, $order->product_id, $order );
				}
			}

		}

		return $orders;
	}


	/**
	 *
	 *
	 * @param unknown $products
	 *
	 * @return unknown
	 */
	public function filter_products_json( $products )
	{
		$vendor_products = WCV_Vendors::get_vendor_products( $this->vendor_id );

		$ids = array();
		foreach ( $vendor_products as $vendor_product ) {
			$ids[ $vendor_product->ID ] = $vendor_product->post_title;
		}

		return array_intersect_key( $products, $ids );
	}


}
