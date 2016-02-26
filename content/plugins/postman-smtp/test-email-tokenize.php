<?php
$multipleEmails = 'test@hendriks.ca, test@tvwp.ca';
display ( str_getcsv ( $multipleEmails ) );
display ( stringGetCsvAlternate ( $multipleEmails ) );
function display($t) {
	foreach ( $t as $k => $v ) {
		if (strpos ( $v, ',' ) !== false) {
			$t [$k] = '"' . str_replace ( ' <', '" <', $v );
		}
		$email = trim ( $t [$k] );
		print $email . "\n";
	}
}

/**
 * Using fgetscv (PHP 4) as a work-around for str_getcsv (PHP 5.3)
 * @param unknown $string
 * @return multitype:
 */
function stringGetCsvAlternate($string) {
	$fh = fopen ( 'php://temp', 'r+' );
	fwrite ( $fh, $string );
	rewind ( $fh );
	
	$row = fgetcsv ( $fh );
	
	fclose ( $fh );
	return $row;
}