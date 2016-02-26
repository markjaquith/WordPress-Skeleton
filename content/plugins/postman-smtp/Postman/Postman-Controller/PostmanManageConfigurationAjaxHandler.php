<?php
class PostmanWizardSocket {
	
	// these variables are populated by the Port Test
	public $hostname;
	public $hostnameDomainOnly;
	public $port;
	public $protocol;
	public $secure;
	public $mitm;
	public $reportedHostname;
	public $reportedHostnameDomainOnly;
	public $message;
	public $startTls;
	public $authPlain;
	public $auth_login;
	public $auth_crammd5;
	public $auth_xoauth;
	public $auth_none;
	public $try_smtps;
	public $success;
	public $transport;
	
	// these variables are populated by The Transport Recommenders
	public $label;
	public $id;
	
	/**
	 *
	 * @param unknown $queryHostData        	
	 */
	function __construct($queryHostData) {
		$this->hostname = $queryHostData ['hostname'];
		$this->hostnameDomainOnly = $queryHostData ['hostname_domain_only'];
		$this->port = $queryHostData ['port'];
		$this->protocol = $queryHostData ['protocol'];
		$this->secure = PostmanUtils::parseBoolean ( $queryHostData ['secure'] );
		$this->mitm = PostmanUtils::parseBoolean ( $queryHostData ['mitm'] );
		$this->reportedHostname = $queryHostData ['reported_hostname'];
		$this->reportedHostnameDomainOnly = $queryHostData ['reported_hostname_domain_only'];
		$this->message = $queryHostData ['message'];
		$this->startTls = PostmanUtils::parseBoolean ( $queryHostData ['start_tls'] );
		$this->authPlain = PostmanUtils::parseBoolean ( $queryHostData ['auth_plain'] );
		$this->auth_login = PostmanUtils::parseBoolean ( $queryHostData ['auth_login'] );
		$this->auth_crammd5 = PostmanUtils::parseBoolean ( $queryHostData ['auth_crammd5'] );
		$this->auth_xoauth = PostmanUtils::parseBoolean ( $queryHostData ['auth_xoauth'] );
		$this->auth_none = PostmanUtils::parseBoolean ( $queryHostData ['auth_none'] );
		$this->try_smtps = PostmanUtils::parseBoolean ( $queryHostData ['try_smtps'] );
		$this->success = PostmanUtils::parseBoolean ( $queryHostData ['success'] );
		$this->transport = $queryHostData ['transport'];
		assert ( ! empty ( $this->transport ) );
		$this->id = sprintf ( '%s_%s', $this->hostname, $this->port );
	}
}

