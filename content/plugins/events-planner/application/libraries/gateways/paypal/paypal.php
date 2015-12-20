<?php

/* http://coding.smashingmagazine.com/2011/09/05/getting-started-with-the-paypal-api/ */

class EPL_Paypal {

    /**
     * Last error message(s)
     * @var array
     */
    protected $_errors = array( );
    /**
     * API Credentials
     * Use the correct credentials for the environment in use (Live / Sandbox)
     * @var array
     */
    protected $_credentials;
    /**
     * API endpoint
     * Live - https://api-3t.paypal.com/nvp
     * Sandbox - https://api-3t.sandbox.paypal.com/nvp
     * @var string
     */
    protected $_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';
    /**
     * API Version
     * @var string
     */
    protected $_version = '74.0';


    /**
     * Make API request
     *
     * @param string $method string API method to request
     * @param array $params Additional request parameters
     * @return array / boolean Response array / boolean false on failure
     */
    public function request( $method, $params = array( ) ) {
        $this->_errors = array( );
        if ( empty( $method ) ) { //Check if API method is not empty
            $this->_errors = array( 'API method is missing' );
            return false;
        }

        $this->erm = EPL_Base::get_instance()->load_model( 'epl-registration-model' );
        $gateway_info = $this->erm->get_gateway_info();

        $this->_credentials = array(
            'USER' => trim($gateway_info['_epl_pp_exp_user']),
            'PWD' => trim($gateway_info['_epl_pp_exp_pwd']),
            'SIGNATURE' => trim($gateway_info['_epl_pp_exp_sig']),
        );

        if ($gateway_info['_epl_sandbox'] == 0)
            $this->_endPoint = 'https://api-3t.paypal.com/nvp';


        //Our request parameters
        $requestParams = array(
            'METHOD' => $method,
            'VERSION' => $this->_version
                ) + $this->_credentials;
 //echo "<pre class='prettyprint'>" . print_r($this->_endPoint , true). "</pre>";
 //echo "<pre class='prettyprint'>" . print_r($requestParams , true). "</pre>";
        //Building our NVP string
        $request = http_build_query( $requestParams + $params );

        //cURL settings
        $curlOptions = array(
            CURLOPT_URL => $this->_endPoint,
            CURLOPT_VERBOSE => 1,
            //CURLOPT_SSL_VERIFYPEER => true,
            //CURLOPT_SSL_VERIFYHOST => 2,
            //CURLOPT_CAINFO => dirname( __FILE__ ) . '/cacert.pem', //CA cert file


            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request
        );

        $ch = curl_init();
        curl_setopt_array( $ch, $curlOptions );

        //Sending our request - $response will hold the API response
        $response = curl_exec( $ch );

        //Checking for cURL errors
        if ( curl_errno( $ch ) ) {
            $this->_errors = curl_error( $ch );
            curl_close( $ch );
            return false;
            //Handle errors
        }
        else {
            curl_close( $ch );
            $responseArray = array( );
            parse_str( $response, $responseArray ); // Break the NVP string to an array
            return $responseArray;
        }
    }

}