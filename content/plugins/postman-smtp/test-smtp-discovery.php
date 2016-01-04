<?php
require_once 'Postman/PostmanUtils.php';
require_once 'Postman/Postman-Configuration/PostmanSmtpDiscovery.php';
function test() {
	$pass = true;
	$pass &= check ( 'test@1and1.com', 'smtp.1and1.com' );
	$pass &= check ( 'test@aol.com', 'smtp.aol.com' );
	$pass &= check ( 'test@andrethierry.com', 'relay-hosting.secureserver.net' );
	$pass &= check ( 'test@apple.com' );
	$pass &= check ( 'test@artegennaro.com', 'relay-hosting.secureserver.net' );
	$pass &= check ( 'test@btclick.com', 'smtp.office365.com' );
	$pass &= check ( 'test@btconnect.com', 'smtp.office365.com' );
	$pass &= check ( 'test@dunsire.com', 'smtp.office365.com' );
	$pass &= check ( 'test@GMAIL.COM', 'smtp.gmail.com' );
	$pass &= check ( 'test@gmail.com', 'smtp.gmail.com' );
	$pass &= check ( 'test@hendriksandcregg.com' );
	$pass &= check ( 'test@hendriks.ca', 'smtp.gmail.com' );
	$pass &= check ( 'test@HENDRIKS.CA', 'smtp.gmail.com' );
	$pass &= check ( 'test@hotmail.com', 'smtp.live.com' );
	$pass &= check ( 'test@hushmail.com', 'smtp.hushmail.com' );
	$pass &= check ( 'test@ibm.com' );
	$pass &= check ( 'test@icloud.com', 'smtp.mail.me.com' );
	$pass &= check ( 'test@jhenrydesign.net', 'smtp.1and1.com' );
	$pass &= check ( 'test@jknylaw.com', 'smtp.ex1.secureserver.net' );
	$pass &= check ( 'test@live.com', 'smtp.live.com' );
	$pass &= check ( 'test@mac.com', 'smtp.mail.me.com' );
	$pass &= check ( 'test@markoneill.com', 'smtp.bizmail.yahoo.com' );
	$pass &= check ( 'test@me.com', 'smtp.mail.me.com' );
	$pass &= check ( 'test@office365.com' );
	$pass &= check ( 'test@outlook.com', 'smtp.live.com' );
	$pass &= check ( 'test@rocketmail.com', 'plus.smtp.mail.yahoo.com' );
	$pass &= check ( 'test@ronwilsoninsurance.com', 'smtp.office365.com' );
	$pass &= check ( 'test@rogers.com', 'smtp.broadband.rogers.com' );
	$pass &= check ( 'test@ryerson.ca' );
	$pass &= check ( 'test@sdlkfjsdl.com' );
	$pass &= check ( 'test@sdlkfjsdl.co.uk' );
	$pass &= check ( 'test@sdlkfjsdl.gov' );
	$pass &= check ( 'test@sdlkfjsdl.org' );
	$pass &= check ( 'test@sendgrid.com', 'smtp.gmail.com' );
	$pass &= check ( 'test@yahoo.ca', 'smtp.mail.yahoo.ca' );
	$pass &= check ( 'test@yahoo.co.id', 'smtp.mail.yahoo.co.id' );
	$pass &= check ( 'test@yahoo.co.in', 'smtp.mail.yahoo.co.in' );
	$pass &= check ( 'test@yahoo.co.kr', 'smtp.mail.yahoo.com' );
	$pass &= check ( 'test@yahoo.com', 'smtp.mail.yahoo.com' );
	$pass &= check ( 'test@yahoo.com.ar', 'smtp.mail.yahoo.com.ar' );
	$pass &= check ( 'test@yahoo.com.au', 'smtp.mail.yahoo.com.au' );
	$pass &= check ( 'test@yahoo.com.br', 'smtp.mail.yahoo.com.br' );
	$pass &= check ( 'test@yahoo.com.cn', 'smtp.mail.yahoo.com.cn' );
	$pass &= check ( 'test@yahoo.com.co' );
	$pass &= check ( 'test@yahoo.com.hk', 'smtp.mail.yahoo.com.hk' );
	$pass &= check ( 'test@yahoo.com.mx', 'smtp.mail.yahoo.com' );
	$pass &= check ( 'test@yahoo.com.my', 'smtp.mail.yahoo.com.my' );
	$pass &= check ( 'test@yahoo.com.ph', 'smtp.mail.yahoo.com.ph' );
	$pass &= check ( 'test@yahoo.com.sg', 'smtp.mail.yahoo.com.sg' );
	$pass &= check ( 'test@yahoo.com.tw', 'smtp.mail.yahoo.com.tw' );
	$pass &= check ( 'test@yahoo.com.vn', 'smtp.mail.yahoo.com.vn' );
	$pass &= check ( 'test@yahoo.co.nz', 'smtp.mail.yahoo.com.au' );
	$pass &= check ( 'test@yahoo.co.th', 'smtp.mail.yahoo.co.th' );
	$pass &= check ( 'test@yahoo.co.uk', 'smtp.mail.yahoo.co.uk' );
	$pass &= check ( 'test@yahoo.de', 'smtp.mail.yahoo.de' );
	$pass &= check ( 'test@yahoo.dk', 'smtp.mail.yahoo.com' );
	$pass &= check ( 'test@yahoo.es', 'smtp.correo.yahoo.es' );
	$pass &= check ( 'test@yahoo.fr', 'smtp.mail.yahoo.fr' );
	$pass &= check ( 'test@yahoo.ie', 'smtp.mail.yahoo.co.uk' );
	$pass &= check ( 'test@yahoo.it', 'smtp.mail.yahoo.it' );
	$pass &= check ( 'test@yahoo.no', 'smtp.mail.yahoo.com' );
	$pass &= check ( 'test@yahoo.pl', 'smtp.mail.yahoo.com' );
	$pass &= check ( 'test@yahoo.se', 'smtp.mail.yahoo.com' );
	$pass &= check ( 'test@zoho.com', 'smtp.zoho.com' );
	$pass &= check ( 'test@scatterlingsofafrica.net', 'mail.htvhosting.com' );
	
	if ($pass) {
		print " all tests passed.";
	}
	print "\n";
}
function check($email, $expectedSmtp = null) {
	$d = new PostmanSmtpDiscovery ( $email );
	$smtp = $d->getSmtpServer ();
	$displaySmtp = $smtp;
	if ($smtp == $expectedSmtp) {
		print '.';
		return true;
	} else {
		print sprintf ( "\n%s: %s smtp=%s\n", 'Fail', $email, $displaySmtp );
		return false;
	}
}
test ();
