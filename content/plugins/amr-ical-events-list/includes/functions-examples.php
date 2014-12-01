<?php //examples of functions that could be called in another plugin or in a themes function.php 

// example of filter function that could be used.

// ---------------------------------------------------------------------------
function amr_limit_to_first ($count) {
global $amr_ical_am_doing;  //will contain one of:  list, calendar, listwidget, smallcalendar, eventinfo

	if ((!empty ($amr_ical_am_doing)) and ($amr_ical_am_doing === 'list')) return (1);
	else return $count;
}
add_filter('amr_event_repeats','amr_limit_to_first');


// ---------------------------------------------------------------------------

function amr_semi_paginate() {
global $amr_limits;
	return ('your own pagination code - plus lets see what is in the amr_limits. <br />'
	. print_r($amr_limits, true));
} 
?>