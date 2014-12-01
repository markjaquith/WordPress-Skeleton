<?php
/*
Description: Display a sweet, concise list of events from iCal sources, using a list type from the amr iCal plugin <a href="options-general.php?page=manage_amr_ical">Manage Settings Page</a> and  <a href="widgets.php">Manage Widget</a>

*/
class amr_ical_widget extends WP_widget {
    /** constructor */
    function amr_ical_widget() {
		$widget_ops = array ('description'=>__('Upcoming Events', 'amr-ical-events-list' ),'classname'=>'events');
        $this->WP_Widget(false, __('Upcoming Events List', 'amr-ical-events-list' ), $widget_ops);
    }
/* ============================================================================================== */
	function widget ($args /* the title etc */, $instance /* the params */) { /* this is the piece that actualy does the widget display */
	global $amrW,
	$amr_options,
	$amr_limits,
	$amr_listtype,
	$amr_calendar_url,
	$amr_ical_am_doing,
	$change_view_allowed,
	$widget_icalno, /* used to give each ical widget a unique id on a page */
	$amr_been_here;  /* used to detect if we are looping somehow - reset at the end of each widget or shortcode. */
//
	if (amr_a_nested_event_shortcode ()) return (false); //someone entered an event shortcode into event content - causing a loop of events lists inside event lists
		
	amr_ical_load_text(); // do we have to reload all over theplace ?  wp does not always seem to have the translations
	$change_view_allowed = false;
	$amr_ical_am_doing = 'listwidget';
	extract ($args, EXTR_SKIP); /* this is for the before / after widget etc*/
	unset($args);  //nb do not delete this else mucks up the args later
	extract ($instance, EXTR_SKIP); /* this is for the params etc*/

	if (!empty ($shortcode_urls)) // get any args stored in the widget settings
		$args		= shortcode_parse_atts($shortcode_urls);
		
	if (empty ($args['listtype'])) 
		$args['listtype'] = '4';
	$amr_listtype = (int) $args['listtype'];
	

//
	if (!empty ($externalicalonly) and $externalicalonly)
			$args['eventpoststoo'] = '0';
	else 	$args['eventpoststoo'] = '1';
//	$args['show_month_nav'] = '0';    // comment out, otherwise cannot have at all if requetsed?
	$args['headings'] = '1';
	$args['show_views'] = '0';

	$amrW = 'w';	 /* to maintain consistency with previous version and prevent certain actions */
	$criteria 	= amr_get_params ($args);  /* this may update listtype, limits  etc */

// what was this for ??
	if (isset ($criteria['event'])) unset ( $criteria['event']);  //*** later may need to check for other custo posttypes

	if (ICAL_EVENTS_DEBUG) echo '<hr>ical list widget:'.$amr_listtype.' <br />'.amr_echo_parameters();

	if (isset($doeventsummarylink) and !($doeventsummarylink)) $amrW = 'w_no_url';

	$moreurl = trim($moreurl," ");
	$moreurl = (empty($moreurl)) ? null : $moreurl ;
	$amr_calendar_url = esc_url($moreurl);
	if (ICAL_EVENTS_DEBUG) echo 'Calendar url = '.$amr_calendar_url;
	if (isset($_REQUEST['lang'])) 
		$moreurl = add_query_arg('lang',$_REQUEST['lang'],$moreurl);
// wp 3.3.1 doesn't like html in titles anymore - why ?		
//	if (!empty ($moreurl))
//		$title = '<a title="'.__('Look for more','amr-ical-events-list').'" href= "'.$moreurl.'">'.__($title,'amr-ical-events-list') .'</a>';

	if (!(isset($widget_icalno)))
		$widget_icalno = 0;
	else 
		$widget_icalno= $widget_icalno + 1;

	$content = amr_process_icalspec($criteria,
		$amr_limits['start'], $amr_limits['end'], $amr_limits['events'], $widget_icalno);
	//output...

	//var_dump($criteria);
	echo $before_widget;

//	if (!empty($criteria['headings']))
	echo $before_title
		. apply_filters('widget_title',__($title,'amr-ical-events-list' ))
		. $after_title ;

	echo $content;
	echo $after_widget;
	
	/* we made it out the other end without looping ?*/
	$amr_been_here = false;

	}
/* ============================================================================================== */

	function update($new_instance, $old_instance) {  /* this does the update / save */
		$instance                      = $old_instance;

		$instance['title']             = strip_tags($new_instance['title']);
		$instance['moreurl']           = strip_tags($new_instance['moreurl']);
		$instance['moreurl'] 		   = amr_make_sticky_url($instance['moreurl'] );
		$instance['doeventsummarylink']= strip_tags($new_instance['doeventsummarylink']);
		$instance['externalicalonly']  = strip_tags($new_instance['externalicalonly']);
		$instance['shortcode_urls']    = strip_tags($new_instance['shortcode_urls']);
		if (get_option('amr-ical-widget') )
			delete_option('amr-ical-widget'); /* if it exists - leave code for a while for conversion */

		return $instance;

	}

/* ============================================================================================== */

	function form($instance) { /* this does the display form */
	global $amrW;

        $instance = wp_parse_args( (array) $instance, array(
			'title' => __('Upcoming Events','amr-ical-events-list') ,
			'moreurl' => '',
			'doeventsummarylink' => true,
			'externalicalonly'  => false,
			'shortcode_urls' => ''
			) );

		$title             = $instance['title'];
		$moreurl           = $instance['moreurl'];
		$doeventsummarylink= $instance['doeventsummarylink'];
		$externalicalonly  = $instance['externalicalonly'];
		$shortcode_urls    = $instance['shortcode_urls'];

		if ($opt = get_option('amr-ical-widget')) {  /* delete the old option in the save */
			if (isset ($opt['urls']) ) $shortcode_urls = str_replace(',', ' ',$opt['urls']);  /* in case anyone had multiple urls separate by commas - change to spaces*/
			if (isset ($opt['moreurl']) ) $moreurl = $opt['moreurl'];
			if (isset ($opt['title']) ) $title = $opt['title'];
			if (isset ($opt['listtype'])  and (!($opt['listtype']===4))) $shortcode_urls = 'listtype='.$opt['listtype'].' '.$shortcode_urls;
			if (isset ($opt['limit']) and (!($opt['limit']==='5'))) $shortcode_urls = 'events='.$opt['limit'].' '.$shortcode_urls;
		}

	$seemore = __('See plugin website for more details','amr-ical-events-list');
 // <input type="hidden" name="submit" value="1" />?>
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'amr-ical-events-list');
	?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text"
	value="<?php echo esc_attr($title); ?>" />		</label></p>

	<p>
	<label for="<?php echo $this->get_field_id('moreurl'); ?>"><b><?php
	_e('Calendar page url', 'amr-ical-events-list'); ?></b><br /><em>
	<?php _e('Calendar page url in this website', 'amr-ical-events-list');
	?></em> <a href="http://icalevents.com/1901-widgets-calendar-pages-and-event-urls/" title="<?php echo $seemore; ?>"><b>?</b></a>
	<input id="<?php echo $this->get_field_id('moreurl'); ?>" name="<?php echo $this->get_field_name('moreurl'); ?>" type="text" style="width: 200px;"
	value="<?php echo esc_attr($moreurl); ?>" /></label></p>
	<p>
	<label for="<?php echo $this->get_field_id('doeventsummarylink'); ?>"><b><?php
	_e('Hover description on Title', 'amr-ical-events-list'); ?></b><br /><em><?php
	_e('Do an event summary hyperlink with event description as title text ', 'amr-ical-events-list');
	?></em> <a href="http://icalevents.com/1908-hovers-lightboxes-or-clever-css/" title="<?php echo $seemore; ?>"><b>?</b></a>
	<input id="<?php echo $this->get_field_id('doeventsummarylink'); ?>" name="<?php
	echo $this->get_field_name('doeventsummarylink'); ?>" type="checkbox"
	value="true" <?php if ($doeventsummarylink) echo 'checked="checked"';?> /></label></p>
	<p>
	<label for="<?php echo $this->get_field_id('externalicalonly'); ?>"><b><?php
	_e('External events only', 'amr-ical-events-list'); ?></b><br /><em><?php
	_e('Show events from external ics only, do NOT pickup any internal events.', 'amr-ical-events-list');
	?></em><a href="http://icalevents.com" title="<?php _e('Else include events created internally too','amr-ical-events-list'); ?>"><b>?</b></a>
	<input id="<?php echo $this->get_field_id('externalicalonly'); ?>" name="<?php
	echo $this->get_field_name('externalicalonly'); ?>" type="checkbox"
	value="true" <?php if ($externalicalonly) echo 'checked="checked"';?> /></label></p>
	<p>
	<label for="<?php echo $this->get_field_id('shortcode_urls');?>"><b><?php
	_e('External ics urls and advanced options', 'amr-ical-events-list'); ?></b><br /><em><?php
	_e('External ics urls and/or optional shortcode parameters separated by spaces.)', 'amr-ical-events-list'); echo '<br />';
	_e(' Examples: listtype=4 events=10 days=60 start=yyyymmdd startoffset=-2... )', 'amr-ical-events-list');
	?></em> </label>
	<a href="http://icalevents.com/amr-ical-events-list/#shortcode" title="<?php _e('See more parameters','amr-ical-events-list'); ?>"><b>?</b></a>
	<textarea cols="25" rows="10" id="<?php echo $this->get_field_id('shortcode_urls');?>" name="<?php echo $this->get_field_name('shortcode_urls'); ?>" ><?php

		echo esc_attr($shortcode_urls); ?></textarea></p>

<?php }
/* ============================================================================================== */

}
class amr_icalendar_widget extends WP_widget {
    /** constructor */

    function amr_icalendar_widget() {
		$widget_ops = array ('description'=>__('Upcoming Events', 'amr-ical-events-list' ),
		'classname'=>'widget_calendar');

        $this->WP_Widget(false, __('Upcoming Events Calendar', 'amr-ical-events-list' ), $widget_ops);

    }

/* ============================================================================================== */
	function widget ($args, $instance) { /* this is the piece that actualy does the widget display */
	global $amrW;
	global $amr_options;
	global $amr_limits;
	global $amr_listtype;
	global $change_view_allowed;
	global $widget_icalno; /* used to give each ical widget a unique id on a page */
	global $amr_calendar_url,
	$amr_been_here;
	
	// don't check becuase it checks in the shortcode function we call
	// 20140209  redundant - done in the shortcode function too $criteria 	= amr_get_params ($args);  /* this may update listtype, limits  etc */
	amr_ical_load_text(); // do we have to reload all over theplace ?  wp does not always seem to have the translations

	$change_view_allowed = false;
//	$amr_listtype = '8';  /* default only, can be overwitten in shortcode or query string  */

	extract ($args, EXTR_SKIP); /* this is for the before / after widget etc*/
	unset($args);
	extract ($instance, EXTR_SKIP); /* the widget form fields */

	if (isset ($moreurl) ) $moreurl = trim($moreurl," ");
	$amr_calendar_url = (empty($moreurl)) ? null : $moreurl ;

	if (!empty ($shortcode_urls)) // from the instance
		$atts 		= shortcode_parse_atts($shortcode_urls);
	if (!empty ($externalicalonly) and $externalicalonly)
		$atts['eventpoststoo'] = '0';
	else
		$atts['eventpoststoo'] = '1';
	$atts['show_views'] = '0';
	$atts['ignore_query'] = 1;
	$atts['show_month_nav'] = 1;
//
	if (!(isset($widget_icalno))) 
		$widget_icalno = 0;
	else 
		$widget_icalno= $widget_icalno + 1;
	$amrW = 'w';	 /* to maintain consistency with previous version */

	$content 	= amr_do_smallcal_shortcode($atts);  // thsi will check query params etc

	//output...

	if (!empty($before_widget)) echo $before_widget;
	if (!empty($before_title)) echo $before_title;
	if (!empty($title)) echo apply_filters('widget_title',__($title,'amr-ical-events-list') );
	if (!empty($after_title)) echo  $after_title;
	echo $content;
	if (!empty($after_widget)) echo $after_widget;
	if (isset ($savedays)) $amr_limits['days'] = $savedays;
	
	/* we made it out the other end without looping ?*/
	$amr_been_here = false;
	}
/* ============================================================================================== */
	function update($new_instance, $old_instance) {  /* this does the update / save */
		$instance                      = $old_instance;

		$instance['title']             = strip_tags($new_instance['title']);
//		if (!empty($instance['externalicalonly']))   // was causing it not to svae - why?
			$instance['externalicalonly']  = ($new_instance['externalicalonly']);

		$instance['shortcode_urls']    = strip_tags($new_instance['shortcode_urls']);
		$instance['moreurl']		   = strip_tags($new_instance['moreurl']);
		$instance['moreurl'] 		   = amr_make_sticky_url($instance['moreurl'] );

		return $instance;

	}
/* ============================================================================================== */

	function form($instance) { /* this does the display form */

        $instance = wp_parse_args( (array) $instance, array(
			'title' => __('Upcoming Events','amr-ical-events-list'),
			'externalicalonly'  => false,
			'moreurl' 			=> '',
			'shortcode_urls' => 'ignore_query=1'
			) );
		$title             = $instance['title'];
		$moreurl           = $instance['moreurl'];
		$externalicalonly  = $instance['externalicalonly'];
		$shortcode_urls    = $instance['shortcode_urls'];
		$seemore =  __('See more','amr-ical-events-list');
	?><p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'amr-ical-events-list');
	?><input class="widefat" id="<?php
	echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text"
	value="<?php echo esc_attr($title); ?>" /></label></p>
		<p>
	<label for="<?php echo $this->get_field_id('moreurl'); ?>"><b><?php
	_e('Calendar page url', 'amr-ical-events-list'); ?></b><br /><em>
	<?php _e('Calendar page url in this website, for links from widget', 'amr-ical-events-list');
	?></em> <a href="http://icalevents.com/1901-widgets-calendar-pages-and-event-urls/" title="<?php echo $seemore; ?>"><b>?</b></a>
	<input id="<?php echo $this->get_field_id('moreurl'); ?>" name="<?php echo $this->get_field_name('moreurl'); ?>" type="text" style="width: 200px;"
	value="<?php echo esc_attr($moreurl); ?>" /></label></p>
		<p>
	<label for="<?php echo $this->get_field_id('externalicalonly'); ?>"><b><?php
	_e('External events only', 'amr-ical-events-list'); ?></b><br /><em><?php
	_e('Show events from external ics only, do NOT pickup any internal events.', 'amr-ical-events-list');
	?></em><a href="http://icalevents.com" title="<?php _e('Else include events created internally too','amr-ical-events-list'); ?>"><b>?</b></a>
	<input id="<?php echo $this->get_field_id('externalicalonly'); ?>" name="<?php
	echo $this->get_field_name('externalicalonly'); ?>" type="checkbox"
	value="true" <?php if ($externalicalonly) echo 'checked="checked"';?> /></label></p>
	<p>
	<label for="<?php echo $this->get_field_id('shortcode_urls');?>"><b><?php
	_e('External ics urls and advanced options', 'amr-ical-events-list'); ?></b><br /><em><?php
	_e('External ics urls and/or optional shortcode parameters separated by spaces.)', 'amr-ical-events-list'); echo '<br />';
	_e(' Examples: listtype=8 events=10 days=60 start=yymmdd startoffset=-2... )', 'amr-ical-events-list');
	?></em> </label>
	<a href="http://icalevents.com/amr-ical-events-list/#shortcode" title="<?php echo $seemore; ?>"><b>?</b></a>
	<textarea cols="25" rows="10" id="<?php
		echo $this->get_field_id('shortcode_urls');?>" name="<?php
		echo $this->get_field_name('shortcode_urls'); ?>" ><?php
		echo esc_textarea($shortcode_urls); ?></textarea></p>


<?php }
/* ============================================================================================== */

}


?>