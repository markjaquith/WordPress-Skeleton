<?php
require_once 'Postman/PostmanUtils.php';
require_once 'Postman/Postman-Connectivity-Test/Postman-PortTest.php';

// constants
const HTTPS = 2;
const SMTP = 4;
const SMTPS = 8;
const STARTTLS = 16;
const CRAMMD5 = 32;
const XOAUTH = 64;
const PLAIN = 128;
const LOGIN = 256;
const TIMEOUT = 2;

//

test ( 'smtp.gmail.com', 465, SMTP, false );
test ( 'smtp.gmail.com', 465, SMTPS | LOGIN | PLAIN | XOAUTH, true );
test ( 'smtp.gmail.com', 465, SMTPS | CRAMMD5, false );
test ( 'smtp.gmail.com', 587, SMTP | STARTTLS | LOGIN | PLAIN | XOAUTH, true );
test ( 'smtp.gmail.com', 587, SMTP | STARTTLS | CRAMMD5, false );
test ( 'mailtrap.io', 465, SMTP | STARTTLS | CRAMMD5 | PLAIN | LOGIN, true );
test ( 'mailtrap.io', 465, SMTP | STARTTLS | XOAUTH, false );
test ( 'smtp.mail.yahoo.com', 465, SMTPS | XOAUTH, true );
test ( 'smtp.office365.com', 587, SMTP | STARTTLS | LOGIN, true );
test ( 'smtp.office365.com', 587, SMTP | PLAIN, false );
test ( 'smtp.office365.com', 465, SMTPS, false );

/**
 *
 * @param unknown $host        	
 * @param unknown $port        	
 */
function test($host, $port, $tests, $expectedResult) {
	$p = new PostmanPortTest ( $host, $port );
	$success = false;
	if ($tests & SMTP) {
		$success = $p->testSmtpPorts ( TIMEOUT, TIMEOUT );
	} elseif ($tests & SMTPS) {
		$success = $p->testSmtpsPorts ( TIMEOUT, TIMEOUT );
	}
	if ($tests & STARTTLS) {
		$success &= $p->startTls;
	}
	if ($tests & CRAMMD5) {
		$success &= $p->authCrammd5;
	}
	if ($tests & XOAUTH) {
		$success &= $p->authXoauth;
	}
	if ($tests & LOGIN) {
		$success &= $p->authLogin;
	}
	if ($tests & PLAIN) {
		$success &= $p->authPlain;
	}
	$displaySuccess = 'fail';
	if ($success == $expectedResult) {
		$displaySuccess = 'pass';
	}
	print "$displaySuccess: $host:$port\n";
}
