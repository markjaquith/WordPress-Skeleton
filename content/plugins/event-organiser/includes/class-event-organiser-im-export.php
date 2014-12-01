<?php
//TODO import events (check for duplicates: UID)
//TODO check importing venues.
/**
* Event importer / exporter
 */
if ( ! function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a part of plugin, not much I can do when called directly.";
	exit;
}


/**
 * Event Organiser Import/Export handler
 */
class Event_Organiser_Im_Export  {

	static private $classobj = NULL;

	/**
	 * Handler for the action 'init'. Instantiates this class.
	 */
	static function get_object() {

		if ( NULL === self :: $classobj ) {
			self :: $classobj = new self;
		}

		return self :: $classobj;
	}
	
	public function __construct() {
		global $pagenow, $EO_Errors;

		if( !isset( $EO_Errors ) ) $EO_Errors = new WP_Error();

		if( is_feed('eo-events') && eventorganiser_get_option('feed') ){
			$this->get_export_file();
		}

		//If importing / exporting events make sure we a logged in and check nonces.
		if ( is_admin() && !empty($_POST['eventorganiser_download_events']) && check_admin_referer( 'eventorganiser_download_events' ) 
			&& current_user_can('manage_options') ):
			//Exporting events
			//mmm... maybe finally a legitimate use of query_posts
			query_posts(array(
				'post_type'=>'event',
				'showpastevents'=>true,
				'group_events_by'=>'series',
				'posts_per_page'=>-1,
			));
			$this->get_export_file();

		elseif ( is_admin() && !empty($_POST['eventorganiser_import_events']) && check_admin_referer( 'eventorganiser_import_events') 
			&& current_user_can('manage_options') ):
			//Importing events	

			//Perform checks on file:
			if ( in_array($_FILES["ics"]["type"], array("text/calendar","application/octet-stream")) && ($_FILES["ics"]["size"] < 2097152) ):
				if($_FILES["ics"]["error"] > 0){
					$EO_Errors = new WP_Error('eo_error', sprintf(__("File Error encountered: %d",'eventorganiser'), $_FILES["ics"]["error"]));
				}else{
					//Import file
					$this->import_file($_FILES['ics']['tmp_name']);
  				}

			elseif(!isset($_FILES) || empty($_FILES['ics']['name'])):
				$EO_Errors = new WP_Error('eo_error', __("No file detected.",'eventorganiser'));

			else:
				$EO_Errors = new WP_Error('eo_error', __("Invalid file uploaded. The file must be a ics calendar file of type 'text/calendar', no larger than 2MB.",'eventorganiser'));
				$size = size_format($_FILES["ics"]["size"],2);
				$details = sprintf( __('File size: %s. File type: %s','eventorganiser'),$size, $_FILES["ics"]["type"]);
				$EO_Errors->add('eo_error', $details);

			endif;

		endif;

		add_action( 'eventorganiser_event_settings_imexport', array( $this, 'get_im_export_markup' ) );						
	}


	/**
	 * Get markup for ex- and import on settings page
	 * 
	 * @since 1.0.0
	 */
	public function get_im_export_markup() {
		?>
			<h3 class="title"><?php _e('Export Events', 'eventorganiser'); ?></h3>
			<form method="post" action="">
				<?php 	settings_fields( 'eventorganiser_imexport'); ?>
				<p><?php _e( 'The export button below generates an ICS file of your events that can be imported in to other calendar applications such as Google Calendar.', 'eventorganiser'); ?></p>
				<?php wp_nonce_field('eventorganiser_download_events'); ?>
				<input type="hidden" name="eventorganiser_download_events" value="true" />
				<?php submit_button(  __( 'Download Export File', 'eventorganiser' )." &raquo;", 'secondary',  'eventorganiser_download_events' ); ?>
			</form>
			
			<h3 class="title"><?php _e('Import Events', 'eventorganiser'); ?></h3>
			<form method="post" action="" enctype="multipart/form-data">
				<div class="inside">
					<p><?php _e( 'Import an ICS file.', 'eventorganiser'); ?></p>
					<?php if( taxonomy_exists( 'event-venue' ) ){ ?>
							<label><input type="checkbox" name="eo_import_venue" value=1 /> <?php _e( 'Import venues', 'eventorganiser' ); ?></label>
					<?php } ?>
					<label><input type="checkbox" name="eo_import_cat" value=1 /> <?php _e( 'Import categories', 'eventorganiser' ); ?></label>
					<p><input type="file" name="ics" /></p>
					<?php wp_nonce_field('eventorganiser_import_events'); ?>
					<input type="hidden" name="eventorganiser_import_events" value="true" />
					<?php submit_button(  __( 'Upload ICS file', 'eventorganiser' )." &raquo;", 'secondary',  'eventorganiser_import_events' ); ?>
				</div>
			</form>
		<?php 
	}

	/**
	* Gets an ICAL file of events in the database, to be downloaded
 	* @since 1.0.0
 	*/
	public function get_export_file() {
		$filename = urlencode( 'event-organiser_' . date('Y-m-d') . '.ics' );
		$this->export_events( $filename, 'text/calendar' );
	}

	/**
	* Creates an ICAL file of events in the database
 	* @since 1.0.0
	* @param string filename - the name of the file to be created
	* @param string filetype - the type of the file ('text/calendar')
	*/
	public function export_events( $filename, $filetype ){ 
		//Collect output 
		ob_start();

		// File header
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header('Content-type: text/calendar; charset=' . get_option('blog_charset').';');
		header("Pragma: 0");
		header("Expires: 0");

		eo_locate_template( 'ical.php', true,false );

		//Collect output and echo 
		$eventsical = ob_get_contents();
		ob_end_clean();
		echo $eventsical;
		exit();
	}	


	/**
	* Reads in an ICAL file into an array, then parses the array and inserts events into database
 	* @since 1.1.0
 	* @param string $cal_file - the file to import
	*/
	function import_file( $ical_file ){
		global $EO_Errors;

		if ( !current_user_can( 'manage_options' ) || !current_user_can( 'edit_events' ) )
			wp_die( __('You do not have sufficient permissions to import events.','eventorganiser') );

		$ical = new EO_ICAL_Parser();
		$ical->parse( $ical_file );
		
		$import_venues = ( isset($_POST['eo_import_venue'] ) ? true : false);
		$import_cats = ( isset($_POST['eo_import_cat'] ) ? true : false);
		
		$events_imported = 0;
		$events_updated = 0;
		$venues_imported = 0;
		$categories_imported = 0;
				
		if( $import_venues && $ical->venues ){

			foreach( $ical->venues as $venue ){
				if( !eo_get_venue_by( 'name', $venue ) ){
					$args = array();
					
					//If lat/lng meta data is set, include that
					if( isset( $ical->venue_meta[$venue]['latitude'] ) && isset( $ical->venue_meta[$venue]['longtitude'] ) ){
						$args['latitude'] = $ical->venue_meta[$venue]['latitude'];
						$args['longtitude'] = $ical->venue_meta[$venue]['longtitude'];
					}
						
					$new_venue = eo_insert_venue( $venue, $args );
					if( !is_wp_error( $new_venue ) && $new_venue ){
						$venues_imported++;
					}
				}
			}
			
		}
		
		if( $import_cats && $ical->categories ){

			foreach( $ical->categories as $category ){
				if( !get_term_by( 'name', $category, 'event-category' ) ){
					$new_cat = wp_insert_term( $category, 'event-category', array() );
					if( !is_wp_error( $new_cat ) && $new_cat ){
						$categories_imported++;
					}
				}
			}
			
		}
		
		foreach( $ical->events as $event ){
			//TODO refactor eo_insert_event & eo_update_event...
		
			$uid = !empty( $event['uid'] ) ? $event['uid'] : false;

			//TODO Check if event already exists
			//$found_event = eo_get_event_by_uid( $uid );
			
			//Create event
			if( !empty( $event['event-venue'] ) ){
				$venue = eo_get_venue_by( 'name', $event['event-venue'] );
				if( $venue )
					$event['tax_input']['event-venue'][] =  intval( $venue->term_id );
			}
			
			if( !empty( $event['event-category'] ) ){

				$event['tax_input']['event-category'] = array();
				foreach( $event['event-category'] as $category ){
					$cat = get_term_by( 'name', $category, 'event-category' );
					if( $cat )
						$event['tax_input']['event-category'][] =  intval( $cat->term_id );
				}
			}
			
			$event_id = eo_insert_event( $event );
			if( is_wp_error( $event_id ) ){
				$ical->errors[] = $event_id;
			}else{
				$events_updated++;
			}
	
		}
		
		if( $events_updated == 0 )
			$EO_Errors->add( 'eo_error', __( "No events were imported.", 'eventorganiser' ) );
	
		
		if( count( $ical->errors ) > 0 ){
			foreach( $ical->errors as $error ){
				$codes = $error->get_error_codes();
				foreach( $codes as $code ){
					$error_messages = $error->get_error_messages();
					foreach( $error_messages as $error_message ){
						$EO_Errors->add( 'eo_error', '<strong>' . __( 'Error:', 'eventorganiser' ) . '</strong> '. $error_message );
					}
				}
			}
		}
		
		$message = array();
		
		if( $ical->warnings ){
			foreach( $ical->warnings as $warning ){
				$message[] = '<strong>' . __( 'Warning:', 'eventorganiser' ) . '</strong> ' . $warning->get_error_message();
			}
		}
		
		if( count( $ical->events ) == 1 )
			$message[] = __( "1 event was successfully imported", 'eventorganiser' );
		
		elseif( count( $ical->events ) > 1 )
			$message[] = sprintf( __( "%d events were successfully imported",'eventorganiser'), count( $ical->events ) ).".";
		
		if( $venues_imported == 1 ){
			$message[] = __("1 venue was created",'eventorganiser');
			
		}elseif( $venues_imported > 1 ){
			$message[] = sprintf( __( "%d venues were created",'eventorganiser' ), $venues_imported );
		}
		
		if( $categories_imported == 1 ){
			$message[] = __( "1 category was created", 'eventorganiser' );
			
		}elseif( $categories_imported > 1 ){
			$message[] = sprintf( __("%d categories were created",'eventorganiser'), $categories_imported );
		}
					
		$EO_Errors->add( 'eo_notice', implode( '<br/>', $message ) );
			
		
	}

} // end class