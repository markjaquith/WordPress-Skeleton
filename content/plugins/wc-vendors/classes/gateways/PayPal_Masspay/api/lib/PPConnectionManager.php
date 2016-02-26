<?php
require_once 'PPHttpConnection.php';
require_once 'PPConfigManager.php';
class PPConnectionManager
{
	/**
	 * reference to singleton instance
	 * @var PPConnectionManager
	 */
	private static $instance;

	private function __construct()
	{
	}

	public static function getInstance()
	{
		if ( self::$instance == null ) {
			self::$instance = new PPConnectionManager();
		}

		return self::$instance;
	}

	/**
	 * This function returns a new PPHttpConnection object
	 */
	public function getConnection()
	{

		$connection = new PPHttpConnection();

		$configMgr = PPConfigManager::getInstance();
		if ( ( $configMgr->get( "http.ConnectionTimeOut" ) ) ) {
			$connection->setHttpTimeout( $configMgr->get( "http.ConnectionTimeOut" ) );
		}
		if ( $configMgr->get( "http.Proxy" ) ) {
			$connection->setHttpProxy( $configMgr->get( "http.Proxy" ) );
		}
		if ( $configMgr->get( "http.Retry" ) ) {
			$retry = $configMgr->get( "http.Retry" );
			$connection->setHttpRetry( $retry );
		}

		return $connection;
	}

}
