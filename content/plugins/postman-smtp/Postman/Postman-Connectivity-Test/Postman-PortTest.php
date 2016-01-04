<?php
require_once ("registered-domain-libs-master/PHP/effectiveTLDs.inc.php");
require_once ("registered-domain-libs-master/PHP/regDomain.inc.php");

/**
 *
 * @author jasonhendriks
 *        
 */
class PostmanPortTest {
	private $errstr;
	private $logger;
	private $hostname;
	public $hostnameDomainOnly;
	private $port;
	private $connectionTimeout;
	private $readTimeout;
	public $reportedHostname;
	public $reportedHostnameDomainOnly;
	public $protocol;
	public $secure;
	public $mitm;
	public $http;
	public $https;
	public $smtp;
	public $smtps;
	public $startTls;
	public $checkStartTls;
	public $authLogin;
	public $authPlain;
	public $authCrammd5;
	public $authXoauth;
	public $authNone;
	public $trySmtps;
	
	//
	const SMTPS_PROTOCOL = 'SMTPS';
	
	/**
	 */
	public function __construct($hostname, $port) {
		$this->logger = new PostmanLogger ( get_class ( $this ) );
		$this->hostname = $hostname;
		$this->hostnameDomainOnly = $this->getRegisteredDomain ( $hostname );
		$this->port = $port;
		$this->connectionTimeout = 10;
		$this->readTimeout = 10;
	}
	
	/**
	 * Wrap the regDomain/getRegisteredDomain function
	 *
	 * @param unknown $domain        	
	 * @return unknown|Ambigous <NULL, string, unknown, mixed>
	 */
	private function getRegisteredDomain($hostname) {
		$registeredDomain = getRegisteredDomain ( $hostname );
		if ($registeredDomain === NULL) {
			return $hostname;
		}
		return $registeredDomain;
	}
	public function setConnectionTimeout($timeout) {
		$this->connectionTimeout = $timeout;
		$this->logger->trace ( $this->connectionTimeout );
	}
	public function setReadTimeout($timeout) {
		$this->readTimeout = $timeout;
		$this->logger->trace ( $this->readTimeout );
	}
	private function createStream($connectionString) {
		$stream = @stream_socket_client ( $connectionString, $errno, $errstr, $this->connectionTimeout );
		if ($stream) {
			$this->trace ( sprintf ( 'connected to %s', $connectionString ) );
		} else {
			$this->trace ( sprintf ( 'Could not connect to %s because %s [%s]', $connectionString, $errstr, $errno ) );
		}
		return $stream;
	}
	
	/**
	 *
	 * @param number $timeout        	
	 * @return boolean
	 */
	public function genericConnectionTest() {
		$this->logger->trace ( 'testCustomConnection()' );
		// test if the port is open
		$connectionString = sprintf ( '%s:%s', $this->hostname, $this->port );
		$stream = $this->createStream ( $connectionString, $this->connectionTimeout );
		return null != $stream;
	}
	
	/**
	 * Given a hostname, test if it has open ports
	 *
	 * @param string $hostname        	
	 */
	public function testHttpPorts() {
		$this->trace ( 'testHttpPorts()' );
		$connectionString = sprintf ( "https://%s:%s", $this->hostname, $this->port );
		try {
			$response = PostmanUtils::remotePost ( $connectionString );
			$this->trace ( 'wp_remote_retrieve_headers:' );
			$this->logger->trace ( wp_remote_retrieve_headers ( $response ) );
			$this->trace ( wp_remote_retrieve_response_code ( $response ) );
			$this->protocol = 'HTTPS';
			$this->http = true;
			$this->https = true;
			$this->secure = true;
			$this->reportedHostname = $this->hostname;
			$this->reportedHostnameDomainOnly = $this->getRegisteredDomain ( $this->hostname );
			return true;
		} catch ( Exception $e ) {
			$this->debug ( 'return false' );
		}
	}
	
	/**
	 * Given a hostname, test if it has open ports
	 *
	 * @param string $hostname        	
	 */
	public function testSmtpPorts() {
		$this->logger->trace ( 'testSmtpPorts()' );
		if ($this->port == 8025) {
			$this->debug ( 'Executing test code for port 8025' );
			$this->protocol = 'SMTP';
			$this->smtp = true;
			$this->authNone = 'true';
			return true;
		}
		$connectionString = sprintf ( "%s:%s", $this->hostname, $this->port );
		$success = $this->talkToMailServer ( $connectionString, $this->connectionTimeout, $this->readTimeout );
		if ($success) {
			$this->protocol = 'SMTP';
			if (! ($this->authCrammd5 || $this->authLogin || $this->authPlain || $this->authXoauth)) {
				$this->authNone = true;
			}
		} else {
			$this->trySmtps = true;
		}
		return $success;
	}
	
	/**
	 * Given a hostname, test if it has open ports
	 *
	 * @param string $hostname        	
	 */
	public function testSmtpsPorts() {
		$this->logger->trace ( 'testSmtpsPorts()' );
		$connectionString = sprintf ( "ssl://%s:%s", $this->hostname, $this->port );
		$success = $this->talkToMailServer ( $connectionString, $this->connectionTimeout, $this->readTimeout );
		if ($success) {
			if (! ($this->authCrammd5 || $this->authLogin || $this->authPlain || $this->authXoauth)) {
				$this->authNone = true;
			}
			$this->protocol = self::SMTPS_PROTOCOL;
			$this->smtps = true;
			$this->secure = true;
		}
		return $success;
	}
	
	/**
	 * Given a hostname, test if it has open ports
	 *
	 * @param string $hostname        	
	 */
	private function talkToMailServer($connectionString) {
		$this->logger->trace ( 'talkToMailServer()' );
		$stream = $this->createStream ( $connectionString, $this->connectionTimeout );
		if ($stream) {
			$serverName = PostmanUtils::postmanGetServerName ();
			@stream_set_timeout ( $stream, $this->readTimeout );
			// see http://php.net/manual/en/transports.inet.php#113244
			// see http://php.net/stream_socket_enable_crypto
			$result = $this->readSmtpResponse ( $stream );
			if ($result) {
				$this->reportedHostname = $result;
				$this->reportedHostnameDomainOnly = $this->getRegisteredDomain ( $this->reportedHostname );
				$this->logger->trace ( sprintf ( 'comparing %s with %s', $this->reportedHostnameDomainOnly, $this->hostnameDomainOnly ) );
				$this->mitm = true;
				// MITM exceptions
				if ($this->reportedHostnameDomainOnly == 'google.com' && $this->hostnameDomainOnly == 'gmail.com') {
					$this->mitm = false;
				} elseif ($this->reportedHostnameDomainOnly == 'hotmail.com' && $this->hostnameDomainOnly == 'live.com') {
					$this->mitm = false;
				} elseif ($this->reportedHostnameDomainOnly == $this->hostnameDomainOnly) {
					$this->mitm = false;
				}
				$this->debug ( sprintf ( 'domain name: %s (%s)', $this->reportedHostname, $this->reportedHostnameDomainOnly ) );
				$this->sendSmtpCommand ( $stream, sprintf ( 'EHLO %s', $serverName ) );
				$this->readSmtpResponse ( $stream );
				if ($this->checkStartTls) {
					$this->sendSmtpCommand ( $stream, 'STARTTLS' );
					$this->readSmtpResponse ( $stream );
					$starttlsSuccess = @stream_socket_enable_crypto ( $stream, true, STREAM_CRYPTO_METHOD_TLS_CLIENT );
					if ($starttlsSuccess) {
						$this->startTls = true;
						$this->secure = true;
						$this->sendSmtpCommand ( $stream, sprintf ( 'EHLO %s', $serverName ) );
						$this->readSmtpResponse ( $stream );
					} else {
						$this->error ( 'starttls failed' );
					}
				}
				fclose ( $stream );
				$this->debug ( 'return true' );
				return true;
			} else {
				fclose ( $stream );
				$this->debug ( 'return false' );
				return false;
			}
		} else {
			return false;
		}
	}
	private function sendSmtpCommand($stream, $message) {
		$this->trace ( 'tx: ' . $message );
		fputs ( $stream, $message . "\r\n" );
	}
	private function readSmtpResponse($stream) {
		$result = '';
		while ( ($line = fgets ( $stream )) !== false ) {
			$this->trace ( 'rx: ' . $line );
			if (preg_match ( '/^250.AUTH/', $line )) {
				// $this->debug ( '250-AUTH' );
				if (preg_match ( '/\\sLOGIN\\s/', $line )) {
					$this->authLogin = true;
					$this->debug ( 'authLogin' );
				}
				if (preg_match ( '/\\sPLAIN\\s/', $line )) {
					$this->authPlain = true;
					$this->debug ( 'authPlain' );
				}
				if (preg_match ( '/\\sCRAM-MD5\\s/', $line )) {
					$this->authCrammd5 = true;
					$this->debug ( 'authCrammd5' );
				}
				if (preg_match ( '/\\sXOAUTH.\\s/', $line )) {
					$this->authXoauth = true;
					$this->debug ( 'authXoauth' );
				}
				if (preg_match ( '/\\sANONYMOUS\\s/', $line )) {
					// Postman treats ANONYMOUS login as no authentication.
					$this->authNone = true;
					$this->debug ( 'authAnonymous => authNone' );
				}
				// done
				$result = 'auth';
			} elseif (preg_match ( '/STARTTLS/', $line )) {
				$result = 'starttls';
				$this->checkStartTls = true;
				$this->debug ( 'starttls' );
			} elseif (preg_match ( '/^220.(.*?)\\s/', $line, $matches )) {
				if (empty ( $result ))
					$result = $matches [1];
			}
			if (preg_match ( '/^\d\d\d\\s/', $line )) {
				// always exist on last server response line
				// $this->debug ( 'exit' );
				return $result;
			}
		}
		return false;
	}
	public function getErrorMessage() {
		return $this->errstr;
	}
	private function trace($message) {
		$this->logger->trace ( sprintf ( '%s:%s => %s', $this->hostname, $this->port, $message ) );
	}
	private function debug($message) {
		$this->logger->debug ( sprintf ( '%s:%s => %s', $this->hostname, $this->port, $message ) );
	}
	private function error($message) {
		$this->logger->error ( sprintf ( '%s:%s => %s', $this->hostname, $this->port, $message ) );
	}
}
