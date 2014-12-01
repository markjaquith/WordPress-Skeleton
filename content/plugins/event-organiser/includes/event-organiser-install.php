<?php

/**
 * Install routine
 *
 *@since 1.0
 *@access private
 *@ignore
*/
 function eventorganiser_install( $is_networkwide = false ){
       global $wpdb;

       if( !defined( 'EVENT_ORGANISER_URL' ) ){
       		define( 'EVENT_ORGANISER_URL', plugin_dir_url( EVENT_ORGANISER_DIR.'event-organiser.php' ) );
       }
       
    	// Is this multisite and did the user click network activate?
    	$is_multisite = ( function_exists('is_multisite') && is_multisite() );

    	if ($is_multisite && $is_networkwide) {
    	    	// Get the current blog so we can return to it.
	        $current_blog_id = get_current_blog_id();

	        // Get a list of all blogs.
	         $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		if( $blog_ids ){
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
	            		eventorganiser_site_install();
	       		 }
			switch_to_blog( $current_blog_id );
		}else{
			eventorganiser_site_install();
		}
    	}else {
    	    eventorganiser_site_install();
    	}
}

function eventorganiser_site_install(){
	global $wpdb; 
	$eventorganiser_db_version = defined( 'EVENT_ORGANISER_VER' ) ? EVENT_ORGANISER_VER : false;

	eventorganiser_wpdb_fix();

	$charset_collate = '';
	if ( ! empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( ! empty($wpdb->collate) )
		$charset_collate .= " COLLATE $wpdb->collate";

	//Events table
	$sql_events_table = "CREATE TABLE " .$wpdb->eo_events. " (
		event_id bigint(20) NOT NULL AUTO_INCREMENT,
		post_id bigint(20) NOT NULL,
		StartDate DATE NOT NULL,
		EndDate DATE NOT NULL,
		StartTime TIME NOT NULL,
		FinishTime TIME NOT NULL,
		event_occurrence bigint(20) NOT NULL,
		PRIMARY KEY  (event_id),
		KEY StartDate (StartDate),
		KEY EndDate (EndDate)
		)".$charset_collate;
	
	//Venue meta table
	$sql_venuemeta_table ="CREATE TABLE {$wpdb->prefix}eo_venuemeta (
		meta_id bigint(20) unsigned NOT NULL auto_increment,
		eo_venue_id bigint(20) unsigned NOT NULL default '0',
 		meta_key varchar(255) default NULL,
		meta_value longtext,
		PRIMARY KEY  (meta_id),
		KEY eo_venue_id (eo_venue_id),
		KEY meta_key (meta_key)
		) $charset_collate; ";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql_events_table);
	dbDelta($sql_venuemeta_table);

	//Add options and capabilities
	$eventorganiser_options = array (	
		'supports' => array('title','editor','author','thumbnail','excerpt','custom-fields','comments'),
		'event_redirect' => 'events',
		'dateformat'=>'dd-mm',
		'prettyurl'=> 1,
		'templates'=> 1,
		'addtomenu'=> 0,
		'excludefromsearch'=>0,
		'showpast'=> 0,
		'group_events'=>'',
		'url_venue'=>'events/event',
		'url_venue'=> 'events/venues',
		'url_cat' => 'events/category',
		'url_tag' => 'events/tag',
		'navtitle' => __('Events','eventorganiser'),
		'eventtag' => 1,
		'feed' => 1,
		'runningisnotpast' => 0,
		'deleteexpired' => 0
	);
	add_option('eventorganiser_options',$eventorganiser_options);

	/* Add existing notices */
	$notices = array('autofillvenue17','changedtemplate17');
	add_option('eventorganiser_admin_notices',$notices);
	
	//Add roles to administrator		
	global $wp_roles;
	$all_roles = $wp_roles->roles;
	$eventorganiser_roles =  array(
			 'edit_events' => __( 'Edit Events', 'eventorganiser' ),
			 'publish_events' => __( 'Publish Events', 'eventorganiser' ),
			 'delete_events' => __( 'Delete Events', 'eventorganiser' ),
			'edit_others_events' => __( 'Edit Others\' Events', 'eventorganiser' ),
			 'delete_others_events' => __( 'Delete Other\'s Events', 'eventorganiser' ),
			'read_private_events' => __( 'Read Private Events', 'eventorganiser' ),
			 'manage_venues' => __( 'Manage Venues', 'eventorganiser' ),
			 'manage_event_categories' => __( 'Manage Event Categories & Tags', 'eventorganiser' ),
		);
	foreach ($all_roles as $role_name => $display_name):
		$role = $wp_roles->get_role($role_name);
		if($role->has_cap('manage_options')){
			foreach($eventorganiser_roles as $eo_role=>$eo_role_display):
				$role->add_cap($eo_role);
			endforeach;  
		}
	endforeach;  //End foreach $all_roles

	//Manually register CPT and CTs ready for flushing
	eventorganiser_create_event_taxonomies();
	eventorganiser_cpt_register();

	//Flush rewrite rules only on activation, and after CPT/CTs has been registered.
	flush_rewrite_rules();
}

/**
 * Deactivate routine
 *
 * Flushes rewrite rules. Don't clear cron jobs, as these won't be re-added.
 *
 *@since 1.5
 *@access private
 *@ignore
*/
function eventorganiser_deactivate(){
	flush_rewrite_rules();
    }


/**
 * Upgrade routine. Hooked onto admin_init
 *
 *@since 1.1
 *@access private
 *@ignore
*/
function eventorganiser_upgradecheck(){
	global $wpdb, $EO_Errors;
	$eventorganiser_db_version = defined( 'EVENT_ORGANISER_VER' ) ? EVENT_ORGANISER_VER : false;
		
	$installed_ver = get_option('eventorganiser_version');

	if( empty($installed_ver) ){
		//This is a fresh install. Add current database version
		add_option('eventorganiser_version', $eventorganiser_db_version);

		//But a bug in 1.5 means that it could be that they first installed in 1.5 (as no db version was added)
		//So set to 1.5.  Fresh installs will have to go through the 1.6 (and above) update, but this is ok.
		$installed_ver = '1.5';
		
		eventorganiser_install();
	}

	//If this is an old version, perform some updates.
	if ( !empty($installed_ver ) && $installed_ver != $eventorganiser_db_version ):

		if($installed_ver <'1.3'){
			wp_die('You cannot upgrade to this version from 1.3 or before. Please upgrade to 1.5.7 first.');
		}

		if($installed_ver <'1.4'){
			eventorganiser_140_update();
		}

		if($installed_ver <'1.5'){
			eventorganiser_150_update();
		}
		if( $installed_ver < '1.6'  ){
			//Remove columns:
			$columns = $wpdb->get_col("DESC {$wpdb->eo_events}", 0);
			$remove_columns = array('Venue','event_schedule','event_schedule_meta', 'event_frequency','reoccurrence_start', 'reoccurrence_end' );
			$delete_columns = array_intersect($remove_columns, $columns);
			if( !empty($delete_columns) )
				$sql = $wpdb->query("ALTER TABLE {$wpdb->eo_events} DROP COLUMN ".implode(', DROP COLUMN ',$delete_columns).';');
		
			eventorganiser_install();
		}

		if( $installed_ver < '1.6.2' ){
			$options = get_option('eventorganiser_options');
			if( !empty($options['eventtag']) ){
				$options['supports'][] = 'eventtag';
				update_option('eventorganiser_options', $options);
			}
		}
		if( $installed_ver < '2.7.3' ){
			//Ensure event_allday columns is removed. This causes problems on Windows servers.
			$columns = $wpdb->get_col("DESC {$wpdb->eo_events}", 0);
			$remove_columns = array('event_allday');
			$delete_columns = array_intersect( $remove_columns, $columns );
			if( !empty($delete_columns) )
				$sql = $wpdb->query("ALTER TABLE {$wpdb->eo_events} DROP COLUMN ".implode(', DROP COLUMN ',$delete_columns).';');
			flush_rewrite_rules();
		}
		
		update_option('eventorganiser_version', $eventorganiser_db_version);

		//Run upgrade checks
		add_action('admin_notices', 'eventorganiser_db_checks',0);
	endif;
}
add_action('admin_init', 'eventorganiser_upgradecheck');

/**
 * Upgrade routine for 1.5
 *
 *@since 1.5
 *@access private
 *@ignore
*/
function eventorganiser_150_update(){
	global $wpdb;
	$et =$wpdb->eo_events;
	$events = $wpdb->get_results("SELECT*, min({$et}.StartDate) as StartDate, min({$et}.EndDate) as EndDate FROM $wpdb->eo_events GROUP BY {$et}.post_id ORDER BY {$et}.StartDate");
	if( $events ):
		foreach( $events as $event ):

			$post_id = (int) $event->post_id;

			$event_data = array(
				'schedule' => $event->event_schedule,
				'all_day' => $event->event_allday,
				'schedule_meta' => ('weekly' == $event->event_schedule ? maybe_unserialize($event->event_schedule_meta) : $event->event_schedule_meta),
				'frequency' => $event->event_frequency,
				'exclude'=>array(),
				'include'=>array(),
			);
			$start = new DateTime($event->StartDate.' '.$event->StartTime, eo_get_blog_timezone());
			$end = new DateTime($event->EndDate.' '.$event->FinishTime, eo_get_blog_timezone());
			$schedule_last = new DateTime($event->reoccurrence_end.' '.$event->StartTime, eo_get_blog_timezone());

			$seconds = round(abs($start->format('U') - $end->format('U')));
			$days = floor($seconds/86400);// 86400 = 60*60*24 seconds in a normal day
			$sec_diff = $seconds - $days*86400;
			$duration_str = '+'.$days.'days '.$sec_diff.' seconds';
			$event_data['duration_str'] =$duration_str;

			$schedule_last_end = clone $schedule_last;
			$schedule_last_end->modify($duration_str);

			update_post_meta( $post_id,'_eventorganiser_event_schedule', $event_data);
			update_post_meta( $post_id,'_eventorganiser_schedule_start_start', $start->format('Y-m-d H:i:s')); //Schedule start
			update_post_meta( $post_id,'_eventorganiser_schedule_start_finish', $end->format('Y-m-d H:i:s')); //Schedule start
			update_post_meta( $post_id,'_eventorganiser_schedule_last_start', $schedule_last->format('Y-m-d H:i:s'));//Schedule last
			update_post_meta( $post_id,'_eventorganiser_schedule_last_finish', $schedule_last_end->format('Y-m-d H:i:s'));//Schedule last

		endforeach;
	endif;

}

/**
 * Upgrade routine for 1.4
 *
 *@since 1.4
 *@access private
 *@ignore
*/
function eventorganiser_140_update(){
	//Migrates from Venue table to venue meta table

	//Run install to create new table:
	eventorganiser_install();

	global $wpdb;
	$eventorganiser_venue_table = $wpdb->prefix."eo_venues";

	$venues = eo_get_the_venues();
	$venue_metavalues = $wpdb->get_results(" SELECT venue_slug, venue_address, venue_postal, venue_country, venue_lng, venue_lat, venue_description FROM $eventorganiser_venue_table");
	$fields = array('venue_address'=>'_address','venue_postal'=>'_postcode','venue_country'=>'_country','venue_lng'=>'_lng','venue_lat'=>'_lat','venue_description'=>'_description');

	foreach( $venue_metavalues as $venue ){
		$term = get_term_by('slug',$venue->venue_slug,'event-venue');
		if( empty($term) || is_wp_error($term) )
			continue;

		foreach ($fields as $column_name => $meta_key){
			if( ! empty($venue->$column_name) ){
				update_metadata('eo_venue',$term->term_id,$meta_key,$venue->$column_name);
			}
		}
	}
}


/**
 * Uninstall routine
 *
 *@since 1.0
 *@access private
 *@ignore
*/
function eventorganiser_uninstall( $is_networkwide = false ){
	global $wpdb;

    	// Is this multisite and did the user click network activate?
    	$is_multisite = ( function_exists('is_multisite') && is_multisite() );

    	if ( $is_multisite && $is_networkwide ) {
    	    	// Get the current blog so we can return to it.
	        $current_blog_id = get_current_blog_id();

	        // Get a list of all blogs.
	        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		if( $blog_ids ){
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
	            		eventorganiser_uninstall_site();
	       		 }
			switch_to_blog( $current_blog_id );
		}else{
			eventorganiser_uninstall_site();
		}
    	}else {
    	    eventorganiser_uninstall_site();
    	}

}

function eventorganiser_uninstall_site(){
	global $wpdb,$eventorganiser_roles, $wp_roles,$wp_taxonomies;

	eventorganiser_clear_cron_jobs();
	eventorganiser_create_event_taxonomies();

	//Remove 	custom taxonomies and terms.
	$taxs = array('event-category','event-venue','event-tag');
	$terms = get_terms($taxs, 'hide_empty=0' );

	if( $terms ){
		foreach ($terms as $term) {
			$term_id = (int)$term->term_id;
			wp_delete_term($term_id ,$term->taxonomy);
		}
	}

	//Remove all posts of CPT Event
	//?? $wpdb->query("DELETE FROM $wpdb->posts WHERE post_type = 'event'");

	//Delete options
	delete_option('eventorganiser_options');
	delete_option('eventorganiser_admin_notices');
	delete_option('eventorganiser_version');
	delete_option('eo_notice');
	delete_option('widget_eo_calendar_widget');
	delete_option('widget_eo_list_widget');

	//Remove Event Organiser capabilities
	$all_roles = $wp_roles->roles;
	foreach ($all_roles as $role_name => $display_name):
		$role = $wp_roles->get_role($role_name);
		foreach($eventorganiser_roles as $eo_role=>$eo_role_display):
			$role->remove_cap($eo_role);
		endforeach;  
	endforeach; 
	
	eventorganiser_clear_cron_jobs();

	//Drop tables    
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->eo_events");
	$eventorganiser_venue_table = $wpdb->prefix."eo_venues";
	$wpdb->query("DROP TABLE IF EXISTS $eventorganiser_venue_table");
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->eo_venuemeta");

	//Remove user-meta-data:
	$meta_keys = array('metaboxhidden_event','closedpostboxes_event','wp_event_page_venues_per_page','manageedit-eventcolumnshidden');	
	$sql =$wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE ");
	foreach($meta_keys as $key):
		$sql .= $wpdb->prepare("meta_key = %s OR ",$key);
	endforeach;
	$sql.=" 1=0 "; //Deal with final 'OR', must be something false!
	$re =$wpdb->get_results( $sql);	
	flush_rewrite_rules();
    }
?>
