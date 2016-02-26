<?php

require_once 'exceptions/PPConnectionException.php';
require_once 'exceptions/PPConfigurationException.php';
require_once 'PPLoggingManager.php';
/**
 * A wrapper class based on the curl extension.
 * Requires the PHP curl module to be enabled.
 * See for full requirements the PHP manual: http://php.net/curl
 */


class PPHttpConnection
{

	const HTTP_GET  = 'GET';
	const HTTP_POST = 'POST';

	/**
	 * curl options to be set for the request
	 */
	private $curlOpt = array();

	/**
	 * Number of times a retry must be attempted on getting an HTTP error
	 * @var integer
	 */
	private $retry;

	/**
	 * HTTP status codes for which a retry must be attempted
	 */
	private static $retryCodes = array( '401', '403', '404', );

	private $logger;


	/**
	 * Some default options for curl
	 * These are typically overridden by PPConnectionManager
	 */
	public static $DEFAULT_CURL_OPTS = array(
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT        => 60, // maximum number of seconds to allow cURL functions to execute
		CURLOPT_USERAGENT      => 'PayPal-PHP-SDK',
		CURLOPT_POST           => 1,
		CURLOPT_HTTPHEADER     => array(),
		CURLOPT_SSL_VERIFYHOST => 1,
		CURLOPT_SSL_VERIFYPEER => 2
	);

	public function __construct()
	{
		if ( !function_exists( "curl_init" ) ) {
			throw new PPConfigurationException( "Curl module is not available on this system" );
		}
		$this->curlOpt = self::$DEFAULT_CURL_OPTS;
		$this->logger  = new PPLoggingManager( __CLASS__ );
	}

	/**
	 * Set ssl parameters for certificate based client authentication
	 *
	 * @param string $certPath - path to client certificate file (PEM formatted file)
	 */
	public function setSSLCert( $certPath, $passPhrase )
	{
		$this->curlOpt[ CURLOPT_SSLCERT ]       = realpath( $certPath );
		$this->curlOpt[ CURLOPT_SSLCERTPASSWD ] = $passPhrase;
	}

	/**
	 * Set connection timeout in seconds
	 *
	 * @param integer $timeout
	 */
	public function setHttpTimeout( $timeout )
	{
		$this->curlOpt[ CURLOPT_CONNECTTIMEOUT ] = $timeout;
	}

	/**
	 * Set HTTP proxy information
	 *
	 * @param string $proxy
	 *
	 * @throws PPConfigurationException
	 */
	public function setHttpProxy( $proxy )
	{
		$urlParts = parse_url( $proxy );
		if ( $urlParts == false || !array_key_exists( "host", $urlParts ) )
			throw new PPConfigurationException( "Invalid proxy configuration " . $proxy );

		$this->curlOpt[ CURLOPT_PROXY ] = $urlParts[ "host" ];
		if ( isset( $urlParts[ "port" ] ) )
			$this->curlOpt[ CURLOPT_PROXY ] .= ":" . $urlParts[ "port" ];
		if ( isset( $urlParts[ "user" ] ) )
			$this->curlOpt[ URLOPT_PROXYUSERPWD ] = $urlParts[ "user" ] . ":" . $urlParts[ "pass" ];
	}

	public function setHttpHeaders( $headers )
	{
		$this->curlOpt[ CURLOPT_HTTPHEADER ] = $headers;
	}

	/**
	 * @param integer $retry
	 */
	public function setHttpRetry( $retry )
	{
		$this->retry = $retry;
	}

	/**
	 * Executes an HTTP request
	 *
	 * @param string $url
	 * @param string $params  query string OR POST content as a string
	 * @param array  $headers array of HTTP headers to be added to existing headers
	 * @param string $method  HTTP method (GET, POST etc) defaults to POST
	 *
	 * @throws PPConnectionException
	 */
	public function execute( $url, $params, $headers = null, $method = null )
	{

		$ch = curl_init( $url );

		$this->curlOpt[ CURLOPT_POSTFIELDS ] = $params;
		$this->curlOpt[ CURLOPT_URL ]        = $url;
		$this->curlOpt[ CURLOPT_HEADER ]     = false;
		if ( isset( $headers ) ) {
			$this->curlOpt[ CURLOPT_HTTPHEADER ] = array_merge( $this->curlOpt[ CURLOPT_HTTPHEADER ], $headers );
		}
		foreach ( $this->curlOpt[ CURLOPT_HTTPHEADER ] as $header ) {
			//TODO: Strip out credentials when logging.
			$this->logger->info( "Adding header $header" );
		}
		if ( isset( $method ) ) {
			$this->curlOpt[ CURLOPT_CUSTOMREQUEST ] = $method;
		}

		// curl_setopt_array available only in PHP 5 >= 5.1.3
		curl_setopt_array( $ch, $this->curlOpt );

		$result = curl_exec( $ch );

		if ( curl_errno( $ch ) == 60 ) {
			$this->logger->info( "Invalid or no certificate authority found, retrying using bundled CA certs" );
			curl_setopt( $ch, CURLOPT_CAINFO,
				dirname( __FILE__ ) . '/cacert.pem' );
			$result = curl_exec( $ch );
		}
		$httpStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$retries    = 0;
		if ( in_array( $httpStatus, self::$retryCodes ) && isset( $this->retry ) ) {
			$this->logger->info( "Got $httpStatus response from server. Retrying" );

			do {
				$result     = curl_exec( $ch );
				$httpStatus = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

			} while ( in_array( $httpStatus, self::$retryCodes ) && ++$retries < $this->retry );


		}
		if ( curl_errno( $ch ) ) {
			$ex = new PPConnectionException( $url, curl_error( $ch ), curl_errno( $ch ) );
			curl_close( $ch );
			throw $ex;
		}

		curl_close( $ch );

		if ( in_array( $httpStatus, self::$retryCodes ) ) {
			throw new PPConnectionException( $url, "Retried " . $retries . " times, Http Response code " . $httpStatus );
		}

		return $result;
	}

}
