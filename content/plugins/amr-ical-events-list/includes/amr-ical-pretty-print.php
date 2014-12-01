<?php
function amr_prettyprint_weekst ($wkst) {
global $amr_day_of_week_no, $wp_locale,
		$amr_day_of_week_from_no,
		$amr_day_of_week;
	if (empty($wkst)) return '';

	return ('<em>'.sprintf(__('Weeks start on %s','amr-ical-events-list'), 
		$amr_day_of_week[$wkst]
		).'</em> '.$wkst);
}
/* --------------------------------------------------  */
function amr_prettyprint_r_ex_date ($rdate) { /* rrule or exrule */
global $amr_formats;  /* amr check that this get set to the chosen list type */
// 201407 nor rdates include dtstart - should dtstart be reversed out here, or the text changed ?

//	$df = pref_date_entry_format_string();
	if (is_array($rdate)) {
	// 20140709 resort the array
		sort($rdate);
		foreach ($rdate as $i => $d) {
			if (is_object($d))
				$html[] = amr_format_date ($amr_formats['Day'], $d);
//			 = $d->format($df);  /* *** is it already in the right timezone or not ? If just doing 'date' for now, is okay? */
			else $html[] = $d;
		}
	}
	return (implode(', ', $html));
}
/* --------------------------------------------------  */
function amr_prettyprint_byday ($b) {
	$fulldayofweek = amr_fulldaytext(); /* MO, TU= etsc*/
	$h = array();
	$html = '';
	if (is_array($b)) {
		foreach ($b as $d => $n) {
			if (is_array($n)) { /* must be n bydays */
				foreach ($n as $i => $n2) {
					$temp[] = ical_ordinalize_words($n2);
				}
				if (count($temp) == 2) $h[] = implode (__(' and ','amr-ical-events-list'),$temp).' '.$fulldayofweek[$d];
				else $h[] =  implode (', ',$temp).' '.$fulldayofweek[$d];
				$temp = array();
			}
			else { /* normal bydays */
				$h[] = $fulldayofweek[$d];

			}
			if (count($h) == 2) $html = implode(__(' and ','amr-ical-events-list'),$h);
			else $html =  implode (', ',$h);
			if (!is_array($n)) $html =	__('every ','amr-ical-events-list').$html;
		}
		return($html);
	}
	else return ($b);	/* who knows what is in if it is not an array? */

}
/* --------------------------------------------------  */
function amr_prettyprint_byordinal ($b) {
	$h = array();
	if (is_array($b)) {
		foreach ($b as $i => $d) {
			$h[] = ical_ordinalize_words($d);
		};
		if (count($h) == 2) $html = implode(__(' and ','amr-ical-events-list'),$h);
		else $html =  implode (',&nbsp;',$h);
		return($html);
	}
else return ($b);

}
/* --------------------------------------------------  */
function amr_prettyprint_bymonth ($b) {
global $wp_locale;
	$h = array();
	if (is_array($b)) {
		foreach ($b as $i => $d) {
			$h[] = $wp_locale->get_month($d);
		};
		if (count($h) == 2) $html = implode(__(' or ','amr-ical-events-list'),$h);
		else $html =  implode (',&nbsp;',$h);
		return($html);
	}
else return ($b);

}
/* --------------------------------------------------  */
function amr_prettyprint_duration ($duration) {
	if (empty($duration)) return;
	if (!is_array($duration)) echo $duration;
	else { $h = array();
		foreach ($duration as $i => $v) {
			$h[] = sprintf( _n(	'%u '.rtrim($i,'s') /* singular */
			                  , '%u '.$i /* plural */
							  ,$v  // number
							  ,'amr-ical-events-list'),// domain
							  $v);
		}
		$html = implode (',&nbsp;',$h);
	}
	echo $html;
}
/* --------------------------------------------------  */
function amr_prettyprint_rule ($rule) { /* rrule or exrule */
/* Receive an array of prepared fields and combine it into a suitable descriptive string */
global 	$amr_freq,
		$amr_freq_unit,
		$amr_day_of_week_no,
		$wp_locale;
	$sep = '&nbsp;';
	$c = '';

	if (isset($rule['FREQ'])) {
		$nicefrequnit = $amr_freq_unit[$rule['FREQ']]; /* already translated value */
		if (isset($rule['INTERVAL'])) {/*   the freq as it is repetitive */
			$interval  = ' '
			.sprintf(__('Every %s %s','amr-ical-events-list'),
    			ical_ordinalize($rule['INTERVAL']),
				$nicefrequnit).$sep;
		}
//		else $interval = ' '.sprintf( __('every %s','amr-ical-events-list'), $nicefrequnit).$sep; // sounds funny to have daily every day, only have if every 2nd etc
//		$nicefreq = $amr_freq[$rule['FREQ']].$interval; /* already translated value */

		else $interval = $amr_freq[$rule['FREQ']]; /* already translated value */

		if (isset($rule['BYSETPOS'])) $c .= ' '.
			sprintf(__('On %s instance within %s', 'amr-ical-events-list')
			,amr_prettyprint_byordinal($rule['BYSETPOS'])
			,$interval);
//		else $c .= 	$nicefreq;
		else $c .= 	$interval;
		if (isset($rule['COUNT'])) $c .= ' '.sprintf(__('%s times','amr-ical-events-list'), $rule['COUNT']).$sep;
		if (isset($rule['UNTIL'])) {
			if ($rule['UNTIL-TIME'] === '00:00') $rule['UNTIL-TIME'] = '';
			else if (strtolower($rule['UNTIL-TIME']) === '12:00 am') $rule['UNTIL-TIME'] = '';

			$c .= '&nbsp;'.sprintf(__('until %s %s','amr-ical-events-list'), $rule['UNTIL-DATE'], $rule['UNTIL-TIME']).$sep;
			}
		if (isset($rule['MONTH'])) $c .= sprintf(__(' if month is %s','amr-ical-events-list'),amr_prettyprint_bymonth($rule['MONTH']));
//		if (isset($rule['BYWEEKNO'])) $c .= ' '.sprintf(__(' in weeks %s','amr-ical-events-list'),implode(',',$rule['BYWEEKNO']));
		if (isset($rule['BYWEEKNO'])) $c .= ' ' .sprintf(__(' in %s weeks of the year','amr-ical-events-list'),amr_prettyprint_byordinal($rule['BYWEEKNO']));
//		if (isset($rule['BYYEARDAY'])) $c .= ' '.sprintf(__('on the %s day of year','amr-ical-events-list'),implode(',',$rule['BYYEARDAY']));
		if (isset($rule['BYYEARDAY'])) $c .= ' '.sprintf(__('on %s day of the year','amr-ical-events-list'),amr_prettyprint_byordinal($rule['BYYEARDAY']));
		if (isset($rule['DAY'])) $c .= ' '.sprintf(__('on %s day of each month', 'amr-ical-events-list'),amr_prettyprint_byordinal($rule['DAY']));
		if (isset($rule['NBYDAY'])) $nbyday = ' '.sprintf(__('on %s ', 'amr-ical-events-list'),amr_prettyprint_byday($rule['NBYDAY']));
		if (isset($rule['BYDAY'])) $byday = ' '.sprintf(__('on %s ', 'amr-ical-events-list'),amr_prettyprint_byday($rule['BYDAY']));
		$ofthefreq = '';
		// change to accomodate dutch having different artcles for month and year de or het
		if ($rule['FREQ'] == 'MONTHLY')
			$ofthefreq = _x(' of the month','eg: last day of the month', 'amr-ical-events-list');
		else if ($rule['FREQ'] == 'YEARLY')
			$ofthefreq = _x(' of the year','eg: last day of the year','amr-ical-events-list');	
		if (isset ($nbyday) and isset ($byday)) $c .= $nbyday.__(' and ','amr-ical-events-list').$byday.$ofthefreq;
		else { if (isset ($byday)) $c .= $byday.$ofthefreq;
			if (isset ($nbyday)) $c .= $nbyday.$ofthefreq;
		}
		if (isset($rule['BYHOUR'])) $c .= ' '.sprintf(__('at the %s hour', 'amr-ical-events-list'),implode(',',$rule['BYHOUR']));
		if (isset($rule['BYMINUTE'])) $c .= ' '.sprintf(__('at the %s minute', 'amr-ical-events-list'),implode(',',$rule['BYMINUTE']));
		if (isset($rule['BYSECOND'])) $c .= ' '.sprintf(__('at the %s second', 'amr-ical-events-list'),implode(',',$rule['BYSECOND']));
		if (isset($rule['WKST'])) $c .= '; '.amr_prettyprint_weekst($rule['WKST']);
		}
	return (rtrim($c,','));
}
/* --------------------------------------------------  */
function amr_prepare_pretty_rrule ($rule) {

global $ical_timezone, $amr_formats;

/* take the event and it's parsed rrule or exrule and convert some aspects for people use.  Used by both edit event and event info */

	$df = $amr_formats['Day'];

	$tf = $amr_formats['Time'];

	$rule['UNTIL-DATE'] = '';

	$rule['UNTIL-TIME'] = '';

	if (isset($_GET['wpmldebug'])) {echo '<hr> inprep pretty';var_dump($rule);}

	foreach ($rule as $i=>$r) { $rule[strtoupper($i)] = $r;}

	if (isset($rule['UNTIL']) and is_object($rule['UNTIL'])) {  /* until is possibly in Z time, move to our time first */

			date_timezone_set($rule['UNTIL'], $ical_timezone);

//			$rule['UNTIL-DATE'] = $rule['UNTIL']->format($df);
			$rule['UNTIL-DATE'] = amr_format_date($df, $rule['UNTIL']);
			$rule['UNTIL-TIME'] = amr_format_date($tf, $rule['UNTIL']);

	}

	else if (!(isset($rule['COUNT']))) 	$rule['forever'] = 'forever';

	if (isset ($rule['NOMOWEBYDAY'])) { /* what the F?? */
			foreach ($rule['BYDAY'] as $j => $k) {
					$l = strlen($k);
					if ($l > 2) {  /* special treatment required - we have a numeric byday  */
						$d = substr($k, $l-2, $l);

						$rule['NBYDAY'][$d][substr ($k, 0, $l-2)] = true;
						$rule['BYDAY'][$d] = true;
					}
					else {

						$rule['BYDAY'][$k] = true; /* ie recurs every one of those days of week */
						$rule['NBYDAY'][$k]['0'] = true;

					}
					unset($rule['BYDAY'][$j]);
			}
	}
	return ($rule);

	}
/* --------------------------------------------------------- */
function amr_format_bookmark ($text) {
	return ('<a id="'.$text.'"></a>');  /* ***/
}
/* --------------------------------------------------------- */
function amr_format_rrule ($rule) {
	if (isset($rule[0]))  /* we have an array of rules, is this still valid or is it just one now *** if multiple valid, then code change required  */
		$rule = $rule[0];
	if (is_string($rule)) $rule = amr_parseRRULE($rule);
	$rule2 = amr_prepare_pretty_rrule ($rule);
	$rule3 = amr_prettyprint_rule ($rule2);
	return ($rule3);
}
