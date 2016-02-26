<?php
require_once 'PPCredentialManager.php';
require_once 'PPConnectionManager.php';
require_once 'PPObjectTransformer.php';
require_once 'PPLoggingManager.php';
require_once 'PPUtils.php';
require_once 'PPAuthenticationManager.php';

class PPAPIService
{
	public $endpoint;
	public $serviceName;
	private $logger;

	public function __construct( $serviceName = "" )
	{
		$this->serviceName = $serviceName;
		$config            = PPConfigManager::getInstance();
		$this->endpoint    = $config->get( 'service.EndPoint' );
		$this->logger      = new PPLoggingManager( __CLASS__ );
	}

	public function setServiceName( $serviceName )
	{
		$this->serviceName = $serviceName;
	}


	public function makeRequest( $apiMethod, $params, $apiUsername = null, $accessToken = null, $tokenSecret = null )
	{

		$authentication = new PPAuthenticationManager();
		$connectionMgr  = PPConnectionManager::getInstance();
		$connection     = $connectionMgr->getConnection();

		$credMgr = PPCredentialManager::getInstance();
		// $apiUsernam is optional, if null the default account in congif file is taken
		$apIPPCredential = $credMgr->getCredentialObject( $apiUsername );
		$config          = PPConfigManager::getInstance();
		if ( $config->get( 'service.Binding' ) == 'SOAP' ) {
			$url = $this->endpoint;
			if ( isset( $accessToken ) && isset( $tokenSecret ) ) {
				$headers = $authentication->getPayPalHeaders( $apIPPCredential, $connection, $accessToken, $tokenSecret, $url );
			} else {
				$headers = null;
			}
			$params = $authentication->appendSoapHeader( $params, $apIPPCredential, $connection, $accessToken, $tokenSecret, $url );
		} else {
			$url     = $this->endpoint . $this->serviceName . '/' . $apiMethod;
			$headers = $authentication->getPayPalHeaders( $apIPPCredential, $connection, $accessToken, $tokenSecret, $url );
		}


		$this->logger->info( "Request: $params" );
		$response = $connection->execute( $url, $params, $headers );
		$this->logger->info( "Response: $response" );

		return $response;
	}


}
