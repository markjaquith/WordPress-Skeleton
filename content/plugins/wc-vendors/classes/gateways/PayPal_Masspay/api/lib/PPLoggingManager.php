<?php

/**
 * Simple Logging Manager.
 * This does an error_log for now
 * Potential frameworks to use are PEAR logger, log4php from Apache
 */

class PPLoggingLevel
{

	// FINE Logging Level
	const FINE = 3;

	// INFO Logging Level
	const INFO = 2;

	// WARN Logging Level
	const WARN = 1;

	// ERROR Logging Level
	const ERROR = 0;
}

class PPLoggingManager
{

	// Default Logging Level
	const DEFAULT_LOGGING_LEVEL = 0;

	// Logger name
	private $loggerName;

	// Log enabled
	private $isLoggingEnabled;

	// Configured logging level
	private $loggingLevel;

	// Configured logging file
	private $loggerFile;

	public function __construct( $loggerName )
	{
		$this->loggerName       = $loggerName;
		$config                 = PPConfigManager::getInstance();
		$this->loggerFile       = ( $config->get( 'log.FileName' ) ) ? $config->get( 'log.FileName' ) : ini_get( 'error_log' );
		$loggingEnabled         = $config->get( 'log.LogEnabled' );
		$this->isLoggingEnabled = ( isset( $loggingEnabled ) ) ? $loggingEnabled : false;
		$loggingLevel           = strtoupper( $config->get( 'log.LogLevel' ) );
		$this->loggingLevel     = ( isset( $loggingLevel ) && defined( "PPLoggingLevel::$loggingLevel" ) ) ? constant( "PPLoggingLevel::$loggingLevel" ) : PPLoggingManager::DEFAULT_LOGGING_LEVEL;

	}

	public function log( $message, $level = PPLoggingLevel::INFO )
	{
		if ( $this->isLoggingEnabled && ( $level <= $this->loggingLevel ) ) {
			error_log( $this->loggerName . ": $message\n", 3, $this->loggerFile );
		}
	}

	public function error( $message )
	{
		$this->log( $message, PPLoggingLevel::ERROR );
	}

	public function warning( $message )
	{
		$this->log( $message, PPLoggingLevel::WARN );
	}

	public function info( $message )
	{
		$this->log( $message, PPLoggingLevel::INFO );
	}

	public function fine( $message )
	{
		$this->log( $message, PPLoggingLevel::FINE );
	}

}

?>