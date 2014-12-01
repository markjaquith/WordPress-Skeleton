<?php
 /**
 * Register jQuery scripts and CSS files
 * Hooked on to init
 *
 * @since 1.0.0
 * @ignore
 * @access private
 */
function eventorganiser_register_script() {
	global $wp_locale;
	$version = defined( 'EVENT_ORGANISER_VER' ) ? EVENT_ORGANISER_VER : false;

	$ext = (defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG) ? '' : '.min';

	/* FullCalendar */
	wp_register_script( 'eo_fullcalendar', EVENT_ORGANISER_URL."js/fullcalendar{$ext}.js",array(
		'jquery',
		'jquery-ui-core',
		'jquery-ui-widget',
		'jquery-ui-button',
	),$version,true);
	
	/* Google Maps */
	$protocal = is_ssl() ? 'https://' : 'http://';
	if( is_admin() )
		wp_register_script( 'eo_GoogleMap', $protocal.'maps.googleapis.com/maps/api/js?sensor=false&language='.substr(get_locale(),0,2));
	else
		wp_register_script( 'eo_GoogleMap', $protocal.'maps.googleapis.com/maps/api/js?sensor=false&callback=eo_load_map&language='.substr(get_locale(),0,2));

	/* Front-end script */
	wp_register_script( 'eo_front', EVENT_ORGANISER_URL."js/frontend{$ext}.js",array(
		'jquery','eo_qtip2',
		'jquery-ui-core',
		'jquery-ui-widget',
		'jquery-ui-button',
		'jquery-ui-datepicker',
		'eo_fullcalendar',
		'eo-wp-js-hooks'
	),$version,true);
	
	/* Add js variables to frontend script */
	$category = get_taxonomy( 'event-category' );
	$venue    = get_taxonomy( 'event-venue' );
	$tag      = get_taxonomy( 'event-tag' );
	wp_localize_script( 'eo_front', 'EOAjaxFront', array(
		'adminajax' => admin_url( 'admin-ajax.php'),
		'locale'    => array(
			'locale'      => substr(get_locale(),0,2),
			'isrtl'       => $wp_locale->is_rtl(),
			'monthNames'  => array_values( $wp_locale->month ),
			'monthAbbrev' => array_values( $wp_locale->month_abbrev ),
			'dayNames'    => array_values( $wp_locale->weekday ),
			'dayAbbrev'   => array_values( $wp_locale->weekday_abbrev ),
			'ShowMore'    => __( 'Show More', 'eventorganiser '),
			'ShowLess'    => __( 'Show Less', 'eventorganiser' ),
			'today'       => __( 'today', 'eventorganiser' ),
			'day'         => __('day','eventorganiser'),
			'week'        => __('week','eventorganiser'),
			'month'       => __('month','eventorganiser'),
			'gotodate'    => __('go to date','eventorganiser'),
			'cat'         => $category ? $category->labels->view_all_items : false,
			'venue'       => $venue    ? $venue->labels->view_all_items    : false,
			'tag'         => $tag      ? $tag->labels->view_all_items      : false,
			//Allow themes to over-ride juqery ui styling and not use images
			'nextText' => '>',
			'prevText' => '<'
		)
	));

	/* WP-JS-Hooks */
	wp_register_script( 'eo-wp-js-hooks', EVENT_ORGANISER_URL."js/event-manager{$ext}.js",array('jquery'),$version,true);
	
	/* Q-Tip */
	wp_register_script( 'eo_qtip2', EVENT_ORGANISER_URL.'js/qtip2.js',array('jquery'),$version,true);

	/* Styles */
	wp_register_style('eo_calendar-style',EVENT_ORGANISER_URL."css/fullcalendar{$ext}.css",array(),$version);
	wp_register_style('eo_front',EVENT_ORGANISER_URL."css/eventorganiser-front-end{$ext}.css",array(),$version);
}   
add_action('init', 'eventorganiser_register_script');

 /**
 *Register jQuery scripts and CSS files for admin
 *
 * @since 1.0.0
 * @ignore
 * @access private
 */
function eventorganiser_register_scripts(){
	$version = defined( 'EVENT_ORGANISER_VER' ) ? EVENT_ORGANISER_VER : false;
	$ext = (defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG) ? '' : '.min';

	/*  Venue (map) utility script */
	wp_register_script( 'eo-venue-util', EVENT_ORGANISER_URL."js/venue-util{$ext}.js",array(
		'jquery',
		'eo_GoogleMap'
	),$version,true);
	
	/*  Venue script for venue edit */
	wp_register_script( 'eo-venue-admin', EVENT_ORGANISER_URL."js/venue-admin{$ext}.js",array(
		'jquery',
		'eo-venue-util'
	),$version,true);
	
	/*  Script for event edit page. (Legacy version) */
	wp_register_script( 'eo-time-picker', EVENT_ORGANISER_URL."js/time-picker{$ext}.js",array(
		'jquery',
		'jquery-ui-datepicker',		
	),$version,true);
	
	/* New version - prefixed timepicker */
	wp_register_script( 'eo-timepicker', EVENT_ORGANISER_URL."js/jquery-ui-eo-timepicker{$ext}.js",array(
			'jquery',
			'jquery-ui-datepicker',
	),$version,true);
	
	wp_register_script( 'eo_event', EVENT_ORGANISER_URL."js/event{$ext}.js",array(
		'jquery',
		'jquery-ui-datepicker',
		'eo-timepicker',
		'eo-time-picker',//Deprecated remove in 3.0?
		'eo-venue-util',
		'jquery-ui-autocomplete',
		'jquery-ui-widget',
		'jquery-ui-button',
		'jquery-ui-position'
	),$version,true);
	
	wp_register_script( 'eo-edit-event-controller', EVENT_ORGANISER_URL."js/edit-event-controller{$ext}.js",array(
			'jquery',
			'eo_event',
	),$version,true);

	/*  Script for admin calendar */
	wp_register_script( 'eo_calendar', EVENT_ORGANISER_URL."js/admin-calendar{$ext}.js",array(
		'eo_fullcalendar',
		'jquery-ui-datepicker',
		'jquery-ui-autocomplete',
		'jquery-ui-widget',
		'jquery-ui-button',
		'jquery-ui-dialog',
		'jquery-ui-tabs',
		'jquery-ui-position'
	),$version,true);

	/*  Pick and register jQuery UI style */
	$style = ( 'classic' == get_user_option( 'admin_color') ? 'classic' : 'fresh' );
	wp_register_style('eventorganiser-jquery-ui-style',EVENT_ORGANISER_URL."css/eventorganiser-admin-{$style}{$ext}.css",array(),$version);
	
	/* Admin styling */
	wp_register_style( 'eventorganiser-3.8+', EVENT_ORGANISER_URL."css/eventorganiser-admin-3.8+{$ext}.css", array(), $version );
	$deps = array( 'eventorganiser-jquery-ui-style' );
	if ( ( defined( 'MP6' ) && MP6 ) || version_compare( '3.8-beta-1', get_bloginfo( 'version' ) ) <= 0 ) {
		$deps[] = 'eventorganiser-3.8+';
	}
	wp_register_style( 'eventorganiser-style', EVENT_ORGANISER_URL."css/eventorganiser-admin-style{$ext}.css", $deps, $version );

	/* Inline Help */
	wp_register_script( 'eo-inline-help', EVENT_ORGANISER_URL.'js/inline-help.js',array( 'jquery', 'eo_qtip2' ), $version, true );
}
add_action( 'admin_init', 'eventorganiser_register_scripts', 5 );

/**
 * The "Comprehensive Google Map Plugin" plug-in deregisters all other Google scripts registered
 * by other plug-ins causing these plug-ins not to function. This plug-in removes that behaviour.
 *
 * Of course if two google scripts are loaded there may be problems, but this is better than always having
 * experiencing a 'bug'. At time writing the function responsible `cgmp_google_map_deregister_scripts()`
 * can be found here {@see https://github.com/azagniotov/Comprehensive-Google-Map-Plugin/blob/master/functions.php#L520 }
 *
 * @see https://github.com/stephenharris/Event-Organiser/issues/49
 * @see https://wordpress.org/support/topic/googlemap-doesnt-shown-on-event-detail-page
 * @since 1.7.4
 * @ignore
 * @access private
 */
function eventorganiser_cgmp_workaround(){
	remove_action( 'wp_head', 'cgmp_google_map_deregister_scripts', 200 );
}
add_action( 'wp_head', 'eventorganiser_cgmp_workaround', 1 );
	
 /**
 * Check the export and event creation (from Calendar view) actions. 
 * These cannot be called later. Most other actions are only called when
 * the appropriate page is loading.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 */
function eventorganiser_admin_init(){
	global $EO_Errors;
	$EO_Errors = new WP_Error();
}
add_action('admin_init','eventorganiser_admin_init',0);
add_action('admin_init', array('Event_Organiser_Im_Export', 'get_object'));

 /**
 * @since 1.0.0
 * @ignore
 * @access private
 */
function eventorganiser_public_export(){
	if( eventorganiser_get_option('feed') ){
		add_feed('eo-events', array('Event_Organiser_Im_Export','get_object'));
	}
}
add_action('init','eventorganiser_public_export');

/**
 * Queues up the javascript / style scripts for Events custom page type 
 * Hooked onto admin_enqueue_scripts
 *
 * @since 1.0.0
 * @ignore
 * @access private
 */
function eventorganiser_add_admin_scripts( $hook ) {
	global $post,$current_screen,$wp_locale;

	if ( $hook == 'post-new.php' || $hook == 'post.php') {
		if( $post->post_type == 'event' ) {     

			wp_enqueue_script('eo-edit-event-controller');
			wp_localize_script( 'eo_event', 'EO_Ajax_Event', array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'wpversion' => get_bloginfo('version'),
					'startday'=>intval(get_option('start_of_week')),
					'format'=> eventorganiser_php2jquerydate( eventorganiser_get_option('dateformat') ),
					'current_user_can' => array(
						'manage_venues' => current_user_can( 'manage_venues' ),
					),
					'is24hour' => eventorganiser_blog_is_24(),
					'location'=>get_option('timezone_string'),
					'locale'=>array(
						'isrtl' => $wp_locale->is_rtl(),
						'monthNames'=>array_values($wp_locale->month),
						'monthAbbrev'=>array_values($wp_locale->month_abbrev),
						'dayAbbrev'=>array_values($wp_locale->weekday_abbrev),
						'showDates' => __( 'Show dates', 'eventorganiser' ),
						'hideDates' => __( 'Hide dates', 'eventorganiser' ),
						'weekDay'=>$wp_locale->weekday,
						'meridian' => array( $wp_locale->get_meridiem('am'), $wp_locale->get_meridiem('pm') ),
						'hour'=>__('Hour','eventorganiser'),
						'minute'=>__('Minute','eventorganiser'),
						'day'=>__('day','eventorganiser'),
						'days'=>__('days','eventorganiser'),
						'week'=>__('week','eventorganiser'),
						'weeks'=>__('weeks','eventorganiser'),
						'month'=>__('month','eventorganiser'),
						'months'=>__('months','eventorganiser'),
						'year'=>__('year','eventorganiser'),
						'years'=>__('years','eventorganiser'),
						'daySingle'=>__('every day','eventorganiser'),
						'dayPlural'=>__('every %d days','eventorganiser'),
						'weekSingle'=>__('every week on','eventorganiser'),
						'weekPlural'=>__('every %d weeks on','eventorganiser'),
						'monthSingle'=>__('every month on the','eventorganiser'),
						'monthPlural'=>__('every %d months on the','eventorganiser'),
						'yearSingle'=>__('every year on the','eventorganiser'),
						'yearPlural'=>__('every %d years on the','eventorganiser'),
						'summary'=>__('This event will repeat','eventorganiser'),
						'until'=>__('until','eventorganiser'),
						'occurrence'=>array(__('first','eventorganiser'),__('second','eventorganiser'),__('third','eventorganiser'),__('fourth','eventorganiser'),__('last','eventorganiser'))
					)
					));
			wp_enqueue_style('eventorganiser-style');
		}
	}elseif($current_screen->id=='edit-event'){
			wp_enqueue_style('eventorganiser-style');
	}
}
add_action( 'admin_enqueue_scripts', 'eventorganiser_add_admin_scripts', 998, 1 );


/**
 * Perform database and WP version checks. Display appropriate error messages. 
 * Triggered on update.
 *
 * @since 1.4.0
 * @ignore
 * @access private
 */
function eventorganiser_db_checks(){
	global $wpdb;

	//Check tables exist
	$table_errors = array();
	if($wpdb->get_var("show tables like '$wpdb->eo_events'") != $wpdb->eo_events):
		$table_errors[]= $wpdb->eo_events;
	endif;
	if($wpdb->get_var("show tables like '$wpdb->eo_venuemeta'") != $wpdb->eo_venuemeta):
		$table_errors[]=$wpdb->eo_venuemeta;
	endif;

	if(!empty($table_errors)):?>
		<div class="error"	>
		<p>There has been an error with Event Organiser. One or more tables are missing:</p>
		<ul>
		<?php foreach($table_errors as $table):
			echo "<li>".$table."</li>";
		endforeach; ?>
		</ul>
		<p>Please try re-installing the plugin.</p>
		</div>
	<?php	endif;

	//Check WordPress version
	if(get_bloginfo('version')<'3.3'):?>
		<div class="error"	>
			<p>Event Organiser requires <strong>WordPress 3.3</strong> to function properly. Your version is <?php echo get_bloginfo('version'); ?>. </p>
		</div>
	<?php endif; 
}


/**
 * Displays any errors or notices in the global $EO_Errors
 *
 * @since 1.4.0
 * @ignore
 * @access private
 */
function eventorganiser_admin_notices(){
	global $EO_Errors;
	$errors=array();
	$notices=array();
	if(isset($EO_Errors)):
		$errors = $EO_Errors->get_error_messages('eo_error');
		$notices= $EO_Errors->get_error_messages('eo_notice');
		if(!empty($errors)):?>
			<div class="error"	>
			<?php foreach ($errors as $error):?>
				<p><?php echo $error;?></p>
			<?php endforeach;?>
			</div>
		<?php endif;?>
		<?php if(!empty($notices)):?>
			<div class="updated">
			<?php foreach ($notices as $notice):?>
				<p><?php echo $notice;?></p>
			<?php endforeach;?>
			</div>
		<?php	endif;
	endif;
}
add_action('admin_notices','eventorganiser_admin_notices');


 /**
 * Adds link to the plug-in settings on the settings page
 *
 * @since 1.5
 * @ignore
 * @access private
 */
function eventorganiser_plugin_settings_link($links, $file) {
    
	if( $file == 'event-organiser/event-organiser.php' ) {
		/* Insert the link at the end*/
		$links['settings'] = sprintf('<a href="%s"> %s </a>',
			admin_url('options-general.php?page=event-settings'),
			__('Settings','eventorganiser')
                );
        }
	return $links;
}
add_filter('plugin_action_links', 'eventorganiser_plugin_settings_link', 10, 2);    

/**
 * Schedules cron job for automatically deleting expired events
 *
 * @since 1.4.0
 * @ignore
 * @access private
 */
function eventorganiser_cron_jobs(){
	wp_schedule_event(time()+60, 'daily', 'eventorganiser_delete_expired');
}


/**
 * Clears the 'delete expired events' cron job.
 *
 * @since 1.4.0
 * @ignore
 * @access private
 */
function eventorganiser_clear_cron_jobs(){
	wp_clear_scheduled_hook('eventorganiser_delete_expired');
}

/**
 * Returns the time in seconds until a specified cron job is scheduled.
 *
 *@since 1.8
 *@see https://wordpress.stackexchange.com/questions/83270/when-does-next-cron-job-run-time-from-now/83279#83279
 *
 *@param string $cron_name The name of the cron job
 *@return int|bool The time in seconds until the cron job is scheduled. False if
 *it could not be found.
*/
function eventorganiser_get_next_cron_time( $cron_name ){
	if( $timestamp = wp_next_scheduled( $cron_name ) ){
		$timestamp = $timestamp - time();
	}
	return $timestamp;
}


/**
 * Callback for the delete expired events cron job. Deletes events that finished at least 24 hours ago.
 * For recurring events it is only deleted once the last occurrence has expired.
 *
 * @since 1.4.0
 * @ignore
 * @access private
 */
function eventorganiser_delete_expired_events(){
	//Get expired events
	$events = eo_get_events(array('showrepeats'=>0,'showpastevents'=>1,'eo_interval'=>'expired'));
	
	/**
	 * Filters how long (in seconds) after an event as finished it should be considered expired.
	 * 
	 * If enabled in *Settings > Event Organiser > General*, expired events are trashed.
	 * 
	 * @param int $time_until_expired Time (in seconds) to wait after an event has finished. Defaults to 24 hours. 
	 */
	$time_until_expired = (int) apply_filters( 'eventorganiser_events_expire_time', 24*60*60 );
	$time_until_expired = max( $time_until_expired, 0 );

	if($events):
		$now = new DateTime('now', eo_get_blog_timezone());
	
		foreach($events as $event):
			
			$start = eo_get_the_start( DATETIMEOBJ, $event->ID, null, $event->occurrence_id );
			$end = eo_get_the_end( DATETIMEOBJ, $event->ID, null, $event->occurrence_id );
			
			$expired = round(abs($end->format('U')-$start->format('U'))) + $time_until_expired; //Duration + expire time
			
			$finished =  eo_get_schedule_last( DATETIMEOBJ, $event->ID );
			$finished->modify("+$expired seconds");//[Expired time] after the last occurrence finishes
			
			//Delete if [expired time] has passed
			if( $finished <= $now ){
				wp_trash_post((int) $event->ID);
			}
			
		endforeach;
	endif;
}
add_action('eventorganiser_delete_expired', 'eventorganiser_delete_expired_events');



/**
 *  Adds retina support for the screen icon
 * Thanks to numeeja (http://cubecolour.co.uk/)
 *
 * @since 1.5.0
 * @ignore
 * @access private
 */
function eventorganiser_screen_retina_icon(){

	$screen_id = get_current_screen()->id;
	
	if ( ( defined( 'MP6' ) && MP6 ) || version_compare( '3.8-beta-1', get_bloginfo( 'version' ) ) <= 0 ):
		?>
		<style>
			#adminmenu #menu-posts-event div.wp-menu-image:before {content: '\f145';}
			#adminmenu #menu-posts-event div.wp-menu-image img { display:none; }
			/**Add-ons page: for contrast**/
			#eo-addons-wrap .eo-addon{ background: white; }
		</style>
		<?php
	endif;
	
	if( !in_array($screen_id, array('event','edit-event','edit-event-tag','edit-event-category','event_page_venues','event_page_calendar')) )
		return;
		 
	$icon_url = EVENT_ORGANISER_URL.'css/images/eoicon-64.png'
	?>
	<style>
	@media screen and (-webkit-min-device-pixel-ratio: 2) {
		#icon-edit.icon32 {
			background: url(<?php echo $icon_url;?>) no-repeat;
			background-size: 32px 32px;
			height: 32px;
			width: 32px;
		}
	}
	</style>
	<?php
}
add_action('admin_print_styles','eventorganiser_screen_retina_icon');


/**
 * Print generic javascript variables to the page
 * @ignore
 */
function eventorganiser_admin_print_scripts(){
	$is_mp6 = ( ( defined( 'MP6' ) && MP6 ) || version_compare( '3.8-beta-1', get_bloginfo( 'version' ) ) <= 0 );
	?>
	<script type="text/javascript">
		var eventorganiser = eventorganiser || {};
		eventorganiser.wp_version = '<?php echo get_bloginfo("version");?>';
		eventorganiser.is_mp6 = <?php echo $is_mp6 ? 'true' : 'false'; ?>;
	</script>
	<?php
}
add_action( 'admin_print_styles', 'eventorganiser_admin_print_scripts' );


/** 
 * Purge the occurrences cache
 * Hooked onto eventorganiser_save_event and eventorganiser_delete_event
 *
 *@access private
 * @ignore
 *@since 1.6
 */
function _eventorganiser_delete_occurrences_cache($post_id=0){
	wp_cache_delete( 'eventorganiser_occurrences_'.$post_id );
}
//The following need to trigger the cache clear clearly need to trigger a cache clear
$hooks = array('eventorganiser_save_event', 'eventorganiser_delete_event');
foreach( $hooks as $hook ){
	add_action($hook, '_eventorganiser_delete_occurrences_cache');
}


/**
 * Purge the cached results of get_calendar.
 * Hooked onto eventorganiser_save_event, eventorganiser_delete_event, wp_trash_post,
 * update_option_gmt_offset,update_option_start_of_week,update_option_rewrite_rules
 * and edited_event-category.
 *
 *@access private
 * @ignore
 *@since 1.5
 */
function _eventorganiser_delete_calendar_cache() {
	delete_transient( 'eo_widget_calendar' );
	delete_transient( 'eo_full_calendar_public' );
	delete_transient( 'eo_full_calendar_public_priv' );
	delete_transient( 'eo_full_calendar_admin' );
	delete_transient( 'eo_widget_agenda' );
}

//The following need to trigger the cache
$hooks = array(
	'eventorganiser_save_event', 'eventorganiser_delete_event', 'wp_trash_post','update_option_gmt_offset', /* obvious */
	'update_option_start_of_week', /* Start of week is used for calendars */
	'update_option_rewrite_rules', /* If permalinks updated - links on fullcalendar might now be invalid */ 
	'delete_option_rewrite_rules',
	'update_option_siteurl',
	'update_option_home',
	'edited_event-category', /* Colours of events may change */
);
foreach( $hooks as $hook ){
	add_action($hook, '_eventorganiser_delete_calendar_cache');
}


/**
 * Handles admin pointers
 *
 * Kick starts the enquing. Rename this to something unique (i.e. include your plugin/theme name).
 *
 *@access private
 *@ignore
 *@since 1.5
 */
function eventorganiser_pointer_load( $hook_suffix ) {

		$screen_id = get_current_screen()->id;

		//Get pointers for this screen
		/**
		 * Filters the user 'pointers' for a specific screen.
		 *
		 * The `$screen_id` part of the hook refers to the screen's ID.
		 *
		 * @param array $pointers Filters to display on this screen.
		 */
		$pointers = apply_filters('eventorganiser_admin_pointers-'.$screen_id, array());

		if( !$pointers || !is_array($pointers) )
			return;

		// Get dismissed pointers
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$valid_pointers =array();

		//Check pointers and remove dismissed ones.
		foreach ($pointers as $pointer_id => $pointer ){

			//Sanity check
			if ( in_array( $pointer_id, $dismissed ) || empty($pointer)  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) )
				continue;

			 $pointer['pointer_id'] = $pointer_id;

			//Add the pointer to $valid_pointers array
			$valid_pointers['pointers'][] =  $pointer;
		}

		//No valid pointers? Stop here.
		if( empty($valid_pointers) )	
			return;

		// Add pointers style to queue. 
		wp_enqueue_style( 'wp-pointer' );
		
		// Add pointers script to queue. Add custom script.
		wp_enqueue_script('eventorganiser-pointer',EVENT_ORGANISER_URL.'js/eventorganiser-pointer.js',array('wp-pointer','eo_event'));

		// Add pointer options to script. 
		wp_localize_script('eventorganiser-pointer', 'eventorganiserPointer', $valid_pointers);
}
add_action( 'admin_enqueue_scripts', 'eventorganiser_pointer_load',99999);


/**
 * Handles admin notices
 *
 * This is a class which automates (semi-permant) admin notices. This are notices which persist until an 
 * action is performed or they are manually dismissed by the user. The EO_Admin_Notice_Handle::admin_notice()
 * generates the mark-up for the notices, and displays them on the appropriate screen. It also triggers printing
 * javascript so that notices can be dismissed via AJAX. There is also a no-js fallback.
 *
 *@access private
 *@ignore
 *@since 1.7
 */
class EO_Admin_Notice_Handler{

	static $prefix = 'eventorganiser';

	/**
	 * Hooks the dismiss listener (ajax and no-js) and maybe shows notices on admin_notices
	*/
	static function load(){
		add_action( 'admin_notices', array(__CLASS__,'admin_notice'));
		add_action( 'admin_init',array(__CLASS__, 'dismiss_handler'));
	        add_action( 'wp_ajax_'.self::$prefix.'-dismiss-notice', array( __CLASS__, 'dismiss_handler' ) );
	}

	/**
	 * Print appropriate notices.
	 * Hooks EO_Admin_Notice_Handle::print_footer_scripts to admin_print_footer_scripts to
	 * print js to handle AJAX dismiss.
	*/
	static function admin_notice(){

		$screen_id = get_current_screen()->id;

		//Notices of the form ID=> array('screen_id'=>screen ID, 'message' => Message,'type'=>error|alert)
		$notices = array(
			'autofillvenue17'=>array(
				'screen_id'=>'event_page_venues',
				'message' => sprintf(__("<h4>City & State Fields Added</h4>City and state / province fields for venues have now been added. </br> If you'd like, Event Organiser can <a href='%s'>attempt to auto-fill them</a>. You can always manually change the details aftewards.",'eventorganiser'),
								add_query_arg('action','eo-autofillcity',admin_url('admin-post.php'))
								),
				'type' => 'alert'
			),
			'changedtemplate17'=>array(
				'screen_id'=>'',
				'message' => __("<h4>The Default Templates Have Changed</h4>Don't panic! If you've set up your own templates in your theme you won't notice any change. </br> If you haven't and want the old templates back, <a href='http://wp-event-organiser.com/blog/new-default-templates-in-1-7'>see this post<a/>.",'eventorganiser'),
				'type' => 'alert'
			),
		);

		if( !$notices )
			return;

		$seen_notices = get_option(self::$prefix.'_admin_notices',array());

		foreach( $notices as $id => $notice ){
			$id = sanitize_key($id);

			//Notices cannot have been dismissed and must have a message
			if( in_array($id, $seen_notices)  || empty($notice['message'])  )
				continue;

			$notice_screen_id = (array) $notice['screen_id'];
			$notice_screen_id = array_filter($notice_screen_id);
		
			//Notices must for this screen. If empty, its for all screens.
			if( !empty($notice_screen_id) && !in_array($screen_id, $notice_screen_id) )
				continue;

			$class = $notice['type'] == 'error' ? 'error' : 'updated';
	
			printf("<div class='%s-notice {$class}' id='%s'>%s<p> <a class='%s' href='%s' title='%s'><strong>%s</strong></a></p></div>",
						esc_attr(self::$prefix),
						esc_attr(self::$prefix.'-notice-'.$id),
						$notice['message'],
						esc_attr(self::$prefix.'-dismiss'),
						esc_url(add_query_arg(array(
								'action'=>self::$prefix.'-dismiss-notice',
								'notice'=>$id,
								'_wpnonce'=>wp_create_nonce(self::$prefix.'-dismiss-'.$id),
							))),
						__('Dismiss this notice','eventorganiser'),
						__('Dismiss','eventorganiser')
					);
			add_action( 'admin_print_footer_scripts', array( __CLASS__, 'print_footer_scripts' ), 11 );
		}
	}

	/**
	 * Handles AJAX and no-js requests to dismiss a notice
	*/
	static function dismiss_handler(){

		$notice = isset($_REQUEST['notice']) ? $_REQUEST['notice'] : false;
		if( empty($notice) )
			return;

		if ( defined('DOING_AJAX') && DOING_AJAX ){
			//Ajax dismiss handler
			if( empty($_REQUEST['notice'])  || empty($_REQUEST['_wpnonce'])  || $_REQUEST['action'] !== self::$prefix.'-dismiss-notice' )
				return;
	
			if( !wp_verify_nonce( $_REQUEST['_wpnonce'],self::$prefix."-ajax-dismiss") )
				return;

		}else{
			//Fallback dismiss handler
			if( empty($_REQUEST['action']) || empty($_REQUEST['notice'])  || empty($_REQUEST['_wpnonce'])  || $_REQUEST['action'] !== self::$prefix.'-dismiss-notice' )
				return;

			if( !wp_verify_nonce( $_REQUEST['_wpnonce'],self::$prefix.'-dismiss-'.$notice ) )
			return;
		}

		self::dismiss_notice($notice);

		if ( defined('DOING_AJAX') && DOING_AJAX )
			wp_die(1);
	}

	/**
	 * Dismiss a given a notice
	 *@param string $notice The notice (ID) to dismiss
	*/
	static function dismiss_notice($notice){
		$seen_notices = get_option(self::$prefix.'_admin_notices',array());
		$seen_notices[] = $notice;
		$seen_notices = array_unique($seen_notices);
		update_option(self::$prefix.'_admin_notices',$seen_notices);
	}

	/**
	 * Prints javascript in footer to handle AJAX dismiss.
	*/
	static function print_footer_scripts() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($){
				var dismissClass = '<?php echo esc_js(self::$prefix."-dismiss");?>';
        			var ajaxaction = '<?php echo esc_js(self::$prefix."-dismiss-notice"); ?>';
				var _wpnonce = '<?php echo wp_create_nonce(self::$prefix."-ajax-dismiss")?>';
				var noticeClass = '<?php echo esc_js(self::$prefix."-notice");?>';

				jQuery('.'+dismissClass).click(function(e){
					e.preventDefault();
					var noticeID= $(this).parents('.'+noticeClass).attr('id').substring(noticeClass.length+1);

					$.post(ajaxurl, {
						action: ajaxaction,
						notice: noticeID,
						_wpnonce: _wpnonce
					}, function (response) {
						if ('1' === response) {
							$('#'+noticeClass+'-'+noticeID).fadeOut('slow');
			                    } else {
							$('#'+noticeClass+'-'+noticeID).removeClass('updated').addClass('error');
                    		 	   }
                			});
				});
        		});
		</script><?php
	}
}//End EO_Admin_Notice_Handler
EO_Admin_Notice_Handler::load();


/**
 * Handles city auto-fill request.
 *
 * Hooked onto admin_post_eo-autofillcity. Triggered when user clicks 'autofill' link.
 * This routine goes through all the venues, reverse geocodes to find their city and 
 * autofills the city field (added in 1.7).
 *
 *@ignore
 *@access private
 *@link https://github.com/stephenh1988/Event-Organiser/issues/18
 *@link http://open.mapquestapi.com/nominatim/ Nominatim Search Service
 */
function _eventorganiser_autofill_city(){
	$seen_notices = get_option('eventorganiser_admin_notices',array());

	if( in_array('autofillvenue17', $seen_notices) )
		return;

	EO_Admin_Notice_Handler::dismiss_notice('autofillvenue17');

	$cities =array();
	$venues = eo_get_venues();

	foreach( $venues as $venue ){
		$venue_id = (int) $venue->term_id;
		$latlng =extract(eo_get_venue_latlng($venue_id));

		If( eo_get_venue_meta($venue_id,'_city',true) )
			continue;

		$response=wp_remote_get("http://open.mapquestapi.com/nominatim/v1/reverse?format=json&lat={$lat}&lon={$lng}&osm_type=N&limit=1");	
		$geo = json_decode(wp_remote_retrieve_body( $response ));
		if( isset($geo->address->city) ){
			$cities[$venue_id] = $geo->address->city;
			eo_update_venue_meta($venue_id, '_city', $geo->address->city);
		}
		if( isset($geo->address->country_code) && 'gb' == $geo->address->country_code ){
			//For the UK use county not state.
			if( isset($geo->address->county) ){
				$cities[$venue_id] = $geo->address->county;
				eo_update_venue_meta($venue_id, '_state', $geo->address->county);
			}
		}else{
			if( isset($geo->address->state) ){
				$cities[$venue_id] = $geo->address->state;
				eo_update_venue_meta($venue_id, '_state', $geo->address->state);
			}
		}
	}	

	wp_safe_redirect(admin_url('edit.php?post_type=event&page=venues'));
}
add_action('admin_post_eo-autofillcity','_eventorganiser_autofill_city');


/**
 * Adds post-type-event class to the <body> tag of event admin pages.
 * 
 * This was added by WP 3.7+, this function serves for backwards compatability
 * with 3.3 through to 3.6. It's used to fix a bug with EO & EO Pro:
 * @see https://github.com/stephenharris/Event-Organiser/issues/176
 */
function _eventorganiser_add_event_class( $admin_body_class ){
	$screen = get_current_screen();

	if( $screen && 'event' == $screen->post_type && version_compare( '3.7', get_bloginfo( 'version' ) ) == 1 ){
		$admin_body_class .= ' post-type-event ';
	}
	return $admin_body_class;
}
add_filter( 'admin_body_class', '_eventorganiser_add_event_class', 99999 );



/**
 * Helper function to clear the cache for number of authors.
 *
 * @private
 */
function _eventorganiser_clear_multi_organiser_cache( $new_status, $old_status, $post ) {
	if( $new_status !== $old_status && 'event' == get_post_type( $post ) ){
		delete_transient( 'eo_is_multi_event_organiser' );
	}
}
add_action('transition_post_status', '_eventorganiser_clear_multi_organiser_cache', 10, 3 );
?>