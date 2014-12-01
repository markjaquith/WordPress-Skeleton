<?php
global	$timeperiod_conv,
		$amr_day_of_week,
		$amr_day_of_week_no,
		$amr_day_of_week_from_no,
		$amr_bys,  /* an array containing all the diificuly by's such as negative bymonthdays, byday 9days of week) etc */
		$amr_wkst,   /* alpha */
		$amr_rulewkst; /* local to rule and numeric */
$amr_timeperiod_conv = array (
/* used to convert from ical FREQ to gnu relative items for date strings useed by php datetime to do maths */
	'DAILY' => 'day',
	'MONTHLY' => 'month',
	'YEARLY' =>  'year',
	'WEEKLY' => 'week',
	'HOURLY' => 'hour',
	'MINUTELY' => 'minute',
	'SECONDLY' => 'second'
	);
$amr_day_of_week	= array (
	'MO' => 'Monday',
	'TU' => 'Tuesday',
	'WE' => 'Wednesday',
	'TH' => 'Thursday',
	'FR' => 'Friday',
	'SA' => 'Saturday',
	'SU' => 'Sunday'
	);
$amr_day_of_week_no	= array (
	'MO' => 1,
	'TU' => 2,
	'WE' => 3,
	'TH' => 4,
	'FR' => 5,
	'SA' => 6,
	'SU' => 7
	);
$amr_day_of_week_from_no	= array ( /* convert php day of week number  (0 (sunday) to 6 (saturday)) to ical format */
	0 => 'SU',
	1 => 'MO',
	2 => 'TU',
	3 => 'WE',
	4 => 'TH',
	5 => 'FR',
	6 => 'SA'
	);
/* --------------------------------------------------------------------------------------------------- */
function amr_get_date_parts ($dateobj) { /* breaks date into the parts */
	if (!is_object($dateobj)) return (false);
	$parts['year'] 		= $dateobj->format('Y');
	$parts['month'] 	= $dateobj->format('n');
	$parts['day'] 		= $dateobj->format('j');
	$parts['hour'] 		= $dateobj->format('G');
	$parts['minute'] 	= $dateobj->format('i');
	$parts['second'] 	= $dateobj->format('s');
	$parts['date'] 		= new Datetime(); //if cloning dont need tz
	$parts['date'] 		= clone $dateobj; /* save a cope so we don't have to recreate */
	return ($parts);
}
/* --------------------------------------------------------------------------------------------------- */
function print_date_array($datearray) {
	if (isset ($_GET['rdebug'])) {
		foreach ($datearray as $j=> $datea) {
			echo '<br>'.$j. '    ';
			print_date_array1($datea);
//		if (!is_object($parts)) echo ' '.str_pad($parts,2,'_',STR_PAD_LEFT);
		}
		echo '<hr>';
	}
}
/* --------------------------------------------------------------------------------------------------- */
function print_date_array1($datea) {
global $ical_timezone;
	if (isset ($_GET['rdebug'])) {
		foreach ($datea as $i => $parts) if (!is_object($parts)) {
			echo ' '.str_pad($parts,2,'_',STR_PAD_LEFT);
		}
		$dateobj = amr_create_date_from_parts ($datea, $ical_timezone);
		if (is_object($dateobj)) echo ' '.$dateobj->format('l');
		else var_dump($dateobj);
	}
}
/* --------------------------------------------------------------------------------------------------- */
function amr_sort_date_array(&$datearray) {
	// Obtain a list of columns
	if (empty ($datearray)) return (null);
	foreach ($datearray as $key => $row) {
	    $year[$key]  	= $row['year'];
	    $month[$key] 	= $row['month'];
		$day[$key]  	= $row['day'];
	    $hour[$key] 	= $row['hour'];
		$minute[$key]  	= $row['minute'];
	    $second[$key] 	= $row['second'];
	}
	// Sort the data
	// Add $data as the last parameter, to sort by the common key
	array_multisort($year, SORT_ASC, $month, SORT_ASC, $day, SORT_ASC, $minute, SORT_ASC, $second, SORT_ASC, $datearray);
	if (isset ($_GET['rdebug'])) print_date_array($datearray);
	return ($datearray);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_limit_by_setpos(&$datearray, $by) {
	if (empty($datearray)) return;
	$total = count($datearray);
	foreach ($by as $i => $pos) {
		if (!empty($pos)) $pos = (int) $pos;
		else break;
		if ($pos < 0) 	{
			if ( (-$pos) > $total) break;
			else if (isset($datearray[$total+$pos]))
				$limitedarray[$total+$pos] = $datearray[$total+$pos];
			}
		else {
			if ($pos > $total) break;
			else if (isset($datearray[$pos-1]))
			$limitedarray[$pos-1] = $datearray[$pos-1];
		}
	}
	if (!empty($limitedarray) ) return($limitedarray);
	else return(null);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_expand_by_weekno (&$datearray, $pby /* the array of week nos */, $tz) {  /* Only ever done YEARLY ***** what if negative */
	global	$amr_wkst;

	$newdatearray = array();
	foreach ($datearray as $i => $d) {
		$w1d1 		= amr_create_date_from_parts ( array (
					'year' => $d['year'],
					'month' => '01',
					'day' => '04',   /* jan 4 is always in week 1 */
					'hour' => $d['hour'],
					'minute' => $d['minute'],
					'second' => $d['second']
					), $tz);
//		http://en.wikipedia.org/wiki/ISO_week_date#Last_week - says the the ISO 8601 spec defines the last week as the week with December 28 in it
		$lastweekd1 = amr_create_date_from_parts ( array (
					'year' => $d['year'],
					'month' => '12',
					'day' => '31',   /* last week always includes the last day even if it is also in next years week 1 (ie: 53=1)*/
					'hour' => $d['hour'],
					'minute' => $d['minute'],
					'second' => $d['second']
					), $tz);
		$w1d1 		= amr_get_start_of_week ($w1d1, $amr_wkst); /* this is now the start of week 1 */
		$lastweekd1 = amr_get_start_of_week ($lastweekd1, $amr_wkst); /* this is now the start of the last week */
		if (isset($_GET['rdebug'])) { 	echo '<br />Start of week1 :'.$w1d1->format('Ymd l');
										echo '<br />Start of last week :'.$lastweekd1->format('Ymd l');}
		$dateobj = new DateTime;
		foreach ($pby as $weekno) {

			if ($weekno < 0) {
				$dateobj = clone ($lastweekd1);
				if ($weekno < -1) date_modify ($dateobj,(($weekno+1)*7).' days'); /* $weekno is negative - now we have the start of the week we want */
			}
			else if ($weekno > 0){
				$dateobj = clone ($w1d1);
				if ($weekno > 1) date_modify ($dateobj,'+'.(($weekno-1)*7).' days'); /* now we have the start of the week we want */
			}
			/* else just ignore a zero, as it should not exist */

			$new[0] =  amr_get_date_parts($dateobj);
			for ($i = 1; $i <= 6; $i++) {
				date_modify ($dateobj,'+1 days'); /* need to do it with a date modify so that we don't have to worry about going over a month end */
				$new[$i] =  amr_get_date_parts($dateobj);
			}
			if (isset($_GET['rdebug'])) { echo '<br /><b>expanded by week :'.$weekno.' </b> Got '.count($new).'</br>';}
			$newdatearray = array_merge ($newdatearray, $new);
		}
	}
	return ($newdatearray);
}

/* --------------------------------------------------------------------------------------------------- */
function amr_expand_by_yearday (&$datearray, $pbyrdy, $tz) { /* array of y,m,d,h,m,s, and array of by 'x'*/
	/* set the first one and then copy and set the rest */
	if (empty($datearray)) return(false);
	$first = true;
	$sofar = count($datearray);
	if (isset($_GET['rdebug'])) { echo '<br /><b>Expanding byyearday </b>....So far have '.$sofar.' dates to apply bys = '; print_r ($pbyrdy); }

	$newdatearray = array();
	foreach ($pbyrdy as $j => $yd) { /* eg  8,200,360 */
		if (false and $first) {
			foreach ($datearray as $i => $d) { /* get the date object again , adjust the month and day */
				$dateobj = amr_create_date_from_parts ( array (
					'year' => $d['year'],
					'month' => '01',
					'day' => '01',
					'hour' => $d['hour'],
					'minute' => $d['minute'],
					'second' => $d['second']
					), $tz);
				date_modify ($dateobj, '+'.($yd-1).' days');
				$datearray[$i] = amr_get_date_parts($dateobj);
			}
			$first= false;
			$newdatearray = $datearray;
		}
		else {
			$new = $datearray;
			foreach ($new as $i => $d) { /* get the date object again , adjust the month and day */
				if ($yd > 0) {
					$d['month'] = '01';
					$d['day'] 	= '01';
					$adjustment = ($yd-1);
				}
				else if ($yd < 0) {
					$d['month'] = '12';
					$d['day'] 	= '31';
					$adjustment = ($yd+1);
				}
				else break;
				$dateobj = amr_create_date_from_parts ( $d, $tz);
				if (!is_object($dateobj)) break;
				if (isset($_GET['rdebug'])) { echo '<br />Start with '.$dateobj->format('YMD l').' adjust by '.$adjustment;}
				if (!($adjustment == 0)) date_modify ($dateobj, $adjustment.' days');
				$new[$i] = amr_get_date_parts($dateobj);

			}
		}
		if (!empty ($new)) $newdatearray = array_merge ($newdatearray, $new);
	}
//	if (isset($_GET['rdebug'])) {echo '<hr>New date array: '.count($newdatearray); print_date_array($newdatearray);}
	return($newdatearray);
}

/* --------------------------------------------------------------------------------------------------- */
function amr_expand (&$datearray, $pby, $type,$tz) { /* array of y,m,d,h,m,s, and array of by 'x'.  Note could be negative !! */
	/* set the first one and then copy and set the rest */
	if (empty($datearray)) return(false);
	$first = true;
	$sofar = count($datearray);
	if (isset($_GET['rdebug'])) { echo '<br /><b>Expanding with ' .$type. ' </b>....So far have '.$sofar.' dates to apply bys = '; print_r ($pby); }
	$newdatearray = array();
//	$new = $datearray;
	foreach ($datearray as $i => $datea) {
		foreach ($pby as $j => $m) { /* eg j = bymonth, and $m = 8,9,10,11,12 , or neg */
			$new = $datea;
			if ($m < 1) {
				$day = amr_create_date_from_parts ($datea, $tz);
				if (is_object($day)) {
					$daysinmonth = $day->format('t');
					$lastxdayofmonth = ($daysinmonth+1)+$m;
					$new[$type] = $lastxdayofmonth;
				}
			}
			else {
				$new[$type] = $m;
			}
			if (!empty ($new)) $newdatearray[] = $new;
		}
	}
	return($newdatearray);
}
/* --------------------------------------------------------------------------------------------------- */

function amr_limit (&$datearray, $pby, $type) { /* array of y,m,d,h,m,s, and array of by 'x'*/
	/* set the first one and then copy and set the rest */
	if (empty($datearray)) return(false);
	if (isset($_GET['rdebug'])) { echo '<hr><b>Limiting ' .$type. ' </b>....So far have '.count($datearray).' dates to apply bys = '; print_r ($pby); }
	foreach ($datearray as $i => $datea) {
		if (!(in_array($datea[$type], $pby))) unset ($datearray[$i]);
	}
	if (isset($_GET['rdebug'])) {echo '<hr>After limiting: '.count($datearray).' <br />';print_date_array($datearray);}
	return($datearray);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_special_expand_by_day_of_week_and_yearly (&$datearray, $pbys, $tz) { /* note 2 */
global $amr_globatz;

	if (isset($_GET['rdebug'])) { echo '<hr>'.'starting special expand by day of week and yearly ';	print_r($pbys); print_date_array($datearray);}
	/* For each date array - take the year and the month - get the first of the month, then the get the first day with that day of week, then get them all or the nbyday  */
	if (isset($pbys['BYDAY']))  $pbyday = $pbys['BYDAY'];
	else if (isset($pbys['NBYDAY']))  $pbyday = $pbys['NBYDAY'];
	else return ($datearray);
	foreach ($datearray as $i=> $datea) {
		$d = $datea;
		$d['day'] 	= 1;
		$d['month'] = 1;
		$first 		= amr_create_date_from_parts ($d, $tz);
		$d['day']	= 31;
		$d['month'] = 12;
		$last 		= amr_create_date_from_parts ($d, $tz);
		$dateobj 	= new DateTime('',$amr_globaltz);
		if (isset($_GET['rdebug'])) echo '<br />Starting from '.$first->format('Ymd l').' till '.$last->format('Ymd l');
		foreach ($pbyday as $byday => $bool ) { /* get the first day that is that day of week */
			$firstbyday  = amr_goto_byday ($first, $byday, '+'); /* so we should have the first 'byday' of the year */
			$lastbyday 	= amr_goto_byday ($last, $byday, '-');
			if (isset($_GET['rdebug'])) echo '<br />Doing '.$byday. ' with 1st '.$firstbyday ->format('Ymd l').' or last '.$lastbyday->format('Ymd l');
			if (is_array($bool)) {/* then have numeric - possibly many */
				foreach ($bool as $num) {
					if ($num < 0) { /* should never actually be 0 */ /* *** go to end and work back */
						$num=$num+1;
						$dateobj = clone ($lastbyday);
						if (!($num == 0)) date_modify($dateobj,(($num)*7).' days');
						if (($dateobj->format('Y') == $datea['year']))  {/* If still in the same year */
							$newdatearray[] = amr_get_date_parts ($dateobj);
							//if (isset($_GET['rdebug'])) echo '<br />Saved '.$dateobj->format('Ymd l');
						}
					}
					else if ($num > 0) {
						$num=$num-1;
						$dateobj = clone($firstbyday);
						if (!($num == 0)) date_modify($dateobj,'+'.(($num)*7).' days');
						if (($dateobj->format('Y') == $datea['year'])) { /* If still in the same year */
							$newdatearray[] = amr_get_date_parts ($dateobj);
							//if (isset($_GET['rdebug'])) echo '<br />Saved '.$dateobj->format('Ymd l');
						}
					}

				}
			}
			else {
				$dateobj = clone ($firstbyday);
				while ($dateobj <= $lastbyday) {
					$newdatearray[] = amr_get_date_parts ($dateobj);
					//if (isset($_GET['rdebug'])) echo '<br />saved '.$dateobj->format('Ymd l');
					date_modify($dateobj,'+7 days');
				}
			}
		}
	}
	if (isset($_GET['rdebug'])) {print_date_array($newdatearray);    echo '<hr>';}
	return ($newdatearray);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_expand_by_day_of_week_for_weekly (&$datearray, $pbys, $tz, $wkst) { /* Note 1 in monthly frequency only  */
global $amr_globaltz;
	if (isset($_GET['rdebug'])) { echo '<hr><b>starting special expand by day of week in weekly with wkst:'.$wkst.' </b>';
		var_dump($pbys);
		print_date_array($datearray);
	}
	if (isset($pbys['BYDAY']))  
		$pbyday = $pbys['BYDAY'];
	else if (isset($pbys['NBYDAY']))  
		$pbyday = $pbys['NBYDAY'];
	else return ($datearray);
	/* For each date array -  get the first day with that day of week, then get the next byday  */
	$dateobj = new DateTime('now',$amr_globaltz);
	foreach ($datearray as $i=> $datea) {
		$day 	= amr_create_date_from_parts ($datea, $tz);
		if (is_object($day)) {
			$day1 	= amr_get_start_of_week($day, $wkst); // in ics terms not human
			if (isset($_GET['rdebug'])) echo '<br />Got start of week'.$day1->format('Ymd l');
			foreach ($pbyday as $byday => $bool ) {
				if (isset($_GET['rdebug'])) echo '<br />BYDAY='.$byday.' ';
				if (!(is_array($bool)))  {/* else have numeric - possibly many  - skip for weekly is meaningless */
					$dateobj = amr_goto_byday ($day1, $byday, '+');/* get the first day that is that day of week */
					$newdatearray[] = amr_get_date_parts ($dateobj);
				}
			}
		}
	}
	if (isset($_GET['rdebug'])) echo '<hr>';
	if (!empty($newdatearray)) return ($newdatearray);
	else return (null);
}
/* -------------------------------------------------------------------------------------------------------------- */
function amr_special_expand_by_day_of_week_and_month_note2 (&$datearray, $pbys, $tz) { 

/* Note 2 First and Last are relative to the year and the month is a check */
	if (isset($_GET['rdebug'])) { echo '<hr><b>starting special expand by day of week and month in year freq </b>';	print_date_array($datearray);}
		if (isset($pbys['BYDAY']))  $pbyday = $pbys['BYDAY'];
	else if (isset($pbys['NBYDAY']))  $pbyday = $pbys['NBYDAY'];
	else return ($datearray);
	/* For each date array - take the year - get the first day, then the get the first day with that day of week, then get them all or the nbyday  */
	foreach ($datearray as $i=> $datea) {
		$d = $datea;
		$d['day'] = 1;
		$d['month'] = 1;
		$firstdayofyear = amr_create_date_from_parts ($d, $tz);
		$d['day'] = 31;
		$d['month'] = 12;
		$lastdayofyear = amr_create_date_from_parts ($d, $tz);
		$dateobj = amr_newDateTime();
		foreach ($pbyday as $byday => $bool ) {
			if (isset($_GET['rdebug'])) echo '<br />'.$byday.' ';
			if (is_array($bool)) {/* then have numeric - possibly many */
				foreach ($bool as $num) {
					if (($num < 0) and ($num >= -53)) { /* *** go to end and work back */
						$dateobj = clone ($lastdayofyear);
						if (isset($_GET['rdebug'])) { echo '<br/> got end of year '.$dateobj->format('Ymd l');}
						$dateobj = amr_goto_byday ($dateobj, $byday, '-');/* get the last day that is that day of week */
						if (isset($_GET['rdebug'])) { echo ' get day '.$dateobj->format('Ymd l');}
						$num = ($num+1)*7;  /* num is negative */
						if (!($num == 0)) date_modify($dateobj,$num.' days');	/* date modify deos not like zero in some phps */
						if (isset($_GET['rdebug'])) { echo ' and then adjust back '.$num.' '.$dateobj->format('Ymd l');}
						if (($datea['month'] == $dateobj->format('m')) and
							($datea['year'] == $dateobj->format('Y') )	)  {/* if still in the same month and same year  */
							$newdatearray[] = amr_get_date_parts ($dateobj);
							if (isset($_GET['rdebug']))  echo ' -Save';
						}
					}
					else if ($num <= 53){
						date_modify($dateobj,'+'.($num*7-1).' days');
						$newdatearray[] = amr_get_date_parts ($dateobj);
					}
					else return($datearray);/* invalid numeric byday */
				}
			}
			else {
				$dateobj = amr_goto_byday ($firstdayofyear, $byday, '+');/* get the first day that is that day of week */
//				$dayofyear = $dateobj->format('z'); /*  day of the year */
				while ($dateobj <= $lastdayofyear) {
					$d['day'] = $dateobj->format('d');
					$d['month'] = $dateobj->format('m');
					$newdatearray[] = $d;
					date_modify($dateobj,'+7 days');
				}
			}
		}
	}
	if (isset($_GET['rdebug'])) echo '<hr>';
	return ($newdatearray);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_special_expand_by_day_of_week_and_month_note1 (&$datearray, $pbys, $tz) { /* Note 1 in monthly frequency only  */
global $amr_globaltz;
	if (isset($_GET['rdebug'])) {
		echo '<hr><b>starting special expand by day of week in monthly </b>';
		print_date_array($datearray);
	}

	if (isset($pbys['BYDAY']))  
		$pbyday = $pbys['BYDAY'];
	elseif (isset($pbys['NBYDAY']))  
		$pbyday = $pbys['NBYDAY'];
	else 
		return ($datearray);
	/* For each date array - take the year and the month - get the first of the month, then the get the first day with that day of week, then get them all or the nbyday  */
	$dateobj = amr_newDateTime();
	foreach ($datearray as $i=> $datea) {
		$d = $datea;
		$d['day'] = 1;
		$day1 = amr_create_date_from_parts ($d, $tz);
		if (!is_object ($day1) ) { skip;}

		$daysinmonth = $day1->format('t');
		foreach ($pbyday as $byday => $bool ) {
			if (isset($_GET['rdebug'])) {echo '<br />BYDAY='.$byday.' dealing with:'; var_dump($bool); }
			if (is_array($bool)) {/* then have numeric - possibly many */
				foreach ($bool as $num) {
					if (($num < 0) and ($num >= -5)) { /* *** go to end and work back */
						$dateobj = clone ($day1);
						date_modify($dateobj,'+'.($daysinmonth-1).' days');
						if (isset($_GET['rdebug'])) { echo ' got end of month '.$dateobj->format('Ymd l');}
						$dateobj = amr_goto_byday ($dateobj, $byday, '-');/* get the last day that is that day of week */
						if (isset($_GET['rdebug'])) { echo ' get day '.$dateobj->format('Ymd l');}
						$num = ($num+1)*7;
						//if (!($num == 0)) date_modify($dateobj,'-'.$num.' days');	/* date modify deos not like zero in some phps */
						if (!($num == 0)) date_modify($dateobj,$num.' days');	/* date modify deos not like zero in some phps */
						if (isset($_GET['rdebug'])) { echo ' and then adjust by '.$num.' <br />'.$dateobj->format('Ymd l');}
					}
					else if ($num <= 5) {
						if (isset($_GET['rdebug'])) { echo ' we have positive num:'.$num.' starting from day1:'.$day1->format('Ymd l');}
						$dateobj = clone ($day1);
						$dateobj = amr_goto_byday ($dateobj, $byday, '+');/* get the first day that is that day of week */
						$num = (($num-1)*7);
						if (!($num == 0)) 
							date_modify($dateobj,'+'.$num.' days');
						if (isset($_GET['rdebug'])) { echo ' num = '.$num.' '.$dateobj->format('Ymd l');}
					}
					/* else invalid numeric byday */
					if ($datea['month'] == $dateobj->format('m'))  /* if still in the same month */
						$newdatearray[] = amr_get_date_parts ($dateobj);
				}
			}
			else {
				$dateobj = amr_goto_byday ($day1, $byday, '+');/* get the first day that is that day of week */
				$nextday = $dateobj->format('j'); /*  */
				if ($nextday < 0) $nextday = get_oldweekdays($dateobj); /* php seems to break around 1760 */
				while ($nextday <= $daysinmonth) {
					$d['day'] = $nextday;
					$newdatearray[] = $d;
					$nextday = $nextday + 7;
				}
			}
		}
	}
	if (isset($_GET['rdebug'])) echo '<hr>';
	if (!empty($newdatearray)) return ($newdatearray);
	else return (null);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_expand_by_day_of_week_for_year (&$datearray, $pbys, $tz) {
	/* we know the BYDAY exists, lets also check for others as per Note 2 in the limit/expand table */
		/* set the first one and then copy and set the rest */
	if (empty($datearray)) return(false);
	$first = true;
	$sofar = count($datearray);
	if (isset($_GET['rdebug'])) { echo '<br /><b>Dealing with by_day_of_week_for_year </b>....So far have '.$sofar.' dates to apply bys = '; print_r ($pbys); }
	if (isset($pbys['BYWEEKNO'])) { /* if byweekno was also set -  special  expand for weekly and day of week .  We should already have the week's expanded ? */
			$newdatearray = amr_limit_by_day_of_week ($datearray, $pbys['BYDAY'], $tz );
		}
	if (isset($pbys['month'])) { /* if bymonth was also set - special monthly expand by day of week - month expand already done */
			$newdatearray = amr_special_expand_by_day_of_week_and_month_note2 ($datearray, $pbys, $tz);
		}
	else $newdatearray = amr_special_expand_by_day_of_week_and_yearly ($datearray, $pbys, $tz);

	if (isset($_GET['rdebug'])) {echo '<hr>New date array: '.count($newdatearray); print_date_array($datearray);}
	return($newdatearray);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_limit_by_yearday (&$datearray, $pbyyearday, $tz) {
	if (empty($datearray)) return;
	if (isset($_GET['rdebug'])) { echo '<br>Limit by year day';}
	foreach ($datearray as $i=> $d) {
		$dateobj = amr_create_date_from_parts ($d, $tz);
		if (is_object ($dateobj) ) {
			$dateyearday = $dateobj->format('z')+1;
			if (!in_array($dateyearday,$pbyyearday) ) unset ($datearray[$i]);
		}
		else unset ($datearray[$i]);
	}
	return ($datearray);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_limit_by_day_of_week (&$datearray, $pby, $tz) {
	global $amr_day_of_week_from_no; /* MO=> 1, 'TU' => 2... 'SU'=> 6 etc */
	if (empty($datearray)) return;
	if (isset($_GET['rdebug'])) { echo '<br>Limit by day of week '; print_r($pby); var_dump($datearray);}
	foreach ($datearray as $i=> $d) {
		$dateobj = amr_create_date_from_parts ($d, $tz);
		if (is_object ($dateobj) ) {
			$w = $dateobj->format('w');
			if ($w == '-1') {$w = get_oldweekdays($dateobj);} /* php seems to break around 1760 and google passed a zero year date in an ics file  */
			$w = $amr_day_of_week_from_no[$w];
			if (!isset($pby[$w]) ) {
				if (isset($_GET['rdebug'])) { echo '<br>Day of week is '.$w.' reject';}
				unset ($datearray[$i]);
			}
		}
		else {
			if (isset($_GET['rdebug'])) { echo '<br>Not an object '; var_dump($dateobj);}
			unset ($datearray[$i]);
		}
	}
	if (isset($_GET['rdebug'])) { echo '<br />Returning limited '; print_r($datearray);}
	return ($datearray);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_create_date_from_parts ($d, $tz) { /* create a date object from the parts array, with the TZ passed */
	// wtf? new DateTime();
	$datestring = $d['year'].'-'.$d['month'].'-'.$d['day'].' '.$d['hour'].':'.$d['minute'].':'.$d['second'];
	
	$possdate = amr_create_date_time ($datestring, $tz);
	
	//try { $possdate =  new DateTime($datestring, $tz);
	//	}
	//catch (Exception $e) {
	//			echo '<b>'.__('Unexpected error creating date with string: '.$datestring,'amr-ical-events-list').'</b>';
	//			echo $e->getMessage();
	//			return (false);
	//			}
	// somehow this exception business not helping?
	//if (isset($_GET['rdebug'])) { echo '<br />Datestring: '.$datestring.' as date: '.$possdate->format('c');}
	// if date does not match date string, then reject
	if ($d['day'] > 28) { //then check whther date was created correctly, or whether it should have been rejected.
		$newday = $possdate->format('j');
		if (!($newday == $d['day'])) {
			if (isset($_GET['rdebug'])) { echo '<br />Rejecting...';}
			return (false);
		}
	}
	return ($possdate );
}
/* --------------------------------------------------------------------------------------------------- */
function amr_get_a_closer_start ($start, $astart, $int) { // Note can only do this if no COUNT or BYSETPOS, else we break the rule
	$closerstart = new datetime();  // if cloning ok, dont need tz
	$closerstart = clone $start;
	if (isset($_GET['rdebug'])) {echo '<br />Start was set at '.$start->format('c');}
	while ($closerstart < $astart ) {
//		if (isset($_GET['rdebug'])) { echo '<br />Main start= '. $astart->format('c'). ' '. $closerstart->format('c');}
		$closerstart = amr_increment_datetime ($closerstart, $int) ;
		if ($closerstart < $astart) $start = $closerstart;
	}
	if (isset($_GET['rdebug'])) {
		echo '<br />Closer start is '.$start->format('c').' after adding iterations <br />';
	}
	unset($closerstart);
	return ($start);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_limit_occurences ($repeats, $count) { 
	/* we should check contraints like count etc here rather */
	
	$num_events = count($repeats);
	$count = apply_filters('amr_event_repeats',$count);
	
	if (isset ($_GET['cdebug'])) { 
		echo '<br />We have '. $num_events.' events.  We want max:'.$count;
		foreach ($repeats as $i=>$r) {
			echo '<br />'.$i.' '.$r->format('c');
		}
	}
	if ($num_events > $count)  {
		$repeats = array_slice($repeats,0, $count);
		}
	if (isset ($_GET['cdebug'])) {
		echo '<br />Limit date array, now have :'.count($repeats);
		foreach ($repeats as $i=>$r) {
			echo '<br />'.$i.' '.$r->format('c');
		}
	}
	return($repeats);
}
/* --------------------------------------------------------------------------------------------------- */
function amr_process_RRULE($p, $start, $astart, $aend, $limit )  {
	 /* RRULE a parsed array.  If the specified event repeats between the given start and
	 * end times, return one or more nonrepeating date strings in array
	 */
	global	$amr_wkst,
			$amr_timeperiod_conv; /* converts daily to day etc */
	/* now we should have if they are there: $p[freq], $p[interval, $until, $wkst, $ count, $byweekno etc */
	/* check  / set limits  NB don't forget the distinction between the two kinds of limits  */
	$tz = date_timezone_get($start);
	
	if (isset($_GET['rdebug'])) {
		echo '<br />&nbsp;start='.$start->format('c')
		.' <br />astart='.$astart->format('c') 
		.'<br /> parameter passed: ';
		if (is_array($p))
			foreach ($p as $k => $i) { 
				echo $k. ' '; 
				if (is_object($i)) echo $i->format('c'); 
				else print_r($i); echo  '<br />';
			}
		else {echo  '<br />rule passed'; var_dump($p);}
	}
	if (!isset($p['COUNT']))   
		$count = AMR_MAX_REPEATS;  /* to avoid any chance of infinite loop! */
	else 
		$count = $p['COUNT'];
		
	if (isset($_GET['cdebug'])) echo '<br />Limiting the repeats to '.$count;
	
	$until = amr_newDateTime();
	$original_until = amr_newDateTime();	

	if (!isset($p['UNTIL']))  {
		$until = clone $aend;
		$original_until = clone $aend;
	}	
	else {
		$until = clone $p['UNTIL'];
		$original_until = clone $p['UNTIL'];
		if ($until > $aend)	
			$until = clone $aend;
	}
	// 2014 07 09
	$original_start = amr_newDateTime();
	$original_start = clone $start;
	
	if (amr_is_before ($until, $astart )) { 
		return(false); 
		}/* if it ends before our overall start, then skip */

	/* now prepare out "intervals array for date incrementing eg: p[monthly] = 2 etc... Actualy there should only be 1 */
	if (isset($p['FREQ'])) { /* so know yearly, daily or weekly etc  - setup increments eg 2 yearsly or what */
		if (!isset ($p['INTERVAL'])) $p['INTERVAL'] = 1;
		switch ($p['FREQ']) {
			case 'WEEKLY': $int['day'] = $p['INTERVAL'] * 7; break;
			default: {
				$inttype = $amr_timeperiod_conv[$p['FREQ']];
				$int[$inttype] = $p['INTERVAL'];
			}
		}
		$freq = $p['FREQ'];
	}
	
	/*  use the freq increment to get close to our listing start time.  If we are within one freq of our listing start, we should be safe in calculating the odd rrules. */
	/* NOTE we can only do this if we do not have the count or a bysetpos !!!!   */
//	if (empty($int)) var_dump($p);
	if (empty($p['COUNT']) and empty($p['BYSETPOS'])) {
		$start = amr_get_a_closer_start($start, $astart, $int);				
	}	
	// 20110301 the nbydays, especially the negs, require that one initially consider a wider valid range so as not to unintentionally exclude a date before the expand/contract has beeen applied .  Ie we might iterate to 28th and exclude, but actually once one has applied the -2Mo or some such rule, the date would have contracted into the valid range.  So apply restrictions later.	

	$until = amr_increment_datetime ($until, $int);
	
	unset ($p['UNTIL']);  /* unset so we can use other params more cleanly, we have count saved etc */
	unset ($p['COUNT']); unset ($p['FREQ']); unset ($p['INTERVAL']);
	if (!empty($p['WKST'])) {	
		$wkst = $p['WKST']; unset($p['WKST']);
		}
	else 
		$wkst = $amr_wkst;
	if (count($p) === 0) {$p=null; }  /* If that was all we had, get rid of it anyway */

	if (isset ($p['NBYDAY'])) {
	/* if we separated these in the parsing process, merge them here,    NOOO - will cause problems with the +1's and bool */
		if (isset ($p['BYDAY'])) $p['BYDAY'] = array_merge ($p['NBYDAY'], $p['BYDAY']);
		else $p['BYDAY'] = $p['NBYDAY'];
		unset ($p['NBYDAY']);
	}
	while ($start <= $until) {	 /* don't check astart here - may miss some */

		$datearray[] = amr_get_date_parts($start);
		if (isset($_GET['rdebug'])) { 
			echo '<hr>Checked start against extra until (1 extra iteration to allow for negativebydays) etc '.$start->format('c').' '.$until->format('c');
			echo '<br />date array = '; var_dump($datearray);
		}
		
		switch ($freq) { /* the 'bys' are now in an array $p .  NOTE THE sequence here is important */
			case 'SECONDLY': {
				if (isset($p['month'])) 		$datearray = amr_limit ($datearray, $p['month'], 'month');
				/* BYWEEK NO not applicable here */
				if (isset($p['BYYEARDAY'])) 	$datearray = amr_limit_by_yearday ($datearray, $p['BYYEARDAY'],$tz);
				if (isset($p['day'])) 			$datearray = amr_limit   ($datearray, $p['day'], 'day');
				if (isset($p['BYDAY'])) 		$datearray = amr_limit_by_day_of_week ($datearray, $p['BYDAY'],$tz);
//				foreach ($p as $i => $by) amr_limit_dates_withby($dates);
				if (isset($p['hour'])) 			$datearray = amr_limit 	($datearray, $p['hour'],'hour');
				if (isset($p['minute'])) 		$datearray = amr_limit  ($datearray, $p['minute'],'minute');
				if (isset($p['second'])) 		$datearray = amr_limit	($datearray, $p['second'],'second');
				break;
				}
			case 'MINUTELY': {
				if (isset($p['month'])) 		$datearray = amr_limit ($datearray, $p['month'], 'month');
				/* BYWEEK NO not applicable here */
				if (isset($p['BYYEARDAY'])) 	$datearray = amr_limit_by_yearday ($datearray, $p['BYYEARDAY'],$tz);
				if (isset($p['day'])) 			$datearray = amr_limit  ($datearray, $p['day'], 'day');
				if (isset($p['BYDAY'])) 		$datearray = amr_limit_by_day_of_week ($datearray, $p['BYDAY'],$tz);
				if (isset($p['hour'])) 			$datearray = amr_limit 	($datearray, $p['hour'], 'hour');
				if (isset($p['minute'])) 		$datearray = amr_limit  ($datearray, $p['minute'],'minute');
				if (isset($p['second'])) 		$datearray = amr_expand ($datearray, $p['second'],'second',$tz);
				break;
				}
			case 'HOURLY': {
				if (isset($p['month'])) 		$datearray = amr_limit ($datearray, $p['month'], 'month');
				/* BYWEEK NO not applicable here */
				if (isset($p['BYYEARDAY'])) 	$datearray = amr_limit_by_yearday ($datearray, $p['BYYEARDAY'],$tz);
				if (isset($p['day'])) 			$datearray = amr_limit   ($datearray, $p['day'], 'day');
				if (isset($p['BYDAY'])) 		$datearray = amr_limit_by_day_of_week ($datearray, $p['BYDAY'],$tz);
				if (isset($p['hour'])) 			$datearray = amr_limit 	($datearray, $p['hour'],'hour',$tz);
				if (isset($p['minute'])) 		$datearray = amr_expand ($datearray, $p['minute'],'minute',$tz);
				if (isset($p['second'])) 		$datearray = amr_expand ($datearray, $p['second'],'second',$tz);
				break;
				}
			case 'DAILY': {
				if (isset($p['month'])) 		$datearray = amr_limit  ($datearray, $p['month'], 'month');
				/* BYWEEK NO and BYYEARDAY not applicable here */
				if (isset($p['day'])) 			$datearray = amr_limit   ($datearray, $p['day'], 'day');
				if (isset($p['BYDAY'])) 		$datearray = amr_limit_by_day_of_week ($datearray, $p['BYDAY'],$tz);
				if (isset($p['hour'])) 			$datearray = amr_expand ($datearray, $p['hour'],'hour',$tz);
				if (isset($p['minute'])) 		$datearray = amr_expand ($datearray, $p['minute'],'minute',$tz);
				if (isset($p['second'])) 		$datearray = amr_expand ($datearray, $p['second'],'second',$tz);
			break;
			}
			case 'WEEKLY': {
				if (isset($p['month'])) 		$datearray = amr_limit ($datearray, $p['month'], 'month');
				/* BYWEEK NO and BYYEARDAY and BYMONTH DAY not applicable here */
				if (isset($p['BYDAY'])) 		$datearray = amr_expand_by_day_of_week_for_weekly ($datearray, $p,$tz,$wkst);
				if (isset($p['hour'])) 			$datearray = amr_expand ($datearray, $p['hour'],'hour',$tz);
				if (isset($p['minute'])) 		$datearray = amr_expand ($datearray, $p['minute'],'minute',$tz);
				if (isset($p['second'])) 		$datearray = amr_expand ($datearray, $p['second'],'second',$tz);
			break;
			}
			case 'MONTHLY': {
				if (isset($p['month'])) 		$datearray = amr_limit ($datearray, $p['month'], 'month');
				/* BYWEEK NO and BYYEARDAY not applicable here */
				if (isset($p['day'])) 			$datearray = amr_expand ($datearray, $p['day'], 'day',$tz);
				if ((isset($p['BYDAY'])) or (isset($p['NBYDAY']))) 		{
				/* as per note 1  on page 44 of http://www.rfc-archive.org/getrfc.php?rfc=5545 */
					if (isset($p['day'])) /* BYDAY limits if BYMONTH DAY is present , else a special expand for monthly */
												$datearray = amr_limit_by_day_of_week ($datearray, $p, $tz);
					else 						$datearray = amr_special_expand_by_day_of_week_and_month_note1 ($datearray, $p,$tz);
				}
				if (isset($p['hour'])) 			$datearray = amr_expand ($datearray, $p['hour'],'hour',$tz);
				if (isset($p['minute'])) 		$datearray = amr_expand ($datearray, $p['minute'],'minute',$tz);
				if (isset($p['second'])) 		$datearray = amr_expand ($datearray, $p['second'],'second',$tz);
				break;
				}
			case 'YEARLY': {

				if (isset($p['month'])) 		$datearray = amr_expand ($datearray, $p['month'], 'month',$tz);
				if (isset($p['BYWEEKNO'])) 		$datearray = amr_expand_by_weekno ($datearray, $p['BYWEEKNO'],$tz);
				if (isset($p['BYYEARDAY'])) 	$datearray = amr_expand_by_yearday ($datearray, $p['BYYEARDAY'],$tz);
				if (isset($p['day'])) 			$datearray = amr_expand ($datearray, $p['day'], 'day',$tz);
				if (isset($p['BYDAY'])) {
					if (isset($p['day']) or isset($p['BYYEARDAY'])) /*Note 2:  BYDAY limits if BYMONTH DAY or BYYEARDAY  is present */
												$datearray = amr_limit_by_day_of_week ($datearray, $p['BYDAY'],$tz);
					else 						$datearray = amr_expand_by_day_of_week_for_year ($datearray, $p,$tz);
				}
//				if (isset($p['NBYDAY'])) {
//					if (isset($p['day']) or isset($p['BYYEARDAY'])) /*Note 2:  BYDAY limits if BYMONTH DAY or BYYEARDAY  is present */
//												$datearray = amr_limit_by_day_of_week ($datearray, $p['NBYDAY'],$tz);
//					else 						$datearray = amr_expand_by_day_of_week_for_year ($datearray, $p,$tz);
//				}
				if (isset($p['hour'])) 			$datearray = amr_expand ($datearray, $p['hour'],'hour',$tz);
				if (isset($p['minute'])) 		$datearray = amr_expand ($datearray, $p['minute'],'minute',$tz);
				if (isset($p['second'])) 		$datearray = amr_expand ($datearray, $p['second'],'second',$tz);
				break;
			}	
		}
		$datearray = amr_sort_date_array($datearray);
		if (isset ($_GET['rdebug'])) {echo '<br /> We have in date array: '; print_r($datearray); }
			// There will only be > 1 if there was an expanding BY: 
			
		if (!empty($datearray) and !empty($p['BYSETPOS'])) 	{
			$datearray = amr_limit_by_setpos ($datearray, $p['BYSETPOS']);
			if (isset ($_GET['rdebug'])) {
				echo '<br />Selected after bysetpos:'; print_r($p['BYSETPOS']); 
				echo '</br>'; print_date_array($datearray);	}
			$datearray = amr_sort_date_array($datearray); /* sort again as the set position may have trashed it */
		}
		
//		$num_events = count($datearray);
//		if (isset ($_GET['cdebug'])) {echo '<br />We have '. $num_events.' events.  We want max:'.$count;	}
//		if ($num_events > $count)  $datearray = array_slice($datearray,0, $count);
//		if (isset ($_GET['cdebug'])) {echo '<br />Limit date array, now have :'.count($datearray);
//			echo '<br>From  '.$astart->format('Y m d h:i').' until '.$until->format('Y m d h:i');
//		}
		if (!empty ($datearray)) {
			foreach ($datearray as $d) { /* create the date objects  */
				$possdate = amr_create_date_from_parts ($d, $tz);
				if (is_object ($possdate) ) {
					if (isset ($_GET['rdebug'])) echo '<br>Possdate='.$possdate->format('Y m d h:i:s');
//					if 	(($possdate <= $until) and ($possdate >= $astart)) {
						$repeats[] = $possdate;
//						if (isset ($_GET['rdebug'])) echo ' - saved';
					}
				}
		}
		
		unset ($datearray);
		/* now get next start */
		$start = amr_increment_datetime ($start, $int);
		
		if (isset ($_GET['rdebug'])) echo '<hr>Next start data after incrementing = '.$start->format('Y m d l h:i:s');
	} /* end while*/
	//-----------------------------------------------------------------------
	if (isset($_GET['rdebug'])) { 
			echo '<hr>Stop now..checked start against extra until <br>start='
			.$start->format('c')
			.'<br>until='.$until->format('c').' the extra until!';
			if ($start > $until) echo '<br /><b>php says start > extra until </b>';
		}

	if (!empty ($repeats)) {
	
		$repeats = amr_limit_occurences ($repeats, $count);
		
		foreach ($repeats as $i=> $d) { /* check if it is within date limits  */
			if (isset ($_GET['rdebug'])) {
				echo '<br>*** Check for this rrule - original until. '
				.'<br />-------astart = '.$astart->format('c')
				.'<br />original start = '.$original_start->format('c')
				.'<br />instancedate = '.$d->format('c')
				.'<br />originaluntil= '.$original_until->format('c')
				.'<br />';
			}
		
			if 	(!(($d <= $original_until) and ($d >= $original_start))) { //note these are rrule limits, not the overall limits
				unset($repeats[$i]);
				if (isset ($_GET['rdebug'])) 
					echo '<br>Event instance not within rrule limits - <b>removed</b> '.$d->format('Y m d h:i').'<br />';
			}
			else {
				if (isset ($_GET['rdebug'])) 
					echo '<br>Event instance within rrule limits '.$d->format('Y m d h:i').'<br />';
			}
		}			
	}

	if (isset ($_GET['rdebug'])) {
		if (empty ($repeats)) echo '<b>No repeats!</b><hr>'; 
		else {
			echo '<b>'.count($repeats).' repeats before further processing, exclusions etc</b><hr>';
			foreach ($repeats as $i =>$r) echo '<br />'.$r->format('c');
			echo '<hr/>Need use debugexc to check exclusion logic';
			}
	}
	if (empty ($repeats)) return(null);
	return ($repeats);

	}
/* ---------------------------------------------------------------------------- */
function amr_parse_RRULEparams ( $args)	{

global $amr_bys;
global $amr_day_of_week_no;
global $amr_wkst;
global $amr_rulewkst;
global $amr_globaltz;

		if (!is_array($args)) return false;
		foreach ($args as $i => $a) parse_str ($a);
		/* now we should have if they are there: $freq, $interval, $until, $wkst, $ count, $byweekno etc */
		if (isset ($BYMONTH)) {
			$p['month'] = explode (',',$BYMONTH);
			foreach ($p['month'] as $j => $k) {
				if ($k < 0) { $p['month'][$j] = 13 + $k; }
			}
		}
		if (isset ($BYMONTHDAY)) {
			$p['day'] = explode (',',$BYMONTHDAY);
		}
		if (isset ($BYHOUR)) {
				$p['hour'] = explode (',', $BYHOUR);
				foreach ($p['hour'] as $j => $k) {
				if ($k < 0) { $p['hour'][$j] = 24 + $k; }
				}
		}
		if (isset ($BYMINUTE)) {
			$p['minute'] = explode (',', $BYMINUTE);
			foreach ($p['minute'] as $j => $k) {
				if ($k < 0) { $p['minute'][$j] = 60 + $k; }
				}
		}
		if (isset ($BYSECOND)) {
			$p['second'] = explode (',', $BYSECOND);
			foreach ($p['second'] as $j => $k) {
				if ($k < 0) { $p['second'][$j] = 60 + $k; }
				}
		}
		if (isset ($BYDAY)) {
			$p['BYDAY'] = explode(',', $BYDAY);
			foreach ($p['BYDAY'] as $j => $k) {
				$l = strlen($k);
				if ($l > 2) {  /* special treatment required - flag to re handle, keep as we want to isolate a subset anyway */
					$thenumber = substr($k, 0, $l-2);
					$theday = substr($k, $l-2, $l);
					$p['NBYDAY'][$theday][] = $thenumber; /* could be multiple numeric for same day eg first and last */
					unset ($p['BYDAY'][$j]);
				}
				else {
					$p['BYDAY'][$k] = true;
					unset ($p['BYDAY'][$j]);
				}
			}
		}
		if (isset($p['BYDAY']) and ( count($p['BYDAY']) < 1)) unset ($p['BYDAY']);
		if (isset ($BYWEEKNO)) 	{$p['BYWEEKNO'] 	= explode(',', $BYWEEKNO);}
		if (isset ($BYYEARDAY)) {$p['BYYEARDAY'] 	= explode(',', $BYYEARDAY);  }
		if (isset ($UNTIL)) 	{$p['UNTIL'] 		= amr_parseDateTime($UNTIL, $amr_globaltz);}
		if (isset ($COUNT)) 	{$p['COUNT']	 	= $COUNT;}
		if (isset ($INTERVAL)) 	{$p['INTERVAL'] 	= $INTERVAL;}
		if (isset ($FREQ)) 		{$p['FREQ'] 		= $FREQ;}
		if (isset ($BYSETPOS))  {$p['BYSETPOS'] = explode(',', $BYSETPOS);}
		if (isset ($WKST)) 		{
								 $p['WKST'] = $WKST;
								 $amr_wkst = $WKST;
		}
		if (!empty($p))	return ($p);  /* Need the array values to reindex the array so can acess start positions */
		else return (false);
	}
	/* ---------------------------------------------------------------------------- */
function amr_get_human_start_of_week (&$dateobj, $wkst) { // WORKS !
/* get the start of the week in human terms */
	global  $amr_day_of_week_no;
	$wkst_no = $amr_day_of_week_no[$wkst];	/* from 1=Mo to 7=SU */
	
	$php_wkstno = $wkst_no % 7; /* php uses 0=SU , so convert to that */
	
	$dayofweek = $dateobj->format('w'); /* 0=SU, 6 = SA */
	$adj = ($dayofweek - $php_wkstno + 7) % 7;
	$string = '-'.$adj.' days';

	date_modify ($dateobj,$string);
	//if (isset($_GET['debugwks'])) { echo '<br />wkstno='.$wkst_no.' phpwstno='.$php_wkstno.' dow='.$dayofweek.' adj='.$adj.
	//' get start of week using '.$string.' to'.$dateobj->format('c');}
	return ($dateobj);
}
/* ---------------------------------------------------------------------------- */
function amr_get_start_of_week (&$dateobj, $wkst) { // is it right - is it just ics terms or is it wrong
/* get the start of the week in ics terms, not ours necessarily according to the wkst parameter (Sat/Sun/Mon), returns new dat object  */
	global  $amr_day_of_week_no;
	
	$wkst_no = $amr_day_of_week_no[$wkst];	/* from 1=Mo to 7=SU */
	
	$dayofweek = $dateobj->format('w');
	
	//if (isset($_GET['debugwks'])) echo '<br/>dayofweek= '.$dayofweek.' wkst '.$wkst_no;
	
	if ($dayofweek == '-1') 
		$dayofweek = get_oldweekdays($dateobj); /* php seems to break around year 1760   */
	if ($dayofweek < $wkst_no)
		$adj = $wkst_no - $dayofweek;
	else
		$adj = $dayofweek - $wkst_no;
	$string = '-'.$adj.' days';
	date_modify ($dateobj,$string);
	return ($dateobj);
}
/* ---------------------------------------------------------------------------- */
function amr_increment_datetime ($dateobject, $int) {
	/* note we are only incrementing by the freq - can only be one?   */
	/* Now increment and try next repeat
	check we have not fallen foul of a by -or is that elsewhere ?? */
	if ((!isset ($int)) or (!is_array($int))) {echo 'unexpected error: no interval';return (false);}
//	if (isset ($_GET['rdebug'])) {echo '<br />Incre date '.$dateobject->format('c'); }
	foreach ($int as $i=>$interval) {  /* There should actually only be one */
//		if (isset ($_GET['rdebug'])) {echo '<br />Int= '.$i;}
		if ($i === 'month') { /* need special check to cope with php date modify add month bug */
			$day = $dateobject->format('j'); //day of month
			if ($day > 28) { // we may experience the php date modify bug
				date_modify($dateobject,'+'.$interval.' '.$i);
				$day2 = $dateobject->format('j'); //day of month
				if ($day2 < 28) //it went into next month, so reverse
					date_modify($dateobject,'-'.$day2.' days');
			}
			else date_modify($dateobject,'+'.$interval.' '.$i);
		}
		else date_modify($dateobject,'+'.$interval.' '.$i);
//		if (isset ($_GET['rdebug'])) {echo '<br />'.$dateobject->format('c'); }
	}
	return ($dateobject);
}
/* -------------------------------------------------------------------------------- */
function amr_parseRRULE($rrule)  {
	 /* RRULE's can vary so much!  Some examples:
		FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200
		FREQ=WEEKLY;UNTIL=19971007T000000Z;WKST=SU;BYDAY=TU,TH
	 */
	 	// if (isset ($_GET['rdebug'])) { echo '<br/>RRULE in parse: '; var_dump($rrule);}

		if (empty($rrule)) return (null);

		if (is_array($rrule) and !isset($rrule['FREQ'])) {
		//then we may have an array of rrules
			$r = array_shift($rrule);
			if (!is_string($r)) {
				// if (isset ($_GET['rdebug'])) {echo '<hr>RRULE already parsed';}
				return($r);
				}
			}
		else
			$r = $rrule;

			if (!is_string($r)) {
				// if (isset ($_GET['rdebug'])) {echo '<hr>RRULE already parsed';}
				return($r);
		}
		$p = explode (';', $r);
		// if (isset ($_GET['rdebug'])) { echo '<br/>RRULe: '; var_dump($p);}
		$p = amr_parse_RRULEparams ($p);

		return ($p);

	}
/* ---------------------------------------------------------------------------- */
function amr_get_last_xday_of_month ($date, $x=-1)	{ /* helper function format passed is NB.  Can be used for last day of year and last day of month*/
/* php 'last day of month' not working? */
		$last = date_create ($date->format('Y-m-01 H:i:s'), $date->getTimezone()); /* set to first of month  */
		$last->modify('+1 month');
		$x = (int) $x;
		$last->modify($x.' days');
		return ($last);
}
/* ---------------------------------------------------------------------------- */
function amr_get_last_day_of_year ($date)	{ /* helper function format passed is NB.  Can be used for last day of year and last day of month*/
/* php 'last day of month' not working? */
		$last = date_create ($date->format('Y-12-01 H:i:s'), $date->getTimezone()); /* set to first of month  */
		$last->modify('+1 month');
		$last->modify('-1 day');
		return ($last);
}
/* ---------------------------------------------------------------------------- */
function amr_get_last_day ($date, $format)	{ /* helper function format passed is NB.  Can be used for last day of year and last day of month*/
/* php 'last day of month' not working? */
		$last = date_create ($date->format($format), $date->getTimezone()); /* set to first of month  */
		$last->modify('+1 month');
		$last->modify('-1 day');
		return ($last);
}
/* ---------------------------------------------------------------------------- */
function amr_goto_byday ($dateobj, $byday, $sign)	{
	global $amr_day_of_week_no;
		$dayofweek = $dateobj->format('w'); /* 0=sunday, 6 = saturday */
		if ($dayofweek == '-1') 
			$dayofweek = get_oldweekdays($dateobj); /* php seems to break around 1760   */
			
		$target 	= $amr_day_of_week_no[$byday]; /*  mo=1 ,su=7  */
		$adjustment = $target - $dayofweek;
		if (isset ($_GET['rdebug'])) {
			echo '<br />GO to day of week from '.$dateobj->format('Ymd l').
			' going to '.$sign.$byday.' Target = '.$target.' Dayofweek = '.$dayofweek.' '.$dateobj->format('l');
		}
		if ($sign === '+') {
			if ($adjustment < 0) 
				$adjustment = $adjustment + 7;
		}
		else { // sign must be neg
			if ($adjustment > 0) 
				$adjustment = $adjustment-7;
		}
		
		if ($adjustment == 7)  $adjustment = 0;	 // else will skip the first one if it matches
		
		if (isset ($_GET['rdebug'])) echo '<br />Adj = '.	$adjustment;
		$d2 = new DateTime();  // if cloning ok dont need tz
		$d2 = clone ($dateobj);
		date_modify ($d2,$adjustment.' days');
		if (isset ($_GET['rdebug'])) echo ' Got date = '.	$d2->format('Ymd l');
	return ($d2);
	}
/* --------------------------------------------------------------------------------------------------- */
function amr_process_RDATE($p, $start, $end)  {
	 /* RDATE or EXDATE processes  a parsed array.  If the specified event repeats between the given start and
	 * end times, return one or more nonrepeating date strings in array

		RDATE:19970714T123000Z
		RDATE:19970714T083000
		RDATE;TZID=US-EASTERN:19970714T083000
		RDATE;VALUE=PERIOD:19960403T020000Z/19960403T040000Z,19960404T010000Z/PT3H
		RDATE;VALUE=DATE:19970101,19970120,19970217,19970421,19970526,19970704,19970901,19971014,19971128,19971129,19971225
		should be passed as object now?  *** amr check!!
	 */

	$repeats = array();
	if (is_object ($p))	{
		if (isset($_REQUEST['debugexc'])) {
			echo '<br> R or exdate Object passed '. amr_format_date('Y m j, g:i a P',$p);}
		if (amr_falls_between($p, $start, $end));
		$repeats[] = $p;
		}
	else
	if (is_array($p)) {
		foreach ($p as $i => $r) {
			if (amr_falls_between($r, $start, $end))  $repeats[] = $r;
		}
	}
	else {
		if (isset($_REQUEST['rdebug'])) {
		echo '<br />****Cannot process RDATE - Not an Object, Not an array passed <br />'; var_dump($p);}
		//if (amr_falls_between($p, $start, $end))  $repeats[] = $p;
	}
	//if (isset($_REQUEST['debugexc'])) { echo '<br/>*** Array of repeats '; var_dump($repeats); }
	return ($repeats);
	}
