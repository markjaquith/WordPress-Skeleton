<?php

class WCV_Submit_Comment
{

	/**
	 * Submit a comment for an order
	 *
	 * @param object $orders
	 *
	 * @return unknown
	 */
	public static function new_comment( $orders )
	{
		global $woocommerce;

		$user = wp_get_current_user();
		$user = $user->ID;

		// Security
		if ( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'add-comment' ) ) return false;

		// Check if this product belongs to the vendor submitting the comment
		$product_id = (int) $_POST[ 'product_id' ];
		$author     = WCV_Vendors::get_vendor_from_product( $product_id );
		if ( $author != $user ) return false;

		// Find the order belonging to this comment
		foreach ( $orders as $order ) {
			if ( $order->order_id == $_POST[ 'order_id' ] ) {
				$found_order = $order;
				break;
			}
		}

		// No order was found
		if ( empty( $found_order ) ) return false;

		// Don't submit empty comments
		if ( empty( $_POST[ 'comment_text' ] ) ) {
			wc_add_notice( __( 'You\'ve left the comment field empty!', 'wcvendors' ), 'error' );

			return false;
		}

		// Only submit if the order has the product belonging to this vendor
		$found_order = new WC_Order ( $found_order->order_id );
		$valid_order = false;
		foreach ( $found_order->get_items() as $item ) {
			if ( $item[ 'product_id' ] == $product_id ) {
				$valid_order = true;
				break;
			}
		}

		if ( $valid_order ) {
			$comment = esc_textarea( $_POST[ 'comment_text' ] );

			add_filter( 'woocommerce_new_order_note_data', array( __CLASS__, 'filter_comment' ), 10, 2 );
			$found_order->add_order_note( $comment, 1 );
			remove_filter( 'woocommerce_new_order_note_data', array( __CLASS__, 'filter_comment' ), 10, 2 );

			wc_add_notice( __( 'Success. The customer has been notified of your comment.', 'wcvendors' ), 'success' );
		}

	}

	public static function filter_comment( $commentdata, $order )
	{
		$user_id = get_current_user_id();

		$commentdata[ 'user_id' ]              = $user_id;
		$commentdata[ 'comment_author' ]       = WCV_Vendors::get_vendor_shop_name( $user_id );
		$commentdata[ 'comment_author_url' ]   = WCV_Vendors::get_vendor_shop_page( $user_id );
		$commentdata[ 'comment_author_email' ] = wp_get_current_user()->user_email;

		return $commentdata;
	}


}
