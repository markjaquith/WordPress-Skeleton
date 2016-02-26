<?php
require_once 'Postman/Postman-Mail/PostmanEmailAddress.php';
require_once 'Postman/PostmanUtils.php';

// string test
$recipients = 'Kevin.Brine@pppg.com, Robert <Robert.Thomas@pppg.com>, "Warbler" <Blaine.James@pppg.com>, "Guice, Doug" <Doug.Guice@pppg.com>';
var_dump ( PostmanEmailAddress::convertToArray ( $recipients ) );

// array test
$recipients = array (
		'Kevin.Brine@pppg.com',
		'Robert <Robert.Thomas@pppg.com>',
		'"Warbler" <Blaine.James@pppg.com>',
		'"Guice, Doug" <Doug.Guice@pppg.com>' 
);
var_dump ( PostmanEmailAddress::convertToArray ( $recipients ) );

// array test
$recipients = 'webmaster@cirrus.com.br';
var_dump ( PostmanEmailAddress::convertToArray ( $recipients ) );
