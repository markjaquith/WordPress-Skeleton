<?php /* Pluggable functions that need to be loaded after the theme so that a theme functions.php can override
*/
/* --------------------------------------------------  */

if (!function_exists("nl2br2")) {
	function nl2br2($string) {

	$s2 = str_replace(array('\n\n','\r\n'), '<br /><br />', $string);

	$s2 = str_replace(array( '\r', '\n'), '<br />', $string);
	
	// optionally add // $s2 str_replace("=0D=0A", '<br />', $string);
	// or use a wordpress content filter

	return($s2);
	}
}
// ---------------------------------------------------------------
if (!function_exists('amr_events_sort_later_events_first')) {
	function amr_events_sort_later_events_first($constrained) {
		$constrained = amr_reverse_sort_by_key($constrained , 'EventDate');
		return ($constrained);
	}
}
// ---------------------------------------------------------------
if (!function_exists('amr_events_exclude_in_progress')) {
	function amr_events_exclude_in_progress($constrained) { // exclude any events that started before the start date
		global $amr_limits;
		foreach ($constrained as $i => $e) {
			if (amr_is_before($e['EventDate'], $amr_limits['start'])) {
				unset($constrained[$i]);
			}			
		}
		return ($constrained);
	}
}
// ---------------------------------------------------------------
if (!function_exists('amr_register_for_event')) {
	function amr_register_for_event() {
		return ("Register!");
	}
}
// ----------------------------------------------------------------------------------------
if (!function_exists('amr_handle_no_events')) {
	function amr_handle_no_events () {
		global $amr_options,
		$amr_limits;
		
		$thecal = '';
		if (!empty($amr_options['noeventsmessage'])) {
			$thecal .=  '<a class="noeventsmessage" style="cursor:help;" href="" title="'
			.amr_echo_parameters().'"> '
			/* translators: ignore this and translate the string found earlier " No events..." */
			.__($amr_options['noeventsmessage'],'amr-ical-events-list').'</a>';

			if ((isset($amr_limits['show_look_more'])) and ($amr_limits['show_look_more'])) {
					$thecal .= amr_show_look_more();
			}
			if ((isset($amr_limits['pagination'])) and ($amr_limits['pagination'])) {
					$thecal .= amr_semi_paginate();
			}
		}
		return ($thecal);
	} // end function
}
// ----------------------------------------------------------------------------------------
if (!function_exists('amr_human_time')) {
	function amr_human_time ($time) {
		if ($time == '000000') return (__('midnight', 'amr-ical-events-list'));  // to avoid am/pm confusion, note midnight is start of day
		else if ($time == '120000') return (__('midday', 'amr-ical-events-list'));  // to avoid am/pm confusion
		else return ($time);
	}
}
// ----------------------------------------------------------------------------------------
if (!function_exists('amrical_calendar_views')) {
function amrical_calendar_views () {
	global $amr_limits;

//	$link = amr_clean_link();  // // NOT clean link - must remember context.
	$link = remove_query_arg(array(
		'calendar',
		'agenda',
		'listtype',
		'eventmap'));

	if (!empty ($amr_limits['agenda']))   // did the shortcode tell us which list type to use as agenda
		$agenda = $amr_limits['agenda'];
	else 
		$agenda = 1;
	if (!empty ($amr_limits['eventmap'])) // not avail yet, but one day map of events with locations
		$eventmap = $amr_limits['eventmap'];
	else 
		$eventmap = false;  // if not explicitly asked for a map, then do not do it
	if (!empty($amr_limits['calendar']))   // did the shortcode tell us which list type to use as agenda
		$calendar = $amr_limits['calendar'];
	else {		
		$calendar = 9;
	}

	if ($agenda) {
		$agendaviewlink = remove_query_arg('months',$link );
		$agendaviewlink = add_query_arg(array('agenda'=>$agenda),$agendaviewlink );
		$agendaviewlink = '<a class="agendalink button" href="'
		. htmlentities($agendaviewlink)
		. '" title="' . __('Go to agenda or list view', 'amr-ical-events-list'). '">'.__('Agenda', 'amr-ical-events-list').'</a>';
	}
	else 
		$agendaviewlink = '';
	//
	if ($calendar) {
		$calendarviewlink = ' <a class="calendarlink" href="'
		. htmlentities(add_query_arg(array('calendar'=>$calendar),$link ))
		. '" title="' . __('Go to calendar view', 'amr-ical-events-list'). '">'.__('Calendar', 'amr-ical-events-list').'</a>';
	}
	else $calendarviewlink  = '';
	//
	if ($eventmap) {
		$mapviewlink = ' <a class="maplink" href="'
		. htmlentities(add_query_arg('view','map',$link ))
		. '" title="' . __('Go to map view', 'amr-ical-events-list'). '">'.__('Map', 'amr-ical-events-list').'</a>';
	}
	else $mapviewlink = '';
	$htmlviews = $agendaviewlink.$calendarviewlink.$mapviewlink;
	if (!empty ($htmlviews ) ) // this is so we do not return an empty div
		return ('<div id="calendar_views">'.$htmlviews.'</div>');
	else
		return ('');
}
}
// ----------------------------------------------------------------------------------------
if (!function_exists('amr_month_year_navigation')) {
function amr_month_year_navigation ($start) { //note get is faster than post
global $amr_listtype;
	$link = remove_query_arg('start',get_permalink());
	$link = add_query_arg('listtype',$amr_listtype, $link);
	return ('<form method="POST" action="'.htmlspecialchars($link).'">'
			.amr_monthyeardrop_down($start->format('Ymd'))
			.'<input title="'.__('Go to date', 'amr-ical-events-list').'" type="submit" value="'
			._x('&raquo;&raquo;','Submit button for month and year navigation.  Use translation to replace with words if you want.','amr-ical-events-list').'" >'
			.'</form>');
}
}
// ----------------------------------------------------------------------------------------
if (!function_exists('amr_week_links')) {
function amr_week_links ($start,$weeks) { // returns array ($nextlink, $prevlink,

	global $wpdb, $wp_locale;

	// Get the next and previous month and year
	$prev = new Datetime(); //if cloning dont need tz
	$prev = clone $start;
	date_modify($prev, '-'.($weeks*7).' days');   //may need later on if we are going to show multiple boxes on one page
	$prevstring = $prev->format('Ymd');
	$prevstring2 = amr_date_i18n('jS F',$prev);
	$next     = new Datetime(); //if cloning dont need tz
	$next     = clone $start;
	date_modify($next, '+'.($weeks*7).' days');
	$nextstring = $next->format('Ymd');
	$nextstring2 =amr_date_i18n('jS F',$next);

	//---------------------------  get navigation links ---------------------------------------

	$link = amr_clean_link();

	$prevlink =
		'<a rel="prev" class="prevweek" href="'
		. htmlentities(add_query_arg('start',$prevstring,$link)) . '" title="'
		. sprintf(__('Week starting %1$s', 'amr-ical-events-list'), $prevstring2)
		. '">'._x('&larr;','for prev navigation, translate allows you to use words', 'amr-ical-events-list').'</a>';

	$nextlink = '<a rel="next" class="nextweek" href="'
		. htmlentities(add_query_arg('start',$nextstring,$link))
		. '" title="'
		. sprintf(__('Week starting %1$s', 'amr-ical-events-list'), $nextstring2)
		. '">'._x('&rarr;','for next navigation, translate allows you to use words', 'amr-ical-events-list').'</a>';

	return (array('prevlink'=>$prevlink,'nextlink'=>$nextlink));
}
}
// ----------------------------------------------------------------------------------------
if (!function_exists('amr_month_year_links')) {
function amr_month_year_links ($start,$months) { // returns array ($nextlink, $prevlink, $dropdown

	global $wpdb, $wp_locale;

	// Get the next and previous month and year
	$previous = new Datetime(); //if cloning dont need tz
	$previous = clone $start;
	date_modify($previous, '-1 month');   //may need later on if we are going to show multiple boxes on one page
	$prevmonth = $previous->format('m');
	$prevyear = $previous->format('Y');
	$next     = new Datetime(); //if cloning dont need tz
	$next     = clone $start;
	date_modify($next, '+'.$months.' month');
	$nextmonth  = $next->format('m');
	$nextyear 	= $next->format('Y');

	//---------------------------  get navigation links ---------------------------------------

	$link = amr_clean_link();
	if (!empty ($_REQUEST['agenda']) )
		$link = add_query_arg('agenda',$_REQUEST['agenda'], $link);
	elseif (!empty ($_REQUEST['calendar']) )
		$link = add_query_arg('calendar',$_REQUEST['calendar'], $link);

	if ( $previous ) { $prevlink =
		'<a rel="prev" class="prevmonth" href="'
		. htmlentities(amrical_get_month_link($previous->format('Ymd'), $months, $link)) . '" title="'
		. sprintf(__('Go to %1$s %2$s', 'amr-ical-events-list'), $wp_locale->get_month($prevmonth), $prevyear) . '">&laquo;'
		. $wp_locale->get_month_abbrev($wp_locale->get_month($prevmonth)) . '</a>';
	}
	else $prevlink = '';
	if ( $next ) {
		$nextlink = '<a rel="next" class="nextmonth" href="'
		. htmlentities(amrical_get_month_link($next->format('Ymd'), $months, $link))
		. '" title="' . esc_attr( sprintf(__('Go to %1$s %2$s', 'amr-ical-events-list'), $wp_locale->get_month($nextmonth), $nextyear))
		. '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($nextmonth)) . '&raquo;</a>';
	}
	else $nextlink = '';
	return (array('prevlink'=>$prevlink,'nextlink'=>$nextlink));
}
}
// ----------------------------------------------------------------------------------------
if (!function_exists('amr_monthyeardrop_down')) {
function amr_monthyeardrop_down($current_start) {
global $wp_locale;

//	$m = isset($_GET['m']) ? (int)$_GET['m'] : 0;  // actually yyyymm
	$startobj = amr_newDateTime();
	$ym = $startobj->format('Ym');
	$y  = $startobj->format('Y');
	$m  = $startobj->format('m');
	$startobj->setDate($y,$m,'01');
//	$ym = (int) substr($start, 0, 6);
//	$m  = (int) substr($start, 4, 2);
	$html = '';
	$options=array();
//	date_modify($startobj, '-1 months');  // v4.0.19
	for ($i=1; $i<=18; $i=$i+1) {
		$startstring = $startobj->format('Ymd');
		$m = (int) substr($startstring, 4, 2);
		$y = (int) substr($startstring, 0, 4);
		$options[$startstring] = $wp_locale->get_month($m).' '.$y;
		date_modify($startobj,'+1 month');
	}
	$html .= amr_simpledropdown('start', $options, $current_start);
	return($html);
}
}
// ----------------------------------------------------------------------------------------
if (!function_exists('amr_calendar_navigation')) {
function amr_calendar_navigation($start, $months, $weeks, $liststyle, $views='') {

	if ($liststyle === 'weekscalendar')
		$month_nav_html = amr_week_links ($start, $weeks); // returns array ($nextlink, $prevlink, $dropdown
	else
		$month_nav_html = amr_month_year_links ($start, $months); // returns array ($nextlink, $prevlink, $dropdown
	$prevlink = $month_nav_html['prevlink'];
	$nextlink = $month_nav_html['nextlink'];
	//
	if (($liststyle === 'weekscalendar') OR
	(($months < 2) and ($liststyle == "smallcalendar"))) {
		$navigation = $prevlink.'&nbsp;&nbsp;'.$nextlink;
	}
	else {
		$navigation =
		amr_month_year_navigation ($start)
		.$prevlink.'&nbsp;'
		.$nextlink
		;
	}
	return ($navigation);
		//------------------------end navigation-----------
}
}
/* --------------------------------------------------  */
if (!function_exists('amr_weeks_caption')) {
	function amr_weeks_caption($start) {
	// do not just want to use day format here, as may be too concise and week format cannot handle the start date, and there is no universal consistency on the week number logic
	// later may offer an option we can use, but for now people can write a function
	// should we use this for weeks grouping too ?   maybe
		$caption_format = 'l jS F';
		$calendar_caption = sprintf(
			__('Week starting %s','amr-ical-events-list'),
			amr_date_i18n ($caption_format, $start));
		return($calendar_caption);
	}
}
/* --------------------------------------------------  */
if (!function_exists('amr_semi_paginate')) {
function amr_semi_paginate() {
 	global $amr_limits;
	global $amrW;
	
	if ($amrW) return ('');

	$next = new Datetime(); //if cloning dont need tz 
	$next = clone $amr_limits['end'];
	$next->modify('+1 second');
	$nextd = $next->format("Ymd");
	$gobackd = $amr_limits['start']->format("Ymd");
	$next = htmlentities (add_query_arg (array ('start'=>$nextd, 'startoffset'=>0 )));
	$explaint = '';
	if ((!empty ($amr_limits['hours'])) and (!($amr_limits['hours'] == '0'))) {
		$goback = htmlentities (add_query_arg (array ('start'=>$gobackd, 'hoursoffset'=> -$amr_limits['hours'])));
		$goback = remove_query_arg('startoffset',$goback);
		$goback = remove_query_arg('monthsoffset',$goback);
		$showmore = htmlentities (add_query_arg (array(

				'hours' => $amr_limits['hours']*2
				)));
		$showless = htmlentities (add_query_arg (array(

				'hours' => round($amr_limits['hours']/2)
				)));
		$showmuchmore = htmlentities (add_query_arg (array(

				'hours' => $amr_limits['hours']*20
				)));
		$showmuchless = htmlentities (add_query_arg (array(
				'hours' => max (1, round($amr_limits['hours']/20))
				)));
		$explaint =  ' '.__('hours','amr-ical-events-list');
		}
	else
	if (isset ($amr_limits['months'])) {
		$goback = htmlentities (add_query_arg (array ('start'=>$gobackd, 'monthsoffset'=> -$amr_limits['months'])));
		$showmore = htmlentities (add_query_arg (array(

				'months' => $amr_limits['months']*2
				)));
		$showless = htmlentities (add_query_arg (array(

				'months' => round($amr_limits['months']/2)
				)));
		$showmuchmore = htmlentities (add_query_arg (array(

				'months' => $amr_limits['months']*4
				)));
		$showmuchless = htmlentities (add_query_arg (array(
				'months' => round($amr_limits['months']/4)
				)));
		$explaint =  ' '.__('months','amr-ical-events-list');
		}
	else if (isset ($amr_limits['days'])) {
		$goback = htmlentities (add_query_arg (array ('start'=>$gobackd, 'startoffset'=> -$amr_limits['days'])));
		$showmore = htmlentities (add_query_arg (array(
				'days' => $amr_limits['days']*2
				)));
		$showless = htmlentities (add_query_arg (array(
				'days' => round($amr_limits['days']/2)
				)));
		$showmuchmore = htmlentities (add_query_arg (array(
				'days' => $amr_limits['days']*10
				)));
		$showmuchless = htmlentities (add_query_arg (array(
				'days' => max (1,round($amr_limits['days']/10))
				)));
		$explaint =  ' '.__('days','amr-ical-events-list');
	}

	$show10   = htmlentities (add_query_arg (array('events'=> 10)));
	$show50   = htmlentities (add_query_arg (array('events'=> 50)));
	$show100  = htmlentities (add_query_arg (array('events'=> 100)));
//	$explaint = ' - '.amr_explain_limits();

	$prevt    = __('show past events'  ,'amr-ical-events-list');
	$lesst    = __('show less' ,'amr-ical-events-list').$explaint;
	$moret    = __('show more' ,'amr-ical-events-list').$explaint;
	$muchlesst    = __('show much less' ,'amr-ical-events-list').$explaint;
	$muchmoret    = __('show much more' ,'amr-ical-events-list').$explaint;
//	$lesstt   = __('show less' ,'amr-ical-events-list').$explaint;
//	$morett   = __('show more' ,'amr-ical-events-list').$explaint;
	$nextt    = __('show future events'   ,'amr-ical-events-list');
	$eventnum10t= __('show maximum 10 events if available' ,'amr-ical-events-list').$explaint;
	$eventnum50t= __('show maximum 50 events if available' ,'amr-ical-events-list').$explaint;
	$eventnum100t= __('show maximum 100 events if available' ,'amr-ical-events-list').$explaint;
	return (
		'<div id="icalnavs" class="icalnav" >'
		.'<a rel="next" id="icalback" class="icalnav symbol" title="'.$prevt
		.'" href="'.$goback.'">&larr;</a>&nbsp;'
		.'<a id="icalmuchless" class="icalnav symbol" title="'.$muchlesst
		.'" href="'.$showmuchless.'">&minus;&minus;</a>&nbsp;'
		.'<a id="icalless" class="icalnav symbol" title="'.$lesst
		.'" href="'.$showless.'"> 	&minus;</a>&nbsp;&nbsp;'
		.'<a class="icalnav" title="'.$eventnum10t.'" href="'.$show10.'">10</a> '
		.'<a class="icalnav" title="'.$eventnum50t.'" href="'.$show50.'">50</a> '
		.'<a class="icalnav" title="'.$eventnum100t.'" href="'.$show100.'">100</a> '
		.'<a id="icalmore"  class="icalnav symbol" title="'.$moret
		.'" href="'.$showmore.'">+</a>&nbsp;'
		.'<a id="icalmuchmore"  class="icalnav symbol" title="'.$muchmoret
		.'" href="'.$showmuchmore.'">++</a>&nbsp;'
		.'<a rel="next" id="icalnext"  class="icalnav symbol" title="'.$nextt
		.'" href="'.$next.'">&rarr;</a>'
//		.'<br /><span id="explain" style="font-style: italic; font-size: small; ">'.amr_explain_limits().'</span>'
		.'</div>'
		);
	}
}
/* -------------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_CID'))  {
	function amr_format_CID ($cid, $event) {
		return ($cid);
	}
}
/* -------------------------------------------------------------------------*/
if (!function_exists('amr_mimic_taxonomies')) { // only called if we have an ics file
	function amr_mimic_taxonomies ($ical) {  // check if there is anything in the query url and only accept matches

		if (isset($ical['VEVENT']) and (isset($_REQUEST['category']))) 	{
			$catname = $_REQUEST['category'];

			foreach ($ical['VEVENT'] as $i => $e) {
				$found= false;
				if (!empty($e['CATEGORIES'])) {

					foreach ($e['CATEGORIES'] as $j => $c) {
						if (is_array($c)) {

							foreach ($c as $k => $c2) {
								if (($c2 == $catname )) $found= true;
							}
						}
						else if ($c == $catname ) $found= true;
					}
				}
				if (!$found) unset($ical['VEVENT'][$i]);
			}

		}
		return($ical);
	}

	//	var_dump($ical);
		//foreach ($ical )

}
/* --------------------------------------------------------- */
if (!function_exists('amr_format_attendees') ) {
	function amr_format_attendees ($attendees) {/* receive array of hopefully attendess[] CN and MAILTO, and possibly other */
	
	amr_sort_by_two_cols ('PARTSTAT', 'CN', $attendees); // sort by participaton status, may include all
	
	$text = '';

	if (is_array($attendees))
		foreach ($attendees as $i => $attendee) {
			$list[] = amr_format_attendee ($attendee);
		}

	if (!empty($list)) {
		$text = implode (', ',$list);
	}
	return($text);
	}
}
/* --------------------------------------------------------- */
if (!function_exists('amr_format_PARTSTAT') ) {
	function amr_format_PARTSTAT ($participation_status, $attendee) {
		$text = '';
		switch ($participation_status) {
			case 'ACCEPTED': $text = $text. ' &#10004;'; break;
			case 'DECLINED': $text = $text. ' &#10008;'; break;
			case 'TENTATIVE': $text = $text. ' ?'; break;
			case 'DELEGATED': $text = $text. ' &rarr;'; break;
			case 'NEEDS-ACTION': $text = $text. ' &quest;'; break;
			}
		return ($text);	
	}
}
/* --------------------------------------------------------- */
if (!function_exists('amr_format_attendee') ) {
	function amr_format_attendee ($attendee) {  // do not show emails for privacy reasons
// array could have 
//CUTYPE=INDIVIDUAL
//ROLE=REQ-PARTICIPANT
//PARTSTAT=ACCEPTED or PARTSTAT=NEEDS-ACTION  Participation status
// "ACCEPTED" ,"DECLINED","TENTATIVE", "DELEGATED
//X-NUM-GUESTS=0
//CN=Common name
//DELEGATED-FROM="mailto:bob@example.com
//SENT-BY=mailto:jan_doe@example.com

	if (!empty ($attendee['CN'])) {
		if (!empty  ($attendee['LINK']))  // internal representation , not spec
		$text = '<a href="'.$attendee['LINK'].'" >'.$attendee['CN'].'</a>';
		else $text = $attendee['CN'];
	}
	else { // we  have no name
		if (is_array($attendee)) 
			$text= implode(', ', $attendee);
		else 
			$text = $attendee;
	}
	if (!empty($attendee['PARTSTAT'])) {
		$text .= amr_format_PARTSTAT ($attendee['PARTSTAT'], $attendee);
	}

	return ($text);
	}
}
/* -------------------------------------------------------------------------------------------*/
if (!function_exists ('amr_ical_showmap')) {
	function amr_ical_showmap ($text) { /* the address text */
	global $amr_options;
		$t1 = __('Show in Google map','amr-ical-events-list');
		if (isset ($amr_options['no_images']) and $amr_options['no_images']) $t3 = $t1;
		else $t3 = '<img src="'.IMAGES_LOCATION.MAPIMAGE.'" alt="'.	$t1	.'" class="amr-bling" />';
	/* this is used to determine what should be done if a map is desired - a link to google behind the text ? or some thing else  */

	return('<a class="hrefmap" href="http://maps.google.com/maps?q='
		.str_replace(' ','%20',($text)).'" target="_BLANK"'   //google wants unencoded
		.' title="'.__('Show location in Google Maps','amr-ical-events-list').'" >'.$t3.'</a>');
	}
}
/* --------------------------------------------------------- */
/*if (!function_exists('amr_format_allday')) {
	function amr_format_allday ($content) {
			if ($content == 'allday') return (__('all day', 'amr-ical-events-list'));
		else return ('');
	}
}
/* -------------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_attach'))  {
	function amr_format_attach ($item, $event) {  // receive 1 attachment each being an array of type, url, binary (opt)

		if (empty ($item)) return ;
		$hrefhtml = '';
//	---- handle binary
			if (!empty($item['binary'])) {
				$name = str_replace($event['UID'], ' ','').'-image';
				$src = amr_format_binary ($name,$item['binary']);
				if (!empty($src))
					$hrefhtml .= '<img class="ics_attachment" src="'.$src.'" />';
			}
//	---- handle CID
			else if (!empty($item['CID']) ) {
				$hrefhtml .= amr_format_CID ($item['CID'], $event);
			}
//	---- handle url, prepare title
			if (empty($item['title'])) {  // make a title somehow
				if ( !empty($item['url']))   // this will only be  there if it is an internal event
					$item_title = esc_url($item['url']);
				else
					$item_title = '&nbsp;';
			}
			else $item_title = $item['title'];
//	---- handle mime types or url
			if (!empty($item['type']) ) {  // the fmttype or mime type

				$typeparts = explode ('/',$item['type'] );
// check for  title, type = audio/... video/.. etc
				if ($typeparts[0] === 'image') {  // only include if NOT already in the content

					/* if (false and !empty($event['DESCRIPTION'])) {  // desc is an array
						//var_dump($event['DESCRIPTION']);
						if (stristr($event['DESCRIPTION'], $item['url']))
						return;  // ignore it if already in content
						if ((!empty($item['thumb'])) and
							(stristr($event['DESCRIPTION'], $item['thumb']))) 
						return;
					}
					else */
					if (!empty($item['thumb'])) {  // only do thumb?
						$item_title = '<img alt="'.$item_title
						.'" src="'.esc_url($item['thumb']).'" />';
					}
					else $item_title = '<img alt="'.$item_title
						.'" src="'.esc_url($item['url']).'" />';
				}
				else if ($typeparts[0] === 'text') { }
// do not do others for now - may not make sense to do , other than text

			}

			if (!empty($item['url'])) {

				$tmp = apply_filters('amr_attachment_title', 
					array('title'=>$item_title , 'event'=>$event));
				$item_title = $tmp['title'];

				$hrefhtml .= '<a class="ics_attachment" href="'
				.$item['url']
				.'" title="'
				.__('Event attachment','amr-ical-events-list').'" >'
				.$item_title.'</a>';
			}
			else $hrefhtml .= $item_title;

		if (!empty($hrefhtml)) return ($hrefhtml);
	}
}
/* -------------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_url'))  {
	function amr_format_url ($url) {
	// to be used to format the ics file event url - assumed to be valid
		$text = str_replace('http://','', $url);
	// if it is an external url, then open in new window
		if (amr_external_url($url)) {
			$url = '<a href="'.$url.'" >'.$text.'</a>';
		}
		else
			$url = '<a rel="external" target="_blank" href="'.$url.'" >'.$text.'</a>';
		return($url);
	}
}
/* -------------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_binary'))  {
	function amr_format_binary ($name,$binary) {
// getting error - data not in recognised format in the binary, so skip for now till someone wants it
		return null;
				$im = imagecreatefromstring($binary);
				$filename = ICAL_EVENTS_CACHE_LOCATION.'/ical-events-cache/'.$name.'.jpg';
				// Save the image as 'simpletext.jpg'
				imagejpeg($im, $filename);
				$uploads = wp_upload_dir();
				$url = $uploads[base_url].'/ical-events-cache/'.$name;
				return($url);
	}
}
/* --------------------------------------------------------- */
if (!function_exists('amr_format_allday') ) {
	function amr_format_allday ($content) {
		if ($content == 'allday')
			return (_x('all&nbsp;day', 'when an event runs for full days, note the &nbsp; prevents the text wrappping in a table.','amr-ical-events-list'));
		else return ('');
	}
}
/* ------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_taxonomy_link') ) {  //problem ics file categories are string not array ? so skip?
	function amr_format_taxonomy_link ($tax_name, $tax_term, $link='') {  // will receive id
	// if in widget should link to calendar page
	// if in agenda or calendar - same ?
	// if in event info - either calendar page or archive
	global $amr_calendar_url, $amr_taxonomy_url; 

	if (empty($amr_taxonomy_url))  {// not yet set
		if (empty($amr_calendar_url)) {
			$amr_taxonomy_url = amr_clean_link();  // not good - basically populates the global when it possible should not
			}
		else {
			$amr_taxonomy_url = $amr_calendar_url;
		}
	}
	
	if (empty($link) and (!empty($amr_taxonomy_url)) ) {
		$link = $amr_taxonomy_url;	
	}	
	$term = get_term($tax_term, $tax_name, OBJECT);
	
	if (!isset($term->name)) {  // if it is not a wordpress taxonomy ?
		$name = $tax_term;
		$link2 = add_query_arg ('category', $tax_term, $link);
		$title = sprintf(__('View events in %s %s','amr-ical-events-list'),
			__('category'), $tax_term);
		
	}
	else {
		$name = $term->name;
		$link2 = add_query_arg ($tax_name, $term->slug, $link);
		$title = sprintf(__('View events in %s %s','amr-ical-events-list'),
				__($tax_name,'amr-ical-events-list'), $term->name);
	}
	$html = '<a href="'.htmlspecialchars($link2)
		.'" title="'.$title
		.'">'.$name.'</a>';
	return ($html);
	}
}
/* ------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_taxonomies') ) {  //problem ics file categories are string not array ? so skip?
	function amr_format_taxonomies ($tax_name, $tax_array, $link='' ) {

	if (!is_array($tax_array) and (stristr($tax_array,',')))  // if it is a string like in the icsfile, convert to an array
		$tax_array = explode(',',$tax_array);

	foreach ($tax_array as $i => $t) {
		if (is_array($t)) {
			foreach ($t as $i2 => $t2) {
				$links[] = amr_format_taxonomy_link ($tax_name, $t2);
			}
		}
		else
			$links[] = amr_format_taxonomy_link ($tax_name, $t);
		}
	$html = implode(',',$links);
	return( $html);
	}
}
/* --------------------------------------------------  */
if (!function_exists('amr_derive_calprop_further')) {
	function amr_derive_calprop_further (&$p) {
		global $amr_options;
		if (isset ($p['totalevents'])) 
			$title = __('Total events: ', 'amr-ical-events-list').$p['totalevents'];	/* in case we have noename? ***/
		if (isset ($p['X-WR-CALDESC'])) {
			$p['X-WR-CALDESC'] = nl2br2 ($p['X-WR-CALDESC']);
			$desc = __($p['X-WR-CALDESC'],'amr-ical-events-list');  //allow translation of whatever value is in the ics file

		}
		else $desc = __('No description available','amr-ical-events-list');

		if (isset ($p['X-WR-CALNAME'])) {
			$p['X-WR-CALNAME'] = __($p['X-WR-CALNAME'],'amr-ical-events-list');
		}
		if (isset ($p['icsurl']))  {/* must be!! */
			$p['addtogoogle'] = add_cal_to_google ($p['icsurl']);
			if (isset ($p['X-WR-CALNAME'])) {
					$p['subscribe'] = sprintf(__('Subscribe to %s Calendar','amr-ical-events-list'),
					htmlentities ($p['X-WR-CALNAME']));
					$p['X-WR-CALNAME'] = '<a class="x-wr-calname" '
					.' title="'.$p['subscribe'].'"'
					.' href="'.html_entity_decode($p['icsurl']).'">'
					.htmlspecialchars($p['X-WR-CALNAME'])
					.'</a><!-- '.$desc.' -->';
			}
			else { // if we do not have a name, use the basename of the url
					$f = basename($p['icsurl'], ".ics");
					$p['subscribe'] = sprintf(__('Subscribe to %s Calendar','amr-ical-events-list'), $f);
					$p['X-WR-CALNAME'] = '<a '
					.' title="'.$p['subscribe'].'"'
					.' href="'.html_entity_decode($p['icsurl']).'">'
					.$f
					.'</a>';
			}
			$t = __('Subscribe to calendar in your calendar application.', 'amr-ical-events-list');
			if (isset ($amr_options['no_images']) and $amr_options['no_images'])
				$t3 = $t = __('Subscribe to calendar', 'amr-ical-events-list');
			else
				$t3 = '<img class="subscribe amr-bling" src="'.IMAGES_LOCATION.CALENDARIMAGE.'" title= "'.$t.'" alt="'.$t.'" />';

			$p['icsurl'] =
				'<a class="amr-bling icalsubscribe" title="'.$p['subscribe']
				.'" href="'.html_entity_decode($p['icsurl']).'">'
				.$t3.'</a>';
		}
		
		$p['icalrefresh'] = amr_show_refresh_option();
		return ($p);
	}
}
/* --------------------------------------------------------- */
if (!function_exists('amr_derive_summary')) {
	function amr_derive_summary (&$e ) {
		global $amr_options;
		global $amr_listtype;
		global $amrW;
		//global $amrwidget_options;
		global $amr_calendar_url;
		global $amr_liststyle;
	/* If there is a event url, use that as href, else use icsurl, use description as title */


		if (isset($e['SUMMARY'])) 
			$e['SUMMARY'] = (amr_just_flatten_array ($e['SUMMARY'] ));
		else 
			return ('');
			
		if (!empty($e['excerpt'])) {
				$e['excerpt'] = (amr_just_flatten_array ($e['excerpt'] ));
		}
		
		if (isset($e['type']) and ($e['type'] == 'VFREEBUSY') ) {
			$e['SUMMARY'] = __($e['SUMMARY'],'amr-ical-events-list');  // if busy - will be the translation of it
			$e['excerpt'] = $e['SUMMARY'];  // the translated summary
			$e['DESCRIPTION'] = $e['excerpt'];	
			if (!empty($amr_options['freebusymessage']))  // might be a red X, don't want the description and excerpt to be that 
				$e['SUMMARY'] = __($amr_options['freebusymessage'],'amr-ical-events-list');
		}	
	
		if (in_array($amr_liststyle, array('smallcalendar', 'largecalendar','weekscalendar')))
			$hoverdesc = false;
		else {
			if (empty($amrW))
				$hoverdesc = false;
			else if ($amrW == 'w_no_url')
				$hoverdesc = false;
			else $hoverdesc ='maybe';
		}


		if (isset($e['URL'])) 
			$e_url = amr_just_flatten_array($e['URL']);
		else $e_url = '';
		/* If not a widget, not listype 4, then if no url, do not need or want a link */
		/* Correction - we want a link to the bookmark anchor on the calendar page***/
		if (empty($e_url))  {
			if (!($amrW == 'w_no_url'))  {
//				if (!empty($amrwidget_options['moreurl'])) {
				if (!empty($amr_calendar_url)) {
					$e_url = ' href="'.($amr_calendar_url)
	//				.'#'.$e['Bookmark']
					.'" ';
				}
				else {
					if (!empty($amr_options['listtypes'][$amr_listtype]['general']['Default Event URL'])) {
						$e_url = ' class="url" href="'
							.clean_url($amr_options['listtypes'][$amr_listtype]['general']['Default Event URL']).'" ';
						}
					else $e_url = ''; /*empty anchor as defined by w3.org */
					/* not a widget */
				}
			}
			else {return ($e['SUMMARY']);	}
		}
		else {
			$e_url = ' class="url" href="'.esc_url($e_url).'" ' ;
		}
		$e_desc =  '';
		if ($hoverdesc) {
			if (isset ($e['DESCRIPTION'])) {
				$e_desc = amr_just_flatten_array($e['DESCRIPTION']);
				}
		    if (!empty($e_desc)) {
				$e_desc = 'title="'.htmlspecialchars(str_replace( '\n', '  ', (strip_tags($e_desc)))).'"';
			}
		}
		else {
			if (!empty ($e['excerpt'])) {
				$e_desc = strip_tags($e['excerpt']);
				$e_desc = ' title="'.$e_desc.'" ';
				}
			else
				$e_desc = ' title="'.$e['SUMMARY'].' - '.__('More information', 'amr-ical-events-list').'" ';
			}
		if (!empty ($e_url)) {
			if (amr_external_url($e_url)) 
				$e_url .= ' target="_blank" '; 
			$e_summ = '<a '.$e_url.$e_desc.'>'. $e['SUMMARY'].'</a>';
		}
		else $e_summ = $e['SUMMARY'];
		return( $e_summ );
	}
}
/*--------------------------------------------------------------------------------*/
if (!function_exists('add_cal_to_google')) {
	function add_cal_to_google($cal) {
	global $amr_options;
	/* adds a button to add the current calemdar link to the users google calendar */
		$text1 = __('Add to google calendar', 'amr-ical-events-list');
		if (isset ($amr_options['no_images'])  and $amr_options['no_images'])
			$text2 = __('Add to google', 'amr-ical-events-list');
		else
			$text2 = '<img src="'.IMAGES_LOCATION.ADDTOGOOGLEIMAGE.'" title="'.$text1.'" alt="'.$text1.'" class="amr-bling" />';
		return (
		'<a class= "amr-bling addtogoogle" href="http://www.google.com/calendar/render?cid='.html_entity_decode($cal).'" target="_blank"  title="'.$text1.'">'.$text2.'</a>');
	}
}
/*--------------------------------------------------------------------------------*/
if (!function_exists('add_event_to_google')) {
	function add_event_to_google($e) {
	global $amr_options;

		if (!isset($e['EventDate'])) return('');
		if (isset($e['LOCATION'])) $l = 	'&amp;location='.esc_html(strip_tags(str_replace(' ','%20',($e['LOCATION'] ))));
		else $l = '';
		if (!isset($e['DESCRIPTION'])) $e['DESCRIPTION'] = '';
		$t = __("Add event to google" , 'amr-ical-events-list');

		if (isset ($amr_options['no_images']) and $amr_options['no_images']) $t2 = $t;
		else $t2 = '<img src="'.IMAGES_LOCATION.ADDTOGOOGLEIMAGE.'" alt="'.$t.'" class="amr-bling"/>';
		$details = amr_just_flatten_array ($e['DESCRIPTION']); //var_dump($details);
		if (!empty($details)) $details ='&amp;details='.rawurlencode(strip_tags($details));

	/* adds a button to add the current calendar link to the users google calendar */
		$html = '<a class= "amr-bling hrefaddtogoogle" href="http://www.google.com/calendar/event?action=TEMPLATE'
		.'&amp;text='.str_replace(' ','%20',esc_html(strip_tags(amr_just_flatten_array ($e['SUMMARY']))))
		/* dates and times need to be in UTC */
		.'&amp;dates='.amr_get_googleeventdate($e)
		.$l
		.'&amp;trp=false'
		.$details
		.'" target="_blank" title="'.$t.'" >'.$t2.'</a>';
		return ($html);/* Note google only allows simple html*/
	}
}
/* --------------------------------------------------  */
if (!function_exists('amr_show_refresh_option')) {
	function amr_show_refresh_option() {
	global $amr_globaltz, $amr_lastcache, $amr_options, $amr_last_modified;
		$uri = add_query_arg(array('nocache'=>'true'), $_SERVER['REQUEST_URI']);
		if (!is_object($amr_lastcache)) $text = __('Last Refresh time unexpectedly not available','amr-ical-events-list');
		else {
			date_timezone_set($amr_lastcache, $amr_globaltz);
			$t = $amr_lastcache->format(get_option('time_format').' T');
			$text = __('Refresh calendars','amr-ical-events-list');
			$text2 = sprintf(__('Last refresh was at %s. ','amr-ical-events-list'),$t);
			}
		if (!is_object($amr_last_modified)) $text2 =  __('Remote file had no modifications. ','amr-ical-events-list');
		else {
			date_timezone_set($amr_last_modified, $amr_globaltz);
			$t2 = $amr_last_modified->format(get_option('date_format').' '.get_option('time_format').' T.');
			$text2 = sprintf(__('The remote file was last modified on %s.','amr-ical-events-list'),$t2);
			}

		if (isset ($amr_options['no_images']) and $amr_options['no_images']) $t3 = $text;
		else $t3 = '<img src="'.IMAGES_LOCATION.REFRESHIMAGE
			.'" class="amr-bling" title="'.__('Click to refresh','amr-ical-events-list').' '.$text2.'" alt="'.$text.'" />';
		return ( '<a class="refresh amr-bling" href="'.htmlentities($uri).'" title="'.$text.' '.$text2.'">'.$t3.'</a>');
	}
}
/* --------------------------------------------------  */
if (!function_exists('amr_list_properties')) {
	function amr_list_properties($icals, $tid, $class) {  /* List the calendar properties if requested in options  */
	global $amr_options,
		$amr_liststyle,
		$amr_listtype;
/* --- setup the html tags ---------------------------------------------- */

	//if (ICAL_EVENTS_DEBUG) var_dump($icals);

	if ($amr_liststyle === 'custom') {  // get the stored file uirl, if it does not exist, set to table
		$custom_htmlstyle_file = amr_get_htmlstylefile();
		if (empty ($custom_htmlstyle_file ) )
			$amr_liststyle = 'table';
	}

	switch ($amr_liststyle) {
	case 'list' :
		$d ='<span ';
		$dc='</span>';
		$r   = '<div>';
		$rc  = '</div>';
		$htm['box'] = '<div';
		$htm['boxc']= '</div>';
		break;
	case 'breaks' :
		$d ='<span ';
		$dc ='</span>';
		$r   = '<span>';
		$rc  = '</span>';
		$htm['box'] = '<div';
		$htm['boxc']= '</div>';
		break;
	case 'table':
	case 'HTML5table':  // still using a table, so columns will work  but with html5 elements too
		$d 	='<td';
		$dc	='</td>';
		$r   = '<tr> ';
		$rc  = '</tr> ';
		$htm['box'] = '<table';
		$htm['boxc']= '</table>';
		break;
	case 'HTML5':
		$htm['box'] 	= '<section';
		$htm['boxc']	= '</section>';
		$r   	= '<header><h2>';
		$rc  	= '</h2></header> ';
		$d 		=''; 
		$dc 	='';
		break;
	case 'custom':
		$where_am_i = 'in_calendar_properties';
		include ($custom_htmlstyle_file);
		break;
	default:  /* the old way or tableoriginal*/
		$r   = '<tr> ';  $d ='<td';
		$rc  = '</tr> '; $dc='</td>';
		$htm['box'] = '<table';
		$htm['boxc']= '</table>';
	}
	$html = '';

	$columns = prepare_order_and_sequence  ($amr_options['listtypes'][$amr_listtype]['calprop']);
	if (!($columns)) return;
	//if (ICAL_EVENTS_DEBUG) var_dump($columns);

//	if (!($order)) return;
	foreach ($icals as $i => $p)	{ /* go through the options list and list the properties */
		amr_derive_calprop_further ($icals[$i]);

		$rowhtml = '';  //20140806 col 1 of cal properties were being overwritten - fix
		foreach ($columns as $col => $data) {
			$cprop = '';
			foreach ($data as $k => $v) {
				if (!empty ($icals[$i][$k])) {/*only take the fields that are specified in options  */
					$cprop .= amr_format_value($icals[$i][$k], $k,
						$icals[$i], $v['Before'], $v['After'] );
				}
			}
			if (empty($cprop)) $cprop = '&nbsp;';
			if (!empty($d))  // if we have a td type html to bracket the column with
				$cprop = $d.' class="col'.$col.'">'.$cprop.$dc;
			$cprop .= AMR_NL;
			$rowhtml .= $cprop;
		} // end of columns for one calendar

		$html .= $r.$rowhtml.$rc.AMR_NL;
		
	} // end of calendars

	if (!(empty($html)) ) {
			$html  =
			((!empty($htm['box'])) ? $htm['box'].' id="'.$tid.'" class="'.$class.'">' : '')
			.$html
			.((!empty($htm['boxc'])) ? $htm['boxc'] :  '');
		}

	return ($html);
}
}
/* -------------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_grouping') ) {
	function amr_format_grouping ($grouping, $datestamp) {
	/* check what the format for the grouping should be, call functions as necessary*/
	global $amr_options;
	global $amr_listtype;
	global $amr_formats;

		if (empty($grouping)) return '';

		if (in_array ($grouping ,array ('Year', 'Month', 'Day'))) {
			//if (WP_DEBUG) echo '$amr_listtype = '.$amr_listtype.' $grouping='.$grouping;
			return (amr_format_date( $amr_options['listtypes'][$amr_listtype]['format'][$grouping], $datestamp));
		}
		else if ($grouping === 'Week') {
				$f = $amr_formats['Week'];
				$w = amr_format_date( 'W', $datestamp);
				return (sprintf(__('Week  %u', 'amr-ical-events-list'),$w));
			}
		else
		{ 	/* for "Quarter",	"Astronomical Season",	"Traditional Season",	"Western Zodiac",	"Solar Term" */
			$func = str_replace(' ','_',$grouping);
			if (function_exists($func) ) {
				return call_user_func($func,$datestamp);
				}
			else  return ('No function defined for Date Grouping '.$grouping);
		}
	}
}
/* --------------------------------------------------  */
if (!function_exists('amr_get_html_structure') ) {
function amr_get_html_structure($amr_liststyle, $no_cols) {
	if ($amr_liststyle === 'custom') {  // get the stored file uirl, if it does not exist, set to table
		$custom_htmlstyle_file = amr_get_htmlstylefile();
		if (empty ($custom_htmlstyle_file ) )
			$amr_liststyle = 'table';
	}
	switch ($amr_liststyle) {
	case 'list' : // deprecated
		$htm['ul'] 	= ''; $htm['li']= '';
		$htm['ulc']	= ''; $htm['lic']= '';
//		$htm['ul']	= '<span '; 	$htm['li']= '<span ';
//		$htm['ulc']	= '</span>'; 	$htm['lic']= '</span> ';
		$htm['row']	= '<li ';
		$htm['rowc'] 	= '</li>'.AMR_NL;
		$htm['hcell']	= '';
		$htm['cell'] 	= ''; /* no class specification - as html in content can break the span validation */
		$htm['hcellc'] = '';
		$htm['cellc'] 	= '';
		$htm['grow']	= '<li ';
		$htm['growc']  = '</li>'.AMR_NL;
		$htm['ghcell'] = '<span ';
		$htm['ghcellc']= '</span>'.AMR_NL;
		$htm['head'] 	= '<div> '; 		
		$htm['headc'] 	= '</div>'.AMR_NL;
		$htm['body'] 	= '<ul '; // open
		$htm['bodyc'] 	= '</ul>'.AMR_NL;
		$htm['box'] 	= AMR_NL.'<div ';
		$htm['boxc'] 	= '</div>'.AMR_NL;
		break;
	case 'table':
		$htm['ul']		= '<div '; 		
		$htm['li']		= '<div '; // need these if we want details to hover
		$htm['ulc']		= '</div>'; 	
		$htm['lic']		= '</div>';
		$htm['row']		= '<tr '; 		
		$htm['hcell']	='<th '; 	
		$htm['cell'] 	='<td '; /* allow for a class specifictaion */
		$htm['rowc'] 	= '</tr> '; 
		$htm['hcellc'] 	='</th>'; 	
		$htm['cellc'] 	='</td>';
		$htm['grow']	= '<tr ';	
		$htm['ghcell']  = '<th colspan="'.$no_cols.'"'; 
		$htm['ghcellc'] = $htm['hcellc'];
		$htm['growc']  	='</tr>'.AMR_NL;
		$htm['head'] 	= '<thead>';
		$htm['headc'] 	= '</thead>';
		//$foot 	= '<tfoot>';
		//$htm['footc'] 	= '</tfoot>';
		$htm['body'] 	= AMR_NL.'<tbody '; //open
		$htm['bodyc'] 	= AMR_NL.'</tbody>'.AMR_NL;
		$htm['box'] 	= '<table';
		$htm['boxc'] 	= '</table>'.AMR_NL;
		break;
	case 'HTML5table' :
		$htm['ul']		= ''; 
		$htm['ulc']		= ''; 
		$htm['li']		= '<span '; // required for rich snippets, microformat
		$htm['lic']		= '</span>';
		/* allow for a class specifictaion */
		$htm['row']		= PHP_EOL.'<tr ';
		$htm['rowc'] 	= PHP_EOL.'</tr>'.PHP_EOL;
		$htm['hcell']	='<th ';
		$htm['hcellc'] 	='</th>';
		$htm['cell'] 	='<td ';
		$htm['cellc'] 	='</td>';
		$htm['grow']	= '<tr ';
		$htm['ghcell'] 	= '<th colspan="'.$no_cols.'"';
		$htm['ghcellc'] = $htm['hcellc'];
		$htm['growc']  	='</tr>'.PHP_EOL;
		$htm['head'] 	= PHP_EOL.'<thead>';
		$htm['headc'] 	= '</thead>'.PHP_EOL;
		$htm['body'] 	= PHP_EOL.'<tbody '; //open
		$htm['bodyc'] 	= PHP_EOL.'</tbody>'.PHP_EOL;
		$htm['box'] 	= PHP_EOL.'<table';
		$htm['boxc'] 	= '</table>'.PHP_EOL;
		break;
	case 'HTML5' :
		
		$htm['ul']		= ''; 
		$htm['ulc']		= ''; 
		$htm['li']		= '<span '; // required for rich snippets, microformat
		$htm['lic']		= '</span>';
		/* allow for a class specifictaion */
		$htm['row']		= '<article '; 	 // each event
		$htm['rowc'] 	= '</article>'.AMR_NL;
		$htm['hcell']	='<h2 '; 	// the 'column' header cell
		$htm['hcellc'] 	='</h2>';
		$htm['cell'] 	='';
		$htm['cellc'] 	='';
//
		$htm['grow']	= '<header><h3 ';	// the grouping html text for a group of events - not the surrounding selector
		$htm['growc']   = '</h3></header>'.AMR_NL;
		$htm['ghcell']  = '';
		$htm['ghcellc'] = '';
//
		$htm['head'] 	= '<h2 ';
		$htm['headc'] 	= '</h2>';
		$foot 	= '<div ';
		$htm['footc'] 	= '</div>';
//
		$htm['body'] 	= '<section ';	// the grouping html text for a group of events - not the surrounding selector
		$htm['bodyc'] 	= '</section>'.AMR_NL;
//
		$htm['box'] 	= '<section';  // the whole calendar
		$htm['boxc'] 	= '</section>'.AMR_NL;
		break;
	case 'breaks' :
		$htm['ul']		= ''; 
		$htm['ulc']		= ''; 
		$htm['li']		= '<span '; // required for rich snippets, microformat
		$htm['lic']		= '</span>';
		$htm['row']		= '';
		$htm['rowc'] 	= '';
		$htm['hcell']	='<div ';
		$htm['hcellc'] ='</div>&nbsp;';
		$htm['cell'] 	='<div '; /* allow for a class specifictaion */
		$htm['cellc'] 	='</div>';
		$htm['grow']	= '<div ';
		$htm['growc']  ='</div>'.AMR_NL;
		$htm['ghcell'] = $htm['hcell'];
		$htm['ghcellc']= $htm['hcellc'];
		$htm['head'] 	= '<div> ';
		$htm['headc'] 	= '</div>'.AMR_NL;
		$htm['body'] 	= AMR_NL.'<div '; //open
		$htm['bodyc'] 	= '</div>'.AMR_NL;
		$htm['box'] 	= AMR_NL.'<div';
		$htm['boxc'] 	= '</div>'.AMR_NL;
		break;
	case 'custom':
		$where_am_i = 'in_events';
		$htm['ul']		= ''; 
		$htm['ulc']		= ''; 
		$htm['li']		= '<span '; // required for rich snippets, microformat
		$htm['lic']		= '</span>';
		include ($custom_htmlstyle_file);  // can check the $where_am_i
	break;

	default:  /* the old way or tableoriginal*/
		$htm['ul']	= '<ul';	
		$htm['li']= '<li';
		$htm['ulc']	= '</ul>';	
		$htm['lic']= '</li>';
		$htm['row']	= '<tr '; 				
		$htm['hcell']	='<th '; 	
		$htm['cell'] 	='<td '; /* allow for a class specifictaion */
		$htm['rowc'] 	= '</tr> '; 			
		$htm['hcellc'] ='</th>'; 	
		$htm['cellc'] 	='</td>';
		$htm['ghcell'] = '<th colspan="'.$no_cols.'"';
		$htm['grow']	= '<tr ';	        
		$htm['growc']  ='</tr>';
        $htm['ghcellc']= $htm['hcellc'];
		$htm['head'] 	= AMR_NL.'<thead>';
		$htm['body'] 	= AMR_NL.'<tbody ';
		$htm['headc'] 	= AMR_NL.'</thead>';
		$htm['bodyc'] 	= AMR_NL.'</tbody>';
		$htm['box'] 	= AMR_NL.'<table';
		$htm['boxc'] 	= '</table>'.AMR_NL;
	}
	return ($htm);
}
}
/* --------------------------------------------------  */
if (!function_exists('amr_list_events') ) {
function amr_list_events($events,  $tid, $class, $show_views=true) {
	global $wp_locale,
		$locale,
		$amr_options,
		$amr_limits,
		$amr_listtype,
		$amr_liststyle,
		$amr_current_event,
		$amrW,
		$amrtotalevents,
		$amr_globaltz,
		$amr_groupings,
		$change_view_allowed;

		
	if (ICAL_EVENTS_DEBUG) {
		echo '<br />Peak Memory So far :'.amr_memory_convert(memory_get_usage(true));
		echo '<h2>Now Listing, and locale = '.$locale.' and list type = '.$amr_listtype.'</h2>';
		echo '<br />Limits = '; var_dump($amr_limits);
	}

	if (!defined('AMR_NL')) define('AMR_NL','PHP_EOL');
		/* we want to maybe be able to replace the table html for alternate styling - may need to  keep the li items though */
	$amrconstrainedevents = count($events);
	$html = '';

	if (in_array ($amr_liststyle, array('smallcalendar','largecalendar','weekscalendar'))) {
		/* is it a calendar box we want - handle separately */
		$html = amr_events_as_calendar($amr_liststyle, $events, $tid, $class);
		return($html);
	}

	$columns = prepare_order_and_sequence ($amr_options['listtypes'][$amr_listtype]['compprop']);
	if (!$columns) 	return; // no display requested
	else $no_cols = count($columns);

	/* --- setup the html tags ---------------------------------------------- */
	$htm = amr_get_html_structure ($amr_liststyle, $no_cols);

	/* -- show view options or not  ------------------------------------------*/
	if ((isset($amr_limits['show_views']))
	and ($amr_limits['show_views']) and $change_view_allowed) {
		$views = amrical_calendar_views();
	}
	else $views = '';
	/* -- show month year nav options or not  ----------------NOT IN USE - need to lift code out for reuse --------------------------*/

	$start    = amr_newDateTime('now');
	if (!empty($amr_limits['start'])) 
		//$start = amr_newDateTime('now');
	//else	
		$start    = clone $amr_limits['start'];
	$navigation = '';
	if ((isset($amr_limits['show_month_nav']))
	and ($amr_limits['show_month_nav']) ) {
		if (isset ($amr_limits['months']))	$months = $amr_limits['months'];
		else $months = 1;
//		$start    = new Datetime('now',$amr_globaltz);
//		$start    = clone $amr_limits['start'];
		$navigation = amr_calendar_navigation($start, $months, 0, $amr_liststyle); // include month year dropdown	with links
		$navigation = '<div class="calendar_navigation">'.$navigation.'</div>';
	}
	else {
		if ((isset($amr_limits['month_prev_next'])) and $amr_limits['month_prev_next']
		and function_exists('amr_do_month_prev_next_shortcode')) {
			$navigation .= amr_do_month_prev_next_shortcode();
		}
		if ((isset($amr_limits['month_year_dropdown'])) and $amr_limits['month_year_dropdown']
		and function_exists('amr_month_year_navigation')) {
			$navigation .= amr_month_year_navigation($start);
		}

	}

/* -- heading and footers code ------------------------------------------*/

	if (ICAL_EVENTS_DEBUG) {echo '<br />Limit parameters '; var_dump($amr_limits);}
	if (isset($amr_limits['headings'])) 
		$doheadings = $amr_limits['headings'];
	else 
		$doheadings = true;
	if (isset($amr_limits['pagination'])) 
		$dopagination = $amr_limits['pagination'];
	else 
		$dopagination = true;

	$headhtml = '';
	if ($doheadings) {
		$docolheading=false;
		if (ICAL_EVENTS_DEBUG) {echo '<br />Headings? '; var_dump($amr_options['listtypes'][$amr_listtype]['heading']);}
		foreach ($amr_options['listtypes'][$amr_listtype]['heading'] as $i => $h) {
			if (!empty($h)) $docolheading=true;
		}
		if ($docolheading) {
			foreach ($columns as $i => $col) {
				if (isset($amr_options['listtypes'][$amr_listtype]['heading'][$i]))
					$colhead = __($amr_options['listtypes'][$amr_listtype]['heading'][$i],'amr-ical-events-list');
				else
					$colhead = '&nbsp;';
				$headhtml .= amr_do_a_headercell_html($htm, $i, $colhead);
			}
			$html .= amr_do_column_header_html($htm, $i, $headhtml);
		}
	}
/* ***** with thechange in list types, we have to rethink how we do the footers .... for tables we say the footers up front, but for others not. */
		$fhtml = '';
		if ((isset($amr_options['ngiyabonga']) and ($amr_options['ngiyabonga'])))
			$fhtml .= amr_ngiyabonga();
		else
			$fhtml .='<!-- event calendar by anmari.com.  See it at icalevents.com -->';

		if ((isset($amr_limits['show_look_more'])) and ($amr_limits['show_look_more'])) {
				$fhtml .= amr_show_look_more();
		}
		if ((!empty($amr_limits)) and ($amrtotalevents > $amrconstrainedevents) ) {
			if ($dopagination and function_exists('amr_semi_paginate'))
				$fhtml .= amr_semi_paginate();
			if (function_exists('amr_ical_edit'))
				$fhtml .= amr_add_new_event_link();
		}

		$alt = false;
/* -- body code ------------------------------------------*/
/* ----------- check for groupings and compress these to requested groupings only */
	$groupings 		= amr_get_groupings_requested ();
	$groupedevents	= amr_assign_events_to_groupings ($groupings, $events);  // will just return if no grouping
	$html 			.= amr_list_events_in_groupings ($htm, '', $columns, $groupedevents, $events);

	if (!empty ($tid)) {
		$tid = ' id="'.$tid.'" ';
		}
	$html = ((!empty($htm['box'])) ? ($htm['box'].$tid.' class="'.$class.'">') : '')
		.$html
		.$htm['boxc']
		.$fhtml;

	$html =
		$views.AMR_NL
		.$navigation.AMR_NL
		.$html.AMR_NL;

	return ($html);
	}

}
/* --------------------------------------------------  */
if (!function_exists('amr_show_more_prev')) {
// coming later maybe, or will mods to look more be adequate?
// - show 'more' on page 2 onwards
// - on page 2 onwards, show see previous (like a back button?)
// - do not show more on last page - have to check DB for 'last event date' ? may slow things down
	function amr_show_more_prev() {
	 }
}
/* --------------------------------------------------  */
if (!function_exists('amr_show_look_more')) {  // does a google style next
function amr_show_look_more() {
 	global $amr_limits,
	$amr_options,
	$amr_formats,
	$amr_globaltz,
	$amr_last_date_time;

	$next = new datetime('',$amr_globaltz);
	if (!empty($amr_last_date_time)) {
		$next = clone $amr_last_date_time; // get  last used event date
	}
	else {
		$amr_last_date_time = $amr_limits['end'] ;
		$next 				= $amr_limits['end'] ;
	}
	date_time_set($next,0,0,0); // set to the beginning of the day
	$prev = $amr_limits['start'] ;

	$nexturl = add_query_arg ('events', $amr_limits['events']*2);
	$prevurl = remove_query_arg ('events');

	// if no events, then this makes no sense  $explaint = sprintf (__('Displaying %s events.'),$amr_limits['events']) ;
	$explaint = '';
	// due to events limit, it may not show all events in a given day, so do not say displaying until date,
	// rather just start the next display from that last date - may be a few events that overlap.

	foreach ($amr_limits as $i=>$value) {
		if (in_array ($i, array('days','hours','months','weeks'))) {
			$nexturl = add_query_arg ($i, $value, $nexturl);
			$prevurl = add_query_arg ($i, $value, $prevurl);
			date_modify($prev, '-'.$value.' '.$i);  // work back to the previous event
		}
	}
	$nextd = $next->format("Ymd");
	$prevd = $prev->format("Ymd");
	$nexturl = (add_query_arg (array ('start'=>$nextd ), $nexturl));
	$prevurl = (add_query_arg (array ('start'=>$prevd ), $prevurl));
	// NB MUST increase the number of events otherwise one can get caught in a situation where if num of events less than events in a day, one can never get past that day.

	if (empty($amr_options['lookmoremessage']))
		$moret    =  __('Look for more', 'amr-ical-events-list');
	else
	/* translators: ignore, the text appears for translation eslewhere */
		$moret    = __($amr_options['lookmoremessage'],'amr-ical-events-list');

	$morett    = sprintf( __('Look for more from %s' ,'amr-ical-events-list'),$amr_last_date_time->format($amr_formats['Day']));


	if (!empty($_REQUEST['start'])) {
		if (empty($amr_options['lookprevmessage']))
			$prevt    =  '';
		else
		/* translators: ignore, the text appears for translation eslewhere */
			$prevt    = __($amr_options['lookprevmessage'],'amr-ical-events-list');

		if (empty($amr_options['resetmessage']))
			$reset = '';  // allow it to be blanked out
		else
		/* translators: ignore, the text appears for translation eslewhere */
			$reset = __($amr_options['resetmessage'],'amr-ical-events-list');
	}
	else {  // if we on first page, do not show
		$reset = '';
		$prevt = '';
	}

	if (!empty ($reset) ) {
		$reseturl = remove_query_arg(array('start','startoffset','events','days','months','hours','weeks'));
		$reset ='<a id="icalareset"  title="'
		.__('Go back to initial view' ,'amr-ical-events-list')
		.'" href="'.esc_attr($reseturl).'">'.$reset.'</a>';
	}
	if (!empty ($prevt) ) {
		$prevt ='<a rel="prev" id="icalaprev"  title="'
		.__('Go back to previous events' ,'amr-ical-events-list')
		.'" href="'.esc_attr($prevurl).'">'.$prevt.'</a>';
	}
	return (
		'<div id="icallookmore" class="icalnext" >&nbsp;'
		.$prevt.'&nbsp;'
		.$reset.'&nbsp;'
		.'<a rel="next" id="icalalookmore"  title="'.$explaint.' '.$morett.'" href="'.esc_attr($nexturl).'">'.$moret.'</a>'
		.'</div>'
		);
	}
}
/* --------------------------------------------------------- */
if (!function_exists('amr_format_organiser')) {
	function amr_format_organiser ($org) {/* receive array of hopefully CN and MAILTO, and possibly SENTBY */
	//	If (ICAL_EVENTS_DEBUG) {echo '<br />Organiser array:    '; var_dump($org);}
		$text = '';
	//	if (!(is_array($org))) $org = amr_parseOrganiser('ORGANIZER;'.$org);  // may not have been parsed yet (eg in wp events)
	//	var_dump($org);
		if (!empty ($org['CN'])) {
			if (!empty  ($org['MAILTO']))
			$text = '<a href="mailto:'.$org['MAILTO'].'" >'.$org['CN'].'</a>';
			else $text = $org['CN'];
		}
		else {
			if (!empty  ($org['MAILTO'])) $text = '<a href="mailto:'.$org['MAILTO'].'" >'.$org['MAILTO'].'</a>';
		}
		if (!empty ($text)) $text .= '&nbsp;';
		if (!empty ($org['SENT-BY'])) {
			$text .= __('Sent by ','amr-ical-events-list').'<a href="mailto:'.$org['SENT-BY'].'" >'.$org['SENT-BY'].'</a>';
		}
		return($text);
	}
}
/* -------------------------------------------------------- */
if (!function_exists('amr_parseModifiers')) {   
	function amr_parseModifiers($text)    {  
/* NAME="Contact Name";ID=28380;TYPE=SingleLine		
*/	
		$modifers = array();
		$p = explode (';',$text); 	/* if only a single will still return one array value */
		foreach ($p as $i => $v) {
			$eq			= strpos($v,'=');
			$pmodifier 	= substr ($v,0,$eq);
			$pvalue 	= substr ($v,$eq+1);
			$func 		= 'amr_parse'.$pmodifier;
			if (function_exists ($func)) {
				$modifers[$pmodifier] =  (call_user_func($func, $pvalue));
			}
			else {
				$modifers[$pmodifier] = $pvalue;
			}
		}
		return ($modifers);

    }
}	
	
/* -------------------------------------------------------- */
if (!function_exists('amr_format_repeatable_property')) {
function amr_format_repeatable_property ($content, $k, $event, $before='', $after='') {
// for properties that can have multiple values and for which we have received an array of those values, we need to do the format routine for each value

	$c = '';
	foreach ($content as $i => $v) {
		if (!(empty($v))) {
			$c .= amr_format_value ($v, $k, $event,	$before,$after) .' ';
			}
	}
	return ($c);
}
}
/* --------------------------------------------------------- */
if (!function_exists('amr_format_value')) {
	function amr_format_value ($content, $k, $event, $before='', $after='') { /* include the event so we can check for things like all day */
	/*  Format each Ical value for our presentation purposes
	Note: Google does toss away the html when editing the text, but it is there if you add but don't edit.
	what about all day?
	*/
		global $amr_formats;  /* amr check that this get set to the chosen list type */
		global $amr_options;
		global $amr_listtype;
		global $eventtaxonomies;

	//	echo '<br >'.$k;
		if (empty($content)) return('');

		if ($k == 'ORGANIZER') 	{ // it is an array but a parsed one, not repeatable
			$htmlcontent = amr_format_organiser ($content);
			}
		elseif ($k == 'ATTENDEE') 	{
			$htmlcontent = amr_format_attendees ($content);
			}
		else if (is_object($content)) {
			switch ($k){
				case 'EventDate': {
					$htmlcontent = ('<abbr class="dtstart" title="'
	//					.amr_format_date ('l jS F Y, H:i e ', $content).'">'
						.amr_format_date ('c', $content).'">' //must be ISO 8601 date for microformats to work
						.amr_format_date ($amr_formats['Day'], $content)
						.'</abbr>'
						);
					break;
				}
				case 'EndDate': {
					$days = amr_event_is_multiday($event);
					if ( $days > 1)
						$htmlcontent = ('<abbr class="dtend" title="'
						.amr_format_date ('c', $content).'">'  //must be ISO 8601 date
						.amr_format_date ($amr_formats['Day'], $content)
						.'</abbr>'
						);
					else $htmlcontent = '';
					break;
				}
				case 'EndTime':
				case 'StartTime':{
					if (isset($event['allday']) and ($event['allday'] === 'allday'))
						$htmlcontent = '';
					else
						$htmlcontent = amr_format_time ($amr_formats['Time'], $content);
					break;
				}
				case 'DTSTART':
				case 'DTEND':
				case 'UNTIL': {
					$htmlcontent = amr_format_date ($amr_formats['Day'], $content);
					if (empty($event['allday']) or !($event['allday'] == 'allday'))
						$htmlcontent  .= ' '.amr_format_time ($amr_formats['Time'], $content);
					break;
					}
				case 'X-WR-TIMEZONE': { /* amr  need to add code to reformat the timezone as per admin entry.  Also only show if timezone different ? */
					$htmlcontent = amr_format_tz(timezone_name_get($content));
					break;
				}
				case 'TZID': { /* amr  need to add code to reformat the timezone as per admin entry.  Also only show if timezone different ? */
					$htmlcontent = amr_format_tz (timezone_name_get($content));
					break;
				}
				default: 	/* should not be any */
					$htmlcontent = amr_format_date ($amr_formats['DateTime'], $content);
			}
		}
		elseif (is_array($content)) {

			if ($k === 'DURATION') {
				$htmlcontent = amr_format_duration ($content);
			}
			elseif (($k === 'RRULE') or ($k === 'EXRULE')) {
				$htmlcontent = amr_format_rrule($content);
				}
			elseif (($k === 'RDATE') or ($k === 'EXDATE')) {
				$htmlcontent = amr_prettyprint_r_ex_date ($content);
				}
			elseif ($k=== 'CATEGORIES') {  // umm - what if ics category
					$htmlcontent = amr_format_taxonomies ('category', $content);

				}
			elseif ($k=== 'post_tag' ) {
					$htmlcontent = amr_format_taxonomies ('post_tag', $content);

				}
			elseif ($k == 'ATTACH') {
				if (isset($content[0]['type'] ))	{
					// then we are at the top level of the array, so can ask to handled repetaed values
					return ( amr_format_repeatable_property ($content, $k, $event, $before, $after));
				}
				else
					$htmlcontent = amr_format_attach ($content, $event);
			}
			else {  /* simple array don't think we need to list the items separately eg: multiple comments or descriptions - just line  */
				if (!empty( $eventtaxonomies) and in_array( $k, $eventtaxonomies)) {
						$htmlcontent = amr_format_taxonomies ($k, $content);
					}
				else {
					return( amr_format_repeatable_property ($content, $k, $event, $before, $after));
				}
			}
		}
		elseif (is_null($content) OR ($content === ''))
			$htmlcontent = '';
		else {
			if (function_exists ('amr_format_'.$k)) {
				$htmlcontent =(call_user_func('amr_format_'.$k, $content));
			}
			else 
			switch ($k){
				case 'COMMENT':
				case 'DESCRIPTION': {
					$htmlcontent = html_entity_decode(amr_click_and_trim(nl2br2(amr_amp($content))));
					break;
				}
				case 'SUMMARY':
				case 'icsurl':
				case 'addtogoogle':
				case 'addevent':
				case 'subscribeevent':
				case 'subscribeseries':
				case 'map':
				case 'refresh':
				case 'attending_event': {
					$htmlcontent = $content; /* avoid hyperlink as we may have added url already */
					break;
				}
				case 'URL': /* assume valid URL, should not need to validate here, then format it as such */
					$htmlcontent = amr_format_url($content);
					break;
				case 'LOCATION': {
					$htmlcontent = (amr_click_and_trim(nl2br2(amr_amp($content))));
					break;
				}

				case 'X-WR-TIMEZONE':{	/* not parsed as object - since it is cal attribute, not property attribue */
					$htmlcontent = (amr_format_tz ($content));
					break;
				}
				case 'allday': {
					$htmlcontent =(amr_format_allday($content));
					break;
				}


				default: 	/* Convert any newlines to html breaks */

					if (!empty( $eventtaxonomies) and in_array( $k, $eventtaxonomies)) {
						$htmlcontent = amr_format_taxonomies ($k, $content);
					}
					else {
						$func = 'amr_format_'.str_replace('-','_',$k);

						if (function_exists($func))		{	
							$htmlcontent = call_user_func ($func, $content);

						}
						$htmlcontent = str_replace("\n", "<br />", $content);
					}	

			}
			
		}

		if (empty ($htmlcontent) ) 
			return;
		return ($before.$htmlcontent.$after);

	}
}
/* ------------------------------------------------------------------------------------*/
if (!function_exists('amr_wp_format_date')) {
function amr_wp_format_date( $format, $datestamp, $gmttf) { /* want a  integer timestamp or a date object  */
global $amr_options, $wp_locale;
/* Need to get rid the unnecessary date logic - should only be using date objects for now */

	if (is_object($datestamp))	{
		$offset = $datestamp->getOffset();
		If (isset ($_REQUEST['tzdebug'])) {
			echo '<br />Want to format '.$datestamp->format('Ymd His').' in '.$format.' like this '.$datestamp->format($format).' but localised';
//			echo '<br />Add offset '.$offset/(60*60).' back to Unix timestamp to force correct localised date ';
			}
		$dateInt = $datestamp->format('U') + $offset;  //to get the time in right timezone
		}
	else if (is_integer ($datestamp)) $dateInt = $datestamp;
	else return(false);

	if (stristr($format, '%') ) return (strftime( $format, $dateInt ));  /* keep this for compatibility!  will not localise though */
	else {
		$text = date_i18n($format, $dateInt, $gmttf); /*  should  be false, otherwise we get the utc/gmt time.   */
		If (isset ($_REQUEST['tzdebug']))
			{
				echo '<br />Localised with gmt=false: '.$text.'<br />';
				$text2 = date_i18n($format, $dateInt, false);
				echo 'Localised with gmt=true:  '.$text2.'<br />';
				$text3 = amr_date_i18n ('D, F j, Y g:i a', $datestamp);
				echo 'Localised with amr date obj fn: '.$text3.'<br />';
			} 
		return ($text); //
		}
}
}
/* -------------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_time')) {
function amr_format_time( $format, $datestamp) { /* want a  integer timestamp or a date object  */
global 	$amr_options,
		$amr_globaltz;

	date_timezone_set ($datestamp, $amr_globaltz);  /* Converting here, but then some derivations wrong eg: unsetting of end date */
	// check for midnight, midday, noon etc
	$time = $datestamp->format('His');
	if (isset($_GET['tzdebug'])) echo  '<br />Time='.$time;

	$humanspeak = apply_filters('amr_human_time',$time);
	if (!($time === $humanspeak )) return($humanspeak);
	else
		return (amr_format_date( $format, $datestamp))	;
}
}
/* -------------------------------------------------------------------------------------------*/
if (!function_exists('amr_format_date')) {
function amr_format_date( $format, $datestamp) { /* want a  integer timestamp or a date object  */
global 	$amr_options,
		$amr_globaltz;

	//if (is_string($datestamp)) $datestamp = date_create($datestamp, $amr_globaltz);

	if (isset ($amr_options ['date_localise']))
		$method = $amr_options ['date_localise'];
	else
		$method = 'wp';  // v4.0.9 was none

	if (isset($_GET['tzdebug'])) echo  '<br />set tz for: '.$datestamp->format('c');

	date_timezone_set ($datestamp, $amr_globaltz);  /* Converting here, but then some derivations wrong eg: unsetting of end date */

	if (isset($_GET['tzdebug'])) echo  '<br />'.$datestamp->format('c');

	if ($method === 'wp') 
		return amr_wp_format_date ( $format, $datestamp, false);
	else if ($method === 'wpgmt') 
		return amr_wp_format_date ( $format, $datestamp, true);
	else if ($method === 'amr') 
		return amr_date_i18n ( $format, $datestamp);
	else {
		if (stristr($format, '%') ) return (strftime( $format, $datestamp->format('U') ));  /* keep this for compatibility!  will not localise though */
		else return ($datestamp->format($format));
		}
}
}
/*--------------------------------------------------------------------------------*/
if (!function_exists('amr_format_duration')) {
function amr_format_duration ($arr) {
	/* receive an array of hours, min, sec */

	foreach ($arr as $i => $d) if ($d === 0) unset ($arr[$i]);
	$i = count($arr);

	if ($i > 1) $sep = ', ';
	else $sep = '';

	$d = '';
	if (isset ($arr['years'] )) {
		$d .= sprintf (_n ("%u year", "%u years", $arr['years'], 'amr-ical-events-list'), $arr['years']);
		$d .= $sep;
		$i = $i-1;
		}
	if (isset ($arr['months'] )) {
		$d .= sprintf (_n ("%u month ", "%u months ", $arr['months'], 'amr-ical-events-list'), $arr['months']);
		if ($i> 1) {$d .= $sep;}
		$i = $i-1;
		}
	if (isset ($arr['weeks'] )) {
		$d .= sprintf (_n ("%u week ", "%u weeks", $arr['weeks'], 'amr-ical-events-list'), $arr['weeks']);
		if ($i> 1) {$d .= $sep;}
		$i = $i-1;
		}
	if ((isset ($arr['days'] )) ) {
			$d .= sprintf (_n ("%u day", "%u days", $arr['days'], 'amr-ical-events-list'), $arr['days']);
//			If (ICAL_EVENTS_DEBUG) {echo ' and d = '.$d;}
			if ($i> 1) {$d .= $sep;}
			$i = $i-1;
		}
	if (isset ($arr['hours'] )) {
		$d .= sprintf (_n ("%u hour", "%u hours", $arr['hours'], 'amr-ical-events-list'), $arr['hours']);
		if ($i> 1) {$d .= $sep;}
		$i = $i-1;
		}
	if (isset ($arr['minutes'] )) {
		$d .= sprintf (_n ("%u minute", "%u minutes", $arr['minutes'], 'amr-ical-events-list'), $arr['minutes']);
		if ($i> 1) {$d .= $sep;}
		$i = $i-1;
		}
	if (isset ($arr['seconds'] )) {
		$d .= sprintf (_n ("%u second", "%u seconds", $arr['seconds'], 'amr-ical-events-list'), $arr['seconds']);

		}
	return($d);
	}
}
/* --------------------------------------------------------- */
if (!function_exists('amr_format_tz')) {
function amr_format_tz ($tzstring) {
global $amr_globaltz, $amr_options;

	$url = esc_url_raw($_SERVER['REQUEST_URI']);
	$tz = timezone_name_get($amr_globaltz);
	if ($tz === $tzstring) 
		$tz2 = date_default_timezone_get();
	else 
		$tz2 = $tzstring;
	if ($tz2===$tz) 
		$tz2 = 'UTC';
	$text1 = __('Change Timezone','amr-ical-events-list');
	$text2 = sprintf( __('Timezone: %s, Click for %s','amr-ical-events-list'),$tz, $tz2);
	if (isset ($amr_options['no_images']) and $amr_options['no_images']) 
		$t3 = $text1;
	else 
		$t3 = '<img title = "'.$text2.'" src="'.IMAGES_LOCATION.TIMEZONEIMAGE.'" class="amr-bling" alt="'.$text1.'" />';

	return ('<a class="timezone amr-bling" href="'
		.htmlentities(add_querystring_var($url,'tz',$tz2)).'" title="'
		.$text2.'" >'.$t3.' </a>');
}
}
/* ------------------------------------------------------------------------------------*/
if (!function_exists('adebug')) {  // we are loading late, so hope fully this should be fine - don'twant top long a name
	function adebug( $text, $whattodebug=true) {
		if ((isset ($_REQUEST['debug']) ) and ($_REQUEST['debug'] == $whattodebug))
			echo $text;
	}
}