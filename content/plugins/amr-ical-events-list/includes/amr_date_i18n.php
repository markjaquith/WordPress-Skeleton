<?php 
if (!function_exists('amr_date_i18n')) {
function amr_date_i18n( $dateformatstring, $dateobj = null) {
	global $wp_locale;
	// store original value for language with untypical grammars
	// see http://core.trac.wordpress.org/ticket/9396
	$req_format = $dateformatstring;
	//$datefunc = 'date';
	if ( ( !empty( $wp_locale->month ) ) && ( !empty( $wp_locale->weekday ) ) ) {
		$datemonth = $wp_locale->get_month( $dateobj->format('m') ); 
		$datemonth_abbrev = $wp_locale->get_month_abbrev( $datemonth ); 
		$w = $dateobj->format('w'); 
		if ($w == '-1') {$w = get_oldweekdays($dateobj);} /* php seems to break around 1760 and google passed a zero year date */
		$dateweekday = $wp_locale->get_weekday( $w ); 
		$dateweekday_abbrev = $wp_locale->get_weekday_abbrev( $dateweekday );
		$datemeridiem = $wp_locale->get_meridiem( $dateobj->format('a') );
		$datemeridiem_capital = $wp_locale->get_meridiem( $dateobj->format( 'A') );
		$dateformatstring = ' '.$dateformatstring;
		$dateformatstring = preg_replace( "/([^\\\])D/", "\\1" . backslashit( $dateweekday_abbrev ), $dateformatstring );
		$dateformatstring = preg_replace( "/([^\\\])F/", "\\1" . backslashit( $datemonth ), $dateformatstring );
		$dateformatstring = preg_replace( "/([^\\\])l/", "\\1" . backslashit( $dateweekday ), $dateformatstring );
		$dateformatstring = preg_replace( "/([^\\\])M/", "\\1" . backslashit( $datemonth_abbrev ), $dateformatstring );
		$dateformatstring = preg_replace( "/([^\\\])a/", "\\1" . backslashit( $datemeridiem ), $dateformatstring );
		$dateformatstring = preg_replace( "/([^\\\])A/", "\\1" . backslashit( $datemeridiem_capital ), $dateformatstring );
		$dateformatstring = substr( $dateformatstring, 1, strlen( $dateformatstring ) -1 );
	}
	$j = $dateobj->format( $dateformatstring );
	// allow plugins to redo this entirely for languages with untypical grammars
	$j = apply_filters('amr_date_i18n', $j, $req_format, $dateobj );
	return $j;
}
}
?>