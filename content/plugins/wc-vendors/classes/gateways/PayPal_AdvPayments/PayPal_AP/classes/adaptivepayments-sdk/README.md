
PayPal PHP Adaptive Payments SDK
===============================

Prerequisites
-------------

PayPal's PHP Adaptive Payments SDK requires 

 * PHP 5.2 and above with curl extension enabled
  

Using the SDK
-------------

To use the SDK, 

* Copy the config and lib folders into your project.
* Make sure that the lib folder in your project is available in PHP's include path
* Include the services\AdaptivePayments\AdaptivePaymentsService.php file in your code.
* Create a service wrapper object
* Create a request object as per your project's needs. All the API request and response classes are available in services\AdaptivePayments\AdaptivePaymentsService.php
* Invoke the appropriate method on the request object.

For example,

	require_once('services/AdaptivePayments/AdaptivePaymentsService.php');
	require_once('PPLoggingManager.php');

    $payRequest = new PayRequest($requestEnvelope, $_POST['actionType'], $_POST['cancelUrl'], $_POST['currencyCode'], $receiverList, $_POST['returnUrl']);
    // Add optional params
    if($_POST["feesPayer"] != "") {
	$payRequest->feesPayer = $_POST["feesPayer"];
    }
	......

	$service  = new AdaptivePaymentsService();
	$response = $service->Pay($payRequest);
	
	$ack = strtoupper($response->responseEnvelope->ack);
 
	if($ack == 'SUCCESS') {
		// Success
	}
  
 

SDK Configuration
-----------------

replace the API credential in config/sdk_config.ini . You can use the configuration file to configure

 * (Multiple) API account credentials.
 * Service endpoint and other HTTP connection parameters 


Please refer to the sample config file provided with this bundle.
