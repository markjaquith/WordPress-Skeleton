<?php
if (! class_exists ( 'PostmanSmtpMappings' )) {
	class PostmanSmtpMappings {
		// if an email is in this domain array, it is a known smtp server (easy lookup)
		private static $emailDomain = array (
				// from http://www.serversmtp.com/en/outgoing-mail-server-hostname
				'1and1.com' => 'smtp.1and1.com',
				'airmail.net' => 'smtp.airmail.net',
				'aol.com' => 'smtp.aol.com',
				'Bluewin.ch' => 'Smtpauths.bluewin.ch',
				'Comcast.net' => 'Smtp.comcast.net',
				'Earthlink.net' => 'Smtpauth.earthlink.net',
				'gmail.com' => 'smtp.gmail.com',
				'Gmx.com' => 'mail.gmx.com',
				'Gmx.net' => 'mail.gmx.com',
				'Gmx.us' => 'mail.gmx.com',
				'hotmail.com' => 'smtp.live.com',
				'icloud.com' => 'smtp.mail.me.com',
				'mail.com' => 'smtp.mail.com',
				'ntlworld.com' => 'smtp.ntlworld.com',
				'rocketmail.com' => 'plus.smtp.mail.yahoo.com',
				'rogers.com' => 'smtp.broadband.rogers.com',
				'yahoo.ca' => 'smtp.mail.yahoo.ca',
				'yahoo.co.id' => 'smtp.mail.yahoo.co.id',
				'yahoo.co.in' => 'smtp.mail.yahoo.co.in',
				'yahoo.co.kr' => 'smtp.mail.yahoo.com',
				'yahoo.com' => 'smtp.mail.yahoo.com',
				'yahoo.com.ar' => 'smtp.mail.yahoo.com.ar',
				'yahoo.com.au' => 'smtp.mail.yahoo.com.au',
				'yahoo.com.br' => 'smtp.mail.yahoo.com.br',
				'yahoo.com.cn' => 'smtp.mail.yahoo.com.cn',
				'yahoo.com.hk' => 'smtp.mail.yahoo.com.hk',
				'yahoo.com.mx' => 'smtp.mail.yahoo.com',
				'yahoo.com.my' => 'smtp.mail.yahoo.com.my',
				'yahoo.com.ph' => 'smtp.mail.yahoo.com.ph',
				'yahoo.com.sg' => 'smtp.mail.yahoo.com.sg',
				'yahoo.com.tw' => 'smtp.mail.yahoo.com.tw',
				'yahoo.com.vn' => 'smtp.mail.yahoo.com.vn',
				'yahoo.co.nz' => 'smtp.mail.yahoo.com.au',
				'yahoo.co.th' => 'smtp.mail.yahoo.co.th',
				'yahoo.co.uk' => 'smtp.mail.yahoo.co.uk',
				'yahoo.de' => 'smtp.mail.yahoo.de',
				'yahoo.es' => 'smtp.correo.yahoo.es',
				'yahoo.fr' => 'smtp.mail.yahoo.fr',
				'yahoo.ie' => 'smtp.mail.yahoo.co.uk',
				'yahoo.it' => 'smtp.mail.yahoo.it',
				'zoho.com' => 'smtp.zoho.com',
				// from http://www.att.com/esupport/article.jsp?sid=KB401570&cv=801
				'ameritech.net' => 'outbound.att.net',
				'att.net' => 'outbound.att.net',
				'bellsouth.net' => 'outbound.att.net',
				'flash.net' => 'outbound.att.net',
				'nvbell.net' => 'outbound.att.net',
				'pacbell.net' => 'outbound.att.net',
				'prodigy.net' => 'outbound.att.net',
				'sbcglobal.net' => 'outbound.att.net',
				'snet.net' => 'outbound.att.net',
				'swbell.net' => 'outbound.att.net',
				'wans.net' => 'outbound.att.net' 
		);
		// if an email's mx is in this domain array, it is a known smtp server (dns lookup)
		// useful for custom domains that map to a mail service
		private static $mxMappings = array (
				'1and1help.com' => 'smtp.1and1.com',
				'google.com' => 'smtp.gmail.com',
				'Gmx.net' => 'mail.gmx.com',
				'icloud.com' => 'smtp.mail.me.com',
				'hotmail.com' => 'smtp.live.com',
				'mx-eu.mail.am0.yahoodns.net' => 'smtp.mail.yahoo.com',
				// 'mail.protection.outlook.com' => 'smtp.office365.com',
				// 'mail.eo.outlook.com' => 'smtp.office365.com',
				'outlook.com' => 'smtp.office365.com',
				'biz.mail.am0.yahoodns.net' => 'smtp.bizmail.yahoo.com',
				'BIZ.MAIL.YAHOO.com' => 'smtp.bizmail.yahoo.com',
				'hushmail.com' => 'smtp.hushmail.com',
				'gmx.net' => 'mail.gmx.com',
				'mandrillapp.com' => 'smtp.mandrillapp.com',
				'smtp.secureserver.net' => 'relay-hosting.secureserver.net',
				'presmtp.ex1.secureserver.net' => 'smtp.ex1.secureserver.net',
				'presmtp.ex2.secureserver.net' => 'smtp.ex2.secureserver.net',
				'presmtp.ex3.secureserver.net' => 'smtp.ex2.secureserver.net',
				'presmtp.ex4.secureserver.net' => 'smtp.ex2.secureserver.net',
				'htvhosting.com' => 'mail.htvhosting.com' 
		);
		public static function getSmtpFromEmail($hostname) {
			reset ( PostmanSmtpMappings::$emailDomain );
			while ( list ( $domain, $smtp ) = each ( PostmanSmtpMappings::$emailDomain ) ) {
				if (strcasecmp ( $hostname, $domain ) == 0) {
					return $smtp;
				}
			}
			return false;
		}
		public static function getSmtpFromMx($mx) {
			reset ( PostmanSmtpMappings::$mxMappings );
			while ( list ( $domain, $smtp ) = each ( PostmanSmtpMappings::$mxMappings ) ) {
				if (PostmanUtils::endswith ( $mx, $domain )) {
					return $smtp;
				}
			}
			return false;
		}
	}
}
if (! class_exists ( 'PostmanSmtpDiscovery' )) {
	class PostmanSmtpDiscovery {
		
		// private instance variables
		public $isGoogle;
		public $isGoDaddy;
		public $isWellKnownDomain;
		private $smtpServer;
		private $primaryMx;
		private $email;
		private $domain;
		
		/**
		 * Constructor
		 *
		 * @param unknown $email        	
		 */
		public function __construct($email) {
			$this->email = $email;
			$this->determineSmtpServer ( $email );
			$this->isGoogle = $this->smtpServer == 'smtp.gmail.com';
			$this->isGoDaddy = $this->smtpServer == 'relay-hosting.secureserver.net';
		}
		/**
		 * The SMTP server we suggest to use - this is determined
		 * by looking up the MX hosts for the domain.
		 */
		public function getSmtpServer() {
			return $this->smtpServer;
		}
		public function getPrimaryMx() {
			return $this->primaryMx;
		}
		/**
		 *
		 * @param unknown $email        	
		 * @return Ambigous <number, boolean>
		 */
		private function validateEmail($email) {
			return PostmanUtils::validateEmail ( $email );
		}
		private function determineSmtpServer($email) {
			$hostname = substr ( strrchr ( $email, "@" ), 1 );
			$this->domain = $hostname;
			$smtp = PostmanSmtpMappings::getSmtpFromEmail ( $hostname );
			if ($smtp) {
				$this->smtpServer = $smtp;
				$this->isWellKnownDomain = true;
				return true;
			} else {
				$host = strtolower ( $this->findMxHostViaDns ( $hostname ) );
				if ($host) {
					$this->primaryMx = $host;
					$smtp = PostmanSmtpMappings::getSmtpFromMx ( $host );
					if ($smtp) {
						$this->smtpServer = $smtp;
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			}
		}
		
		/**
		 * Uses getmxrr to retrieve the MX records of a hostname
		 *
		 * @param unknown $hostname        	
		 * @return mixed|boolean
		 */
		private function findMxHostViaDns($hostname) {
			if (function_exists ( 'getmxrr' )) {
				$b_mx_avail = getmxrr ( $hostname, $mx_records, $mx_weight );
			} else {
				$b_mx_avail = $this->getmxrr ( $hostname, $mx_records, $mx_weight );
			}
			if ($b_mx_avail && sizeof ( $mx_records ) > 0) {
				// copy mx records and weight into array $mxs
				$mxs = array ();
				
				for($i = 0; $i < count ( $mx_records ); $i ++) {
					$mxs [$mx_weight [$i]] = $mx_records [$i];
				}
				
				// sort array mxs to get servers with highest prio
				ksort ( $mxs, SORT_NUMERIC );
				reset ( $mxs );
				$mxs_vals = array_values ( $mxs );
				return array_shift ( $mxs_vals );
			} else {
				return false;
			}
		}
		/**
		 * This is a custom implementation of mxrr for Windows PHP installations
		 * which don't have this method natively.
		 *
		 * @param unknown $hostname        	
		 * @param unknown $mxhosts        	
		 * @param unknown $mxweight        	
		 * @return boolean
		 */
		function getmxrr($hostname, &$mxhosts, &$mxweight) {
			if (! is_array ( $mxhosts )) {
				$mxhosts = array ();
			}
			$hostname = escapeshellarg ( $hostname );
			if (! empty ( $hostname )) {
				$output = "";
				@exec ( "nslookup.exe -type=MX $hostname.", $output );
				$imx = - 1;
				
				foreach ( $output as $line ) {
					$imx ++;
					$parts = "";
					if (preg_match ( "/^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$/", $line, $parts )) {
						$mxweight [$imx] = $parts [1];
						$mxhosts [$imx] = $parts [2];
					}
				}
				return ($imx != - 1);
			}
			return false;
		}
	}
}

