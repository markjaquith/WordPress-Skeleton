<?php
require_once( 'PPBaseService.php' );
require_once( 'AdaptivePayments.php' );
require_once( 'PPUtils.php' );


/**
 * AUTO GENERATED code for AdaptivePayments
 */
class AdaptivePaymentsService extends PPBaseService
{

	// Service Version
	private static $SERVICE_VERSION = "1.8.1";

	// Service Name
	private static $SERVICE_NAME = "AdaptivePayments";

	public function __construct()
	{
		parent::__construct( 'AdaptivePayments' );
	}


	/**
	 * Service Call: CancelPreapproval
	 *
	 * @param CancelPreapprovalRequest $cancelPreapprovalRequest
	 *
	 * @return CancelPreapprovalResponse
	 * @throws APIException
	 */
	public function CancelPreapproval( $cancelPreapprovalRequest, $apiUsername = null )
	{
		$ret  = new CancelPreapprovalResponse();
		$resp = $this->call( "CancelPreapproval", $cancelPreapprovalRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: ConfirmPreapproval
	 *
	 * @param ConfirmPreapprovalRequest $confirmPreapprovalRequest
	 *
	 * @return ConfirmPreapprovalResponse
	 * @throws APIException
	 */
	public function ConfirmPreapproval( $confirmPreapprovalRequest, $apiUsername = null )
	{
		$ret  = new ConfirmPreapprovalResponse();
		$resp = $this->call( "ConfirmPreapproval", $confirmPreapprovalRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: ConvertCurrency
	 *
	 * @param ConvertCurrencyRequest $convertCurrencyRequest
	 *
	 * @return ConvertCurrencyResponse
	 * @throws APIException
	 */
	public function ConvertCurrency( $convertCurrencyRequest, $apiUsername = null )
	{
		$ret  = new ConvertCurrencyResponse();
		$resp = $this->call( "ConvertCurrency", $convertCurrencyRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: ExecutePayment
	 *
	 * @param ExecutePaymentRequest $executePaymentRequest
	 *
	 * @return ExecutePaymentResponse
	 * @throws APIException
	 */
	public function ExecutePayment( $executePaymentRequest, $apiUsername = null )
	{
		$ret  = new ExecutePaymentResponse();
		$resp = $this->call( "ExecutePayment", $executePaymentRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: GetAllowedFundingSources
	 *
	 * @param GetAllowedFundingSourcesRequest $getAllowedFundingSourcesRequest
	 *
	 * @return GetAllowedFundingSourcesResponse
	 * @throws APIException
	 */
	public function GetAllowedFundingSources( $getAllowedFundingSourcesRequest, $apiUsername = null )
	{
		$ret  = new GetAllowedFundingSourcesResponse();
		$resp = $this->call( "GetAllowedFundingSources", $getAllowedFundingSourcesRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: GetPaymentOptions
	 *
	 * @param GetPaymentOptionsRequest $getPaymentOptionsRequest
	 *
	 * @return GetPaymentOptionsResponse
	 * @throws APIException
	 */
	public function GetPaymentOptions( $getPaymentOptionsRequest, $apiUsername = null )
	{
		$ret  = new GetPaymentOptionsResponse();
		$resp = $this->call( "GetPaymentOptions", $getPaymentOptionsRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: PaymentDetails
	 *
	 * @param PaymentDetailsRequest $paymentDetailsRequest
	 *
	 * @return PaymentDetailsResponse
	 * @throws APIException
	 */
	public function PaymentDetails( $paymentDetailsRequest, $apiUsername = null )
	{
		$ret  = new PaymentDetailsResponse();
		$resp = $this->call( "PaymentDetails", $paymentDetailsRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: Pay
	 *
	 * @param PayRequest $payRequest
	 *
	 * @return PayResponse
	 * @throws APIException
	 */
	public function Pay( $payRequest, $apiUsername = null )
	{
		$ret  = new PayResponse();
		$resp = $this->call( "Pay", $payRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: PreapprovalDetails
	 *
	 * @param PreapprovalDetailsRequest $preapprovalDetailsRequest
	 *
	 * @return PreapprovalDetailsResponse
	 * @throws APIException
	 */
	public function PreapprovalDetails( $preapprovalDetailsRequest, $apiUsername = null )
	{
		$ret  = new PreapprovalDetailsResponse();
		$resp = $this->call( "PreapprovalDetails", $preapprovalDetailsRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: Preapproval
	 *
	 * @param PreapprovalRequest $preapprovalRequest
	 *
	 * @return PreapprovalResponse
	 * @throws APIException
	 */
	public function Preapproval( $preapprovalRequest, $apiUsername = null )
	{
		$ret  = new PreapprovalResponse();
		$resp = $this->call( "Preapproval", $preapprovalRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: Refund
	 *
	 * @param RefundRequest $refundRequest
	 *
	 * @return RefundResponse
	 * @throws APIException
	 */
	public function Refund( $refundRequest, $apiUsername = null )
	{
		$ret  = new RefundResponse();
		$resp = $this->call( "Refund", $refundRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: SetPaymentOptions
	 *
	 * @param SetPaymentOptionsRequest $setPaymentOptionsRequest
	 *
	 * @return SetPaymentOptionsResponse
	 * @throws APIException
	 */
	public function SetPaymentOptions( $setPaymentOptionsRequest, $apiUsername = null )
	{
		$ret  = new SetPaymentOptionsResponse();
		$resp = $this->call( "SetPaymentOptions", $setPaymentOptionsRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: GetFundingPlans
	 *
	 * @param GetFundingPlansRequest $getFundingPlansRequest
	 *
	 * @return GetFundingPlansResponse
	 * @throws APIException
	 */
	public function GetFundingPlans( $getFundingPlansRequest, $apiUsername = null )
	{
		$ret  = new GetFundingPlansResponse();
		$resp = $this->call( "GetFundingPlans", $getFundingPlansRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: GetAvailableShippingAddresses
	 *
	 * @param GetAvailableShippingAddressesRequest $getAvailableShippingAddressesRequest
	 *
	 * @return GetAvailableShippingAddressesResponse
	 * @throws APIException
	 */
	public function GetAvailableShippingAddresses( $getAvailableShippingAddressesRequest, $apiUsername = null )
	{
		$ret  = new GetAvailableShippingAddressesResponse();
		$resp = $this->call( "GetAvailableShippingAddresses", $getAvailableShippingAddressesRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: GetShippingAddresses
	 *
	 * @param GetShippingAddressesRequest $getShippingAddressesRequest
	 *
	 * @return GetShippingAddressesResponse
	 * @throws APIException
	 */
	public function GetShippingAddresses( $getShippingAddressesRequest, $apiUsername = null )
	{
		$ret  = new GetShippingAddressesResponse();
		$resp = $this->call( "GetShippingAddresses", $getShippingAddressesRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}


	/**
	 * Service Call: GetUserLimits
	 *
	 * @param GetUserLimitsRequest $getUserLimitsRequest
	 *
	 * @return GetUserLimitsResponse
	 * @throws APIException
	 */
	public function GetUserLimits( $getUserLimitsRequest, $apiUsername = null )
	{
		$ret  = new GetUserLimitsResponse();
		$resp = $this->call( "GetUserLimits", $getUserLimitsRequest, $apiUsername );
		$ret->init( PPUtils::nvpToMap( $resp ) );

		return $ret;
	}

}

?>