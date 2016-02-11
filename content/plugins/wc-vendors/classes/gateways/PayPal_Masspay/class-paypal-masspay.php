<?php

/**
 * Mass pay users
 *
 * @author Matt Gates <http://mgates.me>
 * @package
 */


class WCV_Mass_Pay
{

	private static $pluginDir;
	private $orders_paid; 

	/**
	 * Pay out all outstanding commission.
	 *
	 * @return array
	 */
	public function do_payments()
	{
		$vendors = array(); 
		self::$pluginDir = trailingslashit( dirname( __FILE__ ) );
		$vendors         = $this->get_users();

		return $this->pay_vendors( $vendors );
	}


	/**
	 * Include the PayPal API.
	 */
	private function include_paypal_sdk()
	{
		$path = self::$pluginDir . 'api/lib';
		set_include_path( get_include_path() . PATH_SEPARATOR . $path );
		require_once 'services/PayPalAPIInterfaceService/PayPalAPIInterfaceServiceService.php';
		require_once 'PPLoggingManager.php';
	}


	/**
	 * Retrieve all vendors and their due commission.
	 *
	 * @return array
	 */
	private function get_users()
	{
		$orders = WCV_Commission::get_all_due();
		if ( empty( $orders ) ) return false;

		//  Initialise the arrays
		$vendors = array(); 
		$due_amounts = array();

		foreach ( $orders as $data ) {
			$due_amounts[ $data->vendor_id ][ ] = $data->total_due;
			$this->orders_paid[ ]               = $data->id;
		}

		foreach ( $due_amounts as $vendor_id => $totals_due ) {
			$due_amounts[ $vendor_id ] = array_sum( $totals_due );
		}

		foreach ( $due_amounts as $vendor_id => $total_due ) {
			$user_id        = $vendor_id;
			$commission_due = $total_due;
			$paypal_email   = get_user_meta( $vendor_id, 'pv_paypal', true );

			// Skip vendors that haven't filled a paypal address
			// Or that don't have an outstanding balance
			if ( empty( $paypal_email ) || empty( $commission_due ) ) continue;

			// Who knows if it exists more than once. Let's not take a risk
			// Therefore, we add the total due to perhaps a previously existing one
			$vendors[ $paypal_email ] = array(
				'user_id'   => $user_id,
				'total_due' => !empty( $vendors[ $paypal_email ][ $user_id ][ 'total_due' ] )
						? $vendors[ $paypal_email ][ $user_id ][ 'total_due' ] + $commission_due
						: $commission_due,
			);
		}

		return $vendors;
	}


	/**
	 * Delete due commission for a vendor.
	 *
	 * @param array $users
	 *
	 * @return unknown
	 */
	private function purge_user_meta( $vendor_ids )
	{
		global $wpdb;

		return WCV_Commission::set_vendor_commission_paid( $vendor_ids );
	}


	/**
	 * Pay out all due commission.
	 *
	 * @param array $vendors
	 *
	 * @return array
	 */
	private function pay_vendors( $vendors )
	{
		if ( empty( $vendors ) ) {
			$return = array( 'status' => 'error', 'msg' => __( 'No vendors found to pay. Maybe they haven\'t set a PayPal address?', 'wcvendors' ) );
			$this->mail_results( $return );

			return $return;
		}

		$vendor_ids = array(); 

		$this->include_paypal_sdk();

		$logger                      = new PPLoggingManager( 'MassPay' );
		$massPayRequest              = new MassPayRequestType();
		$massPayRequest->MassPayItem = array();

		$total_pay = 0;
		foreach ( $vendors as $user_paypal => $user ) {
			// Don't attempt to process payments for users that owe the admin money
			if ( $user[ 'total_due' ] <= 0 ) continue;

			$total_pay += $user[ 'total_due' ];
			$masspayItem                    = new MassPayRequestItemType();
			$masspayItem->Amount            = new BasicAmountType( get_woocommerce_currency(), $user[ 'total_due' ] );
			$masspayItem->ReceiverEmail     = $user_paypal;
			$massPayRequest->MassPayItem[ ] = $masspayItem;
			$vendor_ids[]					= $user['user_id'];
		}

		$massPayReq                 = new MassPayReq();
		$massPayReq->MassPayRequest = $massPayRequest;

		$paypalService = new PayPalAPIInterfaceServiceService();

		// Wrap API method calls on the service object with a try catch
		try {
			$massPayResponse = $paypalService->MassPay( $massPayReq );
		} catch ( Exception $ex ) {
			$return = array(
				'status' => 'error',
				'msg'    => sprintf( __( 'Error: %s', 'wcvendors' ), $ex->getMessage() ),
				'total'  => $total_pay,
			);

			return $return;
		}

		$return = array();

		if ( isset( $massPayResponse ) ) {
			if ( $massPayResponse->Ack === 'Success' ) {
				if ( $this->purge_user_meta( $vendor_ids ) ) {
					$return = array(
						'status' => 'updated',
						'msg'    => __( 'All due commission has been paid for.', 'wcvendors' ),
						'total'  => $total_pay,
					);
				} else {
					$return = array(
						'status' => 'error',
						'msg'    => __( 'All due commission has been paid for, but I could not clear it from their profiles due to an internal error. Commission will still be listed as due. Please manually mark the commission as paid from the Commissions page.', 'wcvendors' ),
						'total'  => $total_pay,
					);
				}
			} else {
				$return = array(
					'status' => 'error',
					'msg'    => sprintf( '%s. %s (%s): %s.', $massPayResponse->Ack, $massPayResponse->Errors->ShortMessage, $massPayResponse->Errors->ErrorCode, $massPayResponse->Errors->LongMessage ),
					'total'  => $total_pay,
				);
			}
		}

		$this->mail_results( $return );

		return $return;
	}


	/**
	 * Mail the manual payment results.
	 *
	 * @param unknown $result
	 *
	 * @return unknown
	 */
	private function mail_results( $result )
	{
		global $woocommerce;

		$send_results = WC_Vendors::$pv_options->get_option( 'mail_mass_pay_results' );

		if ( !$send_results ) return false;

		$to      = sanitize_email( get_option( 'woocommerce_email_from_address' ) );
		$subject = __( 'WooCommerce: Mass payments for vendors update', 'wcvendors' );

		$message = __( 'Hello! A payment was just triggered to mass pay all vendors their due commission.', 'wcvendors' ) . PHP_EOL . PHP_EOL;
		$message .= sprintf( __( 'Payment status: %s.', 'wcvendors' ), $result[ 'status' ] ) . PHP_EOL;
		$message .= sprintf( __( 'Payment message: %s.', 'wcvendors' ), $result[ 'msg' ] ) . PHP_EOL;

		if ( !empty( $result[ 'total' ] ) )
			$message .= sprintf( __( 'Payment total: %s.', 'wcvendors' ), $result[ 'total' ] );

		$sent = wp_mail( $to, $subject, $message, "From: " . $to . "\r\n" );

		return $sent;
	}


}
