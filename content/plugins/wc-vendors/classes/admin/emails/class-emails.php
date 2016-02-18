<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *
 *
 * @author Matt Gates <http://mgates.me>
 * @package
 */


class WCV_Emails
{


	/**
	 *
	 */
	function __construct()
	{
		add_filter( 'woocommerce_email_classes', array( $this, 'check_items' ) );
		add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'order_action' ) );
		add_filter( 'woocommerce_order_product_title', array( 'WCV_Emails', 'show_vendor_in_email' ), 10, 2 );
		add_action( 'set_user_role', array( $this, 'application_status_email' ), 10, 2 );
		add_action( 'transition_post_status', array( $this, 'trigger_new_product' ), 10, 3 );

		add_filter( 'woocommerce_email_recipient_low_stock', 	array( $this, 'vendor_stock_email'), 10, 2 ); 
		add_filter( 'woocommerce_email_recipient_no_stock', 	array( $this, 'vendor_stock_email'), 10, 2 ); 
		add_filter( 'woocommerce_email_recipient_backorder', 	array( $this, 'vendor_stock_email'), 10, 2 ); 
	}

	public function trigger_new_product( $from, $to, $post )
	{
		global $woocommerce;

		if ( $from != $to && $post->post_status == 'pending' && WCV_Vendors::is_vendor( $post->post_author ) ) {
			$mails = $woocommerce->mailer()->get_emails();
			if ( !empty( $mails ) ) {
				$mails[ 'WC_Email_Notify_Admin' ]->trigger( $post->post_id, $post );
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $user_id
	 * @param unknown $role
	 */
	function application_status_email( $user_id, $role )
	{
		global $woocommerce;

		if ( !empty( $_POST[ 'apply_for_vendor' ] ) || ( !empty( $_GET[ 'action' ] ) && ( $_GET[ 'action' ] == 'approve_vendor' || $_GET[ 'action' ] == 'deny_vendor' ) ) ) {

			if ( $role == 'pending_vendor' ) {
				$status = __( 'pending', 'wcvendors' );
			} else if ( $role == 'vendor' ) {
				$status = __( 'approved', 'wcvendors' );
			} else if ( !empty( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'deny_vendor' ) {
				$status = __( 'denied', 'wcvendors' );
			}

			$mails = $woocommerce->mailer()->get_emails();

			if ( isset( $status ) && !empty( $mails ) ) {
				$mails[ 'WC_Email_Approve_Vendor' ]->trigger( $user_id, $status );
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @param unknown $_product
	 *
	 * @return unknown
	 */
	function show_vendor_in_email( $name, $_product )
	{
		$product = get_post( $_product->id );		
		$sold_by_label = WC_Vendors::$pv_options->get_option( 'sold_by_label' ); 
		$sold_by = WCV_Vendors::is_vendor( $product->post_author )
			? sprintf( '<a href="%s">%s</a>', WCV_Vendors::get_vendor_shop_page( $product->post_author ), WCV_Vendors::get_vendor_sold_by( $product->post_author ) )
			: get_bloginfo( 'name' );

		$name .= '<small class="wcvendors_sold_by_in_email"><br />' . apply_filters('wcvendors_sold_by_in_email', $sold_by_label ). $sold_by . '</small><br />';

		return $name;
	}


	/**
	 *
	 *
	 * @param unknown $available_emails
	 *
	 * @return unknown
	 */
	public function order_action( $available_emails )
	{
		$available_emails[ ] = 'vendor_new_order';

		return $available_emails;
	}


	/**
	 *
	 *
	 * @param unknown $emails
	 *
	 * @return unknown
	 */
	public function check_items( $emails )
	{
		require_once wcv_plugin_dir . 'classes/admin/emails/class-wc-notify-admin.php';
		require_once wcv_plugin_dir . 'classes/admin/emails/class-wc-notify-vendor.php';
		require_once wcv_plugin_dir . 'classes/admin/emails/class-wc-approve-vendor.php';
		require_once wcv_plugin_dir . 'classes/admin/emails/class-wc-notify-shipped.php';

		$emails[ 'WC_Email_Notify_Vendor' ]  = new WC_Email_Notify_Vendor();
		$emails[ 'WC_Email_Approve_Vendor' ] = new WC_Email_Approve_Vendor();
		$emails[ 'WC_Email_Notify_Admin' ]   = new WC_Email_Notify_Admin();
		$emails[ 'WC_Email_Notify_Shipped' ] = new WC_Email_Notify_Shipped();

		return $emails;
	}

	/**
	 *	 Add the vendor email to the low stock emails.  
	 *
	 */
	public function vendor_stock_email( $emails, $product ) { 

		if ( WCV_Vendors::is_vendor( $product->post->post_author ) ) { 
			$vendor_data = get_userdata( $product->post->post_author ); 
			$vendor_email = $vendor_data->user_email; 
			$emails .= ','.$vendor_email;  
		}

		return $emails; 

	}


}
